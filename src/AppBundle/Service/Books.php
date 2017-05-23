<?php

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use AppBundle\Entity\User;
use AppBundle\Filter\BookFilter;
use AppBundle\Utils\EntityTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\Bundle\DoctrineBundle\Registry;

class Books
{
	use EntityTrait;

	/**
	 * @var Registry
	 */
	private $doctrine;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * Books constructor.
	 * @param Registry $doctrine
	 * @param string $path
	 */
	public function __construct(Registry $doctrine, $path)
	{
		$this->doctrine = $doctrine;
		$this->path = $path;
	}

	/**
	 * @param User $user
	 * @param Book $book
	 * @param bool $isCreating
	 */
	public function save(User $user, Book $book, $isCreating = true)
	{
		if (false !== $isCreating) {
			$book->setAddedBy($user);
			$book->setViews(0);
		}

		foreach($book->getBookFiles() as $file) {

			if(false === is_object($file->getName())) {

				continue;
			}

			/* @var \Symfony\Component\HttpFoundation\File\File $uploadedFile */
			$uploadedFile = $file->getName();

			$type = $uploadedFile->guessExtension();

			/* @var \AppBundle\Entity\File */
			$file->setBook($book);
			$file->setType($type);

			$filename = sprintf("%s.%s", md5(uniqid(rand(), TRUE)), $type);

			$uploadedFile->move($this->path, $filename);

			$file->setName(sprintf("%s/%s", $this->path, $filename));
		}

		$this->saveEntity($this->doctrine->getManager(), $book);
	}

	/**
	 * @param BookFilter $filter
	 * @return \Doctrine\ORM\Query
	 */
	public function getFilteredBooks(BookFilter $filter)
	{
		/**
		 * @var \Doctrine\ORM\EntityRepository $repo
		 */
		$repo = $this->doctrine->getRepository(Book::class);
		$qb = $repo->createQueryBuilder('b');

		$qb->orderBy('b.id', 'DESC');

		if (false === empty($filter->getName())) {
			$qb->andWhere($qb->expr()->like('LOWER(b.name)', ':name'));
			$qb->setParameter('name', "%" . mb_strtolower($filter->getName()) . "%");
		}

		if (null !== $filter->getAuthor()) {
			$qb->andWhere('b.author IN (:author)');
			$qb->setParameter('author', $filter->getAuthor());
		}

		if (null !== $filter->getGenre()) {
			$qb->andWhere('b.genre IN (:genre)');
			$qb->setParameter('genre', $filter->getGenre());
		}

		if (null !== $filter->getSerie()) {
			$qb->andWhere('b.serie IN (:serie)');
			$qb->setParameter('serie', $filter->getSerie());
		}

		if (false === empty($filter->getSearch())) {
			$qb->join('b.author', 'a');
			$expr = $qb->expr()->orX(
				'LOWER(b.name) LIKE :sr',
				'LOWER(a.lastName) LIKE :sr'
			);
			$qb->andWhere($expr);
			$qb->setParameter('sr', "%" . mb_strtolower($filter->getSearch()) . "%");
		}

		if (false === empty($filter->getCreatedAtStart())) {
			$qb->andWhere('b.createdAt >= :createdAtStart');
			$qb->setParameter('createdAtStart', $filter->getCreatedAtStart());
		}

		if (false === empty($filter->getCreatedAtEnd())) {
			$qb->andWhere('b.createdAt <= :createdAtEnd');
			$qb->setParameter('createdAtEnd', $filter->getCreatedAtEnd());
		}

		if (false !== $filter->getMostPopular()) {
			$qb->orderBy('b.views', 'DESC');
		}

		return $qb->getQuery();
	}

	/**
	 * @param Book $book
	 */
	public function remove(Book $book)
	{
		$this->removeEntity($this->doctrine->getManager(), $book);
	}

}