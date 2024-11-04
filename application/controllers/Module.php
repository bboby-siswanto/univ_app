<?php
class Module extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('student/Student_model', 'Stm');
		if (($this->session->userdata('dikti_required') !== NULL) AND ($this->session->userdata('dikti_required') == false)) {
			if (($this->router->fetch_module() != 'personal_data') AND ($this->router->fetch_method() != 'profile')) {
				$o_student_data = $this->Stm->get_student_filtered(array('ds.personal_data_id' => $this->session->userdata('user')))[0];
				if ($this->validate_dikti_required($o_student_data)) {
					$this->session->unset_userdata('dikti_required');
				}else{
					redirect('personal_data/profile');
				}
			}
		}
	}
	
	public function set($s_module_name)
	{
		$a_module_staff = ['academic', 'finance', 'admission'];
		if (in_array($s_module_name, $a_module_staff)) {
			if ($this->session->userdata('type') == 'student') {
				$s_module_name = 'student_'.$s_module_name;
			}
		}
		$this->session->set_userdata('module', $s_module_name);
		// if ($this->session->userdata('type') == 'student') {
		// 	if (($this->session->userdata('dikti_required') !== NULL) AND ($this->session->userdata('dikti_required') == false)) {
		// 		$o_student_data = $this->Stm->get_student_filtered(array('ds.personal_data_id' => $this->session->userdata('user')))[0];
		// 		if ($this->validate_dikti_required($o_student_data)) {
		// 			var_dump('sorry!');exit;
		// 			// $this->session->userdata('dikti_required') = true;
		// 			// $this->session->set_userdata('dikti_required', 'true');
		// 			$this->session->unset_userdata('dikti_required');
		// 		}
		// 	}
		// }
		redirect(site_url('dashboard'));
	}

	public function validate_dikti_required($o_student_data)
	{
		if ((is_null($o_student_data->personal_data_mother_maiden_name)) OR ($o_student_data->personal_data_mother_maiden_name == '')) {
			return false;
		}else if ((is_null($o_student_data->personal_data_id_card_number)) OR ($o_student_data->personal_data_id_card_number == '')) {
			return false;
		}else if ((is_null($o_student_data->personal_data_place_of_birth)) OR ($o_student_data->personal_data_place_of_birth == '')) {
			return false;
		}else if ((is_null($o_student_data->personal_data_date_of_birth)) OR ($o_student_data->personal_data_date_of_birth == '')) {
			return false;
		}else if ((is_null($o_student_data->citizenship_id)) OR ($o_student_data->citizenship_id == '')) {
			return false;
		}else if ((is_null($o_student_data->address_sub_district)) OR ($o_student_data->address_sub_district == '')) {
			return false;
		}else if ((is_null($o_student_data->dikti_wilayah_id)) OR ($o_student_data->dikti_wilayah_id == '')) {
			return false;
		}else{
			return true;
		}
	}
}