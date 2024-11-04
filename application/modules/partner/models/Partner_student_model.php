<?php
class Partner_student_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_partner_period($s_partner_id)
    {
        $this->db->from('ref_partner_period pp');
        $this->db->where('pp.partner_id', $s_partner_id);
        $this->db->order_by('academic_year_id');
        $this->db->order_by('partner_period');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_partner_program($a_clause = false)
    {
        $this->db->from('ref_partner pt');
        $this->db->join('ref_program rp', 'rp.partner_id = pt.partner_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_partner_student_data($a_clause = false)
    {
        $this->db->select('*, rpp.academic_year_id AS "period_year"');
        $this->db->from('dt_student_partner sn');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = sn.personal_data_id');
        $this->db->join('ref_program_study_program psp', 'psp.program_study_program_id = sn.program_study_program_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = psp.study_program_id');
        $this->db->join('ref_partner_period rpp', 'rpp.partner_period_id = sn.partner_period_id', 'left');
        // $this->db->join('ref_program rp', 'rp.program_id = sn.partner_program_id');
        // $this->db->join('ref_partner rpn', 'rpn.partner_id = rp.partner_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $this->db->group_by('sn.student_partner_id');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function create_new_partner_student($a_student_data)
    {
        $this->db->insert('dt_student_partner', $a_student_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }
}
