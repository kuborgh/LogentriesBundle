<?php

namespace Kuborgh\LogentriesBundle\Services;

use ServusTV\Bundle\AppBundle\Traits\GuzzleTrait;
use ServusTV\CommonBundle\Traits\ParameterTrait;
use ServusTV\CommonBundle\Traits\ProxyTrait;

/**
 * Service to send log entries
 */
class LogentriesService
{
    use GuzzleTrait;
    use ParameterTrait;

    /**
     * Send a custom message to a custom logentries channel via HTTP
     *
     * @param string $log     Log to send data to
     * @param array  $payload Content
     */
    public function sendHttp($log, $payload)
    {
        // @rfe use JMS Serializer. This will also allow \DateTime
        $json = json_encode($payload);

        $this->sendViaGuzzle($log, $json);
    }

    protected function sendViaGuzzle($log, $json)
    {
        // Prepare URL
        $key = $this->getParameter('logentries.account_key');
        $host = $this->getParameter('logentries.host');
        $url = sprintf('http://api.logentries.com/%s/hosts/%s/%s/?realtime=1', $key, $host, $log);

        // Prepare request
        $guzzle = $this->newGuzzleClient();
        $opts = array(
            'body'    => $json."\n",
            'timeout' => 3,
            // Will to async request. Otherwise the connection is blocked
            'future'  => true,
        );

        // Perform request
        $guzzle->put($url, $opts);
    }
}
