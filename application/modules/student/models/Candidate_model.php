<?php
class Candidate_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_candidate_data($a_clause = false)
    {
        $a_status_candidate = ['candidate','participant','pending'];
        $this->db->from('dt_student st');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->where_in('st.student_status', $a_status_candidate);

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
}
