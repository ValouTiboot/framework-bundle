<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Entity\Menu;
use Digitix\FrameworkBundle\Menu\MenuProvider;
use Digitix\FrameworkBundle\Menu\MenuSourceProvider;
use Digitix\FrameworkBundle\Menu\MenuTreeException;
use Digitix\FrameworkBundle\Menu\MenuTreeExporter;
use Digitix\FrameworkBundle\Menu\MenuTreePersister;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Menu builder (viewEntity): sources on the left, nested tree on the right,
 * saved as one JSON tree through the "save" action. Every change to a menu
 * drops its cached front rendering.
 *
 * Projects extend this controller to offer more pages: see getStaticPages().
 */
class AdminMenuController extends AdminController
{
    public const CSRF_TOKEN_ID = 'dgtx_menu';

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            LanguageProvider::class,
            MenuSourceProvider::class,
            MenuTreeExporter::class,
            MenuTreePersister::class,
            MenuProvider::class,
        ]);
    }

    public function viewEntity(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::EDIT, $context);

        $menu = $this->menuOf($context);
        $languages = $this->container->get(LanguageProvider::class)->getActiveLanguages();
        $defaultLanguage = $context->getLanguage();

        return $this->renderAdmin($this->templates()->view($context->getEntityConfig()), $this->baseVars($context) + [
            'menuEntity' => $menu,
            'languages' => $languages,
            'currentLocale' => (string) $defaultLanguage->getLocale(),
            'sources' => $this->container->get(MenuSourceProvider::class)->getSources($this->getStaticPages()),
            'items' => $this->container->get(MenuTreeExporter::class)->export($menu, $languages, $defaultLanguage),
            'maxDepth' => $this->container->get(MenuTreePersister::class)->getMaxDepth(),
            'csrf_token' => $this->container->get('security.csrf.token_manager')->getToken(self::CSRF_TOKEN_ID)->getValue(),
            'save_url' => $this->generateUrl('dgtx_admin_entity_action', [
                'entityName' => $context->getEntitySlug(),
                'action' => 'save',
                'entityId' => $menu->getId(),
            ]),
        ]);
    }

    /**
     * POST JSON {items: [...], _token} from the builder (see MenuTreePersister).
     */
    public function saveAction(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::EDIT, $context);

        $request = $context->getRequest();
        if (!$request->isMethod('POST')) {
            return new JsonResponse(['error' => 'POST expected.'], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $payload = json_decode((string) $request->getContent(), true);
        if (!\is_array($payload)) {
            return new JsonResponse(['error' => 'Invalid JSON payload.'], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_ID, (string) ($payload['_token'] ?? ''))) {
            return new JsonResponse(['error' => $this->trans('Invalid security token, please try again.', [], 'Admin.Message.Error')], Response::HTTP_FORBIDDEN);
        }

        $menu = $this->menuOf($context);

        try {
            $ids = $this->container->get(MenuTreePersister::class)->save($menu, $payload);
        } catch (MenuTreeException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse([
            'ids' => $ids,
            'saved_at' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ]);
    }

    public function edit(AdminContext $context): Response
    {
        $response = parent::edit($context);

        if ($response instanceof RedirectResponse && ($menu = $context->getEntity()) instanceof Menu) {
            $this->container->get(MenuProvider::class)->invalidate($menu);
        }

        return $response;
    }

    public function delete(AdminContext $context): RedirectResponse
    {
        try {
            $menu = $context->getEntity();
        } catch (NotFoundHttpException) {
            $menu = null;
        }

        $response = parent::delete($context);

        if ($menu instanceof Menu) {
            $this->container->get(MenuProvider::class)->invalidate($menu);
        }

        return $response;
    }

    /**
     * Pages a project offers in the builder on top of "digitix_framework.menu.pages".
     * Override it to add dynamic entries (categories, products...).
     *
     * @return array<int, array{route: string, label: string, params?: array<string, mixed>}>
     */
    protected function getStaticPages(): array
    {
        return [];
    }

    private function menuOf(AdminContext $context): Menu
    {
        $menu = $context->getEntity();

        if (!$menu instanceof Menu || null === $menu->getId()) {
            throw new NotFoundHttpException('Menu not found.');
        }

        return $menu;
    }
}
