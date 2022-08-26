<?php

namespace SteveEngine\Safety;

use DateTime;
use SteveEngine\Convert\Sha3;
use SteveEngine\Data\Model;

/**
 * Class Session
 * @package SteveEngine\Safety
 */
class Session extends Model{
    public static $tableName = "sessions";
    /**
     * @var int
     */
    public int $id;
    /**
     * @var string
     */
    public string $sessionId;
    /**
     * @var string
     */
    public string $ip;
    /**
     * @var int
     */
    public int $userId;
    /**
     * @var string
     */
    public string $expirationDate;
    /**
     * @var string|null
     */
    public ?string $token;

    /**
     * @param int $userId
     * @return Session
     * @throws \Exception
     */
    public static function new(int $userId = 0) : Session{
        if ($userId !== 0){
            $query = "delete from session where userId=$userId";
            db()->query($query)->run();
        }
        $newSession                 = new self();
        $newSession->sessionId      = $newSession->getSessionId();
        $newSession->ip             = Request::new()->ip();
        $newSession->userId         = $userId;
        $newSession->expirationDate = (new \DateTime())->add(new \DateInterval("PT" . config()->get("sessionExpirationInterval") . "H"))->format("Y-m-d H:i:s");
        $newSession->insert();
        $_SESSION["sessionId"]      = $newSession->sessionId;
        return $newSession;
    }

    /**
     * @param string $sessionId
     * @return Session|null
     */
    public static function getBySessionId(string $sessionId) : ?Session{
        $session = Session::selectByWhere(["sessionId" => $sessionId]);
        return isset($session) && count($session) === 1 ? $session[0] : null;
    }

    /**
     * @return string
     * @throws \Exception
     */
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

    /**
     * @return string
     * @throws \Exception
     */
    private function getSessionId() : string{
        $base = ( new DateTime )->format( "Y-m-d H:i:s.v" );
        return Sha3::hash( $base, 512);
    }
}