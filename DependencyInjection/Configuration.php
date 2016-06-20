<?php

namespace Kuborgh\LogentriesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\ScalarNode;

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
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kuborgh_logentries');

        $rootNode
            ->children()
                ->scalarNode('enabled')
                    ->info('Completely disable logentries (in local environment for example)')
                    ->defaultValue(true)
                ->end()
                ->arrayNode('monolog')
                    ->info('Monolog handlers')
                    ->useAttributeAsKey('name')
                        ->prototype('array')->children()
                            ->append($this->addLogSetNode())
                            ->append($this->addLogNode('monolog'))
                            ->scalarNode('level')->defaultValue('error')->info('Monolog log level')->end()
                            ->append($this->addTransportNode())
                            ->append($this->addAccountKeyNode())
                            ->append($this->addGuzzleOptionsNode())
                            ->append($this->addPortNode())
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('logger')
                    ->info('Custom loggers')
                    ->useAttributeAsKey('name')
                        ->prototype('array')->children()
                            ->append($this->addLogSetNode())
                            ->append($this->addLogNode('symfony'))
                            ->append($this->addTransportNode())
                            ->append($this->addAccountKeyNode())
                            ->append($this->addGuzzleOptionsNode())
                            ->append($this->addPortNode())
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * @return NodeDefinition
     */
    protected function addTransportNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('transport','scalar');
        $node
            ->info('Transport of the message. Can be one of "http_guzzle".')
            ->defaultValue('http_guzzle')
            ->validate()
            ->ifNotInArray(array('http_guzzle','udp'))
                ->thenInvalid('Invalid transport "%s"')
            ->end()
        ;

        return $node;
    }

    /**
     * @return NodeDefinition
     */
    protected function addLogSetNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('log_set', 'scalar');
        $node->info('LogSet aka Host in logentries backend')->end();

        return $node;
    }

    /**
     * @array $default
     *
     * @return NodeDefinition
     */
    protected function addLogNode($default)
    {
        $builder = new TreeBuilder();
        $node = $builder->root('log', 'scalar');
        $node->defaultValue($default)->info('Log inside the LogSet. Must already exist in logentries backend')->end();

        return $node;
    }

    /**
     * @return NodeDefinition
     */
    protected function addAccountKeyNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('account_key','scalar');
        $node
            ->info('Account key for guzzle_http transport')
            ->end()
        ;

        return $node;
    }

    /**
     * @return NodeDefinition
     */
    protected function addGuzzleOptionsNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('guzzle_options');
        $node
            ->info('Additional Guzzle options for guzzle_http transport')
            ->prototype('scalar')->end()
            ->end()
        ;

        return $node;
    }

    /**
     * @return NodeDefinition
     */
    protected function addPortNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('port','integer');
        $node
            ->info('Port for UDP/TCP transport')
            ->end()
        ;

        return $node;
    }
}
