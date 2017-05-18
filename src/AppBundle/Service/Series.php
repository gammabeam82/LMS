<?php

namespace AppBundle\Service;

use AppBundle\Entity\Serie;
use AppBundle\Filter\EntityFilterInterface;
use AppBundle\Utils\EntityTrait;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Translation\TranslatorInterface;
use AppBundle\Service\Export\Export;

class Series
{
	use EntityTrait;

	/**
	 * @var Registry
	 */
	private $doctrine;

	/**
	 * @var Export
	 */
	private $exportService;

	/**
	 * @var TranslatorInterface
	 */
	private $translator;

	/**
	 * Series constructor.
	 * @param Registry $doctrine
	 * @param Export $export
	 * @param TranslatorInterface $translator
	 */
	public function __construct(Registry $doctrine, Export $export, TranslatorInterface $translator)
	{
		$this->doctrine = $doctrine;
		$this->exportService = $export;
		$this->translator = $translator;
	}

	/**
	 * @param EntityFilterInterface $filter
	 * @return \Doctrine\ORM\Query
	 */
	public function getFilteredSeries(EntityFilterInterface $filter)
	{
		/**
		 * @var \Doctrine\ORM\EntityRepository $repo
		 */
		$repo = $this->doctrine->getRepository(Serie::class);
		$qb = $repo->createQueryBuilder('s');

		if (false === empty($filter->getName())) {
			$qb->andWhere($qb->expr()->like('LOWER(s.name)', ':name'));
			$qb->setParameter('name', "%" . mb_strtolower($filter->getName()) . "%");
		}

		if (false !== $filter->getSortByName()) {
			$qb->orderBy('s.name', 'ASC');
		} else {
			$qb->orderBy('s.id', 'DESC');
		}

		return $qb->getQuery();
	}

	/**
	 * @param Serie $serie
	 */
	public function save(Serie $serie)
	{
		$this->saveEntity($this->doctrine->getManager(), $serie);
	}

	/**
	 * @param Serie $serie
	 */
	public function remove(Serie $serie)
	{
		$this->removeEntity($this->doctrine->getManager(), $serie);
	}

	/**
	 * @return string
	 */
	public function export()
	{
		$translator = $this->translator;

		$repo = $this->doctrine->getRepository(Serie::class);

		$rows = [
			$translator->trans('messages.name') => 'getName',
			$translator->trans('book.books') => 'getBooksCount'
		];

		$this->exportService->export($repo->findAll(), $rows);

		return $this->exportService->getFileName();
	}
}