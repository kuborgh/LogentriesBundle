<?php
namespace Kuborgh\LogentriesBundle\Logger;

use JMS\Serializer\SerializerBuilder;
use Kuborgh\LogentriesBundle\Transport\TransportInterface;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Simple logger
 */
class Logger
{
    /**
     * @var TransportInterface
     */
    protected $transport;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * Logger constructor.
     *
     * @param bool $enabled
     */
    public function __construct($enabled = true)
    {
        $this->enabled = $enabled;
    }

    /**
     * Set a transport
     *
     * @param string $transportClass
     * @param string $transportParams
     */
    public function setTransport($transportClass, $transportParams)
    {
        $this->transport = new $transportClass($transportParams);
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $data
     */
    public function log(array $data)
    {
        // Skip when disabled
        if (!$this->enabled) {
            return;
        }

        // Use JMS Serializer. This will also allow \DateTime
        $serializer = SerializerBuilder::create()->build();
        $json = $serializer->serialize($data, 'json');

        $this->transport->send($json);
    }
}
