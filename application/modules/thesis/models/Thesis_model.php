<?php
class Thesis_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_student_list_thesis($a_clause = false, $a_current_progress_in = false, $a_ordering = false)
    {
        $this->db->select('*, ts.*, st.academic_year_id "student_batch"');
        $this->db->from('thesis_students ts');
        $this->db->join('dt_student st', 'st.student_id = ts.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id');
        $this->db->join('thesis_students_advisor tsad', 'tsad.thesis_student_id = ts.thesis_student_id', 'left');
        $this->db->join('thesis_students_examiner tsex', 'tsex.thesis_student_id = ts.thesis_student_id', 'left');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_current_progress_in) {
            $this->db->where_in('ts.current_progress', $a_current_progress_in);
        }

        $this->db->group_by('ts.thesis_student_id');
        if ($a_ordering) {
            foreach ($a_ordering as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        else {
            $this->db->order_by('ts.date_added');
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
        // return $this->db->last_query();
    }

    public function submit_thesis_file($a_filedata, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('thesis_students_file', $a_filedata, $a_update_clause);
        }
        else {
            $this->db->insert('thesis_students_file', $a_filedata);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_list_thesis_file($a_clause = false) {
        $this->db->from('thesis_students_file sf');
        $this->db->join('thesis_students_log_status sls', 'sls.thesis_log_id = sf.thesis_log_id');
        $this->db->join('thesis_students ts', 'ts.thesis_student_id = sls.thesis_student_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $this->db->group_by('sf.thesis_filetype');
        $this->db->order_by('sls.date_added', 'DESC');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_thesis_list_files($a_clause = false)
    {
        $this->db->select('*, sf.date_added AS thesis_file_uploaded');
        $this->db->from('thesis_students_file sf');
        $this->db->join('thesis_students_log_status sls', 'sls.thesis_log_id = sf.thesis_log_id');
        $this->db->join('thesis_students ts', 'ts.thesis_student_id = sls.thesis_student_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $this->db->order_by('sls.date_added', 'DESC');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
        // return $this->db->last_query();
    }

    public function get_list_student_advisor($a_clause = false, $s_advisor_type = 'advisor', $b_approved_advisor = false, $b_proposed_advisor = false)
    {
        $this->db->select('*, ta.*');
        if ($s_advisor_type == 'examiner') {
            $this->db->from('thesis_students_examiner tsa');
        }
        else {
            $this->db->from('thesis_students_advisor tsa');
            if ($b_approved_advisor) {
                $this->db->like('tsa.advisor_type', 'approved_advisor');
            }
            else if ($b_proposed_advisor) {
                $this->db->like('tsa.advisor_type', 'proposed_advisor');
            }
        }

        $this->db->join('thesis_advisor ta', 'ta.advisor_id = tsa.advisor_id');
        $this->db->join('thesis_students ts', 'ts.thesis_student_id = tsa.thesis_student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = ta.personal_data_id');
        $this->db->join('ref_institution ri', 'ri.institution_id = ta.institution_id', 'left');
        $this->db->join('dt_employee em', 'em.personal_data_id = ta.personal_data_id', 'left');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $s_order = ($s_advisor_type == 'examiner') ? 'examiner_type' : 'advisor_type';
        $this->db->order_by($s_order);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_list_student_advisortest($a_clause = false, $s_advisor_type = 'advisor', $b_approved_advisor = false, $b_proposed_advisor = false)
    {
        $this->db->select('*, ta.*');
        if ($s_advisor_type == 'examiner') {
            $this->db->from('thesis_students_examiner tsa');
        }
        else {
            $this->db->from('thesis_students_advisor tsa');
            if ($b_approved_advisor) {
                $this->db->like('tsa.advisor_type', 'approved_advisor');
            }
            else if ($b_proposed_advisor) {
                $this->db->like('tsa.advisor_type', 'proposed_advisor');
            }
        }

        $this->db->join('thesis_advisor ta', 'ta.advisor_id = tsa.advisor_id');
        $this->db->join('thesis_students ts', 'ts.thesis_student_id = tsa.thesis_student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = ta.personal_data_id');
        $this->db->join('ref_institution ri', 'ri.institution_id = ta.institution_id', 'left');
        $this->db->join('dt_employee em', 'em.personal_data_id = ta.personal_data_id', 'left');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $s_order = ($s_advisor_type == 'examiner') ? 'examiner_type' : 'advisor_type';
        $this->db->order_by($s_order);
        $query = $this->db->get();
        // return ($query->num_rows() > 0) ? $query->result() : false;
        return $this->db->last_query();
    }

    public function is_advisor_examiner_defense($a_clause = false, $s_advisor_type = 'advisor')
    {
        if ($s_advisor_type == 'examiner') {
            $this->db->from('thesis_students_examiner tsa');
        }
        else {
            $this->db->from('thesis_students_advisor tsa');
        }

        $this->db->join('thesis_advisor ta', 'ta.advisor_id = tsa.advisor_id');
        $this->db->join('thesis_students ts', 'ts.thesis_student_id = tsa.thesis_student_id');
        $this->db->join('thesis_defense td', 'td.thesis_students_id = ts.thesis_student_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        
        if ($s_advisor_type == 'examiner') {
            $this->db->order_by('tsa.examiner_type');
        }
        else {
            $this->db->like('tsa.advisor_type', 'approved_advisor');
            $this->db->order_by('tsa.advisor_type');
        }
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function is_advisor_examiner($a_clause = false, $s_advisor_type = 'advisor', $b_approved_advisor = false)
    {
        if ($s_advisor_type == 'examiner') {
            $this->db->from('thesis_students_examiner tsa');
        }
        else {
            $this->db->from('thesis_students_advisor tsa');
        }

        $this->db->join('thesis_advisor ta', 'ta.advisor_id = tsa.advisor_id');
        $this->db->join('thesis_students ts', 'ts.thesis_student_id = tsa.thesis_student_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($b_approved_advisor) {
            $this->db->like('tsa.advisor_type', 'approved_advisor');
        }
        
        if ($s_advisor_type == 'examiner') {
            $this->db->order_by('tsa.examiner_type');
        }
        else {
            $this->db->order_by('tsa.advisor_type');
        }
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function insert_thesis_defense($a_defense_data)
    {
        $this->db->insert('thesis_defense', $a_defense_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function update_thesis_defense($a_defense_data, $s_thesis_defense_id)
    {
        $this->db->update('thesis_defense', $a_defense_data, [
            'thesis_defense_id' => $s_thesis_defense_id
        ]);
        return true;
    }

    public function remove_advisor_data($s_thesis_student_id)
    {
        $this->db->where('thesis_student_id', $s_thesis_student_id);
        $this->db->like('advisor_type', 'approved_advisor');
        $this->db->delete('thesis_students_advisor');
        return true;
    }

    public function force_remove_data($s_table_name, $a_key_delete)
    {
        $this->db->delete($s_table_name, $a_key_delete);
        return true;
    }

    public function force_insert_data($s_table_name, $a_data)
    {
        $this->db->insert($s_table_name, $a_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function submit_thesis_student($a_thesis_student_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('thesis_students', $a_thesis_student_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('thesis_students', $a_thesis_student_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function submit_thesis_score_presentation($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('thesis_score_presentation', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('thesis_score_presentation', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function submit_thesis_score_evaluation($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('thesis_score_evaluation', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('thesis_score_evaluation', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function submit_thesis_score($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('thesis_score', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('thesis_score', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_thesis_evaluation($a_clause = false)
    {
        $this->db->from('thesis_score ts');
        $this->db->join('thesis_score_evaluation te', 'te.thesis_score_id = ts.thesis_score_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('te.timestamp', 'DESC');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_thesis_presentation($a_clause = false)
    {
        $this->db->from('thesis_score ts');
        $this->db->join('thesis_score_presentation tp', 'tp.thesis_score_id = ts.thesis_score_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('tp.timestamp', 'DESC');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function delete_thesis_defense_attendance($s_thesis_defense_id)
    {
        $this->db->delete('thesis_defenses_absence', ['thesis_defense_id' => $s_thesis_defense_id]);
        return true;
    }

    public function submit_thesis_defense_attendance($a_absence_data)
    {
        $this->db->insert('thesis_defenses_absence', $a_absence_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_thesis_defense_student($a_clause = false)
    {
        $this->db->select('*, ts.thesis_student_id, st.study_program_id, td.academic_year_id AS defense_academic_year_id, td.semester_type_id AS defense_semester_type_id');
        $this->db->from('thesis_defense td');
        $this->db->join('thesis_students ts', 'ts.thesis_student_id = td.thesis_students_id');
        $this->db->join('dt_student st', 'st.student_id = ts.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function submit_log_status_note($a_data)
    {
        $this->db->insert('thesis_student_log_notes', $a_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function submit_log_status($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('thesis_students_log_status', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('thesis_students_log_status', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function t($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('thesis_students', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('thesis_students', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function submit_student_advisor($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('thesis_students_advisor', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('thesis_students_advisor', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function submit_student_examiner($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('thesis_students_examiner', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('thesis_students_examiner', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function submit_advisor($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('thesis_advisor', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('thesis_advisor', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_thesis_by_status_log($a_clause = false, $a_status_in = false)
    {
        $this->db->select('*, st.academic_year_id AS "student_batch", tsl.date_added AS "log_date_added"');
        $this->db->from('thesis_student_log_status tsl');
        // $this->db->from('thesis_student ts');
        $this->db->join('thesis_student ts', 'ts.thesis_student_id = tsl.thesis_student_id');
        $this->db->join('dt_student st', 'st.student_id = ts.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('thesis_period tp', 'tp.period_id=  ts.period_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id', 'LEFT');

        if ($a_clause) {
            $this->db->where($a_clause);
        }
        
        if ($a_status_in) {
            $this->db->where_in('tsl.thesis_status', $a_status_in);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function update_status_thesis($s_status, $s_thesis_student_id)
    {
        $this->db->update('thesis_student', ['current_status' => $s_status], ['thesis_student_id' => $s_thesis_student_id]);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_thesis_list_by_log($a_clause = false)
    {
        $this->db->select('*, tsl.date_added AS "log_date_added", st.academic_year_id AS "student_batch", tsl.academic_year_id');
        $this->db->from('thesis_students ts');
        $this->db->join('dt_student st', 'st.student_id = ts.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('thesis_students_log_status tsl', 'tsl.thesis_student_id = ts.thesis_student_id');
        $this->db->order_by('tsl.date_added', 'DESC');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $this->db->group_by('ts.thesis_student_id');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function testget_thesis_list_by_log($a_clause = false)
    {
        $this->db->select('*, tsl.date_added AS "log_date_added", st.academic_year_id AS "student_batch", tsl.academic_year_id');
        $this->db->from('thesis_students ts');
        $this->db->join('dt_student st', 'st.student_id = ts.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('thesis_students_log_status tsl', 'tsl.thesis_student_id = ts.thesis_student_id');
        $this->db->order_by('tsl.date_added', 'DESC');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $this->db->group_by('ts.thesis_student_id');
        $query = $this->db->get();
        return $this->db->last_query();
    }

    public function get_thesis_log($s_thesis_student_id, $a_clause = false)
    {
        $this->db->from('thesis_students_log_status tsl');
        $this->db->where('tsl.thesis_student_id', $s_thesis_student_id);
        $this->db->order_by('tsl.date_added', 'DESC');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_thesis_log_student($a_clause = false)
    {
        $this->db->from('thesis_student ts');
        $this->db->join('thesis_student_log_status tsl', 'tsl.thesis_student_id = ts.thesis_student_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('tsl.date_added', 'DESC');
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function check_is_advisor($a_clause = false, $s_advisor_thpe = 'approved_advisor')
    {
        $this->db->from('thesis_student_advisor tsa');
        $this->db->join('thesis_advisor ta', 'ta.advisor_id = tsa.advisor_id');
        $this->db->join('thesis_student ts', 'ts.thesis_student_id = tsa.thesis_student_id');
        $this->db->join('thesis_period tp', 'tp.period_id = ts.period_id');
        $this->db->like('tsa.advisor_type', $s_advisor_thpe);
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('tsa.advisor_type');
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
    
    public function check_is_examiner($a_clause = false)
    {
        $this->db->from('thesis_student_examiner tse');
        $this->db->join('thesis_advisor ta', 'ta.advisor_id = tse.advisor_id');
        $this->db->join('thesis_student ts', 'ts.thesis_student_id = tse.thesis_student_id');
        $this->db->join('thesis_period tp', 'tp.period_id = ts.period_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('tse.examiner_type');
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_advisor_student($s_thesis_student_id, $s_advisor_thpe = 'approved_advisor', $a_clause = false)
    {
        $this->db->select('*, ta.institution_id, ta.personal_data_id');
        $this->db->from('thesis_student_advisor tsa');
        $this->db->join('thesis_advisor ta', 'ta.advisor_id = tsa.advisor_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = ta.personal_data_id');
        $this->db->join('ref_institution ri', 'ri.institution_id = ta.institution_id', 'left');
        $this->db->where('tsa.thesis_student_id', $s_thesis_student_id);
        $this->db->like('tsa.advisor_type', $s_advisor_thpe);
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('tsa.advisor_type');
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_examiner_student($s_thesis_student_id, $a_clause = false)
    {
        $this->db->select('*, ta.institution_id, ta.personal_data_id');
        $this->db->from('thesis_student_examiner tse');
        $this->db->join('thesis_advisor ta', 'ta.advisor_id = tse.advisor_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = ta.personal_data_id');
        $this->db->join('ref_institution ri', 'ri.institution_id = ta.institution_id', 'left');
        $this->db->where('tse.thesis_student_id', $s_thesis_student_id);
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('tse.examiner_type');
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_advisor_list($s_term = false, $a_clause = false, $b_limit = false)
    {
        $this->db->select('*, ta.institution_id, ta.personal_data_id');
        $this->db->from('thesis_advisor ta');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = ta.personal_data_id');
        $this->db->join('ref_institution ri', 'ri.institution_id = ta.institution_id', 'left');
        $this->db->join('dt_employee em', 'em.personal_data_id = ta.personal_data_id', 'left');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($s_term) {
            $this->db->like('pd.personal_data_name', $s_term);
        }

        if ($b_limit) {
            $this->db->limit(10, 0);
        }
        
        $query  = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_status_thesis($s_thesis_student_id, $a_clause = false)
    {
        $this->db->select('*, tsl.date_added AS "status_added", tsn.date_added AS "note_added", tsl.thesis_log_id');
        $this->db->from('thesis_students_log_status tsl');
        $this->db->join('thesis_student_log_notes tsn', 'tsn.thesis_logs_id = tsl.thesis_log_id', 'LEFT');
        $this->db->order_by('status_added', 'DESC');
        $this->db->order_by('note_added', 'DESC');
        $this->db->where('tsl.thesis_student_id', $s_thesis_student_id);
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_thesis_student($a_clause = false, $s_field_order = false, $s_ordering = 'ASC')
    {
        $this->db->select('*, st.academic_year_id AS "student_batch"');
        $this->db->from('thesis_students ts');
        $this->db->join('dt_student st', 'st.student_id = ts.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id');
        // $this->db->join('thesis_period tp', 'tp.period_id = ts.period_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        // $this->db->order_by('tp.period_name', 'DESC');
        if ($s_field_order) {
            $this->db->order_by($s_field_order, $s_ordering);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    // public function get_where($s_table_name, $a_clause = false)
	// {
	// 	if ($a_clause) {
	// 		$query = $this->tdb->get_where($s_table_name, $a_clause);
	// 	}else{
	// 		$query = $this->tdb->get($s_table_name);
	// 	}

	// 	return ($query->num_rows() > 0) ? $query->result() : false;
	// }

    public function push_data($s_table_name, $a_data)
    {
        $this->db->insert($s_table_name, $a_data);
        return($this->db->affected_rows() > 0) ? true : false;
    }
}
