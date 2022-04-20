<?php

namespace SteveEngine;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use SteveEngine\Validate\Field;

class Email{
    private $mail;
    private $address = [];

    public function __construct(){
        $this->mail = new PHPMailer(true);
        $this->emailConfig();
    }

    public function address(string $address) : Email{
        if (preg_match(Field::emailAddress()->pattern, $address)){
            $this->address[] = $address;
        }
        return $this;
    }

    public function subject(string $subject) : Email{
        $this->mail->Subject = $subject;
        return $this;
    }

    public function body(string $body) : Email{
        $this->mail->Body = $body;
        return $this;
    }

    public function send(){
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

    private function emailConfig(){
        if (config()->get("isSMTP") === true) {
            $this->mail->isSMTP();
            $this->mail->Mailer     = "smtp";
            $this->mail->SMTPDebug  = config()->get("SMTPDebug");
            $this->mail->Host       = config()->get("Host");
            $this->mail->SMTPAuth   = config()->get("SMTPAuth");
            $this->mail->Username   = config()->get("Username");
            $this->mail->Password   = config()->get("Password");
            $this->mail->SMTPSecure = config()->get("SMTPSecure");
            $this->mail->Port       = config()->get("Port");
        }

        $this->mail->isHTML(true);
        $this->mail->CharSet    = "UTF-8";
        $this->mail->setFrom(config()->get("FromAddress"), config()->get("FromName"));

        if (!empty(config()->get("ReplyTo"))) {
            $this->mail->AddReplyTo(config()->get("ReplyTo"));
        }
    }
}
