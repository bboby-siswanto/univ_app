<?php
class Layout extends MX_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->view('layout');
	}

	public function generate_buttons($mbs_sub_module = false)
	{
		$s_module_name = $this->session->userdata('module');
		
		$this->config->load('button_config');
		$a_module_config = $this->config->item('module');
		$a_btn_config = ($mbs_sub_module) ? $a_module_config[$s_module_name][$mbs_sub_module] : $a_module_config[$s_module_name];
		
		$s_btn_html = '';
		foreach($a_btn_config as $key => $value){
			switch($value['type'])
			{
				case "link":
					$s_param = '';
					if(isset($value['include_params'])){
						$s_param = '\'+row[\'personal_data_id\']+\'';
					}
					$s_btn_html .= anchor(site_url($value['target']).$s_param, $value['properties']['content'], array('class' => $value['properties']['class']));
					break;
					
				case "modal":
					$s_btn_html .= form_button($value['properties']);
					break;
					
				case "action":
					break;
			}
		}
		
		return 'asdf';
	}
}