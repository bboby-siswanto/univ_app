<?php
class Internship extends App_core {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('student/Internship_model', 'Inm');
    }

    public function document_list()
    {
        $this->a_page_data['study_program'] = $this->Spm->get_study_program(false, false);
        $this->a_page_data['body'] = $this->load->view('student/internship/document_submit', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function get_student_list_submit()
    {
        if ($this->input->is_ajax_request()) {
            $a_filter_data = false;
            $s_study_progra_id = $this->input->post('filter_study_program_id');
            if ((!empty($s_study_progra_id)) AND ($s_study_progra_id != 'all')) {
                $a_filter_data['st.study_program_id'] = $s_study_progra_id;
            }
            $mba_student_submit_data = $this->Inm->get_internship_student($a_filter_data);
            if ($mba_student_submit_data) {
                foreach ($mba_student_submit_data as $o_student_internship) {
                    $mba_document_data = $this->Inm->get_internship_document([
                        'si.internship_id' => $o_student_internship->internship_id
                    ]);
                    $o_student_internship->document_list = $mba_document_data;
                }
            }
            print json_encode(['data' => $mba_student_submit_data]);
        }
    }

    public function view_doc($s_internship_id, $doc_type = '')
    {
        $mba_internship_data = $this->Inm->get_internship_document(['si.internship_id' => $s_internship_id, 'document_type' => $doc_type]);
        // print('<pre>');var_dump($mba_internship_data);exit;
        if ($mba_internship_data) {
            $o_internship_data = $mba_internship_data[0];
            // print('<pre>');var_dump($o_internship_data);exit;
            $s_path = 'student/'.$o_internship_data->academic_year_id.'/'.$o_internship_data->study_program_abbreviation.'/'.$o_internship_data->student_id.'/internship/'.$o_internship_data->document_link;
            $s_path = (!is_null($o_internship_data->personal_data_path)) ? 'student/'.$o_internship_data->personal_data_path.'/internship/'.$o_internship_data->document_link : $s_path;
            $s_path = urlencode(base64_encode($s_path));

            $a_filelink = explode('.', $o_internship_data->document_link);
            $s_ext = $a_filelink[count($a_filelink) - 1];

            $s_path = str_replace("=", "", $s_path);
            $s_path = str_replace("==", "", $s_path);
            $s_path = str_replace("===", "", $s_path);

            // print("<pre>");var_dump($s_path);exit;
            redirect('file_manager/download_files/'.$s_path.'/'.urlencode($o_internship_data->document_name).'.'.$s_ext);

            // $s_file_path = APPPATH.'uploads/student/'.$o_internship_data->academic_year_id.'/'.$o_internship_data->study_program_abbreviation.'/'.$o_internship_data->student_id.'/internship/'.$o_internship_data->document_link;
            // if (file_exists($s_file_path)) {
            //     $s_mime = mime_content_type($s_file_path);
            //     header("Content-Type: ".$s_mime);
            //     header("filename: tset");
            //     readfile( $s_file_path );
            //     exit;
            // }
            // else {
            //     show_404();
            // }
        }
        else {
            show_404();
        }
    }

    public function page()
    {
        $s_student_id = $this->session->userdata('student_id');
        $mba_has_pick_subject = $this->Scm->get_score_like_subject_name([
            'sc.student_id' => $s_student_id,
            'sc.score_approval' => 'approved'
        ], 'internship');

        if ($mba_has_pick_subject) {
            $mbo_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
            $mba_internship_data = $this->Inm->get_internship_student(['st.student_id' => $s_student_id]);
            $assessment_doc = $this->Inm->get_internship_document(['si.student_id' => $s_student_id, 'sid.document_type' => 'assessment']);
            $logshet_doc = $this->Inm->get_internship_document(['si.student_id' => $s_student_id, 'sid.document_type' => 'logsheet']);
            $report_doc = $this->Inm->get_internship_document(['si.student_id' => $s_student_id, 'sid.document_type' => 'report']);
            $other1_doc = $this->Inm->get_internship_document(['si.student_id' => $s_student_id, 'sid.document_type' => 'other_doc_1']);
            $other2_doc = $this->Inm->get_internship_document(['si.student_id' => $s_student_id, 'sid.document_type' => 'other_doc_2']);

            $this->a_page_data['student_data'] = $mbo_student_data[0];
            $this->a_page_data['internship_data'] = ($mba_internship_data) ? $mba_internship_data[0] : false;
            $this->a_page_data['assessment_doc'] = ($assessment_doc) ? $assessment_doc[0] : false;
            $this->a_page_data['logshet_doc'] = ($logshet_doc) ? $logshet_doc[0] : false;
            $this->a_page_data['report_doc'] = ($report_doc) ? $report_doc[0] : false;
            $this->a_page_data['other1_doc'] = ($other1_doc) ? $other1_doc[0] : false;
            $this->a_page_data['other2_doc'] = ($other2_doc) ? $other2_doc[0] : false;
            $this->a_page_data['body'] = $this->load->view('student/internship/internship_page', $this->a_page_data, true);
        }
        else {
            $this->a_page_data['body'] = iuli_message('', 'This page is only available if you have already taken the KRS internship', true);
        }
        $this->load->view('layout', $this->a_page_data);
        // show_403();
    }

    public function submit_internship()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_internship_id = $this->input->post('internship_id');
            
            $this->form_validation->set_rules('internship_company', 'Company', 'trim|required');
            $this->form_validation->set_rules('internship_department', 'Department', 'trim|required');
            $this->form_validation->set_rules('internship_supervisor', 'Supervisor', 'trim|required');

            $a_return = false;
            if (empty($s_internship_id)) {
                if (empty($_FILES['file_assessment']['name'])) {
                    $a_return = ['code' => 1, 'message' => 'Assessment File is Required'];
                }
                else if (empty($_FILES['file_logsheet']['name'])) {
                    $a_return = ['code' => 1, 'message' => 'Logsheet File is Required'];
                }
                else if (empty($_FILES['file_report']['name'])) {
                    $a_return = ['code' => 1, 'message' => 'Report File is Required'];
                }
            }
            
            if (!$a_return) {
                if (empty($s_student_id)) {
                    $a_return = ['code' => 1, 'message' => 'Error retrieve param data!'];
                }
                else if ($this->form_validation->run()) {
                    $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
                    $a_return = $this->Inm->submit_internship_data($this->input->post(), $mba_student_data[0]);
                }
                else{
                    $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
                }
            }

            print json_encode($a_return);
        }
    }
}