<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

abstract class Repository extends EntityRepository
{
    /**
     * @return int
     */
    public function count(): int
    {
        return $this
            ->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
