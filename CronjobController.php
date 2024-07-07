<?php

namespace SteveEngine;

class CronjobController {
    public function run() {
        $tasks  = get_class_methods(static::class);
        $now    = new \DateTime();

        foreach ($tasks as $task) {
            if ($task !== "run") {
                $this->$task($now);
            }
        }
    }
}