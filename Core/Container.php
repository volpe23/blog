<?php

namespace Core;

use ArrayAccess;
use Core\Exceptions\ContainerException;
use Core\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;
use ReflectionUnionType;

class Container implements ContainerInterface, ArrayAccess
{

    /**
     * @var array<string, callable> $entries
     */
    protected $entries = [];

    /**
     * @var ServiceProvider[] $providers
     */
    protected $providers = [];

    /**
     * @var array<string, object> $instances
     */
    protected $instances = [];

    /**
     * Checks wether the binding exists
     * 
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function bind(string $id, callable $resolver): void
    {
        $this->entries[$id] = $resolver;
    }

    /**
     * @return object
     */
    public function get(string $id)
    {
        if ($this->has($id)) {
            $entry = $this->entries[$id];

            return $entry($this);
        }

        return $this->resolve($id);
    }

    public function make(string $key) {
        return $this->entries[$key]();
    }

    public function singleton(string $id, callable $resolver): void {
        $this->entries[$id] = function () use ($resolver) {
            static $instance;
            if (!$instance) {
                $instance = $resolver($this);
            }

            return $instance;
        };
    }

    /**
     * @return object
     */
    public function resolve(string $id)
    {

        $reflection = new ReflectionClass($id);

        if (!$reflection->isInstantiable()) throw new ContainerException("Class $id in not instantiable");

        $constructor = $reflection->getConstructor();
        if (!$constructor) {
            return $reflection->newInstance();
        }
        $params = $constructor->getParameters();

        if (!$params) return $reflection->newInstance();

        $dependencies = array_map(function (ReflectionParameter $param) use ($id) {
            $name = $param->getName();
            $type = $param->getType();

            if (!$type) throw new ContainerException("Class $id could not be resolved because $name does not have a type hint");

            if ($type instanceof ReflectionUnionType || !$type->isBuiltin()) throw new ContainerException("Provided type: $type is not good");

            return $this->get($name);
        }, $params);

        return $reflection->newInstanceArgs($dependencies);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->entries); 
    }

    /**
     * @param string $offset
     * 
     * @return mixed
     */
    public function offsetGet($offset): mixed {
        return call_user_func($this->entries[$offset]);
    }

    /**
     * @param string $offset
     * @param mixed $value
     * 
     * @return void
     */
    public function offsetSet($offset, $value): void {
        $this->entries[$offset] = $value;
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset): void {
        unset($this->entries[$offset]);
    }

}
