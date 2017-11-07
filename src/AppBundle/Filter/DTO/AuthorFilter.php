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
	 *     	min = 2,
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
	public function getLastName()
	{
		return $this->lastName;
	}

	/**
	 * @param string $lastName
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
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

	/**
	 * @return string
	 */
	public function getFirstLetter()
	{
		return $this->firstLetter;
	}

	/**
	 * @param string $letter
	 */
	public function setFirstLetter($letter)
	{
		$this->firstLetter = $letter;
	}
}