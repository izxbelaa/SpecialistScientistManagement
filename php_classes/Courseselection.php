<?php
class Courseselection {
    public $id;
    public $user_id;
    public $course_name;
    public $course_code;

    public function __construct($id=null, $user_id=null, $course_name=null, $course_code=null) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->course_name = $course_name;
        $this->course_code = $course_code;
    }
}
?>