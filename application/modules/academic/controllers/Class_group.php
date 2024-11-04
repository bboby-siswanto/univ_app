<?php
class Class_group extends App_core
{
    function __construct()
    {
        parent::__construct('academic');
        $this->load->model('academic/Class_group_model','Cgm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('employee/Employee_model', 'Emm');
        $this->load->model('thesis/Thesis_model', 'Tsm');
    }

    public function sign_class_hod() {
        if ($this->input->is_ajax_request()) {
            $s_class_master_id = $this->input->post('class_master_id');
            $s_personal_data_id = $this->session->userdata('user');

            $mba_class_group_id = false;
            $a_study_program = [];
            $mba_study_program = $this->General->get_where('ref_study_program', ['head_of_study_program_id' => $s_personal_data_id]);
            if ($mba_study_program) {
                foreach ($mba_study_program as $o_prodi) {
                    if (!in_array($o_prodi->study_program_id, $a_study_program)) {
                        array_push($a_study_program, $o_prodi->study_program_id);
                    }
                }
            }

            $mba_class_group = $this->Cgm->get_class_master_study_program($s_class_master_id);
            if (($mba_class_group) AND ($mba_study_program)) {
                $mba_class_group_id = [];
                // $s_study_program_id = $mba_study_program[0]->study_program_id;
                foreach ($mba_class_group as $o_class_group) {
                    if (in_array($o_class_group->study_program_id, $a_study_program)) {
                        // $s_class_group_id = $o_class_group->class_group_id;
                        array_push($mba_class_group_id, $o_class_group->class_group_id);
                        break;
                    }
                    // if ($o_class_group->study_program_id == $s_study_program_id) {
                        
                    // }
                }
            }

            // print('<pre>');var_dump($s_class_group_id);exit;
            if ($mba_class_group_id) {
                $a_class_group_data = [
                    'sign_personal_data_id' => $s_personal_data_id,
                    'sign_datetime' => date('Y-m-d H:i:s')
                ];
                foreach ($mba_class_group_id as $s_class_group) {
                    $this->Cgm->save_data($a_class_group_data, $s_class_group);
                }
                $a_return = ['code' => 0, 'message' => 'Success!'];
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Your study program not found!'];
            }

            print json_encode($a_return);
        }
    }

    public function list_pagedata($s_class_master_id = false)
    {
        if ($s_class_master_id) {
            $mbo_class_data = $this->Cgm->get_class_master_filtered(array('cm.class_master_id' => $s_class_master_id))[0];
            
            if ($mbo_class_data) {
                $valid_approval = false;

                foreach ($this->a_user_roles as $roles_id) {
                    if (in_array($roles_id, [3,9])) {
                        $valid_approval = true;
                    }
                }

                // $this->a_page_data['is_hod'] = $this->is_hod_classmaster($s_class_master_id);
                $this->a_page_data['valid_approval'] = $valid_approval;
                $this->a_page_data['class_data'] = $mbo_class_data;
                $this->a_page_data['class_master_id'] = $s_class_master_id;
                $this->a_page_data['class_lecturer'] = $this->Cgm->get_class_master_lecturer(['class_master_id' => $s_class_master_id]);
                $this->a_page_data['body'] = $this->load->view('class_group/class_group_member', $this->a_page_data, true);
            }else {
                $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'class_group');
                $this->a_page_data['btn_html'] = $s_btn_html;
                $this->a_page_data['body'] = $this->load->view('class_group/class_group_lists', $this->a_page_data, true);
            }
        }else{
            $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'class_group');
            $this->a_page_data['btn_html'] = $s_btn_html;
            $this->a_page_data['body'] = $this->load->view('class_group/class_group_lists', $this->a_page_data, true);
        }
        $this->load->view('layout', $this->a_page_data);
    }

    public function sign_attendance() {
        if ($this->input->is_ajax_request()) {
            $s_subject_delivered_id = $this->input->post('subject_delivered_id');
            $s_hod_personal_data_id = $this->session->userdata('user');
            
            $a_study_program_id = [];
            $mba_hod_prodi = $this->General->get_where('ref_study_program', ['head_of_study_program_id' => $s_hod_personal_data_id]);
            if ($mba_hod_prodi) {
                foreach ($mba_hod_prodi as $o_prodi) {
                    if (!in_array($o_prodi->study_program_id, $a_study_program_id)) {
                        array_push($a_study_program_id, $o_prodi->study_program_id);
                    }
                }
            }

            if (count($a_study_program_id)) {
                $mba_absence_list = $this->Cgm->get_absence_student_detail(['das.subject_delivered_id' => $s_subject_delivered_id], $a_study_program_id);
                if ($mba_absence_list) {
                    foreach ($mba_absence_list as $o_absence) {
                        $a_update_absence_sign = ['signed_personal_data_id' => $s_hod_personal_data_id];
                        $this->Cgm->save_student_absence($a_update_absence_sign, ['absence_student_id' => $o_absence->absence_student_id]);
                    }

                    $a_return = ['code' => 0, 'message' => 'Success'];
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'No absence found'];
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => 'No study program found'];
            }

            print json_encode($a_return);exit;
        }
    }

    public function is_hod_classmaster($s_class_master_id) {
        $mba_list_student = $this->Scm->get_student_by_score([
            'sc.class_master_id' => $s_class_master_id
        ]);

        $b_is_hod = false;
        if ($mba_list_student) {
            foreach ($mba_list_student as $o_classstudent) {
                if ($o_classstudent->head_of_study_program_id == $this->session->userdata('user')) {
                    $b_is_hod = true;break;
                }
            }
        }
        return $b_is_hod;
    }

    function lecturer_teaching_subject() {
        $s_employee_id = '123b9806-fe93-49e0-9808-2cb2fb922479';
        $s_subject_name = 'Guiding & Traveling';

        $a_data_lecturer_teaching = $this->get_lecturer_teaching([
            'employee_id' => $s_employee_id,
            'subject_name' => $s_subject_name
        ]);
        print('<pre>');var_dump($a_data_lecturer_teaching);exit;
    }

    public function lecturer_teaching($s_employee_id) {
        $employee_data = $this->Emm->get_employee_data(['em.employee_id' => $s_employee_id]);
        $mba_employee_teaching = $this->Cgm->get_class_master_filtered(['cml.employee_id' => $s_employee_id]);
        if ($employee_data) {
            $employee_data[0]->employee_name = $this->General->retrieve_title($employee_data[0]->personal_data_id);
        }

        // if ($mba_employee_teaching) {
        //     foreach ($mba_employee_teaching as $o_class) {
        //         $mba_class_study_program_lists = $this->Cgm->get_class_master_study_program($o_class->class_master_id);
        //         $mba_class_student_lists = $this->Cgm->get_class_master_student($o_class->class_master_id);
        //         $mba_class_subject_unit_delivered = $this->Cgm->get_unit_subject_delivered($o_class->class_master_id, ['cgsm.employee_id' => $s_employee_id,]);

        //         $a_class_prodi = array();
        //         if ($mba_class_study_program_lists) {
        //             foreach ($mba_class_study_program_lists as $class_prodi) {
        //                 array_push($a_class_prodi, $class_prodi->study_program_abbreviation);
        //             }
        //         }
        //         $o_class->class_prodi = implode('|', $a_class_prodi);
        //         $o_class->student_class = ($mba_class_student_lists) ? count($mba_class_student_lists) : 0;
        //         $o_class->class_absence = ($mba_class_subject_unit_delivered) ? count($mba_class_subject_unit_delivered) : 0;
        //     }
        // }
        $a_data_lecturer_teaching = $this->get_lecturer_teaching([
            'employee_id' => $s_employee_id
        ], 'subject_name');
        $mba_employee_teaching = $a_data_lecturer_teaching['data'];
        $this->a_page_data['employee_data'] = $employee_data;
        $this->a_page_data['class_list'] = $mba_employee_teaching;
        // print('<pre>');var_dump($mba_employee_teaching);exit;
        $this->a_page_data['body'] = $this->load->view('academic/class_group/table/historycall_teaching', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function get_lecturer_teaching($a_clause = false, $s_grouping = 'cm.class_master_id') {
        if ($this->input->is_ajax_request()) {
            $a_clause = [
                'employee_id' => $this->input->post('employee_id'),
                'subject_name' => $this->input->post('subject_name')
            ];

            $s_grouping = (empty($this->input->post('group'))) ? $s_grouping : $this->input->post('group');
        }

        $mba_employee_teaching = false;
        if ($a_clause) {
            $a_detail_clause = [];
            if ((isset($a_clause['employee_id'])) AND (!empty($a_clause['employee_id']))) {
                $a_detail_clause['cml.employee_id'] = $a_clause['employee_id'];
            }
            if ((isset($a_clause['subject_name'])) AND (!empty($a_clause['subject_name']))) {
                $a_detail_clause['sn.subject_name'] = $a_clause['subject_name'];
            }
            if (empty($a_detail_clause)) {
                $a_detail_clause = ['cm.class_master_id' => 'x'];
            }

            $a_order = [
                'cm.academic_year_id' => 'ASC',
                'cm.semester_type_id' => 'ASC'
            ];
            $mba_employee_teaching = $this->Cgm->get_class_master_filtered($a_detail_clause, $s_grouping, $a_order);
            if ($mba_employee_teaching) {
                foreach ($mba_employee_teaching as $key => $o_class) {
                    $mba_class_study_program_lists = $this->Cgm->get_class_master_study_program($o_class->class_master_id);
                    $mba_class_student_lists = $this->Cgm->get_class_master_student($o_class->class_master_id);
                    $mba_class_subject_unit_delivered = $this->Cgm->get_unit_subject_delivered($o_class->class_master_id, ['cgsm.employee_id' => $o_class->employee_id]);
                    $d_meet_hour = 0;
                    if ($mba_class_subject_unit_delivered) {
                        foreach ($mba_class_subject_unit_delivered as $o_absence) {
                            // $d_time_start = date('Y-m-d H:i:s', strtotime($o_absence->subject_delivered_time_start));
                            // $d_time_end = date('Y-m-d H:i:s', strtotime($o_absence->subject_delivered_time_end));

                            // $difftime = 
                            $d_time_start = new DateTime($o_absence->subject_delivered_time_start);
                            $d_time_end = new DateTime($o_absence->subject_delivered_time_end);
                            
                            $difftime = $d_time_end->diff($d_time_start)->h;
                            $d_meet_hour += $difftime;
                        }
                    }

                    if (!$mba_class_student_lists) {
                        unset($mba_employee_teaching[$key]);
                    }
                    else {
                        $a_class_prodi = array();
                        if ($mba_class_study_program_lists) {
                            foreach ($mba_class_study_program_lists as $class_prodi) {
                                array_push($a_class_prodi, $class_prodi->study_program_abbreviation);
                            }
                        }
                        $o_class->class_prodi = implode('|', $a_class_prodi);
                        $o_class->student_class = ($mba_class_student_lists) ? count($mba_class_student_lists) : 0;
                        $o_class->class_absence = ($mba_class_subject_unit_delivered) ? count($mba_class_subject_unit_delivered) : 0;
                        $o_class->total_time_absence = $d_meet_hour;
                    }
                }
                
                $mba_employee_teaching = array_values($mba_employee_teaching);
                if (count($mba_employee_teaching) <= 0) {
                    $mba_employee_teaching = false;
                }
            }
        }

        $a_return = ['data' => $mba_employee_teaching];
        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);
        }
        else {
            return $a_return;
        }
    }

    public function list_lecturer()
    {
        $this->a_page_data['body'] = $this->load->view('academic/lecturer/list_lecturer', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function submit_link()
    {
        if ($this->input->is_ajax_request()) {
            $s_class_master_id = $this->input->post('class_master_id');
            $s_link_exam = $this->input->post('link_exam');
            $s_link_available = $this->input->post('class_master_link_exam_available');

            $submit_data = $this->Cgm->save_class_mastering([
                'class_master_link_exam' => $s_link_exam,
                'class_master_link_exam_available' => $s_link_available
            ], [
                'class_master_id' => $s_class_master_id
            ]);

            if ($submit_data) {
                $a_return = ['code' => 0, 'message' => 'Success'];
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Failed processing data'];
            }

            print json_encode($a_return);
        }
    }

    public function get_student_class_filtered()
    {
        print('<pre>');
        // if ($this->input->is_ajax_request()) {
            // $s_academic_year_id = $this->input->post();
            $s_academic_year_id = 2019;
            $s_semester_type_id = 2;
            // $s_semester_type_id = $this->input->post();

            $a_filter_data = array(
                'academic_year_id' => $s_academic_year_id,
                'semester_type_id' => $s_semester_type_id
            );
            $mbq_class_data = $this->Cgm->get_class_group_master_lists($a_filter_data);
            $a_student_class = array();
            if ($mbq_class_data) {
                foreach ($mbq_class_data as $class) {
                    $mba_class_member_data = $this->Cgm->get_class_master_student($class->class_master_id);
                    var_dump($class->class_master_id);
                    if ($mba_class_member_data) {
                        foreach ($mba_class_member_data as $class_member) {
                            array_push($a_student_class, $class_member);
                        }
                    }
                }
            }
            var_dump($a_student_class);
        // }
    }

    public function download_class_lists()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            // $s_academic_year_id = 2019; $s_semester_type_id = 1;

            $mba_class_data = $this->get_class_lists(array(
                'academic_year_id' => $s_academic_year_id,
                'semester_type_id' => $s_semester_type_id
            ));

            if (($mba_class_data) AND ($mba_class_data['data'])) {
                $s_file = 'class_group_lists.csv';
                $academic_year = $s_academic_year_id.$s_semester_type_id;
                $s_path = APPPATH.'/uploads/academic/'.$academic_year.'/'.$s_file;
                $fp = fopen($s_path, 'w+');

                fputcsv($fp, array(
                    'Class Token ID',
                    'Class Name',
                    'Subject',
                    'Study Program',
                    'Lecturer',
                    'Upload All Score'
                ), ';');

                $a_class_data = $mba_class_data['data'];
                foreach ($a_class_data as $o_class) {
                    if (intval($o_class->student_count) > 0) {
                        fputcsv($fp, array(
                            $o_class->class_master_id,
                            $o_class->class_master_name,
                            $o_class->study_subject,
                            $o_class->study_prog,
                            $o_class->lecturer,
                            ($o_class->has_upload_score) ? 'TRUE' : 'FALSE'
                        ), ';');
                    }
                }

                $a_rtn = array('code' => 0, 'file' => $s_file, 'semester' => $academic_year);
            }else{
                $a_rtn = array('code' => 1, 'message' => 'Class not found');
            }

            print json_encode($a_rtn);exit;
        }
    }

    public function download_score_template($s_class_master_id = false)
    {
        if ($s_class_master_id) {
            $this->load->model('personal_data/Personal_data_model', 'Pdm');

            $mba_class_master_data = $this->get_class_master_details($s_class_master_id);
            $get_files = modules::run('download/excel_download/generate_score_template', $mba_class_master_data);
            // print('<pre>');
            // var_dump($get_files);exit;

            if ($get_files) {
                header('Content-Disposition: attachment; filename='.urlencode($get_files['filename']));
                readfile( $get_files['file_path'] .urlencode($get_files['filename']) );
                // readfile( $get_files['file_path'].str_replace('/', '%2F', $get_files['filename']) );
                exit;
            }else{
                show_404();
            }
        }
    }

    // function cek_hod() {
    //     $s_class_master_id = 'b5f8da28-2c4d-4879-aedd-6275dec02464';
    //     $b_is_hod = $this->is_hod_classmaster($s_class_master_id);

    //     $mba_list_student = $this->Scm->get_student_by_score([
    //         'sc.class_master_id' => $s_class_master_id
    //     ]);

    //     $b_is_hod = false;
    //     if ($mba_list_student) {
    //         foreach ($mba_list_student as $o_classstudent) {
    //             if ($o_classstudent->head_of_study_program_id == $this->session->userdata('user')) {
    //                 $b_is_hod = true;break;
    //             }
    //         }
    //     }
    //     return $b_is_hod;

    //     print("<pre>");var_dump($b_is_hod);
    // }

    public function class_group_lists($s_class_master_id = false)
    {
        if ($s_class_master_id) {
            $mbo_class_data = $this->Cgm->get_class_master_filtered(array('cm.class_master_id' => $s_class_master_id))[0];
            if ($mbo_class_data) {
                $b_is_hod = $this->is_hod_classmaster($s_class_master_id);
                $mba_classhas_verified = $this->Cgm->get_sign_class_master([
                    'sign_personal_data_id != ' => NULL,
                    'class_master_id' => $s_class_master_id
                ]);
                if ($mba_classhas_verified) {
                    foreach ($mba_classhas_verified as $o_classverif) {
                        $o_classverif->employee_name = $this->General->retrieve_title($o_classverif->personal_data_id);
                    }
                }
                
                // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                    // print('<pre>');var_dump($mba_classhas_verified);exit;
                // }
                $b_userhas_sign = false;
                if ($b_is_hod) {
                    $mba_userhas_verified = $this->Cgm->get_sign_class_master([
                        'sign_personal_data_id' => $this->session->userdata('user'),
                        'class_master_id' => $s_class_master_id
                    ]);
                    $b_userhas_sign = ($mba_userhas_verified) ? true : false;
                }
                $valid_approval = false;

                foreach ($this->a_user_roles as $roles_id) {
                    if (in_array($roles_id, [3,9])) {
                        $valid_approval = true;
                    }
                }

                $this->a_page_data['valid_approval'] = $valid_approval;
                $this->a_page_data['class_data'] = $mbo_class_data;
                $this->a_page_data['class_master_id'] = $s_class_master_id;
                $this->a_page_data['is_inhod'] = $b_is_hod;
                $this->a_page_data['hod_sign'] = $b_userhas_sign;
                $this->a_page_data['class_verified'] = $mba_classhas_verified;
                $this->a_page_data['class_lecturer'] = $this->Cgm->get_class_master_lecturer(['class_master_id' => $s_class_master_id]);
                $this->a_page_data['body'] = $this->load->view('class_group/class_group_member', $this->a_page_data, true);
            }else {
                $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'class_group');
                $this->a_page_data['btn_html'] = $s_btn_html;
                $this->a_page_data['body'] = $this->load->view('class_group/class_group_lists', $this->a_page_data, true);
            }
        }else{
            $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'class_group');
            $this->a_page_data['btn_html'] = $s_btn_html;
            $this->a_page_data['body'] = $this->load->view('class_group/class_group_lists', $this->a_page_data, true);
        }
        $this->load->view('layout', $this->a_page_data);
    }

    public function form_filter_class_group()
    {
        $this->load->model('academic/Academic_year_model','Aym');
        
        $this->a_page_data['mbo_semester_type'] = $this->Smm->get_semester_type_lists(false, array('semester_type_id !=' => 5));
        $this->a_page_data['mbo_academic_year'] = $this->Aym->get_academic_year_lists();
        $this->load->view('class_group/form/form_filter_class_group', $this->a_page_data);
    }

    public function form_create_class_group()
    {
        if ($this->input->is_ajax_request()) {
            $this->a_page_data['o_semester_list'] = $this->Smm->get_semester_type_lists();
            $s_html = $this->load->view('class_group/form/form_create_class_group', $this->a_page_data, true);
            print json_encode(array('code' => 0, 'data' => $s_html));
        }
    }

    public function class_absence($s_class_master_id, $s_subject_delivered_id = false, $demo = false)
    {
        if ($s_class_master_id) {
            $mbs_quiz = false;
            $s_subject_delivered_id = ($s_subject_delivered_id == '') ? false : $s_subject_delivered_id;
            if ($s_subject_delivered_id) {
                $mbs_quiz = $this->check_quiz($s_subject_delivered_id);
            }

            $this->a_page_data['quiz_number'] = $mbs_quiz;
            $this->a_page_data['subject_delivered_id'] = $s_subject_delivered_id;
            $this->a_page_data['class_master_id'] = $s_class_master_id;
            $this->a_page_data['is_demo'] = $demo;
            $this->a_page_data['body'] = $this->load->view('class_group/class_absence', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function get_absence_student_lists()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('student/Student_model', 'Stm');
            $s_subject_delivered_id = $this->input->post('subject_delivered_id');
            $mbo_absence_student = $this->Cgm->get_absence_student(array('subject_delivered_id' => $s_subject_delivered_id));
            if ($mbo_absence_student) {
                foreach ($mbo_absence_student as $o_absence) {
                    $mba_student_data = false;
                    $mba_score_data = $this->General->get_where('dt_score', ['score_id' => $o_absence->score_id]);
                    if ($mba_score_data) {
                        $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $mba_score_data[0]->student_id]);
                    }
                    $o_absence->student_data = ($mba_student_data) ? $mba_student_data[0] : false;
                }
            }

            print json_encode(array('code' => 0, 'data' => $mbo_absence_student));
        }
    }

    public function view_table_class_absence($s_class_master_id, $s_subject_delivered_id = false)
    {
        if ($s_class_master_id) {
            $mbs_quiz = false;
            if ($s_subject_delivered_id) {
                $mbs_quiz = $this->check_quiz($s_subject_delivered_id);
            }
            $this->a_page_data['quiz_number'] = $mbs_quiz;
            $this->a_page_data['class_master_id'] = $s_class_master_id;
            $a_absence_lists  = $this->General->get_enum_values('dt_absence_student', 'absence_status');
            $s_option = '';
            foreach ($a_absence_lists as $absence) {
	            $selected = ($absence == 'PRESENT') ? 'selected' : '';
                $s_option .= '<option value="'.$absence.'" '.$selected.'>'.strtoupper($absence)."</option>";
            }
            $this->a_page_data['s_absence_option'] = $s_option;
            $this->load->view('class_group/table/table_class_absence', $this->a_page_data);
        }
    }

    public function check_quiz($s_subject_delivered_id)
    {
        $mbs_quiz = false;
        $mbo_absence_quiz = $this->Cgm->get_absence_student(array('subject_delivered_id' => $s_subject_delivered_id, 'absence_description !=' => null));
        
        if ($mbo_absence_quiz) {
            $o_absence_quiz = json_decode($mbo_absence_quiz[0]->absence_description);
            $mbs_quiz = $o_absence_quiz->quiz_number;
        }

        return $mbs_quiz;
    }

    public function form_input_unit_subject($s_class_master_id, $s_subject_delivered_id = false, $is_demo = false)
    {
        if ($s_class_master_id) {
            $mbo_subject_delivered_data = false;
            $s_personal_data_id = $this->session->userdata('user');
            $mba_class_detail = $this->Cgm->get_class_master_subject(['cm.class_master_id' => $s_class_master_id]);

            $s_subject_delivered_id = ($s_subject_delivered_id == 'false') ? false : $s_subject_delivered_id;
            if ($s_subject_delivered_id) {
                $mbo_subject_delivered_data = $this->Cgm->get_unit_subject_delivered($s_class_master_id, array('subject_delivered_id' => $s_subject_delivered_id))[0];
                $this->a_page_data['o_class_lecturer'] = $mbo_subject_delivered_data;
            }else {
                $this->a_page_data['o_class_lecturer'] = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $s_class_master_id, 'personal_data_id' => $s_personal_data_id))[0];
            }
            $a_times = array();
            for ($i=7; $i <= 20; $i++) { 
                array_push($a_times, (strlen($i) == 1) ? '0'.$i.':00' : $i.':00');
            }

            // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
            //     print('<pre>');var_dump($is_demo);exit;
            // }

            $this->a_page_data['class_data'] = ($mba_class_detail) ? $mba_class_detail[0] : false;
            $this->a_page_data['o_subject_delivered_data'] = $mbo_subject_delivered_data;
            $this->a_page_data['a_times'] = $a_times;
            $s_html = $this->load->view('class_group/form/form_input_unit_subject', $this->a_page_data, true);
            if ($is_demo) {
                $s_html = $this->load->view('class_group/form/form_input_unit_subject_demo', $this->a_page_data, true);
            }
            print $s_html;
        }
    }

    public function view_table_class_group($s_class_master_id = false)
    {
        if ($s_class_master_id) {
            $mbo_class_data = $this->Cgm->get_class_master_filtered(array('cm.class_master_id' => $s_class_master_id))[0];
            if ($mbo_class_data) {
                $mbo_class_study_program_lists = $this->Cgm->get_class_master_study_program($s_class_master_id);
                $mbo_class_lecturer_lists = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $s_class_master_id));
                $mbo_class_student_lists = $this->Cgm->get_class_master_student($s_class_master_id);

                $a_class_prodi = array();
                if ($mbo_class_study_program_lists) {
                    foreach ($mbo_class_study_program_lists as $class_prodi) {
                        array_push($a_class_prodi, $class_prodi->study_program_abbreviation);
                    }
                }

                $a_class_lect = array();
                if ($mbo_class_lecturer_lists) {
                    foreach ($mbo_class_lecturer_lists as $lect) {
                        array_push($a_class_lect, $lect->personal_data_name);
                    }
                }
                $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'class_student_member');
                $this->a_page_data['btn_html'] = $s_btn_html;
                $this->a_page_data['class_data'] = $mbo_class_data;
                $this->a_page_data['lect_lists'] = implode(' / ', $a_class_lect);
                $this->a_page_data['prodi_lists'] = implode(' / ', $a_class_prodi);
                $this->a_page_data['class_master_id'] = $s_class_master_id;
                $this->a_page_data['count_student'] = ($mbo_class_student_lists) ? count($mbo_class_student_lists) : 0;
                $this->load->view('class_group/table/class_group_member_table', $this->a_page_data);
            }
        }else {
            $valid_approval = false;
            foreach ($this->a_user_roles as $roles_id) {
                if (in_array($roles_id, [2,4,3,9])) {
                    $valid_approval = true;
                }
            }

            $a_bypass_users_personal_data_id = [
                // '115d375b-8417-4b23-a625-5f9ffdf4610e', // ASTRID WIRIADIDJAJA
                '41261c5c-94c7-4c5e-b4f9-4117f4567b8a', // SAMUEL PD ANANTADJAYA
                // 'e760991b-f034-4f09-aa9f-3566d40dd1f6', // SATIRI
                // '6d2c9f65-0f6a-4fc2-8a26-61a204d5b156', // ADITYA NOVA PUTRA
            ];

            if (in_array($this->session->userdata('user'), $a_bypass_users_personal_data_id)) {
                $valid_approval = true;
            }
            
            $this->a_page_data['valid_approval'] = $valid_approval;
            $this->load->view('class_group/table/class_group_lists_table', $this->a_page_data);
        }
    }

    public function view_class_group_lists_class()
    {
        $s_html = $this->load->view('class_group/table/class_group_lists_lecturer', $this->a_page_data, true);
        print($s_html);
    }

    public function list_approved_absence($s_class_master_id)
    {
        $mbo_employee_login_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $this->session->userdata('user')))[0];

        $config_email = $this->config->item('email');
        $a_user_email_approved = $config_email['academic'];
        $mba_class_lecturer_list = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $s_class_master_id));
        if ($mba_class_lecturer_list) {
            foreach ($mba_class_lecturer_list as $lecturer) {
                array_push($a_user_email_approved, $lecturer->employee_email);
            }
        }
        array_push($a_user_email_approved, 'employee@company.ac.id');

        if ($mbo_employee_login_data) {
            if (in_array($mbo_employee_login_data->employee_email, $a_user_email_approved)) {
                return true;
            }
        }
        return false;
    }

    public function send_score_template()
    {
        if ($this->input->is_ajax_request()) {
            $s_class_master_id = $this->input->post('class_master_id');
            $mba_class_master_data = $this->get_class_master_details($s_class_master_id);
            if ($mba_class_master_data) {
                $get_files = modules::run('download/excel_download/generate_score_template', $mba_class_master_data);
                $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $s_class_master_id));
                $a_lecturer_email = array();
                $a_lecturer_name = array();
                $s_filepath = $get_files['file_path'].$get_files['file_saved'];
                $s_filename = $get_files['file_saved'];
                if ($mba_class_master_lecturer) {
                    foreach ($mba_class_master_lecturer as $lect) {
                        array_push($a_lecturer_email, $lect->employee_email);
                        array_push($a_lecturer_name, $lect->personal_data_name);
                    }
                }

                // $s_lecturer = implode(',', $a_employee_email); $get_files['file_path'].$get_files['filename']

                if (count($a_lecturer_email) > 0) {
                    modules::run('messaging/send_email_score_template', $a_lecturer_name, $a_lecturer_email, $s_filename, $s_filepath);
                    $a_return = array('code' => 0, 'message' => 'Success');
                }else{
                    $a_return = array('code' => 1, 'message' => 'No lecturer selected');
                }
            }else{
                $a_return = array('code' => 1, 'message' => 'Class not found');
            }

            print json_encode($a_return);
        }
    }

    public function view_table_unit_delivered($s_class_master_id)
    {
        if ($s_class_master_id) {
            $mbo_class_data = $this->Cgm->get_class_master_filtered(array('cm.class_master_id' => $s_class_master_id))[0];
            
            $this->a_page_data['is_inhod'] = $this->is_hod_classmaster($s_class_master_id);
            $this->a_page_data['user_approved'] = ($this->list_approved_absence($s_class_master_id)) ? 'yes' : 'no';
            $this->a_page_data['class_data'] = $mbo_class_data;
            $this->a_page_data['class_master_id'] = $s_class_master_id;
            $s_html = $this->load->view('class_group/table/unit_subject_delivered_lists', $this->a_page_data, true);
            print ($s_html);
        }
    }

    public function filter_class_group_member()
    {
        if ($this->input->is_ajax_request()) {
            $s_class_master_id = $this->input->post('class_master_id');
            $s_student_status = $this->input->post('student_status');
            $a_clause = false;
            if ($s_student_status !== null) {
                $a_clause = array(
                    'student_status' => $s_student_status
                );
            }

            $mba_class_member_data = $this->Cgm->get_class_master_student($s_class_master_id, $a_clause);
            if ($mba_class_member_data) {
                foreach ($mba_class_member_data as $o_class_member) {
                    $mbo_student_semester_status = $this->Smm->get_student_semester(array(
                        'dss.student_id' => $o_class_member->student_id,
                        'dss.academic_year_id' => $o_class_member->class_academic_year,
                        'dss.semester_type_id' => $o_class_member->class_semester_type
                    ))[0];
                    
                    $o_class_member->student_semester_status = ($mbo_student_semester_status) ? $mbo_student_semester_status->student_semester_status : $o_class_member->student_status;
                    $o_class_member->score_sum = round($o_class_member->score_sum, 0, PHP_ROUND_HALF_UP);
                    $o_class_member->score_quiz = round($o_class_member->score_quiz, 0, PHP_ROUND_HALF_UP);
                    $o_class_member->score_final_exam = round($o_class_member->score_final_exam, 0, PHP_ROUND_HALF_UP);
                }
            }

            print json_encode(array('code' => 0, 'data' => $mba_class_member_data));
            exit;
        }
    }

    public function filter_class_subject_delivered()
    {
        if ($this->input->is_ajax_request()) {
            $s_class_master_id = $this->input->post('class_master_id');

            $mbo_class_subject_delivere_lists = $this->Cgm->get_unit_subject_delivered($s_class_master_id);
            
            if ($mbo_class_subject_delivere_lists) {
                foreach ($mbo_class_subject_delivere_lists as $subject_unit) {
                    $mba_hod_sign = $this->Cgm->get_absence_student([
                        'das.subject_delivered_id' => $subject_unit->subject_delivered_id,
                        'das.signed_personal_data_id' => $this->session->userdata('user')
                    ]);
                    $subject_unit->hod_has_signed = ($mba_hod_sign) ? true : false;
                    $subject_unit->date_operation = date('d F Y', strtotime($subject_unit->subject_delivered_time_start));
                    $subject_unit->time_range = date('H:i', strtotime($subject_unit->subject_delivered_time_start)).' - '.date('H:i', strtotime($subject_unit->subject_delivered_time_end));
                }
            }
            
            print json_encode(array('code' => 0, 'data' => $mbo_class_subject_delivere_lists));exit;
        }
    }

    public function filter_class_group_data()
    {
        if ($this->input->is_ajax_request()) {
            $a_filter_data = $this->input->post();
            $mba_class_data = $this->get_class_lists($a_filter_data);
            if ($mba_class_data) {
                $a_return = array('code' => 0, 'data' => $mba_class_data['data'], 'group_lecturer' => $mba_class_data['group_lecturer'], 'group_study_program' => $mba_class_data['group_study_program'], 'group_lecturer_email' => $mba_class_data['group_lecturer_email']);
            }else {
                $a_return = array('code' => 0, 'data' => false, 'group_lecturer' => false, 'group_study_program' => false, 'group_lecturer_email' => false);
            }
            print json_encode($a_return);
        }
        exit;
    }

    // public function get_class_master_lect($a_filter_data = false)
    // {
    //     $mba_class_master_data = $this->Cgm->get_class_master_data($a_filter_data);
    //     if ($mba_class_master_data) {
    //         $a_lect_email = false;
    //         foreach ($mba_class_master_data as $class_master) {
    //             $mba_lect_data = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $class_master->class_master_id));
    //             if ($mba_lect_data) {
    //                 $a_lect_data = array();
    //                 foreach ($mba_lect_data as $lect) {
    //                     if (!in_array($lect->employee_email, $a_lect_email)) {
    //                         array_push($a_lect_email, $lect->employee_email);
    //                     }
    //                 }
    //             }
    //         }
    //         return  $a_lect_email;
    //     }else{
    //         return false;
    //     }
    // }

    public function send_all_score_template()
    {
        if ($this->input->is_ajax_request()) {
            $a_class_master_id = $this->input->post('class_master_list');

            $a_class_master_send = array();
            $i_sending = 0;
            if (count($a_class_master_id) > 0) {
                foreach ($a_class_master_id as $class_master_id) {
                    $mba_class_master_data = $this->get_class_master_details($class_master_id);
                    $get_files = modules::run('download/excel_download/generate_score_template', $mba_class_master_data);

                    $s_filepath = $get_files['file_path'].$get_files['filename'];
                    $s_filename = $get_files['filename'];
                    $mba_class_member = $this->Cgm->get_class_master_student($class_master_id);
                    if ($mba_class_member) {
                        $mba_class_lect_data = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $class_master_id));
                        if ($mba_class_lect_data) {
                            $a_lect_email = array();
                            $a_lect_name = array();
                            foreach ($mba_class_lect_data as $lect) {
                                if (!in_array($lect->employee_email, $a_lect_email)) {
                                    array_push($a_lect_email, $lect->employee_email);
                                    array_push($a_lect_name, $lect->personal_data_name);
                                }
                            }
                            if (count($a_lect_email) > 0) {
                                if (!in_array($class_master_id, $a_class_master_send)) {
                                    modules::run('messaging/send_email_score_template', $a_lect_name, $a_lect_email, $s_filename, $s_filepath);
                                    $i_sending++;
                                }
                            }
                        }
                    }
                }

                if ($i_sending > 0) {
                    $a_return = array('code' => 0, 'message' => 'Success!');
                }else{
                    $a_return = array('code' => 1, 'message' => 'Cannt send email at this time, please contact IT Team!');
                }
            }else{
                $a_return = array('code' => 1, 'message' => 'No data send!');
            }
            
			print json_encode($a_return);
        }
    }

    public function get_class_lists($a_filter_data = false)
    {
        if ($a_filter_data) {
            $mbo_return  = array();
            $mbo_group_lect_return = array();
            $mbo_prodi_return = array();
            $mbo_group_lect_email_return = array();

            $b_search = false;
            $b_lectshow_all = false;
            $b_is_lecturer = false;

            foreach($a_filter_data as $key => $value){
                if (($key == 'show_all') AND ($value == '1')) {
                    unset($a_filter_data['show_all']);
                    $b_lectshow_all = true;
                }
                else if ($key == 'is_lecturer') {
                    unset($a_filter_data['is_lecturer']);
                    $b_is_lecturer = true;
                }
                else if ($value != '') {
                    $b_search = true;
                }
                else if(($a_filter_data[$key] == 'All') OR ($a_filter_data[$key] == '')){
                    unset($a_filter_data[$key]);
                }
            }
            
            if ($b_search) {
                $mbo_result_mastering = $this->get_class_master_lists($a_filter_data);
                $mbo_result_unmastering = $this->get_class_group_lists($a_filter_data);
                foreach ($mbo_result_mastering['class_data'] as $mastering) {
                    array_push($mbo_result_unmastering['class_data'], $mastering);
                }

                foreach ($mbo_result_mastering['lect_data'] as $mastering) {
                    if (!in_array($mastering, $mbo_result_unmastering['lect_data'])) {
                        array_push($mbo_result_unmastering['lect_data'], $mastering);
                    }
                }

                foreach ($mbo_result_mastering['lect_email_data'] as $mastering) {
                    if (!in_array($mastering, $mbo_result_unmastering['lect_email_data'])) {
                        array_push($mbo_result_unmastering['lect_email_data'], $mastering);
                    }
                }
                
                foreach ($mbo_result_mastering['prodi_data'] as $mastering) {
                    if (!in_array($mastering, $mbo_result_unmastering['prodi_data'])) {
                        array_push($mbo_result_unmastering['prodi_data'], $mastering);
                    }
                }
                
                $mbo_return = $mbo_result_unmastering['class_data'];
                $mbo_group_lect_return = $mbo_result_unmastering['lect_data'];
                $mbo_group_lect_email_return = $mbo_result_unmastering['lect_email_data'];
                $mbo_prodi_return = $mbo_result_unmastering['prodi_data'];
            }else{
                $mbo_return = false;
            }
            
            if (($b_is_lecturer) AND (!$b_lectshow_all)) {
                $s_lect_personal_data_id = $this->session->userdata('user');
                if ($mbo_return) {
                    foreach ($mbo_return as $key => $class_data) {
                        $b_has_upload_score = false;
                        if ((isset($class_data->class_master_id)) AND (!is_null($class_data->class_master_id))) {
                            $mba_score_data = $this->General->get_where('dt_score', ['class_master_id' => $class_data->class_master_id]);
                            if ($mba_score_data) {
                                foreach ($mba_score_data as $o_score) {
                                    if ($o_score->score_final_exam !== NULL) {
                                        $b_has_upload_score = true;
                                    }
                                }
                            }
                            $class_data->has_upload_score = $b_has_upload_score;
                            if (!in_array($s_lect_personal_data_id, $class_data->lecturer_data)) {
                                unset($mbo_return[$key]);
                            }
                        }
                    }
                    $mbo_return = array_values($mbo_return);
                }

                return array('code' => 0, 'data' => $mbo_return, 'group_lecturer' => false, 'group_study_program' => false, 'group_lecturer_email' => false);
            }else{
                if ($mbo_return) {
                    foreach ($mbo_return as $key => $class_data) {
                        $b_has_upload_score = false;
                        if ((isset($class_data->class_master_id)) AND (!is_null($class_data->class_master_id))) {
                            $mba_score_data = $this->General->get_where('dt_score', ['class_master_id' => $class_data->class_master_id]);
                            if ($mba_score_data) {
                                foreach ($mba_score_data as $o_score) {
                                    if ($o_score->score_final_exam !== NULL) {
                                        $b_has_upload_score = true;
                                    }
                                }
                            }
                        }
                        $class_data->has_upload_score = $b_has_upload_score;
                    }
                    $mbo_return = array_values($mbo_return);
                }

                return array('code' => 0, 'data' => $mbo_return, 'group_lecturer' => $mbo_group_lect_return, 'group_study_program' => $mbo_prodi_return, 'group_lecturer_email' => $mbo_group_lect_email_return);
            }
        }else{
            return false;
        }
    }

    public function filter_class_destination()
    {
        if ($this->input->is_ajax_request()) {

            $a_clause = array(
                'cm.academic_year_id' => $this->input->post('academic_year_id'),
                'cm.semester_type_id' => $this->input->post('semester_type_id'),
                'sn.subject_name' => $this->input->post('subject_name')
            );

            $mba_class_master_lists = $this->Cgm->get_class_master_filtered($a_clause);
            // print('<pre>');
            // var_dump($mba_class_master_lists);exit;
            if ($mba_class_master_lists) {
                foreach ($mba_class_master_lists as $key => $class) {
                    if (!is_null($class->class_master_id)) {
                        $a_class_study_prog = array();
                        $a_class_lecturer = array();
                        $mbo_class_lecturer = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $class->class_master_id));
                        $mbo_class_study_prog = $this->Cgm->get_class_master_study_program($class->class_master_id);
                        
                        if ($mbo_class_lecturer) {
                            foreach ($mbo_class_lecturer as $lecturer) {
                                if (!in_array($lecturer->personal_data_name, $a_class_lecturer)) {
                                    array_push($a_class_lecturer, $lecturer->personal_data_name);
                                }
                            }
                        }

                        if ($mbo_class_study_prog) {
                            foreach ($mbo_class_study_prog as $prod) {
                                if (!in_array($prod->study_program_abbreviation, $a_class_study_prog)) {
                                    array_push($a_class_study_prog, $prod->study_program_abbreviation);
                                }
                            }
                        }

                        $mbo_class_student_data = $this->Cgm->get_class_master_student($class->class_master_id);

                        $class->study_prog = (count($a_class_study_prog) > 0) ? implode(' / ', $a_class_study_prog) : 'N/A';
                        $class->student_count = ($mbo_class_student_data) ? count($mbo_class_student_data) : 0;
                        $class->lecturer = (count($a_class_lecturer) > 0) ? implode(' & ', $a_class_lecturer) : 'N/A';
                    }else{
                        unset($mba_class_master_lists[$key]);
                    }
                }
                $mba_class_master_lists = array_values($mba_class_master_lists);
            }
            print(json_encode(array('code' => 0, 'data' => $mba_class_master_lists)));
        }
    }

    public function checked_mastering_data()
    {
        if ($this->input->is_ajax_request()) {
            $a_class_id = $this->input->post('data');
            $a_sks = array();
            $a_nama_mk = array();
            $a_prodi_abbreviation = array();
            $a_program_id = array();
            $i_sks = 0;
            foreach ($a_class_id as $class_id) {
                $mbo_class_data = $this->Cgm->get_class_group_subject($class_id)[0];
                $i_sks = $mbo_class_data->curriculum_subject_credit;
                array_push($a_program_id, $mbo_class_data->id_program);
                if (!in_array($mbo_class_data->study_program_abbreviation, $a_prodi_abbreviation)) {
                    array_push($a_prodi_abbreviation, $mbo_class_data->study_program_abbreviation);
                }

                if (!in_array($mbo_class_data->curriculum_subject_credit, $a_sks)) {
                    array_push($a_sks, $mbo_class_data->curriculum_subject_credit);
                }

                if (!in_array($mbo_class_data->subject_name, $a_nama_mk)) {
                    array_push($a_nama_mk, $mbo_class_data->subject_name);
                }
            }

            if (count($a_sks) > 1) {
                $a_rtn = array('code' => 1, 'message' => 'SKS is not same!');
            }else if (count($a_nama_mk) > 1) {
                $a_rtn = array('code' => 1, 'message' => 'Subject name is not same!');
            }else{
                $this->load->model('study_program/Study_program_model', 'Spm');
                $mbo_program_data = $this->Spm->get_program_lists_select(array('program_code' => 'NI'))[0];
                if ($mbo_program_data) {
                    $s_program_id_indonesia = $mbo_program_data->program_id;
                    $program_count = array_count_values($a_program_id);

                    if (in_array($s_program_id_indonesia, $a_program_id)) {
                        $arr_ind = $program_count[$s_program_id_indonesia];
                        if (count($a_program_id) != $arr_ind) {
                            print json_encode(array('code' => 1, 'message' => 'Program '.$mbo_program_data->program_name.' cannot be combined with other programs'));exit;
                        }
                    }

                    $a_data = array(
                        'total_sks' => $i_sks,
                        'subject_name' => '('.$a_nama_mk[0].' - '.$i_sks.' SKS) - ('.implode(' / ',$a_prodi_abbreviation).')',
                        'lecturer' => $this->Cgm->get_class_lecturer_grouping($a_class_id, true)
                    );

                    $a_rtn = array('code' => 0, 'data' => $a_data);
                }else{
                    $a_rtn = array('code' => 1, 'message' => 'Error get program data');
                }
            }
            print json_encode($a_rtn);exit;
        }
    }

    // public function mastering_all_class_groups($s_academic_year_id = false, $s_semester_type_id = false)
    // {
    //     if ($this->input->is_ajax_request()) {
    //         $s_academic_year_id = $this->input->post('academic_year_id');
    //         $s_semester_type_id = $this->input->post('semester_type_id');
    //     }

    //     if (($s_academic_year_id) AND ($s_semester_type_id)) {
    //         $mba_class_groups_lists = $this->Cgm->get_class_groups();
    //         if ($mba_class_groups_lists) {
    //             $this->db->trans_start();
    //             foreach ($mba_class_group_lists as $class) {
    //                 $mba_class_in_master = $this->Cgm->get_class_master_group(array('class_group_id' => $class->class_group_id));
    //                 if (!$mba_class_in_master) {
    //                     $s_class_master_id = $this->uuid->v4();
    //                     $a_class_master_data = array(
    //                         'class_master_id' => $s_class_master_id,
    //                         'academic_year_id' => $s_academic_year_id,
    //                         'semester_type_id' => $s_semester_type_id,
    //                         'class_master_name' => $class->class_group_name,
    //                         'date_added' => date('Y-m-d H:i:s')
    //                     );

    //                     $save_class_master = $this->Cgm->save_class_mastering($a_class_master_data);
    //                     if ($save_class_master) {
    //                         $a_class_master_class_data = array(
    //                             'class_master_id' => $s_class_master_id,
    //                             'class_group_id' => $class->class_group_id,
    //                             'date_added' => date('Y-m-d H:i:s')
    //                         );

    //                         $save_class_master_class = $this->Cgm->save_class_master_class($a_class_master_class_data);
    //                         if ($save_class_master_class) {
    //                             $mba_class_group_lecturer = $this->Cgm->get_class_group_lecturer(array('class_group_id' => $class->class_group_id));
    //                             if ($mba_class_group_lecturer) {
    //                                 foreach ($mba_class_group_lecturer as $lecturer) {
    //                                     $a_class_master_lecturer_data = array(
    //                                         'class_master_lecturer_id' => $this->uuid->v4(),
    //                                         'class_master_id' => $s_class_master_id,
    //                                         'employee_id' => $lecturer->employee_id,
    //                                         'employee_id_reported' => $lecturer->employee_id_reported,
    //                                         'credit_allocation' => $lecturer->credit_allocation,
    //                                         'credit_charged' => $lecturer->credit_charged,
    //                                         'credit_realization' => $lecturer->credit_realization,
    //                                         'class_master_lecturer_status' => $lecturer->class_group_lecturer_status,
    //                                         'class_master_lecturer_preferable_day' => $lecturer->class_group_lecturer_preferable_day,
    //                                         'class_master_lecturer_preferable_time' => $lecturer->class_group_lecturer_preferable_day,
    //                                         'class_master_lecturer_priority' => $lecturer->class_group_lecturer_priority,
    //                                         'is_reported_to_feeder' => $lecturer->is_reported_to_feeder,
    //                                         'date_added' => $lecturer->is_reported_to_feeder
    //                                     );

    //                                     $save_lecturer = $this->Cgm->save_class_master_lect_data($a_class_master_lecturer_data);
    //                                     if (!$save_lecturer) {
    //                                         $a_return = array('code' => 1, 'message' => 'Error saving lecturer');
    //                                         print json_encode($a_return);
    //                                         exit;
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }

    //             if ($this->db->trans_status() === FALSE) {
    //                 $this->db->trans_rollback();
    //                 $a_return = array('code' => 1, 'message' => 'Error mastering class groups');
    //             }else{
    //                 $a_return = array('code' => 0, 'message' => 'Success');
    //             }

    //             print json_encode($a_return);
    //         }
    //     }
    // }

    public function save_class_mastering()
    {
        if ($this->input->is_ajax_request()) {
            $a_class_id = $this->input->post('data');
            $a_class_lect_data = $this->input->post('class_lect_data');
            
            $this->db->trans_start();
            $s_class_master_id = $this->uuid->v4();
            $this_update = false;
            $a_class_master_for_remove = array();
            
            if (count($a_class_id) > 0) {
                foreach ($a_class_id as $class_id) {
                    $mbo_class_in_master = $this->Cgm->get_class_id_master_class(array('class_group_id' => $class_id));
                    if ($mbo_class_in_master) {
	                    $s_class_master_id = $mbo_class_in_master[0]->class_master_id;
	                    
                        foreach ($mbo_class_in_master as $class_group_master) {
                            $class_group_data_master = $this->Cgm->get_class_id_master_class(array('class_master_id' => $class_group_master->class_master_id));
                            
                            $mba_class_subject_delivered = $this->Cgm->get_class_subject_delivered([
	                            'class_master_id' => $class_group_master->class_master_id
                            ]);
                            
                            if($mba_class_subject_delivered){
	                            $s_class_master_id = $class_group_master->class_master_id;
                            }
                            
                            if ($class_group_data_master) {
                                foreach ($class_group_data_master as $cg) {
                                    if (!in_array($cg->class_group_id, $a_class_id)) {
                                        array_push($a_class_id, $cg->class_group_id);
                                    }
                                }
                            }

                            if (!in_array($class_group_master->class_master_id, $a_class_master_for_remove)) {
                                array_push($a_class_master_for_remove, $class_group_master->class_master_id);
                            }
                        }
                        // $s_class_master_id = $mbo_class_in_master[0]->class_master_id;
                        $this_update = true;
                    }
                }

                if (!$this_update) {
                    $mbo_class_data = $this->Cgm->get_class_group_filtered(array('dcg.class_group_id' => $a_class_id[0]))[0];
                    
                    $a_class_master_data = array(
                        'class_master_id' => $s_class_master_id,
                        'academic_year_id' => $mbo_class_data->running_year,
                        'semester_type_id' => $mbo_class_data->class_semester_type_id,
                        'class_master_name' => $mbo_class_data->subject_name
                    );

                    $this->Cgm->save_class_mastering($a_class_master_data);
                }

                $a_class_for_grouping = array();
                
                foreach ($a_class_id as $class_id) {
                    $a_class_master_class_data = array(
                        'class_master_id' => $s_class_master_id,
                        'class_group_id' => $class_id
                    );

                    $this->Scm->save_data(array('class_master_id' => $s_class_master_id), array('class_group_id' => $class_id));

                    $mbo_class_in_master = $this->Cgm->get_class_id_master_class(array('class_group_id' => $class_id))[0];
                    if ($mbo_class_in_master) {
                        if (!in_array($mbo_class_in_master->class_master_id, $a_class_for_grouping)) {
                            array_push($a_class_for_grouping, $mbo_class_in_master->class_master_id);
                        }
                        $this->Cgm->save_class_master_class($a_class_master_class_data, array('class_group_id' => $class_id, 'class_master_id' => $mbo_class_in_master->class_master_id));
                    }else {
                        // array_push($a_class_for_grouping, $class_id);
                        $a_class_master_class_data['date_added'] = date('Y-m-d H:i:s');
                        $this->Cgm->save_class_master_class($a_class_master_class_data);
                    }
                }

                if (count($a_class_for_grouping) > 0) {
                    foreach ($a_class_for_grouping as $classes_id) {
                        $mbo_class_subject_unit_delivered = $this->Cgm->get_unit_subject_delivered($classes_id);

                        if ($mbo_class_subject_unit_delivered) {
                            foreach ($mbo_class_subject_unit_delivered as $subject_unit) {
                                $a_class_subject_delivered_data = array(
                                    'class_master_id' => $s_class_master_id,
                                    'employee_id' => $subject_unit->employee_id,
                                    'subject_delivered_time_start' => $subject_unit->subject_delivered_time_start,
                                    'subject_delivered_time_end' => $subject_unit->subject_delivered_time_end,
                                    'subject_delivered_description' => $subject_unit->subject_delivered_description,
                                    'number_of_meeting' => $subject_unit->number_of_meeting
                                );

                                $a_param_check = array(
                                    'cgsm.employee_id' => $subject_unit->employee_id,
                                    'subject_delivered_time_start' => $subject_unit->subject_delivered_time_start
                                );

                                $mbo_class_subject_unit_delivered = $this->Cgm->get_unit_subject_delivered($s_class_master_id, $a_param_check);
                                if ($mbo_class_subject_unit_delivered) {
                                    # update
                                    $this->Cgm->save_subject_delivered($a_class_subject_delivered_data, $mbo_class_subject_unit_delivered[0]->subject_delivered_id);
                                }else{
                                    # insert
                                    $a_class_subject_delivered_data['subject_delivered_id'] = $this->uuid->v4();
                                    $a_class_subject_delivered_data['date_added'] = date('Y-m-d H:i:s');
                                    $this->Cgm->save_subject_delivered($a_class_subject_delivered_data);
                                }
                            }
                        }
                    }
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => 'Error parsing class group');
            }

            if (count($a_class_lect_data) > 0) {
                if ($this_update) {
                    // $this->Cgm->remove_class_master_lect_sync($s_class_master_id);
                }

                foreach ($a_class_lect_data as $lect_data) {
                    $mbo_class_lect_data = $this->Cgm->get_class_group_lecturer(array('class_group_lecturer_id' => $lect_data['class_lect_id']))[0];
                    if ($mbo_class_lect_data) {
                        $a_class_master_lecturer_data = array(
                            'class_master_lecturer_id' => $this->uuid->v4(),
                            'class_master_id' => $s_class_master_id,
                            'employee_id' => $mbo_class_lect_data->employee_id,
                            'employee_id_reported' => $mbo_class_lect_data->employee_id_reported,
                            'credit_allocation' => $lect_data['sks_allocation'],
                            'credit_charged' => $lect_data['sks_allocation'],
                            'credit_realization' => $mbo_class_lect_data->credit_realization,
                            'class_master_lecturer_status' => $mbo_class_lect_data->class_group_lecturer_status,
                            'class_master_lecturer_preferable_day' => $mbo_class_lect_data->class_group_lecturer_preferable_day,
                            'class_master_lecturer_preferable_time' => $mbo_class_lect_data->class_group_lecturer_preferable_time,
                            'class_master_lecturer_priority' => $mbo_class_lect_data->class_group_lecturer_priority,
                            'is_reported_to_feeder' => $mbo_class_lect_data->is_reported_to_feeder,
                            'date_added' => date('Y-m-d H:i:s')
                        );

                        $this->Cgm->save_class_master_lect_data($a_class_master_lecturer_data);
                    }else{
                        $a_rtn =  array('code' => 1, 'message' => 'Error parsing lecturer');
                    }
                }
            }else{
                $a_rtn =  array('code' => 1, 'message' => 'Error parsing class lecturer');
            }

            // if ($a_class_master_for_remove) {
            //     foreach ($a_class_master_for_remove as $class_master_id) {
            //         if ($class_master_id != $s_class_master_id) {
            //             $this->Cgm->remove_class_master_not_used($class_master_id);
            //         }
            //     }
            // }

            if ($this->db->trans_status() === FALSE) {
                $a_rtn = array('code' => 1, 'message' => 'Error merging class');
                $this->db->trans_rollback();
            }else{
                $a_rtn = array('code' => 0, 'message' => 'Success');
                // $this->commit_mastering_class();
                $this->db->trans_commit();
            }

            print json_encode($a_rtn);
        }
    }

    public function push_class_group_subject($s_class_group_id)
    {
        $mba_class_in_master = $this->Cgm->get_class_master_group(array('class_group_id' => $s_class_group_id));
        
        if ($mba_class_in_master) {
            $a_class_id_for_insert_subject = array();
            $mba_class_group_subject_data = false;
            
            $mba_class_group_master = $this->Cgm->get_class_master_group(array('class_master_id' => $mba_class_in_master[0]->class_master_id));
            
            foreach ($mba_class_group_master as $o_class_groups) {
                $mba_class_group_data = $this->Cgm->get_class_group_lists(array('class_group_id' => $o_class_groups->class_group_id))[0];
                if ($mba_class_group_data) {
                    if (!$mba_class_group_subject_data) {
                        $mba_class_group_subject_data = array(
                            // 'class_group_subject_id' => $this->uuid->v4(),
                            // 'class_group_id' => $s_class_group_id,
                            'offered_subject_id' => $mba_class_group_data->offered_subject_id,
                            'date_added' => date('Y-m-d H:i:s')
                        );
                    }
                }else{
                    if (!in_array($o_class_groups->class_group_id, $a_class_id_for_insert_subject)) {
                        array_push($a_class_id_for_insert_subject, $o_class_groups->class_group_id);
                    }
                }
            }

            if (($mba_class_group_subject_data) AND (count($a_class_id_for_insert_subject) > 0)) {
                foreach ($a_class_id_for_insert_subject as $s_class_id) {
                    $mba_class_group_subject_data['class_group_subject_id'] = $this->uuid->v4();
                    $mba_class_group_subject_data['class_group_id'] = $s_class_id;
                    $this->Cgm->save_class_group_subject($mba_class_group_subject_data);
                }
            }
            return true;
        }else{
            return false;
        }
    }

    public function start_merging_class()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('data')) {
                $a_class_group_id = $this->input->post('data');

                if (count($a_class_group_id) > 0) {
                    $mba_class_group_data = $this->Cgm->get_class_groups(array('class_group_id' => $a_class_group_id[0]));
                    $mba_class_group_lecturer = $this->Cgm->get_class_group_lecturer(array('cgl.class_group_id' => $a_class_group_id[0]));
                    $s_subject_name = '';
                    $s_sks = '';
                    $s_lecturer_allocation = '';
                    $a_lecturer_allocations = array();
                    $s_class_master_id = '';
                    $mbs_error_message = false;

                    foreach ($a_class_group_id as $s_class_group_id) {
                        $mba_class_group_details = $this->Cgm->get_class_group_lists(array('class_group_id' => $s_class_group_id));
                        if (!$mba_class_group_details) {
                            if ($this->push_class_group_subject($s_class_group_id)) {
                                $mba_class_group_details = $this->Cgm->get_class_group_lists(array('class_group_id' => $s_class_group_id));
                            }else{
                                $mbs_error_message = 'Error filling class subject- class_group_id:'.$s_class_group_id;
                                exit;
                            }
                        }
                        $mba_class_group_lecturer = $this->Cgm->get_class_group_lecturer(array('cgl.class_group_id' => $s_class_group_id));
                        $a_lecturer_allocation = array();
                        $s_lecturer_sks = '';
                        if ($mba_class_group_lecturer) {
                            foreach ($mba_class_group_lecturer as $o_class_lecturer) {
                                $s_lecturer = $o_class_lecturer->employee_id.'-'.$o_class_lecturer->credit_allocation;
                                if (!in_array($s_lecturer ,$a_lecturer_allocation)) {
                                    array_push($a_lecturer_allocation, $s_lecturer);
                                }
                            }
                        }
        
                        // if (count($a_lecturer_allocation) > 0) {
                        //     $s_lecturer_sks = implode(',', $a_lecturer_allocation);
                        // }

                        $a_diff_lecturer_allocation = array_diff($a_lecturer_allocation, $a_lecturer_allocations);
                        
                        if ($s_subject_name == '') {
                            $s_subject_name = $mba_class_group_details[0]->subject_name;
                            $s_sks = $mba_class_group_details[0]->curriculum_subject_credit;
                            $s_lecturer_allocation = $s_lecturer_sks;
                            $a_lecturer_allocations = $a_lecturer_allocation;
        
                        }elseif ($s_subject_name != $mba_class_group_details[0]->subject_name) {
                            // $mbs_error_message = 'Subject not same';
                            $mbs_error_message = $s_subject_name.' != '.$mba_class_group_details[0]->subject_name;
                            break;
        
                        }elseif ($s_sks != $mba_class_group_details[0]->curriculum_subject_credit) {
                            $mbs_error_message = 'Credit not same';
                            break;
        
                        }elseif (count($a_diff_lecturer_allocation) > 0) {
                            $mbs_error_message = 'Lecturer or credit allocation not same in class groups';
                            break;
        
                        }
        
                        // print($s_class_group_id.'<br>');
                        $mba_class_in_master = $this->Cgm->get_class_master_group(array('cmc.class_group_id' => $s_class_group_id));
                        if ($mba_class_in_master) {
                            if ($s_class_master_id == '') {
                                $s_class_master_id = $mba_class_in_master[0]->class_master_id;
                            }
        
                            $mba_class_group_in_master = $this->Cgm->get_class_master_group(array(
                                'class_master_id' => $mba_class_in_master[0]->class_master_id
                            ));
        
                            // print('<br>');
                            // var_dump($mba_class_group_in_master);
                            foreach ($mba_class_group_in_master as $o_class_master) {
                                // print('<br>');
                                // var_dump($o_class_master->class_group_id);
                                if (!in_array($o_class_master->class_group_id, $a_class_group_id)) {
                                    array_push($a_class_group_id, $o_class_master->class_group_id);
                                }
                            }
                        }
                    }

                    if (!$mbs_error_message) {
                        // print('<pre>');
                        // var_dump($a_class_group_id);exit;
                        // print('Class ok, ready to merging!<br>');
                        $this->db->trans_start();
        
                        if ($s_class_master_id == '') {
                            $s_class_master_id = $this->uuid->v4();
        
                            # buat class_master baru
                            $a_class_master_data = array(
                                'class_master_id' => $s_class_master_id,
                                'academic_year_id' => $mba_class_group_data[0]->academic_year_id,
                                'semester_type_id' => $mba_class_group_data[0]->semester_type_id,
                                'class_master_name' => $s_subject_name,
                                'date_added' => date('Y-m-d H:i:s')
                            );
        
                            $this->Cgm->save_class_mastering($a_class_master_data);
                            // print('Create new class master!<br>');
                            // print_r($a_class_master_data);
        
                            # buat class_master_lecturer_baru
                            if ($mba_class_group_lecturer) {
                                foreach ($mba_class_group_lecturer as $o_lecturer) {
                                    $a_class_master_lecturer = array(
                                        'class_master_lecturer_id' => $this->uuid->v4(),
                                        'class_master_id' => $s_class_master_id,
                                        'employee_id' => $o_lecturer->employee_id,
                                        'employee_id_reported' => $o_lecturer->employee_id_reported,
                                        'credit_allocation' => $o_lecturer->credit_allocation,
                                        'credit_charged' => $o_lecturer->credit_charged,
                                        'credit_realization' => $o_lecturer->credit_realization,
                                        'class_master_lecturer_status' => $o_lecturer->class_group_lecturer_status,
                                        'class_master_lecturer_preferable_day' => $o_lecturer->class_group_lecturer_preferable_day,
                                        'class_master_lecturer_preferable_time' => $o_lecturer->class_group_lecturer_preferable_time,
                                        'class_master_lecturer_priority' => $o_lecturer->class_group_lecturer_priority,
                                        'is_reported_to_feeder' => $o_lecturer->is_reported_to_feeder,
                                        'date_added' => date('Y-m-d H:i:s')
                                    );
        
                                    $this->Cgm->save_class_master_lect_data($a_class_master_lecturer);
                                    // print('Create new class master lecturer!<br>');
                                    // print_r($a_class_master_lecturer);
                                }
                            }
                        }
        
                        foreach ($a_class_group_id as $s_class_group_id) {
                            $mba_class_in_master = $this->Cgm->get_class_master_group(array('cmc.class_group_id' => $s_class_group_id));
                            if ($mba_class_in_master) {
                                // print("Class group id {$s_class_group_id} already in class master!<br>");
                                if ($mba_class_in_master[0]->class_master_id != $s_class_master_id) {
                                    # update class_master_id
                                    $this->Cgm->save_class_master_class(array(
                                        'class_master_id' => $s_class_master_id
                                    ), array(
                                        'class_master_id' => $mba_class_in_master[0]->class_master_id,
                                        'class_group_id' => $s_class_group_id
                                    ));
                                    // print("Updating class_master_class class_master_id {$mba_class_in_master[0]->class_master_id} to {$s_class_master_id}!<br>");
                                }
        
                                $mba_class_master_subject_delivered = $this->Cgm->get_unit_subject_delivered($mba_class_in_master[0]->class_master_id);
                                if ($mba_class_master_subject_delivered) {
                                    // print("class_master_id {$mba_class_in_master[0]->class_master_id} having Subject delivered<br>");
                                    foreach ($mba_class_master_subject_delivered as $o_class_subject) {
                                        $mba_class_master_subject_delivered_new = $this->Cgm->get_unit_subject_delivered($s_class_master_id, array(
                                            'cgsm.employee_id' => $o_class_subject->employee_id,
                                            'subject_delivered_time_start' => $o_class_subject->subject_delivered_time_start
                                        ));
        
                                        if (!$mba_class_master_subject_delivered_new) {
                                            // print("Subject delivered not same!<br>");
                                            $s_subject_delivered_id = $this->uuid->v4();
                                            #insert class_subject_delivered
                                            $a_class_subject_delivered_data = array(
                                                'subject_delivered_id' => $s_subject_delivered_id,
                                                'class_master_id' => $s_class_master_id,
                                                'class_group_id' => $o_class_subject->class_group_id,
                                                'employee_id' => $o_class_subject->employee_id,
                                                'subject_delivered_time_start' => $o_class_subject->subject_delivered_time_start,
                                                'subject_delivered_time_end' => $o_class_subject->subject_delivered_time_end,
                                                'subject_delivered_description' => $o_class_subject->subject_delivered_description,
                                                'number_of_meeting' => $o_class_subject->number_of_meeting,
                                                'subject_delivered_time_start' => $o_class_subject->subject_delivered_time_start,
                                                'date_added' => date('Y-m-d H:i:s')
                                            );
        
                                            $this->Cgm->save_subject_delivered($a_class_subject_delivered_data);
                                            // print("insert new subject delivered!<br>");
                                            // print_r($a_class_subject_delivered_data);
                                            
                                            $mba_absence_student = $this->Cgm->get_absence_student(array('subject_delivered_id' => $o_class_subject->subject_delivered_id));
                                            if ($mba_absence_student) {
                                                # update subject_delivered_id dari absence_student
                                                $this->Cgm->save_student_absence(array(
                                                    'subject_delivered_id' => $s_subject_delivered_id
                                                ), array(
                                                    'subject_delivered_id' => $o_class_subject->subject_delivered_id
                                                ));
                                                // print("updating subject_delivered_id in absence_student from {$o_class_subject->subject_delivered_id} to {$s_subject_delivered_id}!<br>");
                                            }
                                        }
        
                                        if ($o_class_subject->class_master_id != $s_class_master_id) {
                                            # update class_master_id dengan null di o_class_subject
                                            $this->Cgm->save_subject_delivered(array(
                                                'class_master_id' => NULL
                                            ), $o_class_subject->subject_delivered_id);
                                            // print("set class_master_id null from subject_delivered_id {$o_class_subject->subject_delivered_id}!<br>");
                                        }
                                    }
                                }
                            }else{
                                # insert class_master_class
                                $a_class_master_class_data = array(
                                    'class_master_id' => $s_class_master_id,
                                    'class_group_id' => $s_class_group_id,
                                    'date_added' => date('Y-m-d H:i:s')
                                );
        
                                $this->Cgm->save_class_master_class($a_class_master_class_data);
                                // print("insert new class_master_class!<br>");
                                // print_r($a_class_master_class_data);
                            }
        
                            #update class_master_id di dt_score where class_group_id
                            $this->Scm->save_data(array(
                                'class_master_id' => $s_class_master_id
                            ), array(
                                'class_group_id' => $s_class_group_id
                            ));
                            // print("update dt_score class_master_id to {$s_class_master_id} from  class_group_id {$s_class_group_id}!<br>");
                        }
        
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $a_return = array('code' => 1, 'message' => 'Error merging class!');
                            // print('trans_status FALSE');
                        }else{
                            $this->db->trans_commit();
                            $a_return = array('code' => 0, 'message' => 'Success');
                            // print('trans_status TRUE');
                        }
                    }else{
                        $a_return = array('code' => 1, 'message' => $mbs_error_message);
                    }
                }else{
                    $a_return = array('code' => 1, 'message' => 'No class selected');
                }
            }else{
                $a_return = array('code' => 1, 'message' => 'No data selected');
            }
            // $a_return = array('code' => 0, 'message' => 'Testing');
            print json_encode($a_return);
        }
    }

    public function commit_mastering_class()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('academic_year_id', 'Running Year Filter', 'required');
            $this->form_validation->set_rules('semester_type_id', 'Semester Type Filter', 'required');

            if ($this->form_validation->run()) {
                $mbo_class_group_data = $this->Cgm->get_class_groups(array('academic_year_id' => set_value('academic_year_id'), 'semester_type_id' => set_value('semester_type_id')));
                if ($mbo_class_group_data) {
                    $this->db->trans_start();
                    foreach ($mbo_class_group_data as $class) {
                        $mba_class_in_master = $this->Cgm->get_class_master_group(array('class_group_id' => $class->class_group_id));
                        if (!$mba_class_in_master) {
                            $mbo_class_group_subject_data = $this->Cgm->get_class_group_subject($class->class_group_id)[0];
                            if (!$mbo_class_group_subject_data) {
                                $s_class_name = $class->class_group_name.' '.set_value('academic_year_id').set_value('semester_type_id');
                            }else{
                                $s_class_name = $mbo_class_group_subject_data->subject_name.' '.set_value('academic_year_id').set_value('semester_type_id');
                            }
                            $s_class_master_id = $this->uuid->v4();
                            $a_class_master_data = array(
                                'class_master_id' => $s_class_master_id,
                                'academic_year_id' => $class->academic_year_id,
                                'semester_type_id' => $class->semester_type_id,
                                'class_master_name' => $s_class_name,
                                'date_added' => date('Y-m-d H:i:s')
                            );

                            if ($this->Cgm->save_class_mastering($a_class_master_data)) {
                                $a_class_master_class_data = array(
                                    'class_master_id' => $s_class_master_id,
                                    'class_group_id' => $class->class_group_id,
                                    'date_added' => date('Y-m-d H:i:s')
                                );

                                $this->Cgm->save_class_master_class($a_class_master_class_data);
                                $mbo_class_group_lect = $this->Cgm->get_class_lecturer_grouping(array('class_group_id' => $class->class_group_id));

                                if ($mbo_class_group_lect) {
                                    foreach ($mbo_class_group_lect as $class_lect) {
                                        $a_class_master_lect_data = array(
                                            'class_master_lecturer_id' => $this->uuid->v4(),
                                            'class_master_id' => $s_class_master_id,
                                            'employee_id' => $class_lect->employee_id,
                                            'employee_id_reported' => $class_lect->employee_id_reported,
                                            'credit_allocation' => $class_lect->credit_allocation,
                                            'credit_charged' => $class_lect->credit_charged,
                                            'credit_realization' => $class_lect->credit_realization,
                                            'class_master_lecturer_status' => $class_lect->class_group_lecturer_status,
                                            'class_master_lecturer_preferable_day' => $class_lect->class_group_lecturer_preferable_day,
                                            'class_master_lecturer_priority' => $class_lect->class_group_lecturer_priority,
                                            'is_reported_to_feeder' => $class_lect->is_reported_to_feeder,
                                            'date_added' => date('Y-m-d H:i:s')
                                        );

                                        $this->Cgm->save_class_master_lect_data($a_class_master_lect_data);
                                    }
                                }
                            }
                        }
                    }

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $a_rtn = array('code' => 1, 'message' => 'Error proccessing data');
                    }
                    else{
                        $this->db->trans_commit();
                        $a_rtn = array('code' => 0, 'message' => 'Proccess success');
                    }
                }
                else {
                    $a_rtn = array('code' => 1, 'message' => 'Class not found');
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }
        }
        else {
            $a_rtn = array('code' => 1, 'message' => 'No Action commit class');
        }

        print json_encode($a_rtn);
    }

    public function get_class_group_lists($a_filter_data = false)
    {
        if($a_filter_data) {
            $mbo_class_group_data = $this->Cgm->get_class_group_lists($a_filter_data);
            $a_lect_list = array();
            $a_lect_email_list = array();
            $a_study_prog_list = array();

            $a_data = array();
            if ($mbo_class_group_data) {
                foreach ($mbo_class_group_data as $class) {
                    $mbo_class_in_master = $this->Cgm->get_class_master_group(array('class_group_id' => $class->class_group_id));
                    if (!$mbo_class_in_master) {
                        $mbo_class_lecturer = $this->Cgm->get_class_group_lecturer(array('class_group_id' => $class->class_group_id));
                        $mbo_class_subject = $this->Cgm->get_class_group_subject($class->class_group_id);
                        $mbo_class_study_prog = $this->Cgm->get_class_group_study_program($class->class_group_id);
                        $mbo_class_student_data = $this->Cgm->get_class_group_student($class->class_group_id);
                        $a_class_lecturer = array();
                        $a_class_lecturer_email = array();
                        $a_lecturer_personal_id = array();
                        $a_semester_number = array();

                        if ($mbo_class_student_data) {
                            foreach ($mbo_class_student_data as $o_class_student) {
                                if (!in_array($o_class_student->score_semester, $a_semester_number)) {
                                    array_push($a_semester_number, $o_class_student->score_semester);
                                }
                            }
                        }

                        if (count($a_semester_number) > 0) {
                            sort($a_semester_number);
                        }
                        
                        if ($mbo_class_lecturer) {
                            foreach ($mbo_class_lecturer as $lect) {
                                $class_lecturer = $lect->personal_data_name.' ('.$lect->credit_allocation.')';
                                $class_lecturer_email = $lect->employee_email;
                                if (!in_array($lect->personal_data_name, $a_lect_list)) {
                                    array_push($a_lect_list, $lect->personal_data_name);
                                    array_push($a_lect_email_list, $lect->employee_email);
                                }
                                array_push($a_class_lecturer, $class_lecturer);
                                array_push($a_class_lecturer_email, $class_lecturer_email);
                                array_push($a_lecturer_personal_id, $lect->personal_data_id);
                            }
                        }

                        $a_class_study_subject = array();
                        if ($mbo_class_subject) {
                            foreach ($mbo_class_subject as $class_subject) {
                                array_push($a_class_study_subject, $class_subject->subject_code.' | '.$class_subject->subject_name);
                            }
                        }

                        $a_class_study_prog = array();
                        if ($mbo_class_study_prog) {
                            foreach ($mbo_class_study_prog as $class_study_prog) {
                                if (!in_array($class_study_prog->study_program_abbreviation, $a_study_prog_list)) {
                                    array_push($a_study_prog_list, $class_study_prog->study_program_abbreviation);
                                }
                                array_push($a_class_study_prog, $class_study_prog->study_program_abbreviation);
                            }
                        }
                        $class->study_prog = (count($a_class_study_prog) > 0) ? implode(' / ', $a_class_study_prog) : 'N/A';
                        $class->student_count = ($mbo_class_student_data) ? count($mbo_class_student_data) : 0;
                        $class->study_subject = (count($a_class_study_subject) > 0) ? implode(' / ', $a_class_study_subject) : 'N/A';
                        $s_class_lecturer = (count($a_class_lecturer) > 0) ? implode(' & ', $a_class_lecturer) : 'N/A';
                        $s_class_lecturer_email = (count($a_class_lecturer_email) > 0) ? implode(' & ', $a_class_lecturer_email) : 'N/A';
                        $class->lecturer = $s_class_lecturer;
                        $class->class_student_semester = (count($a_semester_number) > 0) ? implode(' / ', $a_semester_number) : 'N/A';
                        $class->lecturer_email = $s_class_lecturer_email;
                        $class->lecturer_data = $a_lecturer_personal_id;

                        if (($class->study_subject != 'N/A') OR ($class->study_prog != 'N/A')) {
                            array_push($a_data, $class);
                        }
                    }
                }
            }

            $a_return = array(
                'class_data' => $a_data,
                'lect_data' => $a_lect_list,
                'lect_email_data' => $a_lect_email_list,
                'prodi_data' => $a_study_prog_list
            );
            return $a_return;
        }
    }

    public function get_all_class_absence($s_academic_year_id, $s_semester_type_id)
    {
        $mba_student_class_data = $this->Cgm->get_class_group_student(false, array(
            'ds.academic_year_id' => $s_academic_year_id,
            'ds.semester_type_id' => $s_semester_type_id
        ));

        if ($mba_student_class_data) {

            foreach ($mba_student_class_data as $key => $o_class_student) {
                if (!is_null($o_class_student->class_master_id)) {
                    $mba_class_data = $this->Cgm->get_class_master_filtered(array(
                        'cm.class_master_id' => $o_class_student->class_master_id
                    ));

                    $mba_class_lect_data = $this->Cgm->get_class_master_lecturer(array(
                        'class_master_id' => $o_class_student->class_master_id
                    ));

                    $a_employee_name = array();
                    if ($mba_class_lect_data) {
                        foreach ($mba_class_lect_data as $o_class_lecturer) {
                            // $mba_lecturer_data = $this->Emm->get_employee_data(array(
                            //     'employee_id' => $o_class->employee_id
                            // ));
                            if (!in_array($o_class_lecturer->personal_data_name, $a_employee_name)) {
                                array_push($a_employee_name, $o_class_lecturer->personal_data_name);
                            }
                        }
                    }

                    $mba_student_class_data[$key]->class_name = $mba_class_data[0]->class_master_name;
                    $mba_student_class_data[$key]->subject_name = $mba_class_data[0]->subject_name;
                    $mba_student_class_data[$key]->lecturer_list = implode(' & ', $a_employee_name);
    
                    // print_r(
                    //     array(
                    //         'Name' => $o_class_student->personal_data_name,
                    //         'Student ID' => $o_class_student->student_number,
                    //         'Study Program' => $o_class_student->study_program_abbreviation,
                    //         'Semester' => $o_class_student->score_semester,
                    //         'Class Name' => $mba_class_data[0]->class_master_name,
                    //         // 'score_id' => $o_class_student->score_id,
                    //         'Subject' => $mba_class_data[0]->subject_name,
                    //         'Absence' => $o_class_student->score_absence,
                    //         'Lecturer' => implode(' & ', $a_employee_name)
                    //     )
                    // );
                }
                // else{
                //     print('<h1>'.$o_class_student->score_id.'</h1>');
                //     exit;
                // }
            }
        }

        return $mba_student_class_data;

        // print(count($mba_student_class_data));
    }

    public function get_class_master_lists($a_filter_data = false)
    {
        if ($a_filter_data) {
            $mbo_class_data = $this->Cgm->get_class_group_master_lists($a_filter_data);
            $a_lect_list = array();
            $a_lect_email_list = array();
            $a_study_prog_list = array();

            $a_data = array();
            if ($mbo_class_data) {
                foreach ($mbo_class_data as $class) {
                    $mbo_class_lecturer = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $class->class_master_id));
                    $mbo_class_subject = $this->Cgm->get_class_master_subject(array('cm.class_master_id' => $class->class_master_id));
                    $mbo_class_study_prog = $this->Cgm->get_class_master_study_program($class->class_master_id);
                    $mbo_class_student_data = $this->Cgm->get_class_master_student($class->class_master_id);
                    $a_class_lecturer = array();
                    $a_class_lecturer_email = array();
                    $a_lecturer_personal_id = array();
                    $a_semester_number = array();
                    
                    if ($mbo_class_student_data) {
                        foreach ($mbo_class_student_data as $o_class_student) {
                            if (!in_array($o_class_student->score_semester, $a_semester_number)) {
                                array_push($a_semester_number, $o_class_student->score_semester);
                            }
                        }
                    }

                    if (count($a_semester_number) > 0) {
                        sort($a_semester_number);
                    }
                    
                    if ($mbo_class_lecturer) {
                        foreach ($mbo_class_lecturer as $lect) {
                            $class_lecturer = $lect->personal_data_name.' ('.$lect->credit_allocation.')';
                            $class_lecturer_email = $lect->employee_email;
                            if (!in_array($lect->personal_data_name, $a_lect_list)) {
                                array_push($a_lect_list, $lect->personal_data_name);
                                array_push($a_lect_email_list, $lect->employee_email);
                            }
                            array_push($a_class_lecturer, $class_lecturer);
                            array_push($a_class_lecturer_email, $class_lecturer_email);
                            array_push($a_lecturer_personal_id, $lect->personal_data_id);
                        }
                    }

                    $a_class_study_subject = array();
                    if ($mbo_class_subject) {
                        foreach ($mbo_class_subject as $class_subject) {
                            array_push($a_class_study_subject, $class_subject->subject_code.' | '.$class_subject->subject_name);
                        }
                    }

                    $a_class_study_prog = array();
                    if ($mbo_class_study_prog) {
                        foreach ($mbo_class_study_prog as $class_study_prog) {
                            if (!in_array($class_study_prog->study_program_abbreviation, $a_study_prog_list)) {
                                array_push($a_study_prog_list, $class_study_prog->study_program_abbreviation);
                            }
                            array_push($a_class_study_prog, $class_study_prog->study_program_abbreviation);
                        }
                    }
                    $class->study_prog = (count($a_class_study_prog) > 0) ? implode(' / ', $a_class_study_prog) : 'N/A';
                    $class->student_count = ($mbo_class_student_data) ? count($mbo_class_student_data) : 0;
                    $class->study_subject = (count($a_class_study_subject) > 0) ? implode(' / ', $a_class_study_subject) : 'N/A';
                    $s_class_lecturer = (count($a_class_lecturer) > 0) ? implode(' & ', $a_class_lecturer) : 'N/A';
                    $s_class_lecturer_email = (count($a_class_lecturer_email) > 0) ? implode(' & ', $a_class_lecturer_email) : 'N/A';
                    $class->class_student_semester = (count($a_semester_number) > 0) ? implode(' / ', $a_semester_number) : 'N/A';
                    $class->lecturer = $s_class_lecturer;
                    $class->lecturer_email = $s_class_lecturer_email;
                    $class->lecturer_data = $a_lecturer_personal_id;

                    if (($class->study_subject != 'N/A') OR ($class->study_prog != 'N/A')) {
                        array_push($a_data, $class);
                    }
                }
            }

            $a_return = array(
                'class_data' => $a_data,
                'lect_data' => $a_lect_list,
                'lect_email_data' => $a_lect_email_list,
                'prodi_data' => $a_study_prog_list
            );
            return $a_return;
        }
    }

    public function get_class_group_data_lists($a_filter_data = false)
    {
        if ($a_filter_data) {
            $mbo_class_data = $this->Cgm->get_class_group_filtered($a_filter_data);
            if ($mbo_class_data) {
                foreach ($mbo_class_data as $key => $class) {
                    $mbo_class_score = $this->Cgm->student_in_class($class->class_group_id);
                    if ($mbo_class_score) {
                        $mbo_class_lecturer = $this->Cgm->get_class_group_lecturer(array('class_group_id' => $class->class_group_id));
                        $s_class_lecturer = 'N/A';
                        if ($mbo_class_lecturer) {
                            $a_class_lecturer = array();
                            foreach ($mbo_class_lecturer as $lect) {
                                $class_lecturer = $lect->personal_data_name.' ('.$lect->credit_allocation.')';
                                array_push($a_class_lecturer, $class_lecturer);
                            }

                            $s_class_lecturer = implode(',', $a_class_lecturer);
                        }
                        $class->lecturer = $s_class_lecturer;
                    }else{
                        unset($mbo_class_data[$key]);
                    }
                }
            }
            return $mbo_class_data;
        }
    }

    public function save_absence()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('academic/Class_group_model', 'Cgm');

            $this->form_validation->set_rules('personal_data_name', 'Lecturer', 'required');
            $this->form_validation->set_rules('employee_id', 'Lecturer', 'required');
            $this->form_validation->set_rules('class_master_id', 'System 2', 'required');
            $this->form_validation->set_rules('unit_date', 'Date', 'trim|required');
            $this->form_validation->set_rules('unit_time', 'Time Start', 'trim|required');
            $this->form_validation->set_rules('unit_time_end', 'Time End', 'trim');
            $this->form_validation->set_rules('unit_description', 'Topics Covered', 'trim|required');

            if ($this->form_validation->run()) {
                $a_uosd_absence = (empty($this->input->post('uosd_absence'))) ? [] : $this->input->post('uosd_absence');

                $a_absence_data = array(
                    'employee_id' => set_value('employee_id'),
                    'class_master_id' => set_value('class_master_id'),
                    'with_quiz' => $this->input->post('with_quiz'),
                    'quiz_number' => $this->input->post('quiz_number'),
                    'unit_date' => set_value('unit_date'),
                    'unit_time' => set_value('unit_time'),
                    'unit_time_end' => set_value('unit_time_end'),
                    'unit_description' => set_value('unit_description'),
                    'subject_delivered_id' => $this->input->post('subject_delivered_id')
                );
                // var_dump($a_absence_data);exit;

                $a_rtn = $this->Cgm->save_absence($a_absence_data, $a_uosd_absence);
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print(json_encode($a_rtn));exit;
        }
    }

    function save_absence_dev() {
        if ($this->input->is_ajax_request()) {
            print('<pre>');var_dump($this->input->post());exit;
        }
    }

    public function clear_absence()
    {
        // $mba_score_data = $this->Scm->get_score_data(array('sc.academic_year_id' => 2019, 'sc.semester_type_id' => 1));
        // if ($mba_score_data) {
        //     foreach ($mba_score_data as $score) {
        //         $this->Cgm->updating_score($score->score_id);
        //         print(' .');
        //     }
        // }
    }

    public function save_moving_student()
    {
        if ($this->input->is_ajax_request()) {
            $a_score_id = $this->input->post('score_id');
            // $s_subject_delivered_id = $this->input->post('subject_delivered_id');
            $s_class_master_id_destination = $this->input->post('class_master_id');
            $mba_class_master_destination_data = $this->Cgm->get_class_master_filtered(array('cm.class_master_id' => $s_class_master_id_destination))[0];
            
            if (count($a_score_id) > 0) {
                foreach ($a_score_id as $s_score_id) {
                    $mba_score_data = $this->Scm->get_score_data(array('score_id' => $s_score_id))[0];
                    if ($mba_score_data) {
                        $mba_class_master_data = $this->Cgm->get_class_master_filtered(array('cm.class_master_id' => $mba_score_data->class_master_id))[0];
                        
                        $s_subject_now = strtolower($mba_class_master_data->subject_name);
                        $s_subject_destination = strtolower($mba_class_master_destination_data->subject_name);
                        # cek mata kuliah, tahun ajaran dan semester tipe sudah sesuai
                        if ($s_subject_now != $s_subject_destination) {
                            $a_rtn = array('code' => 1, 'message' => 'Subject class not same');
                        }else if ($mba_class_master_data->running_year != $mba_class_master_destination_data->running_year) {
                            $a_rtn = array('code' => 1, 'message' => 'Academic year not same');
                        }else if ($mba_class_master_data->class_semester_type_id != $mba_class_master_destination_data->class_semester_type_id) {
                            $a_rtn = array('code' => 1, 'message' => 'Semester type not same');
                        }else {
                            $a_score_data = array(
                                'class_master_id' => $s_class_master_id_destination,
                                'class_group_id' => $mba_class_master_destination_data->class_group_id
                            );

                            $this->db->trans_start();
                            if ($this->Scm->save_data($a_score_data, array('score_id' => $s_score_id))) {
                                # cek absensi di kelas sebelumnya
                                $mba_subject_delivered_current_class = $this->Cgm->get_unit_subject_delivered($mba_score_data->class_master_id);
                                if ($mba_subject_delivered_current_class) {
                                    foreach ($mba_subject_delivered_current_class as $uosd) {
                                        # cek absensi di kelas tujuan
                                        $mba_subject_delivered_destination = $this->Cgm->get_unit_subject_delivered($s_class_master_id_destination, array('number_of_meeting' => $uosd->number_of_meeting))[0];
                                        if ($mba_subject_delivered_destination) {
                                            $mba_student_current_absence = $this->Cgm->get_absence_student(array('subject_delivered_id' => $uosd->subject_delivered_id))[0];
                                            if ($mba_student_current_absence) {
                                                $a_student_absence = array(
                                                    'subject_delivered_id' => $mba_subject_delivered_destination->subject_delivered_id
                                                );

                                                $this->Cgm->save_student_absence($a_student_absence, array('absence_student_id' => $mba_student_current_absence->absence_student_id));
                                            }
                                        }
                                    }
                                }
                            }else {
                                $a_rtn = array('code' => 1, 'message' => 'Error moving student');
                            }

                            if ($this->db->trans_status() === FALSE) {
                                $this->db->trans_rollback();
                                $a_rtn = array('code' => 1, 'message' => 'Error moving student');
                            }else{
                                $this->db->trans_commit();
                                $a_rtn = array('code' => 0, 'message' => 'Success');
                            }
                        }
                    }else{
                        $a_rtn = array('code' => 1, 'message' => 'Data score not found!');
                    }
                }
            }else {
                $a_rtn = array('code' => 1, 'message' => 'No class selected!');
            }

            print(json_encode($a_rtn));
            exit;
        }
    }

    public function get_class_quiz()
    {
        if ($this->input->is_ajax_request()) {
            $s_class_master_id = $this->input->post('class_master_id');

            $quiz = 0;
            for ($i=1; $i <= 6; $i++) { 
                $mbo_check_quiz = $this->Scm->get_score_data(array('class_master_id' => $s_class_master_id, 'score_quiz'.$i.' !=' => 'null'));
                if (!$mbo_check_quiz) {
                    $quiz = $i;
                    break;
                }
            }
            
            if ($quiz > 0) {
                $a_rtn = array('code' => 0, 'message' => $quiz);
            }else{
                $a_rtn = array('code' => 1, 'message' => 'Slot quiz is full');
            }

            print json_encode($a_rtn);
        }
    }

    public function get_class_master_details($s_class_master_id)
    {
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $mba_class_master_data = $this->Cgm->get_class_master_filtered(array('cm.class_master_id' => $s_class_master_id))[0];
        if ($mba_class_master_data) {
            $mba_class_lecturer_lists = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $s_class_master_id));
            $mbo_class_study_program_lists = $this->Cgm->get_class_master_study_program($s_class_master_id);
            $mba_class_student_lists = $this->Cgm->get_class_master_student($s_class_master_id);
            $a_team_teaching_lists = array();
            $a_class_prodi = array();
            $a_lect_email = array();
            
            if ($mba_class_lecturer_lists) {
                foreach ($mba_class_lecturer_lists as $lecturer) {
                    $s_lecturer = $this->Pdm->retrieve_title($lecturer->personal_data_id);
                    array_push($a_team_teaching_lists, $s_lecturer);
                    array_push($a_lect_email, $lecturer->employee_email);
                }
            }

            if ($mbo_class_study_program_lists) {
                foreach ($mbo_class_study_program_lists as $class_prodi) {
                    array_push($a_class_prodi, $class_prodi->study_program_abbreviation);
                }
            }

            $mba_class_master_data->team_teaching_lists = $a_team_teaching_lists;
            $mba_class_master_data->class_prodi = $a_class_prodi;
            $mba_class_master_data->team_teaching_email = $a_lect_email;
            $mba_class_master_data->student_lists = $mba_class_student_lists;
            return $mba_class_master_data;
        }else{
            return false;
        }
    }
}