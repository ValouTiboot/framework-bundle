<?php

namespace Digitix\FrameworkBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Digitix\FrameworkBundle\Config\ViewConfig;
use Digitix\FrameworkBundle\Route\RouteLoader;
use Digitix\FrameworkBundle\Config\FieldConfig;
use Digitix\FrameworkBundle\Config\EntityConfig;
use Symfony\Component\DependencyInjection\Alias;
use Digitix\FrameworkBundle\Config\AdminMenuConfig;
use Symfony\Component\DependencyInjection\Reference;
use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\Config\ViewConfigInterface;
use Digitix\FrameworkBundle\Config\FieldConfigInterface;
use Digitix\FrameworkBundle\Repository\EntityRepository;
use Digitix\FrameworkBundle\Config\EntityConfigInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Digitix\FrameworkBundle\Config\AdminMenuConfigInterface;
use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Digitix\FrameworkBundle\Controller\Admin\AdminLoginController;
use Digitix\FrameworkBundle\Controller\Admin\AdminParameterController;
use Digitix\FrameworkBundle\Controller\Admin\AdminPerformanceController;
use Digitix\FrameworkBundle\Controller\Admin\AdminTranslationController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

class DigitixFrameworkExtension extends Extension
{
    const ALIAS_ADMIN_MENU_CONFIG = 'dgtx.admin.menu.config';
    const ALIAS_VIEW_CONFIG = 'dgtx.view.config';
    const ALIAS_ENTITY_CONFIG = 'dgtx.entity.config';
    const ALIAS_FIELD_CONFIG = 'dgtx.field.config';

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('digitix_framework.digitix');
        // add parameter to container
        $definition->setArgument(0, $config['admin_menu']);

        // if have interface
        $container->registerForAutoconfiguration(AdminMenuConfigInterface::class)->addTag(self::ALIAS_ADMIN_MENU_CONFIG);
        $container->registerForAutoconfiguration(ViewConfigInterface::class)->addTag(self::ALIAS_VIEW_CONFIG);
        $container->registerForAutoconfiguration(FieldConfigInterface::class)->addTag(self::ALIAS_FIELD_CONFIG);
        $container->registerForAutoconfiguration(EntityConfigInterface::class)->addTag(self::ALIAS_ENTITY_CONFIG);
        $container->registerForAutoconfiguration(EntityRepository::class)->addTag('doctrine.repository_service');

        $container->register('digitix.route_loader', RouteLoader::class)
            ->setPublic(false)
            ->addTag('routing.loader')
        ;

        $container->register(self::ALIAS_ADMIN_MENU_CONFIG, AdminMenuConfig::class)
            ->setPublic(false)
            ->setArguments(['$params' => $config])
        ;

        $container->register(self::ALIAS_VIEW_CONFIG, ViewConfig::class)
            ->setPublic(true)
            ->setArguments([
                '$params' => $config,
                '$context' => new Reference(ContextProvider::class)
            ]
        );

        $container->register(self::ALIAS_FIELD_CONFIG, FieldConfig::class)
            ->setPublic(true)
            ->setArguments([
                '$params' => $config,
                '$context' => new Reference(ContextProvider::class)
            ]
        );

        $container->register(self::ALIAS_ENTITY_CONFIG, EntityConfig::class)
            ->setPublic(true)
            ->setArguments([
                '$params' => $config,
                '$context' => new Reference(ContextProvider::class)
            ]
        );

        $container->register('dgtx.admin.controller', AdminController::class)
            ->setAutowired(true)
            ->setPublic(true)
            ->addTag('container.service_subscriber')
            ->addTag('controller.service_arguments')
        ;

        $container->register('dgtx.admin.controller.login', AdminLoginController::class)
            ->setAutowired(true)
            ->setPublic(true)
            ->addTag('container.service_subscriber')
            ->addTag('controller.service_arguments')
        ;

        $container->register('dgtx.admin.controller.translation', AdminTranslationController::class)
            ->setAutowired(true)
            ->setPublic(true)
            ->addTag('container.service_subscriber')
            ->addTag('controller.service_arguments')
        ;

        $container->register('dgtx.admin.controller.performance', AdminPerformanceController::class)
            ->setAutowired(true)
            ->setPublic(true)
            ->addTag('container.service_subscriber')
            ->addTag('controller.service_arguments')
        ;

        $container->register('dgtx.admin.controller.parameter', AdminParameterController::class)
            ->setAutowired(true)
            ->setPublic(true)
            ->addTag('container.service_subscriber')
            ->addTag('controller.service_arguments')
        ;

        $container->setAlias(AdminController::class, new Alias('dgtx.admin.controller'));
        $container->setAlias(AdminLoginController::class, new Alias('dgtx.admin.controller.login'));
        $container->setAlias(AdminTranslationController::class, new Alias('dgtx.admin.controller.translation'));
        $container->setAlias(AdminPerformanceController::class, new Alias('dgtx.admin.controller.performance'));
        $container->setAlias(AdminParameterController::class, new Alias('dgtx.admin.controller.parameter'));
        $container->setAlias(AdminMenuConfigInterface::class, new Alias(self::ALIAS_ADMIN_MENU_CONFIG));
        $container->setAlias(ViewConfigInterface::class, new Alias(self::ALIAS_VIEW_CONFIG));
        $container->setAlias(EntityConfigInterface::class, new Alias(self::ALIAS_ENTITY_CONFIG));
        $container->setAlias(FieldConfigInterface::class, new Alias(self::ALIAS_FIELD_CONFIG));

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');


    }
}
