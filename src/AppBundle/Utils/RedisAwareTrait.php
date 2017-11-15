<?php

namespace AppBundle\Utils;

use Predis\ClientInterface;

trait RedisAwareTrait
{
    /**
     * @var ClientInterface
     */
    protected $redis;

    /**
     * @param ClientInterface $redis
     */
    public function setRedis(ClientInterface $redis = null): void
    {
        $this->redis = $redis;
    }
}
