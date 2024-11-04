<?php
class Entrance_test extends MX_Controller
{
    public $data = array(
		'pageTitle' => 'Entrance Test Sign In',
		'pageChildTitle' => '',
		// 'body' => 'profile',
		'parentPage' => null,
		'childPage' => null
    );
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('admission/Entrance_test_model', 'Etm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        // $this->load->model('candidate/Profiles');
        $s_environment = 'production';
		if($this->session->userdata('environment') != ''){
			$s_environment = $this->session->userdata('environment');
		}
		$this->db = $this->load->database($s_environment, true);
        $this->load->config('portal_apps_config');
    }

    public function logout()
    {
        $this->session->sess_destroy();
		redirect(site_url('exam/auth_entrance_test'));
    }

    public function check_in($s_token = false)
    {
        // var_dump($this->session->userdata());
        $s_personal_data_id = $this->session->userdata('personal_data_id');
        if ($this->input->is_ajax_request()) {
            $student_data = $this->Stm->get_student_personal_data(array('st.personal_data_id' => $s_personal_data_id))[0];
            $s_token = $this->input->post('token');
            $mbo_exam_candidate = $this->Etm->get_candidate_exam(array('ec.student_id' => $student_data->student_id, 'ec.token' => $s_token))[0];
            // var_dump($mbo_exam_candidate);exit;
            if ($mbo_exam_candidate->candidate_exam_status == 'FINISH') {
                $a_return = array('code' => 1, 'message' => 'You have already answered this online entrance test !');
            }else if ($mbo_exam_candidate->candidate_exam_status == 'CANCEL') {
                $a_return = array('code' => 1, 'message' => 'your token is canceled!');
            }else if ($mbo_exam_candidate) {
                $a_return = array('code' => 0, 'message' => 'success');
            }else{
                $a_return = array('code' => 1, 'message' => 'your token is wrong!');
            }

            print json_encode($a_return);
        }else{
            $mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
            $this->data['personal_data'] = $mbo_personal_data;
            $this->data['body'] = $this->load->view('form/form_check_in', $this->data, true);
		    $this->load->view('exam/entrance_test_layout2', $this->data);
        }
    }

    public function show_section_2()
    {
        $a_question_id = array();
        $mbo_question_section_2A = $this->Etm->get_question(array('exam_section_id' => 2, 'exam_question_number <=' => '15'));
        if ($mbo_question_section_2A) {
            foreach ($mbo_question_section_2A as $q_2A) {
                array_push($a_question_id, $q_2A->exam_question_id);
                $a_option_A = array();
                $mba_option_A = $this->Etm->get_option(array('exam_question_id' => $q_2A->exam_question_id));
                if ($mba_option_A) {
                    foreach ($mba_option_A as $opt_A) {
                        array_push($a_option_A, array(
                            'question_option_id' => $opt_A->question_option_id,
                            'exam_question_option_number' => $opt_A->exam_question_option_number,
                            'question_option_description' => $opt_A->question_option_description
                        ));
                    }
                }
                $q_2A->option = $a_option_A;
            }
        }

        $mbo_question_section_2B = $this->Etm->get_question(array('exam_section_id' => 2, 'exam_question_number >' => '15'));
        if ($mbo_question_section_2B) {
            foreach ($mbo_question_section_2B as $q_2B) {
                array_push($a_question_id, $q_2B->exam_question_id);
                $a_option_B = array();
                $mba_option_B = $this->Etm->get_option(array('exam_question_id' => $q_2B->exam_question_id));
                if ($mba_option_B) {
                    foreach ($mba_option_B as $opt_B) {
                        array_push($a_option_B, array(
                            'question_option_id' => $opt_B->question_option_id,
                            'exam_question_option_number' => $opt_B->exam_question_option_number,
                            'question_option_description' => $opt_B->question_option_description
                        ));
                    }
                }
                $q_2B->option = $a_option_B;
            }
        }
        $this->data['section_2A'] = $mbo_question_section_2A;
        $this->data['section_2B'] = $mbo_question_section_2B;
        $this->data['question_section_id'] = $a_question_id;
        $this->load->view('section/section2', $this->data);
    }

    public function show_section_1()
    {
        $a_question_id = array();
        $a_question_section_1 = array();
        $mbo_question_section_1 = $this->Etm->get_question(array('exam_section_id' => 1));
        if ($mbo_question_section_1) {
            foreach ($mbo_question_section_1 as $q_1) {
                array_push($a_question_id, $q_1->exam_question_id);
                $a_option = array();
                $mba_option = $this->Etm->get_option(array('exam_question_id' => $q_1->exam_question_id));
                if ($mba_option) {
                    foreach ($mba_option as $opt) {
                        array_push($a_option, array(
                            'question_option_id' => $opt->question_option_id,
                            'exam_question_option_number' => $opt->exam_question_option_number,
                            'question_option_description' => $opt->question_option_description
                        ));
                    }
                }
                $q_1->option = $a_option;
            }
        }
        // print('<pre>');
        // var_dump($mbo_question_section_1);exit;
        $this->data['section_1'] = $mbo_question_section_1;
        $this->data['question_section_id'] = $a_question_id;
        $this->load->view('section/section1', $this->data);
    }

    public function send_notification_finish()
    {
        if ($this->session->has_userdata('target')) {
            $s_email_target = false;
            if ($this->session->userdata('target') == 'pmb') {
                $s_personal_data_id = $this->session->userdata('personal_data_id');
                $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $s_personal_data_id]);
                $s_email_target = ($mba_personal_data) ? $mba_personal_data[0]->personal_data_email : false;
            }
            else if ($this->session->userdata('target') == 'event') {
                $s_booking_id = $this->session->userdata('booking_id');
                $mba_booking_data = $this->General->get_where('dt_event_bookings', ['booking_id' => $s_booking_id]);
                $s_email_target = ($mba_booking_data) ? $mba_booking_data[0]->booking_email : false;
            }

            if ($s_email_target) {
                $a_textdata = $this->config->item('message_finish_entrancetest');
                $config['mailtype'] = 'html';
		        $this->email->initialize($config);
                $s_text = $a_textdata['message'];
                $this->email->from('employee@company.ac.id', 'IULI-Portal');
                $this->email->to($s_email_target);
                // $this->email->to('employee@company.ac.id');
                $this->email->bcc('employee@company.ac.id');
                $this->email->subject('[ADMISSION] IULI Online Test');
                $this->email->message($s_text);
                $this->email->send();
                print json_encode(['result' => 0]);
            }
            else {
                print json_encode(['result' => 3]);
            }
        }
        else {
            print json_encode(['result' => 2]);
        }
    }

    public function save_exam_option_answer()
    {
        if ($this->input->is_ajax_request()) {
            $s_exam_candidate_id = $this->input->post('exam_candidate_id');
            $s_exam_question_id = $this->input->post('question_id');
            $s_question_option_id = $this->input->post('question_option_id');

            $data_save = array(
                'exam_candidate_id' => $s_exam_candidate_id,
                'exam_question_id' => $s_exam_question_id,
                'question_option_id' => $s_question_option_id,
                'date_added' => date('Y-m-d H:i:s')
            );

            $this->Etm->save_candidate_answer($data_save, array('exam_candidate_id' => $s_exam_candidate_id, 'exam_question_id' => $s_exam_question_id));
        }
    }

    public function submit_exam()
    {
        if ($this->input->is_ajax_request()) {
            $a_answer = $this->input->post('answer');
            $s_token = $this->input->post('token');
            $s_section = $this->input->post('section_id');
            $s_finish_section = $this->input->post('action');

            $s_personal_data_id = $this->session->userdata('personal_data_id');
            $s_booking_id = $this->session->userdata('booking_id');
            $s_exam_target = $this->session->userdata('target');
            // print('<pre>');
            // var_dump($s_finish_section);exit;

            $s_personal_data_id = $this->session->userdata('personal_data_id');
            $student_data = $this->Stm->get_student_personal_data(array('st.personal_data_id' => $s_personal_data_id))[0];

            if ($s_exam_target == 'pmb') {
                $mbo_exam_candidate = $this->Etm->get_candidate_exam(array('ec.student_id' => $student_data->student_id, 'ec.token' => $s_token), 'pmb')[0];
            }
            else {
                $mbo_exam_candidate = $this->Etm->get_candidate_exam(array('ec.booking_id' => $s_booking_id, 'ec.token' => $s_token), 'event')[0];
            }

            $this->Etm->save_candidate_section(array('section_has_filled' => 'TRUE'), array('exam_candidate_id' => $mbo_exam_candidate->exam_candidate_id, 'exam_section_id' => $s_section));

            $a_data = array();
            $i_counter_correct = 0;
            $i_counter_wrong = 0;
            $this->db->trans_start();
            if ((is_array($a_answer)) AND (count($a_answer) > 0)) {
                // $this->Etm->remove_prev_answer($mbo_exam_candidate->exam_candidate_id);
                foreach ($a_answer as $answer) {
                    $check_answer = $this->Etm->get_option(array('question_option_id' => $answer['question_option_id']))[0];
                    if ($check_answer->option_this_answer == 'TRUE') {
                        $i_counter_correct++;
                    }else{
                        $i_counter_wrong++;
                    }
                    $data_save = array(
                        'exam_candidate_id' => $mbo_exam_candidate->exam_candidate_id,
                        'exam_question_id' => $answer['question_id'],
                        'question_option_id' => $answer['question_option_id'],
                        'date_added' => date('Y-m-d H:i:s')
                    );

                    $this->Etm->save_candidate_answer($data_save, array('exam_candidate_id' => $mbo_exam_candidate->exam_candidate_id, 'exam_question_id' => $answer['question_id']));
                }
            }

            $s_time_now = date('Y-m-d H:i:s');
            $s_end_time = date('Y-m-d H:i:s', strtotime($mbo_exam_candidate->end_time));
            // var_dump($s_end_time.' > '.$s_time_now);exit;
            if (($s_time_now >= $s_end_time) OR ($s_finish_section == 'finish')) {
                $start_time = date_create($mbo_exam_candidate->start_time);
                $now = date_create();
                $total_time = date_diff($start_time, $now);
                $s_total_time = $total_time->h.':'.$total_time->i.':'.$total_time->s;
                $a_candidate_update_data = array(
                    'total_time' => $s_total_time,
                    'correct_answer' => $i_counter_correct,
                    'wrong_answer' => $i_counter_wrong,
                    'filled_question' => (is_array($a_answer)) ? count($a_answer) : 0,
                    'candidate_exam_status' => 'FINISH'
                );

                $this->Etm->save_candidate_exam($a_candidate_update_data, array('exam_candidate_id' => $mbo_exam_candidate->exam_candidate_id));
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $a_return = array('code' => 1, 'message' => 'Error submit your answer!');
            }else{
                $this->db->trans_commit();
                $a_return = array('code' => 0, 'message' => 'Success');
            }
            // var_dump($s_total_time);
            print json_encode($a_return);
        }
    }

    // public function submit_answer()
    // {
    //     if ($this->input->is_ajax_request()) {
    //         $a_answer = $this->input->post('answer');
    //         $s_token = $this->input->post('token');

    //         $s_personal_data_id = $this->session->userdata('personal_data_id');
    //         $student_data = $this->Stm->get_student_personal_data(array('st.personal_data_id' => $s_personal_data_id))[0];
    //         $mbo_exam_candidate = $this->Etm->get_candidate_exam(array('ec.student_id' => $student_data->student_id, 'ec.token' => $s_token))[0];

    //         $a_data = array();
    //         $i_counter_correct = 0;
    //         $i_counter_wrong = 0;
    //         $this->db->trans_start();
    //         if (count($a_answer) > 0) {
    //             $this->Etm->remove_prev_answer($mbo_exam_candidate->exam_candidate_id);
    //             foreach ($a_answer as $answer) {
    //                 $check_answer = $this->Etm->get_option(array('question_option_id' => $answer['question_option_id']))[0];
    //                 if ($check_answer->option_this_answer == 'TRUE') {
    //                     $i_counter_correct++;
    //                 }else{
    //                     $i_counter_wrong++;
    //                 }
    //                 $data_save = array(
    //                     'exam_candidate_id' => $mbo_exam_candidate->exam_candidate_id,
    //                     'exam_question_id' => $answer['question_id'],
    //                     'question_option_id' => $answer['question_option_id'],
    //                     'date_added' => date('Y-m-d H:i:s')
    //                 );

    //                 $this->Etm->save_candidate_answer($data_save, $mbo_exam_candidate->exam_candidate_id);
    //             }
    //         }

    //         $start_time = date_create($mbo_exam_candidate->start_time);
    //         $now = date_create();
    //         $total_time = date_diff($start_time, $now);
    //         $s_total_time = $total_time->h.':'.$total_time->i.':'.$total_time->s;
    //         $a_candidate_update_data = array(
    //             'total_time' => $s_total_time,
    //             'correct_answer' => $i_counter_correct,
    //             'wrong_answer' => $i_counter_wrong,
    //             'filled_question' => count($a_answer),
    //             'candidate_exam_status' => 'FINISH'
    //         );

    //         $this->Etm->save_candidate_exam($a_candidate_update_data, array('exam_candidate_id' => $mbo_exam_candidate->exam_candidate_id));

    //         if ($this->db->trans_status() === false) {
    //             $this->db->trans_rollback();
    //             $a_return = array('code' => 1, 'message' => 'Error submit your answer!');
    //         }else{
    //             $this->db->trans_commit();
    //             $a_return = array('code' => 0, 'message' => 'Success');
    //         }
    //         // var_dump($s_total_time);
    //         print json_encode($a_return);
    //     }
    // }

    public function section_view_data($s_section_id, $s_exam_id, $s_exam_candidate_id)
    {
        $mbo_section = $this->Etm->get_section(array('es.exam_section_id' => $s_section_id, 'es.exam_id' => $s_exam_id))[0];
        if ($mbo_section) {
            $media = false;
            if (!is_null($mbo_section->exam_listening_file)) {
                $media = array('url' => base_url().'assets/vendors/MINImusic-Player-master/data/'.$mbo_section->exam_listening_file);
            }
            $mbo_section->media = $media;
            $s_section_desc = '';
            $s_path_sec_desc = APPPATH.'modules/exam/views/section/section_description_'.$s_section_id.'.php';
            if (file_exists($s_path_sec_desc)) {
                $this->data['section_data'] = $mbo_section;
                $s_section_desc = $this->load->view('section/section_description_'.$s_section_id, $this->data, true);
            }
            $mbo_section->description = $s_section_desc;
            $mba_section_part = $this->Etm->get_exam_part(array('eq.exam_section_id' => $s_section_id));
            if ($mba_section_part) {
                foreach ($mba_section_part as $part) {
                    $s_part_example = '';
                    $s_file_example = 'section_part_example_'.$s_section_id.'_'.str_replace(' ', '-', $part->exam_question_part);
                    $s_path_part_example = APPPATH.'modules/exam/views/section/'.$s_file_example.'.php';
                    if (file_exists($s_path_part_example)) {
                        $this->data['example_data'] = $part;
                        $s_part_example = $this->load->view('section/'.$s_file_example, $this->data, true);
                    }
                    $part->example = $s_part_example;

                    $mba_question_part = $this->Etm->get_question(array('exam_section_id' => $s_section_id, 'exam_question_part' => $part->exam_question_part));
                    if ($mba_question_part) {
                        foreach ($mba_question_part as $question) {
                            $a_option = array();
                            $mba_option = $this->Etm->get_option(array('exam_question_id' => $question->exam_question_id));
                            if ($mba_option) {
                                foreach ($mba_option as $opt) {
                                    array_push($a_option, array(
                                        'question_option_id' => $opt->question_option_id,
                                        'exam_question_option_number' => $opt->exam_question_option_number,
                                        'question_option_description' => $opt->question_option_description
                                    ));
                                }
                            }
                            $question->option = $a_option;
                        }
                    }
                    $part->question = $mba_question_part;
                }
            }
            $mbo_section->part = $mba_section_part;
        }

        // print('<pre>');
        // print_r($mbo_section);
        $this->data['section'] = $mbo_section;
        $this->data['exam_candidate_id'] = $s_exam_candidate_id;
        // $this->data['body'] = $this->load->view('section/section_view', $this->data, true);
        return $this->load->view('section/section_view', $this->data, true);
        // return $mbo_section;
    }

    function dasboard_exam() {
        $this->data['body'] = $this->load->view('exam/dashboard_exam', $this->data, true);
        $this->load->view('layout_ext', $this->data);
    }

    public function online_test($s_token, $s_section_id = false)
    {
        if ((!$this->session->userdata('personal_data_id')) AND (!$this->session->userdata('booking_id'))) {
            redirect('exam/entrance_test/logout');
        }else{
            $s_section_id = ($s_section_id) ? $s_section_id : 1;
            $mbo_section_data = $this->Etm->get_section(array('exam_section_id' => $s_section_id))[0];
            $s_personal_data_id = $this->session->userdata('personal_data_id');
            $s_booking_id = $this->session->userdata('booking_id');
            $s_exam_target = $this->session->userdata('target');
            $student_data = $this->Stm->get_student_personal_data(array('st.personal_data_id' => $s_personal_data_id))[0];
            $booking_data = $this->General->get_where('dt_event_bookings', ['booking_id' => $s_booking_id]);
            
            $s_id_ = ($s_exam_target == 'pmb') ? $student_data->student_id : $s_booking_id;
            if ($s_exam_target == 'pmb') {
                $mbo_exam_candidate = $this->Etm->get_candidate_exam(array('ec.student_id' => $student_data->student_id, 'ec.token' => $s_token), 'pmb');
            }
            else {
                $mbo_exam_candidate = $this->Etm->get_candidate_exam(array('ec.booking_id' => $s_booking_id, 'ec.token' => $s_token), 'event');
            }

            if ($mbo_exam_candidate) {
                if (in_array($mbo_exam_candidate[0]->candidate_exam_status, array('PENDING', 'PROGRESS'))) {
                    $s_exam_id = $mbo_exam_candidate[0]->exam_id;
                    $mba_section_data = $this->Etm->get_section();
                    if (is_null($mbo_exam_candidate[0]->start_time)) {
                        $this->Etm->update_time($mbo_exam_candidate[0]->exam_candidate_id, $mbo_exam_candidate[0]->exam_end_time);
                        if ($s_exam_target == 'pmb') {
                            $mbo_exam_candidate = $this->Etm->get_candidate_exam(array('ec.student_id' => $student_data->student_id, 'ec.token' => $s_token), 'pmb');
                        }
                        else {
                            $mbo_exam_candidate = $this->Etm->get_candidate_exam(array('ec.booking_id' => $s_booking_id, 'ec.token' => $s_token), 'event');
                        }
                    }
                    $this->data['candidate_data'] = $mbo_exam_candidate[0];

                    $mba_candidate_section = $this->Etm->get_candidate_section(array('ecs.exam_candidate_id' => $mbo_exam_candidate[0]->exam_candidate_id, 'ecs.exam_section_id' => $s_section_id))[0];
                    if ($mba_candidate_section) {
                        $s_time_now = date('Y-m-d H:i:s');
                        $s_end_time = date('Y-m-d H:i:s', strtotime($mba_candidate_section->section_time_end));
                        // if ($s_time_now >= $s_end_time) {
                        //     $this->data['finish'] = 'finish';
                        // }
                        if ($mba_candidate_section->section_has_filled == 'TRUE') {
                            $mba_candidate_section = $this->Etm->get_candidate_section(array('ecs.exam_candidate_id' => $mbo_exam_candidate[0]->exam_candidate_id));
                            // if (($mba_section_data) AND (count($mba_section_data) > count($mba_candidate_section))) {
                                $a_sect_id = array();
                                foreach ($mba_section_data as $sect_data) {
                                    if (!in_array($sect_data->exam_section_id, $a_sect_id)) {
                                        array_push($a_sect_id, $sect_data->exam_section_id);
                                    }
                                }

                                $key = array_search($s_section_id, $a_sect_id);
                                $key++;
                                $s_section_id = $a_sect_id[$key];
                                redirect('exam/entrance_test/online_test/'.$s_token.'/'.$s_section_id);
                            // }else if (($mba_section_data) AND (count($mba_section_data) == count($mba_candidate_section))) {
                            //     $this->data['finish'] = 'finish';
                            // }
                            // if ($s_token == '80a6-1800-0ad6-6cc6') {
                            //     print('<pre>');var_dump($mba_section_data);exit;
                            // }
                            // di comment
                            // else {
                            //     print('Error System');
                            //     exit;
                            // }
                        }else{
                            $mba_section_candidate = $this->Etm->get_candidate_section(array('ecs.exam_candidate_id' => $mbo_exam_candidate[0]->exam_candidate_id));
                            if (count($mba_section_data) == count($mba_section_candidate)) {
                                $this->data['finish'] = 'finish';
                            }
                        }
                    }else{
                        $time_now = date('Y-m-d H:i:s');
                        $start_date = new DateTime($time_now);
                        $end_date = $start_date->add(new DateInterval('PT' . $mbo_section_data->exam_section_limit_minute . 'M'));
                        $start_time = $time_now;
                        $end_time = $end_date->format('Y-m-d H:i:s');

                        $a_new_data = array(
                            'exam_candidate_id' => $mbo_exam_candidate[0]->exam_candidate_id,
                            'exam_section_id' => $s_section_id,
                            'section_time_start' => $start_time,
                            'section_time_end' => $end_time,
                            'section_has_filled' => 'FALSE',
                            'date_added' => $start_time
                        );
                        $this->Etm->save_candidate_section($a_new_data);
                        $mba_section_candidate = $this->Etm->get_candidate_section(array('ecs.exam_candidate_id' => $mbo_exam_candidate[0]->exam_candidate_id));
                        if (count($mba_section_data) == count($mba_section_candidate)) {
                            $this->data['finish'] = 'finish';
                        }
                        $mba_candidate_section = $this->Etm->get_candidate_section(array('ecs.exam_candidate_id' => $mbo_exam_candidate[0]->exam_candidate_id, 'ecs.exam_section_id' => $s_section_id))[0];
                    }
                    
                    $mba_section_cand = $this->Etm->get_candidate_section(array('ecs.exam_candidate_id' => $mbo_exam_candidate[0]->exam_candidate_id, 'ecs.exam_section_id' => $s_section_id))[0];
                    $this->data['token'] = $s_token;
                    $this->data['section_id'] = $s_section_id;
                    $this->data['end_time'] = $mba_section_cand->section_time_end;
                    $this->data['body'] = $this->section_view_data($s_section_id, $s_exam_id, $mbo_exam_candidate[0]->exam_candidate_id);
                    $this->data['exam_candidate_id'] = $mbo_exam_candidate[0]->exam_candidate_id;
                    $this->load->view('entrance_test_layout', $this->data);
                }else{
                    $url = base_url().'exam/entrance_test/logout';
                    print('<script>alert("You are not allowed to take this online test because you have completed this test or your token has been canceled"); window.location.href="'.$url.'";</script>');
                }
            }else{
                $url = base_url().'exam/entrance_test/logout';
                print('<script>alert("Your token is wrong"); window.location.href="'.$url.'";</script>');
            }
        }
    }

    public function exam($s_token)
    {
        $s_personal_data_id = $this->session->userdata('personal_data_id');
        $student_data = $this->Stm->get_student_personal_data(array('st.personal_data_id' => $s_personal_data_id))[0];
        $mbo_exam_candidate = $this->Etm->get_candidate_exam(array('ec.student_id' => $student_data->student_id, 'ec.token' => $s_token), 'pmb');
        if (!$mbo_exam_candidate) {
            $mbo_exam_candidate = $this->Etm->get_candidate_exam(array('ec.student_id' => $student_data->student_id, 'ec.token' => $s_token), 'event');
        }
        if ($mbo_exam_candidate) {
            if (is_null($mbo_exam_candidate[0]->start_time)) {
                $this->Etm->update_time($mbo_exam_candidate[0]->exam_candidate_id, $mbo_exam_candidate[0]->exam_end_time);
                $mbo_exam_candidate = $this->Etm->get_candidate_exam(array('ec.student_id' => $student_data->student_id, 'ec.token' => $s_token), 'pmb');
                if (!$mbo_exam_candidate) {
                    $mbo_exam_candidate = $this->Etm->get_candidate_exam(array('ec.student_id' => $student_data->student_id, 'ec.token' => $s_token), 'event');
                }
            }
            $this->data['candidate_data'] = $mbo_exam_candidate[0];
            $this->data['token'] = $s_token;
            $this->load->view('entrance_test_layout2', $this->data);
        }else{
            $url = base_url().'exam/entrance_test/logout';
            print('<script>alert("Your token is wrong"); window.location.href="'.$url.'";</script>');
            // redirect('');
        }
    }
}
