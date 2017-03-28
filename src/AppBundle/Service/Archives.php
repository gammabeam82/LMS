<?php

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use ZipArchive;

class Archives
{
	/**
	 * @var RequestStack
	 */
	private $requestStack;

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
	 * Archives constructor.
	 * @param RequestStack $requestStack
	 * @param Registry $doctrine
	 * @param $varName
	 * @param $path
	 */
	public function __construct(RequestStack $requestStack, Registry $doctrine, $varName, $path)
	{
		$this->requestStack = $requestStack;
		$this->doctrine = $doctrine;
		$this->session = $requestStack->getCurrentRequest()->getSession();
		$this->varName = $varName;
		$this->path = $path;
	}

	/**
	 * @return array|mixed
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

		if(false === in_array($bookId, $data)) {
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

		$bookId = $book->getId();

		$this->setSessionData(array_diff($data, [$bookId]));
	}

	/**
	 * @return array|bool
	 */
	public function getBooksList()
	{
		if(0 == count($this->getSessionData())) {
			return false;
		}

		/**
		 * @var \Doctrine\ORM\EntityRepository $repo
		 */
		$repo = $this->doctrine->getRepository('AppBundle:Book');
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
	 * @return bool|BinaryFileResponse
	 */
	public function getArchive()
	{
		if(0 == count($this->getSessionData())) {
			return false;
		}

		/**
		 * @var \Doctrine\ORM\EntityRepository $repo
		 */
		$repo = $this->doctrine->getRepository('AppBundle:Book');

		$qb = $repo->createQueryBuilder('b');
		$qb->where($qb->expr()->in('b.id', $this->getSessionData()));

		$books = $qb->getQuery()->execute();

		$zip = new ZipArchive();
		$file = sprintf("%s/%s.zip", $this->path, md5(uniqid(rand(), TRUE)));

		$zip->open($file, ZIPARCHIVE::CREATE);

		foreach($books as $book) {
			/**
			 * @var \AppBundle\Entity\Book $book
			 */
			$localname = sprintf("%s-%s.txt", $book->getAuthor()->getShortName(), $book->getName());
			$zip->addFile($book->getFile(), $localname);
		}

		$zip->close();

		$response = new BinaryFileResponse($file);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, "books.zip");

		return $response;
	}

}