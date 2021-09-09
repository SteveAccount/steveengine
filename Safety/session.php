<?php

namespace SteveEngine\Safety;

use DateTime;
use SteveEngine\Config;
use SteveEngine\Convert\Sha3;
use SteveEngine\Data\Model;

class Session extends Model{
    public $id;
    public $sessionId;
    public $ip;
    public $userId;
    public $expirationDate;
    public $token;

    public static function new() : Session{
        $newSession = new self();
        $newSession->sessionId = $newSession->getSessionId();
        $newSession->ip = Request::new()->ip();
        $newSession->userId = 0;
        $newSession->expirationDate = (new \DateTime())->add(new \DateInterval("PT" . Config::get("sessionExpiratingInterval") . "H"))->format("Y-m-d H:i:s");
        return $newSession;
    }

    public function newToken() : string{
        $base = ( new \DateTime )->format( "Y-m-d H:i:s.v" );
        $hash = Sha3::hash(strtoupper(Sha3::hash( $base, 512)), 512);
        $this->token = $hash;
        db()
            ->query( "update session set token=:token where sessionId=:sessionId")
            ->params( ["token" => $hash, "sessionId" => $this->sessionId] )
            ->run();
        return $hash;
    }
    private function getSessionId() : string{
        $base = ( new DateTime )->format( "Y-m-d H:i:s.v" );
        $hash = Sha3::hash( $base, 512);
        return $hash;
    }
}