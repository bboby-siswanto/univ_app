<?php
class Auth extends App_core
{
	public $a_advisor_examiner_account = [];
	public function __construct()
	{
        parent::__construct();
		$this->load->model('Auth_model');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('employee/Employee_model', 'Epm');
        $this->load->model('student/Student_model', 'Stm');
	}

	function reset_password($s_token = null) {
		if ($s_token != null) {
			$this->load->library('IULI_Encryption');
            $a_token = $this->iuli_encryption->parse_data(urldecode($s_token), 'IULIACID');
			// print('<pre>');var_dump($a_token);exit;
			if (is_null($a_token)) {
				$this->a_page_data['body'] = '<h2>Sorry</h2><p></p><p>invalid URL</p>';
			}
			else {
				// $s_keyuser = $a_token[0];
				$s_mailuser = $a_token[1];
				$s_tokenuser = $a_token[2];
				$mbo_token_already_exists = $this->General->get_where('dt_personal_data', array('personal_data_password_token' => $s_tokenuser));
				if ($mbo_token_already_exists) {
					$mbo_token_already_exists = $mbo_token_already_exists[0];
					if ($mbo_token_already_exists->personal_data_password_token_expired === NULL) {
						$this->a_page_data['body'] = '<h2>Sorry</h2><p></p><p>Your token is invalids</p>';
					}
					else{
						$time_now = new DateTime();
						$time_now = date_format($time_now, 'Y-m-d H:i');
						$time_nows = strtotime($time_now);
						
						$time_expired = new DateTime($mbo_token_already_exists->personal_data_password_token_expired);
						$time_expired = date_format($time_expired, 'Y-m-d H:i');
						$time_expireds = strtotime($time_expired);

						if ($time_nows <= $time_expireds) {
							// $data['data_personal'] = $mbo_token_already_exists;
							// $data['pages'] = 'registration/reset_password';
							$this->a_page_data['uid'] = $mbo_token_already_exists->personal_data_id;
							$this->a_page_data['iulimail'] = $s_mailuser;
							$this->a_page_data['body'] = $this->load->view('form/form_input_password', $this->a_page_data, true);
						}
						else{
							$this->a_page_data['body'] = '<h2>Sorry</h2><p></p><p>Your password reset token has expired.</p>';
						}
					}
				}
				else{
					$this->a_page_data['body'] = '<h2>Sorry</h2><p></p><p>Your token is invalid</p>';
				}
			}
        }
        else{
            $this->a_page_data['body'] = '<p>Token not found.</p>';
        }
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function forget_password_form()
	{
		$this->load->view('form/forget_password_form');
	}
	
	public function login_form()
	{
		$this->load->view('form/login_form');
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		redirect(site_url('auth/login'));
	}
	
	function forget_password() {
		if ($this->input->is_ajax_request()) {
			$i_count_forget_password = (($this->session->has_userdata('counter_forget_password'))) ? $this->session->userdata('counter_forget_password') : 0;
			if (($this->session->has_userdata('counter_forget_password')) AND ($this->session->userdata('counter_forget_password')) > 3) {} else {
				$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
				if ($this->form_validation->run()) {
					$email = set_value('email');

					$is_student = $this->General->get_where('dt_student', ['student_email' => $email]);
					$is_employee = $this->General->get_where('dt_employee', ['employee_email' => $email]);
					
					if ($is_student OR $is_employee) {
						$s_key = '';
						if ($is_student) {
							$s_key = 'dt_student';
						}
						else if ($is_employee) {
							$s_key = 'dt_employee';
						}
						$s_personal_data_id = ($is_student) ? $is_student[0]->personal_data_id : $is_employee[0]->personal_data_id;
						$userdata = $this->General->get_where('dt_personal_data', array('personal_data_id' => $s_personal_data_id));
						if ($userdata) {
							$o_userdata = $userdata[0];
							if (!is_null($o_userdata->personal_data_email)) {
								$this->load->library('IULI_Ldap');
								$mba_ldap_data = $this->iuli_ldap->uid_search($email);
								if ($mba_ldap_data) {
									$token = md5(time());
									$datetime = new DateTime();
									$datetime->modify('+2 hours');
									$time_expire = date_format($datetime, 'Y-m-d H:i:s');
									$dataToken = array(
										'personal_data_password_token' => $token,
										'personal_data_password_token_expired' => $time_expire
									);
									$this->General->update_data('dt_personal_data', $dataToken, ['personal_data_id' => $s_personal_data_id]);
									$userdata = $this->General->get_where('dt_personal_data', array('personal_data_id' => $s_personal_data_id));
									$o_userdata = $userdata[0];
									$a_token = [$s_key, $email, $token];
									$this->load->library('IULI_Encryption');
									$s_token = $this->iuli_encryption->hash_data($a_token, '');
									$link = site_url('auth/reset_password/'.urlencode($s_token));
									$this->send_email_forget_password($o_userdata, $link);
									$a_return = array('code' => 0, 'message' => 'Success!', 'email_result' => $o_userdata->personal_data_email);
								}
								else {
									$i_count_forget_password++;
									$a_return = array('code' => 1, 'message' => 'Access not found!', 'counter' => $i_count_forget_password);
								}
							}
							else {
								// $i_count_forget_password++;
								$a_return = array('code' => 1, 'message' => 'Account does not contain a personal email, please contact IT Department for change the password!', 'counter' => $i_count_forget_password);
							}
						}
						else {
							$i_count_forget_password++;
							$a_return = array('code' => 1, 'message' => 'Access not allowed!', 'counter' => $i_count_forget_password);
						}
					}
					else {
						$i_count_forget_password++;
						$a_return = array('code' => 1, 'message' => 'Email not found!', 'counter' => $i_count_forget_password);
					}
				}
				else {
					$a_return = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
				}

				$this->session->set_userdata('counter_forget_password', $i_count_forget_password);
				print json_encode($a_return);exit;
			}
		}
	}
    
	function get_test() {
		$this->load->library('IULI_Ldap');
		$return_ldap = $this->iuli_ldap->ldap_login('employee@company.ac.id', 'localakun');
		var_dump($return_ldap);exit;
	}

	public function create_password()
	{
		print('<pre>');
		$this->load->library('IULI_Ldap');
		$passwordhash = $this->iuli_ldap->create_password('localakun');
		var_dump($passwordhash);exit;
	}

	function submit_new_password() {
		if ($this->input->is_ajax_request()) {
			$s_iulimail = $this->input->post('iulimail');
			$s_password1 = $this->input->post('password1');
			$s_password2 = $this->input->post('password2');

			$mba_personal_data = false;
			$is_student = $this->General->get_where('dt_student', ['student_email' => $s_iulimail]);
			$is_employee = $this->General->get_where('dt_employee', ['employee_email' => $s_iulimail]);
			if ($is_student) {
				$mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $is_student[0]->personal_data_id]);
			}
			else if ($is_employee) {
				$mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $is_employee[0]->personal_data_id]);
			}

			if (($this->session->has_userdata('counter_forget_password')) AND ($this->session->userdata('counter_forget_password')) > 3) {
				$a_return = ['code' => 2, 'message' => 'Cannot submit right now, please try again later!'];
			}
			else if ((strlen($s_password1) < 8) OR (strlen($s_password2) < 8)) {
				$a_return = ['code' => 2, 'message' => 'Minimum password 8 character!'];
			}
			else if (($s_password1 !== $s_password2)) {
				$a_return = ['code' => 2, 'message' => 'Make sure you repeat the password correctly!'];
			}
			else if ($mba_personal_data) {
				$this->load->library('IULI_Ldap');
				$mba_ldap_result = $this->iuli_ldap->updatepassword($s_iulimail, $s_password1);
				if ($mba_ldap_result == 1) {
					$a_return = ['code' => 0, 'message' => 'Success'];
				}
				else {
					$a_return = ['code' => 1, 'message' => 'Cannot submit password right now, please try again later'];
				}
			}
			else {
				$a_return = ['code' => 1, 'message' => 'Invalid request!'];
			}

			print json_encode($a_return);exit;
		}
	}

	public function login()
	{
		// return false;
		if($this->input->is_ajax_request()) {
            // $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('email', 'Email', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');
			
			if($this->form_validation->run()){
				$this->load->library('IULI_Ldap');
				$mba_ldap_login = $this->iuli_ldap->ldap_login(set_value('email'), set_value('password'));
				if($mba_ldap_login['code'] == 0) {
					$a_auth_session = array(
						'auth' => true,
						'type' => $mba_ldap_login['type']
					);
					$a_auth_session['environment'] = 'production';
					$mbo_profile_data = $mba_ldap_login['personal'];
					if($mbo_profile_data){
						$mbo_personal_data = $this->Pdm->get_personal_data_by_id($mbo_profile_data->personal_data_id);
						if ($mbo_personal_data) {
							$this->load->model('academic/Semester_model', 'Smm');
							$o_semester_active = $this->Smm->get_active_semester();
							if ($o_semester_active) {
								$s_redirect_uri = (!is_null($this->session->userdata('url'))) ? $this->session->userdata('url') : site_url('user/profile');
								if (($mba_ldap_login['type'] == 'student') OR ($mba_ldap_login['type'] == 'alumni')) {
									$this->session->set_userdata('module', 'student_academic');
									$this->session->set_userdata('student_status', $mbo_profile_data->student_status);
									if ($mbo_profile_data->student_status == 'graduated') {
										$a_student_alumni_data = [
											'alumni_id' => $this->uuid->v4(),
											'student_id' => $mbo_profile_data->student_id,
											'alumni_fullname' => $mbo_personal_data->personal_data_name,
											'alumni_nickname' => $mbo_personal_data->personal_data_name,
											'alumni_date_of_birth' => $mbo_personal_data->personal_data_date_of_birth,
											'alumni_place_of_birth' => $mbo_personal_data->personal_data_place_of_birth,
											'alumni_personal_email' => $mbo_personal_data->personal_data_email,
											'alumni_personal_cellular' => $mbo_personal_data->personal_data_cellular,
											'alumni_gender' => $mbo_personal_data->personal_data_gender,
											'alumni_marital_status' => $mbo_personal_data->personal_data_marital_status,
										];
										
										$mba_student_alumni_data = $this->General->get_where('dt_student_alumni', ['student_id' => $mbo_profile_data->student_id]);
										if (!$mba_student_alumni_data) {
											$this->Stm->save_student_alumni($a_student_alumni_data);
											$this->session->set_userdata('has_working', 'no');
										}else if ($mba_student_alumni_data[0]->alumni_has_filled_job == 'no') {
											$this->session->set_userdata('has_working', 'no');
										}

										$mba_approve_semester = $this->General->get_thesis_subject($mbo_profile_data->student_id);
										$a_auth_session['allowed_proposed_thesis'] = $mba_approve_semester;

										$this->session->set_userdata('alumni_required', $this->validate_alumni_tracer($mbo_profile_data->personal_data_id));
										$s_redirect_uri = site_url('personal_data/alumni_profile');
									}else{
										$mba_approve_semester = $this->General->get_where('dt_student_semester', [
											'student_id' => $mbo_profile_data->student_id,
											'semester_id' => 8
										]);

										if ($mbo_profile_data->student_status == 'inactive') {
											$a_auth_session['message_academic'] = true;
										}
										$mba_approve_semester = $this->General->get_thesis_subject($mbo_profile_data->student_id);
										// $mba_current_semester = $this->Smm->get_student_semester_number($mbo_profile_data->student_id, [
										// 	'ss.academic_year_id' => $o_semester_active->academic_year_id,
										// 	'ss.semester_type_id' => $o_semester_active->semester_type_id
										// ]);

										$a_auth_session['allowed_proposed_thesis'] = $mba_approve_semester;
										$mbo_student_detail_data = $this->Stm->get_student_filtered(array('ds.student_id' => $mbo_profile_data->student_id))[0];
										$this->session->set_userdata('dikti_required', $this->validate_dikti_required($mbo_student_detail_data));
										$s_redirect_uri = site_url('module/set/student_academic');
									}
								}
								else if ($mba_ldap_login['type'] == 'candidate') {
									$s_redirect_uri = site_url('admission/candidate/personal_data');
								}
								else if ($mba_ldap_login['type'] == 'staff') {
									$s_redirect_uri = site_url('module/set/academic');
								}
								else if ($mba_ldap_login['type'] == 'lect') {
									$s_redirect_uri = site_url('module/set/academic');
								}
								else if ($mba_ldap_login['type'] == 'lecturer') {
									$s_redirect_uri = site_url('module/set/academic');
								}
								else if ($mba_ldap_login['type'] == 'guest') {
									$s_redirect_uri = site_url('module/set/academic');
									// $s_redirect_uri = site_url('thesis/thesis_defense');
								}
								else if ($mba_ldap_login['type'] == 'examiner') {
									$s_redirect_uri = site_url('thesis/thesis_defense');
								}
								else if ($mba_ldap_login['type'] == 'advisor') {
									$s_redirect_uri = site_url('thesis/thesis_defense');
								}
								
								$a_auth_session['user'] = $mbo_profile_data->personal_data_id;
								$a_auth_session['name'] = $mbo_personal_data->personal_data_name;
								$a_auth_session['module'] = 'profile';
								$a_auth_session['academic_year_id_active'] = $o_semester_active->academic_year_id;
								$a_auth_session['semester_type_id_active'] = $o_semester_active->semester_type_id;

								$this->session->set_userdata($a_auth_session);
								$a_return = array('code' => 0, 'redirect_uri' => $s_redirect_uri, 'custom_message' => 'Siapa pun yang membaca ini, selamat anda bisa baca!!!!');
							}
							else {
								$a_return = array('code' => 1, 'message' => 'No semester active found');
							}
						}else{
							$a_return = array('code' => 1, 'message' => 'Error System');
							$this->log_activity(json_encode($a_return));
						}
					}
					else{
						$a_return = array('code' => 11, 'type' => $mba_ldap_login['type'], 'message' => "Wrong Email / Password!", 'result' => $mba_ldap_login);
						$this->log_activity(json_encode($a_return));
					}
				}
				else{
					$this->log_activity(json_encode($mba_ldap_login));
					$a_return = array('code' => 2, 'ldap_code' => $mba_ldap_login['code'], 'message' => '<span>'.$mba_ldap_login['message'].'</span><br>', 'ldap_data' => $mba_ldap_login);
				}
			}
			else{
				$a_return = array('code' => 3, 'message' => validation_errors('<span>', '</span><br>'));
				$this->log_activity(json_encode($a_return));
			}
			
			print json_encode($a_return);
			exit;
		}
		else{
			if($this->session->userdata('auth')){
				if (!$this->session->has_userdata('url')) {
					redirect(site_url());
				}
				else {
					redirect($this->session->userdata('url'));
				}
			}
			$this->a_page_data['body'] = $this->load->view('login', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}
	}

	private function send_email_forget_password($userData, $link)
	{
		$emailBody = <<<TEXT
Dear {$userData->personal_data_name},
 
We have received your request to reset IULI account password.
Please click the link below to reset your account password.
 
{$link}
   
This is a one-time email. You have received it because you request to reset your account password in International University Liaison Indonesia - IULI. Ignore this email and contact IT Department if you never ask to reset the password.
The link above will expired then 2 hours of this email being sent.

Best Regards,

International University Liaison Indonesia
Associate Tower 7th Floor.
Intermark Indonesia BSD
Jl. Lingkar Timur BSD Serpong
Tangerang Selatan 15310
Phone: +62 (0) 852 123 18000
Email: employee@company.ac.id
TEXT;

		$this->email->from('employee@company.ac.id', 'IULI Account');
		$this->email->to($userData->personal_data_email);
		$this->email->bcc('employee@company.ac.id');
		$this->email->subject('[CONFIRMATION RESET PASSWORD] IULI Account');
		$this->email->message($emailBody);
		
		if($this->email->send()){
			return true;
		}
		else{
			return $this->email->print_debugger();
		}
    }

	private function _handle_candidate($s_email, $s_password)
	{
		$this->load->model('student/Candidate_model', 'Mc');

		$a_email = explode('@', $s_email);

		$mbs_is_iulimail = strpos($a_email[1], "iuli");
		if ($mbs_is_iulimail) {
			return false;
		}
		else {
			$mba_userdata = $this->Mc->get_candidate_data(['personal_data_email' => $s_email]);
			if ($mba_userdata) {
				$o_usercandidate = $mba_userdata[0];
				if (password_verify($s_password, $o_usercandidate->personal_data_password)) {
					$mba_ldap_login = array(
						'code' => 0,
						'type' => 'candidate',
						'message' => 'Sukses'
					);
				}
				else {
					$mba_ldap_login = array(
						'code' => 1,
						'type' => 'candidate',
						'message' => 'Wrong Password'
					);
				}

				return $mba_ldap_login;
			}
			else {
				return false;
			}
		}
	}

	public function validate_alumni_tracer($s_personal_data_id)
	{
		$mba_alumni_has_filled_tracer_study = $this->General->get_where('dikti_question_answers', ['personal_data_id' => $s_personal_data_id]);
		return ($mba_alumni_has_filled_tracer_study) ? true : false;
	}

	public function validate_dikti_required($o_student_data)
	{
		if ((is_null($o_student_data->personal_data_mother_maiden_name)) OR ($o_student_data->personal_data_mother_maiden_name == '')) {
			return false;
		}
		else if ((is_null($o_student_data->personal_data_id_card_number)) OR ($o_student_data->personal_data_id_card_number == '')) {
			return false;
		}
		else if ((is_null($o_student_data->personal_data_place_of_birth)) OR ($o_student_data->personal_data_place_of_birth == '')) {
			return false;
		}
		else if ((is_null($o_student_data->personal_data_date_of_birth)) OR ($o_student_data->personal_data_date_of_birth == '')) {
			return false;
		}
		else if ((is_null($o_student_data->citizenship_id)) OR ($o_student_data->citizenship_id == '')) {
			return false;
		}
		else if ((is_null($o_student_data->address_sub_district)) OR ($o_student_data->address_sub_district == '')) {
			return false;
		}
		else if ((is_null($o_student_data->dikti_wilayah_id)) OR ($o_student_data->dikti_wilayah_id == '')) {
			return false;
		}
		else{
			return true;
		}
	}

	private function _validate_requirement($s_personal_data_id, $validation_type = 'vaccine_confirmation')
	{
		$has_validate = $this->General->get_where('ref_confirm_validation', [
			'personal_data_id' => $s_personal_data_id,
			$validation_type => 'confirmed'
		]);

		return ($has_validate) ? true : false;
	}

	private function _show_vote($s_student_id, $s_personal_data_id)
	{
		$select_data = $this->General->get_where('vote_period', [
			'period_status' => 'active'
		]);

		$vote = false;

		if ($select_data) {
			if (!is_null($select_data[0]->study_program_id)) {
				$mba_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id]);
				if ($select_data[0]->study_program_id == $mba_student_data[0]->study_program_id) {
					$vote = true;
				}
			}
			else {
				$vote = true;
			}

			if ($vote) {
				$has_voting = $this->General->get_where('vote_voting', [
					'student_id' => $s_student_id,
					'period_id' => $select_data[0]->period_id,
					'has_pick' => 'yes'
				]);
	
				if ($has_voting) {
					return false;
				}
				else {
					$a_access_kpu_vote = $this->config->item('bem_member')['kpu'];
					return (in_array($s_personal_data_id, $a_access_kpu_vote)) ? false : true;
				}
			}

			// return ($has_voting) ? false : true;
		}
		else {
			return false;
		}
	}
}