<?php

namespace AppBundle\Filter;

use Doctrine\Common\Collections\ArrayCollection;
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
	 * @var string
	 */
	private $search;

	/**
	 * @var \DateTime
	 */
	private $createdAtStart;

	/**
	 * @var \DateTime
	 *
	 * @Assert\Expression("!this.getCreatedAtEnd() || this.getCreatedAtStart() <= this.getCreatedAtEnd()", message="Дата окончания должна быть позже или равна дате начала.")
	 */
	private $createdAtEnd;

	/**
	 * @var boolean
	 */
	private $mostPopular;

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

	/**
	 * @return string
	 */
	public function getSearch()
	{
		return $this->search;
	}

	/**
	 * @param string $search
	 */
	public function setSearch($search)
	{
		$this->search = $search;
	}

	/**
 	* @return \DateTime
 	*/
	public function getCreatedAtStart()
	{
		return $this->createdAtStart;
	}

	/**
	 * @param \DateTime $createdAtStart
	 */
	public function setCreatedAtStart($createdAtStart)
	{
		$this->createdAtStart = $createdAtStart;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAtEnd()
	{
		return $this->createdAtEnd;
	}

	/**
	 * @param \DateTime $createdAtEnd
	 */
	public function setCreatedAtEnd($createdAtEnd)
	{
		$this->createdAtEnd = $createdAtEnd;
	}

	/**
	 * @return boolean
	 */
	public function getMostPopular()
	{
		return $this->mostPopular;
	}

	/**
	 * @param boolean $mostPopular
	 */
	public function setMostPopular($mostPopular)
	{
		$this->mostPopular = $mostPopular;
	}

}
