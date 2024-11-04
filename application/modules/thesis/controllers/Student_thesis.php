<?php
class Student_thesis extends App_core
{
    function __construct()
    {
        parent::__construct('student_thesis');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('thesis/Thesis_model', 'Tm');
    }

    public function view_serv()
    {
        print('<pre>');
        var_dump($_SERVER);
    }

    function thesis_defense() {
        $mba_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $this->session->userdata('student_id')));
        $mba_thesis_data = $this->Tm->get_student_list_thesis([
            'ts.student_id' => $this->session->userdata('student_id')
        ]);
        if ($mba_student_data AND $mba_thesis_data) {
            $o_thesis_data = $mba_thesis_data[0];
            $mba_thesis_advisor_approved_1 = $this->Tm->get_list_student_advisor([
                'ts.thesis_student_id' => $o_thesis_data->thesis_student_id,
                'tsa.advisor_type' => 'approved_advisor_1'
            ], 'advisor');
            $mba_thesis_advisor_approved_2 = $this->Tm->get_list_student_advisor([
                'ts.thesis_student_id' => $o_thesis_data->thesis_student_id,
                'tsa.advisor_type' => 'approved_advisor_2'
            ], 'advisor');
            if ($mba_thesis_advisor_approved_1) {
                $mba_thesis_advisor_approved_1 = $mba_thesis_advisor_approved_1[0];
                $mba_thesis_advisor_approved_1->advisor_name = $this->Pdm->retrieve_title($mba_thesis_advisor_approved_1->personal_data_id);
            }

            if ($mba_thesis_advisor_approved_2) {
                $mba_thesis_advisor_approved_2 = $mba_thesis_advisor_approved_2[0];
                $mba_thesis_advisor_approved_2->advisor_name = $this->Pdm->retrieve_title($mba_thesis_advisor_approved_2->personal_data_id);
            }
            
            $this->a_page_data['o_student_data'] = $mba_student_data[0];
            $this->a_page_data['thesis_data'] = $mba_thesis_data;
            $this->a_page_data['thesis_page_type'] = 'defense';
            $this->a_page_data['advisor_approved_1'] = $mba_thesis_advisor_approved_1;
            $this->a_page_data['advisor_approved_2'] = $mba_thesis_advisor_approved_2;
            // $this->a_page_data['thesis_log_data'] = $mba_thesis_log_data;
            $this->a_page_data['defense_data'] = $this->Tm->get_thesis_defense_student(['st.student_id' => $this->session->userdata('student_id')]);
            $this->a_page_data['body'] = $this->load->view('thesis/student/thesis_student', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    // public function proposal_registration()
    // {
    //     $mba_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $this->session->userdata('student_id')));
    //     if ($mba_student_data) {
    //         $mba_thesis_data = modules::run('thesis/get_thesis_student', $this->session->userdata('student_id'));
    //         $mba_thesis_advisor_approved_1 = false;
    //         $mba_thesis_advisor_approved_2 = false;

    //         if ($mba_thesis_data) {
    //             $mba_thesis_advisor_approved_1 = $this->Tm->get_advisor_student($mba_thesis_data->thesis_student_id, 'proposed_advisor_1');
    //             $mba_thesis_advisor_approved_2 = $this->Tm->get_advisor_student($mba_thesis_data->thesis_student_id, 'proposed_advisor_2');
    //         }
    //         if ($mba_thesis_advisor_approved_1) {
    //             $mba_thesis_advisor_approved_1 = $mba_thesis_advisor_approved_1[0];
    //             $mba_thesis_advisor_approved_1->advisor_name = $this->Pdm->retrieve_title($mba_thesis_advisor_approved_1->personal_data_id);
    //         }

    //         if ($mba_thesis_advisor_approved_2) {
    //             $mba_thesis_advisor_approved_2 = $mba_thesis_advisor_approved_2[0];
    //             $mba_thesis_advisor_approved_2->advisor_name = $this->Pdm->retrieve_title($mba_thesis_advisor_approved_2->personal_data_id);
    //         }

    //         $this->a_page_data['thesis_data'] = $mba_thesis_data;
    //         $this->a_page_data['advisor_proposed_1'] = $mba_thesis_advisor_approved_1;
    //         $this->a_page_data['advisor_proposed_2'] = $mba_thesis_advisor_approved_2;
    //         $this->a_page_data['o_student_data'] = $mba_student_data[0];

    //         $this->a_page_data['message_error'] = 'not allowed!';
    //         $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
    //         if ($this->session->userdata('allowed_proposed_thesis')) {
    //             $this->a_page_data['body'] = $this->load->view('thesis/student/proposal_submission', $this->a_page_data, true);
    //         }
    //         $this->load->view('layout', $this->a_page_data);
    //     }
    //     else {
    //         show_404();
    //     }
    // }

    public function proposal_submission($option_mode = 'detail')
    {
        $mba_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $this->session->userdata('student_id')));
        if ($mba_student_data) {
            $mba_thesis_data = $this->Tm->get_student_list_thesis([
                'ts.student_id' => $this->session->userdata('student_id')
            ]);

            $mba_thesis_log_data = false;
            $mba_thesis_advisor_approved_1 = false;
            $mba_thesis_advisor_approved_2 = false;
            $mba_thesis_advisor_proposed_1 = false;
            $mba_thesis_advisor_proposed_2 = false;

            if ($mba_thesis_data) {
                $o_thesis_data = $mba_thesis_data[0];
                $mba_thesis_log_data = $this->Tm->get_thesis_log($o_thesis_data->thesis_student_id, [
                    'tsl.thesis_log_type' => 'proposal'
                ]);

                $mba_thesis_advisor_approved_1 = $this->Tm->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_thesis_data->thesis_student_id,
                    'tsa.advisor_type' => 'approved_advisor_1'
                ], 'advisor');
                $mba_thesis_advisor_approved_2 = $this->Tm->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_thesis_data->thesis_student_id,
                    'tsa.advisor_type' => 'approved_advisor_2'
                ], 'advisor');
                $mba_thesis_advisor_proposed_1 = $this->Tm->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_thesis_data->thesis_student_id,
                    'tsa.advisor_type' => 'proposed_advisor_1'
                ], 'advisor');
                $mba_thesis_advisor_proposed_2 = $this->Tm->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_thesis_data->thesis_student_id,
                    'tsa.advisor_type' => 'proposed_advisor_2'
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
            
            if ($mba_thesis_advisor_proposed_1) {
                $mba_thesis_advisor_proposed_1 = $mba_thesis_advisor_proposed_1[0];
                $mba_thesis_advisor_proposed_1->advisor_name = $this->Pdm->retrieve_title($mba_thesis_advisor_proposed_1->personal_data_id);
            }

            if ($mba_thesis_advisor_proposed_2) {
                $mba_thesis_advisor_proposed_2 = $mba_thesis_advisor_proposed_2[0];
                $mba_thesis_advisor_proposed_2->advisor_name = $this->Pdm->retrieve_title($mba_thesis_advisor_proposed_2->personal_data_id);
            }

            $this->a_page_data['thesis_data'] = $mba_thesis_data;
            $this->a_page_data['thesis_page_type'] = 'proposal_submission';
            $this->a_page_data['thesis_log_data'] = $mba_thesis_log_data;
            $this->a_page_data['advisor_proposed_1'] = $mba_thesis_advisor_proposed_1;
            $this->a_page_data['advisor_proposed_2'] = $mba_thesis_advisor_proposed_2;
            $this->a_page_data['advisor_approved_1'] = $mba_thesis_advisor_approved_1;
            $this->a_page_data['advisor_approved_2'] = $mba_thesis_advisor_approved_2;
            $this->a_page_data['o_student_data'] = $mba_student_data[0];

            $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
            if ($this->session->userdata('allowed_proposed_thesis')) {
                if ($mba_thesis_data) {
                    if (($this->session->userdata('student_id') == '85037a5b-f25e-43bb-b5c8-3c7f85fa95da') AND ($_SERVER['REMOTE_ADDR'] == '202.93.225.254')) {
                        // print('<pre>');var_dump($mba_thesis_data);exit;
                    }
                    // $this->a_page_data['submission_page'] = $this->load->view('thesis/student/proposal_submission', $this->a_page_data, true);
                    $this->a_page_data['allow_update'] = false;
                    if ($mba_thesis_data[0]->current_progress == 'proposal' ) {
                        $mba_current_semester = $this->Tm->get_thesis_list_by_log([
                            'ts.thesis_student_id' => $mba_thesis_data[0]->thesis_student_id,
                            'tsl.academic_year_id' => $this->session->userdata('academic_year_id_active'),
                            'tsl.semester_type_id' => $this->session->userdata('semester_type_id_active'),
                        ]);
                        if (($mba_thesis_data[0]->current_status == '') OR (is_null($mba_thesis_data[0]->current_status))) {
                            $this->a_page_data['allow_update'] = true;
                        }
                        else if (in_array($mba_thesis_data[0]->current_status, ['pending', 'rejected'])) {
                            $this->a_page_data['allow_update'] = true;
                        }
                        else if (!$mba_current_semester) {
                            $this->a_page_data['allow_update'] = true;
                        }
                    }
                    // $this->a_page_data['body'] = $this->load->view('thesis/student/thesis_student', $this->a_page_data, true);
                    // $this->a_page_data['body'] = maintenance_page(true);
                    // if (($_SERVER['REMOTE_ADDR'] == '202.93.225.254') AND ($this->session->userdata('student_id') == '12962f40-2405-4756-bae2-2c214ceb0c5d')) {
                    // if (($_SERVER['REMOTE_ADDR'] == '120.188.33.253') AND ($this->session->userdata('student_id') == '12962f40-2405-4756-bae2-2c214ceb0c5d')) {
                        // $this->a_page_data['body'] = $this->load->view('thesis/student/thesis_student', $this->a_page_data, true);
                    // }
                    $this->a_page_data['defense_data'] = $this->Tm->get_thesis_defense_student(['st.student_id' => $this->session->userdata('student_id')]);
                    if ($option_mode == 'detail') {
                        $this->a_page_data['body'] = $this->load->view('thesis/student/thesis_student', $this->a_page_data, true);
                    }
                    else if ($option_mode == 'customize') {
                        $this->a_page_data['body'] = $this->load->view('thesis/student/proposal_submission', $this->a_page_data, true);
                    }
                }
                else {
                    $this->a_page_data['body'] = $this->load->view('thesis/student/proposal_submission', $this->a_page_data, true);
                    // $this->a_page_data['body'] = maintenance_page(true);
                }
            }
            else {
                $this->a_page_data['message_error'] = 'Sorry, we did not find krs thesis in your krs list.<br>Please contact head of study program to follow up!';
                $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
            }
            

            // if ($this->session->userdata('student_id') != '61ceebd9-f9e5-4786-b382-c5b15af35870') {
            //     $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
            // }
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    public function test_submission()
    {
        $mba_thesis_data = $this->Tm->get_student_list_thesis([
            'ts.student_id' => $this->session->userdata('student_id')
        ]);
        print('<pre>');var_dump($mba_thesis_data);exit;
    }

    public function final_submissionold($option_mode = 'detail')
    {
        $a_open_door = [
            '3338d11d-25b8-42fc-9a55-625e6897828e', // ANGELIA MAHARANI PUTRI LEAN (CHE/2018)
            'eee89f7c-4326-4406-9ea4-54fea9268f8d', // ANASTASIA RACHEL ROMPAS (CHE/2018)
            '9c8c42a8-f681-49ce-937b-02bea1437ae3', // KEVIN PANNANANDA SULEMAN (CHE/2018)
        ];

        $mba_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $this->session->userdata('student_id')));
        if ($mba_student_data) {
            // $mba_thesis_data = modules::run('thesis/get_thesis_student', $this->session->userdata('student_id'));
            $mba_thesis_data = $this->Tm->get_student_list_thesis([
                'ts.student_id' => $this->session->userdata('student_id')
            ]);

            $mba_thesis_log_data = false;
            $mba_thesis_advisor_approved_1 = false;
            $mba_thesis_advisor_approved_2 = false;

            if ($mba_thesis_data) {
                $o_thesis_data = $mba_thesis_data[0];

                $mba_thesis_log_data = $this->Tm->get_thesis_log($o_thesis_data->thesis_student_id, [
                    'tsl.thesis_log_type' => 'final'
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
            
            $this->a_page_data['allow_update'] = false;
            $this->a_page_data['thesis_data'] = $mba_thesis_data;
            $this->a_page_data['thesis_log_data'] = $mba_thesis_log_data;
            $this->a_page_data['advisor_approved_1'] = $mba_thesis_advisor_approved_1;
            $this->a_page_data['advisor_approved_2'] = $mba_thesis_advisor_approved_2;
            $this->a_page_data['o_student_data'] = $mba_student_data[0];
            $this->a_page_data['thesis_page_type'] = 'final_submission';
            // $this->a_page_data['message_error'] = 'Sorry, The registration page will be ready in the next 1 or 2 hours!';
            // $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
            $s_registration = 'not allowed';
            if ($mba_thesis_data) {
                if ($mba_thesis_data[0]->current_progress == 'final') {
                    if (is_null($mba_thesis_data[0]->current_status)) {
                        $s_registration = 'submission';
                        $this->a_page_data['allow_update'] = true;
                        // $this->a_page_data['body'] = $this->load->view('thesis/student/final_submission', $this->a_page_data, true);
                    }
                    else {
                        $s_registration = 'view';
                    }
                }
                else if ($mba_thesis_data[0]->current_progress == 'work') {
                    $mba_defense_data = $this->General->get_where('thesis_defense', ['thesis_students_id' => $mba_thesis_data[0]->thesis_student_id, 'score_grade != ' => 'F']);
                    if ($mba_defense_data) {
                        $s_registration = 'submission';
                        // $this->a_page_data['body'] = $this->load->view('thesis/student/final_submission', $this->a_page_data, true);
                    }
                    else if (in_array($this->session->userdata('student_id'), $a_open_door)) {
                        $s_registration = 'submission';
                    }
                    else {
                        $this->a_page_data['message_error'] = 'Your thesis defense data not found!';
                        $s_registration = 'not allowed';
                    }
                }
                else if ($mba_thesis_data[0]->current_progress == 'proposal') {
                    $this->a_page_data['message_error'] = 'Your thesis defense data not found!';
                    $s_registration = 'not allowed';
                }
            }
            else {
                $this->a_page_data['message_error'] = 'Sorry, Your thesis data not found!';
                $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
            }
            $this->load->view('layout', $this->a_page_data);
            
            // if ($s_registration == 'submission') {
            //     $this->a_page_data['body'] = $this->load->view('thesis/student/final_submission', $this->a_page_data, true);
            //     // $this->a_page_data['body'] = maintenance_page(true);
            // }
            // else if ($s_registration == 'view') {
            //     $this->a_page_data['body'] = $this->load->view('thesis/student/thesis_student', $this->a_page_data, true);
            //     // $this->a_page_data['body'] = maintenance_page(true);
            // }
            // else {
            //     $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
            // }

            // if ($option_mode == 'detail') {
            //     $this->a_page_data['body'] = $this->load->view('thesis/student/thesis_student', $this->a_page_data, true);
            // }
            // else if ($option_mode == 'customize') {
            //     $this->a_page_data['body'] = $this->load->view('thesis/student/final_submission', $this->a_page_data, true);
            // }
            
            // // $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
            // $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    public function final_submission($option_mode = 'detail')
    {
        $mba_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $this->session->userdata('student_id')));
        if ($mba_student_data) {
            $mba_thesis_data = $this->Tm->get_student_list_thesis([
                'ts.student_id' => $this->session->userdata('student_id')
            ]);

            $mba_thesis_log_data = false;
            $mba_thesis_advisor_approved_1 = false;
            $mba_thesis_advisor_approved_2 = false;

            if ($mba_thesis_data) {
                $o_thesis_data = $mba_thesis_data[0];
                $mba_thesis_log_data = $this->Tm->get_thesis_log($o_thesis_data->thesis_student_id, [
                    'tsl.thesis_log_type' => 'final'
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
            $this->a_page_data['thesis_page_type'] = 'final_submission';
            $this->a_page_data['thesis_log_data'] = $mba_thesis_log_data;
            $this->a_page_data['advisor_approved_1'] = $mba_thesis_advisor_approved_1;
            $this->a_page_data['advisor_approved_2'] = $mba_thesis_advisor_approved_2;
            $this->a_page_data['o_student_data'] = $mba_student_data[0];

            // $this->a_page_data['message_error'] = 'not allowed!';
            $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
            if ($mba_thesis_data) {
                $this->a_page_data['allow_update'] = false;
                if ($mba_thesis_data[0]->current_progress == 'work') {
                    if (in_array($mba_thesis_data[0]->current_status, ['approved', 'approved_hsp'])) {
                        $this->a_page_data['allow_update'] = true;
                    }
                }
                else if ($mba_thesis_data[0]->current_progress == 'final') {
                    if (($mba_thesis_data[0]->current_status == '') OR (is_null($mba_thesis_data[0]->current_status))) {
                        $this->a_page_data['allow_update'] = true;
                    }
                    else if ($mba_thesis_data[0]->current_status == 'pending') {
                        $this->a_page_data['allow_update'] = true;
                    }
                }
                // $this->a_page_data['body'] = $this->load->view('thesis/student/thesis_student', $this->a_page_data, true);
                // $this->a_page_data['body'] = maintenance_page(true);
                // if (($_SERVER['REMOTE_ADDR'] == '202.93.225.254') AND ($this->session->userdata('student_id') == '92d444c1-15e7-49df-8301-110892295476')) {
                // if (($_SERVER['REMOTE_ADDR'] == '120.188.33.253') AND ($this->session->userdata('student_id') == '12962f40-2405-4756-bae2-2c214ceb0c5d')) {
                    $this->a_page_data['defense_data'] = $this->Tm->get_thesis_defense_student(['st.student_id' => $this->session->userdata('student_id')]);
                    if ($option_mode == 'detail') {
                        $this->a_page_data['body'] = $this->load->view('thesis/student/thesis_student', $this->a_page_data, true);
                    }
                    else if ($option_mode == 'customize') {
                        $this->a_page_data['body'] = $this->load->view('thesis/student/final_submission', $this->a_page_data, true);
                    }
                // }
            }
            else {
                $this->a_page_data['message_error'] = 'Sorry, your thesis not found!';
                $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
            }
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    public function work_submission($option_mode = 'detail')
    {
        $mba_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $this->session->userdata('student_id')));
        if ($mba_student_data) {
            $mba_thesis_data = $this->Tm->get_student_list_thesis([
                'ts.student_id' => $this->session->userdata('student_id')
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

            // $this->a_page_data['message_error'] = 'not allowed!';
            $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
            if ($mba_thesis_data) {
                $this->a_page_data['allow_update'] = false;
                if ($mba_thesis_data[0]->current_progress == 'proposal') {
                    if (in_array($mba_thesis_data[0]->current_status, ['approved', 'approved_hsp'])) {
                        $this->a_page_data['allow_update'] = true;
                    }
                }
                else if ($mba_thesis_data[0]->current_progress == 'work') {
                    if (($mba_thesis_data[0]->current_status == '') OR (is_null($mba_thesis_data[0]->current_status))) {
                        $this->a_page_data['allow_update'] = true;
                    }
                    else if ($mba_thesis_data[0]->current_status == 'pending') {
                        $this->a_page_data['allow_update'] = true;
                    }
                }
                // $this->a_page_data['body'] = $this->load->view('thesis/student/thesis_student', $this->a_page_data, true);
                // $this->a_page_data['body'] = maintenance_page(true);
                // if (($_SERVER['REMOTE_ADDR'] == '202.93.225.254') AND ($this->session->userdata('student_id') == '92d444c1-15e7-49df-8301-110892295476')) {
                // if (($_SERVER['REMOTE_ADDR'] == '120.188.33.253') AND ($this->session->userdata('student_id') == '12962f40-2405-4756-bae2-2c214ceb0c5d')) {
                    if ($option_mode == 'detail') {
                        $this->a_page_data['defense_data'] = $this->Tm->get_thesis_defense_student(['st.student_id' => $this->session->userdata('student_id')]);
                        $this->a_page_data['body'] = $this->load->view('thesis/student/thesis_student', $this->a_page_data, true);
                    }
                    else if ($option_mode == 'customize') {
                        $this->a_page_data['body'] = $this->load->view('thesis/student/work_submission', $this->a_page_data, true);
                    }
                // }
            }
            else {
                $this->a_page_data['message_error'] = 'Sorry, please upload proposal submission first!';
                $this->a_page_data['body'] = $this->load->view('student/periode_over', $this->a_page_data, true);
            }
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    public function test_tanggal()
    {
        $date_now = date('Y-m-d H:i:s');
        $date_start_registration = date('Y-m-d H:i:s', strtotime('2022-07-03 00:00:00'));
        $date_end_registration = date('Y-m-d H:i:s', strtotime('2022-07-10 23:59:59'));

        print($date_now.'<br>');
        print($date_start_registration.'<br>');
        print($date_end_registration.'<br>');
        if (($date_now > $date_start_registration) AND ($date_now < $date_end_registration)) {
            print('masuk periode');
        }
        else {
            print('diluar periode');
        }
        exit;
    }
}
