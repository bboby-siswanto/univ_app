<?php
class Document extends App_core
{
    function __construct()
    {
        parent::__construct('profile');
        $this->load->model('personal_data/Personal_data_model','Pdm');
        $this->load->model('student/Student_model', 'Stm');
        if (($this->session->userdata('dikti_required') !== NULL) AND ($this->session->userdata('dikti_required') == false)) {
			redirect('personal_data/profile');
		}
    }

    public function new_document()
    {
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->input->post('personal_data_id');
            $this->form_validation->set_rules('document_type','Document Type','trim|required');

            if ($this->form_validation->run()) {
                $s_document_id = set_value('document_type');

                $directory_file = APPPATH.'uploads/'.$s_personal_data_id.'/';
                if(!file_exists($directory_file)){
                    mkdir($directory_file, 0755);
                }

                $config['upload_path'] = $directory_file;
                $config['allowed_types'] = 'jpg|jpeg|png|pdf';
                $config['max_size'] = 2048;
                $config['file_ext_tolower'] = TRUE;
                $config['replace'] = TRUE;
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);

                if($this->upload->do_upload('file')) {
                    $s_filename = $this->upload->data('file_name');
                    $a_personal_data_document = array(
                        'personal_data_id' => $s_personal_data_id,
                        'document_id' => $s_document_id,
                        'document_requirement_link' => $s_filename,
                        'document_mime' => $this->upload->data('file_type')
                    );
                    
                    $s_file_name_deleted = null;
                    $mbo_personal_document = $this->Pdm->get_personal_document($s_personal_data_id, $s_document_id);
                    if ($mbo_personal_document) {
                        $s_document_link = $mbo_personal_document[0]->document_requirement_link;
                        $s_file_name_deleted = $s_document_link;
                        unlink($directory_file.$s_document_link);
                        $b_save_personal_data_document = $this->Pdm->save_personal_document($a_personal_data_document, $s_personal_data_id, $s_document_id);
                    }else {
                        $b_save_personal_data_document = $this->Pdm->save_personal_document($a_personal_data_document);
                    }

                    if ($b_save_personal_data_document) {
                        $return = array('code' => 0, 'message' => 'Upload success');

                        // if($this->General->is_student_candidate($s_personal_data_id)){
                        //     $this->load->model('Api_core_model','Acm');
                        //     $a_token_config = $this->config->item('token')['pmb'];
                        //     $a_sites = $this->config->item('sites');
                        //     $s_token = $a_token_config['access_token'];
                        //     $s_secret_token = $a_token_config['secret_token'];
                        //     $url = $a_sites['pmb'];

                        //     $a_sync_data = array();
                        //     array_push($a_sync_data, $this->libapi->prepare_data('dt_personal_data_document', array('personal_data_id' => $s_personal_data_id, 'document_id' => $s_document_id)));

                        //     $sync_data = array(
                        //         'sync_data' => $a_sync_data
                        //     );
                            
                        //     $hashed_string = $this->libapi->hash_data($sync_data, $s_token, $s_secret_token);
                        //     $post_data = json_encode(array(
                        //         'access_token' => 'PORTALIULIACID',
                        //         'data' => $hashed_string
                        //     ));
                            
                        //     $a_result = $this->libapi->post_data($url.'api/portal/sync_all', $post_data);
                        //     if ($a_result != null) {
                        //         $this->Acm->update_result_sync(json_decode(json_encode($a_result->a_return_data), true), intval($a_result->code));
                        //     }
                        // }
                    }else{
                        $return = array('code' => 1, 'message' => 'Error proccessing document');
                    }
                }else{
                    $return = array('code' => 1, 'message' => $this->upload->display_errors('<li>', '</li>'));
                }
            }else{
                $return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }
        }else{
            $return = array('code' => 1, 'message' => 'Nothing action');
        }

        print json_encode($return);
    }

    public function delete_personal_document()
    {
        if ($this->input->is_ajax_request()) {
            $s_document_link = $this->input->post('document_link');
            $a_clause = array(
                'personal_data_id' => $this->input->post('personal_data_id'),
                'document_id' => $this->input->post('document_id')
            );
            $directory_file = APPPATH.'uploads/'.$this->input->post('personal_data_id').'/';
            
            if ($this->Pdm->delete_personal_document($a_clause)) {
                $return =  array('code' => 0, 'message' => 'Success');
                if(!file_exists($directory_file)){
                    mkdir($directory_file, 0755);
                }else{
                    unlink($directory_file.$s_document_link);
                }
            }else {
                $return =  array('code' => 1, 'message' => "Cann't remove personal document");
            }
        }else{
            $return =  array('code' => 1, 'message' => 'Nothing action');
        }

        print(json_encode($return));
        exit;
    }

    public function filter_supporting_document()
    {
        $return_data = array();
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->input->post('personal_data_id');
            $return_data = $this->Pdm->get_personal_document($s_personal_data_id);
        }

        print json_encode(array('code' => 0, 'data' => $return_data));
        exit;
    }

    public function document_list($s_student_id = false, $s_personal_data_id = false)
    {
        // modules::run('portal/sync/pmb_sync_document');
        if (!$s_personal_data_id) {
            $s_personal_data_id = $this->session->userdata('user');
        }

        $o_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
        if ($o_personal_data) {
            $s_btn_html = Modules::run('layout/generate_buttons', 'profile', 'document_list');

            if ($student_data = $this->Stm->get_student_filtered(array('ds.personal_data_id' => $s_personal_data_id, 'ds.student_status' => 'active'))) {
				$this->a_page_data['student_data'] = $student_data[0];
			}
            
            $this->a_page_data['btn_html'] = $s_btn_html;
            $this->a_page_data['personal_data_id'] = $s_personal_data_id;
            $this->a_page_data['o_personal_data'] = $o_personal_data;
            $this->a_page_data['body'] = $this->load->view('document_default', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function view_list_supporting_document($s_personal_data_id = false)
    {
        if ($s_personal_data_id) {
            $this->a_page_data['o_personal_data'] = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
            $this->a_page_data['o_supporting_document'] = $this->Pdm->get_personal_document($s_personal_data_id);
            $this->load->view('table/document_history', $this->a_page_data);
        }
    }

    public function form_create_document($s_personal_data_id = false, $s_document_id = false)
    {
        if (!$s_personal_data_id) {
            $s_personal_data_id = $this->session->userdata('user');
        }

        $this->a_page_data['personal_document'] = false;
        if ($s_document_id) {
            $this->a_page_data['personal_document'] = $this->Pdm->get_personal_document($s_personal_data_id, $s_document_id)[0];
        }
        $this->a_page_data['personal_data_id'] = $s_personal_data_id;
        $this->a_page_data['o_requirement_document'] = $this->Pdm->get_requirement_document();
        $this->a_page_data['s_page_child_title'] = 'Add new academic history';
        $s_html = $this->load->view('personal_data/form/form_add_document', $this->a_page_data, true);
        if ($this->input->is_ajax_request()) {
            print json_encode($s_html);
            exit;
        }else{
            print($s_html);
        }
    }

    public function send_document_api($s_personal_data_id = null, $s_filename = null, $s_file_name_o = null)
	{
		$b_stat_exec = false;
		if ($s_personal_data_id != null) {
            $a_token_config = $this->config->item('ftp')['pmb'];
			$this->load->library('ftp');
			$config['hostname'] = $a_token_config['hostname'];
			$config['username'] = $a_token_config['username'];
            $config['password'] = $a_token_config['password'];
            $config['port'] = $a_token_config['port'];
            $config['passive'] = $a_token_config['passive'];
			$config['debug'] = $a_token_config['debug'];

            $s_links =  $a_token_config['path_upload'].$s_personal_data_id.'/';
            $directory_from = APPPATH.'uploads/'.$s_personal_data_id.'/'.$s_filename;
            $directory_to = $s_links.$s_filename;
            
            // var_dump($directory_from);print('<br>');var_dump($directory_to);exit;

			$this->ftp->connect($config);
			if ($s_file_name_o != null) {
				$a_list = $this->ftp->list_files($s_links);
				if (in_array($s_file_name_o, $a_list)) {
					$this->ftp->delete_file($s_links.$s_file_name_o);
				}
			}

			$this->ftp->upload($directory_from, $directory_to, 'binary', 0644);

			$a_list_a = $this->ftp->list_files($s_links);
			if (in_array($s_filename,$a_list_a)) {
				$b_stat_exec = true;
			}else{
				$b_stat_exec = false;
			}

			$this->ftp->close();
		}
		return $b_stat_exec;
	}
}
