<?php
class Curriculum extends App_core
{
    function __construct()
    {
        parent::__construct('academic');
        $this->load->model('Curriculum_model','Cm');
        $this->load->model('study_program/Study_program_model', 'Spm');
    }

    public function curriculum_lists($s_curriculum_id = false, $s_semester_id = false)
    {
        if ($s_curriculum_id) {
            $mbo_curriculum_data = $this->Cm->get_curriculum_filtered(array('curriculum_id' => $s_curriculum_id));
            if ($mbo_curriculum_data) {
                $this->a_page_data['o_curriculum_data'] = $mbo_curriculum_data[0];
                $this->a_page_data['s_curriculum_id'] = $s_curriculum_id;
                $this->a_page_data['mbo_curriculum_semester'] = $this->Cm->get_curriculum_semester($s_curriculum_id);
                if ($s_semester_id) {
                    $mbo_semester_data = $this->Cm->get_semester_list($s_semester_id);
                    $this->a_page_data['s_semester_id'] = ($mbo_semester_data) ? $s_semester_id : 'All';
                    $this->a_page_data['body'] = $this->load->view('academic/curriculum/curriculum_subject_lists', $this->a_page_data, true);
                }else{
                    $this->a_page_data['body'] = $this->load->view('academic/curriculum/curriculum_semester_lists', $this->a_page_data, true);
                }
            }else {
                redirect('academic/curriculum/curriculum_lists');
            }
        }else{
            $this->a_page_data['body'] = $this->load->view('academic/curriculum/curriculum_list', $this->a_page_data, true);
        }
        $this->load->view('layout', $this->a_page_data);
    }

    public function view_table_curriculum()
    {
        $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'curriculum');
        $this->a_page_data['btn_html'] = $s_btn_html;
        $this->load->view('curriculum/table/curriculum_list_table', $this->a_page_data);
    }

    public function view_table_curriculum_semester($s_curriculum_id = false)
    {
        if ($s_curriculum_id) {
            $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'curriculum_semester');
            $this->a_page_data['btn_html'] = $s_btn_html;
            $this->a_page_data['curriculum_id'] = $s_curriculum_id;
            $this->load->view('curriculum/table/curriculum_semester_list_table', $this->a_page_data);
        }
    }

    public function view_table_curriculum_subject($s_curriculum_id = false, $s_semester_id = false)
    {
        if ($s_curriculum_id) {
            $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'curriculum_subject');
            $this->a_page_data['mode'] = 'curriculum';
            $this->a_page_data['btn_html'] = $s_btn_html;
            $this->a_page_data['s_semester_id'] = $s_semester_id;
            $this->a_page_data['curriculum_id'] = $s_curriculum_id;
            $this->a_page_data['o_curriculum_data'] = $this->Cm->get_curriculum_data($s_curriculum_id);
            $this->load->view('curriculum/table/curriculum_subject_list_table', $this->a_page_data);
        }else{
            $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'curriculum_offered_subject');
            $this->a_page_data['mode'] = 'offered_subject';
            $this->a_page_data['btn_html'] = $s_btn_html;
            $this->a_page_data['s_semester_id'] = false;
            $this->a_page_data['curriculum_id'] = false;
            $this->load->view('curriculum/table/curriculum_subject_list_table', $this->a_page_data);
        }
    }

    public function form_filter_curriculum()
    {
        $this->load->model('academic/Academic_year_model', 'Aym');

        $this->a_page_data['o_academic_year'] = $this->Aym->get_academic_year_lists();
        $this->a_page_data['o_program_lists'] = $this->Spm->get_program_lists_select();
        $this->a_page_data['o_study_program_list'] = $this->Spm->get_study_program(false, false);
        $this->load->view('curriculum/form/form_filter_curriculum', $this->a_page_data);
    }

    public function form_curriculum_subject_filter($s_semester_id = false, $s_curriculum_id = false)
    {
        $this->a_page_data['s_semester_id'] = $s_semester_id;
        $this->a_page_data['o_semester_data'] = $this->Cm->get_curriculum_semester($s_curriculum_id);
        $this->load->view('curriculum/form/form_filter_curriculum_subject', $this->a_page_data);
    }

    public function form_filter_curriculum_offered_subject()
    {
        $this->load->model('Academic_year_model', 'Acm');
        $mbo_academic_year = $this->Acm->get_academic_year_lists(array('academic_year_intake_status' => 'active'))[0];
        $this->a_page_data['o_program_lists'] = $this->Spm->get_program_lists_select(array('program_main_id' => NULL));
        $this->a_page_data['o_active_year'] = $this->Acm->get_academic_year_lists();
        $this->a_page_data['o_semester_data'] = $this->Cm->get_semester_list(false, true);
        $this->a_page_data['o_curriculum_list'] = $this->Cm->get_curriculum_filtered(array('valid_academic_year' => $mbo_academic_year->academic_year_id));
        $this->load->view('curriculum/form/form_filter_curriculum_offered_subject', $this->a_page_data);
    }

    public function form_create_curriculum()
    {
        $this->load->model('Academic_year_model', 'Acm');

        $this->a_page_data['o_program_lists'] = $this->Spm->get_program_lists_select();
        $this->a_page_data['o_academic_year_lists'] = $this->Aym->get_academic_year_lists();
        $this->a_page_data['o_study_program_list'] = $this->Spm->get_study_program(false, false);
        $this->a_page_data['o_semester_data'] = $this->Cm->get_semester_list(false, true);
        $this->a_page_data['term'] = false;

        if ($this->input->is_ajax_request()) {
            $s_curriculum_id = $this->input->post('curriculum_id');
            $s_term = $this->input->post('term');
            $this->a_page_data['term'] = $s_term;
            $this->a_page_data['o_curriculum_data'] = $this->Cm->get_curriculum_filtered(array('curriculum_id' => $s_curriculum_id))[0];
            $s_page = $this->load->view('curriculum/form/form_create_curriculum', $this->a_page_data, true);
            print json_encode(array('data' => $s_page));
        }else{
            $this->a_page_data['o_curriculum_data'] = false;
            $s_page = $this->load->view('curriculum/form/form_create_curriculum', $this->a_page_data, true);
            print($s_page);
        }
    }

    public function form_create_curriculum_semester($s_curriculum_id = false)
    {
        if ($s_curriculum_id) {
            $this->a_page_data['semester_list'] = $this->Cm->get_semester_list(false, true);
            $this->a_page_data['curriculum_id'] = $s_curriculum_id;
            $this->a_page_data['term'] = false;
            if ($this->input->is_ajax_request()) {
                $s_semester_id = $this->input->post('semester_id');
                $s_term = $this->input->post('term');
                $this->a_page_data['term'] = $s_term;
                switch ($s_term) {
                    case 'edit':
                        $this->a_page_data['o_curriculum_semester'] = $this->Cm->get_curriculum_semester($s_curriculum_id, $s_semester_id)[0];
                        break;
                    case 'copy':
                        $this->a_page_data['o_curriculum_semester'] = $this->Cm->get_curriculum_semester($s_curriculum_id, $s_semester_id)[0];
                        break;
                    default:
                        $this->a_page_data['o_curriculum_semester'] = false;
                        break;
                }
                $s_page = $this->load->view('curriculum/form/form_create_curriculum_semester', $this->a_page_data, true);
                print json_encode(array('code' => 0, 'data' => $s_page));
            }else{
                $this->a_page_data['o_curriculum_semester'] = false;
                $s_page = $this->load->view('curriculum/form/form_create_curriculum_semester', $this->a_page_data, true);
                print($s_page);
            }
        }
    }

    public function form_create_curriculum_subject($s_curriculum_id = false, $s_semester_id = false)
    {
        if ($s_curriculum_id) {
            $this->load->model('Subject_model','Sm');
            $this->a_page_data['s_semester_id'] = $s_semester_id;
            $this->a_page_data['semester_list'] = $this->Cm->get_curriculum_semester($s_curriculum_id);
            $this->a_page_data['o_subject_type'] = $this->Sm->get_subject_type();
            $this->a_page_data['o_curriculum_subject_category'] = $this->General->get_enum_values('ref_curriculum_subject', 'curriculum_subject_category');
            $this->a_page_data['curriculum_id'] = $s_curriculum_id;
            if ($this->input->is_ajax_request()) {
                $s_curriculum_subject_id = $this->input->post('curriculum_subject_id');
                $this->a_page_data['o_subject_curriculum_data'] = $this->Cm->get_curriculum_subject_data($s_curriculum_subject_id);
                $s_page = $this->load->view('curriculum/form/form_create_curriculum_subject', $this->a_page_data, true);
                print json_encode(array('data' => $s_page));
            }else{
                $this->a_page_data['o_subject_curriculum_data'] = false;
                $s_page = $this->load->view('curriculum/form/form_create_curriculum_subject', $this->a_page_data, true);
                print($s_page);
            }
        }
    }

    public function filter_curriculum_lists()
    {
        if ($this->input->is_ajax_request()) {
            $a_term = $this->input->post('term');
            $s_this_os = $this->input->post('this_os');

            $mba_curriculum_name_list = $this->Cm->get_curriculum_filtered($a_term);
            if ($s_this_os == 'true') {
                if ($mba_curriculum_name_list) {
                    foreach ($mba_curriculum_name_list as $key => $curriculum) {
                        $mba_curriculum_subject = $this->Cm->get_curriculum_subject_filtered(array('rcs.curriculum_id' => $curriculum->curriculum_id));
                        $mba_curriculum_semester = $this->Cm->get_curriculum_semester_filtered(array('rcs.curriculum_id' => $curriculum->curriculum_id));
                        
                        if (!$mba_curriculum_subject) {
                            unset($mba_curriculum_name_list[$key]);
                        }else if($mba_curriculum_semester){
                            $curriculum->semester_lists = $mba_curriculum_semester;
                        }
                    }
                }

                $data = array();
                $i = 0;
                if ($mba_curriculum_name_list) {
                    foreach ($mba_curriculum_name_list as $curr) {
                        $data[$i] = $curr;
                        $i++;
                    }
                }
                
                $a_return = array('code' => 0, 'data' => $data);
            }else{
                if ($mba_curriculum_name_list) {
                    foreach ($mba_curriculum_name_list as $curriculum) {
                        $mba_curriculum_subject = $this->Cm->get_curriculum_subject_filtered(array('rcs.curriculum_id' => $curriculum->curriculum_id));
                        $curriculum->subject_count = ($mba_curriculum_subject) ? count($mba_curriculum_subject) : 0;
                    }
                }
                
                $a_return = array('code' => 0, 'data' => $mba_curriculum_name_list);
            }
			print json_encode($a_return);
			exit;
        }
    }

    public function filter_curriculum_semester_lists()
    {
        if ($this->input->is_ajax_request()) {
            $s_curriculum_id = $this->input->post('curriculum_id');
            $mba_curriculum_semester_lists = $this->Cm->get_curriculum_semester($s_curriculum_id);
            $a_return = array('code' => 0, 'data' => $mba_curriculum_semester_lists);
            print json_encode($a_return);exit;
        }
    }

    public function filter_curriculum_subject_lists()
    {
        if ($this->input->is_ajax_request()) {
            $s_semester_id = $this->input->post('semester_id');
            $s_curriculum_id = $this->input->post('curriculum_id');
            
			$mba_curriculum_name_list = $this->Cm->get_curriculum_subject_list($s_curriculum_id, $s_semester_id);
			$a_return = array('code' => 0, 'data' => $mba_curriculum_name_list);
			print json_encode($a_return);
			exit;
        }
    }

    public function delete_curriculum()
    {
        if ($this->input->is_ajax_request()) {
            $s_curriculum_id = $this->input->post('curriculum_id');
            if ($this->Cm->delete_curriculum($s_curriculum_id)) {
                $a_return = array('code' => 0, 'message' => 'success');
            }else{
                $a_return = array('code' => 1, 'message' => 'Failed remove curriculum data');
            }

            print json_encode($a_return);exit;
        }
    }

    public function delete_subject_curriculum()
    {
        if ($this->input->is_ajax_request()) {
            $s_curriculum_subject_id = $this->input->post('curriculum_subject_id');
            
            if ($this->Cm->delete_curriculum_subject(array('curriculum_subject_id' => $s_curriculum_subject_id))) {
                $a_rtn = array('code' => 0, 'message' => 'Success remove data');
            }else{
                $a_rtn = array('code' => 1, 'message' => 'Error remove data');
            }

            print json_encode($a_rtn);
        }
    }

    public function save_curriculum_semester_credit()
    {
        if ($this->input->is_ajax_request()) {
            $s_curriculum_id = $this->input->post('curriculum_id');
            $s_semester_key = $this->input->post('semester_key_id');
            $s_term = $this->input->post('term');

            if ($s_semester_key == '') {
                $this->form_validation->set_rules('semester_id', 'Semester', 'required');
            }
            // $this->form_validation->set_rules('curriculum_semester_total_credit_mandatory_fixed', 'Total Credit Mandatory Fixed', 'trim|required|numeric');
            // $this->form_validation->set_rules('curriculum_semester_total_credit_elective_fixed', 'Total Credit Elective Fixed', 'trim|required|numeric');
            // $this->form_validation->set_rules('curriculum_semester_total_credit_extracurricular_fixed', 'Total Credit Extracurricular Fixed', 'trim|required|numeric');

            $this->form_validation->set_rules('curriculum_semester_total_credit_mandatory_fixed', 'Total Credit Mandatory Fixed', 'trim|numeric');
            $this->form_validation->set_rules('curriculum_semester_total_credit_elective_fixed', 'Total Credit Elective Fixed', 'trim|numeric');
            $this->form_validation->set_rules('curriculum_semester_total_credit_extracurricular_fixed', 'Total Credit Extracurricular Fixed', 'trim|numeric');
            
            if ($this->form_validation->run()) {
                $mbo_curriculum_semester = $this->Cm->get_curriculum_semester($s_curriculum_id, set_value('semester_id'));
                
                $a_curriculum_semester_data = array(
                    'curriculum_semester_total_credit_mandatory_fixed' => (set_value('curriculum_semester_total_credit_mandatory_fixed') == '') ? 0 : set_value('curriculum_semester_total_credit_mandatory_fixed'),
                    'curriculum_semester_total_credit_elective_fixed' => (set_value('curriculum_semester_total_credit_elective_fixed') == '') ? 0 : set_value('curriculum_semester_total_credit_elective_fixed'),
                    'curriculum_semester_total_credit_extracurricular_fixed' => (set_value('curriculum_semester_total_credit_extracurricular_fixed') == '') ? 0 : set_value('curriculum_semester_total_credit_extracurricular_fixed')
                );
                
                $this->db->trans_start();

                if (($s_semester_key == '') OR ($s_term == 'copy')) {
                    if ($mbo_curriculum_semester) {
                        $a_rtn = array('code' => 1, 'message' => $mbo_curriculum_semester[0]->semester_name.' already exists');
                        print json_encode($a_rtn);exit;
                    }else{
                        $a_curriculum_semester_data['curriculum_id'] = $s_curriculum_id;
                        $a_curriculum_semester_data['semester_id'] = set_value('semester_id');
                        if ($this->Cm->create_new_curriculum_semester($a_curriculum_semester_data)) {
                            $a_rtn = array('code' => 0, 'message' => 'Success saving data');
                            if ($s_term == 'copy') {
                                if ($this->input->post('copy_subject') == 'on') {
                                    $mbo_curriculum_subject_data = $this->Cm->get_curriculum_subject_list($s_curriculum_id, $s_semester_key);
                                    
                                    if ($mbo_curriculum_subject_data) {
                                        foreach ($mbo_curriculum_subject_data as $subject) {
                                            $o_semester_data = $this->Cm->get_semester_list(set_value('semester_id'))[0];
                                            $s_prodi_abbr = ($subject->program_id == $this->a_programs['NI S1']) ? $subject->study_program_ni_abbreviation : $subject->study_program_abbreviation;
                                            $s_curriculum_subject_code = $this->generate_curriculum_subject_code($subject->subject_name_code, $s_prodi_abbr, set_value('semester_id'), $o_semester_data->semester_type_id);
                                            $a_curriculum_subject_data = array(
                                                'curriculum_subject_id' => $this->uuid->v4(),
                                                'curriculum_id' => $s_curriculum_id,
                                                'semester_id' => set_value('semester_id'),
                                                'subject_id' => $subject->subject_id,
                                                'curriculum_subject_code' => $s_curriculum_subject_code,
                                                'curriculum_subject_credit' => $subject->subject_credit,
                                                'curriculum_subject_type' => $subject->curriculum_subject_type,
                                                'curriculum_subject_ects' => $subject->curriculum_subject_ects
                                            );

                                            if ($this->Cm->create_new_curriculum_subject($a_curriculum_subject_data)) {
                                                $a_rtn = array('code' => 0, 'message' => 'Success saving data', 'data:' => $a_curriculum_subject_data);
                                            }else{
                                                $a_rtn = array('code' => 1, 'message' => 'Error copying data', 'data:' => $a_curriculum_subject_data);
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }else{
                            $a_rtn = array('code' => 1, 'message' => 'Error saving data');
                        }
                    }
                }else{
                    $a_clause_update = array('curriculum_id' => $s_curriculum_id, 'semester_id' => $s_semester_key);
                    if ($this->Cm->update_curriculum_semester($a_curriculum_semester_data, $a_clause_update)) {
                        $a_rtn = array('code' => 0, 'message' => 'Success updating data');
                    }else{
                        $a_rtn = array('code' => 1, 'message' => 'Error updating data');
                    }
                }

                $this->Cm->update_credit_curriculum($s_curriculum_id);

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    $a_rtn = array('code' => 1, 'message' => 'Error proccessing data');
                }else{
                    $this->db->trans_commit();
                    $a_rtn = array('code' => 0, 'message' => 'Success');
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }
        }else{
            $a_rtn = array('code' => 1, 'message' => 'Nothing action');
        }

        print json_encode($a_rtn);exit;
    }

    public function save_curriculum_subject()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('academic/Subject_model', 'Sm');
            $s_curriculum_id = $this->input->post('curriculum_id');
            $s_subject_id = $this->input->post('subject_id');
            
            $this->form_validation->set_rules('semester_id', 'Semester', 'required');
            $this->form_validation->set_rules('curriculum_subject_type', 'Curriculum Subject Type', 'required');
            $this->form_validation->set_rules('curriculum_subject_category', 'Curriculum Subject Category', 'required');

            if ($s_subject_id == '') {
                $this->form_validation->set_rules('subject_name', 'Subject Name', 'trim|required');
                $this->form_validation->set_rules('subject_code', 'Subject Code', 'trim');
                $this->form_validation->set_rules('subject_type', 'Subject Type', 'required');
                $this->form_validation->set_rules('subject_credit', 'Subject Credit', 'trim|required|numeric');
            }

            if ($this->form_validation->run()) {
                $o_semester_data = $this->Cm->get_semester_list(set_value('semester_id'))[0];
                $mbo_curriculum_data = $this->Cm->get_curriculum_data($s_curriculum_id);

                if ($mbo_curriculum_data) {
                    $this->db->trans_start();

                    $s_study_program_id = $mbo_curriculum_data[0]->study_program_id;
                    $s_study_program_abbreviation = ($mbo_curriculum_data[0]->program_id == $this->a_programs['NI S1']) ? $mbo_curriculum_data[0]->study_program_ni_abbreviation : $mbo_curriculum_data[0]->study_program_abbreviation;
                    // $s_study_program_abbreviation = $mbo_curriculum_data[0]->study_program_abbreviation;
                    $s_academic_year_id = $mbo_curriculum_data[0]->academic_year_id;

                    if ($s_subject_id == '') {
                        $a_request_data = array(
                            'subject_name_id' => '',
                            'subject_name' => set_value('subject_name'),
                            'subject_id' => $s_subject_id,
                            'subject_code' => set_value('subject_code'),
                            'program_id' => $mbo_curriculum_data[0]->program_id,
                            'study_program_id' => $s_study_program_id,
                            'id_jenis_mata_kuliah' => set_value('subject_type'),
                            'subject_credit' => set_value('subject_credit'),
                            'subject_credit_tm' => set_value('subject_credit')
                        );
        
                        $a_save_subject = modules::run('academic/subject/save_subject_data', $a_request_data);
                        if ($a_save_subject['code'] == 0) {
                            $a_subject_data = $a_save_subject['data'];
                            $s_subject_id = $a_subject_data['subject_id'];
                            $s_subject_name_id = $a_subject_data['subject_name_id'];
                            $s_subject_name_code = $a_subject_data['subject_name_code'];
                            $s_subject_credit = $a_subject_data['subject_credit'];
                        }else{
                            print json_encode($a_save_subject);exit;
                        }                        
                    }else{
                        $mbo_subject_data = $this->Sm->get_subject_filtered(array('subject_id' => $s_subject_id))[0];
                        $s_subject_name_code = $mbo_subject_data->subject_name_code;
                        $s_subject_credit = $mbo_subject_data->subject_credit;
                    }

                    $i_credit_ects = round((intval($s_subject_credit) * 1.4), 2);

                    $s_curriculum_subject_code = $this->generate_curriculum_subject_code($s_subject_name_code, $s_study_program_abbreviation, $o_semester_data->semester_id, $o_semester_data->semester_type_id);
                    $a_curriculum_subject_data = array(
                        'curriculum_subject_id' => $this->uuid->v4(),
                        'curriculum_id' => $s_curriculum_id,
                        'semester_id' => set_value('semester_id'),
                        'subject_id' => $s_subject_id,
                        'curriculum_subject_code' => $s_curriculum_subject_code,
                        'curriculum_subject_category' => set_value('curriculum_subject_category'),
                        'curriculum_subject_credit' => (set_value('curriculum_subject_category') == 'regular semester') ? $s_subject_credit : 0,
                        'curriculum_subject_type' => set_value('curriculum_subject_type'),
                        'curriculum_subject_ects' => $i_credit_ects
                    );

                    if ($this->input->post('curriculum_subject_id') == '') {
                        $check_curriculum_subject = $this->Cm->get_curriculum_subject_filtered(array(
                            'rcs.curriculum_id' => $s_curriculum_id,
                            'rcs.semester_id' => set_value('semester_id'),
                            'rsn.subject_name' => set_value('subject_name'),
                            'rcs.curriculum_subject_category' => set_value('curriculum_subject_category')
                        ));

                        if ($check_curriculum_subject) {
                            $a_rtn = array('code' => 1, 'message' => 'Subject exists in this curriculum semester');
                            print json_encode($a_rtn);exit;
                        }else{
                            $this->Cm->create_new_curriculum_subject($a_curriculum_subject_data);
                        }
                    }else{
                        $o_curriculum_data = $this->Cm->get_curriculum_subject_filtered(array(
                            'rcs.curriculum_subject_id' => $this->input->post('curriculum_subject_id')
                        ))[0];

                        if ($o_curriculum_data->semester_id == set_value('semester_id')) {
                            $this->Cm->update_curriculum_subject($a_curriculum_subject_data, $this->input->post('curriculum_subject_id'));
                        }else{
                            $check_curriculum_subject = $this->Cm->get_curriculum_subject_filtered(array(
                                'rcs.curriculum_id' => $s_curriculum_id,
                                'rcs.semester_id' => set_value('semester_id'),
                                'rsn.subject_name' => set_value('subject_name'),
                                'rcs.curriculum_subject_category' => set_value('curriculum_subject_category')
                            ));

                            if ($check_curriculum_subject) {
                                $a_rtn = array('code' => 1, 'message' => 'Subject exists in this curriculum semester');
                                print json_encode($a_rtn);exit;
                            }else{
                                $this->Cm->update_curriculum_subject($a_curriculum_subject_data, $this->input->post('curriculum_subject_id'));
                            }
                        }                        
                    }

                    $this->Cm->update_credit_count_curriculum_semester($s_curriculum_id, set_value('semester_id'), set_value('curriculum_subject_type'));

                    if ($this->db->trans_status() === false) {
                        $this->db->trans_rollback();
                        $a_rtn = array('code' => 1, 'message' => 'Error proccessing data');
                    }else{
                        $this->db->trans_commit();
                        $a_rtn = array('code' => 0, 'message' => 'Success create new subject curriculum');
                    }
                }else{
                    $a_rtn = array('code' => 1, 'message' => 'error system, please contact IT Team');
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }
        }else{
            $a_rtn = array('code' => 1, 'message' => 'Nothing action');
        }

        print json_encode($a_rtn);exit;
    }

    public function save_curriculum_data()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('Subject_model', 'Sm');
            
            // if ($mbo_academic_year_active) {
            $this->form_validation->set_rules('curriculum_name','Curriculum Name', 'trim|required');
            $this->form_validation->set_rules('study_program_id','Study Program', 'required');
            $this->form_validation->set_rules('academic_year_id', 'Active Year', 'required');
            $this->form_validation->set_rules('program_id', 'Program ID', 'required');
            $s_term = $this->input->post('term');

            if ($this->form_validation->run()) {
                $a_data_curriculum = array(
                    'study_program_id' => set_value('study_program_id'),
                    'academic_year_id' => set_value('academic_year_id'),
                    'program_id' => set_value('program_id'),
                    'valid_academic_year' => set_value('academic_year_id'),
                    'curriculum_name' => set_value('curriculum_name')
                );

                $a_clause_check_curriculum = array(
                    'academic_year_id' => set_value('academic_year_id'),
                    'rc.program_id' => set_value('program_id'),
                    'study_program_id' => set_value('study_program_id')
                );

                if ($s_term == 'edit') {
                    $a_clause_check_curriculum['curriculum_id !='] = $this->input->post('curriculum_id');
                }

                $mbo_curriculum_data = $this->Cm->get_curriculum_lists($a_clause_check_curriculum);
                if ($mbo_curriculum_data) {
                    $s_study_program_name = ($mbo_curriculum_data[0]->program_id == $this->a_programs['NI S1']) ? $mbo_curriculum_data[0]->study_program_ni_name : $mbo_curriculum_data[0]->study_program_name;
                    $rtn = array('code' => 1, 'message' => 'Curriculum with Program '.$mbo_curriculum_data[0]->program_name.' Study Program '.$s_study_program_name.' batch '.$mbo_curriculum_data[0]->academic_year_id.' already exists');
                }else{
                    $this->db->trans_start();

                    if (($this->input->post('curriculum_id') == '') OR ($s_term == 'copy')) {
                        $a_data_curriculum['curriculum_id'] = $this->uuid->v4();
                        $this->Cm->create_new_curriculum($a_data_curriculum);
                        if ($s_term == 'copy') {
                            if ($this->input->post('copy_subject') == 'on'){
                                $mbo_curriculum_semester_list = $this->Cm->get_curriculum_semester_filtered(array('curriculum_id' => $this->input->post('curriculum_id')));
                                $mbo_curriculum_subject_list = $this->Cm->get_curriculum_subject_filtered(array('curriculum_id' => $this->input->post('curriculum_id')));
                                if ($mbo_curriculum_semester_list) {
                                    foreach ($mbo_curriculum_semester_list as $cur_semester) {
                                        $a_curriculum_semester_data = array(
                                            'curriculum_id' => $a_data_curriculum['curriculum_id'],
                                            'semester_id' => $cur_semester->semester_id,
                                            'curriculum_semester_total_credit_mandatory_fixed' => $cur_semester->curriculum_semester_total_credit_mandatory_fixed,
                                            'curriculum_semester_total_credit_elective_fixed' => $cur_semester->curriculum_semester_total_credit_elective_fixed,
                                            'curriculum_semester_total_credit_extracurricular_fixed' => $cur_semester->curriculum_semester_total_credit_extracurricular_fixed
                                        );

                                        $this->Cm->create_new_curriculum_semester($a_curriculum_semester_data);
                                    }
                                }

                                if ($mbo_curriculum_subject_list) {
                                    foreach ($mbo_curriculum_subject_list as $cur_subject) {
                                        $o_subject_data = $this->Sm->get_subject_filtered(array('subject_id' => $cur_subject->subject_id));
                                        $o_study_program_data = $this->Spm->get_study_program(set_value('study_program_id'));
                                        $o_semester_data = $this->Cm->get_semester_list($cur_subject->semester_id);
                                        $s_prodi_abbr = (set_value('program_id') == $this->a_programs['NI S1']) ? $o_study_program_data[0]->study_program_ni_abbreviation : $o_study_program_data[0]->study_program_abbreviation;
                                        $a_curriculum_subject_data = array(
                                            'curriculum_subject_id' => $this->uuid->v4(),
                                            'curriculum_id' => $a_data_curriculum['curriculum_id'],
                                            'semester_id' => $cur_subject->semester_id,
                                            'subject_id' => $cur_subject->subject_id,
                                            'curriculum_subject_code' => $this->generate_curriculum_subject_code($o_subject_data[0]->subject_name_code, $s_prodi_abbr, $o_semester_data[0]->semester_id, $o_semester_data[0]->semester_type_id),
                                            'curriculum_subject_credit' =>  $cur_subject->curriculum_subject_credit,
                                            'curriculum_subject_ects' => $cur_subject->curriculum_subject_ects,
                                            'curriculum_subject_type' => $cur_subject->curriculum_subject_type
                                        );

                                        $this->Cm->create_new_curriculum_subject($a_curriculum_subject_data);
                                    }
                                }
                            }
                        }
                    }else{
                        $s_curriculum_id = $this->input->post('curriculum_id');
                        $this->Cm->update_curriculum($a_data_curriculum, $s_curriculum_id);
                    }

                    if ($this->db->trans_status() === false) {
                        $this->db->trans_rollback();
                        $rtn = array('code' => 0, 'message' => 'Error saving curriculum');
                    }else{
                        $this->db->trans_commit();
                        $rtn = array('code' => 0, 'message' => 'Success saving curriculum');
                    }
                }
            }else{
                $rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }
        }else{
            $rtn = array('code' => 1, 'message' => 'Nothing action');
        }

        print json_encode($rtn);exit;
    }

    public function generate_curriculum_subject_code($s_subject_name_code, $s_study_program_abbreviation, $semester_id, $semester_type_id)
    {
        $s_subject_code = preg_replace('/[^A-Za-z]/', '', $s_subject_name_code);
        $i_subject_code = preg_replace('/[^0-9]/', '', $s_subject_name_code);

        $count_curriculum_list = count($this->Cm->get_curriculum_subject_filtered());
        $s_padding = '';
        for ($i=0; $i < 3-strlen($count_curriculum_list); $i++) { 
            $s_padding .= '0';
        }
        $curriculum_count_id = $s_padding.$count_curriculum_list;

        $s_curriculum_subject_code = $s_subject_code.'-'.$i_subject_code.$s_study_program_abbreviation.$semester_id.'-'.$curriculum_count_id.$semester_type_id;
        return $s_curriculum_subject_code;
    }
}
