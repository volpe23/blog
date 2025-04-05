<?php

namespace Core;

use Core\Validator;

class Request
{
    private array $query;
    private array $body;
    private array $server;
    private array $errors;
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

    public function validate(array $validations)
    {
        foreach ($validations as $attr => $validation) {
            $splitValidation = explode("|", $validation);
            foreach ($splitValidation as $singleValidation) {
                if (str_contains($singleValidation, ":")) {
                    [$singleValidation, $value] = explode(":", $singleValidation);
                    $valid = Validator::$singleValidation($this->body[$attr], $attr, $value);
                    if ($valid !== true) {
                        $this->errors[] = $valid->message;
                    }
                } else {
                    $valid = Validator::$singleValidation($this->body[$attr], $attr);
                    if ($valid !== true) {
                        $this->errors[] = $valid->message;
                    }
                }
            }
        }

        if (!empty($this->errors)) {
            Session::setErrors($this->errors);
            redirect($this->server["REQUEST_URI"]);
        }
        return $this->body;
    }
}
