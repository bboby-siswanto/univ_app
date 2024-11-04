<?php
class Arms extends App_core
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->a_page_data['body'] = $this->load->view('arms_layout.php', $this->a_page_data, true);
        $this->load->view('template_layout', $this->a_page_data);
    }
}
