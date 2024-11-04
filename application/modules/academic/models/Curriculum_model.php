<?php
class Curriculum_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_semester_list($s_semester_id = false, $is_curriculum = false)
	{
		$this->db->select('*');
		$this->db->from('ref_semester rs');
		$this->db->join('ref_semester_type st', 'semester_type_id');
		if ($s_semester_id) {
			$this->db->where('semester_id', $s_semester_id);
		}
		if ($is_curriculum) {
			$this->db->where('rs.semester_number <=', 12);
			$this->db->where_in('rs.semester_type_id', [1,2]);
			$this->db->or_where('semester_id', 17);
		}
		$this->db->order_by('rs.semester_id', 'ASC');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_curriculum_filtered($a_clause = false, $s_order_field = false, $sorting = 'ASC')
	{
		$this->db->select('*, rc.program_id, rc.study_program_id');
		$this->db->from('ref_curriculum rc');
		$this->db->join('ref_study_program rsp', 'study_program_id', 'left');
		if ($a_clause) {
			foreach ($a_clause as $key => $value) {
				if ($value != 'All') {
					$this->db->where($key, $value);
				}
			}
		}
		if ($s_order_field) {
			$this->db->order_by($s_order_field, $sorting);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	public function get_curriculum_list_filter($a_clause = false, $a_order = false)
	{
		$this->db->select('*, cr.program_id, cr.study_program_id');
		$this->db->from('ref_curriculum cr');
		$this->db->join('ref_study_program sp', 'sp.study_program_id = cr.study_program_id', 'left');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		if ($a_order) {
			foreach ($a_order as $key => $value) {
				$this->db->order_by($key, $value);
			}
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_curriculum_subject_filtered($a_clause = false)
	{
		$this->db->select('*, rc.study_program_id, rc.program_id');
		$this->db->from('ref_curriculum_subject rcs');
		$this->db->join('ref_curriculum rc', 'rc.curriculum_id = rcs.curriculum_id');
		$this->db->join('ref_study_program sp', 'sp.study_program_id = rc.study_program_id');
		$this->db->join('ref_subject rs','subject_id','left');
		$this->db->join('ref_subject_name rsn', 'subject_name_id');
		$this->db->join('dikti_jenis_mata_kuliah', 'id_jenis_mata_kuliah', 'left');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_curriculum_subject_data($s_curriculum_subject_id)
	{
		$this->db->from('ref_curriculum_subject rcs');
		$this->db->join('ref_subject rs','rs.subject_id = rcs.subject_id');
		$this->db->join('ref_subject_name', 'subject_name_id');
		$this->db->join('dikti_jenis_mata_kuliah', 'id_jenis_mata_kuliah', 'left');
		$this->db->where('curriculum_subject_id', $s_curriculum_subject_id);
		$query =  $this->db->get();
		return ($query->num_rows() > 0) ? $query->first_row() : false;
	}

	public function get_curriculum_subject_list($s_curriculum_id, $s_semester_id = 'All')
	{
/*
		$this->db->select('*, rcs.curriculum_subject_credit as sks');
		$this->db->from('ref_curriculum_subject rcs');
		$this->db->join('ref_subject rs', 'subject_id', 'left');
		$this->db->join('ref_study_program rsp', 'study_program_id','left');
		$this->db->join('ref_subject_name', 'subject_name_id', 'left');
		$this->db->join('ref_semester', 'semester_id', 'left');
		$this->db->join('dikti_jenis_mata_kuliah', 'id_jenis_mata_kuliah', 'left');
		$this->db->where('curriculum_id', $s_curriculum_id);
		if ($s_semester_id != 'All') {
			$this->db->where('rcs.semester_id', $s_semester_id);
		}
		$this->db->order_by('semester_id', 'asc');
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : false;
*/
		
		$this->db->select('*, rcs.curriculum_subject_credit as sks, rc.program_id, rc.study_program_id');
		$this->db->from('ref_curriculum_subject rcs');
		$this->db->join('ref_curriculum rc', 'rc.curriculum_id = rcs.curriculum_id');
		$this->db->join('ref_study_program rsp', 'rsp.study_program_id = rc.study_program_id');
		$this->db->join('ref_subject rs', 'subject_id', 'left');
		$this->db->join('ref_subject_name', 'subject_name_id', 'left');
		$this->db->join('ref_semester', 'semester_id', 'left');
		$this->db->join('dikti_jenis_mata_kuliah', 'id_jenis_mata_kuliah', 'left');
		$this->db->where('rc.curriculum_id', $s_curriculum_id);
		if ($s_semester_id != 'All') {
			$this->db->where('rcs.semester_id', $s_semester_id);
		}
		$this->db->order_by('semester_id', 'asc');
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : false;
		
/*
		$this->db->select('*, rcs.curriculum_subject_credit as sks');
		$this->db->join('ref_curriculum rc', 'rc.curriculum_id = rcs.curriculum_id');
		$this->db->join('ref_study_program rsp', 'rsp.study_program_id = rc.study_program_id');
		$this->db->join('ref_subject rs', 'rs.subject_id = rcs.subject_id');
		$this->db->join('ref_subject_name rsn', 'rsn.subject_name_id = rs.subject_name_id');
		$this->db->join('ref_semester rsm', 'rsm.semester_id = rcs.semester_id');
		$this->db->join('dikti_jenis_mata_kuliah djmk', 'djmk.id_jenis_mata_kuliah = rs.id_jenis_mata_kuliah');
		$this->db->where('rc.curriculum_id', $s_curriculum_id);
		if($s_semester_id != 'All'){
			$this->db->where('rcs.semester_id', $s_semester_id);
		}
		$query = $this->db->get('ref_curriculum_subject rcs');
		return ($query->num_rows() > 0) ? $query->result() : false;
*/
	}

	public function get_curriculum_semester_filtered($a_clause = false)
	{
		$this->db->select('*');
		$this->db->from('ref_curriculum_semester rcs');
		$this->db->join('ref_semester rs', 'semester_id', 'left');
		$this->db->order_by('rs.semester_id', 'ASC');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_curriculum_semester($s_curriculum_id, $s_semester_id = false)
	{
		$this->db->select('*');
		$this->db->from('ref_curriculum_semester rcs');
		$this->db->join('ref_semester rs', 'semester_id', 'left');
		$this->db->where('curriculum_id', $s_curriculum_id);
		$this->db->order_by('rs.semester_id', 'ASC');
		if ($s_semester_id) {
			$this->db->where('semester_id', $s_semester_id);
		}
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_curriculum_lists($a_clause = false)
	{
		$this->db->select('*, rc.program_id, rc.study_program_id');
		$this->db->from('ref_curriculum rc');
		$this->db->join('ref_program pr', 'program_id');
		$this->db->join('ref_study_program rsp', 'study_program_id', 'left');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	public function get_score_data($s_academic_year_id)
	{
		$db = $this->load->database('portal', true);
		
		$db->select('*');
		$db->from('score_absence sa');
		$db->join('implemented_subject impsub', 'impsub.id = sa.implemented_subject_id');
		$db->join('curriculum_subject cs', 'cs.id = impsub.curriculum_subject_id');
		$db->join('subject sub', 'sub.id = cs.subject_id');
		$db->join('subject_name sn', 'sn.id = sub.subject_name_id');
		$db->join('curriculum c', 'c.id = cs.curriculum_id');
		$db->where('sa.academic_year_id', $s_academic_year_id);
		$query = $db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}

	public function get_curriculum_data($s_curriculum_id = false)
	{
		$this->db->select('*, rc.program_id, rc.study_program_id');
		$this->db->from('ref_curriculum rc');
		$this->db->join('ref_study_program rsp', 'study_program_id','left');
		if ($s_curriculum_id) {
			$this->db->where('curriculum_id', $s_curriculum_id);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function update_curriculum_subject_semester($a_curriculum_subject_data_semester, $a_clause)
	{
		$result = $this->db->update('ref_curriculum_subject', $a_curriculum_subject_data_semester, $a_clause);
		return ($result) ? true : $this->db->error();
	}
	
	public function update_curriculum_subject($a_curriculum_subject_data, $s_curriculum_subject_id)
	{
		$this->db->update('ref_curriculum_subject', $a_curriculum_subject_data, array('curriculum_subject_id' => $s_curriculum_subject_id));
	}
	
	public function create_new_curriculum_subject($a_curriculum_subject_data)
	{
		if(is_object($a_curriculum_subject_data)){
			$a_curriculum_subject_data = (array)$a_curriculum_subject_data;
		}
		
		if(!array_key_exists('curriculum_subject_id', $a_curriculum_subject_data)){
			$a_curriculum_subject_data['curriculum_subject_id'] = $this->uuid->v4();
		}
		
		$this->db->insert('ref_curriculum_subject', $a_curriculum_subject_data);
		return $a_curriculum_subject_data['curriculum_subject_id'];
	}
	
	public function update_curriculum_semester($a_curriculum_semester_data, $a_clause)
	{
		$this->db->update('ref_curriculum_semester', $a_curriculum_semester_data, $a_clause);
		return ($this->db->affected_rows() > 0) ? true : false;
	}
	
	public function create_new_subject($a_subject_data)
	{
		if(is_object($a_subject_data)){
			$a_subject_data = (array)$a_subject_data;
		}
		
		if(!array_key_exists('subject_id', $a_subject_data)){
			$a_subject_data['subject_id'] = $this->uuid->v4();
		}
		
		$this->db->insert('ref_subject', $a_subject_data);
		return $a_subject_data['subject_id'];
	}
	
	public function create_new_subject_name($a_subject_name_data)
	{
		if(is_object($a_subject_name_data)){
			$a_subject_name_data = (array)$a_subject_name_data;
		}
		
		if(!array_key_exists('subject_name_id', $a_subject_name_data)){
			$a_subject_name_data['subject_name_id'] = $this->uuid->v4();
		}
		
		$this->db->insert('ref_subject_name', $a_subject_name_data);
		return $a_subject_name_data['subject_name_id'];
	}
	
	public function create_new_curriculum_semester($a_curriculum_semester_data)
	{	
		$this->db->insert('ref_curriculum_semester', $a_curriculum_semester_data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}else{
			return false;
		}
	}
	
	public function update_curriculum($a_curriculum_data, $s_curriculum_id)
	{
		$this->db->update('ref_curriculum', $a_curriculum_data, array('curriculum_id' => $s_curriculum_id));
	}
	
	public function create_new_curriculum($a_curriculum_data)
	{
		if(is_object($a_curriculum_data)){
			$a_curriculum_data = (array)$a_curriculum_data;
		}
		
		if(!array_key_exists('curriculum_id', $a_curriculum_data)){
			$a_curriculum_data['curriculum_id'] = $this->uuid->v4();
		}
		
		$this->db->insert('ref_curriculum', $a_curriculum_data);
		
		return $a_curriculum_data['curriculum_id'];
	}
	
	public function update_curriculum_data($a_curriculum_data, $s_curriculum_id)
	{
		$this->db->update('ref_curriculum', $a_curriculum_data, array('curriculum_id' => $s_curriculum_id));
	}

	public function delete_curriculum($s_curriculum_id)
	{
		$this->db->delete('ref_curriculum', array('curriculum_id' => $s_curriculum_id));
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function delete_curriculum_subject($a_clause = false)
	{
		if ($a_clause) {
			$this->db->delete('ref_curriculum_subject', $a_clause);

			if ($this->db->affected_rows() > 0) {
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function update_credit_curriculum($s_curriculum_id)
	{
		$this->db->select_sum('curriculum_semester_total_credit_mandatory');
		$this->db->select_sum('curriculum_semester_total_credit_mandatory_fixed');
		$this->db->select_sum('curriculum_semester_total_credit_elective');
		$this->db->select_sum('curriculum_semester_total_credit_elective_fixed');
		$this->db->select_sum('curriculum_semester_total_credit_extracurricular');
		$this->db->select_sum('curriculum_semester_total_credit_extracurricular_fixed');
		$query_curriculum_semester = $this->db->get_where('ref_curriculum_semester', array('curriculum_id' => $s_curriculum_id))->row();

		$a_curriculum_credit_data = array(
			'curriculum_total_credit_mandatory' => $query_curriculum_semester->curriculum_semester_total_credit_mandatory,
			'curriculum_total_credit_mandatory_fixed' => $query_curriculum_semester->curriculum_semester_total_credit_mandatory_fixed,
			'curriculum_total_credit_elective' => $query_curriculum_semester->curriculum_semester_total_credit_elective,
			'curriculum_total_credit_elective_fixed' => $query_curriculum_semester->curriculum_semester_total_credit_elective_fixed,
			'curriculum_total_credit_extracurricular' => $query_curriculum_semester->curriculum_semester_total_credit_extracurricular,
			'curriculum_total_credit_extracurricular_fixed' => $query_curriculum_semester->curriculum_semester_total_credit_extracurricular_fixed
		);

		$this->db->update('ref_curriculum', $a_curriculum_credit_data, array('curriculum_id' => $s_curriculum_id));
		return true;
	}

	public function update_credit_count_curriculum_semester($s_curriculum_id, $s_semester_id, $s_semester_type)
	{
		$this->db->select_sum('curriculum_subject_credit');
		$query_curriculum_subject_type = $this->db->get_where('ref_curriculum_subject', array('curriculum_id' => $s_curriculum_id, 'semester_id' => $s_semester_id, 'curriculum_subject_type' => $s_semester_type))->row();

		$a_curriculum_semester_credit_data = array(
			'curriculum_semester_total_credit_'.$s_semester_type => $query_curriculum_subject_type->curriculum_subject_credit
		);

		$this->db->update('ref_curriculum_semester', $a_curriculum_semester_credit_data, array('curriculum_id' => $s_curriculum_id, 'semester_id' => $s_semester_id));
		return true;
	}

	public function get_remains_credit_curriculum_semester($s_curriculum_id, $s_semester_id, $semester_type)
	{
		$this->db->select('(curriculum_semester_total_credit_'.$semester_type.'_fixed - curriculum_semester_total_credit_'.$semester_type.') AS remains_credit');
		$this->db->from('ref_curriculum_semester rcs');
		$this->db->where(array('curriculum_id' => $s_curriculum_id, 'semester_id' => $s_semester_id));
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->row() : false;
	}
}