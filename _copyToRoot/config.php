<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

return [
    "mode"                      => "develope", // develope, product, testServer

    // Adatbázis kapcslatok
    "databaseInfo"              => [
        "develope"              => [
            "server"            => "localhost",
            "port"              => 3306,
            "username"          => "root",
            "password"          => "",
            "database"          => "healthdiary"
        ],
        "product"               => [
            "server"            => "localhost",
            "port"              => 3306,
            "username"          => "webcompa_health",
            "password"          => "healthAdmin",
            "database"          => "webcompa_health"
        ],
    ],

    //Rögzített útvonalnevek
    "loginPage"                 => "loginPage",
    "mainPage"                  => "mainPage",

    //Egyelőre használaton kívűl.
    "isApi"                     => false,

    //True esetén a használathoz bejelentkezett felhasználó kell.
    //Ha session-ből nem tudja a Request osztály User paraméterét betölteni, akkor azonnal átirányít a login oldalra.
    //Ha nincs login oldal regisztrálva az index.php-ban, akkor 404-es hibakód lesz a válasz.
    "hasUser"                   => true,

    //A session érvényességének ideje órában.
    "sessionExpirationInterval" => 8,

    // A rendezvényre való belépés (építés-bontás) határdátumai
    "extendedStartDate"         => "2023-08-21",
    "extendedEndDate"           => "2023-08-31",

    // A rendezvényre való belépés (főrendezvény) határdátumai
    "himasStartDate"            => "2023-08-26",
    "himasEndDate"              => "2023-08-27",

    // A rendezvényre való belépés (főrendezvény kezdőnappal) határdátumai
    "himasFullStartDate"        => "2023-08-25",
    "himasFullEndDate"          => "2023-08-27",

    // Jelszószabályzat
    "passwordRules"             => [
        "minLength"             => 8,
        "lowers"                => 8,
        "uppers"                => 0,
        "numbers"               => 0,
        "specials"              => 0,
    ],

    // hasUser = true esetén érvényes, ha szükség lenne olyan útvonalra, ahol nincs bejelentkezett felhasználó.
    // A tömbben az útvonalakat kell megadni.
    "noUserRoutes"              => [
        "/himasUserRegistration",
        "/himasMediaRegistration",
    ],

    // Email
    "SMTPDebug"     => SMTP::DEBUG_SERVER,
    "isSMTP"        => true,
    "Host"          => "smtp.gmail.com",
    "SMTPAuth"      => true,
    "Username"      => "boroczistvan0396@gmail.com",
    "Password"      => "Kukucska2022",
    "SMTPSecure"    => PHPMailer::ENCRYPTION_SMTPS,
    "Port"          => 465,
    "FromAddress"   => "boroczistvan0396@gmail.com",
    "FromName"      => "HIMAS admin",
];
