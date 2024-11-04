<?php
class Api extends Api_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Invoice_model', 'Im');
		$this->load->model('Bni_model', 'Bm');
	}
	
	public function create_enrollment_invoice()
	{	
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('student/Student_model', 'Sm');
		
		$s_fee_id = '25df9847-972a-11e9-ac6b-5254005d90f6';
		$a_post_data = $this->a_api_data;
		
		$s_personal_data_id = $a_post_data['personal_data_id'];
		
		$mbo_student_data = $this->Sm->get_student_by_personal_data_id($s_personal_data_id);

		if($mbo_student_data){
			$this->db->trans_start();
			$mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
			if($mbo_personal_data){
				$mba_fee_data = $this->Im->get_fee(array('fee_id' => $s_fee_id));
				
				$mbs_va_number = $this->Bm->get_va_number(
					$mba_fee_data[0]->payment_type_code,
					0,
					0,
					$mbo_student_data->student_enrollment_status,
					null,
					$mbo_student_data->academic_year_id,
					($mbo_student_data->program_id !== null) ? $mbo_student_data->program_id : 1
				);
				
				if($mbs_va_number){
					$a_invoice_data = array(
						'fee_id' => $s_fee_id,
						'personal_data_id' => $s_personal_data_id,
						'invoice_number' => $this->Im->get_invoice_number($mba_fee_data[0]->payment_type_code),
						'invoice_description' => $mba_fee_data[0]->fee_description,
						'invoice_allow_fine' => 'no'
					);
					$s_invoice_id = $this->Im->create_invoice($a_invoice_data);
					
					$a_invoice_details_data = array(
						'invoice_id' => $s_invoice_id,
						'fee_id' => $s_fee_id,
						'invoice_details_amount' => $mba_fee_data[0]->fee_amount,
						'invoice_details_amount_number_type' => $mba_fee_data[0]->fee_amount_number_type,
						'invoice_details_amount_sign_type' => $mba_fee_data[0]->fee_amount_sign_type
					);
					$this->Im->create_invoice_details($a_invoice_details_data);
					
					$a_sub_invoice_data = array(
						'sub_invoice_amount' => $mba_fee_data[0]->fee_amount,
						'sub_invoice_amount_total' => $mba_fee_data[0]->fee_amount,
						'invoice_id' => $s_invoice_id
					);
					$s_sub_invoice_id = $this->Im->create_sub_invoice($a_sub_invoice_data);
					
					$a_billing_data = array(
						'trx_amount' => $mba_fee_data[0]->fee_amount,
						'billing_type' => 'c',
						'customer_name' => str_replace("'", "", $mbo_personal_data->personal_data_name),
						'virtual_account' => $mbs_va_number,
						'description' => $mba_fee_data[0]->fee_description,
						'datetime_expired' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time())."+3 month")),
						'customer_email' => 'bni.employee@company.ac.id'
					);
					
					$a_return_billing_data = $this->Bm->create_billing($a_billing_data);
					
					if($a_return_billing_data['status'] === '000'){
						$a_sub_invoice_details = array(
							'sub_invoice_id' => $s_sub_invoice_id,
							'trx_id' => $a_return_billing_data['trx_id'],
							'sub_invoice_details_amount' => $mba_fee_data[0]->fee_amount,
							'sub_invoice_details_amount_total' => $mba_fee_data[0]->fee_amount,
							'sub_invoice_details_va_number' => $mbs_va_number,
							'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time()))),
							'sub_invoice_details_real_datetime_deadline' => date('Y-m-d 23:59:59', strtotime(date('Y-m-d H:i:s', time()))),
							'sub_invoice_details_description' => $mba_fee_data[0]->fee_description
						);
						$s_sub_invoice_details_id = $this->Im->create_sub_invoice_details($a_sub_invoice_details);
						
						$this->db->trans_commit();
						$a_return = array('code' => 0, 'va_number' => $mbs_va_number);
					}
					else{
						$this->db->trans_rollback();
						$a_return = array('code' => $a_return_billing_data['status'], 'message' => $a_return_billing_data['message']);
					}
				}
				else{
					$a_return = array('code' => 2, 'message' => 'VA is not 16 digits');
				}
			}
			else{
				$a_return = array('code' => 3, 'message' => 'Personal not available');
			}
		}
		else{
			$a_return = array('code' => 1, 'message' => 'Student not available');
		}
		
		$this->return_json($a_return);
	}
}