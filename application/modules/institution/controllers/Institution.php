<?php
class Institution extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('institution/Institution_model', 'Institution');
	}
	
/*
	public function insert_institution($a_institution_data)
	{
		if(!array_key_exists('institution_id', $a_institution_data)){
			$a_institution_data['institution_id'] = $this->uuid->v4();
		}
		
		$this->db->insert('ref_institution', $a_institution_data);
		return ($a_institution_data['institution_id']);
	}
*/
	
/*
	public function get_institution_by_name($s_institution_name)
	{
		$query = $this->db->get_where('ref_institution', array('institution_name' => $s_institution_name));
		return ($query->num_rows() == 1) ? $query->first_row() : false;
	}
*/

	public function get_institutions_ajax()
	{
		if ($this->input->is_ajax_request()) {
			$s_instution_name = $this->input->post('term');
			$is_university = $this->input->post('university');
			if ($is_university == 'true') {
				$a_clause = array('ri.institution_type' => 'university');
			}else{
				$a_clause = false;
			}

			// var_dump($is_university);exit;
			$mba_institution_data = $this->Institution->get_institution_data($a_clause, $s_instution_name, true);

			print json_encode(array('code' => 0, 'data' => $mba_institution_data));
		}
	}

	public function details($s_institution_id)
	{
		$this->a_page_data['institution_data'] = $this->Institution->get_institution_by_id($s_institution_id);
		$this->a_page_data['body'] = $this->load->view('details', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function unique_institution_name()
	{
		$s_institution_name = strtoupper($this->input->post('institution_name'));
		$mba_institution_result = $this->Institution->institution_suggestions($s_institution_name, false, true);
		if($this->input->post('institution_id') == ''){
			if($mba_institution_result){
				$this->form_validation->set_message('unique_institution_name', $s_institution_name.' is already in the DB');
				return false;
			}
			else{
				return true;
			}
		}
		else{
			return true;
		}
	}

	public function save_institution()
	{
		if($this->input->is_ajax_request()){
			$this->load->model('address/Address_model', 'Am');
			
			$this->form_validation->set_rules('institution_name', 'Institution Name', 'trim|required|callback_unique_institution_name');
			$this->form_validation->set_rules('institution_type', 'Institution Type', 'trim|required');
			$this->form_validation->set_rules('institution_is_international', 'Institution Type', 'trim|required');
			$this->form_validation->set_rules('institution_email', 'Institution Email', 'trim|valid_email');
			$this->form_validation->set_rules('institution_phone_number', 'Institution Phone', 'trim|required');
			$this->form_validation->set_rules('address_street', 'Street', 'required');
			$this->form_validation->set_rules('country_id', 'Country', 'trim|required');
			$this->form_validation->set_rules('address_province', 'Province', 'trim|required');
			$this->form_validation->set_rules('address_city', 'City', 'trim|required');
			$this->form_validation->set_rules('address_zipcode', 'Zip Code', 'trim|required');
			
			if($this->form_validation->run()){
				$this->db->trans_start();
				
				$a_address_data = array(
					'address_province' => strtoupper(set_value('address_province')),
					'address_city' => strtoupper(set_value('address_city')),
					'address_zipcode' => set_value('address_zipcode'),
					'address_street' => strtoupper(set_value('address_street')),
					'country_id' => set_value('country_id')
				);
				
				if($s_address_id = $this->input->post('address_id')){
					$this->Am->save_address($a_address_data, $s_address_id);
				}
				else{
					$s_address_id = $this->Am->save_address($a_address_data);
				}

				$a_institution_data = array(
					'address_id' => $s_address_id,
					'institution_name' => strtoupper(set_value('institution_name')),
					'institution_type' => set_value('institution_type'),
					'institution_is_international' => set_value('institution_is_international'),
					'institution_email' => (set_value('institution_email') == '') ? null : set_value('institution_email'),
					'institution_phone_number' => set_value('institution_phone_number')
				);
				if($s_institution_id = $this->input->post('institution_id')){
					$this->Institution->insert_institution($a_institution_data, $s_institution_id);
				}
				else{
					$s_institution_id = $this->Institution->insert_institution($a_institution_data);
				}
				
				if($this->db->trans_status() === false){
					$this->db->trans_rollback();
					$a_return = array('code' => 1, 'message' => 'Unknown error');
				}
				else{
					$this->db->trans_commit();
					$a_return = array('code' => 0, 'message' => 'Success!');
				}
			}
			else{
				$a_return = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
			}
			
			print json_encode($a_return);
			exit;
		}
	}

	public function load_all_institutions()
	{
		if($this->input->is_ajax_request()){
			$mba_institution_result = $this->Institution->institution_datatable($this->input->post());
			$a_display_data = false;
			$i_total_records = $i_filtered_records = 0;
			
			if($mba_institution_result){
				$iteration = 1;
				$a_display_data = [];
				$i_total_records = $mba_institution_result['total_records'];
				$i_filtered_records = $mba_institution_result['num_rows'];
				
				foreach($mba_institution_result['result'] as $institution){
					$a_item = [
						'institution_name' => $institution->institution_name,
						'institution_email' => $institution->institution_email,
						'institution_phone_number' => $institution->institution_phone_number,
						'institution_type' => $institution->institution_type,
						'address_id' => $institution->address_id,
						'institution_id' => $institution->institution_id
					];
					array_push($a_display_data, $a_item);
				}
				$iteration++;
			}
			
			$output = [
				'draw' => intval($this->input->post('draw')),
                'recordsTotal' => $i_total_records,
                'recordsFiltered' => $i_filtered_records,
                'data' => $a_display_data,
                'post_data' => $this->input->post()
           ];  
           print json_encode($output);
           exit;
		}
	}

	public function submit_international_university()
	{
		if ($this->input->is_ajax_request()) {
			$this->form_validation->set_rules('univ_institution_name', 'Name of University', 'trim|required');
			$this->form_validation->set_rules('univ_institution_country', 'Country of University', 'trim|required');
			$this->form_validation->set_rules('univ_institution_email', 'University Email', 'trim');
			$this->form_validation->set_rules('univ_institution_phone', 'University Phone', 'trim');

			if ($this->form_validation->run()) {
				$a_institution_data = [
					'country_id' => set_value('univ_institution_country'),
					'institution_name' => set_value('univ_institution_name'),
					'institution_email' => (!empty(set_value('univ_institution_email'))) ? set_value('univ_institution_email') : NULL,
					'institution_phone_number' => (!empty(set_value('univ_institution_phone'))) ? set_value('univ_institution_phone') : NULL,
					'institution_type' => 'university',
					'institution_is_international' => 'yes'
				];

				if (!empty($this->input->post('univ_institution_id'))) {
					$s_institution_id = $this->input->post('univ_institution_id');

					$this->Institution->insert_institution($a_institution_data, $s_institution_id);
				}
				else {
					$a_institution_data['institution_id'] = $this->uuid->v4();
					$a_institution_data['date_added'] = date('Y-m-d H:i:s');

					$this->Institution->insert_institution($a_institution_data);
				}

				$a_return = ['code' => 0, 'message' => 'Success!'];
			}
			else {
				$a_return = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
			}

			print json_encode($a_return);exit;
		}
	}

	public function filter_result()
	{
		if($this->input->is_ajax_request()){
			$msa_institution_type = $this->input->post('institution_type');
			if((!is_array($msa_institution_type)) AND ($msa_institution_type == 'all')){
				$mba_institution_result = $this->Institution->institution_suggestions();
			}
			else{
				$mba_institution_result = $this->Institution->institution_suggestions(false, $msa_institution_type);
			}
			
			$a_return = array('code' => 0, 'data' => $mba_institution_result);
			print json_encode($a_return);
			exit;
		}
	}
	
	public function form_institution()
	{
		$this->a_page_data['institution_type'] = $this->General->get_enum_values('ref_institution', 'institution_type');
		$this->a_page_data['institution_is_international'] = $this->General->get_enum_values('ref_institution', 'institution_is_international');
		// if($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0'){
		// 	$this->load->view('form/form_institution_dev', $this->a_page_data);
		// }
		// else{
			$this->load->view('form/form_institution', $this->a_page_data);
		// }
	}

	public function institution_table()
	{
		$this->a_page_data['btn_html'] = modules::run('layout/generate_buttons', $this->session->userdata('module'), 'institution');
		$this->a_page_data['modal_html'] = modules::run('layout/generate_modals', $this->session->userdata('module'), 'institution');
		$this->load->view('table/institution_table', $this->a_page_data);
	}
	
	public function lists()
	{
		$this->a_page_data['body'] = $this->load->view('default', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	public function get_occupation_by_name()
	{
		if($this->input->is_ajax_request()){
			$s_term = $this->input->post('term');
			
			$mba_occupation_list = $this->Institution->occupation_suggestions($s_term);
			$a_return = array('code' => 0, 'data' => $mba_occupation_list);
			print json_encode($a_return);
			exit;
		}
	}

	public function get_occupation()
	{
		if($this->input->is_ajax_request()){
			$s_term = $this->input->post('term');
			
			$mba_occupation_list = $this->Institution->occupation_suggestions($s_term);
			$a_return = array('code' => 0, 'data' => $mba_occupation_list);
			print json_encode($a_return);
			exit;
		}
	}

	public function get_institution_partner()
	{
		if($this->input->is_ajax_request()){
			$s_term = $this->input->post('term');
			
			$mba_institution_list = $this->Institution->get_institution_data([
				'ri.institution_type' => 'university',
				'ri.institution_is_international' => 'yes'
			], $s_term);

			$a_return = array('code' => 0, 'data' => $mba_institution_list);
			print json_encode($a_return);
			exit;
		}
	}

	public function get_institutions()
	{
		if($this->input->is_ajax_request()){
			$s_term = $this->input->post('term');
			$s_type = (!empty($this->input->post('type'))) ? $this->input->post('type') : false;
			$a_clause = false;
			if ($s_type) {
				$a_clause = [
					'ri.institution_type' => $s_type
				];
			}
			
			$mba_institution_list = $this->Institution->get_institution_data($a_clause, $s_term);
			$a_return = array('code' => 0, 'data' => $mba_institution_list);
			print json_encode($a_return);
			exit;
		}
	}
	
	public function get_institution()
	{
		if($this->input->is_ajax_request()){
			$s_term = $this->input->post('term');
			
			$mba_institution_list = $this->Institution->institution_suggestions($s_term);
			$a_return = array('code' => 0, 'data' => $mba_institution_list);
			print json_encode($a_return);
			exit;
		}
	}

	public function get_country_by_name()
	{
		if ($this->input->is_ajax_request()) {
			$this->load->model('General_model', 'Gm');
			$s_term = $this->input->post('term');
			
			$mbo_country_list = $this->Gm->get_country($s_term);
			$a_return = array('code' => 0, 'data' => $mbo_country_list);
			echo json_encode($a_return);
			exit;
		}
	}
}