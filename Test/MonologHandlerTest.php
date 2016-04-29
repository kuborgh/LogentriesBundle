<?php

use Kuborgh\LogentriesBundle\Logger\Logger;
use Kuborgh\LogentriesBundle\Monolog\Handler\LogentriesHandler;

/**
 * Test monolog handler behaviour
 */
class MonologHanlderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Mock transport
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $transport;

    /**
     * @var LogentriesHandler
     */
    protected $handler;

    public function testEnabledLogger()
    {
        $data = array(
            'level'   => \Monolog\Logger::ERROR,
            'message' => 'lorem ipsum',
            'extra' => array(),
            'context' => array(),
        );
        $this->transport->expects($this->once())->method('send')->with($this->equalTo('{"level":400,"message":"lorem ipsum","extra":[],"context":[]}'));
        $this->handler->handle($data);
    }

        public function testDisabledLogger()
        {
            $this->handler->setEnabled(false);
            $data = array(
                'level'   => \Monolog\Logger::ERROR,
                'message' => 'lorem ipsum',
                'extra' => array(),
                'context' => array(),
            );
            $this->transport->expects($this->never())->method('send');
            $this->handler->handle($data);
        }

    protected function setUp()
    {
        $this->handler = new LogentriesHandler();
        $this->transport = $this->getMockBuilder('Kuborgh\LogentriesBundle\Transport\TransportInterface')->getMock();

        // Just for the coverage ;-)
        $this->handler->setTransport(get_class($this->transport), array());

        // Inject transport
        $reflClass = new ReflectionClass(get_class($this->handler));
        $refl = $reflClass->getProperty('transport');
        $refl->setAccessible(true);
        $refl->setValue($this->handler, $this->transport);
    }
}
