<?php
class Finance extends App_core
{
    function __construct()
    {
        parent::__construct('student_finance');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Semester_model', 'Sem');
        $this->load->model('finance/Invoice_model', 'Inm');
        $this->load->model('finance/Bni_model', 'Bnm');
        $this->load->model('finance/Finance_model', 'Fim');
        $this->load->model('academic/Academic_year_model', 'Aym');
    }

    function dashboard() {
        $s_personal_data_id = $this->session->userdata('user');
        $mbo_student_data = $this->Stm->get_student_filtered([
            'ds.personal_data_id' => $s_personal_data_id,
        ]);

        if ($mbo_student_data) {
            $this->a_page_data['personal_data_id'] = $s_personal_data_id;
            $this->a_page_data['o_student_data'] = $mbo_student_data[0];
        }

        $this->a_page_data['reminder'] = modules::run('callback/api/get_reminder_billing_teks_student', $this->session->userdata('student_id'));
		$this->a_page_data['body'] = $this->load->view('student/finance/dashboard', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
    }

    function get_list_billing($s_student_id, $b_get_page = false) {
        $mbo_student_data = $this->Stm->get_student_filtered([
            'ds.student_id' => $s_student_id
        ]);
        if ($mbo_student_data) {
            $o_student = $mbo_student_data[0];
            $mba_billing_fee = $this->Inm->get_student_billing([
                'di.personal_data_id' => $o_student->personal_data_id
            ], 'fee.payment_type_code');
            if ($mba_billing_fee) {
                foreach ($mba_billing_fee as $o_billing) {
                    $a_billing_detail_data = modules::run('callback/api/get_list_billing', $o_student->student_id, $o_billing->payment_type_code);
                    $o_billing->billing_detail = $a_billing_detail_data;
                }
            }
            // 
            $this->a_page_data['student_data'] = $o_student;
            $this->a_page_data['billing_list'] = $mba_billing_fee;
            $this->a_page_data['body'] = $this->load->view('student/finance/form/list_va', $this->a_page_data, true);
            if (!$b_get_page) {
                $this->load->view('layout', $this->a_page_data);
            }
            else {
                return $this->a_page_data['body'];
            }
        }
        else {
            return '';
        }
    }

    function payment_history($s_student_id = false) {
        if (!$s_student_id) {
            $s_student_id = $this->session->userdata('student_id');
        }

        $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->session->userdata('student_id')]);
        if ($mba_student_data) {
            $o_student = $mba_student_data[0];
            $this->a_page_data['student_data'] = $o_student;
            $this->a_page_data['body'] = $this->load->view('student/finance/payment_history', $this->a_page_data, true);
		    $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    function unpaid_invoice() {
        $s_personal_data_id = $this->session->userdata('user');
        $s_personal_data_id = $this->session->userdata('user');
        $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->session->userdata('student_id')]);
        if ($mba_student_data) {
            $o_student = $mba_student_data[0];
            $this->a_page_data['student_data'] = $o_student;
            $this->a_page_data['body'] = $this->load->view('student/finance/invoice_list', $this->a_page_data, true);
		    $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    function get_unpaid_invoice() {
        if($this->input->is_ajax_request()){
            $s_personal_data_id = $this->session->userdata('user');
            $current_time = date('Y-m-d H:i:s');
            $mba_invoice_list = $this->Inm->get_unpaid_invoice_full(['di.personal_data_id' => $s_personal_data_id]);
            if ($mba_invoice_list) {
                foreach ($mba_invoice_list as $o_invoice) {
                    $mbo_invoice_fullpayment = $this->Inm->get_invoice_full_payment($o_invoice->invoice_id);
                    $mba_invoice_installment = $this->Inm->get_invoice_installment($o_invoice->invoice_id);

                    $d_amount_billed = 0;
                    $d_amount_fined = 0;
                    $d_amount_paid = 0;
                    $d_amount_total = 0;

                    $invoice_due_date = ($mbo_invoice_fullpayment) ? $mbo_invoice_fullpayment->sub_invoice_details_real_datetime_deadline : $mba_invoice_installment[0]->sub_invoice_details_real_datetime_deadline;
                    
                    $b_has_installment = false;
                    if ($mbo_invoice_fullpayment) {
                        if ($current_time > date('Y-m-d H:i:s', strtotime($invoice_due_date))) {
                            if ($mba_invoice_installment) {
								$b_has_installment = true;
							}
                        }
                    }
                    if (($mba_invoice_installment) AND (!$b_has_installment)) {
						foreach ($mba_invoice_installment as $o_installment) {
							if ($o_installment->sub_invoice_details_amount_paid > 0) {
								$b_has_installment = true;
							}
						}
					}

                    // if ($o_invoice->payment_type_code == '02') {
                        if (!$b_has_installment) {
                            $d_amount_billed = $mbo_invoice_fullpayment->sub_invoice_details_amount;
                            $d_amount_fined = $mbo_invoice_fullpayment->sub_invoice_details_amount_fined;
                            $d_amount_paid = $mbo_invoice_fullpayment->sub_invoice_details_amount_paid;
                            $d_amount_total = $d_amount_billed + $d_amount_fined - $d_amount_paid;
                        }
                        else {
                            if ($mba_invoice_installment) {
                                foreach ($mba_invoice_installment as $o_installment) {
                                    $d_amount_billed += $o_installment->sub_invoice_details_amount;
                                    $d_amount_paid += $o_installment->sub_invoice_details_amount_paid;
                                    $d_amount_fined += $o_installment->sub_invoice_details_amount_fined;
                                }
                            }
                            $d_amount_total = $d_amount_billed + $d_amount_fined - $d_amount_paid;
                        }
                    // }

                    $o_invoice->invoice_amount_billed = $d_amount_billed;
                    $o_invoice->invoice_amount_fined = $d_amount_fined;
                    $o_invoice->invoice_amount_paid = $d_amount_paid;
                    $o_invoice->invoice_amount_total = $d_amount_total;
                    $o_invoice->has_installment = $b_has_installment;
                }
            }
			print json_encode(array('data' => $mba_invoice_list));
			exit;
		}
    }

    function get_payment_student() {
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->input->post('personal_data_id');
            $mba_data = $this->Fim->get_payment_history([
                'bt.transaction_type' => 'paymentnotification',
                'btp.personal_data_id' => $s_personal_data_id
            ], [
                'bt.datetime_payment' => 'DESC'
            ], ['bt.bni_transactions_id']);
            print json_encode(['data' => $mba_data]);
        }
    }

    function get_payment_detail() {
        if ($this->input->is_ajax_request()) {
            $s_bni_transaction_id = $this->input->post('bni_id');

            $mba_data = $this->Fim->get_payment_history([
                'bt.transaction_type' => 'paymentnotification',
                'btp.bni_transactions_id' => $s_bni_transaction_id
            ], [
                'sid.sub_invoice_details_real_datetime_deadline' => 'ASC'
            ]);

            print json_encode(['data' => $mba_data]);
        }
    }

    public function aid()
    {
        $mbo_period_aid_active = $this->Fim->get_aid_period_list([
            'aid_period_status' => 'active'
        ])[0];

        if ($mbo_period_aid_active) {
            $date_now = date('Y-m-d H:i:s');
            $s_period_start_registration = date('Y-m-d H:i:s', strtotime($mbo_period_aid_active->aid_period_datetime_start));
            $s_period_end_registration = date('Y-m-d H:i:s', strtotime($mbo_period_aid_active->aid_period_datetime_end));

            if (($date_now > $s_period_start_registration) AND ($date_now < $s_period_end_registration)) {
                $s_date_period = $mbo_period_aid_active->aid_period_year.'-'.$mbo_period_aid_active->aid_period_month.'-10 23:59:59';
                $datetime_period = date('Y-m-d H:i:s', strtotime($s_date_period));
                $month_period = $mbo_period_aid_active->aid_period_month;

                $s_personal_data_id = $this->session->userdata('user');
                $s_student_id = $this->session->userdata('student_id');

                $mba_has_registration = $this->Fim->get_request_aid([
                    'sta.personal_data_id' => $s_personal_data_id,
                    'sta.aid_period_id' => $mbo_period_aid_active->aid_period_id
                ]);

                if ($mba_has_registration) {
                    // sudah daftar
                    $this->a_page_data['registration_data'] = $mba_has_registration[0];
                    $this->a_page_data['period_data'] = $mbo_period_aid_active;
                    $this->a_page_data['student_id'] = $s_student_id;
                    $this->a_page_data['registration_files'] = $this->General->get_where('dt_student_aid_files', [
                        'request_id' => $mba_has_registration[0]->request_id
                    ]);
                    $this->a_page_data['body'] = $this->load->view('student/finance/aid_success', $this->a_page_data, true);
                }else{
                    $this->a_page_data['aid_period_id'] = $mbo_period_aid_active->aid_period_id;
                    $this->a_page_data['a_bank_list'] = $this->Fim->get_list_bank();
                    
                    // $s_personal_data_id = '0bc014f1-313d-493e-ac82-a41aaa46f29e';
                    // $s_student_id = 'c99c2948-e694-484e-a620-d68d05adb450';

                    $o_active_semester = $this->Sem->get_active_semester();
                    $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
                    $mbo_student_semester = $this->Sem->get_semester_student($s_student_id, [
                        'ss.academic_year_id' => $o_active_semester->academic_year_id,
                        'ss.semester_type_id' => $o_active_semester->semester_type_id
                    ])[0];

                    if (($mbo_student_semester) AND ($mbo_student_data)) {
                        $this->a_page_data['student_data'] = $mbo_student_data;
                        $s_semester_id = (strlen($mbo_student_semester->semester_id) == 1) ? str_pad($mbo_student_semester->semester_id, 2, "0", STR_PAD_LEFT) : $mbo_student_semester->semester_id; 

                        if ($mbo_student_data->student_status == 'active') {

                            $fee_data = $this->General->get_where('dt_fee', [
                                'semester_id' => $mbo_student_semester->semester_id,
                                'payment_type_code' => '02',
                                'study_program_id' => $mbo_student_data->study_program_id
                            ]);

                            $mbs_student_invoice_id = false;

                            if ($fee_data) {
                                foreach ($fee_data as $o_fee) {
                                    $mbo_invoice_student = $this->Inm->student_has_invoice_fee_id($mbo_student_data->personal_data_id, $o_fee->fee_id);

                                    if ($mbo_invoice_student) {
                                        // array_push($mba_student_invoice, $mbo_invoice_student);
                                        $mbs_student_invoice_id = $mbo_invoice_student->invoice_id;
                                    }
                                }
                            }

                            if ($mbs_student_invoice_id) {
                                $mba_sub_invoice = $this->General->get_where('dt_sub_invoice', ['invoice_id' => $mbs_student_invoice_id]);
                                
                                if ($mba_sub_invoice) {
                                    $b_accepted_refund = false;

                                    foreach ($mba_sub_invoice as $key => $o_sub_invoice) {
                                        $mba_sub_invoice_details = $this->General->get_where('dt_sub_invoice_details', [
                                            'sub_invoice_id' => $o_sub_invoice->sub_invoice_id
                                        ]);

                                        $mba_sub_invoice_details_paid_in_period = $this->General->get_where('dt_sub_invoice_details', [
                                            'sub_invoice_id' => $o_sub_invoice->sub_invoice_id,
                                            'sub_invoice_details_datetime_paid_off  <= ' => $datetime_period
                                        ]);

                                        if ($mba_sub_invoice_details) {

                                            if ($o_sub_invoice->sub_invoice_type == 'full') {
                                                if ($mba_sub_invoice_details[0]->sub_invoice_details_status == 'paid') {
                                                    $b_accepted_refund = true;
                                                    break;
                                                }
                                            }else{
                                                $i_total_installment = count($mba_sub_invoice_details);
                                                $i_counter_paid_installment = 0;
                                                $i_installment_period = 0;
                                                $b_installment_period_paid = false;

                                                foreach ($mba_sub_invoice_details as $o_sub_invoice_details) {
                                                    $month_real_deadline = date('m', strtotime($o_sub_invoice_details->sub_invoice_details_real_datetime_deadline));
                                                    $i_installment_number = substr($o_sub_invoice_details->sub_invoice_details_va_number, 8, 1);

                                                    if (($o_sub_invoice_details->sub_invoice_details_amount_total > 0) AND ($o_sub_invoice_details->sub_invoice_details_amount_total == $o_sub_invoice_details->sub_invoice_details_amount_paid)) {
                                                        $i_counter_paid_installment++;

                                                        if ($month_real_deadline == $month_period) {
                                                            $b_installment_period_paid = true;
                                                        }
                                                    }

                                                    if ($month_real_deadline == $month_period) {
                                                        $i_installment_period = $i_installment_number;
                                                    }
                                                }

                                                // opsi harus lunas installment sebelumnya
                                                if ($i_counter_paid_installment >= $i_installment_period) {
                                                    $b_accepted_refund = true;
                                                    break;
                                                }

                                                // opsi lunas hanya installment yang ditentukan
                                                if ($b_installment_period_paid) {
                                                    $b_accepted_refund = true;
                                                    break;
                                                }

                                            }
                                            
                                        }else{
                                            $this->a_page_data['page_error'] = 'Student Aid';
                                            $this->a_page_data['body'] = $this->load->view('dashboard/student_error', $this->a_page_data, true);
                                            break;
                                        }

                                        // print('<pre>');
                                        // var_dump($mba_sub_invoice_details_paid_in_period);

                                        if ($b_accepted_refund) {
                                            break;
                                        }

                                    }

                                    if ($b_accepted_refund) {
                                        $this->a_page_data['body'] = $this->load->view('student/finance/student_aid', $this->a_page_data, true);
                                    }else{
                                        $this->a_page_data['body'] = 'You are not eligible for the aid refund, because you paid the tuition fee after the given deadline (10th of every month), or you have not yet paid the installment of your tuition fee.';
                                    }
                                }else{
                                    $this->a_page_data['page_error'] = 'Student Aid';
                                    $this->a_page_data['body'] = $this->load->view('dashboard/student_error', $this->a_page_data, true);
                                }
                            }
                            else{
                                $this->a_page_data['body'] = '-';
                            }

                        }
                        else {
                            $this->a_page_data['body'] = $this->load->view('dashboard/student_error', $this->a_page_data, true);
                        }

                    }else{
                        $this->a_page_data['page_error'] = 'Student Aid';
                        $this->a_page_data['body'] = $this->load->view('dashboard/student_error', $this->a_page_data, true);
                    }
                }
            }else{
                $this->a_page_data['body'] = $this->load->view('periode_over', $this->a_page_data, true);
            }
        }else{
            // $this->a_page_data['page_error'] = 'Student Aid';
            // $this->a_page_data['body'] = $this->load->view('dashboard/student_error', $this->a_page_data, true);
            $this->a_page_data['body'] = $this->load->view('periode_over', $this->a_page_data, true);
        }
        
        $this->load->view('layout', $this->a_page_data);
    }

    public function check_accepted_aid()
    {
        # code...
    }

    // public function repair_deadline()
    // {
    //     $mba_sub_invoice_details = $this->General->get_where('dt_sub_invoice_details');
    //     if ($mba_sub_invoice_details) {
    //         foreach ($mba_sub_invoice_details as $key => $o_sub_invoice_details) {
    //             if (!is_null($o_sub_invoice_details->trx_id)) {

    //                 $mba_bni_transaction_data = $this->Fim->get_bni_transactions([
    //                     'virtual_account' => $o_sub_invoice_details->sub_invoice_details_va_number
    //                 ], 'bnt.datetime_expired');

    //                 if ($mba_bni_transaction_data) {
    //                     $date_deadline = date('Y-m-d H:i:s', strtotime($o_sub_invoice_details->sub_invoice_details_deadline));
    //                     $date_real_deadline = date('Y-m-d H:i:s', strtotime($mba_bni_transaction_data[0]->datetime_expired . "-10 days"));
    //                     if ($date_deadline < $date_real_deadline) {
    //                         $date_real_deadline = $date_deadline;
    //                     }

    //                     $a_sub_invoice_details_data = [
    //                         'sub_invoice_details_real_datetime_deadline' => $date_real_deadline
    //                     ];
    //                     $this->Inm->update_sub_invoice_details($a_sub_invoice_details_data, [
    //                         'sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id
    //                     ]);
                        
    //                     print($o_sub_invoice_details->sub_invoice_details_va_number.'<br>');
    //                 }
    //             }else{
    //                 $date_real_deadline = $o_sub_invoice_details->sub_invoice_details_deadline;

    //                 $a_sub_invoice_details_data = [
    //                     'sub_invoice_details_real_datetime_deadline' => $date_real_deadline
    //                 ];
    //                 $this->Inm->update_sub_invoice_details($a_sub_invoice_details_data, [
    //                     'sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id
    //                 ]);
                    
    //                 print($o_sub_invoice_details->sub_invoice_details_va_number.'<br>');
    //             }
    //         }
    //     }
    // }

    public function submit_refund_request()
    {
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->session->userdata('user');
            $s_aid_period_id = $this->input->post('aid_period_id');
            $mbo_aid_period_data = $this->General->get_where('dt_student_aid_setting', ['aid_period_id' => $s_aid_period_id])[0];
            // $s_date_period = date('Y-m-d', strtotime($s_datetime_period));

            $this->form_validation->set_rules('request_amount', 'Total Request Amount', 'trim|required');
            $this->form_validation->set_rules('bank_code', 'Name of Bank', 'required');
            // if ($this->input->post('bank_code') == '014') {
                $this->form_validation->set_rules('request_bank_branch', 'Branch', 'trim|required');
            // }
            $this->form_validation->set_rules('request_account_number', 'Bank Account Number', 'required');
            $this->form_validation->set_rules('request_beneficiary', 'Beneficiary Name', 'required');
            // $this->form_validation->set_rules('request_receipt_bill_file', 'Receipt Bill', 'required');

            if ($this->form_validation->run()) {
                $request_amount = str_replace(',', '', set_value('request_amount'));
                $b_has_uploaded_file = false;

                for ($i=0; $i < count($_FILES['request_receipt_bill_file']['name']); $i++) { 
                    if (!empty($_FILES['request_receipt_bill_file']['name'][$i])){
                        $b_has_uploaded_file = true;
                    }
                }

                if (doubleval($request_amount) > 500000) {
                    $a_return = array('code' => 1, 'message' => '<span>Max amount request 500.000</span>');
                }else if (!$b_has_uploaded_file) {
                    $a_return = array('code' => 1, 'message' => 'Please upload Receipt Bill of Internet Provider files');
                }else{
                    $s_file_path = APPPATH.'uploads/'.$s_personal_data_id.'/receipt_bill/'.$mbo_aid_period_data->aid_period_year.'_'.$mbo_aid_period_data->aid_period_month;
					
					$config['allowed_types'] = 'jpg|png|pdf|bmp|jpeg';
		            $config['max_size'] = 102400;
		            $config['encrypt_name'] = true;
		            $config['file_ext_tolower'] = true;
					$config['upload_path'] = $s_file_path;
					
					if(!file_exists($s_file_path)){
						mkdir($s_file_path, 0755, true);
                    }
                    
					$this->load->library('upload', $config);
                    $i_count_upload = 0;
                    $a_files = array();

                    for ($i=0; $i < count($_FILES['request_receipt_bill_file']['name']); $i++) { 
                        if (!empty($_FILES['request_receipt_bill_file']['name'][$i])) {
                            $_FILES['file_request_receipt_bill']['name'] = $_FILES['request_receipt_bill_file']['name'][$i];
                            $_FILES['file_request_receipt_bill']['type'] = $_FILES['request_receipt_bill_file']['type'][$i];
                            $_FILES['file_request_receipt_bill']['tmp_name'] = $_FILES['request_receipt_bill_file']['tmp_name'][$i];
                            $_FILES['file_request_receipt_bill']['error'] = $_FILES['request_receipt_bill_file']['error'][$i];
                            $_FILES['file_request_receipt_bill']['size'] = $_FILES['request_receipt_bill_file']['size'][$i];

                            if($this->upload->do_upload('file_request_receipt_bill')) {
                                $upload_data = $this->upload->data();

                                $data['request_receipt_bill_file'] = $upload_data['file_name'];
                                $data['request_receipt_bill_file_mime'] = $upload_data['file_type'];
                                array_push($a_files, $data);
                                $i_count_upload++;
                            }
                        }
                    }

                    if ($i_count_upload > 0) {
                        $s_request_id = $this->uuid->v4();
                        $a_data_save = [
                            'request_id' => $s_request_id,
                            'personal_data_id' => $s_personal_data_id,
                            'bank_code' => set_value('bank_code'),
                            'aid_period_id' => $s_aid_period_id,
                            'request_amount' => $request_amount,
                            'request_bank_branch' => set_value('request_bank_branch'),
                            'request_account_number' => set_value('request_account_number'),
                            'request_beneficiary' => set_value('request_beneficiary')
                        ];

                        if ($this->Fim->save_request_aid($a_data_save)) {
                            foreach ($a_files as $files_data) {
                                $a_data_file = [
                                    'request_id' => $s_request_id,
                                    'request_receipt_bill_file' => $files_data['request_receipt_bill_file'],
                                    'request_receipt_bill_file_mime' => $files_data['request_receipt_bill_file_mime']
                                ];

                                $this->Fim->save_aid_file($a_data_file);
                            }
                            $a_return = ['code' => 0, 'message' => 'Success!'];

                            // $this->email->from('employee@company.ac.id');
                            // $this->email->to('employee@company.ac.id');
                            // $this->email->subject('Testing');
                            // $this->email->message('Body email');

                            // $this->email->send();
                            // if ($this->email->send()) {
                            //     print('terkirim');
                            // }
                        }else{
                            $a_return = ['code' => 1, 'message' => 'Unable save request!'];
                        }
                    }else{
                        $a_return = ['code' => 1, 'message' => 'Something went wrong, please contact IT Dept.'];
                    }
                    
                }

            }else{
                $a_return = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
            }

            print json_encode($a_return);
        }
    }
}
