<?php

use Kuborgh\LogentriesBundle\Logger\Logger;

/**
 * Test logger behaviour
 */
class LoggerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Mock transport
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $transport;

    /**
     * @var Logger
     */
    protected $logger;

    public function testEnabledLogger()
    {
        $data = array('lorem' => 'ipsum');
        $this->transport->expects($this->once())->method('send')->with($this->equalTo('{"lorem":"ipsum","uri":""}'));
        $this->logger->log($data);
    }

    public function testDisabledLogger()
    {
        $this->setUp(false);
        $data = array('lorem' => 'ipsum');
        $this->transport->expects($this->never())->method('send');
        $this->logger->log($data);
    }

    protected function setUp($enabled = true)
    {
        $this->transport = $this->getMockBuilder('Kuborgh\LogentriesBundle\Transport\TransportInterface')->getMock();

        $container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
        $request = new \Symfony\Component\HttpFoundation\Request();
        $container->set('request',$request);

        $this->logger = new Logger($container, get_class($this->transport), array(), $enabled);

        // Inject transport
        $reflClass = new ReflectionClass(get_class($this->logger));
        $refl = $reflClass->getProperty('transport');
        $refl->setAccessible(true);
        $refl->setValue($this->logger, $this->transport);
    }
}
