<?php

namespace Core\Routing;

use Core\Container;


class Router
{

    const PREG_MATCH = '/{(\w+)}/';

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
     * @param string $path
     * @param string $method
     * @param callable|arary $action
     * 
     * @return Route
     */
    private function add(string $path, string $method, $action)
    {
        return $this->routes[$method][$path] = $this->newRoute($action, $method, $path);
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
    private function newRoute($action, $method, $path, array $params = [])
    {
        return (new Route($action, $method, $params))
            ->setRouter($this)
            ->setContainer($this->container)
            ->setRouteRegex($path);
    }


    private function getRoute(string $uri, string $method): Route|false
    {
        foreach ($this->routes[$method] as $route) {
            if (preg_match($route->getRouteRegex(), $uri, $matches)) {
                $route->setParams($matches);
                return $route;
            }
        }
        return $route;
    }


    private function getRouteParamValues(string $uri): array
    {

        return [];
    }

    /**
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

        $regex = $route->getRouteRegex();
        preg_match($regex, $uri, $matches);
    }
}
