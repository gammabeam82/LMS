<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use \DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SerieRepository")
 * @ORM\Table(name="series")
 * @UniqueEntity(
 *     fields={"name"},
 *     message="serie.unique"
 * )
 */
class Serie implements EntityInterface
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
     * @ORM\Column(type="string", length=200, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "serie.name_min",
     *      maxMessage = "serie.name_max"
     * )
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Book", mappedBy="serie")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $books;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @return Serie
     */
    public function setCreatedAt(DateTime $createdAt): Serie
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
     * @return Serie
     */
    public function setName(string $name): Serie
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
     * @return Serie
     */
    public function addBook(Book $book): Serie
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
    public function getBooksCount(): ?int
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
}
