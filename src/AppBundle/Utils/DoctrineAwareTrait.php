<?php

namespace AppBundle\Utils;

use Symfony\Bridge\Doctrine\RegistryInterface;

trait DoctrineAwareTrait
{

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @param RegistryInterface $doctrine
     */
    public function setDoctrine(RegistryInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }
}
