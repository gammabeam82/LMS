<?php

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use ZipArchive;
use LengthException;

class Archives
{

	/**
	 * @var Registry
	 */
	private $doctrine;

	/**
	 * @var string
	 */
	private $varName;

	/**
	 * @var SessionInterface
	 */
	private $session;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * Archives constructor.
	 * @param SessionInterface $session
	 * @param Registry $doctrine
	 * @param $varName
	 * @param $path
	 * @param User $user
	 */
	public function __construct(SessionInterface $session, Registry $doctrine, $varName, $path, User $user)
	{
		$this->doctrine = $doctrine;
		$this->session = $session;
		$this->varName = $varName;
		$this->path = $path;
		$this->user = $user;
	}

	/**
	 * @return array
	 */
	private function getSessionData()
	{
		$session = $this->session;
		$varName = $this->varName;

		return ($session->get($varName)) ? unserialize($session->get($varName)) : [];
	}

	/**
	 * @param array $data
	 */
	private function setSessionData($data)
	{
		$this->session->set($this->varName, serialize($data));
	}

	/**
	 * @return int
	 */
	public function getBooksCount()
	{
		return count($this->getSessionData());
	}

	/**
	 * @param Book $book
	 */
	public function addBookToArchive(Book $book)
	{

		$data = $this->getSessionData();

		$bookId = $book->getId();

		if (false === in_array($bookId, $data)) {
			$data[] = $bookId;

			$this->setSessionData($data);
		}

	}

	/**
	 * @param Book $book
	 */
	public function removeBookFromArchive(Book $book)
	{

		$data = $this->getSessionData();

		$this->setSessionData(array_diff($data, [
			$book->getId()
		]));
	}

	/**
	 * @return array|bool
	 */
	public function getBooksList()
	{
		if (0 == count($this->getSessionData())) {
			return false;
		}

		/* @var \Doctrine\ORM\EntityRepository $repo */
		$repo = $this->doctrine->getRepository(Book::class);
		$qb = $repo->createQueryBuilder('b');

		$qb->select('b.id, b.name');
		$qb->where($qb->expr()->in('b.id', $this->getSessionData()));

		$result = $qb->getQuery()->execute();

		return $result;
	}

	/**
	 * @return array
	 */
	public function getBookIds()
	{
		return $this->getSessionData();
	}

	/**
	 * @return BinaryFileResponse
	 */
	public function getArchive()
	{
		if (0 == count($this->getSessionData())) {
			throw new LengthException();
		}

		/* @var \Doctrine\ORM\EntityRepository $repo */
		$repo = $this->doctrine->getRepository(Book::class);

		$em = $this->doctrine->getManager();

		$qb = $repo->createQueryBuilder('b');
		$qb->where($qb->expr()->in('b.id', $this->getSessionData()));

		$books = $qb->getQuery()->execute();

		array_map(function($book) use ($em) {
			/* @var \AppBundle\Entity\Book $book */
			$book->incViews();
			$em->persist($book);
			}, $books);

		$em->flush();

		$zip = new ZipArchive();
		$file = sprintf("%s/%s.zip", $this->path, $this->user->getId());

		$zip->open($file, ZIPARCHIVE::CREATE);

		foreach ($books as $book) {
			/* @var \AppBundle\Entity\Book $book */
			$localname = sprintf("%s-%s.txt", $book->getAuthor()->getShortName(), $book->getName());
			$zip->addFile($book->getFile(), $localname);
		}

		$zip->close();

		$response = new BinaryFileResponse($file);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, "books.zip");

		return $response;
	}

}