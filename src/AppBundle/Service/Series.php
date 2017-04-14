<?php

namespace AppBundle\Service;
use AppBundle\Entity\BookSeries;
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
	 * @return \Doctrine\ORM\Query
	 */
	public function getQuery()
	{
		/**
		 * @var \Doctrine\ORM\EntityRepository $repo
		 */
		$repo = $this->doctrine->getRepository('AppBundle:BookSeries');
		$qb = $repo->createQueryBuilder('s');

		$qb->orderBy('s.id', 'DESC');

		return $qb->getQuery();
	}

	/**
	 * @param BookSeries $serie
	 */
	public function save(BookSeries $serie)
	{
		$em = $this->doctrine->getManager();

		$em->persist($serie);
		$em->flush();
	}

	/**
	 * @param BookSeries $serie
	 */
	public function remove(BookSeries $serie)
	{
		$em = $this->doctrine->getManager();

		$em->remove($serie);
		$em->flush();
	}
}