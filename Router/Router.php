<?php

namespace SteveEngine\Router;

use SteveEngine\Config;
use SteveEngine\Singleton;

class Router extends Singleton{
    /**
     * @var Map
     */
    public $map;

    public function map() : Map{
        $this->map = new Map();
        return $this->map;
    }

    public function routeMe(){
        $current = request()->path();
        $method = request()->method();
        $routes = isset($this->map->routes[$method]) ? $this->map->routes[$method] : [];

        foreach ($routes as $route) {
            if ($route->path === $current || ("/" . $route->path) === $current){
                $class = "";
//                if ($index = strpos($route->class, "Controller")){
//                    $namespace = substr($route->class, 0, $index);
//                    $class = "$namespace\\$route->class";
//                } else{
//                    $class = $route->class;
//                }
                $class = $route->class;
//                echo "class: ". $class;
                if (class_exists($class)){
                    if (method_exists($class, $route->method)){
                        $method = $route->method;
                        if ($myClass = new $class){
                            if (isset($myClass->isAdmin)){
                                if (!request()->user() || request()->user()->isAdmin != 1){
                                    header("Location: /login");
                                    return;
                                }
                            }
                            echo $myClass->$method();
                            return;
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

    public function getPathByRouteName( string $name ) : string{
        return $this->map[$name]->path;
    }

    public function loadRoutes() : Router{
        $routes = json_decode( file_get_contents( Config::get("appPath") . "/Config/routes.json"), true );
        foreach( $routes as $routeName => $routeInfo ){
            $this->map += [$routeName => new Route( $routeInfo["method"], $routeInfo["path"], $routeInfo["class"], $routeInfo["function"])];
        }
        return $this;
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
