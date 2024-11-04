<?php
class Family_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_family_by_id($s_family_id)
	{
		$query = $this->db->get_where('dt_family', array('family_id' => $s_family_id));
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}

	public function get_family_lists_filtered($a_clause = false)
	{
		$this->db->select('*, fmm.personal_data_id as personal_data_id_family');
		$this->db->from('dt_family fm');
		$this->db->join('dt_family_member fmm', 'fm.family_id = fmm.family_id');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = fmm.personal_data_id');
		$this->db->join('ref_ocupation ro', 'ro.ocupation_id = pd.ocupation_id', 'left');
		$this->db->join('dt_academic_history dah', 'dah.personal_data_id = fmm.personal_data_id', 'left');
		$this->db->join('ref_institution ri', 'ri.institution_id = dah.institution_id', 'left');

		if ($a_clause) {
			$this->db->where($a_clause);
		}
		
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	public function delete_family($s_family_id)
	{
		$this->db->delete('dt_family', array('family_id' => $s_family_id));
	}

	public function delete_family_member($a_clause_delete)
	{
		$this->db->delete('dt_family_member', $a_clause_delete);
		return ($this->db->affected_rows() > 0) ? true : false;
	}
	
	public function get_family_members($s_family_id, $a_clause)
	{
		$a_family_id = array(
			'family_id' => $s_family_id
		);
		$a_db_clause = array_merge($a_family_id, $a_clause);
		
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = dfm.personal_data_id');
		$this->db->where($a_db_clause);
		$query = $this->db->get('dt_family_member dfm');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_family_by_personal_data_id($s_personal_data_id)
	{
		$query = $this->db->get_where('dt_family_member', array('personal_data_id' => $s_personal_data_id));
		return ($query->num_rows() >= 1) ? $query->first_row() : false;
	}
	
	public function add_family_member($a_family_member_data)
	{
		$this->db->insert('dt_family_member', $a_family_member_data);
	}

	public function update_family_member($a_family_member_data, $a_clause_family)
	{
		$this->db->update('dt_family_member', $a_family_member_data, $a_clause_family);
	}
	
	public function create_family($a_family_data = null)
	{
		if(!is_null($a_family_data)){
			if(is_object($a_family_data)){
				$a_family_data = (array)$a_family_data;
			}
			
			if($a_family_data['family_id'] === null) {
				$a_family_data['family_id'] = $this->uuid->v4();
			}
		}
		else{
			$a_family_data = array(
				'family_id' => $this->uuid->v4(),
				'date_added' => date('Y-m-d H:i:s')
			);
		}
		
		$query_check = $this->db->get_where('dt_family', ['family_id' => $a_family_data['family_id']]);
		if ($query_check->num_rows() > 0) {
			$query_result = $query_check->first_row();
			return $query_result->family_id;
		}
		else {
			$this->db->insert('dt_family', $a_family_data);
			return $a_family_data['family_id'];
		}
	}
}