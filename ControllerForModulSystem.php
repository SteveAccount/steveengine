<?php

namespace SteveEngine;

use SteveEngine\Safety\Request;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class ControllerForModulSystem{
    protected Environment $twig;

    public function __construct(){
        $parts  = explode(DIRECTORY_SEPARATOR, get_class($this));
        $path   = implode(DIRECTORY_SEPARATOR, [config()->get("appPath"), "Moduls", $parts[0], "Templates"]);
        $loader = new FilesystemLoader($path);
        $twig   = new Environment($loader);
    }
}