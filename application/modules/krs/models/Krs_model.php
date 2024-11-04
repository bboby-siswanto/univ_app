<?php
class Krs_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function detele_student_semester($a_clause) {
        $this->db->trans_begin();
        $this->db->delete('dt_student_semester', $a_clause);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function get_student_semester($s_student_id = false, $a_param_data = false, $a_order = false)
    {
        $this->db->select('*, stu.academic_year_id AS "batch", dss.academic_year_id, dss.semester_type_id, dss.semester_id');
        $this->db->from('dt_student_semester dss');
        $this->db->join('dt_student stu', 'dss.student_id = stu.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = stu.personal_data_id');
        $this->db->join('ref_semester rs', 'rs.semester_id = dss.semester_id');
        $this->db->join('ref_semester_type rt', 'rt.semester_type_id = dss.semester_type_id');
        if ($s_student_id) {
            $this->db->where('dss.student_id', $s_student_id);
        }
        if ($a_param_data) {
            $this->db->where($a_param_data);
        }
        if ($a_order) {
            foreach ($a_order as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_student_registration_lists($a_param_data = false)
    {
        $this->db->from('dt_student st');
        $this->db->join('dt_score sc', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        if ($a_param_data) {
            $this->db->where($a_param_data);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
}
