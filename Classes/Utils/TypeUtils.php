<?php
namespace Aoe\Imgix\Utils;

class TypeUtils
{
    /**
     * @var string
     */
    const TYPE_STRING = 'string';

    /**
     * @var string
     */
    const TYPE_INTEGER = 'integer';

    /**
     * @var string
     */
    const TYPE_BOOLEAN = 'boolean';

    /**
     * @param array $map
     * @param array $target
     * @return array
     */
    public static function castTypesByMap(array $map, array $target)
    {
        $casted = array();
        foreach ($target as $key => $value) {
            if (isset($map[$key])) {
                settype($value, $map[$key]);
            }
            $casted[$key] = $value;
        }
        return $casted;
    }
}
