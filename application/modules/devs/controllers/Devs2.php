<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;	

class Devs2 extends App_core
{
    public $style_border = array(
        'borders' => array(
            'allBorders' => array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => '00000000'],
            )
        )
    );

    function __construct()
    {
        parent::__construct();
        $this->load->model('academic/Ofse_model', 'Ofm');
        $this->load->model('academic/Class_group_model', 'Cgm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('devs/Devs_model', 'Dm');
        $this->load->model('academic/Subject_model', 'Sbm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('finance/Invoice_model', 'Im');
        $this->load->model('finance/Finance_model', 'Fm');
        $this->load->model('finance/Bni_model', 'Bnim');
        $this->load->model('personal_data/Family_model', 'Fmm');
        $this->load->model('academic/Offered_subject_model', 'Osm');

        $this->load->model('portal/Portal_model', 'Mdb');
        // $this->load->library('FeederAPI', ['mode' => 'production']);
    }

    function find_text() {
        $s_string = 'CHANDRA HENDRIANTO';
        // $s_string = '47013ff8-89df-11ef-8f45-0068eb6957a0';
        $path = APPPATH;
        
        $list = $this->finding($path, $s_string);
        print('<pre>');var_dump($list);
    }

    function finding($s_dir, $s_stringfind = false) {
        $a_exept_find = ['.', '..', '...', 'uploads', 'vendor', 'vendor.old', 'logs', 'cache'];
        $a_extension_allowed = ['php'];
        $list_dir = scandir($s_dir);
        $a_result = [];
        
        foreach ($list_dir as $s_directory) {
            if (!in_array($s_directory, $a_exept_find)) {
                $s_newpath = $s_dir.$s_directory.'/';
                if (is_dir($s_newpath)) {
                    $this->finding($s_newpath, $s_stringfind);
                }
                else {
                    $s_newpath = $s_dir.$s_directory;
                    if (file_exists($s_newpath)) {
                        $a_file = explode('.', $s_directory);
                        $s_extension = $a_file[count($a_file) - 1];
                        if (in_array($s_extension, $a_extension_allowed)) {
                            $content = file_get_contents($s_newpath);
                            $finder = strpos($content, $s_stringfind);
                            if ($finder !== false) {
                                // print($s_newpath);
                                // print('<br>');
                                array_push($a_result, $s_newpath);
                            }
                        }
                    }
                    
                }
            }
        }
        return $a_result;
    }

    function goahead_pin() {
        $s_path_file = APPPATH."uploads/academic/pin_kemdikbud/";
        $ls = scandir($s_path_file);

        print('<table border="1" style="width: 100%;">');
        print('<tr><td>NIM</td><td>Nama</td><td>Nomor Ijazah</td><td>Kode Batch</td></tr>');

        foreach ($ls AS $list) {
            if (!in_array($list, ['.', '..'])) {
                $filepath  = $s_path_file.$list;
                
                $o_spreadsheet = IOFactory::load($filepath);
                $o_sheet = $o_spreadsheet->getActiveSheet();
                $s_kodebatch = str_replace("Daftar Nomor Ijazah-", "", $list);
                $s_kodebatch = str_replace(".xlsx", "", $s_kodebatch);

                $i_row = 7;
                while (!empty(trim($o_sheet->getCell("B$i_row")->getValue()))) {
                    $nim = $o_sheet->getCell("B$i_row")->getValue();
                    $nama = $o_sheet->getCell("C$i_row")->getValue();
                    $pin = $o_sheet->getCell("D$i_row")->getValue();

                    print('<tr>');
                    print('<td>'.$nim.'</td>');
                    print('<td>'.$nama.'</td>');
                    print('<td>'.$pin.'</td>');
                    print('<td>'.$s_kodebatch.'</td>');
                    print('</tr>');
                    $i_row++;
                }
                
                // print($filepath);
                // print('<br>');
            }
        }

        print('</table>');
        // print('<pre>');var_dump($ls);exit;
    }

    public function rescheduler() {
        $s_file = APPPATH."uploads/templates/testing_schedule.csv";
        $data = file($s_file);

        if (count($data) > 0) {
            $index = 0;
            foreach ($data as $line => $s_line_data) {
                $index++;
                $a_data = explode(',', $s_line_data);
                $a_username = explode(']', $a_data[0]);
                $s_username = str_replace('[', '', $a_username[0]);
                print($s_username);
                print('<br>');
            }
        }
        // print('<pre>');var_dump($data);exit;
    }

    public function render_page()
    {
        $url = 'https://web.iuli.ac.id/news/wp-json/wp/v2/posts?categories=49';
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_GET, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json'
		));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch));
        // $result = curl_exec($ch);
		curl_close($ch);
        $o_content = $result[0]->content;
        echo $o_content->rendered;
        exit;
        // var_dump();exit;
    }

    public function set_active_date()
    {
        $mba_student_active = $this->Stm->get_student_filtered([
            'ds.academic_year_id' => '2021'
        ], ['active', 'inactive', 'onleave', 'graduated']);
        if ($mba_student_active) {
            $i_numb = 1;
            foreach ($mba_student_active as $o_student) {
                $mba_student_invoice = $this->Im->student_has_invoice_list($o_student->personal_data_id, [
                    'df.payment_type_code' => '02'
                ]);

                if ($mba_student_invoice) {
                    $o_first_invoice = $mba_student_invoice[count($mba_student_invoice) - 1];
                    $mba_first_payment = $this->Im->get_first_payment([
                        'di.invoice_id' => $o_first_invoice->invoice_id,
                        'sub_invoice_details_datetime_paid_off != ' => NULL
                    ]);
                    // print('<pre>');var_dump();exit;
                    if ($mba_first_payment) {
                        $s_active_date_by_payment = $mba_first_payment[0]->sub_invoice_details_datetime_paid_off;
                        $s_startdate = date('Y-m-d H:i:s', strtotime($o_student->student_date_enrollment));
                        $s_enddate = date('Y-m-d H:i:s', strtotime($s_active_date_by_payment));
                        $s_result = ($s_startdate >= $s_enddate) ? 'false' : 'true';
                        $s_student_id = $o_student->student_id;
                        print($i_numb++.', '.$o_student->student_date_enrollment.' - '.$s_active_date_by_payment.' :'.$s_result);
                        // $s_script = "UPDATE dt_student SET student_date_active='$s_enddate' WHERE student_id = '$s_student_id';";
                        // print($s_script);
                        print('<br>');
                    }
                }
                // exit;
            }
        }
    }

    public function sync_hid()
    {
        $this->load->model('portal/Portal_model', 'Pm');
        $this->load->model('hris/Hris_model', 'Hrm');
        $mba_hid_data = $this->Pm->retrieve_data('hid');

        if ($mba_hid_data) {
            $a_personal_data_not_found = [];
            foreach ($mba_hid_data as $o_hid) {
                $mba_personal_data = $this->General->get_where('dt_personal_data', ['portal_id' => $o_hid->personal_data_id]);
                if (!$mba_personal_data) {
                    // print('not found.... '.$mba_personal_data[0]->personal_data_name);
                    array_push($a_personal_data_not_found, $o_hid->personal_data_id);
                }
                else if (!in_array($o_hid->id, [76, 75])) {
                    print('processing.... '.$mba_personal_data[0]->personal_data_name);
                    $mba_hid_data = $this->General->get_where('dt_hid', ['portal_id' => $o_hid->id]);
                    $mba_employee_data = $this->General->get_where('dt_employee', ['personal_data_id' => $mba_personal_data[0]->personal_data_id]);
                    $s_status = strtolower($o_hid->status);
                    if ($mba_employee_data) {
                        if ($mba_employee_data[0]->status == 'active') {
                            $s_status = 'active';
                        }
                        else {
                            $s_status = 'inactive';
                        }
                    }

                    $a_hid_data = [
                        'hid_id' => $this->uuid->v4(),
                        'personal_data_id' => $mba_personal_data[0]->personal_data_id,
                        'hid_key' => $o_hid->hidkey,
                        'hid_status' => $s_status,
                        'portal_id' => $o_hid->id,
                        'date_added' => date('Y-m-d H:i:s')
                    ];

                    if ($mba_hid_data) {
                        unset($a_hid_data['hid_id']);
                        unset($a_hid_data['date_added']);
                        $this->Hrm->submit_hid($a_hid_data, $mba_hid_data[0]->hid_id);
                    }
                    else {
                        $this->Hrm->submit_hid($a_hid_data);
                    }
                }
                print('<br>');
                // exit;
            }

            if (count($a_personal_data_not_found)) {
                print('<pre>');
                var_dump($a_personal_data_not_found);
            }
        }
    }

    public function check_sub_invoice()
    {
        $mba_sub_invoice = $this->General->get_where('dt_sub_invoice', [
            'sub_invoice_status != ' => 'paid',
        ]);

        $a_data_result = [];

        if ($mba_sub_invoice) {
            foreach ($mba_sub_invoice as $o_sub_invoice) {
                $mba_invoice_data = $this->General->get_where('dt_invoice', [
                    'invoice_id' => $o_sub_invoice->invoice_id
                ]);

                if (($mba_invoice_data) AND ($mba_invoice_data[0]->invoice_status != 'paid')) {
                    $mba_sub_invoice_details = $this->General->get_where('dt_sub_invoice_details', [
                        'sub_invoice_id' => $o_sub_invoice->sub_invoice_id
                    ]);

                    if ($mba_sub_invoice_details) {
                        $a_installment_paid = [];
                        $d_total_amount = 0;
                        $d_total_amount_paid = 0;
                        $b_paid = false;
                        foreach ($mba_sub_invoice_details as $o_sub_invoice_details) {
                            if ($o_sub_invoice_details->sub_invoice_details_status == 'paid') {
                                array_push($a_installment_paid, $o_sub_invoice_details->sub_invoice_details_id);
                                $d_total_amount_paid += $o_sub_invoice_details->sub_invoice_details_amount_paid;
                            }

                            $d_total_amount += $o_sub_invoice_details->sub_invoice_details_amount_total;
                        }

                        $d_sisa = $d_total_amount - $d_total_amount_paid;

                        if ($a_installment_paid == count($mba_sub_invoice_details)) {
                            $b_paid = true;
                        }

                        if ($o_sub_invoice->sub_invoice_type == 'full') {
                            if ($o_sub_invoice->sub_invoice_amount_total != $d_sisa) {
                                array_push($a_data_result, [
                                    'sub_invoice_id' => $o_sub_invoice->sub_invoice_id,
                                    'invoice_id' => $o_sub_invoice->invoice_id,
                                    'sub_invoice_amount' => $o_sub_invoice->sub_invoice_amount,
                                    'sub_invoice_amount_total' => $o_sub_invoice->sub_invoice_amount_total,
                                    'total_details_amount' => $d_sisa,
                                    'sub_invoice_type' => $o_sub_invoice->sub_invoice_type,
                                    'status' => ($b_paid) ? 'paid' : 'unpaid'
                                ]);
                            }
                        }
                        else if ($o_sub_invoice->sub_invoice_type == 'installment') {
                            // if (count($a_installment_paid) == 0) {
                            //     print('update '.$mba_sub_invoice_details[0]->sub_invoice_details_description);
                            //     print('<br>');
                            //     $this->Im->update_sub_invoice([
                            //         'sub_invoice_amount_total' => $o_sub_invoice->sub_invoice_amount
                            //     ], [
                            //         'sub_invoice_id' => $o_sub_invoice->sub_invoice_id
                            //     ]);
                            // }

                            if ($o_sub_invoice->sub_invoice_amount_total != $d_sisa) {
                                array_push($a_data_result, [
                                    'sub_invoice_id' => $o_sub_invoice->sub_invoice_id,
                                    'invoice_id' => $o_sub_invoice->invoice_id,
                                    'sub_invoice_amount' => $o_sub_invoice->sub_invoice_amount,
                                    'sub_invoice_amount_total' => $o_sub_invoice->sub_invoice_amount_total,
                                    'total_details_amount' => $d_sisa,
                                    'sub_invoice_type' => $o_sub_invoice->sub_invoice_type,
                                    'status' => ($b_paid) ? 'paid' : 'unpaid'
                                ]);
                            }
                        }
                    }
                }
            }
        }

        print('<pre>');
        var_dump($a_data_result);exit;
    }

    public function force_send_email()
    {
        print('closed!');exit;
        $s_text = <<<TEXT
Dear VERENA SOEWANDI,

You have registered the following subject/s:

1. Civics
2. English 4
3. English 6
4. Pancasila


Please wait for the evaluation from the Head of Study Program to confirm
your registration & the decision to run the courses in this short semester.
We will inform you about this by email. Please check your email accordingly.

After that, please do confirmation for your registration of short semester
status on your student portal http://student.iuli.ac.id

Make sure that you confirm the registration status to get a virtual account
number for payment and that you are registered in the short semester.


Academic Services Centre
International University Liaison Indonesia - IULI.
TEXT;
        $s_to = 'verena.soewandi@stud.iuli.ac.id';
        // $s_to = 'employee@company.ac.id';
        $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		$this->email->initialize($config);
        
        $this->email->from('employee@company.ac.id', 'IULI Academic Services Centre');
		$this->email->to($s_to);
        $this->email->cc('thing16188@gmail.com');
        $this->email->bcc(['employee@company.ac.id']);
        $this->email->subject("Confirmation of Subject Registration for Short Semester");
		$this->email->message($s_text);
		if ($this->email->send()) {
            print('sukses');
        }
        else{
            print('gagal');
        }
    }

    public function create_development_billing()
	{
		// $s_va_number = $this->Bnim->get_va_number(
		// 	'01',
		// 	0,
		// 	0,
		// 	'candidate',
		// 	null,
		// 	2021,
		// 	1
		// );

		// print($s_va_number);exit;
		$a_trx_data = array(
			'trx_amount' => '200000',
			'billing_type' => 'c',
			'customer_name' => 'account testing',	
			'virtual_account' => '8141010001210001',
			'description' => 'testing payment development',
			'datetime_expired' => '2021-07-08 23:59:59',
			'customer_email' => 'bni.employee@company.ac.id',
            'type' => 'createbilling',
            'client_id' => '141',
            'trx_id' => mt_rand()
		);
		
        $s_hashed_string = $this->libapi->hash_data(
			$a_trx_data,
			'141',
			'2d9b7b2442a0dd722690b8c525a52915'
		);
		
		$a_post_data = array(
			'client_id' => '141',
			'data' => $s_hashed_string,
		);
		$o_bni_response = $this->libapi->post_data('https://apibeta.bni-ecollection.com/', json_encode($a_post_data));

		print('<pre>');
		var_dump($o_bni_response);
	}

    public function read_file()
    {
        $this->a_page_data['body'] = $this->load->view('form/form_read_file', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function reading_files()
    {
        if ($this->input->post('target') == '') {
            show_404();
        }
        else {
            $s_file = $_FILES['file_input']['tmp_name'];
            switch ($this->input->post('target')) {
                case 'get_invoice':
                    print('closed!');exit;
                    $o_spreadsheet = IOFactory::load("$s_file");
                    $o_sheet = $o_spreadsheet->getActiveSheet();
                    $i_row = 2;
                    $i_send = 0;

                    $a_invoice_id = [];

                    print('<table border="1" style="width: 100%;">');
                    print('<tr><td>invoice_id</td><td>personal_data_name</td><td>semester_id</td><td>amount</td><td>description</td><td>Send</td></tr>');

                    while ($o_sheet->getCell("A$i_row")->getValue() !== NULL) {
                        $mbo_student_data = $this->Stm->get_student_filtered([
                            'dpd.personal_data_name' => $o_sheet->getCell("A$i_row")->getValue(),
                            'ds.student_number' => $o_sheet->getCell("B$i_row")->getValue()
                        ]);

                        if (!$mbo_student_data) {
                            print('row '.$i_row.' not found!');exit;
                        }
                        
                        $o_semester_data = $this->General->get_where('ref_semester', ['semester_number' => $o_sheet->getCell("J$i_row")->getValue()]);
                        if (!$o_semester_data) {
                            print("semester row $i_row not found");exit;
                        }

                        $mbo_invoice_student = $this->Im->student_has_invoice_data($mbo_student_data[0]->personal_data_id, [
                            'df.payment_type_code' => '02',
                            'df.semester_id' => $o_semester_data[0]->semester_id,
                            'di.invoice_status != ' => 'cancelled'
                        ]);

                        if (!$mbo_invoice_student) {
                            print("row $i_row tidak ada invoice!");exit;
                        }

                        $b_has_send = 'false';
                        if (in_array($mbo_invoice_student->invoice_status, ['created', 'pending'])) {
                            if (!in_array($mbo_invoice_student->invoice_id, $a_invoice_id)) {
                                array_push($a_invoice_id, $mbo_invoice_student->invoice_id);
                                $mba_invoice_unpaid = $this->Im->get_unpaid_invoice(['di.invoice_id' => $mbo_invoice_student->invoice_id]);
    
                                if ($mba_invoice_unpaid) {
                                    // print('send to '.$mba_invoice_unpaid[0]->personal_data_name.' <br>');
                                    $i_send++;
                                    modules::run('callback/api/send_reminder', $mba_invoice_unpaid[0]);
                                    $b_has_send = 'true';
                                }
                            }
                        }
                        
                        print('<tr>');
                        print('<td>'.$mbo_invoice_student->invoice_id.'</td>');
                        print('<td>'.$mbo_student_data[0]->personal_data_name.'</td>');
                        print('<td>'.$mbo_invoice_student->semester_id.'</td>');
                        print('<td>'.$mbo_invoice_student->fee_amount.'</td>');
                        print('<td>'.$mbo_invoice_student->invoice_description.'</td>');
                        print('<td>'.$b_has_send.'</td>');
                        print('</tr>');
                        $i_row++;
                    }
                    print('</table>');

                    print('<h1>Total send '.$i_send.' invoice:</h1>');
                    // print('<h1>Total '.count($a_invoice_id).' invoice:</h1>');
                    // print(implode("','", $a_invoice_id));
                    exit;
                    break;
                
                case 'read_ecoll':
                    $this->convert_bni_report($s_file);
                    break;

                default:
                    show_404();
                    break;
            }
        }
    }

    public function convert_bni_report($s_file)
    {
        $o_spreadsheet = IOFactory::load("$s_file");
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $i_row = 2;
        $a_trx_id_not_found = [];
        while ($o_sheet->getCell("G".$i_row)->getValue() !== NULL) {
            $s_trx_id = str_replace('="', '', $o_sheet->getCell("G".$i_row)->getValue());
            $s_trx_id = str_replace('"', '', $s_trx_id);

            $mba_sub_invoice_details_data = $this->General->get_where('dt_sub_invoice_details', [
                'trx_id' => $s_trx_id
            ]);

            if (!$mba_sub_invoice_details_data) {
                $mba_sub_invoice_details_portal = $this->Mdb->retrieve_data('bni_transactions', [
                    'trx_id' => $s_trx_id
                ]);

                if ((!$mba_sub_invoice_details_portal) AND (!in_array($s_trx_id, $a_trx_id_not_found))) {
                    array_push($a_trx_id_not_found, $s_trx_id);
                }
            }
        }

        print('<pre>');
        var_dump($a_trx_id_not_found);exit;
        // var_dump($o_sheet->getCell("A2")->getValue());
    }

    public function cek_forlap()
    {
        $a_aktivitas_dikti = $this->feederapi->post('GetListMahasiswa', [
            'filter' => "nim='11201910007'"
        ]);
        // $a_aktivitas_dikti = $this->feederapi->post('GetAktivitasKuliahMahasiswa', [
        //     'filter' => "id_semester='20151'"
        // ]);
        print('<pre>');
        var_dump($a_aktivitas_dikti);exit;
    }

    public function subsstr()
    {
        $s_string = '20192';
        $ac = substr($s_string, 0, 4);
        print($ac);
    }

    public function get_historycall_study()
    {
        // print('process!');exit;
        error_reporting(0);
        $a_result = $this->feederapi->post('GetAllProdi', [
			'filter' => "kode_perguruan_tinggi='041058'"
		]);

        if ((is_array($a_result->data)) AND (count($a_result->data) > 0)) {
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'Rekap_Aktivitas_Perkuliahan_Mahasiswa';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/academic/custom_request/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services")
                ->setCategory("Invoice Batch Report");

            $o_sheet_rasio = $o_spreadsheet->getSheet(0)->setTitle('Student Rasio');
            $o_spreadsheet->createSheet();
            $o_sheet_pdpt = $o_spreadsheet->getSheet(1)->setTitle('Student PDPT');
            
            $i_row_pdpt = 4;
            $i_start_row_pdpt = $i_row_pdpt;
            $a_col_pdpt = [];
            $a_semester_dikti = [];

            $o_sheet_pdpt->setCellValue('A2', "Prodi");
            $o_sheet_pdpt->mergeCells('A2:A3');
        
            $mba_semester_list = $this->Smm->get_semester_setting(false, false, false, "ASC");
            foreach ($a_result->data as $key => $o_prodi) {
                $c_col_pdpt = 'B';
                if ($o_prodi->id_prodi != 'bfad84ea-f6f9-441e-af70-f75900b6112f') {
                    
                    $o_sheet_pdpt->setCellValue('A'.$i_row_pdpt, $o_prodi->nama_program_studi);

                    foreach ($mba_semester_list as $o_semester) {
                        $a_aktivitas_dikti = $this->feederapi->post('GetAktivitasKuliahMahasiswa', [
                            'filter' => "id_semester='".$o_semester->academic_year_id.$o_semester->semester_type_id."'"
                        ]);
                        
                        if (count($a_aktivitas_dikti->data) > 0) {
                            if (!in_array($o_semester->academic_year_id.$o_semester->semester_type_id, $a_semester_dikti)) {
                                array_push($a_semester_dikti, $o_semester->academic_year_id.$o_semester->semester_type_id);
                            }

                            $s_clause = "dss.academic_year_id = '$o_semester->academic_year_id' AND dss.semester_type_id = '$o_semester->semester_type_id' AND (sp.study_program_id = '$o_prodi->id_prodi' OR sp.study_program_main_id = '$o_prodi->id_prodi')";
                            $s_active_clause = $s_clause." AND dss.student_semester_status = 'active'";
                            $s_inactive_clause = $s_clause." AND dss.student_semester_status = 'inactive'";
                            $s_onleave_clause = $s_clause." AND dss.student_semester_status = 'onleave'";

                            $mba_student_active = $this->Smm->get_student_semester($s_active_clause);
                            $mba_student_onleave = $this->Smm->get_student_semester($s_onleave_clause);
                            $mba_student_inactive = $this->Smm->get_student_semester($s_inactive_clause);

                            $c_start_semester = $c_col_pdpt;
                            if (!in_array($c_col_pdpt, $a_col_pdpt)) {
                                array_push($a_col_pdpt, $c_col_pdpt);
                            }

                            $o_sheet_pdpt->setCellValue($c_col_pdpt.'2', ($o_semester->academic_year_id.'-'.$o_semester->semester_type_id));
                            $o_sheet_pdpt->setCellValue($c_col_pdpt.'3', 'Aktif');
                            $o_sheet_pdpt->setCellValue($c_col_pdpt.$i_row_pdpt, (($mba_student_active) ? count($mba_student_active) : 0));

                            $c_col_pdpt++;
                            if (!in_array($c_col_pdpt, $a_col_pdpt)) {
                                array_push($a_col_pdpt, $c_col_pdpt);
                            }
                            $o_sheet_pdpt->setCellValue($c_col_pdpt.'3', 'Cuti');
                            $o_sheet_pdpt->setCellValue($c_col_pdpt.$i_row_pdpt, (($mba_student_onleave) ? count($mba_student_onleave) : 0));

                            $c_col_pdpt++;
                            if (!in_array($c_col_pdpt, $a_col_pdpt)) {
                                array_push($a_col_pdpt, $c_col_pdpt);
                            }
                            $o_sheet_pdpt->setCellValue($c_col_pdpt.'3', 'Pertukaran');
                            $o_sheet_pdpt->setCellValue($c_col_pdpt.$i_row_pdpt, '0');

                            $c_col_pdpt++;
                            if (!in_array($c_col_pdpt, $a_col_pdpt)) {
                                array_push($a_col_pdpt, $c_col_pdpt);
                            }
                            $o_sheet_pdpt->setCellValue($c_col_pdpt.'3', 'Non Aktif');
                            $o_sheet_pdpt->setCellValue($c_col_pdpt.$i_row_pdpt, (($mba_student_inactive) ? count($mba_student_inactive) : 0));

                            $c_col_pdpt++;
                            if (!in_array($c_col_pdpt, $a_col_pdpt)) {
                                array_push($a_col_pdpt, $c_col_pdpt);
                            }
                            $o_sheet_pdpt->setCellValue($c_col_pdpt.'3', 'Sedang DD');
                            $o_sheet_pdpt->setCellValue($c_col_pdpt.$i_row_pdpt, '0');

                            $o_sheet_pdpt->mergeCells($c_start_semester.'2'.':'.$c_col_pdpt.'2');
                            $c_col_pdpt++;
                            if (!in_array($c_col_pdpt, $a_col_pdpt)) {
                                array_push($a_col_pdpt, $c_col_pdpt);
                            }
                        }
                    }

                    $i_row_pdpt++;
                }
            }

            $c_max_col = $c_col_pdpt;
            if (count($a_col_pdpt) > 0) {
                foreach ($a_col_pdpt as $c_col) {
                    $c_max_col = $c_col;
                    $o_sheet_pdpt->setCellValue($c_col.$i_row_pdpt, '=SUM('.$c_col.$i_start_row_pdpt.':'.$c_col.($i_row_pdpt - 1).')');
                }
            }

            $c_max_col--;
            $o_sheet_pdpt->getStyle("B2:".($c_max_col--).$i_row_pdpt)->getAlignment()->setHorizontal('center');
            $o_sheet_pdpt->getStyle('A2:'.$c_max_col.$i_row_pdpt)->applyFromArray($this->style_border);
            // $o_sheet->getColumnDimension($c_max_col)->setVisible(false);

            $this->student_semester($o_spreadsheet, $a_semester_dikti);

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
            print('<pre>');var_dump($a_result);exit;
        }
    }

    public function student_semester($o_spreadsheet = false)
    {
        // $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        // $s_file_name = 'Rekap_Aktivitas_Perkuliahan_Mahasiswa_Persemester';
        // $s_filename = $s_file_name.'.xlsx';

        // $s_file_path = APPPATH."uploads/academic/custom_request/";
        // if(!file_exists($s_file_path)){
        //     mkdir($s_file_path, 0777, TRUE);
        // }

        // $o_spreadsheet = IOFactory::load($s_template_path);
        
        // $o_spreadsheet->getProperties()
        //     ->setTitle($s_file_name)
        //     ->setCreator("IULI Academic Services")
        //     ->setCategory("Dikti Validation");
        
        $mba_semester_list = $this->Smm->get_semester_setting(false, false, false, 'ASC');
        $mba_batch_data = $this->General->get_where('dt_academic_year');
        $i_sheet = 2;
        if ($mba_batch_data) {
            foreach ($mba_batch_data as $o_academic_year) {
                $a_mahasiswa_dikti = $this->feederapi->post('GetListMahasiswa', [
                    'filter' => "id_periode='".$o_academic_year->academic_year_id."1' OR id_periode='".$o_academic_year->academic_year_id."2'"
                ]);

                if (count($a_mahasiswa_dikti->data) > 0) {
                    $o_spreadsheet->createSheet();
                    $o_sheet = $o_spreadsheet->getSheet($i_sheet)->setTitle('Batch '.$o_academic_year->academic_year_id);
                    $i_sheet++;

                    $mba_student_data = $this->Stm->get_student_filtered(['academic_year_id' => $o_academic_year->academic_year_id], ['active', 'inactive', 'onleave','graduated', 'resign', 'dropout']);
                    if ($mba_student_data) {
                        $o_sheet->setCellValue("A4", "No.");
                        $o_sheet->setCellValue("B4", "Nama Mahasiswa");
                        $o_sheet->setCellValue("C4", "NIM");
                        $o_sheet->setCellValue("D4", "Prodi");
                        $o_sheet->setCellValue("E4", "IPK");

                        $o_sheet->mergeCells('A4'.':A5');
                        $o_sheet->mergeCells('B4'.':B5');
                        $o_sheet->mergeCells('C4'.':C5');
                        $o_sheet->mergeCells('D4'.':D5');
                        $o_sheet->mergeCells('E4'.':E5');

                        $a_empty_col = [];
                        $c_col_general = 'F';
                        $c_col_start = $c_col_general;
                        if ($mba_semester_list) {
                            $c_header_semester = $c_col_general;
                            foreach ($mba_semester_list as $o_semester) {
                                $s_semester_dikti = $o_semester->academic_year_id.$o_semester->semester_type_id;
                                if ($o_semester->academic_year_id < $o_academic_year->academic_year_id) {
                                    // if (!in_array($c_header_semester, $a_empty_col)) {
                                    //     // array_push($a_empty_col, $c_header_semester);
                                    //     // $o_sheet->getColumnDimension($c_header_semester)->setVisible(false);
                                    // }
                                    // $o_sheet->getColumnDimension($c_header_semester)->setVisible(false);
                                    $c_col_not_visible = $c_header_semester;
                                    $o_sheet->getColumnDimension($c_col_not_visible)->setVisible(false);
                                    $c_col_not_visible++;
                                    $o_sheet->getColumnDimension($c_col_not_visible)->setVisible(false);
                                    if ($o_semester->semester_type_id == 2) {
                                        $c_col_not_visible++;
                                        $o_sheet->getColumnDimension($c_col_not_visible)->setVisible(false);
                                        $c_col_not_visible++;
                                        $o_sheet->getColumnDimension($c_col_not_visible)->setVisible(false);
                                    }
                                }

                                $o_sheet->setCellValue($c_header_semester."4", $s_semester_dikti);
                                $o_sheet->setCellValue($c_header_semester."5", 'Status');
                                $o_sheet->mergeCells($c_header_semester.'4'.':'.++$c_header_semester.'4');
                                $o_sheet->setCellValue($c_header_semester."5", 'IPS');

                                $c_header_semester++;

                                if ($o_semester->semester_type_id == 2) {
                                    $s_semester_dikti = $o_semester->academic_year_id.'3';
                                    $o_sheet->setCellValue($c_header_semester."4", $s_semester_dikti);
                                    $o_sheet->setCellValue($c_header_semester."5", 'Status');
                                    $o_sheet->mergeCells($c_header_semester.'4'.':'.++$c_header_semester.'4');
                                    $o_sheet->setCellValue($c_header_semester."5", 'IPS');
                                    $c_header_semester++;
                                }
                            }
                        }

                        $i_row = 6;
                        $i_num = 1;
                        $c_end_col = $c_col_general;
                        foreach ($mba_student_data as $o_student) {
                            $o_dikti_mahasiswa = $this->feederapi->post('GetListMahasiswa', [
                                'filter' => "id_registrasi_mahasiswa='$o_student->student_id'"
                            ]);

                            if (count($o_dikti_mahasiswa->data) > 0) {
                                $o_sheet->setCellValue("A".$i_row, $i_num++);
                                $o_sheet->setCellValue("B".$i_row, $o_student->personal_data_name);
                                $o_sheet->setCellValue("C".$i_row, $o_student->student_number);
                                $o_sheet->setCellValue("D".$i_row, $o_student->study_program_abbreviation);

                                $a_merit_student = [];
                                $a_sks_student = [];

                                if ($mba_semester_list) {
                                    $c_col = $c_col_general;
                                    foreach ($mba_semester_list as $o_semester) {
                                        $s_academic_year_id = $o_semester->academic_year_id;
                                        $s_semester_type_id = $o_semester->semester_type_id;

                                        $mbo_student_semester = $this->Smm->get_student_semester([
                                            'dss.student_id' => $o_student->student_id,
                                            'dss.academic_year_id' => $s_academic_year_id,
                                            'dss.semester_type_id' => $s_semester_type_id
                                        ])[0];

                                        if ($mbo_student_semester) {
                                            $c_col_start = $c_col;
                                            $a_score_filter = array(
                                                'sc.student_id' => $o_student->student_id,
                                                'curs.curriculum_subject_type != ' => 'extracurricular',
                                                'curs.curriculum_subject_credit > ' => 0,
                                                'curs.curriculum_subject_category' => 'regular semester',
                                                'sc.score_approval' => 'approved',
                                                'sc.score_display' => 'TRUE',
                                                'sc.academic_year_id' => $s_academic_year_id,
                                                'sc.semester_type_id' => $s_semester_type_id
                                            );
        
                                            $mba_student_score = $this->Scm->get_score_data_transcript($a_score_filter);
                                            $a_merit_semester = [];
                                            $a_sks_semester = [];
                                            if ($mba_student_score) {
                                                foreach ($mba_student_score as $o_score) {
                                                    $s_score_sum = intval(round($o_score->score_sum, 0, PHP_ROUND_HALF_UP));
                                                    $s_grade_point = $this->grades->get_grade_point($s_score_sum);
                                                    $s_grade = $this->grades->get_grade($s_score_sum);
                                                    $s_merit = $this->grades->get_merit($o_score->curriculum_subject_credit, $s_grade_point);
                                                    
                                                    if ($this->Scm->get_good_grades($o_score->subject_name, $o_score->student_id, $o_score->score_sum)) {
                                                        array_push($a_merit_student, $s_merit);
                                                        array_push($a_sks_student, $o_score->curriculum_subject_credit);
                                                        array_push($a_merit_semester, $s_merit);
                                                        array_push($a_sks_semester, $o_score->curriculum_subject_credit);
                                                    }
                                                }
                                            }
                                            $s_total_merit = array_sum($a_merit_semester);
                                            $s_total_sks = array_sum($a_sks_semester);
                                            $gpa = $this->grades->get_ipk($s_total_merit, $s_total_sks);

                                            $o_sheet->setCellValue($c_col.$i_row, $mbo_student_semester->student_semester_status);
                                            $c_col++;
                                            $o_sheet->setCellValue($c_col.$i_row, $gpa);

                                            if ($s_semester_type_id == '2') {
                                                $c_col++;
                                                
                                                $a_score_filter_half = array(
                                                    'sc.student_id' => $o_student->student_id,
                                                    'curs.curriculum_subject_type != ' => 'extracurricular',
                                                    'curs.curriculum_subject_credit > ' => 0,
                                                    'curs.curriculum_subject_category' => 'regular semester',
                                                    'sc.score_approval' => 'approved',
                                                    'sc.score_display' => 'TRUE',
                                                    'sc.academic_year_id' => $s_academic_year_id
                                                );
            
                                                $mba_student_score_half = $this->Scm->get_score_data($a_score_filter_half, ['3', '7', '8']);
                                                $a_merit_semester_half = [];
                                                $a_sks_semester_half = [];
                                                if ($mba_student_score_half) {
                                                    foreach ($mba_student_score_half as $o_score) {
                                                        $s_score_sum = intval(round($o_score->score_sum, 0, PHP_ROUND_HALF_UP));
                                                        $s_grade_point = $this->grades->get_grade_point($s_score_sum);
                                                        $s_grade = $this->grades->get_grade($s_score_sum);
                                                        $s_merit = $this->grades->get_merit($o_score->curriculum_subject_credit, $s_grade_point);
                                                        
                                                        if ($this->Scm->get_good_grades($o_score->subject_name, $o_score->student_id, $o_score->score_sum)) {
                                                            array_push($a_merit_student, $s_merit);
                                                            array_push($a_sks_student, $o_score->curriculum_subject_credit);
                                                            array_push($a_merit_semester_half, $s_merit);
                                                            array_push($a_sks_semester_half, $o_score->curriculum_subject_credit);
                                                        }
                                                    }
                                                }
                                                $s_total_merit_half = array_sum($a_merit_semester_half);
                                                $s_total_sks_half = array_sum($a_sks_semester_half);
                                                $gpa_half = $this->grades->get_ipk($s_total_merit_half, $s_total_sks_half);
        
                                                if ($mba_student_score_half) {
                                                    $o_sheet->setCellValue($c_col.$i_row, (($mba_student_score_half) ? 'active' : 'inactive'));
                                                    $c_col++;
                                                    $o_sheet->setCellValue($c_col.$i_row, $gpa_half);
                                                }else{
                                                    $c_col++;
                                                }
                                            }
                                        }else{
                                            $c_col++;
                                            if ($s_semester_type_id == '2') {
                                                $c_col++;
                                                $c_col++;
                                            }
                                        }
                                        $c_end_col = $c_col;
                                        $c_col++;
                                    }
                                }

                                $s_total_merit_all = array_sum($a_merit_student);
                                $s_total_sks_all = array_sum($a_sks_student);
                                $s_gpa_all = $this->grades->get_ipk($s_total_merit_all, $s_total_sks_all);
                                $o_sheet->setCellValue('E'.$i_row, $s_gpa_all);

                                $i_row++;
                            }
                        }

                        $o_sheet->getStyle("E4:".($c_end_col).$i_row--)->getAlignment()->setHorizontal('center');
                        $o_sheet->getStyle('A4:'.$c_end_col.$i_row)->applyFromArray($this->style_border);
                        // if (count($a_empty_col) > 0) {
                        //     foreach ($a_empty_col as $c_cols) {
                        //         $o_sheet->getColumnDimension($c_cols)->setVisible(false);
                        //     }
                        // }
                    }
                }
            }
        }

        // $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        // $o_writer->save($s_file_path.$s_filename);
        // $o_spreadsheet->disconnectWorksheets();
        // unset($o_spreadsheet);

        // $a_path_info = pathinfo($s_file_path.$s_filename);
        // $s_file_ext = $a_path_info['extension'];
        // header('Content-Disposition: attachment; filename='.urlencode($s_filename));
        // readfile( $s_file_path.$s_filename );
        // exit;
    }

    // public function sync_student_data_from_mdb($s_mdb_student_id)
    // {
    //     print('warung tutup!');exit;
    //     $mbo_portal_student = $this->Mdb->retrieve_data('student', [
    //         'id' => $s_mdb_student_id
    //     ])[0];

    //     if ($mbo_portal_student) {
    //         $mba_portal_personal_data = $this->Mdb->retrieve_data('personal_data', ['id' => $mbo_portal_student->personal_data_id])[0];
    //         $mbo_staging_student = $this->General->get_where('dt_student', ['portal_id' => $s_mdb_student_id]);
            
    //         if (!$mbo_staging_student) {
    //             $mbo_staging_student = $this->General->get_where('dt_student', ['student_email' => $mbo_portal_student->iulimail]);
    //         }

    //         if (!$mbo_staging_student) {
    //             $mba_portal_study_program_data = $this->Mdb->retrieve_data('study_program', array('id' => $mbo_portal_student->study_program_id))[0];
    //             $mba_portal_academic_year_data = $this->Mdb->retrieve_data('academic_year', array('id' => $mbo_portal_student->academic_year_id))[0];
    //             $mba_portal_mother_data = $this->Mdb->retrieve_data('parents', array('student_id' => $mbo_portal_student->id));

    //             $s_prodi_abbreviation = ($mba_portal_study_program_data->abbreviation == 'COS') ? 'CSE' : $mba_portal_study_program_data->abbreviation;
    //             $s_student_status = ($mbo_portal_student->status == 'PENDINGPARTICIPANT') ? 'participant' : strtolower($mbo_portal_student->status);

    //             $mba_staging_study_program_data = $this->General->get_where('ref_study_program', array('study_program_abbreviation' => $s_prodi_abbreviation))[0];
                
    //             $s_personal_data_id = $this->uuid->v4();
    //             $s_student_id = $this->uuid->v4();

    //             $a_personal_data = [
    //                 'personal_data_id' => $s_personal_data_id,
    //                 'personal_data_name' => trim(strtoupper(str_replace('  ', ' ', implode(' ', array(
    //                     $mba_portal_personal_data->firstname, 
    //                     $mba_portal_personal_data->middlename,
    //                     $mba_portal_personal_data->lastname
    //                 ))))),
    //                 'personal_data_email' => (!is_null($mba_portal_personal_data->personal_email)) ? $mba_portal_personal_data->personal_email : NULL,
    //                 'personal_data_cellular' => (!is_null($mba_portal_personal_data->personal_mobilephone)) ? $mba_portal_personal_data->personal_mobilephone : '0',
    //                 'personal_data_email_confirmation' => 'yes',
    //                 'country_of_birth' => (is_null($mba_portal_personal_data->birth_country_id)) ? NULL : '9bb722f5-8b22-11e9-973e-52540001273f',
    //                 'citizenship_id' => (is_null($mba_portal_personal_data->country_id)) ? NULL : '9bb722f5-8b22-11e9-973e-52540001273f',
    //                 'religion_id' => (is_null($mba_portal_personal_data->religion_id)) ? NULL : '53b17ff0-e4c0-4fc9-8735-bbb8c7054048',
    //                 'personal_data_id_card_number' => (is_null($mba_portal_personal_data->idcard_number)) ? NULL : $mba_portal_personal_data->idcard_number,
    //                 'personal_data_place_of_birth' => (is_null($mba_portal_personal_data->birth_place)) ? NULL : $mba_portal_personal_data->birth_place,
    //                 'personal_data_date_of_birth' => (is_null($mba_portal_personal_data->birthday)) ? NULL : $mba_portal_personal_data->birthday,
    //                 'personal_data_gender' => ($mba_portal_personal_data->gender == 'MALE') ? 'M' : 'F',
    //                 'personal_data_nationality' => $mba_portal_personal_data->nationality,
    //                 'personal_data_marital_status' => strtolower($mba_portal_personal_data->marital_status),
    //                 'personal_data_mother_maiden_name' => ($mba_portal_mother_data) ? $mba_portal_mother_data[0]->mother_given_name : NULL,
    //                 'personal_data_password' => $mba_portal_personal_data->password,
    //                 'personal_data_reference_code' => (is_null($mbo_portal_student->sgsagentcode)) ? NULL : $mbo_portal_student->sgsagentcode,
    //                 'pmb_sync' => 0,
    //                 'portal_id' => $mba_portal_personal_data->id,
    //                 'date_added' => ($mba_portal_personal_data->date_created == '0000-00-00 00:00:00') ? date('Y-m-d H:i:s', time()) : $mba_portal_personal_data->date_created
    //             ];

    //             $a_student_data = array(
    //                 'student_id' => $s_student_id,
    //                 'personal_data_id' => $s_personal_data_id,
    //                 'student_email' => ($mbo_portal_student->iulimail == '') ? NULL : $mbo_portal_student->iulimail,
    //                 'study_program_id' => $mba_staging_study_program_data->study_program_id,
    //                 'program_id' => 1,
    //                 'date_added' => date('Y-m-d H:i:s', time()),
    //                 'academic_year_id' => $mba_portal_academic_year_data->year_name,
    //                 'finance_year_id' => $mba_portal_academic_year_data->year_name,
    //                 'student_number' => $mbo_portal_student->id_number,
    //                 'student_type' => ($mbo_portal_student->admission_type == 'new') ? 'regular' :'transfer',
    //                 'portal_id' => $mbo_portal_student->id,
    //                 'student_status' => $s_student_status
    //             );

    //             if ($this->Pdm->create_new_personal_data($a_personal_data)) {
    //                 if (!$this->Stm->create_new_student($a_student_data)) {
    //                     print('error insert new student');
    //                 }
    //             }else{
    //                 print('error insert personal_data');exit;
    //             }
    //         }
    //     }
    // }

    public function show_question_list_tracer()
    {
        $this->a_page_data['user_has_answered'] = false;
        $this->a_page_data['dikti_question'] = modules::run('alumni/get_dikti_question');
        // $this->a_page_data['body'] = modules::run('alumni/show_tracer_study_modal');
        $this->a_page_data['body'] = $this->load->view('alumni/tracer_study/form/form_question_alumae', $this->a_page_data, true);
        $this->load->view('layout_ext', $this->a_page_data);
        // print('<code>');
        // print($this->a_page_data['body']);
        // print('</code>');
        // exit;
        // $s_text = '[7] Lainnya, tuliskan: _';
        // $s_choices = ((strpos($s_text, '_')) AND (strpos($s_text, '_') != (strlen($s_text) - 1))) ? '<br>' : ' ';
        // print('<pre>');
        // var_dump($s_choices);
        // var_dump(strpos($s_text, '_'));
        // print('------------'.(strlen($s_text) - 1));
    }

    public function test_get()
    {
        // $mba_student_invoice_semester = $this->Im->student_has_invoice_list('7974f8ed-1297-4cfb-9588-feb466265239', [
        //     'di.invoice_id' => 'ed8350bf-fcf5-496e-91d3-3642889fafaf'
        // ]);
        $mba_student_invoice_semester = $this->Im->student_has_invoice_data('7974f8ed-1297-4cfb-9588-feb466265239', [
            'di.academic_year_id' => 2021,
            'di.semester_type_id' => 1
        ]);
        print('<pre>');var_dump($mba_student_invoice_semester);
    }

    public function invoice_semester_tuition_fee($s_academic_year_id, $s_semester_type_id)
    {
        $a_month = [
            '1' => ['July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            '2' => ['Jan', 'Feb', 'March', 'Apr', 'May', 'Jun']
        ];

        $a_current_month = $a_month[$s_semester_type_id];
        $mba_student_data = $this->Stm->get_student_filtered([
            'ds.academic_year_id <=' => $s_academic_year_id
        ], ['active', 'onleave']);

        $paid_style = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '05AF46')
            )
        );

        $header_style = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            )
        );
        

        if ($mba_student_data) {
            $a_enrollment_fee2021 = ['eacd17dd-465b-41b5-baaa-fa96eca86bdd', '9b5f70a8-662a-4d9c-9152-b867c517ef83'];
            $a_student_not_have_invoice = [];
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'TuitionFee_Academic_Year_'.$s_academic_year_id.'-'.$s_semester_type_id;
            $s_filename = $s_file_name.'.xlsx';
            $s_file_path = APPPATH."uploads/finance/tuition_fee/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services")
                ->setCategory("Tuition Fee Billed Semester 1-8");

            $i_row = $i_start = 1;
            $c_col_start_tuition_fee = $c_col_tuition_fee = 'M';
            $o_sheet->setCellValue('A'.$i_row, "Fac");
            $o_sheet->setCellValue('B'.$i_row, "SP");
            $o_sheet->setCellValue('C'.$i_row, "Student Name");
            $o_sheet->setCellValue('D'.$i_row, "Student ID");
            $o_sheet->setCellValue('E'.$i_row, "Status");
            $o_sheet->setCellValue('F'.$i_row, "Enrollment Fee");
            $o_sheet->setCellValue('G'.$i_row, "Development Fee");
            $o_sheet->setCellValue('H'.$i_row, "Orientation Fee");
            $o_sheet->setCellValue('I'.$i_row, "EPC Fee");
            $o_sheet->setCellValue('J'.$i_row, "Discount Tuition Fee");
            $o_sheet->setCellValue('K'.$i_row, "Flyer Tuition Fee");
            $o_sheet->setCellValue('L'.$i_row, "Payment Method");
            $o_sheet->setCellValue('M'.$i_row, "Tuition Fee");
            
            $i_row++;
            for ($i=0; $i < count($a_current_month); $i++) { 
                $o_sheet->setCellValue($c_col_tuition_fee.$i_row, $a_current_month[$i]);
                $c_col_tuition_fee++;
            }
            $c_col_last_tuition_fee = chr(ord($c_col_tuition_fee) - 1);
            $o_sheet->mergeCells('A'.$i_start.':A'.$i_row);
            $o_sheet->mergeCells('B'.$i_start.':B'.$i_row);
            $o_sheet->mergeCells('C'.$i_start.':C'.$i_row);
            $o_sheet->mergeCells('D'.$i_start.':D'.$i_row);
            $o_sheet->mergeCells('E'.$i_start.':E'.$i_row);
            $o_sheet->mergeCells('F'.$i_start.':F'.$i_row);
            $o_sheet->mergeCells('G'.$i_start.':G'.$i_row);
            $o_sheet->mergeCells('H'.$i_start.':H'.$i_row);
            $o_sheet->mergeCells('I'.$i_start.':I'.$i_row);
            $o_sheet->mergeCells('J'.$i_start.':J'.$i_row);
            $o_sheet->mergeCells('K'.$i_start.':K'.$i_row);
            $o_sheet->mergeCells('L'.$i_start.':L'.$i_row);
            $o_sheet->mergeCells('M'.$i_start.':'.$c_col_last_tuition_fee.$i_start);

            $o_sheet->getStyle('A'.$i_start.':'.$c_col_last_tuition_fee.$i_row)->applyFromArray($header_style);
            $i_row++;
            $i_start_row = $i_row;
            $o_sheet->freezePane('F'.$i_row);

            foreach ($mba_student_data as $o_student) {
                $mbo_invoice_data = $this->Im->student_has_invoice_data($o_student->personal_data_id, [
                    'di.academic_year_id' => $s_academic_year_id,
                    'di.semester_type_id' => $s_semester_type_id,
                    'df.payment_type_code' => '02'
                ]);
                // if ($o_student->personal_data_id == '8619e2a6-3e1a-4278-884b-02cdd2f8dff0') {
                //     print('<pre>');var_dump($mbo_invoice_data);exit;
                // }

                $o_sheet->setCellValue('A'.$i_row, $o_student->faculty_abbreviation);
                $o_sheet->setCellValue('B'.$i_row, $o_student->study_program_abbreviation);
                $o_sheet->setCellValue('C'.$i_row, $o_student->personal_data_name);
                $o_sheet->setCellValue('D'.$i_row, $o_student->student_number);
                $o_sheet->setCellValue('E'.$i_row, $o_student->student_status);
                
                if ($mbo_invoice_data) {
                    $o_invoice_created = $this->General->get_where('dt_invoice', ['invoice_id' => $mbo_invoice_data->invoice_id])[0];
                    if (!in_array($mbo_invoice_data->fee_id, $a_enrollment_fee2021)) {
                        $a_installment_fined = [];
                        $mba_invoice_details = $this->General->get_where('dt_invoice_details', [
                            'invoice_id' => $mbo_invoice_data->invoice_id
                        ]);

                        $mbo_invoice_full = $this->Im->get_invoice_full_payment($mbo_invoice_data->invoice_id);
                        $mba_invoice_installment = $this->Im->get_invoice_installment($mbo_invoice_data->invoice_id);
                        // $a_enrollment_fee = [];
                        $d_enrollment_fee = false;
                        $a_development_fee = [];
                        $a_orientation_fee = [];
                        $a_english_course_fee = [];
                        $a_discount = [];
                        $a_discount_note = [];
                        $d_tuition_fee = 0;
                        $d_unknow_fee = 0;
                        $s_payment_method = '';
                        $d_flyer_tuition_fee = $mbo_invoice_data->fee_amount;

                        if (($mbo_invoice_data->invoice_status == 'paid') AND ($mbo_invoice_full->sub_invoice_details_amount_paid > 0)) {
                            $s_payment_method = 'full';
                        }
                        else if ($mba_invoice_installment) {
                            foreach ($mba_invoice_installment as $o_installment) {
                                if ($o_installment->sub_invoice_details_status == 'paid') {
                                    $s_payment_method = 'installment';
                                    break;
                                }
                            }
                        }

                        if ($mba_invoice_installment) {
                            foreach ($mba_invoice_installment as $o_installment) {
                                array_push($a_installment_fined, $o_installment->sub_invoice_details_amount_fined);
                            }
                        }

                        $s_payment_method_text = ucfirst($s_payment_method);
                        if ($s_payment_method == 'installment') {
                            $s_payment_method_text .=' x'.count($mba_invoice_installment);
                        }
                        $o_sheet->setCellValue('K'.$i_row, $d_flyer_tuition_fee);
                        $o_sheet->setCellValue('L'.$i_row, $s_payment_method_text);

                        if ($mba_invoice_installment) {
                            $d_total_amount_paid = $mba_invoice_installment[0]->sub_invoice_amount_paid;
                        }
                        else if ($mbo_invoice_full) {
                            $d_total_amount_paid = $mbo_invoice_full->sub_invoice_amount_paid;
                        }
                        else {
                            $d_total_amount_paid = 0;
                        }
                        
                        if (!$mba_invoice_details) {
                            print($mbo_invoice_data->invoice_id.'<br>');exit;
                        }

                        if ($o_student->finance_year_id == $s_academic_year_id) {
                            $mbo_enrollment_invoice = $this->Im->student_has_invoice_data($o_student->personal_data_id, [
                                'df.payment_type_code' => '01'
                            ]);

                            if ($mbo_enrollment_invoice) {
                                $d_enrollment_fee = ($mbo_enrollment_invoice->invoice_status == 'paid') ? $mbo_enrollment_invoice->invoice_details_amount : 0;
                            }
                        }

                        foreach ($mba_invoice_details as $o_invoice_details) {
                            $mba_fee_data = $this->General->get_where('dt_fee', ['fee_id' => $o_invoice_details->fee_id]);

                            if (!$mba_fee_data) {
                                print($o_invoice_details->invoice_id);exit;
                            }
                            
                            if (in_array($mba_fee_data[0]->fee_id, ['776dbd58-95ff-40d2-bd62-b0b71c71640a', '23b4fca7-b8e2-11e9-849d-5254005d90f6'])) {
                                array_push($a_orientation_fee, $o_invoice_details->invoice_details_amount);
                            }
                            else if ($mba_fee_data[0]->fee_id == '877f534d-e503-4920-8b8e-25b4c01143fa') {
                                array_push($a_development_fee, $o_invoice_details->invoice_details_amount);
                            }
                            else if ($mba_fee_data[0]->fee_id == '8ac0fd8e-9592-4929-a2f3-eca7af8291cc') {
                                array_push($a_english_course_fee, $o_invoice_details->invoice_details_amount);
                            }
                            else if ($o_invoice_details->invoice_details_amount_sign_type == 'negative') {
                                $d_discount = ($o_invoice_details->invoice_details_amount_number_type == 'percentage') ? $o_invoice_details->invoice_details_amount.'%' : $o_invoice_details->invoice_details_amount;
                                array_push($a_discount, [
                                    'amount' => $o_invoice_details->invoice_details_amount,
                                    'type' => $o_invoice_details->invoice_details_amount_number_type
                                ]);
                                array_push($a_discount_note, '- '.$mba_fee_data[0]->fee_description.': '.$d_discount);
                            }
                            else if ($mba_fee_data[0]->payment_type_code == '02') {
                                $d_tuition_fee += $o_invoice_details->invoice_details_amount;
                            }
                            else {
                                $d_unknow_fee += $o_invoice_details->invoice_details_amount;
                            }
                        }
    
                        if ($d_enrollment_fee) {
                            $o_sheet->setCellValue('F'.$i_row, $d_enrollment_fee);
                            $o_sheet->getStyle('F'.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        }
                        else {
                            $o_sheet->setCellValue('F'.$i_row, '-');
                            $o_sheet->getStyle('F'.$i_row)->getAlignment()->setHorizontal('center');
                        }
                        
                        if (count($a_development_fee) > 0) {
                            $d_development_fee = array_sum($a_development_fee);
                            if ($d_development_fee >= $d_total_amount_paid) {
                                $d_development_paid = $d_total_amount_paid;
                            }
                            else {
                                $d_development_paid = $d_development_fee;
                            }
                            $d_total_amount_paid -= $d_development_paid;
                            $o_sheet->setCellValue('G'.$i_row, $d_development_paid);
                            $o_sheet->getStyle('G'.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        }
                        else {
                            $o_sheet->setCellValue('G'.$i_row, '-');
                            $o_sheet->getStyle('G'.$i_row)->getAlignment()->setHorizontal('center');
                        }
                        
                        if (count($a_orientation_fee) > 0) {
                            $d_orientation_fee = array_sum($a_orientation_fee);
                            if ($d_orientation_fee >= $d_total_amount_paid) {
                                $d_orientation_paid = $d_total_amount_paid;
                            }
                            else {
                                $d_orientation_paid = $d_orientation_fee;
                            }
                            $d_total_amount_paid -= $d_orientation_paid;
                            $o_sheet->setCellValue('H'.$i_row, $d_orientation_paid);
                            $o_sheet->getStyle('H'.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        }
                        else {
                            $o_sheet->setCellValue('H'.$i_row, '-');
                            $o_sheet->getStyle('H'.$i_row)->getAlignment()->setHorizontal('center');
                        }
                        
                        if (count($a_english_course_fee) > 0) {
                            $d_english_course_fee = array_sum($a_english_course_fee);
                            if ($d_english_course_fee >= $d_total_amount_paid) {
                                $d_english_course_paid = $d_total_amount_paid;
                            }
                            else {
                                $d_english_course_paid = $d_english_course_fee;
                            }
                            $d_total_amount_paid -= $d_english_course_paid;
                            $o_sheet->setCellValue('I'.$i_row, $d_english_course_paid);
                            $o_sheet->getStyle('I'.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        }
                        else {
                            $o_sheet->setCellValue('I'.$i_row, '-');
                            $o_sheet->getStyle('I'.$i_row)->getAlignment()->setHorizontal('center');
                        }

                        if (count($a_discount) > 0) {
                            $d_discount_total = 0;
                            foreach ($a_discount as $a_data) {
                                if ($a_data['type'] == 'percentage') {
                                    if ($d_tuition_fee == 0) {
                                        $d_amount_discount = 0;
                                    }
                                    else {
                                        $d_amount_discount = $d_tuition_fee * $a_data['amount'] / 100;
                                    }
                                }
                                else {
                                    $d_amount_discount = $a_data['amount'];
                                }
                                
                                $d_discount_total += $d_amount_discount;
                            }

                            $d_tuition_fee -= $d_discount_total;
                            $o_sheet->setCellValue('J'.$i_row, '-'.$d_discount_total);
                            $o_sheet->getStyle('J'.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        }
                        else {
                            $o_sheet->setCellValue('J'.$i_row, '-');
                            $o_sheet->getStyle('J'.$i_row)->getAlignment()->setHorizontal('center');
                        }

                        if (count($a_discount_note)) {
                            $s_notes = implode("\r\n", $a_discount_note);
                            $o_sheet->getComment('J'.$i_row)->setAuthor('Database');
                            $commentRichText = $o_sheet->getComment('J'.$i_row)->getText()->createTextRun('Notes:');
                            $commentRichText->getFont()->setBold(true);
                            $o_sheet->getComment('J'.$i_row)->getText()->createTextRun("\r\n");
                            $o_sheet->getComment('J'.$i_row)->getText()->createTextRun($s_notes);
                        }

                        if ($d_tuition_fee > 0) {
                            $d_additional_installment = 0;
                            if (($o_student->finance_year_id == '2021') AND ($s_payment_method == 'installment')) {
                                $s_invoice_created = date('Y-m-d', strtotime($o_invoice_created->date_added));
                                $s_additional_2021_start = date('Y-m-d', strtotime('2021-02-03'));
                                if ($s_invoice_created > $s_additional_2021_start) {
                                    $d_total_additional = $d_flyer_tuition_fee * 10/100;
                                    $d_additional_installment = $d_total_additional / count($a_current_month);
                                }
                            }

                            // if ($mbo_invoice_data->personal_data_id == '6462b04f-07f9-443b-aad9-16d25c8674bc') {
                            //     print($d_tuition_fee);exit;
                            // }

                            $d_installment = $d_tuition_fee / count($a_current_month);
                            $c_col_installment = $c_col_start_tuition_fee;

                            for ($i=0; $i < 6; $i++) { 
                                $d_installment_month = $d_installment;

                                if ($o_student->finance_year_id < 2021) {
                                    if ((count($a_installment_fined) > 0) AND (isset($a_installment_fined[$i]))) {
                                        if ($a_installment_fined[$i] > 0) {
                                            $d_installment_month += $a_installment_fined[$i];
                                        }
                                    }
                                }

                                if ($d_additional_installment > 0) {
                                    $d_installment_month += $d_additional_installment;
                                }
                                
                                if ($d_installment_month >= $d_total_amount_paid) {
                                    $d_installment_paid = $d_total_amount_paid;
                                }
                                else {
                                    $d_installment_paid = $d_installment_month;
                                }

                                $d_total_amount_paid -= $d_installment_paid;
                                if ($d_installment_paid > 100) { // dibayar lebih dari 100 perak baru di cetak
                                    $o_sheet->setCellValue($c_col_installment.$i_row, $d_installment_paid);
                                    $o_sheet->getStyle($c_col_installment.$i_row)->getNumberFormat()->setFormatCode('#,##');
                                }

                                if ($d_installment_month > $d_installment) {
                                    $d_diff_installment = $d_installment_month - $d_installment;
                                    $s_notes = "+".number_format($d_diff_installment, 0, '.', '.');
                                    $o_sheet->getComment($c_col_installment.$i_row)->setAuthor('Database');
                                    $commentRichText = $o_sheet->getComment($c_col_installment.$i_row)->getText()->createTextRun('Notes:');
                                    $commentRichText->getFont()->setBold(true);
                                    $o_sheet->getComment($c_col_installment.$i_row)->getText()->createTextRun("\r\n");
                                    $o_sheet->getComment($c_col_installment.$i_row)->getText()->createTextRun($s_notes);
                                }
                                $c_col_installment++;
                            }
                        }
                        else {
                            $c_col_installment = $c_col_start_tuition_fee;
                            for ($i=0; $i < 6; $i++) {
                                $o_sheet->setCellValue($c_col_installment.$i_row, 0);
                                $o_sheet->getStyle($c_col_installment.$i_row)->getNumberFormat()->setFormatCode('#,##');
                                $c_col_installment++;
                            }
                        }

                        if ($d_unknow_fee > 0) {
                            $o_sheet->setCellValue($c_col_installment.$i_row, $d_unknow_fee);
                            $o_sheet->getStyle($c_col_installment.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        }
                        $c_col_installment++;

                        // if ($d_total_amount_paid > 0) {
                        //     $o_sheet->setCellValue($c_col_installment.$i_row, $d_total_amount_paid);
                        //     $o_sheet->getStyle($c_col_installment.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        // }
                        
                        // if ($mbo_invoice_data->invoice_status == 'paid') {
                        //     $o_sheet->getStyle('A'.$i_row.':'.$c_col_last_tuition_fee.$i_row)->applyFromArray($paid_style);
                        // }
                    }
                    // else {
                    //     print($mbo_invoice_data->invoice_id);exit;
                    // }
                }
                $i_row++;
            }

            // if ($a_student_not_have_invoice) {
            //     print('<pre>');var_dump($a_student_not_have_invoice);
            // }

            $c_col = 'A';
            for ($i = 1; $i < 20; $i++) { 
                $o_sheet->getColumnDimension($c_col++)->setAutoSize(true);
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
        else {
            print('No data found!');
        }
    }

    public function invoice_semester_tuition_fee_general($s_academic_year_id, $s_semester_type_id)
    {
        $a_month = [
            '1' => ['July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            '2' => ['Jan', 'Feb', 'March', 'Apr', 'May', 'Jun']
        ];

        $a_current_month = $a_month[$s_semester_type_id];
        $mba_student_data = $this->Stm->get_student_filtered([
            'ds.academic_year_id <=' => $s_academic_year_id
        ], ['active', 'onleave']);

        $paid_style = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '05AF46')
            )
        );

        $header_style = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            )
        );
        

        if ($mba_student_data) {
            $a_enrollment_fee2021 = ['eacd17dd-465b-41b5-baaa-fa96eca86bdd', '9b5f70a8-662a-4d9c-9152-b867c517ef83'];
            $a_student_not_have_invoice = [];
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'TuitionFee_Academic_Year_'.$s_academic_year_id.'-'.$s_semester_type_id;
            $s_filename = $s_file_name.'.xlsx';
            $s_file_path = APPPATH."uploads/finance/tuition_fee/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services")
                ->setCategory("Tuition Fee Billed Semester 1-8");

            $i_row = $i_start = 1;
            $c_col_start_tuition_fee = $c_col_tuition_fee = 'N';
            $o_sheet->setCellValue('A'.$i_row, "Fac");
            $o_sheet->setCellValue('B'.$i_row, "SP");
            $o_sheet->setCellValue('C'.$i_row, "Student Name");
            $o_sheet->setCellValue('D'.$i_row, "Student ID");
            $o_sheet->setCellValue('E'.$i_row, "Status");
            $o_sheet->setCellValue('F'.$i_row, "Enrollment Fee");
            $o_sheet->setCellValue('G'.$i_row, "Development Fee");
            $o_sheet->setCellValue('H'.$i_row, "Orientation Fee");
            $o_sheet->setCellValue('I'.$i_row, "EPC Fee");
            $o_sheet->setCellValue('J'.$i_row, "Discount Tuition Fee");
            $o_sheet->setCellValue('K'.$i_row, "Flyer Tuition Fee");
            $o_sheet->setCellValue('L'.$i_row, "Payment Method");
            $o_sheet->setCellValue('M'.$i_row, "Paid Full");
            $o_sheet->setCellValue('N'.$i_row, "Paid Installment");
            
            $i_row++;
            for ($i=0; $i < count($a_current_month); $i++) { 
                $o_sheet->setCellValue($c_col_tuition_fee.$i_row, $a_current_month[$i]);
                $c_col_tuition_fee++;
            }
            $c_col_last_tuition_fee = chr(ord($c_col_tuition_fee) - 1);
            $o_sheet->mergeCells('A'.$i_start.':A'.$i_row);
            $o_sheet->mergeCells('B'.$i_start.':B'.$i_row);
            $o_sheet->mergeCells('C'.$i_start.':C'.$i_row);
            $o_sheet->mergeCells('D'.$i_start.':D'.$i_row);
            $o_sheet->mergeCells('E'.$i_start.':E'.$i_row);
            $o_sheet->mergeCells('F'.$i_start.':F'.$i_row);
            $o_sheet->mergeCells('G'.$i_start.':G'.$i_row);
            $o_sheet->mergeCells('H'.$i_start.':H'.$i_row);
            $o_sheet->mergeCells('I'.$i_start.':I'.$i_row);
            $o_sheet->mergeCells('J'.$i_start.':J'.$i_row);
            $o_sheet->mergeCells('K'.$i_start.':K'.$i_row);
            $o_sheet->mergeCells('L'.$i_start.':L'.$i_row);
            $o_sheet->mergeCells('M'.$i_start.':M'.$i_row);
            $o_sheet->mergeCells('N'.$i_start.':'.$c_col_last_tuition_fee.$i_start);

            $o_sheet->getStyle('A'.$i_start.':'.$c_col_last_tuition_fee.$i_row)->applyFromArray($header_style);
            $i_row++;
            $i_start_row = $i_row;
            $o_sheet->freezePane('F'.$i_row);

            foreach ($mba_student_data as $o_student) {
                $mbo_invoice_data = $this->Im->student_has_invoice_data($o_student->personal_data_id, [
                    'di.academic_year_id' => $s_academic_year_id,
                    'di.semester_type_id' => $s_semester_type_id,
                    'df.payment_type_code' => '02'
                ]);
                // if ($o_student->personal_data_id == '8619e2a6-3e1a-4278-884b-02cdd2f8dff0') {
                //     print('<pre>');var_dump($mbo_invoice_data);exit;
                // }

                $o_sheet->setCellValue('A'.$i_row, $o_student->faculty_abbreviation);
                $o_sheet->setCellValue('B'.$i_row, $o_student->study_program_abbreviation);
                $o_sheet->setCellValue('C'.$i_row, $o_student->personal_data_name);
                $o_sheet->setCellValue('D'.$i_row, $o_student->student_number);
                $o_sheet->setCellValue('E'.$i_row, $o_student->student_status);
                
                if ($mbo_invoice_data) {
                    $o_invoice_created = $this->General->get_where('dt_invoice', ['invoice_id' => $mbo_invoice_data->invoice_id])[0];
                    if (!in_array($mbo_invoice_data->fee_id, $a_enrollment_fee2021)) {
                        $a_installment_fined = [];
                        $mba_invoice_details = $this->General->get_where('dt_invoice_details', [
                            'invoice_id' => $mbo_invoice_data->invoice_id
                        ]);

                        $mbo_invoice_full = $this->Im->get_invoice_full_payment($mbo_invoice_data->invoice_id);
                        $mba_invoice_installment = $this->Im->get_invoice_installment($mbo_invoice_data->invoice_id);
                        // $a_enrollment_fee = [];
                        $d_enrollment_fee = false;
                        $a_development_fee = [];
                        $a_orientation_fee = [];
                        $a_english_course_fee = [];
                        $a_discount = [];
                        $a_discount_note = [];
                        $d_tuition_fee = 0;
                        $d_unknow_fee = 0;
                        $s_payment_method = '';
                        $d_payment_full_paid = 0;
                        $d_flyer_tuition_fee = $mbo_invoice_data->fee_amount;
                        $mbs_sholarship = false;
                        if (!is_null($mbo_invoice_data->scholarship_id)) {
                            $mba_scholarship = $this->General->get_where('ref_scholarship', [
                                'scholarship_id' => $mbo_invoice_data->scholarship_id
                            ]);
                            $mbs_sholarship = ($mba_scholarship) ? $mba_scholarship[0]->scholarship_description : false;
                        }

                        if (($mbo_invoice_data->invoice_status == 'paid') AND ($mbo_invoice_full->sub_invoice_details_amount_paid > 0)) {
                            $s_payment_method = 'full';
                            $d_payment_full_paid = $mbo_invoice_data->invoice_amount_paid;
                        }
                        else if ($mba_invoice_installment) {
                            foreach ($mba_invoice_installment as $o_installment) {
                                if ($o_installment->sub_invoice_details_status == 'paid') {
                                    $s_payment_method = 'installment';
                                    break;
                                }
                            }
                        }

                        if ($mba_invoice_installment) {
                            foreach ($mba_invoice_installment as $o_installment) {
                                array_push($a_installment_fined, $o_installment->sub_invoice_details_amount_fined);
                            }
                        }

                        $s_payment_method_text = ucfirst($s_payment_method);
                        if ($s_payment_method == 'installment') {
                            $s_payment_method_text .=' x'.count($mba_invoice_installment);
                        }
                        $o_sheet->setCellValue('K'.$i_row, $d_flyer_tuition_fee);
                        $o_sheet->setCellValue('L'.$i_row, $s_payment_method_text);
                        $o_sheet->setCellValue('M'.$i_row, $d_payment_full_paid);

                        $o_sheet->getStyle('K'.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        $o_sheet->getStyle('M'.$i_row)->getNumberFormat()->setFormatCode('#,##');

                        if ($mbs_sholarship) {
                            $o_sheet->getComment('K'.$i_row)->setAuthor('Database');
                            $commentRichText = $o_sheet->getComment('K'.$i_row)->getText()->createTextRun('Notes:');
                            $commentRichText->getFont()->setBold(true);
                            $o_sheet->getComment('K'.$i_row)->getText()->createTextRun("\r\n");
                            $o_sheet->getComment('K'.$i_row)->getText()->createTextRun($mbs_sholarship);
                        }

                        if ($mba_invoice_installment) {
                            $d_total_amount_paid = $mba_invoice_installment[0]->sub_invoice_amount_paid;
                        }
                        else if ($mbo_invoice_full) {
                            $d_total_amount_paid = $mbo_invoice_full->sub_invoice_amount_paid;
                        }
                        else {
                            $d_total_amount_paid = 0;
                        }
                        
                        if (!$mba_invoice_details) {
                            print($mbo_invoice_data->invoice_id.'<br>');exit;
                        }

                        if ($o_student->finance_year_id == $s_academic_year_id) {
                            $mbo_enrollment_invoice = $this->Im->student_has_invoice_data($o_student->personal_data_id, [
                                'df.payment_type_code' => '01'
                            ]);

                            if ($mbo_enrollment_invoice) {
                                $d_enrollment_fee = ($mbo_enrollment_invoice->invoice_status == 'paid') ? $mbo_enrollment_invoice->invoice_details_amount : 0;
                            }
                        }

                        foreach ($mba_invoice_details as $o_invoice_details) {
                            $mba_fee_data = $this->General->get_where('dt_fee', ['fee_id' => $o_invoice_details->fee_id]);

                            if (!$mba_fee_data) {
                                print($o_invoice_details->invoice_id);exit;
                            }
                            
                            if (in_array($mba_fee_data[0]->fee_id, ['776dbd58-95ff-40d2-bd62-b0b71c71640a', '23b4fca7-b8e2-11e9-849d-5254005d90f6'])) {
                                array_push($a_orientation_fee, $o_invoice_details->invoice_details_amount);
                            }
                            else if ($mba_fee_data[0]->fee_id == '877f534d-e503-4920-8b8e-25b4c01143fa') {
                                array_push($a_development_fee, $o_invoice_details->invoice_details_amount);
                            }
                            else if ($mba_fee_data[0]->fee_id == '8ac0fd8e-9592-4929-a2f3-eca7af8291cc') {
                                array_push($a_english_course_fee, $o_invoice_details->invoice_details_amount);
                            }
                            else if ($o_invoice_details->invoice_details_amount_sign_type == 'negative') {
                                $d_discount = ($o_invoice_details->invoice_details_amount_number_type == 'percentage') ? $o_invoice_details->invoice_details_amount.'%' : $o_invoice_details->invoice_details_amount;
                                array_push($a_discount, [
                                    'amount' => $o_invoice_details->invoice_details_amount,
                                    'type' => $o_invoice_details->invoice_details_amount_number_type
                                ]);
                                array_push($a_discount_note, '- '.$mba_fee_data[0]->fee_description.': '.$d_discount);
                            }
                            else if ($mba_fee_data[0]->payment_type_code == '02') {
                                $d_tuition_fee += $o_invoice_details->invoice_details_amount;
                            }
                            else {
                                $d_unknow_fee += $o_invoice_details->invoice_details_amount;
                            }
                        }
    
                        if ($d_enrollment_fee) {
                            $o_sheet->setCellValue('F'.$i_row, $d_enrollment_fee);
                            $o_sheet->getStyle('F'.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        }
                        else {
                            $o_sheet->setCellValue('F'.$i_row, '-');
                            $o_sheet->getStyle('F'.$i_row)->getAlignment()->setHorizontal('center');
                        }
                        
                        if (count($a_development_fee) > 0) {
                            $d_development_fee = array_sum($a_development_fee);
                            if ($d_development_fee >= $d_total_amount_paid) {
                                $d_development_paid = $d_total_amount_paid;
                            }
                            else {
                                $d_development_paid = $d_development_fee;
                            }
                            $d_total_amount_paid -= $d_development_paid;
                            $o_sheet->setCellValue('G'.$i_row, $d_development_paid);
                            $o_sheet->getStyle('G'.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        }
                        else {
                            $o_sheet->setCellValue('G'.$i_row, '-');
                            $o_sheet->getStyle('G'.$i_row)->getAlignment()->setHorizontal('center');
                        }
                        
                        if (count($a_orientation_fee) > 0) {
                            $d_orientation_fee = array_sum($a_orientation_fee);
                            if ($d_orientation_fee >= $d_total_amount_paid) {
                                $d_orientation_paid = $d_total_amount_paid;
                            }
                            else {
                                $d_orientation_paid = $d_orientation_fee;
                            }
                            $d_total_amount_paid -= $d_orientation_paid;
                            $o_sheet->setCellValue('H'.$i_row, $d_orientation_paid);
                            $o_sheet->getStyle('H'.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        }
                        else {
                            $o_sheet->setCellValue('H'.$i_row, '-');
                            $o_sheet->getStyle('H'.$i_row)->getAlignment()->setHorizontal('center');
                        }
                        
                        if (count($a_english_course_fee) > 0) {
                            $d_english_course_fee = array_sum($a_english_course_fee);
                            if ($d_english_course_fee >= $d_total_amount_paid) {
                                $d_english_course_paid = $d_total_amount_paid;
                            }
                            else {
                                $d_english_course_paid = $d_english_course_fee;
                            }
                            $d_total_amount_paid -= $d_english_course_paid;
                            $o_sheet->setCellValue('I'.$i_row, $d_english_course_paid);
                            $o_sheet->getStyle('I'.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        }
                        else {
                            $o_sheet->setCellValue('I'.$i_row, '-');
                            $o_sheet->getStyle('I'.$i_row)->getAlignment()->setHorizontal('center');
                        }

                        if (count($a_discount) > 0) {
                            $d_discount_total = 0;
                            foreach ($a_discount as $a_data) {
                                if ($a_data['type'] == 'percentage') {
                                    if ($d_tuition_fee == 0) {
                                        $d_amount_discount = 0;
                                    }
                                    else {
                                        $d_amount_discount = $d_tuition_fee * $a_data['amount'] / 100;
                                    }
                                }
                                else {
                                    $d_amount_discount = $a_data['amount'];
                                }
                                
                                $d_discount_total += $d_amount_discount;
                            }

                            $d_tuition_fee -= $d_discount_total;
                            $o_sheet->setCellValue('J'.$i_row, '-'.$d_discount_total);
                            $o_sheet->getStyle('J'.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        }
                        else {
                            $o_sheet->setCellValue('J'.$i_row, '-');
                            $o_sheet->getStyle('J'.$i_row)->getAlignment()->setHorizontal('center');
                        }

                        if (count($a_discount_note)) {
                            $s_notes = implode("\r\n", $a_discount_note);
                            $o_sheet->getComment('J'.$i_row)->setAuthor('Database');
                            $commentRichText = $o_sheet->getComment('J'.$i_row)->getText()->createTextRun('Notes:');
                            $commentRichText->getFont()->setBold(true);
                            $o_sheet->getComment('J'.$i_row)->getText()->createTextRun("\r\n");
                            $o_sheet->getComment('J'.$i_row)->getText()->createTextRun($s_notes);
                        }

                        $c_col_installment = $c_col_start_tuition_fee;
                        if ($s_payment_method != 'full') {
                            if ($d_tuition_fee > 0) {
                                $d_additional_installment = 0;
                                if (($o_student->finance_year_id == '2021') AND ($s_payment_method == 'installment')) {
                                    $s_invoice_created = date('Y-m-d', strtotime($o_invoice_created->date_added));
                                    $s_additional_2021_start = date('Y-m-d', strtotime('2021-02-03'));
                                    if ($s_invoice_created > $s_additional_2021_start) {
                                        $d_total_additional = $d_flyer_tuition_fee * 10/100;
                                        $d_additional_installment = $d_total_additional / count($a_current_month);
                                    }
                                }
    
                                // if ($mbo_invoice_data->personal_data_id == '6462b04f-07f9-443b-aad9-16d25c8674bc') {
                                //     print($d_tuition_fee);exit;
                                // }
    
                                $d_installment = $d_tuition_fee / count($a_current_month);
                                $c_col_installment = $c_col_start_tuition_fee;
    
                                for ($i=0; $i < 6; $i++) { 
                                    $d_installment_month = $d_installment;
    
                                    if ($o_student->finance_year_id < 2021) {
                                        if ((count($a_installment_fined) > 0) AND (isset($a_installment_fined[$i]))) {
                                            if ($a_installment_fined[$i] > 0) {
                                                $d_installment_month += $a_installment_fined[$i];
                                            }
                                        }
                                    }
    
                                    if ($d_additional_installment > 0) {
                                        $d_installment_month += $d_additional_installment;
                                    }
                                    
                                    if ($d_installment_month >= $d_total_amount_paid) {
                                        $d_installment_paid = $d_total_amount_paid;
                                    }
                                    else {
                                        $d_installment_paid = $d_installment_month;
                                    }
    
                                    $d_total_amount_paid -= $d_installment_paid;
                                    if ($d_installment_paid > 100) { // dibayar lebih dari 100 perak baru di cetak
                                        $o_sheet->setCellValue($c_col_installment.$i_row, $d_installment_paid);
                                        $o_sheet->getStyle($c_col_installment.$i_row)->getNumberFormat()->setFormatCode('#,##');
                                    }
    
                                    if ($d_installment_month > $d_installment) {
                                        $d_diff_installment = $d_installment_month - $d_installment;
                                        $s_notes = "+".number_format($d_diff_installment, 0, '.', '.');
                                        $o_sheet->getComment($c_col_installment.$i_row)->setAuthor('Database');
                                        $commentRichText = $o_sheet->getComment($c_col_installment.$i_row)->getText()->createTextRun('Notes:');
                                        $commentRichText->getFont()->setBold(true);
                                        $o_sheet->getComment($c_col_installment.$i_row)->getText()->createTextRun("\r\n");
                                        $o_sheet->getComment($c_col_installment.$i_row)->getText()->createTextRun($s_notes);
                                    }
                                    $c_col_installment++;
                                }
                            }
                            else {
                                $c_col_installment = $c_col_start_tuition_fee;
                                for ($i=0; $i < 6; $i++) {
                                    $o_sheet->setCellValue($c_col_installment.$i_row, 0);
                                    $o_sheet->getStyle($c_col_installment.$i_row)->getNumberFormat()->setFormatCode('#,##');
                                    $c_col_installment++;
                                }
                            }
                        }

                        if ($d_unknow_fee > 0) {
                            $o_sheet->setCellValue($c_col_installment.$i_row, $d_unknow_fee);
                            $o_sheet->getStyle($c_col_installment.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        }
                        $c_col_installment++;

                        // if ($d_total_amount_paid > 0) {
                        //     $o_sheet->setCellValue($c_col_installment.$i_row, $d_total_amount_paid);
                        //     $o_sheet->getStyle($c_col_installment.$i_row)->getNumberFormat()->setFormatCode('#,##');
                        // }
                        
                        // if ($mbo_invoice_data->invoice_status == 'paid') {
                        //     $o_sheet->getStyle('A'.$i_row.':'.$c_col_last_tuition_fee.$i_row)->applyFromArray($paid_style);
                        // }
                    }
                    // else {
                    //     print($mbo_invoice_data->invoice_id);exit;
                    // }
                }
                $i_row++;
            }

            // if ($a_student_not_have_invoice) {
            //     print('<pre>');var_dump($a_student_not_have_invoice);
            // }

            $c_col = 'A';
            for ($i = 1; $i < 20; $i++) { 
                $o_sheet->getColumnDimension($c_col++)->setAutoSize(true);
            }
            
            $o_sheet->removeColumn('I');
            $o_sheet->removeColumn('H');
            $o_sheet->removeColumn('G');
            $o_sheet->removeColumn('F');
            
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
        else {
            print('No data found!');
        }
    }

    public function income_report()
    {
        $s_academic_year_id_not_include = 2022;
        $s_semester_type_id_not_include = 1;

        $s_start_range = '2021-07-01 00:00:00';
        $s_end_range = date('Y-m-d H:i:s');

        $mba_sub_invoice_details_data = $this->General->get_where('dt_sub_invoice_details', [
            'sub_invoice_details_amount_paid > ' => 0, 
            'sub_invoice_details_status' => 'paid',
            'sub_invoice_details_datetime_paid_off >=' => $s_start_range,
            // 'sub_invoice_details_datetime_paid_off <=' => $s_end_range,
        ]);

        $a_null_ntb = [];
        $a_data = [];
        $a_sheet_names = [];
        if ($mba_sub_invoice_details_data) {
            foreach ($mba_sub_invoice_details_data as $o_sub_invoice_details) {
                if (is_null($o_sub_invoice_details->trx_id)) {
                    print($o_sub_invoice_details->sub_invoice_details_id);exit;
                }

                $mbo_invoice_details_data = $this->Im->get_invoice_detail_by_trx_id($o_sub_invoice_details->trx_id);
                $mbo_sub_invoice = $this->General->get_where('dt_sub_invoice', ['sub_invoice_id' => $o_sub_invoice_details->sub_invoice_id]);
                if ($mbo_sub_invoice) {
                    $mbo_invoice = $this->General->get_where('dt_invoice', ['invoice_id' => $mbo_sub_invoice[0]->invoice_id]);
                    if ($mbo_invoice AND $mbo_invoice_details_data) {
                        $o_invoice = $mbo_invoice[0];
                        if (($mbo_invoice_details_data->payment_type_name != '02') AND ($o_invoice->academic_year_id != $s_academic_year_id_not_include) AND ($o_invoice->semester_type_id != $s_semester_type_id_not_include)) {
                            $mbo_student_data = $this->Stm->get_student_filtered([
                                'ds.personal_data_id' => $o_invoice->personal_data_id
                            ]);

                            if ($mbo_student_data) {
                                $mbo_bni_trans_data = $this->General->get_where('bni_billing', ['trx_id' => $o_sub_invoice_details->trx_id]);
                                if (is_null($mbo_bni_trans_data[0]->payment_ntb)) {
                                    array_push($a_null_ntb, $o_sub_invoice_details->trx_id);
                                }
                                $s_sheet_names = date('M Y', strtotime($o_sub_invoice_details->sub_invoice_details_datetime_paid_off));
                                if (!in_array($s_sheet_names, $a_sheet_names)) {
                                    array_push($a_sheet_names, $s_sheet_names);
                                }

                                array_push($a_data, [
                                    'personal_data_name' => $mbo_student_data[0]->personal_data_name,
                                    'student_number' => $mbo_student_data[0]->student_number,
                                    'academic_year_id' => $mbo_student_data[0]->academic_year_id,
                                    'finance_year_id' => $mbo_student_data[0]->finance_year_id,
                                    'faculty_abbreviation' => $mbo_student_data[0]->faculty_abbreviation,
                                    'study_program_abbreviation' => $mbo_student_data[0]->study_program_abbreviation,
                                    'payment_type_name' => $mbo_invoice_details_data->payment_type_name,
                                    'sub_invoice_details_va_number' => $o_sub_invoice_details->sub_invoice_details_va_number,
                                    'sub_invoice_details_amount_paid' => $o_sub_invoice_details->sub_invoice_details_amount_paid,
                                    'sub_invoice_details_datetime_paid_off' => $o_sub_invoice_details->sub_invoice_details_datetime_paid_off,
                                    'payment_ntb' => ($mbo_bni_trans_data) ? $mbo_bni_trans_data[0]->payment_ntb : '',
                                    'trx_id' => $o_sub_invoice_details->trx_id,
                                    'sub_invoice_details_description' => $o_sub_invoice_details->sub_invoice_details_description,
                                    'sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id
                                ]);
                            }
                        }
                    }
                    // else {
                    //     print('invoice not found1!');
                    //     print('<pre>');var_dump($mbo_sub_invoice);exit;
                    // }
                }
                else {
                    print('sub invoice not found!');
                    print('<pre>');var_dump($mbo_sub_invoice_details);exit;
                }
                // if (($mbo_invoice_details_data) AND ($mbo_invoice_details_data->academic_year_id)) {
                    # code...
                // }
            }
        }

        // print('<pre>');var_dump($a_data);exit;
        if (count($a_data) > 0) {
            $a_data = array_values($a_data);
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'Income_report_from_'.str_replace(' ', '_', date('d F Y', strtotime($s_start_range)));
            $s_filename = $s_file_name.'.xlsx';
            $s_file_path = APPPATH."uploads/finance/custom_report/".date('F').'_'.date('Y')."/";

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services")
                ->setCategory("Invoice Batch Report");

            $i_row = 1;
            $a_irow = [];

            $o_sheet->setCellValue("A$i_row", 'Student Name');
            $o_sheet->setCellValue("B$i_row", 'Student ID');
            $o_sheet->setCellValue("C$i_row", 'Batch');
            $o_sheet->setCellValue("D$i_row", 'Entry Year');
            $o_sheet->setCellValue("E$i_row", 'Fac');
            $o_sheet->setCellValue("F$i_row", 'Prodi');
            $o_sheet->setCellValue("G$i_row", 'Payment Type');
            $o_sheet->setCellValue("H$i_row", 'VA Number');
            $o_sheet->setCellValue("I$i_row", 'Payment Date');
            $o_sheet->setCellValue("J$i_row", 'Payment Amount');
            $o_sheet->setCellValue("K$i_row", 'Journal Number');
            $o_sheet->setCellValue("L$i_row", 'Billing ID');
            $o_sheet->setCellValue("M$i_row", 'Description');
            $i_row++;

            if (count($a_sheet_names) > 0) {
                foreach ($a_sheet_names as $key => $s_sheet) {
                    $o_spreadsheet->createSheet();
                    $o_sheet_child[$key] = $o_spreadsheet->getSheet($key + 1)->setTitle($s_sheet);
                    $a_irow[$key] = 1;

                    $o_sheet_child[$key]->setCellValue("A".$a_irow[$key], 'Student Name');
                    $o_sheet_child[$key]->setCellValue("B".$a_irow[$key], 'Student ID');
                    $o_sheet_child[$key]->setCellValue("C".$a_irow[$key], 'Batch');
                    $o_sheet_child[$key]->setCellValue("D".$a_irow[$key], 'Entry Year');
                    $o_sheet_child[$key]->setCellValue("E".$a_irow[$key], 'Fac');
                    $o_sheet_child[$key]->setCellValue("F".$a_irow[$key], 'Prodi');
                    $o_sheet_child[$key]->setCellValue("G".$a_irow[$key], 'Payment Type');
                    $o_sheet_child[$key]->setCellValue("H".$a_irow[$key], 'VA Number');
                    $o_sheet_child[$key]->setCellValue("I".$a_irow[$key], 'Payment Date');
                    $o_sheet_child[$key]->setCellValue("J".$a_irow[$key], 'Payment Amount');
                    $o_sheet_child[$key]->setCellValue("K".$a_irow[$key], 'Journal Number');
                    $o_sheet_child[$key]->setCellValue("L".$a_irow[$key], 'Billing ID');
                    $o_sheet_child[$key]->setCellValue("M".$a_irow[$key], 'Description');
                    $a_irow[$key]++;
                }
            }

            foreach ($a_data as $data) {
                $o_sheet->setCellValue("A$i_row", $data['personal_data_name']);
                $o_sheet->setCellValue("B$i_row", $data['student_number']);
                $o_sheet->setCellValue("C$i_row", $data['academic_year_id']);
                $o_sheet->setCellValue("D$i_row", $data['finance_year_id']);
                $o_sheet->setCellValue("E$i_row", $data['faculty_abbreviation']);
                $o_sheet->setCellValue("F$i_row", $data['study_program_abbreviation']);
                $o_sheet->setCellValue("G$i_row", $data['payment_type_name']);
                $o_sheet->setCellValue("H$i_row", $data['sub_invoice_details_va_number']);
                $o_sheet->setCellValue("I$i_row", $data['sub_invoice_details_datetime_paid_off']);
                $o_sheet->setCellValue("J$i_row", $data['sub_invoice_details_amount_paid']);
                $o_sheet->setCellValue("K$i_row", '="'.$data['payment_ntb'].'"');
                $o_sheet->setCellValue("L$i_row", '="'.$data['trx_id'].'"');
                $o_sheet->setCellValue("M$i_row", $data['sub_invoice_details_description']);

                if (count($a_sheet_names) > 0) {
                    foreach ($a_sheet_names as $key => $s_sheet) {
                        $s_date_payment = date('M Y', strtotime($data['sub_invoice_details_datetime_paid_off']));
                        if ($s_date_payment == $s_sheet) {
                            $o_sheet_child[$key]->setCellValue("A".$a_irow[$key], $data['personal_data_name']);
                            $o_sheet_child[$key]->setCellValue("B".$a_irow[$key], $data['student_number']);
                            $o_sheet_child[$key]->setCellValue("C".$a_irow[$key], $data['academic_year_id']);
                            $o_sheet_child[$key]->setCellValue("D".$a_irow[$key], $data['finance_year_id']);
                            $o_sheet_child[$key]->setCellValue("E".$a_irow[$key], $data['faculty_abbreviation']);
                            $o_sheet_child[$key]->setCellValue("F".$a_irow[$key], $data['study_program_abbreviation']);
                            $o_sheet_child[$key]->setCellValue("G".$a_irow[$key], $data['payment_type_name']);
                            $o_sheet_child[$key]->setCellValue("G".$a_irow[$key], $data['sub_invoice_details_va_number']);
                            $o_sheet_child[$key]->setCellValue("I".$a_irow[$key], $data['sub_invoice_details_datetime_paid_off']);
                            $o_sheet_child[$key]->setCellValue("J".$a_irow[$key], $data['sub_invoice_details_amount_paid']);
                            $o_sheet_child[$key]->setCellValue("K".$a_irow[$key], '="'.$data['payment_ntb'].'"');
                            $o_sheet_child[$key]->setCellValue("L".$a_irow[$key], '="'.$data['trx_id'].'"');
                            $o_sheet_child[$key]->setCellValue("M".$a_irow[$key], $data['sub_invoice_details_description']);

                            $a_irow[$key]++;
                        }
                    }
                }

                $i_row++;
            }

            $c_char = 'A';
            for ($i=1; $i < 14; $i++) { 
                $o_sheet->getColumnDimension($c_char++)->setAutoSize(true);
            }

            if (count($a_sheet_names) > 0) {
                foreach ($a_sheet_names as $key => $s_sheet) {
                    $c_char = 'A';
                    for ($i=1; $i < 14; $i++) { 
                        $o_sheet_child[$key]->getColumnDimension($c_char++)->setAutoSize(true);
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
        else {
            print('data not found!');
        }
        // print('<pre>');var_dump($mba_sub_invoice_details_data);exit;
    }

    public function retrieve_old_school()
    {
        print('close');exit;
        $this->load->model('address/Address_model', 'Adm');
        $this->load->model('institution/Institution_model', 'Ins');
        $mba_student = $this->General->get_where('dt_student');
        
        $a_personal_data_id_execute = [];
        $a_personal_for_retrieve = [];
        foreach ($mba_student as $o_student) {
            $mba_student_data = $this->Ins->get_student_institution([
                'st.student_id' => $o_student->student_id,
                'ah.academic_history_this_job' => 'no',
                'ri.institution_type' => 'highschool'
            ]);

            // if ($o_student->personal_data_id == 'ff6e2ccf-c920-4463-8e20-d703a445e98a') {
            //     print('<pre>');var_dump($mba_student_data);exit;
            // }

            if (!$mba_student_data) {
                // print($mba_student_data);exit;
                $mba_personal_data = $this->General->get_where('dt_personal_data', [
                    'personal_data_id' => $o_student->personal_data_id
                ]);

                if ($mba_personal_data) {
                    $o_personal_data = $mba_personal_data[0];
                    if (!in_array($o_personal_data->personal_data_id, $a_personal_data_id_execute)) {
                        if (($x = strpos($o_personal_data->personal_data_name, "'")) === false) {
                            $a_personal_data_for_retrieve = [
                                'personal_data_id' => $o_personal_data->personal_data_id,
                                'personal_data_name' => $o_personal_data->personal_data_name,
                                'personal_data_email' => $o_personal_data->personal_data_email
                            ];
    
                            array_push($a_personal_data_id_execute, $o_personal_data->personal_data_id);
                            array_push($a_personal_for_retrieve, $a_personal_data_for_retrieve);
                        }
                    }
                }
            }
        }

        if (count($a_personal_for_retrieve) > 0) {
            $a_post_data = array(
                'list_data' => $a_personal_for_retrieve
            );
    
            $hashed_string = $this->libapi->hash_data($a_post_data, $this->s_apiuid, $this->s_apitoken);
    
            $post_data = json_encode(array(
                'access_token' => $this->s_apiuid,
                'data' => $hashed_string
            ));

            $url = 'https://pmb.iuli.ac.id/api/portal/retrieve_data';
		    $o_result = $this->libapi->post_data($url, $post_data);

            if ((!is_null($o_result)) AND ($o_result->code == 0)) {
                foreach ($o_result->return_data as $s_personal_data_id => $o_data) {
                    print($s_personal_data_id);
                    // exit;
                    $o_institution_data = $o_data->institution_data;
                    $o_academic_history = $o_data->academic_history_data;
                    $o_institution_address = $o_data->institution_address;

                    $mba_academic_history_student = $this->General->get_where('dt_academic_history', [
                        'personal_data_id' => $s_personal_data_id,
                        'academic_history_id' => $o_academic_history->academic_history_id
                    ]);

                    $mba_institution_student = $this->General->get_where('ref_institution', [
                        'institution_name' => $o_institution_data->institution_name
                    ]);

                    $a_institution_data = [
                        'institution_id' => $o_institution_data->institution_id,
                        'institution_name' => $o_institution_data->institution_name,
                        'institution_email' => $o_institution_data->institution_email,
                        'institution_phone_number' => $o_institution_data->institution_phone_number,
                        'institution_type' => $o_institution_data->institution_type,
                        'institution_is_international' => $o_institution_data->institution_is_international,
                        'date_added' => $o_institution_data->date_added,
                    ];

                    if ($mba_institution_student) {
                        $s_institution_id = $mba_institution_student[0]->institution_id;
                        if ((is_null($mba_institution_student[0]->address_id)) AND ($o_institution_address)) {
                            $a_institution_data['address_id'] = $o_institution_address->address_id;

                            $a_address_data = (array) $o_institution_address;
                            $this->Adm->save_address($a_address_data);
                            print('insert address<br>');
                        }
                    }
                    else {
                        $s_institution_id = $o_institution_data->institution_id;
                        if ($o_institution_address) {
                            $a_institution_data['address_id'] = $o_institution_address->address_id;

                            $a_address_data = (array) $o_institution_address;
                            $this->Adm->save_address($a_address_data);
                            print('insert address<br>');
                        }

                        $this->Ins->insert_institution($a_institution_data);
                        print('insert institution history<br>');
                    }

                    $a_academic_history_data = [
                        'academic_history_id' => $o_academic_history->academic_history_id,
                        'institution_id' => $o_academic_history->institution_id,
                        'personal_data_id' => $s_personal_data_id,
                        'academic_history_graduation_year' => $o_academic_history->academic_history_graduation_year,
                        'academic_history_major' => $o_academic_history->academic_history_major,
                        'academic_year_start_date' => $o_academic_history->academic_year_start_date,
                        'academic_year_end_date' => $o_academic_history->academic_year_end_date,
                        'date_added' => $o_academic_history->date_added,
                        'academic_history_main' => 'yes',
                        'academic_history_this_job' => 'no',
                        'status' => 'active',
                    ];

                    if (!$mba_academic_history_student) {
                        $this->Ins->insert_academic_history($a_academic_history_data);
                        print('insert academic history<br>');
                    }

                    exit;
                }
            }

            // print('<pre>');var_dump($post_data);exit;
            print('<pre>');var_dump($o_result);exit;
        }
    }

    public function cheat_invoice_onleave()
    {
        $mba_invoice_unpaid = $this->Im->get_invoice_list_detail([
            'di.invoice_allow_reminder' => 'yes',
            'fee.payment_type_code' => '02'
        ], ['created', 'pending']);
        // print('<pre>');
        // var_dump($mba_invoice_unpaid);exit;

        $a_invoice_has_onleave = [];

        if ($mba_invoice_unpaid) {
            foreach ($mba_invoice_unpaid as $o_invoice) {
                $b_has_onleave = false;
                $mba_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $o_invoice->personal_data_id]);
                if ($mba_student_data) {
                    foreach ($mba_student_data as $o_student) {
                        $mba_student_semester = $this->General->get_where('dt_student_semester', [
                            'student_id' => $o_student->student_id,
                            'student_semester_status' => 'onleave'
                        ]);

                        if ($mba_student_semester) {
                            $b_has_onleave = true;
                            break;
                        }
                    }
                }

                if ($b_has_onleave) {
                    // if (!in_array($o_invoice->invoice_id, $a_invoice_has_onleave)) {
                        array_push($a_invoice_has_onleave, $o_invoice);
                    // }
                }
            }
        }

        // print('<pre>');
        print json_encode($a_invoice_has_onleave);exit;
    }

    public function log_data()
    {
        $s_student_id = '02100c1c-55e9-44e5-af27-952e07ab7ef7';
        $dblog = $this->load->database('dblog', true);
        $table_list = $dblog->list_tables();
        foreach ($table_list as $s_table) {
            $dblog->where(['access_log_method' => 'save_settings']);
            $dblog->like('access_log_post_data', '"resign"');
            $q = $dblog->get($s_table);
            // print($dblog->last_query());exit;
            if ($q->num_rows() > 0) {
                $log_data = $q->first_row();
                $post_data = json_decode($log_data->access_log_post_data);
                $post_data = (array) $post_data;
                if (array_key_exists('student_id', $post_data)) {
                    $mbo_student_data = $this->General->get_where('dt_student', [
                        'student_id' => $post_data['student_id'],
                        'student_date_resign' => '0000-00-00 00:00:00'
                    ]);
                    if ($mbo_student_data) {
                        var_dump($log_data->access_log_timestamp);
                        print('<pre>');
                        var_dump($mbo_student_data);exit;
                    }
                }
            }
        }
    }

    public function check_full_payment()
    {
        $mba_invoice_2021_1 = $this->General->get_where('dt_invoice', ['academic_year_id' => 2021, 'semester_type_id' => 1]);
        if ($mba_invoice_2021_1) {
            $i_count = 0;
            $a_invoice_must_be_fixed = [];
            foreach ($mba_invoice_2021_1 as $o_invoice) {
                if (in_array($o_invoice->invoice_status, ['created', 'pending'])) {
                    $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                    if ($mba_invoice_installment) {
                        $mbo_invoice_full = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
                        if (!$mbo_invoice_full) {
                            print('ga ada full payment!');
                            print('<pre>');
                            var_dump($o_invoice);exit;
                        }
                        
                        if (!$mbo_invoice_full) {
                            print('ga ada installment!');
                            print('<pre>');
                            var_dump($o_invoice);exit;
                        }

                        $d_unpaid_installment = 0;
                        $b_denda = false;
                        foreach ($mba_invoice_installment as $o_installment) {
                            if (($o_installment->sub_invoice_details_amount_total > 0) AND ($o_installment->sub_invoice_details_status != 'paid')) {
                                $d_unpaid_installment += $o_installment->sub_invoice_details_amount_total;
                            }

                            if ($o_installment->sub_invoice_details_amount_fined > 0) {
                                $b_denda = true;
                            }
                        }

                        if ($mbo_invoice_full->sub_invoice_details_amount_total != $d_unpaid_installment) {
                            array_push($a_invoice_must_be_fixed, $o_invoice->invoice_id);
                            // $mba_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $o_invoice->personal_data_id]);
                            // // array_push($a_invoice_must_be_fixed, $o_invoice->invoice_id);
                            // $mba_invoice_details = $this->Im->get_invoice_list_detail(['di.invoice_id' => $o_invoice->invoice_id]);
                            // if ($mba_student_data[0]->finance_year_id != 2021) {
                            //     if (!$b_denda) {
                            //         $this->Im->update_sub_invoice_details([
                            //             'sub_invoice_details_amount' => $d_unpaid_installment,
                            //             'sub_invoice_details_amount_total' => $d_unpaid_installment
                            //         ], [
                            //             'sub_invoice_details_id' => $mbo_invoice_full->sub_invoice_details_id
                            //         ]);

                            //         $this->Im->update_sub_invoice([
                            //             'sub_invoice_amount' => $d_unpaid_installment,
                            //             'sub_invoice_amount_total' => $d_unpaid_installment
                            //         ], [
                            //             'sub_invoice_id' => $mbo_invoice_full->sub_invoice_id
                            //         ]);

                            //         if (!is_null($mbo_invoice_full->trx_id)) {
                            //             $a_update_billing = array(
                            //                 'trx_id' => $mbo_invoice_full->trx_id,
                            //                 'trx_amount' => $d_unpaid_installment,
                            //                 // 'customer_name' => $o_bni_data->customer_name,
                            //                 // 'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +1 month")),
                            //                 'description' => $mbo_invoice_full->sub_invoice_details_description,
                            //                 'customer_email' => 'bni.employee@company.ac.id'
                            //             );
                                        
                            //             $update = $this->Bnim->update_billing($a_update_billing);
                            //             print('<pre>');var_dump($update);print('<br>');
                            //         }
                                    
                            //         array_push($a_invoice_must_be_fixed, [
                            //             'invoice_id' => $o_invoice->invoice_id,
                            //             'semester_id' => $mba_invoice_details[0]->semester_id
                            //         ]);
                            //     }
                            // }
                        }
                    }

                    // print($o_invoice->personal_data_id);
                    // print('<br>');
                    // $i_count++;
                }
            }

            if (count($a_invoice_must_be_fixed) > 0) {
                print('<pre>');
                var_dump($a_invoice_must_be_fixed);exit;
            }
            else {
                print('aman');
            }
        }
    }

    public function convert_paydata()
    {
        $this->load->helper('file');
        $s_filepath = APPPATH.'uploads/finance/custom_report/paydata-20170601_20210921 8-310.xlsx';
        $o_spreadsheet = IOFactory::load("$s_filepath");
        $o_sheet = $o_spreadsheet->getActiveSheet();
		
        $i_row = 2;
        while ($o_sheet->getCell('E'.$i_row)->getValue() !== NULL) {
            $s_student_name = str_replace('="', '', trim(str_replace('"', '', $o_sheet->getCell("E$i_row")->getValue())));
            $s_billing_id = str_replace('="', '', trim(str_replace('"', '', $o_sheet->getCell("I$i_row")->getValue())));

            // $mba_student_data = $this->Stm->get_student_filtered([
            //     'pd.personal_data_name' => $s_student_name
            // ]);
            $mba_student_data = $this->Stm->get_student_by_name_filtered($s_student_name);

            if (!$mba_student_data) {
                $mba_billing_data = $this->Im->get_invoice_detail_by_trx_id($s_billing_id);
                if ($mba_billing_data) {
                    $mba_student_data = $this->Stm->get_student_filtered([
                        'ds.personal_data_id' => $mba_billing_data->personal_data_id
                    ]);
                }

                if (!$mba_billing_data) {
                    print("row: ".$i_row."; ".$s_student_name);
                    print('<br>');
                }
                // $mba_student_data = 
            }

            $i_row++;
        }
        print('<h1>Total '.$i_row.' row');
    }

    function copy_ofse_subject() {
        exit;
        $s_ofse_id_target = '0245741d-3e98-4cfb-bd1e-1f350b34d10d';
        $s_ofse_id_refference = '049f1ece-886e-40a1-b6bb-fabe61b62fd8';
        $mbo_semester_active = $this->Smm->get_active_semester();

        $mba_offered_subject_data = $this->General->get_where('dt_offered_subject', ['ofse_period_id' => $s_ofse_id_refference]);
        // $mba_offered_subject_data = $this->Osm->get_offered_subject_subject(['os.ofse_period_id' => $s_ofse_id_refference]);
        if ($mba_offered_subject_data) {
            foreach ($mba_offered_subject_data as $o_offered_subject) {
                $mba_offered_subject_data = $this->Osm->get_offered_subject_subject(['os.offered_subject_id' => $o_offered_subject->offered_subject_id]);
                $s_offered_subject_id = $this->uuid->v4();
                $a_data = [
                    'offered_subject_id' => $s_offered_subject_id,
                    'curriculum_subject_id' => $o_offered_subject->curriculum_subject_id,
                    'academic_year_id' => $mbo_semester_active->academic_year_id,
                    'semester_type_id' => ($mbo_semester_active->semester_type_id == 1) ? 4 : 6,
                    'program_id' => 1,
                    'study_program_id' => $o_offered_subject->study_program_id,
                    'ofse_period_id' => $s_ofse_id_target,
                    'ofse_status' => $o_offered_subject->ofse_status,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                if ($this->Osm->save_offer_subject($a_data)) {
                    $s_class_group_id = $this->uuid->v4();
                    $a_class_group_data = [
                        'class_group_id' => $s_class_group_id,
                        'academic_year_id' => $mbo_semester_active->academic_year_id,
                        'semester_type_id' => ($mbo_semester_active->semester_type_id == 1) ? 4 : 6,
                        'class_group_name' => 'OFSE '.$mba_offered_subject_data[0]->subject_name,
                        'date_added' => date('Y-m-d H:i:s')
                    ];

                    $a_class_group_subject_data = [
                        'class_group_subject_id' => $this->uuid->v4(),
                        'class_group_id' => $s_class_group_id,
                        'offered_subject_id' => $s_offered_subject_id,
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    if ($this->Cgm->save_data($a_class_group_data)) {
                        if ($this->Cgm->save_class_group_subject($a_class_group_subject_data)) {
                            print('sukses copy subject..'.$mba_offered_subject_data[0]->subject_name);
                        }else{
                            print('gagal copy kelas subject..'.$mba_offered_subject_data[0]->subject_name);
                        }
                    }else{
                        print('gagal copy kelas..'.$mba_offered_subject_data[0]->subject_name);
                    }
                    print('<br>');
                }
                else {
                    print('failed insert subject:');
                    var_dump($a_data);
                }
            }
            // print('<pre>');var_dump($mba_offered_subject_data);exit;
        }
    }

    public function fill_student_thesis_alumni()
    {
        $this->load->helper('file');
        $this->load->model('thesis/Thesis_model', 'Tm');
        $s_filepath = APPPATH.'uploads/temp/student_graduated.xlsx';
        $s_file_path = APPPATH.'uploads/temp/';
        $s_filename = 'student_graduated_result.xlsx';
        $o_spreadsheet = IOFactory::load("$s_filepath");
        $o_sheet = $o_spreadsheet->getActiveSheet();
		
        $i_row = 2;
        while ($o_sheet->getCell('A'.$i_row)->getValue() !== NULL) {
            $s_student_id = str_replace('="', '', trim(str_replace('"', '', $o_sheet->getCell("A$i_row")->getValue())));
            
            $mba_student_thesis = $this->Tm->get_thesis_student([
                "ts.student_id" => $s_student_id
            ]);

            if ($mba_student_thesis) {
                $o_thesis = $mba_student_thesis[0];
                $s_thesis_title = $o_thesis->thesis_title;
                $o_sheet->setCellValue("H$i_row", $s_thesis_title);

                $mba_thesis_advisor_1 = $this->Tm->get_advisor_student($o_thesis->thesis_student_id, 'advisor_1');
                $mba_thesis_advisor_2 = $this->Tm->get_advisor_student($o_thesis->thesis_student_id, 'advisor_2');
                $mba_thesis_examiner_1 = $this->Tm->get_examiner_student($o_thesis->thesis_student_id, ['tse.examiner_type' => 'examiner_1']);
                $mba_thesis_examiner_2 = $this->Tm->get_examiner_student($o_thesis->thesis_student_id, ['tse.examiner_type' => 'examiner_2']);
                $mba_thesis_examiner_3 = $this->Tm->get_examiner_student($o_thesis->thesis_student_id, ['tse.examiner_type' => 'examiner_3']);
                $mba_thesis_examiner_4 = $this->Tm->get_examiner_student($o_thesis->thesis_student_id, ['tse.examiner_type' => 'examiner_4']);

                if ($mba_thesis_advisor_1) {
                    $advisor_name_1 = $this->Pdm->retrieve_title($mba_thesis_advisor_1[0]->personal_data_id);
                    $o_sheet->setCellValue("I$i_row", $advisor_name_1);
                }

                if ($mba_thesis_advisor_2) {
                    $advisor_name_2 = $this->Pdm->retrieve_title($mba_thesis_advisor_2[0]->personal_data_id);
                    $o_sheet->setCellValue("J$i_row", $advisor_name_2);
                }

                if ($mba_thesis_examiner_1) {
                    $examiner_name_1 = $this->Pdm->retrieve_title($mba_thesis_examiner_1[0]->personal_data_id);
                    $o_sheet->setCellValue("K$i_row", $examiner_name_1);
                }

                if ($mba_thesis_examiner_2) {
                    $examiner_name_2 = $this->Pdm->retrieve_title($mba_thesis_examiner_2[0]->personal_data_id);
                    $o_sheet->setCellValue("L$i_row", $examiner_name_2);
                }

                if ($mba_thesis_examiner_3) {
                    $examiner_name_3 = $this->Pdm->retrieve_title($mba_thesis_examiner_3[0]->personal_data_id);
                    $o_sheet->setCellValue("M$i_row", $examiner_name_3);
                }

                if ($mba_thesis_examiner_4) {
                    $examiner_name_4 = $this->Pdm->retrieve_title($mba_thesis_examiner_4[0]->personal_data_id);
                    $o_sheet->setCellValue("N$i_row", $examiner_name_4);
                }
            }
            $i_row++;
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
}