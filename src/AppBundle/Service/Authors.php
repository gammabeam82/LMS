<?php

namespace AppBundle\Service;
use AppBundle\Entity\User;
use AppBundle\Entity\Author;

class Authors
{
	use DoctrineTrait;

	/**
	 * @param User $user
	 * @param Author $author
	 * @return Author
	 */
	public function add(User $user, Author $author)
	{
		$em = $this->doctrine->getManager();

		$author->setAddedBy($user);

		$em->persist($author);
		$em->flush();

		return $author;
	}
}