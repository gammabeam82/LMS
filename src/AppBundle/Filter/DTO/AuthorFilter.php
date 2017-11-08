<?php

namespace AppBundle\Filter\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Filter\FilterInterface;

class AuthorFilter implements FilterInterface
{

    /**
     * @var string
     *
     * @Assert\Length(
     *        min = 2,
     *      minMessage = "filter.name_min",
     *      max = 100,
     *      maxMessage = "author.last_name_max"
     * )
     */
    private $lastName;

    /**
     * @var boolean
     */
    private $sortByName;

    /**
     * @var string
     *
     * @Assert\Length(
     *     min = 1,
     *     max = 1
     * )
     */
    private $firstLetter;

    /**
     * AuthorFilter constructor.
     */
    public function __construct()
    {
        $this->sortByName = false;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName = null): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return bool
     */
    public function getSortByName(): bool
    {
        return $this->sortByName;
    }

    /**
     * @param bool $sortByName
     */
    public function setSortByName(bool $sortByName): void
    {
        $this->sortByName = $sortByName;
    }

    /**
     * @return string
     */
    public function getFirstLetter(): ?string
    {
        return $this->firstLetter;
    }

    /**
     * @param string $letter
     */
    public function setFirstLetter(string $letter = null): void
    {
        $this->firstLetter = $letter;
    }
}
