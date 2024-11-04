<?php
class General_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database('production');
	}
	
	public function get_enum_values( $table, $field )
	{
		$type = $this->db->query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" )->row( 0 )->Type;
		preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
		$enum = explode("','", $matches[1]);
		return $enum;
	}

	public function get_thesis_subject($s_student_id)
	{
		$this->db->from('dt_score sc');
		$this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = sc.curriculum_subject_id');
		$this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
		$this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
		$this->db->where('sc.student_id', $s_student_id);
		$this->db->like('sn.subject_name', 'thesis', 'after');
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? true : false;
	}

	public function get_where($s_table_name, $a_clause = false, $a_order = false)
	{
		if ($a_order) {
			foreach ($a_order as $key => $value) {
				$this->db->order_by($key, $value);
			}
		}
		if ($a_clause) {
			$query = $this->db->get_where($s_table_name, $a_clause);
		}else{
			$query = $this->db->get($s_table_name);
		}

		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_join($s_table, $s_select = '*', $mba_join = false, $a_clause = false)
    {
		$this->db->select($s_select);
        $this->db->from($s_table);
        
        if ($mba_join) {
            foreach ($mba_join as $key => $s_join) {
                $this->db->join($key, $s_join);
            }
        }
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

	public function force_delete($s_table_name, $s_field, $s_field_value)
	{
		if (!empty($s_field_value)) {
			$this->db->delete($s_table_name, [$s_field => $s_field_value]);
			return ($this->db->affected_rows() > 0) ? true : false;
		}
		else {
			return false;
		}
	}

	public function execute_query($s_query)
	{
		$return = $this->db->query($s_query);
		// return $this->db->last_query();
		return ($return->num_rows() > 0) ? $return->result() : false;
	}

	public function list_field($s_table_name)
	{
		$query = $this->db->list_fields($s_table_name);
		return $query;
	}

	public function get_like($s_table_name, $a_like_clause = false)
	{
		if ($a_like_clause) {
			$this->db->like($a_like_clause);
		}
		
		$query = $this->db->get($s_table_name);

		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function get_in($s_table_name, $s_clause_in = false, $a_clause_in = false, $a_clause = false)
	{
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		if ($s_clause_in) {
			$this->db->where_in($s_clause_in, $a_clause_in);
		}
		
		$query = $this->db->get($s_table_name);

		return ($query->num_rows() > 0) ? $query->result() : false;
		// return $this->db->last_query();
	}

	public function update_data($s_table_name, $a_data, $a_clause)
	{
		$this->db->update($s_table_name, $a_data, $a_clause);
		return true;
		// return $this->db->last_query();
	}

	public function insert_data($s_table_name, $a_data)
	{
		$this->db->insert($s_table_name, $a_data);
		// return true;
		return ($this->db->affected_rows() > 0) ? true : $this->db->last_query();
		// return $this->db->last_query();
	}
	
	public function get_programs()
	{
		$query = $this->db->get('ref_program');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_scholarship()
	{
		$query = $this->db->get_where('ref_scholarship', array('scholarship_status' => 'active'));
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_semester($a_clause = false)
	{
		($a_clause) ? $this->db->where($a_clause) : '';
		$query = $this->db->get('ref_semester');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}

	public function get_list_candidate_status($s_status)
	{
		$this->db->select('rsp.study_program_name, count(*) AS total');
		$this->db->from('dt_student st');
		$this->db->join('ref_study_program rsp', 'rsp.study_program_id = st.study_program_id', 'left');
		$this->db->join('dt_academic_year day', 'day.academic_year_id = st.academic_year_id', 'left');
		$this->db->group_by('st.study_program_id');
		$this->db->where('st.student_status', $s_status);
		$this->db->where('day.academic_year_intake_status', 'active');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function is_student_candidate($s_personal_data_id)
	{
		$this->db->select('*');
		$this->db->from('dt_personal_data pd');
		$this->db->join('dt_student st', 'pd.personal_data_id = st.personal_data_id','left');
		$this->db->where('pd.personal_data_id', $s_personal_data_id);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$o_data = $query->row();
			$a_candidate_true = array('candidate','participant','pending');
			if(in_array($o_data->student_status, $a_candidate_true)) {
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function get_academic_year($s_batch_id = false, $b_active_batch = false)
	{
		if($s_batch_id){
			$this->db->where('academic_year_id', $s_batch_id);
		}
		
		if($b_active_batch){
			$this->db->where('academic_year_intake_status', 'active');
		}
		
		$this->db->order_by('academic_year_id','DESC');
		$query = $this->db->get('dt_academic_year');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_batch($s_batch_id = false, $b_active_batch = false, $s_ordering = 'ASC')
	{
		if($s_batch_id){
			$this->db->where('academic_year_id', $s_batch_id);
		}
		
		if($b_active_batch){
			$this->db->where('academic_year_intake_status', 'active');
		}
		
		$this->db->order_by('academic_year_id', $s_ordering);
		$query = $this->db->get('dt_academic_year');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_dikti_wilayah($s_term = false)
	{
		$this->db->order_by('nama_wilayah');
		if($s_term){
			$this->db->like('nama_wilayah', $s_term);
		}
		$query = $this->db->get('dikti_wilayah');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_country($s_term = false)
	{
		$this->db->order_by('country_name');
		if($s_term){
			$this->db->like('country_name', $s_term);
		}
		$query = $this->db->get('ref_country');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function get_religions()
	{
		$this->db->order_by('religion_name');
		$query = $this->db->get('ref_religion');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}

	public function get_last_invoice($s_personal_data_id)
	{
		$this->db->from('dt_invoice');
		$this->db->where('personal_data_id', $s_personal_data_id);
		$this->db->order_by('date_added', 'DESC');
		$q = $this->db->get();
		return ($q->num_rows() > 0) ? $q->first_row() : false;
	}

	public function send_notification($s_id, $s_message, $s_key, $s_token)
	{
		$uri = "https://api.telegram.org/bot$s_key:$s_token/sendMessage?parse_mode=markdown&chat_id=$s_id";
        $uri .= "&text=".urlencode($s_message);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $rs = curl_exec($ch);
		curl_close($ch);
	}

	public function get_rectorate($s_option = 'rector')
    {
        $this->db->from('ref_department dp');
		$this->db->join('dt_employee em', 'em.employee_id = dp.employee_id');
        $this->db->join('dt_personal_data pd', 'em.personal_data_id = pd.personal_data_id');

		switch ($s_option) {
			case 'rector':
				$this->db->where('department_abbreviation', 'REC');
				break;

			case 'vice_rector':
				$this->db->where('department_abbreviation', 'VREC');
				break;

			case 'human_resource':
				$this->db->where('department_abbreviation', 'HRD');
				break;
			
			default:
				$this->db->where('department_abbreviation', 'REC');
				break;
		}
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $mbo_rectorate = $query->first_row();
            $mbo_rectorate->rector_full_name = $this->retrieve_title($mbo_rectorate->personal_data_id);
        }
        else {
            $mbo_rectorate = false;
        }

        return $mbo_rectorate;
    }

	public function get_head_department($s_dept_abbr)
	{
		$this->db->from('ref_department dp');
		$this->db->join('dt_employee em', 'em.employee_id = dp.employee_id');
        $this->db->join('dt_personal_data pd', 'em.personal_data_id = pd.personal_data_id');
		$this->db->where('department_abbreviation', $s_dept_abbr);
		$query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $mbo_department = $query->first_row();
            $mbo_department->head_full_name = $this->retrieve_title($mbo_department->personal_data_id);
        }
        else {
            $mbo_department = false;
        }

        return $mbo_department;
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
			$s_title_prefix = (!is_null($o_data->personal_data_title_prefix)) ? $o_data->personal_data_title_prefix.'. ' : '';
			return  $s_title_prefix.
					$s_personal_data_name.
					((!is_null($o_data->personal_data_title_suffix)) ? ', ' : '').
					$o_data->personal_data_title_suffix;
		}
		else {
			return '';
		}
	}

	public function generate_sign(
		$s_personal_document_id = false,
		$s_personal_data_id_target = false,
		$s_id_key = false,
		$s_document_token = false,
		$s_document_link,
		$s_id_type = 'letter_number',
		$s_personal_data_id_generated = false
	)
	{
		if (!$s_personal_data_id_generated) {
			$s_personal_data_id_generated = $this->session->userdata('user');
		}
		if (!$s_personal_document_id) {
			$s_personal_document_id = $this->uuid->v4();
		}
		if (!$s_personal_data_id_target) {
			$s_personal_data_id_target = NULL;
		}
		if (!$s_document_token) {
			$s_document_token = md5(date('Y-m-d H:i:s'));
		}
		
		$s_letter_number_id = ($s_id_type == 'letter_number') ? $s_id_key : NULL;
		$s_key_table = ($s_id_type == 'letter_number') ? 'portal_main.dt_letter_number' : $s_id_type;
		$s_date = date('Y-m-d H:i:s');

		$a_sign_data = [
			'personal_document_id' => $s_personal_document_id,
			'personal_data_id_generated' => $s_personal_data_id_generated,
			'personal_data_id_target' => $s_personal_data_id_target,
			'letter_number_id' => $s_letter_number_id,
			'key_id' => ($s_id_key) ? $s_id_key : NULL,
			'key_table' => (!is_null($s_id_key)) ? $s_id_type : NULL,
			'document_token' => $s_document_token,
			'document_link' => $s_document_link,
			'date_added' => $s_date
		];
		$this->db->insert('dt_personal_document', $a_sign_data);
		$this->_generate_qrcode($s_date, $s_document_token);

		switch ($s_id_type) {
			case 'portal_gsr.dt_gsr_main':
				$this->submit_personal_document_gsr($s_id_key, $s_personal_document_id);
				break;

			case 'portal_gsr.dt_df_main':
				$this->submit_personal_document_df($s_id_key, $s_personal_document_id);
				break;
			
			default:
				break;
		}

		return true;
	}

	public function submit_personal_document_gsr($s_gsr_id, $s_personal_document_id)
	{
		$this->db->where('gsr_id', $s_gsr_id);
		$this->db->order_by('date_added', 'DESC');
		$gsr_query = $this->db->get('portal_gsr.dt_gsr_status');

		if ($gsr_query->num_rows() > 0) {
			$o_gsr_status_data = $gsr_query->first_row();
			$this->db->update('portal_gsr.dt_gsr_status', ['personal_document_id' => $s_personal_document_id], ['status_id' => $o_gsr_status_data->status_id]);
		}
	}

	public function submit_personal_document_df($s_df_id, $s_personal_document_id)
	{
		$this->db->where('df_id', $s_df_id);
		$this->db->order_by('date_added', 'DESC');
		$gsr_query = $this->db->get('portal_gsr.dt_df_status');

		if ($gsr_query->num_rows() > 0) {
			$o_gsr_status_data = $gsr_query->first_row();
			$this->db->update('portal_gsr.dt_df_status', ['personal_document_id' => $s_personal_document_id], ['status_id' => $o_gsr_status_data->status_id]);
		}
	}

	private function _generate_qrcode($s_date, $s_token)
    {
		$s_year = date('Y', strtotime($s_date));
		$s_month = date('M', strtotime($s_date));
        $this->load->library('ciqrcode');
		$s_data = base_url().'public/files/show_digitalsign/'.$s_token;
        $image_name='qr_view'.$s_token.'.png';
        $s_file_path = APPPATH.'uploads/qr_view/'.$s_year.'/'.$s_month.'/';

        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

        $config['cacheable']    = true;
        $config['cachedir']     = "application/temp/";
        $config['errorlog']     = "application/temp/";
        $config['quality']      = true;
        $config['size']         = '1024';
        $config['black']        = array(224,255,255);
        $config['white']        = array(70,130,180);
        $config['imagedir']     = $s_file_path;
        $this->ciqrcode->initialize($config);

        $params['data'] = $s_data;
        $params['level'] = 'H';
        $params['size'] = 10;
        $params['savename'] = $config['imagedir'].$image_name;
        $this->ciqrcode->generate($params);
    }
}