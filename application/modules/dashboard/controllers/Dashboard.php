<?php
class Dashboard extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('File_manager_model', 'File_manager');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('student/Student_model', 'Stm');
		$this->load->model('employee/Employee_model', 'Emm');
		$this->load->model('academic/Semester_model', 'Sem');
		$this->load->model('academic/Score_model', 'Scm');
		$this->load->model('academic/Class_group_model', 'Cgm');
		$this->load->model('admission/Admission_model', 'Adm');
		$this->load->model('thesis/Thesis_model', 'Tsm');
		$this->load->model('academic/Ofse_model', 'Ofm');
	}

	public function push_notification($s_message = false)
	{
		if ($this->input->is_ajax_request()) {
			$s_message = $this->input->post('message');
		}

		$this->send_notification_telegram($s_message);
	}

	public function check()
	{
		if (($this->session->userdata('type') == 'student') OR ($this->session->userdata('type') == 'alumni')) {
			$s_personal_data_id = $this->session->userdata('user');
			$s_student_id = $this->session->userdata('student_id');
			$mbo_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $s_student_id))[0];
			$mbo_student_alumni = $this->Stm->get_alumni_data($this->session->userdata('student_id'));
			if (!$mbo_student_alumni) {
				$a_show_data = $mbo_student_data;
				$a_show_data->alumni_nickname = null;

				// print('<pre>');
				// var_dump($this->session->userdata());exit;
			}else{
				$a_show_data = $mbo_student_alumni;
				$a_show_data->personal_data_name = $mbo_student_alumni->alumni_fullname;
				$a_show_data->personal_data_gender = $mbo_student_alumni->alumni_gender;
				$a_show_data->personal_data_email = $mbo_student_alumni->alumni_personal_email;
				$a_show_data->personal_data_cellular = $mbo_student_alumni->alumni_personal_cellular;
				$a_show_data->personal_data_place_of_birth = $mbo_student_alumni->alumni_place_of_birth;
				$a_show_data->personal_data_date_of_birth = $mbo_student_alumni->alumni_date_of_birth;
			}

			$this->a_page_data['personal_data_id'] = $s_personal_data_id;

			$this->a_page_data['o_personal_data'] = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
			$this->a_page_data['o_student_data'] = $a_show_data;
			$this->a_page_data['a_avatar'] = $this->File_manager->get_files($s_personal_data_id, '0bde3152-5442-467a-b080-3bb0088f6bac');
			$this->a_page_data['body'] = $this->load->view('student', $this->a_page_data, true);
		}else {
			$mba_employee_lecturer = $this->Cgm->get_class_master_lecturer(array(
				'employee_id' => $this->session->userdata('employee_id')
			));
			if (in_array($this->session->userdata('user'), ['29a17389-ed7e-11ed-ab3e-52540039e1c3', 'bf91a8a4-ee0d-11ed-ab3e-52540039e1c3'])) {
				$mba_employee_lecturer = $this->Cgm->get_class_master_lecturer();
				// print('<pre>');var_dump($mba_employee_lecturer);exit;
			}

			if (($this->session->userdata('module') == 'academic') AND ($mba_employee_lecturer)) {
				$mbo_semester_active = $this->Sem->get_active_semester();
				$this->a_page_data['semester_active'] = $mbo_semester_active;
				$this->a_page_data['empoyee_data'] = $mba_employee_lecturer[0];
				$this->a_page_data['show_all_dummy'] = (in_array($this->session->userdata('user'), ['29a17389-ed7e-11ed-ab3e-52540039e1c3', 'bf91a8a4-ee0d-11ed-ab3e-52540039e1c3'])) ? true : false;
				$this->a_page_data['body'] = $this->load->view('lecturer_dashboard', $this->a_page_data, true);
			}else{
				$this->a_page_data['student_status'] = $this->General->get_enum_values('dt_student', 'student_status');
				// $index_file = ($this->session->userdata('environment') == 'production') ? 'index' : 'dashboard';
				$index_file = 'dashboard';
				$this->a_page_data['student_statistics'] = $this->get_student_status();
				$this->a_page_data['body'] = $this->load->view($index_file, $this->a_page_data, true);
			}
		}
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function index()
	{
		if (($this->session->userdata('type') == 'student') OR ($this->session->userdata('type') == 'alumni')) {
			$s_personal_data_id = $this->session->userdata('user');
			$s_student_id = $this->session->userdata('student_id');
			$mbo_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $s_student_id))[0];
			$mbo_student_alumni = $this->Stm->get_alumni_data($this->session->userdata('student_id'));
			if (!$mbo_student_alumni) {
				// print('a');exit;
				$a_show_data = $mbo_student_data;
				$a_show_data->alumni_nickname = null;
			}else{
				$a_show_data = $mbo_student_alumni;
				$a_show_data->personal_data_name = $mbo_student_alumni->alumni_fullname;
				$a_show_data->personal_data_gender = $mbo_student_alumni->alumni_gender;
				$a_show_data->personal_data_email = $mbo_student_alumni->alumni_personal_email;
				$a_show_data->personal_data_cellular = $mbo_student_alumni->alumni_personal_cellular;
				$a_show_data->personal_data_place_of_birth = $mbo_student_alumni->alumni_place_of_birth;
				$a_show_data->personal_data_date_of_birth = $mbo_student_alumni->alumni_date_of_birth;
			}

			$list_zoom_timetable =  modules::run('academic/document/list_dir', 'zoomid_timetable');
			$this->a_page_data['list_zoom_timetable'] = $list_zoom_timetable;
			$this->a_page_data['personal_data_id'] = $s_personal_data_id;

			$this->a_page_data['o_personal_data'] = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
			$this->a_page_data['o_student_data'] = $a_show_data;
			$this->a_page_data['a_avatar'] = $this->File_manager->get_files($s_personal_data_id, '0bde3152-5442-467a-b080-3bb0088f6bac');
			$this->a_page_data['body'] = $this->load->view('student', $this->a_page_data, true);

			// if ($this->session->userdata('user') == '2c088deb-9143-4153-bdd7-7f6661fa8696') {
				// $this->a_page_data['body'] = $this->load->view('student/studyplan/table/krs_list', $this->a_page_data, true);
			// }
		}else {
			$mba_employee_lecturer = $this->Cgm->get_class_master_lecturer(array(
				'employee_id' => $this->session->userdata('employee_id')
			));

			if (in_array($this->session->userdata('user'), ['29a17389-ed7e-11ed-ab3e-52540039e1c3', 'bf91a8a4-ee0d-11ed-ab3e-52540039e1c3'])) {
				$mba_employee_lecturer = $this->Cgm->get_class_master_lecturer();
				// print('<pre>');var_dump($mba_employee_lecturer);exit;
			}

			if (($this->session->userdata('module') == 'academic') AND ($mba_employee_lecturer)) {
				$mbo_semester_active = $this->Sem->get_active_semester();
				$this->load->model('apps/Letter_numbering_model', 'Lnm');

				$s_academic_year_id = $this->session->userdata('academic_year_id_active');
				$s_semester_type_id = $this->session->userdata('semester_type_id_active');
				$mba_letter_data = $this->Lnm->get_letter_target([
					'lnt.personal_data_id' => $this->session->userdata('user'),
					'ln.academic_year_id' => $s_academic_year_id,
					'ln.semester_type_id' => $s_semester_type_id,
					'lnt.template_id' => '7'
				]);

				$mba_is_advisor_semester = $this->Tsm->get_list_student_advisor([
					'ta.personal_data_id' => $this->session->userdata('user')
				], 'advisor', true);
				// $mba_is_advisor_semester = $this->Tsm->is_advisor_examiner_defense([
				// 	'ta.personal_data_id' => $this->session->userdata('user'),
				// 	'td.academic_year_id' => $s_academic_year_id,
				// 	'td.semester_type_id' => $s_semester_type_id
				// ], 'advisor');

				$mba_is_examiner_semester = $this->Tsm->is_advisor_examiner_defense([
					'ta.personal_data_id' => $this->session->userdata('user'),
					'td.academic_year_id' => $s_academic_year_id,
					'td.semester_type_id' => $s_semester_type_id
				], 'examiner');

				$is_lect_internship = false;
				$mba_is_lect_internship = $this->Cgm->get_class_master_filtered([
					'cml.employee_id' => $this->session->userdata('employee_id'),
					'cm.academic_year_id' => $s_academic_year_id,
					'cm.semester_type_id' => $s_semester_type_id
				]);
				if ($mba_is_lect_internship) {
					foreach ($mba_is_lect_internship as $o_class_master) {
						if (strpos(strtolower($o_class_master->subject_name), 'internship') !== false) {
							// $mba_score_data = $this->Scm->get_studentscore_like_subject_name([
							// 	'sc.class_master_id' => $o_class_master->class_master_id,
							// 	'sc.score_approval' => 'approved'
							// ], 'internship');
							$mba_score_data = $this->General->get_where('dt_score', [
								'class_master_id' => $o_class_master->class_master_id,
								'score_approval' => 'approved'
							]);
							if ($mba_score_data) {
								$is_lect_internship = true;
							}
						}
					}
				}

				$this->a_page_data['is_lecturer_internship'] = $is_lect_internship;
				$this->a_page_data['is_advisor_current'] = ($mba_is_advisor_semester) ? true : false;
				$this->a_page_data['is_examiner_current'] = ($mba_is_examiner_semester) ? true : false;
				
				$s_semester = intval($s_academic_year_id.$s_semester_type_id);
				// if ($s_semester > 20211) {
				// 	$this->a_page_data['assigment_generated'] = ($mba_letter_data) ? true : false;
				// }
				$this->a_page_data['assigment_generated'] = ($s_semester > 20211) ? true : false;
				// $this->a_page_data['show_all_dummy'] = (in_array($this->session->userdata('user'), ['29a17389-ed7e-11ed-ab3e-52540039e1c3', 'bf91a8a4-ee0d-11ed-ab3e-52540039e1c3'])) ? true : false;
				// $this->a_page_data['show_all_dummy'] = (in_array($this->session->userdata('user'), ['bf91a8a4-ee0d-11ed-ab3e-52540039e1c3'])) ? true : false;
				$this->a_page_data['semester_active'] = $mbo_semester_active;
				$this->a_page_data['empoyee_data'] = $mba_employee_lecturer[0];
				$this->a_page_data['body'] = $this->load->view('lecturer_dashboard', $this->a_page_data, true);
			}
			else{
				switch ($this->session->userdata('module')) {
					case 'hris':
						redirect('hris/employee_list');
						break;
					
					default:
						$this->a_page_data['student_status'] = $this->General->get_enum_values('dt_student', 'student_status');
						$index_file = 'dashboard';
						$this->a_page_data['student_statistics'] = $this->get_student_status();
						$this->a_page_data['body'] = $this->load->view($index_file, $this->a_page_data, true);
						break;
				}
			}
		}
		$this->load->view('layout', $this->a_page_data);
	}

	public function lecturer_dashboard()
	{
		$s_personal_data_id = $this->session->userdata('user');
		$mba_employee_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $s_personal_data_id));
		if ($mba_employee_data) {
			$mbo_semester_active = $this->Sem->get_active_semester();
			$this->a_page_data['semester_active'] = $mbo_semester_active;
			$this->a_page_data['empoyee_data'] = $mba_employee_data[0];
			$this->a_page_data['body'] = $this->load->view('lecturer_dashboard', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}else{
			show_404();
		}
	}

	public function staff_dashboard()
	{
		$s_personal_data_id = $this->session->userdata('user');
		$s_employee_id = $this->session->userdata('employee_id');
		
		$mba_employee_data = $this->General->get_where('dt_employee', [
			'employee_id' => $s_employee_id,
			'department_id != ' => NULL
		]);

		if ($mba_employee_data) {
			$this->a_page_data['body'] = $this->load->view('staff_dashboard', $this->a_page_data, true);
			$this->load->view('layout_ext', $this->a_page_data);
		}
	}

	public function get_data_dashboard()
	{
		// for new dashboard
		$s_academic_year_id = $this->input->post('academic_year');
		$s_semester_type_id = $this->input->post('semester_type');
		if ((empty($s_academic_year_id)) OR (empty($s_semester_type_id))) {
			$s_academic_year_id = $this->session->userdata('academic_year_id_active');
			$s_semester_type_id = $this->session->userdata('semester_type_id_active');
		}

		$mba_admission_year = $this->General->get_where('dt_academic_year', ['academic_year_intake_status' => 'active']);
		$s_admission_year = ($mba_admission_year) ? $mba_admission_year[0]->academic_year_id : (intval($s_academic_year_id) + 1);

		// Current Student
		// $mba_student_data = $this->Stm->get_student_filtered(false, ['active']);
	}
	
	public function get_dashboard_data()
	{
		if($this->input->is_ajax_request()){
			$s_date_range = $this->input->post('date_range');
			if (!empty($s_date_range)) {
				$a_dates = explode('-', $s_date_range);
				$s_start_date = date('Y-m-d 00:00:00', strtotime(trim($a_dates[0])));
				$s_end_date = date('Y-m-d 23:59:59', strtotime(trim($a_dates[1])));
				$s_student_status = $this->input->post('student_status');
				
				$a_params = [$s_start_date, $s_end_date, $s_student_status];
				
				$a_student_stats = modules::run('student/get_total_students', $s_start_date, $s_end_date);
			}
			else {
				$a_student_stats = modules::run('student/get_total_students');
			}
			
			// print "<pre>";
			// print_r($a_student_stats);exit;
			
			print json_encode($a_student_stats);
			exit;
		}
	}

	public function get_student_status($external = false)
	{
		$mbo_active_year = $this->Adm->get_active_intake_year();
		$mbo_candidate_statistics = $this->General->get_list_candidate_status('candidate');
		$i_sum_candidate = 0;
		if ($mbo_candidate_statistics) {
			foreach ($mbo_candidate_statistics as $candidate) {
				$i_sum_candidate += intval($candidate->total);
			}
		}

		$mbo_participant_statistics = $this->General->get_list_candidate_status('participant');
		$i_sum_participant = 0;
		if ($mbo_participant_statistics) {
			foreach ($mbo_participant_statistics as $participant) {
				$i_sum_participant += intval($participant->total);
			}
		}

		$mbo_pending_statistics = $this->General->get_list_candidate_status('pending');
		$i_sum_pending = 0;
		if ($mbo_pending_statistics) {
			foreach ($mbo_pending_statistics as $pending) {
				$i_sum_pending += intval($pending->total);
			}
		}

		$mbo_active_statistics = $this->General->get_list_candidate_status('active');
		$i_sum_active = 0;
		if ($mbo_active_statistics) {
			foreach ($mbo_active_statistics as $active) {
				$i_sum_active += intval($active->total);
			}
		}

		$candidate_data = array(
			'active_year' => $mbo_active_year->academic_year_id,
			'candidate' => array(
				'total' => $i_sum_candidate,
				'list' => $mbo_candidate_statistics
			),
			'paid' => array(
				'total' => $i_sum_participant,
				'list' => $mbo_participant_statistics
			),
			'pending' => array(
				'total' => $i_sum_pending,
				'list' => $mbo_pending_statistics
			),
			'active' => array(
				'total' => $i_sum_active,
				'list' => $mbo_active_statistics
			)
		);

		if ($external) {
			print('<pre>');
			var_dump($candidate_data);exit;
		}else{
			return $candidate_data;
		}
	}

	public function get_topbarpage($top_bar = false, $s_layout = 'topmenu') {
		$mba_topmenu = ($top_bar) ? $top_bar : $this->a_page_data['top_bar'];
		$top_bar_permission = false;
		$mba_permissions = modules::run('devs/devs_employee/employee_permission');
		$top_bar_permission = (array_key_exists('topbar_list', $mba_permissions)) ? $mba_permissions['topbar_list'] : false;
		
		if ((!is_null($this->session->userdata('student_status'))) AND ($this->session->userdata('student_status') != 'graduated')) {
			unset($mba_topmenu['student_alumni']);
		}else if ((!is_null($this->session->userdata('student_status'))) AND ($this->session->userdata('student_status') == 'graduated')) {
			unset($mba_topmenu['student_finance']);
		}

		$a_newtopmenu = [];
		// print('<pre>');var_dump($mba_topmenu);exit;
		if (is_array($mba_topmenu)) {
			foreach ($mba_topmenu as $key => $value) {
				if ($value['show']) {
					if (in_array($this->session->userdata('type'), $value['allowed_user_type'])) {
						if (!is_null($value['title'])) {
							if (($top_bar_permission) AND (in_array($key, $top_bar_permission))) {
								array_push($a_newtopmenu, $value);
							}
							else if ($this->session->userdata('type') == 'student') {
								if ($key == 'student_thesis') {
									$mba_student_get_thesis_subject = $this->Scm->get_score_like_subject_name([
										'sc.student_id' => $this->session->userdata('student_id'),
										'sc.score_approval' => 'approved'
									], 'thesis');
									if ($mba_student_get_thesis_subject) {
										array_push($a_newtopmenu, $value);
									}
								}
								else if ($key == 'student_internship') {
									$mba_student_get_internship_subject = $this->Scm->get_score_like_subject_name([
										'sc.student_id' => $this->session->userdata('student_id'),
										'sc.score_approval' => 'approved'
									], 'internship');
									if ($mba_student_get_internship_subject) {
										array_push($a_newtopmenu, $value);
									}
								}
								else if ($key == 'student_ofse') {
									$mba_is_ofsemember = $this->Ofm->get_ofse_student_member([
										'sc.student_id' => $this->session->userdata('student_id'),
										'do.ofse_status' => 'active'
									]);
									if ($mba_is_ofsemember) {
										array_push($a_newtopmenu, $value);
									}
								}
								else if ($key == 'student_abroad') {
									$mba_is_abroad = $this->General->get_where('dt_student_exchange', [
										'student_id' => $this->session->userdata('student_id'),
										'exchange_type' => 'out'
									]);
									if ($mba_is_abroad) {
										array_push($a_newtopmenu, $value);
									}
								}
								else {
									array_push($a_newtopmenu, $value);
								}
							}
							else if ($this->session->userdata('type') == 'alumni') {
								array_push($a_newtopmenu, $value);
							}
							else if ($key == 'student_document') {
								array_push($a_newtopmenu, $value);
							}
						}
					}
				}
			}
		}
		// print('<pre>');var_dump($a_newtopmenu);exit;
		if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
			// print('<pre>');var_dump($top_bar_permission);exit;
		}
		$this->a_page_data['top_bar_permission'] = $top_bar_permission;
		$this->a_page_data['layout_bar'] = $s_layout;
		$this->a_page_data['topbar'] = $a_newtopmenu;
		$this->load->view('pagelayout/topbar', $this->a_page_data);
	}

	function tets() {
		$this->load->model('finance/Invoice_model','Im');
		$a_fee_filter = [
			'payment_type_code' => '04',
			'program_id' => 1,
			'study_program_id' => 'ed375a1a-81cc-11e9-bdfc-5254005d90f6',
			'academic_year_id' => '2017',
			'semester_id' => '30',
			'fee_amount_type' => 'main'
		];

		$mba_fee_data = $this->Im->get_fee($a_fee_filter);
		print('<pre>');var_dump($mba_fee_data);exit;
	}

	public function get_sidebar($side_bar = false)
	{
		$a_side_bar_allowed = array('my_personal_data', 'my_family', 'my_addess_data', 'my_academic_history', 'my_job_history', 'my_document',
			'covid_vaccine_certificate','academic_student_activity_dikti','alumni_dikti_tracer', 'company_survey'
		);

		$mba_permissions = modules::run('devs/devs_employee/employee_permission');
		$a_sidebar_user = (array_key_exists('pages_list', $mba_permissions)) ? $mba_permissions['pages_list'] : false;
		$a_newsidebar = [];
		$s_login_type = $this->session->userdata('type');
		if ((isset($this->a_page_data['side_bar'])) AND ($s_login_type != 'examiner')) {
			// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
			// 	print('<pre>');var_dump($side_bar);exit;
			// }
			$mba_sidebar_page = ($side_bar) ? $side_bar : $this->a_page_data['side_bar'];
			if ((is_array($mba_sidebar_page)) AND (count($mba_sidebar_page) > 0)) {
				foreach ($mba_sidebar_page as $value) {
					$s_name = (array_key_exists('name', $value)) ? $value['name'] : '';
					$s_title = (array_key_exists('title', $value)) ? $value['title'] : '';
					$s_url = (array_key_exists('url', $value)) ? $value['url'] : '';
					$b_allow_param = (array_key_exists('allow_param', $value)) ? $value['allow_param'] : false;
					$a_disallow_status = (array_key_exists('disallow_status', $value)) ? $value['disallow_status'] : [];
					$b_show = (array_key_exists('show', $value)) ? $value['show'] : false;
					$a_child = (array_key_exists('child', $value)) ? $value['child'] : [];
					$a_allowed_personal_data_id = (array_key_exists('allowed_personal_data_id', $value)) ? $value['allowed_personal_data_id'] : [];

					if (count($a_disallow_status) > 0) {
						if (($this->session->has_userdata('student_status')) AND (in_array($this->session->userdata('student_status'), $a_disallow_status))) {
							$b_show = false;
						}
					}

					if ((count($a_allowed_personal_data_id) > 0) AND (!in_array($this->session->userdata('user'), $a_allowed_personal_data_id))) {
						$b_show = false;
					}

					if ($b_show) {
						if(isset($value['allow_param']) AND ($value['allow_param'])){
							$k_param = $this->uri->total_segments();
							$first_param = (strlen($this->uri->segment($k_param - 1)) >= 36) ? $this->uri->segment($k_param - 1) : $this->uri->segment($k_param);
							$last_param = $this->uri->segment($k_param);
							
							if($this->router->fetch_method() != $last_param){
								if ($first_param != $last_param) {
									$value['url'] = $value['url'].'/'.$first_param.'/'.$last_param;
								}else{
									$value['url'] = $value['url'].'/'.$last_param;
								}
							}
						}

						if (count($a_child) > 0) {
							$b_show_parent = false;
							$a_child_sidebar = [];
							foreach ($a_child as $value_child) {
								$s_child_name = (array_key_exists('name', $value_child)) ? $value_child['name'] : '';
								$s_child_title = (array_key_exists('title', $value_child)) ? $value_child['title'] : '';
								$s_child_url = (array_key_exists('url', $value_child)) ? $value_child['url'] : '';
								$b_child_allow_param = (array_key_exists('allow_param', $value_child)) ? $value_child['allow_param'] : false;
								$a_child_disallow_status = (array_key_exists('disallow_status', $value_child)) ? $value_child['disallow_status'] : [];
								$b_child_show = (array_key_exists('show', $value_child)) ? $value_child['show'] : false;
								$a_child_allowed_personal_data_id = (array_key_exists('allowed_personal_data_id', $value_child)) ? $value_child['allowed_personal_data_id'] : [];
								
								if (count($a_child_disallow_status) > 0) {
									if (($this->session->has_userdata('student_status')) AND (in_array($this->session->userdata('student_status'), $a_child_disallow_status))) {
										$b_child_show = false;
									}
								}
			
								if ((count($a_child_allowed_personal_data_id) > 0) AND (!in_array($this->session->userdata('user'), $a_child_allowed_personal_data_id))) {
									$b_child_show = false;
								}

								
								if ($b_child_show) {
									if (in_array($value_child['name'], $a_side_bar_allowed)) {
										array_push($a_child_sidebar, $value_child);
										$b_show_parent = true;
									}
									else if (($a_sidebar_user) AND (in_array($value_child['name'], $a_sidebar_user))) {
										array_push($a_child_sidebar, $value_child);
										$b_show_parent = true;
									}
									else if (($this->session->userdata('type') == 'student') OR ($this->session->userdata('type') == 'alumni')) {
										array_push($a_child_sidebar, $value_child);
										$b_show_parent = true;
									}
								}
							}

							if ($b_show_parent) {
								$value['child'] = $a_child_sidebar;
								array_push($a_newsidebar, $value);
							}
						}
						else {
							if (in_array($value['name'], $a_side_bar_allowed)) {
								array_push($a_newsidebar, $value);
							}
							else if (($a_sidebar_user) AND (in_array($value['name'], $a_sidebar_user))) {
								array_push($a_newsidebar, $value);
							}
							else if (($this->session->userdata('type') == 'student') OR ($this->session->userdata('type') == 'alumni')) {
								array_push($a_newsidebar, $value);
							}
						}
					}
				}
			}
		}
		if ($this->session->userdata('employee_id') == '4e2b8186-8e7b-4726-a1f5-e280d4ac0825') {
			// print('<pre>');var_dump($a_sidebar_user);exit;
		}
		$this->a_page_data['sidebar'] = $a_newsidebar;
		$this->load->view('pagelayout/sidebar', $this->a_page_data);
	}
}
