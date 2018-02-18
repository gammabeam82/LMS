<?php

namespace AppBundle\Service\Cache;

use AppBundle\Api\Transformer\TransformerInterface;
use AppBundle\Entity\User;
use AppBundle\Filter\FilterInterface;
use Doctrine\ORM\Query;

interface OptionsInterface
{
    public function getFilter(): FilterInterface;

    public function setFilter(FilterInterface $filter): Options;

    public function getQuery(): Query;

    public function setQuery(Query $query): Options;

    public function getUser(): User;

    public function setUser(User $user): Options;

    public function getPage(): int;

    public function setPage(int $page): Options;

    public function getLimit(): int;

    public function setLimit(int $limit): Options;

    public function getTransformer(): ?TransformerInterface;

    public function setTransformer(TransformerInterface $transformer): Options;

    public function isRefresh(): bool;

    public function setRefresh(bool $refresh): Options;
}
