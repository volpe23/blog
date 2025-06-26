<?php

namespace Core\Support;

class Arr
{

    /**
     * Returns parameter as an array
     * @param mixed $value
     * 
     * @return array
     */
    public static function wrap(mixed $value): array
    {
        if (is_null($value)) return [];

        return is_array($value) ? $value : [$value];
    }
}