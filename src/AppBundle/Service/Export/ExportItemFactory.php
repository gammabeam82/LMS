<?php

namespace AppBundle\Service\Export;

use AppBundle\Entity\ExportItem;

final class ExportItemFactory
{
    /**
     * @param string $filename
     * @param string $entity
     *
     * @return ExportItem
     */
    public static function get(string $filename, string $entity): ExportItem
    {
        $exportItem = new ExportItem();
        $exportItem->setFilename($filename);
        $exportItem->setTargetEntity($entity);

        return $exportItem;
    }
}
