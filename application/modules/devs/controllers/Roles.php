<?php
class Roles extends App_core
{
    public function __construct()
    {
        parent::__construct('ite');
        $this->load->model('devs/Devs_model', 'Dm');
    }

    public function roles_list()
    {
        $this->a_page_data['body'] = $this->load->view('roles_lists', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function roles_table()
    {
        $this->load->view('table/roles_table', $this->a_page_data);
    }

    public function pages_list($s_roles_id)
    {
        $mba_role_data = $this->Dm->get_roles(array('roles_id' => $s_roles_id));
        if ($mba_role_data) {
            $mba_role_pages_data = $this->Dm->get_roles_pages(array('drp.roles_id' => $s_roles_id));
            $this->a_page_data['role_pages_data'] = $mba_role_pages_data;
            $this->a_page_data['roles_id'] = $s_roles_id;
            $this->a_page_data['roles_data'] = $mba_role_data[0];
            $this->a_page_data['body'] = $this->load->view('roles_pages', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }else {
            show_404();
        }
    }

    public function pages_lists_table($s_roles_id)
    {
        $mba_role_data = $this->Dm->get_roles(array('roles_id' => $s_roles_id));
        if ($mba_role_data) {
            $this->a_page_data['roles_id'] = $s_roles_id;
            $this->a_page_data['roles_data'] = $mba_role_data[0];
            $this->load->view('table/role_pages_table', $this->a_page_data);
        }
    }

    public function form_input()
    {
        $this->load->view('form/form_input_roles', $this->a_page_data);
    }

    public function save_roles_pages()
    {
        if ($this->input->is_ajax_request()) {
            $s_roles_id = $this->input->post('roles_id');
            $mba_pages_data = $this->input->post('data');

            $this->db->trans_start();
            if (($mba_pages_data) AND ($mba_pages_data !== NULL)) {
                foreach ($mba_pages_data as $pages) {
                    $roles_pages_available = $this->Dm->get_roles_pages(array(
                        'drp.roles_id' => $s_roles_id,
                        'drp.pages_id' => $pages['pages_id']
                    ));
                    if (!$roles_pages_available) {
                        $a_roles_pages_data = array(
                            'roles_pages_id' => $this->uuid->v4(),
                            'roles_id' => $s_roles_id,
                            'pages_id' => $pages['pages_id']
                        );
                        $this->Dm->save_roles_pages($a_roles_pages_data);
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $a_return = array('code' => 1, 'message' => 'Failed save data!');
            }else{
                $this->db->trans_commit();
                $a_return = array('code' => 0, 'message' => 'Success');
            }

            print json_encode($a_return);
        }
    }

    // public function save_role_pages()
    // {
    //     if ($this->input->is_ajax_request()) {
    //         $s_roles_id = $this->input->post('roles_id');
    //         $mba_pages_data = $this->input->post('data');
    //         $role_pages = false;
    //         if (($mba_pages_data) AND ($mba_pages_data !== NULL)) {
    //             $role_pages = array();
    //             foreach ($mba_pages_data as $pages) {
    //                 array_push($role_pages, array(
    //                     'roles_pages_id' => $this->uuid->v4(),
    //                     'roles_id' => $s_roles_id,
    //                     'pages_id' => $pages['pages_id']
    //                 ));
    //             }

    //             $save_data = $this->Dm->save_roles_pages($s_roles_id, $role_pages);
    //         }else{
    //             $save_data = $this->Dm->save_roles_pages($s_roles_id);
    //         }

    //         if ($save_data) {
    //             $a_return = array('code' => 0, 'message' => 'success');
    //         }else {
    //             $a_return = array('code' => 1, 'message' => 'Fail  process  data');
    //         }

    //         print json_encode($a_return);
    //     }
    // }

    public function remove_roles_pages()
    {
        if ($this->input->is_ajax_request()) {
            $s_roles_pages_id = $this->input->post('roles_pages_id');
            if ($this->Dm->remove_roles_pages($s_roles_pages_id)) {
                $a_return = array('code' => 0, 'message' => 'Success');
            }else{
                $a_return = array('code' => 0, 'message' => 'Error remove pages');
            }

            print json_encode($a_return);
        }
    }

    public function get_roles()
    {
        if ($this->input->is_ajax_request()) {
            $a_data = $this->General->get_where('ref_roles');
            $a_return = array('code' => 0, 'data' => $a_data);

            print json_encode($a_return);
        }
    }

    public function remove_roles()
    {
        if ($this->input->is_ajax_request()) {
            $s_roles_id = $this->input->post('roles_id');
            
            if ($this->Dm->remove_roles($s_roles_id)) {
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
            $s_roles_id = $this->input->post('roles_id');
            $s_roles_name = $this->input->post('roles_name');
            if($s_roles_name == ''){
                $a_return = array('code' => 1, 'message' => 'Role Name field is required!');
            }else{
                $a_role_data = array(
                    'roles_name' => $s_roles_name
                );

                if ($s_roles_id == '') {
                    $save = $this->Dm->save_roles($a_role_data);
                }else{
                    $save = $this->Dm->save_roles($a_role_data, array('roles_id' => $s_roles_id));
                }
                if ($save) {
                    $a_return = array('code' => 0, 'message' => 'Successs');
                }else{
                    $a_return = array('code' => 1, 'message' => 'Error save role');
                }
            }

            print json_encode($a_return);
        }
    }
}
