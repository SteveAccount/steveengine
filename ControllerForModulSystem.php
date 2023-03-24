<?php

namespace SteveEngine;

use SteveEngine\Safety\Request;
use SteveEngine\Translate;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

abstract class ControllerForModulSystem{
    public string           $path;
    protected Environment   $twig;

    public function __construct(string $folderInModul = "Templates"){
        $parts      = explode("\\", get_class($this));
        $path       = implode(DIRECTORY_SEPARATOR, [config()->get("appPath"), "Moduls", $parts[0], $folderInModul]);
        $loader     = new FilesystemLoader($path);
        $this->twig = new Environment($loader);
        $this->twig->addFunction(new \Twig_SimpleFunction("trans", function($huString) {
            $translate = Translate::new();
            return $translate->trans($huString);
        }));
    }

    public function getData(string $functionName, string $where = ""){
        //TableInfo betöltése
        $tableInfo  = include("$this->path/Tables/$functionName.php");
        $tableName  = $tableInfo["tableName"];

        $query      = $tableInfo["query"];

        //where
        if (request()->only("isActive") !== null){
            $where = $where === "" ? "" : "$where and ";
            $where .= "$tableName.isActive = " . (int)request()->only("isActive");
        }

        //where for search
        $search = request()->only("search");
        if (isset($search["field"]) && isset($search["operation"]) && isset($search["searchText"]) && $search["searchText"] !== ""){
            switch ($search["operation"]){
                case "equal":
                    $field = array_keys($tableInfo["fields"])[(int)$search["field"]];
                    $value = $search["searchText"];
                    $where = $where === "" ? "" : "$where and ";
                    if (is_numeric($value)){
                        $where .= "$tableName.$field = $value";
                    } else{
                        $where .= "$tableName.$field = '$value'";
                    }
                    break;
                case "content":
                    $field = array_keys($tableInfo["fields"])[(int)$search["field"]];
                    $value = $search["searchText"];
                    $where = $where === "" ? "" : "$where and ";
                    if (is_numeric($value)){
                        $where .= "$tableName.$field = $value";
                    } else{
                        $where .= "$tableName.$field like '%$value%'";
                    }
            }
        }

        if ($where !== ""){
            $where = "where $where";
        }

        //order by
        $orderBy = "";
        if (request()->only("fieldIndex") !== null){
            $index      = (int)request()->only("fieldIndex");
            $field      = array_keys($tableInfo["fields"])[$index];
            if (isset($tableInfo["fields"][$field]["ordering"])){
                $field  = $tableInfo["fields"][$field]["ordering"];
            }
            $direction  = request()->only("direction") === "Desc" ? "desc" : "asc";
            $orderBy    = "order by $field $direction";
        }

        //limit
        $limitStart = (int)request()->only("limitStart");
        $limit = "limit $limitStart, " . $tableInfo["limit"];

        $query = "$query $where $orderBy $limit";
toLog($query);
        return db()->query($query)->answer("StdClass")->select();;
    }
}