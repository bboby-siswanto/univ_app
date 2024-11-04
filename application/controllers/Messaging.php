<?php
class Messaging extends App_core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function compose_email_form()
	{
		$this->load->view('form/compose_email_form', $this->a_page_data);
	}
}