<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle;

use Digitix\FrameworkBundle\Admin\Config\AdminConfig;
use Digitix\FrameworkBundle\DependencyInjection\AdminConfigResolver;
use Digitix\FrameworkBundle\DependencyInjection\Compiler\RuntimeTranslatorPass;
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

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RuntimeTranslatorPass());
    }

    /**
     * @param array<string, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $config = AdminConfigResolver::resolve($config);

        $parameters = $container->parameters();
        $parameters->set('digitix_framework.entity_namespaces', $config['entity_namespaces']);
        // the bundle's own sources and templates are always scanned for translation keys
        $parameters->set('digitix_framework.translation.paths', array_values(array_unique(array_merge(
            [self::getPathDir().'/src', self::getPathDir().'/templates'],
            $config['translation']['paths']
        ))));
        $parameters->set(RuntimeTranslatorPass::OUTPUT_DIR_PARAMETER, $config['translation']['output_dir']);
        $parameters->set('digitix_framework.front.home_route', $config['front']['home_route']);
        $parameters->set('digitix_framework.menu.max_depth', $config['menu']['max_depth']);
        $parameters->set('digitix_framework.menu.pages', $config['menu']['pages']);

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
