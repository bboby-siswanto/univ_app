<?php
class Academic extends App_core
{
    function __construct()
    {
        parent::__construct('profile');
        $this->load->model('personal_data/Personal_data_model','Pdm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('student/Student_model', 'Stm');
        if (($this->session->userdata('dikti_required') !== NULL) AND ($this->session->userdata('dikti_required') == false)) {
			redirect('personal_data/profile');
		}
    }

    public function delete_academic_history()
    {
        if ($this->input->post()) {
            $a_clause = array(
                'academic_history_id' => $this->input->post('academic_history_id')
            );

            $mbo_academic_history_data = $this->Pdm->get_academic_filtered($a_clause);
            if (($mbo_academic_history_data) AND ($mbo_academic_history_data[0]->academic_history_main == 'no')) {
                if ($this->Pdm->delete_academic_history($a_clause)) {
                    $return  = array('code' => 0, 'message' => "Data has been deleted");
                }else{
                    $return  = array('code' => 1, 'message' => "Cann't delete academic history");
                }
            }else{
                $return  = array('code' => 1, 'message' => "Cann't delete academic history data");
            }
        }else{
            $return  = array('code' => 1, 'message' => "Nothing deleted");
        }

        print json_encode($return);
    }

    public function get_academic_history_data()
    {
        if ($this->input->is_ajax_request()) {
            $a_filter = array(
                'personal_data_id' => $this->input->post('personal_data_id'),
                'academic_history_id' => $this->input->post('academic_history_id')
            );

            $mbo_academic_history_data = $this->Pdm->get_academic_filtered($a_filter);

            if ($mbo_academic_history_data) {
                print json_encode(array('code' => 0, 'data' => $mbo_academic_history_data[0]));
            }else{
                print json_encode(array('code' => 1, 'data' => false));
            }
        }
    }

    public function academic_history($s_student_id = false, $s_personal_data_id = null)
    {
        if ($s_personal_data_id == null) {
            $s_personal_data_id = $this->session->userdata('user');
        }

        $o_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
        if ($o_personal_data) {
            $s_btn_html = Modules::run('layout/generate_buttons', 'profile', 'academic_history');
            
            if ($student_data = $this->Stm->get_student_filtered(array('ds.personal_data_id' => $s_personal_data_id, 'ds.student_status' => 'active'))) {
				$this->a_page_data['student_data'] = $student_data[0];
			}
            
            $this->a_page_data['btn_html'] = $s_btn_html;
            $this->a_page_data['personal_data_id'] = $s_personal_data_id;
            $this->a_page_data['o_personal_data'] = $o_personal_data;
            $this->a_page_data['mba_student_data'] = $this->Stm->get_student_filtered(array('ds.personal_data_id' => $s_personal_data_id));
            $this->a_page_data['a_study_program'] = $this->Spm->get_study_program();
            $this->a_page_data['body'] = $this->load->view('academic_default', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function form_filter_academic_history()
    {
        $this->load->view('personal_data/form/academic_filter');
    }

    public function form_edit_academic_history()
    {
        if ($this->input->is_ajax_request()) {
            $a_filter = array(
                'personal_data_id' => $this->input->post('personal_data_id'),
                'academic_history_id' => $this->input->post('academic_history_id')
            );

            $this->a_page_data['personal_data_id'] = $this->input->post('personal_data_id');
            $this->a_page_data['o_study_program_list'] = $this->Spm->get_study_program(false, true);
            $this->a_page_data['s_page_child_title'] = 'Edit academic history';
            $this->a_page_data['o_academic_history_data'] = $this->Pdm->get_academic_filtered($a_filter);
            $s_html = $this->load->view('personal_data/form/form_create_academic_history', $this->a_page_data, true);
            
            print json_encode(array('data' => $s_html));
            exit;
        }
    }

    public function form_create_academic_history($s_personal_data_id = false)
    {
        if ($s_personal_data_id) {
            $this->a_page_data['personal_data_id'] = $s_personal_data_id;
        }else{
            $this->a_page_data['personal_data_id'] = $this->session->userdata('user');
        }

        $this->a_page_data['o_study_program_list'] = $this->Spm->get_study_program(false, true);
        $this->a_page_data['s_page_child_title'] = 'Add new academic history';
        $s_html = $this->load->view('personal_data/form/form_create_academic_history', $this->a_page_data, true);
        if ($this->input->is_ajax_request()) {
            print json_encode($s_html);
            exit;
        }else{
            print($s_html);
        }
    }

    public function view_list_academic_history($s_personal_data_id = null)
    {
        if ($s_personal_data_id != null) {
            $this->a_page_data['o_personal_data'] = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
            $this->a_page_data['o_academic_history'] = $this->Pdm->get_academic_history($s_personal_data_id);
            $this->load->view('table/academic_history', $this->a_page_data);
        }
    }

    public function filter_academic_history()
    {
        $return  = array();
        if ($this->input->post()) {
            $a_filter_data = $this->input->post();
			foreach($a_filter_data as $key => $value){
				if($a_filter_data[$key] == 'all'){
					unset($a_filter_data[$key]);
				}
            }
            
            $return  = $this->Pdm->get_academic_filtered($a_filter_data);
        }

        print json_encode(array('code' => 0, 'data' => $return));
        exit;
    }

    public function save_academic_history()
    {
        if ($this->input->post()) {
            $this->load->model('institution/Institution_model', 'Insm');
            // $this->load->model('Api_code_model', 'Acm');
            $i_school_found_status = $this->input->post('school_found_status');
            $s_institution_id = $this->input->post('institution_id');
            $s_personal_data_id = $this->input->post('personal_data_id');
            $a_api_data = array();

            if ($i_school_found_status == 0) {
                $this->form_validation->set_rules('institution_name','School name', 'trim|required');
                $this->form_validation->set_rules('institution_address','School address', 'trim');
                $this->form_validation->set_rules('institution_phone_number','School phone number', 'trim|numeric');
                $this->form_validation->set_rules('institution_email','School email', 'trim|valid_email');
                $this->form_validation->set_rules('institution_country','School country', 'trim|required');
                $this->form_validation->set_rules('institution_province','School province', 'trim');
                $this->form_validation->set_rules('institution_city','School city', 'trim|required');
                $this->form_validation->set_rules('institution_zipcode','School zipcode', 'trim|numeric|max_length[5]');
            }

            $this->form_validation->set_rules('school_graduation_year', 'Graduations year', 'trim|required|numeric');
            $this->form_validation->set_rules('major', 'Discipline/Major', 'trim|required');

            $s_err_message = '';
            $b_this_main = false;
            
            if ($this->form_validation->run()) {
                $this->db->trans_start();

                $b_save_institution = false;
                if ($i_school_found_status == 0) {
                    $s_school_name = strtoupper(set_value('institution_name'));
                    $mbo_school_exists = $this->Insm->institution_suggestions($s_school_name);
                    
                    if (!$mbo_school_exists) {
                        $a_institution_data = array(
                            'institution_name' => strtoupper(set_value('institution_name')),
                            'institution_phone_number' =>  set_value('institution_phone_number'),
                            'institution_email' => set_value('institution_email'),
                            'institution_type' => 'highschool',
                            'date_added' => date('Y-m-d H:i:s')
                        );

                        $a_address_data = array(
                            'country_id' => set_value('institution_country_id'),
                            'address_province' => strtoupper(set_value('institution_province')),
                            'address_city' => strtoupper(set_value('institution_city')),
                            'address_street' => strtoupper(set_value('institution_address')),
                            'address_zipcode' => set_value('institution_zipcode'),
                            'date_added' => date('Y-m-d H:i:s')
                        );

                        if ($s_address_id = $this->Insm->insert_address($a_address_data)) {
                            $a_institution_data['address_id'] = $s_address_id;
                            if ($s_institution_id = $this->Insm->insert_institution($a_institution_data)) {
                                $b_save_institution = true;
                            }else{
                                $b_save_institution = false;
                                $s_err_message = 'processing institution data';
                            }
                        }else{
                            $b_save_institution = false;
                            $s_err_message = 'processing address data';
                        }
                    }else{
                        $s_institution_id = $mbo_school_exists[0]->institution_id;
                        $s_address_id = $mbo_school_exists[0]->address_id;
                        $b_save_institution = true;
                    }
                }else{
                    $mbo_institution_data = $this->Insm->get_institution_by_id($s_institution_id);
                    $s_address_id = $mbo_institution_data->address_id;
                    $b_save_institution = true;
                }

                if ($b_save_institution) {
                    $a_academic_history_data = array(
                        'institution_id' => $s_institution_id,
                        'personal_data_id' => $s_personal_data_id,
                        'academic_history_major' => set_value('major'),
                        'academic_history_graduation_year' => set_value('school_graduation_year')
                    );

                    if ($this->input->post('academic_history_id') != '') {
                        $mbs_save_academic_history = $this->Insm->insert_academic_history($a_academic_history_data, $this->input->post('academic_history_id'));
                        if ($mbs_save_academic_history) {
                            $s_academic_history_id = $mbs_save_academic_history;
                        }else{
                            $s_err_message = 'processing academic history';
                        }
                    }else{
                        if ($this->Pdm->get_academic_history($s_personal_data_id)) {
                            $a_academic_history_data['academic_history_main'] = 'no';
                            $b_this_main = false;
                        }else{
                            $a_academic_history_data['academic_history_main'] = 'yes';
                            $b_this_main = true;
                        }

                        if ($this->Pdm->get_academic_filtered($a_academic_history_data)) {
                            $s_err_message = 'nothing saved';
                        }else{
                            $a_academic_history_data['date_added'] = date('Y-m-d H:i:s');
                            $mbs_save_academic_history = $this->Insm->insert_academic_history($a_academic_history_data);
                            if ($mbs_save_academic_history) {
                                $s_academic_history_id = $mbs_save_academic_history;
                            }else{
                                $s_err_message = 'processing academic history';
                            }
                        }
                    }
                }else{
                    $s_err_message = 'processing data';
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $rtn = array('code' => 1, 'message' => $s_err_message);
                }else{
                    $update_complete_data = $this->General->update_data('dt_personal_data', ['has_completed_school_data' => '0'], ['personal_data_id' => $s_personal_data_id]);
					$mba_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $s_personal_data_id]);
					if ($mba_student_data) {
						if ($mba_student_data[0]->student_status == 'register') {
							$mba_personal_data_new = $this->General->get_where('dt_personal_data', ['personal_data_id' => $s_personal_data_id]);
							if ($mba_personal_data_new) {
								$o_new_personal_data = $mba_personal_data_new[0];
								if ((!is_null($o_new_personal_data->has_completed_personal_data)) AND (!is_null($o_new_personal_data->has_completed_parents_data))) {
									$update_student_data = $this->General->update_data('dt_student', ['student_status' => 'candidate'], ['student_id' => $mba_student_data[0]->student_id]);
								}
							}
						}
					}

                    $this->db->trans_commit();
                    $rtn = array('code' => 0);

                    if ($this->General->is_student_candidate($s_personal_data_id)) {
                        if ($b_this_main) {
                            $this->load->model('Api_core_model','Acm');
                            $a_token_config = $this->config->item('token')['pmb'];
                            $a_sites = $this->config->item('sites');
                            $s_token = $a_token_config['access_token'];
                            $s_secret_token = $a_token_config['secret_token'];
                            $url = $a_sites['pmb'];
                            
                            $a_sync_data = array();
                            array_push($a_sync_data, $this->libapi->prepare_data('dt_address', array('address_id' => $s_address_id)));
                            array_push($a_sync_data, $this->libapi->prepare_data('ref_institution', array('institution_id' => $s_institution_id)));
                            array_push($a_sync_data, $this->libapi->prepare_data('dt_academic_history', array('academic_history_id' => $s_academic_history_id)));
                            
                            $sync_data = array(
                                'sync_data' => $a_sync_data
                            );
                            
                            $hashed_string = $this->libapi->hash_data($sync_data, $s_token, $s_secret_token);
                            $post_data = json_encode(array(
                                'access_token' => 'PORTALIULIACID',
                                'data' => $hashed_string
                            ));
                            
                            $a_result = $this->libapi->post_data($url.'api/portal/sync_all', $post_data);
                            if ($a_result != null) {
                                $this->Acm->update_result_sync(json_decode(json_encode($a_result->a_return_data), true), intval($a_result->code));
                            }
                        }
                    }
                }
            }else{
                $rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            echo json_encode($rtn);
            exit;
        }
    }
}
