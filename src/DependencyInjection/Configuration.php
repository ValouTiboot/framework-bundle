<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * "digitix_framework" configuration tree.
 *
 * The YAML format is intentionally backward compatible with the historical
 * one. Two optional keys were added per admin entity ("class", "controller"),
 * plus "items_per_page" on lists. Unknown keys under a field or a filter are
 * kept as-is so that custom field/filter types can declare their own options.
 *
 * The tree is exposed through buildTree() so that the bundle can load it from
 * config/definition.php (AbstractBundle) while the class stays usable as a
 * regular ConfigurationInterface.
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('digitix_framework');
        self::buildTree($treeBuilder->getRootNode());

        return $treeBuilder;
    }

    public static function buildTree(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('entity_namespaces')
                    ->info('Namespaces searched, in order, to find the Doctrine class of an admin entity by its name.')
                    ->scalarPrototype()->end()
                    ->defaultValue(['App\\Entity\\', 'Digitix\\FrameworkBundle\\Entity\\'])
                ->end()
                ->arrayNode('controller_namespaces')
                    ->info('Namespaces searched, in order, to find "{Name}Controller" or "Admin{Name}Controller" for an admin entity.')
                    ->scalarPrototype()->end()
                    ->defaultValue(['App\\Controller\\Admin\\', 'Digitix\\FrameworkBundle\\Controller\\Admin\\'])
                ->end()
                ->append(self::menuNode())
                ->append(self::adminEntitiesNode())
                ->append(self::frontEntitiesNode())
            ->end()
        ;
    }

    private static function menuNode(): NodeDefinition
    {
        $node = (new TreeBuilder('admin_menu'))->getRootNode();

        $node
            ->info('Admin menu. Keys are entity names (camel case) or arbitrary names for entries with a sub menu.')
            ->useAttributeAsKey('entity_name')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('title')->defaultValue('Entity title')->end()
                    ->scalarNode('route')->defaultValue('dgtx_admin_entity_read')->end()
                    ->scalarNode('icon')->defaultValue('home')->end()
                    ->arrayNode('sub')
                        ->useAttributeAsKey('entity_name')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('title')->defaultValue('Entity title')->end()
                                ->scalarNode('route')->defaultValue('dgtx_admin_entity_read')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private static function adminEntitiesNode(): NodeDefinition
    {
        $node = (new TreeBuilder('admin_entities'))->getRootNode();

        $node
            ->info('Entities managed by the admin, keyed by name (camel case).')
            ->useAttributeAsKey('entity_name')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('class')
                        ->info('Doctrine entity class. Defaults to the first "<entity_namespaces>\{Name}" that exists; null means a virtual entity.')
                        ->defaultNull()
                    ->end()
                    ->scalarNode('controller')
                        ->info('Controller class (must extend the bundle AdminController). Defaults to a class found by convention, then to the generic controller.')
                        ->defaultNull()
                    ->end()
                    ->arrayNode('view')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('template')->defaultNull()->end()
                        ->end()
                    ->end()
                    ->arrayNode('list')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('template')->defaultNull()->end()
                            ->booleanNode('has_create')->defaultFalse()->end()
                            ->booleanNode('sortable')->defaultFalse()->end()
                            ->integerNode('items_per_page')->min(1)->defaultValue(30)->end()
                            ->arrayNode('toolbar')
                                ->scalarPrototype()->end()
                                ->defaultValue(['add'])
                            ->end()
                            ->arrayNode('actions')
                                ->scalarPrototype()->end()
                                ->defaultValue(['edit', 'delete'])
                            ->end()
                            ->variableNode('header_link')->defaultValue([])->end()
                            ->append(self::fieldsNode('fields'))
                            ->append(self::filtersNode())
                        ->end()
                    ->end()
                    ->append(self::formNode('Admin.Fields.Label'))
                ->end()
            ->end()
        ;

        return $node;
    }

    private static function frontEntitiesNode(): NodeDefinition
    {
        $node = (new TreeBuilder('front_entities'))->getRootNode();

        $node
            ->info('Front forms generated from configuration, keyed by name.')
            ->useAttributeAsKey('entity_name')
            ->arrayPrototype()
                ->children()
                    ->append(self::formNode('Messages'))
                ->end()
            ->end()
        ;

        return $node;
    }

    private static function formNode(string $defaultTranslationDomain): NodeDefinition
    {
        $node = (new TreeBuilder('form'))->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('template')->defaultNull()->end()
                ->booleanNode('has_return_link')->defaultTrue()->end()
                ->booleanNode('has_auto_submit_button')->defaultTrue()->end()
                ->scalarNode('translation_domain')->defaultValue($defaultTranslationDomain)->end()
                ->append(self::fieldsNode('fields'))
            ->end()
        ;

        return $node;
    }

    private static function fieldsNode(string $name): NodeDefinition
    {
        $node = (new TreeBuilder($name))->getRootNode();

        $node
            ->useAttributeAsKey('field_name')
            ->normalizeKeys(false)
            ->arrayPrototype()
                ->ignoreExtraKeys(false)
                ->children()
                    ->scalarNode('label')->end()
                    ->scalarNode('name')->info('Entity property (defaults to the key).')->end()
                    ->scalarNode('type')->defaultValue('text')->end()
                    ->booleanNode('required')->end()
                    ->booleanNode('disabled')->end()
                    ->scalarNode('help')->end()
                    ->booleanNode('sort')->end()
                    ->enumNode('default_sort')->values(['asc', 'desc', 'ASC', 'DESC'])->end()
                    ->scalarNode('column')->info('Property displayed for "entity" list columns.')->end()
                    ->scalarNode('callback')->info('Static callable returning choices, e.g. "App\\Foo::choices".')->end()
                    ->scalarNode('collectionType')->end()
                    ->integerNode('rows')->end()
                    ->scalarNode('class')->info('CSS class(es) added to the widget.')->end()
                    ->variableNode('attr')->end()
                    ->variableNode('choice')->end()
                    ->arrayNode('collection')
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('label')->end()
                            ->scalarNode('value')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private static function filtersNode(): NodeDefinition
    {
        $node = (new TreeBuilder('filters'))->getRootNode();

        $node
            ->useAttributeAsKey('field_name')
            ->normalizeKeys(false)
            ->arrayPrototype()
                ->ignoreExtraKeys(false)
                ->children()
                    ->scalarNode('label')->end()
                    ->scalarNode('type')->defaultValue('text')->end()
                    ->scalarNode('alias')->info('DQL alias: "a" (entity) or "t" (translation). Guessed when omitted.')->end()
                    ->scalarNode('callback')->end()
                    ->variableNode('choice')->end()
                    ->arrayNode('collection')
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('label')->end()
                            ->scalarNode('value')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
