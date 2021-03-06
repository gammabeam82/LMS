<?php

namespace AppBundle\Entity;

use AppBundle\Api\Request\Author\CreateAuthorRequest;
use AppBundle\Api\Request\Author\UpdateAuthorRequest;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AuthorRepository")
 * @ORM\Table(name="authors")
 */
class Author implements EntityInterface
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
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message = "blank")
     * @Assert\Length(
     *      min = 2,
     *      max = 40,
     *      minMessage = "author.first_name_min",
     *      maxMessage = "author.first_name_max"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message = "blank")
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "author.last_name_min",
     *      maxMessage = "author.last_name_max"
     * )
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Book", mappedBy="author", cascade={"all"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $books;

    /**
     * @var \Doctrine\Common\Collections\Collection|User[]
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="subscriptions")
     */
    private $subscribers;

    /**
     * Author constructor.
     *
     * @param string|null $firstName
     * @param string|null $lastName
     */
    public function __construct(string $firstName = null, string $lastName = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;

        $this->books = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
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
     * @return Author
     */
    public function setCreatedAt(DateTime $createdAt): Author
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Author
     */
    public function setFirstName(string $firstName): Author
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Author
     */
    public function setLastName(string $lastName): Author
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return sprintf("%s %s", $this->firstName, $this->lastName);
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return sprintf("%s. %s", mb_substr($this->getFirstName(), 0, 1), $this->lastName);
    }

    /**
     * @return string
     */
    public function getFirstLetter(): string
    {
        return mb_substr($this->getLastName(), 0, 1);
    }

    /**
     * Add book
     *
     * @param \AppBundle\Entity\Book $book
     *
     * @return Author
     */
    public function addBook(\AppBundle\Entity\Book $book): Author
    {
        $this->books[] = $book;

        return $this;
    }

    /**
     * Remove book
     *
     * @param \AppBundle\Entity\Book $book
     */
    public function removeBook(\AppBundle\Entity\Book $book): void
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
     * @param User $user
     */
    public function addSubscriber(User $user): void
    {
        if (false !== $this->subscribers->contains($user)) {
            return;
        }

        $this->subscribers->add($user);
        $user->addSubscription($this);
    }

    /**
     * @param User $user
     */
    public function removeSubscriber(User $user): void
    {
        if (false === $this->subscribers->contains($user)) {
            return;
        }

        $this->subscribers->removeElement($user);
        $user->removeSubscription($this);
    }

    /**
     * @return User[]|ArrayCollection|Collection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isSubscribed(User $user)
    {
        return $this->subscribers->contains($user);
    }

    /**
     * @param CreateAuthorRequest $dto
     *
     * @return Author
     */
    public static function createFromDTO(CreateAuthorRequest $dto): Author
    {
        return new self($dto->firstName, $dto->lastName);
    }

    /**
     * @param UpdateAuthorRequest $dto
     *
     * @return Author
     */
    public function update(UpdateAuthorRequest $dto): Author
    {
        $this->setFirstName($dto->firstName);
        $this->setLastName($dto->lastName);

        return $this;
    }
}
