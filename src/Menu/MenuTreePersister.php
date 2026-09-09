<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Menu;

use Digitix\FrameworkBundle\Admin\Persistence\EntityPersister;
use Digitix\FrameworkBundle\Entity\Language;
use Digitix\FrameworkBundle\Entity\Menu;
use Digitix\FrameworkBundle\Entity\MenuItem;
use Digitix\FrameworkBundle\Entity\MenuItemTranslation;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Digitix\FrameworkBundle\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Saves the tree posted by the menu builder as a diff against the stored
 * items: existing items (by id) are updated in place, new ones are created,
 * missing ones are deleted. Ids and translations survive a save.
 *
 * Payload: {"items": [{key, id|null, parent (key)|null, position, type,
 * route, params, idEntity, link, cssClass, target, active, label, titles: {locale: title}}]}
 *
 * @phpstan-type Row array{key: string, id: int|null, parent: string|null, position: int, depth: int, type: MenuItemType, route: string|null, params: array<string, mixed>, idEntity: int|null, link: string|null, cssClass: string|null, target: string|null, active: bool, label: string, titles: array<string, string>}
 */
final class MenuTreePersister
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MenuRepository $menus,
        private readonly LanguageProvider $languages,
        private readonly EntityPersister $persister,
        private readonly MenuProvider $menuProvider,
        private readonly int $maxDepth,
    ) {
    }

    public function getMaxDepth(): int
    {
        return $this->maxDepth;
    }

    /**
     * @param array<string, mixed> $payload decoded JSON
     *
     * @return array<string, int> key => id, for every saved item
     *
     * @throws MenuTreeException when the tree is invalid (nothing is written)
     */
    public function save(Menu $menu, array $payload): array
    {
        $rows = $this->normalize($payload);
        $languages = $this->languages->getActiveLanguages();

        $existing = [];
        foreach ($this->menus->findItems($menu) as $item) {
            $existing[(int) $item->getId()] = $item;
        }

        /** @var array<string, MenuItem> $entities */
        $entities = [];

        foreach ($rows as $row) {
            if (null !== $row['id']) {
                $entity = $existing[$row['id']] ?? throw new MenuTreeException(sprintf('Item %d does not belong to this menu.', $row['id']));
            } else {
                $entity = (new MenuItem())->setMenu($menu);
            }

            $entity
                ->setParent(null === $row['parent'] ? null : $entities[$row['parent']])
                ->setPosition($row['position'])
                ->setDepth($row['depth'])
                ->setRoute($row['route'])
                ->setRouteParams($row['params'])
                ->setIdEntity($row['idEntity'])
                ->setLink($row['link'])
                ->setCssClass($row['cssClass'])
                ->setTarget($row['target'])
                ->setActive($row['active']);

            $this->applyTranslations($entity, $row, $languages);
            $entities[$row['key']] = $entity;
        }

        // items that left the tree: deepest first, so children go before their parent
        $deleted = array_filter($existing, static fn (MenuItem $item) => !\in_array($item, $entities, true));
        usort($deleted, static fn (MenuItem $a, MenuItem $b) => $b->getDepth() <=> $a->getDepth());
        foreach ($deleted as $item) {
            $this->entityManager->remove($item);
        }

        $this->persister->saveAll([...array_values($entities), $menu]);
        $this->menuProvider->invalidate($menu);

        $ids = [];
        foreach ($entities as $key => $entity) {
            $ids[$key] = (int) $entity->getId();
        }

        return $ids;
    }

    /**
     * @param Row        $row
     * @param Language[] $languages
     */
    private function applyTranslations(MenuItem $item, array $row, array $languages): void
    {
        foreach ($languages as $language) {
            $translation = $item->findTranslation((int) $language->getId());

            if (null === $translation) {
                $translation = (new MenuItemTranslation())->setLanguage($language);
                $item->addTranslation($translation);
            }

            $translation
                ->setName(trim($row['titles'][(string) $language->getLocale()] ?? ''))
                ->setLabel($row['label']);
        }
    }

    /**
     * Validates the payload and computes the depth of every item.
     *
     * @param array<string, mixed> $payload
     *
     * @return list<Row> parents before children
     */
    private function normalize(array $payload): array
    {
        $items = $payload['items'] ?? null;

        if (!\is_array($items)) {
            throw new MenuTreeException('The payload must contain an "items" list.');
        }

        /** @var array<string, Row> $rows */
        $rows = [];

        foreach ($items as $index => $item) {
            if (!\is_array($item)) {
                throw new MenuTreeException(sprintf('Item #%s is not an object.', $index));
            }

            $key = trim((string) ($item['key'] ?? ''));
            if ('' === $key || isset($rows[$key])) {
                throw new MenuTreeException(sprintf('Item #%s has a missing or duplicated key.', $index));
            }

            $type = MenuItemType::tryFrom((string) ($item['type'] ?? ''))
                ?? throw new MenuTreeException(sprintf('Item "%s": unknown type "%s".', $key, (string) ($item['type'] ?? '')));

            $titles = [];
            foreach ((array) ($item['titles'] ?? []) as $locale => $title) {
                $titles[(string) $locale] = \is_scalar($title) ? (string) $title : '';
            }

            $label = trim((string) ($item['label'] ?? ''));
            if ('' === $label && '' === trim(implode('', $titles))) {
                throw new MenuTreeException(sprintf('Item "%s" needs a title.', $key));
            }

            $row = [
                'key' => $key,
                'id' => isset($item['id']) && '' !== $item['id'] ? (int) $item['id'] : null,
                'parent' => isset($item['parent']) && '' !== $item['parent'] ? (string) $item['parent'] : null,
                'position' => (int) ($item['position'] ?? 0),
                'depth' => 0,
                'type' => $type,
                'route' => null,
                'params' => [],
                'idEntity' => null,
                'link' => null,
                'cssClass' => self::nullableString($item['cssClass'] ?? null),
                'target' => '_blank' === ($item['target'] ?? null) || true === ($item['target'] ?? null) ? '_blank' : null,
                'active' => filter_var($item['active'] ?? true, \FILTER_VALIDATE_BOOLEAN),
                'label' => $label,
                'titles' => $titles,
            ];

            switch ($type) {
                case MenuItemType::Link:
                    $row['link'] = self::nullableString($item['link'] ?? null)
                        ?? throw new MenuTreeException(sprintf('Item "%s": a link needs a URL.', $key));
                    break;
                case MenuItemType::Cms:
                    $row['idEntity'] = (int) ($item['idEntity'] ?? 0) ?: throw new MenuTreeException(sprintf('Item "%s": a CMS item needs a page.', $key));
                    $row['route'] = MenuItemType::CMS_ROUTE;
                    break;
                case MenuItemType::Route:
                    $row['route'] = self::nullableString($item['route'] ?? null)
                        ?? throw new MenuTreeException(sprintf('Item "%s": a page item needs a route.', $key));
                    $row['params'] = \is_array($item['params'] ?? null) ? $item['params'] : [];
                    break;
            }

            $rows[$key] = $row;
        }

        foreach ($rows as $key => $row) {
            $rows[$key]['depth'] = $this->depthOf($rows, $key);
        }

        $rows = array_values($rows);
        usort($rows, static fn (array $a, array $b) => [$a['depth'], $a['position']] <=> [$b['depth'], $b['position']]);

        return $rows;
    }

    /**
     * @param array<string, Row> $rows
     */
    private function depthOf(array $rows, string $key): int
    {
        $depth = 0;
        $current = $key;

        while (null !== ($parent = $rows[$current]['parent'])) {
            if (!isset($rows[$parent])) {
                throw new MenuTreeException(sprintf('Item "%s" references an unknown parent "%s".', $current, $parent));
            }

            if (++$depth >= $this->maxDepth) {
                throw new MenuTreeException(sprintf('The menu cannot have more than %d levels.', $this->maxDepth));
            }

            $current = $parent;
        }

        return $depth;
    }

    private static function nullableString(mixed $value): ?string
    {
        if (!\is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return '' === $value ? null : $value;
    }
}
