<?php

namespace AppBundle\Utils;

use Doctrine\Common\Persistence\ObjectManager as EntityManager;

trait EntityTrait
{
	/**
	 * @param EntityManager $em
	 * @param $entity
	 */
	public function saveEntity(EntityManager $em, $entity)
	{
		$em->persist($entity);
		$em->flush();
	}

	/**
	 * @param EntityManager $em
	 * @param $entity
	 */
	public function removeEntity(EntityManager $em, $entity)
	{
		$em->remove($entity);
		$em->flush();
	}
}