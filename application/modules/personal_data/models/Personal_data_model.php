<?php
class Personal_data_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database('production');
	}

	public function submit_student_profile($a_postdata) {
		$this->form_validation->set_rules('pr_student_id', 'Key Student Profile', 'trim|required');

		// personal data form
		// $this->form_validation->set_rules('pr_pd_personal_data_name', 'Fullname', 'trim|required');
		// $this->form_validation->set_rules('pr_pd_personal_data_email', 'Personal Email', 'trim|required');
		$this->form_validation->set_rules('pr_pd_personal_data_mother_name', 'Mother Maiden Name', 'trim|required');
		$this->form_validation->set_rules('pr_pd_personal_data_phone', 'Phone Number', 'trim|decimal');
		$this->form_validation->set_rules('pr_pd_personal_data_cellular', 'Cellular Number', 'trim|required|decimal');
		$this->form_validation->set_rules('pr_pd_personal_data_citizenship', 'Citizenship', 'trim|required');
		$this->form_validation->set_rules('pr_pd_personal_data_identification_number', 'Identification Number', 'trim|required');
		$this->form_validation->set_rules('pr_pd_personal_data_country_birth', 'Country of Birth', 'trim|required');
		$this->form_validation->set_rules('pr_pd_personal_data_city_birth', 'City of Birth', 'trim|required');
		$this->form_validation->set_rules('pr_pd_personal_data_gender', 'Gender', 'trim|required');
		$this->form_validation->set_rules('pr_pd_personal_data_religion', 'Religion', 'trim|required');
		// $this->form_validation->set_rules('pr_pd_personal_data_name', 'Date of Birth', 'trim|required');

		// address form
		$this->form_validation->set_rules('pr_ad_address_street', 'Address Street', 'trim');
		$this->form_validation->set_rules('pr_ad_country_id', 'Address Country', 'trim|required');
		$this->form_validation->set_rules('pr_ad_address_province', 'Address Province', 'trim');
		$this->form_validation->set_rules('pr_ad_address_city', 'Address City', 'trim|required');
		$this->form_validation->set_rules('pr_ad_address_district', 'Address District', 'trim|required');
		$this->form_validation->set_rules('pr_ad_address_sub_district', 'Address Sub District', 'trim|required');
		$this->form_validation->set_rules('pr_ad_address_rt', 'Address RT', 'trim|decimal');
		$this->form_validation->set_rules('pr_ad_address_rw', 'Address RW', 'trim|decimal');
		$this->form_validation->set_rules('pr_ad_address_zip_code', 'Address Zip Code', 'trim');

		// parent form
		$this->form_validation->set_rules('pr_pt_family_relation', 'Parent Relation', 'trim|required');
		$this->form_validation->set_rules('pr_pt_personal_data_name', 'Parent / Guardian Name', 'trim|required');
		$this->form_validation->set_rules('pr_pt_personal_data_email', 'Parent / Guardian Email', 'trim|required');
		$this->form_validation->set_rules('pr_pt_personal_data_phone', 'Parent / Guardian Cellular', 'trim|required|decimal');
		$this->form_validation->set_rules('pr_pt_personal_data_occupation', 'Parent / Guardian Job Title', 'trim');
		$this->form_validation->set_rules('pr_pt_personal_data_institution_name', 'Parent / Guardian Job Company Name', 'trim');

		// Highschools form
		$this->form_validation->set_rules('pr_hs_institution_name', 'Highschool Name', 'trim|required');
		$this->form_validation->set_rules('pr_hs_nisn_number', 'NISN (Student Number)', 'trim|required');
		$this->form_validation->set_rules('pr_hs_graduation_year', 'Graduation Year', 'trim|required');
		$this->form_validation->set_rules('pr_hs_email', 'Highschool Email', 'trim');
		$this->form_validation->set_rules('pr_hs_phone_number', 'Highschool Phone Number', 'trim|decimal');
		$this->form_validation->set_rules('pr_hs_country', 'Highschool Country', 'trim|required');
		$this->form_validation->set_rules('pr_hs_city', 'Highschool City', 'trim|required');
		$this->form_validation->set_rules('pr_hs_transfer_student', 'Transfer Student', 'trim|required');
		if ($this->input->post('pr_hs_transfer_student') == 'on') {
			$this->form_validation->set_rules('pr_hs_university_name', 'University Name', 'trim|required');
			$this->form_validation->set_rules('pr_hs_university_prodi', 'Study Program', 'trim|required');
			$this->form_validation->set_rules('pr_hs_university_country', 'Country', 'trim|required');
			$this->form_validation->set_rules('pr_hs_university_city', 'City', 'trim|required');
			$this->form_validation->set_rules('pr_hs_university_ipk', 'IPK', 'trim|decimal');
		}

		$mba_student_data = $this->General->get_where('dt_student', ['student_id' => $this->input->post('pr_student_id')]);
		
		// Employment form
		if (($mba_student_data) AND ($mba_student_data[0]->student_class_type == 'karyawan')) {
			$this->form_validation->set_rules('pr_jd_institution_name', 'Company Name', 'trim|required');
			$this->form_validation->set_rules('pr_jd_institution_country', 'Company Country', 'trim|required');
			$this->form_validation->set_rules('pr_jd_institution_city', 'Company City', 'trim|required');
			$this->form_validation->set_rules('pr_jd_job_title', 'Job Title', 'trim|required');
			$this->form_validation->set_rules('pr_jd_working_start', 'Working Date Start', 'trim|required');
			if ($this->input->post('pr_jd_working_still') !== 'on') {
				$this->form_validation->set_rules('pr_jd_working_end', 'Working Date End', 'trim|required');
			}
		}

		if (empty($this->input->post('pr_student_id'))) {
			$a_result = ['code' => 2, 'message' => 'Undefined key for submit data!'];
		}
		else if (!$mba_student_data) {
			$a_result = ['code' => 2, 'message' => 'Student data not found!'];
		}
		else if ($this->form_validation->run()) {
			$this->db->trans_begin();
			$s_personal_data_id = $mba_student_data[0]->personal_data_id;
			$s_student_id = $mba_student_data[0]->student_id;

			$mba_personal_address = $this->General->get_where('dt_personal_address', ['personal_data_id' => $s_personal_data_id, 'address_id != ' => NULL]);
			$mba_student_family = $this->General->get_where('dt_family_member', ['personal_data_id' => $s_personal_data_id]);
			$mba_wilayah_data = $this->General->get_where('dikti_wilayah', ['id_wilayah' => set_value('pr_ad_address_district')]);

			$s_personal_data_id_parent = false;
			$s_family_id = false;
			$parent_family = false;
			if ($mba_student_family) {
				$parent_family = $this->General->get_where('dt_family_member', ['family_id' => $mba_student_family[0]->family_id, 'family_member_status !=' => 'child']);
				$s_personal_data_id_parent = ($parent_family) ? $parent_family[0]->personal_data_id : false;
				$s_family_id = $mba_student_family[0]->family_id;
			}

			$a_personal_data = [
				'personal_data_mother_maiden_name' => set_value('pr_pd_personal_data_mother_name'),
				'personal_data_phone' => (empty(set_value('pr_pd_personal_data_phone'))) ? NULL : set_value('pr_pd_personal_data_phone'),
				'personal_data_cellular' => set_value('pr_pd_personal_data_cellular'),
				'citizenship_id' => set_value('pr_pd_personal_data_citizenship'),
				'personal_data_id_card_number' => set_value('pr_pd_personal_data_identification_number'),
				'country_of_birth' => set_value('pr_pd_personal_data_country_birth'),
				'personal_data_place_of_birth' => set_value('pr_pd_personal_data_city_birth'),
				'personal_data_gender' => set_value('pr_pd_personal_data_gender'),
				'religion_id' => set_value('pr_pd_personal_data_religion'),
				'personal_data_id_card_type' => (set_value('pr_pd_personal_data_citizenship') == '9bb722f5-8b22-11e9-973e-52540001273f') ? 'national_id' : 'passport',
				'personal_data_nationality' => (set_value('pr_pd_personal_data_citizenship') == '9bb722f5-8b22-11e9-973e-52540001273f') ? 'WNI' : 'WNA',
				'has_completed_personal_data' => 0
			];
			// update personal data

			$a_student_data = [
				'student_nisn' => set_value('pr_hs_nisn_number'),
				'student_type' => (set_value('pr_hs_transfer_student') == 'on') ? 'transfer' : 'regular',
				'student_not_reported_to_feeder' => ($mba_student_data[0]->student_class_type == 'exchange') ? '1' : NULL
			];
			// update student data
			
			$a_student_address_data = [
				'dikti_wilayah_id' => set_value('pr_ad_address_district'),
				'country_id' => set_value('pr_ad_country_id'),
				'address_rt' => (empty(set_value('pr_ad_address_rt'))) ? NULL : set_value('pr_ad_address_rt'),
				'address_rw' => (empty(set_value('pr_ad_address_rw'))) ? NULL : set_value('pr_ad_address_rw'),
				'address_province' => (empty(set_value('pr_ad_address_province'))) ? NULL : set_value('pr_ad_address_province'),
				'address_city' => set_value('pr_ad_address_city'),
				'address_zipcode' => (empty(set_value('pr_ad_address_zip_code'))) ? NULL : set_value('pr_ad_address_zip_code'),
				'address_street' => (empty(set_value('pr_ad_address_street'))) ? NULL : set_value('pr_ad_address_street'),
				'address_district' => ($mba_wilayah_data) ? $mba_wilayah_data[0]->nama_wilayah : NULL,
				'address_sub_district' => set_value('pr_ad_address_sub_district')
			];

			$a_personal_address = [
				'personal_address_name' => 'My Address',
				// 'personal_address_text' => 
			];

			if ($mba_personal_address) {
				$s_address_id = $mba_personal_address[0]->address_id;
				// update address data

				$a_personal_address['address_id'] = $s_address_id;
				$a_personal_address['personal_address_text'] = $this->retrieve_address_text($s_address_id);
				// update personal address
			}
			else {
				$s_address_id = $this->uuid->v4();
				$a_student_address_data['address_ud'] = $s_address_id;
				// insert address data

				$a_personal_address['personal_address_text'] = $this->retrieve_address_text($s_address_id);
				// insert personal address
			}

			$s_ocupation_id = NULL;
			if (!empty(set_value('pr_pt_personal_data_occupation'))) {
				$mba_ocupation_office_data = $this->General->get_where('ref_ocupation', ['ocupation_name' => set_value('pr_pt_personal_data_occupation')]);
				if (!$mba_ocupation_office_data) {
					$s_ocupation_id = $this->uuid->v4();
					$a_ocupation_office_data = [
						'ocupation_id' => $s_ocupation_id,
						'ocupation_name' => set_value('pr_pt_personal_data_occupation')
					];
					// insert ocupation data
				}
				else {
					$s_ocupation_id = $mba_ocupation_office_data[0]->ocupation_id;
				}
			}

			$s_parentinstitution_id = NULL;
			if (!empty(set_value('pr_pt_personal_data_institution_name'))) {
				$mba_institution_office_data = $this->General->get_where('ref_institution', ['institution_name' => set_value('pr_pt_personal_data_institution_name')]);
				if (!$mba_institution_office_data) {
					$s_parentinstitution_id = $this->uuid->v4();
					$a_institution_office_data = [
						'institution_id' => $s_parentinstitution_id,
						'institution_name' => set_value('pr_pt_personal_data_institution_name'),
						'institution_type' => 'office'
					];
					// insert institution data
				}
				else {
					$s_parentinstitution_id = $mba_institution_office_data[0]->institution_id;
				}
			}

			$a_personal_data_parent = [
				'personal_data_name' => set_value('pr_pt_personal_data_name'),
				'personal_data_email' => set_value('pr_pt_personal_data_email'),
				'personal_data_cellular' => set_value('pr_pt_personal_data_phone'),
				'ocupation_id' => $s_ocupation_id
			];
			if (!$s_personal_data_id_parent) {
				$s_personal_data_id_parent = $this->uuid->v4();
				$a_personal_data_parent['personal_data_id'] = $s_personal_data_id_parent;
				// insert personal data parent
			}
			else {
				// update personal data parent
			}

			if (!is_null($s_parentinstitution_id)) {
				$mba_parent_institution = $this->General->get_where('dt_academic_history', ['personal_data_id' => $s_personal_data_id_parent, 'insitution_id != ' => NULL]);
				$a_parent_academic_history = [
					'institution_id' => $s_parentinstitution_id,
					'academic_history_this_job' => 'yes',
					'status' => 'active'
				];

				if ($mba_parent_institution) {
					// update academic history parent
				}
				else {
					$a_parent_academic_history['academic_history_id'] = $this->uuid->v4();
					// insert academic history parent
				}
			}
			
			if (!$s_family_id) {
				$s_family_id = $this->uuid->v4();
				$a_family_data = [
					'family_id' => $s_family_id
				];
				// insert family data

				$a_student_family_member = [
					'family_id' => $s_family_id,
					'personal_data_id' => $s_personal_data_id,
					'family_member_status' => 'child'
				];
				// insert student family member
			}

			if (!$parent_family) {
				$a_family_memberparent_data = [
					'family_id' => $s_family_id,
					'personal_data_id' => $s_personal_data_id_parent,
					'family_member_status' => set_value('pr_pt_family_relation')
				];
				// insert parent family member
			}

			$mba_highschool_data = $this->General->get_where('ref_institution', [
				'institution_name' => set_value('pr_hs_institution_name'),
				'institution_type' => 'highschool'
			]);
			if (!$mba_highschool_data) {
				$s_highscinstitution_id = $this->uuid->v4();
				$s_address_highschool_id = $this->uuid->v4();
				$a_highschool_address_data = [
					'address_id' => $s_address_highschool_id,
					'country_id' => set_value('pr_hs_country'),
					'address_city' => set_value('pr_hs_city')
				];
				// insert address data

				$a_institution_highschool_data = [
					'institution_id' => $s_highscinstitution_id,
					'institution_name' => set_value('pr_hs_institution_name'),
					'country_id' => set_value('pr_hs_country'),
					'address_id' => $s_address_highschool_id,
					'institution_type' => 'highschool',
					'institution_email' => (empty(set_value('pr_hs_email'))) ? NULL : set_value('pr_hs_email'),
					'institution_phone_number' => (empty(set_value('pr_hs_phone_number'))) ? NULL : set_value('pr_hs_phone_number'),
				];
				// insert institution data
			}
			else {
				$s_highscinstitution_id = $mba_highschool_data[0]->institution_id;

				if (!is_null($mba_highschool_data[0]->address_id)) {
					$s_address_highschool_id = $mba_highschool_data[0]->address_id;
					$a_highschool_address_data = [
						'country_id' => set_value('pr_hs_country'),
						'address_city' => set_value('pr_hs_city')
					];
					// update address data
				}
				else {
					$s_address_highschool_id = $this->uuid->v4();
					$a_highschool_address_data = [
						'address_id' => $s_address_highschool_id,
						'country_id' => set_value('pr_hs_country'),
						'address_city' => set_value('pr_hs_city')
					];
					// insert address data
				}

				$a_institution_highschool_data = [
					'country_id' => set_value('pr_hs_country'),
					'address_id' => $s_address_highschool_id,
					'institution_type' => 'highschool',
					'institution_email' => (empty(set_value('pr_hs_email'))) ? NULL : set_value('pr_hs_email'),
					'institution_phone_number' => (empty(set_value('pr_hs_phone_number'))) ? NULL : set_value('pr_hs_phone_number'),
				];
				// update highschool data
			}

			$a_academic_history_highschool_data = [
				'institution_id' => $s_highscinstitution_id,
				'personal_data_id' => $s_personal_data_id,
				'academic_history_graduation_year' => set_value('pr_hs_graduation_year'),
				// ''
			];

			$mba_student_highschool = $this->General->get_where('dt_academic_history', [
				'personal_data_id' => $s_personal_data_id,
				'insitution_id != ' => NULL,
				'academic_history_this_job' => 'no'
			]);
			if ($mba_student_highschool) {
				$s_academic_history_id = $mba_student_highschool[0]->academic_history_id;
			}
			else {
				$s_academic_history_id = $this->uuid->v4();
			}
			
			
			if ($this->input->post('pr_hs_transfer_student') == 'on') {
				$a_institution_university_addressdata = [];
				$a_institution_university_data = [];
				$a_academic_history_university_data = [];
			}
			// 
$this->form_validation->set_rules('', 'Highschool Name', 'trim|required');
$this->form_validation->set_rules('', 'NISN (Student Number)', 'trim|required');
$this->form_validation->set_rules('', 'Graduation Year', 'trim|required');
$this->form_validation->set_rules('', 'Highschool Email', 'trim');
$this->form_validation->set_rules('', 'Highschool Phone Number', 'trim|decimal');
$this->form_validation->set_rules('', 'Highschool Country', 'trim|required');
$this->form_validation->set_rules('', 'Highschool City', 'trim|required');
$this->form_validation->set_rules('pr_hs_transfer_student', 'Transfer Student', 'trim|required');
if ($this->input->post('pr_hs_transfer_student') == 'on') {
	$this->form_validation->set_rules('pr_hs_university_name', 'University Name', 'trim|required');
	$this->form_validation->set_rules('pr_hs_university_prodi', 'Study Program', 'trim|required');
	$this->form_validation->set_rules('pr_hs_university_country', 'Country', 'trim|required');
	$this->form_validation->set_rules('pr_hs_university_city', 'City', 'trim|required');
	$this->form_validation->set_rules('pr_hs_university_ipk', 'IPK', 'trim|decimal');
}
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$a_result = ['code' => 1, 'message' => 'Failed submit data!'];
			}
			else {
				$this->db->trans_commit();
				$a_result = ['code' => 0, 'message' => 'Success'];
			}
		}
		else{
			$a_result = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
		}

		return $a_result;
	}

	public function submit_personal_contact($a_data, $a_update_clause = false)
	{
		if ($a_update_clause) {
			$this->db->update('dt_personal_data_contact', $a_data, $a_update_clause);
			return true;
		}
		else {
			$this->db->insert('dt_personal_data_contact', $a_data);
			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}

	public function get_personal_data_by_name($s_personal_data_name, $a_clause = false, $b_limit = false)
	{
		$this->db->from('dt_personal_data pd');
		$this->db->like('pd.personal_data_name', $s_personal_data_name);
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		if ($b_limit) {
			$this->db->limit(10, 0);
		}
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function get_personal_data_sibling($s_personal_data_id, $a_clause = false, $sibling_type = false)
	{
		$this->db->from('dt_personal_data_scholarship pds');
		$this->db->join('dt_personal_data pdsb', 'pdsb.personal_data_id = pds.personal_data_id_sibling_with');
		if ($sibling_type) {
			if (strtolower($sibling_type) == 'student') {
				$this->db->join('dt_student st', 'st.personal_data_id = pdsb.personal_data_id');
			}
			else if (strtolower($sibling_type) == 'employee') {
				$this->db->join('dt_employee em', 'em.personal_data_id = pdsb.personal_data_id');
			}
		}

		$this->db->where('pds.personal_data_id', $s_personal_data_id);
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	public function get_personal_data_scholarship($s_personal_data_id, $a_clause = false, $a_scholarship_id_in = false)
	{
		$this->db->select('*, pds.scholarship_id');
		$this->db->from('dt_personal_data_scholarship pds');
		$this->db->join('ref_scholarship rs', 'rs.scholarship_id = pds.scholarship_id');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = pds.personal_data_id');
		$this->db->where('pds.personal_data_id', $s_personal_data_id);
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		if ($a_scholarship_id_in) {
			$this->db->where_in('pds.scholarship_id', $a_scholarship_id_in);
		}
		$q = $this->db->get();

		return ($q->num_rows() > 0) ? $q->result() : false;
		// return ($q->num_rows() > 0) ? $q->result() : $this->db->last_query();
	}

	public function set_sgs_promo($reference_data)
	{
		$this->db->insert('dt_reference', $reference_data);
	}

	public function get_reference_code($s_reference_code)
	{
		$query = $this->db->get_where('dt_personal_data', array('personal_data_reference_code' => $s_reference_code));
		return ($query->num_rows() > 0) ? $query->first_row() : false;
	}
	
	public function get_latest_reference_code($s_reference_code)
	{
		$this->db->like('personal_data_reference_code', $s_reference_code);
		$this->db->order_by('personal_data_reference_code', 'DESC');
		$query = $this->db->get('dt_personal_data');
		return ($query->num_rows() >= 1) ? $query->first_row() : false;
	}

	public function get_personal_address($s_personal_data_id, $s_address_id = false, $is_primary = false)
	{
		$this->db->select("*");
		$this->db->from('dt_personal_address dpa');
		$this->db->join('dt_personal_data dpd', 'personal_data_id');
		$this->db->join('dt_address da', 'address_id');
		$this->db->join('ref_country rc', 'country_id');
		$this->db->join('dikti_wilayah dw', 'dw.id_wilayah = da.dikti_wilayah_id');
		$this->db->where('dpa.personal_data_id', $s_personal_data_id);
		if($s_address_id){
			$this->db->where('dpa.address_id', $s_address_id);
		}
		
		if($is_primary){
			$this->db->where('dpa.personal_address_type', 'primary');
		}
		
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	public function save_testimonial($a_testimonial_data, $s_testimonial_id = false)
	{
		if (is_object($a_testimonial_data)) {
			$a_testimonial_data = (array)$a_testimonial_data;
		}

		if ($s_testimonial_id) {
			$this->db->update('dt_testimonial', $a_testimonial_data, array('testimonial_id' => $s_testimonial_id));
		}else{
			if ((is_array($a_testimonial_data)) AND (!array_key_exists('testimonial_id', $a_testimonial_data))) {
			// if ($a_testimonial_data['testimonial_id'] === null) {
				$a_testimonial_data['testimonial_id'] = $this->uuid->v4();
			}

			$this->db->insert('dt_testimonial', $a_testimonial_data);
		}


		return ($this->db->affected_rows() > 0) ? true : false;
	}
	
	public function get_testimonial_personal_data($s_personal_data_id = false)
	{
		$this->db->from('dt_testimonial dte');
		$this->db->join('dt_personal_data pd', 'pd.personal_data_id = dte.personal_data_id');
		$this->db->join('dt_student ds', 'ds.personal_data_id = pd.personal_data_id');
		if ($s_personal_data_id) {
			$this->db->where('dte.personal_data_id', $s_personal_data_id);
		}
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function save_alumni_data($a_alumni_data, $s_alumni_id = false)
	{
		if ($s_alumni_id) {
			$this->db->update('dt_student_alumni', $a_alumni_data, array('alumni_id' => $s_alumni_id));
			return true;
		}else{
			if (is_object($a_alumni_data)) {
				$a_alumni_data = (array)$a_alumni_data;
			}

			// if ($a_alumni_data['alumni_id'] === null) {
			if ((is_array($a_alumni_data)) AND (!array_key_exists('alumni_id', $a_alumni_data))) {
				$a_alumni_data['alumni_id'] = $this->uuid->v4();
			}

			$this->db->insert('dt_student_alumni', $a_alumni_data);
			return ($this->db->affected_rows() > 0) ? true : false;
		}
	}

	public function save_personal_document($a_personal_document_data, $s_personal_data_id = false, $s_document_id = false)
	{
		if (($s_personal_data_id) AND ($s_document_id)) {
			$this->db->update('dt_personal_data_document', $a_personal_document_data, array('personal_data_id' => $s_personal_data_id, 'document_id' => $s_document_id));
		}else{
			$this->db->insert('dt_personal_data_document', $a_personal_document_data);
		}

		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function get_requirement_document($a_param_data = false)
	{
		$this->db->select('*');
		$this->db->from('ref_document rd');
		$this->db->join('ref_document_type rdt' ,'rd.document_id = rdt.document_id', 'left');
		if ($a_param_data) {
			$this->db->where($a_param_data);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_personal_document($s_personal_data_id, $s_document_id = false)
	{
		$this->db->select('*, dpd.document_id');
		$this->db->from('dt_personal_data_document dpd');
		$this->db->join('ref_document rd', 'dpd.document_id = rd.document_id', 'left');
		$this->db->join('ref_document_type rdt', 'rdt.document_id = rd.document_id', 'left');
		$this->db->where('dpd.personal_data_id', $s_personal_data_id);
		if ($s_document_id) {
			$this->db->where('dpd.document_id', $s_document_id);
		}
		$query = $this->db->get();
		// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
		// 	return $this->db->last_query();
		// }
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function delete_personal_document($a_clause)
	{
		$this->db->delete('dt_personal_data_document', $a_clause);

		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function delete_academic_history($a_clause)
	{
		$this->db->delete('dt_academic_history', $a_clause);

		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function get_religion($clause = false)
	{
		$this->db->select('*');
		if ($clause) {
			$this->db->where($clause);
		}
		$this->db->from('ref_religion');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	public function update_address($a_address_data, $s_address_id)
	{
		$this->db->update('dt_address', $a_address_data, array('address_id' => $s_address_id));
	}
	
	public function get_address_data($s_personal_data_id, $s_address_id = false, $is_primary = false)
	{
		$this->db->select("*");
		$this->db->from('dt_personal_address dpa');
		$this->db->join('dt_personal_data dpd', 'personal_data_id');
		$this->db->join('dt_address da', 'address_id');
		$this->db->join('ref_country rc', 'country_id', 'LEFT');
		$this->db->join('dikti_wilayah dw', 'dw.id_wilayah = da.dikti_wilayah_id', 'LEFT');
		$this->db->where('dpa.personal_data_id', $s_personal_data_id);
		if($s_address_id){
			$this->db->where('dpa.address_id', $s_address_id);
		}
		
		if($is_primary){
			$this->db->where('dpa.personal_address_type', 'primary');
		}
		
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
/*
		if($query->num_rows() > 1){
			return $query->result();
		}
		else{
			if($query->num_rows() == 1){
				return $query->first_row();
			}
			else{
				return false;
			}
		}
*/
	}
	
	public function insert_academic_history($s_personal_data_id, $s_institution_id)
	{
		$s_academic_history_id = $this->uuid->v4();
		$this->db->insert('dt_academic_history', array(
			'academic_history_id' => $s_academic_history_id,
			'personal_data_id' => $s_personal_data_id,
			'institution_id' => $s_institution_id
		));
		
		if($this->db->affected_rows() == 1){
			return array(
				'academic_history_id' => $s_academic_history_id,
				'personal_data_id' => $s_personal_data_id,
				'institution_id' => $s_institution_id
			);
		}
		else{
			return false;
		}
	}
	
	public function update_academic_history($s_personal_data_id, $s_old_institution_id, $s_new_institution_id)
	{
		$this->db->update('dt_academic_history',
			array(
				'personal_data_id' => $s_personal_data_id,
				'institution_id' => $s_new_institution_id
			),
			array(
				'personal_data_id' => $s_personal_data_id,
				'institution_id' => $s_old_institution_id
			)
		);
	}
	
	public function get_academic_history($s_personal_data_id)
	{
		$this->db->select('*');
		$this->db->from('dt_academic_history dah');
		$this->db->join('ref_institution ri', 'ri.institution_id = dah.institution_id');
		$this->db->join('dt_address da','da.address_id = ri.address_id','left');
		$this->db->where('dah.personal_data_id', $s_personal_data_id);
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_academic_filtered($filter_data = false)
	{
		$this->db->select('*');
		$this->db->from('dt_academic_history dah');
		$this->db->join('ref_institution ri', 'institution_id');
		$this->db->join('dt_address da','da.address_id = ri.address_id','left');
		$this->db->join('ref_country rc','rc.country_id = ri.country_id','left');
		if ($filter_data) {
			$this->db->where($filter_data);
		}
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}
	
	public function create_address($a_address_data, $s_personal_data_id)
	{
		$a_address_data['address_id'] = $this->uuid->v4();
		$this->db->insert('dt_address', $a_address_data);
		$a_personal_address = array(
			'personal_data_id' => $s_personal_data_id,
			'address_id' => $a_address_data['address_id'],
			'date_added' => date('Y-m-d H:i:s')
		);
		$this->db->insert('dt_personal_address', $a_personal_address);
		return $a_address_data['address_id'];
	}
	
	public function get_personal_data_by_id($s_personal_data_id, $b_sync_to_portal = false)
	{
    	if($b_sync_to_portal){
        	$s_select_statement = "dpd.*";
    	}
    	else{
        	$s_select_statement = "dpd.*,
			pd_cob.country_name AS 'country_of_birth_country_name',
			pd_citi.country_name AS 'citizenship_country_name'";
    	}
		$this->db->select($s_select_statement);
		$this->db->from('dt_personal_data dpd');
		$this->db->join('ref_religion rr', 'religion_id', 'LEFT');
		$this->db->join('ref_country pd_cob', 'pd_cob.country_id = dpd.country_of_birth', 'LEFT');
		$this->db->join('ref_country pd_citi', 'pd_citi.country_id = dpd.citizenship_id', 'LEFT');
		$this->db->where('dpd.personal_data_id', $s_personal_data_id);
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->first_row() : false;
	}
	
	public function update_personal_data($a_personal_data, $s_personal_data_id)
	{
		if (is_object($a_personal_data)) {
			$a_personal_data = (array)$a_personal_data;
		}

		if ((is_array($a_personal_data)) AND (array_key_exists('personal_data_id', $a_personal_data))) {
		// if ($a_personal_data['personal_data_id'] !== null) {
			$s_directory = APPPATH.'uploads/'.$s_personal_data_id;
			$s_new_directory = APPPATH.'uploads/'.$a_personal_data['personal_data_id'];
			if ($s_directory != $s_new_directory) {
				if(!file_exists($s_directory)){
					mkdir($s_new_directory, 0755, true);
				}else{
					rename($s_directory, $s_new_directory);
				}
			}
		}

		if((is_array($a_personal_data)) AND (array_key_exists('portal_sync', $a_personal_data))){
			unset($a_personal_data['portal_sync']);
		}

		$this->db->update('dt_personal_data', $a_personal_data, array('personal_data_id' => $s_personal_data_id));

		return true;
	}

	public function create_personal_data_parents($a_personal_data)
	{
		if (is_object($a_personal_data)) {
			$a_personal_data = (array)$a_personal_data;
		}

		if ((is_array($a_personal_data)) AND (!array_key_exists('personal_data_id', $a_personal_data))) {
		// if ($a_personal_data['personal_data_id'] === null) {
			$a_personal_data['personal_data_id'] = $this->uuid->v4();
		}

		$a_personal_data['date_added'] = date('Y-m-d H:i:s', time());
		$this->db->insert('dt_personal_data', $a_personal_data);

		return ($this->db->affected_rows() > 0) ? true : false;
	}
	
	public function create_new_personal_data($a_personal_data)
	{
		if(is_object($a_personal_data)){
			$a_personal_data = (array)$a_personal_data;
		}
		
		if((is_array($a_personal_data)) AND (!array_key_exists('personal_data_id', $a_personal_data))) {
			$a_personal_data['personal_data_id'] = $this->uuid->v4();
		}
		
		if((is_array($a_personal_data)) AND (array_key_exists('portal_sync', $a_personal_data))){
		// if ($a_personal_data['portal_sync'] !== null) {
			unset($a_personal_data['portal_sync']);
		}
		
		$a_personal_data['date_added'] = date('Y-m-d H:i:s', time());
		$this->db->insert('dt_personal_data', $a_personal_data);
		
		$s_directory_file = APPPATH.'uploads/'.$a_personal_data['personal_data_id'].'/';
		// if(!file_exists($s_directory_file)){
		// 	mkdir($s_directory_file, 0755);
		// }
		
		return $a_personal_data['personal_data_id'];
	}

	public function create_new_personal_data_test($a_personal_data)
	{
		if(is_object($a_personal_data)){
			$a_personal_data = (array)$a_personal_data;
		}
		
		return $a_personal_data;
		// if((is_array($a_personal_data)) AND (!array_key_exists('personal_data_id', $a_personal_data))) {
		// 	$a_personal_data['personal_data_id'] = $this->uuid->v4();
		// }
		
		// if((is_array($a_personal_data)) AND (array_key_exists('portal_sync', $a_personal_data))){
		// // if ($a_personal_data['portal_sync'] !== null) {
		// 	unset($a_personal_data['portal_sync']);
		// }
		
		// $a_personal_data['date_added'] = date('Y-m-d H:i:s', time());
		// $this->db->insert('dt_personal_data', $a_personal_data);
		
		// $s_directory_file = APPPATH.'uploads/'.$a_personal_data['personal_data_id'].'/';
		// if(!file_exists($s_directory_file)){
		// 	mkdir($s_directory_file, 0755);
		// }
		
		// return $a_personal_data['personal_data_id'];
	}
	
	public function get_personal_data_by_email($s_email)
	{
		$query = $this->db->get_where('dt_personal_data', array('personal_data_email' => $s_email));
		if($query->num_rows() == 1){
			return $this->get_personal_data_by_id($query->first_row()->personal_data_id);
		}
		else{
			return false;
		}
	}

	function retrieve_address_text($s_address_id) {
        $mba_address_data = $this->General->get_where('dt_address', ['address_id' => $s_address_id]);
        $address_text = '';
        if ($mba_address_data) {
            $mba_address_data = $mba_address_data[0];
            $district_data = $this->General->get_where('dikti_wilayah', ['id_wilayah' => $mba_address_data->dikti_wilayah_id]);
            $country_data = $this->General->get_where('ref_country', ['country_id' => $mba_address_data->country_id]);


            $street = (!empty($mba_address_data->address_street)) ? $mba_address_data->address_street : NULL;
            $rt = (!empty($mba_address_data->address_rt)) ? $mba_address_data->address_rt : NULL;
            $rw = (!empty($mba_address_data->address_rw)) ? $mba_address_data->address_rw : NULL;
            $sub_district = (!empty($mba_address_data->address_sub_district)) ? $mba_address_data->address_sub_district : NULL;
            $district = ($district_data) ? $district_data[0]->nama_wilayah : NULL;
            $city = (!empty($mba_address_data->address_city)) ? $mba_address_data->address_city : NULL;
            $province = (!empty($mba_address_data->address_province)) ? $mba_address_data->address_province : NULL;
            $country = ($country_data) ? $country_data[0]->country_name : NULL;

            if (!is_null($street)) {
                $address_text .= $street;
            }
            if (!is_null($rt)) {
                $address_text .= ' RT '.$rt;
            }
            if (!is_null($rw)) {
                $address_text .= '/'.$rw;
            }
            if (!is_null($sub_district)) {
                $address_text .= ' kel. '.ucfirst(strtolower($sub_district));
            }
            if (!is_null($district)) {
                $address_text .= ' '.$district;
            }
            if (!is_null($city)) {
                $address_text .= ' '.ucfirst(strtolower($city));
            }
            if (!is_null($province)) {
                $address_text .= ' '.ucfirst(strtolower($province));
            }
            if (!is_null($country)) {
                $address_text .= ' '.$country;
            }

            $address_text = trim($address_text);
        }

        return $address_text;
	}

	public function retrieve_address($s_personal_data_id)
	{
		$s_address = '';
		$mba_personal_address = $this->get_address_data($s_personal_data_id);
		if ($mba_personal_address) {
			$o_personal_address = $mba_personal_address[0];
			foreach ($mba_personal_address as $o_address) {
				if ($o_address->personal_address_type == 'primary') {
					$o_personal_address = $o_address;
				}
			}

			$address_street = (!empty($o_address->address_street)) ? $o_address->address_street.' ' : '';
			$address_rt = (!empty($o_address->address_rt)) ? 'RT '.$o_address->address_rt.' ' : '';
			$address_rw = (!empty($o_address->address_rw)) ? 'RW '.$o_address->address_rw.' ' : '';
			$address_sub_district = (!empty($o_address->address_sub_district)) ? $o_address->address_sub_district.' ' : '';
			$nama_wilayah = (!empty($o_address->nama_wilayah)) ? $o_address->nama_wilayah.' ' : '';
			$address_city = (!empty($o_address->address_city)) ? $o_address->address_city.' ' : '';
			$address_province = (!empty($o_address->address_province)) ? $o_address->address_province.' ' : '';
			$country_name = (!empty($o_address->country_name)) ? $o_address->country_name.' ' : '';
			$address_zipcode = (!empty($o_address->address_zipcode)) ? $o_address->address_zipcode.' ' : '';

			$s_address = $address_street.$address_rt.$address_rw.$address_sub_district.$nama_wilayah.$address_city.$address_province.$country_name.$address_zipcode;
		}
		else {
			$query = $this->db->get_where('dt_personal_address', ['personal_data_id' => $s_personal_data_id]);
			if ($query->num_rows() > 0) {
				$o_personal_address = $query->first_row();
				$s_address = $o_personal_address->personal_address_text;
			}
		}

		return $s_address;
	}

	public function retrieve_title($s_personal_data_id)
	{
		$query = $this->db->get_where('dt_personal_data', array('personal_data_id' => $s_personal_data_id));
		if ($query->num_rows() > 0) {
			$o_data = $query->row();

			if ($o_data->personal_data_id == '41261c5c-94c7-4c5e-b4f9-4117f4567b8a') {
				$a_personal_data_name = explode(' ', $o_data->personal_data_name);
				$firstname = ucfirst(strtolower($a_personal_data_name[0]));
				$middlename = strtoupper($a_personal_data_name[1]);
				$lastname = ucfirst(strtolower($a_personal_data_name[2]));
				
				$s_personal_data_name = $firstname.' '.$middlename.' '.$lastname;
			}else{
				$s_personal_data_name = ucwords(strtolower($o_data->personal_data_name));
			}
			return  $o_data->personal_data_title_prefix.' '.
					$s_personal_data_name.
					((!is_null($o_data->personal_data_title_suffix)) ? ', ' : ' ').
					$o_data->personal_data_title_suffix;
		}
		else {
			return '';
		}
	}
}