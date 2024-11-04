<?php
class Gsr_old extends App_core
{
    public $access = '';
    public $special_access = ['review', 'approve', 'finish'];
    function __construct()
    {
        parent::__construct('gsr');
        $this->load->model('Gsr_model', 'Grm');
        $this->load->model('Dfrf_model', 'Drm');
        $this->load->model('employee/Employee_model', 'Emm');

        $this->access = $this->_get_access();
        $this->a_page_data['access'] = $this->access;
        $this->a_page_data['special_access'] = $this->special_access;
    }

    public function set_approve()
    {
        print('<pre>');var_dump($_POST);exit;
    }

    public function download($s_gsr_id)
    {
        $mbo_gsr_data = $this->Grm->get_where('dt_gsr_main', ['gsr_id' => $s_gsr_id]);
        if (!$mbo_gsr_data) {
            show_404();
        }

        $mbo_gsr_data = $mbo_gsr_data[0];
        $mba_gsr_request_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mbo_gsr_data->personal_data_id_request]);
        $s_firts_char_user = substr($mba_gsr_request_data[0]->personal_data_name, 0, 1);
        
        $s_gsr_path  = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$mbo_gsr_data->personal_data_id_request.'/'.date('Y', strtotime($mbo_gsr_data->gsr_date_request)).'/gsr/'.str_replace(' ', '_', $mbo_gsr_data->gsr_code).'/';
        $s_gsr_name = str_replace(' ', '_', $mbo_gsr_data->gsr_code).'.pdf';
        $s_filename = $mbo_gsr_data->gsr_code.'.pdf';
        if (file_exists($s_gsr_path.$s_gsr_name)) {
            $s_mime = mime_content_type($s_gsr_path.$s_gsr_name);
            header("Content-Type: ".$s_mime);
            header('Content-Disposition: inline; filename='.$mba_gsr_file_data[0]->document_name);
            readfile( $s_gsr_path.$s_gsr_name );
            exit;
        }
        else {
            show_404();
        }
    }

    public function submit_action($param = false, $mbs_action = false)
    {
        $this->load->model('hris/Hris_model', 'Hrm');
        $a_param = json_decode(base64_decode($param));
        $mbo_finishing_data = $this->General->get_head_department('Y-IULI');
        // print('<pre>');var_dump($a_param);exit;
        $b_link_redirect = 'apps/gsr/view_detail/'.$a_param->sid;
        $b_allow_action = false;
        
        if ((!$param) OR (is_null($a_param))) {
            show_404();
        }
        else if ((isset($a_param->dfrf_form)) AND ($a_param->dfrf_form)) {
            $mba_is_finance = $this->Hrm->get_employee_department([
                'em.personal_data_id' => $this->session->userdata('user')
            ], ['11', '12']);

            if ($mba_is_finance) {
                redirect($b_link_redirect);
            }
            else {
                show_403();
            }
        }
        else if ($a_param->pidt == $mbo_finishing_data->personal_data_id) {
            $b_allow_action = true;
        }
        else if ($this->session->userdata('user') != $a_param->pidt) {
            show_403();
        }

        if ($b_allow_action) {
            switch ($mbs_action) {
                case 'approve':
                    redirect($b_link_redirect.'/?act=approve');
                    break;

                case 'reject':
                    redirect($b_link_redirect.'/?act=reject');
                    break;

                case 'approve_review':
                    redirect($b_link_redirect.'/?act=approve_review');
                    break;

                case 'reject_review':
                    redirect($b_link_redirect.'/?act=reject_review');
                    break;

                case 'approve_finish':
                    redirect($b_link_redirect.'/?act=approve_finish');
                    break;

                case 'reject_finish':
                    redirect($b_link_redirect.'/?act=reject_finish');
                    break;
                
                default:
                    redirect($b_link_redirect);
                    break;
            }
        }
        
        redirect(base_url());
    }

    public function submit_action_df($param = false, $mbs_action = false)
    {
        $a_param = json_decode(base64_decode($param));
        // print("<pre>");var_dump($a_param);exit;
        if ((!$param) OR (is_null($a_param))) {
            show_403();
        }
        else if ($this->session->userdata('user') != $a_param->pidt) {
            show_403();
        }
        else {
            switch ($mbs_action) {
                case 'approve':
                    redirect('apps/gsr/view_df_detail/'.$a_param->sid.'/?act=approve');
                    break;

                case 'reject':
                    redirect('apps/gsr/view_df_detail/'.$a_param->sid.'/?act=reject');
                    break;

                case 'approve_checked':
                    redirect('apps/gsr/view_df_detail/'.$a_param->sid.'/?act=approve_checked');
                    break;

                case 'reject_checked':
                    redirect('apps/gsr/view_df_detail/'.$a_param->sid.'/?act=reject_review');
                    break;

                case 'approve_finish':
                    redirect('apps/gsr/view_df_detail/'.$a_param->sid.'/?act=approve_finish');
                    break;

                case 'reject_finish':
                    redirect('apps/gsr/view_df_detail/'.$a_param->sid.'/?act=reject_finish');
                    break;
                
                default:
                    redirect('apps/gsr/view_df_detail/'.$a_param->sid);
                    break;
            }
        }
        redirect(base_url());
    }

    public function submit_checking_df()
    {
        if ($this->input->is_ajax_request()) {
            // print('<pre>');var_dump($this->input->post());exit;
            $s_df_id = $this->input->post('df_id');
            $s_result = $this->input->post('action_approval');
            $s_user_action = $this->input->post('request_action');
            $a_note = $this->input->post('gsr_note');

            if (empty($s_df_id)) {
                $a_return = ['code' => 1, 'message' => 'No Key found!'];
            }
            else if ($this->access == 'review') {
                $mba_df_data = $this->Drm->get_where('dt_df_main', ['df_id' => $s_df_id]);
                $s_df_type = ($mba_df_data[0]->df_type == 'Bank Receipt') ? 'RF' : 'DF';

                $a_df_update = [
                    'personal_data_id_checked' => $this->session->userdata('user'),
                    'df_allow_update' => 'false'
                ];

                if (($s_result == 'reject') AND ($s_user_action == 'repair')) {
                    $a_df_update['df_allow_update'] = 'true';
                }

                $s_df_status_id = $this->uuid->v4();
                $s_status_action_id = ($s_result == 'approve') ? '5' : '4';
                $a_df_status = [
                    'status_id' => $s_df_status_id,
                    'df_id' => $s_df_id,
                    'personal_data_id' => $this->session->userdata('user'),
                    'status_action_id' => $s_status_action_id,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                $a_note_data = [];
                if ((is_array($a_note)) AND (count($a_note))) {
                    $a_note_data = [];
                    foreach ($a_note as $s_note) {
                        if (!empty(trim($s_note))) {
                            $a_notes = [
                                'note_id' => $this->uuid->v4(),
                                'status_id' => $s_df_status_id,
                                'personal_data_id' => $this->session->userdata('user'),
                                'note' => trim($s_note),
                                'date_added' => date('Y-m-d H:i:s')
                            ];

                            array_push($a_note_data, $a_notes);
                        }
                    }
                }
                // print('<pre>');var_dump($a_note_data);exit;

                $a_return = $this->Drm->submit_checking($a_df_update, $a_df_status, $a_note_data, $s_df_id);
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Access Denied!'];
            }

            print json_encode($a_return);
        }
    }

    public function submit_review()
    {
        if ($this->input->is_ajax_request()) {
            $s_gsr_id = $this->input->post('gsr_id');
            $s_result = $this->input->post('action_approval');
            $s_user_action = $this->input->post('request_action');
            $a_note = $this->input->post('gsr_note');

            if (empty($s_gsr_id)) {
                $a_return = ['code' => 1, 'message' => 'No Key found!'];
            }
            else if ($this->access == 'review') {
                $a_gsr_update = [
                    'personal_data_id_review' => $this->session->userdata('user'),
                    'gsr_allow_update' => 'false'
                ];

                if (($s_result == 'reject') AND ($s_user_action == 'repair')) {
                    $a_gsr_update['gsr_allow_update'] = 'true';
                }

                $s_status_action_id = ($s_result == 'approve') ? '3' : '2';

                $s_gsr_status_id = $this->uuid->v4();
                $a_gsr_status = [
                    'status_id' => $s_gsr_status_id,
                    'gsr_id' => $s_gsr_id,
                    'personal_data_id' => $this->session->userdata('user'),
                    'status_action_id' => $s_status_action_id,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                $a_note_data = [];
                if ((is_array($a_note)) AND (count($a_note))) {
                    $a_note_data = [];
                    foreach ($a_note as $s_note) {
                        if (!empty(trim($s_note))) {
                            $a_notes = [
                                'note_id' => $this->uuid->v4(),
                                'status_id' => $s_gsr_status_id,
                                'personal_data_id' => $this->session->userdata('user'),
                                'note' => trim($s_note),
                                'date_added' => date('Y-m-d H:i:s')
                            ];

                            array_push($a_note_data, $a_notes);
                        }
                    }
                }
                // print('<pre>');var_dump($a_note_data);exit;

                $a_return = $this->Grm->submit_review($a_gsr_update, $a_gsr_status, $a_note_data, $s_gsr_id);
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Access Denied!'];
            }

            print json_encode($a_return);
        }
    }

    public function submit_approve()
    {
        if ($this->input->is_ajax_request()) {
            // print('<pre>');var_dump($this->input->post());exit;
            $s_gsr_id = $this->input->post('gsr_id');
            $s_result = $this->input->post('action_approval');
            $s_user_action = $this->input->post('request_action');
            $a_note = $this->input->post('gsr_note');

            if (empty($s_gsr_id)) {
                $a_return = ['code' => 1, 'message' => 'No Key found!'];
            }
            else if ($this->access == 'approve') {
                $a_gsr_update = [
                    'personal_data_id_approved' => $this->session->userdata('user'),
                    'gsr_allow_update' => 'false'
                ];

                if (($s_result == 'reject') AND ($s_user_action == 'repair')) {
                    $a_gsr_update['gsr_allow_update'] = 'true';
                }

                $s_gsr_status_id = $this->uuid->v4();
                $s_status_action_id = ($s_result == 'approve') ? '7' : '6';
                $a_gsr_status = [
                    'status_id' => $s_gsr_status_id,
                    'gsr_id' => $s_gsr_id,
                    'personal_data_id' => $this->session->userdata('user'),
                    'status_action_id' => $s_status_action_id,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                $a_note_data = [];
                if ((is_array($a_note)) AND (count($a_note))) {
                    $a_note_data = [];
                    foreach ($a_note as $s_note) {
                        if (!empty(trim($s_note))) {
                            $a_notes = [
                                'note_id' => $this->uuid->v4(),
                                'status_id' => $s_gsr_status_id,
                                'personal_data_id' => $this->session->userdata('user'),
                                'note' => trim($s_note),
                                'date_added' => date('Y-m-d H:i:s')
                            ];

                            array_push($a_note_data, $a_notes);
                        }
                    }
                }

                $a_return = $this->Grm->submit_approve($a_gsr_update, $a_gsr_status, $a_note_data, $s_gsr_id);
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Access Denied!'];
            }

            print json_encode($a_return);
        }
    }

    public function submit_finish()
    {
        if ($this->input->is_ajax_request()) {
            $s_gsr_id = $this->input->post('gsr_id');
            $s_result = $this->input->post('action_approval');
            $s_user_action = $this->input->post('request_action');
            $a_note = $this->input->post('gsr_note');

            if (empty($s_gsr_id)) {
                $a_return = ['code' => 1, 'message' => 'No Key found!'];
            }
            else if ($this->access == 'finish') {
                $a_gsr_update = [
                    'personal_data_id_finishing' => $this->session->userdata('user'),
                    'gsr_allow_update' => 'false'
                ];

                if (($s_result == 'reject') AND ($s_user_action == 'repair')) {
                    $a_gsr_update['gsr_allow_update'] = 'true';
                }

                $s_gsr_status_id = $this->uuid->v4();
                $s_status_action_id = ($s_result == 'approve') ? '9' : '8';
                $a_gsr_status = [
                    'status_id' => $s_gsr_status_id,
                    'gsr_id' => $s_gsr_id,
                    'personal_data_id' => $this->session->userdata('user'),
                    'status_action_id' => $s_status_action_id,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                $a_note_data = [];
                if ((is_array($a_note)) AND (count($a_note))) {
                    $a_note_data = [];
                    foreach ($a_note as $s_note) {
                        if (!empty(trim($s_note))) {
                            $a_notes = [
                                'note_id' => $this->uuid->v4(),
                                'status_id' => $s_gsr_status_id,
                                'personal_data_id' => $this->session->userdata('user'),
                                'note' => trim($s_note),
                                'date_added' => date('Y-m-d H:i:s')
                            ];

                            array_push($a_note_data, $a_notes);
                        }
                    }
                }

                $a_return = $this->Grm->submit_finish($a_gsr_update, $a_gsr_status, $a_note_data, $s_gsr_id);
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Access Denied!'];
            }

            print json_encode($a_return);
        }
    }

    // public function test_view()
    // {
    //     $mba_gsr_data = $this->Grm->get_gsr_data([
    //         'gm.gsr_id' => $s_gsr_id
    //     ]);

    //     print('<pre>');var_dump($mba_gsr_data);exit;
    //     $this->load->view('gsr/misc/gsr_view');
    // }

    public function submit_gsr()
    {
        if ($this->input->is_ajax_request()) {
            // print ('<pre>');var_dump($_POST);exit;
            // if (!is_null($this->input->post('fileattach'))) {
            //     print('<pre>');var_dump($this->input->post('fileattach'));
            // }
            // else {
            //     print('ga ada!');
            // }
            // exit;
            $this->form_validation->set_rules('gsr_code', 'Gsr No', 'trim|required');
            $this->form_validation->set_rules('booking_code', 'Booking Code / Account Name', 'trim|required');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');
            $this->form_validation->set_rules('budget', 'Budget Proposal No', 'trim');
            $this->form_validation->set_rules('activity_no', 'Activity', 'trim');
            $this->form_validation->set_rules('total_amount', 'Total Price', 'trim|callback_validate');
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

    public function test_get_data()
    {
        // 
        $mbo_gsr_data = $this->Drm->get_where('dt_gsr_main', ['gsr_id' => '3233da2f-cd46-4b9c-b2a1-cdbc7d9ca650']);
        $mbo_gsr_data = $mbo_gsr_data[0];
        print('<pre>');var_dump($mbo_gsr_data);
    }

    public function submit_df()
    {
        if ($this->input->is_ajax_request()) {
            // print ('<pre>');var_dump($_POST);exit;
            // if (!is_null($this->input->post('fileattach'))) {
            //     print('<pre>');var_dump($this->input->post('fileattach'));
            // }
            // else {
            //     print('ga ada!');
            // }
            // exit;
            $this->form_validation->set_rules('df_number', 'VCH No', 'trim|required');
            $this->form_validation->set_rules('df_type', 'Transaction Type', 'trim|required');
            $this->form_validation->set_rules('df_date', 'Date', 'trim|required');
            $this->form_validation->set_rules('gsr_id', 'Reff No', 'trim|required');
            $this->form_validation->set_rules('df_top', 'Term of Payment', 'trim|required');
            $this->form_validation->set_rules('df_account', 'Account', 'trim|required');
            $this->form_validation->set_rules('transaction_paidreceive', 'Paid / Receive', 'trim|required');
            $this->form_validation->set_rules('budget', 'Budget Dept', 'trim');
            $this->form_validation->set_rules('activity_no', 'Activity', 'trim');
            $this->form_validation->set_rules('bank_code', 'Bank', 'trim');
            $this->form_validation->set_rules('gsr_remarks', 'Remarks', 'trim');
            $this->form_validation->set_rules('accountno[]', 'Account No at Detail Form', 'trim|required');
            $this->form_validation->set_rules('df_desc[]', 'Remark Transaction at Detail Form', 'trim|required');
            $this->form_validation->set_rules('df_kredit[]', 'Debet (IDR) at Detail Form', 'trim');
            $this->form_validation->set_rules('df_debet[]', 'Kredit (IDR) at Detail Form', 'trim');
            
            // $this->form_validation->set_rules('total_amount_text', 'Total Amount ', 'trim');

            // print("<pre>");
            // var_dump($this->input->post('fileattach'));exit;
            if ($this->form_validation->run()) {
                $valid_details = false;
                $s_df_id = $this->input->post('df_update_key');
                $s_df_id = (empty($s_gsr_id)) ? null : $s_df_id;
                if ((is_array($this->input->post('accountno'))) AND (count($this->input->post('accountno')) > 0)) {
                    $a_return = $this->Drm->submit_df($this->input->post());
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

    public function validate($number)
    {
        $number = str_replace(',', '', $number);
        if (preg_match("/^[0-9,]+$/", $number)) {
            return true;
        }
        else {
            $this->form_validation->set_message('validate', 'The {field} Must be a number');
            return false;
        }
    }

    public function get_gsr_for_df()
    {
        if ($this->input->is_ajax_request()) {
            $s_gsr_id = $this->input->post('data_id');

            $mba_details_data = $this->Grm->get_gsr_details(['gd.gsr_id' => $s_gsr_id]);
            $mba_attachment_ = $this->Grm->get_where('dt_gsr_attachment', ['gsr_id' => $s_gsr_id, 'gsr_show' => 'true']);
            print json_encode(['code' => 0, 'data_detail' => $mba_details_data, 'data_attach' => $mba_attachment_]);
        }
    }

    public function new_request($s_gsr_id = false)
    {
        $mba_department_list = $this->General->get_where('ref_department', ['employee_id' => $this->session->userdata('employee_id')]);
        if (!$mba_department_list){
            if ($this->access == 'not allowed') {
                show_403();
            }
            else {
                $mba_employee_data = $this->General->get_where('dt_employee', ['employee_id' => $this->session->userdata('employee_id')]);
                $mba_department_list = ($mba_employee_data) ? $this->General->get_where('ref_department', ['department_id' => $mba_employee_data[0]->department_id]) : false;
                if (!$mba_department_list) {
                    show_403();
                }
            }
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

    public function new_form($s_target_type = 'DF', $s_df_id = false)
    {
        $mba_department_list = $this->General->get_in('dt_employee', 'department_id', ['11', '12'], [
            'employee_id' => $this->session->userdata('employee_id')
        ]);

        if (!$mba_department_list){
            if ($this->access == 'not allowed') {
                show_403();
            }
            else {
                $mba_employee_data = $this->General->get_where('dt_employee', ['employee_id' => $this->session->userdata('employee_id')]);
                $mba_department_list = ($mba_employee_data) ? $this->General->get_where('ref_department', ['department_id' => $mba_employee_data[0]->department_id]) : false;
                if (!$mba_department_list) {
                    show_403();
                }
            }
        }

        if ($s_df_id) {
            $mba_df_data = $this->Grm->get_df_data(['dm.df_id' => $s_df_id]);
            if ($mba_df_data) {
                $this->a_page_data['df_main_data'] = $mba_df_data[0];
                $this->a_page_data['df_details_data'] = $this->Grm->get_df_details(['dd.df_id' => $s_df_id]);
                // $this->a_page_data['df_attachment_data'] = $this->Grm->get_gsr_attachment(['gsm.gsr_id' => $s_gsr_id]);
            }
        }

        $mba_userdata = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
        $mba_gsr_available = $this->Grm->get_gsr_data([
            'personal_data_id_approved != ' => NULL,
            'personal_data_id_finishing  != ' => NULL,
            'gsr_allow_update' => 'false'
        ], false, '9');

        // print('<pre>');var_dump($mba_gsr_available);exit;
        if ($mba_gsr_available) {
            foreach ($mba_gsr_available as $key => $o_gsr) {
                $mba_gsrin_df = $this->Grm->get_where('dt_df_main', ['gsr_id' => $o_gsr->gsr_id]);
                if ($mba_gsrin_df) {
                    unset($mba_gsr_available[$key]);
                }
            }
            $mba_gsr_available = array_values($mba_gsr_available);
        }

        $this->a_page_data['target_type'] = $s_target_type;
        $this->a_page_data['rector_name'] = ($mbo_rector_data = $this->General->get_rectorate('rector')) ? $mbo_rector_data->rector_full_name : '';
        $this->a_page_data['optional_df_number'] = $this->get_df_number();
        $this->a_page_data['account_list'] = $this->Grm->get_account_list();
        $this->a_page_data['bank_list'] = $this->General->get_where('ref_bank');
        $this->a_page_data['top'] = $this->Grm->get_enum_values('dt_df_main', 'df_top');
        $this->a_page_data['gsr_list'] = $mba_gsr_available;

        $this->a_page_data['type'] = $this->Grm->get_enum_values('dt_df_main', 'df_type');
        // print('<pre>');var_dump($this->a_page_data['unit_list']);exit;
        $this->a_page_data['request_name'] = $mba_userdata[0]->personal_data_name;
        $this->a_page_data['account_list'] = $this->Grm->get_account_list();
        $this->a_page_data['body'] = $this->load->view('gsr/form/form_df_request', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function view_attachment($s_key = false)
    {
        $s_key = base64_decode(urldecode($s_key));
        $a_key = explode('|', $s_key);
        // $s_key = APPPATH.''.base64_decode(urldecode($s_key));
        // print('<pre>');
        // var_dump($a_key);exit;
        if (!$s_key) {
            show_404();exit;
        }
        else if (count($a_key) <= 1) {
            show_404();exit;
        }
        else {
            $mba_gsr_file_data = $this->Grm->get_gsr_attachment(['gsa.gsr_file_id' => $a_key[0]]);
            if (!$mba_gsr_file_data) {
                show_404();exit;
            }
            
            $s_firts_char_user = substr($mba_gsr_file_data[0]->personal_data_name, 0, 1);
            $s_path  = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$mba_gsr_file_data[0]->personal_data_id_request.'/'.$mba_gsr_file_data[0]->document_link;
            if (!file_exists($s_path)) {
                show_404();exit;
            }

            $a_file = explode('/', $s_path);
            $s_filename = $a_file[count($a_file) -1];
            
            $s_mime = mime_content_type($s_path);
            header("Content-Type: ".$s_mime);
            header('Content-Disposition: inline; filename='.$mba_gsr_file_data[0]->document_name);
            readfile( $s_path );
            exit;
        }
    }
    
    public function view_df_detail($s_df_id)
    {
        if ($this->access == 'not allowed'){
            show_403();
        }

        $mbs_action = (isset($_GET['act'])) ? $_GET['act'] : null;
        $mba_df_data = $this->Drm->get_df_data(['dm.df_id' => $s_df_id]);
        if ($mba_df_data) {
            $o_df_data = $mba_df_data[0];
            // print('<pre>');var_dump($o_df_data);exit;
            $this->a_page_data['current_status'] = 'requested';
            if (!is_null($o_df_data->personal_data_id_finishing)) {
                $this->a_page_data['current_status'] = 'finish';
            }
            else if (!is_null($o_df_data->personal_data_id_approved)) {
                $this->a_page_data['current_status'] = 'approved';
            }
            else if (!is_null($o_df_data->personal_data_id_checked)) {
                $this->a_page_data['current_status'] = 'checked';
            }

            $df_requested = $this->Drm->get_df_status_log(['ds.df_id' => $s_df_id, 'ds.status_action_id' => '1']);
            $df_all_remarks = $this->Drm->get_remarks_list(['ds.df_id' => $s_df_id]);
            $s_firts_char_user = $df_requested->personal_data_name;
            $s_firts_char_user = substr($s_firts_char_user, 0, 1);

            $mba_attachment = $this->Grm->get_where('dt_gsr_attachment', ['gsr_id' => $o_df_data->gsr_id]);
            if ($mba_attachment) {
                foreach ($mba_attachment as $o_attachment) {
                    $s_path = $s_firts_char_user.'/'.$df_requested->personal_data_id.'/'.date('Y', strtotime($df_requested->df_date_created)).'/gsr/'.$o_df_data->df_number.'/';
                    $o_attachment->filedata = $s_path.$o_attachment->document_link;
                }
            }

            $this->a_page_data['df_type'] = ($o_df_data->df_type == 'Bank Receipt') ? 'RF' : 'DF';
            $this->a_page_data['with_action'] = $mbs_action;
            $this->a_page_data['df_data'] = $o_df_data;
            $this->a_page_data['df_attachment'] = $mba_attachment;
            $this->a_page_data['df_remarks'] = $df_all_remarks;
            $this->a_page_data['df_details'] = $this->Drm->get_df_details(['dd.df_id' => $s_df_id]);
            $this->a_page_data['df_request_data'] = $df_requested;
            $this->a_page_data['df_check_data'] = $this->Drm->get_df_status_log(['ds.df_id' => $s_df_id, 'rs.current_progress' => 'checked', 'rs.status_action' => 'approve']);
            $this->a_page_data['df_approve_data'] = $this->Drm->get_df_status_log(['ds.df_id' => $s_df_id, 'rs.current_progress' => 'approved', 'rs.status_action' => 'approve']);
            $this->a_page_data['df_finish_data'] = $this->Drm->get_df_status_log(['ds.df_id' => $s_df_id, 'rs.current_progress' => 'finish', 'rs.status_action' => 'approve']);
            // $this->a_page_data['gst_attachment']; = $this->Grm->get_attachment;

            $this->a_page_data['df_view'] = $this->load->view('gsr/misc/df_view', $this->a_page_data, true);
            $this->a_page_data['body'] = $this->load->view('gsr/misc/df_action', $this->a_page_data, true);
            // print('<pre>');var_dump($this->a_page_data);exit;
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    public function view_detail($s_gsr_id)
    {
        $s_access_page = $this->access;
        $mba_department_list = $this->General->get_where('ref_department', ['employee_id' => $this->session->userdata('employee_id')]);
        if ($s_access_page == 'not allowed'){
            show_403();
        }

        $mbs_action = (isset($_GET['act'])) ? $_GET['act'] : null;
        if (in_array($mbs_action, ['approve_finish', 'reject_finish'])) {
            $s_access_page = 'finish';
            $this->a_page_data['access'] = $s_access_page;
        }

        $mba_gsr_data = $this->Grm->get_gsr_data(['gm.gsr_id' => $s_gsr_id]);
        if ($mba_gsr_data) {
            $mbo_last_status = $this->Grm->get_gsr_status_log(['gs.gsr_id' => $s_gsr_id]);
            $gsr_requested = $this->Grm->get_gsr_status_log(['gs.gsr_id' => $s_gsr_id, 'rs.current_progress' => 'requested']);
            $gsr_all_remarks = $this->Grm->get_remarks_list(['gs.gsr_id' => $s_gsr_id]);
            $o_gsr_data = $mba_gsr_data[0];
            $s_firts_char_user = $gsr_requested->personal_data_name;
            $s_firts_char_user = substr($s_firts_char_user, 0, 1);

            $mba_gsr_attachment = $this->Grm->get_where('dt_gsr_attachment', ['gsr_id' => $s_gsr_id]);
            if ($mba_gsr_attachment) {
                foreach ($mba_gsr_attachment as $o_attachment) {
                    $s_path = $s_firts_char_user.'/'.$gsr_requested->personal_data_id.'/'.date('Y', strtotime($gsr_requested->gsr_last_date)).'/gsr/'.$o_gsr_data->gsr_code.'/';
                    $o_attachment->filedata = $s_path.$o_attachment->document_link;
                }
            }

            $this->a_page_data['status_info'] = $this->translate_status($mbo_last_status->status_action_id);
            $this->a_page_data['gsr_current_status'] = $mbo_last_status;
            $this->a_page_data['with_action'] = $mbs_action;
            $this->a_page_data['gsr_data'] = $o_gsr_data;
            $this->a_page_data['gsr_attachment'] = $mba_gsr_attachment;
            $this->a_page_data['gsr_remarks'] = $gsr_all_remarks;
            $this->a_page_data['gsr_details'] = $this->Grm->get_gsr_details(['gd.gsr_id' => $s_gsr_id]);
            $this->a_page_data['gsr_request_data'] = $gsr_requested;
            $this->a_page_data['gsr_review_data'] = $this->Grm->get_gsr_status_log(['gs.gsr_id' => $s_gsr_id, 'rs.current_progress' => 'reviewed', 'rs.status_action' => 'approve']);
            $this->a_page_data['gsr_approve_data'] = $this->Grm->get_gsr_status_log(['gs.gsr_id' => $s_gsr_id, 'rs.current_progress' => 'approved', 'rs.status_action' => 'approve']);
            $this->a_page_data['gsr_finish_data'] = $this->Grm->get_gsr_status_log(['gs.gsr_id' => $s_gsr_id, 'rs.current_progress' => 'finish', 'rs.status_action' => 'approve']);
            // $this->a_page_data['gst_attachment']; = $this->Grm->get_attachment;

            $this->a_page_data['gsr_view'] = $this->load->view('gsr/misc/gsr_view', $this->a_page_data, true);
            $this->a_page_data['body'] = $this->load->view('gsr/misc/gsr_action', $this->a_page_data, true);
            // print('<pre>');var_dump($this->a_page_data);exit;
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    public function force_finishing()
    {
        $s_gsr_id = '53096dc1-4856-4321-8fd6-c56d5009f6b7';
        // $prosess = $this->Grm->mail_for_finishing($s_gsr_id);
        // print('<pre>');var_dump($prosess);exit;
    }

    public function chart_list()
    {
        $allowed = ($this->access == 'not allowed') ? false : true;

        // print($this->access);exit;
        $mba_user_employee_data = $this->General->get_where('dt_employee', ['employee_id' => $this->session->userdata('employee_id')]);
        if ($mba_user_employee_data) {
            foreach ($mba_user_employee_data as $o_employee) {
                if (in_array($o_employee->department_id, ['11', '12'])) {
                    $allowed = true;
                }
            }
        }

        if (!$allowed) {
            show_403();
        }

        $this->a_page_data['account_list'] = $this->Grm->get_account_list();
        // $this->a_page_data['department'] = $this->General->get_where('ref_department');
        $this->a_page_data['body'] = $this->load->view('gsr/chart_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function request_list()
    {
        if ($this->access == 'not allowed') {
            show_403();
        }
        $this->a_page_data['department'] = $this->General->get_where('ref_department');
        $this->a_page_data['body'] = $this->load->view('gsr/request_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function df_list()
    {
        $this->a_page_data['department'] = $this->General->get_where('ref_department');
        $this->a_page_data['target_type'] = 'DF';
        $this->a_page_data['body'] = $this->load->view('gsr/df_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function rf_list()
    {
        $this->a_page_data['department'] = $this->General->get_where('ref_department');
        $this->a_page_data['target_type'] = 'RF';
        $this->a_page_data['body'] = $this->load->view('gsr/df_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
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

    public function get_list_dfrf()
    {
        if ($this->input->is_ajax_request()) {
            $s_df_type = ($this->input->post('df_type') == 'DF') ? 'Bank Disbursement' : 'Bank Receipt';
            $s_datestart = $this->input->post('df_daterange_start');
            $s_dateend = $this->input->post('df_daterange_end');
            $s_dfno = $this->input->post('df_no');
            $s_gsrcode = $this->input->post('gsr_code');
            $s_department_id = $this->input->post('department_id');
            $a_filter_data = [
                'dm.df_type' => $s_df_type
            ];

            $a_like_clause = false;

            if (!empty($s_datestart)) {
                $a_filter_data['dm.df_date_created >='] = date('Y-m-d 00:00:00', strtotime($s_datestart));
            }
            if (!empty($s_dateend)) {
                $a_filter_data['dm.df_date_created <='] = date('Y-m-d 23:59:59', strtotime($s_dateend));
            }

            if ((!empty($s_dfno)) OR (!empty($s_gsrcode))) {
                $a_like_clause = [];
            }

            if (!empty($s_dfno)) {
                $a_like_clause['dm.df_number'] = $s_dfno;
            }
            if (!empty($s_gsrcode)) {
                $a_like_clause['gm.gsr_code'] = $s_gsrcode;
            }

            $mba_data = $this->Drm->get_df_data($a_filter_data, $a_like_clause);
            // print('<pre>');var_dump($mba_data);exit;
            if ($mba_data) {
                foreach ($mba_data as $o_data) {
                    $mba_list_status = $this->Drm->get_df_status_log(['dm.df_id' => $o_data->df_id]);
                    $o_data->current_status = 'requested';

                    if ($mba_list_status) {
                        $progress = trim($mba_list_status->current_progress, "ed");
                        // $o_data->current_status = $mba_list_status->status_action;
                        // $o_data->current_status = $mba_list_status->status_action.' '.$mba_list_status->current_progress;
                        $o_data->current_status = $progress.' '.$mba_list_status->status_action_id;
                    }
                    // if (!is_null($o_data->personal_data_id_review)) {
                    //     $o_data->current_status = 'reviewed';
                    // }
                    // else if (!is_null($o_data->personal_data_id_approved)) {
                    //     $o_data->current_status = 'approved';
                    // }
                    // else if (!is_null($o_data->personal_data_id_finishing)) {
                    //     $o_data->current_status = 'finished';
                    // }
                    
                    $mba_user_request = $this->Emm->get_employee_data(['em.personal_data_id' => $o_data->personal_data_id_request]);
                    $mba_user_checked = $this->Emm->get_employee_data(['em.personal_data_id' => $o_data->personal_data_id_checked]);
                    $mba_user_approve = $this->Emm->get_employee_data(['em.personal_data_id' => $o_data->personal_data_id_approved]);
                    $mba_user_finish = $this->Emm->get_employee_data(['em.personal_data_id' => $o_data->personal_data_id_finishing]);

                    $o_data->user_request = ($mba_user_request) ? $mba_user_request[0]->personal_data_name : '';
                    $o_data->user_check = ($mba_user_checked) ? $mba_user_checked[0]->personal_data_name : '';
                    $o_data->user_approve = ($mba_user_approve) ? $mba_user_approve[0]->personal_data_name : '';
                    $o_data->user_finish = ($mba_user_finish) ? $mba_user_finish[0]->personal_data_name : '';
                }
            }

            print json_encode(['data' => $mba_data]);
        }
    }

    public function get_list_request()
    {
        if ($this->input->is_ajax_request()) {
            $mba_data = false;
            $b_show_all = false;
            $mba_department_list = $this->General->get_where('ref_department', ['employee_id' => $this->session->userdata('employee_id')]);
            if ($mba_department_list) {
                $a_department_id = [];
                foreach ($mba_department_list as $o_department) {
                    if (!in_array($o_department->department_id, $a_department_id)) {
                        array_push($a_department_id, $o_department->department_id);
                    }
                }

                if (in_array($this->access, $this->special_access)) {
                    if (!empty($this->input->post('check_department'))) {
                        if (($this->input->post('department_id') == '') OR ($this->input->post('department_id') == 'all')) {
                            $b_show_all = true;
                        }
                        else {
                            $a_department_id = [$this->input->post('department_id')];
                        }
                    }
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
                        $mba_list_status = $this->Grm->get_gsr_status_log(['gs.gsr_id' => $o_data->gsr_id]);
                        $o_data->current_status = 'requested';

                        if ($mba_list_status) {
                            $o_data->current_status = $this->translate_status($mba_list_status->status_action_id);
                        }
                        
                        $mba_user_request = $this->Emm->get_employee_data(['em.personal_data_id' => $o_data->personal_data_id_request]);
                        $mba_user_review = $this->Emm->get_employee_data(['em.personal_data_id' => $o_data->personal_data_id_review]);
                        $mba_user_approve = $this->Emm->get_employee_data(['em.personal_data_id' => $o_data->personal_data_id_approved]);
                        $mba_user_finish = $this->Emm->get_employee_data(['em.personal_data_id' => $o_data->personal_data_id_finishing]);

                        $o_data->user_request = ($mba_user_request) ? $mba_user_request[0]->personal_data_name : '';
                        $o_data->user_review = ($mba_user_review) ? $mba_user_review[0]->personal_data_name : '';
                        $o_data->user_approve = ($mba_user_approve) ? $mba_user_approve[0]->personal_data_name : '';
                        $o_data->user_finish = ($mba_user_finish) ? $mba_user_finish[0]->personal_data_name : '';

                        $o_data->total_items = 0;
                        $o_data->total_price = 0;
                    }
                }
            }
            print json_encode(['data' => $mba_data]);
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
            // print($s_gsr_number);
        }
    }

    public function get_df_number()
    {   
        $i_df_number = 1;
        $i_year = date('Y');
        $get_df_year = $this->Grm->get_where('dt_df_main', ['YEAR(df_date_created)' => $i_year]);
        if ($get_df_year) {
            $i_df_number = $this->Grm->get_last_number_df($i_year);
            $i_df_number = substr($i_df_number, (strlen($i_df_number) - 4));
        }
        // print($i_df_number);exit;

        $s_df_number = substr($i_year, 1, 3).str_pad($i_df_number, 4, "0", STR_PAD_LEFT);

        if ($this->input->is_ajax_request()) {
            print json_encode(['gsr_number' => $s_df_number]);
        }else{
            return $s_df_number;
            // print($s_df_number);
        }
    }

    public function translate_status($s_status_action_id)
    {
        $s_status = '';
        switch ($s_status_action_id) {
            case '1':
                $s_status = 'Requested';
                break;

            case '2':
                $s_status = 'Review Rejected';
                break;

            case '3':
                $s_status = 'Review Approved';
                break;

            case '4':
                $s_status = 'Check Rejected';
                break;

            case '5':
                $s_status = 'Check Approved';
                break;

            case '6':
                $s_status = 'Approval Recorate Rejected';
                break;

            case '7':
                $s_status = 'Approval Recorate Approved';
                break;

            case '8':
                $s_status = 'Approval Foundation Rejected';
                break;

            case '9':
                $s_status = 'Approval Foundation Approved';
                break;
            
            default:
                break;
        }

        return $s_status;
    }

    private function _get_access()
    {
        $s_access = 'not allowed';
        $s_personal_data_id = $this->session->userdata('user');
        $s_employee_id = $this->session->userdata('employee_id');
        $a_config_allowed_access = $this->config->item('allowed_page');
        $a_config_allowed_access = $a_config_allowed_access['gsr_page'];
        $mba_user_head_department = $this->General->get_where('ref_department', ['employee_id' => $s_employee_id]);
        
        if ($mba_user_head_department) {
            $s_access = 'request';
        }
        else if (in_array($s_personal_data_id, $a_config_allowed_access)) {
            $s_access = 'request';
        }

        if ($s_access == 'request') {
            $mba_is_reviewer = $this->General->get_where('ref_department', ['department_id' => '11', 'employee_id' => $s_employee_id]);
            $mba_is_approver = $this->General->get_where('ref_department', ['department_id' => '6', 'employee_id' => $s_employee_id]);
            $mba_is_finishing = $this->General->get_where('ref_department', ['department_id' => '47', 'employee_id' => $s_employee_id]);

            if ($mba_is_approver) {
                $s_access = 'approve';
            }
            else if ($mba_is_reviewer) {
                $s_access = 'review';
            }
            else if ($mba_is_finishing) {
                $s_access = 'finish';
            }
        }

        return $s_access;
    }
}
