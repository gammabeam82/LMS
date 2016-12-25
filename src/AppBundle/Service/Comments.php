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
	 * @param Book $book
	 * @return mixed
	 */
	public function getQuery(Book $book)
	{
		$repo = $this->doctrine->getRepository('AppBundle:Comment');
		$qb = $repo->createQueryBuilder('c');

		$qb->andWhere('c.book = :book');
		$qb->setParameter('book', $book);

		$qb->orderBy('c.id', 'DESC');

		return $qb->getQuery();
	}


	/**
	 * @param User $user
	 * @param Book $book
	 * @param Comment $comment
	 * @return Comment
	 */
	public function save(User $user, Book $book, Comment $comment)
	{
		$comment->setBook($book);

		$comment->setUser($user);

		$em = $this->doctrine->getManager();
		$em->persist($comment);
		$em->flush();

		return $comment;
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