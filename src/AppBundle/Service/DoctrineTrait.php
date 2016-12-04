<?php

namespace AppBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;

trait DoctrineTrait
{
	/**
	 * @var Registry
	 */
	private $doctrine;

	/**
	 * @param Registry $doctrine
	 */
	public function __construct(Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}

}