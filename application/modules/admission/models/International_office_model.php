<?php
class International_office_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

	public function get_student_abroad_document($a_clause = false)
	{
		$this->db->select('*, st.academic_year_id AS "student_batch"');
		$this->db->from('dt_student_exchange_doc sed');
		$this->db->join('dt_student_exchange se', 'se.exchange_id = sed.exchange_id');
		$this->db->join('dt_student st', 'st.student_id = se.student_id');
		$this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');

		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function remove_document_abroad($a_clause_delete)
	{
		$this->db->delete('dt_student_exchange_doc', $a_clause_delete);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function submit_document_abroad($a_data)
	{
		$this->db->insert('dt_student_exchange_doc', $a_data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function submit_student_abroad($a_data, $a_update_clause = false)
	{
		if ($a_update_clause) {
			$this->db->update('dt_student_exchange', $a_data, $a_update_clause);
			return true;
		}
		else {
			$this->db->insert('dt_student_exchange', $a_data);
			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}

	public function get_candidate_document($a_clause = false)
	{
		$this->db->from('dt_personal_data_document pdd');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = pdd.personal_data_id');
		$this->db->join('dt_student st', 'st.personal_data_id = pd.personal_data_id');
		$this->db->join("dt_student_exchange se", 'se.student_id = st.student_id');
		$this->db->join('ref_document rd', 'rd.document_id = pdd.document_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_institution_contact($s_institution_id, $a_clause = false)
    {
        $this->db->from('dt_institution_contact ic');
        $this->db->join('ref_institution ri', 'ri.institution_id = ic.institution_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = ic.personal_data_id');
        $this->db->where('ic.institution_id', $s_institution_id);
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->first_row() : false;
    }

	public function get_parent_student($s_personal_data_id, $a_clause = false)
    {
        $query_family = $this->db->get_where('dt_family_member', ['personal_data_id' => $s_personal_data_id]);
        if ($query_family->num_rows() > 0) {
            $o_family = $query_family->first_row();
            $s_family_id = $o_family->family_id;

            $this->db->order_by('fm.date_added', 'DESC');
            $this->db->join('dt_personal_data pd', 'pd.personal_data_id = fm.personal_data_id');
            $query_family_list = $this->db->get_where('dt_family_member fm', ['fm.family_id' => $s_family_id, 'fm.family_member_status != ' => 'child']);
            return ($query_family_list->num_rows() > 0) ? $query_family_list->result() : false;
        }
        else {
            return false;
        }
    }

	public function get_international_data($a_clause = false)
	{
		$this->db->select('*, st.academic_year_id AS "student_batch", ex.academic_year_id');
		$this->db->from('dt_student_exchange ex');
		$this->db->join('dt_student st', 'st.student_id = ex.student_id');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
		$this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id', 'LEFT');
		$this->db->join('ref_program rp', 'rp.program_id = ex.program_id');
		$this->db->join('ref_institution ri', 'ri.institution_id = ex.institution_id', 'LEFT');
		$this->db->join('ref_country rc', 'rc.country_id = ri.country_id', 'LEFT');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$this->db->group_by('ex.student_id');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

    public function get_student_filtered($a_clause = false, $a_status_in = false, $mbs_custom_ordering = false, $a_prodi_in = false, $a_program_in = false)
	{
		$this->db->select("
			*,
			ds.student_status AS 'status_student',
			ds.portal_id AS 'student_portal_id',
			ds.date_added AS 'register_date',
			ds.personal_data_id,
			ds.academic_year_id AS 'student_batch',
			ds.study_program_id,
			rpge.program_id AS 'exchange_program_id',
			rpge.program_name AS 'exchange_program_name',
			ds.program_id AS 'student_program',
			pd_citi.country_name AS 'citizenship_country_name',
			ad_citi.country_name AS 'address_country_name',
			ins_citi.country_id AS 'institution_country_id',
			ins_citi.country_name AS 'institution_country_name',
			da.country_id AS 'address_country_id',
            ex.faculty_name AS 'exchange_faculty_name',
            ex.study_program_name AS 'exchange_study_program_name',
			ex.academic_year_id AS 'exchange_academic_year',
		");
		$this->db->from('dt_student ds');
        $this->db->join('dt_student_exchange ex', 'ex.student_id = ds.student_id');
		$this->db->join('ref_semester_type sty', 'sty.semester_type_id = ex.semester_type_id', 'LEFT');
		$this->db->join('dt_personal_data dpd', 'ds.personal_data_id = dpd.personal_data_id');
		$this->db->join('ref_program rpg', 'rpg.program_id = ds.program_id', 'LEFT');
		$this->db->join('ref_program rpge', 'rpge.program_id = ex.program_id', 'LEFT');
		$this->db->join('ref_study_program rsp', 'rsp.study_program_id = ds.study_program_id', 'LEFT');
		$this->db->join('ref_faculty fc', 'fc.faculty_id = rsp.faculty_id', 'LEFT');
		$this->db->join('dt_personal_address dpa', 'dpa.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('ref_country pd_citi', 'pd_citi.country_id = dpd.citizenship_id', 'LEFT');
		$this->db->join('dt_address da', 'da.address_id = dpa.address_id', 'LEFT');
        $this->db->join('ref_country ad_citi', 'ad_citi.country_id = da.country_id', 'LEFT');
		$this->db->join('dt_family_member dfm', 'dfm.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('dt_academic_history dah', 'dah.personal_data_id = dpd.personal_data_id', 'LEFT');
		$this->db->join('ref_ocupation oc', 'oc.ocupation_id = dpd.ocupation_id', 'LEFT');
		$this->db->join('ref_institution ri', 'ri.institution_id = ex.institution_id', 'LEFT');
		$this->db->join('ref_country ins_citi', 'ins_citi.country_id = ri.country_id', 'LEFT');
		if($a_clause){
			$this->db->where($a_clause);
		}

		if ($a_status_in) {
			$this->db->where_in('ds.student_status', $a_status_in);
		}

		if ($a_prodi_in) {
			$this->db->where_in('ds.study_program_id', $a_prodi_in);
		}

		if ($a_program_in) {
			$this->db->where_in('ex.program_id', $a_program_in);
		}

		if ($mbs_custom_ordering) {
			$this->db->order_by($mbs_custom_ordering);
		}else{
			$this->db->order_by('ds.student_status');
			$this->db->order_by('ds.academic_year_id', 'DESC');
			$this->db->order_by('ex.program_id');
			$this->db->order_by('fc.faculty_abbreviation');
			$this->db->order_by('rsp.study_program_name');
		}
		$this->db->group_by('ds.student_id');
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
}
