<?php

namespace AppBundle\Api\Transformer;

use AppBundle\Entity\Book;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BookTransformer extends TransformerAbstract implements TransformerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param Book $book
     * @return array
     */
    public function transform(Book $book): array
    {
        $author = $book->getAuthor();
        $genre = $book->getGenre();

        return [
            'id' => $book->getId(),
            'name' => $book->getName(),
            'createdAt' => $book->getCreatedAt()->getTimestamp(),
            'url' => $this->router->generate('books_view', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'author' => [
                'id' => $author->getId(),
                'firstName' => $author->getFirstName(),
                'lastName' => $author->getLastName()
            ],
            'genre' => [
                'id' => $genre->getId(),
                'name' => $genre->getName()
            ],
        ];
    }
}
