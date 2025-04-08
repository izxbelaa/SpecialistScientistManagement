<?php
class Departments {
    public $id;
    public $academy_id;
    public $department_name;
    public $department_code;

    public function __construct($id=null, $academy_id=null, $department_name=null, $department_code=null) {
        $this->id = $id;
        $this->academy_id = $academy_id;
        $this->department_name = $department_name;
        $this->department_code = $department_code;
    }
}
?>