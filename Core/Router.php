<?php

namespace Core;

use ReflectionMethod;
use Error;

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

    public function route($uri, $method)
    {
        // var_dump($uri, $method);
        foreach ($this->routes as $route) {
            if ($route["uri"] === $uri && $route["method"] === $method) {
                if (is_array($route["callback"])) {
                    [$controller, $method] = $route["callback"];

                    $reflection = new ReflectionMethod($controller, $method);
                    $params = $reflection->getParameters();

                    $dependencies = [];
                    if (count($params) > 0) {
                        foreach ($params as $param) {
                            $type = $param->getType();
                            if (!$type || $type->isBuiltin()) {
                                throw new Error("cannot resolve dependency");
                            }
                            $dependencies[] = App::make($type->getName());
                        }
                        call_user_func([$controller, $method], new Request($_GET, $_POST, $_SERVER));
                    } else {
                        call_user_func([$controller, $method]);
                    }
                } else {
                    call_user_func($route["callback"]);
                }
            }
        }
    }
}
