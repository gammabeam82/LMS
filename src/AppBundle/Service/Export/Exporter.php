<?php

namespace AppBundle\Service\Export;

use AppBundle\Entity\ExportItem;
use AppBundle\Factory\ExportItemFactory;
use AppBundle\Service\BaseService;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Exporter extends BaseService
{
    private const LIMIT = 15;
    private const WRITER_TYPE = 'Excel2007';

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $filename;

    /**
     * Export constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param string $entityClass
     * @param array $rows
     * @return string
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     * @throws \ReflectionException
     */
    public function export(string $entityClass, array $rows): string
    {
        if (false === in_array($entityClass, ExportItem::getAllowedEntitiesList())) {
            throw new \UnexpectedValueException();
        }

        if (0 === $this->doctrine->getRepository($entityClass)->count()) {
           throw new  \LogicException();
        }

        $repo = $this->doctrine->getRepository($entityClass);
        $exportData = $repo->findAll();

        $this->filename = sprintf("%s/%ss-%s.xlsx",
            $this->path,
            strtolower((new \ReflectionClass($exportData[0]))->getShortName()),
            date("Y.m.d_H:i:s")
        );

        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet();
        $col = 0;
        $row = 1;

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($rows as $title => $getter) {
            $sheet->setCellValueByColumnAndRow($col, $row, $title);
            $this->setAutoSize($sheet, $col);
            $sheet
                ->getStyleByColumnAndRow($col, $row)
                ->applyFromArray([
                    'fill' => [
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F2C81E']
                    ]
                ]);
            $col++;
        }

        $row++;

        foreach ($exportData as $item) {
            $col = 0;
            foreach ($rows as $title => $property) {
                $sheet->setCellValueByColumnAndRow($col, $row, $accessor->getValue($item, $property));
                $this->setAutoSize($sheet, $col);
                $col++;
            }
            $row++;
        }

        $tmpFile = sprintf("%s/%s.xlsx", sys_get_temp_dir(), uniqid());
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, self::WRITER_TYPE);
        $objWriter->save($tmpFile);

        copy($tmpFile, $this->filename);

        $exportItem = ExportItemFactory::get($this->filename, $entityClass);

        $this->saveEntity($this->doctrine->getManager(), $exportItem);

        return $this->filename;
    }

    /**
     * @param \PHPExcel_Worksheet $sheet
     * @param int $column
     */
    private function setAutoSize(\PHPExcel_Worksheet $sheet, int $column): void
    {
        $colLetter = \PHPExcel_Cell::stringFromColumnIndex($column);
        $sheet
            ->getColumnDimension($colLetter)
            ->setAutoSize(true);
    }

    public function purge(): void
    {
        $repo = $this->doctrine->getRepository(ExportItem::class);
        $em = $this->doctrine->getManager();

        array_map(function ($item) use ($em) {
            $em->remove($item);
        }, $repo->findAll());

        $em->flush();
    }

    /**
     * @return array
     */
    public function getExportsList(): array
    {
        /* @var \Doctrine\ORM\EntityRepository $repo */
        $repo = $this->doctrine->getRepository(ExportItem::class);

        $exports = [
            'authors' => $repo->findBy(['targetEntity' => ExportItem::AUTHOR], ['createdAt' => 'DESC'], self::LIMIT),
            'genres' => $repo->findBy(['targetEntity' => ExportItem::GENRE], ['createdAt' => 'DESC'], self::LIMIT),
            'series' => $repo->findBy(['targetEntity' => ExportItem::SERIE], ['createdAt' => 'DESC'], self::LIMIT)
        ];

        return $exports;
    }

    /**
     * @param ExportItem $item
     */
    public function remove(ExportItem $item): void
    {
        $this->removeEntity($this->doctrine->getManager(), $item);
    }

    /**
     * @return array
     *
     * @throws \ReflectionException
     */
    public function getItemsCount(): array
    {
        $data = [];

        foreach (ExportItem::getAllowedEntitiesList() as $entity) {
            $data[(new \ReflectionClass($entity))->getShortName()] = $this->doctrine->getRepository($entity)->count();
        }

        return $data;
    }
}
