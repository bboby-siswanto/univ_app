<?php
class Entrance_test extends App_core
{
    function __construct()
    {
        parent::__construct('admission');
        $this->load->model('admission/Entrance_test_model', 'Etm');
        // $this->load->model('exam/Entrance_test_model', 'xEtm');
        $this->load->model('student/Student_model', 'Stm');
    }

    public function test()
    {
        // $s_personal_data_id = $this->session->userdata('user');
        // $mbo_employee_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $s_personal_data_id, 'em.status' => 'active'))[0];
        // if ($mbo_employee_data) {
        //     $mba_employee_roles = $this->Dm->get_employee_list_roles(array('ep.employee_id' => $mbo_employee_data->employee_id));
        //     print('<pre>');
        //     var_dump($mba_employee_roles);
        //     // var_dump(modules::run('devs/devs_employee/employee_permission'));
        // }
    }

    public function set_pending_status() {
        if ($this->input->is_ajax_request()) {
            $s_exam_candidate_id = $this->input->post('exam_candidate_id');
            if ($reset_data = $this->Etm->save_candidate_exam([
                'candidate_exam_status' => 'PENDING'
            ], [
                'exam_candidate_id' => $s_exam_candidate_id
            ])) {
                $a_return = array('code' => 0, 'message' => 'Success');
            }else{
                $a_return = array('code' => 1, 'message' => 'Error reset data');
            }

            print json_encode($a_return);
        }
    }

    public function reset_data()
    {
        if ($this->input->is_ajax_request()) {
            $s_exam_candidate_id = $this->input->post('exam_candidate_id');
            if ($reset_data = $this->Etm->reset_data($s_exam_candidate_id)) {
                $a_return = array('code' => 0, 'message' => 'Success');
            }else{
                $a_return = array('code' => 1, 'message' => 'Error reset data');
            }

            print json_encode($a_return);
        }
    }

    public function send_token()
    {
        if ($this->input->is_ajax_request()) {
            $s_exam_candidate_id = $this->input->post('exam_candidate_id');
            $sending = $this->send_mail_participant($s_exam_candidate_id);
            if ($sending) {
                $a_return = array('code' => 0, 'message' => 'Success');
            }else{
                $a_return = array('code' => 1, 'message' => 'Cannt send email to participant now');
            }

            print json_encode($a_return);
        }
    }

    public function participant_result($s_exam_candidate_id)
    {
        $s_target = 'pmb';
        $mbo_participant_data = $this->Etm->get_candidate_exam(array('exam_candidate_id' => $s_exam_candidate_id), 'pmb')[0];
        if (!$mbo_participant_data) {
            $s_target = 'event';
            $mbo_participant_data = $this->Etm->get_candidate_exam(array('exam_candidate_id' => $s_exam_candidate_id), 'event')[0];
        }
        $mba_participant_result = $this->Etm->get_participant_answer(array('ca.exam_candidate_id' => $s_exam_candidate_id))[0];
        // var_dump($mbo_participant_data);exit;
        if ($mba_participant_result) {
            $this->a_page_data['result'] = true;
            
            $mba_section_data = $this->Etm->get_section();
            foreach ($mba_section_data as $section) {
                $i_counter_correct = 0;
                $mba_candidate_section_answer = $this->Etm->get_participant_answer(array('ca.exam_candidate_id' => $mbo_participant_data->exam_candidate_id, 'eq.exam_section_id' => $section->exam_section_id));
                if ($mba_candidate_section_answer) {
                    foreach ($mba_candidate_section_answer as $answer) {
                        if ($answer->option_this_answer == 'TRUE') {
                            $i_counter_correct++;
                        }
                    }
                }
                $section->correct_answer = $i_counter_correct;
            }

            if ($s_target == 'event') {
                $mbo_participant_data->personal_data_name = $mbo_participant_data->booking_name;
                $mbo_participant_data->personal_data_email = $mbo_participant_data->booking_email;
            }
            
            
            $this->a_page_data['result_section'] = $mba_section_data;
            $this->a_page_data['result_sess_1'] = $this->result_session_1($s_exam_candidate_id);
            $this->a_page_data['result_sess_2A'] = $this->result_session_2A($s_exam_candidate_id);
            $this->a_page_data['result_sess_2B'] = $this->result_session_2B($s_exam_candidate_id);
            $this->a_page_data['result_sess_3'] = $this->result_session_3($s_exam_candidate_id);
            $this->a_page_data['participant_data'] = $mbo_participant_data;
            $this->a_page_data['body'] = $this->load->view('entrance_test/participant_result', $this->a_page_data, true);
        }else{
            $this->a_page_data['result'] = false;
            $this->a_page_data['body'] = $this->load->view('entrance_test/participant_result', $this->a_page_data, true);
        }
        $this->load->view('layout', $this->a_page_data);
    }

    public function extra_time()
    {
        if ($this->input->is_ajax_request()) {
            $s_time = $this->input->post('extra_minutes');
            // $s_section_id = $this->input->post('option_time');
            $s_exam_candidate_id = $this->input->post('exam_candidate_id');

            $mbo_candidate_exam_data = $this->Etm->get_candidate_exam(array('ec.exam_candidate_id' => $s_exam_candidate_id))[0];
            $mba_participant_section = $this->Etm->get_candidate_section(array('ecs.exam_candidate_id' => $s_exam_candidate_id, 'section_has_filled' => 'FALSE'))[0];
            $s_exam_end_time = $mbo_candidate_exam_data->end_time;
            $s_exam_end_time = new DateTime($s_exam_end_time);
            $s_exam_end_time->add(new DateInterval('PT' . $s_time . 'M'));
            $s_new_time = $s_exam_end_time->format('Y-m-d H:i:s');
            $a_data = array();

            $s_section_end_time = $mba_participant_section->section_time_end;
            $s_section_end_time = new DateTime($s_section_end_time);
            $s_section_end_time->add(new DateInterval('PT' . $s_time . 'M'));
            $s_section_new_time = $s_section_end_time->format('Y-m-d H:i:s');
            // if ($s_section_id == 'all') {
            //     # semua section
            //     $mba_participant_section = $this->Etm->get_candidate_section(array('ecs.exam_candidate_id' => $s_exam_candidate_id));
            // }else{
            //     # section dipilih
            //     $mba_participant_section = $this->Etm->get_candidate_section(array('ecs.exam_candidate_id' => $s_exam_candidate_id, 'exam_section_id' => $s_section_id));
            // }
            // print('<pre>');
            // var_dump($mba_participant_section);exit;

            $save = $this->Etm->save_candidate_exam(array('end_time' => $s_new_time), array('exam_candidate_id' => $s_exam_candidate_id));
            $save_section = $this->Etm->save_candidate_section(array('section_time_end' => $s_section_new_time), array('exam_candidate_id' => $s_exam_candidate_id, 'exam_section_id' => $mba_participant_section->exam_section_id));
            if ($save) {
                $a_return = array('code' => 0, 'message' => 'Success!');
            }else{
                $a_return = array('code' => 1, 'message' => 'Error processing data!');
            }

            print json_encode($a_return);
        }
    }

    public function result_session_1($s_exam_candidate_id)
    {
        $a_question_section_1 = array();
        $mbo_question_section_1 = $this->Etm->get_question(array('exam_section_id' => 1));
        // var_dump($mbo_question_section_1);exit;
        if ($mbo_question_section_1) {
            foreach ($mbo_question_section_1 as $q_1) {
                $a_option = array();
                $mbo_participant_result = $this->Etm->get_participant_answer(array('ca.exam_question_id' => $q_1->exam_question_id, 'ca.exam_candidate_id' => $s_exam_candidate_id))[0];
                // if ($q_1->exam_question_number == 1) {
                //     // var_dump($this->db->last_query());exit;
                //     print('<pre>');
                //     var_dump($mbo_participant_result);exit;
                // }
                if ($mbo_participant_result) {
                    $q_1->o_answer = $mbo_participant_result;
                }else{
                    $q_1->o_answer = 'false';
                }
            }
        }
        // print('<pre>');
        // var_dump($mbo_question_section_1);exit;
        return $mbo_question_section_1;
    }

    public function result_session_2A($s_exam_candidate_id)
    {
        $a_question_id = array();
        $mbo_question_section_2A = $this->Etm->get_question(array('exam_section_id' => 2, 'exam_question_number <=' => '15'));
        if ($mbo_question_section_2A) {
            foreach ($mbo_question_section_2A as $q_2A) {
                $a_option = array();
                $mbo_participant_result = $this->Etm->get_participant_answer(array('ca.exam_question_id' => $q_2A->exam_question_id, 'ca.exam_candidate_id' => $s_exam_candidate_id))[0];
                // var_dump($mbo_participant_result);exit;
                if ($mbo_participant_result) {
                    $q_2A->o_answer = $mbo_participant_result;
                }else{
                    $q_2A->o_answer = 'false';
                }
            }
        }
        // print('<pre>');
        // var_dump($mbo_question_section_2A);

        return $mbo_question_section_2A;
    }
    
    public function result_session_2B($s_exam_candidate_id)
    {
        $mbo_question_section_2B = $this->Etm->get_question(array('exam_section_id' => 2, 'exam_question_number >' => '15'));
        if ($mbo_question_section_2B) {
            foreach ($mbo_question_section_2B as $q_2B) {
                $a_option = array();
                $mbo_participant_result = $this->Etm->get_participant_answer(array('ca.exam_question_id' => $q_2B->exam_question_id, 'ca.exam_candidate_id' => $s_exam_candidate_id))[0];
                if ($mbo_participant_result) {
                    $q_2B->o_answer = $mbo_participant_result;
                }else{
                    $q_2B->o_answer = 'false';
                }
            }
        }

        return $mbo_question_section_2B;
    }

    public function result_session_3($s_exam_candidate_id)
    {
        $a_question_section_1 = array();
        $mbo_question_section = $this->Etm->get_question(array('exam_section_id' => 3));
        // var_dump($mbo_question_section);exit;
        if ($mbo_question_section) {
            foreach ($mbo_question_section as $q_answer) {
                $a_option = array();
                $mbo_participant_result = $this->Etm->get_participant_answer(array('ca.exam_question_id' => $q_answer->exam_question_id, 'ca.exam_candidate_id' => $s_exam_candidate_id))[0];
                // if ($q_1->exam_question_number == 1) {
                //     // var_dump($this->db->last_query());exit;
                //     print('<pre>');
                //     var_dump($mbo_participant_result);exit;
                // }
                if ($mbo_participant_result) {
                    $q_answer->o_answer = $mbo_participant_result;
                }else{
                    $q_answer->o_answer = 'false';
                }
            }
        }
        // print('<pre>');
        // var_dump($mbo_question_section);exit;
        return $mbo_question_section;
    }

    public function period_list()
    {
        if ($this->input->is_ajax_request()) {
            $mba_exam_data = $this->Etm->get_period_exam();
            print json_encode(array('code' => 0, 'data' => $mba_exam_data));
        }else{
            $this->a_page_data['body'] = $this->load->view('entrance_test_view', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function prop_status_token()
    {
        if ($this->input->is_ajax_request()) {
            $s_status = $this->input->post('status');
            $s_exam_candidate_id = $this->input->post('exam_candidate_id');
            $save = $this->Etm->save_candidate_exam(array('candidate_exam_status' => $s_status), array('exam_candidate_id' => $s_exam_candidate_id));
            if ($save) {
                $a_return = array('code' => 0, 'message' => 'Success!');
            }else{
                $a_return = array('code' => 1, 'message' => 'Error processing data!');
            }

            print json_encode($a_return);
        }
    }

    public function generate_token($s_student_id)
    {
        $s_new_token = md5($s_student_id.time());
        $s_new_token = substr($s_new_token, 0, 16);
        $a_new_token = str_split($s_new_token);
        $s_token = '';
        for ($i=0; $i < count($a_new_token); $i++) { 
            if (($i > 0) AND ($i % 4 == 0)) {
                $s_token .= '-';
            }
            $s_token .= $a_new_token[$i];
        }

        if ($s_token != '') {
            return $s_token;
        }else{
            return false;
        }
    }

    public function reset_token()
    {
        if ($this->input->is_ajax_request()) {
            $s_exam_candidate_id = $this->input->post('exam_candidate_id');
            $s_new_token = md5($s_exam_candidate_id.time());
            $s_new_token = substr($s_new_token, 0, 16);
            $a_new_token = str_split($s_new_token);
            $s_token = '';
            for ($i=0; $i < count($a_new_token); $i++) { 
                if (($i > 0) AND ($i % 4 == 0)) {
                    $s_token .= '-';
                }
                $s_token .= $a_new_token[$i];
            }
            if ($s_token != '') {
                $a_data = array('token' => $s_token);
                $save = $this->Etm->save_candidate_exam($a_data, array('exam_candidate_id' => $s_exam_candidate_id));
                if ($save) {
                    $a_return = array('code' => 0, 'message' => 'Success!');
                }else{
                    $a_return = array('code' => 1, 'message' => 'Error processing data!');
                }
            }else{
                $a_return = array('code' => 1, 'message' => 'Error generate new token!');
            }

            print json_encode($a_return);
        }
    }

    public function submit_candidate_exam()
    {
        if ($this->input->is_ajax_request()) {
            $s_exam_id = $this->input->post('exam_id');
            $mba_student_participant = $this->Stm->get_student_personal_data(false, ['register', 'candidate']);
            if ($mba_student_participant) {
                foreach ($mba_student_participant as $participant) {
                    $s_exam_candidate_id = $this->uuid->v4();
                    $s_token = $this->generate_token($s_exam_candidate_id);
                    $s_token = ($s_token != false) ? $s_token : '0';
                    $a_data = array(
                        'exam_candidate_id' => $s_exam_candidate_id,
                        'student_id' => $participant->student_id,
                        'exam_id' => $s_exam_id,
                        'total_question' => '90',
                        'token' => $s_token,
                        'date_added' => date('Y-m-d H:i:s')
                    );

                    $mba_candidate_exam_ready = $this->Etm->get_candidate_exam(array('ec.student_id' => $participant->student_id, 'ec.exam_id' => $s_exam_id))[0];
                    if ($mba_candidate_exam_ready) {
                        unset($a_data['exam_candidate_id']);
                        unset($a_data['token']);
                        unset($a_data['date_added']);
                        $save = $this->Etm->save_candidate_exam($a_data, array('exam_candidate_id' => $mba_candidate_exam_ready->exam_candidate_id));
                    }else{
                        $save = $this->Etm->save_candidate_exam($a_data);
                    }
                }
            }

            print json_encode(array('code' => 0, 'message' => 'finish'));
        }
    }

    public function question_list()
    {
        if ($this->input->is_ajax_request()) {
            $mba_question_data = $this->Etm->get_exam_question();
            if ($mba_question_data) {
                foreach ($mba_question_data as $question) {
                    $s_question_option = '';
                    $mba_question_option = $this->Etm->get_question_option(array('exam_question_id' => $question->exam_question_id));
                    if ($mba_question_option) {
                        $s_question_option = '<ul class="list-unstyled">';
                        foreach ($mba_question_option as $option) {
                            $trim_option = $this->Etm->clean_html($option->exam_question_option_number, $option->question_option_description);
                            $s_question_option .= '<li>'.$trim_option.'</li>';
                            // $s_question_option .= '<li>'.$option->exam_question_option_number.'. '.$option->question_option_description.'</li>';
                        }
                        $s_question_option .= '<ul>';
                        // $s_question_option = implode('</li><li>', $mba_question_option);
                        // $s_question_option = '<ul class="list-unstyled"><li>'.$s_question_option.'</li></ul>';
                    }
                    $question->exam_option = $s_question_option;
                    $question->trim_queston = $this->Etm->clean_html($question->exam_question_number, $question->exam_question_description);
                }
            }
            $data = array('code' => 0, 'data' => $mba_question_data);
            print json_encode($data);
        }else{
            $this->a_page_data['body'] = $this->load->view('entrance_test/question_list', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function form_filter()
    {
        $this->a_page_data['event_list'] = $this->General->get_where('dt_event');
        $this->a_page_data['academic_yar_list'] = $this->General->get_where('dt_academic_year');
        $this->load->view('form/form_filter_participant_entrance_test', $this->a_page_data);
    }

    public function participant_list()
    {
        if ($this->input->is_ajax_request()) {
            // $s_exam_id = $this->input->post('exam_id');
            $s_finance_year_id = $this->input->post('academic_year_id');
            $s_participant_type = $this->input->post('participant_type');
            $s_event_id = $this->input->post('event_key');

            $a_filter = [];

            switch ($s_participant_type) {
                case 'pmb':
                    $a_filter_student = $a_filter;
                    if (!empty($s_finance_year_id)) {
                        $a_filter_student['st.finance_year_id'] = $s_finance_year_id;
                    }

                    $mba_participant_data = $this->Etm->get_candidate_exam($a_filter_student);
                    break;

                case 'event':
                    $a_filter_booking = $a_filter;
                    if (!empty($s_event_id)) {
                        $a_filter_booking['eb.event_id'] = $s_event_id;
                    }

                    $mba_participant_data = $this->Etm->get_candidate_event($a_filter_booking);
                    break;
                
                default:
                    $a_filter_student = $a_filter;
                    if (!empty($s_finance_year_id)) {
                        $a_filter_student['st.finance_year_id'] = $s_finance_year_id;
                    }

                    $mba_participant_data_student = $this->Etm->get_candidate_exam($a_filter_student);
                    $mba_participant_data_student = ($mba_participant_data_student) ? $mba_participant_data_student : [];

                    $a_filter_booking = $a_filter;
                    if (!empty($s_event_id)) {
                        $a_filter_booking['eb.event_id'] = $s_event_id;
                    }

                    $mba_participant_data_booking = $this->Etm->get_candidate_event($a_filter_booking);
                    $mba_participant_data_booking = ($mba_participant_data_booking) ? $mba_participant_data_booking : [];

                    $mba_participant_data = array_merge($mba_participant_data_student, $mba_participant_data_booking);
                    break;
            }

            $a_data = array('code' => 0, 'data' => $mba_participant_data);
            print json_encode($a_data);
        }else{
            // $this->a_page_data['exam_id'] = $s_exam_id;
            $this->a_page_data['section_list'] = $this->Etm->get_section();
            $this->a_page_data['body'] = $this->load->view('entrance_test/participant_list', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    function save_option_question() {
        if ($this->input->is_ajax_request()) {
            $a_option_desc = $this->input->post('option_desc_value');
            $a_option_number = $this->input->post('option_number');
            $s_answer_option = $this->input->post('answer_option');
            $s_question_id = $this->input->post('question_id_option');

            if (count($a_option_number) > 0) {
                $mba_option_answered = $this->Etm->get_participant_answer(['aqo.exam_question_id' => $s_question_id]);
                if ($mba_option_answered) {
                    $a_return = ['code' => 1, 'message' => 'Question has answered!'];
                }
                else {
                    $this->General->force_delete('dt_exam_question_option', 'exam_question_id', $s_question_id);
                    for ($option=0; $option < count($a_option_number); $option++) { 
                        $a_data = [
                            'question_option_id' => $this->uuid->v4(),
                            'exam_question_id' => $s_question_id,
                            'exam_question_option_number' => $a_option_number[$option],
                            'question_option_description' => $a_option_desc[$option],
                            'option_this_answer' => ($s_answer_option == $a_option_number[$option]) ? 'TRUE' : 'FALSE',
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        $this->General->insert_data('dt_exam_question_option', $a_data);
                    }

                    $a_return = ['code' => 0, 'message' => 'Success!'];
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => 'No option saved!'];
            }

            print json_encode($a_return);
        }
    }

    public function save_question()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('question_id') != '') {
                $s_question_id = $this->input->post('question_id');
            }else{
                $s_question_id = $this->uuid->v4();
            }

            $a_data = array(
                'exam_question_id' => $s_question_id,
                'exam_question_number' => $this->input->post('number_question'),
                'exam_section_id' => $this->input->post('section_question'),
                'exam_question_type' => ($this->input->post('section_question') == '1') ? 'LISTENING' : 'NONLISTENING',
                'exam_question_description' => $this->input->post('question_desc_body'),
                'date_added' => date('Y-m-d H:i:s')
            );
            // var_dump($a_data);exit;

            if ($this->input->post('question_id') != '') {
                $save_data = $this->Etm->save_question($a_data, array('exam_question_id' => $s_question_id));
                // var_dump('update');exit;
            }else{
                $save_data = $this->Etm->save_question($a_data);
                // var_dump('insert');exit;
            }

            if ($save_data) {
                $a_return = array('code' => 0, 'message' => 'success');
            }else{
                $a_return = array('code' => 1, 'message' => 'Error save data');
            }

            print json_encode($a_return);
        }
    }

    public function submit_period_exam()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('exam_period_name', 'Period Exam Name', 'required|trim');
            $this->form_validation->set_rules('date_start', 'Duration Start', 'required');
            $this->form_validation->set_rules('date_end', 'Duration End', 'required');

            if ($this->form_validation->run()) {
                $directory_file = APPPATH.'uploads/templates/admission/';
                if(!file_exists($directory_file)){
                    mkdir($directory_file, 0755);
                }

                $config['upload_path'] = $directory_file;
                $config['allowed_types'] = 'mp3';
                $config['max_size'] = ‭4096‬;
                $config['file_ext_tolower'] = TRUE;
                $config['replace'] = TRUE;
                
                $s_file_name = NULL;
                if (!empty($_FILES['listening_file']['name'])) {
                    $s_file_name = md5($_FILES['listening_file']['name']);
                }
                // $s_filename = (!empty($_FILES['listening_file']['name'])) ? md5($_FILES['listening_file']['name']) : NULL;
                $a_data = array(
                    'exam_period_name' => set_value('exam_period_name'),
                    'exam_start_time' => date('Y-m-d H:i:s', strtotime($this->input->post('date_start'))),
                    'exam_end_time' => date('Y-m-d H:i:s', strtotime($this->input->post('date_end'))),
                    'exam_listening_file' => $s_filename.'.mp3',
                    'exam_random_question' => ($this->input->post('period_table') == 'true') ? 'TRUE' : 'FALSE'
                );

                $b_upload = true;
                if (!empty($_FILES['listening_file']['name'])) {
                    $this->load->library('upload', $config);
                    if(!$this->upload->do_upload('listening_file')) {
                        $b_upload = false;
                        $return = array('code' => 1, 'message' => $this->upload->display_errors('<li>', '</li>'));
                    }
                }

                if ($b_upload) {
                    if ($this->input->post('period_id') == '') {
                        $a_data['exam_id'] = $this->uuid->v4();
                        $a_data['date_added'] = date('Y-m-d H:i:s');
                        $save = $this->Etm->save_exam_period($a_data);
                    }else{
                        $save = $this->Etm->save_exam_period($a_data, $this->input->post('period_id'));
                    }

                    if ($save) {
                        $return = array('code' => 0);
                    }else{
                        $return = array('code' => 1, 'message' => 'Error processing data');
                    }
                }
            }else{
                $return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }
            // $date_start = date('Y-m-d H:i:s', strtotime($this->input->post('date_start')));
            // $date_end = date('Y-m-d H:i:s', strtotime($this->input->post('date_end')));
            // // print('<pre>');
            // var_dump($this->input->post());
            print json_encode($return);
        }
    }

    public function send_mail_participant($s_exam_candidate_id)
    {
        $mba_candidate_exam = $this->Etm->get_candidate_exam(array('ec.exam_candidate_id' => $s_exam_candidate_id))[0];
        // var_dump($mba_candidate_exam);exit;
        if ($mba_candidate_exam) {
            $t_body = <<<TEXT
Dear {$mba_candidate_exam->personal_data_name},

Your token: {$mba_candidate_exam->token}
Please log in with url: https://bit.ly/2Uow3fc
TEXT;
            
            // $config = $this->config->item('mail_config');
            // $config['mailtype'] = 'html';
            // $this->email->initialize($config);
            $this->email->from('employee@company.ac.id', 'IULI PMB Team');
            $this->email->to($mba_candidate_exam->personal_data_email);
            // $this->email->to('budi.siswanto1450@gmail.com');
            $bccEmail = array('employee@company.ac.id', 'employee@company.ac.id');
            $this->email->bcc($bccEmail);
            
            $this->email->subject("Online Entrance Test");
            $this->email->message($t_body);
            if($this->email->send()){
                $this->email->clear();
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function input_exam()
    {
        $this->load->view('form/new_exam');
    }

    public function period_table()
    {
        $this->load->view('table/period_list');
    }
}
