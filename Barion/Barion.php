<?php

namespace SteveEngine\Barion;

use SteveEngine\Barion\Models\BarionConfig;
use SteveEngine\Barion\Models\BarionProduct;
use SteveEngine\Barion\Models\PaymentTransaction;
use SteveEngine\Barion\Models\ShippingAddress;
use SteveEngine\Curl;

class Barion {
    private BarionConfig $barionConfig;
    private array $products = [];
    private ShippingAddress $shippingAddress;

    public function __construct(BarionConfig $barionConfig) {
        $this->barionConfig = $barionConfig;
    }

    public function addProduct(BarionProduct $product) {
        $this->products[] = $product;

        return $this;
    }

    public function addShippingAddress(ShippingAddress $shippingAddress) {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function paymentStart(string $transactionId, $cart) {
        // A küldendő adatok összeállítása
        $messageArray = [
            "POSKey"            => $this->barionConfig->posKey,
            "PaymentType"       => "Immediate",
            "GuestCheckOut"     => true,
            "FundingSources"    => ["All"],
            "PaymentRequestId"  => (new \DateTime())->format("YmdHis"),
            "RedirectUrl"       => $this->barionConfig->redirectUrl,
            "CallbackUrl"       => $this->barionConfig->callbackUrl,
            "Transactions"      => $this->makeTransaction($transactionId),
            "ShippingAddress"   => $this->shippingAddress->getArray(),
            "Locale"            => "hu-HU",
            "Currency"          => "HUF",
        ];

        $url        = implode("/", [$this->barionConfig->url, $this->barionConfig->version, "Payment", "Start"]);
        $message    = json_encode($messageArray, JSON_UNESCAPED_UNICODE);

        $curl       = new Curl();
        $response   = json_decode($curl->send($url, "json", $message), 1);
        $paymentId  = $response["PaymentId"];
        $gatewayUrl = $response["GatewayUrl"];

        $cart->paymentId = $paymentId;
        $cart->update();

        return $gatewayUrl;
    }

    public function makeTransaction(string $transactionId) {
        $items = [];
        $total = 0;
        foreach ($this->products as $product) {
            $total += $product->itemTotal;
            $items[] = $product->getArray();
        }

        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction->postTransactionId  = $transactionId;
        $paymentTransaction->payee              = $this->barionConfig->email;
        $paymentTransaction->items              = $items;
        $paymentTransaction->total              = $total;



        return [$paymentTransaction->getArray()];
    }

    public function getPaymentState(string $paymentId) {
        $message = json_encode([
            "POSKey"    => $this->barionConfig->posKey,
            "PaymentId" => $paymentId,
        ]);

        $url    = implode("/", [$this->barionConfig->url, $this->barionConfig->version, "Payment", "GetPaymentState"]);
        $url   .= "?POSKey=" . $this->barionConfig->posKey;
        $url   .= "&PaymentId=" . $paymentId;
        $curl       = new Curl();

        return json_decode($curl->send($url, "json", "", "GET"), 1);
    }
}