<?php

namespace Digitix\FrameworkBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Digitix\FrameworkBundle\Config\AdminViewConfig;
use Digitix\FrameworkBundle\Route\RouteLoader;
use Symfony\Component\DependencyInjection\Alias;
use Digitix\FrameworkBundle\Config\AdminFormConfig;
use Digitix\FrameworkBundle\Config\AdminListConfig;
use Digitix\FrameworkBundle\Config\AdminMenuConfig;
use Symfony\Component\DependencyInjection\Reference;
use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\Config\AdminViewConfigInterface;
use Digitix\FrameworkBundle\Repository\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Digitix\FrameworkBundle\Config\AdminFormConfigInterface;
use Digitix\FrameworkBundle\Config\AdminListConfigInterface;
use Digitix\FrameworkBundle\Config\AdminMenuConfigInterface;
use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Digitix\FrameworkBundle\Controller\Admin\AdminLoginController;
use Digitix\FrameworkBundle\Controller\Admin\AdminParameterController;
use Digitix\FrameworkBundle\Controller\Admin\AdminPerformanceController;
use Digitix\FrameworkBundle\Controller\Admin\AdminTranslationController;

class DigitixFrameworkExtension extends Extension
{
    const ALIAS_ADMIN_MENU_CONFIG = 'dgtx.admin.menu.config';
    const ALIAS_ADMIN_VIEW_CONFIG = 'dgtx.admin.view.config';
    const ALIAS_ADMIN_LIST_CONFIG = 'dgtx.admin.list.config';
    const ALIAS_ADMIN_FORM_CONFIG = 'dgtx.admin.form.config';

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
        $container->registerForAutoconfiguration(AdminViewConfigInterface::class)->addTag(self::ALIAS_ADMIN_VIEW_CONFIG);
        $container->registerForAutoconfiguration(AdminFormConfigInterface::class)->addTag(self::ALIAS_ADMIN_FORM_CONFIG);
        $container->registerForAutoconfiguration(AdminListConfigInterface::class)->addTag(self::ALIAS_ADMIN_LIST_CONFIG);
        $container->registerForAutoconfiguration(EntityRepository::class)->addTag('doctrine.repository_service');

        $container->register('digitix.route_loader', RouteLoader::class)
            ->setPublic(false)
            ->addTag('routing.loader')
        ;

        $container->register(self::ALIAS_ADMIN_MENU_CONFIG, AdminMenuConfig::class)
            ->setPublic(false)
            ->setArguments(['$params' => $config])
        ;

        $container->register(self::ALIAS_ADMIN_VIEW_CONFIG, AdminViewConfig::class)
            ->setPublic(true)
            ->setArguments([
                '$params' => $config,
                '$context' => new Reference(ContextProvider::class)
            ]
        );

        $container->register(self::ALIAS_ADMIN_FORM_CONFIG, AdminFormConfig::class)
            ->setPublic(true)
            ->setArguments([
                '$params' => $config,
                '$context' => new Reference(ContextProvider::class)
            ]
        );

        $container->register(self::ALIAS_ADMIN_LIST_CONFIG, AdminListConfig::class)
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
        $container->setAlias(AdminViewConfigInterface::class, new Alias(self::ALIAS_ADMIN_VIEW_CONFIG));
        $container->setAlias(AdminListConfigInterface::class, new Alias(self::ALIAS_ADMIN_LIST_CONFIG));
        $container->setAlias(AdminFormConfigInterface::class, new Alias(self::ALIAS_ADMIN_FORM_CONFIG));

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');


    }
}
