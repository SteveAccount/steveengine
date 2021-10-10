<?php

namespace SteveEngine\Router;

use SteveEngine\Config;
use SteveEngine\Singleton;

class Router extends Singleton{
    /**
     * @var Map
     */
    public Map $map;

    public function map() : Map{
        $this->map = new Map();
        return $this->map;
    }

    public function routeMe(){
        $current    = request()->path();
        $method     = request()->method();
        $routes     = $this->map->routes[$method] ?? [];

        foreach ($routes as $route) {
            if ($route->path === $current || ($route->path . "/") === $current){
                $class = "";
                
                $class = $route->class;
                if (class_exists($class)){
                    if (method_exists($class, $route->method)){
                        $method = $route->method;
                        if ($myClass = new $class){
                            if ($route->permission && request()->user && in_array($route->permission,  request()->user->getPermissions())){
                                echo $myClass->$method();
                                return;
                            }else{
                                response("Nincs jogosultságod a funkcióhoz.", 403);
                            }
                        }
                    } else{
                        echo "Nincs ilyen funkció";
                    }
                } else{
                    echo "Nincs ilyen osztály";
                }
                break;
            }
        }
        echo "404";
//        include \Config()->appPath . "/html/0404.html";
    }

    public function getPathByRouteName( string $name ) : ?string{
        foreach ($this->map->routes as $method => $routes){
            foreach ($routes as $route){
                if ($route->name === $name){
                    return $route->path;
                }
            }
        }
        return null;
    }

    private function getRouteByRequest(){
        foreach( $this->map as $routeName => $route ){
            if( $route->method == request()->method() && $route->path == request()->path() ){
                return $route;
            }
        }
        return false;
    }
}
