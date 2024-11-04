<?php
	
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;	
	
class Referral extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('admission/Referral_model', 'Refm');
		$this->load->model('student/Student_model', 'Stm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('address/Address_model', 'Am');
	}

	public function get_reference_list()
	{
		if ($this->input->is_ajax_request()) {
			$s_referrer_id = $this->input->post('referrer_id');
			$mba_reference_list = $this->Refm->get_reference_list(array('referrer_id' => $s_referrer_id));
			if ($mba_reference_list) {
				foreach ($mba_reference_list as $key => $o_reference) {
					$mbo_student_data = $this->Stm->get_student_filtered(array('ds.personal_data_id' => $o_reference->referenced_id))[0];
					if ($mbo_student_data) {
						$o_reference = (array) $o_reference;
						$mbo_student_data = (array) $mbo_student_data;
						$mba_reference_list[$key] = array_merge($o_reference, $mbo_student_data);
					}
				}
			}

			print json_encode(array('code' =>  0, 'data' => $mba_reference_list));
		}
	}
	
	public function get_referral_lists()
	{
		if($this->input->is_ajax_request()){
			$data = $this->Refm->get_referral_list();
			$rtn = ['code' => 0, 'data' => $data];
			print json_encode($rtn);
			exit;
		}
	}
	
	private function create_referral_pdf($o_personal_data)
	{
		$a_personal_data_address = $this->Am->get_personal_address($o_personal_data->personal_data_id);
		$o_personal_data_address = $a_personal_data_address[0];
		$s_template_path = APPPATH.'uploads/templates/';
		$s_media_path = APPPATH.'uploads/'.$o_personal_data->personal_data_id.'/form_referral/';
		
		if(!file_exists($s_media_path)){
			mkdir($s_media_path, 0777, TRUE);
		}
		
		$s_filename = "FormPendaftaran-SOMEONE-".$o_personal_data->personal_data_name;
		$s_xls_filename = str_replace(' ', '_', $s_filename.'.xls');
		$s_pdf_filename = str_replace(' ', '_', $s_filename.'.pdf');
		$s_xls_filepath = $s_media_path.$s_xls_filename;
		$s_pdf_filepath = $s_media_path.$s_pdf_filename;
		
		$s_template = $s_template_path.'FormPendaftaran-SOMEONE.xls';
		$s_title = "Form Pendaftaran SOMEONE";
		
		$o_spreadsheet = IOFactory::load($s_template);
		$o_sheet = $o_spreadsheet->getActiveSheet();
		$o_spreadsheet->getProperties()
		->setTitle($s_title)
		->setCreator("IULI Admission")
		->setCategory("Form Pendaftaran Someone");
		
		$i_month = date('n', time());
		
		$o_sheet->setCellValue('G10', $o_personal_data->personal_data_reference_code);
		$o_sheet->setCellValue('F13', $o_personal_data->personal_data_name);
		$o_sheet->setCellValue('F15', implode(' ', [$o_personal_data_address->address_street, "RT ".$o_personal_data_address->address_rt, "RW ".$o_personal_data_address->address_rw]));
		$o_sheet->setCellValue('F16', implode(' ', [$o_personal_data_address->address_sub_district, $o_personal_data_address->nama_wilayah, $o_personal_data_address->address_zipcode]));
		$o_sheet->setCellValue('F17', $o_personal_data_address->country_name);
		$o_sheet->setCellValue('F20', $o_personal_data->personal_data_cellular);
		$o_sheet->setCellValue('F22', $o_personal_data->personal_data_email);
		$o_sheet->setCellValue('F24', strval($o_personal_data->personal_data_id_card_number));
		$o_sheet->setCellValue('B42', 'Bumi Serpong Damai, '.date('j F, Y'));
		$o_sheet->setCellValue('C50', $o_personal_data->personal_data_name);
		
		$o_writer = IOFactory::createWriter($o_spreadsheet, 'Xls');
		$o_writer->save($s_xls_filepath);
		$s_shell_exec = '/usr/bin/soffice --headless --convert-to pdf '.str_replace('  ', '_', $s_xls_filepath).' --outdir '.$s_media_path;
		shell_exec($s_shell_exec);
		
		return $s_pdf_filepath;
	}
	
	private function send_referral_code($o_personal_data)
	{
		$s_pdf_file = $this->create_referral_pdf($o_personal_data);
		$s_email_to = $o_personal_data->personal_data_email;
		$s_email_subject = 'Nomor Kode SOMEONE GET STUDENT 2020';
		$a_email_cc = [
			'employee@company.ac.id',
			'employee@company.ac.id'
		];
		$s_template_path = APPPATH.'uploads/templates/';
		$s_email_body = <<<TEXT
Greetings From IULI! 
Selamat!

Anda berhasil melakukan Registrasi Program Reward SOMEONE Get Students (SOMEONE) yang diselenggarakan oleh International University Liaison Indonesia (IULI).
Untuk selanjutnya mohon agar mengikuti petunjuk ini :
Catat Kode SOMEONE Anda, sebagai nomer referensi ketika kandidat yang menjadi referral Anda mendaftar di IULI.
Print dan Tandatangani diatas materai Formulir Pendaftaran dan Pernyataan Anda.
Kirimkan formulir yang sudah ditandatangani tersebut berserta fotokopi dokumen penunjang ke alamat kami di:
Kantor Admission, Kampus IULI
Associate Tower 7th Floor.
Intermark Indonesia
Up. Ibu Diah Danuri
Pelajari dengan baik Info Panduan Program SOMEONE Get Students 2020. Apabila ada pertanyaan Anda dapat menghubungi kami di 0878 440 33007 (Diah) atau di 0878 4403 3046 (Zaky) pada jam operasional Senin – Jumat : 08.30 – 17.00 WIB.
 
Hormat kami,
Admission IULI
TEXT;
		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
		$this->email->from('employee@company.ac.id', 'IULI PMB Team');
/*
		$this->email->to($s_email_to);
		$this->email->cc($a_email_cc);
*/
		$this->email->to(['employee@company.ac.id']);
		$this->email->subject($s_email_subject);
		// $this->email->message($s_email_body);
		$this->email->message($this->load->view('referral_body_email', null, true));
		$this->email->attach($s_pdf_file);
		$this->email->attach($s_template_path.'Info-PanduanSOMEONE-GS-2020.pdf');
		$this->email->attach($s_template_path.'EnrollmentForm-2020.pdf');
		$this->email->attach($s_template_path.'Form CheckList-Dok.Kandidat-SomeoneProgram.pdf');
		$this->email->send();
	}
	
	public function new_referrer()
	{
		if($this->input->is_ajax_request()){
			$this->form_validation->set_rules('name', 'Name', 'trim|required');
			$this->form_validation->set_rules('street', 'Street', 'trim|required');
			$this->form_validation->set_rules('district_id', 'District', 'trim|required');
			$this->form_validation->set_rules('rt', 'RT', 'trim|required');
			$this->form_validation->set_rules('rw', 'RW', 'trim|required');
			$this->form_validation->set_rules('zip_code', 'Zip Code', 'trim|required');
			$this->form_validation->set_rules('phone', 'Phone', 'trim|required|numeric');
			$this->form_validation->set_rules('email', 'Email', 'trim|required');
			$this->form_validation->set_rules('id_card_number', 'KTP Number', 'trim|required');
			
			if($this->form_validation->run()){
				$s_email = set_value('email');
				if(!$this->Pdm->get_personal_data_by_email($s_email)){
					$a_new_personal_data = [
						'personal_data_name' => strtoupper(set_value('name')),
						'personal_data_cellular' => set_value('phone'),
						'portal_status' => 'no',
						'personal_data_id_card_number' => set_value('id_card_number'),
						'personal_data_id_card_type' => 'national_id',
						'personal_data_email' => $s_email,
						'is_referrer_agent' => true
					];
					$s_personal_data_id = $this->Pdm->create_new_personal_data($a_new_personal_data);
					$a_address_data = [
						'dikti_wilayah_id' => set_value('district_id'),
						'address_rt' => set_value('rt'),
						'address_rw' => set_value('rw'),
						'address_zipcode' => set_value('zip_code'),
						'address_street' => $this->input->post('street')
					];
					$s_address_id = $this->Am->save_address($a_address_data);
					
					$this->Am->save_personal_address([
						'personal_data_id' => $s_personal_data_id,
						'address_id' => $s_address_id,
						'personal_address_name' => 'Home',
						'personal_address_type' => 'primary'
					]);
					
					$s_reference_code = modules::run('personal_data/create_reference_code', $s_personal_data_id);
					$o_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
					$this->send_referral_code($o_personal_data);
					$rtn = ['code' => 0, 'message' => 'Success', 'referral_code' => $s_reference_code];
				}
				else{
					$rtn = ['code' => 1, 'message' => 'Email is already registered, please use another email'];
				}
			}
			else{
				$rtn = ['code' => 1, 'message' => validation_errors('<li>', '</li>')];
			}
			
			print json_encode($rtn);
			exit;
		}
	}

	public function reference_list($s_referrer_id)
	{
		$mbo_persoal_referer = $this->Pdm->get_personal_data_by_id($s_referrer_id);
		// if (($mbo_persoal_referer) AND ($mbo_persoal_referer->is_referrer_agent == '1')) {
		// 	$this->a_page_data['referral_agent'] = true;
		// 	$this->a_page_data['referral_data'] = $mbo_persoal_referer;
		// }else{
		// 	$this->a_page_data['referral_agent'] = false;
		// }
		$this->a_page_data['referral_agent'] = true;
			$this->a_page_data['referral_data'] = $mbo_persoal_referer;
			
		$this->a_page_data['body'] = $this->load->view('table/referal_referenced', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function lists()
	{
		$this->a_page_data['body'] = $this->load->view('referral_list', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function form_referral($b_display_layout = false)
	{
		if($b_display_layout){
			$this->a_page_data['body'] = $this->load->view('form/new_referral_person_form', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}
		else{
			$this->load->view('form/new_referral_person_form', $this->a_page_data);
		}
	}
}