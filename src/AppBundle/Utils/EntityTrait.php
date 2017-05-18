<?php

namespace AppBundle\Utils;

use Doctrine\Common\Persistence\ObjectManager;

trait EntityTrait
{
	/**
	 * @param ObjectManager $em
	 * @param $entity
	 */
	public function saveEntity(ObjectManager $em, $entity)
	{
		$em->persist($entity);
		$em->flush();
	}

	/**
	 * @param ObjectManager $em
	 * @param $entity
	 */
	public function removeEntity(ObjectManager $em, $entity)
	{
		$em->remove($entity);
		$em->flush();
	}
}