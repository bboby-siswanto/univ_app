<?php
class Repeat extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('personal_data/Family_model', 'Fmm');
    }

    // public function submit_new_repeat_registration()
    // {
    //     //  MOHAMED HAAZIM (MEE/2020)
    //     $s_student_id = 'e6d16031-2c08-4bd0-b5cb-3bf72ca2130a';

    //     $mbo_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id])[0];

    //     if ($mbo_student_data) {
    //         $a_score_id = ['372ea98e-f5bb-4ab9-9f41-007da6ef8894','24f7cfa5-f267-4114-9b2e-10e9995943b7'];

    //         $s_bill = count($a_score_id) * 400000;
    //         $a_create_invoice = modules::run('finance/Invoice/create_student_invoice', 
    //             '03',
    //             '1', //semester_id
    //             0,
    //             'student',
    //             $mbo_student_data->student_number,
    //             $mbo_student_data->personal_data_id,
    //             'MOHAMED HAAZIM', //name
    //             '2021-01-30',
    //             'Repetition Examination Fee for 2 subject(s)',
    //             $s_bill,
    //             $mbo_student_data->finance_year_id
    //         );

    //         $a_param_email = array(
    //             's_student_id' => $mbo_student_data->student_id,
    //             'a_score_id' => $a_score_id,
    //             'va_number' => $a_create_invoice['va_number'],
    //             'bill' => $s_bill
    //         );
            
    //         $a_return = $this->send_confirmation_email($a_param_email);
    //         print('<pre>');
    //         var_dump($a_return);exit;
    //     }else{
    //         print('zonk');
    //     }
    // }

    function get_repeat() {
        $o_semester_active = $this->Smm->get_active_semester();
        $repeat_end = $o_semester_active->repetition_registration_end_date;
        $s_deadline = date('Y-m-d 23:59:59', strtotime($repeat_end." +7 day"));
        // $valid_registration = modules::run('academic/semester/checker_semester_academic', 'repetition_registration_start_date', 'repetition_registration_end_date', $o_semester_active->academic_year_id, $o_semester_active->semester_type_id);
        print('<pre>');var_dump($s_deadline);exit;
    }

    public function registration($s_student_id = false)
    {
        $o_semester_active = $this->Smm->get_active_semester();
        if ($this->input->is_ajax_request()) {
            $valid_registration = modules::run('academic/semester/checker_semester_academic', 'repetition_registration_start_date', 'repetition_registration_end_date', $o_semester_active->academic_year_id, $o_semester_active->semester_type_id);
            $s_student_id = $this->input->post('student_id');
            $a_score_id = $this->input->post('score_id');
            if ($this->session->userdata('student_id') == '4cea2049-4b45-4ba4-aca2-34c46cb3519b') {
                $valid_registration = true;
            }

            if (!$valid_registration) {
                $a_return = array('code' => 1, 'message' => 'invalid registration period!!');
            }
            else if (count($a_score_id) > 0) {
                $o_student_data = $this->Stm->get_student_by_id($s_student_id);
                $o_score_data = $this->Scm->get_score_by_id($a_score_id[0])[0];
                $d_now = date('Y-m-d H:i:s');
                $s_deadline = date('Y-m-d 23:59:59', strtotime($d_now." +7 day"));
                if (!is_null($o_semester_active->repetition_registration_end_date)) {
                    $s_deadline = date('Y-m-d 23:59:59', strtotime($o_semester_active->repetition_registration_end_date." +1 day"));
                }

                $s_bill = count($a_score_id) * 400000;
                
                $a_create_invoice = modules::run('finance/Invoice/create_student_invoice', 
                    '03',
                    $o_score_data->semester_id,
                    0,
                    'student',
                    $o_student_data->student_number,
                    $o_student_data->personal_data_id,
                    $o_student_data->personal_data_name,
                    $s_deadline,
                    'Repetition Examination Fee for '.count($a_score_id).' subject(s)',
                    $s_bill,
                    $o_student_data->finance_year_id,
                    false,
                    $o_student_data->program_id,
                    $o_score_data->academic_year_id,
                    $o_score_data->semester_type_id
                );

                // var_dump($a_create_invoice);exit;

                if ($a_create_invoice['code'] == 0) {
                    
                    foreach ($a_score_id as $s_score_id) {
                        
                        $this->Scm->save_data(array(
                            'score_mark_for_repetition' => 1
                        ), array('score_id' => $s_score_id));
                    }

                    $a_param_email = array(
                        's_student_id' => $s_student_id,
                        'a_score_id' => $a_score_id,
                        'va_number' => $a_create_invoice['va_number'],
                        'deadline' => $s_deadline,
                        'bill' => $s_bill
                    );
                    
                    $a_return = $this->send_confirmation_email($a_param_email);
                }else{
                    $a_return = $a_create_invoice;
                }
                
            }else{
                $a_return = array('code' => 1, 'message' => 'Please select one or more subject for repeat!');
            }
            
            print json_encode($a_return);
        }else{
            $this->a_page_data['valid_registration'] = modules::run('academic/semester/checker_semester_academic', 'repetition_registration_start_date', 'repetition_registration_end_date', $o_semester_active->academic_year_id, $o_semester_active->semester_type_id);

            if (!$s_student_id) {
                $s_student_id = $this->session->userdata('student_id');
            }

            if ($this->session->userdata('student_id') == '4cea2049-4b45-4ba4-aca2-34c46cb3519b') {
                $this->a_page_data['valid_registration'] = true;
            }
            
            // if (!$this->a_page_data['valid_registration']) {
            //     $this->a_page_data['body'] = $this->load->view('periode_over', $this->a_page_data, true);
            // }else{
                $mba_subject_repeat = $this->Scm->get_score_student($s_student_id, array(
                    'score_mark_for_repetition != ' => null,
                    // 'sc.academic_year_id' => $o_semester_active->academic_year_id,
                    // 'sc.semester_type_id' => $o_semester_active->semester_type_id
                    'sc.academic_year_id' => '2023',
                    'sc.semester_type_id' => '2'
                ));

                // if ($this->session->userdata('student_id') == '116284a9-6d67-469f-85c0-89ba4fa60a47') {
                //     $mba_subject_repeat = false;
                // }
    
                // print('<pre>');
                // var_dump($s_student_id);exit;
                if ($mba_subject_repeat) {
                    $this->a_page_data['subject_repeat'] = $mba_subject_repeat;
                    $this->a_page_data['body'] = $this->load->view('repeat/after_registration', $this->a_page_data, true);
                }
                else if (!$this->a_page_data['valid_registration']) {
                    $this->a_page_data['body'] = $this->load->view('periode_over', $this->a_page_data, true);
                }
                else{
                    $this->a_page_data['semester_type_id'] = '2';
                    $this->a_page_data['semester_type_name'] = $o_semester_active->semester_type_name;
                    // $this->a_page_data['academic_year_id'] = $o_semester_active->academic_year_id;
                    $this->a_page_data['academic_year_id'] = '2023';
                    $this->a_page_data['student_id'] = ($s_student_id) ? $s_student_id : $this->session->userdata('student_id');
                    $this->a_page_data['body'] = $this->load->view('repeat/registration', $this->a_page_data, true);
                }
            // }

            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function force_push_create_invoice_repeat()
    {
        $a_create_invoice = modules::run('finance/Invoice/create_student_invoice', 
            '03',
            1,
            0,
            'student',
            '11202102016',
            '53aea85d-47e9-41c1-8ed4-8938151b93cb',
            'SHINTA JASMINE STELLA INDRIANI',
            '2022-02-05',
            'Repetition Examination Fee for 1 subject(s)',
            '400000',
            2021,
            false,
            1
        );
    }

    public function push_send_confirmation_email()
    {
        // $a_param_email = array(
        //     's_student_id' => '116284a9-6d67-469f-85c0-89ba4fa60a47',
        //     'a_score_id' => ['99b09772-2526-469d-a5aa-928b2661c7ee'],
        //     'va_number' => '8310030601190215',
        //     'bill' => '400000'
        // );
        
        // $a_return = $this->send_confirmation_email($a_param_email);
    }

    public function send_confirmation_email($a_data)
    {
        // if (!$a_data) {
        //     $a_data['s_student_id'] = '900530fd-592d-438f-9da2-28c57d1872f8';
        //     $a_data['a_score_id'] = array('8aa14a6d-38c1-4933-86bb-2d3c43d5b765');
        //     $a_data['va_number'] = '8310030601170120';
        //     $a_data['bill'] = 400000;
        // }

        $o_student_data = $this->Stm->get_student_by_id($a_data['s_student_id']);
        $mbo_family_data = $this->Fmm->get_family_by_personal_data_id($o_student_data->personal_data_id);
        $a_email_cc = array('employee@company.ac.id');

        if ($mbo_family_data) {
            $mba_parent_data = $this->Fmm->get_family_lists_filtered(array(
                'fmm.family_id' => $mbo_family_data->family_id,
                'fmm.family_member_status != ' => 'child'
            ));

            if ($mba_parent_data) {
                foreach ($mba_parent_data as $o_parents) {
                    if (strpos($o_parents->personal_data_email, '@') !== false) {
                        if (!in_array($o_parents->personal_data_email, $a_email_cc)) {
                            array_push($a_email_cc, $o_parents->personal_data_email);
                        }
                    }
                }
            }
        }

        $s_email_body = $this->email_body_repetition($a_data, $o_student_data);

        $email_subject = 'Confirmation of Subject Registration for Repetition';
        $this->load->library('email');
        // $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
        $s_email_to = $o_student_data->student_email;
        // if ($s_email_to == 'cylysce.widjaja@stud.iuli.ac.id') {
        //     $s_email_to = 'employee@company.ac.id';
        //     $a_email_cc = ['budi.siswanto1450@gmail.com'];
        // }
        $this->email->to($s_email_to);
        $this->email->from('employee@company.ac.id', 'IULI Academic Services Centre');
        $this->email->cc($a_email_cc);
        $this->email->bcc('employee@company.ac.id');
        // $this->email->bcc(array('employee@company.ac.id', 'employee@company.ac.id', $this->config->item('email')['academic']['head']));

        $this->email->subject($email_subject);
        $this->email->message($s_email_body);
        if(!$this->email->send()){
            $this->log_activity('Email did not sent');
            $this->log_activity('Error Message: '.$this->email->print_debugger());
            
            $a_return = array('code' => 1, 'message' => 'Email not send to '.$s_email_to.' !');
            // print json_encode($a_return);exit;
        }else{
            $a_return = array('code' => 0, 'message' => 'Success');
        }
        $this->email->clear(TRUE);

        return $a_return;
    }

    public function email_body_repetition($a_data, $o_student_data)
    {
        $s_subject_list = '';
        $i_number = 0;

        foreach ($a_data['a_score_id'] as $s_score_id) {
            $o_score_details = $this->Scm->get_score_by_id($s_score_id)[0];
            $i_number++;
            $s_subject_list.= "{$i_number}. {$o_score_details->subject_name}\n";
        }
        
        $transfer_amount = "Rp. ".number_format($a_data['bill'], 0, ',', '.').",-";
        $vaNumber = implode(' ', str_split($a_data['va_number'], 4));
        $s_count_subject = count($a_data['a_score_id']);
        $s_deadline = date('d F Y', strtotime($a_data['deadline']));
        $email_subject = 'Confirmation of Subject Registration for Repetition';
        $s_filelink = base_url()."public/files/download/finance/Panduan-Pembayaran-Virtual-Account-BNI.pdf";

        $s_email_body = <<<TEXT
Dear {$o_student_data->personal_data_name} <{$o_student_data->student_email}>,

This email is to confirm your registration for Repetition. You have registered the following {$s_count_subject} subject(s):

{$s_subject_list}

Please transfer the repetition examination fee in the amount of {$transfer_amount} to
Account Holder Name: {$o_student_data->personal_data_name}
Bank: BNI 46
Virtual Account Number: {$vaNumber}
Deadline: {$s_deadline}

Terms and Conditions:
1. Please transfer the exact amount stated above.
2. Unmatched payment will be rejected by BNI.
3. Registration will be considered fail if the payment has not been received by the start of Repetition.
4. The repetition schedule will be announced in the IULI website a week before the start of the repetition week.
5. Should you have any inquiry regarding:
5.1. Registration, please email to employee@company.ac.id
5.2. Repetition Examination, please email to employee@company.ac.id
5.3. Payment, please email to employee@company.ac.id and if you have any payment difficulties, please download and read the manual provided: {$s_filelink}

Thank you for your registration and cooperation to transfer in the exact amount stated above before due.

Academic Services Centre
International University Liaison Indonesia - IULI.
TEXT;
        return $s_email_body;
    }
}
