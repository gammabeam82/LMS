<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GenreRepository")
 * @ORM\Table(name="genres")
 * @UniqueEntity(
 *     fields={"name"},
 *     message="genre.unique"
 * )
 */
class Genre implements EntityInterface
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @Gedmo\Timestampable(on="create")
	 * @ORM\Column(type="datetime")
	 */
	private $createdAt;

	/**
	 * @ORM\Column(type="string", length=100, unique=true)
	 * @Assert\NotBlank(message = "blank")
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 50,
	 *      minMessage = "genre.name_min",
	 *      maxMessage = "genre.name_max"
	 * )
	 */
	private $name;

	/**
	 * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Book", mappedBy="genre")
	 * @ORM\OrderBy({"name" = "ASC"})
	 */
	private $books;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->books = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Genre
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Genre
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
     * Add book
     *
     * @param \AppBundle\Entity\Book $book
     *
     * @return Genre
     */
    public function addBook(\AppBundle\Entity\Book $book)
    {
        $this->books[] = $book;

        return $this;
    }

    /**
     * Remove book
     *
     * @param \AppBundle\Entity\Book $book
     */
    public function removeBook(\AppBundle\Entity\Book $book)
    {
        $this->books->removeElement($book);
    }

    /**
     * Get books
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBooks()
    {
        return $this->books;
    }

	/**
	 * @return int
	 */
	public function getBooksCount()
	{
		return count($this->getBooks());
	}

	/**
	 * @return bool
	 */
	public function isDeletable()
	{
		return count($this->getBooks()) < 1;
	}
}
