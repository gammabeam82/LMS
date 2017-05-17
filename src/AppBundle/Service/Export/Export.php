<?php

namespace AppBundle\Service\Export;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;
use PHPExcel_Worksheet;
use BadMethodCallException;

class Export
{
	/**
	 * @var PHPExcel
	 */
	private $excel;

	/**
	 * @var string
	 */
	private $filename;

	/**
	 * Export constructor.
	 * @param string $path
	 */
	public function __construct($path)
	{
		$this->filename = sprintf("%s/export-%s.xlsx", $path, date("Y.m.d_H:i:s"));
		$this->excel = new PHPExcel();
	}

	/**
	 * @param array $exportData
	 * @param array $rows
	 */
	public function export(array $exportData, array $rows)
	{

		$excel = $this->excel;

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
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
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
					throw new BadMethodCallException();
				}
				$sheet->setCellValueByColumnAndRow($col, $row, $item->$getter());
				$this->setAutoSize($sheet, $col);
				$col++;
			}
			$row++;
		}

		$tmpFile = sprintf("%s/%s.xlsx", sys_get_temp_dir(), uniqid());
		$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$objWriter->save($tmpFile);

		copy($tmpFile, $this->filename);
	}

	/**
	 * @param PHPExcel_Worksheet $sheet
	 * @param int $column
	 */
	private function setAutoSize(PHPExcel_Worksheet $sheet, $column)
	{
		$colLetter = PHPExcel_Cell::stringFromColumnIndex($column);
		$sheet
			->getColumnDimension($colLetter)
			->setAutoSize(true);
	}

	/**
	 * @return string
	 */
	public function getFileName()
	{
		return $this->filename;
	}
}