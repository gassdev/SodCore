<?php

declare(strict_types=1);

namespace Sod\Router;

use Exception;
use Sod\Router\RouterInterface;

class Router implements RouterInterface
{
    /**
     * return an array of routes from routing table
     * @var array
     */
    protected array $routes = [];

    /**
     * return an array of route parameters
     */
    protected array $params = [];

    /**
     * add a suffix onto the controller name
     * @var string
     */
    protected string $controllerSuffix = 'controller';

    /**
     * @inheritdoc
     */
    public function add(string $route, array $params): void
    {
        $this->routes[$route] = $params;
    }

    /**
     * @inheritdoc
     */
    public function dispatch(string $url): void
    {
        if ($this->match($url)) {
            $controllerString = $this->params['controller'];
            $controllerString = $this->transformUpperCamelCase(
                $controllerString
            );
            $controllerString = $this->getNamespace($controllerString);

            if (class_exists($controllerString)) {
                $controllerObject = new $controllerString();
                $action = $this->params['action'];
                $action = $this->transformCamelCase($action);

                if (is_callable([$controllerObject, $action])) {
                    $controllerObject->$action();
                } else {
                    throw new Exception();
                }
            } else {
                throw new Exception();
            }
        } else {
            throw new Exception();
        }
    }

    /**
     * transformUpperCamelCase
     *
     * @param  string $string
     * @return string
     */
    private function transformUpperCamelCase(string $string): string
    {
        return str_replace(' ', '', ucfirst(str_replace('-', ' ', $string)));
    }

    /**
     * match the route to the routes in the routing table, setting this params
     * property if a route is found
     *
     * @param string $url
     * @return bool
     */
    private function match(string $url): bool
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $param) {
                    if (is_string($key)) {
                        $params[$key] = $param;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    /**
     * get the namespace for the controller class. the namespace defined with in the route parameters
     * only if it was addedd.
     *
     * @param  string $string
     * @return string
     */
    private function getNamespace(string $string): string
    {
        $namespace = 'App\Controller\\';
        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }
        return $namespace;
    }

    /**
     * transformCamelCase
     *
     * @param  string $string
     * @return string
     */
    private function transformCamelCase(string $string): string
    {
        return ucfirst($this->transformUpperCamelCase($string));
    }
}
