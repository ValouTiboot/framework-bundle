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

    public function testRefreshFromTheAdminThenEditThroughTheForm(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/admin/translation');
        self::assertResponseIsSuccessful();
        self::assertCount(0, $crawler->filter('tr.dgtx-trans-row'), 'nothing extracted yet');

        // refresh is a POST with a token
        $this->client->request('POST', '/admin/translation/action/refresh?locale=fr_FR', ['_token' => 'wrong']);
        self::assertResponseRedirects('/admin/translation?locale=fr_FR');
        self::assertSame(0, $this->repository()->count([]), 'a bad token does nothing');

        $this->client->request('POST', '/admin/translation/action/refresh?locale=fr_FR', ['_token' => $crawler->filter('.dgtx-trans-table')->attr('data-token')]);
        self::assertResponseRedirects('/admin/translation?locale=fr_FR');
        $crawler = $this->client->followRedirect();
        // the flash is translated by the bundle's own fr_FR file
        self::assertStringContainsString('Clés de traduction actualisées', implode(' ', $this->flashes($crawler, 'success')));
        self::assertGreaterThan(0, $crawler->filter('tr.dgtx-trans-row')->count());
        self::assertSame('fr_FR', $crawler->filter('.dgtx-trans-tabs .nav-link.active')->attr('data-locale'));

        $entry = $this->entry('Admin.List.Default', 'list.default.add');
        $crawler = $this->client->request('GET', '/admin/translation/edit/'.$entry->getId());
        self::assertResponseIsSuccessful();

        $this->submitAdminForm($crawler, 'translation', ['value' => 'Ajouter (formulaire)']);
        self::assertResponseRedirects('/admin/translation?locale=fr_FR');

        $this->em()->clear();
        self::assertSame('Ajouter (formulaire)', $this->entry('Admin.List.Default', 'list.default.add')->getValue());
        self::assertStringContainsString("'list.default.add' => 'Ajouter (formulaire)'", (string) file_get_contents($this->compiler()->getOutputDir().'/fr_FR/Admin.List.Default.fr_FR.php'));
    }

    public function testEditorFiltersAndSearch(): void
    {
        $this->synchronizer()->synchronize();
        $this->login();

        $crawler = $this->client->request('GET', '/admin/translation?status=missing');
        self::assertResponseIsSuccessful();
        self::assertGreaterThan(0, $crawler->filter('tr.dgtx-trans-row')->count());
        self::assertCount(0, $crawler->filter('tr.dgtx-trans-row-translated'), 'only missing entries');

        $crawler = $this->client->request('GET', '/admin/translation?domain=Admin.List.Default&q=list.default.add');
        self::assertCount(1, $crawler->filter('tr.dgtx-trans-row'));
        self::assertStringContainsString('list.default.add', $crawler->filter('.dgtx-trans-key')->text());

        $crawler = $this->client->request('GET', '/admin/translation?q=nothing-like-this-anywhere');
        self::assertCount(0, $crawler->filter('tr.dgtx-trans-row'));
        self::assertStringContainsString('Aucune entrée ne correspond', $crawler->filter('table tbody')->text());
    }

    public function testInlineUpdateThroughJson(): void
    {
        $this->synchronizer()->synchronize();
        $this->login();

        $crawler = $this->client->request('GET', '/admin/translation');
        $token = $crawler->filter('.dgtx-trans-table')->attr('data-token');
        $entry = $this->entry('Admin.Message.Error', 'Invalid security token, please try again.');
        $url = '/admin/translation/action/update/'.$entry->getId();
        $headers = ['CONTENT_TYPE' => 'application/json', 'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'];

        $this->client->request('POST', $url, [], [], $headers, (string) json_encode(['value' => 'Jeton invalide', '_token' => 'wrong']));
        self::assertResponseStatusCodeSame(403);
        self::assertJson((string) $this->client->getResponse()->getContent());

        $this->client->request('POST', $url, [], [], $headers, (string) json_encode(['value' => 'Jeton invalide', '_token' => $token]));
        self::assertResponseIsSuccessful();
        $data = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertIsArray($data);
        self::assertSame('Jeton invalide', $data['value']);
        self::assertSame('translated', $data['status']);
        self::assertSame('fr_FR', $data['locale']);
        self::assertArrayHasKey('translated', $data['counts']);

        /** @var TranslatorInterface $translator */
        $translator = static::getContainer()->get('translator');
        self::assertSame('Jeton invalide', $translator->trans('Invalid security token, please try again.', [], 'Admin.Message.Error', 'fr_FR'), 'compiled right away');

        // blanking the value puts the entry back to "missing"
        $this->client->request('POST', $url, [], [], $headers, (string) json_encode(['value' => '  ', '_token' => $token]));
        $data = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertIsArray($data);
        self::assertNull($data['value']);
        self::assertSame('missing', $data['status']);

        $this->client->request('POST', '/admin/translation/action/update/999999', [], [], $headers, (string) json_encode(['value' => 'x', '_token' => $token]));
        self::assertResponseStatusCodeSame(404);

        $this->client->request('POST', '/admin/translation/action/no_such_action', [], [], $headers, '{}');
        self::assertResponseStatusCodeSame(404);
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
