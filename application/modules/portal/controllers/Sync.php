<?php
class Sync extends App_core
{
    public function __construct()
    {
        parent::__construct();
        $s_environment = 'production';
		if($this->session->userdata('auth')){
			$s_environment = $this->session->userdata('environment');
        }
        
        $this->load->model('Portal_model', 'Pm');
    }

    public function pmb_sync_document()
    {
        $ch = curl_init("");
        $fp = fopen(APPPATH.'uploads/templates/_logs.txt', "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        if(curl_error($ch)) {
            fwrite($fp, curl_error($ch));
        }
        curl_close($ch);
        fclose($fp);
    }

    // public function portal_sync()
    // {
    //     $this->a_page_data['body'] = $this->load->view('syncronize', $this->a_page_data, true);
    //     $this->load->view('layout', $this->a_page_data);
    // }
}
