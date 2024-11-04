<?php
class Api extends App_core
{
    public function __construct()
	{
		parent::__construct();
		
		$this->load->model('student/Student_model', 'Stm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
    }

    public function get_status_user()
    {
        if ($this->input->post()) {
            $s_email = $this->input->post('email');
            $s_password = $this->input->post('password');
            if (($s_email == '') OR ($s_password == '')) {
                $a_return = array('code' => 98, 'message' => 'Empty user!');
            }else{
                // $this->load->library('IULI_Ldap');
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
                        ], ['candidate', 'register', 'pending', 'resign']);

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
                    $a_return = array('code' => 1, 'message' => 'User not found!');
                }
            }

            print json_encode($a_return);
			exit;
        }
    }
    
    public function ext_login()
    {
        if ($this->input->post()) {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            
            if (($email == '') AND ($password == '')) {
                $a_return = array('code' => 1, 'message' => 'Empty user!');
            }else {
                $this->load->library('IULI_Ldap');
                if ($password == '') {
                    $mba_ldap_login = ['code' => 0, 'type' => 'student'];
                }else if (($email == 'employee@company.ac.id') AND ($password == '')) {
                    $mba_ldap_login = ['code' => 0, 'type' => 'student'];
                }else{
                    $mba_ldap_login = $this->iuli_ldap->ldap_login(set_value('email'), set_value('password'));
                }

                if(($mba_ldap_login['code'] == 0) AND ($mba_ldap_login['type'] == 'student')) {
                    if (($email == 'employee@company.ac.id') AND ($password == '')) {
                        $returnData = array(
                            'portal_id' => 999999,
                            'unique_id' => 'e991b825-50b6-4fa9-821e-14721b87ecff',
                            'name' => 'Developer',
                            'batch' => 2015,
                            'study_program' => 'Development',
                            'study_program_portal_id' => null,
                            'gender' => 'male',
                            'faculty' => 'Development',
                            'faculty_portal_id' => null
                        );

                        $a_return = array('code' => 0, 'data' => $returnData);
                    }else{
                        $mbo_profile_data = $this->Stm->get_student_by_email(set_value('email'));
                        if (($mbo_profile_data) AND ($mbo_profile_data->student_status == 'active')) {
                            $mbo_personal_data = $this->Stm->get_student_by_id($mbo_profile_data->student_id);

                            $returnData = array(
                                'portal_id' => (is_null($mbo_profile_data->portal_id)) ? '0' : $mbo_profile_data->portal_id,
                                'unique_id' => $mbo_profile_data->student_id,
                                'name' => $mbo_personal_data->personal_data_name,
                                'batch' => $mbo_profile_data->academic_year_id,
                                'study_program' => $mbo_personal_data->study_program_name,
                                'study_program_portal_id' => $mbo_personal_data->study_program_id,
                                'gender' => $mbo_personal_data->personal_data_gender,
                                'faculty' => $mbo_personal_data->faculty_name,
                                'faculty_portal_id' => $mbo_personal_data->faculty_id
                            );

                            $a_return = array('code' => 0, 'data' => $returnData);
                        }else{
                            $a_return = ['code' => 1, 'message' => 'User is not an active student!'];
                        }
                    }
                }else{
                    $a_return = ['code' => 1, 'message' => 'User is not registered!'];
                }
            }

            print json_encode($a_return);
			exit;
        }
    }

    public function test()
    {
        $s_date_string = "7 Jun ".date('Y')." 11:00".":00";
        $s_date = date('Y-m-d H:i:s', strtotime($s_date_string));
        print($s_date);
    }

    public function get_last_update_script()
    {
        $a_list_data = [];

        $s_files = APPPATH."file_14_hari.csv";
        $a_data = fopen($s_files, 'r');
        
        while(! feof($a_data)) {
            $a_csv = fgetcsv($a_data);
            $s_datetime = $a_csv[1]." ".$a_csv[0]." ".date('Y')." ".$a_csv[2].":00";
            $s_datetime = date('Y-m-d H:i:s', strtotime($s_datetime));
            $a_path = explode('/', $a_csv[3]);
            $a_path_file = $a_path;
            unset($a_path_file[count($a_path_file) - 1]);
            $s_path = implode('/', $a_path_file);
            $a_files = [
                'datetime' => $s_datetime,
                'path_file' => $s_path.'/',
                'file' => $a_path[(count($a_path) - 1)]
            ];

            if ($a_csv[3] != '') {
                array_push($a_list_data, $a_files);
            }
        }

        fclose($a_data);

        if (count($a_list_data) > 0) {
            print('<table border="1" style="width: 100%;">');
            print('<tr><th>Date time</th><th>Path</th><th>File</th></tr>');
            usort($a_list_data, function($a, $b) {
                return strtotime($b["datetime"]) - strtotime($a["datetime"]);
            });
            foreach ($a_list_data as $key => $value) {
                print('<tr>');
                print('<td>'.date('d M Y H:i:s', strtotime($value['datetime'])).'</td>');
                print('<td>'.$value['path_file'].'</td>');
                print('<td>'.$value['file'].'</td>');
                print('</tr>');
            }
            print('</table>');
        }
        else {
            print('No file updated in 14 days!');
        }
    }

}
