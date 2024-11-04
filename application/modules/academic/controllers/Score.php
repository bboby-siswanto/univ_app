<?php
class Score extends App_core
{
    function __construct()
    {
        parent::__construct('academic');
        $this->load->model('Score_model', 'Scm');
        $this->load->model('Class_group_model', 'Cgm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('employee/Employee_model', 'Emm');
        $this->load->model('Academic_year_model', 'Aym');
    }

    public function show_score()
    {
        if ($this->input->is_ajax_request()) {
            $s_score_id = $this->input->post('score_id');
            // print('<pre>');var_dump($s_score_id);exit;
            $this->Scm->save_data([
                'score_display' => 'TRUE'
            ], [
                'score_id' => $s_score_id
            ]);

            return true;
        }
    }

    public function hide_score()
    {
        if ($this->input->is_ajax_request()) {
            $s_score_id = $this->input->post('score_id');
            // print('<pre>');var_dump($s_score_id);exit;
            $this->Scm->save_data([
                'score_display' => 'FALSE'
            ], [
                'score_id' => $s_score_id
            ]);

            return true;
        }
    }

    // public function get_ipk($s_student_id)
    // {
        // salah
    //     $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
    //     if ($mbo_student_data) {
    //         $a_score_filter = array(
    //             'st.student_id' => $mbo_student_data->student_id,
    //             'curs.curriculum_subject_type != ' => 'extracurricular',
    //             'curs.curriculum_subject_credit > ' => 0,
    //             'curs.curriculum_subject_category' => 'regular semester',
    //             'sc.score_approval' => 'approved',
    //             'sc.score_display' => 'TRUE'
    //         );

    //         $mba_score = $this->Scm->get_score_data_transcript($a_score_filter);
            
    //         if ($mbo_student_data->student_type == 'transfer') {
    //             $a_score_trcr_filter = array(
    //                 'st.student_id' => $mbo_student_data->student_id,
    //                 'sc.score_approval' => 'approved',
    //                 'curs.curriculum_subject_type != ' => 'extracurricular',
    //                 'curs.curriculum_subject_credit > ' => 0,
    //                 'sc.score_display' => 'TRUE'
    //             );
    //             $a_semester_trcr_id = [18];
    
    //             $mba_score_semester_trcr = $this->Scm->get_score_data_transcript($a_score_trcr_filter, $a_semester_trcr_id);
    //             if ($mba_score_semester_trcr) {
    //                 if ($mba_score) {
    //                     $mba_score = array_merge($mba_score, $mba_score_semester_trcr);
    //                 }
    //                 else {
    //                     $mba_score = $mba_score_semester_trcr;
    //                 }
    //             }
    //         }

    //         print('<pre>');var_dump($mba_score);exit;
    //     }
    //     else {
    //         $s_ipk = 0;
    //     }
    // }

    public function get_credit_student_semester()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_semester_id = $this->input->post('semester_id');

            $mba_score_data = $this->Scm->get_score_semester([
                'sc.student_id' => $s_student_id,
                'sc.semester_id' => $s_semester_id,
                'sc.score_approval' => 'approved'
            ]);

            $d_credit_approved = 0;
            if ($mba_score_data) {
                foreach ($mba_score_data as $o_score) {
                    $d_credit_approved += $o_score->curriculum_subject_credit;
                }
            }

            print json_encode(['sks' => $d_credit_approved]);
        }
    }

    public function has_repeat_subject($s_student_id, $b_force_print = false)
    {
        $has_repeat_subject = false;
        $mba_normal_semester = $this->Scm->get_score_semester([
            'sc.student_id' => $s_student_id,
            'sm.semester_number > ' => 8,
            'sc.score_approval' => 'approved',
            'sc.score_display' => 'TRUE'
        ]);

        if ($mba_normal_semester) {
            // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
            //     print('<pre>');var_dump($mba_normal_semester);exit;
            // }
            $has_repeat_subject = true;
        }
        else {
            $a_semester_type_subject = [1,2,3];
            $mba_normal_semester = $this->Scm->get_score_student($s_student_id, [
                'sm.semester_number <= ' => 8,
                'sc.score_approval' => 'approved',
                'sc.score_display' => 'TRUE'
            ]);
            if (!$mba_normal_semester) {
                return true;
            }

            foreach ($mba_normal_semester as $o_score) {
                $mba_score_data_detail = $this->Scm->get_score_data([
                    'sc.student_id' => $s_student_id,
                    'sc.score_approval' => 'approved',
                    'sn.subject_name' => $o_score->subject_name,
                    'sc.score_display' => 'TRUE'
                ], [1,2,3]);

                if (($mba_score_data_detail) AND (count($mba_score_data_detail) != 1)) {
                    $has_repeat_subject = true;

                    if ($b_force_print) {
                        $has_repeat_subject = $mba_score_data_detail;
                    }

                    break;
                }
            }
        }

        if ($b_force_print) {
            print('<pre>');var_dump($has_repeat_subject);exit;
        }
        return $has_repeat_subject;
    }

	public function download_cumulative_gpa()
	{
		if($this->input->is_ajax_request()){
			$s_academic_year_id = $this->input->post('academic_year_id');
			$s_file_path = APPPATH."uploads/cumulative_gpa/cumulative-gpa-$s_academic_year_id.csv";
			$fp = fopen($s_file_path, 'w+');
			fputcsv($fp, [
				'Name',
				'Student ID',
				'Study Program',
				'Semester',
				// 'GPA',
				'Cumulative GPA'
			]);
			
			// array_push($items, $csv_header);
			if($s_academic_year_id != 'all'){
				$mba_student_filtered = $this->Stm->get_student_filtered(['ds.academic_year_id' => $s_academic_year_id], ['graduated']);
				if($mba_student_filtered){
					foreach($mba_student_filtered as $o_student_filtered){
						$mba_semester_student = $this->Smm->get_semester_student($o_student_filtered->student_id);
						if($mba_semester_student){
							foreach($mba_semester_student as $o_semester_student){
                                $mbo_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $o_semester_student->student_id])[0];
                                $s_study_program_name = ($mbo_student_data->student_program == $this->a_programs['NI S1']) ? $mbo_student_data->study_program_ni_name : $mbo_student_data->study_program_name;
								fputcsv($fp, [
									$o_student_filtered->personal_data_name,
									$o_student_filtered->student_number,
									$s_study_program_name,
									$o_semester_student->semester_year_id.$o_semester_student->semester_type_id,
									// $o_semester_student->student_semester_gp,
									$o_semester_student->student_semester_gpa
								]);
							}
						}
					}
				}
			}
			
			fclose($fp);
			$this->session->set_flashdata('file_token', $s_file_path);
			print json_encode(['code' => 0]);
			exit;
		}
	}

    public function download_transcript($s_student_id, $transcript_type, $s_filename)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        if ($mbo_student_data) {
            $s_prodi_abbr = ($mbo_student_data->program_id == $this->a_programs['NI S1']) ? $mbo_student_data->study_program_ni_abbreviation : $mbo_student_data->study_program_abbreviation;
            switch ($transcript_type) {
                case 'halfway':
                    $s_path = APPPATH.'uploads/academic/transcript-halfway/'.$mbo_student_data->academic_year_id.'/'.$s_prodi_abbr.'/'.$s_filename;
                    break;
                
                default:
                    show_404();
                    break;
            }
            header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile($s_path);
            exit;
        }else{
            show_404();
        }
    }

    public function send_transcript_semester()
    {
        $this->load->model('personal_data/Family_model', 'Fmm');
        if ($this->input->is_ajax_request()) {
            // $a_filter_data = $this->input->post();
            $a_filter_data = [
                'ds.academic_year_id' => $this->input->post('academic_year_id')
            ];
			$passed_defense = $this->input->post('passed_defense');

			// foreach ($a_filter_data as $key => $value) {
			// 	if (is_array($value)) {
			// 		unset($a_filter_data[$key]);
			// 	}
			// 	else if($a_filter_data[$key] == 'all'){
			// 		unset($a_filter_data[$key]);
			// 	}
			// 	// else if($key == 'study_program_id') {
			// 	// 	$a_filter_data['ds.study_program_id'] = $value;
			// 	// 	unset($a_filter_data[$key]);
			// 	// }
			// 	else if($key == 'program_id') {
			// 		$a_filter_data['ds.program_id'] = $value;
			// 		unset($a_filter_data[$key]);
			// 	}
			// 	else if ($key == 'passed_defense') {
			// 		unset($a_filter_data[$key]);
			// 	}
			// 	else if ($key == 'transcript_body_mail') {
			// 		unset($a_filter_data[$key]);
			// 	}
			// }

            if ($this->input->post('academic_year_id') == 'all') {
                unset($a_filter_data['ds.academic_year_id']);
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
			if (is_array($this->input->post('study_program_id[]'))) {
				if (count($this->input->post('study_program_id[]')) > 0) {
					$prodi_filter = $this->input->post('study_program_id[]');
				}
			}

            $s_email_body = $this->input->post('transcript_body_mail');

            $a_student_send = array();

            $o_semester_active = $this->Smm->get_active_semester();

            // $mba_filtered_data = $this->Stm->get_student_filtered($a_filter_data);
            $mba_filtered_data = $this->Stm->get_student_filtered($a_filter_data, $status_filter, false, $prodi_filter);
            // print('<pre>');
            // var_dump($this->input->post());exit;
            // var_dump($s_email_body);exit;
            
            if ($mba_filtered_data) {
                
                foreach ($mba_filtered_data as $o_student) {

                    if ($o_student->student_status == 'active') {

                        if ($o_student->student_send_transcript == 'TRUE') {

                            $get_files = modules::run('download/excel_download/generate_transcript_semester', $o_student->student_id, $o_semester_active->academic_year_id, $o_semester_active->semester_type_id);

                            if ($get_files) {
                                // $config = $this->config->item('mail_config');
                                $config['mailtype'] = 'html';
                                $this->email->initialize($config);

                                $mbo_deans_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $o_student->deans_id))[0];
                                $mbo_family_data = $this->Fmm->get_family_by_personal_data_id($o_student->personal_data_id);

                                $a_parent_email = array();

                                if ($mbo_family_data) {
                                    $mba_parent_data = $this->Fmm->get_family_lists_filtered(array(
                                        'fmm.family_id' => $mbo_family_data->family_id,
                                        'fmm.family_member_status != ' => 'child'
                                    ));

                                    if ($mba_parent_data) {
                                        foreach ($mba_parent_data as $o_parents) {
                                            if (!in_array($o_parents->personal_data_email, $a_parent_email)) {
                                                array_push($a_parent_email, $o_parents->personal_data_email);
                                            }
                                        }
                                    }
                                }
                                
                                $this->email->to($o_student->student_email);
                                $this->email->from('employee@company.ac.id', 'IULI Academic Service Centre');
                                
                                if (count($a_parent_email) > 0) {
                                    $this->email->cc($a_parent_email);
                                }
                                $this->email->bcc(array($this->config->item('email')['academic']['head'], 'employee@company.ac.id', $mbo_deans_data->employee_email));

                                $this->email->subject('[TRANSCRIPT] Final Transcript of Academic Semester');
                                $this->email->message($s_email_body);

                                $this->email->attach($get_files['file_path']);

                                if(!$this->email->send()){
                                    $this->log_activity('Email did not sent');
                                    $this->log_activity('Error Message: '.$this->email->print_debugger());
                                    
                                    $a_return = array('code' => 1, 'student_send' => $a_student_send, 'message' => 'Email not send to '.$s_email_to.' !');
                                    print json_encode($a_return);exit;
                                }else{
                                    $a_return = array('code' => 0, 'student_send' => $a_student_send, 'message' => 'Success!');
                                }
                                $this->email->clear(TRUE);

                                array_push($a_student_send, $o_student->student_email);
                            }
                            // else{
                            //     var_dump($o_student);exit;
                            //     $a_return = array('code' => 1, 'student_send' => $a_student_send, 'message' => 'Error generate transcript semester!');
                            //     print json_encode($a_return);exit;
                            // }
                        }
                    }
                }
            }else{
                $a_return = array('code' => 1, 'message' => 'No student data found!');
            }

            print json_encode($a_return);
        }
    }

    public function download_semester_transcript($s_student_id, $s_academic_year_id, $s_semester_type_id)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        if ($mbo_student_data) {
            $a_score_param = [
                // 'st.personal_data_id' => $mbo_student_data->personal_data_id,
                'st.student_id' => $s_student_id,
                'sc.academic_year_id' => $s_academic_year_id, 
                'sc.semester_type_id' => $s_semester_type_id,
                'sc.score_display' => 'TRUE'
            ];
            
            $mba_score_data = $this->Scm->get_score_data($a_score_param);
            // $mba_score_data = $this->Scm->get_score_student($s_student_id, array('sc.academic_year_id' => $s_academic_year_id, 'sc.semester_type_id' => $s_semester_type_id));
            if ($mba_score_data) {
                $get_files = modules::run('download/excel_download/generate_transcript_semester', $s_student_id, $s_academic_year_id, $s_semester_type_id);
                if ($get_files) {
                    header('Content-Disposition: attachment; filename='.urlencode($get_files['filename']));
                    readfile( $get_files['file_path'] );
                    exit;
                }else{
                    
                    show_404();
                }
            }else{
                // print('a');
                show_404();
            }
        }else{
            show_404();
        }
    }

    public function download_semester_krs($s_student_id, $s_academic_year_id, $s_semester_type_id)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        if ($mbo_student_data) {
            $a_score_param = [
                // 'st.personal_data_id' => $mbo_student_data->personal_data_id,
                'st.student_id' => $s_student_id,
                'sc.academic_year_id' => $s_academic_year_id, 
                'sc.semester_type_id' => $s_semester_type_id,
                'sc.score_display' => 'TRUE'
            ];
            
            $mba_score_data = $this->Scm->get_score_data($a_score_param);
            // $mba_score_data = $this->Scm->get_score_student($s_student_id, array('sc.academic_year_id' => $s_academic_year_id, 'sc.semester_type_id' => $s_semester_type_id));
            if ($mba_score_data) {
                $get_files = modules::run('download/excel_download/generate_transcript_krs_semester', $s_student_id, $s_academic_year_id, $s_semester_type_id);
                if ($get_files) {
                    header('Content-Disposition: attachment; filename='.urlencode($get_files['filename']));
                    readfile( $get_files['file_path'] );
                    exit;
                }else{
                    
                    show_404();
                }
            }else{
                // print('a');
                show_404();
            }
        }else{
            show_404();
        }
    }
    
    public function download_mid_transcript($s_student_id, $s_academic_year_id, $s_semester_type_id)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        if ($mbo_student_data) {
            $mba_score_data = $this->Scm->get_score_data(array(
                'st.student_id' => $s_student_id,
                'sc.academic_year_id' => $s_academic_year_id, 
                'sc.semester_type_id' => $s_semester_type_id,
                'sc.score_display' => 'TRUE'
            ));
            
            if ($mba_score_data) {
                $s_date_start_semester = '2020-09-7';
                $s_date_end_semester = '2020-10-23';
                $s_date_issue = date('Y-m-d');

                $get_files = modules::run('download/excel_download/generate_mid_transcript', $s_student_id, $s_academic_year_id, $s_semester_type_id, $s_date_start_semester,  $s_date_end_semester, $s_date_issue);
                // print('<pre>');
                // var_dump($get_files);exit;
                if ($get_files) {
                    header('Content-Disposition: attachment; filename='.urlencode($get_files['filename']));
                    readfile( $get_files['file_path'] );
                    exit;
                }else{
                    show_404();
                }
            }else{
                show_404();
            }
        }else{
            show_404();
        }
    }

    public function Student_score($s_student_id = false)
    {
        if ($s_student_id) {
            $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
            if ($mbo_student_data) {
                $this->a_page_data['personal_data_id'] = $mbo_student_data->personal_data_id;
                $this->a_page_data['student_id'] = $s_student_id;
                $this->a_page_data['student_data'] = $mbo_student_data;
                $this->a_page_data['body'] = $this->load->view('score/student_score', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
        }
    }

    public function form_filter_halfway($s_student_id = false)
    {
        if ($s_student_id) {
            $this->a_page_data['student_last_score'] = $this->Scm->get_last_score(array(
                'st.student_id' => $s_student_id
            ));

            $this->a_page_data['student_id'] = $s_student_id;
            $this->a_page_data['student_data'] = $this->Stm->get_student_filtered(array('ds.student_id' => $s_student_id));
        }

        $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, array(1,2));
        $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
        
        $this->load->view('score/form/form_filter_halfway', $this->a_page_data);
    }

    public function form_input_score()
    {
        $this->a_page_data['list_score'] = array();
        $this->load->view('score/form/form_input_score', $this->a_page_data);
    }

    public function view_filter_score()
    {
        $this->a_page_data['academic_year_lists'] = $this->Aym->get_academic_year_lists();
        $this->a_page_data['semester_type_lists'] = $this->Smm->get_semester_type_lists(false, false, array(1,2,4,7,8,5));
        $this->load->view('score/form/form_filter_score', $this->a_page_data);
    }

    public function view_table_student_supplement($s_student_id = false)
    {
        if ($this->input->is_ajax_request()) {
            // $s_student_id = $this->input->post('student_id');
            // $s_academic_year_id = $this->input->post('academic_year_id');
            // $s_semester_type_id = $this->input->post('semester_type_id');
            $mba_supplement_data = $this->Scm->get_supplement(array(
                'student_id' => $this->input->post('student_id'),
                'academic_year_id' => $this->input->post('academic_year_id'),
                'semester_type_id' => $this->input->post('semester_type_id')
            ));
            $a_return = array('code' => 0, 'data' => $mba_supplement_data);
            print json_encode($a_return);
        }else if ($s_student_id) {
            $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
            if ($mbo_student_data) {
                $this->a_page_data['student_category'] = $this->General->get_enum_values('dt_student_supplement', 'supplement_category');
                $this->a_page_data['personal_data_id'] = $mbo_student_data->personal_data_id;
                $this->a_page_data['student_id'] = $s_student_id;
                $this->load->view('score/table/student_supplement_lists', $this->a_page_data);
            }
        }
        
    }

    public function view_table_score_student($s_student_id = false)
    {
        if ($s_student_id) {
            $a_personal_data_allowed_setting_score = ['47013ff8-89df-11ef-8f45-0068eb6957a0','37b0f8e9-e13c-4104-adea-6c83ca1f5855'];
            $this->a_page_data['personal_data_allowed_setting_score'] = $a_personal_data_allowed_setting_score;
            
            $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
            if ($mbo_student_data) {
                $this->a_page_data['personal_data_id'] = $mbo_student_data->personal_data_id;
                $this->a_page_data['student_id'] = $s_student_id;
                $this->load->view('score/table/student_score_table', $this->a_page_data);
            }
        }
    }

    public function get_score_cummulative($s_student_id, $s_academic_year_id, $s_semester_type_id = false, $s_get_custom = 'gpa', $b_include_repeat = true, $b_all = false, $b_print = false)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        if ($mbo_student_data) {
            $a_clause = array(
                'sc.student_id' => $mbo_student_data->student_id,
                'curs.curriculum_subject_type !=' => 'extracurricular',
                'curs.curriculum_subject_credit >' => 0,
                'score_approval' => 'approved',
            );

            if ($b_all) {
                $a_clause['sc.score_display'] = 'TRUE';
            }

            if ($s_semester_type_id) {
                $a_clause['sc.academic_year_id'] = $s_academic_year_id;
                if ($s_semester_type_id != 3) {
                    $a_clause['sc.semester_type_id'] = $s_semester_type_id;
                    $mbo_score_lists = $this->Scm->get_score_data($a_clause);
                }else{
                    $mbo_score_lists = $this->Scm->get_score_data($a_clause, [7, 8]);
                }
            }else{
                $a_clause['sc.academic_year_id <= '] = $s_academic_year_id;
                $mbo_score_lists = $this->Scm->get_score_data($a_clause);
                $mbo_score_lists = $this->clear_semester_score($mbo_score_lists);
            }

            $a_credit = array();
            $a_merit = array();
            $a_absence = array();

            $i_sum_credit = 0;
            $i_sum_merit = 0;
            $i_sum_absence = 0;
            
            if ($mbo_score_lists) {

                foreach ($mbo_score_lists as $score) {
                    // $b_this_good_grades = true;
                    if ($b_all) {
                        $b_this_good_grades = $this->Scm->get_good_grades($score->subject_name, $score->student_id, $score->score_sum);
                    }else{
                        $b_this_good_grades = true;
                    }

                    if ($b_this_good_grades) {
                        $score_sum = intval(round($score->score_sum, 0, PHP_ROUND_HALF_UP));
                        if (!$b_include_repeat) {
                            $score_sum = $this->grades->get_score_sum($score->score_quiz, round($score->score_final_exam, 0, PHP_ROUND_HALF_UP));
                            $score_sum = intval(round($score_sum, 0, PHP_ROUND_HALF_UP));
                        }
                        
                        $score_grade = $this->grades->get_grade($score_sum);
                        $score_grade_point = $this->grades->get_grade_point($score_sum);
                        $score_merit = $this->grades->get_merit($score->curriculum_subject_credit, $score_grade_point);

                        array_push($a_credit, $score->curriculum_subject_credit);
                        array_push($a_merit, $score_merit);
                        array_push($a_absence, $score->score_absence);
                    }
                }

                $i_sum_credit = array_sum($a_credit);
                $i_sum_merit = array_sum($a_merit);
                $i_sum_absence = array_sum($a_absence);
            }

            $d_cummulative_score = $this->grades->get_ipk($i_sum_merit, $i_sum_credit);
            $d_average_absence = (count($a_absence) > 0) ? ($i_sum_absence / count($a_absence)) : 0;

            // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
            //     print('GPA: '.$d_cummulative_score.'<br>');
            //     print('Credit: '.$i_sum_credit.'<br>');
            //     print('Average absence '.$d_average_absence.'<br>');
            //     print('All score: <br><pre>');
            //     var_dump($mbo_score_lists);
            //     exit;
            // }

            if ($s_get_custom == 'gpa') {
                return $d_cummulative_score;
            }else if($s_get_custom == 'credit') {
                // // print('etts');
                // print('<pre>');
                // var_dump($a_credit);
                return $i_sum_credit;
            }else if ($s_get_custom == 'average_absence') {
                return $d_average_absence;
            }else if ($s_get_custom == 'testing') {
                return $mbo_score_lists;
            }
            
        }
    }

    public function get_score_list_by_id()
    {
        if ($this->input->is_ajax_request()) {
            $s_score_id = (empty($this->input->post('score_id')) ? false : $this->input->post('score_id'));
            $a_score_id = ((empty($this->input->post('a_score_id'))) OR ($this->input->post('a_score_id') == 'false')) ? false : $this->input->post('a_score_id');
            // $return = array('data' => false);
            if (($a_score_id) AND (count($a_score_id) == 0)) {
                $return = array('data' => false);
            }
            else if (!$a_score_id) {
                $return = array('data' => false);
            }
            else{
                $mba_score_data = $this->Scm->get_score_by_id($s_score_id, $a_score_id);
                $return = array('data' => $mba_score_data);
            }

            // $return = $this->input->post();

            print json_encode($return);
        }
    }

    public function filter_score_student()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            // $s_personal_data_id = $this->input->post('personal_data_id');
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');

            $s_subject_type = $this->input->post('curriculum_subject_type');
            $s_study_plan = $this->input->post('get_study_plan');
            $b_proccess = false;

            if ($s_semester_type_id == '4') {
                $b_proccess = true;
            }else if($s_semester_type_id == '5'){
                $b_proccess = true;
            }else if ($s_academic_year_id == '') {
                $b_proccess = false;
            }else if (($s_academic_year_id != 'all') AND ($s_semester_type_id == '')) {
                $b_proccess = false;
            }else{
                $b_proccess = true;
            }

            if ($b_proccess) {
                $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
                if ($mbo_student_data) {
                    if (($s_subject_type !== null) AND ($s_subject_type != 'all')) {
                        $a_score_filter = array(
                            'sc.student_id' => $mbo_student_data->student_id,
                            'curs.curriculum_subject_type' => $s_subject_type,
                            'score_approval' => 'approved'
                        );
                    }else if (($s_subject_type !== null) AND ($s_subject_type == 'all')) {
                        $a_score_filter = array(
                            'sc.student_id' => $mbo_student_data->student_id,
                            'score_approval' => 'approved'
                        );
                    }else{
                        $a_score_filter = array(
                            'sc.student_id' => $mbo_student_data->student_id,
                            'curs.curriculum_subject_type !=' => 'extracurricular',
                            'score_approval' => 'approved'
                        );
                    }

                    // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                    //     print('<pre>');var_dump($a_score_filter);exit;
                    // }

                    if (($s_study_plan !== null) AND ($s_study_plan == 'true')) {
                        $a_score_filter['sc.score_display'] = 'TRUE';
                        // $mbo_score_lists = $this->Scm->get_score_student($mbo_student_data->student_id);
                        $mbo_score_lists = $this->Scm->get_score_data($a_score_filter);
                        $mbo_score_lists = $this->clear_semester_score($mbo_score_lists);
                        $a_score_lists = false;
                        if ($mbo_score_lists) {
                            $a_score_lists = array();
                            foreach ($mbo_score_lists as $score) {
                                if (in_array($score->score_grade, array('D', 'F'))) {
                                    array_push($a_score_lists, $score);
                                }
                            }
                        }
                        $mbo_score_lists = $a_score_lists;
                    }else if ($s_academic_year_id == 'all') {
                        $a_score_filter['sc.score_display'] = 'TRUE';
                        $a_score_filter['curs.curriculum_subject_credit >'] = 0;
                        $mbo_score_lists = $this->Scm->get_score_data($a_score_filter);
                        $mbo_score_lists = $this->clear_semester_score($mbo_score_lists);
                        
                    }else{
                        if ($s_semester_type_id == '4') {
                            # Score OFSE Semester
                            $mbo_score_lists = $this->Scm->get_score_data($a_score_filter, array(4, 6));
                        }else if($s_semester_type_id == '5'){
                            $a_score_filter['sc.semester_type_id'] = $s_semester_type_id;
                            $mbo_score_lists = $this->Scm->get_score_data($a_score_filter);
                            // print('<pre>');var_dump($a_score_filter);exit;
                        }else{
                            # Score Normal Semester
                            $a_score_filter['sc.semester_type_id'] = $s_semester_type_id;
                            $a_score_filter['sc.academic_year_id'] = $s_academic_year_id;
                            $a_score_filter['curs.curriculum_subject_credit >'] = 0;
                            $mbo_score_lists = $this->Scm->get_score_data($a_score_filter);
                        }
                    }

                    if ($mbo_score_lists) {
                        foreach ($mbo_score_lists as $o_score) {
                            $a_score_filter['sn.subject_name'] = $o_score->subject_name;
                            $mba_score_ngulang = $this->Scm->get_score_data($a_score_filter);
                            $s_lecturer = 'N/A';
                            $s_academic_year_score = $o_score->academic_year_id.'-'.$o_score->semester_type_id;
                            if ($mba_score_ngulang) {
                                $a_score_semester = [];
                                foreach ($mba_score_ngulang as $o_ngulangscore) {
                                    array_push($a_score_semester, $o_ngulangscore->academic_year_id.'-'.$o_ngulangscore->semester_type_id);
                                }

                                $s_academic_year_score = implode(" | ", $a_score_semester);
                            }
                            $o_score->academic_year_score = $s_academic_year_score;

                            $score_sum = intval(round($o_score->score_sum, 0, PHP_ROUND_HALF_UP));
                            $score_grade = $this->grades->get_grade($score_sum);
                            $score_grade_point = $this->grades->get_grade_point($score_sum);
                            $score_merit = $this->grades->get_merit($o_score->curriculum_subject_credit, $score_grade_point);
                            $mba_class_master_data = $this->General->get_where('dt_class_master', ['class_master_id' => $o_score->class_master_id]);
                            
                            if (!is_null($o_score->class_master_id)) {
                                $mba_class_lecturer = $this->Cgm->get_class_master_lecturer(array(
                                    'class_master_id' => $o_score->class_master_id
                                ));

                                if ($mba_class_lecturer) {
                                    $a_lecturer = array();
                                    foreach ($mba_class_lecturer as $o_lecturer) {
                                        $s_lecturer_name = $this->Pdm->retrieve_title($o_lecturer->personal_data_id);
                                        if (!in_array($s_lecturer_name,  $a_lecturer)) {
                                            array_push($a_lecturer, $s_lecturer_name);
                                        }
                                    }

                                    if (count($a_lecturer) > 0) {
                                        $s_lecturer = implode(' & ', $a_lecturer);
                                    }
                                }
                            }

                            $o_score->class_master_link_exam = ($mba_class_master_data) ? $mba_class_master_data[0]->class_master_link_exam : '';
                            $o_score->class_master_link_exam_available = ($mba_class_master_data) ? $mba_class_master_data[0]->class_master_link_exam_available : '';
                            $o_score->lecturer_class = $s_lecturer;
                            $o_score->score_sum = $score_sum;
                            $o_score->score_merit = $score_merit;
                            $o_score->score_grade = $score_grade;
                            $o_score->score_grade_point = $score_grade_point;
                            $o_score->score_final_exam = round($o_score->score_final_exam, 0, PHP_ROUND_HALF_UP);
                            $o_score->score_quiz = round($o_score->score_quiz, 0, PHP_ROUND_HALF_UP);
                        }
                    }
                    $a_rtn = array('code' => 0, 'data' => $mbo_score_lists);
                }else{
                    $a_rtn = array('code' => 1, 'data' => false, 'message' => $s_student_id);
                }
            }else{
                $a_rtn = array('code' => 0, 'data' => false);
            }
            print json_encode($a_rtn);
        }
    }

    public function save_supplement()
    {
        if ($this->input->is_ajax_request()) {
            $mba_employee_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $this->session->userdata('user')));
            if ($mba_employee_data) {
                $this->form_validation->set_rules('supplement_category','Category', 'required|trim');
                $this->form_validation->set_rules('supplement_comment', 'Comment', 'required|trim');
                if (($this->input->post('academic_year_id') == '') OR ($this->input->post('semester_type_id') == '') OR ($this->input->post('student_id') == '')) {
                    $a_return = array('code' => 1, 'message' => 'Error retrieving data!');
                }else if ($this->form_validation->run()) {
                    $mbo_student_data = $this->Stm->get_student_by_id($this->input->post('student_id'));
                    if ($mbo_student_data->student_status != 'active') {
                        $a_return = array('code' => 1, 'message' => 'Student status is not active');
                    }else{
                        $a_supplement_data = array(
                            'supplement_id' => $this->uuid->v4(),
                            'student_id' => $this->input->post('student_id'),
                            'employee_id' => $mba_employee_data[0]->employee_id,
                            'academic_year_id' => $this->input->post('academic_year_id'),
                            'semester_type_id' => $this->input->post('semester_type_id'),
                            'supplement_comment' => set_value('supplement_comment'),
                            'supplement_category' => set_value('supplement_category'),
                            'date_added' => date('Y-m-d H:i:s')
                        );
    
                        $save_data = $this->Scm->save_student_suppement($a_supplement_data);
                        if ($save_data) {
                            $a_return = array('code' => 0, 'message' => 'Success');
                        }else{
                            $a_return = array('code' => 1, 'message' => 'Error saving data!');
                        }
                    }
                }else{
                    $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
                }
            }else{
                $a_return = array('code' => 1, 'message' => 'Your employee data not found!');
            }

            print json_encode($a_return);
        }
    }

    public function halfway_transcript_all()
    {
        if ($this->input->is_ajax_request()) {
            $a_filter_data = $this->input->post();
			foreach($a_filter_data as $key => $value){
				if($a_filter_data[$key] == 'all'){
					unset($a_filter_data[$key]);
				}
				else if($key == 'study_program_id') {
					$a_filter_data['ds.study_program_id'] = $value;
					unset($a_filter_data[$key]);
				}
			}
			$mba_filtered_data = $this->Stm->get_student_filtered($a_filter_data);
			
			print json_encode(array('code' => 0, 'data' => $mba_filtered_data));
        }
    }

    public function generate_transcript_halfway()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $b_ects = ($this->input->post('ects') !== null) ? true : false;
            $b_fgrade = ($this->input->post('f_grade') !== null) ? true : false;
            $b_ascsign = ($this->input->post('asc_sign') !== null) ? true : false;
            $b_short_semester = ($this->input->post('short_semester') !== null) ? true : false;
            $this->form_validation->set_rules('academic_year_start', 'Academic Year Semester Start', 'required');
            $this->form_validation->set_rules('academic_year_end', 'Academic Year Semester End', 'required');
            $this->form_validation->set_rules('semester_type_start', 'Semester Type Start', 'required');
            $this->form_validation->set_rules('semester_type_end', 'Semester Type End', 'required');

            if (($this->input->post('send_email') !== null) AND ($this->input->post('send_email') != '')) {
                $this->form_validation->set_rules('mail_student', 'Email To', 'required');
                $this->form_validation->set_rules('mail_subject', 'Email Subject', 'required');
                // $this->form_validation->set_rules('body_email', 'Message', 'required');
            }

            $transcript_halfway = false;
            if ($this->form_validation->run()) {
                $transcript_halfway = modules::run(
                    'download/excel_download/generate_halfway_transcript',
                    $s_student_id,
                    set_value('academic_year_start'),
                    set_value('semester_type_start'),
                    set_value('academic_year_end'),
                    set_value('semester_type_end'),
                    $b_ects,
                    $b_short_semester,
                    $b_fgrade,
                    $b_ascsign
                );

                if ($transcript_halfway) {
                    if (($this->input->post('send_email') !== null) AND ($this->input->post('send_email') != '')) {
                        $a_email_to = explode('; ', set_value('mail_student'));
                        if (count($a_email_to) > 0) {
                            $send_email = modules::run('messaging/send_email',
                                $a_email_to,
                                set_value('mail_subject'),
                                $this->input->post('body_email'),
                                'employee@company.ac.id',
                                array(
                                    'employee@company.ac.id',
                                    $this->config->item('email')['academic']['head']
                                ),
                                $transcript_halfway['file_path']
                            );

                            if ($send_email) {
                                $a_return = array('code' => 0, 'data' => $transcript_halfway['filename']);
                            }else {
                                $a_return = array('code' => 1, 'message' => 'Email to '.set_value('mail_student').' failed to send');
                            }
                        }else{
                            $a_return = array('code' => 1, 'message' => 'Destination email not found!');
                        }
                    }else{
                        $a_return = array('code' => 0, 'data' => $transcript_halfway['filename']);
                    }
                }else{
                    $a_return = array('code' => 1, 'message' => 'Failed generate transcript halfway data');
                }
            }else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
            exit;
        }
    }
    
    public function remove_supplement()
    {
        if ($this->input->is_ajax_request()) {
            $s_supplement_id = $this->input->post('supplement_id');
            if ($this->Scm->remove_student_supplement($s_supplement_id)) {
                $a_return = array('code' => 0, 'message' => 'Success');
            }else{
                $a_return = array('code' => 1, 'message' => 'Error removing data');
            }

            print json_encode($a_return);
        }
    }

    public function clear_score($mba_score_data, $a_filter_semester = false)
    {
        $a_score_final = false;
        if ($mba_score_data) {
            $a_score_final = array();
            foreach ($mba_score_data as $score) {
                if ($pos = array_search($score->subject_name, array_column($a_score_final, 'subject_name'))) {
                    if ($a_score_final[$pos]->score_grade > $score->score_grade) {
                        unset($a_score_final[$pos]);
                        $a_score_final[$pos] = $score;
                    }
                }else{
                    array_push($a_score_final, $score);
                }
            }
            $a_score_final = array_values($a_score_final);
        }

        return $a_score_final;
    }

    public function clear_semester_score($mba_score_data, $a_filter_semester = false)
    {
        $a_score_final = false;

        if (($a_filter_semester) AND ($mba_score_data)) {
            foreach ($mba_score_data as $key => $final) {
                if (($a_filter_semester['semester_type_start'] !== null) AND ($a_filter_semester['semester_type_start'] == 2)) {
                    if (($a_filter_semester['academic_year_start'] == $final->academic_year_id) AND ($final->semester_type_id == 1)) {
                        unset($mba_score_data[$key]);
                    }
                }

                if (($a_filter_semester['semester_type_end'] !== null) AND ($a_filter_semester['semester_type_end'] == 1)) {
                    // if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id == 2)) {
                    if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id != 1)) {
                        unset($mba_score_data[$key]);
                    }
                }
            }
        }

        if ($mba_score_data) {
            $a_score_final = array();
            $i = 0;
            foreach ($mba_score_data as $score) {
                $pos = array_search($score->subject_name, array_column($a_score_final, 'subject_name'));
                $pos++;
                if ($pos) {
                    $pos--;
                    if ($a_score_final[$pos]->score_merit < $score->score_merit) {
                        // unset($a_score_final[$pos]);
                        $a_score_final[$pos] = $score;
                        // $a_score_final = array_values($a_score_final);
                    }
                }else{
                    array_push($a_score_final, $score);
                    // $a_score_final = array_values($a_score_final);
                }
            }
            $a_score_final = array_values($a_score_final);
        }

        return $a_score_final;
    }

    public function calculate_score($s_score_id)
    {
        $mbo_score_data = $this->Scm->get_score_data(array('score_id' => $s_score_id))[0];
        $return = false;
        if ($mbo_score_data) {
            $return = array();
            $return['score_sum'] = intval(round($mbo_score_data->score_sum, 0, PHP_ROUND_HALF_UP));
            $return['score_grade'] = $this->grades->get_grade($return['score_sum']);
            $return['score_grade_point'] = $this->grades->get_grade_point($return['score_sum']);
            $return['score_merit'] = $this->grades->get_merit($mbo_score_data->curriculum_subject_credit, $return['score_grade_point']);
        }

        return $return;
    }

    public function calculate_score_absence_student($s_score_id)
    {
        $mba_score_data = $this->Scm->get_score_data(array('score_id' => $s_score_id))[0];
        if (!is_null($mba_score_data->class_master_id)) {
            $a_class_param = array(
                'cm.class_master_id' => $mba_score_data->class_master_id
            );
        }else{
            $a_class_param = array(
                'cmc.class_group_id' => $mba_score_data->class_group_id
            );
        }

        $mba_class_subject_data = $this->Cgm->get_class_master_subject($a_class_param)[0];
        if ($mba_class_subject_data) {
            $i_subject_credit = $mba_class_subject_data->sks;

            $mba_absence_student_lists = $this->Cgm->get_absence_student(array('score_id' => $s_score_id, 'absence_status' => 'ALPHA'));
            $i_count_alpha =  ($mba_absence_student_lists) ? count($mba_absence_student_lists) : 0;

            $i_score_recap = $this->grades->get_score_absence($i_count_alpha, $i_subject_credit);
            return $i_score_recap;
        }else {
            return false;
        }
    }
}
