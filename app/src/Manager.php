<?php

/*
 * This file is part of the Supervisor Monitor project.
 *
 * (c) Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Supervisor\Monitor;

use fXmlRpc\Client;
use fXmlRpc\Transport\StreamSocketTransport;
use Supervisor\Connector\XmlRpc;
use Supervisor\Supervisor;

/**
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Manager
{
    /**
     * Created Supervisor instances.
     *
     * @var Supervisor[]
     */
    protected $instances = [];

    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Returns a specific instance.
     *
     * @param string $instance
     *
     * @return Supervisor
     */
    public function get($instance)
    {
        if (isset($this->instances[$instance])) {
            return $this->instances[$instance];
        } elseif (!isset($this->config[$instance])) {
            throw new \InvalidArgumentException(sprintf('Supervisor instance "%s" cannot be found', $instance));
        }

        $config = $this->config[$instance];
        $transport = new StreamSocketTransport();

        $transport->setHeader('Authorization', 'Basic '.base64_encode(sprintf('%s:%s', $config['username'], $config['password'])));

        $client = new Client($config['url'], $transport);
        $connector = new XmlRpc($client);

        return $this->instances[$instance] = new Supervisor($connector);
    }

    /**
     * Returns all Supervisor instances.
     *
     * @return Superisor[]
     */
    public function getAll()
    {
        foreach (array_keys($this->config) as $instance) {
            $this->get($instance);
        }

        return $this->instances;
    }
}
