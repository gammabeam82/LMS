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
		if(false !== $isCreating) {
			$file = $book->getFile();
			$fileName = md5(uniqid(rand(), TRUE)).".".$file->guessExtension();

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

	/**
	 * @param Book $book
	 */
	public function remove(Book $book)
	{
		$file = $this->path."/".$book->getFile();
		unlink($file);

		$em = $this->doctrine->getManager();

		$em->remove($book);
		$em->flush();
	}

	/**
	 * @param Book $book
	 * @return BinaryFileResponse
	 */
	public function download(Book $book)
	{
		$file = $this->path."/".$book->getFile();
		$fileName =
			$book->getAuthor()->getShortName()."-".
			$book->getName().".txt";

		$response = new BinaryFileResponse($file);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

		return $response;
	}
}