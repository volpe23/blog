<?php

namespace Core\Routing;

use Closure;
use InvalidArgumentException;

class Registrar
{
    /**
     * Stored attributes
     * @var array $attributes
     */
    protected array $attributes = [];

    protected array $allowedMethods = [
        "middleware",
        "prefix",
    ];

    public function __construct(protected Router $router) {}

    /**
     * Sets a value to the attribute
     * @param string $key
     * @param mixed $value
     * 
     * @return self
     * 
     * @throws \InvalidArgumentException
     */
    public function attribute(string $key, mixed $value): self
    {
        if (!$this->isMethodAllowed($key)) {
            throw new InvalidArgumentException("Attribute {$key} does not exist");
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    private function group(Closure $callaback): self
    {
        $this->router->group($this->attributes, $callaback);

        return $this;
    }

    /**
     * Checks if method is allowed
     * @param string $method
     * 
     * @return bool
     */
    private function isMethodAllowed(string $method): bool
    {
        return in_array($method, $this->allowedMethods);
    }
}
