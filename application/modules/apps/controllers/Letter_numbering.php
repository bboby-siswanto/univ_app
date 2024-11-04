<?php
class Letter_numbering extends App_core
{
    public $b_start_month = '9';
    function __construct()
    {
        parent::__construct('letter_numbering');
        $this->load->model('apps/Letter_numbering_model', 'Lnm');
        $this->load->model('employee/Employee_model', 'Emm');
        $this->load->model('Study_program_model', 'Spm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('academic/Academic_year_model', 'Aym');
        $this->load->model('thesis/Thesis_model', 'Tsm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Class_group_model', 'Cgm');
        $this->load->model('academic/Score_model', 'Scm');
    }

    public function update_detailtemp_17()
    {
        if ($this->input->is_ajax_request()) {
            // print('<pre>');var_dump($this->input->post());exit;
            $s_employee_id = $this->session->userdata('employee_id');
            $s_letter_description = $this->input->post('description_letter_update');
            $s_letter_number_id = $this->input->post('letter_number_id_description_update');
            $s_template_id = $this->input->post('template_id');

            $mba_employee_id = (!empty($this->input->post('employee_id'))) ? $this->input->post('employee_id') : false;
            $mba_student_id = (!empty($this->input->post('student_id'))) ? $this->input->post('student_id') : false;

            $mba_letter_data = $this->General->get_where('dt_letter_number', ['letter_number_id' => $s_letter_number_id]);
            if (!$mba_letter_data) {
                $a_return = ['code' => 2, 'message' => 'Data not found!'];
            }
            else {
                $department_data = $this->General->get_where('ref_department', ['department_id' => $mba_letter_data[0]->department_id]);
                $mba_letter_generated = $this->Lnm->get_personal_document([
                    'pdc.letter_number_id' => $s_letter_number_id,
                    'pdc.personal_data_id_generated' => $mba_letter_data[0]->personal_data_id
                ]);
    
                $mba_letter_target_list = $this->Lnm->get_letter_target([
                    'lnt.letter_number_id' => $s_letter_number_id,
                    'lnt.template_id' => $s_template_id
                ]);

                $a_data_newupdate = ['letter_description' => $s_letter_description];
                $this->Lnm->save_new_number($a_data_newupdate, ['letter_number_id' => $s_letter_number_id]);
    
                if ($mba_letter_target_list) {
                    $this->Lnm->remove_letter_target($s_letter_number_id, $s_template_id);
                }
    
                $a_return = $this->generate_assignment_letter_community_personal($s_employee_id, false, false, false, $s_letter_number_id, $department_data[0]->department_abbreviation, $s_letter_description, $mba_employee_id, $mba_student_id);
                if ($a_return['code'] == 0) {
                    // print('exit');exit;
                    if (($mba_employee_id) AND (is_array($mba_employee_id))) {
                        foreach ($mba_employee_id as $s_employee_id) {
                            $mba_employee_data = $this->General->get_where('dt_employee', ['employee_id' => $s_employee_id]);
                            if ($mba_employee_data) {
                                $this->Lnm->save_letter_number_target([
                                    'letter_number_id' => $s_letter_number_id,
                                    'personal_data_id' => $mba_employee_data[0]->personal_data_id,
                                    'template_id' => $s_template_id,
                                    'target_type' => 'lecturer',
                                    'date_added' => date('Y-m-d H:i:s')
                                ]);
                                $this->Lnm->submit_letter_number([
                                    'personal_document_id' => $this->uuid->v4(),
                                    'personal_data_id_generated' => $this->session->userdata('user'),
                                    'personal_data_id_target' => $mba_employee_data[0]->personal_data_id,
                                    'letter_number_id' => $s_letter_number_id,
                                    'document_token' => $a_return['doc_passcode'],
                                    'document_link' => urldecode($a_return['file']),
                                    'date_added' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    }
                    if (($mba_student_id) AND (is_array($mba_student_id))) {
                        foreach ($mba_student_id as $s_student_id) {
                            $mba_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id]);
                            if ($mba_student_data) {
                                $this->Lnm->save_letter_number_target([
                                    'letter_number_id' => $s_letter_number_id,
                                    'personal_data_id' => $mba_student_data[0]->personal_data_id,
                                    'template_id' => $s_template_id,
                                    'target_type' => 'student',
                                    'date_added' => date('Y-m-d H:i:s')
                                ]);
                                $this->Lnm->submit_letter_number([
                                    'personal_document_id' => $this->uuid->v4(),
                                    'personal_data_id_generated' => $this->session->userdata('user'),
                                    'personal_data_id_target' => $mba_student_data[0]->personal_data_id,
                                    'letter_number_id' => $s_letter_number_id,
                                    'document_token' => $a_return['doc_passcode'],
                                    'document_link' => urldecode($a_return['file']),
                                    'date_added' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    }
                }
            }

            print json_encode($a_return);exit;
        }
    }

    function test_character() {
        $s_description = 'IULI';
        print(strlen($s_description));exit;
    }

    public function update_description()
    {
        if ($this->input->is_ajax_request()) {
            $s_description = $this->input->post('desc');
            $s_letter_number_id = $this->input->post('letter_number_id');

            if (strlen($s_description) < 5) {
                print json_encode(['code' => 1, 'message' => 'Please enter 5 or more character']);
            }
            else {
                $a_data_update = ['letter_description' => $s_description];
                $this->Lnm->save_new_number($a_data_update, ['letter_number_id' => $s_letter_number_id]);
                print json_encode(['code' => 0]);
            }
        }
    }

    public function check_assignment_letter_community_dahsboard() {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_employee_id = $this->session->userdata('employee_id');
            $s_semester = intval($s_academic_year_id.$s_semester_type_id);
            $s_current_semester = intval($this->session->userdata('academic_year_id_active').$this->session->userdata('semester_type_id_active'));

            $mba_letter_data = $this->Lnm->get_letter_target([
                'lnt.personal_data_id' => $this->session->userdata('user'),
                'ln.academic_year_id' => $s_academic_year_id,
                'ln.semester_type_id' => $s_semester_type_id,
                'lnt.template_id' => '17'
            ]);

            if (!$mba_letter_data) {
                print json_encode(['code' => 88, 'message' => 'Not found!!']);exit;
            }

            $s_letter_number_id = $mba_letter_data[0]->letter_number_id;
            if ($s_semester == $s_current_semester) {
                $a_return = $this->generate_assignment_letter_community_personal(
                    $s_employee_id,
                    false,
                    false,
                    false,
                    $s_letter_number_id,
                    'LPPPM',
                    false
                );
            }
            else if ($s_semester != $s_current_semester) {
                if (in_array($s_semester_type_id, [1,2,7,8])) {
                    $s_semester_type_selected = (in_array($s_semester_type_id, [1,7])) ? 1 : 2;
                    $mba_semester_data = $this->General->get_where('dt_semester_settings', ['academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_selected]);

                    if ($mba_semester_data) {
                        $s_semester_date = (in_array($s_semester_type_id, [7,8])) ? $mba_semester_data[0]->offer_subject_end_date : $mba_semester_data[0]->semester_start_date;
                        if ($s_semester == '20211') {
                            $s_semester_date = '2021-09-04 14:53:29';
                        }
                        
                        $a_return = $this->generate_assignment_letter_community_personal(
                            $s_employee_id,
                            $s_semester_date,
                            $s_academic_year_id,
                            $s_semester_type_id,
                            $s_letter_number_id,
                            'LPPPM',
                            false
                        );
                    }
                    else {
                        $a_return = ['code' => 2, 'message' => 'Semester selected data not found!'];
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Semester selected is invalid to generate this action!'];
                }
            }
            else {
                $a_return = ['code' => 3, 'message' => 'Semester '.$s_semester.' is invalid to generate this action'];
            }

            print json_encode($a_return);
        }
    }

    public function get_assigment_letter_community_dashboard()
    {
        // if ($this->session->userdata('user') != '1d202529-a1c2-47ca-b010-fa829be2ec53') {
        //     $a_return = ['code' => '2', 'message' => 'this function under maintenance!!', 'data' => $this->input->post()];
        //     print json_encode($a_return);exit;
        // }
        
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_employee_id = $this->session->userdata('employee_id');
            $s_semester = intval($s_academic_year_id.$s_semester_type_id);
            $mba_employee_id = (!empty($this->input->post('employee_id'))) ? $this->input->post('employee_id') : false;
            $mba_student_id = (!empty($this->input->post('student_id'))) ? $this->input->post('student_id') : false;
            $s_current_semester = intval($this->session->userdata('academic_year_id_active').$this->session->userdata('semester_type_id_active'));

            // if ($s_semester > 20211) {
                $mba_letter_data = $this->Lnm->get_letter_target([
                    'lnt.personal_data_id' => $this->session->userdata('user'),
                    'ln.academic_year_id' => $s_academic_year_id,
                    'ln.semester_type_id' => $s_semester_type_id,
                    'lnt.template_id' => '17'
                ]);
                // print('<pre>');var_dump($mba_letter_data);exit;

                $s_letter_number_id = false;
                if ($mba_letter_data) {
                    $s_letter_number_id = $mba_letter_data[0]->letter_number_id;
                    // $mba_personal_document_data = $this->General->get_where('dt_personal_document', ['letter_number_id' => $mba_letter_data[0]->letter_number_id]);
                    // if ($mba_personal_document_data) {
                    //     $a_return = ['code' => 0, 'file' => urlencode($mba_personal_document_data[0]->document_link), 'doc_key' => $mba_personal_document_data[0]->personal_document_id];
                    // }
                    // else {
                    //     $a_return = ['code' => 1, 'message' => 'Document created, but file is missing!'];
                    // }
                }

                if ($s_semester == $s_current_semester) {
                    $a_return = $this->generate_assignment_letter_community_personal(
                        $s_employee_id,
                        false,
                        false,
                        false,
                        $s_letter_number_id,
                        'LPPPM',
                        false,
                        $mba_employee_id,
                        $mba_student_id
                    );
                    // $a_return = ['code' => 0];
                }
                else if ($s_semester != $s_current_semester) {
                    if (in_array($s_semester_type_id, [1,2,7,8])) {
                        $s_semester_type_selected = (in_array($s_semester_type_id, [1,7])) ? 1 : 2;
                        $mba_semester_data = $this->General->get_where('dt_semester_settings', ['academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_selected]);

                        if ($mba_semester_data) {
                            $s_semester_date = (in_array($s_semester_type_id, [7,8])) ? $mba_semester_data[0]->offer_subject_end_date : $mba_semester_data[0]->semester_start_date;
                            if ($s_semester == '20211') {
                                $s_semester_date = '2021-09-04 14:53:29';
                            }
                            
                            $a_return = $this->generate_assignment_letter_community_personal(
                                $s_employee_id,
                                $s_semester_date,
                                $s_academic_year_id,
                                $s_semester_type_id,
                                $s_letter_number_id,
                                'LPPPM',
                                false,
                                $mba_employee_id,
                                $mba_student_id
                            );
                        }
                        else {
                            $a_return = ['code' => 2, 'message' => 'Semester selected data not found!'];
                        }
                    }
                    else {
                        $a_return = ['code' => 2, 'message' => 'Semester selected is invalid to generate this action!'];
                    }
                }
                else {
                    $a_return = ['code' => 3, 'message' => 'Semester '.$s_semester.' is invalid to generate this action'];
                }
            // }

            print json_encode($a_return);
        }
    }

    public function get_assignment_letter_community()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('academic_semester_key', 'Academic Semester Year', 'required|trim');
            $this->form_validation->set_rules('semester_type_key', 'Academic Semester Type', 'required|trim');
            $this->form_validation->set_rules('template_key', 'File Template', 'required|trim');
            $this->form_validation->set_rules('department', 'Department', 'required|trim');
            $this->form_validation->set_rules('description', 'Description Letter', 'required|trim');
            
            if ($this->input->post('backdated_switch') !== null) {
                $this->form_validation->set_rules('backdate', 'Back Date', 'required|trim');
            }
            
            if ($this->form_validation->run() === false) {
                $a_return = ['code' => 1, 'message' => validation_errors('<li>', '</li>')];
            }
            else {
                if ((!empty($this->input->post('employee_id'))) OR (!empty($this->input->post('student_id')))) {
                    $mba_employee_id = (!empty($this->input->post('employee_id'))) ? $this->input->post('employee_id') : false;
                    $mba_student_id = (!empty($this->input->post('student_id'))) ? $this->input->post('student_id') : false;
                    $mba_department = $this->General->get_where('ref_department', ['department_id' => set_value('department')]);

                    $mba_employee_data = $this->General->get_where('dt_employee', ['employee_id' => $this->session->userdata('employee_id')]);
                    $mba_letter_data = $this->Lnm->get_letter_target([
                        'lnt.personal_data_id' => $mba_employee_data[0]->personal_data_id,
                        'ln.academic_year_id' => set_value('academic_semester_key'),
                        'ln.semester_type_id' => set_value('semester_type_key'),
                        'lnt.template_id' => '17'
                    ]);

                    $s_letter_number_id = false;
                    if ($mba_letter_data) {
                        $s_letter_number_id = $mba_letter_data[0]->letter_number_id;
                    }

                    $s_backdated = false;
                    if ($this->input->post('backdated_switch') !== null) {
                        $s_backdated = set_value('backdate');
                    }

                    $a_return = $this->generate_assignment_letter_community_personal(
                        $this->session->userdata('employee_id'),
                        $s_backdated,
                        set_value('academic_semester_key'),
                        set_value('semester_type_key'),
                        $s_letter_number_id,
                        $mba_department[0]->department_abbreviation,
                        set_value('description'),
                        $mba_employee_id,
                        $mba_student_id
                    );
                    if ($a_return['code'] == 0) {
                        // 
                    }
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'Please fill assign to list!'];
                }
            }

            print json_encode($a_return);
        }
    }

    public function get_assigment_letter_research_dashboard()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_employee_id = $this->session->userdata('employee_id');
            $s_semester = intval($s_academic_year_id.$s_semester_type_id);
            $s_current_semester = intval($this->session->userdata('academic_year_id_active').$this->session->userdata('semester_type_id_active'));

            // if ($s_semester > 20211) {
                $mba_letter_data = $this->Lnm->get_letter_target([
                    'lnt.personal_data_id' => $this->session->userdata('user'),
                    'ln.academic_year_id' => $s_academic_year_id,
                    'ln.semester_type_id' => $s_semester_type_id,
                    'lnt.template_id' => '21'
                ]);

                if ($this->session->userdata('user') == '511f9070-9e7b-4326-b053-e70e0ec67f08') {
                    // print('<pre>');var_dump($mba_letter_data);exit;
                }

                $s_letter_number_id = false;
                if ($mba_letter_data) {
                    $s_letter_number_id = $mba_letter_data[0]->letter_number_id;
                    // $mba_personal_document_data = $this->General->get_where('dt_personal_document', ['letter_number_id' => $mba_letter_data[0]->letter_number_id]);
                    // if ($mba_personal_document_data) {
                    //     $a_return = ['code' => 0, 'file' => urlencode($mba_personal_document_data[0]->document_link), 'doc_key' => $mba_personal_document_data[0]->personal_document_id];
                    // }
                    // else {
                    //     $a_return = ['code' => 1, 'message' => 'Document created, but file is missing!'];
                    // }
                }


                if ($s_semester == $s_current_semester) {
                    $this->generate_assignment_letter_research_personal($s_employee_id, false, false, false, $s_letter_number_id);
                }
                else if ($s_semester != $s_current_semester) {
                    if (in_array($s_semester_type_id, [1,2])) {
                        $s_semester_type_selected = (in_array($s_semester_type_id, [1,7])) ? 1 : 2;
                        $mba_semester_data = $this->General->get_where('dt_semester_settings', ['academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_selected]);

                        if ($mba_semester_data) {
                            $s_semester_date = (in_array($s_semester_type_id, [7,8])) ? $mba_semester_data[0]->offer_subject_end_date : $mba_semester_data[0]->semester_start_date;
                            if ($s_semester == '20211') {
                                $s_semester_date = '2021-09-04 14:53:29';
                            }
                            
                            $this->generate_assignment_letter_research_personal($s_employee_id, $s_semester_date, $s_academic_year_id, $s_semester_type_id, $s_letter_number_id);
                            $a_return = ['code' => 0];
                        }
                        else {
                            $a_return = ['code' => 2, 'message' => 'Semester selected data not found!'];
                        }
                    }
                    else {
                        $a_return = ['code' => 2, 'message' => 'Semester selected is invalid to generate this action!'];
                    }
                }
                else {
                    $a_return = ['code' => 3, 'message' => 'Semester '.$s_semester.' is invalid to generate this action'];
                }
            // }

            print json_encode($a_return);
        }
    }

    public function get_assigment_letter_thesis_examiner_dashboard()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_employee_id = $this->session->userdata('employee_id');
            $s_semester = intval($s_academic_year_id.$s_semester_type_id);
            $s_current_semester = intval($this->session->userdata('academic_year_id_active').$this->session->userdata('semester_type_id_active'));
            $mba_is_examiner_semester = $this->Tsm->is_advisor_examiner_defense([
                'ta.personal_data_id' => $this->session->userdata('user'),
                'td.academic_year_id' => $s_academic_year_id,
                'td.semester_type_id' => $s_semester_type_id
            ], 'examiner');

            if ($mba_is_examiner_semester) {
                if ($s_semester > 20210) {
                    $mba_letter_data = $this->Lnm->get_letter_target([
                        'lnt.personal_data_id' => $this->session->userdata('user'),
                        'ln.academic_year_id' => $s_academic_year_id,
                        'ln.semester_type_id' => $s_semester_type_id,
                        'lnt.template_id' => '6'
                    ]);

                    $s_letter_number_id = false;
                    if ($mba_letter_data) {
                        $s_letter_number_id = $mba_letter_data[0]->letter_number_id;
                    }

                    if ($s_semester == $s_current_semester) {
                        $this->generate_assignment_letter_examiner_thesis($s_employee_id, false, false, false, false, $s_letter_number_id);
                    }
                    else if ($s_semester != $s_current_semester) {
                        if (in_array($s_semester_type_id, [1,2])) {
                            $mba_semester_data = $this->General->get_where('dt_semester_settings', ['academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id]);
                            if ($mba_semester_data) {
                                $s_semester_date = (in_array($s_semester_type_id, [7,8])) ? $mba_semester_data[0]->offer_subject_end_date : $mba_semester_data[0]->semester_start_date;
                                
                                $this->generate_assignment_letter_examiner_thesis($s_employee_id, $s_semester_date, $s_academic_year_id, $s_semester_type_id, false, $s_letter_number_id);
                                $a_return = ['code' => 0];
                            }
                            else {
                                $a_return = ['code' => 2, 'message' => 'Semester selected data not found!'];
                            }
                        }
                        else {
                            $a_return = ['code' => 2, 'message' => 'Semester selected is invalid to generate this action!'];
                        }
                    }
                    else {
                        $a_return = ['code' => 3, 'message' => 'Semester '.$s_semester.' is invalid to generate this action'];
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Semester selected is invalid to generate this action!'];
                }
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Advisor not found in semester selected!'];
            }

            print json_encode($a_return);
            // exit;
        }
    }

    function cek_advisor() {
        $s_employee_id = '365330c2-e11f-4c26-8a71-0fba4a92406f';
        $s_academic_year_id = '2022';
        $s_semester_type_id = '2';
        $a_student_idlisted = [];

        $mba_is_advisor_semester = $this->Tsm->get_list_student_advisor([
            'ta.personal_data_id' => '089a7b36-a1cd-4683-adcc-a2e8e8d5606d'
        ], 'advisor', true);
        if ($mba_is_advisor_semester) {
            $i_numb = 1;
            foreach ($mba_is_advisor_semester as $o_advisor) {
                $mba_thesis_log_data = $this->Tsm->get_thesis_log($o_advisor->thesis_student_id, [
                    'tsl.academic_year_id' => $s_academic_year_id,
                    'tsl.semester_type_id' => $s_semester_type_id,
                    'tsl.thesis_log_type' => 'work'
                ]);
                if ($mba_thesis_log_data) {
                    if (!in_array($o_advisor->student_id, $a_student_idlisted)) {
                        array_push($a_student_idlisted, $o_advisor->student_id);
                    }
                    $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $o_advisor->student_id]);
                    print($i_numb++.'. ');
                    if ($mba_student_data) {
                        print($mba_student_data[0]->personal_data_name);
                    }
                    else {
                        print($o_advisor->student_id);
                    }

                    print('<br>');
                }
            }
        }
        print('<pre>');var_dump($a_student_idlisted);
    }

    public function get_assigment_letter_internship_advisor_dashboard()
    {
        if ($this->input->is_ajax_request()) {
            // if ($this->session->userdata('user') != '5a9e49ce-729a-4be2-98f4-723c6bf2b87a') {
            //     $a_return = ['code' => 2, 'message' => 'This feature under development'];
            //     print json_encode($a_return);exit;
            // }

            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_employee_id = $this->session->userdata('employee_id');
            $s_semester = intval($s_academic_year_id.$s_semester_type_id);
            $s_current_semester = intval($this->session->userdata('academic_year_id_active').$this->session->userdata('semester_type_id_active'));

            $a_student_id_push = [];
            $mba_student_internship = false;
            $mba_is_lect_internship = $this->Cgm->get_class_master_filtered([
                'cml.employee_id' => $this->session->userdata('employee_id'),
                'cm.academic_year_id' => $s_academic_year_id,
                'cm.semester_type_id' => $s_semester_type_id
            ]);
            if ($mba_is_lect_internship) {
                foreach ($mba_is_lect_internship as $o_class_master) {
                    if (strpos(strtolower($o_class_master->subject_name), 'internship') !== false) {
                        // $mba_score_data = $this->General->get_where('dt_score', [
                        //     'class_master_id' => $o_class_master->class_master_id,
                        //     'score_approval' => 'approved'
                        // ]);
                        $mba_score_data = $this->Scm->get_student_by_score(['sc.class_master_id' => $o_class_master->class_master_id, 'sc.score_approval' => 'approved']);
                        if ($mba_score_data) {
                            $mba_student_internship = [];
                            foreach ($mba_score_data as $o_score) {
                                if (!in_array($o_score->student_id, $a_student_id_push)) {
                                    array_push($a_student_id_push, $o_score->student_id);
                                    array_push($mba_student_internship, $o_score);
                                }
                            }
                            $mba_student_internship = array_values($mba_student_internship);
                        }
                    }
                }
            }

            if (($mba_student_internship) AND ($s_semester > 20210)) {
                $mba_letter_data = $this->Lnm->get_letter_target([
                    'lnt.personal_data_id' => $this->session->userdata('user'),
                    'ln.academic_year_id' => $s_academic_year_id,
                    'ln.semester_type_id' => $s_semester_type_id,
                    'lnt.template_id' => '27'
                ]);

                if ($mba_letter_data) {
                    $mba_personal_document_data = $this->General->get_where('dt_personal_document', ['letter_number_id' => $mba_letter_data[0]->letter_number_id]);
                    if ($mba_personal_document_data) {
                        $a_return = ['code' => 0, 'file' => urlencode($mba_personal_document_data[0]->document_link), 'doc_key' => $mba_personal_document_data[0]->personal_document_id];
                    }
                    else {
                        $a_return = ['code' => 1, 'message' => 'Document created, but file is missing!'];
                    }
                }
                else if ($s_semester == $s_current_semester) {
                    $this->generate_assignment_letter_advisor_internship($s_employee_id);
                }
                else if ($s_semester != $s_current_semester) {
                    if (in_array($s_semester_type_id, [1,2])) {
                        // $s_semester_type_selected = (in_array($s_semester_type_id, [1,7])) ? 1 : 2;
                        $mba_semester_data = $this->General->get_where('dt_semester_settings', ['academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id]);

                        if ($mba_semester_data) {
                            $s_semester_date = (in_array($s_semester_type_id, [7,8])) ? $mba_semester_data[0]->offer_subject_end_date : $mba_semester_data[0]->semester_start_date;
                            // if ($s_semester == '20211') {
                            //     $s_semester_date = '2021-09-04 14:53:29';
                            // }
                            
                            $this->generate_assignment_letter_advisor_internship($s_employee_id, $s_semester_date, $s_academic_year_id, $s_semester_type_id);
                            $a_return = ['code' => 0];
                        }
                        else {
                            $a_return = ['code' => 2, 'message' => 'Semester selected data not found!'];
                        }
                    }
                    else {
                        $a_return = ['code' => 2, 'message' => 'Semester selected is invalid to generate this action!'];
                    }
                }
                else {
                    $a_return = ['code' => 3, 'message' => 'Semester '.$s_semester.' is invalid to generate this action'];
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => 'No student internship found'];
            }

            // $a_return = ['code' => 2, 'message' => 'This feature under development', 'data' => $mba_student_internship];
            print json_encode($a_return);exit;
        }
    }

    public function get_assigment_letter_thesis_advisor_dashboard()
    {
        if ($this->input->is_ajax_request()) {
            // print json_encode(['code' => 1, 'message' => 'Function disabled!']);exit;
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_employee_id = $this->session->userdata('employee_id');
            $s_semester = intval($s_academic_year_id.$s_semester_type_id);
            $s_current_semester = intval($this->session->userdata('academic_year_id_active').$this->session->userdata('semester_type_id_active'));

            $mba_is_advisor_semester = $this->Tsm->get_list_student_advisor([
                'ta.personal_data_id' => $this->session->userdata('user')
            ], 'advisor', true);

            // $mba_is_advisor_semester = $this->Tsm->is_advisor_examiner_defense([
            //     'ta.personal_data_id' => $this->session->userdata('user'),
            //     'td.academic_year_id' => $s_academic_year_id,
            //     'td.semester_type_id' => $s_semester_type_id
            // ], 'advisor');

            if ($mba_is_advisor_semester) {
                $a_student_idlisted = [];
                foreach ($mba_is_advisor_semester as $o_advisor) {
                    $mba_thesis_log_data = $this->Tsm->get_thesis_log($o_advisor->thesis_student_id, [
                        'tsl.academic_year_id' => $s_academic_year_id,
                        'tsl.semester_type_id' => $s_semester_type_id,
                        'tsl.thesis_log_type' => 'work'
                    ]);
                    if ($mba_thesis_log_data) {
                        if (!in_array($o_advisor->student_id, $a_student_idlisted)) {
                            array_push($a_student_idlisted, $o_advisor->student_id);
                        }
                    }
                }

                if (count($a_student_idlisted) > 0) {
                    if ($s_semester > 20210) {
                        $mba_letter_data = $this->Lnm->get_letter_target([
                            'lnt.personal_data_id' => $this->session->userdata('user'),
                            'ln.academic_year_id' => $s_academic_year_id,
                            'ln.semester_type_id' => $s_semester_type_id,
                            'lnt.template_id' => '4'
                        ]);
        
                        if ($mba_letter_data) {
                            $mba_personal_document_data = $this->General->get_where('dt_personal_document', ['letter_number_id' => $mba_letter_data[0]->letter_number_id]);
                            if ($mba_personal_document_data) {
                                $a_return = ['code' => 0, 'file' => urlencode($mba_personal_document_data[0]->document_link), 'doc_key' => $mba_personal_document_data[0]->personal_document_id];
                            }
                            else {
                                $a_return = ['code' => 1, 'message' => 'Document created, but file is missing!'];
                            }
                        }
                        else if ($s_semester == $s_current_semester) {
                            $this->generate_assignment_letter_advisor_thesis($s_employee_id);
                        }
                        else if ($s_semester != $s_current_semester) {
                            if (in_array($s_semester_type_id, [1,2])) {
                                // $s_semester_type_selected = (in_array($s_semester_type_id, [1,7])) ? 1 : 2;
                                $mba_semester_data = $this->General->get_where('dt_semester_settings', ['academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id]);
        
                                if ($mba_semester_data) {
                                    $s_semester_date = (in_array($s_semester_type_id, [7,8])) ? $mba_semester_data[0]->offer_subject_end_date : $mba_semester_data[0]->semester_start_date;
                                    // if ($s_semester == '20211') {
                                    //     $s_semester_date = '2021-09-04 14:53:29';
                                    // }
                                    
                                    $this->generate_assignment_letter_advisor_thesis($s_employee_id, $s_semester_date, $s_academic_year_id, $s_semester_type_id);
                                    $a_return = ['code' => 0];
                                }
                                else {
                                    $a_return = ['code' => 2, 'message' => 'Semester selected data not found!'];
                                }
                            }
                            else {
                                $a_return = ['code' => 2, 'message' => 'Semester selected is invalid to generate this action!'];
                            }
                        }
                        else {
                            $a_return = ['code' => 3, 'message' => 'Semester '.$s_semester.' is invalid to generate this action'];
                        }
                    }else {
                        $a_return = ['code' => 2, 'message' => 'Semester selected is invalid to generate this action!'];
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Advisor not found in semester selected!'];
                }
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Advisor not found!'];
            }

            print json_encode($a_return);
        }
    }

    public function get_assigment_letter_dashboard()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_employee_id = $this->session->userdata('employee_id');
            $s_semester = intval($s_academic_year_id.$s_semester_type_id);
            $s_current_semester = intval($this->session->userdata('academic_year_id_active').$this->session->userdata('semester_type_id_active'));

            if ($s_semester > 20200) {
                $mba_letter_data = $this->Lnm->get_letter_target([
                    'lnt.personal_data_id' => $this->session->userdata('user'),
                    'ln.academic_year_id' => $s_academic_year_id,
                    'ln.semester_type_id' => $s_semester_type_id,
                    'lnt.template_id' => '7'
                ]);

                if ($mba_letter_data) {
                    $mba_personal_document_data = $this->General->get_where('dt_personal_document', ['letter_number_id' => $mba_letter_data[0]->letter_number_id]);
                    if ($mba_personal_document_data) {
                        $a_return = ['code' => 0, 'file' => urlencode($mba_personal_document_data[0]->document_link), 'doc_key' => $mba_personal_document_data[0]->personal_document_id];
                    }
                    else {
                        $a_return = ['code' => 1, 'message' => 'Document created, but file is missing!'];
                    }
                }
                else if ($s_semester == $s_current_semester) {
                    $this->generate_assignment_letter_personal($s_employee_id);
                }
                else if ($s_semester != $s_current_semester) {
                    if (in_array($s_semester_type_id, [1,2,7,8])) {
                        $s_semester_type_selected = (in_array($s_semester_type_id, [1,7])) ? 1 : 2;
                        $mba_semester_data = $this->General->get_where('dt_semester_settings', ['academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_selected]);

                        if ($mba_semester_data) {
                            $s_semester_date = (in_array($s_semester_type_id, [7,8])) ? $mba_semester_data[0]->offer_subject_end_date : $mba_semester_data[0]->semester_start_date;
                            if ($s_semester == '20211') {
                                $s_semester_date = '2021-09-04 14:53:29';
                            }
                            
                            $this->generate_assignment_letter_personal($s_employee_id, $s_semester_date, $s_academic_year_id, $s_semester_type_id);
                            $a_return = ['code' => 0];
                        }
                        else {
                            $a_return = ['code' => 2, 'message' => 'Semester selected data not found!'];
                        }
                    }
                    else {
                        $a_return = ['code' => 2, 'message' => 'Semester selected is invalid to generate this action!'];
                    }
                }
                else {
                    $a_return = ['code' => 3, 'message' => 'Semester '.$s_semester.' is invalid to generate this action'];
                }
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Semester selected is invalid to generate this action!'];
            }

            print json_encode($a_return);
        }
    }

    public function test_char()
    {
        $s_char = 'Z';
        // $s_next = chr(ord($s_char) + 1) ;
        $s_next = ++$s_char;
        print($s_next);
    }

    public function generate_reference_letter_employee($s_employee_id, $s_backdated = false, $s_post_academic_year_id = false, $s_post_semester_type_id = false)
    {
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $s_template_id = '22';
        $s_letter_type_id = '4';

        $mbo_employee_hr = $this->General->get_rectorate('human_resource');
        $mba_employee_data = $this->Emm->get_employee_data(['em.employee_id' => $s_employee_id]);

        if ($mbo_employee_hr) {
            if ($mba_employee_data) {
                $o_employee_data = $mba_employee_data[0];
                $mba_letter = $this->Lnm->get_template(false, [
                    'template_id' => $s_template_id
                ]);
                
                if (!$mba_letter) {
                    $a_return = ['code' => 1, 'message' => 'Template not found!'];
                }

                $s_academic_year_id = $this->session->userdata('academic_year_id_active');
                $s_semester_type_id = $this->session->userdata('semester_type_id_active');

                if (($s_post_academic_year_id) AND ($s_post_semester_type_id)) {
                    $s_academic_year_id = $s_post_academic_year_id;
                    $s_semester_type_id = $s_post_semester_type_id;
                }
    
                $s_new_number = $this->Lnm->get_new_number();
                $s_employee_name = $this->Pdm->retrieve_title($o_employee_data->personal_data_id);
                $s_new_char = NULL;
                $s_month = date('m');
                $s_year = date('Y');
                $s_letter_date = date('Y-m-d');
    
                if ($s_backdated) {
                    $s_month = date('m', strtotime($s_backdated));
                    $s_year = date('Y', strtotime($s_backdated));
                    $s_letter_date = $s_year.'-'.$s_month.'-01';
    
                    $s_new_char = $this->Lnm->get_new_number('char', $s_month, $s_year);
                    $mba_count_number = $this->General->get_where('dt_letter_number');
                    
                    if ($mba_count_number) {
                        $mba_count_number = $this->General->get_where('dt_letter_number', [
                            'letter_number != ' => NULL
                        ]);
    
                        if ($mba_count_number) {
                            $mba_count_number = $this->Lnm->get_list([
                                'ln.letter_month' => $s_month,
                                'ln.letter_year' => $s_year,
                                'ln.letter_number != ' => NULL
                            ]);
                            if ($mba_count_number) {
                                $s_new_number = $mba_count_number[0]->letter_number;
                            }
                            else {
                                $s_new_number = $this->Lnm->get_last_number($s_month, $s_year);
                            }
                        }
                        else {
                            $s_new_number = 520;
                        }
                    }
                    else {
                        $s_new_number = 520;
                    }
                }
    
                $s_result_number = $this->_format_number_letter(
                    $mbo_employee_hr->department_abbreviation,
                    $mba_letter[0]->letter_abbreviation,
                    $s_new_number,
                    $s_new_char,
                    $s_month,
                    $s_year
                );
    
                $a_letter_number_data = [
                    'personal_data_id' => $this->session->userdata('user'),
                    'letter_type_id' => $s_letter_type_id,
                    'department_id' => $mbo_employee_hr->department_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'letter_number_result' => $s_result_number,
                    'letter_number' => $s_new_number,
                    'letter_char' => $s_new_char,
                    'letter_month' => $s_month,
                    'letter_year' => $s_year,
                    'letter_description' => 'Refference Letter for '.$s_employee_name,
                    'letter_purpose' => $s_employee_name,
                    'letter_date' => $s_letter_date,
                    'date_added' => date('Y-m-d H:i:s')
                ];
    
                $mbs_letter_number_id = $this->Lnm->save_new_number($a_letter_number_data);
                if ($mbs_letter_number_id) {
                    // $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_abbreviation' => $o_employee_data->department_abbreviation]);
                    // if ($mba_study_program_data) {
                    //     if (!is_null($mba_study_program_data[0]->study_program_main_id)) {
                    //         $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mba_study_program_data[0]->study_program_main_id]);
                    //     }
                    // }
                    // else {
                    //     $this->db->trans_rollback();
                    //     $a_return = ['code' => 1, 'message' => 'Department not found!'];
                    // }
    
                    $a_letter_number_target = [
                        'letter_number_id' => $mbs_letter_number_id,
                        'personal_data_id' => $this->session->userdata('user'),
                        'template_id' => $s_template_id,
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Lnm->save_letter_number_target($a_letter_number_target);
                    
                    $a_data = [
                        'template_id' => $s_template_id,
                        'employee_id' => $o_employee_data->employee_id,
                        'letter_number' => $s_result_number,
                        // 'department_id' => $mba_study_program_data[0]->study_program_id,
                        'academic_year_id' => $s_academic_year_id,
                        'semester_type_id' => $s_semester_type_id,
                        'letter_date' => $s_letter_date,
                        'letter_year' => $s_year
                    ];
                    // generate file
                    $a_return = modules::run('download/doc_download/generate_refference_letter_employee', $a_data);
                    if ($a_return['code'] == 0) {
                        $s_personal_document_id = $this->uuid->v4();
                        $a_personal_document_data = [
                            'personal_document_id' => $s_personal_document_id,
                            'personal_data_id_generated' => $this->session->userdata('user'),
                            'personal_data_id_target' => $o_employee_data->personal_data_id,
                            'letter_number_id' => $mbs_letter_number_id,
                            'document_link' => urldecode($a_return['file'])
                        ];
    
                        $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
                        $this->Lnm->submit_letter_number($a_personal_document_data);
                        $a_return['doc_key'] = $s_personal_document_id;
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Error generate number!'];
                }
                // 
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Employee not found!'];
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'Head of Human Resource not found!'];
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);exit;
        }
        else {
            return $a_return;
        }
        // print('<pre>');var_dump($a_return);exit;
    }

    public function generate_lolos_butuh_employee($s_employee_id, $s_backdated = false, $s_post_academic_year_id = false, $s_post_semester_type_id = false)
    {
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $s_template_id = '23';
        $s_letter_type_id = '4';

        $mbo_employee_hr = $this->General->get_rectorate('human_resource');
        $mba_employee_data = $this->Emm->get_employee_data(['em.employee_id' => $s_employee_id]);

        if ($mbo_employee_hr) {
            if ($mba_employee_data) {
                $o_employee_data = $mba_employee_data[0];
                $mba_letter = $this->Lnm->get_template(false, [
                    'template_id' => $s_template_id
                ]);
                
                if (!$mba_letter) {
                    $a_return = ['code' => 1, 'message' => 'Template not found!'];
                }

                $s_academic_year_id = $this->session->userdata('academic_year_id_active');
                $s_semester_type_id = $this->session->userdata('semester_type_id_active');

                if (($s_post_academic_year_id) AND ($s_post_semester_type_id)) {
                    $s_academic_year_id = $s_post_academic_year_id;
                    $s_semester_type_id = $s_post_semester_type_id;
                }
    
                $s_new_number = $this->Lnm->get_new_number();
                $s_employee_name = $this->Pdm->retrieve_title($o_employee_data->personal_data_id);
                $s_new_char = NULL;
                $s_month = date('m');
                $s_year = date('Y');
                $s_letter_date = date('Y-m-d');
    
                if ($s_backdated) {
                    $s_month = date('m', strtotime($s_backdated));
                    $s_year = date('Y', strtotime($s_backdated));
                    $s_letter_date = $s_year.'-'.$s_month.'-01';
    
                    $s_new_char = $this->Lnm->get_new_number('char', $s_month, $s_year);
                    $mba_count_number = $this->General->get_where('dt_letter_number');
                    
                    if ($mba_count_number) {
                        $mba_count_number = $this->General->get_where('dt_letter_number', [
                            'letter_number != ' => NULL
                        ]);
    
                        if ($mba_count_number) {
                            $mba_count_number = $this->Lnm->get_list([
                                'ln.letter_month' => $s_month,
                                'ln.letter_year' => $s_year,
                                'ln.letter_number != ' => NULL
                            ]);
                            if ($mba_count_number) {
                                $s_new_number = $mba_count_number[0]->letter_number;
                            }
                            else {
                                $s_new_number = $this->Lnm->get_last_number($s_month, $s_year);
                            }
                        }
                        else {
                            $s_new_number = 520;
                        }
                    }
                    else {
                        $s_new_number = 520;
                    }
                }
    
                $s_result_number = $this->_format_number_letter(
                    $mbo_employee_hr->department_abbreviation,
                    $mba_letter[0]->letter_abbreviation,
                    $s_new_number,
                    $s_new_char,
                    $s_month,
                    $s_year
                );
    
                $a_letter_number_data = [
                    'personal_data_id' => $this->session->userdata('user'),
                    'letter_type_id' => $s_letter_type_id,
                    'department_id' => $mbo_employee_hr->department_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'letter_number_result' => $s_result_number,
                    'letter_number' => $s_new_number,
                    'letter_char' => $s_new_char,
                    'letter_month' => $s_month,
                    'letter_year' => $s_year,
                    'letter_description' => 'Surat Lolos Butuh',
                    'letter_purpose' => $s_employee_name,
                    'letter_date' => $s_letter_date,
                    'date_added' => date('Y-m-d H:i:s')
                ];
    
                $mbs_letter_number_id = $this->Lnm->save_new_number($a_letter_number_data);
                if ($mbs_letter_number_id) {
                    $a_letter_number_target = [
                        'letter_number_id' => $mbs_letter_number_id,
                        'personal_data_id' => $this->session->userdata('user'),
                        'template_id' => $s_template_id,
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Lnm->save_letter_number_target($a_letter_number_target);
                    
                    $a_data = [
                        'template_id' => $s_template_id,
                        'employee_id' => $o_employee_data->employee_id,
                        'letter_number' => $s_result_number,
                        // 'department_id' => $mba_study_program_data[0]->study_program_id,
                        'academic_year_id' => $s_academic_year_id,
                        'semester_type_id' => $s_semester_type_id,
                        'letter_date' => $s_letter_date,
                        'letter_year' => $s_year
                    ];
                    // generate file
                    $a_return = modules::run('download/doc_download/generate_lolos_butuh_letter', $a_data);
                    if ($a_return['code'] == 0) {
                        $a_return['data_send'] = $a_data;
                        $s_personal_document_id = $this->uuid->v4();
                        $a_personal_document_data = [
                            'personal_document_id' => $s_personal_document_id,
                            'personal_data_id_generated' => $this->session->userdata('user'),
                            'personal_data_id_target' => $o_employee_data->personal_data_id,
                            'letter_number_id' => $mbs_letter_number_id,
                            'document_link' => urldecode($a_return['file'])
                        ];
    
                        $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
                        $this->Lnm->submit_letter_number($a_personal_document_data);
                        $a_return['doc_key'] = $s_personal_document_id;
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Error generate number!'];
                }
                // 
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Employee not found!'];
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'Head of Human Resource not found!'];
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);exit;
        }
        else {
            return $a_return;
        }
        // print('<pre>');var_dump($a_return);exit;
    }

    public function generate_reference_letter_resign($s_employee_id, $s_backdated = false, $s_post_academic_year_id = false, $s_post_semester_type_id = false)
    {
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $s_template_id = '24';
        $s_letter_type_id = '4';

        $mbo_employee_hr = $this->General->get_rectorate('human_resource');
        $mba_employee_data = $this->Emm->get_employee_data(['em.employee_id' => $s_employee_id]);

        if ($mbo_employee_hr) {
            if ($mba_employee_data) {
                $o_employee_data = $mba_employee_data[0];
                $mba_letter = $this->Lnm->get_template(false, [
                    'template_id' => $s_template_id
                ]);
                
                if (!$mba_letter) {
                    $a_return = ['code' => 1, 'message' => 'Template not found!'];
                }

                $s_academic_year_id = $this->session->userdata('academic_year_id_active');
                $s_semester_type_id = $this->session->userdata('semester_type_id_active');

                if (($s_post_academic_year_id) AND ($s_post_semester_type_id)) {
                    $s_academic_year_id = $s_post_academic_year_id;
                    $s_semester_type_id = $s_post_semester_type_id;
                }
    
                $s_new_number = $this->Lnm->get_new_number();
                $s_employee_name = $this->Pdm->retrieve_title($o_employee_data->personal_data_id);
                $s_new_char = NULL;
                $s_month = date('m');
                $s_year = date('Y');
                $s_letter_date = date('Y-m-d');
    
                if ($s_backdated) {
                    $s_month = date('m', strtotime($s_backdated));
                    $s_year = date('Y', strtotime($s_backdated));
                    $s_letter_date = $s_year.'-'.$s_month.'-01';
    
                    $s_new_char = $this->Lnm->get_new_number('char', $s_month, $s_year);
                    $mba_count_number = $this->General->get_where('dt_letter_number');
                    
                    if ($mba_count_number) {
                        $mba_count_number = $this->General->get_where('dt_letter_number', [
                            'letter_number != ' => NULL
                        ]);
    
                        if ($mba_count_number) {
                            $mba_count_number = $this->Lnm->get_list([
                                'ln.letter_month' => $s_month,
                                'ln.letter_year' => $s_year,
                                'ln.letter_number != ' => NULL
                            ]);
                            if ($mba_count_number) {
                                $s_new_number = $mba_count_number[0]->letter_number;
                            }
                            else {
                                $s_new_number = $this->Lnm->get_last_number($s_month, $s_year);
                            }
                        }
                        else {
                            $s_new_number = 520;
                        }
                    }
                    else {
                        $s_new_number = 520;
                    }
                }
    
                $s_result_number = $this->_format_number_letter(
                    $mbo_employee_hr->department_abbreviation,
                    $mba_letter[0]->letter_abbreviation,
                    $s_new_number,
                    $s_new_char,
                    $s_month,
                    $s_year
                );
    
                $a_letter_number_data = [
                    'personal_data_id' => $this->session->userdata('user'),
                    'letter_type_id' => $s_letter_type_id,
                    'department_id' => $mbo_employee_hr->department_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'letter_number_result' => $s_result_number,
                    'letter_number' => $s_new_number,
                    'letter_char' => $s_new_char,
                    'letter_month' => $s_month,
                    'letter_year' => $s_year,
                    'letter_description' => 'Reference Letter',
                    'letter_purpose' => $s_employee_name,
                    'letter_date' => $s_letter_date,
                    'date_added' => date('Y-m-d H:i:s')
                ];
    
                $mbs_letter_number_id = $this->Lnm->save_new_number($a_letter_number_data);
                if ($mbs_letter_number_id) {
                    $a_letter_number_target = [
                        'letter_number_id' => $mbs_letter_number_id,
                        'personal_data_id' => $this->session->userdata('user'),
                        'template_id' => $s_template_id,
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Lnm->save_letter_number_target($a_letter_number_target);
                    
                    $a_data = [
                        'template_id' => $s_template_id,
                        'employee_id' => $o_employee_data->employee_id,
                        'letter_number' => $s_result_number,
                        // 'department_id' => $mba_study_program_data[0]->study_program_id,
                        'academic_year_id' => $s_academic_year_id,
                        'semester_type_id' => $s_semester_type_id,
                        'letter_date' => $s_letter_date,
                        'letter_year' => $s_year
                    ];
                    // generate file
                    $a_return = modules::run('download/doc_download/generate_reference_letter_resign', $a_data);
                    if ($a_return['code'] == 0) {
                        $s_personal_document_id = $this->uuid->v4();
                        $a_personal_document_data = [
                            'personal_document_id' => $s_personal_document_id,
                            'personal_data_id_generated' => $this->session->userdata('user'),
                            'personal_data_id_target' => $o_employee_data->personal_data_id,
                            'letter_number_id' => $mbs_letter_number_id,
                            'document_link' => urldecode($a_return['file'])
                        ];
    
                        $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
                        $this->Lnm->submit_letter_number($a_personal_document_data);
                        $a_return['doc_key'] = $s_personal_document_id;
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Error generate number!'];
                }
                // 
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Employee not found!'];
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'Head of Human Resource not found!'];
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);exit;
        }
        else {
            return $a_return;
        }
        // print('<pre>');var_dump($a_return);exit;
    }

    public function generate_permohonan_nidn_rektor($s_employee_id = false)
    {
        if ($this->input->is_ajax_request()) {
            // $s_employee_id = $this->session->userdata('employee_id');
            $s_employee_id = $this->input->post('employee_id');
        }

        $mba_employee_data = $this->Emm->get_employee_department([
            'em.employee_id' => $s_employee_id
        ]);

        if (!$mba_employee_data) {
            $a_return = ['code' => 1, 'message' => 'Your data not found!'];
        }
        else {
            $o_employee_data = $mba_employee_data[0];
            $a_data = [
                'employee_id' => $o_employee_data->employee_id
            ];

            $a_return = modules::run('download/doc_download/generate_permohonan_nidn_rektor', $a_data);
            if ($a_return['code'] == 0) {
                $s_personal_document_id = $this->uuid->v4();
                $a_personal_document_data = [
                    'personal_document_id' => $s_personal_document_id,
                    'personal_data_id_generated' => $this->session->userdata('user'),
                    'personal_data_id_target' => $o_employee_data->personal_data_id,
                    'letter_number_id' => NULL,
                    'document_link' => urldecode($a_return['savepath'])
                ];

                $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
                $this->Lnm->submit_letter_number($a_personal_document_data);
                $a_return['doc_key'] = $s_personal_document_id;
            }
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);exit;
        }
        else {
            return $a_return;
        }
    }

    public function generate_assignment_letter_community_personal(
        $s_employee_id = false,
        $s_backdated = false,
        $s_post_academic_year_id = false,
        $s_post_semester_type_id = false,
        $s_letter_number_id = false,
        $s_department_abbreviation = false,
        $s_letter_desc = false,
        $mba_employee_id = false,
        $mba_student_id = false
    ){
        if (!$s_employee_id) {
            $s_employee_id = $this->session->userdata('employee_id');
        }

        $s_template_id = '17';
        $s_letter_type_id = '7';

        $mba_employee_data = $this->Emm->get_employee_department([
            'em.employee_id' => $s_employee_id
        ]);

        if (!$mba_employee_data) {
            $a_return = ['code' => 1, 'message' => 'Your data not found!'];
        }
        else {
            $o_employee_data = $mba_employee_data[0];
            $mba_letter = $this->Lnm->get_template(false, [
                'template_id' => $s_template_id
            ]);
            
            if (!$mba_letter) {
                $a_return = ['code' => 1, 'message' => 'Template not found!'];
            }

            $s_academic_year_id = $this->session->userdata('academic_year_id_active');
            $s_semester_type_id = $this->session->userdata('semester_type_id_active');

            if (($s_post_academic_year_id) AND ($s_post_semester_type_id)) {
                $s_academic_year_id = $s_post_academic_year_id;
                $s_semester_type_id = $s_post_semester_type_id;
            }

            $this->load->model('personal_data/Personal_data_model', 'Pdm');
            $s_new_number = $this->Lnm->get_new_number();
            $s_user_name = $this->Pdm->retrieve_title($this->session->userdata('user'));
            $s_new_char = NULL;
            $s_month = date('m');
            $s_year = date('Y');
            $s_letter_date = date('Y-m-d');

            if ($s_backdated) {
                $s_month = date('m', strtotime($s_backdated));
                $s_year = date('Y', strtotime($s_backdated));
                $s_letter_date = $s_year.'-'.$s_month.'-01';

                $s_new_char = $this->Lnm->get_new_number('char', $s_month, $s_year);
                $mba_count_number = $this->General->get_where('dt_letter_number');
                
                if ($mba_count_number) {
                    $mba_count_number = $this->General->get_where('dt_letter_number', [
                        'letter_number != ' => NULL
                    ]);

                    if ($mba_count_number) {
                        $mba_count_number = $this->Lnm->get_list([
                            'ln.letter_month' => $s_month,
                            'ln.letter_year' => $s_year,
                            'ln.letter_number != ' => NULL
                        ]);
                        if ($mba_count_number) {
                            $s_new_number = $mba_count_number[0]->letter_number;
                        }
                        else {
                            $s_new_number = $this->Lnm->get_last_number($s_month, $s_year);
                        }
                    }
                    else {
                        $s_new_number = 520;
                    }
                }
                else {
                    $s_new_number = 520;
                }
            }

            $s_result_number = $this->_format_number_letter(
                (!$s_department_abbreviation) ? $o_employee_data->department_abbreviation : $s_department_abbreviation,
                $mba_letter[0]->letter_abbreviation,
                $s_new_number,
                $s_new_char,
                $s_month,
                $s_year
            );

            $b_exists = false;
            if ($s_letter_number_id) {
                $mba_letter_number_data = $this->General->get_where('dt_letter_number', ['letter_number_id' => $s_letter_number_id]);
                $mba_letter_data_exist = $this->Lnm->get_letter_target([
                    'ln.letter_number_id' => $s_letter_number_id
                ]);
                $b_exists = true;
                $mbs_letter_number_id = $s_letter_number_id;
                $s_month = ($mba_letter_number_data) ? $mba_letter_number_data[0]->letter_month : $s_month;
                $s_year = ($mba_letter_number_data) ? $mba_letter_number_data[0]->letter_year : $s_year;
                $s_new_number = ($mba_letter_number_data) ? $mba_letter_number_data[0]->letter_number : $s_new_number;
                $s_new_char = ($mba_letter_number_data) ? $mba_letter_number_data[0]->letter_char : $s_new_char;
                $s_letter_date = ($mba_letter_number_data) ? $mba_letter_number_data[0]->letter_date : $s_letter_date;
                $s_result_number = ($mba_letter_number_data) ? $mba_letter_number_data[0]->letter_number_result : $s_result_number;
                $s_academic_year_id = ($mba_letter_number_data) ? $mba_letter_number_data[0]->academic_year_id : $s_academic_year_id;
                $s_semester_type_id = ($mba_letter_number_data) ? $mba_letter_number_data[0]->semester_type_id : $s_semester_type_id;

                $mba_employee_id = [];
                $mba_student_id = [];
                if ($mba_letter_data_exist) {
                    foreach ($mba_letter_data_exist as $o_target) {
                        if ($o_target->target_type == 'student') {
                            $mba_student_data = $this->Stm->get_student_filtered([
                                'ds.personal_data_id' => $o_target->personal_data_id_target,
                                'ds.student_status' => 'resign'
                            ]);

                            if (($mba_student_data) AND (!in_array($mba_student_data[0]->student_id, $mba_student_id))) {
                                array_push($mba_student_id, $mba_student_data[0]->student_id);
                            }
                        }
                        else if ($o_target->target_type == 'lecturer') {
                            $mba_employee_data = $this->Emm->get_employee_data(['em.personal_data_id' => $o_target->personal_data_id_target]);
                            if (($mba_employee_data) AND (!in_array($mba_employee_data[0]->employee_id, $mba_employee_id))) {
                                array_push($mba_employee_id, $mba_employee_data[0]->employee_id);
                            }
                        }
                    }
                }
            }
            else {
                $a_letter_number_data = [
                    'personal_data_id' => $this->session->userdata('user'),
                    'letter_type_id' => $s_letter_type_id,
                    'department_id' => $o_employee_data->department_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'letter_number_result' => $s_result_number,
                    'letter_number' => $s_new_number,
                    'letter_char' => $s_new_char,
                    'letter_month' => $s_month,
                    'letter_year' => $s_year,
                    'letter_description' => ($s_letter_desc) ? $s_letter_desc : 'Assignment Letter for Community',
                    'letter_purpose' => $s_user_name,
                    'letter_date' => $s_letter_date,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                $mbs_letter_number_id = $this->Lnm->save_new_number($a_letter_number_data);
            }

            if ($mbs_letter_number_id) {
                // $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_abbreviation' => $o_employee_data->department_abbreviation]);
                // if ($mba_study_program_data) {
                //     if (!is_null($mba_study_program_data[0]->study_program_main_id)) {
                //         $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mba_study_program_data[0]->study_program_main_id]);
                //     }
                // }
                // else {
                //     $this->db->trans_rollback();
                //     $a_return = ['code' => 1, 'message' => 'Department not found!'];
                // }
                
                $a_data = [
                    'letter_number_id' => $mbs_letter_number_id,
                    'template_id' => $s_template_id,
                    'employee_id' => $o_employee_data->employee_id,
                    'letter_number' => $s_result_number,
                    // 'study_program_id' => $mba_study_program_data[0]->study_program_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'letter_date' => $s_letter_date,
                    'letter_year' => $s_year,
                    'file_exists' => $b_exists,
                    'a_employee_id' => $mba_employee_id,
                    'a_student_id' => $mba_student_id,
                ];

                if ((is_array($mba_employee_id)) OR (is_array($mba_student_id))) {
                    $this->Lnm->remove_letter_target($mbs_letter_number_id, $s_template_id);
                    $this->General->force_delete('dt_personal_document', 'letter_number_id', $mbs_letter_number_id);
                }
                // generate file
                $a_return = modules::run('download/doc_download/generate_assignment_letter_for_community', $a_data);
                if ($a_return['code'] == 0) {
                    $s_personal_document_id = $this->uuid->v4();
                    $a_personal_document_data = [
                        'personal_document_id' => $s_personal_document_id,
                        'personal_data_id_generated' => $this->session->userdata('user'),
                        'personal_data_id_target' => $this->session->userdata('user'),
                        'letter_number_id' => $mbs_letter_number_id,
                        'document_link' => urldecode($a_return['file'])
                    ];

                    $s_document_token = md5(json_encode($a_personal_document_data).time());
                    $a_personal_document_data['document_token'] = $s_document_token;
                    $this->Lnm->submit_letter_number($a_personal_document_data);
                    $a_return['doc_key'] = $s_personal_document_id;
                    $a_return['doc_passcode'] = $s_document_token;
                    $a_return['doc_letter'] = $mbs_letter_number_id;

                    if (($mba_employee_id) AND (is_array($mba_employee_id))) {
                        foreach ($mba_employee_id as $s_employee_id) {
                            $mba_employee_data = $this->General->get_where('dt_employee', ['employee_id' => $s_employee_id]);
                            if ($mba_employee_data) {
                                $this->Lnm->save_letter_number_target([
                                    'letter_number_id' => $a_return['doc_letter'],
                                    'personal_data_id' => $mba_employee_data[0]->personal_data_id,
                                    'template_id' => $s_template_id,
                                    'target_type' => 'lecturer',
                                    'date_added' => date('Y-m-d H:i:s')
                                ]);
                                $this->Lnm->submit_letter_number([
                                    'personal_document_id' => $this->uuid->v4(),
                                    'personal_data_id_generated' => $this->session->userdata('user'),
                                    'personal_data_id_target' => $mba_employee_data[0]->personal_data_id,
                                    'letter_number_id' => $a_return['doc_letter'],
                                    'document_token' => $a_return['doc_passcode'],
                                    'document_link' => urldecode($a_return['file']),
                                    'date_added' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    }
                    if (($mba_student_id) AND (is_array($mba_student_id))) {
                        foreach ($mba_student_id as $s_student_id) {
                            $mba_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id]);
                            if ($mba_student_data) {
                                $this->Lnm->save_letter_number_target([
                                    'letter_number_id' => $a_return['doc_letter'],
                                    'personal_data_id' => $mba_student_data[0]->personal_data_id,
                                    'template_id' => $s_template_id,
                                    'target_type' => 'student',
                                    'date_added' => date('Y-m-d H:i:s')
                                ]);
                                $this->Lnm->submit_letter_number([
                                    'personal_document_id' => $this->uuid->v4(),
                                    'personal_data_id_generated' => $this->session->userdata('user'),
                                    'personal_data_id_target' => $mba_student_data[0]->personal_data_id,
                                    'letter_number_id' => $a_return['doc_letter'],
                                    'document_token' => $a_return['doc_passcode'],
                                    'document_link' => urldecode($a_return['file']),
                                    'date_added' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    }
                }
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Error generate number!'];
            }
        }

        // if ($this->input->is_ajax_request()) {
        //     print json_encode($a_return);
        //     // exit;
        // }
        // else {
            return $a_return;
        // }
    }

    public function generate_assignment_letter_research_personal($s_employee_id = false, $s_backdated = false, $s_post_academic_year_id = false, $s_post_semester_type_id = false, $s_letter_number_id = false)
    {
        if ($this->input->is_ajax_request()) {
            $s_employee_id = $this->session->userdata('employee_id');
        }
        
        $s_template_id = '21';
        $s_letter_type_id = '7';

        $mba_employee_data = $this->Emm->get_employee_department([
            'em.employee_id' => $s_employee_id
        ]);

        if (!$mba_employee_data) {
            $a_return = ['code' => 1, 'message' => 'Your data not found!'];
        }
        else {
            $o_employee_data = $mba_employee_data[0];
            $mba_letter = $this->Lnm->get_template(false, [
                'template_id' => $s_template_id
            ]);
            
            if (!$mba_letter) {
                $a_return = ['code' => 1, 'message' => 'Template not found!'];
            }

            $s_academic_year_id = $this->session->userdata('academic_year_id_active');
            $s_semester_type_id = $this->session->userdata('semester_type_id_active');

            if (($s_post_academic_year_id) AND ($s_post_semester_type_id)) {
                $s_academic_year_id = $s_post_academic_year_id;
                $s_semester_type_id = $s_post_semester_type_id;
            }

            // $mba_letter_data = $this->Lnm->get_letter_target([
            //     'lnt.personal_data_id' => $o_employee_data->personal_data_id,
            //     'ln.academic_year_id' => $s_academic_year_id,
            //     'ln.semester_type_id' => $s_semester_type_id,
            //     'lnt.template_id' => $s_template_id
            // ]);

            // if (!$mba_letter_data) {
                $this->load->model('personal_data/Personal_data_model', 'Pdm');
                $s_new_number = $this->Lnm->get_new_number();
                $s_user_name = $this->Pdm->retrieve_title($this->session->userdata('user'));
                $s_new_char = NULL;
                $s_month = date('m');
                $s_year = date('Y');
                $s_letter_date = date('Y-m-d');

                if ($s_backdated) {
                    $s_month = date('m', strtotime($s_backdated));
                    $s_year = date('Y', strtotime($s_backdated));
                    $s_letter_date = $s_year.'-'.$s_month.'-01';

                    $s_new_char = $this->Lnm->get_new_number('char', $s_month, $s_year);
                    $mba_count_number = $this->General->get_where('dt_letter_number');
                    
                    if ($mba_count_number) {
                        $mba_count_number = $this->General->get_where('dt_letter_number', [
                            'letter_number != ' => NULL
                        ]);

                        if ($mba_count_number) {
                            $mba_count_number = $this->Lnm->get_list([
                                'ln.letter_month' => $s_month,
                                'ln.letter_year' => $s_year,
                                'ln.letter_number != ' => NULL
                            ]);
                            if ($mba_count_number) {
                                $s_new_number = $mba_count_number[0]->letter_number;
                            }
                            else {
                                $s_new_number = $this->Lnm->get_last_number($s_month, $s_year);
                            }
                        }
                        else {
                            $s_new_number = 520;
                        }
                    }
                    else {
                        $s_new_number = 520;
                    }
                }

                $s_result_number = $this->_format_number_letter(
                    $o_employee_data->department_abbreviation,
                    $mba_letter[0]->letter_abbreviation,
                    $s_new_number,
                    $s_new_char,
                    $s_month,
                    $s_year
                );

                $a_letter_number_data = [
                    'personal_data_id' => $this->session->userdata('user'),
                    'letter_type_id' => $s_letter_type_id,
                    'department_id' => $o_employee_data->department_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'letter_number_result' => $s_result_number,
                    'letter_number' => $s_new_number,
                    'letter_char' => $s_new_char,
                    'letter_month' => $s_month,
                    'letter_year' => $s_year,
                    'letter_description' => 'Assignment Letter for Research',
                    'letter_purpose' => $s_user_name,
                    'letter_date' => $s_letter_date,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                if ($s_letter_number_id) {
                    $mbs_letter_number_id = $s_letter_number_id;
                }
                else {
                    $mbs_letter_number_id = $this->Lnm->save_new_number($a_letter_number_data);
                }

                if ($mbs_letter_number_id) {
                    $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_abbreviation' => $o_employee_data->department_abbreviation]);
                    if ($mba_study_program_data) {
                        if (!is_null($mba_study_program_data[0]->study_program_main_id)) {
                            $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mba_study_program_data[0]->study_program_main_id]);
                        }
                    }
                    else {
                        $this->db->trans_rollback();
                        $a_return = ['code' => 1, 'message' => 'Department not found!'];
                    }

                    $a_letter_number_target = [
                        'letter_number_id' => $mbs_letter_number_id,
                        'personal_data_id' => $this->session->userdata('user'),
                        'template_id' => $s_template_id,
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Lnm->save_letter_number_target($a_letter_number_target);
                    
                    $a_data = [
                        'template_id' => $s_template_id,
                        'employee_id' => $o_employee_data->employee_id,
                        'letter_number' => $s_result_number,
                        'study_program_id' => $mba_study_program_data[0]->study_program_id,
                        'academic_year_id' => $s_academic_year_id,
                        'semester_type_id' => $s_semester_type_id,
                        'letter_date' => $s_letter_date,
                        'letter_year' => $s_year
                    ];
                    // generate file
                    $a_return = modules::run('download/doc_download/generate_assignment_letter_for_research', $a_data);
                    if ($a_return['code'] == 0) {
                        $s_personal_document_id = $this->uuid->v4();
                        $a_personal_document_data = [
                            'personal_document_id' => $s_personal_document_id,
                            'personal_data_id_generated' => $this->session->userdata('user'),
                            'personal_data_id_target' => $this->session->userdata('user'),
                            'letter_number_id' => $mbs_letter_number_id,
                            'document_link' => urldecode($a_return['file'])
                        ];

                        $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
                        $this->Lnm->submit_letter_number($a_personal_document_data);
                        $a_return['doc_key'] = $s_personal_document_id;
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Error generate number!'];
                }
            // }
            // else {
            //     $a_return = ['code' => 1, 'message' => 'invalid letter'];
            // }
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);exit;
        }
        else {
            return $a_return;
        }
    }

    public function generate_assignment_letter_examiner_thesis($s_employee_id = false, $s_backdated = false, $s_post_academic_year_id = false, $s_post_semester_type_id = false, $s_personal_data_id = false, $s_letter_number_id = false)
    {
        if ($this->input->is_ajax_request()) {
            $s_employee_id = $this->session->userdata('employee_id');
        }

        if (!$s_personal_data_id) {
            $s_personal_data_id = $this->session->userdata('user');
        }

        $s_template_id = '6';
        $s_letter_type_id = '7';

        $mba_employee_data = $this->Emm->get_employee_department([
            'em.employee_id' => $s_employee_id
        ]);

        if (!$mba_employee_data) {
            $a_return = ['code' => 1, 'message' => 'Your data not found!'];
        }
        else {
            $o_employee_data = $mba_employee_data[0];
            $mba_letter = $this->Lnm->get_template(false, [
                'template_id' => $s_template_id
            ]);
            
            if (!$mba_letter) {
                $a_return = ['code' => 1, 'message' => 'Template not found!'];
            }

            $s_academic_year_id = $this->session->userdata('academic_year_id_active');
            $s_semester_type_id = $this->session->userdata('semester_type_id_active');

            if (($s_post_academic_year_id) AND ($s_post_semester_type_id)) {
                $s_academic_year_id = $s_post_academic_year_id;
                $s_semester_type_id = $s_post_semester_type_id;
            }

            // $mba_letter_data = $this->Lnm->get_letter_target([
            //     'lnt.personal_data_id' => $o_employee_data->personal_data_id,
            //     'ln.academic_year_id' => $s_academic_year_id,
            //     'ln.semester_type_id' => $s_semester_type_id,
            //     'lnt.template_id' => $s_template_id
            // ]);

            // if (!$mba_letter_data) {
                $this->load->model('personal_data/Personal_data_model', 'Pdm');
                $s_new_number = $this->Lnm->get_new_number();
                $s_user_name = $this->Pdm->retrieve_title($this->session->userdata('user'));
                $s_new_char = NULL;
                $s_month = date('m');
                $s_year = date('Y');
                $s_letter_date = date('Y-m-d');

                if ($s_backdated) {
                    $s_month = date('m', strtotime($s_backdated));
                    $s_year = date('Y', strtotime($s_backdated));
                    $s_letter_date = $s_year.'-'.$s_month.'-01';

                    $s_new_char = $this->Lnm->get_new_number('char', $s_month, $s_year);
                    $mba_count_number = $this->General->get_where('dt_letter_number');
                    
                    if ($mba_count_number) {
                        $mba_count_number = $this->General->get_where('dt_letter_number', [
                            'letter_number != ' => NULL
                        ]);

                        if ($mba_count_number) {
                            $mba_count_number = $this->Lnm->get_list([
                                'ln.letter_month' => $s_month,
                                'ln.letter_year' => $s_year,
                                'ln.letter_number != ' => NULL
                            ]);
                            if ($mba_count_number) {
                                $s_new_number = $mba_count_number[0]->letter_number;
                            }
                            else {
                                $s_new_number = $this->Lnm->get_last_number($s_month, $s_year);
                            }
                        }
                        else {
                            $s_new_number = 520;
                        }
                    }
                    else {
                        $s_new_number = 520;
                    }
                }

                $s_result_number = $this->_format_number_letter(
                    $o_employee_data->department_abbreviation,
                    $mba_letter[0]->letter_abbreviation,
                    $s_new_number,
                    $s_new_char,
                    $s_month,
                    $s_year
                );

                $a_letter_number_data = [
                    'personal_data_id' => $this->session->userdata('user'),
                    'letter_type_id' => $s_letter_type_id,
                    'department_id' => $o_employee_data->department_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'letter_number_result' => $s_result_number,
                    'letter_number' => $s_new_number,
                    'letter_char' => $s_new_char,
                    'letter_month' => $s_month,
                    'letter_year' => $s_year,
                    'letter_description' => 'Assignment Letter Examiner Thesis Defense',
                    'letter_purpose' => $s_user_name,
                    'letter_date' => $s_letter_date,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                if ($s_letter_number_id) {
                    $mbs_letter_number_id = $s_letter_number_id;
                }
                else {
                    $mbs_letter_number_id = $this->Lnm->save_new_number($a_letter_number_data);
                }

                // $mbs_letter_number_id = $this->Lnm->save_new_number($a_letter_number_data);
                if ($mbs_letter_number_id) {
                    $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_abbreviation' => $o_employee_data->department_abbreviation]);
                    $mba_is_deans = $this->General->get_where('ref_faculty', ['deans_id' => $this->session->userdata('user')]);
                    if ($mba_study_program_data) {
                        if (!is_null($mba_study_program_data[0]->study_program_main_id)) {
                            $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mba_study_program_data[0]->study_program_main_id]);
                        }

                        $a_letter_number_target = [
                            'letter_number_id' => $mbs_letter_number_id,
                            'personal_data_id' => $this->session->userdata('user'),
                            'template_id' => $s_template_id,
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        $this->Lnm->save_letter_number_target($a_letter_number_target);
                        
                        $a_data = [
                            'personal_data_id' => $s_personal_data_id,
                            'template_id' => $s_template_id,
                            'employee_id' => $o_employee_data->employee_id,
                            'letter_number' => $s_result_number,
                            'study_program_id' => $mba_study_program_data[0]->study_program_id,
                            'academic_year_id' => $s_academic_year_id,
                            'semester_type_id' => $s_semester_type_id,
                            'letter_year' => $s_year,
                            'letter_date' => $s_letter_date
                        ];
                        
                        // generate file
                        $a_return = modules::run('download/doc_download/generate_assignment_letter_examiner_thesis', $a_data);
                        if ($a_return['code'] == 0) {
                            $s_personal_document_id = $this->uuid->v4();
                            $a_personal_document_data = [
                                'personal_document_id' => $s_personal_document_id,
                                'personal_data_id_generated' => $this->session->userdata('user'),
                                'personal_data_id_target' => $this->session->userdata('user'),
                                'letter_number_id' => $mbs_letter_number_id,
                                'document_link' => urldecode($a_return['file'])
                            ];
    
                            $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
                            $this->Lnm->submit_letter_number($a_personal_document_data);
                            $a_return['doc_key'] = $s_personal_document_id;
                        }
                    }
                    else {
                        $this->db->trans_rollback();
                        $a_return = ['code' => 1, 'message' => 'Department study program not found!'];
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Error generate number!'];
                }
            // }
            // else {
            //     $a_return = ['code' => 1, 'message' => 'letter exists'];
            // }
        }

        // if ($this->input->is_ajax_request()) {
            print json_encode($a_return);exit;
        // }
        // else {
        //     return $a_return;
        // }
    }

    function generate_assignment_letter_advisor_internship($s_employee_id = false, $s_backdated = false, $s_post_academic_year_id = false, $s_post_semester_type_id = false, $s_personal_data_id = false)
    {
        if ($this->input->is_ajax_request()) {
            $s_employee_id = $this->session->userdata('employee_id');
        }

        if (!$s_personal_data_id) {
            $s_personal_data_id = $this->session->userdata('user');
        }
        
        $s_template_id = '27';
        $s_letter_type_id = '7';

        $mba_employee_data = $this->Emm->get_employee_department([
            'em.employee_id' => $s_employee_id
        ]);

        if (!$mba_employee_data) {
            $a_return = ['code' => 1, 'message' => 'Your data not found!'];
        }
        else {
            $o_employee_data = $mba_employee_data[0];
            $mba_letter = $this->Lnm->get_template(false, [
                'template_id' => $s_template_id
            ]);
            
            if (!$mba_letter) {
                $a_return = ['code' => 1, 'message' => 'Template not found!'];
            }

            $s_academic_year_id = $this->session->userdata('academic_year_id_active');
            $s_semester_type_id = $this->session->userdata('semester_type_id_active');

            if (($s_post_academic_year_id) AND ($s_post_semester_type_id)) {
                $s_academic_year_id = $s_post_academic_year_id;
                $s_semester_type_id = $s_post_semester_type_id;
            }

            $mba_letter_data = $this->Lnm->get_letter_target([
                'lnt.personal_data_id' => $o_employee_data->personal_data_id,
                'ln.academic_year_id' => $s_academic_year_id,
                'ln.semester_type_id' => $s_semester_type_id,
                'lnt.template_id' => $s_template_id
            ]);

            if (!$mba_letter_data) {
                $this->load->model('personal_data/Personal_data_model', 'Pdm');
                $s_new_number = $this->Lnm->get_new_number();
                $s_user_name = $this->Pdm->retrieve_title($this->session->userdata('user'));
                $s_new_char = NULL;
                $s_month = date('m');
                $s_year = date('Y');
                $s_letter_date = date('Y-m-d');

                if ($s_backdated) {
                    $s_month = date('m', strtotime($s_backdated));
                    $s_year = date('Y', strtotime($s_backdated));
                    $s_letter_date = $s_year.'-'.$s_month.'-01';

                    $s_new_char = $this->Lnm->get_new_number('char', $s_month, $s_year);
                    $mba_count_number = $this->General->get_where('dt_letter_number');
                    
                    if ($mba_count_number) {
                        $mba_count_number = $this->General->get_where('dt_letter_number', [
                            'letter_number != ' => NULL
                        ]);

                        if ($mba_count_number) {
                            $mba_count_number = $this->Lnm->get_list([
                                'ln.letter_month' => $s_month,
                                'ln.letter_year' => $s_year,
                                'ln.letter_number != ' => NULL
                            ]);
                            if ($mba_count_number) {
                                $s_new_number = $mba_count_number[0]->letter_number;
                            }
                            else {
                                $s_new_number = $this->Lnm->get_last_number($s_month, $s_year);
                            }
                        }
                        else {
                            $s_new_number = 520;
                        }
                    }
                    else {
                        $s_new_number = 520;
                    }
                }

                $s_result_number = $this->_format_number_letter(
                    $o_employee_data->department_abbreviation,
                    $mba_letter[0]->letter_abbreviation,
                    $s_new_number,
                    $s_new_char,
                    $s_month,
                    $s_year
                );

                $a_letter_number_data = [
                    'personal_data_id' => $this->session->userdata('user'),
                    'letter_type_id' => $s_letter_type_id,
                    'department_id' => $o_employee_data->department_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'letter_number_result' => $s_result_number,
                    'letter_number' => $s_new_number,
                    'letter_char' => $s_new_char,
                    'letter_month' => $s_month,
                    'letter_year' => $s_year,
                    'letter_description' => 'Assignment Letter Internship Supervision',
                    'letter_purpose' => $s_user_name,
                    'letter_date' => $s_letter_date,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                $mbs_letter_number_id = $this->Lnm->save_new_number($a_letter_number_data);
                if ($mbs_letter_number_id) {
                    $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_abbreviation' => $o_employee_data->department_abbreviation]);
                    $mba_is_deans = $this->General->get_where('ref_faculty', ['deans_id' => $this->session->userdata('user')]);
                    if ($mba_study_program_data) {
                        if (!is_null($mba_study_program_data[0]->study_program_main_id)) {
                            $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mba_study_program_data[0]->study_program_main_id]);
                        }
                    }
                    else {
                        $this->db->trans_rollback();
                        $a_return = ['code' => 1, 'message' => 'Department not found!'];
                    }

                    $a_letter_number_target = [
                        'letter_number_id' => $mbs_letter_number_id,
                        'personal_data_id' => $this->session->userdata('user'),
                        'template_id' => $s_template_id,
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Lnm->save_letter_number_target($a_letter_number_target);
                    // ambil mahasiswa bimbingan sini
                    
                    $a_data = [
                        'personal_data_id' => $s_personal_data_id,
                        'template_id' => $s_template_id,
                        'employee_id' => $o_employee_data->employee_id,
                        'letter_number' => $s_result_number,
                        'study_program_id' => ($mba_study_program_data) ? $mba_study_program_data[0]->study_program_id : '',
                        'academic_year_id' => $s_academic_year_id,
                        'semester_type_id' => $s_semester_type_id,
                        'letter_year' => $s_year,
                        'letter_date' => $s_letter_date
                    ];
                    // generate file
                    $a_return = modules::run('download/doc_download/generate_assignment_letter_advisor_internship', $a_data);
                    if ($a_return['code'] == 0) {
                        $s_personal_document_id = $this->uuid->v4();
                        $a_personal_document_data = [
                            'personal_document_id' => $s_personal_document_id,
                            'personal_data_id_generated' => $this->session->userdata('user'),
                            'personal_data_id_target' => $this->session->userdata('user'),
                            'letter_number_id' => $mbs_letter_number_id,
                            'document_link' => urldecode($a_return['file'])
                        ];

                        $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
                        $this->Lnm->submit_letter_number($a_personal_document_data);
                        $a_return['doc_key'] = $s_personal_document_id;
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Error generate number!'];
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => 'letter exists'];
            }
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);exit;
        }
        else {
            return $a_return;
        }
    }

    public function generate_assignment_letter_advisor_thesis($s_employee_id = false, $s_backdated = false, $s_post_academic_year_id = false, $s_post_semester_type_id = false, $s_personal_data_id = false)
    {
        if ($this->input->is_ajax_request()) {
            $s_employee_id = $this->session->userdata('employee_id');
        }

        if (!$s_personal_data_id) {
            $s_personal_data_id = $this->session->userdata('user');
        }
        
        $s_template_id = '4';
        $s_letter_type_id = '7';
        // print('<pre>');var_dump($s_employee_id);
        $mba_employee_data = $this->Emm->get_employee_department([
            'em.employee_id' => $s_employee_id
        ]);
        // print('<pre>');var_dump($mba_employee_data);exit;

        if (!$mba_employee_data) {
            $a_return = ['code' => 1, 'message' => 'Your data not found!'];
        }
        else {
            $o_employee_data = $mba_employee_data[0];
            $mba_letter = $this->Lnm->get_template(false, [
                'template_id' => $s_template_id
            ]);
            
            if (!$mba_letter) {
                $a_return = ['code' => 1, 'message' => 'Template not found!'];
            }

            $s_academic_year_id = $this->session->userdata('academic_year_id_active');
            $s_semester_type_id = $this->session->userdata('semester_type_id_active');

            if (($s_post_academic_year_id) AND ($s_post_semester_type_id)) {
                $s_academic_year_id = $s_post_academic_year_id;
                $s_semester_type_id = $s_post_semester_type_id;
            }

            $mba_letter_data = $this->Lnm->get_letter_target([
                'lnt.personal_data_id' => $o_employee_data->personal_data_id,
                'ln.academic_year_id' => $s_academic_year_id,
                'ln.semester_type_id' => $s_semester_type_id,
                'lnt.template_id' => $s_template_id
            ]);

            if (!$mba_letter_data) {
                $this->load->model('personal_data/Personal_data_model', 'Pdm');
                $s_new_number = $this->Lnm->get_new_number();
                $s_user_name = $this->Pdm->retrieve_title($this->session->userdata('user'));
                $s_new_char = NULL;
                $s_month = date('m');
                $s_year = date('Y');
                $s_letter_date = date('Y-m-d');

                if ($s_backdated) {
                    $s_month = date('m', strtotime($s_backdated));
                    $s_year = date('Y', strtotime($s_backdated));
                    $s_letter_date = $s_year.'-'.$s_month.'-01';

                    $s_new_char = $this->Lnm->get_new_number('char', $s_month, $s_year);
                    $mba_count_number = $this->General->get_where('dt_letter_number');
                    
                    if ($mba_count_number) {
                        $mba_count_number = $this->General->get_where('dt_letter_number', [
                            'letter_number != ' => NULL
                        ]);

                        if ($mba_count_number) {
                            $mba_count_number = $this->Lnm->get_list([
                                'ln.letter_month' => $s_month,
                                'ln.letter_year' => $s_year,
                                'ln.letter_number != ' => NULL
                            ]);
                            if ($mba_count_number) {
                                $s_new_number = $mba_count_number[0]->letter_number;
                            }
                            else {
                                $s_new_number = $this->Lnm->get_last_number($s_month, $s_year);
                            }
                        }
                        else {
                            $s_new_number = 520;
                        }
                    }
                    else {
                        $s_new_number = 520;
                    }
                }

                $s_result_number = $this->_format_number_letter(
                    $o_employee_data->department_abbreviation,
                    $mba_letter[0]->letter_abbreviation,
                    $s_new_number,
                    $s_new_char,
                    $s_month,
                    $s_year
                );

                $a_letter_number_data = [
                    'personal_data_id' => $this->session->userdata('user'),
                    'letter_type_id' => $s_letter_type_id,
                    'department_id' => $o_employee_data->department_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'letter_number_result' => $s_result_number,
                    'letter_number' => $s_new_number,
                    'letter_char' => $s_new_char,
                    'letter_month' => $s_month,
                    'letter_year' => $s_year,
                    'letter_description' => 'Assignment Letter Advisor Thesis Defense',
                    'letter_purpose' => $s_user_name,
                    'letter_date' => $s_letter_date,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                $mbs_letter_number_id = $this->Lnm->save_new_number($a_letter_number_data);
                if ($mbs_letter_number_id) {
                    $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_abbreviation' => $o_employee_data->department_abbreviation]);
                    $mba_is_deans = $this->General->get_where('ref_faculty', ['deans_id' => $this->session->userdata('user')]);
                    if ($mba_study_program_data) {
                        if (!is_null($mba_study_program_data[0]->study_program_main_id)) {
                            $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mba_study_program_data[0]->study_program_main_id]);
                        }
                    }
                    else {
                        $this->db->trans_rollback();
                        $a_return = ['code' => 1, 'message' => 'Department not found!'];
                    }

                    $a_letter_number_target = [
                        'letter_number_id' => $mbs_letter_number_id,
                        'personal_data_id' => $this->session->userdata('user'),
                        'template_id' => $s_template_id,
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Lnm->save_letter_number_target($a_letter_number_target);
                    // ambil mahasiswa bimbingan sini
                    
                    $a_data = [
                        'personal_data_id' => $s_personal_data_id,
                        'template_id' => $s_template_id,
                        'employee_id' => $o_employee_data->employee_id,
                        'letter_number' => $s_result_number,
                        'study_program_id' => $mba_study_program_data[0]->study_program_id,
                        'academic_year_id' => $s_academic_year_id,
                        'semester_type_id' => $s_semester_type_id,
                        'letter_year' => $s_year,
                        'letter_date' => $s_letter_date
                    ];
                    // generate file
                    $a_return = modules::run('download/doc_download/generate_assignment_letter_advisor_thesis', $a_data);
                    if ($a_return['code'] == 0) {
                        $s_personal_document_id = $this->uuid->v4();
                        $a_personal_document_data = [
                            'personal_document_id' => $s_personal_document_id,
                            'personal_data_id_generated' => $this->session->userdata('user'),
                            'personal_data_id_target' => $this->session->userdata('user'),
                            'letter_number_id' => $mbs_letter_number_id,
                            'document_link' => urldecode($a_return['file'])
                        ];

                        $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
                        $this->Lnm->submit_letter_number($a_personal_document_data);
                        $a_return['doc_key'] = $s_personal_document_id;
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Error generate number!'];
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => 'letter exists'];
            }
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);exit;
        }
        else {
            return $a_return;
        }
    }

    public function generate_assignment_letter_personal($s_employee_id = false, $s_backdated = false, $s_post_academic_year_id = false, $s_post_semester_type_id = false)
    {
        if ($this->input->is_ajax_request()) {
            $s_employee_id = $this->session->userdata('employee_id');
        }
        
        $s_template_id = '7';
        $s_letter_type_id = '7';
        // print('<pre>');var_dump($s_employee_id);
        $mba_employee_data = $this->Emm->get_employee_department([
            'em.employee_id' => $s_employee_id
        ]);
        // print('<pre>');var_dump($mba_employee_data);exit;

        if (!$mba_employee_data) {
            $a_return = ['code' => 1, 'message' => 'Your data not found!'];
        }
        else {
            $o_employee_data = $mba_employee_data[0];
            $mba_letter = $this->Lnm->get_template(false, [
                'template_id' => $s_template_id
            ]);
            
            if (!$mba_letter) {
                $a_return = ['code' => 1, 'message' => 'Template not found!'];
            }

            $s_academic_year_id = $this->session->userdata('academic_year_id_active');
            $s_semester_type_id = $this->session->userdata('semester_type_id_active');

            if (($s_post_academic_year_id) AND ($s_post_semester_type_id)) {
                $s_academic_year_id = $s_post_academic_year_id;
                $s_semester_type_id = $s_post_semester_type_id;
            }

            $mba_letter_data = $this->Lnm->get_letter_target([
                'lnt.personal_data_id' => $o_employee_data->personal_data_id,
                'ln.academic_year_id' => $s_academic_year_id,
                'ln.semester_type_id' => $s_semester_type_id,
                'lnt.template_id' => $s_template_id
            ]);

            if (!$mba_letter_data) {
                $this->load->model('personal_data/Personal_data_model', 'Pdm');
                $s_new_number = $this->Lnm->get_new_number();
                $s_user_name = $this->Pdm->retrieve_title($this->session->userdata('user'));
                $s_new_char = NULL;
                $s_month = date('m');
                $s_year = date('Y');
                $s_letter_date = date('Y-m-d');

                if ($s_backdated) {
                    $s_month = date('m', strtotime($s_backdated));
                    $s_year = date('Y', strtotime($s_backdated));
                    $s_letter_date = $s_year.'-'.$s_month.'-01';

                    $s_new_char = $this->Lnm->get_new_number('char', $s_month, $s_year);
                    $mba_count_number = $this->General->get_where('dt_letter_number');
                    
                    if ($mba_count_number) {
                        $mba_count_number = $this->General->get_where('dt_letter_number', [
                            'letter_number != ' => NULL
                        ]);

                        if ($mba_count_number) {
                            $mba_count_number = $this->Lnm->get_list([
                                'ln.letter_month' => $s_month,
                                'ln.letter_year' => $s_year,
                                'ln.letter_number != ' => NULL
                            ]);
                            if ($mba_count_number) {
                                $s_new_number = $mba_count_number[0]->letter_number;
                            }
                            else {
                                $s_new_number = $this->Lnm->get_last_number($s_month, $s_year);
                            }
                        }
                        else {
                            $s_new_number = 520;
                        }
                    }
                    else {
                        $s_new_number = 520;
                    }
                }

                $s_result_number = $this->_format_number_letter(
                    $o_employee_data->department_abbreviation,
                    $mba_letter[0]->letter_abbreviation,
                    $s_new_number,
                    $s_new_char,
                    $s_month,
                    $s_year
                );

                $a_letter_number_data = [
                    'personal_data_id' => $this->session->userdata('user'),
                    'letter_type_id' => $s_letter_type_id,
                    'department_id' => $o_employee_data->department_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'letter_number_result' => $s_result_number,
                    'letter_number' => $s_new_number,
                    'letter_char' => $s_new_char,
                    'letter_month' => $s_month,
                    'letter_year' => $s_year,
                    'letter_description' => 'Assignment Letter Lecturer',
                    'letter_purpose' => $s_user_name,
                    'letter_date' => $s_letter_date,
                    'date_added' => date('Y-m-d H:i:s')
                ];

                $mbs_letter_number_id = $this->Lnm->save_new_number($a_letter_number_data);
                if ($mbs_letter_number_id) {
                    $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_abbreviation' => $o_employee_data->department_abbreviation]);
                    $mba_is_deans = $this->General->get_where('ref_faculty', ['deans_id' => $this->session->userdata('user')]);
                    if ($mba_is_deans) {
                        $s_template_id = '19';
                    }
                    else if ($mba_study_program_data) {
                        if (!is_null($mba_study_program_data[0]->study_program_main_id)) {
                            $mba_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $mba_study_program_data[0]->study_program_main_id]);
                        }
                    }
                    else {
                        $this->db->trans_rollback();
                        $a_return = ['code' => 1, 'message' => 'Department not found!'];
                    }

                    $a_letter_number_target = [
                        'letter_number_id' => $mbs_letter_number_id,
                        'personal_data_id' => $this->session->userdata('user'),
                        'template_id' => $s_template_id,
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $this->Lnm->save_letter_number_target($a_letter_number_target);
                    
                    $a_data = [
                        'template_id' => $s_template_id,
                        'employee_id' => $o_employee_data->employee_id,
                        'letter_number' => $s_result_number,
                        'study_program_id' => ($mba_study_program_data) ? $mba_study_program_data[0]->study_program_id : NULL,
                        'academic_year_id' => $s_academic_year_id,
                        'semester_type_id' => $s_semester_type_id,
                        'target' => ($mba_is_deans) ? 'deans' : 'lecturer',
                        'letter_year' => $s_year
                    ];
                    // generate file
                    $a_return = modules::run('download/doc_download/generate_assignment_letter_lecturing', $a_data);
                    if ($a_return['code'] == 0) {
                        $s_personal_document_id = $this->uuid->v4();
                        $a_personal_document_data = [
                            'personal_document_id' => $s_personal_document_id,
                            'personal_data_id_generated' => $this->session->userdata('user'),
                            'personal_data_id_target' => $this->session->userdata('user'),
                            'letter_number_id' => $mbs_letter_number_id,
                            'document_link' => urldecode($a_return['file'])
                        ];

                        $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
                        $this->Lnm->submit_letter_number($a_personal_document_data);
                        $a_return['doc_key'] = $s_personal_document_id;
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Error generate number!'];
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Assigment letter already generate!'];
            }
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);exit;
        }
        else {
            return $a_return;
        }
        // var_dump($a_return);exit;
    }

    public function incoming_letter()
    {
        $this->a_page_data['body'] = $this->load->view('view_incoming', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function form_asl_advisor_thesis()
    {
        $html = $this->load->view('apps/modal/asl_advisor_thesis', $this->a_page_data, true);
        return $html;
    }

    public function form_apl_internship_student()
    {
        $html = $this->load->view('apps/modal/apl_internship_form', $this->a_page_data, true);
        return $html;
    }

    public function modal_assignment_letter_lecturing()
    {
        $this->a_page_data['employee_list'] = $this->Emm->get_employee_data(['em.employee_is_lecturer' => 'YES', 'status' => 'ACTIVE']);
        $this->a_page_data['study_program_list'] = $this->Spm->get_study_program();
        $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, [1,2,7,8]);
        $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
        // print('<pre>');var_dump($this->a_page_data['academic_year_list']);exit;
        $html = $this->load->view('apps/modal/asl_assignment_letter_lecturing', $this->a_page_data, true);
        return $html;
    }

    public function modal_assignment_letter_community()
    {
        $this->a_page_data['employee_list'] = $this->Emm->get_employee_data(['em.employee_is_lecturer' => 'YES', 'status' => 'ACTIVE']);
        $this->a_page_data['study_program_list'] = $this->Spm->get_study_program();
        $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, [1,2,7,8]);
        $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
        // print('<pre>');var_dump($this->a_page_data['academic_year_list']);exit;
        $html = $this->load->view('apps/modal/asl_assignment_letter_community', $this->a_page_data, true);
        return $html;
    }

    public function form_generated()
    {
        $html = $this->load->view('apps/modal/form_template_letter', $this->a_page_data, true);
        return $html;
    }

    public function get_list_template()
    {
        if ($this->input->is_ajax_request()) {
            $s_letter_type_id = $this->input->post('letter_type_key');
            $mba_template_file = $this->Lnm->get_template($s_letter_type_id);
            if ($mba_template_file) {
                foreach ($mba_template_file as $o_template) {
                    $o_template->filename_download = urlencode($o_template->template_filelink);
                }
            }

            $a_template_link = [];
            print json_encode(['data' => $mba_template_file]);
        }
        else {
            show_404();
        }
    }

    public function test_temp_download()
    {
        $s_path = '/home/portal/applications/portal2/uploads/staff/L/4d1cbbb3-75ae-4bc0-89af-4422a028618b/2021/request_letter/06 08 Lecture-Assignment-Letter-for Lect Rev082(Lucia Kusumawati-20211).docx';
        if (is_file($s_path)) {
            print('ada');
        }
        else {
            print('ga ada!');
        }
    }

    public function download_template_result($s_filename, $s_personal_document_id = false)
    {
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
            if ($this->session->userdata('user') == '2be26a04-9010-48be-a3aa-10b814b32ae0') {
                // print('<pre>');var_dump($o_personal_document_data);exit;
            }
            $s_firts_char_user = substr($o_personal_document_data->personal_data_name, 0, 1);
            
            $s_filepath = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_personal_document_data->personal_data_id.'/'.$o_personal_document_data->letter_year.'/request_letter/';
        }
        else {
            $mba_letter_type = $this->Lnm->get_template(false, ['template_filelink' => $s_filename]);
            if ($mba_letter_type) {
                $s_filepath .= $mba_letter_type[0]->letter_abbreviation.'/';
            }
        }
        
        $s_fullpath = $s_filepath.$s_filename;
        
        if (is_file($s_fullpath)) {
            header('Content-Disposition: attachment; filename='.$s_filename);
            flush();
            readfile( $s_fullpath );
            exit;
        }
        else {
            if ($this->session->userdata('user') == '2be26a04-9010-48be-a3aa-10b814b32ae0') {
                // print('<pre>');var_dump($s_fullpath);exit;
            }

            log_message('error', 'ERROR from '.__FILE__.' '.__LINE__);
            $this->a_page_data['page_error'] = current_url();
            $this->a_page_data['body'] = $this->load->view('dashboard/student_error', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function new_template_file()
    {
        if ($this->input->is_ajax_request()) {
            $s_letter_type_id = $this->input->post('letter_type_id');
            $s_template_id = $this->input->post('template_key');

            $mba_letter_type_data = $this->General->get_where('ref_letter_type', ['letter_type_id' => $s_letter_type_id]);
            $mba_letter_template_data = $this->General->get_where('ref_letter_type_template', ['letter_type_id' => $s_letter_type_id, 'filename' => $_FILES['template_file']['name'], 'template_id != ' => $s_template_id]);
            if (!$mba_letter_type_data) {
                $a_return = ['code' => 1, 'message' => 'Error retrieve letter type data!'];
            }
            else if ($mba_letter_template_data) {
                $a_return = ['code' => 1, 'message' => 'File names cannot be the same!'];
            }
            else if(empty($_FILES['template_file']['name'])) {
                $a_return = ['code' => 1, 'message' => 'Error retrieve file data!'];
            }
            else {
                $o_letter_type = $mba_letter_type_data[0];
                $s_file_path = APPPATH.'uploads/templates/spmi/'.$o_letter_type->letter_abbreviation.'/';

                if (!empty($s_template_id)) {
                    $mba_template_data = $this->General->get_where('ref_letter_type_template', ['template_id' => $s_template_id]);
                    if ($mba_template_data) {
                        $s_path_unlink = $s_file_path.$mba_template_data[0]->filename;
                        if(file_exists($s_path_unlink)){
                            unlink($s_path_unlink);
                        }
                    }
                }
                
                // $s_filename = str_replace('.');
                $config['allowed_types'] = 'doc|odt|docx';
                $config['max_size'] = 2400;
                $config['file_ext_tolower'] = true;
                $config['upload_path'] = $s_file_path;
                // $config['file_name'] = $s_filename;
                
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0755, true);
                }
                $this->load->library('upload', $config);

                if($this->upload->do_upload('template_file')){
                    $a_letter_tempate_data = array(
                        'letter_type_id' => $s_letter_type_id,
                        'filename' => $_FILES['template_file']['name'],
                        'template_filelink' => $this->upload->data('file_name'),
                        'date_added' => date('Y-m-d H:i:s')
                    );

                    if (!empty($s_template_id)) {
                        $this->Lnm->submit_template($a_letter_tempate_data, $s_template_id);
                    }
                    else {
                        $this->Lnm->submit_template($a_letter_tempate_data);
                    }
                    
                    $a_return = ['code' => 0,'message' => 'Success'];
                }
                else{
                    $a_return = array('code' => 2, 'message' => $this->upload->display_errors('<span>', '</span><br>'));
                }
            }

            print json_encode($a_return);
        }
        else {
            show_404();
        }
    }

    public function generate_key_number_letter($letter_number_result, $s_template_id)
    {
        if ($this->input->is_ajax_request()) {
            $a_data = [
                'template_id' => $s_template_id,
                'letter_number' => $letter_number_result
            ];

            $a_return = modules::run('download/doc_download/generate_key_letter_template', $a_data);

            if ($a_return['code'] == 0) {
                $mba_letter_data = $this->General->get_where('dt_letter_number', ['letter_number_result' => $letter_number_result]);
                $s_personal_document_id = $this->uuid->v4();
                $a_personal_document_data = [
                    'personal_document_id' => $s_personal_document_id,
                    'personal_data_id_generated' => $this->session->userdata('user'),
                    'letter_number_id' => $mba_letter_data[0]->letter_number_id,
                    'document_link' => urldecode($a_return['file'])
                ];

                $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
                $this->Lnm->submit_letter_number($a_personal_document_data);
                $a_return['doc_key'] = $s_personal_document_id;
            }

            return $a_return;
        }
    }

    public function generate_internship_student_letter($letter_number_result, $s_template_id)
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('student_key', 'Student', 'required|trim');
            $this->form_validation->set_rules('supervisor_name', 'To', 'trim');
            $this->form_validation->set_rules('company_name', 'Company Name', 'trim');
            $this->form_validation->set_rules('supervisor_occupation', 'Position', 'trim');
            $this->form_validation->set_rules('company_address', 'Company Address', 'trim');
            $this->form_validation->set_rules('start_date', 'Start of Internship', 'trim');
            $this->form_validation->set_rules('end_date', 'End of Internship', 'trim');
            
            if ($this->form_validation->run() === false) {
                $a_return = ['code' => 1, 'message' => validation_errors('<li>', '</li>')];
            }
            else {
                $a_data = [
                    'template_id' => $s_template_id,
                    'student_id' => set_value('student_key'),
                    'letter_number' => $letter_number_result,
                    'spv_name' => set_value('supervisor_name'),
                    'spv_occupation' => set_value('supervisor_occupation'),
                    'company_name' => set_value('company_name'),
                    'company_address' => set_value('company_address'),
                    'start_date' => set_value('start_date'),
                    'end_date' => set_value('end_date')
                ];
        
                $a_return = modules::run('download/doc_download/generate_application_letter_internship_student', $a_data);

                if ($a_return['code'] == 0) {
                    $mba_student_data = $this->General->get_where('dt_student', ['student_id' => set_value('student_key')]);
                    $mba_letter_data = $this->General->get_where('dt_letter_number', ['letter_number_result' => $letter_number_result]);
                    $s_personal_document_id = $this->uuid->v4();
                    $a_personal_document_data = [
                        'personal_document_id' => $s_personal_document_id,
                        'personal_data_id_generated' => $this->session->userdata('user'),
                        'personal_data_id_target' => $mba_student_data[0]->personal_data_id,
                        'letter_number_id' => $mba_letter_data[0]->letter_number_id,
                        'document_link' => urldecode($a_return['file'])
                    ];

                    $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
                    $this->Lnm->submit_letter_number($a_personal_document_data);
                    $a_return['doc_key'] = $s_personal_document_id;
                }
            }

            return $a_return;
        }
        else {
            show_404();
        }
    }

    public function generate_lecturer_assignment($letter_number_result, $s_template_id)
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('employee_key', 'Lecturer', 'required|trim');
            $this->form_validation->set_rules('academic_semester_key', 'Academic Semester Year', 'required|trim');
            $this->form_validation->set_rules('semester_type_key', 'Academic Semester Type', 'required|trim');
            $this->form_validation->set_rules('study_program_key', 'Study Program', 'required|trim');
            
            if ($this->form_validation->run() === false) {
                $a_return = ['code' => 1, 'message' => validation_errors('<li>', '</li>')];
            }
            else {
                $a_data = [
                    'template_id' => $s_template_id,
                    'employee_id' => set_value('employee_key'),
                    'letter_number' => $letter_number_result,
                    'study_program_id' => set_value('study_program_key'),
                    'academic_year_id' => set_value('academic_semester_key'),
                    'semester_type_id' => set_value('semester_type_key')
                ];
        
                $a_return = modules::run('download/doc_download/generate_assigment_letter_study_program', $a_data);

                if ($a_return['code'] == 0) {
                    $mba_employee_data = $this->General->get_where('dt_employee', ['employee_id' => set_value('employee_key')]);
                    $mba_letter_data = $this->General->get_where('dt_letter_number', ['letter_number_result' => $letter_number_result]);
                    $s_personal_document_id = $this->uuid->v4();
                    $a_personal_document_data = [
                        'personal_document_id' => $s_personal_document_id,
                        'personal_data_id_generated' => $this->session->userdata('user'),
                        'personal_data_id_target' => $mba_employee_data[0]->personal_data_id,
                        'letter_number_id' => $mba_letter_data[0]->letter_number_id,
                        'document_link' => urldecode($a_return['file'])
                    ];

                    $a_personal_document_data['document_token'] = md5(json_encode($a_personal_document_data).time());
                    $this->Lnm->submit_letter_number($a_personal_document_data);
                    $a_return['doc_key'] = $s_personal_document_id;
                }
            }

            return $a_return;
        }
        else {
            show_404();
        }
    }

    public function generate_number()
    {
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->session->userdata('user');
            $this->form_validation->set_rules('letter_type', 'Letter Type', 'trim|required');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');
            $this->form_validation->set_rules('purpose', 'To ', 'trim|required');
            $this->form_validation->set_rules('description', 'Description', 'trim|required');
            $this->form_validation->set_rules('template_key', 'Template', 'trim|required');

            if ($this->input->post('backdated_switch') !== null) {
                $this->form_validation->set_rules('backdate', 'Back Date', 'required|trim');
            }

            if (in_array($this->input->post('template_key'), ['null', '', 'all', 'false'])) {
                $a_return = ['code' => 998, 'message' => 'Please select a template!'];
                print json_encode($a_return);exit;
            }

            if (empty($this->input->post('template_data'))) {
                $a_return = ['code' => 999, 'message' => 'Unknow target!'];
                print json_encode($a_return);exit;
            }

            $method_generate = $this->input->post('template_data');
            switch ($method_generate) {
                case 'generate_lecturer_assignment':
                    $this->form_validation->set_rules('employee_key', 'Lecturer', 'required|trim');
                    $this->form_validation->set_rules('academic_semester_key', 'Academic Semester Year', 'required|trim');
                    $this->form_validation->set_rules('semester_type_key', 'Academic Semester Type', 'required|trim');
                    $this->form_validation->set_rules('study_program_key', 'Study Program', 'required|trim');
                    break;

                // case 'generate_lecturer_assignment_community':
                //     $this->form_validation->set_rules('employee_key', 'Lecturer', 'required|trim');
                //     $this->form_validation->set_rules('academic_semester_key', 'Academic Semester Year', 'required|trim');
                //     $this->form_validation->set_rules('semester_type_key', 'Academic Semester Type', 'required|trim');
                //     $this->form_validation->set_rules('study_program_key', 'Study Program', 'required|trim');
                //     break;

                case 'generate_internship_student_letter':
                    $this->form_validation->set_rules('student_key', 'Student', 'required|trim');
                    $this->form_validation->set_rules('supervisor_name', 'To', 'trim');
                    $this->form_validation->set_rules('company_name', 'Company Name', 'trim');
                    $this->form_validation->set_rules('supervisor_occupation', 'Position', 'trim');
                    $this->form_validation->set_rules('company_address', 'Company Address', 'trim');
                    $this->form_validation->set_rules('start_date', 'Start of Internship', 'trim');
                    $this->form_validation->set_rules('end_date', 'End of Internship', 'trim');
                    break;
                
                default:
                    // $a_return = ['code' => 999, 'message' => 'Unknow target!'];
                    // print json_encode($a_return);exit;
                    break;
            }

            if ($this->form_validation->run()) {
                $s_new_number = $this->Lnm->get_new_number();
                $mba_template_file = $this->Lnm->get_template(set_value('letter_type'));
                $s_new_char = NULL;
                $mba_department_data = $this->General->get_where('ref_department', ['department_id' => set_value('department')])[0];
                $mba_letter_type_data = $this->General->get_where('ref_letter_type', ['letter_type_id' => set_value('letter_type')])[0];
                
                $s_month = date('m');
                $s_year = date('Y');
                $s_letter_date = date('Y-m-d H:i:s');

                if ($this->input->post('backdated_switch') !== null) {
                    $a_dateselected = explode(' ', set_value('backdate'));
                    $s_month = date('m', strtotime($a_dateselected[0]));
                    $s_year = $a_dateselected[1];
                    $s_letter_date = $s_year.'-'.$s_month.'-01';

                    $s_new_char = $this->Lnm->get_new_number('char', $s_month, $s_year);
                    $mba_count_number = $this->General->get_where('dt_letter_number');
                    
                    if ($mba_count_number) {
                        $mba_count_number = $this->General->get_where('dt_letter_number', [
                            'letter_number != ' => NULL
                        ]);

                        if ($mba_count_number) {
                            $mba_count_number = $this->Lnm->get_list([
                                'ln.letter_month' => $s_month,
                                'ln.letter_year' => $s_year,
                                'ln.letter_number != ' => NULL
                            ]);
                            if ($mba_count_number) {
                                $s_new_number = $mba_count_number[0]->letter_number;
                                // print('ksoong<pre>');var_dump($s_new_number);exit;
                            }
                            else {
                                $s_new_number = $this->Lnm->get_last_number($s_month, $s_year);
                            }
                        }
                        else {
                            $s_new_number = 520;
                        }
                    }
                    else {
                        $s_new_number = 520;
                    }
                }

                $s_result_number = $this->_format_number_letter(
                    $mba_department_data->department_abbreviation,
                    $mba_letter_type_data->letter_abbreviation,
                    $s_new_number,
                    $s_new_char,
                    $s_month,
                    $s_year
                );

                $a_letter_number_data = [
                    'personal_data_id' => $s_personal_data_id,
                    'letter_type_id' => set_value('letter_type'),
                    'department_id' => set_value('department'),
                    'letter_number_result' => $s_result_number,
                    'letter_number' => ($this->input->post('backdated_switch') !== null) ? NULL : $s_new_number,
                    'letter_char' => ($this->input->post('backdated_switch') !== null) ? $s_new_char : NULL,
                    'letter_month' => $s_month,
                    'letter_year' => $s_year,
                    'letter_description' => set_value('description'),
                    'letter_purpose' => set_value('purpose'),
                    'letter_date' => $s_letter_date,
                    'date_added' => date('Y-m-d H:i:s'),
                    'timestamp' => date('Y-m-d H:i:s')
                ];

                $b_save = $this->Lnm->save_new_number($a_letter_number_data);

                if ($b_save) {
                    $a_return = $this->$method_generate($s_result_number, $this->input->post('template_key'));
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'Unknow Error, please contact IT Dept.'];
                }
            }
            else {
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
        else {
            show_404();
        }
    }

    public function form_generate()
    {
        $s_employee_id = ($this->session->has_userdata('employee_id')) ? $this->session->userdata('employee_id') : false;
        $this->a_page_data['letter_type'] = $this->General->get_where('ref_letter_type');
        $this->a_page_data['list_dept'] = $this->General->get_where('ref_department');
        $this->a_page_data['form_generate'] = $this->load->view('apps/form/form_generate', $this->a_page_data, true);
        $this->a_page_data['list_table'] = $this->load->view('apps/table/list_generated', $this->a_page_data, true);
        $this->load->view('apps/form/form_generate', $this->a_page_data);
    }

    public function letter_type()
    {
        $a_personal_data_id_allowed = $this->a_allowed_page['letter_type_list'];

        if ($this->input->is_ajax_request()) {
            $mba_letter_data = $this->General->get_where('ref_letter_type');
            if ($mba_letter_data) {
                foreach ($mba_letter_data as $o_letter) {
                    $mba_letter_template = $this->Lnm->get_template($o_letter->letter_type_id);
                    $o_letter->template_list = $mba_letter_template;
                }
            }
            print json_encode(['data' => $mba_letter_data]);
        }
        else {
            if (in_array($this->session->userdata('user'), $a_personal_data_id_allowed)) {
                $this->a_page_data['body'] = $this->load->view('view_letter_type', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
            else {
                show_404();
            }
        }
    }

    public function list_number_of_letter()
    {
        // if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        //     $this->a_page_data['page_error'] = current_url();
        //     $this->a_page_data['body'] = $this->load->view('dashboard/maintenance_page', $this->a_page_data, true);
        //     $s_html = $this->load->view('layout', $this->a_page_data, true);
        //     echo $s_html;
        //     exit(4);
        // }

        $s_employee_id = ($this->session->has_userdata('employee_id')) ? $this->session->userdata('employee_id') : false;
        $mba_allowed = $this->General->get_where('ref_department', ['employee_id' => $s_employee_id]);
        if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
            $mba_allowed = true;
        }

        if (!$mba_allowed) {
            $a_config_allowed_page = $this->config->item('allowed_page');
            $a_config_allowed_page = $a_config_allowed_page['letter_number_page'];
            if (in_array($this->session->userdata('user'), $a_config_allowed_page)) {
                $mba_allowed = true;
            }
        }

        if ($mba_allowed) {
            $this->a_page_data['user_allowed_template'] = $this->a_allowed_page['letter_type_list'];
            $this->a_page_data['letter_type'] = $this->General->get_where('ref_letter_type');
            $this->a_page_data['list_dept'] = $this->General->get_where('ref_department');
            $this->a_page_data['list_table'] = $this->load->view('apps/table/list_generated', $this->a_page_data, true);
            $this->a_page_data['body'] = $this->load->view('apps/view_list', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    public function new_number()
    {
        $number = $this->Lnm->get_new_number();
        print('<pre>');
        var_dump($number);exit;
    }

    public function get_list_number_generated()
    {
        if ($this->session->has_userdata('employee_id')) {
            if ($this->input->is_ajax_request()) {
                // $s_employee_id = $this->session->userdata('employee_id');
                $a_data = $this->Lnm->get_list();
                if ($a_data) {
                    foreach ($a_data as $o_data) {
                        $mba_letter_generated = $this->Lnm->get_personal_document([
                            'pdc.letter_number_id' => $o_data->letter_number_id,
                            'pdc.personal_data_id_generated' => $o_data->personal_data_id
                        ]);

                        $s_template_link = '';
                        $mba_letter_target = false;
                        if ($mba_letter_generated) {
                            $mba_letter_target = $this->Lnm->get_letter_target([
                                'lnt.letter_number_id' => $o_data->letter_number_id,
                                'lnt.personal_data_id' => $mba_letter_generated[0]->personal_data_id_target
                            ]);
                            if (!$mba_letter_target) {
                                $mba_letter_target = $this->Lnm->get_letter_target([
                                    'lnt.letter_number_id' => $o_data->letter_number_id,
                                    'lnt.personal_data_id != ' => $mba_letter_generated[0]->personal_data_id_target
                                ]);
                            }
                            $s_filename_template = $mba_letter_generated[0]->document_link;
                            $s_template_link = '<a href="'.base_url().'apps/letter_numbering/download_template_result/'.urlencode($s_filename_template).'/'.$mba_letter_generated[0]->personal_document_id.'" target="_blank">'.$o_data->letter_number_result.'</a>';
                        }
                        
                        $o_data->template_generated = $s_template_link;
                        $o_data->template_id = ($mba_letter_target) ? $mba_letter_target[0]->template_id : '';
                        // $o_data->letter_description = urlencode($o_data->letter_description);
                    }
                }
                print json_encode(['data' => $a_data]);
            }
        }
        else {
            show_404();
        }
    }

    private function _format_number_letter(
        $s_department_abbreviation,
        $s_letter_abbreviation,
        $s_number,
        $s_char,
        $s_month,
        $s_year
    )
    {
        $romawi = array("I","II","III", "IV", "V","VI","VII","VIII","IX","X", "XI","XII");
        // $s_number = preg_replace("/[^0-9]/","",$s_new_number);
        // $s_char_number = preg_replace("/[^a-zA-Z]+/", "", $s_new_number);
        $s_new_number_ = str_pad($s_number, 4, '0', STR_PAD_LEFT);
        $s_new_number_ .= $s_char;
        $s_month = $romawi[intval($s_month) - 1];
        $a_new_format = [
            $s_letter_abbreviation,
            $s_department_abbreviation,
            $s_new_number_,
            'IULI',
            $s_month,
            $s_year
        ];

        return implode('/', $a_new_format);
    }
}
