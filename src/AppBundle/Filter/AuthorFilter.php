<?php

namespace AppBundle\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class AuthorFilter
{

	/**
	 * @var string
	 *
	 * @Assert\Length(
	 *      max = 100,
	 *      maxMessage = "author.last_name_max"
	 * )
	 */
	private $lastName;

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

}
