<?php

namespace Core\Routing;

use Closure;
use Core\Container;
use Core\Support\Arr;

class Router
{

    const PREG_MATCH = '/\{(\w+)\}/';

    /**
     * App container instance
     * @var \Core\Container
     */
    protected $container;

    /**
     * Registered routes
     * @var array<string, array<string, Route>>
     */
    private array $routes = [
        "GET" => [],
        "POST" => []
    ];

    /**
     * Registered group stack functions
     * @var array<array<string, mixed>> $groupStack
     */
    private array $groupStack = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Add new route to the router
     * @param string $path
     * @param string $method
     * @param callable|arary $action
     * 
     * @return Route
     */
    private function add(string $path, string $method, $action)
    {
        $route = $this->newRoute($action, $method, $path);

        if ($this->hasGroupStack()) {
            foreach ($this->groupStack as $stack) {
                foreach ($stack as $groupMethod => $value) {
                    $route->{$groupMethod}($value);
                }
            }
        }

        return $this->routes[$method][$route->getPath()] = $route;
    }

    /**
     * Registers a GET route in the application
     * 
     * @param string $path
     * @param callable|array $action
     * 
     * @return Route
     */
    public function get(string $path, $action)
    {
        return $this->add($path, "GET", $action);
    }

    /**
     * Registers a POST route in the application
     * 
     * @param string $path
     * @param callable|array $action
     * 
     * @return Route
     */
    public function post(string $path, $action)
    {
        return $this->add($path, "POST", $action);
    }

    /**
     * @param callable|array $action
     * @param string $method
     * @param array $params
     * @param string $path
     * 
     * @return Route
     */
    private function newRoute($action, $method, $path)
    {
        return (new Route($action, $method, $path))
            ->setRouter($this)
            ->setContainer($this->container);
    }

    private function updateGroupStack(array $attributes): void
    {
        $this->groupStack[] = $attributes;
    }

    /**
     * Checks wether there are attributes in group stack
     * @return bool
     */
    private function hasGroupStack(): bool
    {
        return !empty($this->groupStack);
    }

    public function group(array $attributes, callable $routes): self
    {
        foreach (Arr::wrap($routes) as $groupRoutes) {
            $this->updateGroupStack($attributes);
            $this->loadRoutes($groupRoutes);

            array_pop($this->groupStack);
        }

        return $this;
    }

    private function loadRoutes(Closure $routes): void
    {
        call_user_func($routes, $this);
    }

    /**
     * Gets route that matches URI
     * @param string $uri
     * @param string $method
     * 
     * @return Route|false
     */
    private function getRoute(string $uri, string $method): Route|false
    {
        foreach ($this->routes[$method] as $route) {
            if (preg_match($route->getRouteRegex(), $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $route->setParams($params);

                return $route;
            }
        }
        return $route;
    }

    /**
     * Routes to the route matching provided URI
     * @param string $uri
     * @param string $method
     * 
     * @return void
     */
    public function route(string $uri, string $method)
    {
        $route = $this->getRoute($uri, $method);
        if (!$route) {
            view("404", [
                "uri" => $uri,
                "statusCode" => 404
            ]);
        }

        $route->dispatch();
    }

    public function __call(string $method, array $arguments): Registrar
    {
        return (new Registrar($this))->attribute($method, array_key_exists(0, $arguments) ? $arguments[0] : true);
    }
}
