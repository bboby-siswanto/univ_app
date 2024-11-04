<?php
class Hris_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
		$this->hrm = $this->load->database('hr_db', true);
    }

	public function get_department($a_clause = false)
	{
		$this->db->select('*, rd.department_id');
		$this->db->from('ref_department rd');
		$this->db->join('dt_employee em', 'em.employee_id = rd.employee_id', 'LEFT');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = em.personal_data_id', 'LEFT');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_log_absence($a_clause = false)
	{
		$this->hrm->select('log.*');
		$this->hrm->from('dt_hid_log log');
		$this->hrm->join('dt_hid hid', 'hid.hid_id = log.hid_id', 'left');

		if ($a_clause) {
			$this->hrm->where($a_clause);
		}
		$this->hrm->order_by('log.date_added', 'DESC');
		$query = $this->hrm->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function submit_attendace($a_data, $a_update_clause = false)
	{
		if ($a_update_clause) {
			$this->hrm->update('dt_hid_log', $a_data, $a_update_clause);
			return true;
		}
		else {
			$this->hrm->insert('dt_hid_log', $a_data);
			return ($this->hrm->affected_rows() > 0) ? true : false;
		}
	}

	public function get_where_hr($s_table_name, $a_clause = false)
	{
		if ($a_clause) {
			$query = $this->hrm->get_where($s_table_name, $a_clause);
		}else{
			$query = $this->hrm->get($s_table_name);
		}

		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_hid_data($s_hid_key)
	{
		$query_hr = $this->hrm->get_where('dt_hid', ['hid_key' => $s_hid_key]);
		if ($query_hr->num_rows() > 0) {
			$o_hr_data = $query_hr->first_row();
			$query_personal_data = $this->db->get_where('dt_personal_data', ['personal_data_id' => $o_hr_data->personal_data_id]);
			if ($query_personal_data->num_rows() > 0) {
				$o_personal_data = $query_personal_data->first_row();
				$o_return_data = $this->merge_object([$o_hr_data, $o_personal_data]);
				// $o_return_data = array_merge((array) $o_hr_data, (array) $o_personal_data);
				return $o_return_data;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	public function get_employee_hid($a_clause = false)
	{
		$q_hid = $this->hrm->get('dt_hid');
		if ($q_hid->num_rows() > 0) {
			$a_personal_data_id = [];
			foreach ($q_hid->result() as $o_hid) {
				if (!in_array($o_hid->personal_data_id, $a_personal_data_id)) {
					array_push($a_personal_data_id, $o_hid->personal_data_id);
				}
			}

			if (count($a_personal_data_id) > 0) {
				$this->db->select('em.*, pd.*, dp.department_name, dp.department_abbreviation');
				$this->db->from('dt_employee em');
				$this->db->join('dt_personal_data pd', 'pd.personal_data_id = em.personal_data_id');
				$this->db->join('ref_department dp', 'dp.department_id = em.department_id', 'left');
				$this->db->where_in('em.personal_data_id', $a_personal_data_id);
				if ($a_clause) {
					$this->db->where($a_clause);
				}

				$query = $this->db->get();
				return ($query->num_rows() > 0) ? $query->result() : false;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

    public function get_employee_data($clause = false)
	{
		$this->db->select('em.*, pd.*, dp.department_name, dp.department_abbreviation');
		$this->db->from('dt_employee em');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = em.personal_data_id');
		$this->db->join('ref_department dp', 'dp.department_id = em.department_id', 'left');
		$this->db->join('ref_religion rr', 'rr.religion_id = pd.religion_id', 'LEFT');
		if ($clause) {
			$this->db->where($clause);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function submit_hid($a_data, $s_hid_id = false)
	{
		if ($s_hid_id) {
			$this->hrm->update('dt_hid', $a_data, ['hid_id' => $s_hid_id]);
			return true;
		}
		else {
			$this->hrm->insert('dt_hid', $a_data);
			return ($this->hrm->affected_rows() > 0) ? true : false;
		}
	}

	public function get_employee_department($a_clause = false, $mba_department_id = false)
	{
		$this->db->from('dt_employee em');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = em.personal_data_id');
		$this->db->join('ref_department rd', 'rd.department_id = em.department_id');

		if ($a_clause) {
			$this->db->where($a_clause);
		}

		if ($mba_department_id) {
			$this->db->where_in($mba_department_id);
		}

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function merge_object($a_array_list)
	{
		$a_array_return = [];
		if (count($a_array_list) > 0) {
			foreach ($a_array_list as $o_array) {
				$a_array_target = $o_array;
				if (is_object($o_array)) {
					$a_array_target = (array) $o_array;
				}

				$a_array_return = array_merge($a_array_return, $a_array_target);
			}
		}

		return (object) $a_array_return;
	}

	public function get_last_nip($s_status, $s_group, $s_joindate)
	{
		$s_year_join_date = date('Y', strtotime($s_joindate));
		$this->db->from('dt_employee');
		$this->db->where('employment_group', $s_group);
		$this->db->where('employment_status', $s_status);
		$this->db->where('year(employee_join_date)', $s_year_join_date);
		$this->db->order_by('employee_join_date', 'DESC');

		$query = $this->db->get();

		$s_status_code = ($s_status == 'PERMANENT') ? 1 : 2;
		$s_group_code = ($s_group == 'ACADEMIC') ? 1 : 2;
		
		$s_nipformat = $s_status_code.$s_group_code.substr($s_year_join_date, 2, 2);
		if ($query->num_rows() > 0) {
			$o_result_data = $query->first_row();
			$s_nip = $o_result_data->employee_id_number;

			$zz = substr($s_nip, 5, 2);
			$zz++;
			$zz = str_pad('0', 2, $zz, STR_PAD_RIGHT);
			$s_new_nip = $s_nipformat.$zz;
		}
		else {
			$zz = '01';
			$s_new_nip = $s_nipformat.$zz;
		}

		return (['code' => 0, 'nip' => $s_new_nip]);
	}
}
