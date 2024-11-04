<?php
class Employee extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('employee/Employee_model', 'Emm');
		$this->load->model('hris/Hris_model', 'Hrm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('File_manager_model', 'File_manager');
		$this->load->library('FeederAPI', ['mode' => 'production']);
	}

	public function profile($s_employee_id = false, $s_personal_data_id = false)
	{
		if (!$s_personal_data_id) {
			show_404();
		}

		$mba_employee_data = $this->Emm->get_employee_data([
			'em.employee_id' => $s_employee_id
		]);

		if (!$mba_employee_data) {
			show_404();
		}
		else {
			$this->a_page_data['personal_data'] = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
			$this->a_page_data['personal_data_id'] = $s_personal_data_id;
			
			$this->a_page_data['body'] = $this->load->view('employee/staff_profile', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}
	}

	public function form_filter()
	{
		$this->load->view('form/form_filter', $this->a_page_data);
	}

	public function form_input()
	{
		$this->load->view('form/form_input', $this->a_page_data);
	}

	public function get_employee_by_name()
	{
		if($this->input->is_ajax_request()){
			$s_keyword = $this->input->post('keyword');
			$s_status = $this->input->post('status');
			
			if (($s_status !== null) AND ($s_status != '')) {
				$a_clause['de.status'] = strtoupper($s_status);
				$mba_employee_list = $this->Emm->get_employee_by_name($s_keyword, $a_clause);
			}else{
				$mba_employee_list = $this->Emm->get_employee_by_name($s_keyword);
			}
			
			print json_encode(array('code' => 0, 'data' => $mba_employee_list));
			exit;
		}
	}

	public function employee_lists()
	{
		$this->a_page_data['body'] = $this->load->view('employee_lists', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	function submit_employee_department() {
		if ($this->input->is_ajax_request()) {
			$s_employee_id = $this->input->post('employee_id');
			$a_dept_abbr = $this->input->post('data');
			if ((is_array($a_dept_abbr)) AND (count($a_dept_abbr) > 0)) {
				$this->db->trans_begin();
				foreach ($a_dept_abbr as $s_dept_abbr) {
					$mba_dept_data = $this->General->get_where('ref_department', ['department_abbreviation' => $s_dept_abbr]);
					$mba_employee_dept = $this->Emm->get_employee_sub_department(['ed.employee_id' => $s_employee_id]);
					$a_dept_em = [];
					if ($mba_employee_dept) {
						foreach ($mba_employee_dept as $o_dept) {
							if (!in_array($o_dept->department_abbreviation, $a_dept_abbr)) {
								$this->General->force_delete('dt_employee_department', 'employee_department_id', $o_dept->employee_department_id);
							}
						}
					}
					if ($mba_dept_data) {
						$s_dept_id = $mba_dept_data[0]->department_id;
						$mba_data_exists = $this->General->get_where('dt_employee_department', ['employee_id' => $s_employee_id, 'department_id' => $s_dept_id]);

						$a_data = [
							'employee_id' => $s_employee_id,
							'department_id' => $s_dept_id
						];
						if (!$mba_data_exists) {
							$this->General->insert_data('dt_employee_department', $a_data);
						}
					}
				}

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$a_return = ['code' => 2, 'message' => 'Departments not synchronized'];
				}
				else {
					$this->db->trans_commit();
					$a_return = ['code' => 0, 'message' => 'Success'];
				}
			}
			else {
				$a_return = ['code' => 1, 'message' => 'No department selected!'];
			}

			print json_encode($a_return);
		}
	}

	function lecturer_list_pddikti() {
		$mba_forlapdosen = $this->feederapi->post('GetListDosen');
		$mba_dosenlist = false;
		if (($mba_forlapdosen->error_code == 0) AND (count($mba_forlapdosen->data) > 0)) {
			$mba_dosenlist = $mba_forlapdosen->data;
			foreach ($mba_dosenlist as $o_dosen) {
				$iuli_exist = $this->General->get_where('dt_employee', ['employee_id' => $o_dosen->id_dosen]);
				$o_dosen->iuli_exists = ($iuli_exist) ? true : false;
			}
		}

		$this->a_page_data['lecturer_list'] = $mba_dosenlist;
		$this->a_page_data['body'] = $this->load->view('lecturer_list_feeder', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}

	function staff_data($s_employee_id = false) {
		if ($s_employee_id) {
			$mba_forlapbiodata = $this->feederapi->post('DetailBiodataDosen', [
				'filter' => "id_dosen = '$s_employee_id'"
			]);
			$mba_forlappenugasan = $this->feederapi->post('GetListPenugasanDosen', [
				'filter' => "id_dosen = '$s_employee_id'"
			]);
			$mba_forlapfungsional = $this->feederapi->post('GetRiwayatFungsionalDosen', [
				'filter' => "id_dosen = '$s_employee_id'"
			]);
			$mba_forlappangkat = $this->feederapi->post('GetRiwayatPangkatDosen', [
				'filter' => "id_dosen = '$s_employee_id'"
			]);
			$mba_forlappendidikan = $this->feederapi->post('GetRiwayatPendidikanDosen', [
				'filter' => "id_dosen = '$s_employee_id'"
			]);
			$mba_forlapsertifikasi = $this->feederapi->post('GetRiwayatSertifikasiDosen', [
				'filter' => "id_dosen = '$s_employee_id'"
			]);
			$mba_forlappenelitian = $this->feederapi->post('GetRiwayatPenelitianDosen', [
				'filter' => "id_dosen = '$s_employee_id'"
			]);
			$mba_employee_data = $this->General->get_where('dt_employee', ['employee_id' => $s_employee_id]);
			$a_avatar = false;
			$dekan = false;
			$kaprodi = false;
			if ($mba_employee_data) {
				$a_avatar = $this->File_manager->get_files($mba_employee_data[0]->personal_data_id, '0bde3152-5442-467a-b080-3bb0088f6bac');
				$dekan = $this->General->get_where('ref_faculty', ['deans_id' => $mba_employee_data[0]->personal_data_id]);
				$kaprodi = $this->General->get_where('ref_study_program', ['head_of_study_program_id' => $mba_employee_data[0]->personal_data_id]);
			}
			$this->a_page_data['biodata'] = (($mba_forlapbiodata->error_code == 0) AND (count($mba_forlapbiodata->data) > 0)) ? $mba_forlapbiodata->data[0] : false;
			$this->a_page_data['fungsional'] = (($mba_forlapfungsional->error_code == 0) AND (count($mba_forlapfungsional->data) > 0)) ? $mba_forlapfungsional->data : false;
			$this->a_page_data['penugasan'] = (($mba_forlappenugasan->error_code == 0) AND (count($mba_forlappenugasan->data) > 0)) ? $mba_forlappenugasan->data : false;
			$this->a_page_data['pangkat'] = (($mba_forlappangkat->error_code == 0) AND (count($mba_forlappangkat->data) > 0)) ? $mba_forlappangkat->data : false;
			$this->a_page_data['pendidikan'] = (($mba_forlappendidikan->error_code == 0) AND (count($mba_forlappendidikan->data) > 0)) ? $mba_forlappendidikan->data : false;
			$this->a_page_data['sertifikasi'] = (($mba_forlapsertifikasi->error_code == 0) AND (count($mba_forlapsertifikasi->data) > 0)) ? $mba_forlapsertifikasi->data : false;
			$this->a_page_data['penelitian'] = (($mba_forlappenelitian->error_code == 0) AND (count($mba_forlappenelitian->data) > 0)) ? $mba_forlappenelitian->data : false;
			$this->a_page_data['a_avatar'] = $a_avatar;
			$this->a_page_data['is_deans'] = $dekan;
			$this->a_page_data['is_hod'] = $kaprodi;
			$this->a_page_data['body'] = $this->load->view('staff_data', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}
		else {
			show_404();
		}
	}

	function get_feeder_data_dosen($id_dosen = '', $s_function = '') {
		if ($this->input->is_ajax_request()) {
			$id_dosen = $this->input->post('id_dosen');
			$s_function = $this->input->post('fungsi');
		}

		$mba_data = false;
		if ((!empty($id_dosen)) AND (!empty($s_function))) {
			$mba_forlapdosen = $this->feederapi->post($s_function, [
				'filter' => "id_dosen = '$id_dosen'"
			]);
			if (($mba_forlapdosen->error_code == 0) AND (count($mba_forlapdosen->data) > 0)) {
				$mba_data = $mba_forlapdosen->data;
			}
		}

		if ($this->input->is_ajax_request()) {
			print json_encode(['data' => $mba_data]);
		}
		else {
			return $mba_data;
		}
	}

	public function view_employee_lists()
	{
		$this->a_page_data['list_dept'] = $this->Emm->get_department_list();
		$this->load->view('table/employee_lists_table', $this->a_page_data);
	}

	public function get_lecturer_sugestion()
	{
		if ($this->input->is_ajax_request()) {
			$s_personal_data_name = $this->input->post('term');

			$mbo_employee_lists = $this->Emm->get_lecturer_by_name($s_personal_data_name, false, true);
			$a_return = array('code' => 0, 'data' => $mbo_employee_lists);
			print json_encode($a_return);
			exit;
		}
	}

	public function get_lecturer_by_name()
	{
		if($this->input->is_ajax_request()){
			$s_keyword = $this->input->post('keyword');
			$s_have_nidn = $this->input->post('have_nidn');

			// $a_clause = ((isset($s_lecturer)) AND ($s_lecturer != '')) ? (['employee_is_lecturer' => 'YES']) : false;
			if ((isset($s_have_nidn)) AND ($s_have_nidn != '')) {
				$mba_data = $this->Emm->get_lecturer_by_name($s_keyword, true);
			}else{
				$mba_data = $this->Emm->get_lecturer_by_name($s_keyword);
			}
			
			if ($mba_data) {
				foreach ($mba_data as $o_lecturer) {
					$o_lecturer->fullname = $this->General->retrieve_title($o_lecturer->personal_data_id);
				}
			}
			print json_encode(array('code' => 0, 'data' => $mba_data));
			exit;
		}
	}

	public function academic_login()
	{
		$s_personal_data_id = $this->session->userdata('user');
		$mbo_user_login = $this->Emm->get_employee_data(array('em.personal_data_id' => $this->session->userdata('user')))[0];
		# id number of suhendin = 121502
		if ($mbo_user_login->employee_id_number == '221911') {
			return true;
		}else{
			return false;
		}
	}

	public function generate_reference_letter($s_employee_id)
	{
		$this->load->model('apps/Letter_numbering_model', 'Lnm');
		$mba_employee_data = $this->Emm->get_employee_data(['em.employee_id' => $s_employee_id]);
		$s_link_prev = $_SERVER['HTTP_REFERER'];
		
		if ($mba_employee_data) {
			$s_template_id = '22';
        	$s_letter_type_id = '4';

			$o_employee_data = $mba_employee_data[0];
            $mba_letter = $this->Lnm->get_template(false, [
                'template_id' => $s_template_id
            ]);
            
            if (!$mba_letter) {
                print('<script>alert("Template not found!")</script>');
				redirect($s_link_prev);exit;
            }

			$a_result_generate = modules::run('apps/letter_numbering/generate_reference_letter_employee', $s_employee_id);
			if ($a_result_generate['code'] == 0) {
				// print('<pre>masuk');var_dump($a_result_generate);exit;
				$s_link_next = base_url().'apps/letter_numbering/download_template_result/'.$a_result_generate['file'].'/'.$a_result_generate['doc_key'];
				redirect($s_link_next);exit;
			}
			else {
				print('<script>alert("'.$a_result_generate['message'].'")</script>');
				redirect($s_link_prev);exit;
			}
		}
		else {
			print('<script>alert("Employee not found!")</script>');
			redirect($s_link_prev);exit;
		}
	}

	public function generate_lolos_butuh_employee($s_employee_id)
	{
		$this->load->model('apps/Letter_numbering_model', 'Lnm');
		$mba_employee_data = $this->Emm->get_employee_data(['em.employee_id' => $s_employee_id]);
		$s_link_prev = $_SERVER['HTTP_REFERER'];
		
		if ($mba_employee_data) {
			$s_template_id = '23';
        	$s_letter_type_id = '4';

			$o_employee_data = $mba_employee_data[0];
            $mba_letter = $this->Lnm->get_template(false, [
                'template_id' => $s_template_id
            ]);
            
            if (!$mba_letter) {
                print('<script>alert("Template not found!")</script>');
				redirect($s_link_prev);exit;
            }

			$a_result_generate = modules::run('apps/letter_numbering/generate_lolos_butuh_employee', $s_employee_id);
			if ($a_result_generate['code'] == 0) {
				// print('<pre>masuk');var_dump($a_result_generate);exit;
				$s_link_next = base_url().'apps/letter_numbering/download_template_result/'.$a_result_generate['file'].'/'.$a_result_generate['doc_key'];
				redirect($s_link_next);exit;
			}
			else {
				print('<script>alert("'.$a_result_generate['message'].'")</script>');
				redirect($s_link_prev);exit;
			}
		}
		else {
			print('<script>alert("Employee not found!")</script>');
			redirect($s_link_prev);exit;
		}
	}

	public function generate_reference_letter_resign($s_employee_id)
	{
		$this->load->model('apps/Letter_numbering_model', 'Lnm');
		$mba_employee_data = $this->Emm->get_employee_data(['em.employee_id' => $s_employee_id]);
		$s_link_prev = $_SERVER['HTTP_REFERER'];
		
		if ($mba_employee_data) {
			$s_template_id = '24';
        	$s_letter_type_id = '4';

			$o_employee_data = $mba_employee_data[0];
            $mba_letter = $this->Lnm->get_template(false, [
                'template_id' => $s_template_id
            ]);
            
            if (!$mba_letter) {
                print('<script>alert("Template not found!")</script>');
				redirect($s_link_prev);exit;
            }

			$a_result_generate = modules::run('apps/letter_numbering/generate_reference_letter_resign', $s_employee_id);
			if ($a_result_generate['code'] == 0) {
				// print('<pre>masuk');var_dump($a_result_generate);exit;
				$s_link_next = base_url().'apps/letter_numbering/download_template_result/'.$a_result_generate['file'].'/'.$a_result_generate['doc_key'];
				redirect($s_link_next);exit;
			}
			else {
				print('<script>alert("'.$a_result_generate['message'].'")</script>');
				redirect($s_link_prev);exit;
			}
		}
		else {
			print('<script>alert("Employee not found!")</script>');
			redirect($s_link_prev);exit;
		}
	}

	public function get_filter_data()
	{
		if ($this->input->is_ajax_request()) {
			$s_employee_status = $this->input->post('employee_status');
			$s_employee_type = $this->input->post('employee_type');
			$a_filter_data = [
				'em.status' => $s_employee_status,
				'em.employee_is_lecturer' => ($s_employee_type == 'lecturer') ? 'YES' : 'NO'
			];

			if ($s_employee_status == 'all') {
				unset($a_filter_data['em.status']);
			}
			
			if ($s_employee_type == 'all') {
				unset($a_filter_data['em.employee_is_lecturer']);
			}

			if (count($a_filter_data) == 0) {
				$a_filter_data = false;
			}
			
			$mbo_employee_lists = $this->Hrm->get_employee_data($a_filter_data);
			if ($mbo_employee_lists) {
				foreach ($mbo_employee_lists as $o_employee) {
					$mba_sub_dept = $this->Emm->get_employee_sub_department(['em.employee_id' => $o_employee->employee_id]);
					$o_employee->fullname = $this->Pdm->retrieve_title($o_employee->personal_data_id);
					$o_employee->sub_department = $mba_sub_dept;
				}
			}
			$a_return = array('code' => 0, 'data' => $mbo_employee_lists);
			print json_encode($a_return);
		}
	}
}