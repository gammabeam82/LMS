<?php

namespace AppBundle\Repository;

class BookRepository extends AbstractRepository
{
    /**
     * @param array $bookIds
     * @return array
     */
    public function findByIds(array $bookIds): array
    {
        $qb = $this->createQueryBuilder('b');
        $qb->where($qb->expr()->in('b.id', $bookIds));

        return $qb
            ->getQuery()
            ->getResult();
    }
}
