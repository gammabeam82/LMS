<?php

namespace AppBundle\Service;

use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use AppBundle\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AppBundle\Utils\EntityTrait;
use Doctrine\ORM\Query;

class Comments
{
    use EntityTrait;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * Comments constructor.
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param Book|null $book
     * @return Query
     */
    public function getQuery(Book $book = null): Query
    {
        /**
         * @var \Doctrine\ORM\EntityRepository $repo
         */
        $repo = $this->doctrine->getRepository(Comment::class);
        $qb = $repo->createQueryBuilder('c');

        if (null !== $book) {
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
     * @param bool $isCreating
     */
    public function save(User $user, Book $book, Comment $comment): void
    {
        if (null === $comment->getId()) {
            $comment->setBook($book);
            $comment->setUser($user);
        }

        $this->saveEntity($this->doctrine->getManager(), $comment);
    }

    /**
     * @param Comment $comment
     */
    public function remove(Comment $comment): void
    {
        $this->removeEntity($this->doctrine->getManager(), $comment);
    }
}
