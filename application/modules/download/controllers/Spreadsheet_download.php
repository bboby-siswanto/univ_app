<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;	

class Spreadsheet_download extends App_core
{
    function __construct()
    {
        parent::__construct();

        // $this->load->model('academic/Class_group_model','Cgm');
        // $this->load->model('alumni/Alumni_model','Alm');
        // $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('academic/Semester_model', 'Smm');
        // $this->load->model('study_program/Study_program_model', 'Spm');
        // $this->load->model('employee/Employee_model', 'Emm');
        // $this->load->model('finance/Invoice_model', 'Im');
        // $this->load->model('institution/Institution_model', 'Inm');
        // $this->load->model('personal_data/Family_model', 'Fm');

        // $this->load->model('portal/Portal_model', 'Mdb');
    }

    public function test_generate_cummulative_gpa_feeder()
    {
        // error_reporting(E_ALL);
        // ini_set('display_errors', 1);
        
        $s_batch = 'all';
        $passed_defense = false;
        $prodi_filter = [
            '01a781d9-81cd-11e9-bdfc-5254005d90f6',
            // 'e0c165f7-a2f8-4372-aa6b-20e3dbc61f32',
            // 'ed375a1a-81cc-11e9-bdfc-5254005d90f6'
        ];
        $mba_student_status = ['active', 'inactive', 'onleave', 'graduated', 'resign'];
        $b_semester_selected = true;
        $b_last_short_semester = false;
        $b_last_repetition = true;
        $s_academic_year_id = 2022;
        $s_semester_type_id = 1;
        $b_feeder_check = true;
        // print($testgabut);exit;
        // print('yak masuk');exit;

        $result = $this->generate_cummulative_gpa_feeder($s_batch, $passed_defense, $prodi_filter, $mba_student_status, $b_semester_selected, $b_last_short_semester, $b_last_repetition, $s_academic_year_id, $s_semester_type_id, $b_feeder_check);
        print('<pre>aaaaaaaa');var_dump($result);exit;
    }

    public function generate_cummulative_gpa_feeder(
        $s_student_batch,
        $b_passed_deffence = false,
        $a_study_program_id,
        $a_student_status = false,
        $b_semester_selected = false, // $b_last_semester = false,
        $b_last_short_semester = false,
        $b_last_repetition = true,
        $s_academic_year_id = false,
        $s_semester_type_id = false,
        $b_feeder_check = false
    )
    {
        $mba_semester_active = $this->Smm->get_active_semester();
        $i_short_semester_blocked = 0;
        
        if ($s_academic_year_id AND $s_semester_type_id) {
            $i_semester_selected = (in_array($s_semester_type_id, [7,8])) ? $s_academic_year_id.'3' : $s_academic_year_id.$s_semester_type_id;
            $a_semester_selected = [
                'academic_year_id' => $s_academic_year_id,
                'semester_type_id' => $s_semester_type_id
            ];
        }
        else{
            $mba_semester_settings = $this->Smm->get_semester_setting();
            $index_semester = count($mba_semester_settings) - 1;
            foreach ($mba_semester_settings as $key => $o_semester) {
                if ($o_semester->semester_status == 'active') {
                    $index_semester = $key + 1;
                    break;
                }
            }
            
            $i_semester_selected = $mba_semester_settings[$index_semester]->academic_year_id.$mba_semester_settings[$index_semester]->semester_type_id;
            $a_semester_selected = [
                'academic_year_id' => $mba_semester_settings[$index_semester]->academic_year_id,
                'semester_type_id' => $mba_semester_settings[$index_semester]->semester_type_id
            ];
            
            if ($b_last_short_semester) {
                $i_semester_selected = ($mba_semester_settings[$index_semester]->semester_type_id == 1) ? $mba_semester_settings[$index_semester]->academic_year_id.'7' : $mba_semester_settings[$index_semester]->academic_year_id.'8';
                $a_semester_selected = [
                    'academic_year_id' => $mba_semester_settings[$index_semester]->academic_year_id,
                    'semester_type_id' => ($mba_semester_settings[$index_semester]->semester_type_id == 1) ? 7 : 8
                ];
            }
        }
        
        $a_filter = array(
            'ds.academic_year_id' => $s_student_batch,
            // 'ds.study_program_id' => $s_study_program_id
        );

        $s_text_header = 'MAHASISWA PRODI ';
        // $s_text_header = 'MAHASISWA ';
        $s_file_name = 'GPA_Recapitulation_';

        // if (($s_study_program_id == 'all') OR ($s_study_program_id == '')) {
        //     // unset($a_filter['ds.study_program_id']);
        //     $s_text_header .= '-';
        //     $s_file_name .= 'All_Prodi';
        // }else {
        //     $mbo_prodi_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
        //     $s_text_header .= strtoupper($mbo_prodi_data->study_program_name);
        //     $s_file_name .= strtoupper($mbo_prodi_data->study_program_abbreviation);
        // }
        $a_text_color = [
            'graduated' => '9F9F9F',
            'onleave' => 'B3C416',
            'active' => '000000',
            'resign' => '9F9F9F',
            'inactive' => 'B3C416'
        ];
        
        $a_prodi_name_ = [];
        $a_prodi_abbr_ = [];
        if (count($a_study_program_id) > 0) {
            $mba_prodi_list_data = $this->General->get_in('ref_study_program', 'study_program_id', $a_study_program_id);
            if ($mba_prodi_list_data) {
                foreach ($mba_prodi_list_data as $o_prodi) {
                    if (!in_array($o_prodi->study_program_name, $a_prodi_name_)) {
                        array_push($a_prodi_name_, $o_prodi->study_program_name);
                    }

                    if (!in_array($o_prodi->study_program_abbreviation, $a_prodi_abbr_)) {
                        array_push($a_prodi_abbr_, $o_prodi->study_program_abbreviation);
                    }
                }
            }
        }
        
        $s_text_header .= strtoupper(implode(' / ', $a_prodi_name_));
        $s_file_name .= strtoupper(implode('_', $a_prodi_abbr_));
        // print('<pre>');var_dump($s_file_name);exit;
        

        $s_text_header .= ' ANGKATAN ';

        if (($s_student_batch == 'all') OR ($s_student_batch == '')) {
            unset($a_filter['ds.academic_year_id']);
            // $s_text_header .= '_-';
        }else{
            $s_text_header .= ' '.$s_student_batch.'/'.(intval($s_student_batch) + 1);
            $s_file_name .= '_'.$s_student_batch.'-'.(intval($s_student_batch) + 1);
        }

        if ($b_semester_selected) {
            $s_text_header .= ' Semester '.$s_academic_year_id.'-'.$s_semester_type_id;
        }
        // $s_template_path = APPPATH.'uploads/templates/template-rekap-ipsipk-v2.xls';
        $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        if ($b_passed_deffence) {
            $a_filter['ds.student_mark_completed_defense'] = 1;
        }
        
        $a_filter = (count($a_filter) > 0) ? $a_filter : false;

        $mba_student_data = $this->Stm->get_student_list_data($a_filter, $a_student_status, array(
            // 'student_status' => 'ASC',
            'ds.academic_year_id' => 'ASC',
            'ds.student_number' => 'ASC',
            // 'faculty_name' => 'ASC',
            // 'study_program_name' => 'ASC',
            'personal_data_name' => 'ASC'
        ), $a_study_program_id);
        
        if ($mba_student_data) {
            $this->load->library('FeederAPI', ['mode' => 'production']);
            $s_filepath = APPPATH.'uploads/academic/'.$mba_semester_active->academic_year_id.$mba_semester_active->semester_type_id.'/cummulative_gpa/';

            if(!file_exists($s_filepath)){
                mkdir($s_filepath, 0777, TRUE);
            }
            // print('<pre>');var_dump($s_filepath);exit;

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_spreadsheet->getDefaultStyle()->getFont()->setSize(9);
            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_text_header)
                ->setCreator("IULI Academic Services")
                ->setLastModifiedBy("IULI Academic Services")
                ->setCategory("Cummulative GPA");
            // $o_sheet = $o_spreadsheet->setActiveSheetIndexByName("Template IPK");
            // $o_sheet->getFont()->setSize(9);
            $o_sheet->setCellValue('A1', 'REKAPITULASI IPS dan IPK '.$i_semester_selected);
            $o_sheet->setCellValue('A2', $s_text_header);
            
            $o_sheet->setCellValue('A3', 'No');
            $o_sheet->setCellValue('B3', 'Nama Mahasiswa');
            $o_sheet->setCellValue('C3', 'NIM');
            $o_sheet->setCellValue('D3', 'Batch');
            $o_sheet->setCellValue('E3', 'Status');
            $o_sheet->setCellValue('F3', 'SKS SEM');
            $o_sheet->setCellValue('H3', 'SKS TOTAL');
            $o_sheet->setCellValue('J3', 'IPS');
            $o_sheet->setCellValue('L3', 'IPK');
            
            $o_sheet->setCellValue('F4', 'P');
            $o_sheet->setCellValue('G4', 'F');
            $o_sheet->setCellValue('H4', 'P');
            $o_sheet->setCellValue('I4', 'F');
            $o_sheet->setCellValue('J4', 'P');
            $o_sheet->setCellValue('K4', 'F');
            $o_sheet->setCellValue('L4', 'P');
            $o_sheet->setCellValue('M4', 'F');

            $o_sheet->mergeCells('A3:A4');
            $o_sheet->mergeCells('B3:B4');
            $o_sheet->mergeCells('C3:C4');
            $o_sheet->mergeCells('D3:D4');
            $o_sheet->mergeCells('E3:E4');
            $o_sheet->mergeCells('F3:G3');
            $o_sheet->mergeCells('H3:I3');
            $o_sheet->mergeCells('J3:K3');
            $o_sheet->mergeCells('L3:M3');
            
            $i_row = 5;
            $i_number_counter = 1;

            $a_gpa_semester_data = array();
            $a_gpa_cummulative_data = array();
            $a_absence_data = array();
            
            foreach ($mba_student_data as $o_student) {
                $s_color = 'C00000';
                
                $s_student_status = strtolower($o_student->student_status);
                if (array_key_exists($s_student_status, $a_text_color)) {
                    $s_color = $a_text_color[$s_student_status];
                    // if ($s_student_status == 'active') {
                    //     print('<pre>');var_dump($s_color);exit;
                    // }
                }
                // else {
                //     print($s_student_status);exit;
                // }
                // print('<pre>'.__LINE__);var_dump($s_filepath);exit;
                $styleColor = array(
                    'font'  => array(
                        'color' => array('rgb' => $s_color)
                    )
                );
                $a_total_sks_feeder = array();
                $a_total_merit_feeder = array();

                $mba_student_semester = $this->Smm->get_semester_student_personal_data(array(
                    // 'st.personal_data_id' => $o_student->personal_data_id
                    'dss.student_id' => $o_student->student_id
                ), array(1,2,3,7,8));

                if ($mba_student_semester) {
                    $a_total_semester_absence = array();
                    $has_repetition = false;

                    $o_sheet->setCellValue('A'.$i_row, $i_number_counter);
                    $o_sheet->setCellValue('B'.$i_row, strtoupper($o_student->personal_data_name));
                    $o_sheet->setCellValue('C'.$i_row, strtoupper($o_student->student_number));
                    $o_sheet->setCellValue('D'.$i_row, strtoupper($o_student->academic_year_id));
                    $o_sheet->setCellValue('E'.$i_row, strtoupper(ucfirst($s_student_status)));
                    
                    // if ($o_student->student_status != 'active') {
                        $o_sheet->getStyle('A'.$i_row.':M'.$i_row)->applyFromArray($styleColor);
                    // }
                    $i_semester_student_start = $mba_student_semester[0]->semester_academic_year_id.$mba_student_semester[0]->semester_semester_type_id;

                    foreach($mba_student_semester AS $key => $o_student_semester) {
                        $i_semester_student = $o_student_semester->semester_academic_year_id.$o_student_semester->semester_semester_type_id;
                        $mbo_student_semester_data = $this->Stm->get_student_by_id($o_student_semester->student_id);
                        $a_score_forlap = $this->feederapi->post('ExportDataAktivitasKuliah', [
                            'filter' => "id_registrasi_mahasiswa='$o_student->student_id' AND id_periode='$i_semester_student'"
                        ]);

                        // $a_score_all_semester_forlap = $this->feederapi->post('GetRiwayatNilaiMahasiswa', [
                        //     'filter' => "id_registrasi_mahasiswa='$o_student->student_id' AND id_periode >= '$i_semester_student_start' AND id_periode<='$i_semester_student'"
                        // ]);
                        
                        $i_sks_forlap = 0;
                        $i_ips_forlap = 0;
                        $i_sks_total_forlap = 0;
                        $i_ipk_forlap = 0;
                        // $b_german_found = false;
                        if ($a_score_forlap->error_code == 0) {
                            foreach ($a_score_forlap->data as $o_score) {
                                $i_sks_forlap += $o_score->sks_semester;
                                $i_ips_forlap += $o_score->ips;
                                $i_sks_total_forlap += $o_score->total_sks;
                                $i_ipk_forlap += $o_score->ipk;
                            }
                        }else{
                            print('<pre>');
                            var_dump($a_score_forlap);exit;
                        }

                        // if ($a_score_all_semester_forlap->error_code == 0) {
                        //     foreach ($a_score_all_semester_forlap->data as $o_score) {
                        //         // print($o_score->nama_mata_kuliah);
                        //         $i_sks_total_forlap += $o_score->sks_mata_kuliah;
                        //         $i_merit_total_forlap += $o_score->sks_mata_kuliah * $o_score->nilai_indeks;
                        //     }   
                        // }else{
                        //     print('<pre>');
                        //     var_dump($a_score_forlap);exit;
                        // }
                        
                        $b_print = true;
                        if ($b_semester_selected) {
                            if (
                                ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND
                                ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])
                            ) {
                                $s_gpa_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'gpa', $b_last_repetition);
                                $s_credit_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'credit', $b_last_repetition);
                                $s_average_absence_ = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'average_absence', $b_last_repetition);
                                $s_average_absence_semester = 100 - $s_average_absence_;
                            }
                            else if ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) {
                                if (($a_semester_selected['semester_type_id'] == 3) AND (in_array($o_student_semester->semester_semester_type_id, [3,7,8]))) {
                                    $s_gpa_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'gpa', $b_last_repetition);
                                    $s_credit_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'credit', $b_last_repetition);
                                    $s_average_absence_ = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'average_absence', $b_last_repetition);
                                    $s_average_absence_semester = 100 - $s_average_absence_;
                                }
                                else {
                                    $b_print = false;
                                }
                            }
                            else{
                                $b_print = false;
                            }
                            
                        }else{
                            $s_gpa_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id);
                            $s_credit_semester = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'credit');
                            $s_average_absence_ = modules::run('academic/score/get_score_cummulative', $o_student->student_id, $o_student_semester->semester_academic_year_id, $o_student_semester->semester_semester_type_id, 'average_absence');
                            $s_average_absence_semester = 100 - $s_average_absence_;
                        }
                        // if ($o_student->student_id == '92d444c1-15e7-49df-8301-110892295476') {
                        //     print('<pre>');var_dump($b_print);
                        //     var_dump($s_credit_semester);
                        // }

                        // $s_gpa_forlap = $this->grades->get_ipk($i_merit_forlap, $i_sks_forlap);

                        if ((!$b_semester_selected) AND ($b_print)) {
                            array_push($a_total_semester_absence, $s_average_absence_);

                            array_push($a_gpa_semester_data, $s_gpa_semester);
                            array_push($a_absence_data, $s_average_absence_);

                            // array_push($a_total_sks_feeder, $i_sks_forlap);
                            // array_push($a_total_merit_feeder, $i_merit_forlap);

                            $o_sheet->setCellValue('F'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                            $o_sheet->setCellValue('J'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));

                            $o_sheet->setCellValue('G'.$i_row, round($i_sks_forlap, 2, PHP_ROUND_HALF_UP));
                            $o_sheet->setCellValue('K'.$i_row, round($i_ips_forlap, 2, PHP_ROUND_HALF_UP));
                        }

                        if ($b_print AND $b_semester_selected) {
                            if ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) {
                                if ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id']) {
                                    array_push($a_total_semester_absence, $s_average_absence_);
    
                                    array_push($a_gpa_semester_data, $s_gpa_semester);
                                    array_push($a_absence_data, $s_average_absence_);

                                    // array_push($a_total_sks_feeder, $i_sks_forlap);
                                    // array_push($a_total_merit_feeder, $i_merit_forlap);

                                    $o_sheet->setCellValue('F'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                                    $o_sheet->setCellValue('J'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));

                                    $o_sheet->setCellValue('G'.$i_row, round($i_sks_forlap, 0, PHP_ROUND_HALF_UP));
                                    $o_sheet->setCellValue('K'.$i_row, round($i_ips_forlap, 2, PHP_ROUND_HALF_UP));
                                }
                                else if (($a_semester_selected['semester_type_id'] == 3) AND (in_array($o_student_semester->semester_semester_type_id, [3,7,8]))) {
                                    array_push($a_total_semester_absence, $s_average_absence_);
    
                                    array_push($a_gpa_semester_data, $s_gpa_semester);
                                    array_push($a_absence_data, $s_average_absence_);

                                    // array_push($a_total_sks_feeder, $i_sks_forlap);
                                    // array_push($a_total_merit_feeder, $i_merit_forlap);

                                    $o_sheet->setCellValue('F'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                                    $o_sheet->setCellValue('J'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));

                                    $o_sheet->setCellValue('G'.$i_row, round($i_sks_forlap, 0, PHP_ROUND_HALF_UP));
                                    $o_sheet->setCellValue('K'.$i_row, round($i_ips_forlap, 2, PHP_ROUND_HALF_UP));
                                }
                            }
                        }
                        if (
                            ($b_print) AND
                            ($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND
                            ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])
                        ) {
                            if ($b_semester_selected) {
                                array_push($a_total_semester_absence, $s_average_absence_);
    
                                array_push($a_gpa_semester_data, $s_gpa_semester);
                                array_push($a_absence_data, $s_average_absence_);

                                // array_push($a_total_sks_feeder, $i_sks_forlap);
                                // array_push($a_total_merit_feeder, $i_merit_forlap);

                                $o_sheet->setCellValue('F'.$i_row, round($s_credit_semester, 0, PHP_ROUND_HALF_UP));
                                $o_sheet->setCellValue('J'.$i_row, round($s_gpa_semester, 2, PHP_ROUND_HALF_UP));

                                $o_sheet->setCellValue('G'.$i_row, round($i_sks_forlap, 0, PHP_ROUND_HALF_UP));
                                $o_sheet->setCellValue('K'.$i_row, round($i_ips_forlap, 2, PHP_ROUND_HALF_UP));
                            }
                        }

                        $a_param_score = array(
                            // 'st.personal_data_id' => $o_student->personal_data_id,
                            'sc.student_id' => $o_student->student_id,
                            'sc.score_approval' => 'approved',
                            // 'sc.score_display' => 'TRUE',
                            'sc.semester_id !=' => '17',
                            'curriculum_subject_credit !=' => '0',
                            'sc.academic_year_id >=' => $mba_student_semester[0]->semester_academic_year_id,
                            'sc.academic_year_id <=' => $o_student_semester->semester_academic_year_id,
                            'curriculum_subject_type !=' => 'extracurricular'
                        );

                        // if (!$b_last_repetition) {
                        //     $a_param_score['score_mark_for_repetition'] = NULL;
                        // }
                        
                        $a_filter_semester = array(
                            'academic_year_start' => $mba_student_semester[0]->semester_academic_year_id,
                            'semester_type_start' => $mba_student_semester[0]->semester_semester_type_id,
                            // 'academic_year_end' => $a_semester_selected['academic_year_id'],
                            // 'semester_type_end' => $a_semester_selected['semester_type_id']
                            'academic_year_end' => $o_student_semester->semester_academic_year_id,
                            'semester_type_end' => $o_student_semester->semester_semester_type_id
                        );

                        // if (in_array($o_student_semester->semester_semester_type_id, [1,2])) {
                        //     $mba_score_data = $this->Scm->get_score_data($a_param_score, [1,2]);
                        // }
                        // else if (in_array($o_student_semester->semester_semester_type_id, [3,7,8])) {
                        //     $mba_score_data = $this->Scm->get_score_data($a_param_score, [3,7,8]);
                        // }

                        $mba_score_data = $this->Scm->get_score_data($a_param_score, [1,2,3,7,8]);
                        $mba_transfer_credit = $this->Scm->get_score_data([
                            'sc.student_id' => $o_student->student_id
                        ], [5]);

                        // $mba_score_data = modules::run('academic/score/clear_semester_score', $mba_score_data, $a_filter_semester);
                        if (($a_filter_semester) AND ($mba_score_data)) {
                            foreach ($mba_score_data as $key => $final) {
                                if (($a_filter_semester['semester_type_start'] !== null) AND ($a_filter_semester['semester_type_start'] == 2)) {
                                    if (($a_filter_semester['academic_year_start'] == $final->academic_year_id) AND ($final->semester_type_id == 1)) {
                                        unset($mba_score_data[$key]);
                                    }
                                }

                                if (($a_filter_semester['semester_type_end'] !== null) AND ($a_filter_semester['semester_type_end'] == 1)) {
                                    // if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id == 2)) {
                                    if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id != 1)) {
                                        unset($mba_score_data[$key]);
                                    }
                                }
                                else if (($a_filter_semester['semester_type_end'] !== null) AND ($a_filter_semester['semester_type_end'] == 2)) {
                                    // if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND ($final->semester_type_id == 2)) {
                                    if (($a_filter_semester['academic_year_end'] == $final->academic_year_id) AND (in_array($final->semester_type_id, ['7', '8']))) {
                                        unset($mba_score_data[$key]);
                                    }
                                }
                            }
                        }

                        if (!$b_feeder_check) {
                            if ($mba_transfer_credit) {
                                $mba_score_data = array_merge($mba_score_data, $mba_transfer_credit);
                            }
                        }

                        $i_sum_credit = 0;
                        $i_sum_merit = 0;
                        $s_sum_absence_student = array_sum($a_total_semester_absence);
                        $s_average_absence_student = (count($a_total_semester_absence) > 0) ? ($s_sum_absence_student / count($a_total_semester_absence)) : 0;
                        $s_average_absence_student = 100 - $s_average_absence_student;

                        if ($mba_score_data) {
                            $a_credit = array();
                            $a_merit = array();

                            foreach ($mba_score_data as $score) {
                                if ($b_feeder_check) {
                                    if(!is_null($score->score_repetition_exam)){
                                        $has_repetition = true;
                                    }

                                    // if ($o_student->student_id == '2ce485fd-b21c-4504-8d8e-d8220a67ba36') {
                                    //     if ($i_semester_student == '20191') {
                                    //         print($score->academic_year_id.$score->semester_type_id.': '.$score->subject_name.' - '.$score->curriculum_subject_credit);
                                    //         print('<br>');
                                    //     }
                                    // }

                                    $score_sum = intval(round($score->score_sum, 0, PHP_ROUND_HALF_UP));
                                    $score_grade_point = $this->grades->get_grade_point($score_sum);
                                    $score_merit = $this->grades->get_merit($score->curriculum_subject_credit, $score_grade_point);
                                    array_push($a_credit, $score->curriculum_subject_credit);
                                    array_push($a_merit, $score_merit);
                                }
                                else if ($this->Scm->get_good_grades($score->subject_name, $score->student_id, $score->score_sum)) {
                                    // if (!in_array($score->subject_name, $a_subject_name_fill)) {
                                    if(!is_null($score->score_repetition_exam)){
                                        $has_repetition = true;
                                    }

                                    $score_sum = intval(round($score->score_sum, 0, PHP_ROUND_HALF_UP));
                                    $score_grade_point = $this->grades->get_grade_point($score_sum);
                                    $score_merit = $this->grades->get_merit($score->curriculum_subject_credit, $score_grade_point);
                                    array_push($a_credit, $score->curriculum_subject_credit);
                                    array_push($a_merit, $score_merit);

                                    //     array_push($a_subject_name_fill, $o_score->subject_name);
                                    // }
                                }
                            }

                            // if ($o_student->student_id == '2ce485fd-b21c-4504-8d8e-d8220a67ba36') {
                            //     print('<pre>');var_dump($a_credit);
                            // }

                            $i_sum_credit = array_sum($a_credit);
                            $i_sum_merit = array_sum($a_merit);
                        }

                        $s_gpa_cummulative = $this->grades->get_ipk($i_sum_merit, $i_sum_credit);
                        // $s_gpa_forlap_cummulative = $this->grades->get_ipk($i_merit_total_forlap, $i_sks_total_forlap);
                        
                        array_push($a_gpa_cummulative_data, $s_gpa_cummulative);
                        
                        $s_predicate = '-';
                        $b_has_repeat_subject = modules::run('academic/score/has_repeat_subject', $o_student->student_id);
                        if(!$b_has_repeat_subject) {
                            $s_predicate = $this->grades->get_graduation_predicate(round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        }
                        // if (!$has_repetition) {
                        //     $s_predicate = $this->grades->get_graduation_predicate(round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        // }
                        
                        $o_sheet->setCellValue('L'.$i_row, round($s_gpa_cummulative, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('H'.$i_row, round($i_sum_credit));
                        $o_sheet->setCellValue('M'.$i_row, round($i_ipk_forlap, 2, PHP_ROUND_HALF_UP));
                        $o_sheet->setCellValue('I'.$i_row, round($i_sks_total_forlap));
                        
                        // $o_sheet->setCellValue('P'.$i_row, $s_predicate);

                        if (!$b_semester_selected) {
                            $i_row++;
                            $o_sheet->insertNewRowBefore($i_row, 1);
                        }
                        else {
                            if (($o_student_semester->semester_academic_year_id == $a_semester_selected['academic_year_id']) AND ($o_student_semester->semester_semester_type_id == $a_semester_selected['semester_type_id'])) {
                                // if ($o_student->student_id == '32d9f6ba-e0e6-4914-875c-0e6a40575293') {
                                //     print('<pre>');var_dump($i_semester_student.'=>'.$i_sum_credit);
                                // }
                                break;
                            }
                            // else {
                            //     if ($o_student->student_id == '32d9f6ba-e0e6-4914-875c-0e6a40575293') {
                            //         print('<pre>');var_dump($i_semester_student.'=>'.$i_sum_credit);
                            //     }
                            // }
                            // if ($o_student->student_id == '32d9f6ba-e0e6-4914-875c-0e6a40575293') {
                            //     print('<br>');
                            // }
                        }

                        // print($i_sum_credit.'<br>');
                    }
                    // if ($o_student->student_id == '92d444c1-15e7-49df-8301-110892295476') {
                    // exit;
                    // }

                    $i_number_counter++;
                    $i_row++;
                    $o_sheet->insertNewRowBefore($i_row, 1);
                }

                // if ($o_student->student_id == '32d9f6ba-e0e6-4914-875c-0e6a40575293') {
                //     print('last:'.$i_sum_credit);exit;
                // }
            }
            // print('<pre>'.__LINE__);var_dump($a_study_program_id);exit;
            $style_border = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '00000000'],
                    )
                )
            );

            $o_sheet->removeRow($i_row, 1);

            $o_sheet->getColumnDimension('A')->setWidth(3);
            $o_sheet->getColumnDimension('B')->setWidth(30);
            $o_sheet->getColumnDimension('C')->setWidth(12);
            $o_sheet->getColumnDimension('D')->setWidth(6.5);
            $o_sheet->getColumnDimension('E')->setWidth(9);

            $o_sheet->getColumnDimension('F')->setWidth(5.3);
            $c_start = 'F';
            for ($i=0; $i < 8; $i++) { 
                $o_sheet->getColumnDimension($c_start++)->setWidth(5.5);
            }
            // $o_sheet->getColumnDimension('G:M')->setWidth(3);

            $o_sheet->getStyle('A3:M'.$i_row)->applyFromArray($style_border);

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_filepath.$s_file_name.'.xlsx');
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            return $s_file_name.'.xlsx';
        }
        else{
            return false;
        }
    }
}