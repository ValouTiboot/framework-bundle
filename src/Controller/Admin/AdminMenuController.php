<?php

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Digitix\FrameworkBundle\Entity\Cms;
use Digitix\FrameworkBundle\Entity\Language;
use Digitix\FrameworkBundle\Entity\Menu;
use Digitix\FrameworkBundle\Entity\MenuItem;
use Digitix\FrameworkBundle\Entity\MenuItemTranslation;

class AdminMenuController extends AdminController
{
    public function viewEntity(string $entityName, int $entityId)
    {
        $post = $this->getContext()->getRequest()->request->all();
        $menu = $this->getContext()->getEntity()->getInstance();

        if (isset($post['menu_item'])) {
            $this->deleteItems($menu->getId());
            $this->addItems($menu, $post['menu_item']);

            $this->addFlash('success', $this->getContext()->trans('Entity successfuly updated.', [], 'Admin.Message.Success'));
            return $this->redirectToRoute(
                'dgtx_admin_entity_view_entity',
                [
                    'entityName' => $entityName,
                    'entityId' => $entityId
                ]
            );
        }


        $tplVars = [
            'menuEntity' => $this->getContext()->getEntity()->getInstance(),
            'pages' => $this->getAllPages(),
            'items' => $this->getItemByMenu($menu->getId()),
        ];

        $helperView = $this->get('dgtx.helper.view.factory')
            ->build(
                $this->get('dgtx.parameter.factory')->build()->getParameters(),
                $tplVars
            )
        ;

        return $this->display($helperView->generateView());
    }

    private function getAllPages()
    {
        $pages = [];

        $pages['pages'] = [
            'blog' => [
                'route' => 'front_blog_index',
                'label' => $this->getContext()->trans('Blog', [], 'Menu.Label')
            ],
            'contact' => [
                'route' => 'front_contact_index',
                'label' => $this->getContext()->trans('Contact', [], 'Menu.Label')
            ],
            'faq' => [
                'route' => 'front_faq_show',
                'label' => $this->getContext()->trans('Faq\'s', [], 'Menu.Label')
            ],
            'lexicon' => [
                'route' => 'front_lexicon_index',
                'label' => $this->getContext()->trans('Lexique', [], 'Menu.Label')
            ],
            'realEstateIndex' => [
                'route' => 'front_real_estate_index',
                'label' => $this->getContext()->trans('Vendre en viager', [], 'Menu.Label')
            ],
            'realEstate' => [
                'route' => 'front_real_estate_create',
                'label' => $this->getContext()->trans('Estimation', [], 'Menu.Label')
            ],
            // 'sales' => [
            //     'route' => 'front_sales_view',
            //     'label' => $this->getContext()->trans('Nos ventes', [], 'Menu.Label')
            // ],
            'viager' => [
                'route' => 'front_viager_view',
                'label' => $this->getContext()->trans('Différents viager', [], 'Menu.Label')
            ],
        ];

        $cmsRepository = $this->get('dgtx.entity.repository.provider')->getRepository(Cms::class);
        $cmsPages = $cmsRepository->findAll();

        foreach ($cmsPages as $cms) {
            $pages['cms'][] = [
                'route' => 'front_cms_show',
                'label' => $cms->getName(),
                'idEntity' => $cms->getId()
            ];
        }

        $pages['link'] = [
            'liens' => [
                'route' => '',
                'label' => $this->getContext()->trans('Link', [], 'Menu.Label')
            ]
        ];

        return $pages;
    }

    private function getItemByMenu($idMenu)
    {
        $menuItemRepository = $this->get('dgtx.entity.repository.provider')->getRepository(MenuItem::class);
        $menuItems = $menuItemRepository->findBy(['menu' => $idMenu]);

        return $menuItems;
    }

    private function deleteItems(int $idMenu): bool
    {
        $entityManager = $this->getDoctrine()->getManager();

        $menuItemRepository = $this->get('dgtx.entity.repository.provider')->getRepository(MenuItem::class);
        $menuItems = $menuItemRepository->findBy(['menu' => $idMenu]);

        if (null !== $menuItems) {
            foreach (array_reverse($menuItems) as $menuItem) {
                $entityManager->remove($menuItem);
                $entityManager->flush();
            }
        }

        return true;
    }

    private function addItems(Menu $menu, array $items): bool
    {
        $itemsArray = [];
        $entityPersister = $this->get('dgtx.entity.persister');
        $languageRepository = $this->get('dgtx.entity.repository.provider')->getRepository(Language::class);
        $languages = $languageRepository->findAll();

        foreach ($items as $item) {
            $parent = null;

            if ($item['idParent'] != 0) {
                $parent = $itemsArray[$item['idParent']];
            }

            $menuItem = new MenuItem();
            $menuItem
                ->setMenu($menu)
                ->setParent($parent)
                ->setIdEntity($item['idEntity'])
                ->setRoute($item['route'])
                ->setCssClass($item['class'])
                ->setLink($item['link'])
                ->setDepth($item['depth'])
            ;

            foreach ($languages as $language) {
                $menuItemTranslation = new MenuItemTranslation();
                $menuItemTranslation
                    ->setLanguage($language)
                    ->setName($item['name'])
                    ->setLabel($item['label'])
                ;

                $menuItem->addTranslation($menuItemTranslation);
            }

            $entityPersister->persistObject($menuItem);
            $itemsArray[$item['itemId']] = $menuItem;
        }

        return true;
    }
}
