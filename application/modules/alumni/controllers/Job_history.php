<?php
class Job_history extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('personal_data/Job_history_model', 'Jhm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('personal_data/Personal_data_model','Pdm');
        $this->load->model('institution/Institution_model', 'Insm');
        $this->load->model('Api_core_model','Acm');
    }

    // public function job_lists()
    // {
    //     $s_btn_html = Modules::run('layout/generate_buttons', 'profile', 'job_history');

    //     $this->a_page_data['personal_data_id'] = $this->session->userdata('user');
    //     $this->a_page_data['s_btn_html'] = $s_btn_html;
    //     $this->a_page_data['body'] = $this->load->view('personal_data/job_default', $this->a_page_data, true);
    //     $this->load->view('layout', $this->a_page_data);
    // }

    public function view_list_job_history($s_personal_data_id = false)
    {
        if ($s_personal_data_id) {
            $s_btn_html = Modules::run('layout/generate_buttons', 'profile', 'job_history');
            $this->a_page_data['s_btn_html'] = $s_btn_html;
            $this->a_page_data['personal_data_id'] = $s_personal_data_id;
            $this->load->view('table/job_history_lists', $this->a_page_data);
        }
    }

    public function form_create_job_history($this_my_job = true)
    {
        $s_personal_data_id = $this->session->userdata('user');
        $this->a_page_data['my_job'] = $this_my_job;
        $this->a_page_data['country_list'] = ($this->General->get_where('ref_country')) ? $this->General->get_where('ref_country') : false;
        if ($this->input->is_ajax_request()) {
            // var_dump($this->a_page_data);
            $s_academic_history_id = $this->input->post('academic_history_id');
            $mbo_job_history_data = $this->Jhm->get_job_history($s_personal_data_id, array('academic_history_id' => $s_academic_history_id));
            $this->a_page_data['o_academic_history_data'] = $mbo_job_history_data;
            $s_html = $this->load->view('form/form_create_job', $this->a_page_data, true);
            print json_encode(array('data' => $s_html));
        }else{
            // $this->a_page_data['o_academic_history_data'] = $s_personal_data_id;
            $s_html = $this->load->view('form/form_create_job', $this->a_page_data, true);
            return $s_html;
        }
        exit;
    }

    public function get_job_filtered($s_personal_data_id = false)
    {
        if (!$s_personal_data_id) {
            $s_personal_data_id = $this->session->userdata('user');
        }

        if ($this->input->is_ajax_request()) {
            $mbo_job_history = $this->Jhm->get_job_history($s_personal_data_id);
            print json_encode(array('code' => 0, 'data' => $mbo_job_history));
        }
    }

    public function submit_job_alumni()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('institution_name','Company name', 'trim|required');
            $this->form_validation->set_rules('personal_data_supervisor','Supervisor Name', 'trim|required');
            $this->form_validation->set_rules('institution_phone_number','Company Phone Number', 'trim|required');
            $this->form_validation->set_rules('institution_email','Company Email', 'trim|valid_email|required');
            $this->form_validation->set_rules('company_start_date','Start Working', 'trim|required');

            if ($this->form_validation->run()) {
                $s_personal_data_id = $this->session->userdata('user');
                $mba_alumni_job = $this->Pdm->get_academic_history($s_personal_data_id, [
                    'academic_history_this_job' => 'yes',
                    'ri.institution_name' => set_value('institution_name')
                ]);

                $a_prep_institution = [
                    'institution_name' => set_value('institution_name'),
                    'institution_email' => set_value('institution_email'),
                    'institution_phone_number' => set_value('institution_phone_number'),
                    'institution_type' => 'office',
                    'date_added' => date('Y-m-d H:i:s')
                ];

                $a_prep_academic_history = [
                    'personal_data_id' => $s_personal_data_id,
                    'academic_year_start_date' => set_value('company_start_date'),
                    'academic_history_main' => 'no',
                    'academic_history_this_job' => 'yes',
                    'status' => 'active'
                ];

                if ($mba_alumni_job) {
                    $s_institution_id = $mba_alumni_job[0]->institution_id;
                    $this->Insm->insert_institution($a_prep_institution, $s_institution_id);
                    $this->Insm->insert_academic_history($a_prep_academic_history, $mba_alumni_job[0]->academic_history_id);
                }else{
                    $s_institution_id = $this->uuid->v4();
                    $s_academic_history_id = $this->uuid->v4();
                    $a_prep_institution['institution_id'] = $s_institution_id;
                    $a_prep_academic_history['institution_id'] = $s_institution_id;
                    $a_prep_academic_history['academic_history_id'] = $s_academic_history_id;
                    $a_prep_academic_history['date_added'] = date('Y-m-d H:i:s');

                    $this->Insm->insert_institution($a_prep_institution);
                    $this->Insm->insert_academic_history($a_prep_academic_history);
                }

                $mba_institution_contact = $this->Insm->get_institution_contact([
                    'ic.institution_id' => $s_institution_id,
                    'pd.personal_data_name' => set_value('personal_data_supervisor')
                ]);

                if (!$mba_institution_contact) {
                    $s_personal_data_spv = $this->uuid->v4();
                    $a_prep_personal_data_spv = [
                        'personal_data_id' => $s_personal_data_spv,
                        'personal_data_name' => set_value('personal_data_supervisor'),
                        'personal_data_cellular' => 0
                    ];
                    $this->Pdm->create_personal_data_parents($a_prep_personal_data_spv);
                    $this->Insm->save_institution_contact($s_institution_id, $s_personal_data_spv);
                }else{
                    $s_personal_data_spv = $mba_institution_contact[0]->personal_data_id;
                }
                
                $mba_alumni_job_list = $this->Pdm->get_academic_history($s_personal_data_id, [
                    'academic_history_this_job' => 'yes'
                ]);

                if ($mba_alumni_job_list) {
                    $mbo_userdata = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
                    foreach ($mba_alumni_job_list as $key => $o_job) {
                        $this->Insm->insert_academic_history(['status' => 'active'], $o_job->academic_history_id);
                    }

                    $mbo_student_alumni = $this->General->get_where('dt_student_alumni', ['student_id' => $this->session->userdata('student_id')])[0];
                    if ($mbo_student_alumni) {
                        $this->Stm->save_student_alumni(['alumni_has_filled_job' => 'yes'], $mbo_student_alumni->alumni_id);
                    }else{
                        $a_return = array('code' => 1, 'message' => 'Failed updating your data, please contact IT Team!');
                        print json_encode($a_return);exit;
                    }

                    $this->session->set_userdata('has_working', 'yes');
                    $s_user_name = $mbo_userdata[0]->personal_data_name;
                    $s_company_name = set_value('institution_name');
                    $s_company_phone = set_value('institution_phone_number');
                    $s_company_email = set_value('institution_email');
                    $s_company_supervisor = set_value('personal_data_supervisor');

                    $s_body_message = <<<TEXT
{$s_user_name} has filled company data:
Company/Institution: {$s_company_name}
Email: {$s_company_email}
Phone Number: {$s_company_phone}
Supervisor: {$s_company_supervisor}
TEXT;

                    $a_email = $this->config->item('email');
                    // $config = $this->config->item('mail_config');
                    // $config['mailtype'] = 'html';
                    // $this->email->initialize($config);

                    $this->email->from($a_email['it']['main'], 'IULI Notification');
                    $this->email->to([$a_email['it']['members'][0], $a_email['alumni_affair']['main']]);
                    $this->email->subject('[Alumni Notification] Alumni fill job data');
                    $this->email->message($s_body_message);
                    if(!$this->email->send()){
                        $this->log_activity('Email did not sent');
                        $this->log_activity('Error Message: '.$this->email->print_debugger());
                    }

                    if ($this->send_email_to_company(
                        set_value('personal_data_supervisor'),
                        set_value('institution_email'),
                        ucwords(strtolower($s_user_name))
                    )) {
                        $a_return = array('code' => 0, 'message' => 'Success!');
                    }else{
                        modules::run('messaging/send_email',
                            'employee@company.ac.id',
                            'Failed send email to company survey!',
                            json_encode(['personal_data_spv_id' => $s_personal_data_spv, 'personal_data_student_id' => $s_personal_data_id]),
                            'employee@company.ac.id',
                            false,
                            false,
                            '[Error]'
                        );
                    }

                    $a_return = array('code' => 0, 'message' => 'Success!');
                }else{
                    $a_return = array('code' => 1, 'message' => 'Failed processing your data!');
                }
            }else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }

    public function send_request_assesment()
    {
        if ($this->input->is_ajax_request()) {
            $s_institution_id = $this->input->post('s_institution_id');

            $mba_institution_data = $this->General->get_where('ref_institution', ['institution_id' => $s_institution_id]);
            if (($mba_institution_data) AND (!is_null($mba_institution_data[0]->institution_email))) {
                $mba_student_alumni = $this->General->get_where('dt_student_alumni', ['student_id' => $this->session->userdata('student_id')]);

                if ($mba_student_alumni) {
                    $mba_has_input_company_survey = $this->General->get_where('dikti_question_answers', ['student_id' => $this->session->userdata('student_id')]);
                    $mbo_userdata = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
                    if (!$mba_has_input_company_survey) {
                        if ($this->send_email_to_company(
                            $mba_institution_data[0]->institution_name,
                            $mba_institution_data[0]->institution_email,
                            ucwords(strtolower($mbo_userdata[0]->personal_data_name))
                        )) {
                            $a_return = array('code' => 0, 'message' => 'Success!');
                        }else{
                            $a_email = $this->config->item('email');
                            modules::run('messaging/send_email',
                                $a_email['it']['members'][0],
                                'Failed send email to company survey!',
                                json_encode(['personal_data_spv_id' => $s_personal_data_spv, 'personal_data_student_id' => $s_personal_data_id]),
                                'employee@company.ac.id',
                                false,
                                false,
                                '[Error]'
                            );

                            $a_return = array('code' => 1, 'message' => 'Email not send, please try again later!');
                        }
                    }else{
                        $a_return = ['code' => 1, "You have been assessed by a company, can't make requests anymore"];
                    }
                }else{
                    $a_return = ['code' => 1, 'message' => "You are not registered as a graduate alumni"];
                }
            }else{
                $a_return = ['code' => 1, 'message' => "Make sure the company email is filled!", 'data' => $mba_institution_data];
            }

            print json_encode($a_return);
        }
    }

    public function send_email_to_company(
        $s_spv_name,
        $s_spv_mail,
        $alumni_name
    )
    {
        $a_email = $this->config->item('email');
        $s_links = 'https://survey.iuli.ac.id/';
        $s_links_scrim = '<a href="'.$s_links.'">link</a>';
        // $s_links_idn = '<a href="'.$s_links.'">Link ini</a>';
        $s_body_message = <<<TEXT
<p>Dear Sir/Madam</p>
<p>We have acquired information from our alumni, {$alumni_name} confirming that he/she is working or has worked in your company/institution.</p>
<p>Following the requirement of receiving feedback from IULI graduate users,  given by the DIREKTORAT PENDIDIKAN TINGGI KEMENTRIAN PENDIDIKAN DAN KEBUDAYAAN REPUBLIK INDONESIA, we kindly ask you to fill in the <b>graduate user satisfaction feedback form</b> via the following {$s_links_scrim}:</p>
<p>{$s_links}</p>
<p>We hope that your feedback can contribute to the improvement of the quality of education in IULI.</p>
<p>Thank you very much for your cooperation. We truly appreciate your help.</p>
<p>Sincerly</p>
<p></p>
<p></p>
<p>International University Liaison Indonesia</p>
<p>Associate Tower 7th Floor.</p>
<p>Intermark Indonesia BSD</p>
<p>Jl. Lingkar Timur BSD Serpong</p>
<p>Tangerang Selatan 15310</p>
<p>Phone: +62 (0) 852 123 18000</p>
<p></p>
<p></p>
<hr>
<p></p>
<p></p>
<p>Dengan Hormat</p>
<p>Sesuai informasi yang kami dapatkan dari lulusan perguruan tinggi kami atas nama {$alumni_name} bahwa yang bersangkutan telah bekerja di perusahaan/institusi bapak/ibu.</p>
<p>Sehubungan diperlukan masukan dari pihak pengguna lulusan IULI sesuai dengan permintaan dari DIREKTORAT PENDIDIKAN TINGGI KEMENTRIAN PENDIDIKAN DAN KEBUDAYAAN REPUBLIK INDONESIA, maka dengan ini kami memohon dengan hormat kepada bapak/ibu untuk memberikan <b>penilaian kepuasan pengguna lulusan</b>, pada {$s_links_scrim}:</p>
<p>{$s_links}</p>
<p>Semoga masukan penilaian ini dapat memberikan kontribusi dalam peningkatan kualitas pendidikan di IULI.</p>
<p>Atas bantuan dan kerjasama bapak/ibu, kami ucapkan terima kasih.</p>
<p>Hormat Kami</p>
<p></p>
<p></p>
<p>International University Liaison Indonesia</p>
<p>Associate Tower 7th Floor.</p>
<p>Intermark Indonesia BSD</p>
<p>Jl. Lingkar Timur BSD Serpong</p>
<p>Tangerang Selatan 15310</p>
<p>Phone: +62 (0) 852 123 18000</p>
TEXT;

        // 
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'mail.iuli.ac.id',
            'smtp_port' => 587,
            'smtp_user' => 'employee@company.ac.id',
            'smtp_pass' => 'kovid-19-2020',
            'mailtype'  => 'html', 
            'smtp_crypto' => 'tls',
            'smtp_timeout' => 10,
            'priority' => 1,
            'charset'   => 'utf-8'
        );
        $this->load->library('email');
        $this->email->initialize($config);
        
        $this->email->from($a_email['rectorate']['vice_of_academic'], 'International University Liaison Indonesia');
        $this->email->to([$s_spv_mail]);
        $this->email->bcc($a_email['it']['members'][0]);
        $this->email->subject('[IULI] Alumni User Satisfaction Assesment');
        $this->email->message($s_body_message);
        if(!$this->email->send()){
			$this->log_activity('Email did not sent');
            $this->log_activity('Error Message: '.$this->email->print_debugger());
			return false;
		}
		else{
			return true;
		}
    }

    public function delete_job_history()
    {
        if ($this->input->post()) {
            $a_clause = array(
                'academic_history_id' => $this->input->post('academic_history_id')
            );

            $mbo_academic_history_data = $this->Pdm->get_academic_filtered($a_clause, true);
            if (($mbo_academic_history_data) AND ($mbo_academic_history_data[0]->academic_history_main == 'no')) {
                if ($this->Pdm->delete_academic_history($a_clause)) {
                    $return  = array('code' => 0, 'message' => "Data has been deleted");
                }else{
                    $return  = array('code' => 1, 'message' => "Cann't delete job history");
                }
            }else{
                $return  = array('code' => 1, 'message' => "Cann't delete job history data");
            }
        }else{
            $return  = array('code' => 1, 'message' => "Nothing deleted");
        }

        print json_encode($return);
    }

    public function save_job_history()
    {
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->session->userdata('user');
            $i_school_found_status = $this->input->post('company_found_status');
            $s_institution_id = $this->input->post('institution_id');
            $s_occupation_id = $this->input->post('institution_occupation_id');

            if ($i_school_found_status == 0) {
                $this->form_validation->set_rules('institution_name','Company name', 'trim|required');
                $this->form_validation->set_rules('institution_address','Company address', 'trim|required');
                $this->form_validation->set_rules('institution_phone_number','Company phone number', 'trim|required|numeric');
                $this->form_validation->set_rules('institution_email','Company email', 'trim|required|valid_email');
                $this->form_validation->set_rules('institution_country_id','Company country', 'trim|required');
                // $this->form_validation->set_rules('institution_country','Company country', 'trim|required');
                // $this->form_validation->set_rules('institution_province','Company province', 'trim|required');
                $this->form_validation->set_rules('institution_city','Company city', 'trim|required');
                // $this->form_validation->set_rules('institution_zipcode','Company zipcode', 'trim|required|numeric|max_length[5]');
            }

            $this->form_validation->set_rules('company_start_date', 'Start Date', 'trim|required');
            if ($this->input->post('string_still_working') == 'no') {
                $this->form_validation->set_rules('company_end_date', 'End Date', 'trim|required');
            }
            $this->form_validation->set_rules('institution_occupation', 'Job Title', 'trim|required');
            $s_still_working = $this->input->post('is_available');

            $s_err_message = '';
            $b_this_main = false;
            
            if ($this->form_validation->run()) {
                $this->db->trans_start();

                if ($s_occupation_id == '') {
                    $s_occupation_id = $this->uuid->v4();
                    $a_occupation_data = array(
                        'ocupation_id' => $s_occupation_id,
                        'ocupation_name' => strtoupper(set_value('institution_occupation'))
                    );
                    $this->Insm->save_occupation($a_occupation_data);
                }

                $b_save_institution = false;
                // if ($i_school_found_status == 0) {
                    $s_school_name = strtoupper(set_value('institution_name'));
                    $mbo_company_exists = $this->Insm->get_institution_data([
                        'institution_name' => set_value('institution_name'),
                        'institution_email' => set_value('institution_email'),
                        'institution_type' => 'office'
                    ]);
                    // $mbo_company_exists = $this->Insm->institution_suggestions($s_school_name, false, true);
                    $a_institution_data = array(
                        'institution_name' => strtoupper(set_value('institution_name')),
                        'institution_phone_number' =>  set_value('institution_phone_number'),
                        'institution_email' => set_value('institution_email'),
                        'institution_type' => 'office'
                    );
    
                    $a_address_data = array(
                        'country_id' => set_value('institution_country_id'),
                        // 'address_province' => strtoupper(set_value('institution_province')),
                        'address_city' => strtoupper(set_value('institution_city')),
                        'address_street' => strtoupper(set_value('institution_address')),
                        // 'address_zipcode' => set_value('institution_zipcode'),
                        'date_added' => date('Y-m-d H:i:s')
                    );
                    
                    if (!$mbo_company_exists) {
                        if ($s_address_id = $this->Insm->insert_address($a_address_data)) {
                            $a_institution_data['address_id'] = $s_address_id;
                            if ($s_institution_id = $this->Insm->insert_institution($a_institution_data)) {
                                $b_save_institution = true;
                            }else{
                                $b_save_institution = false;
                                $s_err_message = 'processing institution data';
                            }
                        }else{
                            $b_save_institution = false;
                            $s_err_message = 'processing address data';
                        }
                    }else{
                        $mbo_company_exists = $mbo_company_exists[0];
                        if (is_null($mbo_company_exists->address_id)) {
                            $s_address_id = $this->Insm->insert_address($a_address_data);
                        }else{
                            $s_address_id = $mbo_company_exists->address_id;
                            $this->Insm->insert_address($a_address_data, $s_address_id);
                        }
    
                        $a_institution_data['address_id'] = $s_address_id;
    
                        $this->Insm->insert_institution($a_institution_data, $mbo_company_exists->institution_id);
                        $s_institution_id = $mbo_company_exists->institution_id;
                        // $s_address_id = $mbo_company_exists->address_id;
                        $b_save_institution = true;
                    }
                // }else{
                //     $mbo_institution_data = $this->Insm->get_institution_by_id($s_institution_id);
                //     $s_address_id = $mbo_institution_data->address_id;
                //     $b_save_institution = true;
                // }

                if ($b_save_institution) {
                    $end_date_working = set_value('company_end_date');
                    if ($this->input->post('string_still_working') == 'yes'){
                        $end_date_working = NULL;
                    }
                    $a_academic_history_data = array(
                        'institution_id' => $s_institution_id,
                        'personal_data_id' => $s_personal_data_id,
                        'occupation_id' => $s_occupation_id,
                        'academic_year_start_date' => set_value('company_start_date'),
                        'academic_year_end_date' => $end_date_working,
                        'academic_history_this_job' => 'yes'
                    );

                    if ($this->input->post('academic_history_id') != '') {
                        $mbs_save_academic_history = $this->Insm->insert_academic_history($a_academic_history_data, $this->input->post('academic_history_id'));
                        if ($mbs_save_academic_history) {
                            $s_academic_history_id = $mbs_save_academic_history;
                        }else{
                            $s_err_message = 'processing academic history';
                        }
                    }else{
                        if ($this->Pdm->get_academic_history($s_personal_data_id, true)) {
                            $a_academic_history_data['academic_history_main'] = 'no';
                            $b_this_main = false;
                        }else{
                            $a_academic_history_data['academic_history_main'] = 'yes';
                            $b_this_main = true;
                        }

                        // if ($this->Pdm->get_academic_filtered($a_academic_history_data, true)) {
                        //     $s_err_message = 'nothing saved';
                        // }else{
                            $mbs_save_academic_history = $this->Insm->insert_academic_history($a_academic_history_data);
                            if ($mbs_save_academic_history) {
                                $s_academic_history_id = $mbs_save_academic_history;
                            }else{
                                $s_err_message = 'processing academic history';
                            }
                        // }
                    }
                }else{
                    $s_err_message = 'processing data';
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $rtn = array('code' => 1, 'message' => $s_err_message);
                }else{
                    $this->db->trans_commit();
                    $rtn = array('code' => 0);

                    if ($this->General->is_student_candidate($s_personal_data_id)) {
                        if ($b_this_main) {
                            $a_token_config = $this->config->item('token')['pmb'];
                            $a_sites = $this->config->item('sites');
                            $s_token = $a_token_config['access_token'];
                            $s_secret_token = $a_token_config['secret_token'];
                            $url = $a_sites['pmb'];
                            
                            $a_sync_data = array();
                            array_push($a_sync_data, $this->libapi->prepare_data('dt_address', array('address_id' => $s_address_id)));
                            array_push($a_sync_data, $this->libapi->prepare_data('ref_institution', array('institution_id' => $s_institution_id)));
                            array_push($a_sync_data, $this->libapi->prepare_data('dt_academic_history', array('academic_history_id' => $s_academic_history_id)));
                            
                            $sync_data = array(
                                'sync_data' => $a_sync_data
                            );
                            
                            $hashed_string = $this->libapi->hash_data($sync_data, $s_token, $s_secret_token);
                            $post_data = json_encode(array(
                                'access_token' => 'PORTALIULIACID',
                                'data' => $hashed_string
                            ));
                            
                            $a_result = $this->libapi->post_data($url.'api/portal/sync_all', $post_data);
                            if ($a_result != null) {
                                $this->Acm->update_result_sync(json_decode(json_encode($a_result->a_return_data), true), intval($a_result->code));
                            }
                        }
                    }
                    // else{
                    //     $mba_student_alumni = $this->General->get_where('dt_student_alumni', ['student_id' => $this->session->userdata('student_id')]);
                        // $mbo_userdata = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
                    //     if ($mba_student_alumni) {
                    //         $mba_has_input_company_survey = $this->General->get_where('dikti_question_answers', ['student_id' => $this->session->userdata('student_id')]);
                    //         if (!$mba_has_input_company_survey) {
                    //             if ($this->send_email_to_company(
                    //                 set_value('institution_occupation'),
                    //                 set_value('institution_email'),
                    //                 ucwords(strtolower($mbo_userdata[0]->personal_data_name))
                    //             )) {
                    //                 // $a_return = array('code' => 0, 'message' => 'Success!');
                    //             }else{
                    //                 modules::run('messaging/send_email',
                    //                     $a_email['it']['members'][0],
                    //                     'Failed send email to company survey!',
                    //                     json_encode(['personal_data_spv_id' => $s_personal_data_spv, 'personal_data_student_id' => $s_personal_data_id]),
                    //                     'employee@company.ac.id',
                    //                     false,
                    //                     false,
                    //                     '[Error]'
                    //                 );
                    //             }
                    //         }
                    //     }
                    // }
                }
            }else{
                $rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            echo json_encode($rtn);
            exit;
        }
    }
}
