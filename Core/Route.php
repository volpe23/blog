<?php

namespace Core;

use Error;
use ReflectionMethod;

/**
 * @method static \Core\Route get(string $uri, array|callable $action)
 * @method static \Core\Route post(string $uri, array|callable $action)
 * 
 * @see \Core\Router
 */
class Route
{

    private static array $allowedMethods = ["GET", "POST"];
    private static array $csrfMethods = ["POST", "PUT", "DELETE", "UPDATE"];
    private bool $csrf = false;
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
            $cb[0] = App::resolve($cb[0]);

            if (!is_callable($cb)) throw new Error("provided callback is not callable");
        }
        $inst = new static($cb);

        App::resolve(Router::class)->$name($uri, $inst);
        return $inst;
    }

    public function dispatch(string $requestMethod)
    {
        $config = App::resolve(Config::class);
        if ($config->csrf && !Hash::getCsrfToken()) Hash::generateCsrf();

        if (in_array($requestMethod, static::$csrfMethods) && $config->csrf && !$this->csrf) {
            $this->middleware("csrf");
        }
        if (!empty($this->options["middleware"])) {
            foreach ($this->options["middleware"] as $mw) {
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
                    $dependencies[] = App::resolve($type->getName());
                }
            }
            call_user_func([$controller, $method], $dependencies);
        } else {
            call_user_func($this->options["callback"]);
        }
    }

    /**
     * @param string $mwKey
     * 
     * @return $this
     */
    public function middleware($mwKey)
    {
        $this->options["middleware"][] = $mwKey;
        return $this;
    }

    /**
     * Sets the route csrf status
     * @param bool $flag
     * 
     * @return $this
     */
    public function csrfExempt(bool $flag = true)
    {
        $this->csrf = $flag;
        return $this;
    }
}
