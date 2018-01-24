<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ExportItemRepository")
 * @ORM\Table(name="export_items")
 * @ORM\HasLifecycleCallbacks()
 */
class ExportItem implements EntityInterface
{
    public const AUTHOR = 'AppBundle\Entity\Author';
    public const GENRE = 'AppBundle\Entity\Genre';
    public const SERIE = 'AppBundle\Entity\Serie';

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
     * @param string $entityClass
     * @return $this
     */
    public function setTargetEntity(string $entityClass): ExportItem
    {
        if (false === in_array($entityClass, self::getAllowedEntitiesList())) {
            throw new \LogicException();
        }
        $this->targetEntity = $entityClass;

        return $this;
    }

    /**
     * @ORM\PreRemove
     */
    public function removeFile(): void
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
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param DateTime $createdAt
     *
     * @return ExportItem
     */
    public function setCreatedAt(DateTime $createdAt): ExportItem
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
     * Get targetEntity
     *
     * @return string
     */
    public function getTargetEntity(): string
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
    public function setFilename(string $filename): ExportItem
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return array
     */
    public static function getAllowedEntitiesList(): array
    {
        return [
            self::AUTHOR,
            self::GENRE,
            self::SERIE
        ];
    }
}
