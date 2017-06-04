<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FileRepository")
 * @ORM\Table(name="files")
 * @ORM\HasLifecycleCallbacks()
 */
class File implements EntityInterface
{
	const TXT = "text/plain";
	const FB2 = "text/xml";
	const ZIP = "application/zip";

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set uploadedAt
     *
     * @param \DateTime $uploadedAt
     *
     * @return File
     */
    public function setUploadedAt($uploadedAt)
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    /**
     * Get uploadedAt
     *
     * @return \DateTime
     */
    public function getUploadedAt()
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
    public function setIsImage($isImage)
    {
        $this->isImage = $isImage;

        return $this;
    }

    /**
     * Get isImage
     *
     * @return boolean
     */
    public function getIsImage()
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
     * Set book
     *
     * @param \AppBundle\Entity\Book $book
     *
     * @return File
     */
    public function setBook(\AppBundle\Entity\Book $book)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return \AppBundle\Entity\Book
     */
    public function getBook()
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
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

	/**
	 * @ORM\PreRemove
	 */
	public function removeFile()
	{
		if(false !== file_exists($this->name)) {
			unlink($this->name);
		}

		if(false !== file_exists($this->thumbnail)) {
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
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

	/**
	 * @return string
	 */
    public function getSizeInKb()
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
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType()
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
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }
}
