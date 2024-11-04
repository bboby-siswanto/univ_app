<?php
class Bni_model extends CI_Model
{
	
	private $s_production_client_id = '';
	private $s_development_client_id = '';
	
	private $s_production_secret_key = '';
	private $s_development_secret_key = '';
	
	private $s_production_url = '';
	private $s_development_url = '';
	
	private $s_url, $s_client_id, $s_secret_key, $db;
	
	public function __construct()
	{
		parent::__construct();
		$s_environment = 'production';
		if($this->session->userdata('auth')){
			$s_environment = $this->session->userdata('environment');
		}
		$this->db = $this->load->database($s_environment, true);
		$this->set_environment($s_environment);
	}
	
	public function get_data_by_trx_id($s_trx_id)
	{
		$this->inquiry_billing($s_trx_id);
		$query = $this->db->get_where('bni_billing', array('trx_id' => $s_trx_id));
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}
	
	public function set_environment($s_type)
	{
		switch($s_type)
		{
			case "sandbox":
				$this->s_url = $this->s_development_url;
				$this->s_client_id = $this->s_development_client_id;
				$this->s_secret_key = $this->s_development_secret_key;
				break;
				
			case "production":
				$this->s_url = $this->s_production_url;
				$this->s_client_id = $this->s_production_client_id;
				$this->s_secret_key = $this->s_production_secret_key;
				break;
		}
	}

	public function get_url()
	{
		return $this->s_client_id;
		// return $this->session->userdata('environment');
	}

	public function get_va_enroll_sequence($s_payment_type_code, $s_year) {
		$this->db->select('sub_invoice_details_va_number');
		$this->db->from('dt_sub_invoice_details');
		$this->db->like('sub_invoice_details_va_number', '831010'.$s_year, 'after');
		$this->db->like('sub_invoice_details_va_number', $s_payment_type_code, 'before');
		$query = $this->db->get();

		$i_sequence = 0;
		if ($query->num_rows() > 0) {
			$list_va = $query->result();
			foreach ($list_va as $o_va) {
				$sequence_va = intval(substr($o_va->sub_invoice_details_va_number, 10, -3));
				if ($sequence_va > $i_sequence) {
					$i_sequence = $sequence_va;
				}
			}
		}
		$i_sequence++;
		
		return str_pad($i_sequence, 3, '0', STR_PAD_LEFT);
	}
	
	public function get_sequence($s_va_number)
	{
		$this->db->select_max('sub_invoice_details_va_number');
		$this->db->like('sub_invoice_details_va_number', $s_va_number);
		$query = $this->db->get('dt_sub_invoice_details');
		if($query->num_rows() == 1){
			$s_va_number = $query->first_row()->sub_invoice_details_va_number;
			$i_sequence = intval(substr($s_va_number, -4, 4));
			$i_sequence++;
		}
		else{
			$i_sequence = 1;
		}
		
		return str_pad($i_sequence, 4, '0', STR_PAD_LEFT);
		// return ($query->num_rows() == 1) ? $query->first_row() : false;
	}

	public function get_va_partner(
		$s_payment_type_code,
		$i_semester,
		$i_installments,
		$s_student_type = 'accepted',
		$i_year_period,
		$s_prodi_code,
		$i_institute,
		$s_sequence
	)
	{
		if (strlen($s_payment_type_code) != 1) {
			$a_return = ['code' => 1, 'message' => 'Payment type code must 1 digits'];
		}
		else if (strlen($i_semester) != 1) {
			$a_return = ['code' => 1, 'message' => 'Semester code must 1 digits'];
		}
		else {
			$s_installments = str_pad($i_installments, 2, '0', STR_PAD_LEFT);
			$s_institute = str_pad($i_institute, 2, '0', STR_PAD_LEFT);
			$s_year_period = substr($i_year_period,(strlen($i_year_period) - 2), 2);
			$s_va_number = '8'.$this->s_client_id.$s_payment_type_code.$i_semester.$s_installments.$s_institute.$s_year_period.$s_prodi_code.$s_sequence;

			$a_return = ['code' => 0, 'va_number' => $s_va_number];
		}

		return $a_return;
	}

	function generate_va_number(
		$s_payment_type_code,
		$s_student_type,
		$s_student_number = null,
		$i_year = null,
		$i_program_id = 1,
		$s_client = 'production'
	) {
		$s_va_number = '8'.$this->s_client_id;
		if ($s_client != 'production') {
			$s_va_number = '7'.$this->s_development_client_id;
		}

		if ($s_student_type == 'student') {
			$s_number = '1'.$i_program_id.substr($s_student_number, -7);
			$s_va_number .= $s_number;
		}
		else {
			$padd_year = substr($i_year, 2);
			$sequence = $this->get_va_enroll_sequence($s_payment_type_code, $padd_year);
			$s_number = '10'.$s_student_number;
			$s_va_number .= $s_number.$sequence;
		}

		$s_va_number .= '0'.$s_payment_type_code;
		if(strlen($s_va_number) == 16){
			return $s_va_number;
		}
		else{
			$a_error_data = array(
				'student_type' => $s_student_type,
				'student_number' => $s_student_number,
				'year_name' => $i_year,
				'payment_type_code' => $s_payment_type_code,
				'program' => $i_program_id,
				'created_va' => $s_va_number
			);
			$s_json_error = json_encode($a_error_data);
			
			$t_error_message = <<<TEXT
Error getting 16 digits VA Number

VA Payment Type: {$s_payment_type_code}
Program ID: {$i_program_id}
Student Number VA: {$s_student_number}
Student Type: {$s_student_type}
VA Number: {$s_va_number}

Data: $s_json_error
TEXT;
			
			$this->email->from('', 'Error Log');
			$this->email->to(array(''));
			$this->email->subject('Error Create VA Number!!!');
			$this->email->message($t_error_message);
			$this->email->send();
			return false;
		}
	}
	
	public function get_va_number(
		$s_payment_type_code,
		$i_semester,
		$i_installments,
		$s_student_type,
		$s_student_number = null,
		$i_year = null,
		$i_program_id = 1
		)
	{
		$s_va_semester = str_pad($i_semester, 2, '0', STR_PAD_LEFT);
		$s_va_number = '8'.$this->s_client_id.$s_payment_type_code.$s_va_semester.$i_installments.$i_program_id;
		
		if($s_student_type == 'student'){
			$i_year = substr($s_student_number, 4, 2);
			$s_study_program = substr($s_student_number, 6, 2);
			$i_sequence = substr($s_student_number, -2);
			
			$s_student_id_va = $i_year.$s_study_program.$i_sequence;
			$s_student_id_va = str_pad($s_student_id_va, 6, '0', STR_PAD_LEFT);
		}
		else{
			$mba_active_year = $this->General->get_batch(false, true);
			$s_va_year = substr($i_year, 2);
			$s_va_check = $s_va_number.$s_va_year;
			$s_va_sequence = $this->get_sequence($s_va_check);
			$s_student_id_va = $s_va_year.$s_va_sequence;
		}
		
		$s_va_number .= $s_student_id_va;
		
		if(strlen($s_va_number) == 16){
			return $s_va_number;
		}
		else{
			$a_error_data = array(
				'student_type' => $s_student_type,
				'student_number' => $s_student_number,
				'year_name' => $i_year,
				'client_id' => '8'.$this->s_client_id,
				'payment_type_code' => $s_payment_type_code,
				'semester_id' => $s_va_semester,
				'nr_of_installments' => $i_installments,
				'program' => 1,
				'student_id_va' => $s_student_id_va,
				'created_va' => $s_va_number
			);
			$s_json_error = json_encode($a_error_data);
			
			$t_error_message = <<<TEXT
Error getting 16 digits VA Number

BNI Code: {$this->s_client_id}
VA Payment Type: {$s_payment_type_code}
VA Semester: {$s_va_semester}
Number of Installments: {$i_installments}
Program ID: 1
Student ID VA: {$s_student_id_va}
Student Type: {$s_student_type}
VA Number: {$s_va_number}

Data: $s_json_error
TEXT;
			
			$this->email->from('error_employee@company.ac.id', 'Error Log');
			$this->email->to(array('employee@company.ac.id'));
			$this->email->subject('Error Boskuuuuu!!!');
			$this->email->message($t_error_message);
			$this->email->send();
			return false;
		}
	}
	
	public function payment_notification($a_transaction_data)
	{
		$a_transaction_data['transaction_type'] = 'paymentnotification';
		$this->db->insert('bni_transactions', $a_transaction_data);
		
		return $this->inquiry_billing($a_transaction_data['trx_id']);
	}

	public function check_inquiry_billing($s_trx_id, $is_checked = false)
	{
		$a_data = array(
			'type' => 'inquirybilling',
			'client_id' => $this->s_client_id,
			'trx_id' => $s_trx_id
		);
		
		$s_hashed_string = $this->libapi->hash_data(
			$a_data,
			$this->s_client_id,
			$this->s_secret_key
		);
		
		$a_post_data = array(
			'client_id' => $this->s_client_id,
			'data' => $s_hashed_string,
		);
		
		$o_bni_response = $this->libapi->post_data($this->s_url, json_encode($a_post_data));
		
		return $o_bni_response;
		// if ($o_bni_response->status !== '000') {
		// 	return $o_bni_response;
		// }
		// else {
		// 	$a_data_response = $this->libapi->parse_data($o_bni_response->data, $this->s_client_id, $this->s_secret_key);
			
		// 	unset($a_data_response['datetime_created_iso8601']);
		// 	unset($a_data_response['datetime_expired_iso8601']);
		// 	unset($a_data_response['datetime_last_updated_iso8601']);
		// 	unset($a_data_response['datetime_payment_iso8601']);
			
		// 	$a_data_insert_response = $a_data_response;
		// 	$a_data_insert_response['transaction_type'] = 'inquirybilling';
		// 	if (!$is_checked) {
		// 		$this->db->insert('bni_transactions', $a_data_insert_response);
		// 	}
		// 	$this->db->update('bni_billing', $a_data_response, array('trx_id' => $s_trx_id));
			
		// 	return $a_data_response;
		// }
	}
	
	public function inquiry_billing($s_trx_id, $is_checked = false)
	{
		$a_data = array(
			'type' => 'inquirybilling',
			'client_id' => $this->s_client_id,
			'trx_id' => $s_trx_id
		);
		
		$s_hashed_string = $this->libapi->hash_data(
			$a_data,
			$this->s_client_id,
			$this->s_secret_key
		);
		
		$a_post_data = array(
			'client_id' => $this->s_client_id,
			'data' => $s_hashed_string,
		);
		
		$o_bni_response = $this->libapi->post_data($this->s_url, json_encode($a_post_data));
		
		if (empty($o_bni_response)) {
			return ['code' => 999, 'message' => 'No response!'];
		}
		else if ($o_bni_response->status !== '000') {
			return $o_bni_response;
		}
		else {
			$a_data_response = $this->libapi->parse_data($o_bni_response->data, $this->s_client_id, $this->s_secret_key);
			
			unset($a_data_response['datetime_created_iso8601']);
			unset($a_data_response['datetime_expired_iso8601']);
			unset($a_data_response['datetime_last_updated_iso8601']);
			unset($a_data_response['datetime_payment_iso8601']);
			
			$a_data_insert_response = $a_data_response;
			$a_data_insert_response['transaction_type'] = 'inquirybilling';
			if (!$is_checked) {
				$this->db->insert('bni_transactions', $a_data_insert_response);
			}
			$this->db->update('bni_billing', $a_data_response, array('trx_id' => $s_trx_id));
			
			return $a_data_response;
		}
	}
	
	public function update_billing($a_billing_data)
	{
		$a_billing_data['type'] = 'updatebilling';
		$a_billing_data['client_id'] = $this->s_client_id;
		$a_billing_data['customer_name'] = convert_accented_characters($a_billing_data['customer_name']);
		
		$s_hashed_string = $this->libapi->hash_data(
			$a_billing_data,
			$this->s_client_id,
			$this->s_secret_key
		);
		
		$a_post_data = array(
			'client_id' => $this->s_client_id,
			'data' => $s_hashed_string
		);
		$o_bni_response = $this->libapi->post_data($this->s_url, json_encode($a_post_data));

		// if (!isset($o_bni_response->status)) {
		// 	print('<pre>');var_dump($a_billing_data);print('<br>');var_dump($o_bni_response);exit;
		// }
		if ($o_bni_response === null) {
			return array('status' => '900', 'message' => 'No response from bni!');
		}
		else {
			if($o_bni_response->status !== '000'){
				// return $o_bni_response;
				return array('status' => $o_bni_response->status, 'message' => $o_bni_response->message);
			}
			else{
				$a_billing_data['transaction_type'] = 'updatebilling';
				unset($a_billing_data['type']);
				$this->db->insert('bni_transactions', $a_billing_data);
				
				$a_retrieve_response = $this->inquiry_billing($a_billing_data['trx_id']);
				if (is_array($a_retrieve_response)) {
					$this->db->update('bni_billing', $a_retrieve_response, array('trx_id' => $a_billing_data['trx_id']));
				}
				
				return array('status' => $o_bni_response->status, 'trx_id' => $a_retrieve_response['trx_id'], 'retrieve_data' => $a_retrieve_response);
			}
		}
	}
	
	public function create_billing($a_billing_data)
	{
		// $data = array(
			// 'trx_amount' => 200000,
			// 'billing_type' => 'c/i',
			// 'customer_name' => 'customer_name',
			// 'virtual_account' => '8310020211234567',
			// 'description' => 'description',
			// 'datetime_expired' => date('Y-m-d H:i:s', time()),
			// 'customer_email' => 'bni.employee@company.ac.id'
		// );
		$s_trx_id = mt_rand();
		$s_trx_id = mt_rand();
		
		$a_billing_data['type'] = 'createbilling';
		$a_billing_data['client_id'] = $this->s_client_id;
		$a_billing_data['trx_id'] = $s_trx_id;
		$a_billing_data['customer_name'] = convert_accented_characters($a_billing_data['customer_name']);
		
		$s_hashed_string = $this->libapi->hash_data(
			$a_billing_data,
			$this->s_client_id,
			$this->s_secret_key
		);
		
		$a_post_data = array(
			'client_id' => $this->s_client_id,
			'data' => $s_hashed_string,
		);
		$o_bni_response = $this->libapi->post_data($this->s_url, json_encode($a_post_data));
		
		if($o_bni_response->status !== '000'){
			// return $o_bni_response;
			return array('status' => $o_bni_response->status, 'message' => $o_bni_response->message, 'billing_data' => $a_billing_data);
		}
		else{
			$a_data_response = $this->libapi->parse_data($o_bni_response->data, $this->s_client_id, $this->s_secret_key);
			$this->db->insert('bni_billing', $a_data_response);
			
			$a_billing_data['transaction_type'] = 'createbilling';
			unset($a_billing_data['type']);
			$this->db->insert('bni_transactions', $a_billing_data);
			
			$a_retrieve_response = $this->inquiry_billing($a_data_response['trx_id']);
			return array('status' => '000', 'trx_id' => $a_data_response['trx_id'], 'retrieve_data' => $a_retrieve_response);
		}
	}

	public function update_billing_portal($s_trx_id, $a_parsed_data)
	{
		$bni_query = $this->db->get_where('bni_billing', ['trx_id' => $s_trx_id]);
		if ($bni_query->num_rows() > 0) {
			$this->db->update('bni_billing', [
				// 'va_status' => '2',
				'payment_ntb' => $a_parsed_data['payment_ntb'],
				'payment_amount' => $a_parsed_data['payment_amount'],
				'cumulative_payment_amount' => $a_parsed_data['cumulative_payment_amount'],
				'datetime_payment' => $a_parsed_data['datetime_payment'],
				'datetime_last_updated' => date('Y-m-d H:i:s')
			], [
				'trx_id' => $s_trx_id
			]);
		}

		return true;
	}
}