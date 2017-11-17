<?php

namespace AppBundle\Service;

use Symfony\Bridge\Doctrine\RegistryInterface;

interface ServiceInterface
{
    public function setDoctrine(RegistryInterface $doctrine): void;
}
