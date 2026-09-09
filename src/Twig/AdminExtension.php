<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Twig;

use Digitix\FrameworkBundle\Admin\Config\AdminConfig;
use Digitix\FrameworkBundle\Entity\Language;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Small helpers for the admin templates.
 *
 *   dgtx_entity_title(slug)  title of the admin menu entry of an entity ("Pages" for "cms"), or the slug
 *   dgtx_language_iso(id)    "fr" for a language id (form children of translated fields are keyed by id)
 *   dgtx_language_name(id)   "Français"
 */
final class AdminExtension extends AbstractExtension
{
    public function __construct(
        private readonly LanguageProvider $languages,
        private readonly AdminConfig $config,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dgtx_entity_title', $this->entityTitle(...)),
            new TwigFunction('dgtx_language_iso', fn (int|string $id): string => (string) ($this->language($id)?->getIso() ?? $id)),
            new TwigFunction('dgtx_language_name', fn (int|string $id): string => (string) ($this->language($id)?->getName() ?? $id)),
        ];
    }

    public function entityTitle(?string $slug): string
    {
        if (null === $slug || '' === $slug) {
            return '';
        }

        foreach ($this->config->getMenu() as $key => $entry) {
            if (strcasecmp((string) $key, $slug) === 0 && isset($entry['title'])) {
                return (string) $entry['title'];
            }

            foreach ($entry['sub'] ?? [] as $subKey => $subEntry) {
                if (strcasecmp((string) $subKey, $slug) === 0 && isset($subEntry['title'])) {
                    return (string) $subEntry['title'];
                }
            }
        }

        return ucfirst($slug);
    }

    private function language(int|string $id): ?Language
    {
        foreach ($this->languages->getActiveLanguages() as $language) {
            if ($language->getId() === (int) $id) {
                return $language;
            }
        }

        return null;
    }
}
