<?php
class Semester extends App_core
{
    public $s_rector_email = false;
    public $s_vice_rector_email = false;
    public $a_deans_email = [];
    function __construct()
    {
        parent::__construct('academic');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('Academic_year_model', 'Aym');
        $this->load->model('student/Student_model', 'Stm');

        $this->s_rector_email = $this->config->item('email')['rectorate']['rector'];
        $this->s_vice_rector_email = $this->config->item('email')['rectorate']['vice_of_academic'];
        $a_deans_id = [];
        $mba_faculty_data = $this->General->get_where('ref_faculty');
        if ($mba_faculty_data) {
            foreach ($mba_faculty_data as $o_faculty) {
                if (!in_array($o_faculty->deans_id, $a_deans_id)) {
                    array_push($a_deans_id, $o_faculty->deans_id);
                }
            }
        }

        if (count($a_deans_id) > 0) {
            foreach ($a_deans_id as $s_personal_data_id) {
                $mbo_employee_data = $this->General->get_where('dt_employee', ['personal_data_id' => $s_personal_data_id])[0];
                array_push($this->a_deans_email, $mbo_employee_data->employee_email);
            }
        }
    }

    public function semester_lists($s_academic_year_id = false, $s_semester_type_id = false)
    {
        $s_user_login_type = $this->session->userdata('type');
        if ($s_user_login_type == 'staff') {
            $mbo_employee_data = $this->General->get_where('dt_employee', ['employee_id' => $this->session->userdata('employee_id')]);
            if ($mbo_employee_data) {
                $mbo_employee_data = $mbo_employee_data[0];
                $this->a_page_data['approval'] = (($this->s_rector_email == $mbo_employee_data->employee_email) OR ($this->s_vice_rector_email == $mbo_employee_data->employee_email) OR (in_array($mbo_employee_data->employee_email, $this->a_deans_email))) ? true : false;
            }
            else {
                $this->a_page_data['approval'] = false;
            }
            // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
            //     $this->a_page_data['approval'] = true;
            // }
            if ($s_academic_year_id AND $s_semester_type_id) {
                
                $mba_semester_data = $this->Smm->get_semester_setting(array('dss.academic_year_id' => $s_academic_year_id, 'dss.semester_type_id' => $s_semester_type_id))[0];
                $requested_data = false;
                if ($this->a_page_data['approval']) {
                    $mba_request = $this->Smm->get_semester_setting_request(['ssr.academic_year_id' => $mba_semester_data->academic_year_id, 'ssr.semester_type_id' => $mba_semester_data->semester_type_id])[0];
                    if (($mba_request) AND (is_null($mba_request->personal_data_id_approve))) {
                        $requested_data = $mba_request;
                        $mba_semester_data = $mba_request->request_json;
                        $mba_semester_data = json_decode($mba_semester_data);
                    }
                }

                if ($mba_semester_data) {
                    $this->a_page_data['requested'] = $requested_data;
                    $this->a_page_data['semester_data'] = $mba_semester_data;
                    $this->a_page_data['body'] = $this->load->view('semester/semester_details', $this->a_page_data, true);
                }else {
                    $this->a_page_data['body'] = $this->load->view('semester/semester_lists', $this->a_page_data, true);
                }
            }else{
                $this->a_page_data['body'] = $this->load->view('semester/semester_lists', $this->a_page_data, true);
            }

            $this->load->view('layout', $this->a_page_data);
        }else{
            print('User type not allowed!!!');exit;
        }
    }

    public function form_input_semester()
    {
        $this->a_page_data['semester_type_lists'] = $this->Smm->get_semester_type_lists(false, false, array(1, 2, 3));
        $this->a_page_data['academic_year_lists'] = $this->Aym->get_academic_year_lists();
        $this->load->view('semester/form/form_input_semester', $this->a_page_data);
    }

    public function semester_table()
    {
        $this->a_page_data['btn_html'] = modules::run('layout/generate_buttons', 'academic', 'semester_settings');
        $this->load->view('semester/table/semester_table', $this->a_page_data);
    }

    public function filter_semester_lists()
    {
        if ($this->input->is_ajax_request()) {
            $a_filter_data = false;

            $mba_semester_lists = $this->Smm->get_semester_setting($a_filter_data);
            if ($mba_semester_lists) {
                foreach ($mba_semester_lists as $o_semester_settings) {
                    $mbo_request = $this->Smm->get_semester_setting_request(['ssr.academic_year_id' => $o_semester_settings->academic_year_id, 'ssr.semester_type_id' => $o_semester_settings->semester_type_id]);
                    $mbo_request = ($mbo_request) ? $mbo_request[0] : false;
                    $o_semester_settings->request_approval = $mbo_request;
                }
            }
            print json_encode(array('code' => 0, 'data' => $mba_semester_lists));exit;
        }
    }

    public function semester_setting_master_save()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('academic_year_id', 'Academic year', 'trim|required');
            $this->form_validation->set_rules('semester_type_id', 'Semester type', 'trim|required');
            $this->form_validation->set_rules('semester_start_date', 'Semester Period Start Date', 'trim|required');
            $this->form_validation->set_rules('semester_end_date', 'Semester Period End Date', 'trim|required');
            
            if ($this->form_validation->run()) {
                $s_now = date('Y-m-d H:i:s');
                $start_date_semester = date('Y-m-d H:i:s', strtotime(set_value('semester_start_date')));
                $end_date_semester = date('Y-m-d H:i:s', strtotime(set_value('semester_end_date')));
                $s_status = (($s_now >= $start_date_semester) AND ($s_now <= $end_date_semester)) ? 'active' : 'inactive';

                if ($this->Smm->get_semester_setting(array('dss.academic_year_id' => set_value('academic_year_id'), 'dss.semester_type_id' => set_value('semester_type_id')))) {
                    $a_rtn = array('code' => 1, 'message' => 'Semester with academic year '.set_value('academic_year_id').'/'.set_value('semester_type_id').' is active');
                }else if(!$this->validate_date_semester_settings('semester_start_date', 'semester_end_date', set_value('semester_start_date'))){
                    $a_rtn = array('code' => 1, 'message' => 'Semester Start Date is availabe');
                }else if(!$this->validate_date_semester_settings('semester_start_date', 'semester_end_date', set_value('semester_end_date'))){
                    $a_rtn = array('code' => 1, 'message' => 'Semester End Date is availabe');
                }else {
                    $this->db->trans_start();
                    $a_semester_settings_data = array(
                        'academic_year_id' => set_value('academic_year_id'),
                        'semester_type_id' => set_value('semester_type_id'),
                        'semester_start_date' => set_value('semester_start_date').' 00:00:00',
                        'semester_end_date' => set_value('semester_end_date').' 23:59:59',
                        'semester_status' => $s_status
                    );

                    if ($this->Smm->save_semester_setttings($a_semester_settings_data)) {
                        if (!$this->semester_setting_save_students($a_semester_settings_data)) {
                            $this->db->trans_rollback();
                            $a_rtn = array('code' => 1, 'message' => 'Error save student in this semester!');
                            print json_encode($a_rtn);exit;
                        }
                    }else {
                        $a_rtn = array('code' => 1, 'message' => 'Error saving data!');
                    }

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                    }else{
                        $this->db->trans_commit();
                        $a_rtn = array('code' => 0, 'message' => 'Success!');
                    }
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_rtn);
        }
    }

    public function semester_setting_save_students($a_semester_setting_data)
    {
        if (is_object($a_semester_setting_data)) {
            $a_semester_setting_data = (array)$a_semester_setting_data;
        }
        $mba_checked_student_semester = $this->Smm->get_student_semester(array(
            'dss.academic_year_id' => $a_semester_setting_data['academic_year_id'],
            'dss.semester_type_id' => $a_semester_setting_data['semester_type_id']
        ));

        if ($mba_checked_student_semester) {
            $this->Smm->remove_student_semeter(array(
                'academic_year_id' => $a_semester_setting_data['academic_year_id'],
                'semester_type_id' => $a_semester_setting_data['semester_type_id']
            ));
        }
        $mba_student_active = $this->Stm->get_student_filtered(array('student_status' => 'active', 'ds.academic_year_id  <=' => $a_semester_setting_data['academic_year_id']));
        if ($mba_student_active) {
            foreach ($mba_student_active as $student) {
                $a_student_semester_data = array(
                    'student_id' => $student->student_id,
                    'semester_type_id' => $a_semester_setting_data['semester_type_id'],
                    'academic_year_id' => $a_semester_setting_data['academic_year_id'],
                    'student_semester_status' => 'inactive',
                    'date_added' => date('Y-m-d H:i:s')
                );

                if (!$this->Smm->save_student_semester($a_student_semester_data)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function semester_setting_detail_save_old()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('semester_status', 'Semester Status', 'trim|required');

            $s_semester_start_date = $this->input->post('semester_start_date');
            $s_semester_end_date = $this->input->post('semester_end_date');
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_dikti_report_deadline = $this->input->post('dikti_report_deadline');

            $s_regular_offer_subject_end_date = $this->input->post('regular_offer_subject_end_date');
            $s_short_semester_offer_subject_end_date = $this->input->post('short_semester_offer_subject_end_date');
            $s_ofse_offer_subject_end_date = $this->input->post('ofse_offer_subject_end_date');

            $s_regular_study_plan_start_date = $this->input->post('regular_study_plan_start_date');
            $s_regular_study_plan_end_date = $this->input->post('regular_study_plan_end_date');
            $s_short_semester_study_plan_start_date = $this->input->post('short_semester_study_plan_start_date');
            $s_short_semester_study_plan_end_date = $this->input->post('short_semester_study_plan_end_date');
            $s_ofse_study_plan_start_date = $this->input->post('ofse_study_plan_start_date');
            $s_ofse_study_plan_end_date = $this->input->post('ofse_study_plan_end_date');

            $s_regular_study_plan_approval_end_date = $this->input->post('regular_study_plan_approval_end_date');
            $s_short_semester_study_plan_approval_end_date = $this->input->post('short_semester_study_plan_approval_end_date');
            $s_ofse_study_plan_approval_end_date = $this->input->post('ofse_study_plan_approval_end_date');

            $s_regular_repetition_registration_start_date = $this->input->post('regular_repetition_registration_start_date');
            $s_regular_repetition_registration_end_date = $this->input->post('regular_repetition_registration_end_date');
            $s_ofse_repetition_registration_start_date = $this->input->post('ofse_repetition_registration_start_date');
            $s_ofse_repetition_registration_end_date = $this->input->post('ofse_repetition_registration_end_date');

            if ($this->form_validation->run()) {
                $a_semester_settings_data = array(
                    'offer_subject_end_date' => ($s_regular_offer_subject_end_date != '') ? $s_regular_offer_subject_end_date.' 23:59:59' : null,
                    'offer_subject_short_semester_end_date' => ($s_short_semester_offer_subject_end_date != '') ? $s_short_semester_offer_subject_end_date.' 23:59:59' : null,
                    'offer_subject_ofse_end_date' => ($s_ofse_offer_subject_end_date != '') ? $s_ofse_offer_subject_end_date.' 23:59:59' : null,
                    'study_plan_start_date' => ($s_regular_study_plan_start_date != '') ? $s_regular_study_plan_start_date.' 00:00:00' : null,
                    'study_plan_end_date' => ($s_regular_study_plan_end_date != '') ? $s_regular_study_plan_end_date.' 23:59:59' : null,
                    'study_plan_short_semester_start_date' => ($s_short_semester_study_plan_start_date != '') ? $s_short_semester_study_plan_start_date.' 00:00:00' : null,
                    'study_plan_short_semester_end_date' => ($s_short_semester_study_plan_end_date != '') ? $s_short_semester_study_plan_end_date.' 23:59:59' : null,
                    'study_plan_ofse_start_date' => ($s_ofse_study_plan_start_date != '') ? $s_ofse_study_plan_start_date.' 00:00:00' : null,
                    'study_plan_ofse_end_date' => ($s_ofse_study_plan_end_date != '') ? $s_ofse_study_plan_end_date.' 23:59:59' : null,
                    'study_plan_approval_end_date' => ($s_regular_study_plan_approval_end_date != '') ? $s_regular_study_plan_approval_end_date.' 23:59:59' : null,
                    'study_plan_approval_short_semester_end_date' => ($s_short_semester_study_plan_approval_end_date != '') ? $s_short_semester_study_plan_approval_end_date.' 23:59:59' : null,
                    'study_plan_approval_ofse_end_date' => ($s_ofse_study_plan_approval_end_date != '') ? $s_ofse_study_plan_approval_end_date.' 23:59:59' : null,
                    'repetition_registration_start_date' => ($s_regular_repetition_registration_start_date != '') ? $s_regular_repetition_registration_start_date.' 00:00:00' : null,
                    'repetition_registration_end_date' => ($s_regular_repetition_registration_end_date != '') ? $s_regular_repetition_registration_end_date.' 23:59:59' : null,
                    'repetition_registration_ofse_start_date' => ($s_ofse_repetition_registration_start_date != '') ? $s_ofse_repetition_registration_start_date.' 00:00:00' : null,
                    'repetition_registration_ofse_end_date' => ($s_ofse_repetition_registration_end_date != '') ? $s_ofse_repetition_registration_end_date.' 23:59:59' : null,
                    'dikti_report_deadline' => ($s_dikti_report_deadline != '') ? $s_dikti_report_deadline.' 23:59:59' : null,
                    'semester_status' => set_value('semester_status')
                );

                if (set_value('semester_status') == 'active') {
                    $this->Smm->inactive_semester_academic();
                }
                
                $this->Smm->save_semester_setttings($a_semester_settings_data, array('academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id));
                $a_rtn = array('code' => 0, 'message' => 'Success');
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_rtn);
        }
    }

    public function semester_setting_detail_save()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('semester_status', 'Semester Status', 'trim|required');
            $this->form_validation->set_rules('request_note', 'Request Note', 'trim|required');

            $s_semester_start_date = $this->input->post('semester_start_date');
            $s_semester_end_date = $this->input->post('semester_end_date');
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_dikti_report_deadline = $this->input->post('dikti_report_deadline');

            $s_regular_offer_subject_end_date = $this->input->post('regular_offer_subject_end_date');
            $s_short_semester_offer_subject_end_date = $this->input->post('short_semester_offer_subject_end_date');
            $s_ofse_offer_subject_end_date = $this->input->post('ofse_offer_subject_end_date');

            $s_regular_study_plan_start_date = $this->input->post('regular_study_plan_start_date');
            $s_regular_study_plan_end_date = $this->input->post('regular_study_plan_end_date');
            $s_short_semester_study_plan_start_date = $this->input->post('short_semester_study_plan_start_date');
            $s_short_semester_study_plan_end_date = $this->input->post('short_semester_study_plan_end_date');
            $s_ofse_study_plan_start_date = $this->input->post('ofse_study_plan_start_date');
            $s_ofse_study_plan_end_date = $this->input->post('ofse_study_plan_end_date');

            $s_regular_study_plan_approval_end_date = $this->input->post('regular_study_plan_approval_end_date');
            $s_short_semester_study_plan_approval_end_date = $this->input->post('short_semester_study_plan_approval_end_date');
            $s_ofse_study_plan_approval_end_date = $this->input->post('ofse_study_plan_approval_end_date');

            $s_regular_repetition_registration_start_date = $this->input->post('regular_repetition_registration_start_date');
            $s_regular_repetition_registration_end_date = $this->input->post('regular_repetition_registration_end_date');
            $s_ofse_repetition_registration_start_date = $this->input->post('ofse_repetition_registration_start_date');
            $s_ofse_repetition_registration_end_date = $this->input->post('ofse_repetition_registration_end_date');

            if ($this->form_validation->run()) {
                $a_semester_settings_data = array(
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'semester_start_date' => $s_semester_start_date,
                    'semester_end_date' => $s_semester_end_date,
                    'offer_subject_end_date' => ($s_regular_offer_subject_end_date != '') ? $s_regular_offer_subject_end_date.' 23:59:59' : null,
                    'offer_subject_short_semester_end_date' => ($s_short_semester_offer_subject_end_date != '') ? $s_short_semester_offer_subject_end_date.' 23:59:59' : null,
                    'offer_subject_ofse_end_date' => ($s_ofse_offer_subject_end_date != '') ? $s_ofse_offer_subject_end_date.' 23:59:59' : null,
                    'study_plan_start_date' => ($s_regular_study_plan_start_date != '') ? $s_regular_study_plan_start_date.' 00:00:00' : null,
                    'study_plan_end_date' => ($s_regular_study_plan_end_date != '') ? $s_regular_study_plan_end_date.' 23:59:59' : null,
                    'study_plan_short_semester_start_date' => ($s_short_semester_study_plan_start_date != '') ? $s_short_semester_study_plan_start_date.' 00:00:00' : null,
                    'study_plan_short_semester_end_date' => ($s_short_semester_study_plan_end_date != '') ? $s_short_semester_study_plan_end_date.' 23:59:59' : null,
                    'study_plan_ofse_start_date' => ($s_ofse_study_plan_start_date != '') ? $s_ofse_study_plan_start_date.' 00:00:00' : null,
                    'study_plan_ofse_end_date' => ($s_ofse_study_plan_end_date != '') ? $s_ofse_study_plan_end_date.' 23:59:59' : null,
                    'study_plan_approval_end_date' => ($s_regular_study_plan_approval_end_date != '') ? $s_regular_study_plan_approval_end_date.' 23:59:59' : null,
                    'study_plan_approval_short_semester_end_date' => ($s_short_semester_study_plan_approval_end_date != '') ? $s_short_semester_study_plan_approval_end_date.' 23:59:59' : null,
                    'study_plan_approval_ofse_end_date' => ($s_ofse_study_plan_approval_end_date != '') ? $s_ofse_study_plan_approval_end_date.' 23:59:59' : null,
                    'repetition_registration_start_date' => ($s_regular_repetition_registration_start_date != '') ? $s_regular_repetition_registration_start_date.' 00:00:00' : null,
                    'repetition_registration_end_date' => ($s_regular_repetition_registration_end_date != '') ? $s_regular_repetition_registration_end_date.' 23:59:59' : null,
                    'repetition_registration_ofse_start_date' => ($s_ofse_repetition_registration_start_date != '') ? $s_ofse_repetition_registration_start_date.' 00:00:00' : null,
                    'repetition_registration_ofse_end_date' => ($s_ofse_repetition_registration_end_date != '') ? $s_ofse_repetition_registration_end_date.' 23:59:59' : null,
                    'dikti_report_deadline' => ($s_dikti_report_deadline != '') ? $s_dikti_report_deadline.' 23:59:59' : null,
                    'semester_status' => set_value('semester_status')
                );

                // if (set_value('semester_status') == 'active') {
                //     $this->Smm->inactive_semester_academic();
                // }
                // $this->Smm->save_semester_setttings($a_semester_settings_data, array('academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id));
                $a_data = [
                    'request_semester_setting_id' => $this->uuid->v4(),
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'personal_data_id_request' => $this->session->userdata('user'),
                    'request_json' => json_encode($a_semester_settings_data),
                    'request_note' => set_value('request_note'),
                    'request_datetime' => date('Y-m-d H:i:s'),
                    'date_added' => date('Y-m-d H:i:s')
                ];

                $saved = $this->Smm->save_request($a_data);
                if ($saved) {
                    $s_title = '[URGENT NOTIFICATION] New request approval semester period';
                    $s_links = base_url().'academic/semester/semester_lists/'.$s_academic_year_id.'/'.$s_semester_type_id;
                    $s_message = <<<TEXT
Dear rector and vice rector, please visit the link below to proceed the approval process. 
{$s_links}
or
login via portal.iuli.ac.id and find the Semester page, then select the requested semester
TEXT;

                    $bccEmail = array($this->config->item('email')['academic']['head'], 'employee@company.ac.id');
                    // $config = $this->config->item('mail_config');
                    // $config['mailtype'] = 'html';
                    // $this->email->initialize($config);

                    $this->email->from('employee@company.ac.id', 'IULI Academic Service Centre');
                    $a_send_to = [$this->s_rector_email, $this->s_vice_rector_email];
                    // $a_send_to = array_merge($a_mail_to, $this->a_deans_email);
                    $this->email->to($a_send_to);
                    // $this->email->to('employee@company.ac.id');
                    $this->email->bcc($bccEmail);
                    $this->email->subject($s_title);
                    $this->email->message($s_message);
                    if ($this->email->send()) {
                        $a_rtn = array('code' => 0, 'message' => 'Success');
                        $this->email->clear(TRUE);
                    }else{
                        $a_rtn = array('code' => 1, 'message' => 'Reminder fail to send!', 'data' => $$this->email->print_debugger());
                    }
                    
                }else{
                    $a_rtn = array('code' => 1, 'message' => 'Nothing requested!');
                }
                
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_rtn);
        }
    }
    
    public function semester_setting_detail_approved()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('semester_status', 'Semester Status', 'trim|required');

            $s_semester_start_date = $this->input->post('semester_start_date');
            $s_semester_end_date = $this->input->post('semester_end_date');
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_dikti_report_deadline = $this->input->post('dikti_report_deadline');

            $s_regular_offer_subject_end_date = $this->input->post('regular_offer_subject_end_date');
            $s_short_semester_offer_subject_end_date = $this->input->post('short_semester_offer_subject_end_date');
            $s_ofse_offer_subject_end_date = $this->input->post('ofse_offer_subject_end_date');

            $s_regular_study_plan_start_date = $this->input->post('regular_study_plan_start_date');
            $s_regular_study_plan_end_date = $this->input->post('regular_study_plan_end_date');
            $s_short_semester_study_plan_start_date = $this->input->post('short_semester_study_plan_start_date');
            $s_short_semester_study_plan_end_date = $this->input->post('short_semester_study_plan_end_date');
            $s_ofse_study_plan_start_date = $this->input->post('ofse_study_plan_start_date');
            $s_ofse_study_plan_end_date = $this->input->post('ofse_study_plan_end_date');

            $s_regular_study_plan_approval_end_date = $this->input->post('regular_study_plan_approval_end_date');
            $s_short_semester_study_plan_approval_end_date = $this->input->post('short_semester_study_plan_approval_end_date');
            $s_ofse_study_plan_approval_end_date = $this->input->post('ofse_study_plan_approval_end_date');

            $s_regular_repetition_registration_start_date = $this->input->post('regular_repetition_registration_start_date');
            $s_regular_repetition_registration_end_date = $this->input->post('regular_repetition_registration_end_date');
            $s_ofse_repetition_registration_start_date = $this->input->post('ofse_repetition_registration_start_date');
            $s_ofse_repetition_registration_end_date = $this->input->post('ofse_repetition_registration_end_date');

            if ($this->form_validation->run()) {
                $a_semester_settings_data = array(
                    'offer_subject_end_date' => ($s_regular_offer_subject_end_date != '') ? $s_regular_offer_subject_end_date.' 23:59:59' : null,
                    'offer_subject_short_semester_end_date' => ($s_short_semester_offer_subject_end_date != '') ? $s_short_semester_offer_subject_end_date.' 23:59:59' : null,
                    'offer_subject_ofse_end_date' => ($s_ofse_offer_subject_end_date != '') ? $s_ofse_offer_subject_end_date.' 23:59:59' : null,
                    'study_plan_start_date' => ($s_regular_study_plan_start_date != '') ? $s_regular_study_plan_start_date.' 00:00:00' : null,
                    'study_plan_end_date' => ($s_regular_study_plan_end_date != '') ? $s_regular_study_plan_end_date.' 23:59:59' : null,
                    'study_plan_short_semester_start_date' => ($s_short_semester_study_plan_start_date != '') ? $s_short_semester_study_plan_start_date.' 00:00:00' : null,
                    'study_plan_short_semester_end_date' => ($s_short_semester_study_plan_end_date != '') ? $s_short_semester_study_plan_end_date.' 23:59:59' : null,
                    'study_plan_ofse_start_date' => ($s_ofse_study_plan_start_date != '') ? $s_ofse_study_plan_start_date.' 00:00:00' : null,
                    'study_plan_ofse_end_date' => ($s_ofse_study_plan_end_date != '') ? $s_ofse_study_plan_end_date.' 23:59:59' : null,
                    'study_plan_approval_end_date' => ($s_regular_study_plan_approval_end_date != '') ? $s_regular_study_plan_approval_end_date.' 23:59:59' : null,
                    'study_plan_approval_short_semester_end_date' => ($s_short_semester_study_plan_approval_end_date != '') ? $s_short_semester_study_plan_approval_end_date.' 23:59:59' : null,
                    'study_plan_approval_ofse_end_date' => ($s_ofse_study_plan_approval_end_date != '') ? $s_ofse_study_plan_approval_end_date.' 23:59:59' : null,
                    'repetition_registration_start_date' => ($s_regular_repetition_registration_start_date != '') ? $s_regular_repetition_registration_start_date.' 00:00:00' : null,
                    'repetition_registration_end_date' => ($s_regular_repetition_registration_end_date != '') ? $s_regular_repetition_registration_end_date.' 23:59:59' : null,
                    'repetition_registration_ofse_start_date' => ($s_ofse_repetition_registration_start_date != '') ? $s_ofse_repetition_registration_start_date.' 00:00:00' : null,
                    'repetition_registration_ofse_end_date' => ($s_ofse_repetition_registration_end_date != '') ? $s_ofse_repetition_registration_end_date.' 23:59:59' : null,
                    'dikti_report_deadline' => ($s_dikti_report_deadline != '') ? $s_dikti_report_deadline.' 23:59:59' : null,
                    'semester_status' => set_value('semester_status')
                );

                if (set_value('semester_status') == 'active') {
                    $this->Smm->inactive_semester_academic();
                }
                $this->Smm->save_semester_setttings($a_semester_settings_data, array('academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id));
                
                $mbo_request_data = $this->Smm->get_semester_setting_request(['ssr.academic_year_id' => $s_academic_year_id, 'ssr.semester_type_id' => $s_semester_type_id])[0];
                if ($mbo_request_data) {
                    $a_data = [
                        'personal_data_id_approve' => $this->session->userdata('user'),
                        'approve_datetime' => date('Y-m-d H:i:s')
                    ];
    
                    $this->Smm->save_request($a_data, $mbo_request_data->request_semester_setting_id);

                    $s_title = '[REQUEST APPROVED] Request approval semester period';
                    $s_links = base_url().'academic/semester/semester_lists/'.$s_academic_year_id.'/'.$s_semester_type_id;
                    $s_message = <<<TEXT
Dear Academic Team, Rector has been approved your request.
please visit the link below to view approval data. 
{$s_links}
or
login via portal.iuli.ac.id and find the Semester page, then select the requested semester
TEXT;

                    $bccEmail = array('employee@company.ac.id');
                    // $config = $this->config->item('mail_config');
                    // $config['mailtype'] = 'html';
                    // $this->email->initialize($config);

                    $this->email->from($this->s_rector_email, 'IULI Academic Service Centre');
                    $this->email->to($this->config->item('email')['academic']['head']);
                    // $this->email->to('employee@company.ac.id');
                    $this->email->bcc($bccEmail);
                    $this->email->subject($s_title);
                    $this->email->message($s_message);
                    $this->email->send();
                }

                $a_rtn = array('code' => 0, 'message' => 'Success');
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_rtn);
        }
    }

    public function validate_detail_semester_settings($s_semester_start_date, $s_semester_end_date, $s_date)
    {
        $start_semester_date = date('Y-m-d H:i:s', strtotime($s_semester_start_date));
        $end_semester_date = date('Y-m-d H:i:s', strtotime($s_semester_end_date));
        $date_key = date('Y-m-d H:i:s', strtotime($s_date));

        if (($date_key >= $start_semester_date) AND ($date_key <= $end_semester_date)) {
            return true;
        }else{
            return false;
        }
    }

    public function validate_date_semester_settings($s_start_key, $s_end_key, $s_value)
    {
        $mba_semester_setting_data = $this->Smm->get_semester_setting(array($s_start_key.' !=' => null));
        if ($mba_semester_setting_data) {
            foreach ($mba_semester_setting_data as $semester_setting) {
                $start_date_db = date('Y-m-d H:i:s', strtotime($semester_setting->$s_start_key));
                $end_date_db = date('Y-m-d H:i:s', strtotime($semester_setting->$s_end_key));
                $date_key = date('Y-m-d H:i:s', strtotime($s_value));
                if (($date_key >= $start_date_db) AND ($date_key <= $end_date_db)) {
                    return false;
                    exit;
                }
            }
        }
        return true;
    }

    public function get_active_semester()
    {
        $o_semester_active = $this->Smm->get_active_semester();
        return $o_semester_active;
    }

    public function checker_semester_academic($s_field_start = null, $s_field_end, $s_academic_year_id, $s_semester_type_id)
    {
        $a_by_pass_user = [
        ]; // personal_data_id

        // $mba_semester_setting_data = $this->Smm->get_semester_setting([
        //     'dss.academic_year_id' => $s_academic_year_id,
        //     'dss.semester_type_id' => $s_semester_type_id
        // ]);
        // if ($mba_semester_setting_data) {
        //     $o_semester_setting_data = $mba_semester_setting_data[0];
        //     if (!is_null($s_field_start)) {
        //         $s_date_field_start = $o_semester_setting_data->$s_field_start;
        //         if (date('Y-m-d H:i:s') <= date('Y-m-d H:i:s', strtotime($s_date_field_start))) {
        //             return true;
        //         }
        //         else {
        //             return false;
        //         }
        //     }
        //     else {
        //         # code...
        //     }
        // }

        if (!is_null($s_field_start)) {
            $mbo_semester_setting_data = $this->Smm->get_semester_setting(
                array(
                    'dss.'.$s_field_start.' <= ' => date('Y-m-d H:i:s'),
                    'dss.'.$s_field_end.' >=' => date('Y-m-d H:i:s'),
                    'dss.academic_year_id' => $s_academic_year_id,
                    'dss.semester_type_id' => $s_semester_type_id
                )
            )[0];
        }else{
            $mbo_semester_setting_data = $this->Smm->get_semester_setting(
                array(
                    'dss.'.$s_field_end.' >=' => date('Y-m-d H:i:s'),
                    'dss.academic_year_id' => $s_academic_year_id,
                    'dss.semester_type_id' => $s_semester_type_id
                )
            )[0];
        }

        if ($mbo_semester_setting_data) {
            return true;
        }else{
            if (in_array($this->session->userdata('user'), $a_by_pass_user)) {
                return true;
            }else{
                return false;
            }
            // return false;
        }
    }
    
}
