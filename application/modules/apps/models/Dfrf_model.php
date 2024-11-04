<?php
class Dfrf_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->grm = $this->load->database('gsr_db', true);
    }

    public function submit_checking($a_df_update_data, $a_df_status_data, $a_note_data, $s_df_id)
    {
        $this->grm->trans_begin();

        $this->grm->update('dt_df_main', $a_df_update_data, ['df_id' => $s_df_id]);
        $this->grm->insert('dt_df_status', $a_df_status_data);

        if (count($a_note_data) > 0) {
            foreach ($a_note_data as $a_note) {
                $this->grm->insert('dt_df_note', $a_note);
            }
        }

        if ($this->grm->trans_status() === false) {
            $this->grm->trans_rollback();
            $a_return = ['code' => 1, 'message' => 'Failed response process!'];
        }
        else {
            $this->grm->trans_commit();
            $a_return = ['code' => 0, 'message' => 'Success'];

            if ($a_df_status_data['status_action_id'] == '5') {
                $a_return = $this->mail_for_approv_check($s_df_id);
                if ($a_return['code'] != 0) {
                    $a_return = ['code' => 0, 'message' => 'Success submit but Failed sending notification for approve!\n'.$a_return['message']];
                }
            }
            else {
                $a_return = $this->mail_for_reject($s_df_id);
                if ($a_return['code'] != 0) {
                    $a_return = ['code' => 0, 'message' => 'Success submit but Failed sending notification for reject!\n'.$a_return['message']];
                }
            }
        }
        
        return $a_return;
    }

    public function get_remarks_list($a_clause = false)
    {
        $this->db->select("*, dn.date_added AS 'df_date_remarks'");
        $this->db->from('portal_gsr.dt_df_note dn');
        $this->db->join('portal_gsr.dt_df_status ds', 'ds.status_id = dn.status_id');
        $this->db->join('portal_gsr.dt_df_main dm', 'dm.df_id = ds.df_id');
        $this->db->join('portal_main.dt_personal_data pd', 'pd.personal_data_id = ds.personal_data_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('dn.date_added', 'ASC');

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_df_status_log($a_clause = false)
    {
        $this->db->select('*, ds.date_added AS "df_last_date"');
        $this->db->from('portal_gsr.dt_df_status ds');
        $this->db->join('portal_gsr.ref_status_action rs', 'rs.status_action_id = ds.status_action_id');
        $this->db->join('portal_main.dt_personal_data pd', 'pd.personal_data_id = ds.personal_data_id');
        $this->db->join('portal_gsr.dt_df_main dm', 'dm.df_id = ds.df_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('ds.date_added', 'DESC');
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

    public function get_df_details($a_clause = false)
    {
        $this->grm->from('dt_df_details dd');
        $this->grm->join('dt_account_list al', 'al.account_no = dd.account_no');
        $this->grm->join('dt_df_main dm', 'dm.df_id = dd.df_id');
        if ($a_clause) {
            $this->grm->where($a_clause);
        }

        $query = $this->grm->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_df_data($a_clause = false, $a_likeclause = false)
    {
        $this->db->select('*, dm.*');
        $this->db->from('portal_gsr.dt_df_main dm');
        $this->db->join('portal_gsr.dt_gsr_main gm', 'gm.gsr_id = dm.gsr_id');
        $this->db->join('portal_main.ref_bank rb', 'rb.bank_code = dm.bank_code', 'left');
        $this->db->join('portal_main.ref_department rd', 'rd.department_id = gm.department_id', 'left');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_likeclause) {
            $this->db->like($a_likeclause);
        }

        $this->db->order_by('dm.df_date_created', 'DESC');

        $query = $this->db->get();
        // return $this->db->last_query();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
    
    public function submit_df($post)
    {
        $mbo_rector_data = $this->General->get_rectorate('vice_rector');
        $mbo_finance_data = $this->General->get_head_department('FIN');

        $post['total_amount_debet'] = (empty($post['total_amount_debet'])) ? 0 : str_replace(',', '', str_replace('.','',$post['total_amount_debet']));
        $post['total_amount_kredit'] = (empty($post['total_amount_kredit'])) ? 0 : str_replace(',', '', str_replace('.','',$post['total_amount_kredit']));
        $mbo_user_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
        $s_gsr_id = $post['gsr_id'];
        $mbo_gsr_query = $this->get_where('dt_gsr_details', ['gsr_id' => $s_gsr_id]);
        $d_total_amount_gsr = 0;
        $d_total_amount_df = $post['total_amount_kredit'] - $post['total_amount_debet'];

        if ($mbo_gsr_query) {
            foreach ($mbo_gsr_query as $o_gsr_details) {
                $d_total_amount_gsr += $o_gsr_details->gsr_details_total_price;
            }
        }

        $b_amount_exact = false;
        if ($post['total_amount_kredit'] == $d_total_amount_gsr) {
            $b_amount_exact = true;
        }
        else if ($post['total_amount_debet'] == $d_total_amount_gsr) {
            $b_amount_exact = true;
        }

        if (!$b_amount_exact) {
            $a_return = ['code' => 1, 'message' => 'Total Price not exact with amount reference'];
        }else {
            $this->grm->trans_begin();
            $a_main_data = [
                'gsr_id' => $post['gsr_id'],
                'personal_data_id_requested' => $this->session->userdata('user'),
                // 'bank_code' => $post['bank_code'],
                'df_number' => $post['df_number'],
                'df_top' => $post['df_top'],
                'df_date_created' => date('Y-m-d', strtotime($post['df_date'])).' '.date('H:i:s'),
                'df_bank_account' => $post['df_account'],
                'df_budget_dept' => $post['budget'],
                'df_transaction' => $post['transaction_paidreceive'],
                'df_type' => $post['df_type']
            ];

            if ((array_key_exists('df_update_key', $post)) AND (!empty($post['df_update_key']))) {
                $s_df_id = $post['df_update_key'];

                $this->grm->update('dt_df_main', $a_main_data, ['df_id' => $s_df_id]);
                $this->grm->delete('dt_df_details', ['df_id' => $s_df_id]);
            }
            else {
                $s_df_id = $this->uuid->v4();

                $a_main_data['df_id'] = $s_df_id;
                $this->grm->insert('dt_df_main', $a_main_data);
            }

            $s_status_id = $this->uuid->v4();
            $a_log_status_data = [
                'status_id' => $s_status_id,
                'df_id' => $s_df_id,
                'personal_data_id' => $this->session->userdata('user'),
                'status_action_id' => '1',
                'date_added' => date('Y-m-d H:i:s')
            ];

            $this->grm->insert('dt_df_status', $a_log_status_data);

            if (!empty($post['df_remarks'])) {
                $s_remark = trim($post['df_remarks']);

                $a_note_data = [
                    'note_id' => $this->uuid->v4(),
                    'status_id' => $s_status_id,
                    'personal_data_id' => $this->session->userdata('user'),
                    'note' => $s_remark,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                $this->grm->insert('dt_df_note', $a_note_data);
            }

            for ($i = 0; $i < count($post['df_desc']); $i++) { 
                $s_amount_kredit = str_replace(',', '', $post['df_kredit'][$i]);
                $s_amount_debet = str_replace(',', '', $post['df_debet'][$i]);
                $s_amount_kredit = str_replace('.', '', $s_amount_kredit);
                $s_amount_debet = str_replace('.', '', $s_amount_debet);

                $a_details_data = [
                    'df_details_id' => $this->uuid->v4(),
                    'df_id' => $s_df_id,
                    'df_details_remarks' => $post['df_desc'][$i],
                    'account_no' => $post['accountno'][$i],
                    'df_details_debet' => $s_amount_debet,
                    'df_details_kredit' => $s_amount_kredit,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                $this->grm->insert('dt_df_details', $a_details_data);
            }

            if ((isset($post['fileattach'])) AND (!is_null($post['fileattach']))) {
                foreach ($post['fileattach'] as $s_gsr_fileid) {
                    $this->grm->update('dt_gsr_attachment', ['df_show' => 'true'], ['gsr_file_id' => $s_gsr_fileid]);
                }
            }

            $mba_have_attachment = $this->get_where('dt_gsr_attachment', ['gsr_id' => $s_gsr_id, 'df_show' => 'true']);
            if ($mba_have_attachment) {
                // $mba_have_attachment = $mba_have_attachment->result();
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
                $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$this->session->userdata('user').'/'.date('Y').'/gsr/'.$post['gsr_code'].'/';
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $config['allowed_types'] = 'pdf|jpg|jpeg|png|bmp|doc|docx|xls|xlsx|ppt|pptx';
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
                                'document_link' => date('Y').'/gsr/'.$post['gsr_code'].'/'.$this->upload->data('file_name'),
                                'document_name' => $a_value['name'],
                                'gsr_show' => 'false',
                                'df_show' => 'true',
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
                $a_return = $this->mail_for_check($s_df_id);
                if ($a_return['code'] != 0) {
                    $a_return = ['code' => 2, 'message' => 'Failed to send notification for review!\n'.$a_return['message']];
                }
            }
        }

        return $a_return;
    }

    public function mail_for_check($s_df_id)
    {
        $this->load->model('apps/Letter_numbering_model', 'Lnm');
        $generate_file = modules::run('download/pdf_download/generate_df_file', $s_df_id);
        if ($generate_file) {
            $mbo_finance_data = $this->General->get_head_department('FIN');
            $mbo_df_data = $this->get_where('dt_df_main', ['df_id' => $s_df_id])[0];
            $mbo_gsr_data = false;

            if (!is_null($mbo_df_data->gsr_id)) {
                $gsr_id = $mbo_df_data->gsr_id;
            }
            
            $mbo_gsr_data = $this->get_where('dt_gsr_main', ['gsr_id' => $gsr_id]);
            $s_personal_document_id = $this->uuid->v4();
            $a_personal_document_data = [
                'personal_document_id' => $s_personal_document_id,
                'personal_data_id_generated' => $this->session->userdata('user'),
                'personal_data_id_target' => NULL,
                'letter_number_id' => NULL,
                'document_link' => urldecode($generate_file['file'])
            ];

            $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
            $this->Lnm->submit_letter_number($a_personal_document_data);
            $generate_file['doc_key'] = $s_personal_document_id;
            
            $mba_df_request_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mbo_df_data->personal_data_id_requested]);

            // kirim email ke head GSR
            $email_data = array();
            $email_data['pidt'] = $mbo_finance_data->personal_data_id;
            $email_data['pidg'] = $mbo_df_data->personal_data_id_requested;
            $email_data['sid'] = $s_df_id;
            $email_data['param_link'] = base64_encode(json_encode($email_data));
            $email_data['s_user_request'] = ucwords(strtolower($mba_df_request_data[0]->personal_data_name));
            $email_data['s_user_purpose'] = ucwords(strtolower($mbo_finance_data->head_full_name));
            $email_data['df_number'] = strtoupper($mbo_df_data->df_number);
            $email_data['df_data'] = $mbo_df_data;
            $s_body_mail = $this->load->view('gsr/misc/message_df_to_check', $email_data, true);
            $config['mailtype'] = 'html';
            $this->email->initialize($config);

            $this->email->attach($generate_file['pathfile'].$generate_file['file']);
            if ($mbo_gsr_data) {
                $mbo_gsr_data = $mbo_gsr_data[0];
                $mba_have_attachment = $this->get_where('dt_gsr_attachment', ['gsr_id' => $gsr_id, 'df_show' => 'true']);
                // $mbo_user_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);

                $mba_gsr_request_ = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mbo_gsr_data->personal_data_id_request]);
                $s_firts_char_user = substr($mba_gsr_request_[0]->personal_data_name, 0, 1);

                $s_gsr_code = $mbo_gsr_data->gsr_code;
                $s_gsr_path  = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$mbo_gsr_data->personal_data_id_request.'/'.date('Y', strtotime($mbo_gsr_data->gsr_date_request)).'/gsr/'.$s_gsr_code.'/';
                
                if ($mba_have_attachment) {
                    // $mba_have_attachment = $mba_have_attachment->result();
                    foreach ($mba_have_attachment as $o_attachment) {
                        $a_filedata = explode('/', $o_attachment->document_link);
                        $s_filename = $a_filedata[count($a_filedata) - 1];
                        $this->email->attach($s_gsr_path.$s_filename, 'attachment', $o_attachment->document_name);
                    }
                }
            }
            $this->email->from('employee@company.ac.id', '[IULI-SYSTEM] NO REPLY');
            // $this->email->to($mbo_approver_data->employee_email);
            // $this->email->cc([$mbo_finance_data->employee_email,$mba_request_data[0]->employee_email]);
            $this->email->to('employee@company.ac.id');
            $this->email->subject('[GSR-Notification] Need Action!');
            $this->email->message($s_body_mail);

            if (!$this->email->send()) {
                $a_return = ['code' => 1, 'message' => 'Failed send notification for approval'];
                log_message('error', 'ERROR send notification '.__FILE__.' '.__LINE__);
                $this->email->from('employee@company.ac.id');
                $this->email->to(array('employee@company.ac.id'));
                $this->email->subject('ERROR send notification for approve');
                $this->email->message('DF_ID:'.$s_df_id.'||'.json_encode($email_data));
                $this->email->send();
            }
            else {
                $a_return = ['code' => 0, 'message' => 'Success'];
            }
        }
        
        return $a_return;
    }

    public function mail_for_approv_check($s_df_id)
    {
        $generate_file = modules::run('download/pdf_download/generate_df_file', $s_df_id);
        
        $mbo_finance_data = $this->General->get_head_department('FIN');
        $mbo_approver_data = $this->General->get_head_department('REC');
        $mbo_df_data = $this->grm->get_where('dt_df_main', ['df_id' => $s_df_id])->first_row();
        // $mbo_gsr_data = $this->grm->get_where('dt_gsr_main', ['gsr_id' => $s_gsr_id])->first_row();
        $mba_have_attachment = $this->grm->get_where('dt_gsr_attachment', ['df_id' => $s_df_id, 'df_show' => 'true']);
        $mbo_user_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
        $mba_gsr_request_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mbo_gsr_data->personal_data_id_request]);
        $mba_request_data = $this->General->get_where('dt_employee', ['personal_data_id' => $mbo_gsr_data->personal_data_id_request]);
        $s_firts_char_user = substr($mba_gsr_request_data[0]->personal_data_name, 0, 1);

        // kirim email ke head GSR
        $email_data['pidt'] = $mbo_approver_data->personal_data_id;
        $email_data['pidg'] = $mbo_gsr_data->personal_data_id_request;
        $email_data['sid'] = $s_gsr_id;
        $email_data['param_link'] = base64_encode(json_encode($email_data));
        $email_data['s_user_request'] = ucwords(strtolower($mba_gsr_request_data[0]->personal_data_name));
        $email_data['s_approve_name'] = ucwords(strtolower($mbo_approver_data->head_full_name));
        $email_data['gsr_code'] = strtoupper($mbo_gsr_data->gsr_code);
        $s_body_mail = $this->load->view('gsr/misc/message_approval_rectorate', $email_data, true);
        $config['mailtype'] = 'html';
        $this->email->initialize($config);

        $s_gsr_path  = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$mbo_gsr_data->personal_data_id_request.'/'.date('Y', strtotime($mbo_gsr_data->gsr_date_request)).'/gsr/'.$mbo_gsr_data->gsr_code.'/';
        $s_gsr_name = str_replace(' ', '_', $mbo_gsr_data->gsr_code).'.pdf';
        if (file_exists($s_gsr_path.$s_gsr_name)) {
            $this->email->attach($s_gsr_path.$s_gsr_name);
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
        // $this->email->to($mbo_approver_data->employee_email);
        // $this->email->cc([$mbo_finance_data->employee_email,$mba_request_data[0]->employee_email]);
        $this->email->to('employee@company.ac.id');
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

    public function mail_for_reject($s_df_id, $s_reject_from = 'check')
    {
        $mbo_df_data = $this->grm->get_where('dt_df_main', ['df_id' => $s_df_id])->first_row();

        $mbo_finance_data = $this->General->get_head_department('FIN');
        $mbo_approver_data = $this->General->get_head_department('REC');
        $mbo_finishing_data = $this->General->get_head_department('Y-IULI');
        
        $mba_df_request_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $mbo_df_data->personal_data_id_requested]);

        $mba_have_attachment = $this->grm->get_where('dt_gsr_attachment', ['gsr_id' => $mbo_df_data->gsr_id, 'df_show' => 'true']);
        $mbo_user_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
        
        $mba_request_data = $this->General->get_where('dt_employee', ['personal_data_id' => $mbo_df_data->personal_data_id_requested]);
        $s_firts_char_user = substr($mba_df_request_data[0]->personal_data_name, 0, 1);

        if ($s_reject_from == 'check') {
            $email_data['pidg'] = $mbo_df_data->personal_data_id_checked;
            $email_data['s_user_reject'] = ucwords(strtolower($mbo_finance_data->head_full_name));
        }
        else if ($s_reject_from == 'approve') {
            $email_data['pidg'] = $mbo_df_data->personal_data_id_approved;
            $email_data['s_user_reject'] = ucwords(strtolower($mbo_approver_data->head_full_name));
        }
        else if ($s_reject_from == 'finish') {
            $email_data['pidg'] = $mbo_df_data->personal_data_id_finishing;
            $email_data['s_user_reject'] = ucwords(strtolower($mbo_finishing_data->head_full_name));
        }

        $email_data['pidt'] = $mba_df_request_data[0]->personal_data_id;
        $email_data['sid'] = $s_gsr_id;
        $email_data['param_link'] = str_replace('=', '', base64_encode(json_encode($email_data)));
        $email_data['s_user_request'] = ucwords(strtolower($mba_df_request_data[0]->personal_data_name));
        $email_data['key_code'] = strtoupper($mbo_df_data->df_number);
        $email_data['form_type'] = ($mbo_df_data->df_type == 'Bank Receipt') ? 'RF' : 'DF';

        $s_body_mail = $this->load->view('gsr/misc/message_reject', $email_data, true);
        $config['mailtype'] = 'html';
        $this->email->initialize($config);

        $s_df_path  = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$mbo_df_data->personal_data_id_requested.'/'.date('Y', strtotime($mbo_df_data->df_date_created)).'/gsr/'.str_replace(' ', '_', $mbo_df_data->df_number).'/';
        if ($mba_have_attachment->num_rows() > 0) {
            $mba_have_attachment = $mba_have_attachment->result();
            foreach ($mba_have_attachment as $o_attachment) {
                $a_filedata = explode('/', $o_attachment->document_link);
                $s_filename = $a_filedata[count($a_filedata) - 1];
                $this->email->attach($s_gsr_path.$s_filename, 'attachment', $o_attachment->document_name);
            }
        }
        $this->email->from('employee@company.ac.id', '[IULI-SYSTEM] NO REPLY');
        // $this->email->to($mba_request_data[0]->employee_email);
        // if ($s_reject_from == 'approve') {
        //     $this->email->cc([$mbo_finance_data->employee_email]);
        // }
        // else if ($s_reject_from == 'finish') {
        //     $this->email->cc([$mbo_finance_data->employee_email,$mbo_approver_data->employee_email]);
        // }
        
        $this->email->to('employee@company.ac.id');
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
    
    public function get_where($s_table_name, $a_clause = false)
	{
		if ($a_clause) {
			$query = $this->grm->get_where($s_table_name, $a_clause);
		}else{
			$query = $this->grm->get($s_table_name);
		}

		return ($query->num_rows() > 0) ? $query->result() : false;
	}
}
