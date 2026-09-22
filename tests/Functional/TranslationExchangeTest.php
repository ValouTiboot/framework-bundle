<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Functional;

use Digitix\FrameworkBundle\Entity\Translation;
use Digitix\FrameworkBundle\Repository\TranslationRepository;
use Digitix\FrameworkBundle\Translation\TranslationCompiler;
use Digitix\FrameworkBundle\Translation\TranslationExchange;
use Digitix\FrameworkBundle\Translation\TranslationSynchronizer;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Export / import of a locale, from the service, the admin and the console.
 */
final class TranslationExchangeTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        (new Filesystem())->remove($this->compiler()->getOutputDir());
        $this->compiler()->compile();
        $this->synchronizer()->synchronize();
    }

    public function testExportHoldsEveryActiveEntryOfTheLocale(): void
    {
        $this->entry('Admin.List.Default', 'list.default.add')->markObsolete();
        $this->em()->flush();

        $export = $this->exchange()->export('fr_FR');

        self::assertSame(TranslationExchange::FORMAT, $export['format']);
        self::assertSame('fr_FR', $export['locale']);
        self::assertGreaterThan(50, \count($export['entries']));

        $keys = array_map(static fn (array $entry) => $entry['domain'].'|'.$entry['key'], $export['entries']);
        self::assertContains('Admin.Fields.Label|label.role.name', $keys);
        self::assertNotContains('Admin.List.Default|list.default.add', $keys, 'obsolete entries stay home');

        $entry = array_values(array_filter($export['entries'], static fn (array $entry) => 'label.role.name' === $entry['key']))[0];
        self::assertSame('Nom', $entry['value']);
    }

    public function testImportFillsUpdatesAndCreatesAndCompiles(): void
    {
        $this->entry('Admin.Message.Error', 'Invalid security token, please try again.')->setValue(null);
        $this->em()->flush();

        $report = $this->exchange()->import([
            'format' => TranslationExchange::FORMAT,
            'locale' => 'fr_FR',
            'entries' => [
                ['domain' => 'Admin.Message.Error', 'key' => 'Invalid security token, please try again.', 'value' => 'Jeton invalide'], // fills a missing one
                ['domain' => 'Admin.Fields.Label', 'key' => 'label.role.name', 'value' => 'Libellé'],                                   // existing: kept without overwrite
                ['domain' => 'Admin.Fields.Label', 'key' => 'label.user.email', 'value' => 'Email'],                                    // same value
                ['domain' => 'Project.Custom', 'key' => 'Brand new', 'value' => 'Tout neuf'],                                           // unknown key: created
                ['domain' => 'Project.Custom', 'key' => 'Empty one', 'value' => ''],                                                     // nothing to bring
                ['domain' => '', 'key' => 'no domain'],                                                                                 // invalid
            ],
        ]);

        self::assertSame(1, $report->added);
        self::assertSame(1, $report->updated);
        self::assertSame(1, $report->unchanged);
        self::assertSame(2, $report->skipped);
        self::assertSame(1, $report->invalid);

        $this->em()->clear();
        self::assertSame('Jeton invalide', $this->entry('Admin.Message.Error', 'Invalid security token, please try again.')->getValue());
        self::assertSame('Nom', $this->entry('Admin.Fields.Label', 'label.role.name')->getValue(), 'not overwritten');
        self::assertSame('Tout neuf', $this->entry('Project.Custom', 'Brand new')->getValue());

        /** @var TranslatorInterface $translator */
        $translator = static::getContainer()->get('translator');
        self::assertSame('Tout neuf', $translator->trans('Brand new', [], 'Project.Custom', 'fr_FR'), 'compiled right away');

        $report = $this->exchange()->import([
            'format' => TranslationExchange::FORMAT,
            'locale' => 'fr_FR',
            'entries' => [['domain' => 'Admin.Fields.Label', 'key' => 'label.role.name', 'value' => 'Libellé']],
        ], null, true);

        self::assertSame(1, $report->updated);
        $this->em()->clear();
        self::assertSame('Libellé', $this->entry('Admin.Fields.Label', 'label.role.name')->getValue(), 'overwritten on demand');
    }

    public function testImportRejectsForeignFiles(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->exchange()->import(['locale' => 'fr_FR', 'entries' => []]);
    }

    public function testRoundTripThroughTheAdmin(): void
    {
        $this->login();

        $this->client->request('GET', '/admin/translation/action/export?locale=fr_FR');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json; charset=UTF-8');
        self::assertStringContainsString('translations-fr_FR-', (string) $this->client->getResponse()->headers->get('content-disposition'));
        $json = (string) $this->client->getResponse()->getContent();
        self::assertStringContainsString('"label.role.name"', $json);

        // change a value, import the export with overwrite: the old value is back
        $this->entry('Admin.Fields.Label', 'label.role.name')->setValue('Changé');
        $this->em()->flush();

        $file = tempnam(sys_get_temp_dir(), 'dgtx');
        file_put_contents((string) $file, $json);
        $crawler = $this->client->request('GET', '/admin/translation');
        $token = $crawler->filter('.dgtx-trans-table')->attr('data-token');

        $this->client->request('POST', '/admin/translation/action/import?locale=fr_FR', ['_token' => 'wrong'], ['file' => new UploadedFile((string) $file, 'translations.json', 'application/json', null, true)]);
        self::assertResponseRedirects('/admin/translation?locale=fr_FR');
        self::assertStringContainsString('Jeton de sécurité invalide', implode(' ', $this->flashes($this->client->followRedirect(), 'danger')));

        $this->client->request('POST', '/admin/translation/action/import?locale=fr_FR', ['_token' => $token, 'overwrite' => '1'], ['file' => new UploadedFile((string) $file, 'translations.json', 'application/json', null, true)]);
        self::assertResponseRedirects('/admin/translation?locale=fr_FR');
        self::assertStringContainsString('Traductions importées', implode(' ', $this->flashes($this->client->followRedirect(), 'success')));

        $this->em()->clear();
        self::assertSame('Nom', $this->entry('Admin.Fields.Label', 'label.role.name')->getValue());
    }

    public function testConsoleExportAndImport(): void
    {
        $application = new Application(static::$kernel);
        $file = sys_get_temp_dir().'/dgtx-export-'.uniqid().'.json';

        $tester = new CommandTester($application->find('dgtx:translation:export'));
        $tester->execute(['locale' => 'fr_FR', 'file' => $file]);
        $tester->assertCommandIsSuccessful();
        self::assertFileExists($file);

        $tester = new CommandTester($application->find('dgtx:translation:import'));
        $tester->execute(['file' => $file, '--overwrite' => true]);
        $tester->assertCommandIsSuccessful();
        self::assertStringContainsString('entries processed', $tester->getDisplay());

        $tester->execute(['file' => '/nowhere/nothing.json']);
        self::assertSame(1, $tester->getStatusCode());

        unlink($file);
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

    private function exchange(): TranslationExchange
    {
        return static::getContainer()->get(TranslationExchange::class);
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
