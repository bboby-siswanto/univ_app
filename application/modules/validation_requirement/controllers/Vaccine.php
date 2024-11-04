<?php
class Vaccine extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Validation_requirement_model', 'Vrm');
    }

    public function submit_vaccine()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->helper('file');
            if ($this->input->post('first_vaccine_check') !== null) {
                $this->form_validation->set_rules('vaccine_date[0]', 'First Vaccine Date', 'required|trim');
                $this->form_validation->set_rules('vaccine_number[0]', 'First Vaccine Number', 'required|trim');
                $this->form_validation->set_rules('vaccine_type[0]', 'First Vaccine Type', 'required|trim');
                if (empty($_FILES['first_vaccine_link']['name'])) {
                    $a_return = ['code' => 1, 'message' => 'Please upload the certificate firts vaccine!'];
                    print json_encode($a_return);exit;
                }
            }
            
            if ($this->input->post('second_vaccine_check') !== null) {
                $this->form_validation->set_rules('vaccine_date[1]', 'Second Vaccine Date', 'required|trim');
                $this->form_validation->set_rules('vaccine_number[1]', 'Second Vaccine Number', 'required|trim');
                $this->form_validation->set_rules('vaccine_type[1]', 'Second Vaccine Type', 'required|trim');
                if (empty($_FILES['second_vaccine_link']['name'])) {
                    $a_return = ['code' => 1, 'message' => 'Please upload the certificate second vaccine!'];
                    print json_encode($a_return);exit;
                }
            }

            if ($this->input->post('third_vaccine_check') !== null) {
                $this->form_validation->set_rules('vaccine_date[2]', 'Third Vaccine Date', 'required|trim');
                $this->form_validation->set_rules('vaccine_number[2]', 'Third Vaccine Number', 'required|trim');
                $this->form_validation->set_rules('vaccine_type[2]', 'Third Vaccine Type', 'required|trim');
                if (empty($_FILES['third_vaccine_link']['name'])) {
                    $a_return = ['code' => 1, 'message' => 'Please upload the certificate Third vaccine!'];
                    print json_encode($a_return);exit;
                }
            }

            if (($this->input->post('first_vaccine_check') === null) AND ($this->input->post('second_vaccine_check') === null) AND ($this->input->post('third_vaccine_check') === null)) {
                $a_return = ['code' => 1, 'message' => 'Nothing saved!'];
                print json_encode($a_return);exit;
            }

            if ($this->form_validation->run()) {
                $s_directory_file = APPPATH.'uploads/'.$this->session->userdata('user')."/";
                if(!file_exists($s_directory_file)){
                    mkdir($s_directory_file, 0755);
				}
                // delete_files($s_directory_file);

                $config['upload_path'] = $s_directory_file;
                $config['allowed_types'] = 'jpg|jpeg|png|pdf';
                $config['max_size'] = 2028;
                $config['file_ext_tolower'] = TRUE;
                $config['replace'] = TRUE;
                // $config['encrypt_name'] = TRUE;

                $a_data = [];
                $a_personal_data_document = [];
                $a_error = [];

                // $this->load->library('upload', $config);
                if ($this->input->post('first_vaccine_check') !== null) {
                    $config_first = $config;
                    $config_first['file_name'] = 'cecd1a3f-ca66-11eb-96dc-52540039e1c3';
                    $this->load->library('upload', $config_first);
                    $this->upload->initialize($config_first);

                    if($this->upload->do_upload('first_vaccine_link')) {
                        $s_filename = $this->upload->data('file_name');

                        $a_personal_data_document_data = [
                            'personal_data_id' => $this->session->userdata('user'),
                            'document_id' => 'cecd1a3f-ca66-11eb-96dc-52540039e1c3',
                            'document_requirement_link' => $s_filename,
                            'document_mime' => $this->upload->data('file_type'),
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        
                        $a_vaccine_data = [
                            'vaccine_id' => $this->uuid->v4(),
                            'personal_data_id' => $this->session->userdata('user'),
                            'document_id' => 'cecd1a3f-ca66-11eb-96dc-52540039e1c3',
                            'vaccine_type' => set_value('vaccine_type[0]'),
                            'vaccine_date' => set_value('vaccine_date[0]'),
                            'vaccine_number' => set_value('vaccine_number[0]'),
                            // 'vaccine_link' => $s_filename,
                            'vaccine_status' => 'vaccinated',
                            'date_added' => date('Y-m-d H:i:s')
                        ];

                        array_push($a_data, $a_vaccine_data);
                        array_push($a_personal_data_document, $a_personal_data_document_data);
                    }
                    else {
                        array_push($a_error, $this->upload->display_errors());
                    }
                }
                
                if ($this->input->post('second_vaccine_check') !== null) {
                    $config_second = $config;
                    $config_second['file_name'] = 'd79d3bf1-ca7b-11eb-96dc-52540039e1c3';
                    $this->load->library('upload', $config_second);
                    $this->upload->initialize($config_second);

                    if($this->upload->do_upload('second_vaccine_link')) {
                        $s_filename = $this->upload->data('file_name');

                        $a_personal_data_document_data = [
                            'personal_data_id' => $this->session->userdata('user'),
                            'document_id' => 'd79d3bf1-ca7b-11eb-96dc-52540039e1c3',
                            'document_requirement_link' => $s_filename,
                            'document_mime' => $this->upload->data('file_type'),
                            'date_added' => date('Y-m-d H:i:s')
                        ];

                        $a_vaccine_data = [
                            'vaccine_id' => $this->uuid->v4(),
                            'personal_data_id' => $this->session->userdata('user'),
                            'document_id' => 'd79d3bf1-ca7b-11eb-96dc-52540039e1c3',
                            'vaccine_type' => set_value('vaccine_type[1]'),
                            'vaccine_date' => set_value('vaccine_date[1]'),
                            'vaccine_number' => set_value('vaccine_number[1]'),
                            // 'vaccine_link' => $s_filename,
                            'vaccine_status' => 'vaccinated',
                            'date_added' => date('Y-m-d H:i:s')
                        ];

                        array_push($a_data, $a_vaccine_data);
                        array_push($a_personal_data_document, $a_personal_data_document_data);
                    }
                    else {
                        array_push($a_error, $this->upload->display_errors());
                    }
                }

                if ($this->input->post('third_vaccine_check') !== null) {
                    $config_third = $config;
                    $config_third['file_name'] = 'e1653945-ae4e-11ec-91ba-52540039e1c3';
                    $this->load->library('upload', $config_third);
                    $this->upload->initialize($config_third);

                    if($this->upload->do_upload('third_vaccine_link')) {
                        $s_filename = $this->upload->data('file_name');

                        $a_personal_data_document_data = [
                            'personal_data_id' => $this->session->userdata('user'),
                            'document_id' => 'e1653945-ae4e-11ec-91ba-52540039e1c3',
                            'document_requirement_link' => $s_filename,
                            'document_mime' => $this->upload->data('file_type'),
                            'date_added' => date('Y-m-d H:i:s')
                        ];

                        $a_vaccine_data = [
                            'vaccine_id' => $this->uuid->v4(),
                            'personal_data_id' => $this->session->userdata('user'),
                            'document_id' => 'e1653945-ae4e-11ec-91ba-52540039e1c3',
                            'vaccine_type' => set_value('vaccine_type[2]'),
                            'vaccine_date' => set_value('vaccine_date[2]'),
                            'vaccine_number' => set_value('vaccine_number[2]'),
                            // 'vaccine_link' => $s_filename,
                            'vaccine_status' => 'vaccinated',
                            'date_added' => date('Y-m-d H:i:s')
                        ];

                        array_push($a_data, $a_vaccine_data);
                        array_push($a_personal_data_document, $a_personal_data_document_data);
                    }
                    else {
                        array_push($a_error, $this->upload->display_errors());
                    }
                }

                if (count($a_error) > 0) {
                    $a_return = ['code' => 1, 'message' => '<li>'.implode('</li><li>', $a_error).'</li>'];
                }
                else if (count($a_data) > 0) {
                    $this->load->model('personal_data/Personal_data_model', 'Pdm');

                    foreach ($a_personal_data_document as $a_document_data) {
                        $mba_personal_data_document = $this->Pdm->get_personal_document($this->session->userdata('user'), $a_document_data['document_id']);
                        if ($mba_personal_data_document) {
                            $this->Pdm->save_personal_document($a_document_data, $this->session->userdata('user'), $a_document_data['document_id']);
                        }
                        else {
                            $this->Pdm->save_personal_document($a_document_data);
                        }
                    }

                    $this->Vrm->delete_personal_vaccine($this->session->userdata('user'));
                    foreach ($a_data as $a_vaccine_data) {
                        $this->Vrm->submit_vaccine($a_vaccine_data);
                    }
                    $a_return = ['code' => 0, 'message' => 'Success!'];
                    modules::run('validation_requirement/submit_confirmation');
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'No data processing!'];
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => validation_errors('<li>', '</li>')];
            }

            print json_encode($a_return);
        }
    }

    public function modal_input()
    {
        $this->load->view('validation_requirement/covid_vaccine/modal_input', $this->a_page_data);
    }

    public function form_question()
    {
        if ($this->session->userdata('type') == 'student') {
            if (($this->session->has_userdata('student_status')) AND ($this->session->userdata('student_status') == 'active')) {
                $this->load->view('validation_requirement/covid_vaccine/validation_question', $this->a_page_data);
            }
        }
    }
}
