<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;	

class Admission extends App_core
{	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('Admission_model', 'Am');
		$this->load->model('finance/Invoice_model', 'Im');
		$this->load->model('finance/Bni_model', 'Bm');
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('academic/Semester_model', 'Smm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');
		$this->load->model('File_manager_model', 'Fmam');
		$this->load->model('academic/Academic_year_model', 'Aym');
		$this->load->model('study_program/Study_program_model', 'Spm');
	}
	
	public function dashboard($b_is_partial = false)
	{
		if($b_is_partial){
			$this->load->view('layout', $this->a_page_data);
		}
		else{
			// $this->load->view('')
		}
	}

	// public function show_layout_email()
	// {
	// 	// $s_template_email = $this->load->view('messaging/admission/layout_cron_register', $this->a_page_data, true);
	// 	$s_template_email = $this->load->view('messaging/admission/layout_cron_register', $this->a_page_data, true);
	// 	// $config['mailtype'] = 'html';
	// 	// $this->email->initialize($config);

	// 	// $this->email->from('employee@company.ac.id', 'IULI Admission');
	// 	// $this->email->to('bboby.siswanto@gmail.com');
	// 	// $this->email->subject("[IULI ADMISSION] IULI Account Registration");
	// 	// $this->email->message($s_template_email);
	// 	// if ($this->email->send()) {
	// 		print($s_template_email);exit;
	// 	// }
	// 	// else {
	// 	// 	print('ga ke kirim');
	// 	// }
	// }

	public function save_siblings()
	{
		if ($this->input->is_ajax_request()) {
			$s_personal_data_id = $this->input->post('personal_data_id');
			$s_sibling_type = $this->input->post('sibling_type');
			$s_personal_data_id_sibling_with_employee = $this->input->post('employee_personal_siblings_id');
			$s_personal_data_id_sibling_with_student = $this->input->post('student_personal_siblings_id');
			$mba_have_discount = $this->General->get_where('dt_personal_data_scholarship', ['personal_data_id' => $s_personal_data_id]);

			if (strtolower($s_sibling_type) == 'employee') {
				if (empty($s_personal_data_id_sibling_with_employee)) {
					$a_return = ['code' => 1, 'message' => 'Please select relation/sibling user!'];
				}
				else if ($mba_have_discount) {
					$this->Sm->update_scholarship_data([
						'personal_data_id_sibling_with' => $s_personal_data_id_sibling_with_employee,
						'sibling_type' => strtolower($s_sibling_type)
					], $s_personal_data_id);
					$a_return = ['code' => 0, 'message' => 'Success!'];
				}
				else {
					$insert = $this->Sm->insert_student_scholarship([
						'personal_data_id' => $s_personal_data_id,
						'scholarship_id' => 'b3148593-56a9-11ea-8aee-5254005d90f6',
						'personal_data_id_sibling_with' => $s_personal_data_id_sibling_with_employee,
						'sibling_type' => strtolower($s_sibling_type)
					], $s_personal_data_id);

					if ($insert) {
						$a_return = ['code' => 0, 'message' => 'Success!'];
					}
					else {
						$a_return = array('code' => 1, 'message' => 'Error processing data');
					}
				}
			}
			else if (strtolower($s_sibling_type) == 'student') {
				if (empty($s_personal_data_id_sibling_with_student)) {
					$a_return = ['code' => 1, 'message' => 'Please select relation/sibling user!'];
				}
				else if ($mba_have_discount) {
					$this->Sm->update_scholarship_data([
						'personal_data_id_sibling_with' => $s_personal_data_id_sibling_with_student,
						'sibling_type' => strtolower($s_sibling_type)
					], $s_personal_data_id);
					$a_return = ['code' => 0, 'message' => 'Success!'];
				}
				else {
					$insert = $this->Sm->insert_student_scholarship([
						'personal_data_id' => $s_personal_data_id,
						'scholarship_id' => 'b3148593-56a9-11ea-8aee-5254005d90f6',
						'personal_data_id_sibling_with' => $s_personal_data_id_sibling_with_employee,
						'sibling_type' => strtolower($s_sibling_type)
					], $s_personal_data_id);

					if ($insert) {
						$a_return = ['code' => 0, 'message' => 'Success!'];
					}
					else {
						$a_return = array('code' => 1, 'message' => 'Error processing data');
					}
				}
			}

			print json_encode($a_return);
		}

	}

	public function save_candidate_settings()
	{
		if ($this->input->is_ajax_request()) {
			$s_student_id = $this->input->post('student_id');
			$s_student_status = $this->input->post('student_status');
			$s_academic_year_id = $this->input->post('academic_year_id');
			$s_cancel_status_note = ($s_student_status == 'cancel') ? $this->input->post('student_candidate_cancel_note') : NULL;
			$s_student_type = ($this->input->post('student_type') == '') ? NULL : $this->input->post('student_type');
			// $s_accepted_semester = $this->input->post('accepted_semester');
			// $s_semester_type_accepted = $this->input->post('semester_type_id');
			$s_program_id = $this->input->post('program_id');
			$s_study_program_id = ($s_program_id == '') ? NULL : $this->input->post('study_program_id');

			$a_data = [
				'student_status' => $s_student_status,
				'student_candidate_cancel_note' => $s_cancel_status_note,
				'academic_year_id' => $s_academic_year_id,
				'finance_year_id' => $s_academic_year_id,
				'student_type' => $s_student_type,
				'program_id' => $s_program_id,
				'student_class_type' => 'regular',
				'study_program_id' => $s_study_program_id
			];

			if ($s_program_id == '2') {
				$a_data['student_class_type'] = 'karyawan';
			}

			$mbo_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id]);
			if ($mbo_student_data) {
				$mbo_student_data = $mbo_student_data[0];
				$a_allowed_status = ['register', 'candidate', 'pending', 'cancel'];

				if (in_array($s_student_status, $a_allowed_status)) {
					if ($mbo_student_data->academic_year_id != $s_academic_year_id) {
						$mba_student_invoice = $this->General->get_where('dt_invoice', [
							'personal_data_id' => $mbo_student_data->personal_data_id
						]);
	
						if ($mba_student_invoice) {
							if ($s_student_type != 'transfer') {
								foreach ($mba_student_invoice as $o_invoice) {
									$this->Im->update_invoice([
										'academic_year_id' => $s_academic_year_id,
										'semester_type_id' => 1
									], [
										'invoice_id' => $o_invoice->invoice_id
									]);
								}
							}
						}
					}
					// if ($s_student_type == 'transfer') {
					// 	if (($s_accepted_semester == '') OR ($s_semester_type_accepted == '')) {
					// 		print json_encode(['code' => 1, 'message' => 'Please select accepted semester!']);
					// 		exit;
					// 	}
					// }

					$this->Sm->update_student_data($a_data, $s_student_id);
					$a_return = ['code' => 0, 'message' => 'Success!'];

				}else{
					$a_return = ['code' => 1, 'message' => 'Status not allowed in admission module!'];
				}
				
			}else{
				$a_return = ['code' => 1, 'message' => 'Candidate not found!'];
			}

			print json_encode($a_return);
		}
	}

	public function candidate_setting($s_student_id = null)
	{
		if (!is_null($s_student_id)) {
			$a_allowed_status = ['register', 'candidate', 'pending', 'cancel'];
			$mbo_student_data = $this->Sm->get_student_filtered(array('ds.student_id' => $s_student_id));
			if ($mbo_student_data) {
				$mbo_student_data = $mbo_student_data[0];
				// if (in_array($mbo_student_data->student_status, $a_allowed_status)) {
					$this->a_page_data['setting_allowed_status'] = $a_allowed_status;
					$this->a_page_data['student_data'] = $mbo_student_data;
					$this->a_page_data['personal_data_id'] = $mbo_student_data->personal_data_id;
					$this->a_page_data['body'] = $this->load->view('admission/candidate_settings', $this->a_page_data, true);
					$this->load->view('layout', $this->a_page_data);
				// }
			}
			else {
				show_404();
			}
		}
	}
	
	public function form_candidate_setting($s_student_id = false)
	{
		$this->a_page_data['student_data'] = $this->Sm->get_student_by_id($s_student_id);
		$this->a_page_data['o_student_semester'] = $this->Smm->get_student_start_semester($s_student_id);
		$this->a_page_data['student_status'] = $this->General->get_enum_values('dt_student', 'student_status');
		$this->a_page_data['semester_number'] = $this->Smm->get_semester_number_regular_list();
		$this->a_page_data['student_type'] = $this->General->get_enum_values('dt_student', 'student_type');
		$this->a_page_data['status_admission'] = ['register', 'candidate', 'pending', 'cancel'];
		$this->a_page_data['program_lists'] = $this->Spm->get_program(false, [1,2,3]);
		$this->a_page_data['academic_year'] = $this->General->get_where('dt_academic_year');
		// $this->a_page_data['program_lists'] = $this->Spm->get_program_lists_select(['program_id != ' => 4]);

		$student_data = $this->a_page_data['student_data'];
		$this->a_page_data['personal_data_id'] = $student_data->personal_data_id;
		if (!is_null($student_data->program_id)) {
			// $this->a_page_data['study_program_lists'] = $this->Spm->get_study_program_lists(array('program_id' => $student_data->program_id));
			$this->a_page_data['study_program_lists'] = $this->Spm->get_study_program_lists();
		}
		$this->load->view('form/candidate_form_setting', $this->a_page_data);
	}

	public function candidate_siblings($s_personal_data_id = false)
	{
		if ($s_personal_data_id) {
			$mba_sibling_data = false;
			$student_scholarship = $this->General->get_where('dt_personal_data_scholarship', ['personal_data_id' => $s_personal_data_id]);
			if ($student_scholarship) {
				$mba_sibling_data = $this->Pdm->get_personal_data_sibling($s_personal_data_id, false, $student_scholarship[0]->sibling_type);
			}
			// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
			// 	print('<pre>');
			// 	var_dump($s_personal_data_id);
			// }
			$this->a_page_data['sibling_data'] = $mba_sibling_data;
			$this->a_page_data['student_scholarship'] = ($student_scholarship) ? $student_scholarship[0] : false;
			$this->a_page_data['sibling_type'] = $this->General->get_enum_values('dt_personal_data_scholarship', 'sibling_type');
			$this->load->view('candidate_siblings_with', $this->a_page_data);
		}
	}

	public function candidate_discount_tuition_fee($s_personal_data_id = false)
	{
		if ($s_personal_data_id) {
			$s_scholarship_id = 'd6f380a4-87fc-11ed-abdf-52540039e1c3';
			$student_has_discount_semester = $this->General->get_where('dt_personal_data_scholarship', ['personal_data_id' => $s_personal_data_id, 'scholarship_id' => $s_scholarship_id]);
			// if ($student_has_discount_semester) {
			// 	$mba_sibling_data = $this->Pdm->get_personal_data_sibling($s_personal_data_id, false, $student_scholarship[0]->sibling_type);
			// }
			// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
			// 	print('<pre>');
			// 	var_dump($s_personal_data_id);
			// }
			$mba_discount_list = $this->General->get_where('dt_fee', ['scholarship_id' => $s_scholarship_id]);
			$this->a_page_data['student_scholarship'] = $student_has_discount_semester[0];
			$this->a_page_data['disc_scholarship_id'] = $s_scholarship_id;
			$this->a_page_data['discount_data'] = $student_has_discount_semester;
			$this->a_page_data['discount_list'] = $mba_discount_list;
			$this->a_page_data['status_scholarship'] = ['active', 'inactive'];
			// $this->a_page_data['sibling_type'] = $this->General->get_enum_values('dt_personal_data_scholarship', 'sibling_type');
			$this->load->view('candidate_student_discount_fee', $this->a_page_data);
		}
	}

	public function candidate_scholarship($s_student_id = false)
	{
		if ($s_student_id) {
			$mbo_student_data = $this->Sm->get_student_by_id($s_student_id);
			$this->a_page_data['student_data'] = $mbo_student_data;
			if (is_null($mbo_student_data->scholarship_id)) {
				$mbo_registration_scholarship = $this->General->get_where('dt_registration_scholarship', ['personal_data_id' => $mbo_student_data->personal_data_id])[0];
				if ($mbo_registration_scholarship) {
					$mbo_student_data->scholarship_id = $mbo_registration_scholarship->scholarship_id;
					$mbo_student_data->scholarship_status = 'inactive';
				}
			}
			$this->a_page_data['scholarship_id'] = $this->Am->get_scholarship_student([
				'rs.scholarship_type' => 'scholarship',
				'rs.scholarship_fee_type != ' => NULL,
			]);
			$this->a_page_data['scholarship_status'] = $this->General->get_enum_values('ref_scholarship','scholarship_status');
			$this->a_page_data['status_scholarship'] = ['active', 'inactive'];

			$student_data = $this->a_page_data['student_data'];
			$this->a_page_data['personal_data_id'] = $mbo_student_data->personal_data_id;
			$this->load->view('candidate_scholarship', $this->a_page_data);
		}
	}

	public function save_candidate_discount()
	{
		if ($this->input->is_ajax_request()) {
			$a_personal_data_scholarship = [
				'personal_data_id' => $this->input->post('disc_personal_data_id'),
				'scholarship_id' => $this->input->post('disc_scholarship_id'),
				'scholarship_fee_id' => $this->input->post('discount_id'),
				'scholarship_status' => $this->input->post('disc_status'),
				'date_added' => date('Y-m-d H:i:s')
			];

			$mba_student_have_discount = $this->General->get_where('dt_personal_data_scholarship', ['personal_data_id' => $this->input->post('disc_personal_data_id'), 'scholarship_id' => $this->input->post('disc_scholarship_id')]);
			if ($mba_student_have_discount) {
				$save = $this->Sm->update_student_scholarship($a_personal_data_scholarship, [
					'personal_data_id' => $this->input->post('disc_personal_data_id'),
					'scholarship_id' => $this->input->post('disc_scholarship_id')
				]);
			}
			else {
				$save = $this->Sm->insert_student_scholarship($a_personal_data_scholarship);
			}
			
			$a_return = ['code' => 0, 'message' => 'Success!'];
			print json_encode($a_return);
		}
	}

	public function save_candidate_scholarship()
	{
		if ($this->input->is_ajax_request()) {
			$s_personal_data_id = $this->input->post('personal_data_id');
			$s_scholarship_id = $this->input->post('scholarship_id');
			$s_semester_fee = $this->input->post('semester_fee');
			$s_scholarship_status = $this->input->post('scholarship_status');
			$s_scholarship_status = ($s_scholarship_status == '') ? 'inactive' : $s_scholarship_status;

			$a_data = [
				'scholarship_id' => $s_scholarship_id,
				'scholarship_status' => $s_scholarship_status,
				'semester_fee' => (($s_semester_fee !== null) AND ($s_semester_fee != '')) ? $s_semester_fee : NULL
			];
			
			$mbo_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $s_personal_data_id]);
			if ($mbo_student_data) {
				$a_allow_status = ['active', 'inactive'];
				$o_active_intake = $this->Am->get_active_intake_year();

				if (in_array($s_scholarship_status, $a_allow_status)) {
					$mba_student_scholarship = $this->General->get_where('dt_personal_data_scholarship', ['personal_data_id' => $s_personal_data_id]);
					$mba_has_registration = $this->Am->get_registration_scholarship(['personal_data_id' => $s_personal_data_id, 'academic_year_id' => $o_active_intake->academic_year_id]);
					if ($mba_student_scholarship) {
						$save = $this->Sm->update_scholarship_data($a_data, $s_personal_data_id);
					}else{
						$a_data['personal_data_id'] = $s_personal_data_id;
						$save = $this->Sm->insert_student_scholarship($a_data);
					}

					if ($mba_has_registration) {
						$registration = $this->Am->submit_registration_sholarship([
							'personal_data_id' => $s_personal_data_id,
							'scholarship_id' => $s_scholarship_id
						], ['registration_id' => $mba_has_registration[0]->registration_id]);
					}else{
						$registration = $this->Am->submit_registration_sholarship([
							'registration_id' => $this->uuid->v4(),
							'personal_data_id' => $s_personal_data_id,
							'scholarship_id' => $s_scholarship_id,
							'academic_year_id' => $o_active_intake->academic_year_id,
							'date_added' => date('Y-m-d H:i:s')
						]);
					}

					$a_return = ['code' => 0, 'message' => 'Success!'];

				}else{
					$a_return = ['code' => 1, 'message' => 'Status not found!'];
				}
				
			}else{
				$a_return = ['code' => 1, 'message' => 'Candidate not found!'];
			}

			print json_encode($a_return);
		}
	}
	
	public function form_create_new_student()
	{
		$this->a_page_data['study_program_list'] = $this->Spm->get_study_program();
    	$this->load->view('form/new_student_form', $this->a_page_data);
	}
	
	public function get_candidate_student()
	{
		if($this->input->is_ajax_request()){
			$s_academic_year_id = $this->input->post('academic_year_id');
			if($s_academic_year_id == 'all'){
				$mba_student_list = $this->db->get_candidate_student();
			}
			else{
				$mba_student_list = $this->db->get_candidate_student($s_academic_year_id);
			}
			
			print json_encode($mba_student_list);
			exit;
		}
	}
	
	public function send_credentials($b_send_email = true, $s_student_id = null)
	{
		if(is_null($s_student_id)){
			$s_student_id = $this->input->post('student_id');
		}
		
		$mbo_student_data = $this->Sm->get_student_by_id($s_student_id);
		if($mbo_student_data){
    		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($mbo_student_data->personal_data_id, true);
            $mbo_family_data = $this->Fm->get_family_by_personal_data_id($mbo_personal_data->personal_data_id);
            
            // Construct data to be sent to API
    		$a_post_data = array(
    			'personal_data' => $mbo_personal_data,
    			'student_data' => $mbo_student_data,
    			'family_data' => $mbo_family_data
    		);
    		
    		/**
    		* Encrypt the data
    		* @params array $a_post_data
    		* @params string $app_name
    		* @params string $app_secret_keys
			**/
			
			$a_token_config = $this->config->item('token')['pmb'];
			$a_sites = $this->config->item('sites');
			$s_token = $a_token_config['access_token'];
			$s_secret_token = $a_token_config['secret_token'];

    		$hashed_string = $this->libapi->hash_data($a_post_data, $s_token, $s_secret_token);
    		
    		/**
    		* Post data format to API
    		* JSON string with 2 array keys
    		* access_token: APP Name
    		* data: hashed string
    		**/
    		$post_data = json_encode(array(
    			'access_token' => $s_token,
    			'data' => $hashed_string
			));
			$url = $a_sites['pmb'].'api/create_new_student';
    		$result = $this->libapi->post_data($url, $post_data);
    		
    		if($result['code'] == 0){
    			if($mbo_personal_data){
    				$a_personal_data = (array)$mbo_personal_data;
    				$this->send_registration_email($a_personal_data, $b_send_email);
    				return true;
    			}
    			else{
        			return false;
    			}
    		}
    		else{
    			return false;
    		}
		}
		else{
    		return false;
		}
	}

	public function testing_send_email()
	{
		$s_personal_data_id  = '973fbceb-e5a9-4a55-b637-595eb10b96eb';
		$s_invoice_id = 'f3e6e0bc-dae2-4bfd-a48c-5e3c57c96c0f';
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
		$mba_student_data = $this->Sm->get_student_filtered([
			'ds.personal_data_id' => $s_personal_data_id
		]);
		$a_student_data = (array) $mba_student_data[0];

		$o_family_data = $this->Fm->get_family_by_personal_data_id($s_personal_data_id);
		$mba_family_members = $this->Fm->get_family_members($o_family_data->family_id, array(
			'family_member_status != ' => 'child'
		));
		
		$evaluation_data = modules::run('download/doc_download/generate_evaluation_result', $a_student_data);
		$s_admission_result_file = $evaluation_data['path'].$evaluation_data['file'];

		// $a_invoice_information = modules::run('finance/invoice/get_all_invoice_information', $s_invoice_id);
		// print('<pre>');var_dump($a_invoice_information);exit;
		$this->send_admission_result($mbo_student_data, $mba_family_members, $s_admission_result_file, $s_invoice_id);
	}

	public function accepted_student_active()
	{
		if ($this->input->is_ajax_request()) {
			$s_student_id = $this->input->post('student_id');
			$mba_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id]);
			if ($mba_student_data) {
				modules::run('student/init_student_data');
				$a_return = ['code' => 0, 'message' => 'Success!'];
			}
			else {
				$a_return = ['code' => 1, 'message' => 'student data not found!'];
			}

			print json_encode($a_return);
		}
	}
	
	public function accepted_as_student($o_student_data, $s_invoice_id)
	{
		$o_family_data = $this->Fm->get_family_by_personal_data_id($o_student_data->personal_data_id);
		$mba_family_members = $this->Fm->get_family_members($o_family_data->family_id, array(
			'family_member_status != ' => 'child'
		));
		$this->Sm->update_student_data(array('student_status' => 'pending'), $o_student_data->student_id);
		$a_file_data = array(
			'personal_data_id' => $o_student_data->personal_data_id,
			'document_id' => '0d06de83-ac58-11e9-9ee5-5254005d90f6'
		);
		// $s_admission_result_file = $this->create_admission_result($o_student_data);
		$evaluation_data = modules::run('download/doc_download/generate_evaluation_result', $a_student_data);
		$s_admission_result_file = $evaluation_data['path'].$evaluation_data['file'];
		$this->send_admission_result($o_student_data, $mba_family_members, $s_admission_result_file, $s_invoice_id);
		$this->Fmam->upload_file($a_file_data);
	}

	public function push_admission_result($s_student_id)
	{
		$o_student_data = $this->Sm->get_student_filtered(['ds.student_id' => $s_student_id])[0];
		// $s_admission_result_file = $this->create_admission_result($o_student_data);
		$evaluation_data = modules::run('download/doc_download/generate_evaluation_result', $a_student_data);
		$s_admission_result_file = $evaluation_data['path'].$evaluation_data['file'];
	}

	function test_create_admission_result($s_student_id) {
		$mba_student_data = $this->Sm->get_student_filtered([
			'ds.student_id' => $s_student_id
		]);
		if ($mba_student_data) {
			$o_student_data = $mba_student_data[0];
			$a_student_data = (array) $o_student_data;
			$evaluation_data = modules::run('download/doc_download/generate_evaluation_result', $a_student_data);
			print('<pre>');var_dump($evaluation_data);exit;
		}
	}
	
	public function send_admission_result($o_student_data, $mba_family_data, $s_admission_result_file, $s_invoice_id)
	{
		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
		$a_invoice_information = modules::run('finance/invoice/get_all_invoice_information', $s_invoice_id);
		
		$o_personal_data = $this->Pdm->get_personal_data_by_id($o_student_data->personal_data_id);
		$s_full_payment_amount_display = "Rp. ".number_format($a_invoice_information->invoice_full->sub_invoice_amount, 0, ',', '.').",-";
		$s_full_payment_deadline_display = date('j F Y', strtotime($a_invoice_information->invoice_full->sub_invoice_details[0]->sub_invoice_details_deadline));
		$s_display_va_full_payment = implode(' ', str_split($a_invoice_information->invoice_full->sub_invoice_details[0]->sub_invoice_details_va_number, 4));
		$s_installment_amount_display = "Rp. ".number_format($a_invoice_information->invoice_installment->sub_invoice_amount, 0, ',', '.').",-";
		
		$t_installment_details = '';
		$i = 1;
		// foreach($a_invoice_information->invoice_installment->sub_invoice_details as $installment){
		// 	$t_installment_details .= "Instalment $i: ".number_format($installment->sub_invoice_details_amount, 0, ',', '.').",- VA Number: ".implode(' ', str_split($installment->sub_invoice_details_va_number, 4))." at the latest on ".date('j F Y', strtotime($installment->sub_invoice_details_deadline))."\n";
		// 	$i++;
		// }
		
		$i = 1;
		$t_bill_details = '';
		$b_pec_status = false;
		foreach($a_invoice_information->invoice_details as $o_invoice_details){
			if($o_invoice_details->payment_type_code == '06'){
				$b_pec_status = true;
			}
			$s_operator_sign = ($o_invoice_details->fee_amount_sign_type == 'negative') ? '-' : '';
			$t_bill_details .= "$i. " . $o_invoice_details->fee_description." $s_operator_sign @ ".number_format($o_invoice_details->fee_amount, 0, ',', '.').",-\n";
			$i++;
		}

		$this->email->clear(true);
		$t_body = <<<TEXT
Dear Mr/Mrs. {$o_personal_data->personal_data_name},

Greetings from International University Liaison Indonesia - IULI.

Congratulations on your admission to the International University Liaison Indonesia!

We are pleased to inform you that you have passed the admission process.

Kindly find enclosed the Evaluation Result, Statement Letter & Study Fee Payment Agreement.

Below is the payment scheme for the 1st semester {$o_student_data->academic_year_id}.

Payment Details:
{$t_bill_details}

Total Semester Fee: {$s_full_payment_amount_display}

Payment Deadline: {$s_full_payment_deadline_display}.

Please transfer payment to:
Beneficiary Name: {$o_personal_data->personal_data_name}
Bank: Bank Negara Indonesia - Aeon Mall Branch, BSD Tangerang.
Virtual Account Number: {$s_display_va_full_payment}

Note: BNI Virtual Account System will reject payment which is not the exact amount as 'Total Semester Fee' stated above.
	
To confirm your registration as a IULI student of batch {$o_student_data->academic_year_id} you need to take the following steps:

1. Pay the tuition fee (by the payment schedule date above).
2. Sign and submit the Study Fee Payment Agreement (at the same time as the payment date).
3. Sign and submit the Statement Letter (at the same time as the payment date).
4. Submit a legalized copy of high school certificate & transcript (Ijazah & SKHUN SMA). For students who graduate from an international school, the Ijazah & SKHUN can be substituted with "Surat Keterangan Penyetaraan Sertifikat" from the Ministry of National Education of Indonesia or Ijazah & SKHUN Paket C.

We look forward to seeing you at the beginning of the first semester of {$o_student_data->academic_year_id} in IULI Campus.

Best regards,

IULI Admissions Office
www.iuli.ac.id
Hotline No.: 085212318000 (WA/Line/Phone call)
TEXT;
		
		$this->email->from('employee@company.ac.id', 'IULI Admission Team');
		$this->email->to(array('employee@company.ac.id'));
		$this->email->bcc('employee@company.ac.id');
/*
		$this->email->to($o_personal_data->personal_data_email);
		$a_cc = array();
		if($mba_family_data){
			foreach($mba_family_data as $family){
				array_push($a_cc, $family->personal_data_email);
			}
		}
		$this->email->cc($a_cc);
*/
		$this->email->subject('[IULI] Evaluation Result');
		$this->email->message($t_body);
		$this->email->attach($s_admission_result_file);
		$this->email->attach(APPPATH.'uploads/templates/admission/2024/STUDY FEE PAYMENT AGREEMENT-2024.pdf');
		$this->email->attach(APPPATH.'uploads/templates/admission/2024/StatementLetter-2024-2025.pdf');
		// $this->email->attach(APPPATH.'uploads/templates/admission/2022/Pilihan_Jurusan_Alternative_Batch_2022-2023.pdf');
		// if($b_pec_status){
		// 	$this->email->attach(APPPATH.'uploads/templates/EnglishPreparationCourseForm-2020.pdf');
		// }
		if($this->email->send()){
			$this->email->clear();
		}
	}
	
	public function create_admission_result($o_student_data)
	{		
		$o_personal_data = $this->Pdm->get_personal_data_by_id($o_student_data->personal_data_id);
		$o_study_program = $this->Spm->get_study_program($o_student_data->study_program_id);
		
		set_time_limit(0);
		$a_month = array(
			1 => "I",
			2 => "II",
			3 => "III",
			4 => "IV",
			5 => "V",
			6 => "VI",
			7 => "VII",
			8 => "VIII",
			9 => "IX",
			10 => "X",
			11 => "XI",
			12 => "XII"
		);

		$o_active_year = $this->Aym->get_academic_year_by_id($o_student_data->academic_year_id);
		$o_semester_settings_data = $this->Aym->get_semester_settings($o_student_data->academic_year_id, 1);
		$s_letter_counter = $this->Sm->count_student($o_student_data->academic_year_id, 'active');
		$s_letter_counter += 1;

		// $s_semester_date = date('F Y', strtotime($o_semester_settings_data->semester_start_date));
		$s_semester_date = 'September '.date('Y', strtotime($o_semester_settings_data->semester_start_date));
		$s_template_path = APPPATH.'uploads/templates/admission/';
		$s_media_path = APPPATH.'uploads/'.$o_student_data->personal_data_id.'/evaluation_result/';		
		
		if(!file_exists($s_media_path)){
			mkdir($s_media_path, 0777, TRUE);
		}
		
		$s_filename = "ET_Result_".$o_personal_data->personal_data_name;
		$s_xls_filename = str_replace(' ', '_', $s_filename.'.xls');
		$s_pdf_filename = str_replace(' ', '_', $s_filename.'.pdf');
		$s_xls_filepath = $s_media_path.$s_xls_filename;
		$s_pdf_filepath = $s_media_path.$s_pdf_filename;
		
		$s_template = $s_template_path.'Form_ET_Result_2022-2023.xls';
		$s_title = "Evaluation Result ".$o_personal_data->personal_data_name;
		
		$o_spreadsheet = IOFactory::load($s_template);
		$o_sheet = $o_spreadsheet->getActiveSheet();
		$o_spreadsheet->getProperties()
		->setTitle($s_title)
		->setCreator("IULI Admission")
		->setCategory("Evaluation Result");
		
		$i_month = date('n', time());
		$s_called = ($o_personal_data->personal_data_gender == 'M') ? 'Mr. ' : 'Mrs. ';
		
		$o_sheet->setCellValue('A6', 'Tangerang Selatan, '.date('j F Y', time()));
		$o_sheet->setCellValue('A7', 'Ref: L/ADM/'.$s_letter_counter.'/'.$a_month[$i_month].'/'.$o_active_year->academic_year_id.'/SYS');
		$o_sheet->setCellValue('A11', $s_called.$o_personal_data->personal_data_name);
		$o_sheet->setCellValue('A12', 'Department of '.$o_study_program[0]->study_program_name);
		$o_sheet->setCellValue('A13', '');
		$o_sheet->setCellValue('A14', 'Dear '.$s_called.$o_personal_data->personal_data_name);
		$o_sheet->setCellValue('A20', 'The first semester starts on '.$s_semester_date.'. Please pay attention to the following information.');
		$o_sheet->setCellValue('A24', 'To confirm your registration as a IULI student of batch '.$o_student_data->academic_year_id.' you need to take the following steps:');
		$o_sheet->setCellValue('B28', 'Pay the tuition fee (by the payment schedule indicated on the email).');
		$o_sheet->setCellValue('B31', 'Sign and submit the Agreement for Tuition Fee Payment '.$o_student_data->academic_year_id.' (at the same time as the payment date).');
		$o_sheet->setCellValue('A46', 'We look forward to seeing you at the beginning of the new semester in '.$o_student_data->academic_year_id.' in IULI Campus.');
		
		$o_sheet->setCellValue('D20', 'Perkuliahan anda dimulai pada bulan '.$s_semester_date.'. Mohon luangkan waktu untuk mempelajari beberapa dokumen penting terlampir.');
		$o_sheet->setCellValue('D24', 'Untuk memastikan bahwa anda terdaftar sebagai mahasiswa IULI tahun ajaran '.$o_student_data->academic_year_id.', anda diwajibkan melakukan beberapa langkah berikut:');
		$o_sheet->setCellValue('E28', 'Membayar biaya kuliah (sesuai dengan jadwal yang tertera di email).');
		$o_sheet->setCellValue('E31', 'Menandatangani dan mengembalikan Perjanjian Pembayaran Biaya Kuliah '.$o_student_data->academic_year_id.' (sesuai dengan tanggal pembayaran).');
		$o_sheet->setCellValue('D46', 'Kami menantikan untuk bertemu anda kembali pada semester baru tahun ajaran '.$o_student_data->academic_year_id.' di kampus IULI.');
		
		$o_writer = IOFactory::createWriter($o_spreadsheet, 'Xls');
		$o_writer->save($s_xls_filepath);
		$s_shell_exec = '/usr/bin/soffice --headless --convert-to pdf '.str_replace('  ', '_', $s_xls_filepath).' --outdir '.$s_media_path;
		shell_exec($s_shell_exec);
		
		return $s_pdf_filepath;
	}
	
	public function send_enrollment_va($a_personal_data, $s_va_number, $s_amount, $b_send_email, $b_scholarship = false)
	{
		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
		$s_va_number = implode(' ', str_split($s_va_number, 4));
		$s_filelink = base_url()."public/files/download/finance/Panduan-Pembayaran-Virtual-Account-BNI.pdf";
		$t_email_body = <<<TEXT
Dear {$a_personal_data['personal_data_name']},

Thank you for your interest in joining International University Liaison Indonesia.

Please transfer the enrollment fee in the amount of {$s_amount} to
Beneficiary Name: {$a_personal_data['personal_data_name']}
Bank: BNI 46
Virtual Account Number: {$s_va_number}

Terms and Conditions:
1. Please transfer the exact amount stated above.
2. Unmatched payment will be rejected by BNI.
3. Should you have any inquiry regarding:
3.1. Registration, please email to employee@company.ac.id
3.2. Payment, please email to employee@company.ac.id and if you have any payment
difficulties, please download and read the manual provided:
{$s_filelink}

Thank you for your registration and cooperation to transfer in the exact
amount stated above.

IULI Admission
International University Liaison Indonesia - IULI.
TEXT;

		if ($b_scholarship) {
			$this->email->from('employee@company.ac.id', 'IULI Scholarship');
			$this->email->bcc(array('employee@company.ac.id', 'employee@company.ac.id', 'employee@company.ac.id'));
			$this->email->subject('[FINANCE] IULI Enrollment Fee Details');
		}else{
			$this->email->from('employee@company.ac.id', 'IULI Admission');
			$this->email->bcc(array('employee@company.ac.id', 'employee@company.ac.id'));
			$this->email->subject('[FINANCE] IULI Enrollment Fee Details');
		}
		
		if($b_send_email){
			$s_email = $a_personal_data['personal_data_email'];
		}
		else{
			$s_email = 'employee@company.ac.id';
		}
		
		$this->email->to($s_email);
		$this->email->message($t_email_body);
		
		if(!$this->email->send()){
			$this->log_activity('Email did not sent');
			$this->log_activity('Error Message: '.$this->email->print_debugger());
			return false;
		}
		else{
			return true;
		}
	}

	public function send_registration_scholarship_existing_student($o_student_data)
	{
		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
		$t_email_body = <<<TEXT
Dear {$o_student_data->personal_data_name},

Thank you for your interest in joining Scholarship DAAD International
University Liaison Indonesia.
This is a one-time email. You have received it because you signed up for an
scholarship from DAAD in International University Liaison Indonesia - IULI.

Please complete your registration by submitting the additional documents to employee@company.ac.id:
1. Curriculum Vitae (in English)
2. Transcript Semester 1 - 5 
3. Motivational letter (≥300 words in English/ 2 page of A4 size).
4. Letters of recommendation from IULI Professor/ Lecturer
5. Certification of language proficiency (TOEFL/IELTS/TOEIC).
6. Certificates of achievement performance (academic or non-academic performance).

If you believe that you have received this email in error please ignore it.

Best Regards,
Admission Team

International University Liaison Indonesia
Associate Tower 7th Floor.
Intermark Indonesia BSD
Jl. Lingkar Timur BSD Serpong
Tangerang Selatan 15310
Phone: +62 (0) 852 123 18000
Email: employee@company.ac.id
TEXT;

		$this->email->from('employee@company.ac.id', 'IULI Scholarship');
		$this->email->to($o_student_data->student_email);
		// $this->email->to('employee@company.ac.id');
		$this->email->bcc(array('employee@company.ac.id', 'employee@company.ac.id'));
		$this->email->subject('[DAAD-IULI] Scholarship Registration');
		$this->email->message($t_email_body);
		return $this->email->send();
	}
	
	public function send_registration_email($a_personal_data, $b_send_email)
	{
		$s_pmb_url = $this->config->item('sites')['pmb'];
		$s_link = $s_pmb_url.'confirmation_token/sign_in/set_password/'.$a_personal_data['personal_data_password_token'];
		
		$t_email_body = <<<TEXT
Dear {$a_personal_data['personal_data_name']},
 
Thank you for your interest in joining International University Liaison Indonesia.
Please click the link below to confirm your email address.
 
{$s_link}

This is a one-time email. You have received it because you signed up for an admission account in International University Liaison Indonesia - IULI. If you believe that you have received this email in error please ignore it.

Best Regards,
Admission Team

International University Liaison Indonesia
Associate Tower 7th Floor.
Intermark Indonesia BSD
Jl. Lingkar Timur BSD Serpong
Tangerang Selatan 15310
Phone: +62 (0) 852 123 18000
Email: employee@company.ac.id
TEXT;
		
		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
		$this->email->from('employee@company.ac.id', 'IULI PMB');
		if($b_send_email){
			$s_email = $a_personal_data['personal_data_email'];
		}
		else{
			$s_email = 'employee@company.ac.id';
		}
		$this->email->to($s_email);
		$this->email->bcc(array('employee@company.ac.id', 'employee@company.ac.id'));
		$this->email->subject('[ADMISSION] IULI Account Registration');
		$this->email->message($t_email_body);
		
		if(!$this->email->send()){
			$this->log_activity('Email did not sent');
			$this->log_activity('Error Message: '.$this->email->print_debugger());
			return false;
		}
		else{
			return true;
		}
	}

	function test_invitation_online_test() {
		$this->a_page_data['personal_data_name'] = 'Budi Siswanto';
		$this->a_page_data['token_english_test'] = 'bba4-37cb-cbbe-d67b';
		$s_teks = $this->load->view('messaging/admission/invitation_online_test', $this->a_page_data, true);
		print($s_teks);exit;
	}

	public function send_enrollment_custom()
	{
		// print('warung tutup!');exit;
		$mbo_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => 'eaea849e-e2e1-49a1-bcf1-64b71f629fad'])[0];
		$a_personal_data = array(
			'personal_data_name' => $mbo_personal_data->personal_data_name,
			'personal_data_email' => $mbo_personal_data->personal_data_email,
			'personal_data_cellular' => $mbo_personal_data->personal_data_cellular,
			'personal_data_password_token' => md5(uniqid().time())
		);

		$b_send = $this->send_enrollment_va(
			$a_personal_data,
			'8310102408136001', 
			"Rp. ".number_format(200000, 0, ",", "."),
			true
		);

		if ($b_send) {
			print('<pre>');
			var_dump($a_personal_data);
		}else{
			print('fail');
		}
		// $this->send_credentials(true, 'c20e9a21-0a7e-498a-ba8f-fd597325c4e8');
	}
	
	// public function create_new_student()
	// {
	// 	if($this->input->is_ajax_request()){
	// 		$this->form_validation->set_rules('fullname', 'Name', 'trim|required|alpha_numeric_spaces');
	// 		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
	// 		$this->form_validation->set_rules('mobile_phone', 'Mobile Phone', 'trim|required|numeric');
	// 		$this->form_validation->set_rules('study_program_id', 'Study Program', 'trim|required');
			
	// 		if($this->form_validation->run()){
	// 			$this->db->trans_start();
				
	// 			$mbo_personal_data = $this->Pdm->get_personal_data_by_email(set_value('email'));
	// 			$o_active_year = $this->Am->get_active_intake_year();
	// 			$mbo_family_data = false;
				
	// 			if($mbo_personal_data){
	// 				$a_return = array('code' => 1, 'message' => 'Email has been used');
	// 			}
	// 			else{
	// 				$a_personal_data = array(
	//                     'personal_data_name' => strtoupper(set_value('fullname')),
	//                     'personal_data_email' => set_value('email'),
	//                     'personal_data_cellular' => set_value('mobile_phone'),
	//                     'personal_data_password_token' => md5(uniqid().time())
	//                 );
	//                 $s_personal_data_id = $this->Pdm->create_new_personal_data($a_personal_data);
	                
	//                 $a_student_data = array(
	// 	                'personal_data_id' => $s_personal_data_id,
	// 	                'academic_year_id' => $o_active_year->academic_year_id,
	// 	                'finance_year_id' => $o_active_year->academic_year_id,
	// 					'study_program_id' => set_value('study_program_id'),
	// 	                'date_added' => date('Y-m-d H:i:s'),
	// 	                'student_date_enrollment' => date('Y-m-d H:i:s')
	//                 );
	                
	//                 $s_student_id = $this->Sm->create_new_student($a_student_data);
	//                 $mba_academic_year_data = $this->General->get_batch(false, true);
	                
	// 				if($mba_academic_year_data){
	// 					$i_academic_year_candidate_counter = $mba_academic_year_data[0]->academic_year_candidates_counter;
	// 					$i_academic_year_candidate_counter++;
	// 					$this->Am->update_academic_year_data($mba_academic_year_data[0]->academic_year_id, array('academic_year_candidates_counter' => $i_academic_year_candidate_counter));
	// 				}
	                
	//                 if(!$mbo_family_data){
	// 	                $s_family_id = $this->Fm->create_family();
	                
	// 	                $a_init_family_member = array(
	// 						'family_id' => $s_family_id,
	// 						'personal_data_id' => $s_personal_data_id,
	// 						'family_member_status' => 'child',
	// 						'date_added' => date('Y-m-d H:i:s')
	// 					);
		                
	// 	                $this->Fm->add_family_member($a_init_family_member);
	//                 }
	                
	//                 if($this->db->trans_status() === false){
	// 	                $this->db->trans_rollback();
	// 	                $a_return = array('code' => 1, 'message' => 'Something wrong!');
	//                 }
	//                 else{
	// 					$mba_fee_data = $this->Im->get_fee(array('payment_type_code' => '01', 'academic_year_id' => $o_active_year->academic_year_id));
	// 	                if(!$mba_fee_data){
	// 		                $this->db->trans_rollback();
	// 						$a_return = array('code' => 1, 'message' => 'Payment data not found, Rolling back transaction!');   
	// 	                }
	// 	                else{
	// 						$mbs_va_number = $this->Bm->get_va_number(
	// 							$mba_fee_data[0]->payment_type_code,
	// 							0,
	// 							0,
	// 							'candidate',
	// 							null,
	// 							$mba_academic_year_data[0]->academic_year_id
	// 						);
	// 						if($mbs_va_number){
	// 							$a_invoice_data = array(
	// 								'personal_data_id' => $s_personal_data_id,
	// 								'invoice_number' => $this->Im->get_invoice_number($mba_fee_data[0]->payment_type_code),
	// 								'invoice_description' => $mba_fee_data[0]->fee_description,
	// 								'invoice_allow_fine' => 'no'
	// 							);
	// 							$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
								
	// 							$a_invoice_details_data = array(
	// 								'invoice_id' => $s_invoice_id,
	// 								'fee_id' => $mba_fee_data[0]->fee_id,
	// 								'invoice_details_amount' => $mba_fee_data[0]->fee_amount,
	// 								'invoice_details_amount_number_type' => $mba_fee_data[0]->fee_amount_number_type,
	// 								'invoice_details_amount_sign_type' => $mba_fee_data[0]->fee_amount_sign_type
	// 							);
	// 							$this->Im->create_invoice_details($a_invoice_details_data);
								
	// 							$a_sub_invoice_data = array(
	// 								'sub_invoice_amount' => $mba_fee_data[0]->fee_amount,
	// 								'invoice_id' => $s_invoice_id
	// 							);
	// 							$s_sub_invoice_id = $this->Im->create_sub_invoice($a_sub_invoice_data);
								
	// 							$a_billing_data = array(
	// 								'trx_amount' => $mba_fee_data[0]->fee_amount,
	// 								'billing_type' => 'c',
	// 								'customer_name' => $a_personal_data['personal_data_name'],
	// 								'virtual_account' => $mbs_va_number,
	// 								'description' => $mba_fee_data[0]->fee_description,
	// 								'datetime_expired' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+3 month")),
	// 								'customer_email' => 'bni.employee@company.ac.id'
	// 							);
								
	// 							$a_return_billing_data = $this->Bm->create_billing($a_billing_data);
								
	// 							if($a_return_billing_data['status'] === '000'){
	// 								$a_sub_invoice_details = array(
	// 									'sub_invoice_id' => $s_sub_invoice_id,
	// 									'trx_id' => $a_return_billing_data['trx_id'],
	// 									'sub_invoice_details_amount' => $mba_fee_data[0]->fee_amount,
	// 									'sub_invoice_details_va_number' => $mbs_va_number,
	// 									'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time()))),
	// 									'sub_invoice_details_description' => $mba_fee_data[0]->fee_description
	// 								);
	// 								$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details);
									
	// 								$this->db->trans_commit();
									
	// 								$b_send_email = false;
	// 								if($this->input->post('send_email')){
	// 									$b_send_email = true;
	// 								}
	// 								$this->send_enrollment_va(
	// 									$a_personal_data, 
	// 									$mbs_va_number, 
	// 									"Rp. ".number_format($mba_fee_data[0]->fee_amount, 0, ",", "."),
	// 									$b_send_email
	// 								);
	// 								$this->send_credentials($b_send_email, $s_student_id);
									
	// 								$a_return = array('code' => 0, 'va_number' => $mbs_va_number);
	// 							}
	// 							else{
	// 								$this->db->trans_rollback();
	// 								$a_return = array('code' => $a_return_billing_data['status'], 'message' => $a_return_billing_data['message'].": ".$mbs_va_number, 'status' => $this->db->trans_status());
	// 							}
	// 						}
	// 						else{
	// 							$this->db->trans_rollback();
	// 							$a_return = array('code' => 2, 'message' => 'Please contact IS&T Dept');
	// 						}
	// 	                }
	//                 }
	// 			}
	// 		}
	// 		else{
	// 			$a_return = array('code' => 1, 'message' => validation_errors('<p>', '</p>'));
	// 		}
			
	// 		print json_encode($a_return);
	// 		exit;
	// 	}
	// }
}