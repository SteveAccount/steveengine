<?php

namespace SteveEngine\Menu;

use SteveEngine\TreeNode;

class Menu {
    public function getMenuFromModuls() : string {
        $menuItems  = $this->getMenuItemsFromModuls();
        $menuTree   = new TreeNode();
        $menuTree->fillTree($menuItems, "name", "parentName");
        $menu = $this->renderMenu($menuTree);

        return $menu;
    }

    private function getMenuItemsFromModuls() : array {
        $userPermissions    = request()->user ? request()->user->getPermissions() : [];
        $menu               = [];
        $path               = config()->get("appPath") . DIRECTORY_SEPARATOR . "Moduls" . DIRECTORY_SEPARATOR . "*";
        $moduls             = glob($path, GLOB_ONLYDIR);

        foreach ($moduls as $modulPath) {
            $className  = basename($modulPath);
            $class      = implode("\\", [$className, "Helpers", $className]);

            if (class_exists($class)) {
                $modul      = new $class ();

                if (method_exists($modul, "getMenu")) {
                    $submenus   = $modul->getMenu();

                    foreach ($submenus as $submenu) {
                        if ($submenu["permissions"] === [] || !empty(array_intersect($submenu["permissions"], $userPermissions))) {
                            $menu[] = new MenuItem($submenu["name"], $submenu["parentName"], $submenu["orderIndex"], $submenu["label"], $submenu["action"], $submenu["permissions"], $submenu["hasWindow"] ?? true);
                        }
                    }
                }
            }
        }

        usort($menu, function ($a, $b) {
            $result = 0;
            if ($a->parentName === $b->parentName) {
                $result = $a->orderIndex <=> $b->orderIndex;
            } else {
                if ($a->parentName === "0") {
                    $result = 1;
                } elseif ($b->parentName === "0") {
                    $result = -1;
                }
            }

            return $result;
        });

        return $menu;
    }

    public function renderMenu(TreeNode $node, bool $isSubmenu = false) {
        $menu = $isSubmenu ? "<ul class='submenu'>" : "<ul>";

        foreach ($node->nodes as $subnode) {
            $menuItemArrow = $subnode->nodes ? "&#x25BC;" : "";
            $menu .= "<li>";
            $url = $subnode->content->action === "" ? "" : " data-url='" . $subnode->content->action . "'";
            $menu .= "<a class='menuItem' $url " .
                "data-id='" . $subnode->content->name . "' data-window='" . $subnode->content->hasWindow . "'>" .
                "<span class='parentMenuItem'>" . $menuItemArrow . "</span>" .
                "<span>" . $subnode->content->label . "</span>" .
                "</a>";
            if ($subnode->nodes) {
                $menu .= $this->renderMenu($subnode, true);
            } else {
                $menu .= "</li>";
            }
        }
        $menu .= "</ul>";

        return $menu;
    }
}