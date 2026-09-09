<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Menu;

use Digitix\FrameworkBundle\Entity\Cms;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * What the menu builder offers to add: the project pages declared in
 * "digitix_framework.menu.pages" (plus the ones a controller adds), and the
 * active CMS pages.
 *
 * @phpstan-type Source array{type: string, label: string, route: string|null, params: array<string, mixed>, idEntity: int|null, url: string|null}
 */
final class MenuSourceProvider
{
    public const LABEL_DOMAIN = 'Menu.Label';

    /**
     * @param array<int, array{route: string, label: string, params?: array<string, mixed>}> $pages
     */
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly LanguageProvider $languages,
        private readonly MenuUrlResolver $urls,
        private readonly TranslatorInterface $translator,
        private readonly array $pages,
    ) {
    }

    /**
     * @param array<int, array{route: string, label: string, params?: array<string, mixed>}> $extraPages pages added by the controller
     *
     * @return array{pages: list<Source>, cms: list<Source>}
     */
    public function getSources(array $extraPages = []): array
    {
        $language = $this->languages->getDefaultLanguage();
        $pages = [];

        foreach (array_merge($this->pages, $extraPages) as $page) {
            $params = (array) ($page['params'] ?? []);

            $pages[] = [
                'type' => MenuItemType::Route->value,
                'label' => $this->translator->trans($page['label'], [], self::LABEL_DOMAIN),
                'route' => $page['route'],
                'params' => $params,
                'idEntity' => null,
                'url' => $this->urls->resolve(MenuItemType::Route, $page['route'], $params, null, null, $language),
            ];
        }

        $cms = [];
        /** @var Cms $page */
        foreach ($this->registry->getRepository(Cms::class)->findBy(['active' => true]) as $page) {
            $page->setCurrentLanguageId((int) $language->getId());

            $cms[] = [
                'type' => MenuItemType::Cms->value,
                'label' => (string) $page->getName(),
                'route' => MenuItemType::CMS_ROUTE,
                'params' => [],
                'idEntity' => $page->getId(),
                'url' => $this->urls->resolve(MenuItemType::Cms, MenuItemType::CMS_ROUTE, [], $page->getId(), null, $language),
            ];
        }

        usort($cms, static fn (array $a, array $b) => strcasecmp($a['label'], $b['label']));

        return ['pages' => $pages, 'cms' => $cms];
    }
}
