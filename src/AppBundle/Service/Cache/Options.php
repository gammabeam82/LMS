<?php

namespace AppBundle\Service\Cache;

use AppBundle\Api\Transformer\TransformerInterface;
use AppBundle\Entity\User;
use AppBundle\Filter\FilterInterface;
use Doctrine\ORM\Query;

class Options implements OptionsInterface
{
    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @var Query
     */
    private $query;

    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var TransformerInterface
     */
    private $transformer;

    /**
     * @var bool
     */
    private $refresh = false;

    /**
     * @return FilterInterface
     */
    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }

    /**
     * @param FilterInterface $filter
     *
     * @return Options
     */
    public function setFilter(FilterInterface $filter): Options
    {
        $this->filter = $filter;

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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Options
     */
    public function setUser(User $user): Options
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
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
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
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
     * @return TransformerInterface|null
     */
    public function getTransformer(): ?TransformerInterface
    {
        return $this->transformer;
    }

    /**
     * @return bool
     */
    public function isRefresh(): bool
    {
        return $this->refresh;
    }

    /**
     * @param bool $refresh
     *
     * @return Options
     */
    public function setRefresh(bool $refresh): Options
    {
        $this->refresh = $refresh;

        return $this;
    }

}
