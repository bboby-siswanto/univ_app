<?php
class Api_core_model extends CI_Model
{
	private $dbforge;
	private $db;
	
	public function __construct()
	{
		parent::__construct();
	}

	public function update_result_sync($a_api_result, $i_return_code)
	{
		$a_data = array(
			'pmb_sync' => strval($i_return_code)
		);

		$this->db->trans_start();
		foreach ($a_api_result as $list) {
			if (isset($list['condition']) AND $list['condition'] != null) {
				$this->db->update($list['table_name'], $a_data, $list['condition']);
			}else if ((isset($list['clause'])) AND ($list['clause'] != null)) {
				$this->db->update($list['table_name'], $a_data, $list['clause']);
			}
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return false;
		}else{
			$this->db->trans_commit();
			return true;
		}
	}

	public function get_prepare_data($s_table_name, $a_condition = null)
	{
		$a_list_field_blocked = array('pmb_sync', 'academic_history_main');

		$a_field = $this->db->list_fields($s_table_name);
		foreach ($a_field as $field) {
			if (!in_array($field, $a_list_field_blocked)) {
				$this->db->select($field);
			}
		}

		$this->db->from($s_table_name);

		if ($a_condition != null) {
			$this->db->where($a_condition);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->first_row() : false;
	}

	public function save_table_api($s_table, $a_data, $a_condition = null)
	{
		if ($a_condition != null) {
			$this->db->update($s_table, $a_data, $a_condition);
		}else{
			$this->db->insert($s_table, $a_data);
			if ($s_table == "dt_personal_data") {
				$directory_file = APPPATH.'uploads/'.$a_data['personal_data_id'].'/';
				if(!file_exists($directory_file)){
					mkdir($directory_file, 0755);
				}
			}
		}

		if ($this->db->affected_rows() > 0) {
			return TRUE;
		}else{
			return FALSE;
		}
	}

	public function get_row_data($s_table, $a_condition, $a_data = false)
	{
		$this->db->select('*');
		$this->db->from($s_table);
		$this->db->where($a_condition);
		if ($a_data) {
			if ($a_data['timestamp'] === TRUE) {
				$this->db->where('timestamp < ', $a_data['timestamp']);
			}
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->first_row() : FALSE;
	}

	public function check_data($s_table, $a_condition, $a_data = false)
	{
		$this->db->select('*');
		$this->db->from($s_table);
		$this->db->where($a_condition);
		if ($a_data) {
			if ($a_data['timestamp'] === TRUE) {
				$this->db->where('timestamp < ', $a_data['timestamp']);
			}
		}
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? TRUE : FALSE;
	}
	
	public function get_api_data_by_access_token($s_access_token)
	{
		$o_query = $this->db->get_where('dt_api', array('api_access_token' => $s_access_token));
		return ($o_query->num_rows() == 1) ? $o_query->first_row() : false;
	}
}