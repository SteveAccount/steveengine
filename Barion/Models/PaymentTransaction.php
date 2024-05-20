<?php

namespace SteveEngine\Barion\Models;

class PaymentTransaction extends BarionBaseModel {
    public string   $postTransactionId;
    public string   $payee;
    public float    $total;
    public string   $comment;
    public array    $payeeTransactions;
    public array    $items;
}