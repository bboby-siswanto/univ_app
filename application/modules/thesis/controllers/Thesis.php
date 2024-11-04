<?php
class Thesis extends App_core
{
    public $s_access;
    function __construct()
    {
        parent::__construct();
        $this->load->model('Thesis_model', 'Tm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('employee/Employee_model', 'Em');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('academic/Academic_year_model', 'Aym');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('institution/Institution_model', 'Inm');
        $this->a_page_data['access_thesis']  = $this->s_access = $this->get_access();
    }

    public function thesis_student()
    {
        // $type_allowd = ['staff', 'lect','lecturer'];
        // if (in_array($this->session->userdata('type'), $type_allowd)) {
            // $this->a_page_data['status_list'] = $this->get_log_status();
            $this->a_page_data['study_program_list'] = $this->Spm->get_study_program(false, false);
            // $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, array(1,2));
            $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
            $this->a_page_data['list_filetype'] = $this->General->get_enum_values('thesis_students_file', 'thesis_filetype');
            $this->a_page_data['body'] = $this->load->view('thesis/thesis_student_list', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        // }
        // else {
        //     $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
        //     $this->load->view('layout', $this->a_page_data);
        // }
    }

    public function view_access()
    {
        print($this->s_access);exit;
    }

    public function thesis_detail($s_student_id) {
        $mba_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $s_student_id));
        if ($mba_student_data) {
            $mba_thesis_data = $this->Tm->get_student_list_thesis([
                'ts.student_id' => $s_student_id
            ]);
            $mba_thesis_log_data = false;
            $mba_thesis_advisor_approved_1 = false;
            $mba_thesis_advisor_approved_2 = false;

            if ($mba_thesis_data) {
                $o_thesis_data = $mba_thesis_data[0];
                $mba_thesis_log_data = $this->Tm->get_thesis_log($o_thesis_data->thesis_student_id, [
                    'tsl.thesis_log_type' => 'work'
                ]);

                $mba_thesis_advisor_approved_1 = $this->Tm->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_thesis_data->thesis_student_id,
                    'tsa.advisor_type' => 'approved_advisor_1'
                ], 'advisor');
                $mba_thesis_advisor_approved_2 = $this->Tm->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_thesis_data->thesis_student_id,
                    'tsa.advisor_type' => 'approved_advisor_2'
                ], 'advisor');
            }

            if ($mba_thesis_advisor_approved_1) {
                $mba_thesis_advisor_approved_1 = $mba_thesis_advisor_approved_1[0];
                $mba_thesis_advisor_approved_1->advisor_name = $this->Pdm->retrieve_title($mba_thesis_advisor_approved_1->personal_data_id);
            }

            if ($mba_thesis_advisor_approved_2) {
                $mba_thesis_advisor_approved_2 = $mba_thesis_advisor_approved_2[0];
                $mba_thesis_advisor_approved_2->advisor_name = $this->Pdm->retrieve_title($mba_thesis_advisor_approved_2->personal_data_id);
            }
            
            $this->a_page_data['thesis_data'] = $mba_thesis_data;
            $this->a_page_data['thesis_page_type'] = 'work_submission';
            $this->a_page_data['thesis_log_data'] = $mba_thesis_log_data;
            $this->a_page_data['advisor_approved_1'] = $mba_thesis_advisor_approved_1;
            $this->a_page_data['advisor_approved_2'] = $mba_thesis_advisor_approved_2;
            $this->a_page_data['o_student_data'] = $mba_student_data[0];
            $this->a_page_data['allow_update'] = false;
            $this->a_page_data['body'] = $this->load->view('thesis/student/thesis_student', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    public function get_list_student()
    {
        if ($this->input->is_ajax_request()) {
            $s_study_program_id = $this->input->post('thesis_filter_prodi');
            $s_batch = $this->input->post('thesis_filter_batch');

            $a_filter = [
                'st.study_program_id' => $s_study_program_id,
                'st.academic_year_id' => $s_batch
            ];

            if (empty($s_study_program_id)) {
                unset($a_filter['st.study_program_id']);
            }
            if (empty($s_batch)) {
                unset($a_filter['st.academic_year_id']);
            }

            $mba_list_file = $this->General->get_enum_values('thesis_students_file', 'thesis_filetype');
            // switch ($this->s_access) {
            //     case 'super':
                    $mba_student_list_data = $this->Tm->get_student_list_thesis($a_filter);
                    // break;

            //     case 'deans':
            //         $a_filter['fc.deans_id'] = $this->session->userdata('user');
            //         $mba_student_list_data = $this->Tm->get_student_list_thesis($a_filter);
            //         break;

            //     case 'hod':
            //         $a_filter['sp.head_of_study_program_id'] = $this->session->userdata('user');
            //         $mba_student_list_data = $this->Tm->get_student_list_thesis($a_filter);
            //         break;
                    
            //     case 'hsp':
            //         // $a_filter['sp.head_of_study_program_id'] = $this->session->userdata('user');
            //         $mba_student_list_data = $this->Tm->get_student_list_thesis($a_filter);
            //         break;

            //     case 'advisor':
            //         $mba_student_list_data = false;
            //         $mba_advisor_data = $this->General->get_where('thesis_advisor', ['personal_data_id' => $this->session->userdata('user')]);
            //         if ($mba_advisor_data) {
            //             $a_filter['tsad.advisor_id'] = $mba_advisor_data[0]->advisor_id;
            //             $mba_student_list_data = $this->Tm->get_student_list_thesis($a_filter);
            //         }
            //         break;

            //     case 'examiner':
            //         $mba_student_list_data = false;
            //         $mba_advisor_data = $this->General->get_where('thesis_advisor', ['personal_data_id' => $this->session->userdata('user')]);
            //         if ($mba_advisor_data) {
            //             $a_filter['tsex.advisor_id'] = $mba_advisor_data[0]->advisor_id;
            //             $mba_student_list_data = $this->Tm->get_student_list_thesis($a_filter);
            //         }
            //         break;
                
            //     default:
            //         $mba_student_list_data = false;
            //         break;
            // }
            // $mba_student_list_data = $this->Tm->get_student_list_thesis();
            if ($mba_student_list_data) {
                foreach ($mba_student_list_data as $o_student) {
                    // print('thesis_student<pre>');var_dump($o_student);exit;
                    if (is_array($mba_list_file)) {
                        foreach ($mba_list_file as $s_filetype) {
                            $mba_file_doc = $this->Tm->get_thesis_list_files([
                                'ts.thesis_student_id' => $o_student->thesis_student_id,
                                'sf.thesis_filetype' => $s_filetype
                            ]);
                            // if (($o_student->student_id == '5f76c22f-171c-46a0-bdb4-37558581b6dc') AND ($s_filetype == 'thesis_final_file')) {
                                // print('thesis_file<pre>');var_dump($mba_file_doc);exit;
                            // }
                            $o_student->$s_filetype = ($mba_file_doc) ? $mba_file_doc[0]->thesis_filename : '';
                        }
                    }
                    $mba_final_last_log_status = $this->Tm->get_thesis_log($o_student->thesis_student_id, [
                        'thesis_log_type' => 'final'
                    ]);

                    $mba_student_advisor = $this->Tm->get_list_student_advisor([
                        'ts.thesis_student_id' => $o_student->thesis_student_id
                    ], 'advisor', true);

                    $mba_student_examiner = $this->Tm->get_list_student_advisor([
                        'ts.thesis_student_id' => $o_student->thesis_student_id
                    ], 'examiner');

                    $mba_thesis_student = $this->Tm->get_thesis_defense_student([
                        'ts.thesis_student_id' => $o_student->thesis_student_id
                    ]);

                    $a_advisor_list = [];
                    $a_examiner_list = [];
                    if ($mba_student_advisor) {
                        foreach ($mba_student_advisor as $o_advisor) {
                            $s_advisor_name = $this->General->retrieve_title($o_advisor->personal_data_id);
                            if (!in_array($s_advisor_name, $a_advisor_list)) {
                                array_push($a_advisor_list, $s_advisor_name);
                            }
                        }
                    }
                    if ($mba_student_examiner) {
                        foreach ($mba_student_examiner as $o_examiner) {
                            $s_examiner_name = $this->General->retrieve_title($o_examiner->personal_data_id);
                            if (!in_array($s_examiner_name, $a_examiner_list)) {
                                array_push($a_examiner_list, $s_examiner_name);
                            }
                        }
                    }
                    $o_student->last_final_log = ($mba_final_last_log_status) ? $mba_final_last_log_status[0] : false;
                    $o_student->list_advisor_name = (count($a_advisor_list) > 0) ? implode('|', $a_advisor_list) : '';
                    $o_student->list_examiner_name = (count($a_examiner_list) > 0) ? implode('|', $a_examiner_list) : '';
                    $o_student->current_progress = ($o_student->student_status == 'graduated') ? 'graduated' : $o_student->current_progress;
                    $o_student->defense_id = ($mba_thesis_student) ? $mba_thesis_student[0]->thesis_defense_id : false;
                }
            }
            print json_encode(['data' => $mba_student_list_data]);exit;
        }
    }

    public function view_serv()
	{
		print('<pre>');var_dump($_SERVER);exit;
	}

    public function advisor_examiner_list()
    {
        $this->a_page_data['body'] = $this->load->view('advisor_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function open_score()
    {
        if ($this->input->is_ajax_request()) {
            $post = $this->input->post();
            $a_data = [
                'score_status' => 'open'
            ];
            
            if ($post['key'] == 'score_presentation_id') {
                $this->Tm->submit_thesis_score_presentation($a_data, [
                    'score_presentation_id' => $post['key_id']
                ]);
            }
            else if ($post['key'] == 'score_evaluation_id') {
                $this->Tm->submit_thesis_score_evaluation($a_data, [
                    'score_evaluation_id' => $post['key_id']
                ]);
            }
            $a_return = ['code' => 0, 'message' => 'success'];
            print json_encode($a_return);exit;
        }
    }

    function list_advisor() {
        $mba_advisor_list = $this->Tm->get_advisor_list();
        if ($mba_advisor_list) {
            $s_table = '<table border="1">';
            $s_table .= '<tr>';
            $s_table .= '<td>Advisor ID</td>';
            $s_table .= '<td>Advisor Fullname</td>';
            $s_table .= '</tr>';
            foreach ($mba_advisor_list as $o_advisor) {
                $mba_employee_data = $this->General->get_where('dt_employee', ['personal_data_id' => $o_advisor->personal_data_id]);
                $o_advisor->employee_email = ($mba_employee_data) ? $mba_employee_data[0]->employee_email : false;
                $o_advisor->advisor_name = $this->Pdm->retrieve_title($o_advisor->personal_data_id);

                $s_table .= '<tr>';
                $s_table .= '<td>'.$o_advisor->advisor_id.'</td>';
                $s_table .= '<td>'.$o_advisor->advisor_name.'</td>';
                $s_table .= '</tr>';
            }
            $s_table .= '</table>';

            print($s_table);exit;
        }
    }

    public function get_list_advisor()
    {
        if ($this->input->is_ajax_request()) {
            $mba_advisor_list = $this->Tm->get_advisor_list();
            if ($mba_advisor_list) {
                foreach ($mba_advisor_list as $o_advisor) {
                    $mba_employee_data = $this->General->get_where('dt_employee', ['personal_data_id' => $o_advisor->personal_data_id]);
                    $o_advisor->employee_email = ($mba_employee_data) ? $mba_employee_data[0]->employee_email : $o_advisor->personal_data_email;
                    $o_advisor->advisor_name = $this->Pdm->retrieve_title($o_advisor->personal_data_id);
                }
            }

            print json_encode(['data' => $mba_advisor_list]);
        }
    }

    public function publish_defense_score()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_defense_id = $this->input->post('thesis_defense_id');

            $mba_thesis_defense_data = $this->Tm->get_thesis_defense_student([
                'td.thesis_defense_id' => $s_thesis_defense_id
            ]);
            
            if ($mba_thesis_defense_data) {
                $o_defense_data = $mba_thesis_defense_data[0];

                // get_score_like_subject_name($a_clause = false, $s_subject_name = false, $s_ordering = 'ASC')
                $mba_score_thesis_data = $this->Scm->get_score_like_subject_name([
                    'sc.student_id' => $o_defense_data->student_id
                ], 'thesis', 'DESC');

                if ($mba_score_thesis_data) {
                    $o_score_data = $mba_score_thesis_data[0];
                    $s_grade_point = $this->grades->get_grade_point($o_defense_data->score_final);
                    $a_score_data = [
                        'score_quiz' => $o_defense_data->score_final,
                        'score_quiz1' => $o_defense_data->score_final,
                        'score_final_exam' => $o_defense_data->score_final,
                        'score_sum' => $o_defense_data->score_final,
                        'score_grade' => $this->grades->get_grade($o_defense_data->score_final),
                        'score_grade_point' => $s_grade_point,
                        'score_ects' => $this->grades->get_ects_score($o_score_data->curriculum_subject_credit, $o_score_data->subject_name),
                        'score_merit' => $this->grades->get_merit($o_score_data->curriculum_subject_credit, $s_grade_point)
                    ];

                    $this->Scm->save_data($a_score_data, [
                        'score_id' => $o_score_data->score_id
                    ]);

                    $a_return = ['code' => 0, 'message' => 'Success'];
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'KRS Thesis not found!'];
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Defense data not found!'];
            }

            print json_encode($a_return);
        }
    }

    public function force_publish_all_score()
    {
        // if ($this->input->is_ajax_request()) {
            $s_academic_year_id = 2023;
            $s_semester_type_id = 2;

            $mba_thesis_student_list = $this->Tm->get_thesis_defense_student([
                'td.academic_year_id' => $s_academic_year_id,
                'td.semester_type_id' => $s_semester_type_id
            ]);
            $s_message = '';
            // print('<pre>');var_dump($mba_thesis_student_list);exit;

            if ($mba_thesis_student_list) {
                $i = 1;
                foreach ($mba_thesis_student_list as $o_defense_data) {
                    $mba_score_thesis_data = $this->Scm->get_score_like_subject_name([
                        'sc.student_id' => $o_defense_data->student_id
                    ], 'Thesis / ', 'DESC');
                    // print($mba_score_thesis_data[0]->subject_name);

                    if ($mba_score_thesis_data) {
                        $o_score_data = $mba_score_thesis_data[0];
                        $s_grade_point = $this->grades->get_grade_point($o_defense_data->score_final);
                        $a_score_data = [
                            'score_quiz' => $o_defense_data->score_final,
                            'score_quiz1' => $o_defense_data->score_final,
                            'score_final_exam' => $o_defense_data->score_final,
                            'score_sum' => $o_defense_data->score_final,
                            'score_grade' => $this->grades->get_grade($o_defense_data->score_final),
                            'score_grade_point' => $s_grade_point,
                            'score_ects' => $this->grades->get_ects_score($o_score_data->curriculum_subject_credit, $o_score_data->subject_name),
                            'score_merit' => $this->grades->get_merit($o_score_data->curriculum_subject_credit, $s_grade_point)
                        ];

                        $a_student_thesis_update = [
                            'student_thesis_title' => $o_defense_data->thesis_title
                        ];

                        $this->Stm->update_student_data($a_student_thesis_update, $o_defense_data->student_id);
                        $this->Scm->save_data($a_score_data, [
                            'score_id' => $o_score_data->score_id
                        ]);
                    }
                    else {
                        $s_message .= 'KRS Thesis Not found for student '.$o_defense_data->personal_data_name.'!<br>';
                        break;
                        // $a_return = ['code' => 1, 'message' => 'KRS Thesis not found!'];
                    }
                }
            }
            else {
                $s_message = 'no student found!';
            }

            if (!empty($s_message)) {
                $a_return = ['code' => 1, 'message' => $s_message];
            }
            else {
                $a_return = ['code' => 0, 'message' => 'Success'];
            }

            print json_encode($a_return);exit;
        // }
    }

    public function get_list_remarks()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_log_id = $this->input->post('thesis_log_id');
            $mba_note_list = $this->General->get_where('thesis_student_log_notes', ['thesis_logs_id' => $s_thesis_log_id]);
            print json_encode(['data' => $mba_note_list]);
        }
    }

    public function submit_student_proposal()
    {
        if ($this->input->is_ajax_request()) {
            // print('<pre>');var_dump($this->input->post());exit;
            $s_thesis_student_id = $this->input->post('thesis_student_id');
            $s_thesis_log_id = $this->input->post('thesis_log_key');
            $s_thesis_title = $this->input->post('thesis_title');
            $s_advisor_1_id = $this->input->post("advisor_1_update");
            $s_advisor_2_id = $this->input->post("advisor_2_update");

            if ((empty($s_advisor_1_id)) AND (empty($s_thesis_title))) {
                $a_return = ['code' => 1, 'message' => 'Thesis title and advisor 1 field is required!'];
            }
            else {
                $this->db->trans_begin();
                $a_thesis_student_data = [
                    'thesis_title' => ($this->input->post('thesis_title') == '') ? NULL : $this->input->post('thesis_title'),
                    'student_id' => $this->session->userdata('student_id'),
                    'remarks' => NULL,
                    'current_progress' => 'proposal',
                    'current_status' => 'pending',
                    'date_added' => date('Y-m-d H:i:s'),
                ];

                if (empty($s_thesis_student_id)) {
                    $s_thesis_student_id = $this->uuid->v4();
                    $a_thesis_student_data['thesis_student_id'] = $s_thesis_student_id;
                    $this->Tm->submit_thesis_student($a_thesis_student_data);
                }
                else {
                    $this->Tm->submit_thesis_student($a_thesis_student_data, ['thesis_student_id' => $s_thesis_student_id]);
                }

                $a_thesis_log_data = [
                    'thesis_student_id' => $s_thesis_student_id,
                    'academic_year_id' => ($this->session->has_userdata('academic_year_id_active')) ? $this->session->userdata('academic_year_id_active') : NULL,
                    'semester_type_id' => ($this->session->has_userdata('semester_type_id_active')) ? $this->session->userdata('semester_type_id_active') : NULL,
                    'thesis_log_type' => 'proposal',
                    'thesis_status' => 'pending'
                ];

                if (empty($s_thesis_log_id)) {
                    $s_thesis_log_id = $this->uuid->v4();
                    $a_thesis_log_data['thesis_log_id'] = $s_thesis_log_id;
                    $this->Tm->submit_log_status($a_thesis_log_data);
                }
                else {
                    $this->Tm->submit_log_status($a_thesis_log_data, ['thesis_log_id' => $s_thesis_log_id]);
                }
                // print('<pre>');var_dump($mbs_advisor_1);exit;
                if (!empty($s_advisor_1_id)) {
                    $student_advisor_1 = $this->Tm->get_list_student_advisor([
                        'ts.thesis_student_id' => $s_thesis_student_id,
                        'tsa.advisor_type' => 'proposed_advisor_1'
                    ], 'advisor');

                    $a_student_advisor_data_1 = [
                        'thesis_student_id' => $s_thesis_student_id,
                        'advisor_id' => $s_advisor_1_id,
                        'advisor_type' => 'proposed_advisor_1'
                    ];
                    if ($student_advisor_1) {
                        $this->Tm->submit_student_advisor($a_student_advisor_data_1, ['student_advisor_id' => $student_advisor_1[0]->student_advisor_id]);
                    }
                    else {
                        $a_student_advisor_data_1['student_advisor_id'] = $this->uuid->v4();
                        $this->Tm->submit_student_advisor($a_student_advisor_data_1);
                    }
                }
    
                if (!empty($s_advisor_2_id)) {
                    $student_advisor_2 = $this->Tm->get_list_student_advisor([
                        'ts.thesis_student_id' => $s_thesis_student_id,
                        'tsa.advisor_type' => 'proposed_advisor_2'
                    ], 'advisor');

                    $a_student_advisor_data_2 = [
                        'thesis_student_id' => $s_thesis_student_id,
                        'advisor_id' => $s_advisor_2_id,
                        'advisor_type' => 'proposed_advisor_2'
                    ];
                    if ($student_advisor_2) {
                        $this->Tm->submit_student_advisor($a_student_advisor_data_2, ['student_advisor_id' => $student_advisor_2[0]->student_advisor_id]);
                    }
                    else {
                        $a_student_advisor_data_2['student_advisor_id'] = $this->uuid->v4();
                        $this->Tm->submit_student_advisor($a_student_advisor_data_2);
                    }
                }

                if ($this->db->trans_status() === TRUE) {
                    $a_return = ['code' => 0, 'message' => 'Success!'];
                    $this->send_notification($s_thesis_student_id, 'thesis proposal submission');
                    $this->db->trans_commit();
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'Error processing data!'];
                    $this->db->trans_rollback();
                }
            }

            print json_encode($a_return);
        }
    }

    public function get_advisor_examiner()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_defense_id = $this->input->post('thesis_defense_id');
            $mba_thesis_defense_data = $this->General->get_where('thesis_defense', ['thesis_defense_id' => $s_thesis_defense_id]);
            // print("<pre>");var_dump($mba_thesis_defense_data);exit;
            if ($mba_thesis_defense_data) {
                $s_thesis_student_id = $mba_thesis_defense_data[0]->thesis_students_id;
                $a_return_data = [];
                $mba_advisor_data = $this->Tm->get_list_student_advisor([
                    'ts.thesis_student_id' => $s_thesis_student_id
                ], 'advisor', true);
                $mba_examiner_data = $this->Tm->get_list_student_advisor([
                    'ts.thesis_student_id' => $s_thesis_student_id
                ], 'examiner');

                if ($mba_advisor_data) {
                    foreach ($mba_advisor_data as $o_advisor) {
                        $s_advisor_name = $this->Pdm->retrieve_title($o_advisor->personal_data_id);
                        $mba_twe_score = $this->Tm->get_thesis_evaluation([
                            'ts.student_advisor_id' => $o_advisor->student_advisor_id
                        ]);

                        $mba_tpe_score = $this->Tm->get_thesis_presentation([
                            'ts.student_advisor_id' => $o_advisor->student_advisor_id
                        ]);

                        array_push($a_return_data, [
                            'user_name' => $s_advisor_name,
                            'user_type' => ucwords(strtolower(str_replace('approved_', ' ', $o_advisor->advisor_type))),
                            'thesis_student_id' => $s_thesis_student_id,
                            'student_advisor_id' => $o_advisor->student_advisor_id,
                            'student_examiner_id' => NULL,
                            'twe_score' => $mba_twe_score,
                            'tpe_score' => $mba_tpe_score,
                        ]);
                    }
                }

                if ($mba_examiner_data) {
                    foreach ($mba_examiner_data as $o_examiner) {
                        $s_examiner_name = $this->Pdm->retrieve_title($o_examiner->personal_data_id);
                        $mba_tpe_score = $this->Tm->get_thesis_presentation([
                            'ts.student_examiner_id' => $o_examiner->student_examiner_id
                        ]);

                        array_push($a_return_data, [
                            'user_name' => $s_examiner_name,
                            'user_type' => ucwords(strtolower(str_replace('_', ' ', $o_examiner->examiner_type))),
                            'thesis_student_id' => $s_thesis_student_id,
                            'student_advisor_id' => NULL,
                            'student_examiner_id' => $o_examiner->student_examiner_id,
                            'twe_score' => false,
                            'tpe_score' => $mba_tpe_score,
                        ]);
                    }
                }

                $a_return_data = (count($a_return_data) > 0) ? array_values($a_return_data) : false;

                print json_encode(['data' => $a_return_data]);
            }
            else {
                print json_encode(['data' => false]);
            }
        }
    }

    public function submit_schedule()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_student_id = $this->input->post('sc_thesis_student_id');
            $s_thesis_defense_id = $this->input->post('thesis_defense_id');

            $this->form_validation->set_rules('sc_academic_year_id', 'Academic Year', 'required');
            $this->form_validation->set_rules('sc_semester_type_id', 'Semester Type', 'required');
            $this->form_validation->set_rules('sc_room', 'Room', 'required');
            $this->form_validation->set_rules('sc_date', 'Date', 'required');
            $this->form_validation->set_rules('sc_time_start', 'Time Start', 'required');
            $this->form_validation->set_rules('sc_time_end', 'Time End', 'required');

            if (empty($s_thesis_student_id)) {
                $a_return = ['code' => 1, 'message' => 'Error retrieve param data!'];
            }
            else if ($this->form_validation->run()) {
                $a_defense_data = [
                    'thesis_students_id' => $s_thesis_student_id,
                    'thesis_defense_date' => set_value('sc_date'),
                    'thesis_defense_room' => set_value('sc_room'),
                    'thesis_defense_time_start' => set_value('sc_time_start'),
                    'thesis_defense_time_end' => set_value('sc_time_end'),
                    'academic_year_id' => set_value('sc_academic_year_id'),
                    'semester_type_id' => set_value('sc_semester_type_id')
                ];

                $mba_thesis_defense_data = $this->General->get_where('thesis_defense', ['thesis_students_id' => $s_thesis_student_id]);
                if ($mba_thesis_defense_data) {
                    $this->Tm->update_thesis_defense($a_defense_data, $mba_thesis_defense_data[0]->thesis_defense_id);
                }
                else {
                    $a_defense_data['thesis_defense_id'] = $this->uuid->v4();
                    $this->Tm->insert_thesis_defense($a_defense_data);
                }

                $a_return = ['code' => 0, 'message' => 'Success!'];
            }
            else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }

    public function new_advisor()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('advisor_personal_data_name', 'Advisor Name', 'required');
            $this->form_validation->set_rules('advisor_institution_name', 'Institution', 'required');
            
            if ($this->form_validation->run()) {
                $s_personal_data_name = set_value('advisor_personal_data_name');
                $s_personal_data_id = $this->input->post("advisor_personal_data_id");
                $s_institution_name = set_value('advisor_institution_name');
                $s_institution_id = $this->input->post("advisor_institution_id");

                $this->db->trans_begin();
                if (empty($s_personal_data_id)) {
                    $mba_personal_data = $this->Pdm->get_personal_data_by_name($s_personal_data_name, true);
                    if ($mba_personal_data) {
                        $s_personal_data_id = $mba_personal_data[0]->personal_data_id;
                    }
                    else {
                        $s_personal_data_id = $this->uuid->v4();
                        $a_personal_data = [
                            'personal_data_id' => $s_personal_data_id,
                            'personal_data_name' => $s_personal_data_name,
                            'personal_data_cellular' => 0
                        ];
                        $this->Pdm->create_personal_data_parents($a_personal_data);
                    }
                }

                if (empty($s_institution_id)) {
                    $mba_institution_data = $this->Inm->get_institution_data(false, $s_institution_name, true);
                    if ($mba_institution_data) {
                        $s_institution_id = $mba_institution_data[0]->institution_id;
                    }
                    else {
                        $s_institution_id = null;
                    }
                }
                
                $s_advisor_id = $this->uuid->v4();
                $a_advisor_data = [
                    'advisor_id' => $s_advisor_id,
                    'personal_data_id' => $s_personal_data_id,
                    'institution_id' => $s_institution_id
                ];

                $this->Tm->submit_advisor($a_advisor_data);

                if($this->db->trans_status() === false){
                    $this->db->trans_rollback();
                    $a_return = array('code' => 1, 'message' => 'Error processing data!');
                }
                else{
                    $this->db->trans_commit();
                    $a_return = array('code' => 0, 'message' => 'Success');
                    $this->send_notification_telegram('new advisor added: '.$s_personal_data_name.' / '.$s_institution_name);
                }
            }
            else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }

    public function submit_student_final()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_student_id = $this->input->post('thesis_student_id');
            $s_thesis_log_id = $this->input->post('thesis_log_key');
            $s_thesis_title = $this->input->post('thesis_title');

            $mba_thesis_data = $this->Tm->get_thesis_student([
                'thesis_student_id' => $s_thesis_student_id,
                // 'current_progress' => 'final'
            ]);

            $mba_thesis_final_log = $this->Tm->get_status_thesis($s_thesis_student_id, [
                'tsl.thesis_log_type' => 'final',
            ]);

            // if (!$mba_thesis_data) {
            //     // $mba_defense_data = $this->General->get_where('thesis_defense', ['thesis_students_id' => $s_thesis_student_id, 'score_grade != ' => 'F']);
            //     // if ($mba_defense_data) {
            //         $mba_thesis_data = $this->Tm->get_thesis_student([
            //             'thesis_student_id' => $s_thesis_student_id,
            //             'current_progress' => 'work'
            //         ]);
            //     // }
            // }

            if (!$mba_thesis_data) {
                $a_return = ['code' => 1, 'message' => 'Current status is invalid for this action!'];
            }
            else if (!$mba_thesis_final_log) {
                $a_return = ['code' => 1, 'message' => 'Please upload document required before submit!'];
            }
            else {
                $a_thesis_student_data = [
                    'current_progress' => 'final',
                    'current_status' => 'pending',
                ];

                $this->Tm->submit_thesis_student($a_thesis_student_data, ['thesis_student_id' => $s_thesis_student_id]);
                $this->send_notification($s_thesis_student_id, 'thesis final submission');
                $a_return = ['code' => 0, 'message' => 'Success!'];
            }

            print json_encode($a_return);
        }
    }

    public function submit_student_work()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_student_id = $this->input->post('thesis_student_id');
            $s_thesis_log_id = $this->input->post('thesis_log_key');
            $s_thesis_title = $this->input->post('thesis_title');
            $s_advisor_1_id = $this->input->post("advisor_1_update");
            $s_advisor_2_id = $this->input->post("advisor_2_update");

            if ((empty($s_advisor_1_id)) AND (empty($s_thesis_title))) {
                $a_return = ['code' => 1, 'message' => 'Thesis title and advisor 1 field is required!'];
            }
            else {
                $this->db->trans_begin();
                $a_thesis_student_data = [
                    'thesis_title' => ($this->input->post('thesis_title') == '') ? NULL : $this->input->post('thesis_title'),
                    'student_id' => $this->session->userdata('student_id'),
                    'remarks' => NULL,
                    'current_progress' => 'work',
                    'current_status' => 'pending',
                    'date_added' => date('Y-m-d H:i:s'),
                ];

                if (empty($s_thesis_student_id)) {
                    $s_thesis_student_id = $this->uuid->v4();
                    $a_thesis_student_data['thesis_student_id'] = $s_thesis_student_id;
                    $this->Tm->submit_thesis_student($a_thesis_student_data);
                }
                else {
                    $this->Tm->submit_thesis_student($a_thesis_student_data, ['thesis_student_id' => $s_thesis_student_id]);
                }
                // print('<pre>');var_dump($mbs_advisor_1);exit;
                if (!empty($s_advisor_1_id)) {
                    $student_advisor_1 = $this->Tm->get_advisor_student($s_thesis_student_id, 'approved_advisor_1', [
                        'advisor_type' => 'approved_advisor_1'
                    ]);

                    $a_student_advisor_data_1 = [
                        'thesis_student_id' => $s_thesis_student_id,
                        'advisor_id' => $s_advisor_1_id,
                        'advisor_type' => 'approved_advisor_1'
                    ];
                    if ($student_advisor_1) {
                        $this->Tm->submit_student_advisor($a_student_advisor_data_1, ['student_advisor_id' => $student_advisor_1[0]->student_advisor_id]);
                    }
                    else {
                        $a_student_advisor_data_1['student_advisor_id'] = $this->uuid->v4();
                        $this->Tm->submit_student_advisor($a_student_advisor_data_1);
                    }
                }
    
                if (!empty($s_advisor_2_id)) {
                    $student_advisor_2 = $this->Tm->get_advisor_student($s_thesis_student_id, 'approved_advisor_2', [
                        'advisor_type' => 'approved_advisor_2'
                    ]);
                    $a_student_advisor_data_2 = [
                        'thesis_student_id' => $s_thesis_student_id,
                        'advisor_id' => $s_advisor_2_id,
                        'advisor_type' => 'approved_advisor_2'
                    ];
                    if ($student_advisor_2) {
                        $this->Tm->submit_student_advisor($a_student_advisor_data_2, ['student_advisor_id' => $student_advisor_2[0]->student_advisor_id]);
                    }
                    else {
                        $a_student_advisor_data_2['student_advisor_id'] = $this->uuid->v4();
                        $this->Tm->submit_student_advisor($a_student_advisor_data_2);
                    }
                }

                if ($this->db->trans_status() === TRUE) {
                    $a_return = ['code' => 0, 'message' => 'Success!'];
                    // $this->send_notification($s_thesis_student_id, 'thesis work submission');
                    $this->db->trans_commit();
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'Error processing data!'];
                    $this->db->trans_rollback();
                }
            }

            print json_encode($a_return);
        }
    }

    public function check_tesis_data()
    {
        $mba_thesis_student_data = $this->Tm->get_thesis_student([
            'ts.thesis_student_id' => '058ab9d6-857f-43b1-8eb9-bfea4ad6c2f4'
        ]);
        print('<pre>');var_dump($mba_thesis_student_data);exit;
    }

    public function send_notification($s_thesis_student_id, $s_upload_mode = 'thesis work submission')
    {
        $mba_thesis_student_data = $this->Tm->get_thesis_student([
            'ts.thesis_student_id' => $s_thesis_student_id
        ]);

        if ($mba_thesis_student_data) {
            $thesis_data = $mba_thesis_student_data[0];
            $mba_deans_data = $this->General->get_where('dt_employee', ['personal_data_id' => $mba_thesis_student_data[0]->deans_id]);
            if ($mba_deans_data) {
                $s_deans_name = $this->Pdm->retrieve_title($thesis_data->deans_id);
                $s_text = <<<TEXT
Dear {$s_deans_name},

{$thesis_data->personal_data_name} has uploaded {$s_upload_mode}, please check the file on the portal.
TEXT;
                // 
                
                // $config = $this->config->item('mail_config');
                // $config['mailtype'] = 'html';
                // $this->email->initialize($config);
                $this->email->from('employee@company.ac.id', '[Notification] IULI System');
                // $this->email->to('employee@company.ac.id');
                $this->email->to($mba_deans_data[0]->employee_email);
                $bccEmail = array($this->config->item('email')['academic']['head'], 'employee@company.ac.id');
                $this->email->bcc($bccEmail);
                $this->email->subject(ucfirst(strtolower($s_upload_mode)));
                $this->email->message($s_text);
                $this->email->send();
                $this->email->clear(TRUE);
            }
        }
    }

    function submit_all_document_thesis() {
        if ($this->input->is_ajax_request()) {
            $student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->input->post('student_id')]);
            $list_filetype = $this->General->get_enum_values('thesis_students_file', 'thesis_filetype');
            if ($student_data) {
                $o_student = $student_data[0];
                $mba_thesis_data = $this->Tm->get_thesis_student([
                    'st.student_id' => $o_student->student_id
                ]);
                $s_thesis_path = APPPATH.'uploads/student/'.$o_student->academic_year_id.'/'.$o_student->study_program_abbreviation.'/'.$o_student->student_id.'/';
                
                if ($mba_thesis_data) {
                    $this->db->trans_begin();
                    $o_thesis_student = $mba_thesis_data[0];
                    $a_error_upload = [];
                    $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx';
                    $config['max_size'] = 204800;
                    $config['file_ext_tolower'] = true;
                    $config['overwrite'] = true;
                    $this->load->library('upload', $config);

                    foreach ($list_filetype as $s_filetype) {
                        if (!empty($_FILES[$s_filetype]['name'])) {
                            $a_type_stucture = explode('_', $s_filetype);
                            $s_thesis_type = $a_type_stucture[1];

                            // cek di portal data dan file sudah ada atau belum?
                            $mba_thesis_file = $this->Tm->get_list_thesis_file(['sf.thesis_filetype' => $s_filetype, 'ts.thesis_student_id' => $o_thesis_student->thesis_student_id]);
                            if (!$mba_thesis_file) { // kalau belum ada
                                // cek status log thesis
                                $mba_student_log_thesis = $this->General->get_where('thesis_students_log_status', [
                                    'thesis_student_id' => $o_thesis_student->thesis_student_id,
                                    'thesis_log_type' => $s_thesis_type
                                ]);
                                if (!$mba_student_log_thesis) {
                                    // insert baru kalau belum ada
                                    $s_thesis_log_id = $this->uuid->v4();
                                    $this->Tm->submit_log_status([
                                        'thesis_log_id' => $s_thesis_log_id,
                                        'thesis_student_id' => $o_thesis_student->thesis_student_id,
                                        'academic_year_id' => (intval($o_student->graduated_year_id) - 1),
                                        'semester_type_id' => '2',
                                        'thesis_status' => 'approved',
                                        'thesis_log_type' => $s_thesis_type,
                                        'date_added' => date('Y-m-d H:i:s')
                                    ]);
                                }
                                else {
                                    $s_thesis_log_id = $mba_student_log_thesis[0]->thesis_log_id;
                                }

                                // insert filenya
                                $s_file_path = $s_thesis_path.$a_type_stucture[0].'_'.$a_type_stucture[1].'/';
                                if(!file_exists($s_file_path)){
                                    mkdir($s_file_path, 0755, true);
                                }
                                $graduate_date = (!is_null($o_student->student_date_graduated)) ? $o_student->student_date_graduated : date('Y-m-d');
                                $s_fname = date('M', strtotime($graduate_date)).'_'.date('Y', strtotime($graduate_date)).$o_student->student_number.'_';
                                
                                $config['upload_path'] = $s_file_path;
                                $config['file_name'] = $s_fname.$s_filetype;
                                $this->upload->initialize($config);
                                if($this->upload->do_upload($s_filetype)) {
                                    $a_filedata = [
                                        'thesis_file_id' => $this->uuid->v4(),
                                        'thesis_log_id' => $s_thesis_log_id,
                                        'thesis_filetype' => $s_filetype,
                                        'thesis_filename' => $this->upload->data('file_name'),
                                        'date_added' => date('Y-m-d H:i:s')
                                    ];
                                    $this->Tm->submit_thesis_file($a_filedata);
                                }
                                else {
                                    array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>'));
                                }
                            }
                        }
                    }

                    if (count($a_error_upload) > 0) {
                        $a_return = ['code' => 1, 'message' => implode(';', $a_error_upload)];
                        $this->db->trans_rollback();
                    }
                    else if ($this->db->trans_status() === FALSE) {
                        $a_return = ['code' => 1, 'message' => 'Error processing data!'];
                        $this->db->trans_rollback();
                    }
                    else {
                        $a_return = ['code' => 0, 'message' => 'Success', 'thesis_log_id' => $s_thesis_log_id];
                        $this->db->trans_commit();
                    }
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'Thesis data not found'];
                }
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Student not found!'];
            }

            print json_encode($a_return);exit;
        }
    }

    public function submit_document_final_thesis()
    {
        if ($this->input->is_ajax_request()) {
            $student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->session->userdata('student_id')]);
            $s_thesis_student_id = $this->input->post('thesis_student_id');
            $mba_thesis_data = $this->Tm->get_thesis_student([
                'thesis_student_id' => $s_thesis_student_id
            ]);
            
            if (empty($_FILES['file_tf']['name'])) {
                $a_return = ['code' => 1, 'message' => 'Thesis Final File is required!'];
            }
            if (empty($_FILES['file_jp']['name'])) {
                $a_return = ['code' => 1, 'message' => 'Thesis Journal Publication File is required!'];
            }
            else if (empty($s_thesis_student_id)) {
                $a_return = ['code' => 1, 'message' => 'Error retrieve thesis data!!'];
            }
            else if ($mba_thesis_data) {
                $s_fname = date('M').'_'.date('Y').$student_data[0]->student_number.'_';
                $s_tf_fname = $s_fname.'thesis_final';
                $s_jp_fname = $s_fname.'thesis_final_journal';
                $s_tfo_fname = $s_fname.'thesis_final_other';

                $this->db->trans_begin();
                $a_thesis_student_data = [
                    'current_progress' => 'final',
                    'current_status' => 'pending'
                ];
                
                $this->Tm->submit_thesis_student($a_thesis_student_data, ['thesis_student_id' => $s_thesis_student_id]);
                $a_thesis_log_data = [
                    'thesis_log_type' => 'final',
                    'thesis_status' => 'pending',
                    'thesis_approved_hsp' => 'false',
                    'thesis_approved_deans' => 'false',
                    'date_added' => date('Y-m-d H:i:s'),
                    'academic_year_id' => $this->session->userdata('academic_year_id_active'),
                    'semester_type_id' => $this->session->userdata('semester_type_id_active')
                ];

                $mba_thesis_log_data = $this->Tm->get_status_thesis($s_thesis_student_id, [
                    'tsl.thesis_log_type' => 'final'
                ]);
                if (!$mba_thesis_log_data) {
                    $s_thesis_log_id = $this->uuid->v4();
                    $a_thesis_log_data['thesis_log_id'] = $s_thesis_log_id;
                    $a_thesis_log_data['thesis_student_id'] = $s_thesis_student_id;
                    $this->Tm->submit_log_status($a_thesis_log_data);
                }
                else {
                    $s_thesis_log_id = $mba_thesis_log_data[0]->thesis_log_id;
                }

                $s_file_path = APPPATH.'uploads/student/'.$student_data[0]->academic_year_id.'/'.$student_data[0]->study_program_abbreviation.'/'.$student_data[0]->student_id.'/thesis_final/';
                $upload_success = false;
                $a_error_upload = [];
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0755, true);
                }

                $config['allowed_types'] = 'pdf|doc|docx';
                $config['max_size'] = 204800;
                $config['file_ext_tolower'] = true;
                $config['overwrite'] = true;
                $config['upload_path'] = $s_file_path;
                $this->load->library('upload', $config);
                
                $config['file_name'] = $s_tf_fname;
                $this->upload->initialize($config);
                if($this->upload->do_upload('file_tf')) {
                    // $this->Tm->submit_log_status(['thesis_work_fname' => $this->upload->data('file_name')], ['thesis_log_id' => $s_thesis_log_id]);
                    $a_filedata = [
                        'thesis_file_id' => $this->uuid->v4(),
                        'thesis_log_id' => $s_thesis_log_id,
                        'thesis_filetype' => 'thesis_final_file',
                        'thesis_filename' => $this->upload->data('file_name'),
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Tm->submit_thesis_file($a_filedata);
                }
                else {
                    array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Thesis Final'));
                }

                $config['file_name'] = $s_jp_fname;
                $this->upload->initialize($config);
                if($this->upload->do_upload('file_jp')) {
                    // $this->Tm->submit_log_status(['thesis_work_fname' => $this->upload->data('file_name')], ['thesis_log_id' => $s_thesis_log_id]);
                    $a_filedata = [
                        'thesis_file_id' => $this->uuid->v4(),
                        'thesis_log_id' => $s_thesis_log_id,
                        'thesis_filetype' => 'thesis_final_journal_publication',
                        'thesis_filename' => $this->upload->data('file_name'),
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Tm->submit_thesis_file($a_filedata);
                }
                else {
                    array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Thesis Journal Publication'));
                }

                if (!empty($_FILES['file_tf_ot']['name'])) {
                    $config['file_name'] = $s_tfo_fname;
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('file_tf_ot')) {
                        $a_filedata = [
                            'thesis_file_id' => $this->uuid->v4(),
                            'thesis_log_id' => $s_thesis_log_id,
                            'thesis_filetype' => 'thesis_final_other_doc',
                            'thesis_filename' => $this->upload->data('file_name'),
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        $this->Tm->submit_thesis_file($a_filedata);
                    }
                    else {
                        array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Thesis Final Other Doc'));
                    }
                }

                if (count($a_error_upload) > 0) {
                    $a_return = ['code' => 1, 'message' => implode(';', $a_error_upload)];
                    $this->db->trans_rollback();
                }
                else if ($this->db->trans_status() === FALSE) {
                    $a_return = ['code' => 1, 'message' => 'Error processing data!'];
                    $this->db->trans_rollback();
                }
                else {
                    $a_return = ['code' => 0, 'message' => 'Success', 'thesis_student_id' => $s_thesis_student_id, 'thesis_log_id' => $s_thesis_log_id];
                    $this->db->trans_commit();
                }
            }
            else {
                $a_return = ['code' => 3, 'message' => 'Error retrieve thesis data!'];
            }

            print json_encode($a_return);
        }
    }

    public function submit_document_final_thesis2()
    {
        if ($this->input->is_ajax_request()) {
            $student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->session->userdata('student_id')]);
            $s_thesis_student_id = $this->input->post('thesis_student_id');
            $mba_thesis_data = $this->Tm->get_thesis_student([
                'thesis_student_id' => $s_thesis_student_id
            ]);

            if (empty($_FILES['file_tf']['name'])) {
                $a_return = ['code' => 1, 'message' => 'Thesis Final File is required!'];
            }
            else if (empty($s_thesis_student_id)) {
                $a_return = ['code' => 1, 'message' => 'Error retrieve thesis data!!'];
            }
            else if ($mba_thesis_data) {
                $o_thesis_data = $mba_thesis_data[0];

                if ($o_thesis_data->current_progress != 'final') {
                    $mba_defense_data = $this->General->get_where('thesis_defense', ['thesis_student_id' => $s_thesis_student_id, 'score_grade != ' => 'F']);
                    if ($mba_defense_data) {
                        $o_thesis_data->current_progress = 'final';
                    }
                }
                if ($o_thesis_data->current_progress == 'final') {
                    $s_fname = date('M').'_'.date('Y').$student_data[0]->student_number.'_thesis_final';
                    $s_fname_other = date('M').'_'.date('Y').$student_data[0]->student_number.'_thesis_final_other';
                    $s_file_path = APPPATH.'uploads/student/'.$student_data[0]->academic_year_id.'/'.$student_data[0]->study_program_abbreviation.'/'.$student_data[0]->student_id.'/thesis_final/';
                    if(!file_exists($s_file_path)){
                        mkdir($s_file_path, 0755, true);
                    }

                    $a_thesis_log_data = [
                        'thesis_log_type' => 'final',
                        'thesis_status' => 'pending',
                        'thesis_approved_hsp' => 'false',
                        'thesis_approved_deans' => 'false',
                        'date_added' => date('Y-m-d H:i:s'),
                    ];

                    $mba_thesis_log_data = $this->Tm->get_status_thesis($s_thesis_student_id, [
                        'tsl.thesis_log_type' => 'final'
                    ]);
                    
                    if (!$mba_thesis_log_data) {
                        $s_thesis_log_id = $this->uuid->v4();
                        $a_thesis_log_data['thesis_log_id'] = $s_thesis_log_id;
                        $a_thesis_log_data['thesis_student_id'] = $s_thesis_student_id;
                        $this->Tm->submit_log_status($a_thesis_log_data);
                    }
                    else {
                        $s_thesis_log_id = $mba_thesis_log_data[0]->thesis_log_id;
                    }

                    $a_error_upload = [];
                    $config['allowed_types'] = 'pdf|rtf';
                    $config['max_size'] = 204800;
                    $config['file_ext_tolower'] = true;
                    $config['overwrite'] = true;
                    $config['upload_path'] = $s_file_path;
                    $this->load->library('upload', $config);

                    $config['file_name'] = $s_fname;
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('file_tf')) {
                        $this->Tm->submit_log_status(['thesis_final_fname' => $this->upload->data('file_name')], ['thesis_log_id' => $s_thesis_log_id]);
                    }
                    else {
                        array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Thesis Final'));
                    }

                    if (!empty($_FILES['file_tf_ot']['name'])) {
                        $config['file_name'] = $s_fname_other;
                        $this->upload->initialize($config);
                        if($this->upload->do_upload('file_tf_ot')) {
                            $this->Tm->submit_log_status(['thesis_final_other_fname' => $this->upload->data('file_name')], ['thesis_log_id' => $s_thesis_log_id]);
                        }
                        else {
                            array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Thesis Final'));
                        }
                    }

                    if (count($a_error_upload) > 0) {
                        $a_return = ['code' => 1, 'message' => implode(';', $a_error_upload)];
                    }
                    else {
                        $a_return = ['code' => 0, 'message' => 'Success', 'thesis_student_id' => $s_thesis_student_id, 'thesis_log_id' => $s_thesis_log_id];
                    }
                }
                else {
                    $a_return = ['code' => 3, 'message' => 'Current status is invalid for this action!'];
                }
            }
            else {
                $a_return = ['code' => 3, 'message' => 'Error retrieve thesis data!'];
            }

            print json_encode($a_return);
        }
    }

    public function submit_document_thesis_proposal()
    {
        if ($this->input->is_ajax_request()) {
            $student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->session->userdata('student_id')]);
            $s_thesis_student_id = $this->input->post('thesis_student_id');
            if (empty($_FILES['file_proposal']['name'])) {
                $a_return = ['code' => 1, 'message' => 'Thesis proposal File is required!'];
            }
            else {
                $s_fname = date('M').'_'.date('Y').$student_data[0]->student_number.'_';
                $s_fname = $s_fname.'proposal';

                $this->db->trans_begin();
                $a_thesis_student_data = [
                    // 'period_id' => $s_period_id,
                    'thesis_title' => ($this->input->post('thesis_title') == '') ? NULL : $this->input->post('thesis_title'),
                    'student_id' => $this->session->userdata('student_id'),
                    'remarks' => NULL,
                    'current_progress' => 'proposal',
                    'current_status' => 'pending',
                    'date_added' => date('Y-m-d H:i:s'),
                ];
                
                if (empty($s_thesis_student_id)) {
                    $s_thesis_student_id = $this->uuid->v4();
                    $a_thesis_student_data['thesis_student_id'] = $s_thesis_student_id;
                    $this->Tm->submit_thesis_student($a_thesis_student_data);
                }
                else {
                    $this->Tm->submit_thesis_student($a_thesis_student_data, ['thesis_student_id' => $s_thesis_student_id]);
                }

                $a_thesis_log_data = [
                    'thesis_log_type' => 'proposal',
                    'thesis_status' => 'pending',
                    'thesis_approved_hsp' => 'false',
                    'thesis_approved_deans' => 'false',
                    'date_added' => date('Y-m-d H:i:s'),
                    'academic_year_id' => $this->session->userdata('academic_year_id_active'),
                    'semester_type_id' => $this->session->userdata('semester_type_id_active')
                ];

                $mba_thesis_log_data = $this->Tm->get_status_thesis($s_thesis_student_id, [
                    'tsl.thesis_log_type' => 'proposal',
                    'tsl.academic_year_id' => $this->session->userdata('academic_year_id_active'),
                    'tsl.semester_type_id' => $this->session->userdata('semester_type_id_active')
                ]);

                if (!$mba_thesis_log_data) {
                    $s_thesis_log_id = $this->uuid->v4();
                    $a_thesis_log_data['thesis_log_id'] = $s_thesis_log_id;
                    $a_thesis_log_data['thesis_student_id'] = $s_thesis_student_id;
                    $this->Tm->submit_log_status($a_thesis_log_data);
                }
                else {
                    $s_thesis_log_id = $mba_thesis_log_data[0]->thesis_log_id;
                }

                $s_file_path = APPPATH.'uploads/student/'.$student_data[0]->academic_year_id.'/'.$student_data[0]->study_program_abbreviation.'/'.$student_data[0]->student_id.'/thesis_proposal/';
                $upload_success = false;
                $a_error_upload = [];
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0755, true);
                }

                $config['allowed_types'] = 'pdf';
                $config['max_size'] = 204800;
                $config['file_ext_tolower'] = true;
                $config['overwrite'] = true;
                $config['upload_path'] = $s_file_path;
                $this->load->library('upload', $config);
                
                $config['file_name'] = $s_fname;
                $this->upload->initialize($config);
                if($this->upload->do_upload('file_proposal')) {
                    $a_filedata = [
                        'thesis_file_id' => $this->uuid->v4(),
                        'thesis_log_id' => $s_thesis_log_id,
                        'thesis_filetype' => 'thesis_proposal_file',
                        'thesis_filename' => $this->upload->data('file_name'),
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    // $this->Tm->submit_log_status(['thesis_proposal_fname' => $this->upload->data('file_name')], ['thesis_log_id' => $s_thesis_log_id]);
                    $this->Tm->submit_thesis_file($a_filedata);
                }
                else {
                    array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Thesis Work'));
                }

                if (count($a_error_upload) > 0) {
                    $a_return = ['code' => 1, 'message' => implode(';', $a_error_upload)];
                    $this->db->trans_rollback();
                }
                else if ($this->db->trans_status() === FALSE) {
                    $a_return = ['code' => 1, 'message' => 'Error processing data!'];
                    $this->db->trans_rollback();
                }
                else {
                    $a_return = ['code' => 0, 'message' => 'Success', 'thesis_student_id' => $s_thesis_student_id, 'thesis_log_id' => $s_thesis_log_id];
                    $this->db->trans_commit();
                }
            }

            print json_encode($a_return);
        }
    }

    public function update_status_thesis_work() {
        if ($this->input->is_ajax_request()) {
            $s_thesis_student_id = $this->input->post('bypass_thesis_student_id');
            $s_status = $this->input->post('bypass_status');

            $a_thesis_data = [
                'current_status' => $s_status
            ];

            $a_thesis_log_data = [
                'thesis_status' => $s_status
            ];

            $this->Tm->submit_thesis_student($a_thesis_data, [
                'thesis_student_id' => $s_thesis_student_id,
                'current_progress' => 'work'
            ]);
            $this->Tm->submit_log_status($a_thesis_log_data, [
                'thesis_student_id' => $s_thesis_student_id,
                'thesis_log_type' => 'work'
            ]);

            print json_encode(['code' => 0, 'message' => 'Success']);
        }
    }

    public function submit_document_thesis()
    {
        if ($this->input->is_ajax_request()) {
            $student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->session->userdata('student_id')]);
            $s_thesis_student_id = $this->input->post('thesis_student_id');
            if (empty($_FILES['file_tw']['name'])) {
                $a_return = ['code' => 1, 'message' => 'Thesis Work File is required!'];
            }
            else if (empty($_FILES['file_pc']['name'])) {
                $a_return = ['code' => 1, 'message' => 'Thesis Plagiarism Check File is required!'];
            }
            else if (empty($_FILES['file_tl']['name'])) {
                $a_return = ['code' => 1, 'message' => 'Thesis Thesis Log File is required!'];
            }
            else {
                $s_fname = date('M').'_'.date('Y').$student_data[0]->student_number.'_';
                $s_tw_fname = $s_fname.'thesis_work';
                $s_tl_fname = $s_fname.'thesis_log';
                $s_tpl_fname = $s_fname.'thesis_pc';
                $s_tod_fname = $s_fname.'other_doc';

                $this->db->trans_begin();
                $a_thesis_student_data = [
                    // 'period_id' => $this->get_thesis_period(),
                    'thesis_title' => ($this->input->post('thesis_title') == '') ? NULL : $this->input->post('thesis_title'),
                    'student_id' => $this->session->userdata('student_id'),
                    'remarks' => NULL,
                    'current_progress' => 'work',
                    'current_status' => 'pending',
                    'date_added' => date('Y-m-d H:i:s')
                ];
                
                if (empty($s_thesis_student_id)) {
                    $s_thesis_student_id = $this->uuid->v4();
                    $a_thesis_student_data['thesis_student_id'] = $s_thesis_student_id;
                    $this->Tm->submit_thesis_student($a_thesis_student_data);
                }
                else {
                    $this->Tm->submit_thesis_student($a_thesis_student_data, ['thesis_student_id' => $s_thesis_student_id]);
                }
                
                $a_thesis_log_data = [
                    'thesis_log_type' => 'work',
                    'thesis_status' => 'pending',
                    'thesis_approved_hsp' => 'false',
                    'thesis_approved_deans' => 'false',
                    'date_added' => date('Y-m-d H:i:s'),
                    'academic_year_id' => $this->session->userdata('academic_year_id_active'),
                    'semester_type_id' => $this->session->userdata('semester_type_id_active')
                ];

                $mba_thesis_log_data = $this->Tm->get_status_thesis($s_thesis_student_id, [
                    'tsl.thesis_log_type' => 'work',
                    'tsl.academic_year_id' => $this->session->userdata('academic_year_id_active'),
                    'tsl.semester_type_id' => $this->session->userdata('semester_type_id_active'),
                ]);
                if (!$mba_thesis_log_data) {
                    $s_thesis_log_id = $this->uuid->v4();
                    $a_thesis_log_data['thesis_log_id'] = $s_thesis_log_id;
                    $a_thesis_log_data['thesis_student_id'] = $s_thesis_student_id;
                    $this->Tm->submit_log_status($a_thesis_log_data);
                }
                else {
                    $s_thesis_log_id = $mba_thesis_log_data[0]->thesis_log_id;
                }

                $s_file_path = APPPATH.'uploads/student/'.$student_data[0]->academic_year_id.'/'.$student_data[0]->study_program_abbreviation.'/'.$student_data[0]->student_id.'/thesis_work/';
                $upload_success = false;
                $a_error_upload = [];
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0755, true);
                }

                $config['allowed_types'] = 'pdf|doc|docx';
                $config['max_size'] = 204800;
                $config['file_ext_tolower'] = true;
                $config['overwrite'] = true;
                $config['upload_path'] = $s_file_path;
                $this->load->library('upload', $config);
                
                $config['file_name'] = $s_tw_fname;
                $this->upload->initialize($config);
                if($this->upload->do_upload('file_tw')) {
                    // $this->Tm->submit_log_status(['thesis_work_fname' => $this->upload->data('file_name')], ['thesis_log_id' => $s_thesis_log_id]);
                    $a_filedata = [
                        'thesis_file_id' => $this->uuid->v4(),
                        'thesis_log_id' => $s_thesis_log_id,
                        'thesis_filetype' => 'thesis_work_file',
                        'thesis_filename' => $this->upload->data('file_name'),
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Tm->submit_thesis_file($a_filedata);
                }
                else {
                    array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Thesis Work'));
                }

                $config['file_name'] = $s_tl_fname;
                $this->upload->initialize($config);
                if($this->upload->do_upload('file_tl')) {
                    // $this->Tm->submit_log_status(['thesis_log_fname' => $this->upload->data('file_name')], ['thesis_log_id' => $s_thesis_log_id]);
                    $a_filedata = [
                        'thesis_file_id' => $this->uuid->v4(),
                        'thesis_log_id' => $s_thesis_log_id,
                        'thesis_filetype' => 'thesis_work_log',
                        'thesis_filename' => $this->upload->data('file_name'),
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Tm->submit_thesis_file($a_filedata);
                }
                else {
                    array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Thesis Log'));
                }

                $config['file_name'] = $s_tpl_fname;
                $this->upload->initialize($config);
                // if (!empty($_FILES['file_other_doc']['name'])) {
                if($this->upload->do_upload('file_pc')) {
                    // $this->Tm->submit_log_status(['thesis_plagiate_check_fname' => $this->upload->data('file_name')], ['thesis_log_id' => $s_thesis_log_id]);
                    $a_filedata = [
                        'thesis_file_id' => $this->uuid->v4(),
                        'thesis_log_id' => $s_thesis_log_id,
                        'thesis_filetype' => 'thesis_work_plagiate_check',
                        'thesis_filename' => $this->upload->data('file_name'),
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Tm->submit_thesis_file($a_filedata);
                }
                else {
                    array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Thesis Plagiarism Check'));
                }
                // }

                if (!empty($_FILES['file_other_doc']['name'])) {
                    $config['file_name'] = $s_tod_fname;
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('file_other_doc')) {
                        // $this->Tm->submit_log_status(['thesis_other_doc_fname' => $this->upload->data('file_name')], ['thesis_log_id' => $s_thesis_log_id]);
                        $a_filedata = [
                            'thesis_file_id' => $this->uuid->v4(),
                            'thesis_log_id' => $s_thesis_log_id,
                            'thesis_filetype' => 'thesis_work_other_doc',
                            'thesis_filename' => $this->upload->data('file_name'),
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        $this->Tm->submit_thesis_file($a_filedata);
                    }
                    else {
                        array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Thesis Other Doc'));
                    }
                }

                if (count($a_error_upload) > 0) {
                    $a_return = ['code' => 1, 'message' => implode(';', $a_error_upload)];
                    $this->db->trans_rollback();
                }
                else if ($this->db->trans_status() === FALSE) {
                    $a_return = ['code' => 1, 'message' => 'Error processing data!'];
                    $this->db->trans_rollback();
                }
                else {
                    $a_return = ['code' => 0, 'message' => 'Success', 'thesis_student_id' => $s_thesis_student_id, 'thesis_log_id' => $s_thesis_log_id];
                    $this->db->trans_commit();
                }
            }

            print json_encode($a_return);
        }
    }

    public function get_list_file_upload($s_thesis_log_id = false, $s_log_type = 'work')
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_log_id = $this->input->post('thesis_log_id');
            $s_log_type = $this->input->post('thesis_log_type');
        }

        $a_file_data = false;
        $mba_thesis_log_file_data = $this->Tm->get_thesis_list_files([
            'sls.thesis_log_id' => $s_thesis_log_id,
            'sls.thesis_log_type' => $s_log_type
        ]);

        // $mba_thesis_log_data = $this->General->get_where('thesis_student_log_status', [
        //     'thesis_log_id' => $s_thesis_log_id,
        //     'thesis_log_type' => $s_log_type
        // ]);
        
        if ($mba_thesis_log_file_data) {
            foreach ($mba_thesis_log_file_data as $o_filelog) {
                $s_path = '';
                switch ($s_log_type) {
                    case 'proposal':
                        $s_path = 'thesis_proposal/';
                        break;

                    case 'work':
                        $s_path = 'thesis_work/';
                        break;

                    case 'final':
                        $s_path = 'thesis_final/';
                        break;
                    
                    default:
                        break;
                }
                $o_filelog->filename = $o_filelog->thesis_filetype;
                $o_filelog->filepath = $s_path.$o_filelog->thesis_student_id.'/'.$o_filelog->thesis_filename;
            }
        }

        $a_data = ['data' => $mba_thesis_log_file_data];
        if ($this->input->is_ajax_request()) {
            print json_encode($a_data);
        }
        else {
            return $a_data;
        }
    }

    public function get_thesis_period()
    {
        $mba_period_data = $this->General->get_where('thesis_period', [
            'academic_year_id' => $this->session->userdata('academic_year_id_active'),
            'semester_type_id' => $this->session->userdata('semester_type_id_active'),
            // 'academic_year_id' => 2021,
            // 'semester_type_id' => 1,
        ]);

        return ($mba_period_data) ? $mba_period_data[0]->period_id : '9c49cf29-6b27-11ec-a24a-52540039e1c3';
    }

    public function close_tpe()
    {
        if ($this->input->is_ajax_request()) {
            $s_score_presentation_id = $this->input->post('presentation_id');
            $a_score_presentation_data = [
                'score_status' => 'closed'
            ];
            $this->Tm->submit_thesis_score_presentation($a_score_presentation_data, [
                'score_presentation_id' => $s_score_presentation_id
            ]);
            print json_encode(['code' => 0]);
        }
    
    }
    public function close_twe()
    {
        if ($this->input->is_ajax_request()) {
            $s_score_evaluation_id = $this->input->post('evaluation_id');
            $a_score_evaluation_data = [
                'score_status' => 'closed'
            ];
            $this->Tm->submit_thesis_score_evaluation($a_score_evaluation_data, [
                'score_evaluation_id' => $s_score_evaluation_id
            ]);
            print json_encode(['code' => 0]);
        }
    }

    public function submit_tpe_performance()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_defense_id = $this->input->post('thesis_defense_id');
            $s_total_score = doubleval($this->input->post("presentation_score")) + doubleval($this->input->post("argumentation_score"));
            $a_evaluation_content_data = [
                'score_presentation_id' => $this->uuid->v4(),
                'presentation_remarks' => $this->input->post("presentation_remarks"),
                'argumentation_remarks' => $this->input->post("argumentation_remarks"),
                'presentation_score' => $this->input->post("presentation_score"),
                'argumentation_score' => $this->input->post("argumentation_score"),
                'score_total' => $s_total_score,
                'date_added' => date('Y-m-d H:i:s')
            ];

            if (intval($this->input->post('presentation_score')) > 30) {
                $a_return = ['code' => 1, 'message' => 'Max score for Presentation Performance is 30'];
            }
            else if (intval($this->input->post('argumentation_score')) > 70) {
                $a_return = ['code' => 1, 'message' => 'Max score for Argumentation Performance is 70'];
            }
            else if ($s_total_score > 100) {
                $a_return = ['code' => 1, 'message' => 'Max total score is 100'];
            }
            else {
                $a_return = $this->submit_tpe_score($a_evaluation_content_data, 'thesis_score_evaluation_content', $s_thesis_defense_id, $s_total_score);
            }

            print json_encode($a_return);
        }
    }

    public function form_presentation_performance($s_thesis_defense_id)
    {
        $this->a_page_data['body'] = $this->load->view('form/tpe_performance', $this->a_page_data);
    }

    private function get_evaluation_details_data($s_target, $s_thesis_defense_id)
    {
        $mba_thesis_defense_data = $this->General->get_where('thesis_defense', ['thesis_defense_id' => $s_thesis_defense_id]);
        if ($mba_thesis_defense_data) {
            $a_param = [
                'ts.thesis_defense_id' => $s_thesis_defense_id,
            ];
    
            $mba_student_advisor = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'advisor', true);
            $mba_student_examiner = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'examiner');

            if ($mba_student_advisor) {
                $a_param['ts.students_advisor_id'] = $mba_student_advisor[0]->student_advisor_id;
            }
            else if ($mba_student_examiner) {
                $a_param['ts.students_examiner_id'] = $mba_student_examiner[0]->student_examiner_id;
            }
            else {
                return false;
            }

            $mba_thesis_evaluation_data = $this->Tm->get_thesis_evaluation($a_param);
            if ($mba_thesis_evaluation_data) {
                $mba_target_data = $this->General->get_where($s_target, [
                    'score_evaluation_id' => $mba_thesis_evaluation_data[0]->score_evaluation_id
                ]);

                return $mba_target_data;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    public function form_evaluation_format($s_thesis_defense_id)
    {
        $thesis_evaluation_desc_data = $this->get_evaluation_details_data('thesis_score_evaluation_format', $s_thesis_defense_id);
        $this->a_page_data['evaluation_format_data'] = ($thesis_evaluation_desc_data) ? $thesis_evaluation_desc_data[0] : false;
        $this->a_page_data['body'] = $this->load->view('form/twe_format', $this->a_page_data);
    }

    public function form_working_process($s_thesis_defense_id)
    {
        $thesis_evaluation_desc_data = $this->get_evaluation_details_data('thesis_score_evaluation_working_process', $s_thesis_defense_id);
        $this->a_page_data['evaluation_working_process_data'] = ($thesis_evaluation_desc_data) ? $thesis_evaluation_desc_data[0] : false;
        $this->a_page_data['body'] = $this->load->view('form/twe_working_process', $this->a_page_data);
    }

    public function form_subject($s_thesis_defense_id)
    {
        $thesis_evaluation_desc_data = $this->get_evaluation_details_data('thesis_score_evaluation_subject', $s_thesis_defense_id);
        $this->a_page_data['evaluation_subject_data'] = ($thesis_evaluation_desc_data) ? $thesis_evaluation_desc_data[0] : false;
        $this->a_page_data['body'] = $this->load->view('form/twe_subject', $this->a_page_data);
    }

    public function form_potential_user($s_thesis_defense_id)
    {
        $thesis_evaluation_desc_data = $this->get_evaluation_details_data('thesis_score_evaluation_potential_user', $s_thesis_defense_id);
        $this->a_page_data['evaluation_potential_user_data'] = ($thesis_evaluation_desc_data) ? $thesis_evaluation_desc_data[0] : false;
        $this->a_page_data['body'] = $this->load->view('form/twe_potential_user', $this->a_page_data);
    }

    public function form_content($s_thesis_defense_id)
    {
        $thesis_evaluation_desc_data = $this->get_evaluation_details_data('thesis_score_evaluation_content', $s_thesis_defense_id);
        $this->a_page_data['evaluation_content_data'] = ($thesis_evaluation_desc_data) ? $thesis_evaluation_desc_data[0] : false;
        $this->a_page_data['body'] = $this->load->view('form/twe_content', $this->a_page_data);
    }

    public function form_twe($s_thesis_defense_id)
    {
        $mba_thesis_defense_data = $this->Tm->get_thesis_defense_student([
            'td.thesis_defense_id' => $s_thesis_defense_id
        ]);

        if ($mba_thesis_defense_data) {
            $mba_student_advisor = $this->Tm->get_list_student_advisor([
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'advisor', true);

            // $mba_is_advisor = $this->Tm->is_advisor_examiner([
            //     'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
            //     'ta.personal_data_id' => $this->session->userdata('user')
            // ], 'advisor', true);
            // $mba_is_examiner = $this->Tm->is_advisor_examiner([
            //     'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
            //     'ta.personal_data_id' => $this->session->userdata('user')
            // ], 'examiner');

            if (!$mba_student_advisor) {
                redirect('thesis/thesis_defense');
            }
            else {
                $mba_thesis_defense_data = $this->Tm->get_thesis_defense_student([
                    'td.thesis_defense_id' => $s_thesis_defense_id
                ]);
                
                $mba_thesis_evaluation_data = $this->Tm->get_thesis_evaluation([
                    'ts.thesis_defense_id' => $s_thesis_defense_id
                ]);
                // print('<pre>');var_dump($mba_thesis_evaluation_data);exit;
    
                $this->a_page_data['thesis_defense'] = $mba_thesis_defense_data;
                $this->a_page_data['thesis_evaluation_data'] = ($mba_thesis_evaluation_data) ? $mba_thesis_evaluation_data[0] : false;
                $this->a_page_data['body'] = $this->load->view('twe_form', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
        }
    }

    public function form_twe_review_score($s_thesis_defense_id)
    {
        $s_html = '';
        $mba_thesis_defense_data = $this->Tm->get_thesis_defense_student([
            'td.thesis_defense_id' => $s_thesis_defense_id
        ]);

        $mba_evaluation_format = false;
        $mba_evaluation_content = false;
        $mba_evaluation_potential_user = false;
        $mba_evaluation_subject = false;
        $mba_evaluation_working_process = false;
        
        if ($mba_thesis_defense_data) {
            $a_param = [
                'ts.thesis_defense_id' => $s_thesis_defense_id
            ];
            $mba_is_advisor = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'advisor', true);
            $mba_is_examiner = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'examiner');

            if ($mba_is_advisor) {
                $a_param['ts.students_advisor_id'] = $mba_is_advisor[0]->student_advisor_id;
            }
            else if ($mba_is_examiner) {
                $a_param['ts.students_examiner_id'] = $mba_is_advisor[0]->student_examiner_id;
            }

            $mba_thesis_evaluation = $this->Tm->get_thesis_evaluation($a_param);
            if ($mba_thesis_evaluation) {
                $mba_evaluation_format = $this->General->get_where('thesis_score_evaluation_format', ['score_evaluation_id' => $mba_thesis_evaluation[0]->score_evaluation_id]);
                $mba_evaluation_content = $this->General->get_where('thesis_score_evaluation_content', ['score_evaluation_id' => $mba_thesis_evaluation[0]->score_evaluation_id]);
                $mba_evaluation_potential_user = $this->General->get_where('thesis_score_evaluation_potential_user', ['score_evaluation_id' => $mba_thesis_evaluation[0]->score_evaluation_id]);
                $mba_evaluation_subject = $this->General->get_where('thesis_score_evaluation_subject', ['score_evaluation_id' => $mba_thesis_evaluation[0]->score_evaluation_id]);
                $mba_evaluation_working_process = $this->General->get_where('thesis_score_evaluation_working_process', ['score_evaluation_id' => $mba_thesis_evaluation[0]->score_evaluation_id]);
            }
        }

        $this->a_page_data['evaluation_format'] = $mba_evaluation_format;
        $this->a_page_data['evaluation_content'] = $mba_evaluation_content;
        $this->a_page_data['evaluation_potential_user'] = $mba_evaluation_potential_user;
        $this->a_page_data['evaluation_subject'] = $mba_evaluation_subject;
        $this->a_page_data['evaluation_working_process'] = $mba_evaluation_working_process;
        $s_html = $this->load->view('form/twe_score_review', $this->a_page_data, true);

        if ($this->input->is_ajax_request()) {
            print json_encode(['html' => $s_html]);
        }
        else {
            return $s_html;
        }
    }

    public function form_tpe_review_score($s_thesis_defense_id)
    {
        $s_html = '';
        $mba_thesis_defense_data = $this->Tm->get_thesis_defense_student([
            'td.thesis_defense_id' => $s_thesis_defense_id
        ]);

        $mba_thesis_presentation = false;
        
        if ($mba_thesis_defense_data) {
            $a_param = [
                'ts.thesis_defense_id' => $s_thesis_defense_id
            ];

            $mba_is_advisor = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'advisor', true);
            $mba_is_examiner = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'examiner');

            if ($mba_is_advisor) {
                $a_param['ts.students_advisor_id'] = $mba_is_advisor[0]->student_advisor_id;
            }
            else if ($mba_is_examiner) {
                $a_param['ts.students_examiner_id'] = $mba_is_examiner[0]->student_examiner_id;
            }

            $mba_thesis_presentation = $this->Tm->get_thesis_presentation($a_param);
        }

        $this->a_page_data['thesis_presentation'] = $mba_thesis_presentation;
        $s_html = $this->load->view('form/tpe_score_review', $this->a_page_data, true);

        if ($this->input->is_ajax_request()) {
            print json_encode(['html' => $s_html]);
        }
        else {
            return $s_html;
        }
    }

    public function download_score($s_thesis_defense_id) {
        $mba_thesis_defense_data = $this->Tm->get_thesis_defense_student([
            'td.thesis_defense_id' => $s_thesis_defense_id
        ]);

        if ($mba_thesis_defense_data) {
            $a_resultfile = modules::run('download/pdf_download/generate_thesis_defense_result', $s_thesis_defense_id);
            // print('<pre>');var_dump($a_resultfile);exit;
            // $a_path_info = pathinfo($a_resultfile['filepath'].$a_resultfile['filename']);
            $mime = mime_content_type($a_resultfile['filepath'].$a_resultfile['filename']);
            header("Content-Type: ".$mime);
            readfile( $a_resultfile['filepath'].$a_resultfile['filename'] );exit;
            // header('Content-Disposition: attachment; filename='.urlencode($a_resultfile['filename']));
            // readfile( $a_resultfile['filepath'].$a_resultfile['filename'] );
            // exit;
        }
        else {
            show_404();
        }
    }

    public function form_score($s_thesis_defense_id)
    {
        $mba_thesis_defense_data = $this->Tm->get_thesis_defense_student([
            'td.thesis_defense_id' => $s_thesis_defense_id
        ]);

        if ($mba_thesis_defense_data) {
            $is_student_advisor = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'advisor', true);
            $is_student_examiner = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'examiner');

            $mba_is_deans = $this->General->get_where('ref_faculty', ['deans_id' => $this->session->userdata('user')]);
            
            if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0', '37b0f8e9-e13c-4104-adea-6c83ca1f5855'])) {
                $is_student_advisor = true;
            }
            else if ($mba_is_deans) {
                $is_student_advisor = true;
            }
            else if ($is_student_examiner) {
                $is_student_advisor = true;
            }

            if (!$is_student_advisor) {
                redirect('thesis/thesis_defense');
            }
            else {
                $mba_student_advisor = $this->Tm->get_list_student_advisor([
                    'ts.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id
                ], 'advisor', true);
                $mba_student_examiner = $this->Tm->get_list_student_advisor([
                    'ts.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id
                ], 'examiner');
                $mba_student_attendance = $this->General->get_where('thesis_defenses_absence', [
                    'thesis_defense_id' => $s_thesis_defense_id,
                    'thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id
                ]);
                
                if ($mba_student_advisor) {
                    foreach ($mba_student_advisor as $o_advisor) {
                        $mba_advisor_attendance = $this->General->get_where('thesis_defenses_absence', [
                            'thesis_defense_id' => $s_thesis_defense_id,
                            'student_advisor_id' => $o_advisor->student_advisor_id
                        ]);
                        $mba_thesis_score = $this->General->get_where('thesis_score', [
                            'thesis_defense_id' => $s_thesis_defense_id,
                            'students_advisor_id' => $o_advisor->student_advisor_id
                        ]);
                        
                        $mba_score_evaluation = false;
                        if ($mba_thesis_score) {
                            $mba_score_evaluation = $this->General->get_where('thesis_score_evaluation', [
                                'thesis_score_id' => $mba_thesis_score[0]->thesis_score_id
                            ]);
                        }

                        $mba_score_presentation = false;
                        if ($mba_thesis_score) {
                            $mba_score_presentation = $this->General->get_where('thesis_score_presentation', [
                                'thesis_score_id' => $mba_thesis_score[0]->thesis_score_id
                            ]);
                        }
                        $o_advisor->advisor_name = $this->Pdm->retrieve_title($o_advisor->personal_data_id);
                        $o_advisor->attendance = ($mba_advisor_attendance) ? $mba_advisor_attendance[0]->attendance : 'ABSENT';
                        $o_advisor->thesis_score = ($mba_thesis_score) ? $mba_thesis_score[0] : false;
                        $o_advisor->thesis_score_evaluation = ($mba_score_evaluation) ? $mba_score_evaluation[0] : false;
                        $o_advisor->thesis_score_presentation = ($mba_score_presentation) ? $mba_score_presentation[0] : false;
                        $o_advisor->number = substr($o_advisor->advisor_type, (strlen($o_advisor->advisor_type) - 1));
                    }
                }
                if ($mba_student_examiner) {
                    foreach ($mba_student_examiner as $o_examiner) {
                        $mba_attendance = $this->General->get_where('thesis_defenses_absence', [
                            'thesis_defense_id' => $s_thesis_defense_id,
                            'student_examiner_id' => $o_examiner->student_examiner_id
                        ]);
                        $mba_thesis_score = $this->General->get_where('thesis_score', [
                            'thesis_defense_id' => $s_thesis_defense_id,
                            'students_examiner_id' => $o_examiner->student_examiner_id
                        ]);
                        $mba_score_presentation = false;
                        if ($mba_thesis_score) {
                            $mba_score_presentation = $this->General->get_where('thesis_score_presentation', [
                                'thesis_score_id' => $mba_thesis_score[0]->thesis_score_id
                            ]);
                        }
                        $o_examiner->examiner_name = $this->Pdm->retrieve_title($o_examiner->personal_data_id);
                        $o_examiner->attendance = ($mba_attendance) ? $mba_attendance[0]->attendance : 'ABSENT';
                        $o_examiner->thesis_score = ($mba_thesis_score) ? $mba_thesis_score[0] : false;
                        $o_examiner->thesis_score_presentation = ($mba_score_presentation) ? $mba_score_presentation[0] : false;
                        $o_examiner->number = substr($o_examiner->examiner_type, (strlen($o_examiner->examiner_type) - 1));
                    }
                }
                $mba_thesis_defense_data[0]->attendance = ($mba_student_attendance) ? $mba_student_attendance[0]->attendance : 'ABSENT';
    
                $this->a_page_data['thesis_defense'] = $mba_thesis_defense_data;
                $this->a_page_data['student_advisor'] = $mba_student_advisor;
                $this->a_page_data['student_examiner'] = $mba_student_examiner;
                // $this->a_page_data['score_page'] = $this->load->view('form/thesis_score_data', $this->a_page_data, true);
                $this->a_page_data['body'] = $this->load->view('thesis_score_page', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
        }
    }

    public function form_tpe($s_thesis_defense_id)
    {
        $mba_thesis_defense_data = $this->Tm->get_thesis_defense_student([
            'td.thesis_defense_id' => $s_thesis_defense_id
        ]);

        if ($mba_thesis_defense_data) {
            $a_param = [
                'ts.thesis_defense_id' => $s_thesis_defense_id,
            ];

            $mba_student_advisor = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'advisor', true);
            $mba_student_examiner = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'examiner');

            if ($mba_student_advisor) {
                $a_param['ts.students_advisor_id'] = $mba_student_advisor[0]->student_advisor_id;
            }
            else if ($mba_student_examiner) {
                $a_param['ts.students_examiner_id'] = $mba_student_examiner[0]->student_examiner_id;
            }
            else {
                redirect('thesis/thesis_defense');
            }

            $mba_thesis_presentation_data = $this->Tm->get_thesis_presentation($a_param);

            $this->a_page_data['thesis_defense'] = $mba_thesis_defense_data;
            $this->a_page_data['thesis_presentation_data'] = ($mba_thesis_presentation_data) ? $mba_thesis_presentation_data[0] : false;
            $this->a_page_data['body'] = $this->load->view('tpe_form', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function submit_twe_content()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_defense_id = $this->input->post('thesis_defense_id');
            $a_evaluation_content_data = [
                'score_evaluation_content_id' => $this->uuid->v4(),
                'problem_statement' => $this->input->post("research_question"),
                'research_question' => $this->input->post("research_question"),
                'analytical_framework' => $this->input->post("analytical_framework"),
                'methods' => $this->input->post("methods"),
                'result' => $this->input->post("result"),
                'discussion' => $this->input->post("discussion"),
                'conclusion' => $this->input->post("conclusion"),
                'literature' => $this->input->post("literature"),
                'iuli_infrastructure' => $this->input->post("iuli_infrastructure"),
                'grade' => $this->input->post("grade_content")
            ];

            if ($this->input->post("grade_content") > 30) {
                $a_return = ['code' => 1, 'message' => 'Max score is 30!'];
            }
            else {
                $a_return = $this->submit_twe_score($a_evaluation_content_data, 'thesis_score_evaluation_content', $s_thesis_defense_id, $this->input->post("grade_content"));
            }
            print json_encode($a_return);
        }
    }

    public function submit_twe_format()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_defense_id = $this->input->post('thesis_defense_id');
            $a_evaluation_format_data = [
                'score_evaluation_format_id' => $this->uuid->v4(),
                'text_style' => $this->input->post("text_style"),
                'summary' => $this->input->post("summary"),
                'chapter_structur' => $this->input->post("chapter_structur"),
                'citations' => $this->input->post("citations"),
                'table_figure' => $this->input->post("table_figure"),
                'layout' => $this->input->post("layout"),
                'reference' => $this->input->post("reference"),
                'grade' => $this->input->post("grade_format")
            ];
            
            if ($this->input->post("grade_format") > 20) {
                $a_return = ['code' => 1, 'message' => 'Max score is 20!'];
            }
            else {
                $a_return = $this->submit_twe_score($a_evaluation_format_data, 'thesis_score_evaluation_format', $s_thesis_defense_id, $this->input->post("grade_format"));
            }
            print json_encode($a_return);
        }
    }

    public function submit_twe_potential_user()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_defense_id = $this->input->post('thesis_defense_id');
            $a_evaluation_potential_user_data = [
                'score_evaluation_potential_user_id' => $this->uuid->v4(),
                'applicable_for_user' => $this->input->post("applicable_for_user"),
                'benefit_for_user' => $this->input->post("benefit_for_user"),
                'will_employ_student' => $this->input->post("will_employ_student"),
                'grade' => $this->input->post("grade_potential_user")
            ];

            if ($this->input->post("grade_potential_user") > 30) {
                $a_return = ['code' => 1, 'message' => 'Max score is 30!'];
            }
            else {
                $a_return = $this->submit_twe_score($a_evaluation_potential_user_data, 'thesis_score_evaluation_potential_user', $s_thesis_defense_id, $this->input->post("grade_potential_user"));
            }
            print json_encode($a_return);
        }
    }

    public function submit_twe_subject()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_defense_id = $this->input->post('thesis_defense_id');
            $a_evaluation_subject_data = [
                'score_evaluation_subject_id' => $this->uuid->v4(),
                'identification_objective' => $this->input->post("identification_objective"),
                'understanding_specific_topic' => $this->input->post("understanding_specific_topic"),
                'method_project_plan' => $this->input->post("method_project_plan"),
                'thesis_dificulty' => $this->input->post("thesis_dificulty"),
                'similar_thesis' => $this->input->post("similar_thesis"),
                'grade' => $this->input->post("grade_subject")
            ];

            if ($this->input->post("grade_subject") > 10) {
                $a_return = ['code' => 1, 'message' => 'Max score is 10!'];
            }
            else {
                $a_return = $this->submit_twe_score($a_evaluation_subject_data, 'thesis_score_evaluation_subject', $s_thesis_defense_id, $this->input->post("grade_subject"));
            }
            print json_encode($a_return);
        }
    }
    
    public function submit_twe_working_process()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_defense_id = $this->input->post('thesis_defense_id');
            $a_evaluation_working_process_data = [
                'score_evaluation_working_process_id' => $this->uuid->v4(),
                'identification_problem' => $this->input->post("identification_problem"),
                'independence' => $this->input->post("independence"),
                'progress' => $this->input->post("progress"),
                'grade' => $this->input->post("grade_working_process")
            ];

            if ($this->input->post("grade_working_process") > 10) {
                $a_return = ['code' => 1, 'message' => 'Max score is 10!'];
            }
            else {
                $a_return = $this->submit_twe_score($a_evaluation_working_process_data, 'thesis_score_evaluation_working_process', $s_thesis_defense_id, $this->input->post("grade_working_process"));
            }
            print json_encode($a_return);
        }
    }

    public function submit_twe_score($a_data, $s_target, $s_thesis_defense_id, $d_final_score)
    {
        $mba_thesis_student = $this->Tm->get_thesis_defense_student([
            'td.thesis_defense_id' => $s_thesis_defense_id
        ]);

        if (!$mba_thesis_student) {
            $a_return = ['code' => 1, 'message' => 'Error retireve thesis student!'];
        }
        else {
            $mba_student_advisor = $this->Tm->get_list_student_advisor([
                'ts.thesis_student_id' => $mba_thesis_student[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'advisor', true, false);

            $s_user_type = 'guest';
            $b_user_having_access = false;
            if (!$mba_student_advisor) {
                // if (!$mba_student_examiner) {
                    $a_return = ['code' => 2, 'message' => 'Sorry, you dont have access for submiting score!'];
                // }
                // else {
                //     $s_user_type = 'examiner';
                //     $o_student_examiner = $mba_student_examiner[0];
                //     $b_user_having_access = true;
                // }
            }
            else {
                $s_user_type = 'advisor';
                $o_student_advisor = $mba_student_advisor[0];
                $b_user_having_access = true;
            }

            if ($b_user_having_access) {
                $this->db->trans_begin();
                $mba_score_evaluation = $this->Tm->get_thesis_evaluation([
                    'ts.thesis_defense_id' => $s_thesis_defense_id,
                    'ts.students_advisor_id' => $mba_student_advisor[0]->student_advisor_id,
                ]);
        
                if ($mba_score_evaluation) {
                    $o_score_eval = $mba_score_evaluation[0];
                    $s_thesis_evaluation_id = $o_score_eval->score_evaluation_id;
                    $s_thesis_score_id = $o_score_eval->thesis_score_id;

                    $this->Tm->force_remove_data($s_target, [
                        'score_evaluation_id' => $o_score_eval->score_evaluation_id
                    ]);
                    $a_data['score_evaluation_id'] = $o_score_eval->score_evaluation_id;

                    $this->Tm->force_insert_data($s_target, $a_data);
                }
                else {
                    $mba_thesis_score_data = $this->General->get_where('thesis_score', [
                        'thesis_defense_id' => $s_thesis_defense_id,
                        'students_advisor_id' => $mba_student_advisor[0]->student_advisor_id,
                    ]);
        
                    if ($mba_thesis_score_data) {
                        $o_thesis_score = $mba_thesis_score_data[0];
                        $s_thesis_evaluation_id = $this->uuid->v4();
                        $s_thesis_score_id = $o_thesis_score->thesis_score_id;

                        $mba_thesis_score_evaluation_data = [
                            'score_evaluation_id' => $s_thesis_evaluation_id,
                            'thesis_score_id' => $s_thesis_score_id,
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        $this->Tm->submit_thesis_score_evaluation($mba_thesis_score_evaluation_data);
                    }
                    else {
                        $s_thesis_score_id = $this->uuid->v4();
                        $s_thesis_evaluation_id = $this->uuid->v4();
        
                        $mba_thesis_score_data = [
                            'thesis_score_id' => $s_thesis_score_id,
                            'thesis_defense_id' => $s_thesis_defense_id,
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        if ($mba_student_advisor) {
                            $mba_thesis_score_data['students_advisor_id'] = $mba_student_advisor[0]->student_advisor_id;
                        }
                        else {
                            $mba_thesis_score_data['students_examiner_id'] = $mba_student_examiner[0]->student_examiner_id;
                        }
                        $this->Tm->submit_thesis_score($mba_thesis_score_data);

                        $mba_thesis_score_evaluation_data = [
                            'score_evaluation_id' => $s_thesis_evaluation_id,
                            'thesis_score_id' => $s_thesis_score_id,
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        $this->Tm->submit_thesis_score_evaluation($mba_thesis_score_evaluation_data);
                    }

                    $a_data['score_evaluation_id'] = $s_thesis_evaluation_id;
                    $this->Tm->force_insert_data($s_target, $a_data);
                }

                $s_field = '';
                switch ($s_target) {
                    case 'thesis_score_evaluation_content':
                        $s_field = 'score_academic';
                        break;

                    case 'thesis_score_evaluation_format':
                        $s_field = 'score_evaluation_format';
                        break;

                    case 'thesis_score_evaluation_potential_user':
                        $s_field = 'score_user';
                        break;

                    case 'thesis_score_evaluation_subject':
                        $s_field = 'score_subject';
                        break;

                    case 'thesis_score_evaluation_working_process':
                        $s_field = 'score_working_process';
                        break;
                    
                    default:
                        break;
                }

                if ($s_field != '') {
                    $a_thesis_score_evaluation_update_data = [
                        $s_field => $d_final_score
                    ];
                    $this->Tm->submit_thesis_score_evaluation($a_thesis_score_evaluation_update_data, [
                        'score_evaluation_id' => $s_thesis_evaluation_id
                    ]);
                }
                
                $mba_thesis_evaluation_new_data = $this->General->get_where('thesis_score_evaluation', [
                    'score_evaluation_id' => $s_thesis_evaluation_id
                ]);
                $d_total_score_evaluation = 0;
                if ($mba_thesis_evaluation_new_data) {
                    $d_score_evaluation_format = (is_null($mba_thesis_evaluation_new_data[0]->score_evaluation_format)) ? 0 : $mba_thesis_evaluation_new_data[0]->score_evaluation_format;
                    $d_score_working_process = (is_null($mba_thesis_evaluation_new_data[0]->score_working_process)) ? 0 : $mba_thesis_evaluation_new_data[0]->score_working_process;
                    $d_score_subject = (is_null($mba_thesis_evaluation_new_data[0]->score_subject)) ? 0 : $mba_thesis_evaluation_new_data[0]->score_subject;
                    $d_score_user = (is_null($mba_thesis_evaluation_new_data[0]->score_user)) ? 0 : $mba_thesis_evaluation_new_data[0]->score_user;
                    $d_score_academic = (is_null($mba_thesis_evaluation_new_data[0]->score_academic)) ? 0 : $mba_thesis_evaluation_new_data[0]->score_academic;

                    $d_total_score_evaluation = $d_score_evaluation_format + $d_score_working_process + $d_score_subject + $d_score_user + $d_score_academic;
                    $this->Tm->submit_thesis_score_evaluation([
                        'score_total' => $d_total_score_evaluation
                    ], [
                        'score_evaluation_id' => $s_thesis_evaluation_id
                    ]);
                }

                $a_update_score_data = [
                    'score_evaluation' => $d_total_score_evaluation
                ];
                $this->Tm->submit_thesis_score($a_update_score_data, [
                    'thesis_score_id' => $s_thesis_score_id
                ]);

                $mba_thesis_score_list = $this->General->get_where('thesis_score', [
                    'thesis_defense_id' => $s_thesis_defense_id
                ]);
                $a_presentation_score = [];
                $a_evaluation_score = [];
                if ($mba_thesis_score_list) {
                    foreach ($mba_thesis_score_list as $o_score) {
                        if (!is_null($o_score->score_presentation)) {
                            array_push($a_presentation_score, $o_score->score_presentation);
                        }
                        if (!is_null($o_score->score_evaluation)) {
                            array_push($a_evaluation_score, $o_score->score_evaluation);
                        }
                    }
                }

                if ($this->session->userdata('user') == '10531d2c-2534-4a0c-a3c3-d442588fe4f7') {
                    // print('<pre>');var_dump($s_thesis_defense_id);exit;
                }

                $d_average_presentation_score = (count($a_presentation_score) > 0) ? (array_sum($a_presentation_score) / count($a_presentation_score)) : 0;
                $d_average_evaluation_score = (count($a_evaluation_score) > 0) ? (array_sum($a_evaluation_score) / count($a_evaluation_score)) : 0;
                $d_final_score = $this->grades->thesis_final_score($d_average_evaluation_score, $d_average_presentation_score);

                $a_new_thesis_defense_data = [
                    'score_evaluation_average' => $d_average_evaluation_score,
                    'score_presentation_average' => $d_average_presentation_score,
                    'score_final' => $d_final_score,
                    'score_grade' => $this->grades->get_grade($d_final_score)
                ];
                $this->Tm->update_thesis_defense($a_new_thesis_defense_data, $s_thesis_defense_id);

                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $a_return = ['code' => 0, 'message' => 'Success'];
                }
                else {
                    $this->db->trans_rollback();
                    $a_return = ['code' => 1, 'message' => 'Error processing score!'];
                }
            }else {
                $a_return = ['code' => 2, 'message' => 'Sorry, you dont have access for submiting score'];
            }
        }

        return $a_return;
    }

    public function submit_tpe_score($a_data, $s_target, $s_thesis_defense_id, $d_final_score)
    {
        $mba_thesis_student = $this->Tm->get_thesis_defense_student([
            'td.thesis_defense_id' => $s_thesis_defense_id
        ]);

        if (!$mba_thesis_student) {
            $a_return = ['code' => 1, 'message' => 'Error retireve thesis student!'];
        }
        else {
            $mba_student_advisor = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $mba_thesis_student[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'advisor', true);
            $mba_student_examiner = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $mba_thesis_student[0]->thesis_students_id,
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'examiner');

            $s_user_type = 'guest';
            $b_user_having_access = false;
            if (!$mba_student_advisor) {
                if (!$mba_student_examiner) {
                    $a_return = ['code' => 2, 'message' => 'Sorry, you dont have access for submiting score!'];
                }
                else {
                    $s_user_type = 'examiner';
                    $b_user_having_access = true;
                }
            }
            else {
                $s_user_type = 'advisor';
                $b_user_having_access = true;
            }

            if ($b_user_having_access) {
                $this->db->trans_begin();
                $a_param_score = [
                    'ts.thesis_defense_id' => $s_thesis_defense_id
                ];

                if ($s_user_type == 'examiner') {
                    $a_param_score['ts.students_examiner_id'] = $mba_student_examiner[0]->student_examiner_id;
                }
                else if ($s_user_type == 'advisor') {
                    $a_param_score['ts.students_advisor_id'] = $mba_student_advisor[0]->student_advisor_id;
                }
                $mba_score_presentation = $this->Tm->get_thesis_presentation($a_param_score);
        
                if ($mba_score_presentation) {
                    unset($a_data['score_presentation_id']);
                    unset($a_data['date_added']);
                    $s_thesis_score_id = $mba_score_presentation[0]->thesis_score_id;
                    $this->Tm->submit_thesis_score_presentation($a_data, [
                        'thesis_score_id' => $s_thesis_score_id
                    ]);
                }
                else {
                    $a_param_score_ = [
                        'thesis_defense_id' => $s_thesis_defense_id
                    ];
    
                    if ($s_user_type == 'examiner') {
                        $a_param_score_['students_examiner_id'] = $mba_student_examiner[0]->student_examiner_id;
                    }
                    else if ($s_user_type == 'advisor') {
                        $a_param_score_['students_advisor_id'] = $mba_student_advisor[0]->student_advisor_id;
                    }
                    $mba_thesis_score_data = $this->General->get_where('thesis_score', $a_param_score_);
        
                    if ($mba_thesis_score_data) {
                        $s_thesis_score_id = $mba_thesis_score_data[0]->thesis_score_id;
                    }
                    else {
                        $s_thesis_score_id = $this->uuid->v4();
                        $mba_thesis_score_data = [
                            'thesis_score_id' => $s_thesis_score_id,
                            'thesis_defense_id' => $s_thesis_defense_id,
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        if ($mba_student_advisor) {
                            $mba_thesis_score_data['students_advisor_id'] = $mba_student_advisor[0]->student_advisor_id;
                        }
                        else {
                            $mba_thesis_score_data['students_examiner_id'] = $mba_student_examiner[0]->student_examiner_id;
                        }
                        $this->Tm->submit_thesis_score($mba_thesis_score_data);
                    }

                    $a_data['thesis_score_id'] = $s_thesis_score_id;
                    $this->Tm->submit_thesis_score_presentation($a_data);
                }

                $a_update_score_data = [
                    'score_presentation' => $d_final_score
                ];
                $this->Tm->submit_thesis_score($a_update_score_data, [
                    'thesis_score_id' => $s_thesis_score_id
                ]);

                $mba_thesis_score_list = $this->General->get_where('thesis_score', [
                    'thesis_defense_id' => $s_thesis_defense_id
                ]);
                $a_presentation_score = [];
                $a_evaluation_score = [];
                if ($mba_thesis_score_list) {
                    foreach ($mba_thesis_score_list as $o_score) {
                        if (!is_null($o_score->score_presentation)) {
                            array_push($a_presentation_score, $o_score->score_presentation);
                        }
                        if (!is_null($o_score->score_evaluation)) {
                            array_push($a_evaluation_score, $o_score->score_evaluation);
                        }
                    }
                }

                $d_average_presentation_score = (count($a_presentation_score) > 0) ? (array_sum($a_presentation_score) / count($a_presentation_score)) : 0;
                $d_average_evaluation_score = (count($a_evaluation_score) > 0) ? (array_sum($a_evaluation_score) / count($a_evaluation_score)) : 0;
                $d_final_score = $this->grades->thesis_final_score($d_average_evaluation_score, $d_average_presentation_score);

                $a_new_thesis_defense_data = [
                    'score_evaluation_average' => $d_average_evaluation_score,
                    'score_presentation_average' => $d_average_presentation_score,
                    'score_final' => $d_final_score,
                    'score_grade' => $this->grades->get_grade($d_final_score)
                ];
                $this->Tm->update_thesis_defense($a_new_thesis_defense_data, $s_thesis_defense_id);

                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $a_return = ['code' => 0, 'message' => 'Success'];
                }
                else {
                    $this->db->trans_rollback();
                    $a_return = ['code' => 1, 'message' => 'Error processing score!'];
                }
            }else {
                $a_return = ['code' => 2, 'message' => 'Sorry, you dont have access for submiting score'];
            }
        }

        return $a_return;
    }

    public function submit_twe_scor2e()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_defense_id = $this->input->post('thesis_defense_id');
            $mba_score_evaluation = $this->Tm->get_thesis_evaluation([
                'ts.thesis_defense_id' => $s_thesis_defense_id
            ]);
            if ($mba_score_evaluation) {
                $o_score_eval = $mba_score_evaluation[0];
                $a_evaluation_format_data['score_evaluation_id'] = $o_score_eval->score_evaluation_id;
                $a_evaluation_content_data['score_evaluation_id'] = $o_score_eval->score_evaluation_id;
                $a_evaluation_potential_user_data['score_evaluation_id'] = $o_score_eval->score_evaluation_id;
                $a_evaluation_subject_data['score_evaluation_id'] = $o_score_eval->score_evaluation_id;
                $a_evaluation_working_process_data['score_evaluation_id'] = $o_score_eval->score_evaluation_id;
            }
            else {
                $mba_thesis_score_data = $this->General->get_where('thesis_score', [
                    'thesis_defense_id' => $s_thesis_defense_id
                ]);
                if ($mba_thesis_score_data) {
                    $mba_thesis_score_evaluation_data = [];
                }
                else {
                    $mba_thesis_score_data = [];
                    $mba_thesis_score_evaluation_data = [];
                }

                $a_evaluation_format_data['score_evaluation_id'] = $s_score_evaluation_id;
                $a_evaluation_content_data['score_evaluation_id'] = $s_score_evaluation_id;
                $a_evaluation_potential_user_data['score_evaluation_id'] = $s_score_evaluation_id;
                $a_evaluation_subject_data['score_evaluation_id'] = $s_score_evaluation_id;
                $a_evaluation_working_process_data['score_evaluation_id'] = $s_score_evaluation_id;

                $a_evaluation_format_data['score_evaluation_format_id'] = $this->uuid->v4();
                $a_evaluation_content_data['score_evaluation_content_id'] = $this->uuid->v4();
                $a_evaluation_potential_user_data['score_evaluation_potential_user_id'] = $this->uuid->v4();
                $a_evaluation_subject_data['score_evaluation_subject_id'] = $this->uuid->v4();
                $a_evaluation_working_process_data['score_evaluation_working_process_id'] = $this->uuid->v4();
            }
        }
    }

    public function submit_attendance()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_defense_id = $this->input->post('thesis_defense_id');
            $s_attendance_student = $this->input->post('attendance_student');
            $s_attendance_student_remarks = $this->input->post('attendance_student_remarks');

            $a_student_advisor_id = $this->input->post('student_advisor_id');
            $a_attendance_advisor = $this->input->post('attendance_advisor');
            $a_student_examiner_id = $this->input->post('student_examiner_id');
            $a_attendance_examiner = $this->input->post('attendance_examiner');

            if (empty($s_thesis_defense_id)) {
                $a_return = ['code' => 1, 'message' => 'Error retrieve data!'];
            }
            else {
                $this->db->trans_begin();
                $mba_thesis_defense_student = $this->General->get_where('thesis_defense', ['thesis_defense_id' => $s_thesis_defense_id]);
                if (!$mba_thesis_defense_student) {
                    $a_return = ['code' => 2, 'message' => 'Error retrieve thesis defense data!'];
                }
                else {
                    $this->Tm->delete_thesis_defense_attendance($s_thesis_defense_id);
                    $a_attendance_student_data = [
                        'thesis_absence_id' => $this->uuid->v4(),
                        'thesis_defense_id' => $s_thesis_defense_id,
                        'thesis_student_id' => $mba_thesis_defense_student[0]->thesis_students_id,
                        'attendance' => $s_attendance_student,
                        'attendance_remarks' => (empty($s_attendance_student_remarks)) ? NULL : $s_attendance_student_remarks,
                        'absence_by' => $this->session->userdata('user'),
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Tm->submit_thesis_defense_attendance($a_attendance_student_data);
                    
                    if ((is_array($a_student_advisor_id)) AND (count($a_student_advisor_id) > 0)) {
                        foreach ($a_student_advisor_id as $key => $s_student_advisor_id) {
                            $a_attendance_advisor_data = [
                                'thesis_absence_id' => $this->uuid->v4(),
                                'thesis_defense_id' => $s_thesis_defense_id,
                                'student_advisor_id' => $s_student_advisor_id,
                                'attendance' => $a_attendance_advisor[$key],
                                'absence_by' => $this->session->userdata('user'),
                                'date_added' => date('Y-m-d H:i:s')
                            ];
    
                            $this->Tm->submit_thesis_defense_attendance($a_attendance_advisor_data);
                        }
                    }
    
                    if ((is_array($a_student_examiner_id)) AND (count($a_student_examiner_id) > 0)) {
                        foreach ($a_student_examiner_id as $key => $s_student_examiner) {
                            $a_attendance_examiner_data = [
                                'thesis_absence_id' => $this->uuid->v4(),
                                'thesis_defense_id' => $s_thesis_defense_id,
                                'student_examiner_id' => $s_student_examiner,
                                'attendance' => $a_attendance_examiner[$key],
                                'absence_by' => $this->session->userdata('user'),
                                'date_added' => date('Y-m-d H:i:s')
                            ];
    
                            $this->Tm->submit_thesis_defense_attendance($a_attendance_examiner_data);
                        }
                    }
    
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $a_return = ['code' => 1, 'message' => 'Error submiting your data!'];
                    }
                    else {
                        $this->db->trans_commit();
                        $a_return = ['code' => 0, 'message' => 'Success!'];
                    }
                }
            }

            print json_encode($a_return);
        }
    }

    public function form_attendance($s_thesis_defense_id)
    {
        $mba_thesis_defense_data = $this->Tm->get_thesis_defense_student([
            'td.thesis_defense_id' => $s_thesis_defense_id
        ]);

        if ($mba_thesis_defense_data) {
            $mba_check_attendance_student = $this->General->get_where('thesis_defenses_absence', [
                'thesis_defense_id' => $s_thesis_defense_id,
                'thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id
            ]);
            $mba_thesis_advisor_approved = $this->Tm->get_list_student_advisor([
                'ts.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id
            ], 'advisor', true, false);
            $mba_thesis_examiner_approved = $this->Tm->get_list_student_advisor([
                'ts.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id
            ], 'examiner');

            $a_advisor_list = [];
            $a_advisor_data = [];
            if ($mba_thesis_advisor_approved) {
                foreach ($mba_thesis_advisor_approved as $o_advisor) {
                    $mba_check_attendance = $this->General->get_where('thesis_defenses_absence', [
                        'thesis_defense_id' => $s_thesis_defense_id,
                        'student_advisor_id' => $o_advisor->student_advisor_id
                    ]);
                    $s_advisor = $this->Pdm->retrieve_title($o_advisor->personal_data_id);
                    $o_advisor->advisor_name = $s_advisor;
                    if (!in_array($s_advisor, $a_advisor_list)) {
                        $a_advisor_ = [
                            'student_advisor_id' => $o_advisor->student_advisor_id,
                            'number' => substr($o_advisor->advisor_type, (strlen($o_advisor->advisor_type) - 1)),
                            'personal_data_title_suffix' => $o_advisor->personal_data_title_suffix,
                            'personal_data_title_prefix' => $o_advisor->personal_data_title_prefix,
                            'personal_data_name' => $o_advisor->personal_data_name,
                            'advisor_id' => $o_advisor->advisor_id,
                            'advisor_type' => $o_advisor->advisor_type,
                            'personal_data_id' => $o_advisor->personal_data_id,
                            'advisor_name' => $s_advisor,
                            'institution_name' => $o_advisor->institution_name,
                            'attendance' => ($mba_check_attendance) ? $mba_check_attendance[0]->attendance : false
                        ];
                        array_push($a_advisor_list, $s_advisor);
                        array_push($a_advisor_data, $a_advisor_);
                    }
                }
            }
            $a_examiner_list = [];
            $a_examiner_data = [];
            if ($mba_thesis_examiner_approved) {
                foreach ($mba_thesis_examiner_approved as $o_examiner) {
                    $mba_check_attendance = $this->General->get_where('thesis_defense_absence', [
                        'thesis_defense_id' => $s_thesis_defense_id,
                        'student_examiner_id' => $o_examiner->student_examiner_id
                    ]);
                    $s_examiner = $this->Pdm->retrieve_title($o_examiner->personal_data_id);
                    $o_examiner->examiner_name = $s_examiner;
                    if (!in_array($s_examiner, $a_examiner_list)) {
                        $a_examiner_ = [
                            'student_examiner_id' => $o_examiner->student_examiner_id,
                            'number' => substr($o_examiner->examiner_type, (strlen($o_examiner->examiner_type) - 1)),
                            'personal_data_title_suffix' => $o_examiner->personal_data_title_suffix,
                            'personal_data_title_prefix' => $o_examiner->personal_data_title_prefix,
                            'personal_data_name' => $o_examiner->personal_data_name,
                            'advisor_id' => $o_examiner->advisor_id,
                            'examiner_type' => $o_examiner->examiner_type,
                            'personal_data_id' => $o_examiner->personal_data_id,
                            'examiner_name' => $s_examiner,
                            'institution_name' => $o_examiner->institution_name,
                            'attendance' => ($mba_check_attendance) ? $mba_check_attendance[0]->attendance : false
                        ];
                        array_push($a_examiner_data, $a_examiner_);
                        array_push($a_examiner_list, $s_examiner);
                    }
                }
            }

            $mba_thesis_defense_data[0]->advisor_list = $a_advisor_data;
            $mba_thesis_defense_data[0]->examiner_list = $a_examiner_data;
            $mba_thesis_defense_data[0]->attendance = ($mba_check_attendance_student) ? $mba_check_attendance_student[0]->attendance : false;
            $mba_thesis_defense_data[0]->attendance_remarks = ($mba_check_attendance_student) ? $mba_check_attendance_student[0]->attendance_remarks : false;
        }
        // print('<pre>');var_dump($mba_thesis_defense_data);exit;
        $this->a_page_data['thesis_defense'] = $mba_thesis_defense_data;
        $this->a_page_data['body'] = $this->load->view('thesis_defense_attendance', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function get_student_available_defense()
    {
        if ($this->input->is_ajax_request()) {
            $mba_student_thesis_data = $this->Tm->get_thesis_student(['ts.current_progress' => 'work']);
            if ($mba_student_thesis_data) {
                foreach ($mba_student_thesis_data as $key => $o_student_defense) {
                    // if (in_array($o_student_defense->current_status, ['approved', 'approved_hsp'])) {
                        $mba_advisor = $this->Tm->get_list_student_advisor([
                            'ts.thesis_student_id' => $o_student_defense->thesis_student_id
                        ], 'advisor', true);
                        $a_advisor = [];
                        if ($mba_advisor) {
                            foreach ($mba_advisor as $o_advisor) {
                                $s_advisor_name = $this->General->retrieve_title($o_advisor->personal_data_id);
                                if (!in_array($s_advisor_name, $a_advisor)) {
                                    array_push($a_advisor, $s_advisor_name);
                                }
                            }
                        }
                        $o_student_defense->advisor = (count($a_advisor) > 0) ? implode('/', $a_advisor) : '';
                    // }
                    // else {
                    //     unset($mba_student_thesis_data[$key]);
                    // }
                }

                $mba_student_thesis_data = array_values($mba_student_thesis_data);
            }

            print json_encode(['data' => $mba_student_thesis_data]);
        }
    }

    public function thesis_defense_check()
    {
        $s_academic_year_id = 2022;
        $s_semester_type_id = 1;

        $mba_thesis_student_list = $this->Tm->get_thesis_defense_student([
            'td.academic_year_id' => $s_academic_year_id,
            'td.semester_type_id' => $s_semester_type_id
        ]);

        if ($mba_thesis_student_list) {
            foreach ($mba_thesis_student_list as $key => $o_thesis_student) {
                $mba_is_deans = $this->General->get_where('ref_faculty', [
                    'deans_id' => $this->session->userdata('user'),
                    'faculty_id' => $o_thesis_student->faculty_id
                ]);

                $o_thesis_student->defense_date = date('d F Y', strtotime($o_thesis_student->thesis_defense_date));
                $o_thesis_student->thesis_defense_time = date('H:i', strtotime($o_thesis_student->thesis_defense_time_start)).' - '.date('H:i', strtotime($o_thesis_student->thesis_defense_time_end));
                $o_thesis_student->user_type = $this->get_user_type($o_thesis_student->thesis_students_id);

                if (!$this->is_student_children($o_thesis_student->thesis_student_id)) {
                    if (!in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0','37b0f8e9-e13c-4104-adea-6c83ca1f5855'])) {
                        unset($mba_thesis_student_list[$key]);
                    }
                    else {
                        $o_thesis_student->user_type = 'advisor';
                    }
                }

                $mba_thesis_status_log = $this->Tm->get_thesis_log($o_thesis_student->thesis_student_id, [
                    'thesis_log_type' => 'work',
                    'thesis_status' => 'approved'
                ]);

                if ($o_thesis_student->personal_data_name == 'DANIEL ROMPAS') {
                    print('<pre>');var_dump($mba_thesis_status_log);exit;
                }

                $mba_thesis_final_status_log = $this->Tm->get_thesis_log($o_thesis_student->thesis_student_id, [
                    'thesis_log_type' => 'final',
                    // 'thesis_status' => 'approved'
                ]);

                $mba_log_files = $this->Tm->get_thesis_list_files([
                    'sls.thesis_log_id' => ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_log_id : 'x'
                ]);
                if ($mba_log_files) {
                    foreach ($mba_log_files as $o_files) {
                        $o_files->filename_button = str_replace('thesis_', '', $o_files->thesis_filetype);
                    }
                }

                $o_thesis_student->thesis_log_work_files = $mba_log_files;
                $o_thesis_student->thesis_log_id = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_log_id : false;
                // $o_thesis_student->thesis_work_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_work_fname : false;
                // $o_thesis_student->thesis_plagiate_check_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_plagiate_check_fname : false;
                // $o_thesis_student->thesis_log_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_log_fname : false;
                // $o_thesis_student->thesis_proposal_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_proposal_fname : false;
                // $o_thesis_student->thesis_final_fname = ($mba_thesis_final_status_log) ? $mba_thesis_final_status_log[0]->thesis_final_fname : false;
                // $o_thesis_student->thesis_other_doc_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_other_doc_fname : false;
                // $o_thesis_student->thesis_final_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_other_doc_fname : false;
            }
            $mba_thesis_student_list = array_values($mba_thesis_student_list);
        }
        print json_encode(['data' => $mba_thesis_student_list]);
    }

    public function cekcek()
    {
        $mba_thesis_student_list = $this->Tm->get_thesis_defense_student([
            'td.academic_year_id' => 2022,
            'td.semester_type_id' => 1,
            '058ab9d6-857f-43b1-8eb9-bfea4ad6c2f4'
        ]);
        print('<pre>');var_dump($mba_thesis_student_list);exit;
    }

    public function get_thesis_defense()
    {
        if ($this->input->is_ajax_request()) {
            // $s_academic_year_id = $this->session->userdata('academic_year_id_active');
            // $s_semester_type_id = $this->session->userdata('semester_type_id_active');
            $s_academic_year_id = 2023;
            $s_semester_type_id = 2;
            
            $s_academic_year_id = (empty($this->input->post('academic_year_defense'))) ? $s_academic_year_id : $this->input->post('academic_year_defense');
            $s_semester_type_id = (empty($this->input->post('semester_type_defense'))) ? $s_semester_type_id : $this->input->post('semester_type_defense');

            $mba_thesis_student_list = $this->Tm->get_thesis_defense_student([
                'td.academic_year_id' => $s_academic_year_id,
                'td.semester_type_id' => $s_semester_type_id
            ]);

            if ($this->session->userdata('user') == '41261c5c-94c7-4c5e-b4f9-4117f4567b8a') {
                $mba_thesis_student_list = $this->Tm->get_thesis_defense_student();
            }
            else if ($s_academic_year_id == 'all') {
                $mba_thesis_student_list = $this->Tm->get_thesis_defense_student();
            }

            if ($mba_thesis_student_list) {
                foreach ($mba_thesis_student_list as $key => $o_thesis_student) {
                    $mba_is_deans = $this->General->get_where('ref_faculty', [
                        'deans_id' => $this->session->userdata('user'),
                        'faculty_id' => $o_thesis_student->faculty_id
                    ]);

                    $o_thesis_student->defense_date = date('d F Y', strtotime($o_thesis_student->thesis_defense_date));
                    $o_thesis_student->thesis_defense_time = date('H:i', strtotime($o_thesis_student->thesis_defense_time_start)).' - '.date('H:i', strtotime($o_thesis_student->thesis_defense_time_end));
                    $o_thesis_student->user_type = $this->get_user_type($o_thesis_student->thesis_students_id);

                    if (!$this->is_student_children($o_thesis_student->thesis_students_id)) {
                        if (!in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0','37b0f8e9-e13c-4104-adea-6c83ca1f5855', '150ba76c-4dd2-46e2-bf67-60123b0e2ce6'])) {
                            unset($mba_thesis_student_list[$key]);
                        }
                        else {
                            $o_thesis_student->user_type = 'advisor';
                        }
                    }

                    $mba_thesis_status_log = $this->Tm->get_thesis_log($o_thesis_student->thesis_students_id, [
                        'thesis_log_type' => 'work',
                        // 'thesis_status' => 'approved'
                    ]);

                    $mba_thesis_final_status_log = $this->Tm->get_thesis_log($o_thesis_student->thesis_students_id, [
                        'thesis_log_type' => 'final',
                        // 'thesis_status' => 'approved'
                    ]);

                    $mba_log_files = $this->Tm->get_thesis_list_files([
                        'sls.thesis_log_id' => ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_log_id : 'x'
                    ]);
                    if ($mba_log_files) {
                        foreach ($mba_log_files as $o_files) {
                            $o_files->filename_button = str_replace('thesis_', '', $o_files->thesis_filetype);
                        }
                    }

                    $mba_advisor_data = $this->Tm->get_list_student_advisor([
                        'ts.thesis_student_id' => $o_thesis_student->thesis_students_id
                    ], 'advisor', true);
                    $mba_examiner_data = $this->Tm->get_list_student_advisor([
                        'ts.thesis_student_id' => $o_thesis_student->thesis_students_id
                    ], 'examiner');
    
                    $a_advisor = [];
                    if ($mba_advisor_data) {
                        foreach ($mba_advisor_data as $o_advisor) {
                            $s_advisor_name = $this->Pdm->retrieve_title($o_advisor->personal_data_id);
                            array_push($a_advisor, $s_advisor_name);
                        }
                    }
    
                    $a_examiner = [];
                    if ($mba_examiner_data) {
                        foreach ($mba_examiner_data as $o_examiner) {
                            $s_examiner_name = $this->Pdm->retrieve_title($o_examiner->personal_data_id);
                            array_push($a_examiner, $s_examiner_name);
                        }
                    }

                    $mba_have_activity = $this->General->get_where('dt_activity_study', ['activity_title' => $o_thesis_student->thesis_title]);

                    $o_thesis_student->advisors = implode('|', $a_advisor);
                    $o_thesis_student->examiners = implode('|', $a_examiner);
                    $o_thesis_student->thesis_log_work_files = $mba_log_files;
                    $o_thesis_student->thesis_log_id = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_log_id : false;
                    $o_thesis_student->thesis_activity = ($mba_have_activity) ? $mba_have_activity[0]->activity_study_id : false;
                    // $o_thesis_student->thesis_work_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_work_fname : false;
                    // $o_thesis_student->thesis_plagiate_check_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_plagiate_check_fname : false;
                    // $o_thesis_student->thesis_log_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_log_fname : false;
                    // $o_thesis_student->thesis_proposal_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_proposal_fname : false;
                    // $o_thesis_student->thesis_final_fname = ($mba_thesis_final_status_log) ? $mba_thesis_final_status_log[0]->thesis_final_fname : false;
                    // $o_thesis_student->thesis_other_doc_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_other_doc_fname : false;
                    // $o_thesis_student->thesis_final_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_other_doc_fname : false;
                }
                $mba_thesis_student_list = array_values($mba_thesis_student_list);
            }
            print json_encode(['data' => $mba_thesis_student_list]);
        }
    }

    function defense_list() {
        $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, array(1,2));
        $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
        $this->a_page_data['body'] = $this->load->view('table/defense_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function thesis_defense()
    {
        // if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        //     $this->a_page_data['body'] = maintenance_page(true);
        //     $this->load->view('layout', $this->a_page_data);
        //     // exit;
        // }
        // else {
            $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, array(1,2));
            $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
            
            $this->a_page_data['academic_year_id'] = $this->session->userdata('academic_year_id_active');
            $this->a_page_data['semester_type_id'] = '2';
            $this->a_page_data['body'] = $this->load->view('thesis_defense_page', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        // }
    }

    public function test_advisor()
    {
        $mba_advisor = $this->Tm->get_list_student_advisortest([
            'ts.thesis_student_id' => '058ab9d6-857f-43b1-8eb9-bfea4ad6c2f4'
        ], 'advisor', true);
        print('<pre>');var_dump($mba_advisor);exit;
    }

    public function manage_defense($s_academic_semester)
    {
        $s_academic_year_id = substr($s_academic_semester, 0, 4);
        $s_semester_type_id = substr($s_academic_semester, 4, 1);
        $mba_thesis_student_list = $this->Tm->get_thesis_defense_student([
            'td.academic_year_id' => $s_academic_year_id,
            'td.semester_type_id' => $s_semester_type_id
        ]);
        if ($mba_thesis_student_list) {
            foreach ($mba_thesis_student_list as $o_defense) {
                $mba_advisor = $this->Tm->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_defense->thesis_students_id
                ], 'advisor', true);
                $mba_examiner = $this->Tm->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_defense->thesis_students_id
                ], 'examiner');
                $s_advisor = 'N/A';
                $a_advisor = [];
                if ($mba_advisor) {
                    foreach ($mba_advisor as $o_advisor) {
                        $s_advisor_name = $this->General->retrieve_title($o_advisor->personal_data_id);
                        $o_advisor->advisor_name = $s_advisor_name;
                        // if (!in_array($s_advisor_name, $a_advisor)) {
                        //     array_push($a_advisor, $s_advisor_name);
                        // }
                    }
                }
                if ($mba_examiner) {
                    foreach ($mba_examiner as $o_examiner) {
                        $s_advisor_name = $this->General->retrieve_title($o_examiner->personal_data_id);
                        $o_examiner->advisor_name = $s_advisor_name;
                        // if (!in_array($s_advisor_name, $a_advisor)) {
                        //     array_push($a_advisor, $s_advisor_name);
                        // }
                    }
                }
                $o_defense->advisor_data = $mba_advisor;
                $o_defense->examiner_data = $mba_examiner;
            }
        }
        
        // $this->a_page_data['academic_year_id'] = $s_academic_year_id;
        // $this->a_page_data['semester_type_id'] = $s_semester_type_id;
        $this->a_page_data['defense_list'] = $mba_thesis_student_list;
        $this->a_page_data['examiner_list'] = $this->Tm->get_advisor_list();
        $this->a_page_data['body'] = $this->load->view('thesis/table/defense_manage', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function submit_defense_collective()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_semester = $this->input->post('academic_semester');
            $mba_defense_list = $this->input->post('data');
            $a_message = [];

            if ((is_array($mba_defense_list)) AND ($mba_defense_list)) {
                foreach ($mba_defense_list as $value) {
                    if (!isset($value['thesis_student_id'])) {
                        array_push($a_message, 'student name is empty!');
                    }
                    if (!isset($value['thesis_room'])) {
                        array_push($a_message, 'room is empty!');
                    }
                    if (!isset($value['thesis_date'])) {
                        array_push($a_message, 'date is empty!');
                    }
                    if (!isset($value['thesis_time'])) {
                        array_push($a_message, 'time is empty!');
                    }
                    if (!isset($value['thesis_examiner1'])) {
                        array_push($a_message, 'examiner 1 is empty!');
                    }

                    if (count($a_message) > 0) {
                        break;
                    }

                    // $s_timestart = 
                    // $s_timeend = 
                }
            }
            else {
                array_push($a_message, 'error retrieve defense data!');
            }

            if (count($a_message) > 0) {
                $a_return = ['code' => 1, 'message' => implode('/', $a_message)];
            }
            else {
                $a_return = ['code' => 0, 'message' => 'Success!'];
            }

            print json_encode($a_return);
            // print("<pre>");var_dump($this->input->post());
        }
    }

    public function view_file($s_target = 'thesis_work', $s_thesis_student_id = null, $s_filename = null)
    {
        if ((!empty($s_thesis_student_id)) AND (!empty($s_filename))) {
            // $mba_thesis_student_data = $this->Tm->get_thesis_student([
            //     'ts.thesis_student_id' => $s_thesis_student_id
            // ]);

            $mba_old_thesis_student_data = $this->General->get_where('thesis_student', ['thesis_student_id' => $s_thesis_student_id]);
            $mba_thesis_student_data = $this->General->get_where('thesis_students', ['thesis_student_id' => $s_thesis_student_id]);

            $mba_student_filtered = false;
            // if ($mba_old_thesis_student_data) {
            //     $mba_student_filtered = $this->Stm->get_student_filtered(['ds.student_id' => $mba_old_thesis_student_data[0]->student_id]);
            // }
            // else 
            if ($mba_thesis_student_data) {
                $mba_student_filtered = $this->Stm->get_student_filtered(['ds.student_id' => $mba_thesis_student_data[0]->student_id]);
            }
            // print('<pre>');var_dump($mba_student_filtered);exit;

            if ($mba_student_filtered) {
                $o_thesis_student = $mba_student_filtered[0];
                $s_dir = APPPATH.'uploads/student/'.$o_thesis_student->academic_year_id.'/'.$o_thesis_student->study_program_abbreviation.'/'.$o_thesis_student->student_id.'/'.$s_target.'/'.$s_filename;
                if (file_exists($s_dir)) {
                    $mime = mime_content_type($s_dir);
                    header("Content-Type: ".$mime);
                    readfile( $s_dir );exit;
                }
                else {
                    // print('<pre>');var_dump($s_dir);exit;
                    show_404();
                }
            }
            else {
                show_404();
            }
        }
        else {
            show_404();
        }
    }

    public function form_edit_thesis_work()
    {
        $this->load->view('thesis/form/update_thesis_work', $this->a_page_data);
    }

    public function proposal_submission()
    {
        // if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        //     $this->a_page_data['body'] = maintenance_page(true);
        //     $this->load->view('layout', $this->a_page_data);
        //     // exit;
        // }
        // else {
            $type_allowd = ['staff', 'lect','lecturer'];
            if (in_array($this->session->userdata('type'), $type_allowd)) {
                $this->a_page_data['status_list'] = $this->get_log_status();
                $this->a_page_data['study_program_list'] = $this->Spm->get_study_program(false, false);
                $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, array(1,2));
                $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
                $this->a_page_data['body'] = $this->load->view('thesis/thesis_proposal_page', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
            else {
                $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
        // }
    }
    
    public function work_submission()
    {
        $type_allowd = ['staff', 'lect','lecturer'];
        if (in_array($this->session->userdata('type'), $type_allowd)) {
            $this->a_page_data['status_list'] = $this->get_log_status();
            $this->a_page_data['study_program_list'] = $this->Spm->get_study_program(false, false);
            $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, array(1,2));
            $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
            $this->a_page_data['body'] = $this->load->view('thesis/thesis_work_page', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function final_submission()
    {
        // if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        //     $this->a_page_data['body'] = maintenance_page(true);
        //     $this->load->view('layout', $this->a_page_data);
        //     // exit;
        // }
        // else {
            $type_allowd = ['staff', 'lect','lecturer'];
            if (in_array($this->session->userdata('type'), $type_allowd)) {
                $this->a_page_data['status_list'] = $this->get_log_status();
                $this->a_page_data['study_program_list'] = $this->Spm->get_study_program(false, false);
                $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, array(1,2));
                $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
                $this->a_page_data['body'] = $this->load->view('thesis/thesis_final_page', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
            else {
                $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
        // }
    }

    public function thesis_logs()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_student_id = $this->input->post('thesis_student_id');
            $mba_log_data = $this->Tm->get_thesis_log($s_thesis_student_id);
            if ($mba_log_data) {
                foreach ($mba_log_data as $o_log) {
                    $mba_thesis_log_file_data = $this->Tm->get_thesis_list_files([
                        'sls.thesis_log_id' => $o_log->thesis_log_id
                    ]);
                    if ($mba_thesis_log_file_data) {
                        foreach ($mba_thesis_log_file_data as $o_thesis_log) {
                            $o_thesis_log->target = explode('_', $o_thesis_log->thesis_filetype)[0].'_'.explode('_', $o_thesis_log->thesis_filetype)[1];
                        }
                    }

                    $o_log->log_file = $mba_thesis_log_file_data;
                }
            }
            print json_encode(['code' => 0, 'data' => $mba_log_data]);
        }
    }

    public function submit_reject_thesis_work()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_student_id = $this->input->post('thesis_id');
            $s_thesis_log_id = $this->input->post('thesis_log_id');
            $a_remarks = $this->input->post('remarks_data');

            $a_thesis_student_data = [
                'current_status' => 'rejected',
                // 'current_progress' => 'work'
            ];

            $a_thesis_status_log_data = [
                'thesis_status' => 'rejected'
            ];

            $this->Tm->submit_thesis_student($a_thesis_student_data, [
                'thesis_student_id' => $s_thesis_student_id
            ]);

            $this->Tm->submit_log_status($a_thesis_status_log_data, [
                'thesis_log_id' => $s_thesis_log_id
            ]);

            if (is_array($a_remarks)) {
                foreach ($a_remarks as $s_remarks) {
                    if (!empty($s_remarks)) {
                        $a_note_data = [
                            'thesis_log_note_id' => $this->uuid->v4(),
                            'thesis_log_id' => $s_thesis_log_id,
                            'remarks' => $s_remarks,
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        $this->Tm->submit_log_status_note($a_note_data);
                    }
                }
            }

            $a_return = ['code' => 0, 'message' => 'success'];

            print json_encode($a_return);
        }
    }

    // public function notification_approval($s_thesis_student_id, $s_target)
    // {
    //     $s_thesis_student_id = '';
    //     $s_target = '';
    //     $mba_thesis_student_data = $this->Tm->get_thesis_student([
    //         'ts.thesis_student_id' => $s_thesis_student_id

    //     $mba_thesis_log = $this->Tm->get_status_thesis($mba_thesis_student_data[0]->thesis_student_id, [
    //         'tsl.thesis_log_type' => $mba_thesis_student_data[0]->current_progress
    //     ]);

    //     $mba_thesis_advisor_approved = $this->Tm->get_advisor_student($mba_thesis_student_data[0]->thesis_student_id, 'approved_advisor');
    //     $a_advisor_list = [];
    //     $s_advisor_list = '';;
    //     if ($mba_thesis_advisor_approved) {
    //         foreach ($mba_thesis_advisor_approved as $o_advisor) {
    //             $s_advisor = $this->Pdm->retrieve_title($o_advisor->personal_data_id);
    //             $o_advisor->advisor_name = $s_advisor;
    //             $o_advisor->advisor_number = substr($o_advisor->advisor_type, (strlen($o_advisor->advisor_type) - 1));
    //             if (!in_array($s_advisor, $a_advisor_list)) {
    //                 array_push($a_advisor_list, $s_advisor);
    //             }
    //         }
    //         $s_advisor_list = '<li>'.implode('</li><li>', $a_advisor_list).'</li>';
    //     }

    //     if ($mba_thesis_student_data[0]->current_status == 'approved') {
    //         $dear = 'student';
    //     }
    //     $this->a_page_data['log_data'] = $mba_thesis_log;
    //     $this->a_page_data['advisor_approved'] = $s_advisor_list;
    //     $this->a_page_data['thesis_data'] = $mba_thesis_student_data[0];
    //     $this->a_page_data['body'] = $this->load->view('notification/thesis_data', $this->a_page_data, true);
    //     $this->load->view('layout', $this->a_page_data);
    // }

    public function check_roles()
    {
        print('<pre>');var_dump($this->a_user_roles);exit;
    }
    
    public function submit_approve_thesis()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_student_id = $this->input->post('thesis_id');
            $s_thesis_log_id = $this->input->post('thesis_log_id');
            $a_remarks = $this->input->post('remarks_data');
            $s_thesis_type = $this->input->post('thesis_type');
            $s_user_access = $this->get_access();
            $saving = false;

            switch ($s_user_access) {
                case 'deans':
                    $s_status_update = 'approved';
                    $saving = true;
                    break;

                case 'hsp':
                    $s_status_update = 'approved_hsp';
                    $saving = true;
                    break;
                
                default:
                    break;
            }

            $a_thesis_status_log_data = [
                'thesis_status' => $s_status_update
            ];

            if ($s_user_access == 'deans') {
                $a_thesis_status_log_data['thesis_approved_deans'] = 'true';
            }
            else if ($s_user_access == 'hsp') {
                $a_thesis_status_log_data['thesis_approved_hsp'] = 'true';
            }
            
            $a_thesis_student_data = [
                'current_status' => $s_status_update,
                'current_progress' => $s_thesis_type
            ];

            if ($saving) {
                $this->db->trans_begin();
                $this->Tm->submit_thesis_student($a_thesis_student_data, [
                    'thesis_student_id' => $s_thesis_student_id
                ]);

                $this->Tm->submit_log_status($a_thesis_status_log_data, [
                    'thesis_log_id' => $s_thesis_log_id
                ]);

                if (is_array($a_remarks)) {
                    foreach ($a_remarks as $s_remarks) {
                        if (!empty($s_remarks)) {
                            $a_note_data = [
                                'thesis_log_note_id' => $this->uuid->v4(),
                                'thesis_logs_id' => $s_thesis_log_id,
                                'remarks' => $s_remarks,
                                'date_added' => date('Y-m-d H:i:s')
                            ];
                            $this->Tm->submit_log_status_note($a_note_data);
                        }
                    }
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $a_return = ['code' => 1, 'message' => 'Error processing your data!'];
                }
                else {
                    $this->db->trans_commit();
                    $a_return = ['code' => 0, 'message' => 'success'];

//                     if ($s_status_update == 'approved_hsp') {
//                         # code... kirim email ke dekan
//                         $s_text = <<<TEXT
// Dear deans,
// HSP has approve thesis nama_mahasiswa dengan judul "judulnya",
// silahkan tinjau kembali pada portal.
//                         TEXT;
//                     }
//                     else if ($s_status_update == 'approved') {
//                         # code... kirim email ke mahasiswa
//                         $s_text = <<<TEXT
// Dear student,
// your deans has approve your thesis dengan judul "",
// anda bisa melihat progres statusnya pada portal mahasiswa.
//                         TEXT;
//                     }
                }
            }
            else {
                $a_return = ['code' => 'Your access is invalid to this action!'];
            }

            print json_encode($a_return);
        }
    }

    public function update_thesis_work()
    {
        if ($this->input->is_ajax_request()) {
            $s_thesis_title = $this->input->post("thesis_title");
            $s_thesis_student_id = $this->input->post("thesis_id");

            $s_advisor_1_id = $this->input->post("advisor_1_update");
            $s_advisor_2_id = $this->input->post("advisor_2_update");
            $s_examiner_1_id = $this->input->post("examiner_1_update");
            $s_examiner_2_id = $this->input->post("examiner_2_update");
            $s_examiner_3_id = $this->input->post("examiner_3_update");
            $s_examiner_4_id = $this->input->post("examiner_4_update");

            $this->db->trans_begin();
            $a_thesis_data = [
                'thesis_title' => $s_thesis_title
            ];
            $this->Tm->submit_thesis_student($a_thesis_data, [
                'thesis_student_id' => $s_thesis_student_id
            ]);

            $this->Tm->remove_advisor_data($s_thesis_student_id);
            $this->Tm->force_remove_data('thesis_students_examiner', [
                'thesis_student_id' => $s_thesis_student_id
            ]);

            if (!empty($s_advisor_1_id)) {
                $a_student_advisor_data_1 = [
                    'student_advisor_id' => $this->uuid->v4(),
                    'thesis_student_id' => $s_thesis_student_id,
                    'advisor_id' => $s_advisor_1_id,
                    'advisor_type' => 'approved_advisor_1'
                ];
                
                $this->Tm->submit_student_advisor($a_student_advisor_data_1);
            }

            if (!empty($s_advisor_2_id)) {

                $a_student_advisor_data_2 = [
                    'student_advisor_id' => $this->uuid->v4(),
                    'thesis_student_id' => $s_thesis_student_id,
                    'advisor_id' => $s_advisor_2_id,
                    'advisor_type' => 'approved_advisor_2'
                ];
                
                $this->Tm->submit_student_advisor($a_student_advisor_data_2);
            }

            if (!empty($s_examiner_1_id)) {
                $a_student_examiner_data_1 = [
                    'student_examiner_id' => $this->uuid->v4(),
                    'thesis_student_id' => $s_thesis_student_id,
                    'advisor_id' => $s_examiner_1_id,
                    'examiner_type' => 'examiner_1'
                ];
                
                $this->Tm->submit_student_examiner($a_student_examiner_data_1);
            }

            if (!empty($s_examiner_2_id)) {
                $a_student_examiner_data_2 = [
                    'student_examiner_id' => $this->uuid->v4(),
                    'thesis_student_id' => $s_thesis_student_id,
                    'advisor_id' => $s_examiner_2_id,
                    'examiner_type' => 'examiner_2'
                ];
                
                $this->Tm->submit_student_examiner($a_student_examiner_data_2);
            }
            if (!empty($s_examiner_3_id)) {
                $a_student_examiner_data_3 = [
                    'student_examiner_id' => $this->uuid->v4(),
                    'thesis_student_id' => $s_thesis_student_id,
                    'advisor_id' => $s_examiner_3_id,
                    'examiner_type' => 'examiner_3'
                ];
                
                $this->Tm->submit_student_examiner($a_student_examiner_data_3);
            }
            if (!empty($s_examiner_4_id)) {
                $a_student_examiner_data_4 = [
                    'student_examiner_id' => $this->uuid->v4(),
                    'thesis_student_id' => $s_thesis_student_id,
                    'advisor_id' => $s_examiner_4_id,
                    'examiner_type' => 'examiner_4'
                ];
                
                $this->Tm->submit_student_examiner($a_student_examiner_data_4);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $a_return = ['code' => 1, 'message' => 'failed processing data!'];
            }
            else {
                $this->db->trans_commit();
                $a_return = ['code' => 0, 'message' => 'succes'];
            }

            print json_encode($a_return);
        }
    }

    public function processing_advisor($s_advisor_id, $s_personal_data_name, $s_institution_id, $s_institution_name)
    {
        $mbs_advisor_id = false;
        
        if (empty($s_advisor_id)) {
            $s_personal_data_id = '';
            if (!empty($s_personal_data_name)) {
                $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_name' => $s_personal_data_name]);
                if ($mba_personal_data) {
                    $s_personal_data_id = $mba_personal_data[0]->personal_data_id;
                }
                else {
                    $s_personal_data_id = $this->uuid->v4();
                    $a_personal_data = [
                        'personal_data_id' => $s_personal_data_id,
                        'personal_data_name' => $s_personal_data_name,
                        'personal_data_cellular' => 0
                    ];
                    
                    $this->Pdm->create_personal_data_parents($a_personal_data);
                }
            }

            if (!empty($s_personal_data_id)) {
                if (empty($s_institution_id)) {
                    if (!empty($s_institution_name)) {
                        $s_institution_id = $this->uuid->v4();
                        $a_institution_data = [
                            'institution_id' => $s_institution_id,
                            'institution_name' => $s_institution_name
                        ];
                        
                        $this->Inm->insert_institution($a_institution_data);
                    }
                }
    
                $s_institution_id = (empty($s_institution_id)) ? null : $s_institution_id;
                $mba_advisor_data = $this->General->get_where('thesis_advisor', ['personal_data_id' => $s_personal_data_id]);
                if ($mba_advisor_data) {
                    $o_advisor = $mba_advisor_data[0];
                    $mbs_advisor_id = $o_advisor->advisor_id;
                    $a_advisor_data = [
                        'personal_data_id' => $s_personal_data_id,
                        'institution_id' => $s_institution_id
                    ];
                    $this->Tm->submit_advisor($a_advisor_data, [
                        'advisor_id' => $mbs_advisor_id
                    ]);
                }
                else {
                    $mbs_advisor_id = $this->uuid->v4();
                    $a_advisor_data = [
                        'advisor_id' => $mbs_advisor_id,
                        'personal_data_id' => $s_personal_data_id,
                        'institution_id' => $s_institution_id
                    ];
                    $this->Tm->submit_advisor($a_advisor_data);
                }
            }
        }
        else {
            $mba_advisor_data = $this->General->get_where('thesis_advisor', ['advisor_id' => $s_advisor_id]);
            if ($mba_advisor_data) {
                if (empty($s_institution_id)) {
                    if (!empty($s_institution_name)) {
                        $s_institution_id = $this->uuid->v4();
                        $a_institution_data = [
                            'institution_id' => $s_institution_id,
                            'institution_name' => $s_institution_name
                        ];
                        
                        $this->Inm->insert_institution($a_institution_data);
                    }
                }
    
                $s_institution_id = (empty($s_institution_id)) ? null : $s_institution_id;
                $mbs_advisor_id = $s_advisor_id;
                $a_advisor_data = [
                    'institution_id' => $s_institution_id
                ];
                $this->Tm->submit_advisor($a_advisor_data, [
                    'advisor_id' => $mbs_advisor_id
                ]);
            }
        }

        return $mbs_advisor_id;
    }

    // public function get_list_thesis()
    // {
    //     if ($this->input->is_ajax_request()) {
    //         $a_clause = [
    //             ''
    //         ];
    //     }
    // }

    public function get_thesis_list()
    {
        if ($this->input->is_ajax_request()) {
            $a_clause = [
                'tsl.academic_year_id' => $this->input->post('academic_year_id'),
                'tsl.semester_type_id' => $this->input->post('semester_type_id'),
                'st.study_program_id' => $this->input->post('study_program_id'),
                'ts.current_progress' => $this->input->post('current_progress')
            ];

            foreach ($a_clause as $key => $value) {
                if (empty($value)) {
                    unset($a_clause[$key]);
                }
            }

            if ((isset($a_clause['ts.current_progress'])) AND ($a_clause['ts.current_progress'] == 'work')) {
                unset($a_clause['ts.current_progress']);
                $a_clause['tsl.thesis_log_type'] = 'work';
            }
            else if ((isset($a_clause['ts.current_progress'])) AND ($a_clause['ts.current_progress'] == 'proposal')) {
                unset($a_clause['ts.current_progress']);
                $a_clause['tsl.thesis_log_type'] = 'proposal';
            }
            else if ((isset($a_clause['ts.current_progress'])) AND ($a_clause['ts.current_progress'] == 'final')) {
                unset($a_clause['ts.current_progress']);
                $a_clause['tsl.thesis_log_type'] = 'final';
            }

            // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                // $mba_data = $this->Tm->testget_thesis_list_by_log($a_clause);
                // print('<pre>');var_dump($a_clause);exit;
            // }

            $mba_data = $this->Tm->get_thesis_list_by_log($a_clause);
            if ($mba_data) {
                foreach ($mba_data as $o_thesis_student) {
                    $mba_thesis_advisor_approved = $this->Tm->get_list_student_advisor([
                        'ts.thesis_student_id' => $o_thesis_student->thesis_student_id
                    ], 'advisor', true, false);
                    $mba_thesis_advisor_proposed = $this->Tm->get_list_student_advisor([
                        'ts.thesis_student_id' => $o_thesis_student->thesis_student_id
                    ], 'advisor', false, true);
                    $mba_thesis_examiner = $this->Tm->get_list_student_advisor([
                        'ts.thesis_student_id' => $o_thesis_student->thesis_student_id
                    ], 'examiner');

                    // $mba_thesis_status_log = $this->Tm->get_thesis_log($o_thesis_student->thesis_student_id);
                    $a_proposed_advisor = [];
                    $a_approved_advisor = [];
                    $a_examiner = [];
                    if ($mba_thesis_advisor_proposed) {
                        foreach ($mba_thesis_advisor_proposed as $o_advisor_proposed) {
                            $s_advisor_name = $this->General->retrieve_title($o_advisor_proposed->personal_data_id);
                            if (!in_array($s_advisor_name, $a_proposed_advisor)) {
                                array_push($a_proposed_advisor, $s_advisor_name);
                            }
                        }
                    }
                    if ($mba_thesis_advisor_approved) {
                        foreach ($mba_thesis_advisor_approved as $o_advisor_approved) {
                            $s_advisor_name = $this->General->retrieve_title($o_advisor_approved->personal_data_id);
                            if (!in_array($s_advisor_name, $a_approved_advisor)) {
                                array_push($a_approved_advisor, $s_advisor_name);
                            }
                        }
                    }
                    if ($mba_thesis_examiner) {
                        foreach ($mba_thesis_examiner as $o_examiner) {
                            $s_advisor_name = $this->General->retrieve_title($o_examiner->personal_data_id);
                            if (!in_array($s_advisor_name, $a_examiner)) {
                                array_push($a_examiner, $s_advisor_name);
                            }
                        }
                    }

                    $mba_log_files = $this->Tm->get_thesis_list_files([
                        'sls.thesis_log_id' => $o_thesis_student->thesis_log_id
                    ]);
                    if ($mba_log_files) {
                        foreach ($mba_log_files as $o_files) {
                            $o_files->filename_button = str_replace('thesis_', '', $o_files->thesis_filetype);
                        }
                    }

                    $mba_thesis_defense_data = $this->Tm->get_thesis_defense_student([
                        'td.thesis_students_id' => $o_thesis_student->thesis_student_id
                    ]);

                    $o_thesis_student->defense_data = ($mba_thesis_defense_data) ? $mba_thesis_defense_data[0] : false;
                    $o_thesis_student->thesis_log_files = $mba_log_files;
                    $o_thesis_student->advisor_proposed = implode(' | ', $a_proposed_advisor);
                    $o_thesis_student->advisor_approved = implode(' | ', $a_approved_advisor);
                    $o_thesis_student->examiner_approved = implode(' | ', $a_examiner);
                }
            }

            print json_encode(['code' => 0, 'data' => $mba_data]);
        }
    }


    public function test()
    {
        $mba_log_files = $this->Tm->get_thesis_list_files([
            'sls.thesis_log_id' => '4c7cb4fe-b41c-4283-90c8-6c134e73191f'
        ]);
        print('<pre>');var_dump($mba_log_files);exit;
    }

    public function get_list_thesis()
    {
        if ($this->input->is_ajax_request()) {
            $a_clause = [
                'tp.academic_year_id' => $this->input->post('academic_year_id'),
                'tp.semester_type_id' => $this->input->post('semester_type_id'),
                'tsl.thesis_log_type' => $this->input->post('current_progress'),
                'st.study_program_id' => $this->input->post('study_program_id'),
                // 'ts.current_status' => $this->input->post('status')
            ];

            if ($this->input->post('current_progress') == 'final') {
                $a_clause = [
                    'ts.current_progress' => $this->input->post('current_progress'),
                ];
            }

            foreach ($a_clause as $key => $value) {
                if (empty($value)) {
                    unset($a_clause[$key]);
                }
            }

            $mba_data = $this->Tm->get_thesis_by_status_log($a_clause);
            if ($mba_data) {
                foreach ($mba_data as $o_thesis_student) {
                    $mba_thesis_advisor_approved = $this->Tm->get_advisor_student($o_thesis_student->thesis_student_id, 'approved_advisor');
                    $mba_thesis_advisor_proposed = $this->Tm->get_advisor_student($o_thesis_student->thesis_student_id, 'proposed_advisor');
                    $mba_thesis_examiner_approved = $this->Tm->get_examiner_student($o_thesis_student->thesis_student_id);
                    $mba_thesis_status_log = $this->Tm->get_thesis_log($o_thesis_student->thesis_student_id, [
                        'thesis_log_type' => $this->input->post('current_progress'),
                        // 'thesis_status' => $this->input->post('status')
                    ]);
                    $mba_thesis_defense_data = $this->Tm->get_thesis_defense_student([
                        'td.thesis_student_id' => $o_thesis_student->thesis_student_id
                    ]);

                    if ($this->input->post('current_progress') == 'final') {
                        $mba_thesis_status_log = $this->Tm->get_thesis_log($o_thesis_student->thesis_student_id);
                    }

                    $mba_thesis_log_note = false;
                    if ($mba_thesis_status_log) {
                        $mba_thesis_log_note = $this->General->get_where('thesis_student_log_notes', [
                            'thesis_log_id' => $mba_thesis_status_log[0]->thesis_log_id
                        ]);
                    }

                    $o_thesis_student->thesis_log_id = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_log_id : false;
                    $o_thesis_student->thesis_work_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_work_fname : false;
                    $o_thesis_student->thesis_plagiate_check_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_plagiate_check_fname : false;
                    $o_thesis_student->thesis_log_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_log_fname : false;
                    $o_thesis_student->thesis_proposal_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_proposal_fname : false;
                    $o_thesis_student->thesis_final_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_final_fname : false;
                    $o_thesis_student->thesis_other_doc_fname = ($mba_thesis_status_log) ? $mba_thesis_status_log[0]->thesis_other_doc_fname : false;
                    $o_thesis_student->log_remarks = $mba_thesis_log_note;
                    $o_thesis_student->defense_data = ($mba_thesis_defense_data) ? $mba_thesis_defense_data[0] : false;
                    $o_thesis_student->thesis_defense_schedule = ($mba_thesis_defense_data) ? $mba_thesis_defense_data[0]->thesis_defense_room.' '.date('D', strtotime($mba_thesis_defense_data[0]->thesis_defense_date)).', '.date('d M Y', strtotime($mba_thesis_defense_data[0]->thesis_defense_date)).' '.$mba_thesis_defense_data[0]->thesis_defense_time_start.'-'.$mba_thesis_defense_data[0]->thesis_defense_time_end : '';

                    $a_advisor_prop_list = [];
                    $a_advisor_prop_data = [];
                    
                    if ($mba_thesis_advisor_proposed) {
                        foreach ($mba_thesis_advisor_proposed as $o_advisor_prop) {
                            $s_advisor = $this->Pdm->retrieve_title($o_advisor_prop->personal_data_id);
                            $o_advisor_prop->advisor_name = $s_advisor;
                            if (!in_array($s_advisor, $a_advisor_prop_list)) {
                                $a_advisor_ = [
                                    'student_advisor_id' => $o_advisor_prop->student_advisor_id,
                                    'number' => substr($o_advisor_prop->advisor_type, (strlen($o_advisor_prop->advisor_type) - 1)),
                                    'personal_data_title_suffix' => $o_advisor_prop->personal_data_title_suffix,
                                    'personal_data_title_prefix' => $o_advisor_prop->personal_data_title_prefix,
                                    'personal_data_name' => $o_advisor_prop->personal_data_name,
                                    'advisor_id' => $o_advisor_prop->advisor_id,
                                    'advisor_type' => $o_advisor_prop->advisor_type,
                                    'personal_data_id' => $o_advisor_prop->personal_data_id,
                                    'advisor_name' => $s_advisor,
                                    'institution_name' => $o_advisor_prop->institution_name,
                                ];
                                array_push($a_advisor_prop_list, $s_advisor);
                                array_push($a_advisor_prop_data, $a_advisor_);
                            }
                        }
                    }
                    $a_advisor_list = [];
                    $a_advisor_data = [];
                    $s_advisor_1 = '';
                    $s_advisor_2 = '';

                    if ($mba_thesis_advisor_approved) {
                        foreach ($mba_thesis_advisor_approved as $o_advisor) {
                            $s_advisor = $this->Pdm->retrieve_title($o_advisor->personal_data_id);
                            $o_advisor->advisor_name = $s_advisor;
                            $s_advisor_number = substr($o_advisor->advisor_type, (strlen($o_advisor->advisor_type) - 1));
                            if (!in_array($s_advisor, $a_advisor_list)) {
                                $a_advisor_ = [
                                    'student_advisor_id' => $o_advisor->student_advisor_id,
                                    'number' => $s_advisor_number,
                                    'personal_data_title_suffix' => $o_advisor->personal_data_title_suffix,
                                    'personal_data_title_prefix' => $o_advisor->personal_data_title_prefix,
                                    'personal_data_name' => $o_advisor->personal_data_name,
                                    'advisor_id' => $o_advisor->advisor_id,
                                    'advisor_type' => $o_advisor->advisor_type,
                                    'personal_data_id' => $o_advisor->personal_data_id,
                                    'advisor_name' => $s_advisor,
                                    'institution_name' => $o_advisor->institution_name,
                                ];
                                array_push($a_advisor_list, $s_advisor);
                                array_push($a_advisor_data, $a_advisor_);
                            }

                            if ($s_advisor_number == '1') {
                                $s_advisor_1 = $s_advisor;
                            }
                            else if ($s_advisor_number == '2') {
                                $s_advisor_2 = $s_advisor;
                            }
                        }
                    }

                    $a_examiner_list = [];
                    $a_examiner_data = [];
                    if ($mba_thesis_examiner_approved) {
                        foreach ($mba_thesis_examiner_approved as $o_examiner) {
                            $s_examiner = $this->Pdm->retrieve_title($o_examiner->personal_data_id);
                            $o_examiner->examiner_name = $s_examiner;
                            if (!in_array($s_examiner, $a_examiner_list)) {
                                $a_examiner_ = [
                                    'student_examiner_id' => $o_examiner->student_examiner_id,
                                    'number' => substr($o_examiner->examiner_type, (strlen($o_examiner->examiner_type) - 1)),
                                    'personal_data_title_suffix' => $o_examiner->personal_data_title_suffix,
                                    'personal_data_title_prefix' => $o_examiner->personal_data_title_prefix,
                                    'personal_data_name' => $o_examiner->personal_data_name,
                                    'advisor_id' => $o_examiner->advisor_id,
                                    'examiner_type' => $o_examiner->examiner_type,
                                    'personal_data_id' => $o_examiner->personal_data_id,
                                    'examiner_name' => $s_examiner,
                                    'institution_name' => $o_examiner->institution_name,
                                ];
                                array_push($a_examiner_data, $a_examiner_);
                                array_push($a_examiner_list, $s_examiner);
                            }
                        }
                    }
                    $o_thesis_student->advisor_1 = $s_advisor_1;
                    $o_thesis_student->advisor_2 = $s_advisor_2;
                    $o_thesis_student->advisor_proposed = implode(' | ', $a_advisor_prop_list);
                    $o_thesis_student->advisor_approved = implode(' | ', $a_advisor_list);
                    $o_thesis_student->examiner_approved = implode(' | ', $a_examiner_list);
                    // $o_thesis_student->advisor_data = $mba_thesis_advisor_approved;
                    $o_thesis_student->advisor_prop_data = $a_advisor_prop_data;
                    $o_thesis_student->advisor_data = $a_advisor_data;
                    $o_thesis_student->examiner_data = $a_examiner_data;
                }
            }
            print json_encode(['code' => 0, 'data' => $mba_data]);
        }
    }

    public function get_advisor_by_name()
    {
        if ($this->input->is_ajax_request()) {
            $s_term = $this->input->post('term');
            $s_term = trim(strip_tags($s_term));
            $s_advisor_target = $this->input->post('target');
            $a_clause = false;

            if (!empty($s_advisor_target)) {
                if ($s_advisor_target == 'internal') {
                    $a_clause = [
                        'em.employee_id != ' => NULL
                    ];
                }
                else {
                    $a_clause = [
                        'em.employee_id' => NULL
                    ];
                }
            }

            $a_return_data= [];
            $mba_advisor_data = $this->Tm->get_advisor_list($s_term, $a_clause, true);
            
            if ($mba_advisor_data) {
                foreach ($mba_advisor_data as $o_advisor) {
                    $s_advisor_name = $this->Pdm->retrieve_title($o_advisor->personal_data_id);
                    $a_data = [
                        'personal_data_id' => $o_advisor->personal_data_id,
                        'personal_data_name' => $o_advisor->personal_data_name,
                        'advisor_id' => $o_advisor->advisor_id,
                        'insitution_id' => $o_advisor->institution_id,
                        'institution_name' => $o_advisor->institution_name,
                        'advisor_name' => $s_advisor_name
                    ];
                    array_push($a_return_data, $a_data);
                }
            }
            if (count($a_return_data) > 0) {
                $a_return_data = array_values($a_return_data);
            }

            print json_encode($a_return_data);
        }
    }

    public function get_log_status()
    {
        $a_status_list = [];
        $a_status = $this->General->get_enum_values('thesis_student_log_status', 'thesis_status');
        foreach ($a_status as $key => $value) {
            if ($value == 'approved_hsp') {
                $a_status_list[$value] = 'waiting deans approval';
            }
            else if ($value == 'pending') {
                $a_status_list[$value] = 'student submitted';
            }
            else {
                $a_status_list[$value] = $value;
            }
        }
        
        return $a_status_list;
    }

    public function check_children()
    {
        $s_advisor_id = 'ce1f9ab5-3651-4aee-8fa7-5ea3a3b19587';
        $s_thesis_student_id = '832d99e3-4580-433f-9265-96d5ac524184';
        
        $is_student_advisor = $this->Tm->is_advisor_examiner([
            'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
            'ta.personal_data_id' => $this->session->userdata('user')
        ], 'advisor', true);
        $is_student_examiner = $this->Tm->is_advisor_examiner([
            'tsa.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id,
            'ta.personal_data_id' => $this->session->userdata('user')
        ], 'examiner');

        $mba_student_advisor = $this->Tm->check_is_advisor([
            'tsa.advisor_id' => $s_advisor_id,
            'tsa.thesis_student_id' => $s_thesis_student_id
        ]);
        if (!$mba_student_advisor) {
            $mba_student_examiner = $this->Tm->check_is_examiner([
                'tse.advisor_id' => $s_advisor_id,
                'tse.thesis_student_id' => $s_thesis_student_id
            ]);

            if ($mba_student_examiner) {
                print('ada');
            }
            else {
                print('kosong');
            }
        }
        else {
            print('ada');
        }
    }

    public function cek_usertype()
    {
        $mba_advisor_data = $this->General->get_where('thesis_advisor', ['personal_data_id' => $this->session->userdata('user')]);
        print('<pre>');var_dump($mba_advisor_data);exit;
    }

    private function get_user_type($s_thesis_student_id)
    {
        $s_type = 'guest';
        $mba_advisor_data = $this->General->get_where('thesis_advisor', ['personal_data_id' => $this->session->userdata('user')]);
        if ($mba_advisor_data) {
            $s_advisor_id = $mba_advisor_data[0]->advisor_id;

            $mba_student_advisor = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $s_thesis_student_id,
                'tsa.advisor_id' => $s_advisor_id
            ], 'advisor', true);

            if (!$mba_student_advisor) {
                $mba_student_examiner = $this->Tm->is_advisor_examiner([
                    'tsa.thesis_student_id' => $s_thesis_student_id,
                    'tsa.advisor_id' => $s_advisor_id
                ], 'examiner');

                if ($mba_student_examiner) {
                    $s_type = 'examiner';
                }
            }
            else {
                $s_type = 'advisor';
            }
        }

        if ($s_type == 'guest') {
            $mba_thesis_student_data = $this->Tm->get_thesis_defense_student(['ts.thesis_student_id' => $s_thesis_student_id]);
            $mba_is_deans = $this->General->get_where('ref_faculty', [
                'deans_id' => $this->session->userdata('user'),
                'faculty_id' => $mba_thesis_student_data[0]->faculty_id
            ]);

            if ($mba_is_deans) {
                $s_type = 'deans';
            }
        }
        
        return $s_type;
    }

    private function is_student_children($s_thesis_student_id)
    {
        $mba_advisor_data = $this->General->get_where('thesis_advisor', ['personal_data_id' => $this->session->userdata('user')]);
        $mba_thesis_student_data = $this->Tm->get_thesis_defense_student(['td.thesis_students_id' => $s_thesis_student_id]);
        
        $mba_is_deans = $this->General->get_where('ref_faculty', [
            'deans_id' => $this->session->userdata('user'),
            'faculty_id' => $mba_thesis_student_data[0]->faculty_id
        ]);

        if ($mba_is_deans) {
            return true;
        }
        else if ($mba_advisor_data) {
            $s_advisor_id = $mba_advisor_data[0]->advisor_id;
            $mba_student_advisor = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $s_thesis_student_id,
                'tsa.advisor_id' => $s_advisor_id
            ], 'advisor', true);

            if (!$mba_student_advisor) {
                $mba_student_examiner = $this->Tm->is_advisor_examiner([
                    'tsa.thesis_student_id' => $s_thesis_student_id,
                    'tsa.advisor_id' => $s_advisor_id
                ], 'examiner');

                if ($mba_student_examiner) {
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }

    public function cek_children()
    {
        $s_thesis_student_id = '058ab9d6-857f-43b1-8eb9-bfea4ad6c2f4';
        $mba_advisor_data = $this->General->get_where('thesis_advisor', ['personal_data_id' => $this->session->userdata('user')]);
        $mba_thesis_student_data = $this->Tm->get_thesis_defense_student(['td.thesis_students_id' => $s_thesis_student_id]);

        $mba_is_deans = $this->General->get_where('ref_faculty', [
            'deans_id' => $this->session->userdata('user'),
            'faculty_id' => $mba_thesis_student_data[0]->faculty_id
        ]);

        if ($mba_is_deans) {
            return true;
        }
        else if ($mba_advisor_data) {
            $s_advisor_id = $mba_advisor_data[0]->advisor_id;
            $mba_student_advisor = $this->Tm->is_advisor_examiner([
                'tsa.thesis_student_id' => $s_thesis_student_id,
                'tsa.advisor_id' => $s_advisor_id
            ], 'advisor', true);

            print('<pre>');var_dump($mba_student_advisor);exit;

            if (!$mba_student_advisor) {
                $mba_student_examiner = $this->Tm->is_advisor_examiner([
                    'tsa.thesis_student_id' => $s_thesis_student_id,
                    'tsa.advisor_id' => $s_advisor_id
                ], 'examiner');

                if ($mba_student_examiner) {
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }

    private function get_access()
    {
        $type_allowd = ['staff', 'lect', 'lecturer'];
        $s_access = 'guest';
        
        if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0', '37b0f8e9-e13c-4104-adea-6c83ca1f5855'])) {
            $s_access = 'super';
        }
        if ($this->session->userdata('type') == 'student') {
            $s_access = 'student';
        }
        else if (in_array($this->session->userdata('type'), $type_allowd)) {
            $mba_faculty = $this->General->get_where('ref_faculty', ['deans_id' => $this->session->userdata('user')]);
            $mba_prodi = $this->General->get_where('ref_study_program', ['head_of_study_program_id' => $this->session->userdata('user')]);
            $mba_is_advisor = $this->Tm->get_list_student_advisor([
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'advisor', true);
            $mba_is_examiner = $this->Tm->get_list_student_advisor([
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'examiner', true);
            
            
            if ($mba_faculty) {
                $s_access = 'deans';
            }
            else if ($mba_prodi) {
                $s_access = 'hsp';
            }
            else if ($mba_is_advisor) {
                $s_access = 'advisor';
            }
            else if ($mba_is_examiner) {
                $s_access = 'examiner';
            }

            // if ($this->session->userdata('user') == '8ad35388-0e41-4731-9cc3-fcded4c1ed7b') {
            //     $s_access = 'super';
            // }
        }

        // print('<pre>');var_dump($s_access);exit;
        return $s_access;
    }

    public function get_thesis_student($s_student_id = false)
    {
        if (!$s_student_id) {
            $s_student_id = $this->session->userdata('student_id');
        }

        $mba_thesis_student = $this->Tm->get_thesis_student([
            'ts.student_id' => $this->session->userdata('student_id'),
            // 'tp.academic_year_id' => $this->session->userdata('academic_year_id_active'),
            // 'tp.semester_type_id' => $this->session->userdata('semester_type_id_active')

        ], 'ts.date_added', 'DESC');

        // if ($this->session->userdata('user') == '08452f3f-263b-4fb7-a590-020c17ceef06') {
        //     print('<pre>');var_dump($mba_thesis_student[0]->current_progress);exit;
        // }

        if (($mba_thesis_student) AND ($mba_thesis_student[0]->current_progress == 'proposal')) {
            $mba_thesis_student = $this->Tm->get_thesis_student([
                'ts.student_id' => $this->session->userdata('student_id'),
                'tp.academic_year_id' => $this->session->userdata('academic_year_id_active'),
                'tp.semester_type_id' => $this->session->userdata('semester_type_id_active')
    
            ], 'ts.date_added', 'DESC');
        }
        
        $o_thesis_student = false;
        if ($mba_thesis_student) {
            $o_thesis_student=  $mba_thesis_student[0];
            // var_dump($o_thesis_student->thesis_student_id);exit;
            $mba_thesis_log = $this->Tm->get_status_thesis($o_thesis_student->thesis_student_id, [
                'tsl.thesis_log_type' => $o_thesis_student->current_progress
            ]);

            $mba_thesis_advisor_approved = $this->Tm->get_advisor_student($o_thesis_student->thesis_student_id, 'approved_advisor');
            $mba_thesis_advisor_proposed = $this->Tm->get_advisor_student($o_thesis_student->thesis_student_id, 'proposed_advisor');
            $o_thesis_student->log_data = $mba_thesis_log;
            $o_thesis_student->thesis_log_id = ($mba_thesis_log) ? $mba_thesis_log[0]->thesis_log_id : '';
            $a_advisor_list = [];
            $s_advisor_list = '';
            $s_advisor_1 = '';
            $s_advisor_2 = '';
            $a_advisor_list_proposed = [];
            $s_advisor_list_proposed = '';
            if ($mba_thesis_advisor_approved) {
                foreach ($mba_thesis_advisor_approved as $o_advisor) {
                    $s_advisor = $this->Pdm->retrieve_title($o_advisor->personal_data_id);
                    $o_advisor->advisor_name = $s_advisor;
                    $o_advisor->advisor_number = substr($o_advisor->advisor_type, (strlen($o_advisor->advisor_type) - 1));
                    if (!in_array($s_advisor, $a_advisor_list)) {
                        array_push($a_advisor_list, $s_advisor);
                    }

                    if ($o_advisor->advisor_number == 1) {
                        $s_advisor_1 = $s_advisor;
                    }
                    if ($o_advisor->advisor_number == 2) {
                        $s_advisor_2 = $s_advisor;
                    }
                }
                $s_advisor_list = '<li>'.implode('</li><li>', $a_advisor_list).'</li>';
            }

            if ($mba_thesis_advisor_proposed) {
                foreach ($mba_thesis_advisor_proposed as $o_advisor_prop) {
                    $s_advisor = $this->Pdm->retrieve_title($o_advisor_prop->personal_data_id);
                    $o_advisor_prop->advisor_name = $s_advisor;
                    $o_advisor_prop->advisor_number = substr($o_advisor_prop->advisor_type, (strlen($o_advisor_prop->advisor_type) - 1));
                    if (!in_array($s_advisor, $a_advisor_list_proposed)) {
                        array_push($a_advisor_list_proposed, $s_advisor);
                    }
                }
                $s_advisor_list_proposed = '<li>'.implode('</li><li>', $a_advisor_list_proposed).'</li>';
            }
            $o_thesis_student->advisor_approved = $s_advisor_list;
            $o_thesis_student->advisor_proposed = $s_advisor_list_proposed;
            $o_thesis_student->advisor_approve_data = $mba_thesis_advisor_approved;
            $o_thesis_student->advisor_propose_data = $mba_thesis_advisor_proposed;
            $o_thesis_student->advisor_1 = $s_advisor_1;
            $o_thesis_student->advisor_2 = $s_advisor_2;
        }

        return ($mba_thesis_student) ? $o_thesis_student : false;
    }
}
