<?php

namespace AppBundle\Api\Service;

use AppBundle\Api\Transformer\TransformerInterface;
use Doctrine\ORM\Query;

class Options implements OptionsInterface
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @var TransformerInterface
     */
    private $transformer;

    /**
     * @var int
     */
    private $page = 1;

    /**
     * @var int
     */
    private $limit = 30;

    /**
     * @var bool
     */
    private $refreshCache = false;

    /**
     * @param Query $query
     *
     * @return Options
     */
    public function setQuery(Query $query): Options
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param TransformerInterface $transformer
     *
     * @return Options
     */
    public function setTransformer(TransformerInterface $transformer): Options
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * @param int $page
     *
     * @return Options
     */
    public function setPage(int $page): Options
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return Options
     */
    public function setLimit(int $limit): Options
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param bool $refreshCache
     *
     * @return Options
     */
    public function setRefreshCache(bool $refreshCache): Options
    {
        $this->refreshCache = $refreshCache;

        return $this;
    }

    /**
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * @return TransformerInterface
     */
    public function getTransformer(): TransformerInterface
    {
        return $this->transformer;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return bool
     */
    public function getRefreshCache(): bool
    {
        return $this->refreshCache;
    }
}
