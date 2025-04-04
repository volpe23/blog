<?php

namespace Core;

use Core\Validator;

class Request
{
    private array $query;
    private array $body;
    private array $server;
    public function __construct(array $query, array $body, array $server, private array $session)
    {
        $this->query = $query;
        $this->body = $body;
        $this->server = $server;
    }

    public function __get(string $param): string | null
    {
        return $this->body[$param] ?? null;
    }

    public function validate(array $validations): bool
    {
        foreach ($validations as $attr => $validation) {
            $splitValidation = explode("|", $validation);
            foreach ($splitValidation as $singleValidation) {
                if (str_contains($singleValidation, ":")) {
                    [$singleValidation, $value] = explode(":", $singleValidation);
                    if (!Validator::$singleValidation($this->body[$attr], $value)) return false;
                } else {
                    if (!Validator::$singleValidation($this->body[$attr])) return false;
                }
            }
        }
        return true;
    }
}
