<?php

class Employeedestroyed extends Api_core
{
    function __construct()
    {
        parent::__construct();
        $s_environment = 'production';
		if($this->session->userdata('auth')){
			$s_environment = $this->session->userdata('environment');
		}
		$this->db = $this->load->database($s_environment, true);

		switch ($s_environment) {
			case 'production':
				$this->load->model('Admission_model', 'Sm');
				break;

			case 'sanbox':
				$this->load->model('Staging_model', 'Sm');
				break;
			
			default:
				$this->load->model('Staging_model', 'Sm');
				break;
        }
        
        $this->load->model('Portal_model', 'Pm');
        
        $this->load->model('employee/Employee_model','Emm');
        $this->load->model('personal_data/Personal_data_model','Pdm');
    }

    public function sync_employee()
    {
        $mbo_portal_employee = $this->Pm->retrieve_data('employee');
        $mbo_portal_lecturer = $this->Pm->retrieve_data('lecturer');
        $start = strtotime("now");

        $this->db->trans_start();
        print('<pre>');

        $this->sync_employee_lecturer($mbo_portal_lecturer);

        print('<br>');

        $this->sync_employee_non_lecturer($mbo_portal_employee);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            print('<b>Rollback</b>');
        }else{
            $this->db->trans_commit();
            print('<b>Data commit</b>');
            $end = strtotime("now");
            $interval = $end - $start;
            $seconds = $interval % 60;
            $minutes = floor(($interval % 3600) / 60);
            $hours = floor($interval / 3600);
            echo "<br>Total time: ".$hours.":".$minutes.":".$seconds;
        }
    }

    public function sync_employee_lecturer($mbo_portal_lecturer) // updatenya masih beblum bisa
    {
        print('<b>Syncronize employee lecturer');
        foreach ($mbo_portal_lecturer as $lecturer) {
            $s_personal_data_id = $this->uuid->v4();
            $s_employee_id = (is_null($lecturer->id_feeder)) ? $this->uuid->v4() : $lecturer->id_feeder;

            $mbo_personal_data = $this->Pm->retrieve_data('personal_data', array('id' => $lecturer->personal_data_id))[0];
            $s_religion_portal_id = (($mbo_personal_data) AND (!is_null($mbo_personal_data->religion_id))) ? $mbo_personal_data->religion_id : '7';
            $mbo_portal_religion_data = $this->Pm->retrieve_data('religion', array('id' => $s_religion_portal_id));
            $mbo_religion_staging = $this->Pdm->get_religion(array('religion_feeder_id' => (!is_null($mbo_portal_religion_data[0]->id_feeder)) ? $mbo_portal_religion_data[0]->id_feeder : '99'))[0];
            $mbo_portal_maiden = $this->Pm->retrieve_data('parents', array('personal_data_id' => $lecturer->personal_data_id))[0];

            $mbo_employment_data = $this->Pm->retrieve_data('employee', array('id' => $lecturer->employee_id));
            if (!$mbo_employment_data) {
                $mbo_employment_data = $this->Pm->retrieve_data('employee', array('personal_data_id' => $mbo_personal_data->id));
            }

            $mbo_staging_check_id_feeder_double = $this->Sm->retrieve_data('dt_employee', array('employee_id' => $s_employee_id));
            if ($mbo_staging_check_id_feeder_double) {
                $s_employee_id = $this->uuid->v4();
                // var_dump($mbo_staging_check_id_feeder_double);exit;
            }

            if (($mbo_employment_data) AND (!is_null($mbo_employment_data[0]->email))) {
                $s_employee_email = $mbo_employment_data[0]->email;
            }elseif (!is_null($lecturer->email)) {
                $s_employee_email = $lecturer->email;
            }else{
                $s_employee_email = $mbo_personal_data->personal_data_id.'@email.com';
            }

            $a_personal_data = array(
                'personal_data_id' => $s_personal_data_id,
                'country_of_birth' => (($mbo_personal_data) AND ($mbo_personal_data->birth_country_id != 0)) ? $mbo_personal_data->birth_country_id : NULL,
                'citizenship_id' => (($mbo_personal_data) AND ($mbo_personal_data->country_id != 0)) ? $mbo_personal_data->country_id : NULL,
                'religion_id' => $mbo_religion_staging->religion_id,
                'personal_data_title_prefix' => ($mbo_personal_data) ? $mbo_personal_data->title_front : null,
                'personal_data_title_suffix' => ($mbo_personal_data) ? $mbo_personal_data->title_back : null,
                'personal_data_name' => ($mbo_personal_data) ? strtoupper($mbo_personal_data->fullname) : '',
                'personal_data_email' => strtolower((($mbo_personal_data) AND (!is_null($mbo_personal_data->personal_email))) ? $this->clear_email($mbo_personal_data->personal_email) : $s_personal_data_id.'@domain.com'),
                'personal_data_phone' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->personal_phone))) ? $mbo_personal_data->personal_phone : NULL,
                'personal_data_cellular' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->personal_mobilephone))) ? $mbo_personal_data->personal_mobilephone : '0',
                'personal_data_id_card_number' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->idcard_number))) ? $mbo_personal_data->idcard_number : NULL,
                'personal_data_id_card_type' => 'national_id',
                'personal_data_place_of_birth' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->birth_place))) ? $mbo_personal_data->birth_place : NULL,
                'personal_data_date_of_birth' => (($mbo_personal_data) AND ($mbo_personal_data->birthday != '0000-00-00')) ? $mbo_personal_data->birthday : NULL,
                'personal_data_gender' => (($mbo_personal_data) AND ($mbo_personal_data->gender == 'MALE')) ? 'M' : 'F',
                'personal_data_nationality' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->nationality))) ? $mbo_personal_data->nationality : NULL,
                'personal_data_marital_status' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->marital_status))) ? $mbo_personal_data->marital_status : 'single',
                'personal_data_password' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->password))) ? $mbo_personal_data->password : NULL,
                'personal_data_mother_maiden_name' => (($mbo_portal_maiden) AND (!is_null($mbo_portal_maiden->mother_given_name))) ? $mbo_portal_maiden->mother_given_name : NULL,
                'portal_id' => $lecturer->personal_data_id
            );

            $a_lecturer_data = array(
                'employee_id' => $s_employee_id,
                'personal_data_id' => $s_personal_data_id,
                'employee_id_number' => (($mbo_employment_data) AND (!is_null($mbo_employment_data[0]->id_number)) AND($mbo_employment_data[0]->id_number != '')) ? $mbo_employment_data[0]->id_number : NULL,
                'employee_email' => $this->clear_email($s_employee_email),
                'employment_group' => 'ACADEMIC',
                'employment_status' => ($mbo_employment_data) ? $mbo_employment_data[0]->employment_status : 'NON-PERMANENT',
                'employee_join_date' => (($mbo_employment_data) AND (!is_null($mbo_employment_data[0]->join_date))) ? $mbo_employment_data[0]->join_date : $mbo_personal_data->date_created,
                'employee_is_lecturer' => 'YES',
                'employee_lecturer_number' => (is_null($lecturer->lecturer_number)) ? NULL : $lecturer->lecturer_number,
                'employee_lecturer_number_type' => $lecturer->number_type,
                'employee_lecturer_is_reported' => (is_null($lecturer->id_feeder)) ? 'FALSE' : 'TRUE',
                'status' => ($mbo_employment_data) ? $mbo_employment_data[0]->status : 'RESIGN',
                'employee_code_number' => $lecturer->id,
                'portal_id' => $lecturer->id
            );

            if ($mbo_staging_personal_data = $this->Sm->retrieve_data('dt_personal_data', array('portal_id' => $mbo_personal_data->id))) {
                $s_personal_id_old = $mbo_staging_personal_data[0]->personal_data_id;
                unset($a_personal_data['personal_data_id']);
                $save_personal_data = $this->Pdm->update_personal_data($a_personal_data, $mbo_staging_personal_data[0]->personal_data_id);
                if ($save_personal_data) {
                    if ($mbo_staging_employee_data = $this->Sm->retrieve_data('dt_employee', array('personal_data_id' => $mbo_staging_personal_data[0]->personal_data_id))) {
                        unset($a_lecturer_data['personal_data_id']);
                        unset($a_lecturer_data['employee_id']);
                        $this->Emm->update_empoyee_param($a_lecturer_data, array('personal_data_id' => $mbo_staging_personal_data[0]->personal_data_id));
                        print_r(array(
                            'personal_data_name' => ($mbo_personal_data) ? strtoupper($mbo_personal_data->fullname) : ''
                        ));
                    }else{
                        $this->Emm->save_employee($a_lecturer_data);
                        print(', ');
                    }
                }
            }else{
                $this->Pdm->create_new_personal_data($a_personal_data);
                $this->Emm->save_employee($a_lecturer_data);
                print('* ');
            }

            // print_r($a_personal_data);
        }
    }

    public function list_attendance()
    {
        $date = date('Y-m-d');
        // $date = '2019-12-10';
        $a_att = $this->Pm->retrieve_data('Logs', array(
            'log_date' => $date
        ));
        print('<h3>'.$date.'</h3>');

        if ($a_att) {
            // print('<pre>');
            foreach ($a_att as $att) {
                print($att->Checkin.' - '.$att->Checkout.': '.$att->Name);
                print('<br>');
            }
        }
    }

    public function sync_employee_non_lecturer($mbo_portal_employee)
    {
        print('<b>====================Syncronize employee non lecturer===========================');
        $i_count = 1;
        foreach ($mbo_portal_employee as $employee) {
            $mbo_portal_lecturer_data = $this->Pm->retrieve_data('lecturer', array('personal_data_id' => $employee->personal_data_id));
            if (!$mbo_portal_lecturer_data) {
                $s_personal_data_id = $this->uuid->v4();
                $s_employee_id = $this->uuid->v4();

                $mbo_personal_data = $this->Pm->retrieve_data('personal_data', array('id' => $employee->personal_data_id))[0];
                if ($mbo_personal_data) {
                    $s_religion_portal_id = (($mbo_personal_data) AND (!is_null($mbo_personal_data->religion_id))) ? $mbo_personal_data->religion_id : '7';
                    $mbo_portal_religion_data = $this->Pm->retrieve_data('religion', array('id' => $s_religion_portal_id));
                    $mbo_religion_staging = $this->Pdm->get_religion(array('religion_feeder_id' => (!is_null($mbo_portal_religion_data[0]->id_feeder)) ? $mbo_portal_religion_data[0]->id_feeder : '99'))[0];
                    $mbo_portal_maiden = $this->Pm->retrieve_data('parents', array('personal_data_id' => $employee->personal_data_id))[0];

                    $a_personal_data = array(
                        'personal_data_id' => $s_personal_data_id,
                        'country_of_birth' => (($mbo_personal_data) AND ($mbo_personal_data->birth_country_id != 0)) ? $mbo_personal_data->birth_country_id : NULL,
                        'citizenship_id' => (($mbo_personal_data) AND ($mbo_personal_data->country_id != 0)) ? $mbo_personal_data->country_id : NULL,
                        'religion_id' => $mbo_religion_staging->religion_id,
                        'personal_data_title_prefix' => ($mbo_personal_data) ? $mbo_personal_data->title_front : null,
                        'personal_data_title_suffix' => ($mbo_personal_data) ? $mbo_personal_data->title_back : null,
                        'personal_data_name' => ($mbo_personal_data) ? strtoupper($mbo_personal_data->fullname) : '',
                        'personal_data_email' => strtolower((($mbo_personal_data) AND (!is_null($mbo_personal_data->personal_email))) ? $this->clear_email($mbo_personal_data->personal_email) : $s_personal_data_id.'@domain.com'),
                        'personal_data_phone' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->personal_phone))) ? $mbo_personal_data->personal_phone : NULL,
                        'personal_data_cellular' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->personal_mobilephone))) ? $mbo_personal_data->personal_mobilephone : '0',
                        'personal_data_id_card_number' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->idcard_number))) ? $mbo_personal_data->idcard_number : NULL,
                        'personal_data_id_card_type' => 'national_id',
                        'personal_data_place_of_birth' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->birth_place))) ? $mbo_personal_data->birth_place : NULL,
                        'personal_data_date_of_birth' => (($mbo_personal_data) AND ($mbo_personal_data->birthday != '0000-00-00')) ? $mbo_personal_data->birthday : NULL,
                        'personal_data_gender' => (($mbo_personal_data) AND ($mbo_personal_data->gender == 'MALE')) ? 'M' : 'F',
                        'personal_data_nationality' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->nationality))) ? $mbo_personal_data->nationality : NULL,
                        'personal_data_marital_status' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->marital_status))) ? $mbo_personal_data->marital_status : 'single',
                        'personal_data_password' => (($mbo_personal_data) AND (!is_null($mbo_personal_data->password))) ? $mbo_personal_data->password : NULL,
                        'personal_data_mother_maiden_name' => (($mbo_portal_maiden) AND (!is_null($mbo_portal_maiden->mother_given_name))) ? $mbo_portal_maiden->mother_given_name : NULL,
                        'portal_id' => $employee->personal_data_id
                    );

                    $i_employee_code_number = $this->generate_number($i_count);
                    $a_employee_data = array(
                        'employee_id' => $s_employee_id,
                        'personal_data_id' => $s_personal_data_id,
                        'employee_id_number' => (($employee->id_number != '') AND (!is_null($employee->id_number))) ? $employee->id_number : NULL,
                        'employee_email' => (is_null($employee->email)) ? $s_employee_id.'@domain.com' : $this->clear_email($employee->email),
                        'employment_group' => 'ACADEMIC',
                        'employment_status' => $employee->employment_status,
                        'employee_join_date' => (!is_null($employee->join_date)) ? $employee->join_date : $mbo_personal_data->date_created,
                        'employee_is_lecturer' => 'NO',
                        'status' => $employee->status,
                        'employee_code_number' => $i_employee_code_number,
                        'portal_id' => $employee->id
                    );

                    $mbo_staging_personal_data = $this->Sm->retrieve_data('dt_personal_data', array('portal_id' => $mbo_personal_data->id));
                    if ($mbo_staging_personal_data) {
                        $s_personal_id_old = $mbo_staging_personal_data[0]->personal_data_id;
                        unset($a_personal_data['personal_data_id']);
                        $save_personal_data = $this->Pdm->update_personal_data($a_personal_data, $mbo_staging_personal_data[0]->personal_data_id);

                        if ($save_personal_data) {
                            if ($mbo_staging_employee_data = $this->Sm->retrieve_data('dt_employee', array('personal_data_id' => $mbo_staging_personal_data[0]->personal_data_id))) {
                                unset($a_employee_data['personal_data_id']);
                                $this->Emm->update_empoyee_param($a_employee_data, array('employee_id' => $mbo_staging_employee_data[0]->employee_id));
                                print_r(array(
                                    'personal_data_name' => ($mbo_personal_data) ? strtoupper($mbo_personal_data->fullname) : '',
                                    'code_number' => $i_employee_code_number
                                ));
                                $i_count = $this->generate_number($i_count);
                                // print('. ');
                            }else{
                                $this->Emm->save_employee($a_employee_data);
                                print(', ');
                            }
                        }
                    }else{
                        $this->Pdm->create_new_personal_data($a_personal_data);
                        $this->Emm->save_employee($a_employee_data);

                        print('* ');
                    }
                }
            }
        }
    }

    public function clear_email($s_staging_email)
    {
        $mba_staging_personal_data = $this->Sm->retrieve_data('dt_personal_data', array('personal_data_email' => $s_staging_email));
        if ($mba_staging_personal_data) {
            $a_email = explode('@', $s_staging_email);
            $new_email = $a_email[0].'+'.count($mba_staging_personal_data).'@'.$a_email[1];
            return $new_email;
        }else{
            return $s_staging_email;
        }
    }

    public function generate_number($i_start = 1)
    {
        $mba_staging_employee_lists = $this->Sm->retrieve_data('dt_employee');
        // var_dump($mba_staging_employee_lists);exit;
        $i_count_employee = ($mba_staging_employee_lists) ? count($mba_staging_employee_lists) : NULL;

        for ($i = $i_start; $i < $i_count_employee; $i++) { 
            $mba_lecturer_data = $this->Sm->retrieve_data('dt_employee', array('employee_code_number' => $i));
            if (!$mba_lecturer_data) {
                return $i;
                break;
            }
        }
        return NULL;
    }

    public function feeder_list_dosen()
    {
        $this->load->library('FeederAPI');
        print('<pre>');
        $result = $this->feederapi->post('GetListDosen');
        if ($result->error_code == 0) {
            $data = $result->data;
            if (count($data) > 0) {
                foreach ($data as $lecturer) {
                    print_r($lecturer);
                }
            }
        }
		print "</pre>";
    }
}
