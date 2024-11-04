<?php
class Activity_study_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_activity_lecturer($a_clause = false)
    {
        $this->db->select('*, das.program_id, das.study_program_id, al.feeder_sync AS "activity_lecturer_sync"');
        $this->db->from('dt_activity_lecturer al');
        $this->db->join('dt_employee em', 'em.employee_id = al.employee_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = em.personal_data_id');
        $this->db->join('dikti_kategori_kegiatan dkk', 'dkk.id_kategori_kegiatan = al.id_kategori_kegiatan');
        $this->db->join('dt_activity_study das', 'das.activity_study_id = al.activity_study_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function delete_activity_student($s_activity_student_id)
    {
        $this->db->delete('dt_activity_student', ['activity_student_id' => $s_activity_student_id]);
        return ($this->db->affected_rows() > 0) ? true : false;
    }
    
    public function delete_activity_lecturer($s_activity_lecturer_id)
    {
        $this->db->delete('dt_activity_lecturer', ['activity_lecturer_id' => $s_activity_lecturer_id]);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function save_student_activity($a_data, $mbs_activity_student_id = false)
    {
        if ($mbs_activity_student_id) {
            $this->db->update('dt_activity_student', $a_data, ['activity_student_id' => $mbs_activity_student_id]);
            return true;
        }else{
            $this->db->insert('dt_activity_student', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }

    }
    
    public function save_lecturer_activity($a_data, $mbs_activity_lecturer_id = false)
    {
        if ($mbs_activity_lecturer_id) {
            $this->db->update('dt_activity_lecturer', $a_data, ['activity_lecturer_id' => $mbs_activity_lecturer_id]);
            return true;
        }else{
            $this->db->insert('dt_activity_lecturer', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
        
    }

    public function save_activity_study($a_data, $mbs_activity_study_id = false)
    {
        if ($mbs_activity_study_id) {
            $this->db->update('dt_activity_study', $a_data, ['activity_study_id' => $mbs_activity_study_id]);
            return true;
        }else{
            $this->db->insert('dt_activity_study', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_activity_student_data($a_clause = false)
    {
        $this->db->select('*, st.program_id, st.study_program_id, dast.feeder_sync AS "student_activity_sync"');
        $this->db->from('dt_activity_student dast');
        $this->db->join('dt_student st', 'st.student_id = dast.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_activity_data($a_clause = false)
    {
        $this->db->select('*, das.program_id, das.study_program_id');
        $this->db->from('dt_activity_study das');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = das.study_program_id');
        $this->db->join('ref_semester_type st', 'st.semester_type_id = das.semester_type_id');
        $this->db->join('dikti_jenis_aktivitas dja', 'dja.id_jenis_aktivitas_mahasiswa = das.id_jenis_aktivitas_mahasiswa');
        $this->db->join('dikti_message dm', 'dm.error_code = das.feeder_sync', 'left');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_dikti_jenis_aktivitas($a_clause = false)
    {
        if ($a_clause) {
            $q = $this->db->get_where('dikti_jenis_aktivitas', $a_clause);
        }else{
            $q = $this->db->get('dikti_jenis_aktivitas');
        }

        return ($q->num_rows() > 0) ? $q->result() : false;
    }
    
    public function get_dikti_kategori_kegiatan($a_clause = false)
    {
        if ($a_clause) {
            $q = $this->db->get_where('dikti_kategori_kegiatan', $a_clause);
        }else{
            $q = $this->db->get('dikti_kategori_kegiatan');
        }

        return ($q->num_rows() > 0) ? $q->result() : false;
    }
}
