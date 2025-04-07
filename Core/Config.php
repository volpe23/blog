<?php

namespace Core;

class Config
{
    public function __construct(private array $config) {}

    public function __get($name)
    {
        return $this->config[$name] ?? null;
    }
}
