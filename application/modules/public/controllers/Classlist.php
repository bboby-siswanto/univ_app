<?php
class Classlist extends App_core
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->a_page_data['body'] = $this->load->view('arms_layout.php', $this->a_page_data, true);
        $this->load->view('template_layout', $this->a_page_data);
    }

    public function view($s_personal_document_id = false, $s_filename = false)
    {
        $this->load->model('apps/Letter_numbering_model', 'Lnm');

        $s_filename = urldecode($s_filename);
        $s_filepath = APPPATH.'uploads/templates/spmi/';
        if ($s_personal_document_id) {
            $mba_personal_document_data = $this->Lnm->get_personal_document(['personal_document_id' => $s_personal_document_id]);
            if (!$mba_personal_document_data) {
                show_404();exit;
            }
            else if (is_null($mba_personal_document_data[0]->letter_number_id)) {
                show_404();exit;
            }

            $o_personal_document_data = $mba_personal_document_data[0];
            $s_firts_char_user = substr($o_personal_document_data->personal_data_name, 0, 1);
            
            $s_filepath = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_personal_document_data->personal_data_id.'/'.$o_personal_document_data->letter_year.'/request_letter/';
            $s_fullpath = $s_filepath.$s_filename;
            if (is_file($s_fullpath)) {
                header('Content-Disposition: attachment; filename='.$s_filename);
                flush();
                readfile( $s_fullpath );
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

    public function get_attendance_meeting()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('student/Student_model', 'Stm');
            $this->load->model('academic/Class_group_model', 'Cgm');

            $s_subject_delivered_id = $this->input->post('paramid');
            $mbo_absence_student = $this->Cgm->get_absence_student(array('subject_delivered_id' => $s_subject_delivered_id));
            $a_result_data = false;

            if ($mbo_absence_student) {
                $a_result_data = [];
                foreach ($mbo_absence_student as $o_absence) {
                    $mba_student_data = false;
                    $mba_score_data = $this->General->get_where('dt_score', ['score_id' => $o_absence->score_id]);
                    if ($mba_score_data) {
                        $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $mba_score_data[0]->student_id]);
                        $a_absence_data = [
                            'student_number' => $mba_student_data[0]->student_number,
                            'personal_data_name' => $mba_student_data[0]->personal_data_name,
                            'academic_year_id' => $mba_student_data[0]->academic_year_id,
                            'study_program_name' => $mba_student_data[0]->study_program_name,
                            'study_program_abbreviation' => $mba_student_data[0]->study_program_abbreviation,
                            'absence_status' => $o_absence->absence_status,
                            'absence_note' => $o_absence->absence_note,
                        ];
                        array_push($a_result_data, $a_absence_data);
                    }
                }
            }

            print json_encode(array('code' => 0, 'data' => $a_result_data));
        }
    }

    public function class_detail($s_class_master_id = false)
    {
        if ($s_class_master_id) {
            $this->load->model('academic/Class_group_model', 'Cgm');
            $mba_class_data = $this->Cgm->get_class_master_filtered(array('cm.class_master_id' => $s_class_master_id));
            if ($mba_class_data) {
                $mbo_class_data = $mba_class_data[0];
                $mba_class_member_data = $this->Cgm->get_class_master_student($s_class_master_id);
                $mba_list_topics = $this->Cgm->get_unit_subject_delivered($s_class_master_id);
                if ($mba_list_topics) {
                    foreach ($mba_list_topics as $subject_unit) {
                        $subject_unit->date_operation = date('d F Y', strtotime($subject_unit->subject_delivered_time_start));
                        $subject_unit->time_range = date('H:i', strtotime($subject_unit->subject_delivered_time_start)).' - '.date('H:i', strtotime($subject_unit->subject_delivered_time_end));
                    }
                }
                if ($mba_class_member_data) {
                    foreach ($mba_class_member_data as $o_class_member) {
                        $o_class_member->score_sum = round($o_class_member->score_sum, 0, PHP_ROUND_HALF_UP);
                        $o_class_member->score_quiz = round($o_class_member->score_quiz, 0, PHP_ROUND_HALF_UP);
                        $o_class_member->score_final_exam = round($o_class_member->score_final_exam, 0, PHP_ROUND_HALF_UP);
                    }
                }
                
                $this->a_page_data['list_topics'] = $mba_list_topics;
                $this->a_page_data['list_member'] = $mba_class_member_data;
                $this->a_page_data['class_data'] = $mbo_class_data;
                $this->a_page_data['class_master_id'] = $s_class_master_id;
                $this->a_page_data['class_lecturer'] = $this->Cgm->get_class_master_lecturer(['class_master_id' => $s_class_master_id]);
                $this->a_page_data['body'] = $this->load->view('public/classlist/detail_class', $this->a_page_data, true);
                $this->load->view('layout_ext', $this->a_page_data);
            }
            else {
                show_404();
            }
        }
        else {
            show_404();
        }
    }

    public function class($s_employee_id = false, $s_periode = false)
    {
        if ($s_employee_id AND $s_periode) {
            $this->load->model('academic/Class_group_model', 'Cgm');
            $this->load->model('employee/Employee_model', 'Emm');
            $this->load->model('apps/Letter_numbering_model', 'Lnm');
            
            $s_academic_year = substr($s_periode, 0, 4);
            $s_semester_type_id = substr($s_periode, 4, 1);
            
            $mba_list_class = $this->Cgm->get_class_master_filtered([
                'cml.employee_id' => $s_employee_id,
                'cm.academic_year_id' => $s_academic_year,
                'cm.semester_type_id' => $s_semester_type_id
            ]);

            $mba_employee_data = $this->Emm->get_employee_data(['em.employee_id' => $s_employee_id]);
            if ($mba_list_class AND $mba_employee_data) {
                $this->session->set_userdata('eid', $s_employee_id);
                $this->session->set_userdata('pid', $s_periode);
                $o_employee_data = $mba_employee_data[0];

                $mba_is_deans = $this->General->get_where('ref_faculty', ['deans_id' => $o_employee_data->personal_data_id]);
                $a_param_letter = [
                    'ln.academic_year_id' => $s_academic_year,
                    'ln.semester_type_id' => $s_semester_type_id,
                    'lnt.personal_data_id' => $o_employee_data->personal_data_id,
                ];
                $a_param_asl = $a_param_letter;
                $s_template_asl = ($mba_is_deans) ? '19' : '7';
                $this->a_page_data['asl_teaching'] = $this->Lnm->get_document_template($a_param_asl, [$s_template_asl]);
                $this->a_page_data['asl_community'] = $this->Lnm->get_document_template($a_param_asl, ['20']);
                $this->a_page_data['$asl_research'] = $this->Lnm->get_document_template($a_param_asl, ['21']);
                $this->a_page_data['asl_defense_examiner'] = $this->Lnm->get_document_template($a_param_asl, ['6']);
                $this->a_page_data['asl_defense_advisor'] = $this->Lnm->get_document_template($a_param_asl, ['4']);
                $this->a_page_data['asl_ofse_examiner'] = $this->Lnm->get_document_template($a_param_asl, ['5']);
                // print('<pre>');var_dump($this->a_page_data['asl_teaching']);exit;
                
                $o_employee_data->employee_fullname = $this->General->retrieve_title($o_employee_data->personal_data_id);
                foreach ($mba_list_class as $o_class) {
                    $a_class_study_prog = array();
                    $mba_class_study_prog = $this->Cgm->get_class_master_study_program($o_class->class_master_id);
                    $mba_score_list = $this->General->get_where('dt_score', ['class_master_id' => $o_class->class_master_id]);
                    if ($mba_class_study_prog) {
                        foreach ($mba_class_study_prog as $o_classprodi) {
                            if (!in_array($o_classprodi->study_program_abbreviation, $a_class_study_prog)) {
                                array_push($a_class_study_prog, $o_classprodi->study_program_abbreviation);
                            }
                        }
                    }
                    
                    $o_class->study_programlist = implode(' / ', $a_class_study_prog);
                    $o_class->count_student = ($mba_score_list) ? count($mba_score_list) : 0;
                }

                $this->a_page_data['list_class'] = $mba_list_class;
                $this->a_page_data['lecturer_data'] = $o_employee_data;
                $this->a_page_data['academic_year'] = $s_academic_year;
                $this->a_page_data['semester'] = ($s_semester_type_id == 1) ? 'odd' : 'even';
                $this->a_page_data['body'] = $this->load->view('public/classlist/listclass', $this->a_page_data, true);
                $this->load->view('layout_ext', $this->a_page_data);
            }
            else {
                show_404();
            }
        }
        else {
            show_404();
        }
    }
}
