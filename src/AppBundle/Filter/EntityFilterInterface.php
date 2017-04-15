<?php

namespace AppBundle\Filter;

interface EntityFilterInterface
{
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $name
	 */
	public function setName($name);

	/**
	 * @return bool
	 */
	public function getSortByName();


	/**
	 * @param bool $sortByName
	 */
	public function setSortByName($sortByName);

}