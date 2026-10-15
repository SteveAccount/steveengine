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
    public function prepare(string $path) {
        $this->settings["appPath"] = $path;
        $envPath = $this->settings["appPath"] . '/Moduls/System/.env';
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (preg_match("/^\s*#/", $line)) continue;
            if (!strpos($line,"=")) continue;
            list($key, $value) = explode("=", $line, 2);
            $value = trim($value);

            if (str_contains($value, "[")) {
                $value = substr($value, 1, -1);
                $items = explode(",", $value);
                foreach ($items as $item) {
                    $this->settings[trim($key)][] = $item;
                }
            } else {
                $this->settings[trim($key)] = $value;
            }
        }
toLog($this->settings);
        return $this;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name) {
        return $this->settings[$name] ?? null;
    }

    public function getPasswordRules() {
        return [
            "minLength" => $this->settings["passMinLength"],
            "lowers"    => $this->settings["passLowers"],
            "uppers"    => $this->settings["passUppers"],
            "numbers"   => $this->settings["passNumbers"],
            "specials"  => $this->settings["passSpecials"],
            "exp"       => $this->settings["passExpirationMonths"],
        ];
    }
}