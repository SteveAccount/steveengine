<?php

namespace SteveEngine\Validate;

use SteveEngine\Validate\FieldType;

class Field{
    public $name;
    public $label;
    public $type = FieldType::TEXT;
    public $outputType = "string";
    public $isRequired = false;
    public $min;
    public $max;
    public $maxLength = 100;
    public $pattern;
    public $message;

    public static function new() : Field{
        return new self();
    }

    public function name( string $name ) : Field{
        $this->name = $name;
        return $this;
    }

    public function label( string $label ) : Field{
        $this->label = $label;
        return $this;
    }

    public function type( string $type ) : Field{
        $this->type = $type;
        return $this;
    }

    public function outputType( string $outputType ) : Field{
        $this->outputType = $outputType;
        return $this;
    }

    public function required() : Field{
        $this->isRequired = true;
        return $this;
    }

    public function min( int $min ) : Field{
        $this->min = $min;
        $this->type( FieldType::INTEGER );
        return $this;
    }

    public function max( int $max ) : Field{
        $this->max = $max;
        $this->type( FieldType::INTEGER );
        $this->maxLength( strlen( $max ));
        return $this;
    }

    public function maxLength( int $maxLength ) : Field{
        $this->maxLength = $maxLength;
        return $this;
    }

    public function pattern( string $pattern ) : Field{
        $this->pattern = $pattern;
        return $this;
    }

    public function message( string $message ) : Field{
        $this->message = $message;
        return $this;
    }

    public static function taxNumber() : Field{
        $newField = new self();
        $newField
            ->name("taxNumber")
            ->label("Adószám")
            ->type(FieldType::TEXT)
            ->pattern("/^[0-9]{8}-?[0-9]{1}-?[0-9]{2}$/");
        return $newField;
    }

    public static function bankAccountNumber() : Field{
        $newField = new self();
        $newField
            ->name("bankAccountNumber")
            ->label("Bankszámlaszám")
            ->type(FieldType::TEXT)
            ->pattern("/^[0-9]{8}(-[0-9]{8}){1,2}$/");
        return $newField;
    }

    public static function emailAddress() : Field{
        $newField = new self();
        $newField
            ->name("email")
            ->label("Emailcím")
            ->type(FieldType::TEXT)
            ->maxLength(100)
            ->pattern("/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD");
        return $newField;
    }

    public static function password() : Field{
        $newField = new self();
        $newField
            ->name("password")
            ->label("Jelszó")
            ->type(FieldType::PASSWORD)
            ->maxLength(20)
            ->pattern("/^[a-z0-9]{5,20}$/i");
        return $newField;
    }

    public static function repeatPassword() : Field{
        $newField = new self();
        $newField->name("repeatPassword")->label("Jelszó megismétlése")->type(FieldType::PASSWORD)->maxLength(20)->pattern("/^[a-z0-9]{5,20}$/");
        return $newField;
    }

    public static function regName() : Field{
        $newField = new self();
        $newField->name("name")->label("Név")->type(FieldType::TEXT)->maxLength(45)->pattern("/^[a-zöüóőúéáí-]{5,20}$/");
        return $newField;
    }

    public static function number() : Field{
        $newField = new self();
        $newField->type(FieldType::INTEGER)->pattern("/^[0]{1}|[0-9]{1,10}$/");
        return $newField;
    }

    public static function someName() : Field{
        $newField = new self();
        $newField->type(FieldType::TEXT)->maxLength(100)->pattern("/^[a-zöüóőúéáí-]{5,20}$/i");
        return $newField;
    }
}