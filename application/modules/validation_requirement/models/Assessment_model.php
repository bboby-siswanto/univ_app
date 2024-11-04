<?php
class Assessment_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->assesdb = $this->load->database('assessment_db', true);
    }

    public function get_assessment_result($a_clause = false, $b_grouping_user = false)
    {
        $this->db->from('portal_assessment.dt_question_result qr');
        $this->db->join('portal_main.dt_personal_data pd', 'pd.personal_data_id = qr.personal_data_id');
        $this->db->join('portal_assessment.dt_assessment_option ao', 'ao.assessment_option_id = qr.assessment_option_id', 'LEFT');
        $this->db->join('portal_assessment.dt_question dq', 'dq.question_id = qr.question_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($b_grouping_user) {
            $this->db->group_by('qr.personal_data_id');
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    
    }
    public function get_result($a_clause = false, $b_grouping_student = false, $a_clause_in = false)
    {
        $this->db->select('*, st.academic_year_id student_batch');
        $this->db->from('portal_assessment.dt_question_result qr');
        $this->db->join('portal_main.dt_personal_data pd', 'pd.personal_data_id = qr.personal_data_id');
        $this->db->join('portal_main.dt_student st', 'st.personal_data_id = pd.personal_data_id');
        $this->db->join('portal_main.ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('portal_assessment.dt_assessment_option ao', 'ao.assessment_option_id = qr.assessment_option_id', 'LEFT');
        $this->db->join('portal_assessment.dt_question dq', 'dq.question_id = qr.question_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($b_grouping_student) {
            $this->db->group_by('st.student_id');
        }
        if ($a_clause_in) {
            foreach ($a_clause_in as $key => $a_value) {
                $this->db->where_in($key, $a_value);
            }
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function remove_result($s_personal_data_id, $s_assessment_id)
    {
        $this->assesdb->delete('dt_question_result', ['personal_data_id' => $s_personal_data_id, 'assessment_id' => $s_assessment_id]);
        return true;
    }

    public function submit_result($a_data)
    {
        $this->assesdb->insert('dt_question_result', $a_data);
        return ($this->assesdb->affected_rows() > 0) ? true : false;
    }

    public function get_option_list($s_assessment_id, $a_clause = false)
    {
        $this->assesdb->from('dt_assessment_option qo');
        if ($a_clause) {
            $this->assesdb->where($a_clause);
        }
        $this->assesdb->where('qo.assessment_id', $s_assessment_id);
        $this->assesdb->order_by('option_value', 'DESC');
        $query = $this->assesdb->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_question_list($s_assessment_id, $a_clause = false)
    {
        $this->assesdb->from('dt_question dq');
        if ($a_clause) {
            $this->assesdb->where($a_clause);
        }
        $this->assesdb->where('dq.assessment_id', $s_assessment_id);
        $this->assesdb->order_by('question_number');
        $query = $this->assesdb->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    function get_list_assessment($a_clause = false) {
        $this->assesdb->select('ass.*');
        $this->assesdb->from('dt_assessment ass');
        $this->assesdb->join('dt_question qs', 'qs.assessment_id = ass.assessment_id');

        if ($a_clause) {
            $this->assesdb->where($a_clause);
        }
        
        $this->assesdb->group_by('ass.assessment_id');
        $query = $this->assesdb->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
}