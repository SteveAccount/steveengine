<?php
namespace SteveEngine;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\ScriptEvents;
use Composer\Script\Event;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    public function activate(Composer $composer, IOInterface $io) {
        // opcionális: inicializálás, composer/context tárolása
    }

    public function deactivate(Composer $composer, IOInterface $io) {}
    public function uninstall(Composer $composer, IOInterface $io) {}

    // EventSubscriberInterface
    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'onPostInstall',
            ScriptEvents::POST_UPDATE_CMD  => 'onPostUpdate',
        ];
    }

    public function onPostInstall(Event $event)
    {
        // itt a telepítés/upgrade után lefutó kód
        // például: létrehozás, migráció meghívása stb.
        $cwd = getcwd();
        @file_put_contents($cwd . '/steveengine_postinstall.log', date('c')." postInstall\n", FILE_APPEND);
        // futtathatsz parancsokat, de légy óvatos: NE írj fájlokat root rendszerre, ne futtass interaktív parancsot
    }

    public function onPostUpdate(Event $event)
    {
        $cwd = getcwd();
        @file_put_contents($cwd . '/steveengine_postupdate.log', date('c')." postUpdate\n", FILE_APPEND);
    }
}
