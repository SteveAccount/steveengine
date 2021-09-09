<?php

namespace SteveEngine\Safety;

use SteveEngine\Data\Model;

class User extends Model{
    public $id;
    public $name;
    public $email;
    public $passwordHash;

    public static function new() : User{
        return new self();
    }
}