<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\DependencyInjection;

use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Digitix\FrameworkBundle\Controller\Admin\AdminMenuController;
use Digitix\FrameworkBundle\DependencyInjection\AdminConfigResolver;
use Digitix\FrameworkBundle\Entity\Menu;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

final class AdminConfigResolverTest extends TestCase
{
    private const NAMESPACES = [
        'entity_namespaces' => ['App\\Entity\\', 'Digitix\\FrameworkBundle\\Entity\\'],
        'controller_namespaces' => ['App\\Controller\\Admin\\', 'Digitix\\FrameworkBundle\\Controller\\Admin\\'],
    ];

    public function testResolvesByConvention(): void
    {
        $resolved = AdminConfigResolver::resolve(self::NAMESPACES + ['admin_entities' => [
            'Menu' => [],
            'Dashboard' => [],
            'User' => [],
        ]]);

        self::assertSame(Menu::class, $resolved['admin_entities']['Menu']['class']);
        self::assertSame(AdminMenuController::class, $resolved['admin_entities']['Menu']['controller'], '"Admin{Name}Controller" is found by convention');

        self::assertNull($resolved['admin_entities']['Dashboard']['class'], 'no class = virtual entity');
        self::assertSame(AdminController::class, $resolved['admin_entities']['Dashboard']['controller']);

        self::assertSame(AdminController::class, $resolved['admin_entities']['User']['controller'], 'generic controller when none matches');
    }

    public function testExplicitValuesWin(): void
    {
        $resolved = AdminConfigResolver::resolve(self::NAMESPACES + ['admin_entities' => [
            'Anything' => ['class' => Menu::class, 'controller' => AdminMenuController::class],
        ]]);

        self::assertSame(Menu::class, $resolved['admin_entities']['Anything']['class']);
        self::assertSame(AdminMenuController::class, $resolved['admin_entities']['Anything']['controller']);
    }

    public function testUnknownExplicitClassThrows(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        AdminConfigResolver::resolve(self::NAMESPACES + ['admin_entities' => ['X' => ['class' => 'App\\Entity\\Nope']]]);
    }

    public function testControllerMustExtendAdminController(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('must extend');

        AdminConfigResolver::resolve(self::NAMESPACES + ['admin_entities' => ['X' => ['controller' => \stdClass::class]]]);
    }
}
