<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\DependencyInjection\Compiler;

use Digitix\FrameworkBundle\Translation\RuntimeTranslator;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Swaps the framework translator for RuntimeTranslator so that the catalogue
 * files generated from the database are loaded without recompiling the
 * container. Left alone when another class is already installed there.
 */
final class RuntimeTranslatorPass implements CompilerPassInterface
{
    public const OUTPUT_DIR_PARAMETER = 'digitix_framework.translation.output_dir';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('translator.default') || !$container->hasParameter(self::OUTPUT_DIR_PARAMETER)) {
            return;
        }

        $definition = $container->getDefinition('translator.default');

        if (Translator::class !== $definition->getClass()) {
            return;
        }

        $definition
            ->setClass(RuntimeTranslator::class)
            ->addMethodCall('setRuntimeDirectory', ['%'.self::OUTPUT_DIR_PARAMETER.'%']);
    }
}
