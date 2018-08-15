<?php

namespace AppBundle\Api\Request\Author;

use AppBundle\Api\Request\RequestObject;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateAuthorRequest extends RequestObject
{
    /**
     * @Assert\NotBlank(message = "blank")
     * @Assert\Length(
     *      min = 2,
     *      max = 40,
     *      minMessage = "author.first_name_min",
     *      maxMessage = "author.first_name_max"
     * )
     */
    public $firstName;

    /**
     * @Assert\NotBlank(message = "blank")
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "author.last_name_min",
     *      maxMessage = "author.last_name_max"
     * )
     */
    public $lastName;
}
