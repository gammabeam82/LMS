<?php

namespace AppBundle\Filter;

use AppBundle\Entity\Serie;
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
	 *      maxMessage = "book.name_error"
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
	 * @var ArrayCollection
	 */
	private $serie;

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
	 * @Assert\Expression(
	 *     "!this.getCreatedAtEnd() || this.getCreatedAtStart() <= this.getCreatedAtEnd()",
	 *     message="book.date_error"
	 * )
	 */
	private $createdAtEnd;

	/**
	 * @var boolean
	 */
	private $mostPopular;

	/**
	 * BookFilter constructor.
	 */
	public function __construct()
	{
		$this->mostPopular = false;
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

	/**
	 * @param ArrayCollection $author
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}

	/**
	 * @param ArrayCollection $genre
	 */
	public function setGenre($genre)
	{
		$this->genre = $genre;
	}

	/**
	 * @param Serie $serie
	 */
	public function addSerie(Serie $serie)
	{
		$this->serie[] = $serie;

	}

	/**
	 * @param Serie $serie
	 */
	public function removeSerie($serie)
	{
		$this->serie->removeElement($serie);
	}

	/**
	 * @return ArrayCollection
	 */
	public function getSerie()
	{
		return $this->serie;
	}

	/**
	 * @param ArrayCollection $serie
	 */
	public function setSerie($serie)
	{
		$this->serie = $serie;
	}

}
