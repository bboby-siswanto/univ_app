<?php
class Personal_data extends App_core
{		
	public function __construct()
	{
		parent::__construct('profile');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('student/Student_model', 'Stm');
	}

	public function get_personal_data_by_name()
	{
		if ($this->input->is_ajax_request()) {
			$s_term = $this->input->post('term');
			$mba_personal_data = $this->Pdm->get_personal_data_by_name($s_term, false, true);
			print json_encode(array('code' => 0, 'data' => $mba_personal_data));
		}
	}

	public function covid_certificate($s_student_id = null, $s_personal_data_id = null, $b_from_modal = false)
	{
		$this->load->model('File_manager_model', 'File_manager');
		if (is_null($s_personal_data_id)) {
			$s_personal_data_id = $this->session->userdata('user');
		}

        $mba_has_first_vaccine = $this->General->get_where('dt_personal_data_covid_vaccine', ['personal_data_id' => $s_personal_data_id, 'vaccine_number' => 1]);
        $mba_has_second_vaccine = $this->General->get_where('dt_personal_data_covid_vaccine', ['personal_data_id' => $s_personal_data_id, 'vaccine_number' => 2]);
        $mba_has_third_vaccine = $this->General->get_where('dt_personal_data_covid_vaccine', ['personal_data_id' => $s_personal_data_id, 'vaccine_number' => 3]);
        // print('<pre>');
        // var_dump($mba_has_filled_vaccine);exit;

		$this->a_page_data['first_vaccine'] = $mba_has_first_vaccine;
		$this->a_page_data['second_vaccine'] = $mba_has_second_vaccine;
		$this->a_page_data['third_vaccine'] = $mba_has_third_vaccine;
		$this->a_page_data['first_cerificate_vaccine'] = $this->File_manager->get_files($s_personal_data_id, 'cecd1a3f-ca66-11eb-96dc-52540039e1c3');
		$this->a_page_data['second_cerificate_vaccine'] = $this->File_manager->get_files($s_personal_data_id, 'd79d3bf1-ca7b-11eb-96dc-52540039e1c3');
		$this->a_page_data['third_cerificate_vaccine'] = $this->File_manager->get_files($s_personal_data_id, 'e1653945-ae4e-11ec-91ba-52540039e1c3');
		$this->a_page_data['vaccine_type'] = $this->General->get_enum_values('dt_personal_data_covid_vaccine', 'vaccine_type');
		$this->a_page_data['from_modal'] = $b_from_modal;
        
		if (!$b_from_modal) {
			$this->a_page_data['body'] = $this->load->view('validation_requirement/covid_vaccine/form_input', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}
		else {
			$this->load->view('validation_requirement/covid_vaccine/form_input', $this->a_page_data);
		}
	}

	public function job_history($s_student_id = false, $s_personal_data_id = null)
	{
		if(is_null($s_personal_data_id)){
			$s_personal_data_id = $this->session->userdata('user');
		}
		// $s_personal_data_id = $this->session->userdata('user');
		$mba_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		if ($mba_personal_data) {
			$this->a_page_data['personal_data'] = $mba_personal_data;
			$this->a_page_data['body'] = $this->load->view('alumni/job_history/job_default', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}
	}

	public function profile($mbs_student_id = false, $s_personal_data_id = null)
	{
		$mba_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		if(is_null($s_personal_data_id)){
			$s_personal_data_id = $this->session->userdata('user');
		}else if (!$mba_personal_data) {
			$s_personal_data_id = $this->session->userdata('user');
		}

		if ($mbs_student_id) {
			$student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $mbs_student_id));
		}else{
			$student_data = $this->Stm->get_student_filtered(array('ds.personal_data_id' => $s_personal_data_id));
		}

		if ($student_data) {
			// print('<pre>');var_dump($student_data);exit;
			$this->a_page_data['student_data'] = $student_data[0];
		}
		else if ($this->session->userdata('student_status') == 'graduated') {
			redirect('personal_data/alumni_profile');
		}
		
		$this->a_page_data['personal_data'] = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		$this->a_page_data['personal_data_id'] = $s_personal_data_id;
		
		$this->a_page_data['body'] = $this->load->view('default', $this->a_page_data, true);
		if (!$student_data) {
			$this->a_page_data['body'] = $this->load->view('form/form_personal_data_staff', $this->a_page_data, true);
		}
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function save_alumni_profile()
	{
		if ($this->input->is_ajax_request()) {
			$this->form_validation->set_rules('alumni_personal_email', 'Personal Email', 'trim|required');
			$this->form_validation->set_rules('alumni_place_of_birth', 'Place of Birth', 'trim|required');
			$this->form_validation->set_rules('alumni_date_of_birth', 'Date of Birth', 'trim|required');
			$this->form_validation->set_rules('alumni_gender', 'Gender', 'trim|required');
			$this->form_validation->set_rules('alumni_cellular', 'Cellular', 'trim|required|numeric');
			$this->form_validation->set_rules('marital_status', 'Marital Status', 'trim|required');
			// $this->form_validation->set_rules('personal_data_id', 'Personal Data', 'trim|required');
			$s_personal_data_id = $this->input->post('personal_data_id');

			if ($this->form_validation->run()) {
				$mba_document_type_data = $this->Pdm->get_requirement_document(array('rd.document_name' => 'Foto'))[0];
				// var_dump($mba_document_type_data);exit;
				$directory_file = APPPATH.'uploads/'.$s_personal_data_id.'/';
                if(!file_exists($directory_file)){
                    mkdir($directory_file, 0755, true);
				}
				
				$config['upload_path'] = $directory_file;
                $config['allowed_types'] = 'jpg|jpeg|png|bmp';
                $config['max_size'] = 4048;
                $config['file_ext_tolower'] = TRUE;
                $config['replace'] = TRUE;
                $config['encrypt_name'] = TRUE;

				$this->load->library('upload', $config);

				$s_error = '';
				// print('<pre>');
				// var_dump($_FILES);exit;
				if (count($_FILES) > 0) {
					if($this->upload->do_upload('file')) {
						$s_filename = $this->upload->data('file_name');
						$a_personal_data_document = array(
							'personal_data_id' => $s_personal_data_id,
							'document_id' => $mba_document_type_data->document_id,
							'document_requirement_link' => $s_filename,
							'document_mime' => $this->upload->data('file_type')
						);
						
						$s_file_name_deleted = null;
						$mbo_personal_document = $this->Pdm->get_personal_document($s_personal_data_id, $mba_document_type_data->document_id);
						if ($mbo_personal_document) {
							$s_document_link = $mbo_personal_document[0]->document_requirement_link;
							$s_file_name_deleted = $s_document_link;
							unlink($directory_file.$s_document_link);
							$b_save_personal_data_document = $this->Pdm->save_personal_document($a_personal_data_document, $s_personal_data_id, $mba_document_type_data->document_id);
						}else {
							$b_save_personal_data_document = $this->Pdm->save_personal_document($a_personal_data_document);
						}
					}else{
						$s_error = $this->upload->display_errors('<span>', '</span><br>');
					}
				}

				if ($s_error == '') {
					$a_alumni_data = array(
						'student_id' => trim($this->input->post('student_id')),
						'alumni_fullname' => trim($this->input->post('alumni_fullname')),
						'alumni_nickname' => ($this->input->post('alumni_nickname') == '') ? null : $this->input->post('alumni_nickname'),
						'alumni_place_of_birth' => set_value('alumni_place_of_birth'),
						'alumni_date_of_birth' => set_value('alumni_date_of_birth'),
						'alumni_personal_email' => set_value('alumni_personal_email'),
						'alumni_personal_cellular' => set_value('alumni_cellular'),
						'alumni_gender' => set_value('alumni_gender'),
						'alumni_marital_status' => set_value('marital_status')
					);
	
					if ($this->input->post('alumni_id') == '') {
						$a_alumni_data['alumni_id'] = $this->uuid->v4();
						$this->Pdm->save_alumni_data($a_alumni_data);
					}else{
						$a_alumni_data['alumni_id'] = $this->input->post('alumni_id');
						$this->Pdm->save_alumni_data($a_alumni_data, $this->input->post('alumni_id'));
					}

					$a_return = array('code' => 0, 'message' => 'Success');
				}else{
					$a_return = array('code' => 1, 'message' => $s_error);
				}
			}else {
				$a_return = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
			}

			print json_encode($a_return);exit;
		}
	}

	public function alumni_profile()
	{
		$s_personal_data_id = $this->session->userdata('user');
		// var_dump($this->session->userdata());exit;
		$this->a_page_data['personal_data_id'] = $s_personal_data_id;
		// $mba_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		$this->a_page_data['body'] = $this->load->view('alumni/default', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function form_personal_alumni($s_personal_data_id)
	{
		$mba_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		$mbo_alumni_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id);
		if (($mbo_alumni_student_data) AND ($mbo_alumni_student_data->student_status == 'graduated')) {
			$mbo_alumni_data = $this->Stm->get_alumni_data($mbo_alumni_student_data->student_id);
			if (!$mbo_alumni_data) {
				$a_show_input = array(
					'alumni_fullname' => $mba_personal_data->personal_data_name,
					'alumni_nickname' => '',
					'alumni_date_of_birth' => $mba_personal_data->personal_data_date_of_birth,
					'alumni_place_of_birth' => $mba_personal_data->personal_data_place_of_birth,
					'alumni_personal_email' => $mba_personal_data->personal_data_email,
					'alumni_personal_cellular' => $mba_personal_data->personal_data_cellular,
					'alumni_gender' => $mba_personal_data->personal_data_gender,
					'alumni_marital_status' => $mba_personal_data->personal_data_marital_status
				);
			}else {
				$a_show_input = (array)$mbo_alumni_data;
			}
			$this->load->model('File_manager_model', 'File_manager');
		
			$this->a_page_data['a_religions'] = $this->General->get_religions();
			$this->a_page_data['o_personal_data'] = $mba_personal_data;
			$this->a_page_data['o_alumni_data'] = $mbo_alumni_data;
			$this->a_page_data['a_show_input'] = $a_show_input;
			$this->a_page_data['o_option_gender'] = $this->General->get_enum_values('dt_student_alumni', 'alumni_gender') ;
			$this->a_page_data['o_option_marital_status'] = $this->General->get_enum_values('dt_student_alumni', 'alumni_marital_status') ;
			$this->a_page_data['o_student_data'] = $this->Stm->get_student_filtered(array('ds.personal_data_id' => $s_personal_data_id))[0];
			$this->a_page_data['a_avatar'] = $this->File_manager->get_files($s_personal_data_id, '0bde3152-5442-467a-b080-3bb0088f6bac');
			$this->load->view('alumni/personal_data/form_input_personal_alumni', $this->a_page_data);
		}
	}
	
	public function form_address($s_personal_data_id)
	{
		$this->a_page_data['o_address'] = $this->Pdm->get_address_data($s_personal_data_id, false, true)[0];
		$this->a_page_data['mbo_country'] = modules::run('address/get_country');
		$this->a_page_data['mbo_wilayah'] = modules::run('address/get_dikti_wilayah');
		$this->load->view('form/form_address', $this->a_page_data);
	}
	
	public function form_personal_data($s_personal_data_id = null)
	{
		$this->load->model('File_manager_model', 'File_manager');
		
		if ($s_personal_data_id != null) {
			$o_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
			if ($o_personal_data) {
				$mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id);
				$this->a_page_data['student_data'] = $mbo_student_data;
				$this->a_page_data['a_religions'] = $this->General->get_religions();
				$this->a_page_data['o_personal_data'] = $o_personal_data;
				$this->a_page_data['a_avatar'] = $this->File_manager->get_files($s_personal_data_id, '0bde3152-5442-467a-b080-3bb0088f6bac');
				$this->a_page_data['mba_country'] = modules::run('address/get_country');
				$this->load->view('form/form_personal_data', $this->a_page_data);
			}else{
				show_404();
			}
		}
	}
	
	public function view_send_email_form($b_text = false)
	{
		if($b_text){
			return $this->load->view('form/send_email_form', $this->a_page_data, true);
		}
		else{
			$this->load->view('form/send_email_form', $this->a_page_data);
		}
	}
	
	public function tester_sgs()
	{
		// $s_personal_data_id = 'c62115ce-9a44-410f-a575-39aacaff3050';
		// $s_personal_data_id = 'b006a2ec-b0ae-4140-9ba5-884464f6e8fb';
		$s_personal_data_id = '83ed238f-9266-4093-a43e-a955a3069ce6';
		$this->create_reference_code($s_personal_data_id);
	}
	
	public function create_reference_code($s_personal_data_id)
	{
		$mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id);
		if($mbo_student_data){
			$s_academic_year_id = $mbo_student_data->academic_year_id;
		}
		else{
			$o_active_batch = $this->General->get_batch(false, true);
			$s_academic_year_id = $o_active_batch[0]->academic_year_id;
		}
		$s_reference_code = 'REF'.substr($s_academic_year_id, 2, 2).'.';
		
		$mbs_latest_reference_code = $this->Pdm->get_latest_reference_code($s_reference_code);
		if($mbs_latest_reference_code){
			$s_latest_reference_code = $mbs_latest_reference_code->personal_data_reference_code;
			$a_latest_reference_code = explode('.', $s_latest_reference_code);
			$i_sequence = intval($a_latest_reference_code[1]);
			$i_sequence++;
			
			$s_reference_code .= str_pad($i_sequence, 3, '0', STR_PAD_LEFT);
		}
		else{
			$s_reference_code .= str_pad('1', 3, '0', STR_PAD_LEFT);
		}
		
		$this->Pdm->update_personal_data(array(
			'personal_data_reference_code' => $s_reference_code
		), $s_personal_data_id);
		return $s_reference_code;
	}

	public function save_address_data()
	{
		if($this->input->is_ajax_request()){
			$this->form_validation->set_rules('address_country_id', 'Country address', 'trim|required', array('required' => 'Country must be picked from autocomplete'));
			$this->form_validation->set_rules('rt', 'RT', 'trim|max_length[3]');
			$this->form_validation->set_rules('rw', 'RW', 'trim|max_length[3]');
			$this->form_validation->set_rules('address_province', 'Province', 'trim');
			$this->form_validation->set_rules('address_city', 'City', 'trim|required');
			$this->form_validation->set_rules('address_street', 'Street', 'trim');
			$this->form_validation->set_rules('zip_code', 'Zip Code', 'trim|numeric|exact_length[5]');
			$this->form_validation->set_rules('address_district_id', 'District/Kelurahan', 'trim|required');
			// $this->form_validation->set_rules('addr_phone_number', 'Phone Number', 'trim|numeric');
			// $this->form_validation->set_rules('addr_cellular', 'Cellular', 'trim|numeric');
			$this->form_validation->set_rules('address_sub_district', 'Sub-district/Kecamatan', 'trim|required');
			
			if($this->form_validation->run()){
				$this->db->trans_start();
				
				$s_personal_data_id = $this->input->post('personal_data_id');
				$a_address_data = array(
					'country_id' => set_value('address_country_id'),
					'address_province' => set_value('address_province'),
					'address_city' => set_value('address_city'),
					'address_street' => set_value('address_street'),
					'address_sub_district' => set_value('address_sub_district'),
					'address_rt' => set_value('rt'),
					'address_rw' => set_value('rw'),
					'dikti_wilayah_id' => set_value('address_district_id'),
					// 'dikti_wilayah_id' => NULL,
					'address_zipcode' => set_value('zip_code')
				);
				
				if($s_address_id = $this->input->post('address_id')){
					$this->Pdm->update_address($a_address_data, $s_address_id);
				}
				else{
					$this->Pdm->create_address($a_address_data, $s_personal_data_id);
				}
				
				if($this->db->trans_status() === false){
					$this->db->trans_rollback();
					$a_return = array('code' => 1, 'message' => 'Error');
				}
				else{
					$this->db->trans_commit();
					$a_return = array('code' => 0);
					
					$this->load->model('Api_core_model', 'Acm');

					if ($this->General->is_student_candidate($s_personal_data_id)) {
						$a_token_config = $this->config->item('token')['pmb'];
						$a_sites = $this->config->item('sites');
						$s_token = $a_token_config['access_token'];
						$s_secret_token = $a_token_config['secret_token'];
						$url = $a_sites['pmb'];

						$a_sync_data = array();
						$o_address_data = $this->Acm->get_prepare_data('dt_address', array('address_id' => $s_address_id));
						$o_personal_address = $this->Acm->get_prepare_data('dt_personal_address', array('address_id' => $s_address_id, 'personal_data_id' => $s_personal_data_id));

						array_push($a_sync_data, array(
							'table_name' => 'dt_address',
							'data' => $o_address_data,
							'clause' => array('address_id' => $s_address_id)
						));

						array_push($a_sync_data, array(
							'table_name' => 'dt_personal_address',
							'data' => $o_personal_address,
							'clause' => array('personal_data_id' => $s_personal_data_id, 'address_id' => $s_address_id)
						));

						$sync_data = array('sync_data' => $a_sync_data);
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

					if ($this->session->userdata('type') == 'student') {
						$mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->session->userdata('student_id')]);
						$mba_dikti_ok = modules::run('auth/validate_dikti_required', $mba_student_data[0]);
						if ($mba_dikti_ok) {
							$this->session->unset_userdata('dikti_required');
						}
					}
				}
			}
			else{
				$a_return = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
			}
			
			print json_encode($a_return);
			exit;		
		}
	}
	
	public function save_personal_data()
	{
		if($this->input->is_ajax_request()){
			$this->load->model('student/Student_model','Stm');
			$s_personal_data_id = $this->input->post('personal_data_id');
			
			if ($this->input->post('email') != '') {
				$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
				$this->form_validation->set_rules('phone_number', 'Phone', 'trim|numeric');
				$this->form_validation->set_rules('cellular_number', 'Cellular', 'trim|required|numeric');
			}

			$this->form_validation->set_rules('name', 'Name', 'trim|required');
			$this->form_validation->set_rules('identification_number', 'ID Card Number', 'trim|alpha_numeric');
			$this->form_validation->set_rules('identification_type', 'ID Card Type', 'trim');
			$this->form_validation->set_rules('placeofbirth', 'Place of birth', 'trim|required|alpha_numeric_spaces');
			$this->form_validation->set_rules('bdate', 'Birthday Date', 'trim|required');
			$this->form_validation->set_rules('bmonth', 'Birthday Month', 'trim|required');
			$this->form_validation->set_rules('byear', 'Birthday Year', 'trim|required');
			$this->form_validation->set_rules('citizenship_id', 'Citizenship', 'trim|required', array('required' => 'Citizenship must be picked from autocomplete'));
			$this->form_validation->set_rules('birth_country_id', 'Country of birth', 'trim|required', array('required' => 'Country of birth must be picked from autocomplete'));
			$this->form_validation->set_rules('gender', 'Gender', 'trim|required');
			$this->form_validation->set_rules('religion', 'Religion', 'trim|required');
			$this->form_validation->set_rules('nationality', 'Nationality', 'trim|required');
			$this->form_validation->set_rules('personal_data_mother_maiden_name', 'trim|required');
			
			// if($s_student_id = $this->input->post('student_id')){
				
			// }
			
			if($this->form_validation->run()){
				$this->db->trans_start();
				
				$a_personal_data = array(
					'personal_data_name' => set_value('name'),
					'personal_data_id_card_number' => set_value('identification_number'),
					'personal_data_id_card_type' => set_value('identification_type'),
					'personal_data_mother_maiden_name' => set_value('personal_data_mother_maiden_name'),
					'personal_data_place_of_birth' => set_value('placeofbirth'),
					'personal_data_date_of_birth' => date('Y-m-d', strtotime(implode('-', array(set_value('byear'), set_value('bmonth'), set_value('bdate'))))),
					'citizenship_id' => set_value('citizenship_id'),
					'country_of_birth' => set_value('birth_country_id'),
					'personal_data_gender' => set_value('gender'),
					'religion_id' => set_value('religion'),
					'personal_data_nationality' => set_value('nationality'),
					'has_completed_personal_data' => '0'
				);

				if ($this->input->post('email') != ''){
					$a_personal_data['personal_data_email'] = strtolower(set_value('email'));
					$a_personal_data['personal_data_phone'] = (set_value('phone_number') != "") ? set_value('phone_number') : NULL;
					$a_personal_data['personal_data_cellular'] = set_value('cellular_number');
				}
				
				$this->Pdm->update_personal_data($a_personal_data, $s_personal_data_id);
				$mba_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $s_personal_data_id]);
				if ($mba_student_data) {
					if ($mba_student_data[0]->student_status == 'register') {
						$mba_personal_data_new = $this->General->get_where('dt_personal_data', ['personal_data_id' => $s_personal_data_id]);
						if ($mba_personal_data_new) {
							$o_new_personal_data = $mba_personal_data_new[0];
							if ((!is_null($o_new_personal_data->has_completed_parents_data)) AND (!is_null($o_new_personal_data->has_completed_school_data))) {
								$update_student_data = $this->General->update_data('dt_student', ['student_status' => 'candidate'], ['student_id' => $mba_student_data[0]->student_id]);
							}
						}
					}
				}
				
				if(!empty($_FILES['profile_picture']['name'])){
					$this->load->model('File_manager_model', 'File_manager');
					$s_file_path = APPPATH.'uploads/'.$s_personal_data_id.'/';
					
					$config['allowed_types'] = 'gif|jpg|png|mp4|mov|jpeg';
		            $config['max_size'] = 102400;
		            $config['encrypt_name'] = true;
		            $config['file_ext_tolower'] = true;
					$config['upload_path'] = $s_file_path;
					
					if(!file_exists($s_file_path)){
						mkdir($s_file_path, 0755, true);
					}
					$this->load->library('upload', $config);
					
					if($this->upload->do_upload('profile_picture')){
						$a_document_data = array(
							'personal_data_id' => $s_personal_data_id,
							'document_id' => '0bde3152-5442-467a-b080-3bb0088f6bac',
							'document_requirement_link' => $this->upload->data('file_name'),
							'document_mime' => $this->upload->data('file_type')
						);
						$this->File_manager->upload_file($a_document_data);
					}
					else{
						$this->db->trans_rollback();
						$a_return = array('code' => 2, 'message' => $this->upload->display_errors('<span>', '</span><br>'));
						print json_encode($a_return);
						exit;
					}
				}
				
				if($this->db->trans_status() === false){
					$this->db->trans_rollback();
					$a_return = array('code' => 1, 'message' => 'Error');
				}
				else{
					$mbo_student_detail_data = $this->Stm->get_student_filtered(array(
						'ds.personal_data_id' => $s_personal_data_id
						// 'ds.student_status' => 'active'
					))[0];

					if ($this->session->userdata('type') == 'student') {
						if ($mbo_student_detail_data) {
							$this->session->set_userdata('dikti_required', modules::run('auth/validate_dikti_required', $mbo_student_detail_data));
						}
					}

					$this->db->trans_commit();
					$a_return = array('code' => 0);

					$this->load->model('Api_core_model', 'Acm');
					$a_token_config = $this->config->item('token')['pmb'];
					$a_sites = $this->config->item('sites');
					$s_token = $a_token_config['access_token'];
					$s_secret_token = $a_token_config['secret_token'];
					$url = $a_sites['pmb'];
					
					$a_sync_data = array();
					$o_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id, true);
					
					if ($this->General->is_student_candidate($s_personal_data_id)) {
						unset($o_personal_data->pmb_sync);
						$a_portal_data = array(
							'table_name' => 'dt_personal_data',
							'data' => $o_personal_data,
							'clause' => array('personal_data_id' => $s_personal_data_id)
						);
						array_push($a_sync_data, $a_portal_data);
						$sync_data = array('sync_data' => $a_sync_data);

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

					if ($this->session->userdata('type') == 'student') {
						$mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->session->userdata('student_id')]);
						$mba_dikti_ok = modules::run('auth/validate_dikti_required', $mba_student_data[0]);
						if ($mba_dikti_ok) {
							$this->session->unset_userdata('dikti_required');
						}
					}
				}
				
			}
			else{
				$a_return = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
			}
			
			print json_encode($a_return);
			exit;
		}
	}
}