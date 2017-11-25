<?php

namespace AppBundle\Api\Service;

use AppBundle\Utils\RedisAwareTrait;
use Doctrine\ORM\Query;
use AppBundle\Api\Transformer\TransformerInterface;
use AppBundle\Api\Provider\ApiDataProvider;
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
     * RedisCache constructor.
     *
     * @param PaginatorInterface $paginator
     */
    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @param Query $query
     * @param TransformerInterface $transformer
     * @param int $page
     * @param int $limit
     * @param bool $refreshCache
     *
     * @return array
     */
    public function loadData(Query $query, TransformerInterface $transformer, int $page, int $limit = 30, bool $refreshCache = false): array
    {
        $key = sprintf("cache:%s:%s", substr(md5($query->getSQL()), 0, 20), $page);

        if (1 !== $this->redis->exists($key) || false !== $refreshCache) {
            $provider = new ApiDataProvider([
                'items' => $this->paginator->paginate($query, $page, $limit),
                'transformer' => $transformer
            ]);

            $data = $provider->getData();

            $this->redis->set($key, serialize($data));
            $this->redis->expire($key, self::EXPIRE);
        } else {
            $data = unserialize($this->redis->get($key));
        }

        return $data;
    }
}
