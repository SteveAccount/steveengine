<?php
namespace SteveEngine;

class ComposerInstall {
    public array $folders = [
        "Modules", "Main", "System"
    ];

    public static function postInstall(\Composer\Script\Event $event) {
        $composerInstall = new self;
        $composerInstall->setFolders();
    }

    public static function postUpdate(\Composer\Script\Event $event) {
        $composerInstall = new self;
        $composerInstall->setFolders();
    }

    private function setFolders() {
        $rootFolder =  realpath($_SERVER['DOCUMENT_ROOT']);
        $appPath = dirname($rootFolder);

        foreach ($this->folders as $folder) {
            $modulesFolder = $appPath . DIRECTORY_SEPARATOR . $folder;
            if (!is_dir($modulesFolder)) {
                mkdir($modulesFolder);
            }
        }
    }

    private function setTables() {

    }
}



