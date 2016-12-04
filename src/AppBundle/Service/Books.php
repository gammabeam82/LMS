<?php

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use AppBundle\Entity\User;

class Books
{
	use DoctrineTrait;

	/**
	 * @param User $user
	 * @param Book $book
	 * @return Book
	 */
	public function add(User $user, Book $book)
	{
		$em = $this->doctrine->getManager();

		$book->setAddedBy($user);

		$em->persist($book);
		$em->flush();

		return $book;
	}
}