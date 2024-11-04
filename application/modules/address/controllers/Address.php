<?php
class Address extends App_core
{
    function __construct()
    {
        parent::__construct('profile');
        $this->load->model('address/Address_model','Adm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
    }

    public function address_lists($s_student_id = false, $s_personal_data_id = false)
    {
        if (!$s_personal_data_id) {
            $s_personal_data_id = $this->session->userdata('user');
        }

        $mbo_personal_data = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
        if ($mbo_personal_data) {
            $s_btn_html = Modules::run('layout/generate_buttons', 'profile', 'address_list');

            $this->a_page_data['btn_html'] = $s_btn_html;
            $this->a_page_data['o_personal_data'] = $mbo_personal_data;
            $this->a_page_data['personal_data_id'] = $s_personal_data_id;
            $this->a_page_data['body'] = $this->load->view('address/address_default', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function get_country()
	{
		$s_term = false;
		if($this->input->is_ajax_request()){
			$s_term = $this->input->post('term');
		}
		
		$a_country_data = $this->General->get_country($s_term);
		return $a_country_data;
    }
    
    public function get_dikti_wilayah()
	{
		$s_term = false;
		if($this->input->is_ajax_request()){
			$s_term = $this->input->post('term');
		}
		
		$a_dikti_wilayah = $this->General->get_dikti_wilayah($s_term);
		return $a_dikti_wilayah;
	}

    public function view_list_address_list($s_personal_data_id = false)
    {
        if ($s_personal_data_id) {
            $this->a_page_data['o_address_list'] = $this->Adm->get_personal_address($s_personal_data_id);
            $this->a_page_data['o_personal_data'] = $this->Pdm->get_personal_data_by_id($s_personal_data_id);
            $this->load->view('table/address_history', $this->a_page_data);
        }
    }

    public function form_create_address($s_personal_data_id = false)
    {
        if (!$s_personal_data_id) {
            $s_personal_data_id = $this->session->userdata('user');
        }

        $this->a_page_data['o_address'] = false;
        if ($this->input->is_ajax_request()) {
            $this->a_page_data['o_address'] = $this->Adm->get_personal_address_filtered(array('dpa.personal_data_id' => $this->input->post('personal_data_id'), 'da.address_id' => $this->input->post('address_id')))[0];
        }

        $this->a_page_data['personal_data_id'] = $s_personal_data_id;
        $this->a_page_data['s_page_child_title'] = 'Add new address data';
        $s_html = $this->load->view('form/form_input_address', $this->a_page_data, true);

        if ($this->input->is_ajax_request()) {
            print json_encode(array('code' => 0, "data" => $s_html));
            exit;
        }else{
            print($s_html);
        }
    }

    public function filter_address_history()
    {
        $return  = array();
        if ($this->input->post()) {
            $a_filter_data = $this->input->post();
			foreach($a_filter_data as $key => $value){
				if($a_filter_data[$key] == 'all'){
					unset($a_filter_data[$key]);
				}
            }
            
            $return  = $this->Adm->get_personal_address_filtered($a_filter_data);
        }

        print json_encode(array('code' => 0, 'data' => $return));
        exit;
    }

    public function delete_address_data()
    {
        if ($this->input->is_ajax_request()) {
            $a_clause = array(
                'dpa.address_id' => $this->input->post('address_id'),
                'dpa.personal_data_id' => $this->input->post('personal_data_id')
            );

            $mbo_address_data = $this->Adm->get_personal_address_filtered($a_clause);
            if (($mbo_address_data) AND ($mbo_address_data[0]->personal_address_type != 'primary')) {
                if ($this->Adm->delete_address_data($this->input->post('address_id'), $this->input->post('personal_data_id'))) {
                    $rtn = array('code' => 0, 'message' => 'Success delete data');
                }else{
                    $rtn = array('code' => 1, 'message' => 'Error delete data');
                }
            }else{
                $rtn = array('code' => 1, 'message' => 'Data type is primary');
            }
        }else{
            $rtn = array('code' => 1, 'message' => 'Nothing action');
        }

        print json_encode($rtn);
    }

    public function save_address_data()
    {
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->input->post('personal_data_id');
            $s_address_id = ($this->input->post('address_id') == '' ? $this->uuid->v4() : $this->input->post('address_id'));

            $o_personal_address_data = $this->Adm->get_personal_address($s_personal_data_id);
            $s_personal_address_type = 'primary';
            if ($o_personal_address_data) {
                $s_personal_address_type = 'alternative';
            }

            $this->form_validation->set_rules('address_street', 'Street', 'trim|required');
            $this->form_validation->set_rules('address_name', 'Address Name', 'trim|required');
            $this->form_validation->set_rules('address_country_name', 'Country', 'trim|required');
            $this->form_validation->set_rules('address_province', 'Province', 'trim|required');
            $this->form_validation->set_rules('address_city', 'City', 'trim|required');
            $this->form_validation->set_rules('address_district', 'District', 'trim|required');
            $this->form_validation->set_rules('address_sub_district', 'Sub District', 'trim|required');
            $this->form_validation->set_rules('rt', 'RT', 'trim|required|numeric');
            $this->form_validation->set_rules('rw', 'RW', 'trim|required|numeric');
            $this->form_validation->set_rules('zip_code', 'Zip Code', 'trim|required|numeric|max_length[5]');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|numeric|min_length[5]');

            if ($this->form_validation->run()) {
                $a_address_data = array(
                    'address_id' => $s_address_id,
                    'dikti_wilayah_id' => set_value('address_district_id'),
                    'country_id' => set_value('address_country_id'),
                    'address_rt' => set_value('rt'),
                    'address_rw' => set_value('rw'),
                    'address_province' => strtoupper(set_value('address_province')),
                    'address_city' => strtoupper(set_value('address_city')),
                    'address_zipcode' => set_value('zip_code'),
                    'address_street' => strtoupper(set_value('address_street')),
                    'address_sub_district' => strtoupper(set_value('address_sub_district')),
                    'address_phonenumber' => (set_value('phone_number') == '') ? NULL : set_value('phone_number')
                );

                $a_personal_address = array(
                    'personal_data_id' => $s_personal_data_id,
                    'address_id' => $s_address_id,
                    'personal_address_name' => strtoupper(set_value('address_name'))
                );

                $this->db->trans_start();
                if ($this->input->post('address_id') == '') {
                    $a_address_data['date_added'] = date('Y-m-d H:i:s');
                    $a_personal_address['date_added'] = date('Y-m-d H:i:s');
                    $a_personal_address['personal_address_type'] = $s_personal_address_type;
                    if ($this->Adm->save_address($a_address_data)) {
                        $this->Adm->save_personal_address($a_personal_address);
                    }else {
                        $rtn = array('code' => 1, 'message' => "Error proccessing data");
                    }
                }else{
                    if ($this->Adm->save_address($a_address_data, $s_address_id)) {
                        $this->Adm->save_personal_address($a_personal_address, $s_personal_data_id, $s_address_id);
                    }else{
                        $rtn = array('code' => 1, 'message' => "Error proccessing data");
                    }
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $rtn = array('code' => 1, 'message' => 'Error processing data');
                }else{
                    $this->db->trans_commit();
                    $rtn = array('code' => 0, 'message' => 'Success processing data');

                    if ($this->General->is_student_candidate($s_personal_data_id)) {
                        if ($s_personal_address_type == 'primary') {
                            $this->load->model('Api_core_model','Acm');
                            $a_token_config = $this->config->item('token')['pmb'];
                            $url = $this->config->item('sites')['pmb'];
                            $s_token = $a_token_config['access_token'];
                            $s_secret_token = $a_token_config['secret_token'];

                            $a_sync_data = array();
                            array_push($a_sync_data, $this->libapi->prepare_data('dt_address', array('address_id' => $s_address_id)));
                            array_push($a_sync_data, $this->libapi->prepare_data('dt_personal_address', array('personal_data_id' => $s_personal_data_id, 'address_id' => $s_address_id)));

                            $sync_data = array(
                                'sync_data' => $a_sync_data
                            );
                            
                            $hashed_string = $this->libapi->hash_data($sync_data, $s_token, $s_secret_token);
                            $post_data = json_encode(array(
                                'access_token' => $s_token,
                                'data' => $hashed_string
                            ));
                            
                            $a_result = $this->libapi->post_data($url.'api/portal/sync_all', $post_data);
                            if ($a_result != null) {
                                $this->Acm->update_result_sync(json_decode(json_encode($a_result->a_return_data), true), intval($a_result->code));
                            }
                        }
                    }
                }
            }else{
                $rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }
        }else{
            $rtn = array('code' => 1, 'message' => 'Nothing action');
        }

        print json_encode($rtn);
        exit;
    }
}
