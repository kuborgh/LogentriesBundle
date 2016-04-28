<?php

namespace Kuborgh\LogentriesBundle\Transport;

use GuzzleHttp\Client;

/**
 * Send logentries via Guzzle HTTP
 */
interface TransportInterface
{
    /**
     * Send a custom message to a custom logentries channel
     *
     * @param string $logSet  Logset
     * @param string $log     Log to send data to
     * @param array  $payload Content
     */
    public function send($logSet, $log, $payload);
}
