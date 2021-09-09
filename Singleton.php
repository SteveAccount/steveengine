<?php

namespace SteveEngine;

abstract class Singleton {
    private static $map = array();
 
    protected function __construct() {}

    public static function new() {
        $class = get_called_class();
        if (!isset(self::$map[$class])) {
             self::$map[$class] = new $class();
        }
        return self::$map[$class];
    }
}