<?php

namespace SteveEngine;

class Curl {
    public array $httpHeaders           = [
        "xml"   => ["Content-Type: application/xml", "Accept: application/xml"],
        "json"  => ["Content-Type: application/json"],
    ];
    public int $curlOptVerbose          = 1;
    public int $curlOptHeader           = 1;
    public int $curlOptSslVerifyPeer    = 1;
    public int $curlOptSslVerifyHost    = 2;
    public bool $curlOptPost            = true;
    public bool $curlOptReturnTransfer  = true;

    public function send(string $url, string $type, string $message, string $method = "POST") {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_VERBOSE, $this->curlOptVerbose);
        curl_setopt($ch, CURLOPT_HEADER, $this->curlOptHeader);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->curlOptSslVerifyHost);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->curlOptSslVerifyPeer);
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->httpHeaders[$type]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->curlOptReturnTransfer);

        if ($method === "POST") {
            curl_setopt($ch, CURLOPT_POST, $this->curlOptPost );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        }

        $result     = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $response   = substr( $result, curl_getinfo( $ch, CURLINFO_HEADER_SIZE ));
        
        $header     = substr($result, 0, $headerSize);
        $headers    = preg_split('/\n|\r\n?/', $header);
        $body       = substr($result, $headerSize);

//        $response = [
//            "headers" => $headers,
//            "body"    => $body,
//        ];

        switch ( $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE)){
            case 200:
                curl_close($ch);
                return $response;
            default:
                curl_close($ch);
                throw new \Exception($result, 422);
        }
    }
}
