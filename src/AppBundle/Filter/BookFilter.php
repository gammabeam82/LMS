<?php

namespace AppBundle\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Author;
use AppBundle\Entity\Genre;

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
	 * @var ArrayCollection
	 */
	private $author;

	/**
	 * @var ArrayCollection
	 */
	private $genre;

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
	 * @param Author $author
	 */
	public function addAuthor(Author $author)
	{
		$this->author[] = $author;

	}

	/**
	 * @param Author $author
	 */
	public function removeAuthor($author)
	{
		$this->author->removeElement($author);
	}

	/**
	 * @return ArrayCollection
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param Genre $genre
	 */
	public function addGenre(Genre $genre)
	{
		$this->genre[] = $genre;

	}

	/**
	 * @param Genre $genre
	 */
	public function removeGenre($genre)
	{
		$this->genre->removeElement($genre);
	}

	/**
	 * @return ArrayCollection
	 */
	public function getGenre()
	{
		return $this->genre;
	}
}
