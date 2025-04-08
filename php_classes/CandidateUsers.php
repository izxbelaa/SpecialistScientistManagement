<?php
class CandidateUsers {
    public $id;
    public $first_name_greek;
    public $middle_name_greek;
    public $last_name_greek;
    public $patronymic;
    public $gender;
    public $id_number;
    public $social_security_number;
    public $dob;
    public $address;
    public $city;
    public $country;
    public $mobile_phone;
    public $landline_phone;
    public $university_email;
    public $id_kind;
    public $user_id;

    public function __construct($id=null, $first_name_greek=null, $middle_name_greek=null, $last_name_greek=null, $patronymic=null, $gender=null, $id_number=null, $social_security_number=null, $dob=null, $address=null, $city=null, $country=null, $mobile_phone=null, $landline_phone=null, $university_email=null, $id_kind=null, $user_id=null) {
        $this->id = $id;
        $this->first_name_greek = $first_name_greek;
        $this->middle_name_greek = $middle_name_greek;
        $this->last_name_greek = $last_name_greek;
        $this->patronymic = $patronymic;
        $this->gender = $gender;
        $this->id_number = $id_number;
        $this->social_security_number = $social_security_number;
        $this->dob = $dob;
        $this->address = $address;
        $this->city = $city;
        $this->country = $country;
        $this->mobile_phone = $mobile_phone;
        $this->landline_phone = $landline_phone;
        $this->university_email = $university_email;
        $this->id_kind = $id_kind;
        $this->user_id = $user_id;
    }
}
?>