<?php

namespace AppBundle\Repository;

class AuthorRepository extends AbstractRepository
{
	/**
	 * @param $letter
	 * @return array
	 */
	public function findAllStartsWith($letter)
	{
		$qb = $this->createQueryBuilder('a');

		$qb->andWhere($qb->expr()->like('LOWER(a.lastName)', ':name'));
		$qb->setParameter('name', mb_strtolower($letter) . "%");

		return $qb
			->getQuery()
			->getResult();
	}
}