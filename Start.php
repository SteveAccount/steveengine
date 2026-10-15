<?php

namespace SteveEngine;

use Main\Main\MainController;
use SteveEngine\Data\Database;
use SteveEngine\Router\Router;
use SteveEngine\Safety\Header;
use SteveEngine\Safety\Request;

class Start {
    public function __construct(string $path) {
        //A közvetlen elérésű funckciók betöltése
        include $path . "/vendor/steveaccount/steveengine/Kernel.php";

        // A konfigurációs adatok alapján a Config osztály létrehozása
        Config::new()
            ->prepare($path);

        if (config()->get("mode") === "develope") {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            error_reporting(E_ALL);
        }

        //A Database osztály inicializálása
        Database::new()->prepare();
        Header::new()->prepare();
        
        //A Router osztály inicializálása, az útvonalak regisztrálása
        $router = Router::new();
        $router->map()
            ->get  ("/", MainController::class, "mainPage");

        // Útvonalak a controllerekből
        $files = findFile(implode(DIRECTORY_SEPARATOR, [config()->get("appPath"), "Moduls"]), "Controller", false);

        foreach ($files as $file) {
            $file = str_replace([config()->get("appPath") . DIRECTORY_SEPARATOR, ".php"], "", $file);
            $sep = DIRECTORY_SEPARATOR;
            $file = str_replace("Moduls", "", $file);

            $class = str_replace(DIRECTORY_SEPARATOR, "\\", $file);

            if (class_exists($class)) {
                $class::addRoutes($router);
            } else {
                toLog(["Rossz", $file, $class]);
            }
        }
        
        Build::build();

//        toLog($router);
        //A Request osztály létrehozása, a session, a user és a permission ellenőrzése
        if (Request::new()->prepare()->check()) {
            $router->routeMe();
        }
    }
}
