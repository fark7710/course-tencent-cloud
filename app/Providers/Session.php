<?php

namespace App\Providers;

use Phalcon\Session\Adapter\Redis as RedisSession;

class Session extends AbstractProvider
{

    protected $serviceName = 'session';

    public function register()
    {
        $this->di->setShared($this->serviceName, function () {

            $config = $this->getShared('config');

            $session = new RedisSession([
                'host' => $config->redis->host,
                'port' => $config->redis->port,
                'auth' => $config->redis->auth,
                'lifetime' => $config->session->lifetime,
                'index' => 1,
            ]);

            $session->start();

            return $session;
        });
    }

}