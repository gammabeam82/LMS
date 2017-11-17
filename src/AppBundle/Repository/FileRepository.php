<?php

namespace AppBundle\Repository;

class FileRepository extends AbstractRepository
{
    /**
     * @return array
     */
    public function findAllFileNames(): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('f.name', 'f.thumbnail');

        return $qb
            ->getQuery()
            ->getResult();
    }
}
