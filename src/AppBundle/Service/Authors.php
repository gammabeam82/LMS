<?php

namespace AppBundle\Service;
use AppBundle\Entity\User;
use AppBundle\Entity\Author;
use AppBundle\Filter\AuthorFilter;
use Doctrine\Bundle\DoctrineBundle\Registry;

class Authors
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

	/**
	 * @param User $user
	 * @param Author $author
	 * @param bool $isCreating
	 * @return Author
	 */
	public function save(User $user, Author $author, $isCreating = true)
	{
		if(false !== $isCreating) {
			$author->setAddedBy($user);
		}

		$em = $this->doctrine->getManager();
		$em->persist($author);
		$em->flush();

		return $author;
	}

	/**
	 * @param AuthorFilter $filter
	 * @return mixed
	 */
	public function getFilteredAuthors(AuthorFilter $filter)
	{
		$repo = $this->doctrine->getRepository('AppBundle:Author');
		$qb = $repo->createQueryBuilder('a');

		if (!empty($filter->getLastName())) {
			$qb->andWhere($qb->expr()->like('LOWER(a.lastName)', ':name'));
			$qb->setParameter('name', "%" . mb_strtolower($filter->getLastName()) . "%");
		}

		$qb->orderBy('a.id', 'DESC');

		return $qb->getQuery();
	}

	/**
	 * @param Author $author
	 */
	public function remove(Author $author)
	{
		$em = $this->doctrine->getManager();
		$em->remove($author);
		$em->flush();
	}
}