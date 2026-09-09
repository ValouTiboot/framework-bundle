<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Config;

use Digitix\FrameworkBundle\Admin\Exception\UnknownEntityException;

/**
 * Immutable, compiled view of the "digitix_framework" configuration.
 *
 * Built once at container compile time (see AdminConfigFactory), it is the
 * single source of truth for everything the admin generator needs: menu,
 * entities, their list/form/view definitions.
 */
final class AdminConfig
{
    /**
     * @param array<string, mixed>        $menu          raw admin_menu tree (consumed as-is by the menu template)
     * @param array<string, EntityConfig> $entities      keyed by lower-cased entity name
     * @param array<string, FormConfig>   $frontForms    front_entities forms, keyed by lower-cased name
     */
    public function __construct(
        private readonly array $menu,
        private readonly array $entities,
        private readonly array $frontForms = [],
    ) {
    }

    /** @return array<string, mixed> */
    public function getMenu(): array
    {
        return $this->menu;
    }

    /**
     * Title of the admin menu entry (or sub-entry) of an entity, if any:
     * "Pages" for "cms".
     */
    public function getEntityTitle(string $slug): ?string
    {
        foreach ($this->menu as $key => $entry) {
            if (0 === strcasecmp((string) $key, $slug) && isset($entry['title'])) {
                return (string) $entry['title'];
            }

            foreach ($entry['sub'] ?? [] as $subKey => $subEntry) {
                if (0 === strcasecmp((string) $subKey, $slug) && isset($subEntry['title'])) {
                    return (string) $subEntry['title'];
                }
            }
        }

        return null;
    }

    public function hasEntity(string $name): bool
    {
        return isset($this->entities[self::key($name)]);
    }

    public function getEntity(string $name): EntityConfig
    {
        return $this->entities[self::key($name)] ?? throw new UnknownEntityException($name, array_map(
            static fn (EntityConfig $entity) => $entity->name,
            $this->entities
        ));
    }

    /** @return array<string, EntityConfig> */
    public function getEntities(): array
    {
        return $this->entities;
    }

    public function hasFrontForm(string $name): bool
    {
        return isset($this->frontForms[self::key($name)]);
    }

    public function getFrontForm(string $name): FormConfig
    {
        return $this->frontForms[self::key($name)] ?? throw new UnknownEntityException($name, array_keys($this->frontForms));
    }

    /** @return array<string, FormConfig> */
    public function getFrontForms(): array
    {
        return $this->frontForms;
    }

    /**
     * Entity names are matched case-insensitively so that the URL slug
     * ("cmsCategory"), the config key ("CmsCategory") and any other spelling
     * resolve to the same definition.
     */
    public static function key(string $name): string
    {
        return strtolower($name);
    }
}
