<?php

namespace Site;

use SteveEngine\ControllerForModulSystem;

class SiteController extends ControllerForModulSystem{
    public function mainPage(){
        return $this->twig->render("main.html.twig");
    }
}
