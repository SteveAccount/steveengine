<?php

namespace SteveEngine;

class PostInstallScript {
    public static function script() {
        toLog(("uff"));
        file_put_contents("log2.php", "Uff");
        file_put_contents("log.php", "UffUff");
    }
}