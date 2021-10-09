<?php

namespace SteveEngine\Router;

class Route{
    public $path;
    public $class;
    public $method;
    public $name;

    public function __construct( string $path, string $class, string $method, string $name ){
        $this->path     = strtolower( $path );
        $this->class    = $class;
        $this->method   = $method;
        $this->name     = $name;
    }
}