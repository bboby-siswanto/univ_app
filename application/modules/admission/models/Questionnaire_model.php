<?php
class Questionnaire_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_question_answers_student($a_clause = false, $b_count = false)
	{
		$this->db->from('dt_questionnaire_answers dqa');
		$this->db->join('dt_personal_data dpd', 'dpd.personal_data_id = dqa.personal_data_id');
		$this->db->join('dt_student st', 'st.personal_data_id = dpd.personal_data_id', 'LEFT');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		
		if ($b_count) {
			return $this->db->count_all_results();
		}else{
			$query = $this->db->get();
			return ($query->num_rows() >= 1) ? $query->result() : false;
		}
	}
	
	public function get_question_answers($a_clause)
	{
		$this->db->join('dt_personal_data dpd', 'dpd.personal_data_id = dqa.personal_data_id');
		$query = $this->db->get_where('dt_questionnaire_answers dqa', $a_clause);
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_count_question_answers($i_question_id, $i_section_id)
	{
		$this->db->where(['question_id' => $i_question_id, 'question_section_id' => $i_section_id]);
		$this->db->from('dt_questionnaire_answers');
		return $this->db->count_all_results();
	}
	
	public function get_questions($a_clause = false)
	{
		$this->db->join('dt_question_section_group dqsc', 'dqsc.question_id = rq.question_id');
		if($a_clause){
			$this->db->where($a_clause);
		}
		$query = $this->db->get('ref_questions rq');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_question_sections($a_clause = false)
	{
		if($a_clause){
			$this->db->where($a_clause);
		}
		$query = $this->db->get('ref_question_sections');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
}