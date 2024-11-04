<?php
class Job_history_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_parent_occupation($s_personal_data_id, $a_clause = false)
    {
        $this->db->from('dt_personal_data pd');
        $this->db->join('ref_ocupation oc', 'oc.ocupation_id = pd.ocupation_id', 'LEFt');
        $this->db->where('pd.personal_data_id', $s_personal_data_id);
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_job_history($s_personal_data_id, $a_clause = false)
    {
        $this->db->from('dt_academic_history dah');
        $this->db->join('ref_institution ri', 'ri.institution_id = dah.institution_id');
        $this->db->join('dt_address da', 'da.address_id = ri.address_id', 'left');
        $this->db->join('ref_country rc', 'rc.country_id = ri.country_id', 'left');
        $this->db->join('ref_ocupation ro', 'ro.ocupation_id = dah.occupation_id', 'left');
        $this->db->where('dah.academic_history_this_job', 'yes');
        $this->db->where('dah.personal_data_id', $s_personal_data_id);
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
}
