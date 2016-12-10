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
	 * @param User $user
	 * @param Genre $genre
	 * @return Genre
	 */
	public function add(User $user, Genre $genre)
	{
		$em = $this->doctrine->getManager();

		$genre->setAddedBy($user);

		$em->persist($genre);
		$em->flush();

		return $genre;
	}
}