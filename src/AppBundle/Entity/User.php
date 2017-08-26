<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class User extends BaseUser
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
	 * @Gedmo\Timestampable(on="update")
	 * @ORM\Column(type="datetime")
	 */
	private $updatedAt;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $deletedAt;

	/**
	 * @ORM\OneToMany(targetEntity="Book", mappedBy="addedBy")
	 */
	private $books;

	/**
	 * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Rating", mappedBy="user")
	 */
	private $ratings;

	/**
	 * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Comment", mappedBy="user")
	 * @ORM\OrderBy({"postedAt" = "DESC"})
	 */
	private $comments;

	/**
	 * @var \Doctrine\Common\Collections\Collection|Book[]
	 *
	 * @ORM\ManyToMany(targetEntity="Book", inversedBy="users")
	 * @ORM\JoinTable(
	 *  name="user_likes",
	 *  joinColumns={
	 *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *  },
	 *  inverseJoinColumns={
	 *      @ORM\JoinColumn(name="book_id", referencedColumnName="id")
	 *  }
	 * )
	 */
	private $likes;

	public function __construct()
	{
		parent::__construct();

		$this->books = new ArrayCollection();
		$this->ratings = new ArrayCollection();
		$this->comments = new ArrayCollection();
		$this->likes = new ArrayCollection();
	}

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return User
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Add book
     *
     * @param \AppBundle\Entity\Book $book
     *
     * @return User
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
     * Add rating
     *
     * @param \AppBundle\Entity\Rating $rating
     *
     * @return User
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
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return User
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

	/**
	 * @param Book $book
	 */
	public function addLike(Book $book)
	{
		if (false !== $this->likes->contains($book)) {
			return;
		}

		$this->likes->add($book);
		$book->addUser($this);
	}

	/**
	 * @param Book $book
	 */
	public function removeLike(Book $book)
	{
		if (false === $this->likes->contains($book)) {
			return;
		}

		$this->likes->removeElement($book);
		$book->removeUser($this);
	}

	/**
	 * @return Book[]|ArrayCollection|\Doctrine\Common\Collections\Collection
	 */
	public function getLikes()
	{
		return $this->likes;
	}
}
