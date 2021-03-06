<?php

namespace SteveEngine\Router;

/**
 * Class Route
 * @package SteveEngine\Router
 */
class Route{
    /**
     * @var string
     */
    public string $path;
    /**
     * @var string
     */
    public string $class;
    /**
     * @var string
     */
    public string $method;
    /**
     * @var string
     */
    public string $name;
    /**
     * @var array
     */
    public array $permission;

    /**
     * Route constructor.
     * @param string $path
     * @param string $class
     * @param string $method
     * @param string $name
     * @param string $permission
     */
    public function __construct(string $path, string $class, string $method, string $name, array $permission){
        $this->path         = $path;
        $this->class        = $class;
        $this->method       = $method;
        $this->name         = $name;
        $this->permission   = $permission;
    }
}