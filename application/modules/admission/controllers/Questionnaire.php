<?php
class Questionnaire extends App_core
{	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('admission/Questionnaire_model', 'Qm');
	}
	
	public function question_list()
	{
		$this->a_page_data['status_lists'] = $this->General->get_enum_values('dt_student', 'student_status');
		$this->a_page_data['batch'] = $this->General->get_batch();
		$this->a_page_data['question_sections'] = $this->Qm->get_question_sections();
		$this->a_page_data['body'] = $this->load->view('question_list', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function get_answers()
	{
		if($this->input->is_ajax_request()){
			$i_question_section_id = $this->input->post('question_section_id');
			$i_question_id = $this->input->post('question_id');
			$s_academic_year_id = $this->input->post('academic_year_id');
			$s_student_status = $this->input->post('student_status');

			$a_clause_answer = [
				'dqa.question_id' => $this->input->post('question_id'),
				'dqa.question_section_id' => $this->input->post('question_section_id'),
				'st.academic_year_id' => $this->input->post('academic_year_id'),
				'st.student_status' => $this->input->post('student_status')
			];

			foreach ($a_clause_answer as $key => $a_clause) {
				if ($a_clause == '') {
					unset($a_clause_answer[$key]);
				}
			}
			
			$mba_queation_answers = $this->Qm->get_question_answers_student($a_clause_answer);
			
			print json_encode(['data' => $mba_queation_answers]);
			exit;
		}
	}
	
	public function get_questions()
	{
		if($this->input->is_ajax_request()){
			$i_question_section_id = $this->input->post('question_section_id');
			$s_academic_year_id = $this->input->post('academic_year_id');
			$s_student_status = $this->input->post('student_status');

			$mba_questions = $this->Qm->get_questions(['question_section_id' => $i_question_section_id]);
			if($mba_questions){
				$a_clause_answer = [
					'dqa.question_section_id' => $i_question_section_id,
					'st.academic_year_id' => $s_academic_year_id,
					'st.student_status' => $s_student_status
				];

				if ($s_academic_year_id == '') {
					unset($a_clause_answer['st.academic_year_id']);
				}
				if ($s_student_status == '') {
					unset($a_clause_answer['st.student_status']);
				}

				foreach($mba_questions as $o_mba_question){
					$a_clause_answer['dqa.question_id'] = $o_mba_question->question_id;
					$o_mba_question->total_answers = $this->Qm->get_question_answers_student($a_clause_answer, true);
					// $o_mba_question->total_answers = $this->Qm->get_count_question_answers($o_mba_question->question_id, $i_question_section_id);
				}
				// var_dump($a_clause_answer);exit;
			}
			print json_encode(['code' => 0, 'data' => $mba_questions]);
			exit;
		}
	}
}