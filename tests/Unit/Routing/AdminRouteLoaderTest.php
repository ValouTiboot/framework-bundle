<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Routing;

use Digitix\FrameworkBundle\Admin\Routing\AdminRouteLoader;
use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use PHPUnit\Framework\TestCase;

final class AdminRouteLoaderTest extends TestCase
{
    public function testRoutes(): void
    {
        $loader = new AdminRouteLoader();
        self::assertTrue($loader->supports('.', 'digitix_framework'));
        self::assertFalse($loader->supports('.', 'yaml'));

        $routes = $loader->load('.', 'digitix_framework');

        self::assertSame([
            'dgtx_admin_login',
            'dgtx_admin_logout',
            'dgtx_admin_entity_view',
            'dgtx_admin_entity_view_entity',
            'dgtx_admin_entity_read',
            'dgtx_admin_entity_create',
            'dgtx_admin_entity_edit',
            'dgtx_admin_entity_delete',
            'dgtx_admin_entity_ajax_sortable',
        ], array_keys($routes->all()));

        $delete = $routes->get('dgtx_admin_entity_delete');
        self::assertNotNull($delete);
        self::assertSame(['POST'], $delete->getMethods(), 'delete is never a GET');
        self::assertSame(AdminController::class.'::delete', $delete->getDefault('_controller'));
        self::assertTrue($delete->getDefault('_dgtx_admin'));
        self::assertSame('\d+', $delete->getRequirement('entityId'));

        $this->expectException(\RuntimeException::class);
        $loader->load('.', 'digitix_framework');
    }
}
