<?php
class Invoice_model extends CI_Model
{
    public function __construct()
    {
		parent::__construct();
    }

	function get_student_billing($a_clause = false, $s_grouping = false) {
        $this->db->from('dt_invoice di');
        $this->db->join('dt_personal_data pd','pd.personal_data_id = di.personal_data_id');
        $this->db->join('dt_invoice_details did', 'did.invoice_id = di.invoice_id');
        $this->db->join('dt_fee fee', 'fee.fee_id = did.fee_id');
		$this->db->join('ref_payment_type rpt', 'rpt.payment_type_code = fee.payment_type_code');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->where_in('di.invoice_status', ['created', 'pending']);
        $this->db->where_not_in('fee.payment_type_code', ['01', '06', '08', '10']);

        if ($s_grouping) {
            $this->db->group_by($s_grouping);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

	function get_invoice_by_deadline($a_clause = false, $a_payment_type_code = false, $unpaid_invoice = true) {
		$this->db->from('dt_sub_invoice_details sid');
		$this->db->join('dt_sub_invoice si', 'si.sub_invoice_id = sid.sub_invoice_id');
		$this->db->join('dt_invoice di', 'di.invoice_id = si.invoice_id');
		$this->db->join('dt_invoice_details id', 'id.invoice_id = di.invoice_id');
		$this->db->join('dt_fee fee', 'fee.fee_id = id.fee_id');
		$this->db->join('ref_payment_type pt', 'pt.payment_type_code = fee.payment_type_code');

		if ($a_clause) {
			$this->db->where($a_clause);
		}
		if ($a_payment_type_code) {
			$this->db->where_in('fee.payment_type_code', $a_payment_type_code);
		}
		if ($unpaid_invoice) {
			$this->db->where_in('di.invoice_status', array('created', 'pending'));
		}

		$this->db->group_by('di.invoice_id');
		$this->db->order_by('sid.sub_invoice_details_real_datetime_deadline', 'ASC');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_first_payment($a_clause = false)
	{
		// $this->db->select('*, di.date_added');
		$this->db->from('dt_sub_invoice_details sid');
		$this->db->join('dt_sub_invoice si', 'si.sub_invoice_id = sid.sub_invoice_id');
		$this->db->join('dt_invoice di', 'di.invoice_id = si.invoice_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$this->db->order_by('sub_invoice_details_datetime_paid_off', 'ASC');

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_invoice_open_payment($a_clause = false)
	{
		$this->db->from('dt_invoice di');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = di.personal_data_id');
		$this->db->join('dt_sub_invoice si', 'si.invoice_id = di.invoice_id');
		$this->db->join('dt_sub_invoice_details sid', 'sid.sub_invoice_id = si.sub_invoice_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$this->db->like('sid.sub_invoice_details_va_number', '831088');
		$this->db->group_by('di.invoice_id');
		$this->db->order_by('di.invoice_number');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_count_invoice_open_payment()
	{
		$this->db->from('dt_sub_invoice_details sid');
		$this->db->join('dt_sub_invoice si', 'si.sub_invoice_id = sid.sub_invoice_id');
		$this->db->join('dt_invoice di', 'di.invoice_id = si.invoice_id');
		$this->db->like('sid.sub_invoice_details_va_number', '831088');
		$this->db->order_by('di.date_added', 'DESC');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->first_row() : false;
	}

	public function get_invoice_list_detail($a_clause = false, $a_invoice_status_in = false)
	{
		$this->db->select('*, fee.scholarship_id AS "fee_scholarship_id"');
		$this->db->from('dt_invoice di');
		$this->db->join('dt_invoice_details did', 'did.invoice_id = di.invoice_id');
		$this->db->join('dt_fee fee', 'fee.fee_id = did.fee_id');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = di.personal_data_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		if ($a_invoice_status_in) {
			$this->db->where_in('di.invoice_status', $a_invoice_status_in);
		}
		$this->db->group_by('di.invoice_id');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
    
    public function get_invoice_data($a_clause = false, $a_invoice_status_in = false)
    {
	    $this->db->join('dt_sub_invoice dsi', 'dsi.sub_invoice_id = dsid.sub_invoice_id');
	    $this->db->join('dt_invoice di', 'di.invoice_id = dsi.invoice_id');
	    ($a_clause) ? $this->db->where($a_clause) : '';
		if ($a_invoice_status_in) {
			$this->db->where_in('di.invoice_status', $a_invoice_status_in);
		}
		$this->db->order_by('dsi.sub_invoice_type', 'ASC');
	    $this->db->order_by('dsid.sub_invoice_details_va_number', 'ASC');
	    $this->db->order_by('dsid.sub_invoice_details_real_datetime_deadline', 'ASC');
	    $query = $this->db->get('dt_sub_invoice_details dsid');
	    return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function student_has_invoice_data($s_personal_data_id, $a_clause = false, $a_status_in = false)
	{
		$this->db->from('dt_invoice di');
		$this->db->join('dt_invoice_details did', 'did.invoice_id = di.invoice_id');
		$this->db->join('dt_fee df', 'df.fee_id = did.fee_id');
		$this->db->where('di.personal_data_id', $s_personal_data_id);
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		if ($a_status_in) {
			$this->db->where_in('di.invoice_status', $a_status_in);
		}
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->first_row() : false;
	}
	
	public function student_has_invoice_list($s_personal_data_id, $a_clause = false, $a_status_in = false, $a_payment_id_in = false)
	{
		$this->db->select('*, di.academic_year_id AS "invoice_academic_year", di.semester_type_id AS "invoice_semester_type", di.date_added AS "invoice_date"');
		$this->db->from('dt_invoice di');
		$this->db->join('dt_invoice_details did', 'did.invoice_id = di.invoice_id');
		$this->db->join('dt_fee df', 'df.fee_id = did.fee_id');
		$this->db->join('ref_semester rs', 'rs.semester_id = df.semester_id', 'LEFT');
		$this->db->where('di.personal_data_id', $s_personal_data_id);
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		if ($a_status_in) {
			$this->db->where_in('di.invoice_status', $a_status_in);
		}

		if ($a_payment_id_in) {
			$this->db->where_in('df.payment_type_code', $a_payment_id_in);
		}

		$this->db->group_by('di.invoice_id');
		$this->db->order_by('di.date_added', 'desc');
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
		// return ($q->num_rows() > 0) ? $q->result() : $this->db->last_query();
	}
    
    public function student_has_invoice_fee_id($s_personal_data_id, $s_fee_id, $mba_invoice_status = false)
    {
	    $this->db->join('dt_invoice_details did', 'did.invoice_id = di.invoice_id');
	    $this->db->join('dt_fee df', 'df.fee_id = did.fee_id');
		if ($mba_invoice_status) {
			$this->db->where_in('di.invoice_status', $mba_invoice_status);
		}
	    $query = $this->db->get_where('dt_invoice di', [
		    'di.personal_data_id' => $s_personal_data_id,
		    'df.fee_id' => $s_fee_id
	    ]);
	    return ($query->num_rows() >= 1) ? $query->first_row() : false;
	}
	
	public function student_has_invoice_va_number($s_personal_data_id, $s_fee_id)
	{
		$this->db->from('dt_sub_invoice_details sid');
		$this->db->join('dt_sub_invoice si', 'si.sub_invoice_id = sid.sub_invoice_id');
		$this->db->join('dt_invoice di', 'di.invoice_id = si.invoice_id');
		$this->db->join('dt_invoice_details did', 'did.invoice_id = di.invoice_id');
		$this->db->join('dt_fee fee', 'fee.fee_id = did.fee_id');
		$this->db->where('di.personal_data_id', $s_personal_data_id);
		$this->db->where('fee.fee_id', $s_fee_id);
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}
    
    public function get_invoice_details($a_clause)
    {
	    $this->db->join('dt_fee df', 'df.fee_id = did.fee_id');
	    $this->db->where($a_clause);
	    $this->db->order_by('df.fee_amount_type', 'ASC');
	    $this->db->order_by('df.fee_amount_sign_type', 'DESC');
	    $query = $this->db->get('dt_invoice_details did');
	    return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    
    public function get_payment_type($s_payment_code = false)
    {
	    if($s_payment_code){
		    $query = $this->db->get_where('ref_payment_type', array('payment_type_code' => $s_payment_code));
		    return ($query->num_rows() == 1) ? $query->first_row() : false;
	    }
	    else{
		    $query = $this->db->get('ref_payment_type');
		    return ($query->num_rows() >= 1) ? $query->result() : false;
	    }
    }

	public function get_invoice_partner($s_personal_data_id = false)
	{
		$this->db->select("
	    	dpd.personal_data_name,
	    	ds.*,
	    	di.*,
			df.semester_id
	    ");
	    $this->db->join('dt_invoice_details did', 'did.invoice_id = di.invoice_id');
	    $this->db->join('dt_fee df', 'df.fee_id = did.fee_id');
	    $this->db->join('dt_personal_data dpd', 'dpd.personal_data_id = di.personal_data_id');
	    $this->db->join('dt_student_partner ds', 'ds.personal_data_id = dpd.personal_data_id');
		$this->db->where('df.fee_amount_type', 'main');
		
		if ($s_personal_data_id) {
			$this->db->where('di.personal_data_id', $s_personal_data_id);
			$this->db->group_by('di.invoice_id');
		}
		// else{
		// 	$this->db->where_in('ds.student_status', ['active', 'onleave', 'inactive']);
		// }

	    $query = $this->db->get('dt_invoice di');
	    return ($query->num_rows() >= 1) ? $query->result() : false;
	}
    
    public function get_invoice_list($s_payment_type_code = false, $s_personal_data_id = false, $a_clause = false)
    {
	    $this->db->select("
			di.date_added,
	    	dpd.personal_data_name,
	    	ds.*,
	    	rsp.*,
	    	dtay.*,
	    	di.*,
			df.semester_id,
			ds.program_id AS student_program_id,
			ds.study_program_id AS student_study_program_id
	    ");
		
	    $this->db->join('dt_invoice_details did', 'did.invoice_id = di.invoice_id');
	    $this->db->join('dt_fee df', 'df.fee_id = did.fee_id');
	    $this->db->join('dt_personal_data dpd', 'dpd.personal_data_id = di.personal_data_id');
	    $this->db->join('dt_student ds', 'ds.personal_data_id = dpd.personal_data_id');
	    $this->db->join('dt_academic_year dtay', 'dtay.academic_year_id = ds.finance_year_id');
	    $this->db->join('ref_study_program rsp', 'rsp.study_program_id = ds.study_program_id', 'LEFT');
		$this->db->where('df.fee_amount_type', 'main');
		
		if ($s_payment_type_code) {
			$this->db->where('df.payment_type_code', $s_payment_type_code);
		}

		if ($a_clause) {
			$this->db->where($a_clause);
		}
		
		if ($s_personal_data_id) {
			$this->db->where('di.personal_data_id', $s_personal_data_id);
			// $this->db->where('ds.student_status !=', 'resign');
			$this->db->group_by('di.invoice_id');
		}else{
			$this->db->where_in('ds.student_status', ['active', 'onleave', 'inactive', 'graduated']);
		}

		$this->db->order_by('di.date_added', 'DESC');
	    $query = $this->db->get('dt_invoice di');
	    return ($query->num_rows() >= 1) ? $query->result() : false;
    }
	
	public function get_invoice_list_additional($s_invoice_id = false, $s_personal_data_id = false)
    {
	    $this->db->select("
	    	dpd.personal_data_name,
	    	ds.*,
	    	rsp.*,
	    	dtay.*,
			di.*,
			df.*,
			did.*,
			ds.program_id AS student_program_id,
			ds.study_program_id AS student_study_program_id
	    ");
	    $this->db->join('dt_invoice_details did', 'did.invoice_id = di.invoice_id');
	    $this->db->join('dt_fee df', 'df.fee_id = did.fee_id');
	    $this->db->join('dt_personal_data dpd', 'dpd.personal_data_id = di.personal_data_id');
	    $this->db->join('dt_student ds', 'ds.personal_data_id = dpd.personal_data_id');
	    $this->db->join('dt_academic_year dtay', 'dtay.academic_year_id = ds.finance_year_id');
	    $this->db->join('ref_study_program rsp', 'rsp.study_program_id = ds.study_program_id', 'LEFT');
		$this->db->where('df.fee_amount_type', 'additional');
		
		if ($s_invoice_id) {
			$this->db->where('di.invoice_id', $s_invoice_id);
		}
		
		if ($s_personal_data_id) {
			$this->db->where('di.personal_data_id', $s_personal_data_id);
		}else{
			$this->db->where_in('ds.student_status', ['active', 'onleave', 'inactive']);
		}

	    $query = $this->db->get('dt_invoice di');
	    return ($query->num_rows() >= 1) ? $query->result() : false;
    }

	public function get_unpaid_invoice_full($a_clause = false, $a_payment_type_code = false)
	{
		$this->db->select('di.*, pd.*, fee.semester_id');
		$this->db->from('dt_invoice di');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = di.personal_data_id');
		$this->db->join('dt_invoice_details id', 'id.invoice_id = di.invoice_id');
		$this->db->join('dt_fee fee', 'fee.fee_id = id.fee_id');

		if ($a_clause) {
			$this->db->where($a_clause);
		}
		if ($a_payment_type_code) {
			$this->db->where_in('fee.payment_type_code', $a_payment_type_code);
		}
		$this->db->where_in('di.invoice_status', array('created', 'pending'));

		$this->db->group_by('di.invoice_id');
		$this->db->order_by('di.date_added', 'DESC');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
    
    public function get_unpaid_invoice($a_clause = false, $mbs_allow_reminder = 'yes')
    {
		$this->db->join('dt_personal_data dpd', 'dpd.personal_data_id = di.personal_data_id');
		
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		if ($mbs_allow_reminder) {
			$this->db->where('di.invoice_allow_reminder', $mbs_allow_reminder);
		}

	    $this->db->where_in('di.invoice_status', array('created', 'pending'));
	    $query = $this->db->get('dt_invoice di');
	    return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    
    public function get_installment_payment_invoice_by_invoice_id($s_invoice_id)
    {
	    $this->db->join('dt_sub_invoice', 'invoice_id');
	    $this->db->join('dt_sub_invoice_details', 'sub_invoice_id');
		$this->db->join('bni_billing', 'trx_id');
	    $this->db->where(array(
			'sub_invoice_type' => 'installment',
			'invoice_id' => $s_invoice_id,
			'sub_invoice_details_status' => 'default'
	    ));
	    $query = $this->db->get('dt_invoice');
	    return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    
    public function get_full_payment_invoice_by_invoice_id($s_invoice_id)
    {
	    $this->db->join('dt_sub_invoice', 'invoice_id');
	    $this->db->join('dt_sub_invoice_details', 'sub_invoice_id');
		$this->db->join('bni_billing', 'trx_id');
		$this->db->where(array(
			'sub_invoice_type' => 'full',
			'invoice_id' => $s_invoice_id
	    ));
	    $query = $this->db->get('dt_invoice');
	    return ($query->num_rows() == 1) ? $query->first_row() : false;
	}
	
	public function get_invoice_installment($s_invoice_id)
    {
	    $this->db->join('dt_sub_invoice', 'invoice_id');
	    $this->db->join('dt_sub_invoice_details', 'sub_invoice_id');
	    $this->db->where(array(
			'sub_invoice_type' => 'installment',
			'invoice_id' => $s_invoice_id
		));
		$this->db->order_by('sub_invoice_details_va_number');
		$this->db->order_by('sub_invoice_details_real_datetime_deadline');
	    $query = $this->db->get('dt_invoice');
	    return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_invoice_full_payment($s_invoice_id)
    {
	    $this->db->join('dt_sub_invoice', 'invoice_id');
	    $this->db->join('dt_sub_invoice_details', 'sub_invoice_id');
		$this->db->where(array(
			'sub_invoice_type' => 'full',
			'invoice_id' => $s_invoice_id
	    ));
	    $query = $this->db->get('dt_invoice');
	    return ($query->num_rows() == 1) ? $query->first_row() : false;
	}
    
    public function get_sub_invoice_by_trx_id($s_trx_id)
    {
	    $query = $this->db->get_where('dt_sub_invoice_details', array('trx_id' => $s_trx_id));
	    return ($query->num_rows() == 1) ? $query->first_row() : false;
    }
    
    public function get_sub_invoice_data($a_clause)
    {
	    $this->db->join('dt_invoice di', 'di.invoice_id = dsi.invoice_id');
	    $this->db->order_by('dsi.sub_invoice_type', 'ASC');
	    $query = $this->db->get_where('dt_sub_invoice dsi', $a_clause);
	    return ($query->num_rows() >= 1) ? $query->result() : false;
    }

	public function get_invoice_student($a_clause = false, $a_student_status = false)
    {
		// $this->db->select('');
	    $this->db->join('dt_invoice di', 'di.invoice_id = si.invoice_id');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = di.personal_data_id');
		$this->db->join('dt_student st', 'st.personal_data_id = pd.personal_data_id', 'left');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		if ($a_student_status) {
			$this->db->where_in('st.student_status', $a_student_status);
		}
	    $query = $this->db->get();
	    return ($query->num_rows() >= 1) ? $query->result() : false;
    }

	public function get_trx_invoice_for_checking($a_clause = false, $a_student_status = false)
    {
		// $this->db->select('');
		$this->db->from('dt_sub_invoice_details did');
	    $this->db->join('dt_sub_invoice si', 'si.sub_invoice_id = did.sub_invoice_id');
	    $this->db->join('dt_invoice di', 'di.invoice_id = si.invoice_id');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = di.personal_data_id');
		$this->db->join('dt_student st', 'st.personal_data_id = pd.personal_data_id', 'left');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		if ($a_student_status) {
			$this->db->where_in('st.student_status', $a_student_status);
		}
	    $query = $this->db->get();
	    return ($query->num_rows() >= 1) ? $query->result() : false;
    }

	public function get_invoice_detail_open_payment($i_trx_id, $s_amount)
	{
		$this->db->join('dt_sub_invoice', 'invoice_id');
	    $this->db->join('dt_sub_invoice_details', 'sub_invoice_id');
	    $this->db->join('bni_billing', 'trx_id');
	    $this->db->where('trx_id', $i_trx_id);
		$this->db->where('sub_invoice_details_amount_total', $s_amount);
	    $query = $this->db->get('dt_invoice');
	    return ($query->num_rows() >= 1) ? $query->first_row() : false;
	}

	public function get_detail_invoice_by_trx_id($i_trx_id)
    {
	    $this->db->join('dt_sub_invoice', 'invoice_id');
	    $this->db->join('dt_sub_invoice_details', 'sub_invoice_id');
	    $this->db->join('bni_billing', 'trx_id');
	    // $this->db->join('dt_invoice_details', 'invoice_id');
	    // $this->db->join('dt_fee', 'fee_id');
	    // $this->db->join('ref_payment_type', 'payment_type_code', 'left');
	    $this->db->where('trx_id', $i_trx_id);
	    $query = $this->db->get('dt_invoice');
	    return ($query->num_rows() >= 1) ? $query->first_row() : false;
    }
    
    public function get_invoice_detail_by_trx_id($i_trx_id)
    {
	    $this->db->join('dt_sub_invoice', 'invoice_id');
	    $this->db->join('dt_sub_invoice_details', 'sub_invoice_id');
	    $this->db->join('bni_billing', 'trx_id');
	    $this->db->join('dt_invoice_details', 'invoice_id');
	    $this->db->join('dt_fee', 'fee_id');
	    $this->db->join('ref_payment_type', 'payment_type_code', 'left');
	    $this->db->where('trx_id', $i_trx_id);
	    $query = $this->db->get('dt_invoice');
	    return ($query->num_rows() >= 1) ? $query->first_row() : false;
    }
    
    public function update_invoice($a_invoice_data, $a_invoice_data_clause)
    {
	    $this->db->update('dt_invoice', $a_invoice_data, $a_invoice_data_clause);
    }
    
    public function update_sub_invoice($a_sub_invoice_data, $a_sub_invoice_clause)
    {
	    $this->db->update('dt_sub_invoice', $a_sub_invoice_data, $a_sub_invoice_clause);
    }
    
    public function update_sub_invoice_details($a_sub_invoice_details_data, $a_sub_invoice_details_clause)
    {
	    $this->db->update('dt_sub_invoice_details', $a_sub_invoice_details_data, $a_sub_invoice_details_clause);
    }
    
    private function _get_latest_invoice_number($s_payment_code, $s_date)
    {
	    $this->db->like('invoice_number', 'INV-'.$s_date.$s_payment_code);
	    $this->db->order_by('invoice_number', 'DESC');
	    $query = $this->db->get('dt_invoice');
	    
	    $s_invoice_counter = 1;
	    
	    if($query->num_rows() >= 1){
		    $o_invoice_data = $query->first_row();
		    list($s_code, $s_date_counter) = explode('-', $o_invoice_data->invoice_number);
		    $s_invoice_counter = (strlen($s_date_counter) <= 8) ? 1 : intval(substr($s_date_counter, -3, 3) + 1);
	    }
	    return $s_invoice_counter;
    }
    
	public function get_latest_invoice_number($s_payment_code, $s_date)
    {
	    $this->db->like('invoice_number', 'INV-'.$s_date.$s_payment_code);
	    $this->db->order_by('invoice_number', 'DESC');
	    $query = $this->db->get('dt_invoice');
	    
	    $s_invoice_counter = 1;
	    
	    if($query->num_rows() >= 1){
		    $o_invoice_data = $query->first_row();
		    list($s_code, $s_date_counter) = explode('-', $o_invoice_data->invoice_number);
		    $s_invoice_counter = (strlen($s_date_counter) <= 8) ? 1 : intval(substr($s_date_counter, -3, 3) + 1);
	    }
	    return $s_invoice_counter;
    }
    
    public function get_invoice_number($s_payment_code, $s_date = null)
	{
		if(is_null($s_date)){
			$s_date = date('ymd', time());
		}
		else{
			$s_date = date('ymd', strtotime($s_date));
		}
		
		$s_last_invoice_counter = $this->_get_latest_invoice_number($s_payment_code, $s_date);
		$s_invoice_number = 'INV-'.$s_date.$s_payment_code.str_pad($s_last_invoice_counter, 3, "0", STR_PAD_LEFT);
		return $s_invoice_number;
	}

	public function get_fee_data($a_clause = false, $a_scholarship_id = false)
	{
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		if ($a_scholarship_id) {
			$this->db->where_in('scholarship_id', $a_scholarship_id);
		}

		$query = $this->db->get('dt_fee');
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
    
    public function get_fee($a_clause)
    {
        $query = $this->db->get_where('dt_fee', $a_clause);
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    
    public function insert_invoice_details($a_invoice_details)
    {
	    $this->db->insert('dt_invoice', $a_invoice_details);
    }
    
    public function create_sub_invoice_details($a_sub_invoice_details_data)
    {
	    if(is_object($a_sub_invoice_details_data)){
		    $a_sub_invoice_details_data = (array)$a_sub_invoice_details_data;
	    }
	    
	    if(!isset($a_sub_invoice_details_data['sub_invoice_details_id'])){
		    $a_sub_invoice_details_data['sub_invoice_details_id'] = $this->uuid->v4();
	    }
	    
	    $this->db->insert('dt_sub_invoice_details', $a_sub_invoice_details_data);
	    return $a_sub_invoice_details_data['sub_invoice_details_id'];
    }
    
    public function create_sub_invoice($a_sub_invoice_data)
    {
	    if(is_object($a_sub_invoice_data)){
		    $a_sub_invoice_data = (array)$a_sub_invoice_data;
	    }
	    
	    if(!isset($a_sub_invoice_data['sub_invoice_id'])){
		    $a_sub_invoice_data['sub_invoice_id'] = $this->uuid->v4();
	    }
	    
	    $this->db->insert('dt_sub_invoice', $a_sub_invoice_data);
	    return $a_sub_invoice_data['sub_invoice_id'];
    }
    
    public function create_invoice_details($a_invoice_details_data)
    {
	    if(is_object($a_invoice_details_data)){
		    $a_invoice_details_data = (array)$a_invoice_details_data;
	    }
	    
	    $this->db->insert('dt_invoice_details', $a_invoice_details_data);
    }
    
    public function create_invoice($a_invoice_data)
    {
	    if(is_object($a_invoice_data)){
            $a_invoice_data = (array)$a_invoice_data;
        }
        
        if(!isset($a_invoice_data['invoice_id'])){
            $a_invoice_data['invoice_id'] = $this->uuid->v4();
        }
        
        $this->db->insert('dt_invoice', $a_invoice_data);
        return $a_invoice_data['invoice_id'];
	}
	
	public function get_invoice_data_like_va_number($a_clause = false, $a_like = false)
    {
	    $this->db->join('dt_sub_invoice dsi', 'dsi.sub_invoice_id = dsid.sub_invoice_id');
	    $this->db->join('dt_invoice di', 'di.invoice_id = dsi.invoice_id');
	    ($a_clause) ? $this->db->where($a_clause) : '';
	    ($a_like) ? $this->db->like($a_like) : '';
	    $this->db->order_by('dsid.sub_invoice_details_deadline', 'ASC');
	    $query = $this->db->get('dt_sub_invoice_details dsid');
	    return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function update_invoice_details($a_data, $s_invoice_id, $s_fee_id)
	{
		$this->db->update('dt_invoice_details', $a_data, [
			'invoice_id' => $s_invoice_id,
			'fee_id' => $s_fee_id
		]);

		return true;
	}

	public function remove_invoice_details($s_invoice_id, $s_fee_id)
	{
		$this->db->delete('dt_invoice_details', ['invoice_id' => $s_invoice_id, 'fee_id' => $s_fee_id]);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function remove_sub_invoice_details($s_sub_invoice_details_id)
	{
		$this->db->delete('dt_sub_invoice_details', ['sub_invoice_details_id' => $s_sub_invoice_details_id]);
		return ($this->db->affected_rows() > 0) ? true : false;
	}
	
}