<?php

namespace SteveEngine\Router;

class Route{
    public $path;
    public $class;
    public $method;

    public function __construct( string $path, string $class, string $method ){
        $this->path = strtolower( $path );
        $this->class = $class;
        $this->method = $method;
    }
}