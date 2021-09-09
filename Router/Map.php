<?php

namespace SteveEngine\Router;

/**
 * Class Map
 * @package HardtEngine\Router
 */
class Map{
    /**
     * @var array $routes
     */
    public $routes;

    /**
     * @param string $url
     * @return Map
     */
    public function get(string $url, string $class, string $method) : Map{
        $this->routes["get"][] = new Route($url, $class, $method);
        return $this;
    }

    /**
     * @param string $url
     * @return Map
     */
    public function post(string $url, string $class, string $method) : Map{
        $this->routes["post"][] = new Route($url, $class, $method);
        return $this;
    }

    /**
     * @param string $url
     * @return Map
     */
    public function put(string $url, string $class, string $method) : Map{
        $this->routes["put"][] = new Route($url, $class, $method);
        return $this;
    }

    /**
     * @param string $url
     * @return Map
     */
    public function delete(string $url, string $class, string $method) : Map{
        $this->routes["delete"][] = new Route($url, $class, $method);
        return $this;
    }

    /**
     * @param string $prefix
     * @param Map $map
     * @return Map
     */
    public function group(string $prefix, Map $map) : Map{
        foreach ($map->routes as $method => $routes){
            foreach ($routes as &$route){
                if ($route->path !== "" && $route->path !== "/"){
                    $route->path = $prefix . $route->path;
                } else{
                    $route->path = $prefix;
                }
                $this->routes[$method][] = $route;
            }
        }
        return $this;
    }
}
