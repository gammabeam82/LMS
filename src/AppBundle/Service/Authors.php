<?php

namespace AppBundle\Service;

use AppBundle\Entity\Author;
use AppBundle\Filter\DTO\AuthorFilter;
use AppBundle\Service\Export\Export;
use Symfony\Component\Translation\TranslatorInterface;
use AppBundle\Service\Export\ExportInterface;
use Doctrine\ORM\Query;

class Authors extends BaseService implements ExportInterface
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
     * Authors constructor.
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
     * @param Author $author
     */
    public function save(Author $author): void
    {
        $this->saveEntity($this->doctrine->getManager(), $author);
    }

    /**
     * @param AuthorFilter $filter
     * @return \Doctrine\ORM\Query
     */
    public function getFilteredAuthors(AuthorFilter $filter): Query
    {
        /**
         * @var \Doctrine\ORM\EntityRepository $repo
         */
        $repo = $this->doctrine->getRepository(Author::class);
        $qb = $repo->createQueryBuilder('a');

        if (false === empty($filter->getLastName())) {
            $qb->andWhere($qb->expr()->like('LOWER(a.lastName)', ':name'));
            $qb->setParameter('name', sprintf("%%%s%%", mb_strtolower($filter->getLastName())));
        }

        if (false === empty($filter->getFirstLetter())) {
            $qb->andWhere($qb->expr()->like('LOWER(a.lastName)', ':letter'));
            $qb->setParameter('letter', sprintf("%s%%", mb_strtolower($filter->getFirstLetter())));
        }

        if (false !== $filter->getSortByName()) {
            $qb->orderBy('a.lastName', 'ASC');
        } else {
            $qb->orderBy('a.id', 'DESC');
        }

        return $qb->getQuery();
    }

    /**
     * @param Author $author
     */
    public function remove(Author $author): void
    {
        $this->removeEntity($this->doctrine->getManager(), $author);
    }

    /**
     * @return string
     */
    public function export(): string
    {
        $translator = $this->translator;

        $rows = [
            $translator->trans('author.first_name') => 'getFirstName',
            $translator->trans('author.last_name') => 'getLastName',
            $translator->trans('book.books') => 'getBooksCount'
        ];

        return $this->exportService->export(Author::class, $rows);
    }
}
