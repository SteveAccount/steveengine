<?php
namespace SteveEngine\Install;

class ComposerInstall {
    public array $folders = [
        "Modules", "Main", "System"
    ];

    public static function postInstall(): void {
        $composerInstall = new self;
        $composerInstall->setFolders();
        $composerInstall->setTables();
    }

    public static function postUpdate(): void {
        $composerInstall = new self;
        $composerInstall->setFolders();
        $composerInstall->setTables();
    }

    private function setFolders(): void {
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



