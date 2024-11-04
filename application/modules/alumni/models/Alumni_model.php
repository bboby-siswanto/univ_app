<?php
class Alumni_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_survey_answer($a_clause = false)
    {
        $this->db->select('dqa.*, dq.*, dqc.*, pd.*, ri.*, da.*, oc.ocupation_name, dqa.timestamp AS "answer_timestamp", ds.program_id, ds.study_program_id,
            pda.personal_data_name AS "alumni_name", pda.personal_data_title_prefix AS "alumni_title_prefix",
            pda.personal_data_title_suffix AS "alumni_title_suffix", sp.study_program_name, sp.study_program_abbreviation, ds.academic_year_id AS "student_batch"');
		$this->db->from('dikti_question_answers dqa');
        $this->db->join('dikti_questions dq', 'dq.question_id = dqa.question_id');
        $this->db->join('dikti_question_choices dqc', 'dqc.question_choice_id = dqa.question_section_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = dqa.personal_data_id');
        $this->db->join('dt_institution_contact ic', 'ic.personal_data_id = dqa.personal_data_id');
        $this->db->join('ref_institution ri', 'ri.institution_id = ic.institution_id');
        $this->db->join('ref_ocupation oc', 'oc.ocupation_id = pd.ocupation_id', 'LEFT');
        $this->db->join('dt_address da', 'da.address_id = ri.address_id', 'LEFT');
        $this->db->join('dt_student ds', 'ds.student_id = dqa.student_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = ds.study_program_id');
        // $this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id');
        $this->db->join('dt_personal_data pda', 'pda.personal_data_id = ds.personal_data_id');
        // $this->db->join('ref_ocupation ocs', 'ocs.ocupation_id = pda.ocupation_id', 'LEFT');
        // // $this->db->join('ref_study_program rsp', 'rsp.study_program_id = ds.study_program_id');
        // // $this->db->join('ref_faculty rf', 'rf.faculty_id = rsp.faculty_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->group_by('dqa.personal_data_id');
        $this->db->order_by('dqa.date_added', 'ASC');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_alumni_answer_lists($a_clause = false, $a_prodi_in = false, $a_graduate_year_in = false)
    {
        $this->db->select('*, dqa.timestamp AS "answer_timestamp", ds.program_id, ds.study_program_id');
		$this->db->from('dikti_question_answers dqa');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = dqa.personal_data_id');
        $this->db->join('dt_student ds', 'ds.personal_data_id = pd.personal_data_id');
        $this->db->join('ref_study_program rsp', 'rsp.study_program_id = ds.study_program_id');
        $this->db->join('ref_faculty rf', 'rf.faculty_id = rsp.faculty_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        if ($a_prodi_in) {
            $this->db->where_in('ds.study_program_id', $a_prodi_in);
        }
        if ($a_graduate_year_in) {
            $this->db->where_in('ds.graduated_year_id', $a_graduate_year_in);
        }
        $this->db->group_by('dqa.personal_data_id');
        $this->db->order_by('dqa.date_added', 'ASC');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_question_answer($a_clause = false)
    {
        $this->db->from('dikti_questions dq');
        $this->db->join('dikti_question_answers dqa','dqa.question_id = dq.question_id');
        $this->db->join('dikti_question_choices dqc', 'dqc.question_choice_id = dqa.question_section_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('dq.question_number');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_user_answer($s_personal_data_id, $a_clause = false)
    {
        $this->db->from('dikti_question_answers dqa');
        $this->db->where('personal_data_id', $s_personal_data_id);

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_dikti_question_choice($a_clause = false)
    {
        $this->db->from('dikti_question_choices dqc');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $this->db->order_by('question_choices_order', 'ASC');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_dikti_choice_answer($s_personal_data_id, $a_clause = false)
    {
        $this->db->from('dikti_question_answers qw');
        $this->db->join('dikti_question_choices qc', 'qc.question_choice_id = qw.question_section_id');
        $this->db->where('qw.personal_data_id', $s_personal_data_id);
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_dikti_question($a_clause = false, $s_question_type = 'alumni')
    {
        $this->db->from('dikti_questions dq');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->where('question_type', $s_question_type);

        // $this->db->cast('question_number AS int');
        $this->db->order_by('ABS(question_number)', 'ASC');

        $q = $this->db->get();
        return  ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function remove_personal_data_dikti_tracer_study($s_personal_data_id)
    {
        $this->db->delete('dikti_question_answers', array(
            'personal_data_id' => $s_personal_data_id
        ));

        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function submit_dikti_survey_batch($a_batch_data) {
        $this->db->insert_batch('dikti_question_answers', $a_batch_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function submit_dikti_tracer_study($a_data)
    {
        $this->db->insert('dikti_question_answers', $a_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_question_list($s_question_type = 'alumni', $s_personal_data_id = false)
    {
        $mba_question = $this->get_dikti_question(array(
            'parent_question_id' => NULL
        ), $s_question_type);

        if ($mba_question) {
            foreach ($mba_question as $o_question) {
                $mba_have_child_question = $this->get_dikti_question(array(
                    'parent_question_id' => $o_question->question_id
                ), $s_question_type);
                $bq_have_description = false;

                if ($mba_have_child_question) {
                    foreach ($mba_have_child_question as $o_child_questions) {
                        $mba_question_child_choice = $this->get_dikti_question_choice(array(
                            'question_id' => $o_child_questions->question_id
                        ));
                        $bc_have_description = false;

                        if ($mba_question_child_choice) {
                            foreach ($mba_question_child_choice as $o_child_choice) {
                                $o_child_choice->answer_data = false;
                                if ($o_child_choice->question_choice_description == 'TRUE') {
                                    $bc_have_description = true;
                                }
                                
                                if ($s_personal_data_id) {
                                    $mbo_question_choice_answer = $this->get_dikti_choice_answer($s_personal_data_id, [
                                        'question_section_id' => $o_child_choice->question_choice_id
                                    ]);
                                    
                                    $o_child_choice->answer_data = ($mbo_question_choice_answer) ? $mbo_question_choice_answer[0] : false;
                                }
                            }
                        }
    
                        $o_child_questions->have_description = $bc_have_description;
                        $o_child_questions->question_choices = $mba_question_child_choice;
                    }
                    $o_question_choices = false;
                }else{
                    $mba_question_choice = $this->get_dikti_question_choice(array(
                        'question_id' => $o_question->question_id
                    ));

                    if ($mba_question_choice) {
                        foreach ($mba_question_choice as $o_child_choice) {
                            $o_child_choice->answer_data = false;
                            if ($o_child_choice->question_choice_description == 'TRUE') {
                                $bq_have_description = true;
                            }
                            if ($s_personal_data_id) {
                                $mbo_question_choice_answer = $this->get_dikti_choice_answer($s_personal_data_id, [
                                    'question_section_id' => $o_child_choice->question_choice_id
                                ]);
                                
                                $o_child_choice->answer_data = ($mbo_question_choice_answer) ? $mbo_question_choice_answer[0] : false;
                            }
                        }
                    }

                    $o_question_choices = $mba_question_choice;
                }
                
                $o_question->have_description = $bq_have_description;
                $o_question->question_child = $mba_have_child_question;
                $o_question->question_choices = $o_question_choices;
            }
        }

        return $mba_question;
    }
}
