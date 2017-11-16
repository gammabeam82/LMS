<?php

namespace AppBundle\Fractal;

use AppBundle\Entity\Book;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BookTransformer extends TransformerAbstract
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * BookTransformer constructor.
     * @param RouterInterface $router
     */
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
        return [
            'id' => $book->getId(),
            'name' => $book->getName(),
            'url' => $this->router->generate('books_view', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
        ];
    }
}
