<?php
class Institution_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_student_institution($a_clause = false)
	{
		$this->db->from('dt_student st');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
		$this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
		$this->db->join('dt_academic_history ah', 'ah.personal_data_id = pd.personal_data_id', 'left');
		$this->db->join('ref_institution ri', 'ri.institution_id = ah.institution_id', 'left');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$this->db->group_by('st.student_id');

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
		// return ($query->num_rows() > 0) ? $query->result() : $this->db->last_query();
	}

	public function get_institution_contact($a_clause = false)
	{
		$this->db->from('dt_institution_contact ic');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = ic.personal_data_id');
		$this->db->join('ref_institution ri', 'ri.institution_id = ic.institution_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function save_institution_contact($s_institution_id, $s_personal_data_id, $a_clause_update = false)
	{
		if ($a_clause_update) {
			$this->db->update('dt_institution_contact', ['personal_data_id' => $s_personal_data_id], $a_clause_update);
			
			return true;
		}else{
			$this->db->insert('dt_institution_contact', [
				'institution_id' => $s_institution_id,
				'personal_data_id' => $s_personal_data_id
			]);

			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}

	public function get_institution_data($a_clause = false, $s_institution_name = false, $b_limit = false)
	{
		$this->db->join('dt_address da', 'da.address_id = ri.address_id', 'LEFT');
		$this->db->join('ref_country rc', 'rc.country_id = ri.country_id', 'LEFT');
		
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		
		if ($s_institution_name) {
			$this->db->like('ri.institution_name', $s_institution_name);
		}

		if ($b_limit) {
			$this->db->limit(10, 0);
		}

		$q = $this->db->get('ref_institution ri');
		return ($q->num_rows() > 0) ? $q->result(): false;
	}

	public function insert_academic_history($a_academic_history_data, $s_academic_history_id = null)
	{
		if ($s_academic_history_id == null) {
			if (!array_key_exists('academic_history_id', $a_academic_history_data)) {
				$a_academic_history_data['academic_history_id'] = $this->uuid->v4();
			}

			$this->db->insert('dt_academic_history', $a_academic_history_data);
		}else{
			$this->db->update('dt_academic_history', $a_academic_history_data, array('academic_history_id' => $s_academic_history_id));
			$a_academic_history_data['academic_history_id'] = $s_academic_history_id;
		}

		if ($this->db->affected_rows() > 0) {
			return $a_academic_history_data['academic_history_id'];
		}else{
			return false;
		}
	}

	public function get_institution_by_id($s_institution_id)
	{
		$this->db->select('*');
		$this->db->from('ref_institution ri');
		$this->db->join('dt_address da', 'da.address_id = ri.address_id','left');
		$this->db->join('ref_country rc', 'rc.country_id = da.country_id', 'left');
		$this->db->where('ri.institution_id', $s_institution_id);
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->first_row() : false;
	}

	public function insert_address($a_address_data, $s_address_id = null)
	{
		if ($s_address_id == null) {
			if (!array_key_exists('address_id', $a_address_data)) {
				$a_address_data['address_id'] = $this->uuid->v4();
			}

			$this->db->insert('dt_address', $a_address_data);
		}else{
			$this->db->update('dt_address', $a_address_data, array('address_id' => $s_address_id));
			$a_address_data['address_id'] = $s_address_id;
		}
		return $a_address_data['address_id'];
	}
	
	public function insert_institution($a_institution_data, $s_institution_id = null)
	{
		if ($s_institution_id == null) {
			if(!array_key_exists('institution_id', $a_institution_data)){
				$a_institution_data['institution_id'] = $this->uuid->v4();
			}
			
			$this->db->insert('ref_institution', $a_institution_data);
		}else{
			$this->db->update('ref_institution', $a_institution_data, array('institution_id' => $s_institution_id));
			$a_institution_data['institution_id'] = $s_institution_id;
		}

		return ($a_institution_data['institution_id']);
	}

	public function insert_occupation($a_occupation_data)
	{
		$this->db->insert('ref_ocupation', $a_occupation_data);
		return ($this->db->afected_rows() > 0) ? true : false;
	}

	public function occupation_suggestions($s_occupation_name = false, $b_exact = false)
	{
		$this->db->from('ref_ocupation ro');
		if($s_occupation_name){
			if($b_exact){
				$this->db->where('ro.ocupation_name', $s_occupation_name);
			}
			else{
				$this->db->like('ro.ocupation_name', $s_occupation_name);
			}
		}
		$query = $this->db->get();

		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function institution_datatable($a_datatables_post)
	{
		$i_start = $a_datatables_post['start'];
		$i_length = $a_datatables_post['length'];
		$a_columns = $a_datatables_post['columns'];
		$a_order = $a_datatables_post['order'];
		$s_search = $a_datatables_post['search']['value'];
		
		$s_field = $a_columns[$a_order[0]['column']]['data'];
		$s_direction = $a_order[0]['dir'];
		
		$this->db->join('dt_address da','da.address_id = ri.address_id','left');
		$this->db->join('ref_country rc','rc.country_id = da.country_id','left');
		
		if($s_search != ''){
			$iteration = 0;
			foreach($a_columns as $a_column_data){
				if($iteration == 0){
					$this->db->like('ri.'.$a_column_data['data'], $s_search);
				}
				else{
					$this->db->or_like('ri.'.$a_column_data['data'], $s_search);
				}
				$iteration++;
			}
		}
		$this->db->order_by($s_field, $s_direction);
		$this->db->limit($i_length, $i_start);
		$query = $this->db->get('ref_institution ri');
		
		return ($query->num_rows() >= 1) ? [
			'result' => $query->result(),
			'num_rows' => $this->db->count_all('ref_institution'),
			'total_records' => $this->db->count_all('ref_institution')
		] : false;
	}
	
	public function institution_suggestions($s_institution_name = false, $a_institution_type = false, $b_exact = false)
	{
		$this->db->select('*');
		$this->db->from('ref_institution ri');
		$this->db->join('dt_address da','da.address_id = ri.address_id','left');
		$this->db->join('ref_country rc','rc.country_id = da.country_id','left');
		
		if($a_institution_type){
			$this->db->where_in('ri.institution_type', $a_institution_type);
		}
		
		if($s_institution_name){
			if($b_exact){
				$this->db->where('ri.institution_name', $s_institution_name);
			}
			else{
				$this->db->like('ri.institution_name', $s_institution_name);
			}
		}
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function save_occupation($a_ocupation_data, $s_occupation_id = false)
	{
		if (is_object($a_ocupation_data)) {
			$a_ocupation_data = (array)$a_ocupation_data;
		}

		if ($s_occupation_id) {
			$this->db->update('ref_ocupation', $a_ocupation_data, array('ocupation_id' => $s_occupation_id));
		}else{
			if (!array_key_exists('ocupation_id', $a_ocupation_data)) {
				$a_ocupation_data['ocupation_id'] = $this->uuid->v4();
			}

			$this->db->insert('ref_ocupation', $a_ocupation_data);
		}

		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function get_occupation_sugestion($s_occupation_name = false, $b_exact = false)
	{
		$this->db->from('ref_ocupation ro');
		if ($s_occupation_name) {
			if ($b_exact) {
				$this->db->where('ro.ocupation_name', $s_occupation_name);
			}else{
				$this->db->like('ro.ocupation_name', $s_occupation_name);
			}
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
}