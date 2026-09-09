<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Menu;

use Digitix\FrameworkBundle\Entity\Language;
use Digitix\FrameworkBundle\Entity\Menu;
use Digitix\FrameworkBundle\Entity\MenuItem;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Digitix\FrameworkBundle\Repository\MenuRepository;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Builds the tree of a menu for the front, in a language, with URLs
 * resolved and inactive items removed. Trees are cached per menu and locale;
 * the cache is dropped whenever the menu or its items are saved.
 */
final class MenuProvider
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private readonly MenuRepository $menus,
        private readonly LanguageProvider $languages,
        private readonly MenuUrlResolver $urls,
        private readonly CacheItemPoolInterface $cache,
    ) {
    }

    /**
     * @param string|int  $identifier menu code ("main") or id
     * @param string|null $locale     defaults to the default language
     *
     * @return MenuTree|null null when the menu does not exist or is disabled
     */
    public function getTree(string|int $identifier, ?string $locale = null): ?MenuTree
    {
        $language = $this->resolveLanguage($locale);
        $item = $this->cache->getItem(self::cacheKey($identifier, (string) $language->getLocale()));

        if (!$item->isHit()) {
            $menu = $this->menus->findOneByCodeOrId($identifier);
            $data = null === $menu || !$menu->isActive() ? null : $this->build($menu, $language)->toArray();

            $item->set($data)->expiresAfter(self::CACHE_TTL);
            $this->cache->save($item);
        } else {
            $data = $item->get();
        }

        return \is_array($data) ? MenuTree::fromArray($data) : null;
    }

    /** Forgets the cached trees of a menu, for every active language. */
    public function invalidate(Menu $menu): void
    {
        $keys = [];

        foreach ($this->languages->getActiveLanguages() as $language) {
            foreach (array_filter([$menu->getId(), $menu->getCode()]) as $identifier) {
                $keys[] = self::cacheKey($identifier, (string) $language->getLocale());
            }
        }

        if ([] !== $keys) {
            $this->cache->deleteItems($keys);
        }
    }

    /** Uncached build, used by the admin preview. */
    public function build(Menu $menu, Language $language): MenuTree
    {
        $languageId = (int) $language->getId();
        $nodes = [];
        $roots = [];

        // parents come first (ordered by depth), so a parent node always exists when its child shows up
        foreach ($this->menus->findItems($menu) as $item) {
            if (!$item->isActive()) {
                continue;
            }

            $parentId = $item->getParent()?->getId();
            if (null !== $parentId && !isset($nodes[$parentId])) {
                continue; // parent inactive or unresolvable: the branch is hidden
            }

            $url = $this->urls->resolveItem($item, $language);
            if (null === $url) {
                continue;
            }

            $node = new MenuNode(
                (int) $item->getId(),
                self::title($item, $languageId),
                $url,
                $item->getTarget(),
                $item->getCssClass(),
                $item->getType(),
                $item->getDepth(),
            );

            $nodes[$node->id] = $node;

            if (null === $parentId) {
                $roots[] = $node;
            } else {
                $nodes[$parentId]->children[] = $node;
            }
        }

        $menu->setCurrentLanguageId($languageId);

        return new MenuTree((int) $menu->getId(), (string) $menu->getCode(), (string) $menu->getName(), (string) $language->getLocale(), $roots);
    }

    private static function title(MenuItem $item, int $languageId): string
    {
        $translation = $item->findTranslation($languageId);
        $title = trim((string) $translation?->getName());

        if ('' === $title) {
            $title = trim((string) ($translation?->getLabel() ?? $item->getTranslation()?->getLabel()));
        }

        return '' !== $title ? $title : (string) ($item->getRoute() ?? $item->getLink());
    }

    private function resolveLanguage(?string $locale): Language
    {
        if (null !== $locale) {
            foreach ($this->languages->getActiveLanguages() as $language) {
                if ($language->getLocale() === $locale) {
                    return $language;
                }
            }
        }

        return $this->languages->getDefaultLanguage();
    }

    private static function cacheKey(string|int $identifier, string $locale): string
    {
        return 'dgtx_menu.'.hash('xxh128', strtolower((string) $identifier).'|'.$locale);
    }
}
