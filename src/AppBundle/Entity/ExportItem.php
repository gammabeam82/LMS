<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="export_items")
 * @ORM\HasLifecycleCallbacks()
 */
class ExportItem implements EntityInterface
{
	const AUTHOR = 'AppBundle\Entity\Author';
	const GENRE = 'AppBundle\Entity\Genre';
	const SERIE = 'AppBundle\Entity\Serie';

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
	 * @ORM\Column(type="string", length=250, nullable=false)
	 * @Assert\NotBlank()
	 */
	private $targetEntity;

	/**
	 * @ORM\Column(type="string", length=250, nullable=false)
	 * @Assert\NotBlank()
	 */
	private $filename;


	/**
	 * @param $entityClass
	 * @return ExportItem
	 */
	public function setTargetEntity($entityClass)
	{
		if (false === in_array($entityClass, [self::AUTHOR, self::GENRE, self::SERIE])) {
			throw new \LogicException();
		}
		$this->targetEntity = $entityClass;
		return $this;
	}

	/**
	 * @ORM\PreRemove
	 */
	public function removeFile()
	{
		if (false !== file_exists($this->filename)) {
			unlink($this->filename);
		}
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
	 * @return ExportItem
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
	 * Get targetEntity
	 *
	 * @return string
	 */
	public function getTargetEntity()
	{
		return $this->targetEntity;
	}

	/**
	 * Set filename
	 *
	 * @param string $filename
	 *
	 * @return ExportItem
	 */
	public function setFilename($filename)
	{
		$this->filename = $filename;

		return $this;
	}

	/**
	 * Get filename
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}
}
