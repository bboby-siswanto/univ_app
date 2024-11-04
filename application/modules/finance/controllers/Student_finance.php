<?php
class Student_finance extends App_core
{
    public function __construct()
	{
		parent::__construct('finance');
        $this->load->model('finance/Finance_model', 'Fm');
        $this->load->model('finance/Invoice_model', 'Im');
		$this->load->model('finance/Bni_model', 'Bm');
    }

    public function download_files($s_request_id)
    {
        $this->load->library('zip');

        $mbo_student_aid_data = $this->Fm->get_student_aid_list(['sta.request_id' => $s_request_id])[0];
        if ($mbo_student_aid_data) {
            $s_period = $mbo_student_aid_data->aid_period_year.'_'.$mbo_student_aid_data->aid_period_month;
            $s_file_path = APPPATH.'uploads/'.$mbo_student_aid_data->personal_data_id.'/receipt_bill/'.$s_period.'/';
            // print($s_path);
            if (is_dir($s_file_path)) {
                if ($dir = opendir($s_file_path)) {
                    while (($file = readdir($dir)) !== false){
                        if (strlen($file) > 2) {
                            $this->zip->read_file($s_file_path.$file);
                        }
                    }
                }
            }

            $s_folder_name = str_replace(' ', '_', $mbo_student_aid_data->personal_data_name.'-'.$s_period);
            $this->zip->download($s_folder_name.'.zip');
        }
        // $files = scandir($s_file_path);
        // print('<pre>');
        // var_dump($files);
    }

    public function update_request_note()
    {
        if ($this->input->is_ajax_request()) {
            $s_request_id = $this->input->post('request_id');
            $s_request_note = $this->input->post('request_note');

            $this->Fm->save_request_aid([
                'request_note' => $s_request_note
            ], [
                'request_id' => $s_request_id
            ]);

            print json_encode(['code' => 0, 'message' => 'Success']);
        }
    }
    
    public function update_request_amount_approved()
    {
        if ($this->input->is_ajax_request()) {
            $s_request_id = $this->input->post('request_id');
            $s_request_amount_accepted = $this->input->post('request_amount_accepted');

            $this->Fm->save_request_aid([
                'request_amount_accepted' => $s_request_amount_accepted
            ], [
                'request_id' => $s_request_id
            ]);

            print json_encode(['code' => 0, 'message' => 'Success']);
        }
    }

    public function student_aid_list($s_aid_period_id)
    {
        $mbo_aid_period = $this->General->get_where('dt_student_aid_setting', [
            'aid_period_id' => $s_aid_period_id
        ])[0];

        if ($mbo_aid_period) {
            $this->a_page_data['aid_period'] = $mbo_aid_period;
            $this->a_page_data['body'] = $this->load->view('finance/table/student_aid_list', $this->a_page_data, true);
        }else{
            $this->a_page_data['page_error'] = 'Student Aid';
            $this->a_page_data['body'] = $this->load->view('dashboard/student_error', $this->a_page_data, true);
        }

        $this->load->view('layout', $this->a_page_data);
    }
    
    public function aid($s_student_id = false)
    {
        $this->a_page_data['academic_year'] = $this->General->get_where('dt_academic_year');
        $this->a_page_data['a_month'] = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $this->a_page_data['body'] = $this->load->view('finance/student_aid_setting', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function get_student_aid_list($s_aid_period_id = false)
    {
        if ($this->input->is_ajax_request()) {
            $s_aid_period_id = $this->input->post('aid_period_id');

            $mba_student_aid_list = $this->Fm->get_student_aid_list([
                'sta.aid_period_id' => $s_aid_period_id
            ]);

            if ($mba_student_aid_list) {
                foreach ($mba_student_aid_list as $o_student_aid) {
                    $o_student_aid->no_rekening = '"='.$o_student_aid->request_account_number.'"';
                    $o_student_aid->files_data = $this->General->get_where('dt_student_aid_files', [
                        'request_id' => $o_student_aid->request_id
                    ]);
                }
            }

            $a_return = ['code' => 1, 'data' => $mba_student_aid_list];

            print json_encode($a_return);
        }
    }

    public function get_aid_period_list()
    {
        $mba_student_aid_period_list = $this->Fm->get_aid_period_list();
        if ($mba_student_aid_period_list) {
            foreach ($mba_student_aid_period_list as $o_aid_period) {
                $mba_student_aid_requested = $this->General->get_where('dt_student_aid', [
                    'aid_period_id' => $o_aid_period->aid_period_id
                ]);
                $o_aid_period->count_request = ($mba_student_aid_requested) ? count($mba_student_aid_requested) : 0;
            }
        }

        print json_encode(['code' => 0, 'data' => $mba_student_aid_period_list]);
    }

    public function inactiv_period($s_aid_period_id)
    {
        $mba_aid_period = $this->General->get_where('dt_student_aid_setting', ['aid_period_id' => $s_aid_period_id]);

        if ($mba_aid_period) {
            $this->Fm->save_period_student_aid([
                'aid_period_status' => 'inactive'
            ], [
                'aid_period_id' => $s_aid_period_id
            ]);

            $a_return = ['code' => 0, 'message' => 'Success'];
        }else{
            $a_return = ['code' => 1, 'message' => 'Data not found!'];
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);
        }
    }

    public function activate_period($s_aid_period_id)
    {
        $mba_aid_period = $this->General->get_where('dt_student_aid_setting', ['aid_period_id' => $s_aid_period_id]);

        if ($mba_aid_period) {
            $this->Fm->save_period_student_aid([
                'aid_period_status' => 'inactive'
            ], [
                'aid_period_id != ' => $s_aid_period_id
            ]);

            $this->Fm->save_period_student_aid([
                'aid_period_status' => 'active'
            ], [
                'aid_period_id' => $s_aid_period_id
            ]);

            $a_return = ['code' => 0, 'message' => 'Success'];
        }else{
            $a_return = ['code' => 1, 'message' => 'Data not found!'];
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);
        }
    }

    public function submit_period_aid()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('aid_period_year', 'Period on Year', 'required');
            $this->form_validation->set_rules('aid_period_month', 'Period on Month', 'required');
            $this->form_validation->set_rules('aid_period_datetime_start', 'Student Registration Start', 'required');
            $this->form_validation->set_rules('aid_period_datetime_end', ' Student Registration End', 'required');

            if ($this->form_validation->run()) {
                $a_data = [
                    'aid_period_month' => set_value('aid_period_month'),
                    'aid_period_year' => set_value('aid_period_year'),
                    'aid_period_datetime_start' => date('Y-m-d 00:00:00', strtotime(set_value('aid_period_datetime_start'))),
                    'aid_period_datetime_end' => date('Y-m-d 23:59:59', strtotime(set_value('aid_period_datetime_end'))),
                ];

                if ($this->input->post('student_aid_period_id') == '') {
                    $a_data['aid_period_id'] = $this->uuid->v4();
                    $save_data = $this->Fm->save_period_student_aid($a_data);
                }else{
                    $save_data = $this->Fm->save_period_student_aid($a_data, ['aid_period_id' => $this->input->post('student_aid_period_id')]);
                }

                if ($save_data) {
                    $a_return = ['code' => 0, 'message' => 'Success'];
                }else{
                    $a_return = ['code' => 1, 'message' => 'Fail submit period!'];
                }
            }else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }
}
