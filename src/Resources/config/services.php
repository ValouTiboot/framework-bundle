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
    $services->load('Digitix\\FrameworkBundle\\Admin\\', '../../Admin/')
        ->exclude([
            '../../Admin/Config/',
            '../../Admin/Exception/',
            '../../Admin/Context/AdminContext.php',
            '../../Admin/Field/FieldTypeContext.php',
            '../../Admin/List/Sorter.php',
            '../../Admin/List/Paginator.php',
        ]);

    $services->set(AdminConfig::class)
        ->factory([AdminConfigFactory::class, 'fromArray'])
        ->args([abstract_arg('resolved configuration, injected by DigitixFrameworkExtension')]);

    $services->set(FieldTypeRegistry::class)
        ->args([tagged_iterator(FieldTypeRegistry::TAG, null, 'getTypeName')]);

    $services->set(FilterTypeRegistry::class)
        ->args([tagged_iterator(FilterTypeRegistry::TAG, null, 'getTypeName')]);

    $services->set(EntityClassResolver::class)
        ->arg('$namespaces', param('digitix_framework.entity_namespaces'));

    $services->set(AdminRouteLoader::class)
        ->tag('routing.loader');

    // --- Controllers, providers, listeners, security --------------------------------
    $services->load('Digitix\\FrameworkBundle\\Controller\\', '../../Controller/');
    $services->load('Digitix\\FrameworkBundle\\Provider\\', '../../Provider/');
    $services->load('Digitix\\FrameworkBundle\\EventListener\\', '../../EventListener/');
    $services->load('Digitix\\FrameworkBundle\\Security\\', '../../Security/');

    // --- Content kit: translations, mailer, cache, fixtures, validators --------------
    $services->load('Digitix\\FrameworkBundle\\Finder\\', '../../Finder/');
    $services->load('Digitix\\FrameworkBundle\\Updater\\', '../../Updater/');
    $services->load('Digitix\\FrameworkBundle\\Factory\\', '../../Factory/');
    $services->load('Digitix\\FrameworkBundle\\Utils\\', '../../Utils/')
        ->exclude(['../../Utils/ToolString.php', '../../Utils/Tools.php']);
    $services->load('Digitix\\FrameworkBundle\\Validator\\', '../../Validator/')
        ->exclude(['../../Validator/Constraints/NotBlankAtFirst.php']);
    $services->load('Digitix\\FrameworkBundle\\DataFixtures\\', '../../DataFixtures/');
};
