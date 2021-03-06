<?php

namespace AppBundle\Filter\DTO;

use AppBundle\Filter\FilterInterface;
use AppBundle\Utils\PopulateFromArrayTrait;
use Symfony\Component\Validator\Constraints as Assert;

class GenreFilter implements FilterInterface
{
    use PopulateFromArrayTrait;

    /**
     * @var string
     *
     * @Assert\Length(
     *      min = 2,
     *      minMessage = "filter.name_min",
     *      max = 100,
     *      maxMessage = "filter.name_max",
     * )
     */
    private $name;

    /**
     * @var boolean
     */
    private $sortByName;

    /**
     * GenreFilter constructor.
     */
    public function __construct()
    {
        $this->sortByName = false;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name = null): void
    {
        $this->name = $name;
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
    public function setSortByName(bool $sortByName)
    {
        $this->sortByName = $sortByName;
    }
}
