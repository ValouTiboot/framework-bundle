<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Translation;

use Digitix\FrameworkBundle\Repository\TranslationRepository;
use Digitix\FrameworkBundle\Translation\TranslationCompiler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslationCompilerTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir().'/dgtx_compiler_'.uniqid();
        (new Filesystem())->mkdir([$this->dir.'/out', $this->dir.'/cache']);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->dir);
    }

    public function testWritesOneLoadableFilePerLocaleAndDomain(): void
    {
        $compiler = $this->compiler([
            'fr_FR' => [
                'Admin.List.Default' => ['list.default.add' => 'Ajouter', 'list.default.total' => 'Total'],
                'Admin.Message.Success' => ['Entity successfuly added.' => "Entité ajoutée avec succès."],
            ],
            'en_US' => ['Admin.List.Default' => ['list.default.add' => 'Add']],
        ]);

        $files = $compiler->compile();

        self::assertCount(3, $files);
        self::assertFileExists($this->dir.'/out/fr_FR/Admin.List.Default.fr_FR.php');
        self::assertFileExists($this->dir.'/out/fr_FR/Admin.Message.Success.fr_FR.php');
        self::assertFileExists($this->dir.'/out/en_US/Admin.List.Default.en_US.php');

        $catalogue = (new PhpFileLoader())->load($this->dir.'/out/fr_FR/Admin.List.Default.fr_FR.php', 'fr_FR', 'Admin.List.Default');
        self::assertSame('Ajouter', $catalogue->get('list.default.add', 'Admin.List.Default'));
    }

    public function testRemovesFilesOfDomainsWithoutTranslationsAndTheTranslatorCache(): void
    {
        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->dir.'/out/fr_FR/Old.Domain.fr_FR.php', '<?php return [];');
        $filesystem->dumpFile($this->dir.'/out/de_DE/Old.Domain.de_DE.php', '<?php return [];');
        $filesystem->dumpFile($this->dir.'/cache/catalogue.fr_FR.abc.php', '<?php');
        $filesystem->dumpFile($this->dir.'/cache/catalogue.fr_FR.abc.php.meta', '');
        $filesystem->dumpFile($this->dir.'/cache/other.php', '<?php');

        $this->compiler(['fr_FR' => ['Admin.List.Default' => ['list.default.add' => 'Ajouter']]])->compile();

        self::assertFileDoesNotExist($this->dir.'/out/fr_FR/Old.Domain.fr_FR.php');
        self::assertFileDoesNotExist($this->dir.'/out/de_DE/Old.Domain.de_DE.php', 'a locale without any translation left is cleaned too');
        self::assertFileExists($this->dir.'/out/fr_FR/Admin.List.Default.fr_FR.php');
        self::assertFileDoesNotExist($this->dir.'/cache/catalogue.fr_FR.abc.php');
        self::assertFileDoesNotExist($this->dir.'/cache/catalogue.fr_FR.abc.php.meta');
        self::assertFileExists($this->dir.'/cache/other.php', 'only translator catalogues are removed');
    }

    public function testCompilesASingleLocale(): void
    {
        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->dir.'/out/en_US/Admin.List.Default.en_US.php', '<?php return ["list.default.add" => "Add"];');

        $files = $this->compiler(['fr_FR' => ['Admin.List.Default' => ['list.default.add' => 'Ajouter']]])->compile('fr_FR');

        self::assertCount(1, $files);
        self::assertFileExists($this->dir.'/out/en_US/Admin.List.Default.en_US.php', 'other locales are untouched');
    }

    /**
     * @param array<string, array<string, array<string, string>>> $values
     */
    private function compiler(array $values): TranslationCompiler
    {
        $repository = $this->createMock(TranslationRepository::class);
        $repository->method('findValuesGroupedByLocale')
            ->willReturnCallback(static fn (?string $locale) => null === $locale ? $values : array_intersect_key($values, [$locale => true]));

        return new TranslationCompiler(
            $repository,
            $this->dir.'/out',
            $this->dir.'/cache',
            $this->createMock(TranslatorInterface::class),
        );
    }
}
