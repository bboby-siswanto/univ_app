<?php
class Setting extends App_core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->view('setting');
	}
	
	public function set_environment($s_environment)
	{
		$this->config->set_item('active_environment', $s_environment);
		redirect(site_url('setting'));
	}
}