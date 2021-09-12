<?php

use SteveEngine\Safety\Request;

//Az autoloader indítása
include "vendor/autoload.php";

//A közvetlen elérésű funckciók betöltése
include "vendor/steveaccount/steveengine/Kernel.php";

//A Request osztály létrehozása
$request = Request::new()->prepare();

