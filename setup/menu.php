<?php

namespace SteveEngine\Setup;

use SteveEngine\Config;
use SteveEngine\File;
use SteveEngine\Menu\Menuitem;

class Menu{
    public function __construct(){
        $storedMenu = Menuitem::selectAll();
        $newMenu = $this->getMenuOfModuls();
        $menu = $this->compareMenuitems( $storedMenu, $newMenu );
        $this->saveMenuitems( $menu );
    }

    private function saveMenuitems( array $menuitems ){
        foreach( $menuitems as $menuitem ){
            if( $menuitem->id === 0 ){
                $menuitem->insert();
            }else{
                $menuitem->update();
            }
        }
    }

    private function compareMenuitems( array $storedMenu, array $newMenu ) : array{
        $menu = [];
        foreach( $newMenu as $index => $newMenuitem ){
            $storedIndex = $this->getMenuitemByName( $storedMenu, $newMenuitem->name );
            if( $storedIndex !== null ){
                $newMenuitem->id = $storedMenu[$storedIndex]->id;
                $newMenuitem->orderIndex = $storedMenu[$storedIndex]->orderIndex;
            }
            $menu[] = $newMenuitem;
        }
        foreach( $storedMenu as $index => $storedMenuitem ){
            $newIndex = $this->getMenuitemByName( $newMenu, $storedMenuitem->name);
            if( $newIndex === null ){
                $storedMenu[$index]->isActive = false;
                $menu[] = $storedMenu[$index];
            }
        }
        return $menu;
    }

    private function getMenuitemByName( array $menu, string $name ){
        foreach( $menu as $index => $menuitem ){
            if( $menuitem->name === $name ){
                return $index;
            }
        }
        return null;
    }

    private function getMenuOfModuls() : array{
        $menu = [];
        $path = Config::get("appPath") . "/Moduls/";
        $folders = File::getFolders( $path );
        foreach( $folders as $folder ){
            $file = "$folder/Config/menu.json";
            $content = json_decode( file_get_contents( $file), true );
            foreach( $content as $name => $item ){
                $menuitem = Menuitem::new();
                $menuitem->name = $name;
                $menuitem->parentName = $item["parentName"] == "main" ? 0 : $item["parentName"];
                $menuitem->menuText = $item["menuText"];
                $menuitem->routeName = $item["routeName"];
                $menu[] = $menuitem;
            }
        }
        return $menu;
    }
}