<?php

namespace Kuborgh\LogentriesBundle\Transport;

use GuzzleHttp\Client;

/**
 * Send logentries via Guzzle HTTP
 */
interface TransportInterface
{
    /**
     * Transport constructor
     *
     * @param array $params Service parameters
     */
    public function __construct(array $params);

    /**
     * Send a custom message to a custom logentries channel
     *
     * @param string $data Data in JSON format
     */
    public function send($json);
}
