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

    /**
     * Add new route to the router
     * @param string $uri
     * @param string $method
     * @param callable|arary $action
     * 
     * @return Route
     */
    private function add(string $uri, string $method, $action)
    {
        return $this->routes[$method][$uri] = $this->newRoute($action, $method);
    }

    /**
     * Registers a GET route in the application
     * 
     * @param string $uri
     * @param callable|array $action
     * 
     * @return Route
     */
    public function get(string $uri, $action)
    {
        return $this->add($uri, "GET", $action);
    }

    /**
     * Registers a POST route in the application
     * 
     * @param string $uri
     * @param callable|array $action
     * 
     * @return Route
     */
    public function post(string $uri, $action)
    {
        return $this->add($uri, "POST", $action);
    }

    /**
     * @param callable|array $action
     * @param string $method
     * 
     * @return Route
     */
    private function newRoute($action, $method)
    {
        return (new Route($action, $method))
            ->setRouter($this)
            ->setContainer($this->container);
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
