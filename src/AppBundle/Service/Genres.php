<?php

namespace AppBundle\Service;
use AppBundle\Entity\User;
use AppBundle\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AppBundle\Filter\EntityFilterInterface;

class Genres
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
	public function getFilteredGenres(EntityFilterInterface $filter)
	{
		/**
		 * @var \Doctrine\ORM\EntityRepository $repo
		 */
		$repo = $this->doctrine->getRepository(Genre::class);
		$qb = $repo->createQueryBuilder('g');

		if (false === empty($filter->getName())) {
			$qb->andWhere($qb->expr()->like('LOWER(g.name)', ':name'));
			$qb->setParameter('name', "%" . mb_strtolower($filter->getName()) . "%");
		}

		if (false !== $filter->getSortByName()) {
			$qb->orderBy('g.name', 'ASC');
		} else {
			$qb->orderBy('g.id', 'DESC');
		}

		return $qb->getQuery();
	}

	/**
	 * @param User $user
	 * @param Genre $genre
	 * @param bool $isCreating
	 */
	public function save(User $user, Genre $genre, $isCreating = true)
	{
		if(false !== $isCreating) {
			$genre->setAddedBy($user);
		}

		$em = $this->doctrine->getManager();
		$em->persist($genre);
		$em->flush();

	}

	/**
	 * @param Genre $genre
	 */
	public function remove(Genre $genre)
	{
		$em = $this->doctrine->getManager();
		$em->remove($genre);
		$em->flush();
	}
}