<?php

namespace AppBundle\Service\Export;

use AppBundle\Utils\EntityTrait;
use AppBundle\Entity\ExportItem;
use Doctrine\Bundle\DoctrineBundle\Registry;

class Export
{
    use EntityTrait;

    private const LIMIT = 15;

    /**
     * @var string
     */
    private $path;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var string
     */
    private $filename;

    /**
     * Export constructor.
     * @param Registry $doctrine
     * @param string $path
     */
    public function __construct(Registry $doctrine, string $path)
    {
        $this->doctrine = $doctrine;
        $this->path = $path;
    }

    /**
     * @param string $entityClass
     * @param array $rows
     * @return string
     */
    public function export(string $entityClass, array $rows): string
    {
        if (false === in_array($entityClass, [ExportItem::AUTHOR, ExportItem::GENRE, ExportItem::SERIE])) {
            throw new \LogicException();
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
            foreach ($rows as $title => $getter) {
                if (false === method_exists($item, $getter)) {
                    throw new \BadMethodCallException();
                }
                $sheet->setCellValueByColumnAndRow($col, $row, $item->$getter());
                $this->setAutoSize($sheet, $col);
                $col++;
            }
            $row++;
        }

        $tmpFile = sprintf("%s/%s.xlsx", sys_get_temp_dir(), uniqid());
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save($tmpFile);

        copy($tmpFile, $this->filename);

        $exportItem = new ExportItem();
        $exportItem->setFilename($this->filename);
        $exportItem->setTargetEntity($entityClass);

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
        //array_map(function ($file) { unlink($file); }, glob(sprintf("%s/*.xlsx", $this->path)));

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
}
