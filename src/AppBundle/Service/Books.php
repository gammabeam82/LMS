<?php

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use AppBundle\Entity\User;
use AppBundle\Filter\BookFilter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class Books
{
	use DoctrineTrait;

	/**
	 * @param User $user
	 * @param Book $book
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
			$book->setFile($fileName);
			$book->setAddedBy($user);
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

		return $qb->getQuery();
	}

	/**
	 * @param Book $book
	 */
	public function remove(Book $book)
	{
		$file = $this->path . "/" . $book->getFile();
		unlink($file);

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
		$file = $this->path . "/" . $book->getFile();

		if (false === file_exists($file)) {
			return false;
		}

		$fileName =
			$book->getAuthor()->getShortName() . "-" .
			$book->getName() . ".txt";

		$response = new BinaryFileResponse($file);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

		return $response;
	}
}