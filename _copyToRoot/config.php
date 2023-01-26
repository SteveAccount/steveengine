<?php

return [
    "mode"                      => "develope", // develope vagy product

    // Adatbázis kapcslatok
    "databaseInfo"              => [
        "main"                  => [
            "server"            => "localhost",
            "port"              => 3306,
            "username"          => "root",
            "password"          => "",
            "database"          => "webcompany"
        ],
    ],

    // Rögzített útvonalnevek
    "loginPage"                 => "loginPage",
    "mainPage"                  => "mainPage",
    "registrationPage"          => "regPage",

    // Egyelőre használaton kívűl.
    "isApi"                     => false,

    // True esetén a használathoz bejelentkezett felhasználó kell.
    // Ha session-ből nem tudja a Request osztály User paraméterét betölteni, akkor azonnal átirányít a login oldalra.
    // Ha nincs login oldal regisztrálva az index.php-ban, akkor 404-es hibakód lesz a válasz.
    "hasUser"                   => false,

    // Ha engedélyezett a kívülről történő regisztráció
    "hasRegistration"           => false,

    // A session érvényességének ideje órában.
    "sessionExpirationInterval" => 8,

    // Jelszószabályzat
    "passwordRules"             => [
        "minLength"             => 8,
        "lowers"                => 8,
        "uppers"                => 0,
        "numbers"               => 0,
        "specials"              => 0,
    ],
];
