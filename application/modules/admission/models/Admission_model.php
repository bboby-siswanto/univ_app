<?php
class Admission_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_registration_scholarship($a_clause = false)
	{
		$this->db->from('dt_registration_scholarship rs');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function submit_registration_sholarship($a_data, $a_update_clause = false)
	{
		if ($a_update_clause) {
			$this->db->update('dt_registration_scholarship', $a_data, $a_update_clause);
			return true;
		}else{
			$this->db->insert('dt_registration_scholarship', $a_data);
			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}
	
	public function questionnaire_answers($s_personal_data_id, $i_section_id)
	{
		$query = $this->db->get_where('dt_questionnaire_answers dqa', 
			array(
				'question_section_id' => $i_section_id, 
				'personal_data_id' => $s_personal_data_id
			)
		);
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function update_academic_year_data($s_academic_year_id, $a_academic_year_data)
	{
		$this->db->update('dt_academic_year', $a_academic_year_data, array('academic_year_id' => $s_academic_year_id));
	}
	
	public function insert_questionnaire($a_quetionnaire_data)
	{
		$this->db->insert('dt_questionnaire_answers', $a_quetionnaire_data);
	}
	
	public function get_candidate_student($s_academic_year_id = null, $a_clause = false)
	{
		$this->db->select("
			stu.student_id,
			pd.personal_data_name,
			pd.personal_data_email,
			pd.personal_data_phone,
			pd.personal_data_cellular,
			stu.program_id,
			stu.study_program_id,
			sp.study_program_name,
			sp.study_program_ni_name,
			f.faculty_name
		");
		$this->db->from('dt_student stu');
		$this->db->join('dt_personal_data pd', 'personal_data_id');
		$this->db->join('ref_study_program sp', 'study_program_id', 'LEFT');
		$this->db->join('ref_faculty f', 'faculty_id', 'LEFT');
		if(!is_null($s_academic_year_id)){
			$this->db->where('stu.academic_year_id', $s_academic_year_id);
		}

		if ($a_clause) {
			$this->db->where($a_clause);
		}
		// $this->db->where('stu.student_enrollment_status', )
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_active_intake_year()
	{
		$query = $this->db->get_where('dt_academic_year', array('academic_year_intake_status' => 'active'));
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}

	public function scholarship_type($a_clause = false)
	{
		$this->db->from('ref_scholarship');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$this->db->where('scholarship_fee_type !=', 'additional');
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}

	public function get_scholarship_student($a_clause = false)
	{
		$this->db->from('ref_scholarship rs');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$this->db->where_not_in('scholarship_id', 'c7423c02-0192-11eb-909d-5254005d90f6'); // academic semester scholarship
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
}