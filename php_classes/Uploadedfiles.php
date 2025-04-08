<?php
class Uploadedfiles {
    public $id;
    public $user_id;
    public $file_name;
    public $file_path;
    public $uploaded_at;

    public function __construct($id=null, $user_id=null, $file_name=null, $file_path=null, $uploaded_at=null) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->file_name = $file_name;
        $this->file_path = $file_path;
        $this->uploaded_at = $uploaded_at;
    }
}
?>