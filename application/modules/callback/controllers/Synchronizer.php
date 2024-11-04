<?php
class Synchronizer extends Api_core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function retrieve_sync()
	{
		$a_sync_data = $this->a_api_data;
		
		$a_return = array();
		for($i = 0; $i < count($a_sync_data); $i++){
			$a_ref_data = $a_sync_data[$i];
			
			for($j = 0; $j < count($a_ref_data['batch_data']); $j++){
				$a_clause = array();
				
				$s_table_name = $a_ref_data['table'];
				$a_primary_key = $a_ref_data['primary_key'];
				$a_batch_data = $a_ref_data['batch_data'];
				
				for($k = 0; $k < count($a_batch_data); $k++){
					for($l = 0; $l < count($a_ref_data['primary_key']); $l++){
						$a_clause[$a_ref_data['primary_key'][$l]] = $a_ref_data['batch_data'][$k][$a_ref_data['primary_key'][$l]];
					}
					$query = $this->db->get_where($s_table_name, $a_clause);
					
					if($query->num_rows() == 1){
						if(array_key_exists('portal_sync', $a_batch_data[$k])){
							unset($a_batch_data[$k]['portal_sync']);
						}
						$this->db->update($s_table_name, $a_batch_data[$k], $a_clause);
					}
					else{
						if(array_key_exists('portal_sync', $a_batch_data[$k])){
							unset($a_batch_data[$k]['portal_sync']);
						}
						$a_batch_data[$k]['pmb_sync'] = 0;
						$this->db->insert($s_table_name, $a_batch_data[$k]);
					}
					
					var_dump($this->db->last_query());
				}
			}
		}
	}
}