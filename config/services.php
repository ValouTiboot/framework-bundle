<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Digitix\FrameworkBundle\Admin\Config\AdminConfig;
use Digitix\FrameworkBundle\Admin\Config\AdminConfigFactory;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeRegistry;
use Digitix\FrameworkBundle\Admin\Filter\FilterTypeRegistry;
use Digitix\FrameworkBundle\Admin\Persistence\EntityClassResolver;
use Digitix\FrameworkBundle\Admin\Routing\AdminRouteLoader;
use Digitix\FrameworkBundle\Translation\TranslationCompiler;
use Digitix\FrameworkBundle\Translation\TranslationExtractor;
use Digitix\FrameworkBundle\Translation\TranslationSynchronizer;

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

    // --- Translations: extraction from the code, database sync, catalogue files ------
    $services->load('Digitix\\FrameworkBundle\\Translation\\', '../src/Translation/')
        ->exclude([
            '../src/Translation/RuntimeTranslator.php',
            '../src/Translation/SyncReport.php',
            '../src/Translation/TranslationStatus.php',
        ]);
    $services->load('Digitix\\FrameworkBundle\\Repository\\', '../src/Repository/');
    $services->load('Digitix\\FrameworkBundle\\Command\\', '../src/Command/');

    $services->set(TranslationExtractor::class)
        ->arg('$extractor', service('translation.extractor'))
        ->arg('$paths', param('digitix_framework.translation.paths'));

    $services->set(TranslationSynchronizer::class)
        ->arg('$translator', service('translator'));

    $services->set(TranslationCompiler::class)
        ->arg('$outputDir', param('digitix_framework.translation.output_dir'))
        ->arg('$translatorCacheDir', '%kernel.cache_dir%/translations')
        ->arg('$translator', service('translator.default'));

    // --- Content kit: mailer, cache, fixtures, validators ------------------------------
    $services->load('Digitix\\FrameworkBundle\\Utils\\', '../src/Utils/')
        ->exclude(['../src/Utils/ToolString.php', '../src/Utils/Tools.php']);
    $services->load('Digitix\\FrameworkBundle\\Validator\\', '../src/Validator/')
        ->exclude(['../src/Validator/Constraints/NotBlankAtFirst.php']);
    $services->load('Digitix\\FrameworkBundle\\DataFixtures\\', '../src/DataFixtures/');
};
