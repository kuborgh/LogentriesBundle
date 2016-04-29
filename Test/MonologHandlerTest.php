<?php

use Kuborgh\LogentriesBundle\Logger\Logger;
use Kuborgh\LogentriesBundle\Monolog\Handler\LogentriesHandler;

/**
 * Test monolog handler behaviour
 */
class MonologHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Mock transport
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    /**
     * @var LogentriesHandler
     */
    protected $handler;

    public function testHandler()
    {
        $data = array(
            'level'   => \Monolog\Logger::ERROR,
            'message' => 'lorem ipsum',
            'extra'   => array(),
            'context' => array(),
        );
        $this->logger->expects($this->once())->method('log')->with($this->equalTo($data));
        $this->handler->handle($data);
    }

    protected function setUp($enabled = true)
    {
        $this->logger = $this->getMockBuilder('Kuborgh\LogentriesBundle\Logger\Logger')->disableOriginalConstructor()->getMock();

        // Build handler
        $this->handler = new LogentriesHandler();
        $this->handler->setLogger($this->logger);
    }
}
