<?php

namespace Digitix\FrameworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('digitix_framework');

        $treeBuilder->getRootNode()
            ->beforeNormalization()
                ->ifTrue(function ($v) { return !\array_key_exists('admin_menu', $v); })
                ->then(function ($v): void {
                    throw new InvalidConfigurationException("The child node \"admin_menu\" must be configured as root node.");
                })
                ->ifTrue(function ($v) { return !\array_key_exists('admin_entities', $v); })
                ->then(function ($v): void {
                    throw new InvalidConfigurationException("The child node \"admin_entities\" must be configured as root node.");
                })
            ->end()
            ->children()
                ->arrayNode('admin_menu')
                    ->info('Admin Menu configuration')
                        ->useAttributeAsKey('entity_name')
                        ->arrayPrototype()
                            ->validate()
                                ->ifTrue(function($v) {return \count($v) > 3; })
                                ->then(function($v): void {
                                    throw new InvalidConfigurationException("Config \"$v[title]\" must have 4 keys maximum [title,route,icon,sub].");
                                })
                                ->ifTrue(function($v) { return \in_array(array_keys($v), ['title','route','icon','sub']); })
                                ->then(function($v): void {
                                    throw new InvalidConfigurationException("Config \"$v[title]\" parameters must be in [title,route,icon,sub].");
                                })
                            ->end()
                            ->info('Entity name or indicatif name if have sub menu (camel case)')
                            ->children()
                                ->scalarNode('title')->defaultValue('Entity title')->end()
                                ->scalarNode('route')->defaultValue('dgtx_admin_entity_view')->end()
                                ->scalarNode('icon')->defaultValue('home')->end()
                                ->arrayNode('sub')
                                    ->useAttributeAsKey('entity_name')
                                    ->arrayPrototype()
                                        ->validate()
                                            ->ifTrue(function($v) {return \count($v) != 2; })
                                            ->then(function($v): void {
                                                throw new InvalidConfigurationException("Config \"$v[title]\" must have 2 keys [title,route].");
                                            })
                                            ->ifTrue(function($v) { return \in_array(array_keys($v), ['title','route']); })
                                            ->then(function($v): void {
                                                throw new InvalidConfigurationException("Config \"$v[title]\" parameters must be [title,route].");
                                            })
                                        ->end()
                                        ->info('Entity name (camel case)')
                                        ->children()
                                            ->scalarNode('title')->defaultValue('Entity title')->end()
                                            ->scalarNode('route')->defaultValue('dgtx_admin_entity_view')->end()
                                        ->end()
                                    ->end() // sub proto entity
                                ->end() // sub
                            ->end()
                        ->end() // dashboard
                    ->end()
                ->end() // admin menu
            ->end()
        ->getRootNode()
            ->children()
                ->arrayNode('admin_entities')
                    ->info('Admin Entities configuration for generate vues')
                        ->useAttributeAsKey('entity_name')
                        ->arrayPrototype() // entity node
                            ->validate()
                                ->ifTrue(function($v) {return \in_array(array_keys($v), ['view','list','form']); })
                                ->then(function($v): void {
                                    throw new InvalidConfigurationException("Config for entity parameters must be [view,list,form].");
                                })
                            ->end()
                            ->info('parameters for entity')
                            ->children()
                                ->arrayNode('view')
                                    ->children()
                                        ->scalarNode('template')->defaultValue(null)->end()
                                    ->end()
                                ->end() // end view node
                                ->arrayNode('list')
                                    ->children()
                                        ->scalarNode('template')->defaultValue(null)->end()
                                        ->booleanNode('has_create')->end()
                                        ->booleanNode('sortable')->end()
                                        ->arrayNode('toolbar')
                                            ->defaultValue(['add'])
                                            ->prototype('scalar')->end()
                                        ->end()
                                        ->arrayNode('actions')
                                            ->defaultValue(['edit','delete'])
                                            ->prototype('scalar')->end()
                                        ->end()
                                        ->arrayNode('header_link')
                                            ->normalizeKeys(false)
                                            ->prototype('variable')->end()
                                        ->end()
                                        ->arrayNode('fields')
                                            ->useAttributeAsKey('field_name')
                                            ->arrayPrototype() // field name
                                                ->info('Define field config')
                                                ->children()
                                                    ->scalarNode('label')->end()
                                                    ->scalarNode('name')->end()
                                                    ->scalarNode('type')->end()
                                                    ->booleanNode('sort')->end()
                                                    ->scalarNode('default_sort')->end()
                                                ->end()
                                            ->end()
                                        ->end() // end fields
                                        ->arrayNode('filters')
                                            ->useAttributeAsKey('field_name')
                                            ->arrayPrototype() // field name
                                                ->info('Define filter config')
                                                ->children()
                                                    ->scalarNode('label')->end()
                                                    ->scalarNode('type')->end()
                                                    ->scalarNode('alias')->end()
                                                    ->arrayNode('collection')
                                                        ->children()
                                                            ->scalarNode('name')->end()
                                                        ->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end() // end filters
                                    ->end()
                                ->end() // end list node
                                ->arrayNode('form')
                                    ->children()
                                        ->booleanNode('has_return_link')->end()
                                        ->scalarNode('template')->defaultValue(null)->end()
                                        ->arrayNode('fields')
                                            ->useAttributeAsKey('field_name')
                                            ->arrayPrototype() // field name
                                                ->info('Define field config')
                                                ->children()
                                                    ->scalarNode('label')->end()
                                                    ->scalarNode('name')->end()
                                                    ->scalarNode('type')->end()
                                                    ->booleanNode('required')->end()
                                                    ->scalarNode('help')->end()
                                                    ->scalarNode('callback')->end()
                                                    ->scalarNode('collectionType')->end()
                                                    ->integerNode('rows')->end()
                                                    ->scalarNode('class')->end() // to change for attr
                                                    ->arrayNode('attr')
                                                        ->normalizeKeys(false)
                                                        ->prototype('variable')->end()
                                                    ->end()
                                                    ->arrayNode('collection')
                                                        ->children()
                                                            ->scalarNode('name')->end()
                                                            ->scalarNode('value')->end()
                                                        ->end()
                                                    ->end()
                                                    ->arrayNode('choice')
                                                        ->normalizeKeys(false)
                                                        ->prototype('variable')->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end() // end fields
                                    ->end()
                                ->end() // end list node
                            ->end()
                        ->end()
                ->end() // end admin_entities
            ->end()
            ->children()
                ->arrayNode('front_entities')
                    ->info('Front Entities configuration for generate form')
                        ->useAttributeAsKey('entity_name')
                        ->arrayPrototype() // entity node
                            ->validate()
                                ->ifTrue(function($v) {return \in_array(array_keys($v), ['view','list','form']); })
                                ->then(function($v): void {
                                    throw new InvalidConfigurationException("Config for entity parameters must be [view,list,form].");
                                })
                            ->end()
                            ->info('parameters for entity')
                            ->children()
                                ->arrayNode('form')
                                    ->children()
                                        ->booleanNode('has_return_link')->end()
                                        ->booleanNode('has_auto_submit_button')->end()
                                        ->scalarNode('template')->defaultValue(null)->end()
                                        ->scalarNode('translation_domain')->end()
                                        ->arrayNode('fields')
                                            ->useAttributeAsKey('field_name')
                                            ->arrayPrototype() // field name
                                                ->info('Define field config')
                                                ->children()
                                                    ->scalarNode('label')->end()
                                                    ->scalarNode('name')->end()
                                                    ->scalarNode('type')->end()
                                                    ->booleanNode('required')->end()
                                                    ->scalarNode('help')->end()
                                                    ->scalarNode('callback')->end()
                                                    ->scalarNode('collectionType')->end()
                                                    ->integerNode('rows')->end()
                                                    ->scalarNode('class')->end() // to change for attr
                                                    ->arrayNode('attr')
                                                        ->normalizeKeys(false)
                                                        ->prototype('variable')->end()
                                                    ->end()
                                                    ->arrayNode('collection')
                                                        ->children()
                                                            ->scalarNode('name')->end()
                                                            ->scalarNode('value')->end()
                                                        ->end()
                                                    ->end()
                                                    ->arrayNode('choice')
                                                        ->normalizeKeys(false)
                                                        ->prototype('variable')->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end() // end fields
                                    ->end()
                                ->end() // end list node
                            ->end()
                        ->end()
                ->end() // end front_entities
            ->end()
        ;

        return $treeBuilder;
    }
}
