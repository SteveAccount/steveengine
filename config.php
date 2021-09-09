<?php

namespace SteveEngine;

class Config extends Singleton{
    public static $settings = [];

    public static function start(){
        $settings = [];
        $path = self::$settings["appPath"] . "/Config/companies.php";
        self::$settings["companies"] = include $path;
    }

    public static function set( string $name, string $value ){
        self::$settings[$name] = $value;
    }

    public static function get( string $name ){
        return self::$settings[$name] ?? null;
    }

    public static function databaseInfo( string $companySign ) : array{
        return self::$settings["companies"][$companySign];
    }

    public static function page( string $pageName ) : array{
        global $appPath;
        $path = "$appPath/Config/pages/$pageName.json";
        return json_decode( File::loadJson( $path ), true );
    }

    public static function menu() : array{
        global $appPath;
        $path = "$appPath/Config/menu.json";
        return json_decode( File::loadJson( $path ), true );
    }

    public static function settings() : array{
        global $appPath;
        $path = "$appPath/Config/settings.json";
        return json_decode( File::loadJson( $path ), true );
    }
}