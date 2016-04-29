<?php
namespace Kuborgh\LogentriesBundle\Monolog\Handler;

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\SerializerBuilder;
use Kuborgh\LogentriesBundle\Logger\Logger;
use Kuborgh\LogentriesBundle\Transport\TransportInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InactiveScopeException;

/**
 * Monolog handler for logentries
 */
class LogentriesHandler extends AbstractProcessingHandler
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Set logger
     *
     * @param Logger $logger
     *
     * @return LogentriesHandler
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Delegate to the logger
     *
     * @param  array $record
     */
    protected function write(array $record)
    {
        // Remove formatted message
        unset($record['formatted']);

        $this->logger->log($record);
    }
}
