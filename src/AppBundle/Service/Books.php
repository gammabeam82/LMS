<?php

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use AppBundle\Entity\User;
use AppBundle\Filter\BookFilter;

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

	/**
	 * @param BookFilter $filter
	 * @return mixed
	 */
	public function getFilteredBooks(BookFilter $filter)
	{
		$repo = $this->doctrine->getRepository('AppBundle:Book');
		$qb = $repo->createQueryBuilder('b');

		$qb->orderBy('b.name', 'ASC');


		if(!empty($filter->getName())) {
			$qb->andWhere('b.name LIKE :name')
				->setParameter(':name', "%".$filter->getName()."%");
		}

		if($filter->getAuthor()) {
			$qb->andWhere('b.author = :author')
				->setParameter('author', $filter->getAuthor());
		}

		if($filter->getGenre()) {
			$qb->andWhere('b.genre = :genre')
				->setParameter('genre', $filter->getGenre());
		}

		return $qb->getQuery();
	}
}