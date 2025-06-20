<?php
require_once __DIR__ . "/../interfaces/TableInterface.php";
class Department implements TableInterface {
    public $id;
    public $DepartmentName;
    public $DepartmentShortName;
    public $DepartmentCode;
	public $StatusId;
    public $CreationDate;

    public function getAttributes(): array {
        $department = new self();
        $attributes = [];
        foreach($department as $key => $value) {
            $attributes[] = $key;
        }
        return $attributes;
    }

    public function getTableName(): string {
        return 'tbldepartments';
    }
}
