<?php

namespace AppBundle\Filter\DTO;

use AppBundle\Entity\Author;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Serie;
use AppBundle\Filter\FilterInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class BookFilter implements FilterInterface
{

    /**
     * @var string
     *
     * @Assert\Length(
     *      min = 2,
     *      minMessage = "filter.name_min",
     *      max = 100,
     *      maxMessage = "book.name_error"
     * )
     */
    private $name;

    /**
     * @var ArrayCollection
     */
    private $authors;

    /**
     * @var ArrayCollection
     */
    private $genres;

    /**
     * @var ArrayCollection
     */
    private $serie;

    /**
     * @var string
     */
    private $search;

    /**
     * @var \DateTime
     */
    private $createdAtStart;

    /**
     * @var \DateTime
     *
     * @Assert\Expression(
     *     "!this.getCreatedAtEnd() || this.getCreatedAtStart() <= this.getCreatedAtEnd()",
     *     message="book.date_error"
     * )
     */
    private $createdAtEnd;

    /**
     * @var boolean
     */
    private $mostPopular;

    /**
     * @var boolean
     */
    private $liked;

    /**
     * BookFilter constructor.
     */
    public function __construct()
    {
        $this->mostPopular = false;
        $this->liked = false;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name = null): void
    {
        $this->name = $name;
    }

    /**
     * @param Author $author
     */
    public function addAuthor(Author $author): void
    {
        $this->authors[] = $author;
    }

    /**
     * @param Author $author
     */
    public function removeAuthor(Author $author): void
    {
        $this->authors->removeElement($author);
    }

    /**
     * @return ArrayCollection|array|null
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param Genre $genre
     */
    public function addGenre(Genre $genre): void
    {
        $this->genres[] = $genre;
    }

    /**
     * @param Genre $genre
     */
    public function removeGenre($genre): void
    {
        $this->genres->removeElement($genre);
    }

    /**
     * @return ArrayCollection|array|null
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @return string
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @param string $search
     */
    public function setSearch(string $search = null): void
    {
        $this->search = $search;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAtStart(): ?DateTime
    {
        return $this->createdAtStart;
    }

    /**
     * @param DateTime $createdAtStart
     */
    public function setCreatedAtStart(DateTime $createdAtStart = null): void
    {
        $this->createdAtStart = $createdAtStart;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAtEnd(): ?DateTime
    {
        return $this->createdAtEnd;
    }

    /**
     * @param DateTime $createdAtEnd
     */
    public function setCreatedAtEnd(DateTime $createdAtEnd = null): void
    {
        $this->createdAtEnd = $createdAtEnd;
    }

    /**
     * @return boolean
     */
    public function getMostPopular(): bool
    {
        return $this->mostPopular;
    }

    /**
     * @param boolean $mostPopular
     */
    public function setMostPopular(bool $mostPopular): void
    {
        $this->mostPopular = $mostPopular;
    }

    /**
     * @param ArrayCollection $author
     */
    public function setAuthors($authors): void
    {
        $this->authors = $authors;
    }

    /**
     * @param ArrayCollection $genre
     */
    public function setGenres($genres): void
    {
        $this->genres = $genres;
    }

    /**
     * @param Serie $serie
     */
    public function addSerie(Serie $serie): void
    {
        $this->serie[] = $serie;
    }

    /**
     * @param Serie $serie
     */
    public function removeSerie($serie): void
    {
        $this->serie->removeElement($serie);
    }

    /**
     * @return ArrayCollection
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * @param ArrayCollection $serie
     */
    public function setSerie($serie): void
    {
        $this->serie = $serie;
    }

    /**
     * @return boolean
     */
    public function getLiked(): bool
    {
        return $this->liked;
    }

    /**
     * @param boolean $liked
     */
    public function setLiked(bool $liked): void
    {
        $this->liked = $liked;
    }
}
