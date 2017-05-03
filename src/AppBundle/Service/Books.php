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
	public function save(User $user, Book $book, $isCreating = true, $originalFile = null)
	{
		if (false !== $isCreating) {
			$book->setAddedBy($user);
			$book->setViews(0);
			$this->saveFile($book);
		}

		if(false == empty($originalFile) && file_exists($originalFile)) {
			unlink($originalFile);
			$this->saveFile($book);
		}

		$em = $this->doctrine->getManager();
		$em->persist($book);
		$em->flush();

	}

	/**
	 * @param Book $book
	 */
	private function saveFile(Book $book)
	{
		/**
		 * @var \Symfony\Component\HttpFoundation\File\File $file
		 */
		$file = $book->getFile();
		$fileName = md5(uniqid(rand(), TRUE)) . "." . $file->guessExtension();

		$file->move(
			$this->path,
			$fileName
		);

		$book->setFile(sprintf("%s/%s", $this->path, $fileName));
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
		$repo = $this->doctrine->getRepository('AppBundle:Book');
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

		if (false === file_exists($book->getFile())) {
			throw new \LogicException();
		}

		$fileName = sprintf("%s-%s.txt", $book->getAuthor()->getShortName(), $book->getName());

		$book->setViews($book->getViews() + 1);

		$em = $this->doctrine->getManager();
		$em->persist($book);
		$em->flush();

		$response = new BinaryFileResponse($book->getFile());
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

		return $response;
	}
}