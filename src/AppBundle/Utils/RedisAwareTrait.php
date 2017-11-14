<?php

namespace AppBundle\Utils;

use Predis\Client;

trait RedisAwareTrait
{
    /**
     * @var Client
     */
    protected $redis;

    /**
     * @param Client $redis
     */
    public function setRedis(Client $redis = null): void
    {
        $this->redis = $redis;
    }
}
