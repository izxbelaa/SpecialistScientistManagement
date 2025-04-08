<?php
class Workexperience {
    public $id;
    public $user_id;
    public $start_date;
    public $end_date;
    public $employer_name;
    public $job_title;
    public $employment_type;
    public $responsibilities;

    public function __construct($id=null, $user_id=null, $start_date=null, $end_date=null, $employer_name=null, $job_title=null, $employment_type=null, $responsibilities=null) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->employer_name = $employer_name;
        $this->job_title = $job_title;
        $this->employment_type = $employment_type;
        $this->responsibilities = $responsibilities;
    }
}
?>