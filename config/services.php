<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Digitix\FrameworkBundle\Admin\Config\AdminConfig;
use Digitix\FrameworkBundle\Admin\Config\AdminConfigFactory;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeRegistry;
use Digitix\FrameworkBundle\Admin\Filter\FilterTypeRegistry;
use Digitix\FrameworkBundle\Admin\Persistence\EntityClassResolver;
use Digitix\FrameworkBundle\Admin\Routing\AdminRouteLoader;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->bind('$projectDir', param('kernel.project_dir'));

    // --- Admin engine -----------------------------------------------------------------
    $services->load('Digitix\\FrameworkBundle\\Admin\\', '../src/Admin/')
        ->exclude([
            '../src/Admin/Config/',
            '../src/Admin/Exception/',
            '../src/Admin/Context/AdminContext.php',
            '../src/Admin/Field/FieldTypeContext.php',
            '../src/Admin/List/Sorter.php',
            '../src/Admin/List/Paginator.php',
        ]);

    $services->set(AdminConfig::class)
        ->factory([AdminConfigFactory::class, 'fromArray'])
        ->args([abstract_arg('resolved configuration, injected by DigitixFrameworkBundle::loadExtension()')]);

    $services->set(FieldTypeRegistry::class)
        ->args([tagged_iterator(FieldTypeRegistry::TAG, null, 'getTypeName')]);

    $services->set(FilterTypeRegistry::class)
        ->args([tagged_iterator(FilterTypeRegistry::TAG, null, 'getTypeName')]);

    $services->set(EntityClassResolver::class)
        ->arg('$namespaces', param('digitix_framework.entity_namespaces'));

    $services->set(AdminRouteLoader::class)
        ->tag('routing.loader');

    // --- Controllers, providers, listeners, security --------------------------------
    $services->load('Digitix\\FrameworkBundle\\Controller\\', '../src/Controller/');
    $services->load('Digitix\\FrameworkBundle\\Provider\\', '../src/Provider/');
    $services->load('Digitix\\FrameworkBundle\\EventListener\\', '../src/EventListener/');
    $services->load('Digitix\\FrameworkBundle\\Security\\', '../src/Security/');

    // --- Content kit: translations, mailer, cache, fixtures, validators --------------
    $services->load('Digitix\\FrameworkBundle\\Finder\\', '../src/Finder/');
    $services->load('Digitix\\FrameworkBundle\\Updater\\', '../src/Updater/');
    $services->load('Digitix\\FrameworkBundle\\Factory\\', '../src/Factory/');
    $services->load('Digitix\\FrameworkBundle\\Utils\\', '../src/Utils/')
        ->exclude(['../src/Utils/ToolString.php', '../src/Utils/Tools.php']);
    $services->load('Digitix\\FrameworkBundle\\Validator\\', '../src/Validator/')
        ->exclude(['../src/Validator/Constraints/NotBlankAtFirst.php']);
    $services->load('Digitix\\FrameworkBundle\\DataFixtures\\', '../src/DataFixtures/');
};
