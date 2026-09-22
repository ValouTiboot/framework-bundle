<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Routing;

use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Digitix\FrameworkBundle\Controller\Admin\AdminLoginController;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Generic admin routes. "_controller" always targets the generic
 * AdminController; AdminRequestListener swaps it for the entity's own
 * controller when one is configured.
 */
final class AdminRouteLoader extends Loader
{
    public const TYPE = 'digitix_framework';
    public const ACTION_REQUIREMENT = '[a-z][a-z0-9_]*';

    private bool $loaded = false;

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new \RuntimeException('Do not add the "digitix_framework" routes twice.');
        }
        $this->loaded = true;

        $routes = new RouteCollection();

        $routes->add('dgtx_admin_login', $this->route('/admin/', AdminLoginController::class, 'login'));
        $routes->add('dgtx_admin_logout', $this->route('/admin/logout', AdminLoginController::class, 'logout'));

        $routes->add('dgtx_admin_entity_view', $this->route('/admin/{entityName}/view', AdminController::class, 'view'));
        $routes->add('dgtx_admin_entity_view_entity', $this->route('/admin/{entityName}/view/{entityId}', AdminController::class, 'viewEntity', ['entityId' => '\d+']));
        $routes->add('dgtx_admin_entity_read', $this->route('/admin/{entityName}', AdminController::class, 'read'));
        $routes->add('dgtx_admin_entity_create', $this->route('/admin/{entityName}/create', AdminController::class, 'create'));
        $routes->add('dgtx_admin_entity_edit', $this->route('/admin/{entityName}/edit/{entityId}', AdminController::class, 'edit', ['entityId' => '\d+']));
        $routes->add('dgtx_admin_entity_delete', $this->route('/admin/{entityName}/delete/{entityId}', AdminController::class, 'delete', ['entityId' => '\d+'], ['POST']));
        $routes->add('dgtx_admin_entity_ajax_sortable', $this->route('/admin/{entityName}/sort', AdminController::class, 'ajaxSortable', [], ['POST']));

        // custom actions of an entity controller: "{action}Action(AdminContext $context)"
        $routes->add('dgtx_admin_entity_action', $this->route('/admin/{entityName}/action/{action}/{entityId}', AdminController::class, 'action', ['action' => self::ACTION_REQUIREMENT, 'entityId' => '\d+']));
        $routes->add('dgtx_admin_entity_action_collection', $this->route('/admin/{entityName}/action/{action}', AdminController::class, 'action', ['action' => self::ACTION_REQUIREMENT]));

        return $routes;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return self::TYPE === $type;
    }

    /**
     * @param array<string, string> $requirements
     * @param string[]              $methods
     */
    private function route(string $path, string $controller, string $action, array $requirements = [], array $methods = ['GET', 'POST']): Route
    {
        return (new Route($path, [
            '_controller' => $controller.'::'.$action,
            '_dgtx_admin' => true,
        ]))
            ->setRequirements($requirements)
            ->setMethods($methods);
    }
}
