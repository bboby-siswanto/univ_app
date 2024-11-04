<?php
class Thesis extends App_core
{
    function __construct() {
        parent::__construct();
        $this->load->model('Thesis/Thesis_model', 'Tsm');
    }
    
    function student($s_student_number, $abstract_get = false) {
        $this->load->model('student/Student_model', 'Stm');
        $a_thesis_filetype_allowed = ['thesis_final_file', 'thesis_work_file'];
        $mba_student_data = $this->Stm->get_student_filtered(['ds.student_number' => $s_student_number]);
        if ($mba_student_data) {
            $o_student = $mba_student_data[0];
            $mba_file_thesis = $this->Tsm->get_list_thesis_file(['ts.student_id' => $o_student->student_id]);
            if (!$mba_file_thesis) {
                show_404();
            }

            $s_path = STUDENTPATH.$o_student->academic_year_id.'/'.$o_student->study_program_abbreviation.'/'.$o_student->student_id.'/';
            $a_thesis_final_data = [
                'filepath' => $s_path.'thesis_final/'
            ];
            $a_thesis_work_data = [
                'filepath' => $s_path.'thesis_work/'
            ];
            $s_filepath = false;

            foreach ($mba_file_thesis as $o_filethesis) {
                if ($o_filethesis->thesis_filetype == 'thesis_final_file') {
                    $a_thesis_final_data['filename'] = $o_filethesis->thesis_filename;
                }
                else if ($o_filethesis->thesis_filetype == 'thesis_work_file') {
                    $a_thesis_work_data['filename'] = $o_filethesis->thesis_filename;
                }
            }

            if (array_key_exists('filename', $a_thesis_final_data)) {
                $s_filepath = $a_thesis_final_data['filepath'].$a_thesis_final_data['filename'];
            }
            else if (array_key_exists('filename', $a_thesis_work_data)) {
                $s_filepath = $a_thesis_work_data['filepath'].$a_thesis_work_data['filename'];
            }
            
            if ($s_filepath) {
                // $mime = mime_content_type($s_filepath);
                // if ($abstract_get == 'text') {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($s_filepath);

                    $detail  = $pdf->getDetails();
                    $total_page = $detail['Pages'];
                    $abstract_page = 0;
                    for ($i=0; $i < $total_page; $i++) { 
                        $s_content_page = $pdf->getPages()[$i]->getText();
                        $content_lower = strtolower($s_content_page);
                        // print('<br><br><pre>');var_dump($content_lower);
                        $start_pos_abstract = strpos($content_lower, 'abstract');
                        // var_dump($start_pos_abstract);
                        if ($start_pos_abstract !== false) {
                            $abstract_page = $i;break;
                        }
                    }
                    
                    // print('<pre>');var_dump($abstract_page);print('<br>');
                    // $data = $pdf->getPages()[$abstract_page]->getText();
                    // $data = $pdf->getText();
                    // print('<pre>');var_dump($data);
                    // exit;

                    $a_content = ['<hr><center><b>ABSTRACT</b></center>'];
                    $s_body = '';
                    if ($abstract_page > 0) {
                        $abstract = $pdf->getPages()[$abstract_page]->getText();
                        $text_lower = strtolower($abstract);
                        
                        $start_pos_abstract = strpos($text_lower, 'abstract');
                        $find_name = strpos($text_lower, strtolower($o_student->personal_data_name));
                        $start_pos_abstract = ($find_name !== false) ? strpos($text_lower, strtolower($o_student->personal_data_name)) : $start_pos_abstract;
                        $find_end_abstract = strpos($text_lower, 'keyword');
                        $start_end_pos_abstract = ($find_end_abstract !== false) ? $find_end_abstract : (strlen($text_lower) - 20);

                        $len_content = $start_end_pos_abstract - $start_pos_abstract;
                        $text_content = substr($abstract, $start_pos_abstract, $len_content);
                        $text_content = str_replace('=', 'I', $text_content);
                        $text_content = str_replace(':', 'H', $text_content);
                        $text_content = str_replace("", 'fi', $text_content);

                        $body_text = nl2br($text_content);
                        $a_body_text = explode('<br />', $body_text);
                        $a_body_content = [];
                        // print('<pre>');var_dump($body_text);exit;
                        unset($a_body_text[0]);
                        foreach ($a_body_text as $body) {
                            // if (!empty(trim($body))) {
                                array_push($a_body_content, $body);
                            // }
                        }

                        $i_countbr = 0;
                        foreach ($a_body_content as $s_teks) {
                            $s_teks = nl2br($s_teks);
                            $s_teks = trim(str_replace('<br />', '', $s_teks));
                            if (empty($s_teks)) {
                                // print('empteeeh.');
                                $i_countbr++;
                                $s_body = $s_body.'<br>';
                            }
                            else {
                                $i_countbr = 0;
                                $s_body = $s_body.$s_teks;
                            }
                            if (($i_countbr == 2) AND ($find_end_abstract === false)) {
                                break;
                            }
                            // var_dump($s_teks.'-'.$i_countbr.'<br>');
                        }

                        $s_body_kontent = '<style>body{text-align: justify;}</style>'.$s_body;
                        array_push($a_content, $s_body_kontent);

                        // print('<pre>');var_dump($a_body_content);
                        // exit;
                        if ($find_end_abstract !== false) {
                            $text_end_substring = substr($abstract, $start_end_pos_abstract);
                            $text_end_encode = urlencode($text_end_substring);
                            // $keyword_text = strstr($text_end_encode, '%0A%0A', true);
                            $keyword_text = strstr($text_end_encode, '%0A', true);
                            $keyword_content = ($keyword_text) ? urldecode($keyword_text) : trim($text_end_substring);
                            // print('<pre>');var_dump($keyword_content);exit;
                            array_push($a_content, '<b>'.$keyword_content.'<b>');
                        }
                    }

                    // $s_body1 = implode('<br>', $a_content);
                    

                    $abstract_teks = implode('<br>', $a_content);
                    $s_filename = $s_student_number.'.pdf';
                    if (!is_null($o_student->personal_data_path)) {
                        $s_dir = STUDENTPATH.$o_student->personal_data_path.'abstract_thesis/';
                    }
                    else {
                        $s_dir = APPPATH.'uploads/temp/';
                    }
                    
                    if(!file_exists($s_dir)){
                        mkdir($s_dir, 0777, TRUE);
                    }

                    $mpdf = new \Mpdf\Mpdf([
                        'default_font_size' => 11,
                        'format' => 'A4-P',
                        'setAutoTopMargin' => 'stretch',
                        'setAutoBottomMargin' => 'stretch'
                    ]);
                    $mpdf->adjustFontDescLineheight = 1.7;
                    $s_header_file = '<img src="' . base_url() . 'assets/img/header_of_file.png"/>';
                    $s_footer_file = '<img src="' . base_url() . 'assets/img/footer_of_letter.png"/>';
                    $mpdf->SetHTMLHeader($s_header_file);
                    $mpdf->SetHTMLFooter($s_footer_file);
                    $mpdf->WriteHTML($abstract_teks);
                    $mpdf->Output($s_dir.$s_filename, 'F');
                    $a_path_info = pathinfo($s_dir.$s_filename);
                    $s_mime = mime_content_type($s_dir.$s_filename);
                    header("Content-Type: ".$s_mime);
                    readfile( $s_dir.$s_filename );
                    exit;
                    // print('<pre>');var_dump($s_body2);exit;
                    // print('<pre>');
                    var_dump($s_body2_end);exit;
                // }
                // else {
                //     header("Content-Type: ".$mime);
                //     readfile( $s_filepath );exit;
                // }
            }
            else {
                show_404();
            }
            // print('<pre>');var_dump($mba_file_thesis);exit;
        }
        else {
            show_404();
        }
    }

    function final_thesis() {
        $mba_thesis_data = $this->Tsm->get_student_list_thesis(['st.student_status' => 'graduated'], ['final', 'finish']);
        if ($mba_thesis_data) {
            foreach ($mba_thesis_data as $o_thesis) {
                $thesis_filelist = $this->Tsm->get_list_thesis_file([
                    'sls.thesis_student_id' => $o_thesis->thesis_student_id,
                    // 'sls.thesis_log_type' => 'final'
                    'thesis_filetype' => 'thesis_final_file'
                ]);
                $s_filepath = '';
                $s_filename = '';
                if ($thesis_filelist) {
                    $s_filepath = 'student/'.$o_thesis->student_batch.'/'.$o_thesis->study_program_abbreviation.'/'.$o_thesis->student_id.'/thesis_final/'.$thesis_filelist[0]->thesis_filename;
                    $s_filepath = urlencode(base64_encode($s_filepath));
                    $s_filename = $thesis_filelist[0]->thesis_filename;
                }
                // $filelink = base_url().'thesis/view_file/thesis_final/f223302f-eb9b-4408-b7a6-951730e44ebb/Apr_202411201803008_thesis_final.pdf';
                $o_thesis->linkfile = $s_filepath;
                $o_thesis->filename = $s_filename;
            }
        }
        $this->a_page_data['thesis_list'] = $mba_thesis_data;
        $this->a_page_data['body'] = $this->load->view('thesis_public/final_thesis', $this->a_page_data, true);
        $this->load->view('layout_public', $this->a_page_data);
    }
}
