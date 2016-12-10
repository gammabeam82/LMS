<?php

namespace AppBundle\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class AuthorFilter
{

	/**
	 * @var string
	 *
	 * @Assert\Length(
	 *      max = 50,
	 *      maxMessage = "Фамилия не должна быть длинее {{ limit }} символов"
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
