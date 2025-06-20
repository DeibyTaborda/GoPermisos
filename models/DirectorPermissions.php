<?php
require_once __DIR__ . "/../interfaces/TableInterface.php";
class DirectorPermissions  implements TableInterface{
    public $DirectorID;
    public $DepartmentID;
    public $ManagedRoleID;

    public function getAttributes(): array {
      $directorPermissions = new self();
      $attributes = [];
      foreach($directorPermissions as $key => $value) {
          $attributes[] = $key;
      }
      return $attributes;
  }

  public function getTableName(): string {
      return 'director_permissions';
  }
}