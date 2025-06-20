<?php
require_once __DIR__ . "/../interfaces/TableInterface.php";
class Sede implements TableInterface {
    public $id;
    public $sede;
    public $status_id;

    public function getAttributes(): array {
        $sede = new self();
        $attributes = [];
        foreach($sede as $key => $value) {
            $attributes[] = $key;
        }
        return $attributes;
    }

    public function getTableName(): string {
        return 'sede';
    }
}