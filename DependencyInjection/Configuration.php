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
                ->arrayNode('handler')
                    ->useAttributeAsKey('name')
                        ->prototype('array')->children()
                            ->scalarNode('transport')
                            ->scalarNode('account_key')
                            ->scalarNode('log_set')
                            ->scalarNode('log')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
