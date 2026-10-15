<?php

namespace SteveEngine\Safety;

use SteveEngine\Convert\Sha3;
use SteveEngine\Data\Model;
use SteveEngine\Validate\Field;

/**
 * Class User
 * @package SteveEngine\Safety
 */
class User extends Model {
    public static $tableName = "users";

    public int      $id;
    public string   $name;
    public string   $email;
    public string   $passwordHash;
    public int      $isActive;
    public int      $createdBy;
    public string   $createdAt;
    public ?int     $modifiedBy;
    public ?string  $modifiedAt;

    public static function new() : User {
        return new self();
    }

    public static function getFieldsForLogin() : array  {
        return [
            "email"     => Field::emailAddress()->message("Nem megfelelő emailcím."),
            "password"  => Field::password()->message("A jelszó 5-20 karekter lehet és betűket, számokat tartalmazhat.")
        ];
    }

    public static function getFieldsForChangePassword() : array {
        return [
            "oldPassword"   => Field::password()->label("Régi Jelszó")->message("A Régi jelszónak az emailben kapott jelszót használd.")->required(),
            "password"      => Field::password()->label("Új jelszó")->message("A jelszó 5-20 karekter lehet és betűket, számokat tartalmazhat.")->required(),
            "passwordAgain" => Field::password()->label("Új jelszó megismétlése")->message("A jelszó 5-20 karekter lehet és betűket, számokat tartalmazhat.")->required(),
        ];
    }

    public function getPermissions() : array{
        $userId = request()->user->id;
        $query = "
            select *
            from permissions as p 
            left join users_permissions_rel as upr on p.id = upr.permissionId
            where upr.userId = $userId 
        ";
        $permissions = db()->query($query)->select() ?? [];
        $permissionArray = [];
        foreach($permissions as $permission) {
            $permissionArray[] = $permission->permission;
        }

        return $permissionArray;
    }

    public static function changePassword() {
        $password = request()->only("oldPassword");
        $passwordHash = Sha3::hash($password, 512);

        if ($passwordHash !== request()->user->passwordHash) {
            throw new \Exception(json_encode(["oldPassword" => "A jelenlegi jelszó hibás."]), 422);
        }

        $data = request()->all();
        if (validate()->fields(self::getFieldsForChangePassword())->check($data)) {
            if ($data["password"] === $data["passwordAgain"]) {
                $user = request()->user;
                $user->passwordHash = Sha3::hash($data["password"], 512);
                $user->update();
            } else {
                throw new \Exception(json_encode(["password" => "Az új jelszó és a megismételt jelszó nem egyezik."]), 422);
            }
        } else {
            throw new \Exception(json_encode(validate()->getErrors()), 422);
        }
    }

    public function generatePassword(array $passwordRules = null) {
        $countOfLower   = 0;
        $countOfUpper   = 0;
        $countOfNumber  = 0;
        $countOfSpecial = 0;
        $types          = ["lower", "upper", "number", "special"];
        $chars          = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "x", "y", "w", "z"];
        $numbers        = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
        $specials       = ["-", "_", "!", "+", "/", "=", "(", ")"];

        if (!$passwordRules) {
            $passwordRules  = config()->getPasswordRules();
        }

        $password       = "";
        do {
            $freeChars = $passwordRules["minLength"] - strlen($password);
            $type = $types[rand(0, 3)];
            toLog([$password, $freeChars, $passwordRules]);
            switch($type) {
                case "lower":
                    if ($passwordRules["lowers"] || $freeChars > ($passwordRules["uppers"] + $passwordRules["numbers"] + $passwordRules["specials"])) {
                        $password .= $chars[rand(0, 25)];
                        $passwordRules["lowers"]--;
                    }
                    break;
                case "upper":
                    if ($passwordRules["uppers"] || $freeChars > ($passwordRules["lowers"] + $passwordRules["numbers"] + $passwordRules["specials"])) {
                        $password .= strtoupper($chars[rand(0, 25)]);
                        $passwordRules["uppers"]--;
                    }
                    break;
                case "number":
                    if ($passwordRules["numbers"] || $freeChars > ($passwordRules["uppers"] + $passwordRules["lowers"] + $passwordRules["specials"])) {
                        $password .= $numbers[rand(0, 9)];
                        $passwordRules["numbers"]--;
                    }
                    break;
                case "special":
                    if ($passwordRules["specials"] || $freeChars > ($passwordRules["uppers"] + $passwordRules["numbers"] + $passwordRules["lowers"])) {
                        $password .= $specials[rand(0, 7)];
                        $passwordRules["specials"]--;
                    }
            }

        } while (strlen($password) < $passwordRules["minLength"]);

        return $password;
    }

    public function hasPermission(string $permission) : bool {
        $query = "
            select *
            from permissions as p
            left join users_permissions_rel as upr on upr.permissionId = p.id
            where p.permission = '$permission'
        ";

        $permissions = Permission::selectByQuery($query) ?? [];

        return count($permissions) === 1;
    }

    public function checkPassword(string $password, array $passwordRules = null) : bool {
        $result = true;
        $countOfLower   = 0;
        $countOfUpper   = 0;
        $countOfNumber  = 0;
        $countOfSpecial = 0;
        $types          = ["lower", "upper", "number", "special"];
        $chars          = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "x", "y", "w", "z"];
        $numbers        = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
        $specials       = ["-", "_", "!", "+", "/", "=", "(", ")"];

        if (!$passwordRules) {
            $passwordRules  = config()->getPasswordRules();
        }

        if (strlen($password) >= $passwordRules["minLength"]) {
            for ($i = 0; $i < strlen($password); $i++) {
                $char = $password[$i];

                if (in_array($char, $chars)) {
                    $countOfLower++;
                }
                if (in_array($char, $numbers)) {
                    $countOfNumber++;
                }
                if (in_array($char, $specials)) {
                    $countOfSpecial++;
                }
                if ($char !== strtolower($char)) {
                    $char = strtolower($char);
                    if (in_array($char, $chars)) {
                        $countOfUpper++;
                    }
                }
            }

            if ($countOfLower < $passwordRules["lowers"]
            || $countOfNumber < $passwordRules["numbers"]
            || $countOfSpecial < $passwordRules["specials"]
            || $countOfUpper < $passwordRules["uppers"]) {
                $result = false;
            }
        } else {
            $result = false;
        }

        return $result;
    }
}