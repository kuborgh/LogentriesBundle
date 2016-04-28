<?php

namespace Kuborgh\LogentriesBundle\Transport;

use GuzzleHttp\Client;

/**
 * Send logentries via Guzzle HTTP
 */
class HttpGuzzleTransport implements TransportInterface
{
    /**
     * @var Client
     */
    protected $guzzleClient;

    /**
     * @var string
     */
    protected $accountKey;

    /**
     * Send a custom message to a custom logentries channel via HTTP
     *
     * @param string $logSet  Logset
     * @param string $log     Log to send data to
     * @param array  $payload Content
     */
    public function send($logSet, $log, $payload)
    {
        // @rfe use JMS Serializer. This will also allow \DateTime
        $json = json_encode($payload);

        // Prepare URL
        $key = $this->accountKey;
        $url = sprintf('http://api.logentries.com/%s/hosts/%s/%s/?realtime=1', $key, $logSet, $log);

        // Prepare request
        $opts = array(
            'body'            => $json."\n",
            'timeout'         => 3,
            'connect_timeout' => 3,
            // Will do async request. Otherwise the connection is blocked
            'future'          => true,
        );

        // Perform request
        $this->guzzleClient->put($url, $opts);
    }

    /**
     * Set guzzleClient
     *
     * @param Client $guzzleClient
     */
    public function setGuzzleClient($guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * Set accountKey
     *
     * @param string $accountKey
     */
    public function setAccountKey($accountKey)
    {
        $this->accountKey = $accountKey;
    }
}
