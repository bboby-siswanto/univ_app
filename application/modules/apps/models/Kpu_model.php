<?php
class Kpu_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function submit_voting($a_vote_data, $a_paslon_update, $s_paslon_id)
    {
        $this->db->trans_begin();

        $this->db->insert('vote_voting', $a_vote_data);
        $this->db->update('vote_paslon', $a_paslon_update, ['paslon_id' => $s_paslon_id]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function update_period($a_data, $s_period_id)
    {
        $this->db->update('vote_period', $a_data, ['period_id' => $s_period_id]);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function save_paslon($a_data, $s_paslon_id = false)
    {
        if ($s_paslon_id) {
            $this->db->update('vote_paslon', $a_data, ['paslon_id' => $s_paslon_id]);
            return true;
        }
        else {
            $this->db->insert('vote_paslon', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    function get_paslon($a_clause = false)
    {
        $this->db->select('vp.*, st_chairman.study_program_id AS "chairman_prodi_id", st_vice_chairman.study_program_id AS "vice_chairman_prodi_id",
            pd_chairman.personal_data_name AS "chairman_personal_name", pd_vice_chairman.personal_data_name AS "vice_chairman_personal_name"');
        $this->db->from('vote_paslon vp');
        $this->db->join('dt_student st_chairman', 'st_chairman.student_id = vp.paslon_chairman', 'LEFT');
        $this->db->join('dt_personal_data pd_chairman', 'pd_chairman.personal_data_id = st_chairman.personal_data_id', 'LEFT');
        $this->db->join('dt_student st_vice_chairman', 'st_vice_chairman.student_id = paslon_vice_chairman', 'LEFT');
        $this->db->join('dt_personal_data pd_vice_chairman', 'pd_vice_chairman.personal_data_id = st_vice_chairman.personal_data_id', 'LEFT');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('nomor_urut');
        $this->db->order_by('paslon_id');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_student($a_clause = false)
    {
        $this->db->select('st.student_id, pd.personal_data_name, st.academic_year_id, sp.study_program_abbreviation');
        $this->db->from('dt_student st');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
}
