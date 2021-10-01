<?php

use SteveEngine\Config;
use SteveEngine\Data\Database;
use SteveEngine\Data\Setup;
use SteveEngine\Router\Map;
use SteveEngine\Router\Router;
use SteveEngine\Safety\Request;

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
$setup = new Setup();
if (!$setup->createDatabase("webcompany")){
    die ("Hiba történt az adatbázis létrehozása során.");
} else{
    die("Az adatbázis létrehozása megtörtént.");
}


//A Request osztály létrehozása
$request = Request::new()->prepare();

//A Router osztály inicializálása, az útvonalak regisztrálása
$router = Router::new();
$router->map()
    ->group     ("/wc", (new Map)
        ->get   ("", "Main\MainController", "pageMain"));

$router->routeMe();
