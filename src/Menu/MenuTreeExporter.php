<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Menu;

use Digitix\FrameworkBundle\Entity\Language;
use Digitix\FrameworkBundle\Entity\Menu;
use Digitix\FrameworkBundle\Repository\MenuRepository;

/**
 * The items of a menu in the flat format the builder works with (and posts
 * back to MenuTreePersister): one row per item, parent referenced by key,
 * titles for every language.
 *
 * @phpstan-type ExportedItem array{key: string, id: int, parent: string|null, position: int, depth: int, type: string, route: string|null, params: array<string, mixed>, idEntity: int|null, link: string|null, cssClass: string|null, target: string|null, active: bool, label: string, titles: array<string, string>, url: string|null}
 */
final class MenuTreeExporter
{
    public function __construct(
        private readonly MenuRepository $menus,
        private readonly MenuUrlResolver $urls,
    ) {
    }

    /**
     * @param Language[] $languages
     *
     * @return list<ExportedItem>
     */
    public function export(Menu $menu, array $languages, Language $urlLanguage): array
    {
        $rows = [];

        foreach ($this->menus->findItems($menu) as $item) {
            $titles = [];
            $label = '';

            foreach ($languages as $language) {
                $translation = $item->findTranslation((int) $language->getId());
                $titles[(string) $language->getLocale()] = (string) $translation?->getName();
                $label = '' !== $label ? $label : (string) $translation?->getLabel();
            }

            $rows[] = [
                'key' => self::key($item->getId()),
                'id' => (int) $item->getId(),
                'parent' => null === $item->getParent() ? null : self::key($item->getParent()->getId()),
                'position' => $item->getPosition(),
                'depth' => $item->getDepth(),
                'type' => $item->getType()->value,
                'route' => $item->getRoute(),
                'params' => $item->getRouteParams(),
                'idEntity' => $item->getIdEntity(),
                'link' => $item->getLink(),
                'cssClass' => $item->getCssClass(),
                'target' => $item->getTarget(),
                'active' => $item->isActive(),
                'label' => $label,
                'titles' => $titles,
                'url' => $this->urls->resolveItem($item, $urlLanguage),
            ];
        }

        return $rows;
    }

    public static function key(?int $id): string
    {
        return 'i'.$id;
    }
}
