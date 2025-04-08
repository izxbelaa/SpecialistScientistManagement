<?php
class Academies {
    public $id;
    public $academy_name;
    public $academy_code;

    public function __construct($id=null, $academy_name=null, $academy_code=null) {
        $this->id = $id;
        $this->academy_name = $academy_name;
        $this->academy_code = $academy_code;
    }
}
?>