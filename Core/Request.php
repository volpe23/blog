<?php

namespace Core;

use Core\Validator;

class Request
{
    private array $errors;
    private Session $session;
    public function __construct(private array $query, private array $body, private array $server, App $app)
    {
        $this->session = $app->get("session");
    }

    public function __get(string $param): string | null
    {
        return $this->body[$param] ?? null;
    }

    // TODO: Refactor validation classes and this monstrosity
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
            $this->session->setErrors($this->errors);
            redirect($this->server["REQUEST_URI"]);
        }
        return $this->body;
    }
}
