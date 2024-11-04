<?php
class Entrance_test_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_participant_answer($a_clause = false)
    {
        $this->db->from('dt_candidate_answer ca');
        $this->db->join('dt_exam_question_option aqo', 'aqo.question_option_id = ca.question_option_id');
        $this->db->join('dt_exam_question eq', 'eq.exam_question_id = ca.exam_question_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        
        $this->db->order_by('aqo.exam_question_option_number');
        $q = $this->db->get();
        // return $this->db->last_query();
        return ($q->num_rows() > 0) ? $q->result() : false;
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

    public function save_exam_period($a_period_exam_data, $s_period_exam_id = false)
    {
        if ($s_period_exam_id) {
            $q = $this->db->get_where('dt_exam_period', array('exam_id' => $s_period_exam_id));
            if ($q->num_rows() > 0) {
                $this->db->update('dt_exam_period', $a_period_exam_data, array('exam_id' => $s_period_exam_id));
                return true;
            }else{
                return false;
            }
        }else{
            $this->db->insert('dt_exam_period', $a_period_exam_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
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

    public function get_question_option($a_clause = false)
    {
        $this->db->from('dt_exam_question_option');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('exam_question_id', 'ASC');
        $this->db->order_by('exam_question_option_number', 'ASC');
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

    public function save_candidate_answer($a_data, $a_clause_checker)
    {
        $mba_answer_data = $this->db->get_where('dt_candidate_answer', $a_clause_checker);
        if ($mba_answer_data->num_rows() > 0) {
            $this->db->update('dt_candidate_answer', $a_data, $a_clause_checker);
            return true;
        }
        else{
            $this->db->insert('dt_candidate_answer', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function reset_data($s_exam_candidate_id)
    {
        $this->db->trans_start();
        $this->db->update('dt_exam_candidate', array(
            'start_time' => NULL,
            'end_time' => NULL,
            'total_time' => NULL,
            'correct_answer' => NULL,
            'wrong_answer' => NULL,
            'filled_question' => NULL,
            'candidate_exam_status' => 'PENDING',
        ), array('exam_candidate_id' => $s_exam_candidate_id));

        $this->db->delete('dt_exam_candidate_section', array('exam_candidate_id' => $s_exam_candidate_id));
        $this->db->delete('dt_candidate_answer', array('exam_candidate_id' => $s_exam_candidate_id));
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }

    public function save_candidate_section($a_data, $a_clause_update = false)
    {
        if ($a_clause_update) {
            $this->db->update('dt_exam_candidate_section', $a_data, $a_clause_update);
            return true;
        }else{
            $this->db->insert('dt_exam_candidate_section', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_exam_part($a_clause = false)
    {
        $this->db->from('dt_exam_question eq');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->group_by('eq.exam_question_part');
        $this->db->order_by('eq.exam_section_id');
        $this->db->order_by('eq.exam_question_part');
        $this->db->order_by('eq.exam_question_number');

        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_candidate_section($a_clause = false)
    {
        $this->db->from('dt_exam_candidate_section ecs');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('ecs.exam_section_id');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_section($a_clause = false)
    {
        $this->db->from('dt_exam_section es');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('es.exam_section_id');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_candidate_event($a_clause = false)
    {
        $this->db->select('*, eb.booking_name AS "personal_data_name", eb.booking_email AS "personal_data_email"');
        $this->db->from('dt_exam_candidate ec');
        $this->db->join('dt_event_bookings eb', 'eb.booking_id = ec.booking_id');
        $this->db->join('dt_exam_period ep', 'ep.exam_id = ec.exam_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
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

    public function save_question($a_data, $a_clause_update = false)
    {
        if ($a_clause_update) {
            $this->db->update('dt_exam_question', $a_data, $a_clause_update);
            return true;
        }else{
            $this->db->insert('dt_exam_question', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_exam_question($a_clause = false)
    {
        $this->db->from('dt_exam_question');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('exam_section_id');
        $this->db->order_by('exam_question_number');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_period_exam($a_clause = false)
    {
        $this->db->from('dt_exam_period');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    function clean_html($s_padd_left, $s_string) {
        $result = $s_string;
        if (substr($s_string, 0, 3) == '<p>') {
            $s_string = substr($s_string, 3);
            $result = '<p>'.$s_padd_left.'. '.$s_string;
        }
        else {
            $result = $s_padd_left.'. '.$s_string;
        }

        return $result;
    }
}
