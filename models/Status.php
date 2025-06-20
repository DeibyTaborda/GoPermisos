<?php
require_once __DIR__ . "/../interfaces/TableInterface.php";

class Status implements TableInterface {
    public $id;
    public $status;

    public function getAttributes(): array {
        $status = new self();
        $attributes = [];
        foreach($status as $key => $value) {
            $attributes[] = $key;
        }
        return $attributes;
    }

    public function getTableName(): string {
        return 'tblstatus';
    }


}