<?php

namespace Kuborgh\LogentriesBundle\Transport;

use eZ\Bundle\EzPublishRestBundle\Features\Context\RestClient\GuzzleClient;
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
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $log;

    /**
     * @var array
     */
    protected $guzzleOptions = array();

    /**
     * Transport constructor
     *
     * @param array $params Service parameters
     */
    public function __construct(array $params)
    {
        $this->host = isset($params['log_set']) ? $params['log_set'] : '';
        $this->log = isset($params['log']) ? $params['log'] : '';
        $this->accountKey = isset($params['account_key']) ? $params['account_key'] : '';
        $this->guzzleOptions = isset($params['guzzle_options']) ? $params['guzzle_options'] : array();
    }

    /**
     * Send a custom message to a custom logentries channel via HTTP
     *
     * @param string $logSet Logset
     * @param string $log    Log to send data to
     * @param string $json   Data in JSON format
     */
    public function send($json)
    {
        // Prepare URL
        $url = sprintf('http://api.logentries.com/%s/hosts/%s/%s/?realtime=1', $this->accountKey, $this->host, $this->log);

        // Prepare guzzle options
        $defaults = array(
            'timeout'         => 3,
            'connect_timeout' => 3,
        );
        $options = array_merge($defaults, $this->guzzleOptions);

        // Apply fix fields
        $options['body'] = $json."\n";

        // Will do async request. Otherwise the connection is blocked until timeout
        $options['future'] = true;

        // Perform request
        $client = new Client();
        $res = $client->put($url, $options);
    }
}
