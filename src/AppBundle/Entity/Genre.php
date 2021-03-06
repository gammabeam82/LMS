<?php

namespace AppBundle\Entity;

use AppBundle\Api\Request\Genre\CreateGenreRequest;
use AppBundle\Api\Request\Genre\UpdateGenreRequest;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

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
    private $id;

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
     * Genre constructor.
     *
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        $this->name = $name;
        $this->books = new ArrayCollection();
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
     * @return Genre
     */
    public function setCreatedAt(DateTime $createdAt): Genre
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
     * @return Genre
     */
    public function setName(string $name): Genre
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
     * Add book
     *
     * @param Book $book
     *
     * @return Genre
     */
    public function addBook(Book $book): Genre
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
     * @return int
     */
    public function getBooksCount(): int
    {
        return count($this->getBooks());
    }

    /**
     * @return bool
     */
    public function isDeletable(): bool
    {
        return count($this->getBooks()) < 1;
    }

    /**
     * @param CreateGenreRequest $dto
     *
     * @return Genre
     */
    public static function createFromDTO(CreateGenreRequest $dto): Genre
    {
        return new self($dto->name);
    }

    /**
     * @param UpdateGenreRequest $dto
     *
     * @return Genre
     */
    public function update(UpdateGenreRequest $dto): Genre
    {
        $this->setName($dto->name);

        return $this;
    }
}
