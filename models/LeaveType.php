<?php
require_once __DIR__ . "/../interfaces/TableInterface.php";
class LeaveType implements TableInterface {
    public $id;
    public $LeaveType;
    public $Description;
    public $CreationDate;
    public $Status;
    
    public function getAttributes(): array {
      $leaveType = new self();
      $attributes = [];
      foreach($leaveType as $key => $value) {
          $attributes[] = $key;
      }
      return $attributes;
  }

  public function getTableName(): string {
      return 'tblleavetype';
  }
}
