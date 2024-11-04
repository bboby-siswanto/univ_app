<?php
class Iuli_info extends App_core
{
    function __construct()
    {
        $s_user = ($this->session->userdata('type') == 'staff') ? 'staff_alumni' : 'student_alumni';
        parent::__construct($s_user);
        $this->load->model('Info_model', 'Ifm');
    }

    public function info_lists()
    {
        $this->a_page_data['body'] = $this->load->view('iuli_info/iuli_info', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function form_list_info()
    {
        $this->a_page_data['o_info_data'] = $this->Ifm->get_info_lists(array('info_status' => 'publish'));
        $this->load->view('iuli_info/table/lists_info', $this->a_page_data); 
    }

    public function my_info()
    {
        $s_btn_html = Modules::run('layout/generate_buttons', 'profile', 'my_info');
        $this->a_page_data['btn_html'] = $s_btn_html;
        $this->a_page_data['body'] = $this->load->view('iuli_info/table/lists_info_table', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function form_input_info()
    {
        $this->load->view('iuli_info/form/form_input_info', $this->a_page_data);
    }

    public function get_data_filtered()
    {
        if ($this->input->is_ajax_request()) {
            $mbo_info_data = $this->Ifm->get_info_lists();
            print json_encode(array('code' => 0, 'data' => $mbo_info_data));
        }
    }

    public function save_info()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('info_title', 'Title', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('info_message', 'Message', 'required|trim|max_length[500]');
            $s_published = ($this->input->post('publish') === null) ? 'draft' : 'publish';
            $s_info_id = $this->input->post('info_id');

            if ($this->form_validation->run()) {
                $a_info_data = array(
                    'info_title' => set_value('info_title'),
                    'info_message' => set_value('info_message'),
                    'info_status' => $s_published
                );


                if ($s_info_id == '') {
                    $a_info_data['info_id'] = $this->uuid->v4();
                    $a_info_data['personal_data_id'] = $this->session->userdata('user');
                    $save_info = $this->Ifm->save_info($a_info_data);
                }else{
                    $save_info = $this->Ifm->save_info($a_info_data, $s_info_id);
                }

                if ($save_info) {
                    $a_rtn = array('code' => 0, 'message' => 'Success');
                }else{
                    $a_rtn = array('code' => 1, 'message' => 'Error saving info');
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_rtn);exit;
        }
    }

    public function prop_status()
    {
        if ($this->input->is_ajax_request()) {
            $s_info_id = $this->input->post('info_id');
            $s_info_status = $this->input->post('status');

            $a_info_data = array(
                'info_status' => ($s_info_status == 'draft') ? 'publish' : 'draft'
            );

            if ($this->Ifm->save_info($a_info_data, $s_info_id)) {
                $a_rtn = array('code' => 0, 'message' => 'Success');
            }else{
                $a_rtn = array('code' => 1, 'message' => 'Error saving info');
            }

            print json_encode($a_rtn);exit;
        }
    }
}
