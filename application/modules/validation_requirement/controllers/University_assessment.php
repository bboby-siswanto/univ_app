<?php
class university_assessment extends App_core{
    public $assessment_id;
    function __construct()
    {
        parent::__construct();
        $this->load->model('Validation_requirement_model', 'Vm');
        $this->load->model('Assessment_model', 'Asm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->assessment_id = '6300dd69-b415-11ed-9d77-52540039e1c3';
    }

    public function get_responden_result()
    {
        // if ($this->input->is_ajax_request()) {
            $mba_study_program_list = $this->Spm->get_study_program(false, false);
            if ($mba_study_program_list) {
                foreach ($mba_study_program_list as $o_study_program) {
                    $mba_assessment_result = $this->Asm->get_result([
                        'qr.assessment_id' => $this->assessment_id,
                        'st.study_program_id' => $o_study_program->study_program_id
                    ], true);
                    $o_study_program->total_responden = ($mba_assessment_result) ? count($mba_assessment_result) : 0;
                }
            }

            print json_encode(['data' => $mba_study_program_list]);
        // }
    }

    public function get_question_result()
    {
        if ($this->input->is_ajax_request()) {
            $s_study_program_id = $this->input->post('study_program_id');
            $s_assessment_id = $this->input->post('assessment_id');
            $mba_assessment_data = $this->Asm->get_list_assessment(['ass.assessment_id' => $s_assessment_id]);
            $s_assessment_purpose = $mba_assessment_data[0]->assessment_purpose;
            $a_assessment_purpose = explode(';', $s_assessment_purpose);
            $mba_question = $this->Asm->get_question_list($s_assessment_id);

            if ($mba_question) {
                $mba_option_list = $this->Asm->get_option_list($s_assessment_id);
                if (is_array($s_study_program_id)) {
                    $s_study_program_id = ['st.study_program_id' => $s_study_program_id];
                }
                foreach ($mba_question as $o_question) {
                    if ($mba_option_list) {
                        foreach ($mba_option_list as $o_option) {
                            $a_clause = [
                                'qr.assessment_id' => $s_assessment_id,
                                'qr.question_id' => $o_question->question_id,
                                'qr.assessment_option_id' => $o_option->assessment_option_id,
                            ];
                            $a_prodi_filter = false;
                            if (!is_array($s_study_program_id)) {
                                $a_clause['st.study_program_id'] = $s_study_program_id;
                            }
                            else if ((is_array($s_study_program_id)) AND (count($s_study_program_id) > 0)) {
                                $a_prodi_filter = $s_study_program_id;
                            }

                            $mba_assessment_result = false;
                            if (in_array('student', $a_assessment_purpose)) {
                                $mba_assessment_result = $this->Asm->get_result($a_clause, false, $a_prodi_filter);
                            }
                            else {
                                $mba_assessment_result = $this->Asm->get_assessment_result([
                                    'qr.assessment_id' => $s_assessment_id,
                                    'qr.question_id' => $o_question->question_id,
                                    'qr.assessment_option_id' => $o_option->assessment_option_id
                                ]);
                            }
                            
                            $s_option_id = $o_option->assessment_option_id;
                            $s_result_option = 'result_option_'.$s_option_id;
                            $o_question->$s_result_option = ($mba_assessment_result) ? count($mba_assessment_result) : 0;
                        }
                    }
                }
            }

            print json_encode(['data' => $mba_question]);exit;
        }
    }

    function assessment_result() {
        $mba_list_assessment = $this->Asm->get_list_assessment();
        $s_assessment_id = '6300dd69-b415-11ed-9d77-52540039e1c3';
        $this->a_page_data['list_assessment'] = $mba_list_assessment;
        $this->a_page_data['assessment_id'] = $s_assessment_id;
        $this->a_page_data['study_program_list'] = $this->Spm->get_study_program(false, false);
        $this->a_page_data['option_list'] = $this->Asm->get_option_list($s_assessment_id);
        $this->a_page_data['question_list'] = $this->Asm->get_question_list($s_assessment_id, [
            'question_status' => 'active'
        ]);
        $this->a_page_data['body'] = $this->load->view('campuss_assessment/result_page', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function responden_view() {
        $this->a_page_data['body'] = $this->load->view('campuss_assessment/responden_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function result_view($s_assessment_id, $s_view_mode = 'table') {
        $result_view = '';

        $mba_assessment_data = $this->Asm->get_list_assessment(['ass.assessment_id' => $s_assessment_id]);
        $s_assessment_purpose = $mba_assessment_data[0]->assessment_purpose;
        $a_assessment_purpose = explode(';', $s_assessment_purpose);

        $this->a_page_data['assessment_id'] = $s_assessment_id;
        $this->a_page_data['assessment_purpose'] = $a_assessment_purpose;
        $this->a_page_data['study_program_list'] = $this->Spm->get_study_program(false, false);
        $this->a_page_data['option_list'] = $this->Asm->get_option_list($s_assessment_id);
        $this->a_page_data['question_list'] = $this->Asm->get_question_list($s_assessment_id, [
            'question_status' => 'active'
        ]);

        if ($s_view_mode == 'table') {
            $result_view = $this->load->view('campuss_assessment/view_mode/table_result', $this->a_page_data, true);
        }
        return $result_view;
    }

    public function student_satisfaction()
    {
        $s_assessment_id = '6300dd69-b415-11ed-9d77-52540039e1c3';
        $this->a_page_data['assessment_id'] = $s_assessment_id;
        $this->a_page_data['study_program_list'] = $this->Spm->get_study_program(false, false);
        $this->a_page_data['option_list'] = $this->Asm->get_option_list($s_assessment_id);
        $this->a_page_data['question_list'] = $this->Asm->get_question_list($s_assessment_id, [
            'question_status' => 'active'
        ]);
        $this->a_page_data['body'] = $this->load->view('campuss_assessment/assessment_result', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function get_assessment_data() {
        if ($this->input->is_ajax_request()) {
            $s_assessment_id = $this->input->post('ass_id');
            $mba_list_assessment = $this->Asm->get_list_assessment([
                'ass.assessment_id' => $s_assessment_id
            ]);
            $mba_prodi_list = $this->Spm->get_study_program(false, false);
            if ($mba_list_assessment) {
                // $mba_list_assessment = $mba_list_assessment[0];
                foreach ($mba_list_assessment as $o_assessment) {
                    $mba_assessment_option = $this->Asm->get_option_list($o_assessment->assessment_id);
                    $mba_assessment_question = $this->Asm->get_question_list($o_assessment->assessment_id, [
                        'question_status' => 'active'
                    ]);
                    $o_assessment->option_list = $mba_assessment_option;
                    $o_assessment->question_list = $mba_assessment_question;
                    $o_assessment->prodi_list = $mba_prodi_list;
                }
            }

            $s_table_result_page = $this->result_view($s_assessment_id, 'table');
            print json_encode(['data' => $mba_list_assessment, 'table_view' => $s_table_result_page]);
        }
    }

    public function submit_assessment()
    {
        if ($this->input->is_ajax_request()) {
            // print('<pre>');var_dump($this->input->post());exit;
            $s_assessment_id = (!empty($this->input->post('assessment_id'))) ? $this->input->post('assessment_id') : $this->assessment_id;
            $mba_question_list = $this->Asm->get_question_list($s_assessment_id, [
                'question_status' => 'active'
            ]);
            
            if ($mba_question_list) {
                foreach ($mba_question_list as $o_question) {
                    $this->form_validation->set_rules('result_question_'.$o_question->question_id, 'Question Number '.$o_question->question_number, 'required');
                }
            }
            else {
                print json_encode(['code' => 2, 'message' => 'No Question available!']);exit;
            }

            if ($this->form_validation->run()) {
                $a_details_data = [];

                $mba_student_has_filled = $this->Asm->get_result([
                    'qr.personal_data_id' => $this->session->userdata('user'),
                    'dq.assessment_id' => $s_assessment_id
                ]);
                if ($mba_student_has_filled) {
                    $this->Asm->remove_result($this->session->userdata('user'), $s_assessment_id);
                }

                foreach ($mba_question_list as $o_question) {
                    $s_result_question_id = $this->uuid->v4();
                    $s_option_id = set_value('result_question_'.$o_question->question_id);
                    $mba_option_data = $this->Asm->get_option_list($s_assessment_id, [
                        'assessment_option_id' => $s_option_id
                    ]);

                    if (!$mba_option_data) {
                        print json_encode(['code' => 2, 'message' => 'invalid choice!']);exit;
                    }

                    $a_result_question = [
                        'assessment_id' => $s_assessment_id,
                        'question_result_id' => $s_result_question_id,
                        'personal_data_id' => $this->session->userdata('user'),
                        'question_id' => $o_question->question_id,
                        'result_text' => $mba_option_data[0]->option_name_eng,
                        'assessment_option_id' => $s_option_id,
                        'result_value' => $mba_option_data[0]->option_value,
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    
                    $this->Asm->submit_result($a_result_question);
                }
                $a_return = ['code' => 0, 'message' => "Success!"];
            }
            else {
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }
}
