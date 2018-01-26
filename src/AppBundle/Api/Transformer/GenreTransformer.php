<?php

namespace AppBundle\Api\Transformer;

use AppBundle\Entity\Genre;
use League\Fractal\TransformerAbstract;

class GenreTransformer extends TransformerAbstract implements TransformerInterface
{
    /**
     * @param Genre $genre
     *
     * @return array
     */
    public function transform(Genre $genre): array
    {
        return [
            'id' => $genre->getId(),
            'name' => $genre->getName(),
            'booksCount' => $genre->getBooksCount()
        ];
    }
}
