<?php

namespace SteveEngine\Safety;

use DateTime;
use SteveEngine\Convert\Sha3;
use SteveEngine\Singleton;

class Request extends Singleton{
    private string  $sessionId;
    private string  $method;
    private string  $path;
    private string  $ip;
    private array   $data;
    public ?User    $user;
    public Session $session;
    public string   $lang;

    public function prepare() : Request{
        session_start();

        $this->method               = strtolower( $_POST["_method"] ?? $_SERVER["REQUEST_METHOD"] );
        $index                      = strpos($_SERVER["REQUEST_URI"], "?");
        $requestUriWithoutAnchor    = $_SERVER["REQUEST_URI"] !== '/'
            ? rtrim(substr($_SERVER["REQUEST_URI"],strpos($_SERVER["REQUEST_URI"], "#")),"/")
            : "/";

        $index = strpos($requestUriWithoutAnchor, "?");
        if ($index){
            $this->path(substr($requestUriWithoutAnchor, 0, $index));
        } else{
            $this->path($requestUriWithoutAnchor);
        }
        $this->ip( filter_var( $this->getIp() ));
        $this->sessionId    = $_SESSION["sessionId"] ?? "";
        $this->csrf         = $_POST["csrf"] ?? "";
        $this->data         = $_POST + $_GET + (json_decode(file_get_contents("php://input")) ?? []);

        if (!isset($_SESSION["lang"])) {
            $_SESSION["lang"] = config()->get("lang");
        }

        $this->lang = $_SESSION["lang"];

        return $this;
    }

    public function check() : bool {
        //Ha nincs sessionId, akkor csinájunk, egyébként a session és a user betöltése.
        $this->session  = Session::getBySessionId($this->sessionId) ?? Session::new();
        $this->user     = User::selectById($this->session->pseudoUserId ?? $this->session->userId);

        // Várt útvonal és id, valamint az ellenőrzőkódok vizsgálata
        if ($this->method === "post" && isset($this->session->checkRoute)) {
            if ($this->session->checkRoute === $this->path) {
                if (isset($this->data["checkCode1"])
                    && isset($this->data["checkCode2"])
                    && $this->data["checkCode1"] === $this->session->checkCode1
                    && $this->data["checkCode2"] === $this->session->checkCode2
                    && (
                        $this->session->checkId && $this->session->checkId === $this->data["id"] || !$this->session->checkId
                    )
                ) {
                    $this->session->checkCode1  = null;
                    $this->session->checkCode2  = null;
                    $this->session->checkRoute  = null;
                    $this->session->checkId     = null;
                    $this->session->update();

                    return true;
                } else {
                    die ("Hacking");
                }
            }
        }

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

    public function method(string $method = "") {
        if( $method == ""){
            return $this->method;
        }

        $this->method = $method;

        return $this;
    }

    public function path(string $path = "") {
        if( $path == ""){
            return $this->path;
        }

        $this->path = $path;

        return $this;
    }

    public function ip(string $ip = "") {
        if( $ip == "") {
            return $this->ip;
        }

        $this->ip = $ip;

        return $this;
    }

    public function session(Session $session = null) {
        if( !$session ) {
            return $this->session;
        }

        $this->session          = $session;
        $this->sessionId        = $session->sessionId;
        $_SESSION["sessionId"]  = $session->sessionId;

        return $this;
    }

    public function sessionId(string $sessionId = "") {
        if( $sessionId === "" ) {
            return $this->sessionId;
        }


        $this->sessionId = $sessionId;
        return $this;
    }

    public function token() {
        return $this->token;
    }

    public function more($findKey ) : array {
        $keys   = is_array( $findKey ) ? $findKey : [$findKey];
        $result = [];

        foreach( $keys as $key) {
            if(isset($this->data[$key])) {
                $result += [$key => $this->data[$key]];
            }
        }

        return $result;
    }

    public function only($findKey ) {
        return $this->data[$findKey] ?? null;
    }

    public function all(){
        return $this->data;
    }

    private function getIp() : string {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }
}