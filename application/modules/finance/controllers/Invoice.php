<?php
class Invoice extends App_core
{
	public $d_additional_fee_installment = 500000;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Invoice_model', 'Im');
		$this->load->model('finance/Finance_model', 'Finm');
		$this->load->model('finance/Bni_model', 'Bnim');
		$this->load->model('admission/Admission_model', 'Adm');
		$this->load->model('academic/Semester_model', 'Sem');
		$this->load->model('student/Student_model', 'Stm');
		$this->load->model('academic/Score_model', 'Scm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');
		$this->load->model('academic/Academic_year_model', 'Aym');
		$this->load->model('partner/Partner_student_model', 'Psm');
	}

	function get_payment_history() {
		if ($this->input->is_ajax_request()) {
			$a_clause = [
				'bt.transaction_type' => 'paymentnotification'
			];

			$mba_data = $this->Finm->get_payment_history($a_clause, [
                'bt.datetime_payment' => 'DESC',
            ], ['bt.bni_transactions_id']);
            print json_encode(['data' => $mba_data]);
			// print('<pre>');var_dump($this->input->post());exit;
		}
	}

	function payment_lists() {
		$this->a_page_data['body'] = $this->load->view('table/payment_list', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	function get_receipt($bni_id, $s_filename = false) {
		$receipt = modules::run('download/pdf_download/generate_receipt', $bni_id);
		if ($receipt) {
			$s_filename = (!$s_filename) ? $receipt['filename'] : $s_filename;
			$s_files = $receipt['filename'];
			$s_filepath = $receipt['filepath'];
			$s_mime = mime_content_type($s_filepath.$s_files);
			header("Content-Type: ".$s_mime);
			readfile( $s_filepath.$s_files );
			exit;
		}
	}

	function history_payment($s_student_id) {
        $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->session->userdata('student_id')]);
        if ($mba_student_data) {
            $o_student = $mba_student_data[0];
            $this->a_page_data['student_data'] = $o_student;
            $this->a_page_data['body'] = $this->load->view('table/payment_list', $this->a_page_data, true);
		    $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

	function test_dev_bni() {
		$create_billing = $this->Bnim->create_billing_dev();
		print('<pre>');var_dump($create_billing);exit;
	}

	public function submit_approval_payment_delay()
	{
		$s_academic_year_id_skipped = 2022;
		$s_semester_type_id_skipped = 1;
		if ($this->input->is_ajax_request()) {
			$this->form_validation->set_rules('deadline_date', 'Invoice Deadline', 'trim|required');
			$s_personal_data_id = $this->input->post('personal_data_id');

			if ($this->form_validation->run()) {
				if (!empty($_FILES['approval_file']['name'])) {
					$this->load->model('File_manager_model', 'File_manager');
					$s_file_path = APPPATH.'uploads/'.$s_personal_data_id.'/';
					
					$config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx|jpeg';
		            $config['max_size'] = 102400;
		            $config['encrypt_name'] = true;
		            $config['file_ext_tolower'] = true;
					$config['upload_path'] = $s_file_path;
					
					if(!file_exists($s_file_path)){
						mkdir($s_file_path, 0755, true);
					}
					$this->load->library('upload', $config);
					
					if($this->upload->do_upload('approval_file')){
						$mbo_student_semester_skipped = $this->General->get_where('dt_student_semester', [
							'student_id' => $o_student->student_id,
							'academic_year_id' => $s_academic_year_id_skipped,
							'semester_type_id' => $s_semester_type_id_skipped
						]);
						
						$this->Stm->update_student_data(['student_approved_payment_delay' => 1], $this->input->post('student_id'));
						$a_document_data = array(
							'personal_data_id' => $s_personal_data_id,
							'document_id' => '778397e1-d5e6-11eb-98d0-52540039e1c3',
							'document_requirement_link' => $this->upload->data('file_name'),
							'document_mime' => $this->upload->data('file_type')
						);
						$this->File_manager->upload_file($a_document_data);
						
						$mba_student_invoice_semester = $this->Im->student_has_invoice_list($s_personal_data_id, false, ['created', 'pending']);
						if ($mba_student_invoice_semester) {
							foreach ($mba_student_invoice_semester as $o_invoice) {
								if (($mbo_student_semester_skipped) AND ($mbo_student_semester_skipped[0]->semester_id == $o_invoice->semester_id)) {
								}else {
									$o_invoice_full = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
									$a_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);

									if (($o_invoice_full) AND ($o_invoice_full->sub_invoice_details_status != 'paid')) {
										$this->Im->update_sub_invoice_details(
											[
												'sub_invoice_details_deadline' => set_value('deadline_date').' 23:59:59'
											], 
											array(
												'sub_invoice_details_id' => $o_invoice_full->sub_invoice_details_id
											)
										);

										if (!is_null($o_invoice_full->trx_id)) {
											$this->change_trx_details($o_invoice_full->trx_id);
										}
									}

									if ($a_invoice_installment) {
										foreach ($a_invoice_installment as $o_installment) {
											if ($o_installment->sub_invoice_details_status != 'paid') {
												$this->Im->update_sub_invoice_details(
													[
														'sub_invoice_details_deadline' => set_value('deadline_date')
													], 
													array(
														'sub_invoice_details_id' => $o_installment->sub_invoice_details_id
													)
												);

												if (!is_null($o_installment->trx_id)) {
													$this->change_trx_details($o_installment->trx_id);
												}
											}
										}
									}
								}
							}
						}
						
						$a_return = ['code' => 0, 'message' => 'Success!'];
					}
					else{
						$a_return = array('code' => 2, 'message' => $this->upload->display_errors('<span>', '</span><br>'));
					}
				}
				else {
					$a_return = array('code' => 1, 'message' => 'Please upload approval file!');
				}
			}
			else{
				$a_return = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
			}
			
			print json_encode($a_return);exit;
		}
	}

	public function get_month()
	{
		$s_date = '2022-12-31 10:41:38';
		// print(date('m', strtotime($s_date)));exit;
		$s_year = date('Y', strtotime($s_date));
		$s_month = date('m', strtotime($s_date));
		$s_semester_type_id_must = (!in_array($s_month, ['01', '02', '03', '04', '05', '06'])) ? 2 : 1;
		// if ($s_semester_type_id_must == 2) {
		// 	$s_year = $s_year - 1;
		// }
		$s_semester_select = $s_year.'-'.$s_semester_type_id_must;
		print($s_semester_select);exit;
	}

	public function get_invoice_recap()
	{
		$s_academic_year_id_skipped = date('Y');
		$s_semester_type_id_skipped = (!in_array(date('m'), ['01', '02', '03', '04', '05', '06'])) ? 2 : 1;

		if ($this->input->is_ajax_request()) {
			$s_batch = $this->input->post('batch');
			$s_academic_semester = $this->input->post('academic_semester');
			if ((!empty($s_academic_semester)) AND ($s_academic_semester != 'all')) {
				$s_academic_year_id = explode('-',$s_academic_semester)[0];
				$s_semester_type_id = explode('-',$s_academic_semester)[1];
			}
			$s_payment_type_code = $this->input->post('payment_type_code');

			$status_filter = ['not selected'];
			$invoice_status = ['created', 'pending'];
			if (is_array($this->input->post('student_status'))) {
				if (count($this->input->post('student_status')) > 0) {
					$status_filter = $this->input->post('student_status');
				}
			}

			$a_filter_student = ((empty($s_batch)) OR ($s_batch == 'all')) ? ['ds.academic_year_id <= ' => $s_academic_year_id] : ['ds.academic_year_id' => $s_batch];
			$mba_student_data = $this->Stm->get_student_filtered($a_filter_student, $status_filter);
			if ($mba_student_data) {
				if ((!empty($s_payment_type_code)) AND ($s_payment_type_code != 'all')) {
					$a_invoice_filter['df.payment_type_code'] = $s_payment_type_code;
				}

				foreach ($mba_student_data as $o_student) {
					$a_invoice_processing = [];
					// $a_invoice_filter['df.academic_year_id'] = $o_student->finance_year_id;
					// $a_invoice_filter['di.invoice_status != '] = 'paid';
					$d_amount_pending_semester = 0;
					$d_amount_pending = 0;
					$d_amount_fined_semester = 0;
					$d_amount_fined = 0;
					if ((!empty($s_academic_semester)) AND ($s_academic_semester != 'all')) {
						$mba_student_invoice_semester = $this->Im->student_has_invoice_list($o_student->personal_data_id, [
							'df.payment_type_code' => $s_payment_type_code,
							'di.academic_year_id != ' => '2024'
						], $invoice_status);
						
						if ($mba_student_invoice_semester) {
							$mbo_student_semester_skipped = $this->General->get_where('dt_student_semester', [
								'student_id' => $o_student->student_id,
								'academic_year_id' => $s_academic_year_id_skipped,
								'semester_type_id' => $s_semester_type_id_skipped
							]);

							$mbo_student_semester = $this->General->get_where('dt_student_semester', [
								'student_id' => $o_student->student_id,
								'academic_year_id' => $s_academic_year_id,
								'semester_type_id' => $s_semester_type_id
							]);

							foreach ($mba_student_invoice_semester as $o_invoice) {
								if (!in_array($o_invoice->invoice_id, $a_invoice_processing)) {
									array_push($a_invoice_processing, $o_invoice->invoice_id);

									$o_invoice_full = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
									$a_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
									$d_amount_full_unpaid = 0;
									$d_amount_installment_unpaid = 0;
									$d_amount_unpaid = 0;
									$d_amount_with_fined = 0;

									if ($a_invoice_installment) {
                                        foreach ($a_invoice_installment as $installment) {
                                            if ($installment->sub_invoice_details_status != 'paid') {
                                                $d_amount_unpaid += $installment->sub_invoice_details_amount_total;
                                                $d_amount_with_fined += ($installment->sub_invoice_details_amount_total - $installment->sub_invoice_details_amount);
                                            }
                                        }
                                    }else if ($o_invoice_full) {
                                        if ($o_invoice_full->sub_invoice_details_status != 'paid') {
                                            $d_amount_unpaid += $o_invoice_full->sub_invoice_details_amount_total;
											$d_amount_with_fined += ($installment->sub_invoice_details_amount_total - $installment->sub_invoice_details_amount);
                                        }
                                    }
									
									if ($o_invoice_full) {
										$d_amount_full_unpaid = $o_invoice_full->sub_invoice_details_amount_total;
									}

									if ($a_invoice_installment) {
										foreach ($a_invoice_installment as $o_installment) {
											if ($o_installment->sub_invoice_details_status != 'paid') {
												$d_amount_installment_unpaid += $o_installment->sub_invoice_details_amount_total;
											}
										}
									}

									if ($d_amount_full_unpaid != $d_amount_installment_unpaid) {
										// print('<pre>');var_dump($o_invoice);
									}
									
									if (($mbo_student_semester) AND ($mbo_student_semester[0]->semester_id == $o_invoice->semester_id)) {
										$d_amount_pending_semester = $d_amount_unpaid;
										$d_amount_fined_semester = $d_amount_with_fined;
										// if ($o_invoice->personal_data_id == '08e95d34-7062-4738-b3d0-3171929f9501') {
										// 	print('<pre>');var_dump($mbo_student_semester_skipped);exit;
										// }
									}

									if (($mbo_student_semester_skipped) AND ($mbo_student_semester_skipped[0]->semester_id == $o_invoice->semester_id)) {
										# code...
									}else {
										$d_amount_pending += $d_amount_unpaid;
										$d_amount_fined += $d_amount_with_fined;
									}

									// if (($o_invoice->personal_data_id == 'a92d54ad-b5ef-4e86-a1b2-23902c84b72a') AND ($o_invoice->semester_id == '4')) {
									// 	print('<pre>');var_dump($a_invoice_installment);exit;
									// }
								}
							}
						}
					}

					// $o_student->amount_semester_pending = number_format($d_amount_pending_semester, 0, '.', '.');
					// $o_student->amount_total_pending = number_format($d_amount_pending, 0, '.', '.');
					$o_student->amount_semester_pending = $d_amount_pending_semester;
					$o_student->amount_total_pending = $d_amount_pending;
					$o_student->amount_semester_fined = $d_amount_fined_semester;
					$o_student->amount_total_fined = $d_amount_fined;
				}
			}

			print json_encode(['code' => 1, 'data' => $mba_student_data]);
		}
	}

	public function billing_student_recaps()
	{
		$this->a_page_data['body'] = $this->load->view('recaps', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function recaps()
	{
		// if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
		// 	print('under maintenance!');exit;
		// }
		$this->a_page_data['body'] = $this->load->view('recaps', $this->a_page_data, true);
		$this->load->view('layout_ext', $this->a_page_data);
	}

	public function recap_list()
	{
		$this->a_page_data['user_update_allowed'] = [
			'47013ff8-89df-11ef-8f45-0068eb6957a0', // budi siswanto
			'4a01b1ee-05e3-4ff2-8f15-e70221aa24e3', // erny handjoyo
			'28010a21-6a24-49d3-988e-58316afd6e97', // rahma martaningtyas
		];
		$this->load->view('table/recap_list', $this->a_page_data);
	}

	public function form_filter_recap()
	{
		$this->a_page_data['batch'] = $this->General->get_batch();
		$this->a_page_data['status_lists'] = ['active', 'inactive', 'onleave', 'graduated'];
		$this->a_page_data['academic_semester'] = $this->Sem->get_semester_setting();
		$this->a_page_data['payment_type'] = $this->Im->get_payment_type();

		$s_academic_year_id_active = ($this->session->has_userdata('academic_year_id_active')) ? $this->session->userdata('academic_year_id_active') : false;
		$this->load->view('form/filter_recap', $this->a_page_data);
	}

	public function check_environment()
	{
		print('<pre>');
		var_dump($this->session->userdata('environment'));exit;
	}

	public function tuition_fee_report_semester($s_academic_year_id, $s_semester_type_id, $s_module = 'finance')
	{
		if ($s_module == 'finance') {
			modules::run('devs/devs2/invoice_semester_tuition_fee', $s_academic_year_id, $s_semester_type_id);
		}
		else {
			modules::run('devs/devs2/invoice_semester_tuition_fee_general', $s_academic_year_id, $s_semester_type_id);
		}
	}

	public function download_custom_student_report_tuition_fee($s_case)
	{
		modules::run('download/excel_download/download_custom_student_report', $s_case);
	}

	public function download_invoice_report()
	{
		if ($this->input->is_ajax_request()) {
			$a_filter_data = $this->input->post();

			// foreach ($a_filter_data as $key => $value) {
			// 	if (is_array($value)) {
			// 		unset($a_filter_data[$key]);
			// 	}
			// 	else if ($value == 'all') {
			// 		unset($a_filter_data[$key]);
			// 	}else if($key == 'study_program_id') {
			// 		$a_filter_data['ds.study_program_id'] = $value;
			// 		unset($a_filter_data[$key]);
			// 	}
			// }

			// $a_filter_data = (count($a_filter_data) > 0) ? $a_filter_data : false;

			$status_filter = false;
			if (is_array($this->input->post('student_status'))) {
				if (count($this->input->post('student_status')) > 0) {
					$status_filter = $this->input->post('student_status');
				}
			}

			$mba_student_data = modules::run('student/filter_student_finance', $a_filter_data, true);
			// print('<pre>');var_dump($mba_student_data);exit;
			// $mba_student_data = $this->Stm->get_student_filtered($a_student_filter, $a_student_status, "ds.academic_year_id ASC, fc.faculty_abbreviation ASC, rsp.study_program_abbreviation ASC, dpd.personal_data_name");
			$a_return = modules::run('devs/generate_invoice_report', '02', $mba_student_data);

			print json_encode($a_return);
		}
	}

	public function virtual_account_invoice_form()
	{
		// $this->a_page_data['body'] = $this->load->view('form/invoice_open_form', $this->a_page_data);
		$this->load->view('form/open_invoice_form', $this->a_page_data);
	}
	
	public function invoice_form()
	{
		$this->a_page_data['batch'] = $this->General->get_batch();
		$this->a_page_data['semester'] = $this->Sem->get_semester_lists([
			'rst.semester_type_master' => NULL
		]);
		$this->a_page_data['payment_type'] = $this->Finm->get_payment_type_code();
		$this->a_page_data['body'] = $this->load->view('form/invoice_form', $this->a_page_data, true);
		
		// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
			$this->load->view('form/new_invoice_form', $this->a_page_data);
		// }
		// else {
		// 	$this->load->view('form/invoice_form', $this->a_page_data);
		// }
// 		$this->load->view('layout', $this->a_page_data);
	}

	public function send_billing()
	{
		if ($this->input->is_ajax_request()) {
			$i_now = time();
			$this->load->model('partner/Partner_student_model', 'Psm');
			
			$s_invoice_id = $this->input->post('invoice_id');
			$s_student_email = $this->input->post('student_email');
			$s_subject_email = $this->input->post('subject_email');
			$s_message_body = $this->input->post('message_body');
			$s_email_cc = $this->input->post('student_cc_email');

			$mba_parent_email = explode(';', $s_email_cc);
			foreach ($mba_parent_email as $key_email => $smail) {
				if (empty($smail)) {
					unset($mba_parent_email[$key_email]);
				}
			}
			// $mba_parent_email = [];

			$s_partner = (!empty($this->input->post('partner'))) ? $this->input->post('partner') : false;
			// print($s_subject_email);exit;

			$o_invoice = $this->Im->get_unpaid_invoice(array('di.invoice_id' => $s_invoice_id))[0];
			if ($o_invoice) {
				$mba_sub_invoice_details = $this->Im->get_invoice_data(['di.invoice_id' => $o_invoice->invoice_id]);
				if($mba_sub_invoice_details) {
					$a_email = $this->config->item('email');
					$s_email_from = $a_email['finance']['payment'];
					$a_bcc_email = array('employee@company.ac.id');
					// $a_bcc_email = array('employee@company.ac.id');

					$mbo_student_data = $this->Stm->get_student_by_personal_data_id($o_invoice->personal_data_id, [
						'student_status != ' => 'resign'
					]);
					$mbo_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_invoice->personal_data_id])[0];

					if ($s_partner == 'srh') {
						$mbo_student_data = $this->Psm->get_partner_student_data([
							'sn.personal_data_id' => $o_invoice->personal_data_id
						]);

						if ($mbo_student_data) {
							$mbo_student_data = $mbo_student_data[0];
						}
					}
					// print($s_partner);exit;
					// $mbo_student_data = $this->Stm->get_student_by_personal_data_id($o_invoice->personal_data_id, [
					// 	'student_status !=' => 'resign'
					// ]);

					$mbo_family_data = $this->Fm->get_family_by_personal_data_id($o_invoice->personal_data_id);
					if($mbo_family_data){
						$mba_family_members = $this->Fm->get_family_members($mbo_family_data->family_id, array(
							'family_member_status != ' => 'child'
						));
						if($mba_family_members){
							foreach($mba_family_members as $family){
								if (!in_array($family->personal_data_email, $mba_parent_email)) {
									array_push($mba_parent_email, $family->personal_data_email);
								}
							}
						}
					}

					$s_payment_type_code = '02';

					$mba_invoice_details = $this->Im->get_invoice_details([
						'did.invoice_id' => $o_invoice->invoice_id
					]);

					$s_payment_type = 'Tuition Fee';
					if ($mba_invoice_details) {
						foreach ($mba_invoice_details as $o_invoice_details) {
							if ($o_invoice_details->fee_amount_type == 'main') {
								$mbo_amount_type = $this->Im->get_payment_type($o_invoice_details->payment_type_code);
								$s_payment_type = $mbo_amount_type->payment_type_name;
								$s_payment_type_code = $mbo_amount_type->payment_type_code;
							}
						}
					}

					$s_subject_email = ($s_subject_email == '') ? "[REMINDER] ".$s_payment_type." Invoice" : $s_subject_email;

					if ($s_message_body == '') {
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
						
						$this->a_page_data['sub_invoice_data'] = $mba_sub_invoice;
						$this->a_page_data['invoice_data'] = $o_invoice;
						$this->a_page_data['invoice_details'] = $mba_invoice_details;

						if($o_invoice->invoice_allow_fine == 'yes'){
							$s_html = $this->load->view('callback/email_reminder_template_fine', $this->a_page_data, TRUE);
						}
						else{
							$s_html = $this->load->view('callback/email_reminder_template', $this->a_page_data, TRUE);
						}

						if (in_array($s_payment_type_code, ['03', '04', '05', '07', '08', '09'])) {
							$s_html = $this->load->view('callback/billing_template', $this->a_page_data, TRUE);
						}
					}else{
						$s_html = $s_message_body;
					}
					
					// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
					// 	print('<pre>');var_dump($mbo_student_data);exit;
					// }

					// $s_attch = false;
					if ($s_partner == 'srh') {
						$s_student_email = $mbo_student_data->personal_data_email;
						$mba_parent_email = ['employee@company.ac.id'];
						if ($s_student_email == 'cindy.uray.mbae@stud.mobile-university.de') {
							if (!in_array('cindyangrainy@outlook.com', $mba_parent_email)) {
								array_push($mba_parent_email, 'cindyangrainy@outlook.com');
							}
						}
						else if ($s_student_email == 'naufal.ghn@gmail.com') {
							if (!in_array('n_fayola@yahoo.co.id', $mba_parent_email)) {
								array_push($mba_parent_email, 'n_fayola@yahoo.co.id');
							}
						}

						$mba_email_cc = explode(';', $s_email_cc);
						foreach ($mba_email_cc as $key_email => $smail) {
							if ((!empty($smail)) AND (!in_array($smail, $mba_parent_email))) {
								array_push($mba_parent_email, $smail);
							}
						}
						// $s_attch = modules::run('download/pdf_download/generate_invoice_billing/', $s_invoice_id, $s_partner, true);
					}
					else if ((isset($mbo_student_data->student_email)) AND (is_null($mbo_student_data->student_email))) {
						$a_return = ['code' => 1, 'message' => 'Error retrieve student data!'];
						print json_encode($a_return);exit;
					}
					else {
						if ($mbo_student_data->student_email == 'hose.winanda@stud.iuli.ac.id') {
							if (!in_array('hosewinanda@gmail.com', $mba_parent_email)) {
								array_push($mba_parent_email, 'hosewinanda@gmail.com');
							}
						}
						else if ($mbo_student_data->student_email == 'joy.winata@stud.iuli.ac.id') {
							if (!in_array('winata.mh@gmail.com', $mba_parent_email)) {
								array_push($mba_parent_email, 'winata.mh@gmail.com');
							}
						}
						else if ($mbo_student_data->student_email == 'andy.daniswara@stud.iuli.ac.id') {
							if (!in_array('lia.naomi03@gmail.com', $mba_parent_email)) {
								array_push($mba_parent_email, 'lia.naomi03@gmail.com');
							}
						}

						if ($mbo_student_data->student_status != 'active') {
							if (!in_array($mbo_personal_data->personal_data_email, $mba_parent_email)) {
								array_push($mba_parent_email, $mbo_personal_data->personal_data_email);
							}
						}

						$s_student_email = $mbo_student_data->student_email;
					}

					if ($s_payment_type_code == '14') {
						$s_student_email = [$mbo_personal_data->personal_data_email, $s_student_email];
						if($mba_parent_email){
							if (!in_array('employee@company.ac.id', $mba_parent_email)) {
								array_push($mba_parent_email, 'employee@company.ac.id');
							}
						}
						else {
							$mba_parent_email = ['employee@company.ac.id'];
						}
					}
					else if ($s_payment_type_code == '15') {
						$s_student_email = 'employee@company.ac.id';
						$mba_parent_email = ['employee@company.ac.id'];
					}
					
					// print('<pre>');var_dump($s_student_email);exit;
					// $s_subject_email = str_replace('+', ' ', $s_subject_email);
					// $config = $this->config->item('mail_config');
					$config['mailtype'] = 'html';
					$this->email->initialize($config);

					if (($s_payment_type_code == '02') AND ($s_partner != 'SRH')) {
						$this->email->attach(FCPATH.'assets/img/notification_blocked_portal.png');
					}

					// if (($s_attch) AND (!empty($s_attch))) {
					// 	$this->email->attach($s_attch);
					// }

					// print('<pre>');var_dump($s_student_email);exit;
					$this->email->subject($s_subject_email);
					$this->email->from($s_email_from, 'IULI Reminder System');
					// $this->email->to('employee@company.ac.id');
					$this->email->to($s_student_email);
					if((is_array($mba_parent_email)) AND (count($mba_parent_email) > 0)){
						$this->email->cc($mba_parent_email);
					}
					
					$this->email->bcc($a_bcc_email);
					$this->email->reply_to($a_email['finance']['main']);

					$this->email->message($s_html);
					if (!$this->email->send()) {
						$a_return = ['code' => 1, 'message' => 'Email not send!'.$this->email->print_debugger()];
						// $this->send_notification_telegram('error send billing '.$this->email->print_debugger());
						print('<pre>');
						var_dump($this->email->print_debugger());exit;
						$this->log_activity('Email did not sent');
						$this->log_activity('Error Message: '.$this->email->print_debugger());
					}else{
						$a_return = ['code' => 0, 'message' => 'Success'];
					}
				}else{
					$a_return = ['code' => 1, 'message' => 'Eror retrieve data sub_invoice_details!'];
				}
			}else{
				$a_return = ['code' => 1, 'message' => 'Eror retrieve data invoice!'];
			}

			print json_encode($a_return);

		}
	}

	function set_cancel_all_va($s_invoice_id) {
		$mba_invoice_full_payment = $this->Im->get_invoice_full_payment($s_invoice_id);
		$mba_invoice_installment = $this->Im->get_invoice_installment($s_invoice_id);
		if ($mba_invoice_full_payment) {
			if (!is_null($mba_invoice_full_payment->trx_id)) {
				$this->cancel_payment($mba_invoice_full_payment->trx_id, true);
			}
		}

		if ($mba_invoice_installment) {
			foreach ($mba_invoice_installment as $o_installment) {
				if (!is_null($o_installment->trx_id)) {
					$this->cancel_payment($o_installment->trx_id, true);
				}
			}
		}
	}

	public function send_custom_email()
	{
		// print('inactive');exit;
		$s_text = "
		<p>Greetings from IULI.</p>

<p>Kindly remind you for this payment:</p>

<h2>Invoice Number: INV-22081914001</h2>

<p>Student Name: <strong>JENG YIING WU</strong></p>

<p>&nbsp;</p>

<p>INTERNATIONAL FULL TIME STUDENT FEE IDR 10,950,000</p>

<p><strong>Fee Details:</strong></p>

<ul>
	<li>ADMINISTRATION FEE IDR 5,000,000</li>
	<li>TELEX VISA and e-VISA FEE IDR 5,950,000</li>
</ul>

<p>Payment Type: Full Amount IDR 10,950,000</p>

<p>&nbsp;</p>

<p>Please transfer payment to:</p>

<p>&nbsp;</p>

<p>Account Number: BNINIDJA8310140001220905<br />
(swift code: BNINIDJA follow by BNI Virtual Account Number)</p>

<p>Beneficiary Name: JENG YIING WU</p>

<p>Bank: Bank Negara Indonesia (BNI) BSD Tangerang Branch &ndash; Indonesia</p>

<p>Due Date: 26 August 2022</p>

<h3>Notes:</h3>

<ol>
	<li>BNI will reject payment which is not at the exact amount and account as stated above.</li>
	<li>If payment for the Full Payment Method past due date, the system will automatically reject the virtual account.</li>
	<li>If student makes a payment without using BNI Virtual Account, then we assume no payment has been made.</li>
	<li>Please ignore this email if you have already paid.</li>
	<li><strong>BNI Call Officer (BCO) and Interactive Voice Response (IVR) : 1500046 (Open 24/7)</strong></li>
</ol>

<p>&nbsp;</p>

<p>&nbsp;</p>

<p>&nbsp;</p>

<p>&nbsp;</p>

<p>Best regards,</p>

<p>&nbsp;</p>

<p>&nbsp;</p>

<p>&nbsp;</p>

<p>&nbsp;</p>

<p>IULI ANF Department</p>
		";

		$s_email_to = ['johnny06wu@gmail.com', 'jeng.wu@stud.iuli.ac.id'];
		// $s_email_to = 'employee@company.ac.id';
		$a_cc_mail = ['johnny06wu@gmail.com','employee@company.ac.id'];
		$a_bcc_email = array('employee@company.ac.id', 'employee@company.ac.id', 'employee@company.ac.id');

		// $config = $this->config->item('mail_config');
		$a_email = $this->config->item('email');
		$s_email_from = $a_email['finance']['payment'];

		$config['mailtype'] = 'html';
		$this->email->initialize($config);

		$this->email->subject('[REMINDER] INTERNATIONAL FULL TIME STUDENT FEE Invoice ');
		$this->email->from($s_email_from, 'IULI Reminder System');

		$this->email->to($s_email_to);
		$this->email->cc($a_cc_mail);
		$this->email->bcc($a_bcc_email);
		$this->email->reply_to($a_email['finance']['main']);

		$this->email->message($s_text);
		$this->email->send();
	}

	public function activate_virtual_account($s_sub_invoice_details_id, $b_ajax = true)
	{
		$mbo_invoice_data = $this->Im->get_invoice_data([
			'dsid.sub_invoice_details_id' => $s_sub_invoice_details_id
		])[0];

		if ($mbo_invoice_data) {
			$mbo_customer_data = $this->General->get_where('dt_personal_data', [
				'personal_data_id' => $mbo_invoice_data->personal_data_id
			])[0];

			if (is_null($mbo_invoice_data->trx_id)) {
				$a_billing_data = array(
					'trx_amount' => $mbo_invoice_data->sub_invoice_details_amount,
					'billing_type' => 'c',
					'customer_name' => str_replace("'", "", $mbo_customer_data->personal_data_name),
					'virtual_account' => $mbo_invoice_data->sub_invoice_details_va_number,
					'description' => $mbo_invoice_data->sub_invoice_details_description,
					'datetime_expired' => date('Y-m-d 23:59:59', strtotime($mbo_invoice_data->sub_invoice_details_deadline." +3 day")),
					'customer_email' => 'bni.employee@company.ac.id'
				);

				$a_return_billing_data = $this->Bnim->create_billing($a_billing_data);
				
				if($a_return_billing_data['status'] === '000'){
					$this->Im->update_sub_invoice_details(['trx_id' => $a_return_billing_data['trx_id']], ['sub_invoice_details_id' => $s_sub_invoice_details_id]);

					$a_return = ['code' => 0, 'message' => 'Activated!'];
				}else{
					$a_return = ['code' => $a_return_billing_data['status'], 'message' => $a_return_billing_data['message']];
				}
			}else{
				$mbo_invoice_bni_status = $this->Bnim->inquiry_billing($mbo_invoice_data->trx_id, true);

				if ($mbo_invoice_bni_status['va_status'] != 1) {
					$date_now = date('Y-m-d H:i:s');
					
					if (date('Y-m-d H:i:s', strtotime($mbo_invoice_data->sub_invoice_details_deadline)) < $date_now) {
						$a_return = ['code' => 1, 'message' => 'deadline is expired!'];
					}else{
						$this->change_trx_details($mbo_invoice_data->trx_id);
						$a_return = ['code' => 0, 'message' => 'Activated!'];
					}
				}else{
					$a_return = ['code' => 1, 'message' => 'Virtual Account number is still active!'];
				}
			}
		}else{
			$a_return = ['code' => 1, 'message' => 'Invoice not found!'];
		}

		if ($b_ajax) {
			print json_encode($a_return);
		}else{
			return $a_return;
		}
	}

	public function checker($s_invoice_id)
	{
		$mba_sub_invoice = $this->Im->get_sub_invoice_data(['dsi.invoice_id' => $s_invoice_id]);
		$o_invoice = $this->Im->get_unpaid_invoice(array('di.invoice_id' => $s_invoice_id))[0];
		$mba_invoice_details = $this->Im->get_invoice_details([
			'did.invoice_id' => $s_invoice_id
		]);

		print('<pre>');
		var_dump($mba_sub_invoice);
	}

	function get_reminder_teks($s_student_id, $s_payment_type = '02', $b_force_send = false) {
		$mba_student = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
		$o_student = $mba_student[0];
		$body = modules::run('callback/api/get_reminder_billing_teks_student', $s_student_id, $s_payment_type, true);

		if ($b_force_send) {
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

			$a_email = $this->config->item('email');
			$s_email_from = $a_email['finance']['payment'];
			$a_bcc_email = array('employee@company.ac.id');

			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			$this->email->from($s_email_from, 'IULI Reminder System');
			// $this->email->to('employee@company.ac.id');
			$this->email->to($o_student->student_email);

			if($mba_parent_email){
				$this->email->cc($mba_parent_email);
			}
			
			$this->email->subject('[REMINDER] Tuition Fee Invoice');
			$this->email->bcc($a_bcc_email);
			$this->email->reply_to($a_email['finance']['main']);

			$this->email->message($body);
			$this->email->send();
			print('<h3>Send to '.$o_student->student_email.'</h3>');
		}
		print($body);exit;
	}

	// function get_invoice_reminder_teks($s_invoice_id = false, $s_partner = false) {
	// 	if ($s_partner) {
	// 		$return_data = $this->get_invoice_reminder_teks_old($s_invoice_id, $s_partner);
	// 	}
	// 	else {
	// 		$teks_message = $this->get_reminder_teks($s_student_id, $s_payment_type = '02');
	// 	}
	// }

	public function get_invoice_reminder_teks($s_invoice_id = false, $s_partner = false)
	{
		if ($this->input->is_ajax_request()) {
			$s_invoice_id = $this->input->post('invoice_id');
			$s_partner = (!empty($this->input->post('partner'))) ? $this->input->post('partner') : false;
		}

		$mba_sub_invoice = $this->Im->get_sub_invoice_data(['dsi.invoice_id' => $s_invoice_id]);
		$o_invoice = $this->Im->get_unpaid_invoice(array('di.invoice_id' => $s_invoice_id))[0];
		
		$mba_invoice_details = $this->Im->get_invoice_details([
			'did.invoice_id' => $s_invoice_id
		]);

		if (($mba_sub_invoice) AND ($mba_sub_invoice[0]->invoice_allow_reminder == 'yes')) {
			if (($o_invoice) AND ($mba_invoice_details)) {
				$o_student_data = $this->Stm->get_student_by_personal_data_id($o_invoice->personal_data_id);
				$mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_invoice->personal_data_id]);
				$mbo_student_data = $this->Stm->get_student_by_personal_data_id($o_invoice->personal_data_id, [
					'student_status !=' => 'resign'
				]);

				$s_payment_type_code = '02';
				$s_payment_type_name = 'Tuition Fee';
				foreach ($mba_invoice_details as $o_invoice_details) {
					if ($o_invoice_details->fee_amount_type == 'main') {
						$mbo_amount_type = $this->Im->get_payment_type($o_invoice_details->payment_type_code);
						$s_payment_type_code = $mbo_amount_type->payment_type_code;
						$s_payment_type_name = $mbo_amount_type->payment_type_name;
					}
				}

				foreach($mba_sub_invoice as $sub_invoice){
					$mba_sub_invoice_details = $this->Im->get_invoice_data(['dsid.sub_invoice_id' => $sub_invoice->sub_invoice_id]);
					$sub_invoice->sub_invoice_details_data = false;
					
					if($mba_sub_invoice_details){
						$sub_invoice->sub_invoice_details_data = $mba_sub_invoice_details;
					}
				}

				$this->a_page_data['sub_invoice_data'] = $mba_sub_invoice;
				$this->a_page_data['invoice_data'] = $o_invoice;
				$this->a_page_data['invoice_details'] = $mba_invoice_details;
				$this->a_page_data['student_data'] = $mbo_student_data;

				if($o_invoice->invoice_allow_fine == 'yes'){
					$s_html = $this->load->view('callback/email_reminder_template_fine', $this->a_page_data, TRUE);
				}
				else{
					$s_html = $this->load->view('callback/email_reminder_template', $this->a_page_data, TRUE);
				}

				$mbo_family_data = $this->Fm->get_family_by_personal_data_id($o_invoice->personal_data_id);
				$a_cc_email = [];
				if($mbo_family_data){
					$mba_family_members = $this->Fm->get_family_members($mbo_family_data->family_id, array(
						'family_member_status != ' => 'child'
					));
					if($mba_family_members){
						foreach($mba_family_members as $family){
							if (!in_array($family->personal_data_email, $a_cc_email)) {
								array_push($a_cc_email, $family->personal_data_email);
							}
						}
					}
				}

				if (($o_student_data) AND ($o_student_data->student_status != 'active')) {
					if (!in_array($mba_personal_data[0]->personal_data_email, $a_cc_email)) {
						array_push($a_cc_email, $mba_personal_data[0]->personal_data_email);
					}
				}
		
				if (in_array($s_payment_type_code, ['05', '07', '08'])) {
					$s_html = $this->load->view('callback/billing_template', $this->a_page_data, TRUE);
				}
				else if ($s_payment_type_code == '09') {
					$o_student = $this->Stm->get_student_filtered(['ds.personal_data_id' => $o_invoice->personal_data_id])[0];
					$mba_invoice_full_payment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
					$a_param = [
						'invoice_number' => $o_invoice->invoice_number,
                        'personal_data_name' => $o_student->personal_data_name,
                        'study_program_name' => $o_student->study_program_name,
                        'description' => $o_invoice->invoice_description,
						'invoice_details' => $mba_invoice_details,
                        'sub_invoice_details_va_number' => $mba_invoice_full_payment->sub_invoice_details_va_number,
                        'sub_invoice_details_amount_total' => $mba_invoice_full_payment->sub_invoice_details_amount_total,
                        'sub_invoice_details_deadline' => $mba_invoice_full_payment->sub_invoice_details_deadline
					];
					$s_html = modules::run('messaging/text_template/graduation_billing', $a_param);
					$s_html = preg_replace("/\r\n|\r|\n/", '<br>', $s_html);
				}
				else if ($s_payment_type_code == '17') {
					$o_student = $this->Stm->get_student_filtered(['ds.personal_data_id' => $o_invoice->personal_data_id])[0];
					$mba_invoice_full_payment = $this->Im->get_invoice_full_payment($o_invoice->invoice_id);
					$a_param = [
						'invoice_number' => $o_invoice->invoice_number,
                        'personal_data_name' => $o_student->personal_data_name,
                        'study_program_name' => $o_student->study_program_name,
                        'description' => $o_invoice->invoice_description,
						'invoice_details' => $mba_invoice_details,
                        'sub_invoice_details_va_number' => $mba_invoice_full_payment->sub_invoice_details_va_number,
                        'sub_invoice_details_amount_total' => $mba_invoice_full_payment->sub_invoice_details_amount_total,
                        'sub_invoice_details_deadline' => $mba_invoice_full_payment->sub_invoice_details_deadline
					];
					$s_html = modules::run('messaging/text_template/additional_coupon_billing', $a_param);
					$s_html = preg_replace("/\r\n|\r|\n/", '<br>', $s_html);
				}
				else if ($s_payment_type_code == '04') {
					$o_student = $this->Stm->get_student_filtered(['ds.personal_data_id' => $o_invoice->personal_data_id])[0];
					$mba_score_student = $this->Scm->get_score_data_transcript([
						'sc.student_id' => $o_student_data->student_id,
						'sc.semester_id' => $mba_invoice_details[0]->semester_id,
						'sc.score_approval' => 'approved'
					]);

					$a_score_id = [];
					$i_count_subject = 0;
					$s_subject_list = '';
					if ($mba_score_student) {
						foreach ($mba_score_student as $o_score) {
							$i_count_subject++;
							$s_subject_list.= "{$i_count_subject}. {$o_score->subject_name}\n";
							array_push($a_score_id, $o_score->score_id);
						}
					}
					
					$o_sub_invoice_details = $mba_sub_invoice[0]->sub_invoice_details_data;
					
					$a_template_param = array(
						'invoice_id' => $s_invoice_id,
						'subjects_count' => $i_count_subject,
						'transfer_amount' => $o_sub_invoice_details[0]->sub_invoice_details_amount,
						'va_number' => $o_sub_invoice_details[0]->sub_invoice_details_va_number,
						'personal_data_name' => $o_student->personal_data_name,
						'a_score_id' => $a_score_id,
						'payment_deadline' => date('d F Y', strtotime($o_sub_invoice_details[0]->sub_invoice_details_deadline))
					);

					$s_html = modules::run('messaging/text_template/short_semester_billing', $a_template_param);
					$s_html = preg_replace("/\r\n|\r|\n/", '<br>', $s_html);
					if (($mba_sub_invoice) AND (count($mba_sub_invoice) > 1)) {
						$s_html = $this->load->view('callback/email_reminder_template', $this->a_page_data, TRUE);
					}
				}
				else if ($s_payment_type_code == '03') {
					$mbo_active_semester = $this->Sem->get_active_semester();
					$o_student = $this->Stm->get_student_filtered(['ds.personal_data_id' => $o_invoice->personal_data_id])[0];

					$mba_score_student = $this->Scm->get_score_data_transcript([
						'sc.student_id' => $o_student->student_id,
						'sc.score_approval' => 'approved',
						'sc.academic_year_id' => $mbo_active_semester->academic_year_id,
						'sc.semester_type_id' => $mbo_active_semester->semester_type_id,
						'sc.score_mark_for_repetition' => '1'
					]);

					$a_score_id = [];
					if ($mba_score_student) {
						foreach ($mba_score_student as $o_score) {
							array_push($a_score_id, $o_score->score_id);
						}
					}

					$o_sub_invoice_details = $mba_sub_invoice[0]->sub_invoice_details_data;
					$a_data = [
						's_student_id' => $o_student->student_id,
                        'a_score_id' => $a_score_id,
                        'va_number' => $o_sub_invoice_details[0]->sub_invoice_details_va_number,
                        'bill' => $o_sub_invoice_details[0]->sub_invoice_details_amount_total
					];
					$s_html = modules::run('student/repeat/email_body_repetition', $a_data, $o_student);
					$s_html = preg_replace("/\r\n|\r|\n/", '<br>', $s_html);
				}
				else if ($s_payment_type_code == '12') {
					$this->a_page_data['student_data'] = $o_student_data;
					$s_html = $this->load->view('callback/email_template_NFU_billing', $this->a_page_data, TRUE);
				}
				else if ($s_payment_type_code == '14') {
					$this->a_page_data['student_data'] = $o_student_data;
					$s_html = $this->load->view('callback/billing_international_full_time', $this->a_page_data, TRUE);
					if (!in_array('employee@company.ac.id', $a_cc_email)) {
						array_push($a_cc_email, 'employee@company.ac.id');
					}
				}

				if ($s_partner == 'srh') {
					$s_html = $this->load->view('callback/email_template_srh', $this->a_page_data, TRUE);
					$mbo_student_data = $this->Psm->get_partner_student_data([
						'sn.personal_data_id' => $o_invoice->personal_data_id
					]);

					if ($mbo_student_data) {
						$mbo_student_data = $mbo_student_data[0];
					}

					array_push($a_cc_email, 'employee@company.ac.id');
					if ($mbo_student_data->personal_data_email == 'cindy.uray.mbae@stud.mobile-university.de') {
						array_push($a_cc_email, 'cindyangrainy@outlook.com');
					}
					else if ($mbo_student_data->personal_data_email == 'naufal.ghn@gmail.com') {
						array_push($a_cc_email, 'n_fayola@yahoo.co.id');
					}
				}
				else if ($s_payment_type_code == '02') {
					$s_html .= '<img src="'.base_url().'assets/img/notification_blocked_portal.png" alt="IMG">';
				}

				$s_payment_type_name = ($s_payment_type_code == '14') ? $mba_invoice_details[0]->fee_description.' Fee' : $s_payment_type_name;

				$s_payment_type_name = "[REMINDER] ".$s_payment_type_name." Invoice";
				$a_return = array('code' => 0, 'message' => $s_html, 'payment_type' => $s_payment_type_name, 'email_cc' => $a_cc_email);
			}else{
				$a_return = array('code' => 1, 'message' => 'Invoice details not found!');
			}
		}else{
			$a_return = array('code' => 2, 'message' => 'This invoice cannot be sent, because this invoice reminder permission is disabled');
		}

		if ($this->input->is_ajax_request()) {
			print json_encode($a_return);exit;
		}else{
			return $a_return;
		}
	}

	public function initial_installment_form($s_sub_invoice_id)
	{
		$mba_sub_invoice_data = $this->Im->get_sub_invoice_data(array('dsi.sub_invoice_id' => $s_sub_invoice_id))[0];
        if (($mba_sub_invoice_data) AND ($mba_sub_invoice_data->sub_invoice_type == 'installment')) {

            $mbo_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $mba_sub_invoice_data->personal_data_id, 'ds.student_status != ' => 'resign'])[0];
            
            $mba_sub_invoice_details_data = $this->Im->get_invoice_data(array('dsi.sub_invoice_id' => $s_sub_invoice_id));

            if (!$mba_sub_invoice_details_data) {
                $this->a_page_data['invoice_data'] = $mba_sub_invoice_data;
                $this->a_page_data['personal_data'] = $mbo_student_data;
                $this->a_page_data['body'] = $this->load->view('finance/form/initial_installment', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }else{
                show_404();
            }
        }
	}
	
	public function invoice_page()
	{
		$this->a_page_data['body'] = $this->load->view('invoice_page', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function download_invoice_tuition_fee_student_nfu()
	{
		modules::run('devs/generate_invoice_with_installment_report_by_batch', 
			'all',
			'all',
			'all',
			'all',
			['active', 'inactive', 'onleave'],
			'02',
			'nfu'
		);
	}
	
	public function download_invoice_tuition_fee_thesis_proposal()
	{
		modules::run('devs/generate_invoice_with_installment_report_by_batch', 
			'all',
			'all',
			'all',
			'all',
			['active'],
			'02',
			'proposal_thesis'
		);
	}
	
	public function download_invoice_tuition_fee_student_thesis()
	{
		modules::run('devs/generate_invoice_with_installment_report_by_batch', 
			'all',
			'all',
			'all',
			'all',
			['active', 'inactive', 'onleave'],
			'02',
			'thesis'
		);
		// print(count($data));
	}

	function create_open_payment_invoice()
	{
		print('function disabled!');exit;
		$a_billing_data = array(
			'trx_amount' => 0,
			'billing_type' => 'o',
			'customer_name' => 'Hans Jakob Matauschek',
			'virtual_account' => '8310140007000000',
			'description' => 'Full Payment Student Exchange Batch 2023 - 2022/ODD',
			'datetime_expired' => date('Y-m-d 23:59:59', strtotime('2023-06-17 23:59:59')),
			'customer_email' => 'bni.employee@company.ac.id'
		);
		
		$a_return_billing_data = $this->Bnim->create_billing($a_billing_data);
	}

	public function download_income_report()
	{
		modules::run('devs/devs2/income_report');
	}
	
	public function create_student_invoice(
			$s_payment_type_code,
			$i_semester,
			$i_installments,
			$s_student_type,
			$s_student_number,
			$s_personal_data_id,
			$s_personal_data_name,
			$s_payment_deadline_date,
			$s_description,
			$d_amount,
			$s_academic_year_id,
			$s_study_program_id = false,
			$s_program_id,
			$s_academic_year_academic = false,
			$s_semester_type_academic = false
	)
	{
		$this->db->trans_start();

		$a_fee_clause = [
			'payment_type_code' => $s_payment_type_code,
			'academic_year_id' => $s_academic_year_id
		];

		if ($s_payment_type_code == '04') {
			$a_fee_clause['study_program_id'] = $s_study_program_id;
			$a_fee_clause['semester_id'] = $i_semester;
		}
		
		$mba_fee_data = $this->Im->get_fee($a_fee_clause);
		
		if($mba_fee_data){
			// $mbs_va_number = $this->Bnim->get_va_number(
			// 	$s_payment_type_code,
			// 	$i_semester,
			// 	$i_installments,
			// 	'student',
			// 	$s_student_number,
			// 	null,
			// 	$s_program_id
			// );
			$mbs_va_number = $this->Bnim->generate_va_number(
				$s_payment_type_code,
				'student',
				$s_student_number
			);
			
			if($mbs_va_number){
				if ($s_academic_year_academic AND $s_semester_type_academic) {
					$s_invoice_desc .= ' - '.$s_academic_year_academic.'/'.$s_semester_type_academic;
				}

				$a_invoice_data = array(
					'personal_data_id' => $s_personal_data_id,
					'invoice_number' => $this->Im->get_invoice_number($s_payment_type_code),
					'invoice_description' => $s_description,
					'invoice_allow_fine' => 'no'
				);

				if ($s_academic_year_academic AND $s_semester_type_academic) {
					$a_invoice_data['academic_year_id'] = $s_academic_year_academic;
					$a_invoice_data['semester_type_id'] = $s_semester_type_academic;
				}
				
				$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
				
				$a_invoice_details_data = array(
					'invoice_id' => $s_invoice_id,
					'fee_id' => $mba_fee_data[0]->fee_id,
					'invoice_details_amount' => $mba_fee_data[0]->fee_amount,
					'invoice_details_amount_number_type' => $mba_fee_data[0]->fee_amount_number_type,
					'invoice_details_amount_sign_type' => $mba_fee_data[0]->fee_amount_sign_type
				);
				$this->Im->create_invoice_details($a_invoice_details_data);
				
				$a_sub_invoice_data = array(
					'sub_invoice_amount' => $d_amount,
					'sub_invoice_amount_total' => $d_amount,
					'invoice_id' => $s_invoice_id
				);
				$s_sub_invoice_id = $this->Im->create_sub_invoice($a_sub_invoice_data);
				
				$a_billing_data = array(
					'trx_amount' => $d_amount,
					'billing_type' => 'c',
					'customer_name' => $s_personal_data_name,
					'virtual_account' => $mbs_va_number,
					'description' => $s_description,
					'datetime_expired' => date('Y-m-d 23:59:59', strtotime($s_payment_deadline_date."+3 month")),
					'customer_email' => 'bni.employee@company.ac.id'
				);
				
				$a_return_billing_data = $this->Bnim->create_billing($a_billing_data);
				
				if($a_return_billing_data['status'] === '000'){
					$a_sub_invoice_details = array(
						'sub_invoice_id' => $s_sub_invoice_id,
						'trx_id' => $a_return_billing_data['trx_id'],
						'sub_invoice_details_amount' => $d_amount,
						'sub_invoice_details_amount_total' => $d_amount,
						'sub_invoice_details_va_number' => $mbs_va_number,
						'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($s_payment_deadline_date)),
						'sub_invoice_details_real_datetime_deadline' => date('Y-m-d 23:59:59', strtotime($s_payment_deadline_date)),
						'sub_invoice_details_description' => $s_description
					);
					$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details);
					
					$this->db->trans_commit();
					
					$a_return = array('code' => 0, 'va_number' => $mbs_va_number);
				}
				else{
					$this->db->trans_rollback();
					$a_return = array('code' => $a_return_billing_data['status'], 'message' => $a_return_billing_data['message'].": ".$mbs_va_number);
				}
			}
			else{
				$a_return = [
					'code' => 3, 'message' => 'Error during generating VA number'
				];
			}
		}
		else{
			$a_return = array('code' => 2, 'message' => 'Fee data has not been set up from Finance Dept.');
		}
		
		return $a_return;
	}
	
	public function new_invoice()
	{
		if($this->input->is_ajax_request()){
			$this->form_validation->set_rules('create_type', 'Create type', 'trim|required');
			$this->form_validation->set_rules('finance_batch', 'Entry Year', 'trim|required');
			$this->form_validation->set_rules('academic_year_academic', 'Academic Year', 'trim|required');
			$this->form_validation->set_rules('semester_type_academic', 'Semester Type', 'trim|required');
			$this->form_validation->set_rules('fee_id', 'Fee name', 'trim|required');

			if($this->input->post('payment_type_code')){
				$this->form_validation->set_rules('payment_type_code', 'Payment type', 'trim|required');
				if ($this->input->post('payment_type_code') == '13') {
					$this->form_validation->set_rules('total_package_certificate', 'Total Package Ceriticate', 'trim|required');
				}
				else if ($this->input->post('payment_type_code') == '19') {
					$this->form_validation->set_rules('fee_amount_fined', 'Amount Fined', 'trim|required');
					$this->form_validation->set_rules('semester_id', 'Semester', 'trim|required');
					$this->form_validation->set_rules('fee_id', 'Fee name', 'trim');
				}
			}

			if ($this->input->post('create_type') == 'single') {
				$this->form_validation->set_rules('personal_data_id', 'Student Name', 'trim|required');
			}

			$this->form_validation->set_rules('initial_deadline', 'Payment Deadline', 'trim|required');
			$this->form_validation->set_rules('installments', 'Installments', 'trim|required');

			if (($this->input->post('semester_id')) AND ($this->input->post('semester_id') != '')) {
				$mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $this->input->post('semester_id')])[0];

				if (($mbo_semester_data) AND (intval($mbo_semester_data->semester_number) > 8)) {
					if (($this->input->post('payment_type_code') == '02') AND (set_value('create_type') == 'single')) {
						$this->form_validation->set_rules('semester_credit', 'Total SKS Approved', 'trim|required|numeric');
					}
				}
				else if (($mbo_semester_data) AND (intval($mbo_semester_data->semester_number) <= 8)) {
					// if ($this->input->post('installments') != '6') {
					// 	print json_encode(['code' => 2, 'message' => 'installments must be 6 times, this installment is '.$this->input->post('installments')]);exit;
					// }
				}
			}
			
			if($this->form_validation->run()){
				switch(set_value('create_type'))
				{
					case "single":
						if ($this->input->post('payment_type_code') == '09') {
							$rtn = $this->_create_graduation_invoice(
								set_value('personal_data_id'), 
								set_value('initial_deadline'),
								set_value('fee_id')
							);
						}
						else if ($this->input->post('payment_type_code') == '17') {
							$rtn = $this->_create_additional_coupon_invoice(
								set_value('personal_data_id'), 
								set_value('initial_deadline'),
								set_value('fee_id')
							);
						}
						else if ($this->input->post('payment_type_code') == '19') {
							// create fined invoice
							// $rtn = ['code' => 4, 'message' => 'function disabled!'];
							$rtn = $this->_create_fined_invoice(
								set_value('personal_data_id'),
								set_value('semester_id'),
								set_value('initial_deadline'),
								set_value('installments'),
								set_value('academic_year_academic'),
								set_value('semester_type_academic'),
								set_value('fee_amount_fined')
							);
						}
						else{
							$rtn = $this->_create_new_single_invoice(
								set_value('personal_data_id'), 
								set_value('semester_id'), 
								set_value('semester_credit'), 
								set_value('fee_id'),
								set_value('payment_type_code'), 
								set_value('initial_deadline'),
								set_value('installments'),
								set_value('academic_year_academic'),
								set_value('semester_type_academic'),
								set_value('total_package_certificate')
							);
						}
						
						break;
						
					case "bulk":
						$rtn = $this->_create_bulk_invoice(
							set_value('finance_batch'), 
							set_value('semester_id'), 
							set_value('fee_id'),
							'02',
							set_value('initial_deadline'),
							set_value('installments'),
							set_value('academic_year_academic'),
							set_value('semester_type_academic')
						);

						break;

					default:
						$rtn = ['code' => 1, 'message' => 'No action needed!'];
						break;
				}
			}
			else{
				$rtn = ['code' => 1, 'message' => validation_errors('<span>','</span><br>')];
			}
			
			print json_encode($rtn);
			exit;
		}
	}

	public function send_invoice_graduation()
	{
		// $a_student_number_german = ['11201805004','11201705005'];
		$a_student_number_nfu = ['11201807005','11201713001','11201807003','11201707006','11201801006','11201802017','11201808016','11201806004','11201802010','11201802005','11201801028','11201801029','11201704007','11201812003'];
		$a_student_number_regular = ['11201703004','11201810001','11201808017','11201607003','11201807015','11201812006','11201802020','11201807012','11201807004','11201807016','11201702003','11201701015','11201808019','11201803012','11201807002','11201802001','11201713003','11201807006','11201701002','11201804002','11201802003','11201701016','11201602012','11201702019','11201701014','11201801001','11201802013','11201607017','11201801015','11201910004','11201808004','11201806001','11201802004','11201502016','11201706001','11201801014','11201810003','11201608005','11201812005','11201608008','11201807013','11201808005','11201808007','11201601012','11201701009','11201807001','11201806006','11201701018','11201609001','11201802012','11201812001','11201802014','11201701020','11201802008','11201811001','11201807008','11201616001'];
		// $a_student_number_regular_pending = ['11201807009']; // reska sarira tomo

		$s_fee_online = '04117751-7f73-4c3c-a7ff-b2a64c6e4aa7';
		$s_fee_offline = '3309fc8d-251e-4d53-ba88-24197f5a74c2';

		$i_count = 0;
		foreach ($a_student_number_nfu as $s_student_number) {
			$mbo_student_data = $this->General->get_where('dt_student', ['student_number' => $s_student_number]);
			
			if (!$mbo_student_data) {
				print('student number not found:'.$s_student_number);exit;
			}
			$mbo_student_data = $mbo_student_data[0];

			$mbo_student_invoice = $this->Im->student_has_invoice_data($mbo_student_data->personal_data_id, [
				'df.fee_id' => $s_fee_online,
				'di.invoice_status' => 'created'
			]);

			if (!$mbo_student_invoice) {
				// print('<strong>invoice not found:'.$mbo_student_data->student_email.'</strong>');
				// exit;
			}
			else {
				$i_count++;
				print($mbo_student_data->student_email);
				// $result = $this->send_email_graduation_invoice(
				// 	$mbo_student_data->personal_data_id,
				// 	$mbo_student_invoice->invoice_id,
				// 	$mbo_student_invoice->invoice_number,
				// 	$mbo_student_data->student_email
				// );
				// print('---<pre>');var_dump($result);
				print('<br>');
			}
			// print('<br>');
		}

		print('<h1>'.$i_count.'</h1>');
	}

	public function generate_graduation_invoice_custom()
	{
		// sudah publish
		// print('warung tutup!');exit;
		if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
			// $a_student_number_german = ['11201805004','11201705005'];
			// $a_student_number_nfu = ['11201807005','11201713001','11201807003','11201707006','11201801006','11201802017','11201808016','11201806004','11201802010','11201802005','11201801028','11201801029','11201704007','11201812003'];
			// $a_student_number_regular = ['11201703004','11201810001','11201808017','11201607003','11201807015','11201812006','11201802020','11201807012','11201807004','11201807016','11201702003','11201701015','11201808019','11201803012','11201807002','11201802001','11201713003','11201807006','11201701002','11201804002','11201802003','11201701016','11201602012','11201702019','11201701014','11201801001','11201802013','11201607017','11201801015','11201910004','11201808004','11201806001','11201802004','11201502016','11201706001','11201801014','11201810003','11201608005','11201812005','11201608008','11201807013','11201808005','11201808007','11201601012','11201701009','11201807001','11201806006','11201701018','11201609001','11201802012','11201812001','11201802014','11201701020','11201802008','11201811001','11201807008','11201616001'];
			// $a_student_number_regular_pending = ['11201807009']; // reska sarira tomo
			$a_student_number = ['11202003003','11201803008','11202003001','11201903006','11201903008','11201803010','11201903004','11201903001','11201701007','11201701021','11201801007','11202001002','11201901001','11202001001','11201901013','11201909005','11202009001','11201802006','11202002004','11201802009','11202002006','11201902011','11201702002','11201902015','11201902004','11202002001','11202012003','11201812007','11202112001','11201912010','11202005005','11201805001','11202005002','11202005003','11202310004','11202210001','11202310002','11202310001','11202010002','11201710005','11201708008','11201708007','11202008008','11202008002','11202008012','11202204003','11202004002','11201904007','11201904002','11202007005','11202007010','11202007003','11202007008','11202007018','11202007004','11202007007','11202007017','11201907005'];

			$s_deadline_date = '2024-09-18';
			// $s_fee_online = '04117751-7f73-4c3c-a7ff-b2a64c6e4aa7';
			$s_fee_offline = '530898bb-93eb-4c5e-acf2-23ed8ff83c80';

			$i_count = 0;
			foreach ($a_student_number as $s_student_number) {
				$i_count++;
				$mbo_student_data = $this->General->get_where('dt_student', ['student_number' => $s_student_number])[0];
				
				if (!$mbo_student_data) {
					print('student number not found:'.$s_student_number);exit;
				}

				$mbo_student_invoice = $this->Im->student_has_invoice_data($mbo_student_data->personal_data_id, [
					'df.fee_id' => $s_fee_offline
				]);

				if ($mbo_student_invoice) {
					// print('fee not found:'.$s_student_number);exit;
					print('invoice exists:'.$s_student_number);exit;
				}
				$result = $this->_create_graduation_invoice($mbo_student_data->personal_data_id, $s_deadline_date, $s_fee_offline);
				// $result = $this->send_email_graduation_invoice(
				// 	$mbo_student_data->personal_data_id,
				// 	$mbo_student_invoice->invoice_id,
				// 	$mbo_student_invoice->invoice_number,
				// 	$mbo_student_data->student_email
				// );
				if ($result['code'] != 0) {
					print('<pre>');
					var_dump($result);
					exit;
				}
				else{
					print($mbo_student_data->student_email);
					print('<br>');
				}
				// exit;
			}

			print('<h1>'.$i_count.'</h1>');
		}
	}

	private function _create_graduation_invoice($s_personal_data_id, $s_deadline_date, $s_fee_id, $send_mail = false)
	{
		$s_payment_type_code = '09';
		$mbo_fee_data = $this->Im->get_fee([
			'fee_id' => $s_fee_id
		]);
		
		if ($mbo_fee_data) {
			$s_deadline_date = date('Y-m-d 23:59:59', strtotime($s_deadline_date));

			$mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id, [
				'student_status != ' => 'resign'
			]);

			if ($mbo_student_data) {
				$mbo_personal_data = $this->Pdm->get_personal_data_by_id($mbo_student_data->personal_data_id);
				$mbo_student_invoice_data = $this->Im->student_has_invoice_fee_id($mbo_student_data->personal_data_id, $s_fee_id);

				if (!$mbo_student_invoice_data) {
					$s_invoice_number = $this->Im->get_invoice_number($s_payment_type_code);
					$a_invoice_data = [
						'invoice_description' => $mbo_fee_data[0]->fee_description,
						'personal_data_id' => $mbo_student_data->personal_data_id,
						'invoice_number' => $s_invoice_number,
						'invoice_allow_fine' => 'no'
					];
					
					$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
				
					$a_invoice_details_data = [
						'fee_id' => $s_fee_id,
						'invoice_id' => $s_invoice_id,
						'invoice_details_amount' => $mbo_fee_data[0]->fee_amount,
						'invoice_details_amount_number_type' => $mbo_fee_data[0]->fee_amount_number_type,
						'invoice_details_amount_sign_type' => $mbo_fee_data[0]->fee_amount_sign_type
					];
					
					$this->Im->create_invoice_details($a_invoice_details_data);

					$a_sub_invoice_data = [
						'sub_invoice_amount' => $mbo_fee_data[0]->fee_amount,
						'sub_invoice_amount_total' => $mbo_fee_data[0]->fee_amount,
						'invoice_id' => $s_invoice_id
					];
					
					$s_sub_invoice_id_full = $this->Im->create_sub_invoice($a_sub_invoice_data);
					
					$s_va_number = $this->Bnim->generate_va_number(
						$s_payment_type_code,
						'student',
						$mbo_student_data->student_number,
						$mbo_student_data->finance_year_id,
						$mbo_student_data->program_id
					);

					// $s_va_number = $this->Bnim->get_va_number(
					// 	$s_payment_type_code, 
					// 	0, 
					// 	0, 
					// 	'student', 
					// 	$mbo_student_data->student_number, 
					// 	$mbo_student_data->finance_year_id,
					// 	$mbo_student_data->program_id
					// );
					if ($s_va_number) {
						$s_description = "Full Payment Graduation Fee 2024";
						$s_payment_deadline_date = $s_deadline_date;
						$d_amount = $mbo_fee_data[0]->fee_amount;
						
						$a_sub_invoice_details_data = [
							'sub_invoice_id' => $s_sub_invoice_id_full,
							'sub_invoice_details_amount' => $d_amount,
							'sub_invoice_details_amount_total' => $d_amount,
							'sub_invoice_details_va_number' => $s_va_number,
							'sub_invoice_details_deadline' => $s_payment_deadline_date,
							'sub_invoice_details_real_datetime_deadline' => $s_payment_deadline_date,
							'sub_invoice_details_description' => $s_description
						];
						$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details_data);

						$a_trx_data = array(
							'trx_amount' => $d_amount,
							'billing_type' => 'c',
							'customer_name' => $mbo_personal_data->personal_data_name,	
							'virtual_account' => $s_va_number,
							'description' => $s_description,
							'datetime_expired' => date('Y-m-d 23:59:59', strtotime($s_payment_deadline_date." +10 day")),
							'customer_email' => 'bni.employee@company.ac.id'
						);
						$a_bni_result = $this->Bnim->create_billing($a_trx_data);
						
						$a_sub_invoice_details_update = array(
							'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($s_payment_deadline_date))
						);
						
						if($a_bni_result['status'] == '000'){
							$a_sub_invoice_details_update['trx_id'] = $a_bni_result['trx_id'];
						}
						else{
							if($a_bni_result['status'] == '102'){
								$a_update_billing = array(
									'trx_id' => $a_bni_result['trx_id'],
									'trx_amount' => 999,
									'customer_name' => 'CANCEL PAYMENT',
									'datetime_expired' => '2020-01-01 23:59:59',
									'description' => 'CANCEL PAYMENT'
								);
								$this->Bnim->update_billing($a_update_billing);
							}
							else{
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
						}
						
						$this->Im->update_sub_invoice_details(
							$a_sub_invoice_details_update, 
							array(
								'sub_invoice_details_id' => $s_sub_invoice_details_id
							)
						);
						$s_personal_data_name = $mbo_personal_data->personal_data_name;
						$s_study_program_name = $mbo_student_data->study_program_name;
						$s_description_mail = "Graduation Fee 2020";
						$s_vanumber = implode(' ', str_split($s_va_number, 4));
						$s_deadline = date('j F Y', strtotime($s_deadline_date));
						$s_payment_amount = "Rp. ".number_format($d_amount, 0, ',', '.').",-";
						$a_return = ['code' => 0, 'message' => 'Success!'];
					}
					else {
						$a_return = ['code' => 1, 'message' => 'Failed generate VA Number!'];
					}
				}else{
					$a_return = ['code' => 1, 'message' => 'Invoice student for fee has been created before!'];
				}
			}else{
				$a_return = ['code' => 1, 'message' => 'Student not found!'];
			}
		}else{
			$a_return = ['code' => 1, 'message' => 'Fee not found!'];
		}

		return $a_return;
	}

	private function _create_additional_coupon_invoice($s_personal_data_id, $s_deadline_date, $s_fee_id)
	{
		$s_payment_type_code = '17';
		$mbo_fee_data = $this->Im->get_fee([
			'fee_id' => $s_fee_id
		]);
		
		if ($mbo_fee_data) {
			$s_deadline_date = date('Y-m-d 23:59:59', strtotime($s_deadline_date));

			$mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id, [
				'student_status != ' => 'resign'
			]);

			if ($mbo_student_data) {
				$mbo_personal_data = $this->Pdm->get_personal_data_by_id($mbo_student_data->personal_data_id);
				// $mbo_student_invoice_data = $this->Im->student_has_invoice_fee_id($mbo_student_data->personal_data_id, $s_fee_id);
				$mba_student_invoice_data = $this->Im->student_has_invoice_va_number($mbo_student_data->personal_data_id, $s_fee_id);

				$installment = 0;
				if ($mba_student_invoice_data) {
					$installment = count($mba_student_invoice_data);
				}
				// print('<pre>');var_dump($installment);exit;

				$s_invoice_number = $this->Im->get_invoice_number($s_payment_type_code);
				$a_invoice_data = [
					'invoice_description' => $mbo_fee_data[0]->fee_description,
					'personal_data_id' => $mbo_student_data->personal_data_id,
					'invoice_number' => $s_invoice_number,
					'invoice_allow_fine' => 'no'
				];
				
				$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
			
				$a_invoice_details_data = [
					'fee_id' => $s_fee_id,
					'invoice_id' => $s_invoice_id,
					'invoice_details_amount' => $mbo_fee_data[0]->fee_amount,
					'invoice_details_amount_number_type' => $mbo_fee_data[0]->fee_amount_number_type,
					'invoice_details_amount_sign_type' => $mbo_fee_data[0]->fee_amount_sign_type
				];
				
				$this->Im->create_invoice_details($a_invoice_details_data);

				$a_sub_invoice_data = [
					'sub_invoice_amount' => $mbo_fee_data[0]->fee_amount,
					'sub_invoice_amount_total' => $mbo_fee_data[0]->fee_amount,
					'invoice_id' => $s_invoice_id
				];
				
				$s_sub_invoice_id_full = $this->Im->create_sub_invoice($a_sub_invoice_data);
				
				$s_va_number = $this->Bnim->get_va_number(
					$s_payment_type_code, 
					0, 
					$installment,
					'student', 
					$mbo_student_data->student_number, 
					$mbo_student_data->finance_year_id,
					$mbo_student_data->program_id
				);
				
				$s_description = "Graduation / Additional Coupon";
				$s_payment_deadline_date = $s_deadline_date;
				$d_amount = $mbo_fee_data[0]->fee_amount;
				
				$a_sub_invoice_details_data = [
					'sub_invoice_id' => $s_sub_invoice_id_full,
					'sub_invoice_details_amount' => $d_amount,
					'sub_invoice_details_amount_total' => $d_amount,
					'sub_invoice_details_va_number' => $s_va_number,
					'sub_invoice_details_deadline' => $s_payment_deadline_date,
					'sub_invoice_details_real_datetime_deadline' => $s_payment_deadline_date,
					'sub_invoice_details_description' => $s_description
				];

				$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details_data);

				$a_trx_data = array(
					'trx_amount' => $d_amount,
					'billing_type' => 'c',
					'customer_name' => $mbo_personal_data->personal_data_name,	
					'virtual_account' => $s_va_number,
					'description' => $s_description,
					'datetime_expired' => date('Y-m-d 23:59:59', strtotime($s_payment_deadline_date." +1 day")),
					'customer_email' => 'bni.employee@company.ac.id'
				);
				$a_bni_result = $this->Bnim->create_billing($a_trx_data);
				
				$a_sub_invoice_details_update = array(
					'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($s_payment_deadline_date))
				);
				
				if($a_bni_result['status'] == '000'){
					$a_sub_invoice_details_update['trx_id'] = $a_bni_result['trx_id'];
				}
				else {
					if($a_bni_result['status'] == '102'){
						$a_update_billing = array(
							'trx_id' => $a_bni_result['trx_id'],
							'trx_amount' => 999,
							'customer_name' => 'CANCEL PAYMENT',
							'datetime_expired' => '2020-01-01 23:59:59',
							'description' => 'CANCEL PAYMENT'
						);
						$this->Bnim->update_billing($a_update_billing);
					}
					else{
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
				}
				
				$this->Im->update_sub_invoice_details(
					$a_sub_invoice_details_update, 
					array(
						'sub_invoice_details_id' => $s_sub_invoice_details_id
					)
				);

				$a_return = ['code' => 0, 'message' => 'Success!'];
			}else{
				$a_return = ['code' => 1, 'message' => 'Student not found!'];
			}
		}else{
			$a_return = ['code' => 1, 'message' => 'Fee not found!'];
		}

		return $a_return;
	}

	public function test()
	{
		$t_body = <<<TEXT
Greetings from IULI.
Kindly remind you for your next payment:
Invoice Number: {$s_invoice_number}
Student Name: {$s_personal_data_name}
Study Program: {$s_study_program_name}
Description: {$s_description_mail}

Account Number: {$s_vanumber}
Amount: {$s_payment_amount}
Payment Deadline: {$s_deadline}

Note:
•	BNI will reject payment which is not at the exact amount and account as stated above
•	You must bring your receipt payment / payment screen captured in exchange for your Toga

Best regards,
IULI Finance Dept.
TEXT;

		$mbo_family_data = $this->Fm->get_family_by_personal_data_id($s_personal_data_id);


	}

	public function send_email_graduation_invoice(
		$s_personal_data_id,
		$s_invoice_id,
		$s_invoice_number,
		$student_email
	)
	{
		$mba_invoice_details = $this->Im->get_invoice_details([
			'did.invoice_id' => $s_invoice_id
		]);

		$o_student = $this->Stm->get_student_filtered(['ds.personal_data_id' => $s_personal_data_id])[0];
		$mba_invoice_full_payment = $this->Im->get_invoice_full_payment($s_invoice_id);

		$a_param = [
			'invoice_number' => $s_invoice_number,
			'personal_data_name' => $o_student->personal_data_name,
			'study_program_name' => $o_student->study_program_name,
			'invoice_details' => $mba_invoice_details,
			'sub_invoice_details_va_number' => $mba_invoice_full_payment->sub_invoice_details_va_number,
			'sub_invoice_details_amount_total' => $mba_invoice_full_payment->sub_invoice_details_amount_total,
			'sub_invoice_details_deadline' => $mba_invoice_full_payment->sub_invoice_details_deadline
		];
		$s_html = modules::run('messaging/text_template/graduation_billing', $a_param);
		$s_html = preg_replace("/\r\n|\r|\n/", '<br>', $s_html);

		$mbo_family_data = $this->Fm->get_family_by_personal_data_id($s_personal_data_id);
		$mba_parent_email = false;
		if($mbo_family_data){
			$mba_family_members = $this->Fm->get_family_members($mbo_family_data->family_id, array(
				'family_member_status != ' => 'child'
			));
			if($mba_family_members){
				$mba_parent_email = array();
				foreach($mba_family_members as $family){
					array_push($mba_parent_email, $family->personal_data_email);
				}
			}
		}

		$s_email = $student_email;
		$a_email = $this->config->item('email');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from('employee@company.ac.id', 'IULI Finance Team');
		$this->email->to($s_email);
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

		$this->email->subject("[REMINDER] Graduation Fee Invoice");
		$this->email->message($s_html);
		if (!$this->email->send()) {
			$a_return = ['code' => 1, 'message' => 'Email not send!'.$this->email->print_debugger()];
			// $this->send_notification_telegram('error send billing '.$this->email->print_debugger());
			$this->log_activity('Email did not sent');
			$this->log_activity('Error Message: '.$this->email->print_debugger());
			print('<pre>');
			var_dump($this->email->print_debugger());exit;
		}else{
			$a_return = ['code' => 0, 'message' => 'Success'];
		}

		return $a_return;
	}

	// public function create_semester_leave_invoice($s_student_id, $s_academic_year_id, $s_semester_type_id)
	// {
	// 	$mbo_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id])[0];

	// 	if ($mbo_student_data) {
	// 		$mbo_student_semester = $this->Sem->get_student_semester(['dss.student_id' => $s_student_id, 'academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id])[0];
	// 		if ($mbo_student_semester) {
	// 			$mbo_fee_data = $this->Im->get_fee([
	// 				'payment_type_code' => '05',
	// 				'program_id' => $mbo_student_data->student_program,
	// 				'academic_year_id' => $mbo_student_data->finance_year_id,
	// 				'semester_id' => $mbo_student_semester->semester_id,
	// 				'fee_amount_type' => 'main'
	// 			])[0];

	// 			if ($mbo_fee_data) {
	// 				$i_now = date('Y-m-d H:i:s');
	// 				$s_deadline_date = date('Y-m-d 23:59:59', strtotime($i_now." +7 day"));

	// 				$a_create_invoice = $this->_create_new_single_invoice(
	// 					$mbo_student_data->personal_data_id,
	// 					$mbo_student_semester->semester_id, 
	// 					0, 
	// 					$mbo_fee_data->fee_id, 
	// 					'05', 
	// 					$s_deadline_date,
	// 					0
	// 				);

	// 				$b_send = true;

	// 				if ($a_create_invoice['code'] == 3) {
	// 					$mbo_student_invoice_data = $this->Im->student_has_invoice_fee_id($mbo_student_data->personal_data_id, $mbo_fee_data->fee_id);

	// 					$template_teks = $this->get_invoice_reminder_teks($mbo_student_invoice_data->invoice_id);
	// 					$a_return = ['code' => 0, 'message' => 'Success!'];
	// 				}
	// 				else if ($a_create_invoice['code'] == 0)  {
	// 					$mbo_student_invoice_data = $this->Im->student_has_invoice_fee_id($mbo_student_data->personal_data_id, $mbo_fee_data->fee_id);

	// 					$template_teks = $this->get_invoice_reminder_teks($mbo_student_invoice_data->invoice_id);
	// 					$a_return = $a_create_invoice;
	// 				}
	// 				else{
	// 					$b_send = false;
	// 					$a_return = $a_create_invoice;
	// 				}

	// 				// if ($b_send) {
	// 				// 	$a_email = $this->config->item('email');
	// 				// 	$s_email_from = $a_email['finance']['payment'];
	// 				// 	$a_bcc_email = array('employee@company.ac.id', 'employee@company.ac.id', 'employee@company.ac.id', 'employee@company.ac.id');
	// 				// 	// $a_bcc_email = array('employee@company.ac.id');

	// 				// 	$mbo_family_data = $this->Fm->get_family_by_personal_data_id($mbo_student_data->personal_data_id);
	// 				// 	$mba_parent_email = false;
	// 				// 	if($mbo_family_data){
	// 				// 		$mba_family_members = $this->Fm->get_family_members($mbo_family_data->family_id, array(
	// 				// 			'family_member_status != ' => 'child'
	// 				// 		));
	// 				// 		if($mba_family_members){
	// 				// 			$mba_parent_email = array();
	// 				// 			foreach($mba_family_members as $family){
	// 				// 				array_push($mba_parent_email, $family->personal_data_email);
	// 				// 			}
	// 				// 		}
	// 				// 	}
	// 				// }
	// 			}else{
	// 				$a_return = ['code' => 3, 'message' => 'Fee not found!Please Contact finance dept to filled fee semester leave for semester '.$mbo_student_semester->semester_number.' year in '.$mbo_student_data->finance_year_id];
	// 			}
	// 		}else{
	// 			$a_return = ['code' => 2, 'message' => 'Error retrieve student semester!'];
	// 		}
			
	// 	}else{
	// 		$a_return = ['code' => 1, 'message' => 'Student not foound!'];
	// 	}

	// 	if ($this->input->is_ajax_request()) {
	// 		print json_encode($a_return);
	// 	}else{
	// 		return $a_return;
	// 	}
	// }

	// public function create_bulk_invoice_over_semester()
	// {
	// 	$a_student_id = [];

	// 	if (count($a_student_id) > 0) {
	// 		foreach ($a_student_id as $s_student_id) {
	// 			$mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
	// 			if (!$mba_student_data) {
	// 				print($s_student_id.' not found in portal!');
	// 			}
	// 			$o_student = $mba_student_data[0];
	// 			$mba_score_student = $this->Scm->get_score_data_transcript([
	// 				'sc.student_id' => $o_student->student_id,
	// 				'sc.semester_id' => $s_semester_id,
	// 				'sc.score_approval' => 'approved'
	// 			]);

	// 			if ($mba_score_student) {
	// 				$i_total_sks = 0;
	// 				foreach ($mba_score_student as $o_score) {
	// 					$i_total_sks += $o_score->curriculum_subject_credit;
	// 				}

	// 				if ($i_total_sks > 0) {
	// 					$create_invoice = $this->_create_new_single_invoice(
	// 						$o_student->personal_data_id,
	// 						$s_semester_id, 
	// 						$i_total_sks, 
	// 						$s_fee_id, 
	// 						$s_payment_type_code, 
	// 						$s_deadline_date,
	// 						$i_installments,
	// 						$s_academic_year_academic,
	// 						$s_semester_type_academic
	// 					);

	// 					if ($create_invoice['code'] != 0) {
	// 						array_push($a_error, $create_invoice['message']);
	// 					}else{
	// 						$i_created++;
	// 					}
	// 				}
	// 			}
	// 		}
	// 	}
	// }

	private function _create_fined_invoice(
		$s_personal_data_id,
		$s_semester_id,
		$s_deadline_date,
		$i_installments,
		$s_academic_year_id,
		$s_semester_type_id,
		$d_fee_amount_fined
	)
	{
		$s_fee_id = 'ed86d964-71c1-4a40-8b79-c0eb473c5c3c';
		$s_payment_type_code = '19';
		$mbo_fee_data = $this->Im->get_fee([
			'fee_id' => $s_fee_id
		]);

		if ($mbo_fee_data) {
			$s_deadline_date = date('Y-m-d 23:59:59', strtotime($s_deadline_date));

			$mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id, [
				'student_status != ' => 'resign'
			]);

			if ($mbo_student_data) {
				$d_fee_amount = $d_fee_amount_fined;
				$mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $s_semester_id])[0];
				$mba_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $s_semester_type_id]);
				$s_invoice_desc = $mbo_fee_data[0]->fee_description.' Semester '.$mbo_semester_data->semester_number.' academic year '.$s_academic_year_id.' '.$mba_semester_type_data[0]->semester_type_name;

				$a_invoice_data = [
					'invoice_description' => $s_invoice_desc,
					'personal_data_id' => $mbo_student_data->personal_data_id,
					'invoice_number' => $this->Im->get_invoice_number($s_payment_type_code),
					'invoice_allow_fine' => 'no',
					'academic_year_id' => $s_academic_year_id,
					'semester_type_id' => $s_semester_type_id,
				];

				$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
				$a_invoice_details_data = [
					'fee_id' => $s_fee_id,
					'invoice_id' => $s_invoice_id,
					'invoice_details_amount' => $d_fee_amount,
					'invoice_details_amount_number_type' => $mbo_fee_data[0]->fee_amount_number_type,
					'invoice_details_amount_sign_type' => $mbo_fee_data[0]->fee_amount_sign_type
				];
				
				$this->Im->create_invoice_details($a_invoice_details_data);

				$a_sub_invoice_data = [
					'sub_invoice_amount' => $d_fee_amount,
					'sub_invoice_amount_total' => $d_fee_amount,
					'invoice_id' => $s_invoice_id
				];
				$d_fee_installment_amount = $d_fee_amount;
				
				$s_sub_invoice_id_full = $this->Im->create_sub_invoice($a_sub_invoice_data);

				if($i_installments >= 2){
					$d_sub_inv_amount_installment = (is_int($d_fee_amount / $i_installments)) ? round(($d_fee_amount / $i_installments), 0, PHP_ROUND_HALF_UP) : intval($d_fee_amount / $i_installments);
					$d_sub_inv_amount_installment = $this->_get_round_thousand($d_sub_inv_amount_installment);
					$d_sub_inv_amount_total = $d_sub_inv_amount_installment * $i_installments;

					$a_sub_invoice_data['sub_invoice_amount'] = $d_sub_inv_amount_total;
					$a_sub_invoice_data['sub_invoice_amount_total'] = $d_sub_inv_amount_total;
					$a_sub_invoice_data['sub_invoice_type'] = 'installment';
					$s_sub_invoice_id_installment = $this->Im->create_sub_invoice($a_sub_invoice_data);
				}

				for($i = 0; $i <= $i_installments; $i++){
					$s_va_number = $this->Bnim->get_va_number(
						$s_payment_type_code, 
						$s_semester_id, 
						$i, 
						'student', 
						$mbo_student_data->student_number, 
						$mbo_student_data->finance_year_id,
						$mbo_student_data->program_id
					);
					
					if($i == 0){
						$s_description = "Full Payment {$s_invoice_desc}";

						// $s_payment_deadline_date = date('Y-m-15 23:59:59', strtotime($s_deadline_date));
						$s_payment_deadline_date = $s_deadline_date;
						$d_amount = $d_fee_amount;
					}
					else{
						$s_description = "Installment $i {$s_invoice_desc}";
						$s_payment_deadline_date = ($i == 1) ? $s_deadline_date : date('Y-m-d 23:59:59', strtotime($s_payment_deadline_date." +1 month"));
						$d_amount = (is_int($d_fee_amount / $i_installments)) ? round(($d_fee_amount / $i_installments), 0, PHP_ROUND_HALF_UP) : intval(($d_fee_amount / $i_installments) + 1);
						$d_amount = $this->_get_round_thousand($d_amount);
					}
					
					$a_sub_invoice_details_data = [
						'sub_invoice_id' => ($i == 0) ? $s_sub_invoice_id_full : $s_sub_invoice_id_installment,
						'sub_invoice_details_amount' => $d_amount,
						'sub_invoice_details_amount_total' => $d_amount,
						'sub_invoice_details_va_number' => $s_va_number,
						'sub_invoice_details_deadline' => $s_payment_deadline_date,
						'sub_invoice_details_real_datetime_deadline' => $s_payment_deadline_date,
						'sub_invoice_details_description' => $s_description
					];
					$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details_data);
				}

				$a_return = ['code' => 0, 'message' => 'Success!', 'fullpayment_amount' => $d_fee_amount];
			}
			else{
				$a_return = ['code' => 2, 'message' => 'Student not found!'];
			}
		}
		else{
			$a_return = ['code' => 1, 'message' => 'Fee not found!'];
		}

		return $a_return;
	}

	private function _create_single_invoice(
		$s_personal_data_id,
		$s_semester_id, 
		$s_total_credit, 
		$s_fee_id, 
		$s_payment_type_code, 
		$s_deadline_date,
		$i_installments,
		$s_academic_year_id = false,
		$s_semester_type_id = false,
		$d_total_package_certificate = 1
	)
	{
		$mbo_fee_data = $this->Im->get_fee([
			'fee_id' => $s_fee_id
		]);

		$mbo_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $s_semester_type_id]);
		
		if ($mbo_fee_data) {
			$s_deadline_date = date('Y-m-d 23:59:59', strtotime($s_deadline_date));

			$mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id, [
				'student_status != ' => 'resign'
			]);

			if ($mbo_student_data) {
				$mbo_student_invoice_data = $this->Im->student_has_invoice_fee_id($mbo_student_data->personal_data_id, $s_fee_id, ['created', 'pending', 'paid']);
				$mba_ofse_repeat_data = false;
				if ($s_payment_type_code == '07') {
					$mba_ofse_repeat_data = $this->General->get_where('dt_score', [
						'student_id' => $mbo_student_data->student_id,
						'academic_year_id' => $s_academic_year_id,
						'semester_type_id' => ($s_semester_type_id == 2) ? 6 : 4,
						'score_approval' => 'approved'
					]);
				}
				
				// if (!$mbo_student_invoice_data) {
					$d_fee_amount = $mbo_fee_data[0]->fee_amount;
					$d_total_credit_german = 0;
					// $mbo_invoice_setting = $this->General->get_where('ref_settings_invoice', [
					// 	'academic_year_id' => $mbo_student_data->finance_year_id,
					// 	'program_id' => $mbo_student_data->program_id
					// ])[0];

					if ($s_semester_id != '') {
						$mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $s_semester_id])[0];
		
						if ($s_payment_type_code == '02'){
							if (($mbo_semester_data) AND (intval($mbo_semester_data->semester_number) > 8)) {
								$d_fee_amount = $d_fee_amount * $s_total_credit;
							}
						}
						else if (($mbo_semester_data) AND ($s_payment_type_code == '04')) {
							$mba_krs_data = $this->Scm->get_score_data([
								'sc.student_id' => $mbo_student_data->student_id,
								'sc.academic_year_id' => $s_academic_year_id,
								'sc.semester_type_id' => ($s_semester_type_id == 2) ? 8 : 7,
								'sc.score_approval' => 'approved'
							]);

							if ($mba_krs_data) {
								$i_total_credit = 0;
								foreach ($mba_krs_data as $o_krs) {
									$i_total_credit += $o_krs->curriculum_subject_credit;
								}

								if ($i_total_credit > 0) {
									$d_fee_amount = $d_fee_amount * $i_total_credit;
								}
							}
						}
						else if ($s_payment_type_code == '13') {
							$d_fee_amount = $d_fee_amount * $d_total_package_certificate;
						}
						else if ($s_payment_type_code == '07') {
							if ($mba_ofse_repeat_data) {
								$d_fee_amount = $d_fee_amount * count($mba_ofse_repeat_data);
							}
						}
					}
					else if ($s_payment_type_code == '18') {
						$mba_score_data_german = $this->Scm->get_score_like_subject_name([
							'sc.student_id' => $mbo_student_data->student_id,
							'sc.academic_year_id' => $s_academic_year_id,
							'sc.semester_type_id' => $s_semester_type_id,
							'sc.score_approval' => 'approved'
						], 'german');

						if ($mba_score_data_german) {
							foreach ($mba_score_data_german as $o_score) {
								$d_total_credit_german += $o_score->curriculum_subject_credit;
							}
						}

						$d_fee_amount = $d_fee_amount * $d_total_credit_german;
					}

					if ($s_payment_type_code == '04') {
						$s_invoice_desc = $mbo_fee_data[0]->fee_description.' for total '.$s_total_credit.' credit/SKS';
					}
					else if (($s_payment_type_code == '07') AND ($mba_ofse_repeat_data)) {
						$s_invoice_desc = $mbo_fee_data[0]->fee_description.' for total '.count($mba_ofse_repeat_data).' subject';
					}
					else if ($s_payment_type_code == '18') {
						$s_invoice_desc = $mbo_fee_data[0]->fee_description.' for total '.$d_total_credit_german.' credit/SKS';
					}
					else {
						$s_invoice_desc = $mbo_fee_data[0]->fee_description;
					}
					// $s_invoice_desc = ($s_payment_type_code == '04') ? $mbo_fee_data[0]->fee_description.' for total '.$s_total_credit.' credit/SKS' : $mbo_fee_data[0]->fee_description;
					
					if ($s_academic_year_id AND $s_semester_type_id) {
						$s_invoice_desc .= ' - '.$s_academic_year_id.'/'.$s_semester_type_id;
					}

					$a_invoice_data = [
						'invoice_description' => $s_invoice_desc,
						'personal_data_id' => $mbo_student_data->personal_data_id,
						'invoice_number' => $this->Im->get_invoice_number($s_payment_type_code),
						'invoice_allow_fine' => 'no'
					];

					if ($s_academic_year_id AND $s_semester_type_id) {
						$a_invoice_data['academic_year_id'] = $s_academic_year_id;
						$a_invoice_data['semester_type_id'] = $s_semester_type_id;
					}

					if ($s_payment_type_code == '02') {
						$a_invoice_data['invoice_allow_fine'] = 'yes';
					}
					
					$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
				
					$a_invoice_details_data = [
						'fee_id' => $s_fee_id,
						'invoice_id' => $s_invoice_id,
						'invoice_details_amount' => $d_fee_amount,
						'invoice_details_amount_number_type' => $mbo_fee_data[0]->fee_amount_number_type,
						'invoice_details_amount_sign_type' => $mbo_fee_data[0]->fee_amount_sign_type
					];
					
					$this->Im->create_invoice_details($a_invoice_details_data);

					$a_sub_invoice_data = [
						'sub_invoice_amount' => $d_fee_amount,
						'sub_invoice_amount_total' => $d_fee_amount,
						'invoice_id' => $s_invoice_id
					];
					$d_fee_installment_amount = $d_fee_amount;
					
					$s_sub_invoice_id_full = $this->Im->create_sub_invoice($a_sub_invoice_data);

					if($i_installments >= 2){
						$d_sub_inv_amount_installment = (is_int($d_fee_amount / $i_installments)) ? round(($d_fee_amount / $i_installments), 0, PHP_ROUND_HALF_UP) : intval($d_fee_amount / $i_installments);
						if (($mbo_student_data->finance_year_id >= 2021) AND ($s_payment_type_code == '02')) {
							$d_additional_installment = $d_sub_inv_amount_installment/100*10;
							$d_sub_inv_amount_installment = $d_sub_inv_amount_installment + $d_additional_installment;
						}
						$d_sub_inv_amount_installment = $this->_get_round_thousand($d_sub_inv_amount_installment);
						$d_sub_inv_amount_total = $d_sub_inv_amount_installment * $i_installments;

						$a_sub_invoice_data['sub_invoice_amount'] = $d_sub_inv_amount_total;
						$a_sub_invoice_data['sub_invoice_amount_total'] = $d_sub_inv_amount_total;
						$a_sub_invoice_data['sub_invoice_type'] = 'installment';
						$s_sub_invoice_id_installment = $this->Im->create_sub_invoice($a_sub_invoice_data);
					}
					
					for($i = 0; $i <= $i_installments; $i++){
						$s_va_number = $this->Bnim->get_va_number(
							$s_payment_type_code, 
							$s_semester_id, 
							$i, 
							'student', 
							$mbo_student_data->student_number, 
							$mbo_student_data->finance_year_id,
							$mbo_student_data->program_id
						);
						
						if($i == 0){
							if ($s_payment_type_code == '12') {
								$s_description = "Full Payment {$mbo_fee_data[0]->fee_description}";
							}
							else{
								$s_description = "Full Payment {$mbo_fee_data[0]->fee_description} Batch {$mbo_student_data->finance_year_id}";
							}

							// $s_payment_deadline_date = date('Y-m-15 23:59:59', strtotime($s_deadline_date));
							$s_payment_deadline_date = $s_deadline_date;
							$d_amount = $d_fee_amount;
						}
						else{
							$s_description = "Installment $i {$mbo_fee_data[0]->fee_description} Batch {$mbo_student_data->finance_year_id}";
							$s_payment_deadline_date = ($i == 1) ? $s_deadline_date : date('Y-m-10 23:59:59', strtotime($s_payment_deadline_date." +1 month"));
							// if ($i == 1) {
							// 	$s_deadline_date = date('Y-m-15 23:59:59', strtotime($s_deadline_date));
							// }
							// else {
							// 	$s_deadline_date = date('Y-m-10 23:59:59', strtotime($s_deadline_date." +1 month"));
							// }
							// $s_payment_deadline_date = $s_deadline_date;
							// $d_amount = round(($d_fee_amount / $i_installments), 0, PHP_ROUND_HALF_UP);
							$d_amount = (is_int($d_fee_amount / $i_installments)) ? round(($d_fee_amount / $i_installments), 0, PHP_ROUND_HALF_UP) : intval(($d_fee_amount / $i_installments) + 1);

							if (($mbo_student_data->finance_year_id >= 2021) AND ($s_payment_type_code == '02')) {
								// $d_amount += $this->d_additional_fee_installment;
								$d_additional_installment = $d_amount/100*10;
								$d_amount = $d_amount + $d_additional_installment;
							}

							$d_amount = $this->_get_round_thousand($d_amount);
						}

						if ($s_academic_year_id AND $s_semester_type_id) {
							$s_description .= ' - '.$s_academic_year_id.'/'.$mbo_semester_type_data[0]->semester_type_name;
						}

						if ($s_payment_type_code == '13') {
							$s_description .= ' '.$d_total_package_certificate.' paket';
						}
						
						$a_sub_invoice_details_data = [
							'sub_invoice_id' => ($i == 0) ? $s_sub_invoice_id_full : $s_sub_invoice_id_installment,
							'sub_invoice_details_amount' => $d_amount,
							'sub_invoice_details_amount_total' => $d_amount,
							'sub_invoice_details_va_number' => $s_va_number,
							'sub_invoice_details_deadline' => $s_payment_deadline_date,
							'sub_invoice_details_real_datetime_deadline' => $s_payment_deadline_date,
							'sub_invoice_details_description' => $s_description
						];
						$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details_data);

						// $this->activate_virtual_account($s_sub_invoice_details_id, false);
					}

					$a_return = ['code' => 0, 'message' => 'Success!', 'fullpayment_amount' => $d_fee_amount];
					
				// }
				// else{
				// 	$a_return = ['code' => 3, 'message' => 'Invoice student for fee has been created before!'];
				// }
			}else{
				$a_return = ['code' => 2, 'message' => 'Student not found!'];
			}

		}else{
			$a_return = ['code' => 1, 'message' => 'Fee not found!'];
		}

		return $a_return;
	}

	function test_get_($s_va_number) {
		// $mba_invoice_list = $this->Im->get_invoice_data([
		// 	'dsid.sub_invoice_details_va_number' => $s_va_number,
		// 	'dsid.sub_invoice_details_status != ' => 'paid'
		// ]);
		// print('<pre>');var_dump($mba_invoice_list);exit;
		// $amount = modules::run('callback/api/get_minimum_payment', $s_va_number);
		$amount = $this->get_minimum_payment($s_va_number);
		print(number_format($amount, 0, ',', '.'));exit;
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

	// public function get_total_invoice_unpaid($s_invoice_id) {
	// 	$mba_invoice_data = $this->General->get_where('dt_invoice', ['invoice_id' => $s_invoice_id]);
	// 	$mba_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $mba_invoice_data[0]->personal_data_id, 'ds.student_status != ' => 'resign']);

	// 	$mba_invoice_installment = $this->Im->get_invoice_installment($s_invoice_id);
	// 	$mbo_invoice_full_payment = $this->Im->get_invoice_full_payment($s_invoice_id);

	// 	$d_total_unpaid = 0;
	// 	if ($mba_invoice_installment) {
	// 		foreach ($mba_invoice_installment as $o_installment) {
	// 			$d_installment_unpaid = $o_installment->sub_invoice_details_amount_total - $o_installment->sub_invoice_details_amount_paid;
	// 			if ($d_installment_unpaid > 0) {
	// 				$d_total_unpaid += $d_installment_unpaid;
	// 			}
	// 		}
	// 	}
	// 	else if ($mbo_invoice_full_payment) {
	// 		$d_total_unpaid += $mbo_invoice_full_payment->sub_invoice_details_amount_total;
	// 	}
		
	// 	if ($mba_student_data[0]->academic_year_id >= 2021) {
	// 		$b_has_installment = false;
	// 		$d_total_unpaid = 0;
	// 		if ($mba_invoice_installment) {
	// 			$d_invoice_min_payment = 0;
	// 			foreach ($mba_invoice_installment as $o_installment) {
	// 				$d_installment_unpaid = $o_installment->sub_invoice_details_amount_total - $o_installment->sub_invoice_details_amount_paid;
	// 				if ($o_installment->sub_invoice_details_amount_paid > 0) {
	// 					$b_has_installment = true;
	// 				}

	// 				if ($d_installment_unpaid > 0) {
	// 					$d_total_unpaid += $d_installment_unpaid;
	// 				}
	// 			}
	// 		}

	// 		if (!$b_has_installment) {
	// 			if ($mbo_invoice_full_payment) {
	// 				$d_total_unpaid = $mbo_invoice_full_payment->sub_invoice_details_amount_total;
	// 			}
	// 		}
	// 	}

	// 	return $d_total_unpaid;
	// }

	function force_update() {
		exit;
		$a_update_billing = array(
			'trx_id' => '1691441212',
			'trx_amount' => '12000000',
			'customer_name' => 'TALAL MOHAMMED BIN SNKAR',
			'datetime_expired' => '2024-06-30 23:59:59',
			'description' => 'Billing Tuition Fee TALAL MOHAMMED BIN SNKAR'
		);
		$update = $this->Bnim->update_billing($a_update_billing);
		print('<pre>');var_dump($update);exit;
	}

	function announcement() {
		exit;
		// $a_student_id = ['033b159d-482f-453b-8862-4f1958e348c8','08faee47-08f4-4757-93a6-a92d934d2617','0b542548-591d-4b61-9fb5-79b426cac913','17ea3630-474c-4ae6-bfaf-ec6bdd5fe959','17ecfc4d-6d9e-4b7a-bc24-bda7e9719a29','1c99a0ed-8220-4b92-a04b-5df72ba2935d','214ed020-7b01-4ed9-be50-3acb617279d0','21f96a35-e0fe-4c09-a429-7db303fff9b1','2bb67365-b6ab-423c-988a-548c991266af','2ce485fd-b21c-4504-8d8e-d8220a67ba36','356231a9-2d0e-4e4d-adf2-ccb685804919','3fb17179-4d5b-4734-87ae-f171a85212e5','3fe6d2a4-1772-44ae-aca9-8a54b2aeeaa4','450bcb67-5e90-4158-b48e-ffed93413715','477e3295-4af8-4865-a73d-0b5730820253','4cea2049-4b45-4ba4-aca2-34c46cb3519b','4eed3289-b288-4464-9b7f-a679b155cbe6','52912ca3-6146-407c-8c31-e5d89faaed44','549f9f9f-3735-4a7b-81d5-3611172c5391','5826dea1-9da2-454b-b586-82cb8761ef05','6d21f8c4-4b3a-48cd-9c0d-da36d18a819c','718c0df2-5f8a-4031-ad31-d08286528b42','7a2b70bb-93f4-48d8-b437-5c477053b613','8138476e-c083-48db-a5ae-e9fc0c664583','87144876-f989-427d-a746-e572fa2e4593','87f68919-5175-4edb-8f0a-c43103651af0','8d16d280-84c5-487c-abf3-99f76e4d7f08','9182ddfa-a209-4d8b-9194-698fb988426f','9a834caa-e22d-4f62-8b88-adf910aa6126','9b8763ad-7966-4146-a220-46954e942e0b','9efca642-14c2-4880-9ef9-4b46eee9efc0','ad47b529-49e8-4864-a708-6ba18b8bfa3c','b43a5844-89c6-4101-ba31-b405bbcd10e5','c07ee362-ca65-4f21-8874-025882786c1d','c99c2948-e694-484e-a620-d68d05adb450','cb59c9de-9c73-4908-99a4-c04c138ca848','cb5b759e-67e8-4720-b540-1ebf871673f1','cf35718b-8cc2-4d84-a6cc-29b866c8115c','d92907d0-adc6-45e3-8bf6-dc1e37c13966','da66ed8e-412e-4bef-8953-7eab9e672532','df2d1a19-df74-456d-9647-29d30ea7aa11','e22df690-460c-4671-ab89-497103479dc0','e4f31647-7082-430d-909f-99346aa6ae1f','e916b364-c22f-4138-a019-2779f27eec35','eb30a213-0b21-4834-84c8-0e681dc2d4d2','ecffad0a-40e7-4ad8-843f-a2858ac89123','f19f2138-e4fa-4e76-b54a-76bfc6715039','f77ab29e-b265-4889-90fa-3695fe30e366','fbb23301-056d-4147-9bd2-df7f0059cbf6','fea67766-d047-4499-a267-f078d8096794','ff13416c-0ec5-4115-8a7d-acaf6e99b183'];
		// $a_student_id = ['004ddb6b-2014-4c77-bbd8-b75d38d82770','03253eda-1c2d-4e1a-9f72-e38c43ff20ed'];
		$message = <<<TEXT
Dear Students,

Announcement
Students who still have payment pending,  they will not be able to access student portal, and will not be able to join Final exam.

For students who are working on their thesis, if all the administration has not been completed, access to student portal is blocked and they cannot submit thesis work.

--
Salam,
Chandra Hendrianto
Academic Services Center

IULI Campus
IULI - Associate Tower - Intermark
Jl. Lingkar Timur - BSD City
Tangerang Selatan 15310
Tel. 0852 123 18000 (IULI Hotline)
HP. 0856 9734 8846
www.iuli.ac.id  
TEXT;
		$mba_list_student = $this->Stm->get_student_colectif_filtered(false, false, $a_student_id);
		$s_attachment = APPPATH.'uploads/temp/Announcement of Tuition Fee Deadline Payment 20231206.pdf';
		if ($mba_list_student) {
			$a_bcc_email = array('employee@company.ac.id');
			// $s_email_from = '[IULI] Academic Services Center';
			$s_email_subject = 'Announcement';
			$s_mail_reply = 'employee@company.ac.id';

			foreach ($mba_list_student as $o_student) {
				$mbo_family_data = $this->Fm->get_family_by_personal_data_id($o_student->personal_data_id);
				$mba_parent_email = (!is_null($o_student->personal_data_email)) ? $o_student->personal_data_email : false;
				if($mbo_family_data){
					$mba_family_members = $this->Fm->get_family_members($mbo_family_data->family_id, array(
						'family_member_status != ' => 'child'
					));
					if($mba_family_members){
						$mba_parent_email = (is_array($mba_parent_email)) ? $mba_parent_email : array();
						foreach($mba_family_members as $family){
							if (!is_null($family->personal_data_email)) {
								array_push($mba_parent_email, $family->personal_data_email);
							}
						}
					}
				}

				$this->email->clear(TRUE);
				$this->email->attach($s_attachment);
				$this->email->from('employee@company.ac.id', '[IULI] Academic Services Center');
				// $this->email->to('employee@company.ac.id');
				$this->email->to($o_student->student_email);
				if (is_array($mba_parent_email)){
					$this->email->cc($mba_parent_email);
				}
				$this->email->bcc($a_bcc_email);
				$this->email->subject($s_email_subject);
				$this->email->reply_to($s_mail_reply);
				$this->email->message($message);

				print($o_student->personal_data_name);
				if(!$this->email->send()){
					$s_cc = implode('|', $mba_parent_email);
					print(' -- '.$o_student->student_email.' --> '.$s_cc);
				}
				print('<br>');
				// exit;
			}
		}
	}

	function activated_virtual_account($s_va_number) {
		// $mba_invoice_list = $this->Im->get_invoice_data([
		// 	'dsid.sub_invoice_details_va_number' => $s_va_number,
		// 	'dsid.sub_invoice_details_status != ' => 'paid'
		// ], ['created', 'pending']);
		$mba_invoice_list = $this->Im->get_invoice_by_deadline([
			'sid.sub_invoice_details_va_number' => $s_va_number,
			'sid.sub_invoice_details_status != ' => 'paid'
		]);
		$current_time = date('Y-m-d H:i:s');

		// print('<pre>');var_dump($mba_invoice_list);exit;
		if ($mba_invoice_list) {
			$mba_student_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mba_invoice_list[0]->personal_data_id]);
			// $mba_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $mba_invoice_list[0]->personal_data_id, 'ds.student_status != ' => 'resign']);
			// if ($mba_student_data) {
			if ($mba_student_data) {
				$s_payment_type = substr($s_va_number, -2);
				$o_student_data = $mba_student_data[0];
				$s_trx_id = (is_null($mba_invoice_list[0]->trx_id)) ? false : $mba_invoice_list[0]->trx_id;
				// var_dump($s_trx_id);exit;
				// $d_min_payment = $this->get_minimum_payment($s_va_number);
				$d_min_payment = modules::run('callback/api/get_minimum_payment', $s_va_number);
				$a_trx_data = array(
					'trx_amount' => $d_min_payment,
					// 'trx_amount' => 26730000,
					// 'billing_type' => ($s_payment_type == '02') ? 'n' : 'c',
					'billing_type' => 'n',
					'customer_name' => $o_student_data->personal_data_name,	
					'virtual_account' => $s_va_number,
					'description' => 'Billing Tuition Fee '.$o_student_data->personal_data_name,
					// 'datetime_expired' => date('Y-m-d 23:59:59', strtotime((intval($o_student_data->finance_year_id) + 5).'-12-31')),
					'datetime_expired' => date('Y-m-d 23:59:59', strtotime('2025-01-10')),
					'customer_email' => 'bni.employee@company.ac.id'
				);

				$s_client_id = substr($s_va_number, 1, 3);
				if ($s_client_id == '141') {
					$this->Bnim->set_environment('sandbox');
				}

				if ($s_trx_id) {
					$this->cancel_payment($s_trx_id);
				}

				$a_bni_result = $this->Bnim->create_billing($a_trx_data);
				if($a_bni_result['status'] == '000'){
					// $a_sub_invoice_details_update['trx_id'] = $a_bni_result['trx_id'];
					$this->Im->update_sub_invoice_details(
						['trx_id' => $a_bni_result['trx_id']], ['sub_invoice_details_va_number' => $s_va_number]
					);
					print('oke<br>');
					print('<pre>');
					var_dump($a_bni_result);
				}
				else{
					print('<pre>');var_dump($a_bni_result);exit;
					if($a_bni_result['status'] == '102'){
						$a_update_billing = array(
							'trx_id' => $a_bni_result['trx_id'],
							'trx_amount' => 999,
							'customer_name' => 'CANCEL PAYMENT',
							'datetime_expired' => '2020-01-01 23:59:59',
							'description' => 'CANCEL PAYMENT'
						);
						$this->Bnim->update_billing($a_update_billing);
						print('Cancel Billing !'.$s_va_number);
					}
					else{
						$this->email->from('employee@company.ac.id');
						$this->email->to(array('employee@company.ac.id'));
						$this->email->subject('ERROR CHECK BILLING');
						$this->email->message(json_encode($a_bni_result));
						$this->email->send();
						// print('failed!');
					}
					print('gagal');
				}
			}
			else {
				// customer tidak ditemukan
				print('customer tidak ditemukan');
			}
		}
		else {
			// va unpaid not found!
			print('va unpaid not found!');
		}
	}

	function activate_billing($b_ajax_request = false, $s_va = false) {
		if ($b_ajax_request) {
			$s_va = $this->input->post('virtual_account');
		}

		// $mba_va_data = $this->Im->get_invoice_by_deadline([
		// 	'sid.sub_invoice_details_va_number' => $s_va,
		// 	'sid.sub_invoice_details_status != ' => 'paid'
		// ], false, true);
		$mba_va_data = $this->General->get_where('dt_sub_invoice_details', [
			'sub_invoice_details_va_number' => $s_va,
			'sub_invoice_details_status != ' => 'paid',
			//  tidak bisa dipakai karena invoice cancel ikut keambil
		]);
		$d_min_payment = modules::run('callback/api/get_minimum_payment', $s_va);
		if (($mba_va_data) AND ($d_min_payment > 10000)) {
			$mba_invoice_data = $this->Im->get_invoice_by_deadline(['sid.sub_invoice_details_id' => $mba_va_data[0]->sub_invoice_details_id]);
			// print('<pre>');var_dump($mba_va_data);exit;
			$mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mba_invoice_data[0]->personal_data_id]);
			if ($mba_personal_data) {
				$s_trx_id = '';
				$s_last_date_deadline = date('Y-m-d H:i:s');
				foreach ($mba_va_data as $o_invoice_details) {
					if (!is_null($o_invoice_details->trx_id)) {
						$s_trx_id = $o_invoice_details->trx_id;
					}
					// else if (!empty($s_trx_id)) {
					// 	$this->General->update_data('dt_sub_invoice_details', ['trx_id' => $s_trx_id], ['sub_invoice_details_id' => $o_invoice_details->sub_invoice_details_id]);
					// }

					// if ($s_last_date_deadline < date('Y-m-d H:i:s', strtotime($o_invoice_details->sub_invoice_details_real_datetime_deadline))) {
					// 	$s_last_date_deadline = date('Y-m-d H:i:s', strtotime($o_invoice_details->sub_invoice_details_real_datetime_deadline));
					// }
				}
				$s_last_date_deadline = date('Y-m-d H:i:s', strtotime($s_last_date_deadline." +6 month"));

				// print('<pre>');var_dump($s_last_date_deadline);exit;

				if (empty($s_trx_id)) {
					$a_billing_data = array(
						'trx_amount' => $d_min_payment,
						'billing_type' => 'n',
						'customer_name' => $mba_personal_data[0]->personal_data_name,
						'virtual_account' => $s_va,
						'description' => 'Invoice '.$mba_invoice_data[0]->payment_type_name,
						'datetime_expired' => $s_last_date_deadline,
						'customer_email' => 'bni.employee@company.ac.id'
					);
					
					$a_bni_result = $this->Bnim->create_billing($a_billing_data);
				}
				else {
					$check_va = $this->Bnim->inquiry_billing($s_trx_id, true);
					if (($check_va) AND (!array_key_exists('status', $check_va))) {
						if ($check_va['va_status'] != 1) {
							$a_billing_data = array(
								'trx_amount' => $d_min_payment,
								'billing_type' => 'n',
								'customer_name' => $mba_personal_data[0]->personal_data_name,
								'virtual_account' => $s_va,
								'description' => 'Invoice '.$mba_invoice_data[0]->payment_type_name,
								'datetime_expired' => $s_last_date_deadline,
								'customer_email' => 'bni.employee@company.ac.id'
							);
							
							$a_bni_result = $this->Bnim->create_billing($a_billing_data);
						}
						else {
							$a_billing_data = [
								'trx_id' => $s_trx_id,
								'trx_amount' => $d_min_payment,
								'customer_name' => $mba_personal_data[0]->personal_data_name,
								'datetime_expired' => $s_last_date_deadline,
								'description' => 'Invoice '.$mba_invoice_data[0]->payment_type_name,
							];
							$a_bni_result = $this->Bnim->update_billing($a_billing_data);
						}
					}
					else {
						$a_bni_result = false;
					}
				}
				
				if (($a_bni_result) AND (!array_key_exists('message', $a_bni_result))) {
					if ($a_bni_result['status'] === '000') {
						$s_trx_id = $a_bni_result['trx_id'];
						foreach ($mba_va_data as $o_invoice_details) {
							// if (is_null($o_invoice_details->trx_id)) {
								$a_sub_invoice_details_update = [
									'trx_id' => $s_trx_id
								];
								$this->Im->update_sub_invoice_details($a_sub_invoice_details_update, ['sub_invoice_details_id' => $o_invoice_details->sub_invoice_details_id]);
							// }
						}
					}
					$a_return = ['code' => 0, 'message' => 'Success', 'data' => $a_bni_result];
				}
				else if (($a_bni_result) AND (array_key_exists('message', $a_bni_result))) {
					$a_return = ['code' => 1, 'message' => $a_bni_result['message'], 'data' => $a_bni_result];
				}
				else {
					$a_return = ['code' => 2, 'message' => 'Fail update billing data!'];
				}
			}
			else {
				$a_return = ['code' => 1, 'message' => 'User not found!'];
			}
		}
		else {
			$a_return = ['code' => 1, 'message' => 'Transaction not found!'];
		}

		if ($b_ajax_request) {
			print json_encode($a_return);
		}
		else {
			return $a_return;
		}
	}

	function update_billing($b_ajax_request = false, $s_va = false) {
		if ($b_ajax_request) {
			$s_va = $this->input->post('virtual_account');
		}

		$s_va_data = $this->General->get_where('dt_sub_invoice_details', ['sub_invoice_details_va_number' => $s_va, 'trx_id != ' => null]);
		if ($s_va_data) {
			$s_trx_id = $s_va_data[0]->trx_id;
			$check_va = $this->Bnim->inquiry_billing($s_trx_id, true);
			if (($check_va) AND (!array_key_exists('status', $check_va))) {
				// $d_min_payment = $this->get_minimum_payment($s_va);
				$d_min_payment = modules::run('callback/api/get_minimum_payment', $s_va);
				if ($d_min_payment > 1000) {
					$a_update_billing = array(
						'trx_id' => $check_va['trx_id'],
						'trx_amount' => $d_min_payment,
						'customer_name' => $check_va['customer_name'],
						'datetime_expired' => $check_va['datetime_expired'],
						'description' => $check_va['description'],
						'customer_email' => 'bni.employee@company.ac.id'
					);
					
					$update_billing = $this->Bnim->update_billing($a_update_billing);
					if (($update_billing) AND (!array_key_exists('message', $update_billing))) {
						$a_return = ['code' => 0, 'message' => 'Success', 'data' => $update_billing];
					}
					else if (($update_billing) AND (array_key_exists('message', $update_billing))) {
						$a_return = ['code' => 1, 'message' => $update_billing['message'], 'data' => $update_billing];
					}
					else {
						$a_return = ['code' => 1, 'message' => 'Fail update billing data!'];
					}
				}
				else {
					$a_return = ['code' => 1, 'message' => 'Minimum payment is less than 1000'];
				}
			}
			else {
				$a_return = ['code' => 1, 'message' => 'Failed to check virtual account data with BNI'];
			}
		}
		else {
			$a_return = ['code' => 1, 'message' => 'Transaction not found!'];
		}

		if ($b_ajax_request) {
			print json_encode($a_return);
		}
		else {
			return $a_return;
		}
	}

	function set_force_update_billing() {
		// exit;
		$check_va = $this->Bnim->inquiry_billing('1492804906', true);
		print('<pre>');var_dump($check_va);exit;
		$d_min_payment = $this->get_minimum_payment('8310112102008002');
		// if (($check_va) AND (!array_key_exists('status', $check_va))) {
		// 	$s_va_status = $check_va['va_status'];
		// 	$d_bni_amount = $check_va['trx_amount'];
		// 	if ($d_min_payment > 1000) {
		// 		$a_update_billing = array(
		// 			'trx_id' => $check_va['trx_id'],
		// 			'trx_amount' => $d_min_payment,
		// 			'customer_name' => $check_va['customer_name'],
		// 			'datetime_expired' => $check_va['datetime_expired'],
		// 			'description' => $check_va['description'],
		// 			'customer_email' => 'bni.employee@company.ac.id'
		// 		);
				
		// 		$update_billing = $this->Bnim->update_billing($a_update_billing);
		// 		print('<pre>');var_dump($update_billing);
		// 	}
		// 	else {
		// 		print($d_min_payment);
		// 	}
		// }
		// else {
		// 	var_dump($check_va);
		// }
	}

	private function _create_new_single_invoice(
		$s_personal_data_id,
		$s_semester_id, 
		$s_total_credit, 
		$s_fee_id, 
		$s_payment_type_code, 
		$s_deadline_date,
		$i_installments,
		$s_academic_year_id = false,
		$s_semester_type_id = false,
		$d_total_package_certificate = 1
	)
	{
		$mbo_fee_data = $this->Im->get_fee([
			'fee_id' => $s_fee_id
		]);

		$mbo_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $s_semester_type_id]);
		
		if ($mbo_fee_data) {
			$s_deadline_date = date('Y-m-d 23:59:59', strtotime($s_deadline_date));

			$mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id, [
				'student_status != ' => 'resign'
			]);

			if ($mbo_student_data) {
				$mbo_student_invoice_data = $this->Im->student_has_invoice_fee_id($mbo_student_data->personal_data_id, $s_fee_id, ['created', 'pending', 'paid']);
				$mba_ofse_repeat_data = false;

				if ($s_payment_type_code == '07') {
					$mba_ofse_repeat_data = $this->General->get_where('dt_score', [
						'student_id' => $mbo_student_data->student_id,
						'academic_year_id' => $s_academic_year_id,
						'semester_type_id' => ($s_semester_type_id == 2) ? 6 : 4,
						'score_approval' => 'approved'
					]);
				}
				
				$s_mode = (in_array($s_personal_data_id, $this->student_personal_id_dummy)) ? 'development' : 'production';
				if ($s_payment_type_code == '01') {
					$s_year_pad = substr($mbo_student_data->finance_year_id, 2);
					$s_va_number = $this->Bnim->generate_va_number(
						$s_payment_type_code,
						'candidate', 
						$s_year_pad.$mbo_student_data->study_program_code, 
						$mbo_student_data->finance_year_id,
						$mbo_student_data->program_id,
						$s_mode
					);
				}
				else {
					$s_va_number = $this->Bnim->generate_va_number(
						$s_payment_type_code,
						'student', 
						$mbo_student_data->student_number, 
						$mbo_student_data->finance_year_id,
						$mbo_student_data->program_id,
						$s_mode
					);
				}
				
				if ($s_va_number) {
					$d_fee_amount = $mbo_fee_data[0]->fee_amount;
					$d_total_credit_german = 0;
					// $mbo_invoice_setting = $this->General->get_where('ref_settings_invoice', [
					// 	'academic_year_id' => $mbo_student_data->finance_year_id,
					// 	'program_id' => $mbo_student_data->program_id
					// ])[0];

					if ($s_semester_id != '') {
						$mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $s_semester_id])[0];
		
						if ($s_payment_type_code == '02'){
							if (($mbo_semester_data) AND (intval($mbo_semester_data->semester_number) > 8)) {
								$d_fee_amount = $d_fee_amount * $s_total_credit;
							}
						}
						else if (($mbo_semester_data) AND ($s_payment_type_code == '04')) {
							$mba_krs_data = $this->Scm->get_score_data([
								'sc.student_id' => $mbo_student_data->student_id,
								'sc.academic_year_id' => $s_academic_year_id,
								'sc.semester_type_id' => ($s_semester_type_id == 2) ? 8 : 7,
								'sc.score_approval' => 'approved'
							]);

							if ($mba_krs_data) {
								$i_total_credit = 0;
								foreach ($mba_krs_data as $o_krs) {
									$i_total_credit += $o_krs->curriculum_subject_credit;
								}

								if ($i_total_credit > 0) {
									$d_fee_amount = $d_fee_amount * $i_total_credit;
								}
							}
						}
						else if ($s_payment_type_code == '13') {
							$d_fee_amount = $d_fee_amount * $d_total_package_certificate;
						}
						else if ($s_payment_type_code == '07') {
							if ($mba_ofse_repeat_data) {
								$d_fee_amount = $d_fee_amount * count($mba_ofse_repeat_data);
							}
						}
					}
					else if ($s_payment_type_code == '18') {
						$mba_score_data_german = $this->Scm->get_score_like_subject_name([
							'sc.student_id' => $mbo_student_data->student_id,
							'sc.academic_year_id' => $s_academic_year_id,
							'sc.semester_type_id' => $s_semester_type_id,
							'sc.score_approval' => 'approved'
						], 'german');

						if ($mba_score_data_german) {
							foreach ($mba_score_data_german as $o_score) {
								$d_total_credit_german += $o_score->curriculum_subject_credit;
							}
						}

						$d_fee_amount = $d_fee_amount * $d_total_credit_german;
					}

					if ($s_payment_type_code == '04') {
						$s_invoice_desc = $mbo_fee_data[0]->fee_description.' for total '.$s_total_credit.' credit/SKS';
					}
					else if (($s_payment_type_code == '07') AND ($mba_ofse_repeat_data)) {
						$s_invoice_desc = $mbo_fee_data[0]->fee_description.' for total '.count($mba_ofse_repeat_data).' subject';
					}
					else if ($s_payment_type_code == '18') {
						$s_invoice_desc = $mbo_fee_data[0]->fee_description.' for total '.$d_total_credit_german.' credit/SKS';
					}
					else {
						$s_invoice_desc = $mbo_fee_data[0]->fee_description;
					}
					// $s_invoice_desc = ($s_payment_type_code == '04') ? $mbo_fee_data[0]->fee_description.' for total '.$s_total_credit.' credit/SKS' : $mbo_fee_data[0]->fee_description;
					
					if ($s_academic_year_id AND $s_semester_type_id) {
						$s_invoice_desc .= ' - '.$s_academic_year_id.'/'.$s_semester_type_id;
					}

					$a_invoice_data = [
						'invoice_description' => $s_invoice_desc,
						'personal_data_id' => $mbo_student_data->personal_data_id,
						'invoice_number' => $this->Im->get_invoice_number($s_payment_type_code),
						'invoice_allow_fine' => 'no'
					];

					if ($s_academic_year_id AND $s_semester_type_id) {
						$a_invoice_data['academic_year_id'] = $s_academic_year_id;
						$a_invoice_data['semester_type_id'] = $s_semester_type_id;
					}

					if ($s_payment_type_code == '02') {
						$a_invoice_data['invoice_allow_fine'] = 'yes';
					}
					
					$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
				
					$a_invoice_details_data = [
						'fee_id' => $s_fee_id,
						'invoice_id' => $s_invoice_id,
						'invoice_details_amount' => $d_fee_amount,
						'invoice_details_amount_number_type' => $mbo_fee_data[0]->fee_amount_number_type,
						'invoice_details_amount_sign_type' => $mbo_fee_data[0]->fee_amount_sign_type
					];
					
					$this->Im->create_invoice_details($a_invoice_details_data);

					$a_sub_invoice_data = [
						'sub_invoice_amount' => $d_fee_amount,
						'sub_invoice_amount_total' => $d_fee_amount,
						'invoice_id' => $s_invoice_id
					];
					$d_fee_installment_amount = $d_fee_amount;
					
					$s_sub_invoice_id_full = $this->Im->create_sub_invoice($a_sub_invoice_data);

					if($i_installments >= 2){
						$d_sub_inv_amount_installment = (is_int($d_fee_amount / $i_installments)) ? round(($d_fee_amount / $i_installments), 0, PHP_ROUND_HALF_UP) : intval($d_fee_amount / $i_installments);
						if (($mbo_student_data->finance_year_id >= 2021) AND ($s_payment_type_code == '02')) {
							$d_additional_installment = $d_sub_inv_amount_installment/100*10;
							$d_sub_inv_amount_installment = $d_sub_inv_amount_installment + $d_additional_installment;
						}
						$d_sub_inv_amount_installment = $this->_get_round_thousand($d_sub_inv_amount_installment);
						$d_sub_inv_amount_total = $d_sub_inv_amount_installment * $i_installments;

						$a_sub_invoice_data['sub_invoice_amount'] = $d_sub_inv_amount_total;
						$a_sub_invoice_data['sub_invoice_amount_total'] = $d_sub_inv_amount_total;
						$a_sub_invoice_data['sub_invoice_type'] = 'installment';
						$s_sub_invoice_id_installment = $this->Im->create_sub_invoice($a_sub_invoice_data);
					}

					for($i = 0; $i <= $i_installments; $i++){
						if($i == 0){
							if ($s_payment_type_code == '12') {
								$s_description = "Full Payment {$mbo_fee_data[0]->fee_description}";
							}
							else{
								$s_description = "Full Payment {$mbo_fee_data[0]->fee_description} Batch {$mbo_student_data->finance_year_id}";
							}

							// $s_payment_deadline_date = date('Y-m-15 23:59:59', strtotime($s_deadline_date));
							$s_payment_deadline_date = $s_deadline_date;
							$d_amount = $d_fee_amount;
						}
						else{
							$s_description = "Installment $i {$mbo_fee_data[0]->fee_description} Batch {$mbo_student_data->finance_year_id}";
							$s_payment_deadline_date = ($i == 1) ? $s_deadline_date : date('Y-m-10 23:59:59', strtotime($s_payment_deadline_date." +1 month"));
							
							$d_amount = (is_int($d_fee_amount / $i_installments)) ? round(($d_fee_amount / $i_installments), 0, PHP_ROUND_HALF_UP) : intval(($d_fee_amount / $i_installments) + 1);

							if (($mbo_student_data->finance_year_id >= 2021) AND ($s_payment_type_code == '02')) {
								$d_additional_installment = $d_amount/100*10;
								$d_amount = $d_amount + $d_additional_installment;
							}

							$d_amount = $this->_get_round_thousand($d_amount);
						}

						if ($s_academic_year_id AND $s_semester_type_id) {
							$s_description .= ' - '.$s_academic_year_id.'/'.$mbo_semester_type_data[0]->semester_type_name;
						}

						if ($s_payment_type_code == '13') {
							$s_description .= ' '.$d_total_package_certificate.' paket';
						}
						
						$a_sub_invoice_details_data = [
							'sub_invoice_id' => ($i == 0) ? $s_sub_invoice_id_full : $s_sub_invoice_id_installment,
							'sub_invoice_details_amount' => $d_amount,
							'sub_invoice_details_amount_total' => $d_amount,
							'sub_invoice_details_va_number' => $s_va_number,
							'sub_invoice_details_deadline' => $s_payment_deadline_date,
							'sub_invoice_details_real_datetime_deadline' => $s_payment_deadline_date,
							'sub_invoice_details_description' => $s_description
						];
						$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details_data);

						// $this->activate_virtual_account($s_sub_invoice_details_id, false);
					}

					$a_return = ['code' => 0, 'message' => 'Success!', 'fullpayment_amount' => $d_fee_amount];
				}
				else{
					$a_return = ['code' => 3, 'message' => 'Failed generate virtual account number!'];
				}
			}else{
				$a_return = ['code' => 2, 'message' => 'Student not found!'];
			}

		}else{
			$a_return = ['code' => 1, 'message' => 'Fee not found!'];
		}

		return $a_return;
	}

	function calculate_billing_student(
		$s_personal_data_id,
		$s_semester_id,
		$s_total_credit,
		$s_fee_id,
		$s_academic_year_id = false,
		$s_semester_type_id = false,
		$d_total_package_certificate = 1
	) {
		$mbo_fee_data = $this->Im->get_fee([
			'fee_id' => $s_fee_id
		]);

		$mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id, [
			'student_status != ' => 'resign'
		]);

		$total_billed = 0;
		if (($mbo_fee_data) AND ($mbo_student_data)) {
			$o_fee = $mbo_fee_data[0];
			$s_payment_type_code = $o_fee->payment_type_code;
			$d_fee_amount = $o_fee->fee_amount;
			$d_total_discount = 0;

			$mba_ofse_repeat_data = false;
			$mba_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $s_semester_id]);
			if ($s_payment_type_code == '02'){
				$mba_student_has_scholarship_main = $this->Finm->get_personal_data_scholarship([
					'pds.personal_data_id' => $mbo_student_data->personal_data_id,
					'pds.scholarship_status' => 'active',
					'rs.scholarship_fee_type' => 'main',
					'rs.cut_of_tuition_fee' => 'yes'
				]);

				$mba_student_has_scholarship_additional = $this->Finm->get_personal_data_scholarship([
					'pds.personal_data_id' => $mbo_student_data->personal_data_id,
					'pds.scholarship_status' => 'active',
					'rs.scholarship_fee_type' => 'additional',
					'rs.cut_of_tuition_fee' => 'yes'
				]);
				
				if (($mba_semester_data) AND (intval($mba_semester_data[0]->semester_number) > 8)) {
					if (($s_total_credit == '') OR ($s_total_credit = '0')) {
						$mba_score_data = $this->Scm->get_score_semester([
							'sc.student_id' => $mbo_student_data->student_id,
							'sc.academic_year_id' => $s_academic_year_id,
							'sc.semester_type_id' => $s_semester_type_id,
							'sc.score_approval' => 'approved',
							'cs.curriculum_subject_credit > ' => 0
						]);

						if ($mba_score_data) {
							$s_total_credit = 0;
							foreach ($mba_score_data as $o_score) {
								$s_total_credit += intval($o_score->curriculum_subject_credit);
							}
						}
					}
					
					$d_fee_amount = ($s_total_credit > 0) ? $d_fee_amount * $s_total_credit : 0;
				}
				else {
					if ($mba_student_has_scholarship_main) {
						$mba_fee_data = $this->Im->get_fee([
							'payment_type_code' => '02',
							'program_id' => $mbo_student_data->program_id,
							'scholarship_id' => $mbo_student_has_scholarship_main->scholarship_id,
							'study_program_id' => $mbo_student_data->study_program_id,
							'academic_year_id' => $mbo_student_data->finance_year_id,
							'semester_id' => $s_semester_id,
							'fee_amount_type' => 'main'
						]);
						if ($mba_fee_data) {
							$o_fee = $mba_fee_data[0];
							$d_fee_amount = $o_fee->fee_amount;
						}
					}

					if ($mba_student_has_scholarship_additional) {
						foreach ($mba_student_has_scholarship_additional as $o_scholarship_additional) {
							$a_fee_filter = [
								'program_id' => $mbo_student_data->program_id,
								'scholarship_id' => $o_scholarship_additional->scholarship_id,
								'academic_year_id' => $mbo_student_data->finance_year_id,
								'fee_amount_type' => 'additional'
							];
		
							if ($o_scholarship_additional->specific_user == 'yes') {
								$a_fee_filter['payment_type_code'] = '02';
								$a_fee_filter['study_program_id'] = $mbo_student_data->study_program_id;
								$a_fee_filter['semester_id'] = $s_semester_id;
							}
		
							if (!is_null($o_scholarship_additional->scholarship_fee_id)) {
								$a_fee_filter = ['fee_id' => $o_scholarship_additional->scholarship_fee_id];
							}
	
							$mbo_fee_data_additional = $this->Im->get_fee($a_fee_filter)[0];
	
							if ($mbo_fee_data_additional) {
								if ($mbo_fee_data_additional->fee_amount_sign_type == 'negative') {
									if ($mbo_fee_data_additional->fee_amount_number_type == 'number') {
										$d_total_discount += $mbo_fee_data_additional->fee_amount;
										// $d_fee_amount -= $mbo_fee_data_additional->fee_amount;
									}else{
										$d_fee_amount_additional = ($d_fee_amount * $mbo_fee_data_additional->fee_amount) / 100;
										// $d_fee_amount -= $d_fee_amount_additional;
										$d_total_discount += $d_fee_amount_additional;
									}
								}else{
									if ($mbo_fee_data_additional->fee_amount_number_type == 'number') {
										// $d_fee_amount += $mbo_fee_data_additional->fee_amount;
										$d_total_discount -= $mbo_fee_data_additional->fee_amount;
									}else{
										$d_fee_amount_additional = ($d_fee_amount * $mbo_fee_data_additional->fee_amount) / 100;
										// $d_fee_amount += $d_fee_amount_additional;
										$d_total_discount -= $d_fee_amount_additional;
									}
								}
							}
						}
					}
				}
			}
			else if (($mbo_semester_data) AND ($s_payment_type_code == '04')) {
				$mba_krs_data = $this->Scm->get_score_data([
					'sc.student_id' => $mbo_student_data->student_id,
					'sc.academic_year_id' => $s_academic_year_id,
					'sc.semester_type_id' => ($s_semester_type_id == 2) ? 8 : 7,
					'sc.score_approval' => 'approved'
				]);

				$s_total_credit = 0;
				if ($mba_krs_data) {
					foreach ($mba_krs_data as $o_krs) {
						$s_total_credit += $o_krs->curriculum_subject_credit;
					}
				}

				$d_fee_amount = ($s_total_credit > 0) ? $d_fee_amount * $s_total_credit : 0;
			}
			else if ($s_payment_type_code == '07') {
				$mba_ofse_repeat_data = $this->General->get_where('dt_score', [
					'student_id' => $mbo_student_data->student_id,
					'academic_year_id' => $s_academic_year_id,
					'semester_type_id' => ($s_semester_type_id == 2) ? 6 : 4,
					'score_approval' => 'approved'
				]);

				$d_fee_amount = ($mba_ofse_repeat_data) ? $d_fee_amount * count($mba_ofse_repeat_data) : 0;
			}
			else if ($s_payment_type_code == '13') {
				$d_fee_amount = $d_fee_amount * $d_total_package_certificate;
			}
			else if ($s_payment_type_code == '18') {
				$mba_score_data_german = $this->Scm->get_score_like_subject_name([
					'sc.student_id' => $mbo_student_data->student_id,
					'sc.academic_year_id' => $s_academic_year_id,
					'sc.semester_type_id' => $s_semester_type_id,
					'sc.score_approval' => 'approved'
				], 'german');

				$s_total_credit = 0;
				if ($mba_score_data_german) {
					foreach ($mba_score_data_german as $o_score) {
						$s_total_credit += $o_score->curriculum_subject_credit;
					}
				}

				$d_fee_amount = ($s_total_credit > 0) ? $d_fee_amount * $d_total_credit_german : 0;
			}

			$total_billed = $d_fee_amount - $d_total_discount;
		}

		return $total_billed;
	}

	public function test_round()
	{
		$d_oldstring = 2740833;
		$d_last_string = substr($d_oldstring, (strlen($d_oldstring) - 3), (strlen($d_oldstring) - 1));
		$d_front_string = substr($d_oldstring, 0, (strlen($d_oldstring) - 3));
		$d_ex = (intval($d_last_string) != 0) ? ($d_front_string + 1) : $d_front_string;
		$d_ex .= '000';
		// $d_new_string = round($d_string, 0, PHP_ROUND_HALF_UP);
		print('<pre>');
		var_dump($d_ex);exit;
	}

	private function _get_round_thousand($d_billing)
	{
		$d_billing = intval($d_billing);
		$d_last_string = substr($d_billing, (strlen($d_billing) - 3), (strlen($d_billing) - 1));
		$d_front_string = substr($d_billing, 0, (strlen($d_billing) - 3));
		$d_ex = (intval($d_last_string) != 0) ? ($d_front_string + 1) : $d_front_string;
		$d_ex .= '000';
		// $d_new_string = round($d_string, 0, PHP_ROUND_HALF_UP);
		return $d_ex;
	}

	private function _create_scholarship_invoice_tuition_fee(
		$s_personal_data_id,
		$s_semester_id,
		$s_fee_id,
		$s_deadline_date,
		$i_installments,
		$s_academic_year_id = false,
		$s_semester_type_id = false
	)
	{
		$mbo_student_data = $this->General->get_where('dt_student', [
			'personal_data_id' => $s_personal_data_id,
			'student_status' => 'active'
		])[0];

		// 'program_id' => $mbo_student_data->program_id,
		// 				'scholarship_id' => $o_scholarship_additional->scholarship_id,
		// 				'academic_year_id' => $mbo_student_data->finance_year_id,
		// 				'fee_amount_type' => 'additional'
		$mbo_student_invoice_data = $this->Im->student_has_invoice_data($s_personal_data_id, [
			'df.fee_amount_type' => 'main',
			'df.academic_year_id' => $mbo_student_data->finance_year_id,
			'df.program_id' => $mbo_student_data->program_id,
			'df.payment_type_code' => '02',
			'df.semester_id' => $s_semester_id
		]);

		if (!$mbo_student_invoice_data) {
			$mba_student_has_scholarship_additional = $this->Finm->get_personal_data_scholarship([
				'pds.personal_data_id' => $s_personal_data_id,
				'rs.scholarship_fee_type' => 'additional',
				'rs.cut_of_tuition_fee' => 'yes'
			]);
			
			$mbo_student_has_scholarship_main = $this->Finm->get_personal_data_scholarship([
				'pds.personal_data_id' => $s_personal_data_id,
				'rs.scholarship_fee_type' => 'main',
				'rs.cut_of_tuition_fee' => 'yes'
			])[0];
	
			$mbo_prodi_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mbo_student_data->study_program_id])[0];
			
			if ($mbo_student_has_scholarship_main) {
				$mbo_fee_data = $this->Im->get_fee([
					'payment_type_code' => '02',
					'program_id' => $mbo_student_data->program_id,
					'scholarship_id' => $mbo_student_has_scholarship_main->scholarship_id,
					'study_program_id' => $mbo_student_data->study_program_id,
					'academic_year_id' => $mbo_student_data->finance_year_id,
					'semester_id' => $s_semester_id,
					'fee_amount_type' => 'main'
				])[0];
			}else{
				$mbo_fee_data = $this->Im->get_fee([
					'fee_id' => $s_fee_id
				])[0];
			}
	
			if ($mbo_fee_data) {
				$d_fee_amount = $mbo_fee_data->fee_amount;
				$this->db->trans_start();

				$s_invoice_desc = $mbo_fee_data->fee_description;
				if ($s_academic_year_id AND $s_semester_type_id) {
					$s_invoice_desc .= ' - '.$s_academic_year_id.'/'.$s_semester_type_id;
				}
	
				$a_invoice_data = [
					'invoice_description' => $s_invoice_desc,
					'personal_data_id' => $mbo_student_data->personal_data_id,
					'invoice_number' => $this->Im->get_invoice_number('02'),
					'invoice_allow_fine' => 'yes'
				];

				if ($s_academic_year_id AND $s_semester_type_id) {
					$a_invoice_data['academic_year_id'] = $s_academic_year_id;
					$a_invoice_data['semester_type_id'] = $s_semester_type_id;
				}
	
				$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
	
				$a_invoice_details_data = [
					'fee_id' => $mbo_fee_data->fee_id,
					'invoice_id' => $s_invoice_id,
					'invoice_details_amount' => $mbo_fee_data->fee_amount,
					'invoice_details_amount_number_type' => $mbo_fee_data->fee_amount_number_type,
					'invoice_details_amount_sign_type' => $mbo_fee_data->fee_amount_sign_type
				];
	
				$this->Im->create_invoice_details($a_invoice_details_data);
	
				if ($mba_student_has_scholarship_additional) {
					foreach ($mba_student_has_scholarship_additional as $o_scholarship_additional) {
						$a_fee_filter = [
							'program_id' => $mbo_student_data->program_id,
							'scholarship_id' => $o_scholarship_additional->scholarship_id,
							'academic_year_id' => $mbo_student_data->finance_year_id,
							'fee_amount_type' => 'additional'
						];
	
						if ($o_scholarship_additional->specific_user == 'yes') {
							$a_fee_filter['payment_type_code'] = '02';
							$a_fee_filter['study_program_id'] = $mbo_student_data->study_program_id;
							$a_fee_filter['semester_id'] = $s_semester_id;
						}
	
						if (!is_null($o_scholarship_additional->scholarship_fee_id)) {
							$a_fee_filter = ['fee_id' => $o_scholarship_additional->scholarship_fee_id];
						}

						$mbo_fee_data_additional = $this->Im->get_fee($a_fee_filter)[0];

						if ($mbo_fee_data_additional) {
							if ($mbo_fee_data_additional->fee_amount_sign_type == 'negative') {
								if ($mbo_fee_data_additional->fee_amount_number_type == 'number') {
									$d_fee_amount -= $mbo_fee_data_additional->fee_amount;
								}else{
									$d_fee_amount_additional = ($d_fee_amount * $mbo_fee_data_additional->fee_amount) / 100;
									$d_fee_amount -= $d_fee_amount_additional;
								}
							}else{
								if ($mbo_fee_data_additional->fee_amount_number_type == 'number') {
									$d_fee_amount += $mbo_fee_data_additional->fee_amount;
								}else{
									$d_fee_amount_additional = ($d_fee_amount * $mbo_fee_data_additional->fee_amount) / 100;
									$d_fee_amount += $d_fee_amount_additional;
								}
							}
	
							$a_invoice_details_data = [
								'fee_id' => $mbo_fee_data_additional->fee_id,
								'invoice_id' => $s_invoice_id,
								'invoice_details_amount' => $mbo_fee_data_additional->fee_amount,
								'invoice_details_amount_number_type' => $mbo_fee_data_additional->fee_amount_number_type,
								'invoice_details_amount_sign_type' => $mbo_fee_data_additional->fee_amount_sign_type
							];
				
							$this->Im->create_invoice_details($a_invoice_details_data);
						}else{
							$this->db->trans_rollback();
							return ['code' => 1, 'message' => 'Fee '.$o_scholarship_additional->scholarship_name.' batch '.$mbo_student_data->finance_year_id.' not yet setup', 'filter' => $a_fee_filter];
						}
					}
				}

				$d_sub_invoice_amount = $d_fee_amount;
				
				$a_sub_invoice_data = [
					'sub_invoice_amount' => $d_sub_invoice_amount,
					'sub_invoice_amount_total' => $d_sub_invoice_amount,
					'invoice_id' => $s_invoice_id
				];
				
				$s_sub_invoice_id_full = $this->Im->create_sub_invoice($a_sub_invoice_data);
	
				if($i_installments >= 2){
					$d_sub_inv_amount_installment = (is_int($d_fee_amount / $i_installments)) ? round(($d_fee_amount / $i_installments), 0, PHP_ROUND_HALF_UP) : intval($d_fee_amount / $i_installments);
					if ($mbo_student_data->finance_year_id >= 2021) {
						$d_additional_installment = $d_sub_inv_amount_installment/100*10;
						$d_sub_inv_amount_installment = $d_sub_inv_amount_installment + $d_additional_installment;
					}
					$d_sub_inv_amount_installment = $this->_get_round_thousand($d_sub_inv_amount_installment);
					$d_sub_inv_amount_total = $d_sub_inv_amount_installment * $i_installments;

					// if ($mbo_student_data->finance_year_id >= 2021) {
					// 	$d_sub_invoice_amount = $d_fee_amount + 3000000;
					// }

					$a_sub_invoice_data['sub_invoice_amount'] = $d_sub_inv_amount_total;
					$a_sub_invoice_data['sub_invoice_amount_total'] = $d_sub_inv_amount_total;
					$a_sub_invoice_data['sub_invoice_type'] = 'installment';
					$s_sub_invoice_id_installment = $this->Im->create_sub_invoice($a_sub_invoice_data);
				}
				
				$s_va_number = $this->Bnim->generate_va_number(
					'02',
					'student', 
					$mbo_student_data->student_number, 
					$mbo_student_data->finance_year_id,
					$mbo_student_data->program_id
				);

				$d_amount_filled = 0;
				for($i = 0; $i <= $i_installments; $i++){
					if($i == 0){
						$s_description = "Full Payment {$mbo_fee_data->fee_description} Semester {$s_semester_id} - {$mbo_student_data->finance_year_id}";
	
						$s_payment_deadline_date = date('Y-m-10 23:59:59', strtotime($s_deadline_date));
						// $s_payment_deadline_date = $s_deadline_date;
						$d_amount = $d_fee_amount;
					}
					else{
						$mba_student_data = $this->General->get_where('dt_student st', ['personal_data_id']);
						// $s_test_installment = date('Y-m-15 23:59:59', strtotime($s_deadline_date));
						if ($i == 1) {
							$s_deadline_date = date('Y-m-10 23:59:59', strtotime($s_deadline_date));
						}
						else {
							$s_deadline_date = date('Y-m-10 23:59:59', strtotime($s_deadline_date." +1 month"));
						}

						$s_description = "Installment $i {$mbo_fee_data->fee_description} Semester {$s_semester_id} - {$mbo_student_data->finance_year_id}";
						$s_payment_deadline_date = $s_deadline_date;
						// $d_amount = round(($d_fee_amount / $i_installments), 0, PHP_ROUND_HALF_UP);
						$d_amount = (is_int($d_fee_amount / $i_installments)) ? round(($d_fee_amount / $i_installments), 0, PHP_ROUND_HALF_UP) : intval(($d_fee_amount / $i_installments) + 1);
						if ($mbo_student_data->finance_year_id >= 2021) {
							$d_additional_installment = $d_amount/100*10;
							$d_amount = $d_amount + $d_additional_installment;
						}

						$d_amount = $this->_get_round_thousand($d_amount);
					}
					
					$a_sub_invoice_details_data = [
						'sub_invoice_id' => ($i == 0) ? $s_sub_invoice_id_full : $s_sub_invoice_id_installment,
						'sub_invoice_details_amount' => $d_amount,
						'sub_invoice_details_amount_total' => $d_amount,
						'sub_invoice_details_va_number' => $s_va_number,
						'sub_invoice_details_deadline' => $s_payment_deadline_date,
						'sub_invoice_details_real_datetime_deadline' => $s_payment_deadline_date,
						'sub_invoice_details_description' => $s_description
					];
					$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details_data);
	
					// $this->activate_virtual_account($s_sub_invoice_details_id, false);
				}
	
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$a_return = ['code' => 1, 'message' => 'fail create invoice'];
				}else{
					$this->db->trans_commit();
					$a_return = ['code' => 0, 'message' => 'Succes', 'fullpayment_amount' => $d_fee_amount];
				}
			}else{
				$a_return = ['code' => 1, 'message' => 'scholarship fee not found'];
			}
		}else{
			$a_return = ['code' => 1, 'message' => 'Invoice student for fee has been created before!'];
		}

		return $a_return;
	}

	// function test_function() {
	// 	$mba_invoice_list = $this->Im->get_invoice_data([
	// 		'dsid.trx_id' => $a_parsed_data['trx_id'],
	// 		// 'dsid.sub_invoice_details_va_number' => $a_parsed_data['virtual_account'],
	// 		'dsid.sub_invoice_details_status != ' => 'paid'
	// 	]);
	// 	print('<pre>');var_dump($mba_invoice_list);
	// }
	
	private function _create_bulk_invoice(
		$s_finance_year_id, 
		$s_semester_id, 
		$s_fee_id, 
		$s_payment_type_code, 
		$s_deadline_date,
		$i_installments,
		$s_academic_year_academic = false,
		$s_semester_type_academic = false
	)
	{
		$mbo_fee_data = $this->Im->get_fee([
			'fee_id' => $s_fee_id
		]);
		
		$s_deadline_date = date('Y-m-d 23:59:59', strtotime($s_deadline_date));
		$mba_student_data = $this->Stm->get_student_filtered([
			'finance_year_id' => $s_finance_year_id,
			'ds.student_status' => 'active',
			'ds.study_program_id' => $mbo_fee_data[0]->study_program_id
		]);

		$mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $s_semester_id])[0];
		$a_error = [];
		$i_created = 0;
		$a_created = [];

		if (($mbo_semester_data) AND ($mba_student_data)) {
			foreach($mba_student_data as $o_student){
				$mba_student_has_scholarship = $this->Finm->get_personal_data_scholarship([
					'pds.personal_data_id' => $o_student->personal_data_id,
					'pds.scholarship_status' => 'active'
				]);
	
				if (($mba_student_has_scholarship) AND ($s_payment_type_code == '02') AND ($mbo_semester_data->semester_number <= 8)) {
					$create_invoice = $this->_create_scholarship_invoice_tuition_fee(
						$o_student->personal_data_id,
						$s_semester_id,
						$s_fee_id,
						$s_deadline_date,
						$i_installments,
						$s_academic_year_academic,
						$s_semester_type_academic
					);
					
					if ($create_invoice['code'] != 0) {
						array_push($a_error, $create_invoice['message']);
					}
					else {
						$i_created++;
						array_push($a_created, $o_student->personal_data_name.':Rp.'.number_format($create_invoice['fullpayment_amount'], 0, ',', '.'));
					}
				}
				else{
					if ($mbo_semester_data->semester_number <= 8) {
						$create_invoice = $this->_create_new_single_invoice(
							$o_student->personal_data_id,
							$s_semester_id, 
							0, 
							$s_fee_id, 
							$s_payment_type_code, 
							$s_deadline_date,
							$i_installments,
							$s_academic_year_academic,
							$s_semester_type_academic
						);

						if ($create_invoice['code'] != 0) {
							array_push($a_error, $create_invoice['message']);
						}
						else {
							$i_created++;
							array_push($a_created, $o_student->personal_data_name.':Rp.'.number_format($create_invoice['fullpayment_amount'], 0, ',', '.'));
						}
					}
					else{
						$mba_score_student = $this->Scm->get_score_data_transcript([
							'sc.student_id' => $o_student->student_id,
							'sc.semester_id' => $s_semester_id,
							'sc.score_approval' => 'approved'
						]);

						if ($mba_score_student) {
							$i_total_sks = 0;
							foreach ($mba_score_student as $o_score) {
								$i_total_sks += $o_score->curriculum_subject_credit;
							}

							if ($i_total_sks > 0) {
								$create_invoice = $this->_create_new_single_invoice(
									$o_student->personal_data_id,
									$s_semester_id, 
									$i_total_sks, 
									$s_fee_id, 
									$s_payment_type_code, 
									$s_deadline_date,
									$i_installments,
									$s_academic_year_academic,
									$s_semester_type_academic
								);
		
								if ($create_invoice['code'] != 0) {
									array_push($a_error, $create_invoice['message']);
								}else{
									array_push($a_created, $o_student->personal_data_name.':Rp.'.number_format($create_invoice['fullpayment_amount'], 0, ',', '.'));
									$i_created++;
								}
							}
						}
					}
					
				}
			}

			// print('<pre>');var_dump($a_created);

			$s_success_message = 'Created '.$i_created.' invoice: '.implode(' / ', $a_created);
			if (count($a_error) > 0) {
				$s_message = ($i_created > 0) ? $s_success_message.'<br><li>'.implode('</li><li>', $a_error).'</li>' : '<li>'.implode('</li><li>', $a_error).'</li>';
				$a_return = ['code' => 1, 'message' => $s_message];
			}else{
				$a_return = ['code' => 0, 'message' => $s_success_message];
			}
		}else{
			$a_return = ['code' => 1, 'message' => 'failed retrieve data!'];
		}

		return $a_return;
	}

	public function create_additional_graduation_coupon()
	{
		# code...
	}

	public function create_short_semester_invoice()
    {
		print('warung tutup!');exit;
        $s_academic_year_id = 2023;
        $s_semester_type_id = 7;
		$mbo_active_semester = $this->Sem->get_active_semester();
		$a_curriculum_subject_skipped = [
			'ca301f6b-cf74-4496-98f6-a8bf186d4142', //Process Control
			'bb50e2d2-9c50-42f4-ae24-fafa164fdc79', //Applied Mathematics
			'09f6777f-114b-45c5-b42c-cd6630deb1e4', //Applied Mathematics
			'9cba2b43-a4ed-4148-923b-5a5a8ebe54f7', //Applied Mathematics
			'9cedfb7d-9698-4d27-adda-373e7664b05b', //Applied Mathematics
			'b7a3cb88-72f3-401a-a8f4-ce718b49a802', //Applied Mathematics
			'bb50e2d2-9c50-42f4-ae24-fafa164fdc79', //Applied Mathematics
			'cd261844-b3c1-488b-82be-6696a2eeca98', //Applied Mathematics
			'f5ae6ea2-b3c3-48eb-9bdd-7a2e7a691164', //Applied Mathematics
		];
		// untuk short semester 2020 - 8

        $mba_score_data = $this->Scm->get_student_by_score([
            'sc.academic_year_id' => $s_academic_year_id,
            'sc.semester_type_id' => $s_semester_type_id,
            'sc.score_approval' => 'approved'
        ]);

		if (!$mbo_active_semester) {
			print('semester active not found!');exit;
		}
        else if ($mba_score_data) {
            $i_count = 0;
            foreach ($mba_score_data as $o_student) {
                $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $o_student->semester_id])[0];

                $mbo_fee_data = $this->Im->get_fee([
                    'semester_id' => $o_student->semester_id,
                    'payment_type_code' => '04',
                    'program_id' => $o_student->program_id,
                    'study_program_id' => $o_student->study_program_id,
                    'academic_year_id' => $o_student->finance_year_id,
                    'fee_amount_type' => 'main'
                ])[0];

                if ($mbo_fee_data) {
                    $mba_score_student = $this->Scm->get_score_data_transcript([
                        'sc.student_id' => $o_student->student_id,
                        'sc.academic_year_id' => $s_academic_year_id,
                        'sc.semester_type_id' => $s_semester_type_id,
                        'sc.score_approval' => 'approved'
                    ]);

                    if (!$mba_score_student) {
                        print('Score '.$o_student->personal_data_name.'-'.$mbo_semester_data->semester_number.' ga ada!<br>');
                    }else{
                        $a_sks = [];
                        foreach ($mba_score_student as $o_score) {
							// if ($o_score->student_id == '5bfb3427-f247-4a1f-9c37-070d4f171893') {
							// 	array_push($a_sks, $o_score->curriculum_subject_credit);
							// }
							// else 
							if (!in_array($o_score->curriculum_subject_id, $a_curriculum_subject_skipped)) {
								array_push($a_sks, $o_score->curriculum_subject_credit);
							}
                        }
                        $i_sum_sks = array_sum($a_sks);

						if ($i_sum_sks > 0) {
							$a_create_invoice = $this->_create_new_single_invoice(
								$o_student->personal_data_id,
								$o_student->semester_id, 
								$i_sum_sks, 
								$mbo_fee_data->fee_id, 
								'04', 
								'2024-01-31 23:59:59',
								0,
								$mbo_active_semester->academic_year_id,
								$mbo_active_semester->semester_type_id
							);
							print($o_student->personal_data_name.' --- ');
							print json_encode($a_create_invoice);
							print('<br>');
						}
						else {
							print($o_student->personal_data_name.' --- 0 SKS');
							print('<br>');
						}
                    }

                    print($o_student->personal_data_name.'-'.$mbo_semester_data->semester_number);
                    print('<br>');
                    $i_count++;
                }else{
                    print('Fee '.$o_student->personal_data_name.'-'.$mbo_semester_data->semester_number.' ga ada!<br>');
                }
            }

            print('<h1>'.$i_count.'</h1>');
        }
    }

	public function update_sub_invoice_details_tester()
	{
		if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
			$this->form_validation->set_rules('billed_amount', 'Billing amount', 'trim|required');
			$this->form_validation->set_rules('description', 'Description', 'trim|required');
			$this->form_validation->set_rules('fined_amount', 'Fine', 'trim|required');
			$this->form_validation->set_rules('total_amount', 'Total Amount', 'trim|required');
			$this->form_validation->set_rules('payment_deadline', 'Payment deadline', 'trim|required');
			$this->form_validation->set_rules('remarks', 'Remarks', 'trim|required');
			
			$s_sub_invoice_details_id = $this->input->post('sub_invoice_details_id');
			$s_trx_id = $this->input->post('trx_id');
			$a_bni_status = $a_bni_result = false;

			if ($this->form_validation->run()) {
				$a_sub_invoice_data = $this->Im->get_invoice_data([
					'dsid.sub_invoice_id' => $this->input->post('sub_invoice_id')
				]);

				// print('<pre>');
				// var_dump($a_sub_invoice_data);exit;

				$a_sub_invoice_details_update = [
					'sub_invoice_details_amount' => set_value('billed_amount'),
					'sub_invoice_details_amount_fined' => set_value('fined_amount'),
					'sub_invoice_details_amount_total' => set_value('total_amount'),
					'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime(set_value('payment_deadline'))),
					'sub_invoice_details_description' => $this->input->post('description'),
					'sub_invoice_details_remarks' => $this->input->post('remarks')
				];

				if ($s_sub_invoice_details_id) {
					if ($a_sub_invoice_data) {
						if ((count($a_sub_invoice_data) == 1) AND ($a_sub_invoice_data[0]->sub_invoice_type == 'full')) {
							$this->Im->update_sub_invoice_details($a_sub_invoice_details_update, ['sub_invoice_details_id' => $s_sub_invoice_details_id]);
							$mba_installment_invoice = $this->Im->get_invoice_installment($a_sub_invoice_data[0]->invoice_id);
							
							$d_amount_total = 0;
							$d_fined_total = 0;
							$d_amount_paid = 0;
	
							if ($mba_installment_invoice) {
								$a_amount_total = [];
								$a_fined_total = [];
								$a_amount_paid = [];
	
								foreach ($mba_installment_invoice as $o_installment) {
									array_push($a_amount_total, $o_installment->sub_invoice_details_amount_total);
									array_push($a_fined_total, $o_installment->sub_invoice_details_amount_fined);
									array_push($a_amount_paid, $o_installment->sub_invoice_details_amount_paid);
								}
	
								$d_amount_total = array_sum($a_amount_total);
								$d_fined_total = array_sum($a_fined_total);
								$d_amount_paid = array_sum($a_amount_paid);
	
							}
							else {
								$d_amount_total = set_value('billed_amount');
								$d_fined_total = set_value('fined_amount');
							}
	
							$a_sub_invoice_data_update = [
								'sub_invoice_amount' => ($d_amount_total + $d_fined_total - $d_amount_paid),
								'sub_invoice_amount_total' => $d_amount_total
							];
	
						}else if (count($a_sub_invoice_data) > 0) {
							$this->Im->update_sub_invoice_details($a_sub_invoice_details_update, ['sub_invoice_details_id' => $s_sub_invoice_details_id]);
	
							$a_amount = [];
							$a_amount_total = [];
							$a_fined_total = [];
							$a_amount_paid = [];
							
							foreach($a_sub_invoice_data as $o_sub_invoice_data){
								array_push($a_amount, $o_sub_invoice_data->sub_invoice_details_amount);
								array_push($a_amount_total, $o_sub_invoice_data->sub_invoice_details_amount_total);
								array_push($a_fined_total, $o_sub_invoice_data->sub_invoice_details_amount_fined);
								array_push($a_amount_paid, $o_sub_invoice_data->sub_invoice_details_amount_paid);
							}
	
							$d_amount = array_sum($a_amount);
							$d_amount_total = array_sum($a_amount_total);
							$d_fined_total = array_sum($a_fined_total);
							$d_amount_paid = array_sum($a_amount_paid);
	
							$a_sub_invoice_data_update = [
								'sub_invoice_amount' => $d_amount,
								'sub_invoice_amount_total' => ($d_amount - $d_amount_paid)
							];
	
						}

						$this->Im->update_sub_invoice($a_sub_invoice_data_update, [
							'sub_invoice_id' => $a_sub_invoice_data[0]->sub_invoice_id
						]);

						if(!empty($s_trx_id)){
							$a_bni_result = $this->change_trx_details($s_trx_id);
						}

						$rtn = array('code' => 0, 'message' => 'Success!', 'data' => $a_sub_invoice_details_update, 'bni' => [$a_bni_result, $a_bni_status]);
					}else{
						$a_rtn = array('code' => 1, 'message' => 'Error retrieving data!');
					}

				}else{
					$invoice_data = $this->Im->get_invoice_data([
						'di.invoice_id' => set_value('invoice_id')
					])[0];

					$s_personal_data_id = $invoice_data->personal_data_id;
					$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
					$mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id, [
						'student_status !=' => 'resign'
					]);
					
					$mba_invoice_details = $this->Im->get_invoice_details([
						'invoice_id' => set_value('invoice_id'),
						'fee_amount_type' => 'main',
						'fee_amount_number_type' => 'number',
						'fee_amount_sign_type' => 'positive',
						'semester_id != ' => NULL
					]);
					
					if($mba_invoice_details && count($mba_invoice_details) > 1){
						print json_encode(['code' => 2, 'message' => 'System error']);
						exit;
					}
					else{
						$o_invoice_details = $mba_invoice_details[0];
					}
					
					$s_va_number = $this->Bnim->get_va_number(
						$o_invoice_details->payment_type_code, 
						$o_invoice_details->semester_id, 
						set_value('num_installment'), 
						$mbo_student_data->student_status, 
						null, 
						$mbo_student_data->finance_year_id,
						$mbo_student_data->program_id
					);
					
					$a_payment_data = array(
						'trx_amount' => set_value('total_amount'),
						'billing_type' => 'c',
						'customer_name' => $mbo_personal_data->personal_data_name,
						'virtual_account' => $s_va_number,
						'description' => 'Full Payment '.$o_invoice_details->fee_description." - Batch ".$mbo_student_data->finance_year_id,
						'datetime_expired' => date('Y-m-d 23:59:59', strtotime(set_value('payment_deadline')." +3 day")),
						'customer_email' => 'bni.employee@company.ac.id'
					);
					
					$a_bni_result_payment = $this->Bnim->create_billing($a_payment_data);
					if($a_bni_result_payment['status'] === '000'){
						$s_trx_id = $a_bni_result_payment['trx_id'];
						$a_sub_invoice_details_update = array_merge($a_sub_invoice_details_update, [
							'trx_id' => $s_trx_id,
							'sub_invoice_id' => set_value('sub_invoice_id'),
							'sub_invoice_details_va_number' => $s_va_number,
						]);
						$this->Im->create_sub_invoice_details($a_sub_invoice_details_update);
					}
					else{
						if(is_array($a_bni_status)){
							array_push($a_bni_status, $a_bni_result_payment);
						}
						else{
							$a_bni_status[] = $a_bni_result_payment;
						}
					}

					$rtn = array('code' => 0, 'message' => 'Success!', 'data' => $a_sub_invoice_details_update, 'bni' => [$a_bni_result, $a_bni_status]);

				}
			}
			else{
				$a_rtn = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
			}
			
			print json_encode($rtn);
			exit;
		}
	}
	
	public function update_sub_invoice_details()
	{
		if($this->input->is_ajax_request()){
			$this->form_validation->set_rules('billed_amount', 'Billing amount', 'trim|required');
			$this->form_validation->set_rules('description', 'Description', 'trim|required');
			$this->form_validation->set_rules('fined_amount', 'Fine', 'trim|required');
			$this->form_validation->set_rules('total_amount', 'Total Amount', 'trim|required');
			$this->form_validation->set_rules('payment_deadline', 'Payment deadline', 'trim|required');
			// $this->form_validation->set_rules('remarks', 'Remarks', 'trim|required');
			$this->form_validation->set_rules('remarks', 'Remarks', 'trim');
			
			$s_sub_invoice_details_id = $this->input->post('sub_invoice_details_id');
			$s_trx_id = $this->input->post('trx_id');
			$a_bni_status = $a_bni_result = false;
			
			if($this->form_validation->run()){
				$a_sub_invoice_details_update = [
					'sub_invoice_details_amount' => set_value('billed_amount'),
					'sub_invoice_details_amount_fined' => set_value('fined_amount'),
					'sub_invoice_details_amount_total' => set_value('total_amount'),
					'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime(set_value('payment_deadline'))),
					'sub_invoice_details_real_datetime_deadline' => date('Y-m-d 23:59:59', strtotime(set_value('payment_deadline'))),
					'sub_invoice_details_description' => $this->input->post('description'),
					'sub_invoice_details_remarks' => $this->input->post('remarks')
				];
				
				if ($s_sub_invoice_details_id) {
					$this->Im->update_sub_invoice_details($a_sub_invoice_details_update, ['sub_invoice_details_id' => $s_sub_invoice_details_id]);
					$a_sub_invoice_data = $this->Im->get_invoice_data([
						'dsid.sub_invoice_id' => $this->input->post('sub_invoice_id')
					]);
					$s_sub_invoice_type = $a_sub_invoice_data[0]->sub_invoice_type;
					
					$a_invoice_data = $this->Im->get_invoice_data([
						'di.invoice_id' => $this->input->post('invoice_id')
					]);
					
					$a_amount = [];
					$a_amount_total = [];
					$a_fined_total = [];
					$a_amount_paid = [];

					foreach($a_sub_invoice_data as $o_sub_invoice_data){
						array_push($a_amount, $o_sub_invoice_data->sub_invoice_details_amount);
						array_push($a_amount_total, $o_sub_invoice_data->sub_invoice_details_amount_total);
						array_push($a_fined_total, $o_sub_invoice_data->sub_invoice_details_amount_fined);
						if ($o_sub_invoice_data->sub_invoice_details_amount_paid > 0) {
							array_push($a_amount_paid, $o_sub_invoice_data->sub_invoice_details_amount);
						}
					}
					$d_amount = array_sum($a_amount);
					$d_amount_total = array_sum($a_amount_total);
					$d_fined_total = array_sum($a_fined_total);
					$d_amount_paid = array_sum($a_amount_paid);

					$a_sub_invoice_data_update = [
						'sub_invoice_amount' => $d_amount,
						'sub_invoice_amount_total' => (($d_amount + $d_fined_total) - $d_amount_paid)
					];

					$this->Im->update_sub_invoice($a_sub_invoice_data_update, [
						'sub_invoice_id' => $this->input->post('sub_invoice_id')
					]);

					$this->Im->update_invoice(['invoice_amount_fined' => $d_fined_total], ['invoice_id' => $this->input->post('invoice_id')]);

					if ($d_fined_total > 0) {
						// $invoice_full = $this->Im->get_full_payment_invoice_by_invoice_id($this->input->post('invoice_id'));

						// sebentar aja untuk cheat / mempermudah
						// if ($invoice_full) {
						// 	$this->Im->update_sub_invoice_details([
						// 		'sub_invoice_details_amount_fined' => $d_fined_total,
						// 		'sub_invoice_details_amount_total' => $invoice_full->sub_invoice_details_amount + $d_fined_total
						// 	], ['sub_invoice_details_id' => $invoice_full->sub_invoice_details_id]);
							
						// 	$this->Im->update_sub_invoice([
						// 		'sub_invoice_amount_total' => ($invoice_full->sub_invoice_details_amount + $d_fined_total)
						// 	], [
						// 		'sub_invoice_id' => $invoice_full->sub_invoice_id
						// 	]);

						// 	if(!empty($invoice_full->trx_id)){
						// 		$a_bni_result = $this->change_trx_details($invoice_full->trx_id);
						// 	}
						// }
						// 
					}
					
					if(!empty($s_trx_id)){
						$a_bni_result = $this->change_trx_details($s_trx_id);
					}

					$rtn = array('code' => 0, 'message' => 'Success!', 'data' => $a_sub_invoice_details_update, 'bni' => [$a_bni_result]);
				}
				else{
					$invoice_data = $this->Im->get_invoice_data([
						'di.invoice_id' => set_value('invoice_id')
					]);

					if (!$invoice_data) {
						$rtn = array('code' => 1, 'message' => 'Error retrieve invoice data!');
					}
					else {
						$invoice_data = $invoice_data[0];
						$s_personal_data_id = $invoice_data->personal_data_id;
						$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
						$mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id, [
							'student_status !=' => 'resign'
						]);

						if ($mbo_student_data) {
							$mba_sub_invoice_data = $this->General->get_where('dt_sub_invoice', ['sub_invoice_id' => $this->input->post('sub_invoice_id')]);
							$mba_invoice_details = $this->Im->get_invoice_details([
								'invoice_id' => set_value('invoice_id'),
								'fee_amount_type' => 'main',
								'fee_amount_number_type' => 'number',
								'fee_amount_sign_type' => 'positive',
								'semester_id != ' => NULL
							]);
							$mba_sub_invoice_details = $this->Im->get_invoice_installment(set_value('invoice_id'));
							
							if ($mba_sub_invoice_data[0]->sub_invoice_type == 'full') {
								print json_encode(['code' => 5, 'message' => 'Payment type selected is full payment, can not adding installment!']);;
								exit;
							}
							else if($mba_invoice_details && count($mba_invoice_details) > 1){
								print json_encode(['code' => 2, 'message' => 'System error']);
								exit;
							}
							else if (!$mba_invoice_details) {
								print json_encode(['code' => 3, 'message' => 'Error retrieve invoice data!']);
								exit;
							}
							else if (!$mba_sub_invoice_details) {
								print json_encode(['code' => 3, 'message' => 'Error retrieve current installment!']);
								exit;
							}
							else{
								$o_invoice_details = $mba_invoice_details[0];
							}
							
							$s_va_number_old = $mba_sub_invoice_details[0]->sub_invoice_details_va_number;
							// $s_va_number = $this->Bnim->get_va_number(
							// 	$o_invoice_details->payment_type_code, 
							// 	$o_invoice_details->semester_id, 
							// 	set_value('num_installment'), 
							// 	$mbo_student_data->student_status, 
							// 	null, 
							// 	$mbo_student_data->finance_year_id,
							// 	$mbo_student_data->program_id
							// );
							$s_va_prefix = substr($s_va_number_old, 0, 8);
							$s_va_suffix = substr($s_va_number_old, 9);
							$s_va_number = $s_va_prefix.set_value('num_installment').$s_va_suffix;

							$s_sub_invoice_details_id = $this->uuid->v4();
							$date_deadline = date('Y-m-d 23:59:59', strtotime(set_value('payment_deadline')));
							$a_sub_invoice_details_data = [
								'sub_invoice_details_id' => $s_sub_invoice_details_id,
								'sub_invoice_id' => $this->input->post('sub_invoice_id'),
								'sub_invoice_details_amount' => set_value('billed_amount'),
								'sub_invoice_details_amount_fined' => set_value('fined_amount'),
								'sub_invoice_details_amount_total' => set_value('total_amount'),
								'sub_invoice_details_va_number' => $s_va_number,
								'sub_invoice_details_deadline' => $date_deadline,
								'sub_invoice_details_real_datetime_deadline' => $date_deadline,
								'sub_invoice_details_description' => set_value('description'),
								'sub_invoice_details_remarks' => set_value('remarks'),
								'sub_invoice_details_status' => 'default'
							];
							$this->Im->create_sub_invoice_details($a_sub_invoice_details_data);

							$d_amount = [];
							$d_amount_fined = [];
							$d_amount_paid = [];
							$mba_installment_invoice = $this->Im->get_invoice_installment(set_value('invoice_id'));
							foreach ($mba_installment_invoice as $o_installment) {
								array_push($d_amount, $o_installment->sub_invoice_details_amount);
								if ($o_installment->sub_invoice_details_status != 'paid') {
									array_push($d_amount_fined, $o_installment->sub_invoice_details_amount_fined);
								}
								array_push($d_amount_paid, $o_installment->sub_invoice_details_amount_paid);
							}

							$d_total_amount = array_sum($d_amount);
							$d_total_amount_fined = array_sum($d_amount_fined);
							$d_total_amount_paid = array_sum($d_amount_paid);
							$a_sub_invoice_data_update = [
								'sub_invoice_amount' => $d_total_amount,
								'sub_invoice_amount_total' => ($d_total_amount - $d_total_amount_paid)
							];

							$this->Im->update_sub_invoice($a_sub_invoice_data_update, [
								'sub_invoice_id' => $this->input->post('sub_invoice_id')
							]);
							
							$i_now = time();
							$i_deadline = strtotime($date_deadline);
							$i_datediff = $i_now - $i_deadline;
							$i_float = round($i_datediff / (60 * 60 * 24));

							$a_bni_result_payment = false;
							if ($i_float >= -14) {
								$a_payment_data = array(
									'trx_amount' => set_value('total_amount'),
									'billing_type' => 'c',
									'customer_name' => $mbo_personal_data->personal_data_name,
									'virtual_account' => $s_va_number,
									'description' => set_value('description'),
									'datetime_expired' => date('Y-m-d 23:59:59', strtotime($date_deadline." +3 day")),
									'customer_email' => 'bni.employee@company.ac.id'
								);
								
								$a_bni_result_payment = $this->Bnim->create_billing($a_payment_data);
								if($a_bni_result_payment['status'] === '000'){
									$s_trx_id = $a_bni_result_payment['trx_id'];
									$a_sub_invoice_details_update = [
										'trx_id' => $s_trx_id
									];
									$this->Im->update_sub_invoice_details($a_sub_invoice_details_update, [
										'sub_invoice_details_id' => $s_sub_invoice_details_id
									]);
								}
								else{
									// set log dan email error
									$this->email->from('employee@company.ac.id');
									$this->email->to(array('employee@company.ac.id'));
									$this->email->subject('ERROR CREATE BILLING');
									$this->email->message(json_encode($a_bni_result_payment));
									$this->email->send();
								}
							}

							$rtn = array('code' => 0, 'message' => 'Success!', 'data' => $a_sub_invoice_details_update, 'bni' => [$a_bni_result_payment]);
						}
						else {
							$rtn = array('code' => 1, 'message' => 'Error retrieve student data!');
						}
					}
				}
			}
			else{
				$rtn = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
			}
			
			print json_encode($rtn);
			exit;
		}
	}

	public function test_get()
	{
		$mba_unpaid_invoice = $this->Im->get_unpaid_invoice(false, false);
		foreach ($mba_unpaid_invoice as $o_invoice) {
			if (strpos($o_invoice->invoice_description, 'german')) {
				print($o_invoice->invoice_description);
				print('<br>');
			}
		}
		// print('<pre>');var_dump($mba_unpaid_invoice);exit;
	}

	public function test_create_va()
	{
		$s_va_number = '8310020131220128';
		$s_va_prefix = substr($s_va_number, 0, 8);
		$s_va_suffix = substr($s_va_number, 9);
		$s_installment = substr($s_va_number, 8, 1);
		$s_new_va = $s_va_prefix.'4'.$s_va_suffix;
		print('<pre>');var_dump($s_new_va);exit;
	}

	public function delete_sub_invoice_details()
	{
		if ($this->input->is_ajax_request()) {
			$s_sub_invoice_details_id = $this->input->post('sub_invoice_details_id');
			$s_invoice_id = $this->input->post('invoice_id');

			$mbo_invoice_data = $this->Im->get_invoice_data(['dsid.sub_invoice_details_id' => $s_sub_invoice_details_id]);
			if (!$mbo_invoice_data) {
				$a_return = ['code' => 1, 'message' => 'Error retrieve invoice data!'];
			}
			else if ($mbo_invoice_data[0]->sub_invoice_type == 'full') {
				$a_return = ['code' => 2, 'message' => 'Cannot delete payment type full!'];
			}
			else if (in_array($mbo_invoice_data[0]->invoice_status, ['paid', 'cancelled'])) {
				$a_return = ['code' => 3, 'message' => 'Invoice has been paid or cancelled!'];
			}
			else if ($mbo_invoice_data[0]->sub_invoice_details_status == 'paid') {
				$a_return = ['code' => 4, 'message' => 'Virtual Account has been paid!'];
			}
			else {
				if (!is_null($mbo_invoice_data[0]->trx_id)) {
					$this->cancel_payment($mbo_invoice_data[0]->trx_id);
				}

				$delete_data = $this->Im->remove_sub_invoice_details($s_sub_invoice_details_id);
				if (!$delete_data) {
					$a_return = ['code' => 5, 'message' => 'Error delete this virtual account!'];
				}
				else {
					$mba_installment_invoice = $this->Im->get_invoice_installment($s_invoice_id);
					if ($mba_installment_invoice) {
						$d_amount = [];
						$d_amount_fined = [];
						$d_amount_paid = [];
						foreach ($mba_installment_invoice as $o_installment) {
							array_push($d_amount, $o_installment->sub_invoice_details_amount);
							if ($o_installment->sub_invoice_details_status != 'paid') {
								array_push($d_amount_fined, $o_installment->sub_invoice_details_amount_fined);
							}
							array_push($d_amount_paid, $o_installment->sub_invoice_details_amount_paid);
						}
	
						$d_total_amount = array_sum($d_amount);
						$d_total_amount_paid = array_sum($d_amount_paid);
						$a_sub_invoice_data_update = [
							'sub_invoice_amount' => $d_total_amount,
							'sub_invoice_amount_total' => ($d_total_amount - $d_total_amount_paid)
						];

						if (count($d_amount_fined) > 0) {
							$a_data_update = [
								'invoice_amount_fined' => array_sum($d_amount_fined)
							];
							$this->Im->update_invoice($a_data_update, ['invoice_id' => $s_invoice_id]);
						}
	
						$this->Im->update_sub_invoice($a_sub_invoice_data_update, [
							'sub_invoice_id' => $mba_installment_invoice[0]->sub_invoice_id
						]);
						$a_return = ['code' => 0, 'message' => 'Success'];
					}
					else {
						$a_return = ['code' => 999, 'message' => 'Installment payment method not found!'];
					}
				}
			}

			print json_encode($a_return);
		}
	}

	public function bill_invoice()
	{
		// if ($this->input->is_ajax_request()) {
		// 	$s_invoice_id = $this->input->post('invoice_id');
		// 	// $s_invoice_id = '7a9a6980-4edd-4d4e-a5df-f306a54f1216';

		// 	$mba_invoice_unpaid = $this->Im->get_unpaid_invoice(array('di.invoice_id' => $s_invoice_id));
		// 	// print('<pre>');
		// 	// var_dump($mba_invoice_unpaid);exit;
		// 	if ($mba_invoice_unpaid) {
		// 		modules::run('callback/api/check_invoice', $mba_invoice_unpaid[0]);
		// 	}
		// }
	}

	public function checker_bni($trx_id = '1508149188')
	{
		$check = $this->Bnim->inquiry_billing($trx_id, true);
		print('<pre>');
		var_dump($check);
		
		// if (($check) AND (!isset($check->status))) {
		// 	// $o_sub_invoice->va_bni_status = $check->va_status;
		// 	print('<pre>');
		// 	var_dump($check['va_status']);
		// }
	}
	
	public function get_sub_invoice_details()
	{
		if($this->input->is_ajax_request()){
			$s_sub_invoice_id = $this->input->post('sub_invoice_id');
			$mba_sub_invoice_details_list = $this->Im->get_invoice_data(['dsid.sub_invoice_id' => $s_sub_invoice_id]);

			if ($mba_sub_invoice_details_list) {
				foreach ($mba_sub_invoice_details_list as $o_sub_invoice) {
					$o_sub_invoice->va_bni_status = 2;
					if (($o_sub_invoice->sub_invoice_details_status != 'paid') AND (!is_null($o_sub_invoice->trx_id))) {
						$check = $this->Bnim->inquiry_billing($o_sub_invoice->trx_id, true);
						
						// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
						// 	if ($o_sub_invoice->trx_id == '261390348') {
						// 		print('<pre>');var_dump($check);
						// 		print('<pre>');var_dump($o_sub_invoice->trx_id);
						// 		exit;
						// 	}
						// }
						if (($check) AND (!array_key_exists('status', $check))) {
							$o_sub_invoice->va_bni_status = $check['va_status'];
							if ($o_sub_invoice->sub_invoice_details_amount_total != $check['trx_amount']) {
								if ($check['billing_type'] != 'o') {
									$o_sub_invoice->va_bni_status = 99;
								}
							}
						}
					}
				}
				// exit;
			}

			print json_encode(array('data' => $mba_sub_invoice_details_list));
			exit;
		}
	}
	
	public function get_payment_method_list()
	{
		if($this->input->is_ajax_request()){
			$s_invoice_id = $this->input->post('invoice_id');
			$mba_sub_invoice_list = $this->Im->get_sub_invoice_data(['dsi.invoice_id' => $s_invoice_id]);

			if ($mba_sub_invoice_list) {
				foreach ($mba_sub_invoice_list as $key => $o_sub_invoice) {

					$mba_sub_invoice_details = false;
					
					if ($o_sub_invoice->sub_invoice_type == 'installment') {
						$mba_sub_invoice_details = $this->Im->get_invoice_data(array(
							'dsi.sub_invoice_id'=> $o_sub_invoice->sub_invoice_id
						));
					}

					$o_sub_invoice->sub_invoice_details_data = $mba_sub_invoice_details;
				}
			}
			print json_encode(array('data' => $mba_sub_invoice_list));
			exit;
		}
	}
	
	public function save_invoice_setting()
	{
		if ($this->input->is_ajax_request()) {
			$s_invoice_id = $this->input->post('invoice_id');
			$s_invoice_status = $this->input->post('invoice_status');
			$s_invoice_note = (empty($this->input->post('invoice_note'))) ? null : $this->input->post('invoice_note');
			$s_invoice_allow_fine = (empty($this->input->post('invoice_allow_fine'))) ? null : $this->input->post('invoice_allow_fine');
			$s_invoice_allow_reminder = (empty($this->input->post('invoice_allow_reminder'))) ? null : $this->input->post('invoice_allow_reminder');

			if (empty($s_invoice_id)) {
				$a_return = ['code' => 1, 'message' => 'Invalid parameter update!'];
			}else if ($s_invoice_status == '') {
				$a_return = ['code' => 1, 'message' => 'Please select status invoice!'];
			}else{
				$a_invoice_data = [
					'invoice_status' => $s_invoice_status,
					'invoice_note' => $s_invoice_note,
					'invoice_allow_fine' => ($s_invoice_allow_fine == null) ? 'no' : 'yes',
					'invoice_allow_reminder' => ($s_invoice_allow_reminder == null) ? 'no' : 'yes',
				];

				$this->Im->update_invoice($a_invoice_data, ['invoice_id' => $s_invoice_id]);
				$a_return = ['code' => 0, 'message' => 'Success processing data!'];
			}

			print json_encode($a_return);
		}
	}
	
	public function get_invoice_list()
	{
		if($this->input->is_ajax_request()){
			$s_payment_type_code = $this->input->post('payment_type');
			$s_personal_data_id = $this->input->post('personal_data_id');
			$s_student_invoice_type = $this->input->post('student_invoice_type');
			// $s_invoice_status = $this->input->post('invoice_status');
			// $s_invoice_allow_fine = $this->input->post('invoice_allow_fine');
			$s_invoice_academic_year_id = $this->input->post('invoice_academic_year_id');
			$s_invoice_semester_type_id = $this->input->post('invoice_semester_type_id');

			if ($s_personal_data_id !== null) {
				if ($s_payment_type_code == 'all') {
					$mba_invoice_list = $this->Im->get_invoice_list(false, $s_personal_data_id);
				}else if ($s_payment_type_code != ''){
					$mba_invoice_list = $this->Im->get_invoice_list($s_payment_type_code, $s_personal_data_id);
				}else{
					$mba_invoice_list = false;
				}
			}
			else if ((!empty($s_student_invoice_type)) AND ($s_student_invoice_type == 'partner')) {
				$mba_invoice_list = $this->Im->get_invoice_partner();
			}
			else{
				$a_clause = [
					// 'di.invoice_status' => $s_invoice_status,
					// 'di.invoice_allow_fine' => $s_invoice_allow_fine,
					'di.academic_year_id' => $s_invoice_academic_year_id,
					'di.semester_type_id' => $s_invoice_semester_type_id
				];

				foreach ($a_clause as $key => $value) {
					if ($value == 'all') {
						unset($a_clause[$key]);
					}
				}
				// var_dump($a_clause);exit;
				
				$mba_invoice_list = $this->Im->get_invoice_list($s_payment_type_code, false, $a_clause);
			}
			print json_encode(array('data' => $mba_invoice_list));
			exit;
		}
	}
	
	public function sub_invoice_details($s_sub_invoice_id)
	{
		$a_sub_invoice_details = $this->Im->get_sub_invoice_data([
			'dsi.sub_invoice_id' => $s_sub_invoice_id
		]);

		if ($a_sub_invoice_details) {
			$mbo_student_data = $this->Stm->get_student_list_data([
				'ds.personal_data_id' => $a_sub_invoice_details[0]->personal_data_id
			], false, [
				'ds.student_status' => 'ASC'
			]);

			if ($mbo_student_data) {
				$this->a_page_data['o_invoice_data'] = $a_sub_invoice_details[0];
				$this->a_page_data['o_student_data'] = $mbo_student_data[0];
			}
		}
		$this->a_page_data['invoice_data'] = $a_sub_invoice_details[0];
		$this->a_page_data['body'] = $this->load->view('sub_invoice_details', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function generate_va()
	{
		// $this->load->model('Bni_model', 'Bm');
		// $mbs_va_number = $this->Bm->get_va_number(
		// 	'02',
		// 	0,
		// 	0,
		// 	'candidate',
		// 	null,
		// 	2022
		// );
		// print('<pre>');var_dump($mbs_va_number);exit;
	}
	
	public function sub_invoice($s_invoice_id)
	{
		$mba_invoice_data = $this->Im->get_invoice_data([
			'di.invoice_id' => $s_invoice_id
		]);

		$b_has_payment = false;
		$b_partner = false;
		$s_option = '';
		if ($mba_invoice_data) {
			// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
			// 	print('<pre>');var_dump('add');exit;
			// }
			$mbo_student_data = $this->Stm->get_student_list_data([
				'ds.personal_data_id' => $mba_invoice_data[0]->personal_data_id
			], false, [
				'ds.student_status' => 'ASC'
			]);
			
			if (!$mbo_student_data) {
				$mbo_student_data = $this->Psm->get_partner_student_data([
					'sn.personal_data_id' => $mba_invoice_data[0]->personal_data_id
				]);
				$b_partner = ($mbo_student_data) ? true : false;
			}

			if ($mbo_student_data) {
				$this->a_page_data['o_student_data'] = $mbo_student_data[0];
			}

			if (!$b_partner) {
				$mba_fee_list_select = $this->Im->get_fee([
					'academic_year_id' => $mbo_student_data[0]->finance_year_id,
					'fee_amount_type' => 'additional'
				]);
	
				if ($mba_fee_list_select) {
					$s_option.= '<option value="">Plese select...</option>';
					foreach ($mba_fee_list_select as $o_fee) {
						if (!is_null($o_fee->study_program_id)) {
							$mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $o_fee->study_program_id]);
							$s_fee_desc = $o_fee->fee_description.' - '.$mba_study_program_data[0]->study_program_abbreviation.'';
						}
						else {
							$s_fee_desc = $o_fee->fee_description;
						}
	
						if ($o_fee->fee_amount_number_type == 'number') {
							$s_fee_desc .= '( '.(($o_fee->fee_amount_sign_type == 'negative') ? '- ' : '').'Rp. '.number_format($o_fee->fee_amount, 2, ',', '.').')';
						}
						else {
							$s_fee_desc .= '( '.(($o_fee->fee_amount_sign_type == 'negative') ? '- ' : '').number_format($o_fee->fee_amount, 2, ',', '.').'% )';
						}
						$s_option.= '<option value="'.$o_fee->fee_id.'">'.$s_fee_desc.'</option>';
					}
				}
	
				// foreach ($mba_invoice_data as $o_sub_invoice_details) {
				// 	if ($o_sub_invoice_details->sub_invoice_details_amount_paid > 0) {
				// 		$b_has_payment = true;
				// 	}
				// }
				$b_has_payment = (in_array($mba_invoice_data[0]->invoice_status, ['paid', 'cancelled'])) ? true : false;
			}
		}
		
		$this->a_page_data['option_fee'] = str_replace("'", '&apos;', $s_option);
		$this->a_page_data['has_payment'] = $b_has_payment;
		$this->a_page_data['invoice_id'] = $s_invoice_id;
		$this->a_page_data['body'] = $this->load->view('sub_invoice', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function lists($s_personal_data_id = false)
	{
		if ($s_personal_data_id) {
			$mbo_student_data = $this->Stm->get_student_filtered([
				'ds.personal_data_id' => $s_personal_data_id,
				// 'student_status != ' => 'resign'
			]);

			if ($mbo_student_data) {
				$index_stu = 0;
				if (count($mbo_student_data) > 1) {
					foreach ($mbo_student_data as $key => $stu) {
						if ($stu->student_status != 'resign') {
							$index_stu = $key;
						}
					}
				}
				$mba_billing_fee = $this->Im->get_student_billing([
					'di.personal_data_id' => $mbo_student_data[$index_stu]->personal_data_id
				], 'fee.payment_type_code');
				if ($mba_billing_fee) {
					foreach ($mba_billing_fee as $o_billing) {
						$a_billing_detail_data = modules::run('callback/api/get_list_billing', $mbo_student_data[$index_stu]->student_id, $o_billing->payment_type_code);
						$o_billing->billing_detail = $a_billing_detail_data;
					}
				}

				$this->a_page_data['personal_data_id'] = $s_personal_data_id;
				$this->a_page_data['o_student_data'] = $mbo_student_data[$index_stu];
				$this->a_page_data['billing_list'] = $mba_billing_fee;
			}
		}

		// $this->a_page_data['test_array'] = json_encode([
		// 	'payment_type_id' => '09',
		// 	'student_invoice_type' => 'internal',
		// 	'invoice_academic_year_id' => '2022',
		// 	'invoice_semester_type_id' => '1',
		// ]);
		
		$this->a_page_data['semester_type_lists'] = $this->Sem->get_semester_type_lists(false, false, array(1,2));
		$this->a_page_data['academic_year_lists'] = $this->Aym->get_academic_year_lists();
		$this->a_page_data['invoice_status'] = $this->General->get_enum_values('dt_invoice', 'invoice_status');
		$this->a_page_data['payment_type'] = $this->Im->get_payment_type();
		$this->a_page_data['body'] = $this->load->view('table/invoice_list', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function delete_invoice_details()
	{
		if ($this->input->is_ajax_request()) {
			$s_invoice_id = $this->input->post('invoice_id');
			$s_fee_id = $this->input->post('fee_id');

			$mba_invoice_details_list = $this->Im->get_invoice_details([
				'did.invoice_id' => $s_invoice_id,
				'did.fee_id' => $s_fee_id
			]);

			if (!$mba_invoice_details_list) {
				$a_return = ['code' => 1, 'message' => "Failed retrieve invoice details data!"];
			}
			else if ($mba_invoice_details_list[0]->fee_amount_type == 'main') {
				$a_return = ['code' => 1, 'message' => "Fee type is main, can't remove main fee!"];
			}
			else {
				$mba_invoice_data = $this->General->get_where('dt_invoice', ['invoice_id' => $s_invoice_id]);
				$s_personal_data_id = $mba_invoice_data[0]->personal_data_id;

				$b_remove_details = $this->Im->remove_invoice_details($s_invoice_id, $s_fee_id);
				if (!$b_remove_details) {
					$a_return = ['code' => 1, 'message' => 'Error remove data!'];
				}
				else {
					$mba_invoice_details_main_data = $this->Im->student_has_invoice_data($mba_invoice_data[0]->personal_data_id, [
						'di.invoice_id' => $s_invoice_id,
						'df.fee_amount_type' => 'main'
					]);
					
					$mba_invoice_details_data = $this->Im->student_has_invoice_list($mba_invoice_data[0]->personal_data_id, [
						'di.invoice_id' => $s_invoice_id
					]);
					$installment_invoice = $this->Im->get_invoice_installment($s_invoice_id);
					$full_payment_invoice = $this->Im->get_invoice_full_payment($s_invoice_id);

					$d_total_amount = 0;
					if ($mba_invoice_details_data) {
						foreach ($mba_invoice_details_data as $o_invoice_details) {
							if ($o_invoice_details->invoice_details_amount_number_type == 'percentage') {
								$d_amount_details = $mba_invoice_details_main_data->invoice_details_amount * $o_invoice_details->invoice_details_amount / 100;
								if ($o_invoice_details->invoice_details_amount_sign_type == 'positive') {
									$d_total_amount += $d_amount_details;
								}
								else {
									$d_total_amount -= $d_amount_details;
								}
							}
							else {
								if ($o_invoice_details->invoice_details_amount_sign_type == 'positive') {
									$d_total_amount += $o_invoice_details->invoice_details_amount;
								}
								else {
									$d_total_amount -= $o_invoice_details->invoice_details_amount;
								}
							}
						}
					}

					$a_sub_invoice_data = [
						'sub_invoice_amount' => $d_total_amount,
						'sub_invoice_amount_total' => $mba_invoice_data[0]->invoice_amount_fined + $d_total_amount
					];
					$this->Im->update_sub_invoice($a_sub_invoice_data, ['invoice_id' => $s_invoice_id]);

					if ($full_payment_invoice) {
						$a_sub_invoice_details_data = [
							'sub_invoice_details_amount' => $d_total_amount,
							'sub_invoice_details_amount_total' => $d_total_amount + $full_payment_invoice->sub_invoice_details_amount_fined
						];
						$this->Im->update_sub_invoice_details($a_sub_invoice_details_data, ['sub_invoice_details_id' => $full_payment_invoice->sub_invoice_details_id]);
						if (!is_null($full_payment_invoice->trx_id)) {
							$this->change_trx_details($full_payment_invoice->trx_id);
						}
					}

					if ($installment_invoice) {
						$d_invoice_amount = round(($d_total_amount / count($installment_invoice)), 0, PHP_ROUND_HALF_UP);
						$d_installment_filled = 0;
						foreach ($installment_invoice as $o_installment) {
							$a_sub_invoice_details_data = [
								'sub_invoice_details_amount' => $d_invoice_amount,
								'sub_invoice_details_amount_total' => $d_invoice_amount + $o_installment->sub_invoice_details_amount_fined
							];
							$this->Im->update_sub_invoice_details($a_sub_invoice_details_data, ['sub_invoice_details_id' => $o_installment->sub_invoice_details_id]);
							if (!is_null($o_installment->trx_id)) {
								$this->change_trx_details($o_installment->trx_id);
							}
						}
					}

					$a_return = ['code' => 0, 'message' => 'Success!'];
				}
			}

			print json_encode($a_return);
		}
	}

	public function update_invoice_details()
	{
		if ($this->input->is_ajax_request()) {
			$s_invoice_id = $this->input->post('invoice_id');
			$s_old_fee_id = $this->input->post('old_fee_id');
			$s_new_fee_id = $this->input->post('new_fee_id');

			$mba_invoice_data = $this->Im->get_invoice_data([
				'di.invoice_id' => $s_invoice_id
			]);

			$mba_fee_data = $this->Finm->is_fee_exists([
				'fee_id' => $s_new_fee_id
			]);

			if (empty($s_new_fee_id)) {
				$a_return = ['code' => 1, 'message' => 'Please select additional fee!'];
			}
			else if(!$mba_invoice_data){
				$a_return = ['code' => 1, 'message' => 'Failed retrieve invoice data!'];
			}
			else if (in_array($mba_invoice_data[0]->invoice_status, ['paid', 'cancelled'])) {
				$a_return = ['code' => 1, 'message' => 'invoice status is paid or cancelled!'];
			}
			else if (!$mba_fee_data) {
				$a_return = ['code' => 1, 'message' => 'Failed retrieve fee data!'];
			}
			else {
				$installment_invoice = $this->Im->get_invoice_installment($s_invoice_id);
				$full_payment_invoice = $this->Im->get_invoice_full_payment($s_invoice_id);

				// $b_already_payment = false;
				// if (($full_payment_invoice) AND ($full_payment_invoice->sub_invoice_details_status == 'paid')) {
				// 	$b_already_payment = true;
				// }
				// else if ($installment_invoice) {
				// 	foreach ($installment_invoice as $o_installment) {
				// 		if ($o_installment->sub_invoice_details_status == 'paid') {
				// 			$b_already_payment = true;
				// 		}
				// 	}
				// }

				// if ($b_already_payment) {
				// 	$a_return = ['code' => 1, 'message' => 'There is already a payment on the invoice'];
				// }
				// else {
					$a_invoice_details_data = [
						'fee_id' => $s_new_fee_id,
						'invoice_details_amount' => $mba_fee_data->fee_amount,
						'invoice_details_amount_number_type' => $mba_fee_data->fee_amount_number_type,
						'invoice_details_amount_sign_type' => $mba_fee_data->fee_amount_sign_type
					];

					if (empty($s_old_fee_id)) {
						$a_invoice_details_data['invoice_id'] = $s_invoice_id;
						$this->Im->create_invoice_details($a_invoice_details_data);
					}
					else {
						$this->Im->update_invoice_details($a_invoice_details_data, $s_invoice_id, $s_old_fee_id);
					}

					$mba_invoice_details_main_data = $this->Im->student_has_invoice_data($mba_invoice_data[0]->personal_data_id, [
						'di.invoice_id' => $s_invoice_id,
						'df.fee_amount_type' => 'main'
					]);
					
					$mba_invoice_details_data = $this->Im->student_has_invoice_list($mba_invoice_data[0]->personal_data_id, [
						'di.invoice_id' => $s_invoice_id
					]);

					if (!$mba_invoice_details_data OR !$mba_invoice_details_main_data) {
						$a_return = ['code' => 1, 'message' => 'something wrong when update details invoice!'];
					}
					else {
						$d_total_amount = 0;
						foreach ($mba_invoice_details_data as $o_invoice_details) {
							if ($o_invoice_details->invoice_details_amount_number_type == 'percentage') {
								$d_amount_details = $mba_invoice_details_main_data->invoice_details_amount * $o_invoice_details->invoice_details_amount / 100;
								if ($o_invoice_details->invoice_details_amount_sign_type == 'positive') {
									$d_total_amount += $d_amount_details;
								}
								else {
									$d_total_amount -= $d_amount_details;
								}
							}
							else {
								if ($o_invoice_details->invoice_details_amount_sign_type == 'positive') {
									$d_total_amount += $o_invoice_details->invoice_details_amount;
								}
								else {
									$d_total_amount -= $o_invoice_details->invoice_details_amount;
								}
							}
						}

						$a_sub_invoice_data = [
							'sub_invoice_amount' => $d_total_amount,
							'sub_invoice_amount_total' => $mba_invoice_data[0]->invoice_amount_fined + $d_total_amount
						];
						$this->Im->update_sub_invoice($a_sub_invoice_data, ['invoice_id' => $mba_invoice_data[0]->invoice_id]);

						if ($full_payment_invoice) {
							$a_sub_invoice_details_data = [
								'sub_invoice_details_amount' => $d_total_amount,
								'sub_invoice_details_amount_total' => $d_total_amount + $full_payment_invoice->sub_invoice_details_amount_fined
							];
							$this->Im->update_sub_invoice_details($a_sub_invoice_details_data, ['sub_invoice_details_id' => $full_payment_invoice->sub_invoice_details_id]);
							if (!is_null($full_payment_invoice->trx_id)) {
								$this->change_trx_details($full_payment_invoice->trx_id);
							}
						}

						if ($installment_invoice) {
							$d_invoice_amount = round(($d_total_amount / count($installment_invoice)), 0, PHP_ROUND_HALF_UP);
							$d_installment_filled = 0;
							foreach ($installment_invoice as $o_installment) {
								$a_sub_invoice_details_data = [
									'sub_invoice_details_amount' => $d_invoice_amount,
									'sub_invoice_details_amount_total' => $d_invoice_amount + $o_installment->sub_invoice_details_amount_fined
								];
								$this->Im->update_sub_invoice_details($a_sub_invoice_details_data, ['sub_invoice_details_id' => $o_installment->sub_invoice_details_id]);
								if (!is_null($o_installment->trx_id)) {
									$this->change_trx_details($o_installment->trx_id);
								}
							}
						}
					}

					$a_return = ['code' => 0, 'message' => 'Success'];
				// }
			}

			print json_encode($a_return);
		}
	}
	
	public function get_invoice_details()
	{
		if($this->input->is_ajax_request()){
			$s_invoice_id = $this->input->post('invoice_id');
			$mba_invoice_details = $this->Im->get_invoice_details([
				'did.invoice_id' => $s_invoice_id
			]);

			$mba_fee_list_select = false;
			if ($mba_invoice_details) {
				foreach ($mba_invoice_details as $o_invoice_details) {
					$mba_fee_list_select = $this->Im->get_fee([
						'academic_year_id' => $o_invoice_details->academic_year_id,
						'fee_amount_type' => $o_invoice_details->fee_amount_type
					]);

					$s_option = '';
					if ($mba_fee_list_select) {
						foreach ($mba_fee_list_select as $o_fee) {
							if (!is_null($o_fee->study_program_id)) {
								$mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $o_fee->study_program_id]);
								$s_fee_desc = $o_fee->fee_description.' - '.$mba_study_program_data[0]->study_program_abbreviation.'';
							}
							else {
								$s_fee_desc = $o_fee->fee_description;
							}

							if ($o_fee->fee_amount_number_type == 'number') {
								$s_fee_desc .= '( '.(($o_fee->fee_amount_sign_type == 'negative') ? '- ' : '').'Rp. '.number_format($o_fee->fee_amount, 2, ',', '.').')';
							}
							else {
								$s_fee_desc .= '( '.(($o_fee->fee_amount_sign_type == 'negative') ? '- ' : '').number_format($o_fee->fee_amount, 2, ',', '.').'% )';
							}

							$s_selected = ($o_fee->fee_id == $o_invoice_details->fee_id) ? 'selected="selected"' : '';
							$s_option.= '<option value="'.$o_fee->fee_id.'" '.$s_selected.'>'.$s_fee_desc.'</option>';
						}
					}

					$o_invoice_details->key = md5($o_invoice_details->invoice_id.$o_invoice_details->fee_id);
					$o_invoice_details->fee_option = $s_option;
				}
			}
			
			print json_encode(array('data' => $mba_invoice_details));
			exit;
		}
	}
	
	public function reactivate_billing($s_trx_id)
	{
		$o_bni_data = $this->Bnim->get_data_by_trx_id($s_trx_id);
		$o_sub_invoice_details_data = $this->Im->get_sub_invoice_by_trx_id($s_trx_id);
		
		$a_trx_data = array(
			'trx_amount' => $o_sub_invoice_details_data->sub_invoice_details_amount_total,
			'billing_type' => $o_bni_data->billing_type,
			'customer_name' => $o_bni_data->customer_name,
			'virtual_account' => $o_bni_data->virtual_account,
			'description' => $o_sub_invoice_details_data->sub_invoice_details_description,
			'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details_data->sub_invoice_details_deadline." +10 day")),
			'customer_email' => $o_bni_data->customer_email
		);
		
		$a_bni_result = $this->Bnim->create_billing($a_trx_data);
		if($a_bni_result['status'] === '000'){
			$s_new_trx_id = $a_bni_result['trx_id'];
			$this->Im->update_sub_invoice_details(
				array(
					'trx_id' => $s_new_trx_id,
					'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details_data->sub_invoice_details_deadline))
				), 
				array(
					'sub_invoice_details_id' => $o_sub_invoice_details_data->sub_invoice_details_id
				)
			);
			return $s_new_trx_id;
		}
	}

	public function change_trx_details_custom()
	{
		$a_data = [
			'trx_id' => '762984109',
			'trx_amount' => '20000000',
			'customer_name' => 'JOHANNES ARIA PRADANA',
			'datetime_expired' => '2021-09-04 23:59:59',
			'description' => 'Installment 2 1st semester fee - Batch 2021',
			'customer_email' => 'bni.employee@company.ac.id'
		];

		$update = $this->Bnim->update_billing($a_data);
		print('<pre>');
		var_dump($update);exit;
	}
	
	public function change_trx_details($s_trx_id)
	{
		$this->load->model('Bni_model', 'Bm');
		$o_bni_data = $this->Bnim->get_data_by_trx_id($s_trx_id);
		
		if($o_bni_data->va_status == 2){
			$s_trx_id = $this->reactivate_billing($s_trx_id);
			$o_bni_data = $this->Bnim->get_data_by_trx_id($s_trx_id);
		}
		
		if($o_sub_invoice_details_data = $this->Im->get_sub_invoice_by_trx_id($s_trx_id)){
			$a_update_billing = array(
				'trx_id' => $o_sub_invoice_details_data->trx_id,
				'trx_amount' => $o_sub_invoice_details_data->sub_invoice_details_amount_total,
				'customer_name' => $o_bni_data->customer_name,
				'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details_data->sub_invoice_details_deadline." +3 day")),
				'description' => $o_sub_invoice_details_data->sub_invoice_details_description,
				'customer_email' => 'bni.employee@company.ac.id'
			);
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
		
		$this->Bnim->update_billing($a_update_billing);
	}

	// public function cancel_all_billing()
	// {
	// 	$a_trx_id = ['420452464','2021999708','958359575','1479556265','909427688','828458153','1774209514','1995146943','2114741680','572514340','754187671','2089672363','1582074988','397097073','1233069089','1493487987','1597731373','508488406','1448166321','1217725835','1237559250','1363213750','665434977','951697073','660408617','979037780','1790180899','295058704','296883117'];
	// 	foreach ($a_trx_id as $s_trx_id) {
	// 		$this->cancel_payment($s_trx_id, true);
	// 		print('<br>');
	// 	}
	// }

	public function cancel_payment($s_trx_id, $b_print = false)
	{
		$a_update_billing = array(
			'trx_id' => $s_trx_id,
			'trx_amount' => 99,
			'customer_name' => 'CANCEL PAYMENT',
			'datetime_expired' => '2020-01-01 23:59:59',
			'description' => 'CANCEL PAYMENT'
		);

		$update = $this->Bnim->update_billing($a_update_billing);
		if ($b_print) {
			print('<pre>');
			var_dump($update);
		}
	}
	
	public function get_all_invoice_information($s_invoice_id)
	{
		$o_invoice_data = $this->Im->get_invoice_data([
			'di.invoice_id' => $s_invoice_id
		])[0];
		
		$o_invoice_data->invoice_details = $this->Im->get_invoice_details([
			'did.invoice_id' => $s_invoice_id
		]);
		
		$a_sub_invoice = $this->Im->get_sub_invoice_data(['dsi.invoice_id' => $s_invoice_id]);
		
		foreach($a_sub_invoice as $o_sub_invoice){
			$a_sub_invoice_details = $this->Im->get_invoice_data(['dsid.sub_invoice_id' => $o_sub_invoice->sub_invoice_id]);
			$o_sub_invoice->sub_invoice_details = $a_sub_invoice_details;
			$o_invoice_data->{'invoice_'.$o_sub_invoice->sub_invoice_type} = $o_sub_invoice;
		}
		
		return $o_invoice_data;
	}

	public function create_initial_installment()
	{
		if ($this->input->is_ajax_request()) {
            $post_data = $this->input->post('data');
            $s_student_id = $this->input->post('student_id');
            $s_sub_invoice_id = $this->input->post('sub_invoice_id');

            $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);

            $mba_student_last_score = $this->Scm->get_last_score(array('st.student_id' => $s_student_id));
            $a_error_process = array();

			$d_total_amount = 0;
			$d_total_amount_fine = 0;
			$d_total_amount_billed = 0;

            if (count($post_data) > 0) {
                foreach ($post_data as $key => $a_installment) {
                    $i_installment = $a_installment['installment_number'];
                    $amount_transaction = str_replace('.', '', $a_installment['installment_billed_amount']);
                    $amount_fined = str_replace('.', '', $a_installment['installment_fine_amount']);
                    $amount_total = doubleval($amount_transaction) + doubleval($amount_fined);
                    $s_payment_type = '02';
                    
                    if (($s_payment_type == '02') AND ($i_installment == 0)) {
                        $i_installment = 1;
                        $s_payment_type = '10';
                    }

					$mbs_va_number = $this->Bnim->generate_va_number(
						$s_payment_type,
						(is_null($mbo_student_data->student_number)) ? 'candidate' : 'student',
						(is_null($mbo_student_data->student_number)) ? null : $mbo_student_data->student_number,
						$mbo_student_data->finance_year_id,
						$mbo_student_data->program_id
					);

                    // $mbs_va_number = $this->Bnim->get_va_number(
                    //     $s_payment_type,
                    //     ($mba_student_last_score) ? $mba_student_last_score->semester_id : '1',
                    //     $i_installment,
                    //     (is_null($mbo_student_data->student_number)) ? 'candidate' : 'student',
                    //     (is_null($mbo_student_data->student_number)) ? null : $mbo_student_data->student_number,
                    //     $mbo_student_data->finance_year_id,
                    //     $mbo_student_data->program_id
                    // );

                    if ($mbs_va_number) {

                        // $a_billing_data = array(
                        //     'trx_amount' => $amount_total,
                        //     'billing_type' => 'c',
                        //     'customer_name' => str_replace("'", "", $mbo_student_data->personal_data_name),
                        //     'virtual_account' => $mbs_va_number,
                        //     'description' => $a_installment['installment_description'],
                        //     'datetime_expired' => date('Y-m-d 23:59:59', strtotime($a_installment['installment_deadline']." +3 day")),
                        //     'customer_email' => 'bni.employee@company.ac.id'
                        // );
    
                        // $a_return_billing_data = $this->Bnim->create_billing($a_billing_data);
                        
                        // if($a_return_billing_data['status'] === '000'){
							$d_total_amount += $amount_transaction;
							$d_total_amount_fine += $amount_fined;
							$d_total_amount_billed += $amount_total;

                            $a_sub_invoice_details = array(
                                'sub_invoice_id' => $s_sub_invoice_id,
                                // 'trx_id' => $a_return_billing_data['trx_id'],
								'trx_id' => NULL,
                                'sub_invoice_details_amount' => $amount_transaction,
                                'sub_invoice_details_amount_fined' => $amount_fined,
                                'sub_invoice_details_amount_total' => $amount_total,
                                'sub_invoice_details_va_number' => $mbs_va_number,
                                'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($a_installment['installment_deadline'])),
                                'sub_invoice_details_real_datetime_deadline' => date('Y-m-d 23:59:59', strtotime($a_installment['installment_deadline'])),
                                'sub_invoice_details_description' => $a_installment['installment_description']
                            );

                            $s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details);
                        // }
                        // else{
                        //     $this->db->trans_rollback();
                        //     array_push($a_error_process, $a_return_billing_data['message'].": ".$mbs_va_number);
                        //     // print($a_return_billing_data['message'].": ".$mbs_va_number);
                        // }

                    }else {
						$this->db->trans_rollback();
                        array_push($a_error_process, "Error creating va number for payment type ".$s_payment_type." installment ".$i_installment);
                        // print('Error buat nomor virtual account');
                    }
                    
                }

                if (count($a_error_process) > 0) {
                    $a_return = array('code' => 1, 'message' => '<li>'.implode('</li><li>', $a_error_process).'</li>');
                }else{
					$a_sub_invoice_data = [
						'sub_invoice_amount' => $d_total_amount_billed,
						'sub_invoice_amount_total' => $d_total_amount_billed,
					];

					$this->Im->update_sub_invoice($a_sub_invoice_data, ['sub_invoice_id' => $s_sub_invoice_id]);
                    $a_return = array('code' => 0, 'message' => 'Succses');
                }
            }else{
                $a_return = array('code' => 1, 'message' => 'No data processing!');
            }

            print json_encode($a_return);
            
        }
	}
	
	public function create_initial_tuition_fee()
	{
		if($this->input->is_ajax_request()){
			$this->load->model('Bni_model', 'Bm');
			$this->load->model('personal_data/Personal_data_model', 'Pdm');
			
			// $this->form_validation->set_rules('installments[]', 'Additional Fee', 'trim|required');
			$this->form_validation->set_rules('deadline[]', 'Deadline', 'trim|required');
			
			if($this->form_validation->run()){
				$s_personal_data_id = $this->input->post('personal_data_id');
				$d_sub_total = $this->input->post('total');
				$d_sub_total_installment = array_sum($this->input->post('installments'));
				$s_fee_id = $this->input->post('fee_id');
				
				$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
				$mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id, [
					'student_status !=' => 'resign'
				]);
				$s_student_number = $mbo_student_data->student_number;
				if (is_null($mbo_student_data->student_number)) {
					$s_student_number = modules::run('student/create_student_number', $mbo_student_data);
				}
				
				$mba_fee_data = $this->Im->get_fee(array('fee_id' => $s_fee_id));
				
				$a_invoice_data = array(
					'personal_data_id' => $s_personal_data_id,
					'invoice_number' => $this->Im->get_invoice_number('02'),
					'invoice_description' => $mba_fee_data[0]->fee_description." - Batch ".$mbo_student_data->finance_year_id,
					'invoice_allow_fine' => 'no',
					'invoice_admission_reminder' => 'yes',
					'academic_year_id' => $mbo_student_data->academic_year_id,
					'semester_type_id' => 1
				);
				
				$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
				
				$a_invoice_details = array(
					array(
						'fee_id' => $s_fee_id,
						'invoice_id' => $s_invoice_id,
						'invoice_details_amount' => $mba_fee_data[0]->fee_amount,
						'invoice_details_amount_number_type' => $mba_fee_data[0]->fee_amount_number_type,
						'invoice_details_amount_sign_type' => $mba_fee_data[0]->fee_amount_sign_type
					)
				);

				if($this->input->post('discount')){
					$discounts = $this->input->post('discount');
					if (is_array($discounts)) {
						if (count($discounts) > 0) {
							foreach ($discounts as $discount) {
								if ($discount !== '') {
									$mbo_fee_invoice_details = $this->General->get_where('dt_fee', ['fee_id' => $discount])[0];
									array_push($a_invoice_details, array(
										'fee_id' => $discount,
										'invoice_id' => $s_invoice_id,
										'invoice_details_amount' => $mbo_fee_invoice_details->fee_amount,
										'invoice_details_amount_number_type' => $mbo_fee_invoice_details->fee_amount_number_type,
										'invoice_details_amount_sign_type' => $mbo_fee_invoice_details->fee_amount_sign_type
									));
								}
							}
						}
					}else{
						if ($discount !== '') {
							$mbo_fee_invoice_details = $this->General->get_where('dt_fee', ['fee_id' => $discount])[0];
							array_push($a_invoice_details, array(
								'fee_id' => $this->input->post('discount'),
								'invoice_id' => $s_invoice_id,
								'invoice_details_amount' => $mbo_fee_invoice_details->fee_amount,
								'invoice_details_amount_number_type' => $mbo_fee_invoice_details->fee_amount_number_type,
								'invoice_details_amount_sign_type' => $mbo_fee_invoice_details->fee_amount_sign_type
							));
						}
					}
				}
				
				if($this->input->post('additional_fees')){
					for($i = 0; $i < count($this->input->post('additional_fees')); $i++){
						$mbo_fee_invoice_details = $this->General->get_where('dt_fee', ['fee_id' => $this->input->post('additional_fees')[$i]])[0];
						array_push($a_invoice_details, array(
							'fee_id' => $this->input->post('additional_fees')[$i],
							'invoice_id' => $s_invoice_id,
							'invoice_details_amount' => $mbo_fee_invoice_details->fee_amount,
							'invoice_details_amount_number_type' => $mbo_fee_invoice_details->fee_amount_number_type,
							'invoice_details_amount_sign_type' => $mbo_fee_invoice_details->fee_amount_sign_type
						));
					}
				}
				
				if(count($a_invoice_details) >= 1){
					for($i = 0; $i < count($a_invoice_details); $i++){
						$this->Im->create_invoice_details($a_invoice_details[$i]);
					}
				}
				$a_sub_invoice_installment = array(
					'invoice_id' => $s_invoice_id,
					'sub_invoice_type' => 'installment',
					'sub_invoice_amount' => $d_sub_total_installment,
					'sub_invoice_amount_total' => $d_sub_total_installment
				);
				$s_sub_invoice_id_installment = $this->Im->create_sub_invoice($a_sub_invoice_installment);
				
				$a_sub_invoice_fullpayment = array(
					'invoice_id' => $s_invoice_id,
					'sub_invoice_type' => 'full',
					'sub_invoice_amount' => $d_sub_total,
					'sub_invoice_amount_total' => $d_sub_total
				);
				$s_sub_invoice_id_fullpayment = $this->Im->create_sub_invoice($a_sub_invoice_fullpayment);
				
				$a_bni_status = array();
				
				// $s_va_number_0 = '';
				// $s_va_number_0 = $this->Bnim->get_va_number('02', 1, 0, $mbo_student_data->student_status, null, $mbo_student_data->finance_year_id, $mbo_student_data->program_id);
				$s_va_number = $this->Bnim->generate_va_number(
					'02',
					'student',
					$s_student_number
				);
				$a_fullpayment_data = array(
					'trx_amount' => $d_sub_total,
					'billing_type' => 'n',
					'customer_name' => ucwords(strtolower(str_replace("'", "", $mbo_personal_data->personal_data_name))),
					'virtual_account' => $s_va_number,
					'description' => 'Full Payment '.$mba_fee_data[0]->fee_description." Batch ".$mbo_student_data->finance_year_id,
					'datetime_expired' => date('Y-m-d 23:59:59', strtotime($this->input->post('deadline')[0]." +3 day")),
					'customer_email' => 'bni.employee@company.ac.id'
				);
				$a_bni_result_fullpayment = $this->Bnim->create_billing($a_fullpayment_data);
				if($a_bni_result_fullpayment['status'] === '000'){
					$s_trx_id_fullpayment = $a_bni_result_fullpayment['trx_id'];
					$a_sub_invoice_details_fullpayment = array(
						'trx_id' => $s_trx_id_fullpayment,
						'sub_invoice_id' => $s_sub_invoice_id_fullpayment,
						'sub_invoice_details_amount' => $d_sub_total,
						'sub_invoice_details_amount_total' => $d_sub_total,
						'sub_invoice_details_va_number' => $s_va_number,
						'sub_invoice_details_description' => 'Full Payment '.$mba_fee_data[0]->fee_description." - Batch ".$mbo_student_data->finance_year_id,
						'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($this->input->post('deadline')[0])),
						'sub_invoice_details_real_datetime_deadline' => date('Y-m-d 23:59:59', strtotime($this->input->post('deadline')[0]))
					);
					$this->Im->create_sub_invoice_details($a_sub_invoice_details_fullpayment);
				}
				else{
					array_push($a_bni_status, $a_bni_result_fullpayment);
				}
				
				modules::run('admission/accepted_as_student', $mbo_student_data, $s_invoice_id);
				
				$a_return = array('code' => 0, 'message' => 'Success', 'bni_statuses' => $a_bni_status);
			}
			else{
				$a_return = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
			}
			
			print json_encode($a_return);
			exit;
		}
	}
	
	public function view_send_email_form($b_text = false)
	{
		if($b_text){
			if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
				return $this->load->view('form/new_invoice_form', $this->a_page_data, true);
			}
			else {
				return $this->load->view('form/invoice_form', $this->a_page_data, true);
			}
		}
		else{
			if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
				$this->load->view('form/new_invoice_form', $this->a_page_data);
			}
			else {
				$this->load->view('form/invoice_form', $this->a_page_data);
			}
		}
	}
	
	
	public function initial_tuition_fee($b_text = false)
	{
		$o_active_batch = $this->Adm->get_active_intake_year();
		
		$this->a_page_data['discounts'] = $this->Im->get_fee(array('fee_amount_sign_type' => 'negative', 'fee_amount_type' => 'additional', 'academic_year_id' => $o_active_batch->academic_year_id));
		$this->a_page_data['additional_fees'] = $this->Im->get_fee(array('fee_amount_sign_type' => 'positive', 'fee_amount_type' => 'additional', 'academic_year_id' => $o_active_batch->academic_year_id));
		if($b_text){
			return $this->load->view('form/initial_tuition_fee', $this->a_page_data, true);
		}
		else{
			$this->load->view('form/initial_tuition_fee', $this->a_page_data);
		}
	}

	function save_setting() {
		if ($this->input->is_ajax_request()) {
			$s_student_id = $this->input->post('student_id');
			$s_personal_data_id = $this->input->post('personal_data_id');

			$s_min_payment = $this->input->post('finance_min_payment');
			$s_min_payment = (empty($s_min_payment)) ? NULL : $s_min_payment;

			$a_personal_update = $this->General->update_data('dt_personal_data', [
				'finance_min_payment' => $s_min_payment
			], [
				'personal_data_id' => $s_personal_data_id
			]);

			$mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
			$billing_body = modules::run('callback/api/get_list_billing', $s_student_id, '02');
			if ((count($billing_body) > 0) AND (!is_null($billing_body['trx_id']))) {
				if ($mba_student_data) {
					$a_update_billing = array(
						'trx_id' => $billing_body['trx_id'],
						'trx_amount' => $billing_body['min_payment'],
						'customer_name' => $mba_student_data[0]->personal_data_name,
						'datetime_expired' => '2024-06-30 23:59:59',
						'description' => $billing_body['billing_description']
					);
					$this->Bnim->update_billing($a_update_billing);
				}
			}
			
			$a_return = ['code' => 0,'message' => 'Success'];
			print json_encode($a_return);exit;
		}
	}

	function save_min_payment() {
		if ($this->input->is_ajax_request()) {
			$s_student_id = $this->input->post('student_id');
			$s_personal_data_id = $this->input->post('personal_data_id');

			$s_min_payment = $this->input->post('min_payment');
			$s_min_payment = (empty($s_min_payment)) ? NULL : $s_min_payment;

			$a_personal_update = $this->General->update_data('dt_personal_data', [
				'finance_min_payment' => $s_min_payment
			], [
				'personal_data_id' => $s_personal_data_id
			]);

			$mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
			$billing_body = modules::run('callback/api/get_list_billing', $s_student_id, '02');
			if ((count($billing_body) > 0) AND (!is_null($billing_body['trx_id']))) {
				if ($mba_student_data) {
					$a_update_billing = array(
						'trx_id' => $billing_body['trx_id'],
						'trx_amount' => $billing_body['min_payment'],
						'customer_name' => $mba_student_data[0]->personal_data_name,
						'datetime_expired' => '2024-06-30 23:59:59',
						'description' => $billing_body['billing_description']
					);
					$this->Bnim->update_billing($a_update_billing);
				}
			}
			
			$a_return = ['code' => 0,'message' => 'Success'];
			print json_encode($a_return);exit;
		}
	}

	function student_setting($s_student_id) {
		$mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
		if ($mba_student_data) {
			$this->a_page_data['o_student'] = $mba_student_data[0];
			$this->a_page_data['body'] = $this->load->view('finance/student_setting', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}
		else {
			show_404();
		}
	}
	
	public function get_initial_tuition_fee(){
		if($this->input->is_ajax_request()){
			$s_study_program_id = $this->input->post('study_program_id');
			$s_academic_year_id = $this->input->post('finance_year');
			$s_semester_id = $this->input->post('semester_id');
			$s_student_class_type = $this->input->post('student_class_type');
			$a_clause = array(
				'study_program_id' => $s_study_program_id,
				'academic_year_id' => $s_academic_year_id,
				'semester_id' => $s_semester_id
			);

			if ($s_student_class_type !== null) {
				$a_clause['program_id'] = ($s_student_class_type == 'karyawan') ? 2 : 1;
			}
			
			$mba_fee_data = $this->Im->get_fee($a_clause);
			if ($mba_fee_data) {
				$a_return = ['code' => 0, 'data' => $mba_fee_data];
			}else{
				$a_return = ['code' => 1, 'message' => 'Fee has not been set up!', 'filter' => $a_clause];
			}
			
			print json_encode($a_return);
			exit;
		}
	}

	public function create_custom_student_invoice_tuition_fee()
	{
		$a_student_number = ['11201808006','11201808009','11201807010','11201813004','11201813002','11201801007','11201806005','11201806002','11201806003','11201806009','11201812007','11201803011','11201803007','11201710005','11201701007','11201702002','11201702012','11201608004','11201607008','11201602002','11201604004','11201602031','11201602025'];
		foreach ($a_student_number as $s_student_number) {
			$mba_student_data = $this->Stm->get_student_filtered(['ds.student_number' => $s_student_number]);
			if (!$mba_student_data) {
				print('student number '.$s_student_number.' not found!');exit;
			}

			$o_student_data = $mba_student_data[0];
			print($o_student_data->personal_data_name.' ('.$o_student_data->finance_year_id.')');
			print('<br>');
			
			// $this->_create_new_single_invoice(
			// 	$o_student_data->personal_data_id,
			// 	// $s_semester_id, 
			// 	// $s_total_credit, 
			// 	// $s_fee_id, 
			// 	'02', 
			// 	' ',
			// 	$i_installments,
			// 	$s_academic_year_id = false,
			// 	$s_semester_type_id = false,
			// 	$d_total_package_certificate = 1
			// );
		}
	}

	// public function begadaaang($s_trx_id)
	// {
	// 	$this->load->model('Bni_model', 'Bm');
	// 	$o_bni_data = $this->Bnim->get_data_by_trx_id($s_trx_id);
		
	// 	if($o_bni_data->va_status == 2){
	// 		$s_trx_id = $this->reactivate_billing($s_trx_id);
	// 		$o_bni_data = $this->Bnim->get_data_by_trx_id($s_trx_id);
	// 	}
		
	// 	if($o_sub_invoice_details_data = $this->Im->get_sub_invoice_by_trx_id($s_trx_id)){
	// 		$a_update_billing = array(
	// 			'trx_id' => $o_sub_invoice_details_data->trx_id,
	// 			'trx_amount' => $o_sub_invoice_details_data->sub_invoice_details_amount,
	// 			'customer_name' => $o_bni_data->customer_name,
	// 			'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_sub_invoice_details_data->sub_invoice_details_deadline." +3 day")),
	// 			'description' => $o_sub_invoice_details_data->sub_invoice_details_description,
	// 			'customer_email' => 'bni.employee@company.ac.id'
	// 		);
	// 	}
	// 	else{
	// 		$a_update_billing = array(
	// 			'trx_id' => $s_trx_id,
	// 			'trx_amount' => 999,
	// 			'customer_name' => 'CANCEL PAYMENT',
	// 			'datetime_expired' => '2020-01-01 23:59:59',
	// 			'description' => 'CANCEL PAYMENT'
	// 		);
	// 	}
		
	// 	$update = $this->Bnim->update_billing($a_update_billing);
	// 	print('<pre>');
	// 	var_dump($update);
	// }
}