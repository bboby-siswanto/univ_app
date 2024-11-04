<?php
class Reference extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('finance/Finance_model', 'Fm');
	}
	
	public function payment_code()
	{
		$this->a_page_data['body'] = $this->load->view('payment_code/lists', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function get_payment_code()
	{
		if($this->input->is_ajax_request()){
			$mba_payment_type_code = $this->Fm->get_payment_type_code();
			print json_encode(array('code' => 0, 'data' => $mba_payment_type_code));
			exit;
		}
	}
	
	public function save_payment_code()
	{
		if($this->input->is_ajax_request()){
			$this->form_validation->set_rules('payment_type_name', 'Payment type name', 'trim|required');
			if($this->form_validation->run()){
				$a_payment_type_data = array(
					'payment_type_name' => set_value('payment_type_name')
				);
				
				if($s_payment_type_code = $this->input->post('payment_type_code')){
					$this->Fm->update_payment_type_code($a_payment_type_data, $s_payment_type_code);
				}
				else{
					$this->Fm->insert_payment_type_code($a_payment_type_data);
				}
				
				$rtn = array('code' => 0, 'message' => 'Success!');
			}
			else{
				$rtn = array('code' => 1,  'message' => validation_errors('<span>','</span><br>'));
			}
			print json_encode($rtn);
			exit;
		}
	}
}