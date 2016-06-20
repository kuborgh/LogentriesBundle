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
     * Transport constructor
     *
     * @param array $params Service parameters
     */
    public function __construct(array $params)
    {
        $this->port = isset($params['port']) ? $params['port'] : null;
    }

    /**
     * Send message
     *
     * @param string $json Data in JSON format
     * @throws \Exception
     */
    public function send($json)
    {
        // Check port
        if (!$this->port) {
            throw new \Exception('Logentries - UDP port not set');
        }

        // Open socket
        $socket = fsockopen('udp://data.logentries.com', $this->port, $errNo, $errStr);
        if (!$socket) {
            $errMsg = sprintf('Logentires - UDP socket error: (%d) %s', $errNo, $errStr);
            throw new \Exception($errMsg);
        }

        // Send data
        fwrite($socket, $json);
        fclose($socket);
    }
}
