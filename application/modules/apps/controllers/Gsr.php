<?php
class Gsr extends App_core
{
    public $access = '';
    public $a_department;
    public $special_access = ['review', 'approve', 'finish'];

    function __construct()
    {
        parent::__construct('gsr');
        $this->load->model('Gsr_model', 'Grm');
        $this->load->model('Dfrf_model', 'Drm');
        $this->load->model('employee/Employee_model', 'Emm');

        $this->a_department = [
            'finance' => '11',
            'vice_rector' => '6',
            // 'rector' => '5'
            'hrd' => '20'
        ];
        $this->access = $this->_get_access();
    }

    public function request_list()
    {
        // +month
        // cc finance + yopi
        // df number & tanggal
        // translate english

        // show_404();
        // print('<pre>');var_dump($this->access);
        // exit;
        // $mba_is_approver_gsr = $this->General->get_where('ref_department', ['department_id' => $this->a_department['vice_rector'], 'employee_id' => $this->session->userdata('employee_id')]);
        // print('<pre>');var_dump($mba_is_approver_gsr);exit;
        $mba_user_finance = $this->General->get_in('dt_employee_department', 'department_id', ['11','12'], ['employee_id' => $this->session->userdata('employee_id')]);
            
        $this->a_page_data['userfinance'] = $mba_user_finance;
        $this->a_page_data['department'] = $this->General->get_where('ref_department');
        $this->a_page_data['body'] = $this->load->view('gsr/request_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function chart_list() {
        $this->a_page_data['account_list'] = $this->Grm->get_account_list();
        $this->a_page_data['body'] = $this->load->view('gsr/chart_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function new_request($s_gsr_id = false)
    {
        $mba_department_list = $this->General->get_where('ref_department', ['employee_id' => $this->session->userdata('employee_id')]);
        $mba_employee_department = $this->General->get_where('dt_employee_department', ['employee_id' => $this->session->userdata('employee_id')]);
        $mba_employee_data = $this->General->get_where('dt_employee', ['employee_id' => $this->session->userdata('employee_id')]);

        $a_department_id = [];
        if ($mba_department_list) {
            foreach ($mba_department_list as $o_dept) {
                array_push($a_department_id, $o_dept->department_id);
            }
        }
        if ($mba_employee_department) {
            foreach ($mba_employee_department as $o_emdept) {
                if (!in_array($o_emdept->department_id, $a_department_id)) {
                    array_push($a_department_id, $o_emdept->department_id);
                }
            }
        }
        if (($mba_employee_data) AND (!is_null($mba_employee_data[0]->department_id))) {
            if (!in_array($mba_employee_data[0]->department_id, $a_department_id)) {
                array_push($a_department_id, $mba_employee_data[0]->department_id);
            }
        }
        // if ($this->session->userdata('employee_id') == '9ec3571e-4d25-4c1b-8694-dd8ce10b2986') {
        //     print('<pre>');var_dump($a_department_id);exit;
        // }
        $mba_department_list = false;
        if (count($a_department_id) > 0){
            $mba_department_list = $this->General->get_in('ref_department', 'department_id', $a_department_id);
        }

        if ($s_gsr_id) {
            $mba_gsr_data = $this->Grm->get_gsr_data(['gm.gsr_id' => $s_gsr_id]);
            if ($mba_gsr_data) {
                $this->a_page_data['gsr_main_data'] = $mba_gsr_data[0];
                $this->a_page_data['gsr_details_data'] = $this->Grm->get_gsr_details(['gd.gsr_id' => $s_gsr_id]);
                $this->a_page_data['gst_attachment_data'] = $this->Grm->get_gsr_attachment(['gsm.gsr_id' => $s_gsr_id]);
            }
        }

        $mba_userdata = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);

        $this->a_page_data['department_list'] = $mba_department_list;
        $this->a_page_data['rector_name'] = ($mbo_rector_data = $this->General->get_rectorate('vice_rector')) ? $mbo_rector_data->rector_full_name : '';
        $this->a_page_data['optional_gsr_number'] = $this->get_gsr_number($mba_department_list[0]->department_id);
        $this->a_page_data['unit_list'] = $this->Grm->get_unit_list();
        // print('<pre>');var_dump($this->a_page_data['unit_list']);exit;
        $this->a_page_data['request_name'] = $mba_userdata[0]->personal_data_name;
        $this->a_page_data['body'] = $this->load->view('gsr/form/form_request', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function submit_finance_note() {
        if ($this->input->is_ajax_request()) {
            $s_gsr_id = $this->input->post('note_gsr_id');
            $s_note_gsr_finance = $this->input->post('note_gsr_finance');
            $s_note_gsr_finance = (empty($s_note_gsr_finance)) ? NULL : $s_note_gsr_finance;

            $this->Grm->force_update('dt_gsr_main', ['gsr_finance_note' => $s_note_gsr_finance], ['gsr_id' => $s_gsr_id]);
            print json_encode(['code' => 0, 'message' => 'Success']);
        }
    }

    function submit_account() {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('input_account_no', 'Account No', 'trim');
            $this->form_validation->set_rules('input_sub_account_no', 'Sub Account', 'trim');
            $this->form_validation->set_rules('input_account_name', 'Account Name', 'trim');
            $this->form_validation->set_rules('input_account_type', 'Account Type', 'trim');
            if ($this->form_validation->run()) {
                $mba_account_main = false;
                if (!empty(set_value('input_sub_account_no'))) {
                    $mba_account_main = $this->Grm->get_where('dt_account_list', ['account_no' => set_value('input_sub_account_no')]);
                }
                $a_account_data = [
                    'account_no' => set_value('input_account_no'),
                    'account_no_main' => (empty(set_value('input_sub_account_no'))) ? NULL : set_value('input_sub_account_no'),
                    'account_marked_strong' => (empty(set_value('input_sub_account_no'))) ? '1' : '0',
                    'account_name' => set_value('input_account_name'),
                    'account_type' => set_value('input_account_type'),
                    'level_of_padd' => ($mba_account_main) ? (intval($mba_account_main[0]->level_of_padd) + 1) : 0
                ];

                if ($this->Grm->save_account_list($a_account_data)) {
                    $a_return = ['code' => 0, 'message' => 'Success'];
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'No data save!'];
                }
            }
            else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }

    function submit_review() {
        if ($this->input->is_ajax_request()) {
            $s_gsr_id = $this->input->post('gsr_id');
            $s_approval = $this->input->post('action_approval');
            $s_action_allowed = $this->input->post('request_action');
            $a_note = $this->input->post('gsr_note');
            $a_note_data = [];
            
            $mba_status_action = $this->Grm->get_where('ref_status_action', ['status_action' => $s_approval, 'current_progress' => 'reviewed']);

            $s_gsr_status_id = $this->uuid->v4();
            $a_gsr_main_update = [
                'gsr_allow_update' => ($s_action_allowed == 'repair') ? 'true' : 'false',
                'personal_data_id_review' => ($s_approval == 'approve') ? $this->session->userdata('user') : NULL,
                'gsr_reviewed' => ($s_approval == 'approve') ? $this->session->userdata('name') : NULL,
            ];
            $a_gsr_status = [
                'status_id' => $s_gsr_status_id,
                'gsr_id' => $s_gsr_id,
                'personal_data_id' => $this->session->userdata('user'),
                'status_action_id' => ($mba_status_action) ? $mba_status_action[0]->status_action_id : 1,
                'date_added' => date('Y-m-d H:i:s')
            ];
            if ((is_array($a_note)) AND (count($a_note) > 0)) {
                foreach ($a_note as $s_note) {
                    array_push($a_note_data, [
                        'note_id' => $this->uuid->v4(),
                        'status_id' => $s_gsr_status_id,
                        'personal_data_id' => $this->session->userdata('user'),
                        'note' => $s_note,
                        'date_added' => date('Y-m-d H:i:s')
                    ]);
                }
            }
            
            $a_return = $this->Grm->submit_review($a_gsr_main_update, $a_gsr_status, $a_note_data, $s_gsr_id);
            print json_encode($a_return);
        }
    }

    function submit_approve() {
        if ($this->input->is_ajax_request()) {
            $s_gsr_id = $this->input->post('gsr_id');
            $s_approval = $this->input->post('action_approval');
            $s_action_allowed = $this->input->post('request_action');
            $a_note = $this->input->post('gsr_note');
            $a_note_data = [];
            
            $mba_status_action = $this->Grm->get_where('ref_status_action', ['status_action' => $s_approval, 'current_progress' => 'approved']);
            $mba_gsr_data = $this->Grm->get_where('dt_gsr_main', ['gsr_id' => $s_gsr_id]);

            if ($mba_gsr_data) {
                if (!is_null($mba_gsr_data[0]->personal_data_id_review)) {
                    $s_gsr_status_id = $this->uuid->v4();
                    $a_gsr_main_update = [
                        'gsr_allow_update' => ($s_action_allowed == 'repair') ? 'true' : 'false',
                        'personal_data_id_approved' => ($s_approval == 'approve') ? $this->session->userdata('user') : NULL,
                        'gsr_approved' => ($s_approval == 'approve') ? $this->session->userdata('name') : NULL,
                    ];
                    $a_gsr_status = [
                        'status_id' => $s_gsr_status_id,
                        'gsr_id' => $s_gsr_id,
                        'personal_data_id' => $this->session->userdata('user'),
                        'status_action_id' => ($mba_status_action) ? $mba_status_action[0]->status_action_id : 1,
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    if ((is_array($a_note)) AND (count($a_note) > 0)) {
                        foreach ($a_note as $s_note) {
                            array_push($a_note_data, [
                                'note_id' => $this->uuid->v4(),
                                'status_id' => $s_gsr_status_id,
                                'personal_data_id' => $this->session->userdata('user'),
                                'note' => $s_note,
                                'date_added' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                    
                    $a_return = $this->Grm->submit_approve($a_gsr_main_update, $a_gsr_status, $a_note_data, $s_gsr_id);
                }
                else {
                    $a_return = ['code' => '2', 'message' => 'Process cannot continue, need to review it first!'];
                }
            }
            else {
                $a_return = ['code' => '1', 'message' => 'GSR Not found!!'];
            }
            print json_encode($a_return);
        }
    }

    public function submit_gsr()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('gsr_code', 'Gsr No', 'trim|required');
            $this->form_validation->set_rules('booking_code', 'Booking Code / Account Name', 'trim|required');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');
            $this->form_validation->set_rules('budget', 'Budget Proposal No', 'trim');
            $this->form_validation->set_rules('activity_no', 'Activity', 'trim');
            $this->form_validation->set_rules('total_amount', 'Total Price', 'trim');
            // $this->form_validation->set_rules('approved_by', 'Approved By', 'trim|required');
            // $this->form_validation->set_rules('reviewed_by', 'Reviewed By', 'trim|required');
            $this->form_validation->set_rules('quantity[]', 'Quantity at Detail Form', 'trim|required');
            $this->form_validation->set_rules('description[]', 'Description at Detail Form', 'trim|required');
            $this->form_validation->set_rules('unit[]', 'Unit at Detail Form', 'trim|required');
            $this->form_validation->set_rules('unitprice[]', 'Unit Price (IDR) at Detail Form', 'trim|required');
            
            // $this->form_validation->set_rules('total_amount_text', 'Total Amount ', 'trim');

            if ($this->form_validation->run()) {
                $valid_details = false;
                $s_gsr_id = $this->input->post('gsr_update_key');
                $s_gsr_id = (empty($s_gsr_id)) ? null : $s_gsr_id;
                if ((is_array($this->input->post('description'))) AND (count($this->input->post('description')) > 0)) {
                    $a_return = $this->Grm->submit_gsr($this->input->post());
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'No data save!'];
                }
            }
            else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }

    function submit_action($s_param, $action = false) {
        $a_key = json_decode(base64_decode($s_param));
        // print('<pre>');var_dump($a_key);exit;
        if (!is_null($a_key)) {
            $a_key = (array) $a_key;
            if ($this->session->userdata('user') == $a_key['pidt']) {
                $o_gsr_data = $this->Grm->get_gsr_data(['gm.gsr_id' => $a_key['sid']])[0];
                $gsr_attachment = $this->Grm->get_gsr_attachment(['gsm.gsr_id' => $a_key['sid']]);
                if ($gsr_attachment) {
                    $mba_user_request = $this->Emm->get_employee_data(['em.personal_data_id' => $o_gsr_data->personal_data_id_request]);
                    $s_firts_char_user = substr($mba_user_request[0]->personal_data_name, 0, 1);
                    $s_gsr_path  = 'staff/'.$s_firts_char_user.'/'.$o_gsr_data->personal_data_id_request.'/';
                    foreach ($gsr_attachment as $o_attachment) {
                        $s_path_link = $s_gsr_path.$o_attachment->document_link;
                        $o_attachment->path_link = urlencode(base64_encode($s_path_link));
                        // base64_decode(urldecode($s_filesencrypt));
                    }
                }
                $this->a_page_data['access'] = $this->access;
                $this->a_page_data['last_status'] = $this->Grm->get_gsr_status_log(['gs.gsr_id' => $a_key['sid']]);
                $this->a_page_data['with_action'] = ($action) ? $action : '';
                $this->a_page_data['gsr_data'] = $o_gsr_data;
                $this->a_page_data['gsr_attachment'] = $gsr_attachment;
                $this->a_page_data['gsr_remarks'] = $this->Grm->get_remarks_list(['gm.gsr_id' => $a_key['sid']]);
                $this->a_page_data['gsr_view'] = $this->view_detail($a_key['sid'], true);
                $this->a_page_data['body'] = $this->load->view('apps/gsr/misc/gsr_action', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
            else {
                show_404();
            }
        }
        else {
            show_404();
        }
    }

    function view_detail($s_gsr_id, $b_get_page = false) {
        $mba_gsr_data = $this->Grm->get_gsr_data([
            'gm.gsr_id' => $s_gsr_id
        ]);
        if ($mba_gsr_data) {
            $o_gsr = $mba_gsr_data[0];
            $mba_user_request = $this->Grm->get_gsr_user(['pdd.key_table' => 'portal_gsr.dt_gsr_main', 'key_id' => $o_gsr->gsr_id, 'em.personal_data_id' => $o_gsr->personal_data_id_request], ['pdd.date_added' => 'DESC']);
            $mba_user_review = $this->Grm->get_gsr_user(['pdd.key_table' => 'portal_gsr.dt_gsr_main', 'key_id' => $o_gsr->gsr_id, 'em.personal_data_id' => $o_gsr->personal_data_id_review], ['pdd.date_added' => 'DESC']);
            $mba_user_approve = $this->Grm->get_gsr_user(['pdd.key_table' => 'portal_gsr.dt_gsr_main', 'key_id' => $o_gsr->gsr_id, 'em.personal_data_id' => $o_gsr->personal_data_id_approved], ['pdd.date_added' => 'DESC']);
            $mba_user_finish = $this->Grm->get_gsr_user(['pdd.key_table' => 'portal_gsr.dt_gsr_main', 'key_id' => $o_gsr->gsr_id, 'em.personal_data_id' => $o_gsr->personal_data_id_finishing], ['pdd.date_added' => 'DESC']);

            // print('<pre>');var_dump($mba_user_request);exit;
            $this->a_page_data['gsr_request_data'] = ($mba_user_request) ? $mba_user_request[0] : false;
            $this->a_page_data['gsr_review_data'] = ($mba_user_review) ? $mba_user_review[0] : false;
            $this->a_page_data['gsr_approve_data'] = ($mba_user_approve) ? $mba_user_approve[0] : false;
            $this->a_page_data['gsr_finish_data'] = ($mba_user_finish) ? $mba_user_finish[0] : false;
            $this->a_page_data['gsr_data'] = $o_gsr;
            $this->a_page_data['gsr_details'] = $this->Grm->get_gsr_details(['gd.gsr_id' => $s_gsr_id]);

            if ($b_get_page) {
                $body = $this->load->view('apps/gsr/misc/gsr_view', $this->a_page_data, true);
                return $body; 
            }
            else {
                $this->a_page_data['body'] = $this->load->view('apps/gsr/misc/gsr_view', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
        }
        else {
            show_404();
        }
    }

    function check_access($s_personal_data_id) {
        print('<pre>');var_dump($this->access);
    }

    public function get_list_request()
    {
        if ($this->input->is_ajax_request()) {
            $a_config_allowed_access = $this->config->item('allowed_page');
            $a_config_allowed_access = $a_config_allowed_access['gsr_request_page'];
            $mba_user_finance = false;
            if (in_array($this->session->userdata('user'), $a_config_allowed_access)) {
                $mba_user_finance = $this->General->get_in('dt_employee_department', 'department_id', ['11','12'], ['employee_id' => $this->session->userdata('employee_id')]);
            }

            $mba_data = false;
            $b_show_all = false;
            $mba_department_list = $this->General->get_where('ref_department', ['employee_id' => $this->session->userdata('employee_id')]);
            $mba_employee_department_list = $this->General->get_where('dt_employee_department', ['employee_id' => $this->session->userdata('employee_id')]);
            
            // print('<pre>');var_dump($mba_department_list);exit;
            if ($mba_department_list) {
                $a_department_id = [];
                foreach ($mba_department_list as $o_department) {
                    if (!in_array($o_department->department_id, $a_department_id)) {
                        array_push($a_department_id, $o_department->department_id);
                    }
                }

                if ($mba_employee_department_list) {
                    foreach ($mba_employee_department_list as $o_department) {
                        if (!in_array($o_department->department_id, $a_department_id)) {
                            array_push($a_department_id, $o_department->department_id);
                        }
                    }
                }

                if (in_array($this->access, $this->special_access)) {
                    if (!empty($this->input->post('check_department'))) {
                        if (($this->input->post('department_id') == '') OR ($this->input->post('department_id') == 'all')) {
                            $b_show_all = true;
                        }
                        // 
                        else {
                            $a_department_id = [$this->input->post('department_id')];
                        }
                    }
                    else if (in_array($this->access, ['review', 'approve'])) {
                        $b_show_all = true;
                    }
                }
                else if ($mba_user_finance) {
                    $b_show_all = true;
                }
                
                if ($b_show_all) {
                    $mba_data = $this->Grm->get_gsr_data(false);
                }
                else {
                    $mba_data = $this->Grm->get_gsr_data(false, $a_department_id);
                }
                // print('<pre>');var_dump($mba_dat);
                
                if ($mba_data) {
                    foreach ($mba_data as $o_data) {
                        // $mba_list_status = $this->Grm->get_gsr_status_log(['gs.gsr_id' => $o_data->gsr_id]);
                        // $o_data->current_status = 'requested';

                        // if ($mba_list_status) {
                        //     $o_data->current_status = $this->translate_status($mba_list_status->status_action_id);
                        // }
                        
                        $mba_user_request = $this->Emm->get_employee_data(['em.personal_data_id' => $o_data->personal_data_id_request]);
                        $mba_user_review = $this->Emm->get_employee_data(['em.personal_data_id' => $o_data->personal_data_id_review]);
                        $mba_user_approve = $this->Emm->get_employee_data(['em.personal_data_id' => $o_data->personal_data_id_approved]);
                        $mba_user_finish = $this->Emm->get_employee_data(['em.personal_data_id' => $o_data->personal_data_id_finishing]);

                        $o_data->user_request = ($mba_user_request) ? $mba_user_request[0]->personal_data_name : '';
                        $o_data->user_review = ($mba_user_review) ? $mba_user_review[0]->personal_data_name : '';
                        $o_data->user_approve = ($mba_user_approve) ? $mba_user_approve[0]->personal_data_name : '';
                        $o_data->user_finish = ($mba_user_finish) ? $mba_user_finish[0]->personal_data_name : '';
                        $o_data->is_finance = $b_show_all;

                        $o_data->total_items = 0;
                        $o_data->total_price = 0;

                        $a_for_view = [
                            'sid' => $o_data->gsr_id,
                            'pidt' => $this->session->userdata('user'),
                            'mode' => 'view'
                        ];
                        $o_data->for_view = str_replace('=', '', base64_encode(json_encode($a_for_view)));
                    }
                }
            }
            print json_encode(['data' => $mba_data]);
        }
    }

    public function get_list_account()
	{
        if ($this->input->is_ajax_request()){
            if ($this->input->post('term') !== null) {
                $account_list = $this->Grm->get_account_list(false, $this->input->post('term'));
            }
            else {
                $account_list = $this->Grm->get_account_list();
            }
            
            $return = array('code' => 0, 'data' => $account_list);
            print json_encode($return);
    
        }
	}

    public function get_gsr_number($s_department_id = false)
    {
        if ($this->input->is_ajax_request()) {
            $s_department_id = $this->input->post('department_id');
        }
        
        $s_gsr_number = '';
        $i_year = date('Y');
        $is_department = $this->General->get_where('ref_department', ['department_id' => $s_department_id]);
        if ($is_department) {
            $s_gsr_number = $this->Grm->get_last_number_gsr($s_department_id, $i_year);
        }

        if ($this->input->is_ajax_request()) {
            print json_encode(['gsr_number' => $s_gsr_number]);
        }else{
            return $s_gsr_number;
        }
    }

    function force_generate_gsr($s_gsr_id) {
        $generate_file = modules::run('download/pdf_download/generate_gsr_file', $s_gsr_id);
        print('<pre>');var_dump($generate_file);exit;
    }

    private function _get_access()
    {
        $s_access = 'not allowed';
        $s_personal_data_id = $this->session->userdata('user');
        $s_employee_id = $this->session->userdata('employee_id');
        $a_config_allowed_access = $this->config->item('allowed_page');
        $a_config_allowed_access = $a_config_allowed_access['gsr_request_page'];
        $mba_user_head_department = $this->General->get_where('ref_department', ['employee_id' => $s_employee_id]);
        
        if ($mba_user_head_department) {
            $s_access = 'request';
        }
        else if (in_array($s_personal_data_id, $a_config_allowed_access)) {
            $s_access = 'request';
        }
        
        if ($s_access == 'request') {
            $mba_is_reviewer = $this->General->get_where('ref_department', ['department_id' => $this->a_department['finance'], 'employee_id' => $s_employee_id]);
            $mba_is_approver_gsr = $this->General->get_where('ref_department', ['department_id' => $this->a_department['vice_rector'], 'employee_id' => $s_employee_id]);
            // $mba_is_approver_gsr = $this->General->get_where('ref_department', ['department_id' => $this->a_department['hrd'], 'employee_id' => $s_employee_id]);
            $mba_is_finishing = $this->General->get_where('ref_department', ['department_id' => '47', 'employee_id' => $s_employee_id]);

            // print('<pre>');var_dump($mba_is_approver_gsr);exit;
            if ($mba_is_approver_gsr) {
                $s_access = 'approve';
            }
            else if ($mba_is_reviewer) {
                $s_access = 'review';
            }
            else if ($mba_is_finishing) {
                $s_access = 'finish';
            }
            // else if (in_array($this->session->userdata('user'), ['06981f96-4332-45d1-943a-5a316a19d5c0'])) {
            //     $s_access = 'review';
            // }
        }

        return $s_access;
    }
}
