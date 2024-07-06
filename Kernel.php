<?php

use SteveEngine\Config;
use SteveEngine\Data\Database;
use SteveEngine\IComparable;
use SteveEngine\Router\Router;
use SteveEngine\Safety\Request;
use SteveEngine\Translate;
use SteveEngine\Validate\Validate;

/**
 * Returns a singleton Config class.
 * @return Config
 */
function config() : Config {
    return Config::new();
}

/**
 * Returns a singleton Route class what contents routes.
 * @param string $routeName
 * @return string
 */
function route(string $routeName) : ?string {
    return Router::new()->getPathByRouteName($routeName);
}

/**
 * Returns a singleton Database class.
 * @return Database
 */
function db() : Database {
    return Database::new();
}

/**
 * Returns a singleton Request class.
 * @return Request
 */
function request() : Request {
    return Request::new();
}

function trans(string $huString) {
    $translate = Translate::new();

    return $translate->trans($huString);
}

function isPermission(string $permission) : bool {
    return in_array($permission, \request()->user->permissions);
}

/**
 * Returns a singleton Validate class.
 * @return Validate
 */
function validate() : Validate {
    return Validate::new();
}

/**
 * @param string $routeName
 */
function redirect(string $routeName) {
    if ($route = route($routeName)) {
        header( "Location: " . $route );
        exit;
    }

    toLog("Nincs $routeName nevű útvonal.");
    http_response_code(404);
    die;
}

function response($message, int $code = 200, bool $isJson = true) {
    http_response_code($code);
    $message = $isJson ? json_encode($message) : $message;

    return $message;
}

/**
 * @return string
 */
function token() : string {
    return request()->session()->newToken();
}

/**
 * @param string $class
 * @return string
 */
function getClassname(string $class ) : string {
    $parts = explode( "\\", $class );

    return $parts[count( $parts ) - 1];
}

/**
 * @param DOMNode $node
 * @return DOMNode|null
 */
function castDOMNodeToDOMElement(DOMNode $node ) {
    if ( $node ){
        if ( $node->nodeType === XML_ELEMENT_NODE ) {
            return $node;
        }
    }

    return null;
}

/**
 * @param IComparable $object1
 * @param IComparable $object2
 * @return bool
 */
function compareObject(IComparable $object1, IComparable $object2 ) : bool {
    if( get_class( $object1 ) !== get_class( $object2 )) {
        return false;
    }

    $vars1 = $object1->getVars();
    $vars2 = $object2->getVars();
    foreach ($vars1 as $key => $value) {
        if($vars1[$key] !== $vars2[$key]) {
            return false;
        }
    }

    return true;
}

/**
 * Write something to log.txt file to root folder.
 * @param mixed $some
 * @param bool $isAppend
 */
function toLog($some, $isAppend = true, $filename = "log.php") {
    $fileContent     = $isAppend ? file_get_contents($filename) : "";
    $fileContent    .= "\n\n" . (new DateTime())->format("Y-m-d H:i:s") . "\n";
    $fileContent    .= var_export($some, true);
    file_put_contents($filename, $fileContent);
}

/**
 * Return true if software state is "dev", else false.
 * @return  bool
 */
function isDev() : bool {
    return config()->get("mode") !== "product";
}

function removeDirectory(string $folder) {
    if (! is_dir($folder)) {
        throw new InvalidArgumentException("$folder must be a directory");
    }
    if (substr($folder, strlen($folder) - 1, 1) != '/') {
        $folder .= '/';
    }
    
    $files = glob($folder . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            removeDirectory($file);
        } else {
            unlink($file);
        }
    }
    
    rmdir($folder);
}

function getSettings(?string $key = null) {
    if ($key) {
        $query = "select * from settings where settingKey = '$key'";

        return db()->query($query)->scalar("settingValue");
    } else {
        $query      = "select * from settings";
        $settings   = db()->query($query)->select();
        $result     = [];

        foreach ($settings as $setting) {
            $result[$setting->settingKey] = json_decode($setting->settingValue);
        }

        return $result;
    }
}

function getUrl(string $name) {
    if ($name) {
        $name = strtolower($name);
        $name = str_replace(["ö", "ü", "ó", "ő", "ú", "é", "á", "ű", "í", " "], ["o", "u", "o", "o", "u", "e", "a", "u", "i", "-"], $name);
        $name = str_replace(["/", ","], "", $name);
        $name = preg_replace("/[^a-zA-Z0-9-]/", "", $name);
    }

    return $name;
}

function getPureFilename(string $filename) {
    $basename   = pathinfo($filename, PATHINFO_FILENAME);
    $extension  = pathinfo($filename, PATHINFO_EXTENSION);
    $filename   = getUrl($basename) . "." . $extension;

    return $filename;
}