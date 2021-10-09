<?php

namespace SteveEngine\Data;

abstract class Model{
    public static function selectAll(){
        $table = self::getTablename();
        $query = "select * from $table";
        $result = db()->query( $query )->answer( static::class )->select();
        return $result ?? null;
    }

    public static function selectById( int $id ){
        $table = self::getTablename();
        $query = "select * from $table where id=:id";
        $result = db()->query( $query )->answer( static::class )->params( ["id" => $id] )->select();
        return $result ? $result[0] : null;
    }

    public static function selectByWhere(array $conditions, bool $isOnlyOne = false){
        $table = self::getTablename();
        $where = "";
        foreach ($conditions as $field => $value){
            $where .= $where === "" ? "$field=:$field" : " and $field=:$field";
        }
        $query  = "select * from $table where $where";
        $result = db()->query($query)->answer(static::class)->params($conditions)->select();
        return count($result) > 0 ? ($isOnlyOne ? $result[0] : $result) : null;
    }

    public static function selectByQuery(string $query){
        $table  = self::getTablename();
        $result = db()->query($query)->answer(static::class)->select();
        return $result ?? [];
    }

    public function insert(){
        db()->data( $this )->insert();
    }

    public function update(){
        db()->data( $this )->update();
    }
    
    private static function getTablename() : string{
        $parts = explode( "\\", static::class );
        return strtolower( $parts[count( $parts ) -1] );
    }
}