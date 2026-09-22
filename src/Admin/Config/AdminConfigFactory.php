<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Config;

/**
 * Turns the processed (and resolved, see AdminConfigResolver) configuration
 * array into the immutable AdminConfig object graph.
 *
 * Used as a service factory: the container calls fromArray() once with the
 * array baked in at compile time.
 */
final class AdminConfigFactory
{
    /**
     * @param array<string, mixed> $config processed "digitix_framework" configuration,
     *                                     with "class" and "controller" already resolved
     */
    public static function fromArray(array $config): AdminConfig
    {
        $entities = [];
        foreach ($config['admin_entities'] ?? [] as $name => $definition) {
            $entities[AdminConfig::key($name)] = new EntityConfig(
                $name,
                $definition['class'] ?? null,
                $definition['controller'],
                self::createListConfig($definition['list'] ?? []),
                self::createFormConfig($definition['form'] ?? []),
                new ViewConfig($definition['view']['template'] ?? null),
            );
        }

        $frontForms = [];
        foreach ($config['front_entities'] ?? [] as $name => $definition) {
            $frontForms[AdminConfig::key($name)] = self::createFormConfig($definition['form'] ?? [], 'Messages');
        }

        return new AdminConfig($config['admin_menu'] ?? [], $entities, $frontForms);
    }

    /**
     * @param array<string, mixed> $list
     */
    private static function createListConfig(array $list): ListConfig
    {
        $fields = [];
        foreach ($list['fields'] ?? [] as $name => $definition) {
            $fields[$name] = FieldConfig::fromArray((string) $name, $definition ?? []);
        }

        $filters = [];
        foreach ($list['filters'] ?? [] as $name => $definition) {
            $filters[$name] = FilterConfig::fromArray((string) $name, $definition ?? []);
        }

        return new ListConfig(
            $list['template'] ?? null,
            (bool) ($list['has_create'] ?? false),
            (bool) ($list['sortable'] ?? false),
            (int) ($list['items_per_page'] ?? 30),
            array_values($list['toolbar'] ?? []),
            array_values($list['actions'] ?? []),
            array_values($list['header_link'] ?? []),
            $fields,
            $filters,
        );
    }

    /**
     * @param array<string, mixed> $form
     */
    private static function createFormConfig(array $form, string $defaultDomain = 'Admin.Fields.Label'): FormConfig
    {
        $fields = [];
        foreach ($form['fields'] ?? [] as $name => $definition) {
            $fields[$name] = FieldConfig::fromArray((string) $name, $definition ?? []);
        }

        return new FormConfig(
            $form['template'] ?? null,
            (bool) ($form['has_return_link'] ?? true),
            (bool) ($form['has_auto_submit_button'] ?? true),
            (string) ($form['translation_domain'] ?? $defaultDomain),
            $fields,
        );
    }
}
