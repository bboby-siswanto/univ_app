<?php
class Activity_study extends App_core
{
    function __construct()
    {
        parent::__construct('academic');
        $this->load->model('academic/Activity_study_model', 'Asm');
        $this->load->model('academic/Academic_year_model', 'Aym');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
    }

    public function tab_activity_examiner_form($mbo_activity_data)
    {
        $this->a_page_data['dikti_kategori_kegiatan'] = $this->Asm->get_dikti_kategori_kegiatan();
        $this->a_page_data['activity_study'] = $mbo_activity_data;
        $this->a_page_data['activity_lecturer_type'] = 'Examiner';

        $this->load->view('activity_study/activity_examiner', $this->a_page_data);
    }
    
    public function tab_activity_adviser_form($mbo_activity_data)
    {
        $this->a_page_data['dikti_kategori_kegiatan'] = $this->Asm->get_dikti_kategori_kegiatan();
        $this->a_page_data['activity_study'] = $mbo_activity_data;
        $this->a_page_data['activity_lecturer_type'] = 'Adviser';

        $this->load->view('activity_study/activity_adviser', $this->a_page_data);
    }

    public function save_lecturer_activity()
    {
        if ($this->input->is_ajax_request()) {
            $s_activity_lecturer_type = strtolower($this->input->post('activity_lecturer_type'));

            $this->form_validation->set_rules('activity_study_id', 'System', 'required');
            $this->form_validation->set_rules('employee_id', 'Lecturer', 'required');
            $this->form_validation->set_rules('activity_lecturer_sequence', 'Sequence', 'required');
            $this->form_validation->set_rules('id_kategori_kegiatan', 'Category', 'required');

            if ($this->form_validation->run()) {
                $a_data = [
                    'activity_lecturer_id' => $this->uuid->v4(),
                    'activity_study_id' => set_value('activity_study_id'),
                    'id_kategori_kegiatan' => set_value('id_kategori_kegiatan'),
                    'employee_id' => set_value('employee_id'),
                    'activity_lecturer_sequence' => set_value('activity_lecturer_sequence'),
                    'activity_lecturer_type' => $s_activity_lecturer_type,
                ];

                if ($this->Asm->save_lecturer_activity($a_data)) {
                    
                    if ($s_activity_lecturer_type == 'adviser') {
                        $dikti_sync = modules::run('feeder/activity/sync_pembimbing_aktvitas', $a_data['activity_lecturer_id'], 'execute', true);
                    }else{
                        $dikti_sync = modules::run('feeder/activity/sync_penguji_aktvitas', $a_data['activity_lecturer_id']);
                    }

                    // if ($dikti_sync == '') {
                        $a_rtn = array('code' => 0, 'message' => 'Success!');
                    // }else{
                    //     $a_rtn = array('code' => 1, 'message' => $dikti_sync);
                    // }

                }else{
                    $a_rtn = array('code' => 1, 'message' => 'Failed saving data');
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_rtn);
        }
    }

    public function force_sync_student_activity()
    {
        if ($this->input->is_ajax_request()) {
            $s_activity_student_id = $this->input->post('activity_student_id');
            
            $dikti_sync = modules::run('feeder/activity/sync_peserta_aktivitas', $s_activity_student_id);

            $a_return = ($dikti_sync == '') ? ['code' => 0, 'message' => 'Success'] : ['code' => 1, 'message' => $dikti_sync];
            print json_encode($a_return);
        }
    }

    public function force_sync_lecturer_activity()
    {
        if ($this->input->is_ajax_request()) {
            $s_activity_lecturer_id = $this->input->post('activity_lecturer_id');
            $s_activity_lecturer_type = $this->input->post('activity_lecturer_type');
            
            if ($s_activity_lecturer_type == 'adviser') {
                $dikti_sync = modules::run('feeder/activity/sync_pembimbing_aktvitas', $s_activity_lecturer_id);
            }else{
                $dikti_sync = modules::run('feeder/activity/sync_penguji_aktvitas', $s_activity_lecturer_id);
            }

            $a_return = ($dikti_sync == '') ? ['code' => 0, 'message' => 'Success'] : ['code' => 1, 'message' => $dikti_sync];
            print json_encode($a_return);
        }
    }

    public function save_student_activity()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('activity_study_id', 'System', 'required');
            $this->form_validation->set_rules('student_id', 'Student', 'required');
            $this->form_validation->set_rules('role_type', 'Member Role', 'required');

            if ($this->form_validation->run()) {
                $a_data = [
                    'activity_student_id' => $this->uuid->v4(),
                    'activity_study_id' => set_value('activity_study_id'),
                    'student_id' => set_value('student_id'),
                    'role_type' => set_value('role_type')
                ];

                if ($this->Asm->save_student_activity($a_data)) {
                    $dikti_sync = modules::run('feeder/activity/sync_peserta_aktivitas', $a_data['activity_student_id']);

                    // if ($dikti_sync == '') {
                        $a_rtn = array('code' => 0, 'message' => 'Success!');
                    // }else{
                    //     $a_rtn = array('code' => 1, 'message' => $dikti_sync);
                    // }

                }else{
                    $a_rtn = array('code' => 1, 'message' => 'Failed saving data');
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_rtn);
        }
    }

    public function save_activity()
    {
        if ($this->input->is_ajax_request()) {
            $s_activity_study_id = $this->input->post('activity_study_id');
            $this->form_validation->set_rules('academic_year_id', 'Running Year', 'required');
            $this->form_validation->set_rules('semester_type_id', 'Semester Type', 'required');
            $this->form_validation->set_rules('study_program_id', 'Study Program', 'required');
            $this->form_validation->set_rules('id_jenis_aktivitas_mahasiswa', 'Type of Activity', 'required');
            $this->form_validation->set_rules('activity_member_type', 'Member Type', 'required');
            $this->form_validation->set_rules('activity_title', 'Title', 'required|trim');

            if ($this->form_validation->run()) {
                // $this->db->trans_start();
                $a_data = [
                    'activity_study_id' => $this->uuid->v4(),
                    'academic_year_id' => set_value('academic_year_id'),
                    'semester_type_id' => set_value('semester_type_id'),
                    'study_program_id' => set_value('study_program_id'),
                    'id_jenis_aktivitas_mahasiswa' => set_value('id_jenis_aktivitas_mahasiswa'),
                    'activity_member_type' => set_value('activity_member_type'),
                    'activity_title' => set_value('activity_title'),
                    'activity_location' => ($this->input->post('activity_location') != '') ? $this->input->post('activity_location') : null,
                    'activity_sk_number' => ($this->input->post('activity_sk_number') != '') ? $this->input->post('activity_sk_number') : null,
                    'activity_sk_date' => ($this->input->post('activity_sk_date') != '') ? $this->input->post('activity_sk_date') : null,
                    'activity_remarks' => ($this->input->post('activity_remarks') != '') ? $this->input->post('activity_remarks') : null
                ];

                if ($s_activity_study_id != '') {
                    unset($a_data['activity_study_id']);

                    $save_data = $this->Asm->save_activity_study($a_data, $s_activity_study_id);
                    // $this->db->update('dt_activity_study', $a_data, ['activity_study_id' => $s_activity_study_id]);
                }else{
                    $s_activity_study_id = $a_data['activity_study_id'];
                    $save_data = $this->Asm->save_activity_study($a_data);
                    // $this->db->insert('dt_activity_study', $a_data);
                }

                if ($save_data) {
                    $sync_data = modules::run('feeder/activity/sync_activity', $s_activity_study_id);
                    $s_id_aktivitas = $sync_data['id_aktivitas'];
                    $a_rtn = array('code' => 0, 'message' => 'Success!', 'activity_id' => $s_id_aktivitas);
                }else{
                    $a_rtn = array('code' => 1, 'message' => 'Failed saving data');
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_rtn);
        }
    }

    public function tab_activity_student_form($mbo_activity_data)
    {   
        if ($mbo_activity_data->activity_member_type == 0) {
            $a_role_list = [ '3' => 'Personal' ];
        }else{
            $a_role_list = [ '1' => 'Head', '2' => 'Member'];
        }

        $s_academic_year_id = $mbo_activity_data->academic_year_id;
        $s_semester_type_id = $mbo_activity_data->semester_type_id;
        $mbo_semester_data = $this->Smm->get_semester_setting(['dss.academic_year_id' => $s_academic_year_id, 'dss.semester_type_id' => $s_semester_type_id])[0];
        $s_semester_end_date = date('Y-m-d', strtotime($mbo_semester_data->semester_end_date));

        $mba_student_list = $this->Stm->get_student_list_data(false, ['active', 'inactive', 'graduated', 'onleave', 'dropout']);
        if ($mba_student_list) {
            foreach ($mba_student_list as $key => $o_student) {
                if (!is_null($o_student->student_date_graduated)) {
                    $s_student_graduuated_date = date('Y-m-d', strtotime($o_student->student_date_graduated));
                    // if ($s_student_graduuated_date < $s_semester_end_date) {
                    //     unset($mba_student_list[$key]);
                    // }
                }
            }

            $mba_student_list = array_values($mba_student_list);
        }

        $this->a_page_data['student_list'] = $mba_student_list;
        $this->a_page_data['role_list'] = $a_role_list;
        $this->a_page_data['activity_study'] = $mbo_activity_data;
        $this->load->view('activity_study/activity_student', $this->a_page_data);
    }

    public function delete_activity_student()
    {
        if ($this->input->is_ajax_request()) {
            $s_activity_student_id = $this->input->post('activity_student_id');

            $dikti_sync = modules::run('feeder/activity/sync_peserta_aktivitas', $s_activity_student_id, 'delete');

            if ($this->Asm->delete_activity_student($s_activity_student_id)) {
                
                // if ($dikti_sync == '') {
                    $a_return = array('code' => 0, 'message' => 'Success!');
                // }else{
                //     $a_return = array('code' => 1, 'message' => 'Error sync to dikti, message: '.$dikti_sync);
                // }

            }else{
                $a_return = ['code' => 1, 'message' => 'Fail remove data!'];
            }

            print json_encode($a_return);
        }
    }
    
    public function delete_activity_lecturer()
    {
        if ($this->input->is_ajax_request()) {
            $s_activity_lecturer_id = $this->input->post('activity_lecturer_id');
            $mbo_activity_lecturer = $this->Asm->get_activity_lecturer(['al.activity_lecturer_id' => $s_activity_lecturer_id])[0];

            if ($mbo_activity_lecturer->activity_lecturer_type == 'adviser') {
                $dikti_sync = modules::run('feeder/activity/sync_pembimbing_aktvitas', $s_activity_lecturer_id, 'delete');
            }else{
                $dikti_sync = modules::run('feeder/activity/sync_penguji_aktvitas', $s_activity_lecturer_id, 'delete');
            }

            if ($this->Asm->delete_activity_lecturer($s_activity_lecturer_id)) {
                
                // if ($dikti_sync == '') {
                    $a_return = array('code' => 0, 'message' => 'Success!');
                // }else{
                //     $a_return = array('code' => 1, 'message' => 'Error sync to dikti, message: '.$dikti_sync);
                // }

            }else{
                $a_return = ['code' => 1, 'message' => 'Fail remove data!'];
            }

            print json_encode($a_return);
        }
    }

    public function get_list_activity_lecturer()
    {
        if ($this->input->is_ajax_request()) {
            $s_activity_study_id = $this->input->post('activity_study_id');
            $s_activity_lecturer_type = $this->input->post('lecturer_type');

            $mba_activity_lecturer = $this->Asm->get_activity_lecturer(['al.activity_study_id' => $s_activity_study_id, 'al.activity_lecturer_type' => strtolower($s_activity_lecturer_type)]);
            print json_encode(['code' => 0, 'data' => $mba_activity_lecturer]);
        }
    }

    public function get_list_activity_student()
    {
        if ($this->input->is_ajax_request()) {
            $s_activity_study_id = $this->input->post('activity_study_id');

            $mba_activities_student_list = $this->Asm->get_activity_student_data(['dast.activity_study_id' => $s_activity_study_id]);

            print json_encode(['code' => 0, 'data' => $mba_activities_student_list]);
        }
    }

    public function get_list_activity()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_study_program_id = $this->input->post('study_program_id');

            $a_filter_data = [
                'das.academic_year_id' => $this->input->post('academic_year_id'),
                'das.semester_type_id' => $this->input->post('semester_type_id'),
                'das.study_program_id' => $this->input->post('study_program_id')
            ];

            if ($s_study_program_id == 'all') {
                unset($a_filter_data['das.study_program_id']);
            }

            $mba_activities_data = $this->Asm->get_activity_data($a_filter_data);

            print json_encode(['code' => 0, 'data' => $mba_activities_data]);
        }
    }

    public function form_input_activity_study()
    {
        $this->a_page_data['study_program_list'] = $this->Spm->get_study_program(false, false);
        $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, array(1,2,7,8));
        $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
        $this->a_page_data['dikti_jenis_aktivitas'] = $this->Asm->get_dikti_jenis_aktivitas();

        $this->load->view('activity_study/form/form_input_activity_study', $this->a_page_data);
    }
    
    public function activity_study_table_list()
    {
        $this->load->view('activity_study/table/activity_list_table', $this->a_page_data);
    }

    public function activity_study_list($s_activity_study_id = false)
    {
        if ($s_activity_study_id) {
            $mba_activities_data = $this->Asm->get_activity_data(['das.activity_study_id' => $s_activity_study_id]);
            if ($mba_activities_data) {
                $this->a_page_data['activity_data'] = $mba_activities_data[0];
                $this->a_page_data['body'] = $this->load->view('activity_study/activity_data', $this->a_page_data, true);
            }else{
                $this->a_page_data['body'] = $this->load->view('activity_study/activity_list', $this->a_page_data, true);
            }

        }else{
            $this->a_page_data['body'] = $this->load->view('activity_study/activity_list', $this->a_page_data, true);
        }
        
        $this->load->view('layout', $this->a_page_data);
    }

    public function form_filter_activity_study()
    {
        $this->a_page_data['study_program_list'] = $this->Spm->get_study_program(false, false);
        $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, array(1,2,7,8));
        $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();

        $this->load->view('activity_study/form/form_filter_activity_study', $this->a_page_data);
    }

    public function from_defense() {
        if ($this->input->is_ajax_request()) {
            $this->load->model('academic/Activity_study_model', 'Asem');
            $this->load->model('thesis/Thesis_model', 'Tesi');

            $s_defense_id = $this->input->post('defense_id');
            $s_activity_study_id = $this->uuid->v4();
            $mba_defense = $this->Tesi->get_thesis_defense_student([
                'td.thesis_defense_id' => $s_defense_id,
            ]);

            if ($mba_defense) {
                $o_defense = $mba_defense[0];
                $a_activity_data = [
                    'activity_study_id' => $s_activity_study_id,
                    'academic_year_id' => $o_defense->defense_academic_year_id,
                    'semester_type_id' => $o_defense->defense_semester_type_id,
                    'program_id' => '1',
                    'study_program_id' => $o_defense->study_program_id,
                    'id_jenis_aktivitas_mahasiswa' => '22',
                    'activity_member_type' => '0',
                    'activity_title' => $o_defense->thesis_title,
                    'activity_location' => 'Tangerang Selatan'
                ];
    
                $this->Asem->save_activity_study($a_activity_data);
                $sync_data = modules::run('feeder/activity/sync_activity', $a_activity_data['activity_study_id']);
                $s_activity_study_id = $sync_data['id_aktivitas'];

                $s_activity_student_id = $this->uuid->v4();
                $a_actstudent_data = [
                    'activity_student_id' => $s_activity_student_id,
                    'activity_study_id' => $s_activity_study_id,
                    'student_id' => $o_defense->student_id,
                    'role_type' => '3'
                ];

                if ($this->Asem->save_student_activity($a_actstudent_data)) {
                    $dikti_sync = modules::run('feeder/activity/sync_peserta_aktivitas', $s_activity_student_id);
                }

                $mba_advisor_data = $this->Tesi->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_defense->thesis_students_id
                ], 'advisor', true);
                $mba_examiner_data = $this->Tesi->get_list_student_advisor([
                    'ts.thesis_student_id' => $o_defense->thesis_students_id
                ], 'examiner');

                if ($mba_examiner_data) {
                    foreach ($mba_examiner_data as $o_examiner) {
                        $s_activity_lecturer_id = $this->uuid->v4();
                        $sequence = substr($o_examiner->examiner_type, (strlen($o_examiner->examiner_type) - 1), 1);
                        $a_data = [
                            'activity_lecturer_id' => $s_activity_lecturer_id,
                            'activity_study_id' => $s_activity_study_id,
                            'id_kategori_kegiatan' => ($o_examiner->examiner_type == 'examiner_1') ? '110501' : '110502',
                            'employee_id' => $o_examiner->employee_id,
                            'activity_lecturer_sequence' => $sequence,
                            'activity_lecturer_type' => 'examiner',
                        ];
                        if ($this->Asem->save_lecturer_activity($a_data)) {
                            modules::run('feeder/activity/sync_penguji_aktvitas', $s_activity_lecturer_id);
                        }
                    }
                }
                if ($mba_advisor_data) {
                    foreach ($mba_advisor_data as $o_advisor) {
                        $s_activity_lecturer_id = $this->uuid->v4();
                        $sequence = substr($o_advisor->advisor_type, (strlen($o_advisor->advisor_type) - 1), 1);
                        $a_data = [
                            'activity_lecturer_id' => $s_activity_lecturer_id,
                            'activity_study_id' => $s_activity_study_id,
                            'id_kategori_kegiatan' => ($o_advisor->advisor_type == 'approved_advisor_1') ? '110403' : '110407',
                            'employee_id' => $o_advisor->employee_id,
                            'activity_lecturer_sequence' => $sequence,
                            'activity_lecturer_type' => 'adviser',
                        ];
                        if ($this->Asem->save_lecturer_activity($a_data)) {
                            modules::run('feeder/activity/sync_pembimbing_aktvitas', $s_activity_lecturer_id);
                        }
                    }
                }

                $a_return = ['code' => 0, 'message' => 'OK!'];
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Defense not found!'];
            }

            print json_encode($a_return);
        }
    }
}
