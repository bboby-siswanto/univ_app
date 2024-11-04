<?php
class Entrance_test_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_exam_question($a_clause = false)
    {
        $this->db->from('dt_exam_question eq');
        $this->db->join('dt_exam_section es', 'es.exam_section_id = eq.exam_section_id');
        $this->db->join('dt_exam_period ep', 'ep.exam_id = es.exam_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function update_time($s_exam_candidate_id, $end_time_exam)
    {
        $start_date = date('Y-m-d H:i:s');
        $date = date('Y-m-d');
        $hours_date = date('H') + 1;
        $minute_date = date('i');
        $second_date = date('s');
        $end_date = $date.' '.$hours_date.':'.$minute_date.':'.$second_date;
        if ($end_date > $end_time_exam) {
            $end_date = $end_time_exam;
        }
        $this->db->update('dt_exam_candidate', array(
            'start_time' => $start_date, 
            'end_time' => $end_date, 
            'total_question' => 90,
            'candidate_exam_status' => 'PROGRESS'
        ), array('exam_candidate_id' => $s_exam_candidate_id));
        return true;
    }

    public function remove_prev_answer($s_exam_candidate_id)
    {
        $q1 = $this->db->get_where('dt_candidate_answer', array('exam_candidate_id' => $s_exam_candidate_id));
        if ($q1->num_rows() > 0) {
            $del = $this->db->delete('dt_candidate_answer', array('exam_candidate_id' => $s_exam_candidate_id));
            return ($this->db->affected_rows() > 0) ? true : false;
        }else{
            return true;
        }
    }

    public function save_candidate_answer($a_data, $exam_candidate_id)
    {
        $this->db->insert('dt_candidate_answer', $a_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_candidate_exam($a_clause = false, $s_target = 'pmb')
    {
        if ($s_target == 'event') {
            $this->db->from('dt_exam_candidate ec');
            $this->db->join('dt_event_bookings eb', 'eb.booking_id = ec.booking_id');
            $this->db->join('dt_exam_period ep', 'ep.exam_id = ec.exam_id');
            if ($a_clause) {
                $this->db->where($a_clause);
            }
        }
        else {
            $this->db->from('dt_exam_candidate ec');
            $this->db->join('dt_student st', 'st.student_id = ec.student_id');
            $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
            $this->db->join('dt_exam_period ep', 'ep.exam_id = ec.exam_id');
            if ($a_clause) {
                $this->db->where($a_clause);
            }
        }
        
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
        // return ($q->num_rows() > 0) ? $q->result() : $this->db->last_query();
    }

    public function get_option($a_clause = false)
    {
        $this->db->from('dt_exam_question_option eqo');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('exam_question_option_number');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_question($a_clause = false)
    {
        $this->db->from('dt_exam_question eq');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('exam_question_number');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function save_candidate_exam($a_data, $a_clause = false)
    {
        if ($a_clause) {
            $this->db->update('dt_exam_candidate', $a_data, $a_clause);
            return true;
        }else{
            $this->db->insert('dt_exam_candidate', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }
}
