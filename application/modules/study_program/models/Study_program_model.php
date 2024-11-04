<?php
class Study_program_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_faculty_data($a_clause = false)
	{
		$this->db->from('ref_faculty');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$this->db->order_by('faculty_name');
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function get_study_program_instititute($a_clause = false)
    {
        $this->db->from('ref_program_study_program psp');
        $this->db->join('ref_program rp', 'rp.program_id = psp.program_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = psp.study_program_id');
        $this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
		$this->db->group_by('sp.study_program_id');
		$this->db->order_by('fc.faculty_name', 'asc');
		$this->db->order_by('sp.study_program_name', 'asc');

        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
	}
	
	public function get_study_program_by_abbreviation($s_abbreviation)
	{
		$query = $this->db->get_where('ref_study_program', array(
			'study_program_abbreviation' => $s_abbreviation
		));
		
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}
	
	public function get_study_program_child($s_study_program_id)
	{
		$query = $this->db->get_where('ref_study_program', array('study_program_main_id' => $s_study_program_id));
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_latest_id_number($s_finance_year_id, $s_program_id, $s_study_program_id)
	{
		$mba_study_program = $this->get_study_program($s_study_program_id, false);
		$s_study_program_main_id = $mba_study_program[0]->study_program_main_id;
		
		$a_study_program_id = array();
		
		if(!is_null($s_study_program_main_id)){
			$mba_child_study_program = $this->get_study_program_child($s_study_program_main_id);
			foreach($mba_child_study_program as $o_study_program){
				array_push($a_study_program_id, $o_study_program->study_program_id);
			}
		}
		else{
			array_push($a_study_program_id, $s_study_program_id);
		}
		
		$this->db->select_max("student_number");
		$this->db->from('dt_student ds');
		$this->db->join('ref_study_program rsp', 'rsp.study_program_id = ds.study_program_id');
		$this->db->where(array(
			'ds.finance_year_id' => $s_finance_year_id,
			'ds.program_id' => $s_program_id
		));
		$this->db->where_in('rsp.study_program_id', $a_study_program_id);
		$query = $this->db->get();
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}

	public function get_study_program_lists($a_clause = false)
	{
		$this->db->from('ref_program_study_program rpsp');
		$this->db->join('ref_study_program rsp', 'study_program_id');
		$this->db->join('ref_faculty rf', 'faculty_id');
		$this->db->join('ref_program rp', 'program_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$this->db->order_by('rf.faculty_name', 'asc');
		$this->db->order_by('rsp.study_program_name', 'asc');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_program_lists_select($a_clause = false)
	{
		$this->db->from('ref_program');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$this->db->where('program_main_id', null);
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_program($a_clause = false, $a_program_id = false)
	{
		$this->db->from('ref_program');

		if ($a_clause) {
			$this->db->where($a_clause);
		}

		if ($a_program_id) {
			$this->db->where_in('program_id', $a_program_id);
		}

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	public function get_study_program($s_study_program_id = false, $b_show_active = true)
	{
		if($s_study_program_id){
			$this->db->where('study_program_id', $s_study_program_id);
		}
		
		if($b_show_active){
			$this->db->where('study_program_is_active', 'yes');
		}
		
		$this->db->join('ref_faculty fc', 'fc.faculty_id = rsp.faculty_id');
		$this->db->order_by('fc.faculty_name', 'asc');
		$this->db->order_by('rsp.study_program_name', 'asc');
		$o_query = $this->db->get('ref_study_program rsp');
		return ($o_query->num_rows() >= 1) ? $o_query->result() : false;
	}
	
	public function insert_study_program($a_data)
	{
		if(!array_key_exists('study_program_id', $a_data)){
			$uuid = $this->uuid->v4();
			$a_data['study_program_id'] = $uuid;
		}
		else{
			$uuid = $a_data['study_program_id'];
		}
		
		if($this->db->insert('ref_study_program', $a_data)){
			return $uuid;
		}
		else{
			$a_db_error = $this->db->error();
			return $a_db_error['message'];
		}
	}
}