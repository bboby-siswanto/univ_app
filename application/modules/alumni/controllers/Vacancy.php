<?php
class Vacancy extends App_core
{
    function __construct()
    {
        $s_user = ($this->session->userdata('type') == 'staff') ? 'staff_alumni' : 'student_alumni';
        parent::__construct($s_user);
        $this->load->model('institution/Institution_model', 'Insm');
        $this->load->model('alumni/Vacancy_model', 'Vcm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
    }

    public function lists_vacancy()
    {
        // var_dump($this->session->userdata());exit;
        $this->a_page_data['body'] = $this->load->view('vacancy/default', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function job_detail($s_job_vacancy_id = false)
    {
        if ($s_job_vacancy_id) {
            $mbo_job_vacancy_data = $this->Vcm->get_job_vacancy(false, array('post_status' => 'open', 'job_vacancy_id' => $s_job_vacancy_id))[0];
            if ($mbo_job_vacancy_data) {
                $s_requirements = '';
                if (!is_null($mbo_job_vacancy_data->requirements)) {
                    $a_requirements = explode(PHP_EOL, $mbo_job_vacancy_data->requirements);
                    $s_requirements .= '<ul>';
                    foreach ($a_requirements as $requirements) {
                        $s_requirements .= '<li>'.$requirements.'</li>';
                    }
                    $s_requirements .= '</ul>';
                }

                $s_jobdesc = '';
                if (!is_null($mbo_job_vacancy_data->job_description)) {
                    $a_description = explode(PHP_EOL, $mbo_job_vacancy_data->job_description);
                    $s_jobdesc .= '<ul>';
                    foreach ($a_description as $description) {
                        $s_jobdesc .= '<li>'.$description.'</li>';
                    }
                    $s_jobdesc .= '</ul>';
                }

                $this->a_page_data['real_job_description'] = $s_jobdesc;
                $this->a_page_data['real_job_requirements'] = $s_requirements;
                $this->a_page_data['o_job_vacancy_data'] = $mbo_job_vacancy_data;
                $this->a_page_data['body'] = $this->load->view('vacancy/job_detail', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }else{
                redirect('vacancy');
            }
        }
    }

    public function vacancy_lists()
    {
        $this->load->view('alumni/vacancy/vacancy_lists', $this->a_page_data);
    }

    public function my_vacancies()
    {
        $s_btn_html = Modules::run('layout/generate_buttons', 'alumni', 'job_vacancy');
        $this->a_page_data['btn_html'] = $s_btn_html;
        // $this->load->view('table/table_vacancy_lists', $this->a_page_data);
        $this->a_page_data['body'] = $this->load->view('alumni/vacancy/table/table_vacancy_lists', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function form_input_vacancy()
    {
        $s_personal_data_id = $this->session->userdata('user');
        $o_job_vacancy_data = false;
        if ($this->input->is_ajax_request()) {
            $s_job_vacancy_id = $this->input->post('job_vacancy_id');
            $o_job_vacancy_data = $this->Vcm->get_job_vacancy($s_personal_data_id, array('job_vacancy_id' => $s_job_vacancy_id))[0];
            $this->a_page_data['o_academic_history_data'] = $o_job_vacancy_data;
            $s_html = $this->load->view('vacancy/form/form_input_vacancy', $this->a_page_data, true);
            print json_encode(array('code' => 0, 'data' =>$s_html));exit;
        }else{
            $this->load->view('vacancy/form/form_input_vacancy', $this->a_page_data);
        }
    }

    public function get_data_filtered($extend = 'true')
    {
        $s_personal_data_id = $this->session->userdata('user');
        if ($this->input->is_ajax_request()) {
            if($extend == 'false'){
                $mbo_job_vacancy = $this->Vcm->get_job_vacancy(false, array('post_status' => 'open'));
            }else {
                $mbo_job_vacancy = $this->Vcm->get_job_vacancy($s_personal_data_id);
            }
            if ($mbo_job_vacancy) {
                foreach ($mbo_job_vacancy as $vacancy) {
                    $vacancy->job_vacancy_site = (is_null($vacancy->job_vacancy_site)) ? '' : $vacancy->job_vacancy_site;
                }
            }
            print json_encode(array('code' => 0, 'data' => $mbo_job_vacancy));
        }
    }

    public function remove_job_vacancy()
    {
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->session->userdata('user');
            $s_job_vacancy_id = $this->input->post('job_vacancy_id');
            $a_vacancy_data = array(
                'post_status' => 'deleted'
            );

            if ($this->Vcm->save_job_vacancy($a_vacancy_data, $s_job_vacancy_id)) {
                $a_rtn = array('code' => 0, 'message' => 'Success');
            }else{
                $a_rtn = array('code' => 1, 'message' => 'Error removing job vacancy');
            }

            print json_encode($a_rtn);
        }
    }

    public function prop_status()
    {
        if ($this->input->is_ajax_request()) {
            $s_job_vacancy_id = $this->input->post('job_vacancy_id');
            $s_status = $this->input->post('status');
            $a_vacancy_data = array(
                'post_status' => ($s_status == 'open') ? 'close' : 'open'
            );

            if ($this->Vcm->save_job_vacancy($a_vacancy_data, $s_job_vacancy_id)) {
                $a_rtn = array('code' => 0, 'message' => 'Success');
            }else{
                $a_rtn = array('code' => 1, 'message' => 'Error change data vacancy');
            }

            print json_encode($a_rtn);
        }
    }

    public function save_job_vacancy()
    {
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->session->userdata('user');

            $s_institution_id = $this->input->post('institution_id');
            $s_occupation_id = $this->input->post('occupation_id');
            $s_job_vacancy_id = $this->input->post('job_vacancy_id');

            $this->form_validation->set_rules('ocupation_name', 'Occupation', 'required|trim');
            $this->form_validation->set_rules('requirements', 'Requirements', 'trim|required|max_length[500]');
            $this->form_validation->set_rules('job_description', 'Job Description', 'trim|max_length[500]');
            $this->form_validation->set_rules('job_vacancy_email', 'Email', 'trim|valid_email|required');
            $this->form_validation->set_rules('job_vacancy_site', 'URL Website', 'trim');

            if ($s_institution_id == '') {
                $this->form_validation->set_rules('institution_name','Company name', 'trim|required');
                $this->form_validation->set_rules('address_street','Company address', 'trim|required');
                $this->form_validation->set_rules('institution_phone_number','Company phone number', 'trim|required|numeric');
                $this->form_validation->set_rules('institution_email','Company email', 'trim|required|valid_email');
                $this->form_validation->set_rules('country_name','Company country', 'trim|required');
                $this->form_validation->set_rules('address_province','Company province', 'trim|required');
                $this->form_validation->set_rules('address_city','Company city', 'trim|required');
                $this->form_validation->set_rules('address_zipcode','Company zipcode', 'trim|required|numeric|max_length[5]');
            }

            if ($this->form_validation->run()) {
                $this->db->trans_start();

                if ($s_institution_id == '') {
                    $s_institution_name = strtoupper(set_value('institution_name'));
                    $mbo_company_exists = $this->Insm->institution_suggestions($s_institution_name, false, true);
                    if ($mbo_company_exists) {
                        $s_institution_id = $mbo_company_exists[0]->institution_id;
                    }else{
                        $a_institution_data = array(
                            'institution_name' => strtoupper(set_value('institution_name')),
                            'institution_phone_number' =>  set_value('institution_phone_number'),
                            'institution_email' => set_value('institution_email'),
                            'institution_type' => 'office'
                        );

                        $a_address_data = array(
                            'country_id' => set_value('country_id'),
                            'address_province' => strtoupper(set_value('address_province')),
                            'address_city' => strtoupper(set_value('address_city')),
                            'address_street' => strtoupper(set_value('address_street')),
                            'address_zipcode' => set_value('address_zipcode'),
                            'date_added' => date('Y-m-d H:i:s')
                        );

                        if ($s_address_id = $this->Insm->insert_address($a_address_data)) {
                            $a_institution_data['address_id'] = $s_address_id;
                            if ($s_institution_id = $this->Insm->insert_institution($a_institution_data)) {
                                $b_save_institution = true;
                            }else{
                                $a_rtn = array('code' => 1, 'message' => 'Eror saving company');exit;
                            }
                        }else{
                            $a_rtn = array('code' => 1, 'message' => 'Eror saving company data');exit;
                        }
                    }
                }

                if ($s_occupation_id == '') {
                    
                    $s_occupation_name = strtoupper(set_value('ocupation_name'));

                    $mbo_occupation_exists = $this->Insm->get_occupation_sugestion($s_occupation_name, true);
                    $a_occupation_data = array(
                        'ocupation_name' => $s_occupation_name
                    );

                    if ($mbo_occupation_exists) {
                        $s_occupation_id = $mbo_occupation_exists[0]->ocupation_id;
                        $this->Insm->save_occupation($a_occupation_data, $s_occupation_id);
                    }else{
                        $s_occupation_id = $this->uuid->v4();
                        $a_occupation_data['ocupation_id'] = $s_occupation_id;
                        $this->Insm->save_occupation($a_occupation_data);
                    }
                }

                $a_job_vacancy_data = array(
                    'personal_data_id' => $s_personal_data_id,
                    'institution_id' => $s_institution_id,
                    'occupation_id' => $s_occupation_id,
                    'job_description' => (set_value('job_description') == '') ? NULL : set_value('job_description'),
                    'requirements' => set_value('requirements'),
                    'job_vacancy_email' => set_value('job_vacancy_email'),
                    'job_vacancy_site' => (set_value('job_vacancy_site') == '') ? NULL : str_replace(' ', '', set_value('job_vacancy_site'))
                );

                if ($s_job_vacancy_id == '') {
                    $a_job_vacancy_data['job_vacancy_id'] = $this->uuid->v4();
                    $this->Vcm->save_job_vacancy($a_job_vacancy_data);
                }else{
                    $this->Vcm->save_job_vacancy($a_job_vacancy_data, $s_job_vacancy_id);
                }

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    $a_rtn = array('code' => 1, 'message' => 'Error saving data');
                }else{
                    $this->db->trans_commit();
                    $a_rtn = array('code' => 0, 'message' => 'Success');
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }
        }else{
            $a_rtn = array('code' => 1, 'message' => 'Nothing action');
        }

        print json_encode($a_rtn);exit;
    }

    function valid_url_format($str){
        $pattern = "|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i";
        if (!preg_match($pattern, $str)){
            $this->form_validation->set_message('valid_url_format', 'The {field} you entered is not correctly formatted.');
            return FALSE;
        }else{
            return TRUE;
        }
    }
}
