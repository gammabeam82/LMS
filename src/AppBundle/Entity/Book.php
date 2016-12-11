<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
	 */
	private $addedBy;

	/**
	 * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Genre", inversedBy="books")
	 * @ORM\JoinColumn(name="genre_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
	 */
	private $genre;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $views;

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
	 * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Rating", mappedBy="book")
	 */
	private $ratings;

	/**
	 * Book constructor.
	 */
	public function __construct()
	{
		$this->ratings = new ArrayCollection();
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

    /**
     * Set views
     *
     * @param integer $views
     *
     * @return Book
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add rating
     *
     * @param \AppBundle\Entity\Rating $rating
     *
     * @return Book
     */
    public function addRating(\AppBundle\Entity\Rating $rating)
    {
        $this->ratings[] = $rating;

        return $this;
    }

    /**
     * Remove rating
     *
     * @param \AppBundle\Entity\Rating $rating
     */
    public function removeRating(\AppBundle\Entity\Rating $rating)
    {
        $this->ratings->removeElement($rating);
    }

    /**
     * Get ratings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

	/**
	 * @return float|int|string
	 */
    public function getAverageRating()
	{
		if(0 !== count($this->ratings)) {
			$sum = 0;
			foreach($this->ratings as $rating){
				$sum += $rating->getValue();
			}
			return round(($sum / count($this->ratings)), 2);
		}

		return "-";
	}
}
