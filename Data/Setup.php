<?php

namespace SteveEngine\Data;

class Setup{
    public function createDatabase(string $databaseName) : bool{
        $connection             = config()->get("databaseInfo")["main"];
        $connection["database"] = "";
        Database::new()->prepare($connection);
        $query = "create schema $databaseName";
        if (db()->query($query)->run()){
            db()->switchDb($databaseName);
            return $this->createTables();
        }
        return false;
    }

    private function createTables() : bool{
        $filenames = glob(__DIR__ . DIRECTORY_SEPARATOR . "Datatables" . DIRECTORY_SEPARATOR . "*");
        foreach($filenames as $filename){
            if(is_file($filename)){
                $query = file_get_contents($filename);
                if (!db()->query($query)->run()){
                    return false;
                }
            }
        }
        return true;
    }
}