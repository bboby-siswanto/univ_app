<?php
class Academic_destroyed extends App_core
{
	public function __construct()
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

		$this->load->model('academic/Class_group_model', 'Cgm');
		$this->load->model('academic/Score_model', 'Scm');
	}

	public function get_student_absence()
	{
		$i = 0;
		$mba_student_score = $this->Pm->retrieve_data('score_absence', array('approval' => 'APPROVED'));
		if ($mba_student_score) {
			foreach ($mba_student_score as $absence) {
				$mbo_student_data = $this->Pm->retrieve_data('student', array('id' => $absence->student_id, 'status' => 'ACTIVE'))[0];
				if ($mbo_student_data) {
					$mbo_class_member = $this->Pm->retrieve_data('class_group_member', array('id' => $absence->class_group_member_id))[0];
					if ($mbo_class_member) {
						$mbo_class_group = $this->Pm->retrieve_data('class_group', array('id' => $mbo_class_member->class_group_id, 'academic_year_id' => '6', 'semester_type' => 'ODD'))[0];
						if ($mbo_class_group) {
							if (!is_null($mbo_class_group->team_teaching)) {
								$a_lecturer = array();
								$mba_class_group = $this->Pm->retrieve_data('class_group', array('id' => $mbo_class_group->team_teaching));
								if ($mba_class_group) {
									// $mbo_lecturer = $this->Pm->retrieve_data('personal_data')
								}
							}

							// $data;
						}
					}
				}
			}
		}
		var_dump($i);
	}

	public function academic_sync()
	{
		$this->bob_sync_subject();
		$this->bob_sync_curriculum();
		$this->bob_sync_offer_subject();
	}

	public function get_class_available($s_portal_class_group_id = false)
	{
		if ($s_portal_class_group_id) {
			$mbo_portal_class_group_lists = $this->Pm->retrieve_data('class_group',array('id' => $s_portal_class_group_id, 'team_teaching' => null));
		}else{
			$mbo_portal_class_group_lists = $this->Pm->retrieve_data('class_group', array('team_teaching' => null));
		}

		$a_class_available = array();

		if ($mbo_portal_class_group_lists) {
			$a_portal_class_notfound = array();
			foreach ($mbo_portal_class_group_lists as $class) {
				$mbo_portal_class_member = $this->Pm->retrieve_data('class_group_member', array('class_group_id' => $class->id));
				if ($mbo_portal_class_member) {
					foreach ($mbo_portal_class_member as $class_member) {
						$mbo_portal_class_score = $this->Pm->retrieve_data('score_absence', array('class_group_member_id' => $class_member->id));
						if (!$mbo_portal_class_score) {
							if (!in_array($class_member->class_group_id, $a_portal_class_notfound)) {
								array_push($class_member->class_group_id);
							}
						}

						$mbo_staging_implemented_subject_data = $this->Sm->retrieve_data('dt_offered_subject', array('portal_id' => $class_member->implemented_subject_id))[0];
						if (!$mbo_staging_implemented_subject_data) {
							if (!$this->sync_offered_subject_data($class_member->implemented_subject_id)) {
								if (!in_array($class_member->class_group_id, $a_portal_class_notfound)) {
									array_push($class_member->class_group_id);
								}
							}
						}
					}
				}else if (!in_array($class->id, $a_portal_class_notfound)) {
					array_push($class->id);
				}
			}
		}
	}

	public function sync_offered_subject_data($s_portal_implemented_subject_data = false)
	{
		print('<pre>Syncronize offered subject</pre>');
		if ($s_portal_implemented_subject_data) {
			$mbo_portal_implemented_subject_lists = $this->Pm->retrieve_data('implemented_subject', array('implemented_subject_id' => $s_portal_implemented_subject_data));
		}else{
			$mbo_portal_implemented_subject_lists = $this->Pm->retrieve_data('implemented_subject');
		}

		if ($mbo_portal_implemented_subject_lists) {
			# code...
		}
	}

	public function get_ipk()
	{
		
		$mbo_student_data = $this->Pm->retrieve_data('student', array('status' => 'GRADUATED'));
		$student_data = array();
		// print('<pre>');
		
		foreach ($mbo_student_data as $stu) {
			$score_data = $this->Pm->get_score_student($stu->id);
			$personal_data = $this->Pm->retrieve_data('personal_data', array('id' => $stu->personal_data_id))[0];
			$study_program = $this->Pm->retrieve_data('study_program', array('id' => $stu->study_program_id))[0];

			$total_merit = 0;
			$total_sks = 0;
			foreach ($score_data as $score) {
				$grade_point = $this->grades->get_grade_point($score->score_sum);
				
				$total_merit += ($score->sks_credit * $grade_point);
				$total_sks += $score->sks_credit;
			}

			$IPK = $total_merit / $total_sks;
			array_push($student_data, array(
				'Student ID' => $stu->id,
				'Nama Lengkap' => $personal_data->fullname,
				'Study Program' => $study_program->name,
				'Total SKS' => $total_sks,
				'Total Merit' => $total_merit,
				'IPK' => $IPK
			));
		}

		// print('<pre>');print_r($student_data);
		$csvFileName = 'student_ipk.csv';
		$folder = '0470f7cf-f59e-415a-b973-d86cf7c29cef';
		$fp = fopen(APPPATH.'uploads/0470f7cf-f59e-415a-b973-d86cf7c29cef/'.$csvFileName, 'w');
		foreach($student_data as $row){
			fputcsv($fp, $row);
		}
		fclose($fp);

		$s_file_path = APPPATH.'uploads/0470f7cf-f59e-415a-b973-d86cf7c29cef/'.$csvFileName;

		$a_path_info = pathinfo($s_file_path);
		$s_file_ext = $a_path_info['extension'];
		$s_download_filename = str_replace(' ', '_', implode('-', array($folder, $csvFileName))).'.'.$s_file_ext;
		header("Content-Type: application/vnd-ms-excel");
		
		header('Content-Disposition: attachment; filename='.urlencode($s_download_filename));
		
		readfile( $s_file_path );
		exit;
	}

	public function bob_sync_offer_subject()
	{
		$this->load->model('academic/Offered_subject_model', 'Osm');
		$a_curriculum_subject_notfound = array(286, 287, 289, 291, 292, 293, 294, 295, 296, 298);
		
		$mbo_curriculum_subject_notfound_data = $this->Pm->retrieve_data('implemented_subject', 'curriculum_subject_id NOT IN (SELECT id FROM curriculum_subject)');
		foreach ($mbo_curriculum_subject_notfound_data as $notfound) {
			array_push($a_curriculum_subject_notfound, $notfound->curriculum_subject_id);
		}

		$mbo_curriculum_subject_notfound_semester = $this->Pm->retrieve_data('implemented_subject', 'curriculum_subject_id IN (SELECT id FROM curriculum_subject WHERE semester_id=0)');
		foreach ($mbo_curriculum_subject_notfound_semester as $semester_noll) {
			array_push($a_curriculum_subject_notfound, $semester_noll->curriculum_subject_id);
		}
		$mbo_portal_offer_subject_data = $this->Pm->retrieve_data('implemented_subject');

		$this->db->trans_start();
		print('<br><b>syncronize offered subject data</b>');
		foreach ($mbo_portal_offer_subject_data as $offer_subject) {
			if (!is_null($offer_subject->curriculum_subject_id)) {
				if (!is_null($offer_subject->study_program_id)) {
					if (!in_array($offer_subject->curriculum_subject_id, $a_curriculum_subject_notfound)) {
						$s_offered_subject_id = $this->uuid->v4();
						$mbo_staging_curriculum_subject = $this->Sm->retrieve_data('ref_curriculum_subject', array('portal_id' => $offer_subject->curriculum_subject_id))[0];
						if (!$mbo_staging_curriculum_subject) {
							var_dump('a'.$offer_subject->curriculum_subject_id);exit;
						}
						$mbo_portal_academic_year = $this->Pm->retrieve_data('academic_year', array('id' => $offer_subject->academic_year_id))[0];
						$mbo_staging_semester = $this->Sm->retrieve_data('ref_semester', array('semester_id' => $offer_subject->semester_id))[0];
						$mbo_portal_prodi_data = $this->Pm->retrieve_data('study_program', array('id' => $offer_subject->study_program_id))[0];
						if (!$mbo_portal_prodi_data) {
							var_dump('b'.$offer_subject->study_program_id);exit;
						}
						$mbo_staging_prodi_data = $this->Sm->retrieve_data('ref_study_program', array('study_program_name' => $mbo_portal_prodi_data->name))[0];
						$a_staging_offered_subject_data = array(
							'offered_subject_id' => $s_offered_subject_id,
							'curriculum_subject_id' => $mbo_staging_curriculum_subject->curriculum_subject_id,
							'academic_year_id' => $mbo_portal_academic_year->year_name,
							'semester_type_id' => $mbo_staging_semester->semester_type_id,
							'program_id' => '1',
							'study_program_id' => $mbo_staging_prodi_data->study_program_id,
							'portal_id' => $offer_subject->id
						);
						
						if ($cek_data = $this->Sm->retrieve_data('dt_offered_subject', array('portal_id' => $offer_subject->id))) {
							if ($save_data = $this->Osm->update_sync_offered_subject($a_staging_offered_subject_data, $cek_data[0]->offered_subject_id)) {
								print('* ');
							}else{
								print($save_data);exit;
							}
						}else{
							// var_dump($this->db->last_query());exit;
							if ($save_data = $this->Osm->save_offer_subject($a_staging_offered_subject_data)) {
								print('. ');
							}else{
								print($save_data);exit;
							}
						}
					}
				}
			}
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			print('<b>Offered Subject Rollback</b>');
		}else{
			$this->db->trans_commit();
			print('<b>Offered Subject Commit</b>');
		}
	}

	public function bob_sync_curriculum()
	{
		$this->load->model('academic/Curriculum_model', 'Csm');
		$a_prodi_in_null = array(0, 18);
		$mbo_portal_curriculum_data = $this->Pm->retrieve_data('curriculum');

		$this->db->trans_start();
		print('<br><b>syncronize curriculum data</b>');
		foreach ($mbo_portal_curriculum_data as $curriculum) {
			$s_curriculum_id = $this->uuid->v4();

			if(!in_array($curriculum->study_program_id, $a_prodi_in_null)) {
				$mbo_portal_prodi_data = $this->Pm->retrieve_data('study_program', array('id' => $curriculum->study_program_id))[0];
				$mbo_staging_prodi_data = $this->Sm->retrieve_data('ref_study_program', array('study_program_name' => $mbo_portal_prodi_data->name))[0];
				if (!$mbo_staging_prodi_data) {
					var_dump($mbo_portal_prodi_data);exit;
				}
				$mbo_portal_academic_year = $this->Pm->retrieve_data('academic_year', array('id' => $curriculum->academic_year_id))[0];
				$s_academic_year_id = $mbo_portal_academic_year->year_name;
				$s_study_program_id = $mbo_staging_prodi_data->study_program_id;

				$a_curriculum_data = array(
					'curriculum_id' => $s_curriculum_id,
					'study_program_id' => $s_study_program_id,
					'program_id' => '1',
					'academic_year_id' => $s_academic_year_id,
					'valid_academic_year' => $s_academic_year_id,
					'curriculum_name' => $curriculum->name,
					'portal_id' => $curriculum->id
				);

				if ($mbo_staging_curriculum_data = $this->Sm->retrieve_data('ref_curriculum', array('portal_id' => $curriculum->id))[0]) {
					unset($a_curriculum_data['curriculum_id']);
					$s_curriculum_id = $mbo_staging_curriculum_data->curriculum_id;
					$this->Csm->update_curriculum($a_curriculum_data, $s_curriculum_id);
					print('c');
				}else{
					$this->Csm->create_new_curriculum($a_curriculum_data);
					print('d');
				}

				$mbo_portal_curriculum_subject_data = $this->Pm->retrieve_data('curriculum_subject', array('curriculum_id' => $curriculum->id));
				if ($mbo_portal_curriculum_subject_data) {
					foreach ($mbo_portal_curriculum_subject_data as $curr_subject) {
						if ($curr_subject->semester_id != '0') {
							$s_curriculum_subject_id = $this->uuid->v4();
							$mbo_portal_subject_data = $this->Pm->retrieve_data('subject', array('id' => $curr_subject->subject_id))[0];
							$mbo_staging_subject_data = $this->Sm->retrieve_data('ref_subject', array('portal_id' => $mbo_portal_subject_data->id))[0];
							if (!$mbo_staging_subject_data) {
								// print('Staging subject not found');
								// var_dump($mbo_portal_subject_data->id);
								continue;
							}
							$mbo_staging_subject_name_data = $this->Sm->retrieve_data('ref_subject_name', array('subject_name_id' => $mbo_staging_subject_data->subject_name_id))[0];
							$mbo_staging_semester_data = $this->Sm->retrieve_data('ref_semester', array('semester_id' => $curr_subject->semester_id))[0];
							if (!$mbo_staging_semester_data) {
								var_dump($this->db->last_query());exit;
							}
							$s_staging_curriculum_subject_code = modules::run('curriculum/generate_curriculum_subject_code', $mbo_staging_subject_name_data->subject_name_code, $mbo_staging_prodi_data->study_program_abbreviation, $curr_subject->semester_id, $mbo_staging_semester_data->semester_type_id);

							$a_curriculum_subject_data = array(
								'curriculum_subject_id' => $s_curriculum_subject_id,
								'curriculum_id' => $s_curriculum_id,
								'semester_id' => $curr_subject->semester_id,
								'subject_id' => $mbo_staging_subject_data->subject_id,
								'curriculum_subject_code' => $s_staging_curriculum_subject_code,
								'curriculum_subject_credit' => $curr_subject->sks,
								'curriculum_subject_ects' => ($curr_subject->ects != NULL) ? $curr_subject->ects : round((intval($curr_subject->sks) * 1.4), 2),
								'curriculum_subject_type' => strtolower($curr_subject->subject_type),
								'portal_id' => $curr_subject->id
							);

							$a_curriculum_semester_data = array(
								'curriculum_id' => $s_curriculum_id,
								'semester_id' => $curr_subject->semester_id
							);

							if ($mbo_staging_curriculum_semester_data = $this->Sm->retrieve_data('ref_curriculum_semester', array('curriculum_id' => $s_curriculum_id, 'semester_id' => $curr_subject->semester_id))) {
								$this->Csm->update_credit_count_curriculum_semester($s_curriculum_id, $curr_subject->semester_id, strtolower($curr_subject->subject_type));
							}else{
								$this->Csm->create_new_curriculum_semester($a_curriculum_semester_data);
							}

							if ($mbo_staging_curriculum_subject_data = $this->Sm->retrieve_data('ref_curriculum_subject', array('portal_id' => $curr_subject->id))[0]) {
								$this->Csm->update_curriculum_subject($a_curriculum_subject_data, $mbo_staging_curriculum_subject_data->curriculum_subject_id);
								print('. ');
							}else{
								$this->Csm->create_new_curriculum_subject($a_curriculum_subject_data);
								print('* ');
							}
						}
					}
					
					$this->Csm->update_credit_curriculum($s_curriculum_id);
				}
			}
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			print('<p><b>Curriculum rollback</b></p>');
		}else{
			$this->db->trans_commit();
			print('<p><b>Curriculum commit</b></p>');
		}
		// print('<pre>');var_dump($mbo_portal_curriculum_data);exit;
	}

	public function bob_sync_subject()
	{
		$this->load->model('academic/Subject_model', 'Sbm');
		$mbo_portal_subject_data = $this->Pm->retrieve_data('subject');

		$this->db->trans_start();
		print('<br><b>syncronize subject data</b>');
		foreach ($mbo_portal_subject_data as $subject) {
			$portal_subject_id = $subject->id;
			$s_subject_id = $this->uuid->v4();
			$mbo_portal_subject_name = $this->Pm->retrieve_data('subject_name', array('id' => $subject->subject_name_id));
			$mbo_portal_prodi_data = $this->Pm->retrieve_data('study_program', array('id' => $subject->study_program_id, 'status' => 'ACTIVE'))[0];
			if ($mbo_portal_prodi_data) {
				$mbo_staging_prodi_data = $this->Sm->retrieve_data('ref_study_program', array('study_program_name' => $mbo_portal_prodi_data->name));
				if (!$mbo_staging_prodi_data) {
					var_dump($mbo_portal_prodi_data->name);exit;
				}
				
				if ($mbo_portal_subject_name) {
					$a_subject_name = $mbo_portal_subject_name[0];
					$s_subject_name_id = $this->uuid->v4();
					$subject_name_code = modules::run('curriculum/subject/generate_subject_name_code', $a_subject_name->name);
					$s_subject_code = modules::run('curriculum/subject/generate_subject_code', $subject_name_code, $mbo_staging_prodi_data[0]->study_program_id, $subject->sks);
					$a_subject_name_data = array(
						'portal_id' => $a_subject_name->id,
						'subject_name_id' => $s_subject_name_id,
						'subject_name' => $a_subject_name->name,
						'subject_name_code' => $subject_name_code
					);
					
					$a_subject_data = array(
						'subject_id' => $s_subject_id,
						'subject_name_id' => $s_subject_name_id,
						'subject_code' => $s_subject_code,
						'study_program_id' => ($mbo_staging_prodi_data) ? $mbo_staging_prodi_data[0]->study_program_id : NULL,
						'program_id' => '1',
						'subject_credit' => $subject->sks,
						'subject_credit_tm' => $subject->sks,
						'portal_id' => $portal_subject_id
					);

					if ($subject_name_data = $this->Sm->retrieve_data('ref_subject_name', array('portal_id' => $a_subject_name->id))) {
						if ($this->Sbm->save_subject_name($a_subject_name_data, $subject_name_data[0]->subject_name_id)) {
							if ($mbo_subject_data = $this->Sm->retrieve_data('ref_subject', array('portal_id' => $portal_subject_id))) {
								unset($a_subject_data['subject_code']);
								if ($this->Sbm->save_subject_data($a_subject_data, $mbo_subject_data[0]->subject_id)) {
									print('- ');
								}else{
									print('x ');
								}
							}else{
								if ($this->Sbm->save_subject_data($a_subject_data)) {
									print('. ');
								}else{
									print('x ');
								}
							}
						}else{
							print('-x-');
						}
					}else{
						if ($this->Sbm->save_subject_name($a_subject_name_data)) {
							$mbo_subject_staging_data = $this->Sm->retrieve_data('ref_subject', array('portal_id' => $portal_subject_id));
							if ($mbo_subject_staging_data) {
								unset($a_subject_data['subject_id']);
								unset($a_subject_data['subject_code']);
								if ($this->Sbm->save_subject_data($a_subject_data, $mbo_subject_staging_data[0]->subject_id)) {
									print('- ');
								}else{
									print('x');
								}
							}else{
								if ($this->Sbm->save_subject_data($a_subject_data)) {
									print('. ');
								}else{
									print('x ');
								}
							}
						}else{
							print('x');
						}
					}
				}
			}
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			print('<p><b>Subject rollback</b></p>');
		}else{
			$this->db->trans_commit();
			print('<p><b>Subject commit</b></p>');
		}
	}

	public function sync_score_portal($s_portal_class_member_id = false, $s_staging_class_group_id = false)
	{
		if ($s_portal_class_member_id) {
			$mbo_score_data = $this->Pm->retrieve_data('score_absence', array('class_group_member_id' => $s_portal_class_member_id));
			if (!$mbo_score_data) {
				// print($s_portal_class_member_id.' ');
			}else{
				foreach ($mbo_score_data as $score) {
					$mbo_portal_student_data = $this->Pm->retrieve_data('student', array('id' => $score->student_id))[0];
					$mbo_staging_student_data = $this->Sm->retrieve_data('dt_student', array('student_email' => $mbo_portal_student_data->iulimail))[0];
					if (!$mbo_staging_student_data) {
						var_dump('portal_class_member_id='.$s_portal_class_member_id);
						var_dump('staging_class_member_id='.$s_staging_class_group_id);
						var_dump($mbo_portal_student_data->iulimail);exit;
					}
					$mbo_portal_implemented_subject_data = $this->Pm->retrieve_data('implemented_subject', array('id' => $score->implemented_subject_id))[0];
					if ($mbo_portal_implemented_subject_data) {
						$mbo_staging_curriculum_subject_data = $this->Sm->retrieve_data('ref_curriculum_subject', array('portal_id' => $mbo_portal_implemented_subject_data->curriculum_subject_id))[0];
						if ($mbo_staging_curriculum_subject_data) {
							$mbo_portal_academic_year_data = $this->Pm->retrieve_data('academic_year', array('id' => $score->academic_year_id))[0];
							$s_score_id = $this->uuid->v4();

							$mbo_portal_implemented_subject = $this->Pm->retrieve_data('implemented_subject', array('id' => $score->implemented_subject_id))[0];
							$i_sks = $mbo_portal_implemented_subject->sks;
							$i_grade_point = $this->grades->get_grade_point($score->score_sum);
							$a_score_data = array(
								'score_id' => $s_score_id,
								'class_group_id' => $s_staging_class_group_id,
								'student_id' => $mbo_staging_student_data->student_id,
								'curriculum_subject_id' => $mbo_staging_curriculum_subject_data->curriculum_subject_id,
								'semester_id' => $score->semester_id,
								'academic_year_id' => $mbo_portal_academic_year_data->year_name,
								'score_quiz' => $score->quiz,
								'score_quiz1' => $score->q1,
								'score_quiz2' => $score->q2,
								'score_quiz3' => $score->q3,
								'score_quiz4' => $score->q4,
								'score_quiz5' => $score->q5,
								'score_quiz6' => $score->q6,
								'score_final_exam' => $score->final_exam,
								'score_repetition_exam' => $score->repetition_exam,
								'score_mark_for_repetition' => $score->mark_for_repetition,
								'score_sum' => (is_null($score->score_sum)) ? 0 : $score->score_sum,
								'score_grade' => (is_null($score->grade_point)) ? $this->grades->get_grade($score->score_sum) : $score->grade_point,
								'score_grade_point' => $i_grade_point,
								'score_ects' => round(($i_sks * 1.4), 2, PHP_ROUND_HALF_UP),
								'score_absence' => (is_null($score->absence)) ? 0 : $score->absence,
								'score_merit' => $i_sks * $i_grade_point,
								'score_approval' => (is_null($score->approval)) ? 'pending' : $score->approval,
								'score_display' => $score->score_display,
								'portal_id' => $score->id
							);

							if ($mbo_staging_score_data = $this->Sm->retrieve_data('dt_score', array('portal_id' => $score->id))) {
								$this->Scm->save_data($a_score_data, array('score_id' => $mbo_staging_score_data[0]->score_id));
							}else{
								$this->Scm->save_data($a_score_data);
							}
						}
					}
				}
			}
		}
	}
	
	public function sync_score($s_academic_year_id)
	{
		print "<pre>";
		$this->load->model('academic/Curriculum_model', 'Cm');
		$mba_score_data = $this->Cm->get_score_data($s_academic_year_id);
		// var_dump($mba_score_data);exit;
		
		foreach($mba_score_data as $score){
			var_dump($score);exit;
		}
	}
	
	public function sync_curriculum_subject($s_portal_curriculum_id, $s_staging_curriculum_id)
	{
		$this->load->model('curriculum/Curriculum_model', 'Cm');
		
		$a_total_curriculum_mandatory_sks = array();
		$a_total_curriculum_elective_sks = array();
		$a_total_curriculum_extracurricular_sks = array();
		
		for($i = 1; $i <= 8; $i++){
			$mba_portal_curriculum_subject = $this->Pm->retrieve_data('curriculum_subject', array('curriculum_id' => $s_portal_curriculum_id, 'semester_id' => $i));
			
			if($mba_portal_curriculum_subject){
				
				$mba_staging_curriculum_semester = $this->Sm->retrieve_data('ref_curriculum_semester', array('curriculum_id' => $s_staging_curriculum_id, 'semester_id' => $i));
				$a_curriculum_semester_data = array(
					'curriculum_id' => $s_staging_curriculum_id,
					'semester_id' => $i
				);
				
				if(!$mba_staging_curriculum_semester){
					$this->Cm->create_new_curriculum_semester($a_curriculum_semester_data);
				}
				
				$a_total_semester_mandatory_sks = array();
				$a_total_semester_elective_sks = array();
				$a_total_semester_extracurricular_sks = array();
				
				foreach($mba_portal_curriculum_subject as $curriculum_subject){
					$mba_subject_data = $this->Pm->retrieve_data('subject', array('id' => $curriculum_subject->subject_id));
					$s_subject_id = $this->sync_subject($mba_subject_data[0]);
					
					switch($curriculum_subject->subject_type)
					{
						case "MANDATORY":
							array_push($a_total_semester_mandatory_sks, $curriculum_subject->sks);
							break;
							
						case "ELECTIVE":
							array_push($a_total_semester_elective_sks, $curriculum_subject->sks);
							break;
							
						case "EXTRACURRICULAR":
							array_push($a_total_semester_extracurricular_sks, $curriculum_subject->sks);
							break;
					}
					
					$a_curriculum_subject_data = array(
						'subject_id' => $s_subject_id,
						'curriculum_id' => $s_staging_curriculum_id,
						'semester_id' => $i,
						'curriculum_subject_credit' => $curriculum_subject->sks,
						'curriculum_subject_ects' => ($curriculum_subject->sks * 1.4),
						'curriculum_subject_type' => strtolower($curriculum_subject->subject_type)
					);
					
					$a_staging_curriculum_subject = $this->Sm->retrieve_data('ref_curriculum_subject', array(
						'curriculum_id' => $s_staging_curriculum_id,
						'semester_id' => $i,
						'subject_id' => $s_subject_id
					));
					
					if($a_staging_curriculum_subject){
						$s_curriculum_subject_id = $a_staging_curriculum_subject[0]->curriculum_subject_id;
						$this->Cm->update_curriculum_subject($a_curriculum_subject_data, $s_curriculum_subject_id);
					}
					else{
						$this->Cm->create_new_curriculum_subject($a_curriculum_subject_data);
					}
				}
				
				$i_total_semester_mandatory_sks = array_sum($a_total_semester_mandatory_sks);
				$i_total_semester_elective_sks = array_sum($a_total_semester_elective_sks);
				$i_total_semester_extracurricular_sks = array_sum($a_total_semester_extracurricular_sks);
				
				array_push($a_total_curriculum_mandatory_sks, $i_total_semester_mandatory_sks);
				array_push($a_total_curriculum_elective_sks, $i_total_semester_elective_sks);
				array_push($a_total_curriculum_extracurricular_sks, $i_total_semester_extracurricular_sks);
				
				$a_curriculum_semester_data['curriculum_semester_total_credit_mandatory'] = $i_total_semester_mandatory_sks;
				$a_curriculum_semester_data['curriculum_semester_total_credit_mandatory_fixed'] = $i_total_semester_mandatory_sks;
				$a_curriculum_semester_data['curriculum_semester_total_credit_elective'] = $i_total_semester_elective_sks;
				$a_curriculum_semester_data['curriculum_semester_total_credit_elective_fixed'] = $i_total_semester_elective_sks;
				$a_curriculum_semester_data['curriculum_semester_total_credit_extracurricular'] = $i_total_semester_extracurricular_sks;
				$a_curriculum_semester_data['curriculum_semester_total_credit_extracurricular_fixed'] = $i_total_semester_extracurricular_sks;
				
				$this->Cm->update_curriculum_semester($a_curriculum_semester_data, array('curriculum_id' => $s_staging_curriculum_id, 'semester_id' => $i));
			}
		}
		
		$i_total_curriculum_mandatory_sks = array_sum($a_total_curriculum_mandatory_sks);
		$i_total_curriculum_elective_sks = array_sum($a_total_curriculum_elective_sks);
		$i_total_curriculum_extracurricular_sks = array_sum($a_total_curriculum_extracurricular_sks);
		
		return array('mandatory_sks' => $i_total_curriculum_mandatory_sks, 'elective_sks' => $i_total_curriculum_elective_sks, 'extracurricular_sks' => $i_total_curriculum_extracurricular_sks);
	}
	
	public function sync_curriculum()
	{
		print "<pre>";
		$this->load->model('curriculum/Curriculum_model', 'Cm');
		
		$mba_portal_curriculum = $this->Pm->retrieve_data('curriculum');
		foreach($mba_portal_curriculum as $curriculum){
			// print "Curriculum: "
			$mba_portal_study_program = $this->Pm->retrieve_data('study_program', array('id' => $curriculum->study_program_id));
			$mba_portal_academic_year = $this->Pm->retrieve_data('academic_year', array('id' => $curriculum->academic_year_id));
			
			$mba_staging_curriculum = $this->Sm->retrieve_data('ref_curriculum', array('curriculum_name' => $curriculum->name));
			
			if($mba_portal_study_program){
				$mba_staging_study_program = $this->Sm->retrieve_data('ref_study_program', array('study_program_abbreviation' => $mba_portal_study_program[0]->abbreviation));
				$s_study_program_id = $mba_staging_study_program[0]->study_program_id;
				$s_academic_year_id = $mba_portal_academic_year[0]->year_name;
				
				$a_curriculum_data = array(
					'curriculum_name' => $curriculum->name,
					'study_program_id' => $s_study_program_id,
					'academic_year_id' => $s_academic_year_id
				);
				
				if($mba_staging_curriculum){
					$s_curriculum_id = $mba_staging_curriculum[0]->curriculum_id;
					$this->Cm->update_curriculum($a_curriculum_data, $s_curriculum_id);
				}
				else{
					$s_curriculum_id = $this->Cm->create_new_curriculum($a_curriculum_data);
				}
				$a_total_sks = $this->sync_curriculum_subject($curriculum->id, $s_curriculum_id);
				
				$a_curriculum_data['curriculum_total_credit_mandatory'] = $a_total_sks['mandatory_sks'];
				$a_curriculum_data['curriculum_total_credit_mandatory_fixed'] = $a_total_sks['mandatory_sks'];
				$a_curriculum_data['curriculum_total_credit_elective'] = $a_total_sks['elective_sks'];
				$a_curriculum_data['curriculum_total_credit_elective_fixed'] = $a_total_sks['elective_sks'];
				$a_curriculum_data['curriculum_total_credit_extracurricular'] = $a_total_sks['extracurricular_sks'];
				$a_curriculum_data['curriculum_total_credit_extracurricular_fixed'] = $a_total_sks['extracurricular_sks'];
				
				$this->Cm->update_curriculum($a_curriculum_data, $s_curriculum_id);
			}
		}
	}
}