<?php
class Ofse_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function submit_bulk_ofse_schedule(
        $s_ofse_period_id,
        $s_ofse_exam_date,
        $s_ofse_exam_room,
        $s_exam_zoomid,
        $s_exam_zoompasscode,
        $a_ofse_exam_score_id,
        $a_ofse_exam_time_start,
        $a_ofse_exam_time_end
    )
    {
        if (!is_array($a_ofse_exam_score_id)) {
            $a_return = ['code' => 2, 'message' => 'Error score is not array!'];
        }
        else if (count($a_ofse_exam_score_id) > 0) {
            $this->db->trans_begin();
            
            $remove_data_existing = $this->db->delete('dt_ofse_exam', [
                'exam_room' => $s_ofse_exam_room,
                'exam_date' => $s_ofse_exam_date
            ]);
            
            foreach ($a_ofse_exam_score_id as $key => $s_score_id) {
                if (!empty($s_score_id)) {
                    $s_zoom_id = str_replace(' ', '', $s_exam_zoomid);
                    $s_zoom_passcode = str_replace(' ', '', $s_exam_zoompasscode);
                    $a_data = [
                        'ofse_exam_id' => $this->uuid->v4(),
                        'score_id' => $s_score_id,
                        'exam_room' => $s_ofse_exam_room,
                        'exam_date' => $s_ofse_exam_date,
                        'exam_zoom_id' => (empty($s_zoom_id)) ? NULL : $s_zoom_id,
                        'exam_zoom_passcode' => (empty($s_zoom_passcode)) ? NULL : $s_zoom_passcode,
                        'exam_time_start' => $a_ofse_exam_time_start[$key],
                        'exam_time_end' => $a_ofse_exam_time_end[$key]
                    ];

                    $this->db->insert('dt_ofse_exam', $a_data);
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $a_return = ['code' => 2, 'message' => 'Transaction rolling back, unknow error!'];
            }
            else {
                $this->db->trans_commit();
                $a_return = ['code' => 0, 'message' => 'Success!'];
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'Data not found!'];
        }

        return $a_return;
    }

    public function get_ofse_participant_data($a_clause = false)
    {
        $this->db->select('*, st.academic_year_id AS "student_batch", sc.academic_year_id, sc.semester_type_id');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->group_by('sc.student_id');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
    
    public function get_ofse_shecdule_date($s_ofse_period_id, $a_clause = false)
    {
        $this->db->from('dt_ofse_exam ox');
        $this->db->join('dt_score sc', 'sc.score_id = ox.score_id');
        $this->db->where('sc.ofse_period_id', $s_ofse_period_id);
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->group_by('ox.exam_date');
        $this->db->group_by('ox.exam_room');

        $this->db->order_by('ox.exam_date');
        $this->db->order_by('ox.exam_room');

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_ofse_participant_exam_date($a_clause = false)
    {
        $this->db->from('dt_score sc');
        $this->db->join('dt_ofse_exam ox', 'ox.score_id = sc.score_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function submit_schedule($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('dt_ofse_exam', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('dt_ofse_exam', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_evaluation_examiner($a_clause = false)
    {
        $this->db->from('dt_ofse_evaluation oe');
        $this->db->join('dt_ofse_examiner om', 'om.student_examiner_id = oe.student_examiner_id');
        $this->db->join('dt_ofse_subject_question oq', 'oq.subject_question_id = oe.subject_question_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_ofse_structure($s_ofse_period_id = false, $a_clause = false)
    {
        $this->db->from('dt_ofse do');
        $this->db->join('dt_score sc', 'sc.ofse_period_id = do.ofse_period_id');
        // $this->db->join('dt_ofse_exam oe', 'oe.score_id = sc.score_id', 'left');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        if ($s_ofse_period_id) {
            $this->db->where('sc.ofse_period_id', $s_ofse_period_id);
        }
        $this->db->where('sc.score_approval', 'approved');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function is_examiner($a_clause = false)
    {
        $this->db->from('dt_ofse_examiner oe');
        $this->db->join('dt_score sc', 'sc.score_id = oe.score_id');
        $this->db->join('thesis_advisor ta', 'ta.advisor_id = oe.advisor_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = ta.personal_data_id');
        $this->db->join('ref_institution ri', 'ri.institution_id = ta.institution_id', 'LEFT');
        $this->db->order_by('oe.examiner_type');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false ;
    }

    public function get_ofse_student_member($a_clause = false)
    {
        $this->db->from('dt_score sc');
        $this->db->join('dt_ofse do', 'do.ofse_period_id = sc.ofse_period_id');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_subject_question_multi($a_subject_question_id, $a_clause = false)
    {
        if (is_array($a_subject_question_id)) {
            $this->db->from('dt_ofse_subject_question oq');
            $this->db->join('ref_subject sb', 'sb.subject_id = oq.subject_id');
            $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
            $this->db->where_in('oq.subject_question_id', $a_subject_question_id);
            if ($a_clause) {
                $this->db->where($a_clause);
            }
            $query = $this->db->get();
            return ($query->num_rows() > 0) ? $query->result() : false;
        }
        else {
            return false;
        }
    }

    public function get_score_evaluation($a_clause = false)
    {
        $this->db->from('dt_ofse_evaluation oe');
        $this->db->join('dt_ofse_subject_question oq', 'oq.subject_question_id = oe.subject_question_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('oe.subject_question_id');
        $this->db->order_by('oe.score_sequence');

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function destroy_score_evaluation($s_student_examiner_id, $s_subject_question_id)
    {
        $this->db->delete('dt_ofse_evaluation', ['student_examiner_id' => $s_student_examiner_id, 'subject_question_id' => $s_subject_question_id]);
        return true;
    }

    public function submit_score_evaluation($a_data, $a_update_clause = false)
    {
        $this->db->insert('dt_ofse_evaluation', $a_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_ofse_exam_data($a_clause = false)
    {
        $this->db->select('*, st.academic_year_id AS "student_batch"');
        $this->db->from('dt_ofse_exam oe');
        $this->db->join('dt_score sc', 'sc.score_id = oe.score_id');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('oe.exam_date');
        $this->db->order_by('oe.exam_time_start');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function submit_file($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('dt_ofse_subject_question', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('dt_ofse_subject_question', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function remove_examiner($s_student_examiner_id)
    {
        $this->db->delete('dt_ofse_examiner', ['student_examiner_id' => $s_student_examiner_id]);
        return true;
    }

    public function unlock_evaluation($s_student_examiner_id)
    {
        $this->db->update('dt_ofse_examiner', ['examiner_lock_evaluation' => 'false'], ['student_examiner_id' => $s_student_examiner_id]);
        return true;
    }

    public function submit_ofse_examiner($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('dt_ofse_examiner', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('dt_ofse_examiner', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_student_ofse_examiner($s_score_id, $a_clause = false)
    {
        $this->db->from('dt_ofse_examiner oe');
        $this->db->join('dt_score sc', 'sc.score_id = oe.score_id');
        $this->db->join('thesis_advisor ta', 'ta.advisor_id = oe.advisor_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = ta.personal_data_id');
        $this->db->join('ref_institution ri', 'ri.institution_id = ta.institution_id', 'LEFT');
        $this->db->order_by('oe.examiner_type');
        $this->db->where('oe.score_id', $s_score_id);
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false ;
    }

    public function get_subject_examiner($a_clause = false)
    {
        $this->db->from('dt_ofse_examiner oe');
        $this->db->join('thesis_advisor ta', 'ta.advisor_id = oe.advisor_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = ta.personal_data_id');
        $this->db->join('ref_institution ri', 'ri.institution_id = ta.institution_id', 'LEFT');
        $this->db->order_by('oe.examiner_type');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false ;
    }

    public function get_ofse_list_student($a_clause = false)
    {
        $this->db->from('dt_score sc');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_ofse_list_subject($a_clause = false)
    {
        $this->db->from('dt_score sc');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->group_by('sn.subject_name');
        $this->db->order_by('sn.subject_name');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_ofse_subject_question($a_clause = false)
    {
        $this->db->from('dt_ofse_subject_question oq');
        $this->db->join('ref_subject sb', 'sb.subject_id = oq.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('oq.ofse_question_sequence');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
    
    public function get_ofse_participants_subjects($s_student_id, $s_academic_year_id, $s_semester_type_id)
    {
	    $this->db->join('ref_curriculum_subject rcs', 'curriculum_subject_id');
	    $this->db->join('ref_subject rs', 'subject_id');
	    $this->db->join('ref_subject_name rsn', 'subject_name_id');
	    $query = $this->db->get_where('dt_score ds', array(
		    'ds.academic_year_id' => $s_academic_year_id,
		    'ds.student_id' => $s_student_id,
		    'ds.semester_type_id' => $s_semester_type_id
	    ));
	    return ($query->num_rows() >= 1) ? $query->result() : false;
    }

    public function submit_new_ofse($a_data, $s_ofse_period_id = false)
    {
        if ($s_ofse_period_id) {
            $this->db->update('dt_ofse', $a_data, ['oofse_period_id' => $s_ofse_period_id]);
            return true;
        }else{
            $this->db->insert('dt_ofse', $a_data);

            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }
    
    public function get_ofse_participants_list($s_academic_year_id, $s_semester_type_id, $s_study_program_id)
    {
	    $this->db->join('dt_student dstu', 'student_id');
	    $this->db->join('dt_personal_data dpd', 'personal_data_id');
	    $this->db->group_by('ds.student_id');
	    $query = $this->db->get_where('dt_score ds', array(
		    'ds.academic_year_id' => $s_academic_year_id,
		    'dstu.study_program_id' => $s_study_program_id,
		    'ds.semester_type_id' => $s_semester_type_id
	    ));
	    return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    
    public function get_ofse_lists_subject($a_filter_data = false)
    {
/*
        $this->db->from('dt_class_group cg');
        $this->db->join('dt_class_group_subject cgs', 'class_group_id');
        $this->db->join('dt_offered_subject ofs', 'offered_subject_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = ofs.study_program_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = ofs.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        if ($a_filter_data) {
            $this->db->where($a_filter_data);
        }
        $this->db->order_by('sn.subject_name');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
*/
        $this->db->select('*, ofs.program_id, ofs.study_program_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = ofs.study_program_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = ofs.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        $this->db->join('dt_class_group_subject cgs', 'cgs.offered_subject_id = ofs.offered_subject_id', 'LEFT');
        if ($a_filter_data) {
            $this->db->where($a_filter_data);
        }
        $this->db->order_by('sn.subject_name');
        $this->db->group_by('sn.subject_name');
        $this->db->group_by('ofs.study_program_id');
        $query = $this->db->get('dt_offered_subject ofs');
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_ofse_class($a_clause = false)
    {
        $this->db->select('*, ofs.program_id, ofs.study_program_id');
        $this->db->from('dt_class_group cg');
        $this->db->join('dt_class_group_subject cgs', 'class_group_id');
        $this->db->join('dt_offered_subject ofs', 'offered_subject_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = ofs.study_program_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = ofs.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('sn.subject_name');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_ofse_examiner($s_class_group_id, $a_filter_data = false)
    {
        $this->db->from('dt_class_group_lecturer cgl');
        $this->db->join('dt_employee emm', 'emm.employee_id = cgl.employee_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = emm.personal_data_id');
        $this->db->where('cgl.class_group_id', $s_class_group_id);
        if ($a_filter_data) {
            $this->db->where($a_filter_data);
        }
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_ofse_student($s_class_group_id, $a_filter_data = false)
    {
        $this->db->from('dt_score sc');
        // $this->db->join('dt_class_group cg', 'cg.class_group_id = sc.class_group_id');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->where('sc.class_group_id', $s_class_group_id);
        if ($a_filter_data) {
            $this->db->where($a_filter_data);
        }
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_ofse_member($a_clause = false, $b_group_student = false)
    {
        $this->db->select('*, st.program_id, st.study_program_id');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id');
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($b_group_student) {
            $this->db->group_by('sc.student_id');
        }

        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_class_group_ofse_student($s_class_group_id = false, $a_clause = false, $mbs_where_in = false)
    {
        $this->db->select('*, st.program_id, st.study_program_id');
        $this->db->from('dt_score ds');
        $this->db->join('dt_student st', 'st.student_id = ds.student_id');
        $this->db->join('dt_personal_data st_dpd', 'st.personal_data_id = st_dpd.personal_data_id');
        $this->db->join('ref_program rp', 'rp.program_id = st.program_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->where('score_approval','approved');
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
}
