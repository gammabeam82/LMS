<?php

namespace AppBundle\Api\Transformer;

use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Serie;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class BookTransformer extends TransformerAbstract implements TransformerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * BookTransformer constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getDefaultIncludes(): array
    {
        return ['author', 'genre', 'serie', 'textFiles'];
    }

    /**
     * @param Book $book
     *
     * @return array
     */
    public function transform(Book $book): array
    {
        $data = [
            'id' => $book->getId(),
            'name' => $book->getName(),
            'createdAt' => $book->getCreatedAt()->getTimestamp(),
            'url' => $this->router->generate('books_view', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        return $data;
    }

    /**
     * @param Book $book
     *
     * @return Item
     */
    public function includeAuthor(Book $book): Item
    {
        return new Item($book->getAuthor(), new AuthorTransformer());
    }

    /**
     * @param Book $book
     *
     * @return Collection
     */
    public function includeTextFiles(Book $book): Collection
    {
        return new Collection($book->getTextFiles(), new FileTransformer($this->router));
    }

    /**
     * @param Book $book
     *
     * @return Item
     */
    public function includeGenre(Book $book): Item
    {
        $genre = $book->getGenre() ?? new Genre();

        return new Item($genre, new GenreTransformer());
    }

    /**
     * @param Book $book
     *
     * @return Item
     */
    public function includeSerie(Book $book): Item
    {
        $serie = $book->getSerie() ?? new Serie();

        return new Item($serie, new SerieTransformer());
    }
}
