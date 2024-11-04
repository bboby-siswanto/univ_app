<?php
class Api extends Api_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('employee/Employee_model', 'EmM');
		$this->load->library('FeederAPI');
	}
	
	public function sync_feeder_data()
	{
		print "<pre>";
		$mba_list_dosen = $this->feederapi->post('GetListDosen');
		if($mba_list_dosen->error_code == '0'){
			foreach($mba_list_dosen->data as $lecturer){
				$mbo_emp_data = $this->EmM->get_employee_data([
					'employee_id' => $lecturer->id_dosen
				]);
				
				$update = false;
				if(!$mbo_emp_data){
					$mbo_emp_data = $this->EmM->get_employee_data([
						'employee_lecturer_number' => $lecturer->nidn
					]);
					
					if($mbo_emp_data){
						$update = true;
					}
					else{
						print implode(' - ', [$lecturer->nama_dosen, $lecturer->nidn])."\n";
					}
				}
				else{
					$update = true;
				}
				
				if($update){
					$x = $this->EmM->save_employee([
						'employee_lecturer_number' => $lecturer->nidn,
						'employee_id' => $lecturer->id_dosen,
						'employee_lecturer_is_reported' => 'TRUE'
					], $mbo_emp_data[0]->employee_id);
				}
			}
		}
	}
}