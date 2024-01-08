<?php

namespace SteveEngine;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use SteveEngine\Validate\Field;

class Email {
    private $mail;
    private $address = [];

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->emailConfig(config()->get("email")[config()->get("mode")]);
    }

    public function address(string $address) : Email {
        if (preg_match(Field::emailAddress()->pattern, $address)){
            $this->address[] = $address;
        }

        return $this;
    }

    public function subject(string $subject) : Email {
        $this->mail->Subject = $subject;

        return $this;
    }

    public function body(string $body) : Email {
        $this->mail->Body = $body;

        return $this;
    }

    public function send() {
        $result = false;

        if (count($this->address) > 0){
            foreach ($this->address as $address){
                $this->mail->addAddress($address);
            }
            if (!$this->mail->Send()){
                toLog($this->mail->ErrorInfo);
            } else{
                $result =  true;
            }
        }

        return $result;
    }

    private function emailConfig(array $config) {
        if ($config["isSMTP"] === true) {
            $this->mail->isSMTP();
            $this->mail->Mailer     = "smtp";
            $this->mail->SMTPDebug  = $config["SMTPDebug"];
            $this->mail->Host       = $config["Host"];
            $this->mail->SMTPAuth   = $config["SMTPAuth"];
            $this->mail->Username   = $config["Username"];
            $this->mail->Password   = $config["Password"];
            $this->mail->SMTPSecure = $config["SMTPSecure"];
            $this->mail->Port       = $config["Port"];
        }

        $this->mail->isHTML(true);
        $this->mail->CharSet    = "UTF-8";
        $this->mail->setFrom($config["FromAddress"], $config["FromName"]);

        if (!empty($config["ReplyTo"])) {
            $this->mail->AddReplyTo($config["ReplyTo"]);
        }
    }
}
