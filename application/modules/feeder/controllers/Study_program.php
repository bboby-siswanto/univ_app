<?php
class Study_program extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('FeederAPI', ['mode' => 'production']);
		$this->load->model('study_program/Study_Program_Model', 'Spm');
	}

	public function test()
	{
		$feeder_institution_same = $this->feederapi->post('GetAllPT', [
			'filter' => "nama_perguruan_tinggi LIKE '%UNIVERSITY OF LEEDS%'"
		]);
		print('<pre>');var_dump($feeder_institution_same);exit;
	}

	public function sync_institution_feeder()
	{
		print('closed!');exit;
		$this->load->model('institution/Institution_model', 'Inm');
		$o_get_pt = $this->feederapi->post('GetAllPT');
		$a_data_pt = $o_get_pt->data;
		$a_institution_id_exists = [];
		$a_institution_name_exists = [];
		$a_institution_push = [];

		foreach ($a_data_pt as $o_pt) {
			// print($o_pt->kode_perguruan_tinggi);exit;
			// print('<pre>');var_dump($o_pt);exit;
			$mba_instition_id_data = $this->General->get_where('ref_institution', ['institution_id' => $o_pt->id_perguruan_tinggi]);
			$mba_instition_name_data = $this->General->get_where('ref_institution', ['institution_name' => $o_pt->nama_perguruan_tinggi]);

			if ($mba_instition_id_data) {
				// if (!in_array($o_pt->id_perguruan_tinggi, $a_institution_id_exists)) {
				// 	array_push($a_institution_id_exists, $o_pt->id_perguruan_tinggi);
				// }
			}
			else if ($mba_instition_name_data) {
				// if (!in_array($o_pt->nama_perguruan_tinggi, $a_institution_name_exists)) {
				// 	array_push($a_institution_name_exists, $o_pt->nama_perguruan_tinggi);
				// }
				$feeder_institution_same = $this->feederapi->post('GetAllPT', [
					'filter' => "nama_perguruan_tinggi LIKE '%$o_pt->nama_perguruan_tinggi%'"
				]);

				$a_data_result = $feeder_institution_same->data;
				if (count($a_data_result) == 1) {
					if (!in_array($o_pt->nama_perguruan_tinggi, $a_institution_push)) {
						array_push($a_institution_push, $o_pt->nama_perguruan_tinggi);
					}
				}
				// print('<pre>');var_dump(count($a_data_result));exit;
			}
			// else {
			// 	$a_institution_data = [
			// 		'institution_id' => $o_pt->id_perguruan_tinggi,
			// 		'institution_code' => $o_pt->kode_perguruan_tinggi,
			// 		'institution_name' => $o_pt->nama_perguruan_tinggi,
			// 		'date_added' => date('Y-m-d H:i:s')
			// 	];
			// 	$this->Inm->insert_institution($a_institution_data);
			// 	print('tambah '.$o_pt->nama_perguruan_tinggi);
			// 	print('<br>');
			// }
		}

		print('<pre>');
		// print('<h3>Institution ID Exists:');var_dump($a_institution_id_exists);
		// print('<h3>Institution Name Exists:');var_dump($a_institution_name_exists);
		print('<h3>Institution Push Exists:');var_dump($a_institution_push);
	}
	
	public function sync_study_program()
	{
		$o_get_prodi = $this->feederapi->post('GetProdi');
		
		if($o_get_prodi->error_code == 0){
			$a_prodi = $o_get_prodi->data;
			
			if(count($a_prodi) >= 1){
				foreach($a_prodi as $o_prodi){
					$a_study_program_data = array(
						'study_program_name_feeder' => $o_prodi->nama_program_studi,
						'date_added' => date('Y-m-d H:i:s', time())
					);
					
					if($mba_db_study_program_data = $this->Spm->get_study_program($o_prodi->id_prodi)){
						$i_study_program_id = $mba_db_study_program_data[0]->study_program_id;
						$this->Spm->update_study_program($a_study_program_data, $i_study_program_id);
					}
					else{
						$a_study_program_data['study_program_id'] = $o_prodi->id_prodi;
						$this->Spm->insert_study_program($a_study_program_data);
					}
				}
			}
		}
	}
}