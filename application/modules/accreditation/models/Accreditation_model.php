<?php
class Accreditation_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_student_list($a_student_batch, $a_student_prodi, $a_student_status)
    {
        $this->db->select('*, st.academic_year_id AS "student_batch"');
        $this->db->from('dt_student st');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');

        $this->db->where_in('st.study_program_id', $a_student_prodi);
        $this->db->where_in('st.academic_year_id', $a_student_batch);
        $this->db->where_in('st.student_status', $a_student_status);

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
        // return $this->db->last_query();
    }

    public function get_class_master($a_clause = false, $a_semester_type_in = false, $a_study_program_id = false)
    {
        $this->db->select('*, cm.academic_year_id as running_year, cm.semester_type_id as class_semester_type_id');
        $this->db->from('dt_class_master cm');
        $this->db->join('dt_class_master_class cmc', 'cmc.class_master_id = cm.class_master_id');
        $this->db->join('ref_semester_type smt', 'smt.semester_type_id = cm.semester_type_id');
        $this->db->join('dt_class_group cg', 'cg.class_group_id = cmc.class_group_id');
        $this->db->join('dt_class_group_subject cgs', 'cgs.class_group_id = cg.class_group_id');
        $this->db->join('dt_offered_subject ofs', 'ofs.offered_subject_id = cgs.offered_subject_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = ofs.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        $this->db->join('dt_class_master_lecturer cml', 'cml.class_master_id = cm.class_master_id','LEFT');
        $this->db->join('ref_program pr', 'pr.program_id = ofs.program_id', 'LEFT');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_semester_type_in) {
            $this->db->where_in('cm.semester_type_id', [1,2]);
        }
        if ($a_study_program_id) {
            $this->db->where_in('ofs.study_program_id', $a_study_program_id);
        }

        $this->db->group_by('cm.class_master_id');
        $this->db->order_by('cm.academic_year_id, cm.semester_type_id');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_list_lecturer_class($s_academic_year_id = false, $a_semester_type_in = false)
    {
        $this->db->from('dt_class_master');
    }
}
