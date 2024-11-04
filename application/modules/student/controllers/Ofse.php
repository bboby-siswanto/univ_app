<?php
class Ofse extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('student/Student_model', 'Stm');
		$this->load->model('academic/Semester_model', 'Sem');
		$this->load->model('academic/Score_model', 'Scm');
		$this->load->model('academic/Offered_subject_model', 'Osm');
		$this->load->model('academic/Curriculum_model', 'Crm');
		$this->load->model('personal_data/Family_model', 'Fm');
		$this->load->model('academic/Ofse_model', 'Ofm');
	}

	public function view($s_subject_question_id, $s_ofse_subject_code)
	{
		$mba_subject_question = $this->General->get_where('dt_ofse_subject_question', ['subject_question_id' => $s_subject_question_id]);
		if ($mba_subject_question) {
			$mba_ofse_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $mba_subject_question[0]->ofse_period_id]);
			if ($mba_ofse_data) {
				$s_ofse_name = str_replace(' ', '-', $mba_ofse_data[0]->ofse_period_name);
				$s_file_path = APPPATH.'uploads/academic/ofse/'.$s_ofse_name.'/question_list/'.$s_ofse_subject_code.'/'.$mba_subject_question[0]->subject_fname;
				if(file_exists($s_file_path)) {
					$mime = mime_content_type($s_file_path);
					header("Content-Type: ".$mime);
					readfile( $s_file_path );exit;
				}
			}
		}
	}

	public function question($s_ofse_period_id, $s_ofse_subject_question_id, $s_ofse_subject_code)
    {
        $mba_ofse_question = $this->Ofm->get_ofse_subject_question([
            'oq.ofse_subject_code' => $s_ofse_subject_code,
			'oq.subject_question_id' => $s_ofse_subject_question_id,
            'oq.ofse_period_id' => $s_ofse_period_id
        ]);

        $s_path = false;
        if ($mba_ofse_question) {
			$mba_ofse_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id]);
			if ($mba_ofse_data) {
				$s_ofse_name = str_replace(' ', '-', $mba_ofse_data[0]->ofse_period_name);
				$s_path = APPPATH.'uploads/academic/ofse/'.$s_ofse_name.'/question_list/'.$s_ofse_subject_code.'/'.$mba_ofse_question[0]->subject_fname;
			}
        }

		if(($s_path) AND (file_exists($s_path))) {
			$mime = mime_content_type($s_path);
			header("Content-Type: ".$mime);
			// header('Content-Disposition: attachment; filename='.urlencode($s_file));
			// print($s_path);exit;
			readfile( $s_path );exit;
		}
		else {
			show_404();
		}
        // $this->a_page_data['body'] = $this->load->view('ofse/exam_question', $this->a_page_data, true);
        // $this->load->view('layout', $this->a_page_data);
    }

	public function exam()
	{
		$mba_is_member = $this->Ofm->get_ofse_student_member([
			'sc.student_id' => $this->session->userdata('student_id'),
			'do.ofse_status' => 'active'
		]);

		if ($mba_is_member) {
			$this->a_page_data['ofse_data'] = $mba_is_member[0];
			$this->a_page_data['body'] = $this->load->view('ofse/ofse_subject_list', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}
		else {
			redirect('module/set/student_academic');
		}
	}

	public function set_log_test()
	{
		// print(__LINE__);
		print(__FILE__);
		log_message('error', 'ERROR from '.__FILE__.' '.__LINE__);
		// trigger_error("Error message here", E_USER_ERROR);
		// print($ada);
	}
	
	public function registration($force_registration = 'false')
	{
		$o_semester_active = $this->Sem->get_active_semester();
		$this->a_page_data['valid_registration'] = modules::run('academic/semester/checker_semester_academic', 'study_plan_ofse_start_date', 'study_plan_ofse_end_date', $o_semester_active->academic_year_id, $o_semester_active->semester_type_id);
		// redirect(site_url('personal_data/profile'));

		$b_allow_registration = false;
		if ($this->a_page_data['valid_registration']) {
			$b_allow_registration = true;
		}
		// else if(in_array($this->session->userdata('student_id'), ['92201f16-a926-4ae7-a99c-4696d0e63e2a'])) {
		// 	$b_allow_registration = true;
		// }

		if ($b_allow_registration) {
			$mba_score_data = $this->Scm->get_score_student($this->session->userdata('student_id'), array(
				'sc.academic_year_id' => $o_semester_active->academic_year_id,
				'sc.semester_type_id' => ($o_semester_active->semester_type_id == 2) ? 6 : 4,
				'sc.semester_id' => 17
			));
			
			if (($mba_score_data) AND ($force_registration !== 'true')) {
				// if ($_SERVER['REMOTE_ADDR'] == '202.93.225.254') {
					foreach ($mba_score_data as $o_score) {
						$mba_offered_subject_data = $this->General->get_where('dt_offered_subject', [
							'curriculum_subject_id' => $o_score->curriculum_subject_id,
							'study_program_id' => $o_score->study_program_id,
							'academic_year_id' => $o_semester_active->academic_year_id,
							'semester_type_id' => ($o_semester_active->semester_type_id == 2) ? 6 : 4
						]);
						$o_score->ofse_subject_type = ($mba_offered_subject_data) ? $mba_offered_subject_data[0]->ofse_status : '';
					}
					$this->a_page_data['ofse_data'] = $mba_score_data;
					$this->a_page_data['body'] = $this->load->view('ofse/after_registration', $this->a_page_data, true);
					$this->load->view('layout', $this->a_page_data);
				// }
			}
			else {
				echo modules::run('student/ofse_registration');
			}
		}
		else {
			$this->a_page_data['body'] = $this->load->view('periode_over', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}

		// $this->a_page_data['body'] = $this->load->view('dashboard/maintenance_page', $this->a_page_data, true);
		// $this->load->view('layout', $this->a_page_data);
	}

	public function register()
	{
		if($this->input->is_ajax_request()){
			$this->load->model('academic/Class_group_model', 'Cgm');
			$a_subjects = $this->input->post('subjects');
			$s_student_id = $this->input->post('student_id');
			$s_academic_year_id = $this->input->post('academic_year_id');
			$s_semester_type_id = $this->input->post('semester_type_id');

			$s_student_data = $this->Stm->get_student_by_id($s_student_id);
			$mbo_ofse_active_data = $this->General->get_where('dt_ofse', ['ofse_status' => 'active'])[0];
			$a_registration_subject_error = [];
			$a_subject_list = [];

			$this->db->trans_start();
			
			for($i = 0; $i < count($a_subjects); $i++){
				$a_filter_class_data = array(
					'ofs.curriculum_subject_id' => $a_subjects[$i],
					'ofs.academic_year_id' => $s_academic_year_id,
					'ofs.semester_type_id' => $s_semester_type_id,
					'ofs.study_program_id' => $s_student_data->study_program_id
				);

				$mba_curriculum_subject_data = $this->Crm->get_curriculum_subject_data($a_subjects[$i]);
				$mba_class_group_data = $this->Osm->get_offer_subject_class_group($a_filter_class_data);
				if (!$mba_class_group_data) {
					array_push($a_registration_subject_error, $a_subjects[$i]);
				}
				else if (!$mba_curriculum_subject_data) {
					$rtn = array('code' => 1, 'message' => 'Error processing data subject!');
					print json_encode($rtn);exit;
				}

				array_push($a_subject_list, $mba_curriculum_subject_data->subject_name);

				$mba_score_data = $this->Scm->get_score_student($s_student_id, array(
					'sc.academic_year_id' => $s_academic_year_id,
					'sc.semester_type_id' => $s_semester_type_id,
					'sc.curriculum_subject_id' => $a_subjects[$i]
				));
				
				if(!$mba_score_data){
					$this->Scm->save_data(array(
						'academic_year_id' => $s_academic_year_id,
						'class_group_id' => ($mba_class_group_data) ? $mba_class_group_data[0]->class_group_id : null,
						'semester_id' => 17,
						'semester_type_id' => $s_semester_type_id,
						'curriculum_subject_id' => $a_subjects[$i],
						'student_id' => $s_student_id,
						'ofse_period_id' => ($mbo_ofse_active_data) ? $mbo_ofse_active_data->ofse_period_id : null,
						'score_approval' => 'approved'
					));
				}
			}
			
			$mba_score_data = $this->Scm->get_score_student($s_student_id, array(
				'sc.academic_year_id' => $s_academic_year_id,
				'sc.semester_type_id' => $s_semester_type_id
			));
			
			foreach($mba_score_data as $score){
				if(!in_array($score->curriculum_subject_id, $a_subjects)){
					$this->Scm->delete_data($score->score_id);
				}
			}

			if (count($a_registration_subject_error) > 0) {
				modules::run('messaging/send_email', 
					['employee@company.ac.id'],
					'[Error] OFSE Class',
					$s_student_data->personal_data_name.'<br>'.json_encode($a_registration_subject_error),
					'employee@company.ac.id',
					false,
					false,
					''
				);
			}

			$mbo_family_data = $this->Fm->get_family_by_personal_data_id($s_student_data->personal_data_id);
			$mba_parent_email = false;
			if($mbo_family_data){
				$mba_family_members = $this->Fm->get_family_members($mbo_family_data->family_id, array(
					'family_member_status != ' => 'child'
				));
				if($mba_family_members){
					$mba_parent_email = array();
					foreach($mba_family_members as $family){
						array_push($mba_parent_email, $family->personal_data_email);
					}
				}
			}
			
			if($this->db->trans_status() === false){
				$this->db->trans_rollback();
				$rtn = array('code' => 1, 'message' => 'Fail');
			}
			else{
				$this->db->trans_commit();
				$s_subject_list = implode('</li><li>', $a_subject_list);
				$s_subject_list = (count($a_subject_list) > 0) ? '<ol><li>'.$s_subject_list.'</li></ol>' : '';

				$s_email_asc_head = $this->config->item('email')['academic']['head'];
				// $a_email_asc_member = $this->config->item('email')['academic']['members'];
				// $this->mail_confirmation($s_student_data->personal_data_name, $s_student_data->student_email, $s_subject_list, $mba_parent_email);
				$this->mail_confirmation($s_student_data->personal_data_name, $s_email_asc_head, $s_subject_list, false);
				
				$rtn = array('code' => 0, 'message' => 'Success');
			}
			
			print json_encode($rtn);
			exit;
		}
	}

	public function mail_confirmation($s_student_name, $s_student_email, $s_subject_list, $a_parent_email = false)
	{
		$body = '<p>Dear '.$s_student_name.' <'.$s_student_email.'>,</p>
			<p>This email is to confirm your registration for Repetition. You haveregistered the following subject(s):</p>'.$s_subject_list.'<p>Thank you for your registration.</p>';
		$config = $this->config->item('mail_config');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from('employee@company.ac.id', 'IULI OFSE Registration');
		$this->email->to($s_student_email);
		// $this->email->to('employee@company.ac.id');
		if ($a_parent_email) {
			// $this->email->cc($a_parent_email);
		}
		$bccEmail = array('employee@company.ac.id');
		$this->email->bcc($bccEmail);
		$this->email->subject("OFSE Registration");
		$this->email->message($body);
		$this->email->send();
		$this->email->clear(TRUE);
	}
}