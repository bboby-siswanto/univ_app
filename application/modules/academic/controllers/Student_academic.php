<?php
class Student_academic extends App_core
{
    public function __construct()
    {
        parent::__construct('academic');
        $this->load->model('student/Student_model', 'Sm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('thesis/Thesis_model', 'Tsm');
        $this->load->model('academic/Class_group_model', 'Cgm');
        $this->load->model('employee/Employee_model', 'Emm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('academic/Academic_year_model','Aym');
        $this->load->model('krs/Krs_model','Krm');
        $this->load->model('personal_data/Family_model', 'Fmm');
        $this->load->model('institution/Institution_model', 'Inm');
        $this->load->model('study_program/Study_program_model', 'Spm');
    }

    function graduation_registration() {
        $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
        $this->a_page_data['option_checklist'] = $this->General->get_enum_values('dt_graduation_checklist', 'checklist_type');;
        // print('<pre>');var_dump($this->a_page_data['option_checklist']);exit;
        $this->a_page_data['body'] = $this->load->view('academic/table/graduation_registration', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function get_graduation_registration() {
        if ($this->input->is_ajax_request()) {
            $s_year = $this->input->post('graduation_registration_year');
            $mba_data = $this->Sm->get_student_filtered([
                'ds.student_graduation_registration' => $s_year
            ]);
            if ($mba_data) {
                foreach ($mba_data as $o_student) {
                    $mba_checklist_student = $this->Smm->get_graduation_checklist([
                        'gc.student_id' => $o_student->student_id
                    ]);
                    $mba_thesis_final_student = $this->Tsm->get_thesis_list_files([
                        'ts.student_id' => $o_student->student_id,
                        'sf.thesis_filetype' => 'thesis_final_file'
                    ]);
                    // $has_paid_graduation_fee = $this->;
                    $o_student->checklist_data = $mba_checklist_student;
                    $o_student->thesis_final_submit = $mba_thesis_final_student[0];
                }
            }

            print json_encode(['data' => $mba_data]);
        }
    }

    function remove_checklist() {
        if ($this->input->is_ajax_request()) {
            $s_checklist_id = $this->input->post('check_id');
            if (empty($s_checklist_id)) {
                $a_return = ['code' => 1, 'message' => 'Checklist not found!'];
            }
            else {
                $this->General->force_delete('dt_graduation_checklist', 'checklist_id', $s_checklist_id);
                $a_return = ['code' => 0, 'message' => 'Success'];
            }
            print json_encode($a_return);
        }
    }

    public function submit_check_graduation() {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('check_student_id');
            $s_checklist_type = $this->input->post('check_type');
            $s_checklist_note = $this->input->post('input_note');
            
            $mba_checklist_exist = $this->General->get_where('dt_graduation_checklist', [
                'student_id' => $s_student_id,
                'checklist_type' => $s_checklist_type,
            ]);

            $a_data = [
                'checklist_id' => $this->uuid->v4(),
                'student_id' => $s_student_id,
                'checklist_type' => $s_checklist_type,
                'checklist_result' => 'TRUE',
                'checklist_note' => $s_checklist_note,
                'checklist_by' => $this->session->userdata('user'),
                'date_added' => date('Y-m-d H:i:s')
            ];
            if ($mba_checklist_exist) {
                unset($a_data['checklist_id']);
                $this->General->update_data('dt_graduation_checklist', $a_data, ['checklist_id' => $mba_checklist_exist[0]->checklist_id]);
            }
            else {
                $this->General->insert_data('dt_graduation_checklist', $a_data);
            }

            $a_return = ['code' => 0, 'message' => 'Success'];
            print json_encode($a_return);
        }
    }

    public function supplement_list() {
        $this->a_page_data['body'] = $this->load->view('student/supplement/supplement_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function generate_ijazah()
    {
        // print json_encode(['code' => 1, 'message' => 'this feature under maintenance!']);exit;
        $a_kalender_indo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        if ($this->input->is_ajax_request()) {
            $s_mode = $this->input->post('mode');
            $s_program = $this->input->post('program');
            $s_graduation_date = '2023-09-22';

            $mba_student_data = false;
            if ($s_mode == 'single') {
                $mba_student_data = $this->Sm->get_student_filtered([
                    'ds.student_id' => $this->input->post('student_id')
                ]);
            }
            else if ($s_mode == 'bulk') {
                $a_student_id_list = $this->input->post('student_id');
                if (is_array($a_student_id_list)) {
                    if (count($a_student_id_list) > 0) {
                        $mba_student_data = [];
                        foreach ($a_student_id_list as $s_student_id) {
                            $mba_student_data_list = $this->Sm->get_student_filtered([
                                'ds.student_id' => $s_student_id
                            ]);

                            if ($mba_student_data_list) {
                                array_push($mba_student_data, $mba_student_data_list[0]);
                            }
                        }
                        $mba_student_data = (count($mba_student_data) > 0) ? array_values($mba_student_data) : false;
                    }
                }
            }

            if ($mba_student_data) {
                $a_filename_file = [];
                $a_path_file = [];

                foreach ($mba_student_data as $o_student_data) {
                    // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                    //     print('<pre>');var_dump($o_student_data->deans_id);exit;
                    // }
                    if (!is_null($o_student_data->study_program_main_id)) {
                        $mba_program_study_program_data = $this->Spm->get_study_program_lists([
                            'rpsp.study_program_id' => $o_student_data->study_program_main_id,
                            'rpsp.program_id' => $o_student_data->program_id
                        ]);
                    }
                    else {
                        $mba_program_study_program_data = $this->Spm->get_study_program_lists([
                            'rpsp.study_program_id' => $o_student_data->study_program_id,
                            'rpsp.program_id' => $o_student_data->program_id
                        ]);
                    }

                    $mba_student_major_data = false;
                    if (!is_null($o_student_data->study_program_majoring_id)) {
                        $mba_student_major_data = $this->General->get_where('ref_study_program_majoring', [
                            'study_program_majoring_id' => $o_student_data->study_program_majoring_id
                        ]);
                    }

                    $mba_country_birthday = (!is_null($o_student_data->country_of_birth)) ? $this->General->get_where('ref_country', ['country_id' => $o_student_data->country_of_birth]) : false;

                    $a_data = [
                        'program' => $s_program,
                        'pin_number' => $o_student_data->student_pin_number,
                        'student_name' => $o_student_data->personal_data_name,
                        'personal_data_id' => $o_student_data->personal_data_id,
                        'call_gender' => ($o_student_data->personal_data_gender == 'M') ? 'him' : 'her',
                        'student_number' => $o_student_data->student_number,
                        'date_birth_indo' => date('d', strtotime($o_student_data->personal_data_date_of_birth)).' '.$a_kalender_indo[intval(date('m', strtotime($o_student_data->personal_data_date_of_birth)))].' '.date('Y', strtotime($o_student_data->personal_data_date_of_birth)),
                        'date_birth' => $o_student_data->personal_data_date_of_birth,
                        'place_birth' => $o_student_data->personal_data_place_of_birth,
                        'country_birth' => ($mba_country_birthday) ? $mba_country_birthday[0]->country_name : '',
                        'batch' => $o_student_data->academic_year_id,
                        'prodi_name' => $mba_program_study_program_data[0]->study_program_name,
                        'prodi_abbreviation' => $mba_program_study_program_data[0]->study_program_abbreviation,
                        'prodi_name_feeder' => $mba_program_study_program_data[0]->study_program_name_feeder,
                        'gradute_date_indo' => date('d', strtotime($o_student_data->student_date_graduated)).' '.$a_kalender_indo[intval(date('m', strtotime($o_student_data->student_date_graduated)))].' '.date('Y', strtotime($o_student_data->student_date_graduated)),
                        'gradute_date' => $o_student_data->student_date_graduated,
                        'degree_name' => ($mba_program_study_program_data) ? $mba_program_study_program_data[0]->degree_name : '',
                        'degree_abbreviation' => ($mba_program_study_program_data) ? $mba_program_study_program_data[0]->degree_abbreviation : '',
                        'deans_id' => $o_student_data->deans_id,
                        'head_of_department_id' => $mba_program_study_program_data[0]->head_of_study_program_id,
                        'concentration' => (($mba_student_major_data) AND (!is_null($mba_student_major_data[0]->majoring_name))) ? 'majoring in '.$mba_student_major_data[0]->majoring_name : '',
                        'konsentrasi' => (($mba_student_major_data) AND (!is_null($mba_student_major_data[0]->majoring_name_indo))) ? 'konsentrasi '.$mba_student_major_data[0]->majoring_name_indo : '',
                        'faculty_id' => $mba_program_study_program_data[0]->faculty_id,
                        'faculty_name' => $mba_program_study_program_data[0]->faculty_name,
                        'faculty_name_feeder' => $mba_program_study_program_data[0]->faculty_name_feeder,
                        'sk_prodi' => $o_student_data->study_program_sk_accreditation,
                        // 'current_date_indo' => date('d').' '.$a_kalender_indo[intval(date('m'))].' '.date('Y'),
                        // 'current_date' => date('d F Y'),
                        'current_date_indo' => date('d', strtotime($s_graduation_date)).' '.$a_kalender_indo[intval(date('m', strtotime($s_graduation_date)))].' '.date('Y', strtotime($s_graduation_date)),
                        'current_date' => date('d F Y', strtotime($s_graduation_date))
                    ];
                    // print('<pre>');var_dump($mba_program_study_program_data);exit;

                    $generate_file = modules::run('download/doc_download/generate_ijazah', $a_data);
                    if ($generate_file['code'] == 0) {
                        if ($generate_file['count'] > 0) {
                            foreach ($generate_file['files'] as $s_files) {
                                $s_filepath = APPPATH.'uploads/'.$o_student_data->personal_data_id.'/ijazah/'.urldecode($s_files);
                                array_push($a_path_file, $s_filepath);
                                array_push($a_filename_file, urldecode($s_files));
                            }
                        }
                        
                        // $s_filepath = APPPATH.'uploads/'.$generate_file['personal_data_id'].'/ijazah/'.urldecode($generate_file['file']);
                        // array_push($a_path_file, $s_filepath);
                        // array_push($a_filename_file, urldecode($generate_file['file']));
                        // array_push($a_file_generated, $generate_file['personal_data_id'].'/ijazah/'.$generate_file['file']);
                    }
                }

                if (count($a_filename_file) > 0) {
                    $employee_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $this->session->userdata('user')))[0];
                    if ($employee_data) {
                        $s_email_to = $employee_data->employee_email;
                        $s_message = 'Ijazah Generated:<br><li>';
                        $s_message .= implode('</li><li>', $a_filename_file);
                        $s_message .= '</li>';

                        $config = $this->config->item('mail_config');
                        $config['mailtype'] = 'html';
                        $this->email->initialize($config);

                        $this->email->from('employee@company.ac.id', 'IULI Academic Service Centre');
                        $this->email->to($s_email_to);
                        if ($s_email_to != 'employee@company.ac.id') {
                            // $this->email->bcc('employee@company.ac.id');
                        }

                        $this->email->subject('[Academic Services] Graduated Ijazah');
                        $this->email->message($s_message);

                        foreach ($a_path_file as $s_path) {
                            $this->email->attach($s_path);
                        }

                        if(!$this->email->send()){
                            $this->log_activity('Email did not sent');
                            print('<pre>');var_dump($this->email->print_debugger());exit;
                            // $this->log_activity('Error Message: '.$this->email->print_debugger());
                        }
                    }
                    $a_return = ['code' => 0, 'message' => 'Files has been sent to your email!'];
                }
                else {
                    $a_return = ['code' => 3, 'message' => 'No file generated!'];
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => 'No student data found!'];
            }

            print json_encode($a_return);
        }
    }

    public function transcript_flying_faculty()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $a_generated_file = modules::run('download/doc_download/generate_flying_faculty_transcript', $s_student_id);
            
            print json_encode($a_generated_file);
        }
    }

    public function registration_graduation_year() {
        if ($this->input->is_ajax_request()) {
            $student_id = $this->input->post('student_id');
            $s_graduate_year = $this->input->post('graduate_year');

            $mba_student_data = $this->General->get_where('dt_student', [
                'student_id' => $student_id
            ]);
            if ($mba_student_data) {
                $a_data = [
                    'student_graduation_registration' => (empty($s_graduate_year)) ? NULL : $s_graduate_year
                ];
                $this->Sm->update_student_data($a_data, $student_id);
                $a_return = ['code' => 0, 'message' => 'Success'];
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Student data not found!!'];
            }

            print json_encode($a_return);exit;
        }
    }

    public function mark_submitted_thesis_proposal()
    {
        if ($this->input->is_ajax_request()) {
            $student_id = $this->input->post('student_id');

            $o_student_data = $this->General->get_where('dt_student', [
                'student_id' => $student_id
            ])[0];

            $a_data = false;
            if (($o_student_data) AND ($o_student_data->student_mark_submitted_thesis_proposal == 1)) {
                $a_data = ['student_mark_submitted_thesis_proposal' => NULL];
            }else if (($o_student_data) AND (is_null($o_student_data->student_mark_submitted_thesis_proposal))) {
                $a_data = ['student_mark_submitted_thesis_proposal' => 1];
            }

            if ($a_data) {
                $this->Sm->update_student_data($a_data, $student_id);

                $a_return = ['code' => 0, 'message' => 'Success'];
            }else{
                $a_return = ['code' => 1, 'message' => 'Error processing data'];
            }

            print json_encode($a_return);
        }
    }

    public function mark_completed_defense()
    {
        if ($this->input->is_ajax_request()) {
            $student_id = $this->input->post('student_id');

            $o_student_data = $this->General->get_where('dt_student', [
                'student_id' => $student_id
            ])[0];

            $a_data = false;
            if (($o_student_data) AND ($o_student_data->student_mark_completed_defense == 1)) {
                $a_data = ['student_mark_completed_defense' => NULL];
            }else if (($o_student_data) AND (is_null($o_student_data->student_mark_completed_defense))) {
                $a_data = ['student_mark_completed_defense' => 1];
            }

            if ($a_data) {
                $this->Sm->update_student_data($a_data, $student_id);

                $a_return = ['code' => 0, 'message' => 'Success'];
            }else{
                $a_return = ['code' => 1, 'message' => 'Error processing data'];
            }

            print json_encode($a_return);
        }
    }

    public function form_view_internship()
    {
        $this->load->view('academic/internship/internship_page', $this->a_page_data);
    }

    public function form_english_as_medium_instruction()
    {
        $date = date('Y-m-d');
        $this->a_page_data['date_now'] = $date;
        $this->a_page_data['month_roman'] = $this->iuli_lib->numberToRomanRepresentation(intval(date('m', strtotime($date))));
        $this->a_page_data['year_data'] = date('Y', strtotime($date));
        $this->load->view('form/form_english_as_medium_instruction', $this->a_page_data);
    }
    
    public function form_letter_temporary_graduation()
    {
        $date = date('Y-m-d');
        $this->a_page_data['date_now'] = $date;
        $this->a_page_data['month_roman'] = $this->iuli_lib->numberToRomanRepresentation(intval(date('m', strtotime($date))));
        $this->a_page_data['year_data'] = date('Y', strtotime($date));
        $this->load->view('form/form_letter_temporary_graduation', $this->a_page_data);
    }

    public function ref_letter_germany()
	{
        $date = date('Y-m-d');
        $this->a_page_data['date_now'] = $date;
        $this->a_page_data['month_roman'] = $this->iuli_lib->numberToRomanRepresentation(intval(date('m', strtotime($date))));
        $this->a_page_data['year_data'] = date('Y', strtotime($date));
        $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
		$this->load->view('academic/form/form_ref_letter_to_germany', $this->a_page_data);
	}

    public function generate_graduated_transcript()
    {
        if ($this->input->is_ajax_request()) {
            $a_filter_data = $this->input->post();
            $passed_defense = $this->input->post('passed_defense');
            
			foreach($a_filter_data as $key => $value){
                if (in_array($key, ['academic_year_id', 'study_program_id'])) {
                    if (is_array($value)) {
                        unset($a_filter_data[$key]);
                    }
                    else if($a_filter_data[$key] == 'all'){
                        unset($a_filter_data[$key]);
                    }
                    else{
                        $new_key = 'ds.'.$key;
                        $a_filter_data[$new_key] = $value;
                        unset($a_filter_data[$key]);
                    }
                }else{
                    unset($a_filter_data[$key]);
                }
                
			}

            // $a_student_filter = array(
            //     'ds.academic_year_id' => $this->input->post('academic_year_id'),
            //     'ds.study_program_id' => $this->input->post('study_program_id')
            // );

            // if ($this->input->post('academic_year_id') != 'all') {
            //     $a_student_filter['ds.academic_year_id'] = $this->input->post('academic_year_id');
            // }
            
            // if ($this->input->post('study_program_id') != 'all') {
            //     $a_student_filter['ds.study_program_id'] = $this->input->post('study_program_id');
            // }

			$status_filter = false;
			if (is_array($this->input->post('student_status'))) {
				if (count($this->input->post('student_status')) > 0) {
					$status_filter = $this->input->post('student_status');
				}
			}

            $prodi_filter = false;
			if (is_array($this->input->post('study_program_id'))) {
				if (count($this->input->post('study_program_id')) > 0) {
					$prodi_filter = $this->input->post('study_program_id');
				}
			}
			
            $mba_student_data = $this->Sm->get_student_filtered($a_filter_data, $status_filter, false, $prodi_filter);
            $a_file_transcript = array();
            $a_file_name_transcript = array();
            // print('<pre>');
            if ($mba_student_data) {
                $s_graduation_date = (($this->input->post('graduation_date')) AND ($this->input->post('graduation_date') != 'false')) ? $this->input->post('graduation_date') : false;
                $s_rector_date = (($this->input->post('transcript_date')) AND ($this->input->post('transcript_date') != 'false')) ? $this->input->post('transcript_date') : false;
                $s_ijd_date = (($this->input->post('ijd_date')) AND ($this->input->post('ijd_date') != 'false')) ? $this->input->post('ijd_date') : false;

                foreach ($mba_student_data as $key => $o_student_data) {
                    if ($passed_defense !== null) {
						if ((is_null($o_student_data->student_mark_completed_defense)) OR ($o_student_data->student_mark_completed_defense != '1')) {
                            continue;
						}
					}
                    // generate_transcript_graduated($s_student_id, $s_degree, $s_graduation_date, $s_rector_date, $s_ijd_date);
                    $mba_file_transcript = modules::run('download/excel_download/generate_transcript_graduated', $o_student_data->student_id, $this->input->post('transcript_degree'), $s_graduation_date, $s_rector_date, $s_ijd_date);
                    if ($mba_file_transcript) {
                        array_push($a_file_transcript, $mba_file_transcript['filepath'].$mba_file_transcript['filename']);
                        array_push($a_file_name_transcript, $mba_file_transcript['filename']);
                    }else{
                        print($o_student_data->personal_data_name);exit;
                    }
                }
            }else{
                print('<pre>');
                var_dump($a_student_filter);exit;
                $a_return = array('code' => 1, 'message' => 'No student found!');
                print json_encode($a_return);exit;
            }

            if (count($a_file_transcript) > 0) {
                $employee_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $this->session->userdata('user')))[0];
                if ($employee_data) {
                    $s_email_to = $employee_data->employee_email;
                    $s_message = 'File Transcript Generated:<br><li>';
                    $s_message .= implode('</li><li>', $a_file_name_transcript);
                    $s_message .= '</li>';

                    // $config = $this->config->item('mail_config');
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);

                    $this->email->from('employee@company.ac.id', 'IULI Academic Service Centre');
                    $this->email->to($s_email_to);
                    if ($s_email_to != 'employee@company.ac.id') {
                        // $this->email->bcc('employee@company.ac.id');
                    }

                    $this->email->subject('[Academic Services] Graduated Transcript');
                    $this->email->message($s_message);

                    foreach ($a_file_transcript as $s_transcript_file) {
                        $this->email->attach($s_transcript_file);
                    }

                    if(!$this->email->send()){
                        $this->log_activity('Email did not sent');
                        // $this->log_activity('Error Message: '.$this->email->print_debugger());
                        
                        $a_return = array('code' => 1, 'message' => 'Error send transcript file!');
                    }
                    else{
                        $a_return = array('code' => 0, 'message' => 'success!');
                    }

                }else{
                    $a_return = array('code' => 1, 'message' => 'Your data not found on database!');
                }
            }else{
                $a_return = array('code' => 1, 'message' => 'No transcript file generated!');
            }

            print json_encode($a_return);
        }
    }

    public function form_input_internhip_application_modal()
    {
        // $s_form = '';
        $s_form = $this->load->view('form/form_application_internship', $this->a_page_data, true);
        return $s_form;
    }

    function delete_student_semester() {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');

            $delete = $this->Krm->detele_student_semester([
                'student_id' => $s_student_id,
                'academic_year_id' => $s_academic_year_id,
                'semester_type_id' => $s_semester_type_id
            ]);
            if ($delete) {
                $a_return = ['code' => 0, 'message' => 'Success'];
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Failed remove data!'];
            }

            print json_encode($a_return);
        }
    }

    function get_historical_study() {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $mba_student_semester_data = $this->Krm->get_student_semester($s_student_id, false, ['dss.academic_year_id' => 'asc', 'dss.semester_type_id' => 'asc']);
            if ($mba_student_semester_data) {
                $a_merit_total = array();
                $a_credit_total = array();
                $a_semester_list = array();
                
                foreach ($mba_student_semester_data as $o_student_semester) {
                    $mba_score_data = $this->Scm->get_score_data(array(
                        'sc.student_id' => $s_student_id,
                        'sc.academic_year_id' => $o_student_semester->academic_year_id,
                        'sc.semester_type_id' => $o_student_semester->semester_type_id,
                        'sc.score_approval' => 'approved',
                        'curs.curriculum_subject_type !=' => 'extracurricular',
                        'sc.score_display' => 'TRUE'
                    ));
                    $mbo_student_semester_data = $this->Smm->get_semester_student($s_student_id, [
                        'ss.academic_year_id' => $o_student_semester->academic_year_id,
                        'ss.semester_type_id' => $o_student_semester->semester_type_id,
                        'ss.semester_id' => $o_student_semester->semester_id
                    ]);
                    $a_credit_sum = array();
                    $a_merit_sum = array();
                    $s_student_semester = (!is_null($o_student_semester->semester_number)) ? $o_student_semester->semester_number : '';
                    $s_student_semester .= '/';
                    
                    if ($mba_score_data) {
                        if (!is_null($mba_score_data[0]->semester_id)) {
                            $score_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $mba_score_data[0]->semester_id]);
                            $s_student_semester .= ($score_semester_data) ? $score_semester_data[0]->semester_number : '';
                        }
                        foreach ($mba_score_data as $o_score) {
                            $score_sum = intval(round($o_score->score_sum, 0, PHP_ROUND_HALF_UP));
                            $score_grade = $this->grades->get_grade($score_sum);
                            $score_grade_point = $this->grades->get_grade_point($score_sum);
                            $score_merit = $this->grades->get_merit($o_score->curriculum_subject_credit, $score_grade_point);

                            array_push($a_credit_sum, $o_score->curriculum_subject_credit);
                            array_push($a_merit_sum, $score_merit);

                            array_push($a_credit_total, $o_score->curriculum_subject_credit);
                            array_push($a_merit_total, $score_merit);
                        }
                    }

                    $s_sum_credit = array_sum($a_credit_sum);
                    $s_sum_merit = array_sum($a_merit_sum);

                    $s_sum_credit_total = array_sum($a_credit_total);
                    $s_sum_merit_total = array_sum($a_merit_total);

                    $o_student_semester->semester_status = $o_student_semester->student_semester_status;
                    $o_student_semester->credit = $s_sum_credit;
                    $o_student_semester->student_semester_number = $s_student_semester;
                    $o_student_semester->credit_cummulative = $s_sum_credit_total;
                    $o_student_semester->cummulative_semester_score = $this->grades->get_ipk($s_sum_merit, $s_sum_credit);
                    $o_student_semester->cummulative_score = $this->grades->get_ipk($s_sum_merit_total, $s_sum_credit_total);
                    $o_student_semester->student_semester = ($mbo_student_semester_data) ? $mbo_student_semester_data[0] : false;
                }
            }

            print json_encode(['data' => $mba_student_semester_data]);
        }
    }

    public function get_historical_score()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_ofse_score = $this->input->post('ofse_score');
            
            if ($s_ofse_score !== null) {
                $a_semester_in = array(4, 6);
            }else{
                $a_semester_in = array(1, 2, 7, 8);
            }

            $mba_data = $this->Scm->get_historycal_score($s_student_id, $a_semester_in);
            // print('<pre>');var_dump($mba_data);exit;

            $a_merit_total = array();
            $a_credit_total = array();
            $a_semester_list = array();
            $a_data = array();
            
            if ($mba_data) {
                foreach ($mba_data as $key => $o_data) {
                    $s_semester_list = $o_data->academic_year_id.$o_data->semester_type_id;

                    if (!in_array($s_semester_list, $a_semester_list)) {
                        $mba_semester_status = $this->Smm->get_semester_student($s_student_id, array(
                            'ss.academic_year_id' => $o_data->academic_year_id,
                            'ss.semester_type_id' => $o_data->semester_type_id
                        ));
                        $mba_student_semester_data = false;
                        if (($mba_semester_status) AND (!is_null($mba_semester_status))) {
                            $mba_student_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $mba_semester_status[0]->semester_id]);
                        }
                        array_push($a_semester_list, $s_semester_list);

                        $mba_score_data = $this->Scm->get_score_data(array(
                            'sc.student_id' => $s_student_id,
                            'sc.academic_year_id' => $o_data->semester_academic_year_id,
                            'sc.semester_type_id' => $o_data->semester_type_id,
                            'sc.score_approval' => 'approved',
                            'curs.curriculum_subject_type !=' => 'extracurricular',
                            'sc.score_display' => 'TRUE'
                        ));

                        $a_credit_sum = array();
                        $a_merit_sum = array();

                        if ($mba_score_data) {
                            $score_semester_data = false;
                            if (!is_null($mba_score_data[0]->semester_id)) {
                                $score_semester_data = $this->General->get_where('ref_semester', ['semester_id' => $mba_score_data[0]->semester_id]);
                            }
                            foreach ($mba_score_data as $o_score) {
                                $score_sum = intval(round($o_score->score_sum, 0, PHP_ROUND_HALF_UP));
                                $score_grade = $this->grades->get_grade($score_sum);
                                $score_grade_point = $this->grades->get_grade_point($score_sum);
                                $score_merit = $this->grades->get_merit($o_score->curriculum_subject_credit, $score_grade_point);

                                array_push($a_credit_sum, $o_score->curriculum_subject_credit);
                                array_push($a_merit_sum, $score_merit);

                                array_push($a_credit_total, $o_score->curriculum_subject_credit);
                                array_push($a_merit_total, $score_merit);
                            }
                        }


                        $s_student_semester = ($mba_student_semester_data) ? $mba_student_semester_data[0]->semester_number : '';
                        if ($score_semester_data) {
                            $s_student_semester .= "/".$score_semester_data[0]->semester_number;
                        }
                        $s_sum_credit = array_sum($a_credit_sum);
                        $s_sum_merit = array_sum($a_merit_sum);

                        $s_sum_credit_total = array_sum($a_credit_total);
                        $s_sum_merit_total = array_sum($a_merit_total);

                        $mba_data[$key]->semester_status = ($mba_semester_status) ? $mba_semester_status[0]->student_semester_status : '-';
                        $mba_data[$key]->credit = $s_sum_credit;
                        $mba_data[$key]->student_semester_number = $s_student_semester;
                        $mba_data[$key]->credit_cummulative = $s_sum_credit_total;
                        $mba_data[$key]->cummulative_semester_score = $this->grades->get_ipk($s_sum_merit, $s_sum_credit);
                        $mba_data[$key]->cummulative_score = $this->grades->get_ipk($s_sum_merit_total, $s_sum_credit_total);

                        $a_param_student_semester = [
                            // 'student_id' => $o_data->student_id,
                            'ss.academic_year_id' => $o_data->semester_academic_year_id,
                            'ss.semester_type_id' => $o_data->semester_type_id,
                            'ss.semester_id' => $o_data->semester_id
                        ];

                        // $mbo_student_semester_data = $this->General->get_where('dt_student_semester', $a_param_student_semester)[0];
                        $mbo_student_semester_data = $this->Smm->get_semester_student($o_data->student_id, $a_param_student_semester)[0];

                        // $s_study_location = '';
                        // if ($mbo_student_semester_data) {
                        //     $mbo_institution_id = $this->General->get_where('ref_institution', [
                        //         'institution_id' => $mbo_student_semester_data->institution_id
                        //     ])[0];

                        //     if ($mbo_institution_id) {
                        //         $s_study_location = $mbo_institution_id->institution_name;
                        //     }
                            
                        // }

                        $mba_data[$key]->student_semester = $mbo_student_semester_data;
                    }else{
                        unset($mba_data[$key]);
                    }
                }
                $mba_data = array_values($mba_data);
            }

            print json_encode(array('data' => $mba_data));
        }
    }

    public function save_institution_semester()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_semester_id = $this->input->post('semester_id');

            $s_institution_id = $this->input->post('institution_id');

            if ($s_institution_id == '') {
                $a_return = ['code' => 1, 'message' => 'University field is required!'];
            }else if (($s_student_id == '') OR ($s_academic_year_id == '') OR ($s_semester_type_id == '') OR ($s_semester_id == '')) {
                $a_return = ['code' => 1, 'message' => 'Error system!'];
            }else{
                $a_data = ['institution_id' => $s_institution_id];

                $a_update_param = [
                    'student_id' => $s_student_id,
                    'academic_year_id' => $s_academic_year_id,
                    'semester_type_id' => $s_semester_type_id,
                    'semester_id' => $s_semester_id
                ];

                $this->Sm->save_student_semester($a_data, $a_update_param);

                $a_return = ['code' => 0, 'message' => 'Success!'];
            }

            print json_encode($a_return);
        }
    }

    public function get_student_study()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');

            $mba_student_semester = $this->Smm->get_semester_student($s_student_id);
            print json_encode(array('code' => 0, 'data' => $mba_student_semester));
        }
    }

    public function activity_study($s_student_id)
    {
        $mbo_student_data = $this->Sm->get_student_filtered(array('ds.student_id' => $s_student_id))[0];
        if ($mbo_student_data) {
            $this->a_page_data['student_id'] = $s_student_id;
            $this->a_page_data['university_list'] = $this->Inm->get_institution_data([
                'ri.institution_type != ' => 'highschool'
            ]);
            $this->a_page_data['body'] = $this->load->view('student/table/activity_study', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }else{
            show_404();
        }
    }

    public function student_lists()
    {
        $o_semester_active = $this->Smm->get_active_semester();
        // var_dump($o_semester_active);exit;
		$this->a_page_data['draft_transcript_template'] = modules::run('messaging/text_template/final_transcript', array(
			'semester_type_name' => strtolower($o_semester_active->semester_type_name)
        ));
        $this->a_page_data['draft_transcript_template'] = trim(preg_replace('/\s\s+/', '<br/>', $this->a_page_data['draft_transcript_template']));
        
        $this->a_page_data['mbo_semester_type'] = $this->Smm->get_semester_type_lists(false, array('semester_type_id !=' => 5));
        $this->a_page_data['mbo_academic_year'] = $this->Aym->get_academic_year_lists();
        $this->a_page_data['message_to'] = '';
        $this->a_page_data['transcript_body'] = '';
        $this->a_page_data['body'] = $this->load->view('student/academic_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function student_list_table()
    {
        $this->a_page_data['accepted_button'] = false;
        if (in_array(intval($this->a_user_roles), array(1, 2, 3, 4, 8, 9))) {
            $this->a_page_data['accepted_button'] = true;
        }
        $this->a_page_data['o_year_now'] = $this->Smm->get_semester_setting(array('semester_status' => 'active'))[0];
        $this->load->view('student/table/academic_student_list_table', $this->a_page_data);
    }

    public function student_setting($s_student_id = null)
	{
        // if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        //     print('<h2>mohon maaf!<br>sedang penambahan feature.. :D</h2>');exit;
        // }

		if (!is_null($s_student_id)) {
            $this->a_page_data['study_program_lists'] = $this->Spm->get_study_program(false, false);
            $this->a_page_data['academic_year_list'] = $this->Aym->get_academic_year_lists();
            $this->a_page_data['semester_type_list'] = $this->Smm->get_semester_type_lists(false, false, [1,2]);
		    $this->a_page_data['semester_list'] = $this->General->get_in('ref_semester', 'semester_type_id', ['1', '2']);
			$this->a_page_data['student_data'] = $this->Sm->get_student_filtered(array('ds.student_id' => $s_student_id))[0];
			$this->a_page_data['personal_data_id'] = $this->a_page_data['student_data']->personal_data_id;
			$this->a_page_data['body'] = $this->load->view('student/settings', $this->a_page_data, true);
			$this->load->view('layout', $this->a_page_data);
		}
    }
    
    public function download_cummulative_gpa2()
    {
        if ($this->input->is_ajax_request()) {
            $s_batch = $this->input->post('academic_year_id');
            // $s_study_program_id = $this->input->post('study_program_id');
            $s_academic_year_id = $this->input->post('gpa_recap_academic_year_id');
            $s_semester_type_id = $this->input->post('gpa_recap_semester_type_id');
            $s_all_checked = $this->input->post('gpa_recap_all_semester');
            $s_feeder_check = $this->input->post('gpa_feeder_check');
            $b_all_checked = ($s_all_checked !== null)  ? true : false;
            $b_feeder_check = ($s_feeder_check !== null)  ? true : false;

            $b_short_semester = $this->input->post('include_short_semester');
            $b_short_semester = ($b_short_semester !== null)  ? true : false;

            $b_repeat = $this->input->post('include_repeat');
            $b_repeat = ($b_repeat !== null)  ? true : false;

            $passed_defense = $this->input->post('passed_defense');
            $passed_defense = ($passed_defense !== null) ? true : false;

            $mba_student_status = false;
            if (is_array($this->input->post('student_status'))) {
                if (count($this->input->post('student_status')) > 0) {
                    $mba_student_status = $this->input->post('student_status');
                }
            }

            $prodi_filter = false;
			if (is_array($this->input->post('study_program_id'))) {
				if (count($this->input->post('study_program_id')) > 0) {
					$prodi_filter = $this->input->post('study_program_id');
				}
			}

            // print("<pre>");var_dump(implode("','", $prodi_filter));exit;
			// $mba_filtered_data = $this->Sm->get_student_filtered($a_filter_data, $status_filter, false, $prodi_filter);

            // $s_student_batch,
            // $b_passed_deffence = false,
            // $s_study_program_id,
            // $a_student_status = false,
            // $b_semester_selected = false, // $b_last_semester = false,
            // $b_last_short_semester = false,
            // $b_last_repetition = true,
            // $s_academic_year_id = false,
            // $s_semester_type_id = false

            if ($b_all_checked) {
                $a_student_gpa_result = modules::run('download/excel_download/generate_cummulative_gpa2', $s_batch, $passed_defense, $prodi_filter, $mba_student_status, false, false, true, false, false, $b_feeder_check);
            }
            else if ($b_feeder_check) {
                // print('<pre>');var_dump($b_short_semester);exit;
                // print(, $, $mba_student_status, true, $, $b_repeat, $s_academic_year_id, $s_semester_type_id, $b_feeder_check);exit;
                $a_student_gpa_result = modules::run('download/excel_download/generate_cummulative_gpa_feeder', $s_batch, $passed_defense, $prodi_filter, $mba_student_status, true, $b_short_semester, $b_repeat, $s_academic_year_id, $s_semester_type_id, $b_feeder_check);
            }
            else {
                $a_student_gpa_result = modules::run('download/excel_download/generate_cummulative_gpa2', $s_batch, $passed_defense, $prodi_filter, $mba_student_status, true, $b_short_semester, $b_repeat, $s_academic_year_id, $s_semester_type_id, $b_feeder_check);
            }
            // print('<pre>');var_dump($a_student_gpa_result);exit;
    
            if ($a_student_gpa_result) {
                $a_return  = array('code' => 0, 'data' => $a_student_gpa_result);
            }else{
                $a_return  = array('code' => 1, 'message' => 'No Student data found');
            }
    
            print json_encode($a_return);
        }
    }

    public function download_cummulative_gpa()
    {
        // old
        $s_academic_year_id = $this->input->post('academic_year_id');
        $s_study_program_id = $this->input->post('study_program_id');
        $s_option_semester = $this->input->post('option_semester');
        $passed_defense = $this->input->post('passed_defense');

        $b_short_semester = $this->input->post('include_short_semester');
        $b_short_semester = ($b_short_semester !== null)  ? true : false;

        $b_repeat = $this->input->post('include_repeat');
        $b_repeat = ($b_repeat !== null)  ? true : false;
        
        $mba_student_status = false;
        if (is_array($this->input->post('student_status'))) {
            if (count($this->input->post('student_status')) > 0) {
                $mba_student_status = $this->input->post('student_status');
            }
        }

        if ($passed_defense !== null) {
            $passed_defense = true;
        }else{
            $passed_defense = false;
        }

        if ($s_option_semester == 'prev_semester') {
            $a_student_gpa_result = modules::run('download/excel_download/generate_cummulative_gpa', $s_academic_year_id, $passed_defense, $s_study_program_id, $mba_student_status, true, $b_short_semester, $b_repeat);
        }else{
            $a_student_gpa_result = modules::run('download/excel_download/generate_cummulative_gpa', $s_academic_year_id, $passed_defense, $s_study_program_id, $mba_student_status);
        }

        if ($a_student_gpa_result) {
            $a_return  = array('code' => 0, 'data' => $a_student_gpa_result);
        }else{
            $a_return  = array('code' => 1, 'message' => 'No Student data found');
        }

        print json_encode($a_return);
        // print('<pre>');
        // var_dump($a_student_gpa_result);
    }

    // buat dipakai sendiri untuk validasi forlap
    public function download_internal_cummulative_gpa()
    {
        $s_academic_year_id = $this->input->post('academic_year_id');
        $s_study_program_id = $this->input->post('study_program_id');
        $s_option_semester = $this->input->post('option_semester');
        $passed_defense = $this->input->post('passed_defense');

        $b_short_semester = $this->input->post('include_short_semester');
        $b_short_semester = ($b_short_semester !== null)  ? true : false;

        $b_repeat = $this->input->post('include_repeat');
        $b_repeat = ($b_repeat !== null)  ? true : false;
        
        $mba_student_status = false;
        if (is_array($this->input->post('student_status'))) {
            if (count($this->input->post('student_status')) > 0) {
                $mba_student_status = $this->input->post('student_status');
            }
        }

        if ($passed_defense !== null) {
            $passed_defense = true;
        }else{
            $passed_defense = false;
        }

        if ($s_option_semester == 'prev_semester') {
            $a_student_gpa_result = modules::run('download/excel_download/generate_gpa_recapitulation_for_internal_check', $s_academic_year_id, $passed_defense, $s_study_program_id, $mba_student_status, true, $b_short_semester, $b_repeat, true);
        }else{
            $a_student_gpa_result = modules::run('download/excel_download/generate_gpa_recapitulation_for_internal_check', $s_academic_year_id, $passed_defense, $s_study_program_id, $mba_student_status, false, false, true, true);
        }

        if ($a_student_gpa_result) {
            $a_return  = array('code' => 0, 'data' => $a_student_gpa_result);
        }else{
            $a_return  = array('code' => 1, 'message' => 'No Student data found');
        }

        print json_encode($a_return);
        // print('<pre>');
        // var_dump($a_student_gpa_result);
    }

    function save_account_settings() {
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->input->post('personal_data_id');
			$s_student_id = $this->input->post('student_id');
            $s_student_email = $this->input->post('student_email');
            $s_student_block = (is_null($this->input->post('student_block'))) ? 'FALSE' : 'TRUE';
            $s_student_transcript = (is_null($this->input->post('student_send_transcript'))) ? 'TRUE' : 'FALSE';
            $s_student_mailenabled = (is_null($this->input->post('student_email_enable'))) ? 'TRUE' : 'FALSE';
            $s_student_internetenabled = (is_null($this->input->post('student_inet_enable'))) ? 'TRUE' : 'FALSE';

            $s_blocked_message = NULL;
            if ($s_student_block == 'TRUE') {
                $s_blocked_message = $this->input->post('blocked_message');
                $s_blocked_message = ($s_blocked_message == '') ? NULL : $s_blocked_message;
            }
            
            $a_student_data = array(
                'student_portal_blocked' => $s_student_block,
                'student_portal_blocked_message' => $s_blocked_message,
                'student_send_transcript' => $s_student_transcript,
            );

            $this->load->library('IULI_Ldap');
            $mba_ldap_result = $this->iuli_ldap->access_account($s_student_email, [
                'mailenabled' => $s_student_mailenabled,
                'internetenabled' => $s_student_internetenabled,
            ]);
            $this->Sm->update_student_data($a_student_data, $s_student_id);
            $a_return = array('code' => 0, 'message' => 'Success');
            // print('<pre>');var_dump($this->input->post());exit;
            print json_encode($a_return);exit;
        }
    }
    
    public function save_settings()
	{
		if ($this->input->is_ajax_request()) {
			$s_personal_data_id = $this->input->post('personal_data_id');
			$s_student_id = $this->input->post('student_id');
			// $s_student_block = (is_null($this->input->post('student_block'))) ? 'FALSE' : 'TRUE';
			$s_student_status = $this->input->post('student_status');
			// $s_student_transcript = (is_null($this->input->post('student_send_transcript'))) ? 'FALSE' : 'TRUE';
			$s_student_program_id = $this->input->post('program_id');
            $s_student_study_program_id = $this->input->post('study_program_id');
            $s_student_study_program_majoring_id = $this->input->post('study_program_majoring_id');
            $s_student_study_program_majoring_id = ($s_student_study_program_majoring_id == '') ? NULL : $s_student_study_program_majoring_id;
            $s_student_date_resign = ($this->input->post('student_status') == 'resign') ? $this->input->post('date_resign') : NULL;
            $s_student_resign_note = ($this->input->post('resign_note') != '') ? $this->input->post('resign_note') : NULL;
            $s_student_thesis_title = ($this->input->post('thesis_title') != '') ? $this->input->post('thesis_title') : NULL;
            $s_student_date_graduated = (empty($this->input->post('date_graduated'))) ? NULL : $this->input->post('date_graduated');
            $s_graduated_year_id = (empty($s_student_date_graduated)) ? NULL : date('Y', strtotime($s_student_date_graduated));

            // if ($this->input->post('student_status') == 'graduated') {
            //     $s_student_date_graduated = $this->input->post('date_graduated');
            //     $s_graduated_year_id = ;
            //     $s_graduated_year_id = ($s_student_date_graduated == '') ? NULL : $s_graduated_year_id;
            // }
            
            $s_academic_year_id_leave = '';
            $s_semester_type_id_leave = '';
            $b_extend_semester = false;

            if ($this->input->post('student_status') == 'onleave') {
                // $s_academic_year_id_leave = $this->input->post('academic_year_id');
                // $s_semester_type_id_leave = $this->input->post('semester_type_id');
                // $b_extend_semester = ($this->input->post('extend_semester') !== null) ? true : false;

                // if (($s_academic_year_id_leave == '') OR ($s_semester_type_id_leave == '')) {
                //     $a_return = array('code' => 1, 'message' => 'Please select Academic Semester to generate invoice!');
                //     print json_encode($a_return);exit;
                // }
            }

            // $s_blocked_message = NULL;
            // if ($s_student_block == 'TRUE') {
            //     $s_blocked_message = $this->input->post('blocked_message');
            //     $s_blocked_message = ($s_blocked_message == '') ? NULL : $s_blocked_message;
            // }
            // $s_student_date_graduated = ($this->input->post('student_status') == 'graduated') ? $this->input->post('date_graduated') : NULL;

			$mbo_student_data = $this->Sm->get_student_by_id($s_student_id);

			if ($this->input->post('student_number') == 0) {
				if ($s_student_program_id == '') {
					$a_return = array('code' => 1, 'message' => 'Please select program!');
					print json_encode($a_return);exit;
				}

				if ($s_student_study_program_id == '') {
					$a_return = array('code' => 1, 'message' => 'Please select study program!');
					print json_encode($a_return);exit;
				}
			}

			if ($mbo_student_data) {
                $mba_semester_active = $this->Smm->get_semester_setting(array('semester_status' => 'active'))[0];
				$a_student_data = array(
					'student_status' => $s_student_status,
					// 'student_portal_blocked' => $s_student_block,
					// 'student_portal_blocked_message' => $s_blocked_message,
                    // 'student_send_transcript' => $s_student_transcript,
                    'student_date_resign' => $s_student_date_resign,
                    'student_resign_note' => $s_student_resign_note,
                    'student_date_graduated' => $s_student_date_graduated,
                    'study_program_majoring_id' => $s_student_study_program_majoring_id,
                    'graduated_year_id' => $s_graduated_year_id,
                    'student_thesis_title' => $s_student_thesis_title,
                    'student_pin_number' => ($this->input->post('ijazah_pin') !== null) ? $this->input->post('ijazah_pin') : NULL
                );
                
                $a_student_semester_update = array(
                    'student_semester_status' => $s_student_status
                );

                $a_student_semester_conditional = array(
                    'student_id' => $s_student_id,
                    'academic_year_id' => $mba_semester_active->academic_year_id,
                    'semester_type_id' => $mba_semester_active->semester_type_id
                );

                if ($s_student_status == 'graduated') {
                    $a_student_semester_conditional = array(
                        'student_id' => $s_student_id,
                        'academic_year_id' => date('Y', strtotime($s_student_date_graduated)),
                        'semester_type_id' => 1
                    );
                }

				if ($this->input->post('student_number') == 0) {
                    // $a_student_data['program_id'] = $s_student_program_id;
                    $a_student_data['program_id'] = '1';
					$a_student_data['study_program_id'] = $s_student_study_program_id;
				}

                $this->Smm->save_student_semester($a_student_semester_update, $a_student_semester_conditional);
                $this->Sm->update_student_data($a_student_data, $s_student_id);

                if ($this->input->post('student_status') == 'onleave') {
                    // $mba_semester_academic_list = $this->Smm->get_semester_setting();
                    // $key_index = false;
                    // foreach ($mba_semester_academic_list as $key => $o_semester_academic) {
                    //     if (($o_semester_academic->academic_year_id == $s_academic_year_id_leave) AND ($o_semester_academic->semester_type_id == $s_semester_type_id_leave)) {
                    //         $key_index = $key;
                    //         break;
                    //     }
                    // }

                    // // create invoice semester leave
                    // if ($b_extend_semester) {
                    //     if ($key_index) {
                    //         // 
                    //     }
                    // }
                }

                $a_return = array('code' => 0, 'message' => 'Success');

                $s_body = $this->session->userdata('name').' has update student settings data '.$mbo_student_data->personal_data_name.' :<br>'.json_encode($a_student_data);
                if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                    modules::run('messaging/send_email', 'employee@company.ac.id', '[IULI Notification] Update Settings Student', $s_body, 'employee@company.ac.id');
                }
			}else{
				$a_return = array('code' => 1, 'message' => 'Student not found');
			}

			print json_encode($a_return);
		}
    }

    function save_change_prodi() {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');

            $this->form_validation->set_rules('study_program_id_target', 'Destination Study Program ', 'required');
            $this->form_validation->set_rules('academic_year_id_target', 'Destination Batch ', 'required');
            $this->form_validation->set_rules('academic_year_semester', 'Start Academic Year ', 'required');
            $this->form_validation->set_rules('semester_type_semester', 'Start Academic Year ', 'required');
            $this->form_validation->set_rules('semester_id_accepted', 'Semester Number ', 'required');

            if ($this->form_validation->run()) {
                $mba_student_data = $this->General->get_where('dt_student', ['student_id' => $s_student_id]);
                if ($mba_student_data) {
                    $o_student = $mba_student_data[0];
                    $s_new_student_id = $this->uuid->v4();
                    $a_new_student_data = [
                        'student_id' => $s_new_student_id,
                        'personal_data_id' => $o_student->personal_data_id,
                        'program_id' => $o_student->program_id,
                        'study_program_id' => set_value('study_program_id_target'),
                        'academic_year_id' => set_value('academic_year_id_target'),
                        'finance_year_id' => set_value('academic_year_id_target'),
                        'student_registration_scholarship_id' => $o_student->student_registration_scholarship_id,
                        'student_number' => NULL,
                        'student_nisn' => $o_student->student_nisn,
                        'student_date_enrollment' => $o_student->student_date_enrollment,
                        'student_type' => 'transfer',
                        'student_class_type' => $o_student->student_class_type,
                        'student_email' => $o_student->student_email,
                        'student_date_active' => $o_student->student_date_active,
                        'student_status' => 'active',
                        'student_has_siblings' => $o_student->student_has_siblings,
                        'has_to_pay_enrollment_fee' => $o_student->has_to_pay_enrollment_fee,
                        'student_portal_blocked' => $o_student->student_portal_blocked,
                        'student_portal_blocked_message' => $o_student->student_portal_blocked_message,
                        'student_send_transcript' => $o_student->student_send_transcript,
                        'date_added' => date('Y-m-d H:i:s')
                    ];
                    $a_old_student_data = [
                        'student_email' => NULL,
                        'student_status' => 'resign',
                        'student_date_resign' => date('Y-m-d H:i:s'),
                        'student_resign_note' => 'Transfer Prodi'
                    ];
                    $this->Sm->create_new_student($a_new_student_data);
                    $mba_new_student_data = $this->General->get_where('dt_student', ['student_id' => $s_new_student_id]);
                    if ($mba_new_student_data) {
                        $o_new_student_data = $mba_new_student_data[0];
                        $this->Sm->update_student_data($a_old_student_data, $s_student_id);
                        modules::run('student/create_student_number', $o_new_student_data);

                        // set student_semester
                        $a_new_student_semester = [
                            'student_id' => $s_new_student_id,
                            'academic_year_id' => set_value('academic_year_semester'),
                            'semester_type_id' => set_value('semester_type_semester'),
                            'semester_id' => set_value('semester_id_accepted'),
                            'date_added' => date('Y-m-d H:i:s'),
                        ];
                        $a_old_student_semester = [
                            'student_semester_status' => 'resign'
                        ];

                        $this->Sm->save_student_semester($a_new_student_semester);
                        $this->Sm->save_student_semester($a_old_student_semester, [
                            'student_id' => $s_student_id,
                            'academic_year_id' => set_value('academic_year_semester'),
                            'academic_year_id' => set_value('semester_type_semester'),
                        ]);
                        $a_return = ['code' => 0, 'message' => 'Success!'];
                    }
                    else {
                        $a_return = ['code' => 1, 'message' => 'Failed submit new student data!'];
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Student not found in our database!'];
                }
            }
            else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);exit;
        }
    }

    public function get_data_student_filter($a_filter_data)
    {
        foreach($a_filter_data as $key => $value){
            if($a_filter_data[$key] == 'all'){
                unset($a_filter_data[$key]);
            }
        }
        $mba_filtered_data = $this->Sm->get_student_filtered($a_filter_data);
        return $mba_filtered_data;
    }

    public function get_student_krs()
    {
        if ($this->input->is_ajax_request()) {
            $a_term = $this->input->post('term');
            $s_student_id = $a_term['student_id'];
            
            $mba_student_krs = $this->Scm->get_score_student($s_student_id, array(
                'stu.program_id' => $a_term['program_id'],
                'stu.study_program_id' => $a_term['study_program_id'],
                'sc.academic_year_id' => $a_term['academic_year_id'],
                'sc.semester_type_id' => $a_term['semester_type_id']
            ));

            if ($mba_student_krs) {
                $this->load->model('academic/Offered_subject_model', 'Osm');
                $this->load->model('personal_data/Personal_data_model', 'Pdm');
                foreach ($mba_student_krs as $krs) {
                    $mba_offered_subject_data = $this->Osm->get_offered_subject_filtered(array(
                        'curriculum_subject_id' => $krs->curriculum_subject_id,
                        'academic_year_id' => $krs->academic_year_id,
                        'semester_type_id' => $krs->semester_type_id
                    ));
                    
                    $mbo_lect_subject = $this->Osm->get_offer_subject_lecturer($mba_offered_subject_data[0]->offered_subject_id);
                    $a_lect_data = array();
                    $a_lect_subject = array();
                    $i_sks_count = 0;
                    if ($mbo_lect_subject) {
                        foreach ($mbo_lect_subject as $lect_subject) {
                            $s_lecturer_name = $this->Pdm->retrieve_title($lect_subject->personal_data_id);
                            $s_lect_class = $s_lecturer_name.' ('.$lect_subject->credit_allocation.')';
                            $i_sks_count += $lect_subject->credit_allocation;
                            array_push($a_lect_subject, $s_lect_class);
                            array_push($a_lect_data, $s_lecturer_name);
                        }
                    }
                    $s_lect_data = (count($a_lect_subject) > 0) ? implode(' | ', $a_lect_subject) : '';
                    $krs->sks_count_total = $i_sks_count;
                    $krs->lecturer_subject = $s_lect_data;
                    $krs->lecturer_data = $a_lect_data;
                }
            }
            
            // var_dump($this->db->last_query());exit;
            $a_rtn = array('code' => 0, 'data' => $mba_student_krs);
            print json_encode($a_rtn);
            exit;
        }
    }

    public function download_student_filtered()
    {
        if ($this->input->is_ajax_request()) {
            $mba_semester_active = $this->Smm->get_semester_setting(array('semester_status' => 'active'))[0];
            $s_semester_active = $mba_semester_active->academic_year_id.''.$mba_semester_active->semester_type_id;

            $a_filter_data = array(
                'ds.academic_year_id' => $this->input->post('academic_year_id'),
                'ds.study_program_id' => $this->input->post('study_program_id'),
                'ds.student_status' => $this->input->post('student_status')
            );

            $mba_student_filter_data = $this->get_data_student_filter($a_filter_data);
            if ($mba_student_filter_data) {
                $s_file = 'student_data.csv';
                $s_path = APPPATH.'uploads/academic/'.$s_semester_active.'/';
                if(!file_exists($s_path)){
                    mkdir($s_path, 0755);
                }
                $s_file_path = $s_path.$s_file;
                $fp = fopen($s_file_path, 'w+');

                fputcsv($fp, array(
                    'Student ID',
                    'Student Name',
                    'Study Program',
                    'Batch',
                    'Student Email',
                    'Date of Birth',
                    'Graduation Year',
                    'Graduation Date',
                    'Student IPK',
                    'Student Address',
                    'Parent Name',
                    'Parent Email',
                    'Parent Cellular',
                    'Student Status'
                ), ';');

                foreach ($mba_student_filter_data as $student) {
                    $s_parent_name = '';
                    $s_parent_email = '';
                    $s_parent_cellular = '';

                    if (!is_null($student->family_id)) {
                        $mba_student_family = $this->Fmm->get_family_members($student->family_id, array('family_member_status != ' => 'child'))[0];
                        if ($mba_student_family) {
                            $s_parent_name = $mba_student_family->personal_data_name;
                            $s_parent_email = $mba_student_family->personal_data_email;
                            $s_parent_cellular = $mba_student_family->personal_data_cellular;
                        }
                    }

                    fputcsv($fp, array(
                        $student->student_number,
                        $student->personal_data_name,
                        $student->study_program_name,
                        $student->academic_year_id,
                        $student->student_email,
                        $student->personal_data_date_of_birth,
                        $student->graduated_year_id,
                        $student->student_date_graduated,
                        $student->student_ipk,
                        $student->address_street,
                        $s_parent_name,
                        $s_parent_email,
                        '="'.$s_parent_cellular.'"',
                        $student->student_status
                    ), ';');
                }

                $a_rtn = array('code' => 0, 'data' => array('file' => $s_file, 'semester_active' => $s_semester_active));
            }else{
                $a_rtn = array('code' => 1, 'message' => 'Student not found');
            }

            print json_encode($a_rtn);
        }
    }
}
