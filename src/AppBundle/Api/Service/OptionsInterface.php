<?php

namespace AppBundle\Api\Service;

use AppBundle\Api\Transformer\TransformerInterface;
use Doctrine\ORM\Query;

interface OptionsInterface
{
    public function getQuery(): Query;

    public function getTransformer(): TransformerInterface;

    public function getPage(): int;

    public function getLimit(): int;

    public function getRefreshCache(): bool;
}
