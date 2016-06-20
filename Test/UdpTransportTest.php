<?php

use Kuborgh\LogentriesBundle\Logger\Logger;
use Kuborgh\LogentriesBundle\Monolog\Handler\LogentriesHandler;
use Kuborgh\LogentriesBundle\Transport\UdpTransport;

/**
 * Test udp transport
 */
class UdpTransportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Mock transport
     * @var UdpTransport
     */
    protected $transport;

    public function testLogging()
    {
        $data = '{"lorem":"ipsum"}';

        $this->transport->send($data);
    }

    /**
     * @expectedException \Exception
     */
    public function testNoPort()
    {
        $transport = new UdpTransport(array());
        $data = '{"lorem":"ipsum"}';
        $transport->send($data);
    }

    protected function setUp()
    {
        $this->transport = new UdpTransport(array('port' => 12345));
    }
}
