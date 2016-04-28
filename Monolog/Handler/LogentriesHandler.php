<?php
namespace Kuborgh\LogentriesBundle\Monolog\Handler;

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
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     *
     * @return void
     */
    protected function write(array $record)
    {
        $this->transport->send($log, $logSet, $record);
        // @todo
    }
}
