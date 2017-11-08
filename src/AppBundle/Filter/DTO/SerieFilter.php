<?php

namespace AppBundle\Filter\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Filter\FilterInterface;

class SerieFilter implements FilterInterface
{

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
     * SerieFilter constructor.
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
    public function setName(string $name = null)
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
     * @param $sortByName
     */
    public function setSortByName(bool $sortByName): void
    {
        $this->sortByName = $sortByName;
    }
}
