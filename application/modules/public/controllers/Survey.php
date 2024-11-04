<?php
class Survey extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('alumni/Alumni_model', 'Alm');
        $this->load->model('admission/International_office_model', 'Iom');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('institution/Institution_model', 'Inm');
    }

    public function tracer_study_sample() {
        $this->load->model('alumni/Alumni_model', 'Alm');
        $this->a_page_data['user_has_answered'] = false;
        $this->a_page_data['dikti_question'] = $this->get_dikti_question();
        $this->a_page_data['body'] = $this->load->view('alumni/tracer_study/form/form_question_alumae', $this->a_page_data, true);
        $this->load->view('layout_ext', $this->a_page_data);
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

    public function send_result($s_student_id) {
        $mba_exchange_data = $this->Iom->get_international_data([
            'ex.student_id' => $s_student_id
        ]);

        if ($mba_exchange_data) {
            $o_exchange_data = $mba_exchange_data[0];
            $s_dir = APPPATH.'uploads/international_office/survey/exchange_student/'.$o_exchange_data->academic_year_id.'/';
            $s_filename = 'Exchange-Survey-'.str_replace(' ', '_', convert_accented_characters($o_exchange_data->personal_data_name)).'.pdf';
            if (file_exists($s_dir.$s_filename)) {
                $s_email_body = <<<TEXT
Dear team,
new student exchange has filled out the survey.
TEXT;
                $this->email->from('employee@company.ac.id');
                $this->email->to(['employee@company.ac.id']);
                $this->email->cc(['employee@company.ac.id']);
                $this->email->bcc(['employee@company.ac.id']);
                $this->email->subject('Student Survey Exchange Program Submission');
		        $this->email->message($s_email_body);
                $this->email->attach($s_dir.$s_filename);
                $this->email->send();
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    public function generate_pdf($s_personal_data_id) {
        $mba_exchange_data = $this->Iom->get_international_data([
            'st.personal_data_id' => $s_personal_data_id
        ]);

        if ($mba_exchange_data) {
            $o_exchange_data = $mba_exchange_data[0];
            $mba_question_list = $this->Alm->get_question_list('exchange', $s_personal_data_id);
            $this->a_page_data['exchange_data'] = $o_exchange_data;
            $this->a_page_data['question_list'] = $mba_question_list;
            $s_html = $this->load->view('exchange_survey/form/form_print', $this->a_page_data, true);

            // print($s_html);exit;
            $mpdf = new \Mpdf\Mpdf([
                'default_font_size' => 9,
                'default_font' => 'sans_fonts',
                'mode' => 'utf-8',
                'format' => 'A4-P',
                'setAutoTopMargin' => 'stretch',
                'setAutoBottomMargin' => 'stretch'
            ]);

            $s_filename = 'Exchange-Survey-'.str_replace(' ', '_', convert_accented_characters($o_exchange_data->personal_data_name)).'.pdf';
            $s_header_file = '<img src="' . base_url() . 'assets/img/header_of_file.jpg"/>';
            $s_footer_file = '<img src="' . base_url() . 'assets/img/footer_of_letter.png"/>';
            $mpdf->SetHTMLHeader($s_header_file);
            $mpdf->SetHTMLFooter($s_footer_file);
            $mpdf->WriteHTML($s_html);
            // print($s_filename);exit;
            $s_dir = APPPATH.'uploads/international_office/survey/exchange_student/'.$o_exchange_data->academic_year_id.'/';
            if(!file_exists($s_dir)){
                mkdir($s_dir, 0777, TRUE);
            }
            $mpdf->Output($s_dir.$s_filename, 'F');
            return true;

            // $s_mime = mime_content_type($s_dir.$s_filename);
            // $a_path_info = pathinfo($s_dir.$s_filename);
            // $s_file_ext = $a_path_info['extension'];
            // header("Content-Type: ".$s_mime);
            // readfile( $s_dir.$s_filename );
            // exit;
        }
        else {
            return false;
        }
        // print($s_formbody);
    }

    public function submit_exchange_survey() {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('input_student_id', 'Student Name', 'required|trim');
            $this->form_validation->set_rules('input_homeuniv', 'Home University', 'required|trim');
            $this->form_validation->set_rules('input_prodi', 'Study Program', 'required|trim');

            if ($this->form_validation->run()) {
                $s_student_id = set_value('input_student_id');
                $mba_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id]);
                $a_answerlist = [];
                $a_message_error = [];

                $mba_question_list = $this->Alm->get_question_list('exchange');
                if ($mba_question_list) {
                    foreach ($mba_question_list as $o_question) {
                        if ($o_question->question_child) {
                            foreach ($o_question->question_child as $o_question_child) {
                                $s_input_name = $o_question_child->question_id;
                                
                                if ($o_question_child->have_description) {
                                    $a_input_data = $this->input->post($o_question_child->question_id);
                                    if (is_array($a_input_data)) {
                                        // if ($a_input_data['is_double'] !== null) {
                                        //     # code...
                                        // }
                                        if (array_key_exists('is_double', $a_input_data)) {
                                            unset($a_input_data['is_double']);
                                            // if (count($a_input_data) != 2) {
                                                // print('post<pre>');var_dump($a_input_data);exit;
                                                // foreach ($a_input_data as $key => $value) {
                                                    // if (empty($value)) {
                                                    //     # code...
                                                    // }
                                                // }
                                                // array_push($a_message_error, "Please explain and choose ".$o_question_child->question_name);
                                            // }
                                            // else {
                                                foreach ($a_input_data as $key => $value) {
                                                    if ((!empty($value)) AND ($value != '')) {
                                                        $a_answer_data = [
                                                            'answer_id' => $this->uuid->v4(),
                                                            'personal_data_id' => $mba_student_data[0]->personal_data_id,
                                                            'question_id' => $o_question_child->question_id,
                                                            'question_section_id' => $key,
                                                            'answer_content' => (empty($value)) ? NULL : $value,
                                                            'date_added' => date('Y-m-d H:i:s')
                                                        ];
                                                        array_push($a_answerlist, $a_answer_data);
                                                    }
                                                }
                                            // }
                                        }
                                        else {
                                            foreach ($a_input_data as $key => $value) {
                                                if ((!empty($value)) AND ($value != '')) {
                                                    $a_answer_data = [
                                                        'answer_id' => $this->uuid->v4(),
                                                        'personal_data_id' => $mba_student_data[0]->personal_data_id,
                                                        'question_id' => $o_question_child->question_id,
                                                        'question_section_id' => $key,
                                                        'answer_content' => (empty($value)) ? NULL : $value,
                                                        'date_added' => date('Y-m-d H:i:s')
                                                    ];
                                                    array_push($a_answerlist, $a_answer_data);
                                                }
                                            }
                                        }
                                    }
                                }
                                else {
                                    $s_input_data = $this->input->post($o_question_child->question_id);
                                    if (!empty($s_input_data)) {
                                        $a_answer_data = [
                                            'answer_id' => $this->uuid->v4(),
                                            'personal_data_id' => $mba_student_data[0]->personal_data_id,
                                            'question_id' => $o_question_child->question_id,
                                            'question_section_id' => $s_input_data,
                                            'answer_content' => NULL,
                                            'date_added' => date('Y-m-d H:i:s')
                                        ];
                                        array_push($a_answerlist, $a_answer_data);
                                    }
                                }
                            }
                        }
                    }
                }

                if (count($a_message_error) > 0) {
                    $a_return = ['code' => 2, 'message' => '<li>'.implode('</li><li>', $a_message_error).'</li>'];
                }
                else if (count($a_answerlist) == 0) {
                    $a_return = ['code' => 1, 'message' => 'No surveys saved'];
                }
                else {
                    // print('<pre>');var_dump($a_answerlist);exit;
                    $mba_institution_data = $this->General->get_where('ref_institution', ['institution_name' => set_value('input_homeuniv')]);
                    if ($mba_institution_data) {
                        $s_institution_id = $mba_institution_data[0]->institution_id;
                    }
                    else {
                        $s_institution_id = $this->uuid->v4();
                        $a_institution_data = [
                            'institution_id' => $s_institution_id,
                            'institution_name' => set_value('input_homeuniv'),
                            'institution_type' => 'university',
                            'institution_is_international' => 'yes'
                        ];

                        $this->Inm->insert_institution($a_institution_data);
                    }

                    $mba_exchange_data = $this->General->get_where('dt_student_exchange', ['student_id' => $s_student_id]);
                    $a_exchange_data = [
                        'program_id' => '7',
                        'institution_id' => $s_institution_id,
                        'study_program_name' => set_value('input_prodi')
                    ];

                    if ($mba_exchange_data) {
                        $this->Iom->submit_student_abroad($a_exchange_data, ['exchange_id' => $mba_exchange_data[0]->exchange_id]);
                    }
                    else {
                        $a_exchange_data['exchange_id'] = $this->uuid->v4();
                        $a_exchange_data['student_id'] = $s_student_id;
                        $a_exchange_data['exchange_type'] = 'in';

                        $this->Iom->submit_student_abroad($a_exchange_data);
                    }

                    $mba_student_answered = $this->Alm->get_alumni_answer_lists(['ds.student_id' => $s_student_id]);
                    if ($mba_student_answered) {
                        $this->Alm->remove_personal_data_dikti_tracer_study($mba_student_data[0]->personal_data_id);
                    }

                    $this->Alm->submit_dikti_survey_batch($a_answerlist);
                    $this->generate_pdf($mba_student_data[0]->personal_data_id);
                    $this->send_result($s_student_id);
                    $a_return = ['code' => 0, 'message' => 'Success', 'data' => $a_answerlist];
                }
            }
            else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }

    public function get_institutions()
	{
		if ($this->input->is_ajax_request()) {
            $this->load->model('institution/Institution_model', 'Institution');
			$s_instution_name = $this->input->post('term');
			$a_clause = array('ri.institution_type' => 'university');

			// var_dump($is_university);exit;
			$mba_institution_data = $this->Institution->get_institution_data($a_clause, $s_instution_name, true);

			print json_encode(array('code' => 0, 'data' => $mba_institution_data));
		}
	}

    public function get_student_exchange() {
        if ($this->input->is_ajax_request()) {
            $s_term_student_name = $this->input->post('student_name');
            $s_student_id = $this->input->post('student_id');
            $s_get_type = $this->input->post('filter_type');
            $a_result = [];
            $i_limit = 10;

            $mba_student_data = $this->Stm->get_student_by_name($s_term_student_name);
            if ($mba_student_data) {
                foreach ($mba_student_data as $o_student) {
                    if ($i_limit > 0) {
                        $mba_student_exchange_data = $this->Iom->get_international_data([
                            'st.student_id' => $o_student->student_id
                        ]);
    
                        if ($mba_student_exchange_data) {
                            array_push($a_result, [
                                'student_name' => $o_student->personal_data_name,
                                'student_id' => $o_student->student_id,
                                'exchange_id' => ($mba_student_exchange_data) ? $mba_student_exchange_data[0]->exchange_id : '',
                                'institution_id' => ($mba_student_exchange_data) ? $mba_student_exchange_data[0]->institution_id : '',
                                'institution_name' => ($mba_student_exchange_data) ? $mba_student_exchange_data[0]->institution_name : '',
                                'study_program_name' => ($mba_student_exchange_data) ? $mba_student_exchange_data[0]->study_program_name : '',
                                'finance_year_id' => $o_student->academic_year_id.'/'.(intval($o_student->academic_year_id) + 1)
                            ]);
                            $i_limit--;
                        }
                    }
                }
            }

            print json_encode($a_result);exit;
        }
    }

    public function student_exchange_survey()
    {
        // $mba_question_list = $this->Alm->get_dikti_question([
        //     'parent_question_id' => NULL,
        //     'dq.question_id' => $s_question_id
        // ]);

        $mba_question_list = $this->Alm->get_question_list('exchange');
        // print('<pre>');var_dump($mba_question_list);exit;
        $this->a_page_data['question_list'] = $mba_question_list;
        $this->a_page_data['title_site'] = 'Survey';
        $this->a_page_data['form_survey'] = $this->load->view('exchange_survey/form/survey_form', $this->a_page_data, true);
        $this->a_page_data['body'] = $this->load->view('exchange_survey/survey', $this->a_page_data, true);
        $this->load->view('layout_public', $this->a_page_data);
    }
}
