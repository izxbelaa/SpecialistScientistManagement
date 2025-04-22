<?php
class Users {
    public $id;
    public $first_name;
    public $last_name;
    public $middle_name;
    public $email;
    public $password;
    public $type_of_user;
    public $logged_in;
    public $disabled_user;

    public function __construct(
        $id = null, $first_name = null, $last_name = null, $middle_name = null,
        $email = null, $password = null, $type_of_user = null,
        $logged_in = null, $disabled_user = null
    ) {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->middle_name = $middle_name;
        $this->email = $email;
        $this->password = $password;
        $this->type_of_user = $type_of_user;
        $this->logged_in = $logged_in;
        $this->disabled_user = $disabled_user;
    }

    public function getUserTypeName() {
        $types = [
            0 => "Χρήστης",             // User
            1 => "Υποψήφιος",           // Candidate
            2 => "Ειδικός Επιστήμονας", // Special Scientist
            3 => "Επιθεωρητής",         // Inspector
            4 => "Προϊστάμενος Ανθρώπινου Δυναμικού", // Head of HR
            5 => "Διαχειριστής"         // Admin
        ];
    
        return $types[$this->type_of_user] ?? "Άγνωστος"; // Unknown
    }
    
    
}
?>
