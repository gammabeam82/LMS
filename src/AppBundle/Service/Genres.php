<?php

namespace AppBundle\Service;
use AppBundle\Entity\User;
use AppBundle\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Registry;

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
	 * @return \Doctrine\ORM\Query
	 */
	public function getQuery()
	{
		/**
		 * @var \Doctrine\ORM\EntityRepository $repo
		 */
		$repo = $this->doctrine->getRepository('AppBundle:Genre');
		$qb = $repo->createQueryBuilder('g');

		$qb->orderBy('g.id', 'DESC');

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