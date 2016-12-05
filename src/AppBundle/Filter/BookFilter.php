<?php

namespace AppBundle\Filter;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

class BookFilter
{

	/**
	 * @var string
	 *
	 * @Assert\Length(
	 *      max = 100,
	 *      maxMessage = "Название не должно быть длинее {{ limit }} символов"
	 * )
	 */
	private $name;

	/**
	 * @var string
	 */
	private $author;

	/**
	 * @var string
	 */
	private $genre;



	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return BookFilter
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set author
	 *
	 * @param string $author
	 *
	 * @return BookFilter
	 */
	public function setAuthor($author)
	{
		$this->author = $author;

		return $this;
	}

	/**
	 * Get author
	 *
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * Set genre
	 *
	 * @param string $genre
	 *
	 * @return BookFilter
	 */
	public function setGenre($genre)
	{
		$this->genre = $genre;

		return $this;
	}

	/**
	 * Get genre
	 *
	 * @return string
	 */
	public function getGenre()
	{
		return $this->genre;
	}

}
