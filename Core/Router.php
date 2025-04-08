<?php

namespace Core;


class Router
{

    /**
     * @var array<string, array<string, Route>>
     */
    private array $routes = [
        "GET" => [],
        "POST" => []
    ];

    private function add(string $uri, string $method, Route $route)
    {
        $this->routes[$method][$uri] = $route;
    }

    public function get(string $uri, Route $route)
    {
        $this->add($uri, "GET", $route);
    }

    public function post(string $uri, Route $route)
    {
        $this->add($uri, "POST", $route);
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
