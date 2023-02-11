<?php

use SteveEngine\Config;
use SteveEngine\Data\Database;
use SteveEngine\Data\Setup;
use SteveEngine\Router\Map;
use SteveEngine\Router\Router;
use SteveEngine\Safety\Request;
use Site\SiteController;
use System\SystemController;

//Az autoloader indítása
include "vendor/autoload.php";

// A konfigurációs adatok alapján a Config osztály létrehozása
Config::new()
    ->prepare()
    ->set("appPath", __DIR__);

//A közvetlen elérésű funckciók betöltése
include "vendor/steveaccount/steveengine/Kernel.php";

// Hibák megjelenítése
if (isDev()) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

//Az engine működéséhez szükséges adatbázis létrehozása
//Az első alkalommal kell lefutnia.
//$setup = new Setup();
//if (!$setup->createDatabase("webcompany")){
//    die ("Hiba történt az adatbázis létrehozása során.");
//} else{
//    die("Az adatbázis létrehozása megtörtént.");
//}

//A Database osztály inicializálása
Database::new()->prepare(config()->get("databaseInfo")[config()->get("mode")]);

//A Router osztály inicializálása, az útvonalak regisztrálása
$router = Router::new();
$router->map()
    ->group     ("/", "", (new Map)
        ->get   ("/", SiteController::class, "mainPage", "mainPage")

        ->get   ("/login", SystemController::class, "loginPage", "loginPage")
        ->post  ("/login", SystemController::class, "login", "login")

        // Errors
        ->get   ("/error404", SystemController::class, "error404", "error404")
        ->get   ("/error403", SystemController::class, "error403", "error403")
    );

//A Request osztály létrehozása, a session, a user és a permission ellenőrzése
if (Request::new()->prepare()->check()){
    $router->routeMe();
}


