<?php

namespace SteveEngine\Router;

use SteveEngine\Config;
use SteveEngine\Safety\User;
use SteveEngine\Singleton;

class Router extends Singleton {
    public Map $map;

    public function map() : Map {
        $this->map = new Map();

        return $this->map;
    }

    public function routeMe() {
        $method     = request()->method();
        $routes     = $this->map->routes[$method] ?? [];

        foreach ($routes as $route) {
            $param      = null;
            $current    = request()->path();
            $pureRoute  = $route->path;

            if ($varStart = strpos($route->path, "{")) {
                $pureRoute  = substr($route->path, 0, $varStart);
                $current    = substr(request()->path(), 0, $varStart);
                $param      = substr(request()->path(), $varStart);
            }

            if ($pureRoute === $current || ($pureRoute . "/") === $current || $pureRoute === "/" . $current) {
                $class = $route->class;

                if (class_exists($class)) {
                    if (method_exists($class, $route->method)) {
                        $method = $route->method;

                        if ($myClass = new $class) {
                            if ($this->isPermissionOK($route, request()->user)) {
                                try{
                                    if ($param) {
                                        echo $myClass->$method($param);
                                    } else {
                                        echo $myClass->$method();
                                    }

                                } catch(\Exception $e) {
                                    db()->endTransaction(false);
                                    toLog($e);
                                    http_response_code($e->getCode());
                                    echo $e->getMessage();
                                }
                                return;
                            } else {
                                if (request()->user) {
                                    redirect("error403");
                                } else {
                                    redirect("login");
                                }
                            }
                        }
                    } else {
                        echo "Nincs ilyen funkció";
                        exit;
                    }
                } else {
                    echo "Nincs ilyen osztály";
                    exit;
                }
                break;
            }
        }
        redirect("error404");
    }

    public function getPathByRouteName(string $name) : ?string {
        foreach ($this->map->routes as $method => $routes) {
            foreach ($routes as $route) {
                if ($route->name === $name) {

                    return $route->path;
                }
            }
        }

        return null;
    }

    private function isPermissionOK(Route $route, ?User $user) : bool{
        if ($route->permission === []){
            return true;
        }
        if (!$user){
            return false;
        }
        foreach ($route->permission as $permission){
            if (in_array($permission, $user->getPermissions())){
                return true;
            }
        }
        return false;
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
