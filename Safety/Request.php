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
    private string $token;
    /**
     * @var
     */
    private $data;
    /**
     * @var User
     */
    private User $user;
    /**
     * @var Session
     */
    private Session $session;

    /**
     * A Request osztály feltöltése.
     * @return $this
     */
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

    /**
     *
     */
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

    /**
     * @param string $method
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function session(Session $session = null){
        if( !$session ){
            return $this->session;
        }
        $this->session = $session;
        return $this;
    }

    /**
     * @param string $sessionId
     * @return $this
     */
    public function sessionId(string $sessionId = ""){
        if( $sessionId == "" ){
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
    public function more($findKey ){
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
     * @return mixed
     */
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

    /**
     *
     */
    private function newSession(){
        $this->session( Session::new() );
        $this->session->insert();
        $this->sessionId( $this->session->sessionId );
        $_SESSION["sessionId"] = $this->sessionId();
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