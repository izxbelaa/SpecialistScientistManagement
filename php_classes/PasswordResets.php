<?php
class PasswordResets {
    public $id;
    public $email;
    public $token;
    public $expires_at;

    public function __construct($id=null, $email=null, $token=null, $expires_at=null) {
        $this->id = $id;
        $this->email = $email;
        $this->token = $token;
        $this->expires_at = $expires_at;
    }
}
?>