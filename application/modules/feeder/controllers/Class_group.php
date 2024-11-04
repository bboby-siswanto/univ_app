<?php
class Class_group extends App_core
{
    public function __construct()
	{
		parent::__construct('academic');
		$this->load->model('student/Student_model', 'Stm');
		$this->load->model('academic/Academic_year_model', 'Aym');
		$this->load->model('academic/Class_group_model', 'Cgm');
		$this->load->model('academic/Curriculum_model', 'Cm');
		$this->load->model('academic/Semester_model', 'Sm');
		$this->load->model('academic/Subject_model', 'Sbj');
		$this->load->model('academic/Score_model', 'Scm');
		$this->load->model('feeder/Feeder_model', 'FeM');
		$this->load->model('employee/Employee_model', 'EeM');
		$this->load->library('FeederAPI', ['mode' => 'production']);
		// $this->load->library('FeederAPI', ['mode' => 'sandbox']);
		
		// var_dump($this->session->userdata('environment'));
    }
    
	function sync_report($s_academic_year_id, $s_semester_type_id) {
		$s_dikttiperiode = $s_academic_year_id.$s_semester_type_id;
		$a_mahasiswa_feeder = $this->feederapi->post('GetListMahasiswa', [
			'filter' => "id_periode IN ('".$s_academic_year_id."1', '".$s_academic_year_id."2')"
		]);

		$data_mahasiswa = [];
		$data_feeder = $a_mahasiswa_feeder->data;
		
		$mba_portal_data = $this->Sm->get_semester_student_personal_data([
			'st.academic_year_id >= ' => $s_academic_year_id,
			'dss.academic_year_id' => $s_academic_year_id,
			'dss.semester_type_id' => $s_semester_type_id,
			'dss.student_semester_status' => 'active'
		]);
		$count_data_portal = ($mba_portal_data) ? count($mba_portal_data) : 0;
		$count_data_feeder = count($data_feeder);

		// if ($mba_portal_data) {
		// 	foreach ($mba_portal_data as $o_portal) {
		// 		foreach ($data_feeder as $o_feeder) {
		// 			if ($o_portal->student_id == $o_feeder->id_registrasi_mahasiswa) {
		// 				if (!in_ar) {
		// 					# code...
		// 				}
		// 			}
		// 		}
		// 	}
		// }
		// else {
		// 	$data_mahasiswa = $data_feeder;
		// }

		print('<pre>');var_dump($data_mahasiswa);exit;

		// if ($count_data_feeder > $count_data_portal) {
		// 	print('feeder menang');
		// }
		// else {
		// 	print('portal menang');
		// }
		// if ($mba_portal_data) {
		// 	foreach ($mba_portal_data as $key => $value) {
		// 		# code...
		// 	}
		// }

		$this->a_page_data['data_list'] = $mba_portal_data;
		$this->a_page_data['body'] = $this->load->view('reportsync', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
		// print('<pre>');var_dump($mba_portal_data);exit;
	}
}