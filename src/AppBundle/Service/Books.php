<?php

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use AppBundle\Entity\User;
use AppBundle\Filter\BookFilter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\Bundle\DoctrineBundle\Registry;

class Books
{
	/**
	 * @var Registry
	 */
	private $doctrine;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @param Registry $doctrine
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
	 * @return Book
	 */
	public function save(User $user, Book $book, $isCreating = true)
	{
		if (false !== $isCreating) {
			$file = $book->getFile();
			$fileName = md5(uniqid(rand(), TRUE)) . "." . $file->guessExtension();

			$file->move(
				$this->path,
				$fileName
			);
			$book->setFile($this->path . "/" . $fileName);
			$book->setAddedBy($user);
			$book->setViews(0);
		}

		$em = $this->doctrine->getManager();
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

		$qb->orderBy('b.id', 'DESC');

		if (!empty($filter->getName())) {
			$qb->andWhere($qb->expr()->like('LOWER(b.name)', ':name'))
				->setParameter('name', "%" . strtolower($filter->getName()) . "%");
		}

		if ($filter->getAuthor() && count($filter->getAuthor())) {
			$qb->andWhere('b.author IN (:author)')
				->setParameter('author', $filter->getAuthor());
		}

		if ($filter->getGenre() && count($filter->getGenre())) {
			$qb->andWhere('b.genre IN (:genre)')
				->setParameter('genre', $filter->getGenre());
		}

		if (!empty($filter->getSearch())) {
			$qb->join('b.author', 'a');
			$expr = $qb->expr()->orX(
				'LOWER(b.name) LIKE :sr',
				'LOWER(a.lastName) LIKE :sr'
			);
			$qb->andWhere($expr);
			$qb->setParameter('sr', "%" . strtolower($filter->getSearch()) . "%");
		}

		if ($filter->getCreatedAtStart()) {
			$qb->andWhere('b.createdAt >= :createdAtStart');
			$qb->setParameter('createdAtStart', $filter->getCreatedAtStart());
		}

		if ($filter->getCreatedAtEnd()) {
			$qb->andWhere('b.createdAt <= :createdAtEnd');
			$qb->setParameter('createdAtEnd', $filter->getCreatedAtEnd());
		}

		if ($filter->getMostPopular()) {
			$qb->orderBy('b.views', 'DESC');
		}

		return $qb->getQuery();
	}

	/**
	 * @param Book $book
	 */
	public function remove(Book $book)
	{
		$em = $this->doctrine->getManager();
		$em->remove($book);
		$em->flush();
	}

	/**
	 * @param Book $book
	 * @return bool|BinaryFileResponse
	 */
	public function download(Book $book)
	{

		if (false === file_exists($book->getFile())) {
			return false;
		}

		$fileName =
			$book->getAuthor()->getShortName() . "-" .
			$book->getName() . ".txt";

		$book->setViews($book->getViews() + 1);

		$em = $this->doctrine->getManager();
		$em->persist($book);
		$em->flush();

		$response = new BinaryFileResponse($book->getFile());
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

		return $response;
	}
}