<?php

namespace SteveEngine\Setup;

use SteveEngine\Config;
use SteveEngine\File;

class Databases{
    private $mainInfo;
    private $programInfo;

    public function __construct(){
        //Az általános működéshez szükséges adatbázisok, adattáblák adatainak betöltése
        $this->mainInfo = $this->getMainDatabaseInfo();
        $this->programInfo = $this->getProgramDatabaseInfo();

        //Adatbázisok, adattáblák létrehozása módosítása
        $this->setupDatabase( $this->mainInfo );
        foreach( $this->programInfo as $databaseInfo ){
            $this->setupDatabase( $databaseInfo );
        }

        //Adattáblák feltöltése alapértékekkel
        $this->prefillTable( $this->mainInfo );
        foreach( $this->programInfo as $databaseInfo ){
            $this->prefillTable( $databaseInfo );
        }
    }

    private function getMainDatabaseInfo() : array{
        $path = Config::get("appPath") . "/Config/databaseinfo.json";
        return json_decode( File::loadJson( $path ), true);
    }
    
    private function getProgramDatabaseInfo() : array{
        $files = [];
        $path = Config::get("appPath") . "/Moduls";
        $folders = File::getFolders( $path );
        foreach( $folders as $folder ){
            $path = "$folder/databaseinfo.json";
            $fileInfo = File::loadJson( $path );
            if( $fileInfo != ""){
                $files[] = json_decode( $fileInfo, true );
            }
        }
        return $files;
    }

    private function setupDatabase( array $info ) : void{
        //Adatbázis létrehozása
        $this->databaseHandler( $info["name"]);

        //Táblák létrehozása, módosítása
        foreach( $info["tables"] as $tableName => $tableInfo){
            $this->datatableHandler( $info["name"], $tableName, $tableInfo);
        }
    }

    private function databaseHandler( string $databaseName ) : void{
        $query = "create schema if not exists $databaseName";
        db()->query( $query )->run();
    }

    private function datatableHandler( string $database, string $tableName, array $tableInfo ): void{
        $query = "SELECT table_name FROM information_schema.tables
        WHERE table_schema = '$database' and table_name='$tableName'";
        if ( !db()->query( $query )->select() ){
            $this->createTable( $database, $tableName, $tableInfo );
        }
    }

    private function createTable( string $database, string $tableName, array $tableInfo ){
        $fields = "";
        $indexes = "";
        foreach( $tableInfo["fields"] as $fieldInfo ){
            $fields .= $fields == "" ? "" : ", ";
            $fields .= $fieldInfo["name"] . " " . $fieldInfo["type"];
            $fields .= isset( $fieldInfo["notNull"] ) ? " not null" : "";
            $fields .= isset( $fieldInfo["autoIncrement"] ) ? " auto_increment" : "";
            $fields .= isset( $fieldInfo["default"] ) ? " default '" . $fieldInfo["default"] . "'" : "";
            $indexes .= isset( $fieldInfo["primaryKey"] ) ? ($indexes == "" ? "primary key(" . $fieldInfo["name"] . ")" : ", primary key(" . $fieldInfo["name"] . ")") : "";
            $indexes .= isset( $fieldInfo["unique"] ) ? ($indexes == "" ? "unique key " . $fieldInfo["name"] . "_unique (" . $fieldInfo["name"] . ")" : ", unique key " . $fieldInfo["name"] . "_unique (" . $fieldInfo["name"] . ")") : "";
        }
        
        $query = "create table $database.$tableName ($fields, $indexes) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
        db()->query( $query )->run();
    }

    private function prefillTable( array $info ) : void{
        $database = $info["name"];
        foreach( $info["tables"] as $tablename => $tableInfo ){
            if( $tableInfo["prefill"] ){
                foreach( $tableInfo["prefill"] as $datarow ){
                    $fields = "";
                    foreach( $datarow as $field => $value ){
                        if( is_string( $value )){
                            $fields .= $fields == "" ? "'$value'" : ", '$value'";    
                        }else{
                            $fields .= $fields == "" ? $value : ", $value";
                        }
                    }
                    $query = "insert ignore into $database.$tablename values ($fields)";
                    db()->query( $query )->run();
                }
            }
        }
    }
}