<?php

namespace Kuborgh\LogentriesBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KuborghLogentriesExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('parameters.yml');

        // Register monolog handler
        foreach ($config['monolog'] as $handlerName => $handlerConfig) {
            // Configure transport
            $transportClass = $container->getParameter(sprintf('kuborgh_logentries.transport.%s.class', $handlerConfig['transport']));

            // Loglevel
            $level = is_int($handlerConfig['level']) ? $handlerConfig['level'] : constant('Monolog\Logger::'.strtoupper($handlerConfig['level']));

            // Build Service
            $handlerClass = $container->getParameter('kuborgh_logentries.handler.class');
            $serviceDef = new Definition($handlerClass);
            $serviceDef->addArgument($level);
            $serviceDef->addMethodCall('setTransport', array($transportClass, $handlerConfig));
            $serviceDef->addMethodCall('setEnabled', array($config['enabled']));

            $containerRef = new Reference('service_container');
            $serviceDef->addMethodCall('setContainer', array($containerRef));
            $serviceName = sprintf('kuborgh_logentries.handler.%s', $handlerName);
            $container->setDefinition($serviceName, $serviceDef);
        }

        // Register simple logger
        foreach ($config['logger'] as $handlerName => $handlerConfig) {
            // Configure transport
            $transportClass = $container->getParameter(sprintf('kuborgh_logentries.transport.%s.class', $handlerConfig['transport']));

            // Build Service
            $handlerClass = $container->getParameter('kuborgh_logentries.logger.class');
            $serviceDef = new Definition($handlerClass);
            $serviceDef->addArgument($config['enabled']);
            $serviceDef->addMethodCall('setTransport', array($transportClass, $handlerConfig));
            $serviceName = sprintf('kuborgh_logentries.%s', $handlerName);
            $container->setDefinition($serviceName, $serviceDef);
        }
    }
}
