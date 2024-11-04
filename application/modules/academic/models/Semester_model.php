<?php
class Semester_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get_graduation_checklist($a_clause = false) {
        $this->db->select('*, gc.date_added');
        $this->db->from('dt_graduation_checklist gc');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = gc.checklist_by');
        $this->db->join('dt_employee em', 'em.personal_data_id = pd.personal_data_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_student_semester_number($s_student_id, $a_clause = false)
    {
        $this->db->from('dt_student_semester ss');
        $this->db->join('ref_semester sm', 'sm.semester_id = ss.semester_id');
        $this->db->where('ss.student_id', $s_student_id);
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_semester_setting_request($a_clause = false)
    {
        $this->db->select('ssr.*, pdr.personal_data_name AS "request_by", pda.personal_data_name AS "aproved_by"');
        $this->db->from('dt_semester_setting_request ssr');
        $this->db->join('dt_personal_data pdr', 'pdr.personal_data_id = ssr.personal_data_id_request');
        $this->db->join('dt_personal_data pda', 'pda.personal_data_id = ssr.personal_data_id_approve', 'LEFT');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $this->db->order_by('date_added', 'DESC');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function save_request($a_data, $s_request_semester_setting_id = false)
    {
        if ($s_request_semester_setting_id) {
            $this->db->update('dt_semester_setting_request', $a_data, ['request_semester_setting_id' => $s_request_semester_setting_id]);
            return true;
        }else{
            $this->db->insert('dt_semester_setting_request', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }
    
    public function get_ofse_semester()
    {
	    $mbo_active_semester = $this->get_active_semester();
	    if($mbo_active_semester){
		    $query = $this->db->get_where('ref_semester_type', array('semester_type_master' => $mbo_active_semester->semester_type_id));
		    $mbo_ofse_semester = ($query->num_rows() == 1) ? $query->first_row() : false;
		    return array('active_semester' => $mbo_active_semester, 'ofse_semester' => $mbo_ofse_semester);
	    }
	    else{
		    return false;
	    }
    }
    
    public function get_active_semester()
    {
        $this->db->join('ref_semester_type sm', 'sm.semester_type_id = ss.semester_type_id');
        // $this->db->where
	    $query = $this->db->get_where('dt_semester_settings ss', array('ss.semester_status' => 'active'));
	    return ($query->num_rows() == 1) ? $query->first_row() : false;
    }

    public function inactive_semester_academic()
    {
        $this->db->update('dt_semester_settings', ['semester_status' => 'inactive']);
    }

    public function save_semester_setttings($a_semester_setting_data, $a_clause_update = false)
    {
        if ($a_clause_update) {
            $this->db->update('dt_semester_settings', $a_semester_setting_data, $a_clause_update);
        }else {
            $this->db->insert('dt_semester_settings', $a_semester_setting_data);

            $s_directory_file = APPPATH.'uploads/academic/'.$a_semester_setting_data['academic_year_id'].$a_semester_setting_data['semester_type_id'].'/';
            if(!file_exists($s_directory_file)){
                mkdir($s_directory_file, 0755);
            }
        }

        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_semester_setting($a_filter_data = false, $s_order_field = false, $s_ordering = false, $s_sort_all = 'DESC')
    {
        $this->db->from('dt_semester_settings dss');
        $this->db->join('dt_academic_year day', 'dss.academic_year_id = day.academic_year_id');
        $this->db->join('ref_semester_type rst', 'rst.semester_type_id = dss.semester_type_id');
        if ($a_filter_data) {
            $this->db->where($a_filter_data);
        }

        if ($s_order_field) {
            $this->db->order_by($s_order_field, $s_ordering);
        }else{
            $this->db->order_by('dss.academic_year_id', $s_sort_all);
            $this->db->order_by('dss.semester_type_id', $s_sort_all);
        }
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_semester_setting_list()
    {
        $this->db->from('dt_semester_settings dss');
        $this->db->join('dt_academic_year day', 'dss.academic_year_id = day.academic_year_id');
        $this->db->join('ref_semester_type rst', 'rst.semester_type_id = dss.semester_type_id');
        
        $this->db->order_by('dss.academic_year_id', 'ASC');
        $this->db->order_by('dss.semester_type_id', 'ASC');
            
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_semester_lists($a_clause = false, $s_clause_in = false)
    {
        $this->db->from('ref_semester rs');
        $this->db->join('ref_semester_type rst', 'semester_type_id');
        if ($s_clause_in) {
            $this->db->where_in($s_clause_in, $a_clause);
        }else if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('semester_id', 'asc');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function test($a_clause = false, $s_clause_in = false)
    {
        $this->db->from('ref_semester rs');
        $this->db->join('ref_semester_type rst', 'semester_type_id');
        if ($s_clause_in) {
            $this->db->where_in($s_clause_in, $a_clause);
        }else if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('semester_id', 'asc');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : $this->db->last_query();
    }

    public function get_semester_number_regular_list()
    {
        $this->db->from('ref_semester smm');
        $this->db->join('ref_semester_type smt', 'smt.semester_type_id = smm.semester_type_id');
        $this->db->where_in('smm.semester_type_id', [1,2]);
        $this->db->order_by('smm.semester_id');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_semester_student($s_student_id, $a_param_data = false, $a_type_in = false)
    {
        $this->db->select('*, ss.academic_year_id AS "semester_year_id", stu.program_id');
        $this->db->from('dt_student_semester ss');
        $this->db->join('dt_student stu', 'ss.student_id = stu.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = stu.personal_data_id');
        $this->db->join('ref_semester_type st', 'st.semester_type_id = ss.semester_type_id');
        $this->db->join('ref_institution ri', 'ri.institution_id = ss.institution_id', 'LEFT');
        $this->db->join('dt_address dad', 'dad.address_id = ri.address_id', 'LEFT');
        $this->db->join('ref_country rc', 'rc.country_id = dad.country_id', 'LEFT');
        if ($a_param_data) {
            $this->db->where($a_param_data);
        }

        if ($a_type_in) {
            $this->db->where_in('ss.semester_type_id', $a_type_in);
        }

        $this->db->where('stu.student_id', $s_student_id);
        $this->db->order_by('ss.academic_year_id', 'ASC');
        $this->db->order_by('ss.semester_type_id', 'ASC');
        $q = $this->db->get();

        return ($q->num_rows() > 0) ? $q->result() : false;
        // return ($q->num_rows() > 0) ? $q->result() : $this->db->last_query();
    }

    public function get_semester_type_lists($s_semester_type_id = false, $a_clause = false, $a_type_in = false)
    {
        $this->db->from('ref_semester_type rst');
        if ($s_semester_type_id) {
            $this->db->where('semester_type_id', $s_semester_type_id);
        }
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        if ($a_type_in) {
            $this->db->where_in('semester_type_id', $a_type_in);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_semester_student_personal_data($a_clause = false, $a_type_in = false)
    {
        $this->db->select('*, st.program_id, dss.academic_year_id AS "semester_academic_year_id", dss.semester_type_id AS "semester_semester_type_id", rst.semester_type_id AS "semester_type_type_id"');
        $this->db->from('dt_student_semester dss');
        $this->db->join('dt_student st', 'st.student_id = dss.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('ref_semester_type rst', 'rst.semester_type_id = dss.semester_type_id');
        $this->db->where('student_semester_status', 'active');
        $this->db->order_by('dss.academic_year_id','ASC');
        $this->db->order_by('dss.semester_type_id','ASC');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_type_in) {
            $this->db->where_in('dss.semester_type_id', $a_type_in);
        }

        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_student_semester_number_list($s_student_id, $a_clause = false)
    {
        $this->db->from('dt_score sc');
        $this->db->join('ref_semester sm', 'sm.semester_id = sc.semester_id');
        $this->db->where('sc.student_id', $s_student_id);

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $this->db->group_by('sc.semester_id');
        $q = $this->db->get();
        // return ($q->num_rows() > 0) ? $q->result() : false;
        return $this->db->last_query();
    }

    public function get_student_semester($a_clause = false)
    {
        $this->db->select('*, dss.timestamp AS "semester_timestamp", st.academic_year_id AS "batch", st.program_id, st.study_program_id, dss.academic_year_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->join('dt_student st', 'st.student_id = dss.student_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $query = $this->db->get('dt_student_semester dss');
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function remove_student_semeter($a_clause)
    {
        $this->db->delete('dt_student_semester', $a_clause);
        
        return ($this->db->affected_rows() > 0) ? true : false;
    }
    
    private function insert_student_semester($a_student_semester_data)
    {
	    $this->db->insert('dt_student_semester', $a_student_semester_data);
		return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function save_student_semester($a_student_semester_data, $a_clause_update = false)
    {
	    if($a_clause_update){
		    $query = $this->db->get_where('dt_student_semester', $a_clause_update);
		    if($query->num_rows() >= 1){
			    $this->db->update('dt_student_semester', $a_student_semester_data, $a_clause_update);
	            return true;
		    }
		    else{
	            // array_merge($a_student_semester_data, $a_clause_update);
	            $a_student_semester_data = array_merge($a_student_semester_data, $a_clause_update);
                $this->insert_student_semester($a_student_semester_data);
                return ($this->db->affected_rows() > 0) ? true : false;
		    }
	    }
	    else{
            $this->insert_student_semester($a_student_semester_data);
            return ($this->db->affected_rows() > 0) ? true : false;
	    }
    }

    public function get_student_start_semester($s_student_id = false, $a_clause = false)
    {
        $this->db->select('*, dss.academic_year_id, dss.semester_type_id');
        $this->db->join('dt_student st', 'st.student_id = dss.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');

        if ($s_student_id) {
            $this->db->where('dss.student_id', $s_student_id);
        }

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $this->db->order_by('dss.academic_year_id', 'ASC');
        $this->db->order_by('dss.semester_type_id', 'ASC');
        $q = $this->db->get('dt_student_semester dss');
        return ($q->num_rows() > 0) ?$q->first_row() : false;
    }

    // public function get_semester_before()
    // {
    //     $this->db->order_by('semester_end_date', 'DESC');
    //     $query = $this->db->get('dt_semester_settings');
    // }
}
