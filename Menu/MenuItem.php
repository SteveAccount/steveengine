<?php

namespace SteveEngine\Menu;

class MenuItem{
    public string $name;
    public string $parentName;
    public int $orderIndex;
    public string $label;

    /**
     * MenuItem constructor.
     * @param string $name
     * @param string $parentName
     * @param int $orderIndex
     * @param string $label
     */
    public function __construct(string $name, string $parentName, int $orderIndex, string $label){
        $this->name = $name;
        $this->parentName = $parentName;
        $this->orderIndex = $orderIndex;
        $this->label = $label;
    }
}