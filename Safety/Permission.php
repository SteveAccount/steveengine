<?php

namespace SteveEngine\Safety;

use SteveEngine\Data\Model;
use SteveEngine\Validate\Field;

#[AllowDynamicProperties]
class Permission extends Model {
    public static $tableName = "permissions";

    public ?int     $id;
    public string   $permission;
    public string   $pathFinderLabel;
    public ?string  $description;
//    public ?string  $dashboard;
//    public ?string  $page;
    public int      $isActive;
    public int      $createdBy;
    public string   $createdAt;
    public ?int     $modifiedBy;
    public ?string  $modifiedAt;

    public static function create(array $data) {
        $new = new self();

        if (validate()->fields($new->getFields(true))->check($data)) {

            $new->setFields($data);
            $new->createdBy     = request()->user->id;
            $new->createdAt     = (new \DateTime())->format('Y-m-d H:i:s');
            $new->id            = $new->insert();

        } else {
            throw new \Exception(json_encode(validate()->getErrors()), 422);
        }

        return $new;
    }

    public function getFields(bool $isRequired = false) : array {
        return [
            "id"            => Field::number(),
            "permission"    => Field::someText(100)->label("Név")->required($isRequired),
            "isActive"      => Field::number(0,1),
        ];
    }
}