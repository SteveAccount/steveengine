<?php

namespace SteveEngine\Barion\Models;

use SteveEngine\Barion\Models\BarionBaseModel;

class ShippingAddress extends BarionBaseModel {
    public string $country;
    public string $city;
    public string $region;
    public string $zip;
    public string $street;
    public string $street2;
    public string $street3;
    public string $fullName;
}