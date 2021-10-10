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
    public array $routes;

    /**
     * @param string $url
     * @param string $class
     * @param string $method
     * @param string $name
     * @param string $permission
     * @return Map
     */
    public function get(string $url, string $class, string $method, string $name = "", string $permission = "") : Map{
        $this->routes["get"][] = new Route($url, $class, $method, $name, $permission);
        return $this;
    }

    /**
     * @param string $url
     * @param string $class
     * @param string $method
     * @param string $name
     * @param string $permission
     * @return Map
     */
    public function post(string $url, string $class, string $method, string $name = "", string $permission = "") : Map{
        $this->routes["post"][] = new Route($url, $class, $method, $name, $permission);
        return $this;
    }

    /**
     * @param string $url
     * @param string $class
     * @param string $method
     * @param string $name
     * @param string $permission
     * @return Map
     */
    public function put(string $url, string $class, string $method, string $name = "", string $permission = "") : Map{
        $this->routes["put"][] = new Route($url, $class, $method, $name, $permission);
        return $this;
    }

    /**
     * @param string $url
     * @param string $class
     * @param string $method
     * @param string $name
     * @param string $permission
     * @return Map
     */
    public function delete(string $url, string $class, string $method, string $name = "", string $permission = "") : Map{
        $this->routes["delete"][] = new Route($url, $class, $method, $name, $permission);
        return $this;
    }

    /**
     * @param string $prefix
     * @param string $permission
     * @param Map $map
     * @return Map
     */
    public function group(string $prefix, string $permission, Map $map) : Map{
        foreach ($map->routes as $method => $routes){
            foreach ($routes as &$route){
                $route->permission = $route->permission ?? $permission;
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
