<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use \DateTime;

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

    /**
     * User constructor.
     */
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
     * @param DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt(DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param DateTime $updatedAt
     *
     * @return User
     */
    public function setUpdatedAt(DateTime $updatedAt): User
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param DateTime $deletedAt
     *
     * @return User
     */
    public function setDeletedAt(DateTime $deletedAt): User
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return DateTime
     */
    public function getDeletedAt(): DateTime
    {
        return $this->deletedAt;
    }

    /**
     * Add book
     *
     * @param Book $book
     *
     * @return User
     */
    public function addBook(Book $book)
    {
        $this->books[] = $book;

        return $this;
    }

    /**
     * Remove book
     *
     * @param Book $book
     */
    public function removeBook(Book $book): void
    {
        $this->books->removeElement($book);
    }

    /**
     * Get books
     *
     * @return Collection
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * Add rating
     *
     * @param Rating $rating
     *
     * @return User
     */
    public function addRating(Rating $rating): User
    {
        $this->ratings[] = $rating;

        return $this;
    }

    /**
     * Remove rating
     *
     * @param Rating $rating
     */
    public function removeRating(Rating $rating): void
    {
        $this->ratings->removeElement($rating);
    }

    /**
     * Get ratings
     *
     * @return Collection
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return User
     */
    public function addComment(Comment $comment): User
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment): void
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Book $book
     */
    public function addLike(Book $book): void
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
    public function removeLike(Book $book): void
    {
        if (false === $this->likes->contains($book)) {
            return;
        }

        $this->likes->removeElement($book);
        $book->removeUser($this);
    }

    /**
     * @return Collection
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }
}
