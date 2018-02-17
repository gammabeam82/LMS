<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FileRepository")
 * @ORM\Table(name="files")
 * @ORM\HasLifecycleCallbacks()
 */
class File implements EntityInterface
{
    public const TXT = "text/plain";
    public const FB2 = "text/xml";
    public const ZIP = "application/zip";

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
    private $uploadedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="bookFiles")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $book;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isImage = false;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $thumbnail;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $mimeType;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $size;

    /**
     * @var string
     */
    private $basename;

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
     * Set uploadedAt
     *
     * @param DateTime $uploadedAt
     *
     * @return File
     */
    public function setUploadedAt(DateTime $uploadedAt): File
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    /**
     * Get uploadedAt
     *
     * @return DateTime
     */
    public function getUploadedAt(): DateTime
    {
        return $this->uploadedAt;
    }

    /**
     * Set isImage
     *
     * @param boolean $isImage
     *
     * @return File
     */
    public function setIsImage(bool $isImage): File
    {
        $this->isImage = $isImage;

        return $this;
    }

    /**
     * Get isImage
     *
     * @return boolean
     */
    public function getIsImage(): bool
    {
        return $this->isImage;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return File
     */
    public function setName(string $name): File
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
     * Set book
     *
     * @param Book $book
     *
     * @return File
     */
    public function setBook(Book $book): File
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return Book
     */
    public function getBook(): Book
    {
        return $this->book;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return File
     */
    public function setType(string $type): File
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @ORM\PreRemove
     */
    public function removeFile(): void
    {
        if (false !== file_exists($this->name)) {
            unlink($this->name);
        }

        if (false !== file_exists($this->thumbnail)) {
            unlink($this->thumbnail);
        }
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return File
     */
    public function setSize(int $size): File
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getSizeInKb(): string
    {
        return sprintf("%s kB", round($this->size / 1024, 0));
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     *
     * @return File
     */
    public function setMimeType(string $mimeType): File
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * Set thumbnail
     *
     * @param string $thumbnail
     *
     * @return File
     */
    public function setThumbnail(string $thumbnail): File
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @return string
     */
    public function getBasename(): string
    {
        return $this->basename;
    }

    /**
     * @param string $basename
     *
     * @return File
     */
    public function setBasename(string $basename): File
    {
        $this->basename = $basename;

        return $this;
    }
}
