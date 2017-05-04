<?php

namespace AppBundle\Service;

use AppBundle\Entity\Serie;
use AppBundle\Filter\EntityFilterInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;

class Series
{
	/**
	 * @var Registry
	 */
	private $doctrine;

	/**
	 * @param Registry $doctrine
	 */
	public function __construct(Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}

	/**
	 * @param EntityFilterInterface $filter
	 * @return \Doctrine\ORM\Query
	 */
	public function getFilteredSeries(EntityFilterInterface $filter)
	{
		/**
		 * @var \Doctrine\ORM\EntityRepository $repo
		 */
		$repo = $this->doctrine->getRepository(Serie::class);
		$qb = $repo->createQueryBuilder('s');

		if (false === empty($filter->getName())) {
			$qb->andWhere($qb->expr()->like('LOWER(s.name)', ':name'));
			$qb->setParameter('name', "%" . mb_strtolower($filter->getName()) . "%");
		}

		if (false !== $filter->getSortByName()) {
			$qb->orderBy('s.name', 'ASC');
		} else {
			$qb->orderBy('s.id', 'DESC');
		}

		return $qb->getQuery();
	}

	/**
	 * @param Serie $serie
	 */
	public function save(Serie $serie)
	{
		$em = $this->doctrine->getManager();
		$em->persist($serie);
		$em->flush();
	}

	/**
	 * @param Serie $serie
	 */
	public function remove(Serie $serie)
	{
		$em = $this->doctrine->getManager();
		$em->remove($serie);
		$em->flush();
	}
}