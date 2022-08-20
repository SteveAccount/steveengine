<?php

/*
Fontos!!!
A vendor/steveaccount/steveengine/_copyToRoot mappa tartalmát be kell másolni a gyökérkönyvtárba.
A composer.json fájlba be kell illeszteni a következőt:
"autoload": {
    "psr-4": {
      "System\\": "Moduls/System"
    }
  }
Majd frissítsd az autoloadert!
*/

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

//Az engine működéséhez szükséges adatbázis létrehozása
//Az első alkalommal kell lefutnia.
//$setup = new Setup();
//if (!$setup->createDatabase("webcompany")){
//    die ("Hiba történt az adatbázis létrehozása során.");
//} else{
//    die("Az adatbázis létrehozása megtörtént.");
//}

//A Database osztály inicializálása
Database::new()->prepare(config()->get("databaseInfo")["main"]);

//A Router osztály inicializálása, az útvonalak regisztrálása
$router = Router::new();
$router->map()
    ->group     ("/", "", (new Map)
        ->get   ("/", SiteController::class, "mainPage", "mainPage")

        ->get   ("/login", SystemController::class, "loginPage", "loginPage")
        ->post  ("/login", SystemController::class, "login", "login")

    );

//A Request osztály létrehozása, a session, a user és a permission ellenőrzése
if (Request::new()->prepare()->check()){
    $router->routeMe();
}


