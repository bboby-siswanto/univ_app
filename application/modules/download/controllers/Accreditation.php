<?php
error_reporting(0);
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;	

class Accreditation extends App_core {
    function __construct()
    {
        parent::__construct();

        $this->load->model('academic/Class_group_model','Cgm');
        $this->load->model('alumni/Alumni_model','Alm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('employee/Employee_model', 'Emm');
        $this->load->model('finance/Invoice_model', 'Im');
        $this->load->model('institution/Institution_model', 'Inm');
        $this->load->model('personal_data/Family_model', 'Fm');
    }

    public function download_lecturer_teach($s_academic_year_id = false, $s_study_program_id = false)
    {
        // print('ada');exit;
        // $mba_data = modules::run('accreditation/get_lecturer_list_data', $s_academic_year_id, $s_study_program_id);
        $mba_data = modules::run('accreditation/get_lecturer_list_data/'.$s_academic_year_id.'/'.$s_study_program_id);
        print('<pre>');var_dump($mba_data);exit;
        if ($mba_data) {
            # code...
        }
        else {
            show_404();
        }
    }
}