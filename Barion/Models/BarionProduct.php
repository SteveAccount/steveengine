<?php

namespace SteveEngine\Barion\Models;

class BarionProduct extends BarionBaseModel {
    public string   $name;
    public string   $description;
    public string   $imageUrl;
    public float    $quantity;
    public string   $unit;
    public float    $unitPrice;
    public float    $itemTotal;
    public string   $sku;
}