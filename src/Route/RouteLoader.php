<?php

namespace Digitix\FrameworkBundle\Route;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteLoader extends Loader
{
    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null): RouteCollection
    {
        $routeCollection = new RouteCollection();

        $routeCollection->add('dgtx_admin_login', (new Route('/admin/', [
            '_controller' => 'dgtx.admin.controller.login::login',
        ]))->setMethods(['GET','POST']));

        $routeCollection->add('dgtx_admin_entity_view', (new Route('/admin/{entityName}/view', [
            '_controller' => 'dgtx.admin.controller::view',
        ]))->setMethods(['GET','POST']));

        $routeCollection->add('dgtx_admin_entity_read', (new Route('/admin/{entityName}/read', [
            '_controller' => 'dgtx.admin.controller::read',
        ]))->setMethods(['GET','POST']));

        $routeCollection->add('dgtx_admin_entity_create', (new Route('/admin/{entityName}/create', [
            '_controller' => 'dgtx.admin.controller::create',
            ]))->setMethods(['GET','POST']));

        $routeCollection->add('dgtx_admin_entity_edit', (new Route('/admin/{entityName}/edit/{entityId}', [
            '_controller' => 'dgtx.admin.controller::edit',
        ]))->setMethods(['GET','POST']));

        $routeCollection->add('dgtx_admin_entity_delete', (new Route('/admin/{entityName}/delete', [
            '_controller' => 'dgtx.admin.controller::delete',
        ]))->setMethods(['GET','POST']));

        $routeCollection->add('dgtx_admin_entity_ajax_sortable', (new Route('/admin/{entityName}/sort', [
            '_controller' => 'dgtx.admin.controller::ajaxSortable',
        ]))->setMethods(['GET','POST']));

        return $routeCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'digitix_framework' === $type;
    }
}
