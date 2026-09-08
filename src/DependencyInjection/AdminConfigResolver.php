<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\DependencyInjection;

use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Compile-time resolution of the "class" and "controller" of every admin
 * entity, by convention when not explicitly configured:
 *
 *   class:      <entity_namespaces>\{Name}
 *   controller: <controller_namespaces>\{Name}Controller
 *               <controller_namespaces>\Admin{Name}Controller
 *               falls back to the generic AdminController
 *
 * Namespaces are tried in order (App first, then the bundle), so a project can
 * override any bundle entity or controller just by creating the class.
 */
final class AdminConfigResolver
{
    /**
     * @param array<string, mixed> $config processed configuration
     *
     * @return array<string, mixed> same configuration with "class" and "controller" filled in
     */
    public static function resolve(array $config): array
    {
        $entityNamespaces = $config['entity_namespaces'];
        $controllerNamespaces = $config['controller_namespaces'];

        foreach ($config['admin_entities'] as $name => &$definition) {
            $definition['class'] = self::resolveClass($name, $definition['class'] ?? null, $entityNamespaces);
            $definition['controller'] = self::resolveController($name, $definition['controller'] ?? null, $controllerNamespaces);
        }
        unset($definition);

        return $config;
    }

    /**
     * @param string[] $namespaces
     */
    private static function resolveClass(string $name, ?string $explicit, array $namespaces): ?string
    {
        if (null !== $explicit) {
            if (!class_exists($explicit)) {
                throw new InvalidConfigurationException(sprintf('digitix_framework.admin_entities.%s.class: class "%s" does not exist.', $name, $explicit));
            }

            return $explicit;
        }

        foreach ($namespaces as $namespace) {
            $candidate = rtrim($namespace, '\\').'\\'.ucfirst($name);

            if (class_exists($candidate)) {
                return $candidate;
            }
        }

        return null; // virtual entity
    }

    /**
     * @param string[] $namespaces
     */
    private static function resolveController(string $name, ?string $explicit, array $namespaces): string
    {
        if (null !== $explicit) {
            if (!class_exists($explicit)) {
                throw new InvalidConfigurationException(sprintf('digitix_framework.admin_entities.%s.controller: class "%s" does not exist.', $name, $explicit));
            }

            return self::assertAdminController($name, $explicit);
        }

        $shortName = ucfirst($name);

        foreach ($namespaces as $namespace) {
            $namespace = rtrim($namespace, '\\').'\\';

            foreach ([$namespace.$shortName.'Controller', $namespace.'Admin'.$shortName.'Controller'] as $candidate) {
                if (class_exists($candidate)) {
                    return self::assertAdminController($name, $candidate);
                }
            }
        }

        return AdminController::class;
    }

    private static function assertAdminController(string $name, string $class): string
    {
        if (!is_a($class, AdminController::class, true)) {
            throw new InvalidConfigurationException(sprintf(
                'digitix_framework.admin_entities.%s.controller: "%s" must extend %s.',
                $name,
                $class,
                AdminController::class
            ));
        }

        return $class;
    }
}
