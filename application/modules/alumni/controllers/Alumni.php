<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;	

class Alumni extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('alumni/Alumni_model', 'Alm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('study_program/Study_program_model', 'Spm');
    }

    public function survey_details($s_personal_data_id)
    {
        $mba_survey_answer = $this->Alm->get_survey_answer(['dqa.personal_data_id' => $s_personal_data_id, 'dqa.student_id != ' => NULL]);
        // print('<pre>');var_dump($mba_survey_answer);exit;
        if ($mba_survey_answer) {
            $this->a_page_data['question_list'] = $this->get_question_list('company', $mba_survey_answer[0]->personal_data_id);
            // print('<pre>');var_dump($this->a_page_data['question_list']);exit;
            $this->a_page_data['alumni_data'] = $this->Stm->get_student_filtered(['ds.student_id' => $mba_survey_answer[0]->student_id])[0];
            $this->a_page_data['company_data'] = $mba_survey_answer[0];
            $this->a_page_data['body'] = $this->load->view('alumni/company_survey/survey_details', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function list_survey()
    {
        if ($this->input->is_ajax_request()) {
            $mba_survey_answer = $this->Alm->get_survey_answer();
            print json_encode(['code' => $mba_survey_answer, 'data' => $mba_survey_answer]);
        }else{
            $this->a_page_data['body'] = $this->load->view('company_survey/table/survey_table', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function update_job_data()
    {
        if ($this->input->is_ajax_request()) {
            $s_value = $this->input->post('filled');

            $mbo_student_alumni = $this->General->get_where('dt_student_alumni', ['student_id' => $this->session->userdata('student_id')])[0];
            if ($mbo_student_alumni) {
                $this->Stm->save_student_alumni(['alumni_has_filled_job' => $s_value], $mbo_student_alumni->alumni_id);
                $this->session->set_userdata('has_working', 'yes');
                $a_return = array('code' => 0, 'message' => 'Success');
            }else{
                $a_return = array('code' => 1, 'message' => 'Failed updating your data, please contact IT Team!');
            }

            print json_encode($a_return);exit;
        }
    }

    public function show_job_history_modal()
    {
        $s_personal_data_id = $this->session->userdata('user');
        // $mba_student_alumni_data = $this->General->get_where('dt_student_alumni', ['student_id' => $mbo_profile_data->student_id]);

        // $s_body = '';
        // if (($mba_student_alumni_data) AND ($mba_student_alumni_data[0]->alumni_has_filled_job == 'no')) {
            $this->a_page_data['country_list'] = ($this->General->get_where('ref_country')) ? $this->General->get_where('ref_country') : false;
            $s_body = $this->load->view('alumni/job_history/form/form_create_job', $this->a_page_data, true);
            // $s_body = modules::run('alumni/job_history/form_create_job_history', true);
        // }else{
        //     $s_body = modules::run('alumni/job_history/form_create_job_history', true);
        // }

        // $this->a_page_data['body'] = $s_body;
        $modal_job_history = $this->load->view('alumni/job_history/job_history_modal', $this->a_page_data, true);
        
        return $modal_job_history;
    }

    public function lists_alumni()
    {
        $this->a_page_data['body'] = $this->load->view('alumni_list/list_alumni', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function form_filter_alumni()
    {
        $this->a_page_data['batch'] = $this->General->get_batch();
        $this->a_page_data['ref_program'] = $this->Spm->get_program_lists_select(['is_active' => 'yes', 'is_institute' => 'yes']);
		$this->a_page_data['study_program'] = $this->Spm->get_study_program_instititute();
        $this->load->view('alumni_list/form/form_filter_alumni', $this->a_page_data);
    }

    public function list_table_alumni()
    {
        $this->load->view('alumni_list/table/list_alumni', $this->a_page_data);
    }

    public function show_answer($s_personal_data_id)
    {
        $list = $this->get_dikti_question($s_personal_data_id);
        print('<pre>');
        var_dump($list);
    }

    public function lists_tracer()
	{
		if ($this->input->is_ajax_request()) {
            $mba_tracer_lists = $this->Alm->get_alumni_answer_lists();
            $a_return = array('code' => 0, 'data' => $mba_tracer_lists);

            print json_encode($a_return);
        }else {
			$this->a_page_data['body'] = $this->load->view('tracer_study/table/tracer_list', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
        }
	}

    public function question_answer($s_personal_data_id)
	{
        $mbo_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $s_personal_data_id])[0];

        if ($mbo_student_data) {
            $this->a_page_data['userdata'] = $mbo_student_data;
            $this->a_page_data['dikti_question'] = $this->get_dikti_question($s_personal_data_id);
            $this->a_page_data['personal_data_id'] = $s_personal_data_id;
            $this->a_page_data['body'] = $this->load->view('tracer_study/table/question_answer_alumni', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }else{
            show_404();
        }
	}

    public function list_questions()
	{
        $this->a_page_data['user_has_answered'] = false;
        $this->a_page_data['dikti_question'] = $this->get_dikti_question();
        $this->a_page_data['body'] = $this->load->view('tracer_study/form/form_question_alumae', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
	}

    public function tracer_study_question_old()
    {
        $this->a_page_data['body'] = $this->load->view('testimonial/form/form_question_tracer_study', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function show_tracer_study_modal()
    {
        $mba_answer = $this->Alm->get_user_answer($this->session->userdata('user'));
        if (!$mba_answer) {
            if ($this->router->fetch_method() != 'dikti_tracer_study') {
                $this->a_page_data['user_has_answered'] = false;

                $this->a_page_data['dikti_question'] = $this->get_dikti_question();
                $this->a_page_data['body'] = $this->load->view('tracer_study/form/form_question_alumae', $this->a_page_data, true);
                $modal_tracer_study = $this->load->view('tracer_study/tracer_study_modal', $this->a_page_data, true);
                
                return $modal_tracer_study;
            }else{
                return '';
            }
        }
    }

    public function dikti_tracer_study($s_action = false)
    {
        // $mba_user_has_answered = $this->Alm->get_user_answer($this->session->userdata('user'));

        $this->a_page_data['user_has_answered'] = (($s_action) AND ($s_action == 'filling')) ? false : $this->Alm->get_user_answer($this->session->userdata('user'));

        $this->a_page_data['dikti_question'] = $this->get_dikti_question();
        $this->a_page_data['body'] = $this->load->view('tracer_study/form/form_question_alumae', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function get_dikti_question($mbs_personal_data_id_answer = false)
    {
        $mba_dikti_question = $this->Alm->get_dikti_question(array(
            'parent_question_id' => NULL
        ));

        if ($mba_dikti_question) {
            foreach ($mba_dikti_question as $o_question) {
                $mba_have_child_question = $this->Alm->get_dikti_question(array(
                    'parent_question_id' => $o_question->question_id
                ));

                if ($mba_have_child_question) {
                    foreach ($mba_have_child_question as $o_child_questions) {
                        $mba_question_child_choice = $this->Alm->get_dikti_question_choice(array(
                            'question_id' => $o_child_questions->question_id
                        ));

                        if ($mba_question_child_choice AND $mbs_personal_data_id_answer) {
                            foreach ($mba_question_child_choice as $o_child_choice) {
                                $mbo_question_choice_answer = $this->Alm->get_dikti_choice_answer($mbs_personal_data_id_answer, [
                                    'question_section_id' => $o_child_choice->question_choice_id
                                ])[0];
                                
                                $o_child_choice->answer_data = $mbo_question_choice_answer;
                            }
                        }
    
                        $o_child_questions->question_choices = $mba_question_child_choice;
                    }
                    $o_question_choices = false;
                }else{
                    $mba_question_choice = $this->Alm->get_dikti_question_choice(array(
                        'question_id' => $o_question->question_id
                    ));

                    if ($mba_question_choice AND $mbs_personal_data_id_answer) {
                        foreach ($mba_question_choice as $o_child_choice) {
                            $mbo_question_choice_answer = $this->Alm->get_dikti_choice_answer($mbs_personal_data_id_answer, [
                                'question_section_id' => $o_child_choice->question_choice_id
                            ])[0];
                            
                            $o_child_choice->answer_data = $mbo_question_choice_answer;
                        }
                    }

                    $o_question_choices = $mba_question_choice;
                }
                $o_question->question_child = $mba_have_child_question;
                $o_question->question_choices = $o_question_choices;
            }
        }

        return $mba_dikti_question;
    }

    public function test_array()
    {
        print('<pre>');
        var_dump($this->router->fetch_method());
    }

    public function submit_dikti_tracer_study()
    {
        if ($this->input->is_ajax_request()) {
            $a_post_data = $this->input->post();
            $s_personal_data_id = $this->session->userdata('user');

            if (count($a_post_data) > 0) {
                $a_prep_data = [];
                $a_question_id_required = [];
                $mba_question_required = $this->General->get_where('dikti_questions', ['is_required' => 'TRUE', 'question_type' => 'alumni']);

                if ($mba_question_required) {
                    foreach ($mba_question_required as $o_question) {
                        if (!in_array($o_question->question_id, $a_question_id_required)) {
                            array_push($a_question_id_required, $o_question->question_id);
                        }
                    }
                }
                
                foreach ($a_post_data as $key => $value_question) {
                    $mbo_question_data = $this->General->get_where('dikti_questions', ['question_id' => $key])[0];
                    $mbo_question_input_data = $this->General->get_where('dikti_question_choices', ['dikti_input_code' => $key])[0];
                    if ($mbo_question_data) {
                        if (array_search($key, $a_question_id_required) !== false) {
                            unset($a_question_id_required[array_search($key, $a_question_id_required)]);
                        }
                        
                        if (is_array($value_question)) {
                            foreach ($value_question as $key_multiple => $value_multiple) {
                                $a_data = [
                                    'answer_id' => $this->uuid->v4(),
                                    'personal_data_id' => $s_personal_data_id,
                                    'question_id' => $key,
                                    'question_section_id' => $value_multiple,
                                    'answer_content' => NULL,
                                    'date_added' => date('Y-m-d H:i:s')
                                ];

                                array_push($a_prep_data, $a_data);
                            }
                        }else if($mbo_question_input_data){
                            if ($value_question != '') {
                                $a_data = [
                                    'answer_id' => $this->uuid->v4(),
                                    'personal_data_id' => $s_personal_data_id,
                                    'question_id' => $mbo_question_input_data->question_id,
                                    'question_section_id' => $mbo_question_input_data->question_choice_id,
                                    'answer_content' => $value_question,
                                    'date_added' => date('Y-m-d H:i:s')
                                ];
    
                                array_push($a_prep_data, $a_data);
                            }
                        }else{
                            $a_data = [
                                'answer_id' => $this->uuid->v4(),
                                'personal_data_id' => $s_personal_data_id,
                                'question_id' => $key,
                                'question_section_id' => $value_question,
                                'answer_content' => NULL,
                                'date_added' => date('Y-m-d H:i:s')
                            ];

                            array_push($a_prep_data, $a_data);
                        }
                        
                    }else if($mbo_question_input_data){
                        if ($value_question != '') {
                            $a_prep_data = array_values($a_prep_data);
                            $b_has_filled = false;

                            if (array_search($mbo_question_input_data->question_id, $a_question_id_required) !== false) {
                                unset($a_question_id_required[array_search($mbo_question_input_data->question_id, $a_question_id_required)]);
                            }
                            
                            if (!is_null($mbo_question_input_data->dikti_choice_code)) {
                                foreach ($a_prep_data as $key => $data_value) {
                                    if ($mbo_question_input_data->question_choice_id == $data_value['question_section_id']) {
                                        $a_prep_data[$key]['answer_content'] = $value_question;
                                        $b_has_filled = true;
                                    }
                                }
                            }
                            
                        }
                    }
                }

                if (count($a_question_id_required) > 0) {
                    $a_return = ['code' => 1, 'message' => 'questions marked with (<span class="text-danger">*</span>) are mandatory', 'data' => $a_question_id_required];
                    // print('<pre>');var_dump($a_return);exit;
                }
                else{
                    $a_prep_data = array_values($a_prep_data);
                    
                    if (count($a_prep_data) > 0) {
                        $this->Alm->remove_personal_data_dikti_tracer_study($s_personal_data_id);

                        $i_count = 0;
                        foreach ($a_prep_data as $o_data) {
                            $saved_data = $this->Alm->submit_dikti_tracer_study($o_data);
                            if ($saved_data) {
                                $i_count++;
                            }
                        }

                        if ($i_count > 0) {
                            $a_return = ['code' => 0, 'message' => 'Success!'];
                        }else{
                            $a_return = ['code' => 1, 'message' => 'No data saved!'];
                        }
                    }else{
                        $a_return = ['code' => 1, 'message' => 'No data processing!'];
                    }
                }
            }else{
                $a_return = ['code' => 1, 'message' => 'No data sending!'];
            }

            print json_encode($a_return);exit;
        }
    }

    public function get_question_list($s_question_type = 'alumni', $s_personal_data_id = false)
    {
        $mba_question = $this->Alm->get_dikti_question(array(
            'parent_question_id' => NULL
        ), $s_question_type);

        if ($mba_question) {
            foreach ($mba_question as $o_question) {
                $mba_have_child_question = $this->Alm->get_dikti_question(array(
                    'parent_question_id' => $o_question->question_id
                ), $s_question_type);

                if ($mba_have_child_question) {
                    foreach ($mba_have_child_question as $o_child_questions) {
                        $mba_question_child_choice = $this->Alm->get_dikti_question_choice(array(
                            'question_id' => $o_child_questions->question_id
                        ));

                        if ($mba_question_child_choice) {
                            foreach ($mba_question_child_choice as $o_child_choice) {
                                $o_child_choice->answer_data = false;
                                if ($s_personal_data_id) {
                                    $mbo_question_choice_answer = $this->Alm->get_dikti_choice_answer($s_personal_data_id, [
                                        'question_section_id' => $o_child_choice->question_choice_id
                                    ]);
                                    
                                    $o_child_choice->answer_data = ($mbo_question_choice_answer) ? $mbo_question_choice_answer[0] : false;
                                }
                            }
                        }
    
                        $o_child_questions->question_choices = $mba_question_child_choice;
                    }
                    $o_question_choices = false;
                }else{
                    $mba_question_choice = $this->Alm->get_dikti_question_choice(array(
                        'question_id' => $o_question->question_id
                    ));

                    if ($mba_question_choice) {
                        foreach ($mba_question_choice as $o_child_choice) {
                            $o_child_choice->answer_data = false;
                            if ($s_personal_data_id) {
                                $mbo_question_choice_answer = $this->Alm->get_dikti_choice_answer($s_personal_data_id, [
                                    'question_section_id' => $o_child_choice->question_choice_id
                                ]);
                                
                                $o_child_choice->answer_data = ($mbo_question_choice_answer) ? $mbo_question_choice_answer[0] : false;
                            }
                        }
                    }

                    $o_question_choices = $mba_question_choice;
                }
                $o_question->question_child = $mba_have_child_question;
                $o_question->question_choices = $o_question_choices;
            }
        }

        return $mba_question;
    }

    public function generate_tracer_report_kemdikbud()
    {
        $mba_tracer_answer = $this->Alm->get_alumni_answer_lists();

        if ($mba_tracer_answer) {
            $s_template_path = APPPATH.'uploads/templates/alumni/master2019.xls';
            $s_file_name = 'master_report_'.date('d-M-Y');
            $s_filename = $s_file_name.'.xls';

            $s_file_path = APPPATH."uploads/alumni/tracer_study/report/".date('Y')."/".date('M')."/";
            $s_filedir = $s_file_path.$s_filename;
            
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            if (copy($s_template_path, $s_filedir)) {
                $o_spreadsheet = IOFactory::load($s_filedir);
                $o_sheet = $o_spreadsheet->getSheetByName('Sheet1');
                $i_row = 2;
                
                foreach ($mba_tracer_answer as $o_tracer) {
                    $c_col_question = 'H';
                    $mbo_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $o_tracer->personal_data_id])[0];

                    if (($mbo_student_data) AND ($mbo_student_data->student_email != 'firstname.lastname@stud.iuli.ac.id') AND (!is_null($mbo_student_data->graduated_year_id))) {
                        $s_study_program_id = (!is_null($mbo_student_data->study_program_main_id)) ? $mbo_student_data->study_program_main_id : $mbo_student_data->study_program_id;
                        $mbo_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $s_study_program_id])[0];

                        $o_sheet->setCellValue('A'.$i_row, '041058');
                        $o_sheet->setCellValue('B'.$i_row, $mbo_study_program_data->dikti_code);
                        $o_sheet->setCellValue('C'.$i_row, $mbo_student_data->student_number);
                        $o_sheet->setCellValue('D'.$i_row, $mbo_student_data->personal_data_name);
                        $o_sheet->setCellValue('E'.$i_row, $mbo_student_data->personal_data_cellular);
                        $o_sheet->setCellValue('F'.$i_row, $mbo_student_data->student_alumni_email);
                        $o_sheet->setCellValue('G'.$i_row, $mbo_student_data->graduated_year_id);
                        
                        do {
                            $s_dikti_code = $o_sheet->getCell($c_col_question.'1')->getValue();
                            
                            $s_type = 'input';
                            $mbo_question_answer = $this->Alm->get_question_answer([
                                'dqa.personal_data_id' => $o_tracer->personal_data_id,
                                'dqc.dikti_input_code' => $s_dikti_code
                            ])[0];
                            
                            if (!$mbo_question_answer) {
                                $mbo_question_answer = $this->Alm->get_question_answer([
                                    'dqa.personal_data_id' => $o_tracer->personal_data_id,
                                    'dqc.dikti_choice_code' => $s_dikti_code
                                ])[0];
                                $s_type = 'choice';
                            }

                            if ($mbo_question_answer) {
                                $s_value = $mbo_question_answer->answer_content;
                                if ($s_type == 'choice') {
                                    $s_value = $mbo_question_answer->question_choice_value;
                                }

                                $s_value = (is_null($s_value)) ? '' : $s_value;
                                if (($mbo_question_answer->question_id == 'f13') AND ($s_value == '')) {
                                    $s_value = '0';
                                }
                                $o_sheet->setCellValue($c_col_question.$i_row, $s_value);
                            }

                            $c_col_question++;
                        } while ($o_sheet->getCell($c_col_question.'1')->getValue() != '');
                        $i_row++;

                    }
                }
                
                // print('<pre>');
                // var_dump($a_text);

                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xls');
                $o_writer->save($s_filedir);
                $o_spreadsheet->disconnectWorksheets();
                unset($o_spreadsheet);

                // $a_path_info = pathinfo($s_filedir);
                // $s_file_ext = $a_path_info['extension'];
                // header('Content-Disposition: attachment; filename='.urlencode($s_filename));
                // readfile( $s_filedir );
                // exit;
                $a_return = array('code' => 0, 'file_name' => $s_filename);
            }else{
                $a_return = array('code' => 1, 'message' => 'Failed generate data!');
            }
            // print($s_filename);
        }else{
            $a_return = array('code' => 1, 'message' => 'No answer data!');
        }
        
        return $a_return;
    }
}
