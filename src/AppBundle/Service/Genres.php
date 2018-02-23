<?php

namespace AppBundle\Service;

use AppBundle\Entity\Genre;
use AppBundle\Filter\DTO\GenreFilter;
use AppBundle\Service\Export\Exporter;
use AppBundle\Service\Export\ExportInterface;
use AppBundle\Utils\SanitizeQueryTrait;
use Doctrine\ORM\Query;
use Symfony\Component\Translation\TranslatorInterface;

class Genres extends BaseService implements ExportInterface
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
     * Genres constructor.
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
     * @param GenreFilter $filter
     * @return Query
     */
    public function getFilteredGenres(GenreFilter $filter): Query
    {
        /**
         * @var \Doctrine\ORM\EntityRepository $repo
         */
        $repo = $this->doctrine->getRepository(Genre::class);
        $qb = $repo->createQueryBuilder('g');

        if (false === empty($filter->getName())) {
            $qb->andWhere($qb->expr()->like('LOWER(g.name)', ':name'));
            $qb->setParameter('name', sprintf("%%%s%%", $this->sanitizeQuery($filter->getName())));
        }

        if (false !== $filter->getSortByName()) {
            $qb->orderBy('g.name', 'ASC');
        } else {
            $qb->orderBy('g.id', 'DESC');
        }

        return $qb->getQuery();
    }

    /**
     * @param Genre $genre
     */
    public function save(Genre $genre): void
    {
        $this->saveEntity($this->doctrine->getManager(), $genre);
    }

    /**
     * @param Genre $genre
     */
    public function remove(Genre $genre): void
    {
        $this->removeEntity($this->doctrine->getManager(), $genre);
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

        return $this->exportService->export(Genre::class, $rows);
    }
}
