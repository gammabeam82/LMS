<?php

namespace AppBundle\Service\Cache;

use AppBundle\Api\Provider\ApiDataProvider;
use AppBundle\Utils\RedisAwareTrait;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
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
     * @var string
     */
    private $environment;

    /**
     * CacheService constructor.
     *
     * @param PaginatorInterface $paginator
     * @param string $environment
     */
    public function __construct(PaginatorInterface $paginator, string $environment)
    {
        $this->paginator = $paginator;
        $this->environment = $environment;
    }

    /**
     * @param OptionsInterface $options
     *
     * @return SlidingPagination | array
     */
    public function getData(OptionsInterface $options)
    {
        $key = $this->getCacheKey($options);

        if (1 !== $this->redis->exists($key) && false === $options->isRefresh()) {
            $data = $this->paginator->paginate($options->getQuery(), $options->getPage(), $options->getLimit());

            $apiData = (null !== $options->getTransformer()) ? $this->prepareApiData($options, $data) : null;

            $this->redis->set($key, serialize($data));
            $this->redis->expire($key, self::EXPIRE);
        } else {
            $data = unserialize($this->redis->get($key));
        }

        if (null !== $options->getTransformer()) {
            $data = $apiData ?? $this->prepareApiData($options, $data);
        }

        if ('test' === $this->environment) {
            $this->clearCache();
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

    /**
     * @param OptionsInterface $options
     * @param PaginationInterface $data
     *
     * @return array
     */
    private function prepareApiData(OptionsInterface $options, PaginationInterface $data): array
    {
        $provider = new ApiDataProvider([
            'items' => $data,
            'transformer' => $options->getTransformer()
        ]);

        return $provider->getData();
    }
}
