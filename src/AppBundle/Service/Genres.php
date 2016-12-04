<?php

namespace AppBundle\Service;
use AppBundle\Entity\User;
use AppBundle\Entity\Genre;

class Genres
{
	use DoctrineTrait;

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