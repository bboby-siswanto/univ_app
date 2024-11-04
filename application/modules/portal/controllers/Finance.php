<?php
class Finance extends App_core
{
    function __construct()
    {
        parent::__construct();
        $s_environment = 'production';
		if($this->session->userdata('auth')){
			$s_environment = $this->session->userdata('environment');
		}
		$this->db = $this->load->database($s_environment, true);

		switch ($s_environment) {
			case 'production':
				$this->load->model('Admission_model', 'Sm');
				break;

			case 'sanbox':
				$this->load->model('Staging_model', 'Sm');
				break;
			
			default:
				$this->load->model('Staging_model', 'Sm');
				break;
        }
        
        $this->load->model('Portal_model', 'Pm');
        
        $this->load->model('employee/Employee_model','Emm');
        $this->load->model('personal_data/Personal_data_model','Pdm');
    }

    public function get_invoice($student_id, $semester_id, $payment_type)
    {
        $mbo_invoice_data = $this->Pm->retrieve_data('invoice', [
            'student_id' => $student_id,
            'semester_id' => $semester_id,
            'payment_type_id' => $payment_type
        ])[0];
        // $mbo_invoice_data = $this->Pm->retrieve_data('invoice', [
        //     'student_id' => $student_id
        // ]);

        return $mbo_invoice_data;
        // print('<pre>');
        // var_dump($mbo_invoice_data);
    }
}
