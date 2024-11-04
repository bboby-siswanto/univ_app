<?php
class Devs_employee extends App_core
{
    function __construct()
    {
        parent::__construct('ite');
        $this->load->model('devs/Devs_model', 'Dm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('employee/Employee_model', 'Emm');
        $this->load->model('student/Student_model', 'Stm');
    }

    public function generate_pass($b_return = false)
    {
        $length = 8;
        // $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        if ($b_return) {
            return $randomString;exit;
        }
        var_dump($randomString);exit;
    }

    public function restore_session()
    {
        // $s_prev_link = $_SERVER['HTTP_REFERER'];
        if ($this->session->has_userdata('session_old')) {
            $a_old_session = $this->session->userdata('session_old');
            // print('<pre>');var_dump($a_old_session);exit;
            $a_destroy_session = ['session_old','employee_id','student_id', 'dikti_required', 'student_submit_thesis', 'vaccine_covid', 'show_vote_modal', 'allowed_proposed_thesis'];
            $this->session->unset_userdata($a_destroy_session);

            $this->session->set_userdata($a_old_session);
        }
        // redirect($s_prev_link);
        redirect('devs/devs_employee/employee_list');
    }

    public function change_session($s_data_id, $s_target)
    {
        $s_prev_link = $_SERVER['HTTP_REFERER'];
        // print('<pre>');
        // var_dump($_SESSION);

        $s_uid = '';
        $mbo_data_full = false;
        switch ($s_target) {
            case 'employee':
                $mba_employee_data = $this->Emm->get_employee_data(['em.employee_id' => $s_data_id]);
                if ($mba_employee_data) {
                    $s_uid = $mba_employee_data[0]->employee_email;
                    $mbo_data_full = $mba_employee_data[0];
                }
                break;

            case 'student':
                $this->load->model('student/Student_model', 'Stm');
                $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_data_id]);
                if ($mba_student_data) {
                    $s_uid = $mba_student_data[0]->student_email;
                    $mbo_data_full = $mba_student_data[0];
                }
                break;
            
            default:
                break;
        }

        if ((!empty($s_uid)) AND ($mbo_data_full)) {
            $this->load->library('IULI_Ldap');
			$mba_ldap_data = $this->iuli_ldap->uid_search($s_uid);
            if ($mba_ldap_data) {
                $a_type = $this->iuli_ldap->get_components($s_uid);
                $s_type = $a_type['type'];

                $a_backup_session = [
                    'type' => $this->session->userdata('type'),
                    'employee_id' => $this->session->userdata('employee_id'),
                    'user' => $this->session->userdata('user'),
                    'name' => $this->session->userdata('name'),
                ];
                $a_session_data = false;

                if ($s_target == 'employee') {
                    $a_session_data = [
                        'type' => $s_type,
                        'employee_id' => $mbo_data_full->employee_id,
                        'user' => $mbo_data_full->personal_data_id,
                        'name_as' => $mbo_data_full->personal_data_name
                    ];
                }
                else if ($_target == 'student') {
                    $a_session_data = [
                        'type' => $s_type,
                        'student_id' => $mbo_data_full->student_id,
                        'user' => $mbo_data_full->personal_data_id,
                        'name_as' => $mbo_data_full->personal_data_name
                    ];

                    $mba_approve_semester = $this->General->get_thesis_subject($mbo_profile_data->student_id);
                    $mba_student_get_thesis_subject = $this->Scm->get_score_like_subject_name([
                        'sc.student_id' => $mbo_profile_data->student_id
                    ], 'thesis');

                    $a_custom_session = [
                        'dikti_required' => true,
                        'student_submit_thesis' => ($mba_student_get_thesis_subject) ? true : false,
                        'vaccine_covid' => true,
                        'show_vote_modal' => false,
                        'allowed_proposed_thesis' => $mba_approve_semester,
                    ];
                    $a_session_data = array_merge($a_session_data, $a_custom_session);
                    $this->session->unset_userdata('employee_id');
                }
                
                if ($a_session_data) {
                    $a_session_data['session_old'] = $a_backup_session;
                    $this->session->set_userdata($a_session_data);

                    // redirect(base_url());
                    redirect('module/set/gsr');
                }
            }
        }
        redirect($s_prev_link);
    }

    public function test_get($s_personal_data_id = false)
    {
        if (!$s_personal_data_id) {
            $s_personal_data_id = $this->session->userdata('user');
        }

        // var_dump($this->session->userdata('user'));
        // $mbo_employee_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $s_personal_data_id, 'em.status !=' => 'RESIGN'))[0];
        // $mba_permission = $this->employee_permission($s_personal_data_id);
        // $mba_permission_pages = $this->Dm->get_employee_pages(array('ep.employee_id' => $mbo_employee_data->employee_id));
        print('<pre>');
        // var_dump($mba_permission);
        // exit;
        
        var_dump($this->a_page_data);exit;
        // $s_personal_data_id = $this->session->userdata('user');
        // $mbo_employee_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $s_personal_data_id, 'em.status != ' => 'RESIGN'))[0];
        // if ($mbo_employee_data) {
        //     $mba_employee_roles = $this->Dm->get_employee_roles(array('em.employee_id' => $mbo_employee_data->employee_id));
        //     print('<pre>');
        //     var_dump($mba_employee_roles);
        //     // var_dump(modules::run('devs/devs_employee/employee_permission'));
        // }
    }

    public function set_password($s_pass)
    {
        $s_pass_b = '';
        $s_pass_a = password_hash($s_pass, PASSWORD_DEFAULT);
        $a_pass = password_verify($s_pass, $s_pass_b);
        var_dump($s_pass_a);
    }

    public function employee_permission($s_personal_data_id = false)
    {
        $this->load->model('thesis/Thesis_model', 'Tm');
        $this->load->model('academic/Ofse_model', 'Ofm');
        if (!$s_personal_data_id) {
            $s_personal_data_id = $this->session->userdata('user');
        }
        
        $mbo_employee_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $s_personal_data_id, 'em.status !=' => 'RESIGN'))[0];
        $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $s_personal_data_id])[0];
        if ($mbo_employee_data) {
            $a_pages_list = array();
            $a_topbar_list = array('e_journal');
            $mba_permission_pages = $this->Dm->get_employee_pages(array('ep.employee_id' => $mbo_employee_data->employee_id));
            if ($mba_permission_pages) {
                foreach ($mba_permission_pages as $permission) {
                    if (!in_array($permission->pages_name, $a_pages_list)) {
                        array_push($a_pages_list, $permission->pages_name);
                    }

                    if (!in_array($permission->pages_top_bar, $a_topbar_list)) {
                        array_push($a_topbar_list, $permission->pages_top_bar);
                    }
                }
            }

            $mba_ofse_examiner = $this->Ofm->is_examiner([
                'pd.personal_data_id' => $this->session->userdata('user')
            ]);

            $mba_is_advisor = $this->Tm->is_advisor_examiner([
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'advisor', true);
            $mba_is_examiner = $this->Tm->is_advisor_examiner([
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'examiner');
            
            $b_thesis_allow = false;
            $b_ofse_allow = false;
            if ($mba_is_advisor) {
                $b_thesis_allow = true;
            }
            else if ($mba_is_examiner) {
                $b_thesis_allow = true;
            }

            if ($mba_ofse_examiner) {
                $b_ofse_allow = true;
            }

            if ($b_thesis_allow) {
                if (!in_array('thesis_defense', $a_pages_list)) {
                    array_push($a_pages_list, 'thesis_defense');
                }
                if (!in_array('thesis_student', $a_pages_list)) {
                    array_push($a_pages_list, 'thesis_student');
                }

                if (!in_array('staff_thesis', $a_topbar_list)) {
                    array_push($a_topbar_list, 'staff_thesis');
                }
            }

            if ($b_ofse_allow) {
                if (!in_array('staff_ofse', $a_topbar_list)) {
                    array_push($a_topbar_list, 'staff_ofse');
                }
            }

            $a_permission_pages = array(
                'pages_list' => $a_pages_list,
                'topbar_list' => $a_topbar_list
            );

            $a_permission_pages = $this->manage_letter_number_page($s_personal_data_id, $a_permission_pages);
            $a_permission_pages = $this->manage_gsr_page($s_personal_data_id, $a_permission_pages);
            
            return $a_permission_pages;
        }
        else if ($mba_personal_data) {
            $a_pages_list = array();
            $a_topbar_list = array();
            $mba_is_advisor = $this->Tm->check_is_advisor([
                'ta.personal_data_id' => $this->session->userdata('user'),
                'tp.academic_year_id' => 2021,
                'tp.semester_type_id' => 2
            ]);
            $mba_is_examiner = $this->Tm->check_is_examiner([
                'ta.personal_data_id' => $this->session->userdata('user'),
                'tp.academic_year_id' => 2021,
                'tp.semester_type_id' => 2
            ]);
            
            $b_thesis_allow = false;
            if ($mba_is_advisor) {
                $b_thesis_allow = true;
            }
            else if ($mba_is_examiner) {
                $b_thesis_allow = true;
            }

            if ($b_thesis_allow) {
                if (!in_array('thesis_defense', $a_pages_list)) {
                    array_push($a_pages_list, 'thesis_defense');
                }

                if (!in_array('staff_thesis', $a_topbar_list)) {
                    array_push($a_topbar_list, 'staff_thesis');
                }
            }

            $a_permission_pages = array(
                'pages_list' => $a_pages_list,
                'topbar_list' => $a_topbar_list
            );
            
            return $a_permission_pages;
        }
        else{
            return false;
        }
    }

    // public function manage_gsr_page($s_personal_data_id, $a_permission_pages)
    // {
    //     $s_employee_id = ($this->session->has_userdata('employee_id')) ? $this->session->userdata('employee_id') : false;
    //     if ($s_employee_id) {
    //         $employee_data = $this->General->get_where('dt_employee', ['employee_id' => $s_employee_id]);
    //         $mba_allowed = $this->General->get_where('ref_department', ['employee_id' => $s_employee_id]);
    //         if (!$mba_allowed) {
    //             $a_config_allowed_page = $this->config->item('allowed_page');
    //             $a_config_allowed_page = $a_config_allowed_page['gsr_page'];
    //             if (in_array($this->session->userdata('user'), $a_config_allowed_page)) {
    //                 if ($employee_data) {
    //                     $mba_allowed = $this->General->get_where('ref_department', ['department_id' => $employee_data[0]->department_id]);
    //                 }
    //                 else {
    //                     $mba_allowed = true;
    //                 }
    //             }
    //             // else if ($employee_data) {
    //             //     foreach ($employee_data as $o_employee) {
    //             //         if (in_array($o_employee->department_id, ['11', '12'])) {
    //             //             $mba_allowed = $this->General->get_where('ref_department', ['department_id' => $o_employee->department_id]);
    //             //             break;
    //             //         }
    //             //     }
    //             // }
    //         }
    //         // if ($this->session->userdata('user') == '28010a21-6a24-49d3-988e-58316afd6e97') {
    //         //     print('<pre>');var_dump($mba_allowed);exit;
    //         // }

    //         if ($mba_allowed) {
    //             $b_allow_other = false;
    //             if (!in_array('request_list', $a_permission_pages['pages_list'])) {
    //                 array_push($a_permission_pages['pages_list'], 'request_list');
    //             }

    //             if (is_array($mba_allowed)) {
    //                 foreach ($mba_allowed as $o_department) {
    //                     if (in_array($o_department->department_id, ['11', '12'])) {
    //                         $b_allow_other = true;
    //                         break;
    //                     }
    //                     else if ($employee_data) {
    //                         foreach ($employee_data as $o_employee) {
    //                             if (in_array($o_employee->department_id, ['11', '12'])) {
    //                                 $b_allow_other = true;
    //                                 break;
    //                             }
    //                         }
    //                     }
    //                 }
    //             }

    //             if ($b_allow_other) {
    //                 if (!in_array('chart_list', $a_permission_pages['pages_list'])) {
    //                     array_push($a_permission_pages['pages_list'], 'chart_list');
    //                 }
    //                 if (!in_array('df_list', $a_permission_pages['pages_list'])) {
    //                     array_push($a_permission_pages['pages_list'], 'df_list');
    //                 }
    //                 if (!in_array('rf_list', $a_permission_pages['pages_list'])) {
    //                     array_push($a_permission_pages['pages_list'], 'rf_list');
    //                 }
    //             }

    //             if (!in_array('gsr', $a_permission_pages['topbar_list'])) {
    //                 array_push($a_permission_pages['topbar_list'], 'gsr');
    //             }
    //         }
    //     }
        
    //     return $a_permission_pages;
    // }

    public function manage_letter_number_page($s_personal_data_id, $a_permission_pages)
    {
        $s_employee_id = ($this->session->has_userdata('employee_id')) ? $this->session->userdata('employee_id') : false;
        if ($s_employee_id) {
            $mba_allowed = $this->General->get_where('ref_department', ['employee_id' => $s_employee_id]);
            if (!$mba_allowed) {
                $a_config_allowed_page = $this->config->item('allowed_page');
                $a_config_allowed_page = $a_config_allowed_page['letter_number_page'];
                if (in_array($this->session->userdata('user'), $a_config_allowed_page)) {
                    $mba_allowed = true;
                }
            }

            if ($mba_allowed) {
                if (!in_array('letter_numbering', $a_permission_pages['pages_list'])) {
                    array_push($a_permission_pages['pages_list'], 'letter_numbering');
                }

                if (!in_array('apps', $a_permission_pages['topbar_list'])) {
                    array_push($a_permission_pages['topbar_list'], 'apps');
                }
            }
        }
        
        return $a_permission_pages;
    }
    public function manage_gsr_page($s_personal_data_id, $a_permission_pages)
    {
        $s_employee_id = ($this->session->has_userdata('employee_id')) ? $this->session->userdata('employee_id') : false;
        if ($s_employee_id) {
            $mba_allowed = $this->General->get_where('ref_department', ['employee_id' => $s_employee_id]);
            if (!$mba_allowed) {
                $a_config_allowed_page = $this->config->item('allowed_page');
                $a_config_allowed_page = $a_config_allowed_page['gsr_request_page'];
                if (in_array($this->session->userdata('user'), $a_config_allowed_page)) {
                    $mba_allowed = true;
                }
            }

            if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                // print('<pre>');var_dump($mba_allowed);exit;
            }

            if ($mba_allowed) {
                if (!in_array('request_list', $a_permission_pages['pages_list'])) {
                    array_push($a_permission_pages['pages_list'], 'request_list');
                }

                if (!in_array('apps', $a_permission_pages['topbar_list'])) {
                    array_push($a_permission_pages['topbar_list'], 'apps');
                }
            }
        }
        
        return $a_permission_pages;
    }

    public function employee_list()
    {
        if ($this->input->is_ajax_request()) {
            $mba_employee_lists = $this->Dm->get_employee_lists();
            $a_return = array('code' => 0, 'data' => $mba_employee_lists);

            print json_encode($a_return);
        }else {
            $this->a_page_data['body'] = $this->load->view('employee_lists', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function permission($s_employee_id)
    {
        $mba_employee_data = $this->Emm->get_employee_data(array('em.employee_id' => $s_employee_id));
        if ($mba_employee_data) {
            $this->a_page_data['employee_data'] = $mba_employee_data[0];
            $this->a_page_data['employee_id'] = $s_employee_id;
            $this->a_page_data['body'] = $this->load->view('employee_permission', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }else{
            show_404();
        }
    }

    public function employee_pages_table($s_employee_id)
    {
        if ($this->input->is_ajax_request()) {
            $mba_employee_pages = $this->Dm->get_employee_pages(array('ep.employee_id' => $s_employee_id));
            $a_return = array('code' => 0, 'data' => $mba_employee_pages);

            print json_encode($a_return);
        }else{
            $this->a_page_data['roles_lists'] = $this->Dm->get_roles();
            $this->a_page_data['employee_id'] = $s_employee_id;
            $this->load->view('devs/table/employee_pages_table', $this->a_page_data);
        }
    }

    public function save_pages_permission()
    {
        if ($this->input->is_ajax_request()) {
            $s_employee_id = $this->input->post('employee_id');
            $mba_pages_permission = $this->input->post('data');

            $role_pages = false;
            if (($mba_pages_permission) AND ($mba_pages_permission !== NULL)) {
                $role_pages = array();
                $this->db->trans_start();
                foreach ($mba_pages_permission as $pages) {
                    $a_roles_pages_data = array(
                        'employee_id' => $s_employee_id,
                        'roles_pages_id' => $pages['roles_pages_id']
                    );
                    // array_push($role_pages, array(
                    //     'employee_id' => $s_employee_id,
                    //     'roles_pages_id' => $pages['roles_pages_id']
                    // ));

                    $mba_employee_pages = $this->Dm->get_employee_pages(array(
                        'ep.employee_id' => $s_employee_id,
                        'ep.roles_pages_id' => $pages['roles_pages_id']
                    ));
                    if (!$mba_employee_pages) {
                        $this->Dm->save_employee_permission_page($a_roles_pages_data);
                    }
                }
                
                if ($this->db->trans_status() === TRUE) {
                    $a_return = array('code' => 0, 'message' => 'Succes');
                }else{
                    $a_return = array('code' => 1, 'message' => 'Failed  to save permission pages');
                }
            }else{
                $a_return = array('code' => 1, 'message' => 'Nothing save!');
            }

            print json_encode($a_return);
        }
    }

    public function remove_employee_pages_permission()
    {
        if ($this->input->is_ajax_request()) {
            $a_param = array(
                'employee_id' => $this->input->post('employee_id'),
                'roles_pages_id' => $this->input->post('roles_pages_id')
            );

            if ($this->Dm->remove_employee_pages_permission($a_param)) {
                $a_return = array('code' => 0, 'message' => 'Success');
            }else{
                $a_return = array('code' => 1, 'message' => 'Failed remove data!');
            }

            print json_encode($a_return);
        }
    }

    public function get_permission()
    {
        $mba_permission_lists = $this->Dm->get_roles_pages();
        $a_return = array('code' => 0, 'results' => $mba_permission_lists);
        print json_encode($a_return);
    }

    public function form_input()
    {
        $this->load->view('devs/form/form_input_employee', $this->a_page_data);
    }

    public function employee_table()
    {
        $this->load->view('table/employee_table', $this->a_page_data);
    }

    public function remove_employee()
    {
        if ($this->input->is_ajax_request()) {
            $s_employee_id = $this->input->post('employee_id');
            if ($this->Emm->remove_employee($s_employee_id)) {
                $a_return = array('code' => 0, 'message' => 'Success');
            }else{
                $a_return = array('code' => 1, 'message' => 'Error processing data');
            }

            print json_encode($a_return);
        }
    }

    public function save_employee()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('personal_data_title_prefix', 'Title Prefix', 'trim');
            $this->form_validation->set_rules('personal_data_name', 'Fullname', 'required|trim');
            $this->form_validation->set_rules('personal_data_title_suffix', 'Title Suffix', 'trim');
            $this->form_validation->set_rules('personal_data_cellular', 'Cellular Number', 'required|trim|numeric');
            $this->form_validation->set_rules('personal_data_id_card_number', 'NIK', 'trim|numeric');
            $this->form_validation->set_rules('personal_data_email', 'Personal Email', 'trim|required');
            $this->form_validation->set_rules('personal_data_date_of_birth', 'Date of  Birth', 'trim');
            $this->form_validation->set_rules('personal_data_gender', 'Gender', 'required|trim');
            $this->form_validation->set_rules('employee_id_number', 'ID Card Number', 'trim|required|numeric');
            $this->form_validation->set_rules('employee_email', 'Employee Email', 'trim');
            $this->form_validation->set_rules('employment_status', 'Employee Status', 'trim');
            $this->form_validation->set_rules('employee_is_lecturer', 'Is Lecturer', 'trim|required');

            $s_employee_id = $this->input->post('employee_id');

            if (set_value('employee_is_lecturer') == 'YES') {
                $this->form_validation->set_rules('employee_lecturer_number_type', 'Lecturer Number Type', 'trim');
                $this->form_validation->set_rules('employee_lecturer_number', 'NIDN', 'trim|numeric');
            }

            if ($this->form_validation->run()) {
                $s_personal_data_id = $this->uuid->v4();
                $a_personal_data = array(
                    'personal_data_id' => $s_personal_data_id,
                    'personal_data_title_prefix' => (set_value('personal_data_title_prefix') != '') ? set_value('personal_data_title_prefix') : NULL,
                    'personal_data_name' => set_value('personal_data_name'),
                    'personal_data_title_suffix' => (set_value('personal_data_title_suffix') != '') ? set_value('personal_data_title_suffix') : NULL,
                    'personal_data_cellular' => set_value('personal_data_cellular'),
                    'personal_data_id_card_number' => set_value('personal_data_id_card_number'),
                    'personal_data_email' => set_value('personal_data_email'),
                    'personal_data_gender' => set_value('personal_data_gender')
                );

                $a_employee_data = array(
                    'employee_id' => $this->uuid->v4(),
                    'personal_data_id' => $s_personal_data_id,
                    'employee_id_number' => set_value('employee_id_number'),
                    'employee_email' => (set_value('employee_email') != '') ? set_value('employee_email') : NULL,
                    'employee_is_lecturer' => set_value('employee_is_lecturer'),
                    'employment_group' => (set_value('employee_is_lecturer') == 'YES') ? 'ACADEMIC' : 'NONACADEMIC'
                );

                if (set_value('employee_is_lecturer') == 'YES') {
                    $a_employee_data['employee_lecturer_number_type'] = (set_value('employee_lecturer_number_type') != '') ? set_value('employee_lecturer_number_type') : NULL;
                    $a_employee_data['employee_lecturer_number'] = (set_value('employee_lecturer_number') != '') ? set_value('employee_lecturer_number') : NULL;
                }

                $this->db->trans_start();

                if ($s_employee_id != '') {
                    $mba_employee_data = $this->Emm->get_employee_data(array('em.employee_id' => $s_employee_id))[0];
                    if ($this->Pdm->update_personal_data($a_personal_data, $mba_employee_data->personal_data_id)) {
                        $this->Emm->save_employee($a_employee_data, $s_employee_id);
                    }
                }else{
                    if ($this->Pdm->create_new_personal_data($a_personal_data)) {
                        $this->Emm->save_employee($a_employee_data);
                    }
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $a_return = array('code' => 1, 'message' => 'Error processing data');
                }else{
                    $this->db->trans_commit();
                    $a_return = array('code' => 0, 'message' => 'Success');
                }
            }else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }
}
