<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use \DateTime;

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
	private $id;

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
	public function __toString(): string
	{
		return $this->name;
	}

	/**
	 * @return float
	 */
	public function getAverageRating(): float
	{
		if (0 !== count($this->ratings)) {
			$sum = 0;
			foreach ($this->ratings as $rating) {
				$sum += $rating->getValue();
			}
			return round(($sum / count($this->ratings)), 2);
		}

		return 0;
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * Set createdAt
	 *
	 * @param DateTime $createdAt
	 *
	 * @return Book
	 */
	public function setCreatedAt(DateTime $createdAt): Book
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
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Book
	 */
	public function setName(string $name): Book
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * Set author
	 *
	 * @param Author $author
	 *
	 * @return Book
	 */
	public function setAuthor(Author $author): Book
	{
		$this->author = $author;

		return $this;
	}

	/**
	 * Get author
	 *
	 * @return Author
	 */
	public function getAuthor(): ?Author
	{
		return $this->author;
	}

	/**
	 * Set addedBy
	 *
	 * @param User $addedBy
	 *
	 * @return Book
	 */
	public function setAddedBy(User $addedBy): Book
	{
		$this->addedBy = $addedBy;

		return $this;
	}

	/**
	 * Get addedBy
	 *
	 * @return User
	 */
	public function getAddedBy(): User
	{
		return $this->addedBy;
	}

	/**
	 * Set genre
	 *
	 * @param Genre $genre
	 *
	 * @return Book
	 */
	public function setGenre(Genre $genre = null): Book
	{
		$this->genre = $genre;

		return $this;
	}

	/**
	 * Get genre
	 *
	 * @return Genre
	 */
	public function getGenre(): ?Genre
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
	public function setViews(int $views): Book
	{
		$this->views = $views;

		return $this;
	}

	/**
	 * Get views
	 *
	 * @return integer
	 */
	public function getViews(): int
	{
		return $this->views;
	}

	/**
	 * Add rating
	 *
	 * @param Rating $rating
	 *
	 * @return Book
	 */
	public function addRating(Rating $rating): Book
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
	 * @return Book
	 */
	public function addComment(Comment $comment): Book
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
	 * Set annotation
	 *
	 * @param string $annotation
	 *
	 * @return Book
	 */
	public function setAnnotation(string $annotation): Book
	{
		$this->annotation = $annotation;

		return $this;
	}

	/**
	 * Get annotation
	 *
	 * @return string
	 */
	public function getAnnotation(): ?string
	{
		return $this->annotation;
	}

	/**
	 * Set serie
	 *
	 * @param Serie $serie
	 *
	 * @return Book
	 */
	public function setSerie(Serie $serie = null): Book
	{
		$this->serie = $serie;

		return $this;
	}

	/**
	 * Get serie
	 *
	 * @return Serie
	 */
	public function getSerie(): ?Serie
	{
		return $this->serie;
	}

	public function incViews(): void
	{
		$this->views++;
	}

	/**
	 * Add bookFile
	 *
	 * @param File $bookFile
	 *
	 * @return Book
	 */
	public function addBookFile(File $bookFile): Book
	{
		$this->bookFiles[] = $bookFile;

		$bookFile->setBook($this);

		return $this;
	}

	/**
	 * Remove bookFile
	 *
	 * @param File $bookFile
	 */
	public function removeBookFile(File $bookFile): void
	{
		$this->bookFiles->removeElement($bookFile);
	}

	/**
	 * Get bookFiles
	 *
	 * @return Collection
	 */
	public function getBookFiles(): Collection
	{
		return $this->bookFiles;
	}

	/**
	 * @param Collection $bookFiles
	 */
	public function setBookFiles($bookFiles): void
	{
		$this->bookFiles = $bookFiles;
	}

	/**
	 * @param User $user
	 */
	public function addUser(User $user): void
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
	public function removeUser(User $user): void
	{
		if (false === $this->users->contains($user)) {
			return;
		}

		$this->users->removeElement($user);
		$user->removeLike($this);
	}

	/**
	 * @return ArrayCollection
	 */
	public function getUsers(): ArrayCollection
	{
		return $this->users;
	}


	/**
	 * @param User $user
	 * @return bool
	 */
	public function isLikedBy(User $user): bool
	{
		return false !== $this->users->contains($user);
	}

	/**
	 * @return array
	 */
	public function getImages(): array
	{
		return array_filter($this->getBookFiles()->toArray(), function ($file) {
			/** @var File $file */
			return false !== $file->getIsImage();
		});
	}
}
