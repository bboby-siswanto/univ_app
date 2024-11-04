<?php
class Download extends App_core
{
    function __construct()
    {
        parent::__construct();
    }

    public function download_ijazah($s_personal_data_id, $s_filename, $b_bulk_zip = false)
    {
        $s_filename = urldecode($s_filename);

        if (!$b_bulk_zip) {
            $s_file_path = APPPATH.'uploads/'.$s_personal_data_id.'/ijazah/'.$s_filename;
        }
        else {
            $s_file_path = APPPATH.'uploads/temp/'.$s_filename;
        }

        if (file_exists($s_file_path)) {
            $a_path_info = pathinfo($s_file_path);
            header('Content-Disposition: attachment; filename='.$s_filename);
            readfile( $s_file_path );

            if ($b_bulk_zip) {
                unlink($s_file_path);
            }
            exit;
        }else{
            show_404();
        }
    }

    public function download_invoice_report($s_academic_semester, $s_filename)
    {
        $s_dir = APPPATH.'uploads/finance/report/semester/'.$s_academic_semester.'/tuition_fee/';

        $a_path_info = pathinfo($s_dir.$s_filename);
        header('Content-Disposition: attachment; filename='.urlencode($s_filename));
        readfile( $s_dir.$s_filename );
        exit;
    }

    public function download_tracer_alumni()
    {
        $s_file_generate = modules::run('download/pdf_download/generate_alumni_tracer');
        if ($s_file_generate['code'] == 0) {
            $s_dir = APPPATH.'uploads/alumni/tracer_study/report/'.date('Y').'/'.date('M').'/';

            $a_path_info = pathinfo($s_dir.$s_file_generate['file_name']);
            header('Content-Disposition: attachment; filename='.urlencode($s_file_generate['file_name']));
            readfile( $s_dir.$s_file_generate['file_name'] );
            exit;
        }else{
            show_404();
        }
    }

    public function download_transcript_flying_faculty($s_academic_year_id, $s_filename)
    {
        $s_file_path = APPPATH.'/uploads/academic/transcript-flying-faculty/'.$s_academic_year_id.'/'.$s_filename;
        if (file_exists($s_file_path)) {
            $a_path_info = pathinfo($s_file_path);
            header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path );
            exit;
        }else{
            show_404();
        }
    }

    public function download_template_report_tracer()
    {
        $s_file_generate = modules::run('alumni/generate_tracer_report_kemdikbud');
        if ($s_file_generate['code'] == 0) {
            $s_dir = APPPATH."uploads/alumni/tracer_study/report/".date('Y')."/".date('M')."/";

            $a_path_info = pathinfo($s_dir.$s_file_generate['file_name']);
            header('Content-Disposition: attachment; filename='.urlencode($s_file_generate['file_name']));
            readfile( $s_dir.$s_file_generate['file_name'] );
            exit;
        }else{
            show_404();
        }
    }
}
