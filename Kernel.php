<?php

use SteveEngine\Config;
use SteveEngine\Data\Database;
use SteveEngine\IComparable;
use SteveEngine\Router\Router;
use SteveEngine\Safety\Request;
use SteveEngine\Validate\Validate;
use Admin\Services\Translate;


/**
 * Returns a singleton Config class.
 * @return Config
 */
function config() : Config{
    return Config::new();
}

/**
 * Returns a singleton Route class what contents routes.
 * @param string $routeName
 * @return string
 */
function route(string $routeName) : ?string{
    return Router::new()->getPathByRouteName($routeName);
}

/**
 * Returns a singleton Database class.
 * @return Database
 */
function db() : Database{
    return Database::new();
}

/**
 * Returns a singleton Request class.
 * @return Request
 */
function request() : Request{
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
function validate() : Validate{
    return Validate::new();
}

/**
 * @param string $routeName
 */
function redirect(string $routeName){
    if ($route = route($routeName)){
        toLog($route);
        header( "Location: " . $route );
        exit;
    }
    toLog("Nincs $routeName nevű útvonal.");
    http_response_code(404);
    die;
}

function response($message, int $code = 200, bool $isJson = true){
    http_response_code($code);
    $message = $isJson ? json_encode($message) : $message;
    return $message;
}

/**
 * @return string
 */
function token() : string{
    return request()->session()->newToken();
}

/**
 * @param string $class
 * @return string
 */
function getClassname(string $class ) : string{
    $parts = explode( "\\", $class );
    return $parts[count( $parts ) - 1];
}

/**
 * @param DOMNode $node
 * @return DOMNode|null
 */
function castDOMNodeToDOMElement(DOMNode $node ){
    if ( $node ){
        if ( $node->nodeType === XML_ELEMENT_NODE ) {
            return $node;
        }
    }
    return null;
}

/**
 * @param mixed $printingObject
 * @param bool $isExit
 */
function console($printingObject, bool $isExit = false ){
    if( is_object( $printingObject) || is_array( $printingObject )){
        echo "<pre>";
        var_dump( $printingObject );
    }else{
        echo "<pre>";
        echo $printingObject;
    }
    if( $isExit ){
        exit;
    }
    echo "<br>";
}

/**
 * @param IComparable $object1
 * @param IComparable $object2
 * @return bool
 */
function compareObject(IComparable $object1, IComparable $object2 ) : bool{
    if( get_class( $object1 ) !== get_class( $object2 )){
        return false;
    }
    $vars1 = $object1->getVars();
    $vars2 = $object2->getVars();
    foreach( $vars1 as $key => $value){
        if( $vars1[$key] !== $vars2[$key] ){
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
function toLog($some, $isAppend = true){
    $fileContent     = $isAppend ? file_get_contents("log.php") : "";
    $fileContent    .= "\n\n" . (new DateTime())->format("Y-m-d H:i:s") . "\n";
    $fileContent    .= var_export($some, true);
    file_put_contents('log.php', $fileContent);
}

/**
 * Return true if software state is "dev", else false.
 * @return  bool
 */
function isDev() : bool {
    return config()->get("mode") !== "product";
}