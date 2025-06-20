<?php
require_once __DIR__ . "/../interfaces/TableInterface.php";
class UserDepartment implements TableInterface {
    public $id;
    public $UserID;
    public $DepartmentID;

    public function getAttributes(): array {
        $userDepartment = new self();
        $attributes = [];
        foreach($userDepartment as $key => $value) {
            $attributes[] = $key;
        }
        return $attributes;
    }

    public function getTableName(): string {
        return 'user_departments';
    }
}