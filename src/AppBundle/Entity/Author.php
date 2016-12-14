<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="authors")
 */
class Author
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
	 * @ORM\Column(type="string", length=100)
	 * @Assert\NotBlank()
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 40,
	 *      minMessage = "author.first_name_min",
	 *      maxMessage = "author.first_name_max"
	 * )
	 */
	private $firstName;

	/**
	 * @ORM\Column(type="string", length=100)
	 * @Assert\NotBlank()
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 100,
	 *      minMessage = "author.last_name_min",
	 *      maxMessage = "author.last_name_max"
	 * )
	 */
	private $lastName;

	/**
	 * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Book", mappedBy="author", cascade={"all"})
	 */
	private $books;

	/**
	 * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\User", inversedBy="authors")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 */
	private $addedBy;


	public function __construct()
	{
		$this->books = new ArrayCollection();
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
     * @return Author
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Author
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Author
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set addedBy
     *
     * @param \AppBundle\Entity\User $addedBy
     *
     * @return Author
     */
    public function setAddedBy(\AppBundle\Entity\User $addedBy)
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    /**
     * Get addedBy
     *
     * @return \AppBundle\Entity\User
     */
    public function getAddedBy()
    {
        return $this->addedBy;
    }

	/**
	 * @return string
	 */
    public function getFullName()
	{
		return "{$this->firstName} {$this->lastName}";
	}

	/**
	 * @return string
	 */
	public function getShortName()
	{
		return mb_substr($this->getFirstName(), 0, 1).". {$this->lastName}";
	}

	/**
	 * Add book
	 *
	 * @param \AppBundle\Entity\Book $book
	 *
	 * @return Author
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

}
