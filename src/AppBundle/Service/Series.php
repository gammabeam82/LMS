<?php

namespace AppBundle\Service;

use AppBundle\Entity\Serie;
use AppBundle\Filter\DTO\SerieFilter;
use Symfony\Component\Translation\TranslatorInterface;
use AppBundle\Service\Export\Export;
use AppBundle\Service\Export\ExportInterface;
use Doctrine\ORM\Query;

class Series extends AbstractService implements ExportInterface
{
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
     *
     * @param Export $export
     * @param TranslatorInterface $translator
     */
    public function __construct(Export $export, TranslatorInterface $translator)
    {
        $this->exportService = $export;
        $this->translator = $translator;
    }

    /**
     * @param SerieFilter $filter
     * @return Query
     */
    public function getFilteredSeries(SerieFilter $filter): Query
    {
        /**
         * @var \Doctrine\ORM\EntityRepository $repo
         */
        $repo = $this->doctrine->getRepository(Serie::class);
        $qb = $repo->createQueryBuilder('s');

        if (false === empty($filter->getName())) {
            $qb->andWhere($qb->expr()->like('LOWER(s.name)', ':name'));
            $qb->setParameter('name', sprintf("%%%s%%", mb_strtolower($filter->getName())));
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
    public function save(Serie $serie): void
    {
        $this->saveEntity($this->doctrine->getManager(), $serie);
    }

    /**
     * @param Serie $serie
     */
    public function remove(Serie $serie): void
    {
        $this->removeEntity($this->doctrine->getManager(), $serie);
    }

    /**
     * @return string
     */
    public function export(): string
    {
        $translator = $this->translator;

        $rows = [
            $translator->trans('messages.name') => 'getName',
            $translator->trans('book.books') => 'getBooksCount'
        ];

        return $this->exportService->export(Serie::class, $rows);
    }
}
