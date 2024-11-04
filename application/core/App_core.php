<?php
define('STUDENTPATH', APPPATH.'uploads/student/');
class App_core extends MX_Controller
{
	public $a_page_data = array();
	public $a_user_roles, $student_personal_id_dummy;
	public $s_module_name;
	public $a_portal_config;
	public $a_allowed_page;
	public $a_programs;
	public $a_list_email;
	public $s_environment = 'production';
	public $s_apitoken = '';
	public $s_apiuid = '';
	public $fdb;
	
	public function __construct($s_module_name = false)
	{
		parent::__construct();

		$this->db = $this->load->database('production', true);
		
		$this->load->library('form_validation');
		$this->form_validation->CI =& $this;
		
		$this->log_activity();
		$this->set_user_roles();
		$b_reject_unauthorized = true;
		
		$a_unauthorized_module = array('auth', 'callback', 'portal','public');
		$a_unauthorized_class = array('auth', 'api', 'api_sync', 'cron', 'sponsor', 'text_template');
		$a_unauthorized_method = array('login', 'view', 'recaps', 'get_invoice_recap', 'public', 'event_check_in', 'view_public','view_doc','general_information', 'get_question_list', 'create_student_email', 'get_minimum_payment');
		
		$this->config->load('portal_apps_config');
		$this->config->load('portal_config_production');
		$this->a_portal_config = $this->config->item('portal_menu');
		$this->a_allowed_page = $this->config->item('allowed_page');
		$this->a_programs = $this->config->item('program_data_id');
		$this->a_list_email = $this->config->item('email');
		$this->student_personal_id_dummy = $this->config->item('personal_id_student_dummy');
		
		if($s_module_name){
			$this->s_module_name = $s_module_name;
		}
		else{
			$this->s_module_name = $this->session->userdata('module');
		}
		
		if(in_array($this->router->fetch_method(), $a_unauthorized_method)){
			$b_reject_unauthorized = false;
		}
		else{
			if(in_array($this->router->fetch_class(), $a_unauthorized_class)){
				$b_reject_unauthorized = false;
			}
			else{
				if(in_array($this->router->fetch_module(), $a_unauthorized_module)){
					$b_reject_unauthorized = false;
				}
			}
		}
		
/*
		if(in_array($this->router->fetch_class(), $a_unauthorized_class)){
			$b_reject_unauthorized = false;
		}
		else{
			if(in_array($this->router->fetch_module(), $a_unauthorized_module)){
				if(in_array($this->router->fetch_method(), $a_unauthorized_method)){
					$b_reject_unauthorized = false;
				}
			}
		}
*/
		
		if($b_reject_unauthorized){
			if (!$this->session->userdata('bypass_everything')) {
				$this->protect_app();
			}
			// $this->a_page_data['top_bar'] = $this->a_portal_config['top_bar'];
			$this->a_page_data['top_bar'] = $this->get_topbar();;
			if (isset($this->a_portal_config['top_bar'][$this->s_module_name]['side_bar'])) {
				$this->a_page_data['side_bar'] = $this->a_portal_config['top_bar'][$this->s_module_name]['side_bar'];
			}else{
				$this->a_page_data['side_bar'] = array();
			}
		}		
	}

	public function get_topbar()
	{
		$a_top_menu = $this->a_portal_config['top_bar'];
		// print('<pre>');var_dump($a_top_menu);exit;
		// print('<pre>');var_dump($this->session->userdata());exit;
		if ((!empty($a_top_menu)) AND (count($a_top_menu) > 0)) {
			foreach ($a_top_menu as $s_key_topbar => $a_topbar) {
				// print('<pre>');var_dump($a_topbar);exit;
				if (!$a_topbar['show']) {
					unset($a_top_menu[$s_key_topbar]);
				}
				else if (!in_array($this->session->userdata('type'), $a_topbar['allowed_user_type'])) {
					unset($a_top_menu[$s_key_topbar]);
				}
				else if ((isset($a_topbar['allowed_personal_data_id'])) AND (!in_array($this->session->userdata('user'), $a_topbar['allowed_personal_data_id']))) {
					unset($a_top_menu[$s_key_topbar]);
				}
			}
		}
		// print('<pre>');var_dump($a_top_menu);exit;
		return $a_top_menu;
	}

	function validation_errors($prefix = '', $suffix = '')
	{
		// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
			if (FALSE === ($OBJ =& _get_validation_object()))
			{
				return '';
			}
	
			return $OBJ->error_array();
		// }
		// else {
		// 	return false;
		// }
	}

	public function set_module($s_module_name)
	{
		$this->s_module_name = $s_module_name;
	}
	
	private function protect_app()
	{
		if(!$this->session->userdata('auth')){
			$this->Activities->log_activity('User not authenticated');
			if($this->uri->segment(1, false)){
				$aSessionData = array(
					'url' => "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"
				);
			}
			else{
				$aSessionData = array(
					'url' => site_url('user/profile')
				);
			}
			$this->session->set_userdata($aSessionData);
			// end

			if ($this->input->is_ajax_request()) {
				$a_return = ['code' => 1, 'message' => 'Your session has been destroyed! Please reload browser and login again!'];
				print json_encode($a_return);exit;
			}
			
			// redirect user to login page
			// redirect($this->app_url.'auth');
			redirect(site_url('auth/login'));
		}
	}

	public function set_user_roles()
	{
		$this->load->model('devs/Devs_model', 'Dem');
		$a_roles = $this->Dem->get_employee_list_roles(array(
            'em.personal_data_id' => $this->session->userdata('user')
        ));
        $this->a_user_roles = $a_roles;
	}
	
	public function log_activity($details = null)
	{
		$this->load->model('Activities_model', 'Activities');
		$this->Activities->log_activity($details);
	}
	
	public function load_sidebar($s_parent_module)
	{
		switch($s_parent_module)
		{
			
		}
	}
}