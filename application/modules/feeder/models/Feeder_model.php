<?php
class Feeder_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$s_environment = 'production';
		if($this->session->userdata('auth')){
			$s_environment = $this->session->userdata('environment');
		}
		$this->db = $this->load->database($s_environment, true);
	}

	public function save_dikti_kategori_kegiatan($a_data, $a_clause = false)
	{
		if ($a_clause) {
			$this->db->update('dikti_kategori_kegiatan', $a_data, $a_clause);
			return true;
		}else{
			$this->db->insert('dikti_kategori_kegiatan', $a_data);

			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}

	public function save_dikti_jenis_aktivitas($a_data, $a_clause = false)
	{
		if ($a_clause) {
			$this->db->update('dikti_jenis_aktivitas', $a_data, $a_clause);
			return true;
		}else{
			$this->db->insert('dikti_jenis_aktivitas', $a_data);

			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}

	public function get_where($s_table_name, $a_clause = false)
	{
		if ($a_clause) {
			$query = $this->db->get_where($s_table_name, $a_clause);
		}else{
			$query = $this->db->get($s_table_name);
		}

		return ($query->num_rows() > 0) ? $query->result() : false;
		// return $this->db->last_query();
	}
	
	public function insert_dikti_wilayah_batch($a_data_wilayah)
	{
		$this->db->insert_batch('dikti_wilayah', $a_data_wilayah);
	}
	
	public function check_dikti_wilayah($s_id_wilayah)
	{
		$query = $this->db->get_where('dikti_wilayah', [
			'id_wilayah' => $s_id_wilayah
		]);
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}

	public function push_error_dikti($o_feeder_sync)
	{
		$q = $this->db->get_where('dikti_message', ['error_code' => $o_feeder_sync->error_code]);
		if ($q->num_rows() == 0) {
			$this->db->insert('dikti_message', ['error_code' => $o_feeder_sync->error_code, 'error_message' => $o_feeder_sync->error_desc]);

			if ($this->db->affected_rows() > 0) {
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}
	}
}