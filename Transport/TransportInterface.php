<?php

namespace Kuborgh\LogentriesBundle\Transport;

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
     * @param string $json Data in JSON format
     */
    public function send($json);
}
