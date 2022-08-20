<?php

namespace System;

use SteveEngine\ControllerForModulSystem;

class SiteController extends ControllerForModulSystem{
    public function mainPage(){
        return $this->twig->render("login.html.twig");
    }
}
