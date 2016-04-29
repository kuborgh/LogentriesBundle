<?php
namespace Kuborgh\LogentriesBundle\Logger;

use JMS\Serializer\SerializerBuilder;
use Kuborgh\LogentriesBundle\Transport\TransportInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InactiveScopeException;

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
     * Container to fetch request from
     *
     * @var Container
     */
    protected $container;

    /**
     * Logger constructor.
     *
     * @param Container $container       Container to get request
     * @param string    $transportClass  Class of the transport service
     * @param array     $transportParams Parameters for the transport service
     * @param bool      $enabled         Enable logging
     */
    public function __construct(Container $container, $transportClass, $transportParams = array(), $enabled = true)
    {
        $this->container = $container;
        $this->transport = new $transportClass($transportParams);
        $this->enabled = $enabled;
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

        // Try to extract request
        try {
            $rq = $this->container->get('request');
            $data['uri'] = $rq->getRequestUri();
            if ($rq->headers->has('referer')) {
                $data['referer'] = $rq->headers->get('referer');
            }
        } catch (InactiveScopeException $exc) {
            // Kernel already terminated or console command
            if (isset($_SERVER['argv']) && $_SERVER['argv']) {
                $data['uri'] = implode(' ', $_SERVER['argv']);
            }
        } catch (\Exception $exc) {
            $data['uri'] = 'Error: '.$exc->getMessage();
        }

        // Use JMS Serializer. This will also allow \DateTime
        try {
            $serializer = SerializerBuilder::create()->build();
            $json = $serializer->serialize($data, 'json');
        } catch (\Exception $exc) {
            // Retry without context
            if (!empty($data['context'])) {
                unset($data['context']);

                $this->log($data);

                return;
            }
            $json = json_encode(array('error' => $exc->getMessage()));
        }

        $this->transport->send($json);
    }
}
