<?php

namespace SteveEngine\Safety;

use DateTime;
use SteveEngine\Convert\Sha3;
use SteveEngine\Singleton;

class Request extends Singleton{
    private $sessionId;
    private $method;
    private $path;
    private $ip;
    private $token;
    private $data;
    private $user;
    private $session;

    public function prepare() : Request{
        $this->method = strtolower( $_POST["_method"] ?? $_SERVER["REQUEST_METHOD"] );
        $index = strpos($_SERVER["REQUEST_URI"], "?");
        if ($index){
            $this->path(substr($_SERVER["REQUEST_URI"], 0, $index));
        } else{
            $this->path($_SERVER["REQUEST_URI"]);
        }
        $this->ip( filter_var( $this->getIp() ));
        $this->sessionId( $_SESSION["sessionId"] ?? "" );
        $this->token = $_POST["token"] ?? "";
        $this->passport = $_POST["passport"] ?? "";
        $this->data = $_POST + $_GET;
        return $this;
    }

    public function check(){
        //Ha nincs sessionId, akkor login
        if ( $this->sessionId == "" ){
            redirect( "loginPage" );
        }

        //Ha a kérés login, akkor get estén új session, majd mehet tovább.
        if( request()->path() == route( "loginPage" )){
            if( request()->method() == "get" ){
                $this->newSession();
            }else{
                $this->session = $this->loadSession();        
            }
            return;
        }

        //Ha a kérés nem login, akkor töltsük be a session-t.
        $this->session = $this->loadSession();

        //Ha nincs user, vagy a session érvényessége lejárt, akkor login.
        if( $this->session->userId == 0 || $this->session->expirationDate < (new \DateTime())->format("Y-m-d H:i:s") ){
            redirect( "loginPage");
        }
    }

    public function method( string $method = ""){
        if( $method == ""){
            return $this->method;
        }
        $this->method = $method;
        return $this;
    }

    public function path( string $path = ""){
        if( $path == ""){
            return $this->path;
        }
        $this->path = $path;
        return $this;
    }

    public function ip( string $ip = ""){
        if( $ip == ""){
            return $this->ip;
        }
        $this->ip = $ip;
        return $this;
    }

    public function session( Session $session = null){
        if( !$session ){
            return $this->session;
        }
        $this->session = $session;
        return $this;
    }

    public function sessionId( string $sessionId = ""){
        if( $sessionId == "" ){
            return $this->sessionId;
        }
        $this->sessionId = $sessionId;
        return $this;
    }

    public function token(){
        return $this->token;
    }

    public function more( $findKey ){
        $keys = is_array( $findKey ) ? $findKey : [$findKey];
        $result = [];
        foreach( $keys as $key){
            if(isset($this->data[$key])){
                $result += [$key => $this->data[$key]];
            }
        }
        return $result;
    }

    public function only( $findKey ){
        return $this->data[$findKey] ?? null;
    }

    public function all(){
        return $this->data;
    }

    private function loadSession(){
        // A session betöltése
        $session = Session::selectByWhere( "sessionId", $this->sessionId());

        //Ellenőrizni a session-t. Ha van sessionId, akkor csak 1 db session lehet. Minden egyéb megoldás hibás.
        if( count( $session) == 0 ){
            session_destroy();
            redirect( "loginPage" );
        }
        if( count( $session ) > 1){
            db()->query( "delete from session where sessionId=:sessionId")
                ->params( ["sessionId" => $this->sessionId()])
                ->run();
            session_destroy();
            redirect( "loginPage" );
        }
        return $session[0];
    }

    private function newSession(){
        $this->session( Session::new() );
        $this->session->insert();
        $this->sessionId( $this->session->sessionId );
        $_SESSION["sessionId"] = $this->sessionId();
    }

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