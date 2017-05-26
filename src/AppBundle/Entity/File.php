<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 * @ORM\Table(name="files")
 * @ORM\HasLifecycleCallbacks()
 */
class File
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
	 * @ORM\Column(type="string", length=100)
	 */
	private $type;


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
	}
}
