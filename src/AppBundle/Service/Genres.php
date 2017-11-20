<?php

namespace AppBundle\Service;

use AppBundle\Entity\Genre;
use AppBundle\Service\Export\Export;
use AppBundle\Filter\DTO\GenreFilter;
use Symfony\Component\Translation\TranslatorInterface;
use AppBundle\Service\Export\ExportInterface;
use Doctrine\ORM\Query;

class Genres extends BaseService implements ExportInterface
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
     * Genres constructor.
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
            $qb->setParameter('name', sprintf("%%%s%%", mb_strtolower($filter->getName())));
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
     */
    public function export(): string
    {
        $translator = $this->translator;

        $rows = [
            $translator->trans('messages.name') => 'getName',
            $translator->trans('book.books') => 'getBooksCount'
        ];

        return $this->exportService->export(Genre::class, $rows);
    }
}
