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
	 */
	public function save(User $user, Book $book, Rating $newRating)
	{
		/**
		 * @var \Doctrine\ORM\EntityRepository $repo
		 */
		$repo = $this->doctrine->getRepository('AppBundle:Rating');

		$rating = $repo->findOneBy([
			'book' => $book,
			'user' => $user
		]);

		if(null === $rating) {
			$newRating->setUser($user);
			$newRating->setBook($book);
			$rating = $newRating;
		} else {
			$rating->setValue(
				$newRating->getValue()
			);
		}

		$em = $this->doctrine->getManager();
		$em->persist($rating);
		$em->flush();

	}

}