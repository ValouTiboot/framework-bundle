<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Entity\Cms;
use Digitix\FrameworkBundle\Entity\Menu;
use Digitix\FrameworkBundle\Entity\MenuItem;
use Digitix\FrameworkBundle\Entity\MenuItemTranslation;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Drag & drop menu builder (viewEntity). The tree is posted as a flat
 * "menu_item[n][...]" list and replaces the existing items.
 */
class AdminMenuController extends AdminController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [LanguageProvider::class]);
    }

    public function viewEntity(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::EDIT, $context);

        $menu = $context->getEntity();
        if (!$menu instanceof Menu) {
            throw new NotFoundHttpException('Menu not found.');
        }

        $request = $context->getRequest();

        if ($request->isMethod('POST') && $request->request->has('menu_item')) {
            $this->replaceItems($menu, (array) $request->request->all('menu_item'));
            $this->addFlash('success', $this->trans('Entity successfuly updated.'));

            return $this->redirectToRoute('dgtx_admin_entity_view_entity', [
                'entityName' => $context->getEntitySlug(),
                'entityId' => $menu->getId(),
            ]);
        }

        return $this->renderAdmin(
            $this->templates()->view($context->getEntityConfig()),
            $this->baseVars($context) + [
                'menuEntity' => $menu,
                'pages' => $this->getAvailablePages(),
                'items' => $this->doctrine()->getRepository(MenuItem::class)->findBy(['menu' => $menu]),
            ]
        );
    }

    /**
     * Pages offered on the left panel, grouped: static routes, CMS pages, free links.
     * Override getStaticPages() in a project controller to add its own routes.
     *
     * @return array<string, array<string|int, array{route: string, label: string, idEntity?: int|null}>>
     */
    protected function getAvailablePages(): array
    {
        $pages = [];

        if ($static = $this->getStaticPages()) {
            $pages['pages'] = $static;
        }

        foreach ($this->doctrine()->getRepository(Cms::class)->findAll() as $cms) {
            $pages['cms'][] = [
                'route' => 'front_cms_show',
                'label' => (string) $cms->getName(),
                'idEntity' => $cms->getId(),
            ];
        }

        $pages['link'] = [
            'link' => ['route' => '', 'label' => $this->trans('Link', 'Menu.Label')],
        ];

        return $pages;
    }

    /**
     * @return array<string, array{route: string, label: string}>
     */
    protected function getStaticPages(): array
    {
        return [];
    }

    /**
     * @param array<int|string, array<string, mixed>> $items
     */
    private function replaceItems(Menu $menu, array $items): void
    {
        $manager = $this->doctrine()->getManagerForClass(MenuItem::class);
        $existing = $manager->getRepository(MenuItem::class)->findBy(['menu' => $menu]);

        // children before parents
        foreach (array_reverse($existing) as $item) {
            $manager->remove($item);
        }
        $manager->flush();

        $languages = $this->container->get(LanguageProvider::class)->getActiveLanguages();
        $created = [];

        foreach ($items as $item) {
            $parentKey = $item['idParent'] ?? 0;

            $menuItem = (new MenuItem())
                ->setMenu($menu)
                ->setParent(0 != $parentKey ? ($created[$parentKey] ?? null) : null)
                ->setIdEntity(isset($item['idEntity']) && '' !== $item['idEntity'] ? (int) $item['idEntity'] : null)
                ->setRoute($item['route'] ?? null)
                ->setCssClass($item['class'] ?? null)
                ->setLink($item['link'] ?? null)
                ->setDepth((int) ($item['depth'] ?? 0));

            foreach ($languages as $language) {
                $menuItem->addTranslation((new MenuItemTranslation())
                    ->setLanguage($language)
                    ->setName((string) ($item['name'] ?? ''))
                    ->setLabel((string) ($item['label'] ?? '')));
            }

            $this->persister()->save($menuItem);
            $created[$item['itemId'] ?? count($created)] = $menuItem;
        }
    }
}
