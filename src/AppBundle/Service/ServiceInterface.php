<?php

namespace AppBundle\Service;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\EntityInterface;

interface ServiceInterface
{
    public function setDoctrine(RegistryInterface $doctrine): void;

    public function saveEntity(ObjectManager $em, EntityInterface $entity): void;

    public function removeEntity(ObjectManager $em, EntityInterface $entity): void;
}
