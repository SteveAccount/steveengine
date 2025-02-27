<?php

namespace SteveEngine\Validate;

use SteveEngine\Validate\FieldType;

class Field{
    public $name;
    public $label;
    public $list        = [];
    public $type        = FieldType::TEXT;
    public $outputType  = "string";
    public $isRequired  = false;
    public $isUnique    = false;
    public $tableName   = "";
    public $min;
    public $max;
    public $maxLength   = 100;
    public $pattern;
    public $message;
    public $checkFunction;
    public $item;

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

    public function required(bool $isRequired = true) : Field{
        $this->isRequired = $isRequired;
        return $this;
    }

    public function unique(string $tableName) : Field {
        $this->isUnique     = true;
        $this->tableName    = $tableName;

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

    public function list(array $list) : Field{
        $this->list = $list;
        return $this;
    }

    public static function enums(string $class, bool $allowAll = false) : Field {
        $reflection = new \ReflectionClass($class);
        $constants  = $reflection->getConstants();

        if ($allowAll) {
            $constants["all"] = "all";
        }

        $newField = new self();
        $newField
            ->type(FieldType::TEXT)
            ->list(array_values($constants));

        return $newField;
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

    public static function euTaxNumber() : Field{
        $newField = new self();
        $newField
            ->name("taxNumber")
            ->label("Adószám")
            ->type(FieldType::TEXT)
            ->pattern("/^[a-z]{2}[0-9]{8}$/");
        return $newField;
    }

    public static function taxIdentification() : Field{
        $newField = new self();
        $newField
            ->name("taxIdentification")
            ->label("Adóazonosító")
            ->type(FieldType::TEXT)
            ->pattern("/^[0-9]{10}$/");
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

    public static function tajCardNumber() : Field{
        $newField = new self();
        $newField
            ->name("tajCardNumber")
            ->label("TAJ-szám")
            ->type(FieldType::TEXT)
            ->pattern("/^[0-9]{9}$/");
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

    public static function passwordHash() : Field{
        $newField = new self();
        $newField
            ->name("password")
            ->label("Jelszó")
            ->type(FieldType::PASSWORD)
            ->maxLength(200)
            ->pattern("/^[a-z0-9]{5,200}$/i");
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

    public static function number(int $min = 0, int $max = 100) : Field{
        $newField = new self();
        $newField
            ->min($min)
            ->max($max)
            ->type(FieldType::INTEGER);
        return $newField;
    }

    public static function someName() : Field{
        $newField = new self();
        $newField->type(FieldType::TEXT)->maxLength(100)->pattern("/^[ a-zöüóőúéáí0-9-\.'\"_]*$/ui");
        return $newField;
    }
    
    public static function communityTaxNumber() : Field{
        $newField = new self();
        $newField
            ->label("Közösségi adószám")
            ->pattern("/^[a-z]{2}|[0-9]{8}$/i");
        return $newField;
    }

    public static function companyRegistrationCode() : Field{
        $newField = new self();
        $newField
            ->label("Cégjegyzékszám")
            ->pattern("/^[0-9]{2}-[0-9]{2}-[0-9]{6}$/");
        return $newField;
    }

    public static function someText(int $maxLength = 100) : Field{
        $newField = new self();
        $newField
            ->maxLength($maxLength)
            ->pattern("/^[ a-zöüóúőűáéí0-9_,;\+\-\.'\/\?~:\(\)]*$/ui");
        return $newField;
    }

    public static function multiRowText(int $maxLength = 100) : Field{
        $newField = new self();
        $newField
            ->maxLength($maxLength)
            ->pattern("/^[ a-zöüóúőűáéí0-9\-_\.'\/\?]*$/uim");
        return $newField;
    }

    public static function date() : Field{
        $newField = new self();
        $newField
            ->type(FieldType::DATE)
            ->pattern("/^\d{4}[\-\/\s]?((((0[13578])|(1[02]))[\-\/\s]?(([0-2][0-9])|(3[01])))|(((0[469])|(11))[\-\/\s]?(([0-2][0-9])|(30)))|(02[\-\/\s]?[0-2][0-9]))$/")
            ->message("Nem megfelelő formátum.");
        return $newField;
    }

    public static function dateTime() : Field{
        $newField = new self();
        $newField
            ->type(FieldType::TEXT)
            ->pattern("/^(?=\d)(?:(?:1[6-9]|[2-9]\d)?\d\d([-.\/])(?:1[012]|0?[1-9])\1(?:31(?<!.(?:0[2469]|11))|(?:30|29)(?<!.02)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/m")
            ->message("Nem megfelelő formátum.");
        return $newField;
    }

    public static function float() : Field{
        $newField = new self();
        $newField
            ->type(FieldType::FLOAT);
        return $newField;
    }

    public static function checked() : Field{
        $newField = new self();
        $newField
            ->pattern("/^true$/");
        return $newField;
    }

    public static function css(int $maxLength = 100) : Field{
        $newField = new self();
        $newField
            ->maxLength($maxLength)
            ->pattern("/^[ a-zöüóúőűáéí0-9_,;#\+\-\.'\/\?~:\(\)]*$/ui");
        return $newField;
    }

    public static function url(int $maxLength = 200) : Field{
        $newField = new self();
        $newField
            ->maxLength($maxLength)
            ->pattern("/^[a-z0-9\-\.\/\?:]*$/");
        return $newField;
    }

    public static function htmlContent() : Field {
        $newField = new self();
        $newField
            ->type(FieldType::HTML)
            ->checkFunction = function($value) {

            if (preg_match('/<iframe.*sandbox="allow-scripts allow-same-origin".*src="https:\/\/(www\.)?youtube\.com\/embed\/.*".*><\/iframe>/i', $value) === 1) {
                //toLog("Iframe sandbox attributummal: " . $value);
                return false;
            }

            if (preg_match('/<iframe.*src="https:\/\/(www\.)?youtube\.com\/embed\/.*".*><\/iframe>/i', $value) === 1) {
                //toLog("Youtube video Iframe: " . $value);
                return false;
            }

            if (preg_match('/<iframe/i', $value) === 1) {
                // toLog("Nem engedélyezett Iframe: " . $value);
                return true;
            }

            if (preg_match("/(script|onclick|onchange|onmouse|onkey|onload)/i", $value) === 1) {
                // toLog("Veszélyes elemet tartalmaz: " . $value);
                return true;
            }

            //toLog("Valid Tartalom: " . $value);
            return false;
        };
        return $newField;
    }

    public static function array() {
        $newField = new self();
        $newField
            ->type(FieldType::ARRAY);

        return $newField;
    }

    public static function dbField() : Field {
        $newField = new self();
        $newField
            ->type(FieldType::TEXT)
            ->pattern("/^[a-z0-9_]{0,50}$/i");
        return $newField;
    }

    public static function bool() : Field {
        $newField = new self();
        $newField
            ->type(FieldType::BOOL);

        return $newField;
    }
}