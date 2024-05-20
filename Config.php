<?php

namespace SteveEngine;

/**
 * Class Config
 * @package SteveEngine
 */
class Config extends Singleton {
    /**
     * @var array
     */
    public array $settings = [];

    /**
     * @return Config
     */
    public function prepare() {
        $this->settings = include ("./config.php");

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return Config
     */
    public function set(string $name, string $value) {
        $this->settings[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name) {
        return $this->settings[$name] ?? null;
    }
}