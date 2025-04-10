<?php

namespace Core\Routing;

use Exception;
use Error;
use ReflectionMethod;

class Route
{

    private static array $allowedMethods = ["GET", "POST"];
    private static array $csrfMethods = ["POST", "PUT", "DELETE", "UPDATE"];
    private bool $csrf = false;

    /**
     * Route middleware store
     * 
     * @var array<callable>
     */
    protected array $middlewares = [];

    /**
     * Resolved route action
     * 
     * @var callable $resolvedAction
     */
    protected $resolvedAction;

    /**
     * @param array|callable $action
     * @param string $method
     * @param Router $router
     */
    public function __construct(protected $action, protected $method, protected Router $router) {}

    public function dispatch(string $requestMethod)
    {
        $this->executeMiddlewares();

        call_user_func($this->resolvedAction);
    }

    protected function resolveAction()
    {
        if (is_callable($this->action)) $this->resolvedAction = $this->action;
        else if (is_array($this->action)) $this->resolvedAction = $this->resolveControlerAction();
    }

    /**
     * @return callable
     */
    protected function resolveControlerAction()
    {
        if (count($this->action) !== 2) throw new Exception("Incorrect controller action provided");
        // [$controllerString, $method] = $this->action;

        $methodReflection = new ReflectionMethod($this->action);
        $params = $methodReflection->getParameters();

        $dependencies = [];
        if ($params) {
            foreach ($params as $param) {
                $type = $param->getType();
                if (!$type || $type->isBuiltin()) {
                    throw new Exception("controller parameter cannot be built in");
                }

                $dependencies[] = $this->resolveFromContainer($type);
            }
        }

        return function () use ($dependencies) {
            return call_user_func($this->action, $dependencies);
        };
    }

    /**
     * @return object
     */
    protected function resolveFromContainer(string $key)
    {
        return $this->router->getContainer()->get($key);
    }

    /**
     * Adds middlewares to the route
     * @param string|array $middleware
     * 
     * @return self
     */
    public function middleware($middleware)
    {
        if (is_array($middleware)) {
            foreach ($middleware as $mw) {
                $this->addMiddleware($mw);
            }
        } else $this->addMiddleware($middleware);

        return $this;
    }

    /**
     * @param string $middleware
     * 
     * @return $this
     */
    private function addMiddleware($middleware)
    {
        $this->middlewares[] = $this->resolveFromContainer("middleware")->resolve($middleware);

        return $this;
    }

    /**
     * Runs middlewares
     * 
     * @return void
     */
    private function executeMiddlewares()
    {
        foreach ($this->middlewares as $middleware) {
            call_user_func($middleware);
        }
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
