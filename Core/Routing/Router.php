<?php

namespace Core\Routing;

use Core\Container;


class Router
{

    /**
     * @var \Core\Container
     */
    protected $container;
    /**
     * @var array<string, array<string, Route>>
     */
    private array $routes = [
        "GET" => [],
        "POST" => []
    ];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    private function add(string $uri, string $method, $action)
    {
        $this->routes[$method][$uri] = $this->newRoute($action, $method);
    }

    public function get(string $uri, $action)
    {
        $this->add($uri, "GET", $action);
    }

    public function post(string $uri, $action)
    {
        $this->add($uri, "POST", $action);
    }

    /**
     * @param callable|array $action
     * @param string $method
     * 
     * @return Route
     */
    private function newRoute($action, $method)
    {
        return new Route($action, $method, $this);
    }

    /**
     * Get the main app container
     * 
     * @return \Core\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $uri
     * @param string $method
     * 
     * @return void
     */
    public function route($uri, $method)
    {
        if (isset($this->routes[$method][$uri])) {
            $route = $this->routes[$method][$uri];
            $route->dispatch($method);
        } else {
            view("404", [
                "uri" => $uri,
                "statusCode" => 404
            ]);
        }
    }
}
