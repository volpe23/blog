<?php

namespace Core;

use Reflection;
use ReflectionClass;

class App extends Container
{

    public function __construct(protected array $config) {}

    /**
     * Returns the config settings
     * 
     * @return array $config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param class-string<ServiceProvider>[] $providers
     * 
     * @return static
     */
    public function register($providers)
    {
        foreach ($providers as $provider) {
            $reflector = new ReflectionClass($provider);

            $instance = $reflector->newInstance($this);
            $this->providers[] = $instance;

            $instance->register();
        }

        return $this;
    }
}
