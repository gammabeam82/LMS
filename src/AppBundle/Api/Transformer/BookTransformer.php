<?php

namespace AppBundle\Api\Transformer;

use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Serie;
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
        $genre = $book->getGenre();
        $serie = $book->getSerie();

        $authorTransformer = new AuthorTransformer();

        $data = [
            'id' => $book->getId(),
            'name' => $book->getName(),
            'createdAt' => $book->getCreatedAt()->getTimestamp(),
            'url' => $this->router->generate('books_view', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'author' => $authorTransformer->transform($book->getAuthor())
        ];

        $data['genre'] = null;
        $data['serie'] = null;

        if (false !== $genre instanceof Genre) {
            $genreTransformer = new GenreTransformer();
            $data['genre'] = $genreTransformer->transform($genre);
        }

        if (false !== $serie instanceof Serie) {
            $serieTransformer = new SerieTransformer();
            $data['serie'] = $serieTransformer->transform($serie);
        }

        return $data;
    }
}
