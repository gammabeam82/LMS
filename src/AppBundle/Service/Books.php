<?php

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use AppBundle\Entity\File as BookFile;
use AppBundle\Entity\User;
use AppBundle\Filter\DTO\BookFilter;
use AppBundle\Utils\ImageThumbnailTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Books extends BaseService
{
    use ImageThumbnailTrait;

    private const IMAGE_TYPES = ['image/jpeg', 'image/png'];

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var string
     */
    private $path;

    /**
     * Books constructor.
     *
     * @param RequestStack $requestStack
     * @param ValidatorInterface $validator
     * @param string $path
     */
    public function __construct(RequestStack $requestStack, ValidatorInterface $validator, string $path)
    {
        $this->requestStack = $requestStack;
        $this->validator = $validator;
        $this->path = $path;
    }

    /**
     * @param User $user
     * @param Book $book
     */
    public function save(User $user, Book $book): void
    {
        $request = $this->requestStack->getCurrentRequest();

        /* @var \Symfony\Component\HttpFoundation\FileBag $fileBag */
        $fileBag = $request->files;

        if (null === $book->getId()) {
            $newFiles = $fileBag->get('book')['bookFiles'];

            $book->setAddedBy($user);
            $book->setViews(0);
            $book->setBookFiles(new ArrayCollection());
            foreach ($newFiles as $file) {
                /* @var UploadedFile $uploadedFile */
                $uploadedFile = $file['name'];

                $this->saveFile($book, $uploadedFile);
            }
        } else {
            $newFiles = $fileBag->get('book_edit')['bookFiles'];

            if (0 !== count($newFiles)) {
                foreach ($newFiles as $file) {
                    /* @var UploadedFile $uploadedFile */
                    $uploadedFile = $file['name'];

                    $this->saveFile($book, $uploadedFile);
                }
            }
        }

        foreach ($book->getBookFiles() as $bookFile) {
            if (0 !== count($this->validator->validate($bookFile))) {
                if (false !== file_exists($bookFile->getName())) {
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
    private function saveFile(Book $book, UploadedFile $uploadedFile, BookFile $bookFile = null): void
    {
        $type = $uploadedFile->guessExtension();
        $filename = sprintf("%s-%s.%s", $book->getName(), uniqid(), $type);

        if (false === $bookFile instanceof BookFile) {
            $bookFile = new BookFile();
        }

        $mimeType = $uploadedFile->getMimeType();

        $bookFile->setBook($book);
        $bookFile->setType($type);
        $bookFile->setMimeType($mimeType);
        $bookFile->setSize($uploadedFile->getSize());
        $bookFile->setName(sprintf("%s/%s", $this->path, $filename));

        $uploadedFile->move($this->path, $filename);

        if (false !== in_array($mimeType, self::IMAGE_TYPES)) {
            $bookFile->setIsImage(true);
            try {
                $thumbnail = $this->generateThumbnail($bookFile->getName(), $this->path);
                $bookFile->setThumbnail($thumbnail);
            } catch (\Exception $e) {

            }
        }

        if (false === $book->getBookFiles()->contains($bookFile)) {
            $book->addBookFile($bookFile);
        }
    }

    /**
     * @param BookFilter $filter
     * @param User|null $user
     * @return Query
     */
    public function getFilteredBooks(BookFilter $filter, User $user = null): Query
    {
        /**
         * @var \Doctrine\ORM\EntityRepository $repo
         */
        $repo = $this->doctrine->getRepository(Book::class);
        $qb = $repo->createQueryBuilder('b');

        $qb->orderBy('b.id', 'DESC');

        if (false === empty($filter->getName())) {
            $qb->andWhere($qb->expr()->like('LOWER(b.name)', ':name'));
            $qb->setParameter('name', sprintf("%%%s%%", mb_strtolower($filter->getName())));
        }

        if (null !== $filter->getAuthors()) {
            $qb->andWhere('b.author IN (:author)');
            $qb->setParameter('author', $filter->getAuthors());
        }

        if (null !== $filter->getGenres()) {
            $qb->andWhere('b.genre IN (:genre)');
            $qb->setParameter('genre', $filter->getGenres());
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
            $qb->setParameter('sr', sprintf("%%%s%%", mb_strtolower($filter->getSearch())));
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

        if (false !== $filter->getLiked() && false !== $user instanceof User) {
            $ids = array_map(function ($book) {
                /** @var Book $book */
                return $book->getId();
            }, $user->getLikes()->toArray());
            $qb->andWhere('b.id IN (:ids)');
            $qb->setParameter('ids', $ids);
        }

        return $qb->getQuery();
    }

    /**
     * @param Book $book
     */
    public function remove(Book $book): void
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
     * @param int $getThumbnail
     * @return BinaryFileResponse
     */
    public function downloadFile(BookFile $file, int $getThumbnail = 0): BinaryFileResponse
    {
        $book = $file->getBook();

        if (false === file_exists($file->getName())) {
            throw new \LogicException();
        }

        $item = (1 === $getThumbnail && file_exists($file->getThumbnail())) ? $file->getThumbnail() : $file->getName();

        $filename = sprintf("%s-%s.%s", $book->getAuthor()->getShortName(), $book->getName(), $file->getType());

        $response = new BinaryFileResponse($item);

        if (false === $file->getIsImage()) {
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
    public function getImages(Book $book): array
    {
        $images = [];
        foreach ($book->getBookFiles() as $file) {
            if (false !== $file->getIsImage()) {
                $images[] = $file;
                $book->removeBookFile($file);
            }
        }

        return $images;
    }

    /**
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function toggleLike(User $user, Book $book): bool
    {
        $hasLike = false;

        if (false === $user->getLikes()->contains($book)) {
            $book->addUser($user);
            $hasLike = true;
        } else {
            $book->removeUser($user);
        }

        $this->save($user, $book);

        return $hasLike;
    }

}
