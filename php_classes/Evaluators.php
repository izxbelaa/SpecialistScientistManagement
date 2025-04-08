<?php
class Evaluators {
    public $id;
    public $user_id;
    public $request_id;

    public function __construct($id=null, $user_id=null, $request_id=null) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->request_id = $request_id;
    }
}
?>