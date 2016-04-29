<?php

use Kuborgh\LogentriesBundle\Logger\Logger;
use Kuborgh\LogentriesBundle\Monolog\Handler\LogentriesHandler;
use Kuborgh\LogentriesBundle\Transport\HttpGuzzleTransport;

/**
 * Test guzzle http transport
 */
class HttpGuzzleTransportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Mock transport
     * @var HttpGuzzleTransport
     */
    protected $transport;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $guzzleClient;

    public function testLogging()
    {
        $data = '{"lorem":"ipsum"}';

        $this->transport->send($data);
    }

    protected function setUp()
    {
        $this->transport = new HttpGuzzleTransport(array());
        $this->guzzleClient = $this->getMockBuilder('\GuzzleHttp\Client')->getMock();
        $this->markTestIncomplete('Guzzle Client not mockable yet :-(');
    }
}
