<?php

namespace AppBundle\Filter;

use Symfony\Component\Validator\Constraints as Assert;

abstract class EntityFilter implements EntityFilterInterface
{

	/**
	 * @var string
	 *
	 * @Assert\Length(
	 *      min = 2,
	 *      minMessage = "filter.name_min",
	 *      max = 100,
	 *      minMessage = "filter.name_max",
	 * )
	 */
	private $name;

	/**
	 * @var boolean
	 */
	private $sortByName;

	/**
	 * EntityFilter constructor.
	 */
	public function __construct()
	{
		$this->sortByName = false;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return bool
	 */
	public function getSortByName()
	{
		return $this->sortByName;
	}

	/**
	 * @param $sortByName
	 */
	public function setSortByName($sortByName)
	{
		$this->sortByName = $sortByName;
	}
}
