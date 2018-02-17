<?php

namespace AppBundle\Service\Cache;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

interface CacheServiceInterface
{
    public function getData(OptionsInterface $options): SlidingPagination;

    public function clearCache(): void;
}
