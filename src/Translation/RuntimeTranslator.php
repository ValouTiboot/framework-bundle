<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Translation;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileExistenceResource;

/**
 * The framework translator only knows the translation files that existed
 * when the container was compiled. This subclass (installed on
 * "translator.default" by RuntimeTranslatorPass) also loads, at runtime,
 * the files that TranslationCompiler generates from the database, so a
 * domain translated for the first time is picked up without rebuilding
 * the container.
 *
 * Runtime files are registered last: their values win over the bundle's
 * defaults and over the project's own translation files.
 */
final class RuntimeTranslator extends Translator
{
    private ?string $runtimeDirectory = null;
    private bool $scanned = false;
    private bool $loaderRegistered = false;

    /** @var array<string, true> */
    private array $registered = [];

    public function setRuntimeDirectory(?string $directory): void
    {
        $this->runtimeDirectory = $directory;
    }

    public function getRuntimeDirectory(): ?string
    {
        return $this->runtimeDirectory;
    }

    /**
     * Registers a "<Domain>.<locale>.php" file. Safe to call several times
     * with the same file, and with a file that may be deleted later.
     */
    public function registerRuntimeFile(string $file): void
    {
        if (isset($this->registered[$file])) {
            return;
        }

        $parts = explode('.', basename($file));

        if (\count($parts) < 3 || 'php' !== array_pop($parts)) {
            return;
        }

        $locale = array_pop($parts);
        $domain = implode('.', $parts);

        if (!$this->loaderRegistered) {
            $this->addLoader(RuntimeFileLoader::FORMAT, new RuntimeFileLoader());
            $this->loaderRegistered = true;
        }

        $this->addResource(RuntimeFileLoader::FORMAT, $file, $locale, $domain);
        $this->registered[$file] = true;
    }

    /** Forgets the catalogues loaded so far: the next trans() reloads them. */
    public function flushCatalogues(): void
    {
        $this->catalogues = [];
    }

    protected function initializeCatalogue(string $locale): void
    {
        $this->scanRuntimeDirectory();

        parent::initializeCatalogue($locale);
    }

    protected function doLoadCatalogue(string $locale): void
    {
        parent::doLoadCatalogue($locale);

        // in debug mode, a change in the runtime directory refreshes the catalogue cache
        if (null !== $this->runtimeDirectory) {
            $this->catalogues[$locale]->addResource(is_dir($this->runtimeDirectory)
                ? new DirectoryResource($this->runtimeDirectory)
                : new FileExistenceResource($this->runtimeDirectory));
        }
    }

    private function scanRuntimeDirectory(): void
    {
        if ($this->scanned || null === $this->runtimeDirectory) {
            return;
        }

        $this->scanned = true;

        foreach (glob($this->runtimeDirectory.'/*/*.php') ?: [] as $file) {
            $this->registerRuntimeFile($file);
        }
    }
}
