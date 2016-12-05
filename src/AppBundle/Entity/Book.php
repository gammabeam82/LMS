<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="books")
 */
class Book
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
	 * @ORM\Column(type="string", length=200)
	 * @Assert\NotBlank(groups={"edit"})
	 * @Assert\Length(
	 *     	groups={"edit"},
	 *      min = 1,
	 *      max = 100,
	 *      minMessage = "Название должно содержать хотя бы {{ limit }} символ",
	 *      maxMessage = "Название не должно быть длинее {{ limit }} символов"
	 * )
	 */
	private $name;

	/**
	 * @ORM\ManyToOne(targetEntity="Author", inversedBy="books")
	 * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
	 */
	private $author;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="books")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 */
	private $addedBy;

	/**
	 * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Genre", inversedBy="books")
	 * @ORM\JoinColumn(name="genre_id", referencedColumnName="id", nullable=true)
	 */
	private $genre;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 *
	 * @Assert\NotBlank(message="Пожалуйста, выберите текстовый файл")
	 * @Assert\File(
	 *     mimeTypes={ "text/plain" }
	 *	 )
	 */
	private $file;

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
     * @return Book
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
     * @return Book
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
     * @param \AppBundle\Entity\Author $author
     *
     * @return Book
     */
    public function setAuthor(\AppBundle\Entity\Author $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set addedBy
     *
     * @param \AppBundle\Entity\User $addedBy
     *
     * @return Book
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
     * Set genre
     *
     * @param \AppBundle\Entity\Genre $genre
     *
     * @return Book
     */
    public function setGenre(\AppBundle\Entity\Genre $genre = null)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return \AppBundle\Entity\Genre
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set file
     *
     * @param string $file
     *
     * @return Book
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
}
