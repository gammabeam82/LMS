<?php

namespace AppBundle\Service\Cache;

use AppBundle\Utils\RedisAwareTrait;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;

class CacheService implements CacheServiceInterface
{
    use RedisAwareTrait;

    private const EXPIRE = 3600;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * CacheService constructor.
     *
     * @param PaginatorInterface $paginator
     */
    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @param OptionsInterface $options
     *
     * @return SlidingPagination
     */
    public function getData(OptionsInterface $options): SlidingPagination
    {
        $key = $this->getCacheKey($options);

        if (1 !== $this->redis->exists($key) && false === $options->isRefresh()) {
            $data = $this->paginator->paginate($options->getQuery(), $options->getPage(), $options->getLimit());

            $this->redis->set($key, serialize($data));
            $this->redis->expire($key, self::EXPIRE);

        } else {
            $data = unserialize($this->redis->get($key));
        }

        return $data;
    }

    public function clearCache(): void
    {
        $this->redis->flushdb();
    }

    /**
     * @param OptionsInterface $options
     *
     * @return string
     */
    private function getCacheKey(OptionsInterface $options): string
    {
        return sprintf(
            "cache_filter:%s:%s",
            substr(md5(serialize($options->getFilter())), 0, 20),
            $options->getPage()
        );
    }
}
