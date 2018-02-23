<?php

namespace AppBundle\Service;

use Predis\ClientInterface;

interface RedisAwareInterface
{
    public function setRedis(ClientInterface $redis = null): void;
}
