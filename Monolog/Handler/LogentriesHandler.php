<?php
namespace Kuborgh\LogentriesBundle\Monolog\Handler;

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\SerializerBuilder;
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
     * @var TransportInterface
     */
    protected $transport;

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * Container to fetch request from
     *
     * @var Container
     */
    protected $container;

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
     * Set container
     *
     * @param Container $container
     *
     * @return LogentriesHandler
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
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

        // Try to extract request
        try {
            $rq = $this->container->get('request');
            $record['uri'] = $rq->getRequestUri();
        } catch (InactiveScopeException $exc) {
            // Kernel already terminated or console command
            if (isset($_SERVER['argv']) && $_SERVER['argv']) {
                $record['uri'] = implode(' ', $_SERVER['argv']);
            }
        } catch (\Exception $exc) {
            $record['uri'] = 'Error: '.$exc->getMessage();
        }

        // Use JMS Serializer. This will also allow \DateTime
        try {
            $serializer = SerializerBuilder::create()->build();
            $json = $serializer->serialize($record, 'json');
        } catch (\Exception $exc) {
            // Retry without context
            if (!empty($record['context'])) {
                unset($record['context']);

                return $this->write($record);
            }
            $json = json_encode(array('error' => $exc->getMessage()));
        }

        $this->transport->send($json);
    }
}
