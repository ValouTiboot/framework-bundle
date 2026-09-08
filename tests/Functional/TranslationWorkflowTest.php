<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use Digitix\FrameworkBundle\Entity\Translation;
use Digitix\FrameworkBundle\Repository\TranslationRepository;
use Digitix\FrameworkBundle\Translation\RuntimeTranslator;
use Digitix\FrameworkBundle\Translation\TranslationCompiler;
use Digitix\FrameworkBundle\Translation\TranslationStatus;
use Digitix\FrameworkBundle\Translation\TranslationSynchronizer;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Extraction from the code, synchronisation with the database, compilation
 * to catalogue files and immediate visibility in the translator.
 */
final class TranslationWorkflowTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // catalogue files left by a previous run must not seed this one
        (new Filesystem())->remove($this->compiler()->getOutputDir());
        $this->compiler()->compile();
    }

    public function testExtractionCreatesOneEntryPerKeyAndLocaleSeededFromExistingFiles(): void
    {
        $report = $this->synchronizer()->synchronize();

        self::assertSame(['fr_FR'], $report->locales);
        self::assertGreaterThan(50, $report->keys);
        self::assertSame($report->keys, $report->added);
        self::assertGreaterThan(0, $report->seeded);

        // a YAML label, translated by the bundle's own fr_FR file
        $label = $this->entry('Admin.Fields.Label', 'label.role.name');
        self::assertSame('Nom', $label->getValue());
        self::assertSame(TranslationStatus::Translated, $label->getStatus());

        // a Twig key, translated by the bundle's Admin.List.Default file
        self::assertTrue($this->entry('Admin.List.Default', 'list.default.add')->isTranslated());

        // a PHP key with its explicit domain, seeded by the bundle's Admin.Message.Success file
        self::assertSame('Ajouté avec succès.', $this->entry('Admin.Message.Success', 'Entity successfuly added.')->getValue());

        // a PHP key without any translation yet
        self::assertSame(TranslationStatus::Missing, $this->entry('Admin.Message.Error', 'Invalid security token, please try again.')->getStatus());
    }

    public function testASecondExtractionChangesNothing(): void
    {
        $this->synchronizer()->synchronize();
        $report = $this->synchronizer()->synchronize();

        self::assertSame(0, $report->added);
        self::assertSame(0, $report->obsoleted);
        self::assertSame(0, $report->reactivated);
    }

    public function testKeysLeavingAndComingBackToTheCode(): void
    {
        $this->synchronizer()->synchronize();

        $onlyOne = new MessageCatalogue('en');
        $onlyOne->set('list.default.add', '', 'Admin.List.Default');
        $onlyOne->set('brand.new.key', '', 'Admin.List.Default');

        $report = $this->synchronizer()->synchronize($onlyOne);
        $obsoleted = $report->obsoleted;
        self::assertSame(1, $report->added);
        self::assertGreaterThan(50, $obsoleted);
        self::assertSame(TranslationStatus::Translated, $this->entry('Admin.List.Default', 'list.default.add')->getStatus());
        self::assertSame(TranslationStatus::Obsolete, $this->entry('Admin.Fields.Label', 'label.role.name')->getStatus());
        self::assertSame('Nom', $this->entry('Admin.Fields.Label', 'label.role.name')->getValue(), 'obsolete entries keep their value');

        $report = $this->synchronizer()->synchronize();
        self::assertSame($obsoleted, $report->reactivated, 'every real key is back');
        self::assertSame(1, $report->obsoleted, 'only "brand.new.key" becomes obsolete');
        self::assertSame(TranslationStatus::Translated, $this->entry('Admin.Fields.Label', 'label.role.name')->getStatus());
        self::assertSame(TranslationStatus::Obsolete, $this->entry('Admin.List.Default', 'brand.new.key')->getStatus());
    }

    public function testCompiledTranslationsAreVisibleWithoutClearingTheCache(): void
    {
        $this->synchronizer()->synchronize();

        /** @var TranslatorInterface $translator */
        $translator = static::getContainer()->get('translator');
        self::assertInstanceOf(RuntimeTranslator::class, $translator);
        self::assertNotSame('Ajouter une entrée', $translator->trans('list.default.add', [], 'Admin.List.Default', 'fr_FR'));
        self::assertSame('Invalid security token, please try again.', $translator->trans('Invalid security token, please try again.', [], 'Admin.Message.Error', 'fr_FR'), 'untranslated keys fall back to the key');

        $this->entry('Admin.List.Default', 'list.default.add')->setValue('Ajouter une entrée');
        $this->em()->flush();

        $files = $this->compiler()->compile('fr_FR');

        self::assertContains($this->compiler()->getOutputDir().'/fr_FR/Admin.List.Default.fr_FR.php', $files);
        self::assertSame('Ajouter une entrée', $translator->trans('list.default.add', [], 'Admin.List.Default', 'fr_FR'));
        // seeded values are compiled too
        self::assertSame('Nom', $translator->trans('label.role.name', [], 'Admin.Fields.Label', 'fr_FR'));

        $this->login();
        $crawler = $this->client->request('GET', '/admin/role');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Ajouter une entrée', $crawler->filter('body')->text());
    }

    public function testExtractCommandSynchronisesAndCompiles(): void
    {
        $application = new Application(static::$kernel);
        $tester = new CommandTester($application->find('dgtx:translation:extract'));

        $tester->execute([]);

        $tester->assertCommandIsSuccessful();
        self::assertStringContainsString('catalogue file(s) written', $tester->getDisplay());
        self::assertGreaterThan(0, $this->repository()->count([]));
        self::assertFileExists($this->compiler()->getOutputDir().'/fr_FR/Admin.Fields.Label.fr_FR.php');
    }

    public function testCompileCommandTargetsOneLocale(): void
    {
        $this->synchronizer()->synchronize();

        $application = new Application(static::$kernel);
        $tester = new CommandTester($application->find('dgtx:translation:compile'));

        $tester->execute(['locale' => 'fr_FR']);

        $tester->assertCommandIsSuccessful();
        self::assertStringContainsString('Admin.Fields.Label.fr_FR.php', $tester->getDisplay());
    }

    public function testRefreshFromTheAdminAndEditAnEntry(): void
    {
        $this->login();

        $this->client->request('GET', '/admin/translation?refresh');
        self::assertResponseRedirects('/admin/translation');
        $crawler = $this->client->followRedirect();
        self::assertStringContainsString('Translation keys refreshed', implode(' ', $this->flashes($crawler, 'success')));
        self::assertGreaterThan(0, $crawler->filter('table tbody tr')->count());

        $entry = $this->entry('Admin.List.Default', 'list.default.add');
        $crawler = $this->client->request('GET', '/admin/translation/edit/'.$entry->getId());
        self::assertResponseIsSuccessful();

        $this->submitAdminForm($crawler, 'translation', ['value' => 'Ajouter']);
        self::assertResponseRedirects('/admin/translation');

        $this->em()->clear();
        self::assertSame('Ajouter', $this->entry('Admin.List.Default', 'list.default.add')->getValue());
        self::assertStringContainsString("'list.default.add' => 'Ajouter'", (string) file_get_contents($this->compiler()->getOutputDir().'/fr_FR/Admin.List.Default.fr_FR.php'));
    }

    private function entry(string $domain, string $key): Translation
    {
        $entry = $this->repository()->findOneBy(['domain' => $domain, 'keyHash' => Translation::hashKey($key), 'locale' => 'fr_FR']);
        self::assertInstanceOf(Translation::class, $entry, sprintf('entry "%s" in "%s" missing', $key, $domain));

        return $entry;
    }

    private function repository(): TranslationRepository
    {
        return static::getContainer()->get(TranslationRepository::class);
    }

    private function synchronizer(): TranslationSynchronizer
    {
        return static::getContainer()->get(TranslationSynchronizer::class);
    }

    private function compiler(): TranslationCompiler
    {
        return static::getContainer()->get(TranslationCompiler::class);
    }
}
