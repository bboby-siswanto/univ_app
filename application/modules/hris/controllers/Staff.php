<?php
class Staff extends App_core
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function general_information($s_personal_data_id = false)
    {
        if (!$s_personal_data_id) {
            show_404();
        }
        $this->load->model('employee/Employee_model', 'Emm');
        $mba_employee_data = $this->Emm->get_employee_data([
            'em.personal_data_id' => $s_personal_data_id
        ]);
        
        // print('<pre>');var_dump($mba_employee_data);exit;
        if (!$mba_employee_data) {
            show_403();
        }

        $this->a_page_data['userdata'] = $mba_employee_data[0];
        // print('<pre>');var_dump($mba_employee_data[0]);exit;
        $this->a_page_data['user_name'] = $this->General->retrieve_title($s_personal_data_id);
        $this->a_page_data['employee_type'] = $this->_get_type($mba_employee_data[0]->employee_email);
        $this->a_page_data['url_img'] = base_url().'file_manager/view/0bde3152-5442-467a-b080-3bb0088f6bac/'.$s_personal_data_id;
        $this->load->view('misc/publish_staff', $this->a_page_data);
        // print('show');
    }
    
    private function _get_type($s_email)
	{
		$a_email_components = explode('@', $s_email);
		
		$s_username = $a_email_components[0];
		$s_domain = $a_email_components[1];
		$a_domain_components = explode('.', $s_domain);
		
		switch($a_domain_components[0])
		{
			case "iuli":
				$s_dc = 'staff';
				$s_type = 'staff';
				break;
				
			case "student":

			case "stud":
				$s_dc = 'student';
				$s_type = 'student';
				break;

			case "alumni":
				$s_dc = 'alumni';
				$s_type = 'alumni';
				break;
				
			case "lect":
				$s_dc = 'lect';
				$s_type = 'lecturer';
				break;
				
			default:
				$s_type = false;
		}
		
		return $s_type;
	}
}
