<?php

namespace AppBundle\Utils;

use InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;

trait PopulateFromArrayTrait
{
    /**
     * @param array $data
     *
     * @throws \TypeError
     */
    public function fromArray(array $data): void
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($data as $property => $value) {
            if (false !== property_exists($this, $property)) {
                $accessor->setValue($this, $property, $value);
            } else {
                throw new InvalidArgumentException();
            }
        }
    }
}
