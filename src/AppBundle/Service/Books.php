<?php

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use AppBundle\Entity\File as BookFile;
use AppBundle\Entity\User;
use AppBundle\Filter\BookFilter;
use AppBundle\Utils\EntityTrait;
use AppBundle\Utils\ImageThumbnailTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class Books
{
	use EntityTrait;
	use ImageThumbnailTrait;

	/**
	 * @var RequestStack
	 */
	private $requestStack;

	/**
	 * @var Registry
	 */
	private $doctrine;

	/**
	 * @var RecursiveValidator
	 */
	private $validator;

	/**
	 * @var string
	 */
	private $path;


	public function __construct(RequestStack $requestStack, Registry $doctrine, RecursiveValidator $validator, $path)
	{
		$this->requestStack = $requestStack;
		$this->doctrine = $doctrine;
		$this->validator = $validator;
		$this->path = $path;
	}

	public function save(User $user, Book $book, $isCreating = true)
	{
		if (false !== $isCreating) {
			$book->setAddedBy($user);
			$book->setViews(0);

			foreach ($book->getBookFiles() as $file) {
				/* @var UploadedFile $uploadedFile */
				$uploadedFile = $file->getName();

				$this->saveFile($book, $uploadedFile, $file);
			}
		} else {
			$request = $this->requestStack->getCurrentRequest();

			/* @var \Symfony\Component\HttpFoundation\FileBag $fileBag */
			$fileBag = $request->files;

			$newFiles = $fileBag->get('book_edit')['bookFiles'];

			if(0 !== count($newFiles)) {
				foreach ($newFiles as $file) {
					/* @var UploadedFile $uploadedFile */
					$uploadedFile = $file['name'];

					$this->saveFile($book, $uploadedFile);
				}
			}
		}

		foreach($book->getBookFiles() as $bookFile) {
			if(0 !== count($this->validator->validate($bookFile))) {
				if(false !== file_exists($bookFile->getName())) {
					unlink($bookFile->getName());
				}
				throw new \UnexpectedValueException();
			}
		}

		$this->saveEntity($this->doctrine->getManager(), $book);
	}


	/**
	 * @param Book $book
	 * @param UploadedFile $uploadedFile
	 * @param BookFile|null $bookFile
	 */
	private function saveFile(Book $book, UploadedFile $uploadedFile, BookFile $bookFile = null)
	{
		$type = $uploadedFile->guessExtension();
		$filename = sprintf("%s-%s.%s", $book->getName(), uniqid(), $type);

		if(false === $bookFile instanceof BookFile) {
			$bookFile = new BookFile();
		}

		$mimeType = $uploadedFile->getMimeType();

		$bookFile->setBook($book);
		$bookFile->setType($type);
		$bookFile->setMimeType($mimeType);
		$bookFile->setSize($uploadedFile->getSize());
		$bookFile->setName(sprintf("%s/%s", $this->path, $filename));

		$uploadedFile->move($this->path, $filename);

		if(false !== in_array($mimeType, ['image/jpeg', 'image/png'])) {
			$bookFile->setIsImage(true);
			try {
				$thumbnail = $this->generateThumbnail($bookFile->getName(), $this->path);
				$bookFile->setThumbnail($thumbnail);
			} catch (\Exception $e) {

			}
		}

		if(false === $book->getBookFiles()->contains($bookFile)) {
			$book->addBookFile($bookFile);
		}
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

	/**
	 * @param BookFile $file
	 */
	public function removeFile(BookFile $file)
	{
		$this->removeEntity($this->doctrine->getManager(), $file);
	}

	/**
	 * @param BookFile $file
	 * @return BinaryFileResponse
	 */
	public function downloadFile(BookFile $file, $getThumbnail = 0)
	{
		$book = $file->getBook();

		if (false === file_exists($file->getName())) {
			throw new \LogicException();
		}

		$item = (1 === $getThumbnail && file_exists($file->getThumbnail())) ? $file->getThumbnail() : $file->getName();

		$filename = sprintf("%s-%s.%s", $book->getAuthor()->getShortName(), $book->getName(), $file->getType());

		$response = new BinaryFileResponse($item);

		if(false === $file->getIsImage()) {
			$book->incViews();
			$this->saveEntity($this->doctrine->getManager(), $book);

			$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
		}

		return $response;
	}

	/**
	 * @param Book $book
	 * @return array
	 */
	public function getImages(Book $book)
	{
		$images = [];
		foreach($book->getBookFiles() as $file) {
			if(false !== $file->getIsImage()) {
				$images[] = $file;
				$book->removeBookFile($file);
			}
		}
		return $images;
	}

}