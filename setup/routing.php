<?php

namespace SteveEngine\Setup;

use SteveEngine\Config;
use SteveEngine\File;

class Routing{
    public function __construct(){
        $this->saveRoutes( $this->getRoutesOfModuls());
    }

    private function getRoutesOfModuls() : array{
        $routes = [];
        $path = Config::get("appPath") . "/Moduls/";
        $folders = File::getFolders( $path );
        foreach( $folders as $folder ){
            $file = "$folder/Config/routes.json";
            $routes += json_decode( file_get_contents( $file), true );
        }
        return $routes;
    }
    
    private function saveRoutes( array $routes ) : void{
        $path = Config::get("appPath") . "/Config/routes.json";
        file_put_contents( $path, json_encode( $routes ));
    }
}