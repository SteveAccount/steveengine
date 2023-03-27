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
        $this->fields = [];
        foreach( $fields as $name => $field){
            $this->fields[$name] = $field;
        }
        return $this;
    }
    
    public function check(array &$inputData ) : bool{
        //A felesleges mezők eltávolítása a küldött adatok közül
        foreach ($inputData as $key => $value){
            if (!isset($this->fields[$key])){
                unset($inputData[$key]);
            }
        }

        //A hiányzó mezők ellenőrzése
        foreach ($this->fields as $key => $field){
            if (!isset($inputData[$key]) && $field->isRequired){
                $this->errors[$key] = $field->label . " - Kötelező mező";
            }
        }

        //Ellenőrzés
        foreach ($inputData as $key => $fieldValue){
            try{
                $field = $this->fields[$key];

                $values = is_array($fieldValue) ? $fieldValue : [$fieldValue];
                foreach ($values as &$value){
                    //Kötelező mező ellenőrzése
                    if ($field->isRequired === true && $value === ""){
                        throw new \Exception($field->label . " - Kötelező mező");
                    } else{
                        if ($value !== ""){
                            //Pattern ellenőrzése, ha van
                            if ($field->pattern){
                                if (!preg_match ($field->pattern, $value)){
                                    throw new \Exception($field->label . " - A formátum nem megfelelő vagy tiltott karaktert tartalmaz.");
                                }
                            }

                            //Ellenőrzés a mező típusa alapján
                            if ($field->list !== []){
                                if (!in_array($value, $field->list)){
                                    throw new \Exception($field->label . " - Az elem nem szerepel a listában.");
                                }
                            } else{
                                if ($field->type == "float"){
                                    $value = str_replace(",", ".", $value);
                                    $value = (float)$value;
                                }
                                if ($field->type == "boolean"){
                                    $value = $value == "true";
                                }
                            }
                        } else {
                            if ($field->type == "date"){
                                $value = null;
                            }
                        }
                    }
                }
                $inputData[$key] = is_array($fieldValue) ? $values : $values[0];
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
                $this->errors[$password2] = $message;
            }
        }
    }

    /**
     * @return array
     */
    public function getErrors():array{
        return $this->errors;
    }

    public function addError(string $key, string $error) {
        $this->errors[$key] = $error;
    }
}









