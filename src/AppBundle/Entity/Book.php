<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BookRepository")
 * @ORM\Table(name="books")
 */
class Book implements EntityInterface
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
	 *      groups={"edit"},
	 *      min = 1,
	 *      max = 100,
	 *      minMessage = "book.name_min",
	 *      maxMessage = "book.name_max"
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
	 * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Serie", inversedBy="books")
	 * @ORM\JoinColumn(name="serie_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
	 */
	private $serie;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $views;

	/**
	 * @ORM\Column(type="string", length=2000, nullable=true)
	 * @Assert\Length(
	 *      max = 1000,
	 *      maxMessage = "annotation.message_max"
	 * )
	 */
	private $annotation;

	/**
	 * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Rating", mappedBy="book")
	 */
	private $ratings;

	/**
	 * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Comment", mappedBy="book")
	 */
	private $comments;

	/**
	 * @ORM\OneToMany(targetEntity="\AppBundle\Entity\File", mappedBy="book", cascade={"persist", "remove"})
	 */
	private $bookFiles;

	/**
	 * @var \Doctrine\Common\Collections\Collection|User[]
	 *
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="likes")
	 */
	private $users;

	/**
	 * Book constructor.
	 */
	public function __construct()
	{
		$this->ratings = new ArrayCollection();
		$this->comments = new ArrayCollection();
		$this->bookFiles = new ArrayCollection();
		$this->users = new ArrayCollection();
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return sprintf("%s-%s", $this->getAuthor()->getShortName(), $this->name);
	}

	/**
	 * @return float|int|string
	 */
	public function getAverageRating()
	{
		if (0 !== count($this->ratings)) {
			$sum = 0;
			foreach ($this->ratings as $rating) {
				$sum += $rating->getValue();
			}
			return round(($sum / count($this->ratings)), 2);
		}

		return "-";
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
	 * Add comment
	 *
	 * @param \AppBundle\Entity\Comment $comment
	 *
	 * @return Book
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
	 * Set annotation
	 *
	 * @param string $annotation
	 *
	 * @return Book
	 */
	public function setAnnotation($annotation)
	{
		$this->annotation = $annotation;

		return $this;
	}

	/**
	 * Get annotation
	 *
	 * @return string
	 */
	public function getAnnotation()
	{
		return $this->annotation;
	}

	/**
	 * Set serie
	 *
	 * @param \AppBundle\Entity\Serie $serie
	 *
	 * @return Book
	 */
	public function setSerie(\AppBundle\Entity\Serie $serie = null)
	{
		$this->serie = $serie;

		return $this;
	}

	/**
	 * Get serie
	 *
	 * @return \AppBundle\Entity\Serie
	 */
	public function getSerie()
	{
		return $this->serie;
	}

	public function incViews()
	{
		$this->views++;
	}

	/**
	 * Add bookFile
	 *
	 * @param \AppBundle\Entity\File $bookFile
	 *
	 * @return Book
	 */
	public function addBookFile(\AppBundle\Entity\File $bookFile)
	{
		$this->bookFiles[] = $bookFile;

		$bookFile->setBook($this);

		return $this;
	}

	/**
	 * Remove bookFile
	 *
	 * @param \AppBundle\Entity\File $bookFile
	 */
	public function removeBookFile(\AppBundle\Entity\File $bookFile)
	{
		$this->bookFiles->removeElement($bookFile);
	}

	/**
	 * Get bookFiles
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getBookFiles()
	{
		return $this->bookFiles;
	}

	/**
	 * @param  $bookFiles
	 */
	public function setBookFiles($bookFiles)
	{
		$this->bookFiles = $bookFiles;
	}

	/**
	 * @param User $user
	 */
	public function addUser(User $user)
	{
		if (false !== $this->users->contains($user)) {
			return;
		}

		$this->users->add($user);
		$user->addLike($this);
	}

	/**
	 * @param User $user
	 */
	public function removeUser(User $user)
	{
		if (false === $this->users->contains($user)) {
			return;
		}

		$this->users->removeElement($user);
		$user->removeLike($this);
	}

	/**
	 * @return User[]|ArrayCollection|\Doctrine\Common\Collections\Collection
	 */
	public function getUsers()
	{
		return $this->users;
	}


	/**
	 * @param User $user
	 * @return bool
	 */
	public function isLikedBy(User $user)
	{
		return false !== $this->users->contains($user);
	}

	/**
	 * @return array
	 */
	public function getImages()
	{
		return array_filter($this->getBookFiles()->toArray(), function ($file) {
			/** @var File $file */
			return false !== $file->getIsImage();
		});
	}
}
