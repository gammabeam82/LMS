<?php

namespace AppBundle\Service;
use AppBundle\Entity\Rating;
use AppBundle\Entity\Book;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;

class Ratings
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
	 * @param Book $book
	 * @param Rating $rating
	 * @return Rating
	 */
	public function save(User $user, Book $book, Rating $rating)
	{
		$rating->setUser($user);
		$rating->setBook($book);

		$em = $this->doctrine->getManager();
		$em->persist($rating);
		$em->flush();

		return $rating;
	}

}