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
    ->group     ("/wc", "", (new Map)
        ->get   ("/login", "System\SystemController", "loginPage", "loginPage")
        ->post  ("/login", "System\SystemController", "login", "login")
        ->get   ("/registration", "System\SystemController", "regPage", "regPage")
        ->post  ("/registration", "System\SystemController", "reg", "reg")
        ->get   ("/", "Main\MainController", "mainPage", "mainPage")
        ->get   ("/migration", "System\SystemController", "migration")

        ->post  ("/globalSearch", "Main\MainController", "globalSearch")

        ->post  ("/modalPartner", "Administry\AdministryController", "getModal", "getModalPartner", ["partnersWrite"])

        ->group         ("/administry", "", (new Map)
            ->group     ("/partners", "", (new Map)
                ->post  ("/partnerspage", "Administry\AdministryController", "partnersPage", "partnersPage", ["partnersRead", "partnersWrite"])
                ->post  ("/getdata", "Administry\AdministryController", "getPartnersData", "getPartnersData", ["partnersRead", "partnersWrite"])
                ->post  ("/newpartner", "Administry\AdministryController", "newPartner", "newPartner", ["partnersWrite"])
            )

            ->group     ("/myCompanies", "", (new Map)
                ->post  ("/myCompaniesPage", "Administry\AdministryController", "myCompaniesPage", "myCompaniesPage", ["myCompaniesRead", "myCompaniesWrite"])
                ->post  ("/getdata", "Administry\AdministryController", "getMyCompaniesData", "getMyCompaniesData", ["myCompaniesRead", "myCompaniesWrite"])
                ->post  ("/newpartner", "Administry\AdministryController", "newPartner", "newPartner", ["myCompaniesWrite"])
            )
        )
    );

//A Request osztály létrehozása, a session, a user és a permission ellenőrzése
if (Request::new()->prepare()->check()){
    $router->routeMe();
}


