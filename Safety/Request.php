<?php

namespace SteveEngine\Safety;

use DateTime;
use SteveEngine\Convert\Sha3;
use SteveEngine\Singleton;

/**
 * Class Request
 * @package SteveEngine\Safety
 */
class Request extends Singleton{
    /**
     * @var string
     */
    private string $sessionId;
    /**
     * @var string
     */
    private string $method;
    /**
     * @var string
     */
    private string $path;
    /**
     * @var string
     */
    private string $ip;
    /**
     * @var string
     */
    private string $csrf;
    /**
     * @var array
     */
    private array $data;
    /**
     * @var User|null
     */
    public ?User $user;
    /**
     * @var Session
     */
    private Session $session;

    /**
     * A Request osztály feltöltése.
     * @return Request
     */
    public function prepare() : Request{
        session_start();

        $this->method   = strtolower( $_POST["_method"] ?? $_SERVER["REQUEST_METHOD"] );
        $index          = strpos($_SERVER["REQUEST_URI"], "?");
        if ($index){
            $this->path(substr($_SERVER["REQUEST_URI"], 0, $index));
        } else{
            $this->path($_SERVER["REQUEST_URI"]);
        }
        $this->ip( filter_var( $this->getIp() ));
        $this->sessionId    = $_SESSION["sessionId"] ?? "";
        $this->csrf         = $_POST["csrf"] ?? "";
        $this->data         = $_POST + $_GET;
        return $this;
    }


    /**
     * @throws \Exception
     */
    public function check() : bool{
        //Ha nincs sessionId, akkor csinájunk, egyébként a session és a user betöltése.
        $this->session  = Session::getBySessionId($this->sessionId) ?? Session::new();
        $this->user = User::selectById($this->session->userId);

        //Ha nem kell a programhoz User, akkor mehet tovább.
        if (!config()->get("hasUser")){
            return true;
        } else {
            if (config()->get("noUserRoutes") && in_array($this->path, config()->get("noUserRoutes"))) {
                return true;
            }
        }
        
        //Ha a method GET, az útvonal loginPage vagy regPage, akkor mehet tovább.
        if ($this->method === "get" && ($this->path === route(config()->get("loginPage")) || $this->path === route("regPage"))){
            return true;
        }
        
        //Ha a method POST, az útvonal login vagy reg, akkor mehet tovább.
        if ($this->method === "post" && ($this->path === route(config()->get("loginPage")) || $this->path === route("reg"))){
            return true;
        }

        //Minden egyéb esetben érvényes Session és adatbázisban szereplő User kell.
        if  (!(isset($this->user) && $this->session->expirationDate > (new \DateTime())->format("Y-m-d H:i:s"))){
            if ($this->method === "post"){
                http_response_code(401);
                return false;
            }
            redirect( "loginPage");
        }
        return true;
    }

    /**
     * @param string $method
     * @return string|Request
     */
    public function method(string $method = ""){
        if( $method == ""){
            return $this->method;
        }
        $this->method = $method;
        return $this;
    }

    /**
     * @param string $path
     * @return string|Request
     */
    public function path(string $path = ""){
        if( $path == ""){
            return $this->path;
        }
        $this->path = $path;
        return $this;
    }

    /**
     * @param string $ip
     * @return string|Request
     */
    public function ip(string $ip = ""){
        if( $ip == ""){
            return $this->ip;
        }
        $this->ip = $ip;
        return $this;
    }

    /**
     * @param Session|null $session
     * @return Session|Request
     */
    public function session(Session $session = null){
        if( !$session ){
            return $this->session;
        }
        $this->session          = $session;
        $this->sessionId        = $session->sessionId;
        $_SESSION["sessionId"]  = $session->sessionId;
        return $this;
    }

    /**
     * @param string $sessionId
     * @return string|Request
     */
    public function sessionId(string $sessionId = ""){
        if( $sessionId === "" ){
            return $this->sessionId;
        }
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function token(){
        return $this->token;
    }

    /**
     * @param $findKey
     * @return array
     */
    public function more($findKey ) : array
    {
        $keys = is_array( $findKey ) ? $findKey : [$findKey];
        $result = [];
        foreach( $keys as $key){
            if(isset($this->data[$key])){
                $result += [$key => $this->data[$key]];
            }
        }
        return $result;
    }

    /**
     * @param $findKey
     * @return mixed|null
     */
    public function only($findKey ){
        return $this->data[$findKey] ?? null;
    }

    /**
     * @return mixed
     */
    public function all(){
        return $this->data;
    }

    /**
     * @return string
     */
    private function getIp() : string{
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
            $ip = $_SERVER['HTTP_CLIENT_IP'];  
        }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
        }else{  
            $ip = $_SERVER['REMOTE_ADDR'];  
        }  
        return $ip;  
    }  
}