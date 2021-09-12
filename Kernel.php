<?php

use SteveEngine\Data\Database;
use SteveEngine\IComparable;
use SteveEngine\Router\Router;
use SteveEngine\Safety\Request;
use SteveEngine\Validate\Validate;

/**
 * Return a singleton Route class what contents routes.
 * @param string $routeName
 * @return string
 */
function route(string $routeName ) : string{
    return Router::new()->getPathByRouteName( $routeName );
}

/**
 * Return a singleton Database class.
 * @return Database
 */
function db() : Database{
    return Database::new();
}

/**
 * Return a singleton Request class.
 * @return Request
 */
function request() : Request{
    return Request::new();
}

/**
 * Return a singleton Validate class.
 * @return Validate
 */
function validate() : Validate{
    return Validate::new();
}

/**
 * @param string $routeName
 */
function redirect(string $routeName ){
    $newRoute = route( $routeName );
    header( "Location: " . $newRoute );
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
    $fileContent = $isAppend ? file_get_contents("log.php") : "";
    $var_str = var_export($some, true);
    $var = "<?php\n\n\$some = $var_str;\n\n?>";
    $fileContent .= (new DateTime())->format("Y-m-d H:i:s");
    $fileContent .= $var;
    file_put_contents('log.php', $fileContent);
}