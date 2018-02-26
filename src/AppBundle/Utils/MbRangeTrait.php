<?php

namespace AppBundle\Utils;

trait MbRangeTrait
{
    /**
     * @param string $start
     * @param string $end
     *
     * @return array
     */
    public function mb_range(string $start, string $end): array // @codingStandardsIgnoreLine
    {
        if ($start == $end) {
            return [$start];
        }

        $_result = [];

        list(, $_start, $_end) = unpack("N*", mb_convert_encoding($start . $end, "UTF-32BE", "UTF-8"));
        $_offset = $_start < $_end ? 1 : -1;
        $_current = $_start;
        while ($_current != $_end) {
            $_result[] = mb_convert_encoding(pack("N*", $_current), "UTF-8", "UTF-32BE");
            $_current += $_offset;
        }
        $_result[] = $end;

        return $_result;
    }
}
