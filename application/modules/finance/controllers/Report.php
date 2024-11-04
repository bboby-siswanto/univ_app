<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Report extends App_core
{
    public $month = [
        '1' => [7,8,9,10,11,12],
        '2' => [1,2,3,4,5,6],
    ];
    public $semestercurrent;
    function __construct()
    {
        parent::__construct('finance');
        $this->load->model('finance/Finance_model', 'Fm');
        $this->load->model('finance/Invoice_model', 'Im');
		$this->load->model('finance/Bni_model', 'Bm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('academic/Semester_model', 'Sem');

        $this->semestercurrent = $this->session->userdata('academic_year_id_active').'-'.$this->session->userdata('semester_type_id_active');
    }

    public function billing_report() {
        $this->a_page_data['body'] = $this->load->view('report/billing_fee', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function report_tuition_fee($s_semester = false) {
        // if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        //     $this->a_page_data['page_error'] = current_url();
        //     $this->a_page_data['body'] = $this->load->view('dashboard/maintenance_page', $this->a_page_data, true);
        //     $s_html = $this->load->view('layout', $this->a_page_data, true);
        //     echo $s_html;
        //     exit(4);
        // }

        if (!$s_semester) {
            $s_semester = $this->session->userdata('academic_year_id_active').'-'.$this->session->userdata('semester_type_id_active');
        }

        $a_semester = explode('-', $s_semester);
        $a_month = $this->month[$a_semester[1]];
        $i_semester = str_replace("-", "", $s_semester);
        $i_semestercurrent = str_replace("-", "", $this->semestercurrent);

        $mba_semester_data = $this->General->get_where('dt_semester_settings', [
            'academic_year_id' => $a_semester[0],
            'semester_type_id' => $a_semester[1]
        ]);
        $year = ($mba_semester_data) ? date('Y', strtotime($mba_semester_data[0]->semester_start_date)) : date('Y');

        $start_month = $a_month[0];
        $current_month = intval(date('m'));
        $d_current_month = $current_month.'-'.date('Y');
        if ($d_current_month != $current_month.'-'.$year) {
            $current_month = 0;
        }
        $prev_month = ($current_month > 0) ? $current_month - 1 : $a_month[count($a_month) - 1];

        $current_month_teks = ($current_month > 0) ? date('F Y', strtotime($year.'-'.$current_month.'-01')) : '';
        $prev_month_teks = ($prev_month > 0) ? date('F Y', strtotime($year.'-'.$prev_month.'-01')) : '';
        $start_month_teks = ($prev_month_teks != '') ? date('F Y', strtotime($year.'-'.$start_month.'-01')) : '';
        $prev_installment = (($prev_month_teks != '') AND ($start_month_teks != '')) ? $start_month_teks.' - '.$prev_month_teks : '';
        
        
        $this->a_page_data['semester_selected'] = $s_semester;
        $this->a_page_data['current_selected'] = $this->semestercurrent;
        $this->a_page_data['start_month'] = $start_month;
        $this->a_page_data['prev_month'] = $prev_month;
        $this->a_page_data['year_data'] = $year;
        $this->a_page_data['current_month'] = $current_month;
        
        $this->a_page_data['current_month_installment'] = $current_month_teks;
        $this->a_page_data['prev_installment'] = $prev_installment;
        $this->a_page_data['list_installment'] = $this->month[$a_semester[1]];
        $this->a_page_data['academic_semester'] = $this->Sem->get_semester_setting(['dss.academic_year_id > ' => 2021]);
        $this->a_page_data['semester_selected_body'] = $this->load->view('report/tuition_fee/semester_selected', $this->a_page_data, true);
        $this->a_page_data['summary_table'] = $this->load->view('report/tuition_fee/summary_table', $this->a_page_data, true);
        $this->a_page_data['body'] = $this->load->view('report/tuition_fee_report', $this->a_page_data, true);

        // if ($this->session->userdata('user') !== '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        //     $this->a_page_data['body'] = $this->load->view('dashboard/maintenance_page', $this->a_page_data, true);
        // }
        $this->load->view('layout', $this->a_page_data);
    }

    function get_current_semester($return_value = false, $s_student_status = 'active', $s_select_semester = false) {
        // $s_student_status = 'active';
        $s_select_semester = (!$s_select_semester) ? $this->session->userdata('academic_year_id_active').'-'.$this->session->userdata('semester_type_id_active') : $s_select_semester;
        if (!$return_value) {
            $s_student_status = $this->input->post('student_status');
            $s_select_semester = $this->input->post('semester_selected');
        }
        
        $a_semester = explode('-', $s_select_semester);
        $s_academic_year = $a_semester[0];
        $s_semester_type = $a_semester[1];

        $mba_invoice_list = $this->Im->get_invoice_by_deadline([
            'fee.payment_type_code' => '02',
            'di.academic_year_id' => $s_academic_year,
            'di.semester_type_id' => $s_semester_type,
        ]);
        if ($mba_invoice_list) {
            foreach ($mba_invoice_list as $key => $o_invoice) {
                if (is_array($s_student_status)) {
                    $mba_student_data = $this->Stm->get_student_filtered([
                        'ds.personal_data_id' => $o_invoice->personal_data_id
                    ], $s_student_status);
                }
                else {
                    $mba_student_data = $this->Stm->get_student_filtered([
                        'ds.personal_data_id' => $o_invoice->personal_data_id,
                        'ds.student_status' => $s_student_status
                    ]);
                }
                $mba_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                if (!$mba_student_data) {
                    unset($mba_invoice_list[$key]);
                }
                else if (!in_array($o_invoice->invoice_status, ['created', 'pending'])) {
                    unset($mba_invoice_list[$key]);
                }
                else if (($mba_invoice_fullpayment) AND ($mba_invoice_fullpayment->sub_invoice_details_status == 'paid')) {
                    unset($mba_invoice_list[$key]);
                }
                else {
                    $o_student = $mba_student_data[0];
                    $mba_invoice_installment = $this->Im->get_invoice_data([
                        'di.invoice_id' => $o_invoice->invoice_id,
                        'dsi.sub_invoice_type' => 'installment'
                    ]);
                    
                    $installment['1'] = $installment['2'] = $installment['3'] = $installment['4'] = $installment['5'] = $installment['6'] = 0;
                    if ($mba_invoice_installment) {
                        $i_installment_count = count($mba_invoice_installment);
                        // $selisih = ($i_installment_count < 6) ? 6 - $i_installment_count : 0;
                        $list_month = $this->month[$s_semester_type];
                        foreach ($list_month as $key => $value) {
                            $index_installment = $key + 1;
                            $mba_installment_month = $this->General->get_where('dt_sub_invoice_details', [
                                'sub_invoice_id' => $mba_invoice_installment[0]->sub_invoice_id,
                                'MONTH(sub_invoice_details_real_datetime_deadline)' => $value,
                                'sub_invoice_details_status != ' => 'paid',
                                // 'sub_invoice_details_amount_paid' => 0,
                            ]);

                            $d_amount_total = ($mba_installment_month) ? $mba_installment_month[0]->sub_invoice_details_amount_total : 0;
                            $d_amount_paid = ($mba_installment_month) ? $mba_installment_month[0]->sub_invoice_details_amount_paid : 0;
                            $d_unpaid = $d_amount_total - $d_amount_paid;
                            $installment[$index_installment] = $d_unpaid;
                        }
                    }
                    else if ($mba_invoice_fullpayment) {
                        $d_unpaid = $mba_invoice_fullpayment->sub_invoice_details_amount_total - $mba_invoice_fullpayment->sub_invoice_details_amount_paid;
                        $installment['1'] = $d_unpaid;
                    }

                    $o_invoice->installment_1 = $installment['1'];
                    $o_invoice->installment_2 = $installment['2'];
                    $o_invoice->installment_3 = $installment['3'];
                    $o_invoice->installment_4 = $installment['4'];
                    $o_invoice->installment_5 = $installment['5'];
                    $o_invoice->installment_6 = $installment['6'];

                    $o_invoice->student_id = $o_student->student_id;
                    $o_invoice->student_status = $o_student->student_status;
                    $o_invoice->personal_data_name = $o_student->personal_data_name;
                    $o_invoice->faculty_abbreviation = $o_student->faculty_abbreviation;
                    $o_invoice->study_program_abbreviation = $o_student->study_program_abbreviation;
                    $o_invoice->student_number = $o_student->student_number;
                    $o_invoice->student_type = $o_student->student_type;
                    $o_invoice->student_batch = $o_student->academic_year_id;
                    $o_invoice->installment = $installment;
                    $o_invoice->total_unpaid_installment = array_sum($installment);
                }
            }
            $mba_invoice_list = array_values($mba_invoice_list);
        }

        if (!$return_value) {
            print json_encode(['data' => $mba_invoice_list]);
        }
        else {
            return $mba_invoice_list;
        }
    }

    function get_current_semester_new($return_value = false, $s_student_status = 'active', $s_select_semester = false) {
        // $s_student_status = 'active';
        $s_select_semester = (!$s_select_semester) ? $this->session->userdata('academic_year_id_active').'-'.$this->session->userdata('semester_type_id_active') : $s_select_semester;
        if (!$return_value) {
            $s_student_status = $this->input->post('student_status');
            $s_select_semester = $this->input->post('semester_selected');
        }
        
        $a_semester = explode('-', $s_select_semester);
        $s_academic_year = $a_semester[0];
        $s_semester_type = $a_semester[1];

        $a_status = (is_array($s_student_status)) ? $s_student_status : [$s_student_status];
        $mba_student_list = $this->Stm->get_student_filtered(false, $a_status);
        if ($mba_student_list) {
            foreach ($mba_student_list as $o_student) {
                $o_student->student_batch = $o_student->academic_year_id;
                $mba_invoice_list = $this->Im->get_invoice_by_deadline([
                    'di.personal_data_id' => $o_student->personal_data_id,
                    'fee.payment_type_code' => '02',
                    'di.academic_year_id' => $s_academic_year,
                    'di.semester_type_id' => $s_semester_type,
                ], false, false);
                if ($mba_invoice_list) {
                    foreach ($mba_invoice_list as $o_invoice) {
                        $mba_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                        $installment['1'] = $installment['2'] = $installment['3'] = $installment['4'] = $installment['5'] = $installment['6'] = 0;
                        if (in_array($o_invoice->invoice_status, ['created', 'pending'])) {
                            if (($mba_invoice_fullpayment) AND ($mba_invoice_fullpayment->sub_invoice_details_status != 'paid')) {
                                $mba_invoice_installment = $this->Im->get_invoice_data([
                                    'di.invoice_id' => $o_invoice->invoice_id,
                                    'dsi.sub_invoice_type' => 'installment'
                                ]);
                                if ($mba_invoice_installment) {
                                    $i_installment_count = count($mba_invoice_installment);
                                    // $selisih = ($i_installment_count < 6) ? 6 - $i_installment_count : 0;
                                    $list_month = $this->month[$s_semester_type];
                                    foreach ($list_month as $key => $value) {
                                        $index_installment = $key + 1;
                                        $mba_installment_month = $this->General->get_where('dt_sub_invoice_details', [
                                            'sub_invoice_id' => $mba_invoice_installment[0]->sub_invoice_id,
                                            'MONTH(sub_invoice_details_real_datetime_deadline)' => $value,
                                            'sub_invoice_details_status != ' => 'paid',
                                            // 'sub_invoice_details_amount_paid' => 0,
                                        ]);
    
                                        $d_amount_total = ($mba_installment_month) ? $mba_installment_month[0]->sub_invoice_details_amount_total : 0;
                                        $d_amount_paid = ($mba_installment_month) ? $mba_installment_month[0]->sub_invoice_details_amount_paid : 0;
                                        $d_unpaid = $d_amount_total - $d_amount_paid;
                                        $installment[$index_installment] = $d_unpaid;
                                    }
                                }
                                else if ($mba_invoice_fullpayment) {
                                    $d_amount_total = $mba_invoice_fullpayment->sub_invoice_details_amount + $mba_invoice_fullpayment->sub_invoice_details_amount_fined;
                                    $d_unpaid = $d_amount_total - $mba_invoice_fullpayment->sub_invoice_details_amount_paid;
                                    $installment['1'] = $d_unpaid;
                                }
                            }
                        }

                        $o_student->installment_1 = $installment['1'];
                        $o_student->installment_2 = $installment['2'];
                        $o_student->installment_3 = $installment['3'];
                        $o_student->installment_4 = $installment['4'];
                        $o_student->installment_5 = $installment['5'];
                        $o_student->installment_6 = $installment['6'];

                        $o_student->installment = $installment;
                        $o_student->total_unpaid_installment = array_sum($installment);
                    }
                }
                else {
                    $installment['1'] = $installment['2'] = $installment['3'] = $installment['4'] = $installment['5'] = $installment['6'] = '';
                    $o_student->installment_1 = $installment['1'];
                    $o_student->installment_2 = $installment['2'];
                    $o_student->installment_3 = $installment['3'];
                    $o_student->installment_4 = $installment['4'];
                    $o_student->installment_5 = $installment['5'];
                    $o_student->installment_6 = $installment['6'];

                    $o_student->installment = $installment;
                    $o_student->total_unpaid_installment = '';
                }
            }

            $mba_student_list = array_values($mba_student_list);
        }
        if (!$return_value) {
            print json_encode(['data' => $mba_student_list]);
        }
        else {
            return $mba_student_list;
        }
    }

    function get_student_all_billing($return_value = false, $s_payment_type_code = false, $s_student_status = 'active', $b_include_tf = true) {
        if (!$return_value) {
            $s_student_status = $this->input->post('student_status');
            $s_payment_type_code = $this->input->post('payment_type_code');
            $b_include_tf = (!empty($this->input->post('include_tf'))) ? true : false;
            $s_payment_type_code = (($s_payment_type_code == 'all') OR (empty($s_payment_type_code))) ? false : $s_payment_type_code;
        }

        $s_order = "ds.student_status, ds.academic_year_id ASC, fc.faculty_abbreviation ASC, rsp.study_program_abbreviation ASC, dpd.personal_data_name";
        if (is_array($s_student_status)) {
            $mba_student_data = $this->Stm->get_student_filtered(false, $s_student_status, $s_order);
        }
        else {
            $mba_student_data = $this->Stm->get_student_filtered([
                'ds.student_status' => $s_student_status
            ], false, $s_order);
        }

        if ($mba_student_data) {
            $a_payment_id = [];
            if ($s_payment_type_code) {
                $a_payment_id = (is_array($s_payment_type_code)) ? $s_payment_type_code : [$s_payment_type_code];
            }
            else {
                $mba_payment_code = $this->General->get_where('ref_payment_type');
                if ($mba_payment_code) {
                    foreach ($mba_payment_code as $o_payment_id) {
                        $b_push = true;
                        if (!$b_include_tf) {
                            if (in_array($o_payment_id->payment_type_code, ['02', '05'])) {
                                $b_push = false;
                            }
                        }

                        if ($b_push) {
                            if (!in_array($o_payment_id->payment_type_code, $a_payment_id)) {
                                array_push($a_payment_id, $o_payment_id->payment_type_code);
                            }
                        }
                    }
                }
            }
            
            foreach ($mba_student_data as $key => $o_student) {
                $total_tunggakan = 0;
                $mba_student_invoice = $this->Im->student_has_invoice_list($o_student->personal_data_id, [
                    'df.study_program_id' => $o_student->study_program_id
                ], ['created', 'pending'], $a_payment_id);
                $o_student->list_billing = $mba_student_invoice;
            }
            $mba_student_data = array_values($mba_student_data);
        }

        if (!$return_value) {
            print json_encode(['data' => $mba_student_data]);
        }
        else {
            return $mba_student_data;
        }
    }

    function get_student_body($return_value = false, $s_student_status = 'active', $s_select_semester = false) {
        // $s_student_status = 'active';
        $s_select_semester = (!$s_select_semester) ? $this->session->userdata('academic_year_id_active').'-'.$this->session->userdata('semester_type_id_active') : $s_select_semester;
        if (!$return_value) {
            $s_student_status = $this->input->post('student_status');
            $s_select_semester = $this->input->post('semester_selected');
        }

        $a_semester = explode('-', $s_select_semester);
        // print('<pre>');var_dump($a_semester);exit;
        $s_academic_year = $a_semester[0];
        $s_semester_type = $a_semester[1];
        $s_semester_selected = $s_academic_year.$s_semester_type;
        
        // $mba_student_data = $this->Stm->get_student_filtered([
        //     'ds.student_status' => $s_student_status
        // ], false);

        $s_order = "ds.student_status, ds.academic_year_id ASC, fc.faculty_abbreviation ASC, rsp.study_program_abbreviation ASC, dpd.personal_data_name";
        if (is_array($s_student_status)) {
            $mba_student_data = $this->Stm->get_student_filtered(false, $s_student_status, $s_order);
        }
        else {
            $mba_student_data = $this->Stm->get_student_filtered([
                'ds.student_status' => $s_student_status
            ], false, $s_order);
        }
        if ($mba_student_data) {
            foreach ($mba_student_data as $key => $o_student) {
                $mba_student_note = $this->General->get_where('dt_personal_data_record', [
                    'personal_data_id' => $o_student->personal_data_id
                ]);
                $mba_has_invoice_onleave = $this->Im->get_invoice_list_detail([
                    'di.personal_data_id' => $o_student->personal_data_id,
                    'fee.payment_type_code' => '05',
                ]);
                $a_student_note = [];
                $sem['1'] = $sem['2'] = $sem['3'] = $sem['4'] = $sem['5'] = $sem['6'] = $sem['7'] = 0;
                $sem['8'] = $sem['9'] = $sem['10'] = $sem['11'] = $sem['12'] = $sem['13'] = $sem['14'] = 0;

                if ($mba_student_note) {
                    foreach ($mba_student_note as $o_personal_note) {
                        array_push($a_student_note, $o_personal_note->record_comment);
                    }
                }
                $mba_student_semester_current = $this->General->get_where('dt_student_semester', [
                    'student_id' => $o_student->student_id,
                    'academic_year_id' => $s_academic_year,
                    'semester_type_id' => $s_semester_type
                ]);
                $student_current_semester = (($s_academic_year - $o_student->academic_year_id) * 2) + $s_semester_type;
                if (($mba_student_semester_current) AND (!is_null($mba_student_semester_current[0]->semester_id))) {
                    $semester_data = $this->General->get_where('ref_semester', ['semester_id' => $mba_student_semester_current[0]->semester_id]);
                    if (!$semester_data) {
                        print('<pre>');var_dump($mba_student_semester_current[0]->semester_id);exit;
                    }
                    $student_current_semester = $semester_data[0]->semester_number;
                }
                
                $current_semester_tunggakan = 0;
                $total_tunggakan = 0;
                for ($semester=1; $semester <= 14; $semester++) {
                    if ($semester <= $student_current_semester) {
                        $mba_student_invoice_semester = $this->Im->student_has_invoice_list($o_student->personal_data_id, [
                            'rs.semester_number' => $semester,
                            'df.payment_type_code' => '02',
                            'df.study_program_id' => $o_student->study_program_id
                        ]);
                        $mba_student_invoice_leave = false;
                        if ($mba_has_invoice_onleave) {
                            $mba_student_academic_semester = $this->General->get_where('dt_student_semester', [
                                'student_id' => $o_student->student_id,
                                'semester_id' => $semester
                            ]);
                            $mba_student_invoice_leave = $this->Im->student_has_invoice_list($o_student->personal_data_id, [
                                'rs.semester_number' => $semester,
                                'df.payment_type_code' => '05'
                            ]);
                            if ($mba_student_academic_semester) {
                                $mba_student_invoice_semester = $this->Im->student_has_invoice_list($o_student->personal_data_id, [
                                    'df.payment_type_code' => '02',
                                    'df.study_program_id' => $o_student->study_program_id,
                                    'di.academic_year_id' => $mba_student_academic_semester[0]->academic_year_id,
                                    'di.semester_type_id' => $mba_student_academic_semester[0]->semester_type_id
                                ]);
                            }
                        }
    
                        // if ($o_student->student_id == '9efca642-14c2-4880-9ef9-4b46eee9efc0') {
                        //     if ($semester == 5) {
                        //         print('<pre>');var_dump($mba_student_invoice_semester);exit;
                        //     }
                        // }
                        $s_outstanding = "";
                        $i_outstanding = 0;
                        if ($mba_student_invoice_leave) {
                            // $i_outstanding = 0;
                            if (!in_array($mba_student_invoice_leave[0]->invoice_status, ['paid', 'cancelled'])) {
                                $i_outstanding += $mba_student_invoice_leave[0]->invoice_details_amount;
                            }
                            $s_outstanding = $i_outstanding;
                        }
                        
                        if ($mba_student_invoice_semester) {
                            // $i_outstanding = 0;
                            foreach ($mba_student_invoice_semester as $o_invoice) {
                                if (!in_array($o_invoice->invoice_status, ['paid', 'cancelled'])) {
                                    $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                                    $mba_invoice_full_payment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                                    if ($mba_invoice_installment) {
                                        foreach ($mba_invoice_installment as $installment) {
                                            if (!in_array($installment->sub_invoice_details_status, ['paid'])) {
                                                $d_unpaid = $installment->sub_invoice_details_amount_total - $installment->sub_invoice_details_amount_paid;
                                                $i_outstanding += $d_unpaid;
                                            }
                                        }
                                    }else if ($mba_invoice_full_payment) {
                                        if ($mba_invoice_full_payment->sub_invoice_details_status != 'paid') {
                                            $i_outstanding += $mba_invoice_full_payment->sub_invoice_details_amount_total;
                                        }
                                    }
        
                                    if ((!is_null($o_invoice->invoice_note)) OR ($o_invoice->invoice_note != '')) {
                                        array_push($a_student_note, trim($o_invoice->invoice_note));
                                    }
                                }
                            }
                            $s_outstanding = $i_outstanding;
                        }
    
                        if ($semester == $student_current_semester) {
                            $current_semester_tunggakan = ($s_outstanding == "") ? 0 : $s_outstanding;
                        }
                        $sem[$semester] = ($s_outstanding == "") ? "" : number_format($s_outstanding, 0, ".", ".");
                        $total_tunggakan += ($s_outstanding == "") ? 0 : $s_outstanding;
                    }
                }

                $o_student->sem_1 = $sem['1'];
                $o_student->sem_2 = $sem['2'];
                $o_student->sem_3 = $sem['3'];
                $o_student->sem_4 = $sem['4'];
                $o_student->sem_5 = $sem['5'];
                $o_student->sem_6 = $sem['6'];
                $o_student->sem_7 = $sem['7'];
                $o_student->sem_8 = $sem['8'];
                $o_student->sem_9 = $sem['9'];
                $o_student->sem_10 = $sem['10'];
                $o_student->sem_11 = $sem['11'];
                $o_student->sem_12 = $sem['12'];
                $o_student->sem_13 = $sem['13'];
                $o_student->sem_14 = $sem['14'];
                $o_student->total_tunggakan = number_format($total_tunggakan, 0, ".", ".");
                $o_student->semester_tunggakan = number_format($current_semester_tunggakan, 0, ".", ".");
                $o_student->semester_lain_tunggakan = number_format(($total_tunggakan - $current_semester_tunggakan), 0, ".", ".");
                $o_student->ori_total_tunggakan = $total_tunggakan;
                $o_student->ori_semester_tunggakan = $current_semester_tunggakan;
                $o_student->ori_semester_lain_tunggakan = ($total_tunggakan - $current_semester_tunggakan);
                $o_student->student_note = $a_student_note;
                $o_student->have_onleave = ($mba_has_invoice_onleave) ? true : false;

                // if ($total_tunggakan == 0) {
                //     $is_active = true;
                //     if (is_array($s_student_status)) {
                //         if (!in_array('active', $s_student_status)) {
                //             unset($mba_student_data[$key]);
                //         }
                //     }
                //     else if ($s_student_status != 'active') {
                //         unset($mba_student_data[$key]);
                //     }
                // }
            }
            $mba_student_data = array_values($mba_student_data);
        }

        if (!$return_value) {
            print json_encode(['data' => $mba_student_data]);
        }
        else {
            return $mba_student_data;
        }
    }

    public function billed_tuition_fee_semester()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');

            $mba_student_data = $this->Stm->get_student_filtered([
                'finance_year_id <=' => $s_academic_year_id
            ], ['active','onleave','inactive']);

            $a_data = [];
            if ($mba_student_data) {
                foreach ($mba_student_data as $o_student) {
                    $a_student_data = [
                        'personal_data_name' => $o_student->personal_data_name,
                        'study_program_abbreviation' => $o_student->study_program_abbreviation,
                        'current_semester' => '#',
                        'semester_leave' => '#',
                        'discount_scholarship' => '#',
                        'sks_approved' => '#',
                        'billed_semester' => '#',
                        'flyer_amount' => '#',
                        'billed_amount' => '#',
                        'total_paid' => '#',
                        'invoice_id' => '#',
                        'student_id' => $o_student->student_id,
                        'personal_data_id' => $o_student->personal_data_id,
                        'student_number' => $o_student->student_number,
                        'student_status' => $o_student->student_status,
                        'student_batch' => $o_student->academic_year_id,
                        'finance_year_id' => $o_student->finance_year_id,
                        'has_created' => false,
                        'note_remarks' => [],
                    ];
                    if ($o_student->student_mark_completed_defense != 1) {
                        $mba_student_semester = $this->Smm->get_semester_student($o_student->student_id, ['ss.student_semester_status' => 'onleave'], [1,2]);
                        $mba_score_data = $this->Scm->get_score_data([
                            'sc.academic_year_id' => $s_academic_year_id,
                            'sc.semester_type_id' => $s_semester_type_id,
                            'sc.student_id' => $o_student->student_id,
                            'sc.score_approval' => 'approved'
                        ]);
    
                        $mbo_semester_setting_data = $this->General->get_where('dt_student_semester', [
                            'student_id' => $o_student->student_id,
                            'academic_year_id' => $s_academic_year_id,
                            'semester_type_id' => $s_semester_type_id
                        ])[0];
    
                        if (($mbo_semester_setting_data) AND (!is_null($mbo_semester_setting_data->semester_id))) {
                            $i_total_sks = 0;
                            $a_semester_id_leave = [];
                            $a_semester_number_leave = [];

                            $mbo_semester_score = $this->General->get_where('ref_semester', ['semester_id' => $mbo_semester_setting_data->semester_id])[0];
                            if ($mba_score_data) {
                                foreach ($mba_score_data as $o_score) {
                                    $i_total_sks += $o_score->curriculum_subject_credit;
                                }
                            }
                            $i_current_semester_id = $mbo_semester_setting_data->semester_id;
                            $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $i_current_semester_id])[0];
                            $i_current_semester_number = $mbo_semester_data->semester_number;

                            $a_student_data['sks_approved'] = $i_total_sks;
                            $a_student_data['current_semester'] = $mbo_semester_score->semester_number;

                            if ($mba_student_semester) {
                                foreach ($mba_student_semester as $o_student_semester) {
                                    if ($o_student_semester->semester_year_id <= $s_academic_year_id) {
                                        if (!is_null($o_student_semester->semester_id)) {
                                            $mbo_semester_data_leave = $this->General->get_where('ref_semester', ['semester_id' => $o_student_semester->semester_id])[0];
                                            if (!in_array($o_student_semester->semester_id, $a_semester_id_leave)) {
                                                array_push($a_semester_id_leave, $o_student_semester->semester_id);
                                                array_push($a_semester_number_leave, $mbo_semester_data_leave->semester_number);
                                            }
                                        }
                                    }
                                }
                            }

                            if (count($a_semester_number_leave) > 0) {
                                $i_current_semester_number = $i_current_semester_number - count($a_semester_number_leave);
                                $mbo_semester_data_current = $this->General->get_where('ref_semester', ['semester_number' => $i_current_semester_number])[0];
                                $i_current_semester_id = $mbo_semester_data_current->semester_id;
                            }

                            $a_student_data['semester_leave'] = implode('/', $a_semester_number_leave);
                            $a_scholarship_id = [];
                            $a_siblings = [];
                            $a_scholarship_name = [];

                            if ($mbo_semester_score->semester_number <= 8) {
                                $mba_scholarship_data = $this->General->get_where('dt_personal_data_scholarship', [
                                    'personal_data_id' => $o_student->personal_data_id,
                                    'scholarship_status' => 'active'
                                ]);
    
                                if ($mba_scholarship_data) {
                                    foreach ($mba_scholarship_data as $o_ps) {
                                        $mbo_scholarship_data = $this->General->get_where('ref_scholarship', ['scholarship_id' => $o_ps->scholarship_id, 'scholarship_fee_type' => 'main'])[0];
                                        
                                        if ($mbo_scholarship_data) {
                                            if (!in_array($o_ps->scholarship_id, $a_scholarship_id)) {
                                                array_push($a_scholarship_id, $o_ps->scholarship_id);
                                                array_push($a_scholarship_name, $mbo_scholarship_data->scholarship_name);
                                            }
                                        }
                                        else if (!is_null($o_ps->personal_data_id_sibling_with)) {
                                            $mba_sibling = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_ps->personal_data_id_sibling_with]);
                                            $s_sibling = 'sibling with '.ucwords(strtolower($mba_sibling[0]->personal_data_name));
                                            if (!in_array($s_sibling, $a_scholarship_name)) {
                                                array_push($a_scholarship_name, $s_sibling);
                                            }
                                        }
                                    }
                                }

                                $a_student_data['discount_scholarship'] = implode('<br>', $a_scholarship_name);
                            }

                            $mba_has_invoice = $this->Im->student_has_invoice_data($o_student->personal_data_id, [
                                'di.academic_year_id' => $s_academic_year_id,
                                'di.semester_type_id' => $s_semester_type_id,
                                'di.invoice_status != ' => 'cancelled',
                                'df.payment_type_code' => '02',
                                'df.fee_amount_type' => 'main',
                                'df.fee_special' => 'false'
                            ]);

                            if ($mba_has_invoice) {
                                $i_current_semester_id = $mba_has_invoice->semester_id;
                                $mbo_semester_data_ = $this->General->get_where('ref_semester', ['semester_id' => $i_current_semester_id])[0];
                                $i_current_semester_number = $mbo_semester_data_->semester_number;
                            }
    
                            if ($i_current_semester_number > 8) {
                                $i_current_semester_number = $mbo_semester_score->semester_number;
                            }
                            $a_student_data['billed_semester'] = $i_current_semester_number;

                            $a_filter_fee = [
                                'payment_type_code' => '02', 
                                'program_id' => $o_student->student_program,
                                'study_program_id' => $o_student->study_program_id,
                                'academic_year_id' => $o_student->finance_year_id,
                                'fee_amount_type' => 'main',
                                'semester_id' => $i_current_semester_id,
                                'fee_special' => 'false'
                            ];
    
                            if (count($a_scholarship_id) > 0) {
                                $a_filter_fee['scholarship_id != '] = NULL;
                            }else{
                                $a_filter_fee['scholarship_id'] = NULL;
                            }
    
                            $mbo_fee_semester = $this->Im->get_fee($a_filter_fee)[0];
                            if ($mbo_fee_semester) {
                                $mbo_invoice = $this->Im->student_has_invoice_data($o_student->personal_data_id, [
                                    'df.fee_id' => $mbo_fee_semester->fee_id
                                ]);

                                $i_amount_paid = 0;
                                if ($mba_has_invoice) {
                                    $mbo_invoice = $mba_has_invoice;
                                    $mbo_sub_invoice = $this->General->get_where('dt_sub_invoice', ['invoice_id' => $mbo_invoice->invoice_id])[0];
                                    if ($mbo_invoice->invoice_status != 'paid') {
                                        $mba_invoice_installment = $this->Im->get_invoice_installment($mbo_invoice->invoice_id);

                                        if ($mba_invoice_installment) {
                                            foreach ($mba_invoice_installment as $o_installment) {
                                                $i_amount_paid += $o_installment->sub_invoice_details_amount_paid;
                                            }
                                        }
                                    }else{
                                        $i_amount_paid = $mbo_sub_invoice->sub_invoice_amount;
                                    }

                                    $a_student_data['billed_amount'] = $mbo_invoice->invoice_details_amount;
                                    $a_student_data['invoice_id'] = $mbo_invoice->invoice_id;
                                    $a_student_data['invoice_note'] = $mbo_invoice->invoice_note;

                                }else if (($mbo_semester_score->semester_number > 8) AND ($i_total_sks > 0)) {
                                    $total_billed = $mbo_fee_semester->fee_amount * $i_total_sks;
                                    $a_student_data['billed_amount'] = $total_billed;
                                }

                                $a_student_data['flyer_amount'] = $mbo_fee_semester->fee_amount;
                                $a_student_data['total_paid'] = $i_amount_paid;
                            }

                            $a_notes = [];
                            $mba_student_notes = $this->General->get_where('dt_personal_data_record', ['personal_data_id' => $o_student->personal_data_id]);
                            if ($mba_student_notes) {
                                foreach ($mba_student_notes as $o_notes) {
                                    array_push($a_notes, $o_notes->record_comment);
                                }
                            }

                            if (count($a_notes) > 0) {
                                $a_student_data['note_remarks'] = $a_notes;
                            }
                        }
                    }

                    array_push($a_data, $a_student_data);
                }

                $a_data = array_values($a_data);
            }

            print json_encode(['data' => $a_data]);
        }
        else {
            $this->a_page_data['body'] = $this->load->view('report/billed_tuition_fee_semester', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    function generate_report() {
        if ($this->input->is_ajax_request()) {
            $s_semester = $this->input->post('semester');
            $a_semester = explode('-', $s_semester);
            $s_academic_year = $a_semester[0];
            $s_semester_type = $a_semester[1];
            $a_month = $this->month[$s_semester_type];
            $i_semester = str_replace("-", "", $s_semester);
            $i_semestercurrent = str_replace("-", "", $this->semestercurrent);

            $mba_semester_data = $this->General->get_where('dt_semester_settings', [
                'academic_year_id' => $a_semester[0],
                'semester_type_id' => $a_semester[1]
            ]);
            $year = ($mba_semester_data) ? date('Y', strtotime($mba_semester_data[0]->semester_start_date)) : date('Y');

            $start_month = $a_month[0];
            $current_month = intval(date('m'));
            $d_current_month = $current_month.'-'.date('Y');
            if ($d_current_month != $current_month.'-'.$year) {
                $current_month = 0;
            }
            $prev_month = ($current_month > 0) ? $current_month - 1 : $a_month[count($a_month) - 1];

            $current_month_teks = ($current_month > 0) ? date('F Y', strtotime($year.'-'.$current_month.'-01')) : '';
            $prev_month_teks = ($prev_month > 0) ? date('F Y', strtotime($year.'-'.$prev_month.'-01')) : '';
            $start_month_teks = ($prev_month_teks != '') ? date('F Y', strtotime($year.'-'.$start_month.'-01')) : '';
            $prev_installment_teks = (($prev_month_teks != '') AND ($start_month_teks != '')) ? $start_month_teks.' - '.$prev_month_teks : '';

            $mba_student_body_active = $this->get_student_body(true, 'active', $s_semester);
            $mba_student_body_graduated = $this->get_student_body(true, 'graduated', $s_semester);
            $mba_invoice_semester = $this->get_current_semester_new(true, 'active', $s_semester);

            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'report_billing_owing_by_student('.date('d F Y').')';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/finance/report/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_spreadsheet->createSheet();
            $o_spreadsheet->createSheet();
            $o_spreadsheet->createSheet();
            
            $sheet0 = $o_spreadsheet->getSheet(0)->setTitle('Summary');
            $sheet1 = $o_spreadsheet->getSheet(1)->setTitle('List of Student Active');
            $sheet2 = $o_spreadsheet->getSheet(2)->setTitle('List of Student Graduated');
            $sheet3 = $o_spreadsheet->getSheet(3)->setTitle('Semester '.$s_semester.' Tuition Fee');

            $sheet0->setCellValue('A1', "Summary");
            $sheet0->setCellValue('A2', "Owing By Student");
            $sheet0->setCellValue('B2', "IDR");
            $sheet0->setCellValue('A3', "Estimated income from arrears previous semester");
            $sheet0->setCellValue('A4', "Estimated income from arrears current semester ($prev_installment_teks)");
            $sheet0->setCellValue('A5', "Estimated income from arrears current semester ($current_month_teks)");
            $sheet0->setCellValue('A6', "Estimated income from arrears current semester until end semester");
            $sheet0->setCellValue('A7', "Total Owed (Active Student)");
            $sheet0->setCellValue('A8', "Total Owed (Graduated Student)");
            $sheet0->setCellValue('A9', "Total Owed");

            $sheet1->setCellValue('A1', "Active Student");
            $sheet1->setCellValue('A2', "No");
            $sheet1->setCellValue('B2', "Student Name");
            $sheet1->setCellValue('C2', "Fac");
            $sheet1->setCellValue('D2', "SP");
            $sheet1->setCellValue('E2', "Student ID");
            $sheet1->setCellValue('F2', "Type");
            $sheet1->setCellValue('G2', "Batch");
            $sheet1->setCellValue('H2', "Student Status");
            $sheet1->setCellValue('I2', "Semester");
            $sheet1->setCellValue('W2', "Total Tunggakan");
            $sheet1->setCellValue('X2', "Tunggakan Semester Lain");
            $sheet1->setCellValue('Y2', "Tunggakan Semester ".$s_semester);
            $sheet1->setCellValue('Z2', "Note");

            $sheet2->setCellValue('A1', "Active Student");
            $sheet2->setCellValue('A2', "No");
            $sheet2->setCellValue('B2', "Student Name");
            $sheet2->setCellValue('C2', "Fac");
            $sheet2->setCellValue('D2', "SP");
            $sheet2->setCellValue('E2', "Student ID");
            $sheet2->setCellValue('F2', "Type");
            $sheet2->setCellValue('G2', "Batch");
            $sheet2->setCellValue('H2', "Student Status");
            $sheet2->setCellValue('I2', "Semester");
            $sheet2->setCellValue('W2', "Total Tunggakan");
            $sheet2->setCellValue('X2', "Tunggakan Semester Lain");
            $sheet2->setCellValue('Y2', "Tunggakan Semester ".$s_semester);
            $sheet2->setCellValue('Z2', "Note");

            $sheet3->setCellValue('A1', "Tuition Fee ".$s_semester);
            $sheet3->setCellValue('A2', "No");
            $sheet3->setCellValue('B2', "Student Name");
            $sheet3->setCellValue('C2', "Fac");
            $sheet3->setCellValue('D2', "SP");
            $sheet3->setCellValue('E2', "Student ID");
            $sheet3->setCellValue('F2', "Type");
            $sheet3->setCellValue('G2', "Batch");
            $sheet3->setCellValue('H2', "Student Status");
            $sheet3->setCellValue('I2', "Installment Unpaid");
            $sheet3->setCellValue('O2', "Total Unpaid");


            $sheet0->getColumnDimension('A')->setWidth(85);
            $sheet0->getColumnDimension('B')->setWidth(20);
            $sheet0->getStyle('A2:B2')->getFont()->setBold( true );
            $sheet0->getStyle('B3:B10')->getNumberFormat()->setFormatCode('#,##');
            $sheet0->mergeCells('A1:B1');

            $sheet1->getColumnDimension('A')->setWidth(5);
            $sheet1->getColumnDimension('B')->setWidth(30);
            $sheet1->getColumnDimension('C')->setWidth(6);
            $sheet1->getColumnDimension('D')->setWidth(6);
            $sheet1->getColumnDimension('E')->setWidth(12);
            $sheet1->getColumnDimension('G')->setWidth(6);
            $sheet1->getColumnDimension('H')->setWidth(9);
            // $sheet1->getColumnDimension('I:Z')->setWidth(13);
            $sheet1->getStyle('A2:Z2')->getFont()->setBold( true );
            $sheet1->mergeCells('A2:A3');
            $sheet1->mergeCells('B2:B3');
            $sheet1->mergeCells('C2:C3');
            $sheet1->mergeCells('D2:D3');
            $sheet1->mergeCells('E2:E3');
            $sheet1->mergeCells('F2:F3');
            $sheet1->mergeCells('G2:G3');
            $sheet1->mergeCells('H2:H3');
            $sheet1->mergeCells('W2:W3');
            $sheet1->mergeCells('X2:X3');
            $sheet1->mergeCells('Y2:Y3');
            $sheet1->mergeCells('Z2:Z3');
            $sheet1->freezePane('I4');
            
            $sheet2->getColumnDimension('A')->setWidth(5);
            $sheet2->getColumnDimension('B')->setWidth(30);
            $sheet2->getColumnDimension('C')->setWidth(6);
            $sheet2->getColumnDimension('D')->setWidth(6);
            $sheet2->getColumnDimension('E')->setWidth(12);
            $sheet2->getColumnDimension('G')->setWidth(6);
            $sheet2->getColumnDimension('H')->setWidth(9);
            // $sheet2->getColumnDimension('I:Z')->setWidth(13);
            $sheet2->getStyle('A2:Z2')->getFont()->setBold( true );
            $sheet2->mergeCells('A2:A3');
            $sheet2->mergeCells('B2:B3');
            $sheet2->mergeCells('C2:C3');
            $sheet2->mergeCells('D2:D3');
            $sheet2->mergeCells('E2:E3');
            $sheet2->mergeCells('F2:F3');
            $sheet2->mergeCells('G2:G3');
            $sheet2->mergeCells('H2:H3');
            $sheet2->mergeCells('W2:W3');
            $sheet2->mergeCells('X2:X3');
            $sheet2->mergeCells('Y2:Y3');
            $sheet2->mergeCells('Z2:Z3');
            $sheet2->freezePane('I4');
            
            $sheet3->getColumnDimension('A')->setWidth(5);
            $sheet3->getColumnDimension('B')->setWidth(30);
            $sheet3->getColumnDimension('C')->setWidth(6);
            $sheet3->getColumnDimension('D')->setWidth(6);
            $sheet3->getColumnDimension('E')->setWidth(12);
            $sheet3->getColumnDimension('G')->setWidth(6);
            $sheet3->getColumnDimension('H')->setWidth(9);
            // $sheet3->getColumnDimension('I:Z')->setWidth(13);
            $sheet3->getStyle('A2:Z2')->getFont()->setBold( true );
            $sheet3->mergeCells('A2:A3');
            $sheet3->mergeCells('B2:B3');
            $sheet3->mergeCells('C2:C3');
            $sheet3->mergeCells('D2:D3');
            $sheet3->mergeCells('E2:E3');
            $sheet3->mergeCells('F2:F3');
            $sheet3->mergeCells('G2:G3');
            $sheet3->mergeCells('H2:H3');
            $sheet3->mergeCells('O2:O3');
            $sheet3->freezePane('I4');

            $col_sem = 'I';
            for ($sem=1; $sem <= 14 ; $sem++) { 
                $sheet1->setCellValue($col_sem.'3', $sem);
                $sheet2->setCellValue($col_sem.'3', $sem);
                $col_sem++;
            }

            $col_sem = 'I';
            foreach ($a_month as $i_month) {
                $sheet3->setCellValue($col_sem.'3', date('M', strtotime('2023-'.$i_month.'-1')));
                $col_sem++;
            }

            $i_row1 = $i_row2 = $i_row3 = 4;
            $i_num1 = $i_num2 = $i_num3 = 1;

            $d_total_active_prev_semester = 0;
            $d_total_active_prev_installment = 0;
            $d_total_active_current_installment = 0;
            $d_total_active_next_installment = 0;
            $d_total_graduated = 0;

            if ($mba_student_body_active) {
                foreach ($mba_student_body_active as $o_student_active) {
                    $sheet1->setCellValue('A'.$i_row1, $i_num1++);
                    $sheet1->setCellValue('B'.$i_row1, $o_student_active->personal_data_name);
                    $sheet1->setCellValue('C'.$i_row1, $o_student_active->faculty_abbreviation);
                    $sheet1->setCellValue('D'.$i_row1, $o_student_active->study_program_abbreviation);
                    $sheet1->setCellValue('E'.$i_row1, $o_student_active->student_number);
                    $sheet1->setCellValue('F'.$i_row1, $o_student_active->student_type);
                    $sheet1->setCellValue('G'.$i_row1, $o_student_active->academic_year_id);
                    $sheet1->setCellValue('H'.$i_row1, $o_student_active->student_status);

                    $col_sem = 'I';
                    for ($sem=1; $sem <= 14 ; $sem++) { 
                        $key_sem = 'sem_'.$sem;
                        $amount = $o_student_active->$key_sem;
                        $amount = str_replace('.', '', $amount);
                        $sheet1->setCellValue($col_sem.$i_row1, $amount);
                        $col_sem++;
                    }
                    $sheet1->setCellValue('W'.$i_row1, $o_student_active->ori_total_tunggakan);
                    $sheet1->setCellValue('X'.$i_row1, $o_student_active->ori_semester_lain_tunggakan);
                    $sheet1->setCellValue('Y'.$i_row1, $o_student_active->ori_semester_tunggakan);

                    $d_total_active_prev_semester += $o_student_active->ori_semester_lain_tunggakan;
                    $i_row1++;
                }
                $last_irow1 = $i_row1 - 1;
                $sheet1->setCellValue('A'.$i_row1, "Total");
                $sheet1->setCellValue('W'.$i_row1, "=SUM(W4:W$last_irow1)");
                $sheet1->setCellValue('X'.$i_row1, "=SUM(X4:X$last_irow1)");
                $sheet1->setCellValue('Y'.$i_row1, "=SUM(Y4:Y$last_irow1)");
                $sheet1->getStyle('I4:Y'.$i_row1)->getNumberFormat()->setFormatCode('#,##');
            }
            if ($mba_student_body_graduated) {
                foreach ($mba_student_body_graduated as $o_student_graduated) {
                    $sheet2->setCellValue('A'.$i_row2, $i_num2++);
                    $sheet2->setCellValue('B'.$i_row2, $o_student_graduated->personal_data_name);
                    $sheet2->setCellValue('C'.$i_row2, $o_student_graduated->faculty_abbreviation);
                    $sheet2->setCellValue('D'.$i_row2, $o_student_graduated->study_program_abbreviation);
                    $sheet2->setCellValue('E'.$i_row2, $o_student_graduated->student_number);
                    $sheet2->setCellValue('F'.$i_row2, $o_student_graduated->student_type);
                    $sheet2->setCellValue('G'.$i_row2, $o_student_graduated->academic_year_id);
                    $sheet2->setCellValue('H'.$i_row2, $o_student_graduated->student_status);

                    $col_sem = 'I';
                    for ($sem=1; $sem <= 14 ; $sem++) { 
                        $key_sem = 'sem_'.$sem;
                        $amount = $o_student_graduated->$key_sem;
                        $amount = str_replace('.', '', $amount);
                        $sheet2->setCellValue($col_sem.$i_row2, $amount);
                        $col_sem++;
                    }
                    $sheet2->setCellValue('W'.$i_row2, $o_student_graduated->ori_total_tunggakan);
                    $sheet2->setCellValue('X'.$i_row2, $o_student_graduated->ori_semester_lain_tunggakan);
                    $sheet2->setCellValue('Y'.$i_row2, $o_student_graduated->ori_semester_tunggakan);

                    $d_total_graduated += $o_student_graduated->ori_total_tunggakan;
                    $i_row2++;
                }
                $last_irow2 = $i_row2 - 1;
                $sheet2->setCellValue('A'.$i_row2, "Total");
                $sheet2->setCellValue('W'.$i_row2, "=SUM(W4:W$last_irow2)");
                $sheet2->setCellValue('X'.$i_row2, "=SUM(X4:X$last_irow2)");
                $sheet2->setCellValue('Y'.$i_row2, "=SUM(Y4:Y$last_irow2)");
                $sheet2->getStyle('I4:Y'.$i_row2)->getNumberFormat()->setFormatCode('#,##');
            }
            if ($mba_invoice_semester) {
                foreach ($mba_invoice_semester as $o_semester) {
                    $sheet3->setCellValue('A'.$i_row3, $i_num3++);
                    $sheet3->setCellValue('B'.$i_row3, $o_semester->personal_data_name);
                    $sheet3->setCellValue('C'.$i_row3, $o_semester->faculty_abbreviation);
                    $sheet3->setCellValue('D'.$i_row3, $o_semester->study_program_abbreviation);
                    $sheet3->setCellValue('E'.$i_row3, $o_semester->student_number);
                    $sheet3->setCellValue('F'.$i_row3, $o_semester->student_type);
                    $sheet3->setCellValue('G'.$i_row3, $o_semester->academic_year_id);
                    $sheet3->setCellValue('H'.$i_row3, $o_semester->student_status);

                    $col_sem = 'I';
                    $i_installment = 1;
                    foreach ($a_month as $i_month) {
                        $key_installment = 'installment_'.$i_installment;
                        $sheet3->setCellValue($col_sem.$i_row3, $o_semester->$key_installment);
                        $col_sem++;
                        $i_installment++;

                        $d_semester_installment = ($o_semester->$key_installment == '') ? 0 : $o_semester->$key_installment;
                        if ($current_month == $i_month) {
                            $d_total_active_current_installment += $d_semester_installment;
                        }
                        else if ($i_month < $current_month) {
                            $d_total_active_prev_installment += $d_semester_installment;
                        }
                        else if ($i_month > $current_month) {
                            $d_total_active_next_installment += $d_semester_installment;
                        }
                    }
                    $sheet3->setCellValue('O'.$i_row3, $o_semester->total_unpaid_installment);
                    $i_row3++;
                }
                $last_irow3 = $i_row3 - 1;
                $sheet3->setCellValue('A'.$i_row3, "Total");
                $sheet3->setCellValue('I'.$i_row3, "=SUM(I4:I$last_irow3)");
                $sheet3->setCellValue('J'.$i_row3, "=SUM(J4:J$last_irow3)");
                $sheet3->setCellValue('K'.$i_row3, "=SUM(K4:K$last_irow3)");
                $sheet3->setCellValue('L'.$i_row3, "=SUM(L4:L$last_irow3)");
                $sheet3->setCellValue('M'.$i_row3, "=SUM(M4:M$last_irow3)");
                $sheet3->setCellValue('N'.$i_row3, "=SUM(N4:N$last_irow3)");
                $sheet3->setCellValue('O'.$i_row3, "=SUM(O4:O$last_irow3)");
                $sheet3->getStyle('I4:O'.$i_row3)->getNumberFormat()->setFormatCode('#,##');
            }

            $sheet0->setCellValue('B3', $d_total_active_prev_semester);
            $sheet0->setCellValue('B4', $d_total_active_prev_installment);
            $sheet0->setCellValue('B5', $d_total_active_current_installment);
            $sheet0->setCellValue('B6', $d_total_active_next_installment);
            $sheet0->setCellValue('B7', "=SUM(B3:B6)");
            $sheet0->setCellValue('B8', $d_total_graduated);
            $sheet0->setCellValue('B9', "=SUM(B7:B8)");

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $s_filepath = urlencode(base64_encode('finance/report/'.$s_filename));
            $s_custom_filename = urlencode($s_file_name.'|'.date('H:i'));
            $uri = base_url().'file_manager/download_files/'.$s_filepath.'/'.$s_custom_filename.'.xlsx';

            $a_return = ['code' => 0, 'message' => 'Function disabled!', 'uri' => $uri];
            print json_encode($a_return);exit;
        }
    }
}

?>