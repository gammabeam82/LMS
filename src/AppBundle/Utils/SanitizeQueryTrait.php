<?php

namespace AppBundle\Utils;

trait SanitizeQueryTrait
{
    /**
     * @param string $query
     *
     * @return string
     */
    public function sanitizeQuery(string $query): string
    {
        return trim(mb_strtolower(preg_replace('/[^0-9a-zа-яё\s]+/ui', '', $query)));
    }
}
