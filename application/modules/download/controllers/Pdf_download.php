<?php
class Pdf_download extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Class_group_model', 'Cgm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('personal_data/Family_model', 'Fm');
        $this->load->model('employee/Employee_model', 'Emm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('finance/Invoice_model', 'Im');
        $this->load->model('finance/Finance_model', 'Fim');
        $this->load->model('alumni/Alumni_model', 'Alm');
        $this->load->model('thesis/Thesis_model', 'Tm');
    }

    public function sebentar()
    {
        $s_html = $this->load->view('devs/devs_view', $this->a_page_data, true);
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 9,
            'default_font' => 'sans_fonts',
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $s_filename = 'result.pdf';
        $mpdf->WriteHTML($s_html);
        $s_dir = APPPATH.'uploads/temp/';
        $mpdf->Output($s_dir.$s_filename, 'F');

        $s_mime = mime_content_type($s_dir.$s_filename);
        $a_path_info = pathinfo($s_dir.$s_filename);
        $s_file_ext = $a_path_info['extension'];
        header("Content-Type: ".$s_mime);
        readfile( $s_dir.$s_filename );
        exit;
        // print($s_html);
    }

    function generate_thesis_defense_result($s_thesis_defense_id) {
        $mba_thesis_defense_data = $this->Tm->get_thesis_defense_student([
            'td.thesis_defense_id' => $s_thesis_defense_id
        ]);

        if ($mba_thesis_defense_data) {
            $mba_student_advisor = $this->Tm->get_list_student_advisor([
                'ts.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id
            ], 'advisor', true);
            $mba_student_examiner = $this->Tm->get_list_student_advisor([
                'ts.thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id
            ], 'examiner');
            $mba_student_attendance = $this->General->get_where('thesis_defenses_absence', [
                'thesis_defense_id' => $s_thesis_defense_id,
                'thesis_student_id' => $mba_thesis_defense_data[0]->thesis_students_id
            ]);
            
            if ($mba_student_advisor) {
                foreach ($mba_student_advisor as $o_advisor) {
                    $mba_advisor_attendance = $this->General->get_where('thesis_defenses_absence', [
                        'thesis_defense_id' => $s_thesis_defense_id,
                        'student_advisor_id' => $o_advisor->student_advisor_id
                    ]);
                    $mba_thesis_score = $this->General->get_where('thesis_score', [
                        'thesis_defense_id' => $s_thesis_defense_id,
                        'students_advisor_id' => $o_advisor->student_advisor_id
                    ]);
                    
                    $mba_score_evaluation = false;
                    if ($mba_thesis_score) {
                        $mba_score_evaluation = $this->General->get_where('thesis_score_evaluation', [
                            'thesis_score_id' => $mba_thesis_score[0]->thesis_score_id
                        ]);
                    }

                    $mba_score_presentation = false;
                    if ($mba_thesis_score) {
                        $mba_score_presentation = $this->General->get_where('thesis_score_presentation', [
                            'thesis_score_id' => $mba_thesis_score[0]->thesis_score_id
                        ]);
                    }
                    $o_advisor->advisor_name = $this->Pdm->retrieve_title($o_advisor->personal_data_id);
                    $o_advisor->attendance = ($mba_advisor_attendance) ? $mba_advisor_attendance[0]->attendance : 'ABSENT';
                    $o_advisor->thesis_score = ($mba_thesis_score) ? $mba_thesis_score[0] : false;
                    $o_advisor->thesis_score_evaluation = ($mba_score_evaluation) ? $mba_score_evaluation[0] : false;
                    $o_advisor->thesis_score_presentation = ($mba_score_presentation) ? $mba_score_presentation[0] : false;
                    $o_advisor->number = substr($o_advisor->advisor_type, (strlen($o_advisor->advisor_type) - 1));
                }
            }
            if ($mba_student_examiner) {
                foreach ($mba_student_examiner as $o_examiner) {
                    $mba_attendance = $this->General->get_where('thesis_defenses_absence', [
                        'thesis_defense_id' => $s_thesis_defense_id,
                        'student_examiner_id' => $o_examiner->student_examiner_id
                    ]);
                    $mba_thesis_score = $this->General->get_where('thesis_score', [
                        'thesis_defense_id' => $s_thesis_defense_id,
                        'students_examiner_id' => $o_examiner->student_examiner_id
                    ]);
                    $mba_score_presentation = false;
                    if ($mba_thesis_score) {
                        $mba_score_presentation = $this->General->get_where('thesis_score_presentation', [
                            'thesis_score_id' => $mba_thesis_score[0]->thesis_score_id
                        ]);
                    }
                    $o_examiner->examiner_name = $this->Pdm->retrieve_title($o_examiner->personal_data_id);
                    $o_examiner->attendance = ($mba_attendance) ? $mba_attendance[0]->attendance : 'ABSENT';
                    $o_examiner->thesis_score = ($mba_thesis_score) ? $mba_thesis_score[0] : false;
                    $o_examiner->thesis_score_presentation = ($mba_score_presentation) ? $mba_score_presentation[0] : false;
                    $o_examiner->number = substr($o_examiner->examiner_type, (strlen($o_examiner->examiner_type) - 1));
                }
            }
            $mba_thesis_defense_data[0]->attendance = ($mba_student_attendance) ? $mba_student_attendance[0]->attendance : 'ABSENT';

            $this->a_page_data['thesis_defense'] = $mba_thesis_defense_data;
            $this->a_page_data['student_advisor'] = $mba_student_advisor;
            $this->a_page_data['student_examiner'] = $mba_student_examiner;
            $s_html = $this->load->view('thesis/form/thesis_score_data', $this->a_page_data, true);
            
            $mpdf = new \Mpdf\Mpdf([
                'default_font_size' => 9,
                'default_font' => 'sans_fonts',
                'mode' => 'utf-8',
                'format' => 'A4-P',
                'setAutoTopMargin' => 'stretch',
                'setAutoBottomMargin' => 'stretch'
            ]);

            $s_filename = 'Defense_result_'.str_replace(' ', '_', str_replace("'", "", $mba_thesis_defense_data[0]->personal_data_name)).'.pdf';

            $s_header_file = '<img src="' . base_url() . 'assets/img/header_of_file.png"/>';
            $s_footer_file = '<img src="' . base_url() . 'assets/img/footer_of_letter.png"/>';
            $mpdf->SetHTMLHeader($s_header_file);
            $mpdf->SetHTMLFooter($s_footer_file);
            $mpdf->WriteHTML($s_html);
            
            $s_dir = APPPATH.'uploads/academic/thesis_defense/'.$mba_thesis_defense_data[0]->academic_year_id.$mba_thesis_defense_data[0]->semester_type_id.'/';
            if(!file_exists($s_dir)){
                mkdir($s_dir, 0777, TRUE);
            }
            $mpdf->Output($s_dir.$s_filename, 'F');
            
            $a_return = array('code' => 0, 'filename' => $s_filename, 'filepath' => $s_dir);
        }
        else {
            $a_return = array('code' => 1, 'message' => 'No defense found!');
        }

        return $a_return;
    }

    public function generate_alumni_tracer()
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 9,
            'default_font' => 'sans_fonts',
            'mode' => 'utf-8',
            'format' => 'A4-P'
        ]);

        $a_data = [];
        $mba_faculty_data = $this->Spm->get_faculty_data();
        if ($mba_faculty_data) {
            foreach ($mba_faculty_data as $o_faculty) {
                $b_have_alumni = false;
                $mba_prodi_data = $this->Spm->get_study_program_lists([
                    'rf.faculty_id' => $o_faculty->faculty_id,
                    'rpsp.program_id' => 1
                ]);

                if ($mba_prodi_data) {
                    foreach ($mba_prodi_data as $o_prodi) {
                        $mba_alumni_list = $this->Stm->get_student_filtered([
                            'ds.study_program_id' => $o_prodi->study_program_id,
                            'ds.student_status' => 'graduated'
                        ]);

                        if ($mba_alumni_list) {
                            $b_have_alumni = true;
                            foreach ($mba_alumni_list as $o_alumni) {
                                $mba_answer_data = $this->Alm->get_alumni_answer_lists(['dqa.personal_data_id' => $o_alumni->personal_data_id]);
                                $o_alumni->answer_data = $mba_answer_data;
                                $o_alumni->last_submit = ($mba_answer_data) ? date('d M Y H:i:s', strtotime($mba_answer_data[0]->answer_timestamp)) : '';
                            }
                        }

                        $o_prodi->alumni_list = $mba_alumni_list;
                    }

                    $o_faculty->have_alumni = $b_have_alumni;
                    $o_faculty->prodi_list = $mba_prodi_data;
                }
            }

            $this->a_page_data['list_data'] = $mba_faculty_data;
            $s_html = $this->load->view('alumni/alumni_tracer', $this->a_page_data, true);

            $s_filename = 'Alumni_Tracer_Study_('.date('dMY-Hi').').pdf';

            $mpdf->WriteHTML($s_html);
            $s_dir = APPPATH.'uploads/alumni/tracer_study/report/'.date('Y').'/'.date('M').'/';
            if(!file_exists($s_dir)){
                mkdir($s_dir, 0777, TRUE);
            }
            $mpdf->Output($s_dir.$s_filename, 'F');

            // echo $s_html;
            if(!file_exists($s_dir.$s_filename)){
                $a_return = array('code' => 1, 'message' => 'Failed generated file!');
            }
            else{
                $a_return = array('code' => 0, 'file_name' => $s_filename);
                // $a_path_info = pathinfo($s_dir.$s_filename);
                // header('Content-Disposition: attachment; filename='.urlencode($s_filename));
                // readfile( $s_dir.$s_filename );
                // exit;
            }
            // print('<pre>');
            // var_dump($mba_faculty_data);exit;
        }else{
            $a_return = array('code' => 1, 'message' => 'No faculty found!');
        }

        return $a_return;
    }

    public function create_sample()
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 9,
            'default_font' => 'sans_fonts',
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch'
        ]);
        $s_html = $this->load->view('callback/template_sample', $this->a_page_data, TRUE);

        $s_filename = 'Invoice_Template_Student.pdf';
        $mpdf->WriteHTML($s_html);
        $s_dir = APPPATH.'uploads/finance/20221/invoice_billing/';
        if(!file_exists($s_dir)){
            mkdir($s_dir, 0777, TRUE);
        }
        
        $mpdf->Output($s_dir.$s_filename, 'F');
        // $a_return = array('code' => 0, 'file_name' => $s_filename);

        // echo $s_html;exit;
        if(!file_exists($s_dir.$s_filename)){
            return show_404();
        }
        else{
            // print($s_html);exit;
            // print('<pre>');var_dump($this->a_page_data);exit;
            $a_path_info = pathinfo($s_dir.$s_filename);
            header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_dir.$s_filename );
            exit;
        }
    }

    public function generate_df_file($s_df_id)
    {
        $this->load->model('apps/Gsr_model', 'Grm');
        $this->load->model('apps/Dfrf_model', 'Drm');
        $df_data = $this->Drm->get_df_data(['dm.df_id' => $s_df_id]);
        if ($df_data) {
            $mba_userdata = $this->General->get_where('dt_personal_data', ['personal_data_id' => $df_data[0]->personal_data_id_requested]);
            if ($mba_userdata) {
                $s_firts_char_user = substr($mba_userdata[0]->personal_data_name, 0, 1);
                $this->a_page_data['df_data'] = $df_data[0];
                $this->a_page_data['df_details'] = $this->Drm->get_df_details(['dd.df_id' => $s_df_id]);

                $this->a_page_data['df_request_data'] = $this->Drm->get_df_status_log(['ds.df_id' => $s_df_id, 'current_progress' => 'requested']);
                $this->a_page_data['df_check_data'] = $this->Drm->get_df_status_log(['ds.df_id' => $s_df_id, 'current_progress' => 'checked', 'status_action' => 'approve']);
                $this->a_page_data['df_approve_data'] = $this->Drm->get_df_status_log(['ds.df_id' => $s_df_id, 'current_progress' => 'approved', 'status_action' => 'approve']);
                $this->a_page_data['df_finish_data'] = $this->Drm->get_df_status_log(['ds.df_id' => $s_df_id, 'current_progress' => 'finish', 'status_action' => 'approve']);

                $s_html_body = $this->load->view('apps/gsr/misc/df_view', $this->a_page_data, true);
                $s_filename = str_replace(' ', '_', $df_data[0]->df_type).'-'.str_replace(' ', '_', $df_data[0]->df_number).'.pdf';

                $mpdf = new \Mpdf\Mpdf([
                    'default_font_size' => 9,
                    'default_font' => 'sans_fonts',
                    'mode' => 'utf-8',
                    'format' => 'A4-P',
                    'setAutoTopMargin' => 'stretch',
                    'setAutoBottomMargin' => 'stretch'
                ]);

                $s_header_file = '<img src="' . base_url() . 'assets/img/header_of_file.png"/>';
                $s_footer_file = '<img src="' . base_url() . 'assets/img/footer_of_letter.png"/>';
                $mpdf->SetHTMLHeader($s_header_file);
                $mpdf->SetHTMLFooter($s_footer_file);

                $mpdf->WriteHTML($s_html_body);
                $s_dir = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$mba_userdata[0]->personal_data_id.'/'.date('Y', strtotime($df_data[0]->df_date_created)).'/gsr/'.$df_data[0]->df_number.'/';
                if(!file_exists($s_dir)){
                    mkdir($s_dir, 0777, TRUE);
                }
                
                $mpdf->Output($s_dir.$s_filename, 'F');
                if(!file_exists($s_dir.$s_filename)){
                    $a_return = ['code' => 1, 'message' => 'failed generate file!'];
                }
                else{
                    $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename), 'pathfile' => $s_dir];

                    $mba_gsr_attachment = $this->Grm->get_where('dt_gsr_attachment', ['gsr_id' => $df_data[0]->gsr_id, 'document_name' => $s_filename]);
                    if (!$mba_gsr_attachment) {
                        $this->Grm->save_gsr_attachment([
                            'gsr_file_id' => $this->uuid->v4(),
                            'gsr_id' => $df_data[0]->gsr_id,
                            'document_link' => date('Y', strtotime($df_data[0]->df_date_created)).'/gsr/'.str_replace(' ', '_', $df_data[0]->df_number).'/'.$s_filename,
                            'document_name' => $s_filename,
                            'gsr_show' => 'false',
                            'df_show' => 'true',
                            'date_added' => $df_data[0]->df_date_created
                        ]);
                    }
                }
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Failed retrieve data!'];
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'.$s_gsr_id];
        }

        return $a_return;
    }

    public function generate_gsr_file($s_gsr_id)
    {
        $this->load->model('apps/Gsr_model', 'Grm');
        $gsr_data = $this->Grm->get_gsr_data(['gm.gsr_id' => $s_gsr_id]);
        // $mba_userdata = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
        if ($gsr_data) {
            $mba_userdata = $this->General->get_where('dt_personal_data', ['personal_data_id' => $gsr_data[0]->personal_data_id_request]);
            if ($mba_userdata) {
                $s_firts_char_user = substr($mba_userdata[0]->personal_data_name, 0, 1);
                $s_filename = str_replace(' ', '_', $gsr_data[0]->gsr_code).'.pdf';
                $s_personal_document_id = $this->uuid->v4();
                $generate_sign = $this->General->generate_sign($s_personal_document_id, false, $s_gsr_id, false, $s_filename, 'portal_gsr.dt_gsr_main');

                $this->a_page_data['gsr_data'] = $gsr_data[0];
                $this->a_page_data['gsr_details'] = $this->Grm->get_gsr_details(['gd.gsr_id' => $s_gsr_id]);
                // $this->a_page_data['gsr_request_data'] = $this->Grm->get_gsr_status_log(['gs.gsr_id' => $s_gsr_id, 'current_progress' => 'requested']);
                // $this->a_page_data['gsr_review_data'] = $this->Grm->get_gsr_status_log(['gs.gsr_id' => $s_gsr_id, 'current_progress' => 'reviewed', 'status_action' => 'approve']);
                // $this->a_page_data['gsr_approve_data'] = $this->Grm->get_gsr_status_log(['gs.gsr_id' => $s_gsr_id, 'current_progress' => 'approved', 'status_action' => 'approve']);
                // $this->a_page_data['gsr_finish_data'] = $this->Grm->get_gsr_status_log(['gs.gsr_id' => $s_gsr_id, 'current_progress' => 'finish', 'status_action' => 'approve']);
                $this->a_page_data['gsr_request_data'] = $this->Grm->get_gsr_user(['pdd.key_table' => 'portal_gsr.dt_gsr_main', 'key_id' => $gsr_data[0]->gsr_id, 'em.personal_data_id' => $gsr_data[0]->personal_data_id_request], ['pdd.date_added' => 'DESC'])[0];
                $this->a_page_data['gsr_review_data'] = $this->Grm->get_gsr_user(['pdd.key_table' => 'portal_gsr.dt_gsr_main', 'key_id' => $gsr_data[0]->gsr_id, 'em.personal_data_id' => $gsr_data[0]->personal_data_id_review], ['pdd.date_added' => 'DESC'])[0];
                $this->a_page_data['gsr_approve_data'] = $this->Grm->get_gsr_user(['pdd.key_table' => 'portal_gsr.dt_gsr_main', 'key_id' => $gsr_data[0]->gsr_id, 'em.personal_data_id' => $gsr_data[0]->personal_data_id_approved], ['pdd.date_added' => 'DESC'])[0];
                $this->a_page_data['gsr_finish_data'] = $this->Grm->get_gsr_user(['pdd.key_table' => 'portal_gsr.dt_gsr_main', 'key_id' => $gsr_data[0]->gsr_id, 'em.personal_data_id' => $gsr_data[0]->personal_data_id_finishing], ['pdd.date_added' => 'DESC'])[0];

                if ($generate_sign) {
                    $mpdf = new \Mpdf\Mpdf([
                        'default_font_size' => 7,
                        'default_font' => 'sans_fonts',
                        'mode' => 'utf-8',
                        'format' => 'A4-P',
                        'setAutoTopMargin' => 'stretch',
                        'setAutoBottomMargin' => 'stretch'
                    ]);
    
    
                    $s_html_body = $this->load->view('apps/gsr/misc/gsr_view', $this->a_page_data, true);
                    $s_header_file = '<img src="' . base_url() . 'assets/img/header_of_file.png"/>';
                    $s_footer_file = '<img src="' . base_url() . 'assets/img/footer_of_letter.png"/>';
                    $mpdf->SetHTMLHeader($s_header_file);
                    $mpdf->SetHTMLFooter($s_footer_file);
    
                    $mpdf->WriteHTML($s_html_body);
                    $s_dir = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$mba_userdata[0]->personal_data_id.'/'.date('Y', strtotime($gsr_data[0]->gsr_date_request)).'/gsr/'.str_replace(' ', '_', $gsr_data[0]->gsr_code).'/';
                    if(!file_exists($s_dir)){
                        mkdir($s_dir, 0777, TRUE);
                    }
                    
                    $mpdf->Output($s_dir.$s_filename, 'F');
                    if(!file_exists($s_dir.$s_filename)){
                        $a_return = ['code' => 1, 'message' => 'failed generate file!'];
                    }
                    else{
                        $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename), 'pathfile' => $s_dir, 'doc_key' => $s_personal_document_id];
    
                        $mba_gsr_attachment = $this->Grm->get_where('dt_gsr_attachment', ['gsr_id' => $s_gsr_id, 'document_name' => $s_filename]);
                        if (!$mba_gsr_attachment) {
                            $this->Grm->save_gsr_attachment([
                                'gsr_file_id' => $this->uuid->v4(),
                                'gsr_id' => $s_gsr_id,
                                'document_link' => date('Y', strtotime($gsr_data[0]->gsr_date_request)).'/gsr/'.str_replace(' ', '_', $gsr_data[0]->gsr_code).'/'.$s_filename,
                                'document_name' => $s_filename,
                                'gsr_show' => 'true',
                                'df_show' => 'true',
                                'date_added' => $gsr_data[0]->gsr_date_request
                            ]);
                        }
                    }
                }
                else {
                    $a_return = ['code' => 3, 'message' => 'Failed generate digital sign!'];
                }
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Failed retrieve GSR data!'];
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'.$s_gsr_id];
        }

        return $a_return;
        // print('<pre>');var_dump($a_return);exit;
    }

    function generate_single_invoice($s_student_id, $s_invoice_id) {
        $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
        $mba_invoice_data = $this->General->get_where('dt_invoice', ['invoice_id' => $s_invoice_id]);
        if (($mba_student_data) AND ($mba_invoice_data)) {
            $o_student_data = $mba_student_data[0];
            $o_invoice = $mba_invoice_data[0];
            $mpdf = new \Mpdf\Mpdf([
                'default_font_size' => 9,
                'default_font' => 'sans_fonts',
                'mode' => 'utf-8',
                'format' => 'A4-P',
                'setAutoTopMargin' => 'stretch',
                'setAutoBottomMargin' => 'stretch'
            ]);
            
            $s_header_file = '<img src="' . base_url() . 'assets/img/header_of_file.png"/>';
            $s_footer_file = '<img src="' . base_url() . 'assets/img/footer_of_letter.png"/>';
            $mpdf->SetHTMLHeader($s_header_file);
            $mpdf->SetHTMLFooter($s_footer_file);
    
            $s_html = modules::run('callback/api/get_payment_method', $s_invoice_id, 'return', true);
            $mpdf->WriteHTML($s_html);

            $s_filename = $o_invoice->invoice_number.'_'.(str_replace(' ', '_', $o_student_data->personal_data_name)).'.pdf';
            $s_dir = STUDENTPATH.$o_student_data->personal_data_path.'invoice/';
            if(!file_exists($s_dir)){
                mkdir($s_dir, 0777, TRUE);
            }
            
            $mpdf->Output($s_dir.$s_filename, 'F');
            // print $s_html;
            $a_path_info = pathinfo($s_dir.$s_filename);
            $s_mime = mime_content_type($s_dir.$s_filename);
            header("Content-Type: ".$s_mime);
            // header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_dir.$s_filename );
            exit;
        }
    }

    public function generate_invoice_billing($s_invoice_id, $s_partner = false, $b_generate = false)
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 9,
            'default_font' => 'sans_fonts',
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch'
        ]);

        $mbo_invoice_data = $this->Im->get_invoice_data(['di.invoice_id' => $s_invoice_id])[0];
        if ($mbo_invoice_data) {
            $mbo_personal_data = $this->Pdm->get_personal_data_by_id($mbo_invoice_data->personal_data_id);
            if ($mbo_personal_data) {
                $mbo_invoice_data = (array)$mbo_invoice_data;
                $mbo_personal_data = (array)$mbo_personal_data;
                $mbo_invoice_data = array_merge($mbo_invoice_data, $mbo_personal_data);
                $mbo_invoice_data = (object) $mbo_invoice_data;

                $mbo_unpaid_invoice = $this->Im->get_unpaid_invoice(['di.invoice_id' => $s_invoice_id])[0];
                
                if ($mbo_unpaid_invoice) {
                    // $checker = modules::run('callback/api/check_invoice', $mbo_unpaid_invoice);
                    // print('<pre>');
                    // var_dump($checker);exit;
                }

                $mbo_student_data = $this->Stm->get_student_by_personal_data_id($mbo_invoice_data->personal_data_id);
                $mbo_family_data = $this->Fm->get_family_by_personal_data_id($mbo_invoice_data->personal_data_id);
                $mba_parent_email = false;

                if($mbo_family_data){
                    $mba_family_members = $this->Fm->get_family_members($mbo_family_data->family_id, array(
                        'family_member_status != ' => 'child'
                    ));
                    if($mba_family_members){
                        $mba_parent_email = array();
                        foreach($mba_family_members as $family){
                            array_push($mba_parent_email, $family->personal_data_email);
                        }
                    }
                }

                $mba_sub_invoice = $this->Im->get_sub_invoice_data(['dsi.invoice_id' => $mbo_invoice_data->invoice_id]);
                $mba_invoice_full_payment = $this->Im->get_invoice_full_payment($mbo_invoice_data->invoice_id);
                $d_total_amount_billing = ($mba_invoice_full_payment) ? $mba_invoice_full_payment->sub_invoice_details_amount_total : 0;
                $s_va_number = ($mba_invoice_full_payment) ? $mba_invoice_full_payment->sub_invoice_details_va_number : '';

                if($mba_sub_invoice){
                    foreach($mba_sub_invoice as $sub_invoice){
                        $mba_sub_invoice_details = $this->Im->get_invoice_data(['dsid.sub_invoice_id' => $sub_invoice->sub_invoice_id]);
                        $sub_invoice->sub_invoice_details_data = false;
                        
                        if($mba_sub_invoice_details){
                            $sub_invoice->sub_invoice_details_data = $mba_sub_invoice_details;
                        }
                    }
                }

                $mba_invoice_details = $this->Im->get_invoice_details([
                    'did.invoice_id' => $mbo_invoice_data->invoice_id
                ]);
                
                $this->a_page_data['sub_invoice_data'] = $mba_sub_invoice;
                $this->a_page_data['invoice_data'] = $mbo_invoice_data;
                $this->a_page_data['invoice_details'] = $mba_invoice_details;
                $this->a_page_data['student_data'] = $mbo_student_data;

                $s_payment_type = 'Tuition Fee';
                // $s_payment_type_code = '02';
                $s_payment_type_code = ($s_va_number != '') ? substr($s_va_number, 4, 2) : '02';
                if ($mba_invoice_details) {
                    foreach ($mba_invoice_details as $o_invoice_details) {
                        if ($o_invoice_details->fee_amount_type == 'main') {
                            $mbo_amount_type = $this->Im->get_payment_type($o_invoice_details->payment_type_code);
                            $s_payment_type = $mbo_amount_type->payment_type_name;
                            $s_payment_type_code = $mbo_amount_type->payment_type_code;
                        }
                    }
                }

                $s_header_file = '<img src="' . base_url() . 'assets/img/header_of_file.png"/>';
                $s_footer_file = '<img src="' . base_url() . 'assets/img/footer_of_letter.png"/>';
                $mpdf->SetHTMLHeader($s_header_file);
                $mpdf->SetHTMLFooter($s_footer_file);

                if($mbo_invoice_data->invoice_allow_fine == 'yes'){
                    $s_html = $this->load->view('callback/email_reminder_template_fine', $this->a_page_data, TRUE);
                }
                else{
                    if ($s_payment_type_code != '88') {
                        $s_html = $this->load->view('callback/email_reminder_template', $this->a_page_data, TRUE);
                    }
                }

                if (in_array($s_payment_type_code, ['03', '05', '07', '08'])) {
                    $s_html = $this->load->view('callback/billing_template', $this->a_page_data, TRUE);
                }
                else if ($s_payment_type_code == '09') {
                    $a_template_param = [
                        'invoice_number' => $mbo_invoice_data->invoice_number,
                        'personal_data_name' => $mbo_invoice_data->personal_data_name,
                        'study_program_name' => $mbo_student_data->study_program_name,
                        'description' => $mba_invoice_full_payment->sub_invoice_details_description,
                        'invoice_details' => $mba_invoice_details,
                        'sub_invoice_details_va_number' => $mba_invoice_full_payment->sub_invoice_details_va_number,
                        'sub_invoice_details_amount_total' => $mba_invoice_full_payment->sub_invoice_details_amount_total,
                        'sub_invoice_details_deadline' => $mba_invoice_full_payment->sub_invoice_details_deadline
                    ];

                    $s_html = modules::run('messaging/text_template/graduation_billing', $a_template_param);
                    $s_html = str_replace("\r\n", '<br>', $s_html);
                    $s_html = str_replace("\n", '<br>', $s_html);
                }
                else if ($s_payment_type_code == '17') {
                    $a_template_param = [
                        'invoice_number' => $mbo_invoice_data->invoice_number,
                        'personal_data_name' => $mbo_invoice_data->personal_data_name,
                        'study_program_name' => $mbo_student_data->study_program_name,
                        'description' => $mba_invoice_full_payment->sub_invoice_details_description,
                        'invoice_details' => $mba_invoice_details,
                        'sub_invoice_details_va_number' => $mba_invoice_full_payment->sub_invoice_details_va_number,
                        'sub_invoice_details_amount_total' => $mba_invoice_full_payment->sub_invoice_details_amount_total,
                        'sub_invoice_details_deadline' => $mba_invoice_full_payment->sub_invoice_details_deadline
                    ];

                    $s_html = modules::run('messaging/text_template/additional_coupon_billing', $a_template_param);
                    $s_html = str_replace("\r\n", '<br>', $s_html);
                    $s_html = str_replace("\n", '<br>', $s_html);
                }
                else if (in_array($s_payment_type_code, ['04'])) {
                    $mba_score_student = $this->Scm->get_score_data_transcript([
						'sc.student_id' => $mbo_student_data->student_id,
						'sc.semester_id' => $mba_invoice_details[0]->semester_id,
						'sc.score_approval' => 'approved'
					]);
                    
                    $a_score_id = [];
                    $i_count_subject = 0;
					$s_subject_list = '';
					foreach ($mba_score_student as $o_score) {
						$i_count_subject++;
						$s_subject_list.= "{$i_count_subject}. {$o_score->subject_name}\n";
                        array_push($a_score_id, $o_score->score_id);
					}

                    $a_template_param = array(
                        'invoice_id' => $mbo_invoice_data->invoice_id,
                        'subjects_count' => $i_count_subject,
						'transfer_amount' => $d_total_amount_billing,
						'va_number' => $s_va_number,
						'personal_data_name' => $mbo_personal_data['personal_data_name'],
						'a_score_id' => $a_score_id,
						'payment_deadline' => date('d F Y', strtotime($mba_invoice_full_payment->sub_invoice_details_deadline))
					);

                    $s_html = modules::run('messaging/text_template/short_semester_billing', $a_template_param);
                    $s_html = str_replace("\r\n", '<br>', $s_html);
                    $s_html = str_replace("\n", '<br>', $s_html);
                }
                else if ($s_payment_type_code == '14') {
                    $s_html = $this->load->view('callback/billing_international_full_time', $this->a_page_data, TRUE);
                }
                else if ($s_payment_type_code == '88') {
                    $s_html = $this->load->view('callback/billing_iulifest', $this->a_page_data, TRUE);
                    $s_header_file = '<img src="' . base_url() . 'assets/img/header_of_file.png"/>';
                    $s_footer_file = '<img src="' . base_url() . 'assets/img/footer_of_letter.png"/>';
                    $mpdf->SetHTMLHeader($s_header_file);
                    $mpdf->SetHTMLFooter($s_footer_file);
                }
                
                if ($s_partner == 'srh') {
                    $s_html = $this->load->view('callback/email_template_srh', $this->a_page_data, TRUE);
                    $s_header_file = '<img src="' . base_url() . 'assets/img/header_invoice_srh.PNG"/>';
                    $s_footer_file = '<img src="' . base_url() . 'assets/img/footer_of_file.jpeg"/>';
                    $mpdf->SetHTMLHeader($s_header_file);
                    $mpdf->SetHTMLFooter($s_footer_file);
                    // print($s_header_file);exit;
                }

                $mbo_semester_active = $this->Smm->get_active_semester();
                $s_filename = $mbo_invoice_data->invoice_number.'_'.(str_replace(' ', '_', $mbo_invoice_data->personal_data_name)).'.pdf';

                $mpdf->WriteHTML($s_html);
                $s_dir = APPPATH.'uploads/finance/'.$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id.'/invoice_billing/';
                if(!file_exists($s_dir)){
                    mkdir($s_dir, 0777, TRUE);
                }
                
                $mpdf->Output($s_dir.$s_filename, 'F');
                // $a_return = array('code' => 0, 'file_name' => $s_filename);

                // echo $s_html;exit;
                if(!file_exists($s_dir.$s_filename)){
                    return show_404();
                }
                else if ($b_generate) {
                    return $s_dir.$s_filename;
                }
                else{
                    // print($s_html);exit;
                    // print('<pre>');var_dump($this->a_page_data);exit;
                    $a_path_info = pathinfo($s_dir.$s_filename);
                    $s_mime = mime_content_type($s_dir.$s_filename);
                    header("Content-Type: ".$s_mime);
                    header('Content-Disposition: attachment; filename='.urlencode($s_filename));
                    readfile( $s_dir.$s_filename );
                    exit;
                }
            }else{
                // print('a');
                show_404();
            }
        }else{
            show_404();
        }
        // print('<pre>');
        // var_dump($mba_invoice_data);
    }

    public function generate_class_report_test($s_class_master_id, $s_employee_id = false)
    {
        // print('ada');exit;
        $mbo_class_master_data = $this->Cgm->get_class_master_filtered(['cm.class_master_id' => $s_class_master_id])[0];
        $mba_class_master_study_program = $this->Cgm->get_class_master_study_program($s_class_master_id);
        $o_class_study_program = $mba_class_master_study_program[0];
        $s_folder_name = $mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id.'_Class_Report_'.str_replace(' ', '-', strtolower($mbo_class_master_data->subject_name)).'_'.$mbo_class_master_data->class_master_id;
        $s_folder_name = str_replace('&amp;', 'and', $s_folder_name);
        $s_folder_name = str_replace('&', 'and', $s_folder_name);
        $s_folder_name = str_replace('/', '-', $s_folder_name);
        $s_path_master = APPPATH."uploads/academic/class_reporting/".$mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id."/";
        
        $s_file_path = $s_path_master.$s_folder_name."/";
        
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

        $s_student_absence_report_file = $this->generate_student_absence_report($mbo_class_master_data, $s_file_path, $o_class_study_program->study_program_id, $s_employee_id);
        $a_path_info = pathinfo($s_file_path.$s_student_absence_report_file);
        header('Content-Disposition: attachment; filename='.urlencode($s_student_absence_report_file));
        readfile( $s_file_path.$s_student_absence_report_file );
        exit;
    }

    function generate_absence_class() {
        $s_class_master_id = 'efd75722-71a7-437c-81b5-9b5c6953ded3';
        $s_study_program_id = '903eb8ee-159e-406b-8f7e-38d63a961ea4';
        $s_employee_id = '7d63b00f-a29a-4eb5-9758-9f0757cbe982';
        $mbo_class_master_data = $this->Cgm->get_class_master_filtered(['cm.class_master_id' => $s_class_master_id])[0];
        $s_folder_name = $mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id.'_Class_Report_'.str_replace(' ', '-', strtolower($mbo_class_master_data->subject_name)).'_'.$mbo_class_master_data->class_master_id;
        $s_folder_name = str_replace('&amp;', 'and', $s_folder_name);
        $s_folder_name = str_replace('&', 'and', $s_folder_name);
        $s_folder_name = str_replace('/', '-', $s_folder_name);

        $s_path_master = APPPATH."uploads/academic/class_reporting/".$mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id."/";
        $s_file_path = $s_path_master.$s_folder_name."/";

        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }

        $s_student_absence_report_filename = $this->generate_absence_report($mbo_class_master_data, $s_file_path, $s_study_program_id, $s_employee_id);
        $s_mime = mime_content_type($s_file_path.$s_student_absence_report_filename);
        header("Content-Type: ".$s_mime);
        readfile( $s_file_path.$s_student_absence_report_filename );
        exit;
    }

    // public function generate_class_report($s_class_master_id, $s_employee_id = false)
    public function generate_class_report()
    {
        if ($this->input->is_ajax_request()) {
            $s_class_master_id = $this->input->post('class_master_id');
            $s_employee_id = $this->input->post('employee_id');
            $s_employee_id = (($s_employee_id != '') OR ($s_employee_id !== null)) ? $s_employee_id : false;

            $mbo_class_master_data = $this->Cgm->get_class_master_filtered(['cm.class_master_id' => $s_class_master_id])[0];

            if ($mbo_class_master_data) {
                $mba_class_master_study_program = $this->Cgm->get_class_master_study_program($s_class_master_id);
                if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                    // print('<pre>');var_dump($mba_class_master_study_program);exit;
                }

                $s_folder_name = $mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id.'_Class_Report_'.str_replace(' ', '-', strtolower($mbo_class_master_data->subject_name)).'_'.$mbo_class_master_data->class_master_id;
                $s_folder_name = str_replace('&amp;', 'and', $s_folder_name);
                $s_folder_name = str_replace('&', 'and', $s_folder_name);
                $s_folder_name = str_replace('/', '-', $s_folder_name);
                $s_path_master = APPPATH."uploads/academic/class_reporting/".$mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id."/";
                
                $s_file_path = $s_path_master.$s_folder_name."/";
                
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $a_file_data = array();
                if ($mba_class_master_study_program) {
                    foreach ($mba_class_master_study_program as $o_class_study_program) {
                        // $s_lecturer_absence_report_file = $this->generate_lecturer_absence_report($mbo_class_master_data, $s_file_path, $o_class_study_program->study_program_id, $s_employee_id);
                        // // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                        //     $s_student_absence_report_file = $this->generate_student_absence_report($mbo_class_master_data, $s_file_path, $o_class_study_program->study_program_id, $s_employee_id);
                        // // }else {
                        // //     $s_student_absence_report_file = $this->generate_student_absence_report2($mbo_class_master_data, $s_file_path, $o_class_study_program->study_program_id, $s_employee_id);
                        // // }
                        $s_student_absence_report_file = $this->generate_absence_report($mbo_class_master_data, $s_file_path, $o_class_study_program->study_program_id, $s_employee_id);
                        $s_student_score_report_file = $this->generate_student_score_report($mbo_class_master_data, $s_file_path, $o_class_study_program->study_program_id, $s_employee_id);

                        // array_push($a_file_data, $s_lecturer_absence_report_file);
                        array_push($a_file_data, $s_student_absence_report_file);
                        array_push($a_file_data, $s_student_score_report_file);
                    }
                }

                if (count($a_file_data) > 0) {
                    $a_return = array(
                        'code' => 0,
                        'a_file' => $a_file_data,
                        's_semester' => $mbo_class_master_data->running_year.$mbo_class_master_data->class_semester_type_id,
                        's_message' => $s_folder_name
                    );
                }else{
                    $a_return= array('code' => 1, 'message' => 'No file generated!');
                }
            }else{
                $a_return= array('code' => 1, 'message' => 'Class not found!');
            }

            print json_encode($a_return);
            // $this->test_download($a_return);
        }
    }

    public function download_class_report()
    {
        $this->load->library('zip');
        $s_semester = $this->input->get('semester');
        $s_folder = $this->input->get('class');
        $file_count = $this->input->get('file_count');

        $s_file_path = APPPATH.'uploads/academic/class_reporting/'.$s_semester.'/'.$s_folder.'/';
        
        for ($i=0; $i < $file_count; $i++) { 
            $s_filename = $this->input->get('file'.$i);
            $this->zip->read_file($s_file_path.$s_filename);
        }

        $this->zip->download($s_folder.'.zip');
    }

    function generate_receipt($s_bni_transaction_id) {
        $mba_transaction_data = $this->Fim->get_payment_history([
            'bt.transaction_type' => 'paymentnotification',
            'btp.bni_transactions_id' => $s_bni_transaction_id
        ], [
            'sid.sub_invoice_details_real_datetime_deadline' => 'ASC'
        ]);

        if ($mba_transaction_data) {
            if ((is_null($mba_transaction_data[0]->receipt_no)) AND (is_null($mba_transaction_data[0]->receipt_number))) {
                $receipt_no = $this->Fim->get_receipt_no($mba_transaction_data[0]->transaction_date_added);
                $a_receipt = explode('/', $receipt_no);
                $receipt_number = $a_receipt[2];
            }
            else {
                $receipt_no = $mba_transaction_data[0]->receipt_no;
                $receipt_number = $mba_transaction_data[0]->receipt_number;
            }
            
            $s_personal_data_id = $mba_transaction_data[0]->personal_data_id;
            $s_key_id = $mba_transaction_data[0]->bni_transactions_id;
            $s_id_type = 'portal_main.bni_transactions';
            $mba_student_data = $this->Stm->get_student_filtered(['ds.personal_data_id' => $s_personal_data_id, 'ds.student_status != ' => 'resign']);

            if ($mba_student_data) {
                $s_filepath = APPPATH.'uploads/student/'.$mba_student_data[0]->academic_year_id.'/'.$mba_student_data[0]->study_program_abbreviation.'/'.$s_personal_data_id.'/finance/receipt/';
            }
            else {
                $s_filepath = APPPATH.'uploads/'.$s_personal_data_id.'/receipt/';
            }

            if(!file_exists($s_filepath)){
                mkdir($s_filepath, 0777, TRUE);
            }

            $s_file_name = 'Receipt '.str_replace('/', '-', $receipt_no);
            $s_filename = $s_file_name.'.pdf';
            $a_invoice_number = [];
            if ($mba_transaction_data) {
                foreach ($mba_transaction_data as $o_transaction) {
                    if (!in_array($o_transaction->invoice_number, $a_invoice_number)) {
                        array_push($a_invoice_number, $o_transaction->invoice_number);
                    }
                }
            }
            
            $this->General->update_data('bni_transactions', [
                'receipt_number' => $receipt_number,
                'receipt_no' => $receipt_no
            ], ['bni_transactions_id' => $s_bni_transaction_id]);

            $mba_personal_document_data = $this->General->get_where('dt_personal_document', [
                'key_table' => $s_id_type,
                'key_id' => $s_key_id
            ]);
            if (!$mba_personal_document_data) {
                $s_personal_document_id = $this->uuid->v4();
                $generate_sign = $this->General->generate_sign($s_personal_document_id, false, $s_key_id, false, $s_filename, $s_id_type, $s_personal_data_id);
            }
            else {
                $s_personal_document_id = $mba_personal_document_data[0]->personal_document_id;
            }
            
            $this->a_page_data['transaction_data'] = $mba_transaction_data;
            $this->a_page_data['invoice_no'] = implode(' & ', $a_invoice_number);
            $this->a_page_data['receipt_no'] = $receipt_no;
            // $this->a_page_data['spelling_total_amount'] = ($mba_transaction_data) ? number_to_words($mba_transaction_data[0]->total_payment_amount) : '';
            // print('<pre>');var_dump($this->a_page_data['transaction_data']);exit;
            $this->a_page_data['personal_document_id'] = $s_personal_document_id;
            $this->a_page_data['body'] = $this->load->view('finance/form/receipt_form', $this->a_page_data, true);
            $s_html = $this->load->view('layout_document', $this->a_page_data, true);
    
            $mpdf = new \Mpdf\Mpdf([
                'default_font_size' => 9,
                'default_font' => 'sans_fonts',
                'mode' => 'utf-8',
                'format' => 'A4-P',
                'setAutoTopMargin' => 'stretch',
                'setAutoBottomMargin' => 'stretch',
            ]);
            
            $mpdf->SetHTMLHeader('<img src="./assets/img/header_of_file.png" alt="">');
            $mpdf->SetHTMLFooter('<img src="./assets/img/footer_of_letter.png" alt="">');
            $mpdf->WriteHTML($s_html);
            
            $mpdf->Output($s_filepath.$s_filename, 'F');

            return [
                'filepath' => $s_filepath,
                'filename' => $s_filename
            ];
        }
        else {
            return false;
        }
    }

    public function generate_student_score_report($o_class_master_data, $s_file_path, $s_study_program_id, $s_employee_id = false)
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sans_fonts',
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'setAutoTopMargin' => 'pad',
            'setAutoBottomMargin' => 'pad'
        ]);

        $s_semester_academic = $o_class_master_data->running_year.$o_class_master_data->class_semester_type_id;
        $mbo_study_program_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
        if (!$mbo_study_program_data) {
            print($s_study_program_id);exit;
        }
        $a_student_prodi_in = [$s_study_program_id];
        if (is_null($mbo_study_program_data->study_program_main_id)) {
            $mba_sub_study_program = $this->General->get_where('ref_study_program', [
                'study_program_main_id' => $s_study_program_id
            ]);
            if ($mba_sub_study_program) {
                foreach ($mba_sub_study_program as $o_stprodi) {
                    array_push($a_student_prodi_in, $o_stprodi->study_program_id);
                }
            }
        }

        // $a_clausa = (!is_null($mbo_study_program_data->study_program_main_id)) ? ['st.study_program_id' => $s_study_program_id] : ['sp.study_program_main_id' => $s_study_program_id];
        // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        // if ($s_study_program_id == 'e0c165f7-a2f8-4372-aa6b-20e3dbc61f32') {
        //     print('<pre>');var_dump($a_clausa);exit;
        // }
        $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer(['class_master_id' => $o_class_master_data->class_master_id]);
        $a_lecturer_name = [];
        if ($mba_class_master_lecturer) {
            foreach ($mba_class_master_lecturer as $o_lecturer) {
                $s_lecturer = $this->Pdm->retrieve_title($o_lecturer->personal_data_id);
                if (!in_array($s_lecturer, $a_lecturer_name)) {
                    if (($s_employee_id) AND ($o_lecturer->employee_id == $s_employee_id)) {
                        array_push($a_lecturer_name, $s_lecturer);
                    }else if(!$s_employee_id){
                        array_push($a_lecturer_name, $s_lecturer);
                    }
                }
            }
        }

        $mba_score_list = $this->Cgm->get_class_master_student($o_class_master_data->class_master_id, false, [
            'st.study_program_id' => $a_student_prodi_in
        ]);
        if ($mba_score_list) {
            foreach ($mba_score_list as $o_score) {
                $o_score->o_student_data = $this->Stm->get_student_by_id($o_score->student_id);
                $o_score->d_absence = 100 - (floatval((is_null($o_score->score_absence) ? 0 : $o_score->score_absence)));
                $o_score->d_repeat = (is_null($o_score->score_repetition_exam)) ? '-' : intval(round($o_score->score_repetition_exam, 0, PHP_ROUND_HALF_UP));
            }
        }

        $s_deans = $this->Pdm->retrieve_title($mbo_study_program_data->deans_id);

        $this->a_page_data['o_study_program'] = $mbo_study_program_data;
        $this->a_page_data['o_class_master_data'] = $o_class_master_data;
        $this->a_page_data['mba_score'] = $mba_score_list;
        $this->a_page_data['a_lecturer_name'] = $a_lecturer_name;
        $this->a_page_data['s_deans'] = $s_deans;

        $html = '';
        $html .= $this->load->view('student_score_report', $this->a_page_data, true);

        $s_file_name_subject = str_replace(' ', '-', strtolower($o_class_master_data->subject_name));
        $s_file_name_subject = str_replace('&amp;', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('&', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('/', '-', $s_file_name_subject);
        $s_semester_akademic = $o_class_master_data->running_year.$o_class_master_data->class_semester_type_id;
        $s_file_name = 'Student_score_'.$s_file_name_subject.'_'.$mbo_study_program_data->study_program_abbreviation.'_'.$s_semester_academic.'_'.$o_class_master_data->class_master_id;
        $s_filename = $s_file_name.'.pdf';

        $mpdf->SetHTMLHeader('<img src="./assets/img/header_of_file.png" alt="">');
        $mpdf->SetHTMLFooter('<img src="./assets/img/footer_of_letter.png" alt="">');
        $mpdf->WriteHTML($html);
        $mpdf->Output($s_file_path.$s_filename, 'F');
        // $mpdf->Output();
        return $s_filename;
    }

    public function test_generate($s_class_master_id, $s_employee_id = false)
    {
        $mbo_class_master_data = $this->Cgm->get_class_master_filtered(['cm.class_master_id' => $s_class_master_id])[0];
        $mba_class_master_study_program = $this->Cgm->get_class_master_study_program($s_class_master_id);
        if ($mbo_class_master_data AND $mba_class_master_study_program) {
            foreach ($mba_class_master_study_program as $o_class) {
                $this->generate_absence_report($mbo_class_master_data, '', $o_class->study_program_id, $s_employee_id);
            }
        }
    }

    public function generate_absence_report($o_class_master_data, $s_file_path, $s_study_program_id, $s_employee_id = false)
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sans_fonts',
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'setAutoTopMargin' => 'pad',
            'setAutoBottomMargin' => 'pad'
        ]);

        $s_semester_academic = $o_class_master_data->running_year.$o_class_master_data->class_semester_type_id;
        $mbo_study_program_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
        if (!$mbo_study_program_data) {
            print($s_study_program_id);exit;
        }

        $a_student_prodi_in = [$s_study_program_id];
        if (is_null($mbo_study_program_data->study_program_main_id)) {
            $mba_sub_study_program = $this->General->get_where('ref_study_program', [
                'study_program_main_id' => $s_study_program_id
            ]);
            if ($mba_sub_study_program) {
                foreach ($mba_sub_study_program as $o_stprodi) {
                    array_push($a_student_prodi_in, $o_stprodi->study_program_id);
                }
            }
        }
        
        $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer(['class_master_id' => $o_class_master_data->class_master_id]);
        $a_lecturer_name = [];
        if ($mba_class_master_lecturer) {
            foreach ($mba_class_master_lecturer as $o_lecturer) {
                $s_lecturer = $this->Pdm->retrieve_title($o_lecturer->personal_data_id);
                if (!in_array($s_lecturer, $a_lecturer_name)) {
                    if (($s_employee_id) AND ($o_lecturer->employee_id == $s_employee_id)) {
                        array_push($a_lecturer_name, $s_lecturer);
                    }else if(!$s_employee_id){
                        array_push($a_lecturer_name, $s_lecturer);
                    }
                }
            }
        }

        if ($s_employee_id) {
            $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($o_class_master_data->class_master_id, [
                'cgsm.employee_id' => $s_employee_id
            ], 'ASC');
        }else{
            $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($o_class_master_data->class_master_id, false, 'ASC');
        }

        $s_deans = $this->Pdm->retrieve_title($mbo_study_program_data->deans_id);
        // $mba_score_list = $this->Cgm->get_class_master_student($o_class_master_data->class_master_id, $a_clausa);
        $mba_score_list = $this->Cgm->get_class_master_student($o_class_master_data->class_master_id, false, [
            'st.study_program_id' => $a_student_prodi_in
        ]);

        $html = '';
        if ($mba_score_list) {
            foreach ($mba_score_list as $o_score) {
                $a_student_absence = [];
                if ($mba_uosd_list) {
                    foreach ($mba_uosd_list as $o_uosd) {
                        $mbo_student_absence_data = $this->Cgm->get_absence_student(['score_id' => $o_score->score_id, 'subject_delivered_id' => $o_uosd->subject_delivered_id]);
                        array_push($a_student_absence, $mbo_student_absence_data);
                    }
                }
                $mbo_student_data = $this->Stm->get_student_by_id($o_score->student_id);
                $d_absence = 100 - (floatval((is_null($o_score->score_absence) ? 0 : $o_score->score_absence)));
                $d_repeat = (is_null($o_score->score_repetition_exam)) ? '-' : $o_score->score_repetition_exam;

                $o_score->student_data = $mbo_student_data;
                $o_score->d_absence = $d_absence;
                $o_score->d_repeat = $d_repeat;
                $o_score->absence_data = $a_student_absence;
            }
        }

        $this->a_page_data['o_study_program'] = $mbo_study_program_data;
        $this->a_page_data['o_class_master_data'] = $o_class_master_data;
        $this->a_page_data['mba_score'] = $mba_score_list;
        $this->a_page_data['mba_uosd'] = $mba_uosd_list;
        $this->a_page_data['key_absence'] = [
            'PRESENT' => 'P',
            'SICK' => 'S',
            'EXCUSE' => 'E',
            'ABSENT' => 'A',
            '' => 'P'
        ];
        $this->a_page_data['a_lecturer_name'] = $a_lecturer_name;
        $this->a_page_data['s_deans'] = $s_deans;
        
        $html .= $this->load->view('lecturer_absence_report', $this->a_page_data, true);
        $html .= $this->load->view('student_absence_report', $this->a_page_data, true);
        // 
        // print($html);
        $s_file_name_subject = str_replace(' ', '-', strtolower($o_class_master_data->subject_name));
        $s_file_name_subject = str_replace('&amp;', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('&', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('/', '-', $s_file_name_subject);
        $s_semester_akademic = $o_class_master_data->running_year.$o_class_master_data->class_semester_type_id;
        $s_file_name = 'Absence_Report_'.$s_file_name_subject.'_'.$mbo_study_program_data->study_program_abbreviation.'_'.$s_semester_academic.'_'.$o_class_master_data->class_master_id;
        $s_filename = $s_file_name.'.pdf';

        $mpdf->SetHTMLHeader('<img src="./assets/img/header_of_file.png" alt="">');
        $mpdf->SetHTMLFooter('<img src="./assets/img/footer_of_letter.png" alt="">');
        $mpdf->WriteHTML($html);
        $mpdf->Output($s_file_path.$s_filename, 'F');
        // $mpdf->Output();
        return $s_filename;
    }

    public function generate_lecturer_absence_report($o_class_master_data, $s_file_path, $s_study_program_id, $s_employee_id = false)
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sans_fonts',
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'setAutoTopMargin' => 'pad',
            'setAutoBottomMargin' => 'pad'
        ]);

        $s_semester_academic = $o_class_master_data->running_year.$o_class_master_data->class_semester_type_id;
        $mbo_study_program_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
        if (!$mbo_study_program_data) {
            print($s_study_program_id);exit;
        }

        $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer(['class_master_id' => $o_class_master_data->class_master_id]);
        $a_lecturer_name = [];
        if ($mba_class_master_lecturer) {
            foreach ($mba_class_master_lecturer as $o_lecturer) {
                $s_lecturer = $this->Pdm->retrieve_title($o_lecturer->personal_data_id);
                if (!in_array($s_lecturer, $a_lecturer_name)) {
                    if (($s_employee_id) AND ($o_lecturer->employee_id == $s_employee_id)) {
                        array_push($a_lecturer_name, $s_lecturer);
                    }else if(!$s_employee_id){
                        array_push($a_lecturer_name, $s_lecturer);
                    }
                }
            }
        }

        if ($s_employee_id) {
            $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($o_class_master_data->class_master_id, [
                'cgsm.employee_id' => $s_employee_id
            ], 'ASC');
        }else{
            $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($o_class_master_data->class_master_id, false, 'ASC');
        }

        $s_deans = $this->Pdm->retrieve_title($mbo_study_program_data->deans_id);

        $this->a_page_data['o_study_program'] = $mbo_study_program_data;
        $this->a_page_data['o_class_master_data'] = $o_class_master_data;
        $this->a_page_data['mba_uosd'] = $mba_uosd_list;
        $this->a_page_data['a_lecturer_name'] = $a_lecturer_name;
        $this->a_page_data['s_deans'] = $s_deans;

        $html = '';
        $html .= $this->load->view('lecturer_absence_report', $this->a_page_data, true);

        $s_file_name_subject = str_replace(' ', '-', strtolower($o_class_master_data->subject_name));
        $s_file_name_subject = str_replace('&amp;', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('&', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('/', '-', $s_file_name_subject);
        $s_semester_akademic = $o_class_master_data->running_year.$o_class_master_data->class_semester_type_id;
        $s_file_name = 'Lecturer_absence_'.$s_file_name_subject.'_'.$mbo_study_program_data->study_program_abbreviation.'_'.$s_semester_academic.'_'.$o_class_master_data->class_master_id;
        $s_filename = $s_file_name.'.pdf';

        $mpdf->SetHTMLHeader('<img src="./assets/img/header_of_file.png" alt="">');
        $mpdf->SetHTMLFooter('<img src="./assets/img/footer_of_letter.png" alt="">');
        $mpdf->WriteHTML($html);
        $mpdf->Output($s_file_path.$s_filename, 'F');
        // $mpdf->Output();
        return $s_filename;
    }

    public function generate_student_absence_report2($o_class_master_data, $s_file_path, $s_study_program_id, $s_employee_id = false)
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sans_fonts',
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'setAutoTopMargin' => 'pad',
            'setAutoBottomMargin' => 'pad'
        ]);
        
        $s_semester_academic = $o_class_master_data->running_year.$o_class_master_data->class_semester_type_id;
        $mbo_study_program_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
        if (!$mbo_study_program_data) {
            print($s_study_program_id);exit;
        }

        $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer(['class_master_id' => $o_class_master_data->class_master_id]);
        $a_lecturer_name = [];
        if ($mba_class_master_lecturer) {
            foreach ($mba_class_master_lecturer as $o_lecturer) {
                $s_lecturer = $this->Pdm->retrieve_title($o_lecturer->personal_data_id);
                if (!in_array($s_lecturer, $a_lecturer_name)) {
                    if (($s_employee_id) AND ($o_lecturer->employee_id == $s_employee_id)) {
                        array_push($a_lecturer_name, $s_lecturer);
                    }else if(!$s_employee_id){
                        array_push($a_lecturer_name, $s_lecturer);
                    }
                }
            }
        }

        $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($o_class_master_data->class_master_id, false, 'ASC');
        $mba_score_list = $this->Cgm->get_class_master_student($o_class_master_data->class_master_id, [
            'st.study_program_id' => $s_study_program_id
        ]);

        $html = '';

        if ($mba_score_list) {
            $html .= '<div style="width: 100%; text-align: center;">
                        <h4>STUDENT ABSENCE</h4>
                    </div>
                    <table border="0"><tr>
                        <td>Subject</td>
                        <td>: '.$o_class_master_data->subject_name.'</td>
                    </tr>
                    <tr>
                        <td>Study Program</td>
                        <td>: '.$mbo_study_program_data->study_program_name_feeder.'</td>
                    </tr>
                    <tr>
                        <td>Semester</td>
                        <td>: '.$s_semester_academic.'</td>
                    </tr></table>';
            foreach ($mba_score_list as $o_score) {
                $mbo_student_data = $this->Stm->get_student_by_id($o_score->student_id);
                $o_score->student_data = $mbo_student_data;
                if ($mba_uosd_list) {
                    foreach ($mba_uosd_list as $o_uosd) {
                        $s_lecturer = $this->Pdm->retrieve_title($o_uosd->personal_data_id);
                        $mbo_student_absence_data = $this->Cgm->get_absence_student(['score_id' => $o_score->score_id, 'subject_delivered_id' => $o_uosd->subject_delivered_id])[0];
                        $o_uosd->absence_data = $mbo_student_absence_data;
                        $o_uosd->lecturer = $s_lecturer;
                    }
                }

                $this->a_page_data['o_study_program'] = $mbo_study_program_data;
                $this->a_page_data['o_class_master_data'] = $o_class_master_data;
                $this->a_page_data['o_score'] = $o_score;
                $this->a_page_data['mba_uosd'] = $mba_uosd_list;
                // $this->a_page_data['a_lecturer_name'] = $a_lecturer_name;

                $html .= $this->load->view('student_absence_report_v2', $this->a_page_data, true);
            }
        }

        $html .= '<div>
            <p></p>
            <div style="float: left; width: 50%;">
                Received By
                <p></p><p></p><p></p>
                <u>Chandra Hendrianto</u>
                <br>
                Head of Academic Service Centre
            </div>
            <div style="">
                Prepared by
                <p></p><p></p><p></p>
                <u>'.implode(' & ', $a_lecturer_name).'</u><br>
                Lecturer
            </div>
        </div>';

        $s_file_name_subject = str_replace(' ', '-', strtolower($o_class_master_data->subject_name));
        $s_file_name_subject = str_replace('&amp;', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('&', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('/', '-', $s_file_name_subject);
        
        $s_file_name = 'Student_absence_'.$s_file_name_subject.'_'.$mbo_study_program_data->study_program_abbreviation.'_'.$s_semester_academic.'_'.$o_class_master_data->class_master_id;
        $s_filename = $s_file_name.'.pdf';

        $mpdf->SetHTMLHeader('<img src="./assets/img/header_of_file.png" alt="">');
        $mpdf->SetHTMLFooter('<img src="./assets/img/footer_of_letter.png" alt="">');
        $mpdf->WriteHTML($html);
        $mpdf->Output($s_file_path.$s_filename, 'F');
        // $mpdf->Output();
        return $s_filename;
    }

    public function itung()
    {
        $i_count_table = 28 / 15;
        $i_count_table = round($i_count_table, 0, PHP_ROUND_HALF_UP);
        print($i_count_table);
    }

    public function generate_student_absence_report($o_class_master_data, $s_file_path, $s_study_program_id, $s_employee_id = false)
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sans_fonts',
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'setAutoTopMargin' => 'pad',
            'setAutoBottomMargin' => 'pad'
        ]);

        $s_semester_academic = $o_class_master_data->running_year.$o_class_master_data->class_semester_type_id;
        $mbo_study_program_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
        if (!$mbo_study_program_data) {
            print($s_study_program_id);exit;
        }

        $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer(['class_master_id' => $o_class_master_data->class_master_id]);
        $a_lecturer_name = [];
        if ($mba_class_master_lecturer) {
            foreach ($mba_class_master_lecturer as $o_lecturer) {
                $s_lecturer = $this->Pdm->retrieve_title($o_lecturer->personal_data_id);
                if (!in_array($s_lecturer, $a_lecturer_name)) {
                    if (($s_employee_id) AND ($o_lecturer->employee_id == $s_employee_id)) {
                        array_push($a_lecturer_name, $s_lecturer);
                    }else if(!$s_employee_id){
                        array_push($a_lecturer_name, $s_lecturer);
                    }
                }
            }
        }

        if ($s_employee_id) {
            $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($o_class_master_data->class_master_id, [
                'cgsm.employee_id' => $s_employee_id
            ], 'ASC');
        }else{
            $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($o_class_master_data->class_master_id, false, 'ASC');
        }

        // $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($o_class_master_data->class_master_id, false, 'ASC');
        $mba_score_list = $this->Cgm->get_class_master_student($o_class_master_data->class_master_id, [
            'st.study_program_id' => $s_study_program_id
        ]);

        $html = '';

        if ($mba_score_list) {
            foreach ($mba_score_list as $o_score) {
                $a_student_absence = [];
                if ($mba_uosd_list) {
                    foreach ($mba_uosd_list as $o_uosd) {
                        $mbo_student_absence_data = $this->Cgm->get_absence_student(['score_id' => $o_score->score_id, 'subject_delivered_id' => $o_uosd->subject_delivered_id]);
                        array_push($a_student_absence, $mbo_student_absence_data);
                    }
                }
                $mbo_student_data = $this->Stm->get_student_by_id($o_score->student_id);
                $d_absence = 100 - (floatval((is_null($o_score->score_absence) ? 0 : $o_score->score_absence)));
                $d_repeat = (is_null($o_score->score_repetition_exam)) ? '-' : $o_score->score_repetition_exam;

                $o_score->student_data = $mbo_student_data;
                $o_score->d_absence = $d_absence;
                $o_score->d_repeat = $d_repeat;
                $o_score->absence_data = $a_student_absence;
            }
        }

        // $o_uosd->score_data = $mba_score_list;

        $this->a_page_data['o_study_program'] = $mbo_study_program_data;
        $this->a_page_data['o_class_master_data'] = $o_class_master_data;
        $this->a_page_data['mba_score'] = $mba_score_list;
        $this->a_page_data['mba_uosd'] = $mba_uosd_list;
        $this->a_page_data['key_absence'] = [
            'PRESENT' => 'P',
            'SICK' => 'S',
            'EXCUSE' => 'E',
            'ABSENT' => 'A',
            '' => 'P'
        ];
        $this->a_page_data['a_lecturer_name'] = $a_lecturer_name;
        
        $html .= $this->load->view('student_absence_report', $this->a_page_data, true);

        $s_deans = $this->Pdm->retrieve_title($mbo_study_program_data->deans_id);

        $this->a_page_data['deans'] = $s_deans;

        $html .= '<div>
            <p></p>
            <div style="float: left; width: 50%;">
                Received By
                <p></p><p></p><p></p>
                <u>Chandra Hendrianto</u>
                <br>
                Head of Academic Service Centre
            </div>
            <div style="">
                Prepared by
                <p></p><p></p><p></p>
                <u>'.implode(' & ', $a_lecturer_name).'</u><br>
                Lecturer
            </div>
        </div>';
        // print($html);exit;

        $s_file_name_subject = str_replace(' ', '-', strtolower($o_class_master_data->subject_name));
        $s_file_name_subject = str_replace('&amp;', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('&', 'and', $s_file_name_subject);
        $s_file_name_subject = str_replace('/', '-', $s_file_name_subject);
        
        $s_file_name = 'Student_absence_'.$s_file_name_subject.'_'.$mbo_study_program_data->study_program_abbreviation.'_'.$s_semester_academic.'_'.$o_class_master_data->class_master_id;
        $s_filename = $s_file_name.'.pdf';

        $mpdf->SetHTMLHeader('<img src="./assets/img/header_of_file_landscape.png" alt="">');
        $mpdf->SetHTMLFooter('<img src="./assets/img/footer_of_letter.png" alt="">');
        $mpdf->WriteHTML($html);
        $mpdf->Output($s_file_path.$s_filename, 'F');
        // $mpdf->Output();
        return $s_filename;
        // return $s_file_path.$s_filename;
    }

    public function generate_template_of_ref_letter()
    {
        if ($this->input->is_ajax_request()) {
            $mpdf = new \Mpdf\Mpdf([
                'default_font_size' => 9,
                'default_font' => 'sans_fonts',
                'mode' => 'utf-8',
                'format' => 'A4-P'
            ]);
            
            $s_student_id = $this->input->post('student_id_letter');
            $s_date = $this->input->post('date_letter');
            $s_number = $this->input->post('number_letter');
            // $s_date = '2020-03-04';
            // $s_student_id = 'a3b301fd-b72e-4ce7-9f10-131b98544358';
            // $s_number = '123';

            if ($s_date != '') {
                $s_date = date('d F Y', strtotime($s_date));
            }

            $this->a_page_data['deans_data'] = false;
            $s_filename = 'Ref_Letter_';
            $mbo_semester_active = $this->Smm->get_active_semester();
            $this->a_page_data['student_data'] = $this->Stm->get_student_filtered(array('student_id' => $s_student_id))[0];
            if ($this->a_page_data['student_data']) {
                $this->a_page_data['deans_data'] = $this->Emm->get_employee_data(array('em.personal_data_id' => $this->a_page_data['student_data']->deans_id))[0];
                $this->a_page_data['hod_data'] = $this->Emm->get_employee_data(array('em.personal_data_id' => $this->a_page_data['student_data']->head_of_study_program_id))[0];
                $this->a_page_data['hod_name'] = $this->Pdm->retrieve_title($this->a_page_data['student_data']->head_of_study_program_id);
                $s_student_name = $this->a_page_data['student_data']->personal_data_name;
                $s_filename = $s_filename.(str_replace(' ', '_', $s_student_name));
            }
            $s_filename .= '.pdf';
            $this->a_page_data['date_input'] = $s_date;
            $this->a_page_data['number_input'] = $s_number;
            
            $html = $this->load->view('template_of_ref_letter', $this->a_page_data, TRUE);
            // $mpdf->setFooter('{PAGENO}');
            $mpdf->WriteHTML($html);
            $s_dir = APPPATH.'uploads/academic/'.$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id.'/ref_letter/';
            if(!file_exists($s_dir)){
                mkdir($s_dir, 0777, TRUE);
            }
            
            $mpdf->Output($s_dir.$s_filename, 'F');
            $a_return = array('code' => 0, 'file_name' => $s_filename);

            print json_encode($a_return);
            
            // $mpdf->Output($s_filename, 'D');
            // $mpdf->Output();
        }
    }

    public function download_academic_file($s_file_name)
    {
        $mbo_semester_active = $this->Smm->get_active_semester();
        $s_dir = APPPATH.'uploads/academic/'.$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id.'/ref_letter/'.$s_file_name;
        // $s_file_path = APPPATH.'/uploads/templates/score_class/'.$s_file;
		if(!file_exists($s_dir)){
			return show_404();
		}
		else{
			$a_path_info = pathinfo($s_dir);
			header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
			readfile( $s_dir );
			exit;
		}
    }
}
