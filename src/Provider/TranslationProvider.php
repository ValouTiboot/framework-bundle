<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Provider;

use Symfony\Component\Translation\Loader\ArrayLoader;

/**
 * Holds the translations being edited: domain => key => value.
 */
final class TranslationProvider
{
    /** @var array<string, array<string, string>> */
    private array $translations = [];

    /**
     * Loads PHP translation files named "<Domain>.<locale>.php".
     *
     * @param string[] $files
     *
     * @return array<string, array<string, string>>
     */
    public function getTradInFile(array $files, string $locale): array
    {
        $translations = [];
        $loader = new ArrayLoader();

        foreach ($files as $file) {
            $resource = require $file;

            if (!\is_array($resource)) {
                continue;
            }

            $domain = str_replace('.'.$locale, '', pathinfo($file, \PATHINFO_FILENAME));
            $translations[$domain] = $loader->load($resource, $locale, $domain)->all($domain);
        }

        return $translations;
    }

    /**
     * @param array<string, array<string, string>> $translations
     */
    public function setTranslations(array $translations): self
    {
        $this->translations = $translations;

        return $this;
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }
}
