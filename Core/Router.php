<?php

namespace Core;

class Router
{

    private array $routes = [];

    private function add(string $uri, string $method, callable $cb)
    {
        $this->routes[] = [
            "uri" => $uri,
            "method" => $method,
            "callback" => $cb
        ];
    }

    public function get(string $uri, callable $cb)
    {
        $this->add($uri, "GET", $cb);
    }

    public function post(string $uri, callable $cb)
    {
        $this->add($uri, "POST", $cb);
    }

    public function route($uri, $method) {
        // var_dump($uri, $method);
        foreach ($this->routes as $route) {
            if ($route["uri"] === $uri && $route["method"] === $method) {
                call_user_func($route["callback"]);
            }
        }
    }
}


