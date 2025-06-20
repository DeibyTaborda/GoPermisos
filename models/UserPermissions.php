<?php
require_once __DIR__ . "/../interfaces/TableInterface.php";

class UserPermissions implements TableInterface {
    public $id;
    public $UserID;
    public $DepartmentID;
    public $RoleID;
    public $SedeID;

    public function getTableName(): string {
        return "user_permissions";
    }
    
    public function getAttributes(): array {
      $userPermissions = new self();
      $attributes = [];
      foreach($userPermissions as $key => $value) {
          $attributes[] = $key;
      }
      return $attributes;
  }
}