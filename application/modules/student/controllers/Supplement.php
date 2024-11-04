<?php
class Supplement extends App_core
{
    function __construct()
    {
        parent::__construct('student_academic');
        $this->load->model('student/Supplement_model', 'Ssm');
        $this->load->model('employee/Employee_model', 'Emm');
    }

    public function form_input()
    {
        $this->a_page_data['student_id'] = $this->session->userdata('student_id');
        $this->load->view('student/supplement/form/form_supplement', $this->a_page_data);
    }

    public function view_doc($s_supplement_doc_id = false)
    {
        if ($s_supplement_doc_id) {
            $mba_doc_data = $this->Ssm->get_supplement_doc([
                'ssp.supplement_doc_id' => $s_supplement_doc_id
            ]);

            if ($mba_doc_data) {
                $o_doc_data = $mba_doc_data[0];
                $s_file_path = APPPATH.'uploads/student/'.$o_doc_data->student_batch.'/'.$o_doc_data->study_program_abbreviation.'/'.$o_doc_data->student_id.'/achievement/'.$o_doc_data->supplement_doc_link;
                if (file_exists($s_file_path)) {
                    $s_mime = mime_content_type($s_file_path);
                    header("Content-Type: ".$s_mime);
                    header('Content-Disposition: inline; filename='.$o_doc_data->supplement_doc_fname);
                    readfile( $s_file_path );
                    exit;
                }
                else {
                    show_404();
                }
            }
            else {
                show_404();
            }
        }
        else {
            show_404();
        }
    }

    public function page()
    {
        $this->a_page_data['student_id'] = $this->session->userdata('student_id');
        $this->a_page_data['body'] = $this->load->view('student/supplement/page', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function get_supplement_student()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_render_from = $this->input->post('render_from');

            $a_clause = false;
            if ((!empty($s_student_id)) OR (!empty($s_render_from))) {
                $a_clause = ['ssp.student_id' => $s_student_id];
                if ($s_render_from == 'student') {
                    $a_clause['ssp.employee_id'] = NULL;
                }
            }

            $mba_supplement_list = $this->Ssm->get_supplement($a_clause);
            if ($mba_supplement_list) {
                foreach ($mba_supplement_list as $o_supplement) {
                    $mba_supplement_files = $this->Ssm->get_supplement_doc(['sp.supplement_id' => $o_supplement->supplement_id]);
                    $s_added_by = $o_supplement->personal_data_name;
                    if (!is_null($o_supplement->employee_id)) {
                        $employee_data = $this->Emm->get_employee_data(['em.employee_id' => $o_supplement->employee_id]);
                        if ($employee_data) {
                            $s_added_by = $this->General->retrieve_title($employee_data[0]->personal_data_id);
                        }
                    }

                    $o_supplement->added_by = $s_added_by;
                    $o_supplement->added_date = date('d F Y H:i:s', strtotime($o_supplement->date_supplement));
                    $o_supplement->added_datestring = strtotime($o_supplement->date_supplement);
                    $o_supplement->supplement_files = $mba_supplement_files;
                    $o_supplement->date_upload = date('d F Y', strtotime($o_supplement->date_supplement));
                }
            }
            print json_encode(['data' => $mba_supplement_list]);
        }
    }

    public function submit_supplement()
    {
        if ($this->input->is_ajax_request()) {
            // print('<pre>');var_dump($_FILES);exit;
            if (empty($this->input->post('supplement_desc'))) {
                $a_return = ['code' => 1, 'message' => 'Description field is required'];
            }
            else {
                $s_error_file = 'Please upload at least 1 file';
                foreach ($_FILES as $s_key => $a_value) {
                    if (!empty($a_value['name'])) {
                        $s_error_file = '';break;
                    }
                }

                if (empty($s_error_file)) {
                    $a_return = $this->Ssm->submit_supplement($this->input->post());
                }
                else {
                    $a_return = ['code' => 2, 'message' => $s_error_file];
                }
            }

            print json_encode($a_return);exit;
        }
    }
}

?>