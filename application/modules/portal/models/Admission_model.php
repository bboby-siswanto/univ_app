<?php
class Admission_model extends CI_Model
{
    function __construct()
    {
		parent::__construct();
    }

    public function retrieve_data($s_table_name, $a_clause = false)
	{
		if($a_clause){
			$query = $this->pda->get_where($s_table_name, $a_clause);
		}
		else{
			$query = $this->pda->get($s_table_name);
		}

		return ($query->num_rows() >= 1) ? $query->result() : false;
	}

	public function retrieve_data_like($s_table_name, $a_clause = false)
	{
		if($a_clause){
			$query = $this->pda->like($a_clause);
		}
		$query = $this->pda->get($s_table_name);
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}

	public function retrieve_group_data($s_table_name, $s_group_field_name)
	{
		$this->pda->from($s_table_name);
		$this->pda->group_by($s_group_field_name);
		$query = $this->pda->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}

	public function get_score_sync($s_student_id, $s_subject_name, $s_academic_year_id, $s_semester_type_id, $s_approval)
	{
		$this->db->select('*, sc.portal_id');
		$this->db->from('dt_score sc');
		$this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = sc.curriculum_subject_id');
		$this->db->join("ref_subject sb", 'sb.subject_id = cs.subject_id');
		$this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
		$this->db->where('sc.student_id', $s_student_id);
		$this->db->where('sc.academic_year_id', $s_academic_year_id);
		$this->db->where('sc.semester_type_id', $s_semester_type_id);
		$this->db->where('sn.subject_name', $s_subject_name);
		$this->db->where('sc.score_approval', $s_approval);
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function get_ordering_subject_delivered()
	{
		$this->pda->from('dt_class_subject_delivered');
		$this->pda->order_by('class_master_id');
		$this->pda->order_by('date(subject_delivered_time_start)', 'ASC');
		$this->pda->order_by('time(subject_delivered_time_start)', 'DESC');
		$query = $this->pda->get();
		return $query->result();
	}

	public function remove_staging_data($s_table_name, $a_clause)
	{
		$this->pda->delete($s_table_name, $a_clause);
		return ($this->pda->affected_rows() > 0) ? true : false;
	}
}
