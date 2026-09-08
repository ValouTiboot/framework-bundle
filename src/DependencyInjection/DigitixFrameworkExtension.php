<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\DependencyInjection;

use Digitix\FrameworkBundle\Admin\Config\AdminConfig;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class DigitixFrameworkExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $config = AdminConfigResolver::resolve($config);

        $container->setParameter('digitix_framework.entity_namespaces', $config['entity_namespaces']);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        // The whole configuration is baked into the container: AdminConfig is
        // built once by AdminConfigFactory::fromArray() and shared everywhere.
        $container->getDefinition(AdminConfig::class)->setArgument(0, $config);
    }

    public function getAlias(): string
    {
        return 'digitix_framework';
    }
}
