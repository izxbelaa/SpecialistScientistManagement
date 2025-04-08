<?php
class Education {
    public $id;
    public $user_id;
    public $start_date;
    public $end_date;
    public $institution;
    public $major;
    public $grade;

    public function __construct($id=null, $user_id=null, $start_date=null, $end_date=null, $institution=null, $major=null, $grade=null) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->institution = $institution;
        $this->major = $major;
        $this->grade = $grade;
    }
}
?>