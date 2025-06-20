<?php 
require_once __DIR__ . "/../interfaces/TableInterface.php";
class User implements TableInterface {
    public $id;
    public $EmpId;
    public $FirstName;
    public $LastName;
    public $EmailId;
    public $Password;
    public $RolID;
    public $SedeID;
    public $Gender;
    public $Dob;
    public $Address;
    public $City;
    public $Country;
    public $Phonenumber;
    public $Status;
    public $RegDate;

    public function getAttributes(): array {
        $user = new self();
        $attributes = [];
        foreach($user as $key => $value) {
            $attributes[] = $key;
        }
        return $attributes;
    }

    public function getTableName(): string {
        return 'tblusers';
    }
}