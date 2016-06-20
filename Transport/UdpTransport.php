<?php

namespace Kuborgh\LogentriesBundle\Transport;

/**
 * Send logentries via UDP
 */
class UdpTransport implements TransportInterface
{
    /**
     * Port number
     *
     * @var int
     */
    protected $port;

    /**
     * Host. Can be configured to make use of a UDP proxy
     *
     * @var string
     */
    protected $host = 'data.logentries.com';

    /**
     * Transport constructor
     *
     * @param array $params Service parameters
     */
    public function __construct(array $params)
    {
        if (isset($params['port'])) {
            $this->port = $params['port'];
        }
        if (isset($params['host'])) {
            $this->host = $params['host'];
        }
    }

    /**
     * Send message
     *
     * @param string $json Data in JSON format
     *
     * @throws \Exception
     */
    public function send($json)
    {
        // Check port
        if (!$this->port) {
            throw new \Exception('Logentries - UDP port not set');
        }

        // Open socket
        $socket = fsockopen('udp://'.$this->host, $this->port, $errNo, $errStr);
        if (!$socket) {
            $errMsg = sprintf('Logentires - UDP socket error: (%d) %s', $errNo, $errStr);
            throw new \Exception($errMsg);
        }

        // Send data
        fwrite($socket, $json);
        fclose($socket);
    }
}
