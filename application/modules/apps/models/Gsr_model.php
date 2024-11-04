<?php
class Gsr_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->grm = $this->load->database('gsr_db', true);
    }

    function force_update($s_gsr_table, $a_update_data, $a_update_clause) {
        $this->grm->update($s_gsr_table, $a_update_data, $a_update_clause);
        return true;
    }

    function save_account_list($a_data) {
        $this->grm->insert('dt_account_list', $a_data);
        return ($this->grm->affected_rows() > 0) ? true : false;
    }

    public function save_gsr_attachment($a_data, $s_gsr_file_id = false)
    {
        if ($s_gsr_file_id) {
            $this->grm->update('dt_gsr_attachment', $a_data, ['gsr_file_id' => $s_gsr_file_id]);
            return true;
        }
        else {
            $this->grm->insert('dt_gsr_attachment', $a_data);
            return ($this->grm->affected_rows() > 0) ? true : false;
        }
    }

    public function get_remarks_list($a_clause = false)
    {
        $this->db->select("*, gn.date_added AS 'gsr_date_remarks'");
        $this->db->from('portal_gsr.dt_gsr_note gn');
        $this->db->join('portal_gsr.dt_gsr_status gs', 'gs.status_id = gn.status_id');
        $this->db->join('portal_gsr.ref_status_action rs', 'rs.status_action_id = gs.status_action_id');
        $this->db->join('portal_gsr.dt_gsr_main gm', 'gm.gsr_id = gs.gsr_id');
        $this->db->join('portal_main.dt_personal_data pd', 'pd.personal_data_id = gs.personal_data_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('gn.date_added', 'ASC');

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function submit_review($a_gsr_update_data, $a_gsr_status_data, $a_note_data, $s_gsr_id)
    {
        $this->grm->trans_begin();

        $this->grm->update('dt_gsr_main', $a_gsr_update_data, ['gsr_id' => $s_gsr_id]);
        $this->grm->insert('dt_gsr_status', $a_gsr_status_data);

        if (count($a_note_data) > 0) {
            foreach ($a_note_data as $a_note) {
                $this->grm->insert('dt_gsr_note', $a_note);
            }
        }

        if ($this->grm->trans_status() === false) {
            $this->grm->trans_rollback();
            $a_return = ['code' => 1, 'message' => 'Failed response process!'];
        }
        else {
            $this->grm->trans_commit();
            $a_return = ['code' => 0, 'message' => 'Success'];

            if ($a_gsr_status_data['status_action_id'] == '3') {
                $a_return = $this->mail_for_approved($s_gsr_id);
                if ($a_return['code'] != 0) {
                    $a_return = ['code' => 0, 'message' => 'Success submit GSR but Failed sending notification for approve!\n'.$a_return['message']];
                }
            }
            else {
                $a_return = $this->mail_for_reject($s_gsr_id);
                if ($a_return['code'] != 0) {
                    $a_return = ['code' => 0, 'message' => 'Success submit GSR but Failed sending notification for reject!\n'.$a_return['message']];
                }
            }
        }
        
        return $a_return;
    }

    public function submit_approve($a_gsr_update_data, $a_gsr_status_data, $a_note_data, $s_gsr_id)
    {
        // print('<pre>');var_dump($a_gsr_update_data);exit;
        $this->grm->trans_begin();

        $this->grm->update('dt_gsr_main', $a_gsr_update_data, ['gsr_id' => $s_gsr_id]);
        $this->grm->insert('dt_gsr_status', $a_gsr_status_data);

        if (count($a_note_data) > 0) {
            foreach ($a_note_data as $a_note) {
                $this->grm->insert('dt_gsr_note', $a_note);
            }
        }

        if ($this->grm->trans_status() === false) {
            $this->grm->trans_rollback();
            $a_return = ['code' => 1, 'message' => 'Failed response process!'];
        }
        else {
            $this->grm->trans_commit();
            $a_return = ['code' => 0, 'message' => 'Success'];

            if ($a_gsr_status_data['status_action_id'] == '7') {
                // $a_return = $this->mail_for_finishing($s_gsr_id);
                // if ($a_return['code'] != 0) {
                //     $a_return = ['code' => 0, 'message' => 'Success submit GSR but Failed sending notification for approve!\n'.$a_return['message']];
                // }
                $a_return = $this->mail_finish_gsr($s_gsr_id);
                if ($a_return['code'] != 0) {
                    $a_return = ['code' => 0, 'message' => 'Success submit GSR but Failed sending notification\n'.$a_return['message']];
                }
            }
            else {
                $a_return = $this->mail_for_reject($s_gsr_id, 'approve');
                if ($a_return['code'] != 0) {
                    $a_return = ['code' => 0, 'message' => 'Success submit GSR but Failed sending notification for reject!\n'.$a_return['message']];
                }
            }
        }
        
        return $a_return;
    }

    public function submit_finish($a_gsr_update_data, $a_gsr_status_data, $a_note_data, $s_gsr_id)
    {
        // print('<pre>');var_dump($a_gsr_update_data);exit;
        $this->grm->trans_begin();

        $this->grm->update('dt_gsr_main', $a_gsr_update_data, ['gsr_id' => $s_gsr_id]);
        $this->grm->insert('dt_gsr_status', $a_gsr_status_data);

        if (count($a_note_data) > 0) {
            foreach ($a_note_data as $a_note) {
                $this->grm->insert('dt_gsr_note', $a_note);
            }
        }

        if ($this->grm->trans_status() === false) {
            $this->grm->trans_rollback();
            $a_return = ['code' => 1, 'message' => 'Failed response process!'];
        }
        else {
            $this->grm->trans_commit();
            $a_return = ['code' => 0, 'message' => 'Success'];

            if ($a_gsr_status_data['status_action_id'] == '9') {
                $a_return = $this->mail_finish_gsr($s_gsr_id);
                if ($a_return['code'] != 0) {
                    $a_return = ['code' => 0, 'message' => 'Success submit GSR but Failed sending notification for approve!\n'.$a_return['message']];
                }
            }
            else {
                $a_return = $this->mail_for_reject($s_gsr_id, 'finish');
                if ($a_return['code'] != 0) {
                    $a_return = ['code' => 0, 'message' => 'Success submit GSR but Failed sending notification for reject!\n'.$a_return['message']];
                }
            }
        }
        
        return $a_return;
    }

    public function get_gsr_attachment($a_clause = false, $b_show = 'gsr')
    {
        $this->db->from('portal_gsr.dt_gsr_attachment gsa');
        $this->db->join('portal_gsr.dt_gsr_main gsm', 'gsm.gsr_id = gsa.gsr_id');
        $this->db->join('portal_main.dt_personal_data pd', 'pd.personal_data_id = gsm.personal_data_id_request');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($b_show == 'gsr') {
            $this->db->where('gsa.gsr_show', 'true');
        }
        else if ($b_show == 'df') {
            $this->db->where('gsa.df_show', 'true');
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function submit_gsr($post)
    {
        $mbo_rector_data = $this->General->get_rectorate('vice_rector');
        $mbo_finance_data = $this->General->get_head_department('FIN');
        $mbo_user_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
        
        // print('<pre>');var_dump($post['fileattach']);exit;
        // print('<pre>');var_dump($post);exit;
        $this->grm->trans_begin();
        $s_total_amount = str_replace(',', '', $post['total_amount']);
        $s_total_amount = str_replace('.', '', $s_total_amount);

        $a_main_data = [
            'gsr_code' => $post['gsr_code'],
            'personal_data_id_request' => $this->session->userdata('user'),
            'personal_data_id_review' => NULL,
            'personal_data_id_approved' => NULL,
            'personal_data_id_finishing' => NULL,
            'gsr_reviewed' => NULL,
            'gsr_approved' => NULL,
            'gsr_finished' => NULL,
            'account_no' => $post['booking_code'],
            'department_id' => $post['department'],
            'gsr_number' => intval(substr($post['gsr_code'], 3, 3)),
            'gsr_date_request' => date('Y-m-d H:i:s'),
            'gsr_budget_proposal_number' => $post['budget'],
            'gsr_activity' => $post['activity_no'],
            'gsr_total_amount' => $s_total_amount,
            'gsr_total_amount_text' => $post['amount_speeling_input'],
            'gsr_allow_update' => 'false'
        ];

        if ((array_key_exists('gsr_update_key', $post)) AND (!empty($post['gsr_update_key']))) {
            $s_gsr_id = $post['gsr_update_key'];

            $this->grm->update('dt_gsr_main', $a_main_data, ['gsr_id' => $s_gsr_id]);
            $this->grm->delete('dt_gsr_details', ['gsr_id' => $s_gsr_id]);
        }
        else {
            $s_gsr_id = $this->uuid->v4();
            $a_main_data['gsr_id'] = $s_gsr_id;

            $this->grm->insert('dt_gsr_main', $a_main_data);
        }

        // print('<pre>');var_dump($post);exit;
        $s_status_id = $this->uuid->v4();
        $a_log_status_data = [
            'status_id' => $s_status_id,
            'gsr_id' => $s_gsr_id,
            'personal_data_id' => $this->session->userdata('user'),
            'status_action_id' => '1',
            'date_added' => date('Y-m-d H:i:s')
        ];

        $this->grm->insert('dt_gsr_status', $a_log_status_data);

        if (!empty($post['gsr_remarks'])) {
            $s_remark = trim($post['gsr_remarks']);

            $a_note_data = [
                'note_id' => $this->uuid->v4(),
                'status_id' => $s_status_id,
                'personal_data_id' => $this->session->userdata('user'),
                'note' => $s_remark,
                'date_added' => date('Y-m-d H:i:s')
            ];

            $this->grm->insert('dt_gsr_note', $a_note_data);
        }

        for ($i = 0; $i < count($post['description']); $i++) { 
            $s_unit_price = str_replace(',', '', $post['unitprice'][$i]);
            $s_unit_price = str_replace('.', '', $s_unit_price);
            $s_amount = str_replace(',', '', $post['amount'][$i]);
            $s_amount = str_replace('.', '', $s_amount);

            $a_details_data = [
                'gsr_details_id' => $this->uuid->v4(),
                'gsr_id' => $s_gsr_id,
                'gsr_details_description' => $post['description'][$i],
                'gsr_details_activity_id' => $post['activityno'][$i],
                'gsr_details_qty' => $post['quantity'][$i],
                'unit_id' => $post['unit'][$i],
                'gsr_details_price' => $s_unit_price,
                'gsr_details_total_price' => $s_amount,
                'gsr_details_total_price_text' => $post['amountspell'][$i],
                'gsr_details_remarks' => $post['remarks'][$i],
                'date_added' => date('Y-m-d H:i:s')
            ];

            $this->grm->insert('dt_gsr_details', $a_details_data);
        }

        $mba_have_attachment = $this->grm->get_where('dt_gsr_attachment', ['gsr_id' => $s_gsr_id]);
        if ($mba_have_attachment->num_rows() > 0) {
            $mba_have_attachment = $mba_have_attachment->result();
            if ((isset($post['fileattach'])) AND (!is_null($post['fileattach']))) {
                $a_attach_id = [];
                foreach ($mba_have_attachment as $o_attachment_exists) {
                    if (!in_array($o_attachment_exists->gsr_file_id, $post['fileattach'])) {
                        $this->grm->delete('dt_gsr_attachment', ['gsr_file_id' => $o_attachment_exists->gsr_file_id]);
                    }
                }
            }
            else {
                $this->grm->delete('dt_gsr_attachment', ['gsr_id' => $s_gsr_id]);
            }
        }

        $mbs_attach_has_error = false;
        if (count($_FILES) > 0) {
            $a_error_upload = [];
            $s_firts_char_user = substr($mbo_user_data[0]->personal_data_name, 0, 1);
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$this->session->userdata('user').'/'.date('Y').'/gsr/'.str_replace(' ', '_', $post['gsr_code']).'/';
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $config['allowed_types'] = 'pdf|jpg|jpeg|png|bmp|doc|docx|xls|xlsx|ppt|pptx|csv';
            $config['max_size'] = 204800;
            $config['file_ext_tolower'] = true;
            $config['overwrite'] = true;
            $config['upload_path'] = $s_file_path;
            $this->load->library('upload', $config);

            foreach ($_FILES as $s_key => $a_value) {
                if (!empty($a_value['name'])) {
                    $s_fname = md5(json_encode($a_value));
                    $config['file_name'] = $s_fname;
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload($s_key)) {
                        $a_gsr_attachment_data= [
                            'gsr_file_id' => $this->uuid->v4(),
                            'gsr_id' => $s_gsr_id,
                            'document_link' => date('Y').'/gsr/'.str_replace(' ', '_', $post['gsr_code']).'/'.$this->upload->data('file_name'),
                            'document_name' => $a_value['name'],
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        $this->grm->insert('dt_gsr_attachment', $a_gsr_attachment_data);
                    }
                    else {
                        array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>'));
                    }
                }
            }

            if (count($a_error_upload) > 0) {
                $mbs_attach_has_error = implode(', ', $a_error_upload);
            }
        }

        if ($mbs_attach_has_error) {
            $this->grm->trans_rollback();
            $a_return = ['code' => 1, 'message' => $mbs_attach_has_error];
        }
        else if ($this->grm->trans_status() === false) {
            $this->grm->trans_rollback();
            $a_return = ['code' => 1, 'message' => 'Failed response process!'];
        }
        else {
            $this->grm->trans_commit();
            $a_return = $this->mail_for_review($s_gsr_id);
            // $a_return = $this->mail_for_approved($s_gsr_id);
            if ($a_return['code'] != 0) {
                $a_return = ['code' => 2, 'message' => 'Failed to send notification for review!\n'.$a_return['message']];
            }
        }

        return $a_return;
    }

    public function mail_for_reject($s_gsr_id, $s_reject_from = 'review')
    {
        $mbo_gsr_data = $this->grm->get_where('dt_gsr_main', ['gsr_id' => $s_gsr_id])->first_row();

        $mbo_finance_data = $this->General->get_head_department('FIN');
        $mbo_approver_data = $this->General->get_head_department('VREC');
        // $mbo_approver_data = $this->General->get_head_department('HRD');
        $mbo_finishing_data = $this->General->get_head_department('Y-IULI');
        
        $mba_gsr_request_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mbo_gsr_data->personal_data_id_request]);

        $mba_have_attachment = $this->grm->get_where('dt_gsr_attachment', ['gsr_id' => $s_gsr_id, 'gsr_show' => 'true']);
        $mbo_user_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
        
        $mba_request_data = $this->General->get_where('dt_employee', ['personal_data_id' => $mbo_gsr_data->personal_data_id_request]);
        $s_firts_char_user = substr($mba_gsr_request_data[0]->personal_data_name, 0, 1);

        if ($s_reject_from == 'review') {
            $email_data['pidg'] = $mbo_gsr_data->personal_data_id_review;
            $email_data['s_user_reject'] = ucwords(strtolower($mbo_finance_data->head_full_name));
        }
        else if ($s_reject_from == 'approve') {
            $email_data['pidg'] = $mbo_gsr_data->personal_data_id_approved;
            $email_data['s_user_reject'] = ucwords(strtolower($mbo_approver_data->head_full_name));
        }
        else if ($s_reject_from == 'finish') {
            $email_data['pidg'] = $mbo_gsr_data->personal_data_id_finishing;
            $email_data['s_user_reject'] = ucwords(strtolower($mbo_finishing_data->head_full_name));
        }

        $email_data['pidt'] = $mba_gsr_request_data[0]->personal_data_id;
        $email_data['sid'] = $s_gsr_id;
        $email_data['param_link'] = str_replace('=', '', base64_encode(json_encode($email_data)));
        $email_data['s_user_request'] = ucwords(strtolower($mba_gsr_request_data[0]->personal_data_name));
        $email_data['key_code'] = strtoupper($mbo_gsr_data->gsr_code);

        $s_body_mail = $this->load->view('gsr/misc/message_reject', $email_data, true);
        $config['mailtype'] = 'html';
        $this->email->initialize($config);

        $s_gsr_path  = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$mbo_gsr_data->personal_data_id_request.'/'.date('Y', strtotime($mbo_gsr_data->gsr_date_request)).'/gsr/'.str_replace(' ', '_', $mbo_gsr_data->gsr_code).'/';
        // $s_gsr_name = str_replace(' ', '_', $mbo_gsr_data->gsr_code).'.pdf';
        // if (file_exists($s_gsr_path.$s_gsr_name)) {
        //     $this->email->attach($s_gsr_path.$s_gsr_name);
        // }

        if ($mba_have_attachment->num_rows() > 0) {
            $mba_have_attachment = $mba_have_attachment->result();
            foreach ($mba_have_attachment as $o_attachment) {
                $a_filedata = explode('/', $o_attachment->document_link);
                $s_filename = $a_filedata[count($a_filedata) - 1];
                $this->email->attach($s_gsr_path.$s_filename, 'attachment', $o_attachment->document_name);
            }
        }
        $this->email->from('employee@company.ac.id', '[IULI-SYSTEM] NO REPLY');
        $this->email->to($mba_request_data[0]->employee_email);
        // if ($s_reject_from == 'approve') {
        //     $this->email->cc([$mbo_finance_data->employee_email]);
        // }
        // else if ($s_reject_from == 'finish') {
        //     $this->email->cc([$mbo_finance_data->employee_email,$mbo_approver_data->employee_email]);
        // }
        
        // $this->email->to('employee@company.ac.id');
        $this->email->subject('[GSR-Notification] Need Action!');
        $this->email->message($s_body_mail);

        if (!$this->email->send()) {
            $a_return = ['code' => 1, 'message' => 'Failed send notification for approval'];
            log_message('error', 'ERROR send notification '.__FILE__.' '.__LINE__);
            $this->email->from('employee@company.ac.id');
            $this->email->to(array('employee@company.ac.id'));
            $this->email->subject('ERROR send notification for approve');
            $this->email->message('GSR_ID:'.$s_gsr_id.'||'.json_encode($email_data));
            $this->email->send();
        }
        else {
            $a_return = ['code' => 0, 'message' => 'Success'];
        }
        
        return $a_return;
    }

    public function mail_for_approved($s_gsr_id)
    {
        $generate_file = modules::run('download/pdf_download/generate_gsr_file', $s_gsr_id);
        if ($generate_file['code'] == 0) {
            $mbo_finance_data = $this->General->get_head_department('FIN');
            $mbo_approver_data = $this->General->get_head_department('VREC');
            // $mbo_approver_data = $this->General->get_head_department('HRD');
            $mbo_gsr_data = $this->grm->get_where('dt_gsr_main', ['gsr_id' => $s_gsr_id])->first_row();
            $mba_have_attachment = $this->grm->get_where('dt_gsr_attachment', ['gsr_id' => $s_gsr_id, 'gsr_show' => 'true']);
            $mbo_user_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
            $mba_gsr_request_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mbo_gsr_data->personal_data_id_request]);
            $mba_request_data = $this->General->get_where('dt_employee', ['personal_data_id' => $mbo_gsr_data->personal_data_id_request]);
            $s_firts_char_user = substr($mba_gsr_request_data[0]->personal_data_name, 0, 1);

            // kirim email ke head GSR
            $email_data['pidt'] = $mbo_approver_data->personal_data_id;
            $email_data['pidg'] = $mbo_gsr_data->personal_data_id_request;
            $email_data['sid'] = $s_gsr_id;
            $email_data['param_link'] = str_replace('=', '', base64_encode(json_encode($email_data)));
            $email_data['s_user_request'] = ucwords(strtolower($mba_gsr_request_data[0]->personal_data_name));
            $email_data['s_approve_name'] = ucwords(strtolower($mbo_approver_data->head_full_name));
            $email_data['gsr_code'] = strtoupper($mbo_gsr_data->gsr_code);
            $s_body_mail = $this->load->view('gsr/misc/message_approval_rectorate', $email_data, true);
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->email->clear(TRUE);

            $s_gsr_path  = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$mbo_gsr_data->personal_data_id_request.'/'.date('Y', strtotime($mbo_gsr_data->gsr_date_request)).'/gsr/'.str_replace(' ', '_', $mbo_gsr_data->gsr_code).'/';
            // $s_gsr_name = str_replace(' ', '_', $mbo_gsr_data->gsr_code).'.pdf';
            // if (file_exists($s_gsr_path.$s_gsr_name)) {
            //     $this->email->attach($s_gsr_path.$s_gsr_name);
            // }

            if ($mba_have_attachment->num_rows() > 0) {
                $mba_have_attachment = $mba_have_attachment->result();
                foreach ($mba_have_attachment as $o_attachment) {
                    $a_filedata = explode('/', $o_attachment->document_link);
                    $s_filename = $a_filedata[count($a_filedata) - 1];
                    $this->email->attach($s_gsr_path.$s_filename, 'attachment', $o_attachment->document_name);
                }
            }
            $this->email->from('employee@company.ac.id', '[IULI-SYSTEM] NO REPLY');
            $this->email->to($mbo_approver_data->employee_email);
            // $this->email->cc([$mbo_finance_data->employee_email,$mba_request_data[0]->employee_email]);
            // $this->email->to('employee@company.ac.id');
            $this->email->subject('[GSR-Notification] Need Action!');
            $this->email->message($s_body_mail);

            if (!$this->email->send()) {
                $generate_file = ['code' => 1, 'message' => 'Failed send notification for approval'];
                log_message('error', 'ERROR send notification '.__FILE__.' '.__LINE__);
                $this->email->from('employee@company.ac.id');
                $this->email->to(array('employee@company.ac.id'));
                $this->email->subject('ERROR send notification for approve');
                $this->email->message('GSR_ID:'.$s_gsr_id.'||'.json_encode($email_data));
                $this->email->send();
            }
        }
        
        return $generate_file;
    }

    public function mail_for_finishing($s_gsr_id)
    {
        $generate_file = modules::run('download/pdf_download/generate_gsr_file', $s_gsr_id);
        if ($generate_file['code'] == 0) {
            $mbo_finance_data = $this->General->get_head_department('FIN');
            $mbo_approver_data = $this->General->get_head_department('VREC');
            // $mbo_approver_data = $this->General->get_head_department('HRD');
            $mbo_finishing_data = $this->General->get_head_department('Y-IULI');
            $mbo_gsr_data = $this->grm->get_where('dt_gsr_main', ['gsr_id' => $s_gsr_id])->first_row();
            $mba_have_attachment = $this->grm->get_where('dt_gsr_attachment', ['gsr_id' => $s_gsr_id, 'gsr_show' => 'true']);
            $mbo_user_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
            $mba_gsr_request_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mbo_gsr_data->personal_data_id_request]);
            $mba_request_data = $this->General->get_where('dt_employee', ['personal_data_id' => $mbo_gsr_data->personal_data_id_request]);
            $s_firts_char_user = substr($mba_gsr_request_data[0]->personal_data_name, 0, 1);

            // kirim email ke head GSR
            $email_data['pidt'] = $mbo_finishing_data->personal_data_id;
            $email_data['pidg'] = $mbo_gsr_data->personal_data_id_request;
            $email_data['sid'] = $s_gsr_id;
            $email_data['param_link'] = str_replace('=', '', base64_encode(json_encode($email_data)));
            $email_data['s_user_request'] = ucwords(strtolower($mba_gsr_request_data[0]->personal_data_name));
            $email_data['s_approve_name'] = ucwords(strtolower($mbo_finishing_data->head_full_name));
            $email_data['gsr_code'] = strtoupper($mbo_gsr_data->gsr_code);
            $s_body_mail = $this->load->view('gsr/misc/message_approval_foundation', $email_data, true);
            $config['mailtype'] = 'html';
            $this->email->initialize($config);

            $s_gsr_path  = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$mbo_gsr_data->personal_data_id_request.'/'.date('Y', strtotime($mbo_gsr_data->gsr_date_request)).'/gsr/'.str_replace(' ', '_', $mbo_gsr_data->gsr_code).'/';
            // $s_gsr_name = str_replace(' ', '_', $mbo_gsr_data->gsr_code).'.pdf';
            // if (file_exists($s_gsr_path.$s_gsr_name)) {
            //     $this->email->attach($s_gsr_path.$s_gsr_name);
            // }

            if ($mba_have_attachment->num_rows() > 0) {
                $mba_have_attachment = $mba_have_attachment->result();
                foreach ($mba_have_attachment as $o_attachment) {
                    $a_filedata = explode('/', $o_attachment->document_link);
                    $s_filename = $a_filedata[count($a_filedata) - 1];
                    $this->email->attach($s_gsr_path.$s_filename, 'attachment', $o_attachment->document_name);
                }
            }
            $this->email->from('employee@company.ac.id', '[IULI-SYSTEM] NO REPLY');
            $this->email->to($mbo_finishing_data->employee_email);
            // $this->email->to('employee@company.ac.id');
            $this->email->bcc('employee@company.ac.id');
            $this->email->subject('[GSR-Notification] Need Action!');
            $this->email->message($s_body_mail);

            if (!$this->email->send()) {
                $generate_file = ['code' => 1, 'message' => 'Failed send notification for approval'];
                log_message('error', 'ERROR send notification '.__FILE__.' '.__LINE__);
                $this->email->from('employee@company.ac.id');
                $this->email->to(array('employee@company.ac.id'));
                $this->email->subject('ERROR send notification for approve');
                $this->email->message('GSR_ID:'.$s_gsr_id.'||'.json_encode($email_data));
                $this->email->send();
            }
        }
        
        return $generate_file;
    }

    public function mail_finish_gsr($s_gsr_id)
    {
        $generate_file = modules::run('download/pdf_download/generate_gsr_file', $s_gsr_id);
        if ($generate_file['code'] == 0) {
            $s_action_user = $this->General->retrieve_title($this->session->userdata('user'));
            $mbo_finance_data = $this->General->get_head_department('FIN');
            $mbo_approver_data = $this->General->get_head_department('VREC');
            // $mbo_approver_data = $this->General->get_head_department('HRD');
            // $mbo_finishing_data = $this->General->get_head_department('Y-IULI');
            $mbo_gsr_data = $this->grm->get_where('dt_gsr_main', ['gsr_id' => $s_gsr_id])->first_row();
            $mba_have_attachment = $this->grm->get_where('dt_gsr_attachment', ['gsr_id' => $s_gsr_id, 'gsr_show' => 'true']);
            $mbo_user_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
            $mba_gsr_request_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mbo_gsr_data->personal_data_id_request]);
            $mba_request_data = $this->General->get_where('dt_employee', ['personal_data_id' => $mbo_gsr_data->personal_data_id_request]);
            $s_firts_char_user = substr($mba_gsr_request_data[0]->personal_data_name, 0, 1);

            $mba_finance_list = $this->General->get_where('dt_employee', ['department_id' => $mbo_approver_data->department_id, 'status' => 'active']);

            // kirim email ke head GSR
            $email_data['pidt'] = $mbo_finance_data->personal_data_id;
            $email_data['pidg'] = $mbo_gsr_data->personal_data_id_request;
            $email_data['sid'] = $s_gsr_id;
            $email_data['dfrf_form'] = true;
            $email_data['param_link'] = str_replace('=', '', base64_encode(json_encode($email_data)));
            $email_data['s_user_finance'] = ucwords(strtolower($mbo_finance_data->personal_data_name));
            $email_data['s_finishing_name'] = $s_action_user;
            $email_data['gsr_code'] = strtoupper($mbo_gsr_data->gsr_code);
            $s_body_mail = $this->load->view('gsr/misc/message_finish_gsr', $email_data, true);
            $config['mailtype'] = 'html';
            $this->email->initialize($config);

            $s_gsr_path  = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$mbo_gsr_data->personal_data_id_request.'/'.date('Y', strtotime($mbo_gsr_data->gsr_date_request)).'/gsr/'.str_replace(' ', '_', $mbo_gsr_data->gsr_code).'/';
            // $s_gsr_name = str_replace(' ', '_', $mbo_gsr_data->gsr_code).'.pdf';
            // if (file_exists($s_gsr_path.$s_gsr_name)) {
            //     $this->email->attach($s_gsr_path.$s_gsr_name);
            // }

            $a_cc_mail = [
                $mbo_approver_data->employee_email,
                $mbo_finance_data->employee_email,
                $mba_request_data[0]->employee_email
            ];

            if ($mba_finance_list) {
                foreach ($mba_finance_list as $o_finance) {
                    if (!in_array($o_finance->employee_email, $a_cc_mail)) {
                        array_push($a_cc_mail, $o_finance->employee_email);
                    }
                }
            }

            if ($mba_have_attachment->num_rows() > 0) {
                $mba_have_attachment = $mba_have_attachment->result();
                foreach ($mba_have_attachment as $o_attachment) {
                    $a_filedata = explode('/', $o_attachment->document_link);
                    $s_filename = $a_filedata[count($a_filedata) - 1];
                    $this->email->attach($s_gsr_path.$s_filename, 'attachment', $o_attachment->document_name);
                }
            }
            
            $this->email->from('employee@company.ac.id', '[IULI-SYSTEM] NO REPLY');
            $this->email->to($mbo_finance_data->employee_email);
            // $this->email->cc($a_cc_mail);
            // $this->email->to('employee@company.ac.id');
            $this->email->bcc('employee@company.ac.id');
            $this->email->subject('[GSR-Notification] Need Action!');
            $this->email->message($s_body_mail);

            if (!$this->email->send()) {
                $generate_file = ['code' => 1, 'message' => 'Failed send notification for approval'];
                log_message('error', 'ERROR send notification '.__FILE__.' '.__LINE__);
                $this->email->from('employee@company.ac.id');
                $this->email->to(array('employee@company.ac.id'));
                $this->email->subject('ERROR send notification for approve');
                $this->email->message('GSR_ID:'.$s_gsr_id.'||'.json_encode($email_data));
                $this->email->send();
            }
        }
        
        return $generate_file;
    }

    public function mail_for_review($s_gsr_id)
    {
        $generate_file = modules::run('download/pdf_download/generate_gsr_file', $s_gsr_id);
        if ($generate_file['code'] == 0) {
            $mbo_finance_data = $this->General->get_head_department('FIN');
            $mbo_approver_data = $this->General->get_head_department('VREC');
            // $mbo_approver_data = $this->General->get_head_department('HRD');
            $mbo_gsr_data = $this->grm->get_where('dt_gsr_main', ['gsr_id' => $s_gsr_id])->first_row();
            $mba_have_attachment = $this->grm->get_where('dt_gsr_attachment', ['gsr_id' => $s_gsr_id, 'gsr_show' => 'true']);
            $mbo_user_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);

            // kirim email ke head finance
            $email_data['pidt'] = $mbo_finance_data->personal_data_id;
            $email_data['pidg'] = $this->session->userdata('user');
            $email_data['sid'] = $s_gsr_id;
            $email_data['param_link'] = str_replace('=', '', base64_encode(json_encode($email_data)));
            $email_data['s_user_request'] = ucwords(strtolower($mbo_user_data[0]->personal_data_name));
            $email_data['s_review_name'] = ucwords(strtolower($mbo_finance_data->head_full_name));
            $email_data['gsr_code'] = strtoupper($mbo_gsr_data->gsr_code);
            $s_body_mail = $this->load->view('gsr/misc/message_to_finance', $email_data, true);
            $config['mailtype'] = 'html';
    		$this->email->initialize($config);

            // $this->email->attach($generate_file['pathfile'].$generate_file['file']);
            if ($mba_have_attachment->num_rows() > 0) {
                $mba_have_attachment = $mba_have_attachment->result();
                foreach ($mba_have_attachment as $o_attachment) {
                    $a_filedata = explode('/', $o_attachment->document_link);
                    $s_filename = $a_filedata[count($a_filedata) - 1];
                    $this->email->attach($generate_file['pathfile'].$s_filename, 'attachment', $o_attachment->document_name);
                }
            }
            $this->email->from('employee@company.ac.id', '[IULI-SYSTEM] NO REPLY');
            $this->email->cc($mbo_finance_data->employee_email);
            // $this->email->to('employee@company.ac.id');
            $this->email->subject('[GSR-Notification] Need Action!');
            $this->email->message($s_body_mail);

            if (!$this->email->send()) {
                log_message('error', 'ERROR send notification '.__FILE__.' '.__LINE__);
                $this->email->from('employee@company.ac.id');
                $this->email->to(array('employee@company.ac.id'));
                $this->email->subject('ERROR send notification for review');
                $this->email->message('GSR_ID:'.$s_gsr_id.'||'.json_encode($generate_file));
                $this->email->send();
            }
        }

        return $generate_file;
    }

    public function get_gsr_status_log($a_clause = false)
    {
        $this->db->select('*, gs.date_added AS "gsr_last_date"');
        $this->db->from('portal_gsr.dt_gsr_status gs');
        $this->db->join('portal_gsr.ref_status_action rs', 'rs.status_action_id = gs.status_action_id');
        $this->db->join('portal_main.dt_personal_data pd', 'pd.personal_data_id = gs.personal_data_id');
        $this->db->join('portal_gsr.dt_gsr_main gm', 'gm.gsr_id = gs.gsr_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $this->db->order_by('gs.date_added', 'DESC');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $o_data = $query->first_row();
            $o_data->name_user = (!is_null($o_data->personal_data_id)) ? $this->General->retrieve_title($o_data->personal_data_id) : '';
            return $o_data;
        }
        else {
            return false;
        }
    }

    public function get_gsr_details($a_clause = false)
    {
        $this->grm->from('dt_gsr_details gd');
        $this->grm->join('ref_unit ru', 'ru.unit_id = gd.unit_id');
        if ($a_clause) {
            $this->grm->where($a_clause);
        }

        $query = $this->grm->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_df_details($a_clause = false)
    {
        $this->grm->from('dt_df_details dd');
        $this->grm->join('dt_account_list al', 'al.account_no = dd.account_no');
        if ($a_clause) {
            $this->grm->where($a_clause);
        }

        $query = $this->grm->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    function get_gsr_user($a_clause = false, $a_order = false) {
        $this->db->select('*, pdd.date_added');
        $this->db->from('dt_personal_data pd');
        $this->db->join('dt_employee em', 'em.personal_data_id = pd.personal_data_id');
        $this->db->join('dt_personal_document pdd', 'pdd.personal_data_id_generated = pd.personal_data_id', 'LEFT');

        if ($a_clause) {
            $this->db->where($a_clause);
        }
        if ($a_order) {
            foreach ($a_order as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_gsr_data($a_clause = false, $a_dept_id = false, $s_status_action_id = false)
    {
        $this->db->from('portal_gsr.dt_gsr_main gm');
        $this->db->join('portal_main.ref_department rd', 'rd.department_id = gm.department_id');
        $this->db->join('portal_gsr.dt_account_list al', 'al.account_no = gm.account_no');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_dept_id) {
            $this->db->where_in('gm.department_id', $a_dept_id);
        }

        $this->db->order_by('gm.gsr_date_request', 'DESC');

        $query = $this->db->get();
        $mba_data = false;
        if ($query->num_rows() > 0) {
            $mba_data = $query->result();
            foreach ($mba_data as $key => $o_gsr) {
                $this->grm->from('dt_gsr_status gs');
                $this->grm->join('ref_status_action as', 'as.status_action_id = gs.status_action_id');
                $this->grm->where('gs.gsr_id', $o_gsr->gsr_id);
                $this->grm->order_by('gs.date_added', 'DESC');
                $query_status = $this->grm->get();
                $mbo_gsr_status = ($query_status->num_rows() > 0) ? $query_status->first_row() : false;
                $o_gsr->last_status_data = $mbo_gsr_status;
                $o_gsr->last_status = ($mbo_gsr_status) ? $mbo_gsr_status->current_progress : NULL;
                
                if ($s_status_action_id) {
                    $b_unset_this = true;
                    if (($mbo_gsr_status) AND ($mbo_gsr_status->status_action_id == $s_status_action_id)) {
                        $b_unset_this = false;
                    }

                    if ($b_unset_this) {
                        unset($mba_data[$key]);
                    }
                }
            }
        }
        
        return $mba_data;
    }

    public function get_df_data($a_clause = false)
    {
        $this->db->from('portal_gsr.dt_df_main dm');
        $this->db->join('portal_main.ref_bank bc', 'bc.bank_code = dm.bank_code', 'left');
        $this->db->join('portal_gsr.dt_gsr_main gm', 'gm.gsr_id = dm.gsr_id', 'left');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $this->db->order_by('dm.df_date_created', 'DESC');

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    function get_account_list($a_clause = false, $s_term_search = false)
    {
        $this->grm->from('dt_account_list');
        if ($a_clause) {
            $this->grm->where($a_clause);
        }

        if ($s_term_search) {
            $s_query_search = "(account_no LIKE '%$s_term_search%' OR account_name LIKE '%$s_term_search%')";
            $this->grm->where($s_query_search);
        }
        
        $q = $this->grm->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_unit_list($a_clause = false)
    {
        $this->grm->from('ref_unit ru');
        if ($a_clause) {
            $this->grm->where($a_clause);
        }
        $query = $this->grm->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_last_number_gsr($s_department_id, $i_year)
    {
        $this->grm->select_max('gsr_number');
        // $this->grm->where('department_id', $s_department_id);
        $this->grm->where('year(gsr_date_request)', $i_year);
        $query = $this->grm->get('dt_gsr_main')->first_row();

        $i_number = 0;
        if (!is_null($query->gsr_number)) {
            $i_number = $query->gsr_number;
        }
        $i_number++;

        $q_department = $this->db->get_where('ref_department', ['department_id' => $s_department_id]);
        $s_dept_abbreviation = $q_department->first_row()->department_abbreviation;
        return 'GSR'.str_pad($i_number, 3, "0", STR_PAD_LEFT).' '.$i_year.'-'.$s_dept_abbreviation;
    }

    public function get_last_number_df($i_year)
    {
        $this->grm->select_max('df_number');
        $this->grm->where('year(df_date_created)', $i_year);
        $query = $this->grm->get('dt_df_main')->first_row();

        $i_number = 0;
        if (!is_null($query->df_number)) {
            $i_number = $query->df_number;
        }
        $i_number++;

        return $i_number;
    }

    public function get_where($s_table_name, $a_clause = false)
	{
		if ($a_clause) {
			$query = $this->grm->get_where($s_table_name, $a_clause);
		}else{
			$query = $this->grm->get($s_table_name);
		}

		return ($query->num_rows() > 0) ? $query->result() : false;
	}

    public function get_enum_values( $table, $field )
	{
		$type = $this->grm->query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" )->row( 0 )->Type;
		preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
		$enum = explode("','", $matches[1]);
		return $enum;
	}
}
