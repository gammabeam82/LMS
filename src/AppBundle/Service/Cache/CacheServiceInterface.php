<?php

namespace AppBundle\Service\Cache;

interface CacheServiceInterface
{
    public function getData(OptionsInterface $options);

    public function clearCache(): void;
}
