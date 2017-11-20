<?php

namespace AppBundle\Utils;

use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\EntityInterface;

trait EntityTrait
{
    /**
     * @param ObjectManager $em
     * @param EntityInterface $entity
     */
    public function saveEntity(ObjectManager $em, EntityInterface $entity): void
    {
        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param ObjectManager $em
     * @param EntityInterface $entity
     */
    public function removeEntity(ObjectManager $em, EntityInterface $entity): void
    {
        $em->remove($entity);
        $em->flush();
    }
}
