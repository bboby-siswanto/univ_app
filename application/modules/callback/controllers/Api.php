<?php
use PhpOffice\PhpSpreadsheet\IOFactory;	
class Api extends App_core
{
	private $s_production_client_id = '310';
	private $s_development_client_id = '141';
	private $s_client_id;
	private $a_client_ids = [310 => 'production', 141 => 'sandbox'];
	
	private $s_production_secret_key = 'ff7dd3a3ac49cfc2a3070a317b688c30';
	// private $s_development_secret_key = '2d9b7b2442a0dd722690b8c525a52915';
	private $s_development_secret_key = '08eb8a29c7efe7d879e85b6e15ecdc37';
	private $s_secret_key;
	
	private $s_production_url = 'https://api.bni-ecollection.com/';
	private $s_development_url = 'https://apibeta.bni-ecollection.com/';
	// $s_payment_simulator_bni = 'https://portalbeta.bni-ecollection.com/partner/simulator/payment-simulator/index';
	private $s_url;
	
	private $d_fine_amount = 500000;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('finance/Invoice_model', 'Im');
		$this->load->model('finance/Finance_model', 'Fim');
		$this->load->model('finance/Bni_model', 'Bm');
		$this->load->model('institution/Institution_model', 'Insm');
		$this->load->model('address/Address_model', 'Addrm');
		$this->load->model('partner/Partner_student_model', 'Psm');
		$this->load->model('Study_program_model', 'Spm');
		$this->load->model('personal_data/Family_model', 'Fam');
		$this->load->model('student/Student_model', 'Stm');
		
		$this->set_environment('production');
	}
	
	public function test_api()
	{
		print('oke');
		var_dump($this->input->post());
		exit;
	}

	public function push_reminder()
	{
		print('closed!');exit;
		$a_invoice_id = ['38a9faff-7408-4d1c-8295-b3ab1f433df3','590e5316-fd1d-4abb-9c0a-35e305872fca','494b49f9-0dc8-4c50-b4da-70342285bfe8','6890581e-2e9a-4aef-bacd-3c9e204bf938','418f7c30-59d8-427c-8472-471ac672b14e','3e1d97ba-de1c-4a3d-b731-fc9c4ed7ba5a','36e0d32c-ac7d-4f4c-89a2-8647868c348e','f2aaa107-4fec-49e9-bbfd-8750b1dcf544','4ab802f3-12f1-43b5-867d-db8aa1e46114','40690f41-a893-4ab5-adba-d7045cfbf579','530f90bb-ece2-4521-bed4-5685a77fbf4c','55d05675-c8b2-4825-8c20-34455b2ffafa','e3f241c0-18d6-42ac-a5c5-db7f74359376','0b97aa42-9a09-4ef2-8383-39b8b6544bfb','080994e4-1893-4330-aa74-5f5cfd6a312a','48de02f6-5a83-4479-a331-ebc0bee584e7','4818d5e9-c9c2-4f60-bf83-4ae4fb28f994','6ebf12d4-f3f7-4df7-8ce0-7c93339a4969','12261cac-7f47-4f0f-88dd-1d519fe14dd1','8a786ea7-9dd2-46d4-82d2-e1e0ad8bd95d'];
		if (count($a_invoice_id) > 0) {
			$x = 1;
			foreach ($a_invoice_id as $s_invoice_id) {
				$mba_unpaid_invoice = $this->Im->get_unpaid_invoice([
					'di.invoice_id' => $s_invoice_id
				], false);

				print($x++);
				if ($mba_unpaid_invoice) {
					$o_invoice = $mba_unpaid_invoice[0];
					// modules::run('devs/cheat_billing', $o_invoice->invoice_id, 'true');
					// $send_reminder = $send_reminder = $this->check_invoice($o_invoice);
					// print('<pre>');var_dump($send_reminder);
					// print('<br>');
					// if ($send_reminder) {
						$this->send_reminder($o_invoice);
						print('send to: '.$o_invoice->personal_data_name);
						print('<br>');
					// }
				}
				else {
					print('invoice not found!');exit;
				}
			}
		}
		else {
			print('no invoice_id');exit;
		}
	}

	public function tesrt()
	{
		$mba_unpaid_invoice = $this->Im->get_unpaid_invoice(false, false);
		print('<pre>');
		var_dump($mba_unpaid_invoice);exit;
	}

	public function send_custom_invoice()
	{
		print('closed!');exit;
		$this->load->model('student/Student_model', 'Stm');
		$a_student_number_send = ['11201607017','11201802010','11201901010','11202003007','11201608008','11202102013','11201807013','11202001018','11201908003','11201711007','11202101006','11202107002','11201908002','11202102006','11202008012','11201901009','11201601024','11202005002','11201807009','11201610004','11201701020','11201505006','11201811001','11201807008','11201701018'];
		$i = 0;
		foreach ($a_student_number_send as $s_student_number) {
			$i++;
			// $mba_student_data = $this->General->get_where('dt_student', ['student_number' => $s_student_number]);
			$mba_student_data = $this->Stm->get_student_filtered(['ds.student_number' => $s_student_number], ['active', 'graduated']);
			if (!$mba_student_data) {
				print('student number '.$s_student_number.' not found!');exit;
			}

			$mba_unpaid_invoice = $this->Im->get_unpaid_invoice([
				'di.personal_data_id' => $mba_student_data[0]->personal_data_id
			], 'yes');

			if ($mba_unpaid_invoice) {
				foreach($mba_unpaid_invoice as $o_invoice) {
					$invoice_details = $this->Im->get_invoice_list_detail([
						'di.invoice_id' => $o_invoice->invoice_id
					]);
					if (!$invoice_details) {
						print('details not found!'.$o_invoice->personal_data_name);
						// exit;
					}
					else if ($invoice_details[0]->payment_type_code == '02') {
						print('sending...'.$invoice_details[0]->semester_id.'<br>');
						$send = $this->send_reminder($o_invoice);
						print('<pre>');var_dump($send);
					}
				}
			}
			else {
				print('invoice not found!'.$mba_student_data[0]->personal_data_name);exit;
			}
			print($i.'.'.$mba_student_data[0]->student_email);
			print('<br>');
			// exit;
		}
	}

	public function set_invoice_installment()
	{
		$mba_unpaid_invoice = $this->Im->get_unpaid_invoice(['di.academic_year_id' => 2022, 'di.semester_type_id' => 2], false);
		$a_invoice_have_paid = [];
		foreach ($mba_unpaid_invoice as $o_invoice) {
			$mba_installment_invoice = $this->Im->get_invoice_installment($o_invoice->invoice_id);
			if ($mba_installment_invoice) {
				foreach ($mba_installment_invoice as $o_installment) {
					if ($o_installment->sub_invoice_details_amount_paid > 0) {
						if (!in_array($o_invoice->invoice_id, $a_invoice_have_paid)) {
							array_push($a_invoice_have_paid, $o_invoice->invoice_id);
							break;
						}
					}
				}
			}
		}

		print json_encode($a_invoice_have_paid);exit;
	}

	function check_billing_develop() {
		print('temporary closed! Under maintenance!');exit;
		// $mba_list_inactive_student = $this->Stm->get_student_filtered(false, ['inactive']);
		// $a_report_data = [];
		// if ($mba_list_inactive_student) {
		// 	foreach ($mba_list_inactive_student as $o_student) {
		// 		$mba_billing_fee = $this->Im->get_student_billing([
        //             'di.personal_data_id' => $o_student->personal_data_id
        //         ], 'fee.payment_type_code');
		// 		if ($mba_billing_fee) {
		// 			foreach ($mba_billing_fee as $o_billing_payment) {
		// 				if ($o_billing_payment->payment_type_code != '05') {
		// 					$s_billing_type = $o_billing_payment->payment_type_name;
		// 					$body = $this->get_reminder_billing_teks_student($o_student->student_id, $o_billing_payment->payment_type_code, true);
		// 					if (!empty($body)) {
		// 						$a_data = [
		// 							'personal_data_name' => $o_student->personal_data_name,
		// 							'academic_year_id' => $o_student->academic_year_id,
		// 							'finance_year_id' => $o_student->finance_year_id,
		// 							'study_program_abbreviation' => $o_student->study_program_abbreviation,
		// 							'invoice_description' => '#email_reminder'
		// 						];
		// 						array_push($a_report_data, $a_data);
		// 					}
		// 				}
		// 			}
		// 		}
		// 	}
		// }

		// if (count($a_report_data) > 0) {
		// 	$this->send_report($a_report_data);
		// }
	}

	public function check_billing()
	{
		$d_start_reminder = date('Y-m-25');
		$d_end_reminder = date('Y-m-11');
		$mba_list_student = $this->Stm->get_student_filtered(false, ['active', 'inactive', 'graduated']);
		$a_report_data = [];
		
		if ($mba_list_student) {
			$a_email = $this->config->item('email');
			$s_email_from = $a_email['finance']['payment'];
			$a_bcc_email = array('employee@company.ac.id');
			
			foreach ($mba_list_student as $o_student) {
				$mba_billing_fee = $this->Im->get_student_billing([
                    'di.personal_data_id' => $o_student->personal_data_id
                ], 'fee.payment_type_code');
				if ($mba_billing_fee) {
					// if ($o_student->personal_data_id == '12e89836-193b-42c3-af50-43a10582bc9d') {
					// 	print('<pre>');var_dump($mba_billing_fee);exit;
					// }
					foreach ($mba_billing_fee as $o_billing_payment) {
						if ($o_billing_payment->payment_type_code != '05') {
							$s_billing_type = $o_billing_payment->payment_type_name;
							$body = $this->get_reminder_billing_teks_student($o_student->student_id, $o_billing_payment->payment_type_code, true);
							$b_send_reminder = false;
							if ((intval(date('d')) >= intval(date('d', strtotime($d_start_reminder)))) OR (intval(date('d')) <= intval(date('d', strtotime($d_end_reminder))))) {
								$b_send_reminder = true;
							}

							// $a_student_send = ['87f68919-5175-4edb-8f0a-c43103651af0','3fb17179-4d5b-4734-87ae-f171a85212e5','e22df690-460c-4671-ab89-497103479dc0','9b8763ad-7966-4146-a220-46954e942e0b','6d21f8c4-4b3a-48cd-9c0d-da36d18a819c','f77ab29e-b265-4889-90fa-3695fe30e366','033b159d-482f-453b-8862-4f1958e348c8','ecffad0a-40e7-4ad8-843f-a2858ac89123','17ecfc4d-6d9e-4b7a-bc24-bda7e9719a29'];
							// $b_send_reminder = (in_array($o_student->student_id, $a_student_send)) ? true : false;

							if ((!empty($body)) AND ($b_send_reminder)) {
								// print($o_student->personal_data_name.'-'.$o_billing_payment->payment_type_code);
								// print('<br>');
								$mbo_family_data = $this->Fam->get_family_by_personal_data_id($o_student->personal_data_id);
								$mba_parent_email = false;
								if($mbo_family_data){
									$mba_family_members = $this->Fam->get_family_members($mbo_family_data->family_id, array(
										'family_member_status != ' => 'child'
									));
									if($mba_family_members){
										$mba_parent_email = (is_array($mba_parent_email)) ? $mba_parent_email : array();
										foreach($mba_family_members as $family){
											array_push($mba_parent_email, $family->personal_data_email);
										}
									}
								}

								if ($o_student->student_email == 'hose.winanda@stud.iuli.ac.id') {
									if ($mba_parent_email) {
										array_push($mba_parent_email, 'hosewinanda@gmail.com');
									}
								}
								else if ($o_student->student_email == 'andy.daniswara@stud.iuli.ac.id') {
									if ($mba_parent_email) {
										array_push($mba_parent_email, 'lia.naomi03@gmail.com');
									}
								}
								else if ($o_student->student_email == 'estrella.natalia@stud.iuli.ac.id') {
									if ($mba_parent_email) {
										$mba_parent_email = false;
									}
								}
								else if ($o_student->student_email == 'regan.leonard@stud.iuli.ac.id') {
									if (($mba_parent_email) AND (!in_array('congrestom@gmail.com', $mba_parent_email))) {
										array_push($mba_parent_email, 'congrestom@gmail.com');
									}
									else {
										$mba_parent_email = ['congrestom@gmail.com'];
									}
								}
								else if ($o_student->student_email == 'muhammad.mulyana@stud.iuli.ac.id') {
									if (($mba_parent_email) AND (!in_array('echa.fairley@gmail.com', $mba_parent_email))) {
										array_push($mba_parent_email, 'echa.fairley@gmail.com');
									}
									else {
										$mba_parent_email = ['echa.fairley@gmail.com'];
									}
								}

								$config['mailtype'] = 'html';
								$this->email->initialize($config);
								$this->email->from($s_email_from, 'IULI Reminder System');
								// $this->email->to('employee@company.ac.id');
								$this->email->to($o_student->student_email);

								if($mba_parent_email){
									$this->email->cc($mba_parent_email);
								}
								
								$this->email->subject('[REMINDER] '.$s_billing_type.' Invoice');
								$this->email->bcc($a_bcc_email);
								$this->email->reply_to($a_email['finance']['main']);

								$this->email->message($body);
								// $this->email->send();
								if(!$this->email->send()){
									$this->log_activity('Email did not sent');
									$this->log_activity('Error Message: '.$this->email->print_debugger());
								}
								
								$a_data = [
									'personal_data_name' => $o_student->personal_data_name,
									'academic_year_id' => $o_student->academic_year_id,
									'finance_year_id' => $o_student->finance_year_id,
									'study_program_abbreviation' => $o_student->study_program_abbreviation,
									'invoice_description' => '#email_reminder'
								];
								array_push($a_report_data, $a_data);
							}
						}
					}
				}
			}
		}

		if (count($a_report_data) > 0) {
			$this->send_report($a_report_data);
		}
	}

	function generate_invoice_file($s_student_id, $s_payment_type) {
		$mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
		$a_billing_data = $this->get_list_billing($s_student_id, $s_payment_type);
		if (($mba_student_data) AND (count($a_billing_data) > 0)) {
			$o_student = $mba_student_data[0];

			$this->a_page_data['student_data'] = $o_student;
			$this->a_page_data['billing'] = $a_billing_data['billing'];
			$this->a_page_data['fee_detail'] = $a_billing_data['fee_detail'];
			$this->a_page_data['va_number'] = $a_billing_data['va_number'];
			$d_min_payment = $a_billing_data['min_payment'];
			$a_payment_code = [$s_payment_type];
			
			if (in_array($s_payment_type, ['02', '05'])) {
				$a_payment_code = ['02', '05'];
				$mainbody = $this->load->view('finance/template_billing/tuition_fee', $this->a_page_data, true);
			}
			else {
				$mainbody = $this->load->view('finance/template_billing/academic_fee', $this->a_page_data, true);
			}

			if (!empty($mainbody)) {
				$mpdf = new \Mpdf\Mpdf([
					'default_font_size' => 9,
					'default_font' => 'sans_fonts',
					'mode' => 'utf-8',
					'format' => 'A4-P',
					'setAutoTopMargin' => 'stretch',
					'setAutoBottomMargin' => 'stretch'
				]);
				$s_header_file = '<img src="' . base_url() . 'assets/img/header_of_file.png"/>';
				$s_footer_file = '<img src="' . base_url() . 'assets/img/footer_of_letter.png"/>';
				$mpdf->SetHTMLHeader($s_header_file);
				$mpdf->SetHTMLFooter($s_footer_file);
				$mpdf->WriteHTML($mainbody);

				$mba_have_unpaid_invoice = $this->Im->get_invoice_by_deadline([
					'di.personal_data_id' => $o_student->personal_data_id,
					// 'fee.payment_type_code' => $s_payment_type
				], $a_payment_code);
				// print('<pre>');var_dump($mba_have_unpaid_invoice);exit;
				if ($mba_have_unpaid_invoice) {
					$mpdf->AddPage();
					foreach ($mba_have_unpaid_invoice as $key => $o_invoice) {
						$s_html = modules::run('callback/api/get_payment_method', $o_invoice->invoice_id, 'return', true);
						$mpdf->WriteHTML($s_html);
						if ($key < (count($mba_have_unpaid_invoice) - 1)) {
							$mpdf->AddPage();
						}
					}
				}

				$s_filename = 'Invoice_Billing_'.(str_replace(' ', '_', $o_student->personal_data_name)).'.pdf';
				$s_dir = STUDENTPATH.$o_student->personal_data_path.'invoice/';
				if(!file_exists($s_dir)){
					mkdir($s_dir, 0777, TRUE);
				}
				
				$mpdf->Output($s_dir.$s_filename, 'F');
				// print $s_html;
				$a_path_info = pathinfo($s_dir.$s_filename);
				$s_mime = mime_content_type($s_dir.$s_filename);
				header("Content-Type: ".$s_mime);
				// header('Content-Disposition: attachment; filename='.urlencode($s_filename));
				readfile( $s_dir.$s_filename );
				exit;
			}

			// 
		}
	}

	function set_fined_billing() {
		$mba_have_unpaid_invoice = $this->Im->get_invoice_by_deadline([
			'fee.payment_type_code' => '02'
		]);
		if ($mba_have_unpaid_invoice) {
			foreach ($mba_have_unpaid_invoice as $o_invoice) {
				$mba_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $o_invoice->personal_data_id, 'ds.student_status != ' => 'resign']);
				// print('<pre>');var_dump($o_invoice);exit;
				if (($mba_student_data) AND ($o_invoice->invoice_allow_fine == 'yes')) {
					// if ($o_invoice->invoice_id == 'a5b67f25-40bf-4cae-b43f-acf21b0829a4') {
						$mbo_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
						$mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
						$d_invoice_fine = 0;
						print('<h3>'.$mba_student_data[0]->personal_data_name.'</h3>');
						$s_va_number= '';

						if ($mba_invoice_installment) {
							$d_installment_unpaid = 0;
							$s_va_number = $mba_invoice_installment[0]->sub_invoice_details_va_number;
							foreach ($mba_invoice_installment as $o_installment) {
								if (date('Y-m-d') > (date('Y-m-d', strtotime($o_installment->sub_invoice_details_real_datetime_deadline)))) {
									if ($o_installment->sub_invoice_details_status != 'paid') {
										$d_invoice_fine += 500000;
										$d_installment_unpaid += $o_installment->sub_invoice_details_amount + 500000;
										if ((is_null($o_installment->sub_invoice_details_amount_fined)) OR ($o_installment->sub_invoice_details_amount_fined == 0)) {
											$this->Im->update_sub_invoice_details([
												'sub_invoice_details_amount_fined' => 500000,
												'sub_invoice_details_amount_total' => ($o_installment->sub_invoice_details_amount_total + 500000)
											], ['sub_invoice_details_id' => $o_installment->sub_invoice_details_id]);
											print('update fined sub_invoice_details_id:'.$o_installment->sub_invoice_details_id);
											print('<br>');
										}
									}
								}
							}

							$this->Im->update_sub_invoice([
								'sub_invoice_amount_total' => $d_installment_unpaid
							], array('invoice_id' => $mba_invoice_installment[0]->sub_invoice_id));
						}
						else if ($mbo_invoice_fullpayment) {
							$s_va_number = $mbo_invoice_fullpayment->sub_invoice_details_va_number;
						}

						if ($mbo_invoice_fullpayment) {
							$this->Im->update_sub_invoice_details([
								'sub_invoice_details_amount_fined' => $d_invoice_fine,
								'sub_invoice_details_amount_total' => ($mbo_invoice_fullpayment->sub_invoice_details_amount + $d_invoice_fine)
							], ['sub_invoice_details_id' => $mbo_invoice_fullpayment->sub_invoice_details_id]);

							$this->Im->update_sub_invoice([
								'sub_invoice_amount_total' => $mbo_invoice_fullpayment->sub_invoice_details_amount + $d_invoice_fine
							], array('invoice_id' => $mbo_invoice_fullpayment->sub_invoice_id));
						}

						$check_va = $this->Bm->inquiry_billing($trx_id, true);
						$d_min_payment = $this->get_minimum_payment($s_va_number);
						if (($check_va) AND (!array_key_exists('status', $check_va))) {
							$s_va_status = $check_va['va_status'];
							$d_bni_amount = $check_va['trx_amount'];
							if ($d_min_payment > 1000) {
								$a_update_billing = array(
									'trx_id' => $check_va['trx_id'],
									'trx_amount' => $d_min_payment,
									'customer_name' => $check_va['customer_name'],
									'datetime_expired' => $check_va['datetime_expired'],
									'description' => $check_va['description'],
									'customer_email' => 'bni.employee@company.ac.id'
								);
								
								$this->Bm->update_billing($a_update_billing);
							}
						}
						// print('<pre>');var_dump($d_invoice_fine);exit;
					// }
				}
				
				// exit;
			}
		}
	}

	function get_payment_method($s_invoice_id, $s_get_method = 'return', $req_style = false) {
		$s_body = '';
		$current_time = date('Y-m-d H:i:s');
		$b_has_installment = false;
		$mba_invoice_data = $this->Im->get_invoice_list_detail(['di.invoice_id' => $s_invoice_id]);
		if ($mba_invoice_data) {
			$mba_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $mba_invoice_data[0]->personal_data_id, 'student_status != ' => 'resign']);
			if ($mba_student_data) {
				$o_student = $mba_student_data[0];
				$o_invoice = $mba_invoice_data[0];

				$mba_invoice_detail = $this->get_list_billing($o_student->student_id, $o_invoice->payment_type_code, $s_invoice_id);
				// print('<pre>');var_dump($mba_invoice_detail);
				
				$mbo_invoice_full_payment = $this->Im->get_invoice_full_payment($s_invoice_id);
				$mba_invoice_installment = $this->Im->get_invoice_installment($s_invoice_id);
				$payment_due_date = ($mbo_invoice_full_payment) ? $mbo_invoice_full_payment->sub_invoice_details_real_datetime_deadline : $mba_invoice_installment[0]->sub_invoice_details_real_datetime_deadline;
				
				if ($current_time > date('Y-m-d H:i:s', strtotime($payment_due_date))) {
					if ($o_invoice->payment_type_code == '02') {
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

				$this->a_page_data['req_style'] = $req_style;
				$this->a_page_data['student_data'] = ($mba_student_data) ? $mba_student_data[0] : false;
				$this->a_page_data['invoice_data'] = $o_invoice;
				$this->a_page_data['billing_detail'] = $mba_invoice_detail;
				$this->a_page_data['fullpayment_method'] = $mbo_invoice_full_payment;
				$this->a_page_data['installment_method'] = $mba_invoice_installment;
				$this->a_page_data['has_installment'] = $b_has_installment;
				$s_body = $this->load->view('finance/form/payment_method', $this->a_page_data, true);
			}
		}
		
		if ($s_get_method == 'ajax') {
			print json_encode(['body' => $s_body]);
		}
		else if ($s_get_method = 'print') {
			print($s_body);
		}
		else {
			return $s_body;
		}
		// print($s_body);
		// $this->a_page_data['body'] = $s_body;
		// $this->load->view('layout', $this->a_page_data);
	}

	function get_reminder_billing_teks_student($s_student_id, $s_payment = '02', $b_is_callback = false) {
		$mba_student_data = $this->Stm->get_student_filtered([
			'ds.student_id' => $s_student_id
		]);
		$a_special_min = [];
		// if ($s_payment == '02') {
		// 	$a_special_min = [
		// 		'9ed88b0b-a31c-461d-992c-a0a9bceea6ae' => '4500000', //Faizatul Rizki (IBA/2020) 
		// 		'3fb17179-4d5b-4734-87ae-f171a85212e5' => '4500000', //ASWIN ANDIKA PUTRA (IBA/2020) 
		// 	];
		// }
		
		$current_time = date('Y-m-d H:i:s');
		$body = '';
		if ($mba_student_data) {
			$o_student = $mba_student_data[0];
			$a_billing_data = $this->get_list_billing($s_student_id, $s_payment);

			if (count($a_billing_data) > 0) {
				$this->a_page_data['student_data'] = $o_student;
				$this->a_page_data['billing'] = $a_billing_data['billing'];
				$this->a_page_data['fee_detail'] = $a_billing_data['fee_detail'];
				$this->a_page_data['va_number'] = $a_billing_data['va_number'];
				$d_min_payment = $a_billing_data['min_payment'];
				
				if ($s_payment == '02') {
					$body = $this->load->view('finance/template_billing/tuition_fee', $this->a_page_data, true);
				}
				else {
					$body = $this->load->view('finance/template_billing/academic_fee', $this->a_page_data, true);
				}
				
				$mba_have_unpaid_invoice = $this->Im->get_invoice_by_deadline([
					'di.personal_data_id' => $o_student->personal_data_id,
					'fee.payment_type_code' => $s_payment
				]);
				// print('<pre>');var_dump($mba_have_unpaid_invoice);exit;
				if ($mba_have_unpaid_invoice) {
					$body .= '<p><hr><hr></p>';
					foreach ($mba_have_unpaid_invoice as $key => $o_invoice) {
						// $s_attachment = $this->get_payment_method($o_invoice->invoice_id, 'return', true);
						// print('<pre>');var_dump($s_attachment);exit;
						$s_attachment = modules::run('callback/api/get_payment_method', $o_invoice->invoice_id, 'return', true);
						$body .= $s_attachment;
						if ($key < (count($mba_have_unpaid_invoice) - 1)) {
							$body .= '<p><hr><hr></p>';
						}
					}
				}

				if ($b_is_callback) {
					$b_allow_reminder = ($d_min_payment > 10000) ? true : false;
					// if (in_array($o_student->student_status, ['active'])) {
					// 	$b_allow_reminder = false;
					// 	if ($d_min_payment > 10000) {
					// 		$b_allow_reminder = true;
					// 	}
					// }
					$body = ($b_allow_reminder) ? $body : '';
				}
			}
		}

		return $body;
	}

	function test_biling() {
		$mba_data = $this->get_list_billing('87144876-f989-427d-a746-e572fa2e4593', '09');
		print('<pre>');var_dump($mba_data);exit;
	}

	function get_list_billing($s_student_id, $s_payment = '02', $s_invoice_id = false) {
		$mba_student_data = $this->Stm->get_student_filtered([
			'ds.student_id' => $s_student_id
		]);
		$a_special_min = [];
		
		$current_time = date('Y-m-d H:i:s');
		$return_data = [];
		if ($mba_student_data) {
			$o_student = $mba_student_data[0];
			$a_payment_type_get = (in_array($s_payment, ['02', '05'])) ? ['02', '05'] : [$s_payment];
			$a_billing_filter = [
				'di.personal_data_id' => $o_student->personal_data_id
			];
			if ($s_invoice_id) {
				$a_billing_filter['di.invoice_id'] = $s_invoice_id;
			}
			$mba_have_unpaid_invoice = $this->Im->get_invoice_by_deadline($a_billing_filter, $a_payment_type_get);
			$s_trx_id = false;

			if ($mba_have_unpaid_invoice) {
				$d_total_amount = 0;
				$d_total_fined = 0;
				$d_total_paid = 0;
				$a_fee_details = [];
				$s_payment_type_code = (in_array($s_payment, ['02', '05'])) ? '02' : $s_payment;
				// $s_va_number = '';
				$s_va_number = $this->Bm->generate_va_number(
					$s_payment_type_code,
					'student', 
					$o_student->student_number, 
					$o_student->finance_year_id,
					$o_student->program_id
				);
				$s_deadline = '';

				foreach ($mba_have_unpaid_invoice as $o_invoice) {
					// if (in_array($o_invoice->payment_type_code, ['02', '05'])) {
					$d_invoice_amount = 0;
					$d_invoice_fined = 0;
					$d_invoice_paid = 0;
					$mbo_invoice_fullpayment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
					$mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);

					$payment_due_date = ($mbo_invoice_fullpayment) ? $mbo_invoice_fullpayment->sub_invoice_details_real_datetime_deadline : $mba_invoice_installment[0]->sub_invoice_details_real_datetime_deadline;
					$s_trx_id = (($mbo_invoice_fullpayment) AND (!is_null($mbo_invoice_fullpayment->trx_id))) ? $mbo_invoice_fullpayment->trx_id : $s_trx_id;
					// $s_deadline = $payment_due_date;
					$b_has_installment = false;
					
					$fee_desc = $o_invoice->invoice_description;
					$fee_alt = (!is_null($o_invoice->fee_alt_description)) ? '<br><small>('.$o_invoice->fee_alt_description.')</small>' : '';

					if ($current_time > date('Y-m-d H:i:s', strtotime($payment_due_date))) {
						if ($o_invoice->payment_type_code == '02') {
							if ($mba_invoice_installment) {
								$b_has_installment = true;
							}
						}
					}

					if (!$mba_invoice_installment) {
						$b_has_installment = false;
					}
					else if (($mba_invoice_installment) AND (!$b_has_installment)) {
						foreach ($mba_invoice_installment as $o_installment) {
							if ($o_installment->sub_invoice_details_amount_paid > 0) {
								$b_has_installment = true;
							}
						}
					}

					$billing_mode = '';
					if (!$b_has_installment) {
						// yang ditagih metode fullpayment
						$d_invoice_amount += $mbo_invoice_fullpayment->sub_invoice_details_amount;
						$d_invoice_paid += $mbo_invoice_fullpayment->sub_invoice_details_amount_paid;
						$d_invoice_fined += $mbo_invoice_fullpayment->sub_invoice_details_amount_fined;
						$s_deadline = $mbo_invoice_fullpayment->sub_invoice_details_deadline;
						$billing_mode = 'full payment';
					}
					else if ($mba_invoice_installment) {
						// yang ditagih metode installment
						$billing_mode = 'installment';
						foreach ($mba_invoice_installment as $o_installment) {
							$d_invoice_amount += $o_installment->sub_invoice_details_amount;
							$d_invoice_paid += $o_installment->sub_invoice_details_amount_paid;
							$d_invoice_fined += $o_installment->sub_invoice_details_amount_fined;
							// $s_deadline = date('Y-m-d', strtotime($o_installment->sub_invoice_details_real_datetime_deadline));
							// $s_date_deadline = date('d', strtotime($s_deadline));
							if ($o_installment->sub_invoice_details_status != 'paid') {
								if (date('Y-m-d') <= date('Y-m-d', strtotime($o_installment->sub_invoice_details_real_datetime_deadline))) {
									if (empty($s_deadline)) {
										$s_deadline = date('Y-m-d', strtotime($o_installment->sub_invoice_details_real_datetime_deadline));
									}
								}

								if (!is_null($o_installment->trx_id)) {
									$s_trx_id = $o_installment->trx_id;
								}
							}
						}
					}

					if (empty($s_deadline)) {
						$s_deadline = date('Y-m-d');
					}

					$d_total_amount += $d_invoice_amount;
					$d_total_paid += $d_invoice_paid;
					$d_total_fined += $d_invoice_fined;

					$d_amount_unpaid = $d_invoice_amount + $d_invoice_fined - $d_invoice_paid;
					$fee_desc .= " (IDR ".number_format($d_amount_unpaid, 0, ',', '.').",-)";
					$fee_desc .= $fee_alt;
					array_push($a_fee_details, $fee_desc);
					// }
				}

				$total_billed = $d_total_amount + $d_total_fined - $d_total_paid;
				if (!empty($s_va_number)) {
					$d_min_payment = (array_key_exists($o_student->student_id, $a_special_min)) ? $a_special_min[$o_student->student_id] : $this->get_minimum_payment($s_va_number);
					$s_va_status = 2;
					$d_bni_amount = 0;
					$check_va = false;
					$trx_id = null;
					$billing_desc = $o_invoice->fee_description.' '.$o_student->personal_data_name;
					if ($s_trx_id) {
						$mba_trx_data = $this->General->get_where('bni_billing', ['trx_id' => $s_trx_id]);
					}
					else {
						$mba_trx_data = $this->General->get_where('bni_billing', [
							'virtual_account' => $s_va_number,
							'customer_name != ' => 'CANCEL PAYMENT',
							// 'datetime_payment' => NULL
						], [
							'datetime_payment' => 'ASC'
						]);
					}
					
					if ($mba_trx_data) {
						$trx_id = $mba_trx_data[0]->trx_id;
						$check_va = $this->Bm->inquiry_billing($trx_id, true);
						if (($check_va) AND (!array_key_exists('status', $check_va))) {
							$s_va_status = $check_va['va_status'];
							$d_bni_amount = $check_va['trx_amount'];
							if ($s_va_status != 2) {
								if ($d_min_payment < $check_va['trx_amount']) {
									// if ($check_va['billing_type'] != 'o') {
										$s_va_status = 3;
									// }
								}
								else if ($d_min_payment > $check_va['trx_amount']) {
									$s_va_status = 4;
								}
							}
						}
					}
					
					$return_data['billing'] = [
						'total_amount' => $d_total_amount,
						'total_fined' => $d_total_fined,
						'total_paid' => $d_total_paid,
						'total_billed' => $total_billed,
						'min_payment' => $d_min_payment,
						'deadline' => $s_deadline,
						'billing_mode' => $billing_mode
					];
					$return_data['trx_id'] = $trx_id;
					$return_data['trx_data'] = $mba_trx_data[0];
					$return_data['check_va'] = $check_va;
					$return_data['fee_detail'] = $a_fee_details;
					$return_data['va_status'] = $s_va_status;
					$return_data['va_number'] = $s_va_number;
					$return_data['min_payment'] = $d_min_payment;
					$return_data['bni_amount'] = $d_bni_amount;
					$return_data['billing_description'] = $billing_desc;
				}
			}
		}

		return $return_data;
	}

	public function old_check_billing()
	{
		print('temporary closed! Under maintenance!');exit;
		// $mba_unpaid_invoice = $this->Im->get_unpaid_invoice(['di.invoice_id' => 'b15ffc45-7af6-457e-a11d-70724bcd93ae']);
		// $mba_unpaid_invoice = $this->Im->get_unpaid_invoice(['di.academic_year_id' => 2022, 'di.semester_type_id' => 2], false);
		$mba_unpaid_invoice = $this->Im->get_unpaid_invoice(false, false);
		// print('<pre>');var_dump($mba_unpaid_invoice);exit;
		// $s_json_string = '{"invoice_id":"0d882da4-612b-4373-bac1-2ca66c54fd32","personal_data_id":"7f504d5f-a493-4a66-9cd4-4d8f7dbeae2d","invoice_number":"INV-19081402","invoice_amount_paid":"0","invoice_amount_fined":"0","invoice_fined_count":"0","invoice_description":"Scholarship Tuition Fee - Batch 2019","invoice_allow_fine":"yes","invoice_allow_reminder":"yes","invoice_status":"created","invoice_fine_status":"default","invoice_datetime_paid_off":null,"date_added":"2019-08-14 11:27:55","timestamp":"2019-08-14 11:33:23","country_of_birth":"9bb722f5-8b22-11e9-973e-52540001273f","citizenship_id":"9bb722f5-8b22-11e9-973e-52540001273f","religion_id":"53b17ff0-e4c0-4fc9-8735-bbb8c7054048","ocupation_id":null,"personal_data_name":"DAVE TRIAND ANGGORO","personal_data_email":"davetriandanggoro@gmail.com","personal_data_phone":"75675766","personal_data_cellular":"089653515157","personal_data_id_card_number":"000000000","personal_data_id_card_type":"national_id","personal_data_place_of_birth":"TANGERANG","personal_data_date_of_birth":"2001-10-12","personal_data_gender":"M","personal_data_nationality":"WNI","personal_data_marital_status":"single","personal_data_mother_maiden_name":null,"personal_data_password":null,"personal_data_password_token":"b1cd5dea00238c403068e1069c8f035d","personal_data_password_token_expired":null,"personal_data_email_confirmation":"no","personal_data_email_confirmation_token":null,"personal_data_reference_code":null,"portal_status":"open","pmb_sync":"1","portal_id":"0"}';
		// $o_invoice = json_decode($s_json_string);
		// $a_student_number_late = ['11201607008','11201601024','11201602002','11201602031','11201801002','11201803007','11201908002','11201907013','11201901010','11201902005','11202008012','11202007010','11202007013','11202102012'];
		// $a_student_graduate_late = ['11201502022','11201607006','11201704006','11201608008','11201607017','11201701014'];
		$a_student_number_late = [];
		$a_student_graduate_late = [];

		$a_report_data = [];
		if($mba_unpaid_invoice){
			$x =0;
			foreach($mba_unpaid_invoice as $o_invoice){
				$mba_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $o_invoice->personal_data_id]);
				$mba_student_prodi_faculty_data = false;
				if (($mba_student_data) AND (!is_null($mba_student_data[0]->study_program_id))) {
					$mba_student_prodi_faculty_data = $this->Spm->get_study_program($mba_student_data[0]->study_program_id);
				}

				$mba_hod_data = false;
				$mba_dean_data = false;
				if ($mba_student_prodi_faculty_data) {
					$mba_hod_data = $this->General->get_where('dt_employee', ['personal_data_id' => $mba_student_prodi_faculty_data[0]->head_of_study_program_id]);
					$mba_dean_data = $this->General->get_where('dt_employee', ['personal_data_id' => $mba_student_prodi_faculty_data[0]->deans_id]);
				}

				$b_has_send = false;
				if (($mba_student_data) AND (!is_null($mba_student_data[0]->student_number))) {
					if ((in_array($mba_student_data[0]->student_number, $a_student_number_late)) OR (in_array($mba_student_data[0]->student_number, $a_student_graduate_late))) {
						$mba_invoice_details_data = $this->Im->get_invoice_data([
							'di.invoice_id' => $o_invoice->invoice_id
						]);

						if ($mba_invoice_details_data) {
							$s_payment_type_code = substr($mba_invoice_details_data[0]->sub_invoice_details_va_number, 4, 2);
							if ($s_payment_type_code == '02') {
								$a_emailcc = false;
								// print($o_invoice->personal_data_name.':<br>');
								// if ($mba_invoice_details_data) {
								// 	foreach ($mba_invoice_details_data as $o_sub_invoice_details) {
								// 		if ($o_sub_invoice_details->sub_invoice_details_status != 'paid') {
								// 			$s_sub_invoice_details_deadline = date('Y-m-d H:i:s', strtotime($o_sub_invoice_details->sub_invoice_details_deadline));
								// 			$i_installment = substr($o_sub_invoice_details->sub_invoice_details_va_number, 8, 1);
								// 			switch ($i_installment) {
								// 				case '0':
								// 					$s_new_sub_invoice_details_deadline = date('2022-12-31 23:59:59');
								// 					break;

								// 				case '1':
								// 					$s_new_sub_invoice_details_deadline = date('2022-12-31 23:59:59');
								// 					break;

								// 				case '2':
								// 					$s_new_sub_invoice_details_deadline = date('2022-12-31 23:59:59');
								// 					break;

								// 				case '3':
								// 					$s_new_sub_invoice_details_deadline = date('2022-12-31 23:59:59');
								// 					break;

								// 				case '4':
								// 					$s_new_sub_invoice_details_deadline = date('2022-12-31 23:59:59');
								// 					break;

								// 				case '5':
								// 					$s_new_sub_invoice_details_deadline = date('2022-12-31 23:59:59');
								// 					break;

								// 				case '6':
								// 					$s_new_sub_invoice_details_deadline = date('2022-12-31 23:59:59');
								// 					// if (($o_invoice->academic_year_id == '2022') AND ($o_invoice->semester_type_id == '1')) {
								// 					// 	$s_new_sub_invoice_details_deadline = date('2022-12-10 23:59:59');
								// 					// }
								// 					break;
												
								// 				default:
								// 					$s_new_sub_invoice_details_deadline = $s_sub_invoice_details_deadline;
								// 					break;
								// 			}

								// 			$a_update_data = [
								// 				'sub_invoice_details_deadline' => $s_new_sub_invoice_details_deadline
								// 			];
								// 			$this->Im->update_sub_invoice_details($a_update_data, ['sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id]);

								// 			if (!is_null($o_sub_invoice_details->trx_id)) {
								// 				$o_bni_data = $this->Bm->get_data_by_trx_id($o_sub_invoice_details->trx_id);
								// 				$a_update_billing = array(
								// 					'trx_id' => $o_sub_invoice_details->trx_id,
								// 					'trx_amount' => $o_sub_invoice_details->sub_invoice_details_amount_total,
								// 					'customer_name' => $o_bni_data->customer_name,
								// 					'datetime_expired' => date('Y-m-d 23:59:59', strtotime($s_new_sub_invoice_details_deadline)),
								// 					'description' => $o_sub_invoice_details->sub_invoice_details_description,
								// 					'customer_email' => 'bni.employee@company.ac.id'
								// 				);
												
								// 				$b_update = $this->Bm->update_billing($a_update_billing);
								// 				if ((isset($b_update['status'])) AND ($b_update['status'] == '103')) {
								// 					$b_update = modules::run('finance/invoice/reactivate_billing', $o_sub_invoice_details->trx_id);
								// 				}
								// 				// print('<pre>');var_dump($b_update);exit;
								// 			}

								// 			print('installment '.$i_installment.' to '.$s_new_sub_invoice_details_deadline.'<br>');
								// 			// exit;
								// 		}
								// 	}
								// }

								if ($mba_hod_data) {
									$a_emailcc = (is_array($a_emailcc)) ? array_push($a_emailcc, $mba_hod_data[0]->employee_email) : [$mba_hod_data[0]->employee_email];
									if (is_array($a_emailcc)) {
										if (!in_array($mba_hod_data[0]->employee_email, $a_emailcc)) {
											array_push($a_emailcc, $mba_hod_data[0]->employee_email);
										}
									}
									else {
										$a_emailcc = [$mba_hod_data[0]->employee_email];
									}
								}
								
								if ($mba_dean_data) {
									if (is_array($a_emailcc)) {
										if (!in_array($mba_dean_data[0]->employee_email, $a_emailcc)) {
											array_push($a_emailcc, $mba_dean_data[0]->employee_email);
										}
									}
									else {
										$a_emailcc = [$mba_dean_data[0]->employee_email];
									}
								}
			
								if (!is_null($o_invoice->personal_data_email)) {
									if (is_array($a_emailcc)) {
										if (!in_array($o_invoice->personal_data_email, $a_emailcc)) {
											array_push($a_emailcc, $o_invoice->personal_data_email);
										}
									}
									else {
										$a_emailcc = [$o_invoice->personal_data_email];
									}
								}
			
								// print('<pre>');var_dump($a_emailcc);
								// print('send to: '.$o_invoice->personal_data_name);
								// print('<br>');
								// $x++;
								
								$b_has_send = true;
								$this->send_reminder($o_invoice, $a_emailcc);

								$mba_invoice_details = $this->Im->get_invoice_details([
									'did.invoice_id' => $o_invoice->invoice_id,
									'df.fee_amount_type' => 'main'
								]);

								$mba_prodi_data = ($mba_invoice_details) ? $this->General->get_where('ref_study_program', ['study_program_id' => $mba_invoice_details[0]->study_program_id]) : false;
								$a_data = [
									'personal_data_name' => $o_invoice->personal_data_name,
									'academic_year_id' => ($mba_student_data) ? $mba_student_data[0]->academic_year_id : '',
									'finance_year_id' => ($mba_student_data) ? $mba_student_data[0]->finance_year_id : '',
									'study_program_abbreviation' => ($mba_prodi_data) ? $mba_prodi_data[0]->study_program_abbreviation : '',
									'invoice_description' => $o_invoice->invoice_description
								];
								array_push($a_report_data, $a_data);
								// exit;
							}
						}
					}
				}

				if (!$b_has_send) {
					$send_reminder = $this->check_invoice($o_invoice);
					if ($send_reminder) {
						// $x++;
						$this->send_reminder($o_invoice);
						// print('send to: '.$o_invoice->personal_data_name);
						// print('<br>');

						$mba_invoice_details = $this->Im->get_invoice_details([
							'did.invoice_id' => $o_invoice->invoice_id,
							'df.fee_amount_type' => 'main'
						]);

						$mba_prodi_data = ($mba_invoice_details) ? $this->General->get_where('ref_study_program', ['study_program_id' => $mba_invoice_details[0]->study_program_id]) : false;
						$a_data = [
							'personal_data_name' => $o_invoice->personal_data_name,
							'academic_year_id' => ($mba_student_data) ? $mba_student_data[0]->academic_year_id : '',
							'finance_year_id' => ($mba_student_data) ? $mba_student_data[0]->finance_year_id : '',
							'study_program_abbreviation' => ($mba_prodi_data) ? $mba_prodi_data[0]->study_program_abbreviation : '',
							'invoice_description' => $o_invoice->invoice_description
						];
						array_push($a_report_data, $a_data);
					}
				}
			}
		}

		if (count($a_report_data) > 0) {
			$this->send_report($a_report_data);
		}
	}

	public function send_report($a_data)
	{
		$s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
		$s_file_name = 'Invoice_reminder_report_'.date('d-M-Y');
		$s_filename = $s_file_name.'.xlsx';

		$s_file_path = APPPATH."uploads/finance/reminder/".date('Y')."/".date('m')."/";
		if(!file_exists($s_file_path)){
			mkdir($s_file_path, 0777, TRUE);
		}

		$o_spreadsheet = IOFactory::load($s_template_path);
		$o_sheet = $o_spreadsheet->getActiveSheet();
		$o_spreadsheet->getProperties()
			->setTitle($s_file_name)
			->setCreator("IULI Finance Services")
			->setCategory("Invoice Daily Report");

		$i_row = 1;
		$o_sheet->setCellValue('A'.$i_row, 'Student Name');
		$o_sheet->setCellValue('B'.$i_row, 'Batch');
		$o_sheet->setCellValue('C'.$i_row, 'Year');
		$o_sheet->setCellValue('D'.$i_row, 'Prodi');
		$o_sheet->setCellValue('E'.$i_row, 'Invoice Description');
		$i_row++;

		foreach ($a_data as $key => $value) {
			$o_sheet->setCellValue('A'.$i_row, $value['personal_data_name']);
			$o_sheet->setCellValue('B'.$i_row, $value['academic_year_id']);
			$o_sheet->setCellValue('C'.$i_row, $value['finance_year_id']);
			$o_sheet->setCellValue('D'.$i_row, $value['study_program_abbreviation']);
			$o_sheet->setCellValue('E'.$i_row, $value['invoice_description']);
			$i_row++;
		}

		$o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
		$o_writer->save($s_file_path.$s_filename);
		$o_spreadsheet->disconnectWorksheets();
		unset($o_spreadsheet);

		// $config = $this->config->item('mail_config');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from('employee@company.ac.id', 'IULI Reminder Report');
		$this->email->to('employee@company.ac.id');
        // $this->email->to(['employee@company.ac.id', 'employee@company.ac.id']);
        $this->email->subject('Report Daily Reminder');
		$this->email->message('');
		$this->email->attach($s_file_path.$s_filename);
		$this->email->send();
		exit;
	}
	
	public function send_reminder($o_invoice, $a_special_cc = false)
	{
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fam');
		
		$a_email = $this->config->item('email');
		$s_email_from = $a_email['finance']['payment'];
		$a_bcc_email = array('employee@company.ac.id');
		
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($o_invoice->personal_data_id, [
			'student_status !=' => 'resign'
		]);
		$mbo_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_invoice->personal_data_id])[0];
		$mbo_family_data = $this->Fam->get_family_by_personal_data_id($o_invoice->personal_data_id);
		$mba_parent_email = $a_special_cc;
		if($mbo_family_data){
			$mba_family_members = $this->Fam->get_family_members($mbo_family_data->family_id, array(
				'family_member_status != ' => 'child'
			));
			if($mba_family_members){
				$mba_parent_email = (is_array($mba_parent_email)) ? $mba_parent_email : array();
				foreach($mba_family_members as $family){
					array_push($mba_parent_email, $family->personal_data_email);
				}
			}
		}

		if ($mbo_student_data->student_email == 'hose.winanda@stud.iuli.ac.id') {
			if ($mba_parent_email) {
				array_push($mba_parent_email, 'hosewinanda@gmail.com');
			}
		}
		else if ($mbo_student_data->student_email == 'andy.daniswara@stud.iuli.ac.id') {
			if ($mba_parent_email) {
				array_push($mba_parent_email, 'lia.naomi03@gmail.com');
			}
		}

		if (($mbo_student_data->student_status != 'active') AND (!is_null($mbo_personal_data->personal_data_email))) {
			if ($mba_parent_email) {
				array_push($mba_parent_email, $mbo_personal_data->personal_data_email);
			}
			else {
				$mba_parent_email = [$mbo_personal_data->personal_data_email];
			}
		}
		
		// $config = $this->config->item('mail_config');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		
		$this->email->from($s_email_from, 'IULI Reminder System');
		// $this->email->to('employee@company.ac.id');
		$this->email->to($mbo_student_data->student_email);
		if ($mbo_student_data->student_email == 'estrella.natalia@stud.iuli.ac.id') {
			$mba_parent_email = false;
			// request via pak adit di chat wa: 28 feb 2023 15:00:00
		}

		if ($mbo_student_data->student_email == 'regan.leonard@stud.iuli.ac.id') {
			if (($mba_parent_email) AND (!in_array('congrestom@gmail.com', $mba_parent_email))) {
				array_push($mba_parent_email, 'congrestom@gmail.com');
			}
			else {
				$mba_parent_email = ['congrestom@gmail.com'];
			}
			// request via pak adit di chat wa: 28 feb 2023 15:00:00
		}

		if($mba_parent_email){
			$this->email->cc($mba_parent_email);
		}
		
		$this->email->bcc($a_bcc_email);
		$this->email->reply_to($a_email['finance']['main']);
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
		$this->a_page_data['student_data'] = $mbo_student_data;

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
		else if ($s_payment_type_code = '02') {
			// $this->email->attach(FCPATH.'assets/img/notification_blocked_portal.png');
			$s_html .= '<img src="'.base_url().'assets/img/notification_blocked_portal.png" alt="IMG">';
		}

		$this->email->message($s_html);
		// $this->email->send();
		if(!$this->email->send()){
			$this->log_activity('Email did not sent');
            $this->log_activity('Error Message: '.$this->email->print_debugger());
			return $this->email->print_debugger();
		}
		else{
			return true;
		}
	}

	public function set_fine_flatted($o_invoice, $mba_sub_invoice_details_installment)
	{
		$i_invoice_fined_count = $o_invoice->invoice_fined_count;
		$d_fine_amount = $this->d_fine_amount;
		$a_total_fine = array();
		$a_amount_total = array();
		$mbo_invoice_full_payment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
		
		if (!$mbo_invoice_full_payment) {
			var_dump($o_invoice);
			// exit;
			return false;
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
				$d_sub_invoice_amount_total = $o_installment->sub_invoice_details_amount_total;
				// gara gara ini tadinya "sub_invoice_amount_total" jadinya geger dunia persilatan 14 Desember 2021 17:05
				array_push($a_total_fine, $d_sub_invoice_amount_fined);
				array_push($a_amount_total, $d_sub_invoice_amount_total);
			}

			$o_bni_data = $this->Bm->get_data_by_trx_id($o_installment->trx_id);
			$a_update_billing = array(
				'trx_id' => $o_installment->trx_id,
				'trx_amount' => $d_sub_invoice_amount_total,
				'customer_name' => $o_bni_data->customer_name,
				'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_installment->sub_invoice_details_deadline." +1 month")),
				'description' => $o_installment->sub_invoice_details_description,
				'customer_email' => 'bni.employee@company.ac.id'
			);
			
			$this->Bm->update_billing($a_update_billing);
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

		$o_bni_data = $this->Bm->get_data_by_trx_id($mbo_invoice_full_payment->trx_id);
		$a_update_billing_full_payment = array(
			'trx_id' => $mbo_invoice_full_payment->trx_id,
			'trx_amount' => $d_total_amount_full,
			'customer_name' => $o_bni_data->customer_name,
			'datetime_expired' => date('Y-m-d 23:59:59', strtotime($mbo_invoice_full_payment->sub_invoice_details_deadline." +1 month")),
			'description' => $mbo_invoice_full_payment->sub_invoice_details_description,
			'customer_email' => 'bni.employee@company.ac.id'
		);
		
		$this->Bm->update_billing($a_update_billing_full_payment);

		$a_update_sub_invoice_installment = array(
			'sub_invoice_amount_total' => $d_total_amount_full
		);
		$a_update_sub_invoice_full = $a_update_sub_invoice_installment;
		$a_update_sub_invoice_full['sub_invoice_amount'] = $mbo_invoice_full_payment->sub_invoice_details_amount;
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

	// public function check_invoice_new($o_invoice, $b_reminder = false)
	// {
	// 	$this->load->model('student/Student_model', 'Stm');
	// 	$a_personal_data_id_always_send = [
	// 		'9ba480a7-5d0b-4e8e-9bbf-22d57676003a', //AKNES NUR SARI UMCI (INR/2020)
	// 	];
	// 	// $a_student_cc_all_dept = ['11201607008','11201601024','11201602002','11201602031','11201801002','11201803007','11201908002','11201907013','11201901010','11201902005','11202008012','11202007010','11202007013','11202102012','11201502022','11201607006','11201704006','11201608008','11201607017','11201701014'];

	// 	$i_now = time();
	// 	$b_send_reminder = ($o_invoice->invoice_allow_reminder == 'yes') ? true : false;
	// 	$b_allow_fine = ($o_invoice->invoice_allow_fine == 'yes') ? true : false;

	// 	$mba_sub_invoice_details = $this->Im->get_invoice_data(['di.invoice_id' => $o_invoice->invoice_id]);
	// 	if ($mba_sub_invoice_details) {
	// 		$a_sub_invoice_details_fined = array();
	// 		$o_sub_invoice_details_fined = false;

	// 		foreach ($mba_sub_invoice_details as $o_sub_invoice_details) {
	// 			if (($o_sub_invoice_details->sub_invoice_details_status != 'paid') AND ($o_sub_invoice_details->sub_invoice_details_amount > 0)) {
	// 				$i_deadline = strtotime($o_sub_invoice_details->sub_invoice_details_deadline);
	// 				$i_datediff = $i_now - $i_deadline;
	// 				$i_float = round($i_datediff / (60 * 60 * 24));

	// 				if ($i_float == 0) {
	// 					$s_payment_type_code = substr($o_sub_invoice_details->sub_invoice_details_va_number, 4, 2);
	// 					if (date('d', strtotime($o_sub_invoice_details->sub_invoice_details_deadline)) == '25') {
	// 						$s_new_deadline = date('Y-m-10 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +1 month"));
	// 					}
	// 					else {
	// 						$s_new_deadline = date('Y-m-25 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +1 month"));
	// 					}
						
	// 					$this->Im->update_sub_invoice_details(array('sub_invoice_details_deadline' => $s_new_deadline), array('sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id));

	// 					if (!is_null($o_sub_invoice_details->trx_id)) {
	// 						$o_bni_data = $this->Bm->get_data_by_trx_id($o_sub_invoice_details->trx_id);
	// 						$a_update_billing = array(
	// 							'trx_id' => $o_sub_invoice_details->trx_id,
	// 							'trx_amount' => $o_sub_invoice_details->sub_invoice_details_amount_total,
	// 							'customer_name' => $o_bni_data->customer_name,
	// 							'datetime_expired' => $s_new_deadline,
	// 							'description' => $o_sub_invoice_details->sub_invoice_details_description,
	// 							'customer_email' => 'bni.employee@company.ac.id'
	// 						);
							
	// 						$this->Bm->update_billing($a_update_billing);
	// 					}
						
	// 					if($b_allow_fine){
	// 						if($o_sub_invoice_details->sub_invoice_type == 'installment'){
	// 							array_push($a_sub_invoice_details_fined, $o_sub_invoice_details);
	// 						}
	// 						else{
	// 							$o_sub_invoice_details_fined = $o_sub_invoice_details;
	// 						}
	// 					}
	// 				}
	// 				else if(($i_float >= -14) AND ($i_float < 0)) {
	// 					if(is_null($o_sub_invoice_details->trx_id)){
	// 						// create va
	// 						$a_trx_data = array(
	// 							'trx_amount' => $o_sub_invoice_details->sub_invoice_details_amount,
	// 							'billing_type' => 'c',
	// 							'customer_name' => $o_invoice->personal_data_name,	
	// 							'virtual_account' => $o_sub_invoice_details->sub_invoice_details_va_number,
	// 							'description' => $o_sub_invoice_details->sub_invoice_details_description,
	// 							'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline)),
	// 							'customer_email' => 'bni.employee@company.ac.id'
	// 						);
	// 						$a_bni_result = $this->Bm->create_billing($a_trx_data);
							
	// 						$a_sub_invoice_details_update = array(
	// 							'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline))
	// 						);
							
	// 						if($a_bni_result['status'] == '000'){
	// 							$a_sub_invoice_details_update['trx_id'] = $a_bni_result['trx_id'];
	// 						}
	// 						else{
	// 							if ((isset($a_bni_result['trx_id'])) AND ($a_bni_result['status'] == '102')) {
	// 								// print('<pre>ss');var_dump($a_bni_result);exit;
	// 								$a_update_billing = array(
	// 									'trx_id' => $a_bni_result['trx_id'],
	// 									'trx_amount' => 999,
	// 									'customer_name' => 'CANCEL PAYMENT',
	// 									'datetime_expired' => '2020-01-01 23:59:59',
	// 									'description' => 'CANCEL PAYMENT'
	// 								);
	// 								$this->Bm->update_billing($a_update_billing);
	// 							}

	// 							$a_body = [
	// 								'trx_data' => $a_trx_data,
	// 								'bni_result' => $a_bni_result
	// 							];

	// 							log_message('error', 'ERROR check billing from '.__FILE__.' '.__LINE__);

	// 							$this->email->from('employee@company.ac.id');
	// 							$this->email->to(array('employee@company.ac.id'));
	// 							$this->email->subject('ERROR CHECK BILLING');
	// 							$this->email->message(json_encode($a_body));
	// 							$this->email->send();
	// 						}
							
	// 						$this->Im->update_sub_invoice_details(
	// 							$a_sub_invoice_details_update, 
	// 							array(
	// 								'sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id
	// 							)
	// 						);
	// 					}

	// 					$o_total_has_invoice = $this->General->get_where('dt_invoice', ['personal_data_id' => $o_invoice->personal_data_id]);
	// 					$a_clause_invoice_details = [
	// 						'did.invoice_id' => $o_invoice->invoice_id,
	// 						'df.payment_type_code' => '02'
	// 					];

	// 					if (($o_total_has_invoice) AND (count($o_total_has_invoice) == 1)) {
	// 						$a_clause_invoice_details['df.semester_id != '] = 1;
	// 					}

	// 					// send reminder
	// 					$mba_invoice_details = $this->Im->get_invoice_details($a_clause_invoice_details);
	// 					$mba_invoice_german_details = $this->Im->get_invoice_details([
	// 						'did.invoice_id' => $o_invoice->invoice_id,
	// 						'df.payment_type_code' => '18'
	// 					]);
						
	// 					if($mba_invoice_details){
	// 						if (in_array($o_invoice->personal_data_id, $a_personal_data_id_always_send)) {
	// 							if($o_sub_invoice_details->sub_invoice_details_status != 'paid'){
	// 								$b_send_reminder = true;
	// 							}
	// 						}
	// 						else if ($o_invoice->invoice_allow_reminder == 'yes'){
	// 							$mba_student_data = $this->Sm->get_student_list_data([
	// 								'ds.personal_data_id' => $o_invoice->personal_data_id
	// 							], ['active', 'graduated', 'inactive']);
	// 							// $mbo_student_data = $this->General->get_where('dt_student', [
	// 							// 	'personal_data_id' => $o_invoice->personal_data_id,
	// 							// 	'student_status' => 'active',
	// 							// ]);
	
	// 							if(($o_sub_invoice_details->sub_invoice_details_status != 'paid') AND ($mba_student_data)){
	// 								$b_send_reminder = true;
	// 							}
	// 						}
	// 					}
	// 					else if ($mba_invoice_german_details) {
	// 						if ($o_invoice->invoice_allow_reminder == 'yes') {
	// 							$mba_student_data = $this->Sm->get_student_list_data([
	// 								'ds.personal_data_id' => $o_invoice->personal_data_id
	// 							], ['active', 'graduated']);
	
	// 							if(($o_sub_invoice_details->sub_invoice_details_status != 'paid') AND ($mba_student_data)){
	// 								$b_send_reminder = true;
	// 							}
	// 						}
	// 					}
	// 				}
	// 			}
	// 		}
	// 	}
	// }

	public function test_run()
	{
		print('ada');
	}
	
	public function check_invoice($o_invoice, $b_reminder = false)
	{
		show_404();
		exit;
		$this->load->model('student/Student_model', 'Sm');
		
		$a_personal_data_id_always_send = [
			'50ef9e08-51fe-4fd3-8d8d-989133c95ca1', //LOUAI SEKKOUR (IBA/2018) 
			'831eec77-0fd5-48f0-b066-8b0899106caf', //RIHAM SEBTI SEKKOUR (HTM/2019)
			'9ba480a7-5d0b-4e8e-9bbf-22d57676003a', //AKNES NUR SARI UMCI (INR/2020)
		];

		$a_invoice_always_send = [
			'80c6d638-ee53-4b70-96a2-d06951cdd06e', // 	Short Semester Fee INR 2016 / M. KEVIN GARCIA T. (INR/2016)
		];

		$a_personal_data_id_ignoring_student_status = $this->config->item('invoice_ignore_student_status');
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

							if ($o_invoice->invoice_allow_fine == 'no') {
								$o_bni_data = $this->Bm->get_data_by_trx_id($o_sub_invoice_details->trx_id);

								$a_update_billing = array(
									'trx_id' => $o_sub_invoice_details->trx_id,
									'trx_amount' => $o_sub_invoice_details->sub_invoice_details_amount_total,
									'customer_name' => $o_bni_data->customer_name,
									'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +1 month")),
									'description' => $o_sub_invoice_details->sub_invoice_details_description,
									'customer_email' => 'bni.employee@company.ac.id'
								);
								
								$this->Bm->update_billing($a_update_billing);
							}
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
						else if (($s_payment_type_code == '04') AND (in_array($o_invoice->invoice_id, $a_invoice_always_send))) {
							$a_update_sub_invoice_details = array(
								'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +1 month"))
							);
							$this->Im->update_sub_invoice_details($a_update_sub_invoice_details, array('sub_invoice_details_id' => $o_sub_invoice_details->sub_invoice_details_id));

							if ($o_invoice->invoice_allow_fine == 'no') {
								$o_bni_data = $this->Bm->get_data_by_trx_id($o_sub_invoice_details->trx_id);

								$a_update_billing = array(
									'trx_id' => $o_sub_invoice_details->trx_id,
									'trx_amount' => $o_sub_invoice_details->sub_invoice_details_amount_total,
									'customer_name' => $o_bni_data->customer_name,
									'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details->sub_invoice_details_deadline." +1 month")),
									'description' => $o_sub_invoice_details->sub_invoice_details_description,
									'customer_email' => 'bni.employee@company.ac.id'
								);
								
								$this->Bm->update_billing($a_update_billing);
							}
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
					else if(($i_float >= -14) AND ($i_float < 0)){
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
								if ((isset($a_bni_result['trx_id'])) AND ($a_bni_result['status'] == '102')) {
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

								log_message('error', 'ERROR check billing from '.__FILE__.' '.__LINE__);

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
						$s_payment_type_code = substr($o_sub_invoice_details->sub_invoice_details_va_number, 4, 2);
						$mba_invoice_details = $this->Im->get_invoice_details($a_clause_invoice_details);
						$mba_invoice_german_details = $this->Im->get_invoice_details([
							'did.invoice_id' => $o_invoice->invoice_id,
							'df.payment_type_code' => '18'
						]);
						
						$mba_invoice_tuition_fee_fined = $this->Im->get_invoice_details([
							'did.invoice_id' => $o_invoice->invoice_id,
							'df.payment_type_code' => '19'
						]);
						
						if($mba_invoice_details){
							if (in_array($o_invoice->personal_data_id, $a_personal_data_id_always_send)) {
								if($o_sub_invoice_details->sub_invoice_details_status != 'paid'){
									$b_send_reminder = true;
								}
							}
							else if (in_array($o_invoice->personal_data_id, $a_personal_data_id_ignoring_student_status)) {
								if($o_sub_invoice_details->sub_invoice_details_status != 'paid'){
									$b_send_reminder = true;
								}
							}
							else if ($o_invoice->invoice_allow_reminder == 'yes'){
								$mba_student_data = $this->Sm->get_student_list_data([
									'ds.personal_data_id' => $o_invoice->personal_data_id
								], ['active', 'graduated', 'inactive']);
								// $mbo_student_data = $this->General->get_where('dt_student', [
								// 	'personal_data_id' => $o_invoice->personal_data_id,
								// 	'student_status' => 'active',
								// ]);
	
								if(($o_sub_invoice_details->sub_invoice_details_status != 'paid') AND ($mba_student_data)){
									$b_send_reminder = true;
								}
							}
						}
						else if ($mba_invoice_german_details) {
							if ($o_invoice->invoice_allow_reminder == 'yes') {
								$mba_student_data = $this->Sm->get_student_list_data([
									'ds.personal_data_id' => $o_invoice->personal_data_id
								], ['active', 'graduated']);
	
								if(($o_sub_invoice_details->sub_invoice_details_status != 'paid') AND ($mba_student_data)){
									$b_send_reminder = true;
								}
							}
						}
						else if ($mba_invoice_tuition_fee_fined) {
							if ($o_invoice->invoice_allow_reminder == 'yes') {
								$mba_student_data = $this->Sm->get_student_list_data([
									'ds.personal_data_id' => $o_invoice->personal_data_id
								], ['active', 'inactive', 'onleave']);
	
								if(($o_sub_invoice_details->sub_invoice_details_status != 'paid') AND ($mba_student_data)){
									$b_send_reminder = true;
								}
							}
						}
						else if (in_array($o_invoice->invoice_id, $a_invoice_always_send)) {
							if ($o_sub_invoice_details->sub_invoice_details_status != 'paid'){
								$b_send_reminder = true;
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
			case "sandbox":
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

	// public function test_check_trx_dev()
	// {
	// 	$mba_test_data = $this->Bm->cek_va_dev_status();
	// 	print('<pre>');var_dump($mba_test_data);exit;
	// }
	
	// public function create_test_billing_data()
	// {
	// 	// $a_billing_data = array(
	// 	// 	'trx_amount' => '4000000',
	// 	// 	'billing_type' => 'c',
	// 	// 	'customer_name' => 'DEVELOPER TEST',
	// 	// 	'virtual_account' => '8141010001190001',
	// 	// 	'description' => 'TEST',
	// 	// 	'datetime_expired' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d', time())."+ 1 day")),
	// 	// 	'customer_email' => 'bni.employee@company.ac.id'
	// 	// );
	// 	$a_create_billing_result = $this->Bm->create_billing_dev();
	// 	print('<pre>');var_dump($a_create_billing_result);exit;
	// }
	
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

International University Liaison Indonesia
Associate Tower 7th Floor.
Intermark Indonesia
Email: employee@company.ac.id
TEXT;
		$a_email = $this->config->item('email');
		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to($s_email);
		if($mba_parent_email){
			$this->email->cc($mba_parent_email);
		}

		$this->email->bcc([
			$a_email['admission']['main'],
			$a_email['finance']['payment'],
			'employee@company.ac.id',
			'employee@company.ac.id',
			'employee@company.ac.id'
		]);

		$this->email->subject("[IULI] Payment Confirmation");
		$this->email->message($t_body);
		$this->email->send();
	}

	public function force_send_notification()
	{
		// $this->tuition_fee_payment_notification(
		// 	'ADRIAN ARNEY',
		// 	'adrian.arney@stud.iuli.ac.id',
		// 	'2023-07-28 12:15:12',
		// 	'Rp. 3.000.000,-',
		// 	['adrian091097@gmail.com', 'arthur.luhulima@tokiomarine.co.id'],
		// 	true,
		// 	'Installment 1 Semester Fee MGT study Program - Batch 2023',
		// 	false
		// );
	}

	public function test_email()
	{
		$t_body = <<<TEXT
<p>Dear ngetes</p>
<br>
Best Regards,<br>
Finance Team<br>
<p><br></p>
International University Liaison Indonesia<br>
Associate Tower 7th Floor.<br>
Intermark Indonesia BSD<br>
Jl. Lingkar Timur BSD Serpong<br>
Tangerang Selatan 15310<br>
Email: employee@company.ac.id<br>
TEXT;
		$this->email->clear(TRUE);
		// $config['mailtype'] = 'text';
		// $config = $this->config->item('mail_config');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$a_email = $this->config->item('email');
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to('budi.siswanto1450@gmail.com');
		$this->email->cc(['buds0878@gmail.com']);

		$a_bcc = [
			'employee@company.ac.id',
		];
		
		$this->email->bcc($a_bcc);
		
		$this->email->subject("[IULI] Tuition Fee Payment Confirmation");
		$this->email->message($t_body);
		if (!$this->email->send()) {
			$this->send_error_notification('error payment '.$this->email->print_debugger());

			$this->email->to(array('employee@company.ac.id'));
			$this->email->subject('[Error Payment]Payment notification doesnt work!');
			$this->email->message($this->email->print_debugger());
			$this->email->send();
		}
	}
	
	private function tuition_fee_payment_notification(
		$s_name,
		$s_email,
		$s_date_time_payment,
		$s_amount,
		$mba_parent_email,
		$b_admission_bcc = false,
		$s_description = '',
		$b_is_special = false,
		$s_bni_id = false
	)
	{
		$t_body = <<<TEXT
<p>Dear {$s_name}</p>
<br>
<p>This email is to confirm that we have received your payment on {$s_date_time_payment} for amount of {$s_amount} for your tuition fee {$s_description}</p>
<br>
Best Regards,<br>
Finance Team<br>
<p><br></p>
International University Liaison Indonesia<br>
Associate Tower 7th Floor.<br>
Intermark Indonesia BSD<br>
Jl. Lingkar Timur BSD Serpong<br>
Tangerang Selatan 15310<br>
Email: employee@company.ac.id<br>
TEXT;
		$this->email->clear(TRUE);
		if ($s_bni_id) {
			$receipt = modules::run('download/pdf_download/generate_receipt', $s_bni_id);
			if ($receipt) {
				$s_receipt_path = $receipt['filepath'];
				$s_receipt_file = $receipt['filename'];
				
				if(file_exists($s_receipt_path.$s_receipt_file)){
					$this->email->attach($s_receipt_path.$s_receipt_file);
				}
			}
		}
		// $config['mailtype'] = 'text';
		// $config = $this->config->item('mail_config');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$a_email = $this->config->item('email');
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		// $this->email->to('employee@company.ac.id');
		$this->email->to($s_email);
		if ($s_email == 'estrella.natalia@stud.iuli.ac.id') {
			$mba_parent_email = false;
			// request via pak adit di chat wa: 28 feb 2023 15:00:00
		}
		else if ($s_email == 'andy.daniswara@stud.iuli.ac.id') {
			if ($mba_parent_email) {
				array_push($mba_parent_email, 'lia.naomi03@gmail.com');
			}
		}

		$a_bcc = [
			$a_email['finance']['payment'],
			'employee@company.ac.id',
		];

		if ($b_is_special) {
			$a_bcc = [
				'employee@company.ac.id'
			];
			$mba_parent_email = false;
		}

		if($mba_parent_email){
			$this->email->cc($mba_parent_email);
		}

		if ($b_admission_bcc) {
			array_push($a_bcc, $a_email['admission']['main']);
		}
		
		$this->email->bcc($a_bcc);
		
		$this->email->subject("[IULI] Tuition Fee Payment Confirmation");
		$this->email->message($t_body);
		if (!$this->email->send()) {
			$this->send_error_notification('error payment '.$this->email->print_debugger());

			$this->email->to(array('employee@company.ac.id'));
			$this->email->subject('[Error Payment]Payment notification doesnt work!');
			$this->email->message($this->email->print_debugger());
			$this->email->send();
		}
	}

	public function send_invitation_online_test($s_personal_data_id) {
		$mba_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $s_personal_data_id]);
		if ($mba_student_data) {
			$mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $s_personal_data_id]);
			$mba_candidate_exam = $this->General->get_where('dt_exam_candidate', ['student_id' => $mba_student_data[0]->student_id]);
			if ($mba_candidate_exam) {
				$data['personal_data_name'] = $mba_personal_data[0]->personal_data_name;
				$data['token_english_test'] = $mba_candidate_exam[0]->token;
				$s_body_message = $this->load->view('messaging/admission/invitation_online_test', $data, true);
				$s_header_message = '<img src="'.base_url().'assets/img/header_of_letter.jpg" alt="">';
				$s_footer_message = '<img src="'.base_url().'assets/img/footer_of_letter.png" alt="">';

				$s_message = $s_header_message.$s_body_message.$s_footer_message;
				$this->email->clear(TRUE);

				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				$this->email->from('employee@company.ac.id', '[IULI] Admission Team');
				$this->email->to($mba_personal_data[0]->personal_data_email);
				// $this->email->to('employee@company.ac.id');
				$this->email->bcc([
					'employee@company.ac.id',
					'employee@company.ac.id'
				]);
				$this->email->subject("[IULI] Online Test Invitation");
				$this->email->message($s_message);
				$this->email->send();
			}
			else {
				$this->send_error_notification('error payment '.$this->email->print_debugger());
				$this->email->to(array('employee@company.ac.id'));
				$this->email->subject('[Error Invitation]Candidate not found!');
				$this->email->message('candidate with personal_data_id:'.$s_personal_data_id.' not found');
				$this->email->send();
			}
		}
		else {
			$this->send_error_notification('error payment '.$this->email->print_debugger());
			$this->email->to(array('employee@company.ac.id'));
			$this->email->subject('[Error Invitation]Candidate not found!');
			$this->email->message('candidate with personal_data_id:'.$s_personal_data_id.' not found');
			$this->email->send();
		}
	}
	
	private function enrollment_fee_payment_confirmation(
		$s_name,
		$s_email,
		$s_date_time_payment,
		$s_amount,
		$mba_parent_email,
		$s_bni_id = false
	)
	{
		$t_body = <<<TEXT
Dear {$s_name},

This email is to confirm that we have received your payment on {$s_date_time_payment} for amount of {$s_amount} for IULI’s Admission Process

Should you have inquiries regarding the registration process, please consult with us on email (employee@company.ac.id) or phone at +62 852 123 18000.

Best Regards,
Admission Team

International University Liaison Indonesia
Associate Tower 7th Floor.
Intermark Indonesia BSD
Jl. Lingkar Timur BSD Serpong
Tangerang Selatan 15310
Indonesia
Email: employee@company.ac.id



Kepada Yth. {$s_name},

Dengan email ini kami mengkonfirmasikan bahwa pembayaran Anda pada tanggal {$s_date_time_payment} dengan jumlah {$s_amount} untuk administrasi penerimaan mahasiswa baru IULI telah kami terima.

Bila Anda memiliki pertanyaan mengenai penerimaan mahasiswa baru ini, silahkan menghubungi kami melalui email employee@company.ac.id atau telepon ke 0852 123 18000.

Hormat Kami,
Tim Pendaftaran Mahasiswa Baru

International University Liaison Indonesia
Associate Tower 7th Floor.
Intermark Indonesia BSD
Jl. Lingkar Timur BSD Serpong
Tangerang Selatan 15310
Indonesia
Email: employee@company.ac.id
TEXT;
		$a_email = $this->config->item('email');
		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
		$this->email->clear(TRUE);
		
		if ($s_bni_id) {
			$receipt = modules::run('download/pdf_download/generate_receipt', $s_bni_id);
			if ($receipt) {
				$s_receipt_path = $receipt['filepath'];
				$s_receipt_file = $receipt['filename'];
				
				if(file_exists($s_receipt_path.$s_receipt_file)){
					$this->email->attach($s_receipt_path.$s_receipt_file);
				}
			}
		}
		
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to($s_email);
		if($mba_parent_email){
			$this->email->cc($mba_parent_email);
		}
		// $this->email->bcc(array($a_email['finance']['payment']));
		$this->email->bcc([
			$a_email['admission']['main'],
			$a_email['finance']['payment'],
			'pmb.iuli.ac.id',
			'employee@company.ac.id',
		]);
// 		$this->email->bcc(array('employee@company.ac.id', 'employee@company.ac.id'));
		$this->email->subject("[IULI] Online Registration Payment Confirmation");
		$this->email->message($t_body);
		$this->email->send();
	}

	function handle_open_minimum_payment($mba_invoice_data, $a_paid_billing) {
		// $a_student_id_finance = ['b8e4ed7e-558f-45b6-a867-6e80bee9588f','0a6fd0df-8041-485c-834a-86bdccaa8d77','88399fbc-346e-41d5-9d36-5ced963baf8d','aa9552ea-426b-4907-9ad5-36c0c5f8dc8e','ccfa2e6d-0a53-42d7-9d6c-9adf5c5cb61a','ec98dc1b-7eb7-402e-a937-89ee902f3983','d5d2c23a-85c5-4b88-8493-618a1fba016e','3322b59b-cd07-47ac-aab5-4fc407cedec9','48736040-b217-4cc3-9aea-4acbbf52da67','8325920e-ed42-460a-ab76-e19e06c2f1c8','12e89836-193b-42c3-af50-43a10582bc9d','2dc26092-778e-4184-8f47-22b09222429c','33dd6059-0324-476e-a874-cf573f503adc','909e38c2-d80a-42fc-94e5-6ff4dec143e8','ded5eecd-74ea-444e-b99b-dae786eab65b','555867ef-943a-4130-a2e1-ce1e3721e6ea','5e8bc97b-c0cd-414e-9765-d2e006be5607','dc4997ee-2abf-4b34-b802-184de17ad4dc','3898a159-e5b1-4aa7-9f56-fe755db53fc6','a85cd876-f42e-4e19-8b19-bcc87ed7ec79','600e4be7-d365-4280-a59c-682651a651cd','d65b070f-bc57-4086-b719-a4bad32b8b0e','a92d54ad-b5ef-4e86-a1b2-23902c84b72a','5d10bf82-35f0-4781-8b76-fbebfaa7d21e','94817edd-dff9-472b-8e32-df0bd0ecba2c','2a6a8955-ef54-40bd-8300-905e195c0d9a','b3b518fd-5ff7-42e2-a8d6-f025e67b06c6','18b9b936-a5fa-4eb4-b426-7195bddbc3bd','9300bea7-0d15-4fe1-b3c5-4f52ebd0853f'];
		$a_student_id_finance = [];
		$d_payment_amount = $a_paid_billing['payment_amount'];
		$d_total_billing = $this->_get_billing_total($mba_invoice_data[0]->sub_invoice_details_va_number);
		$d_amount_process = $d_payment_amount;
		if ($d_payment_amount > 0) {
			$bni_transaction_data = $this->General->get_where('bni_transactions', [
				'virtual_account' => $a_paid_billing['virtual_account'],
				'datetime_payment' => $a_paid_billing['datetime_payment']
			]);
			for ($i=0; $i < count($mba_invoice_data) ; $i++) {
				if ($d_amount_process > 0) {
					$o_invoice_detail = $mba_invoice_data[$i];
					$d_amount_paid_receive = $d_amount_process;

					$mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice_detail->invoice_id);
					if ($o_invoice_detail->sub_invoice_type == 'full') {
						$payment_due_date = $o_invoice_detail->sub_invoice_details_real_datetime_deadline;
						$b_has_installment = false;

						if ($mba_invoice_installment) {
							if (date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($payment_due_date))) {
								$b_has_installment = true;
							}

							if (!$b_has_installment) {
								foreach ($mba_invoice_installment as $o_installment) {
									if ($o_installment->sub_invoice_details_amount_paid > 0) {
										$b_has_installment = true;
									}
								}
							}
						}

						if (!$b_has_installment) {
							// print('<pre>');var_dump($o_invoice_detail->sub_invoice_details_amount_total);exit;
							if ($d_amount_process >= $o_invoice_detail->sub_invoice_details_amount_total) {
								$d_amount_process = $d_amount_process - $o_invoice_detail->sub_invoice_details_amount_total;
								$a_update_sub_invoice_details_data = [
									'sub_invoice_details_amount_paid' => $o_invoice_detail->sub_invoice_details_amount_total,
									'sub_invoice_details_status' => 'paid',
									'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
								];
								$this->Im->update_sub_invoice_details($a_update_sub_invoice_details_data, ['sub_invoice_details_id' => $o_invoice_detail->sub_invoice_details_id]);
		
								$a_update_sub_invoice_data = array(
									'sub_invoice_amount_paid' => $o_invoice_detail->sub_invoice_details_amount_total,
									'sub_invoice_amount_total' => 0,
									'sub_invoice_datetime_paid_off' => $a_paid_billing['datetime_payment']
								);
								$this->Im->update_sub_invoice($a_update_sub_invoice_data, array('sub_invoice_id' => $o_invoice_detail->invoice_id));

								if ($mba_invoice_installment) {
									foreach ($mba_invoice_installment as $o_installment) {
										$this->Im->update_sub_invoice_details([
											'sub_invoice_details_status' => 'paid',
											'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
										], ['sub_invoice_id' => $o_installment->sub_invoice_details_id]);
									}
								}
		
								$a_update_invoice_data = array(
									'invoice_amount_paid' => 0,
									'invoice_amount_fined' => 0,
									'invoice_status' => 'paid',
									'invoice_datetime_paid_off' => $a_paid_billing['datetime_payment']
								);
								$this->Im->update_invoice($a_update_invoice_data, array('invoice_id' => $o_invoice_detail->invoice_id));

								if ($bni_transaction_data) {
									$a_payment_history = [
										'payment_id' => $this->uuid->v4(),
										'personal_data_id' => $o_invoice_detail->personal_data_id,
										'bni_transactions_id' => $bni_transaction_data[0]->bni_transactions_id,
										'sub_invoice_details_id' => $o_invoice_detail->sub_invoice_details_id,
										'payment_amount' => $o_invoice_detail->sub_invoice_details_amount_total,
										'date_added' => date('Y-m-d H:i:s')
									];
									$this->General->insert_data('bni_transactions_payment', $a_payment_history);
								}
							}
						}
					}
					else {
						// $mba_new_invoice_data = $this->General->get_where('dt_invoice', ['invoice_id' => $o_invoice_detail->invoice_id]);
						$d_installment_unpaid = $o_invoice_detail->sub_invoice_details_amount_total - $o_invoice_detail->sub_invoice_details_amount_paid;
						$d_amount_paid_receive = $d_amount_process;
						$d_amount_process = $d_amount_process - $d_installment_unpaid;
						if ($d_amount_process > 0) {
							// installment paid
							$d_amount_paid_receive = $d_installment_unpaid;
							$a_update_sub_invoice_details_data = [
								'sub_invoice_details_amount_paid' => $o_invoice_detail->sub_invoice_details_amount_total,
								'sub_invoice_details_status' => 'paid',
								'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
							];
							$this->Im->update_sub_invoice_details($a_update_sub_invoice_details_data, ['sub_invoice_details_id' => $o_invoice_detail->sub_invoice_details_id]);
						}
						else {
							$d_installment_paid = $o_invoice_detail->sub_invoice_details_amount_paid + str_replace('-', '', $d_amount_paid_receive);
							$a_update_sub_invoice_details_data = [
								'sub_invoice_details_amount_paid' => $d_installment_paid,
								'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
							];
							if ($d_installment_unpaid == $d_amount_paid_receive) {
								$a_update_sub_invoice_details_data['sub_invoice_details_status'] = 'paid';
							}
							$this->Im->update_sub_invoice_details($a_update_sub_invoice_details_data, ['sub_invoice_details_id' => $o_invoice_detail->sub_invoice_details_id]);
						}

						if ($bni_transaction_data) {
							$a_payment_history = [
								'payment_id' => $this->uuid->v4(),
								'personal_data_id' => $o_invoice_detail->personal_data_id,
								'bni_transactions_id' => $bni_transaction_data[0]->bni_transactions_id,
								'sub_invoice_details_id' => $o_invoice_detail->sub_invoice_details_id,
								'payment_amount' => $d_amount_paid_receive,
								'date_added' => date('Y-m-d H:i:s')
							];
							$this->General->insert_data('bni_transactions_payment', $a_payment_history);
						}

						// update full payment
						$mbo_invoice_full_payment = $this->Im->get_invoice_full_payment($o_invoice_detail->invoice_id);
						$d_fullpayment_unpaid = $this->_get_total_invoice_unpaid($o_invoice_detail->invoice_id);
						if ($mbo_invoice_full_payment) {
							$d_amount_fullpayment_fined = $mbo_invoice_full_payment->sub_invoice_details_amount_fined;
							$d_fullpayment_amount = $d_fullpayment_unpaid - $d_amount_fullpayment_fined;
							$a_fullpayment_update_sub_invoice_details_data = [
								'sub_invoice_details_amount_total' => $d_fullpayment_unpaid,
								'sub_invoice_details_amount' => $d_fullpayment_amount,
								'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
							];
							$this->Im->update_sub_invoice_details($a_fullpayment_update_sub_invoice_details_data, ['sub_invoice_details_id' => $mbo_invoice_full_payment->sub_invoice_details_id]);

							$a_fullpayment_update_sub_invoice_data = [
								'sub_invoice_amount_total' => $d_fullpayment_unpaid,
								'sub_invoice_amount' => $d_fullpayment_amount,
							];
							$this->Im->update_sub_invoice($a_fullpayment_update_sub_invoice_data, array('sub_invoice_id' => $mbo_invoice_full_payment->sub_invoice_id));
						}

						// update sub_invoice amount
						$mba_sub_invoice_data = $this->General->get_where('dt_sub_invoice', ['sub_invoice_id' => $o_invoice_detail->sub_invoice_id]);
						if ($mba_sub_invoice_data) {
							$d_amount_subinvoice_unpaid = $mba_sub_invoice_data[0]->sub_invoice_amount_total;
							$d_amount_subinvoice_paid = $mba_sub_invoice_data[0]->sub_invoice_amount_paid;

							$d_amount_subinvoice_unpaid -= $d_amount_paid_receive;
							$d_amount_subinvoice_paid += $d_amount_paid_receive;
							
							$a_update_sub_invoice_data = array(
								'sub_invoice_amount_paid' => $d_amount_subinvoice_paid,
								'sub_invoice_amount_total' => $d_amount_subinvoice_unpaid,
								'sub_invoice_datetime_paid_off' => $a_paid_billing['datetime_payment']
							);
							$this->Im->update_sub_invoice($a_update_sub_invoice_data, array('sub_invoice_id' => $o_invoice_detail->sub_invoice_id));
						}

						// cek invoice paid
						$d_invoice_unpaid = $this->_get_total_invoice_unpaid($o_invoice_detail->invoice_id);
						if ($d_invoice_unpaid == 0) {
							if ($mbo_invoice_full_payment) {
								$this->Im->update_sub_invoice_details([
									'sub_invoice_details_status' => 'paid',
									'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
								], ['sub_invoice_id' => $mbo_invoice_full_payment->sub_invoice_details_id]);
							}

							$a_update_invoice_data = array(
								'invoice_amount_paid' => 0,
								'invoice_amount_fined' => 0,
								'invoice_status' => 'paid',
								'invoice_datetime_paid_off' => $a_paid_billing['datetime_payment']
							);
							$this->Im->update_invoice($a_update_invoice_data, array('invoice_id' => $o_invoice_detail->invoice_id));
						}
					}
				}
			}

			if ($d_amount_process > 0) {
				// notifikasi lebih bayar
				$this->email->from('employee@company.ac.id');
				$this->email->to(array('employee@company.ac.id'));
				$this->email->subject('Notifikasi Lebih Bayar');
				$this->email->message('va: '.$mba_invoice_data[0]->sub_invoice_details_va_number);
				$this->email->send();
			}
		}

		$a_update_billing = [
			// 'trx_id' => $a_paid_billing['trx_id'],
			'trx_id' => $mba_invoice_data[0]->trx_id,
			'customer_name' => $a_paid_billing['customer_name'],
		];

		$d_new_total_billing = $this->_get_billing_total($mba_invoice_data[0]->sub_invoice_details_va_number);
		if ($d_new_total_billing <= 0) {
			$a_update_billing['trx_amount'] = 10;
			$a_update_billing['datetime_expired'] = '2020-01-01 23:59:59';
			$a_update_billing['description'] = 'PAID PAYMENT';

			// if (in_array($mba_invoice_data[0]->personal_data_id, $a_student_id_finance)) {
			// 	$this->General->update_data('dt_student', [
			// 		'student_portal_blocked' => 'FALSE',
			// 		'student_portal_blocked_message' => NULL,
			// 		'student_send_transcript' => 'TRUE'
			// 	], ['personal_data_id' => $mba_invoice_data[0]->personal_data_id]);
			// }
		}
		else {
			// $d_amount = modules::run('finance/invoice/get_minimum_payment', $mba_invoice_data[0]->sub_invoice_details_va_number);
			$d_amount = $this->get_minimum_payment($mba_invoice_data[0]->sub_invoice_details_va_number);
			$d_amount = ($d_amount <= 0) ? 10 : $d_amount;
			$a_update_billing['trx_amount'] = $d_amount;
			$a_update_billing['datetime_expired'] = '2024-06-30 23:59:59';
			$a_update_billing['description'] = 'Billing Tuition Fee '.$a_paid_billing['customer_name'];
		}

		$s_client_id = substr($mba_invoice_data[0]->sub_invoice_details_va_number, 1, 3);
		if ($s_client_id == '141') {
			$this->Bm->set_environment('sandbox');
		}

		// print('<pre>');var_dump($a_update_billing);
		// $a_bni_result = $this->Bm->update_billing($a_update_billing);
		// print('<pre>');var_dump($a_bni_result);
		// if ($a_bni_result['status'] !== '000') {
		// 	$this->email->from('employee@company.ac.id');
		// 	$this->email->to(array('employee@company.ac.id'));
		// 	$this->email->subject('ERROR update billing');
		// 	$this->email->message(json_encode($a_bni_result));
		// 	$this->email->send();
		// }
	}

	function get_minimum_payment($s_va_number) {
		$mba_invoice_list = $this->Im->get_invoice_data([
			'dsid.sub_invoice_details_va_number' => $s_va_number,
			'dsid.sub_invoice_details_status != ' => 'paid'
		], ['created', 'pending']);
		
		$s_payment_type = substr($s_va_number, -2);
		$d_min_payment = 1;
		$current_date = date('Y-m-d');
		$next_month = intval(date('m')) + 1;
		$next_year = ($next_month > 12) ? intval(date('Y')) + 1 : date('Y');
		$next_month = ($next_month > 12) ? 1 : $next_month;
		$current_date = (intval(date('d')) > 10) ? date('Y-m-10', strtotime("$next_year-$next_month-10")) : date('Y-m-10');
		
		if ($mba_invoice_list) {
			// print('<pre>');var_dump($mba_invoice_list);exit;
			$mba_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $mba_invoice_list[0]->personal_data_id, 'ds.student_status != ' => 'resign']);
			if ($mba_student_data) {
				$o_student_data = $mba_student_data[0];
				$d_min_payment = 0;
				$d_min_installment = 0;
				$is_full_payment = false;
				foreach ($mba_invoice_list as $o_sub_detail) {
					// if ((!in_array($s_payment_type, ['02', '05'])) AND ($o_sub_detail->sub_invoice_type == 'full')) {
					// 	$d_installment_unpaid = $o_sub_detail->sub_invoice_details_amount_total - $o_sub_detail->sub_invoice_details_amount_paid;
					// 	$d_min_payment += $d_installment_unpaid;
					// }
					// else 
					// print('<pre>');var_dump($current_date.' >= '.date('Y-m-d', strtotime($o_sub_detail->sub_invoice_details_real_datetime_deadline)));exit;
					if ($o_sub_detail->sub_invoice_type != 'full') {
						if ($current_date >= date('Y-m-d', strtotime($o_sub_detail->sub_invoice_details_real_datetime_deadline))) {
							$d_installment_unpaid = $o_sub_detail->sub_invoice_details_amount_total - $o_sub_detail->sub_invoice_details_amount_paid;
							$d_min_installment += $d_installment_unpaid;
							// print($o_sub_detail->sub_invoice_details_description.'<br>');
						}
					}
					else if ($o_sub_detail->sub_invoice_type == 'full') {
						if ($current_date <= date('Y-m-d', strtotime($o_sub_detail->sub_invoice_details_real_datetime_deadline))) {
							$is_full_payment = true;
							$d_installment_unpaid = $o_sub_detail->sub_invoice_details_amount_total - $o_sub_detail->sub_invoice_details_amount_paid;
							$d_min_payment = $d_installment_unpaid;
							// print($o_sub_detail->sub_invoice_details_description.'<br>');
						}
						else if (!in_array($s_payment_type, ['02', '05'])) {
							$is_full_payment = true;
							$d_installment_unpaid = $o_sub_detail->sub_invoice_details_amount_total - $o_sub_detail->sub_invoice_details_amount_paid;
							$d_min_payment = $d_installment_unpaid;
						}
						// $d_installment_unpaid = $o_sub_detail->sub_invoice_details_amount_total - $o_sub_detail->sub_invoice_details_amount_paid;
						// $d_min_payment = $d_installment_unpaid;
					}
				}
				if (!$is_full_payment) {
					$d_min_payment = $d_min_installment;
				}

				if (!is_null($o_student_data->finance_min_payment)) {
					if (in_array($s_payment_type, ['02', '05'])) {
						$d_min_payment = $o_student_data->finance_min_payment;
					}
				}
			}
		}

		return $d_min_payment;
	}

	private function _get_total_invoice_unpaid($s_invoice_id) {
		$mba_invoice_data = $this->General->get_where('dt_invoice', ['invoice_id' => $s_invoice_id]);
		$mba_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $mba_invoice_data[0]->personal_data_id, 'ds.student_status != ' => 'resign']);

		$mba_invoice_installment = $this->Im->get_invoice_installment($s_invoice_id);
		$mbo_invoice_full_payment = $this->Im->get_invoice_full_payment($s_invoice_id);

		$d_total_unpaid = 0;
		if ($mba_invoice_installment) {
			foreach ($mba_invoice_installment as $o_installment) {
				$d_installment_unpaid = $o_installment->sub_invoice_details_amount_total - $o_installment->sub_invoice_details_amount_paid;
				if ($d_installment_unpaid > 0) {
					$d_total_unpaid += $d_installment_unpaid;
				}
			}
		}
		else if ($mbo_invoice_full_payment) {
			$d_total_unpaid += $mbo_invoice_full_payment->sub_invoice_details_amount_total;
		}
		
		if ($mba_student_data[0]->academic_year_id >= 2021) {
			$b_has_installment = false;
			$d_total_unpaid = 0;
			if ($mba_invoice_installment) {
				$d_invoice_min_payment = 0;
				foreach ($mba_invoice_installment as $o_installment) {
					$d_installment_unpaid = $o_installment->sub_invoice_details_amount_total - $o_installment->sub_invoice_details_amount_paid;
					if ($o_installment->sub_invoice_details_amount_paid > 0) {
						$b_has_installment = true;
					}

					if ($d_installment_unpaid > 0) {
						$d_total_unpaid += $d_installment_unpaid;
					}
				}
			}

			if (!$b_has_installment) {
				if ($mbo_invoice_full_payment) {
					$d_total_unpaid = $mbo_invoice_full_payment->sub_invoice_details_amount_total;
				}
			}
		}

		return $d_total_unpaid;
	}

	private function _get_billing_total($s_va_number) {
		$mba_invoice_list = $this->Im->get_invoice_by_deadline([
			'sid.sub_invoice_details_va_number' => $s_va_number,
			'sid.sub_invoice_details_status != ' => 'paid'
		]);
		$d_total_billing = 0;
		if ($mba_invoice_list) {
			$mba_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $mba_invoice_list[0]->personal_data_id, 'ds.student_status != ' => 'resign']);
			if ($mba_student_data) {
				foreach ($mba_invoice_list as $o_invoice) {
					$mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
					$mbo_invoice_full_payment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
					if ($mba_invoice_installment) {
						foreach ($mba_invoice_installment as $o_installment) {
							if ($o_installment->sub_invoice_details_amount_paid == 0) {
								$d_total_billing += $o_invoice->sub_invoice_details_amount_total;
							}
						}
					}
					else if ($mbo_invoice_full_payment) {
						$d_total_billing += $mbo_invoice_full_payment->sub_invoice_details_amount_total;
					}
				}
			}
		}

		return $d_total_billing;
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
			
			$d_total_invoice_amount = 0;
			$d_total_invoice_fine = 0;
			$mba_invoice_installment = $this->Im->get_invoice_installment($mbo_invoice_detail->invoice_id);
			foreach ($mba_invoice_installment as $o_list_installment) {
				if (($o_list_installment->sub_invoice_details_amount_paid == 0) AND ($o_list_installment->sub_invoice_details_amount_total > 0)) {
					$d_total_invoice_amount += $o_list_installment->sub_invoice_details_amount_total;
					$d_total_invoice_fine += $o_list_installment->sub_invoice_details_amount_fined;
				}
			}
			// $d_total_invoice_amount = $mbo_invoice_detail->sub_invoice_amount_total;
			
			$d_total_amount_paid = $mbo_invoice_detail->sub_invoice_amount_paid;
			$d_total_amount_paid += $d_payment_amount;
			
			// $d_accounts_receiveable = $d_total_invoice_amount - $d_payment_amount;
			$d_accounts_receiveable = $d_total_invoice_amount;
			$a_update_sub_invoice_data = array(
				'sub_invoice_amount_paid' => $d_total_amount_paid,
				// 'sub_invoice_amount_total' => $d_accounts_receiveable,
				'sub_invoice_amount_total' => $d_total_invoice_amount,
				'sub_invoice_datetime_paid_off' => $a_paid_billing['datetime_payment']
			);
			
			if($d_accounts_receiveable == '0'){
				$mbo_full_payment_invoice_data = $this->Im->get_full_payment_invoice_by_invoice_id($mbo_invoice_detail->invoice_id);
				$a_update_sub_invoice_data['sub_invoice_status'] = 'paid';
				$this->Im->update_sub_invoice($a_update_sub_invoice_data, array('invoice_id' => $mbo_invoice_detail->invoice_id));
				
				$a_update_invoice_data = array(
					'invoice_amount_paid' => $d_total_amount_paid,
					'invoice_amount_fined' => 0,
					'invoice_status' => 'paid',
					'invoice_datetime_paid_off' => $a_paid_billing['datetime_payment']
				);
				$this->Im->update_invoice($a_update_invoice_data, array('invoice_id' => $mbo_invoice_detail->invoice_id));

				if (!is_null($mbo_full_payment_invoice_data->trx_id)) {
					$a_update_billing = array(
						'trx_id' => $mbo_full_payment_invoice_data->trx_id,
						'trx_amount' => 999,
						'customer_name' => 'CANCEL PAYMENT',
						'datetime_expired' => '2020-01-01 23:59:59',
						'description' => 'CANCEL PAYMENT'
					);
					$this->Bm->update_billing($a_update_billing);
				}
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
						'sub_invoice_amount' => $d_total_invoice_amount,
						'sub_invoice_amount_total' => $d_total_invoice_amount,
					);
					$this->Im->update_sub_invoice($a_update_sub_invoice_data_total, array('sub_invoice_id' => $mbo_full_payment_invoice_data->sub_invoice_id));

					// $this->Im->update_invoice(['invoice_amount_fined' => $d_new_total_amount_full_payment_fined], ['invoice_id' => $mbo_invoice_detail->invoice_id]);
					
					$b_error_status = ($d_payment_amount == $mbo_invoice_detail->sub_invoice_details_amount_total) ? false : true;
					// $s_paid_status = ($d_payment_amount == $mbo_invoice_detail->sub_invoice_details_amount) ? 'paid' : 'default';
					$s_paid_status = ($d_total_amount_full_payment == '0') ? 'paid' : 'default';
					
					if($b_error_status){
						log_message('error', 'ERROR installment handler from '.__FILE__.' '.__LINE__);

						$this->email->from('employee@company.ac.id');
						$this->email->to(array('employee@company.ac.id'));
						$this->email->subject('ERROR INSTALLMENT HANDLER');
						$this->email->message('check '.__FILE__.' '.__LINE__.'. detail: '.json_encode($mbo_invoice_detail));
						$this->email->send();
					}
					else{
						$a_update_sub_invoice_details_data = array(
							'sub_invoice_details_amount' => ($d_total_invoice_amount - $d_total_invoice_fine),
							'sub_invoice_details_amount_fined' => $d_total_invoice_fine,
							'sub_invoice_details_amount_total' => $d_total_invoice_amount,
							'sub_invoice_details_deadline' => $s_new_full_payment_deadline,
							'sub_invoice_details_description' => $mbo_full_payment_invoice_data->sub_invoice_details_description,
							'sub_invoice_details_status' => $s_paid_status
						);
						$this->Im->update_sub_invoice_details($a_update_sub_invoice_details_data, array('sub_invoice_details_id' => $mbo_full_payment_invoice_data->sub_invoice_details_id));
						
						$a_update_billing = array(
							'trx_id' => $mbo_full_payment_invoice_data->trx_id,
							'trx_amount' => $d_total_invoice_amount,
							'customer_name' => $mbo_full_payment_invoice_data->customer_name,
							'datetime_expired' => date('Y-m-d 23:59:59', strtotime($s_new_full_payment_deadline)),
							'description' => $mbo_full_payment_invoice_data->sub_invoice_details_description
						);
						$this->Bm->update_billing($a_update_billing);
					}
				}
			}

		}
		else {
			$this->send_error_notification('error payment '.json_encode($a_body));

			$this->email->to(array('employee@company.ac.id'));
			$this->email->subject('[Error Payment]Payment and Billing not same');
			$this->email->message(json_encode($a_paid_billing));
			$this->email->send();
		}
	}
	
	private function handle_full_payment($mbo_invoice_detail, $a_paid_billing)
	{
		$this->db->trans_start();
		$d_payment_amount = $a_paid_billing['payment_amount'];
		// if(
		// 	($d_payment_amount == $mbo_invoice_detail->sub_invoice_details_amount_total) AND 
		// 	($d_payment_amount == $mbo_invoice_detail->sub_invoice_amount_total)
		// )
		// {
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
		// }
		// else{
		// 	$this->db->trans_rollback();
		// 	$this->send_error_notification('error payment full '.json_encode($a_paid_billing));

		// 	$this->email->to(array('employee@company.ac.id'));
		// 	$this->email->subject('[Error Payment]Payment and Billing not same');
		// 	$this->email->message(json_encode($a_paid_billing));
		// 	$this->email->send();
		// 	return false;
		// }
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
		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to($mbo_student_data->student_email);
		// $this->email->to('employee@company.ac.id');
		
		if($mba_parent_email){
			$this->email->cc($mba_parent_email);
		}
		
		$this->email->bcc([
			$a_email['finance']['payment'],
			'employee@company.ac.id',
			'employee@company.ac.id',
			'employee@company.ac.id'
		]);
		$this->email->subject("[IULI] Graduation Payment Confirmation");
		$this->email->message($t_body);
		$this->email->send();
	}

	public function test_va()
	{
		$s_virtual_account = '8310020651180312';
		$s_installment = intval(substr($s_virtual_account, 6, 2));
		$this->send_notification_telegram($s_installment);
		print($s_installment);exit;
	}

	public function handle_other_payment($mbo_invoice_detail, $a_paid_billing)
	{
		$s_program_code = intval(substr($mbo_invoice_detail->virtual_account, 8, 2));
		$mba_partner_data = $this->Psm->get_partner_program([
			'program_id' => $s_program_code
		]);

		// if (!$mba_partner_data) {
		// 	$s_message = 'Partner data not found '.$s_program_code.'! '.__FILE__.' '.__LINE__;
		// 	$this->send_notification_telegram($s_message);
		// }
		// else {
			// $o_partner = $mba_partner_data[0];
			// switch ($o_partner->partner_code) {
			// 	case '01':
					$this->handle_srh_payment($mbo_invoice_detail, $a_paid_billing);
			// 		break;
				
			// 	default:
			// 		$s_message = 'Partner code not found! '.__FILE__.' '.__LINE__;
			// 		$this->send_notification_telegram($s_message);
			// 		break;
			// }
		// }
	}

	public function handle_srh_payment($mbo_invoice_detail, $a_paid_billing)
	{
		$this->load->model('student/Student_model', 'Sm');
		// $this->load->model('personal_data/Personal_data_model', 'Pdm');

		$s_personal_data_id = $mbo_invoice_detail->personal_data_id;
		$mbo_partner_student_data = $this->Psm->get_partner_student_data(['sn.personal_data_id' => $s_personal_data_id]);
		// $mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		$mba_already_processing = $this->General->get_where('dt_sub_invoice_details', [
			'sub_invoice_details_va_number' => $a_paid_billing['virtual_account'],
			'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
		]);

		if ($mbo_partner_student_data) {
			$o_partner_student_data = $mbo_partner_student_data[0];
			$a_email = $this->config->item('email');

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
			}

			$s_trx_amount = "Rp. ". number_format($a_paid_billing['payment_amount'], 0, ',', '.') .",-";
			$s_date_payment = date('d F Y H:i:s', strtotime($a_paid_billing['datetime_payment']));

			$t_body = <<<TEXT
Dear {$o_partner_student_data->personal_data_name},

This email is to confirm that we have received your payment on {$s_date_payment} for amount of {$s_trx_amount} for your {$mbo_invoice_detail->sub_invoice_details_description} fee

Best Regards,
Finance Team

International University Liaison Indonesia
Associate Tower 7th Floor.
Intermark Indonesia BSD
Jl. Lingkar Timur BSD Serpong
Tangerang Selatan 15310
Indonesia
Email: employee@company.ac.id
TEXT;
			// $s_email = 'employee@company.ac.id';
			$s_email = $o_partner_student_data->personal_data_email;
			$a_cc = [
				'employee@company.ac.id',
			];

			$a_bcc = [
				$a_email['finance']['payment'],
				'employee@company.ac.id',
				'employee@company.ac.id'
			];

			// $config = $this->config->item('mail_config');
			// $config['mailtype'] = 'html';
			// $this->email->initialize($config);

			$this->email->from('employee@company.ac.id', 'IULI Finance Team');
			$this->email->to($s_email);
			$this->email->cc($a_cc);
			$this->email->bcc($a_bcc);
			$this->email->subject("[SRH] Payment Confirmation");
			$this->email->message($t_body);
			$this->email->send();
		}
	}

	public function send_open_payment()
	{
		print('function closed!!!');exit;
		$s_invoice_id = '9a600462-8d0a-4c21-aaaa-d6842984f046';
		$mbo_invoice_detail = $this->Im->get_invoice_detail_open_payment('237058677', '4000001');
		$a_parsed_data = [
			'trx_id' => '237058677',
			'customer_name' => 'Sponsorship IULIFest',
			'virtual_account' => '8310884920220619',
			'trx_amount' => '0',
			'payment_amount' => '4000001',
			'cumulative_payment_amount' => '4000001',
			'payment_ntb' => '274335',
			'datetime_payment' => '2022-06-21 14:44:55',
			'datetime_payment_iso8601' => '2022-06-21T14:44:55+07:00'
		];
		
		$this->handle_open_payment($mbo_invoice_detail, $a_parsed_data);
	}

	public function handle_open_payment($mbo_invoice_detail, $a_paid_billing)
	{
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		
		$s_personal_data_id = $mbo_invoice_detail->personal_data_id;
		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		$s_trx_amount = "Rp. ". number_format($a_paid_billing['payment_amount'], 0, ',', '.') .",-";
		$s_payment_type_code = substr($mbo_invoice_detail->virtual_account, 4, 2);

		// if ($s_payment_type_code == '88') {
		// 	if ($a_paid_billing['payment_amount'] == $mbo_invoice_detail->sub_invoice_amount_total) {
		// 		$a_sub_invoice_details_update = [
		// 			'sub_invoice_details_amount_paid' => $a_paid_billing['payment_amount'],
		// 			'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment'],
		// 			'sub_invoice_details_status' => 'paid'
		// 		];

		// 		$a_sub_invoice_update = [
		// 			'sub_invoice_amount_paid' => $a_paid_billing['payment_amount'],
		// 			'sub_invoice_datetime_paid_off' => $a_paid_billing['datetime_payment'],
		// 			'sub_invoice_status' => 'paid'
		// 		];

		// 		$a_invoice_update = [
		// 			'invoice_status' => 'paid',
		// 			'invoice_datetime_paid_off' => $a_paid_billing['datetime_payment'],
		// 			'invoice_amount_paid' => $a_paid_billing['payment_amount']
		// 		];

		// 		$this->Im->update_invoice($a_invoice_update, ['invoice_id' => $mbo_invoice_detail->invoice_id]);
		// 		$this->Im->update_sub_invoice($a_sub_invoice_update, ['sub_invoice_id' => $mbo_invoice_detail->sub_invoice_id]);
		// 		$this->Im->update_sub_invoice_details($a_sub_invoice_details_update, ['sub_invoice_details_id' => $mbo_invoice_detail->sub_invoice_details_id]);
		// 	}
		// }

		$t_body = <<<TEXT
Dear {$mbo_personal_data->personal_data_name},

This email is to confirm that we have received your contribution on {$a_paid_billing['datetime_payment']} for amount of {$s_trx_amount}

Best Regards,
IULIFest

International University Liaison Indonesia
Associate Tower 7th Floor.
Intermark Indonesia BSD
Jl. Lingkar Timur BSD Serpong
Tangerang Selatan 15310
Indonesia
Email: employee@company.ac.id
TEXT;
		$s_email = $mbo_personal_data->personal_data_email;
		// $s_email = 'employee@company.ac.id';
		// print($s_email);
		$a_email = $this->config->item('email');
		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);

		$this->email->from('employee@company.ac.id', 'IULIFest');
		$this->email->to($s_email);
		
		$a_bcc = [
			$a_email['finance']['payment'],
			'employee@company.ac.id',
			'employee@company.ac.id'
		];

		$a_ccmail = [
			'employee@company.ac.id',
			'employee@company.ac.id'
		];

		$s_payment_type = substr($mbo_invoice_detail->virtual_account, 4, 2);
		
		$this->email->bcc($a_bcc);
		$this->email->cc($a_ccmail);

		$this->email->subject("[IULI] Payment Confirmation - IULIFest");
		$this->email->message($t_body);
		$this->email->send();
	}

	public function handle_open_payment_research($mbo_invoice_detail, $a_paid_billing)
	{
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		
		// $s_personal_data_id = $mbo_invoice_detail->personal_data_id;
		// $mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		$s_trx_amount = "Rp. ". number_format($a_paid_billing['payment_amount'], 0, ',', '.') .",-";
		$s_payment_type_code = substr($mbo_invoice_detail->virtual_account, 4, 2);

		// if ($s_payment_type_code == '88') {
		// 	if ($a_paid_billing['payment_amount'] == $mbo_invoice_detail->sub_invoice_amount_total) {
		// 		$a_sub_invoice_details_update = [
		// 			'sub_invoice_details_amount_paid' => $a_paid_billing['payment_amount'],
		// 			'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment'],
		// 			'sub_invoice_details_status' => 'paid'
		// 		];

		// 		$a_sub_invoice_update = [
		// 			'sub_invoice_amount_paid' => $a_paid_billing['payment_amount'],
		// 			'sub_invoice_datetime_paid_off' => $a_paid_billing['datetime_payment'],
		// 			'sub_invoice_status' => 'paid'
		// 		];

		// 		$a_invoice_update = [
		// 			'invoice_status' => 'paid',
		// 			'invoice_datetime_paid_off' => $a_paid_billing['datetime_payment'],
		// 			'invoice_amount_paid' => $a_paid_billing['payment_amount']
		// 		];

		// 		$this->Im->update_invoice($a_invoice_update, ['invoice_id' => $mbo_invoice_detail->invoice_id]);
		// 		$this->Im->update_sub_invoice($a_sub_invoice_update, ['sub_invoice_id' => $mbo_invoice_detail->sub_invoice_id]);
		// 		$this->Im->update_sub_invoice_details($a_sub_invoice_details_update, ['sub_invoice_details_id' => $mbo_invoice_detail->sub_invoice_details_id]);
		// 	}
		// }

		$t_body = <<<TEXT
Dear Mrs. Normalisa,

This email is to confirm that we have received your contribution on {$a_paid_billing['datetime_payment']} for amount of {$s_trx_amount}

Best Regards,
IULIResearch

International University Liaison Indonesia
Associate Tower 7th Floor.
Intermark Indonesia BSD
Jl. Lingkar Timur BSD Serpong
Tangerang Selatan 15310
Indonesia
Email: employee@company.ac.id
TEXT;
		$s_email = $mbo_personal_data->personal_data_email;
		$s_email = 'employee@company.ac.id';
		// print($s_email);
		$a_email = $this->config->item('email');
		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);

		$this->email->from('employee@company.ac.id', 'IULIResearch');
		$this->email->to($s_email);
		
		$a_bcc = [
			$a_email['finance']['payment'],
			'employee@company.ac.id',
			'employee@company.ac.id'
		];

		$a_ccmail = [
			'employee@company.ac.id',
			'employee@company.ac.id'
		];

		$s_payment_type = substr($mbo_invoice_detail->virtual_account, 4, 2);
		
		// $this->email->bcc($a_bcc);
		$this->email->cc($a_ccmail);

		$this->email->subject("[IULI] Payment Confirmation - IULIResearch");
		$this->email->message($t_body);
		$this->email->send();
	}

	public function handle_single_payment_fee($mba_invoice_data, $a_paid_billing)
	{
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');

		$s_personal_data_id = $mba_invoice_data[0]->personal_data_id;
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);

		$mba_already_processing = $this->General->get_where('dt_sub_invoice_details', [
			'sub_invoice_details_va_number' => $a_paid_billing['virtual_account'],
			'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
		]);

		if (!$mba_already_processing) {
			$this->handle_open_minimum_payment($mba_invoice_data, $a_paid_billing);
			$bni_transaction_data = $this->Fim->get_bni_transactions([
				'trx_id' => $a_paid_billing['trx_id'],
				'transaction_type' => 'paymentnotification',
				'payment_ntb' => $a_paid_billing['payment_ntb'],
				'datetime_payment' => $a_paid_billing['datetime_payment']
			], 'date_added', 'DESC');
			$s_bni_id= ($bni_transaction_data) ? $bni_transaction_data[0]->bni_transactions_id : false;

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

This email is to confirm that we have received your payment on {$a_paid_billing['datetime_payment']} for amount of {$s_trx_amount} for your {$mba_invoice_data[0]->sub_invoice_details_description} fee

Best Regards,
Finance Team

International University Liaison Indonesia
Associate Tower 7th Floor.
Intermark Indonesia BSD
Jl. Lingkar Timur BSD Serpong
Tangerang Selatan 15310
Indonesia
Email: employee@company.ac.id
TEXT;

			if ($s_bni_id) {
				$receipt = modules::run('download/pdf_download/generate_receipt', $s_bni_id);
				if ($receipt) {
					$s_receipt_path = $receipt['filepath'];
					$s_receipt_file = $receipt['filename'];
					
					if(file_exists($s_receipt_path.$s_receipt_file)){
						$this->email->attach($s_receipt_path.$s_receipt_file);
					}
				}
			}

			$s_email = (!is_null($mbo_student_data->student_alumni_email)) ? $mbo_student_data->student_alumni_email : $mbo_student_data->student_email;
			// $s_email = 'employee@company.ac.id';
			// print($s_email);
			$a_email = $this->config->item('email');
			// $config = $this->config->item('mail_config');
			// $config['mailtype'] = 'html';
			// $this->email->initialize($config);

			
			$a_bcc = [
				$a_email['finance']['payment'],
				'employee@company.ac.id',
				'employee@company.ac.id'
			];

			$s_payment_type = substr($a_paid_billing['virtual_account'], -2);
			if ($s_payment_type == '13') {
				array_push($a_bcc, $this->config->item('email')['academic']['head']);
			}
			else if ($s_payment_type == '09') {
				array_push($a_bcc, 'employee@company.ac.id');
			}
			else if ($s_payment_type == '14') {
				if($mba_parent_email){
					array_push($mba_parent_email, 'employee@company.ac.id');
					array_push($mba_parent_email, $mbo_personal_data->personal_data_email);
				}
				else {
					$mba_parent_email = ['employee@company.ac.id', $mbo_personal_data->personal_data_email];
				}
			}

			if($mba_parent_email){
				$this->email->cc($mba_parent_email);
			}
			
			$this->email->from('employee@company.ac.id', 'IULI Finance Team');
			$this->email->to($s_email);
			$this->email->bcc($a_bcc);

			if ($s_bni_id) {
				$receipt = modules::run('download/pdf_download/generate_receipt', $s_bni_id);
				if ($receipt) {
					$s_receipt_path = $receipt['filepath'];
					$s_receipt_file = $receipt['filename'];
					
					if(file_exists($s_receipt_path.$s_receipt_file)){
						$this->email->attach($s_receipt_path.$s_receipt_file);
					}
				}
			}

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
		
		switch($mbo_invoice_detail->sub_invoice_type)
		{
			case "full":
				$this->handle_full_payment($mbo_invoice_detail, $a_paid_billing);
				break;
				
			case "installment":
				$this->handle_installment_payment($mbo_invoice_detail, $a_paid_billing);
				break;
		}

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
		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to($mbo_student_data->student_email);
		// $this->email->to('employee@company.ac.id');
		
		if($mba_parent_email){
			$this->email->cc($mba_parent_email);
		}
		
		$this->email->bcc([
			$a_email['finance']['payment'],
			'employee@company.ac.id',
			'employee@company.ac.id'
		]);
		$this->email->subject("[IULI] Payment Confirmation");
		$this->email->message($t_body);
		$this->email->send();
	}

	public function force_payment($s_trx_id, $s_payment_amount)
	{
		$mbo_invoice_detail = $this->Im->get_detail_invoice_by_trx_id($s_trx_id);
		if($mbo_invoice_detail){
			$s_payment_type_code = substr($mbo_invoice_detail->virtual_account, 4, 2);
			if ($s_payment_type_code == '88') {
				$mbo_invoice_detail = $this->Im->get_invoice_detail_open_payment($s_trx_id, $s_payment_amount);
			}
			else {
				// $this->Bm->update_billing_portal($s_trx_id, $a_parsed_data);
				print('salah jalur!');exit;
			}

			if($mbo_invoice_detail) {
				print('ada');exit;
			}
		}
		else {
			print('<pre>');
			var_dump($mbo_invoice_detail);exit;
		}
	}
	
	public function handle_repetition_fee($mbo_invoice_detail, $a_paid_billing)
	{
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');
		
		$s_personal_data_id = $mbo_invoice_detail->personal_data_id;
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		
		switch($mbo_invoice_detail->sub_invoice_type)
		{
			case "full":
				$this->handle_full_payment($mbo_invoice_detail, $a_paid_billing);
				break;
				
			case "installment":
				$this->handle_installment_payment($mbo_invoice_detail, $a_paid_billing);
				break;
		}
		
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
		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
		
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to($mbo_student_data->student_email);
		
		if($mba_parent_email){
			$this->email->cc($mba_parent_email);
		}
		
		$this->email->bcc([
			$a_email['finance']['payment'],
			'employee@company.ac.id',
			'employee@company.ac.id'
		]);
		$this->email->subject("[IULI] Repetition Exam Payment Confirmation");
		$this->email->message($t_body);
		$this->email->send();
	}

	// public function send_custom_tuition_fee_and_sgs($s_personal_data_id)
	// {
	// 	$this->load->model('student/Student_model', 'Sm');
	// 	$this->load->model('personal_data/Personal_data_model', 'Pdm');
	// 	$this->load->model('personal_data/Family_model', 'Fm');

	// 	$o_family_data = $this->Fm->get_family_by_personal_data_id($s_personal_data_id);
	// 	$mba_family_members = $this->Fm->get_family_members($o_family_data->family_id, array(
	// 		'family_member_status != ' => 'child'
	// 	));
	// 	$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);

	// 	$mba_parent_email = false;
	// 	if($mba_family_members){
	// 		$mba_parent_email = array();
	// 		foreach($mba_family_members as $family){
	// 			array_push($mba_parent_email, $family->personal_data_email);
	// 		}
	// 	}
		
	// 	if(is_null($mbo_personal_data->personal_data_reference_code)){
	// 		modules::run('personal_data/create_reference_code', $s_personal_data_id);
			
	// 		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
	// 		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
			
	// 		modules::run('student/payment_confirmation_sgs', 
	// 			$mbo_personal_data->personal_data_name, 
	// 			$mbo_personal_data->personal_data_reference_code,
	// 			$mbo_personal_data->personal_data_email,
	// 			$mba_parent_email
	// 		);

	// 		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
	// 		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
			
	// 		$d_payment_amount = '17000000';
	// 		$s_amount = "Rp. ".number_format($d_payment_amount, 0, ',', '.').",-";

	// 		$this->tuition_fee_payment_notification(
	// 			$mbo_personal_data->personal_data_name, 
	// 			$mbo_student_data->student_email, 
	// 			'2021-02-27 09:32:00', 
	// 			$s_amount,
	// 			$mba_parent_email,
	// 			true
	// 		);
	// 		print('<pre>');
	// 		var_dump($mba_family_members);exit;
	// 	}else{
	// 		print('kosong');
	// 	}
	// }

	public function handle_tuition_fee($mba_invoice_data, $a_paid_billing)
	{
		$o_invoice_data = $mba_invoice_data[0];
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');
		$s_personal_data_id = $o_invoice_data->personal_data_id;
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		$mba_already_processing = $this->General->get_where('dt_sub_invoice_details', [
			'sub_invoice_details_va_number' => $a_paid_billing['virtual_account'],
			'sub_invoice_details_datetime_paid_off' => $a_paid_billing['datetime_payment']
		]);

		$b_admission_bcc = false;
		if (!$mba_already_processing) {
            $this->handle_open_minimum_payment($mba_invoice_data, $a_paid_billing);
		}
		// else {
		// 	print('already in'.$mba_already_processing[0]->sub_invoice_details_id);exit;
		// }

		$bni_transaction_data = $this->Fim->get_bni_transactions([
			'trx_id' => $a_paid_billing['trx_id'],
			'transaction_type' => 'paymentnotification',
			'payment_ntb' => $a_paid_billing['payment_ntb'],
			'datetime_payment' => $a_paid_billing['datetime_payment']
		], 'date_added', 'DESC');
		$s_bni_id= ($bni_transaction_data) ? $bni_transaction_data[0]->bni_transactions_id : false;
		if(($mbo_student_data->student_status != 'resign') AND (is_null($mbo_student_data->student_email))){
			$b_admission_bcc = true;
			modules::run('student/create_student_email', $mbo_student_data);
		}
		if(($mbo_student_data->student_status != 'resign') AND (is_null($mbo_student_data->student_number))){
			$b_admission_bcc = true;
			modules::run('student/create_student_number', $mbo_student_data);
		}

		if ($o_invoice_data->invoice_admission_reminder == 'yes') {
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
			
			// modules::run('student/payment_confirmation_sgs', 
			// 	$mbo_personal_data->personal_data_name, 
			// 	$mbo_personal_data->personal_data_reference_code,
			// 	$mbo_personal_data->personal_data_email,
			// 	$mba_parent_email
			// );
		}
		
		
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
		$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
		$mbo_invoice_fee_details = $this->Im->student_has_invoice_list($s_personal_data_id, ['di.invoice_id' => $o_invoice_data->invoice_id]);
		
		$d_payment_amount = $a_paid_billing['payment_amount'];
		$s_amount = "Rp. ".number_format($d_payment_amount, 0, ',', '.').",-";
		$s_payment_type_code = substr($a_paid_billing['virtual_account'], -2);
		$s_semester = ($mbo_invoice_fee_details) ? $mbo_invoice_fee_details[0]->semester_number : '';

		// $mba_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $s_semester]);

		$s_personal_data_name = $mbo_personal_data->personal_data_name;
		$s_fee_desc = '';
		if ((!is_null($mbo_student_data->study_program_abbreviation)) AND (!is_null($mbo_student_data->academic_year_id))) {
			$s_personal_data_name.' ('.$mbo_student_data->study_program_abbreviation.'/'.$mbo_student_data->academic_year_id.')';
		}

		if (($mbo_student_data) AND ($mbo_student_data->student_email == 'estrella.natalia@stud.iuli.ac.id')) {
			if ($mba_parent_email) {
				$mba_parent_email = false;
			}
		}
		
		$b_is_special = false;
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
				// $semester = ($mba_semester_data) ? $mba_semester_data[0]->semester_number : '';
				// if ($i_installment > 0) {
					// $s_fee_desc = 'semester '.$s_semester;
				// }
				// else {
				// 	$s_fee_desc = 'semester '.$s_semester;
				// }

				if ($a_paid_billing['virtual_account'] == '8310020601200204') {
					$b_is_special = true;
				}

				$this->tuition_fee_payment_notification(
					$s_personal_data_name,
					$mbo_student_data->student_email,
					$a_paid_billing['datetime_payment'],
					$s_amount,
					$mba_parent_email,
					$b_admission_bcc,
					$s_fee_desc,
					$b_is_special,
					$s_bni_id
				);
			}
		// }
	}
	
	public function handle_enrollment_fee($mba_invoice_detail, $a_paid_billing)
	{
		$mbo_invoice_detail = $mba_invoice_detail[0];
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');
		$s_personal_data_id = $mbo_invoice_detail->personal_data_id;
		
		if($this->handle_full_payment($mbo_invoice_detail, $a_paid_billing)){
			
			$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);
			
			$a_update_student_data = array(
				'has_to_pay_enrollment_fee' => 'yes'
			);
			$this->Sm->update_student_data($a_update_student_data, $mbo_student_data->student_id);
			$bni_transaction_data = $this->Fim->get_bni_transactions([
				'trx_id' => $a_paid_billing['trx_id'],
				'transaction_type' => 'paymentnotification',
				'payment_ntb' => $a_paid_billing['payment_ntb'],
				'datetime_payment' => $a_paid_billing['datetime_payment']
			], 'date_added', 'DESC');
			$s_bni_id = false;
			if ($bni_transaction_data) {
				$s_bni_id = $bni_transaction_data[0]->bni_transactions_id;
				$a_payment_history = [
					'payment_id' => $this->uuid->v4(),
					'personal_data_id' => $mbo_invoice_detail->personal_data_id,
					'bni_transactions_id' => $bni_transaction_data[0]->bni_transactions_id,
					'sub_invoice_details_id' => $mbo_invoice_detail->sub_invoice_details_id,
					'payment_amount' => $mbo_invoice_detail->sub_invoice_details_amount_total,
					'date_added' => date('Y-m-d H:i:s')
				];
				$this->General->insert_data('bni_transactions_payment', $a_payment_history);
			}
			
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
				'data' => array('has_to_pay_enrollment_fee' => 'yes'),
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
				$mba_parent_email,
				$s_bni_id
			);

			$this->send_invitation_online_test($mbo_personal_data->personal_data_id);
			
			$url = $s_site.'api/portal/sync_all';
			$result = $this->libapi->post_data($url, $post_data);
			/**
			* handle sync data
			**/
			
			return true;
		}
	}

	public function send_error_notification($s_message = false)
	{
		if ($s_message) {
			if (is_string($s_message)) {
				$this->General->send_notification('914601915', $s_message, '1939561487', 'AAFmY6XTaT5WSqnCoj-tOVkSS2Um7d1caoM');
			}
		}
	}

	// function notification_from_collection() {
	// 	$s_data = file_get_contents('php://input');
	// 	$a_data_json = json_decode($s_data, true);
	// 	if (array_key_exists($a_data_json['client_id'], $this->a_client_ids)) {
	// 		$this->set_environment($this->a_client_ids[$a_data_json['client_id']]);
			
	// 		$a_parsed_data = $this->libapi->parse_data($a_data_json['data'], $this->s_client_id, $this->s_secret_key);
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

	// 		$mbo_invoice_detail = $this->Im->get_detail_invoice_by_trx_id($a_parsed_data['trx_id']);
	// 		if ($this->a_client_ids[$a_data_json['client_id']] == 'production') {
	// 			$s_storefile_json = APPPATH."uploads/bni/bni-dev/bni_failover_".date('Y_m_d', strtotime($a_parsed_data['datetime_payment'])).".json";
	// 			if(!file_exists($s_storefile_json)){
	// 				file_put_contents($s_storefile_json, json_encode([]));
	// 			}
				
	// 			$s_bni_failover_json = file_get_contents($s_storefile_json);
	// 			$a_bni_failover = json_decode($s_bni_failover_json);
				
	// 			array_push($a_bni_failover, $a_parsed_data);
	// 			file_put_contents($s_storefile_json, json_encode($a_bni_failover));
	// 		}

	// 		// 
	// 	}
	// }

	function test_parsing() {
		$this->set_environment($this->a_client_ids[141]);
		// $string = '{"client_id":"141","data":"GRkjSUskQhMkRRYUOXlgAl5XSwpVXHhJBkNiA3kGCENMTlgOAV03HQxPGkhMRhhICxQPeV5cB1YGR1h4AnYESQoeC2lqPlYrOGgLZFhmLTcYCk56XUsKUANKRQZ4DgFMVlUOUDUeQhUgQRlKREggNRobJEodIEgiOA4LenUMeVhUTk54A0oOUE8FXHUDCVdQHxgeOCMLRRdLFRNKRUJJGTwVGVBKGkwbIj8bTVFJGDcYClp6YlN7VQpERwMGCgJbChsOR0McQgUZNlh6EANQA11KWApOCE0MSRMfTEhMNhANVVsRckoCUl8FXDhUOBlIGQoZOF1bC0YCRghTNk5IHSEbHE9NHDQSDApUCAsOSQFLSU18WFsEWzgfCE1ISUUYGRMeRkQcQxUdRBo7FA"}';
		$string = '{"cumulative_payment_amount":"8222000","customer_name":"STUDENT NAME","datetime_payment":"2023-11-25 10:41:58","datetime_payment_iso8601":"2023-11-25T10:41:58+07:00","payment_amount":"1000","payment_ntb":"316644","trx_amount":"100","trx_id":"919973973","virtual_account":"7141112201012002"}';
		// $a_parsed_data = $this->libapi->parse_data($string, $this->s_client_id, $this->s_secret_key);
		$a_parsed_data = $this->libapi->hash_data((array) json_decode($string), $this->s_client_id, $this->s_secret_key);
		print("<pre>");var_dump($a_parsed_data);exit;
	}

	// function sebentar() {
	// 	$a_parsed_data = '{"trx_id":"1855404489","virtual_account":"8310112312006003","customer_name":"MUHAMMAD RAIHAN RUSLI","trx_amount":"1200000","payment_amount":"1200000","cumulative_payment_amount":"1200000","payment_ntb":"272990","datetime_payment":"2024-01-26 13:41:42","datetime_payment_iso8601":"2024-01-26T13:41:42+07:00"}';
	// 	$a_parsed_data = (array) json_decode($a_parsed_data);
	// 	$mba_invoice_list = $this->Im->get_invoice_data([
	// 		'dsid.trx_id' => $a_parsed_data['trx_id'],
	// 		// 'dsid.sub_invoice_details_va_number' => $a_parsed_data['virtual_account'],
	// 		'dsid.sub_invoice_details_status != ' => 'paid'
	// 	]);
	// 	// print('<pre>');var_dump($mba_invoice_list);exit;
	// 	if($mba_invoice_list){
	// 		$s_payment_type_code = substr($a_parsed_data['virtual_account'], -2);
	// 		switch($s_payment_type_code)
	// 		{
	// 			case "01":
	// 				$this->handle_enrollment_fee($mba_invoice_list, $a_parsed_data);
	// 				break;
					
	// 			case "02":
	// 				$this->handle_tuition_fee($mba_invoice_list, $a_parsed_data);
	// 				break;
					
	// 			case "18":
	// 				$this->handle_single_payment_fee($mba_invoice_list, $a_parsed_data);
	// 				break;
	// 			default:
	// 				$this->handle_single_payment_fee($mba_invoice_list, $a_parsed_data);
	// 				break;
	// 		}
	// 		print('<pre>');var_dump($a_parsed_data);exit;
	// 	}
	// 	else{
	// 		$this->send_error_notification(json_encode($a_parsed_data));

	// 		$this->email->from('employee@company.ac.id', 'ERROR Notification');
	// 		$this->email->to(array('employee@company.ac.id'));
	// 		$this->email->subject('[ERROR_Payment]ERROR TRX NOT FOUND');
	// 		$this->email->message(json_encode($a_parsed_data));
	// 		$this->email->send();
	// 	}
	// }

	public function bni_payment_notification()
	{
		$s_data = file_get_contents('php://input');
		$a_data_json = json_decode($s_data, true);
		// $a_data_json = [
		// 	'client_id' => '141',
		// 	// 'data' => json_decode('')
		// ];
		
		if(array_key_exists($a_data_json['client_id'], $this->a_client_ids)){
			$this->set_environment($this->a_client_ids[$a_data_json['client_id']]);
			
			$a_parsed_data = $this->libapi->parse_data($a_data_json['data'], $this->s_client_id, $this->s_secret_key);
			// print('<pre>');var_dump($a_parsed_data);exit;
			// $a_parsed_data = '{"trx_id":"919973973","virtual_account":"7141112201012002","customer_name":"STUDENT NAME","trx_amount":"100","payment_amount":"10000","cumulative_payment_amount":"8210000","payment_ntb":"466384","datetime_payment":"2023-11-25 10:14:37","datetime_payment_iso8601":"2023-11-25T10:14:37+07:00"}';
			// $a_parsed_data = (array) json_decode($a_parsed_data);
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
            if ($this->a_client_ids[$a_data_json['client_id']] == 'production') {
                $s_storefile_json = APPPATH."uploads/bni/bni-dev/bni_failover_".date('Y_m_d', strtotime($a_parsed_data['datetime_payment'])).".json";
                if(!file_exists($s_storefile_json)){
                    file_put_contents($s_storefile_json, json_encode([]));
                }
                
                $s_bni_failover_json = file_get_contents($s_storefile_json);
                $a_bni_failover = json_decode($s_bni_failover_json);
                
                array_push($a_bni_failover, $a_parsed_data);
                file_put_contents($s_storefile_json, json_encode($a_bni_failover));
            }

			$mba_already_processing = $this->General->get_where('dt_sub_invoice_details', [
				'sub_invoice_details_va_number' => $a_parsed_data['virtual_account'],
				'sub_invoice_details_datetime_paid_off' => $a_parsed_data['datetime_payment']
			]);
			if (!$mba_already_processing) {
				$this->Bm->payment_notification([
					// 'personal_data_id' => $mbo_invoice_detail->personal_data_id,
					'trx_id' => $a_parsed_data['trx_id'],
					'trx_amount' => $a_parsed_data['trx_amount'],
					'client_id' => $a_data_json['client_id'],
					'transaction_type' => 'paymentnotification',
					'datetime_payment' => $a_parsed_data['datetime_payment'],
					'customer_name' => $a_parsed_data['customer_name'],
					'virtual_account' => $a_parsed_data['virtual_account'],
					'payment_ntb' => $a_parsed_data['payment_ntb'],
					'payment_amount' => $a_parsed_data['payment_amount'],
					'cumulative_payment_amount' => $a_parsed_data['cumulative_payment_amount']
				]);
			}
			
            $mba_invoice_list = $this->Im->get_invoice_data([
                'dsid.trx_id' => $a_parsed_data['trx_id'],
                // 'dsid.sub_invoice_details_va_number' => $a_parsed_data['virtual_account'],
                'dsid.sub_invoice_details_status != ' => 'paid',
				'di.invoice_status != ' => 'paid'
            ]);
			if($mba_invoice_list){
				$s_payment_type_code = substr($a_parsed_data['virtual_account'], -2);
				switch($s_payment_type_code)
                {
                    case "01":
                        $this->handle_enrollment_fee($mba_invoice_list, $a_parsed_data);
                        break;
                        
                    case "02":
                        $this->handle_tuition_fee($mba_invoice_list, $a_parsed_data);
                        break;
                        
                    case "18":
                        $this->handle_single_payment_fee($mba_invoice_list, $a_parsed_data);
                        break;
                    
                    // case "04":
                    //     $this->handle_short_semester_fee($mba_invoice_list, $a_parsed_data);
                    //     break;

                    // case "05":
                    //     $this->handle_single_payment_fee($mba_invoice_list, $a_parsed_data);
                    //     break;
                        
                    // case "07":
                    //     $this->handle_single_payment_fee($mba_invoice_list, $a_parsed_data);
                    //     break;
                        
                    // case "09":
                    //     $this->handle_graduation_fee($mba_invoice_list, $a_parsed_data);
                    //     break;

                    // // case "10":
                    // //     $this->handle_tuition_fee($mba_invoice_list, $a_parsed_data);
                    // //     break;

                    // case "11":
                    //     $this->handle_single_payment_fee($mba_invoice_list, $a_parsed_data);
                    //     break;
                    
                    // case "12":
                    //     $this->handle_single_payment_fee($mba_invoice_list, $a_parsed_data);
                    //     break;

                    // case "13":
                    //     $this->handle_single_payment_fee($mba_invoice_list, $a_parsed_data);
                    //     break;

                    // case "14":
                    //     $this->handle_single_payment_fee($mba_invoice_list, $a_parsed_data);
                    //     break;

                    // case "88":
                    //     $this->handle_open_payment($mba_invoice_list, $a_parsed_data);
                    //     break;

                    // case "50":
                    //     $this->handle_open_payment_research($mba_invoice_list, $a_parsed_data);
                    //     break;

                    // case "21":
                    //     $this->handle_srh_payment($mba_invoice_list, $a_parsed_data);
                    //     break;

                    default:
                        $this->handle_single_payment_fee($mba_invoice_list, $a_parsed_data);
                        break;
                }
			}
			else{
				$this->send_error_notification(json_encode($a_parsed_data));

				$this->email->from('employee@company.ac.id', 'ERROR Notification');
				$this->email->to(array('employee@company.ac.id'));
				$this->email->subject('[ERROR_Payment]ERROR TRX NOT FOUND');
				$this->email->message(json_encode($a_parsed_data));
				$this->email->send();
			}
		}
	}
}