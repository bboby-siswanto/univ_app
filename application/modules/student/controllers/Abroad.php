<?php
class Abroad extends App_core {
    public $a_default_configfile_abroad;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('student/Internship_model', 'Inm');
        $this->load->model('admission/International_office_model', 'Iom');
        $this->load->model('thesis/Thesis_model', 'Tsm');
        $this->a_default_configfile_abroad = [
            'allowed_types' => 'pdf|jpg|docx|xlsx|bmp|jpeg|png',
            'max_size' => 204800,
            'file_ext_tolower' => true,
            'overwrite' => true,
        ];
    }

    function registration() {
        $this->a_page_data['body'] = '';
        $this->load->view('layout', $this->a_page_data);
    }

    public function receive_doc()
    {
        if ($this->input->is_ajax_request()) {
            // print($this->input->post('typedoc'));
            // print('<pre>');var_dump($_FILES);exit;
            $mba_student_abroad_data = $this->Iom->get_international_data([
                'ex.student_id' => $this->session->userdata('student_id')
            ]);
            $o_abroad_data = $mba_student_abroad_data[0];
            
            $s_typedoc = $this->input->post('typedoc');
            $a_typedoc = explode('-', $s_typedoc);
            $s_target = $a_typedoc[0];
            $s_doctype = $a_typedoc[1];
            switch ($s_target) {
                case 'abroad':
                    $s_file_path = APPPATH.'uploads/student/'.$o_abroad_data->student_batch.'/'.$o_abroad_data->study_program_abbreviation.'/'.$o_abroad_data->student_id.'/abroad_doc/';
                    if(!file_exists($s_file_path)){
                        mkdir($s_file_path, 0755, true);
                    }

                    $config = $this->a_default_configfile_abroad;
                    $config['upload_path'] = $s_file_path;
                    $this->load->library('upload', $config);

                    $s_uniqid = date('YmdHi');
                    $s_genfname = $o_abroad_data->program_name.'-'.$o_abroad_data->personal_data_name;
                    $s_fname = '';
                    if ($s_doctype == 'transcript') {
                        $s_fname = 'Transcript '.$s_genfname;
                    }
                    else if ($s_doctype == 'certificate_degree') {
                        $s_fname = 'Certificate '.$s_genfname;
                    }
                    else if ($s_doctype == 'other_file') {
                        $s_fname = 'OtherFile '.$s_genfname;
                    }
                    $s_flink = str_replace(' ', '', $s_fname).'-'.$s_uniqid;
                    
                    if (!empty($_FILES['file'])) {
                        $this->Iom->remove_document_abroad(['exchange_id' => $o_abroad_data->exchange_id, 'document_type' => $s_doctype]);
                        $config['file_name'] = $s_flink;
                        $this->upload->initialize($config);
                        if($this->upload->do_upload('file')) {
                            $doc_data = [
                                'exchange_file_id' => $this->uuid->v4(),
                                'exchange_id' => $o_abroad_data->exchange_id,
                                'document_type' => $s_doctype,
                                'document_link' => $this->upload->data('file_name'),
                                'document_name' => $s_fname,
                                'date_added' => date('Y-m-d H:i:s')
                            ];
                            $this->Iom->submit_document_abroad($doc_data);
                            $a_return = ['code' => 0, 'message' => 'Succes', 'name' => $s_fname, 'target' => base_url().'student/abroad/view_doc/'.$this->upload->data('file_name')];
                        }
                        else {
                            $a_return = ['code' => 2, 'message' => $this->upload->display_errors('<span>', '</span>')];
                        }
                    }
                    else {
                        $a_return = ['code' => 1, 'message' => 'no file founded'];
                    }
                    break;

                case 'thesis':
                    $a_return = $this->_submit_thesis_file($this->input->post(), $s_doctype);
                    break;

                case 'internship':
                    $a_return = $this->_submit_internship_file($this->input->post(), $s_doctype);
                    break;
                
                default:
                    $a_return = ['code' => 1, 'message' => 'funciton disable'];
                    break;
            }

            print json_encode($a_return);exit;
        }
    }

    private function _submit_internship_file($a_post_data, $s_doctype)
    {
        $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->session->userdata('student_id')]);
        $s_file_path = APPPATH.'uploads/student/'.$mba_student_data[0]->academic_year_id.'/'.$mba_student_data[0]->study_program_abbreviation.'/'.$mba_student_data[0]->student_id.'/internship/';
        $config = $this->a_default_configfile_abroad;
        $config['upload_path'] = $s_file_path;
        $this->load->library('upload', $config);

        // $s_academic_year_id_active = $this->session->userdata('academic_year_id_active');
        // $s_semester_type_id_active = $this->session->userdata('semester_type_id_active');

        $s_fname = str_replace(' ', '-', str_replace("'" ,"", strtolower($mba_student_data[0]->personal_data_name))).'-'.date('Ymd');
        // $s_fname = $s_fname.'_'.$s_academic_year_id_active.'-'.$s_semester_type_id_active.'_';
        $s_assessment_name = $s_fname.'_internship_assessment';
        $s_logsheet_name = $s_fname.'_internship_logsheet';
        $s_report_name = $s_fname.'_internship_report';
        $s_otherdoc1_name = $s_fname.'_internship_otherdoc1';
        $s_otherdoc2_name = $s_fname.'_internship_otherdoc2';

        // $mba_internship_student = $this->Inm->get_internship_student([
        //     'st.student_id' => $this->session->userdata('student_id'),
        //     'ri.institution_name' => $this->input->post('internship_company')
        // ]);

        // if (!$mba_internship_student) {
        //     $mba_internship_student = $this->Inm->get_internship_student([
        //         'st.student_id' => $this->session->userdata('student_id')
        //     ]);
        // }

        // if (!$mba_internship_student) {
        //     $s_internship_id = $this->uuid->v4();

        //     if (!empty($this->input->post('internship_company'))) {
        //         $s_institution_name = $this->input->post('internship_company');
        //     }
        //     $a_internship_data = [];
        // }
        // else {
        //     $s_internship_id = $mba_internship_student[0]->internship_id;
        // }

        // 

        $a_return = ['code' => 1, 'message' => 'function under maintenance', 'internship place' => $this->input->post()];
        return $a_return;
    }

    private function _submit_thesis_file($a_post_data, $s_doctype)
    {
        $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->session->userdata('student_id')]);
        $s_file_path = APPPATH.'uploads/student/'.$mba_student_data[0]->academic_year_id.'/'.$mba_student_data[0]->study_program_abbreviation.'/'.$mba_student_data[0]->student_id.'/';
        $s_path_final = $s_file_path.'thesis_final/';
        $s_path_work = $s_file_path.'thesis_work/';
        
        $mba_thesis_data = $this->Tsm->get_thesis_student(['st.student_id' => $this->session->userdata('student_id')], 'ts.date_added', 'DESC');
        $a_thesis_data = [
            'thesis_title' => $a_post_data['af_thesis_title'],
            'current_progress' => 'finish',
            'current_status' => 'approved',
        ];

        if ($mba_thesis_data) {
            $s_thesis_student_id = $mba_thesis_data[0]->thesis_student_id;
            $this->Tsm->submit_thesis_student($a_thesis_data, ['thesis_student_id' => $s_thesis_student_id]);
        }
        else {
            $s_thesis_student_id = $this->uuid->v4();
            $a_thesis_data['thesis_student_id'] = $s_thesis_student_id;
            $a_thesis_data['student_id'] = $this->session->userdata('student_id');
            $a_thesis_data['date_added'] = date('Y-m-d H:i:s');
            $this->Tsm->submit_thesis_student($a_thesis_data);
        }
        
        $mba_thesis_log_work = $this->Tsm->get_thesis_log($s_thesis_student_id, [
            'thesis_log_type' => 'work'
        ]);
        $mba_thesis_log_final = $this->Tsm->get_thesis_log($s_thesis_student_id, [
            'thesis_log_type' => 'final'
        ]);
        $a_thesis_log_data = [
            'thesis_student_id' => $s_thesis_student_id,
            'academic_year_id' => $this->session->userdata('academic_year_id_active'),
            'semester_type_id' => $this->session->userdata('semester_type_id_active'),
            'thesis_status' => 'approved'
        ];
        $a_thesis_work_log_data = $a_thesis_log_data;
        $a_thesis_final_log_data = $a_thesis_log_data;

        $a_thesis_work_log_data['thesis_log_type'] = 'work';
        $a_thesis_final_log_data['thesis_log_type'] = 'final';

        if (!$mba_thesis_log_work) {
            $s_thesis_work_log_id = $this->uuid->v4();
            $a_thesis_work_log_data['thesis_log_id'] = $s_thesis_work_log_id;
            $a_thesis_work_log_data['date_added'] = date('Y-m-d H:i:s');

            $this->Tsm->submit_log_status($a_thesis_work_log_data);
        }
        else {
            $s_thesis_work_log_id = $mba_thesis_log_work[0]->thesis_log_id;
            $this->Tsm->submit_log_status($a_thesis_work_log_data, ['thesis_log_id' => $s_thesis_work_log_id]);
        }

        if (!$mba_thesis_log_final) {
            $s_thesis_final_log_id = $this->uuid->v4();
            $a_thesis_final_log_data['thesis_log_id'] = $s_thesis_final_log_id;
            $a_thesis_final_log_data['date_added'] = date('Y-m-d H:i:s');

            $this->Tsm->submit_log_status($a_thesis_final_log_data);
        }
        else {
            $s_thesis_final_log_id = $mba_thesis_log_final[0]->thesis_log_id;
            $this->Tsm->submit_log_status($a_thesis_final_log_data, ['thesis_log_id' => $s_thesis_final_log_id]);
        }

        $config = $this->a_default_configfile_abroad;
        
        $s_deffname = date('M').'_'.date('Y').$mba_student_data[0]->student_number.'_';
        $s_fname = date('M').'_'.'2022'.$mba_student_data[0]->student_number.'_';
        $s_thesis_log_id = $s_thesis_work_log_id;
        $s_target = 'thesis_work';
        if ($s_doctype == 'work_log') {
            $s_fname = $s_deffname.'thesis_log';
            $s_thesis_log_id = $s_thesis_work_log_id;
            $config['upload_path'] = $s_path_work;
            $s_target = 'thesis_work';
        }
        else if ($s_doctype == 'work_plagiate_check') {
            $s_fname = $s_deffname.'thesis_pc';
            $s_thesis_log_id = $s_thesis_work_log_id;
            $config['upload_path'] = $s_path_work;
            $s_target = 'thesis_work';
        }
        else if ($s_doctype == 'final_file') {
            $s_fname = $s_deffname.'thesis_final';
            $s_thesis_log_id = $s_thesis_final_log_id;
            $config['upload_path'] = $s_path_final;
            $s_target = 'thesis_final';
        }
        else if ($s_doctype == 'work_other_doc') {
            $s_fname = $s_deffname.'other_doc';
            $s_thesis_log_id = $s_thesis_work_log_id;
            $config['upload_path'] = $s_path_work;
            $s_target = 'thesis_work';
        }
        else if ($s_doctype == 'final_other_doc') {
            $s_fname = $s_deffname.'thesis_final_other';
            $s_thesis_log_id = $s_thesis_final_log_id;
            $config['upload_path'] = $s_path_final;
            $s_target = 'thesis_final';
        }

        $s_filetype = 'thesis_'.$s_doctype;
        $this->load->library('upload', $config);

        if (!empty($_FILES['file'])) {
            $this->Tsm->force_remove_data('thesis_students_file', [
                'thesis_log_id' => $s_thesis_log_id,
                'thesis_filetype' => $s_filetype
            ]);

            $config['file_name'] = $s_fname;
            $this->upload->initialize($config);
            if($this->upload->do_upload('file')) {
                $a_filedata = [
                    'thesis_file_id' => $this->uuid->v4(),
                    'thesis_log_id' => $s_thesis_log_id,
                    'thesis_filetype' => $s_filetype,
                    'thesis_filename' => $this->upload->data('file_name'),
                    'date_added' => date('Y-m-d H:i:s')
                ];
                $this->Tsm->submit_thesis_file($a_filedata);
                $a_return = ['code' => 0, 'message' => 'Succes', 'name' => $this->upload->data('file_name'), 'target' => base_url().'thesis/view_file/'.$s_target.'/'.$s_thesis_student_id.'/'.$this->upload->data('file_name')];
            }
            else {
                $a_return = ['code' => 2, 'message' => $this->upload->display_errors('<span>', '</span>')];
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'no file founded'];
        }

        return $a_return;
    }
    
    public function submit_data()
    {
        if ($this->input->is_ajax_request()) {
            $_post = $this->input->post();
            $mba_student_abroad_data = $this->Iom->get_international_data([
                'ex.student_id' => $this->session->userdata('student_id')
            ]);
            // print('<pre>');var_dump($_post);exit;

            if ($mba_student_abroad_data) {
                $o_abroad_data = $mba_student_abroad_data[0];
                if ($o_abroad_data->program_id == 7) {
                    $save_thesis = false;
                    $save_internship = true;
                }
                else if ($o_abroad_data->program_id == 4) {
                    $save_thesis = false;
                    $save_internship = false;
                }
                else {
                    $save_thesis = true;
                    $save_internship = true;
                }
                
                $a_return = $this->_save_abroad_document($o_abroad_data->exchange_id, $o_abroad_data, [
                    'ad_transcript_partner_univ' => $_FILES['ad_transcript_partner_univ'],
                    'ad_crtificate_degree_partner_univ' => $_FILES['ad_crtificate_degree_partner_univ'],
                    'ad_abroad_other' => $_FILES['ad_abroad_other'],
                ]);

                if (($save_thesis) AND ($a_return['code'] == 0)) {
                    $a_return = $this->_save_thesis([
                        'thesis_title' => $_post['af_thesis_title'],
                        'iuli_advisor_1_id' => $_post['af_iuli_advisor_1'],
                        'iuli_advisor_2_id' => $_post['af_iuli_advisor_2'],
                        'partner_advisor_1_id' => $_post['af_partner_advisor_1'],
                        'partner_advisor_2_id' => $_post['af_partner_advisor_2'],
                    ], [
                        'td_thesis_log' => $_FILES['td_thesis_log'],
                        'td_thesis_plagiate' => $_FILES['td_thesis_plagiate'],
                        'td_thesis_work_other' => $_FILES['td_thesis_work_other'],
                        'td_thesis_final' => $_FILES['td_thesis_final'],
                        'td_thesis_final_other' => $_FILES['td_thesis_final_other'],
                    ]);
                }

                if (($save_internship) AND ($a_return['code'] == 0)) {
                    $mba_student_internship = $this->Inm->get_internship_student(['si.student_id' => $this->session->userdata('student_id')]);
                    $a_internship_post_param = [
                        'internship_company' => $_post['internship_company'],
                        'student_id' => $this->session->userdata('student_id'),
                        'internship_supervisor' => $_post['internship_supervisor'],
                        'internship_department' => $_post['internship_department']
                    ];

                    if ($mba_student_internship) {
                        $a_internship_post_param['internship_id'] = $mba_student_internship[0]->internship_id;
                    }
                    $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $this->session->userdata('student_id')]);
                    $a_return = $this->Inm->submit_internship_data($a_internship_post_param, $mba_student_data[0]);
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Abroad data not found!'];
            }
            
            print json_encode($a_return);exit;
        }
    }

    private function _save_abroad_document($s_exchange_id, $o_abroad_data, $a_datafile)
    {
        // print('<pre>');var_dump($a_datafile);exit;
        $a_error_upload = [];
        $s_file_path = APPPATH.'uploads/student/'.$o_abroad_data->student_batch.'/'.$o_abroad_data->study_program_abbreviation.'/'.$o_abroad_data->student_id.'/abroad_doc/';
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0755, true);
        }

        $config['allowed_types'] = 'pdf|docx|xlsx';
        $config['max_size'] = 204800;
        $config['file_ext_tolower'] = true;
        $config['overwrite'] = true;
        $config['upload_path'] = $s_file_path;
        $this->load->library('upload', $config);

        // tambahin uniq id di nama filenya
        $s_uniqid = date('YmdHi');
        $s_transcript_fname = 'Transcript '.$o_abroad_data->program_name.'-'.$o_abroad_data->personal_data_name;
        $s_certificate_fname = 'Certificate '.$o_abroad_data->program_name.'-'.$o_abroad_data->personal_data_name;
        $s_other_fname = 'OtherFile '.$o_abroad_data->program_name.'-'.$o_abroad_data->personal_data_name;

        $s_transcript_link = str_replace(' ', '', $s_transcript_fname).'-'.$s_uniqid;
        $s_certificate_link = str_replace(' ', '', $s_certificate_fname).'-'.$s_uniqid;
        $s_other_link = str_replace(' ', '', $s_other_fname).'-'.$s_uniqid;

        if (!empty($a_datafile['ad_transcript_partner_univ']['name'])) {
            $this->Iom->remove_document_abroad(['exchange_id' => $s_exchange_id, 'document_type' => 'transcript']);
            $config['file_name'] = $s_transcript_link;
            $this->upload->initialize($config);
            if($this->upload->do_upload('ad_transcript_partner_univ')) {
                $doc_data = [
                    'exchange_file_id' => $this->uuid->v4(),
                    'exchange_id' => $s_exchange_id,
                    'document_type' => 'transcript',
                    'document_link' => $this->upload->data('file_name'),
                    'document_name' => $s_transcript_fname,
                    'date_added' => date('Y-m-d H:i:s')
                ];
                $this->Iom->submit_document_abroad($doc_data);
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Transcript Files from partner University'));
            }
        }

        if (!empty($a_datafile['ad_crtificate_degree_partner_univ']['name'])) {
            $this->Iom->remove_document_abroad(['exchange_id' => $s_exchange_id, 'document_type' => 'transcript']);
            $config['file_name'] = $s_transcript_link;
            $this->upload->initialize($config);
            if($this->upload->do_upload('ad_crtificate_degree_partner_univ')) {
                $doc_data = [
                    'exchange_file_id' => $this->uuid->v4(),
                    'exchange_id' => $s_exchange_id,
                    'document_type' => 'certificate_degree',
                    'document_link' => $this->upload->data('file_name'),
                    'document_name' => $s_transcript_fname,
                    'date_added' => date('Y-m-d H:i:s')
                ];
                $this->Iom->submit_document_abroad($doc_data);
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Transcript Files from partner University'));
            }
        }

        if (!empty($a_datafile['ad_abroad_other']['name'])) {
            $this->Iom->remove_document_abroad(['exchange_id' => $s_exchange_id, 'document_type' => 'transcript']);
            $config['file_name'] = $s_transcript_link;
            $this->upload->initialize($config);
            if($this->upload->do_upload('ad_abroad_other')) {
                $doc_data = [
                    'exchange_file_id' => $this->uuid->v4(),
                    'exchange_id' => $s_exchange_id,
                    'document_type' => 'other_file',
                    'document_link' => $this->upload->data('file_name'),
                    'document_name' => $s_transcript_fname,
                    'date_added' => date('Y-m-d H:i:s')
                ];
                $this->Iom->submit_document_abroad($doc_data);
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>Transcript Files from partner University'));
            }
        }
        
        return (count($a_error_upload) > 0) ? ['code' => 1, 'message' => json_encode($a_error_upload)] : ['code' => 0, 'message' => 'Success'];
    }

    // untuk thesis
    private function _save_thesis($a_data, $a_datafile)
    {
        $mba_student_data = $this->General->get_where('dt_student st', ['st.student_id' => $this->session->userdata('student_id')]);
        $mba_thesis_data = $this->Tsm->get_thesis_student(['st.student_id' => $this->session->userdata('student_id')], 'ts.date_added', 'DESC');
        $a_thesis_data = [
            'thesis_title' => $a_data['thesis_title'],
            'current_progress' => 'finish',
            'current_status' => 'approved',
        ];

        if ($mba_thesis_data) {
            $s_thesis_student_id = $mba_thesis_data[0]->thesis_student_id;
            $this->Tsm->submit_thesis_student($a_thesis_data, ['thesis_student_id' => $s_thesis_student_id]);
        }
        else {
            $s_thesis_student_id = $this->uuid->v4();
            $a_thesis_data['thesis_student_id'] = $s_thesis_student_id;
            $a_thesis_data['student_id'] = $this->session->userdata('student_id');
            $a_thesis_data['date_added'] = date('Y-m-d H:i:s');
            $this->Tsm->submit_thesis_student($a_thesis_data);
        }

        if ((!empty($a_data['iuli_advisor_1_id'])) OR (!empty($a_data['iuli_advisor_2_id'])) OR (!empty($a_data['partner_advisor_1_id'])) OR (!empty($a_data['partner_advisor_2_id']))) {
            $this->Tsm->remove_advisor_data($s_thesis_student_id);
        }
        
        $i_current_advisor = 1;
        if (!empty($a_data['iuli_advisor_1_id'])) {
            $a_thesis_advisor = [
                'student_advisor_id' => $this->uuid->v4(),
                'thesis_student_id' => $s_thesis_student_id,
                'advisor_id' =>  $a_data['iuli_advisor_1_id'],
                'advisor_type' => 'approved_advisor_1',
                'advisor_section' => 'iuli_advisor',
                'date_added' => date('Y-m-d H:i:s')
            ];
            $this->Tsm->submit_student_advisor($a_thesis_advisor);
            $i_current_advisor++;
        }
        if (!empty($a_data['iuli_advisor_2_id'])) {
            $a_thesis_advisor = [
                'student_advisor_id' => $this->uuid->v4(),
                'thesis_student_id' => $s_thesis_student_id,
                'advisor_id' =>  $a_data['iuli_advisor_2_id'],
                'advisor_type' => 'approved_advisor_'.$i_current_advisor,
                'advisor_section' => 'iuli_co_advisor',
                'date_added' => date('Y-m-d H:i:s')
            ];
            $this->Tsm->submit_student_advisor($a_thesis_advisor);
            $i_current_advisor++;
        }
        if (!empty($a_data['partner_advisor_1_id'])) {
            $a_thesis_advisor = [
                'student_advisor_id' => $this->uuid->v4(),
                'thesis_student_id' => $s_thesis_student_id,
                'advisor_id' =>  $a_data['partner_advisor_1_id'],
                'advisor_type' => 'approved_advisor_'.$i_current_advisor,
                'advisor_section' => 'partner_advisor',
                'date_added' => date('Y-m-d H:i:s')
            ];
            $this->Tsm->submit_student_advisor($a_thesis_advisor);
            $i_current_advisor++;
        }
        if (!empty($a_data['partner_advisor_2_id'])) {
            $a_thesis_advisor = [
                'student_advisor_id' => $this->uuid->v4(),
                'thesis_student_id' => $s_thesis_student_id,
                'advisor_id' =>  $a_data['partner_advisor_2_id'],
                'advisor_type' => 'approved_advisor_'.$i_current_advisor,
                'advisor_section' => 'partner_co_advisor',
                'date_added' => date('Y-m-d H:i:s')
            ];
            $this->Tsm->submit_student_advisor($a_thesis_advisor);
            $i_current_advisor++;
        }

        $mba_thesis_log_work = $this->Tsm->get_thesis_log($s_thesis_student_id, [
            'thesis_log_type' => 'work'
        ]);
        $mba_thesis_log_final = $this->Tsm->get_thesis_log($s_thesis_student_id, [
            'thesis_log_type' => 'final'
        ]);
        $a_thesis_log_data = [
            'thesis_student_id' => $s_thesis_student_id,
            'academic_year_id' => $this->session->userdata('academic_year_id_active'),
            'semester_type_id' => $this->session->userdata('semester_type_id_active'),
            'thesis_status' => 'approved'
        ];
        $a_thesis_work_log_data = $a_thesis_log_data;
        $a_thesis_final_log_data = $a_thesis_log_data;

        $a_thesis_work_log_data['thesis_log_type'] = 'work';
        $a_thesis_final_log_data['thesis_log_type'] = 'final';

        if (!$mba_thesis_log_work) {
            $s_thesis_work_log_id = $this->uuid->v4();
            $a_thesis_work_log_data['thesis_log_id'] = $s_thesis_work_log_id;
            $a_thesis_work_log_data['date_added'] = date('Y-m-d H:i:s');

            $this->Tsm->submit_log_status($a_thesis_work_log_data);
        }
        else {
            $s_thesis_work_log_id = $mba_thesis_log_work[0]->thesis_log_id;
            $this->Tsm->submit_log_status($a_thesis_work_log_data, ['thesis_log_id' => $s_thesis_work_log_id]);
        }

        if (!$mba_thesis_log_final) {
            $s_thesis_final_log_id = $this->uuid->v4();
            $a_thesis_final_log_data['thesis_log_id'] = $s_thesis_final_log_id;
            $a_thesis_final_log_data['date_added'] = date('Y-m-d H:i:s');

            $this->Tsm->submit_log_status($a_thesis_final_log_data);
        }
        else {
            $s_thesis_final_log_id = $mba_thesis_log_final[0]->thesis_log_id;
            $this->Tsm->submit_log_status($a_thesis_final_log_data, ['thesis_log_id' => $s_thesis_final_log_id]);
        }

        // filenya dicek
        $a_error_upload = [];

        // $s_fname = date('M').'_'.date('Y').$mba_student_data[0]->student_number.'_';
        $s_fname = date('M').'_'.'2022'.$mba_student_data[0]->student_number.'_';
        $s_tl_fname = $s_fname.'thesis_log';
        $s_tpl_fname = $s_fname.'thesis_pc';
        $s_tod_fname = $s_fname.'other_doc';
        $s_tf_fname = $s_fname.'thesis_final';
        $s_tfo_fname = $s_fname.'thesis_final_other';

        if (!empty($a_datafile['td_thesis_log']['name'])) {
            $this->Tsm->force_remove_data('thesis_students_file', [
                'thesis_log_id' => $s_thesis_work_log_id,
                'thesis_filetype' => 'thesis_work_log'
            ]);

            $config['file_name'] = $s_tl_fname;
            $this->upload->initialize($config);
            if($this->upload->do_upload('td_thesis_log')) {
                $a_filedata = [
                    'thesis_file_id' => $this->uuid->v4(),
                    'thesis_log_id' => $s_thesis_work_log_id,
                    'thesis_filetype' => 'thesis_work_log',
                    'thesis_filename' => $this->upload->data('file_name'),
                    'date_added' => date('Y-m-d H:i:s')
                ];
                $this->Tsm->submit_thesis_file($a_filedata);
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span> Thesis Log '));
            }
        }
        if (!empty($a_datafile['td_thesis_plagiate']['name'])) {
            $this->Tsm->force_remove_data('thesis_students_file', [
                'thesis_log_id' => $s_thesis_work_log_id,
                'thesis_filetype' => 'thesis_work_plagiate_check'
            ]);

            $config['file_name'] = $s_tpl_fname;
            $this->upload->initialize($config);
            if($this->upload->do_upload('td_thesis_plagiate')) {
                $a_filedata = [
                    'thesis_file_id' => $this->uuid->v4(),
                    'thesis_log_id' => $s_thesis_work_log_id,
                    'thesis_filetype' => 'thesis_work_plagiate_check',
                    'thesis_filename' => $this->upload->data('file_name'),
                    'date_added' => date('Y-m-d H:i:s')
                ];
                $this->Tsm->submit_thesis_file($a_filedata);
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span> Thesis Plagiate Check '));
            }
        }
        if (!empty($a_datafile['td_thesis_work_other']['name'])) {
            $this->Tsm->force_remove_data('thesis_students_file', [
                'thesis_log_id' => $s_thesis_work_log_id,
                'thesis_filetype' => 'thesis_work_other_doc'
            ]);

            $config['file_name'] = $s_tod_fname;
            $this->upload->initialize($config);
            if($this->upload->do_upload('td_thesis_work_other')) {
                $a_filedata = [
                    'thesis_file_id' => $this->uuid->v4(),
                    'thesis_log_id' => $s_thesis_work_log_id,
                    'thesis_filetype' => 'thesis_work_other_doc',
                    'thesis_filename' => $this->upload->data('file_name'),
                    'date_added' => date('Y-m-d H:i:s')
                ];
                $this->Tsm->submit_thesis_file($a_filedata);
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span> Thesis Other File 2 '));
            }
        }
        if (!empty($a_datafile['td_thesis_final']['name'])) {
            $this->Tsm->force_remove_data('thesis_students_file', [
                'thesis_log_id' => $s_thesis_final_log_id,
                'thesis_filetype' => 'thesis_final_file'
            ]);

            $config['file_name'] = $s_tf_fname;
            $this->upload->initialize($config);
            if($this->upload->do_upload('td_thesis_final')) {
                $a_filedata = [
                    'thesis_file_id' => $this->uuid->v4(),
                    'thesis_log_id' => $s_thesis_final_log_id,
                    'thesis_filetype' => 'thesis_final_file',
                    'thesis_filename' => $this->upload->data('file_name'),
                    'date_added' => date('Y-m-d H:i:s')
                ];
                $this->Tsm->submit_thesis_file($a_filedata);
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>  Thesis Final '));
            }
        }
        if (!empty($a_datafile['td_thesis_final_other']['name'])) {
            $this->Tsm->force_remove_data('thesis_students_file', [
                'thesis_log_id' => $s_thesis_final_log_id,
                'thesis_filetype' => 'thesis_final_file'
            ]);

            $config['file_name'] = $s_tfo_fname;
            $this->upload->initialize($config);
            if($this->upload->do_upload('td_thesis_final_other')) {
                $a_filedata = [
                    'thesis_file_id' => $this->uuid->v4(),
                    'thesis_log_id' => $s_thesis_final_log_id,
                    'thesis_filetype' => 'thesis_final_file',
                    'thesis_filename' => $this->upload->data('file_name'),
                    'date_added' => date('Y-m-d H:i:s')
                ];
                $this->Tsm->submit_thesis_file($a_filedata);
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>  Thesis Final '));
            }
        }
        
        if (count($a_error_upload) > 0) {
            $a_return = ['code' => 1, 'message' => implode(';', $a_error_upload)];
        }
        else {
            $a_return = ['code' => 0, 'message' => 'Success'];
        }

        return $a_return;
    }

    function test_getpage() {
        $mba_is_abroad = $this->General->get_where('dt_student_exchange', [
            'student_id' => $this->session->userdata('student_id'),
            'exchange_type' => 'out'
        ]);
        print('<pre>');var_dump($mba_is_abroad);exit;
    }

    public function submission()
    {
        $mba_student_abroad_data = $this->Iom->get_international_data([
            'ex.student_id' => $this->session->userdata('student_id')
        ]);
        $s_internship_link = base_url().'#';
        $s_thesis_prop_link = base_url().'#';
        $s_thesis_work_link = base_url().'#';
        $s_thesis_final_link = base_url().'#';
        
        $a_listpage = (isset($this->a_page_data['top_bar']['student_internship'])) ? $this->a_page_data['top_bar']['student_internship'] : 'ga ada';
        if (isset($a_listpage['side_bar'])) {
            $a_listpage = $a_listpage['side_bar'];
            if ((is_array($a_listpage)) AND (count($a_listpage) > 0)) {
                foreach ($a_listpage as $a_pagelist) {
                    if ($a_pagelist['name'] == 'internship_submission') {
                        $s_internship_link = $a_pagelist['url'];break;
                    }
                }
            }
        }
        $a_listpagethesis = (isset($this->a_page_data['top_bar']['student_thesis'])) ? $this->a_page_data['top_bar']['student_thesis'] : 'ga ada';
        if (isset($a_listpagethesis['side_bar'])) {
            $a_listpagethesis = $a_listpagethesis['side_bar'];
            if ((is_array($a_listpagethesis)) AND (count($a_listpagethesis) > 0)) {
                foreach ($a_listpagethesis as $a_pagelist) {
                    if ($a_pagelist['name'] == 'proposal_submission') {
                        $s_thesis_prop_link = $a_pagelist['url'];
                    }
                    else if ($a_pagelist['name'] == 'work_submission') {
                        $s_thesis_work_link = $a_pagelist['url'];
                    }
                    else if ($a_pagelist['name'] == 'final_submission') {
                        $s_thesis_final_link = $a_pagelist['url'];
                    }
                }
            }
        }

        $this->a_page_data['supporting_link'] = [
            'internship_submission' => $s_internship_link,
            'proposal_submission' => $s_thesis_prop_link,
            'work_submission' => $s_thesis_work_link,
            'final_submission' => $s_thesis_final_link,
        ];

        $this->a_page_data['student_abroad_data'] = $mba_student_abroad_data;
        if ($mba_student_abroad_data) {
            $o_abroad_data = $mba_student_abroad_data[0];
            $this->a_page_data['abroad_transcript'] = $this->Iom->get_student_abroad_document([
                'se.exchange_id' => $o_abroad_data->exchange_id,
                'sed.document_type' => 'transcript'
            ]);
            $this->a_page_data['abroad_certificate'] = $this->Iom->get_student_abroad_document([
                'se.exchange_id' => $o_abroad_data->exchange_id,
                'sed.document_type' => 'certificate_degree'
            ]);
            $this->a_page_data['abroad_otherfile'] = $this->Iom->get_student_abroad_document([
                'se.exchange_id' => $o_abroad_data->exchange_id,
                'sed.document_type' => 'other_file'
            ]);
        }
        
        $this->a_page_data['body'] = $this->load->view('student/abroad/abroad_submission', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function view_doc($s_filenamelink = false)
    {
        if (!$s_filenamelink) {
            show_404();
        }

        $mbastudent_document = $this->Iom->get_student_abroad_document([
            'sed.document_link' => $s_filenamelink
        ]);
        if (!$mbastudent_document) {
            show_404();
        }

        $o_studentfile = $mbastudent_document[0];
        $s_path = 'student/'.$o_studentfile->student_batch.'/'.$o_studentfile->study_program_abbreviation.'/'.$o_studentfile->student_id.'/abroad_doc/'.$s_filenamelink;
        $s_path = urlencode(base64_encode($s_path));

        $a_filelink = explode('.', $s_filenamelink);
        $s_ext = $a_filelink[count($a_filelink) - 1];

        $s_path = str_replace("=", "", $s_path);
        $s_path = str_replace("==", "", $s_path);
        $s_path = str_replace("===", "", $s_path);

        // print("<pre>");var_dump($s_path);exit;
        redirect('file_manager/download_files/'.$s_path.'/'.urlencode($o_studentfile->document_name).'.'.$s_ext);
    }
}