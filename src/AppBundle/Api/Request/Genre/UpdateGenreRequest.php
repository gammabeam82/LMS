<?php

namespace AppBundle\Api\Request\Genre;

use AppBundle\Api\Request\RequestObject;
use AppBundle\Api\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateGenreRequest extends RequestObject
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message = "blank")
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "genre.name_min",
     *      maxMessage = "genre.name_max"
     * )
     * @AppAssert\UniqueValue(
     *     entityClass = "AppBundle\Entity\Genre",
     *     field = "name"
     * )
     */
    public $name;
}
