<?php

namespace System;

use SteveEngine\ControllerForModulSystem;
use SteveEngine\Convert\Sha3;
use SteveEngine\Safety\Session;
use SteveEngine\Safety\User;

class SystemController extends ControllerForModulSystem {
    public function loginPage() {
        return $this->twig->render("login.html.twig");
    }

    public function login() {
        $data = request()->all();

        if (validate()->fields(User::getFieldsForLogin())->check($data)) {
            if ($user = User::selectByWhere(["email" => $data["email"], "passwordHash" => Sha3::hash($data["password"], 512)], true)){
                Session::new($user->id);

                return response("");
            }
        }

        return response("Hibás belépési adatok.", 422);
    }

    public function error404() {
        return $this->twig->render("error404.html.twig");
    }
}
