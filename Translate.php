<?php

namespace SteveEngine;

use SteveEngine\Singleton;

class Translate extends Singleton {
    public array $languages     = ["en"];
    public ?array $dictionary   = null;

    public function trans(string $huString) {
        if (!$this->dictionary) {
            $this->dictionary = $this->loadDictionary();
        }

        if (isset($this->dictionary[$huString][request()->lang])) {
            if ($this->dictionary[$huString][request()->lang] !== "") {
                return $this->dictionary[$huString][request()->lang];
            }
        } else {
            foreach ($this->languages as $language) {
                $this->dictionary[$huString][$language] = "";
            }

            file_put_contents("dictionary.json", json_encode($this->dictionary, JSON_UNESCAPED_UNICODE));
        }

        return $huString;
    }

    public function loadDictionary() : array {
        return file_exists("dictionary.json") ? json_decode(file_get_contents("dictionary.json"), 1) : [];
    }
}