<?php
class Consentforms {
    public $id;
    public $user_id;
    public $consent_type;
    public $agreed;
    public $agreed_at;

    public function __construct($id=null, $user_id=null, $consent_type=null, $agreed=null, $agreed_at=null) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->consent_type = $consent_type;
        $this->agreed = $agreed;
        $this->agreed_at = $agreed_at;
    }
}
?>