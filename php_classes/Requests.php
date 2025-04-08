<?php
class Requests {
    public $id;
    public $course_selection_id;
    public $education_id;
    public $professional_title_id;
    public $work_experience_id;
    public $upload_file_id;
    public $consent_forms_id;

    public function __construct($id=null, $course_selection_id=null, $education_id=null, $professional_title_id=null, $work_experience_id=null, $upload_file_id=null, $consent_forms_id=null) {
        $this->id = $id;
        $this->course_selection_id = $course_selection_id;
        $this->education_id = $education_id;
        $this->professional_title_id = $professional_title_id;
        $this->work_experience_id = $work_experience_id;
        $this->upload_file_id = $upload_file_id;
        $this->consent_forms_id = $consent_forms_id;
    }
}
?>