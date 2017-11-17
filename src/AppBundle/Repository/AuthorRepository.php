<?php

namespace AppBundle\Repository;

class AuthorRepository extends Repository
{
    /**
     * @param string $letter
     * @return array
     */
    public function findAllStartsWith(string $letter): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb->andWhere($qb->expr()->like('LOWER(a.lastName)', ':name'));
        $qb->setParameter('name', sprintf("%s%", mb_strtolower($letter)));

        return $qb
            ->getQuery()
            ->getResult();
    }
}
