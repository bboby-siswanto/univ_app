<?php
class Api extends Api_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Admission_model', 'Adm');
		$this->load->model('personal_data/Personal_data_model', 'Personal_data');
		$this->load->model('personal_data/Family_model', 'Family');
		$this->load->model('student/Student_model', 'Student');
		$this->load->model('finance/Invoice_model', 'Im');
		$this->load->model('finance/Bni_model', 'Bm');
	}

	public function receive_doc()
	{
		$a_post_data = $this->a_api_data;
		$this->return_json(array('uri' => 'connect'));
	}

	public function test_for_sandbox()
	{
		$this->session->set_userdata([
			'auth' => true,
			'environment' => 'sandbox'
		]);

		$this->load->model('finance/Bni_model', 'Bnim');

		$url = $this->Bnim->get_url();
		$a_session = $this->session->userdata();
		$this->session->sess_destroy();
		$this->return_json(array('uri' => $url));
	}
	
	public function has_completed_questionnaire()
	{
		$a_post_data = $this->a_api_data;
		$s_personal_data_id = $a_post_data['personal_data_id'];
		$i_section_id = $a_post_data['section_id'];
		
		$mba_qustionnaire_answers = $this->Adm->questionnaire_answers($s_personal_data_id, $i_section_id);
		if($mba_qustionnaire_answers){
			$rtn = array('code' => 0);
		}
		else{
			$rtn = array('code' => 1);
		}
		$this->return_json($rtn);
	}

	public function student_registration_scholarship()
	{
		$a_post_data = $this->a_api_data;
		$s_student_number = $a_post_data['student_number'];
		$s_scholarship_id = $a_post_data['scholarship_id'];

		$mbo_student_data = $this->Student->get_student_filtered([
			'ds.student_number' => $s_student_number
		])[0];

		if ($mbo_student_data) {
			$registration = $this->handle_registration_scholarship($mbo_student_data->personal_data_id, $s_scholarship_id);
			if ($registration['code'] == 0) {
				$send_email_registration = Modules::run(
					'admission/send_registration_scholarship_existing_student', 
					$mbo_student_data
				);

				if ($send_email_registration) {
					$a_return = [
						'code' => 0,
						'message' => 'Success',
						'data' => $mbo_student_data
					];
				}else{
					$a_return = array('code' => 1, 'message' => 'Failed to send registration email', 'data' => $mbo_student_data);
				}
				
			}else{
				$a_return = $registration;
				$a_return['data'] = $mbo_student_data;
			}
			
		}else{
			$a_return = [
				'code' => 1,
				'message' => 'Data not found',
				'data' => false
			];
		}

		$this->return_json($a_return);
	}

	public function handle_registration_scholarship($s_personal_data_id, $s_scholarship_id)
	{
		$o_academic_year_active = $this->Adm->get_active_intake_year();
		$mbo_registration_data = $this->General->get_where('dt_registration_scholarship', [
			'personal_data_id' => $s_personal_data_id,
			'academic_year_id' => $o_academic_year_active->academic_year_id
		])[0];

		$a_registration_data = [
			'personal_data_id' => $s_personal_data_id,
			'scholarship_id' => $s_scholarship_id,
			'academic_year_id' => $o_academic_year_active->academic_year_id
		];

		if ($mbo_registration_data) {
			$save = $this->Adm->submit_registration_sholarship($a_registration_data, ['registration_id' => $mbo_registration_data->registration_id]);
		}else{
			$a_registration_data['registration_id'] = $this->uuid->v4();
			$a_registration_data['date_added'] = date('Y-m-d H:i:s');
			$save = $this->Adm->submit_registration_sholarship($a_registration_data);
		}
		
		if ($save) {
			$a_return = ['code' => 0, 'message' => 'Success!'];
		}else{
			$a_return = ['code' => 1, 'message' => 'Failed processing data!'];
		}
		
		return $a_return;
	}
	
	public function handle_questionnaire()
	{
		$a_post_data = $this->a_api_data;
		$a_questionnaire_answers = $a_post_data['answers'];
		$s_personal_data_id = $a_post_data['personal_data_id'];
		$i_section_id = $a_post_data['section_id'];

		$mba_student_exists = $this->General->get_where('dt_student', ['personal_data_id' => $s_personal_data_id]);
		if (!$mba_student_exists) {
			$this->create_new_student();
		}

		foreach($a_questionnaire_answers as $val){
			$a_prepare_answer = array(
				'question_id' => $val['question_id'],
				'question_section_id' => $i_section_id,
				'personal_data_id' => $s_personal_data_id,
				'answer_value' => $val['answer']
			);
			$this->Adm->insert_questionnaire($a_prepare_answer);	
		}
		
		$this->return_json(array('code' => 0));
	}

	public function check_if_sunday()
	{
		$s_date = '2023-02-12';
		var_dump();exit;
	}

	// public function test_modules()
	// {
	// 	$wa_send = modules::run('whatsapp/send_wa_invitation_test', 'c2fb53e7-e61a-424a-ae47-9eb6195e5893', '1234', 'ec07-b43e-b153-44d9');
	// 	print('<pre>');var_dump($wa_send);exit;
	// }

	public function send_invitation_online_test($o_personal_data, $s_english_token = '')
	{
		$date_test = date('d F Y', strtotime('sunday this week'));
		$s_link_test = 'https://portal.iuli.ac.id/exam/auth_entrance_test';

		// $wa_send = modules::run('whatsapp/send_wa_invitation_test', $o_personal_data->personal_data_id, $s_link_test, $s_english_token);
		$this->a_page_data['personal_data_name'] = $o_personal_data->personal_data_name;
		$this->a_page_data['link_test'] = $s_link_test;
        $this->a_page_data['day_test'] = 'Sunday';
        $this->a_page_data['hari_test'] = 'Minggu';
        $this->a_page_data['date_test'] = $date_test;
        $this->a_page_data['tanggal_test'] = $date_test;
        $this->a_page_data['time_test'] = '10:00 - 11:00';
		$this->a_page_data['token_english_test'] = $s_english_token;
        // $s_text_message = $this->load->view('messaging/admission/invitation_online_test', $this->a_page_data, true);
		$s_text_message = "";

		$config['mailtype'] = 'html';
		$this->email->initialize($config);

		$this->email->from('employee@company.ac.id', 'IULI Admission');
		if ($o_personal_data->personal_data_email == 'employee@company.ac.id') {
			// $this->email->bcc(array('employee@company.ac.id'));
		}else{
			// $this->email->bcc(array('employee@company.ac.id'));
		}
		$this->email->subject('[ADMISSION] IULI Invitation Online Test');
		$this->email->to($o_personal_data->personal_data_email);
		$this->email->message($s_text_message);
		$send = $this->email->send();
		return [
			'email_sent' => $send,
			// 'wa_sent' => $wa_send
		];
	}
	
	public function create_new_student()
	{	
		$a_post_data = $this->a_api_data;

		$s_token_online_test = '';
		$s_source_remote_addr = (isset($a_post_data['remote_addr'])) ? $a_post_data['remote_addr'] : '';
		$a_personal_data = $a_post_data['personal_data'];
		$a_student_data = $a_post_data['student_data'];
		$a_family_data = $a_post_data['family_data'];
		$sholarship_id = false;
		if (is_array($a_post_data)) {
			if ((array_key_exists('sholarship_id', $a_post_data)) AND ($a_post_data['sholarship_id'] == 'true')) {
				$sholarship_id = true;
			}
		}
		// $sholarship_id = (($a_post_data['sholarship_id'] === null) OR ($a_post_data['sholarship_id'] == 'false')) ? false : $a_post_data['sholarship_id'];
		$o_active_year = $this->Adm->get_active_intake_year();
		
		$a_init_family = array(
			'family_id' => $a_family_data['family_id'],
			'date_added' => date('Y-m-d H:i:s')
		);
		
		$a_init_family_member = array(
			'family_id' => $a_family_data['family_id'],
			'personal_data_id' => $a_family_data['personal_data_id'],
			'family_member_status' => $a_family_data['family_member_status'],
			'date_added' => date('Y-m-d H:i:s')
		);
		
		$mbo_profile = $this->Personal_data->get_personal_data_by_email($a_personal_data['personal_data_email']);
		// $this->db->trans_start();
		// $test_save = $this->Personal_data->create_new_personal_data_test($a_personal_data);
		// print('<pre>');var_dump($test_save);

		if($mbo_profile){
			$mbo_this_student = $this->General->get_where('dt_student', ['personal_data_id' => $mbo_profile->personal_data_id])[0];
			$a_student_status_blocked = ['active','inactive','dropout'];

			if (($mbo_this_student) AND (!in_array($mbo_this_student->student_status, $a_student_status_blocked))) {
				if ($mbo_profile->personal_data_id != $a_personal_data['personal_data_id']) {
					$mba_personal_data_email = $this->General->get_where('dt_personal_data', ['personal_data_email' => $a_personal_data['personal_data_email']]);
					if (count($mba_personal_data_email) == 1) {
						$this->Personal_data->update_personal_data($a_personal_data, $mbo_profile->personal_data_id);
					}else{
						$this->Personal_data->create_new_personal_data($a_personal_data);
						$this->Family->create_family($a_init_family);
						$this->Family->add_family_member($a_init_family_member);
					}
				}
	
				$this->Personal_data->update_personal_data($a_personal_data, $a_personal_data['personal_data_id']);
				
				$mbo_family_data = $this->Family->get_family_by_personal_data_id($a_personal_data['personal_data_id']);
				if(!$mbo_family_data){
					$mbo_check_family_by_id = $this->Family->get_family_by_id($a_family_data['family_id']);
					if(!$mbo_check_family_by_id){
						$this->Family->create_family($a_init_family);
					}
					$this->Family->add_family_member($a_init_family_member);
				}
	
				// $mbo_student = $this->Student->get_student_by_id($a_student_data['student_id']);
				$mbo_student = $this->Student->get_student_by_personal_data_id($a_student_data['personal_data_id']);
				if($mbo_student){
					$this->Student->update_student_data($a_student_data, $a_student_data['student_id']);
	
					$mba_student_has_invoice_enrollment = $this->Im->student_has_invoice_va_number($a_student_data['personal_data_id'], '01');
					if (!$mba_student_has_invoice_enrollment) {
						$a_return = $this->create_enrollment_fee($a_student_data, $a_personal_data, $a_student_data['finance_year_id'], $sholarship_id);
					}else{
						$a_return = ['code' => 0];
					}
				}
				else{
					$this->Student->create_new_student($a_student_data);
					
					$mba_academic_year_data = $this->General->get_batch($a_student_data['academic_year_id']);
					if($mba_academic_year_data){
						$i_academic_year_candidate_counter = $mba_academic_year_data[0]->academic_year_candidates_counter;
						$i_academic_year_candidate_counter++;
						$this->Adm->update_academic_year_data($a_student_data['academic_year_id'], array('academic_year_candidates_counter' => $i_academic_year_candidate_counter));
					}
	
					$a_return = $this->create_enrollment_fee($a_student_data, $a_personal_data, $a_student_data['finance_year_id'], $sholarship_id);
				}
			}else{
				$a_return = ['code' => 1, 'message' => 'Email already used!'];
			}

			// $a_return = array('code' => 0);
		}
		else{
			$this->Personal_data->create_new_personal_data($a_personal_data);
			$this->Family->create_family($a_init_family);
			$this->Family->add_family_member($a_init_family_member);
			
			$mbo_student = $this->Student->get_student_by_id($a_student_data['student_id']);
			
			if($mbo_student){
				$this->Student->update_student_data($a_student_data, $a_student_data['student_id']);
				$a_return = array('code' => 0);
			}
			else{
				$this->Student->create_new_student($a_student_data);
				
				$mba_academic_year_data = $this->General->get_batch($a_student_data['academic_year_id']);
				if($mba_academic_year_data){
					$i_academic_year_candidate_counter = $mba_academic_year_data[0]->academic_year_candidates_counter;
					$i_academic_year_candidate_counter++;
					$this->Adm->update_academic_year_data($a_student_data['academic_year_id'], array('academic_year_candidates_counter' => $i_academic_year_candidate_counter));
				}

				// $s_fee_id = '25df9847-972a-11e9-ac6b-5254005d90f6';
				
				/* enrollment email starts here */
				$a_return = $this->create_enrollment_fee($a_student_data, $a_personal_data, $a_student_data['academic_year_id'], $sholarship_id);

			}
		}

		if ($sholarship_id) {
			$this->handle_registration_scholarship($a_personal_data['personal_data_id'], $sholarship_id);
		}
		
/*
		if($this->db->trans_status() === false){
			$this->db->trans_rollback();
			// $this->return_json(array('code' => 1, 'message' => 'Fail'));
			$this->return_json($a_return);
		}
		else{
			$this->db->trans_commit();
			$this->return_json($a_return);
		}
*/
		if ($a_return['code'] != 0) {
			$this->email->from('employee@company.ac.id');
			$this->email->to(array('employee@company.ac.id'));
			$this->email->subject('ERROR Create new student');
			$this->email->message(json_encode($a_post_data).'-----------------------------------'.json_encode($a_return));
			$this->email->send();
		}
		else {
			$mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $a_personal_data['personal_data_id']]);
			$mba_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $a_personal_data['personal_data_id']]);
			if ($mba_student_data) {
				$s_token_online_test = '';
				$mba_exam_list = $this->General->get_where('dt_exam_period');
				if ($mba_exam_list) {
					foreach ($mba_exam_list as $o_online_exam) {
						$mba_exam_data = $this->General->get_where('dt_exam_candidate', ['student_id' => $mba_student_data[0]->student_id, 'exam_id' => $o_online_exam->exam_id]);
						if (!$mba_exam_data) {
							$s_token_online_test = modules::run('admission/entrance_test/generate_token', $mba_student_data[0]->student_id);
							$mba_exam_data = [
								'exam_candidate_id' => $this->uuid->v4(),
								'student_id' => $mba_student_data[0]->student_id,
								'exam_id' => $o_online_exam->exam_id,
								'total_question' => '90',
								'token' => $s_token_online_test,
								'date_added' => date('Y-m-d H:i:s')
							];
							$this->General->insert_data('dt_exam_candidate', $mba_exam_data);
						}
						else {
							$s_token_online_test = $mba_exam_data[0]->token;
						}
					}
				}
				
				// $send_invitation = $this->send_invitation_online_test($mba_personal_data[0], $s_token_online_test);
				// $this->email->from('employee@company.ac.id');
				// $this->email->to(array('employee@company.ac.id'));
				// $this->email->subject('Notificatoin sent invitation');
				// $this->email->message(json_encode($send_invitation).'-----------------------------------'.json_encode($mba_personal_data));
				// $this->email->send();
			}
		}

		$this->force_remove_candidate($a_personal_data['personal_data_id'], $s_source_remote_addr);
		$this->return_json($a_return);
	}

	public function test_source_ip()
	{
		$this->force_remove_candidate('884d6207-1984-4420-ac98-0df891416ed9', '202.93.225.254');
	}

	public function force_remove_candidate($s_personal_data_id, $s_source_ip = false)
	{
		if (($s_source_ip) AND (!empty($s_source_ip))) {
			if (!empty($s_personal_data_id)) {
				$a_listip = $this->config->item('whitelist_ip');
				if (in_array($s_source_ip, $a_listip)) {
					$mba_personal_candidate = $this->General->get_where('dt_personal_data', ['personal_data_id' => $s_personal_data_id]);
					if ($mba_personal_candidate) {
						$mba_student_candidate = $this->General->get_where('dt_student', ['personal_data_id' => $s_personal_data_id]);
						if ($mba_student_candidate) {
							$this->General->force_delete('dt_personal_data', 'personal_data_id', $s_personal_data_id);
						}
					}
				}
			}
		}
	}

	// public function check_va_enrollment_fee($a_student_data, $a_personal_data)
	// {
	// 	$mba_fee_data = $this->Im->get_fee(array('payment_type_code' => '01', 'academic_year_id' => $a_student_data['academic_year_id']));
	// 	if(!$mba_fee_data){
	// 		$this->db->trans_rollback();
	// 		$a_return = array('code' => 1, 'message' => 'Payment data not found, Rolling back transaction!');   
	// 	}else{
	// 		$mba_student_has_invoice_enrollment = $this->Im->student_has_invoice_va_number($a_student_data['personal_data_id'], '01');
	// 		if (!$mba_student_has_invoice_enrollment) {
	// 			$a_return = $this->create_enrollment_fee($a_student_data, $a_personal_data, $a_student_data['finance_year_id']);
	// 		}else{
	// 			$a_return = ['code' => 0];
	// 		}
	// 	}
	// }

	function test_send_registration() {
		$a_student_data = [
			'academic_year_id' => '2024',
			'program_id' => 1
		];

		$mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => '47013ff8-89df-11ef-8f45-0068eb6957a0']);
		$a_personal_data = (array) $mba_personal_data[0];
		$this->send_registration_email($a_student_data, $a_personal_data, 2024);
	}

	// function send_registration_email($a_student_data, $a_personal_data, $s_academic_year_id, $b_scholarship = false)
	// {
	// 	$mba_fee_data = $this->create_new_enrollment_fee($a_student_data, $a_personal_data, $s_academic_year_id);
	// 	if ($mba_fee_data['code'] == 0) {
	// 		// array_merge($this->a_page_data, $mba_fee_data);
	// 		$this->a_page_data['enrollment_fee'] = $mba_fee_data;
	// 	}
	// 	$s_link = 'https://pmb.iuli.ac.id/confirmation_token/sign_in/email_confirmation/'.$a_personal_data['personal_data_email_confirmation_token'];
	// 	$this->a_page_data['confirmation_link'] = $s_link;
	// 	$this->a_page_data['personal_data'] = $a_personal_data;
	// 	$bodymessage = $this->load->view('messaging/admission/registration_welcome', $this->a_page_data, true);

	// 	$config = $this->config->item('mail_config');
	// 	$config['mailtype'] = 'html';
	// 	$this->email->initialize($config);
		
	// 	$s_email = $a_personal_data['personal_data_email'];
	// 	$this->email->to([$s_email]);
	// 	$this->email->message($bodymessage);

	// 	if ($b_scholarship) {
	// 		$this->email->from('employee@company.ac.id', 'IULI Scholarship');
	// 		// $this->email->bcc(array('employee@company.ac.id', 'employee@company.ac.id', 'employee@company.ac.id'));
	// 		$this->email->subject('[FINANCE] IULI Enrollment Fee Details');
	// 	}
	// 	else{
	// 		$this->email->from('employee@company.ac.id', 'IULI Admission');
	// 		// $this->email->bcc(array('employee@company.ac.id', 'employee@company.ac.id'));
	// 		$this->email->subject('[FINANCE] IULI Enrollment Fee Details');
	// 	}

	// 	$this->email->bcc(array('employee@company.ac.id'));
		
	// 	if(!$this->email->send()){
	// 		$this->log_activity('Email did not sent');
	// 		$this->log_activity('Error Message: '.$this->email->print_debugger());
	// 		return false;
	// 	}
	// 	else{
	// 		return true;
	// 	}
	// }

	// public function create_new_enrollment_fee($a_student_data, $a_personal_data, $s_academic_year_id)
	// {
	// 	$mba_fee_data = $this->Im->get_fee(array(
	// 		'payment_type_code' => '01',
	// 		'academic_year_id' => $a_student_data['academic_year_id'],
	// 	));
		
	// 	if(!$mba_fee_data){
	// 		$this->db->trans_rollback();
	// 		$a_return = array('code' => 1, 'message' => 'Payment data not found, Rolling back transaction!');   
	// 	}
	// 	else{
	// 		$mbs_va_number = $this->Bm->get_va_number(
	// 			$mba_fee_data[0]->payment_type_code,
	// 			0,
	// 			0,
	// 			'register',
	// 			null,
	// 			$s_academic_year_id,
	// 			($a_student_data['program_id'] !== null) ? $a_student_data['program_id'] : 1
	// 		);
			
	// 		if($mbs_va_number){
	// 			$a_billing_data = array(
	// 				'trx_amount' => $mba_fee_data[0]->fee_amount,
	// 				'billing_type' => 'c',
	// 				'customer_name' => $a_personal_data['personal_data_name'],
	// 				'virtual_account' => $mbs_va_number,
	// 				'description' => $mba_fee_data[0]->fee_description,
	// 				'datetime_expired' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+12 month")),
	// 				'customer_email' => 'bni.employee@company.ac.id'
	// 			);
				
	// 			$a_return_billing_data = $this->Bm->create_billing($a_billing_data);
				
	// 			if($a_return_billing_data['status'] === '000'){
	// 				$a_invoice_data = array(
	// 					'personal_data_id' => $a_personal_data['personal_data_id'],
	// 					'invoice_number' => $this->Im->get_invoice_number($mba_fee_data[0]->payment_type_code),
	// 					'invoice_description' => $mba_fee_data[0]->fee_description,
	// 					'invoice_allow_fine' => 'no',
	// 					'invoice_allow_reminder' => 'no',
	// 					'academic_year_id' => $s_academic_year_id,
	// 					'semester_type_id' => 1
	// 				);
	// 				$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
					
	// 				$a_invoice_details_data = array(
	// 					'invoice_id' => $s_invoice_id,
	// 					'fee_id' => $mba_fee_data[0]->fee_id,
	// 					'invoice_details_amount' => $mba_fee_data[0]->fee_amount,
	// 					'invoice_details_amount_number_type' => $mba_fee_data[0]->fee_amount_number_type,
	// 					'invoice_details_amount_sign_type' => $mba_fee_data[0]->fee_amount_sign_type
	// 				);
	// 				$this->Im->create_invoice_details($a_invoice_details_data);
					
	// 				$a_sub_invoice_data = array(
	// 					'sub_invoice_amount' => $mba_fee_data[0]->fee_amount,
	// 					'sub_invoice_amount_total' => $mba_fee_data[0]->fee_amount,
	// 					'invoice_id' => $s_invoice_id
	// 				);
	// 				$s_sub_invoice_id = $this->Im->create_sub_invoice($a_sub_invoice_data);

	// 				$a_sub_invoice_details = array(
	// 					'trx_id' => $a_return_billing_data['trx_id'],
	// 					'sub_invoice_id' => $s_sub_invoice_id,
	// 					'sub_invoice_details_amount' => $mba_fee_data[0]->fee_amount,
	// 					'sub_invoice_details_amount_total' => $mba_fee_data[0]->fee_amount,
	// 					'sub_invoice_details_va_number' => $mbs_va_number,
	// 					'sub_invoice_details_description' => $mba_fee_data[0]->fee_description,
	// 					'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+10 month")),
	// 					'sub_invoice_details_real_datetime_deadline' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+10 month"))
	// 				);

	// 				$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details);
	// 				$a_return = array(
	// 					'code' => 0,
	// 					'va_number' => $mbs_va_number = implode(' ', str_split($mbs_va_number, 4)),
	// 					'amount' => "Rp. ".number_format($mba_fee_data[0]->fee_amount, 0, ",", ".").',-'
	// 				);
	// 			}
	// 			else{
	// 				$a_return = array('code' => $a_return_billing_data['status'], 'message' => $a_return_billing_data['message'].": ".$mbs_va_number);

	// 				$a_body = [
	// 					'personal_data_id' => $a_personal_data['personal_data_id'],
	// 					'date' => date('Y-m-d H:i:s'),
	// 					'data' => $a_return,
	// 					'data_send' => $a_return_billing_data
	// 				];

	// 				$this->email->from('employee@company.ac.id');
	// 				$this->email->to(array('employee@company.ac.id'));
	// 				$this->email->subject('ERROR Create Enrollment Fees!');
	// 				$this->email->message(json_encode($a_body));
	// 				$this->email->send();
	// 			}
	// 		}
	// 		else{
	// 			$a_return = array('code' => 2, 'message' => 'Please contact IT Dept');
	// 		}
	// 	}

	// 	return $a_return;
	// }

	function send_enrollment_va() {
		$mba_student_candidate = $this->Student->get_student_filtered([
			'ds.academic_year_id' => 2024
		], ['pending']);
		// ], ['register', 'candidate']);
		if ($mba_student_candidate) {
			foreach ($mba_student_candidate as $o_candidate) {
				$mba_invoice_enrollment = $this->Im->get_invoice_by_deadline(['di.personal_data_id' => $o_candidate->personal_data_id], '01', false);
				if (!$mba_invoice_enrollment) {
					print($o_candidate->personal_data_name.'<br>');
					// $a_student_data = [
					// 	'academic_year_id' => $o_candidate->academic_year_id,
					// 	'study_program_id' => $o_candidate->study_program_id,
					// 	'program_id' => '1'
					// ];
					// $a_personal_data = [
					// 	'personal_data_name' => $o_candidate->personal_data_name,
					// 	'personal_data_id' => $o_candidate->personal_data_id,
					// 	'personal_data_email' => $o_candidate->personal_data_email
					// ];
					// $create_send_va = $this->create_enrollment_fee($a_student_data, $a_personal_data, 2024);
					// exit;
				}
			}
		}
		// print('<pre>');var_dump($mba_student_candidate);exit;
		// foreach ($a_student_id as $s_student_id) {
		// 	$mba_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id]);
		// 	if ($mba_student_data) {
		// 		$mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mba_student_data[0]->personal_data_id]);
		// 		if ($mba_personal_data) {
		// 			$a_student_data = [
		// 				'academic_year_id' => $mba_student_data[0]->academic_year_id,
		// 				'study_program_id' => $mba_student_data[0]->study_program_id,
		// 				'program_id' => '1'
		// 			];
		// 			$a_personal_data = [
		// 				'personal_data_name' => $mba_personal_data[0]->personal_data_name,
		// 				'personal_data_id' => $mba_personal_data[0]->personal_data_id,
		// 				'personal_data_email' => $mba_personal_data[0]->personal_data_email
		// 			];
		// 			$create_send_va = $this->create_enrollment_fee($a_student_data, $a_personal_data, 2024);
		// 			exit;
		// 		}
		// 		else {
		// 			print($s_student_id.' personal_data not found!');
		// 		}
		// 	}
		// 	else {
		// 		print($s_student_id.' student_data not found!');
		// 	}
		// }
	}

	function force_create_enrollment_fee($s_personal_data_id) {
		$a_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $s_personal_data_id]);
		$a_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $s_personal_data_id]);

		if ($a_student_data AND $a_personal_data) {
			$a_student_data = (array) $a_student_data[0];
			$a_personal_data = (array) $a_personal_data[0];
			$this->create_enrollment_fee($a_student_data, $a_personal_data, 2024, false);
		}
		else {
			print('data tidak ditemukan');exit;
		}
	}

	public function create_enrollment_fee($a_student_data, $a_personal_data, $s_academic_year_id, $b_scholarship = false)
	{
		// $s_start_hide_enrollment = date('Y-m-d H:i:s', strtotime('2021-06-19 00:00:00'));
        // $s_end_hide_enrollment = date('Y-m-d H:i:s', strtotime('2021-06-20 23:59:59'));
        // $now = date('Y-m-d H:i:s');

		// if (($now > $s_start_hide_enrollment) AND ($now <= $s_end_hide_enrollment)){ //Free enrollment fee
		// 	$a_return = array('code' => 0);
		// }else{
			$mba_fee_data = $this->Im->get_fee(array(
				'payment_type_code' => '01',
				'academic_year_id' => $a_student_data['academic_year_id'],
				// 'program_id' => $a_student_data['program_id']
			));
			
			if(!$mba_fee_data){
				$this->db->trans_rollback();
				$a_return = array('code' => 1, 'message' => 'Payment data not found, Rolling back transaction!');   
			}
			else{
				$s_year_pad = substr($s_academic_year_id, 2);
				$s_study_program_id = (isset($a_student_data['study_program_id'])) ? $a_student_data['study_program_id'] : '';
				$mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $s_study_program_id]);
				$s_prodi_code = ($mba_study_program_data) ? $mba_study_program_data[0]->study_program_code : '00';
				$mbs_va_number = $this->Bm->generate_va_number(
					$mba_fee_data[0]->payment_type_code,
					'candidate',
					$s_year_pad.$s_prodi_code,
					$s_academic_year_id,
					($a_student_data['program_id'] !== null) ? $a_student_data['program_id'] : 1
				);
				
				if($mbs_va_number){
					$a_billing_data = array(
						'trx_amount' => $mba_fee_data[0]->fee_amount,
						'billing_type' => 'c',
						'customer_name' => $a_personal_data['personal_data_name'],
						'virtual_account' => $mbs_va_number,
						'description' => $mba_fee_data[0]->fee_description,
						'datetime_expired' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+12 month")),
						'customer_email' => 'bni.employee@company.ac.id'
					);
					
					$a_return_billing_data = $this->Bm->create_billing($a_billing_data);
					
					if($a_return_billing_data['status'] === '000'){
						$a_invoice_data = array(
							'personal_data_id' => $a_personal_data['personal_data_id'],
							'invoice_number' => $this->Im->get_invoice_number($mba_fee_data[0]->payment_type_code),
							'invoice_description' => $mba_fee_data[0]->fee_description,
							'invoice_allow_fine' => 'no',
							'invoice_allow_reminder' => 'no',
							'academic_year_id' => $s_academic_year_id,
							'semester_type_id' => 1
						);
						$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
						
						$a_invoice_details_data = array(
							'invoice_id' => $s_invoice_id,
							'fee_id' => $mba_fee_data[0]->fee_id,
							'invoice_details_amount' => $mba_fee_data[0]->fee_amount,
							'invoice_details_amount_number_type' => $mba_fee_data[0]->fee_amount_number_type,
							'invoice_details_amount_sign_type' => $mba_fee_data[0]->fee_amount_sign_type
						);
						$this->Im->create_invoice_details($a_invoice_details_data);
						
						$a_sub_invoice_data = array(
							'sub_invoice_amount' => $mba_fee_data[0]->fee_amount,
							'sub_invoice_amount_total' => $mba_fee_data[0]->fee_amount,
							'invoice_id' => $s_invoice_id
						);
						$s_sub_invoice_id = $this->Im->create_sub_invoice($a_sub_invoice_data);

						$a_sub_invoice_details = array(
							'trx_id' => $a_return_billing_data['trx_id'],
							'sub_invoice_id' => $s_sub_invoice_id,
							'sub_invoice_details_amount' => $mba_fee_data[0]->fee_amount,
							'sub_invoice_details_amount_total' => $mba_fee_data[0]->fee_amount,
							'sub_invoice_details_va_number' => $mbs_va_number,
							'sub_invoice_details_description' => $mba_fee_data[0]->fee_description,
							'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+10 month")),
							'sub_invoice_details_real_datetime_deadline' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+10 month"))
						);
						$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details);
	
						$send_email_enrollment_va = Modules::run(
							'admission/send_enrollment_va', 
							$a_personal_data,
							$mbs_va_number,
							"Rp. ".number_format($mba_fee_data[0]->fee_amount, 0, ",", "."),
							true,
							$b_scholarship
						);
						
						if($send_email_enrollment_va){
							$a_return = array('code' => 0, 'va_number' => $mbs_va_number);
						}
						else{
							$a_return = array('code' => 1, 'message' => 'Failed to send enrollment email');
						}
					}
					else{
						$a_return = array('code' => $a_return_billing_data['status'], 'message' => $a_return_billing_data['message'].": ".$mbs_va_number);
	
						$a_body = [
							'personal_data_id' => $a_personal_data['personal_data_id'],
							'date' => date('Y-m-d H:i:s'),
							'data' => $a_return,
							'data_send' => $a_return_billing_data
						];
	
						$this->email->from('employee@company.ac.id');
						$this->email->to(array('employee@company.ac.id'));
						$this->email->subject('ERROR Create Enrollment Fees!');
						$this->email->message(json_encode($a_body));
						$this->email->send();
					}
				}
				else{
					$a_return = array('code' => 2, 'message' => 'Please contact IT Dept');
				}
			}
		// }
		$a_return = array('code' => 0, 'message' => 'Success');

		return $a_return;
	}

	public function push_receive_student_data()
	{
		$a_post_data = $this->a_api_data;

		$a_personal_data = $a_post_data['personal_data'];
		$a_student_data = $a_post_data['student_data'];
		$a_family_data = $a_post_data['family_data'];
		$o_active_year = $this->Adm->get_active_intake_year();

		$mbo_student = $this->Student->get_student_by_id($a_student_data['student_id']);

		if($mbo_student){
			$this->Student->update_student_data($a_student_data, $a_student_data['student_id']);
			$a_return = array('code' => 0);
			$this->return_json($a_return);
		}
		else{
			$a_student_data = (array) $a_student_data;

			$this->Student->create_new_student($a_student_data);
			
			$mba_academic_year_data = $this->General->get_batch($a_student_data['academic_year_id']);
			if($mba_academic_year_data){
				$i_academic_year_candidate_counter = $mba_academic_year_data[0]->academic_year_candidates_counter;
				$i_academic_year_candidate_counter++;
				$this->Adm->update_academic_year_data($a_student_data['academic_year_id'], array('academic_year_candidates_counter' => $i_academic_year_candidate_counter));
			}

			// enrollment email starts here

			$mba_fee_data = $this->Im->get_fee(array('payment_type_code' => '01', 'academic_year_id' => $a_student_data['academic_year_id']));
			if(!$mba_fee_data){
				$this->db->trans_rollback();
				$a_return = array('code' => 1, 'message' => 'Payment data not found, Rolling back transaction!');   
			}
			else{
				$mbs_va_number = $this->Bm->get_va_number(
					$mba_fee_data[0]->payment_type_code,
					0,
					0,
					'candidate',
					null,
					$mba_academic_year_data[0]->academic_year_id,
					($a_student_data['program_id'] !== null) ? $a_student_data['program_id'] : 1
				);
				
				if($mbs_va_number){
					$a_invoice_data = array(
						'personal_data_id' => $a_personal_data['personal_data_id'],
						'invoice_number' => $this->Im->get_invoice_number($mba_fee_data[0]->payment_type_code),
						'invoice_description' => $mba_fee_data[0]->fee_description,
						'invoice_allow_fine' => 'no',
						'invoice_allow_reminder' => 'no'
					);
					$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
					
					$a_invoice_details_data = array(
						'invoice_id' => $s_invoice_id,
						'fee_id' => $mba_fee_data[0]->fee_id,
						'invoice_details_amount' => $mba_fee_data[0]->fee_amount,
						'invoice_details_amount_number_type' => $mba_fee_data[0]->fee_amount_number_type,
						'invoice_details_amount_sign_type' => $mba_fee_data[0]->fee_amount_sign_type
					);
					$this->Im->create_invoice_details($a_invoice_details_data);
					
					$a_sub_invoice_data = array(
						'sub_invoice_amount' => $mba_fee_data[0]->fee_amount,
						'invoice_id' => $s_invoice_id
					);
					$s_sub_invoice_id = $this->Im->create_sub_invoice($a_sub_invoice_data);
					
					$a_billing_data = array(
						'trx_amount' => $mba_fee_data[0]->fee_amount,
						'billing_type' => 'c',
						'customer_name' => $a_personal_data['personal_data_name'],
						'virtual_account' => $mbs_va_number,
						'description' => $mba_fee_data[0]->fee_description,
						'datetime_expired' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+3 month")),
						'customer_email' => 'bni.employee@company.ac.id'
					);
					
					$a_return_billing_data = $this->Bm->create_billing($a_billing_data);
					
					if($a_return_billing_data['status'] === '000'){
						$a_sub_invoice_details = array(
							'trx_id' => $a_return_billing_data['trx_id'],
							'sub_invoice_id' => $s_sub_invoice_id,
							'sub_invoice_details_amount' => $mba_fee_data[0]->fee_amount,
							'sub_invoice_details_amount_total' => $mba_fee_data[0]->fee_amount,
							'sub_invoice_details_va_number' => $mbs_va_number,
							'sub_invoice_details_description' => $mba_fee_data[0]->fee_description,
							'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+2 month")),
							'sub_invoice_details_real_datetime_deadline' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+2 month"))
						);
						$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details);

						$send_email_enrollment_va = Modules::run(
							'admission/send_enrollment_va', 
							$a_personal_data,
							$mbs_va_number,
							"Rp. ".number_format($mba_fee_data[0]->fee_amount, 0, ",", "."),
							true
						);
						
						if($send_email_enrollment_va){
							$a_return = array('code' => 0, 'va_number' => $mbs_va_number);
						}
						else{
							$a_return = array('code' => 1, 'message' => 'Failed to send enrollment email');
						}
					}
					else{
						$a_return = array('code' => $a_return_billing_data['status'], 'message' => $a_return_billing_data['message'].": ".$mbs_va_number);

						$a_body = [
							'personal_data_id' => $a_personal_data['personal_data_id'],
							'date' => date('Y-m-d H:i:s'),
							'data' => $a_return
						];
						$this->email->from('employee@company.ac.id');
						$this->email->to(array('employee@company.ac.id'));
						$this->email->subject('ERROR Create Enrollment Fee in receive function!');
						$this->email->message(json_encode($a_body));
						$this->email->send();
					}
				}
				else{
					$a_return = array('code' => 2, 'message' => 'Please contact IT Dept');
				}
			}


		}

		// $a_return = ['code' => 0, 'data' => $s_json];
		$this->return_json($a_return);
	}

	// public function push_enrollment_candidate($s_student_id)
	// {
	// 	$a_student_data = $this->Student->get_student_by_id($s_student_id);
	// 	if ($a_student_data) {
	// 		$a_personal_data = $this->Personal_data->get_personal_data_by_id($a_student_data->personal_data_id);
	// 		$a_student_data = (array)$a_student_data;
	// 		$a_personal_data = (array)$a_personal_data;
	// 		$send = $this->create_invoice_enrollment($a_student_data, $a_personal_data, $a_student_data['academic_year_id']);
	// 		print('<pre>');
	// 		var_dump($send);
	// 	}else{
	// 		print('zoonk');
	// 	}
	// }

	// public function create_invoice_enrollment($a_student_data, $a_personal_data, $mbs_academic_year_id)
	// {
	// 	$mba_fee_data = $this->Im->get_fee(array('payment_type_code' => '01', 'academic_year_id' => $a_student_data['academic_year_id']));

	// 	if(!$mba_fee_data){
	// 		$a_return = array('code' => 1, 'message' => 'Payment data not found, Rolling back transaction!');   
	// 	}
	// 	else{
	// 		$mbs_va_number = $this->Bm->get_va_number(
	// 			$mba_fee_data[0]->payment_type_code,
	// 			0,
	// 			0,
	// 			'candidate',
	// 			null,
	// 			$mbs_academic_year_id,
	// 			($a_student_data['program_id'] !== null) ? $a_student_data['program_id'] : 1
	// 		);
			
	// 		if($mbs_va_number){
	// 			$a_invoice_data = array(
	// 				'personal_data_id' => $a_student_data['personal_data_id'],
	// 				'invoice_number' => $this->Im->get_invoice_number($mba_fee_data[0]->payment_type_code),
	// 				'invoice_description' => $mba_fee_data[0]->fee_description,
	// 				'invoice_allow_fine' => 'no',
	// 				'invoice_allow_reminder' => 'no'
	// 			);
	// 			$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
				
	// 			$a_invoice_details_data = array(
	// 				'invoice_id' => $s_invoice_id,
	// 				'fee_id' => $mba_fee_data[0]->fee_id,
	// 				'invoice_details_amount' => $mba_fee_data[0]->fee_amount,
	// 				'invoice_details_amount_number_type' => $mba_fee_data[0]->fee_amount_number_type,
	// 				'invoice_details_amount_sign_type' => $mba_fee_data[0]->fee_amount_sign_type
	// 			);
	// 			$this->Im->create_invoice_details($a_invoice_details_data);
				
	// 			$a_sub_invoice_data = array(
	// 				'sub_invoice_amount' => $mba_fee_data[0]->fee_amount,
	// 				'invoice_id' => $s_invoice_id
	// 			);
				
	// 			$s_sub_invoice_id = $this->Im->create_sub_invoice($a_sub_invoice_data);
				
	// 			$a_billing_data = array(
	// 				'trx_amount' => $mba_fee_data[0]->fee_amount,
	// 				'billing_type' => 'c',
	// 				'customer_name' => $a_personal_data['personal_data_name'],
	// 				'virtual_account' => $mbs_va_number,
	// 				'description' => $mba_fee_data[0]->fee_description,
	// 				'datetime_expired' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+3 month")),
	// 				'customer_email' => 'bni.employee@company.ac.id'
	// 			);
				
	// 			$a_return_billing_data = $this->Bm->create_billing($a_billing_data);
				
	// 			if($a_return_billing_data['status'] === '000'){
	// 				$a_sub_invoice_details = array(
	// 					'trx_id' => $a_return_billing_data['trx_id'],
	// 					'sub_invoice_id' => $s_sub_invoice_id,
	// 					'sub_invoice_details_amount' => $mba_fee_data[0]->fee_amount,
	// 					'sub_invoice_details_amount_total' => $mba_fee_data[0]->fee_amount,
	// 					'sub_invoice_details_va_number' => $mbs_va_number,
	// 					'sub_invoice_details_description' => $mba_fee_data[0]->fee_description,
	// 					'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+2 month")),
	// 					'sub_invoice_details_real_datetime_deadline' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+2 month"))
	// 				);
	// 				$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details);

	// 				$send_email_enrollment_va = Modules::run(
	// 					'admission/send_enrollment_va', 
	// 					$a_personal_data,
	// 					$mbs_va_number,
	// 					"Rp. ".number_format($mba_fee_data[0]->fee_amount, 0, ",", "."),
	// 					true
	// 				);
					
	// 				if($send_email_enrollment_va){
	// 					$a_return = array('code' => 0, 'va_number' => $mbs_va_number);
	// 				}
	// 				else{
	// 					$a_return = array('code' => 1, 'message' => 'Failed to send enrollment email');
	// 				}
	// 			}
	// 			else{
	// 				$a_return = array('code' => $a_return_billing_data['status'], 'message' => $a_return_billing_data['message'].": ".$mbs_va_number);
	// 			}
	// 		}
	// 		else{
	// 			$a_return = array('code' => 2, 'message' => 'Please contact IS&T Dept');
	// 		}
	// 	}

	// 	return $a_return;
	// }
}