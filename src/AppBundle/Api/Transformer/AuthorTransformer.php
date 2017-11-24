<?php

namespace AppBundle\Api\Transformer;

use AppBundle\Entity\Author;
use League\Fractal\TransformerAbstract;

class AuthorTransformer extends TransformerAbstract implements TransformerInterface
{
    /**
     * @param Author $author
     * @return array
     */
    public function transform(Author $author): array
    {
        return [
            'id' => $author->getId(),
            'firstName' => $author->getFirstName(),
            'lastName' => $author->getLastName(),
            'booksCount' => $author->getBooksCount()
        ];
    }
}
