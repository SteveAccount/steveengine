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
    public string   $monogram;
    public ?string  $image;
    public string   $passwordHash;
    public string   $permissions;
    public ?string  $startPage;

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
            "password"      => Field::password()->label("Új jelszó")->message("A jelszó 5-20 karekter lehet és betűket, számokat tartalmazhat.")->required(),
            "passwordAgain" => Field::password()->label("Új jelszó megismétlése")->message("A jelszó 5-20 karekter lehet és betűket, számokat tartalmazhat.")->required(),
        ];
    }

    public function getPermissions() : array{
        return json_decode($this->permissions, 1);
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

    public function generatePassword() {
        $countOfLower   = 0;
        $countOfUpper   = 0;
        $countOfNumber  = 0;
        $countOfSpecial = 0;
        $types          = ["lower", "upper", "number", "special"];
        $chars          = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "x", "y", "w", "z"];
        $numbers        = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
        $specials       = ["-", "_", "!", "+", "/", "=", "(", ")"];

        $passwordRules  = config()->get("passwordRules");
        $password       = "";
        do {
            $type = $types[rand(0, 3)];
            switch($type) {
                case "lower":
                    if ($countOfLower < $passwordRules["lowers"]) {
                        $password .= $chars[rand(0, 25)];
                        $countOfLower++;
                    }
                    break;
                case "upper":
                    if ($countOfUpper < $passwordRules["uppers"]) {
                        $password .= strtoupper($chars[rand(0, 25)]);
                        $countOfUpper++;
                    }
                    break;
                case "number":
                    if ($countOfNumber < $passwordRules["numbers"]) {
                        $password .= $numbers[rand(0, 9)];
                        $countOfNumber++;
                    }
                    break;
                case "special":
                    if ($countOfSpecial < $passwordRules["specials"]) {
                        $password .= $specials[rand(0, 7)];
                        $countOfSpecial++;
                    }
            }

        } while (!($countOfLower === $passwordRules["lowers"] && $countOfNumber === $passwordRules["numbers"] && $countOfSpecial === $passwordRules["specials"] && $countOfUpper === $passwordRules["uppers"]));

        return $password;
    }

//    public function closeWidget(int $id) {
//        $widgets = json_decode($this->startPage, 1);
//
//        foreach ($widgets as $index => $widgetGroup) {
//            foreach ($widgetGroup["widgets"] as $widgetIndex => $widget) {
//                if ((int)$widget["id"] === $id) {
//                    if (count($widgetGroup["widgets"]) === 1) {
//                        unset ($widgets[$index]);
//                    } else {
//                        unset ($widgets[$index]["widgets"][$widgetIndex]);
//                    }
//                    break 2;
//                }
//            }
//        }
//
//        $this->startPage = json_encode($widgets);
//        $this->update();
//
//        return [];
//    }
//
//    public function addGroup(string $title) {
//        $widgets    = json_decode($this->startPage, 1);
//        $widgets[]  = [
//            "label"     => $title,
//            "widgets"   => []
//        ];
//
//        $this->startPage = json_encode($widgets);
//        $this->update();
//
//        return [];
//    }
//
//    public function removeGroup(string $title) {
//        $widgets = json_decode($this->startPage, 1);
//
//        foreach ($widgets as $index => $widgetGroup) {
//            if ($widgetGroup["label"] === $title) {
//                unset ($widgets[$index]);
//                break;
//            }
//        }
//
//        $this->startPage = json_encode($widgets);
//        $this->update();
//
//        return [];
//    }
//
//    public function displayedWidgets() {
//        $result     = [];
//        $widgets    = json_decode($this->startPage, 1);
//
//        foreach ($widgets as $widgetGroup) {
//            foreach ($widgetGroup["widgets"] as $widget) {
//                array_push($result, $widget["id"]);
//            }
//        }
//
//        return $result;
//    }
//
//    public function addWidget(string $group, int $widgetId) {
//        $widgets    = json_decode($this->startPage, 1);
//
//        foreach ($widgets as $index => $widgetGroup) {
//            if ($widgetGroup["label"] === $group) {
//                $widgets[$index]["widgets"][] = [
//                    "id"    => $widgetId,
//                ];
//
//                break;
//            }
//        }
//
//        $this->startPage = json_encode($widgets);
//        $this->update();
//
//        return db()->query("select * from infoitems where id = $widgetId")->select()[0];
//    }
}