<?php
class Finance_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	function get_transaction_sign($a_clause = false) {
		$this->db->select('*, dpd.date_added');
		$this->db->from('dt_personal_document dpd');
		$this->db->join('bni_transactions bt', 'bt.bni_transactions_id = dpd.key_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	function get_receipt_no($year) 
	{
		$this->load->helper('iuli_helper');
		$this->db->from('bni_transactions');
		$this->db->where('YEAR(date_added)', $year);
		$this->db->where('receipt_number != ', NULL);
		$this->db->order_by('date_added', 'DESC');
		
		$query = $this->db->get();
		$query_receipt = ($query->num_rows() > 0) ? $query->first_row() : false;

        $receipt_number = ($query_receipt) ? (intval($query_receipt->receipt_number) + 1) : 1;
        $receipt_number = str_pad($receipt_number, 4, '0', STR_PAD_LEFT);
        $month = ($query_receipt) ? date('m', strtotime($query_receipt->date_added)) : date('m');
        $receipt_month = numberToRomanRepresentation(intval($month));
        $receipt_year = ($query_receipt) ? date('Y', strtotime($query_receipt->date_added)) : date('Y');
        $receipt_no = "KWT/ANF/$receipt_number/IULI/$receipt_month/$receipt_year";

		return $receipt_no;
	}

	function get_payment_history($a_clause = false, $a_order = false, $a_group = false) {
		$this->db->select('*, bt.payment_amount AS total_payment_amount, btp.payment_amount, di.personal_data_id, bt.date_added AS transaction_date_added');
		$this->db->from('bni_transactions_payment btp');
		$this->db->join('bni_transactions bt', 'bt.bni_transactions_id = btp.bni_transactions_id');
		$this->db->join('dt_sub_invoice_details sid', 'sid.sub_invoice_details_id = btp.sub_invoice_details_id', 'left');
		$this->db->join('dt_sub_invoice si', 'si.sub_invoice_id = sid.sub_invoice_id', 'left');
		$this->db->join('dt_invoice di', 'di.invoice_id = si.invoice_id', 'left');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		if ($a_order) {
			foreach ($a_order as $key => $value) {
				$this->db->order_by($key, $value);
			}
		}

		if ($a_group) {
			foreach ($a_group as $s_group) {
				$this->db->group_by($s_group);
			}
		}

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_aid_request_details($a_clause = false)
	{
		$this->db->from('dt_student_aid sa');
		$this->db->join('dt_student_aid_setting sas', 'sas.aid_period_id = sa.aid_period_id');
		$this->db->join('dt_student_aid_files saf', 'saf.request_id = sa.request_id');
		$this->db->join('ref_bank bk', 'bk.bank_code = sa.bank_code', 'LEFT');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_personal_data_scholarship($a_clause = false)
	{
		$this->db->from('dt_personal_data_scholarship pds');
		$this->db->join('ref_scholarship rs', 'rs.scholarship_id = pds.scholarship_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}
	
	public function update_fee($a_fee_data, $s_fee_id)
	{
		$this->db->update('dt_fee', $a_fee_data, array('fee_id' => $s_fee_id));
	}
	
	public function insert_fee($a_fee_data)
	{
		if(!array_key_exists('fee_id', $a_fee_data)){
			$a_fee_data['fee_id'] = $this->uuid->v4();
		}
		$this->db->insert('dt_fee', $a_fee_data);
	}
	
	public function is_fee_exists($a_clause)
	{
		$query = $this->db->get_where('dt_fee df', $a_clause);
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}
	
	public function get_all_fee($a_clause = false)
	{
		$this->db->select("
			dy.academic_year_id,
			sp.study_program_name,
			sp.study_program_ni_name,
			sp.study_program_abbreviation,
			rs.scholarship_name,
			rsem.semester_number,
			rp.program_name,
			rpt.payment_type_name,
			df.*
		");
		$this->db->from('dt_fee df');
		$this->db->join('ref_payment_type rpt', 'payment_type_code', 'LEFT');
		$this->db->join('ref_program rp', 'program_id', 'LEFT');
		$this->db->join('ref_scholarship rs', 'scholarship_id', 'LEFT');
		$this->db->join('ref_study_program sp', 'study_program_id', 'LEFT');
		$this->db->join('dt_academic_year dy', 'academic_year_id', 'LEFT');
		$this->db->join('ref_semester rsem', 'semester_id', 'LEFT');
		if($a_clause){
			$this->db->where($a_clause);
		}
		$this->db->order_by('sp.study_program_name');
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_payment_type_code($s_order = 'ASC', $mba_clause = false)
	{
		$this->db->order_by('payment_type_code', $s_order);
		if($mba_clause){
			$query = $this->db->get_where('ref_payment_type', $mba_clause);
		}
		else{
			$query = $this->db->get('ref_payment_type');
		}
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function update_payment_type_code($a_payment_type_code, $s_payment_type_code)
	{
		$this->db->update('ref_payment_type', $a_payment_type_code, array('payment_type_code' => $s_payment_type_code));
	}
	
	public function insert_payment_type_code($a_payment_type_code)
	{
		$o_payment_type = $this->get_payment_type_code('DESC');
		$s_payment_type_code = intval($o_payment_type[0]->payment_type_code) + 1;
		$s_payment_type_code = str_pad($s_payment_type_code, 2, '0', STR_PAD_LEFT);
		
		$a_payment_type_code['payment_type_code'] = $s_payment_type_code;
		$this->db->insert('ref_payment_type', $a_payment_type_code);
		return $s_payment_type_code;
	}

	public function get_bni_transactions($a_clause = false, $s_order = 'bnt.bni_transactions_id', $s_sort = 'ASC')
	{
		$this->db->from('bni_transactions bnt');
		
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$this->db->order_by($s_order, $s_sort);
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function get_list_bank($a_clause = false)
	{
		$this->db->from('ref_bank');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function save_aid_file($a_data_file)
	{
		$this->db->insert('dt_student_aid_files', $a_data_file);

		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function save_request_aid($a_data_save, $a_clause_update = false)
	{
		if ($a_clause_update) {
			$this->db->update('dt_student_aid', $a_data_save, $a_clause_update);
			return true;
		}else{
			$this->db->insert('dt_student_aid', $a_data_save);
			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}

	public function get_student_aid_list($a_clause = false)
	{
		$this->db->select('*, st.academic_year_id AS "student_batch", st.study_program_id, st.program_id');
		$this->db->from('dt_student_aid sta');
		$this->db->join('dt_student_aid_setting sas', 'sas.aid_period_id = sta.aid_period_id');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = sta.personal_data_id');
		$this->db->join('ref_bank bank', 'bank.bank_code = sta.bank_code');
		$this->db->join('dt_student st', 'st.personal_data_id = pd.personal_data_id');
		$this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
		$this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id');
		$this->db->where('st.student_status', 'active');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function save_period_student_aid($a_data, $a_clause_update = false)
	{
		$s_table_name = 'dt_student_aid_setting';
		if ($a_clause_update) {
			$this->db->update($s_table_name, $a_data, $a_clause_update);
			return true;
		}else{
			$this->db->insert($s_table_name, $a_data);

			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}

	public function get_aid_period_list($a_clause = false)
	{
		$this->db->from('dt_student_aid_setting sas');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function get_request_aid($a_clause = false)
	{
		$this->db->select('*, sta.date_added AS "registration_time"');
		$this->db->from('dt_student_aid sta');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = sta.personal_data_id');
		$this->db->join('dt_student st', 'st.personal_data_id = pd.personal_data_id');
		$this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
		$this->db->join('ref_bank bk', 'bk.bank_code = sta.bank_code');
		$this->db->where('st.student_status', 'active');

		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}
}