<?php
class Api_check extends App_core
{
	private $s_production_client_id = '310';
	private $s_development_client_id = '141';
	private $s_client_id;
	private $a_client_ids = [310 => 'production', 141 => 'development'];
	
	private $s_production_secret_key = 'ff7dd3a3ac49cfc2a3070a317b688c30';
	private $s_development_secret_key = '2d9b7b2442a0dd722690b8c525a52915';
	private $s_secret_key;
	
	private $s_production_url = 'https://api.bni-ecollection.com/';
	private $s_development_url = 'https://apibeta.bni-ecollection.com/';
	// $s_payment_simulator_bni = 'https://portalbeta-v2.spesandbox.com/partner/simulator/payment-simulator/index';
	private $s_url;
	
	private $d_fine_amount = 500000;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('finance/Invoice_model', 'Im');
		$this->load->model('finance/Bni_model', 'Bm');
		$this->load->model('institution/Institution_model', 'Insm');
		$this->load->model('address/Address_model', 'Addrm');
		
		$this->set_environment('production');
	}

	public function cekinfo()
	{
		phpinfo();
	}

	public function create_dev_va()
	{
		// $result = $this->Bm->create_billing_dev();
		$a_billing_data = array(
			// 'trx_amount' => 20000,
			'billing_type' => 'o',
			'customer_name' => 'IULI SPMI',
			'virtual_account' => '8310000012345678',
			'description' => 'testing billing',
			'datetime_expired' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d', time())."+ 10 day")),
			'customer_email' => 'bni.employee@company.ac.id'
		);
		// $a_bni_result = $this->Bm->create_billing($a_billing_data);
		print('<pre>');var_dump($a_bni_result);exit;
	}
	
	public function test_api()
	{
		var_dump($this->input->post());
		exit;
	}

	public function check_billing()
	{
		print('still working!');exit;
		// $this->failover(date('Y-m-d'), 2);
		
		// $mba_unpaid_invoice = $this->Im->get_unpaid_invoice(['di.invoice_id' => 'fa1fa2ea-f0e3-4f03-84fe-489fea1b703b']);
		$mba_unpaid_invoice = $this->Im->get_unpaid_invoice();
		// $s_json_string = '{"invoice_id":"0d882da4-612b-4373-bac1-2ca66c54fd32","personal_data_id":"7f504d5f-a493-4a66-9cd4-4d8f7dbeae2d","invoice_number":"INV-19081402","invoice_amount_paid":"0","invoice_amount_fined":"0","invoice_fined_count":"0","invoice_description":"Scholarship Tuition Fee - Batch 2019","invoice_allow_fine":"yes","invoice_allow_reminder":"yes","invoice_status":"created","invoice_fine_status":"default","invoice_datetime_paid_off":null,"date_added":"2019-08-14 11:27:55","timestamp":"2019-08-14 11:33:23","country_of_birth":"9bb722f5-8b22-11e9-973e-52540001273f","citizenship_id":"9bb722f5-8b22-11e9-973e-52540001273f","religion_id":"53b17ff0-e4c0-4fc9-8735-bbb8c7054048","ocupation_id":null,"personal_data_name":"DAVE TRIAND ANGGORO","personal_data_email":"davetriandanggoro@gmail.com","personal_data_phone":"75675766","personal_data_cellular":"089653515157","personal_data_id_card_number":"000000000","personal_data_id_card_type":"national_id","personal_data_place_of_birth":"TANGERANG","personal_data_date_of_birth":"2001-10-12","personal_data_gender":"M","personal_data_nationality":"WNI","personal_data_marital_status":"single","personal_data_mother_maiden_name":null,"personal_data_password":null,"personal_data_password_token":"b1cd5dea00238c403068e1069c8f035d","personal_data_password_token_expired":null,"personal_data_email_confirmation":"no","personal_data_email_confirmation_token":null,"personal_data_reference_code":null,"portal_status":"open","pmb_sync":"1","portal_id":"0"}';
		// $o_invoice = json_decode($s_json_string);
		if($mba_unpaid_invoice){
			$x =0;
			foreach($mba_unpaid_invoice as $o_invoice){
				$send_reminder = $this->check_invoice($o_invoice);
				if ($send_reminder) {
					$x++;
					// $this->send_reminder($o_invoice);
					print('send to: '.$o_invoice->personal_data_name);
					print('<br>');
				}
				// print('<pre>');
				// var_dump($send_reminder);
			}

			print('<h1>'.$x.'</h1>');
		}
	}
	
	public function send_reminder($o_invoice)
	{
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fam');
		
		$a_email = $this->config->item('email');
		$s_email_from = $a_email['finance']['payment'];
		$a_bcc_email = array('employee@company.ac.id', 'employee@company.ac.id');
		
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($o_invoice->personal_data_id, [
			'student_status !=' => 'resign'
		]);
		$mbo_family_data = $this->Fam->get_family_by_personal_data_id($o_invoice->personal_data_id);
		$mba_parent_email = false;
		if($mbo_family_data){
			$mba_family_members = $this->Fam->get_family_members($mbo_family_data->family_id, array(
				'family_member_status != ' => 'child'
			));
			if($mba_family_members){
				$mba_parent_email = array();
				foreach($mba_family_members as $family){
					array_push($mba_parent_email, $family->personal_data_email);
				}
			}
		}

		if ($mbo_student_data->student_email == 'hose.winanda@stud.iuli.ac.id') {
			array_push($mba_parent_email, 'hosewinanda@gmail.com');
		}
		
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		
		$this->email->from($s_email_from, 'IULI Reminder System');
		$this->email->to('employee@company.ac.id');
		if($mba_parent_email){
			// $this->email->cc($mba_parent_email);
		}
		
		$this->email->reply_to($a_email['finance']['head']);
		// $this->email->reply_to($a_email['finance']['main']);
		$mba_sub_invoice = $this->Im->get_sub_invoice_data(['dsi.invoice_id' => $o_invoice->invoice_id]);
		
		if($mba_sub_invoice){
			foreach($mba_sub_invoice as $sub_invoice){
				$mba_sub_invoice_details = $this->Im->get_invoice_data(['dsid.sub_invoice_id' => $sub_invoice->sub_invoice_id]);
				$sub_invoice->sub_invoice_details_data = false;
				
				if($mba_sub_invoice_details){
					$sub_invoice->sub_invoice_details_data = $mba_sub_invoice_details;
				}
			}
		}

		$mba_invoice_details = $this->Im->get_invoice_details([
			'did.invoice_id' => $o_invoice->invoice_id
		]);
		
		$this->a_page_data['sub_invoice_data'] = $mba_sub_invoice;
		$this->a_page_data['invoice_data'] = $o_invoice;
		$this->a_page_data['invoice_details'] = $mba_invoice_details;

		$s_payment_type = 'Tuition Fee';
		$s_payment_type_code = '02';
		if ($mba_invoice_details) {
			foreach ($mba_invoice_details as $o_invoice_details) {
				if ($o_invoice_details->fee_amount_type == 'main') {
					$mbo_amount_type = $this->Im->get_payment_type($o_invoice_details->payment_type_code);
					$s_payment_type = $mbo_amount_type->payment_type_name;
					$s_payment_type_code = $mbo_amount_type->payment_type_code;
				}
			}
		}
		
		$this->email->subject("[REMINDER] ".$s_payment_type." Invoice");
		if($o_invoice->invoice_allow_fine == 'yes'){
			$s_html = $this->load->view('email_reminder_template_fine', $this->a_page_data, TRUE);
		}
		else{
			$s_html = $this->load->view('email_reminder_template', $this->a_page_data, TRUE);
		}

		if (in_array($s_payment_type_code, ['03', '04', '05', '07', '08', '09'])) {
			$s_html = $this->load->view('billing_template', $this->a_page_data, TRUE);
		}

		// $this->email->message($s_html);
		// $this->email->send();
	}

	public function set_fine_flatted($o_invoice, $mba_sub_invoice_details_installment)
	{
		$i_invoice_fined_count = $o_invoice->invoice_fined_count;
		$d_fine_amount = $this->d_fine_amount;
		$a_total_fine = array();
		$a_amount_total = array();
		$mbo_invoice_full_payment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
		if (!$mbo_invoice_full_payment) {
			print($o_invoice);exit;
		}

		foreach ($mba_sub_invoice_details_installment as $o_installment) {
			if ($o_installment->sub_invoice_details_amount_fined == 0) {
				$d_sub_invoice_amount = $o_installment->sub_invoice_details_amount;
				$d_sub_invoice_amount_fined = $d_fine_amount;
				$d_sub_invoice_amount_total = $d_sub_invoice_amount + $d_sub_invoice_amount_fined;

				array_push($a_total_fine, $d_sub_invoice_amount_fined);
				array_push($a_amount_total, $d_sub_invoice_amount_total);

				$a_update_sub_invoice_details = array(
					'sub_invoice_details_amount' => $d_sub_invoice_amount,
					'sub_invoice_details_amount_fined' => $d_sub_invoice_amount_fined,
					'sub_invoice_details_amount_total' => $d_sub_invoice_amount_total,
					'sub_invoice_details_status' => 'fined',
				);
				
				$i_invoice_fined_count++;
				$this->Im->update_sub_invoice_details($a_update_sub_invoice_details, array('sub_invoice_details_id' => $o_installment->sub_invoice_details_id));
			}
			else {
				$d_sub_invoice_amount_fined = $o_installment->sub_invoice_details_amount_fined;
				array_push($a_total_fine, $d_sub_invoice_amount_fined);
				array_push($a_amount_total, $o_installment->sub_invoice_amount_total);
			}
		}

		$d_amount_full = $mbo_invoice_full_payment->sub_invoice_details_amount;
		$d_sum_total_fine = array_sum($a_total_fine);
		$d_total_amount_full = $d_amount_full + $d_sum_total_fine;
		$a_update_sub_invoice_details = array(
			'sub_invoice_details_amount' => $mbo_invoice_full_payment->sub_invoice_details_amount,
			'sub_invoice_details_amount_fined' => $d_sum_total_fine,
			'sub_invoice_details_amount_total' => $d_total_amount_full,
			'sub_invoice_details_status' => 'fined',
		);
		$this->Im->update_sub_invoice_details($a_update_sub_invoice_details, array('sub_invoice_details_id' => $mbo_invoice_full_payment->sub_invoice_details_id));

		$a_update_sub_invoice_installment = array(
			'sub_invoice_amount_total' => array_sum($a_amount_total)
		);
		$a_update_sub_invoice_full = $a_update_sub_invoice_installment;
		$a_update_sub_invoice_full['sub_invoice_amount'] = $d_total_amount_full;
		$this->Im->update_sub_invoice($a_update_sub_invoice_installment, array('invoice_id' => $o_invoice->invoice_id, 'sub_invoice_type' => 'installment'));
		$this->Im->update_sub_invoice($a_update_sub_invoice_full, array('invoice_id' => $o_invoice->invoice_id, 'sub_invoice_type' => 'full'));
		
		$a_update_invoice = array(
			'invoice_amount_fined' => $d_sum_total_fine,
			'invoice_fined_count' => $i_invoice_fined_count,
			'invoice_fine_status' => 'fined',
			'invoice_status' => 'pending'
		);
		$this->Im->update_invoice($a_update_invoice, array('invoice_id' => $o_invoice->invoice_id));
	}
	
	public function set_fine($o_invoice, $mba_sub_invoice_details, $o_sub_invoice_details_fined)
	{
		$i_invoice_fined_count = $o_invoice->invoice_fined_count;
		
		$d_fine_amount = $this->d_fine_amount;
		$o_sub_invoice_full_payment;
		$o_sub_invoice_installment;

		$a_total_fine = array();
		foreach($mba_sub_invoice_details as $o_sub_invoice_details){
			$o_sub_invoice_installment = $o_sub_invoice_details;
			
			$d_sub_invoice_amount = $o_sub_invoice_details->sub_invoice_details_amount;
			$d_sub_invoice_amount_fined = $o_sub_invoice_details->sub_invoice_details_amount_fined;
			
			$d_sub_invoice_amount_fined += $d_fine_amount;
			
			array_push($a_total_fine, $d_sub_invoice_amount_fined);
			$d_sub_invoice_amount_total = $d_sub_invoice_amount + $d_sub_invoice_amount_fined;
			
			$a_update_sub_invoice_details = array(
				'sub_invoice_details_amount' => $d_sub_invoice_amount,
				'sub_invoice_details_amount_fined' => $d_sub_invoice_amount_fined,
				'sub_invoice_details_amount_total' => $d_sub_invoice_amount_total,
				'sub_invoice_details_status' => 'fined',
			);
			$i_invoice_fined_count++;
			$this->Im->update_sub_invoice_details($a_update_sub_invoice_details, array('sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id));
		}
		
		$d_sum_total_fine = array_sum($a_total_fine);
		
		$a_update_invoice = array(
			'invoice_amount_fined' => $d_sum_total_fine,
			'invoice_fined_count' => $i_invoice_fined_count,
			'invoice_fine_status' => 'fined',
			'invoice_status' => 'pending'
		);
		$this->Im->update_invoice($a_update_invoice, array('invoice_id' => $o_invoice->invoice_id));
		
		$a_update_sub_invoice_details = array(
			'sub_invoice_details_amount' => $o_sub_invoice_details_fined->sub_invoice_details_amount,
			'sub_invoice_details_amount_fined' => $d_sum_total_fine,
			'sub_invoice_details_amount_total' => (floatval($o_sub_invoice_details_fined->sub_invoice_details_amount) + floatval($d_sum_total_fine)),
			'sub_invoice_details_status' => 'fined',
		);
		$this->Im->update_sub_invoice_details($a_update_sub_invoice_details, array('sub_invoice_details_id' => $o_sub_invoice_details_fined->sub_invoice_details_id));

		
		$a_update_sub_invoice_fp = array(
			'sub_invoice_amount_total' => (floatval($o_sub_invoice_details_fined->sub_invoice_amount) + floatval($d_sum_total_fine))
		);
		$this->Im->update_sub_invoice($a_update_sub_invoice_fp, array('invoice_id' => $o_invoice->invoice_id));

		if (!is_null($o_sub_invoice_details_fined->trx_id)){
			$o_bni_data = $this->Bm->get_data_by_trx_id($o_sub_invoice_details_fined->trx_id);

			$a_update_billing = array(
				'trx_id' => $o_sub_invoice_details_fined->trx_id,
				'trx_amount' => (floatval($o_sub_invoice_details_fined->sub_invoice_details_amount) + floatval($d_sum_total_fine)),
				'customer_name' => $o_bni_data->customer_name,
				'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details_fined->sub_invoice_details_deadline." +1 month")),
				'description' => $o_sub_invoice_details_fined->sub_invoice_details_description,
				'customer_email' => 'bni.employee@company.ac.id'
			);
			
			$this->Bm->update_billing($a_update_billing);
		}
	}

	public function update_bni($o_invoice)
	{
		$mba_sub_invoice_details = $this->Im->get_invoice_data(['di.invoice_id' => $o_invoice->invoice_id]);
		if ($mba_sub_invoice_details) {
			foreach ($mba_sub_invoice_details as $o_sub_invoice_details) {
				if (!is_null($o_sub_invoice_details->trx_id)) {
					$o_bni_data = $this->Bm->get_data_by_trx_id($o_sub_invoice_details->trx_id);

					if($o_bni_data->va_status == 2){
						$s_trx_id = modules::run('finance/invoice/reactivate_billing', $o_sub_invoice_details->trx_id);
						$o_bni_data = $this->Bnim->get_data_by_trx_id($s_trx_id);
					}

					$a_update_billing = array(
						'trx_id' => $o_sub_invoice_details->trx_id,
						'trx_amount' => $o_sub_invoice_details->sub_invoice_details_amount_total,
						'customer_name' => $o_bni_data->customer_name,
						'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +3 day")),
						'description' => $o_sub_invoice_details->sub_invoice_details_description,
						'customer_email' => 'bni.employee@company.ac.id'
					);
					
					$this->Bm->update_billing($a_update_billing);
				}
			}
		}
		
		
		
		if($o_sub_invoice_details_data = $this->Im->get_sub_invoice_by_trx_id($s_trx_id)){
			
		}
		else{
			$a_update_billing = array(
				'trx_id' => $s_trx_id,
				'trx_amount' => 999,
				'customer_name' => 'CANCEL PAYMENT',
				'datetime_expired' => '2020-01-01 23:59:59',
				'description' => 'CANCEL PAYMENT'
			);
		}
		
		
	}

	public function test_invoice_fined()
	{
		$s_invoice_id = '09fb39e5-dd94-465e-aa3f-52ca2b4fde7e';
		$mba_unpaid_invoice = $this->Im->get_unpaid_invoice(['di.invoice_id' => $s_invoice_id]);
		if ($mba_unpaid_invoice) {
			foreach ($mba_unpaid_invoice as $o_invoice) {
				$this->check_invoice($o_invoice);
			}
		}
		else {
			print('ksong!');
		}
	}
	
	public function check_invoice($o_invoice, $b_reminder = false)
	{
		$this->load->model('student/Student_model', 'Sm');
		$a_personal_data_id_always_send = [
			'a4200bdc-f1b7-46c1-a322-0075c85fcb68', //HEIZKEL JAVIER OSZARWIN (AVE/2015)
			'50ef9e08-51fe-4fd3-8d8d-989133c95ca1', //LOUAI SEKKOUR (IBA/2018) 
			'831eec77-0fd5-48f0-b066-8b0899106caf', //RIHAM SEBTI SEKKOUR (HTM/2019)
		];
		$i_now = time();

		$b_send_reminder = false;
		$b_allow_fine = false;
		
		$mba_sub_invoice_details = $this->Im->get_invoice_data(['di.invoice_id' => $o_invoice->invoice_id]);
		if($mba_sub_invoice_details){
			$a_sub_invoice_details_fined = array();
			$o_sub_invoice_details_fined = false;
			// return $mba_sub_invoice_details;
			
			foreach($mba_sub_invoice_details as $o_sub_invoice_details){
				if (($o_sub_invoice_details->sub_invoice_details_status != 'paid') AND ($o_sub_invoice_details->sub_invoice_details_amount > 0)) {
					$i_deadline = strtotime($o_sub_invoice_details->sub_invoice_details_deadline);
					$i_datediff = $i_now - $i_deadline;
					$i_float = round($i_datediff / (60 * 60 * 24));

					if($i_float == 0){
						$mba_student_invoice_data = $this->Im->student_has_invoice_data($o_invoice->personal_data_id);
						$s_payment_type_code = substr($o_sub_invoice_details->sub_invoice_details_va_number, 4, 2);

						if ($s_payment_type_code == '02') {
							$a_update_sub_invoice_details = array(
								'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +1 month"))
							);
							$this->Im->update_sub_invoice_details($a_update_sub_invoice_details, array('sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id));

							$o_bni_data = $this->Bm->get_data_by_trx_id($o_sub_invoice_details->trx_id);

							$a_update_billing = array(
								'trx_id' => $o_sub_invoice_details->trx_id,
								// 'trx_amount' => $o_sub_invoice_details->sub_invoice_details_amount_total,
								'customer_name' => $o_bni_data->customer_name,
								'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +1 month")),
								'description' => $o_sub_invoice_details->sub_invoice_details_description,
								'customer_email' => 'bni.employee@company.ac.id'
							);
							
							$this->Bm->update_billing($a_update_billing);
						}
						else if ($s_payment_type_code == '05') {
							$a_update_sub_invoice_details = array(
								'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +7 day"))
							);
							$this->Im->update_sub_invoice_details($a_update_sub_invoice_details, array('sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id));
							$o_bni_data = $this->Bm->get_data_by_trx_id($o_sub_invoice_details->trx_id);

							$a_update_billing = array(
								'trx_id' => $o_sub_invoice_details->trx_id,
								'trx_amount' => $o_sub_invoice_details->sub_invoice_details_amount_total,
								'customer_name' => $o_bni_data->customer_name,
								'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +10 day")),
								'description' => $o_sub_invoice_details->sub_invoice_details_description,
								'customer_email' => 'bni.employee@company.ac.id'
							);
							$this->Bm->update_billing($a_update_billing);
							$b_send_reminder = true;
						}

						if($o_invoice->invoice_allow_fine == 'yes'){
							if($o_sub_invoice_details->sub_invoice_type == 'installment'){
								array_push($a_sub_invoice_details_fined, $o_sub_invoice_details);
							}
							else{
								$o_sub_invoice_details_fined = $o_sub_invoice_details;
							}
							
							if (($mba_student_invoice_data) AND ($mba_student_invoice_data->semester_id != 1)) {
								$b_allow_fine = true;
							}
						}
					}
					else if($i_float >= -14 AND $i_float < 0){
					// else if($i_float >= -14){
						if(is_null($o_sub_invoice_details->trx_id)){
							// create va
							$a_trx_data = array(
								'trx_amount' => $o_sub_invoice_details->sub_invoice_details_amount,
								'billing_type' => 'c',
								'customer_name' => $o_invoice->personal_data_name,	
								'virtual_account' => $o_sub_invoice_details->sub_invoice_details_va_number,
								'description' => $o_sub_invoice_details->sub_invoice_details_description,
								'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +10 day")),
								'customer_email' => 'bni.employee@company.ac.id'
							);
							$a_bni_result = $this->Bm->create_billing($a_trx_data);
							
							$a_sub_invoice_details_update = array(
								'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline))
							);
							
							if($a_bni_result['status'] == '000'){
								$a_sub_invoice_details_update['trx_id'] = $a_bni_result['trx_id'];
							}
							else{
								if($a_bni_result['status'] == '102'){
									// print('<pre>ss');var_dump($a_bni_result);exit;
									$a_update_billing = array(
										'trx_id' => $a_bni_result['trx_id'],
										'trx_amount' => 999,
										'customer_name' => 'CANCEL PAYMENT',
										'datetime_expired' => '2020-01-01 23:59:59',
										'description' => 'CANCEL PAYMENT'
									);
									$this->Bm->update_billing($a_update_billing);
								}

								$a_body = [
									'trx_data' => $a_trx_data,
									'bni_result' => $a_bni_result
								];
								$this->email->from('employee@company.ac.id');
								$this->email->to(array('employee@company.ac.id'));
								$this->email->subject('ERROR CHECK BILLING');
								$this->email->message(json_encode($a_body));
								$this->email->send();
							}
							
							$this->Im->update_sub_invoice_details(
								$a_sub_invoice_details_update, 
								array(
									'sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id
								)
							);
						}

						$o_total_has_invoice = $this->General->get_where('dt_invoice', ['personal_data_id' => $o_invoice->personal_data_id]);
						$a_clause_invoice_details = [
							'did.invoice_id' => $o_invoice->invoice_id,
							'df.payment_type_code' => '02'
						];

						if (($o_total_has_invoice) AND (count($o_total_has_invoice) == 1)) {
							$a_clause_invoice_details['df.semester_id != '] = 1;
						}

						// send reminder
						$mba_invoice_details = $this->Im->get_invoice_details($a_clause_invoice_details);
						
						if($mba_invoice_details){
							if (in_array($o_invoice->personal_data_id, $a_personal_data_id_always_send)) {
								if($o_sub_invoice_details->sub_invoice_details_status != 'paid'){
									$b_send_reminder = true;
								}
							}
							else if ($o_invoice->invoice_allow_reminder == 'yes') {
								$mba_student_data = $this->Sm->get_student_list_data([
									'ds.personal_data_id' => $o_invoice->personal_data_id
								], ['active', 'graduated']);
								// $mbo_student_data = $this->General->get_where('dt_student', [
								// 	'personal_data_id' => $o_invoice->personal_data_id,
								// 	'student_status' => 'active',
								// ]);
	
								if(($o_sub_invoice_details->sub_invoice_details_status != 'paid') AND ($mba_student_data)){
									$b_send_reminder = true;
								}
							}
						}
					}
				}
			}

			if ($b_allow_fine) {
				// if (!$o_sub_invoice_details_fined) {
				// 	print('<pre>');var_dump($a_sub_invoice_details_fined);exit;
				// }
				// $this->set_fine($o_invoice, $a_sub_invoice_details_fined, $o_sub_invoice_details_fined);
				$this->set_fine_flatted($o_invoice, $a_sub_invoice_details_fined);
			}

			return $b_send_reminder;
			
			// if ($b_reminder) {
			// 	// $this->send_reminder($o_invoice);
			// 	return true;
			// }else if($b_send_reminder){
			// 	// $this->send_reminder($o_invoice);
			// 	return true;
			// }else{
			// 	return false;
			// }
		}else{
			return false;
		}
	}
	
	private function set_environment($s_environment)
	{
		switch($s_environment)
		{
			case "development":
				$this->s_client_id = $this->s_development_client_id;
				$this->s_secret_key = $this->s_development_secret_key;
				$this->s_url = $this->s_development_url;
				break;
				
			case "production":
				$this->s_client_id = $this->s_production_client_id;
				$this->s_secret_key = $this->s_production_secret_key;
				$this->s_url = $this->s_production_url;
				break;
		}
	}
	
	public function create_test_billing_data()
	{
		$a_billing_data = array(
			'trx_amount' => '400000',
			'billing_type' => 'c',
			'customer_name' => 'DEVELOPER TEST',
			'virtual_account' => '8141010001190001',
			'description' => 'TEST',
			'datetime_expired' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d', time())."+ 1 day")),
			'customer_email' => 'bni.employee@company.ac.id'
		);
		$a_create_billing_result = $this->Bm->create_billing($a_billing_data);
	}
	
	public function create_test_data()
	{
/*
		$a_test_data = array(
			'trx_id' => '1388860572',
			'virtual_account' => '8141010001190001',
			'customer_name' => 'DEVELOPER TEST',
			'trx_amount' => '200000',
			'payment_amount' => '200000',
			'cumulative_payment_amount' => '200000',
			'payment_ntb' => '12345',
			'datetime_payment' => date('Y-m-d H:i:s', time()),
			'datetime_payment_iso8601' => date('Y-m-d\TH:i:sO', time())
		);
*/
		$s_json_string = '{"trx_id":"1766173754","virtual_account":"8310020861170501","customer_name":"REINHARD LIM","trx_amount":"5666667","payment_amount":"5666667","cumulative_payment_amount":"5666667","payment_ntb":"775705","datetime_payment":"2020-06-07 12:39:40","datetime_payment_iso8601":"2020-06-07T12:39:40+07:00"}';
		
		$this->set_environment('production');
		
		$a_test_data = (array)json_decode($s_json_string);
		
		$s_hash_data = $this->libapi->hash_data($a_test_data, $this->s_client_id, $this->s_secret_key);
		
		$a_post_data = array(
			'client_id' => $this->s_client_id,
			'data' => $s_hash_data
		);
		
		print json_encode($a_post_data);
		exit;
	}

	private function downpayment_payment_notification(
		$s_name,
		$s_email,
		$s_date_time_payment,
		$s_amount,
		$mba_parent_email
	)
	{
		$t_body = <<<TEXT
Dear {$s_name},

This email is to confirm that we have received your payment on {$s_date_time_payment} for amount of {$s_amount} for your down payment fee

Best Regards,
Finance Team

IULI – Eco Campus
MyRepublic Plaza
The Breeze, BSD City 15345
Indonesia
Email: employee@company.ac.id
TEXT;
		$a_email = $this->config->item('email');
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to('employee@company.ac.id');
		$this->email->subject("[IULI] Payment Confirmation");
		$this->email->message($t_body);
		$this->email->send();
	}
	
	private function tuition_fee_payment_notification(
		$s_name,
		$s_email,
		$s_date_time_payment,
		$s_amount,
		$mba_parent_email,
		$b_admission_bcc = false
	)
	{
		$t_body = <<<TEXT
Dear {$s_name},

This email is to confirm that we have received your payment on {$s_date_time_payment} for amount of {$s_amount} for your tuition fee

Best Regards,
Finance Team

IULI – Eco Campus
MyRepublic Plaza
The Breeze, BSD City 15345
Indonesia
Email: employee@company.ac.id
TEXT;
		$a_email = $this->config->item('email');
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to('employee@company.ac.id');
		$this->email->subject("[IULI] Tuition Fee Payment Confirmation");
		$this->email->message($t_body);
		$this->email->send();
	}
	
	private function enrollment_fee_payment_confirmation(
		$s_name,
		$s_email,
		$s_date_time_payment,
		$s_amount,
		$mba_parent_email
	)
	{
		$t_body = <<<TEXT
Dear {$s_name},

This email is to confirm that we have received your payment on {$s_date_time_payment} for amount of {$s_amount} for IULI’s Admission Process

Should you have inquiries regarding the registration process, please consult with us on email (employee@company.ac.id) or phone at +62 852 123 18000.

Best Regards,
Admission Team

IULI – Eco Campus
MyRepublic Plaza
The Breeze, BSD City 15345
Indonesia
Email: employee@company.ac.id



Kepada Yth. {$s_name},

Dengan email ini kami mengkonfirmasikan bahwa pembayaran Anda pada tanggal {$s_date_time_payment} dengan jumlah {$s_amount} untuk administrasi penerimaan mahasiswa baru IULI telah kami terima.

Bila Anda memiliki pertanyaan mengenai penerimaan mahasiswa baru ini, silahkan menghubungi kami melalui email employee@company.ac.id atau telepon ke 0852 123 18000.

Hormat Kami,
Tim Pendaftaran Mahasiswa Baru

IULI – Eco Campus
MyRepublic Plaza
The Breeze, BSD City 15345
Indonesia
Email: employee@company.ac.id
TEXT;
		$a_email = $this->config->item('email');
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to('employee@company.ac.id');
		$this->email->subject("[IULI] Online Registration Payment Confirmation");
		$this->email->message($t_body);
		$this->email->send();
	}
	
	private function handle_installment_payment($mbo_invoice_detail, $a_paid_billing)
	{
		$d_payment_amount = $a_paid_billing['payment_amount'];
		if($d_payment_amount == $mbo_invoice_detail->sub_invoice_details_amount_total){
			$a_update_sub_invoice_details_data = array(
				'sub_invoice_details_amount_paid' => $d_payment_amount,
				'sub_invoice_details_status' => 'paid',
				'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
			);
			$this->Im->update_sub_invoice_details($a_update_sub_invoice_details_data, array('sub_invoice_details_id' => $mbo_invoice_detail->sub_invoice_details_id));
			
			$d_total_invoice_amount = $mbo_invoice_detail->sub_invoice_amount_total;
			
			$d_total_amount_paid = $mbo_invoice_detail->sub_invoice_amount_paid;
			$d_total_amount_paid += $d_payment_amount;
			
			$d_accounts_receiveable = $d_total_invoice_amount - $d_payment_amount;
			$a_update_sub_invoice_data = array(
				'sub_invoice_amount_paid' => $d_total_amount_paid,
				'sub_invoice_amount_total' => $d_accounts_receiveable,
				'sub_invoice_datetime_paid_off' => $a_paid_billing['datetime_payment']
			);
			
			if($d_accounts_receiveable == '0'){
				$a_update_sub_invoice_data['sub_invoice_status'] = 'paid';
				$this->Im->update_sub_invoice($a_update_sub_invoice_data, array('invoice_id' => $mbo_invoice_detail->invoice_id));
				
				$a_update_invoice_data = array(
					'invoice_amount_paid' => $d_total_amount_paid,
					'invoice_amount_fined' => 0,
					'invoice_status' => 'paid',
					'invoice_datetime_paid_off' => $a_paid_billing['datetime_payment']
				);
				$this->Im->update_invoice($a_update_invoice_data, array('invoice_id' => $mbo_invoice_detail->invoice_id));
			}
			else{
				$this->Im->update_sub_invoice($a_update_sub_invoice_data, array('sub_invoice_id' => $mbo_invoice_detail->sub_invoice_id));
				$mbo_full_payment_invoice_data = $this->Im->get_full_payment_invoice_by_invoice_id($mbo_invoice_detail->invoice_id);
				if($mbo_full_payment_invoice_data){
					$d_total_amount_full_payment = $mbo_full_payment_invoice_data->sub_invoice_amount_total;
					$d_total_amount_full_payment -= $d_payment_amount;
					
					$d_total_amount_full_payment_fined = $mbo_full_payment_invoice_data->invoice_amount_fined;
					$d_new_total_amount_full_payment_fined = floatval($d_total_amount_full_payment_fined) - floatval($mbo_invoice_detail->sub_invoice_details_amount_fined);
					
					$s_new_full_payment_deadline = date('Y-m-d 23:59:59', strtotime($mbo_invoice_detail->sub_invoice_details_deadline."+1 month"));
					
					$a_update_sub_invoice_data_total = array(
						'sub_invoice_amount' => $d_total_amount_full_payment,
						'sub_invoice_amount_total' => $d_accounts_receiveable,
						// 'sub_invoice_amount_fined' => $d_new_total_amount_full_payment_fined
					);
					$this->Im->update_sub_invoice($a_update_sub_invoice_data_total, array('sub_invoice_id' => $mbo_full_payment_invoice_data->sub_invoice_id));

					$this->Im->update_invoice(['invoice_amount_fined' => $d_new_total_amount_full_payment_fined], ['invoice_id' => $mbo_invoice_detail->invoice_id]);
					
					$b_error_status = ($d_payment_amount == $mbo_invoice_detail->sub_invoice_details_amount_total) ? false : true;
					// $s_paid_status = ($d_payment_amount == $mbo_invoice_detail->sub_invoice_details_amount) ? 'paid' : 'default';
					$s_paid_status = ($d_total_amount_full_payment == '0') ? 'paid' : 'default';
					
					if($b_error_status){
						$this->email->from('employee@company.ac.id');
						$this->email->to(array('employee@company.ac.id'));
						$this->email->subject('ERROR INSTALLMENT HANDLER');
						$this->email->message(json_encode($mbo_invoice_detail));
						$this->email->send();
					}
					else{
						$a_update_sub_invoice_details_data = array(
							'sub_invoice_details_amount' => $d_total_amount_full_payment,
							'sub_invoice_details_amount_fined' => $d_new_total_amount_full_payment_fined,
							'sub_invoice_details_amount_total' => $d_total_amount_full_payment,
							'sub_invoice_details_deadline' => $s_new_full_payment_deadline,
							'sub_invoice_details_description' => $mbo_full_payment_invoice_data->sub_invoice_details_description,
							// 'sub_invoice_details_amount_paid' => $d_total_amount_paid,
							'sub_invoice_details_status' => $s_paid_status
						);
						$this->Im->update_sub_invoice_details($a_update_sub_invoice_details_data, array('sub_invoice_details_id' => $mbo_full_payment_invoice_data->sub_invoice_details_id));
						
						$a_update_billing = array(
							'trx_id' => $mbo_full_payment_invoice_data->trx_id,
							'trx_amount' => $d_total_amount_full_payment,
							'customer_name' => $mbo_full_payment_invoice_data->customer_name,
							'datetime_expired' => date('Y-m-d 23:59:59', strtotime($s_new_full_payment_deadline."+3 day")),
							'description' => $mbo_full_payment_invoice_data->sub_invoice_details_description
						);
						$this->Bm->update_billing($a_update_billing);
					}
				}
			}

		}
	}
	
	private function handle_full_payment($mbo_invoice_detail, $a_paid_billing)
	{
		$this->db->trans_start();
		$d_payment_amount = $a_paid_billing['payment_amount'];
		if(
			($d_payment_amount == $mbo_invoice_detail->sub_invoice_details_amount_total) AND 
			($d_payment_amount == $mbo_invoice_detail->sub_invoice_amount_total)
		)
		{
			$a_update_sub_invoice_details_data = array(
				'sub_invoice_details_amount_paid' => $d_payment_amount,
				'sub_invoice_details_status' => 'paid',
				'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
			);
			$this->Im->update_sub_invoice_details($a_update_sub_invoice_details_data, array('sub_invoice_details_id' => $mbo_invoice_detail->sub_invoice_details_id));
			
			$a_update_sub_invoice_data = array(
				'sub_invoice_amount_paid' => $d_payment_amount,
				'sub_invoice_status' => 'paid',
				'sub_invoice_datetime_paid_off' => $a_paid_billing['datetime_payment']
			);
			$this->Im->update_sub_invoice($a_update_sub_invoice_data, array('invoice_id' => $mbo_invoice_detail->invoice_id));
			
			$mbo_installment_payment_invoice_data = $this->Im->get_installment_payment_invoice_by_invoice_id($mbo_invoice_detail->invoice_id);
			if($mbo_installment_payment_invoice_data){
				foreach($mbo_installment_payment_invoice_data as $sub_invoice_details){
					$a_update_sub_invoice_details_data = array(
						// 'sub_invoice_details_amount_paid' => $sub_invoice_details->sub_invoice_details_amount,
						'sub_invoice_details_status' => 'paid',
						'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
					);
					$this->Im->update_sub_invoice_details($a_update_sub_invoice_details_data, array('sub_invoice_details_id' => $sub_invoice_details->sub_invoice_details_id));
				}
			}
			
			$a_update_invoice_data = array(
				'invoice_amount_paid' => $d_payment_amount,
				'invoice_status' => 'paid',
				'invoice_datetime_paid_off' => $a_paid_billing['datetime_payment']
			);
			$this->Im->update_invoice($a_update_invoice_data, array('invoice_id' => $mbo_invoice_detail->invoice_id));
			
			if($this->db->trans_status() === false){
				$this->db->trans_rollback();
				return false;
			}
			else{
				$this->db->trans_commit();
				return true;
			}
		}
		else{
			$this->db->trans_rollback();
			return false;
		}
	}

	public function handle_graduation_fee($mbo_invoice_detail, $a_paid_billing)
	{
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');
		
		$s_personal_data_id = $mbo_invoice_detail->personal_data_id;
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		
		$this->handle_full_payment($mbo_invoice_detail, $a_paid_billing);
		$s_trx_amount = "Rp. ". number_format($a_paid_billing['payment_amount'], 0, ',', '.') .",-";
		
		$o_family_data = $this->Fm->get_family_by_personal_data_id($s_personal_data_id);
		$mba_family_members = $this->Fm->get_family_members($o_family_data->family_id, array(
			'family_member_status != ' => 'child'
		));
		
		$mba_parent_email = false;
		if($mba_family_members){
			$mba_parent_email = array();
			foreach($mba_family_members as $family){
				array_push($mba_parent_email, $family->personal_data_email);
			}
		}
		
		$t_body = <<<TEXT
Dear {$mbo_personal_data->personal_data_name},
{$mbo_student_data->study_program_name} - {$mbo_student_data->academic_year_id}


{$mbo_invoice_detail->sub_invoice_details_description} payment confirmation.
Thank you for your payment on {$a_paid_billing['datetime_payment']} for amount of {$s_trx_amount}.

Regards,
Finance
TEXT;

		$a_email = $this->config->item('email');
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to('employee@company.ac.id');
		$this->email->subject("[IULI] Graduation Payment Confirmation");
		$this->email->message($t_body);
		$this->email->send();
	}

	public function handle_single_payment_fee($mbo_invoice_detail, $a_paid_billing)
	{
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');

		$s_personal_data_id = $mbo_invoice_detail->personal_data_id;
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);

		$mba_already_processing = $this->General->get_where('dt_sub_invoice_details', [
			'sub_invoice_details_va_number' => $a_paid_billing['virtual_account'],
			'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
		]);

		if (!$mba_already_processing) {
			switch($mbo_invoice_detail->sub_invoice_type)
			{
				case "full":
					$this->handle_full_payment($mbo_invoice_detail, $a_paid_billing);
					break;
					
				case "installment":
					$this->handle_installment_payment($mbo_invoice_detail, $a_paid_billing);
					break;
			}

		// if ($mbo_invoice_detail->virtual_account == '8310070801171003') {
		// 	print('<pre>');var_dump($mbo_invoice_detail);exit;
		// }else{
			$s_trx_amount = "Rp. ". number_format($a_paid_billing['payment_amount'], 0, ',', '.') .",-";

			$o_family_data = $this->Fm->get_family_by_personal_data_id($s_personal_data_id);
			$mba_family_members = $this->Fm->get_family_members($o_family_data->family_id, array(
				'family_member_status != ' => 'child'
			));
			
			$mba_parent_email = false;
			if($mba_family_members){
				$mba_parent_email = array();
				foreach($mba_family_members as $family){
					array_push($mba_parent_email, $family->personal_data_email);
				}
			}

			$t_body = <<<TEXT
Dear {$mbo_personal_data->personal_data_name},

This email is to confirm that we have received your payment on {$a_paid_billing['datetime_payment']} for amount of {$s_trx_amount} for your {$mbo_invoice_detail->sub_invoice_details_description} fee

Best Regards,
Finance Team

IULI – Eco Campus
MyRepublic Plaza
The Breeze, BSD City 15345
Indonesia
Island of Java, Indonesia
Email: employee@company.ac.id
TEXT;
			$s_email = (!is_null($mbo_student_data->student_alumni_email)) ? $mbo_student_data->student_alumni_email : $mbo_student_data->student_email;
			// $s_email = 'employee@company.ac.id';
			// print($s_email);
			$a_email = $this->config->item('email');
			$this->email->from('employee@company.ac.id', 'IULI Finance Team');
			$this->email->to('employee@company.ac.id');
			$this->email->subject("[IULI] Payment Confirmation");
			$this->email->message($t_body);
			$this->email->send();
		// }
		}
	}

	public function handle_short_semester_fee($mbo_invoice_detail, $a_paid_billing)
	{
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');
		
		$s_personal_data_id = $mbo_invoice_detail->personal_data_id;
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		
		$this->handle_full_payment($mbo_invoice_detail, $a_paid_billing);
		$s_trx_amount = "Rp. ". number_format($a_paid_billing['payment_amount'], 0, ',', '.') .",-";
		
		$o_family_data = $this->Fm->get_family_by_personal_data_id($s_personal_data_id);
		$mba_family_members = $this->Fm->get_family_members($o_family_data->family_id, array(
			'family_member_status != ' => 'child'
		));
		
		$mba_parent_email = false;
		if($mba_family_members){
			$mba_parent_email = array();
			foreach($mba_family_members as $family){
				array_push($mba_parent_email, $family->personal_data_email);
			}
		}
		
		$t_body = <<<TEXT
Dear {$mbo_personal_data->personal_data_name},
{$mbo_student_data->study_program_name} - {$mbo_student_data->academic_year_id}


{$mbo_invoice_detail->sub_invoice_details_description} payment confirmation.
Thank you for your payment on {$a_paid_billing['datetime_payment']} for amount of {$s_trx_amount}.

Regards,
Finance
TEXT;

		$a_email = $this->config->item('email');
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to('employee@company.ac.id');
		$this->email->subject("[IULI] Payment Confirmation");
		$this->email->message($t_body);
		$this->email->send();
	}
	
	public function handle_repetition_fee($mbo_invoice_detail, $a_paid_billing)
	{
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');
		
		$s_personal_data_id = $mbo_invoice_detail->personal_data_id;
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		
		$this->handle_full_payment($mbo_invoice_detail, $a_paid_billing);
		$s_trx_amount = "Rp. ". number_format($a_paid_billing['payment_amount'], 0, ',', '.') .",-";
		
		$o_family_data = $this->Fm->get_family_by_personal_data_id($s_personal_data_id);
		$mba_family_members = $this->Fm->get_family_members($o_family_data->family_id, array(
			'family_member_status != ' => 'child'
		));
		
		$mba_parent_email = false;
		if($mba_family_members){
			$mba_parent_email = array();
			foreach($mba_family_members as $family){
				array_push($mba_parent_email, $family->personal_data_email);
			}
		}
		
		$t_body = <<<TEXT
Dear {$mbo_personal_data->personal_data_name},
{$mbo_student_data->study_program_name} - {$mbo_student_data->academic_year_id}


{$mbo_invoice_detail->sub_invoice_details_description} payment confirmation.
Thank you for your payment on {$a_paid_billing['datetime_payment']} for amount of {$s_trx_amount}.

Regards,
Finance
TEXT;

		$a_email = $this->config->item('email');
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to('employee@company.ac.id');
		$this->email->subject("[IULI] Repetition Exam Payment Confirmation");
		$this->email->message($t_body);
		$this->email->send();
	}

	public function send_custom_tuition_fee_and_sgs($s_personal_data_id)
	{
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');

		$o_family_data = $this->Fm->get_family_by_personal_data_id($s_personal_data_id);
		$mba_family_members = $this->Fm->get_family_members($o_family_data->family_id, array(
			'family_member_status != ' => 'child'
		));
		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);

		$mba_parent_email = false;
		if($mba_family_members){
			$mba_parent_email = array();
			foreach($mba_family_members as $family){
				array_push($mba_parent_email, $family->personal_data_email);
			}
		}
		
		if(is_null($mbo_personal_data->personal_data_reference_code)){
			modules::run('personal_data/create_reference_code', $s_personal_data_id);
			
			$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
			$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
			
			modules::run('student/payment_confirmation_sgs', 
				$mbo_personal_data->personal_data_name, 
				$mbo_personal_data->personal_data_reference_code,
				$mbo_personal_data->personal_data_email,
				$mba_parent_email
			);

			$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
			$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
			
			$d_payment_amount = '17000000';
			$s_amount = "Rp. ".number_format($d_payment_amount, 0, ',', '.').",-";

			$this->tuition_fee_payment_notification(
				$mbo_personal_data->personal_data_name, 
				$mbo_student_data->student_email, 
				'2021-02-27 09:32:00', 
				$s_amount,
				$mba_parent_email,
				true
			);
			print('<pre>');
			var_dump($mba_family_members);exit;
		}else{
			print('kosong');
		}
	}

	public function handle_tuition_fee($mbo_invoice_detail, $a_paid_billing)
	{
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');
		$s_personal_data_id = $mbo_invoice_detail->personal_data_id;
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		$mba_already_processing = $this->General->get_where('dt_sub_invoice_details', [
			'sub_invoice_details_va_number' => $a_paid_billing['virtual_account'],
			'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
		]);

		if (!$mba_already_processing) {
			$b_admission_bcc = false;
			switch($mbo_invoice_detail->sub_invoice_type)
			{
				case "full":
					$this->handle_full_payment($mbo_invoice_detail, $a_paid_billing);
					break;
					
				case "installment":
					$this->handle_installment_payment($mbo_invoice_detail, $a_paid_billing);
					break;
			}
			
			if(is_null($mbo_student_data->student_email)){
				$b_admission_bcc = true;
				modules::run('student/create_student_email', $mbo_student_data);
				modules::run('student/create_student_portal', $mbo_student_data->student_id, $s_personal_data_id);
			}

			if ($mbo_invoice_detail->invoice_admission_reminder == 'yes') {
				$b_admission_bcc = true;
			}

			$o_family_data = $this->Fm->get_family_by_personal_data_id($s_personal_data_id);
			$mba_family_members = $this->Fm->get_family_members($o_family_data->family_id, array(
				'family_member_status != ' => 'child'
			));
			
			$mba_parent_email = false;
			if($mba_family_members){
				$mba_parent_email = array();
				foreach($mba_family_members as $family){
					array_push($mba_parent_email, $family->personal_data_email);
				}
			}
			
			if(is_null($mbo_personal_data->personal_data_reference_code)){
				modules::run('personal_data/create_reference_code', $s_personal_data_id);
				
				$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
				$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
				
				modules::run('student/payment_confirmation_sgs', 
					$mbo_personal_data->personal_data_name, 
					$mbo_personal_data->personal_data_reference_code,
					$mbo_personal_data->personal_data_email,
					$mba_parent_email
				);
			}
			
			
			$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
			$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
			
			$d_payment_amount = $a_paid_billing['payment_amount'];
			$s_amount = "Rp. ".number_format($d_payment_amount, 0, ',', '.').",-";
			$s_payment_type_code = substr($mbo_invoice_detail->virtual_account, 4, 2);

			// if ($mbo_invoice_detail->virtual_account == '8310070801171003') {
			// 	print($mbo_invoice_detail);
			// }else{
				if ($s_payment_type_code == '10') {
					$this->downpayment_payment_notification(
						$mbo_personal_data->personal_data_name, 
						$mbo_student_data->student_email, 
						$a_paid_billing['datetime_payment'], 
						$s_amount,
						$mba_parent_email
					);
					// print('email dp');
				}else{
					$this->tuition_fee_payment_notification(
						$mbo_personal_data->personal_data_name, 
						$mbo_student_data->student_email, 
						$a_paid_billing['datetime_payment'], 
						$s_amount,
						$mba_parent_email,
						$b_admission_bcc
					);
				}
			// }
		}
	}
	
	public function handle_enrollment_fee($mbo_invoice_detail, $a_paid_billing)
	{
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');
		$s_personal_data_id = $mbo_invoice_detail->personal_data_id;
		
		if($this->handle_full_payment($mbo_invoice_detail, $a_paid_billing)){
			
			$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
			
			$a_update_student_data = array(
				'student_status' => 'participant'
			);
			$this->Sm->update_student_data($a_update_student_data, $mbo_student_data->student_id);
			
			/**
			* handle sync data
			**/
			$a_token_config = $this->config->item('token')['pmb'];
			$s_site = $this->config->item('sites')['pmb'];
			$s_token = $a_token_config['access_token'];
			$s_secret_token = $a_token_config['secret_token'];
			$d_payment_amount = $a_paid_billing['payment_amount'];
			
			$a_portal_data = array(
				'table_name' => 'dt_student',
				'data' => array('student_status' => 'participant'),
				'clause' => array('student_id' => $mbo_student_data->student_id)
			);
			$a_sync_data = array($a_portal_data);
			$sync_data = array('sync_data' => $a_sync_data);
			$hashed_string = $this->libapi->hash_data($sync_data, $s_token, $s_secret_token);
			$post_data = json_encode(array(
				'access_token' => $s_token,
				'data' => $hashed_string
			));
			$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
			$s_amount = "Rp. ".number_format($d_payment_amount, 0, ',', '.').",-";
			
			$o_family_data = $this->Fm->get_family_by_personal_data_id($s_personal_data_id);
			$mba_family_members = $this->Fm->get_family_members($o_family_data->family_id, array(
				'family_member_status != ' => 'child'
			));
			$mba_parent_email = false;
			if($mba_family_members){
				$mba_parent_email = array();
				foreach($mba_family_members as $family){
					array_push($mba_parent_email, $family->personal_data_email);
				}
			}
			
			$this->enrollment_fee_payment_confirmation(
				$mbo_personal_data->personal_data_name, 
				$mbo_personal_data->personal_data_email, 
				$a_paid_billing['datetime_payment'], 
				$s_amount,
				$mba_parent_email
			);
			
			$url = $s_site.'api/portal/sync_all';
			$result = $this->libapi->post_data($url, $post_data);
			/**
			* handle sync data
			**/
			
			return true;
		}
	}

	public function view_failover($s_date)
	{
		$s_storefile_json = APPPATH."uploads/bni/bni-dev/bni_failover_$s_date.json";
		if (file_exists($s_storefile_json)) {
			$s_file = file_get_contents($s_storefile_json);

			print('<pre>');
			print_r(json_decode($s_file));
			// print('<pre>');
			// var_dump();
		}
		else {
			print('<h1>File not found!</h1>');
		}
	}

	public function failover($s_date, $i_num_of_day_before = 0)
	{
		if ($i_num_of_day_before > 0) {
			$s_date = date('Y_m_d', strtotime($s_date." -".$i_num_of_day_before." day"));
		}

		$s_storefile_json = APPPATH."uploads/bni/bni-dev/bni_failover_$s_date.json";
		if(file_exists($s_storefile_json)){
			$a_payment = json_decode(file_get_contents($s_storefile_json));

			// $p = 0;
			// print('<pre>');
			// 		var_dump($a_payment);exit;
			
			foreach($a_payment as $a_parsed_data){
				$a_parsed_data = (array)$a_parsed_data;

				if ($a_parsed_data['trx_id'] == '1253542728') {
					print('<pre>');
					var_dump($a_parsed_data);exit;
					
					$mbo_invoice_detail = $this->Im->get_invoice_detail_by_trx_id($a_parsed_data['trx_id']);
				
					if($mbo_invoice_detail){
						// $p++;
						$s_payment_type_code = substr($mbo_invoice_detail->virtual_account, 4, 2);
						if(($mbo_invoice_detail->sub_invoice_details_amount_paid != $a_parsed_data['payment_amount']) OR (is_null($mbo_invoice_detail->sub_invoice_details_datetime_paid_off))){
							switch($s_payment_type_code)
							{
								case "01":
									$this->handle_enrollment_fee($mbo_invoice_detail, $a_parsed_data);
									break;
									
								case "02":
									$this->handle_tuition_fee($mbo_invoice_detail, $a_parsed_data);
									break;
									
								case "03":
									$this->handle_repetition_fee($mbo_invoice_detail, $a_parsed_data);
									break;

								case "04":
									$this->handle_short_semester_fee($mbo_invoice_detail, $a_parsed_data);
									break;

								case "05":
									$this->handle_single_payment_fee($mbo_invoice_detail, $a_parsed_data);
									break;

								case "07":
									$this->handle_single_payment_fee($mbo_invoice_detail, $a_parsed_data);
									break;

								case "09":
									$this->handle_graduation_fee($mbo_invoice_detail, $a_parsed_data);
									break;

								case "10":
									$this->handle_tuition_fee($mbo_invoice_detail, $a_parsed_data);
									break;

								case "11":
									$this->handle_single_payment_fee($mbo_invoice_detail, $a_parsed_data);
									break;

								case "12":
									$this->handle_single_payment_fee($mbo_invoice_detail, $a_parsed_data);
									break;

								case "13":
									$this->handle_single_payment_fee($mbo_invoice_detail, $a_parsed_data);
									break;
							}
						}
					}else{
						$this->email->to(array('employee@company.ac.id'));
						$this->email->subject('ERROR TRX NOT FOUND');
						$this->email->message(json_encode($a_parsed_data));
						$this->email->send();
					}
					// sleep(5);
				}
				
				// exit;
			}

			// print($p.' = '.count($a_payment));
		}
		
	}

	public function bni_payment_notification()
	{
		$s_data = file_get_contents('php://input');
		
		$a_data_json = json_decode($s_data, true);
		
		if(array_key_exists($a_data_json['client_id'], $this->a_client_ids)){
			$this->set_environment($this->a_client_ids[$a_data_json['client_id']]);
			
			$a_parsed_data = $this->libapi->parse_data($a_data_json['data'], $this->s_client_id, $this->s_secret_key);
			// old
			$s_storefile = APPPATH."uploads/bni/bni-dev/bnicallback-response-".$this->a_client_ids[$a_data_json['client_id']].".txt";
			if(file_exists($s_storefile)){
				$fp = fopen($s_storefile, "a+");
				$s_string_data = json_encode($a_parsed_data)."\n";
				fwrite($fp, $s_string_data);
				fclose($fp);
			}
			else{
				$s_string_data = json_encode($a_parsed_data)."\n";	
				file_put_contents($s_storefile, $s_string_data);
			}
			
			// new
			// $s_storefile_json = APPPATH."uploads/bni/bni-dev/bni_failover_".date('Y_m_d', time()).".json";
			$s_storefile_json = APPPATH."uploads/bni/bni-dev/bni_failover_".date('Y_m_d', strtotime($a_parsed_data['datetime_payment'])).".json";
			
			if(!file_exists($s_storefile_json)){
				file_put_contents($s_storefile_json, json_encode([]));
			}
			
			$s_bni_failover_json = file_get_contents($s_storefile_json);
			$a_bni_failover = json_decode($s_bni_failover_json);
			
			array_push($a_bni_failover, $a_parsed_data);
			
			file_put_contents($s_storefile_json, json_encode($a_bni_failover));
			
			$mbo_invoice_detail = $this->Im->get_invoice_detail_by_trx_id($a_parsed_data['trx_id']);
			if($mbo_invoice_detail){
				$s_payment_type_code = substr($mbo_invoice_detail->virtual_account, 4, 2);
				if($mbo_invoice_detail){
					switch($s_payment_type_code)
					{
						case "01":
							$this->handle_enrollment_fee($mbo_invoice_detail, $a_parsed_data);
							break;
							
						case "02":
							$this->handle_tuition_fee($mbo_invoice_detail, $a_parsed_data);
							break;
							
						case "03":
							$this->handle_repetition_fee($mbo_invoice_detail, $a_parsed_data);
							break;
						
						case "04":
							$this->handle_short_semester_fee($mbo_invoice_detail, $a_parsed_data);
							break;

						case "05":
							$this->handle_single_payment_fee($mbo_invoice_detail, $a_parsed_data);
							break;
							
						case "07":
							$this->handle_single_payment_fee($mbo_invoice_detail, $a_parsed_data);
							break;
							
						case "09":
							$this->handle_graduation_fee($mbo_invoice_detail, $a_parsed_data);
							break;

						case "10":
							$this->handle_tuition_fee($mbo_invoice_detail, $a_parsed_data);
							break;

						case "11":
							$this->handle_single_payment_fee($mbo_invoice_detail, $a_parsed_data);
							break;
						
						case "12":
							$this->handle_single_payment_fee($mbo_invoice_detail, $a_parsed_data);
							break;

						case "13":
							$this->handle_single_payment_fee($mbo_invoice_detail, $a_parsed_data);
							break;
					}
					
	/*
					header('Content-Type: application/json');
					print json_encode($a_parsed_data);exit;
	*/
				}
				// else{
				// 	header('Content-Type: application/json');
				// 	$a_response = ['code' => 1, 'message' => 'mbo_invoice_detail', 'data' => $a_parsed_data];
				// 	print json_encode($a_response);exit;
				// }
			}
			else{
				$this->email->to(array('employee@company.ac.id'));
				$this->email->subject('ERROR TRX NOT FOUND');
				$this->email->message(json_encode($a_parsed_data));
				$this->email->send();
			}
			// else{
			// 	header('Content-Type: application/json');
			// 	$a_response = ['code' => 1, 'message' => 'mbo_invoice_detail', 'data' => $a_parsed_data];
			// 	print json_encode($a_response);exit;
			// }
		}
		// else{
		// 	header('Content-Type: application/json');
		// 	$a_response = ['code' => 1, 'message' => 'key not exist', 'data' => $s_data];
		// 	print json_encode($a_response);exit;
		// }
		// 
	}

	// public function bni_payment_notification_tester()
	// {
	// 	$s_data = file_get_contents('php://input');
		
	// 	$a_data_json = json_decode($s_data, true);
		
	// 	if(array_key_exists($a_data_json['client_id'], $this->a_client_ids)){
	// 		$this->set_environment($this->a_client_ids[$a_data_json['client_id']]);
			
	// 		$a_parsed_data = $this->libapi->parse_data($a_data_json['data'], $this->s_client_id, $this->s_secret_key);
	// 		// header('Content-Type: application/json');
	// 		// 	$a_response = $a_parsed_data;
	// 		// 	print json_encode($a_response);exit;
	// 		// old
	// 		$s_storefile = APPPATH."uploads/bni/bni-dev/bnicallback-response-".$this->a_client_ids[$a_data_json['client_id']].".txt";
	// 		if(file_exists($s_storefile)){
	// 			$fp = fopen($s_storefile, "a+");
	// 			$s_string_data = json_encode($a_parsed_data)."\n";
	// 			fwrite($fp, $s_string_data);
	// 			fclose($fp);
	// 		}
	// 		else{
	// 			$s_string_data = json_encode($a_parsed_data)."\n";	
	// 			file_put_contents($s_storefile, $s_string_data);
	// 		}
			
	// 		// new
	// 		// $s_storefile_json = APPPATH."uploads/bni/bni-dev/bni_failover_".date('Y_m_d', time()).".json";
	// 		$s_storefile_json = APPPATH."uploads/bni/bni-dev/bni_failover_".date('Y_m_d', strtotime($a_parsed_data['datetime_payment'])).".json";
			
	// 		if(!file_exists($s_storefile_json)){
	// 			file_put_contents($s_storefile_json, json_encode([]));
	// 		}
			
	// 		$s_bni_failover_json = file_get_contents($s_storefile_json);
	// 		$a_bni_failover = json_decode($s_bni_failover_json);
			
	// 		array_push($a_bni_failover, $a_parsed_data);
			
	// 		file_put_contents($s_storefile_json, json_encode($a_bni_failover));
			
	// 		$mbo_invoice_detail = $this->Im->get_invoice_detail_by_trx_id($a_parsed_data['trx_id']);
	// 		if($mbo_invoice_detail){
	// 			$s_payment_type_code = substr($mbo_invoice_detail->virtual_account, 4, 2);
	// 			if($mbo_invoice_detail){
					
					
	// 				header('Content-Type: application/json');
	// 				print json_encode($a_parsed_data);exit;
	// 			}
	// 			else{
	// 				header('Content-Type: application/json');
	// 				$a_response = ['code' => 2, 'message' => 'mbo_invoice_detail', 'data' => $a_parsed_data];
	// 				print json_encode($a_response);exit;
	// 			}
	// 		}
	// 		// else{
	// 		// 	$this->email->to(array('employee@company.ac.id'));
	// 		// 	$this->email->subject('ERROR TRX NOT FOUND');
	// 		// 	$this->email->message(json_encode($a_parsed_data));
	// 		// 	$this->email->send();
	// 		// }
	// 		else{
	// 			header('Content-Type: application/json');
	// 			$a_response = ['code' => 1, 'message' => 'mbo_invoice_detail', 'data' => $a_parsed_data];
	// 			print json_encode($a_response);exit;
	// 		}
	// 	}
	// 	else{
	// 		header('Content-Type: application/json');
	// 		$a_response = ['code' => 1, 'message' => 'key not exist', 'data' => $s_data];
	// 		print json_encode($a_response);exit;
	// 	}
	// 	// 
	// }
}