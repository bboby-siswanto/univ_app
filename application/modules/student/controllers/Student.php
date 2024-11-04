<?php
class Student extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Student_model', 'Sm');
		$this->load->model('academic/Academic_year_model', 'Aym');
		$this->load->model('academic/Semester_model', 'SemM');
		$this->load->model('address/Address_model', 'AdrM');
		// $this->load->model('student/Student_model', 'Stm');
		$this->load->model('study_program/Study_program_model', 'Spm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Job_history_model', 'Jhm');
		$this->load->model('academic/Score_model', 'Scm');
		$this->load->model('personal_data/Family_model', 'Fmm');
		$this->load->model('admission/Referral_model', 'Rfm');
		$this->load->model('finance/Invoice_model', 'Im');
	}

	function submit_profile_data() {
		if ($this->input->is_ajax_request()) {
			$post_data = $this->input->post();
			print('<pre>');var_dump($post_data);exit;
			// $a_return = $this->Pdm->submit_student_profile($post_data);
			// if ($a_return['code'] == 0) {
			// 	# code...
			// }

			// print json_encode($a_return)
		}
	}

	public function profile($s_student_id = false)
	{
		if (!$s_student_id) {
			$s_student_id = $this->session->userdata('student_id');
		}

		$this->load->library('FeederAPI', ['mode' => 'production']);
		$this->load->model('File_manager_model', 'Fmd');

		$mba_student_data = $this->Sm->get_student_filtered(['ds.student_id' => $s_student_id]);
		if (!$mba_student_data) {
			show_404();
		}
		$s_personal_data_id = $mba_student_data[0]->personal_data_id;

		$b_forlap_sync = true;
		$a_result_check = $this->feederapi->post('GetBiodataMahasiswa', [
			'filter' => "id_mahasiswa='$s_personal_data_id'"
		]);

		if (($a_result_check->error_code == 0) AND (count($a_result_check->data) == 0)) {
			$b_forlap_sync = false;
		}

		$mba_family_data = $this->Fmm->get_family_lists_filtered([
			'fmm.family_id' => $mba_student_data[0]->family_id,
			'fmm.family_member_status != ' => 'child'
		]);
		$mba_highschooldata = $this->Pdm->get_academic_filtered([
			'dah.personal_data_id' => $mba_student_data[0]->personal_data_id,
			'dah.academic_history_this_job' => 'no',
			'ri.institution_type' => 'highschool'
		]);
		$mba_employmentdata = false;
		if ($mba_student_data[0]->student_class_type == 'karyawan') {
			$mba_employmentdata = $this->Jhm->get_job_history($mba_student_data[0]->personal_data_id);
		}
		
		$picture_avail = $this->Fmd->get_files($mba_student_data[0]->personal_data_id, '0bde3152-5442-467a-b080-3bb0088f6bac');
		$this->a_page_data['profile_src'] = ($picture_avail) ? 'file_manager/view/'.$picture_avail[0]->document_id.'/'.$mba_student_data[0]->personal_data_id : 'assets/img/silhouette.png';

		$this->a_page_data['forlap_sync'] = $b_forlap_sync;
		$this->a_page_data['parent_data'] = ($mba_family_data) ? $mba_family_data[0] : false;
		$this->a_page_data['highschool_data'] = ($mba_highschooldata) ? $mba_highschooldata[0] : false;
		$this->a_page_data['employment_data'] = ($mba_employmentdata) ? $mba_employmentdata[0] : false;
		$this->a_page_data['country_list'] = modules::run('address/get_country');
		$this->a_page_data['religion_list'] = $this->General->get_religions();
		$this->a_page_data['district_list'] = $this->General->get_where('dikti_wilayah');
		$this->a_page_data['student_data'] = $mba_student_data[0];

		$this->a_page_data['student_id'] = $s_student_id;
		$this->a_page_data['page_personal_data'] = $this->load->view('personal_data/page_profile/personal_data_page', $this->a_page_data, true);
		$this->a_page_data['page_parent_data'] = $this->load->view('personal_data/page_profile/parent_data_page', $this->a_page_data, true);
		$this->a_page_data['page_address_data'] = $this->load->view('personal_data/page_profile/address_data_page', $this->a_page_data, true);
		$this->a_page_data['page_highschool_data'] = $this->load->view('personal_data/page_profile/highschool_data_page', $this->a_page_data, true);
		$this->a_page_data['page_employment_data'] = $this->load->view('personal_data/page_profile/employment_data_page', $this->a_page_data, true);

		$this->a_page_data['body'] = $this->load->view('personal_data/student_data', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function lecturer_performance()
	{
		$this->load->model('validation_requirement/Assessment_model', 'Asm');
		$this->load->model('validation_requirement/Validation_requirement_model', 'Vm');
		$s_student_id = $this->session->userdata('student_id');

		$this->a_page_data['student_id'] = $s_student_id;
		$this->a_page_data['question_list'] = $this->Vm->get_question_list([
			'question_status' => 'active'
		]);
		$this->a_page_data['score_option'] = $this->Vm->get_result_option();
		
		$this->a_page_data['body'] = $this->load->view('validation_requirement/lecturer_assesment/assessment_list', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function university_performance()
	{
		$this->load->model('validation_requirement/Assessment_model', 'Asm');
		$s_assessment_id = '6300dd69-b415-11ed-9d77-52540039e1c3';
		$mba_student_has_filled = $this->Asm->get_result([
			'qr.personal_data_id' => $this->session->userdata('user'),
			'dq.assessment_id' => $s_assessment_id
		]);
		$this->a_page_data['assessment_id'] = $s_assessment_id;
		$this->a_page_data['list_question'] = $this->Asm->get_question_list($s_assessment_id);
		$this->a_page_data['list_option'] = $this->Asm->get_option_list($s_assessment_id);

		if ($mba_student_has_filled) {
			$this->a_page_data['body'] = iuli_message('Thank you for participating in this survey', 'We have saved the results of your assessment, We will keep your identity confidential in this and other assessments', true);
		}
		else {
			$this->a_page_data['body'] = $this->load->view('validation_requirement/campuss_assessment/list_question', $this->a_page_data, true);
		}
		$this->load->view('layout', $this->a_page_data);
	}

	function set_internet_student($s_student_id, $setting = "enable_wifi") {
		$mba_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id]);
		if ($mba_student_data) {
			$o_student = $mba_student_data[0];
			$this->load->library('IULI_Ldap');

			$setting = ($setting == 'enable_wifi') ? "TRUE" : "FALSE";
			$mba_ldap_result = $this->iuli_ldap->modifywifi($mba_student_data[0]->student_email, $setting);
			$a_return = ['code' => ($mba_ldap_result == 1) ? 0 : 1, 'message' => "unknow"];
		}
		else {
			$a_return = ['code' => 1, 'message' => 'No student found!'];
		}

		print json_encode($a_return);
	}

	function check_pass() {
		$s_ultah = '2007-01-17';
		print('<pre>');var_dump(date('dmY', strtotime($s_ultah)));
	}

	function cek_ldap($s_student_id) {
		$mba_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id]);
		if ($mba_student_data) {
			$o_student = $mba_student_data[0];
			$this->load->library('IULI_Ldap');

			$mba_ldap_data = $this->iuli_ldap->uid_search($o_student->student_email);
        	print('<pre>');var_dump($mba_ldap_data);exit;
		}
		else {
			$a_return = ['code' => 1, 'message' => 'No student found!'];
		}
	}

	function reset_password_student($s_student_id = false) {
		if ($this->input->is_ajax_request()) {
			$s_student_id = $this->input->post('student_id');
		}
		
		$mba_student_data = $this->Sm->get_student_filtered(['ds.student_id' => $s_student_id]);
		if ($mba_student_data) {
			$o_student = $mba_student_data[0];
			$this->load->library('IULI_Ldap');

			// $newpass = date('dmY', strtotime($o_student->personal_data_date_of_birth));
			$s_date = (is_null($o_student->personal_data_date_of_birth)) ? date('Y-m-d') : $o_student->personal_data_date_of_birth;
			$s_dateday = str_pad(date('d', strtotime($s_date)), 2, '0', STR_PAD_LEFT);
			$s_datemonth = str_pad(date('m', strtotime($s_date)), 2, '0', STR_PAD_LEFT);
			$s_dateyear = date('Y', strtotime($s_date));

			$newpass = $s_dateday.$s_datemonth.$s_dateyear;
			// print($newpass);exit;
			$mba_ldap_result = $this->iuli_ldap->updatepassword($o_student->student_email, $newpass);
			$a_return = ['code' => ($mba_ldap_result == 1) ? 0 : 1, 'message' => "unknow", 'data' => $mba_ldap_result];
		}
		else {
			$a_return = ['code' => 1, 'message' => 'No student found!'];
		}

		if ($this->input->is_ajax_request()) {
			print json_encode($a_return);
		}
		else {
			print('<pre>');var_dump($a_return);exit;
		}
	}

	public function get_eldap_data($s_student_id)
	{
		$mba_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id]);
		if ($mba_student_data) {
			$o_student = $mba_student_data[0];
			$this->load->library('IULI_Ldap');

			$mba_ldap_data = $this->iuli_ldap->uid_search($o_student->student_email, true);
		}
	}

	public function get_transcript_text($s_student_id = false, $s_case = 'transcript_text')
	{
		if ($this->input->is_ajax_request()) {
			$s_student_id = $this->input->post('student_id');
			$s_case = $this->input->post('mode');
		}

		$mbo_student_data = $this->Sm->get_student_filtered(array(
			'ds.student_id' => $s_student_id
		));

		if (!$mbo_student_data) {
			return false;
		}
		
		$mbo_student_data = $mbo_student_data[0];
		$mbo_student_family = false;
		$mbo_family_data = $this->Fmm->get_family_by_personal_data_id($mbo_student_data->personal_data_id);
		if ($mbo_family_data) {
			$mbo_student_family = $this->Fmm->get_family_lists_filtered(array(
				'fmm.family_id' => $mbo_family_data->family_id,
				'fmm.family_member_status !=' => 'child'
			))[0];
		}

		$this->a_page_data['message_to'] = implode('; ', array($mbo_student_data->student_email));
		$parent_email = '';
		if ($mbo_student_family AND (!is_null($mbo_student_family->personal_data_email))) {
			$parent_email = trim($mbo_student_family->personal_data_email);
			$this->a_page_data['message_to'] = implode('; ', array($mbo_student_data->student_email, $parent_email));
		}

		$a_param = array(
			'student_name' => $mbo_student_data->personal_data_name,
			'student_email' => $mbo_student_data->student_email,
			'parent_name' => ($mbo_student_family) ? $mbo_student_family->personal_data_name : '',
			'parent_email' => $parent_email
		);
		$transcript_text = $this->a_page_data['transcript_text'] = modules::run('messaging/text_template/halfway_transcript', $a_param);
		$transcript_text = trim(preg_replace('/\s\s+/', '<br/>', $transcript_text));
		// var_dump($transcript_text);exit;

		if ($this->input->is_ajax_request()) {
			$a_return = [
				'transcript' => $transcript_text
			];

			print json_encode($a_return);
		}
		else if ($s_case == 'transcript_text') {
			return $transcript_text;
		}
		else {
			return '';
		}
	}

	public function get_student_by_name_general()
	{
		if ($this->input->is_ajax_request()) {
			$s_term = $this->input->post('term');
			$a_clause = [];
			if (!empty($this->input->post('status'))) {
				$a_clause['st.student_status'] = $this->input->post('status');
			}

			if (count($a_clause) == 0) {
				$a_clause = false;
			}

			$mba_student_list = $this->Sm->get_student_by_name_filtered($s_term, $a_clause);
			print json_encode(['data' => $mba_student_list]);
		}
	}

	public function remove_file_record()
	{
		if ($this->input->is_ajax_request()) {
			$s_record_file_id = $this->input->post('data');
			$mba_file_data = $this->Sm->get_file_record([
				'pdf.record_file_id' => $s_record_file_id
			]);
			
			if (!$mba_file_data) {
				$a_return = ['code' => 1, 'message' => 'File not found!'];
			}
			else {
				$b_remove = $this->Sm->remove_record_file($s_record_file_id);
				if ($b_remove) {
					$s_filepath = APPPATH."uploads/".$mba_file_data[0]->personal_data_id."/record/".$mba_file_data[0]->record_file_name;
					if (file_exists($s_filepath)) {
						unlink($s_filepath);
					}
					$a_return = ['code' => 0, 'message' => 'Success'];
				}
				else {
					$a_return = ['code' => 2, 'message' => 'Failed remove file!'];
				}
			}
	
			print json_encode($a_return);
		}
	}

	public function notes($s_student_id)
	{
		$mbo_student_data = $this->Sm->get_student_filtered(['ds.student_id' => $s_student_id])[0];
		if ($mbo_student_data) {
			$this->a_page_data['student_data'] = $mbo_student_data;
			
			$this->a_page_data['employee_login'] = $this->session->userdata('employee_id');
			$this->a_page_data['a_record_category'] = $this->General->get_enum_values( 'dt_personal_data_record', 'record_category' );
			$this->a_page_data['body'] = $this->load->view('list_record', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}else{
			show_404();
		}
	}

	public function get_record_notes($s_personal_data_id = false, $s_all_dept = 'false')
	{
		if ($this->input->is_ajax_request()) {
			$s_personal_data_id = $this->input->post('personal_data_id');
			$s_all_dept = $this->input->post('all_dept');
		}

		$s_dept = $this->session->userdata('module');

		if ($s_all_dept == 'true') {
			$mba_record_data = $this->Sm->get_record_list($s_personal_data_id);
		}else{
			$mba_record_data = $this->Sm->get_record_list($s_personal_data_id, ['record_department' => $s_dept]);
		}
		if ($mba_record_data) {
			foreach ($mba_record_data as $o_record) {
				$o_record->record_added = date('d M Y H:i:s', strtotime($o_record->record_added));
				// $mba_record_files = $this->General->get_where('dt_personal_data_record_files', ['record_id' => $o_record->record_id]);
				$o_record->record_files = $this->General->get_where('dt_personal_data_record_files', ['record_id' => $o_record->record_id]);
			}
		}
		$a_return = ['code' => 0, 'data' => $mba_record_data];

		if ($this->input->is_ajax_request()) {
			print json_encode($a_return);
		}else{
			return $a_return;
		}
	}

	public function get_record_file($s_record_id = false)
	{
		if ($this->input->is_ajax_request()) {
			$s_record_id = $this->input->post('record_id');
		}

		$mba_record_file_data = $this->Sm->get_record_file([
			'pdf.record_id' => $s_record_id
		]);

		$a_return = ['code' => 0, 'data' => $mba_record_file_data];
		if ($this->input->is_ajax_request()) {
			print json_encode($a_return);
		}else{
			return $a_return;
		}
	}

	public function submit_record()
	{
		if ($this->input->is_ajax_request()) {
			$s_record_id = $this->input->post('record_id');
			$this->form_validation->set_rules('record_category', 'Category', 'required');
			$this->form_validation->set_rules('record_comment', 'Note', 'required');

			if ($this->form_validation->run()) {
				$s_dept = $this->session->userdata('module');
				$s_employee_id = $this->session->userdata('employee_id');

				$a_record_data = [
					'employee_id' => $s_employee_id,
					'personal_data_id' => $this->input->post('personal_data_id'),
					'record_department' => $s_dept,
					'record_category' => set_value('record_category'),
					'record_comment' => set_value('record_comment'),
					'date_added' => date('Y-m-d H:i:s')
				];

				if ($s_record_id == '') {
					$s_record_id = $this->uuid->v4();
					$a_record_data['record_id'] = $s_record_id;
					$submit = $this->Sm->submit_record($a_record_data);
				}else{
					$submit = $this->Sm->submit_record($a_record_data, ['record_id' => $s_record_id]);
				}

				if ($submit) {
					$a_error = [];
					$s_filepath = APPPATH."uploads/".$this->input->post('personal_data_id')."/record/";
					if(!file_exists($s_filepath)){
						mkdir($s_filepath, 0755, true);
					}

					$countfile = count($_FILES['files']['name']);
					for($i=0; $i<$countfile; $i++) {
						if(!empty($_FILES['files']['name'][$i])){
							$_FILES['file']['name'] = urlencode($_FILES['files']['name'][$i]);
							$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
							$_FILES['file']['type'] = $_FILES['files']['type'][$i];
							$_FILES['file']['size'] = $_FILES['files']['size'][$i];
						
							$config['upload_path'] 			= $s_filepath;
							$config['allowed_types'] 		= 'jpg|jpeg|png|bmp|pdf|doc|docx|xls|xlsx|odt|ods|eml';
							$config['max_size'] 			= 0;
							$config['overwrite']            = true;
	
							$this->load->library('upload', $config);
							$this->upload->initialize($config);
	
							if(!$this->upload->do_upload('file')){
								array_push($a_error, 'Error upload file '.$_FILES['files']['name'][$i]);
							}else {
								$uploadVal = $this->upload->data();
								$filename = $uploadVal['file_name'];
	
								$a_data = [
									'record_file_id' => $this->uuid->v4(),
									'record_id' => $s_record_id,
									'record_file_type' => $this->upload->data('file_type'),
									'record_file_name' => $filename
								];
							
								$result = $this->Sm->insert_record_file($a_data);
							}
						}
					}

					if (count($a_error) > 0) {
						$a_return = ['code' => 2, 'message' => implode(', ', $a_error)];
					}
					else {
						$a_return = ['code' => 0, 'message' => 'Success'];
					}
				}else{
					$a_return = ['code' => 1, 'message' => 'Error submit data'];
				}
			}else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

			print json_encode($a_return);
		}
	}

	public function test()
	{
		print('<pre>');
		// var_dump($this->session->userdata());
		$a_doc_name = array();
		$a_list_doc = modules::run('academic/document/list_dir');
		if (count($a_list_doc) > 0) {
			foreach ($a_list_doc as $s_filename) {
				$s_filesname = strtolower($s_filename);
				$i_pos_1 = strpos($s_filesname, 'thesis');
				$i_pos_2 = strpos($s_filesname, 'tesis');
				$i_pos_3 = strpos($s_filesname, 'research');

				if ($this->session->userdata('type') == 'student') {
					var_dump($i_pos_1);
				}
			}
		}
	}

	public function checker_student_end_semester($s_student_id)
	{
		$mba_student_krs = $this->Scm->get_score_student($s_student_id, array(
			'score_approval' => 'approved',
			'sc.score_display' => 'TRUE'
		));

		if ($mba_student_krs) {
			foreach ($mba_student_krs as $o_krs) {
				$s_subject_name = strtolower($o_krs->subject_name);

				$i_pos_1 = strpos($s_subject_name, 'thesis');
				$i_pos_2 = strpos($s_subject_name, 'tesis');
				$i_pos_3 = strpos($s_subject_name, 'research');

				if (($i_pos_1  !== false) OR ($i_pos_2 !== false) OR ($i_pos_3  !== false)) {
					print($s_subject_name);
					break;
				}
			}
		}else{
			print('false');
			// return false;
		}
	}

	public function download_academic_document($s_file)
	{
		// var_dump($s_file);exit;
		$s_file = str_replace('%20', ' ', $s_file);
		$s_path = APPPATH.'uploads/academic/public_document/';
		header('Content-Disposition: attachment; filename='.urlencode($s_file));
		readfile( $s_path . $s_file );
		exit;
	}

	public function academic_document()
	{
		$a_list_doc = modules::run('academic/document/list_dir');
		// print('<pre>');
		// var_dump($list_doc);exit;
		$this->a_page_data['list_doc'] = $a_list_doc;
		// var_dump($this->a_page_data['list_doc']);
		$this->a_page_data['body'] = $this->load->view('academic/document/academic_document', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function student_profile($s_student_id = false, $b_admission_mode = false)
	{
		$this->load->model('File_manager_model', 'File_manager');
		
		if ($s_student_id) {
			$mbo_student_data = $this->Sm->get_student_filtered(array('ds.student_id' => $s_student_id))[0];
			if ($mbo_student_data) {
				$s_personal_data_id = $mbo_student_data->personal_data_id;
				$a_show_data = $mbo_student_data;
				$a_show_data->alumni_nickname = null;
				$a_show_data->from_staff = true;
				$a_show_data->from_admission = $b_admission_mode;
				$this->a_page_data['personal_data_id'] = $s_personal_data_id;

				$this->a_page_data['o_personal_data'] = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
				$this->a_page_data['o_student_data'] = $a_show_data;
				$this->a_page_data['a_avatar'] = $this->File_manager->get_files($s_personal_data_id, '0bde3152-5442-467a-b080-3bb0088f6bac');
				$this->load->view('dashboard/student', $this->a_page_data);
			}
		}
	}

	public function cekcek()
	{
		$this->load->library('IULI_Ldap');
		$mba_ldap_data = $this->iuli_ldap->uid_search('rafi.wicaksono@stud.iuli.ac.id', true);
		print('<pre>');var_dump($mba_ldap_data);exit;
	}
	
	public function get_total_students($s_start_date = false, $s_end_date = false)
	{
		$o_active_batch = $this->General->get_batch(false, true);
		$a_student_status = $this->General->get_enum_values('dt_student', 'student_status');
		$a_student_data = array();
		$a_total_student_data = array();
		$a_previous_data = array();
		$a_total_previous_data = array();
		$a_participant_data = array();
		
		foreach($a_student_status as $status){
			$a_student_result = $this->Sm->get_total_students($o_active_batch[0]->academic_year_id, $s_start_date, $s_end_date, $status);
			$i_total_students = count($a_student_result);
			array_push($a_total_student_data, $i_total_students);
			if($status == 'participant'){
				$a_participant_data = $a_student_result;
			}
			$a_student_data[$status] = $i_total_students;
		}
		$a_student_data['sum'] = array_sum($a_total_student_data);
		
		foreach($a_student_status as $status){
			$a_student_result = $this->Sm->get_total_students(
				date('Y', strtotime($o_active_batch[0]->academic_year_id." -1 year")), 
				false,
				false,
				$status
			);
			$i_total_students = count($a_student_result);
			array_push($a_total_previous_data, $i_total_students);
			$a_previous_data[$status] = $i_total_students;
		}
		
		$mba_study_programs = $this->Spm->get_study_program(false, true);
		$a_total_by_study_programs = array();
		foreach($mba_study_programs as $study_program){
			$a_student_result = $this->Sm->get_total_students($o_active_batch[0]->academic_year_id, false, false, false, $study_program->study_program_id);
			$a_total_by_study_programs[$study_program->study_program_abbreviation] = count($a_student_result);
		}
		
		$a_previous_data['sum'] = array_sum($a_total_previous_data);
		$i_free_et = $i_paid_et = 0;
		
		if(($a_participant_data) AND (count($a_participant_data) >= 1)){
			foreach($a_participant_data as $participant){
				if($participant->has_to_pay_enrollment_fee == 'no'){
					$i_free_et++;
				}
				else{
					$i_paid_et++;
				}
			}
		}
		
		return array(
			'current_batch' => $a_student_data,
			'previous_batch' => $a_previous_data,
			'active_batch' => $o_active_batch[0]->academic_year_id,
			'free_et' => $i_free_et,
			'paid_et' => $i_paid_et,
			'by_study_programs' => $a_total_by_study_programs
		);
	}

	public function set_refferenced_code()
	{
		if ($this->input->is_ajax_request()) {
			$s_personal_data_id = $this->input->post('personal_data_id');
			$s_refferenced_code = $this->input->post('reffference_code_refferal');

			$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);

			$mbo_referenced_data = $this->Pdm->get_reference_code($s_refferenced_code);

			if (($mbo_student_data) AND ($mbo_referenced_data)) {
				$a_reference_data = array(
					'referrer_id' => $mbo_referenced_data->personal_data_id,
					'referenced_id' => $s_personal_data_id
				);

				$mba_checked_reference_ready = $this->Rfm->get_reference_list($a_reference_data);

				if ($mba_checked_reference_ready) {
					$a_return = array('code' => 1, 'message' => 'Student have been used refference code!');
				}else{
					$this->Pdm->set_sgs_promo($a_reference_data);
					$a_return = array('code' => 0, 'message' => 'Success');
				}
				
			}else{
				$a_return = array('code' => 1, 'message' => 'Reference Code Not Found!');
			}

			print json_encode($a_return);
		}
	}

	public function tracer_study($s_student_id = false)
	{
		if (!$s_student_id) {
			$s_student_id = $this->session->userdata('student_id');
		}

		$this->a_page_data['student_id'] = $s_student_id;
		$this->a_page_data['body'] = $this->load->view('table/tracer_study', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function study_activity($s_personal_data_id = false)
	{
		if (!$s_personal_data_id) {
			$s_personal_data_id = $this->session->userdata('user');
		}

		$mbo_student_data = $this->Sm->get_student_filtered(array('ds.personal_data_id' => $s_personal_data_id))[0];
		if ($mbo_student_data) {
			if ($mbo_student_data->student_send_transcript == 'TRUE' AND $mbo_student_data->student_portal_blocked == 'FALSE') {
				$this->a_page_data['personal_data_id'] = $mbo_student_data->personal_data_id;
				$this->a_page_data['student_id'] = $mbo_student_data->student_id;
				$this->a_page_data['body'] = $this->load->view('table/tracer_study', $this->a_page_data, true);
			}else{
				$this->a_page_data['message_error'] = "Sorry, you have been blocked from accessing this page!";
				$this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
			}

			$this->load->view('layout', $this->a_page_data);
		}
	}

	public function transcript()
	{
		$s_student_id = $this->session->userdata('student_id');
		$mbo_student_data = $this->Sm->get_student_filtered(array('ds.student_id' => $s_student_id))[0];
		if ($mbo_student_data) {
			if ($mbo_student_data->student_send_transcript == 'TRUE' AND $mbo_student_data->student_portal_blocked == 'FALSE') {
				$this->a_page_data['personal_data_id'] = $mbo_student_data->personal_data_id;
				$this->a_page_data['student_id'] = $s_student_id;
				$this->a_page_data['student_data'] = $mbo_student_data;
				$this->a_page_data['body'] = $this->load->view('academic/score/student_score', $this->a_page_data, true);
			}else{
				$this->a_page_data['message_error'] = "Sorry, you have been blocked from accessing this page!";
				$this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
			}

			$this->load->view('layout', $this->a_page_data);
		}
	}

	public function get_test()
	{
		$this->load->model('academic/Semester_model', 'SemM');
		$a_active_semester = $this->SemM->get_ofse_semester();
			$s_academic_year_id = $a_active_semester['active_semester']->academic_year_id;
			$s_semester_type_id = $a_active_semester['ofse_semester']->semester_type_id;

			$s_student_id = $this->session->userdata('student_id');

			$mba_score_student = $this->Scm->get_score_student($s_student_id, array(
				'sc.academic_year_id' => $s_academic_year_id,
				'sc.semester_type_id' => $s_semester_type_id
			));
			print('<pre>');var_dump($mba_score_student);exit;
	}

	public function show_serve()
	{
		print('<pre>');var_dump($_SERVER);exit;
	}
	
	public function ofse_registration($a_params = false)
	{
		$has_repeat = false;
		$this->load->model('academic/Semester_model', 'SemM');
		if($a_params){
			$s_academic_year_id = $a_params['academic_year_id'];
			$s_semester_type_id = $a_params['semester_type_id'];
			$s_student_id = $a_params['student_id'];
			$s_module = $a_params['module'];
		}
		else{
			$a_active_semester = $this->SemM->get_ofse_semester();
			$s_academic_year_id = $a_active_semester['active_semester']->academic_year_id;
			$s_semester_type_id = $a_active_semester['ofse_semester']->semester_type_id;
			$s_student_id = $this->session->userdata('student_id');
			$s_module = 'student';
		}
		
		$mba_score_ofse = $this->Scm->get_score_student($s_student_id, array(
			'sc.semester_id' => '17',
			'sc.score_approval' => 'approved'
		));

		$mba_score_student = $this->Scm->get_score_student($s_student_id, array(
			'sc.academic_year_id' => $s_academic_year_id,
			'sc.semester_type_id' => $s_semester_type_id
		));
		
		// if ($mba_score_ofse) {
		// 	if ($mba_score_student) {
		// 		$mba_score_student = array_merge($mba_score_student, $mba_score_ofse);
		// 	}
		// 	else {
		// 		$mba_score_student = $mba_score_ofse;
		// 	}
		// }
		$a_selected_score = array();
		if($mba_score_student){
			foreach($mba_score_student as $score){
				array_push($a_selected_score, array(
					'subject_id' => $score->curriculum_subject_id,
					'subject_name' => $score->subject_name,
					'approval' => $score->score_approval
				));
			}
		}
		else if ($mba_score_ofse) {
			$has_repeat = true;
			foreach($mba_score_ofse as $score){
				array_push($a_selected_score, array(
					'subject_id' => $score->curriculum_subject_id,
					'subject_name' => $score->subject_name,
					'approval' => $score->score_approval
				));
			}
		}
		// $this->a_page_data['repeat_subject'] = $mba_score_ofse;
		$this->a_page_data['has_repeat'] = $has_repeat;
		$this->a_page_data['module'] = $s_module;
		$this->a_page_data['academic_year_id'] = $s_academic_year_id;
		$this->a_page_data['semester_type_id'] = $s_semester_type_id;
		$this->a_page_data['student_id'] = $s_student_id;
		
		$this->a_page_data['selected_score'] = $a_selected_score;
		$this->a_page_data['student_data'] = $this->Sm->get_student_by_id($s_student_id);
		
		if($a_params){
			$this->load->view('ofse/registration', $this->a_page_data);
		}
		else{
			$this->a_page_data['body'] = $this->load->view('ofse/registration', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}
	}
	
	public function student_basic_init_form()
	{
		$this->load->view('form/student_basic_init_form', $this->a_page_data);
	}
	
	public function init_student_data()
	{
		if($this->input->is_ajax_request()){
			$s_personal_data_id = $this->input->post('personal_data_id');
			$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
			$s_student_id = $mbo_student_data->student_id;
			$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
			
			if(is_null($mbo_student_data->student_email)){
				$this->create_student_email($mbo_student_data);
			}
			
			if(is_null($mbo_personal_data->personal_data_reference_code)){
				modules::run('personal_data/create_reference_code', $s_personal_data_id);
			}
			
			$this->create_student_portal($s_student_id, $s_personal_data_id);
			
			$b_update_student_data = $this->Sm->update_student_data(array(
				'student_status' => 'active'
			), $s_student_id);

			if ($b_update_student_data) {
				$this->set_student_semester($mbo_student_data);
			}
			
			$rtn = array('code' => 0, 'message' => 'Success');
			
			print json_encode($rtn);
			exit;
		}
	}

	// public function tes_set_student_semester()
	// {
	// 	$s_student_personal_data_id = 'e404e2eb-962a-4ca4-a5ae-a6aac45f3d27';
	// 	$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_student_personal_data_id);
	// 	print('<pre>');
	// 	if ($mbo_student_data) {
	// 		$this->set_student_semester($mbo_student_data);
	// 	}else{
	// 		print('ngga ada');
	// 	}
	// }

	public function set_student_semester($o_student_data)
	{
		$mba_semester_settings = $this->SemM->get_semester_setting(array(
			'dss.academic_year_id >= ' => $o_student_data->academic_year_id
		));

		if ($mba_semester_settings) {
			foreach ($mba_semester_settings as $key => $o_semester) {
				$a_student_semester_data = array(
					'student_id' => $o_student_data->student_id,
					'semester_type_id' => $o_semester->semester_type_id,
					'academic_year_id' => $o_semester->academic_year_id,
					'semester_id' => 1
					// 'student_semester_status' => $o_student_data->student_status
				);

				$mba_student_semester = $this->SemM->get_semester_student($o_student_data->student_id, array(
					'ss.academic_year_id' => $o_semester->academic_year_id,
					'ss.semester_type_id' => $o_semester->semester_type_id
				));
				
				// $mba_student_semester = $this->SemM->get_student_semester(array(
				// 	'dss.student_id' => $o_student_data->student_id,
				// 	'dss.academic_year_id' => $o_semester->academic_year_id,
				// 	'dss.semester_type_id' => $o_semester->semester_type_id
				// ));

				if ($mba_student_semester) {
					$this->SemM->save_student_semester($a_student_semester_data, array(
						'student_id' => $o_student_data->student_id,
						'semester_type_id' => $o_semester->semester_type_id,
						'academic_year_id' => $o_semester->academic_year_id,
					));
					// print('update<br>');
					// var_dump($a_student_semester_data);
				}else{
					$this->SemM->save_student_semester($a_student_semester_data);
					// print('insert<br>');
					// var_dump($a_student_semester_data);
				}
			}
		}
	}
	
	public function create_test_data()
	{
		$s_student_id = 'da235d95-9640-4e19-b7dd-e64bf93bbe30';
		$mbo_student_data = $this->Sm->get_student_by_id($s_student_id);
		$a_post_data = array(
			'sync_data' => array(
				array(
					'table_name' => 'dt_personal_data',
					'data' => array('student_status' => $mbo_student_data->student_status),
					'clause' => array('personal_data_id' => $mbo_student_data->student_id)
				)
			)
		);
/*
		$a_post_data = array(
			'data' => array(
				'student_status' => $mbo_student_data->student_status
			),
			'clause' => array(
				'student_id' => $mbo_student_data->student_id
			)
		);
*/
		$hashed_string = $this->libapi->hash_data($a_post_data, 'PORTALIULIACID', 'e38cc4d0-7333-4459-824b-9c61cfb9a38b');
		$post_data = json_encode(array(
			'access_token' => 'PORTALIULIACID',
			'data' => $hashed_string
		));
		print $post_data;exit;
/*
		$url = 'https://www.iuli.ac.id/portal2/api/create_new_student';
		$result = $this->libapi->post_data($url, $post_data);
*/
		// $this->libapi->hash_data()
	}

	public function set_student_status()
	{
		if($this->input->is_ajax_request()) {
			$s_student_id = $this->input->post('student_id');
			$s_status_change = $this->input->post('status');
			

			$a_student_data['student_status'] = $s_status_change;
			($s_status_change == 'participant') ? $a_student_data['has_to_pay_enrollment_fee'] = 'no' : '';

			$this->Sm->update_student_data($a_student_data, $s_student_id);
			$a_rtn = array('code' => 0, 'message' => 'Success');
			
			print json_encode($a_rtn);
		}
	}
	
	public function get_student_by_name()
	{
		if ($this->input->is_ajax_request()) {
			$s_keyword = $this->input->post('keyword');
			$s_finance_year = $this->input->post('finance_year_id');
			$s_status = $this->input->post('status');

			$a_clause = ((isset($s_finance_year)) AND ($s_finance_year != '')) ? (['finance_year_id' => $s_finance_year]) : false;
			if ((isset($s_status)) AND ($s_status != '')) {
				if ($s_status == 'active') {
					$a_clause['ds.student_status'] = 'active';
					$mba_student_list = $this->Sm->get_student_by_name($s_keyword, $a_clause);
				}
				else {
					$mba_student_list = $this->Sm->get_student_by_name($s_keyword, $a_clause, $s_status);
				}
			}else{
				$a_clause['ds.student_status != '] = 'resign';
				$mba_student_list = $this->Sm->get_student_by_name($s_keyword, $a_clause);
			}
			
			print json_encode(array('code' => 0, 'data' => $mba_student_list));
			exit;
		}
	}

	public function form_student_setting_other($s_student_id = false)
	{
		$this->a_page_data['student_data'] = $this->Sm->get_student_by_id($s_student_id);
		$this->load->view('form/student_setting_advance', $this->a_page_data);
	}

	public function form_account_setting($s_student_id = false) {
		$this->load->library('IULI_Ldap');

		$mbo_student_data = $this->Sm->get_student_filtered(array(
			'ds.student_id' => $s_student_id,
		));
		if ($mbo_student_data AND $s_student_id) {
			$o_student = $mbo_student_data[0];
			$mba_ldap_data = $this->iuli_ldap->uid_search($o_student->student_email);
			if ($mba_ldap_data) {
				$this->a_page_data['mailenabled'] = $mba_ldap_data['mailenabled'][0];
				$this->a_page_data['internetenabled'] = $mba_ldap_data['internetenabled'][0];
				$this->a_page_data['student_data'] = $this->Sm->get_student_by_id($s_student_id);
				$this->load->view('form/student_account_setting', $this->a_page_data);
			}
			else {
				print('<h3>Error retrieve account data!</h3><p>Please check student email!</p>');
			}
		}
	}

	public function form_student_setting($s_student_id = false)
	{
		$this->a_page_data['student_data'] = $this->Sm->get_student_by_id($s_student_id);
		$this->a_page_data['student_status'] = $this->General->get_enum_values('dt_student', 'student_status');
		$this->a_page_data['program_lists'] = $this->Spm->get_program_lists_select();
		$this->a_page_data['active_semester'] = $this->SemM->get_active_semester();
		$this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
		$this->a_page_data['semester_type_list'] = $this->SemM->get_semester_type_lists(false, false, [1,2]);

		$student_data = $this->a_page_data['student_data'];
		if (!is_null($student_data->program_id)) {
			$this->a_page_data['study_program_lists'] = $this->Spm->get_study_program_lists(array('program_id' => $student_data->program_id));
		}
		
		if (!is_null($student_data->study_program_id)) {
			$this->a_page_data['study_program_majoring_lists'] = $this->General->get_where('ref_study_program_majoring', [
				'study_program_id' => (is_null($student_data->study_program_main_id)) ? $student_data->study_program_id : $student_data->study_program_main_id
			]);
		}
		
		$this->load->view('form/student_form_setting', $this->a_page_data);
	}
	
	public function create_student_portal($s_student_id)
	{
		// ga usah dipake!!!!
		// $this->load->model('Portal_student_model', 'Psm');
		
		// $mbo_student_data = $this->Sm->get_student_by_id($s_student_id);
		// $mbo_personal_data = $this->Pdm->get_personal_data_by_id($mbo_student_data->personal_data_id);
		
		// $this->Psm->create_student_portal($mbo_personal_data, $mbo_student_data);
	}

	public function get_grade_point_cummulative_or_not($s_student_id, $s_academic_year_id, $s_semester_type_id = false)
	{
		$a_clause = array(
			'sc.student_id' => $s_student_id,
			'sc.score_approval' => 'approved',
			'sc.score_display' => 'TRUE'
		);
		if ($s_semester_type_id) {
			$a_clause['sc.academic_year_id'] = $s_academic_year_id;
			$a_clause['sc.semester_type_id'] = $s_semester_type_id;
		}else{
			$a_clause['sc.academic_year_id <= '] = $s_academic_year_id;
		}

		$mbo_sum_credit_merit = $this->Scm->get_sum_merit_credit($a_clause);

		$s_sum_merit = ($mbo_sum_credit_merit) ? $mbo_sum_credit_merit->sum_merit : 0;
		$s_sum_credit = ($mbo_sum_credit_merit) ? $mbo_sum_credit_merit->sum_credit : 0;

		$d_cummulative_score = $this->grades->get_ipk($s_sum_merit, $s_sum_credit);
		return $d_cummulative_score;
		// print($d_cummulative_score);
	}

	public function show_name($s_student_id = null, $mbs_only_name = false)
	{

			$last_segment = $this->uri->total_segments();
			$s_id = (is_null($s_student_id)) ? $this->uri->segment($last_segment) : $s_student_id;
			$mbo_student_data = $this->Sm->get_student_filtered(array(
				'ds.personal_data_id' => $s_id,
				// 'ds.student_status' => 'active'
			))[0];
			
			if (!$mbo_student_data) {
				$mbo_student_data = $this->Sm->get_student_filtered(array(
					'ds.student_id' => $s_id,
					// 'ds.student_status' => 'active'
				))[0];
			}

			if ($mbo_student_data) {
				$this->a_page_data['student_last_score'] = $this->Scm->get_last_score(array(
					'st.personal_data_id' => $mbo_student_data->personal_data_id
				));
				
				// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
					$mbo_student_family = false;
					$mbo_family_data = $this->Fmm->get_family_by_personal_data_id($mbo_student_data->personal_data_id);
					if ($mbo_family_data) {
						$mbo_student_family = $this->Fmm->get_family_lists_filtered(array(
							'fmm.family_id' => $mbo_family_data->family_id,
							'fmm.family_member_status !=' => 'child'
						))[0];
					}

					$this->a_page_data['message_to'] = implode('; ', array($mbo_student_data->student_email));
					$parent_email = '';
					if ($mbo_student_family AND (!is_null($mbo_student_family->personal_data_email))) {
						$parent_email = trim($mbo_student_family->personal_data_email);
						$this->a_page_data['message_to'] = implode('; ', array($mbo_student_data->student_email, $parent_email));
					}

					$a_param = array(
						'student_name' => $mbo_student_data->personal_data_name,
						'student_email' => $mbo_student_data->student_email,
						'parent_name' => ($mbo_student_family) ? $mbo_student_family->personal_data_name : '',
						'parent_email' => $parent_email
					);
					$transcript_text = $this->a_page_data['transcript_text'] = modules::run('messaging/text_template/halfway_transcript', $a_param);
					$transcript_text = trim(preg_replace('/\s\s+/', '<br/>', $transcript_text));
					// var_dump($transcript_text);exit;
					$this->a_page_data['transcript_body'] = $transcript_text;
				// }
				$this->a_page_data['semester_type_list'] = $this->SemM->get_semester_type_lists(false, false, array(1,2));
				$this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
				$this->a_page_data['student_data'] = $this->Sm->get_student_filtered(array('ds.student_id' => $mbo_student_data->student_id));
				$this->a_page_data['personal_data_id'] = $mbo_student_data->personal_data_id;
				$this->a_page_data['personal_data'] = $mbo_student_data;
                $this->a_page_data['only_name'] = $mbs_only_name;
				$this->load->view('student/student_name', $this->a_page_data);
			}

			// $last_segment = $this->uri->total_segments();
			// $s_id = $this->uri->segment($last_segment);
			// $mbo_student_data = $this->Sm->get_student_filtered(array('ds.personal_data_id' => $s_id))[0];
			// if ($mbo_student_data) {
			// 	// var_dump($mbo_student_data);exit;
			// 	$this->a_page_data['student_data'] = $this->Sm->get_student_filtered(array('ds.student_id' => $mbo_student_data->student_id));
			// 	$this->a_page_data['personal_data_id'] = $mbo_student_data->personal_data_id;
			// 	$this->a_page_data['personal_data'] = $mbo_student_data;
			// 	$this->load->view('student/student_name', $this->a_page_data);
			// }
		// }
	}

	public function force_active($s_student_id)
	{
		$o_student_data = $this->Sm->get_student_by_id($s_student_id);
		$created = $this->create_student_email($o_student_data);
		print('<pre>');
		var_dump($created);
	}

	public function student_number($s_student_id = false)
	{
		if (!$s_student_id) {
			// $s_student_id = 'f37187a9-2885-466a-afde-5b10f0e0e3e9';
		}
		$o_student_data = $this->Sm->get_student_by_id($s_student_id);
		$s_student_number = $this->create_student_number($o_student_data);
		print($s_student_number);exit;
	}

	public function create_student_number($o_student_data)
	{
		$o_latest_student_data = $this->Spm->get_latest_id_number($o_student_data->finance_year_id, $o_student_data->program_id, $o_student_data->study_program_id);
		$o_study_program_data = $this->Spm->get_study_program($o_student_data->study_program_id);
		if (is_null($o_student_data->study_program_id)) {
			return false;
		}
		else {
			if(!is_null($o_latest_student_data->student_number)){
				$s_student_number = intval($o_latest_student_data->student_number) + 1;
			}
			else{
				$s_student_number = implode('', array(
					'1',
					$o_student_data->program_id,
					$o_student_data->academic_year_id,
					$o_study_program_data[0]->study_program_code,
					str_pad(1, 3, '0', STR_PAD_LEFT)
				));
			}
			
			$this->Sm->update_student_data(array(
				'student_number' => $s_student_number
			), $o_student_data->student_id);
			
			return $s_student_number;
		}
	}

	public function send_email_sgs($s_student_id)
	{
		$mbo_student_data = $this->Sm->get_student_by_id($s_student_id);
		$mba_parent_email = array();
		$mbo_family_data = $this->Fmm->get_family_by_personal_data_id($mbo_student_data->personal_data_id);
		if ($mbo_family_data) {
			$mba_family_members = $this->Fmm->get_family_members($mbo_family_data->family_id, array(
				'family_member_status != ' => 'child'
			));

			if ($mba_family_members) {
				foreach($mba_family_members as $family){
					array_push($mba_parent_email, $family->personal_data_email);
				}
			}
		}
		
		$this->payment_confirmation_sgs(
			$mbo_student_data->personal_data_name, 
			$mbo_student_data->personal_data_reference_code,
			$mbo_student_data->personal_data_email,
			(count($mba_parent_email) > 0) ? $mba_parent_email : false
		);
	}

	public function it_facilty_email($s_student_id)
	{
		// print('warung tutup sementara!');
		// exit;
		$s_path_file = APPPATH.'uploads/public/public_student/ist/Orientation-Week-2022-IST.pptx';
		$a_site_list = $this->config->item('iuli_sites');
		$a_email = $this->config->item('iuli_email');
		$mbo_student_data = $this->Sm->get_student_by_id($s_student_id);
		$s_dob = (is_null($mbo_student_data->personal_data_date_of_birth)) ? date('Y-m-d') : $mbo_student_data->personal_data_date_of_birth;
		// print('<pre>');
		// var_dump($a_site_list);exit;
		$this->a_page_data['s_student_name'] = $mbo_student_data->personal_data_name;
		$this->a_page_data['s_student_number'] = $mbo_student_data->student_number;
		$this->a_page_data['s_student_email'] = $mbo_student_data->student_email;
		$this->a_page_data['s_pass_email'] = str_pad(date('d', strtotime($s_dob)), 2, '0', STR_PAD_LEFT).str_pad(date('m', strtotime($s_dob)), 2, '0', STR_PAD_LEFT).date('Y', strtotime($s_dob));
		$this->a_page_data['s_iuli_site'] = $a_site_list['university'];
		$this->a_page_data['s_portal_site'] = $a_site_list['student_portal'];
		$this->a_page_data['s_webmail_site'] = $a_site_list['webmail'];
		$this->a_page_data['a_iuli_mail'] = $a_email;

		$s_html = $this->load->view('messaging/new_student_it', $this->a_page_data, true);
		// print($s_html);exit;
		$config = $this->config->item('mail_config');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);

		$this->email->attach($s_path_file);
		$this->email->from('employee@company.ac.id', 'IULI IST Team');
		$this->email->to($mbo_student_data->personal_data_email);
		// $this->email->to('employee@company.ac.id');
		$this->email->bcc([
			// $a_email['academic']['main'], 
			// $a_email['international_office']['main'], 
			// $a_email['finance']['main'],
			$a_email['it']['head'],
			'employee@company.ac.id'
		]);
		$this->email->subject("[IULI] Email Account");
		$this->email->message($s_html);
		$this->email->send();
		$this->email->clear();
		return true;
	}
	
	public function payment_confirmation_sgs($s_student_fullname, $s_referal_code, $s_student_email, $mba_parent_email = false)
	{
		$t_body = <<<TEXT
Dear {$s_student_fullname},

Spread the word; share your experience; refer a student and get a reward at the end of the semester!

Kindly be informed your SGS code as follow:


IULI Students Name : {$s_student_fullname}

SGS Code : {$s_referal_code}


Rules of the Game:
1. The referring IULI student will get a 5 Million Rupiah reward for every referred applicant who actually joins IULI as a student.
2. A IULI student can recommend IULI to more than one applicant.
3. The reward is valid if the name of the referred applicant has never previously been registered in the IULI database.
4. The reward is valid only if the SGS code of the referee appears on the application form submitted by the referred applicant(s).
5. The reward can be redeemed by IULI student after the referred student finishes his/her 1st semester.
6. The IULI student has paid the tuition fee for the respective semester.
 
Glossary
1. IULI Student : Candidate student who has been registered as an active student and paid the tuition fee.
2. Applicants : Candidate student who has applied to IULI.
3. SGS Code : Special code received by IULI students as an identity in referring IULI to friend(s).

 

If you have any questions, please do not hesitate to contact IULI Admission Office, on employee@company.ac.id or 021-5058 8000 or 0852 123 18000.

Regards,
IULI Admission Team
TEXT;

		$this->email->clear();
		// $config['mailtype'] = 'text';
		$config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		$this->email->initialize($config);
		$a_email = $this->config->item('email');
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		// $this->email->to($s_student_email); // hold sampai batas waktu yang tidak ditentukan
		$this->email->to('employee@company.ac.id');
		if($mba_parent_email){
			$this->email->cc($mba_parent_email);
		}
		$this->email->bcc([
			$a_email['admission']['main'], 
			$a_email['finance']['payment'],
			'employee@company.ac.id'
		]);
		$this->email->subject("[IULI] Your SGS Code");
		$this->email->message($t_body);
		$this->email->send();
	}

	// public function force_create_email()
	// {
	// 	$s_personal_data_name = 'MARSEP TRIANTO PAKONDO';
	// 	$s_date_of_birth = '2004-09-05';
	// 	$s_student_number = '11202201003';

	// 	$this->load->library('IULI_Ldap');
	// 	$mbs_email = $this->iuli_ldap->create_ldap_account($s_personal_data_name, $s_date_of_birth, 'student', $s_student_number);
	// 	print('<pre>');var_dump($mbs_email);exit;
	// }
	
	public function create_student_email($o_student_data)
	{
		$this->load->library('IULI_Ldap');
		
		$o_personal_data = $this->Pdm->get_personal_data_by_id($o_student_data->personal_data_id);
		$s_personal_data_name = $o_personal_data->personal_data_name;

		$s_student_name = str_replace(' ', '-', $s_personal_data_name);
		$s_student_name = preg_replace('/[^A-Za-z0-9\-]/', '', $s_student_name);
		$s_student_name = str_replace('-', ' ', $s_student_name);

		$a_student_name = explode(' ', $s_student_name);
		foreach ($a_student_name as $key => $s_name) {
			if (strlen($s_name) <= 1) {
				unset($a_student_name[$key]);
			}
		}
		$s_dob = (is_null($o_personal_data->personal_data_date_of_birth)) ? date('Y-m-d') : $o_personal_data->personal_data_date_of_birth;
		$s_student_name = implode(' ', $a_student_name);
		$s_student_number = $o_student_data->student_number;
		if (($s_student_name != '') AND (!empty($s_student_number))) {
			$mbs_email = $this->iuli_ldap->create_ldap_account($s_student_name, $s_dob, 'student', $s_student_number);
		
			if($mbs_email){
				$this->Sm->update_student_data(array(
					'student_email' => $mbs_email,
					'student_status' => 'active'
				), $o_student_data->student_id);

				$this->set_student_semester($o_student_data);
				$this->it_facilty_email($o_student_data->student_id);

				return array('code' => 0, 'student_email' => $mbs_email);
			}
			else{
				return array('code' => 1, 'message' => 'Failed to create student email');
			}
		}
		else {
			return array('code' => 1, 'message' => 'Failed retrieve student name and student number');
		}
	}
	
	public function change_study_program()
	{
		if($this->input->is_ajax_request()){
			$s_student_id = $this->input->post('student_id');
			$s_study_program_id = $this->input->post('study_program_id');
			$this->Sm->update_student_data(array('study_program_id' => $s_study_program_id), $s_student_id);
			$rtn = array('code' => 0, 'message' => 'Success!');
			print json_encode($rtn);
			exit;
		}
	}
	
	public function student_list_table()
	{	
		$this->config->load('button_config');
		$a_module_config = $this->config->item('module');
		
		if ($this->session->userdata('module') == 'admission') {
			$this->a_page_data['btn_html'] = modules::run('layout/generate_buttons', $this->session->userdata('module'), 'student');
			$this->a_page_data['modal_html'] = modules::run('layout/generate_modals', $this->session->userdata('module'), 'student');
			$this->load->view('table/admission_student_list_table', $this->a_page_data);
		}
		else if ($this->session->userdata('module') == 'finance') {
			$this->a_page_data['mbo_academic_year'] = $this->General->get_batch();
			$this->a_page_data['mbo_semester_type'] = $this->SemM->get_semester_type_lists(false, false, array(1,2,7,8));
			$this->a_page_data['academic_year_active'] = $this->session->userdata('academic_year_id_active');
			$this->a_page_data['semester_type_active'] = $this->session->userdata('semester_type_id_active');
			$this->load->view('table/finance_student_list_table', $this->a_page_data);
			
		}
	}

	public function show_scholarship()
	{
		$mba_scholarship_list = $this->General->get_where('ref_scholarship', ['scholarship_main_id' => NULL, 'scholarship_target' => 'candidate']);
		if ($mba_scholarship_list) {
			foreach ($mba_scholarship_list as $o_scholarship) {
				$mba_scholarship_list_sub = $this->General->get_where('ref_scholarship', ['scholarship_main_id' => $o_scholarship->scholarship_id]);
				$o_scholarship->sub_scholarship = $mba_scholarship_list_sub;
			}
		}
		print('<pre>');
		var_dump($mba_scholarship_list);
	}
	
	public function form_student_filter()
	{
		$mba_scholarship_list = $this->General->get_where('ref_scholarship', ['scholarship_main_id' => NULL, 'scholarship_target' => 'candidate']);
		if ($mba_scholarship_list) {
			foreach ($mba_scholarship_list as $o_scholarship) {
				$mba_scholarship_list_sub = $this->General->get_where('ref_scholarship', ['scholarship_main_id' => $o_scholarship->scholarship_id]);
				$o_scholarship->sub_scholarship = $mba_scholarship_list_sub;
			}
		}

		$this->a_page_data['active_batch'] = $this->General->get_batch(false, true);
		$this->a_page_data['batch'] = $this->General->get_batch();
		$this->a_page_data['scholarship_list'] = $this->General->get_where('ref_scholarship', ['scholarship_fee_type' => 'main']);
		$this->a_page_data['scholarship_data_list'] = $mba_scholarship_list;
		$this->a_page_data['ref_program'] = $this->Spm->get_program_lists_select(['is_active' => 'yes', 'is_institute' => 'yes']);
		$this->a_page_data['study_program'] = $this->Spm->get_study_program_instititute();
		
		$this->a_page_data['status_lists'] = $this->General->get_enum_values('dt_student', 'student_status');
		switch ($this->session->userdata('module')) {
			case 'admission':
				$this->load->view('form/student_filter_form_admission', $this->a_page_data);
				break;
			case 'finance':
				$this->load->view('form/student_filter_form', $this->a_page_data);
				break;
			default:
				$this->load->view('form/student_filter_form', $this->a_page_data);
				break;
		}
	}

	function lists_course() {
		$this->a_page_data['class_type'] = ['course'];
		$this->a_page_data['body'] = $this->load->view('list', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function lists()
	{
		$this->a_page_data['class_type'] = ['regular', 'exchange'];
		$this->a_page_data['body'] = $this->load->view('list', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function lists_karyawan()
	{
		$this->a_page_data['class_type'] = ['karyawan'];
		$this->a_page_data['body'] = $this->load->view('list', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}
	public function national()
	{
		$this->a_page_data['class_type'] = ['national'];
		$this->a_page_data['body'] = $this->load->view('list', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function filter_student_alumni()
	{
		if ($this->input->is_ajax_request()) {
			$this->load->model('thesis/Thesis_model', 'Tm');
			$this->load->model('alumni/Alumni_model', 'Alm');
			$a_filter_data = $this->input->post();

			foreach ($a_filter_data as $key => $value) {
				if ($value == 'all') {
					unset($a_filter_data[$key]);
				}else if($key == 'study_program_id') {
					$a_filter_data['ds.study_program_id'] = $value;
					unset($a_filter_data[$key]);
				}else if($key == 'program_id') {
					$a_filter_data['ds.program_id'] = $value;
					unset($a_filter_data[$key]);
				}
			}

			$mba_student_list = $this->Sm->get_student_filtered($a_filter_data, ['graduated']);
			if ($mba_student_list) {
				foreach ($mba_student_list as $o_student) {
					$mba_has_filled_tracer_dikti = $this->General->get_where('dikti_question_answers', ['personal_data_id' => $o_student->personal_data_id]);
					$mba_tracer_answer_data = $this->Alm->get_alumni_answer_lists(['dqa.personal_data_id' => $o_student->personal_data_id]);
					$mba_student_thesis = $this->Tm->get_thesis_log_student(['ts.student_id' => $o_student->student_id]);
					$s_thesis_final_fname = '';

					if ($mba_student_thesis) {
						foreach ($mba_student_thesis as $o_thesis_student_log) {
							if (!is_null($o_thesis_student_log->thesis_final_fname)) {
								$s_thesis_final_fname = $o_thesis_student_log->thesis_student_id.'/'.$o_thesis_student_log->thesis_final_fname;break;
							}
						}
					}

					$o_student->company_data = false;
					$o_student->thesis_final_fname = $s_thesis_final_fname;
					$o_student->answer_tracer_data = $mba_tracer_answer_data;
					$o_student->last_submit_tracer = ($mba_tracer_answer_data) ? date('d M Y H:i:s', strtotime($mba_tracer_answer_data[0]->answer_timestamp)) : '';

					if (!is_null($o_student->academic_history_this_job)) {
						$mbo_alumni_job_data = $this->Jhm->get_job_history($o_student->personal_data_id);
						if ($mbo_alumni_job_data) {
							$o_student->company_data = $mbo_alumni_job_data[0];
						}
					}
					$o_student->list_answer_dikti = $mba_has_filled_tracer_dikti;
				}
			}
			print json_encode(['code' => 0, 'data' => $mba_student_list]);
		}
	}

	public function filter_student_finance($a_filter_data = false, $b_not_ajax = false)
	{
		if ($this->input->is_ajax_request()) {
			$a_filter_data = $this->input->post();
		}

		if ($a_filter_data) {
			$status_filter = false;
			if ($a_filter_data['student_status'] !== null) {
				if (is_array($a_filter_data['student_status'])) {
					if (count($a_filter_data['student_status']) > 0) {
						$status_filter = $a_filter_data['student_status'];
					}
				}
			}

			$scholarship_id = ($a_filter_data['scholarship_id'] !== null) ? $a_filter_data['scholarship_id'] : null;

			foreach ($a_filter_data as $key => $value) {
				if (is_array($value)) {
					unset($a_filter_data[$key]);
				}
				else if ($value == 'all') {
					unset($a_filter_data[$key]);
				}
				else if ($key == 'scholarship_id') {
					unset($a_filter_data[$key]);
				}
				else if($key == 'study_program_id') {
					$a_filter_data['ds.study_program_id'] = $value;
					unset($a_filter_data[$key]);
				}
				else if($key == 'program_id') {
					$a_filter_data['ds.program_id'] = $value;
					unset($a_filter_data[$key]);
				}
			}
		}

		$mba_student_list = $this->Sm->get_student_filtered($a_filter_data, $status_filter);

		if ($mba_student_list) {
			foreach ($mba_student_list as $key => $o_student) {
				if ($scholarship_id !== null) {
					
					if ($scholarship_id != '') {
						if ($scholarship_id == 'all') {
							$mba_student_scholarship = $this->Pdm->get_personal_data_scholarship($o_student->personal_data_id, [
								'rs.scholarship_fee_type' => 'main'
							]);

							if (!$mba_student_scholarship) {
								unset($mba_student_list[$key]);
							}
						}else{
							$mba_scholarship_have_sub = $this->General->get_where('ref_scholarship', ['scholarship_main_id' => $scholarship_id]);
							$a_scholarship_id = false;
							$a_filter = false;
							
							if ($mba_scholarship_have_sub) {
								$a_scholarship_id = [];
								foreach ($mba_scholarship_have_sub as $o_sub_scholarship) {
									array_push($a_scholarship_id, $o_sub_scholarship->scholarship_id);
								}
							}else{
								$a_filter = ['pds.scholarship_id' => $scholarship_id];
							}

							$mba_student_scholarship = $this->Pdm->get_personal_data_scholarship($o_student->personal_data_id, $a_filter, $a_scholarship_id);
							if ($mba_student_scholarship == false) {
								// print('<pre>');var_dump($mba_student_list[$key]->personal_data_name);
								unset($mba_student_list[$key]);
							}
							// $o_student->scholarship_data = $mba_student_scholarship;
						}
					}
				}
			}
			$mba_student_list = array_values($mba_student_list);
		}

		if (($this->input->is_ajax_request()) AND (!$b_not_ajax)) {
			print json_encode(['code' => 0, 'data' => $mba_student_list]);
		}else{
			return $mba_student_list;
		}
	}

	public function filter_student_academic()
	{
		if ($this->input->is_ajax_request()) {
			$this->load->model('student/Internship_model', 'Inm');
			$a_filter_data = $this->input->post();
			$passed_defense = $this->input->post('passed_defense');

			foreach ($a_filter_data as $key => $value) {
				if (is_array($value)) {
					unset($a_filter_data[$key]);
				}
				else if($a_filter_data[$key] == 'all'){
					unset($a_filter_data[$key]);
				}
				// else if($key == 'study_program_id') {
				// 	$a_filter_data['ds.study_program_id'] = $value;
				// 	unset($a_filter_data[$key]);
				// }
				else if($key == 'program_id') {
					$a_filter_data['ds.program_id'] = $value;
					unset($a_filter_data[$key]);
				}
				else if ($key == 'passed_defense') {
					unset($a_filter_data[$key]);
				}
			}

			if ($passed_defense !== null) {
				$a_filter_data['ds.student_mark_completed_defense'] = 1;
			}

			$status_filter = false;
			if (is_array($this->input->post('student_status'))) {
				if (count($this->input->post('student_status')) > 0) {
					$status_filter = $this->input->post('student_status');
				}
			}

			$prodi_filter = false;
			if (is_array($this->input->post('study_program_id'))) {
				if (count($this->input->post('study_program_id')) > 0) {
					$prodi_filter = $this->input->post('study_program_id');
				}
			}

			$mba_filtered_data = $this->Sm->get_student_filtered($a_filter_data, $status_filter, false, $prodi_filter);
			$mba_semester_active = $this->SemM->get_active_semester();

			if ($mba_filtered_data) {
				foreach($mba_filtered_data as $key_data => $student_data) {
					// $s_gpa = modules::run('academic/score/get_score_cummulative', $student_data->student_id, $mba_semester_active->academic_year_id, false, 'gpa', true, true);
	
					$mba_student_internship_data = $this->Inm->get_internship_student(['st.student_id' => $student_data->student_id]);
					$mba_student_score = $this->Scm->get_score_data([
						'sc.student_id' => $student_data->student_id,
						'sc.academic_year_id' => $mba_semester_active->academic_year_id,
						'sc.semester_type_id' => $mba_semester_active->semester_type_id,
						'sc.score_approval' => 'approved',
						'sc.score_display' => 'TRUE'
					]);

					if ($mba_student_internship_data) {
						$mba_student_internship_document = $this->Inm->get_internship_document(['si.internship_id' => $mba_student_internship_data[0]->internship_id]);
						$mba_student_internship_data[0]->internship_doc_list = $mba_student_internship_document;
					}
					$student_data->internship_data = $mba_student_internship_data[0];

					$mba_vaccine_data = $this->General->get_where('dt_personal_data_covid_vaccine', ['personal_data_id' => $student_data->personal_data_id]);
					$student_data->vaccine_data = $mba_vaccine_data;
					$mba_student_degree = $this->General->get_where('ref_program_study_program', [
						'program_id' => $student_data->student_program,
						'study_program_id' => $student_data->study_program_id
					]);

					$mba_family_members = $this->Fmm->get_family_members($student_data->family_id, array(
						'dfm.family_member_status != ' => 'child'
					));
	
					$mba_address = $this->AdrM->get_personal_address($student_data->personal_data_id);
					$student_data->address_data = $mba_address;

					if($mba_family_members){
						foreach($mba_family_members as $family_data){
							$mba_parent_ocupation = $this->General->get_where('ref_ocupation', ['ocupation_id' => $family_data->ocupation_id]);
							$mba_job_history = $this->Jhm->get_job_history($family_data->personal_data_id);
							$family_data->job_history = $mba_job_history;
							$family_data->ocupation_name = ($mba_parent_ocupation) ? $mba_parent_ocupation[0]->ocupation_name : '';
						}
					}
	
					$s_subject_name = '';
	
					if ($mba_student_score) {
						foreach ($mba_student_score as $o_score) {
							$subject_name = strtolower($o_score->subject_name);
		
							if(strpos($subject_name, 'thesis') !== false){
								$s_subject_name = 'thesis';
							}else if(strpos($subject_name, 'internship') !== false){
								$s_subject_name = 'internship';
							}else if(strpos($subject_name, 'research semester') !== false){
								$s_subject_name = 'research semester';
							}
							
						}
					}
	
					$mbo_score_research_semester = $this->Scm->get_score_like_subject_name([
						'sc.student_id' => $student_data->student_id,
						'sc.score_approval' => 'approved'
					], 'research semester');

					$s_degreee = '';
					$s_degree_abbr = '';
					if ($mba_student_degree) {
						$s_degreee = $mba_student_degree[0]->degree_name;
						$s_degree_abbr = $mba_student_degree[0]->degree_abbreviation;
					}

					$student_data->subject_current_thesis_internship = $s_subject_name;
					$student_data->degree_name = $s_degreee;
					$student_data->degree_abbreviation = $s_degree_abbr;
					// $student_data->gpa = $s_gpa;
					$student_data->gpa = 0;
					$student_data->family_members = $mba_family_members;
					$student_data->dob = date('d F Y', strtotime($student_data->personal_data_date_of_birth));
					$student_data->has_take_research_semester = ($mbo_score_research_semester) ? true : false;
				}

				$mba_filtered_data = array_values($mba_filtered_data);
			}

			print json_encode(array('code' => 0, 'data' => $mba_filtered_data));
		}
	}

	function tets_invoice() {
		// $mba_invoice_enrollment = $this->Im->get_student_billing(['di.personal_data_id' => '089f11e1-54ea-4bf9-91da-9ebc4b262d9f', 'fee.payment_type_code' => '01'], 'di.invoice_id');
		$mba_invoice_enrollment = $this->Im->get_invoice_by_deadline(['di.personal_data_id' => '089f11e1-54ea-4bf9-91da-9ebc4b262d9f'], '01', false);
		print('<pre>');var_dump($mba_invoice_enrollment);exit;
	}
	
	public function filter_result($live_request = false)
	{
		if($this->input->is_ajax_request()){
			$this->load->model('File_manager_model', 'File_manager');
			// $a_subject_id_thesis = ['2716b5eb-2c38-4eb5-80a7-0600c5bdb71a', 'a88fc6ce-9ae0-4fc3-a3b2-2459b2c96c89'];
			// $a_subject_id_internship = ['0ebfffb3-7b95-41be-8504-36cbe8cf3988', '0f832bdc-0efc-4cd2-b0d8-59addda292b1', '26e00ee5-a185-4a54-a45e-f4531be4f0bb', '50500b60-8769-4919-93ea-3df3ed94687a', ''];
			$a_filter_data = $this->input->post();
			$based_on_reference = $this->input->post('based_on_reference');
			$based_on_scholarship = $this->input->post('based_on_scholarship');
			$b_external_get = ((!empty($this->input->post('target_from'))) AND ($this->input->post('target_from') == 'external')) ? true : false;

			foreach($a_filter_data as $key => $value){
				if (is_array($value)) {
					unset($a_filter_data[$key]);
				}
				else if($a_filter_data[$key] == 'all'){
					unset($a_filter_data[$key]);
				}
				else if($key == 'program_id'){
					$a_filter_data['ds.program_id'] = $value;
					unset($a_filter_data[$key]);
				}
				else if($key == 'study_program_id') {
					$a_filter_data['ds.study_program_id'] = $value;
					unset($a_filter_data[$key]);
				}
				else if($key == 'academic_year_id') {
					$a_filter_data['ds.academic_year_id'] = $value;
					unset($a_filter_data[$key]);
				}
				else if ($key == 'based_on_reference') {
					unset($a_filter_data[$key]);
				}
				else if ($key == 'based_on_scholarship') {
					unset($a_filter_data[$key]);
				}
				else if ($key == 'registration_year') {
					unset($a_filter_data['registration_year']);
				}
				else if ($key == 'scholarship_id') {
					unset($a_filter_data['scholarship_id']);
				}
				else if ($key == 'target_from') {
					unset($a_filter_data['target_from']);
				}
			}

			$status_filter = false;
			if (is_array($this->input->post('student_status'))) {
				if (count($this->input->post('student_status')) > 0) {
					$status_filter = $this->input->post('student_status');
				}
			}
			
			$a_program_id = ($this->input->post('student_class_type') == 'karyawan') ? [2] : [1,3,4,6,7,8,9];
			// print('<pre>');var_dump($a_program_id);exit;
			if ($b_external_get) {
				$a_filter_data['ds.academic_year_id'] = '2024';
				$mba_filtered_data = $this->Sm->get_student_filtered($a_filter_data, ['register', 'candidate', 'pending']);
			}
			else {
				$mba_filtered_data = $this->Sm->get_student_filtered($a_filter_data, $status_filter, false, false, $a_program_id);
			}
			// $mba_semester_active = $this->SemM->get_active_semester();
			if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
				// print('<pre>');var_dump($mba_filtered_data);exit;
			}
			
			if($mba_filtered_data){
				foreach($mba_filtered_data as $key_data => $student_data){
					$b_has_paid_enroll = false;
					$mba_invoice_enrollment = $this->Im->get_invoice_by_deadline(['di.personal_data_id' => $student_data->personal_data_id], '01', false);
					if ($mba_invoice_enrollment) {
						$payment_data = $this->Im->get_first_payment(['di.invoice_id' => $mba_invoice_enrollment[0]->invoice_id, 'sid.sub_invoice_details_amount_paid > ' => '0']);
						if ($payment_data) {
							$d_total_payment = 0;
							foreach ($payment_data as $o_sub_invoice_details) {
								$d_total_payment += $o_sub_invoice_details->sub_invoice_details_amount_paid;
							}

							if ($d_total_payment >= 200000) {
								$b_has_paid_enroll = true;
							}
						}
					}
					$mba_student_referral = $this->Rfm->get_reference_list(array('referenced_id' => $student_data->personal_data_id))[0];
					$mba_student_online_test = $this->General->get_where('dt_exam_candidate', [
						'student_id' => $student_data->student_id,
						'candidate_exam_status' => 'FINISH'
					]);
					$mba_prodi_alt1 = (!is_null($student_data->study_program_id_alt_1)) ? $this->General->get_where('ref_study_program', ['study_program_id' => $student_data->study_program_id_alt_1]) : false;
					$mba_prodi_alt2 = (!is_null($student_data->study_program_id_alt_2)) ? $this->General->get_where('ref_study_program', ['study_program_id' => $student_data->study_program_id_alt_2]) : false;
					$s_prodi_alt = ($mba_prodi_alt1) ? $mba_prodi_alt1[0]->study_program_name : '';
					$s_prodi_alt .= ($mba_prodi_alt2) ? ' / '.$mba_prodi_alt2[0]->study_program_name : '';
					$student_data->has_finished_online_test = ($mba_student_online_test) ? 'Yes' : 'No';
					$student_data->exam_candidate_id = ($mba_student_online_test) ? $mba_student_online_test[0]->exam_candidate_id : '';
					$student_data->has_paid_enrollment_fee = $b_has_paid_enroll;
					$student_data->invoice_enrollment = $mba_invoice_enrollment;
					$student_data->study_program_alternative = $s_prodi_alt;
					
					$mba_student_scholarship_registration = false;
					if ($based_on_scholarship !== null) {
						$a_filter_scholarship = [
							'personal_data_id' => $student_data->personal_data_id
						];
						
						if (($this->input->post('registration_year') !== null) AND ($this->input->post('registration_year') != 'all')) {
							$a_filter_scholarship['academic_year_id'] = $this->input->post('registration_year');
						}

						if (($this->input->post('scholarship_id') !== null) AND ($this->input->post('scholarship_id') != 'all')) {
							$a_filter_scholarship['scholarship_id'] = $this->input->post('scholarship_id');
						}
	
						$mba_student_scholarship_registration = $this->General->get_where('dt_registration_scholarship', $a_filter_scholarship);
					}

					$student_data->scholarship_name = '';
					if (!is_null($student_data->student_registration_scholarship_id)) {
						$mbo_scholarship = $this->General->get_where('ref_scholarship', ['scholarship_id' => $student_data->student_registration_scholarship_id])[0];
						if ($mbo_scholarship) {
							$student_data->scholarship_name = $mbo_scholarship->scholarship_name;
						}
					}

					$mba_family_members = $this->Fmm->get_family_members($student_data->family_id, array(
						'dfm.family_member_status != ' => 'child'
					));

					$s_referral_type = 'OTHER';
					if ($mba_student_referral) {
						$mba_refferal_student = $this->Sm->get_student_by_personal_data_id($mba_student_referral->referrer_id);
						$s_referral_type = ($mba_refferal_student) ? 'STUDENT' : 'OTHER';
					}
					
					$mba_address = $this->AdrM->get_personal_address($student_data->personal_data_id);
					$student_data->address_data = $mba_address;

					$s_address = 'N/A';
					if ($mba_address) {
						$a_address_candidate = [];
						foreach ($mba_address as $o_address) {
							array_push($a_address_candidate, $o_address->address_city);
						}
						$s_address = implode(',', $a_address_candidate);
					}
					$student_data->address_city = $s_address;
					
					$s_family_name = 'N/A';
					$s_family_contact = 'N/A';
					$s_family_email = 'N/A';
					$s_family_ocupation = 'N/A';
					if($mba_family_members){
						$a_family_name = [];
						$a_family_email = [];
						$a_family_contact = [];
						$a_family_ocupation = [];
						foreach($mba_family_members as $family_data){
							// $mba_job_history = $this->Jhm->get_job_history($family_data->personal_data_id);
							$mba_job_history = $this->Jhm->get_parent_occupation($family_data->personal_data_id);
							$family_data->job_history = $mba_job_history;
							array_push($a_family_name, $family_data->personal_data_name);
							array_push($a_family_email, $family_data->personal_data_email);
							array_push($a_family_contact, (($family_data->personal_data_phone == null) ? '' : $family_data->personal_data_phone.'/').($family_data->personal_data_cellular == null) ? '' : $family_data->personal_data_cellular);
							if ($mba_job_history) {
								foreach ($mba_job_history as $job) {
									array_push($a_family_ocupation, $job->ocupation_name);
								}
							}
						}
						$s_family_name = implode(', ', $a_family_name);
						$s_family_contact = implode(', ', $a_family_contact);
						$s_family_email = implode(', ', $a_family_email);
						$s_family_ocupation = implode(', ', $a_family_ocupation);
					}

					$student_data->family_email = $s_family_email;
					$student_data->family_name = $s_family_name;
					$student_data->family_ocupation = $s_family_ocupation;
					$student_data->family_contact = $s_family_contact;
					$student_data->referal_name = ($mba_student_referral) ? $mba_student_referral->referrer_personal_data_name : 'N/A';
					$student_data->referal_type = $s_referral_type;
					$student_data->pas_foto = $this->File_manager->get_files($student_data->personal_data_id, '0bde3152-5442-467a-b080-3bb0088f6bac');
					$student_data->family_members = $mba_family_members;
					$student_data->dob = date('d F Y', strtotime($student_data->personal_data_date_of_birth));

					if ($based_on_reference !== null) {
						if (!$mba_student_referral) {
							unset($mba_filtered_data[$key_data]);
						}
					}

					if ($based_on_scholarship !== null) {
						if (!$mba_student_scholarship_registration) {
							unset($mba_filtered_data[$key_data]);
						}
					}
				}

				$mba_filtered_data = array_values($mba_filtered_data);
			}
			
			if ($live_request) {
				return $mba_filtered_data;
			}
			else {
				print json_encode(array('code' => 0, 'data' => $mba_filtered_data));
			}
			exit;
		}
	}

	public function download_list()
	{
		if ($this->input->is_ajax_request()) {
			$student_data = $this->filter_result(true);
			// $student_data = $this->input->post('data_src');
			$data_key = $this->input->post('list_key');
			$data_title = $this->input->post('list_title');
			$a_data_list = [];
			foreach ($student_data as $o_data) {
				$a_data_print = [];
				$i_col = 0;
				foreach ($data_key as $s_key) {
					$a_data_print[$s_key] = $o_data->$s_key;
				}
				array_push($a_data_list, $a_data_print);
			}
			$s_file = modules::run('download/excel_download/print_result', $a_data_list, $data_key, $data_title, 'IULI_Portal');
			if ($s_file) {
				$a_return = ['code' => 0, 'filename' => $s_file];
			}
			else {
				$a_return = ['code' => 1, 'message' => 'Failed download data!'];
			}
			
			print json_encode($a_return);
		}
	}
}