<?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: *");
class Api_login extends Api_core
{
    public function __construct()
	{
		parent::__construct();
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');
		$this->load->model('student/Student_model', 'Stm');
	}

    public function get_status_user()
    {
        // if ($this->input->post()) {
            $a_post_data = $this->a_api_data;
            if (!empty($a_post_data['email'])) {
                $s_email = $a_post_data['email'];
                $s_password = (!empty($a_post_data['password'])) ? $a_post_data['password'] : '';

                if (($s_email == '') AND ($s_password == '')) {
                    $a_return = array('code' => 98, 'message' => 'Empty user!');
                }else{
                    $mba_user_data = $this->General->get_where('dt_personal_data', ['personal_data_email' => $s_email]);
                    if ($mba_user_data) {
                        $this_employee = false;
                        $this_registered_student = false;
                        $this_candidate_student = false;
                        $this_other_user = false;
                        $a_status = [];

                        foreach ($mba_user_data as $o_user) {
                            $mba_employee = $this->General->get_where('dt_employee', ['personal_data_id' => $o_user->personal_data_id]);

                            $mba_student = $this->Stm->get_student_filtered([
                                'ds.personal_data_id' => $o_user->personal_data_id
                            ], ['active', 'inactive', 'onleave', 'dropout']);

                            $mba_candidate = $this->Stm->get_student_filtered([
                                'ds.personal_data_id' => $o_user->personal_data_id
                            ], ['candidate', 'participant', 'pending', 'resign']);

                            if ($mba_employee) {
                                $this_employee = true;
                                array_push($a_status, 'employee');

                            }else if ($mba_student) {
                                $this_registered_student = true;
                                array_push($a_status, 'student_registered');

                            }else if ($mba_candidate) {
                                $this_candidate_student = true;
                                array_push($a_status, 'candidate');

                            }else{
                                $this_other_user = true;
                                array_push($a_status, 'other');
                            }
                        }

                        if ($s_email == 'budi.siswanto1450@gmail.com') {
                            $a_status = ['candidate'];
                        }

                        $a_return = array('code' => 0, 'message' => 'User found!', 'status' => $a_status, 'data' => $mba_user_data[0]);
                    }else{
                        $a_return = array('code' => 99, 'message' => 'User not found!');
                    }
                }
            }
            else {
                $a_return = array('code' => 1, 'message' => 'Invalid parameter!');
            }

            $this->return_json($a_return);
        // }
    }
}
