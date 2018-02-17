<?php

namespace AppBundle\Factory;

use AppBundle\Entity\Book;
use AppBundle\Entity\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class BookFileFactory
{
    /**
     * @param UploadedFile $uploadedFile
     * @param Book $book
     * @param string $path
     *
     * @return File
     */
    public static function get(UploadedFile $uploadedFile, Book $book, string $path): File
    {
        $type = $uploadedFile->guessExtension();
        $mimeType = $uploadedFile->getMimeType();
        $filename = sprintf(
            "%s-%s.%s",
            preg_replace('/\s+/', '_', $book->getName()),
            uniqid(),
            $type
        );

        $bookFile = new File();

        $bookFile->setBook($book)
            ->setType($type)
            ->setMimeType($mimeType)
            ->setSize($uploadedFile->getSize())
            ->setBasename($filename)
            ->setName(sprintf("%s/%s", $path, $filename));

        return $bookFile;
    }
}
