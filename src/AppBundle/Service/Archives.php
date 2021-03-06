<?php

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use AppBundle\Service\Interfaces\ArchiveServiceInterface;
use AppBundle\Utils\RedisAwareTrait;
use LengthException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use ZipStream\ZipStream;

class Archives extends BaseService implements RedisAwareInterface, ArchiveServiceInterface
{
    use RedisAwareTrait;

    /**
     * @var string
     */
    private $key;

    /**
     * Archives constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->key = sprintf("archive:%s", $tokenStorage->getToken()->getUser()->getId());
    }

    /**
     * @return int
     */
    public function getBooksCount(): int
    {
        return $this->redis->hlen($this->key);
    }

    /**
     * @param Book $book
     */
    public function addBookToArchive(Book $book): void
    {
        $this->redis->hsetnx($this->key, $book->getId(), $book->getName());
    }

    /**
     * @param Book $book
     */
    public function removeBookFromArchive(Book $book): void
    {
        $this->redis->hdel($this->key, [$book->getId()]);
    }

    /**
     * @return array
     */
    public function getBooksList(): array
    {
        if (0 === $this->redis->hlen($this->key)) {
            return [];
        }

        return $this->redis->hgetall($this->key);
    }

    /**
     * @return array
     */
    public function getBookIds(): array
    {
        return $this->redis->hkeys($this->key);
    }

    /**
     * @return BinaryFileResponse
     * @throws \ZipStream\Exception\FileNotFoundException
     * @throws \ZipStream\Exception\FileNotReadableException
     */
    public function getArchive(): BinaryFileResponse
    {
        if (0 === $this->redis->hlen($this->key) || false === $this->redis->exists($this->key)) {
            throw new LengthException();
        }

        $repo = $this->doctrine->getRepository(Book::class);

        $em = $this->doctrine->getManager();

        $books = $repo->findByIds($this->getBookIds());

        array_map(function ($book) use ($em) {
            /* @var Book $book */
            $book->incViews();
            $em->persist($book);
        }, $books);

        $em->flush();

        $zipStream = new ZipStream("books.zip");

        foreach ($books as $book) {
            /* @var \AppBundle\Entity\Book $book */
            foreach ($book->getBookFiles() as $bookFile) {
                /* @var \AppBundle\Entity\File $bookFile */
                if (false === $bookFile->getIsImage()) {
                    $localname = sprintf("%s-%s.%s", $book->getAuthor()->getShortName(), $book->getName(), $bookFile->getType());
                    $zipStream->addFileFromPath($localname, $bookFile->getName());
                }
            }
        }

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $response = new BinaryFileResponse($zipStream->finish());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }
}
