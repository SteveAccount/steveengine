<?php

namespace SteveEngine;

use Main\Main\MainController;
use SteveEngine\Data\Database;
use SteveEngine\Router\Router;
use SteveEngine\Safety\Header;
use SteveEngine\Safety\Request;

class Build {
    public function __construct() {
        
    }
    
    public static function build() {
        $files = findFile(implode(DIRECTORY_SEPARATOR, [config()->get("appPath"), "Moduls"]), ".js", false);
        $windows = "let windowClasses = {};\r\n";
        $modals = "let modalClasses = {};\r\n";
        
        foreach ($files as $file) {
            $fileInfo = pathinfo($file);
            
            if ($fileInfo["extension"] == "js") {
                if ($fileInfo["dirname"] !== "C:\\xampp\\htdocs\\wc3\\Moduls\\System\\Js") {
                    if (str_contains($fileInfo["basename"], "modal")) {
                        $modals .= file_get_contents($file);
                    } else {
                        $windows .= file_get_contents($file);
                    }
                }
            }
        }
        
        $fileWindows = implode(DIRECTORY_SEPARATOR, [config()->get("appPath"), "Public", "js", "windows.js"]);
        file_put_contents($fileWindows, $windows);
        $fileModals = implode(DIRECTORY_SEPARATOR, [config()->get("appPath"), "Public", "js", "modals.js"]);
        file_put_contents($fileModals, $modals);
    }
}
