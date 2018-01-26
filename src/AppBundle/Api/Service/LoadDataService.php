<?php

namespace AppBundle\Api\Service;

use AppBundle\Api\Provider\ApiDataProvider;
use AppBundle\Utils\RedisAwareTrait;
use Knp\Component\Pager\PaginatorInterface;

class LoadDataService
{
    use RedisAwareTrait;

    private const EXPIRE = 3600;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * LoadDataService constructor.
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
     * @return array
     */
    public function loadData(OptionsInterface $options): array
    {
        $key = $this->getCacheKey($options);

        if (1 !== $this->redis->exists($key) || false !== $options->getRefreshCache()) {
            $provider = new ApiDataProvider([
                'items' => $this->paginator->paginate($options->getQuery(), $options->getPage(), $options->getLimit()),
                'transformer' => $options->getTransformer()
            ]);

            $data = $provider->getData();

            $this->redis->set($key, serialize($data));
            $this->redis->expire($key, self::EXPIRE);
        } else {
            $data = unserialize($this->redis->get($key));
        }

        return $data;
    }

    /**
     * @param OptionsInterface $options
     *
     * @return string
     */
    private function getCacheKey(OptionsInterface $options): string
    {
        return sprintf("cache:%s:%s", substr(md5($options->getQuery()->getSQL()), 0, 20), $options->getPage());
    }
}
