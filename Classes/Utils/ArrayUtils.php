<?php
namespace Aoe\Imgix\Utils;

class ArrayUtils
{
    /**
     * @param array $array
     * @return array
     */
    public static function filterEmptyValues(array $array)
    {
        $filtered = array();
        foreach ($array as $name => $value) {
            if ('' === $value || null === $value) {
                continue;
            }
            $filtered[$name] = $value;
        }
        return $filtered;
    }
}
