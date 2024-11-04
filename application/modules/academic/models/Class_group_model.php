<?php
class Class_group_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_sign_class_master($a_filter_data = false) {
        $this->db->from('dt_class_master_class cmc');
        $this->db->join('dt_class_group cg', 'cg.class_group_id = cmc.class_group_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = cg.sign_personal_data_id');
        $this->db->join('dt_employee em', 'em.personal_data_id = pd.personal_data_id');
        
        if ($a_filter_data) {
            $this->db->where($a_filter_data);
        }
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_absence_student_detail($a_clause = false, $a_student_prodi_in = false) {
        $this->db->from('dt_absence_student das');
        $this->db->join('dt_score sc', 'sc.score_id = das.score_id');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_student_prodi_in) {
            $this->db->where_in('st.study_program_id', $a_student_prodi_in);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_id_from_absence($s_subject_delivered_id, $a_clause = false)
    {
        $this->db->from('dt_absence_student das');
        $this->db->join('dt_score sc', 'sc.score_id = das.score_id');
        $this->db->where('das.subject_delivered_id', $s_subject_delivered_id);
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->group_by('sc.class_group_id');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }
    
    public function get_class_subject_delivered($a_clause)
    {
        $this->db->order_by('subject_delivered_time_start');
	    $query = $this->db->get_where('dt_class_subject_delivered', $a_clause);
	    return ($query->num_rows() >= 1) ? $query->result() : false;
    }

    public function save_student_absence($a_absence_student_data, $a_clause = false)
    {
        if ($a_clause) {
            $this->db->update('dt_absence_student', $a_absence_student_data, $a_clause);
            return true;
        }else{
            if (is_object($a_absence_student_data)) {
                $a_absence_student_data = (array)$a_absence_student_data;
            }

            if (!in_array('absence_student_id', $a_absence_student_data)) {
                $a_absence_student_data['absence_student_id'] = $this->uuid->v4();
            }

            $this->db->insert('dt_absence_student', $a_absence_student_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_class_master_group($a_clause = false)
    {
        $this->db->from('dt_class_master_class cmc');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function save_class_master_lect_data($a_class_master_lect_data, $a_clause_update = false)
    {
        if ($a_clause_update) {
            $this->db->update('dt_class_master_lecturer', $a_class_master_lect_data, $a_clause_update);
            return true;
        }else {
            $this->db->insert('dt_class_master_lecturer', $a_class_master_lect_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_class_master_data($s_class_master_id = false, $a_param = false)
    {
        $this->db->from('dt_class_master');
        if ($s_class_master_id) {
            $this->db->where('class_master_id', $s_class_master_id);
        }
        
        if ($a_param) {
            $this->db->where($a_param);
        }

        $this->db->order_by('class_master_name', 'ASC');

        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_master_data_limit($s_class_master_id = false, $a_param = false, $l_start = 0, $l_total = 0)
    {
        $this->db->from('dt_class_master');
        if ($s_class_master_id) {
            $this->db->where('class_master_id', $s_class_master_id);
        }
        
        if ($a_param) {
            $this->db->where($a_param);
        }

        $this->db->order_by('class_master_name', 'ASC');
        $this->db->limit($l_total, $l_start);

        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function save_class_mastering($a_class_master_data, $a_clause_update = false)
    {
        if ($a_clause_update) {
            $this->db->update('dt_class_master', $a_class_master_data, $a_clause_update);
            return true;
        }else {
            $this->db->insert('dt_class_master', $a_class_master_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function save_class_master_class($a_class_master_data, $a_param_update = false)
    {
        if ($a_param_update) {
            $this->db->update('dt_class_master_class', $a_class_master_data, $a_param_update);
            return true;
        }else {
            $this->db->insert('dt_class_master_class', $a_class_master_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_unit_subject_delivered($s_class_master_id = false, $a_clause = false, $s_order_time = 'DESC')
    {
        $this->db->select('*, cgsm.date_added AS "input_date"');
        $this->db->from('dt_class_subject_delivered cgsm');
        $this->db->join('dt_employee em', 'em.employee_id = cgsm.employee_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = em.personal_data_id');
        if ($s_class_master_id) {
            $this->db->where('class_master_id', $s_class_master_id);
        }

        $this->db->order_by('cgsm.subject_delivered_time_start', $s_order_time);
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function remove_class_master_lect_sync($s_class_master_id)
    {
        $this->db->delete('dt_class_master_lecturer', array('class_master_id' => $s_class_master_id));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function delete_class_master_lecturer($a_clause = false)
    {
        $this->db->delete('dt_class_master_lecturer', $a_clause);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_unit_subject_class_filtered($a_clause = false, $mbs_clause_in = false)
    {
        $this->db->from('dt_class_subject_delivered cgd');
        $this->db->join('dt_employee em', 'em.employee_id = cgd.employee_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = em.personal_data_id');
        if ($a_clause) {
            if ($mbs_clause_in) {
                $this->db->where_in($mbs_clause_in, $a_clause);
            }else{
                $this->db->where($a_clause);
            }
        }
        $this->db->order_by('cgd.subject_delivered_time_start', 'DESC');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
    
    public function save_subject_delivered($a_class_subject_delivered_data, $s_subject_delivered_id = false)
    {
        if ($s_subject_delivered_id) {
            $this->db->update('dt_class_subject_delivered', $a_class_subject_delivered_data, array('subject_delivered_id' => $s_subject_delivered_id));
            return true;
        }else{
            if (is_object($a_class_subject_delivered_data)) {
                $a_class_subject_delivered_data = (array)$a_class_subject_delivered_data;
            }
            if (!array_key_exists('subject_delivered_id', $a_class_subject_delivered_data)) {
                $a_class_subject_delivered_data['subject_delivered_id'] = $this->uuid->v4();
            }
            $this->db->insert('dt_class_subject_delivered', $a_class_subject_delivered_data);

            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_absence_student($a_clause, $a_subject_delivered_in = false)
    {
        $this->db->from('dt_absence_student das');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_subject_delivered_in) {
            $this->db->where_in('subject_delivered_id', $a_subject_delivered_in);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function remove_absence_student($a_clause)
    {
        $this->db->delete('dt_absence_student', $a_clause);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function remove_unit_subject_delivered($a_clause)
    {
        $this->db->delete('dt_class_subject_delivered', $a_clause);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function remove_unit_subject_for_sync($s_class_master_id)
    {
        $this->db->delete('dt_class_subject_delivered', array('class_master_id' => $s_class_master_id));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_class_master_student($s_class_master_id, $a_clause = false, $a_clause_in = false)
    {
        $this->db->select("*, st.academic_year_id as 'batch', ds.academic_year_id AS 'class_academic_year', ds.semester_id AS 'score_semester', ds.semester_type_id AS 'class_semester_type', st.program_id, st.study_program_id");
        $this->db->from('dt_score ds');
        $this->db->join('dt_student st', 'st.student_id = ds.student_id');
        $this->db->join('dt_personal_data st_dpd', 'st.personal_data_id = st_dpd.personal_data_id');
        $this->db->join('ref_program rp', 'rp.program_id = st.program_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id');
        $this->db->join('ref_curriculum_subject rcs', 'rcs.curriculum_subject_id = ds.curriculum_subject_id');
        $this->db->join('ref_subject resub', 'resub.subject_id = rcs.subject_id');
        $this->db->join('ref_subject_name rsn', 'rsn.subject_name_id = resub.subject_name_id');
        $this->db->where('class_master_id', $s_class_master_id);
        $this->db->where('score_approval','approved');
        // $this->db->where('score_display','TRUE');
        // $this->db->where('st.student_status','active');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        if ($a_clause_in) {
            foreach ($a_clause_in as $key => $value) {
                $this->db->where_in($key, $value);
            }
        }
        $this->db->order_by('fc.faculty_name', 'DESC');
        $this->db->order_by('sp.study_program_name', 'DESC');
        $this->db->order_by('st.student_number', 'DESC');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_subject_student($a_param = false, $mbs_where_in = false)
    {
        $this->db->select('*, st.study_program_id, st.program_id');
        $this->db->from('dt_score sc');
        $this->db->join('dt_class_group cg', 'cg.class_group_id = sc.class_group_id');
        $this->db->join('dt_class_group_subject cgs', 'cgs.class_group_id = cg.class_group_id');
        $this->db->join('dt_offered_subject ofs', 'ofs.offered_subject_id = cgs.offered_subject_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = ofs.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        if ($a_param) {
            if ($mbs_where_in) {
                $this->db->where_in($mbs_where_in, $a_param);
            }else{
                $this->db->where($a_param);
            }
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_group_student($s_class_group_id = false, $a_clause = false, $mbs_where_in = false)
    {
        $this->db->select('*, ds.semester_id AS "score_semester", st.program_id, st.study_program_id');
        $this->db->from('dt_score ds');
        $this->db->join('dt_student st', 'st.student_id = ds.student_id');
        $this->db->join('dt_personal_data st_dpd', 'st.personal_data_id = st_dpd.personal_data_id');
        $this->db->join('ref_program rp', 'rp.program_id = st.program_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->where('score_approval','approved');
        $this->db->where('score_display','TRUE');
        $this->db->where('st.student_status','active');
        if ($s_class_group_id) {
            $this->db->where('class_group_id', $s_class_group_id);
        }
        if ($a_clause) {
            if ($mbs_where_in) {
                $this->db->where_in($mbs_where_in, $a_clause);
            }else{
                $this->db->where($a_clause);
            }
        }
        $this->db->order_by('st_dpd.personal_data_name');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_master_study_program($s_class_master_id)
    {
        $this->db->select('*, dos.program_id, dos.study_program_id');
        $this->db->from('dt_class_master cm');
        $this->db->join('dt_class_master_class cmc', 'cmc.class_master_id = cm.class_master_id');
        $this->db->join('dt_class_group_subject cgs', 'cgs.class_group_id = cmc.class_group_id');
        $this->db->join('dt_offered_subject dos', 'dos.offered_subject_id = cgs.offered_subject_id');
        $this->db->join('ref_study_program sp', 'dos.study_program_id = sp.study_program_id');
        $this->db->join('ref_program_study_program psp', 'psp.study_program_id = sp.study_program_id');
        $this->db->join('ref_program rp', 'rp.program_id = psp.program_id');
        $this->db->where('cm.class_master_id', $s_class_master_id);
        $this->db->group_by('dos.study_program_id');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_group_study_program($s_class_group_id = false)
    {
        $this->db->select('*, dos.program_id, dos.study_program_id');
        $this->db->from('dt_class_group_subject cgs');
        $this->db->join('dt_offered_subject dos', 'dos.offered_subject_id = cgs.offered_subject_id');
        $this->db->join('ref_study_program sp', 'dos.study_program_id = sp.study_program_id');
        $this->db->join('ref_program_study_program psp', 'psp.study_program_id = sp.study_program_id');
        $this->db->join('ref_program rp', 'rp.program_id = psp.program_id');
        $this->db->where('cgs.class_group_id', $s_class_group_id);
        $this->db->group_by('dos.study_program_id');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_master_subject($a_param = false)
    {
        $this->db->select('*, rcs.curriculum_subject_credit as "sks", dos.program_id, dos.study_program_id, cm.academic_year_id AS "class_academic_year_id", cm.semester_type_id AS "class_semester_type_id"');
        $this->db->from('dt_class_master cm');
        $this->db->join('dt_class_master_class cmc', 'cmc.class_master_id = cm.class_master_id');
        $this->db->join('dt_class_group_subject cgs', 'cgs.class_group_id=  cmc.class_group_id');
        $this->db->join('dt_offered_subject dos', 'dos.offered_subject_id = cgs.offered_subject_id');
        $this->db->join('ref_curriculum_subject rcs', 'rcs.curriculum_subject_id = dos.curriculum_subject_id');
        $this->db->join('ref_curriculum rc', 'rc.curriculum_id = rcs.curriculum_id');
        $this->db->join('ref_subject rs', 'rs.subject_id = rcs.subject_id');
        $this->db->join('ref_subject_name rsn', 'rsn.subject_name_id = rs.subject_name_id');
        $this->db->join('ref_study_program rsp', 'rsp.study_program_id = dos.study_program_id');
        if ($a_param) {
            $this->db->where($a_param);
        }
        $this->db->order_by('rsn.subject_name');
        $this->db->group_by('rs.subject_name_id');

        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_group_subject($s_class_group_id = false, $a_filter_data = false)
    {
        $this->db->select('*, dos.program_id as id_program, dos.study_program_id');
        $this->db->from('dt_class_group_subject cgs');
        $this->db->join('dt_offered_subject dos', 'dos.offered_subject_id = cgs.offered_subject_id');
        $this->db->join('ref_curriculum_subject rcs', 'rcs.curriculum_subject_id = dos.curriculum_subject_id');
        $this->db->join('ref_curriculum rc', 'rc.curriculum_id = rcs.curriculum_id');
        $this->db->join('ref_subject rs', 'rs.subject_id = rcs.subject_id');
        $this->db->join('ref_subject_name rsn', 'rsn.subject_name_id = rs.subject_name_id');
        $this->db->join('ref_study_program rsp', 'rsp.study_program_id = dos.study_program_id');
        if ($s_class_group_id) {
            $this->db->where('cgs.class_group_id', $s_class_group_id);
        }
        if ($a_filter_data) {
            $this->db->where($a_filter_data);
        }
        $this->db->order_by('rsn.subject_name');
        $this->db->group_by('rs.subject_name_id');

        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }
    
    public function student_in_class($s_class_group_id)
    {
        $this->db->select('*, dstu.program_id, dstu.study_program_id');
        $this->db->from('dt_score ds');
        $this->db->join('dt_class_group_subject dcgs', 'dcgs.class_group_subject_id = ds.class_group_subject_id');
        $this->db->join('dt_class_group dcg', 'dcg.class_group_id = dcgs.class_group_id');
        $this->db->join('ref_curriculum_subject rcs', 'rcs.curriculum_subject_id = ds.curriculum_subject_id');
        $this->db->join('ref_subject rs', 'rs.subject_id = rcs.subject_id');
        $this->db->join('dt_student dstu', 'dstu.student_id = ds.student_id');
        $this->db->join('dt_personal_data dpd', 'dpd.personal_data_id = dstu.personal_data_id');
        $this->db->join('ref_study_program rsp', 'rsp.study_program_id = dstu.study_program_id');
        $this->db->where('dcgs.class_group_id', $s_class_group_id);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function remove_class_master_not_used($class_master_id)
    {
        $this->db->update('dt_score', array('class_master_id' => null), array('class_master_id' => $class_master_id));
        
        $this->db->delete('dt_class_master', array('class_master_id' => $class_master_id));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_class_group_master_lists($a_clause = false)
    {
        $this->db->select('*, cm.academic_year_id as running_year');
        $this->db->from('dt_class_master cm');
        $this->db->join('dt_class_master_class cmc', 'cmc.class_master_id = cm.class_master_id');
        $this->db->join('dt_class_group cg', 'cg.class_group_id = cmc.class_group_id');
        $this->db->join('ref_semester_type rsf', 'rsf.semester_type_id = cm.semester_type_id');    
        $this->db->join('dt_class_group_subject cgs', 'cg.class_group_id = cgs.class_group_id');
        $this->db->join('dt_offered_subject ofs', 'ofs.offered_subject_id = cgs.offered_subject_id');
        $this->db->join('ref_curriculum_subject rcs', 'rcs.curriculum_subject_id = ofs.curriculum_subject_id');
        $this->db->join('ref_program pr', 'pr.program_id = ofs.program_id');
        if ($a_clause) {
            foreach ($a_clause as $key => $value) {
                $this->db->where('cm.'.$key, $value);
            }
        }
        $this->db->group_by('cm.class_master_id');
        $this->db->order_by('cm.class_master_name','ASC');
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_id_master_class($a_clause = false)
    {
        $this->db->select('cmc.class_group_id, cmc.class_master_id');
        $this->db->from('dt_class_master_class cmc');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_group_lists($a_clause = false)
    {
        $this->db->select('*, dcg.academic_year_id as running_year, dcg.semester_type_id as running_semester');
        $this->db->from('dt_class_group dcg');
        $this->db->join('ref_semester_type rsf', 'rsf.semester_type_id = dcg.semester_type_id');
        $this->db->join('dt_class_group_subject cgs', 'dcg.class_group_id = cgs.class_group_id');
        $this->db->join('dt_offered_subject ofs', 'ofs.offered_subject_id = cgs.offered_subject_id');
        $this->db->join('ref_curriculum_subject rcs', 'curriculum_subject_id');
        $this->db->join('ref_curriculum rcc', 'curriculum_id');
        $this->db->join('ref_subject rs', 'subject_id');
        $this->db->join('ref_subject_name rsn', 'subject_name_id');
        $this->db->join('ref_program pr', 'pr.program_id = ofs.program_id', 'LEFT');
        if ($a_clause) {
            foreach ($a_clause as $key => $value) {
                $this->db->where('dcg.'.$key, $value);
            }
        }
        $this->db->group_by('dcg.class_group_id');
        $this->db->order_by('dcg.class_group_name','ASC');
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_lecturer_grouping($a_param = false, $b_class_id_in = false)
    {
        $this->db->from('dt_class_group_lecturer');
        $this->db->join('dt_employee', 'employee_id');
        $this->db->join('dt_personal_data', 'personal_data_id');
        if ($a_param) {
            if ($b_class_id_in) {
                $this->db->where_in('class_group_id', $a_param);
            }else{
                $this->db->where($a_param);
            }
        }
        $this->db->group_by('dt_class_group_lecturer.employee_id');
        $this->db->group_by('dt_class_group_lecturer.credit_allocation');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_group_lecturer_in($a_class_group_lect_id)
    {
        $this->db->from('dt_class_group_lecturer');
        $this->db->join('dt_employee', 'employee_id');
        $this->db->join('dt_personal_data', 'personal_data_id');
        $this->db->where_in('class_group_lecturer_id', $a_class_group_lect_id);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_master_lecturer($a_param = false, $mba_param_in = false)
    {
        $this->db->from('dt_class_master_lecturer');
        $this->db->join('dt_employee', 'employee_id');
        $this->db->join('dt_personal_data', 'personal_data_id');
        if ($a_param) {
            if ($mba_param_in) {
                $this->db->where_in($mba_param_in, $a_param);
            }else{
                $this->db->where($a_param);
            }
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_group_lecturer($a_param = false, $b_class_id_in = false)
    {
        $this->db->from('dt_class_group_lecturer cgl');
        $this->db->join('dt_employee emp', 'emp.employee_id = cgl.employee_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = emp.personal_data_id');
        if ($a_param) {
            if ($b_class_id_in) {
                $this->db->where_in('class_group_id', $a_param);
            }else{
                $this->db->where($a_param);
            }
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function remove_team_teaching($s_class_group_lecturer_id)
    {
        $this->db->delete('dt_class_group_lecturer', array('class_group_lecturer_id' => $s_class_group_lecturer_id));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_class_by_offered_subject($a_clause = false, $where_in = false)
    {
        $this->db->select('*, ofs.academic_year_id, ofs.semester_type_id, ofs.study_program_id, ofs.program_id');
        $this->db->from('dt_offered_subject ofs');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = ofs.study_program_id');
        $this->db->join('dt_class_group_subject cgs', 'cgs.offered_subject_id = ofs.offered_subject_id');
        $this->db->join('ref_curriculum_subject crs', 'crs.curriculum_subject_id = ofs.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = crs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        $this->db->join('dt_class_group cg', 'cg.class_group_id = cgs.class_group_id');
        // $this->db->join('dt_class_group_lecturer cgl', 'cgl.class_group_id = cgs.class_group_id');
        if ($a_clause) {
            if ($where_in) {
                $this->db->where_in($where_in, $a_clause);
            }else {
                $this->db->where($a_clause);
            }
        }
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_group_filtered($filter_data = false, $b_feeder_sync = true)
    {
        $this->db->select("
        	*, 
            cs.semester_id as semester_id,
        	dcg.academic_year_id as running_year, 
        	dcg.semester_type_id as class_semester_type_id,
            rsps.study_program_main_id,
            rspc.study_program_main_id AS class_study_program_main_id,
            ofs.study_program_id AS class_group_study_program_id,
            rsps.study_program_id AS subject_study_program_id,
            sb.program_id AS subject_program_id,
            ofs.program_id AS class_program_id,
            rsps.study_program_main_id AS subject_study_program_main_id
        ");
        $this->db->from('dt_class_group dcg');
        $this->db->join('ref_semester_type smt', 'smt.semester_type_id = dcg.semester_type_id');
        $this->db->join('dt_class_group_subject cgs', 'cgs.class_group_id = dcg.class_group_id');
        $this->db->join('dt_class_master_class cmc', 'cmc.class_group_id = dcg.class_group_id', 'left');
        $this->db->join('dt_class_group_lecturer cgl', 'cgl.class_group_id = dcg.class_group_id');
        $this->db->join('dt_offered_subject ofs', 'ofs.offered_subject_id = cgs.offered_subject_id');
        $this->db->join('ref_curriculum_subject cs', ' cs.curriculum_subject_id = ofs.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        $this->db->join('ref_study_program rsps', 'rsps.study_program_id = sb.study_program_id');
        $this->db->join('ref_study_program rspc', 'rspc.study_program_id = ofs.study_program_id');
        if ($filter_data) {
            $this->db->where($filter_data);
        }
        
        if($b_feeder_sync){
	        $this->db->group_by('dcg.class_group_id');
        }
        $this->db->order_by('sn.subject_name');
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_class_group_filtered_test($filter_data = false, $b_feeder_sync = true)
    {
        $this->db->select("
        	*, 
            cs.semester_id as semester_id,
        	dcg.academic_year_id as running_year, 
        	dcg.semester_type_id as class_semester_type_id,
            rsps.study_program_main_id,
            rspc.study_program_main_id AS class_study_program_main_id,
            ofs.study_program_id AS class_group_study_program_id,
            rsps.study_program_id AS subject_study_program_id,
            sb.program_id AS subject_program_id,
            ofs.program_id AS class_program_id,
            rsps.study_program_main_id AS subject_study_program_main_id
        ");
        $this->db->from('dt_class_group dcg');
        $this->db->join('ref_semester_type smt', 'smt.semester_type_id = dcg.semester_type_id');
        $this->db->join('dt_class_group_subject cgs', 'cgs.class_group_id = dcg.class_group_id');
        $this->db->join('dt_class_master_class cmc', 'cmc.class_group_id = dcg.class_group_id', 'left');
        $this->db->join('dt_class_group_lecturer cgl', 'cgl.class_group_id = dcg.class_group_id');
        $this->db->join('dt_offered_subject ofs', 'ofs.offered_subject_id = cgs.offered_subject_id');
        $this->db->join('ref_curriculum_subject cs', ' cs.curriculum_subject_id = ofs.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        $this->db->join('ref_study_program rsps', 'rsps.study_program_id = sb.study_program_id');
        $this->db->join('ref_study_program rspc', 'rspc.study_program_id = ofs.study_program_id');
        if ($filter_data) {
            $this->db->where($filter_data);
        }
        
        if($b_feeder_sync){
	        $this->db->group_by('dcg.class_group_id');
        }
        $this->db->order_by('sn.subject_name');
        
        $query = $this->db->get();
        // return ($query->num_rows() > 0) ? $query->result() : false;
        return $this->db->last_query();
    }

    public function get_class_master_filtered($filter_data = false, $s_grouping = 'cm.class_master_id', $a_order = false)
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
        if ($filter_data) {
            $this->db->where($filter_data);
        }
        if ($s_grouping) {
            $this->db->group_by($s_grouping);
        }
        
        if ($a_order) {
            foreach ($a_order as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
    
    public function check_get_class_master_filtered($filter_data = false)
    {
        $this->db->select('*, cm.academic_year_id as running_year, cm.semester_type_id as class_semester_type_id');
        $this->db->from('dt_class_master cm');
        $this->db->join('dt_class_master_class cmc', 'cmc.class_master_id = cm.class_master_id');
        // $this->db->join('ref_semester_type smt', 'smt.semester_type_id = cm.semester_type_id');
        // $this->db->join('dt_class_group cg', 'cg.class_group_id = cmc.class_group_id');
        // $this->db->join('dt_class_group_subject cgs', 'cgs.class_group_id = cg.class_group_id');
        // $this->db->join('dt_offered_subject ofs', 'ofs.offered_subject_id = cgs.offered_subject_id');
        // $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = ofs.curriculum_subject_id');
        // $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        // $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        // $this->db->join('dt_class_master_lecturer cml', 'cml.class_master_id = cm.class_master_id','LEFT');
        // $this->db->join('ref_program pr', 'pr.program_id = ofs.program_id', 'LEFT');
        if ($filter_data) {
            $this->db->where($filter_data);
        }
        $this->db->group_by('cm.class_master_id');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : $this->db->last_query();
    }

    public function save_class_group_subject($a_class_group_subject_data, $s_class_group_subject_id = false)
    {
        if (is_object($a_class_group_subject_data)) {
            $a_class_group_subject_data = (array)$a_class_group_subject_data;
        }

        if ($s_class_group_subject_id) {
            $this->db->update('dt_class_group_subject', $a_class_group_subject_data, array('class_group_subject_id' => $s_class_group_subject_id));
        }else{
            if (!array_key_exists('class_group_subject_id', $a_class_group_subject_data)) {
                $a_class_group_subject_data['class_group_subject_id'] = $this->uuid->v4();
            }

            $this->db->insert('dt_class_group_subject', $a_class_group_subject_data);
        }

        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function save_data($a_class_group_data, $class_group_id = false)
    {
        if (is_object($a_class_group_data)) {
            $a_class_group_data = (array)$a_class_group_data;
        }

        if ($class_group_id) {
            $this->db->update('dt_class_group', $a_class_group_data, array('class_group_id' => $class_group_id));
            return true;
        }else{
            if (!array_key_exists('class_group_id', $a_class_group_data)) {
                $a_class_group_data['class_group_id'] = $this->uuid->v4();
            }

            $this->db->insert('dt_class_group', $a_class_group_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }
    
    public function remove_class_group_lecturer($a_param)
    {
        $this->db->delete('dt_class_group_lecturer', $a_param);

        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function save_class_group_lecturer($a_class_group_lecturer, $s_class_group_lecturer_id = false)
    {
        if (is_object($a_class_group_lecturer)) {
            $a_class_group_lecturer = (array)$a_class_group_lecturer;
        }

        if ($s_class_group_lecturer_id) {
            $this->db->update('dt_class_group_lecturer', $a_class_group_lecturer, array('class_group_lecturer_id' => $s_class_group_lecturer_id));
            return true;
        }else{
            if (!array_key_exists('class_group_lecturer_id', $a_class_group_lecturer)) {
                $a_class_group_lecturer['class_group_lecturer_id'] = $this->uuid->v4();
            }

            $this->db->insert('dt_class_group_lecturer', $a_class_group_lecturer);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_class_groups($a_clause = false)
    {
        $this->db->from('dt_class_group');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_class_subject_mastering($s_class_group_id)
    {
        $this->db->select('*, cg.academic_year_id as running_year, cg.semester_type_id as class_semester_type_id');
        $this->db->from('dt_class_group cg');
        $this->db->join('dt_class_group_subject cgs', 'cgs.class_group_id = cg.class_group_id');
        $this->db->join('dt_class_group_lecturer cgl', 'cgl.class_group_id = cg.class_group_id');
        $this->db->join('dt_offered_subject ofs', 'ofs.offered_subject_id = cgs.offered_subject_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = ofs.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        $this->db->where('cg.class_group_id', $s_class_group_id);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function remove_class_group_data($s_class_group_id)
    {
        $this->db->delete('dt_class_group', array('class_group_id' => $s_class_group_id));

        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function remove_class_master_sync($s_class_master_id)
    {
        $this->db->delete('dt_class_master', array('class_master_id' => $s_class_master_id));
        
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_class_group_unmastering($a_clause = false, $unmastering = false)
    {
        $this->db->select('*, cg.academic_year_id as running_year');
        $this->db->from('dt_class_master_class cmc');
        $this->db->join('dt_class_group cg', 'cmc.class_group_id = cg.class_group_id');
        $this->db->join('ref_semester_type rsf', 'rsf.semester_type_id = cg.semester_type_id');
        if ($a_clause) {
            foreach ($a_clause as $key => $value) {
                $this->db->where('cg.'.$key, $value);
            }
        }
        if ($unmastering) {
            $this->db->where_not_in('cg.class_group_id', $unmastering);
        }

        $this->db->group_by('cg.class_group_id');
        $this->db->order_by('cg.class_group_name','ASC');
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    function save_absence_dev($a_absence_data, $a_uosd_absence) {
        // $this->db->trans_start();

        // $s_message = '';

        // $mbo_lect_in_class = $this->get_class_master_lecturer(array('class_master_id' => $a_absence_data['class_master_id'], 'employee_id' => $a_absence_data['employee_id']));
        // if ($mbo_lect_in_class) {
        //     if ((is_array($a_absence_data['unit_time'])) AND (is_array($a_absence_data['unit_description']))) {
        //         foreach ($a_absence_data['unit_time'] as $key => $s_time) {
        //             $s_date_start_format = $a_absence_data['unit_date'].' '.$s_time.':00';
        //             $s_date_start = date('Y-m-d H:i:s', strtotime($s_date_start_format));
        //             $s_date_end = date('Y-m-d H:i:s',strtotime('+1 hour',strtotime($s_date_start)));

        //             if ($a_absence_data['subject_delivered_id'] == '') {
        //                 if ($this->get_unit_subject_delivered(false, array('class_master_id' => $a_absence_data['class_master_id'], 'subject_delivered_time_start' => $s_date_start))) {
        //                     $message = 'Topic on '.$s_date_start.' is exists, please change time time '.;
        //                     print(json_encode($a_rtn));exit;
        //                 }
        //                 else{
        //                     $a_subject_delivered_data['date_added'] = date('Y-m-d H:i:s');
        //                     $this->save_subject_delivered($a_subject_delivered_data);
        //                 }
        //             }else{
        //                 $this->remove_absence_student(array('subject_delivered_id' => $a_absence_data['subject_delivered_id']));
        //                 $this->save_subject_delivered($a_subject_delivered_data, $a_absence_data['subject_delivered_id']);
        //             }
        //         }
        //     }
        //     else {
        //         $s_message = "Error getting time and topics";
        //     }
        // }
        // else {
        //     $s_message = "Lecturer not found in class group lecturer";
        // }
        // if (!empty($s_message)) {
        //     $this->db->trans_rollback();
        //     $a_return = ['code' => 1, 'message' => $s_message];
        // }
        // else if ($this->db->trans_status() === false) {
        //     $this->db->trans_rollback();
        //     $a_return = ['code' => 1, 'message' => "Unknow error"];
        // }else{
        //     $this->db->trans_commit();
        //     $a_return = ['code' => 0,'message' => 'Success'];
        // }

        // return $a_return;
    }

    public function save_absence($a_absence_data, $a_uosd_absence)
    {
        $this->db->trans_start();
        $mbo_lect_in_class = $this->get_class_master_lecturer(array('class_master_id' => $a_absence_data['class_master_id'], 'employee_id' => $a_absence_data['employee_id']));
        if ($mbo_lect_in_class) {
            $s_subject_delivered_id = ($a_absence_data['subject_delivered_id'] == '') ? $this->uuid->v4() : $a_absence_data['subject_delivered_id'];
            $s_date_start_format = $a_absence_data['unit_date'].' '.$a_absence_data['unit_time'].':00';
            $s_date_start = date('Y-m-d H:i:s', strtotime($s_date_start_format));
            $s_date_end = date('Y-m-d H:i:s',strtotime('+1 hour',strtotime($s_date_start)));

            if ((array_key_exists('unit_time_end', $a_absence_data)) AND (!empty($a_absence_data['unit_time_end']))) {
                $s_date_end_format = $a_absence_data['unit_date'].' '.$a_absence_data['unit_time_end'].':00';
                $s_date_end = date('Y-m-d H:i:s', strtotime($s_date_end_format));
            }
        
            $a_subject_delivered_data = array(
                'subject_delivered_id' => $s_subject_delivered_id,
                'class_master_id' => $a_absence_data['class_master_id'],
                'employee_id' => $a_absence_data['employee_id'],
                'subject_delivered_time_start' => $s_date_start,
                'subject_delivered_time_end' => $s_date_end,
                'subject_delivered_description' => $a_absence_data['unit_description']
            );

            if ($a_absence_data['subject_delivered_id'] == '') {
                $check_absence = $this->get_unit_subject_delivered(false, array(
                    'class_master_id' => $a_absence_data['class_master_id'],
                    'subject_delivered_time_start <= ' => $s_date_start,
                    'subject_delivered_time_end > ' => $s_date_start,
                ));

                if ($check_absence) {
                    $a_rtn = array('code' => 1, 'message' => 'Unit subject has been delivered on '.$check_absence[0]->subject_delivered_time_start);
                    print(json_encode($a_rtn));exit;
                }else{
                    $a_subject_delivered_data['date_added'] = date('Y-m-d H:i:s');
                    $this->save_subject_delivered($a_subject_delivered_data);
                }
            }else{
                $this->remove_absence_student(array('subject_delivered_id' => $a_absence_data['subject_delivered_id']));
                $this->save_subject_delivered($a_subject_delivered_data, $a_absence_data['subject_delivered_id']);
            }
            
            if (count($a_uosd_absence) > 0) {
                foreach ($a_uosd_absence as $absence) {
                    $s_score_id = $absence['score_id'];
                    $s_score_absence = $absence['score_absence'];
                    if ($s_score_absence == '') {
                        $a_rtn = array('code' => 1, 'message' => 'All absence student required');
                        return $a_rtn;exit;
                    }else{
                        if ($a_absence_data['with_quiz'] != 0) {
                            $score_quiz = $absence['score_quiz'];
                            $a_notes = array(
                                'score_quiz' => $score_quiz,
                                'quiz_number' => $a_absence_data['quiz_number']
                            );

                            $a_score_data = array(
                                'score_quiz'.$a_absence_data['quiz_number'] => $score_quiz
                            );
                            $this->db->update('dt_score', $a_score_data, array('score_id' => $s_score_id));
                        }

                        $a_absence_student_data = array(
                            'absence_student_id' => $this->uuid->v4(),
                            'score_id' => $s_score_id,
                            'subject_delivered_id' => $s_subject_delivered_id,
                            'absence_status' => $s_score_absence,
                            'absence_note' => ($absence['score_note'] == '') ? null : $absence['score_note'],
                            'absence_description' => ($a_absence_data['with_quiz'] != 0) ? json_encode($a_notes) : NULL
                        );
                        
                        if ($this->save_student_absence($a_absence_student_data)) {
                            
                            if($this->updating_score($s_score_id)) {
                                $a_rtn = array('code' => 0, 'message' => 'Success');
                            }else{
                                $a_rtn = array('code' => 1, 'message' => 'Error updating score!');
                            }
                        }else{
                            $a_rtn = array('code' => 1, 'message' => 'Saving error!');
                        }
                    }
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => 'Nothing saved!');
            }
        }else{
            $a_rtn = array('code' => 1, 'message' => 'Lecturer not found in class group lecturer');
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        }else{
            $this->db->trans_commit();
        }

        return $a_rtn;
    }

    public function calculate_score_absence_student($s_score_id)
    {
        $mba_score_data = $this->Scm->get_score_data(array('score_id' => $s_score_id))[0];
        if (!is_null($mba_score_data->class_master_id)) {
            $a_class_param = array(
                'cm.class_master_id' => $mba_score_data->class_master_id
            );
        }else{
            $a_class_param = array(
                'cmc.class_group_id' => $mba_score_data->class_group_id
            );
        }

        $mba_class_subject_data = $this->get_class_master_subject($a_class_param)[0];
        if ($mba_class_subject_data) {
            $i_subject_credit = $mba_class_subject_data->sks;

            $mba_absence_student_lists = $this->get_absence_student(array('score_id' => $s_score_id, 'absence_status' => 'ABSENT'));
            $i_count_alpha =  ($mba_absence_student_lists) ? count($mba_absence_student_lists) : 0;

            $i_score_recap = $this->grades->get_score_absence($i_count_alpha, $i_subject_credit);
            return $i_score_recap;
        }else {
            return false;
        }
    }

    // public function woow()
    // {
    //     $mba_score_data = $this->
    // }

    public function updating_score($s_score_id)
    {
        $q_score_data = $this->db->get_where('dt_score', array('score_id' => $s_score_id));
        if ($q_score_data->num_rows() > 0) {
            $mba_score_data = $q_score_data->row();
            if (!is_null($mba_score_data->class_master_id)) {
                $a_class_param = array(
                    'cm.class_master_id' => $mba_score_data->class_master_id
                );
                
                $mba_class_master_lecturer_data = $this->db->get_where('dt_class_master_lecturer', array('class_master_id' => $mba_score_data->class_master_id));
                if ($mba_class_master_lecturer_data->num_rows() > 0) {
                    foreach ($mba_class_master_lecturer_data->result() as $class_lecturer) {
                        $this->set_credit_realization($mba_score_data->class_master_id, $class_lecturer->employee_id);
                    }
                }
            }else{
                $a_class_param = array(
                    'cmc.class_group_id' => $mba_score_data->class_group_id
                );
            }

            $mba_class_subject_data = $this->get_class_master_subject($a_class_param)[0];
            if ($mba_class_subject_data) {
                $s_class_master_id = $mba_class_subject_data->class_master_id;

                $mba_class_subject_delivered = $this->get_class_subject_delivered(array('class_master_id' => $s_class_master_id));
                $mba_subject_delivered_id = false;
                if ($mba_class_subject_delivered) {
                    $mba_subject_delivered_id = array();
                    foreach ($mba_class_subject_delivered as $o_class_subject) {
                        if (!in_array($o_class_subject->subject_delivered_id, $mba_subject_delivered_id)) {
                            array_push($mba_subject_delivered_id, $o_class_subject->subject_delivered_id);
                        }
                    }
                }

                $mba_absence_student_lists = $this->get_absence_student(array('score_id' => $s_score_id, 'absence_status' => 'ABSENT'), $mba_subject_delivered_id);
                $i_quiz_fill = 0;
                $i_quiz_sum = 0;
                for ($i=1; $i < 6; $i++) { 
                    $key = 'score_quiz'.$i;
                    if ($mba_score_data->$key != null) {
                        $i_quiz_fill++;
                        $i_quiz_sum += (double)$mba_score_data->$key;
                    }
                }
                
                $i_score_quiz = (($i_quiz_sum == 0) && ($i_quiz_fill == 0)) ? 0 : $i_quiz_sum/$i_quiz_fill;
                
                $i_subject_credit = $mba_class_subject_data->sks;
                // $i_count_alpha =  ($mba_absence_student_lists) ? count($mba_absence_student_lists) : 0;
                $i_count_alpha =  0;
                if ($mba_absence_student_lists) {
                    foreach ($mba_absence_student_lists as $o_absence) {
                        $mba_subject_delivered_data = $this->General->get_where('dt_class_subject_delivered', [
                            'subject_delivered_id' => $o_absence->subject_delivered_id
                        ]);
                        if ($mba_subject_delivered_data) {
                            $timestart = date('H', strtotime($mba_subject_delivered_data[0]->subject_delivered_time_start));
                            $timeend = date('H', strtotime($mba_subject_delivered_data[0]->subject_delivered_time_end));
                            $timecourse = intval($timeend) - intval($timestart);
                            $i_count_alpha += $timecourse;
                        }
                    }
                }
                $i_score_absence = $this->grades->get_score_absence($i_count_alpha, $i_subject_credit);

                $f_score_final_exam = (double)(!is_null($mba_score_data->score_final_exam)) ? $mba_score_data->score_final_exam : 0;
                $f_score_repeat_exam = (double)(!is_null($mba_score_data->score_repetition_exam)) ? $mba_score_data->score_repetition_exam : 0;
                $s_score_exam = 0;
                if ($f_score_final_exam >= $f_score_repeat_exam) {
                    $s_score_exam = $f_score_final_exam;
                }else if ($f_score_final_exam <= $f_score_repeat_exam) {
                    $s_score_exam = $f_score_repeat_exam;
                }

                $s_score_sum = $this->grades->get_score_sum($i_score_quiz, $s_score_exam);
                $s_score_grade = $this->grades->get_grade($s_score_sum);
                $s_score_grade_point = $this->grades->get_grade_point($s_score_sum);
                $a_score_data_update = array(
                    'score_absence' => round($i_score_absence, 2, PHP_ROUND_HALF_UP),
                    'score_quiz' => round($i_score_quiz, 2, PHP_ROUND_HALF_UP),
                    'score_sum' => round($s_score_sum, 2, PHP_ROUND_HALF_UP),
                    'score_grade' => $s_score_grade,
                    'score_grade_point' => round($s_score_grade_point, 2, PHP_ROUND_HALF_UP)
                );

                if ($i_subject_credit > 0) {
                    $s_score_merit = $this->grades->get_merit($i_subject_credit, $s_score_grade_point);
                    // $s_score_ects = $this->grades->get_score_ects($i_subject_credit, $s_score_grade_point);
                    $s_score_ects = $this->grades->get_ects_score($i_subject_credit, $mba_class_subject_data->subject_name);

                    $a_score_data_update['score_merit'] = round($s_score_merit, 2, PHP_ROUND_HALF_UP);
                    $a_score_data_update['score_ects'] = round($s_score_ects, 2, PHP_ROUND_HALF_UP);
                }

                if (($mba_score_data->portal_id == 0) OR (is_null($mba_score_data->portal_id))) {
                    $this->db->update('dt_score', $a_score_data_update, array('score_id' => $s_score_id));
                }
                
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }

    public function set_credit_realization($s_class_master_id, $s_employee_id)
    {
        $mbo_class_master_data = $this->get_class_master_filtered(array('cm.class_master_id' => $s_class_master_id));
        if ($mbo_class_master_data) {
            $mbo_class_master_lecturer = $this->get_class_master_lecturer(array('class_master_id' => $s_class_master_id, 'employee_id' => $s_employee_id))[0];
            if ($mbo_class_master_lecturer) {
                $mba_subject_delivered_data = $this->get_unit_subject_delivered($s_class_master_id, array('cgsm.employee_id' => $s_employee_id));
                $d_credit_realization = $this->grades->get_credit_realization($mbo_class_master_data[0]->curriculum_subject_credit, ($mba_subject_delivered_data) ? count($mba_subject_delivered_data) : 0);
                
                $a_class_lecturer_data = array(
                    'credit_realization' => $d_credit_realization
                );
                $this->db->update('dt_class_master_lecturer', $a_class_lecturer_data, array('class_master_id' => $s_class_master_id, 'employee_id' => $s_employee_id));

                $mba_class_group_data = $this->get_class_id_master_class(array('cmc.class_master_id' => $s_class_master_id));
                if ($mba_class_group_data) {
                    $mbo_class_group_lecturer = $this->get_class_group_lecturer(array('cgl.class_group_id' => $mba_class_group_data[0]->class_group_id, 'cgl.employee_id' => $s_employee_id))[0];
                    if ($mbo_class_group_lecturer) {
                        $this->db->update('dt_class_group_lecturer', $a_class_lecturer_data, array('class_group_lecturer_id' => $mbo_class_group_lecturer->class_group_lecturer_id));
                    }
                }
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
