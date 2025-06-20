<?php
require_once __DIR__ . "/../interfaces/TableInterface.php";
class DepartmentsSede implements TableInterface {
    public $id;
    public $sede_id;
    public $department_id;


    public function getAttributes(): array {
        $department_sede = new self();
        $attributes = [];
        foreach($department_sede as $key => $value) {
            $attributes[] = $key;
        }
        return $attributes;
    }

    public function getTableName(): string {
        return 'departments_sede';
    }

}