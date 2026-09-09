<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Entity\Cms;
use Digitix\FrameworkBundle\Entity\Menu;
use Digitix\FrameworkBundle\Entity\MenuItem;
use Digitix\FrameworkBundle\Entity\User;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Digitix\FrameworkBundle\Repository\TranslationRepository;
use Digitix\FrameworkBundle\Translation\TranslationStatus;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Dashboard" virtual entity: a few figures about the site and shortcuts.
 * Projects extend it and override stats() to add their own numbers.
 */
class AdminDashboardController extends AdminController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            LanguageProvider::class,
            TranslationRepository::class,
        ]);
    }

    public function view(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::VIEW, $context);

        return $this->renderAdmin($this->templates()->view($context->getEntityConfig()), $this->baseVars($context) + [
            'stats' => $this->stats(),
            'latestPages' => $this->doctrine()->getRepository(Cms::class)->findBy([], ['dateUpd' => 'DESC'], 5),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function stats(): array
    {
        $registry = $this->doctrine();
        $cms = $registry->getRepository(Cms::class);
        $menus = $registry->getRepository(Menu::class);
        $users = $registry->getRepository(User::class);

        $translations = [];
        $counts = $this->container->get(TranslationRepository::class)->countByLocaleAndStatus();
        foreach ($this->container->get(LanguageProvider::class)->getActiveLanguages() as $language) {
            $locale = (string) $language->getLocale();
            $translated = $counts[$locale][TranslationStatus::Translated->value] ?? 0;
            $total = $translated + ($counts[$locale][TranslationStatus::Missing->value] ?? 0);

            $translations[] = [
                'locale' => $locale,
                'iso' => (string) $language->getIso(),
                'name' => (string) $language->getName(),
                'translated' => $translated,
                'total' => $total,
                'percent' => $total > 0 ? (int) round(100 * $translated / $total) : 0,
            ];
        }

        return [
            'cms' => ['total' => $cms->count([]), 'active' => $cms->count(['active' => true])],
            'menus' => ['total' => $menus->count([]), 'active' => $menus->count(['active' => true]), 'items' => $registry->getRepository(MenuItem::class)->count([])],
            'users' => ['total' => $users->count([]), 'active' => $users->count(['active' => true])],
            'translations' => $translations,
        ];
    }
}
