<?php
require_once __DIR__ . "/../interfaces/TableInterface.php";
class Leave implements TableInterface {
    public $id;
    public $FromDate;
    public $ToDate;
    public $Description;
    public $PostingDate;
    public $AdminRemark;
    public $AdminRemarkDate;
    public $Status;
    public $IsRead;
    public $empid;
    public $RolID;
    public $LeaveTypeID;
    public $LeaveTypeName;
    public $EditToDate;

    public function getAttributes(): array {
        $leave = new self();
        $attributes = [];
        foreach($leave as $key => $value) {
            $attributes[] = $key;
        }
        return $attributes;
    }

    public function getTableName(): string {
        return 'tblleaves';
    }
}


