<?php

namespace SteveEngine\Safety;

use SteveEngine\Data\Model;
use SteveEngine\Validate\Field;

/**
 * Class User
 * @package SteveEngine\Safety
 */
class User extends Model{
    /**
     * @var int
     */
    public int $id;
    /**
     * @var string
     */
    public string $name;
    /**
     * @var string
     */
    public string $email;
    /**
     * @var string
     */
    public string $passwordHash;

    /**
     * @return User
     */
    public static function new() : User{
        return new self();
    }

    /**
     * @return array
     */
    public static function getFieldsForLogin() : array{
        return [
            "email"     => Field::emailAddress()->message("Nem megfelelő emailcím."),
            "password"  => Field::password()->message("A jelszó 5-20 karekter lehet és betűket, számokat tartalmazhat.")
        ];
    }
}