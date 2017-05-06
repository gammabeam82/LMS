<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="ratings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RatingRepository")
 */
class Rating
{
	//TODO define constants

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="ratings")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
	 */
	private $user;

	/**
	 * @ORM\ManyToOne(targetEntity="Book", inversedBy="ratings")
	 * @ORM\JoinColumn(name="book_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
	 */
	private $book;

	/**
	 * @ORM\Column(type="integer")
	 * @Assert\Expression("this.getValue() >= 1 && this.getValue() <= 5")
	 */
	private $value;

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
     * Set value
     *
     * @param integer $value
     *
     * @return Rating
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Rating
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set book
     *
     * @param Book $book
     *
     * @return Rating
     */
    public function setBook(Book $book)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return Book
     */
    public function getBook()
    {
        return $this->book;
    }
}
