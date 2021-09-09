<?php

namespace SteveEngine\Validate;

use SteveEngine\Singleton;

/**
 * Class Validate
 * @package SteveEngine\Validate
 */
class Validate extends Singleton {
    /**
     * @var array
     */
    private array $fields = [];
    /**
     * @var array
     */
    private array $errors = [];

    /**
     * @param array $fields
     * @return $this
     */
    public function fields(array $fields ) : Validate{
        foreach( $fields as $field){
            $this->fields[$field->name] = $field;
        }
        return $this;
    }

    /**
     * @param array $inputData
     * @return bool
     */
    public function check(array &$inputData ) : bool{
        foreach ($inputData as $key => $value){
            try{
                if (!isset($this->fields[$key])){
                    throw new \Exception ("A '$key' mező deklarálása hiányzik.");
                }
                $field = $this->fields[$key];
                if (!isset($field->type)){
                    throw new \Exception ("A '$key' mező típusa nincs megadva.");
                }
                if (!preg_match ($field->pattern, $value)){
                    if (isset($field->message)){
                        $this->errors[$key] = $field->label . " - " . $field->message;
                    }
                }else{
                    if ($field->type == "float"){
                        $inputData = str_replace(",", ".", $value);
                    }
                    if ($field->type == "boolean"){
                        $inputData[$key] = $value == "true";
                    }else{
                        settype ($inputData[$key], $field->outputType);
                    }
                }
            }catch (\Exception $e){
                $this->errors[$key] = $e->getMessage();
            }
        }
        return !( count( $this->errors ) > 0);
    }

    /**
     * @param array $inputData
     * @param string $password1
     * @param $password2
     * @param string $message
     */
    public function comparePasswords(array &$inputData, string $password1, $password2, string $message=""){
        if ($inputData[$password1] === $inputData[$password2]){
            $inputData[$password1] = password_hash($inputData[$password1], 1);
        }else{
            if ($message != ""){
                $this->errors[$password2] = $_message;
            }
        }
    }

    /**
     * @return array
     */
    public function getErrors():array{
        return $this->errors;
    }
}









