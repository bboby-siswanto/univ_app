<?php
error_reporting(0);
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;	

class Excel_download extends App_core
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('academic/Class_group_model','Cgm');
        $this->load->model('alumni/Alumni_model','Alm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('employee/Employee_model', 'Emm');
        $this->load->model('finance/Invoice_model', 'Im');
        $this->load->model('institution/Institution_model', 'Inm');
        $this->load->model('personal_data/Family_model', 'Fm');

        $this->load->model('portal/Portal_model', 'Mdb');
    }

    public function download_score($s_file)
    {
        $s_file_path = APPPATH.'/uploads/templates/score_class/'.$s_file;
		if(!file_exists($s_file_path)){
			return show_404();
		}
		else{
			$a_path_info = pathinfo($s_file_path);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_file));
			readfile( $s_file_path );
			exit;
		}
    }

    public function generate_ofse_invoice_report()
    {
        $this->download_custom_student_report('ofse');
    }
    
    public function generate_thesis_invoice_report()
    {
        $this->download_custom_student_report('thesis');
    }

    public function get_student_active()
    {
        $mba_student_active = $this->Stm->get_student_filtered(['ds.student_status' => 'active', 'ds.finance_year_id < ' => '2022']);
        if ($mba_student_active) {
            print('<table>');
            foreach ($mba_student_active as $o_student) {
                print('<tr>');
                print('<td>'.$o_student->personal_data_name.'</td>');
                print('<td>="'.$o_student->student_number.'"</td>');
                print('<td>'.$o_student->study_program_abbreviation.'</td>');
                print('<td>'.$o_student->faculty_abbreviation.'</td>');
                print('<td>'.$o_student->status_student.'</td>');
                print('<td>'.$o_student->academic_year_id.'</td>');
                print('<td>'.$o_student->finance_year_id.'</td>');
                print('</tr>');
            }
            print('</table>');
        }
        // print(count($mba_student_active));exit;
    }

    public function invoice_student_active()
    {
        $a_student_exchange = ['241467b7-ec27-4ff7-b7f1-63acd5730bda', '671b3434-f57e-4532-9c73-f2d41b31f2b2', 'e08882fd-476e-451b-a927-a2436a8947c0'];
        $a_month = [
            '1' => [7,8,9,10,11,12],
            '2' => [1,2,3,4,5,6]
        ];

        $a_personal_data_invoice_semester_paid = $this->config->item('invoice_semester_paid');

        $s_academic_year_id = 2021;
        $s_semester_type_id = 1;

        $current_installment_index = array_search(intval(date('m')), $a_month["$s_semester_type_id"]);

        $s_file_name = 'List_student_'.$s_academic_year_id.'-'.$s_semester_type_id;
        $s_filename = $s_file_name.'.xlsx';

        $s_file_path = APPPATH."uploads/finance/report/";
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

        $s_template_path = APPPATH.'uploads/templates/finance/template_student_fe_report.xlsx';

        $o_spreadsheet = IOFactory::load("$s_template_path");
		$o_sheet = $o_spreadsheet->getActiveSheet();

        $no = 0;
        $i_sheet = 1;
        $i_row = 7;
        $c_col = 'I';

        $mba_student_active = $this->Stm->get_student_filtered(['ds.student_status' => 'active', 'ds.finance_year_id < ' => '2022']);
        if ($mba_student_active) {
            foreach ($mba_student_active as $mbo_student_data) {
                if (in_array($mbo_student_data->student_id, $a_student_exchange)) {
                    continue;
                }
                $no++;
                $d_total_amount_unpaid = 0;
                $c_current_semester = $c_col;

                $o_sheet->setCellValue('A'.$i_row, $no);
                $o_sheet->setCellValue('B'.$i_row, $mbo_student_data->faculty_abbreviation);
                $o_sheet->setCellValue('C'.$i_row, $mbo_student_data->study_program_abbreviation);
                $o_sheet->setCellValue('D'.$i_row, $mbo_student_data->personal_data_name);
                $o_sheet->setCellValue('E'.$i_row, $mbo_student_data->student_number);
                $o_sheet->setCellValue('F'.$i_row, ucfirst($mbo_student_data->status_student));
                $o_sheet->setCellValue('G'.$i_row, $mbo_student_data->academic_year_id);
                $o_sheet->setCellValue('H'.$i_row, $mbo_student_data->finance_year_id);
                
                $mba_student_semester_selected = $this->General->get_where('dt_student_semester', [
                    'student_id' => $mbo_student_data->student_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id
                ]);

                // print('<pre>');var_dump($mba_student_semester_selected);exit;
                if (!$mba_student_semester_selected) {
                    print('no student semester: '.$mbo_student_data->student_id);exit;
                }

                $c_semester = $c_col;
                $s_student_number = $mbo_student_data->student_number;
                $s_student_name = $mbo_student_data->personal_data_name;
                $s_status = 'paid';

                $b_has_onleave = false;
                for ($i=0; $i < 14; $i++) { 
                // }
                // while ($o_sheet->getCell($c_semester."6")->getValue() !== NULL) {
                    $a_notes = [];
                    // $i_semester_number = $o_sheet->getCell($c_semester."6")->getValue();
                    $i_semester_number = $i + 1;
                    $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_number' => $i_semester_number]);
                    if (!$mbo_semester_data) {
                        print('[ERROR] no have semester '.$i_semester_number.': '.$mbo_student_data->student_id);exit;
                    }
                    $mba_student_semester_list = $this->Smm->get_semester_student($mbo_student_data->student_id, [
                        'ss.semester_id' => $mbo_semester_data[0]->semester_id
                    ]);
                    $mba_student_invoice = $this->Im->student_has_invoice_list($mbo_student_data->personal_data_id, [
                        'rs.semester_number' => $i_semester_number,
                        'df.payment_type_code' => '02'
                    ], ['created', 'pending', 'paid']);

                    $mba_portal_invoice_student = $this->Mdb->retrieve_data('invoice', [
                        'student_id' => $mbo_student_data->student_portal_id,
                        'semester_id' => $mbo_semester_data[0]->semester_id,
                        'payment_type_id' => 2
                    ]);

                    $mba_score_data = $this->Scm->get_score_data_transcript([
                        'sc.student_id' => $mbo_student_data->student_id,
                        'sc.score_approval' => 'approved',
                        'curs.curriculum_subject_credit > ' => 0,
                        'curs.curriculum_subject_type !=' => 'extracurricular',
                        'sc.semester_id' => $mbo_semester_data[0]->semester_id
                    ]);

                    $sks_approved = 0;
                    if ($mba_score_data) {
                        foreach ($mba_score_data as $o_score) {
                            $sks_approved += $o_score->curriculum_subject_credit;
                        }
                    }

                    $this_onleave = false;
                    if (($mba_student_semester_list) AND ($mba_student_semester_list[0]->student_semester_status == 'onleave')) {
                        $this_onleave = true;
                        $b_has_onleave = true;
                        array_push($a_notes, "- Student Onleave");
                    }

                    $this_semester_selected = false;
                    if (($mba_student_semester_selected) AND (!is_null($mba_student_semester_selected[0]->semester_id))) {
                        if ($mba_student_semester_selected[0]->semester_id == $mbo_semester_data[0]->semester_id) {
                            $this_semester_selected = true;
                            $c_current_semester = $c_semester;
                        }
                    }
                    
                    $d_semester_amount_paid = 0;
                    $d_semester_amount_unpaid = '?';

                    $a_scholarship_id = [];
                    $a_scholarship_additional_id = [];
                    if ($i_semester_number <= 8) {
                        $mba_student_scholarship = $this->Pdm->get_personal_data_scholarship($mbo_student_data->personal_data_id, [
                            'pds.scholarship_status' => 'active',
                            // 'rs.cut_of_tuition_fee' => 'yes',
                            // 'rs.scholarship_fee_type' => 'main'
                        ]);
                        
                        if ($mba_student_scholarship) {
                            foreach ($mba_student_scholarship as $o_scholarship) {
                                if ($o_scholarship->scholarship_fee_type == 'main') {
                                    array_push($a_scholarship_id, $o_scholarship->scholarship_id);
                                }
                                else {
                                    array_push($a_scholarship_additional_id, $o_scholarship->scholarship_id);
                                }
                            }
                        }
                    }

                    $a_filter_fee = [
                        'payment_type_code' => '02',
                        'academic_year_id' => $mbo_student_data->finance_year_id,
                        'study_program_id' => $mbo_student_data->study_program_id,
                        'semester_id' => $mbo_semester_data[0]->semester_id,
                        'fee_amount_type' => 'main',
                        'fee_special' => 'false'
                    ];

                    if (count($a_scholarship_id) == 0) {
                        $a_scholarship_id = false;
                    }

                    // if (($mbo_student_data->student_id == '36a18b49-fee8-4af9-9b38-95f32d75ea5e') AND ($i_semester_number == 1)) {
                    //     $a_filter_fee = ['fee_id' => 'f9fc640d-9f8c-49b8-a6ee-9e23dee6aa3b'];
                    // }
                    // else if (($mbo_student_data->student_id == '8a44da91-26a5-4581-942f-32c455271b06') AND ($i_semester_number == '4')) {
                    //     $a_filter_fee = ['fee_id' => '7318c3f7-0f4a-4119-9b2d-bad3725aba02'];
                    // }
                    // else if (($mbo_student_data->student_id == '8a44da91-26a5-4581-942f-32c455271b06') AND ($i_semester_number == '3')) {
                    //     $a_filter_fee = ['fee_id' => '7318c3f7-0f4a-4119-9b2d-bad3725aba02'];
                    // }
                    // else if (($mbo_student_data->student_id == '8a44da91-26a5-4581-942f-32c455271b06') AND ($i_semester_number == '2')) {
                    //     $a_filter_fee = ['fee_id' => '7318c3f7-0f4a-4119-9b2d-bad3725aba02'];
                    // }
                    
                    $mba_billing_data = $this->Im->get_fee_data($a_filter_fee, $a_scholarship_id);
                    $b_go = true;
                    if (!$mba_billing_data) {
                        if (($i_semester_number < 8) AND ($mba_student_semester_selected)) {
                            print('fee not found for:'.$mbo_student_data->personal_data_name.' >');
                            var_dump($a_filter_fee);exit;
                        }
                        // print('fee not found for:'.$mbo_student_data->personal_data_name.' >');
                        // var_dump($a_filter_fee);exit;
                    }

                    $d_billing = $mba_billing_data[0]->fee_amount;

                    if ($i_semester_number > 8) {
                        if ($sks_approved > 0) {
                            $d_billing = $d_billing * $sks_approved;
                        }
                        else {
                            $d_billing = 0;
                        }

                        array_push($a_notes, "- $sks_approved SKS Approved");
                    }

                    if ($this_onleave) {
                        $mba_student_invoice = $this->Im->student_has_invoice_list($mbo_student_data->personal_data_id, [
                            'rs.semester_number' => $i_semester_number,
                            'df.payment_type_code' => '05'
                        ], ['created', 'pending', 'paid']);
    
                        $mba_portal_invoice_student = $this->Mdb->retrieve_data('invoice', [
                            'student_id' => $mbo_student_data->student_portal_id,
                            'semester_id' => $mbo_semester_data[0]->semester_id,
                            'payment_type_id' => 5
                        ]);

                        $a_filter_fee = [
                            'payment_type_code' => '05',
                            'academic_year_id' => $mbo_student_data->finance_year_id,
                            'semester_id' => $mbo_semester_data[0]->semester_id,
                            'fee_amount_type' => 'main',
                            'fee_special' => 'false'
                        ];

                        $mba_billing_data = $this->Im->get_fee_data($a_filter_fee);
                        if (!$mba_billing_data) {
                            print('fee not found for:'.$mbo_student_data->personal_data_name.' >');
                            var_dump($a_filter_fee);exit;
                        }

                        $d_billing = $mba_billing_data[0]->fee_amount;
                    }

                    if (($b_has_onleave) AND (!$this_onleave)) {
                        if ($mba_student_semester_list) {
                            $mba_student_invoice = $this->Im->student_has_invoice_list($mbo_student_data->personal_data_id, [
                                'di.academic_year_id' => $mba_student_semester_list[0]->semester_year_id,
                                'di.semester_type_id' => $mba_student_semester_list[0]->semester_type_id,
                                'df.payment_type_code' => '02'
                            ], ['created', 'pending', 'paid']);

                            if ($mba_student_invoice) {
                                $a_filter_fee = [
                                    'payment_type_code' => '02',
                                    'academic_year_id' => $mbo_student_data->finance_year_id,
                                    'study_program_id' => $mbo_student_data->study_program_id,
                                    'semester_id' => $mba_student_invoice[0]->semester_id,
                                    'fee_amount_type' => 'main',
                                    'fee_special' => 'false'
                                ];
            
                                $mba_billing_data = $this->Im->get_fee_data($a_filter_fee, $a_scholarship_id);
                                if (!$mba_billing_data) {
                                    print('fee not found for:'.$mbo_student_data->personal_data_name.' >');
                                    var_dump($a_filter_fee);exit;
                                }

                                array_push($a_notes, "- Fyler semester ".$mba_student_invoice[0]->semester_id);
            
                                $d_billing = $mba_billing_data[0]->fee_amount;
                            }
                        }
                    }

                    if (count($a_scholarship_additional_id) > 0) {
                        $a_filter_sc_fee = [
                            'academic_year_id' => $mbo_student_data->finance_year_id,
                            'fee_amount_type' => 'additional',
                            'fee_special' => 'false'
                        ];
                        $mba_scholarship_additional = $this->Im->get_fee_data($a_filter_sc_fee, $a_scholarship_additional_id);

                        if (!$mba_scholarship_additional) {
                            print('fee scholarship not found for personal id '.$mbo_student_data->personal_data_id.':');
                            var_dump($a_scholarship_additional_id);exit;
                        }

                        foreach ($mba_scholarship_additional as $o_fee_additional) {
                            if ($o_fee_additional->fee_amount_sign_type == 'negative') {
                                if ($o_fee_additional->fee_amount_number_type == 'number') {
                                    $d_billing -= $o_fee_additional->fee_amount;
                                }else{
                                    $d_fee_amount_additional = ($d_billing * $o_fee_additional->fee_amount) / 100;
                                    $d_billing -= $d_fee_amount_additional;
                                }
                            }else{
                                if ($o_fee_additional->fee_amount_number_type == 'number') {
                                    $d_billing += $o_fee_additional->fee_amount;
                                }else{
                                    $d_fee_amount_additional = ($d_billing * $o_fee_additional->fee_amount) / 100;
                                    $d_billing += $d_fee_amount_additional;
                                }
                            }
                        }
                    }

                    if ($mba_portal_invoice_student) {
                        foreach ($mba_portal_invoice_student as $o_old_invoice) {
                            if ($o_old_invoice->status == 'PAID') {
                                $s_status = 'paid';
                            }
                            else {
                                $s_status = 'unpaid';
                            }
                            
                            if (!in_array($o_old_invoice->status, ['CANCELLED'])) {
                                $invoice_sub = $this->Mdb->retrieve_data('invoice_sub', ['status != ' => 'CANCELLED', 'invoice_id' => $o_old_invoice->id]);
                                // print('<pre>');var_dump($invoice_sub);exit;
                                if ($invoice_sub) {
                                    foreach ($invoice_sub as $o_sub) {
                                        $o_sub_invoice_details = $this->Mdb->retrieve_data('invoice_sub_details', ['invoice_sub_id' => $o_sub->id]);
                                        if (($o_sub->type == 'FULL') AND ($o_sub_invoice_details)) {
                                            $d_amount_paid = $o_sub_invoice_details[0]->amount_paid;
                                            if (!is_null($o_sub_invoice_details[0]->bni_transaction_id)) {
                                                $bni_data = $this->Mdb->retrieve_data('bni_transactions', [
                                                    'id' => $o_sub_invoice_details[0]->bni_transaction_id,
                                                    'payment_amount > ' => '0'
                                                ]);

                                                if ($bni_data) {
                                                    $d_amount_paid = $bni_data[0]->payment_amount;
                                                }
                                            }

                                            $d_semester_amount_paid += $d_amount_paid;
                                            break;
                                        }
                                        
                                        if (($o_sub->type == 'INSTALLMENT') AND ($o_sub_invoice_details)) {
                                            foreach ($o_sub_invoice_details as $o_details) {
                                                $d_amount_paid = $o_details->amount_paid;
                                                if (!is_null($o_details->bni_transaction_id)) {
                                                    $bni_data = $this->Mdb->retrieve_data('bni_transactions', [
                                                        'id' => $o_details->bni_transaction_id,
                                                        'payment_amount > ' => '0'
                                                    ]);
                                                    
                                                    if ($bni_data) {
                                                        $d_amount_paid = $bni_data[0]->payment_amount;
                                                    }
                                                }

                                                $d_semester_amount_paid += $d_amount_paid;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $d_additional_discount = 0;
                    if ($mba_student_invoice) {
                        $d_semester_amount_unpaid = 0;
                        // $d_semester_amount_paid = 0;

                        $mba_invoice_details = $this->Im->get_invoice_details([
                            'did.invoice_id' => $mba_student_invoice[0]->invoice_id,
                            'df.fee_amount_type' => 'additional',
                            'df.scholarship_id != ' => 'b3148593-56a9-11ea-8aee-5254005d90f6' //sibling
                        ]);

                        // if (($mbo_student_data->personal_data_id == '8ae312b4-4e6d-4543-88b8-ba03bc97bd9a') AND ($i_semester_number == 10)) {
                        //     print('inv-details<pre>');
                        //     var_dump($mba_student_invoice);
                        //     // print('<br>');
                        //     // var_dump($d_billing);
                        //     exit;
                        // }

                        if ($mba_invoice_details) {
                            foreach ($mba_invoice_details as $o_details) {
                                if ($o_details->fee_amount_sign_type == 'negative') {
                                    if ($o_details->fee_amount_number_type == 'number') {
                                        $d_billing -= $o_details->fee_amount;
                                        $d_additional_discount += $o_details->fee_amount;
                                    }else{
                                        $d_fee_amount_additional = ($d_billing * $o_details->fee_amount) / 100;
                                        $d_billing -= $d_fee_amount_additional;
                                        $d_additional_discount += $d_fee_amount_additional;
                                    }
                                }else{
                                    if ($o_details->fee_amount_number_type == 'number') {
                                        $d_billing += $o_details->fee_amount;
                                    }else{
                                        $d_fee_amount_additional = ($d_billing * $o_details->fee_amount) / 100;
                                        $d_billing += $d_fee_amount_additional;
                                    }
                                }
                            }
                        }

                        // $a_filter_fee['fee_amount_type'] = 'additional';
                        // $mba_billing_data = $this->Im->get_fee_data($a_filter_fee);
                        // if ($mba_billing_data) {
                        //     $d_billing = $mba_billing_data[0]->fee_amount;
                        // }

                        foreach ($mba_student_invoice as $o_invoice) {
                            $d_fined_included = false;
                            if ($o_invoice->invoice_status == 'PAID') {
                                $s_status = 'paid';
                            }
                            else {
                                $s_status = 'unpaid';
                            }
                            if (!in_array($o_invoice->invoice_status, ['cancelled'])) {
                                $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                                $mba_invoice_full_payment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);

                                if ($o_invoice->invoice_status == 'paid') {
                                    if (($mba_invoice_full_payment) AND ($mba_invoice_full_payment->sub_invoice_details_amount_paid > 0)) {
                                        // print('paid');exit;
                                        if (!$d_fined_included) {
                                            if ($mba_invoice_full_payment->sub_invoice_details_amount_fined > 0) {
                                                $d_billing += $mba_invoice_full_payment->sub_invoice_details_amount_fined;
                                            }
                                        }
    
                                        if ($mba_invoice_full_payment->sub_invoice_details_status != 'paid') {
                                            $d_semester_amount_unpaid += $mba_invoice_full_payment->sub_invoice_details_amount_total;
                                        }
                                    }else if ($mba_invoice_installment) {
                                        foreach ($mba_invoice_installment as $installment) {
                                            if ($installment->sub_invoice_details_amount_fined > 0) {
                                                $d_billing += $installment->sub_invoice_details_amount_fined;
                                            }
    
                                            if ($installment->sub_invoice_details_status != 'paid') {
                                                $d_semester_amount_unpaid += $installment->sub_invoice_details_amount_total;
                                            }
                                            else {
                                                $d_semester_amount_paid += $installment->sub_invoice_details_amount_paid;
                                            }
                                        }
                                    }

                                    if ($mba_invoice_full_payment) {
                                        $d_semester_amount_paid += $mba_invoice_full_payment->sub_invoice_details_amount_paid;
                                    }
                                    
                                    if ($mba_invoice_installment) {
                                        foreach ($mba_invoice_installment as $installment) {
                                            $d_semester_amount_paid += $installment->sub_invoice_details_amount_paid;
                                        }
                                    }
                                }
                                else {
                                    if ($mba_invoice_installment) {
                                        $i_current_month = $a_month["$s_semester_type_id"][$current_installment_index];
                                        // $i_installment_current = $current_installment_index + 1;
                                        // if (count($mba_invoice_installment) < 6) {
                                        //     # code...
                                        // }

                                        // // if (count($mba_invoice_installment) == 6) {
                                        //     $avail_counter_installment = count($mba_invoice_installment) - $i_installment_current;
                                        //     $installment_billing = $d_billing / count($mba_invoice_installment);
                                        //     $d_billing = $i_installment_current * $installment_billing;
                                        // // }
                                        if (($s_academic_year_id == $o_invoice->invoice_academic_year) AND ($s_semester_type_id == $o_invoice->invoice_semester_type)) {
                                            // 
                                        }
                                        // $d_billing = 0;

                                        foreach ($mba_invoice_installment as $installment) {
                                            // if (($s_academic_year_id == $o_invoice->invoice_academic_year) AND ($s_semester_type_id == $o_invoice->invoice_semester_type)) {
                                            //     if (intval(date('m', strtotime($installment->sub_invoice_details_real_datetime_deadline))) > intval($i_current_month)) {
                                            //         $d_billing -= $installment->sub_invoice_details_amount_total;
                                            //     }
                                            //     // else {
                                            //     //     # code...
                                            //     // }
                                            // }

                                            if ($installment->sub_invoice_details_amount_fined > 0) {
                                                $d_billing += $installment->sub_invoice_details_amount_fined;
                                                $d_fined_included = true;
                                            }
    
                                            if ($installment->sub_invoice_details_status != 'paid') {
                                                $d_semester_amount_unpaid += $installment->sub_invoice_details_amount_total;
                                            }
                                            else {
                                                $d_semester_amount_paid += $installment->sub_invoice_details_amount_paid;
                                            }
                                        }
                                    }
                                    
                                    if ($mba_invoice_full_payment) {
                                        if (!$d_fined_included) {
                                            if ($mba_invoice_full_payment->sub_invoice_details_amount_fined > 0) {
                                                $d_billing += $mba_invoice_full_payment->sub_invoice_details_amount_fined;
                                            }
                                        }
    
                                        if ($mba_invoice_full_payment->sub_invoice_details_status != 'paid') {
                                            $d_semester_amount_unpaid += $mba_invoice_full_payment->sub_invoice_details_amount_total;
                                        }
                                        else {
                                            $d_semester_amount_paid += $mba_invoice_full_payment->sub_invoice_details_amount_paid;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($d_semester_amount_paid == 0) {
                        $d_semester_amount_unpaid = $d_billing;
                    }
                    else if ($d_semester_amount_paid > 0) {
                        $d_semester_amount_unpaid = $d_billing - $d_semester_amount_paid;
                    }

                    if (in_array($i_semester_number, [1,2])) {
                        if ($mbo_student_data->finance_year_id <= 2018) {
                            // if (!in_array($mbo_student_data->student_id, ['66813ab5-2bce-4288-a367-36d7bbd5a751'])) {
                                $d_semester_amount_unpaid = 0;
                            // }
                        }
                        // else if ($i_semester_number == 1) {
                        //     $d_semester_amount_unpaid = 0;
                        // }
                    }
                    else if ($s_status == 'paid') {
                        $d_semester_amount_unpaid = 0;
                    }
                    // 
                    if (($mbo_student_data->student_number =='11202008012') AND ($i_semester_number == 2)) {
                        print('<pre>');var_dump($d_semester_amount_unpaid);exit;
                    }

                    if (($mbo_student_data->student_id == '3e772464-8ab4-4b6b-a0fc-7fac7e33cc20') AND (in_array($i_semester_number, [2]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == 'fa41737f-b924-4e89-a5b1-f781ee946e9d') AND (in_array($i_semester_number, [2,3,4]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == 'cae02c5c-6114-44e3-ba96-2a2d4a552d7d') AND (in_array($i_semester_number, [2,3]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == 'be78f1b6-63e1-40b2-bdb5-2dd16345bbfd') AND (in_array($i_semester_number, [2,3,4]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '0f72863d-fb2a-4a60-be80-c685544001cc') AND (in_array($i_semester_number, [2]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == 'df283a81-948f-4815-adeb-b0ea34392468') AND (in_array($i_semester_number, [2]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '4cac2de8-457f-479a-a507-535bca71f5c5') AND (in_array($i_semester_number, [2]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '383dc919-d09f-4985-a1ac-3ee10d4ff756') AND (in_array($i_semester_number, [4,5]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '8e3ade91-939c-4ca7-b2ce-bb24d8c00456') AND (in_array($i_semester_number, [1,2]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == 'e5b843bd-1c1b-4205-8a84-d0405ac31e75') AND (in_array($i_semester_number, [1,2,3,4,5,6]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '41a1bcfb-5764-4db4-9858-234e13c9f5cc') AND (in_array($i_semester_number, [1,2,4]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '34ebd2b8-0171-4c30-8619-444957d8fc53') AND (in_array($i_semester_number, [1,2,3,5]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '7923e4e6-8b5d-4444-a61e-211de6db7a83') AND (in_array($i_semester_number, [1,2]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == 'c521af7f-cb19-4c55-ad92-2caa25e6e2e8') AND (in_array($i_semester_number, [1,2,3]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '2afe8973-b5b8-4e92-90e5-fb28c6f8cb09') AND (in_array($i_semester_number, [1,2,3,5]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == 'abbb1d53-af3f-4e8b-9c38-8e927a4a936d') AND (in_array($i_semester_number, [1,2,4,5]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '52b0e542-968b-4632-b16e-bad1942740bf') AND (in_array($i_semester_number, [6]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '5bca8866-e3ef-41b3-b24b-3ae629489fa7') AND (in_array($i_semester_number, [2,3]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '69fd2fc8-05df-4de0-ba9c-21c7b66f1492') AND (in_array($i_semester_number, [2]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '4b35e1bb-7d9f-47d0-8486-b5ddf8052c7c') AND (in_array($i_semester_number, [2]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '93e2c28d-160d-42d3-81fa-511bd9de3615') AND (in_array($i_semester_number, [6]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == 'b4a3b8b6-2cbd-4bd9-b6d6-ee8ca45f05a4') AND (in_array($i_semester_number, [6]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '4e6a0243-f50c-463f-a654-3035f1acc51b') AND (in_array($i_semester_number, [2]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == 'fd881b07-550e-4eef-9c38-78df4b4f22d6') AND (in_array($i_semester_number, [2]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '528f6c3c-9d5c-41d1-b34b-b5f24fe8530e') AND (in_array($i_semester_number, [2]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '3338d11d-25b8-42fc-9a55-625e6897828e') AND (in_array($i_semester_number, [4]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '420ff310-cab8-4610-b7d1-2599d6eae3c0') AND (in_array($i_semester_number, [2]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == 'c18f818e-4004-41c5-bec5-9a548e0c5ddd') AND (in_array($i_semester_number, [3,4]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == 'eadf0567-141c-4751-a7b3-f472e3e5b284') AND (in_array($i_semester_number, [4]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '6021b24e-1c40-41c4-89a6-59b42bd1821c') AND (in_array($i_semester_number, [7]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '5c438173-124e-46a7-8126-50a3f6eb439a') AND (in_array($i_semester_number, [4]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '874f314b-f2dd-4357-9891-9b4e74d6cb5b') AND (in_array($i_semester_number, [4]))) {
                        $d_semester_amount_unpaid = 4500000;
                    }

                    // if ($a_personal_data_invoice_semester_paid[$i_semester_number][$mbo_student_data->personal_data_id] !== null) {
                    //     $d_semester_amount_unpaid = 0;
                    // }
                    // print('<pre>');var_dump($a_personal_data_invoice_semester_paid[$i_semester_number][$mbo_student_data->personal_data_id]);exit;
                    // if (($i_semester_number == '9') AND ($mbo_student_data->personal_data_id == '0abe5e86-26fa-47e0-8112-c974682f388a')) {
                    //     print('<pre>');var_dump($a_personal_data_invoice_semester_paid[$i_semester_number][$mbo_student_data->personal_data_id]);exit;
                    // }
                    if (($a_personal_data_invoice_semester_paid[$i_semester_number] !== null) AND (in_array($mbo_student_data->personal_data_id, $a_personal_data_invoice_semester_paid[$i_semester_number]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    
                    if ($d_semester_amount_unpaid < 0) {
                        // print($d_semester_amount_paid);exit;
                        $over = $d_semester_amount_paid - $d_billing;
                        if ($d_additional_discount > 0) {
                            $over -= $d_additional_discount;
                        }
                        $d_semester_amount_unpaid = 0;
                        if ($over > 0) {
                            // array_push($a_notes, '- over paid Rp.'.number_format($over , 0, ".", "."));
                        }
                    }
                    // print('<pre>');var_dump($d_semester_amount_unpaid);exit;

                    // $d_amount_semester = ($this_semester_selected) ? $d_semester_amount_unpaid : 0;
                    $d_total_amount_unpaid += $d_semester_amount_unpaid;
                    $o_sheet->setCellValue($c_semester.$i_row, $d_semester_amount_unpaid);
                    $o_sheet->getStyle($c_semester.$i_row)->getNumberFormat()->setFormatCode('#,##');

                    // if ($i_semester_number == 6) {
                    //     print('<pre>');
                    //     var_dump($c_semester.$i_row);exit;
                    // }
                    if (count($a_notes) > 0) {
                        $o_sheet->getComment($c_semester.$i_row)->setAuthor('Database');
                        $commentRichText = $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun('Notes:');
                        $commentRichText->getFont()->setBold(true);
                        $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun("\r\n");
                        $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun(implode("\r\n", $a_notes));
                        $o_sheet->getComment($c_semester.$i_row)->setWidth("300px")->setHeight("120px");
                    }
                    
                    if ($this_semester_selected) {
                        $c_semester = 'W';
                        break;
                    }else {
                        $c_semester++;
                    }
                }

                $c_last_semester = chr(ord($c_semester) - 1);
                if ($c_current_semester > $c_col) {
                    $c_before_current_semester = $c_current_semester;
                    $c_before_current_semester = chr(ord($c_before_current_semester) - 1);
                    // $c_before_current_semester--;
                }
                else {
                    $c_before_current_semester = $c_col;
                }

                $o_sheet->getStyle($c_semester.$i_row)->getNumberFormat()->setFormatCode('#,##');
                $o_sheet->setCellValue($c_semester++.$i_row, '=SUM('.$c_col.$i_row.':'.$c_last_semester.$i_row.')');
                $o_sheet->getStyle($c_semester.$i_row)->getNumberFormat()->setFormatCode('#,##');
                $o_sheet->setCellValue($c_semester++.$i_row, '=SUM('.$c_col.$i_row.':'.$c_before_current_semester.$i_row.')');
                $o_sheet->getStyle($c_semester.$i_row)->getNumberFormat()->setFormatCode('#,##');
                $o_sheet->setCellValue($c_semester++.$i_row, '='.$c_current_semester.$i_row);
                $o_sheet->setCellValue($c_semester++.$i_row, (($d_total_amount_unpaid > 0) ? 'NOT ELIGIBLE' : 'ELIGIBLE'));
                
                $sheet_name = $mbo_student_data->personal_data_name;
                if (strlen($sheet_name) > 30) {
                    $a_names = explode(' ', $sheet_name);
                    $sheet_name = $a_names[0].' '.$a_names[1];
                }

                // $o_spreadsheet->createSheet();
                // $o_sheet_history = $o_spreadsheet->getSheet($i_sheet)->setTitle($sheet_name);
                // $i_sheet++;

                // $a_historycall_data = $this->_get_historycall($mbo_student_data);
                // if (count($a_historycall_data) > 0) {
                //     $a_historycall_data = array_values($a_historycall_data);

                //     // $s_student_name = str_replace(' ', '_', strtolower($mbo_student_data->personal_data_name));
                //     $i_row_sheet = 1;
                //     $o_sheet_history->setCellValue('A'.$i_row_sheet, 'Payment Type');
                //     $o_sheet_history->setCellValue('B'.$i_row_sheet, 'Billed Amount');
                //     $o_sheet_history->setCellValue('C'.$i_row_sheet, 'Description');
                //     $o_sheet_history->setCellValue('D'.$i_row_sheet, 'Paid Amount');
                //     $o_sheet_history->setCellValue('E'.$i_row_sheet, 'Datetime Payment');
                //     $o_sheet_history->setCellValue('F'.$i_row_sheet, 'Billing Status');
                //     $o_sheet_history->setCellValue('G'.$i_row_sheet, 'Invoice Status');
                //     $o_sheet_history->setCellValue('H'.$i_row_sheet, 'Invoice Note');
                //     $o_sheet_history->setCellValue('I'.$i_row_sheet, 'Billing Note');
                //     $i_row_sheet++;
    
                //     foreach ($a_historycall_data as $data) {
                //         $o_sheet_history->setCellValue('A'.$i_row_sheet, $data['payment_type']);
                //         $o_sheet_history->setCellValue('B'.$i_row_sheet, $data['billed_amount']);
                //         $o_sheet_history->setCellValue('C'.$i_row_sheet, $data['description']);
                //         $o_sheet_history->setCellValue('D'.$i_row_sheet, $data['paid_amount']);
                //         $o_sheet_history->setCellValue('E'.$i_row_sheet, $data['datetime_payment']);
                //         $o_sheet_history->setCellValue('F'.$i_row_sheet, $data['status']);
                //         $o_sheet_history->setCellValue('G'.$i_row_sheet, $data['invoice_status']);
                //         $o_sheet_history->setCellValue('H'.$i_row_sheet, $data['note_invoice']);
                //         $o_sheet_history->setCellValue('I'.$i_row_sheet, $data['note_va']);

                //         $o_sheet_history->getStyle('B'.$i_row_sheet)->getNumberFormat()->setFormatCode('#,##');
                //         $o_sheet_history->getStyle('D'.$i_row_sheet)->getNumberFormat()->setFormatCode('#,##');
                //         $i_row_sheet++;
                //     }
                // }

                // 
                $i_row++;
            }
        }

        // while (!empty(trim($o_sheet->getCell("E$i_row")->getValue()))) {
            

        //     $mbo_student_data = $this->Stm->get_student_filtered([
        //         'ds.student_number' => $s_student_number,
        //         'dpd.personal_data_name' => $s_student_name
        //     ]);

        //     if ($mbo_student_data) {
        //         $mbo_student_data = $mbo_student_data[0];
                
        // {sampe sini
        //         // print('kosong');exit;
        //     }
        //     else {
        //         // kasih tanda warna merah + note
        //         print('row number '.$i_row.' not found in portal!: "'.$s_student_number.'; '.$s_student_name);exit;
        //     }
        //     $i_row++;
        // }

        // // $o_sheet->insertNewColumnBefore('G', 1);
        // // $o_sheet->insertNewColumnBefore('G', 1);
        // // $o_sheet->mergeCells('G5:G6');
        // // $o_sheet->mergeCells('H5:H6');
        // // $o_sheet->setCellValue('G5', 'Batch');
        // // $o_sheet->setCellValue('H5', 'Year In');

        // // $x_row = 7;
        // // while (!empty(trim($o_sheet->getCell("E$x_row")->getValue()))) {
        // //     $s_student_number = str_replace('=', '', str_replace('"', '', $o_sheet->getCell("E$x_row")->getValue()));
        // //     $s_student_status = $o_sheet->getCell("F$x_row")->getValue();
        // //     $mbo_student_data = $this->Stm->get_student_filtered([
        // //         'ds.student_number' => $s_student_number,
        // //         'ds.student_status' => $s_student_status
        // //     ]);

        // //     if ($mbo_student_data) {
        // //         $mbo_student_data = $mbo_student_data[0];
        // //         $o_sheet->setCellValue('G'.$x_row, $mbo_student_data->academic_year_id);
        // //         $o_sheet->setCellValue('H'.$x_row, $mbo_student_data->finance_year_id);
        // //     }
        // //     $x_row++;
        // // }

        // // s_file_path
        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_filename);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        // if ($b_force_download) {
        $a_path_info = pathinfo($s_file_path.$s_filename);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_filename));
        readfile( $s_file_path.$s_filename );
        exit;
    }

    function get_unpaid_invoice() {
        $mba_student_data = $this->Stm->get_student_filtered(false, ['active', 'graduated']);
        if ($mba_student_data) {
            foreach ($mba_student_data as $o_student) {
                $d_total_unpaid = 0;
                $mba_invoice_tuitionfee = $this->Im->student_has_invoice_list($o_student->personal_data_id, [
                    'di.academic_year_id != ' => '2023',
                    'df.payment_type_code' => '02'
                ], ['created', 'pending']);

                if ($mba_invoice_tuitionfee) {
                    foreach ($mba_invoice_tuitionfee as $o_invoice) {
                        $d_invoice_unpaid = 0;
                        $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                        $mbo_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                        if ($mba_invoice_installment) {
                            foreach ($mba_invoice_installment as $o_installment) {
                                if (($o_installment->sub_invoice_details_status != 'paid') AND ($o_installment->sub_invoice_details_amount_paid == 0)) {
                                    $d_total_unpaid += $o_installment->sub_invoice_details_amount_total;
                                    $d_invoice_unpaid += $o_installment->sub_invoice_details_amount_total;
                                }
                            }
                        }
                        else if ($mbo_invoice_fullpayment) {
                            $d_total_unpaid += $mbo_invoice_fullpayment->sub_invoice_details_amount_total;
                            $d_invoice_unpaid += $mbo_invoice_fullpayment->sub_invoice_details_amount_total;
                        }
                    }

                    if ($d_total_unpaid > 0) {
                        print($o_student->personal_data_name.'<br>');
                    }
                }
            }
        }
    }

    public function unpaid_graduated_invoice() {
        $s_template_path = APPPATH.'uploads/templates/finance/graduated_student_unpaid.xlsx';
        $s_file_name = 'Unpaid Graduate Student Invoice ('.date('d-M-Y').')';
        $s_filename = str_replace(' ', '_', $s_file_name).'.xlsx';

        $s_file_path = APPPATH."uploads/finance/report/graduate_student_unpaid/";
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

        $o_spreadsheet = IOFactory::load("$s_template_path");
		$o_sheet = $o_spreadsheet->getActiveSheet();

        $i_sheet = 1;
        $i_row = 7;
        $c_col = 'I';

        while (!empty(trim($o_sheet->getCell("E$i_row")->getValue()))) {
            $c_semester = $c_col;
            $s_student_number = str_replace('=', '', str_replace('"', '', $o_sheet->getCell("E$i_row")->getValue()));
            $s_student_number = str_replace("'", '', $s_student_number);
            // $s_student_status = $o_sheet->getCell("F$i_row")->getValue();
            $s_student_name = trim($o_sheet->getCell("D$i_row")->getValue());
            $s_status = 'paid';

            $mbo_student_data = $this->Stm->get_student_filtered([
                'ds.student_number' => $s_student_number,
                'dpd.personal_data_name' => $s_student_name
            ]);

            if ($mbo_student_data) {
                $mbo_student_data = $mbo_student_data[0];
                $d_total_amount_unpaid = 0;
                $c_current_semester = $c_col;

                $o_sheet->setCellValue('B'.$i_row, $mbo_student_data->faculty_abbreviation);
                $o_sheet->setCellValue('C'.$i_row, $mbo_student_data->study_program_abbreviation);
                $o_sheet->setCellValue('F'.$i_row, ucfirst($mbo_student_data->student_status));
                $o_sheet->setCellValue('G'.$i_row, $mbo_student_data->academic_year_id);
                $o_sheet->setCellValue('H'.$i_row, $mbo_student_data->finance_year_id);

                // for ($i=1; $i <= 14; $i++) { 
                while ($o_sheet->getCell($c_semester."6")->getValue() !== NULL) {
                    $i_semester_number = $o_sheet->getCell($c_semester."6")->getValue();
                    $mba_invoice_leavefee = $this->Im->student_has_invoice_list($mbo_student_data->personal_data_id, [
                        'rs.semester_number' => $i_semester_number,
                        'df.payment_type_code' => '05'
                    ], ['created', 'pending']);
                    $mba_invoice_tuitionfee = $this->Im->student_has_invoice_list($mbo_student_data->personal_data_id, [
                        'rs.semester_number' => $i_semester_number,
                        'df.payment_type_code' => '02'
                    ], ['created', 'pending']);
                    $mba_invoice_shortsemesterfee = $this->Im->student_has_invoice_list($mbo_student_data->personal_data_id, [
                        'rs.semester_number' => $i_semester_number.'.5',
                        'df.payment_type_code' => '04'
                    ], ['created', 'pending']);
                    $mba_invoice_graduationfee = $this->Im->student_has_invoice_list($mbo_student_data->personal_data_id, [
                        // 'rs.semester_number' => $i_semester_number.'.5',
                        'df.payment_type_code' => '09'
                    ], ['created', 'pending']);

                    $d_total_unpaidsemester = 0;
                    $a_notes = [];
                    if ($mba_invoice_tuitionfee) {
                        $d_total_invoiceunpaid = 0;
                        foreach ($mba_invoice_tuitionfee as $o_invoice) {
                            $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                            $mbo_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                            if ($mba_invoice_installment) {
                                foreach ($mba_invoice_installment as $o_installment) {
                                    if (($o_installment->sub_invoice_details_status != 'paid') AND ($o_installment->sub_invoice_details_amount_paid == 0)) {
                                        $d_total_invoiceunpaid += $o_installment->sub_invoice_details_amount_total;
                                    }
                                }
                            }
                            else if ($mbo_invoice_fullpayment) {
                                $d_total_invoiceunpaid += $mbo_invoice_fullpayment->sub_invoice_details_amount_total;
                            }
                        }

                        if ($d_total_invoiceunpaid > 0) {
                            array_push($a_notes, "Unpaid Tuition Fee: Rp.".number_format($d_total_invoiceunpaid , 0, ".", "."));
                            $d_total_unpaidsemester += $d_total_invoiceunpaid;
                        }
                    }

                    if ($mba_invoice_shortsemesterfee) {
                        $d_total_invoiceunpaid = 0;
                        foreach ($mba_invoice_shortsemesterfee as $o_invoice) {
                            $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                            $mbo_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                            if ($mba_invoice_installment) {
                                foreach ($mba_invoice_installment as $o_installment) {
                                    if (($o_installment->sub_invoice_details_status != 'paid') AND ($o_installment->sub_invoice_details_amount_paid == 0)) {
                                        $d_total_invoiceunpaid += $o_installment->sub_invoice_details_amount_total;
                                    }
                                }
                            }
                            else if ($mbo_invoice_fullpayment) {
                                $d_total_invoiceunpaid += $mbo_invoice_fullpayment->sub_invoice_details_amount_total;
                            }
                        }

                        if ($d_total_invoiceunpaid > 0) {
                            array_push($a_notes, "Unpaid Short Semester Fee: Rp.".number_format($d_total_invoiceunpaid , 0, ".", "."));
                            $d_total_unpaidsemester += $d_total_invoiceunpaid;
                        }
                    }

                    if ($mba_invoice_leavefee) {
                        $d_total_invoiceunpaid = 0;
                        foreach ($mba_invoice_leavefee as $o_invoice) {
                            $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                            $mbo_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                            if ($mba_invoice_installment) {
                                foreach ($mba_invoice_installment as $o_installment) {
                                    if (($o_installment->sub_invoice_details_status != 'paid') AND ($o_installment->sub_invoice_details_amount_paid == 0)) {
                                        $d_total_invoiceunpaid += $o_installment->sub_invoice_details_amount_total;
                                    }
                                }
                            }
                            else if ($mbo_invoice_fullpayment) {
                                $d_total_invoiceunpaid += $mbo_invoice_fullpayment->sub_invoice_details_amount_total;
                            }
                        }

                        if ($d_total_invoiceunpaid > 0) {
                            array_push($a_notes, "Unpaid Semester Leave Fee: Rp.".number_format($d_total_invoiceunpaid , 0, ".", "."));
                            $d_total_unpaidsemester += $d_total_invoiceunpaid;
                        }
                    }

                    $o_sheet->setCellValue($c_semester.$i_row, $d_total_unpaidsemester);
                    if (count($a_notes) > 0) {
                        $o_sheet->getComment($c_semester.$i_row)->setAuthor('Database');
                        $commentRichText = $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun('Notes:');
                        $commentRichText->getFont()->setBold(true);
                        $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun("\r\n");
                        $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun(implode("\r\n", $a_notes));
                        $o_sheet->getComment($c_semester.$i_row)->setWidth("300px")->setHeight("120px");
                    }

                    $c_semester++;
                }

                if ($mba_invoice_graduationfee) {
                    $d_total_invoiceunpaid = 0;
                    foreach ($mba_invoice_graduationfee as $o_invoice) {
                        $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                        $mbo_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                        if ($mba_invoice_installment) {
                            foreach ($mba_invoice_installment as $o_installment) {
                                if (($o_installment->sub_invoice_details_status != 'paid') AND ($o_installment->sub_invoice_details_amount_paid == 0)) {
                                    $d_total_invoiceunpaid += $o_installment->sub_invoice_details_amount_total;
                                }
                            }
                        }
                        else if ($mbo_invoice_fullpayment) {
                            $d_total_invoiceunpaid += $mbo_invoice_fullpayment->sub_invoice_details_amount_total;
                        }
                    }

                    if ($d_total_invoiceunpaid > 0) {
                        $o_sheet->setCellValue($c_semester++.$i_row, $d_total_invoiceunpaid);
                    }
                }

                $o_sheet->setCellValue('X'.$i_row, '=SUM('.$c_col.$i_row.':W'.$i_row.')');
            }
            else {
                print('row number '.$i_row.' not found in portal!: "'.$s_student_number.'; '.$s_student_name);exit;
            }
            $i_row++;
        }
        // print("name ".$s_filename);exit;

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_filename);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        $a_path_info = pathinfo($s_file_path.$s_filename);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_filename));
        readfile( $s_file_path.$s_filename );
        exit;
    }

    public function download_custom_student_report($s_case = 'ofse')
    {
        // if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        //     print('Please wait a minute');exit;
        // }

        $styleColor = array(
            'font'  => array(
                'color' => array('rgb' => 'DE0000')
            )
        );

        $a_month = [
            '1' => [7,8,9,10,11,12],
            '2' => [1,2,3,4,5,6]
        ];

        $a_personal_data_invoice_semester_paid = $this->config->item('invoice_semester_paid');

        $s_academic_year_id = 2022;
        $s_semester_type_id = 1;

        $current_installment_index = array_search(intval(date('m')), $a_month["$s_semester_type_id"]);

        $s_file_name = 'List_student_'.strtoupper($s_case).'_'.$s_academic_year_id.'-'.$s_semester_type_id;
        $s_filename = $s_file_name.'.xlsx';

        $s_file_path = APPPATH."uploads/finance/report/".$s_case."/";
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

        if ($s_case == 'ofse') {
            $s_template_path = APPPATH.'uploads/templates/finance/template_student_ofse_report.xlsx';
        }
        else if ($s_case == 'thesis') {
            $s_template_path = APPPATH.'uploads/templates/finance/template_student_thesis_report.xlsx';
        }
        else if ($s_case == 'nfu') {
            $s_template_path = APPPATH.'uploads/templates/finance/template_student_nfu_report.xlsx';
        }
        else if ($s_case == 'german_nfu') {
            $s_template_path = APPPATH.'uploads/templates/finance/template_student_german_nfu_report.xlsx';
        }
        else if ($s_case == 'graduation') {
            $s_template_path = APPPATH.'uploads/templates/finance/template_student_graduation_report.xlsx';
        }
        else if ($s_case == 'fe') {
            $s_template_path = APPPATH.'uploads/templates/finance/template_student_fe_report.xlsx';
        }
        else if ($s_case == 'jerman-taiwan') {
            $s_template_path = APPPATH.'uploads/templates/finance/template_student.xlsx';
        }
        else if ($s_case == 'menunggak') {
            $s_template_path = APPPATH.'uploads/templates/finance/template_student_menunggak.xlsx';
        }
        else {
            print('template not found!');exit;
        }

        $o_spreadsheet = IOFactory::load("$s_template_path");
		$o_sheet = $o_spreadsheet->getActiveSheet();

        $i_sheet = 1;
        $i_row = 7;
        $c_col = 'I';
		// print('<pre>');var_dump($o_sheet->getCell("P6")->getValue());exit;

        while (!empty(trim($o_sheet->getCell("E$i_row")->getValue()))) {
            $c_semester = $c_col;
            $s_student_number = str_replace('=', '', str_replace('"', '', $o_sheet->getCell("E$i_row")->getValue()));
            $s_student_number = str_replace("'", '', $s_student_number);
            // $s_student_status = $o_sheet->getCell("F$i_row")->getValue();
            $s_student_name = trim($o_sheet->getCell("D$i_row")->getValue());
            $s_status = 'paid';

            $mbo_student_data = $this->Stm->get_student_filtered([
                'ds.student_number' => $s_student_number,
                'dpd.personal_data_name' => $s_student_name
            ]);

            if ($mbo_student_data) {
                $mbo_student_data = $mbo_student_data[0];
                $d_total_amount_unpaid = 0;
                $c_current_semester = $c_col;

                $mba_student_score_for_ofse = $this->Scm->get_score_ofse([
                    'sc.student_id' => $mbo_student_data->student_id,
                    'sc.semester_id' => '17',
                    'sc.score_approval' => 'approved',
                    'ofse_status !=' => 'active'
                ]);

                $o_sheet->setCellValue('B'.$i_row, $mbo_student_data->faculty_abbreviation);
                $o_sheet->setCellValue('C'.$i_row, $mbo_student_data->study_program_abbreviation);
                $o_sheet->setCellValue('F'.$i_row, ucfirst($mbo_student_data->student_status));

                $mba_student_semester_selected = $this->General->get_where('dt_student_semester', [
                    'student_id' => $mbo_student_data->student_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id
                ]);

                if (!$mba_student_semester_selected) {
                    if ($mbo_student_data->student_status == 'active') {
                        print('no student semester: '.$mbo_student_data->student_id);exit;
                    }
                }

                $b_has_onleave = false;
                // for ($i=1; $i <= 14; $i++) { 
                while ($o_sheet->getCell($c_semester."6")->getValue() !== NULL) {
                    $a_notes = [];
                    $i_semester_number = $o_sheet->getCell($c_semester."6")->getValue();
                    // $i_semester_number = $i;
                    $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_number' => $i_semester_number]);
                    if (!$mbo_semester_data) {
                        print('[ERROR] no have semester '.$i_semester_number.': '.$mbo_student_data->student_id);exit;
                    }
                    $mba_student_semester_list = $this->Smm->get_semester_student($mbo_student_data->student_id, [
                        'ss.semester_id' => $mbo_semester_data[0]->semester_id
                    ]);
                    $mba_student_invoice = $this->Im->student_has_invoice_list($mbo_student_data->personal_data_id, [
                        'rs.semester_number' => $i_semester_number,
                        'df.payment_type_code' => '02'
                    ], ['created', 'pending', 'paid']);

                    $mba_portal_invoice_student = $this->Mdb->retrieve_data('invoice', [
                        'student_id' => $mbo_student_data->student_portal_id,
                        'semester_id' => $mbo_semester_data[0]->semester_id,
                        'payment_type_id' => 2
                    ]);

                    $mba_score_data = $this->Scm->get_score_data_transcript([
                        'sc.student_id' => $mbo_student_data->student_id,
                        'sc.score_approval' => 'approved',
                        'curs.curriculum_subject_credit > ' => 0,
                        'curs.curriculum_subject_type !=' => 'extracurricular',
                        'sc.semester_id' => $mbo_semester_data[0]->semester_id
                    ]);

                    $sks_approved = 0;
                    if ($mba_score_data) {
                        foreach ($mba_score_data as $o_score) {
                            $sks_approved += $o_score->curriculum_subject_credit;
                        }
                    }

                    $this_onleave = false;
                    if (($mba_student_semester_list) AND ($mba_student_semester_list[0]->student_semester_status == 'onleave')) {
                        $this_onleave = true;
                        $b_has_onleave = true;
                        array_push($a_notes, "- Student Onleave");
                    }

                    $this_semester_selected = false;
                    if (($mba_student_semester_selected) AND (!is_null($mba_student_semester_selected[0]->semester_id))) {
                        if ($mba_student_semester_selected[0]->semester_id == $mbo_semester_data[0]->semester_id) {
                            $this_semester_selected = true;
                            $c_current_semester = $c_semester;
                        }
                    }
                    
                    $d_tf_fee_amount = 0;
                    $d_semester_amount_paid = 0;
                    $d_semester_amount_unpaid = '?';

                    $a_scholarship_id = [];
                    $a_scholarship_additional_id = [];
                    if ($i_semester_number <= 8) {
                        $mba_student_scholarship = $this->Pdm->get_personal_data_scholarship($mbo_student_data->personal_data_id, [
                            'pds.scholarship_status' => 'active',
                            // 'rs.cut_of_tuition_fee' => 'yes',
                            // 'rs.scholarship_fee_type' => 'main'
                        ]);
                        
                        if ($mba_student_scholarship) {
                            foreach ($mba_student_scholarship as $o_scholarship) {
                                if ($o_scholarship->scholarship_fee_type == 'main') {
                                    array_push($a_scholarship_id, $o_scholarship->scholarship_id);
                                }
                                else {
                                    array_push($a_scholarship_additional_id, $o_scholarship->scholarship_id);
                                }
                            }
                        }
                    }

                    $a_filter_fee = [
                        'payment_type_code' => '02',
                        'academic_year_id' => $mbo_student_data->finance_year_id,
                        'study_program_id' => $mbo_student_data->study_program_id,
                        'semester_id' => $mbo_semester_data[0]->semester_id,
                        'fee_amount_type' => 'main',
                        'fee_special' => 'false'
                    ];

                    if (count($a_scholarship_id) == 0) {
                        $a_scholarship_id = false;
                        $a_filter_fee['scholarship_id'] = NULL;
                    }

                    $mba_billing_data = $this->Im->get_fee_data($a_filter_fee, $a_scholarship_id);
                    if (!$mba_billing_data) {
                        if ($mba_portal_invoice_student) {
                            print('fee not found for:'.$mbo_student_data->personal_data_name.' >');
                            var_dump($a_filter_fee);exit;
                        }
                        // continue;
                    }

                    $d_billing = $mba_billing_data[0]->fee_amount;
                    if ($i_semester_number > 8) {
                        if ($sks_approved > 0) {
                            $d_billing = $d_billing * $sks_approved;
                        }
                        else {
                            $d_billing = 0;
                        }

                        array_push($a_notes, "- $sks_approved SKS Approved");
                    }
                    
                    $d_tf_fee_amount = $d_billing;
                    // if (($s_student_number == '11201608008') AND ($i_semester_number == '11')) {
                    //     print("billing<pre>");var_dump($d_billing);exit;
                    // }
                    
                    if ($this_onleave) {
                        $mba_student_invoice = $this->Im->student_has_invoice_list($mbo_student_data->personal_data_id, [
                            'rs.semester_number' => $i_semester_number,
                            'df.payment_type_code' => '05'
                        ], ['created', 'pending', 'paid']);
    
                        $mba_portal_invoice_student = $this->Mdb->retrieve_data('invoice', [
                            'student_id' => $mbo_student_data->student_portal_id,
                            'semester_id' => $mbo_semester_data[0]->semester_id,
                            'payment_type_id' => 5
                        ]);

                        $a_filter_fee = [
                            'payment_type_code' => '05',
                            'academic_year_id' => $mbo_student_data->finance_year_id,
                            'semester_id' => $mbo_semester_data[0]->semester_id,
                            'fee_amount_type' => 'main',
                            'fee_special' => 'false'
                        ];

                        $mba_billing_data = $this->Im->get_fee_data($a_filter_fee);
                        if (!$mba_billing_data) {
                            print('fee not found for:');
                            var_dump($a_filter_fee);exit;
                        }
    
                        $d_billing = $mba_billing_data[0]->fee_amount;
                    }

                    
                    if (($b_has_onleave) AND (!$this_onleave)) {
                        if ($mba_student_semester_list) {
                            $mba_student_invoice = $this->Im->student_has_invoice_list($mbo_student_data->personal_data_id, [
                                'di.academic_year_id' => $mba_student_semester_list[0]->semester_year_id,
                                'di.semester_type_id' => $mba_student_semester_list[0]->semester_type_id,
                                'df.payment_type_code' => '02'
                            ], ['created', 'pending', 'paid']);

                            if ($mba_student_invoice) {
                                $a_filter_fee = [
                                    'payment_type_code' => '02',
                                    'academic_year_id' => $mbo_student_data->finance_year_id,
                                    'study_program_id' => $mbo_student_data->study_program_id,
                                    'semester_id' => $mba_student_invoice[0]->semester_id,
                                    'fee_amount_type' => 'main',
                                    'fee_special' => 'false'
                                ];
            
                                $mba_billing_data = $this->Im->get_fee_data($a_filter_fee, $a_scholarship_id);
                                if (!$mba_billing_data) {
                                    print('fee not found for:');
                                    var_dump($a_filter_fee);exit;
                                }

                                array_push($a_notes, "- Fyler semester ".$mba_student_invoice[0]->semester_id);
            
                                $d_billing = $mba_billing_data[0]->fee_amount;

                                if ($mba_student_invoice[0]->semester_number > 8) {
                                    if ($sks_approved > 0) {
                                        $d_billing = $d_billing * $sks_approved;
                                    }
                                    // else {
                                    //     $d_billing = 0;
                                    // }
            
                                    array_push($a_notes, "- $sks_approved SKS Approved");
                                }
                            }
                        }
                    }
                    
                    if (count($a_scholarship_additional_id) > 0) {
                        $a_filter_sc_fee = [
                            'academic_year_id' => $mbo_student_data->finance_year_id,
                            'fee_amount_type' => 'additional',
                            'fee_special' => 'false'
                        ];
                        $mba_scholarship_additional = $this->Im->get_fee_data($a_filter_sc_fee, $a_scholarship_additional_id);

                        if (!$mba_scholarship_additional) {
                            print('fee scholarship not found for personal id '.$mbo_student_data->personal_data_id.':');
                            var_dump($a_scholarship_additional_id);exit;
                        }

                        foreach ($mba_scholarship_additional as $o_fee_additional) {
                            if ($o_fee_additional->fee_amount_sign_type == 'negative') {
                                if ($o_fee_additional->fee_amount_number_type == 'number') {
                                    $d_billing -= $o_fee_additional->fee_amount;
                                }else{
                                    $d_fee_amount_additional = ($d_billing * $o_fee_additional->fee_amount) / 100;
                                    $d_billing -= $d_fee_amount_additional;
                                }
                            }else{
                                if ($o_fee_additional->fee_amount_number_type == 'number') {
                                    $d_billing += $o_fee_additional->fee_amount;
                                }else{
                                    $d_fee_amount_additional = ($d_billing * $o_fee_additional->fee_amount) / 100;
                                    $d_billing += $d_fee_amount_additional;
                                }
                            }
                        }
                    }

                    if ($mba_portal_invoice_student) {
                        foreach ($mba_portal_invoice_student as $o_old_invoice) {
                            if ($o_old_invoice->status == 'PAID') {
                                $s_status = 'paid';
                            }
                            else {
                                $s_status = 'unpaid';
                            }
                            
                            if (!in_array($o_old_invoice->status, ['CANCELLED'])) {
                                $invoice_sub = $this->Mdb->retrieve_data('invoice_sub', ['status != ' => 'CANCELLED', 'invoice_id' => $o_old_invoice->id]);
                                // print('<pre>');var_dump($invoice_sub);exit;
                                if ($invoice_sub) {
                                    foreach ($invoice_sub as $o_sub) {
                                        $o_sub_invoice_details = $this->Mdb->retrieve_data('invoice_sub_details', ['invoice_sub_id' => $o_sub->id]);
                                        if (($o_sub->type == 'FULL') AND ($o_sub_invoice_details)) {
                                            $d_amount_paid = $o_sub_invoice_details[0]->amount_paid;
                                            if (!is_null($o_sub_invoice_details[0]->bni_transaction_id)) {
                                                $bni_data = $this->Mdb->retrieve_data('bni_transactions', [
                                                    'id' => $o_sub_invoice_details[0]->bni_transaction_id,
                                                    'payment_amount > ' => '0'
                                                ]);

                                                if ($bni_data) {
                                                    $d_amount_paid = $bni_data[0]->payment_amount;
                                                }
                                            }

                                            $d_semester_amount_paid += $d_amount_paid;
                                            break;
                                        }
                                        
                                        if (($o_sub->type == 'INSTALLMENT') AND ($o_sub_invoice_details)) {
                                            foreach ($o_sub_invoice_details as $o_details) {
                                                $d_amount_paid = $o_details->amount_paid;
                                                if (!is_null($o_details->bni_transaction_id)) {
                                                    $bni_data = $this->Mdb->retrieve_data('bni_transactions', [
                                                        'id' => $o_details->bni_transaction_id,
                                                        'payment_amount > ' => '0'
                                                    ]);
                                                    
                                                    if ($bni_data) {
                                                        $d_amount_paid = $bni_data[0]->payment_amount;
                                                    }
                                                }

                                                $d_semester_amount_paid += $d_amount_paid;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    $d_additional_discount = 0;
                    if ($mba_student_invoice) {
                        $d_semester_amount_unpaid = 0;
                        // $d_semester_amount_paid = 0;

                        $mba_invoice_details = $this->Im->get_invoice_details([
                            'did.invoice_id' => $mba_student_invoice[0]->invoice_id,
                            'df.fee_amount_type' => 'additional',
                            'df.scholarship_id != ' => 'b3148593-56a9-11ea-8aee-5254005d90f6' //sibling
                        ]);
                        
                        // if (($mbo_student_data->personal_data_id == '4646c496-9aea-4368-84fb-d75e127e4581') AND ($i_semester_number == 7)) {
                        //     print('inv-details<pre>');
                        //     var_dump($mba_student_invoice);
                        //     // print('<br>');
                        //     // var_dump($d_billing);
                        //     exit;
                        // }

                        if ($mba_invoice_details) {
                            foreach ($mba_invoice_details as $o_details) {
                                if ($o_details->fee_amount_sign_type == 'negative') {
                                    if ($o_details->fee_amount_number_type == 'number') {
                                        $d_billing -= $o_details->fee_amount;
                                        $d_additional_discount += $o_details->fee_amount;
                                    }else{
                                        $d_fee_amount_additional = ($d_tf_fee_amount * $o_details->fee_amount) / 100;
                                        $d_billing -= $d_fee_amount_additional;
                                        $d_additional_discount += $d_fee_amount_additional;
                                    }
                                }else{
                                    if ($o_details->fee_amount_number_type == 'number') {
                                        $d_billing += $o_details->fee_amount;
                                    }else{
                                        $d_fee_amount_additional = ($d_tf_fee_amount * $o_details->fee_amount) / 100;
                                        $d_billing += $d_fee_amount_additional;
                                    }
                                }
                            }
                        }

                        // $a_filter_fee['fee_amount_type'] = 'additional';
                        // $mba_billing_data = $this->Im->get_fee_data($a_filter_fee);
                        // if ($mba_billing_data) {
                        //     $d_billing = $mba_billing_data[0]->fee_amount;
                        // }

                        foreach ($mba_student_invoice as $o_invoice) {
                            $d_fined_included = false;
                            if ($o_invoice->invoice_status == 'PAID') {
                                $s_status = 'paid';
                            }
                            else {
                                $s_status = 'unpaid';
                            }

                            if (!in_array($o_invoice->invoice_status, ['cancelled'])) {
                                $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                                $mba_invoice_full_payment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);

                                if ($o_invoice->invoice_status == 'paid') {
                                    if (($mba_invoice_full_payment) AND ($mba_invoice_full_payment->sub_invoice_details_amount_paid > 0)) {
                                        // print('paid');exit;
                                        if (!$d_fined_included) {
                                            if ($mba_invoice_full_payment->sub_invoice_details_amount_fined > 0) {
                                                $d_billing += $mba_invoice_full_payment->sub_invoice_details_amount_fined;
                                            }
                                        }
    
                                        if ($mba_invoice_full_payment->sub_invoice_details_status != 'paid') {
                                            $d_semester_amount_unpaid += $mba_invoice_full_payment->sub_invoice_details_amount_total;
                                        }
                                    }else if ($mba_invoice_installment) {
                                        foreach ($mba_invoice_installment as $installment) {
                                            if ($installment->sub_invoice_details_amount_fined > 0) {
                                                $d_billing += $installment->sub_invoice_details_amount_fined;
                                            }
    
                                            if ($installment->sub_invoice_details_status != 'paid') {
                                                $d_semester_amount_unpaid += $installment->sub_invoice_details_amount_total;
                                            }
                                            else {
                                                $d_semester_amount_paid += $installment->sub_invoice_details_amount_paid;
                                            }
                                        }
                                    }

                                    if (count($mba_student_invoice) == 1) {
                                        if ($mba_invoice_full_payment) {
                                            $d_semester_amount_paid += $mba_invoice_full_payment->sub_invoice_details_amount_paid;
                                        }
                                        
                                        if ($mba_invoice_installment) {
                                            foreach ($mba_invoice_installment as $installment) {
                                                $d_semester_amount_paid += $installment->sub_invoice_details_amount_paid;
                                                // if (($s_student_number == '11201608008') AND ($i_semester_number == '11')) {
                                                //     // print($d_semester_amount_paid.'<br>');
                                                //     print('<pre>');var_dump($d_semester_amount_paid);
                                                //     print('<br>');
                                                // }
                                            }
                                        }
                                    }
                                }
                                else {
                                    if ($mba_invoice_installment) {
                                        // $i_current_month = $a_month["$s_semester_type_id"][$current_installment_index];
                                        // $i_installment_current = $current_installment_index + 1;
                                        // if (count($mba_invoice_installment) < 6) {
                                        //     # code...
                                        // }

                                        // // if (count($mba_invoice_installment) == 6) {
                                        //     $avail_counter_installment = count($mba_invoice_installment) - $i_installment_current;
                                        //     $installment_billing = $d_billing / count($mba_invoice_installment);
                                        //     $d_billing = $i_installment_current * $installment_billing;
                                        // // }
                                        // if (($s_academic_year_id == $o_invoice->invoice_academic_year) AND ($s_semester_type_id == $o_invoice->invoice_semester_type)) {
                                            // 
                                        // }
                                        // $d_billing = 0;

                                        foreach ($mba_invoice_installment as $installment) {
                                            // if (($s_academic_year_id == $o_invoice->invoice_academic_year) AND ($s_semester_type_id == $o_invoice->invoice_semester_type)) {
                                            //     if (intval(date('m', strtotime($installment->sub_invoice_details_real_datetime_deadline))) > intval($i_current_month)) {
                                            //         $d_billing -= $installment->sub_invoice_details_amount_total;
                                            //     }
                                            //     // else {
                                            //     //     # code...
                                            //     // }
                                            // }

                                            if ($installment->sub_invoice_details_amount_fined > 0) {
                                                $d_billing += $installment->sub_invoice_details_amount_fined;
                                                $d_fined_included = true;
                                            }
    
                                            if ($installment->sub_invoice_details_status != 'paid') {
                                                $d_semester_amount_unpaid += $installment->sub_invoice_details_amount_total;
                                            }
                                            else {
                                                $d_semester_amount_paid += $installment->sub_invoice_details_amount_paid;
                                            }
                                        }
                                    }
                                    else if ($mba_invoice_full_payment) {
                                        if ($d_fined_included) {
                                            if ($mba_invoice_full_payment->sub_invoice_details_amount_fined > 0) {
                                                $d_billing += $mba_invoice_full_payment->sub_invoice_details_amount_fined;
                                            }
                                        }
    
                                        if ($mba_invoice_full_payment->sub_invoice_details_status != 'paid') {
                                            $d_semester_amount_unpaid += $mba_invoice_full_payment->sub_invoice_details_amount_total;
                                        }
                                        else {
                                            $d_semester_amount_paid += $mba_invoice_full_payment->sub_invoice_details_amount_paid;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // if (($s_student_number == '11202008012') AND ($i_semester_number == '5')) {
                    //     print("<pre>");var_dump($d_billing);exit;
                    // }

                    if ($d_semester_amount_paid == 0) {
                        $d_semester_amount_unpaid = $d_billing;
                    }
                    else if ($d_semester_amount_paid > 0) {
                        $d_semester_amount_unpaid = $d_billing - $d_semester_amount_paid;
                    }

                    // if (in_array($i_semester_number, [1,2])) {
                    //     $d_semester_amount_unpaid = 0;
                    // }
                    // else 
                    if ($s_status == 'paid') {
                        $d_semester_amount_unpaid = 0;
                    }

                    if (($mbo_student_data->student_id == '14d90678-dfcd-4a2b-b318-29858ea17d7f') AND (in_array($i_semester_number, [3,4]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '9f8ac854-76c1-4cc0-a625-14114c98463e') AND (in_array($i_semester_number, [7]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '6f5f4c0f-c233-4938-a5a8-1b2a429bf5ab') AND (in_array($i_semester_number, [9]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == 'c18f818e-4004-41c5-bec5-9a548e0c5ddd') AND (in_array($i_semester_number, [3,4,10]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '2d3451bb-17c3-4ad0-bf16-64693daeb78f') AND (in_array($i_semester_number, [11,12]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '874f314b-f2dd-4357-9891-9b4e74d6cb5b') AND (in_array($i_semester_number, [10]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '874f314b-f2dd-4357-9891-9b4e74d6cb5b') AND (in_array($i_semester_number, [4,7]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '41a1bcfb-5764-4db4-9858-234e13c9f5cc') AND (in_array($i_semester_number, [4]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->student_id == '5c438173-124e-46a7-8126-50a3f6eb439a') AND (in_array($i_semester_number, [4]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->personal_data_id == '2d1b30dd-5876-4bc2-8153-75f37bc034ec') AND (in_array($i_semester_number, [5,9]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->personal_data_id == '53cc88bc-6484-47f4-9b84-719863e711b1') AND (in_array($i_semester_number, [6]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->personal_data_id == 'aea69e29-a43a-4e66-ae3a-2411bc2d290a') AND (in_array($i_semester_number, [7,10]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->personal_data_id == '4646c496-9aea-4368-84fb-d75e127e4581') AND (in_array($i_semester_number, [7]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->personal_data_id == 'd276b466-0355-47b2-a4f1-2a644d01aaed') AND (in_array($i_semester_number, [4]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->personal_data_id == 'a2fe63c6-c0c9-4c64-a2db-2608b18a8463') AND (in_array($i_semester_number, [9]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->personal_data_id == '9c54e3fa-5a90-488f-8b48-bf9d77853aeb') AND (in_array($i_semester_number, [9]))) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->personal_data_id == 'efb06d43-957e-4237-ae44-9d0c8014237d') AND ($i_semester_number == 8)) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->personal_data_id == 'acc0ccd0-53e5-4df4-af2f-a0c7d4092bc8') AND ($i_semester_number == 8)) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->personal_data_id == '8c8c5ef9-ac6e-48cd-b111-837c928c78f1') AND ($i_semester_number == 8)) {
                        $d_semester_amount_unpaid = 0;
                    }
                    else if (($mbo_student_data->personal_data_id == '41a1bcfb-5764-4db4-9858-234e13c9f5cc') AND ($i_semester_number == 4)) {
                        $d_semester_amount_unpaid = 0;
                    }

                    // if ($a_personal_data_invoice_semester_paid[$i_semester_number][$mbo_student_data->personal_data_id] !== null) {
                    //     $d_semester_amount_unpaid = 0;
                    // }
                    // print('<pre>');var_dump($a_personal_data_invoice_semester_paid[$i_semester_number][$mbo_student_data->personal_data_id]);exit;
                    // if (($i_semester_number == '9') AND ($mbo_student_data->personal_data_id == '0abe5e86-26fa-47e0-8112-c974682f388a')) {
                    //     print('<pre>');var_dump($a_personal_data_invoice_semester_paid[$i_semester_number][$mbo_student_data->personal_data_id]);exit;
                    // }
                    if (($a_personal_data_invoice_semester_paid[$i_semester_number] !== null) AND (in_array($mbo_student_data->personal_data_id, $a_personal_data_invoice_semester_paid[$i_semester_number]))) {
                        $d_semester_amount_unpaid = 0;
                    }

                    if ($d_semester_amount_unpaid < 0) {
                        // print($d_semester_amount_paid);exit;
                        $over = $d_semester_amount_paid - $d_billing;
                        if ($d_additional_discount > 0) {
                            $over -= $d_additional_discount;
                        }
                        $d_semester_amount_unpaid = 0;
                        if ($over > 0) {
                            // array_push($a_notes, '- over paid Rp.'.number_format($over , 0, ".", "."));
                        }
                    }
                    // print('<pre>');var_dump($d_semester_amount_unpaid);exit;

                    // if (($mbo_student_data->personal_data_id == 'a2fe63c6-c0c9-4c64-a2db-2608b18a8463') AND ($i_semester_number == 10)) {
                    //     print('<pre>');
                    //     var_dump($d_billing);
                    //     print('<br>');
                    //     var_dump($d_semester_amount_unpaid);
                    //     exit;
                    // }
                    // $d_amount_semester = ($this_semester_selected) ? $d_semester_amount_unpaid : 0;
                    $d_total_amount_unpaid += $d_semester_amount_unpaid;
                    $o_sheet->setCellValue($c_semester.$i_row, $d_semester_amount_unpaid);
                    if (count($a_notes) > 0) {
                        $o_sheet->getComment($c_semester.$i_row)->setAuthor('Database');
                        $commentRichText = $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun('Notes:');
                        $commentRichText->getFont()->setBold(true);
                        $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun("\r\n");
                        $o_sheet->getComment($c_semester.$i_row)->getText()->createTextRun(implode("\r\n", $a_notes));
                        $o_sheet->getComment($c_semester.$i_row)->setWidth("300px")->setHeight("120px");
                    }
                    
                    if ($this_semester_selected) {
                        $c_semester = 'U';
                    }else {
                        $c_semester++;
                    }
                }

                if (($mba_student_score_for_ofse) AND ($s_case == 'ofse')) {
                    $o_sheet->getStyle('A'.$i_row.':'.$c_semester.$i_row)->applyFromArray($styleColor);
                }

                $c_last_semester = chr(ord($c_semester) - 1);
                if ($c_current_semester > $c_col) {
                    $c_before_current_semester = $c_current_semester;
                    $c_before_current_semester = chr(ord($c_before_current_semester) - 1);
                    // $c_before_current_semester--;
                }
                else {
                    $c_before_current_semester = $c_col;
                }

                $d_invoice_nfu_billing = '?';
                if ($s_case == 'nfu') {
                    $mba_invoice_nfu = $this->Im->get_invoice_list_detail([
                        'di.personal_data_id' => $mbo_student_data->personal_data_id,
                        'fee.payment_type_code' => 12,
                        'fee.fee_amount_type' => 'main',
                        'di.invoice_status != ' => 'cancelled'
                    ]);

                    if ($mba_invoice_nfu) {
                        if ($mba_invoice_nfu[0]->invoice_status != 'paid') {
                            $d_invoice_nfu_billing = $mba_invoice_nfu[0]->invoice_details_amount;
                        }
                        else {
                            $d_invoice_nfu_billing = 0;
                        }
                    }
                }
                // print($c_before_current_semester);exit;
                
                // $c_before_current_semester = ($c_current_semester > $c_col) ? $c_current_semester-- : $c_col;
                $o_sheet->setCellValue($c_semester++.$i_row, '=SUM('.$c_col.$i_row.':'.$c_last_semester.$i_row.')');
                $o_sheet->setCellValue($c_semester++.$i_row, '=SUM('.$c_col.$i_row.':'.$c_before_current_semester.$i_row.')');
                $o_sheet->setCellValue($c_semester++.$i_row, '='.$c_current_semester.$i_row);
                if ($s_case == 'nfu') {
                    $o_sheet->setCellValue($c_semester++.$i_row, $d_invoice_nfu_billing);
                }
                $o_sheet->setCellValue($c_semester++.$i_row, (($d_total_amount_unpaid > 0) ? 'NOT ELIGIBLE' : 'ELIGIBLE'));
                if (($d_total_amount_unpaid > 0) AND ($s_case != 'graduation')) {
                    // $o_sheet->getStyle('A'.$i_row.':'.$c_semester.$i_row)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_YELLOW);
                    $o_sheet->getStyle('A'.$i_row.':'.$c_semester.$i_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
                }
                
                $sheet_name = $mbo_student_data->personal_data_name;
                if (strlen($sheet_name) > 30) {
                    $a_names = explode(' ', $sheet_name);
                    $sheet_name = $a_names[0].' '.$a_names[1];
                }

                $o_spreadsheet->createSheet();
                $o_sheet_history = $o_spreadsheet->getSheet($i_sheet)->setTitle($sheet_name);
                $i_sheet++;

                $a_historycall_data = $this->_get_historycall($mbo_student_data);
                if (count($a_historycall_data) > 0) {
                    $a_historycall_data = array_values($a_historycall_data);

                    // $s_student_name = str_replace(' ', '_', strtolower($mbo_student_data->personal_data_name));
                    $i_row_sheet = 1;
                    $o_sheet_history->setCellValue('A'.$i_row_sheet, 'Payment Type');
                    $o_sheet_history->setCellValue('B'.$i_row_sheet, 'Billed Amount');
                    $o_sheet_history->setCellValue('C'.$i_row_sheet, 'Description');
                    $o_sheet_history->setCellValue('D'.$i_row_sheet, 'Paid Amount');
                    $o_sheet_history->setCellValue('E'.$i_row_sheet, 'Datetime Payment');
                    $o_sheet_history->setCellValue('F'.$i_row_sheet, 'Billing Status');
                    $o_sheet_history->setCellValue('G'.$i_row_sheet, 'Invoice Status');
                    $o_sheet_history->setCellValue('H'.$i_row_sheet, 'Invoice Note');
                    $o_sheet_history->setCellValue('I'.$i_row_sheet, 'Billing Note');
                    $i_row_sheet++;
    
                    foreach ($a_historycall_data as $data) {
                        $o_sheet_history->setCellValue('A'.$i_row_sheet, $data['payment_type']);
                        $o_sheet_history->setCellValue('B'.$i_row_sheet, $data['billed_amount']);
                        $o_sheet_history->setCellValue('C'.$i_row_sheet, $data['description']);
                        $o_sheet_history->setCellValue('D'.$i_row_sheet, $data['paid_amount']);
                        $o_sheet_history->setCellValue('E'.$i_row_sheet, $data['datetime_payment']);
                        $o_sheet_history->setCellValue('F'.$i_row_sheet, $data['status']);
                        $o_sheet_history->setCellValue('G'.$i_row_sheet, $data['invoice_status']);
                        $o_sheet_history->setCellValue('H'.$i_row_sheet, $data['note_invoice']);
                        $o_sheet_history->setCellValue('I'.$i_row_sheet, $data['note_va']);

                        $o_sheet_history->getStyle('B'.$i_row_sheet)->getNumberFormat()->setFormatCode('#,##');
                        $o_sheet_history->getStyle('D'.$i_row_sheet)->getNumberFormat()->setFormatCode('#,##');
                        $i_row_sheet++;
                    }
                }
                // print('kosong');exit;
            }
            else {
                // kasih tanda warna merah + note
                print('row number '.$i_row.' not found in portal!: "'.$s_student_number.'; '.$s_student_name);exit;
            }
            $i_row++;

            if (($s_case == 'german_nfu') AND ($i_row == 23)) {
                $i_row = 28;
            }
        }

        $o_sheet->insertNewColumnBefore('G', 1);
        $o_sheet->insertNewColumnBefore('G', 1);
        $o_sheet->mergeCells('G5:G6');
        $o_sheet->mergeCells('H5:H6');
        $o_sheet->setCellValue('G5', 'Batch');
        $o_sheet->setCellValue('H5', 'Year In');

        if ($s_case == 'graduation') {
            $o_sheet->insertNewColumnBefore('Z', 1);
        }
        $x_row = 7;
        while (!empty(trim($o_sheet->getCell("E$x_row")->getValue()))) {
            $s_student_number = str_replace('=', '', str_replace('"', '', $o_sheet->getCell("E$x_row")->getValue()));
            $s_student_status = $o_sheet->getCell("F$x_row")->getValue();
            $mbo_student_data = $this->Stm->get_student_filtered([
                'ds.student_number' => $s_student_number,
                'ds.student_status' => $s_student_status
            ]);

            if ($mbo_student_data) {
                $mbo_student_data = $mbo_student_data[0];
                $o_sheet->setCellValue('G'.$x_row, $mbo_student_data->academic_year_id);
                $o_sheet->setCellValue('H'.$x_row, $mbo_student_data->finance_year_id);
            }

            if ($s_case == 'graduation') {
                $mbo_invoice_graduation = $this->Im->student_has_invoice_list($mbo_student_data->personal_data_id, [
                    'df.payment_type_code' => '09'
                ], ['created', 'pending', 'paid']);

                if ($mbo_invoice_graduation) {
                    $o_graduation = $mbo_invoice_graduation[0];

                    if ($o_graduation->invoice_status == 'paid') {
                        $d_amount_unpaid = 0;
                    }
                    else {
                        $d_amount_unpaid = $o_graduation->invoice_details_amount;
                    }

                    $o_sheet->setCellValue('Z'.$x_row, $d_amount_unpaid);
                }
            }
            $x_row++;

            if (($s_case == 'german_nfu') AND ($x_row == 23)) {
                $x_row = 28;
            }
        }

        // s_file_path
        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_filename);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        // if ($b_force_download) {
        $a_path_info = pathinfo($s_file_path.$s_filename);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_filename));
        readfile( $s_file_path.$s_filename );
        exit;
    }

    private function _get_historycall($o_student_data)
    {
        $a_data = [];
        $bni_transaction = $this->Mdb->retrieve_data('bni_transactions', ['customer_name' => $o_student_data->personal_data_name]);
        
        if ((!is_null($o_student_data->student_portal_id)) AND ($o_student_data->student_portal_id != 0)) {
            $mba_portal_invoice_student = $this->Mdb->retrieve_data('invoice', ['student_id' => $o_student_data->student_portal_id]);
            if ($mba_portal_invoice_student) {
                foreach ($mba_portal_invoice_student as $o_invoice) {
                    $mba_invoice_sub = $this->Mdb->retrieve_data('invoice_sub', ['invoice_id' => $o_invoice->id]);
                    // $mba_invoice_sub = $this->Mdb->retrieve_data('invoice_sub', ['invoice_id' => $o_invoice->id]);
                    if ($mba_invoice_sub) {
                        foreach ($mba_invoice_sub as $o_invoice_sub) {
                            $mba_invoice_sub_details = $this->Mdb->retrieve_data('invoice_sub_details', ['invoice_sub_id' => $o_invoice_sub->id]);
                            if ($mba_invoice_sub_details) {
                                foreach ($mba_invoice_sub_details as $o_invoice_sub_details) {
                                    $s_payment_code = substr($o_invoice_sub_details->virtual_account, 4, 2);
                                    $mbo_payment_type = $this->Mdb->retrieve_data('payment_type', ['code' => $s_payment_code])[0];
                                    $o_bni_transaction = $this->Mdb->retrieve_data('bni_transactions', ['id' => $o_invoice_sub_details->bni_transaction_id])[0];
                                    $paid_amount = (is_null($o_invoice_sub_details->amount_paid)) ? ((($o_bni_transaction) ? $o_bni_transaction->payment_amount : '')) : $o_invoice_sub_details->amount_paid;

                                    array_push($a_data, [
                                        'payment_type' => ($mbo_payment_type) ? $mbo_payment_type->name : '',
                                        'billed_amount' => $o_invoice_sub_details->amount,
                                        'description' => $o_invoice_sub_details->description,
                                        'paid_amount' => (($o_bni_transaction) AND ($o_bni_transaction->payment_amount > $paid_amount)) ? $o_bni_transaction->payment_amount : $paid_amount,
                                        'note_invoice' => $o_invoice->notes,
                                        'status' => $o_invoice_sub_details->status,
                                        'invoice_status' => $o_invoice->status,
                                        'note_va' => $o_invoice_sub_details->additional_description.' - '.$o_invoice_sub_details->change_details,
                                        'datetime_payment' => ($o_bni_transaction) ? $o_bni_transaction->datetime_payment : '',
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
        // print('<pre>');var_dump($a_data);exit;

        $mba_staging_invoice_data = $this->General->get_where('dt_invoice', ['personal_data_id' => $o_student_data->personal_data_id]);
        if ($mba_staging_invoice_data) {
            foreach ($mba_staging_invoice_data as $o_invoice) {
                $mba_sub_invoice = $this->General->get_where('dt_sub_invoice', ['invoice_id' => $o_invoice->invoice_id]);
                if ($mba_sub_invoice) {
                    foreach ($mba_sub_invoice as $o_sub_invoice) {
                        $mba_sub_invoice_details = $this->General->get_where('dt_sub_invoice_details', ['sub_invoice_id' => $o_sub_invoice->sub_invoice_id]);
                        if ($mba_sub_invoice_details) {
                            foreach ($mba_sub_invoice_details as $o_invoice_sub_details) {
                                $s_payment_code = substr($o_invoice_sub_details->sub_invoice_details_va_number, 4, 2);
                                $mbo_payment_type = $this->General->get_where('ref_payment_type', ['payment_type_code' => $s_payment_code])[0];
                                array_push($a_data, [
                                    'payment_type' => ($mbo_payment_type) ? $mbo_payment_type->payment_type_name : '',
                                    'billed_amount' => $o_invoice_sub_details->sub_invoice_details_amount,
                                    'description' => $o_invoice_sub_details->sub_invoice_details_description,
                                    'paid_amount' => $o_invoice_sub_details->sub_invoice_details_amount_paid,
                                    'note_invoice' => $o_invoice->invoice_note,
                                    'status' => $o_invoice->invoice_status,
                                    'invoice_status' => $o_invoice->invoice_status,
                                    'note_va' => (!is_null($o_invoice_sub_details->sub_invoice_details_remarks)) ? $o_invoice_sub_details->sub_invoice_details_remarks : '',
                                    'datetime_payment' => $o_invoice_sub_details->sub_invoice_details_datetime_paid_off,
                                ]);
                            }
                        }
                    }
                }
            }
        }
        // print('<pre>');var_dump($mba_staging_invoice_data);exit;

        return $a_data;
    }

    public function download_report_company_survey($b_force_download = false)
    {
        $mba_question_list = $this->Alm->get_dikti_question(['parent_question_id' => NULL], 'company');
        if ($mba_question_list) {
            $mba_answer_list = $this->Alm->get_survey_answer([
                'dq.question_type' => 'company'
            ]);
            
            // print('<pre>');var_dump($mba_answer_list);exit;
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

            $s_file_name = 'Hasil_Survey_Kepuasan_Pengguna_Alumni_('.date('d-M-Y').')';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/alumni/company_survey/report/".date('Y')."/".date('M')."/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $i_row = 1;

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Alumni Services")
                ->setCategory("Hasil Survey Pengguna Alumni");

            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_sheet->setCellValue('A'.$i_row++, 'Hasil Survey Pengguna Alumni');
            $o_sheet->setCellValue('A'.$i_row++, 'data per '.date('d-M-Y H:i'));
            
            $i_row++;
            $i_num = 1;
            $o_sheet->setCellValue('A'.$i_row, 'No');
            $o_sheet->setCellValue('B'.$i_row, 'Alumni');
            $o_sheet->setCellValue('C'.$i_row, 'Prodi');
            $o_sheet->setCellValue('D'.$i_row, 'Batch');
            $o_sheet->setCellValue('E'.$i_row, 'Graduation Year');
            $o_sheet->setCellValue('F'.$i_row, 'Company / Name of Institution');
            $o_sheet->setCellValue('G'.$i_row, 'Name of Evaluator');
            $o_sheet->setCellValue('H'.$i_row, 'Position of Alumni');
            $o_sheet->setCellValue('I'.$i_row, 'Position of Evaluator');
            $o_sheet->setCellValue('J'.$i_row, 'Datetime');
            $c_cols = $c_cols_header = $c_cols_footer = 'K';
            $i_number_col = 10;
            $a_choices = [];

            foreach ($mba_question_list as $o_question) {
                $o_sheet->setCellValue($c_cols_header.$i_row, $o_question->question_number);
                $i_number_col++;
                $c_cols_header++;
                
                $mba_question_choice = $this->Alm->get_dikti_question_choice(['dqc.question_id' => $o_question->question_id]);
                if ($mba_question_choice) {
                    foreach ($mba_question_choice as $o_choice) {
                        if (!in_array($o_choice->question_choice_name, $a_choices)) {
                            array_push($a_choices, $o_choice->question_choice_name);
                        }
                    }
                }
            }
            $i_row++;

            $o_sheet->mergeCells('A1:'.($c_cols_header--).'1');
            $o_sheet->mergeCells('A2:'.($c_cols_header).'2');
            $o_sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
            $o_sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
            $i_range_start = $i_row;

            if ($mba_answer_list) {
                foreach ($mba_answer_list as $o_user) {
                    $mba_alumni_data = $this->Stm->get_student_filtered(['ds.student_id' => $o_user->student_id])[0];
                    $o_sheet->setCellValue('A'.$i_row, $i_num++);
                    $o_sheet->setCellValue('B'.$i_row, $mba_alumni_data->personal_data_name);
                    $o_sheet->setCellValue('C'.$i_row, $mba_alumni_data->study_program_abbreviation);
                    $o_sheet->setCellValue('D'.$i_row, $mba_alumni_data->academic_year_id);
                    $o_sheet->setCellValue('E'.$i_row, $mba_alumni_data->graduated_year_id);
                    $o_sheet->setCellValue('F'.$i_row, $mba_alumni_data->institution_name);
                    $o_sheet->setCellValue('G'.$i_row, $o_user->personal_data_name);
                    $o_sheet->setCellValue('H'.$i_row, $mba_alumni_data->ocupation_name);
                    $o_sheet->setCellValue('I'.$i_row, $o_user->ocupation_name);
                    $o_sheet->setCellValue('J'.$i_row, '="'.$o_user->answer_timestamp.'"');

                    $c_cols_row = $c_cols;
                    foreach ($mba_question_list as $o_question) {
                        $mba_answer_data = $this->Alm->get_question_answer([
                            'dqa.question_id' => $o_question->question_id,
                            'dqa.personal_data_id' => $o_user->personal_data_id
                        ]);

                        if ($mba_answer_data) {
                            $o_sheet->setCellValue($c_cols_row.$i_row, $mba_answer_data[0]->question_choice_name);
                        }
                        
                        $c_cols_row++;
                    }

                    $i_row++;
                }
            }

            $i_row++;
            $i_range_max = $i_row;
            $i_row++;
            
            $o_sheet->setCellValue('H'.$i_row, "Total");
            $o_sheet->mergeCells('H'.$i_row.':I'.$i_row);
            
            foreach ($mba_question_list as $o_question) {
                $i_row_footer = $i_row;
                $o_sheet->setCellValue($c_cols_footer.$i_row_footer, "=COUNTA(".$c_cols_footer.$i_range_start.":".$c_cols_footer.$i_range_max.")");
                $i_row_footer++;
                if (count($a_choices) > 0) {
                    foreach ($a_choices as $s_choice) {
                        $o_sheet->setCellValue('H'.$i_row_footer, $s_choice);
                        $o_sheet->mergeCells('H'.$i_row_footer.':I'.$i_row_footer);
                        $o_sheet->setCellValue($c_cols_footer.$i_row_footer, '=COUNTIF('.$c_cols_footer.$i_range_start.':'.$c_cols_footer.$i_range_max.', "'.$s_choice.'")');
                        $i_row_footer++;
                    }
                }

                $c_cols_footer++;
            }



            $c = 'A';
            for ($i=0; $i < $i_number_col; $i++) { 
                $o_sheet->getColumnDimension($c)->setAutoSize(true);
                $c++;
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            // if ($b_force_download) {
            $a_path_info = pathinfo($s_file_path.$s_filename);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path.$s_filename );
            exit;
            // }
        }else{
            $a_return = ['code' => 1, 'message' => 'No data!'];
        }

        print('<pre>');
        var_dump($a_return);exit;
    }

    public function download_report_new_tracer_dikti()
    {
        $mba_alumni_answer_list = $this->Alm->get_alumni_answer_lists([
            'ds.student_status != ' => 'resign'
        ]);
        if ($mba_alumni_answer_list) {
            $a_sheet_list = [
                'Tabel 1' => [1,2,3,4,5,8,9,10,11,12,13,14,15,16,17],
                'Tabel 2' => [6],
                'Tabel 3' => [7],
                'Tabel 4' => [18],
                'Tabel 5' => [19]
            ];

            $style_vertical_top = array(
                'alignment' => array(
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                )
            );

            $style_border = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '00000000'],
                    )
                )
            );

            $mba_question_list = $this->Alm->get_dikti_question(['parent_question_id' => NULL]);

            $s_template_path = APPPATH.'uploads/templates/alumni/Template_Hasil_Survey_Alumni_IULI.xlsx';
            $s_file_name = 'Hasil_Survey_Alumni_IULI_('.date('d-M-Y').')';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/alumni/tracer_study/report/".date('Y')."/".date('M')."/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $i_sheet_index = 0;
            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Alumni Services")
                ->setCategory("Hasil Survey Alumni Tracer Studi");

            foreach ($a_sheet_list as $key => $a_question_number_list) {
                // print($i_sheet_index.'<br>');
                // $a_return = ['code' => 1, 'message' => $mba_alumni_answer_list];
                $o_sheet = $o_spreadsheet->getSheetByName($key);
                $i_row = 4;
                $i_number = 0;
                $i_total_question = 0;

                $s_text_A1 = $o_sheet->getCell('A1')->getValue();
                $s_text_A1 = substr($s_text_A1, 8);
                $s_question_number = implode(', ', $a_question_number_list);
                $s_e2_title = 'Pertanyaan nomor '.$s_question_number.' '.$s_text_A1;
                $o_sheet->setCellValue('E2', $s_e2_title);
                $c_start_col_question = 'E';

                foreach ($mba_alumni_answer_list as $o_personal_data) {
                    // if ($key == 'Tabel 5') {
                    //     print($o_personal_data->personal_data_name.'<br>');
                    // }
                    $i_max_total_question = 0;
                    $s_prodi_abbr = ($o_personal_data->program_id == $this->a_programs['NI S1']) ? $o_personal_data->study_program_ni_abbreviation : $o_personal_data->study_program_abbreviation;
                    if ($o_personal_data->personal_data_id != '2c088deb-9143-4153-bdd7-7f6661fa8696') { // akun dummy
                        $i_number++;
                        $c_question_number_col = $c_start_col_question;
                        
                        $o_sheet->setCellValue('A'.$i_row, $i_number);
                        $o_sheet->setCellValue('B'.$i_row, $o_personal_data->personal_data_name);
                        $o_sheet->setCellValue('C'.$i_row, $s_prodi_abbr);
                        $o_sheet->setCellValue('D'.$i_row, $o_personal_data->academic_year_id);
                        $o_sheet->getStyle('A'.$i_row)->applyFromArray($style_vertical_top);
                        $o_sheet->getStyle('B'.$i_row)->applyFromArray($style_vertical_top);
                        $o_sheet->getStyle('C'.$i_row)->applyFromArray($style_vertical_top);
                        $o_sheet->getStyle('D'.$i_row)->applyFromArray($style_vertical_top);
                        // print('<pre>');var_dump($i_sheet_index);exit;

                        if ($key == "Tabel 1") {
                            foreach ($mba_question_list as $o_question) {
                                $mba_have_child_question = $this->Alm->get_dikti_question(['parent_question_id' => $o_question->question_id]);

                                if (!$mba_have_child_question) {
                                    $s_text_number = $o_sheet->getCell($c_question_number_col.'3')->getValue();
                                    $s_text_number = trim($s_text_number);
                                    if (in_array($o_question->question_number, $a_question_number_list)) {
                                        $i_max_total_question++;
                                        $mba_question_answer = $this->Alm->get_question_answer([
                                            'dqa.question_id' => $o_question->question_id,
                                            'dqa.personal_data_id' => $o_personal_data->personal_data_id,
                                            'dq.question_number' => $s_text_number
                                        ]);

                                        $s_answer = '';
                                        $s_value = '';
                                        $a_answer = [];
                                        $a_value = [];
                                        if ($mba_question_answer) {
                                            foreach ($mba_question_answer as $o_answer) {
                                                if (is_null($o_answer->answer_content)){
                                                    array_push($a_answer, $o_answer->question_choice_name);
                                                    
                                                }else{
                                                    $s_answer_text = str_replace('_', $o_answer->answer_content, $o_answer->question_choice_name);
                                                    array_push($a_answer, $s_answer_text);

                                                }

                                                if (($o_question->question_id == 'f3') OR ($o_question->question_id == 'f5')) {
                                                    if ($o_answer->question_choice_value == 1) {
                                                        $s_value_content = 1;
                                                    }
                                                    else if ($o_answer->question_choice_value == 2) {
                                                        if ($o_answer->answer_content == 0) {
                                                            $s_value_content = 1;
                                                        }
                                                        else if (intval($o_answer->answer_content) <= 6) {
                                                            $s_value_content = 2;
                                                        }
                                                        else if (intval($o_answer->answer_content) <= 12) {
                                                            $s_value_content = 3;
                                                        }
                                                        else {
                                                            $s_value_content = 4;
                                                        }
                                                    }
                                                    else {
                                                        $s_value_content = 0;
                                                    }

                                                    array_push($a_value, $s_value_content);
                                                }else if (is_null($o_answer->question_choice_value)){
                                                    array_push($a_value, $o_answer->answer_content);
                                                }else{
                                                    array_push($a_value, $o_answer->question_choice_value);
                                                }
                                            }
                                        }

                                        if (count($a_answer) > 0) {
                                            $s_answer = implode('"&char(10)&",', $a_answer);
                                            $s_answer = '="'.$s_answer.'"';
                                        }
                                        
                                        if (count($a_value) > 0) {
                                            $s_value = implode('"&char(10)&",', $a_value);
                                            $s_value = '="'.$s_value.'"';
                                        }

                                        if ($s_answer == '') {
                                            $s_value = '="99"';
                                        }

                                        $c_col_question_answer_value = $c_question_number_col;
                                        $c_col_question_answer_value++;
                                        $o_sheet->setCellValue($c_question_number_col.$i_row, "$s_answer");
                                        $o_sheet->setCellValue($c_col_question_answer_value.$i_row, "$s_value");
                                        $o_sheet->getStyle($c_question_number_col.$i_row)->getAlignment()->setWrapText(true);
                                        $o_sheet->getStyle($c_question_number_col.$i_row)->applyFromArray($style_vertical_top);
                                        $o_sheet->getStyle($c_col_question_answer_value.$i_row)->getAlignment()->setWrapText(true);
                                        $o_sheet->getStyle($c_col_question_answer_value.$i_row)->applyFromArray($style_vertical_top);
                                    }
                                    $c_question_number_col++;
                                    $c_question_number_col++;
                                }
                            }

                            $o_sheet->setCellValue($c_question_number_col.$i_row, $o_personal_data->graduated_year_id);
                        }
                        else{
                            $mbo_question_list = $this->Alm->get_dikti_question(['parent_question_id' => NULL, 'question_number' => $a_question_number_list[0]])[0];
                            // print('<pre>');var_dump($mba_question_child_list);exit;
                            if ($mbo_question_list) {
                                $a_salary = [];
                                $mba_question_child_list = $this->Alm->get_dikti_question(['parent_question_id' => $mbo_question_list->question_id]);
                                // print('<pre>');var_dump($mba_question_child_list);exit;
                                if ($mba_question_child_list) {
                                    foreach ($mba_question_child_list as $o_question) {
                                        $i_max_total_question++;
                                        $s_text_number = $o_sheet->getCell($c_question_number_col.'3')->getValue();
                                        $s_text_number = trim($s_text_number);
                                        // if (in_array($o_question->question_number, $a_question_number_list)) {
                                            $mba_question_answer = $this->Alm->get_question_answer([
                                                'dqa.question_id' => $o_question->question_id,
                                                'dqa.personal_data_id' => $o_personal_data->personal_data_id,
                                                'dq.question_number' => $s_text_number
                                            ]);

                                            $s_answer = '';
                                            $a_answer = [];
                                            $s_value = '';
                                            $a_value = [];
                                            $a_answer_money = [];
                                            if ($mba_question_answer) {
                                                foreach ($mba_question_answer as $o_answer) {
                                                    if (is_null($o_answer->answer_content)){
                                                        array_push($a_answer, $o_answer->question_choice_name);
                                                    }else{
                                                        $s_answer_content = $o_answer->answer_content;
                                                        if (($x = strpos($o_answer->question_choice_name, 'Rp.')) !== false) {
                                                            $s_answer_content = str_replace('.', '', $s_answer_content);
                                                            array_push($a_answer_money, $s_answer_content);
                                                            $s_answer_content = ($s_answer_content != '0') ? number_format($s_answer_content , 0, ".", ".") : $s_answer_content;
                                                        }

                                                        $s_answer_text = str_replace('_', $s_answer_content, $o_answer->question_choice_name);
                                                        array_push($a_answer, $s_answer_text);
                                                    }

                                                    if (is_null($o_answer->question_choice_value)){
                                                        array_push($a_value, $o_answer->answer_content);
                                                    }else{
                                                        array_push($a_value, $o_answer->question_choice_value);
                                                    }
                                                }
                                            }
                                            if (count($a_answer) > 0) {
                                                $s_answer = implode(',"&char(10)&"', $a_answer);
                                                $s_answer = '="'.$s_answer.'"';
                                            }

                                            if (count($a_value) > 0) {
                                                $s_value = implode(',"&char(10)&"', $a_value);
                                                $s_value = '="'.$s_value.'"';
                                            }

                                            if ($s_answer == '') {
                                                $s_value = '="99"';
                                            }

                                            if ($key == 'Tabel 2') {
                                                $s_sum_salary = array_sum($a_answer_money);
                                                array_push($a_salary, $s_sum_salary);
                                            }else{
                                                $c_col_question_answer_value = $c_question_number_col;
                                                $c_col_question_answer_value++;

                                                $o_sheet->setCellValue($c_col_question_answer_value.$i_row, "$s_value");
                                                $o_sheet->getStyle($c_col_question_answer_value.$i_row)->getAlignment()->setWrapText(true);
                                                $o_sheet->getStyle($c_col_question_answer_value.$i_row)->applyFromArray($style_vertical_top);
                                            }

                                            $o_sheet->setCellValue($c_question_number_col.$i_row, "$s_answer");
                                            $o_sheet->getStyle($c_question_number_col.$i_row)->getAlignment()->setWrapText(true);
                                            $o_sheet->getStyle($c_question_number_col.$i_row)->applyFromArray($style_vertical_top);
                                        // }
                                        $c_question_number_col++;
                                        if ($key != 'Tabel 2') {
                                            $c_question_number_col++;
                                        }
                                    }

                                    $o_sheet->setCellValue($c_question_number_col.$i_row, $o_personal_data->graduated_year_id);
                                }

                                if ($key == 'Tabel 2') {
                                    $s_sum_total_salary = array_sum($a_salary);
                                    $s_sum_total_salary = ($s_sum_total_salary != '0') ? number_format($s_sum_total_salary , 0, ".", ".") : $s_sum_total_salary;
                                    $o_sheet->setCellValue('H'.$i_row, "Rp. $s_sum_total_salary");
                                }
                            }
                        }

                        $i_row++;
                        $o_sheet->insertNewRowBefore($i_row, 1);
                    }

                    $i_total_question = ($i_total_question < $i_max_total_question) ? $i_max_total_question : $i_total_question;
                }

                $i_max_row_filled = $i_row;
                $i_row +=1;
                $o_sheet->setCellValue('B'.$i_row, "Jumlah Responden Mengisi");
                $o_sheet->setCellValue('C'.$i_row, '=COUNTA(C4:C'.($i_max_row_filled).')');

                // print($i_total_question);exit;
                $i_max_loop = $i_total_question;
                $i_max_loop = $i_max_loop * 2;
                if ($key != 'Tabel 2') {
                    $c = $c_start_col_question;
                    for ($i = 0; $i < $i_max_loop; $i++) {
                        $o_sheet->setCellValue($c.$i_row, '=COUNTA('.$c.'4:'.$c.($i_max_row_filled).')');
                        $c++;
                        $c++;
                    }
                }else{
                    $c = $c_start_col_question;
                    for ($i = 0; $i < 3 ; $i++) {
                        $o_sheet->setCellValue($c.$i_row, '=COUNTA('.$c.'4:'.$c.($i_max_row_filled).')');
                        $c++;
                    }
                }

                $i_row += 2;
                $mba_prodi_list = $this->Spm->get_study_program(false, false);
                $i_prodi_start = $i_row;
                $o_sheet->setCellValue('B'.$i_row, "Prodi:");
                $o_sheet->setCellValue('C'.$i_row, "Responden:");
                $o_sheet->setCellValue('D'.$i_row, "Total Kelulusan:");
                // $o_sheet->mergeCells('B'.$i_row.':C'.$i_row);
                $i_row++;
                if ($mba_prodi_list) {
                    foreach ($mba_prodi_list as $o_prodi) {
                        $mba_student_graduated = $this->General->get_where('dt_student', [
                            'student_status' => 'graduated',
                            'study_program_id' => $o_prodi->study_program_id
                        ]);

                        $o_sheet->setCellValue('B'.$i_row, $o_prodi->study_program_abbreviation);
                        $o_sheet->setCellValue('C'.$i_row, "=COUNTIF(C4:C".($i_max_row_filled).",B".($i_row).")");
                        $o_sheet->setCellValue('D'.$i_row, (($mba_student_graduated) ? count($mba_student_graduated) : 0));
                        $i_row++;
                    }
                }
                $o_sheet->setCellValue('B'.$i_row, "Total:");
                $o_sheet->setCellValue('C'.$i_row, '=SUM(C'.($i_prodi_start + 1).':C'.($i_row - 1).')');
                $o_sheet->setCellValue('D'.$i_row, '=SUM(D'.($i_prodi_start + 1).':D'.($i_row - 1).')');
                $o_sheet->getStyle('B'.$i_prodi_start.':D'.$i_row)->applyFromArray($style_border);
                $i_row++;

                $i_sheet_index++;
                // if ($i_sheet_index == 5) {
                //     print('ada');exit;
                // }
                // if ($key == 'Tabel 5') {
                //     var_dump('tabel 5 akhir');exit;
                // }
            }
            // print('adas');exit;

            $o_sheet = $o_spreadsheet->getSheetByName('List Pertanyaan');
            $i_row = 2;
            if ($mba_question_list) {
                foreach ($mba_question_list as $o_main_question) {
                    $mba_question_child_list = $this->Alm->get_dikti_question(['parent_question_id' => $o_main_question->question_id]);
                    $mba_answer_data = $this->Alm->get_alumni_answer_lists([
                        'question_id' => $o_main_question->question_id
                    ]);

                    $o_sheet->setCellValue('A'.$i_row, $o_main_question->question_number);
                    $o_sheet->setCellValue('B'.$i_row, $o_main_question->question_name);
                    if ($mba_question_child_list) {
                        $i_row++;
                        foreach ($mba_question_child_list as $o_question_child) {
                            $o_sheet->setCellValue('B'.$i_row, $o_question_child->question_number);
                            $o_sheet->setCellValue('C'.$i_row, $o_question_child->question_name);
                            $i_row++;
                        }
                    }
                    $i_row++;
                }
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path.$s_filename );
            exit;
            // $a_return['code']

        }else{
            $a_return = ['code' => 1, 'message' => 'No data!'];
        }

        print('<pre>');
        var_dump($a_return);exit;
    }

    public function download_report_tracer_dikti()
    {
        $mba_alumni_answer_list = $this->Alm->get_alumni_answer_lists();
        if ($mba_alumni_answer_list) {
            $a_sheet_list = [
                'Tabel 1' => [1,2,3,4,5,8,9,10,11,12,13,14,15,16,17],
                'Tabel 2' => [6],
                'Tabel 3' => [7],
                'Tabel 4' => [18],
                'Tabel 5' => [19]
            ];

            $style_vertical_top = array(
                'alignment' => array(
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                )
            );

            $style_border = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '00000000'],
                    )
                )
            );

            $mba_question_list = $this->Alm->get_dikti_question(['parent_question_id' => NULL]);

            $s_template_path = APPPATH.'uploads/templates/alumni/Template_Hasil_Survey_Alumni_IULI.xlsx';
            $s_file_name = 'Hasil_Survey_Alumni_IULI_('.date('d-M-Y').')';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/alumni/tracer_study/report/".date('Y')."/".date('M')."/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $i_sheet_index = 0;
            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Alumni Services")
                ->setCategory("Hasil Survey Alumni Tracer Studi");

            foreach ($a_sheet_list as $key => $a_question_number_list) {
                // print($i_sheet_index.'<br>');
                // $a_return = ['code' => 1, 'message' => $mba_alumni_answer_list];
                $o_sheet = $o_spreadsheet->getSheetByName($key);
                $i_row = 4;
                $i_number = 0;
                $i_total_question = 0;

                $s_text_A1 = $o_sheet->getCell('A1')->getValue();
                $s_text_A1 = substr($s_text_A1, 8);
                $s_question_number = implode(', ', $a_question_number_list);
                $s_e2_title = 'Pertanyaan nomor '.$s_question_number.' '.$s_text_A1;
                $o_sheet->setCellValue('E2', $s_e2_title);
                $c_start_col_question = 'E';

                foreach ($mba_alumni_answer_list as $o_personal_data) {
                    $i_max_total_question = 0;
                    // $s_prodi_abbr = ($o_personal_data->program_id == $this->a_programs['NI S1']) ? $o_personal_data->study_program_ni_abbreviation : $o_personal_data->study_program_abbreviation;
                    $s_prodi_abbr = $o_personal_data->study_program_abbreviation;
                    if ($o_personal_data->personal_data_id != '2c088deb-9143-4153-bdd7-7f6661fa8696') { // akun dummy
                        $i_number++;
                        $c_question_number_col = $c_start_col_question;
                        
                        $o_sheet->setCellValue('A'.$i_row, $i_number);
                        $o_sheet->setCellValue('B'.$i_row, $o_personal_data->personal_data_name);
                        $o_sheet->setCellValue('C'.$i_row, $s_prodi_abbr);
                        $o_sheet->setCellValue('D'.$i_row, $o_personal_data->academic_year_id);
                        $o_sheet->getStyle('A'.$i_row)->applyFromArray($style_vertical_top);
                        $o_sheet->getStyle('B'.$i_row)->applyFromArray($style_vertical_top);
                        $o_sheet->getStyle('C'.$i_row)->applyFromArray($style_vertical_top);
                        $o_sheet->getStyle('D'.$i_row)->applyFromArray($style_vertical_top);

                        if ($i_sheet_index == 0) {
                            foreach ($mba_question_list as $o_question) {
                                $mba_have_child_question = $this->Alm->get_dikti_question(['parent_question_id' => $o_question->question_id]);

                                if (!$mba_have_child_question) {
                                    $s_text_number = $o_sheet->getCell($c_question_number_col.'3')->getValue();
                                    $s_text_number = trim($s_text_number);
                                    if (in_array($o_question->question_number, $a_question_number_list)) {
                                        $i_max_total_question++;
                                        $mba_question_answer = $this->Alm->get_question_answer([
                                            'dqa.question_id' => $o_question->question_id,
                                            'dqa.personal_data_id' => $o_personal_data->personal_data_id,
                                            'dq.question_number' => $s_text_number
                                        ]);

                                        $s_answer = '';
                                        $s_value = '';
                                        $a_answer = [];
                                        $a_value = [];
                                        if ($mba_question_answer) {
                                            foreach ($mba_question_answer as $o_answer) {
                                                if (is_null($o_answer->answer_content)){
                                                    array_push($a_answer, $o_answer->question_choice_name);
                                                    
                                                }else{
                                                    $s_answer_text = str_replace('_', $o_answer->answer_content, $o_answer->question_choice_name);
                                                    array_push($a_answer, $s_answer_text);

                                                }

                                                if (($o_question->question_id == 'f3') OR ($o_question->question_id == 'f5')) {
                                                    if ($o_answer->question_choice_value == 1) {
                                                        $s_value_content = 1;
                                                    }
                                                    else if ($o_answer->question_choice_value == 2) {
                                                        if ($o_answer->answer_content == 0) {
                                                            $s_value_content = 1;
                                                        }
                                                        else if (intval($o_answer->answer_content) <= 6) {
                                                            $s_value_content = 2;
                                                        }
                                                        else if (intval($o_answer->answer_content) <= 12) {
                                                            $s_value_content = 3;
                                                        }
                                                        else {
                                                            $s_value_content = 4;
                                                        }
                                                    }
                                                    else {
                                                        $s_value_content = 0;
                                                    }

                                                    array_push($a_value, $s_value_content);
                                                }
                                                else if (is_null($o_answer->question_choice_value)){
                                                    array_push($a_value, $o_answer->answer_content);
                                                }
                                                else{
                                                    array_push($a_value, $o_answer->question_choice_value);
                                                }
                                            }
                                        }

                                        if (count($a_answer) > 0) {
                                            $s_answer = implode('"&char(10)&",', $a_answer);
                                            $s_answer = '="'.$s_answer.'"';
                                        }
                                        
                                        if (count($a_value) > 0) {
                                            $s_value = implode('"&char(10)&",', $a_value);
                                            $s_value = '="'.$s_value.'"';
                                        }

                                        if ($s_answer == '') {
                                            $s_value = '="99"';
                                        }

                                        $c_col_question_answer_value = $c_question_number_col;
                                        $c_col_question_answer_value++;
                                        $o_sheet->setCellValue($c_question_number_col.$i_row, "$s_answer");
                                        $o_sheet->setCellValue($c_col_question_answer_value.$i_row, "$s_value");
                                        $o_sheet->getStyle($c_question_number_col.$i_row)->getAlignment()->setWrapText(true);
                                        $o_sheet->getStyle($c_question_number_col.$i_row)->applyFromArray($style_vertical_top);
                                        $o_sheet->getStyle($c_col_question_answer_value.$i_row)->getAlignment()->setWrapText(true);
                                        $o_sheet->getStyle($c_col_question_answer_value.$i_row)->applyFromArray($style_vertical_top);
                                    }
                                    $c_question_number_col++;
                                    $c_question_number_col++;
                                }
                            }

                            $o_sheet->setCellValue($c_question_number_col.$i_row, $o_personal_data->graduated_year_id);
                        }else{
                            $mbo_question_list = $this->Alm->get_dikti_question(['parent_question_id' => NULL, 'question_number' => $a_question_number_list[0]])[0];
                            
                            if ($mbo_question_list) {
                                $a_salary = [];
                                $mba_question_child_list = $this->Alm->get_dikti_question(['parent_question_id' => $mbo_question_list->question_id]);
                                if ($mba_question_child_list) {
                                    foreach ($mba_question_child_list as $o_question) {
                                        $i_max_total_question++;
                                        $s_text_number = $o_sheet->getCell($c_question_number_col.'3')->getValue();
                                        $s_text_number = trim($s_text_number);
                                        // if (in_array($o_question->question_number, $a_question_number_list)) {
                                            $mba_question_answer = $this->Alm->get_question_answer([
                                                'dqa.question_id' => $o_question->question_id,
                                                'dqa.personal_data_id' => $o_personal_data->personal_data_id,
                                                'dq.question_number' => $s_text_number
                                            ]);

                                            $s_answer = '';
                                            $a_answer = [];
                                            $s_value = '';
                                            $a_value = [];
                                            $a_answer_money = [];
                                            if ($mba_question_answer) {
                                                foreach ($mba_question_answer as $o_answer) {
                                                    if (is_null($o_answer->answer_content)){
                                                        array_push($a_answer, $o_answer->question_choice_name);
                                                    }else{
                                                        $s_answer_content = $o_answer->answer_content;
                                                        if (($x = strpos($o_answer->question_choice_name, 'Rp.')) !== false) {
                                                            $s_answer_content = str_replace('.', '', $s_answer_content);
                                                            array_push($a_answer_money, $s_answer_content);
                                                            $s_answer_content = ($s_answer_content != '0') ? number_format($s_answer_content , 0, ".", ".") : $s_answer_content;
                                                        }

                                                        $s_answer_text = str_replace('_', $s_answer_content, $o_answer->question_choice_name);
                                                        array_push($a_answer, $s_answer_text);
                                                    }

                                                    if (is_null($o_answer->question_choice_value)){
                                                        array_push($a_value, $o_answer->answer_content);
                                                    }else{
                                                        array_push($a_value, $o_answer->question_choice_value);
                                                    }
                                                }
                                            }
                                            if (count($a_answer) > 0) {
                                                $s_answer = implode(',"&char(10)&"', $a_answer);
                                                $s_answer = '="'.$s_answer.'"';
                                            }

                                            if (count($a_value) > 0) {
                                                $s_value = implode(',"&char(10)&"', $a_value);
                                                $s_value = '="'.$s_value.'"';
                                            }

                                            if ($s_answer == '') {
                                                $s_value = '="99"';
                                            }

                                            if ($key == 'Tabel 2') {
                                                $s_sum_salary = array_sum($a_answer_money);
                                                array_push($a_salary, $s_sum_salary);
                                            }else{
                                                $c_col_question_answer_value = $c_question_number_col;
                                                $c_col_question_answer_value++;

                                                $o_sheet->setCellValue($c_col_question_answer_value.$i_row, "$s_value");
                                                $o_sheet->getStyle($c_col_question_answer_value.$i_row)->getAlignment()->setWrapText(true);
                                                $o_sheet->getStyle($c_col_question_answer_value.$i_row)->applyFromArray($style_vertical_top);
                                            }

                                            $o_sheet->setCellValue($c_question_number_col.$i_row, "$s_answer");
                                            $o_sheet->getStyle($c_question_number_col.$i_row)->getAlignment()->setWrapText(true);
                                            $o_sheet->getStyle($c_question_number_col.$i_row)->applyFromArray($style_vertical_top);
                                        // }
                                        $c_question_number_col++;
                                        if ($key != 'Tabel 2') {
                                            $c_question_number_col++;
                                        }
                                    }

                                    $o_sheet->setCellValue($c_question_number_col.$i_row, $o_personal_data->graduated_year_id);
                                }

                                if ($key == 'Tabel 2') {
                                    $s_sum_total_salary = array_sum($a_salary);
                                    $s_sum_total_salary = ($s_sum_total_salary != '0') ? number_format($s_sum_total_salary , 0, ".", ".") : $s_sum_total_salary;
                                    $o_sheet->setCellValue('H'.$i_row, "Rp. $s_sum_total_salary");
                                }
                            }
                        }
                        if ($key == 'Tabel 5') {
                            print($o_personal_data->personal_data_name.'-'.$i_number.'<br>');
                        }

                        $i_row++;
                        $o_sheet->insertNewRowBefore($i_row, 1);
                    }

                    $i_total_question = ($i_total_question < $i_max_total_question) ? $i_max_total_question : $i_total_question;
                }

                $i_max_row_filled = $i_row;
                $i_row +=1;
                $o_sheet->setCellValue('B'.$i_row, "Jumlah Responden Mengisi");
                // $o_sheet->setCellValue('C'.$i_row, '=COUNTA(C4:C'.($i_max_row_filled).')');

                // print($i_total_question);exit;
                // $i_max_loop = $i_total_question;
                // $i_max_loop = $i_max_loop * 2;
                // if ($key != 'Tabel 2') {
                //     $c = $c_start_col_question;
                //     for ($i = 0; $i < $i_max_loop; $i++) {
                //         $o_sheet->setCellValue($c.$i_row, '=COUNTA('.$c.'4:'.$c.($i_max_row_filled).')');
                //         $c++;
                //         $c++;
                //     }
                // }else{
                //     $c = $c_start_col_question;
                //     for ($i = 0; $i < 3 ; $i++) {
                //         $o_sheet->setCellValue($c.$i_row, '=COUNTA('.$c.'4:'.$c.($i_max_row_filled).')');
                //         $c++;
                //     }
                // }

                // $i_row += 2;
                // $mba_prodi_list = $this->Spm->get_study_program(false, false);
                // $i_prodi_start = $i_row;
                // $o_sheet->setCellValue('B'.$i_row, "Prodi:");
                // $o_sheet->setCellValue('C'.$i_row, "Responden:");
                // $o_sheet->setCellValue('D'.$i_row, "Total Kelulusan:");
                // // $o_sheet->mergeCells('B'.$i_row.':C'.$i_row);
                // $i_row++;
                // if ($mba_prodi_list) {
                //     foreach ($mba_prodi_list as $o_prodi) {
                //         $mba_student_graduated = $this->General->get_where('dt_student', [
                //             'student_status' => 'graduated',
                //             'study_program_id' => $o_prodi->study_program_id
                //         ]);

                //         $o_sheet->setCellValue('B'.$i_row, $o_prodi->study_program_abbreviation);
                //         $o_sheet->setCellValue('C'.$i_row, "=COUNTIF(C4:C".($i_max_row_filled).",B".($i_row).")");
                //         $o_sheet->setCellValue('D'.$i_row, (($mba_student_graduated) ? count($mba_student_graduated) : 0));
                //         $i_row++;
                //     }
                // }
                // $o_sheet->setCellValue('B'.$i_row, "Total:");
                // $o_sheet->setCellValue('C'.$i_row, '=SUM(C'.($i_prodi_start + 1).':C'.($i_row - 1).')');
                // $o_sheet->setCellValue('D'.$i_row, '=SUM(D'.($i_prodi_start + 1).':D'.($i_row - 1).')');
                // $o_sheet->getStyle('B'.$i_prodi_start.':D'.$i_row)->applyFromArray($style_border);
                // $i_row++;

                $i_sheet_index++;
                if ($i_sheet_index == 5) {
                    // print('ada');exit;
                }
                if ($key == 'Tabel 5') {
                    var_dump('tabel 5 akhir');exit;
                }
            }
            // print('adas');exit;

            // $o_sheet = $o_spreadsheet->getSheetByName('List Pertanyaan');
            // $i_row = 2;
            // if ($mba_question_list) {
            //     foreach ($mba_question_list as $o_main_question) {
            //         $mba_question_child_list = $this->Alm->get_dikti_question(['parent_question_id' => $o_main_question->question_id]);
            //         $mba_answer_data = $this->Alm->get_alumni_answer_lists([
            //             'question_id' => $o_main_question->question_id
            //         ]);

            //         // if ($mba_answer_data) {
            //         //     foreach ($mba_answer_data as $o_answer) {
            //         //         switch ($o_answer) {
            //         //             case 'value':
            //         //                 # code...
            //         //                 break;
                                
            //         //             default:
            //         //                 # code...
            //         //                 break;
            //         //         }
            //         //     }
            //         // }

            //         $o_sheet->setCellValue('A'.$i_row, $o_main_question->question_number);
            //         $o_sheet->setCellValue('B'.$i_row, $o_main_question->question_name);
            //         if ($mba_question_child_list) {
            //             $i_row++;
            //             foreach ($mba_question_child_list as $o_question_child) {
            //                 $o_sheet->setCellValue('B'.$i_row, $o_question_child->question_number);
            //                 $o_sheet->setCellValue('C'.$i_row, $o_question_child->question_name);
            //                 $i_row++;
            //             }
            //         }
            //         $i_row++;
            //     }
            // }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path.$s_filename );
            exit;
            // $a_return['code']

        }else{
            $a_return = ['code' => 1, 'message' => 'No data!'];
        }

        print('<pre>');
        var_dump($a_return);exit;
    }

    public function generate_invoice_student_report($s_finance_year_id, $s_student_id = false, $s_payment_type_id = 'all')
    {
        if ($s_student_id) {
            $a_param_data = [
                'ds.student_id' => $s_student_id,
                'ds.finance_year_id' => $s_finance_year_id
            ];
        }else{
            $a_param_data = [
                'ds.finance_year_id' => $s_finance_year_id
            ];
        }

        $mbo_mdb_payment_type_data = false;
        if ($s_payment_type_id != 'all') {
            $mbo_mdb_payment_type_data = $this->Mdb->retrieve_data('payment_type', ['code' => $s_payment_type_id])[0];
        }

        $a_student_status = ['active', 'inactive', 'onleave'];
        $mba_student_data = $this->Stm->get_student_filtered($a_param_data, $a_student_status);

        if ($mba_student_data) {
            $a_all_data = [];
            foreach($mba_student_data as $o_student) {
                $s_prodi_abbr = ($o_student->student_program == $this->a_programs['NI S1']) ? $o_student->study_program_ni_abbreviation : $o_student->study_program_abbreviation;
                $a_bni_id = [];
                $a_data = [];
                $a_fee = [];

                if ($mbo_mdb_payment_type_data) {
                    $bni_transaction = $this->Mdb->retrieve_data('bni_transactions', [
                        'customer_name' => $o_student->personal_data_name,
                        'payment_type_id' => $mbo_mdb_payment_type_data->payment_type_id
                    ]);

                    if (!is_null($o_student->portal_id)) {
                        $mba_portal_invoice_student = $this->Mdb->retrieve_data('invoice', [
                            'student_id' => $o_student->portal_id,
                            'payment_type_id' => $mbo_mdb_payment_type_data->payment_type_id
                        ]);
                    }else{
                        $mba_portal_invoice_student = false;
                    }
                }else{
                    $bni_transaction = $this->Mdb->retrieve_data('bni_transactions', ['customer_name' => $o_student->personal_data_name]);

                    if (!is_null($o_student->portal_id)) {
                        $mba_portal_invoice_student = $this->Mdb->retrieve_data('invoice', ['student_id' => $o_student->portal_id]);
                    }else{
                        $mba_portal_invoice_student = false;
                    }
                }

                if ($s_payment_type_id != 'all') {
                    $mba_staging_invoice_data = $this->Im->get_invoice_list($s_payment_type_id, $o_student->personal_data_id);
                }else{
                    $mba_staging_invoice_data = $this->Im->get_invoice_list(false, $o_student->personal_data_id);
                }
                
                if ($bni_transaction) {
                    foreach ($bni_transaction as $o_mdb_bni) {
                        if (!in_array($o_mdb_bni->id, $a_bni_id)) {
                            array_push($a_bni_id, $o_mdb_bni->id);
                        }
                    }
                }

                if (count($a_bni_id) > 0) {
                    foreach ($a_bni_id as $s_bni_id) {
                        $has_invoice = false;
                        $o_bni_transaction = $this->Mdb->retrieve_data('bni_transactions', ['id' => $s_bni_id])[0];
                        $mbo_payment_type = $this->Mdb->retrieve_data('payment_type', ['id' => $o_bni_transaction->payment_type_id])[0];

                        $mbo_invoice_sub_details = $this->Mdb->retrieve_data('invoice_sub_details', ['virtual_account' => $o_bni_transaction->virtual_account])[0];
    
                        if ($mbo_invoice_sub_details) {
                            $mbo_invoice_sub = $this->Mdb->retrieve_data('invoice_sub', ['id' => $mbo_invoice_sub_details->invoice_sub_id])[0];
                            if ($mbo_invoice_sub) {
                                $mbo_invoice = $this->Mdb->retrieve_data('invoice', ['id' => $mbo_invoice_sub->invoice_id])[0];
                                if ($mbo_invoice) {
                                    $has_invoice = true;
                                }
                            }
                        }
    
                        if (!$has_invoice) {
                            if (($mbo_payment_type) AND ($mbo_payment_type->code == '02')) {
                                $s_desc = strtolower($o_bni_transaction->description);
                                if(($x = strpos($s_desc, 'semester')) !== false){
                                    $s_sem = str_replace(' ', '_', trim(substr($s_desc, $x, 11)));
                                    $a_data[$s_sem]['payment_type'] = ($mbo_payment_type) ? $mbo_payment_type->name : '';
                                    // $a_data[$s_sem]['billed_amount'] = $o_bni_transaction->trx_amount;
                                    // $a_data[$s_sem]['description'] = $o_bni_transaction->description;
                                    // $a_data[$s_sem]['paid_amount'] = $o_bni_transaction->payment_amount;
                                    // $a_data[$s_sem]['datetime_payment'] = $o_bni_transaction->datetime_payment;

                                    if(($x_installment = strpos($s_desc, 'installment')) !== false) {
                                        $i_installment = trim(substr($s_desc, ($x_installment + (strlen('installment'))), 3));
                                        $i_installment = intval($i_installment);

                                        $a_data[$s_sem][$i_installment.'_billed_amount'] = $o_bni_transaction->trx_amount;
                                        $a_data[$s_sem][$i_installment.'_paid_amount'] = $o_bni_transaction->payment_amount;
                                        $a_data[$s_sem][$i_installment.'_datetime_payment'] = $o_bni_transaction->datetime_payment;
                                    }else{
                                        $a_data[$s_sem]['description'] = $o_bni_transaction->description;

                                        $a_data[$s_sem]['full_billed_amount'] = $o_bni_transaction->trx_amount;
                                        $a_data[$s_sem]['full_paid_amount'] = $o_bni_transaction->payment_amount;
                                        $a_data[$s_sem]['full_datetime_payment'] = $o_bni_transaction->datetime_payment;
                                    }
                                }
                                // $a_data
                            }else{
                                array_push($a_data, [
                                    'payment_type' => ($mbo_payment_type) ? $mbo_payment_type->name : '',
                                    'billed_amount' => $o_bni_transaction->trx_amount,
                                    'description' => $o_bni_transaction->description,
                                    'paid_amount' => $o_bni_transaction->payment_amount,
                                    'full_billed_amount' => $o_bni_transaction->payment_amount,
                                    'full_paid_amount' => $o_bni_transaction->payment_amount,
                                    'full_datetime_payment' => $o_bni_transaction->datetime_payment,
                                ]);
                            }
                        }
                    }
                }
                
                if ($mba_portal_invoice_student) {
                    foreach ($mba_portal_invoice_student as $o_invoice) {
                        $mba_invoice_sub = $this->Mdb->retrieve_data('invoice_sub', ['invoice_id' => $o_invoice->id, 'status != ' => 'CANCELLED']);
                        if ($mba_invoice_sub) {
                            foreach ($mba_invoice_sub as $o_invoice_sub) {
                                $mba_invoice_sub_details = $this->Mdb->retrieve_data('invoice_sub_details', ['invoice_sub_id' => $o_invoice_sub->id]);
                                if ($mba_invoice_sub_details) {
                                    foreach ($mba_invoice_sub_details as $o_invoice_sub_details) {
                                        $s_payment_code = substr($o_invoice_sub_details->virtual_account, 4, 2);
                                        $mbo_payment_type = $this->Mdb->retrieve_data('payment_type', ['code' => $s_payment_code])[0];
                                        $o_bni_transaction = $this->Mdb->retrieve_data('bni_transactions', ['id' => $o_invoice_sub_details->bni_transaction_id])[0];
    
                                        array_push($a_data, [
                                            'payment_type' => ($mbo_payment_type) ? $mbo_payment_type->name : '',
                                            'billed_amount' => $o_invoice_sub_details->amount,
                                            'description' => $o_invoice_sub_details->description,
                                            'paid_amount' => ($o_bni_transaction) ? $o_bni_transaction->payment_amount : $o_invoice_sub_details->amount_paid,
                                            'datetime_payment' => ($o_bni_transaction) ? $o_bni_transaction->datetime_payment : '',
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                if ($mba_staging_invoice_data) {
                    foreach ($mba_staging_invoice_data as $o_invoice) {
                        $mba_sub_invoice = $this->General->get_where('dt_sub_invoice', ['invoice_id' => $o_invoice->invoice_id]);
                        if ($mba_sub_invoice) {
                            foreach ($mba_sub_invoice as $o_sub_invoice) {
                                $mba_sub_invoice_details = $this->General->get_where('dt_sub_invoice_details', ['sub_invoice_id' => $o_sub_invoice->sub_invoice_id]);
                                if ($mba_sub_invoice_details) {
                                    foreach ($mba_sub_invoice_details as $o_invoice_sub_details) {
                                        $s_payment_code = substr($o_invoice_sub_details->sub_invoice_details_va_number, 4, 2);
                                        $mbo_payment_type = $this->General->get_where('ref_payment_type', ['payment_type_code' => $s_payment_code])[0];
                                        array_push($a_data, [
                                            'payment_type' => ($mbo_payment_type) ? $mbo_payment_type->payment_type_name : '',
                                            'billed_amount' => $o_invoice_sub_details->sub_invoice_details_amount,
                                            'description' => $o_invoice_sub_details->sub_invoice_details_description,
                                            'paid_amount' => $o_invoice_sub_details->sub_invoice_details_amount_paid,
                                            'datetime_payment' => $o_invoice_sub_details->sub_invoice_details_datetime_paid_off,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                if (count($a_data) > 0) {
                    $a_data = array_values($a_data);

                    array_push($a_all_data, [
                        'personal_data_name' => $o_student->personal_data_name,
                        'student_number' => $o_student->student_number,
                        'academic_year_id' => $o_student->academic_year_id,
                        'finance_year_id' => $o_student->finance_year_id,
                        'faculty_abbreviation' => $o_student->faculty_abbreviation,
                        'study_program_abbreviation' => $s_prodi_abbr,
                        'student_status' => $o_student->student_status,
                        'student_type' => $o_student->student_type,
                        'payment' => $a_data
                    ]);
                }
            }

            if (count($a_all_data) > 0) {
                $a_all_data = array_values($a_all_data);

                $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
                $s_file_name = 'Invoice_Student_Report_Year_In_'.$s_finance_year_id.'_'.date('Y-m-d');
                if ($s_student_id) {
                    $s_file_name = 'Invoice_Student_Report_'.str_replace(' ', '_', $mba_student_data[0]->personal_data_name).'_'.$s_finance_year_id.'('.date('Y-m-d').')';
                }

                $s_filename = $s_file_name.'.xlsx';

                $s_file_path = APPPATH."uploads/finance/report/student/".$s_finance_year_id."/";
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $o_spreadsheet = IOFactory::load($s_template_path);
                $o_sheet = $o_spreadsheet->getActiveSheet();
                $o_spreadsheet->getProperties()
                    ->setTitle($s_file_name)
                    ->setCreator("IULI Finance Services")
                    ->setCategory("Invoice Student Report");

                $i_row = 1;

                $o_sheet->setCellValue('A'.$i_row, 'Student Name');
                $o_sheet->setCellValue('B'.$i_row, 'Student Number');
                $o_sheet->setCellValue('C'.$i_row, 'Batch');
                $o_sheet->setCellValue('D'.$i_row, 'Year In');
                $o_sheet->setCellValue('E'.$i_row, 'Faculty');
                $o_sheet->setCellValue('F'.$i_row, 'Study Program');
                $o_sheet->setCellValue('G'.$i_row, 'Status');
                $o_sheet->setCellValue('H'.$i_row, 'Student Type');
                $o_sheet->setCellValue('I'.$i_row, 'Payment Type');
                $o_sheet->setCellValue('J'.$i_row, 'Description');
                $o_sheet->setCellValue('K'.$i_row, 'Total Billed Amount');
                $o_sheet->setCellValue('L'.$i_row, 'Total Paid Amount');
                // $o_sheet->setCellValue('M'.$i_row, 'Datetime Payment');
                $o_sheet->setCellValue('M'.$i_row, 'Full Payment Billed');
                $o_sheet->setCellValue('N'.$i_row, 'Full Payment Paid');
                $o_sheet->setCellValue('O'.$i_row, 'Datetime Payment');
                $o_sheet->setCellValue('P'.$i_row, 'Down Payment Billed');
                $o_sheet->setCellValue('Q'.$i_row, 'Down Payment Paid');
                $o_sheet->setCellValue('R'.$i_row, 'Datetime Payment');
                $o_sheet->setCellValue('S'.$i_row, '1st Installment Billed');
                $o_sheet->setCellValue('T'.$i_row, '1st Installment Paid');
                $o_sheet->setCellValue('U'.$i_row, 'Datetime Payment');
                $o_sheet->setCellValue('V'.$i_row, '2st Installment Billed');
                $o_sheet->setCellValue('W'.$i_row, '2st Installment Paid');
                $o_sheet->setCellValue('X'.$i_row, 'Datetime Payment');
                $o_sheet->setCellValue('Y'.$i_row, '3st Installment Billed');
                $o_sheet->setCellValue('Z'.$i_row, '3st Installment Paid');
                $o_sheet->setCellValue('AA'.$i_row, 'Datetime Payment');
                $o_sheet->setCellValue('AB'.$i_row, '4st Installment Billed');
                $o_sheet->setCellValue('AC'.$i_row, '4st Installment Paid');
                $o_sheet->setCellValue('AD'.$i_row, 'Datetime Payment');
                $o_sheet->setCellValue('AE'.$i_row, '5st Installment Billed');
                $o_sheet->setCellValue('AF'.$i_row, '5st Installment Paid');
                $o_sheet->setCellValue('AG'.$i_row, 'Datetime Payment');
                $o_sheet->setCellValue('AH'.$i_row, '6st Installment Billed');
                $o_sheet->setCellValue('AI'.$i_row, '6st Installment Paid');
                $o_sheet->setCellValue('AJ'.$i_row, 'Datetime Payment');

                $i_row++;

                // print('<pre>');
                // var_dump($a_all_data);exit;
                foreach ($a_all_data as $a_master_data) {
                    // $o_sheet->setCellValue('M'.$i_row, 'Datetime Payment');
                    if (count($a_master_data['payment']) > 0) {
                        foreach ($a_master_data['payment'] as $a_data) {
                            $o_sheet->setCellValue('A'.$i_row, $a_master_data['personal_data_name']);
                            $o_sheet->setCellValue('B'.$i_row, $a_master_data['student_number']);
                            $o_sheet->setCellValue('C'.$i_row, $a_master_data['academic_year_id']);
                            $o_sheet->setCellValue('D'.$i_row, $a_master_data['finance_year_id']);
                            $o_sheet->setCellValue('E'.$i_row, $a_master_data['faculty_abbreviation']);
                            $o_sheet->setCellValue('F'.$i_row, $a_master_data['study_program_abbreviation']);
                            $o_sheet->setCellValue('G'.$i_row, $a_master_data['student_status']);
                            $o_sheet->setCellValue('H'.$i_row, $a_master_data['student_type']);
                            $o_sheet->setCellValue('I'.$i_row, $a_data['payment_type']);
                            $o_sheet->setCellValue('J'.$i_row, ((isset($a_data['description'])) ? $a_data['description'] : ''));
                            $o_sheet->setCellValue('K'.$i_row, ((isset($a_data['billed_amount'])) ? $a_data['billed_amount'] : ''));
                            $o_sheet->setCellValue('L'.$i_row, ((isset($a_data['paid_amount'])) ? $a_data['paid_amount'] : ''));
                            // $o_sheet->setCellValue('M'.$i_row, ((isset($a_data['datetime_payment'])) ? (is_null($a_data['datetime_payment']) ? '-' : date('d M Y H:i:s', strtotime($a_data['datetime_payment']))) : ''));

                            $o_sheet->setCellValue('M'.$i_row, ((isset($a_data['full_billed_amount'])) ? $a_data['full_billed_amount'] : ''));
                            $o_sheet->setCellValue('N'.$i_row, ((isset($a_data['full_paid_amount'])) ? $a_data['full_paid_amount'] : ''));
                            $o_sheet->setCellValue('O'.$i_row, ((isset($a_data['full_datetime_payment'])) ? (is_null($a_data['full_datetime_payment']) ? '-' : date('d M Y H:i:s', strtotime($a_data['full_datetime_payment']))) : ''));
                            
                            $o_sheet->setCellValue('S'.$i_row, ((isset($a_data['1_billed_amount'])) ? $a_data['1_billed_amount'] : ''));
                            $o_sheet->setCellValue('T'.$i_row, ((isset($a_data['1_paid_amount'])) ? $a_data['1_paid_amount'] : ''));
                            $o_sheet->setCellValue('U'.$i_row, ((isset($a_data['1_datetime_payment'])) ? (is_null($a_data['1_datetime_payment']) ? '-' : date('d M Y H:i:s', strtotime($a_data['1_datetime_payment']))) : ''));
                            $o_sheet->setCellValue('V'.$i_row, ((isset($a_data['2_billed_amount'])) ? $a_data['2_billed_amount'] : ''));
                            $o_sheet->setCellValue('W'.$i_row, ((isset($a_data['2_paid_amount'])) ? $a_data['2_paid_amount'] : ''));
                            $o_sheet->setCellValue('X'.$i_row, ((isset($a_data['2_datetime_payment'])) ? (is_null($a_data['2_datetime_payment']) ? '-' : date('d M Y H:i:s', strtotime($a_data['2_datetime_payment']))) : ''));
                            $o_sheet->setCellValue('Y'.$i_row, ((isset($a_data['3_billed_amount'])) ? $a_data['3_billed_amount'] : ''));
                            $o_sheet->setCellValue('Z'.$i_row, ((isset($a_data['3_paid_amount'])) ? $a_data['3_paid_amount'] : ''));
                            $o_sheet->setCellValue('AA'.$i_row, ((isset($a_data['3_datetime_payment'])) ? (is_null($a_data['3_datetime_payment']) ? '-' : date('d M Y H:i:s', strtotime($a_data['3_datetime_payment']))) : ''));
                            $o_sheet->setCellValue('AB'.$i_row, ((isset($a_data['4_billed_amount'])) ? $a_data['4_billed_amount'] : ''));
                            $o_sheet->setCellValue('AC'.$i_row, ((isset($a_data['4_paid_amount'])) ? $a_data['4_paid_amount'] : ''));
                            $o_sheet->setCellValue('AD'.$i_row, ((isset($a_data['4_datetime_payment'])) ? (is_null($a_data['4_datetime_payment']) ? '-' : date('d M Y H:i:s', strtotime($a_data['4_datetime_payment']))) : ''));
                            $o_sheet->setCellValue('AE'.$i_row, ((isset($a_data['5_billed_amount'])) ? $a_data['5_billed_amount'] : ''));
                            $o_sheet->setCellValue('AF'.$i_row, ((isset($a_data['5_paid_amount'])) ? $a_data['5_paid_amount'] : ''));
                            $o_sheet->setCellValue('AG'.$i_row, ((isset($a_data['5_datetime_payment'])) ? (is_null($a_data['5_datetime_payment']) ? '-' : date('d M Y H:i:s', strtotime($a_data['5_datetime_payment']))) : ''));
                            $o_sheet->setCellValue('AH'.$i_row, ((isset($a_data['6_billed_amount'])) ? $a_data['6_billed_amount'] : ''));
                            $o_sheet->setCellValue('AI'.$i_row, ((isset($a_data['6_paid_amount'])) ? $a_data['6_paid_amount'] : ''));
                            $o_sheet->setCellValue('AJ'.$i_row, ((isset($a_data['6_datetime_payment'])) ? (is_null($a_data['6_datetime_payment']) ? '-' : date('d M Y H:i:s', strtotime($a_data['6_datetime_payment']))) : ''));

                            $i_row++;
                        }
                    }
                }

                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
                $o_writer->save($s_file_path.$s_filename);
                $o_spreadsheet->disconnectWorksheets();
                unset($o_spreadsheet);

                $a_path_info = pathinfo($s_file_path.$s_filename);
                $s_file_ext = $a_path_info['extension'];
                header('Content-Disposition: attachment; filename='.urlencode($s_filename));
                readfile( $s_file_path.$s_filename );
                exit;
            }else{
                print('Invoice kosong!');exit;
            }
            
        }else{
            print('No student found!');exit;
        }
    }

    // public function generate_recomend_subject_short_semester()
    // {
    //     $mba_score_data = $this->Scm->get_score_data_transcript([
    //         'sc.score_approval' => 'approved',
    //         'st.student_status' => 'active',
    //         'curs.curriculum_subject_credit > ' => 0,
    //         'sc.score_sum <= ' => '55.5',
    //         'sc.semester_id != ' => '17',
	// 		'sc.score_display' => 'TRUE'
    //     ]);

    //     if ($mba_score_data) {
    //         $a_key = [];
    //         $mbo_semester_active = $this->Smm->get_active_semester();
    //         $s_semester_academic = $mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id;

    //         $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

    //         $s_file_name = 'Recomend_Subject_for_Short_Semester'.$s_semester_academic.'_'.date('Y-m-d');
    //         $s_filename = $s_file_name.'.xlsx';

    //         $s_file_path = APPPATH."uploads/academic/custom_request/".$s_semester_academic."/";
    //         if(!file_exists($s_file_path)){
    //             mkdir($s_file_path, 0777, TRUE);
    //         }

    //         $o_spreadsheet = IOFactory::load($s_template_path);
    //         $o_sheet = $o_spreadsheet->getActiveSheet();
    //         $o_spreadsheet->getProperties()
    //             ->setTitle($s_file_name)
    //             ->setCreator("IULI Academic Services")
    //             ->setCategory("Recomend Subject for Short Semester");

    //         $o_sheet->setCellValue('A1', "Faculty");
    //         $o_sheet->setCellValue('B1', "Study Program");
    //         $o_sheet->setCellValue('C1', "Subject Name");
    //         $o_sheet->setCellValue('D1', "Count D and F Grade");

    //         $i_row = 2;
    //         foreach ($mba_score_data as $o_score) {
    //             if ($this->Scm->get_good_grades($o_score->subject_name, $o_score->student_id, $o_score->score_sum))  {
    //                 $mbo_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $o_score->semester_type_id])[0];
    //                 $c_score_grade = $this->grades->get_grade($o_score->score_sum);
    //                 // print($c_score_grade.'<br>');
    //                 // $mbo_student_data = $this->Stm->get_student_by_id($o_score->student_id);
    //                 $mbo_student_data = $this->Stm->get_student_filtered([
    //                     'ds.student_id' => $o_score->student_id,
    //                     'ds.academic_year_id <=' => 2019
    //                 ]);

    //                 if ($mbo_student_data) {
    //                     if (($c_score_grade == 'F') OR ($c_score_grade == 'D')) {
    //                         $i_key = (strlen($i_row) == 5) ? $i_row : str_pad($i_row, (5), "0", STR_PAD_LEFT);
    //                         $key = $mbo_student_data->faculty_abbreviation.$mbo_student_data->study_program_abbreviation.$o_score->subject_name;

    //                         if (in_array($key, $a_key)) {
    //                             // $row_data = substr($key, );
    //                             // $i_count = $o_sheet->getCell()
    //                         }else{
    //                             $o_sheet->setCellValue('A'.$i_row, $mbo_student_data->faculty_abbreviation);
    //                             $o_sheet->setCellValue('B'.$i_row, $mbo_student_data->study_program_abbreviation);
    //                             $o_sheet->setCellValue('C'.$i_row, $o_score->subject_name);
    //                             $o_sheet->setCellValue('D'.$i_row, '1');

    //                             array_push($a_key, $key);

    //                             $i_row++;
    //                         }
    //                     }
    //                 }
    //             }
    //         }

    //         $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
    //         $o_writer->save($s_file_path.$s_filename);
    //         $o_spreadsheet->disconnectWorksheets();
    //         unset($o_spreadsheet);

    //         $a_path_info = pathinfo($s_file_path.$s_filename);
	// 		$s_file_ext = $a_path_info['extension'];
	// 		header('Content-Disposition: attachment; filename='.urlencode($s_filename));
	// 		readfile( $s_file_path.$s_filename );
    //         exit;
    //     }else{
    //         print('score not found!');exit;
    //     }
    // }

    public function generate_recomend_subject_short_semester()
    {
        $mba_score_data = $this->Scm->get_score_data_transcript([
            'sc.score_approval' => 'approved',
            'st.student_status' => 'active',
            'curs.curriculum_subject_credit > ' => 0,
            'sc.score_sum <= ' => '55.5',
            'sc.semester_id != ' => '17',
			// 'sc.score_display' => 'TRUE',
            // 'st.study_program_id' => 
        ]);

        $a_student_clause_request = [
            'ds.academic_year_id <=' => 2020,
            'ds.academic_year_id >=' => 2019,
            'fc.faculty_id' => '301a3e19-348d-4398-b640-c9d2acc491fa'
        ];

        if ($mba_score_data) {
            $mbo_semester_active = $this->Smm->get_active_semester();
            $s_semester_academic = $mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id;

            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

            $s_file_name = 'Subject_score_grade_D_and_F_'.$s_semester_academic.'_'.date('Y-m-d');
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/academic/custom_request/".$s_semester_academic."/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet()->setTitle("Score List");
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Academic Services")
                ->setCategory("Recomend Subject for Short Semester");

            $o_sheet->setCellValue('A1', "Student Name");
            $o_sheet->setCellValue('B1', "Student Number");
            $o_sheet->setCellValue('C1', "Batch");
            $o_sheet->setCellValue('D1', "Faculty");
            $o_sheet->setCellValue('E1', "Study Program");
            $o_sheet->setCellValue('F1', "Subject");
            $o_sheet->setCellValue('G1', "Academic Year");
            $o_sheet->setCellValue('H1', "Semester Type");
            $o_sheet->setCellValue('I1', "Final Score");
            $o_sheet->setCellValue('J1', "Grade");

            $i_row = 2;
            foreach ($mba_score_data as $o_score) {
                $a_student_clause_request['ds.student_id'] = $o_score->student_id;
                if ($this->Scm->get_good_grades($o_score->subject_name, $o_score->student_id, $o_score->score_sum)) {
                    $mbo_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $o_score->semester_type_id])[0];
                    $c_score_grade = $this->grades->get_grade($o_score->score_sum);
                    // print($c_score_grade.'<br>');
                    // $mbo_student_data = $this->Stm->get_student_by_id($o_score->student_id);
                    $mbo_student_data = $this->Stm->get_student_filtered($a_student_clause_request, ['active', 'inactive', 'onleave'])[0];

                    if ($mbo_student_data) {
                        $s_prodi_abbr = ($mbo_student_data->student_program == $this->a_programs['NI S1']) ? $mbo_student_data->study_program_ni_abbreviation : $mbo_student_data->study_program_abbreviation;
                        if (($c_score_grade == 'F') OR ($c_score_grade == 'D')) {
                            $o_sheet->setCellValue('A'.$i_row, $mbo_student_data->personal_data_name);
                            $o_sheet->setCellValue('B'.$i_row, $mbo_student_data->student_number);
                            $o_sheet->setCellValue('C'.$i_row, $mbo_student_data->academic_year_id);
                            $o_sheet->setCellValue('D'.$i_row, $mbo_student_data->faculty_abbreviation);
                            $o_sheet->setCellValue('E'.$i_row, $s_prodi_abbr);
                            $o_sheet->setCellValue('F'.$i_row, $o_score->subject_name);
                            $o_sheet->setCellValue('G'.$i_row, $o_score->academic_year_id);
                            $o_sheet->setCellValue('H'.$i_row, $mbo_semester_type_data->semester_type_name);
                            $o_sheet->setCellValue('I'.$i_row, $o_score->score_sum);
                            $o_sheet->setCellValue('J'.$i_row, $c_score_grade);
    
                            $i_row++;
                        }
                    }
                }
            }

            $o_spreadsheet->createSheet();
            $o_spreadsheet->setActiveSheetIndex(1);
            $o_sheet_subject = $o_spreadsheet->getActiveSheet()->setTitle('Subject List');

            $o_sheet_subject->setCellValue('A1', "Faculty");
            $o_sheet_subject->setCellValue('B1', "Study Program");
            $o_sheet_subject->setCellValue('C1', "Subject Name");
            $o_sheet_subject->setCellValue('D1', "Count D and F Grade");
            $i_row = 2;
            $a_key = [];

            foreach ($mba_score_data as $o_score) {
                $a_student_clause_request['ds.student_id'] = $o_score->student_id;
                if ($this->Scm->get_good_grades($o_score->subject_name, $o_score->student_id, $o_score->score_sum)) {
                    $c_score_grade = $this->grades->get_grade($o_score->score_sum);
                    $mbo_student_data = $this->Stm->get_student_filtered($a_student_clause_request, ['active', 'inactive', 'onleave'])[0];

                    if ($mbo_student_data) {
                        $s_prodi_abbr = ($mbo_student_data->student_program == $this->a_programs['NI S1']) ? $mbo_student_data->study_program_ni_abbreviation : $mbo_student_data->study_program_abbreviation;
                        if (($c_score_grade == 'F') OR ($c_score_grade == 'D')) {
                            // $s_key = $mbo_student_data->faculty_abbreviation.$mbo_student_data->study_program_abbreviation.$o_score->subject_name;
                            $s_key = $mbo_student_data->faculty_abbreviation.$o_score->subject_name;
                            $mbs_search = array_search($s_key, $a_key);

                            if ($mbs_search) {
                                $row_data = $mbs_search;
                                $s_cell_data = $o_sheet_subject->getCell('D'.$row_data)->getValue();
                                $i_cell_data = intval($s_cell_data) + 1;
                                // $i_cell_data++;
                                $o_sheet_subject->setCellValue('D'.$row_data, $i_cell_data);
                            }else{
                                // array_push($a_key, $s_key);
                                $a_key[$i_row] = $s_key;

                                $o_sheet_subject->setCellValue('A'.$i_row, $mbo_student_data->faculty_abbreviation);
                                $o_sheet_subject->setCellValue('B'.$i_row, $s_prodi_abbr);
                                $o_sheet_subject->setCellValue('C'.$i_row, $o_score->subject_name);
                                $o_sheet_subject->setCellValue('D'.$i_row, '1');

                                $i_row++;
                            }
                        }
                    }
                }
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
			readfile( $s_file_path.$s_filename );
            exit;
        }
        else{
            print('score not found!');exit;
        }
    }

    public function download_transcript_graduated($s_student_id, $s_degree, $s_graduation_date = false, $s_rector_date = false, $s_ijd_date = false)
    {
        // if ($this->input->is_ajax_request()) {
        //     $s_student_id = $this->input->post('student_id');
        //     $s_degree = $this->input->post('degree');

            $s_graduation_date = (($s_graduation_date) AND ($s_graduation_date != 'false')) ? $s_graduation_date : false;
            $s_rector_date = (($s_rector_date) AND ($s_rector_date != 'false')) ? $s_rector_date : false;
            $s_ijd_date = (($s_ijd_date) AND ($s_ijd_date != 'false')) ? $s_ijd_date : false;
            $s_file = $this->generate_transcript_graduated($s_student_id, $s_degree, $s_graduation_date, $s_rector_date, $s_ijd_date);
            if ($s_file) {
                header('Content-Disposition: attachment; filename='.urlencode($s_file['filename']));
                readfile( $s_file['filepath'].$s_file['filename'] );
                exit;
            }else{
                show_404();
            }
        // }
    }

    public function download_file($s_file, $s_target, $s_semester_dikti = false)
    {
        $mbo_semester_active = $this->Smm->get_active_semester();
        $current_semester = $mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id;

        switch ($s_target) {
            case 'score_template':
                $s_file_path = APPPATH.'/uploads/templates/score_class/'.$s_file;
                break;

            case 'transcript_semester':
                $s_file_path = APPPATH.'/uploads/academic/'.$s_semester_dikti.'/transcript/'.$s_study_abbr.'/'.$s_file;
                break;

            case 'cummulative_gpa':
                $s_file_path = APPPATH.'/uploads/academic/'.$current_semester.'/cummulative_gpa/'.$s_file;
                break;

            case 'cummulative_gpa_validation':
                $s_file_path = APPPATH.'/uploads/academic/'.$current_semester.'/cummulative_gpa_validation/'.$s_file;
                break;

            case 'student_all_class':
                $s_file_path = APPPATH.'/uploads/academic/'.$s_semester_dikti.'/'.$s_file;
                break;
            
            default:
                $s_file_path = '';
                break;
        }

        // if ($this->input->is_ajax_request()) {
        //     $s_file_path = $this->input->post('file_path');
        // }

        if(!file_exists($s_file_path)){
            // return show_404();
            var_dump($s_file_path);
            var_dump($s_target);
            print('<pre>');
            var_dump($this->uri->segment_array());
		}
		else{
			$a_path_info = pathinfo($s_file_path);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_file));
			readfile( $s_file_path );
			exit;
		}
    }

    public function generate_krs_registration()
    {
        // email iuli, email personal, phone number
        $a_curriculum_subject_skipped_short_semester = [
			'1f780c4a-13e7-4fcf-8d3d-0041f935d966', //Virtual Factory Automation (CSE)
			'7d2924bd-a8bd-4bf0-a154-2a4fc1328c68', //Engineering Design, Design of Punching / Blanking Tool (AUE)
			'4b8236e7-a26b-44c8-8e23-620e9e0ec0bd', //Software Based PCB Manufacturing (INE)
			'442ca46c-3011-4567-bb7c-cc4629bda9d8', //Virtual Factory Automation (INE)
			'881df644-a234-4c92-b83a-11e87d9c6203', //Engineering Design, Design of Punching / Blanking Tool (INE)
			'b879101d-fd80-4f18-9829-8c6fe0b92bea', //Software Based PCB Manufacturing (MTE)
			'66bed173-3e67-4356-88ac-faea295923ea', //Virtual Factory Automation (MTE)
			'25b5fcbd-2b91-4f38-b9e1-557bac85288b', //Engineering Design, Design of Punching / Blanking Tool (MTE)
		];
        
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');

            // $s_academic_year_id = 2020;
            // $s_semester_type_id = 7;

            if ($s_semester_type_id == 7) {
                $s_semester_type = 1;
            }else{
                $s_semester_type = 2;
            }

            $mba_score_data = $this->Scm->get_score_data(array(
                'sc.academic_year_id' => $s_academic_year_id,
                'sc.semester_type_id' => $s_semester_type_id,
                'sc.score_approval' => 'approved'
                // 'sc.score_display' => 'TRUE'
            ));

            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            
            $s_file_path = APPPATH.'/uploads/academic/'.$s_academic_year_id.$s_semester_type.'/';
            $s_filename = 'KRS_Registration_'.$s_academic_year_id.'_'.$s_semester_type_id;
            $s_file_name = $s_filename.'.xlsx';

            if ($mba_score_data) {
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }
    
                $o_spreadsheet = IOFactory::load($s_template_path);
                $o_sheet = $o_spreadsheet->getActiveSheet();
                $o_spreadsheet->getProperties()
                    ->setTitle('KRS Registration Academic Year '.$s_academic_year_id.$s_semester_type_id)
                    ->setCreator("IULI Academic Services")
                    ->setLastModifiedBy("IULI Academic Services")
                    ->setCategory("KRS Registration");
    
                $i_row = 2;

                $o_sheet->setCellValue('A1', 'Student Name');
                $o_sheet->setCellValue('B1', 'Student ID');
                $o_sheet->setCellValue('C1', 'Study Program');
                $o_sheet->setCellValue('D1', 'IULI Email');
                $o_sheet->setCellValue('E1', 'Personal Email');
                $o_sheet->setCellValue('F1', 'Phone Number');
                $o_sheet->setCellValue('G1', 'Subject Name');
                $o_sheet->setCellValue('H1', 'Subject Credit');
                $o_sheet->setCellValue('I1', 'Lecturer');
                $o_sheet->setCellValue('J1', 'Lecturer Email');
                if (in_array($s_semester_type_id, ['7', '8'])) {
                    $o_sheet->setCellValue('K1', 'Paid Amount');
                }

                foreach ($mba_score_data as $o_score) {
                    $mbo_student_data = $this->Stm->get_student_by_id($o_score->student_id);
                    $s_lecturer = 'N/A';
                    $s_lecturer_email = 'N/A';
                    if (in_array($s_semester_type_id, ['7', '8'])) {
                        $mbo_fee_data = $this->Im->get_fee([
                            'payment_type_code' => '04',
                            'study_program_id' => $mbo_student_data->study_program_id,
                            'academic_year_id' => $mbo_student_data->academic_year_id
                        ]);
    
                        $d_subject_fee = 0;
                        if ($mbo_fee_data) {
                            $o_fee = $mbo_fee_data[0];
                            if ($o_score->student_id == '5bfb3427-f247-4a1f-9c37-070d4f171893') {
                                $d_subject_fee = $o_fee->fee_amount * $o_score->curriculum_subject_credit;
                            }
                            else if (!in_array($o_score->curriculum_subject_id, $a_curriculum_subject_skipped_short_semester)) {
                                $d_subject_fee = $o_fee->fee_amount * $o_score->curriculum_subject_credit;
                            }
                        }

                        $mbo_student_invoice = $this->Im->student_has_invoice_data($mbo_student_data->personal_data_id, [
                            'df.semester_id' => $o_score->semester_id,
                            'df.payment_type_code' => '04',
                            'df.study_program_id' => $mbo_student_data->study_program_id
                        ]);

                        if ($mbo_student_invoice) {
                            if ($mbo_student_invoice->invoice_status == 'paid') {
                                $o_sheet->setCellValue('K'.$i_row, $d_subject_fee);
                            }
                            else {
                                $o_sheet->setCellValue('K'.$i_row, '0');
                            }
                        }
                        else {
                            $o_sheet->setCellValue('K'.$i_row, '-');
                        }
                    }

                    if (!is_null($o_score->class_master_id)) {
                        $a_lecturer = array();
                        $a_lecturer_email = array();

                        $mba_class_lecturer = $this->Cgm->get_class_master_lecturer(array(
                            'class_master_id' => $o_score->class_master_id
                        ));

                        if ($mba_class_lecturer) {
                            foreach ($mba_class_lecturer as $o_lecturer) {
                                if (!in_array($o_lecturer->personal_data_name, $a_lecturer)) {
                                    array_push($a_lecturer, $o_lecturer->personal_data_name);
                                    array_push($a_lecturer_email, $o_lecturer->employee_email);
                                }
                            }

                            $s_lecturer = implode(' & ', $a_lecturer);
                            $s_lecturer_email = implode(' & ', $a_lecturer_email);
                        }
                    }

                    $o_sheet->setCellValue('A'.$i_row, $mbo_student_data->personal_data_name);
                    $o_sheet->setCellValue('B'.$i_row, $mbo_student_data->student_number);
                    $o_sheet->setCellValue('C'.$i_row, $mbo_student_data->study_program_name);
                    $o_sheet->setCellValue('D'.$i_row, $mbo_student_data->student_email);
                    $o_sheet->setCellValue('E'.$i_row, $mbo_student_data->personal_data_email);
                    $o_sheet->setCellValue('F'.$i_row, $mbo_student_data->personal_data_cellular.'/'.$mbo_student_data->personal_data_phone);
                    
                    $o_sheet->setCellValue('G'.$i_row, $o_score->subject_name);
                    $o_sheet->setCellValue('H'.$i_row, $o_score->curriculum_subject_credit);
                    $o_sheet->setCellValue('I'.$i_row, $s_lecturer);
                    $o_sheet->setCellValue('J'.$i_row, $s_lecturer_email);

                    $i_row++;

                }
                
                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
                $o_writer->save($s_file_path.$s_file_name);
                $o_spreadsheet->disconnectWorksheets();
                unset($o_spreadsheet);
                // print($s_filename);

                $a_return = array('code' => 0, 'file' => $s_file_name, 'semester' => $s_academic_year_id.$s_semester_type);
            }else{
                $a_return = array('code' => 1, 'message' => 'No registration data found!');
            }

            print json_encode($a_return);
        }
    }

    public function download_short_semester_registration($s_academic_year_id, $s_semester_type_id)
    {
        $s_short_semester_type_id = ($s_semester_type_id == 1) ? 7 : 8;

        $mba_student_list = $this->Scm->get_student_by_score([
            'sc.academic_year_id' => $s_academic_year_id,
            'sc.semester_type_id' => $s_short_semester_type_id
        ]);

        // (array(
        //     'sc.academic_year_id' => $s_academic_year_id,
        //     'sc.semester_type_id' => $s_semester_type_id
        // ));

        $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        $s_file_path = APPPATH.'/uploads/finance/report/short_semester/'.$s_academic_year_id.$s_semester_type_id.'/';
        $s_filename = 'Finance_Short_Semester_registration_list_'.$s_academic_year_id.'_'.$s_semester_type_id;
        $s_file_name = $s_filename.'.xlsx';
        $s_fullpath = $s_file_path.$s_file_name;

        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

        $o_spreadsheet = IOFactory::load($s_template_path);
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $o_spreadsheet->getProperties()
            ->setTitle('Short Semester Registration Academic Year '.$s_academic_year_id.$s_semester_type_id)
            ->setCreator("IULI Finance Services")
            ->setLastModifiedBy("IULI Finance Services")
            ->setCategory("Short Semester Registration");

        $i_row = 1;
        $o_sheet->setCellValue('A'.$i_row, 'No');
        $o_sheet->setCellValue('B'.$i_row, 'Student Name');
        $o_sheet->setCellValue('C'.$i_row, 'Student ID');
        $o_sheet->setCellValue('D'.$i_row, 'Fac');
        $o_sheet->setCellValue('E'.$i_row, 'Study Program');
        $o_sheet->setCellValue('F'.$i_row, 'Year In');
        $o_sheet->setCellValue('G'.$i_row, 'SKS Regiter');
        $o_sheet->setCellValue('H'.$i_row, 'SKS Approved');
        $o_sheet->setCellValue('I'.$i_row, 'SKS Pending');
        $o_sheet->setCellValue('J'.$i_row, 'SKS Rejected');
        $o_sheet->setCellValue('K'.$i_row, 'Fee per SKS');
        $o_sheet->setCellValue('L'.$i_row, 'Billed Amount');
        $o_sheet->setCellValue('M'.$i_row, 'Billed Invoice Amount');
        $o_sheet->setCellValue('N'.$i_row, 'Fee Amount Paid');
        $o_sheet->setCellValue('O'.$i_row, 'Fee Amount Paid Datetime');
        if ($mba_student_list) {
            $i_row++;
            $i_number = 1;
            foreach ($mba_student_list as $o_student) {
                $mba_subject_registration = $this->Scm->get_score_student($o_student->student_id, array(
                    'sc.academic_year_id' => $s_academic_year_id,
                    'sc.semester_type_id' => $s_short_semester_type_id
                ));

                $a_credit_register = [];
                $a_credit_approved = [];
                $a_credit_pending = [];
                $a_credit_reject = [];

                $a_subject_approved = [];
                $a_subject_pending = [];
                $a_subject_reject = [];

                $d_amount_billed = 0;
                $d_invoice_billed = 0;
                $d_amount_paid = 0;
                $d_amount_paid_datetime = '';
                $d_fee_amount = 0;

                if ($mba_subject_registration) {
                    $s_semester_id = $mba_subject_registration[0]->student_semester_id;
                    $a_fee_filter = [
                        'payment_type_code' => '04',
                        'program_id' => $o_student->program_id,
                        'study_program_id' => $o_student->study_program_id,
                        'academic_year_id' => $o_student->finance_year_id,
                        'semester_id' => $s_semester_id,
                        'fee_amount_type' => 'main'
                    ];

                    $mba_fee_data = $this->Im->get_fee($a_fee_filter);

                    if (!$mba_fee_data) {
                        print($o_student->personal_data_name.'<br>');
                        print('Fee not found for data: !'.json_encode($a_fee_filter));exit;
                    }

                    $mbo_invoice_student = $this->Im->student_has_invoice_fee_id($o_student->personal_data_id, $mba_fee_data[0]->fee_id);
                    $d_fee_amount = $mba_fee_data[0]->fee_amount;
                    if (($mbo_invoice_student) AND ($mbo_invoice_student->invoice_status != 'cancelled')) {
                        $mbo_invoice_student_full_payment_data = $this->Im->get_invoice_full_payment($mbo_invoice_student->invoice_id);
                        $d_invoice_billed = $mbo_invoice_student_full_payment_data->sub_invoice_details_amount;
                        $d_amount_paid = $mbo_invoice_student_full_payment_data->sub_invoice_details_amount_paid;
                        $d_amount_paid_datetime = $mbo_invoice_student_full_payment_data->sub_invoice_details_datetime_paid_off;
                        $d_amount_paid_datetime = (!is_null($d_amount_paid_datetime)) ? date('d F Y H:i:s', strtotime($d_amount_paid_datetime)) : '-';
                    }
                    else {
                        // print('<pre>22-'.$o_student->personal_data_name.'<br>');var_dump($a_fee_filter);exit;
                    }

                    foreach ($mba_subject_registration as $o_score) {
                        array_push($a_credit_register, $o_score->curriculum_subject_credit);

                        if ($o_score->score_approval == 'approved') {
                            array_push($a_credit_approved, $o_score->curriculum_subject_credit);
                            array_push($a_subject_approved, '- '.$o_score->subject_name.' ('.$o_score->curriculum_subject_credit.' SKS)');
                        }else if ($o_score->score_approval == 'pending') {
                            array_push($a_credit_pending, $o_score->curriculum_subject_credit);
                            array_push($a_subject_pending, '- '.$o_score->subject_name.' ('.$o_score->curriculum_subject_credit.' SKS)');
                        }else if ($o_score->score_approval == 'rejected') {
                            array_push($a_credit_reject, $o_score->curriculum_subject_credit);
                            array_push($a_subject_reject, '- '.$o_score->subject_name.' ('.$o_score->curriculum_subject_credit.' SKS)');
                        }
                    }

                    $d_amount_billed = $d_fee_amount * intval((array_sum($a_credit_approved)));
                }

                // $o_semester_data = $this->Smm->get_semester_setting(array(
                //     'dss.academic_year_id' => $s_academic_year_id,
                //     'dss.semester_type_id' => $s_semester_type_id
                // ))[0];

                // $mba_payment_repeat = $this->Scm->get_repetition_payment($o_student->personal_data_id, $o_student->finance_year_id, $o_semester_data->repetition_registration_start_date, $o_semester_data->repetition_registration_end_date );

                $o_sheet->setCellValue('A'.$i_row, $i_number);
                $o_sheet->setCellValue('B'.$i_row, $o_student->personal_data_name);
                $o_sheet->setCellValue('C'.$i_row, $o_student->student_number);
                $o_sheet->setCellValue('D'.$i_row, $o_student->faculty_abbreviation);
                $o_sheet->setCellValue('E'.$i_row, $o_student->study_program_name);
                $o_sheet->setCellValue('F'.$i_row, $o_student->finance_year_id);
                $o_sheet->setCellValue('G'.$i_row, (array_sum($a_credit_register)));
                $o_sheet->setCellValue('H'.$i_row, (array_sum($a_credit_approved)));
                $o_sheet->setCellValue('I'.$i_row, (array_sum($a_credit_pending)));
                $o_sheet->setCellValue('J'.$i_row, (array_sum($a_credit_reject)));
                $o_sheet->setCellValue('K'.$i_row, $d_fee_amount);
                $o_sheet->setCellValue('L'.$i_row, $d_amount_billed);
                $o_sheet->setCellValue('M'.$i_row, $d_invoice_billed);
                $o_sheet->setCellValue('N'.$i_row, $d_amount_paid);
                $o_sheet->setCellValue('O'.$i_row, $d_amount_paid_datetime);

                if (count($a_subject_approved) > 0) {
                    $s_approve = implode("\r\n", $a_subject_approved);
                    $o_sheet->getComment('H'.$i_row)->setAuthor('Database');
                    $commentRichText = $o_sheet->getComment('H'.$i_row)->getText()->createTextRun('Notes:');
                    $commentRichText->getFont()->setBold(true);
                    $o_sheet->getComment('H'.$i_row)->getText()->createTextRun("\r\n");
                    $o_sheet->getComment('H'.$i_row)->getText()->createTextRun($s_approve);
                    $o_sheet->getComment('H'.$i_row)->setWidth("300px")->setHeight("120px");
                }
                

                if (count($a_subject_pending) > 0) {
                    $s_pending = implode("\r\n", $a_subject_pending);
                    $o_sheet->getComment('I'.$i_row)->setAuthor('Database');
                    $commentRichText = $o_sheet->getComment('I'.$i_row)->getText()->createTextRun('Notes:');
                    $commentRichText->getFont()->setBold(true);
                    $o_sheet->getComment('I'.$i_row)->getText()->createTextRun("\r\n");
                    $o_sheet->getComment('I'.$i_row)->getText()->createTextRun($s_pending);
                    $o_sheet->getComment('I'.$i_row)->setWidth("300px")->setHeight("120px");
                }

                if (count($a_subject_reject) > 0) {
                    $s_reject = implode("\r\n", $a_subject_reject);
                    $o_sheet->getComment('J'.$i_row)->setAuthor('Database');
                    $commentRichText = $o_sheet->getComment('J'.$i_row)->getText()->createTextRun('Notes:');
                    $commentRichText->getFont()->setBold(true);
                    $o_sheet->getComment('J'.$i_row)->getText()->createTextRun("\r\n");
                    $o_sheet->getComment('J'.$i_row)->getText()->createTextRun($s_reject);
                    $o_sheet->getComment('J'.$i_row)->setWidth("300px")->setHeight("120px");
                }

                $i_row++;
                $i_number++;
            }
        }
        
        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_fullpath);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        $a_path_info = pathinfo($s_fullpath);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
        readfile( $s_fullpath );
        exit;
    }

    public function download_repeat_registration($s_academic_year_id, $s_semester_type_id)
    {
        $mba_student_list_repeat = $this->Scm->get_repeat_registration(array(
            'sc.academic_year_id' => $s_academic_year_id,
            'sc.semester_type_id' => $s_semester_type_id
        ));

        $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        $s_file_path = APPPATH.'/uploads/finance/report/repetition/'.$s_academic_year_id.$s_semester_type_id.'/';
        $s_filename = 'Finance_Repetition_registration_list_'.$s_academic_year_id.'_'.$s_semester_type_id;
        $s_file_name = $s_filename.'.xlsx';
        $s_fullpath = $s_file_path.$s_file_name;

        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

        $o_spreadsheet = IOFactory::load($s_template_path);
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $o_spreadsheet->getProperties()
            ->setTitle('Repetition Registration Academic Year '.$s_academic_year_id.$s_semester_type_id)
            ->setCreator("IULI Finance Services")
            ->setLastModifiedBy("IULI Finance Services")
            ->setCategory("Repetition Registration");

        $i_row = 1;
        $o_sheet->setCellValue('A'.$i_row, 'No');
        $o_sheet->setCellValue('B'.$i_row, 'Student Name');
        $o_sheet->setCellValue('C'.$i_row, 'Student ID');
        $o_sheet->setCellValue('D'.$i_row, 'Fac');
        $o_sheet->setCellValue('E'.$i_row, 'Study Program');
        $o_sheet->setCellValue('F'.$i_row, 'Year In');
        $o_sheet->setCellValue('G'.$i_row, 'Count Subject');
        $o_sheet->setCellValue('H'.$i_row, 'Fee Amount Total');
        $o_sheet->setCellValue('I'.$i_row, 'Invoice Description');
        $o_sheet->setCellValue('J'.$i_row, 'Invoice Note');
        $o_sheet->setCellValue('K'.$i_row, 'Fee Amount Paid');
        $o_sheet->setCellValue('L'.$i_row, 'Fee Amount Paid Datetime');
        if ($mba_student_list_repeat) {
            $i_row++;
            $i_number = 1;
            foreach ($mba_student_list_repeat as $o_student) {
                $mba_subject_repeat = $this->Scm->get_score_student($o_student->student_id, array(
                    'score_mark_for_repetition != ' => null,
                    'sc.academic_year_id' => $s_academic_year_id,
                    'sc.semester_type_id' => $s_semester_type_id
                ));

                $o_semester_data = $this->Smm->get_semester_setting(array(
                    'dss.academic_year_id' => $s_academic_year_id,
                    'dss.semester_type_id' => $s_semester_type_id
                ))[0];

                $mba_payment_repeat = $this->Scm->get_repetition_payment($o_student->personal_data_id, $o_student->finance_year_id, $o_semester_data->repetition_registration_start_date, date('Y-m-d H:i:s') );

                // if ($o_student->personal_data_name == 'MOHAMED HAAZIM') {
                //     print('<pre>');
                //     var_dump($mba_payment_repeat);exit;
                // }

                $o_sheet->setCellValue('A'.$i_row, $i_number);
                $o_sheet->setCellValue('B'.$i_row, $o_student->personal_data_name);
                $o_sheet->setCellValue('C'.$i_row, $o_student->student_number);
                $o_sheet->setCellValue('D'.$i_row, $o_student->faculty_abbreviation);
                $o_sheet->setCellValue('E'.$i_row, $o_student->study_program_name);
                $o_sheet->setCellValue('F'.$i_row, $o_student->finance_year_id);
                $o_sheet->setCellValue('G'.$i_row, ((count($mba_subject_repeat) > 0) ? count($mba_subject_repeat) : 0));

                // if ($o_student->student_id == '19ce40d0-9b61-4d4a-97ab-2d312ea5b45d') {
                //     print('<pre>');var_dump($mba_payment_repeat);exit;
                // }
                if ($mba_payment_repeat) {
                    if (count($mba_payment_repeat) > 1) {
                        $z = 0;

                        foreach ($mba_payment_repeat as $o_invoice) {
                            $o_sheet->setCellValue('A'.$i_row, $i_number);
                            $o_sheet->setCellValue('B'.$i_row, $o_student->personal_data_name);
                            $o_sheet->setCellValue('C'.$i_row, $o_student->student_number);
                            $o_sheet->setCellValue('D'.$i_row, $o_student->faculty_abbreviation);
                            $o_sheet->setCellValue('E'.$i_row, $o_student->study_program_name);
                            $o_sheet->setCellValue('F'.$i_row, $o_student->finance_year_id);
                            $o_sheet->setCellValue('G'.$i_row, ((count($mba_subject_repeat) > 0) ? count($mba_subject_repeat) : 0));


                            $d_bill = $o_invoice->sub_invoice_details_amount;
                            $d_paid = $o_invoice->sub_invoice_details_amount_paid;
                            $d_paid_date = $o_invoice->sub_invoice_details_datetime_paid_off;

                            // if (count($o_invoice) > 0) {
                            //     foreach ($mba_payment_repeat as $o_payment) {
                            //         if ($ > 0) {
                            //             $d_paid = $o_payment->sub_invoice_details_amount_paid;
                            //             $d_paid_date = $o_payment->sub_invoice_details_datetime_paid_off;
                            //         }
                            //         $d_bill = $o_payment->sub_invoice_details_amount;
                            //     }
                            // }

                            $o_sheet->setCellValue('H'.$i_row, $d_bill);
                            $o_sheet->setCellValue('I'.$i_row, $o_invoice->invoice_description);
                            $o_sheet->setCellValue('J'.$i_row, $o_invoice->invoice_note);
                            $o_sheet->setCellValue('K'.$i_row, $d_paid);
                            $o_sheet->setCellValue('L'.$i_row, ((!is_null($d_paid_date)) ? date('d F Y H:i:s', strtotime($d_paid_date)) : '-'));

                            $z++;
                            if ($z < count($mba_payment_repeat)) {
                                $i_row++;
                                $i_number++;
                            }
                        }
                    }else{
                        $d_bill = $mba_payment_repeat[0]->sub_invoice_details_amount;
                        $d_paid = 0;
                        $d_paid_date = '-';
                        if (count($mba_payment_repeat) > 0) {
                            foreach ($mba_payment_repeat as $o_payment) {
                                if ($o_payment->sub_invoice_details_amount_paid > 0) {
                                    $d_paid = $o_payment->sub_invoice_details_amount_paid;
                                    $d_paid_date = $o_payment->sub_invoice_details_datetime_paid_off;
                                }
                                $d_bill = $o_payment->sub_invoice_details_amount;
                            }
                        }
                        $count_subject = (count($mba_subject_repeat) > 0) ? count($mba_subject_repeat) : 0;
                        $d_bill = 400000 * $count_subject;

                        $o_sheet->setCellValue('H'.$i_row, $d_bill);
                        $o_sheet->setCellValue('I'.$i_row, $mba_payment_repeat[0]->invoice_description);
                        $o_sheet->setCellValue('J'.$i_row, $mba_payment_repeat[0]->invoice_note);
                        $o_sheet->setCellValue('K'.$i_row, $d_paid);
                        $o_sheet->setCellValue('L'.$i_row, (($d_paid_date != '-') ? date('d F Y H:i:s', strtotime($d_paid_date)) : '-'));
                    }
                }

                $i_row++;
                $i_number++;
            }
        }
        
        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_fullpath);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        $a_path_info = pathinfo($s_fullpath);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
        readfile( $s_fullpath );
        exit;
    }

    public function generated_repat_registration()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');

            $student_list_repeat = $this->Scm->get_repeat_registration(array(
                'sc.academic_year_id' => $s_academic_year_id,
                'sc.semester_type_id' => $s_semester_type_id
            ));

            if ($student_list_repeat) {
                $s_template_path = APPPATH.'uploads/templates/repetition_registration_template.xlsx';
                $s_file_path = APPPATH.'/uploads/academic/'.$s_academic_year_id.$s_semester_type_id.'/';
                $s_filename = 'Repetition_registration_list_'.$s_academic_year_id.'_'.$s_semester_type_id;
                $s_file_name = $s_filename.'.xlsx';
    
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }
    
                $o_spreadsheet = IOFactory::load($s_template_path);
                $o_sheet = $o_spreadsheet->getActiveSheet();
                $o_spreadsheet->getProperties()
                    ->setTitle('Repetition Registration Academic Year '.$s_academic_year_id.$s_semester_type_id)
                    ->setCreator("IULI Academic Services")
                    ->setLastModifiedBy("IULI Academic Services")
                    ->setCategory("Repetition Registration");
    
                $o_sheet->setCellValue('D1', 'Student Email');
                $o_sheet->setCellValue('E1', 'Personal Email');
                $o_sheet->setCellValue('F1', 'Study Program');
                $o_sheet->setCellValue('G1', 'Batch');
                $o_sheet->setCellValue('H1', 'Subject Name');
                $o_sheet->setCellValue('I1', 'Lecturer');
                $o_sheet->setCellValue('J1', 'Lecturer Email');
                $o_sheet->setCellValue('K1', 'Lecturer Personal Email');
                $o_sheet->setCellValue('L1', 'Semester');
                $o_sheet->setCellValue('M1', 'Score Absensi');
                $o_sheet->setCellValue('N1', 'Bill');
                $o_sheet->setCellValue('O1', 'Paid');
                $i_row = 2;
                $i_number = 1;
                
                foreach ($student_list_repeat as $o_student) {
                    $mba_subject_repeat = $this->Scm->get_score_student($o_student->student_id, array(
                        'score_mark_for_repetition != ' => null,
                        'sc.academic_year_id' => $s_academic_year_id,
                        'sc.semester_type_id' => $s_semester_type_id
                    ));

                    $o_semester_data = $this->Smm->get_semester_setting(array(
                        'dss.academic_year_id' => $s_academic_year_id,
                        'dss.semester_type_id' => $s_semester_type_id
                    ))[0];
    
                    $mba_payment_repeat = $this->Scm->get_repetition_payment($o_student->personal_data_id, $o_student->finance_year_id, $o_semester_data->repetition_registration_start_date, $o_semester_data->repetition_registration_end_date );
    
                    // $o_sheet->setCellValue('A'.$i_row, $i_number++);
                    
                    if ($mba_subject_repeat) {
                        foreach ($mba_subject_repeat as $o_score) {
                            $mba_score_semester = $this->General->get_where('ref_semester', ['semester_id' => $o_score->student_semester_id]);
                            $o_sheet->setCellValue('B'.$i_row, $o_student->personal_data_name);
                            $o_sheet->setCellValue('C'.$i_row, $o_student->student_number);
                            $o_sheet->setCellValue('D'.$i_row, $o_student->student_email);
                            $o_sheet->setCellValue('E'.$i_row, $o_student->personal_data_email);
                            $o_sheet->setCellValue('F'.$i_row, $o_student->study_program_name);
                            $o_sheet->setCellValue('G'.$i_row, $o_student->batch);
                            $o_sheet->setCellValue('L'.$i_row, (($mba_score_semester) ? $mba_score_semester[0]->semester_number : ''));

                            if (!is_null($o_score->class_master_id)) {
                                $a_lecturer = array();
                                $a_lecturer_email = array();
                                $a_lecturer_p_email = array();
                                $mba_class_lecturer = $this->Cgm->get_class_master_lecturer(array(
                                    'class_master_id' => $o_score->class_master_id
                                ));
    
                                if ($mba_class_lecturer) {
                                    foreach ($mba_class_lecturer as $o_lecturer) {
                                        if (!in_array($o_lecturer->personal_data_name, $a_lecturer)) {
                                            array_push($a_lecturer, $o_lecturer->personal_data_name);
                                            array_push($a_lecturer_email, $o_lecturer->employee_email);
                                            array_push($a_lecturer_p_email, $o_lecturer->personal_data_email);
                                        }
                                    }
    
                                    $o_sheet->setCellValue('I'.$i_row, implode(' & ', $a_lecturer));
                                    $o_sheet->setCellValue('J'.$i_row, implode(' & ', $a_lecturer_email));
                                    $o_sheet->setCellValue('K'.$i_row, implode(' & ', $a_lecturer_p_email));
                                }
                            }
    
                            $o_sheet->setCellValue('H'.$i_row, $o_score->subject_name);
                            $o_sheet->setCellValue('L'.$i_row, $o_score->semester_number);
                            $o_sheet->setCellValue('M'.$i_row, $o_score->score_absence);

                            if ($mba_payment_repeat) {
                                // $d_bill = $mba_payment_repeat[0]->sub_invoice_details_amount;
                                $d_bill = 0;
                                $d_paid = 0;
                                if (count($mba_payment_repeat) > 0) {
                                    foreach ($mba_payment_repeat as $o_payment) {
                                        if ($o_payment->sub_invoice_details_amount_paid > 0) {
                                            $d_paid += $o_payment->sub_invoice_details_amount_paid;
                                        }
                                        $d_bill += $o_payment->sub_invoice_details_amount;
                                    }
                                }

                                // $o_sheet->setCellValue('N'.$i_row, $d_bill);
                                // $o_sheet->setCellValue('O'.$i_row, $d_paid);
                                $o_sheet->setCellValue('N'.$i_row, '400000');
                                $o_sheet->setCellValue('O'.$i_row, (($d_paid > 0) ? '400000' : '0'));
                            }
                            $i_row++;
                        }
                    }
                }
    
                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
                $o_writer->save($s_file_path.$s_file_name);
                $o_spreadsheet->disconnectWorksheets();
                unset($o_spreadsheet);
    
                // print('<pre>');
                // var_dump($student_list_repeat);
                $a_return = array('code' => 0, 'file' => $s_file_name, 'semester' => $s_academic_year_id.$s_semester_type_id);
            }else{
                $a_return = array('code' => 1, 'message' => 'No registration data found!');
            }
    
            print json_encode($a_return);
        }
    }

    public function transcript_graduated($s_student_id)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        $mbo_student_start_semester = $this->Smm->get_student_start_semester($s_student_id);
        $a_filter_academic_history = [
            'institution_id != ' => '7ed83401-2862-4e12-aa60-79defe7b90a8',
            'academic_history_graduation_year' => NULL,
            'academic_history_main' => 'no',
            'academic_history_this_job' => 'no',
            'status' => 'inactive',
            'personal_data_id' => $mbo_student_data->personal_data_id
        ];

        $mbo_student_old_school = $this->Pdm->get_academic_filtered($a_filter_academic_history);

        print('<pre>');
        var_dump($mbo_student_start_semester);
    }

    public function test_repeat_subject($s_student_id)
    {
        $b_has_repeat_subject = modules::run('academic/score/has_repeat_subject', $s_student_id);
        print('<pre>');var_dump($b_has_repeat_subject);exit;
    }

    public function generate_transcript_graduated($s_student_id, $s_degree, $graduation_date = false, $rector_date = false, $ijd_date = false)
    {
        $a_student_thesis_ects_score = [
            'd5db7eb0-7026-4884-8a71-c4616aa28d99', // KARISSA ADELIA WIRATISNA (IBA/2016)
            'a9b4c6d1-f3d5-4e8b-b0aa-b50d771bbc51', // VATHRA ARYA LAENA PUTRI (IBA/2016)
            '39d08329-8f20-41aa-9d13-efba326e3f3a', // NABIL SETIOSO (IBA/2015)
        ];

        $a_student_internship_ects_score = [
            'd5db7eb0-7026-4884-8a71-c4616aa28d99', // KARISSA ADELIA WIRATISNA (IBA/2016)
            'a9b4c6d1-f3d5-4e8b-b0aa-b50d771bbc51', // VATHRA ARYA LAENA PUTRI (IBA/2016)
            '39d08329-8f20-41aa-9d13-efba326e3f3a', // NABIL SETIOSO (IBA/2015)
        ];

        $a_student_research_semester_ects_score = [
            'bad13566-91eb-494f-9cc2-798dfc3a1194', // ADRIAN PRANANTO (MEE/2016) 
            '7a038e68-2e8d-4159-abf8-ce5d6a67ecd6', // ANJANETTE LOUISE JULIANA (INR/2016) 
            '4f048cc9-96b4-4272-9cea-1702e75b91ed', //  YOHANES PAULUS (INR/2016)
            '19e32e1f-de7a-43b4-9a44-c733916a1fb1', //  FELIA (INR/2016)
        ];
        
        $a_student_id_flying_fac_ects_score = [
            '82acc22d-3dff-4b1e-a8f1-17851e58c38d', // BERNADETH VIONIRIANTY (AVE/2016)
            '2c8d986d-2935-4630-b8ec-83848c95196b', //  HARMAN RIADY SIANYOTO (MTE/2016)
            'c1dd56eb-60be-43d0-a8f6-475431b1763a', //  SAHIL KUMAR (MEE/2016)
            '8a373eef-8fd6-43fc-9d68-76f6bb5e041d', // STEPHEN LOWIS PUTRA (MTE/2016)
            'baea4fbe-90be-457a-bd7c-c04be4217778', // ABD AL KAREEM AKBIK (CHE/2016)
            'd3cdcc09-fe5f-4833-b5f3-3f61f04044af', // ANAK AGUNG AYU PRIMA DESITANIA KARANG (CHE/2016)
            'f88e9b29-05cc-4e72-bd3f-5b8bced9d498', // FARIZ ALEXANDER (CHE/2016)
            '75a0ad0b-f56f-451f-90da-9eecf2dab345', // NADHRAH MUSFIRA (CHE/2016)
            'ab816db1-9879-45ca-ae0c-4bccccb58484', // STEFANIE LOSIANA (CHE/2016)
            'e418c027-aa47-4d76-b4f5-d2a28472ac84', // FAKHRI REYHAN ISMAIL (BME/2016)
            'a8679ae6-6c0e-4e90-8b68-287d5fa1e0a2', // INDRA DANISWARA (BME/2016)
            '4a4efb22-0d87-4e18-a22b-a16aee381395', // VALLEN PATRICIA PUTRI LUWANSA (BME/2016)
            '70f36e7d-e20a-465e-ada5-f003e55b6b83', // BRYAN MICHAEL GOZARI (MTE/2016)
            '3e092532-de89-4adc-ab78-5dd9eab2b9bd', // NADHIFAH ARIANDA ALIKAPUTRI (MTE/2016)
            'b95b791b-11e2-404e-a3d1-5088ce52d78e', // RAHMANITA VALENCIA ADISURYA (MTE/2016)
            'eef16102-a760-4ee9-b4e4-074e60feaa25', // WAHYU KURNIAWAN (MTE/2015)
            '91059dc4-cf23-48d7-b510-4760112d67bb', // ANGELINE TANUPUTRI (FTE/2016)
            '7a589d2a-b445-45a2-8c36-3b526dd75008', // KLARA ELVINA LUKMANTO (FTE/2016)
            'bad13566-91eb-494f-9cc2-798dfc3a1194', // ADRIAN PRANANTO (MEE/2016)
        ];

        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        $mba_semester_data = $this->Smm->get_semester_lists([1,2], 'semester_type_id');
        $a_score_ofse_filter = array(
            'st.student_id' => $mbo_student_data->student_id,
            'sc.score_approval' => 'approved'
        );

        $a_score_extracurricular_filter = array(
            'st.student_id' => $mbo_student_data->student_id,
            'curs.curriculum_subject_type' => 'extracurricular',
            'sc.score_approval' => 'approved',
            'sc.semester_id !=' => '17',
			'sc.score_display' => 'TRUE'
        );

        $mba_score_ofse_list = $this->Scm->get_score_data($a_score_ofse_filter, [4,6]);
        $mba_score_extracurricular_list = $this->Scm->get_score_data($a_score_extracurricular_filter);
        $mba_score_flying_faculty = [];

        $a_case_numbering = ['st', 'nd', 'rd', 'th'];
        $a_subject_name_id_thesis = ['2716b5eb-2c38-4eb5-80a7-0600c5bdb71a', 'a88fc6ce-9ae0-4fc3-a3b2-2459b2c96c89'];
        $mba_thesis_score = false;
        $a_credit = [];
        $a_ects = [];
        $a_merit = [];
        $a_score_sum = [];
        $has_repetition = false;
        
        if (!$mbo_student_data) {
            return false;
            exit;
        }

        $mbo_majoring_data = $this->General->get_where('ref_study_program_majoring', [
            'study_program_majoring_id' => $mbo_student_data->study_program_majoring_id
        ])[0];

        $s_study_program_majoring_name = ($mbo_majoring_data) ? $mbo_majoring_data->majoring_name : '-';
        $s_prodi_abbr = ($mbo_student_data->program_id == $this->a_programs['NI S1']) ? $mbo_student_data->study_program_ni_abbreviation : $mbo_student_data->study_program_abbreviation;

        if (!$graduation_date) {
            $graduation_date = (!is_null($mbo_student_data->student_date_graduated)) ? $mbo_student_data->student_date_graduated : false;
        }

        $this->config->load('portal_config_'.$this->s_environment);
        $a_rectorate = $this->config->item('email');

        $mba_rector = $this->General->get_where('ref_department', ['department_id' => 5]);
        // $mbo_rector_data = $this->Emm->get_employee_data_by_email($a_rectorate['rectorate']['rector']);

        $s_study_program_id = (is_null($mbo_student_data->study_program_main_id)) ? $mbo_student_data->study_program_id : $mbo_student_data->study_program_main_id;
        $mba_study_program_data = $this->Spm->get_study_program($s_study_program_id, false)[0];

        // if ($mba_study_program_data->faculty_id == '51e2f6ff-c394-44c1-8658-7bd9dd46c654') {
        //     $mba_study_program_data->faculty_id = '301a3e19-348d-4398-b640-c9d2acc491fa';
        //     $mba_study_program_data->faculty_name = 'Faculty of Engineering';
        //     $mba_study_program_data->faculty_name_feeder = 'Fakultas Teknik';
        //     $mba_study_program_data->faculty_name_abbreviation = 'ENG';
        // }

        $s_rector_name = '';
        $s_rector_email = '';
        if (($mba_rector) AND (!is_null($mba_rector[0]->employee_id))) {
            $mbo_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_rector[0]->employee_id))[0];
            $s_rector_name = trim($this->Pdm->retrieve_title($mbo_rector_data->personal_data_id));
            $s_rector_email = $mbo_rector_data->employee_email;
        }
        
        $s_deans_name = $this->Pdm->retrieve_title($mba_study_program_data->deans_id);
        // $s_rector_name = $this->Pdm->retrieve_title($mbo_rector_data->personal_data_id);
        $s_student_name = str_replace(' ', '-', ucwords(strtolower($mbo_student_data->personal_data_name)));

        if ($s_degree == 'IJD') {
            $s_template_path = APPPATH.'uploads/templates/graduate-transcript_IJD.xls';
        }else{
            $s_template_path = APPPATH.'uploads/templates/graduate-transcript_v2.xls';
        }
        
        $s_file_path = APPPATH.'/uploads/academic/transcript-graduated/'.$mbo_student_data->academic_year_id.'/'.$s_prodi_abbr.'/';
        $s_filename = $mbo_student_data->student_number.'_Graduated_Transcript_'.$s_degree.'_'.$s_student_name;
        $s_file_name = $s_filename.'.xlsx';
        $s_file_name_ver2 = $s_filename.'_vice_rector.xlsx';

        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }
        
        $o_spreadsheet = IOFactory::load($s_template_path);
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $o_spreadsheet->getProperties()
            ->setTitle('Final Transcript Report '.$s_student_name)
            ->setCreator("IULI Academic Services")
            ->setLastModifiedBy("IULI Academic Services")
            ->setCategory("Final Transcript Report");

        if ($s_degree == 'IJD') {
            $i_header = 1;
        }
        else{
            $o_sheet->setCellValue('F1', 'T'.$mbo_student_data->student_pin_number);
            $o_sheet->setCellValue('J1', ': '.$mbo_student_data->student_pin_number);
            $i_header = 2;
        }

        $o_sheet->setCellValue('F'.$i_header, ucwords(strtolower($mbo_student_data->personal_data_name)));
        $o_sheet->setCellValue('J'.$i_header, ': '.(str_replace('Faculty of ', '', $mba_study_program_data->faculty_name)));
        $i_header++;
        
        $o_sheet->setCellValue('F'.$i_header, ucwords(strtolower($mbo_student_data->personal_data_place_of_birth)).", ".date('j F Y', strtotime($mbo_student_data->personal_data_date_of_birth)));
        $o_sheet->setCellValue('J'.$i_header, ': '.$mba_study_program_data->study_program_name);
        $i_header++;

        $o_sheet->setCellValue('F'.$i_header, $mbo_student_data->student_number);
        $o_sheet->setCellValue('J'.$i_header, ': '.$s_study_program_majoring_name);
        $i_header++;

        $o_sheet->setCellValue('F'.$i_header, ($graduation_date) ? (date('d F Y', strtotime($graduation_date))) : '');
        $o_sheet->setCellValue('J'.$i_header, ': Bachelor Degree');

        $i_row = ($s_degree == 'IJD') ? 10 : 11;
        $a_subject_name_fill = [];
        $i_row_height = 16;

        $style_alignment_left = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
            )
        );

        $style_vertical_center = array(
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            )
        );

        if ($mbo_student_data->student_type == 'transfer') {
            $a_score_trcr_filter = array(
                'st.student_id' => $mbo_student_data->student_id,
                'sc.score_approval' => 'approved',
                'curs.curriculum_subject_type != ' => 'extracurricular',
                'curs.curriculum_subject_credit > ' => 0,
                'sc.score_display' => 'TRUE'
            );
            $a_semester_trcr_id = [18];

            $mba_score_semester_trcr = $this->Scm->get_score_data_transcript($a_score_trcr_filter, $a_semester_trcr_id);
            if ($mba_score_semester_trcr) {
                $a_filter_academic_history = [
                    'institution_id != ' => '7ed83401-2862-4e12-aa60-79defe7b90a8',
                    'academic_history_graduation_year' => NULL,
                    'academic_history_main' => 'no',
                    'academic_history_this_job' => 'no',
                    // 'status' => 'inactive',
                    'personal_data_id' => $mbo_student_data->personal_data_id
                ];

                $mbo_student_old_school = $this->Pdm->get_academic_filtered($a_filter_academic_history);
                // print('<pre>');var_dump($mbo_student_old_school);exit;
                $s_institution_old_name = ($mbo_student_old_school) ? $mbo_student_old_school[0]->institution_name.', '.$mbo_student_old_school[0]->country_name : '';
                $mbo_student_start_semester = $this->Smm->get_student_start_semester($s_student_id);
                $i_padding_semester = $mbo_student_start_semester->semester_id - 1;

                $objRichTextTrcr = new RichText();
                $obj_number_text = $objRichTextTrcr->createTextRun('1');
                $obj_number_text->getFont()->setBold(true)->setSize(12);
                $objSuperscriptTrcr = $objRichTextTrcr->createTextRun($a_case_numbering[0]);
                $objSuperscriptTrcr->getFont()->setSuperScript(true)->setBold(true)->setSize(12);

                if ($i_padding_semester > 1) {
                    $objSuperscript2Sep = $objRichTextTrcr->createTextRun(' - ');

                    $obj_number_text_end = $objRichTextTrcr->createTextRun($i_padding_semester);
                    $obj_number_text_end->getFont()->setBold(true)->setSize(12);
                    $objSuperscript2Trcr = $objRichTextTrcr->createTextRun($a_case_numbering[$i_padding_semester - 1]);
                    $objSuperscript2Trcr->getFont()->setSuperScript(true)->setBold(true)->setSize(12);
                }

                $objSuperscript2Stud = $objRichTextTrcr->createTextRun(" Semester - Study Location : ".$s_institution_old_name);
                $objSuperscript2Stud->getFont()->setBold(true)->setSize(12);

                $o_sheet->setCellValue('B'.$i_row, $objRichTextTrcr);
                $o_sheet->getRowDimension($i_row)->setRowHeight(18);
                $o_sheet->getStyle('B'.$i_row)->getFont()->setSize(12);
                
                $cell_merge = $o_sheet->getCell('C'.$i_row);
                foreach ($o_sheet->getMergeCells() as $cells) {
                    if ($cell_merge->isInRange($cells)) {
                        $o_sheet->unmergeCells('C'.$i_row.':I'.$i_row);
                        break;
                    }
                }
                // $o_sheet->mergeCells('B'.$i_row.':I'.$i_row);
                
                $i_num = 1;
                $i_row++;
                $o_sheet->insertNewRowBefore($i_row, 1);
            
                foreach ($mba_score_semester_trcr as $o_score) {

                    $s_score_sum = intval(round($o_score->score_sum, 0, PHP_ROUND_HALF_UP));
                    $s_grade_point = $this->grades->get_grade_point($s_score_sum);
                    $s_grade = $this->grades->get_grade($s_score_sum);
                    $s_merit = $this->grades->get_merit($o_score->curriculum_subject_credit, $s_grade_point);
                    // $s_ects = $this->grades->get_score_ects($o_score->curriculum_subject_credit, $s_grade_point);
                    $s_ects = $this->grades->get_ects_score($o_score->curriculum_subject_credit, $o_score->subject_name);
                    

                    $subject_name = strtolower($o_score->subject_name);
    
                    // if(strpos($subject_name, 'internship') !== false){
                    //     $s_ects = 12.00;
                    // }else if(strpos($subject_name, 'thesis') !== false){
                    //     $s_ects = 14.00;
                    // }else if(strpos($subject_name, 'research semester') !== false){
                    //     $s_ects = 12.00;
                    // }

                    if(strpos($subject_name, 'research semester') !== false){
                        $s_ects = $this->grades->get_ects_score($o_score->curriculum_subject_credit, $o_score->subject_name, $o_score->score_ects);
                    }

                    if ($this->Scm->get_good_grades($o_score->subject_name, $o_score->student_id, $o_score->score_sum)) {
                        if (!in_array($o_score->subject_name, $a_subject_name_fill)) {
                            if (in_array($o_score->subject_name_id, $a_subject_name_id_thesis)) {
                                $mba_thesis_score = array(
                                    'score_sum' => $s_score_sum,
                                    'grade' => $s_grade,
                                    'credit' => $o_score->curriculum_subject_credit,
                                    'grade_point' => $s_grade_point,
                                    'merit' => $s_merit,
                                    'ects' => $s_ects
                                );
                            }else{
                                if(!is_null($o_score->score_repetition_exam)){
                                    $has_repetition = true;
                                }
    
                                $o_sheet->setCellValue('B'.$i_row, $i_num++);
                                $o_sheet->setCellValue('C'.$i_row, $o_score->subject_name);
                                $o_sheet->setCellValue('J'.$i_row, $s_score_sum);
                                $o_sheet->setCellValue('K'.$i_row, $s_grade);
                                $o_sheet->setCellValue('L'.$i_row, $s_grade_point);
                                $o_sheet->setCellValue('M'.$i_row, $o_score->curriculum_subject_credit);
                                $o_sheet->setCellValue('N'.$i_row, $s_merit);
                                $o_sheet->setCellValue('O'.$i_row, $s_ects);
                                $o_sheet->getStyle('B'.$i_row.':O'.$i_row)->getFont()->setBold( false );
                                $o_sheet->getStyle('B'.$i_row)->applyFromArray($style_alignment_left);
                                $o_sheet->getStyle('B'.$i_row)->getFont()->setSize(11);
                                $o_sheet->getRowDimension($i_row)->setRowHeight(16);

                                $i_row++;
                                $o_sheet->insertNewRowBefore($i_row, 1);
                            }

                            array_push($a_credit, $o_score->curriculum_subject_credit);
                            array_push($a_ects, $s_ects);
                            array_push($a_merit, $s_merit);
                            array_push($a_score_sum, $s_score_sum);

                            array_push($a_subject_name_fill, $o_score->subject_name);
                        }
                    }
                }
                
            }
        }

        $i_row++;
        $o_sheet->insertNewRowBefore($i_row, 1);

        foreach ($mba_semester_data as $key => $o_semester) {
            $a_score_filter = array(
                'st.student_id' => $mbo_student_data->student_id,
                'curs.curriculum_subject_type != ' => 'extracurricular',
                'curs.curriculum_subject_credit > ' => 0,
                'curs.curriculum_subject_category' => 'regular semester',
                'sc.score_approval' => 'approved',
                'sc.score_display' => 'TRUE'
            );

            // $a_semester_id = [$o_semester->semester_id];
            $s_short_semester_number = $o_semester->semester_number + 0.5;
            $mbo_short_semester_id = $this->General->get_where('ref_semester', ['semester_number' => $s_short_semester_number])[0];
            $a_semester_id = [$o_semester->semester_id, $mbo_short_semester_id->semester_id];

            // if ($o_semester->semester_type_id == 2) {
            //     $f_short_semester_number_lower = $o_semester->semester_number - 0.5;
            //     $f_short_semester_number_highest = $o_semester->semester_number + 0.5;

            //     $mbo_short_semester_id_lower = $this->General->get_where('ref_semester', ['semester_number' => $f_short_semester_number_lower])[0];
            //     $mbo_short_semester_id_highest = $this->General->get_where('ref_semester', ['semester_number' => $f_short_semester_number_highest])[0];

            //     if ($mbo_short_semester_id_lower) {
            //         array_push($a_semester_id, $mbo_short_semester_id_lower->semester_id);
            //     }
                
            //     if ($mbo_short_semester_id_highest) {
            //         array_push($a_semester_id, $mbo_short_semester_id_highest->semester_id);
            //     }
                
            // }

            $mba_score_semester = $this->Scm->get_score_data_transcript($a_score_filter, $a_semester_id);
            
            if ($mba_score_semester) {
                $b_print = true;
                $s_study_location = 'International University Liaison Indonesia';

                if (count($mba_score_semester) == 1) {
                    if (in_array($mba_score_semester[0]->subject_name_id, $a_subject_name_id_thesis)) {
                        $b_print = false;
                    }else if ($mba_score_semester[0]->score_mark_flying_faculty == 1) {
                        $b_print = false;
                    }
                }

                if ($b_print) {
                    $mbo_student_semester = $this->Smm->get_semester_student($mbo_student_data->student_id, [
                        'semester_id' => $o_semester->semester_id
                    ])[0];

                    if  ($mbo_student_semester) {
                        $s_study_location = $mbo_student_semester->institution_name;
                        
                        if ($s_study_location != 'International University Liaison Indonesia') {
                            $s_study_location = (!is_null($mbo_student_semester->country_name)) ? $s_study_location.', '.$mbo_student_semester->country_name : $s_study_location;
                        }
                    }
    
                    $objRichText = new RichText();
                    $obj_number_text = $objRichText->createTextRun($o_semester->semester_number);
                    $obj_number_text->getFont()->setBold(true)->setSize(12);
    
                    if ($o_semester->semester_number < 4) {
                        $objSuperscript = $objRichText->createTextRun($a_case_numbering[$o_semester->semester_number - 1]);
                    }else{
                        $objSuperscript = $objRichText->createTextRun('th');
                    }
                    $objSuperscript->getFont()->setSuperScript(true)->setBold(true)->setSize(12);
                    
                    $obj_add_text = $objRichText->createTextRun(' Semester - Study Location : '.$s_study_location);
                    $obj_add_text->getFont()->setBold(true)->setSize(12);

                    $o_sheet->setCellValue('B'.$i_row, $objRichText);
                    $o_sheet->getStyle('B'.$i_row)->getFont()->setSize(12);
                    $o_sheet->getRowDimension($i_row)->setRowHeight(18);

                    $cell_merge = $o_sheet->getCell('C'.$i_row);
                    foreach ($o_sheet->getMergeCells() as $cells) {
                        if ($cell_merge->isInRange($cells)) {
                            $o_sheet->unmergeCells('C'.$i_row.':I'.$i_row);
                            break;
                        }
                    }
                    
                    $i_row++;
                    $o_sheet->insertNewRowBefore($i_row, 1);
                }

                $i_num = 1;
                foreach ($mba_score_semester as $o_score) {
                    $s_score_sum = intval(round($o_score->score_sum, 0, PHP_ROUND_HALF_UP));
                    $s_grade_point = $this->grades->get_grade_point($s_score_sum);
                    $s_grade = $this->grades->get_grade($s_score_sum);
                    $s_merit = $this->grades->get_merit($o_score->curriculum_subject_credit, $s_grade_point);
                    // $s_ects = $this->grades->get_score_ects($o_score->curriculum_subject_credit, $s_grade_point);
                    $b_research_subject = false;
                    $s_ects = $this->grades->get_ects_score($o_score->curriculum_subject_credit, $o_score->subject_name);
                    $subject_name = strtolower($o_score->subject_name);

                    if(strpos($subject_name, 'research semester') !== false){
                        $s_ects = $this->grades->get_ects_score($o_score->curriculum_subject_credit, $o_score->subject_name, $o_score->score_ects);
                        $b_research_subject = true;
                    }

                    if ($this->Scm->get_good_grades($o_score->subject_name, $o_score->student_id, $o_score->score_sum)) {
                        if (!in_array($o_score->subject_name, $a_subject_name_fill)) {
                            if (in_array($o_score->subject_name_id, $a_subject_name_id_thesis)){
                                if (in_array($o_score->student_id, $a_student_thesis_ects_score)) {
                                    $s_ects = $this->grades->get_ects_score($o_score->curriculum_subject_credit, $o_score->subject_name, $o_score->score_ects);
                                }

                                $mba_thesis_score = array(
                                    'score_sum' => $s_score_sum,
                                    'grade' => $s_grade,
                                    'credit' => $o_score->curriculum_subject_credit,
                                    'grade_point' => $s_grade_point,
                                    'merit' => $s_merit,
                                    'ects' => $s_ects
                                );
                            }else if($o_score->score_mark_flying_faculty == 1){

                                array_push($mba_score_flying_faculty, $o_score);

                                if (in_array($o_score->student_id, $a_student_id_flying_fac_ects_score)) {
                                    $s_ects = $this->grades->get_ects_score($o_score->curriculum_subject_credit, $o_score->subject_name, $o_score->score_ects);
                                }

                            }else{
                                if(!is_null($o_score->score_repetition_exam)){
                                    $has_repetition = true;
                                }

                                if(strpos($subject_name, 'internship') !== false){
                                    if (in_array($o_score->student_id, $a_student_internship_ects_score)) {
                                        $s_ects = $this->grades->get_ects_score($o_score->curriculum_subject_credit, $o_score->subject_name, $o_score->score_ects);
                                    }
                                }
    
                                $o_sheet->setCellValue('B'.$i_row, $i_num++);
                                $o_sheet->setCellValue('C'.$i_row, $o_score->subject_name);
                                $o_sheet->setCellValue('J'.$i_row, $s_score_sum);
                                $o_sheet->setCellValue('K'.$i_row, $s_grade);
                                $o_sheet->setCellValue('L'.$i_row, $s_grade_point);
                                $o_sheet->setCellValue('M'.$i_row, $o_score->curriculum_subject_credit);
                                $o_sheet->setCellValue('N'.$i_row, $s_merit);
                                $o_sheet->setCellValue('O'.$i_row, $s_ects);
                                $o_sheet->getStyle('B'.$i_row.':O'.$i_row)->getFont()->setBold( false );
                                $o_sheet->getStyle('B'.$i_row)->applyFromArray($style_alignment_left);
                                $o_sheet->getStyle('B'.$i_row)->getFont()->setSize(11);
                                $o_sheet->getRowDimension($i_row)->setRowHeight(16);
                                $i_row++;
    
                                $o_sheet->insertNewRowBefore($i_row, 1);
                            }

                            array_push($a_credit, $o_score->curriculum_subject_credit);
                            array_push($a_ects, $s_ects);
                            array_push($a_merit, $s_merit);
                            array_push($a_score_sum, $s_score_sum);

                            array_push($a_subject_name_fill, $o_score->subject_name);

                            if ($b_research_subject) {
                                $a_score_research_semester_filter = array(
                                    'st.student_id' => $mbo_student_data->student_id,
                                    'curs.curriculum_subject_credit' => 0,
                                    'curs.curriculum_subject_category' => 'research semester',
                                    'sc.score_approval' => 'approved',
                                    'sc.score_display' => 'TRUE'
                                );
                                
                                $mba_subject_research_semester = $this->Scm->get_score_data_transcript($a_score_research_semester_filter);
                                if ($mba_subject_research_semester) {
                                    // $i_numb = 1;
                                    foreach ($mba_subject_research_semester as $o_subject_research_semester) {
                                        $o_sheet->setCellValue('C'.$i_row, '- '.$o_subject_research_semester->subject_name);
                                        $o_sheet->getStyle('C'.$i_row.':O'.$i_row)->getFont()->setBold( false );
                                        $o_sheet->getStyle('C'.$i_row)->applyFromArray($style_alignment_left);
                                        $o_sheet->getStyle('C'.$i_row)->getFont()->setSize(11);
                                        $o_sheet->getRowDimension($i_row)->setRowHeight(16);

                                        $i_row++;
                                        $o_sheet->insertNewRowBefore($i_row, 1);
                                    }
                                }
                            }
                        }
                    }
                }

                if ($b_print) {
                    $i_row++;
                    $o_sheet->insertNewRowBefore($i_row, 1);
                }
            }
        }

        if (count($mba_score_flying_faculty) > 0) {
            $o_sheet->setCellValue('B'.$i_row, 'Flying Faculty');
            $o_sheet->getStyle('B'.$i_row)->getFont()->setSize(12);
            $o_sheet->getStyle('B'.$i_row)->getFont()->setBold( true );
            $o_sheet->getRowDimension($i_row)->setRowHeight(18);

            $i_row++;
            $o_sheet->insertNewRowBefore($i_row, 1);

            $i_num = 1;
            foreach ($mba_score_flying_faculty as $o_score_ff) {
                $s_score_sum = intval(round($o_score_ff->score_sum, 0, PHP_ROUND_HALF_UP));
                $s_grade_point = $this->grades->get_grade_point($s_score_sum);
                $s_grade = $this->grades->get_grade($s_score_sum);
                $s_merit = $this->grades->get_merit($o_score_ff->curriculum_subject_credit, $s_grade_point);
                // $s_ects = $this->grades->get_score_ects($o_score_ff->curriculum_subject_credit, $s_grade_point);
                $s_ects = $this->grades->get_ects_score($o_score_ff->curriculum_subject_credit, $o_score_ff->subject_name);

                if (in_array($o_score_ff->student_id, $a_student_id_flying_fac_ects_score)) {
                    $s_ects = $this->grades->get_ects_score($o_score_ff->curriculum_subject_credit, $o_score_ff->subject_name, $o_score_ff->score_ects);
                }

                $o_sheet->setCellValue('B'.$i_row, $i_num++);
                $o_sheet->setCellValue('C'.$i_row, $o_score_ff->subject_name);
                // $o_sheet->mergeCells('C'.$i_row.':I'.$i_row);
                $o_sheet->setCellValue('J'.$i_row, $s_score_sum);
                $o_sheet->setCellValue('K'.$i_row, $s_grade);
                $o_sheet->setCellValue('L'.$i_row, $s_grade_point);
                $o_sheet->setCellValue('M'.$i_row, intval($o_score_ff->curriculum_subject_credit));
                $o_sheet->setCellValue('N'.$i_row, $s_merit);
                $o_sheet->setCellValue('O'.$i_row, $s_ects);
                
                $o_sheet->getStyle('B'.$i_row.':O'.$i_row)->getFont()->setBold( false );
                $o_sheet->getStyle('B'.$i_row)->applyFromArray($style_alignment_left);
                $o_sheet->getStyle('B'.$i_row)->getFont()->setSize(11);
                $o_sheet->getRowDimension($i_row)->setRowHeight(16);

                $i_row++;
                $o_sheet->insertNewRowBefore($i_row, 1);
            }
            $i_row++;
            $o_sheet->insertNewRowBefore($i_row, 1);
        }
        // else{
        //     $i_row++;
        //     $o_sheet->insertNewRowBefore($i_row, 1);
        // }

        $o_sheet->setCellValue('B'.$i_row, 'Thesis');
        $o_sheet->getStyle('B'.$i_row)->getFont()->setSize(12);
        $o_sheet->getStyle('B'.$i_row)->getFont()->setBold( true );
        $o_sheet->getRowDimension($i_row)->setRowHeight(18);

        $i_row++;
        $o_sheet->insertNewRowBefore($i_row, 1);

        $cell_merges = $o_sheet->getCell('B'.$i_row);
        foreach ($o_sheet->getMergeCells() as $cells) {
            if ($cell_merges->isInRange($cells)) {
                $o_sheet->unmergeCells('B'.$i_row.':I'.$i_row);
                break;
            }
        }

        $cell_merge = $o_sheet->getCell('C'.$i_row);
        foreach ($o_sheet->getMergeCells() as $cells) {
            if ($cell_merge->isInRange($cells)) {
                $o_sheet->unmergeCells('C'.$i_row.':I'.$i_row);
                break;
            }
        }

        $o_sheet->setCellValue('B'.$i_row, $mbo_student_data->student_thesis_title);

        $o_sheet->mergeCells('B'.$i_row.':I'.$i_row);
        $o_sheet->getStyle('B'.$i_row)->getAlignment()->setWrapText(true);
        $o_sheet->getStyle('B'.$i_row.':O'.$i_row)->applyFromArray($style_vertical_center);
        $o_sheet->getStyle('B'.$i_row.':O'.$i_row)->getFont()->setBold( false );
        $o_sheet->getStyle('B'.$i_row)->applyFromArray($style_alignment_left);
        $o_sheet->getStyle('B'.$i_row.':O'.$i_row)->getFont()->setSize(11);

        $i_string_len = strlen($mbo_student_data->student_thesis_title);

        if ($mba_thesis_score) {
            $o_sheet->setCellValue('J'.$i_row, $mba_thesis_score['score_sum']);
            $o_sheet->setCellValue('K'.$i_row, $mba_thesis_score['grade']);
            $o_sheet->setCellValue('L'.$i_row, $mba_thesis_score['grade_point']);
            $o_sheet->setCellValue('M'.$i_row, $mba_thesis_score['credit']);
            $o_sheet->setCellValue('N'.$i_row, $mba_thesis_score['merit']);
            $o_sheet->setCellValue('O'.$i_row, $mba_thesis_score['ects']);
        }

        $i_row++;
        $o_sheet->insertNewRowBefore($i_row, 1);
        $i_row++;
        $o_sheet->insertNewRowBefore($i_row, 1);

        $objRichText = new RichText();
        $obj_number_text = $objRichText->createTextRun('Oral Final Study Examination (OFSE)');
        $obj_number_text->getFont()->setBold(true)->setSize(12);

        $objSuperscript = $objRichText->createTextRun('7');
        $objSuperscript->getFont()->setSuperScript(true)->setBold(true)->setSize(12);

        $o_sheet->setCellValue('B'.$i_row, $objRichText);
        $o_sheet->getStyle('B'.$i_row)->getAlignment()->setWrapText(false);
        $o_sheet->getStyle('B'.$i_row)->getFont()->setSize(12);
        $o_sheet->getStyle('B'.$i_row)->getFont()->setBold( true );
        $o_sheet->getRowDimension($i_row)->setRowHeight(18);

        $i_row++;
        $o_sheet->insertNewRowBefore($i_row, 1);

        if ($mba_score_ofse_list) {
            $i_num = 1;
            foreach ($mba_score_ofse_list as $o_ofse_score) {
                $cell_merge = $o_sheet->getCell('B'.$i_row);
                foreach ($o_sheet->getMergeCells() as $cells) {
                    if ($cell_merge->isInRange($cells)) {
                        $o_sheet->unmergeCells('B'.$i_row.':I'.$i_row);
                        break;
                    }
                }
                $s_score_sum = intval(round($o_ofse_score->score_sum, 0, PHP_ROUND_HALF_UP));
                $s_grade_point = $this->grades->get_grade_point($s_score_sum);
                $s_grade = $this->grades->get_grade($s_score_sum);

                $o_sheet->setCellValue('B'.$i_row, $i_num++);
                $o_sheet->setCellValue('C'.$i_row, $o_ofse_score->subject_name);
                // $o_sheet->mergeCells('C'.$i_row.':I'.$i_row);
                $o_sheet->setCellValue('J'.$i_row, $s_score_sum);
                $o_sheet->setCellValue('K'.$i_row, $s_grade);
                $o_sheet->setCellValue('L'.$i_row, $s_grade_point);
                $o_sheet->setCellValue('M'.$i_row, '-');
                $o_sheet->setCellValue('N'.$i_row, '-');
                $o_sheet->setCellValue('O'.$i_row, '-');

                $o_sheet->getStyle('B'.$i_row.':O'.$i_row)->getFont()->setBold( false );
                $o_sheet->getStyle('B'.$i_row)->applyFromArray($style_alignment_left);
                $o_sheet->getStyle("B$i_row:O$i_row")->getFont()->setSize(11);
                $o_sheet->getRowDimension($i_row)->setRowHeight(16);
                
                $i_row++;
                $o_sheet->insertNewRowBefore($i_row, 1);
            }
        }else{
            $i_row++;
            $o_sheet->insertNewRowBefore($i_row, 1);
        }

        $i_row++;
        $o_sheet->insertNewRowBefore($i_row, 1);

        $objRichText = new RichText();
        $obj_number_text = $objRichText->createTextRun('Extracurricullar');
        $obj_number_text->getFont()->setBold(true)->setSize(12);

        $objSuperscript = $objRichText->createTextRun('7');
        $objSuperscript->getFont()->setSuperScript(true)->setBold(true)->setSize(12);

        $o_sheet->setCellValue('B'.$i_row, $objRichText);
        $o_sheet->getStyle('B'.$i_row)->getAlignment()->setWrapText(false);
        $o_sheet->getStyle('B'.$i_row)->getFont()->setSize(12);
        $o_sheet->getStyle('B'.$i_row)->getFont()->setBold( true );
        $o_sheet->getRowDimension($i_row)->setRowHeight(18);

        $i_row++;
        $o_sheet->insertNewRowBefore($i_row, 1);

        if ($mba_score_extracurricular_list) {
            $i_num = 1;
            foreach ($mba_score_extracurricular_list as $o_score_excur) {
                if ($this->Scm->get_good_grades($o_score_excur->subject_name, $o_score_excur->student_id, $o_score_excur->score_sum)) {
                    $cell_merge = $o_sheet->getCell('B'.$i_row);
                    foreach ($o_sheet->getMergeCells() as $cells) {
                        if ($cell_merge->isInRange($cells)) {
                            $o_sheet->unmergeCells('B'.$i_row.':I'.$i_row);
                            break;
                        }
                    }
                    
                    $s_score_sum = intval(round($o_score_excur->score_sum, 0, PHP_ROUND_HALF_UP));
                    $s_grade_point = $this->grades->get_grade_point($s_score_sum);
                    $s_grade = $this->grades->get_grade($s_score_sum);
                    $s_merit = $this->grades->get_merit($o_score_excur->curriculum_subject_credit, $s_grade_point);
                    // $s_ects = $this->grades->get_score_ects($o_score_excur->curriculum_subject_credit, $s_grade_point);
                    $s_ects = $this->grades->get_ects_score($o_score_excur->curriculum_subject_credit, $o_score_excur->subject_name);

                    if ($o_score_excur->score_grade != 'F') {
                        $o_sheet->setCellValue('B'.$i_row, $i_num++);
                        $o_sheet->setCellValue('C'.$i_row, $o_score_excur->subject_name);
                        // $o_sheet->mergeCells('C'.$i_row.':I'.$i_row);
                        $o_sheet->setCellValue('J'.$i_row, $s_score_sum);
                        $o_sheet->setCellValue('K'.$i_row, $s_grade);
                        $o_sheet->setCellValue('L'.$i_row, $s_grade_point);
                        $o_sheet->setCellValue('M'.$i_row, intval($o_score_excur->curriculum_subject_credit));
                        $o_sheet->setCellValue('N'.$i_row, $s_merit);
                        $o_sheet->setCellValue('O'.$i_row, $s_ects);
                        
                        $o_sheet->getStyle('B'.$i_row.':O'.$i_row)->getFont()->setBold(false);
                        $o_sheet->getStyle('B'.$i_row)->applyFromArray($style_alignment_left);
                        $o_sheet->getStyle("B$i_row:O$i_row")->getFont()->setSize(11);
                        $o_sheet->getRowDimension($i_row)->setRowHeight(16);

                        $i_row++;
                        $o_sheet->insertNewRowBefore($i_row, 1);
                    }
                }
            }
        }else{
            $i_row++;
            $o_sheet->insertNewRowBefore($i_row, 1);
        }

        $i_sum_credit = array_sum($a_credit);
        $i_sum_ects = array_sum($a_ects);
        $d_sum_merit = array_sum($a_merit);
        $i_sum_score_sum = array_sum($a_score_sum);

        $d_avg_score_sum = $i_sum_score_sum / count($a_score_sum);
        $d_avg_score_sum = intval(round($d_avg_score_sum, 0, PHP_ROUND_HALF_UP));
        $s_avg_grade = $this->grades->get_grade($d_avg_score_sum);
        $cumulative_gpa = $this->grades->get_ipk($d_sum_merit, $i_sum_credit);
        
        $i_row++;
        $o_sheet->setCellValue('J'.$i_row, $d_avg_score_sum);
        $o_sheet->setCellValue('K'.$i_row, $s_avg_grade);

        $i_row++;
        $o_sheet->setCellValue('M'.$i_row, (round($i_sum_credit, 0, PHP_ROUND_HALF_UP)));
        $o_sheet->setCellValue('N'.$i_row, $d_sum_merit);
        $o_sheet->setCellValue('O'.$i_row, (round($i_sum_ects, 1, PHP_ROUND_HALF_UP)));

        $i_row++;
        $o_sheet->setCellValue('J'.$i_row, (round($cumulative_gpa, 2, PHP_ROUND_HALF_UP)));

        $i_row++;
        $s_predicate = '-';
        $b_has_repeat_subject = modules::run('academic/score/has_repeat_subject', $s_student_id);

        if(!$b_has_repeat_subject){
            if(($cumulative_gpa >= 3.5) AND ($cumulative_gpa <= 3.7)){
                $s_predicate = 'Cum Laude';
            }
            
            if(($cumulative_gpa >= 3.71) AND ($cumulative_gpa <= 3.89)){
                $s_predicate = 'Magna Cum Laude';
            }
            
            if(($cumulative_gpa >= 3.9) AND ($cumulative_gpa <= 4)){
                $s_predicate = 'Summa Cum Laude';
            }
        }
        $o_sheet->setCellValue('J'.$i_row, $s_predicate);

        
        $i_row += ($s_degree == 'IJD') ? 16 : 13;
        $s_rectorate_date = 'Tangerang Selatan, '.(($rector_date) ? date('d F Y', strtotime($rector_date)) : '');
        $s_ijd_date = 'Ilmenau, '.(($ijd_date) ? date('d F Y', strtotime($ijd_date)) : '');
        $o_sheet->setCellValue('C'.$i_row, $s_rectorate_date);
        if ($s_degree != 'IJD') {
            $o_sheet->setCellValue('I'.$i_row, '');
            $i_row++;
            $o_sheet->setCellValue('I'.$i_row, '');
            $i_row += 7;
        }else{
            $o_sheet->setCellValue('I'.$i_row, $s_ijd_date);
            $i_row += 11;
        }

        if ($s_degree != 'IJD') {
            $o_sheet->setCellValue('C'.$i_row, $s_rector_name);
            $o_sheet->setCellValue('I'.$i_row, $s_deans_name);
            $i_row++;
            $o_sheet->setCellValue('I'.$i_row, 'Dean of '.$mba_study_program_data->faculty_name);
        }
        else {
            $i_rector_name = $i_row - 3;
            $o_sheet->setCellValue('C'.$i_rector_name, $s_rector_name);
        }
        // else{
        //     $o_sheet->removeRow(1);
        // }
        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_file_name);


        // version vice rector academic
        $o_writer->save($s_file_path.$s_file_name_ver2);
        
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        // if(!file_exists($s_file_path.$s_file_name)){
        //     var_dump($s_file_path);
		// }
		// else{
		// 	$a_path_info = pathinfo($s_file_path.$s_file_name);
		// 	$s_file_ext = $a_path_info['extension'];
		// 	header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
		// 	readfile( $s_file_path.$s_file_name );
		// 	exit;
        // }
        
        return array(
            'filepath' => $s_file_path,
            'filename' => $s_file_name,
            'filename_version_2' => $s_file_name_ver2
        );
    }

    public function testshow()
    {
        $b_has_repeat_subject = modules::run('academic/score/has_repeat_subject', $s_student_id);
        print($b_has_repeat_subject);exit;
    }

    public function generate_all_class_student()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            // $s_academic_year_id = 2019;
            // $s_semester_type_id = 2;

            $mba_data = modules::run('academic/Class_group/get_all_class_absence', $s_academic_year_id, $s_semester_type_id);
        
            if ($mba_data) {
                $s_template_path = APPPATH.'uploads/templates/template_list_all_student_in_class.xlsx';
                $s_file_name = 'student_all_class_'.$s_academic_year_id.$s_semester_type_id;
                $s_filename = $s_file_name.'.xlsx';

                $s_file_path = APPPATH.'uploads/academic/'.$s_academic_year_id.$s_semester_type_id.'/';
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $o_spreadsheet = IOFactory::load($s_template_path);
                $o_sheet = $o_spreadsheet->getActiveSheet();
                $o_spreadsheet->getProperties()
                    ->setTitle($s_file_name)
                    ->setCreator("IULI Academic Services")
                    ->setLastModifiedBy("IULI Academic Services")
                    ->setCategory("Student All Class Data");

                $o_sheet->setCellValue('C1', 'Student Email');
                $o_sheet->setCellValue('D1', 'Personal Email');
                $o_sheet->setCellValue('E1', 'Dept');
                $o_sheet->setCellValue('F1', 'Sem');
                $o_sheet->setCellValue('G1', 'Rombel');
                $o_sheet->setCellValue('H1', 'Subject Name');
                $o_sheet->setCellValue('I1', 'Absence');
                $o_sheet->setCellValue('J1', 'lecturer');
                $i_row = 2;

                foreach ($mba_data as $o_data){
                    $o_sheet->setCellValue('A'.$i_row, $o_data->personal_data_name);
                    $o_sheet->setCellValue('B'.$i_row, $o_data->student_number);
                    $o_sheet->setCellValue('C'.$i_row, $o_data->student_email);
                    $o_sheet->setCellValue('D'.$i_row, $o_data->personal_data_email);
                    $o_sheet->setCellValue('E'.$i_row, $o_data->study_program_abbreviation);
                    $o_sheet->setCellValue('F'.$i_row, $o_data->score_semester);
                    $o_sheet->setCellValue('G'.$i_row, $o_data->class_name);
                    $o_sheet->setCellValue('H'.$i_row, $o_data->subject_name);
                    $o_sheet->setCellValue('I'.$i_row, $o_data->score_absence);
                    $o_sheet->setCellValue('J'.$i_row, $o_data->lecturer_list);

                    $i_row++;
                }

                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
                $o_writer->save($s_file_path.$s_filename);
                $o_spreadsheet->disconnectWorksheets();
                unset($o_spreadsheet);

                $a_return = array(
                    'code' => 0,
                    'filename' => $s_filename,
                    'file_path' => $s_file_path.$s_filename
                );

            }else{
                $a_return = array(
                    'code' => 1,
                    'message' => 'Data not found'
                );
            }

            print json_encode($a_return);
        }
        
    }

    public function generate_mid_transcript($s_student_id, $s_academic_year_id, $s_semester_type_id, $s_date_semester_start, $s_date_semester_end, $s_issued_date = null)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        $mbo_semester_type_data = $this->General->get_where('ref_semester_type',['semester_type_id' => $s_semester_type_id])[0];
        $s_issued_date = (is_null($s_issued_date)) ? date('Y-m-d') : $s_issued_date;
        if ($mbo_student_data) {
            // $s_study_program_id = (is_null($mbo_student_data->study_program_main_id)) ? $mbo_student_data->study_program_id : $mbo_student_data->study_program_main_id;
            
            $mbo_deans_data = $this->Pdm->get_personal_data_by_id($mbo_student_data->deans_id);
            $mbo_hod_data = $this->Pdm->get_personal_data_by_id($mbo_student_data->head_of_study_program_id);
            $a_score_filter = array(
                'st.student_id' => $mbo_student_data->student_id,
                'curs.curriculum_subject_type != ' => 'extracurricular',
                'curs.curriculum_subject_credit > ' => 0,
                'sc.score_approval' => 'approved',
                'sc.score_display' => 'TRUE',
                'sc.academic_year_id' => $s_academic_year_id,
                'sc.semester_type_id' => $s_semester_type_id
            );

            $mba_score_semester = $this->Scm->get_score_data_transcript($a_score_filter);

            $a_score_extracurr_filter = array(
                'st.student_id' => $mbo_student_data->student_id,
                'curs.curriculum_subject_type' => 'extracurricular',
                'curs.curriculum_subject_credit > ' => 0,
                'sc.score_approval' => 'approved',
                'sc.score_display' => 'TRUE',
                'sc.academic_year_id' => $s_academic_year_id,
                'sc.semester_type_id' => $s_semester_type_id
            );

            $mba_score_extracurr_semester = $this->Scm->get_score_data_transcript($a_score_extracurr_filter);
            // print('<pre>');
            // var_dump($mba_score_extracurr_semester);exit;
            $s_template_path = APPPATH.'uploads/templates/transcript-mid-term.xlsx';

            $s_file_path = APPPATH.'uploads/academic/'.$s_academic_year_id.$s_semester_type_id.'/mid_transcript/'.$mbo_student_data->study_program_abbreviation.'/';
            $s_file_name = $mbo_student_data->student_number."_Mid Term Score_".$mbo_student_data->personal_data_name;
            $s_file_name = str_replace("'", "", $s_file_name);
            $s_file_name = str_replace(" ", "-", $s_file_name);
            // $s_file = url_title($s_file_name).".xls";
            $s_file = $s_file_name.".xls";
            $s_file_full_path = $s_file_path.$s_file;

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $title = 'Mid-Term Transcript Report '.$mbo_student_data->personal_data_name;
            $o_spreadsheet->getProperties()
                ->setTitle($title)
                ->setCreator("IULI Academic Services")
                ->setLastModifiedBy("IULI Academic Services")
                ->setCategory("Mid Transcript Report");

            // $o_sheet = $o_spreadsheet->setActiveSheetIndexByName("Transcript");
            $o_sheet->setCellValue('C10', 'Mid-Term Transcript of Academic '.$mbo_semester_type_data->semester_type_name.' Semester');
            $o_sheet->setCellValue('C11', 'Academic Year '.$s_academic_year_id.'-'.(intval($s_academic_year_id) + 1).'');
            $o_sheet->setCellValue('C12', date('d F Y', strtotime($s_date_semester_start)).' - '.date('d F Y', strtotime($s_date_semester_end)));

            $o_sheet->setCellValue('F17', $mbo_student_data->personal_data_name);
            $o_sheet->setCellValue('F18', $mbo_student_data->personal_data_place_of_birth.', '.date('d F Y', strtotime($mbo_student_data->personal_data_date_of_birth)));
            $o_sheet->setCellValue('F19', $mbo_student_data->student_number);
            // $o_sheet->setCellValue('F20', $mbo_student_data->faculty_name.' / '.$mbo_student_data->study_program_name);
            $o_sheet->setCellValue('C20', 'Study Program');
            $o_sheet->setCellValue('F20', $mbo_student_data->study_program_name);
            $o_sheet->setCellValue('F21', date('d F Y', strtotime($s_issued_date)));

            $i_row = 24;
            $i_num = 1;

            $a_sum_score_sum = array();
            $a_sum_credit = array();
            $a_sum_merit = array();

            if ($mba_score_semester) {
                foreach ($mba_score_semester as $o_score) {
                    $o_sheet->insertNewRowBefore($i_row + 1, 1);
                    $o_sheet->mergeCells('D' . $i_row . ':G' . $i_row);
                    $score_absence = round($o_score->score_absence, 2, PHP_ROUND_HALF_UP);
                    $score_quiz = intval(round($o_score->score_quiz, 0, PHP_ROUND_HALF_UP));
                    $score_grade = $this->grades->get_grade($score_quiz);
                    $score_grade_point = $this->grades->get_grade_point($score_quiz);
                    $score_merit = $this->grades->get_merit($o_score->curriculum_subject_credit, $score_grade_point);

                    if (($score_grade == 'F') OR ($score_absence > 25)) {
                        $remarks = 'FAIL';
                    } else {
                        $remarks = '-';
                    }

                    $o_sheet->setCellValue('C'.$i_row, $i_num++);
                    $o_sheet->setCellValue('D'.$i_row, $o_score->subject_name);
                    $o_sheet->setCellValue('H'.$i_row, $score_quiz);
                    $o_sheet->setCellValue('I'.$i_row, $score_grade);
                    $o_sheet->setCellValue('J'.$i_row, $score_grade_point);
                    $o_sheet->setCellValue('K'.$i_row, $o_score->curriculum_subject_credit);
                    $o_sheet->setCellValue('L'.$i_row, $score_merit);
                    $o_sheet->setCellValue('M'.$i_row, $score_absence);
                    $o_sheet->setCellValue('N'.$i_row, $remarks);
                    $i_row++;

                    array_push($a_sum_score_sum, intval($score_quiz));
                    array_push($a_sum_credit, intval($o_score->curriculum_subject_credit));
                    array_push($a_sum_merit, round($score_merit));
                }

            }

            $o_sheet->removeRow($i_row, 1);

            $average_score = round(array_sum($a_sum_score_sum) / count($a_sum_score_sum));
            $average_grade = $this->grades->get_grade($average_score);
            $average_grade_point = round($this->grades->get_grade_point($average_score), 2);
            $total_credit = array_sum($a_sum_score_sum);
            $total_merit = array_sum($a_sum_merit);
            $gpa = $this->grades->get_ipk($total_merit, $total_credit);

            $o_sheet->setCellValue('H'.$i_row, $average_score);
            $o_sheet->setCellValue('I'.$i_row, $average_grade);
            $o_sheet->setCellValue('J'.$i_row, $average_grade_point);
            $i_row++;
            $o_sheet->setCellValue('K'.$i_row, $total_credit);
            $o_sheet->setCellValue('L'.$i_row, $total_merit);
            $i_row++;
            $o_sheet->setCellValue('H'.$i_row, $gpa);

            $i_num = 1;
            $i_row += 4;
            if ($mba_score_extracurr_semester) {
                foreach ($mba_score_extracurr_semester as $o_score) {
                    $o_sheet->insertNewRowBefore($i_row + 1, 1);
                    $o_sheet->mergeCells('D' . $i_row . ':G' . $i_row);
                    $score_absence = round($o_score->score_absence, 2, PHP_ROUND_HALF_UP);
                    $score_quiz = intval(round($o_score->score_quiz, 0, PHP_ROUND_HALF_UP));
                    $score_grade = $this->grades->get_grade($score_quiz);
                    $score_grade_point = $this->grades->get_grade_point($score_quiz);
                    $score_merit = $this->grades->get_merit($o_score->curriculum_subject_credit, $score_grade_point);

                    if (($score_grade == 'F') OR ($score_absence > 25)) {
                        $remarks = 'FAIL';
                    } else {
                        $remarks = '-';
                    }

                    $o_sheet->setCellValue('C'.$i_row, $i_num++);
                    $o_sheet->setCellValue('D'.$i_row, $o_score->subject_name);
                    $o_sheet->setCellValue('H'.$i_row, $score_quiz);
                    $o_sheet->setCellValue('I'.$i_row, $score_grade);
                    $o_sheet->setCellValue('J'.$i_row, $score_grade_point);
                    $o_sheet->setCellValue('K'.$i_row, $o_score->curriculum_subject_credit);
                    $o_sheet->setCellValue('L'.$i_row, $score_merit);
                    $o_sheet->setCellValue('M'.$i_row, $score_absence);
                    $o_sheet->setCellValue('N'.$i_row, $remarks);
                    $i_row++;
                }

                $o_sheet->removeRow($i_row, 1);
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xls');
            $o_writer->save($s_file_full_path);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);
            
            shell_exec('/usr/bin/soffice --headless --convert-to pdf ' . $s_file_full_path . ' --outdir ' . $s_file_path);

            $s_filename = $s_file_name.'.pdf';

            $mba_return = array(
                'filename' => $s_filename,
                'file_path' => $s_file_path.$s_filename
            );
        }else{
            $mba_return = false;
        }

        return $mba_return;
    }

    public function generate_halfway_transcript($s_student_id, $s_academic_year_start, $s_semester_type_start, $s_academic_year_end, $s_semester_type_end, $b_ects = false, $b_short_semester_after = false, $b_fgrade = true, $b_asc_sign = false)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        if ($mbo_student_data) {
            $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
            $mba_asc = $this->General->get_where('ref_department', ['department_id' => 10]);
            $s_vice_rector_name = '';
            $s_vice_rector_email = '';
            if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
                $s_vice_rector_email = $mbo_vice_rector_data->employee_email;
            }
            $mba_asc_data = false;
            if ($mba_asc) {
                $mba_asc_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_asc[0]->employee_id));
            }
            
            $s_prodi_abbr = ($mbo_student_data->program_id == $this->a_programs['NI S1']) ? $mbo_student_data->study_program_ni_abbreviation : $mbo_student_data->study_program_abbreviation;
            $mbo_deans_data = $this->Pdm->get_personal_data_by_id($mbo_student_data->deans_id);
            $mbo_hod_data = $this->Pdm->get_personal_data_by_id($mbo_student_data->head_of_study_program_id);
            $s_hod_name = $this->Pdm->retrieve_title($mbo_student_data->head_of_study_program_id);
            $s_asc_name = ($mba_asc_data) ? $this->Pdm->retrieve_title($mba_asc_data[0]->personal_data_id) : '';
            $s_s_name = $this->Pdm->retrieve_title($mbo_student_data->head_of_study_program_id);
            $s_dean_name = $this->Pdm->retrieve_title($mbo_student_data->deans_id);

            if ($b_asc_sign) {
                $deptname = ($mba_asc) ? $mba_asc[0]->department_name : '';
                $s_header_sign_name = $s_asc_name;
                $s_header_sign_title = 'Head of '.$deptname;
            }
            else if (!is_null($mbo_student_data->head_of_study_program_id)) {
                $s_header_sign_name = $s_hod_name;
                $s_header_sign_title = 'Head of Study Program of '.$mbo_student_data->study_program_name;
            }
            else if (!is_null($mbo_student_data->deans_id)) {
                $s_header_sign_name = $s_dean_name;
                $s_header_sign_title = 'Dean of '.$mbo_student_data->faculty_name;
            }
            else {
                $s_header_sign_name = $s_vice_rector_name;
                $s_header_sign_title = 'Vice Rector Academic';
            }
            // $s_header_sign_name = (is_null($mbo_student_data->head_of_study_program_id)) ? $s_dean_name : $s_hod_name;
            // $s_header_sign_title = (is_null($mbo_student_data->head_of_study_program_id)) ? 'Vice Rector Academic' : 'Head of Study Program of '.$mbo_student_data->study_program_name;
            
            $a_param_data = array(
                'st.student_id' => $mbo_student_data->student_id,
                'sc.score_approval' => 'approved',
                'sc.score_display' => 'TRUE',
                // 'sc.semester_type_id !=' => '4',
                // 'sc.semester_type_id !=' => '6',
                'sc.semester_id !=' => '17',
                'curriculum_subject_credit !=' => '0',
                'sc.academic_year_id >=' => $s_academic_year_start,
                'sc.academic_year_id <=' => $s_academic_year_end
            );
            $a_filter_semester = array(
                'academic_year_start' => $s_academic_year_start,
                'semester_type_start' => $s_semester_type_start,
                'academic_year_end' => $s_academic_year_end,
                'semester_type_end' => $s_semester_type_end,
                // 'sc.score_display' => 'TRUE',
            );

            if (!$b_fgrade) {
                $a_param_data['sc.score_grade != '] = 'F';
            }

            $s_semester_range_start = $s_academic_year_start.'/'.$s_semester_type_start;
            $s_semester_range_end = $s_academic_year_end.'/'.$s_semester_type_end;

            $a_param_non_extracull = $a_param_data;
            $a_param_extracull = $a_param_data;

            $a_param_non_extracull['curriculum_subject_type !='] = 'extracurricular';
            $a_param_extracull['curriculum_subject_type'] = 'extracurricular';
            
            $mba_score_non_extracurricular_data = $this->Scm->get_score_data($a_param_non_extracull);
            
            // print('<pre>');var_dump($mba_score_non_extracurricular_data);exit;
            // $mba_score_non_extracurricular_data = modules::run('academic/score/clear_semester_score', $mba_score_non_extracurricular_data, $a_filter_semester);
            if (($a_filter_semester) AND ($mba_score_non_extracurricular_data)) {
                foreach ($mba_score_non_extracurricular_data as $key => $final) {
                    if (!$b_fgrade){
                        if (in_array($final->score_grade, ['F', ''])) {
                            unset($mba_score_non_extracurricular_data[$key]);
                        }
                    }

                    if (($a_filter_semester['semester_type_start'] !== null) AND ($a_filter_semester['semester_type_start'] == 2)) {
                        if (($a_filter_semester['academic_year_start'] == $final->academic_year_id) AND (in_array($final->semester_type_id, [1, 7]))) {
                            unset($mba_score_non_extracurricular_data[$key]);
                        }
                    }

                    if (($a_filter_semester['semester_type_end'] !== null) AND ($a_filter_semester['semester_type_end'] == 1)) {
                        // if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id == 2)) {
                        // if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id != 1)) {
                        if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND (!in_array($final->semester_type_id, [1, 7]))) {
                            unset($mba_score_non_extracurricular_data[$key]);
                        }
                    }
                }
            }

            $mba_score_extracurricular_data = $this->Scm->get_score_data($a_param_extracull);
            // $mba_score_extracurricular_data = modules::run('academic/score/clear_semester_score', $mba_score_extracurricular_data, $a_filter_semester);
            if (($a_filter_semester) AND ($mba_score_extracurricular_data)) {
                foreach ($mba_score_extracurricular_data as $key => $final) {
                    if (!$b_fgrade){
                        if (in_array($final->score_grade, ['F', ''])) {
                            unset($mba_score_non_extracurricular_data[$key]);
                        }
                    }

                    if (($a_filter_semester['semester_type_start'] !== null) AND ($a_filter_semester['semester_type_start'] == 2)) {
                        if (($a_filter_semester['academic_year_start'] == $final->academic_year_id) AND ($final->semester_type_id == 1)) {
                            unset($mba_score_extracurricular_data[$key]);
                        }
                    }

                    if (($a_filter_semester['semester_type_end'] !== null) AND ($a_filter_semester['semester_type_end'] == 1)) {
                        // if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id == 2)) {
                        if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id != 1)) {
                            unset($mba_score_extracurricular_data[$key]);
                        }
                    }
                }
            }
            // print('<pre>');var_dump($mba_score_extracurricular_data);exit;

            $a_trcr_param = [
                'st.student_id' => $mbo_student_data->student_id,
                'sc.score_approval' => 'approved',
                'sc.score_display' => 'TRUE',
                'sc.semester_id' => '18',
                'sc.semester_type_id' => '5',
                'curriculum_subject_credit !=' => '0'
            ];

            $a_trcr_param_non_extracull = $a_trcr_param;
            $a_trcr_param_extracull = $a_trcr_param;

            $a_trcr_param_non_extracull['curriculum_subject_type !='] = 'extracurricular';
            $a_trcr_param_extracull['curriculum_subject_type'] = 'extracurricular';

            if (!$b_fgrade) {
                $a_trcr_param_non_extracull['sc.score_grade != '] = 'F';
                $a_trcr_param_extracull['sc.score_grade != '] = 'F';
            }

            $mba_score_trcr_non_excul = $this->Scm->get_score_data($a_trcr_param_non_extracull);
            $mba_score_trcr_excul = $this->Scm->get_score_data($a_trcr_param_extracull);

            if ($mba_score_non_extracurricular_data AND $mba_score_trcr_non_excul) {
                $mba_score_non_extracurricular_data = array_merge($mba_score_non_extracurricular_data, $mba_score_trcr_non_excul);
            }

            // print('<pre>');var_dump($mba_score_trcr_excul);exit;
            if ($mba_score_extracurricular_data AND $mba_score_trcr_excul) {
                $mba_score_extracurricular_data = array_merge($mba_score_extracurricular_data, $mba_score_trcr_excul);
            }

            foreach ($mba_score_extracurricular_data as $key => $score) {
                $score->semester_key = ($score->semester_id == '18') ? '0' : $score->semester_id;
            }
            $mba_score_extracurricular_data = array_values($mba_score_extracurricular_data);
            $semester_excull = array_column($mba_score_extracurricular_data, 'semester_key');
            $subject_name_excull = array_column($mba_score_extracurricular_data, 'subject_name');
            array_multisort($semester_excull, SORT_ASC, $subject_name_excull, SORT_ASC, $mba_score_extracurricular_data);

            $title = "Halfway Transcript Report " . $mbo_student_data->personal_data_name;
            $s_halfway_path = APPPATH.'uploads/academic/transcript-halfway/'.$mbo_student_data->academic_year_id.'/'.$s_prodi_abbr.'/';
            if ($b_ects) {
                $s_template_path = APPPATH.'uploads/templates/Transcript_temp template-v2-ECTS.xls';
                $s_file = $mbo_student_data->student_number.'_Halfway_'.str_replace(' ', '-', $mbo_student_data->personal_data_name).'-ECTS';
            }else{
                $s_template_path = APPPATH.'uploads/templates/Transcript_temp template-v2.xls';
                $s_file = $mbo_student_data->student_number.'_Halfway_'.str_replace(' ', '-', $mbo_student_data->personal_data_name);
            }

            if ((($mba_asc_data) AND ($mba_asc_data[0]->personal_data_id == $this->session->userdata('user')))) {
                if ($b_ects) {
                    $s_template_path = APPPATH.'uploads/templates/Transcript template-v2-ECTS.xls';
                }else{
                    $s_template_path = APPPATH.'uploads/templates/Transcript template-v2.xls';
                }
            }

            $s_file = str_replace("'", "", $s_file);
            $s_filename = $s_file.'.xls';
            
            if(!file_exists($s_halfway_path)){
                mkdir($s_halfway_path, 0777, TRUE);
            }

            $s_digital_sign = md5($mbo_student_data->student_number . microtime());
            $save_digital_sign = $this->Stm->update_student_data(array(
                'student_transcript_token' => $s_digital_sign
            ), $mbo_student_data->student_id);

            $save_sign = $this->Stm->save_sign_data([
                'student_id' => $s_student_id,
                'document_type' => 'transcript_halfway', 
                'document_sign' => $s_digital_sign
            ]);

            if ($save_digital_sign AND $save_sign) {
                $o_spreadsheet = IOFactory::load($s_template_path);
                $o_sheet = $o_spreadsheet->getActiveSheet();
                $o_spreadsheet->getProperties()
                    ->setTitle($title)
                    ->setCreator("IULI Academic Services")
                    ->setLastModifiedBy("IULI Academic Services")
                    ->setCategory("Transcript Report");
                $o_sheet = $o_spreadsheet->setActiveSheetIndexByName("Transcript");
                // $o_sheet->setCellValue('B18', ': TRANSCRIPT OF ACADEMIC RECORD (Semester '.$s_semester_range_start.' - '.$s_semester_range_end.')');
                $o_sheet->setCellValue('B18', 'TRANSCRIPT OF ACADEMIC RECORD');
                $o_sheet->setCellValue('F11', ': '.$mbo_student_data->personal_data_name);
                $o_sheet->setCellValue('F12', ': '.$mbo_student_data->personal_data_place_of_birth.', '.date('d-m-Y', strtotime($mbo_student_data->personal_data_date_of_birth)));
                $o_sheet->setCellValue('F13', ': '.$mbo_student_data->student_number);
                $o_sheet->setCellValue('F14', ': '.$mbo_student_data->academic_year_id);

                $s_head_cols = ($b_ects) ? 'T' : 'S';
                $o_sheet->setCellValue($s_head_cols.'11', ': '.date('j F Y', time()));
                // $o_sheet->setCellValue($s_head_cols.'12', ': '.$mbo_student_data->faculty_name);
                $o_sheet->setCellValue($s_head_cols.'13', ': Bachelor');
                $o_sheet->setCellValue($s_head_cols.'14', ': '.$s_digital_sign);
                $o_sheet->setCellValue($s_head_cols.'15', '');
                $o_sheet->setCellValue($s_head_cols.'12', ': '.$mbo_student_data->study_program_name);

                // without deans
                $s_deans_cols = ($b_ects) ? 'Q' : 'P';
                $o_sheet->setCellValue($s_deans_cols.'12', 'Department');
                $o_sheet->setCellValue($s_deans_cols.'13', 'Program');
                $o_sheet->setCellValue($s_deans_cols.'14', 'Digital Sign');
                $o_sheet->setCellValue($s_deans_cols.'15', '');

                $no = 0;
                $i_row_start = 22;
                $a_subject_name_printed = [];
                $a_score_non_extraculicular_temp = [];
                if ($mba_score_non_extracurricular_data) {
                    foreach ($mba_score_non_extracurricular_data as $key => $score) {
                        if ($this->Scm->get_good_grades($score->subject_name, $score->student_id, $score->score_sum) == false) {
                            unset($mba_score_non_extracurricular_data[$key]);
                        }
                        else if (in_array($score->subject_name, $a_subject_name_printed)) {
                            unset($mba_score_non_extracurricular_data[$key]);
                        }
                        else {
                            array_push($a_subject_name_printed, $score->subject_name);
                            $score->semester_key = ($score->semester_id == '18') ? '0' : $score->semester_id;
                        }
                    }

                    $mba_score_non_extracurricular_data = array_values($mba_score_non_extracurricular_data);
                    $semester = array_column($mba_score_non_extracurricular_data, 'semester_key');
                    $subject_name = array_column($mba_score_non_extracurricular_data, 'subject_name');
                    array_multisort($semester, SORT_ASC, $subject_name, SORT_ASC, $mba_score_non_extracurricular_data);
                    // print('<pre>');var_dump($semester);exit;
                    // $mba_score_non_extracurricular_data = array_multisort($price, SORT_ASC, $inventory);

                    foreach ($mba_score_non_extracurricular_data as $key => $score) {
                        $b_move = false;
                        if(strpos(strtolower($score->subject_name), 'internship') !== false){
                            $b_move = true;
                        }else if(strpos(strtolower($score->subject_name), 'thesis') !== false){
                            $b_move = true;
                        }else if(strpos(strtolower($score->subject_name), 'research project') !== false){
                            $b_move = true;
                        }

                        if ($b_move) {
                            array_push($a_score_non_extraculicular_temp, $score);
                            unset($mba_score_non_extracurricular_data[$key]);
                        }
                    }
                    if (count($a_score_non_extraculicular_temp) > 0) {
                        $mba_score_non_extracurricular_data = array_merge($mba_score_non_extracurricular_data, $a_score_non_extraculicular_temp);
                    }
                    $mba_score_non_extracurricular_data = array_values($mba_score_non_extracurricular_data);
                    
                    $i_line = round(count($mba_score_non_extracurricular_data)/2, 0, PHP_ROUND_HALF_UP);
                    $a_score_sum = array();
                    $a_sks = array();
                    $a_merit = array();
                    $a_score_ects = array();

                    $i_start_row = $i_row_start;

                    for ($i=0; $i < $i_line; $i++) { 
                        $o_sheet->insertNewRowBefore($i_start_row + 1, 1);
                        $o_sheet->mergeCells('D'.$i_start_row.':'.'H'.$i_start_row);
                        if ($b_ects) {
                            // $i_ects_score = $this->grades->get_score_ects($mba_score_non_extracurricular_data[$i]->curriculum_subject_credit, $mba_score_non_extracurricular_data[$i]->score_grade_point);
                            $i_ects_score = $this->grades->get_ects_score($mba_score_non_extracurricular_data[$i]->curriculum_subject_credit, $mba_score_non_extracurricular_data[$i]->subject_name);

                            $subject_name = strtolower($mba_score_non_extracurricular_data[$i]->subject_name);
                            if(strpos($subject_name, 'research semester') !== false){
                                $i_ects_score = $this->grades->get_ects_score($mba_score_non_extracurricular_data[$i]->curriculum_subject_credit, $mba_score_non_extracurricular_data[$i]->subject_name, $mba_score_non_extracurricular_data[$i]->score_ects);
                            }

                            $o_sheet->mergeCells('Q'.$i_start_row.':'.'U'.$i_start_row);
                            $o_sheet->setCellValue('N'.$i_start_row, $i_ects_score);
                            array_push($a_score_ects, $i_ects_score);
                        }else{
                            $o_sheet->mergeCells('P'.$i_start_row.':'.'T'.$i_start_row);
                        }

                        $score_sum = intval(round($mba_score_non_extracurricular_data[$i]->score_sum, 0, PHP_ROUND_HALF_UP));
                        $score_grade = $this->grades->get_grade($score_sum);
                        $score_grade_point = $this->grades->get_grade_point($score_sum);
                        $score_merit = $this->grades->get_merit($mba_score_non_extracurricular_data[$i]->curriculum_subject_credit, $score_grade_point);

                        $o_sheet->setCellValue('C'.$i_start_row, ++$no)
                            ->setCellValue('D'.$i_start_row, $mba_score_non_extracurricular_data[$i]->subject_name)
                            ->setCellValue('I'.$i_start_row, $score_sum)
                            ->setCellValue('J'.$i_start_row, $score_grade)
                            ->setCellValue('K'.$i_start_row, $score_grade_point)
                            ->setCellValue('L'.$i_start_row, $mba_score_non_extracurricular_data[$i]->curriculum_subject_credit)
                            ->setCellValue('M'.$i_start_row, $score_merit);

                        array_push($a_score_sum, $score_sum);
                        array_push($a_sks, $mba_score_non_extracurricular_data[$i]->curriculum_subject_credit);
                        array_push($a_merit, $score_merit);
                        $i_start_row++;
                    }
                    // exit;

                    $i_row_start = $i_start_row;
                    $o_sheet->removeRow($i_start_row, 1);
                    $i_start_row = 22;

                    for ($i=$i_line; $i < count($mba_score_non_extracurricular_data); $i++) {
                        $a_cols = ($b_ects) ? array('P', 'Q', 'V', 'W', 'X', 'Y', 'Z', 'AA') : array('O', 'P', 'U', 'V', 'W', 'X', 'Y');
                        
                        $score_sum = intval(round($mba_score_non_extracurricular_data[$i]->score_sum, 0, PHP_ROUND_HALF_UP));
                        $score_grade = $this->grades->get_grade($score_sum);
                        $score_grade_point = $this->grades->get_grade_point($score_sum);
                        $score_merit = $this->grades->get_merit($mba_score_non_extracurricular_data[$i]->curriculum_subject_credit, $score_grade_point);

                        // if (($s_student_id == '3338d11d-25b8-42fc-9a55-625e6897828e') AND ($mba_score_non_extracurricular_data[$i]->subject_name == 'Introduction to Biotechnology')) {
                        //     print($)
                        // }

                        $o_sheet->setCellValue($a_cols[0].$i_start_row, ++$no)
                            ->setCellValue($a_cols[1].$i_start_row, $mba_score_non_extracurricular_data[$i]->subject_name)
                            ->setCellValue($a_cols[2].$i_start_row, $score_sum)
                            ->setCellValue($a_cols[3].$i_start_row, $score_grade)
                            ->setCellValue($a_cols[4].$i_start_row, $score_grade_point)
                            ->setCellValue($a_cols[5].$i_start_row, $mba_score_non_extracurricular_data[$i]->curriculum_subject_credit)
                            ->setCellValue($a_cols[6].$i_start_row, $score_merit);
                        if ($b_ects) {
                            $i_ects_score = $this->grades->get_ects_score($mba_score_non_extracurricular_data[$i]->curriculum_subject_credit, $mba_score_non_extracurricular_data[$i]->subject_name);
                            // $i_ects_score = $this->grades->get_ects_score($mba_score_non_extracurricular_data[$i]->curriculum_subject_credit, $mba_score_non_extracurricular_data[$i]->subject_name, $mba_score_non_extracurricular_data[$i]->score_ects);
                            $o_sheet->setCellValue($a_cols[7].$i_start_row, $i_ects_score);
                            array_push($a_score_ects, $i_ects_score);
                        }

                        array_push($a_score_sum, $score_sum);
                        array_push($a_sks, $mba_score_non_extracurricular_data[$i]->curriculum_subject_credit);
                        array_push($a_merit, $score_merit);
                        $i_start_row++;
                    }
                }
                
                $i_score_sum_average = array_sum($a_score_sum) / count($a_score_sum);
                $s_grade_average = $this->grades->get_grade($i_score_sum_average);
                $i_total_credit = array_sum($a_sks);
                $i_total_merit = array_sum($a_merit);
                $i_gpa = $this->grades->get_ipk($i_total_merit, $i_total_credit);

                $i_row_start++;
                $o_sheet->setCellValue((($b_ects) ? 'V' : 'U').$i_row_start, $i_score_sum_average)
                    ->setCellValue((($b_ects) ? 'W' : 'V').$i_row_start, $s_grade_average)
                    ->setCellValue((($b_ects) ? 'Y' : 'X').intval($i_row_start+1), $i_total_credit)
                    ->setCellValue((($b_ects) ? 'Z' : 'Y').intval($i_row_start+1), $i_total_merit);
                if ($b_ects) {
                    $i_total_ects = array_sum($a_score_ects);
                    $o_sheet->setCellValue('AA'.intval($i_row_start+1), $i_total_ects);
                }
                $o_sheet->setCellValue('I'.intval($i_row_start+1), $i_gpa);

                $i_row_start += 5;
                if ($mba_score_extracurricular_data) {
                    $no = 0;
                    $a_subject_ext_name_printed = [];
                    foreach ($mba_score_extracurricular_data as $score_extra) {
                        if ($this->Scm->get_good_grades($score_extra->subject_name, $score_extra->student_id, $score_extra->score_sum)) {
                            $score_sum = intval(round($score_extra->score_sum, 0, PHP_ROUND_HALF_UP));
                            $score_grade = $this->grades->get_grade($score_sum);
                            $score_grade_point = $this->grades->get_grade_point($score_sum);
                            $score_merit = $this->grades->get_merit($score_extra->curriculum_subject_credit, $score_grade_point);
                            
                            $o_sheet->insertNewRowBefore($i_row_start + 1, 1);
                            $o_sheet->mergeCells('D'.$i_row_start.':'.'H'.$i_row_start);
                            $o_sheet->setCellValue('C'.$i_row_start, ++$no)
                                ->setCellValue('D'.$i_row_start, $score_extra->subject_name)
                                ->setCellValue('I'.$i_row_start, $score_sum)
                                ->setCellValue('J'.$i_row_start, $score_grade)
                                ->setCellValue('K'.$i_row_start, $score_grade_point)
                                ->setCellValue('L'.$i_row_start, $score_extra->curriculum_subject_credit)
                                ->setCellValue('M'.$i_row_start, $score_merit);

                            if ($b_ects) {
                                // $i_extra_ects_score = $this->grades->get_score_ects($score_extra->curriculum_subject_credit, $score_grade_point);
                                $i_extra_ects_score = $this->grades->get_ects_score($score_extra->curriculum_subject_credit, $score_extra->subject_name);
                                $o_sheet->setCellValue('N'.$i_row_start, $i_extra_ects_score);
                            }
                            $i_row_start++;
                        }
                    }
                    $o_sheet->removeRow($i_row_start, 1);
                }
                $i_row_start++;
                $row_sign_title = $i_row_start;
                $o_sheet->setCellValue((($b_ects) ? 'P' : 'O').$row_sign_title, $s_header_sign_title);
                $i_row_start += 6;
                $row_sign_name = $i_row_start;
                $o_sheet->setCellValue((($b_ects) ? 'P' : 'O').$row_sign_name, $s_header_sign_name);

                if ((($mba_asc_data) AND ($mba_asc_data[0]->personal_data_id == $this->session->userdata('user')))) {}else {
                    $o_sheet->setCellValue('B18', 'Not for Distribution');
                    $o_sheet->setCellValue($s_head_cols.'14', '');
                    $o_sheet->setCellValue($s_deans_cols.'14', '');
                    $o_sheet->setCellValue((($b_ects) ? 'P' : 'O').$row_sign_title, '');
                    $o_sheet->setCellValue((($b_ects) ? 'P' : 'O').$row_sign_name, '');

                    // $o_sheet->removeRow(1, 6);
                    $bilder_array = $o_sheet->getDrawingCollection();
                    $bilder_array_copy = $bilder_array->getArrayCopy();
                    $i = 0;
                    foreach ($bilder_array_copy as $drawing) {
                        $coordinates=$drawing->getCoordinates();
                        if ($coordinates==$cell) {
                            // Delete Image from Array/Cell
                            unset($bilder_array_copy[$i]);  
                        }
                        $i++;
                    }
                    // Reorder and exchange array
                    $bilder_array_copy = array_values($bilder_array_copy);
                    $bilder_array->exchangeArray($bilder_array_copy);

                }

                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xls');
                $o_writer->save($s_halfway_path.$s_filename);
                $o_spreadsheet->disconnectWorksheets();
                unset($o_spreadsheet);
                
                shell_exec('/usr/bin/soffice --headless --convert-to pdf ' . $s_halfway_path.$s_filename . ' --outdir ' . $s_halfway_path);

                $s_filename = $s_file.'.pdf';

                $mba_return = array(
                    'filename' => $s_filename,
                    'file_path' => $s_halfway_path.$s_filename
                );
            }else{
                $mba_return = false;
            }
        }else{
            $mba_return = false;
        }

        if ($mba_return) {
            modules::run('messaging/send_email', 'employee@company.ac.id', 'Generate Halfway Transcript', $this->session->userdata('name').' has generate halfway transcript.', 'employee@company.ac.id', false, $mba_return['file_path']);
        }

        return $mba_return;
    }

    public function generate_cummulative_gpa(
        $s_student_batch,
        $b_passed_deffence = false,
        $s_study_program_id,
        $a_student_status = false,
        $b_last_semester = false,
        $b_last_short_semester = false,
        $b_last_repetition = true
    )
    {
        $mba_semester_active = $this->Smm->get_active_semester();
        $i_short_semester_blocked = 0;

        if (!$b_last_semester) {
            $i_semester_selected = $mba_semester_active->academic_year_id.$mba_semester_active->semester_type_id;
            $a_semester_selected = [
                'academic_year_id' => $mba_semester_active->academic_year_id,
                'semester_type_id' => $mba_semester_active->semester_type_id
            ];
        }
        else{
            $mba_semester_settings = $this->Smm->get_semester_setting();
            $index_semester = count($mba_semester_settings) - 1;
            foreach ($mba_semester_settings as $key => $o_semester) {
                if ($o_semester->semester_status == 'active') {
                    $index_semester = $key + 1;
                    break;
                }
            }
            // print('<pre>');
            // var_dump($mba_semester_settings[$index_semester]);exit;

            $i_semester_selected = $mba_semester_settings[$index_semester]->academic_year_id.$mba_semester_settings[$index_semester]->semester_type_id;
            $a_semester_selected = [
                'academic_year_id' => $mba_semester_settings[$index_semester]->academic_year_id,
                'semester_type_id' => $mba_semester_settings[$index_semester]->semester_type_id
            ];
            
            if ($b_last_short_semester) {
                $i_semester_selected = ($mba_semester_settings[$index_semester]->semester_type_id == 1) ? $mba_semester_settings[$index_semester]->academic_year_id.'7' : $mba_semester_settings[$index_semester]->academic_year_id.'8';
                $a_semester_selected = [
                    'academic_year_id' => $mba_semester_settings[$index_semester]->academic_year_id,
                    'semester_type_id' => ($mba_semester_settings[$index_semester]->semester_type_id == 1) ? 7 : 8
                ];
            }
        }
        
        $a_filter = array(
            'ds.academic_year_id' => $s_student_batch,
            'ds.study_program_id' => $s_study_program_id
        );

        $s_text_header = 'MAHASISWA PRODI ';
        $s_file_name = 'GPA_Recapitulation_';

        if (($s_study_program_id == 'all') OR ($s_study_program_id == '')) {
            unset($a_filter['ds.study_program_id']);
            $s_text_header .= '-';
            $s_file_name .= 'All_Semester';
        }else {
            $mbo_prodi_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
            $s_text_header .= strtoupper($mbo_prodi_data->study_program_name);
            $s_file_name .= strtoupper($mbo_prodi_data->study_program_abbreviation);
        }

        $s_text_header .= ' ANGKATAN ';

        if (($s_student_batch == 'all') OR ($s_student_batch == '')) {
            unset($a_filter['ds.academic_year_id']);
            $s_text_header .= '_-';
        }else{
            $s_text_header .= ' '.$s_student_batch.'/'.(intval($s_student_batch) + 1);
            $s_file_name .= '_'.$s_student_batch.'-'.(intval($s_student_batch) + 1);
        }

        $s_template_path = APPPATH.'uploads/templates/template-rekap-ipsipk-v2.xls';
        if ($b_passed_deffence) {
            $a_filter['ds.student_mark_completed_defense'] = 1;
        }
        
        $a_filter = (count($a_filter) > 0) ? $a_filter : false;

        $mba_student_data = $this->Stm->get_student_list_data($a_filter, $a_student_status, array(
            'faculty_name' => 'ASC',
            'study_program_name' => 'ASC',
            'personal_data_name' => 'ASC'
        ));

        
        if ($mba_student_data) {
            $s_filepath = APPPATH.'/uploads/academic/'.$mba_semester_active->academic_year_id.$mba_semester_active->semester_type_id.'/cummulative_gpa/';

            if(!file_exists($s_filepath)){
                mkdir($s_filepath, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_text_header)
                ->setCreator("IULI Academic Services")
                ->setLastModifiedBy("IULI Academic Services")
                ->setCategory("Cummulative GPA");
            $o_sheet = $o_spreadsheet->setActiveSheetIndexByName("Template IPK");
            $o_sheet->setCellValue('A1', 'REKAPITULASI IPS dan IPK '.$i_semester_selected);
            $o_sheet->setCellValue('A2', $s_text_header);

            $i_row = 4;
            $i_number_counter = 1;

            $a_gpa_semester_data = array();
            $a_gpa_cummulative_data = array();
            $a_absence_data = array();

            foreach ($mba_student_data as $o_student) {
                $mba_student_semester = $this->Smm->get_semester_student_personal_data(array(
                    // 'st.personal_data_id' => $o_student->personal_data_id
                    'dss.student_id' => $o_student->student_id
                ), array(1,2,3,7,8));

                if ($mba_student_semester) {
                    $a_total_semester_absence = array();
                    $has_repetition = false;

                    $o_sheet->setCellValue('A'.$i_row, $i_number_counter);
                    $o_sheet->setCellValue('B'.$i_row, strtoupper($o_student->personal_data_name));
                    $o_sheet->setCellValue('C'.$i_row, strtoupper($o_student->student_number));
                    
                    foreach($mba_student_semester AS $key => $o_student_semester) {
                        $i_semester_student = $o_student_semester->semester_academic_year_id.$o_student_semester->semester_semester_type_id;
                        
                        $mbo_student_semester_data = $this->Stm->get_student_by_id($o_student_semester->student_id);
                        
                        $b_print = true;
                        if ($b_last_semester) {
                            if (
                                ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND
                                ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])
                            ) {
                                $s_gpa_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'gpa', $b_last_repetition);
                                $s_credit_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'credit', $b_last_repetition);
                                $s_average_absence_ = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'average_absence', $b_last_repetition);
                                $s_average_absence_semester = 100 - $s_average_absence_;
                            }else{
                                $b_print = false;
                            }
                            
                        }else{
                            $s_gpa_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id);
                            $s_credit_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'credit');
                            $s_average_absence_ = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'average_absence');
                            $s_average_absence_semester = 100 - $s_average_absence_;
                        }

                        if ((!$b_last_semester) AND ($b_print)) {
                            array_push($a_total_semester_absence, $s_average_absence_);

                            array_push($a_gpa_semester_data, $s_gpa_semester);
                            array_push($a_absence_data, $s_average_absence_);

                            $o_sheet->setCellValue('D'.$i_row, strtoupper($mbo_student_semester_data->study_program_name.' - '.$mbo_student_semester_data->academic_year_id));
                            $o_sheet->setCellValue('E'.$i_row, $o_student_semester->semester_academic_year_id.$o_student_semester->semester_semester_type_id);
                            $o_sheet->setCellValue('F'.$i_row, $o_student_semester->semester_type_name);
                            $o_sheet->setCellValue('G'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));
                            $o_sheet->setCellValue('I'.$i_row, round($s_average_absence_semester, 2, PHP_ROUND_HALF_UP));
                            $o_sheet->setCellValue('K'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                        }

                        if (
                            ($b_print) AND
                            ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND
                            ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])
                        ) {
                            if ($b_last_semester) {
                                array_push($a_total_semester_absence, $s_average_absence_);
    
                                array_push($a_gpa_semester_data, $s_gpa_semester);
                                array_push($a_absence_data, $s_average_absence_);

                                $o_sheet->setCellValue('D'.$i_row, strtoupper($mbo_student_semester_data->study_program_abbreviation.' - '.$mbo_student_semester_data->academic_year_id));
                                $o_sheet->setCellValue('E'.$i_row, $o_student_semester->semester_academic_year_id.$o_student_semester->semester_semester_type_id);
                                $o_sheet->setCellValue('F'.$i_row, $o_student_semester->semester_type_name);
                                $o_sheet->setCellValue('G'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));
                                $o_sheet->setCellValue('I'.$i_row, round($s_average_absence_semester, 2, PHP_ROUND_HALF_UP));
                                $o_sheet->setCellValue('K'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                            }

                        }

                        $a_param_score = array(
                            // 'st.personal_data_id' => $o_student->personal_data_id,
                            'sc.student_id' => $o_student->student_id,
                            'sc.score_approval' => 'approved',
                            'sc.score_display' => 'TRUE',
                            'sc.semester_id !=' => '17',
                            'curriculum_subject_credit !=' => '0',
                            'sc.academic_year_id >=' => $mba_student_semester[0]->semester_academic_year_id,
                            'sc.academic_year_id <=' => $o_student_semester->semester_academic_year_id,
                            'curriculum_subject_type !=' => 'extracurricular'
                        );

                        // if (!$b_last_repetition) {
                        //     $a_param_score['score_mark_for_repetition'] = NULL;
                        // }
                        
                        $a_filter_semester = array(
                            'academic_year_start' => $mba_student_semester[0]->semester_academic_year_id,
                            'semester_type_start' => $mba_student_semester[0]->semester_semester_type_id,
                            'academic_year_end' => $a_semester_selected['academic_year_id'],
                            'semester_type_end' => $a_semester_selected['semester_type_id']
                        );

                        $mba_score_data = $this->Scm->get_score_data($a_param_score, [1,2,3,7,8]);
                        $mba_transfer_credit = $this->Scm->get_score_data([
                            'sc.student_id' => $o_student->student_id
                        ], [5]);

                        // $mba_score_data = modules::run('academic/score/clear_semester_score', $mba_score_data, $a_filter_semester);
                        if (($a_filter_semester) AND ($mba_score_data)) {
                            foreach ($mba_score_data as $key => $final) {
                                if (($a_filter_semester['semester_type_start'] !== null) AND ($a_filter_semester['semester_type_start'] == 2)) {
                                    if (($a_filter_semester['academic_year_start'] == $final->academic_year_id) AND ($final->semester_type_id == 1)) {
                                        unset($mba_score_data[$key]);
                                    }
                                }

                                if (($a_filter_semester['semester_type_end'] !== null) AND ($a_filter_semester['semester_type_end'] == 1)) {
                                    // if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id == 2)) {
                                    if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id != 1)) {
                                        unset($mba_score_data[$key]);
                                    }
                                }
                            }
                        }

                        if ($mba_transfer_credit) {
                            $mba_score_data = array_merge($mba_score_data, $mba_transfer_credit);
                        }

                        $i_sum_credit = 0;
                        $i_sum_merit = 0;
                        $s_sum_absence_student = array_sum($a_total_semester_absence);
                        $s_average_absence_student = (count($a_total_semester_absence) > 0) ? ($s_sum_absence_student / count($a_total_semester_absence)) : 0;
                        $s_average_absence_student = 100 - $s_average_absence_student;

                        if ($mba_score_data) {
                            $a_credit = array();
                            $a_merit = array();

                            foreach ($mba_score_data as $score) {
                                if ($this->Scm->get_good_grades($score->subject_name, $score->student_id, $score->score_sum)) {
                                    // if (!in_array($score->subject_name, $a_subject_name_fill)) {
                                        if(!is_null($score->score_repetition_exam)){
                                            $has_repetition = true;
                                        }
    
                                        $score_sum = intval(round($score->score_sum, 0, PHP_ROUND_HALF_UP));
                                        $score_grade_point = $this->grades->get_grade_point($score_sum);
                                        $score_merit = $this->grades->get_merit($score->curriculum_subject_credit, $score_grade_point);
                                        array_push($a_credit, $score->curriculum_subject_credit);
                                        array_push($a_merit, $score_merit);

                                    //     array_push($a_subject_name_fill, $o_score->subject_name);
                                    // }
                                }
                            }

                            $i_sum_credit = array_sum($a_credit);
                            $i_sum_merit = array_sum($a_merit);
                        }

                        $s_gpa_cummulative = $this->grades->get_ipk($i_sum_merit, $i_sum_credit);
                        array_push($a_gpa_cummulative_data, $s_gpa_cummulative);

                        $s_predicate = '-';
                        $b_has_repeat_subject = modules::run('academic/score/has_repeat_subject', $o_student->student_id);
                        if(!$b_has_repeat_subject) {
                            $s_predicate = $this->grades->get_graduation_predicate(round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        }

                        // if (!$has_repetition) {
                        //     $s_predicate = $this->grades->get_graduation_predicate(round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        // }
                        
                        $o_sheet->setCellValue('H'.$i_row, round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('J'.$i_row, round($s_average_absence_student, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('L'.$i_row, round($i_sum_credit, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('P'.$i_row, $s_predicate);

                        if (!$b_last_semester) {
                            $i_row++;
                            $o_sheet->insertNewRowBefore($i_row, 1);
                        }
                        
                    }

                    $i_number_counter++;
                    $i_row++;
                    $o_sheet->insertNewRowBefore($i_row, 1);
                }
            }

            $o_sheet->removeRow($i_row, 1);
            $i_row += 2;

            $s_max_gpa_semester_data = max($a_gpa_semester_data);
            $s_max_gpa_cummulative_data = max($a_gpa_cummulative_data);

            $s_min_gpa_semester_data = min($a_gpa_semester_data);
            $s_min_gpa_cummulative_data = min($a_gpa_cummulative_data);

            $s_average_gpa_semester = (count($a_gpa_semester_data) > 0) ? (array_sum($a_gpa_semester_data) / count($a_gpa_semester_data)) : 0;
            $s_average_gpa_cummulative = (count($a_gpa_cummulative_data) > 0) ? (array_sum($a_gpa_cummulative_data) / count($a_gpa_cummulative_data)) : 0;
            $s_average_absence = 100 - ((count($a_absence_data) > 0) ? (array_sum($a_absence_data) / count($a_absence_data)) : 0);

            $o_sheet->setCellValue('G'.$i_row, round($s_average_gpa_semester, 2, PHP_ROUND_HALF_UP));
            $o_sheet->setCellValue('H'.$i_row, round($s_average_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
            $o_sheet->setCellValue('I'.$i_row, round($s_average_absence, 2, PHP_ROUND_HALF_UP));
            $i_row++;
            $o_sheet->setCellValue('G'.$i_row, round($s_max_gpa_semester_data, 2, PHP_ROUND_HALF_UP));
            $o_sheet->setCellValue('H'.$i_row, round($s_max_gpa_cummulative_data, 2, PHP_ROUND_HALF_UP));
            $i_row++;
            $o_sheet->setCellValue('G'.$i_row, round($s_min_gpa_semester_data, 2, PHP_ROUND_HALF_UP));
            $o_sheet->setCellValue('H'.$i_row, round($s_min_gpa_cummulative_data, 2, PHP_ROUND_HALF_UP));

            if ($b_last_semester) {
                $o_sheet->removeColumn('O');
                $o_sheet->removeColumn('N');
                $o_sheet->removeColumn('M');
                $o_sheet->removeColumn('J');
                $o_sheet->removeColumn('I');
                $o_sheet->removeColumn('F');
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_filepath.$s_file_name.'.xlsx');
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            return $s_file_name.'.xlsx';
        }else{
            return false;
        }
    }

    public function test_generate_cummulative_gpa2()
    {
        $a_prodi_filter = ['226f91bc-81cd-11e9-bdfc-5254005d90f6','903eb8ee-159e-406b-8f7e-38d63a961ea4','12c9ec75-af4a-46a1-ae12-b1ba4bf75c89','6266e096-63ad-4b77-82b0-17216155a70e','c395b273-5acb-41c6-9d44-bccabd93d312','417bc155-81cd-11e9-bdfc-5254005d90f6','7da8cd1e-8f0e-41f4-89dd-361c29801087','01a781d9-81cd-11e9-bdfc-5254005d90f6','6ce5bc8b-10f5-456d-855d-aef18dc641f4','2f5ecc6d-4a67-47f8-80aa-9c3ef8e9b8d8','e0c165f7-a2f8-4372-aa6b-20e3dbc61f32','7ca09ca3-ef1a-4c08-83b7-5d1bb63a633b','208c8d88-2560-4640-a1b2-bfd42b0e7c16','46675bdb-83af-47e7-bef6-07566108fd21','ed375a1a-81cc-11e9-bdfc-5254005d90f6'];
        // $a_student_gpa_result = modules::run('download/excel_download/generate_cummulative_gpa2', $s_batch, $passed_defense, $prodi_filter, $mba_student_status, true, $b_short_semester, $b_repeat, $s_academic_year_id, $s_semester_type_id, $b_feeder_check);
        $test = $this->generate_cummulative_gpa2('all', false, $a_prodi_filter, ['active', 'inactive', 'onleave', 'graduated'], true, false, true, 2021, 7, true);
        print('<pre>');var_dump($test);exit;
    }

    public function show_error()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        var_dump($woows);exit;
    }

    public function test_generate_cummulative_gpa_feeder()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $s_batch = 'all';
        $passed_defense = false;
        $prodi_filter = [
            '01a781d9-81cd-11e9-bdfc-5254005d90f6',
            // 'e0c165f7-a2f8-4372-aa6b-20e3dbc61f32',
            // 'ed375a1a-81cc-11e9-bdfc-5254005d90f6'
        ];
        $mba_student_status = ['active', 'inactive', 'onleave', 'graduated', 'resign'];
        $b_semester_selected = true;
        $b_last_short_semester = false;
        $b_last_repetition = true;
        $s_academic_year_id = 2022;
        $s_semester_type_id = 1;
        $b_feeder_check = true;
        print($testgabut);exit;
        // print('ada');exit;

        $result = $this->generate_cummulative_gpa_feeder($s_batch, $passed_defense, $prodi_filter, $mba_student_status, $b_semester_selected, $b_last_short_semester, $b_last_repetition, $s_academic_year_id, $s_semester_type_id, $b_feeder_check);
        print('<pre>aaaaaaaa');var_dump($result);exit;
    }

    public function generate_cummulative_gpa_feeder(
        $s_student_batch,
        $b_passed_deffence = false,
        $a_study_program_id,
        $a_student_status = false,
        $b_semester_selected = false, // $b_last_semester = false,
        $b_last_short_semester = false,
        $b_last_repetition = true,
        $s_academic_year_id = false,
        $s_semester_type_id = false,
        $b_feeder_check = false
    )
    {
        $mba_semester_active = $this->Smm->get_active_semester();
        $i_short_semester_blocked = 0;
        
        if ($s_academic_year_id AND $s_semester_type_id) {
            $i_semester_selected = (in_array($s_semester_type_id, [7,8])) ? $s_academic_year_id.'3' : $s_academic_year_id.$s_semester_type_id;
            $a_semester_selected = [
                'academic_year_id' => $s_academic_year_id,
                'semester_type_id' => $s_semester_type_id
            ];
        }
        else{
            $mba_semester_settings = $this->Smm->get_semester_setting();
            $index_semester = count($mba_semester_settings) - 1;
            foreach ($mba_semester_settings as $key => $o_semester) {
                if ($o_semester->semester_status == 'active') {
                    $index_semester = $key + 1;
                    break;
                }
            }
            
            $i_semester_selected = $mba_semester_settings[$index_semester]->academic_year_id.$mba_semester_settings[$index_semester]->semester_type_id;
            $a_semester_selected = [
                'academic_year_id' => $mba_semester_settings[$index_semester]->academic_year_id,
                'semester_type_id' => $mba_semester_settings[$index_semester]->semester_type_id
            ];
            
            if ($b_last_short_semester) {
                $i_semester_selected = ($mba_semester_settings[$index_semester]->semester_type_id == 1) ? $mba_semester_settings[$index_semester]->academic_year_id.'7' : $mba_semester_settings[$index_semester]->academic_year_id.'8';
                $a_semester_selected = [
                    'academic_year_id' => $mba_semester_settings[$index_semester]->academic_year_id,
                    'semester_type_id' => ($mba_semester_settings[$index_semester]->semester_type_id == 1) ? 7 : 8
                ];
            }
        }
        
        $a_filter = array(
            'ds.academic_year_id' => $s_student_batch,
            // 'ds.study_program_id' => $s_study_program_id
        );

        $s_text_header = 'MAHASISWA PRODI ';
        // $s_text_header = 'MAHASISWA ';
        $s_file_name = 'GPA_Recapitulation_';

        // if (($s_study_program_id == 'all') OR ($s_study_program_id == '')) {
        //     // unset($a_filter['ds.study_program_id']);
        //     $s_text_header .= '-';
        //     $s_file_name .= 'All_Prodi';
        // }else {
        //     $mbo_prodi_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
        //     $s_text_header .= strtoupper($mbo_prodi_data->study_program_name);
        //     $s_file_name .= strtoupper($mbo_prodi_data->study_program_abbreviation);
        // }
        $a_text_color = [
            'graduated' => '9F9F9F',
            'onleave' => 'B3C416',
            'active' => '000000',
            'resign' => '9F9F9F',
            'inactive' => 'B3C416'
        ];
        
        $a_prodi_name_ = [];
        $a_prodi_abbr_ = [];
        if (count($a_study_program_id) > 0) {
            $mba_prodi_list_data = $this->General->get_in('ref_study_program', 'study_program_id', $a_study_program_id);
            if ($mba_prodi_list_data) {
                foreach ($mba_prodi_list_data as $o_prodi) {
                    if (!in_array($o_prodi->study_program_name, $a_prodi_name_)) {
                        array_push($a_prodi_name_, $o_prodi->study_program_name);
                    }

                    if (!in_array($o_prodi->study_program_abbreviation, $a_prodi_abbr_)) {
                        array_push($a_prodi_abbr_, $o_prodi->study_program_abbreviation);
                    }
                }
            }
        }
        
        $s_text_header .= strtoupper(implode(' / ', $a_prodi_name_));
        $s_file_name .= strtoupper(implode('_', $a_prodi_abbr_));
        // print('<pre>');var_dump($s_file_name);exit;
        

        $s_text_header .= ' ANGKATAN ';

        if (($s_student_batch == 'all') OR ($s_student_batch == '')) {
            unset($a_filter['ds.academic_year_id']);
            // $s_text_header .= '_-';
        }else{
            $s_text_header .= ' '.$s_student_batch.'/'.(intval($s_student_batch) + 1);
            $s_file_name .= '_'.$s_student_batch.'-'.(intval($s_student_batch) + 1);
        }

        if ($b_semester_selected) {
            $s_text_header .= ' Semester '.$s_academic_year_id.'-'.$s_semester_type_id;
        }
        // $s_template_path = APPPATH.'uploads/templates/template-rekap-ipsipk-v2.xls';
        $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        if ($b_passed_deffence) {
            $a_filter['ds.student_mark_completed_defense'] = 1;
        }
        
        $a_filter = (count($a_filter) > 0) ? $a_filter : false;

        $mba_student_data = $this->Stm->get_student_list_data($a_filter, $a_student_status, array(
            // 'student_status' => 'ASC',
            'ds.academic_year_id' => 'ASC',
            'ds.student_number' => 'ASC',
            // 'faculty_name' => 'ASC',
            // 'study_program_name' => 'ASC',
            'personal_data_name' => 'ASC'
        ), $a_study_program_id);
        
        if ($mba_student_data) {
            $this->load->library('FeederAPI', ['mode' => 'production']);
            $s_filepath = APPPATH.'uploads/academic/'.$mba_semester_active->academic_year_id.$mba_semester_active->semester_type_id.'/cummulative_gpa/';

            if(!file_exists($s_filepath)){
                mkdir($s_filepath, 0777, TRUE);
            }
            // print('<pre>');var_dump($s_filepath);exit;

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_spreadsheet->getDefaultStyle()->getFont()->setSize(9);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_text_header)
                ->setCreator("IULI Academic Services")
                ->setLastModifiedBy("IULI Academic Services")
                ->setCategory("Cummulative GPA");
            // $o_sheet = $o_spreadsheet->setActiveSheetIndexByName("Template IPK");
            // $o_sheet->getFont()->setSize(9);
            $o_sheet->setCellValue('A1', 'REKAPITULASI IPS dan IPK '.$i_semester_selected);
            $o_sheet->setCellValue('A2', $s_text_header);
            
            $o_sheet->setCellValue('A3', 'No');
            $o_sheet->setCellValue('B3', 'Nama Mahasiswa');
            $o_sheet->setCellValue('C3', 'NIM');
            $o_sheet->setCellValue('D3', 'Batch');
            $o_sheet->setCellValue('E3', 'Status');
            $o_sheet->setCellValue('F3', 'SKS SEM');
            $o_sheet->setCellValue('H3', 'SKS TOTAL');
            $o_sheet->setCellValue('J3', 'IPS');
            $o_sheet->setCellValue('L3', 'IPK');
            
            $o_sheet->setCellValue('F4', 'P');
            $o_sheet->setCellValue('G4', 'F');
            $o_sheet->setCellValue('H4', 'P');
            $o_sheet->setCellValue('I4', 'F');
            $o_sheet->setCellValue('J4', 'P');
            $o_sheet->setCellValue('K4', 'F');
            $o_sheet->setCellValue('L4', 'P');
            $o_sheet->setCellValue('M4', 'F');

            $o_sheet->mergeCells('A3:A4');
            $o_sheet->mergeCells('B3:B4');
            $o_sheet->mergeCells('C3:C4');
            $o_sheet->mergeCells('D3:D4');
            $o_sheet->mergeCells('E3:E4');
            $o_sheet->mergeCells('F3:G3');
            $o_sheet->mergeCells('H3:I3');
            $o_sheet->mergeCells('J3:K3');
            $o_sheet->mergeCells('L3:M3');
            
            $i_row = 5;
            $i_number_counter = 1;

            $a_gpa_semester_data = array();
            $a_gpa_cummulative_data = array();
            $a_absence_data = array();
            
            foreach ($mba_student_data as $o_student) {
                $s_color = 'C00000';
                
                $s_student_status = strtolower($o_student->student_status);
                if (array_key_exists($s_student_status, $a_text_color)) {
                    $s_color = $a_text_color[$s_student_status];
                    // if ($s_student_status == 'active') {
                    //     print('<pre>');var_dump($s_color);exit;
                    // }
                }
                // else {
                //     print($s_student_status);exit;
                // }
                // print('<pre>'.__LINE__);var_dump($s_filepath);exit;
                $styleColor = array(
                    'font'  => array(
                        'color' => array('rgb' => $s_color)
                    )
                );
                $a_total_sks_feeder = array();
                $a_total_merit_feeder = array();

                $mba_student_semester = $this->Smm->get_semester_student_personal_data(array(
                    // 'st.personal_data_id' => $o_student->personal_data_id
                    'dss.student_id' => $o_student->student_id
                ), array(1,2,3,7,8));

                if ($mba_student_semester) {
                    $a_total_semester_absence = array();
                    $has_repetition = false;

                    $o_sheet->setCellValue('A'.$i_row, $i_number_counter);
                    $o_sheet->setCellValue('B'.$i_row, strtoupper($o_student->personal_data_name));
                    $o_sheet->setCellValue('C'.$i_row, strtoupper($o_student->student_number));
                    $o_sheet->setCellValue('D'.$i_row, strtoupper($o_student->academic_year_id));
                    $o_sheet->setCellValue('E'.$i_row, strtoupper(ucfirst($s_student_status)));
                    
                    // if ($o_student->student_status != 'active') {
                        $o_sheet->getStyle('A'.$i_row.':M'.$i_row)->applyFromArray($styleColor);
                    // }
                    $i_semester_student_start = $mba_student_semester[0]->semester_academic_year_id.$mba_student_semester[0]->semester_semester_type_id;

                    foreach($mba_student_semester AS $key => $o_student_semester) {
                        $i_semester_student = $o_student_semester->semester_academic_year_id.$o_student_semester->semester_semester_type_id;
                        $mbo_student_semester_data = $this->Stm->get_student_by_id($o_student_semester->student_id);
                        $a_score_forlap = $this->feederapi->post('ExportDataAktivitasKuliah', [
                            'filter' => "id_registrasi_mahasiswa='$o_student->student_id' AND id_periode='$i_semester_student'"
                        ]);

                        // $a_score_all_semester_forlap = $this->feederapi->post('GetRiwayatNilaiMahasiswa', [
                        //     'filter' => "id_registrasi_mahasiswa='$o_student->student_id' AND id_periode >= '$i_semester_student_start' AND id_periode<='$i_semester_student'"
                        // ]);
                        
                        $i_sks_forlap = 0;
                        $i_ips_forlap = 0;
                        $i_sks_total_forlap = 0;
                        $i_ipk_forlap = 0;
                        // $b_german_found = false;
                        if ($a_score_forlap->error_code == 0) {
                            foreach ($a_score_forlap->data as $o_score) {
                                $i_sks_forlap += $o_score->sks_semester;
                                $i_ips_forlap += $o_score->ips;
                                $i_sks_total_forlap += $o_score->total_sks;
                                $i_ipk_forlap += $o_score->ipk;
                            }
                        }else{
                            print('<pre>');
                            var_dump($a_score_forlap);exit;
                        }

                        // if ($a_score_all_semester_forlap->error_code == 0) {
                        //     foreach ($a_score_all_semester_forlap->data as $o_score) {
                        //         // print($o_score->nama_mata_kuliah);
                        //         $i_sks_total_forlap += $o_score->sks_mata_kuliah;
                        //         $i_merit_total_forlap += $o_score->sks_mata_kuliah * $o_score->nilai_indeks;
                        //     }   
                        // }else{
                        //     print('<pre>');
                        //     var_dump($a_score_forlap);exit;
                        // }
                        
                        $b_print = true;
                        if ($b_semester_selected) {
                            if (
                                ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND
                                ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])
                            ) {
                                $s_gpa_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'gpa', $b_last_repetition);
                                $s_credit_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'credit', $b_last_repetition);
                                $s_average_absence_ = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'average_absence', $b_last_repetition);
                                $s_average_absence_semester = 100 - $s_average_absence_;
                            }
                            else if ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) {
                                if (($a_semester_selected['semester_type_id'] == 3) AND (in_array($o_student_semester->semester_semester_type_id, [3,7,8]))) {
                                    $s_gpa_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'gpa', $b_last_repetition);
                                    $s_credit_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'credit', $b_last_repetition);
                                    $s_average_absence_ = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'average_absence', $b_last_repetition);
                                    $s_average_absence_semester = 100 - $s_average_absence_;
                                }
                                else {
                                    $b_print = false;
                                }
                            }
                            else{
                                $b_print = false;
                            }
                            
                        }else{
                            $s_gpa_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id);
                            $s_credit_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'credit');
                            $s_average_absence_ = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'average_absence');
                            $s_average_absence_semester = 100 - $s_average_absence_;
                        }
                        // if ($o_student->student_id == '92d444c1-15e7-49df-8301-110892295476') {
                        //     print('<pre>');var_dump($b_print);
                        //     var_dump($s_credit_semester);
                        // }

                        // $s_gpa_forlap = $this->grades->get_ipk($i_merit_forlap, $i_sks_forlap);

                        if ((!$b_semester_selected) AND ($b_print)) {
                            array_push($a_total_semester_absence, $s_average_absence_);

                            array_push($a_gpa_semester_data, $s_gpa_semester);
                            array_push($a_absence_data, $s_average_absence_);

                            // array_push($a_total_sks_feeder, $i_sks_forlap);
                            // array_push($a_total_merit_feeder, $i_merit_forlap);

                            $o_sheet->setCellValue('F'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                            $o_sheet->setCellValue('J'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));

                            $o_sheet->setCellValue('G'.$i_row, round($i_sks_forlap, 2, PHP_ROUND_HALF_UP));
                            $o_sheet->setCellValue('K'.$i_row, round($i_ips_forlap, 2, PHP_ROUND_HALF_UP));
                        }

                        if ($b_print AND $b_semester_selected) {
                            if ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) {
                                if ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id']) {
                                    array_push($a_total_semester_absence, $s_average_absence_);
    
                                    array_push($a_gpa_semester_data, $s_gpa_semester);
                                    array_push($a_absence_data, $s_average_absence_);

                                    // array_push($a_total_sks_feeder, $i_sks_forlap);
                                    // array_push($a_total_merit_feeder, $i_merit_forlap);

                                    $o_sheet->setCellValue('F'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                                    $o_sheet->setCellValue('J'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));

                                    $o_sheet->setCellValue('G'.$i_row, round($i_sks_forlap, 0, PHP_ROUND_HALF_UP));
                                    $o_sheet->setCellValue('K'.$i_row, round($i_ips_forlap, 2, PHP_ROUND_HALF_UP));
                                }
                                else if (($a_semester_selected['semester_type_id'] == 3) AND (in_array($o_student_semester->semester_semester_type_id, [3,7,8]))) {
                                    array_push($a_total_semester_absence, $s_average_absence_);
    
                                    array_push($a_gpa_semester_data, $s_gpa_semester);
                                    array_push($a_absence_data, $s_average_absence_);

                                    // array_push($a_total_sks_feeder, $i_sks_forlap);
                                    // array_push($a_total_merit_feeder, $i_merit_forlap);

                                    $o_sheet->setCellValue('F'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                                    $o_sheet->setCellValue('J'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));

                                    $o_sheet->setCellValue('G'.$i_row, round($i_sks_forlap, 0, PHP_ROUND_HALF_UP));
                                    $o_sheet->setCellValue('K'.$i_row, round($i_ips_forlap, 2, PHP_ROUND_HALF_UP));
                                }
                            }
                        }
                        if (
                            ($b_print) AND
                            ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND
                            ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])
                        ) {
                            if ($b_semester_selected) {
                                array_push($a_total_semester_absence, $s_average_absence_);
    
                                array_push($a_gpa_semester_data, $s_gpa_semester);
                                array_push($a_absence_data, $s_average_absence_);

                                // array_push($a_total_sks_feeder, $i_sks_forlap);
                                // array_push($a_total_merit_feeder, $i_merit_forlap);

                                $o_sheet->setCellValue('F'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                                $o_sheet->setCellValue('J'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));

                                $o_sheet->setCellValue('G'.$i_row, round($i_sks_forlap, 0, PHP_ROUND_HALF_UP));
                                $o_sheet->setCellValue('K'.$i_row, round($i_ips_forlap, 2, PHP_ROUND_HALF_UP));
                            }
                        }

                        $a_param_score = array(
                            // 'st.personal_data_id' => $o_student->personal_data_id,
                            'sc.student_id' => $o_student->student_id,
                            'sc.score_approval' => 'approved',
                            // 'sc.score_display' => 'TRUE',
                            'sc.semester_id !=' => '17',
                            'curriculum_subject_credit !=' => '0',
                            'sc.academic_year_id >=' => $mba_student_semester[0]->semester_academic_year_id,
                            'sc.academic_year_id <=' => $o_student_semester->semester_academic_year_id,
                            'curriculum_subject_type !=' => 'extracurricular'
                        );

                        // if (!$b_last_repetition) {
                        //     $a_param_score['score_mark_for_repetition'] = NULL;
                        // }
                        
                        $a_filter_semester = array(
                            'academic_year_start' => $mba_student_semester[0]->semester_academic_year_id,
                            'semester_type_start' => $mba_student_semester[0]->semester_semester_type_id,
                            // 'academic_year_end' => $a_semester_selected['academic_year_id'],
                            // 'semester_type_end' => $a_semester_selected['semester_type_id']
                            'academic_year_end' => $o_student_semester->semester_academic_year_id,
                            'semester_type_end' => $o_student_semester->semester_semester_type_id
                        );

                        // if (in_array($o_student_semester->semester_semester_type_id, [1,2])) {
                        //     $mba_score_data = $this->Scm->get_score_data($a_param_score, [1,2]);
                        // }
                        // else if (in_array($o_student_semester->semester_semester_type_id, [3,7,8])) {
                        //     $mba_score_data = $this->Scm->get_score_data($a_param_score, [3,7,8]);
                        // }

                        $mba_score_data = $this->Scm->get_score_data($a_param_score, [1,2,3,7,8]);
                        $mba_transfer_credit = $this->Scm->get_score_data([
                            'sc.student_id' => $o_student->student_id
                        ], [5]);

                        // $mba_score_data = modules::run('academic/score/clear_semester_score', $mba_score_data, $a_filter_semester);
                        if (($a_filter_semester) AND ($mba_score_data)) {
                            foreach ($mba_score_data as $key => $final) {
                                if (($a_filter_semester['semester_type_start'] !== null) AND ($a_filter_semester['semester_type_start'] == 2)) {
                                    if (($a_filter_semester['academic_year_start'] == $final->academic_year_id) AND ($final->semester_type_id == 1)) {
                                        unset($mba_score_data[$key]);
                                    }
                                }

                                if (($a_filter_semester['semester_type_end'] !== null) AND ($a_filter_semester['semester_type_end'] == 1)) {
                                    // if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id == 2)) {
                                    if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id != 1)) {
                                        unset($mba_score_data[$key]);
                                    }
                                }
                                else if (($a_filter_semester['semester_type_end'] !== null) AND ($a_filter_semester['semester_type_end'] == 2)) {
                                    // if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id == 2)) {
                                    if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND (in_array($final->semester_type_id, ['7', '8']))) {
                                        unset($mba_score_data[$key]);
                                    }
                                }
                            }
                        }

                        if (!$b_feeder_check) {
                            if ($mba_transfer_credit) {
                                $mba_score_data = array_merge($mba_score_data, $mba_transfer_credit);
                            }
                        }

                        $i_sum_credit = 0;
                        $i_sum_merit = 0;
                        $s_sum_absence_student = array_sum($a_total_semester_absence);
                        $s_average_absence_student = (count($a_total_semester_absence) > 0) ? ($s_sum_absence_student / count($a_total_semester_absence)) : 0;
                        $s_average_absence_student = 100 - $s_average_absence_student;

                        if ($mba_score_data) {
                            $a_credit = array();
                            $a_merit = array();

                            foreach ($mba_score_data as $score) {
                                if ($b_feeder_check) {
                                    if(!is_null($score->score_repetition_exam)){
                                        $has_repetition = true;
                                    }

                                    // if ($o_student->student_id == '2ce485fd-b21c-4504-8d8e-d8220a67ba36') {
                                    //     if ($i_semester_student == '20191') {
                                    //         print($score->academic_year_id.$score->semester_type_id.': '.$score->subject_name.' - '.$score->curriculum_subject_credit);
                                    //         print('<br>');
                                    //     }
                                    // }

                                    $score_sum = intval(round($score->score_sum, 0, PHP_ROUND_HALF_UP));
                                    $score_grade_point = $this->grades->get_grade_point($score_sum);
                                    $score_merit = $this->grades->get_merit($score->curriculum_subject_credit, $score_grade_point);
                                    array_push($a_credit, $score->curriculum_subject_credit);
                                    array_push($a_merit, $score_merit);
                                }
                                else if ($this->Scm->get_good_grades($score->subject_name, $score->student_id, $score->score_sum)) {
                                    // if (!in_array($score->subject_name, $a_subject_name_fill)) {
                                    if(!is_null($score->score_repetition_exam)){
                                        $has_repetition = true;
                                    }

                                    $score_sum = intval(round($score->score_sum, 0, PHP_ROUND_HALF_UP));
                                    $score_grade_point = $this->grades->get_grade_point($score_sum);
                                    $score_merit = $this->grades->get_merit($score->curriculum_subject_credit, $score_grade_point);
                                    array_push($a_credit, $score->curriculum_subject_credit);
                                    array_push($a_merit, $score_merit);

                                    //     array_push($a_subject_name_fill, $o_score->subject_name);
                                    // }
                                }
                            }

                            // if ($o_student->student_id == '2ce485fd-b21c-4504-8d8e-d8220a67ba36') {
                            //     print('<pre>');var_dump($a_credit);
                            // }

                            $i_sum_credit = array_sum($a_credit);
                            $i_sum_merit = array_sum($a_merit);
                        }

                        $s_gpa_cummulative = $this->grades->get_ipk($i_sum_merit, $i_sum_credit);
                        // $s_gpa_forlap_cummulative = $this->grades->get_ipk($i_merit_total_forlap, $i_sks_total_forlap);
                        
                        array_push($a_gpa_cummulative_data, $s_gpa_cummulative);
                        
                        $s_predicate = '-';
                        $b_has_repeat_subject = modules::run('academic/score/has_repeat_subject', $o_student->student_id);
                        if(!$b_has_repeat_subject) {
                            $s_predicate = $this->grades->get_graduation_predicate(round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        }
                        // if (!$has_repetition) {
                        //     $s_predicate = $this->grades->get_graduation_predicate(round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        // }
                        
                        $o_sheet->setCellValue('L'.$i_row, round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('H'.$i_row, round($i_sum_credit));
                        $o_sheet->setCellValue('M'.$i_row, round($i_ipk_forlap, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('I'.$i_row, round($i_sks_total_forlap));
                        
                        // $o_sheet->setCellValue('P'.$i_row, $s_predicate);

                        if (!$b_semester_selected) {
                            $i_row++;
                            $o_sheet->insertNewRowBefore($i_row, 1);
                        }
                        else {
                            if (($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])) {
                                // if ($o_student->student_id == '32d9f6ba-e0e6-4914-875c-0e6a40575293') {
                                //     print('<pre>');var_dump($i_semester_student.'=>'.$i_sum_credit);
                                // }
                                break;
                            }
                            // else {
                            //     if ($o_student->student_id == '32d9f6ba-e0e6-4914-875c-0e6a40575293') {
                            //         print('<pre>');var_dump($i_semester_student.'=>'.$i_sum_credit);
                            //     }
                            // }
                            // if ($o_student->student_id == '32d9f6ba-e0e6-4914-875c-0e6a40575293') {
                            //     print('<br>');
                            // }
                        }

                        // print($i_sum_credit.'<br>');
                    }
                    // if ($o_student->student_id == '92d444c1-15e7-49df-8301-110892295476') {
                    // exit;
                    // }

                    $i_number_counter++;
                    $i_row++;
                    $o_sheet->insertNewRowBefore($i_row, 1);
                }

                // if ($o_student->student_id == '32d9f6ba-e0e6-4914-875c-0e6a40575293') {
                //     print('last:'.$i_sum_credit);exit;
                // }
            }
            // print('<pre>'.__LINE__);var_dump($a_study_program_id);exit;
            $style_border = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '00000000'],
                    )
                )
            );

            $o_sheet->removeRow($i_row, 1);

            $o_sheet->getColumnDimension('A')->setWidth(3);
            $o_sheet->getColumnDimension('B')->setWidth(30);
            $o_sheet->getColumnDimension('C')->setWidth(12);
            $o_sheet->getColumnDimension('D')->setWidth(6.5);
            $o_sheet->getColumnDimension('E')->setWidth(9);

            $o_sheet->getColumnDimension('F')->setWidth(5.3);
            $c_start = 'F';
            for ($i=0; $i < 8; $i++) { 
                $o_sheet->getColumnDimension($c_start++)->setWidth(5.5);
            }
            // $o_sheet->getColumnDimension('G:M')->setWidth(3);

            $o_sheet->getStyle('A3:M'.$i_row)->applyFromArray($style_border);

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_filepath.$s_file_name.'.xlsx');
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            return $s_file_name.'.xlsx';
        }
        else{
            return false;
        }
    }
    
    public function generate_cummulative_gpa2(
        $s_student_batch,
        $b_passed_deffence = false,
        $a_study_program_id,
        $a_student_status = false,
        $b_semester_selected = false, // $b_last_semester = false,
        $b_last_short_semester = false,
        $b_last_repetition = true,
        $s_academic_year_id = false,
        $s_semester_type_id = false,
        $b_feeder_check = false
    )
    {
        $mba_semester_active = $this->Smm->get_active_semester();
        $i_short_semester_blocked = 0;

        if ($s_academic_year_id AND $s_semester_type_id) {
            $i_semester_selected = $s_academic_year_id.$s_semester_type_id;
            $a_semester_selected = [
                'academic_year_id' => $s_academic_year_id,
                'semester_type_id' => $s_semester_type_id
            ];
        }
        else{
            $mba_semester_settings = $this->Smm->get_semester_setting();
            $index_semester = count($mba_semester_settings) - 1;
            foreach ($mba_semester_settings as $key => $o_semester) {
                if ($o_semester->semester_status == 'active') {
                    $index_semester = $key + 1;
                    break;
                }
            }
            // print('<pre>');
            // var_dump($mba_semester_settings[$index_semester]);exit;

            $i_semester_selected = $mba_semester_settings[$index_semester]->academic_year_id.$mba_semester_settings[$index_semester]->semester_type_id;
            $a_semester_selected = [
                'academic_year_id' => $mba_semester_settings[$index_semester]->academic_year_id,
                'semester_type_id' => $mba_semester_settings[$index_semester]->semester_type_id
            ];
            
            if ($b_last_short_semester) {
                $i_semester_selected = ($mba_semester_settings[$index_semester]->semester_type_id == 1) ? $mba_semester_settings[$index_semester]->academic_year_id.'7' : $mba_semester_settings[$index_semester]->academic_year_id.'8';
                $a_semester_selected = [
                    'academic_year_id' => $mba_semester_settings[$index_semester]->academic_year_id,
                    'semester_type_id' => ($mba_semester_settings[$index_semester]->semester_type_id == 1) ? 7 : 8
                ];
            }
        }
        
        $a_filter = array(
            'ds.academic_year_id' => $s_student_batch,
            // 'ds.study_program_id' => $s_study_program_id
        );

        $s_text_header = 'MAHASISWA PRODI ';
        // $s_text_header = 'MAHASISWA ';
        $s_file_name = 'GPA_Recapitulation_';

        // if (($s_study_program_id == 'all') OR ($s_study_program_id == '')) {
        //     // unset($a_filter['ds.study_program_id']);
        //     $s_text_header .= '-';
        //     $s_file_name .= 'All_Prodi';
        // }else {
        //     $mbo_prodi_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
        //     $s_text_header .= strtoupper($mbo_prodi_data->study_program_name);
        //     $s_file_name .= strtoupper($mbo_prodi_data->study_program_abbreviation);
        // }
        $a_prodi_name_ = [];
        $a_prodi_abbr_ = [];
        if (count($a_study_program_id) > 0) {
            $mba_prodi_list_data = $this->General->get_in('ref_study_program', 'study_program_id', $a_study_program_id);
            if ($mba_prodi_list_data) {
                foreach ($mba_prodi_list_data as $o_prodi) {
                    if (!in_array($o_prodi->study_program_name, $a_prodi_name_)) {
                        array_push($a_prodi_name_, $o_prodi->study_program_name);
                    }

                    if (!in_array($o_prodi->study_program_abbreviation, $a_prodi_abbr_)) {
                        array_push($a_prodi_abbr_, $o_prodi->study_program_abbreviation);
                    }
                }
            }
        }
        
        $s_text_header .= strtoupper(implode(' / ', $a_prodi_name_));
        $s_file_name .= strtoupper(implode('_', $a_prodi_abbr_));
        // print('<pre>');var_dump($s_file_name);exit;
        

        $s_text_header .= ' ANGKATAN ';

        if (($s_student_batch == 'all') OR ($s_student_batch == '')) {
            unset($a_filter['ds.academic_year_id']);
            // $s_text_header .= '_-';
        }else{
            $s_text_header .= ' '.$s_student_batch.'/'.(intval($s_student_batch) + 1);
            $s_file_name .= '_'.$s_student_batch.'-'.(intval($s_student_batch) + 1);
        }

        if ($b_semester_selected) {
            $s_text_header .= ' Semester '.$s_academic_year_id.'-'.$s_semester_type_id;
        }

        $s_template_path = APPPATH.'uploads/templates/template-rekap-ipsipk-v2.xls';
        if ($b_passed_deffence) {
            $a_filter['ds.student_mark_completed_defense'] = 1;
        }
        
        $a_filter = (count($a_filter) > 0) ? $a_filter : false;

        $mba_student_data = $this->Stm->get_student_list_data($a_filter, $a_student_status, array(
            'faculty_name' => 'ASC',
            'study_program_name' => 'ASC',
            'personal_data_name' => 'ASC'
        ), $a_study_program_id);
        // $a_student_list = ['11202308003','11202308001','11201711007','11201710002','11202110003','11202007018','11201809002','11201808009','11201907015','11201909003','11201909002','11201908002','11201910001','11201909007','11201907008','11201907005','11202004001','11202007003','11202007004','11202009001','11202008011','11202007005','11202007008','11202007006','11201607008','11202210001','11202110001','11201608004','11201708007','11201710005','11202110002','11201909005','11202109001','11201808001','11201807010','11202008002','11201808006','11202007012','11202007020','11201908005','11201907001','11201907002','11202007007','11201910005','11201907003','11201907004','11201907014','11202208002','11201907012','11201907013','11202007017','11202007010','11201910007','11202008008','11202009003','11202007013','11202008012','11202107007','11201908003','11202010002','11202110005','11202110007','11202110006','11202210003','11202210002','11202208009','11202208003','11202208004','11202208005','11202207005','11202207001','11202207002','11202207003','11202207004','11202209005','11202208010','11202209003','11202209006','11202209002','11202208008'];
        // $mba_student_data = $this->Stm->get_student_list_data_temp($a_filter, $a_student_list, array(
        //     'faculty_name' => 'ASC',
        //     'study_program_name' => 'ASC',
        //     'personal_data_name' => 'ASC'
        // ), $a_study_program_id);
        
        if ($mba_student_data) {
            $s_filepath = APPPATH.'uploads/academic/'.$mba_semester_active->academic_year_id.$mba_semester_active->semester_type_id.'/cummulative_gpa/';

            if(!file_exists($s_filepath)){
                mkdir($s_filepath, 0777, TRUE);
            }
            // print('<pre>');var_dump($s_filepath);exit;

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_text_header)
                ->setCreator("IULI Academic Services")
                ->setLastModifiedBy("IULI Academic Services")
                ->setCategory("Cummulative GPA");
            $o_sheet = $o_spreadsheet->setActiveSheetIndexByName("Template IPK");
            $o_sheet->setCellValue('A1', 'REKAPITULASI IPS dan IPK '.$i_semester_selected);
            $o_sheet->setCellValue('A2', $s_text_header);

            $i_row = 4;
            $i_number_counter = 1;

            $a_gpa_semester_data = array();
            $a_gpa_cummulative_data = array();
            $a_absence_data = array();

            foreach ($mba_student_data as $o_student) {
                $mba_student_semester = $this->Smm->get_semester_student_personal_data(array(
                    // 'st.personal_data_id' => $o_student->personal_data_id
                    'dss.student_id' => $o_student->student_id,
                    // 'dss.academic_year_id >= ' => '2020',
                    // 'dss.academic_year_id <= ' => '2022',
                // ), array(1,2));
                ), array(1,2,3,7,8));

                if ($mba_student_semester) {
                    $a_total_semester_absence = array();
                    $has_repetition = false;

                    $o_sheet->setCellValue('A'.$i_row, $i_number_counter);
                    $o_sheet->setCellValue('B'.$i_row, strtoupper($o_student->personal_data_name));
                    $o_sheet->setCellValue('C'.$i_row, strtoupper($o_student->student_number));

                    foreach($mba_student_semester AS $key => $o_student_semester) {
                        $i_semester_student = $o_student_semester->semester_academic_year_id.$o_student_semester->semester_semester_type_id;
                        $mbo_student_semester_data = $this->Stm->get_student_by_id($o_student_semester->student_id);
                        
                        $b_print = true;
                        if ($b_semester_selected) {
                            if (
                                ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND
                                ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])
                            ) {
                                $s_gpa_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'gpa', $b_last_repetition);
                                $s_credit_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'credit', $b_last_repetition);
                                $s_average_absence_ = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'average_absence', $b_last_repetition);
                                $s_average_absence_semester = 100 - $s_average_absence_;
                            }else{
                                $b_print = false;
                            }
                            
                        }else{
                            $s_gpa_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id);
                            $s_credit_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'credit');
                            $s_average_absence_ = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'average_absence');
                            $s_average_absence_semester = 100 - $s_average_absence_;
                        }

                        if ((!$b_semester_selected) AND ($b_print)) {
                            array_push($a_total_semester_absence, $s_average_absence_);

                            array_push($a_gpa_semester_data, $s_gpa_semester);
                            array_push($a_absence_data, $s_average_absence_);

                            $o_sheet->setCellValue('D'.$i_row, strtoupper($mbo_student_semester_data->study_program_name));
                            $o_sheet->setCellValue('E'.$i_row, strtoupper($mbo_student_semester_data->academic_year_id));
                            $o_sheet->setCellValue('F'.$i_row, $o_student_semester->semester_academic_year_id.$o_student_semester->semester_semester_type_id);
                            $o_sheet->setCellValue('G'.$i_row, $o_student_semester->semester_type_name);
                            $o_sheet->setCellValue('H'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));
                            $o_sheet->setCellValue('J'.$i_row, round($s_average_absence_semester, 2, PHP_ROUND_HALF_UP));
                            $o_sheet->setCellValue('L'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                        }

                        if (
                            ($b_print) AND
                            ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND
                            ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])
                        ) {
                            if ($b_semester_selected) {
                                array_push($a_total_semester_absence, $s_average_absence_);
    
                                array_push($a_gpa_semester_data, $s_gpa_semester);
                                array_push($a_absence_data, $s_average_absence_);

                                $o_sheet->setCellValue('D'.$i_row, strtoupper($mbo_student_semester_data->study_program_abbreviation));
                                $o_sheet->setCellValue('E'.$i_row, strtoupper($mbo_student_semester_data->academic_year_id));
                                $o_sheet->setCellValue('F'.$i_row, $o_student_semester->semester_academic_year_id.$o_student_semester->semester_semester_type_id);
                                $o_sheet->setCellValue('G'.$i_row, $o_student_semester->semester_type_name);
                                $o_sheet->setCellValue('H'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));
                                $o_sheet->setCellValue('J'.$i_row, round($s_average_absence_semester, 2, PHP_ROUND_HALF_UP));
                                $o_sheet->setCellValue('L'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                            }

                        }

                        $a_param_score = array(
                            // 'st.personal_data_id' => $o_student->personal_data_id,
                            'sc.student_id' => $o_student->student_id,
                            'sc.score_approval' => 'approved',
                            // 'sc.score_display' => 'TRUE',
                            'sc.semester_id !=' => '17',
                            'curriculum_subject_credit !=' => '0',
                            'sc.academic_year_id >=' => $mba_student_semester[0]->semester_academic_year_id,
                            'sc.academic_year_id <=' => $o_student_semester->semester_academic_year_id,
                            'curriculum_subject_type !=' => 'extracurricular'
                        );

                        // if (!$b_last_repetition) {
                        //     $a_param_score['score_mark_for_repetition'] = NULL;
                        // }
                        
                        $a_filter_semester = array(
                            'academic_year_start' => $mba_student_semester[0]->semester_academic_year_id,
                            'semester_type_start' => $mba_student_semester[0]->semester_semester_type_id,
                            // 'academic_year_end' => $a_semester_selected['academic_year_id'],
                            // 'semester_type_end' => $a_semester_selected['semester_type_id']
                            'academic_year_end' => $o_student_semester->semester_academic_year_id,
                            'semester_type_end' => $o_student_semester->semester_semester_type_id
                        );

                        // if (in_array($o_student_semester->semester_semester_type_id, [1,2])) {
                        //     $mba_score_data = $this->Scm->get_score_data($a_param_score, [1,2]);
                        // }
                        // else if (in_array($o_student_semester->semester_semester_type_id, [3,7,8])) {
                        //     $mba_score_data = $this->Scm->get_score_data($a_param_score, [3,7,8]);
                        // }

                        $mba_score_data = $this->Scm->get_score_data($a_param_score, [1,2,3,7,8]);
                        $mba_transfer_credit = $this->Scm->get_score_data([
                            'sc.student_id' => $o_student->student_id
                        ], [5]);

                        // $mba_score_data = modules::run('academic/score/clear_semester_score', $mba_score_data, $a_filter_semester);
                        if (($a_filter_semester) AND ($mba_score_data)) {
                            foreach ($mba_score_data as $key => $final) {
                                if (($a_filter_semester['semester_type_start'] !== null) AND ($a_filter_semester['semester_type_start'] == 2)) {
                                    if (($a_filter_semester['academic_year_start'] == $final->academic_year_id) AND ($final->semester_type_id == 1)) {
                                        unset($mba_score_data[$key]);
                                    }
                                }

                                if (($a_filter_semester['semester_type_end'] !== null) AND ($a_filter_semester['semester_type_end'] == 1)) {
                                    // if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id == 2)) {
                                    if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id != 1)) {
                                        unset($mba_score_data[$key]);
                                    }
                                }
                                else if (($a_filter_semester['semester_type_end'] !== null) AND ($a_filter_semester['semester_type_end'] == 2)) {
                                    // if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id == 2)) {
                                    if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND (in_array($final->semester_type_id, ['7', '8']))) {
                                        unset($mba_score_data[$key]);
                                    }
                                }
                            }
                        }

                        if (!$b_feeder_check) {
                            if ($mba_transfer_credit) {
                                $mba_score_data = array_merge($mba_score_data, $mba_transfer_credit);
                            }
                        }

                        $i_sum_credit = 0;
                        $i_sum_merit = 0;
                        $s_sum_absence_student = array_sum($a_total_semester_absence);
                        $s_average_absence_student = (count($a_total_semester_absence) > 0) ? ($s_sum_absence_student / count($a_total_semester_absence)) : 0;
                        $s_average_absence_student = 100 - $s_average_absence_student;

                        if ($mba_score_data) {
                            $a_credit = array();
                            $a_merit = array();

                            foreach ($mba_score_data as $score) {
                                if ($b_feeder_check) {
                                    if(!is_null($score->score_repetition_exam)){
                                        $has_repetition = true;
                                    }

                                    // if ($o_student->student_id == '2ce485fd-b21c-4504-8d8e-d8220a67ba36') {
                                    //     if ($i_semester_student == '20191') {
                                    //         print($score->academic_year_id.$score->semester_type_id.': '.$score->subject_name.' - '.$score->curriculum_subject_credit);
                                    //         print('<br>');
                                    //     }
                                    // }

                                    $score_sum = intval(round($score->score_sum, 0, PHP_ROUND_HALF_UP));
                                    $score_grade_point = $this->grades->get_grade_point($score_sum);
                                    $score_merit = $this->grades->get_merit($score->curriculum_subject_credit, $score_grade_point);
                                    array_push($a_credit, $score->curriculum_subject_credit);
                                    array_push($a_merit, $score_merit);
                                }
                                else if ($this->Scm->get_good_grades($score->subject_name, $score->student_id, $score->score_sum)) {
                                    // if (!in_array($score->subject_name, $a_subject_name_fill)) {
                                    if(!is_null($score->score_repetition_exam)){
                                        $has_repetition = true;
                                    }

                                    $score_sum = intval(round($score->score_sum, 0, PHP_ROUND_HALF_UP));
                                    $score_grade_point = $this->grades->get_grade_point($score_sum);
                                    $score_merit = $this->grades->get_merit($score->curriculum_subject_credit, $score_grade_point);
                                    array_push($a_credit, $score->curriculum_subject_credit);
                                    array_push($a_merit, $score_merit);

                                    //     array_push($a_subject_name_fill, $o_score->subject_name);
                                    // }
                                }
                            }

                            // if ($o_student->student_id == '2ce485fd-b21c-4504-8d8e-d8220a67ba36') {
                            //     print('<pre>');var_dump($a_credit);
                            // }

                            $i_sum_credit = array_sum($a_credit);
                            $i_sum_merit = array_sum($a_merit);
                        }

                        $s_gpa_cummulative = $this->grades->get_ipk($i_sum_merit, $i_sum_credit);
                        array_push($a_gpa_cummulative_data, $s_gpa_cummulative);

                        $s_predicate = '-';
                        $b_has_repeat_subject = modules::run('academic/score/has_repeat_subject', $o_student->student_id);
                        if(!$b_has_repeat_subject) {
                            $s_predicate = $this->grades->get_graduation_predicate(round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        }
                        // if (!$has_repetition) {
                        //     $s_predicate = $this->grades->get_graduation_predicate(round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        // }
                        
                        $o_sheet->setCellValue('I'.$i_row, round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('K'.$i_row, round($s_average_absence_student, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('M'.$i_row, round($i_sum_credit, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('Q'.$i_row, $s_predicate);

                        if (!$b_semester_selected) {
                            $i_row++;
                            $o_sheet->insertNewRowBefore($i_row, 1);
                        }
                        else {
                            if (($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])) {
                                // if ($o_student->student_id == '32d9f6ba-e0e6-4914-875c-0e6a40575293') {
                                //     print('<pre>');var_dump($i_semester_student.'=>'.$i_sum_credit);
                                // }
                                break;
                            }
                            // else {
                            //     if ($o_student->student_id == '32d9f6ba-e0e6-4914-875c-0e6a40575293') {
                            //         print('<pre>');var_dump($i_semester_student.'=>'.$i_sum_credit);
                            //     }
                            // }
                            // if ($o_student->student_id == '32d9f6ba-e0e6-4914-875c-0e6a40575293') {
                            //     print('<br>');
                            // }
                        }

                        // print($i_sum_credit.'<br>');
                    }

                    $i_number_counter++;
                    $i_row++;
                    $o_sheet->insertNewRowBefore($i_row, 1);
                }

                // if ($o_student->student_id == '32d9f6ba-e0e6-4914-875c-0e6a40575293') {
                //     print('last:'.$i_sum_credit);exit;
                // }
            }

            $o_sheet->removeRow($i_row, 1);
            $i_row += 2;

            $s_max_gpa_semester_data = max($a_gpa_semester_data);
            $s_max_gpa_cummulative_data = max($a_gpa_cummulative_data);

            $s_min_gpa_semester_data = min($a_gpa_semester_data);
            $s_min_gpa_cummulative_data = min($a_gpa_cummulative_data);

            $s_average_gpa_semester = (count($a_gpa_semester_data) > 0) ? (array_sum($a_gpa_semester_data) / count($a_gpa_semester_data)) : 0;
            $s_average_gpa_cummulative = (count($a_gpa_cummulative_data) > 0) ? (array_sum($a_gpa_cummulative_data) / count($a_gpa_cummulative_data)) : 0;
            $s_average_absence = 100 - ((count($a_absence_data) > 0) ? (array_sum($a_absence_data) / count($a_absence_data)) : 0);

            $o_sheet->setCellValue('H'.$i_row, round($s_average_gpa_semester, 2, PHP_ROUND_HALF_UP));
            $o_sheet->setCellValue('I'.$i_row, round($s_average_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
            $o_sheet->setCellValue('J'.$i_row, round($s_average_absence, 2, PHP_ROUND_HALF_UP));
            $i_row++;
            $o_sheet->setCellValue('H'.$i_row, round($s_max_gpa_semester_data, 2, PHP_ROUND_HALF_UP));
            $o_sheet->setCellValue('I'.$i_row, round($s_max_gpa_cummulative_data, 2, PHP_ROUND_HALF_UP));
            $i_row++;
            $o_sheet->setCellValue('H'.$i_row, round($s_min_gpa_semester_data, 2, PHP_ROUND_HALF_UP));
            $o_sheet->setCellValue('I'.$i_row, round($s_min_gpa_cummulative_data, 2, PHP_ROUND_HALF_UP));

            if ($b_semester_selected) {
                $o_sheet->removeColumn('P');
                $o_sheet->removeColumn('O');
                $o_sheet->removeColumn('N');
                $o_sheet->removeColumn('K');
                $o_sheet->removeColumn('J');
                $o_sheet->removeColumn('G');
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_filepath.$s_file_name.'.xlsx');
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            return $s_file_name.'.xlsx';
        }
        else{
            return false;
        }

    }

    public function generate_transcript_semester($s_student_id, $s_academic_year_id, $s_semester_type_id)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        if ($mbo_student_data) {
            $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
            $s_vice_rector_name = '';
            $s_vice_rector_email = '';
            if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
                $s_vice_rector_email = $mbo_vice_rector_data->employee_email;
            }

            $mbo_faculty_data = $this->Spm->get_faculty_data(array('faculty_id' => $mbo_student_data->faculty_id))[0];
            $personal_data_deans = $this->Pdm->get_personal_data_by_id($mbo_faculty_data->deans_id);
            $personal_data_hod = $this->Pdm->get_personal_data_by_id($mbo_student_data->head_of_study_program_id);
            $s_hod_name = $this->Pdm->retrieve_title($mbo_student_data->head_of_study_program_id);
            $mba_study_program_data = $this->Spm->get_study_program($mbo_student_data->study_program_id, false)[0];
            $s_deans_name = ($personal_data_deans) ? $this->Pdm->retrieve_title($personal_data_deans->personal_data_id) : '';

            // $s_header_sign_name = (is_null($mbo_student_data->head_of_study_program_id)) ? $s_vice_rector_name : $s_hod_name;
            // $s_header_sign_title = (is_null($mbo_student_data->head_of_study_program_id)) ? 'Vice Rector Academic' : 'Head of Study Program of '.$mba_study_program_data->study_program_name;

            $s_header_sign_name = (empty($s_deans_name)) ? $s_vice_rector_name : $s_deans_name;
            $s_header_sign_title = (empty($s_deans_name)) ? 'Vice Rector Academic' : 'Dean of '.$mbo_faculty_data->faculty_name;

            $a_score_filter = array(
                // 'st.personal_data_id' => $mbo_student_data->personal_data_id,
                'st.student_id' => $mbo_student_data->student_id,
                'sc.academic_year_id' => $s_academic_year_id,
                'sc.semester_type_id' => $s_semester_type_id,
                'sc.score_approval' => 'approved',
                'sc.score_display' => 'TRUE'
            );
            $mba_score_data = $this->Scm->get_score_data($a_score_filter);
            $mba_score_all = $this->Scm->get_score_data(array(
                // 'st.personal_data_id' => $mbo_student_data->personal_data_id,
                'st.student_id' => $mbo_student_data->student_id,
                'sc.score_approval' => 'approved',
                'sc.score_display' => 'TRUE',
                'curs.curriculum_subject_type !=' => 'extracurricular',
                'curs.curriculum_subject_credit > ' => 0
            ));

            if ($mba_score_data) {
                $s_token = md5($mbo_student_data->student_id.time().rand(1000,4000));
                if($this->Smm->save_student_semester(array('student_semester_transcript_token' => $s_token), array(
                    'student_id' => $mbo_student_data->student_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'student_semester_status' => 'active'
                ))) {
                // if($this->Stm->update_student_data(array('student_semester_transcript_token' => $s_token), $mbo_student_data->student_id)) {
                    $i_all_total_credit = 0;
                    $i_all_total_merit = 0;
                    $a_score_filter_extracur = $a_score_filter;
                    $a_score_filter_extracur['curs.curriculum_subject_type'] = 'extracurricular';
                    $a_score_filter_non_extracul = $a_score_filter;
                    $a_score_filter_non_extracul['curs.curriculum_subject_type !='] = 'extracurricular';

                    if ($mba_score_all) {
                        foreach ($mba_score_all as $all_score) {
                            $b_execute_cummulative_gpa = false;
                            if (($all_score->academic_year_id < $s_academic_year_id)) {
                                $b_execute_cummulative_gpa = true;
                            }else if (($all_score->academic_year_id == $s_academic_year_id)) {
                                if ($all_score->semester_type_id <= $s_semester_type_id) {
                                    $b_execute_cummulative_gpa = true;
                                }
                            }

                            if ($b_execute_cummulative_gpa) {
                                $score_sum = intval(round($all_score->score_sum, 0, PHP_ROUND_HALF_UP));
                                $score_grade_point = $this->grades->get_grade_point($score_sum);
                                $score_merit = $this->grades->get_merit($all_score->curriculum_subject_credit, $score_grade_point);

                                // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                                //     print($all_score->subject_name.' / '.$all_score->curriculum_subject_credit.'<br>');
                                // }
                                $i_all_total_credit += $all_score->curriculum_subject_credit;
                                $i_all_total_merit += $score_merit;
                            }
                        }
                    }

                    $s_semester_academic = $s_academic_year_id.''.$s_semester_type_id;
                    // var_dump($s_semester_academic);exit;
                    $mbo_semester_details = $this->Smm->get_semester_setting(array('dss.academic_year_id' => $s_academic_year_id, 'dss.semester_type_id' => $s_semester_type_id))[0];
                    $s_file = $mbo_student_data->student_number.'_Final-Score_'.$s_semester_academic.'_'.str_replace(' ','-', str_replace("&#039;", "", $mbo_student_data->personal_data_name));
                    $s_file_name = $s_file.'.xls';
                    $s_transcript_path = APPPATH.'/uploads/academic/'.$s_semester_academic.'/transcript/'.$mbo_student_data->study_program_abbreviation.'/';
                    // $s_template_path = APPPATH.'uploads/templates/transcript-final-temp.xls';
                    $s_template_path = APPPATH.'uploads/templates/transcript-final.xls';

                    $ai_sum_score_sum = array();
                    $ai_sum_score_merit = array();
                    $ai_sum_score_credit = array();
                    $ai_sum_score_quiz = array();
                    $ai_sum_score_final_exam = array();
                    $mba_score_non_extracul = $this->Scm->get_score_data($a_score_filter_non_extracul);
                    $mba_score_extracul = $this->Scm->get_score_data($a_score_filter_extracur);
                    $s_digital_sign = md5($mbo_student_data->student_number . $s_academic_year_id. $s_semester_type_id . microtime());

                    $save_digital_sign = $this->Stm->update_student_data(array(
                        'student_transcript_token' => $s_digital_sign
                    ), $mbo_student_data->student_id);

                    $save_sign = $this->Stm->save_sign_data([
                        'student_id' => $s_student_id,
                        'document_type' => 'transcript_semester', 
                        'document_sign' => $s_digital_sign
                    ]);

                    if ($save_digital_sign AND $save_sign) {
                        $i_start_score = 19;
                        $i_cumulative_gpa = $this->grades->get_ipk($i_all_total_merit, $i_all_total_credit);
                        // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                        //     print('<h1>Total :'.$i_all_total_merit.'|'.$i_all_total_credit.'</h1><br>');
                        // }
                        $iRow = 16;

                        if(!file_exists($s_transcript_path)){
                            mkdir($s_transcript_path, 0777, TRUE);
                        }

                        $o_spreadsheet = IOFactory::load($s_template_path);
                        $o_sheet = $o_spreadsheet->getActiveSheet();
                        $o_spreadsheet->getProperties()
                            ->setTitle($s_file_name)
                            ->setCreator("IULI Academic Services")
                            ->setCategory("Transcript Semester Report");

                        $sheet = $o_spreadsheet->setActiveSheetIndexByName("master");
                        $sheet->setCellValue('B9', 'Semester ' . $s_semester_academic . ' / Academic Year ' . $s_academic_year_id. '-' . (intval($s_academic_year_id) + 1));
                        $sheet->setCellValue('B10', date('d F Y', strtotime($mbo_semester_details->semester_start_date)).' - '.date('d F Y', strtotime($mbo_semester_details->semester_end_date)));
                        $sheet->setCellValue('E12', str_replace("&#039;", "'", $mbo_student_data->personal_data_name));
                        $sheet->setCellValue('E13', ucfirst(strtolower($mbo_student_data->personal_data_place_of_birth)) . ', ' . date('j M Y ', strtotime($mbo_student_data->personal_data_date_of_birth)));
                        $sheet->setCellValue('E14', $mbo_student_data->student_number);
                        // $sheet->setCellValue('E15', $mbo_student_data->faculty_name . ' / ' . $mbo_student_data->study_program_name);
                        $sheet->setCellValue('B15', 'Study Program');
                        $sheet->setCellValue('E15', $mbo_student_data->study_program_name);
                        $sheet->setCellValue('E' . $iRow, date('j F o', time()));

                        if ($mba_score_non_extracul) {
                            $no = 0;
                            foreach ($mba_score_non_extracul as $score) {
                                $score_sum = intval(round($score->score_sum, 0, PHP_ROUND_HALF_UP));
                                $score_grade = $this->grades->get_grade($score_sum);
                                $score_grade_point = $this->grades->get_grade_point($score_sum);
                                $score_merit = $this->grades->get_merit($score->curriculum_subject_credit, $score_grade_point);

                                $sheet->insertNewRowBefore($i_start_score + 1, 1);
                                $sheet->mergeCells('C' . $i_start_score . ':F' . $i_start_score);
                                $sheet
                                    ->setCellValue('B'.$i_start_score, ++$no)
                                    ->setCellValue('C'.$i_start_score, $score->subject_name)
                                    ->setCellValue('G'.$i_start_score, intval(round($score->score_quiz, 0, PHP_ROUND_HALF_UP)))
                                    ->setCellValue('H'.$i_start_score, intval(round($score->score_final_exam, 0, PHP_ROUND_HALF_UP)))
                                    ->setCellValue('I'.$i_start_score, (is_null($score->score_repetition_exam)) ? '-' : intval(round($score->score_repetition_exam, 0, PHP_ROUND_HALF_UP)))
                                    ->setCellValue('J'.$i_start_score, $score_sum)
                                    ->setCellValue('K'.$i_start_score, $score_grade)
                                    ->setCellValue('L'.$i_start_score, $score_grade_point)
                                    ->setCellValue('M'.$i_start_score, $score->curriculum_subject_credit)
                                    ->setCellValue('N'.$i_start_score, $score_merit)
                                    ->setCellValue('O'.$i_start_score, $score->score_absence.' %')
                                    ->setCellValue('P'.$i_start_score, (($score_grade == 'F') OR (intval($score->score_absence) > 25)) ? 'FAIL' : '-');
                                $i_start_score++;
                                $i_end_score_data = $i_start_score;

                                array_push($ai_sum_score_sum, $score_sum);
                                array_push($ai_sum_score_merit, $score_merit);
                                array_push($ai_sum_score_credit, $score->curriculum_subject_credit);
                                array_push($ai_sum_score_quiz, intval(round($score->score_quiz, 0, PHP_ROUND_HALF_UP)));
                                array_push($ai_sum_score_final_exam, intval(round($score->score_final_exam, 0, PHP_ROUND_HALF_UP)));
                            }

                            $sheet->removeRow($i_start_score, 1);
                            $i_total_credit = array_sum($ai_sum_score_credit);
                            $i_total_score_sum = array_sum($ai_sum_score_sum);
                            $i_total_score_merit = array_sum($ai_sum_score_merit);
                            $i_total_score_quiz = array_sum($ai_sum_score_quiz);
                            $i_total_score_final_exam = array_sum($ai_sum_score_final_exam);

                            $i_ips = $this->grades->get_ipk($i_total_score_merit, $i_total_credit);
                            $i_average_quiz = $i_total_score_quiz / count($ai_sum_score_quiz);
                            $i_average_final_exam = $i_total_score_final_exam / count($ai_sum_score_final_exam);
                            $i_average_score_sum = $i_total_score_sum / count($ai_sum_score_sum);
                            $i_average_grade = $this->grades->get_grade($i_average_score_sum);
                            $i_average_grade_point = $this->grades->get_grade_point($i_average_score_sum);

                            $sheet->setCellValue('G' . ($i_start_score), $i_average_quiz) 
                                -> setCellValue('H' . ($i_start_score), $i_average_final_exam) 
                                -> setCellValue('J' . ($i_start_score), $i_average_score_sum) 
                                -> setCellValue('K' . ($i_start_score), $i_average_grade) 
                                -> setCellValue('L' . ($i_start_score), $i_average_grade_point) 
                                -> setCellValue('M' . ($i_start_score + 1), $i_total_credit) 
                                -> setCellValue('N' . ($i_start_score + 1), $i_total_score_merit) 
                                -> setCellValue('G' . ($i_start_score + 2), round($i_ips, 2, PHP_ROUND_HALF_UP))
                                -> setCellValue('G' . ($i_start_score + 3), round($i_cumulative_gpa, 2, PHP_ROUND_HALF_UP));
                            
                            $i_start_score += 7;
                            if ($mba_score_extracul) {
                                $i_no_extr = 0;
                                foreach ($mba_score_extracul as $score_extr) {
                                    $score_sum = intval(round($score_extr->score_sum, 0, PHP_ROUND_HALF_UP));
                                    $score_grade = $this->grades->get_grade($score_sum);
                                    $score_grade_point = $this->grades->get_grade_point($score_sum);
                                    $score_merit = $this->grades->get_merit($score_extr->curriculum_subject_credit, $score_grade_point);

                                    $sheet->insertNewRowBefore($i_start_score + 1, 1);
                                    $sheet->setCellValue('B'.$i_start_score, ++$i_no_extr)
                                        ->setCellValue('C'.$i_start_score, $score_extr->subject_name)
                                        ->setCellValue('G'.$i_start_score, intval(round($score_extr->score_quiz, 0, PHP_ROUND_HALF_UP)))
                                        ->setCellValue('H'.$i_start_score, intval(round($score_extr->score_final_exam, 0, PHP_ROUND_HALF_UP)))
                                        ->setCellValue('I'.$i_start_score, (is_null($score_extr->score_repetition_exam)) ? '-' : intval(round($score_extr->score_repetition_exam, 0, PHP_ROUND_HALF_UP)))
                                        ->setCellValue('J'.$i_start_score, $score_sum)
                                        ->setCellValue('K'.$i_start_score, $score_grade)
                                        ->setCellValue('L'.$i_start_score, $score_grade_point)
                                        ->setCellValue('M'.$i_start_score, $score_extr->curriculum_subject_credit)
                                        ->setCellValue('N'.$i_start_score, $score_merit)
                                        ->setCellValue('O'.$i_start_score, $score_extr->score_absence.' %')
                                        ->setCellValue('P'.$i_start_score, (($score_grade == 'F') OR (intval($score_extr->score_absence) > 25)) ? 'FAIL' : '-');
                                    $i_start_score++;
                                }
                                $sheet->removeRow($i_start_score, 1);
                            }else{
                                $i_start_score++;
                            }

                            $sheet->setCellValue('B' . ($i_start_score + 1), $s_header_sign_title);
                            $sheet->setCellValue('B' . ($i_start_score + 5), $s_header_sign_name);
                            $i_start_score += (5 + 18);
                            // $sheet->setCellValue('B' . $i_start_score, 'Digital Signature : '.$s_token);
                        }

                        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xls');
                        $o_writer->save($s_transcript_path.$s_file_name);
                        $o_spreadsheet->disconnectWorksheets();
                        unset($o_spreadsheet);

                        shell_exec('/usr/bin/soffice --headless --convert-to pdf ' . $s_transcript_path.$s_file_name . ' --outdir ' . $s_transcript_path);

                        // $file_pdf = $filename_clean[0] . '.pdf';
                        $s_file_name = $s_file.'.pdf';

                        return array(
                            'filename' => $s_file_name,
                            'file_path' => $s_transcript_path.$s_file_name
                        );
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function generate_transcript_krs_semester($s_student_id, $s_academic_year_id, $s_semester_type_id)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        if ($mbo_student_data) {
            $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
            $s_vice_rector_name = '';
            $s_vice_rector_email = '';
            if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
                $s_vice_rector_email = $mbo_vice_rector_data->employee_email;
            }

            $mbo_faculty_data = $this->Spm->get_faculty_data(array('faculty_id' => $mbo_student_data->faculty_id))[0];
            $personal_data_deans = $this->Pdm->get_personal_data_by_id($mbo_faculty_data->deans_id);
            $personal_data_hod = $this->Pdm->get_personal_data_by_id($mbo_student_data->head_of_study_program_id);
            $s_hod_name = $this->Pdm->retrieve_title($mbo_student_data->head_of_study_program_id);
            $mba_study_program_data = $this->Spm->get_study_program($mbo_student_data->study_program_id, false)[0];
            $s_deans_name = ($personal_data_deans) ? $this->Pdm->retrieve_title($personal_data_deans->personal_data_id) : '';

            // $s_header_sign_name = (is_null($mbo_student_data->head_of_study_program_id)) ? $s_vice_rector_name : $s_hod_name;
            // $s_header_sign_title = (is_null($mbo_student_data->head_of_study_program_id)) ? 'Vice Rector Academic' : 'Head of Study Program of '.$mba_study_program_data->study_program_name;

            $s_header_sign_name = (empty($s_deans_name)) ? $s_vice_rector_name : $s_deans_name;
            $s_header_sign_title = (empty($s_deans_name)) ? 'Vice Rector Academic' : 'Dean of '.$mbo_faculty_data->faculty_name;

            $a_score_filter = array(
                // 'st.personal_data_id' => $mbo_student_data->personal_data_id,
                'st.student_id' => $mbo_student_data->student_id,
                'sc.academic_year_id' => $s_academic_year_id,
                'sc.semester_type_id' => $s_semester_type_id,
                'sc.score_approval' => 'approved',
                'sc.score_display' => 'TRUE'
            );
            $mba_score_data = $this->Scm->get_score_data($a_score_filter);
            $mba_score_all = $this->Scm->get_score_data(array(
                // 'st.personal_data_id' => $mbo_student_data->personal_data_id,
                'st.student_id' => $mbo_student_data->student_id,
                'sc.score_approval' => 'approved',
                'sc.score_display' => 'TRUE',
                'curs.curriculum_subject_type !=' => 'extracurricular',
                'curs.curriculum_subject_credit > ' => 0
            ));

            if ($mba_score_data) {
                $s_token = md5($mbo_student_data->student_id.time().rand(1000,4000));
                if($this->Smm->save_student_semester(array('student_semester_transcript_token' => $s_token), array(
                    'student_id' => $mbo_student_data->student_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'student_semester_status' => 'active'
                ))) {
                // if($this->Stm->update_student_data(array('student_semester_transcript_token' => $s_token), $mbo_student_data->student_id)) {
                    $i_all_total_credit = 0;
                    $i_all_total_merit = 0;
                    $a_score_filter_extracur = $a_score_filter;
                    $a_score_filter_extracur['curs.curriculum_subject_type'] = 'extracurricular';
                    $a_score_filter_non_extracul = $a_score_filter;
                    $a_score_filter_non_extracul['curs.curriculum_subject_type !='] = 'extracurricular';

                    if ($mba_score_all) {
                        foreach ($mba_score_all as $all_score) {
                            $b_execute_cummulative_gpa = false;
                            if (($all_score->academic_year_id < $s_academic_year_id)) {
                                $b_execute_cummulative_gpa = true;
                            }else if (($all_score->academic_year_id == $s_academic_year_id)) {
                                if ($all_score->semester_type_id <= $s_semester_type_id) {
                                    $b_execute_cummulative_gpa = true;
                                }
                            }

                            if ($b_execute_cummulative_gpa) {
                                $score_sum = intval(round($all_score->score_sum, 0, PHP_ROUND_HALF_UP));
                                $score_grade_point = $this->grades->get_grade_point($score_sum);
                                $score_merit = $this->grades->get_merit($all_score->curriculum_subject_credit, $score_grade_point);

                                // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                                //     print($all_score->subject_name.' / '.$all_score->curriculum_subject_credit.'<br>');
                                // }
                                $i_all_total_credit += $all_score->curriculum_subject_credit;
                                $i_all_total_merit += $score_merit;
                            }
                        }
                    }

                    $s_semester_academic = $s_academic_year_id.''.$s_semester_type_id;
                    // var_dump($s_semester_academic);exit;
                    $mbo_semester_details = $this->Smm->get_semester_setting(array('dss.academic_year_id' => $s_academic_year_id, 'dss.semester_type_id' => $s_semester_type_id))[0];
                    $s_file = $mbo_student_data->student_number.'_Final-KRS_'.$s_semester_academic.'_'.str_replace(' ','-', str_replace("&#039;", "", $mbo_student_data->personal_data_name));
                    $s_file_name = $s_file.'.xls';
                    $s_transcript_path = APPPATH.'/uploads/academic/'.$s_semester_academic.'/transcript/'.$mbo_student_data->study_program_abbreviation.'/';
                    // $s_template_path = APPPATH.'uploads/templates/transcript-final-temp.xls';
                    $s_template_path = APPPATH.'uploads/templates/krs-final.xls';

                    $ai_sum_score_sum = array();
                    $ai_sum_score_merit = array();
                    $ai_sum_score_credit = array();
                    $ai_sum_score_quiz = array();
                    $ai_sum_score_final_exam = array();
                    $mba_score_non_extracul = $this->Scm->get_score_data($a_score_filter_non_extracul);
                    $mba_score_extracul = $this->Scm->get_score_data($a_score_filter_extracur);
                    $s_digital_sign = md5($mbo_student_data->student_number . $s_academic_year_id. $s_semester_type_id . microtime());

                    $save_digital_sign = $this->Stm->update_student_data(array(
                        'student_transcript_token' => $s_digital_sign
                    ), $mbo_student_data->student_id);

                    $save_sign = $this->Stm->save_sign_data([
                        'student_id' => $s_student_id,
                        'document_type' => 'transcript_semester', 
                        'document_sign' => $s_digital_sign
                    ]);

                    if ($save_digital_sign AND $save_sign) {
                        $i_start_score = 19;
                        $i_cumulative_gpa = $this->grades->get_ipk($i_all_total_merit, $i_all_total_credit);
                        // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                        //     print('<h1>Total :'.$i_all_total_merit.'|'.$i_all_total_credit.'</h1><br>');
                        // }
                        $iRow = 16;

                        if(!file_exists($s_transcript_path)){
                            mkdir($s_transcript_path, 0777, TRUE);
                        }

                        $o_spreadsheet = IOFactory::load($s_template_path);
                        $o_sheet = $o_spreadsheet->getActiveSheet();
                        $o_spreadsheet->getProperties()
                            ->setTitle($s_file_name)
                            ->setCreator("IULI Academic Services")
                            ->setCategory("KRS Semester Report");

                        $sheet = $o_spreadsheet->setActiveSheetIndexByName("master");
                        $sheet->setCellValue('B9', 'Semester ' . $s_semester_academic . ' / Academic Year ' . $s_academic_year_id. '-' . (intval($s_academic_year_id) + 1));
                        $sheet->setCellValue('B10', date('d F Y', strtotime($mbo_semester_details->semester_start_date)).' - '.date('d F Y', strtotime($mbo_semester_details->semester_end_date)));
                        $sheet->setCellValue('E12', str_replace("&#039;", "'", $mbo_student_data->personal_data_name));
                        $sheet->setCellValue('E13', ucfirst(strtolower($mbo_student_data->personal_data_place_of_birth)) . ', ' . date('j M Y ', strtotime($mbo_student_data->personal_data_date_of_birth)));
                        $sheet->setCellValue('E14', $mbo_student_data->student_number);
                        // $sheet->setCellValue('E15', $mbo_student_data->faculty_name . ' / ' . $mbo_student_data->study_program_name);
                        $sheet->setCellValue('B15', 'Study Program');
                        $sheet->setCellValue('E15', $mbo_student_data->study_program_name);
                        $sheet->setCellValue('E' . $iRow, date('j F o', time()));

                        if ($mba_score_non_extracul) {
                            $no = 0;
                            foreach ($mba_score_non_extracul as $score) {
                                $score_sum = intval(round($score->score_sum, 0, PHP_ROUND_HALF_UP));
                                $score_grade = $this->grades->get_grade($score_sum);
                                $score_grade_point = $this->grades->get_grade_point($score_sum);
                                $score_merit = $this->grades->get_merit($score->curriculum_subject_credit, $score_grade_point);

                                $sheet->insertNewRowBefore($i_start_score + 1, 1);
                                $sheet->mergeCells('C' . $i_start_score . ':F' . $i_start_score);
                                $sheet
                                    ->setCellValue('B'.$i_start_score, ++$no)
                                    ->setCellValue('C'.$i_start_score, $score->subject_name)
                                    ->setCellValue('P'.$i_start_score, $score->curriculum_subject_credit);
                                $i_start_score++;
                                $i_end_score_data = $i_start_score;

                                array_push($ai_sum_score_credit, $score->curriculum_subject_credit);
                            }

                            $sheet->removeRow($i_start_score, 1);
                            $i_total_credit = array_sum($ai_sum_score_credit);

                            $sheet->setCellValue('P' . ($i_start_score), $i_total_credit);
                            
                            $i_start_score += 4;
                            if ($mba_score_extracul) {
                                $i_no_extr = 0;
                                $first_score_excul = $i_start_score;
                                $i_start_score++;
                                foreach ($mba_score_extracul as $score_extr) {
                                    $sheet->insertNewRowBefore($i_start_score, 1);
                                    $sheet->setCellValue('B'.$i_start_score, ++$i_no_extr)
                                        ->setCellValue('C'.$i_start_score, $score_extr->subject_name)
                                        ->setCellValue('P'.$i_start_score, $score_extr->curriculum_subject_credit);
                                    $i_start_score++;
                                }
                                $sheet->removeRow($i_start_score, 1);
                                $sheet->removeRow($first_score_excul, 1);
                            }else{
                                $i_start_score++;
                            }

                            $sheet->setCellValue('B' . ($i_start_score + 1), $s_header_sign_title);
                            $sheet->setCellValue('B' . ($i_start_score + 5), $s_header_sign_name);
                            $i_start_score += (5 + 18);
                            $sheet->setCellValue('B' . $i_start_score, ' ');
                        }

                        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xls');
                        $o_writer->save($s_transcript_path.$s_file_name);
                        $o_spreadsheet->disconnectWorksheets();
                        unset($o_spreadsheet);

                        shell_exec('/usr/bin/soffice --headless --convert-to pdf ' . $s_transcript_path.$s_file_name . ' --outdir ' . $s_transcript_path);

                        // $file_pdf = $filename_clean[0] . '.pdf';
                        $s_file_name = $s_file.'.pdf';

                        return array(
                            'filename' => $s_file_name,
                            'file_path' => $s_transcript_path.$s_file_name
                        );
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function bypass_download_excel($s_class_master_id)
    {
        // $s_class_master_id = '97aa7c92-39cd-47ae-befb-b26797ccdc4b';
        $mba_class_master_data = modules::run('academic/class_group/get_class_master_details', $s_class_master_id);
        // $mba_class_master_data = $this->get_class_master_details($s_class_master_id);
        $a_file = $this->generate_score_template($mba_class_master_data);
        if ($a_file) {
            header('Content-Disposition: attachment; filename='.urlencode($a_file['filename']));
            readfile( $a_file['file_path'] .urlencode($a_file['filename']) );
        }
        print('<pre>');
        var_dump($a_file);
        exit;
    }

    public function generate_score_template($mba_class_data = false)
    {
        if ($mba_class_data) {
            $s_filename = 'Score_template-'.str_replace(' ', '_', $mba_class_data->subject_name).'-'.$mba_class_data->running_year.'-'.$mba_class_data->class_semester_type_id.'.xlsx';
            $s_protected = 'IULI-ACADEMIC';
            $s_absence_template_name = 'score_template.xlsx';
            $s_template_path = APPPATH.'uploads/templates/'.$s_absence_template_name;
            $s_file_path = APPPATH.'uploads/templates/score_class/';

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_filename)
                ->setCreator("IULI Academic Services")
                ->setCategory("Score Template");

            $o_sheet->getProtection()->setSheet(true);
            $o_spreadsheet->getDefaultStyle()->getProtection()->setLocked(false);
            $o_sheet->getStyle('A1')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);

            
            // $i_month = date('n', time());
            $o_sheet->setCellValue('C3', implode(' / ', $mba_class_data->team_teaching_lists));
            $o_sheet->setCellValue('C4', implode(' / ', $mba_class_data->class_prodi));
            $o_sheet->setCellValue('C5', $mba_class_data->running_year.''.$mba_class_data->class_semester_type_id);
            $o_sheet->setCellValue('C6', $mba_class_data->class_master_id);
            $o_sheet->setCellValue('A7', 'Subject: '.$mba_class_data->subject_name);
            $o_sheet->protectCells('C3:C6', $s_protected);
            
            $i_student_row = $i_start_row = 12;
            $i_numbering = 1;
            if ($mba_class_data->student_lists) {
                foreach ($mba_class_data->student_lists as $class_member) {
                    // if ($class_member->student_status == 'active') {
                        $o_sheet->insertNewRowBefore($i_student_row + 1, 1);
                        $o_sheet->setCellValue('A'.$i_student_row, $i_numbering);
                        $o_sheet->setCellValue('B'.$i_student_row, str_replace('&#039;', "'", $class_member->personal_data_name));
                        $o_sheet->setCellValue('C'.$i_student_row, $class_member->student_number);
                        $o_sheet->setCellValue('D'.$i_student_row, $class_member->score_quiz1);
                        $o_sheet->setCellValue('E'.$i_student_row, $class_member->score_quiz2);
                        $o_sheet->setCellValue('F'.$i_student_row, $class_member->score_quiz3);
                        $o_sheet->setCellValue('G'.$i_student_row, '=IF(B'.$i_student_row.'="","",IF(COUNTA($D$12:$F$'.$i_student_row.')=0,"",IF(ISERROR(AVERAGE(D'.$i_student_row.':F'.$i_student_row.')),0,ROUND(AVERAGE(D'.$i_student_row.':F'.$i_student_row.'),0))))');
                        $o_sheet->setCellValue('H'.$i_student_row, $class_member->score_quiz4);
                        $o_sheet->setCellValue('I'.$i_student_row, $class_member->score_quiz5);
                        $o_sheet->setCellValue('J'.$i_student_row, $class_member->score_quiz6);
                        $o_sheet->setCellValue('K'.$i_student_row, '=IF(G' .$i_student_row . '="","",IF(AND(G' .$i_student_row . '=0,COUNTA(H' .$i_student_row . ':J' .$i_student_row . ')=0),0,ROUND(AVERAGE(D' .$i_student_row . ':F' .$i_student_row . ',H' .$i_student_row . ':J' .$i_student_row . '),0)))');
                        $o_sheet->setCellValue('L'.$i_student_row, $class_member->score_final_exam);
                        $o_sheet->setCellValue('M'.$i_student_row, $class_member->score_repetition_exam);
                        $o_sheet->setCellValue('N'.$i_student_row, '=IF(COUNTA($L$' .$i_student_row . ':$L$' .$i_student_row . ')=0,"",IF(K' .$i_student_row . '="","",IF(AND(COUNTA($L$' .$i_student_row . ':$L$' .$i_student_row . ')>0,COUNTA(L' .$i_student_row . ':M' .$i_student_row . ')=0),K' .$i_student_row . '*0.4,ROUND(((ROUND(K' .$i_student_row . ',0)*0.4)+ROUND(LARGE(L' .$i_student_row . ':M' .$i_student_row . ',1),0)*0.6),0))))');
                        $o_sheet->setCellValue('O'.$i_student_row, '=IF(N' .$i_student_row . '="","",IF(N' .$i_student_row . '>=86,"A",IF(N' .$i_student_row . '>=71,"B",IF(N' .$i_student_row . '>=56,"C",IF(N' .$i_student_row . '>=46,"D","F")))))');
                        $i_student_row++;
                        $i_numbering++;
                    // }
                }
                $o_sheet->removeRow($i_student_row, 1);
            }
            
            $o_sheet->setCellValue('O' . ($i_student_row + 7), '(' . implode(' / ', $mba_class_data->team_teaching_lists) . ')');
            $o_sheet->setCellValue('O'.($i_student_row + 1), 'Bumi Serpong Damai, '.date('j F Y', time()));
            $o_sheet->getStyle('O'.($i_student_row + 1).':O'.($i_student_row + 7))->getAlignment()->setHorizontal('right');
            $o_sheet->protectCells('G'.$i_start_row.':G'.$i_student_row, $s_protected);
            $o_sheet->protectCells('K'.$i_start_row.':K'.$i_student_row, $s_protected);
            $o_sheet->protectCells('N'.$i_start_row.':N'.$i_student_row, $s_protected);
            $o_sheet->protectCells('O'.$i_start_row.':O'.$i_student_row, $s_protected);

            // print($i_start_row);exit;

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $s_file_save = urlencode($s_filename);
            $o_writer->save($s_file_path.$s_file_save);

            return array(
                'filename' => $s_filename,
                'file_saved' => $s_file_save,
                'file_path' => $s_file_path
            );
        }else{
            return false;
        }
    }

    public function generate_absence_template($s_class_master_id = false)
    {
        if ($s_class_master_id) {
            $mba_class_master_data = $this->Cgm->get_class_master_filtered(array('cm.class_master_id' => $s_class_master_id))[0];
            if ($mba_class_master_data) {
                $mba_class_lecturer_lists = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $s_class_master_id));
                $mbo_class_study_program_lists = $this->Cgm->get_class_master_study_program($s_class_master_id);
                $a_team_teaching_lists = array();
                $a_class_prodi = array();
                $a_lect_email = array();
                
                if ($mba_class_lecturer_lists) {
                    foreach ($mba_class_lecturer_lists as $lecturer) {
                        $s_lecturer = $this->Pdm->retrieve_title($lecturer->personal_data_id);
                        array_push($a_team_teaching_lists, $s_lecturer);
                        array_push($a_lect_email, $lecturer->employee_email);
                    }
                }

                if ($mbo_class_study_program_lists) {
                    foreach ($mbo_class_study_program_lists as $class_prodi) {
                        array_push($a_class_prodi, $class_prodi->study_program_abbreviation);
                    }
                }

                $s_filename = 'Absence class '.$mba_class_master_data->subject_name.' - '.$mba_class_master_data->running_year.' - '.$mba_class_master_data->class_semester_type_id.'.xlsx';
                $s_absence_template_name = 'absence-template.xls';
                $s_template_path = APPPATH.'uploads/templates/'.$s_absence_template_name;
                $s_file_path = APPPATH.'uploads/templates/absence_class/';

                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $o_spreadsheet = IOFactory::load($s_template_path);
                $o_sheet = $o_spreadsheet->getActiveSheet();
                $o_spreadsheet->getProperties()
                ->setTitle($s_filename)
                ->setCreator("IULI Academic")
                ->setCategory("Absence Template");
                
                $i_month = date('n', time());
                $o_sheet->setCellValue('A6', 'Bumi Serpong Damai, '.date('j F Y', time()));

                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
                $o_writer->save($s_file_path.$s_filename);
                
                // print('<pre>');
                // print_r($mba_class_master_data);
                // redirect('academic/class_group/download_absence_template');
                redirect('file_manager/download_template/'.$s_filename);
            }
        }
    }

    public function generate_class_report_old($s_class_master_id, $s_employee_id = false)
    {
        $mbo_class_master_data = $this->Cgm->get_class_master_filtered(['cm.class_master_id' => $s_class_master_id])[0];
        if ($mbo_class_master_data) {
            $s_template_path = APPPATH.'uploads/templates/pak_tjandra_template.xlsx';
            $s_file_path = APPPATH."uploads/academic/pak_tjandra/".$mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id."/";
            $s_filename = str_replace(' ', '-', strtolower($mbo_class_master_data->subject_name));

            $s_filename .= '_'.$mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id.'_'.$mbo_class_master_data->class_master_id;
            $s_file_name = $s_filename.'.xlsx';

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }
            
            $o_spreadsheet = IOFactory::load($s_template_path);
            // $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_sheet->setActiveSheetIndexByName('student_absence');
            $o_spreadsheet->getProperties()
                ->setTitle('Class Report '.$s_student_name)
                ->setCreator("IULI Academic Services")
                ->setLastModifiedBy("IULI Academic Services")
                ->setCategory("Class Report");

            $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($s_class_master_id, false, 'ASC');
            $mba_student_list = $this->Cgm->get_class_master_student($s_class_master_id);

            $i_row = 1;
            $i_col = 'A';
            if ($mba_uosd_list) {
                foreach ($mba_uosd_list as $o_uosd) {
                    $o_sheet->setCellValue('C6', $mba_class_data->class_master_id);
                }
            }
        }
    }

    public function generate_class_report($s_class_master_id, $s_employee_id = false)
    {
        // if ($this->input->is_ajax_request()) {
        //     $s_class_master_id = $this->input->post('class_master_id');
        //     $s_employee_id = $this->input->post('class_master_id');

        //     if (($s_employee_id === null) OR ($s_employee_id == '')) {
        //         $s_employee_id = false;
        //     }
        //     print('<pre>');
        //     var_dump($s_employee_id);
        // exit;
            $mbo_class_master_data = $this->Cgm->get_class_master_filtered(['cm.class_master_id' => $s_class_master_id])[0];

            if ($mbo_class_master_data) {
                $s_folder_name = $mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id.'_Class_Report_'.str_replace(' ', '-', strtolower($mbo_class_master_data->subject_name));
                $s_folder_name = str_replace('&amp;', 'and', $s_folder_name);
                $s_folder_name = str_replace('&', 'and', $s_folder_name);
                $s_folder_name = str_replace('/', '-', $s_folder_name);
                $s_path_master = APPPATH."uploads/academic/class_reporting/".$mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id."/";
                // $s_path_master = APPPATH."uploads/academic/class_reporting/";
                $s_file_path = $s_path_master.$s_folder_name."/";
                // print('<pre>');
                // var_dump($s_file_path);exit;
                
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $this->load->library('zip');

                $mba_class_master_study_program = $this->Cgm->get_class_master_study_program($s_class_master_id);

                $a_file_report = [];

                $a_study_program_id = [];
                if ($mba_class_master_study_program) {
                    foreach ($mba_class_master_study_program as $o_class_study_program) {
                        if (!in_array($o_class_study_program->study_program_id, $a_study_program_id)) {
                            array_push($a_study_program_id, $o_class_study_program->study_program_id);
                            $s_student_score_report_file = $this->generate_student_score_report($mbo_class_master_data, $s_file_path, $o_class_study_program->study_program_id, $s_employee_id);
                            $s_student_absence_report_file = $this->generate_student_absence_report($mbo_class_master_data, $s_file_path, $o_class_study_program->study_program_id, $s_employee_id);
                            $s_lecturer_absence_report_file = $this->generate_lecturer_absence_report($mbo_class_master_data, $s_file_path, $o_class_study_program->study_program_id, $s_employee_id);
                            
                            // array_push($a_file_report, $s_student_score_report_file.'.xlsx');
                            // array_push($a_file_report, $s_student_absence_report_file.'.xlsx');
                            // array_push($a_file_report, $s_lecturer_absence_report_file.'.xlsx');
                            // array_push($a_file_report, $s_student_score_report_file.'.pdf');
                            // array_push($a_file_report, $s_student_absence_report_file.'.pdf');
                            // array_push($a_file_report, $s_lecturer_absence_report_file.'.pdf');

                            $this->zip->read_file($s_file_path.$s_student_score_report_file.'.xlsx');
                            $this->zip->read_file($s_file_path.$s_student_absence_report_file.'.xlsx');
                            $this->zip->read_file($s_file_path.$s_lecturer_absence_report_file.'.xlsx');
                            
                            $this->zip->read_file($s_file_path.$s_student_score_report_file.'.pdf');
                            $this->zip->read_file($s_file_path.$s_student_absence_report_file.'.pdf');
                            $this->zip->read_file($s_file_path.$s_lecturer_absence_report_file.'.pdf');
                        }
                    }
                }

                // $a_return = [
                //     'code' => 0,
                //     'file_name' => $s_folder_name,
                //     'content_report' => $a_file_report,
                //     'semester_academic' => $mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id
                // ];
                // print('<pre>');
                // var_dump($s_file_path);exit;

                $this->zip->download($s_folder_name.'.zip');
            }
            // else{
            //     $a_return = ['code' => 1, 'message' => 'Class not found'];
            // }

        //     print json_encode($a_return);
        // }
    }

    public function generate_lecturer_absence_report($o_class_master_data, $path_file, $s_study_program_id, $s_employee_id = false)
    {
        $s_template_path = APPPATH.'uploads/templates/lecturer_absence_template.xlsx';
        $mbo_study_program_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
        if (!$mbo_study_program_data) {
            print($s_study_program_id);exit;
        }

        $s_file_name_subject = str_replace(' ', '-', strtolower($o_class_master_data->subject_name));
        $s_file_name_subject = str_replace('&amp;', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('&', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('/', '-', $s_file_name_subject);
        $s_semester_akademic = $o_class_master_data->running_year.$o_class_master_data->class_semester_type_id;
        $s_file_name = 'Lecturer_absence_'.$s_file_name_subject.'_'.$mbo_study_program_data->study_program_abbreviation.'_'.$s_semester_akademic.'_'.$o_class_master_data->class_master_id;
        $s_filename = $s_file_name.'.xlsx';

        $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
            ->setTitle($s_filename)
            ->setCreator("IULI Academic Services")
            ->setCategory("Student Absence Report");

        
        $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer(['class_master_id' => $o_class_master_data->class_master_id]);
        $a_lecturer_name = [];
        if ($mba_class_master_lecturer) {
            foreach ($mba_class_master_lecturer as $o_lecturer) {
                if (!in_array($o_lecturer->personal_data_name, $a_lecturer_name)) {
                    if (($s_employee_id) AND ($o_lecturer->employee_id == $s_employee_id)) {
                        array_push($a_lecturer_name, $o_lecturer->personal_data_name);
                    }else if(!$s_employee_id){
                        array_push($a_lecturer_name, $o_lecturer->personal_data_name);
                    }
                }
            }
        }

        $i_subject_wrap = $this->check_wrapp_text($o_class_master_data->subject_name);
        $d_subject_height = ($i_subject_wrap + 1) * 15.75;

        $i_lect_wrap = $this->check_wrapp_text(implode(' / ', $a_lecturer_name));
        $d_lect_height = ($i_lect_wrap + 1) * 15.75;
        
        $o_sheet->setCellValue('B3', ': '.implode(' / ', $a_lecturer_name));
        $o_sheet->setCellValue('B4', ': '.$o_class_master_data->subject_name);
        $o_sheet->setCellValue('B5', ': '.$s_semester_akademic);

        $o_sheet->getRowDimension(3)->setRowHeight($d_lect_height);
        $o_sheet->getStyle('B3')->getAlignment()->setWrapText(true);

        $o_sheet->getRowDimension(4)->setRowHeight($d_subject_height);
        $o_sheet->getStyle('B4')->getAlignment()->setWrapText(true);

        $o_sheet->mergeCells('B3:C3');
        $o_sheet->mergeCells('B4:C4');

        $i_row = 8;

        if ($s_employee_id) {
            $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($o_class_master_data->class_master_id, [
                'cgsm.employee_id' => $s_employee_id
            ], 'ASC');
        }else{
            $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($o_class_master_data->class_master_id, false, 'ASC');
        }

        if ($mba_uosd_list) {
            foreach ($mba_uosd_list as $o_uosd) {
                $i_wrap = $this->check_wrapp_text($o_uosd->subject_delivered_description, 67);
                $dheight = ($i_wrap >= 1) ? 12.75 : 15.75;
                $d_height = ($i_wrap + 1) * $dheight;

                $o_sheet->setCellValue('A'.$i_row, date('d M Y H:i', strtotime($o_uosd->subject_delivered_time_start)));
                $o_sheet->setCellValue('B'.$i_row, date('d M Y H:i', strtotime($o_uosd->subject_delivered_time_start.'+1 hour')));
                $o_sheet->setCellValue('C'.$i_row, str_replace('&amp;', ' and ', $o_uosd->subject_delivered_description));

                $o_sheet->getRowDimension($i_row)->setRowHeight($d_height);
                // $o_sheet->getRowDimension($i_row)->setRowHeight(-1);
                $o_sheet->getStyle('C'.$i_row)->getAlignment()->setWrapText(true);

                $i_row++;
                $o_sheet->insertNewRowBefore($i_row, 1);
            }
        }

        $i_row += 6;
        // $o_sheet->setCellValue('A'.$i_row, 'Prepared by');
        // $o_sheet->setCellValue('C'.$i_row, 'Acknowledge by');
        // $i_row += 4;
        if ($s_employee_id) {

            $mbo_employee_data = $this->Emm->get_employee_data(['employee_id' => $s_employee_id])[0];
            $s_lecturer = $this->Pdm->retrieve_title($mbo_employee_data->personal_data_id);
            $o_sheet->setCellValue('A'.$i_row, $s_lecturer);
            $o_sheet->getStyle('A'.$i_row)->getAlignment()->setHorizontal('left');
        }

        $s_deans = $this->Pdm->retrieve_title($mbo_study_program_data->deans_id);
        // $o_sheet->setCellValue('C'.$i_row, $s_deans);
        $o_sheet->setCellValue('C'.$i_row, "Suhendin");
        $o_sheet->getStyle('A'.$i_row)->getFont()->setUnderline(true);
        $o_sheet->getStyle('C'.$i_row)->getFont()->setUnderline(true);
        $i_row++;
        $o_sheet->setCellValue('A'.$i_row, 'Lecturer');
        // $o_sheet->setCellValue('C'.$i_row, 'Head of Department / Dean');
        $o_sheet->setCellValue('C'.$i_row, 'Head of Academic Service Centre');

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($path_file.$s_filename);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        shell_exec('/usr/bin/soffice --headless --convert-to pdf ' . $path_file.$s_filename . ' --outdir ' . $path_file);
        return $s_file_name;
    }
    
    public function generate_student_absence_report($o_class_master_data, $path_file, $s_study_program_id, $s_employee_id = false)
    {
        $s_template_path = APPPATH.'uploads/templates/student_absence_template.xlsx';
        $mbo_study_program_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
        if (!$mbo_study_program_data) {
            print($s_study_program_id);exit;
        }

        $s_file_name_subject = str_replace(' ', '-', strtolower($o_class_master_data->subject_name));
        $s_file_name_subject = str_replace('&amp;', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('&', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('/', '-', $s_file_name_subject);
        $s_semester_akademic = $o_class_master_data->running_year.$o_class_master_data->class_semester_type_id;
        $s_file_name = 'Student_absence_'.$s_file_name_subject.'_'.$mbo_study_program_data->study_program_abbreviation.'_'.$s_semester_akademic.'_'.$o_class_master_data->class_master_id;
        $s_filename = $s_file_name.'.xlsx';

        $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
            ->setTitle($s_filename)
            ->setCreator("IULI Academic Services")
            ->setCategory("Student Absence Report");

        $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($o_class_master_data->class_master_id, false, 'ASC');
        $mba_student_list = $this->Cgm->get_class_master_student($o_class_master_data->class_master_id, ['st.study_program_id' => $s_study_program_id]);
        
        $i_subject_name_wrap = $this->check_wrapp_text($o_class_master_data->subject_name);
        $d_sn_height = ($i_subject_name_wrap + 1) * 15.75;

        $i_row = $i_start_row_page = 1;
        $i_page_row = 47;
        $i_page = 1;

        if ($mba_uosd_list) {
            foreach ($mba_uosd_list as $o_uosd) {
                $b_print = false;
                if (($s_employee_id) AND ($o_uosd->employee_id == $s_employee_id)) {
                    $b_print = true;
                }else if(!$s_employee_id){
                    $b_print = true;
                }

                if ($b_print) {
                    $o_sheet->setCellValue('A'.$i_row, 'STUDENT ABSENCE');
                    $o_sheet->getStyle('A'.$i_row)->getFont()->setBold( true );
                    $o_sheet->mergeCells('A'.$i_row.':E'.$i_row);
                    $o_sheet->getStyle('A'.$i_row)->getAlignment()->setHorizontal('center');

                    $i_row += 2;
                    $o_sheet->setCellValue('A'.$i_row, "Subject");
                    $o_sheet->setCellValue('B'.$i_row, ': '.$o_class_master_data->subject_name);
                    $o_sheet->getRowDimension($i_row)->setRowHeight($d_sn_height);
                    $o_sheet->getStyle('B'.$i_row)->getAlignment()->setWrapText(true);
                    $o_sheet->mergeCells('B'.$i_row.':E'.$i_row);
                    $i_row++;

                    $o_sheet->setCellValue('A'.$i_row, "Study Program");
                    $o_sheet->setCellValue('B'.$i_row, ': '.$mbo_study_program_data->study_program_name);
                    $i_row += 2;
                    
                    $o_sheet->setCellValue('A'.$i_row, "Lecturer");
                    $o_sheet->setCellValue('B'.$i_row, ': '.$o_uosd->personal_data_name);
                    $i_row++;

                    $o_sheet->setCellValue('A'.$i_row, "Date");
                    $o_sheet->setCellValue('B'.$i_row, ': '.(date('d F Y', strtotime($o_uosd->subject_delivered_time_start))));
                    $i_row++;

                    // $end_time = date('Y-m-d H:i:s', strtotime($o_uosd->subject_delivered_time_start));
                    // $end_time->add(new DateInterval('PT1H'));

                    $o_sheet->setCellValue('A'.$i_row, "Time Start");
                    $o_sheet->setCellValue('B'.$i_row, ': '.(date('H:i:s', strtotime($o_uosd->subject_delivered_time_start))));
                    $i_row++;
                    
                    $o_sheet->setCellValue('A'.$i_row, "Time End");
                    $o_sheet->setCellValue('B'.$i_row, ': '.(date('H:i:s', strtotime($o_uosd->subject_delivered_time_start."+1 hour"))));
                    $i_row++;
                    $s_topics_covered = str_replace('&amp;', '&', $o_uosd->subject_delivered_description);

                    $o_sheet->setCellValue('A'.$i_row, "Topics Covered");
                    $o_sheet->setCellValue('B'.$i_row, ': '.$s_topics_covered);
                    $i_row++;

                    $i_desc_wrap = $this->check_wrapp_text($s_topics_covered);
                    $d_desc_height = ($i_desc_wrap + 1) * 15.75;
                    $o_sheet->getRowDimension($i_row)->setRowHeight($d_desc_height);

                    $i_row++;
                    if ($mba_student_list){
                        $o_sheet->setCellValue('A'.$i_row, "Student Number");
                        $o_sheet->setCellValue('B'.$i_row, "Student Name");
                        $o_sheet->setCellValue('C'.$i_row, "Batch");
                        $o_sheet->setCellValue('D'.$i_row, "Absence");
                        $o_sheet->setCellValue('E'.$i_row, "Note");
                        $i_row_start_border = $i_row;
                        $i_row++;

                        foreach ($mba_student_list  as $o_student) {
                            $mbo_student_absence_data = $this->Cgm->get_absence_student(['score_id' => $o_student->score_id, 'subject_delivered_id' => $o_uosd->subject_delivered_id])[0];

                            $o_sheet->setCellValue('A'.$i_row, $o_student->student_number);
                            $o_sheet->setCellValue('B'.$i_row, $o_student->personal_data_name);
                            // $o_sheet->setCellValue('C'.$i_row, $o_student->study_program_name);
                            $o_sheet->setCellValue('C'.$i_row, $o_student->academic_year_id);
                            $o_sheet->setCellValue('D'.$i_row, ($mbo_student_absence_data) ? $mbo_student_absence_data->absence_status : 'PRESENT');
                            $o_sheet->setCellValue('E'.$i_row, ($mbo_student_absence_data) ? $mbo_student_absence_data->absence_description : '');

                            $o_sheet->getRowDimension($i_row)->setRowHeight(-1);
                            $o_sheet->getStyle('B'.$i_row)->getAlignment()->setWrapText(true);
                            $o_sheet->getStyle('E'.$i_row)->getAlignment()->setWrapText(true);

                            $o_sheet->getStyle("A$i_row:E$i_row")->getFont()->setSize(10);
                            $o_sheet->getStyle('A'.$i_row)->getAlignment()->setHorizontal('right');
                            $o_sheet->getStyle('C'.$i_row)->getAlignment()->setHorizontal('center');
                            $o_sheet->getStyle('D'.$i_row)->getAlignment()->setHorizontal('center');

                            $i_row++;
                            $o_sheet->insertNewRowBefore($i_row, 1);
                        }

                        $styleArray = array(
                            'borders' => array(
                                'allBorders' => array(
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['argb' => '00000000'],
                                )
                            )
                        );
                        
                        $o_sheet->getStyle('A'.$i_row_start_border.':E'.$i_row)->applyFromArray($styleArray);
                        // unset($styleArray);
                    }
                }

                $i_row += 4;
                
            }
        }

        $i_row += 2;
        $o_sheet->setCellValue('A'.$i_row, 'Prepared by');
        $o_sheet->setCellValue('D'.$i_row, 'Acknowledge by');
        $i_row += 4;
        if ($s_employee_id) {

            $mbo_employee_data = $this->Emm->get_employee_data(['employee_id' => $s_employee_id])[0];
            $s_lecturer = $this->Pdm->retrieve_title($mbo_employee_data->personal_data_id);
            $o_sheet->setCellValue('A'.$i_row, $s_lecturer);
            $o_sheet->getStyle('A'.$i_row)->getAlignment()->setHorizontal('left');
        }

        $s_deans = $this->Pdm->retrieve_title($mbo_study_program_data->deans_id);
        // $o_sheet->setCellValue('D'.$i_row, $s_deans);
        $o_sheet->setCellValue('D'.$i_row, 'Suhendin');
        $o_sheet->getStyle('A'.$i_row)->getFont()->setUnderline(true);
        $o_sheet->getStyle('D'.$i_row)->getFont()->setUnderline(true);
        $i_row++;
        $o_sheet->setCellValue('A'.$i_row, 'Lecturer');
        // $o_sheet->setCellValue('D'.$i_row, 'Head of Department / Dean');
        $o_sheet->setCellValue('D'.$i_row, 'Head of Academic Service Centre');

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($path_file.$s_filename);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        shell_exec('/usr/bin/soffice --headless --convert-to pdf ' . $path_file.$s_filename . ' --outdir ' . $path_file);
        return $s_file_name;
    }

    public function generate_student_score_report($o_class_master_data, $path_file, $s_study_program_id, $s_employee_id = false)
    {
        $s_template_path = APPPATH.'uploads/templates/student_score_template.xlsx';
        $mbo_study_program_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
        if (!$mbo_study_program_data) {
            print($s_study_program_id);exit;
        }

        $s_file_name_subject = str_replace(' ', '-', strtolower($o_class_master_data->subject_name));
        $s_file_name_subject = str_replace('&amp;', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('&', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('/', '-', $s_file_name_subject);
        $s_semester_akademic = $o_class_master_data->running_year.$o_class_master_data->class_semester_type_id;
        $s_file_name = 'Student_score_'.$s_file_name_subject.'_'.$mbo_study_program_data->study_program_abbreviation.'_'.$s_semester_akademic.'_'.$o_class_master_data->class_master_id;
        $s_filename = $s_file_name.'.xlsx';

        $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
            ->setTitle($s_filename)
            ->setCreator("IULI Academic Services")
            ->setCategory("Student Score Report");
        
        $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer(['class_master_id' => $o_class_master_data->class_master_id]);
        $a_lecturer_name = [];
        if ($mba_class_master_lecturer) {
            foreach ($mba_class_master_lecturer as $o_lecturer) {
                if (!in_array($o_lecturer->personal_data_name, $a_lecturer_name)) {
                    if (($s_employee_id) AND ($o_lecturer->employee_id == $s_employee_id)) {
                        array_push($a_lecturer_name, $o_lecturer->personal_data_name);
                    }else if(!$s_employee_id){
                        array_push($a_lecturer_name, $o_lecturer->personal_data_name);
                    }
                }
            }
        }

        $mba_score_list = $this->Cgm->get_class_master_student($o_class_master_data->class_master_id, [
            'st.study_program_id' => $s_study_program_id
        ]);

        $i_subject_name_wrap = $this->check_wrapp_text($o_class_master_data->subject_name);
        $d_sn_height = ($i_subject_name_wrap + 1) * 15.75;

        $i_study_prog_wrap = $this->check_wrapp_text($mbo_study_program_data->study_program_name);
        $d_height = ($i_study_prog_wrap + 1) * 15.75;

        $i_lect_wrap = $this->check_wrapp_text(implode(' / ', $a_lecturer_name));
        $d_lect_height = ($i_lect_wrap + 1) * 15.75;
        
        $o_sheet->setCellValue('B3', ': '.$o_class_master_data->subject_name);
        $o_sheet->setCellValue('B4', ': '.$mbo_study_program_data->study_program_name);
        $o_sheet->setCellValue('B5', ': '.implode(' / ', $a_lecturer_name));

        $o_sheet->getRowDimension(3)->setRowHeight($d_sn_height);
        $o_sheet->getStyle('B3')->getAlignment()->setWrapText(true);

        $o_sheet->getRowDimension(4)->setRowHeight($d_height);
        $o_sheet->getStyle('B4')->getAlignment()->setWrapText(true);

        $o_sheet->getRowDimension(5)->setRowHeight($d_lect_height);
        $o_sheet->getStyle('B5')->getAlignment()->setWrapText(true);

        $o_sheet->mergeCells('B3:G3');
        $o_sheet->mergeCells('B4:G4');
        $o_sheet->mergeCells('B5:G5');
        // $o_sheet->getRowDimension(12)->setRowHeight(-1);
        // $o_sheet->getStyle('B4')->getAlignment()->setWrapText(true);
        // $o_spreadsheet->getActiveSheet()->getStyle('B4')->getAlignment()->setWrapText(true);

        $i_row = 8;

        if ($mba_score_list) {
            foreach ($mba_score_list as $o_score) {
                $mbo_student_data = $this->Stm->get_student_by_id($o_score->student_id);
                $d_absence = 100 - (floatval((is_null($o_score->score_absence) ? 0 : $o_score->score_absence)));
                $d_repeat = (is_null($o_score->score_repetition_exam)) ? '-' : $o_score->score_repetition_exam;
                
                $o_sheet->setCellValue('A'.$i_row, $mbo_student_data->student_number);
                $o_sheet->setCellValue('B'.$i_row, $mbo_student_data->personal_data_name);
                // $o_sheet->setCellValue('C'.$i_row, $d_absence);
                $o_sheet->setCellValue('C'.$i_row, $o_score->score_quiz);
                $o_sheet->setCellValue('D'.$i_row, $o_score->score_final_exam);
                $o_sheet->setCellValue('E'.$i_row, $d_repeat);
                $o_sheet->setCellValue('F'.$i_row, $o_score->score_sum);
                $s_col = 'F';
                $o_sheet->setCellValue(++$s_col.$i_row, $o_score->score_grade);

                $o_sheet->getRowDimension($i_row)->setRowHeight(-1);
                $o_sheet->getStyle('B'.$i_row)->getAlignment()->setWrapText(true);

                $i_row++;
                $o_sheet->insertNewRowBefore($i_row, 1);
            }
            
        }

        $i_row += 6;
        if ($s_employee_id) {

            $mbo_employee_data = $this->Emm->get_employee_data(['employee_id' => $s_employee_id])[0];
            $s_lecturer = $this->Pdm->retrieve_title($mbo_employee_data->personal_data_id);
            $o_sheet->setCellValue('A'.$i_row, $s_lecturer);
            $o_sheet->getStyle('A'.$i_row)->getAlignment()->setHorizontal('left');
        }

        $s_deans = $this->Pdm->retrieve_title($mbo_study_program_data->deans_id);
        // $o_sheet->setCellValue('E'.$i_row, $s_deans);
        $o_sheet->setCellValue('E'.$i_row, 'Suhendin');
        $o_sheet->getStyle('A'.$i_row)->getFont()->setUnderline(true);
        $o_sheet->getStyle('E'.$i_row)->getFont()->setUnderline(true);
        $i_row++;
        $o_sheet->setCellValue('A'.$i_row, 'Lecturer');
        $o_sheet->setCellValue('E'.$i_row, 'Head of Academic Service Centre');

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($path_file.$s_filename);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        shell_exec('/usr/bin/soffice --headless --convert-to pdf ' . $path_file.$s_filename . ' --outdir ' . $path_file);
        return $s_file_name;
    }

    public function generate_class_meeting_report($s_academic_year_id, $s_semester_type_id)
    {
        $mba_class_master_list = $this->General->get_where('dt_class_master', [
            'academic_year_id' => $s_academic_year_id,
            'semester_type_id' => $s_semester_type_id
        ]);

        if ($mba_class_master_list) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

            $s_semester_academic = $s_academic_year_id.$s_semester_type_id;
            $s_file_name = 'Lecturer_Absence_Recapitulation_'.$s_semester_academic.'_'.date('Y-m-d');
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/academic/class_reporting/".$s_semester_academic."/SAP/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Academic Services")
                ->setCategory("Lecturer Absence Recapitulation");

            $o_sheet->setCellValue('A1', "Lecturer Recapitulation");
            $o_sheet->setCellValue('A2', "Academic Year ".$s_academic_year_id."-".$s_semester_type_id);

            $i_row = 4;
            $o_sheet->setCellValue('A'.$i_row, "Lecturer Name");
            $o_sheet->setCellValue('B'.$i_row, "Subject");
            $o_sheet->setCellValue('C'.$i_row, "Study Program");
            $o_sheet->setCellValue('D'.$i_row, "Class Token");
            $o_sheet->setCellValue('E'.$i_row, "Number of Student");
            $o_sheet->setCellValue('F'.$i_row, "Total Meeting");
            $o_sheet->setCellValue('G'.$i_row, "Date Time of Meeting");
            
            $i_row++;
            $i_max_cols_absence = 0;
            foreach($mba_class_master_list as $o_class_master) {
                $mba_student_member = $this->General->get_where('dt_score', [
                    'class_master_id' => $o_class_master->class_master_id,
                    'score_approval' => 'approved'
                ]);

                if ($mba_student_member) {
                    $mbo_class_master_subject = $this->Cgm->get_class_master_subject(['cm.class_master_id' => $o_class_master->class_master_id])[0];
                    if ($mbo_class_master_subject) {
                        $a_prodi_abbr = [];
                        $mba_class_master_class = $this->General->get_where('dt_class_master_class', ['class_master_id' => $o_class_master->class_master_id]);
                        
                        if ($mba_class_master_class) {
                            foreach ($mba_class_master_class as $o_class_master_class) {
                                $mbo_class_study_program = $this->Cgm->get_class_group_study_program($o_class_master_class->class_group_id)[0];
                                if ($mbo_class_study_program) {
                                    if (!in_array($mbo_class_study_program->study_program_abbreviation, $a_prodi_abbr)){
                                        array_push($a_prodi_abbr, $mbo_class_study_program->study_program_abbreviation);
                                    }
                                }
                            }
                        }

                        $s_prodi = '';
                        if (count($a_prodi_abbr) > 0) {
                            $s_prodi = implode('/', $a_prodi_abbr);
                        }
                        
                        $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer(['class_master_id' => $o_class_master->class_master_id]);

                        if ($mba_class_master_lecturer) {
                            foreach ($mba_class_master_lecturer as $o_class_lecturer) {
                                $mba_class_subject_delivered = $this->General->get_where('dt_class_subject_delivered', [
                                    'class_master_id' => $o_class_master->class_master_id,
                                    'employee_id' => $o_class_lecturer->employee_id
                                ]);

                                $s_count_absence = ($mba_class_subject_delivered) ? count($mba_class_subject_delivered) : 0;

                                $o_sheet->setCellValue('A'.$i_row, $o_class_lecturer->personal_data_name);
                                $o_sheet->setCellValue('B'.$i_row, $mbo_class_master_subject->subject_name);
                                $o_sheet->setCellValue('C'.$i_row, $s_prodi);
                                $o_sheet->setCellValue('D'.$i_row, $o_class_master->class_master_id);
                                $o_sheet->setCellValue('E'.$i_row, count($mba_student_member));
                                $o_sheet->setCellValue('F'.$i_row, $s_count_absence);

                                if ($mba_class_subject_delivered) {
                                    $c_char = 'F';
                                    $i_char = 7;

                                    foreach ($mba_class_subject_delivered as $o_class_absence)  {
                                        // $o_sheet->setCellValue(++$c_char.$i_row, $o_class_absence->subject_delivered_time_start);
                                        $o_sheet->setCellValueByColumnAndRow($i_char, $i_row, $o_class_absence->subject_delivered_time_start);
                                        $i_char++;
                                    }

                                    if ($i_max_cols_absence < $i_char) {
                                        $i_max_cols_absence = $i_char;
                                    }
                                }

                                $i_row++;
                            }
                        }else{
                            $o_sheet->setCellValue('B'.$i_row, $mbo_class_master_subject->subject_name);
                            $o_sheet->setCellValue('C'.$i_row, $s_prodi);
                            $o_sheet->setCellValue('D'.$i_row, $o_class_master->class_master_id);
                            $o_sheet->setCellValue('E'.$i_row, count($mba_student_member));

                            $i_row++;
                        }

                    }else{
                        print('Subject not found '.$o_class_master->class_master_id);exit;
                    }
                }
            }

            $styleArray = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('argb' => '00000000'),
                    )
                )
            );

            $c_max_cols_absence = 'G';
            $c_max_cols_absence_alt = 'A';
            for ($i = 7; $i <= $i_max_cols_absence; $i++) {
                ++$c_max_cols_absence;
            }

            $o_sheet->getStyle('A1:'.$c_max_cols_absence.$i_row)->getFont()->setSize(11);
            
            for ($i = 0; $i <= $i_max_cols_absence; $i++) {
                $o_sheet->getColumnDimension($c_max_cols_absence_alt)->setAutoSize(true);
                ++$c_max_cols_absence_alt;
            }
            
            $o_sheet->mergeCells('G4:'.$c_max_cols_absence.'4');
            $o_sheet->getStyle('A1:'.$c_max_cols_absence.'4')->getFont()->setBold( true );
            $o_sheet->getStyle('A4:'.$c_max_cols_absence.($i_row-1))->applyFromArray($styleArray);

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
			readfile( $s_file_path.$s_filename );
            exit;
            
        }else{
            print('class not found!');
        }
    }

    public function generate_invoice_semester_report($s_academic_year_id, $s_semester_type_id, $s_fee_type = 'all')
    {
        $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

        $s_file_path = APPPATH.'/uploads/finance/custom_report/';
        $mbo_fee_data = false;
        $s_payment_type = '';
        if ($s_fee_type != 'all') {
            $mbo_fee_data = $this->General->get_where('ref_payment_type', ['payment_type_code' => $s_fee_type])[0];
            $s_payment_type = str_replace(' ', '-', strtolower($mbo_fee_data->payment_type_name)).'_';
        }
        $s_filename = 'invoice_'.$s_payment_type.'mahasiswa_tahun_akademik_'.$s_academic_year_id.'_'.$s_semester_type_id;
        $s_file_name = $s_filename.'.xlsx';

        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

        $o_spreadsheet = IOFactory::load($s_template_path);
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $o_spreadsheet->getProperties()
            ->setTitle('Invoice Mahasiswa Tahun Akademik '.$s_academic_year_id.$s_semester_type_id)
            ->setCreator("IULI Finance Services")
            ->setLastModifiedBy("IULI Finance Services")
            ->setCategory("Invoice Report");

        $i_row = 3;

        // $mba_student_data = $this->General->get_where('dt_student_semester', [
        //     'academic_year_id' => $s_academic_year_id,
        //     'semester_type_id' => $s_semester_type_id
        // ]);

        $mba_student_data = $this->Scm->get_student_by_score([
            'sc.academic_year_id' => $s_academic_year_id,
            'sc.semester_type_id' => $s_semester_type_id
        ]);

        if ($mba_student_data) {
            $i_row = 1;
            $o_sheet->setCellValue('A'.$i_row, 'Student Name');
            $o_sheet->setCellValue('B'.$i_row, 'Student Number');
            $o_sheet->setCellValue('C'.$i_row, 'Batch');
            $o_sheet->setCellValue('D'.$i_row, 'Year In');
            $o_sheet->setCellValue('E'.$i_row, 'Faculty');
            $o_sheet->setCellValue('F'.$i_row, 'Study Program');
            $o_sheet->setCellValue('G'.$i_row, 'Semester');
            $o_sheet->setCellValue('H'.$i_row, 'Payment Type');
            $o_sheet->setCellValue('I'.$i_row, 'Billed Amount');
            $o_sheet->setCellValue('J'.$i_row, 'Total Payment Amount');
            $o_sheet->setCellValue('K'.$i_row, 'Remaining Amount');
            $o_sheet->setCellValue('L'.$i_row, 'Description');
            $o_sheet->setCellValue('M'.$i_row, 'Status');
            $o_sheet->setCellValue('N'.$i_row, 'Paid 1st Installment');
            $o_sheet->setCellValue('O'.$i_row, 'Paid 2nd Installment');
            $o_sheet->setCellValue('P'.$i_row, 'Paid 3rd Installment');
            $o_sheet->setCellValue('Q'.$i_row, 'Paid 4th Installment');
            $o_sheet->setCellValue('R'.$i_row, 'Paid 5th Installment');
            $o_sheet->setCellValue('S'.$i_row, 'Paid 6th Installment');

            // print('<pre>');
            $i_max_installment = 0;
            $c_max_installment = 'L';
            foreach ($mba_student_data as $o_student_semester) {
                $mbo_student = $this->Stm->get_student_by_id($o_student_semester->student_id);
                
                if ($mbo_student->student_status == 'active') {
                    $mbo_semester = $this->General->get_where('ref_semester', ['semester_id' => $o_student_semester->semester_id])[0];

                    if (!$mbo_semester) {
                        print('Incomplete data server!');
                        print($o_student_semester->student_id);exit;
                    }

                    // $mba_student_sks = $this->Scm->get_score_data([
                    //     'sc.student_id' => $mbo_student->student_id,
                    //     'sc.academic_year_id' => $o_active_semester->academic_year_id,
                    //     'sc.semester_type_id' => $o_active_semester->semester_type_id,
                    //     'sc.score_approval' => 'approved',
                    //     'curs.curriculum_subject_credit >' => 0
                    // ]);

                    // $i_sks = 0;
                    // if ($mba_student_sks) {
                    //     foreach ($mba_student_sks as $o_score) {
                    //         $i_sks += $o_score->curriculum_subject_credit;
                    //     }
                    // }

                    $a_fee_filter = [
                        'semester_id' => $o_student_semester->semester_id,
                        'study_program_id' => $mbo_student->study_program_id,
                        'program_id' => $mbo_student->program_id,
                        'payment_type_code' => $s_fee_type,
                        'fee_amount_type' => 'main'
                    ];

                    if ($s_fee_type == 'all') {
                        unset($a_fee_filter['payment_type_code']);
                    }

                    $fee_data = $this->General->get_where('dt_fee', $a_fee_filter);

                    $mba_student_invoice = [];
                    if ($fee_data) {
                        foreach ($fee_data as $o_fee) {
                            $mbo_invoice_student = $this->Im->student_has_invoice_fee_id($mbo_student->personal_data_id, $o_fee->fee_id);

                            if (($mbo_invoice_student) AND ($mbo_invoice_student->invoice_status != 'cancelled')) {
                                array_push($mba_student_invoice, $mbo_invoice_student);
                            }
                        }
                    }

                    if (count($mba_student_invoice) > 0){
                        // print($mbo_student->personal_data_name.'<br>');exit;
                        $a_installment_col = ['N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                        // $i_start_installment = 1;
                        foreach ($mba_student_invoice as $o_invoice) {
                            $i_row++;
                            $mbo_payment_type_data = $this->Im->get_payment_type($o_invoice->payment_type_code);
                            $mba_invoice_installment_data = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                            $d_amount_total = 0;
                            $d_payment = 0;
                            if  ($mba_invoice_installment_data) {
                                foreach ($mba_invoice_installment_data as $o_installment) {
                                    $d_payment += $o_installment->sub_invoice_details_amount_paid;
                                    $d_amount_total += $o_installment->sub_invoice_details_amount;
                                    
                                    $i_installment = substr($o_installment->sub_invoice_details_va_number, 8, 1);
                                    $o_sheet->setCellValue($a_installment_col[$i_installment-1].$i_row, $o_installment->sub_invoice_details_amount_paid);
                                }
                            }else{
                                $mbo_invoice_full_data = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                                $d_amount_total = $mbo_invoice_full_data->sub_invoice_amount_total;
                                $d_payment = $mbo_invoice_full_data->sub_invoice_details_amount_paid;
                            }

                            $d_payment = ($o_invoice->invoice_status == 'paid') ? $o_invoice->invoice_amount_paid : $d_payment;
                            $d_outstanding = $d_amount_total - $d_payment;
                            // if ($d_amount_total == 0){
                            //     print('<pre>');
                            //     print($o_invoice->invoice_id.':<br>');
                            //     var_dump($mba_invoice_installment_data);exit;
                            // }

                            $o_sheet->setCellValue('A'.$i_row, $mbo_student->personal_data_name);
                            $o_sheet->setCellValue('B'.$i_row, $mbo_student->student_number);
                            $o_sheet->setCellValue('C'.$i_row, $mbo_student->academic_year_id);
                            $o_sheet->setCellValue('D'.$i_row, $mbo_student->finance_year_id);
                            $o_sheet->setCellValue('E'.$i_row, $mbo_student->faculty_abbreviation);
                            $o_sheet->setCellValue('F'.$i_row, $mbo_student->study_program_abbreviation);
                            $o_sheet->setCellValue('G'.$i_row, $mbo_semester->semester_number);
                            $o_sheet->setCellValue('H'.$i_row, $mbo_payment_type_data->payment_type_name);
                            $o_sheet->setCellValue('I'.$i_row, $d_amount_total);
                            $o_sheet->setCellValue('J'.$i_row, $d_payment);
                            $o_sheet->setCellValue('K'.$i_row, $d_outstanding);
                            $o_sheet->setCellValue('L'.$i_row, $o_invoice->invoice_description);
                            $o_sheet->setCellValue('M'.$i_row, strtoupper($o_invoice->invoice_status));
                        }
                    }
                    
                }
            }
        }

        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_file_name);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        $a_path_info = pathinfo($s_file_path.$s_file_name);
        $s_file_ext = $a_path_info['extension'];
        header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
        readfile( $s_file_path.$s_file_name );
        exit;
    }

    public function generate_tuition_fee_template($s_academic_year_id, $s_program_id = 1)
    {
        $mba_academic_year = $this->General->get_where('dt_academic_year', ['academic_year_id' => $s_academic_year_id]);
        // print('<pre>');var_dump($mba_academic_year);exit;
        $mbo_program = $this->General->get_where('ref_program', ['program_id' => $s_program_id])[0];
        $s_passcode = '';

        if (($mba_academic_year) AND ($mbo_program)) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

            $s_file_name = 'TuitionFee_batch_'.$s_academic_year_id.'_Master';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/finance/tuition_fee/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $style_vertical_center = array(
                'alignment' => array(
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                )
            );

            $style_border = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '00000000'],
                    )
                )
            );

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
            ->setTitle($s_file_name)
            ->setCreator("IULI Finance Services")
            ->setCategory("Tuition Fee");

            $o_sheet->getProtection()->setSheet(true);
            // $o_spreadsheet->getDefaultStyle()->getProtection()->setLocked(false);

            $o_sheet->setCellValue('A1', "TUITION FEE");
            $o_sheet->setCellValue('A2', "Batch: ".$s_academic_year_id."/".($s_academic_year_id + 1));
            $o_sheet->setCellValue('A3', "Program: ".$mbo_program->program_code);
            $o_sheet->mergeCells('A1:K1');
            $o_sheet->mergeCells('A2:K2');
            $o_sheet->mergeCells('A3:K3');
            $o_sheet->protectCells('A1:K3', $s_passcode);

            $mba_study_program = $this->Spm->get_study_program(false, false);

            $o_sheet->setCellValue('A5', "Study Program");
            $o_sheet->setCellValue('B5', "Study Program Code");
            $o_sheet->setCellValue('C5', "Payment/Semester");
            $o_sheet->setCellValue('K5', "Total");
            $o_sheet->setCellValue('L5', "Semester > 8");

            $o_sheet->mergeCells('A5:A6');
            $o_sheet->mergeCells('B5:B6');
            $o_sheet->mergeCells('K5:K6');
            $o_sheet->mergeCells('L5:Q5');
            $o_sheet->mergeCells('C5:J5');
            $o_sheet->getStyle('A5:L6')->applyFromArray($style_vertical_center);

            $o_sheet->setCellValue('C6', "1");
            $o_sheet->setCellValue('D6', "2");
            $o_sheet->setCellValue('E6', "3");
            $o_sheet->setCellValue('F6', "4");
            $o_sheet->setCellValue('G6', "5");
            $o_sheet->setCellValue('H6', "6");
            $o_sheet->setCellValue('I6', "7");
            $o_sheet->setCellValue('J6', "8");
            $o_sheet->setCellValue('L6', "9");
            $o_sheet->setCellValue('M6', "10");
            $o_sheet->setCellValue('N6', "11");
            $o_sheet->setCellValue('O6', "12");
            $o_sheet->setCellValue('P6', "13");
            $o_sheet->setCellValue('Q6', "14");
            $o_sheet->protectCells('A5:Q6', $s_passcode);

            $i_row = 7;
            if ($mba_study_program) {
                foreach ($mba_study_program as $o_prodi) {
                    $o_sheet->setCellValue('A'.$i_row, $o_prodi->study_program_name);
                    $o_sheet->setCellValue('B'.$i_row, $o_prodi->study_program_abbreviation);

                    $o_sheet->setCellValue('K'.$i_row, "=SUM(C$i_row:J$i_row)");
                    $i_row++;
                }
            }

            $o_sheet->getColumnDimension('A')->setAutoSize(true);
            $o_sheet->getColumnDimension('B')->setAutoSize(true);
            $o_sheet->getStyle('A5:Q'.($i_row-1))->applyFromArray($style_border);
            $o_sheet->getStyle('A1:Q6')->getFont()->setBold( true );

            $o_sheet->protectCells('A7:B'.($i_row-1), $s_passcode);
            $o_sheet->protectCells('K7:K'.($i_row-1), $s_passcode);
            $o_sheet->protectCells('L7:Q'.($i_row-1), $s_passcode);

            $o_sheet->getStyle('C7:J'.$i_row)
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

            $o_sheet->getStyle('L7:Q'.$i_row)
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

            $o_sheet->getStyle('C7:Q'.($i_row-1))->getNumberFormat()
                ->setFormatCode('#,##');
                
            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
			readfile( $s_file_path.$s_filename );
            exit;
        }else{
            $a_return = ['code' => 0, 'message' => 'Academic Year not found!'];
            print json_encode($a_return);exit;
        }
    }

    public function char()
    {
        $z = 'A';
        for ($i = 0; $i < 33; $i++) {
            $z++;
        }
        print($z);
    }

    public function check_wrapp_text($s_string_input, $i_length = 92)
    {
        $a_str = explode(' ', $s_string_input);
        $temp = '';
        $i_wrap = 0;
        foreach ($a_str as $s_string){
            if ((strlen($s_string) + strlen($temp) + 1) > $i_length){
                $i_wrap++;
                $temp = '';
            }else{
                $temp .= $s_string.' ';
            }
        }
        
        return $i_wrap;
    }

    public function get_ni_registration_data()
    {
        $mba_student_data = $this->Stm->get_student_filtered_pmb_function([
			'ds.program_id' => 3
		]);

        if ($mba_student_data) {
            foreach ($mba_student_data as $o_student) {
                $o_student->school_city = NULL;

                $mbo_candidate_invoice_data = $this->Im->student_has_invoice_data($o_student->personal_data_id, [
                    'payment_type_code' => '01',
                    'academic_year_id' => $o_student->finance_year_id
                ]);

                $o_student->has_paid = (($mbo_candidate_invoice_data) AND ($mbo_candidate_invoice_data->invoice_amount_paid > 0)) ? 'yes' : 'no';

                if (!is_null($o_student->institution_id)) {
                    $mbo_institution_data = $this->Inm->get_institution_data(['ri.institution_id' => $o_student->institution_id])[0];
                    
                    $o_student->school_city = ($mbo_institution_data) ? $mbo_institution_data->address_city : NULL;
                }
                
                $o_student->parent_name = NULL;
                $o_student->parent_email = NULL;
                $o_student->parent_phone = NULL;

                $mbo_family_data = $this->Fm->get_family_by_personal_data_id($o_student->personal_data_id);
                if ($mbo_family_data) {
                    $mbo_parent_family = $this->Fm->get_family_members($mbo_family_data->family_id, [
                        'family_member_status != ' => 'child'
                    ])[0];

                    if ($mbo_parent_family) {
                        $mbo_parent_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mbo_parent_family->personal_data_id])[0];
                        $o_student->parent_name = ($mbo_parent_data) ? $mbo_parent_data->personal_data_name : NULL;
                        $o_student->parent_email = ($mbo_parent_data) ? $mbo_parent_data->personal_data_email : NULL;
                        $o_student->parent_phone = ($mbo_parent_data) ? $mbo_parent_data->personal_data_cellular : NULL;
                    }
                }
            }
        }

        return $mba_student_data;
    }

    public function force_insert_to_pmbni()
    {
        $mba_student_data = $this->get_ni_registration_data();
        if ($mba_student_data) {
            $a_data_insert = [];
            $a_otp = [];
            foreach ($mba_student_data as $o_student) {
                $s_otp = rand(1111,'9999');
                if (!in_array($s_otp, $a_otp)) {
                    array_push($a_otp, $s_otp);
                }else{
                    $s_otp = NULL;
                }

                $a_data = [
                    'ApplicantID' => $this->uuid->v4(),
                    'Email' => $o_student->personal_data_email,
                    'Name' => $o_student->personal_data_name,
                    'BirthPlace' => (!is_null($o_student->personal_data_place_of_birth)) ? $o_student->personal_data_place_of_birth : '',
                    'Birthdate' => (!is_null($o_student->personal_data_date_of_birth)) ? $o_student->personal_data_date_of_birth : '',
                    'StudyProgram1' => $o_student->study_program_abbreviation,
                    'StudyProgram2' => $o_student->study_program_abbreviation,
                    'SchoolName' => (!is_null($o_student->institution_name)) ? $o_student->institution_name : '',
                    'GraduateYear' => (!is_null($o_student->academic_history_graduation_year)) ? $o_student->academic_history_graduation_year : '',
                    'OTP' => $s_otp,
                    'CreatedAt' => (!is_null($o_student->student_date_enrollment)) ? $o_student->student_date_enrollment : date('Y-m-d H:i:s'),
                    'UpdatedAt' => (!is_null($o_student->register_timestamp)) ? $o_student->register_timestamp : date('Y-m-d H:i:s'),
                    'Gender' => ($o_student->personal_data_gender == 'M') ? 'PRIA' : 'WANITA',
                    'GuardianName' => (!is_null($o_student->parent_name)) ? $o_student->parent_name : '',
                    'GuardianEmail' => (!is_null($o_student->parent_email)) ? $o_student->parent_email : '',
                    'GuardianPhone' => (!is_null($o_student->parent_phone)) ? $o_student->parent_phone : '',
                    'Nationality' => (!is_null($o_student->personal_data_nationality)) ? $o_student->personal_data_nationality : '',
                    'SchoolCity' => (!is_null($o_student->school_city)) ? $o_student->school_city : '',
                    'CurrentStatus' => ($o_student->student_status == 'candidate') ? 'REGISTRATION' : 'CONFIRMATION',
                    'hasPaid' => ($o_student->has_paid == 'yes') ? 1 : 0,
                ];

                array_push($a_data_insert, $a_data);

                $a_key = [];
                $a_value = [];
                foreach ($a_data as $key => $value) {
                    array_push($a_key, $key);
                    array_push($a_value, $value);
                }
                print("INSERT INTO applicant (".implode(",", $a_key).") VALUES ('".implode("','", $a_value)."');");
                print('<br>');
            }

            $a_data_insert = array_values($a_data_insert);
            print('<pre>');
            var_dump($a_data_insert);
        }
    }

    public function download_ni_registration()
    {
        $mba_student_data = $this->get_ni_registration_data();

		if ($mba_student_data) {
            // $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'Student_National_Institution_Registration_Data';
            $s_filename = $s_file_name.'.csv';

            $s_file_path = APPPATH."uploads/admission/ni_registration/".date('Y')."/".date('m')."/";
            
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_filepath = $s_file_path.$s_filename;
            $fp = fopen($s_filepath, 'w+');

            fputcsv($fp, [
				'ApplicantID',
				'Email',
				'Name',
				'BirthPlace',
				'Birthdate',
				'Phone',
				'StudyProgram1',
				'StudyProgram2',
				'SchoolName',
				'GraduateYear',
                'OTP',
                'CreatedAt',
                'UpdatedAt',
                'IsVerified',
                'Gender',
                'GuardianName',
                'GuardianEmail',
                'GuardianPhone',
                'Nationality',
                'SchoolCity',
                'SchoolReferencePerson',
                'SchoolReferencePersonContact',
                'SchoolReferencePersonContactEmail',
                'CurrentStatus',
                'hasPaid',
                'FixRequest',
                'EmergencyName',
                'EmergencyEmail',
                'EmergencyPhone',
			]);

            foreach ($mba_student_data as $o_student) {
                $s_otp = rand(1111, '9999');
                fputcsv($fp, [
                    $o_student->student_id,
                    $o_student->personal_data_email,
                    $o_student->personal_data_name,
                    (!is_null($o_student->personal_data_place_of_birth)) ? $o_student->personal_data_place_of_birth : '',
                    (!is_null($o_student->personal_data_date_of_birth)) ? $o_student->personal_data_date_of_birth : '',
                    (!is_null($o_student->personal_data_cellular)) ? $o_student->personal_data_cellular : '',
                    $o_student->study_program_abbreviation,
                    $o_student->study_program_abbreviation,
                    (!is_null($o_student->institution_name)) ? $o_student->institution_name : '',
                    (!is_null($o_student->academic_history_graduation_year)) ? $o_student->academic_history_graduation_year : '',
                    $s_otp,
                    (!is_null($o_student->student_date_enrollment)) ? $o_student->student_date_enrollment : date('Y-m-d H:i:s'),
                    (!is_null($o_student->register_timestamp)) ? $o_student->register_timestamp : date('Y-m-d H:i:s'),
                    ($o_student->personal_data_gender == 'M') ? 'PRIA' : 'WANITA',
                    (!is_null($o_student->parent_name)) ? $o_student->parent_name : '',
                    (!is_null($o_student->parent_email)) ? $o_student->parent_email : '',
                    (!is_null($o_student->parent_phone)) ? $o_student->parent_phone : '',
                    (!is_null($o_student->personal_data_nationality)) ? $o_student->personal_data_nationality : '',
                    (!is_null($o_student->school_city)) ? $o_student->school_city : '',
                    '',
                    '',
                    '',
                    ($o_student->student_status == 'candidate') ? 'REGISTRATION' : 'CONFIRMATION',
                    ($o_student->has_paid == 'yes') ? 1 : 0,
                    NULL,
                    NULL,
                    NULL,
                    NULL
                ]);
                
            }

            fclose($fp);
			// $this->session->set_flashdata('file_token', $s_file_path);
			// print json_encode(['code' => 0]);
			// exit;

            $a_path_info = pathinfo($s_filepath);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_filepath );
            exit;
        }
    }

    public function download_ni_student($b_force_download = false)
	{
		// $mba_student_data = $this->Stm->get_student_filtered_pmb_function([
		// 	'ds.program_id' => 3
		// ]);
        $mba_student_data = $this->get_ni_registration_data();

		if ($mba_student_data) {
			$s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'Student_National_Institution_Registration_Data_'.date('Y-m-d');
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/admission/ni_registration/".date('Y')."/".date('m')."/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Admission Services")
                ->setCategory("National Institution Registration");

            $i_row = 1;
            $o_sheet->setCellValue('A'.$i_row, 'Firstname');
            $o_sheet->setCellValue('B'.$i_row, 'Lastname');
            $o_sheet->setCellValue('C'.$i_row, 'Fullname');
            $o_sheet->setCellValue('D'.$i_row, 'Nomor HP');
            $o_sheet->setCellValue('E'.$i_row, 'Email');
            $o_sheet->setCellValue('F'.$i_row, 'Jurusan');
            $o_sheet->setCellValue('G'.$i_row, 'Status');
            $o_sheet->setCellValue('H'.$i_row, 'Registration Datetime');
            $o_sheet->setCellValue('I'.$i_row, 'Paid Enrollment Fee');
            $i_row++;

            foreach ($mba_student_data as $o_student) {
                $s_name = $o_student->personal_data_name;
                $a_name = explode(' ', $s_name);
                $s_firstname = $a_name[0];
                $s_lastname = (count($a_name) == 1) ? '' : $a_name[count($a_name) - 1];
                
                $o_sheet->setCellValue('A'.$i_row, $s_firstname);
                $o_sheet->setCellValue('B'.$i_row, $s_lastname);
                $o_sheet->setCellValue('C'.$i_row, $o_student->personal_data_name);
                $o_sheet->setCellValue('D'.$i_row, $o_student->personal_data_cellular);
                $o_sheet->setCellValue('E'.$i_row, $o_student->personal_data_email);
                $o_sheet->setCellValue('F'.$i_row, $o_student->study_program_ni_name);
                $o_sheet->setCellValue('G'.$i_row, $o_student->student_status);
                $o_sheet->setCellValue('H'.$i_row, date('d F Y H:i:s', strtotime($o_student->student_date_enrollment)));
                $o_sheet->setCellValue('I'.$i_row, $o_student->has_paid);
                $i_row++;
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            if ($b_force_download) {
                $a_path_info = pathinfo($s_file_path.$s_filename);
                $s_file_ext = $a_path_info['extension'];
                header('Content-Disposition: attachment; filename='.urlencode($s_filename));
                readfile( $s_file_path.$s_filename );
                exit;
            }
            $a_return = ['code' => 0, 'message' => 'success', 'filename' => $s_filename];
		}else{
            $a_return = ['code' => 1, 'message' => 'no student found!'];
        }

        return $a_return;
        exit;
	}

    // untuk validasi sendiri ke dikti
    public function generate_gpa_recapitulation_for_internal_check(
        $s_student_batch,
        $b_passed_deffence = false,
        $s_study_program_id,
        $a_student_status = false,
        $b_last_semester = false,
        $b_last_short_semester = false,
        $b_last_repetition = true,
        $b_all_printed = false
    )
    {
        $mba_semester_active = $this->Smm->get_active_semester();
        $i_short_semester_blocked = 0;

        if (!$b_last_semester) {
            $i_semester_selected = $mba_semester_active->academic_year_id.$mba_semester_active->semester_type_id;
            $a_semester_selected = [
                'academic_year_id' => $mba_semester_active->academic_year_id,
                'semester_type_id' => $mba_semester_active->semester_type_id
            ];
        }else{
            $mba_semester_settings = $this->Smm->get_semester_setting();
            $index_semester = count($mba_semester_settings) - 1;
            foreach ($mba_semester_settings as $key => $o_semester) {
                if ($o_semester->semester_status == 'active') {
                    $index_semester = $key + 1;
                    break;
                }
            }
            // print('<pre>');
            // var_dump($mba_semester_settings[$index_semester]);exit;

            $i_semester_selected = $mba_semester_settings[$index_semester]->academic_year_id.$mba_semester_settings[$index_semester]->semester_type_id;
            $a_semester_selected = [
                'academic_year_id' => $mba_semester_settings[$index_semester]->academic_year_id,
                'semester_type_id' => $mba_semester_settings[$index_semester]->semester_type_id
            ];
            
            if ($b_last_short_semester) {
                $i_semester_selected = ($mba_semester_settings[$index_semester]->semester_type_id == 1) ? $mba_semester_settings[$index_semester]->academic_year_id.'7' : $mba_semester_settings[$index_semester]->academic_year_id.'8';
                $a_semester_selected = [
                    'academic_year_id' => $mba_semester_settings[$index_semester]->academic_year_id,
                    'semester_type_id' => ($mba_semester_settings[$index_semester]->semester_type_id == 1) ? 7 : 8
                ];
            }
        }
        
        $a_filter = array(
            'ds.academic_year_id' => $s_student_batch,
            'ds.study_program_id' => $s_study_program_id
        );

        $s_text_header = 'MAHASISWA PRODI ';
        $s_file_name = 'VALIDATION_GPA_Recapitulation_';

        if (($s_study_program_id == 'all') OR ($s_study_program_id == '')) {
            unset($a_filter['ds.study_program_id']);
            $s_text_header .= '-';
            $s_file_name .= 'All_Semester';
        }else {
            $mbo_prodi_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
            $s_text_header .= strtoupper($mbo_prodi_data->study_program_name);
            $s_file_name .= strtoupper($mbo_prodi_data->study_program_abbreviation);
        }

        $s_text_header .= ' ANGKATAN ';

        if (($s_student_batch == 'all') OR ($s_student_batch == '')) {
            unset($a_filter['ds.academic_year_id']);
            $s_text_header .= '_-';
        }else{
            $s_text_header .= ' '.$s_student_batch.'/'.(intval($s_student_batch) + 1);
            $s_file_name .= '_'.$s_student_batch.'-'.(intval($s_student_batch) + 1);
        }

        $s_template_path = APPPATH.'uploads/templates/template-rekap-ipsipk-v2.xls';
        if ($b_passed_deffence) {
            $a_filter['ds.student_mark_completed_defense'] = 1;
        }
        
        $a_filter = (count($a_filter) > 0) ? $a_filter : false;

        $mba_student_data = $this->Stm->get_student_list_data($a_filter, $a_student_status, array(
            'faculty_name' => 'ASC',
            'study_program_name' => 'ASC',
            'personal_data_name' => 'ASC'
        ));

        
        if ($mba_student_data) {
            $s_filepath = APPPATH.'/uploads/academic/'.$mba_semester_active->academic_year_id.$mba_semester_active->semester_type_id.'/cummulative_gpa_validation/';

            if(!file_exists($s_filepath)){
                mkdir($s_filepath, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_text_header)
                ->setCreator("IULI Academic Services")
                ->setLastModifiedBy("IULI Academic Services")
                ->setCategory("Cummulative GPA");
            $o_sheet = $o_spreadsheet->setActiveSheetIndexByName("Template IPK");
            $o_sheet->setCellValue('A1', 'REKAPITULASI IPS dan IPK '.$i_semester_selected);
            $o_sheet->setCellValue('A2', $s_text_header);

            $i_row = 4;
            $i_number_counter = 1;

            $a_gpa_semester_data = array();
            $a_gpa_cummulative_data = array();
            $a_absence_data = array();

            foreach ($mba_student_data as $o_student) {
                $mba_student_semester = $this->Smm->get_semester_student_personal_data(array(
                    // 'st.personal_data_id' => $o_student->personal_data_id
                    'dss.student_id' => $o_student->student_id
                ), array(1,2,3,7,8));

                if ($mba_student_semester) {
                    $a_total_semester_absence = array();
                    $has_repetition = false;

                    $o_sheet->setCellValue('A'.$i_row, $i_number_counter);
                    $o_sheet->setCellValue('B'.$i_row, strtoupper($o_student->personal_data_name));
                    $o_sheet->setCellValue('C'.$i_row, strtoupper($o_student->student_number));
                    
                    foreach($mba_student_semester AS $key => $o_student_semester) {
                        $i_semester_student = $o_student_semester->semester_academic_year_id.$o_student_semester->semester_semester_type_id;
                        
                        $mbo_student_semester_data = $this->Stm->get_student_by_id($o_student_semester->student_id);
                        
                        $b_print = true;
                        if ($b_last_semester) {
                            if (
                                ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND
                                ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])
                            ) {
                                $s_gpa_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'gpa', $b_last_repetition);
                                $s_credit_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'credit', $b_last_repetition);
                                $s_average_absence_ = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'average_absence', $b_last_repetition);
                                $s_average_absence_semester = 100 - $s_average_absence_;
                            }else{
                                $b_print = false;
                            }
                            
                        }else{
                            $s_gpa_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id);
                            $s_credit_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'credit');
                            $s_average_absence_ = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'average_absence');
                            $s_average_absence_semester = 100 - $s_average_absence_;
                        }

                        if ((!$b_last_semester) AND ($b_print)) {
                            array_push($a_total_semester_absence, $s_average_absence_);

                            array_push($a_gpa_semester_data, $s_gpa_semester);
                            array_push($a_absence_data, $s_average_absence_);

                            $o_sheet->setCellValue('D'.$i_row, strtoupper($mbo_student_semester_data->study_program_name.' - '.$mbo_student_semester_data->academic_year_id));
                            $o_sheet->setCellValue('E'.$i_row, $o_student_semester->semester_academic_year_id.$o_student_semester->semester_semester_type_id);
                            $o_sheet->setCellValue('F'.$i_row, $o_student_semester->semester_type_name);
                            $o_sheet->setCellValue('G'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));
                            $o_sheet->setCellValue('I'.$i_row, round($s_average_absence_semester, 2, PHP_ROUND_HALF_UP));
                            $o_sheet->setCellValue('K'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                        }

                        if (
                            ($b_print) AND
                            ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND
                            ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])
                        ) {
                            if ($b_last_semester) {
                                array_push($a_total_semester_absence, $s_average_absence_);
    
                                array_push($a_gpa_semester_data, $s_gpa_semester);
                                array_push($a_absence_data, $s_average_absence_);

                                $o_sheet->setCellValue('D'.$i_row, strtoupper($mbo_student_semester_data->study_program_abbreviation.' - '.$mbo_student_semester_data->academic_year_id));
                                $o_sheet->setCellValue('E'.$i_row, $o_student_semester->semester_academic_year_id.$o_student_semester->semester_semester_type_id);
                                $o_sheet->setCellValue('F'.$i_row, $o_student_semester->semester_type_name);
                                $o_sheet->setCellValue('G'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));
                                $o_sheet->setCellValue('I'.$i_row, round($s_average_absence_semester, 2, PHP_ROUND_HALF_UP));
                                $o_sheet->setCellValue('K'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                            }

                        }

                        $a_param_score = array(
                            // 'st.personal_data_id' => $o_student->personal_data_id,
                            'sc.student_id' => $o_student->student_id,
                            'sc.score_approval' => 'approved',
                            'sc.score_display' => 'TRUE',
                            'sc.semester_id !=' => '17',
                            'curriculum_subject_credit !=' => '0',
                            'sc.academic_year_id >=' => $mba_student_semester[0]->semester_academic_year_id,
                            'sc.academic_year_id <=' => $o_student_semester->semester_academic_year_id,
                            'curriculum_subject_type !=' => 'extracurricular'
                        );

                        // if (!$b_last_repetition) {
                        //     $a_param_score['score_mark_for_repetition'] = NULL;
                        // }
                        
                        $a_filter_semester = array(
                            'academic_year_start' => $mba_student_semester[0]->semester_academic_year_id,
                            'semester_type_start' => $mba_student_semester[0]->semester_semester_type_id,
                            'academic_year_end' => $a_semester_selected['academic_year_id'],
                            'semester_type_end' => $a_semester_selected['semester_type_id']
                        );

                        $mba_score_data = $this->Scm->get_score_data($a_param_score, [1,2,3,7,8]);
                        $mba_transfer_credit = $this->Scm->get_score_data([
                            'sc.student_id' => $o_student->student_id
                        ], [5]);

                        // $mba_score_data = modules::run('academic/score/clear_semester_score', $mba_score_data, $a_filter_semester);
                        if (($a_filter_semester) AND ($mba_score_data)) {
                            foreach ($mba_score_data as $key => $final) {
                                if (($a_filter_semester['semester_type_start'] !== null) AND ($a_filter_semester['semester_type_start'] == 2)) {
                                    if (($a_filter_semester['academic_year_start'] == $final->academic_year_id) AND ($final->semester_type_id == 1)) {
                                        unset($mba_score_data[$key]);
                                    }
                                }

                                if (($a_filter_semester['semester_type_end'] !== null) AND ($a_filter_semester['semester_type_end'] == 1)) {
                                    // if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id == 2)) {
                                    if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id != 1)) {
                                        unset($mba_score_data[$key]);
                                    }
                                }
                            }
                        }

                        if ($mba_transfer_credit) {
                            $mba_score_data = array_merge($mba_score_data, $mba_transfer_credit);
                        }

                        $i_sum_credit = 0;
                        $i_sum_merit = 0;
                        $s_sum_absence_student = array_sum($a_total_semester_absence);
                        $s_average_absence_student = (count($a_total_semester_absence) > 0) ? ($s_sum_absence_student / count($a_total_semester_absence)) : 0;
                        $s_average_absence_student = 100 - $s_average_absence_student;

                        if ($mba_score_data) {
                            $a_credit = array();
                            $a_merit = array();

                            foreach ($mba_score_data as $score) {
                                if (!$b_all_printed) {
                                    if ($this->Scm->get_good_grades($score->subject_name, $score->student_id, $score->score_sum)) {
                                        // if (!in_array($score->subject_name, $a_subject_name_fill)) {
                                            if(!is_null($score->score_repetition_exam)){
                                                $has_repetition = true;
                                            }
        
                                            $score_sum = intval(round($score->score_sum, 0, PHP_ROUND_HALF_UP));
                                            $score_grade_point = $this->grades->get_grade_point($score_sum);
                                            $score_merit = $this->grades->get_merit($score->curriculum_subject_credit, $score_grade_point);
                                            array_push($a_credit, $score->curriculum_subject_credit);
                                            array_push($a_merit, $score_merit);
    
                                        //     array_push($a_subject_name_fill, $o_score->subject_name);
                                        // }
                                    }
                                }else{
                                    if(!is_null($score->score_repetition_exam)){
                                        $has_repetition = true;
                                    }

                                    $score_sum = intval(round($score->score_sum, 0, PHP_ROUND_HALF_UP));
                                    $score_grade_point = $this->grades->get_grade_point($score_sum);
                                    $score_merit = $this->grades->get_merit($score->curriculum_subject_credit, $score_grade_point);
                                    array_push($a_credit, $score->curriculum_subject_credit);
                                    array_push($a_merit, $score_merit);
                                }
                            }

                            $i_sum_credit = array_sum($a_credit);
                            $i_sum_merit = array_sum($a_merit);
                        }

                        $s_gpa_cummulative = $this->grades->get_ipk($i_sum_merit, $i_sum_credit);
                        array_push($a_gpa_cummulative_data, $s_gpa_cummulative);

                        $s_predicate = '-';
                        $b_has_repeat_subject = modules::run('academic/score/has_repeat_subject', $o_student->student_id);
                        if(!$b_has_repeat_subject) {
                            $s_predicate = $this->grades->get_graduation_predicate(round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        }
                        // if (!$has_repetition) {
                        //     $s_predicate = $this->grades->get_graduation_predicate(round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        // }
                        
                        $o_sheet->setCellValue('H'.$i_row, round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('J'.$i_row, round($s_average_absence_student, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('L'.$i_row, round($i_sum_credit, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('P'.$i_row, $s_predicate);

                        if (!$b_last_semester) {
                            $i_row++;
                            $o_sheet->insertNewRowBefore($i_row, 1);
                        }
                        
                    }

                    $i_number_counter++;
                    $i_row++;
                    $o_sheet->insertNewRowBefore($i_row, 1);
                }
            }

            $o_sheet->removeRow($i_row, 1);
            $i_row += 2;

            $s_max_gpa_semester_data = max($a_gpa_semester_data);
            $s_max_gpa_cummulative_data = max($a_gpa_cummulative_data);

            $s_min_gpa_semester_data = min($a_gpa_semester_data);
            $s_min_gpa_cummulative_data = min($a_gpa_cummulative_data);

            $s_average_gpa_semester = (count($a_gpa_semester_data) > 0) ? (array_sum($a_gpa_semester_data) / count($a_gpa_semester_data)) : 0;
            $s_average_gpa_cummulative = (count($a_gpa_cummulative_data) > 0) ? (array_sum($a_gpa_cummulative_data) / count($a_gpa_cummulative_data)) : 0;
            $s_average_absence = 100 - ((count($a_absence_data) > 0) ? (array_sum($a_absence_data) / count($a_absence_data)) : 0);

            $o_sheet->setCellValue('G'.$i_row, round($s_average_gpa_semester, 2, PHP_ROUND_HALF_UP));
            $o_sheet->setCellValue('H'.$i_row, round($s_average_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
            $o_sheet->setCellValue('I'.$i_row, round($s_average_absence, 2, PHP_ROUND_HALF_UP));
            $i_row++;
            $o_sheet->setCellValue('G'.$i_row, round($s_max_gpa_semester_data, 2, PHP_ROUND_HALF_UP));
            $o_sheet->setCellValue('H'.$i_row, round($s_max_gpa_cummulative_data, 2, PHP_ROUND_HALF_UP));
            $i_row++;
            $o_sheet->setCellValue('G'.$i_row, round($s_min_gpa_semester_data, 2, PHP_ROUND_HALF_UP));
            $o_sheet->setCellValue('H'.$i_row, round($s_min_gpa_cummulative_data, 2, PHP_ROUND_HALF_UP));

            if ($b_last_semester) {
                $o_sheet->removeColumn('O');
                $o_sheet->removeColumn('N');
                $o_sheet->removeColumn('M');
                $o_sheet->removeColumn('J');
                $o_sheet->removeColumn('I');
                $o_sheet->removeColumn('F');
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_filepath.$s_file_name.'.xlsx');
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            return $s_file_name.'.xlsx';
        }else{
            return false;
        }

    }

    public function generate_who_teach_what($s_academic_year_id, $s_semester_type_id, $b_force_download = false)
    {
        $mba_class_master_list = $this->General->get_where('dt_class_master', [
            'academic_year_id' => $s_academic_year_id,
            'semester_type_id' => $s_semester_type_id
        ]);

        $a_class_not_found = [];
        if ($mba_class_master_list) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

            $s_file_name = 'Who_teach_what_'.$s_academic_year_id.'-'.$s_semester_type_id.'';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/temp/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $i_row = 1;

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI IT Services")
                ->setCategory("Who Teach What");

            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_sheet->setCellValue('A'.$i_row, 'Lecturer');
            $o_sheet->setCellValue('B'.$i_row, 'Subject Code');
            $o_sheet->setCellValue('C'.$i_row, 'Subject Name');
            $o_sheet->setCellValue('D'.$i_row, 'Study Program');
            $o_sheet->setCellValue('E'.$i_row, 'Semester');
            $o_sheet->setCellValue('F'.$i_row, 'Count Student');
            $i_row++;

            foreach ($mba_class_master_list as $key => $o_class_master) {
                $mba_class_lecturer = $this->Cgm->get_class_master_lecturer([
                    'class_master_id' => $o_class_master->class_master_id
                ]);

                $mba_class_group_list = $this->Cgm->get_class_master_group(['cmc.class_master_id' => $o_class_master->class_master_id]);
                if ($mba_class_group_list AND $mba_class_lecturer) {
                    foreach ($mba_class_lecturer as $o_lecturer) {
                        foreach ($mba_class_group_list as $o_class) {
                            $mba_class_group_details = $this->Cgm->get_class_group_filtered([
                                'dcg.class_group_id' => $o_class->class_group_id
                            ]);
    
                            if (!$mba_class_group_details) {
                                print('class details not found!'.$o_class->class_group_id);
                                // print('<br>');
                                exit;
                            }
                            $o_class_group = $mba_class_group_details[0];
                            if (is_null($o_class_group->semester_id)) {
                                print('semester is empty!'.$o_class_group->class_group_subject_id);
                                exit;
                                // array_push($a_class_not_found, $o_class_group->class_group_subject_id);
                            }
    
                            $mba_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $o_class_group->semester_id]);
                            $o_sheet->setCellValue('A'.$i_row, $o_lecturer->personal_data_name);
                            $o_sheet->setCellValue('B'.$i_row, $o_class_group->subject_code);
                            $o_sheet->setCellValue('C'.$i_row, $o_class_group->subject_name);
                            $o_sheet->setCellValue('D'.$i_row, $o_class_group->study_program_abbreviation);
                            $o_sheet->setCellValue('E'.$i_row, $mba_semester_data[0]->semester_number);
                            $i_row++;
                        }
                    }
                }
            }

            // if (count($a_class_not_found) > 0) {
            //     exit;
            // }
            // else {
            //     print('class ok');exit;
            // }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            if ($b_force_download) {
                $a_path_info = pathinfo($s_file_path.$s_filename);
                $s_file_ext = $a_path_info['extension'];
                header('Content-Disposition: attachment; filename='.urlencode($s_filename));
                readfile( $s_file_path.$s_filename );
                exit;
            }
        }
    }

    public function download_class_by_lecturer($s_academic_year_id, $s_semester_type_id, $b_force_download = false)
    {
        $mba_class_master_list = $this->General->get_where('dt_class_master', [
            'academic_year_id' => $s_academic_year_id,
            'semester_type_id' => $s_semester_type_id
        ]);
        
        if ($mba_class_master_list) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

            $s_file_name = 'Class_Group_List_('.$s_academic_year_id.'-'.$s_semester_type_id.')';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/temp/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $i_row = 1;

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Alumni Services")
                ->setCategory("Hasil Survey Pengguna Alumni");

            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_sheet->setCellValue('A'.$i_row, 'Lecturer');
            $o_sheet->setCellValue('B'.$i_row, 'Lecturer Reported');
            $o_sheet->setCellValue('C'.$i_row, 'NIDN Lecturer Reported');
            $o_sheet->setCellValue('D'.$i_row, 'Credit Allocation');
            $o_sheet->setCellValue('E'.$i_row, 'Study Program');
            $o_sheet->setCellValue('F'.$i_row, 'Subject');
            $o_sheet->setCellValue('G'.$i_row, 'Subject SKS');
            $o_sheet->setCellValue('H'.$i_row, 'Class Group Name');
            $o_sheet->setCellValue('I'.$i_row, 'Count Student');
            $o_sheet->setCellValue('J'.$i_row, 'Count Lecturer Absence');
            $i_row++;

            foreach ($mba_class_master_list as $key => $o_class_master) {
                $mba_class_details = $this->Cgm->get_class_master_filtered(['cm.class_master_id' => $o_class_master->class_master_id]);
                $mba_class_student = $this->Cgm->get_class_master_student($o_class_master->class_master_id);
                $mba_class_prodi = $this->Cgm->get_class_master_study_program($o_class_master->class_master_id);
                $a_prodi = [];

                if ($mba_class_prodi) {
                    foreach ($mba_class_prodi as $o_prodi) {
                        if (!in_array($o_prodi->study_program_abbreviation, $a_prodi)) {
                            array_push($a_prodi, $o_prodi->study_program_abbreviation);
                        }
                    }
                }

                if ($mba_class_details) {
                    $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer([
                        'class_master_id' => $o_class_master->class_master_id
                    ]);
                    
                    foreach ($mba_class_master_lecturer as $keyl => $o_class_master_lecturer) {
                        $mba_class_absence = $this->General->get_where('dt_class_subject_delivered', [
                            'class_master_id' => $o_class_master->class_master_id,
                            'employee_id' => $o_class_master_lecturer->employee_id
                        ]);

                        $mba_lecturer_reported = false;
                        if (!is_null($o_class_master_lecturer->employee_id_reported)) {
                            $mba_lecturer_reported = $this->Emm->get_employee_data(['em.employee_id' => $o_class_master_lecturer->employee_id_reported]);
                        }

                        $o_sheet->setCellValue('A'.$i_row, $o_class_master_lecturer->personal_data_name);
                        $o_sheet->setCellValue('B'.$i_row, ($mba_lecturer_reported) ? $mba_lecturer_reported[0]->personal_data_name : '');
                        $o_sheet->setCellValue('C'.$i_row, ($mba_lecturer_reported) ? '="'.$mba_lecturer_reported[0]->employee_lecturer_number.'"' : '');
                        $o_sheet->setCellValue('D'.$i_row, $o_class_master_lecturer->credit_allocation);
                        $o_sheet->setCellValue('E'.$i_row, implode(' / ', $a_prodi));
                        $o_sheet->setCellValue('F'.$i_row, $mba_class_details[0]->subject_name);
                        $o_sheet->setCellValue('G'.$i_row, $mba_class_details[0]->curriculum_subject_credit.' SKS');
                        $o_sheet->setCellValue('H'.$i_row, $o_class_master->class_master_name);
                        $o_sheet->setCellValue('I'.$i_row, (($mba_class_student) ? count($mba_class_student) : 0));
                        $o_sheet->setCellValue('J'.$i_row, (($mba_class_absence) ? count($mba_class_absence) : 0));
                        $i_row++;
                    }
                }
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            if ($b_force_download) {
                $a_path_info = pathinfo($s_file_path.$s_filename);
                // print('<pre>');var_dump($a_path_info);exit;
                $s_file_ext = $a_path_info['extension'];
                header('Content-Disposition: attachment; filename='.urlencode($s_filename));
                readfile( $s_file_path.$s_filename );
                exit;
            }
        }
        else {
            print('data not found!<br>');
        }
        print('total:'.count($mba_class_master_list));exit;
    }

    public function print_result($a_data_list, $a_key, $a_header, $s_file_name)
    {
        if ((count($a_data_list) > 0) AND (count($a_header) > 0) AND (count($a_key) > 0)) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/temp/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $i_row = 1;
            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI IT Services");

            $o_sheet = $o_spreadsheet->getActiveSheet();
            $s_cols = 'A';
            foreach ($a_header as $s_header) {
                $o_sheet->setCellValue($s_cols.$i_row, $s_header);
                $o_sheet->getStyle($s_cols.$i_row)->getFont()->setBold( true );
                $s_cols++;
            }
            $i_row++;
            
            foreach ($a_data_list as $a_data) {
                $s_cols = 'A';
                foreach ($a_key as $s_key) {
                    $o_sheet->setCellValue($s_cols.$i_row, $a_data[$s_key]);
                    $s_cols++;
                }
                $i_row++;
            }

            // $c = 'A';
            for ($i='A'; $i < $s_cols; $i++) { 
                $o_sheet->getColumnDimension($i)->setAutoSize(true);
                // $c++;
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            return $s_filename;
        }
        else {
            return false;
        }
    }

}
