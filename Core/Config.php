<?php

namespace Core;

use ArrayAccess;

class Config implements ArrayAccess
{
    public function __construct(private array $config) {}

    public function __get($name)
    {
        return $this->config[$name] ?? null;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->config[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->config[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->config[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->config[$offset]);
    }
}
