<?php
class Hris extends App_core
{
    public function __construct()
    {
        parent::__construct('hris');
        $this->load->model('employee/Employee_model', 'Emp');
        $this->load->model('hris/Hris_model', 'Hrm');
    }

    public function employee_list()
    {
        $this->a_page_data['body'] = $this->load->view('hris/employee_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function department()
    {
        $this->a_page_data['body'] = $this->load->view('hris/department', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function get_department() {
        if ($this->input->is_ajax_request()) {
            $mba_data = $this->Hrm->get_department();
            print json_encode(['data' => $mba_data]);
        }
    }

    public function form_input_employee()
    {
        $this->a_page_data['department_list'] = $this->Hrm->get_department();
        $this->load->view('hris/form/form_input', $this->a_page_data);
    }

    public function attendance_list()
    {
        $this->a_page_data['employee_list'] = $this->Hrm->get_employee_hid();
        $this->a_page_data['body'] = $this->load->view('hris/attendance_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function set_hod_department() {
        if ($this->input->is_ajax_request()) {
            $s_department_id = $this->input->post('department_id');
            $s_employee_id = $this->input->post('employee_id');

            if ((empty($s_department_id)) OR (empty($s_employee_id))) {
                $a_return = ['code' => 1, 'message' => 'No Employee or Department selected!'];
            }
            else {
                $this->General->update_data('ref_department', [
                    'employee_id' => $s_employee_id
                ], [
                    'department_id' => $s_department_id
                ]);
                $a_return = ['code' => 0, 'message' => 'Success'];
            }

            print json_encode($a_return);exit;
        }
    }

    function remove_hod_department() {
        if ($this->input->is_ajax_request()) {
            $s_department_id = $this->input->post('department_id');

            if (empty($s_department_id)) {
                $a_return = ['code' => 1, 'message' => 'No Department selected!'];
            }
            else {
                $this->General->update_data('ref_department', [
                    'employee_id' => NULL
                ], [
                    'department_id' => $s_department_id
                ]);
                $a_return = ['code' => 0, 'message' => 'Success'];
            }

            print json_encode($a_return);exit;
        }
    }

    public function save_employee()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('personal_data_title_prefix', 'Title Name Prefix', 'trim');
            $this->form_validation->set_rules('personal_data_name', 'Full Name', 'trim|required');
            $this->form_validation->set_rules('personal_data_title_suffix', 'Title Name Suffix', 'trim');
            $this->form_validation->set_rules('personal_data_gender', 'Gender', 'trim|required');
            $this->form_validation->set_rules('employment_group', 'General Occupation', 'trim|required');
            $this->form_validation->set_rules('employment_status', 'Status of the employee', 'trim|required');
            $this->form_validation->set_rules('employee_id_number', 'NIP', 'trim|required');
            $this->form_validation->set_rules('employee_join_date', 'Join Date', 'trim|required');
            $this->form_validation->set_rules('employee_email', 'IULI Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('employee_job_title', 'Job Title', 'trim|required');
            $this->form_validation->set_rules('employee_department', 'Department', 'trim|required');
            $this->form_validation->set_rules('employee_is_lecturer', 'Is Lecturer', 'trim|required');

            if ($this->input->post('employee_is_lecturer') == 'YES') {
                $this->form_validation->set_rules('employee_lecturer_number_type', 'Lecturer Number Type', 'trim|required');
                $this->form_validation->set_rules('employee_lecturer_number', 'Lecturer Number', 'trim|required');
            }

            if ($this->form_validation->run()) {
                $a_return = $this->Emp->submit_employee_data($this->input->post());
            }
            else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }

    public function get_nip_employee()
    {
        if ($this->input->is_ajax_request()) {
            $s_status = $this->input->post('status_employee');
            $s_group = $this->input->post('group_employee');
            $s_joindate = $this->input->post('joindate_employee');

            $mba_new_nip = $this->Hrm->get_last_nip($s_status, $s_group, date('Y-m-d', strtotime($s_joindate)));
            print json_encode($mba_new_nip);
        }
    }
}
