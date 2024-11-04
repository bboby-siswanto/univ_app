<?php
class Studentdestroyed extends Api_core
{
	private $pdb;
	
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
				$this->load->model('Admission_model', 'Sm');
				break;
		}

		$this->load->model('Portal_model', 'Pm');
		$this->load->model('student/Student_model', 'Stm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('employee/Employee_model', 'Emm');
		$this->load->model('address/Address_model', 'Adm');
		$this->load->model('personal_data/family_model', 'Fmm');
		$this->load->model('institution/Institution_model', 'Im');
		$this->session->set_userdata('bypass_everything', true);
	}

	public function sync_student()
	{
		print('<b>Syncronize student data');
		$i = 0;$start = strtotime("now");
        echo '<br>proccessing <span id="test">'.$i.'</span> data';
		$mba_portal_student_data = $this->Pm->retrieve_data('student');
		$this->db->trans_start();
		foreach($mba_portal_student_data as $student){
			if ((!is_null($student->study_program_id)) OR ($student->study_program_id != 0)) {
				if ($student->study_program_id != 0) {
					$mba_portal_personal_data = $this->Pm->retrieve_data('personal_data', array('id' => $student->personal_data_id))[0];
					$mba_portal_academic_year_data = $this->Pm->retrieve_data('academic_year', array('id' => $student->academic_year_id))[0];
					$mba_portal_study_program_data = $this->Pm->retrieve_data('study_program', array('id' => $student->study_program_id))[0];
					
					$s_student_id = (is_null($student->id_feeder)) ? $this->uuid->v4() : $student->id_feeder;
					$s_personal_data_id = (is_null($student->id_reg_pd)) ? $this->uuid->v4() : $student->id_reg_pd;
					
					$mba_staging_personal_data = $this->Sm->retrieve_data('dt_personal_data', array('portal_id' => $mba_portal_personal_data->id));
					$mba_staging_student_data = $this->Sm->retrieve_data('dt_student', array('portal_id' => $student->id));
					$mba_staging_study_program_data = $this->Sm->retrieve_data('ref_study_program', array('study_program_abbreviation' => $mba_portal_study_program_data->abbreviation))[0];
					$mba_portal_country_birth_data = $this->Pm->retrieve_data('country', array('id' => ((is_null($mba_portal_personal_data->birth_country_id)) OR ($mba_portal_personal_data->birth_country_id == 0)) ? '102' : $mba_portal_personal_data->birth_country_id))[0];
					$mba_staging_country_birth_data = $this->Sm->retrieve_data('ref_country', array('country_code' => $mba_portal_country_birth_data->shortname))[0];
					$mba_portal_country_citizen_data = $this->Pm->retrieve_data('country', array('id' => ((is_null($mba_portal_personal_data->country_id)) OR ($mba_portal_personal_data->country_id == 0)) ? '102' : $mba_portal_personal_data->country_id))[0];
					$mba_staging_country_citizen_data = $this->Sm->retrieve_data('ref_country', array('country_code' => $mba_portal_country_citizen_data->shortname))[0];
					$mba_portal_religion_data = $this->Pm->retrieve_data('religion', array('id' => ((is_null($mba_portal_personal_data->religion_id)) OR ($mba_portal_personal_data->religion_id == 0)) ? '7' : $mba_portal_personal_data->religion_id))[0];
					$mba_staging_religion_data = $this->Sm->retrieve_data('ref_religion', array('religion_feeder_id' => $mba_portal_religion_data->id_feeder))[0];
					$mba_portal_mother_data = $this->Pm->retrieve_data('parents', array('student_id' => $student->id));
					
					$a_personal_data = array(
						'personal_data_id' => $s_personal_data_id,
						'personal_data_name' => trim(strtoupper(str_replace('  ', ' ', implode(' ', array(
							$mba_portal_personal_data->firstname, 
							$mba_portal_personal_data->middlename,
							$mba_portal_personal_data->lastname
						))))),
						'personal_data_email' => (!is_null($mba_portal_personal_data->personal_email)) ? $mba_portal_personal_data->personal_email : $s_personal_data_id.'@domain.com',
						'personal_data_cellular' => (!is_null($mba_portal_personal_data->personal_mobilephone)) ? $mba_portal_personal_data->personal_mobilephone : '0',
						'personal_data_email_confirmation' => 'yes',
						'country_of_birth' => (is_null($mba_portal_personal_data->birth_country_id)) ? NULL : $mba_staging_country_birth_data->country_id,
						'citizenship_id' => (is_null($mba_portal_personal_data->country_id)) ? NULL : $mba_staging_country_citizen_data->country_id,
						'religion_id' => (is_null($mba_portal_personal_data->religion_id)) ? NULL : $mba_staging_religion_data->religion_id,
						'personal_data_id_card_number' => (is_null($mba_portal_personal_data->idcard_number)) ? NULL : $mba_portal_personal_data->idcard_number,
						'personal_data_place_of_birth' => (is_null($mba_portal_personal_data->birth_place)) ? NULL : $mba_portal_personal_data->birth_place,
						'personal_data_date_of_birth' => (is_null($mba_portal_personal_data->birthday)) ? NULL : $mba_portal_personal_data->birthday,
						'personal_data_gender' => ($mba_portal_personal_data->gender == 'MALE') ? 'M' : 'F',
						'personal_data_nationality' => $mba_portal_personal_data->nationality,
						'personal_data_marital_status' => strtolower($mba_portal_personal_data->marital_status),
						'personal_data_mother_maiden_name' => ($mba_portal_mother_data) ? $mba_portal_mother_data[0]->mother_given_name : NULL,
						'personal_data_password' => $mba_portal_personal_data->password,
						'personal_data_reference_code' => (is_null($student->sgsagentcode)) ? NULL : $student->sgsagentcode,
						'pmb_sync' => 0,
						'portal_id' => $mba_portal_personal_data->id,
						'date_added' => ($mba_portal_personal_data->date_created == '0000-00-00 00:00:00') ? date('Y-m-d H:i:s', time()) : $mba_portal_personal_data->date_created
					);
					
					$s_student_status = $this->convert_student_status($student->status);
					$alumni_email = NULL;
					if (($student->iulimail != '') AND ($s_student_status == 'graduated')) {
						$a_student_email = explode('@', $student->iulimail);
						$alumni_email = $a_student_email[0].'@alumni.iuli.ac.id';
					}

					$a_student_data = array(
						'student_id' => $s_student_id,
						'personal_data_id' => $s_personal_data_id,
						'student_email' => ($student->iulimail == '') ? NULL : $student->iulimail,
						'student_alumni_email' => $alumni_email,
						'study_program_id' => $mba_staging_study_program_data->study_program_id,
						'program_id' => 1,
						'date_added' => date('Y-m-d H:i:s', time()),
						'academic_year_id' => $mba_portal_academic_year_data->year_name,
						'finance_year_id' => $mba_portal_academic_year_data->year_name,
						'student_number' => $student->id_number,
						'student_type' => ($student->admission_type == 'new') ? 'regular' :'transfer',
						'portal_id' => $student->id,
						'student_status' => $s_student_status
					);

					// print(' .');
					$i++;
					echo '<script>
						document.getElementById("test").innerHTML = "'.$i.'";
					</script>';
					if($mba_staging_student_data){
						unset($a_personal_data['personal_data_id']);
						unset($a_student_data['personal_data_id']);
						unset($a_student_data['student_id']);

						$s_student_id = $mba_staging_student_data[0]->student_id;
						$s_personal_data_id = $mba_staging_student_data[0]->personal_data_id;
						if ($this->Pdm->update_personal_data($a_personal_data, $mba_staging_student_data[0]->personal_data_id)) {
							$this->Stm->update_student_data($a_student_data, $mba_staging_student_data[0]->student_id);
						}else{
							print('error update personal_data');exit;
						}
					}
					else{
						$mba_staging_personal_data_checker = $this->Sm->retrieve_data('dt_personal_data', array('portal_id' => $student->personal_data_id));
						if ($mba_staging_personal_data_checker) {
							unset($a_personal_data['personal_data_id']);
							$s_personal_data_id = $mba_staging_personal_data_checker[0]->personal_data_id;
							if ($this->Pdm->update_personal_data($a_personal_data, $mba_staging_personal_data_checker[0]->personal_data_id)) {
								$a_student_data['personal_data_id'] = $mba_staging_personal_data_checker[0]->personal_data_id;
								if (!$this->Stm->create_new_student($a_student_data)) {
									print('error insert new student');
								}
							}else{
								print('error update personal_data');exit;
							}
						}else{
							if ($this->Pdm->create_new_personal_data($a_personal_data)) {
								if (!$this->Stm->create_new_student($a_student_data)) {
									print('error insert new student');
								}
							}else{
								print('error insert personal_data');exit;
							}
						}
					}

					$this->sync_student_address($s_personal_data_id, $student->personal_data_id);
					$this->sync_student_semester($s_student_id, $student->id);
					$this->sync_family($s_student_id);
					if ($student->status == 'GRADUATED') {
						$this->create_academic_history_iuli($s_personal_data_id, $student->personal_data_id);
					}
				}
			}
		}
		
		if($this->db->trans_status() === false){
			$this->db->trans_rollback();
			print('<b>Rollback</b>');
		}
		else{
			$this->db->trans_commit();
			print('<b>Commit</b>');
			$end = strtotime("now");
			$interval = $end - $start;
			$seconds = $interval % 60;
			$minutes = floor(($interval % 3600) / 60);
			$hours = floor($interval / 3600);
			echo "total time: ".$hours.":".$minutes.":".$seconds;
		}
		modules::run('portal/academic/sync_offer_subject');
		$this->session->unset_userdata('bypass_everything');
	}

	public function sync_student_semester($s_staging_student_id, $s_portal_student_id)
	{
		$this->load->model('academic/Semester_model', 'Sms');
		$mbo_portal_student_semester_lists = $this->Pm->retrieve_data('semester_transcript', array('student_id' => $s_portal_student_id));
		// var_dump($s_portal_student_id);exit;
		if ($mbo_portal_student_semester_lists) {
			$mba_staging_student_data = $this->Sm->retrieve_data('dt_student', array('portal_id' => $s_portal_student_id));
			if (!$mba_staging_student_data) {
				print('Student belum disinkronisasi!');
			}

			$mbo_staging_student_semester_lists = $this->Sm->retrieve_data('dt_student_semester', array('student_id' => $mba_staging_student_data[0]->student_id));
			if ($mbo_staging_student_semester_lists) {
				$this->Sm->remove_staging_data('dt_student_semester', array('student_id' => $s_staging_student_id, 'portal_id !=' => null));
			}

			foreach ($mbo_portal_student_semester_lists as $student_semester) {
				if ($student_semester->semester_id != 0) {
					$mbo_portal_student_data = $this->Pm->retrieve_data('student', array('id' => $student_semester->student_id))[0];
					$mbo_staging_semester_type_data = $this->Sm->retrieve_data('ref_semester', array('semester_id' => $student_semester->semester_id))[0];
					$mbo_portal_academic_year_id = $this->Pm->retrieve_data('academic_year', array('id' => $mbo_portal_student_data->academic_year_id))[0];
					$a_semester_dikti = $this->parsing_semester($mbo_portal_academic_year_id->year_name, $student_semester->semester_id);
					if (intval($a_semester_dikti['academic_year_id']) > 2020) {
						continue;
					}else if ($mbo_portal_student_data->status == 'GRADUATED') {
						if ($student_semester->gpa == 0) {
							continue;
						}
					}

					$s_student_semester_status = ($student_semester->gpa == 0) ? 'inactive' : $this->convert_student_status($student_semester->student_status);
					$a_student_semester_data = array(
						'student_id' => $s_staging_student_id,
						'semester_type_id' => $a_semester_dikti['semester_type_id'],
						'academic_year_id' => $a_semester_dikti['academic_year_id'],
						'student_semester_status' => $s_student_semester_status,
						'student_semester_gpa' => $student_semester->gpa,
						'date_added' => date('Y-m-d H:i:s'),
						'portal_id' => $student_semester->id
					);

					if(!$this->Sms->save_student_semester($a_student_semester_data, [
						'student_id' => $s_staging_student_id,
						'semester_type_id' => $a_semester_dikti['semester_type_id'],
						'academic_year_id' => $a_semester_dikti['academic_year_id']
					])) {
						var_dump('error saving student semester!');exit;
					}
				}
			}
		}
	}

	public function fill_student_semester_transcript()
	{
		$mba_student_list_data = $this->Sm->retrieve_data('dt_student', array('student_status' => 'active'));
		$mbo_semester_active = $this->Sm->retrieve_data('dt_semester_settings', array('semester_status' => 'active'))[0];
		if ($mba_student_list_data) {
			$this->db->trans_start();
			foreach ($mba_student_list_data as $student) {
				if ($student->academic_year_id <= $mbo_semester_active->academic_year_id) {
					$s_semester_type_id = 1;
					$i_academic_year_id = $student->academic_year_id;
					for ($i=0; $i < (($mbo_semester_active->academic_year_id - $student->academic_year_id) * 2) + 1; $i++) {
						$mba_student_score = $this->Sm->retrieve_data('dt_score', array(
							'student_id' => $student->student_id,
							'academic_year_id' => $i_academic_year_id,
							'semester_type_id' => $s_semester_type_id
						));
						if ($mba_student_score) {
							$mba_student_semester_data = $this->Sm->retrieve_data('dt_student_semester', array(
								'student_id' => $student->student_id,
								'academic_year_id' => $i_academic_year_id,
								'semester_type_id' => $s_semester_type_id
							));
							if (!$mba_student_semester_data) {
								$i_total_merit = 0;
								$i_total_sks = 0;
								foreach ($mba_student_score as $score) {
									if ($score->score_approval == 'approved') {
										$mbo_subject_data = $this->Sm->retrieve_data('ref_curriculum_subject', array('curriculum_subject_id' => $score->curriculum_subject_id))[0];
										$i_total_merit += $score->score_merit;
										$i_total_sks += $mbo_subject_data->curriculum_subject_credit;
									}
								}
								if (($i_total_merit == 0) AND ($i_total_sks == 0)) {
									$i_gpa = 0;
								}else{
									$i_gpa = $this->grades->get_ipk($i_total_merit, $i_total_sks);
								}

								$a_student_semester_data = array(
									'student_id' => $student->student_id,
									'semester_type_id' => $s_semester_type_id,
									'academic_year_id' => $i_academic_year_id,
									'student_semester_gpa' => $i_gpa,
									'student_semester_status' => 'active',
									'date_added' => date('Y-m-d H:i:s')
								);

								$save = $this->Stm->save_student_semester($a_student_semester_data);
								if ($save) {
									print(' .');
								}else{
									print('error  save');exit;
								}
							}
						}
						if ($s_semester_type_id == 2) {
							$s_semester_type_id = 1;
							$i_academic_year_id++;
						}else{
							$s_semester_type_id++;
						}
					}
				}
			}

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				print('rollback');
			}else{
				$this->db->trans_commit();
				print('commit');
			}
		}
	}

	public function sync_family($s_staging_student_id = false)
	{
		// $a_student_id = array('f5171f5e-fcfd-4190-9835-3bac5cadb99a','1edd3920-cd45-49cd-88b5-37b309d31331');
		// print('<h4>syncronize family data</h4>');
		if ($s_staging_student_id) {
			$mba_staging_student_data = $this->Sm->retrieve_data('dt_student', array('student_id' => $s_staging_student_id));
		}else{
			$mba_staging_student_data = $this->Sm->retrieve_data('dt_student');
		}
		if ($mba_staging_student_data) {
			// $this->db->trans_start();
			foreach ($mba_staging_student_data as $student) {
				$mba_student_data = $this->Sm->retrieve_data('dt_student', array('student_id' => $student->student_id))[0];
				if ($mba_student_data) {
					$mba_portal_parent_data = $this->Pm->retrieve_data('parents', array('student_id' => $mba_student_data->portal_id));
					if ($mba_portal_parent_data) {
						$mbo_portal_personal_data_parent = $this->Pm->retrieve_data('personal_data', array('id' => $mba_portal_parent_data[0]->personal_data_id));
						if ($mbo_portal_personal_data_parent) {
							$mbo_student_family_staging_data = $this->Sm->retrieve_data('dt_family_member', array(
								'personal_data_id' => $mba_student_data->personal_data_id,
								'family_member_status' => 'child'
							));
							
							if ($mbo_student_family_staging_data) {
								foreach ($mbo_student_family_staging_data as $family_data) {
									$this->Fmm->delete_family($family_data->family_id);
									// print(' o');
								}
							}

							$mba_family_student = $this->Pm->get_portal_family($mbo_portal_personal_data_parent[0]->personal_email);
							$a_student_id_child = false;
							if ($mba_family_student) {
								$a_student_id_child = array();
								foreach ($mba_family_student as $families) {
									$mbo_student_staging_data = $this->Sm->retrieve_data('dt_student', array('portal_id' => $families->student_id))[0];
									array_push($a_student_id_child, $mbo_student_staging_data->student_id);
								}
							}

							if ($a_student_id_child) {
								// $s_portal_parent_personal_id = $mba_family_student[0]->personal_data_id;
								$o_portal_parent_data = $mba_family_student[0];

								$a_staging_parent_data = array(
									'personal_data_name' => (!is_null($o_portal_parent_data->fullname)) ? $o_portal_parent_data->fullname : $o_portal_parent_data->firstname.' '.$o_portal_parent_data->middlename.' '.$o_portal_parent_data->lastname,
									'personal_data_email' => $o_portal_parent_data->personal_email,
									'personal_data_gender' => $o_portal_parent_data->gender,
									'personal_data_phone' => $o_portal_parent_data->personal_phone,
									'personal_data_cellular' => (is_null($o_portal_parent_data->personal_mobilephone)) ? '0' : $o_portal_parent_data->personal_mobilephone,
									'ocupation_id' => ((!is_null($o_portal_parent_data->occupation)) OR ($o_portal_parent_data->occupation != '')) ? $this->sync_occupation_staging($o_portal_parent_data->occupation) : null,
									'personal_data_marital_status' => ((!is_null($o_portal_parent_data->marital_status)) OR ($o_portal_parent_data->marital_status != '')) ? strtolower($o_portal_parent_data->marital_status) : 'married',
									'personal_data_nationality' => ((!is_null($o_portal_parent_data->nationality)) OR ($o_portal_parent_data->nationality != '')) ? strtoupper($o_portal_parent_data->nationality) : null,
									'portal_status' => 'blocked',
									'portal_id' => $o_portal_parent_data->personal_data_id
								);

								$mbo_parent_staging_data = $this->Sm->retrieve_data('dt_personal_data', array('portal_id' => $mba_family_student[0]->personal_data_id));
								if ($mbo_parent_staging_data) {
									$s_staging_personal_id = $mbo_parent_staging_data[0]->personal_data_id;
									$this->Pdm->update_personal_data($a_staging_parent_data, $s_staging_personal_id);
								}else{
									$s_staging_personal_id = $this->uuid->v4();
									$a_staging_parent_data['personal_data_id'] = $s_staging_personal_id;
									$this->Pdm->create_personal_data_parents($a_staging_parent_data);
								}

								$s_family_id = $this->uuid->v4();

								$a_family_data = array(
									'family_id' => $s_family_id,
									'date_added' => date('Y-m-d H:i:s')
								);

								$a_family_parent_member_data = array(
									'family_id' => $s_family_id,
									'personal_data_id' => $s_staging_personal_id,
									'family_member_status' => strtolower($mba_family_student[0]->relationship),
									'date_added' => date('Y-m-d H:i:s')
								);
								
								$this->Fmm->create_family($a_family_data);
								$this->Fmm->add_family_member($a_family_parent_member_data);

								foreach ($a_student_id_child as $student_id) {
									$mbo_student_staging_data = $this->Sm->retrieve_data('dt_student', array('student_id' => $student_id))[0];
									$mba_family_exist = $this->Sm->retrieve_data('dt_family_member', array(
										'personal_data_id' => $mbo_student_staging_data->personal_data_id,
										'family_id' => $s_family_id
									));

									if (!$mba_family_exist) {
										$a_family_student_member_data = array(
											'family_id' => $s_family_id,
											'personal_data_id' => $mbo_student_staging_data->personal_data_id,
											'family_member_status' => 'child',
											'date_added' => date('Y-m-d H:i:s')
										);
										$this->Fmm->add_family_member($a_family_student_member_data);
									}
								}
							}
						}
					}
				}
			}

			// if ($this->db->trans_status() === FALSE) {
			// 	print('rollback process');
			// 	$this->db->trans_rollback();
			// }else{
			// 	print('commit process');
			// 	$this->db->trans_commit();
			// }
		}
	}

	public function sync_student_address($s_personal_data_id_staging, $s_personal_data_id_portal)
	{
		$mbo_portal_personal_data = $this->Pm->retrieve_data('personal_data', array('id' => $s_personal_data_id_portal))[0];
		if (!is_null($mbo_portal_personal_data->address_id)) {
			$mbo_portal_address_data = $this->Pm->retrieve_data('address', array('id' => $mbo_portal_personal_data->address_id))[0];
			if (($mbo_portal_address_data->id_dikti_wilayah != 'Select Dis') AND ($mbo_portal_address_data->id_dikti_wilayah != 0)) {
				$mbo_staging_personal_address = $this->Sm->retrieve_data('dt_personal_address', array('personal_data_id' => $s_personal_data_id_staging, 'personal_address_type' => 'primary'))[0];
				
				$a_address_data = array(
					// 'address_id' => $this->uuid->v4(),
					'dikti_wilayah_id' => $mbo_portal_address_data->id_dikti_wilayah,
					'country_id' => ((!is_null($mbo_portal_personal_data->country_id)) AND ($mbo_portal_personal_data->country_id != '0')) ? $mbo_portal_personal_data->country_id : NULL,
					'address_rt' => $mbo_portal_address_data->rt,
					'address_rw' => $mbo_portal_address_data->rw,
					'address_province' => NULL,
					'address_city' => NULL,
					'address_zipcode' => $mbo_portal_address_data->zipcode,
					'address_street' => $mbo_portal_address_data->street,
					'address_sub_district' => (!is_null($mbo_portal_address_data->kelurahan)) ? strtoupper($mbo_portal_address_data->kelurahan) : NULL,
					'address_phonenumber' => NULL,
					'address_cellular' => NULL
				);

				$a_personal_address_data = array(
					'personal_data_id' => $s_personal_data_id_staging,
					'address_id' => $this->uuid->v4(),
					'personal_address_type' => 'primary',
					'status' => 'active'
				);

				if ($mbo_staging_personal_address) {
					if ($this->Adm->save_address($a_address_data, $mbo_staging_personal_address->address_id)) {
						$this->Adm->save_personal_address($a_personal_address_data, $s_personal_data_id_staging, $mbo_staging_personal_address->address_id);
					}
				}else {
					$s_address_id = $this->uuid->v4();
					$a_address_data['address_id'] = $s_address_id;
					$a_personal_address_data['address_id'] = $s_address_id;
					if ($this->Adm->save_address($a_address_data)) {
						$this->Adm->save_personal_address($a_personal_address_data);
					}
				}
			}
		}
	}

	public function create_academic_history_iuli($s_staging_personal_data_id, $s_portal_personal_data_id)
	{
		print('Must error and close!');exit;
		$s_iuli_institution_id = 'f5220e90-c7b6-iuli-b9d8-5254005d90f6';
		$s_iuli_address_id = 'a2cad794-c577-iuli-b9d8-5254005d90f6';
		if (!$mbo_institution_data = $this->Sm->retrieve_data('ref_institution', array('institution_id' => $s_iuli_institution_id))[0]) {
			if (!$address_data = $this->Sm->retrieve_data('dt_address', array('address_id' => $s_iuli_address_id))[0]) {
				$a_address_data = array(
					'address_id' => $s_iuli_address_id,
					'dikti_wilayah_id' => '286300',
					'country_id' => '9bb722f5-8b22-11e9-973e-52540001273f',
					'address_province' => 'BANTEN',
					'address_city' => 'TANGERANG SELATAN',
					'address_zipcode' => '15345',
					'address_street' => 'MyRepublic Plaza',
					'address_sub_district' => 'BSD City',
					'address_phonenumber' => '085212318000',
					'address_cellular' => '085212318000'
				);

				$this->Adm->save_address($a_address_data);
			}
			$a_iuli_insititution_data = array(
				'institution_id' => $s_iuli_institution_id,
				'address_id' => $s_iuli_address_id,
				'institution_name' => 'INTERNATIONAL UNIVERSITY LIAISON INDONESIA',
				'institution_email' => 'employee@company.ac.id',
				'institution_phone_number' => '0852123018000',
				'institution_type' => 'university',
				'institution_is_international' => 'yes'
			);

			$this->Im->insert_institution($a_iuli_insititution_data);
		}
		$mbo_staging_student_data = $this->Sm->retrieve_data('dt_student', array('personal_data_id' => $s_staging_personal_data_id))[0];
		$mbo_portal_student_data = $this->Pm->retrieve_data('student', array('personal_data_id' => $s_portal_personal_data_id))[0];
		$mbo_portal_study_program_data = $this->Pm->retrieve_data('study_program', array('id' => $mbo_portal_student_data->study_program_id))[0];

		$a_academic_history_data = array(
			'institution_id' => $s_iuli_institution_id,
			'personal_data_id' => $s_staging_personal_data_id,
			'academic_history_graduation_year' => '2019',
			'academic_history_major' => $mbo_portal_study_program_data->name,
			'academic_history_gpa' => $this->get_ipk($mbo_portal_student_data->id),
			'academic_history_main' => 'yes',
			'academic_history_this_job' => 'no',
			'status' => 'active'
		);

		$mbo_staging_student_academic_history = $this->Sm->retrieve_data('dt_academic_history', array('personal_data_id' => $s_staging_personal_data_id, 'academic_history_this_job' => 'no', 'academic_history_main' => 'yes'))[0];
		if ($mbo_staging_student_academic_history) {
			$this->Im->insert_academic_history($a_academic_history_data, $mbo_staging_student_academic_history->academic_history_id);
		}else{
			$a_academic_history_data['academic_history_id'] = $this->uuid->v4();
			$this->Im->insert_academic_history($a_academic_history_data);
		}
	}

	public function get_ipk($s_portal_student_id)
	{
		$mbo_portal_student_data = $this->Pm->retrieve_data('student', array('id' => $s_portal_student_id))[0];
		$score_data = $this->Pm->get_score_student($s_portal_student_id);
		$personal_data = $this->Pm->retrieve_data('personal_data', array('id' => $mbo_portal_student_data->personal_data_id))[0];
		$study_program = $this->Pm->retrieve_data('study_program', array('id' => $mbo_portal_student_data->study_program_id))[0];

		$total_merit = 0;
		$total_sks = 0;
		foreach ($score_data as $score) {
			$grade_point = $this->grades->get_grade_point($score->score_sum);
			
			$total_merit += ($score->sks_credit * $grade_point);
			$total_sks += $score->sks_credit;
		}

		$IPK = $total_merit / $total_sks;
		return $IPK;
	}

	public function sync_occupation_staging($s_occupation_name)
	{
		$mbo_staging_occupation_data = $this->Sm->retrieve_data('ref_ocupation', array('ocupation_name' => $s_occupation_name))[0];
		if ($mbo_staging_occupation_data) {
			return $mbo_staging_occupation_data->ocupation_id;
		}else{
			$s_occupation_id = $this->uuid->v4();
			$a_occupation_data = array(
				'ocupation_id' => $s_occupation_id,
				'ocupation_name' => $s_occupation_name,
				'date_added' => date('Y-m-d H:i:s')
			);

			$this->Emm->save_occupation($a_occupation_data);
			return $s_occupation_id;
		}
	}

	public function convert_student_status($s_portal_status)
	{
		switch ($s_portal_status) {
			case 'PENDINGPARTICIPANT':
				return 'participant';
				break;
			
			default:
				return strtolower($s_portal_status);
				break;
		}
	}

	public function parsing_semester($s_academic_year_id, $s_semester_id)
    {
        $a_normal_semester_id = array(1,2,3,4,5,6,7,8,9,10);
        $a_between_semester_id = array(11,12,13,14,15,16,19,21);
        $a_ofse_semester_id = array(17, 20);
        $semester_id = intval($s_semester_id);
        if (in_array($semester_id, $a_normal_semester_id)) {
            $i_academic_year_id = (int)$s_academic_year_id;
            $i_semester_id = intval($s_semester_id) / 2;

            if (intval($s_semester_id) % 2 == 0) {
                $academic_year_id = $i_academic_year_id + ($i_semester_id - 1);
                $s_semester_type_id = 2;
            }else{
                $academic_year_id = $i_academic_year_id + (int)$i_semester_id;
                $s_semester_type_id = 1;
            }
            
            $result = array('academic_year_id' => $academic_year_id, 'semester_type_id' => $s_semester_type_id);
        }else if (in_array($s_semester_id, $a_between_semester_id)) {
            $mba_semester_data = $this->Sm->retrieve_data('ref_semester', array('semester_id' => $s_semester_id))[0];
            $s_semester_type = $mba_semester_data->semester_type_id;
            $i_semester_number = intval($mba_semester_data->semester_number);
            if (($i_semester_number == 1 ) OR ($i_semester_number == 2)) {
                $result = array('academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type);
            }else if (($i_semester_number == 3 ) OR ($i_semester_number == 4)) {
                $result = array('academic_year_id' => intval($s_academic_year_id) + 1, 'semester_type_id' => $s_semester_type);
            }else if (($i_semester_number == 5 ) OR ($i_semester_number == 6)) {
                $result = array('academic_year_id' => intval($s_academic_year_id) + 2, 'semester_type_id' => $s_semester_type);
            }else if (($i_semester_number == 7 ) OR ($i_semester_number == 8)) {
                $result = array('academic_year_id' => intval($s_academic_year_id) + 3, 'semester_type_id' => $s_semester_type);
            }
        }else if (in_array($s_semester_id, $a_ofse_semester_id)) {
            $result = array('academic_year_id' => 2018, 'semester_type_id' => 4);
        }else if ($s_semester_id == 18) {
            $result = array('academic_year_id' => $s_academic_year_id, 'semester_type_id' => 5);
        }else{
            print('semester_id error: '.$s_semester_id);exit;
        }
        // print_r($result);
        return $result;
	}

	public function clear_duplicate_personal_email()
	{
		$mba_personal_group = $this->Sm->retrieve_group_data('dt_personal_data', 'personal_data_email');
		if ($mba_personal_group) {
			$this->db->trans_start();
			foreach ($mba_personal_group as $personal_data) {
				if (!is_null($personal_data->personal_data_email)) {
					$mba_personal_duplicate = $this->Sm->retrieve_data('dt_personal_data',array('personal_data_email' => $personal_data->personal_data_email));
					$i_count_personal_data = count($mba_personal_duplicate);
					if ($i_count_personal_data >= 2) {
						// print('<pre>');var_dump($mba_personal_duplicate);exit;
						foreach ($mba_personal_duplicate as $duplicate_data) {
							if (($duplicate_data->portal_id == '0') AND (!is_null($duplicate_data->personal_data_email))) {
								$mba_personal_student = $this->Sm->retrieve_data('dt_student', array('personal_data_id' => $duplicate_data->personal_data_id));
								if (!$mba_personal_student) {
									$mba_personal_employee = $this->Sm->retrieve_data('dt_employee', array('personal_data_id' => $duplicate_data->personal_data_id));
									if (!$mba_personal_employee) {
										if ($i_count_personal_data > 0) {
											print('Deleted:');var_dump($duplicate_data->personal_data_id);
											print('<br>');
											$this->Sm->remove_staging_data('dt_personal_data', array('personal_data_id' => $duplicate_data->personal_data_id));
											$i_count_personal_data--;
											print(' .');
										}
									}
								}
							}
						}
					}
				}
			}

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				print('Rollback');
			}else{
				$this->db->trans_commit();
				print('Commit');
			}
		}
	}

	public function set_parent_mail($s_email)
	{
		// $s_email = 'anarompas@gmail.com';
		$a_email = explode('@', $s_email);
		$new_email = $a_email[0].'+parent@'.$a_email[1];
		return $new_email;
	}

	public function clear_email_parent()
	{
		$mba_personal_data_staging = $this->Sm->retrieve_data_like('dt_personal_data', array('personal_data_email' => '+parent'));
		if ($mba_personal_data_staging) {
			foreach ($mba_personal_data_staging as $personal_data) {
				$s_email = $personal_data->personal_data_email;
				$s_email_ori = $this->get_original_email($s_email);
				$this->Pdm->update_personal_data(array('personal_data_email' => $s_email_ori), $personal_data->personal_data_id);
				print(' .');
			}
		}
		// var_dump($mba_personal_data_staging);
	}

	public function get_original_email($s_email, $only_name = false)
	{
		$a_email = explode('@', $s_email);
		$a_email_name = explode('+', $a_email[0]);
		$s_email_ori = $a_email_name[0].'@'.$a_email[1];
		if ($only_name) {
			return $a_email_name[0];
		}else{
			return $s_email_ori;
		}
	}

	public function repair_student_address()
	{
		print('<pre>');
		$mba_student_null_address = $this->Stm->get_student_null_address();
		if ($mba_student_null_address) {
			print('<pre>');
			foreach ($mba_student_null_address as $student) {
				if (!is_null($student->portal_id)) {
					$mba_portal_personal_data = $this->Pm->retrieve_data('personal_data', array('id' => $student->portal_id))[0];
					if (($mba_portal_personal_data) AND ($mba_portal_personal_data->address_id > 0)) {
						$mba_portal_address_data = $this->Pm->retrieve_data('address', array('id' => $mba_portal_personal_data->address_id))[0];
						// var_dump($student);
						$mba_staging_personal_address = $this->Sm->retrieve_data('dt_personal_address', array('personal_data_id' => $student->personal_data_id));
						if (!$mba_staging_personal_address) {
							$s_address_id = $this->uuid->v4();
							$s_country_id = NULL;
							$s_district = NULL;
							$s_dikti_wiayah = NULL;
							if ($mba_portal_address_data->id_wilayah > 0) {
								$mbo_portal_wilayah_data = $this->Pm->retrieve_data('wilayah', array('id' => $mba_portal_address_data->id_wilayah))[0];
								if ($mbo_portal_wilayah_data) {
									$s_dikti_wiayah = $mbo_portal_wilayah_data->id_wilayah;
									$s_district = $mbo_portal_wilayah_data->nama_wilayah;
									$mbo_staging_country_data = $this->Sm->retrieve_data('ref_country', array('country_code' => $mbo_portal_wilayah_data->id_negara))[0];
									if ($mbo_staging_country_data) {
										$s_country_id = $mbo_staging_country_data->country_id;
									}
								}
							}

							if ($mba_portal_address_data->id_dikti_wilayah > 0) {
								$s_dikti_wiayah = $mba_portal_address_data->id_dikti_wilayah;
							}
							
							$a_address_data = array(
								'address_id' => $s_address_id,
								'dikti_wilayah_id' => $s_dikti_wiayah,
								'country_id' => $s_country_id,
								'address_rt' => (!is_null($mba_portal_address_data->rt)) ? $mba_portal_address_data->rt : null,
								'address_rw' => (!is_null($mba_portal_address_data->rw)) ? $mba_portal_address_data->rw : null,
								// 'address_province' => '',
								// 'address_city' => '',
								'address_zipcode' => (!is_null($mba_portal_address_data->zipcode)) ? $mba_portal_address_data->zipcode : null,
								'address_street' => (!is_null($mba_portal_address_data->street)) ? $mba_portal_address_data->street : null,
								'address_sub_district' => (!is_null($mba_portal_address_data->kelurahan)) ? $mba_portal_address_data->kelurahan : null,
								'address_district' => $s_district,
								'date_added' => $mba_portal_address_data->date_created,
								'address_phonenumber' => NULL,
								'address_cellular' => NULL
							);
							
							$a_personal_adress_data = array(
								'personal_data_id' => $student->personal_data_id,
								'address_id' => $s_address_id,
								'personal_address_name' => NULL,
								'personal_address_type' => 'primary',
								'status' => 'active',
								'date_added' => $mba_portal_address_data->date_created
							);

							if ($this->Adm->save_address($a_address_data)) {
								$this->Adm->save_personal_address($a_personal_adress_data);
								print('. ');
							}else{
								print('error save');exit;
							}
						}
					}
				}
			}
		}
	}

	public function repair_mother_maiden_name()
	{
		$mba_student_null_mother = $this->Stm->get_student_null_mother_maiden();
		print('<pre>');
		if ($mba_student_null_mother) {
			foreach ($mba_student_null_mother as $student) {
				if (!is_null($student->portal_id)) {
					$mba_portal_mother_data = $this->Pm->retrieve_data('parents', array('student_id' => $student->portal_id));
					if ($mba_portal_mother_data) {
						// var_dump($student);
						$s_mother_name = $mba_portal_mother_data[0]->mother_given_name;
						if (!is_null($s_mother_name)) {
							var_dump($s_mother_name);
							$a_staging_mother_update = array(
								'personal_data_mother_maiden_name' => $s_mother_name
							);

							$this->Pdm->update_personal_data($a_staging_mother_update, $student->personal_data_id);
							// print(' .');
						}
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
			$parent_tag = '+parent';
			if (count($mba_staging_personal_data) > 1) {
				$parent_tag = '+parent'.count($mba_staging_personal_data);
			}
            $new_email = $a_email[0].$parent_tag.'@'.$a_email[1];
            return $new_email;
        }else{
            return $s_staging_email;
        }
    }
}