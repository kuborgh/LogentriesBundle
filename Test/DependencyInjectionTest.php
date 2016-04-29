<?php

use Kuborgh\LogentriesBundle\Transport\HttpGuzzleTransport;
use Kuborgh\LogentriesBundle\DependencyInjection\KuborghLogentriesExtension;
use Kuborgh\LogentriesBundle\Logger\Logger;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test symfony configuration
 */
class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var KuborghLogentriesExtension
     */
    protected $extension;

    /**
     * Test minimal config of monolog handler
     */
    public function testMinimalMonologConfig()
    {
        $yml = <<<YML
kuborgh_logentries:
    monolog:
        my_monolog_handler:
            log_set: my_log_set
            log: my_log
YML;
        $config = \Symfony\Component\Yaml\Yaml::parse($yml);
        $this->extension->load($config, $this->container);

        // Fetch service
        $handler = $this->container->get('kuborgh_logentries.handler.my_monolog_handler');

        // Check enabled flag
        $logger = $this->getHiddenPropertyValue($handler, 'logger');
        $this->assertTrue($logger instanceof Logger);
        $this->assertTrue($this->getHiddenPropertyValue($logger, 'enabled'));

        // Check loglevel
        $this->assertEquals(Monolog\Logger::ERROR, $this->getHiddenPropertyValue($handler, 'level'));

        // Get transport
        $transport = $this->getHiddenPropertyValue($logger, 'transport');
        $this->assertTrue($transport instanceof HttpGuzzleTransport);

        // Check transport options
        $this->assertEquals('my_log_set', $this->getHiddenPropertyValue($transport, 'host'));
        $this->assertEquals('my_log', $this->getHiddenPropertyValue($transport, 'log'));
    }

    /**
     * Test minimal config of monolog handler
     */
    public function testMaximumMonologConfig()
    {
        $yml = <<<YML
kuborgh_logentries:
    monolog:
        my_monolog_handler:
            log_set: my_log_set
            log: my_log
            level: NOTICe
            transport: http_guzzle
            account_key: my_key
            guzzle_options:
                option1: value1
                option2: value2
YML;
        $config = \Symfony\Component\Yaml\Yaml::parse($yml);
        $this->extension->load($config, $this->container);

        // Fetch service
        $handler = $this->container->get('kuborgh_logentries.handler.my_monolog_handler');

        // Check loglevel
        $this->assertEquals(Monolog\Logger::NOTICE, $this->getHiddenPropertyValue($handler, 'level'));

        // Get Logger
        $logger = $this->getHiddenPropertyValue($handler, 'logger');
        $this->assertTrue($logger instanceof Logger);

        //Check enabled flag
        $this->assertTrue($this->getHiddenPropertyValue($logger, 'enabled'));

        // Get transport
        $transport = $this->getHiddenPropertyValue($logger, 'transport');
        $this->assertTrue($transport instanceof HttpGuzzleTransport);

        // Check transport options
        $this->assertEquals('my_log_set', $this->getHiddenPropertyValue($transport, 'host'));
        $this->assertEquals('my_log', $this->getHiddenPropertyValue($transport, 'log'));
        $this->assertEquals('my_key', $this->getHiddenPropertyValue($transport, 'accountKey'));
        $expectedGuzzleOptions = array(
            'option1' => 'value1',
            'option2' => 'value2',
        );
        $this->assertEquals($expectedGuzzleOptions, $this->getHiddenPropertyValue($transport, 'guzzleOptions'));
    }

    public function testMinimalLoggerConfig()
    {
        $yml = <<<YML
kuborgh_logentries:
    logger:
        my_logger:
            log_set: my_log_set
            log: my_log
YML;
        $config = \Symfony\Component\Yaml\Yaml::parse($yml);
        $this->extension->load($config, $this->container);

        // Fetch service
        $logger = $this->container->get('kuborgh_logentries.my_logger');

        // Check enabled flag
        $this->assertTrue($this->getHiddenPropertyValue($logger, 'enabled'));

        // Get transport
        $transport = $this->getHiddenPropertyValue($logger, 'transport');
        $this->assertTrue($transport instanceof HttpGuzzleTransport);

        // Check transport options
        $this->assertEquals('my_log_set', $this->getHiddenPropertyValue($transport, 'host'));
        $this->assertEquals('my_log', $this->getHiddenPropertyValue($transport, 'log'));
    }

    public function testEnabledConfig()
    {
        $yml = <<<YML
kuborgh_logentries:
    enabled: false
    monolog:
        my_monolog_handler:
            log_set: my_log_set
            log: my_log
    logger:
        my_logger:
            log_set: my_log_set
            log: my_log
YML;
        $config = \Symfony\Component\Yaml\Yaml::parse($yml);
        $this->extension->load($config, $this->container);

        // Fetch service
        $logger = $this->container->get('kuborgh_logentries.my_logger');
        $handler = $this->container->get('kuborgh_logentries.handler.my_monolog_handler');

        // Check enabled flag
        $this->assertFalse($this->getHiddenPropertyValue($logger, 'enabled'));
        $handlerLogger = $this->getHiddenPropertyValue($handler, 'logger');
        $this->assertFalse($this->getHiddenPropertyValue($handlerLogger, 'enabled'));
    }

    /**
     * Set Up
     */
    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new KuborghLogentriesExtension();
    }

    /**
     * @param Object $object
     * @param string $property
     *
     * @return mixed
     */
    protected function getHiddenPropertyValue($object, $property)
    {
        $reflClass = new ReflectionClass(get_class($object));
        $refl = $reflClass->getProperty($property);
        $refl->setAccessible(true);

        return $refl->getValue($object);
    }
}
