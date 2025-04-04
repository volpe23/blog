<?php

namespace Core;

use Error;
use ReflectionMethod;

class Route
{

    private static array $allowedMethods = ["GET", "POST"];
    /**
     * @var array{callback: callable, middleware: array}
     */
    private array $options;
    public function __construct(private $cb)
    {
        $this->options = [
            "callback" => $cb,
            "middleware" => []
        ];
    }

    public static function __callStatic(string $name, $arguments)
    {
        $method = strtoupper($name);
        if (!is_string($arguments[0])) throw new Error("provided URI is not a string");
        if (!in_array($method, self::$allowedMethods)) throw new Error("method not allowed");
        [$uri, $cb] = $arguments;

        if (is_array($cb) && !is_callable($cb) && isset($cb[0], $cb[1])) {
            $cb[0] = App::make($cb[0]);

            if (!is_callable($cb)) throw new Error("provided callback is not callable");
        }
        $inst = new static($cb);

        App::resolve(Router::class)->$name($uri, $inst);
        return $inst;
    }

    public function dispatch()
    {
        if (!empty($this->options["middleware"])) {
            foreach($this->options["middleware"] as $mw) {
                Middleware::resolve($mw);
            }   
        }
        if (is_array($this->options["callback"])) {
            [$controller, $method] = $this->options["callback"];

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
                call_user_func([$controller, $method], ...$dependencies);
            } else {
                call_user_func([$controller, $method]);
            }
        } else {
            call_user_func($this->options["callback"]);
        }
    }

    public function middleware($mwKey)
    {
        $this->options["middleware"][] = $mwKey;
        return $this;
    }
}
