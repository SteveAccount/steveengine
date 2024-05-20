<?php

namespace SteveEngine\Barion\Models;

abstract class BarionBaseModel {
    public function getArray() {
        $result = [];
        $params = get_class_vars(static::class);

        foreach ($params as $key => $value) {
            if (isset($this->$key)) {
                $result[ucfirst($key)] = $this->$key;
            }

        }

        return $result;
    }
}