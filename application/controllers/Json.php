<?php
class Json extends App_core
{
	public function __construct()
	{
		parent::__construct();
		header('Content-Type: application/json');
	}
	
	public function dikti_wilayah()
	{
		$s_term = false;
		if($this->input->is_ajax_request()){
			$s_term = $this->input->post('term');
		}
		
		$a_dikti_wilayah = $this->General->get_dikti_wilayah($s_term);
		print json_encode($a_dikti_wilayah);
		exit;
	}
	
	public function religion()
	{	
		$a_religion_data = $this->General->get_religion();
		print json_encode($a_religion_data);
		exit;
	}
	
	public function country()
	{
		$s_term = false;
		if($this->input->is_ajax_request()){
			$s_term = $this->input->post('term');
		}
		
		$a_country_data = $this->General->get_country($s_term);
		print json_encode($a_country_data);
		exit;
	}
}