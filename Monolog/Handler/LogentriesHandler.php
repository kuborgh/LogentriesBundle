<?php
namespace Kuborgh\LogentriesBundle\Monolog\Handler;

use JMS\Serializer\SerializerBuilder;
use Kuborgh\LogentriesBundle\Transport\TransportInterface;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Monolog handler for logentries
 */
class LogentriesHandler extends AbstractProcessingHandler
{
    /**
     * @var TransportInterface
     */
    protected $transport;

    /**
     * @var bool
     */
    protected $enabled = true;

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
     * Set enabled
     *
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     */
    protected function write(array $record)
    {
        // Skip when disabled
        if (!$this->enabled) {
            return;
        }

        // Remove formatted message
        unset($record['formatted']);

        // Use JMS Serializer. This will also allow \DateTime
        $serializer = SerializerBuilder::create()->build();
        $json = $serializer->serialize($record, 'json');

        $this->transport->send($json);
    }
}
