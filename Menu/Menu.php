<?php

namespace SteveEngine\Menu;

use SteveEngine\TreeNode;

class Menu{
    public function getMenuFromModuls() : TreeNode{

    }

    private function getMenuItemsFromModuls() : array{
        $menu   = [];
        $path   = config()->get("appPath") . DIRECTORY_SEPARATOR . "Moduls" . DIRECTORY_SEPARATOR . "*";
        $moduls = glob($path, GLOB_ONLYDIR);
        foreach ($moduls as $modulPath){
            $menuFile = $modulPath . DIRECTORY_SEPARATOR . "menu.php";
            if (file_exists($menuFile)){
                $menu = array_merge($menu, include $menuFile);
            }
        }

//        usort($menu, ($a, $b) => {
//
//        })
        toLog($menu);
    }
}