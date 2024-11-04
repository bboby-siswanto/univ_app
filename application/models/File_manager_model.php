<?php
class File_manager_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_files($s_personal_data_id, $s_document_id)
	{
		$this->db->select("*");
		$this->db->from('dt_personal_data_document dpdd');
		$this->db->join('ref_document rd', 'rd.document_id = dpdd.document_id');
		$this->db->join('dt_personal_data dpd', 'personal_data_id');
		$this->db->where('dpdd.personal_data_id', $s_personal_data_id);
		$this->db->where('dpdd.document_id', $s_document_id);
		$query = $this->db->get();
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	
	public function upload_file($a_file_data)
	{
		$a_existing_files = $this->get_files($a_file_data['personal_data_id'], $a_file_data['document_id']);
		
		if(($a_existing_files) AND (count($a_file_data) >= 1)){
			$s_file_path = APPPATH.'uploads/'.$a_file_data['personal_data_id'].'/'.$a_existing_files[0]->document_requirement_link;
			
			if(file_exists($s_file_path)){
				unlink($s_file_path);
			}
			
			$this->db->update('dt_personal_data_document', $a_file_data, array(
				'personal_data_id' => $a_file_data['personal_data_id'],
				'document_id' => $a_file_data['document_id']
			));
		}
		else{
			$a_file_data['date_added'] = date('Y-m-d H:i:s', time());
			$this->db->insert('dt_personal_data_document', $a_file_data);
		}
	}
}