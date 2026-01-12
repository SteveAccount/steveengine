<?php

namespace SteveEngine\Safety;

use DateTime;
use SteveEngine\Convert\Sha3;
use SteveEngine\Data\Model;

class Session extends Model {
    public static $tableName = "sessions";

    public int      $id;
    public string   $sessionId;
    public string   $ip;
    public int      $userId;
    public ?int     $pseudoUserId;
    public ?string  $permission;
    public string   $expirationDate;
    public ?string  $token;
    public ?string  $checkCode1;
    public ?string  $checkCode2;
    public ?int     $checkId;
    public ?string  $checkRoute;
    public ?string  $params;

    public static function new(int $userId = 0) : Session {
        if ($userId !== 0) {
            $query = "delete from sessions where userId = $userId";
            db()->query($query)->run();
        }

        $newSession                 = new self();
        $newSession->sessionId      = $newSession->getSessionId();
        $newSession->ip             = Request::new()->ip();
        $newSession->userId         = $userId;
        $newSession->expirationDate = (new \DateTime())->add(new \DateInterval("PT" . config()->get("sessionExpirationInterval") . "H"))->format("Y-m-d H:i:s");
        $newSession->id             = $newSession->insert();
        $_SESSION["sessionId"]      = $newSession->sessionId;

        return $newSession;
    }

    public static function getBySessionId(string $sessionId) : ?Session {
        $session = Session::selectByWhere(["sessionId" => $sessionId]);

        return $session && count($session) === 1 ? $session[0] : null;
    }

    public function newToken() : string {
        $base = (new \DateTime)->format("Y-m-d H:i:s.v");
        $hash = Sha3::hash(strtoupper(Sha3::hash($base, 512)), 512);
        $this->token = $hash;
        db()
            ->query( "update sessions set token=:token where sessionId=:sessionId")
            ->params(["token" => $hash, "sessionId" => $this->sessionId])
            ->run();

        return $hash;
    }

    private function getSessionId() : string {
        $base = (new DateTime)->format("Y-m-d H:i:s.v");

        return Sha3::hash($base, 512);
    }

    public function setCheckCodes(string $route, int $id = null) :array {
        $isFirst = rand(0, 1);
        $checkCode = substr(Sha3::hash($route . (new DateTime())->format("YmdHisu"), 512), 0, 20);

        $this->checkCode1   = $isFirst ? $checkCode : "";
        $this->checkCode2   = $isFirst ? "" : $checkCode;
        $this->checkId      = $id;
        $this->checkRoute   = $route;
        $this->update();

        return [
            "checkCode1" => $this->checkCode1,
            "checkCode2" => $this->checkCode2,
        ];
    }
}