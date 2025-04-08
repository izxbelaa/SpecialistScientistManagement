<?php
class Course {
    public $id;
    public $department_id;
    public $course_name;
    public $course_code;

    public function __construct($id=null, $department_id=null, $course_name=null, $course_code=null) {
        $this->id = $id;
        $this->department_id = $department_id;
        $this->course_name = $course_name;
        $this->course_code = $course_code;
    }
}
?>