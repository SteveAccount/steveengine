<?php

namespace SteveEngine\Menu;

class MenuItem{
    public string   $name;
    public string   $parentName;
    public int      $orderIndex;
    public string   $label;
    public string   $action;
    public array    $permissions;
    public bool     $hasWindow;

    public function __construct(string $name, string $parentName, int $orderIndex, string $label, string $action, array $permissions, bool $hasWindow = true){
        $this->name         = $name;
        $this->parentName   = $parentName;
        $this->orderIndex   = $orderIndex;
        $this->label        = $label;
        $this->action       = $action;
        $this->permissions  = $permissions;
        $this->hasWindow    = $hasWindow;
    }
}