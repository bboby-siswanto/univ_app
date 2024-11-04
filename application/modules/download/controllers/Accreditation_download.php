<?php
error_reporting(0);
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Accreditation_download extends App_core {
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
        $a_subject_sum_skipped = modules::run('accreditation/list_subject_skipped');
        $mba_data = modules::run('accreditation/get_lecturer_list_data', $s_academic_year_id, $s_study_program_id);
        $mba_class_master_list = $this->General->get_in('dt_class_master', 'semester_type_id', ['1', '2'], [
            'academic_year_id' => $s_academic_year_id
        ]);

        $style_border = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                )
            )
        );
        
        $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $s_study_program_id]);
        // if (($mba_data) AND ($mba_class_master_list) AND ($mba_study_program_data)) {
        if (($mba_data) AND ($mba_class_master_list)) {
            $a_prodi_selected = $mba_data[0]->prodi_selected;
            $s_prodi_abbr = ($mba_study_program_data) ? $mba_study_program_data[0]->study_program_abbreviation : 'All';
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            $s_file_name = 'Lecturer_list_('.$s_prodi_abbr.'_'.$s_academic_year_id.')';
            $s_filename = $s_file_name.'.xlsx';

            $s_file_path = APPPATH."uploads/temp/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $i_row_detail = 1;
            $i_row_akd = 1;
            $i_row = 1;

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Portal System")
                ->setCategory("List Lecturer for Accreditation");

            $sheetDetails = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Raw All Data');
            $sheetAkd = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'TRAKD '.$s_prodi_abbr.' '.$s_academic_year_id);
            $o_spreadsheet->addSheet($sheetDetails);
            $o_spreadsheet->addSheet($sheetAkd, 1);
            $o_detailsheet = $o_spreadsheet->getSheetByName('Raw All Data');
            $o_akdsheet = $o_spreadsheet->getSheetByName('TRAKD '.$s_prodi_abbr.' '.$s_academic_year_id);
            $o_sheet = $o_spreadsheet->getActiveSheet();

            $o_detailsheet->setCellValue('A'.$i_row_detail, 'Lecturer');
            $o_detailsheet->setCellValue('B'.$i_row_detail, 'Lecturer Reported');
            $o_detailsheet->setCellValue('C'.$i_row_detail, 'NIDN Lecturer Reported');
            $o_detailsheet->setCellValue('D'.$i_row_detail, 'Credit Allocation');
            $o_detailsheet->setCellValue('E'.$i_row_detail, 'Study Program');
            $o_detailsheet->setCellValue('F'.$i_row_detail, 'Subject');
            $o_detailsheet->setCellValue('G'.$i_row_detail, 'Subject SKS');
            $o_detailsheet->setCellValue('H'.$i_row_detail, 'Class Group Name');
            $o_detailsheet->setCellValue('I'.$i_row_detail, 'Count Student');
            $o_detailsheet->setCellValue('J'.$i_row_detail, 'Count Lecturer Absence');


            $o_akdsheet->setCellValue('A'.$i_row_akd++, 'TRANSAKSI AKADEMIK DOSEN PERIODE TA '.$s_academic_year_id.' - '.(intval($s_academic_year_id) + 1));
            if ($mba_study_program_data) {
                $o_akdsheet->setCellValue('A'.$i_row_akd++, 'PROGRAM STUDI '.$mba_study_program_data[0]->study_program_name_feeder);
            }

            $o_sheet->setCellValue('A'.$i_row++, 'TRANSAKSI AKADEMIK DOSEN PERIODE TA '.$s_academic_year_id.' - '.(intval($s_academic_year_id) + 1));
            if ($mba_study_program_data) {
                $o_sheet->setCellValue('A'.$i_row++, 'PROGRAM STUDI '.$mba_study_program_data[0]->study_program_name_feeder);
            }
            
            $i_row_detail++;
            $i_row_akd++;
            $i_row++;

            $o_sheet->setCellValue('A'.$i_row, 'No');
            $o_sheet->setCellValue('B'.$i_row, 'Nama Dosen');
            $o_sheet->setCellValue('C'.$i_row, 'NIDN');
            $o_sheet->setCellValue('D'.$i_row, 'BKD Pengajaran di Prodi '.$s_prodi_abbr.' (SKS)');
            $o_sheet->setCellValue('G'.$i_row, 'BKD Prodi Lain ( 1 TA)');
            $o_sheet->setCellValue('H'.$i_row, 'Total SKS');
            $i_row++;
            $o_sheet->setCellValue('D'.$i_row, 'Ganjil '.$s_academic_year_id);
            $o_sheet->setCellValue('E'.$i_row, 'Genap '.$s_academic_year_id);
            $o_sheet->setCellValue('F'.$i_row, 'Total SKS');
            $i_row++;

            $o_sheet->mergeCells('A1:G1');
            $o_sheet->mergeCells('A2:G2');
            $o_sheet->mergeCells('A4:A5');
            $o_sheet->mergeCells('B4:B5');
            $o_sheet->mergeCells('C4:C5');
            $o_sheet->mergeCells('D4:F4');
            $o_sheet->mergeCells('G4:G5');
            $o_sheet->mergeCells('H4:H5');



            foreach ($mba_class_master_list as $key => $o_class_master) {
                $mba_class_details = $this->Cgm->get_class_master_filtered(['cm.class_master_id' => $o_class_master->class_master_id]);
                $mba_class_student = $this->Cgm->get_class_master_student($o_class_master->class_master_id);
                $mba_class_prodi = $this->Cgm->get_class_master_study_program($o_class_master->class_master_id);
                $a_prodi = [];

                if ($mba_class_prodi) {
                    foreach ($mba_class_prodi as $o_prodi) {
                        if (!in_array($o_prodi->study_program_abbreviation, $a_prodi)) {
                            array_push($a_prodi, $o_prodi->study_program_abbreviation);
                        }
                    }
                }

                if ($mba_class_details) {
                    $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer([
                        'class_master_id' => $o_class_master->class_master_id
                    ]);

                    foreach ($mba_class_master_lecturer as $o_class_master_lecturer) {
                        $mba_class_absence = $this->General->get_where('dt_class_subject_delivered', [
                            'class_master_id' => $o_class_master->class_master_id,
                            'employee_id' => $o_class_master_lecturer->employee_id
                        ]);

                        $mba_lecturer_reported = false;
                        if (!is_null($o_class_master_lecturer->employee_id_reported)) {
                            $mba_lecturer_reported = $this->Emm->get_employee_data(['em.employee_id' => $o_class_master_lecturer->employee_id_reported]);
                        }

                        $o_detailsheet->setCellValue('A'.$i_row_detail, $o_class_master_lecturer->personal_data_name);
                        $o_detailsheet->setCellValue('B'.$i_row_detail, ($mba_lecturer_reported) ? $mba_lecturer_reported[0]->personal_data_name : '');
                        $o_detailsheet->setCellValue('C'.$i_row_detail, ($mba_lecturer_reported) ? '="'.$mba_lecturer_reported[0]->employee_lecturer_number.'"' : '');
                        $o_detailsheet->setCellValue('D'.$i_row_detail, $o_class_master_lecturer->credit_allocation);
                        $o_detailsheet->setCellValue('E'.$i_row_detail, implode(' / ', $a_prodi));
                        $o_detailsheet->setCellValue('F'.$i_row_detail, $mba_class_details[0]->subject_name);
                        $o_detailsheet->setCellValue('G'.$i_row_detail, $mba_class_details[0]->curriculum_subject_credit.' SKS');
                        $o_detailsheet->setCellValue('H'.$i_row_detail, $o_class_master->class_master_name);
                        $o_detailsheet->setCellValue('I'.$i_row_detail, (($mba_class_student) ? count($mba_class_student) : 0));
                        $o_detailsheet->setCellValue('J'.$i_row_detail, (($mba_class_absence) ? count($mba_class_absence) : 0));
                        $i_row_detail++;
                    }
                }
            }

            $i_numb = 1;
            $i_row_numb = 1;
            foreach ($mba_data as $o_lecturer) {
                $o_akdsheet->setCellValue('A'.$i_row_akd++, $i_numb++.'. Nama Dosen: '.$o_lecturer->lecturer_fullname.' NIDN:'.$o_lecturer->employee_lecturer_number);
                // $o_akdsheet->setCellValue('A'.$i_row_akd, 'No');
                // $o_akdsheet->setCellValue('A'.$i_row_akd, 'Dosen Kelas');
                $o_akdsheet->setCellValue('B'.$i_row_akd, 'Dosen Kelas');
                $o_akdsheet->setCellValue('C'.$i_row_akd, 'Studi Program');
                $o_akdsheet->setCellValue('D'.$i_row_akd, 'Tahun Ajaran');
                $o_akdsheet->setCellValue('E'.$i_row_akd, 'SKS');
                $o_akdsheet->setCellValue('F'.$i_row_akd, 'Mata Kuliah');
                $o_akdsheet->setCellValue('G'.$i_row_akd, 'Jumlah Mahasiswa Kelas');
                $o_akdsheet->setCellValue('H'.$i_row_akd, 'Jumlah Kehadiran Mengajar');

                $o_sheet->setCellValue('A'.$i_row, $i_row_numb++);
                $o_sheet->setCellValue('B'.$i_row, $o_lecturer->personal_data_name);
                $o_sheet->setCellValue('C'.$i_row, $o_lecturer->employee_lecturer_number);

                $i_row_akd_start = $i_row_akd;
                $i_row_akd++;

                $d_odd_sks = 0;
                $d_even_sks = 0;
                $d_sks_other_prodi = 0;
                if ($o_lecturer->class_data) {
                    $detail_class = $o_lecturer->class_data;
                    $d_total_sks = 0;
                    $i_start_akd = $i_row_akd;
                    foreach ($detail_class as $o_class) {
                        $b_allow_calculate = $o_class->this_calculated;
                        // if (!empty($a_subject_sum_skipped)) {
                        //     foreach ($a_subject_sum_skipped as $s_subject) {
                        //         if (strpos(strtolower($o_class->subject_name), $s_subject) !== false) {
                        //             $b_allow_calculate = false;
                        //         }
                        //     }
                        // }

                        // $o_akdsheet->setCellValue('A'.$i_row_akd, $i_numb++);
                        // $o_akdsheet->setCellValue('A'.$i_row_akd, $o_class->class_employee_name);
                        
                        if (($b_allow_calculate) AND ($o_class->class_prodi_data)) {
                            $mba_prodi_list = $o_class->class_prodi_data;
                            
                            // 

                            foreach ($mba_prodi_list as $o_prodi) {
                                if (in_array($o_prodi->study_program_id, $a_prodi_selected)) {
                                    $mba_student_class = $this->Scm->get_student_krs_class([
                                        'sc.class_master_id' => $o_class->class_master_id,
                                        'sc.score_approval' => 'approved'
                                    ], $a_prodi_selected, 'st.study_program_id');
                                }
                                else {
                                    $mba_student_class = $this->Scm->get_score_data([
                                        'sc.class_master_id' => $o_class->class_master_id,
                                        'st.study_program_id' => $o_prodi->study_program_id,
                                        'sc.score_approval' => 'approved'
                                    ]);
                                }

                                if ($mba_student_class) {
                                    if (in_array($o_prodi->study_program_id, $a_prodi_selected)) {
                                        if ($o_class->semester_type_id == 1) {
                                            $d_odd_sks += $o_class->credit_allocation;
                                        }
                                        else if ($o_class->semester_type_id == 2) {
                                            $d_even_sks += $o_class->credit_allocation;
                                        }
                                    }
                                    else {
                                        $d_sks_other_prodi += $o_class->credit_allocation;
                                    }

                                    $o_akdsheet->setCellValue('B'.$i_row_akd, $o_class->class_employee_name);
                                    $o_akdsheet->setCellValue('C'.$i_row_akd, $o_prodi->study_program_abbreviation);
                                    $o_akdsheet->setCellValue('D'.$i_row_akd, $o_class->academic_year_id.' '.$o_class->semester_type_name);
                                    $o_akdsheet->setCellValue('E'.$i_row_akd, $o_class->credit_allocation);
                                    $o_akdsheet->setCellValue('F'.$i_row_akd, $o_class->subject_name);
                                    $o_akdsheet->setCellValue('G'.$i_row_akd, count($mba_student_class));
                                    $o_akdsheet->setCellValue('H'.$i_row_akd, $o_class->class_lecturer_absence);
                                    $d_total_sks += $o_class->credit_allocation;
                                    $i_row_akd++;
                                }
                            }
                            $o_akdsheet->setCellValue('H'.$i_row_akd, $o_class->class_lecturer_absence);
                            // $b_student_prodi = false;
                            // $b_student_otprodi = false;
                            // $mba_prodi_list = $o_class->class_prodi_data;
                            // foreach ($mba_prodi_list as $o_prodi) {
                            //     $mba_student_class = $this->Scm->get_score_data([
                            //         'sc.class_master_id' => $o_class->class_master_id,
                            //         'st.study_program_id' => $o_prodi->study_program_id,
                            //         'sc.score_approval' => 'approved'
                            //     ]);

                            //     if ($mba_student_class) {
                            //         if ($s_study_program_id == $o_prodi->study_program_id) {
                            //             $b_student_prodi = true;
                            //         }
                            //         else {
                            //             $b_student_otprodi = true;
                            //         }
                            //     }
                            // }

                            // if ($b_student_prodi) {
                            //     $s_class_prodi_id = $o_class->class_prodi_id;
                            //     $a_class_prodi_id = explode('/', $s_class_prodi_id);
                                
                            //     if (in_array($s_study_program_id, $a_class_prodi_id)) {
                            //         if ($o_class->semester_type_id == 1) {
                            //             $d_odd_sks += $o_class->credit_allocation;
                            //         }
                            //         else if ($o_class->semester_type_id == 2) {
                            //             $d_even_sks += $o_class->credit_allocation;
                            //         }
                            //     }
                            // }

                            // if ($b_student_otprodi) {
                            //     $d_sks_other_prodi += $o_class->credit_allocation;
                            // }
                        }
                    }

                    // $o_akdsheet->setCellValue('E'.$i_row_akd, $d_total_sks);
                    $last_akd = $i_row_akd - 1;
                    $range_akd = "E$i_start_akd:E$last_akd";
                    $o_akdsheet->setCellValue('E'.$i_row_akd, '=SUM('.$range_akd.')');
                    $i_row_akd++;
                    $o_akdsheet->setCellValue('C'.$i_row_akd, '=SUMIFS('.$range_akd.',C'.$i_start_akd.':C'.$last_akd.',"ELE",D'.$i_start_akd.':D'.$last_akd.',"2022 EVEN")');
                    $o_akdsheet->setCellValue('D'.$i_row_akd, '=SUMIFS('.$range_akd.',C'.$i_start_akd.':C'.$last_akd.',"ELE")');
                    // $o_akdsheet->setCellValue('D'.$i_row_akd, "=SUMIFS($range_akd,C$i_start_akd:C$last_akd,'ELE')");
                    $o_akdsheet->setCellValue('E'.$i_row_akd, '=SUMIFS('.$range_akd.',C'.$i_start_akd.':C'.$last_akd.',"<>ELE")');
                }

                $i_row_akd_limit = $i_row_akd - 1;
                $o_akdsheet->getStyle('A'.$i_row_akd_start.':H'.$i_row_akd_limit)->applyFromArray($style_border);
                $o_akdsheet->getStyle('B'.$i_row_akd_start++.':H'.$i_row_akd_limit)->getAlignment()->setWrapText(true);

                $d_total_inprodi = $d_odd_sks + $d_even_sks;
                // $d_total_inprodi = number_format($d_total_inprodi, 2, '.');
                $o_sheet->setCellValue('D'.$i_row, $d_odd_sks);
                $o_sheet->setCellValue('E'.$i_row, $d_even_sks);
                $o_sheet->setCellValue('F'.$i_row, $d_total_inprodi);
                $o_sheet->setCellValue('G'.$i_row, $d_sks_other_prodi);

                $i_row_akd++;
                $i_row++;
            }

            $i_row_limit = $i_row - 1;
            $o_sheet->getStyle('A4:G'.$i_row_limit)->applyFromArray($style_border);
            $o_sheet->getStyle('B6:B'.$i_row_limit)->getAlignment()->setWrapText(true);

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path.$s_filename );
            exit;
        }
        else {
            show_404();
        }
    }
}