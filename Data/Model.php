<?php

namespace SteveEngine\Data;

abstract class Model{
    public static $tableName;

    public static function selectAll($orderBy = ""){
        $table      = static::$tableName ?? self::getTablename();

        if ($orderBy !== "") {
            if (!is_array($orderBy)) {
                $orderBy = [$orderBy];
            }
        } else {
            $orderBy = [];
        }

        $order_By = "";
        foreach ($orderBy as $field) {
            $order_By .= $order_By === "" ? $field : ", $field";
        }

        $orderBy    = $order_By === "" ? "" : " order by $order_By";
        $query      = "select * from $table $orderBy";
        toLog($query);
        $result     = db()->query( $query )->answer( static::class )->select();

        return $result ?? null;
    }

    public static function selectById( int $id ){
        $table = static::$tableName ?? self::getTablename();
        $query = "select * from $table where id=:id";
        $result = db()->query( $query )->answer( static::class )->params( ["id" => $id] )->select();
        return $result ? $result[0] : null;
    }

    public static function selectByWhere(array $conditions, bool $isOnlyOne = false, array $fields = null){
        $table = static::$tableName ?? self::getTablename();
        $where = "";
        foreach ($conditions as $field => $value){
            $where .= $where === "" ? "$field=:$field" : " and $field=:$field";
        }
        if ($fields) {
            $fieldList = "";

            foreach ($fields as $field) {
                $fieldList .= $fieldList === "" ? $field : ", $field";
            }
            $query  = "select $fieldList from $table where $where";
        } else {
            $query  = "select * from $table where $where";
        }

        $result = db()->query($query)->answer(static::class)->params($conditions)->select();
        return count($result) > 0 ? ($isOnlyOne ? $result[0] : $result) : null;
    }

    public static function selectByWhereIn(string $field, array $values){
        $table = static::$tableName ?? self::getTablename();
        $whereIn = "";
        foreach ($values as $value){
            $where .= $where === "" ? $value : ", $value";
        }
        $query  = "select * from $table where $field in ($whereIn)";
        $result = db()->query($query)->answer(static::class)->params($conditions)->select();
        return count($result) > 0 ? $result : null;
    }

    public static function selectByQuery(string $query, bool $isOnlyOne = false){
        $table  = static::$tableName ?? self::getTablename();
        $result = db()->query($query)->answer(static::class)->select();

        return count($result) > 0 ? ($isOnlyOne ? $result[0] : $result) : null;
    }
    
    public static function getNext(string $field, array $conditions = []) : int{
        $table = static::$tableName ?? self::getTablename();
        $where = "";
        foreach ($conditions as $fieldName => $value){
            $where .= $where === "" ? "$fieldName=$value" : " and $fieldname=$value";
        }
        $where = $where === "" ? "" : "where $where";
        $query = "select max($field) as maximum from $table $where";
        $result = db()->query($query)->answer("StdClass")->scalar("maximum");
        return $result ? ++$result : 1;
    }

    public function insert(){
        $table = static::$tableName ?? self::getTablename();
        return db()->tableName($table)->data($this)->insert();
    }

    public function insertWithId(){
        $table = static::$tableName ?? self::getTablename();
        return db()->tableName($table)->data($this)->insertWithId();
    }

    public function update(){
        $table = static::$tableName ?? self::getTablename();
        db()->tableName($table)->data($this)->update();
    }

    public static function getListForSelect(string $idField = "id", string $nameField = "name"){
        $table = static::$tableName ?? self::getTablename();
        $query = "select $idField, $nameField as text from $table where isActive = 1 order by text";
        return db()->query($query)->select();
    }

    public function fullDelete(){
        $table  = static::$tableName ?? self::getTablename();
        $id     = $this->id;
        $query  = "delete from $table where id = $id";

        return db()->query($query)->run();
    }
    
    private static function getTablename() : string{
        $parts = explode( "\\", static::class );
        return strtolower( $parts[count( $parts ) -1] );
    }

    protected function setFields(array $data){
        foreach ($data as $key => $value){
            $this->$key = $value;
        }
    }
}