<?php

namespace Core\Routing;

use Exception;
use ReflectionMethod;

class Route
{

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
     * Application router instance
     * 
     * @var Router $router
     */
    protected $router;

    /**
     * Application Container instance
     * 
     * @var Container $container
     */
    protected $container;

    /**
     * Does Route have params
     * @var boolean $hasParams
     */
    protected $hasParams = false;

    /**
     * Compiled route regex
     * @var string $routeRegex
     */
    protected $routeRegex;

    /**
     * @param array|callable $action
     * @param string $method
     * @param array $params
     */
    public function __construct(protected $action, protected $method, protected array $params)
    {
        if (count($params) > 0) $this->hasParams = true;
    }

    public function dispatch()
    {
        $this->executeMiddlewares();
        $this->resolveAction();

        call_user_func($this->resolvedAction);
    }

    public function resolveAction()
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
        [$controllerString, $method] = $this->action;

        $methodReflection = new ReflectionMethod($controllerString, $method);
        $params = $methodReflection->getParameters();

        dd($params);
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

        $instance = $this->resolveFromContainer($controllerString);

        $callable = [$instance, $method];

        return function () use ($callable, $dependencies) {
            call_user_func_array($callable, $dependencies);
        };
    }

    /**
     * @return object
     */
    protected function resolveFromContainer(string $key)
    {
        return $this->container->get($key);
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
            call_user_func($middleware, $this->container);
        }
    }

    public function setRouter(Router $router): static
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Sets the application container to the route
     * @var Container $container
     * 
     * @return self
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Sets values to params
     * @param array $params
     * 
     * @return self
     */
    public function setParams(array $params): static
    {
        foreach ($params as $key => $value) {
            if (isset($this->params[$key])) {
                $this->params[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Routes uri path
     * @param string $path
     * 
     * @return void
     */
    public function setRouteRegex(string $path): self
    {
        $this->routeRegex = $this->compileRoute($path);
        return $this;
    }

    /**
     * Get route regex
     * 
     * @return string
     */
    public function getRouteRegex(): string
    {
        return $this->routeRegex;
    }

    /**
     * Compiles path string to a regex
     * @param string $path
     * 
     * @return string
     */
    private function compileRoute(string $path): string
    {
        return '#^' . preg_replace_callback(Router::PREG_MATCH, function ($matches) {
            return '(?P<' . $matches[1] . '>[^/]+)';
        }, $path) . '$#';
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
