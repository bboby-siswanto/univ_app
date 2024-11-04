<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class IULI_Ldap {
    private $CI;
    function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database('production');
    }
    function ldap_login($email, $password) {
        $query = $this->CI->db->get_where('dt_employee', ['employee_email' => $email]);
        $type = '';$user_id = '';
        $data_personal = false;
        if ($query->num_rows() > 0) {
            $data_employee = $query->first_row();
            $user_id = $data_employee->employee_id;
            $data_personal = $this->CI->db->get_where('dt_personal_data', ['personal_data_id' => $data_employee->personal_data_id]);
            $data_personal = ($data_personal->num_rows() > 0) ? $data_personal->first_row() : false;
            $type = 'employee';
        }
        else {
            $query_student = $this->CI->db->get_where('dt_student', ['student_email' => $email]);
            if ($query_student->num_rows() > 0) {
                $data_student = $query_student->first_row();
                $user_id = $data_student->student_id;
                $data_personal = $this->CI->db->get_where('dt_personal_data', ['personal_data_id' => $data_student->personal_data_id]);
                $data_personal = ($data_personal->num_rows() > 0) ? $data_personal->first_row() : false;
                $type = 'employee';
            }
        }

        if ($data_personal) {
            if (password_verify($password, $data_personal->personal_data_password)) {
                return array(
                    'code' => 0,
                    'type' => 'staff',
                    'personal' => $data_personal,
                    'user_id' => $user_id,
                    'message' => 'Sukses'
                );
            }
            else {
                return array('code' => 2, 'message' => 'email not found');
            }
        }
        else {
            return array('code' => 1, 'message' => 'email not found');
        }
    }

    function create_password($password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        return $password_hash;
    }
}