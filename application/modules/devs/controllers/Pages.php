<?php
class Pages extends App_core
{
    public function __construct()
    {
        parent::__construct('ite');
        $this->load->model('devs/Devs_model', 'Dm');
    }

    public function pages_list()
    {
        $this->a_page_data['body'] = $this->load->view('devs/pages_lists', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function pages_table($s_roles_id = false)
    {
        if ($s_roles_id) {
            $mba_role_data = $this->Dm->get_roles(array('roles_id' => $s_roles_id));
            if ($mba_role_data) {
                $this->a_page_data['roles_id'] = $s_roles_id;
                $this->a_page_data['roles_data'] = $mba_role_data[0];
                $this->load->view('table/pages_table', $this->a_page_data);
            }else{
                show_404();
            }
        }else{
            $this->a_page_data['roles_id'] = false;
            $this->load->view('table/pages_table', $this->a_page_data);
        }
    }

    public function form_input()
    {
        $this->load->view('form/form_input_pages', $this->a_page_data);
    }

    public function get_pages()
    {
        if ($this->input->is_ajax_request()) {
            $s_roles_id = $this->input->post('roles_id');
            if ($s_roles_id !== null) {
                if ($s_roles_id == '') {
                    $a_data = false;
                }else if ($s_roles_id == 'none') {
                    $a_data = $this->Dm->get_roles_pages();
                }else {
                    $a_data = $this->Dm->get_roles_pages(array('drp.roles_id' => $s_roles_id));
                }
            }else{
                $a_data = $this->General->get_where('ref_pages');
            }
            $a_return = array('code' => 0, 'data' => $a_data);

            print json_encode($a_return);
        }
    }

    public function remove_pages()
    {
        if ($this->input->is_ajax_request()) {
            $s_pages_id = $this->input->post('pages_id');
            
            if ($this->Dm->remove_pages($s_pages_id)) {
                $a_return = array('code' => 0, 'message' => 'Success');
            }else {
                $a_return = array('code' => 1, 'message' => 'Error remove data');
            }

            print json_encode($a_return);
        }
    }

    public function save_data()
    {
        if ($this->input->is_ajax_request()) {
            $s_pages_id = $this->input->post('pages_id');
            $s_pages_name = strtolower(str_replace(' ', '_', $this->input->post('pages_name')));
            $s_pages_description = $this->input->post('pages_description');
            $s_pages_top_bar = $this->input->post('pages_top_bar');
            $s_pages_uri = $this->input->post('pages_uri');
            if($s_pages_name == ''){
                $a_return = array('code' => 1, 'message' => 'Pages Name field is required!');
            }else{
                $a_pages_data = array(
                    'pages_name' => $s_pages_name,
                    'pages_description' => $s_pages_description,
                    'pages_top_bar' => $s_pages_top_bar,
                    'pages_uri' => $s_pages_uri
                );

                if ($s_pages_id == '') {
                    $a_pages_data['pages_id'] = $this->uuid->v4();
                    $save = $this->Dm->save_pages($a_pages_data);
                }else{
                    $save = $this->Dm->save_pages($a_pages_data, array('pages_id' => $s_pages_id));
                }
                if ($save) {
                    $a_return = array('code' => 0, 'message' => 'Successs');
                }else{
                    $a_return = array('code' => 1, 'message' => 'Error save pages');
                }
            }

            print json_encode($a_return);
        }
    }
}
