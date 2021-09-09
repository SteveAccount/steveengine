<?php

namespace SteveEngine\Data;

use SteveEngine\Config;
use SteveEngine\Singleton;

class Database extends Singleton{
    private $connectionInfo;
    private $pdo;
    private $query;
    private $answer = "array";
    private $params;
    private $data;
    private $tableName;

    public function prepare( array $connectionInfo, bool $hasDatabase = true ){
        $this->connectionInfo = $connectionInfo["connection"];
        $this->pdo = $this->getConnection( $hasDatabase );
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Mysql query string. 
     */
    public function query( string $query ) : Database{
        $this->query = $query;
        return $this;
    }

    /**
     * A select lekérdezés eredményét a megadott típusban adja vissza.
     * Alapértelmezett: array. 
     */
    public function answer( string $answer ) : Database{
        $this->answer = $answer;
        return $this;
    }

    /**
     * A lekérdezéshez kapcsolódó paraméterek.
     * @param array $params [ fieldname => value, ....] 
     */
    public function params( array $params ) : Database{
        $this->params = $params;
        return $this;
    }

    /**
     * Mysql insert esetén a táblába írandó adatok array<object> formátumban. 
     */
    public function data( $data ) : Database{
        if( is_array( $data )){
            $this->data = $data;
        }else{
            $this->data = [$data];
        }
        return $this;
    }

    /**
     * Mysql insert esetén kell megadni, ha a táblanév más, mint a classname(object). 
     */
    public function tableName( string $tableName ) : Database{
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Futtat egy lekérdezést. 
     */
    public function run() : \PDOStatement{
        $stmt = $this->pdo->prepare( $this->query );
        if ( isset( $this->params )){
            $stmt->execute( $this->params );
        }else{
            $stmt->execute();
        }
        return $stmt;
    }

    /**
     * Select lekérdezés futtatása.
     */
    public function select() : array{
        $stmt = $this->run();
        $result = [];
        if ( $this->answer == "array" ){
            $result = $stmt->fetchAll();
        }else{
            while ( $row = $stmt->fetchObject( $this->answer )){
                $result[] = $row;
            }
        }
        $this->clear();
        return $result;
    }

    /**
     * Select lekérdezés futtatása. Egyetlen értékkel tér vissza.
     */
    public function scalar(){
        $stmt = $this->run();
        $result = $stmt->fetchAll();
        $this->clear();
        return isset( $result[0][0] ) ? $result[0][0] : null;
    }

    public function insert() : int{
        $parts = explode( "\\", get_class( $this->data[0] ));
        $className =  $parts[ count( $parts ) - 1 ];
        $tableName = strtolower( isset( $this->tableName ) ? $this->tableName : $className );
        
        //Keys
        $fields = $valueFields = "";
        foreach ( $this->data[0] as $key => $value){
            if ( $key != "id" ){
                $fields .= $fields == "" ? $key : ", $key";
                $valueFields .= $valueFields == "" ? ":$key" : ", :$key";
            }
        }
        $query = "insert ignore into $tableName ($fields) values ($valueFields)";
        
        $stmt = $this->pdo->prepare( $query );
        foreach ( $this->data as $row ){
            $values = (array)$row;
            unset( $values["id"] );
            $stmt->execute( $values );
        }
        
        $lastId = $this->pdo->lastInsertId();
        return $lastId;
    }

    public function update(){
        $parts = explode( "\\", get_class( $this->data[0] ));
        $className =  $parts[ count( $parts ) - 1 ];
        $tableName = strtolower( isset( $this->tableName ) ? $this->tableName : $className );
        
        //Keys
        $id = 0;
        $fields = "";
        foreach ( $this->data[0] as $key => $value){
            if ( $key != "id" ){
                if( is_null( $value )){
                    $fields .= $fields == "" ? "$key=null" : ", $key=null";
                }elseif( is_string( $value )){
                    $fields .= $fields == "" ? "$key='$value'" : ", $key='$value'";    
                }elseif( is_bool( $value )){
                    $value = $value ? 1 : 0;
                    $fields .= $fields == "" ? "$key=$value" : ", $key=$value";
                }
                else{
                    $fields .= $fields == "" ? "$key=$value" : ", $key=$value";
                }
            }else{
                $id = $value;
            }
        }
        $query = "update $tableName set $fields where id=$id";
        $stmt = $this->pdo->prepare( $query );
        $stmt->execute();
        return;
    }

    public function beginTransaction(){
        $this->pdo->beginTransaction();
    }

    public function endTransaction( bool $isCommit ){
        $isCommit ? $this->pdo->commit() : $this->pdo->rollBack();
    }

    private function clear(){
        $this->query = $this->params = null;
        $this->answer = "array";
        $this->tableName = null;
    }

    protected function getConnection( bool $hasDatabase = true ) : \PDO{
        try{
            $dsn = "mysql:host=".$this->connectionInfo["server"].";";
            if ( $this->connectionInfo["database"] != "" && $hasDatabase ){
                $dsn .= " dbname=".$this->connectionInfo["database"].";";
            }
            $dsn .= " charset=utf8";
            $pdo = new \PDO( $dsn, $this->connectionInfo["username"], $this->connectionInfo["password"] );
            return $pdo;
        }catch(\PDOException $e){
            die("Hiba történt az adatbázishoz való kapcsolódás során!");
        }
    }
}