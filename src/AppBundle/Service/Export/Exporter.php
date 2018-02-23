<?php

namespace AppBundle\Service\Export;

use AppBundle\Entity\ExportItem;
use AppBundle\Factory\ExportItemFactory;
use AppBundle\Service\BaseService;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Exporter extends BaseService
{
    private const LIMIT = 15;
    private const BG_COLOR = 'F2C81E';

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
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param string $entityClass
     * @param array $rows
     *
     * @return string
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \ReflectionException
     */
    public function export(string $entityClass, array $rows): string
    {
        if (false === in_array($entityClass, ExportItem::getAllowedEntitiesList())) {
            throw new \UnexpectedValueException();
        }

        if (0 === $this->doctrine->getRepository($entityClass)->count([])) {
            throw new  \LogicException();
        }

        $repo = $this->doctrine->getRepository($entityClass);
        $exportData = $repo->findAll();

        $this->filename = sprintf("%s/%ss-%s.xlsx",
            $this->path,
            strtolower((new \ReflectionClass($exportData[0]))->getShortName()),
            date("Y.m.d_H:i:s")
        );

        $excel = new Spreadsheet();
        $sheet = $excel->getActiveSheet();
        $col = 1;
        $row = 1;

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($rows as $title => $property) {
            $sheet->setCellValueByColumnAndRow($col, $row, $title);

            $this->setAutoSize($sheet, $col);

            $sheet
                ->getStyleByColumnAndRow($col, $row)
                ->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => self::BG_COLOR]
                    ]
                ]);

            $col++;
        }

        $row++;

        foreach ($exportData as $item) {
            $col = 1;
            foreach ($rows as $title => $property) {
                $sheet->setCellValueByColumnAndRow($col, $row, $accessor->getValue($item, $property));
                $this->setAutoSize($sheet, $col);
                $col++;
            }
            $row++;
        }

        $tmpFile = sprintf("%s/%s.xlsx", sys_get_temp_dir(), uniqid());
        $objWriter = $writer = new Xlsx($excel);
        $objWriter->save($tmpFile);

        copy($tmpFile, $this->filename);

        $exportItem = ExportItemFactory::get($this->filename, $entityClass);

        $this->saveEntity($this->doctrine->getManager(), $exportItem);

        return $this->filename;
    }

    /**
     * @param Worksheet $sheet
     * @param int $column
     */
    private function setAutoSize(Worksheet $sheet, int $column): void
    {
        $colLetter = Coordinate::stringFromColumnIndex($column);

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
            $data[(new \ReflectionClass($entity))->getShortName()] = $this->doctrine->getRepository($entity)->count([]);
        }

        return $data;
    }
}
