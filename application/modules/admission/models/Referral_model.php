<?php
class Referral_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_referral_list()
	{
		$query = $this->db->get_where('dt_personal_data dpd', [
			'dpd.is_referrer_agent' => true
		]);
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}

	public function get_reference_list($a_clause = false)
	{
		$this->db->select('dr.*, referer_dpd.personal_data_name AS "referrer_personal_data_name", referenced_dpd.personal_data_name AS "reference_personal_data_name"');
		$this->db->from('dt_reference dr');
		$this->db->join('dt_personal_data referer_dpd','referer_dpd.personal_data_id = dr.referrer_id');
		$this->db->join('dt_personal_data referenced_dpd','referenced_dpd.personal_data_id = dr.referenced_id');
		
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$q = $this->db->get();

		return ($q->num_rows() > 0) ? $q->result() : false;
	}
}