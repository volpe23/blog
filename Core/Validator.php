<?php

namespace Core;

class Validator {
    public static function required(string $val): bool {
        return !empty($val) || !isset($val);
    }

    public static function email(string $val): bool {
        return filter_var($val, FILTER_VALIDATE_EMAIL);
    }

    public static function min(string $val, int $num): bool {
        return strlen($val) >= $num;
    }
}