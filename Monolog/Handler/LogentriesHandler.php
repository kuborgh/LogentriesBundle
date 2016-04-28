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
     * @param  array $record
     *
     * @return void
     */
    protected function write(array $record)
    {
        // Remove formatted message
        unset($record['formatted']);
        $record['message'] = str_replace('\"', '"', $record['message']);

        // Use JMS Serializer. This will also allow \DateTime
        $serializer = SerializerBuilder::create()->build();
        $json = $serializer->serialize($record, 'json');

        $this->transport->send($json);
    }
}
