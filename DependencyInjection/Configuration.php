<?php

namespace Kuborgh\LogentriesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kuborgh_logentries');

        $rootNode
            ->children()
                ->arrayNode('monolog')
                    ->useAttributeAsKey('name')
                        ->prototype('array')->children()
                            ->scalarNode('transport')->end()
                            ->scalarNode('account_key')->end()
                            ->scalarNode('log_set')->end()
                            ->scalarNode('log')->defaultValue('symfony')->end()
                            ->scalarNode('level')->defaultValue('error')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
