<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Translation;

use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Collects every translation key used by the project: trans() calls in PHP
 * (parsed by Symfony's AST extractor), "|trans" filters and {% trans %} tags
 * in Twig (Symfony's Twig extractor), plus the labels declared in the
 * digitix configuration.
 *
 * Keys must be literal strings, a key held in a variable cannot be found.
 */
final class TranslationExtractor
{
    /**
     * @param string[] $paths directories to scan (bundle and project sources and templates)
     */
    public function __construct(
        private readonly ExtractorInterface $extractor,
        private readonly ConfigKeyExtractor $configKeys,
        private readonly array $paths,
    ) {
    }

    /**
     * Catalogue of keys (values are empty), grouped by domain.
     */
    public function extract(): MessageCatalogue
    {
        $catalogue = new MessageCatalogue('en');

        $this->extractor->setPrefix('');

        foreach ($this->paths as $path) {
            if (is_dir($path)) {
                $this->extractor->extract($path, $catalogue);
            }
        }

        $this->configKeys->extract($catalogue);

        return $catalogue;
    }

    /** @return string[] */
    public function getPaths(): array
    {
        return $this->paths;
    }

    public static function countKeys(MessageCatalogue $catalogue): int
    {
        $count = 0;

        foreach ($catalogue->getDomains() as $domain) {
            $count += \count($catalogue->all($domain));
        }

        return $count;
    }
}
