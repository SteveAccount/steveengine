<?php
namespace SteveEngine;

class ComposerInstall {
    public static function postInstall(\Composer\Script\Event $event) {
        mkdir("Kukucs");
    }

    public static function postUpdate(\Composer\Script\Event $event) {
        // logika: mappák, DB migrációk
    }
}



