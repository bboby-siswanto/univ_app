<?php
class Family extends App_core
{
	public function __construct()
	{
		parent::__construct('profile');
		$this->load->model('personal_data/Family_model', 'Family');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('student/Student_model', 'Stm');
		if (($this->session->userdata('dikti_required') !== NULL) AND ($this->session->userdata('dikti_required') == false)) {
			redirect('personal_data/profile');
		}
	}

	public function get_family_student($s_personal_data_id = 'f9f49573-aa03-4945-acfb-eb69e4d37290')
	{
		$mba_student_family = $this->Family->get_family_by_personal_data_id($s_personal_data_id);
		if ($mba_student_family) {
			$mba_family_data = $this->Family->get_family_lists_filtered(array('fmm.family_id' => $mba_student_family->family_id, 'fmm.family_member_status != ' => 'child'))[0];
			print('<pre>');var_dump($mba_family_data);exit;
		}
		// print('<pre>');
		// var_dump($mba_student_family);exit;
	}

	public function get_family_list()
	{
		if ($this->input->is_ajax_request()) {
			$s_family_id = $this->input->post('family_id');
			$mba_family_list_data = $this->Family->get_family_lists_filtered(['fm.family_id' => $s_family_id]);

			print json_encode(['code' => 0, 'data' => $mba_family_list_data]);
		}
	}

	public function form_add_family($s_personal_data_id)
	{
		if ($s_personal_data_id == null) {
            $s_personal_data_id = $this->session->userdata('user');
		}

		$this->a_page_data['family_data'] = false;
		$this->a_page_data['family_id'] = '';
		$mba_family_data = $this->Family->get_family_by_personal_data_id($s_personal_data_id);
		if ($mba_family_data) {
			$mba_family_lists_data = $this->Family->get_family_lists_filtered(array('fmm.family_id' => $mba_family_data->family_id, 'fmm.family_member_status != ' => 'child'))[0];
			$this->a_page_data['family_id'] = $mba_family_data->family_id;
			$this->a_page_data['family_data'] = $mba_family_lists_data;
		}
		$this->a_page_data['relation_lists'] = $this->General->get_enum_values('dt_family_member', 'family_member_status');
		$this->a_page_data['personal_data_id'] = $s_personal_data_id;
		$this->load->view('form/form_input_family', $this->a_page_data);
	}

	public function view_family_lists_table($s_personal_data_id)
	{
		if ($s_personal_data_id == null) {
            $s_personal_data_id = $this->session->userdata('user');
		}

		// var_dump($s_personal_data_id);exit;

		$mbo_family_data = $this->Family->get_family_by_personal_data_id($s_personal_data_id);
		$this->a_page_data['family_id'] = ($mbo_family_data) ? $mbo_family_data->family_id : false;

		// $s_btn_html = Modules::run('layout/generate_buttons', 'profile', 'academic_history');
		$this->a_page_data['personal_data_id'] = $s_personal_data_id;
		// $this->a_page_data['btn_html'] = $s_btn_html;
		$this->load->view('table/family_lists_table', $this->a_page_data);
	}

	public function family_test($s_personal_data_id = null)
	{
		if ($s_personal_data_id == null) {
            $s_personal_data_id = $this->session->userdata('user');
		}
		
		$o_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
        if ($o_personal_data) {
			if ($student_data = $this->Stm->get_student_filtered(array('ds.personal_data_id' => $s_personal_data_id, 'ds.student_status' => 'active'))) {
				$this->a_page_data['student_data'] = $student_data[0];
			}

	        $this->a_page_data['personal_data_id'] = $s_personal_data_id;
            $this->a_page_data['o_personal_data'] = $o_personal_data;
			$this->a_page_data['body'] = $this->load->view('family_default', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
        }
	}

	public function family_lists($s_student_id = false, $s_personal_data_id = null)
	{
		if ($s_personal_data_id == null) {
            $s_personal_data_id = $this->session->userdata('user');
		}
		
		$o_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
        if ($o_personal_data) {
			if ($student_data = $this->Stm->get_student_filtered(array('ds.personal_data_id' => $s_personal_data_id, 'ds.student_status' => 'active'))) {
				$this->a_page_data['student_data'] = $student_data[0];
			}

	        $this->a_page_data['personal_data_id'] = $s_personal_data_id;
            $this->a_page_data['o_personal_data'] = $o_personal_data;
			$this->a_page_data['body'] = $this->load->view('family_default', $this->a_page_data, true);
			// $this->a_page_data['body'] = $this->load->view('dashboard/maintenance_page', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
        }
	}
	
	public function save_family_member()
	{
		if($this->input->is_ajax_request()){
			$this->load->model('institution/Institution_model', 'Institution');
			
			$s_family_id = $this->input->post('family_id');
			$s_personal_data_id = $this->input->post('personal_data_id');
			
			$this->form_validation->set_rules('name', 'Name', 'trim|required|alpha_numeric_spaces');
			// $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('email', 'Email', 'trim');
			$this->form_validation->set_rules('type', 'Family Status', 'trim|required');
			$this->form_validation->set_rules('personal_data_phone', 'Parent Phone', 'trim|numeric');
			$this->form_validation->set_rules('phone', 'Parent Cellular', 'trim|required|numeric');
			
			if($this->form_validation->run()){
				$this->db->trans_start();

				$mbo_personal_data = $this->Pdm->get_personal_data_by_email(set_value('email'));
				
				$b_proceed = true;

				if (($this->input->post('personal_data_id_parent') !== null) AND ($this->input->post('personal_data_id_parent') != '')) {
					$mbo_personal_data = false;
				}
				
				// if there is a personal_data associated with the email
				if($mbo_personal_data){
					$s_family_member_personal_data_id = $mbo_personal_data->personal_data_id;
					
					$mbo_family_member = $this->Family->get_family_by_personal_data_id($mbo_personal_data->personal_data_id);
					// if there is a family associated to the personal_data
					if($mbo_family_member){
						// if the family is not the same but has family, delete the old family then add new member to the existing family
						if($s_family_id != $mbo_family_member->family_id){
							$this->Family->delete_family($s_family_id);
							$a_family_member = array(
								'family_id' => $mbo_family_member->family_id,
								'personal_data_id' => $s_personal_data_id,
								'family_member_status' => 'child',
								'date_added' => date('Y-m-d H:i:s', time())
							);
							$this->Family->add_family_member($a_family_member);
							// $a_return = array('code' => 2, 'message' => 'Siblings and family detected, reloading the page');
							$a_return = array('code' => 2, 'message' => 'reloading the page');
						}
						else{
							$this->db->trans_rollback();
							$b_proceed = false;
							$a_return = array('code' => 1, 'message' => 'Can not add family member. Email already exists');
						}
					}
					// if there is no family associated to the personal_data, add to the family member
					else{
						$a_family_member = array(
							'family_id' => $s_family_id,
							'family_member_status' => set_value('type'),
							'personal_data_id' => $mbo_personal_data->personal_data_id,
							'date_added' => date('Y-m-d H:i:s', time())
						);
						$this->Family->add_family_member($a_family_member);
						
						$a_return = array('code' => 0);
					}
				}
				else{
					$a_personal_data = array(
						'personal_data_name' => set_value('name'),
						'personal_data_email' => set_value('email'),
						'personal_data_cellular' => set_value('phone'),
						'personal_data_phone' => set_value('personal_data_phone'),
						'date_added' => date('Y-m-d H:i:s', time())
					);

					if ($this->input->post('occupation_name') != '') {
						if ($this->input->post('occupation_id') == '') {
							$mba_occupation_data = $this->Institution->occupation_suggestions($this->input->post('occupation_name', true))[0];
							if ($mba_occupation_data) {
								$a_personal_data['ocupation_id'] = $mba_occupation_data->ocupation_id;
							}else{
								$s_occupation_id = $this->uuid->v4();
								$a_personal_data['ocupation_id'] = $s_occupation_id;

								$a_occupation_data = array(
									'ocupation_id' => $s_occupation_id,
									'ocupation_name' => $this->input->post('occupation_name')
								);

								$this->Institution->insert_occupation($a_occupation_data);
							}
						}else {
							$a_personal_data['ocupation_id'] = $this->input->post('occupation_id');
						}
					}

					if (($this->input->post('personal_data_id_parent') !== null) AND ($this->input->post('personal_data_id_parent') != '')) {
						$this->Pdm->update_personal_data($a_personal_data, $this->input->post('personal_data_id_parent'));
						$a_family_member_data = array(
							'family_member_status' => set_value('type')
						);
						$s_family_member_personal_data_id = $this->input->post('personal_data_id_parent');

						$this->Family->update_family_member($a_family_member_data, array('family_id' => $s_family_id, 'personal_data_id' => $this->input->post('personal_data_id_parent')));
					}else{
						$s_family_member_personal_data_id = $this->Pdm->create_new_personal_data($a_personal_data);
						
						$a_family_member = array(
							'family_id' => $s_family_id,
							'family_member_status' => set_value('type'),
							'personal_data_id' => $s_family_member_personal_data_id,
							'date_added' => date('Y-m-d H:i:s', time())
						);
						$this->Family->add_family_member($a_family_member);
					}
				}
				
				if ($this->input->post('company_name') != '') {
					if($this->input->post('institution_id') == ''){
						$s_institution_name = strtoupper(trim($this->input->post('company_name')));
						$a_institution_data = array(
							'institution_name' => $s_institution_name,
							'date_added' => date('Y-m-d H:i:s', time())
						);
						$s_institution_id = $this->Institution->insert_institution($a_institution_data);
/*
						$mbo_institution_data = $this->Institution->get_institution_by_name($s_institution_name);
						if($mbo_institution_data){
							$s_institution_id = $mbo_institution_data->institution_id;
						}
						else{
							$a_institution_data = array(
								'institution_name' => $s_institution_name,
								'date_added' => date('Y-m-d H:i:s', time())
							);
							$s_institution_id = $this->Institution->insert_institution($a_institution_data);
						}
*/
					}
					else{
						$s_institution_id = $this->input->post('institution_id');
					}

					$mba_academic_history = $this->Pdm->get_academic_history($s_family_member_personal_data_id);
					if($mba_academic_history){
						$o_academic_history = $mba_academic_history[0];
						if($o_academic_history->institution_id != $s_institution_id){
							$this->Pdm->update_academic_history($s_family_member_personal_data_id, $o_academic_history->institution_id, $s_institution_id);
						}
					}
					else{
						if(!$this->Pdm->insert_academic_history($s_family_member_personal_data_id, $s_institution_id)){
							$b_proceed = false;
							$a_return = array('code' => 3, 'message' => 'Transaction rejected insert occupation');
						}
					}
				}
				
				if($b_proceed){
					$update_complete_data = $this->General->update_data('dt_personal_data', ['has_completed_parents_data' => '0'], ['personal_data_id' => $s_personal_data_id]);
					$mba_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $s_personal_data_id]);
					if ($mba_student_data) {
						if ($mba_student_data[0]->student_status == 'register') {
							$mba_personal_data_new = $this->General->get_where('dt_personal_data', ['personal_data_id' => $s_personal_data_id]);
							if ($mba_personal_data_new) {
								$o_new_personal_data = $mba_personal_data_new[0];
								if ((!is_null($o_new_personal_data->has_completed_personal_data)) AND (!is_null($o_new_personal_data->has_completed_school_data))) {
									$update_student_data = $this->General->update_data('dt_student', ['student_status' => 'candidate'], ['student_id' => $mba_student_data[0]->student_id]);
								}
							}
						}
					}

					if($this->db->trans_status() === false){
						$this->db->trans_rollback();
						$a_return = array('code' => 1, 'message' => 'Error');
					}
					else{
						$this->db->trans_commit();
						$a_return = array('code' => 0);
					}
				}
				else{
					$this->db->trans_rollback();
					// $a_return = array('code' => 3, 'message' => 'Transaction rejected');
				}
			}
			else{
				$a_return = array('code' => 1, 'message' => validation_errors('<p>', '</p>'));
			}
			
			print json_encode($a_return);
			exit;
		}
	}
}