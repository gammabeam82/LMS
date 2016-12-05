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
	 * @var string
	 */
	private $path;

	/**
	 * @param Registry $doctrine
	 */
	public function __construct(Registry $doctrine, $path = null)
	{
		$this->doctrine = $doctrine;
		$this->path = $path;
	}

}