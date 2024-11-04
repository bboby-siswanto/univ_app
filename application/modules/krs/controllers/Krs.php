<?php
class Krs extends App_core
{
    function __construct()
    {
        parent::__construct('academic');
        $this->load->model('krs/Krs_model', 'Krsm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('academic/Class_group_model', 'Cgm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('personal_data/Family_model', 'Fmm');
    }

    public function check()
    {
        $mbo_registration_data = $this->Scm->get_score_student('bf182adf-76bd-4e2e-bd83-de47619b40a9', array(
            'sc.academic_year_id' => 2020,
            'sc.semester_type_id' => 1
            // 'sc.score_approval' => 'pending'
        ));

        print('<pre>');
        var_dump($mbo_registration_data);exit;
    }

    public function krs_approval($s_academic_year_id = false, $s_semester_type_id = false, $s_personal_data_id = false)
    {
        if (($s_personal_data_id) AND ($s_academic_year_id) AND ($s_semester_type_id)) {
            // $mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id, ['student_status' => 'active']);
            // $mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id);
            $mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id, ['student_status != ' => 'resign']);
            // print('<pre>');
            // var_dump($mbo_student_data);exit;
            $o_semester_active = $this->Smm->get_active_semester();

            $i_max_credit = 24;
            
            if ($mbo_student_data) {
                $this->a_page_data['research_semester_approved'] = false;

                $mbo_registration_data = $this->Scm->get_score_student($mbo_student_data->student_id, array(
                    'sc.academic_year_id' => $s_academic_year_id,
                    'sc.semester_type_id' => $s_semester_type_id,
                    // 'sc.score_approval' => 'pending'
                ));

                if ($mbo_registration_data) {
                    foreach ($mbo_registration_data as $o_registrations) {
                        if ($o_registrations->score_approval == 'approved') {
                            $sn = strtolower($o_registrations->subject_name);
                            if (strpos($sn, 'research semester') !== false) {
                                $this->a_page_data['research_semester_approved'] = true;
                                break;
                            }
                        }
                    }
                }
                
                $credit_ss = 9;
                if ($s_semester_type_id == 7) {
                    $i_max_credit = $credit_ss;
                    $s_semester_type_parent = 1;

                    $s_field_offered_subject_end_date = 'study_plan_approval_short_semester_end_date';
                }else if ($s_semester_type_id == 8) {
                    $s_semester_type_parent = 2;

                    $s_field_offered_subject_end_date = 'study_plan_approval_short_semester_end_date';

                    $mbo_sum_credit = $this->Scm->get_sum_merit_credit(array(
                        'sc.student_id' => $mbo_student_data->student_id,
                        'sc.academic_year_id' => $s_academic_year_id,
                        'sc.semester_type_id' => 7,
                        'sc.score_approval' => 'approved',
                        'sc.score_display' => 'TRUE'
                    ));

                    $i_max_credit = $credit_ss - intval($mbo_sum_credit->sum_credit);
                    $i_max_credit = ($i_max_credit < 0) ? 0 : $i_max_credit;
                }else{
                    $s_semester_type_parent = $s_semester_type_id;
                    $s_field_offered_subject_end_date = 'study_plan_approval_end_date';
                }

                $this->a_page_data['o_personal_data'] = $this->Pdm->get_personal_data_by_id($mbo_student_data->personal_data_id);
                $this->a_page_data['o_student_data'] = $mbo_student_data;
                $valid_approval = false;
                foreach ($this->a_user_roles as $roles_id) {
                    if (in_array($roles_id, [1,2,3,4,9])) {
                        $valid_approval = true;
                    }
                }
                $this->a_page_data['valid_approval'] = $valid_approval;
                // $this->a_page_data['valid_registration'] = true;
                $this->a_page_data['valid_registration'] = modules::run('academic/semester/checker_semester_academic', null, $s_field_offered_subject_end_date, $s_academic_year_id, $s_semester_type_parent);
                // if ($mbo_student_data->student_status != 'active') {
                //     $this->a_page_data['valid_approval'] = false;
                //     $this->a_page_data['valid_registration'] = false;
                // }

                $this->a_page_data['max_credit'] = $i_max_credit;
                $this->a_page_data['mbo_registration_data'] = $mbo_registration_data;
                $this->a_page_data['o_semester_active'] = $o_semester_active;
                $this->a_page_data['this_approval'] = true;
                $this->a_page_data['a_semester_selected'] = array('academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id);
                $this->a_page_data['personal_data_id'] = $mbo_student_data->personal_data_id;
                $this->a_page_data['body'] = $this->load->view('student/studyplan/registration', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
        }else{
            $this->a_page_data['body'] = $this->load->view('krs/krs_approval', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function form_filter_krs_approval()
    {
        $this->load->model('academic/Academic_year_model', 'Aym');
        $this->load->model('academic/Semester_model', 'Sms');
        $this->a_page_data['program_lists'] = $this->Spm->get_program_lists_select();
        $this->a_page_data['academic_year_lists'] = $this->Aym->get_academic_year_lists();
        $this->a_page_data['semester_type_lists'] = $this->Sms->get_semester_type_lists(false, false, array(1,2,7,8));
        $this->load->view('form/form_filter_krs_approval', $this->a_page_data);
    }

    public function view_table_krs_student()
    {
        $this->load->view('table/student_approval', $this->a_page_data);
    }

    // public function get_student_krs_list()
    // {
    //     if ($this->input->is_ajax_request()) {
    //         if ($this->input->post('semester_type_id') == 7) {
    //             $s_semester_type_parent = 1;
    //         }else if ($this->input->post('semester_type_id') == 8) {
    //             $s_semester_type_parent = 2;
    //         }else{
    //             $s_semester_type_parent = $this->input->post('semester_type_id');
    //         }
            
    //         $a_filter_data = array(
    //             // 'stu.program_id' => $this->input->post('program_id'),
    //             'ds.study_program_id' => $this->input->post('study_program_id')
    //         );

    //         $mba_student_lists = $this->Stm->get_student_filtered($a_filter_data);
    //         if ($mba_student_lists) {
    //             foreach ($mba_student_lists as $student) {
    //                 $mba_student_semester_status = $this->Krsm->get_student_semester($student->student_id, array(
    //                     'dss.academic_year_id' => $this->input->post('academic_year_id'),
    //                     'dss.semester_type_id' => $s_semester_type_parent
    //                 ));
    //                 // 
    //                 $mba_student_krs = $this->Scm->get_score_data(array('sc.student_id' => $student->student_id, 'sc.academic_year_id' => $this->input->post('academic_year_id'), 'sc.semester_type_id' => $this->input->post('semester_type_id')));
    //                 $a_krs_reject = 0;
    //                 $approval = 'N/A';
    //                 if ($mba_student_krs) {
    //                     foreach ($mba_student_krs as $krs) {
    //                         if ($krs->score_approval == 'pending') {
    //                             $approval = 'PENDING';
    //                             break;
    //                         }else if ($krs->score_approval == 'rejected') {
    //                             $a_krs_reject++;
    //                         }
    //                     }

    //                     if ($approval == 'N/A') {
    //                         if (count($mba_student_krs) == $a_krs_reject) {
    //                             $approval = 'REJECTED';
    //                         }else {
    //                             $approval = 'APPROVED';
    //                         }
    //                     }
    //                 }
    //                 $student->approval = $approval;
    //                 $student->count_subject = count($mba_student_krs);
    //             }
    //         }

    //         $a_rtn = array('code' => 0, 'data' => $mba_student_lists);
    //         print json_encode($a_rtn);
    //     }
    // }

    function get_insert_list_student_semester() {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_semester_type_parent = $s_semester_type_id;

            if ($s_semester_type_id == 7) {
                $s_semester_type_parent = 1;
            }else if ($s_semester_type_id == 8) {
                $s_semester_type_parent = 2;
            }

            $mba_student_semester_lists = (!empty($s_student_id)) ? $this->Krsm->get_student_semester($s_student_id) : false;
            if ($mba_student_semester_lists) {
                $o_student_semester = $mba_student_semester_lists[0];
                array_push($mba_student_semester_lists, [
                    'student_id' => $o_student_semester->student_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_parent,
                    'semester_id' => '',
                    'semester_number' => '',
                    'institution_id' => $o_student_semester->institution_id,
                    'student_semester_status' => 'inactive',
                    'date_added' => date('Y-m-d H:i:s'),
                ]);
                $mba_student_semester_lists = array_values($mba_student_semester_lists);
            }
            print json_encode(['data' => $mba_student_semester_lists]);
        }
    }

    function get_student_not_in_semester() {
        if ($this->input->is_ajax_request()) {
            $s_study_program_id = $this->input->post('study_program_id');
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_semester_type_parent = $s_semester_type_id;

            if ($s_semester_type_id == 7) {
                $s_semester_type_parent = 1;
            }else if ($s_semester_type_id == 8) {
                $s_semester_type_parent = 2;
            }

            $mba_student_lists = $this->Stm->get_student_filtered([
                'ds.study_program_id' => $s_study_program_id
            ], ['active', 'inactive', 'onleave', 'graduated']);
            if ($mba_student_lists) {
                foreach ($mba_student_lists as $key => $o_student) {
                    $o_student->allowed_semester = true;
                    $mba_student_semester_lists = $this->Krsm->get_student_semester($o_student->student_id, [
                        'stu.study_program_id' => $s_study_program_id,
                        'dss.academic_year_id' => $s_academic_year_id,
                        'dss.semester_type_id' => $s_semester_type_parent
                    ]);
                    $mba_student_max_semester = $this->Krsm->get_student_semester($o_student->student_id, [
                        'dss.semester_id' => '26',
                    ]);
                    if ($mba_student_semester_lists) {
                        unset($mba_student_lists[$key]);
                    }else if ($mba_student_max_semester) {
                        $o_student->allowed_semester = false;
                    }
                }

                $mba_student_lists = array_values($mba_student_lists);
            }

            print json_encode(['data' => $mba_student_lists]);
        }
    }

    function submit_student_semester() {
        if ($this->input->is_ajax_request()) {
            // print('<pre>');var_dump($this->input->post());exit;
            $s_student_id = $this->input->post('student_id_semester');
            $a_academic_year_id = $this->input->post('academic_year_id');
            $a_semester_type_id = $this->input->post('semester_type_id');
            $a_semester_number = $this->input->post('student_semester_number');

            $a_error = [];
            if (count($a_academic_year_id)) {
                $this->db->trans_begin();
                foreach ($a_academic_year_id as $key => $s_academic_year_id) {
                    $mba_semester_number_data = $this->General->get_where('ref_semester', ['semester_number' => $a_semester_number[$key]]);
                    if ($mba_semester_number_data) {
                        $a_student_semester_check_data = [
                            'student_id' => $s_student_id,
                            'academic_year_id' => $a_academic_year_id[$key],
                            'semester_type_id' => $a_semester_type_id[$key],
                        ];

                        $mba_data_ready = $this->General->get_where('dt_student_semester', $a_student_semester_check_data);
                        if ($mba_data_ready) {
                            # update...
                            $this->General->update_data('dt_student_semester', ['semester_id' => $mba_semester_number_data[0]->semester_id], $a_student_semester_check_data);

                        }
                        else {
                            # insert...
                            $this->General->insert_data('dt_student_semester', [
                                'student_id' => $s_student_id,
                                'academic_year_id' => $a_academic_year_id[$key],
                                'semester_type_id' => $a_semester_type_id[$key],
                                'semester_id' => $mba_semester_number_data[0]->semester_id,
                            ]);
                        }
                    }
                    else {
                        array_push($a_error, 'Semester number '.$a_semester_number[$key].' not found in our system!');
                    }
                }

                if ($this->db->trans_status() === false) {
                    array_push($a_error, 'No data saved!');
                    $this->db->trans_rollback();
                }
                else {
                    $this->db->trans_commit();
                }
            }
            else {
                array_push($a_error, 'No data sent to server!');
            }

            if (count($a_error) > 0) {
                $a_return = ['code' => 1, 'message' => '<li>'.implode('</li><li>', $a_error).'</li>'];
            }
            else {
                $a_return = ['code' => 0, 'message' => 'Success'];
            }

            print json_encode($a_return);
        }
    }

    public function get_student_krs_lists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('semester_type_id') == 7) {
                $s_semester_type_parent = 1;
            }else if ($this->input->post('semester_type_id') == 8) {
                $s_semester_type_parent = 2;
            }else{
                $s_semester_type_parent = $this->input->post('semester_type_id');
            }
            
            $a_filter_data = array(
                // 'stu.program_id' => $this->input->post('program_id'),
                'stu.study_program_id' => $this->input->post('study_program_id'),
                'dss.academic_year_id' => $this->input->post('academic_year_id'),
                'dss.semester_type_id' => $s_semester_type_parent
            );

            $mba_student_krs_lists = $this->Krsm->get_student_semester(false, $a_filter_data);
            if ($mba_student_krs_lists) {
                foreach ($mba_student_krs_lists as $student) {
                    $mba_student_krs = $this->Scm->get_score_data(array('sc.student_id' => $student->student_id, 'sc.academic_year_id' => $this->input->post('academic_year_id'), 'sc.semester_type_id' => $this->input->post('semester_type_id')));
                    $a_krs_reject = 0;
                    $approval = 'N/A';
                    if ($mba_student_krs) {
                        foreach ($mba_student_krs as $krs) {
                            if ($krs->score_approval == 'pending') {
                                $approval = 'PENDING';
                                break;
                            }else if ($krs->score_approval == 'rejected') {
                                $a_krs_reject++;
                            }
                        }

                        if ($approval == 'N/A') {
                            if (count($mba_student_krs) == $a_krs_reject) {
                                $approval = 'REJECTED';
                            }else {
                                $approval = 'APPROVED';
                            }
                        }
                    }
                    $student->approval = $approval;
                    $student->count_subject = ($mba_student_krs) ? count($mba_student_krs) : 0;
                }
            }
            $a_rtn = array('code' => 0, 'data' => $mba_student_krs_lists);
            print json_encode($a_rtn);
        }
    }

    public function set_student_semester($a_filter_data)
    {
        $mba_student_list = $this->Stm->get_student_filtered(array(
            'ds.study_program_id' => $a_filter_data['stu.study_program_id'],
            'ds.student_status' => 'active'
        ));

        if ($mba_student_list) {
            foreach ($mba_student_list as $stu) {
                $mba_student_semeter_data = $this->Smm->get_student_semester(array(
                    'student_id' => $stu->student_id,
                    'academic_year_id' => $a_filter_data['dss.academic_year_id'],
                    'semester_type_id' => $a_filter_data['dss.semester_type_id']
                ));

                if (!$mba_student_semeter_data) {
                    $a_student_semester_data = array(
                        'student_id' => $stu->student_id,
                        'academic_year_id' => $a_filter_data['dss.academic_year_id'],
                        'semester_type_id' => $a_filter_data['dss.semester_type_id'],
                        'student_semester_status' => 'inactive',
                        'date_added' => date('Y-m-d H:i:s')
                    );

                    $this->Smm->save_student_semester($a_student_semester_data);
                }
            }
        }
    }

    public function show_krs_filter_data()
    {
        if ($this->input->is_ajax_request()) {
            $a_filter_data = $this->input->post('filter_data');
            if (count($a_filter_data) > 0) {
                foreach ($a_filter_data as $key => $value) {
                    if ($value == '') {
                        print json_encode(array('code' => 1, 'message' => 'Please select form filter field'));
                        exit;
                    }
                }
                $mbo_student_krs_lists = $this->Krsm->get_student_lists(false, $a_filter_data);
                $a_return = array('code' => 0, 'data' => $mbo_student_krs_lists);
                var_dump($mbo_student_krs_lists);
            }else{
                $a_return = array('code' => 1, 'message' => 'Please input filter field');
            }
            
            print json_encode($a_return);
        }
    }

    public function save_registration_study_plan()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_academic_year_selected = $this->input->post('academic_year_id');
            $s_semester_type_selected = $this->input->post('semester_type_id');
            
            $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
            $o_semester_active = $this->Smm->get_active_semester();
            $a_data = $this->input->post('data');
            
            $s_login_type = $this->session->userdata('type');

            if ($s_semester_type_selected == 7) {
                $s_semester_type_parent = 1;
            }else if ($s_semester_type_selected == 8) {
                $s_semester_type_parent = 2;
            }else{
                $s_semester_type_parent = $s_semester_type_selected;
            }
            
            if ($s_login_type == 'student') {
                if (in_array($s_semester_type_selected, array(7, 8))) {
                    $s_field_semester = 'study_plan_short_semester';
                }else{
                    $s_field_semester = 'study_plan';
                }

                $b_cheker_period = modules::run('academic/semester/checker_semester_academic', $s_field_semester.'_start_date', $s_field_semester.'_end_date', $s_academic_year_selected, $s_semester_type_parent);
            }else {
                if (in_array($s_semester_type_selected, array(7, 8))) {
                    $s_field_semester = 'study_plan_approval_short_semester';
                }else{
                    $s_field_semester = 'study_plan_approval';
                }
                
                $b_cheker_period = modules::run('academic/semester/checker_semester_academic', null, $s_field_semester.'_end_date', $s_academic_year_selected, $s_semester_type_parent);
                // var_dump($b_cheker_period);exit;
            }

            if ($b_cheker_period) {
                if ($mbo_student_data) {
                    $this->db->trans_start();
                    $mba_student_krs_registration_data = $this->Scm->get_score_data(array(
                        'sc.student_id' => $s_student_id,
                        'sc.academic_year_id' => $s_academic_year_selected,
                        'sc.semester_type_id' => $s_semester_type_selected
                    ));

                    if ($mba_student_krs_registration_data) {
                        foreach ($mba_student_krs_registration_data as $student_krs) {
                            if ($s_login_type == 'student') {
                                $this->Scm->delete_data($student_krs->score_id);
                            }else {
                                $this->Scm->save_data(array('score_approval' => 'rejected'), array('score_id' => $student_krs->score_id));
                            }
                        }
                    }

                    if ((is_array($a_data)) AND (count($a_data) > 0)) {
                        $saved = true;
                        $a_subject_approve = [];
                        $semester_id = $this->parsing_semester_type($s_student_id, $s_academic_year_selected, $s_semester_type_selected);
                        if (is_null($semester_id)) {
                            $this->email->from('employee@company.ac.id');
                            $this->email->to(array('employee@company.ac.id'));
                            $this->email->subject('ERROR Create Semester on registration');
                            $this->email->message('student_id:'.$s_student_id.'<br>semester:'.$s_academic_year_selected.$s_semester_type_selected.'<br><br><p>KRS DATA</p>'.json_encode($mba_student_krs_registration_data));
                            $this->email->send();
                        }
                        $mbo_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $semester_id])[0];

                        foreach ($a_data as $krs) {
                            // if (in_array($s_semester_type_selected, array(7, 8))) {
                            //     $semester_id = $this->parsing_semester($mbo_student_data->academic_year_id, $s_semester_type_selected);
                            // }else{
                            //     $semester_id = $this->parsing_semester($mbo_student_data->academic_year_id);
                            // }

                            $mbo_class_data = $this->Cgm->get_class_group_subject(false, array('cgs.offered_subject_id' => $krs['offered_subject_id']))[0];
                            $mbo_class_master_data = $this->Cgm->get_class_id_master_class(array('class_group_id' => $mbo_class_data->class_group_id))[0];

                            $a_score_data = array(
                                'student_id' => $s_student_id,
                                'curriculum_subject_id' => $krs['curriculum_subject_id'],
                                'class_group_id' => $mbo_class_data->class_group_id,
                                'class_master_id' => ($mbo_class_master_data) ? $mbo_class_master_data->class_master_id : NULL,
                                'semester_id' => $semester_id,
                                'semester_type_id' => $s_semester_type_selected,
                                'academic_year_id' => $s_academic_year_selected,
                                'score_approval' => ($s_login_type == 'student') ? 'pending' : 'approved',
                                'score_display' => 'TRUE'
                            );

                            if ($s_login_type == 'student') {
                                if (in_array($s_semester_type_selected, array(7, 8))) {
                                    $a_score_data['score_approval'] = 'approved';
                                }
                                $a_score_data['score_id'] = $this->uuid->v4();
                                $a_score_data['date_added'] = date('Y-m-d H:i:s');
                                $save_registration = $this->Scm->save_data($a_score_data);
                            }else{
                                $mbo_student_krs_registration_data = $this->Scm->get_score_data(array(
                                    'sc.student_id' => $s_student_id,
                                    'sc.academic_year_id' => $s_academic_year_selected,
                                    'sc.semester_type_id' => $s_semester_type_selected,
                                    'sc.curriculum_subject_id' => $krs['curriculum_subject_id']
                                ))[0];

                                if ($mbo_student_krs_registration_data) {
                                    // $save_registration = $this->Scm->save_data($a_score_data, array('score_id' => $mbo_student_krs_registration_data->score_id));
                                    $save_registration = $this->Scm->save_data(array(
                                        'score_approval' => 'approved'
                                    ), array('score_id' => $mbo_student_krs_registration_data->score_id));
                                }else {
                                    $save_registration = $this->Scm->save_data($a_score_data);
                                }

                                if (($mbo_semester_data) AND ($mbo_semester_data->semester_number > 8)) {
                                    array_push($a_subject_approve, $krs);
                                }
                            }

                            if (!$save_registration) {
                                $saved = false;
                            }
                        }

                        $a_student_semeter_update = array(
                            'student_semester_status' => 'active'
                        );

                        $a_student_semester_update_clause = array(
                            'student_id' => $s_student_id,
                            'semester_type_id' => $s_semester_type_selected,
                            'academic_year_id' => $s_academic_year_selected
                        );

                        if ($saved) {
                            $save = $this->Smm->save_student_semester($a_student_semeter_update, $a_student_semester_update_clause);

                            if (in_array($s_semester_type_selected, array(7, 8))) {
                                
                                $a_data = array(
                                    'student_id' => $mbo_student_data->student_id,
                                    'academic_year_id' => $s_academic_year_selected,
                                    'semester_type_id' => $s_semester_type_selected
                                );

                                if ($s_login_type != 'student') {
                                    $s_action = 'approval';
                                }else if ($s_login_type == 'student') {
                                    $s_action = 'registration';
                                }

                                if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                                    $this->send_mail_short_semester($a_data, $s_action);
                                }
                            }else if (($mbo_semester_data) AND ($mbo_semester_data->semester_number > 8) AND (count($a_subject_approve) > 0)) {
                                $mba_userdata = $this->General->get_where('dt_personal_data', ['personal_data_id' => $this->session->userdata('user')]);
                                $s_body = $mba_userdata[0]->personal_data_name.' approve KRS '.$mbo_student_data->personal_data_name.' '.$s_academic_year_selected.'-'.$s_semester_type_selected;
                                modules::run('messaging/send_email', 'employee@company.ac.id', 'KRS Approved', $s_body, 'employee@company.ac.id', false, false, '[Notification]');
                            }

                            // var_dump($save);exit;
                        }else {
                            $this->db->trans_rollback();
                            $a_rtn = array('code' => 1, 'message' => 'Error saving registration');
                            print(json_encode($a_rtn));
                        }

                    }
                    // else{
                    //     $a_rtn = array('code' => 1, 'message' => 'data is null');
                    // }

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $a_rtn = array('code' => 999, 'message' => 'Unknow error proccessing data');
                    }else{
                        $this->db->trans_commit();
                        $a_rtn = array('code' => 0, 'message' => 'success');
                    }
                }else {
                    $a_rtn = array('code' => 1, 'message' => 'student data not found');
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => 'Period is over!');
            }

            print json_encode($a_rtn);
        }
    }

    public function send_mail_short_semester($a_data, $s_action)
    {
        // $a_data = array(
        //     'student_id' => '1a5937e4-1371-45f9-9690-adf5446b1317',
        //     'academic_year_id' => 2019,
        //     'semester_type_id' => 8
        // );

        // $s_action = 'approval';

        $mbo_student_data = $this->Stm->get_student_by_id($a_data['student_id']);
        $mbo_student_family = $this->Fmm->get_family_by_personal_data_id($mbo_student_data->personal_data_id);

        $mba_score_data = $this->Scm->get_score_data(array(
            'sc.student_id' => $a_data['student_id'],
            'sc.academic_year_id' => $a_data['academic_year_id'],
            'sc.semester_type_id' => $a_data['semester_type_id']
        ));

        $a_parent_email = array();

        if ($mbo_student_family) {
            $mba_parent_data = $this->Fmm->get_family_lists_filtered(array(
                'fmm.family_id' => $mbo_student_family->family_id,
                'fmm.family_member_status != ' => 'child'
            ));

            if ($mba_parent_data) {
                foreach ($mba_parent_data as $o_parents) {
                    if (!in_array($o_parents->personal_data_email, $a_parent_email)) {
                        array_push($a_parent_email, $o_parents->personal_data_email);
                    }
                }
            }
        }

        if ($mba_score_data) {
            $s_subject_approved = '';
            $s_subject_registered = '';
            $i_count_subject_approved = 0;
            $i_count_subject_registered = count($mba_score_data);

            $i_num = 1;
            foreach ($mba_score_data as $o_score) {
                $s_subject_registered .= $i_num.". ".$o_score->subject_name."\n";

                if ($o_score->score_approval == 'approved') {
                    $i_count_subject_approved++;
                    $s_subject_approved .= $i_num.". ".$o_score->subject_name."\n";
                }
                $i_num++;
            }

            $email_approval = <<<TEXT
Dear {$mbo_student_data->personal_data_name},

This email is to confirm your short semester registration. Your Dean has approved the following {$i_count_subject_approved} subject(s) out of the {$i_count_subject_registered} subject(s) you have registered:

{$s_subject_approved}

Thank you for your registration.

Academic Services Centre
International University Liaison Indonesia - IULI.
TEXT;

            $email_registration = <<<TEXT
Dear {$mbo_student_data->personal_data_name},

You have registered the following subject/s:

{$s_subject_registered}

Please wait for the evaluation from the Head of Study Program to confirm your registration & the decision to run the courses in this short semester.
We will inform you about this by email. Please check your email accordingly.

After that, please do confirmation for your registration of short semester status on your student portal http://student.iuli.ac.id

Make sure that you confirm the registration status to get a virtual account number for payment and that you are registered in the short semester.


Academic Services Centre
International University Liaison Indonesia - IULI.
TEXT;
        // $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
            $email_to = $mbo_student_data->student_email;
            // $email_to = 'employee@company.ac.id';
            $this->email->from('employee@company.ac.id', 'IULI Academic Services Centre');
            $this->email->to($email_to);
            
            if (count($a_parent_email) > 0) {
                $this->email->cc($a_parent_email);
            }
            
            $this->email->subject("Confirmation of Subject Registration for Short Semester");

            if ($s_action == 'approval') {
                $this->email->message($email_approval);
            }else{
                $this->email->message($email_registration);
            }
            
            if(!$this->email->send()){
                $this->log_activity('Email did not sent');
                $this->log_activity('Error Message: '.$this->email->print_debugger());
                
                $a_return = array('code' => 1, 'message' => 'Email not send to '.$email_to.' !');
                // print json_encode($a_return);exit;
            }else{
                $a_return = array('code' => 0, 'message' => 'Success');
            }
        }else{
            $a_return = array('code' => 1, 'message' => 'No subjects registered!');
        }

        return $a_return;

        // print('<pre>');
        // var_dump($a_return);
    }

    public function parsing_semester($batch, $short_semester = false)
    {
        $semester_active_data = $this->Smm->get_active_semester();
        if ($semester_active_data) {
            if ($short_semester) {
                $semester_active_data->semester_type_id = 3;
            }
            
            $semester = (intval($semester_active_data->academic_year_id) - intval($batch)) * 2;
            // $semester = ($semester < 0) ? 1 : $semester;
            
            if ($semester < 0) {
                $semester = 1;
                return $semester;
            }
            switch ($semester_active_data->semester_type_id) {
                case '1':
                    $semester += 1;
                    break;

                case '2':
                    $semester += 2;
                    break;

                case '3':
                    $mba_semester_before = $this->Smm->get_semester_setting(array('semester_end_date <' => $semester_active_data->semester_start_date), 'semester_end_date', 'DESC')[0];
                    if (!$mba_semester_before) {
                        $semester_number = $semester + 0.5;
                    }else if ($mba_semester_before->semester_type_id == '1') {
                        $semester_number = $semester + 1.5;
                    }else if ($mba_semester_before->semester_type_id == '2') {
                        $semester_number = $semester + 2.5;
                    }
                    
                    $semester_id = $this->Smm->get_semester_lists(array('semester_number' => $semester_number))[0];
                    $semester = $semester_id->semester_id;
                    break;
                
                case '5':
                    $semester_id = $this->Smm->get_semester_lists(array('semester_number' => 'TRCR'))[0];
                    $semester = $semester_id->semester_id;
                    break;

                case '4':
                    $semester_id = $this->Smm->get_semester_lists(array('semester_number' => 'OFSE'))[0];
                    $semester = $semester_id->semester_id;
                    break;

                case '6':
                    $semester_id = $this->Smm->get_semester_lists(array('semester_number' => 'OFSE'))[0];
                    $semester = $semester_id->semester_id;
                    break;

                default:
                    $semester = 1;
                    break;
            }
            
            return $semester;
        }else{
            return false;
        }
    }

    public function parsing_semester_type($s_student_id, $s_academic_year_id, $s_semester_type_id)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);

        $mba_last_score = $this->Scm->get_last_score_semester($s_student_id);
        $mbo_last_score = false;

        if ($s_semester_type_id == 7) {
            $s_semester_type_selected = 1;

        }else if ($s_semester_type_id == 8) {
            $s_semester_type_selected = 2;

        }else{
            $s_semester_type_selected = $s_semester_type_id;

        }

        if ($mba_last_score) {
            $i_temp_down = count($mba_last_score) - 1;
            foreach ($mba_last_score as $key => $o_last_score) {
                if (($o_last_score->academic_year_semester == $s_academic_year_id) AND ($o_last_score->semester_type_semester == $s_semester_type_selected)) {
                    $i_temp = $key + 1;

                    if ($i_temp >= count($mba_last_score)) {
                        $i_temp = count($mba_last_score) - 1;
                    }

                    $mbo_last_score = $mba_last_score[$i_temp];
                    break;
                }else{
                    $mbo_last_score = $mba_last_score[$i_temp_down];
                }

                $i_temp_down--;
            }
        }

        // print('<pre>');
        // var_dump($mbo_last_score);exit;

        if (!$mbo_last_score) {
            $i_current_semester = 1;
        }else{
            $mba_academic_semester_list = $this->Smm->get_semester_setting();
            
            $index_semester_active = 0;
            $index_last_semester = 0;

            foreach ($mba_academic_semester_list as $key => $o_semester_setting) {
                if (($o_semester_setting->academic_year_id == $s_academic_year_id) AND ($o_semester_setting->semester_type_id == $s_semester_type_selected)) {
                    $index_semester_active = $key;
                }

                if (($o_semester_setting->academic_year_id == $mbo_last_score->academic_year_semester) AND ($o_semester_setting->semester_type_id == $mbo_last_score->semester_type_semester)) {
                    $index_last_semester = $key;
                }
            }

            $i_diff_semester = intval($index_last_semester) - intval($index_semester_active);
            $i_current_semester = intval($mbo_last_score->semester_id) + $i_diff_semester;
            $mba_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $i_current_semester]);
            $i_current_semester = ($mba_semester_data) ? $mba_semester_data[0]->semester_number : $i_current_semester;
        }

        switch ($s_semester_type_id) {
            case '3':
                $i_current_semester = intval($i_current_semester) + 0.5;
                break;

            case '5':
                $i_current_semester = 'TRCR';
                break;

            case '4':
                $i_current_semester = 'OFSE';
                break;

            case '6':
                $i_current_semester = 'OFSE';
                break;
            
            case '7':
                $i_current_semester = intval($i_current_semester) + 0.5;
                break;

            case '8':
                $i_current_semester = intval($i_current_semester) + 0.5;
                break;

            default:
                break;
        }

        $mba_semester_data = $this->Smm->get_semester_lists(array(
            'semester_number' => $i_current_semester
        ));

        $s_semester_id = ($mba_semester_data) ? $mba_semester_data[0]->semester_id : NULL;
        return $s_semester_id;
    
    }
}
