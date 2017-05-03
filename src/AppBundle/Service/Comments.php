<?php

namespace AppBundle\Service;

use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use AppBundle\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Registry;

class Comments
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
	 * @param Book|null $book
	 * @return \Doctrine\ORM\Query
	 */
	public function getQuery(Book $book = null)
	{
		/**
		 * @var \Doctrine\ORM\EntityRepository $repo
		 */
		$repo = $this->doctrine->getRepository('AppBundle:Comment');
		$qb = $repo->createQueryBuilder('c');

		if(null !== $book) {
			$qb->andWhere('c.book = :book');
			$qb->setParameter('book', $book);
		}

		$qb->orderBy('c.id', 'DESC');

		return $qb->getQuery();
	}


	/**
	 * @param User $user
	 * @param Book $book
	 * @param Comment $comment
	 */
	public function save(User $user, Book $book, Comment $comment)
	{
		$comment->setBook($book);
		$comment->setUser($user);

		$em = $this->doctrine->getManager();
		$em->persist($comment);
		$em->flush();

	}

	/**
	 * @param Comment $comment
	 */
	public function remove(Comment $comment)
	{
		$em = $this->doctrine->getManager();
		$em->remove($comment);
		$em->flush();
	}
}