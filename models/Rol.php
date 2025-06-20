<?php
require_once __DIR__ . "/../interfaces/TableInterface.php";
class Rol implements TableInterface {
    public $id;
    public $rol;
    public $created_at;

    public function getAttributes(): array {
        $rol = new self();
        $attributes = [];
        foreach($rol as $key => $value) {
            $attributes[] = $key;
        }
        return $attributes;
    }

    public function getTableName(): string {
        return 'tblrol';
    }
}