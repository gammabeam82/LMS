<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Entity\Genre;
use AppBundle\Utils\EntityTrait;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AppBundle\Filter\EntityFilterInterface;

class Genres
{
	use EntityTrait;

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

		$this->saveEntity($this->doctrine->getManager(), $genre);
	}

	/**
	 * @param Genre $genre
	 */
	public function remove(Genre $genre)
	{
		$this->removeEntity($this->doctrine->getManager(), $genre);
	}
}