<?php
class Reports {
    public $id;
    public $template;

    public function __construct($id=null, $template=null) {
        $this->id = $id;
        $this->template = $template;
    }
}
?>