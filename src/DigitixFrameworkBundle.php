<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle;

use Digitix\FrameworkBundle\Admin\Config\AdminConfig;
use Digitix\FrameworkBundle\DependencyInjection\AdminConfigResolver;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Modern bundle layout: config/, templates/, translations/, public/ live at
 * the bundle root, PHP code under src/.
 */
final class DigitixFrameworkBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    /**
     * @param array<string, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $config = AdminConfigResolver::resolve($config);

        $container->parameters()->set('digitix_framework.entity_namespaces', $config['entity_namespaces']);
        $container->import('../config/services.php');

        // The whole configuration is baked into the container: AdminConfig is
        // built once by AdminConfigFactory::fromArray() and shared everywhere.
        $builder->getDefinition(AdminConfig::class)->setArgument(0, $config);
    }

    /** Bundle root directory. */
    public static function getPathDir(): string
    {
        return \dirname(__DIR__);
    }
}
