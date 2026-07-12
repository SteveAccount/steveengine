<?php
namespace SteveEngine;

class ComposerInstall {
    public static function postInstall(\Composer\Script\Event $event) {
        file_put_contents("hapci", "Hapcica");
        mkdir("Kukucs");
    }

    public static function postUpdate(\Composer\Script\Event $event) {
        file_put_contents("hapci", "Hapcica");
        mkdir("Kukucs");
    }
}



