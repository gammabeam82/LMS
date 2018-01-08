<?php

namespace AppBundle\Repository;

class ExportItemRepository extends AbstractRepository
{
    /**
     * @return array
     */
    public function findAllFileNames(): array
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.filename');

        return $qb
            ->getQuery()
            ->getResult();
    }
}
