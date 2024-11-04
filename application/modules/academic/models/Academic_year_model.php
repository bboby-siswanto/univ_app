<?php
class Academic_year_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_academic_year_lists($a_clause = false)
	{
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$this->db->order_by('academic_year_id', 'DESC');
		$query = $this->db->get('dt_academic_year');
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	public function get_academic_year_by_id($s_academic_year_id)
	{
		$query = $this->db->get_where('dt_academic_year', array('academic_year_id' => $s_academic_year_id));
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}
	
	public function get_semester_settings($s_academic_year_id, $s_semester_type_id)
	{
		$query = $this->db->get_where('dt_semester_settings', array('academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id));
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}
}
?>