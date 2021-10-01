<?php

namespace SteveEngine\Menu;

use SteveEngine\TreeNode;

class Menu{
    public function getMenuFromModuls() : string{
        $menuItems  = $this->getMenuItemsFromModuls();
        $menuTree   = new TreeNode();
        $menuTree->fillTree($menuItems, "name", "parentName");
        $menu = $this->renderMenu($menuTree);
        return $menu;
    }

    private function getMenuItemsFromModuls() : array{
        $menu   = [];
        $path   = config()->get("appPath") . DIRECTORY_SEPARATOR . "Moduls" . DIRECTORY_SEPARATOR . "*";
        $moduls = glob($path, GLOB_ONLYDIR);
        foreach ($moduls as $modulPath){
            $menuFile = $modulPath . DIRECTORY_SEPARATOR . "menu.php";
            if (file_exists($menuFile)){
                $submenus = include $menuFile;
                foreach ($submenus as $submenu){
                    $menu[] = new MenuItem($submenu["name"], $submenu["parentName"], $submenu["orderIndex"], $submenu["label"],);
                }
            }
        }

        usort($menu, function ($a, $b)  {
            $result = -1;
            if ($a->parentName > $b->parentName ||
                ($a->parentName === $b->parentName && $a->orderIndex > $b->orderIndex)){
                $result = 1;
                return $result;
            }
        });
        return $menu;
    }

    public function renderMenu(TreeNode $node, bool $isSubmenu = false){
        $menu = $isSubmenu ? "<ul class='submenu'>" : "<ul>";
        foreach ($node->nodes as $subnode){
            $menu .= "<li>";
            $menu .= "<a class='menuItem'>" . $subnode->content->label . "</a>";
            if ($subnode->nodes){
                $menu .= $this->renderMenu($subnode, true);
            } else{
                $menu .= "</li>";
            }
        }
        $menu .= "</ul>";
        return $menu;
    }
}