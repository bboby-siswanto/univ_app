<?php
class Student_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->config->load('portal_config_production');
	}

	function get_student_path($a_clause = false) {
		$this->db->select('st.academic_year_id AS "student_batch", sp.study_program_abbreviation, st.personal_data_id');
		$this->db->from('dt_student st');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
		$this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$o_student = $query->first_row();
			if ((!is_null($o_student->student_batch)) AND (!is_null($o_student->study_program_abbreviation))) {
				$s_path = STUDENTPATH.$o_student->student_batch.'/'.$o_student->study_program_abbreviation.'/'.$o_student->personal_data_id.'/';
				return $s_path;
			}
		}
		return false;
	}

	public function get_student_by_name_filtered($s_term, $a_clause = false)
	{
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$this->db->select('*, st.academic_year_id AS "student_batch"');
		$this->db->from('dt_student st');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
		$this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
		$this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id');
		$this->db->like('pd.personal_data_name', $s_term);
		$this->db->limit(20, 0);
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function remove_record_file($s_record_file_id = false, $s_record_id = false)
	{
		if ($s_record_id) {
			$this->db->delete('dt_personal_data_record_files', ['record_id' => $s_record_id]);
		}
		else if ($s_record_file_id) {
			$this->db->delete('dt_personal_data_record_files', ['record_file_id' => $s_record_file_id]);
		}
		else {
			return false;
		}
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function get_file_record($a_clause = false)
	{
		$this->db->from('dt_personal_data_record_files pdf');
		$this->db->join('dt_personal_data_record pdr', 'pdr.record_id = pdf.record_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function insert_record_file($a_data)
	{
		$this->db->insert('dt_personal_data_record_files', $a_data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function save_sign_data($a_data)
	{
		$this->db->insert('dt_student_document_token', $a_data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function save_student_alumni($a_data, $s_student_alumni_id = false)
	{
		if ($s_student_alumni_id) {
			$this->db->update('dt_student_alumni', $a_data, ['alumni_id' => $s_student_alumni_id]);
			return true;
		}else{
			$this->db->insert('dt_student_alumni', $a_data);
			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}

	public function get_record_file($a_clause = false)
	{
		$this->db->from('dt_personal_data_record_files pdf');
		$this->db->join('dt_personal_data_record pdr', 'pdr.record_id = pdf.record_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_record_list($s_personal_data_id, $a_clause = false)
	{
		$this->db->select('*, pdr.date_added AS "record_added"');
		$this->db->from('dt_personal_data_record pdr');
		$this->db->join('dt_employee em', 'em.employee_id = pdr.employee_id');
		$this->db->join('dt_personal_data pdm', 'pdm.personal_data_id = em.personal_data_id');
		$this->db->where('pdr.personal_data_id', $s_personal_data_id);
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$q = $this->db->get();

		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function submit_record($a_data, $a_clause_update = false)
	{
		if ($a_clause_update) {
			$this->db->update('dt_personal_data_record', $a_data, $a_clause_update);
			return true;
		}else{
			$this->db->insert('dt_personal_data_record', $a_data);
			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}

	public function get_alumni_data($s_student_id, $a_param_data = false)
	{
		$this->db->select("
			*,
			ds.personal_data_id,
			pd_cob.country_name AS 'country_of_birth_country_name',
			pd_citi.country_name AS 'citizenship_country_name',
			ds.program_id,
			ds.study_program_id
		");

		$this->db->from('dt_student_alumni psa');
		$this->db->join('dt_student ds', 'ds.student_id = psa.student_id');
		$this->db->join('dt_personal_data dpd', 'dpd.personal_data_id = ds.personal_data_id');

		$this->db->join('ref_study_program rsp', 'study_program_id', 'LEFT');
		$this->db->join('ref_faculty fc', 'fc.faculty_id = rsp.faculty_id', 'LEFT');
		$this->db->join('dt_personal_address dpa', 'dpa.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('ref_country pd_cob', 'pd_cob.country_id = dpd.country_of_birth', 'LEFT');
		$this->db->join('ref_country pd_citi', 'pd_citi.country_id = dpd.citizenship_id', 'LEFT');
		$this->db->join('dt_address da', 'da.address_id = dpa.address_id', 'LEFT');
		$this->db->join('dt_family_member dfm', 'dfm.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('dt_academic_history dah', 'dah.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('ref_institution ri', 'ri.institution_id = dah.institution_id', 'LEFT');

		$this->db->where('ds.student_id', $s_student_id);
		if ($a_param_data) {
			$this->db->where($a_param_data);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->first_row() : false;
	}
	
	public function get_total_students(
		$s_academic_year_id, 
		$s_start_date = false, 
		$s_end_date = false, 
		$s_student_status = false, 
		$s_study_program_id = false
	)
	{
		$a_clause['ds.academic_year_id'] = $s_academic_year_id;
		
		($s_start_date) ? $a_clause['ds.date_added >='] = $s_start_date : '';
		($s_end_date) ? $a_clause['ds.date_added <='] = $s_end_date : '';
		($s_student_status) ? $a_clause['ds.student_status'] = $s_student_status : '';
		($s_study_program_id) ? $a_clause['ds.study_program_id'] = $s_study_program_id : '';
		
		$query = $this->db->get_where('dt_student ds', $a_clause);
		
		return $query->result();
	}

	public function get_student_alumni($s_alumni_email)
	{
		$a_allowed_status = $this->config->item('student_allowed_status');
		$this->db->where_in('student_status', $a_allowed_status);
		$this->db->where('student_alumni_email', $s_alumni_email);
		$query = $this->db->get('dt_student');
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}
	
	public function get_student_by_email($s_student_email)
	{
		$a_allowed_status = $this->config->item('student_allowed_status');
		if ($s_student_email != 'firstname.lastname@stud.iuli.ac.id') {
			$this->db->where_in('student_status', $a_allowed_status);
		}
		
		$this->db->where('student_email', $s_student_email);
		$query = $this->db->get('dt_student');
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}
	
	public function get_student_by_name($s_student_name, $a_clause= false, $mbs_status = false)
	{
		$this->db->join('dt_academic_year dacy', 'academic_year_id');
		$this->db->join('ref_study_program rsp', 'study_program_id');
		$this->db->join('dt_personal_data dpd', 'personal_data_id');
		$this->db->like('dpd.personal_data_name', $s_student_name);
		
		if ($mbs_status == 'academic') {
			$this->db->where_in('ds.student_status', ['active', 'graduated', 'onleave']);
		}else{
			$this->db->where('ds.student_status != ', 'resign');
		}

		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$query = $this->db->get('dt_student ds');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function count_student($s_academic_year_id, $s_status = false)
	{
		$this->db->where('academic_year_id', $s_academic_year_id);
		if($s_status){
			$this->db->where('student_status', $s_status);
		}
		$this->db->from('dt_student');
		return $this->db->count_all_results();
	}

	function get_student_personal($a_clause = false) {
		$this->db->from('dt_student st');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
		$this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$this->db->group_by('st.personal_data_id');
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_student_filtered($a_clause = false, $a_status_in = false, $mbs_custom_ordering = false, $a_prodi_in = false, $a_program_in = false, $a_order = false)
	{
		$this->db->select("
			*,
			ds.student_status AS 'status_student',
			ds.portal_id AS 'student_portal_id',
			ds.date_added AS 'register_date',
			ds.personal_data_id,
			ds.academic_year_id,
			ds.study_program_id,
			ds.program_id AS 'student_program',
			pd_cob.country_name AS 'country_of_birth_country_name',
			pd_citi.country_name AS 'citizenship_country_name',
			da.country_id AS 'address_country_id'
		");
		$this->db->from('dt_student ds');
		$this->db->join('dt_personal_data dpd', 'ds.personal_data_id = dpd.personal_data_id');
		$this->db->join('ref_program rpg', 'rpg.program_id = ds.program_id', 'LEFT');
		$this->db->join('ref_study_program rsp', 'rsp.study_program_id = ds.study_program_id', 'LEFT');
		$this->db->join('ref_faculty fc', 'fc.faculty_id = rsp.faculty_id', 'LEFT');
		$this->db->join('dt_personal_address dpa', 'dpa.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('ref_country pd_cob', 'pd_cob.country_id = dpd.country_of_birth', 'LEFT');
		$this->db->join('ref_country pd_citi', 'pd_citi.country_id = dpd.citizenship_id', 'LEFT');
		$this->db->join('dt_address da', 'da.address_id = dpa.address_id', 'LEFT');
		$this->db->join('dt_family_member dfm', 'dfm.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('dt_academic_history dah', 'dah.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('ref_ocupation oc', 'oc.ocupation_id = dpd.ocupation_id', 'LEFT');
		$this->db->join('ref_institution ri', 'ri.institution_id = dah.institution_id', 'LEFT');
		if($a_clause){
			if ($this->session->userdata('student_id') == 'd9868ebf-ef1a-4ede-80df-b16ea0df93ee') {
				if (array_key_exists('ds.student_status', $a_clause)) {
					unset($a_clause['ds.student_status']);
				}
			}
			$this->db->where($a_clause);
		}

		if ($a_status_in) {
			$this->db->where_in('ds.student_status', $a_status_in);
		}

		if ($a_prodi_in) {
			$this->db->where_in('ds.study_program_id', $a_prodi_in);
		}

		if ($a_program_in) {
			$this->db->where_in('ds.program_id', $a_program_in);
		}

		if ($a_order) {
			foreach ($a_order as $key => $value) {
				$this->db->order_by($key, $value);
			}
		}
		else if ($mbs_custom_ordering) {
			$this->db->order_by($mbs_custom_ordering);
		}else{
			$this->db->order_by('ds.student_status');
			$this->db->order_by('ds.academic_year_id', 'DESC');
			$this->db->order_by('ds.program_id');
			$this->db->order_by('fc.faculty_abbreviation');
			$this->db->order_by('rsp.study_program_name');
		}
		$this->db->group_by('ds.student_id');
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_student_colectif_filtered($a_clause = false, $a_status_in = false, $a_student_id_in = false)
	{
		$this->db->select("
			*,
			ds.student_status AS 'status_student',
			ds.portal_id AS 'student_portal_id',
			ds.date_added AS 'register_date',
			ds.personal_data_id,
			ds.academic_year_id,
			ds.study_program_id,
			ds.program_id AS 'student_program',
			pd_cob.country_name AS 'country_of_birth_country_name',
			pd_citi.country_name AS 'citizenship_country_name'
		");
		$this->db->from('dt_student ds');
		$this->db->join('dt_personal_data dpd', 'ds.personal_data_id = dpd.personal_data_id');
		$this->db->join('ref_program rpg', 'rpg.program_id = ds.program_id', 'LEFT');
		$this->db->join('ref_study_program rsp', 'rsp.study_program_id = ds.study_program_id', 'LEFT');
		$this->db->join('ref_faculty fc', 'fc.faculty_id = rsp.faculty_id', 'LEFT');
		$this->db->join('dt_personal_address dpa', 'dpa.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('ref_country pd_cob', 'pd_cob.country_id = dpd.country_of_birth', 'LEFT');
		$this->db->join('ref_country pd_citi', 'pd_citi.country_id = dpd.citizenship_id', 'LEFT');
		$this->db->join('dt_address da', 'da.address_id = dpa.address_id', 'LEFT');
		$this->db->join('dt_family_member dfm', 'dfm.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('dt_academic_history dah', 'dah.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('ref_institution ri', 'ri.institution_id = dah.institution_id', 'LEFT');
		if($a_clause){
			$this->db->where($a_clause);
		}

		if ($a_status_in) {
			$this->db->where_in('ds.student_status', $a_status_in);
		}

		if ($a_student_id_in) {
			$this->db->where_in('ds.student_id', $a_student_id_in);
		}
		
		$this->db->order_by('ds.student_status');
		$this->db->group_by('ds.student_id');
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_student_filtered_pmb_function($a_clause = false, $a_status_in = false)
	{
		$this->db->select("
			*,
			ds.student_status AS 'status_student',
			ds.portal_id AS 'student_portal_id',
			ds.date_added AS 'register_date',
			ds.timestamp AS 'register_timestamp',
			ds.personal_data_id,
			ds.academic_year_id,
			ds.study_program_id,
			ds.program_id AS 'student_program',
			pd_cob.country_name AS 'country_of_birth_country_name',
			pd_citi.country_name AS 'citizenship_country_name'
		");
		$this->db->from('dt_student ds');
		$this->db->join('dt_personal_data dpd', 'ds.personal_data_id = dpd.personal_data_id');
		$this->db->join('ref_program rpg', 'rpg.program_id = ds.program_id', 'LEFT');
		$this->db->join('ref_study_program rsp', 'rsp.study_program_id = ds.study_program_id', 'LEFT');
		$this->db->join('ref_faculty fc', 'fc.faculty_id = rsp.faculty_id', 'LEFT');
		$this->db->join('dt_personal_address dpa', 'dpa.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('ref_country pd_cob', 'pd_cob.country_id = dpd.country_of_birth', 'LEFT');
		$this->db->join('ref_country pd_citi', 'pd_citi.country_id = dpd.citizenship_id', 'LEFT');
		$this->db->join('dt_address da', 'da.address_id = dpa.address_id', 'LEFT');
		$this->db->join('dt_family_member dfm', 'dfm.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('dt_academic_history dah', 'dah.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('ref_institution ri', 'ri.institution_id = dah.institution_id', 'LEFT');
		if($a_clause){
			$this->db->where($a_clause);
		}

		if ($a_status_in) {
			$this->db->where_in('ds.student_status', $a_status_in);
		}
		$this->db->order_by('ds.student_date_enrollment');
		$this->db->group_by('ds.student_id');
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}

	public function get_student_list_data($a_clause = false, $a_status_in = false, $a_order = false, $a_prodi_in = false)
	{
		$this->db->select("
			*,
			ds.student_status AS 'status_student',
			ds.date_added AS 'register_date',
			ds.personal_data_id,
			ds.academic_year_id,
			ds.study_program_id,
			ds.program_id
		");
		$this->db->from('dt_student ds');
		$this->db->join('dt_personal_data dpd', 'ds.personal_data_id = dpd.personal_data_id');
		$this->db->join('ref_study_program rsp', 'rsp.study_program_id = ds.study_program_id', 'LEFT');
		$this->db->join('ref_faculty fc', 'fc.faculty_id = rsp.faculty_id', 'LEFT');
		
		if($a_clause){
			$this->db->where($a_clause);
		}

		if ($a_status_in) {
			$this->db->where_in('ds.student_status', $a_status_in);
		}

		if ($a_prodi_in) {
			$this->db->where_in('ds.study_program_id', $a_prodi_in);
		}

		if ($a_order) {
			foreach ($a_order as $key => $value) {
				$this->db->order_by($key, $value);
			}
		}
		$this->db->group_by('ds.student_id');
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	public function get_student_list_data_temp($a_clause = false, $a_student_number = false, $a_order = false, $a_prodi_in = false)
	{
		$this->db->select("
			*,
			ds.student_status AS 'status_student',
			ds.date_added AS 'register_date',
			ds.personal_data_id,
			ds.academic_year_id,
			ds.study_program_id,
			ds.program_id
		");
		$this->db->from('dt_student ds');
		$this->db->join('dt_personal_data dpd', 'ds.personal_data_id = dpd.personal_data_id');
		$this->db->join('ref_study_program rsp', 'rsp.study_program_id = ds.study_program_id', 'LEFT');
		$this->db->join('ref_faculty fc', 'fc.faculty_id = rsp.faculty_id', 'LEFT');
		
		if($a_clause){
			$this->db->where($a_clause);
		}

		if ($a_student_number) {
			$this->db->where_in('ds.student_number', $a_student_number);
		}

		if ($a_prodi_in) {
			$this->db->where_in('ds.study_program_id', $a_prodi_in);
		}

		if ($a_order) {
			foreach ($a_order as $key => $value) {
				$this->db->order_by($key, $value);
			}
		}
		$this->db->group_by('ds.student_id');
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_student_by_personal_data_id($s_personal_data_id, $a_clause = false)
	{
		$this->db->select('*, dt_student.study_program_id, dt_student.program_id, dt_student.academic_year_id');
		$this->db->join('dt_academic_year', 'academic_year_id', 'LEFT');
		$this->db->join('ref_study_program', 'study_program_id', 'LEFT');
		$this->db->where('personal_data_id', $s_personal_data_id);
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$query = $this->db->get('dt_student');
		// $query = $this->db->get_where('dt_student', array('personal_data_id' => $s_personal_data_id));
		return ($query->num_rows() >= 1) ? $query->first_row() : false;
	}
	
	public function create_new_student($a_student_data)
	{
		if(is_object($a_student_data)){
			$a_student_data = (array)$a_student_data;
		}
		
		if(!array_key_exists('student_id', $a_student_data)){
			$a_student_data['student_id'] = $this->uuid->v4();
		}
		
		if(array_key_exists('portal_sync', $a_student_data)){
			unset($a_student_data['portal_sync']);
		}
		
		$this->db->insert('dt_student', $a_student_data);
		
		return $a_student_data['student_id'];
	}
	
	public function update_student_data($a_student_data, $s_student_id)
	{
		if((is_array($a_student_data)) AND (array_key_exists('portal_sync', $a_student_data))){
			unset($a_student_data['portal_sync']);
		}
		$this->db->update('dt_student', $a_student_data, array('student_id' => $s_student_id));
		return true;
	}

	public function insert_student_scholarship($a_data)
	{
		$this->db->insert('dt_personal_data_scholarship', $a_data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function update_student_scholarship($a_data, $a_clause)
	{
		$this->db->update('dt_personal_data_scholarship', $a_data, $a_clause);
		return true;
	}

	public function update_scholarship_data($a_student_data, $s_personal_data_id)
	{
		$this->db->update('dt_personal_data_scholarship', $a_student_data, array('personal_data_id' => $s_personal_data_id));
		return true;
	}

	public function save_student_semester($a_data, $a_clause = false)
	{
		if ($a_clause) {
			$this->db->update('dt_student_semester', $a_data, $a_clause);
			// return $this->db->last_query();
			return true;
		}else{
			$this->db->insert('dt_student_semester', $a_data);
			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}

	public function get_student_null_address()
	{
		$this->db->select('pd.personal_data_id, st.student_id, pd.portal_id, pd.personal_data_name');
		$this->db->from('dt_student st');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
		$this->db->where('st.personal_data_id NOT IN (SELECT personal_data_id FROM dt_personal_address)');
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
		// return $this->db->last_query();
	}

	public function get_student_null_mother_maiden()
	{
		$this->db->select('pd.personal_data_id, st.student_id, st.portal_id, pd.personal_data_name, pd.personal_data_mother_maiden_name');
		$this->db->from('dt_student st');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
		$this->db->where('pd.personal_data_mother_maiden_name', NULL);
		$this->db->group_by('st.personal_data_id');
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function get_student_personal_data($a_clause = false, $a_status_in = false)
	{
		$this->db->from('dt_student st');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		if ($a_status_in) {
			$this->db->where_in('st.student_status', $a_status_in);
		}
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}
	
	public function get_student_by_id($s_student_id)
	{
		$this->db->select('*, st.portal_id AS "student_portal_id", st.study_program_id, st.program_id, pds.scholarship_id, st.personal_data_id');
		$this->db->from('dt_student st');
		$this->db->join('dt_academic_year ay', 'ay.academic_year_id = st.academic_year_id', 'LEFT');
		$this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
		$this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id', 'LEFT');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
		$this->db->join('dt_personal_data_scholarship pds', 'pds.personal_data_id = st.personal_data_id', 'LEFT');
		$this->db->where('st.student_id', $s_student_id);
		$query = $this->db->get();

		// $this->db->select('*, dt_student.portal_id AS "student_portal_id"');
		// $this->db->join('dt_academic_year', 'academic_year_id', 'LEFT');
		// $this->db->join('ref_study_program', 'study_program_id', 'LEFT');
		// $this->db->join('ref_faculty fc', 'faculty_id', 'LEFT');
		// $this->db->join('dt_personal_data', 'personal_data_id', 'LEFT');
		// $query = $this->db->get_where('dt_student', array('student_id' => $s_student_id));
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}
}