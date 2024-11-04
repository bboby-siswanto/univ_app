<?php
class Lecturer_assesment extends App_core
{
    public $s_academic_year_id_current;
    public $s_semester_type_id_current;
    function __construct()
    {
        parent::__construct();
        $this->load->model('Validation_requirement_model', 'Vm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('academic/Class_group_model', 'Cgm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->s_academic_year_id_current = $this->session->userdata('academic_year_id_active');
        $this->s_semester_type_id_current = $this->session->userdata('semester_type_id_active');
    }

    public function view_template()
    {
        $this->load->model('validation_requirement/Assessment_model', 'Asm');
		$this->load->model('validation_requirement/Validation_requirement_model', 'Vm');
		$this->a_page_data['question_list'] = $this->Vm->get_question_list([
			'question_status' => 'active'
		]);
		$this->a_page_data['score_option'] = $this->Vm->get_result_option();

        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 9,
            'default_font' => 'sans_fonts',
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch'
        ]);

        $s_html = $this->load->view('validation_requirement/lecturer_assesment/template/template_question', $this->a_page_data, true);
        // print($s_html);
        $s_filename = 'template_question_lecturer_assessment.pdf';
        $mpdf->WriteHTML($s_html);
        $s_dir = APPPATH.'uploads/templates/academic/assessment/';
        if(!file_exists($s_dir)){
            mkdir($s_dir, 0777, TRUE);
        }
        
        $mpdf->Output($s_dir.$s_filename, 'F');
        if(!file_exists($s_dir.$s_filename)){
            show_404();
        }
        else{
            $s_mime = mime_content_type($s_dir.$s_filename);
            $a_path_info = pathinfo($s_dir.$s_filename);
            $s_file_ext = $a_path_info['extension'];
            header("Content-Type: ".$s_mime);
            readfile( $s_dir.$s_filename );
            exit;
        }
    }

    public function lecturer_assessment_result($s_class_master_id, $s_employee_id)
    {
        $mba_question_aspect_list = $this->Vm->get_student_question_list([
            'ar.employee_id' => $s_employee_id,
            'sc.class_master_id' => $s_class_master_id
        ]);
        $mba_employee_data = $this->General->get_where('dt_employee', ['employee_id' => $s_employee_id]);
        $s_lecturer_name = $this->Pdm->retrieve_title($mba_employee_data[0]->personal_data_id);
        $mba_class_subject = $this->Cgm->get_class_master_subject(['cm.class_master_id' => $s_class_master_id]);
        $i_total_respondent = 0;
        $a_comment = [];
        $a_class_study_prog = [];

        $mba_assessment_result = $this->Vm->get_lecturer_score_counter([
            'd1sc.class_master_id' => $s_class_master_id,
            'd2ar.employee_id' => $s_employee_id
        ]);

        if ($mba_assessment_result) {
            $mbo_class_study_prog = $this->Cgm->get_class_master_study_program($s_class_master_id);
            if ($mbo_class_study_prog) {
                foreach ($mbo_class_study_prog as $prod) {
                    if (!in_array($prod->study_program_abbreviation, $a_class_study_prog)) {
                        array_push($a_class_study_prog, $prod->study_program_abbreviation);
                    }
                }
            }
            
            foreach ($mba_assessment_result as $o_result) {
                $mba_question_aspect_list_result = $this->Vm->get_student_question_list([
                    'ar.employee_id' => $s_employee_id,
                    'sc.class_master_id' => $s_class_master_id
                ]);
                $i_total_respondent++;
                if ((!empty($o_result->result_comment)) AND (!is_null($o_result->result_comment))) {
                    array_push($a_comment, $o_result->result_comment);
                }
                
                if ($mba_question_aspect_list_result) {
                    // $a_question_result = [];
                    foreach ($mba_question_aspect_list_result as $o_question) {
                        $mba_result_question = $this->Vm->get_result_question([
                            'rq.result_id' => $o_result->result_id,
                            'rq.question_id' => $o_question->question_id
                        ]);
                        
                        $q_number = $o_question->number;
                        $o_question->value_question_answer = ($mba_result_question) ? $mba_result_question[0]->score_value : 0;
                        // if ($o_result->result_id == '59a77bcd-7f7e-4ba4-9b70-e7f3277bee04') {
                        //     print('<pre>');var_dump($o_question);exit;
                        // }
                    }
                }
                $o_result->question_details = $mba_question_aspect_list_result;
            }
        }
        
        $this->a_page_data['period'] = $this->s_academic_year_id_current.$this->s_semester_type_id_current;
        $this->a_page_data['lecturer_name'] = $s_lecturer_name;
        $this->a_page_data['subject_name'] = $mba_class_subject[0]->subject_name;
        $this->a_page_data['total_respondent'] = $i_total_respondent;
        $this->a_page_data['result_comment'] = $a_comment;
        $this->a_page_data['assessment_result'] = $mba_assessment_result;
        $this->a_page_data['study_program'] = (count($a_class_study_prog) > 0) ? implode(' / ', $a_class_study_prog) : '-';
        $this->a_page_data['question_list'] = $mba_question_aspect_list;
        $this->a_page_data['body'] = $this->load->view('lecturer_assesment/reports_lecturer', $this->a_page_data, true);
        // $s_html = $this->load->view('layout_public', $this->a_page_data, true);
        $s_html = $this->load->view('lecturer_assesment/reports_lecturer', $this->a_page_data, true);
        // print('<pre>');var_dump($mba_assessment_result);exit;

        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 9,
            'default_font' => 'sans_fonts',
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch'
        ]);

        $s_filename = 'test_file.pdf';

        $s_dir = APPPATH.'uploads/';
        if(!file_exists($s_dir)){
            mkdir($s_dir, 0777, TRUE);
        }
        
        $mpdf->SetHTMLHeader('<img src="./assets/img/header_of_file.png" alt="">');
        $mpdf->SetHTMLFooter('<img src="./assets/img/footer_of_file.jpeg" alt="">');
        $mpdf->WriteHTML($s_html);
        $mpdf->Output($s_dir.$s_filename, 'F');

        // echo $s_html;
        if(!file_exists($s_dir.$s_filename)){
            // $a_return = array('code' => 1, 'message' => 'Failed generated file!');
            print('kosong!');exit;
        }
        else{
            $s_file_path = $s_dir.$s_filename;
            $s_mime = mime_content_type($s_file_path);
			$a_path_info = pathinfo($s_file_path);
			$s_file_ext = $a_path_info['extension'];
			header("Content-Type: ".$s_mime);
			readfile( $s_file_path );
            
            // $a_path_info = pathinfo($s_dir.$s_filename);
            // header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            // readfile( $ );
            exit;
        }
    }

    public function lecturer_result($s_class_master_id, $s_employee_id)
    {
        $mba_question_aspect_list = $this->Vm->get_student_question_list([
            'ar.employee_id' => $s_employee_id,
            'sc.class_master_id' => $s_class_master_id
        ]);
        
        $mba_employee_data = $this->General->get_where('dt_employee', ['employee_id' => $s_employee_id]);
        $s_lecturer_name = $this->Pdm->retrieve_title($mba_employee_data[0]->personal_data_id);
        $mba_class_subject = $this->Cgm->get_class_master_subject(['cm.class_master_id' => $s_class_master_id]);
        $i_total_respondent = 0;
        $a_comment = [];
        $a_class_study_prog = [];
        if ($mba_question_aspect_list) {
            $mbo_class_study_prog = $this->Cgm->get_class_master_study_program($s_class_master_id);
            if ($mbo_class_study_prog) {
                foreach ($mbo_class_study_prog as $prod) {
                    if (!in_array($prod->study_program_abbreviation, $a_class_study_prog)) {
                        array_push($a_class_study_prog, $prod->study_program_abbreviation);
                    }
                }
            }

            $mba_assessment_result = $this->Vm->get_lecturer_score_counter([
                'd1sc.class_master_id' => $s_class_master_id,
                'd2ar.employee_id' => $s_employee_id
            ]);

            if ($mba_assessment_result) {
                foreach ($mba_assessment_result as $o_result) {
                    $i_total_respondent++;
                    if ((!empty($o_result->result_comment)) AND (!is_null($o_result->result_comment))) {
                        array_push($a_comment, $o_result->result_comment);
                    }
                }
            }
            foreach ($mba_question_aspect_list as $o_question) {
                $d_score = 0;
                $assessment = $this->Vm->get_lecturer_score_counter([
                    'd1sc.class_master_id' => $s_class_master_id,
                    'd2ar.employee_id' => $s_employee_id
                ]);
                
                if ($assessment) {
                    foreach ($assessment as $o_assessment) {
                        $mba_question_result = $this->Vm->get_result_question([
                            'rq.result_id' => $o_assessment->result_id,
                            'rq.question_id' => $o_question->question_id
                        ]);

                        if ($mba_question_result) {
                            $d_score += $mba_question_result[0]->score_value;
                        }
                    }
                }
                
                $o_question->result_assessment = $d_score;
            }
        }

        $this->a_page_data['period'] = $mba_class_subject[0]->class_academic_year_id.$mba_class_subject[0]->class_semester_type_id;
        $this->a_page_data['lecturer_name'] = $s_lecturer_name;
        $this->a_page_data['subject_name'] = $mba_class_subject[0]->subject_name;
        $this->a_page_data['total_respondent'] = $i_total_respondent;
        $this->a_page_data['result_comment'] = $a_comment;
        $this->a_page_data['study_program'] = (count($a_class_study_prog) > 0) ? implode(' / ', $a_class_study_prog) : '-';
        $this->a_page_data['question_list'] = $mba_question_aspect_list;
        $this->a_page_data['body'] = $this->load->view('lecturer_assesment/recaps_lecturer', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function list_lecturer()
    {
        $this->a_page_data['active_batch'] = $this->General->get_batch();
        $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, array(1,2));
        $this->a_page_data['body'] = $this->load->view('lecturer_assesment/view_lecturer', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function list_respondent()
    {
        $this->a_page_data['active_batch'] = $this->General->get_batch();
        $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, array(1,2));
        $this->a_page_data['body'] = $this->load->view('lecturer_assesment/view_respondent', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function get_list_respondent_prodi()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('period_academic_year');
            $s_semester_type_id = $this->input->post('period_semester_type_id');

            $s_academic_year_id = (empty($s_academic_year_id)) ? $this->session->userdata('academic_year_id_active') : $s_academic_year_id;
            $s_semester_type_id = (empty($s_semester_type_id)) ? $this->session->userdata('semester_type_id_active') : $s_semester_type_id;

            $mba_student_list_prodi_all = $this->Vm->get_student_list_assessment([
                'sc.academic_year_id' => $s_academic_year_id,
                'sc.semester_type_id' => $s_semester_type_id
            ], 'sp.study_program_id');
            // print('<pre>');var_dum

            if ($mba_student_list_prodi_all) {
                foreach ($mba_student_list_prodi_all as $o_study_program) {
                    $mba_student_list = $this->Vm->get_student_list_assessment([
                        'sc.academic_year_id' => $s_academic_year_id,
                        'sc.semester_type_id' => $s_semester_type_id,
                        'st.study_program_id' => $o_study_program->study_program_id
                    ], 'sc.student_id');

                    $o_study_program->count_student = ($mba_student_list) ? count($mba_student_list) : 0;
                    $o_study_program->list_student = $mba_student_list;
                }
            }

            print json_encode(['data' => $mba_student_list_prodi_all]);
        }
    }

    public function get_list_lecturer_subject()
    {
        $this->load->model('academic/Class_group_model', 'Cgm');
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('period_academic_year');
            $s_semester_type_id = $this->input->post('period_semester_type_id');
            
            $is_me = (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0', '089a7b36-a1cd-4683-adcc-a2e8e8d5606d'])) ? true : false;
            // $is_me = ($this->session->userdata('user') == '') ? true : false;
            $is_deans = $this->General->get_where('ref_faculty', ['deans_id' => $this->session->userdata('user')]);
            $is_hod = $this->General->get_where('ref_study_program', ['head_of_study_program_id' => $this->session->userdata('user'), 'study_program_main_id' => NULL]);
            
            $s_academic_year_id = (empty($s_academic_year_id)) ? $this->session->userdata('academic_year_id_active') : $s_academic_year_id;
            $s_semester_type_id = (empty($s_semester_type_id)) ? $this->session->userdata('semester_type_id_active') : $s_semester_type_id;
            $mba_list_department = $this->General->get_where('ref_department');
            $a_departement_all = [];
            if ($mba_list_department) {
                foreach ($mba_list_department as $o_depart) {
                    $mba_prodi_dept = $this->General->get_where('ref_study_program', ['study_program_abbreviation' => $o_depart->department_abbreviation]);
                    if ((!$mba_prodi_dept) AND (!in_array($o_depart->department_id, $a_departement_all))) {
                        array_push($a_departement_all, $o_depart->department_id);
                    }
                }
            }

            $a_department = [];
            $a_filter = [
                'd1sc.academic_year_id' => $s_academic_year_id,
                'd1sc.semester_type_id' => $s_semester_type_id,
                'd1em.status != ' => 'RESIGN'
            ];

            if ($is_me) {
                $a_department = false;
            }
            else if ($is_deans) {
                foreach ($is_deans as $o_faculty_deans) {
                    $mba_prodi_list = $this->General->get_where('ref_study_program', ['faculty_id' => $o_faculty_deans->faculty_id]);
                    if ($mba_prodi_list) {
                        foreach ($mba_prodi_list as $o_prodi) {
                            $mba_depart_data = $this->General->get_where('ref_department', ['department_abbreviation' => $o_prodi->study_program_abbreviation]);
                            if ($mba_depart_data) {
                                if (!in_array($mba_depart_data[0]->department_id, $a_department)) {
                                    array_push($a_department, $mba_depart_data[0]->department_id);
                                }
                            }
                        }
                    }
                }
            }
            else if ($is_hod) {
                foreach ($is_hod as $o_prodi) {
                    $mba_depart_data = $this->General->get_where('ref_department', ['department_abbreviation' => $o_prodi->study_program_abbreviation]);
                    if ($mba_depart_data) {
                        if (!in_array($mba_depart_data[0]->department_id, $a_department)) {
                            array_push($a_department, $mba_depart_data[0]->department_id);
                        }
                    }
                }
                // $mba_depart_data = $this->General->get_where('ref_department', ['department_abbreviation' => $is_hod[0]->study_program_abbreviation]);
                // if (!in_array($is_hod[0]->study_program_abbreviation, $a_department)) {
                //     array_push($a_department, $is_hod[0]->study_program_abbreviation);
                // }
            }
            else {
                $a_filter = ['d1em.employee_id' => NULL];
            }

            if ($is_deans) {
                $a_department = array_merge($a_department, $a_departement_all);
            }

            $mba_lecturer_list_all = $this->Vm->get_lecturer_list_assessment($a_filter, true, $a_department);
            // print('<pre>');var_dump($this->db->last_query());exit;
            // print('<pre>');var_dump($mba_lecturer_list_all);exit;

            if ($mba_lecturer_list_all) {
                foreach ($mba_lecturer_list_all as $o_lecturer) {
                    $a_class_study_prog = array();
                    $mba_department_data = $this->General->get_where('ref_department', ['department_id' => $o_lecturer->department_id]);
                    $mbo_class_study_prog = $this->Cgm->get_class_master_study_program($o_lecturer->class_master_id);
                    $mba_lecturer_list_counter = $this->Vm->get_lecturer_score_counter([
                        'd1sc.class_master_id' => $o_lecturer->class_master_id,
                        'd2ar.employee_id' => $o_lecturer->employee_assessment
                    ]);
                    
                    if ($mbo_class_study_prog) {
                        foreach ($mbo_class_study_prog as $prod) {
                            if (!in_array($prod->study_program_abbreviation, $a_class_study_prog)) {
                                array_push($a_class_study_prog, $prod->study_program_abbreviation);
                            }
                        }
                    }

                    $o_lecturer->lecturer_name = $this->Pdm->retrieve_title($o_lecturer->personal_data_id);
                    $o_lecturer->study_program = (count($a_class_study_prog) > 0) ? implode(' / ', $a_class_study_prog) : 'N/A';
                    $o_lecturer->counter_respondent = ($mba_lecturer_list_counter) ? count($mba_lecturer_list_counter) : 0;
                    $o_lecturer->employee_department = ($mba_department_data) ? $mba_department_data[0]->department_abbreviation : '';
                }
            }

            print json_encode(['data' => $mba_lecturer_list_all]);
        }
    }

    public function submit_assessment()
    {
        if ($this->input->is_ajax_request()) {
            $mba_question_aspect_list = $this->Vm->get_question_list([
                'question_status' => 'active'
            ]);

            if ($mba_question_aspect_list) {
                foreach ($mba_question_aspect_list as $o_question) {
                    $this->form_validation->set_rules('result_question_'.$o_question->question_id, 'Question Number '.$o_question->number, 'required');
                }
            }
            else {
                print json_encode(['code' => 2, 'message' => 'No Question Aspect available!']);exit;
            }
            
            if ($this->form_validation->run()) {
                if (empty($this->input->post('score_id_assessment'))) {
                    $a_return = array('code' => 1, 'message' => 'Error system LA1, please contact IT Dept.');
                }
                else if (empty($this->input->post('employee_id_assessment'))) {
                    $a_return = array('code' => 1, 'message' => 'Error system LA2, please contact IT Dept.');
                }
                else {
                    $s_result_id = $this->uuid->v4();
                    $d_total_score = 0;
                    $a_details_data = [];
                    
                    foreach ($mba_question_aspect_list as $o_question) {
                        $s_result_question_id = $this->uuid->v4();
                        $a_result_question = [
                            'result_question_id' => $s_result_question_id,
                            'result_id' => $s_result_id,
                            'question_id' => $o_question->question_id,
                            'score_result_id' => set_value('result_question_'.$o_question->question_id)
                        ];
                        array_push($a_details_data, $a_result_question);
                        
                        $mba_score_result_data = $this->Vm->get_result_option(['score_result_id' => set_value('result_question_'.$o_question->question_id)]);
                        $d_total_score += $mba_score_result_data[0]->score_value;
                    }
                    // print('<pre>');var_dump($a_details_data);exit;

                    $a_result_data= [
                        'result_id' => $s_result_id,
                        'score_id' => $this->input->post('score_id_assessment'),
                        'employee_id' => $this->input->post('employee_id_assessment'),
                        'result_comment' => (empty($this->input->post('result_comment'))) ? NULL : $this->input->post('result_comment'),
                        'total_score' => $d_total_score
                    ];

                    $submit_data = $this->Vm->submit_lecturer_assessment($a_result_data, $a_details_data);
                    if ($submit_data) {
                        $a_return = ['code' => 0];
                    }
                    else {
                        $a_return = ['code' => 2, 'message' => 'Error submiting data, please contact IT Dept.'];
                    }
                }
            }
            else {
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }

    public function check_subject()
    {
        $s_student_id = 'f82e85ae-ce4d-4b02-8376-301e9c2773f0';
        $mba_lecturer_list = $this->Vm->get_lecturer_list_student([
            'sc.student_id' => $s_student_id,
            'sc.academic_year_id' => $this->session->userdata('academic_year_id_active'),
            'sc.semester_type_id' => $this->session->userdata('semester_type_id_active')
        ]);

        
        print('<pre>');var_dump($mba_lecturer_list);exit;
    }

    public function validate()
    {
        $s_student_id = $this->session->userdata('student_id');
        $mba_lecturer_list = $this->Vm->get_lecturer_list_student([
            'sc.student_id' => $s_student_id,
            'sc.academic_year_id' => $this->session->userdata('academic_year_id_active'),
            'sc.semester_type_id' => $this->session->userdata('semester_type_id_active')
        ]);
        $b_has_submitted = true;
            
        if ($mba_lecturer_list) {
            foreach ($mba_lecturer_list as $o_lecturer_class) {
                $has_submitted = $this->Vm->check_lecturer_assessment([
                    'ar.score_id' => $o_lecturer_class->score_id,
                    'ar.employee_id' => $o_lecturer_class->employee_id
                ]);
                if (!$has_submitted) {
                    $b_has_submitted = false;
                }
                $o_lecturer_class->has_submited = ($has_submitted) ? true : false;
                $o_lecturer_class->lecturer_name = $this->Pdm->retrieve_title($o_lecturer_class->personal_data_id);
            }
        }

        $this->a_page_data['question_list'] = $this->Vm->get_question_list([
            'question_status' => 'active'
        ]);
        $this->a_page_data['score_option'] = $this->Vm->get_result_option();
        $this->a_page_data['student_id'] = $s_student_id;

        // if ($s_student_id == 'd9868ebf-ef1a-4ede-80df-b16ea0df93ee') {
        //     $b_has_submitted = true;
        // }

        if (!$b_has_submitted) {
            // if ($this->session->userdata('develepment_mode') === false) {
            if (date('Y-m-d') < date('Y-m-d', strtotime('2024-06-22'))) {
                // $this->a_page_data['body'] = $this->load->view('validation_requirement/lecturer_assesment/modal_assesment', $this->a_page_data, true);
                // $this->load->view('validation_requirement/lecturer_assesment/modal_assesment', $this->a_page_data);
            }
            // }
        }
    }

    public function show_question()
    {
        $this->a_page_data['question_list'] = $this->Vm->get_question_list([
            'question_status' => 'active'
        ]);
        $this->a_page_data['student_id'] = 'ea881d6d-21be-4858-bdfd-3dd30ed44eda';
        $this->a_page_data['score_option'] = $this->Vm->get_result_option();
        $this->a_page_data['body'] = $this->load->view('validation_requirement/lecturer_assesment/modal_assesment', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function get_lecturer_subject()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            if (empty($s_student_id)) {
                $s_student_id = $this->session->userdata('student_id');
            }

            $mba_lecturer_list = $this->Vm->get_lecturer_list_student([
                'sc.student_id' => $s_student_id,
                'sc.academic_year_id' => $this->session->userdata('academic_year_id_active'),
                'sc.semester_type_id' => $this->session->userdata('semester_type_id_active')
            ]);

            if ($mba_lecturer_list) {
                foreach ($mba_lecturer_list as $o_lecturer_class) {
                    $has_submitted = $this->Vm->check_lecturer_assessment([
                        'ar.score_id' => $o_lecturer_class->score_id,
                        'ar.employee_id' => $o_lecturer_class->employee_id
                    ]);
                    $o_lecturer_class->has_submited = ($has_submitted) ? true : false;
                    $o_lecturer_class->lecturer_name = $this->Pdm->retrieve_title($o_lecturer_class->personal_data_id);
                }
            }

            // $mba_lecturer_list = false;
            $return = ['data' => $mba_lecturer_list];
            if ($this->session->userdata('student_id') == 'e035524c-0a3c-438d-bf6f-97e6e900011e') {
                // $return = ['data' => 'aas'];
            }
            print json_encode($return);
            // exit;
        }
    }
}
