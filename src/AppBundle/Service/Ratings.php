<?php

namespace AppBundle\Service;

use AppBundle\Entity\Rating;
use AppBundle\Entity\Book;
use AppBundle\Entity\User;

class Ratings extends AbstractService
{

    /**
     * Ratings constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param User $user
     * @param Book $book
     * @param Rating $rating
     */
    public function save(User $user, Book $book, Rating $newRating): void
    {
        /**
         * @var \Doctrine\ORM\EntityRepository $repo
         */
        $repo = $this->doctrine->getRepository(Rating::class);

        $rating = $repo->findOneBy([
            'book' => $book,
            'user' => $user
        ]);

        if (null === $rating) {
            $newRating->setUser($user);
            $newRating->setBook($book);
            $rating = $newRating;
        } else {
            $rating->setValue(
                $newRating->getValue()
            );
        }

        $this->saveEntity($this->doctrine->getManager(), $rating);
    }

    /**
     * @param Book $book
     * @param User $user
     * @return Rating
     */
    public function getRating(Book $book, User $user): Rating
    {
        /**
         * @var \Doctrine\ORM\EntityRepository $repo
         */
        $repo = $this->doctrine->getRepository(Rating::class);

        $rating = $repo->findOneBy([
            'book' => $book,
            'user' => $user
        ]);

        if (null === $rating) {
            $rating = new Rating();
        }

        return $rating;
    }

}
