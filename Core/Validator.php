<?php

namespace Core;

class Validator
{
    public const REQUIRED_FAIL = "was not provided";
    public const EMAIL_FAIL = "is not a valid email";
    public const MIN_FAIL = "is not of size ";


    public function __construct(
        public string $message,
    ) {}

    public static function required(string $value, string $attribute): static | bool
    {
        return !empty($value) ? true : new static(self::parseErrorMessage(self::REQUIRED_FAIL, $attribute));
    }

    public static function email(string $value, string $attribute): static | bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? true : new static(self::parseErrorMessage(self::EMAIL_FAIL, $attribute));
    }

    public static function min(string $value, string $attribute, int $num = 1): static | bool
    {
        return strlen($value) >= $num ? true : new static(self::parseErrorMessage(self::MIN_FAIL, $attribute, $num));
    }

    private static function parseErrorMessage(string $message, string $attribute, $value = ""): string
    {
        $value = !empty($value) ? " " . $value : $value;
        return "$attribute $message" . $value;
    }
}
