<?php

namespace AppBundle\Service;

use AppBundle\Entity\Serie;
use AppBundle\Filter\DTO\SerieFilter;
use AppBundle\Service\Export\Exporter;
use AppBundle\Service\Export\ExportInterface;
use AppBundle\Utils\SanitizeQueryTrait;
use Doctrine\ORM\Query;
use Symfony\Component\Translation\TranslatorInterface;

class Series extends BaseService implements ExportInterface
{
    use SanitizeQueryTrait;

    /**
     * @var Exporter
     */
    private $exportService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Series constructor.
     *
     * @param Exporter $export
     * @param TranslatorInterface $translator
     */
    public function __construct(Exporter $export, TranslatorInterface $translator)
    {
        $this->exportService = $export;
        $this->translator = $translator;
    }

    /**
     * @param SerieFilter $filter
     *
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
            $qb->setParameter('name', sprintf("%%%s%%", $this->sanitizeQuery($filter->getName())));
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
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \ReflectionException
     */
    public function export(): string
    {
        $translator = $this->translator;

        $rows = [
            $translator->trans('messages.name') => 'name',
            $translator->trans('book.books') => 'booksCount'
        ];

        return $this->exportService->export(Serie::class, $rows);
    }
}
