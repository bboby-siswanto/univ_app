<?php
class Employee_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->hdb = $this->load->database('hr_db', true);;
	}

	function get_department_list($a_clause = false) {
		$this->db->from('ref_department rd');
		$this->db->join('dt_employee em', 'em.employee_id = rd.employee_id', 'LEFT');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = em.personal_data_id', 'LEFT');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	function get_employee_sub_department($a_clause = false) {
		$this->db->from('dt_employee_department ed');
		$this->db->join('dt_employee em', 'em.employee_id = ed.employee_id');
		$this->db->join('ref_department dep', 'dep.department_id = ed.department_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_employee_department($a_clause = false)
	{
		$this->db->select('*, em.employee_id');
		$this->db->from('dt_employee em');
		$this->db->join('ref_department rd', 'rd.department_id = em.department_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
		// return $this->db->last_query();
	}

	public function get_employee_by_name($s_employee_name, $a_clause= false)
	{
		$this->db->join('dt_personal_data dpd', 'de.personal_data_id = dpd.personal_data_id');
		
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$this->db->like('dpd.personal_data_name', $s_employee_name);
		$query = $this->db->get('dt_employee de');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}

	public function get_employee_benefit($s_employee_id, $a_clause = false)
	{
		$this->hdb->from('dt_employee_benefit eb');
		$this->hdb->join('ref_benefit rb', 'rb.benefit_id = eb.benefit_id');
		$this->hdb->where('eb.employee_id', $s_employee_id);
		if ($a_clause) {
			$this->hdb->where($a_clause);
		}
		$query = $this->hdb->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_employee_account_bank($s_employee_id, $a_clause = false)
	{
		$this->db->from('portal_hr.dt_employee_account_bank hab');
		$this->db->join('portal_main.ref_bank mrb', 'mrb.bank_code = hab.bank_code');
		$this->db->where('hab.employee_id', $s_employee_id);
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function submit_employee_account($a_data, $a_update_clause = false)
	{
		if ($a_update_clause) {
			$this->hdb->update('dt_employee_account_bank', $a_data, $a_update_clause);
			return true;
		}
		else {
			$this->hdb->insert('dt_employee_account_bank', $a_data);
			return ($this->hdb->affected_rows() > 0) ? true : false;
		}
	}

	public function get_employee_data($clause = false)
	{
		$this->db->select('*, em.employee_id');
		$this->db->from('dt_employee em');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = em.personal_data_id');
		$this->db->join('ref_department rd', 'rd.department_id = em.department_id', 'LEFT');
		if ($clause) {
			$this->db->where($clause);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function save_employee($a_employee_data, $mbs_employee_id = false)
	{
		if(is_object($a_employee_data)){
			$a_employee_data = (array)$a_employee_data;
		}
		
		if ($mbs_employee_id) {
			$this->db->update('dt_employee', $a_employee_data, array('employee_id' => $mbs_employee_id));
			return true;
		}else{
			if(!array_key_exists('employee_id', $a_employee_data)){
				$a_employee_data['employee_id'] = $this->uuid->v4();
			}
			$this->db->insert('dt_employee', $a_employee_data);
			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}

	public function submit_employee_data($a_post_data)
	{
		if (is_array($a_post_data)) {
			$this->db->trans_begin();
			$b_is_update = true;

			if (empty($a_post_data['employee_id'])) {
				$s_personal_data_id = $this->uuid->v4();
				$s_employee_id = $this->uuid->v4();
				$b_is_update = false;
			}
			else {
				$mba_employee_data = $this->get_employee_data([
					'em.employee_id' => $s_employee_id
				]);

				if (!$mba_employee_data) {
					$b_is_update = false;
					$s_personal_data_id = $this->uuid->v4();
					$s_employee_id = $this->uuid->v4();
					$a_post_data['employee_id'] = NULL;
				}
				else {
					$s_personal_data_id = $mba_employee_data[0]->personal_data_id;
					$s_employee_id = $a_post_data['employee_id'];
				}
			}
			
			$a_personal_data = [
				'personal_data_title_prefix' => (empty($a_post_data['personal_data_title_prefix'])) ? NULL : $a_post_data['personal_data_title_prefix'],
				'personal_data_name' => $a_post_data['personal_data_name'],
				'personal_data_title_suffix' => (empty($a_post_data['personal_data_title_suffix'])) ? NULL : $a_post_data['personal_data_title_suffix'],
				'personal_data_gender' => $a_post_data['personal_data_gender'],
				'personal_data_cellular' => '0'
			];

			$a_employee_data = [
				'personal_data_id' => $s_personal_data_id,
				'employee_id_number' => $a_post_data['employee_id_number'],
				'employee_join_date' => date('Y-m-d', strtotime($a_post_data['employee_join_date'])),
				'employee_email' => $a_post_data['employee_email'],
				'employee_job_title' => $a_post_data['employee_job_title'],
				'department_id' => $a_post_data['employee_department'],
				'employment_group' => $a_post_data['employment_group'],
				'employment_status' => $a_post_data['employment_status'],
				'employee_is_lecturer' => $a_post_data['employee_is_lecturer'],
				'employee_lecturer_number' => ($a_post_data['employee_is_lecturer'] == 'YES') ? $a_post_data['employee_lecturer_number'] : NULL,
				'employee_lecturer_number_type' => ($a_post_data['employee_is_lecturer'] == 'YES') ? $a_post_data['employee_lecturer_number_type'] : NULL,
				'employee_lecturer_is_reported' => (!empty($a_post_data['employee_lecturer_number_type'])) ? 'TRUE' : 'FALSE',
				'employee_academic_rank' => (!empty($a_post_data['employee_academic_rank'])) ? $a_post_data['employee_academic_rank'] : NULL,
				'employee_homebase_status' => (!empty($a_post_data['employee_homebase_status'])) ? $a_post_data['employee_homebase_status'] : NULL,
				'employee_pkpt' => (!empty($a_post_data['employee_pkpt'])) ? $a_post_data['employee_pkpt'] : NULL,
				'employee_working_hour_status' => (!empty($a_post_data['employee_working_hour_status'])) ? $a_post_data['employee_working_hour_status'] : NULL,
			];
			$date_now = date('Y-m-d H:i:s');

			if (!$b_is_update) {
				$a_personal_data['personal_data_id'] = $s_personal_data_id;
				$a_personal_data['date_added'] = $date_now;
				$a_employee_data['employee_id'] = $s_employee_id;
				$a_employee_data['date_added'] = $date_now;

				$this->db->insert('dt_personal_data', $a_personal_data);
				$this->db->insert('dt_employee', $a_employee_data);
			}
			else {
				$this->db->update('dt_personal_data', $a_personal_data, ['personal_data_id' => $s_personal_data_id]);
				$this->db->update('dt_employee', $a_employee_data, ['employee_id' => $s_employee_id]);
			}

			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$a_return = ['code' => 2, 'message' => 'Error Processing data..'];
			}
			else {
				$this->db->trans_commit();
				$a_return = ['code' => 0, 'message' => 'Success!'];
			}
		}
		else {
			$a_return  = ['code' => 1, 'message' => 'Internal Server Error'];
		}

		return $a_return;
	}

	public function update_empoyee_param($a_employee_data, $a_param)
	{
		$this->db->update('dt_employee', $a_employee_data, $a_param);
		// return ($this->db->affected_rows() > 0) ? true : $this->db->affected_rows();
	}

	public function remove_employee($s_employee_id)
	{
		$q = $this->db->get_where('dt_employee', array('employee_id' => $s_employee_id));
		if ($q->num_rows() > 0) {
			$mba_employee_data = $q->first_row();
			$this->db->delete('dt_personal_data', array('personal_data_id' => $mba_employee_data->personal_data_id));

			return ($this->db->affected_rows() > 0) ? true : false;
		}else{
			return true;
		}
	}

	public function save_occupation($a_occupation_data, $mbs_occupation_id = false)
	{
		if(is_object($a_occupation_data)){
			$a_occupation_data = (array)$a_occupation_data;
		}
		
		if ($mbs_occupation_id) {
			$this->db->update('ref_ocupation', $a_occupation_data, array('ocupation_id' => $mbs_occupation_id));
		}else{
			$this->db->insert('ref_ocupation', $a_occupation_data);
		}

		if ($this->db->affected_rows() > 0) {
			return true;
		}else{
			return false;
		}
	}

	public function get_lecturer_by_name($s_personal_data_name = false, $b_have_nidn = false, $b_active = false)
	{
		$this->db->from('dt_employee emm');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = emm.personal_data_id');
		$this->db->where('emm.employee_is_lecturer', 'YES');
		if ($s_personal_data_name) {
			$this->db->like('pd.personal_data_name', $s_personal_data_name);
		}

		if ($b_have_nidn) {
			$this->db->where('employee_lecturer_number != ', null);
		}

		if ($b_active) {
			$this->db->where('emm.status != ', 'RESIGN');
		}
		// $this->db->limit(20, 0);
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
		// return $this->db->last_query();
	}

	public function get_occupation_by_name($s_occupation_name = false)
	{
		$this->db->select('*');
		if ($s_occupation_name) {
			$this->db->where('ocupation_name', $s_occupation_name);
		}
		$this->db->from('ref_ocupation');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	public function get_employee_data_by_email($s_email)
	{
		$query = $this->db->get_where('dt_employee', array('employee_email' => $s_email));
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}
}