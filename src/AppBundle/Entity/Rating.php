<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use LogicException;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RatingRepository")
 * @ORM\Table(name="ratings")
 */
class Rating
{
	const VALUE_1 = 1;
	const VALUE_2 = 2;
	const VALUE_3 = 3;
	const VALUE_4 = 4;
	const VALUE_5 = 5;

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
    	if(false === in_array($value, [self::VALUE_1, self::VALUE_2, self::VALUE_3, self::VALUE_4, self::VALUE_5])) {
    		throw new LogicException();
		}

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
