<?php

namespace AppBundle\Api\Transformer;

use AppBundle\Entity\Serie;
use League\Fractal\TransformerAbstract;

class SerieTransformer extends TransformerAbstract implements TransformerInterface
{
    /**
     * @param Serie $serie
     *
     * @return array
     */
    public function transform(Serie $serie): array
    {
        return [
            'id' => $serie->getId(),
            'name' => $serie->getName(),
            'booksCount' => $serie->getBooksCount()
        ];
    }
}
