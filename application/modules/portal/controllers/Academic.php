<?php
class Academic_wrong extends Api_core
{
    public $a_prodi_inactive = array(6,13,17,18,19,20);
    public function __construct()
    {
        parent::__construct();
        $s_environment = 'production';
		if($this->session->userdata('auth')){
			$s_environment = $this->session->userdata('environment');
        }
		$this->db = $this->load->database($s_environment, true);

		// switch ($s_environment) {
        //     case 'production':
		// 		$this->load->model('Admission_model', 'Sm');
		// 		break;

        //     case 'sanbox':
        //         $this->load->model('Staging_model', 'Sm');
		// 		break;
			
		// 	default:
		// 		$this->load->model('Admission_model', 'Sm');
		// 		break;
        // }
        $this->load->model('Staging_model', 'Sm');
        
		$this->load->model('Portal_model', 'Pm');

        $this->load->model('academic/Class_group_model', 'Cgm');
        $this->load->model('academic/Subject_model', 'Sbm');
        $this->load->model('academic/Offered_subject_model', 'Osm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('academic/Curriculum_model', 'Crm');
        $this->load->model('student/Student_model', 'Stm');

        $this->session->set_userdata('bypass_everything', true);
    }

    public function check_env()
    {
        print('<pre>');
        var_dump($this->session->userdata('environment'));
    }

    public function class_report($s_class_id)
    {
        $this->generate_student_absence($s_class_id);
        $this->generate_lecturer_absence($s_class_id);
        $this->generate_student_score($s_class_id);
    }

    public function generate_student_score($s_class_id)
    {
        $mbo_class_master_data = $this->Pm->retrieve_data('class_group', ['id' => $s_class_id])[0];
        if ($mbo_class_master_data) {
            // $mbo_class_member_subject = $this->Pm->retrieve_data('class_group_member', ['class_group_id' => $s_class_id])[0];
            // $mbo_class_implemented = $this->Pm->retrieve_data('implemented_subject', ['id' => $mbo_class_member_subject->implemented_subject_id])[0];
            // $mbo_class_subject = $this->Pm->retrieve_data('subject', ['id' => $mbo_class_implemented->subject_id])[0];
            // $mbo_class_subject_name = $this->Pm->retrieve_data('subject_name', ['id' => $mbo_class_subject->subject_name_id])[0];

            $academic_year_data = $this->Pm->retrieve_data('academic_year', ['id' => $mbo_class_master_data->academic_year_id])[0];
            $mbo_class_subject_name = $this->Pm->retrieve_data('class_group_name', ['id' => $mbo_class_master_data->class_group_name_id])[0];

            $semester_class = $academic_year_data->year_name.$mbo_class_master_data->semester_type;

            $s_file_name = str_replace(' ', '-', strtolower($mbo_class_subject_name->name));
            $s_folder_master = $s_file_name.'_'.$semester_class.'_'.$s_class_id;

            $path_master = APPPATH."uploads/academic/pak_tjandra/$s_folder_master/";

            if(!file_exists($path_master)){
                mkdir($path_master, 0777, TRUE);
            }

            $s_file = 'Student_score_'.$s_file_name.'_'.$semester_class.'_'.$s_class_id;

            $s_file_path = $path_master.$s_file.".csv";
            $fp = fopen($s_file_path, 'w+');

            fputcsv($fp, [
                'Student Number', 'Student Name', 'Study Program', 'Subject', 'Absence', 'Score  Quiz', 'Final Exam', 'Repetition Exam', 'Final Score', 'Grade'
            ]);
            
            $mba_score_list = [];
            $mba_class_member_list = $this->Pm->retrieve_data('class_group_member', ['class_group_id' => $s_class_id]);

            if ($mba_class_member_list) {
                foreach ($mba_class_member_list as $o_class_member) {
                    $mba_score_student_list = $this->Pm->retrieve_data('score_absence', ['class_group_member_id' => $o_class_member->id, 'approval' => 'APPROVED']);
                    if ($mba_score_student_list) {
                        foreach ($mba_score_student_list as $o_score) {
                            array_push($mba_score_list, $o_score);
                        }
                    }
                }
                array_values($mba_score_list);
            }

            if ($mba_score_list) {
                foreach ($mba_score_list as $o_score) {
                    $o_student_data = $this->Pm->retrieve_data('student', ['id' => $o_score->student_id])[0];
                    $o_personal_data = $this->Pm->retrieve_data('personal_data', ['id' => $o_student_data->personal_data_id])[0];
                    $o_study_program = $this->Pm->retrieve_data('study_program', ['id' => $o_student_data->study_program_id])[0];

                    // print('<pre>');
                    // var_dump($o_personal_data);
                    
                    fputcsv($fp, [
                        $o_student_data->id_number,
                        $o_personal_data->fullname,
                        $o_study_program->name,
                        str_replace("&amp;", " and ", $mbo_class_subject_name->name),
                        '="'.$o_score->absence.'"',
                        $o_score->quiz,
                        $o_score->final_exam,
                        $o_score->repetition_exam,
                        $o_score->score_sum,
                        $o_score->grade_point
                    ]);
                }
            }
            // print('<pre>');
            // var_dump($mba_score_list);

            fclose($fp);
            
            print('<pre>');
            var_dump($mba_score_list);exit;
        }
    }

    public function generate_lecturer_absence($s_class_id)
    {
        $mbo_class_master_data = $this->Pm->retrieve_data('class_group', ['id' => $s_class_id])[0];
        if ($mbo_class_master_data) {
            // $mbo_class_member_subject = $this->Pm->retrieve_data('class_group_member', ['class_group_id' => $s_class_id])[0];
            // $mbo_class_implemented = $this->Pm->retrieve_data('implemented_subject', ['id' => $mbo_class_member_subject->implemented_subject_id])[0];
            // $mbo_class_subject = $this->Pm->retrieve_data('subject', ['id' => $mbo_class_implemented->subject_id])[0];
            // $mbo_class_subject_name = $this->Pm->retrieve_data('subject_name', ['id' => $mbo_class_subject->subject_name_id])[0];

            $mba_uosd_list = $this->Pm->retrieve_data('unit_of_subject_delivered', ['class_group_id' => $s_class_id]);
            $academic_year_data = $this->Pm->retrieve_data('academic_year', ['id' => $mbo_class_master_data->academic_year_id])[0];
            $mbo_class_subject_name = $this->Pm->retrieve_data('class_group_name', ['id' => $mbo_class_master_data->class_group_name_id])[0];

            $semester_class = $academic_year_data->year_name.$mbo_class_master_data->semester_type;

            $s_file_name = str_replace(' ', '-', strtolower($mbo_class_subject_name->name));
            $s_folder_master = $s_file_name.'_'.$semester_class.'_'.$s_class_id;

            $path_master = APPPATH."uploads/academic/pak_tjandra/$s_folder_master/";

            if(!file_exists($path_master)){
                mkdir($path_master, 0777, TRUE);
            }

            $s_file = 'Lecturer_absence_'.$s_file_name.'_'.$semester_class.'_'.$s_class_id;

            $s_file_path = $path_master.$s_file.".csv";
            $fp = fopen($s_file_path, 'w+');

            fputcsv($fp, [
                'Lecturer', 'Subject', 'Semester', 'Start Time', 'End Time', 'Topics Covered'
            ]);

            if ($mba_uosd_list) {
                foreach ($mba_uosd_list as $o_uosd) {
                    $o_lectuer_data = $this->Pm->retrieve_data('lecturer', ['id' => $o_uosd->lecturer_id])[0];
                    $o_personal_data = $this->Pm->retrieve_data('personal_data', ['id' => $o_lectuer_data->personal_data_id])[0];

                    fputcsv($fp, [
                        $o_personal_data->fullname,
                        str_replace("&", " and ", $mbo_class_subject_name->name),
                        $semester_class,
                        $o_uosd->start_time,
                        $o_uosd->end_time,
                        str_replace("&amp;", " and ", $o_uosd->description)
                    ]);
                }
            }

            fclose($fp);
            // print('<pre>');
            // var_dump($mba_uosd_list);exit;
        }
    }

    public function generate_student_absence($s_class_id)
    {
        $mbo_class_master_data = $this->Pm->retrieve_data('class_group', ['id' => $s_class_id])[0];
        
        if ($mbo_class_master_data) {
            // $mbo_class_member_subject = $this->Pm->retrieve_data('class_group_member', ['class_group_id' => $s_class_id])[0];
            // $mbo_class_implemented = $this->Pm->retrieve_data('implemented_subject', ['id' => $mbo_class_member_subject->implemented_subject_id])[0];
            // $mbo_class_subject = $this->Pm->retrieve_data('subject', ['id' => $mbo_class_implemented->subject_id])[0];
            // $mbo_class_subject_name = $this->Pm->retrieve_data('subject_name', ['id' => $mbo_class_subject->subject_name_id])[0];

            // $mba_uosd_list = $this->Cgm->get_unit_subject_delivered($s_class_master_id, false, 'ASC');
            
            $academic_year_data = $this->Pm->retrieve_data('academic_year', ['id' => $mbo_class_master_data->academic_year_id])[0];
            $mbo_class_subject_name = $this->Pm->retrieve_data('class_group_name', ['id' => $mbo_class_master_data->class_group_name_id])[0];
            $mba_uosd_list = $this->Pm->retrieve_data('unit_of_subject_delivered', ['class_group_id' => $s_class_id]);
            $mba_student_list = [];
            $mba_class_member_list = $this->Pm->retrieve_data('class_group_member', ['class_group_id' => $s_class_id]);

            if ($mba_class_member_list) {
                foreach ($mba_class_member_list as $o_class_member) {
                    $mba_score_list = $this->Pm->retrieve_data('score_absence', ['class_group_member_id' => $o_class_member->id, 'approval' => 'APPROVED']);
                    if ($mba_score_list) {
                        foreach ($mba_score_list as $o_score) {
                            array_push($mba_student_list, $o_score);
                        }
                    }
                }
                array_values($mba_student_list);
            }

            // print('<pre>');
            // var_dump($mba_uosd_list);exit;

            $semester_class = $academic_year_data->year_name.$mbo_class_master_data->semester_type;

            $s_file_name = str_replace(' ', '-', strtolower($mbo_class_subject_name->name));
            $s_folder_master = $s_file_name.'_'.$semester_class.'_'.$s_class_id;

            $path_master = APPPATH."uploads/academic/pak_tjandra/$s_folder_master/";

            if(!file_exists($path_master)){
                mkdir($path_master, 0777, TRUE);
            }

            $s_file = 'Student_absence_'.$s_file_name.'_'.$semester_class.'_'.$s_class_id;

            $s_file_path = $path_master.$s_file.".csv";
            $fp = fopen($s_file_path, 'w+');
            
            $a_header = [" "];
            if ($mba_uosd_list) {
                foreach ($mba_uosd_list as $o_uosd) {
                    array_push($a_header, $o_uosd->start_time);
                }
            }

            fputcsv($fp, $a_header);

            $a_data = [];
            if ($mba_student_list) {
                foreach ($mba_student_list as $o_student) {
                    $o_student_data = $this->Pm->retrieve_data('student', ['id' => $o_student->student_id])[0];
                    $o_personal_data = $this->Pm->retrieve_data('personal_data', ['id' => $o_student_data->personal_data_id])[0];
                    $a_student_absence = [];
                    array_push($a_student_absence, $o_personal_data->fullname);

                    if ($mba_uosd_list) {
                        foreach ($mba_uosd_list as $o_uosd) {
                            // student_absence
                            $o_student_absence = $this->Pm->retrieve_data('student_absence', ['score_absence_id' => $o_student->id, 'unit_of_subject_delivered_id' => $o_uosd->id])[0];
                            // $mba_student_absence = $this->Scm->get_student_absence(['sc.score_id' => $o_student->score_id, 'as.subject_delivered_id' => $o_uosd->subject_delivered_id])[0];
                            array_push($a_student_absence, (($o_student_absence) ? $o_student_absence->absence : ''));
                        }
                    }

                    array_push($a_data, $a_student_absence);
                }
            }

            if (count($a_data) > 0) {
                foreach ($a_data as $o_data) {
                    fputcsv($fp, $o_data);
                }
            }

            fclose($fp);
        }
    }

    public function sync_academic_cst()
    {
        $start = strtotime("now");
        $this->sync_offer_subject(); // 1
        // $this->sync_class_group(); // 2
        // $this->clear_offer_subject_lecturer(); //3
        $this->score_data_sync(); // 4
        $this->sync_ofse_score(); // 5
        $this->mastering_after_sync(); // 6
        // $this->sync_class_subject_delivered(); // 7
        // $this->sync_supplement(); // 8

        $end = strtotime("now");
        $interval = $end - $start;
        $seconds = $interval % 60;
        $minutes = floor(($interval % 3600) / 60);
        $hours = floor($interval / 3600);
        echo "total time: ".$hours.":".$minutes.":".$seconds;
        $this->session->unset_userdata('bypass_everything');
    }

    public function sync_academic()
    {
        $start = strtotime("now");
        $this->sync_subject(); // 1
        $this->sync_curriculum(); // 2
        $this->sync_offer_subject(); // 3
        $this->score_data_sync(); // 4
        $this->sync_ofse_score(); // 5
        $this->mastering_after_sync(); // 6
        $this->sync_class_subject_delivered(); //7
        $this->sync_supplement(); // 8

        $end = strtotime("now");
        $interval = $end - $start;
        $seconds = $interval % 60;
        $minutes = floor(($interval % 3600) / 60);
        $hours = floor($interval / 3600);
        echo "total time: ".$hours.":".$minutes.":".$seconds;
        $this->session->unset_userdata('bypass_everything');
    }

    public function sync_old_subject_delivered($s_academic_year_id, $s_semester_type_id)
    {
        $mba_class_master_data = $this->Sm->retrieve_data('dt_class_master', ['academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id]);
        if ($mba_class_master_data) {
            foreach ($mba_class_master_data as $o_class_master) {
                $this->test_sync_subject_delivered($o_class_master->class_master_id);
            }
            // print('<pre>');
            // var_dump($mba_class_master_data);
        }
    }

    public function test_sync_subject_delivered($s_class_master_id) {
        $this->load->model('employee/Employee_model', 'Emm');
        $mba_class_group_list_data = $this->Cgm->get_class_master_group(['class_master_id' => $s_class_master_id]);
        if ($mba_class_group_list_data) {
            foreach ($mba_class_group_list_data as $o_class_group_id) {
                // $mbo_class_group_data = $this->Cgm->get_class_group_lists(['class_group_id' => $o_class_group_id->class_group_id])[0];
                $mbo_class_group_data = $this->Sm->retrieve_data('dt_class_group', ['class_group_id' => $o_class_group_id->class_group_id])[0];

                if ($mbo_class_group_data) {
                    $mbo_portal_class_data = $this->Pm->retrieve_data('class_group', ['id' => $mbo_class_group_data->portal_id])[0];

                    if ($mbo_portal_class_data) {
                        $mba_uosd_list = $this->Pm->retrieve_data('unit_of_subject_delivered', ['class_group_id' => $mbo_portal_class_data->id]);

                        if ($mba_uosd_list) {

                            foreach ($mba_uosd_list as $o_uosd) {
                                $mba_class_subject_delivered_data = $this->Sm->retrieve_data('dt_class_subject_delivered', ['portal_id' => $o_uosd->id]);

                                if (!is_null($o_uosd->lecturer_id)) {
                                    $mba_lecturer_data = $this->Pm->retrieve_data('lecturer', ['id' => $o_uosd->lecturer_id])[0];
                                    if ($mba_lecturer_data) {
                                        $o_staging_employee_data = $this->Emm->get_employee_data(['pd.portal_id' => $mba_lecturer_data->personal_data_id])[0];
                                        if ($o_staging_employee_data) {
                                            if (!$mba_class_subject_delivered_data) {
                                                $s_class_subject_delivered_id = $this->uuid->v4();
                                                $a_class_subject_delivered_data = [
                                                    'subject_delivered_id' => $s_class_subject_delivered_id,
                                                    'class_master_id' => $s_class_master_id,
                                                    'employee_id' => $o_staging_employee_data->employee_id,
                                                    'subject_delivered_time_start' => $o_uosd->start_time,
                                                    'subject_delivered_time_start' => $o_uosd->end_time,
                                                    'subject_delivered_description' => $o_uosd->description,
                                                    'portal_id' => $o_uosd->id
                                                ];
            
                                                $b_uosd_ready = false;
            
                                                $this->Cgm->save_subject_delivered($a_class_subject_delivered_data);
                                            }else{
                                                $b_uosd_ready = true;
                                                $s_class_subject_delivered_id = $mba_class_subject_delivered_data[0]->subject_delivered_id;
                                            }
            
                                            $mba_staging_student_absence = $this->Sm->retrieve_data('dt_absence_student', ['subject_delivered_id' => $s_class_subject_delivered_id]);
            
                                            if (!$mba_staging_student_absence) {
                                                $mba_portal_student_absence = $this->Pm->retrieve_data('student_absence', ['unit_of_subject_delivered_id' => $o_uosd->id]);
            
                                                if ($mba_portal_student_absence) {
                                                    foreach ($mba_portal_student_absence as $o_portal_student_absence) {
                                                        $mba_staging_score_data = $this->Sm->retrieve_data('dt_score', ['portal_id' => $o_portal_student_absence->score_absence_id]);
            
                                                        if ($mba_staging_score_data) {
                                                            $a_student_absence_data = [
                                                                'absence_student_id' => $this->uuid->v4(),
                                                                'score_id' => $mba_staging_score_data[0]->score_id,
                                                                'subject_delivered_id' => $s_class_subject_delivered_id,
                                                                'absence_status' => ($o_portal_student_absence->absence == 'ALPHA') ? 'ABSENT' : $o_portal_student_absence->absence,
                                                                'absence_note' => $o_portal_student_absence->note
                                                            ];
                
                                                            $this->Cgm->save_student_absence($a_student_absence_data);
                                                        }
                                                    }
                                                }
                                            }
                                        }else{
                                            print('Employee ngga ada!<br>');
                                            $a_return = ['code' => 1, 'message' => 'Employee not found in staging portal!', 'data' => $mba_lecturer_data];
                                            if ($this->input->is_ajax_request()) {
                                                print json_encode($a_return);
                                            }else{
                                                print('<pre>');
                                                var_dump($a_return);
                                            }
                                            exit;
                                        }
                                    }else{
                                        $a_return = ['code' => 1, 'message' => 'Lecturer id not found in old database!', 'data' => $mbo_portal_class_data];
                                        if ($this->input->is_ajax_request()) {
                                            print json_encode($a_return);
                                        }else{
                                            print('<pre>');
                                            var_dump($a_return);
                                        }
                                        exit;
                                    }
                                }else{
                                    $a_return = ['code' => 1, 'message' => 'Lecturer is null in old database!', 'data' => $mbo_portal_class_data];
                                    if ($this->input->is_ajax_request()) {
                                        print json_encode($a_return);
                                    }else{
                                        print('<pre>');
                                        var_dump($a_return);
                                    }
                                    exit;
                                }
                                
                            }

                        }else{
                            $a_return = ['code' => 1, 'message' => 'Absence not found in old database!', 'data' => $mbo_portal_class_data];
                            break;
                            // print('<pre>');
                            // var_dump($mbo_portal_class_data);
                            // print('kosong uosd');
                        }
                    }else{
                        $a_return = ['code' => 1, 'message' => 'Class group not found in old database!', 'data' => false];
                        // print('Kosyoong mdb!');exit;
                        break;
                    }
                    
                }else{
                    $a_return = ['code' => 1, 'message' => 'Class group not found in staging portal!', 'data' => false];
                    // print('Kosyoong!');
                    break;
                }
                
            }
        }else{
            $a_return = ['code' => 1, 'message' => 'Class list not found in staging portal!', 'data' => false];
            // print('satu');
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);
        }else{
            print('<pre>');
            var_dump($a_return);exit;
        }

        // print('<pre>');
        // var_dump($mba_class_group_list_data);
    }

    public function sync_supplement($s_portal_supplement_id = false)
    {
        if ($s_portal_supplement_id) {
            $mba_portal_supplement_data = $this->Pm->retrieve_data('student_supplement', array('id' => $s_portal_supplement_id, 'print' => 'TRUE'));
        }else{
            $mba_portal_supplement_data = $this->Pm->retrieve_data('student_supplement', array('print' => 'TRUE'));
        }
        print('proccess supplement');

        $this->db->trans_start();

        if ($mba_portal_supplement_data) {
            foreach ($mba_portal_supplement_data as $supplement) {
                $mbo_staging_student_data = $this->Sm->retrieve_data('dt_student', array('portal_id' => $supplement->student_id))[0];
                if (!$mbo_staging_student_data) {
                    print('Student id '.$supplement->student_id.' not found in staging data!');
                    continue;
                }
                
                $mbo_staging_employee_data = false;
                if (!is_null($supplement->personal_data_id)) {
                    $mbo_staging_personal_data = $this->Sm->retrieve_data('dt_personal_data', array('portal_id' => $supplement->personal_data_id))[0];
                    if ($mbo_staging_personal_data) {
                        $mbo_staging_employee_data = $this->Sm->retrieve_data('dt_employee', array('personal_data_id' => $mbo_staging_personal_data->personal_data_id))[0];
                    }
                }else{
                    $mbo_portal_employee_data = $this->Pm->retrieve_data('employee', array('id' => $supplement->user_access_id))[0];
                    if ($mbo_portal_employee_data) {
                        $mbo_staging_employee_data = $this->Sm->retrieve_data('dt_employee', array('employee_email' => $mbo_portal_employee_data->email))[0];
                    }
                }

                if (!$mbo_staging_employee_data) {
                    print('Employee not found in staging. student_supplement_id: '.$supplement->id);
                }
                $mbo_staging_employee_personal_data = $this->Sm->retrieve_data('dt_personal_data', array('portal_id' => $supplement->personal_data_id));
                $a_semester_dikti = $this->parsing_semester($mbo_staging_student_data->academic_year_id, $supplement->semester_id);
                $a_staging_supplement_data = array(
                    'supplement_comment' => $supplement->value,
                    'supplement_category' => strtolower($supplement->category),
                    'student_id' => $mbo_staging_student_data->student_id,
                    'employee_id' => $mbo_staging_employee_data->employee_id,
                    'academic_year_id' => $a_semester_dikti['academic_year_id'],
                    'semester_type_id' => $a_semester_dikti['semester_type_id'],
                    'portal_id' => $supplement->id
                );

                if ($mba_staging_supplement_data = $this->Sm->retrieve_data('dt_student_supplement', array('portal_id' => $supplement->id))[0]) {
                    $this->Scm->save_student_suppement($a_staging_supplement_data, array('supplement_id' => $mba_staging_supplement_data->supplement_id));
                }else{
                    $a_staging_supplement_data['supplement_id'] = $this->uuid->v4();
                    $a_staging_supplement_data['date_added'] = date('Y-m-d H:i:s');
                    $this->Scm->save_student_suppement($a_staging_supplement_data);
                }
                print(' .');
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            print('rollback');
        }else{
            $this->db->trans_commit();
            print('commit');
        }
    }

    // public function clear_class_group()
    // {
    //     $i = 0;
    //     $mba_class_with_portal = $this->Sm->retrieve_data('dt_class_group', array('portal_id >' => '0'));
    //     if ($mba_class_with_portal) {
    //         foreach ($mba_class_with_portal as $class) {
    //             $mba_class_subject = $this->Sm->retrieve_data('dt_class_group_subject', array('class_group_id' => $class->class_group_id));
    //             if (!$mba_class_subject) {
    //                 $this->Sm->remove_staging_data('dt_class_group', array('class_group_id' => $class->class_group_id));
    //                 $i++;
    //                 print('o ');
    //             }
    //         }
    //     }

    //     print('<h1>'.$i.'</h1>');
    // }

    public function sync_class_group($s_portal_class_id = false)
    {
        print('<b>Syncronize class group data');
        if ($s_portal_class_id) {
            $mbo_portal_class_group_lists = $this->Pm->retrieve_data('class_group', array('team_teaching' => null, 'id' => $s_portal_class_id));
        }else{
            $mbo_portal_class_group_lists = $this->Pm->retrieve_data('class_group', array('team_teaching' => null));
        }

        $this->db->trans_start();
        foreach ($mbo_portal_class_group_lists as $class) {
            $mbo_portal_class_subject = $this->Pm->retrieve_data('class_group_member', array('class_group_id' => $class->id));
            if ($mbo_portal_class_subject) {
                $mbo_portal_implemented_subject_data = $this->Pm->retrieve_data('implemented_subject', array('id' => $mbo_portal_class_subject[0]->implemented_subject_id))[0];
                if ($mbo_portal_implemented_subject_data) {
                    $mbo_portal_academic_year = $this->Pm->retrieve_data('academic_year', array('id' => $class->academic_year_id))[0];
                    $a_semester_dikti = $this->parsing_semester($this->get_staging_academic_year($class->academic_year_id), $mbo_portal_implemented_subject_data->semester_id);
                    $mbo_portal_class_name_data = $this->Pm->retrieve_data('class_group_name', array('id' => $class->class_group_name_id))[0];
                    $a_class_data = array(
                        'academic_year_id' => $this->get_staging_academic_year($class->academic_year_id),
                        'semester_type_id' => $a_semester_dikti['semester_type_id'],
                        'class_group_name' => $mbo_portal_class_name_data->name,
                        'portal_id' => $class->id
                    );
                    // print("<pre>");
                    // var_dump($a_class_data);
                    // exit;

                    $mbo_staging_class_group_data = $this->Sm->retrieve_data('dt_class_group', array('portal_id' => $class->id))[0];
                    if ($mbo_staging_class_group_data) {
                        $save_class = $this->Cgm->save_data($a_class_data, $mbo_staging_class_group_data->class_group_id);
                    }else {
                        $a_class_data['class_group_id'] = $this->uuid->v4();
                        $a_class_data['date_added'] = date('Y-m-d H:i:s');
                        $save_class = $this->Cgm->save_data($a_class_data);
                    }
                    if (!$save_class) {
                        print('pantessan!');
                    }
                    $this->sync_class_group_subject($class->id);
                    $this->sync_class_lecturer($class->id);
                    print('. ');
                }
            }
        }

        if ($this->db->trans_status()===FALSE) {
            $this->db->trans_rollback();
            print('Syncronize failed</b>');
        }else{
            $this->db->trans_commit();
            print('Syncronize Success</b>');
        }
    }

    public function sync_class_group_subject($s_portal_class_id)
    {
        $mbo_portal_class_subject = $this->Pm->retrieve_data('class_group_member', array('class_group_id' => $s_portal_class_id));
        $mbo_staging_class_data = $this->Sm->retrieve_data('dt_class_group', array('portal_id' => $s_portal_class_id))[0];
        
        if ($mbo_portal_class_subject) {
            foreach ($mbo_portal_class_subject as $class_subject) {
                $mbo_staging_implemented_subject_data = $this->Sm->retrieve_data('dt_offered_subject', array('portal_id' => $class_subject->implemented_subject_id))[0];
                if ($mbo_staging_implemented_subject_data) {
                    $a_class_subject_data = array(
                        'class_group_id' => $mbo_staging_class_data->class_group_id,
                        'offered_subject_id' => $mbo_staging_implemented_subject_data->offered_subject_id,
                        'portal_id' => $class_subject->id
                    );

                    $mbo_staging_class_subject_data = $this->Sm->retrieve_data('dt_class_group_subject', array('portal_id' => $class_subject->id))[0];
                    if ($mbo_staging_class_subject_data) {
                        $this->Cgm->save_class_group_subject($a_class_subject_data, $mbo_staging_class_subject_data->class_group_subject_id);
                    }else {
                        $a_class_subject_data['class_group_subject_id'] = $this->uuid->v4();
                        $a_class_subject_data['date_added'] = date('Y-m-d H:i:s');
                        $this->Cgm->save_class_group_subject($a_class_subject_data);
                    }
                }
            }
        }
    }

    public function mastering_class_group($s_staging_class_id = false)
    {
        print('<br><b>Mastering class group<b>');
        if ($s_staging_class_id) {
            $mba_staging_class_data = $this->Sm->retrieve_data('dt_class_group', array('class_group_id' => $s_staging_class_id));
        }else{
            $mba_staging_class_data = $this->Sm->retrieve_data('dt_class_group');
        }
        
        $this->db->trans_start();
        if ($mba_staging_class_data) {
            foreach ($mba_staging_class_data as $class) {
                $mba_staging_class_subject = $this->Cgm->get_class_subject_mastering($class->class_group_id);
                # cek mata kuliah di dt_class_group
                if ($mba_staging_class_subject) {
                    $mba_staging_class_in_master = $this->Sm->retrieve_data('dt_class_master_class', array('class_group_id' => $class->class_group_id));
                    $a_class_master_data = array(
                        'academic_year_id' => $class->academic_year_id,
                        'semester_type_id' => $class->semester_type_id,
                        'class_master_name' => $class->class_group_name,
                        'date_added' => $class->date_added
                    );

                    if ($mba_staging_class_in_master) {
                        # class_master sudah ada di dt_class_master_class
                        $mba_staging_class_master_data = $this->Sm->retrieve_data('dt_class_master', array('class_master_id' => $mba_staging_class_in_master[0]->class_master_id));
                        if ($mba_staging_class_master_data) {
                            # update dt_class_master
                            $s_class_master_id = $mba_staging_class_master_data[0]->class_master_id;
                            $save_class_master = $this->Cgm->save_class_mastering($a_class_master_data, array('class_master_id' => $s_class_master_id));
                        }else{
                            # insert dt_class_master with class_master_id in row dt_class_master_class
                            $s_class_master_id = $mba_staging_class_in_master[0]->class_master_id;
                            $a_class_master_data['class_master_id'] = $s_class_master_id;
                            $save_class_master = $this->Cgm->save_class_mastering($a_class_master_data);
                        }
                    }else{
                        # insert dt_class_master
                        $s_class_master_id = $this->uuid->v4();
                        $a_class_master_data['class_master_id'] = $s_class_master_id;
                        $save_class_master = $this->Cgm->save_class_mastering($a_class_master_data);
                        $a_class_master_class_data = array(
                            'class_master_id' => $s_class_master_id,
                            'class_group_id' => $class->class_group_id,
                            'date_added' => date('Y-m-d H:i:s')
                        );

                        $this->Cgm->save_class_master_class($a_class_master_class_data);
                    }

                    if ($save_class_master) {
                        if ($mba_staging_class_master_lect_data = $this->Sm->retrieve_data('dt_class_master_lecturer')) {
                            $this->Sm->remove_staging_data('dt_class_master_lecturer', array('class_master_id' => $s_class_master_id));
                        }

                        $mba_staging_class_group_lecturer_data = $this->Sm->retrieve_data('dt_class_group_lecturer', array('class_group_id' => $class->class_group_id));
                        if ($mba_staging_class_group_lecturer_data) {
                            foreach ($mba_staging_class_group_lecturer_data as $class_lect) {
                                $a_lecturer_class_master_data = array(
                                    'class_master_lecturer_id' => $this->uuid->v4(),
                                    'class_master_id' => $s_class_master_id,
                                    'employee_id' => $class_lect->employee_id,
                                    'employee_id_reported' => $class_lect->employee_id_reported,
                                    'credit_allocation' => $class_lect->credit_allocation,
                                    'credit_charged ' => $class_lect->credit_charged,
                                    'credit_realization' => $class_lect->credit_realization,
                                    'class_master_lecturer_status' => $class_lect->class_group_lecturer_status,
                                    'class_master_lecturer_preferable_day' => $class_lect->class_group_lecturer_preferable_day,
                                    'class_master_lecturer_preferable_time' => $class_lect->class_group_lecturer_preferable_time,
                                    'class_master_lecturer_priority' => $class_lect->class_group_lecturer_priority,
                                    'is_reported_to_feeder' => $class_lect->is_reported_to_feeder,
                                    'date_added' => $class_lect->date_added
                                );

                                $this->Cgm->save_class_master_lect_data($a_lecturer_class_master_data);
                            }
                        }

                        $this->Scm->save_data(array('class_master_id' => $s_class_master_id), array('class_group_id' => $class->class_group_id));
                        print('. ');
                    }else{
                        print('<br>save class master gagal. class_group_id: '.$class->class_group_id);
                        exit;
                    }
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            print('<p>Mastering gagal! Rollback data</p>');
        }else{
            $this->db->trans_commit();
            print('<p>Mastering sukses! Commit data</p>');
        }
    }

    public function mastering_after_sync($s_staging_class_id = false) // belum support update
    {
        print('<br><b>Mastering class group');

        $this->db->trans_start();

        // if ($s_staging_class_id) {
        //     $mbo_staging_class_group_data = $this->Sm->retrieve_data('dt_class_group', array('class_group_id' => $s_staging_class_id));
        // }else{
        //     $mbo_staging_class_group_data = $this->Sm->retrieve_data('dt_class_group');
        // }

        $mbo_staging_class_group_data = $this->Sm->retrieve_data('dt_class_group', array(
            'academic_year_id' => 2019,
            'semester_type_id' => 7,
            'portal_id !=' => NULL
        ));
        // print('<pre>');var_dump($mbo_staging_class_group_data);exit;
        
        if ($mbo_staging_class_group_data) {
            foreach ($mbo_staging_class_group_data as $class_group) {
                $mbo_class_in_master = $this->Sm->retrieve_data('dt_class_master_class', array('class_group_id' => $class_group->class_group_id));
                $mbo_staging_class_details = $this->Cgm->get_class_subject_mastering($class_group->class_group_id);
                // print('<pre>');var_dump($this->db->database);exit;
                if ($mbo_staging_class_details) {
                    if ($mbo_class_in_master) {
                        // $this->db->update('dt_score', array('class_master_id' => NULL));
                        $this->Sm->remove_staging_data('dt_class_master', array(
                            'class_master_id' => $mbo_class_in_master[0]->class_master_id
                        ));
                    }
                    
                    $s_class_master_id = $this->uuid->v4();
                    $a_class_master_data = array(
                        'class_master_id' => $s_class_master_id,
                        'academic_year_id' => $class_group->academic_year_id,
                        'semester_type_id' => $class_group->semester_type_id,
                        'class_master_name' => $class_group->class_group_name,
                        'date_added' => date('Y-m-d H:i:s')
                    );
                    
                    if ($this->Cgm->save_class_mastering($a_class_master_data)) {
                        foreach ($mbo_staging_class_details as $class_subject){
                            $mbo_class_subject = $this->Cgm->get_class_group_filtered(
                                array(
                                    'subject_name' => $class_subject->subject_name, 
                                    'dcg.academic_year_id' => $class_subject->running_year, 
                                    'dcg.semester_type_id' => $class_subject->class_semester_type_id, 
                                    'curriculum_subject_credit' => $class_subject->curriculum_subject_credit, 
                                    'employee_id' => $class_subject->employee_id, 
                                    'credit_allocation' => $class_subject->credit_allocation
                                ));
                            if ($mbo_class_subject) {
                                $i_limit = 1;
                                $a_class_join = array();
                                foreach ($mbo_class_subject as $classes) {
                                    $mbo_classes_in_master = $this->Sm->retrieve_data('dt_class_master_class', array('class_group_id' => $classes->class_group_id));
                                    if (!$mbo_classes_in_master) {
                                        $mbo_class_lect = $this->Cgm->get_class_lecturer_grouping(array('class_group_id' => $classes->class_group_id));
                                        $mbo_student_class = $this->Cgm->get_class_group_student($classes->class_group_id);
                                        $mbo_master_lect = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $s_class_master_id));
                                    
                                        if ($mbo_class_lect) {
                                            if (!$mbo_master_lect) {
                                                foreach ($mbo_class_lect as $class_lect) {
                                                    $this->save_lecturer_mastering($s_class_master_id, $class_lect);
                                                }
                                            }
                                        }

                                        if (!$mbo_student_class) {
                                            array_push($a_class_join, $classes->class_group_id);
                                        }else if ($i_limit == 1) {
                                            $i_limit--;
                                            array_push($a_class_join, $classes->class_group_id);
                                        }
                                    }
                                }

                                if (count($a_class_join) > 0) {
                                    foreach ($a_class_join as $class_data) {
                                        $a_class_master_class_data = array(
                                            'class_master_id' => $s_class_master_id,
                                            'class_group_id' => $class_data,
                                            'date_added' => date('Y-m-d H:i:s')
                                        );
                                        print(' .');
                                        $this->Cgm->save_class_master_class($a_class_master_class_data);
                                        $this->Scm->save_data(array('class_master_id' => $s_class_master_id), array('class_group_id' => $class_data));
                                    }
                                }
                            }
                        }
                    }else{
                        print('err mastering class #1');exit;
                    }
                }else {
                    // print('<pre>function model "get_class_subject_mastering" result is false->Class group id '.$class_group->class_group_id.'</pre>');
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            print('Error mastering. Query rollback');
        }else{
            $this->db->trans_commit();
            print('Success mastering, Query commit');
        }
    }

    public function save_lecturer_mastering($s_class_master_id, $o_class_group_lect)
    {
        $a_class_master_lect_data = array(
            'class_master_lecturer_id' => $this->uuid->v4(),
            'class_master_id' => $s_class_master_id,
            'employee_id' => $o_class_group_lect->employee_id,
            'employee_id_reported' => $o_class_group_lect->employee_id_reported,
            'credit_allocation' => $o_class_group_lect->credit_allocation,
            'credit_charged ' => $o_class_group_lect->credit_charged,
            'credit_realization' => $o_class_group_lect->credit_realization,
            'class_master_lecturer_status' => $o_class_group_lect->class_group_lecturer_status,
            'class_master_lecturer_preferable_day' => $o_class_group_lect->class_group_lecturer_preferable_day,
            'class_master_lecturer_preferable_time' => $o_class_group_lect->class_group_lecturer_preferable_time,
            'class_master_lecturer_priority' => $o_class_group_lect->class_group_lecturer_priority,
            'is_reported_to_feeder' => $o_class_group_lect->is_reported_to_feeder,
            'date_added' => date('Y-m-d H:i:s')
        );

        $this->Cgm->save_class_master_lect_data($a_class_master_lect_data);
    }

    public function sync_ofse_score()
    {
        $a_student_id = ['30','213','365','512','136','377','18','144','399','445','374','300','241','286','632','15','149','372','406','261','279','453','237','479','631','464','69','473','206','364','647','419','429','371','214','31','409','505','272','230','493','455','459','472','218','326','551','6','243','353','496','430','379','219','630','422','428','426','97','411','469','380','317','29','351','294','629','500','427','510','312','447','522','303','410','146','381','16','164','396'];
        $mba_score_data = $this->Pm->retrieve_score_grouping('student_id', array(
            'semester_id' => 17
        ));

        if ($mba_score_data) {
            $this->db->trans_start();
            foreach ($a_student_id as $s_student_id) {
                $mbo_staging_student_data = $this->Sm->retrieve_data('dt_student', array('portal_id' => $s_student_id))[0];
                $mbo_portal_ofse_score = $this->Pm->retrieve_data('score_absence', ['student_id' => $s_student_id, 'semester_id' => 17])[0];
                if (!$mbo_staging_student_data) {
                    continue;
                }

                if (!$mbo_portal_ofse_score) {
                    print('- ');
                    $mba_score_data = $this->Pm->retrieve_data('ofse_score', array('student_id' => $s_student_id));
                    if ($mba_score_data) {
                        foreach ($mba_score_data as $score_data) {
                            $s_ofse_score = $this->grades->get_ofse_score($score_data->examiner_one, $score_data->examiner_two);
                            $a_score_examiner = array(
                                'score_examiner_1' => $score_data->examiner_one,
                                'score_examiner_2' => $score_data->examiner_two
                            );

                            $mbo_staging_subject_data = $this->Crm->get_curriculum_subject_filtered(array(
                                'subject_name' => $score_data->subject_name,
                                'rc.study_program_id' => $mbo_staging_student_data->study_program_id,
                                'rcs.curriculum_subject_credit' => 0
                            ));

                            if (!$mbo_staging_subject_data) {
                                $mba_curriculum_select = $this->Crm->get_curriculum_filtered(array(
                                    'rc.study_program_id' => $mbo_staging_student_data->study_program_id
                                ), 'valid_academic_year', 'DESC');
                                
                                if (!$mba_curriculum_select) {
                                    print('Curriculum for study program id '.$mbo_staging_student_data->study_program_id.' not found in staging data');
                                    exit;
                                }

                                $mba_curriculum_semester_data = $this->Crm->get_curriculum_semester_filtered(array(
                                    'curriculum_id' => $mba_curriculum_select[0]->curriculum_id,
                                    'semester_id' => 17
                                ));

                                if (!$mba_curriculum_semester_data) {
                                    $a_curriculum_semester_data = array(
                                        'curriculum_id' => $mba_curriculum_select[0]->curriculum_id,
                                        'semester_id' => 17
                                    );
                                    $this->Crm->create_new_curriculum_semester($a_curriculum_semester_data);
                                }

                                $mbo_staging_subject_ = $this->Sbm->get_subject_name_filtered(array(
                                    'subject_name' => $score_data->subject_name
                                ))[0];
                                if (!$mbo_staging_subject_) {
                                    $s_subject_name_id = $this->uuid->v4();
                                    $s_subject_name_code = modules::run('academic/subject/generate_subject_name_code', $score_data->subject_name);
                                    $a_subject_name_data = array(
                                        'subject_name_id' => $s_subject_name_id,
                                        'subject_name' => $score_data->subject_name,
                                        'subject_name_code' => $s_subject_name_code
                                    );

                                    $this->Sbm->save_subject_name($a_subject_name_data);
                                }else{
                                    $s_subject_name_id = $mbo_staging_subject_->subject_name_id;
                                    $s_subject_name_code = $mbo_staging_subject_->subject_name_code;
                                }

                                $mbo_subject_staging_data = $this->Sbm->get_subject_filtered(array('rs.subject_name_id' => $s_subject_name_id))[0];
                                if (!$mbo_subject_staging_data) {
                                    $s_subject_id = $this->uuid->v4();
                                    $a_subject_data = array(
                                        'subject_id' => $s_subject_id,
                                        'subject_name_id' => $s_subject_name_id,
                                        'subject_code' => modules::run('academic/subject/generate_subject_code', $s_subject_name_code, $mbo_staging_student_data->study_program_id, 0),
                                        'study_program_id' => $mbo_staging_student_data->study_program_id,
                                        'program_id' => 1
                                    );

                                    $this->Sbm->save_subject_data($a_subject_data);
                                }else{
                                    $s_subject_id = $mbo_subject_staging_data->subject_id;
                                }

                                $s_curriculum_subject_id = $this->uuid->v4();
                                $a_curriculum_subject_data = array(
                                    'curriculum_subject_id' => $s_curriculum_subject_id,
                                    'curriculum_id' => $mba_curriculum_select[0]->curriculum_id,
                                    'semester_id' => 17,
                                    'subject_id' => $s_subject_id,
                                    'curriculum_subject_credit' => 0,
                                    'curriculum_subject_ects' => 0,
                                    'curriculum_subject_type' => 'mandatory'
                                );
                                $this->Crm->create_new_curriculum_subject($a_curriculum_subject_data);
                            }else{
                                $s_curriculum_subject_id = $mbo_staging_subject_data[0]->curriculum_subject_id;
                                // print('<br>cur_sub: '.$s_curriculum_subject_id);
                            }

                            $s_padding_ofse_score = '90000';
                            $portal_id = $s_padding_ofse_score.$score_data->score_id;
                            $a_staging_score_data = array(
                                'student_id' => $mbo_staging_student_data->student_id,
                                'curriculum_subject_id' => $s_curriculum_subject_id,
                                'semester_id' => '17',
                                'semester_type_id' => '4',
                                'academic_year_id' => '2018',
                                'score_quiz' => $s_ofse_score,
                                'score_final_exam' => $s_ofse_score,
                                'score_sum' => $s_ofse_score,
                                'score_grade' => $this->grades->get_grade($s_ofse_score),
                                'score_approval' => 'approved',
                                'score_examiner' => json_encode($a_score_examiner),
                                'portal_id' => $portal_id
                            );

                            if ($mba_staging_score_data = $this->Scm->get_score_data(array('sc.portal_id' => $portal_id))) {
                                $this->Scm->save_data($a_staging_score_data, array('score_id' => $mba_staging_score_data[0]->score_id));
                            }else{
                                $this->Scm->save_data($a_staging_score_data);
                            }
                        }
                    }else{
                        print('ngga ada nilai ofse!<br>');
                    }
                }else{
                    print('+ ');
                    $mba_score_data = $this->Pm->retrieve_data('score_absence', array(
                        'student_id' => $s_student_id,
                        'semester_id' => 17
                    ));

                    if ($mba_score_data) {
                        foreach ($mba_score_data as $score_data) {
                            $mba_score_data = $this->Pm->get_score_data($score_data->id)[0];

                            if (!$mba_score_data) {
                                print('waddauuuh! --- '.$score_data->id);
                                continue;
                            }

                            $s_ofse_score = $this->grades->get_ofse_score($score_data->examiner_one, $score_data->examiner_two);
                            $a_score_examiner = array(
                                'score_examiner_1' => $score_data->examiner_one,
                                'score_examiner_2' => $score_data->examiner_two
                            );

                            // $mbo_staging_curriculum_data = $this->Sm->retrieve_data('ref_curriculum_subject',array(
                            //     'portal_id' => $score_data->curriculum_subject_id,
                            //     'curriculum_subject_credit' => 0
                            // ));

                            $mbo_staging_subject_data = $this->Crm->get_curriculum_subject_filtered(array(
                                'subject_name' => $mba_score_data->subject_name,
                                'rc.study_program_id' => $mbo_staging_student_data->study_program_id,
                                'rcs.curriculum_subject_credit' => 0
                            ));

                            if (!$mbo_staging_subject_data) {
                                $mba_portal_curriculum_subject_data = $this->Pm->retrieve_data('curriculum_subject', array('id' => $score_data->curriculum_subject_id))[0];
                                if (!$mba_portal_curriculum_subject_data) {
                                    print('curriculum subject id '.$score_data->curriculum_subject_id.' tidak ditemukan di mdb!');
                                    continue;
                                }

                                $mbo_staging_curriculum_ = $this->Sm->retrieve_data('ref_curriculum', array('portal_id' => $mba_portal_curriculum_subject_data->curriculum_id))[0];
                                if (!$mbo_staging_curriculum_) {
                                    print('<br>curriculum id mdb '.$mba_portal_curriculum_subject_data->curriculum_id.' belum disinkronisasi!');
                                    continue;
                                }
                                $mbo_staging_subject_curriculum_data = $this->Sm->retrieve_data('ref_subject', array('portal_id' => $mba_portal_curriculum_subject_data->subject_id))[0];
                                if (!$mbo_staging_subject_curriculum_data) {
                                    print('<br>Subject mdb id '.$mba_portal_curriculum_subject_data->subject_id.' tidak ditemukan!');
                                    continue;
                                }

                                $s_curriculum_subject_id = $this->uuid->v4();
                                $a_curriculum_subject_data = array(
                                    'curriculum_subject_id' => $s_curriculum_subject_id,
                                    'curriculum_id' => $mbo_staging_curriculum_->curriculum_id,
                                    'semester_id' => 17,
                                    'subject_id' => $mbo_staging_subject_curriculum_data->subject_id,
                                    'curriculum_subject_credit' => 0,
                                    'curriculum_subject_ects' => 0,
                                    'curriculum_subject_type' => 'mandatory'
                                );
                                $this->Crm->create_new_curriculum_subject($a_curriculum_subject_data);
                            }else{
                                $s_curriculum_subject_id = $mbo_staging_subject_data[0]->curriculum_subject_id;
                            }

                            $a_staging_score_data = array(
                                'student_id' => $mbo_staging_student_data->student_id,
                                'curriculum_subject_id' => $s_curriculum_subject_id,
                                'semester_id' => '17',
                                'semester_type_id' => '4',
                                'academic_year_id' => '2018',
                                'score_quiz' => $s_ofse_score,
                                'score_final_exam' => $s_ofse_score,
                                'score_sum' => $s_ofse_score,
                                'score_grade' => $this->grades->get_grade($s_ofse_score),
                                'score_approval' => 'approved',
                                'score_examiner' => json_encode($a_score_examiner),
                                'portal_id' => $score_data->id
                            );

                            if ($mba_staging_score_data = $this->Scm->get_score_data(array('sc.portal_id' => $score_data->id))) {
                                $this->Scm->save_data($a_staging_score_data, array('score_id' => $mba_staging_score_data[0]->score_id));
                            }else{
                                $this->Scm->save_data($a_staging_score_data);
                            }
                        }
                    }else{
                        print('wleh weleh!<br>');
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                print('rollback');
            }else{
                $this->db->trans_commit();
                print('commit');
            }
        }
    }

    public function score_data_sync($s_student_id_portal = false)
    {
        print('tidak ada yang disinkron!');
        // if ($s_student_id_portal) {
        //     $mba_portal_student_data = $this->Pm->retrieve_data('student', array('id' => $s_student_id_portal));
        // }else{
        //     $mba_portal_student_data = $this->Pm->retrieve_data('student');
        // }

        // $i = 0;
        // echo '<br>proccessing <span id="test">'.$i.'</span> data';
        // if ($mba_portal_student_data) {
        //     $this->db->trans_start();
        //     foreach ($mba_portal_student_data as $student) {
        //         $mba_portal_score_data = $this->Pm->retrieve_data('score_absence', array(
        //             'student_id' => $student->id,
        //             'semester_id !=' => '17'
        //         ));
        //         if ($mba_portal_score_data) {
        //             $mbo_staging_student_data = $this->Sm->retrieve_data('dt_student', array('portal_id' => $student->id))[0];
        //             if (!$mbo_staging_student_data) {
        //                 print('<br>Student id:'.$student->id.' tidak ditemukan di database staging');
        //                 continue;
        //             }
        //             $remove_score = $this->Sm->remove_staging_data('dt_score', array(
        //                 'student_id' => $mbo_staging_student_data->student_id,
        //                 'portal_id >' => 0
        //             ));
        //             if (!$remove_score) {
        //                 print('<br>failed remove score student id: '.$student->id);
        //             }
        //             print($remove_score);
        //             foreach ($mba_portal_score_data as $score) {
        //                 $mbo_portal_implemented_subject_data = $this->Pm->retrieve_data('implemented_subject', array('id' => $score->implemented_subject_id))[0];
        //                 if ($mbo_portal_implemented_subject_data) {
        //                     $mbo_staging_curriculum_subject_data = $this->Sm->retrieve_data('ref_curriculum_subject', array('portal_id' => $mbo_portal_implemented_subject_data->curriculum_subject_id))[0];
        //                     if ($mbo_staging_curriculum_subject_data) {
        //                         $mbo_portal_class_member_data = $this->Pm->retrieve_data('class_group_member', array('id' => $score->class_group_member_id))[0];
        //                         $mbo_staging_class_data = false;
        //                         if ($mbo_portal_class_member_data) {
        //                             $mbo_portal_class_data = $this->Pm->retrieve_data('class_group', array('id' => $mbo_portal_class_member_data->class_group_id))[0];
        //                             if ($mbo_portal_class_data) {
        //                                 $mbo_staging_class_data = $this->Sm->retrieve_data('dt_class_group', array('portal_id' => $mbo_portal_class_data->id))[0];
        //                             }
        //                         }

        //                         $mbo_staging_class_master_data = false;
        //                         if ($mbo_staging_class_data) {
        //                             $mbo_staging_class_master_data = $this->Sm->retrieve_data('dt_class_master_class', array('class_group_id' => $mbo_staging_class_data->class_group_id))[0];
        //                         }
        
        //                         // if (!$mbo_staging_class_data) {
        //                         //     print('<br>Kelas tidak ditemukan di database staging class_group_member_id:'.$score->class_group_member_id);
        //                         // }
        //                         $mbo_portal_implemented_subject = $this->Pm->retrieve_data('implemented_subject', array('id' => $score->implemented_subject_id))[0];

        //                         if ($mbo_portal_implemented_subject_data->sks == 0) {
        //                             print('<br>score_id: '.$score->id);
        //                         }
        //                         $a_staging_curriculum_subject_update_data = array(
        //                             'curriculum_subject_credit' => $mbo_portal_implemented_subject_data->sks,
        //                             'curriculum_subject_ects' => round((intval($mbo_portal_implemented_subject_data->sks) * 1.4), 2)
        //                         );

        //                         $this->Crm->update_curriculum_subject($a_staging_curriculum_subject_update_data, $mbo_staging_curriculum_subject_data->curriculum_subject_id);

        //                         $mbo_portal_curriculum_subject = $this->Pm->retrieve_data('curriculum_subject', array('id' => $mbo_portal_implemented_subject_data->curriculum_subject_id))[0];
        //                         $mbo_portal_subject_data = $this->Pm->retrieve_data('subject', array('id' => $mbo_portal_curriculum_subject->subject_id))[0];
        //                         // if(!$mbo_portal_subject_data) {
        //                         //     print('\nimplemented_subject_id:'.$score->implemented_subject_id);
        //                         //     // continue;
        //                         // }

        //                         $mbo_portal_subject_name_data = $this->Pm->retrieve_data('subject_name', array('id' => $mbo_portal_subject_data->subject_name_id))[0];
        //                         // if(!$mbo_portal_subject_name_data) {
        //                         //     continue;
        //                         // }
        //                         $i_sks = $mbo_portal_implemented_subject->sks;
        //                         $i_grade_point = $this->grades->get_grade_point($score->score_sum);
        //                         $a_semester_dikti = $this->parsing_semester($mbo_staging_student_data->academic_year_id, $score->semester_id);
                            
        //                         $a_score_data = array(
        //                             'class_group_id' => ($mbo_staging_class_data) ? $mbo_staging_class_data->class_group_id : NULL,
        //                             'class_master_id' => ($mbo_staging_class_master_data) ? $mbo_staging_class_master_data->class_master_id : NULL,
        //                             'student_id' => $mbo_staging_student_data->student_id,
        //                             'curriculum_subject_id' => $mbo_staging_curriculum_subject_data->curriculum_subject_id,
        //                             'semester_id' => $score->semester_id,
        //                             'semester_type_id' => $a_semester_dikti['semester_type_id'],
        //                             'academic_year_id' => $a_semester_dikti['academic_year_id'],
        //                             'score_quiz' => $score->quiz,
        //                             'score_quiz1' => $score->q1,
        //                             'score_quiz2' => $score->q2,
        //                             'score_quiz3' => $score->q3,
        //                             'score_quiz4' => $score->q4,
        //                             'score_quiz5' => $score->q5,
        //                             'score_quiz6' => $score->q6,
        //                             'score_final_exam' => $score->final_exam,
        //                             'score_repetition_exam' => $score->repetition_exam,
        //                             'score_mark_for_repetition' => $score->mark_for_repetition,
        //                             'score_sum' => (is_null($score->score_sum)) ? 0 : $score->score_sum,
        //                             'score_grade' => (is_null($score->grade_point)) ? $this->grades->get_grade($score->score_sum) : $score->grade_point,
        //                             'score_grade_point' => $i_grade_point,
        //                             'score_ects' => round(($i_sks * 1.4), 2, PHP_ROUND_HALF_UP),
        //                             'score_absence' => (is_null($score->absence)) ? 0 : $score->absence,
        //                             'score_merit' => $i_sks * $i_grade_point,
        //                             'score_approval' => (is_null($score->approval)) ? 'pending' : $score->approval,
        //                             'score_display' => $score->score_display,
        //                             'portal_id' => $score->id
        //                         );
        
        //                         if (!in_array($a_semester_dikti['semester_type_id'], array(4, 6))) {
        //                             // $mbo_staging_score_data = $this->Sm->retrieve_data('dt_score', array(
        //                             //     // 'portal_id' => $score->id
        //                             //     'student_id' => $mbo_staging_student_data->student_id,
        //                             //     'curriculum_subject_id' => $mbo_staging_curriculum_subject_data->curriculum_subject_id,
        //                             //     'academic_year_id' => $a_semester_dikti['academic_year_id'],
        //                             //     'semester_type_id' => $a_semester_dikti['semester_type_id']
        //                             // ));
        //                             $mbo_staging_score_data = $this->Sm->get_score_sync(
        //                                 $mbo_staging_student_data->student_id,
        //                                 $mbo_portal_subject_name_data->name,
        //                                 $a_semester_dikti['academic_year_id'],
        //                                 $a_semester_dikti['semester_type_id'],
        //                                 (is_null($score->approval)) ? 'pending' : $score->approval
        //                             );
                                    
        //                             if ($mbo_staging_score_data) {
        //                                 $s_portal_id = $mbo_staging_score_data[0]->portal_id;
        //                                 if (!is_null($mbo_staging_score_data[0]->portal_id)) {
        //                                     // var_dump($mbo_staging_score_data);
        //                                     // print('<br>');
        //                                     // print('<br> update'.$mbo_portal_subject_name_data->name);
        //                                     $this->Scm->save_data($a_score_data, array('score_id' => $mbo_staging_score_data[0]->score_id));
        //                                 }
        //                             }else{
        //                                 $a_score_data['score_id'] = $this->uuid->v4();
        //                                 $a_score_data['date_added'] = date('Y-m-d H:i:s');
        //                                 // print('<br> insert'.$mbo_portal_subject_name_data->name);
        //                                 $this->Scm->save_data($a_score_data);
        //                             }
        //                         }
        //                         print(' .');
        
        //                         $i++;
        //                         echo '<script>
        //                             document.getElementById("test").innerHTML = "'.$i.'";
        //                         </script>';
        //                     }
        //                 }
        //             }
        //         }
        //     }

        //     if ($this->db->trans_status() == FALSE) {
        //         $this->db->trans_rollback();
        //         print('\nrollback');
        //     }else{
        //         $this->db->trans_commit();
        //         print('\ncommit');
        //     }
        // }
    }

    public function sync_class_subject_delivered($s_portal_class_id = false)
    {
        print('<br><b>Syncronize unit of subject delivered');
        if ($s_portal_class_id) {
            $mbo_portal_subject_delivered = $this->Pm->retrieve_data('unit_of_subject_delivered', array('class_group_id' => $s_portal_class_id));
        }else{
            $mbo_portal_subject_delivered = $this->Pm->retrieve_data('unit_of_subject_delivered');
        }

        $this->db->trans_start();

        if ($mbo_portal_subject_delivered) {
            foreach ($mbo_portal_subject_delivered as $subject_deliver) {
                if ($subject_deliver->lecturer_id == 0) {
                    continue;
                }

                $mbo_staging_class_data = $this->Sm->retrieve_data('dt_class_group', array('portal_id' => $subject_deliver->class_group_id))[0];
                if ($mbo_staging_class_data) {
                    $mbo_staging_master_data = $this->Sm->retrieve_data('dt_class_master_class', array('class_group_id' => $mbo_staging_class_data->class_group_id))[0];
                    if ($mbo_staging_master_data) {
                        $s_staging_class_master_id = $mbo_staging_master_data->class_master_id;
                        $s_subject_delivered_id = $this->uuid->v4();
                        $a_class_subject_delivered_data = array(
                            'subject_delivered_id' => $s_subject_delivered_id,
                            'class_master_id' => $s_staging_class_master_id,
                            'employee_id' => $this->get_staging_lecturer_employee_data($subject_deliver->lecturer_id),
                            'subject_delivered_time_start' => $subject_deliver->start_time,
                            'subject_delivered_time_end' =>  $subject_deliver->end_time,
                            'subject_delivered_description' => $subject_deliver->description,
                            'date_added' => $subject_deliver->created_timestamp,
                            'portal_id' => $subject_deliver->id
                        );

                        $mbo_staging_subject_delivered_data = $this->Sm->retrieve_data('dt_class_subject_delivered', array('portal_id' => $subject_deliver->id))[0];
                        // print('. ');
                        // $save_data = false;
                        if ($mbo_staging_subject_delivered_data) {
                            unset($a_class_subject_delivered_data['subject_delivered_id']);
                            $s_subject_delivered_id = $mbo_staging_subject_delivered_data->subject_delivered_id;
                            $save_data = $this->Cgm->save_subject_delivered($a_class_subject_delivered_data, $mbo_staging_subject_delivered_data->subject_delivered_id);
                            // if ($this->Cgm->remove_unit_subject_for_sync($s_staging_class_master_id)) {
                            //     $save_data = $this->Cgm->save_subject_delivered($a_class_subject_delivered_data, $mbo_staging_subject_delivered_data->subject_delivered_id);
                            // }else {
                            //     print('<pre>Tidak ');
                            // }
                        }else {
                            $save_data = $this->Cgm->save_subject_delivered($a_class_subject_delivered_data);
                        }

                        $mba_portal_student_absence = $this->Pm->retrieve_data('student_absence', array('unit_of_subject_delivered_id' => $subject_deliver->id));
                        if ($mba_portal_student_absence) {
                            foreach ($mba_portal_student_absence as $student_absence) {
                                $mbo_staging_score_data = $this->Sm->retrieve_data('dt_score', array('portal_id' => $student_absence->score_absence_id))[0];
                                if ($mbo_staging_score_data) {
                                    $mbo_staging_student_absence = $this->Sm->retrieve_data('dt_absence_student', array('score_id' => $mbo_staging_score_data->score_id, 'subject_delivered_id' => $s_subject_delivered_id))[0];
                                    $s_absence_student_id = $this->uuid->v4();
                                    $a_absence_data = array(
                                        'absence_student_id' => $s_absence_student_id,
                                        'subject_delivered_id' => $s_subject_delivered_id,
                                        'score_id' => $mbo_staging_score_data->score_id,
                                        'absence_status' => ($student_absence->absence == 'ALPHA') ? 'ABSENT' : $student_absence->absence,
                                        'absence_note' => $student_absence->note,
                                        'date_added' => $student_absence->timestamp
                                    );

                                    if ($mbo_staging_student_absence) {
                                        // unset($a_absence_data['absence_student_id']);
                                        $this->Cgm->save_student_absence($a_absence_data, array('absence_student_id' => $mbo_staging_student_absence->absence_student_id));
                                    }else{
                                        $this->Cgm->save_student_absence($a_absence_data);
                                    }
                                }
                            }
                        }

                        if ($save_data) {
                            // $this->sync_student_absence($subject_deliver->id);
                            print('. ');
                        }else{
                            print('<pre>Save gagal !</pre>');exit;
                        }
                    }else{
                        // print('<pre>class group id "'.$mbo_staging_class_data->class_group_id.'" not found in master class</pre>');
                    }
                }else{
                    // print('<pre>class group portal id "'.$subject_deliver->class_group_id.'" not found in database staging portal2</pre>');
                }
            }
        }else{
            // print('<pre>class group id "'.$mbo_staging_class_data->class_group_id.'" not found in database portal</pre>');
        }

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            print('Proccess Success<br>Data Commit');
        }else{
            $this->db->trans_rollback();
            print('Proccess Failed<br>Data Rollback');
        }
    }

    public function sync_student_absence($s_portal_uosd_id = false)
    {
        if ($s_portal_uosd_id) {
            $mbo_uosd_data = $this->Pm->retrieve_data('unit_of_subject_delivered', array('id' => $s_portal_uosd_id));
        }else {
            $mbo_uosd_data = $this->Pm->retrieve_data('unit_of_subject_delivered');
        }

        foreach ($mbo_uosd_data as $uosd) {
            $mbo_staging_uosd_data = $this->Sm->retrieve_data('dt_class_subject_delivered', array('portal_id' => $uosd->id))[0];
            if ($mbo_staging_uosd_data) {
                $mbo_student_absence_lists = $this->Pm->retrieve_data('student_absence', array('unit_of_subject_delivered_id' => $uosd->id));
                if ($mbo_student_absence_lists) {
                    $mbo_staging_absence_data = $this->Sm->retrieve_data('dt_absence_student', array('subject_delivered_id' => $mbo_staging_uosd_data->subject_delivered_id));
                    if ($mbo_staging_absence_data) {
                        $this->Cgm->remove_absence_student(array('subject_delivered_id' => $mbo_staging_uosd_data->subject_delivered_id));
                    }

                    foreach ($mbo_student_absence_lists as $absence) {
                        $s_absence_student_id = $this->uuid->v4();
                        $mbo_portal_score_data = $this->Pm->retrieve_data('score_absence', array('id' => $absence->score_absence_id));
                        if ($mbo_portal_score_data) {
                            $mbo_staging_score_data = $this->Sm->retrieve_data('dt_score', array('portal_id' => $absence->score_absence_id))[0];
                            if ($mbo_staging_score_data) {
                                $a_absence_student_data = array(
                                    'absence_student_id' =>$s_absence_student_id,
                                    'score_id' => $mbo_staging_score_data->score_id,
                                    'subject_delivered_id' => $mbo_staging_uosd_data->subject_delivered_id,
                                    'absence_status' => $absence->absence,
                                    'absence_note' => ($absence->note == '') ? null : $absence->note,
                                    'date_added' => $absence->timestamp
                                );

                                if ($this->Cgm->save_student_absence($a_absence_student_data)) {
                                    print('. ');
                                }else{
                                    print('ga ke save');exit;
                                }
                            }else{
                                print('<pre>Student score not found in staging data => portal score_id: '.$absence->score_absence_id.'</pre>');
                            }
                        }else{
                            print('3 ');
                        }
                    }
                }else{
                    print('2 ');
                }
            }else{
                print('1 ');
            }
        }
    }
    
    public function sync_class_lecturer($s_portal_class_id)
    {
        $mbo_portal_class_data = $this->Pm->retrieve_data('class_group', array('id' => $s_portal_class_id))[0];
        $mbo_staging_class_data = $this->Sm->retrieve_data('dt_class_group', array('portal_id' => $s_portal_class_id))[0];
        if (!$mbo_staging_class_data) {
            print('<pre>class not found in staging. Portal id:</pre>');
            var_dump($s_portal_class_id);exit;
        }
        $mbo_portal_class_lecturer_lists = $this->Pm->retrieve_data('class_group', array('team_teaching' => $s_portal_class_id));
        $mbo_staging_class_subject_lists = $this->Sm->retrieve_data('dt_class_group_subject', array('class_group_id' => $mbo_staging_class_data->class_group_id));
        $i_count_sks = 0;
        if ($mbo_staging_class_subject_lists) {
            foreach ($mbo_staging_class_subject_lists as $class_subject) {
                $mbo_staging_offered_subject_data = $this->Sm->retrieve_data('dt_offered_subject', array('offered_subject_id' => $class_subject->offered_subject_id))[0];
                $mbo_staging_curriculum_data = $this->Sm->retrieve_data('ref_curriculum_subject', array('curriculum_subject_id' => $mbo_staging_offered_subject_data->curriculum_subject_id))[0];
                $i_count_sks += $mbo_staging_curriculum_data->curriculum_subject_credit;
            }
        }

        $a_portal_lect_id = array();
        if ($mbo_portal_class_lecturer_lists) {
			array_push($a_portal_lect_id, array(
				'lecturer_id' => $mbo_portal_class_data->lecturer_id,
				'sks_allocation' => (is_null($mbo_portal_class_data->sks_allocation)) ? $i_count_sks : $mbo_portal_class_data->sks_allocation,
				'class_group_lecturer_preferable_day' => (is_null($mbo_portal_class_data->day)) ? $i_count_sks : $mbo_portal_class_data->day,
				'class_group_lecturer_preferable_time' => (is_null($mbo_portal_class_data->time)) ? $i_count_sks : $mbo_portal_class_data->time,
				'class_group_lecturer_priority' => (is_null($mbo_portal_class_data->priority)) ? $i_count_sks : $mbo_portal_class_data->priority
			));
			foreach ($mbo_portal_class_lecturer_lists as $lect_class) {
				if (is_null($lect_class->sks_allocation)) {
					$sks_allocation = $i_count_sks / count($mbo_portal_class_lecturer_lists)+1;
				}else{
					$sks_allocation = $lect_class->sks_allocation;
				}
				
				array_push($a_portal_lect_id, array(
					'lecturer_id' => $lect_class->lecturer_id,
					'sks_allocation' => $sks_allocation,
					'class_group_lecturer_preferable_day' => (is_null($mbo_portal_class_data->day)) ? $i_count_sks : $mbo_portal_class_data->day,
					'class_group_lecturer_preferable_time' => (is_null($mbo_portal_class_data->time)) ? $i_count_sks : $mbo_portal_class_data->time,
					'class_group_lecturer_priority' => (is_null($mbo_portal_class_data->priority)) ? $i_count_sks : $mbo_portal_class_data->priority
				));
			}
		}else{
			array_push($a_portal_lect_id, array(
				'lecturer_id' => $mbo_portal_class_data->lecturer_id,
				'sks_allocation' => (is_null($mbo_portal_class_data->sks_allocation)) ? $i_count_sks : $mbo_portal_class_data->sks_allocation,
				'class_group_lecturer_preferable_day' => (is_null($mbo_portal_class_data->day)) ? $i_count_sks : $mbo_portal_class_data->day,
				'class_group_lecturer_preferable_time' => (is_null($mbo_portal_class_data->time)) ? $i_count_sks : $mbo_portal_class_data->time,
				'class_group_lecturer_priority' => (is_null($mbo_portal_class_data->priority)) ? $i_count_sks : $mbo_portal_class_data->priority
			));
        }
        
        $mbo_staging_class_lecturer_data = $this->Sm->retrieve_data('dt_class_group_lecturer', array('class_group_id' => $mbo_staging_class_data->class_group_id))[0];
        if ($mbo_staging_class_lecturer_data) {
            $this->Cgm->remove_class_group_lecturer(array('class_group_id' => $mbo_staging_class_data->class_group_id));
        }

        if (count($a_portal_lect_id) > 0) {
            foreach ($a_portal_lect_id as $lect_id) {
                $s_class_group_lect_id = $this->uuid->v4();
                $mbo_portal_lecturer_data = $this->Pm->retrieve_data('lecturer', array('id' => $lect_id['lecturer_id']));
                $mbo_staging_lecturer_data = $this->Sm->retrieve_data('dt_employee', array('employee_is_lecturer' => 'YES', 'portal_id' => $lect_id['lecturer_id']));
                if (!$mbo_staging_lecturer_data) {
                    continue;
                }
    
                $a_class_group_lecturer_data = array(
                    'class_group_lecturer_id' => $s_class_group_lect_id,
                    'class_group_id' => $mbo_staging_class_data->class_group_id,
                    'employee_id' => $mbo_staging_lecturer_data[0]->employee_id,
                    'employee_id_reported' => $s_employee_id,
                    'credit_allocation' => $lect_id['sks_allocation'],
                    'credit_charged' => $lect_id['sks_allocation'],
                    'credit_realization' => $lect_id['sks_allocation'],
                    'is_reported_to_feeder' => ($mbo_portal_lecturer_data) ? strtolower($mbo_portal_lecturer_data[0]->is_reported) : 'false',
                    'class_group_lecturer_preferable_day' => $lect_id['class_group_lecturer_preferable_day'],
                    'class_group_lecturer_preferable_time' => $lect_id['class_group_lecturer_preferable_time'],
                    'class_group_lecturer_priority' => $lect_id['class_group_lecturer_priority'],
                    'date_added' => date('Y-m-d H:i:s')
                );
    
                $save_class_lecturer = $this->Cgm->save_class_group_lecturer($a_class_group_lecturer_data);
                if (!$save_class_lecturer) {
                    var_dump($a_class_group_lecturer_data);exit;
                }
            }
        }
    }

    public function sync_curriculum($s_portal_curriculum_id = false)
    {
        print('<br>syncronize curriculum data');

        if ($s_portal_curriculum_id) {
            $mbo_portal_curriculum_data = $this->Pm->retrieve_data('curriculum', array('id' => $s_portal_curriculum_id));
        }else{
            $mbo_portal_curriculum_data = $this->Pm->retrieve_data('curriculum');
        }
        $i_insert = 0;
        $i_update = 0;
        $i_insert_curr_semester = 0;
        $i_update_curr_semester = 0;
        $i_insert_curr_subject = 0;
        $i_update_curr_subject = 0;

        $this->db->trans_start();

        if ($mbo_portal_curriculum_data) {
            foreach ($mbo_portal_curriculum_data as $curriculum) {
                if (!in_array($curriculum->study_program_id, $this->a_prodi_inactive)) {
                    if (($curriculum->study_program_id != 0) AND ($curriculum->academic_year_id != 0)) {
                        $mbo_staging_study_program_data = $this->Sm->retrieve_data('ref_study_program', array('study_program_id' => $this->get_staging_study_program($curriculum->study_program_id)))[0];
                        $s_curriculum_id = $this->uuid->v4();
                        $a_curriculum_data = array(
                            'study_program_id' => $mbo_staging_study_program_data->study_program_id,
                            'program_id' => '1',
                            'academic_year_id' => $this->get_staging_academic_year($curriculum->academic_year_id),
                            'valid_academic_year' => $this->get_staging_academic_year($curriculum->academic_year_id),
                            'curriculum_name' => $curriculum->name,
                            'portal_id' => $curriculum->id,
                            'date_added' => date('Y-m-d H:i:s')
                        );

                        print(' .');
                        if ($mbo_staging_curriculum_data = $this->Sm->retrieve_data('ref_curriculum', array('portal_id' => $curriculum->id))) {
                            $s_curriculum_id = $mbo_staging_curriculum_data[0]->curriculum_id;
                            $this->Crm->update_curriculum($a_curriculum_data, $s_curriculum_id);
                            $i_update++;
                        }else{
                            $a_curriculum_data['curriculum_id'] = $s_curriculum_id;
                            $this->Crm->create_new_curriculum($a_curriculum_data);
                            $i_insert++;
                        }

                        $mbo_curriculum_subject = $this->Pm->retrieve_data('curriculum_subject', array('curriculum_id' => $curriculum->id));
                        if ($mbo_curriculum_subject) {
                            foreach ($mbo_curriculum_subject as $cursub) {
                                if ($cursub->semester_id !=0) {
                                    $staging_subject_data = $this->Sm->retrieve_data('ref_subject', array('portal_id' => $cursub->subject_id))[0];
                                    $portal_semester_data = $this->Pm->retrieve_data('semester', array('id' => $cursub->semester_id))[0];
                                    if (!$staging_subject_data) {
                                        $this->sync_subject($cursub->subject_id);
                                        $staging_subject_data = $this->Sm->retrieve_data('ref_subject', array('portal_id' => $cursub->subject_id))[0];
                                        if (!$staging_subject_data) {
                                            print('error sync subject id'.$cursub->subject_id);
                                            continue;
                                        }
                                    }

                                    $staging_subject_name_data = $this->Sm->retrieve_data('ref_subject_name', array('subject_name_id' => $staging_subject_data->subject_name_id))[0];
                                    $s_staging_curriculum_subject_code = modules::run('academic/generate_curriculum_subject_code', $staging_subject_name_data->subject_name_code, $mbo_staging_study_program_data->study_program_abbreviation, $cursub->semester_id, $this->get_semester_type_data($portal_semester_data->type));
                                    $a_curriculum_subject_data = array(
                                        'curriculum_subject_id' => $this->uuid->v4(),
                                        'curriculum_id' => $s_curriculum_id,
                                        'semester_id' => $cursub->semester_id,
                                        'subject_id' => $staging_subject_data->subject_id,
                                        'curriculum_subject_code' => $s_staging_curriculum_subject_code,
                                        'curriculum_subject_credit' => $cursub->sks,
                                        'curriculum_subject_ects' => ($cursub->ects != NULL) ? $cursub->ects : round((intval($cursub->sks) * 1.4), 2),
                                        'curriculum_subject_type' => strtolower($cursub->subject_type),
                                        'portal_id' => $cursub->id
                                    );

                                    $a_curriculum_semester_data = array(
                                        'curriculum_id' => $s_curriculum_id,
                                        'semester_id' => $cursub->semester_id
                                    );

                                    if ($mbo_staging_curriculum_semester_data = $this->Sm->retrieve_data('ref_curriculum_semester', array('curriculum_id' => $s_curriculum_id, 'semester_id' => $cursub->semester_id))) {
                                        $this->Crm->update_credit_count_curriculum_semester($s_curriculum_id, $cursub->semester_id, strtolower($cursub->subject_type));
                                        $i_update_curr_semester++;
                                    }else{
                                        $this->Crm->create_new_curriculum_semester($a_curriculum_semester_data);
                                        $i_insert_curr_semester++;
                                    }
        
                                    if ($mbo_staging_curriculum_subject_data = $this->Sm->retrieve_data('ref_curriculum_subject', array('portal_id' => $cursub->id))[0]) {
                                        $this->Crm->update_curriculum_subject($a_curriculum_subject_data, $mbo_staging_curriculum_subject_data->curriculum_subject_id);
                                        $i_update_curr_subject++;
                                    }else{
                                        $this->Crm->create_new_curriculum_subject($a_curriculum_subject_data);
                                        $i_insert_curr_subject++;
                                    }
                                }
                            }

                            $this->Crm->update_credit_curriculum($s_curriculum_id);
                        }
                    }
                }
            }
        }

        if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			print('<p><b>Curriculum rollback</b></p>');
		}else{
            $this->db->trans_commit();
            print('<p><b>Curriculum commit</b><br>
            New data <b>'.$i_insert.'</b><br>
            Updated data <b>'.$i_update.'</b><br>
            New curriculum semester data <b>'.$i_insert_curr_semester.'</b><br>
            Update curriculum semester data <b>'.$i_update_curr_semester.'</b><br>
            New curriculum subject data <b>'.$i_insert_curr_subject.'</b><br>
            Update curriculum subject data <b>'.$i_update_curr_subject.'</b><br></p>');
		}
    }

    public function checker_offered_subject($a_offered_subject_data)
    {
        $a_checked_param = array(
            'rsn.subject_name' => $a_offered_subject_data['subject_name'],
            'dos.academic_year_id' => $a_offered_subject_data['academic_year_id'],
            'dos.semester_type_id' => $a_offered_subject_data['semester_type_id'],
            'dos.study_program_id' => $a_offered_subject_data['study_program_id']
        );
        
        $mbo_staging_offered_subject_data = $this->Osm->get_offered_subject_lists_filtered($a_checked_param)[0];
        if ($mbo_staging_offered_subject_data) {
            $mba_portal_offered_subject_lecturer = $this->Pm->retrieve_offered_subject_lect($a_offered_subject_data['portal_id']);
            if ($mba_portal_offered_subject_lecturer) {
                $return = false;
                foreach ($mba_portal_offered_subject_lecturer as $portal_lect) {
                    if ((!is_null($portal_lect->lecturer_id)) OR (!is_null($portal_lect->team_teaching))) {
                        $return = true;
                        break;
                    }
                }
                if ($return) {
                    $mba_staging_offered_subject_lecturer = $this->Osm->get_offer_subject_class_group(array(
                        'cgs.offered_subject_id' => $mbo_staging_offered_subject_data->offered_subject_id
                    ));
                    // $mba_staging_offered_subject_lecturer = $this->
                    if ($mba_staging_offered_subject_lecturer) {
                        $return = false;
                    }else{
                        $return = true;
                    }
                }
            }else{
                $return = false;
            }
        }else{
            $return = true;
        }

        return $return;
        // var_dump($return);
    }

    public function clear_offer_subject_lecturer()
    {
        print('<h2>Remove offered subject without lecturer</h2>');
        $this->db->trans_start();
        $mba_staging_offer_subject_data = $this->Sm->retrieve_data('dt_offered_subject');
        if ($mba_staging_offer_subject_data) {
            foreach ($mba_staging_offer_subject_data as $offer_subject) {
                # check lecturer
                $mba_subject_lecturer = $this->Osm->get_offer_subject_lecturer($offer_subject->offered_subject_id);
                if (!$mba_subject_lecturer) {
                    # remove offered subject
                    $remove_offer_subject = $this->Sm->remove_staging_data('dt_offered_subject', array('offered_subject_id' => $offer_subject->offered_subject_id));
                    print('. ');
                    // #  remove class group
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            print('<pre>Rollback</pre>');
        }else{
            $this->db->trans_commit();
            print('<pre>Commit</pre>');
        }
    }

    // public function tester()
    // {
    //     $mba_offered_subject_groups_cursub = $this->db->from('dt_offered_subject ofs')
    //         ->where('academic_year_id', '2019')->where('semester_type_id', '2')
    //         ->group_by('curriculum_subject_id')->get()->result();

    //     if (count($mba_offered_subject_groups_cursub) > 1) {
    //         foreach ($mba_offered_subject_groups_cursub as $offer_groups) {
    //             $mba_offered_subject_data = $this->Sm->retrieve_data('dt_offered_subject', array('curriculum_subject_id' => $offer_groups->curriculum_subject_id));
    //             if ($mba_offered_subject_data) {
    //                 $a_offered_subject_id_lists = array();
    //                 foreach ($a_offered_subject_id_lists as $offerd) {
    //                     if (!in_array($offerd->offered_subject_id, $a_offered_subject_id_lists)) {
    //                         array_push($a_offered_subject_id_lists, $offerd->offered_subject_id);
    //                     }
    //                 }

    //                 if (count($a_offered_subject_id_lists) > 0) {
    //                     $mba_class_subject_data = $this->db->from('dt_class_group_subject')
    //                         ->where_in('offered_subject_id', $a_offered_subject_id_lists)->get()->result();
    //                     if (count($mba_class_subject_data) > 0) {
    //                         $a_class_id_lists = array();
    //                         foreach ($mba_class_subject_data as $class_subject) {
    //                             # code...
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // }

    // public function clear_offered_subject_duplicate()
    // {
    //     $mba_offered_subject_groups_cursub = $this->db->from('dt_offered_subject ofs')
    //         ->where('academic_year_id', '2019')->where('semester_type_id', '2')
    //         ->group_by('curriculum_subject_id')->get()->result();
    //     if (count($mba_offered_subject_groups_cursub) > 1) {
    //         $a_offered_subject_id = array();
    //         foreach ($mba_offered_subject_groups_cursub as $offer) {
    //             $mba_offered_subject_data = $this->Sm->retrieve_data('dt_offered_subject_data', array('curriculum_subject_id' => $offer->curriculum_subject_id));
    //             if (count($mba_offered_subject_data) > 1) {
    //                 $a_offer_subject_id_remove = array();
    //                 foreach ($mba_offered_subject_data as $offer_subject) {
    //                     $b_remove = false;
    //                     $mba_class_subject_data = $this->db->from('dt_class_group_subject')
    //                         ->where('offered_subject_id', $offer_subject->offered_subject_id)
    //                         ->group_by('class_group_id')->get()->result();
    //                     if($mba_class_subject_data) {
    //                         foreach ($mba_class_subject_data as $class) {
    //                             $mba_score_data = $this->Sm->retrieve_data('dt_score', array('class_group_id' => $class->class_group_id));
    //                             if (!$mba_score_data) {
    //                                 $b_remove = true;
    //                             }
    //                             $mba_class_lecturer_data = $this->Sm->retrieve_data('dt_class_group_lecturer', array('class_group_id' => $class->class_group_id));
    //                             if ($mba_class_lecturer_data) {
    //                                 $a_lecturer = array();
    //                                 foreach ($mba_class_lecturer_data as $class_lecturer) {
    //                                     if (!in_array($class_lecturer->employee_id, $a_lecturer)) {
    //                                         array_push($a_lecturer, $class_lecturer->employee_id);
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //         // print(count($mba_offered_subject_groups_cursub));
    //     }
    // }

    public function sync_offer_subject($implemented_subject_id = false)
	{
		$a_curriculum_subject_notfound = array(286, 287, 289, 291, 292, 293, 294, 295, 296, 298);
		
		$mbo_curriculum_subject_notfound_data = $this->Pm->retrieve_data('implemented_subject', 'curriculum_subject_id NOT IN (SELECT id FROM curriculum_subject)');
		foreach ($mbo_curriculum_subject_notfound_data as $notfound) {
			array_push($a_curriculum_subject_notfound, $notfound->curriculum_subject_id);
		}

		$mbo_curriculum_subject_notfound_semester = $this->Pm->retrieve_data('implemented_subject', 'curriculum_subject_id IN (SELECT id FROM curriculum_subject WHERE semester_id=0)');
		foreach ($mbo_curriculum_subject_notfound_semester as $semester_noll) {
			array_push($a_curriculum_subject_notfound, $semester_noll->curriculum_subject_id);
        }
        
        if ($implemented_subject_id) {
            $mbo_portal_offer_subject_data = $this->Pm->retrieve_data('implemented_subject', array('id' => $implemented_subject_id));
        }else{
            $mbo_portal_offer_subject_data = $this->Pm->retrieve_data('implemented_subject');
        }
        
        $i_insert = 0;
        $i_update = 0;
        $i_delete = 0;

		$this->db->trans_start();
		print('<br><b>syncronize offered subject data</b>');
		foreach ($mbo_portal_offer_subject_data as $offer_subject) {
            if (!is_null($offer_subject->curriculum_subject_id)) {
                if (!is_null($offer_subject->study_program_id)) {
					if (!in_array($offer_subject->curriculum_subject_id, $a_curriculum_subject_notfound)) {
                        $s_offered_subject_id = $this->uuid->v4();
						$mbo_staging_curriculum_subject = $this->Sm->retrieve_data('ref_curriculum_subject', array('portal_id' => $offer_subject->curriculum_subject_id))[0];
						if (!$mbo_staging_curriculum_subject) {
                            $mba_curriculum_portal_data = $this->Pm->retrieve_data('curriculum_subject', array('id' => $offer_subject->curriculum_subject_id))[0];
                            $this->sync_curriculum($mba_curriculum_portal_data->curriculum_id);
                            $mbo_staging_curriculum_subject = $this->Sm->retrieve_data('ref_curriculum_subject', array('portal_id' => $offer_subject->curriculum_subject_id))[0];
                            if (!$mbo_staging_curriculum_subject) {
                                var_dump('error sync curriculum subject id: '.$offer_subject->curriculum_subject_id);exit;
                            }
                        }
                        if (is_null($offer_subject->running_year)) {
                            continue;
                        }

                        $mbo_portal_academic_year = $this->Pm->retrieve_data('academic_year', array('id' => $offer_subject->running_year))[0];
						$mbo_staging_semester = $this->Sm->retrieve_data('ref_semester', array('semester_id' => $offer_subject->semester_id))[0];
						$mbo_portal_prodi_data = $this->Pm->retrieve_data('study_program', array('id' => $offer_subject->study_program_id))[0];
						if (!$mbo_portal_prodi_data) {
							var_dump('study program not found:'.$offer_subject->study_program_id);exit;
                        }
                        $a_semester_dikti = $this->parsing_semester($mbo_portal_academic_year->year_name, $offer_subject->semester_id);
                        $mbo_staging_prodi_data = $this->Sm->retrieve_data('ref_study_program', array('study_program_name' => $mbo_portal_prodi_data->name))[0];
                        $mbo_portal_curriculum_data = $this->Pm->retrieve_data('curriculum_subject', array('id' => $offer_subject->curriculum_subject_id))[0];
                        $mbo_portal_subject_data = $this->Pm->retrieve_data('subject', array('id' => $mbo_portal_curriculum_data->subject_id))[0];
                        $mbo_portal_subject_name_data = $this->Pm->retrieve_data('subject_name', array('id' => $mbo_portal_subject_data->subject_name_id))[0];
                        
                        $a_staging_offered_subject_data = array(
							'offered_subject_id' => $s_offered_subject_id,
							'curriculum_subject_id' => $mbo_staging_curriculum_subject->curriculum_subject_id,
                            'academic_year_id' => $mbo_portal_academic_year->year_name,
                            'semester_type_id' => $a_semester_dikti['semester_type_id'],
							'program_id' => '1',
							'study_program_id' => $mbo_staging_prodi_data->study_program_id,
							'portal_id' => $offer_subject->id
                        );
                        
                        $mba_offered_subject_exist = $this->Sm->retrieve_data('dt_offered_subject', array(
                            'curriculum_subject_id' => $mbo_staging_curriculum_subject->curriculum_subject_id,
                            'academic_year_id' => $mbo_portal_academic_year->year_name,
                            'semester_type_id' => $a_semester_dikti['semester_type_id'],
                            'study_program_id' => $mbo_staging_prodi_data->study_program_id
                        ));
                        // if ($this->checker_offered_subject(array(
                        //     'subject_name' => $mbo_portal_subject_name_data->name,
                        //     'academic_year_id' => $mbo_portal_academic_year->year_name,
                        //     'semester_type_id' => $this->get_staging_semester_type_id($offer_subject->semester_id),
                        //     'study_program_id' => $mbo_staging_prodi_data->study_program_id,
                        //     'portal_id' => $offer_subject->id
                        // ))) {

                        if (!$mba_offered_subject_exist) {
                            if ($cek_data = $this->Sm->retrieve_data('dt_offered_subject', array('portal_id' => $offer_subject->id))) {
                                if ($save_data = $this->Osm->update_sync_offered_subject($a_staging_offered_subject_data, $cek_data[0]->offered_subject_id)) {
                                    print('* ');
                                    $i_update++;
                                }else{
                                    print('err_upt:'.$save_data);exit;
                                }
                            }else{
                                if ($save_data = $this->Osm->save_offer_subject($a_staging_offered_subject_data)) {
                                    print('. ');
                                    $i_insert++;
                                }else{
                                    print('err_save:'.$save_data);exit;
                                }
                            }
                        }
                        // }else{
                        //     $cek_data = $this->Sm->retrieve_data('dt_offered_subject', array('portal_id' => $offer_subject->id));
                        //     if ($cek_data) {
                        //         $remove = $this->Sm->remove_staging_data('dt_offered_subject', array('portal_id' => $offer_subject->id));
                        //         if ($remove) {
                        //             print('^ ');
                        //             $i_delete++;
                        //         }else{
                        //             print('err_del:'.$remove);exit;
                        //         }
                        //     }
                        // }
					}
				}
			}
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			print('<b>Offered Subject Rollback</b>');
		}else{
            $this->db->trans_commit();
            // $this->sync_class_group();
            // $this->clear_offer_subject_lecturer();
            print('<p><b>Offered Subject commit</b><br>
            New data <b>'.$i_insert.'</b><br>
            Updated data <b>'.$i_update.'</b><br></p>');
		}
	}

    public function sync_subject($s_portal_subject_id = false)
    {
        print('<br>syncronize subject data');
        
        if ($s_portal_subject_id) {
            $mbo_portal_subject_lists = $this->Pm->retrieve_data('subject', array('id' => $s_portal_subject_id));
        }else{
            $mbo_portal_subject_lists = $this->Pm->retrieve_data('subject');
        }
        $i_insert = 0;
        $i_update = 0;

		$this->db->trans_start();
		foreach ($mbo_portal_subject_lists as $subject) {
            if (!in_array($subject->study_program_id, $this->a_prodi_inactive)) {
                $portal_subject_id = $subject->id;
                $s_subject_id = (is_null($subject->id_feeder)) ? $this->uuid->v4() : $subject->id_feeder;
                $mbo_portal_subject_name = $this->Pm->retrieve_data('subject_name', array('id' => $subject->subject_name_id));
                $mbo_portal_prodi_data = $this->Pm->retrieve_data('study_program', array('id' => $subject->study_program_id))[0];
                if ($mbo_portal_prodi_data) {
                    $mbo_staging_prodi_data = $this->Sm->retrieve_data('ref_study_program', array('study_program_name' => $mbo_portal_prodi_data->name));
                    if (!$mbo_staging_prodi_data) {
                        print('Prodi tidak ditemukan di staging');
                        var_dump($mbo_portal_prodi_data->name);exit;
                    }
                    
                    if ($mbo_portal_subject_name) {
                        $a_subject_name = $mbo_portal_subject_name[0];
                        $s_subject_name_id = $this->uuid->v4();
                        $subject_name_code = modules::run('academic/subject/generate_subject_name_code', $a_subject_name->name);
                        $s_subject_code = modules::run('academic/subject/generate_subject_code', $subject_name_code, $mbo_staging_prodi_data[0]->study_program_id, $subject->sks);
                        $a_subject_name_data = array(
                            'portal_id' => $a_subject_name->id,
                            'subject_name_id' => $s_subject_name_id,
                            'subject_name' => $a_subject_name->name,
                            'subject_name_code' => $subject_name_code
                        );
                        
                        $a_subject_data = array(
                            'subject_id' => $s_subject_id,
                            'subject_name_id' => $s_subject_name_id,
                            'subject_code' => $s_subject_code,
                            'study_program_id' => ($mbo_staging_prodi_data) ? $mbo_staging_prodi_data[0]->study_program_id : NULL,
                            'program_id' => '1',
                            'subject_credit' => $subject->sks,
                            'subject_credit_tm' => $subject->sks,
                            'portal_id' => $portal_subject_id
                        );

                        if ($subject_name_data = $this->Sm->retrieve_data('ref_subject_name', array('portal_id' => $a_subject_name->id))) {
                            if ($this->Sbm->save_subject_name($a_subject_name_data, $subject_name_data[0]->subject_name_id)) {
                                if ($mbo_subject_data = $this->Sm->retrieve_data('ref_subject', array('portal_id' => $portal_subject_id))) {
                                    unset($a_subject_data['subject_code']);
                                    if ($this->Sbm->save_subject_data($a_subject_data, $mbo_subject_data[0]->subject_id)) {
                                        $i_update++;
                                        print('- ');
                                    }else{
                                        print('x ');
                                    }
                                }else{
                                    if ($this->Sbm->save_subject_data($a_subject_data)) {
                                        $i_insert++;
                                        print('. ');
                                    }else{
                                        print('x ');
                                    }
                                }
                            }else{
                                print('-x-');
                            }
                        }else{
                            if ($this->Sbm->save_subject_name($a_subject_name_data)) {
                                $mbo_subject_staging_data = $this->Sm->retrieve_data('ref_subject', array('portal_id' => $portal_subject_id));
                                if ($mbo_subject_staging_data) {
                                    unset($a_subject_data['subject_id']);
                                    unset($a_subject_data['subject_code']);
                                    if ($this->Sbm->save_subject_data($a_subject_data, $mbo_subject_staging_data[0]->subject_id)) {
                                        print('- ');
                                    }else{
                                        print('x');
                                    }
                                }else{
                                    if ($this->Sbm->save_subject_data($a_subject_data)) {
                                        print('. ');
                                    }else{
                                        print('x ');
                                    }
                                }
                            }else{
                                print('x');
                            }
                        }
                    }
                }
            }
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			print('<p><b>Subject rollback</b></p>');
		}else{
			$this->db->trans_commit();
			print('<p><b>Subject commit</b><br><b>'.$i_insert.'</b> data baru di masukan<br><b>'.$i_update.'</b> data di update</p>');
		}
    }

    public function get_staging_academic_year($s_portal_academic_year_id)
    {
        $mbo_portal_academic_year_data = $this->Pm->retrieve_data('academic_year', array('id' => $s_portal_academic_year_id))[0];
        if ($mbo_portal_academic_year_data) {
            return $mbo_portal_academic_year_data->year_name;
        }else{
            print('error get academic year in portal<br>id:'.$s_portal_academic_year_id);exit;
        }
    }

    public function get_staging_semester_type_id($s_portal_semester_id)
    {
        $mbo_portal_semester_data = $this->Pm->retrieve_data('semester', array('id' => $s_portal_semester_id))[0];
        if ($mbo_portal_semester_data) {
            return $this->get_semester_type_data($mbo_portal_semester_data->type)[0];
        }else{
            print('error get semester in portal<br>id:'.$s_portal_semester_id);exit;
        }
    }

    public function get_staging_study_program($s_portal_study_program_id)
    {
        $mbo_portal_study_program_data = $this->Pm->retrieve_data('study_program', array('id' => $s_portal_study_program_id))[0];
        if ($mbo_portal_study_program_data) {
            $mbo_staging_study_program_data = $this->Sm->retrieve_data('ref_study_program', array('study_program_name' => $mbo_portal_study_program_data->name))[0];
            if ($mbo_staging_study_program_data) {
                return $mbo_staging_study_program_data->study_program_id;
            }else{
                print('Studi program "'.$mbo_portal_study_program_data->name.'" tidak ditemukan di database portal');exit;
            }
        }else {
            print('Study program with id '.$s_portal_study_program_id.' in portal mdb not found');exit;
        }
    }

    public function get_semester_type_data($s_semester_type_name)
    {
        if ($s_semester_type_name == 'TRANSFERCREDIT') {
            $s_semester_type_name = 'TRCR';
        }else if ($s_semester_type_name == '') {
            $s_semester_type_name = 'ODD-OFSE';
        }else if ($s_semester_type_name == 'OFSE') {
            $s_semester_type_name = 'ODD-OFSE';
        }

        $mbo_staging_semester_data = $this->Sm->retrieve_data('ref_semester_type', array('semester_type_name' => $s_semester_type_name))[0];
        if ($mbo_staging_semester_data) {
            return $mbo_staging_semester_data->semester_type_id;
        }else{
            print('error get semester type in staging<br>name:'.$s_semester_type_name);exit;
        }
    }

    public function get_staging_lecturer_employee_data($s_portal_lecturer_id)
    {
        $mbo_portal_lecturer_data = $this->Pm->retrieve_data('lecturer', array('id' => $s_portal_lecturer_id))[0];
        if ($mbo_portal_lecturer_data) {
            $mbo_portal_personal_data = $this->Pm->retrieve_data('personal_data', array('id' => $mbo_portal_lecturer_data->personal_data_id))[0];
            if ($mbo_portal_personal_data) {
                $mbo_staging_personal_data = $this->Sm->retrieve_data('dt_personal_data', array('portal_id' => $mbo_portal_personal_data->id))[0];
                if ($mbo_staging_personal_data) {
                    $mbo_staging_employee_data = $this->Sm->retrieve_data('dt_employee', array('personal_data_id' => $mbo_staging_personal_data->personal_data_id))[0];
                    if ($mbo_staging_employee_data) {
                        return $mbo_staging_employee_data->employee_id;
                    }else{
                        print('Employee not found in staging with portal id='.$s_portal_lecturer_id);exit;
                    }
                }else{
                    print('portal personal data id '.$mbo_portal_personal_data->id.' not syncronized');exit;
                }
            }else{
                print('lecturer id '.$mbo_portal_lecturer_data->personal_data_id.' dont have personal data in mdb');exit;
            }
        }else{
            print('Parameter lecturer id '.$s_portal_lecturer_id.' not found');exit;
        }
    }

    public function parsing_semester($s_academic_year_id, $s_semester_id)
    {
        $a_normal_semester_id = array(1,2,3,4,5,6,7,8,9,10);
        $a_between_semester_id = array(11,12,13,14,15,16,19,21,22);
        $a_between_odd_semester_id = array(12,13,15,19,22);
        $a_between_even_semester_id = array(11,14,16,21);
        $a_ofse_semester_id = array(17, 20);
        $semester_id = intval($s_semester_id);
        if (in_array($semester_id, $a_normal_semester_id)) {
            $i_academic_year_id = (int)$s_academic_year_id;
            $i_semester_id = intval($s_semester_id) / 2;

            if (intval($s_semester_id) % 2 == 0) {
                $academic_year_id = $i_academic_year_id + ($i_semester_id - 1);
                $s_semester_type_id = 2;
            }else{
                $academic_year_id = $i_academic_year_id + (int)$i_semester_id;
                $s_semester_type_id = 1;
            }
            
            $result = array('academic_year_id' => $academic_year_id, 'semester_type_id' => $s_semester_type_id);
        }else if (in_array($s_semester_id, $a_between_semester_id)) {
            $mba_semester_data = $this->Sm->retrieve_data('ref_semester', array('semester_id' => $s_semester_id))[0];
            $s_semester_type = $mba_semester_data->semester_type_id;
            $i_semester_number = intval($mba_semester_data->semester_number);
            if (($i_semester_number == 1 ) OR ($i_semester_number == 2)) {
                $result = array('academic_year_id' => $s_academic_year_id);
            }else if (($i_semester_number == 3 ) OR ($i_semester_number == 4)) {
                $result = array('academic_year_id' => intval($s_academic_year_id) + 1);
            }else if (($i_semester_number == 5 ) OR ($i_semester_number == 6)) {
                $result = array('academic_year_id' => intval($s_academic_year_id) + 2);
            }else if (($i_semester_number == 7 ) OR ($i_semester_number == 8)) {
                $result = array('academic_year_id' => intval($s_academic_year_id) + 3);
            }else if(($i_semester_number == 9 ) OR ($i_semester_number == 10)){
                $result = array('academic_year_id' => intval($s_academic_year_id) + 4);
            }
            if (in_array($s_semester_id, $a_between_odd_semester_id)) {
                $result['semester_type_id'] = 7;
            }else {
                $result['semester_type_id'] = 8;
            }
        }else if (in_array($s_semester_id, $a_ofse_semester_id)) {
            $result = array('academic_year_id' => 2018, 'semester_type_id' => 4);
        }else if ($s_semester_id == 18) {
            $result = array('academic_year_id' => $s_academic_year_id, 'semester_type_id' => 5);
        }else{
            print('semester_id error: '.$s_semester_id);exit;
        }
        // print_r($result);
        return $result;
    }

    public function bear_brand($s_academic_year_id)
    {
        $this->load->model('employee/Employee_model', 'Emm');
        $a_class_group_fix = array();
        $mba_score_data = $this->Scm->get_score_data(array(
            'sc.academic_year_id' => $s_academic_year_id,
            'sc.semester_type_id <=' => '1',
            'sc.score_approval' => 'approved'
        ));
        if ($mba_score_data) {
            $s_file = 'list_dosen_kelas.csv';
            $s_path = APPPATH.'/uploads/templates/class_groups/'.$s_file;
            $fp = fopen($s_path, 'w+');

            fputcsv($fp, array(
                'Lecturer',
                'Subject',
                'Study Program',
                'Semester'
            ), ';');

            foreach ($mba_score_data as $score) {
                if (!is_null($score->class_group_id)) {
                    $mbo_student_data = $this->Stm->get_student_by_id($score->student_id);
                    // $s_key = $score->class_group_id.$mbo_student_data->study_program_abbreviation;
                    $s_class_id = $score->class_group_id;
                    $mba_class_lecturer = $this->Sm->retrieve_data('dt_class_group_lecturer', array(
                        'class_group_id' => $s_class_id
                    ));
                    if ($mba_class_lecturer) {
                        // if (!in_array($s_key, $a_class_group_fix)) {
                            $a_employee = array();
                            foreach ($mba_class_lecturer as $lect) {
                                $mbo_employee_data = $this->Emm->get_employee_data(array('employee_id' => $lect->employee_id))[0];
                                $s_key = $score->class_group_id.$mbo_student_data->study_program_abbreviation.$mbo_employee_data->personal_data_id;
                                if (!in_array($s_key, $a_class_group_fix)) {
                                    // array_push($a_employee, $mbo_employee_data->personal_data_title_prefix.' '.$mbo_employee_data->personal_data_name.' '.$mbo_employee_data->personal_data_title_suffix);
                                    fputcsv($fp, array(
                                        $mbo_employee_data->personal_data_title_prefix.' '.$mbo_employee_data->personal_data_name.' '.$mbo_employee_data->personal_data_title_suffix,
                                        $score->subject_name,
                                        $mbo_student_data->study_program_name,
                                        $score->academic_year_id.$score->semester_type_id
                                    ), ';');
        
                                    array_push($a_class_group_fix, $s_key);
                                }
                            }

                            // fputcsv($fp, array(
                            //     $score->subject_name,
                            //     $mbo_student_data->study_program_name,
                            //     $score->academic_year_id.$score->semester_type_id,
                            //     implode(',', $a_employee)
                            // ), ';');

                            // array_push($a_class_group_fix, $s_key);

                            // var_dump($data);
                            // print('<br>');
                        // }
                    }
                }
            }

            $a_path_info = pathinfo($s_path);
            header('Content-Disposition: attachment; filename='.urlencode($s_file));
            readfile( $s_path );
        }
    }

    public function repair_semester_id()
    {
        // $mba_portal_score_data = $this->Pm->retrieve_data('score_absence');
        $mba_new_score_data = $this->Sm->retrieve_data('dt_score', array('portal_id >' => 0));
        print('<pre>');
        $i = 0;
        if ($mba_new_score_data) {
            foreach ($mba_new_score_data as $o_new_score) {
                $mba_portal_score_data = $this->Pm->retrieve_data('score_absence', array('id' => $o_new_score->portal_id));
                if ($mba_portal_score_data) {
                    if ($o_new_score->semester_id != $mba_portal_score_data[0]->semester_id) {
                        $i++;
                        var_dump($o_new_score->score_id);
                        // $this->Scm->save_data(array('semester_id' => $mba_portal_score_data[0]->semester_id), array('score_id' => $o_new_score->score_id));
                    }
                }
            }
        }

        print($i);
        
        // var_dump($mba_portal_score_data);
    }

    // public function repair_subject_code()
    // {
    //     $mba_subject_data = $this->Sm->retrieve_data('ref_subject', array('subject_code' => ''));
    //     if ($mba_subject_data) {
    //         foreach ($mba_subject_data as $subject) {
    //             // generate_subject_code($s_subject_name_code, $s_prodi_id, $sks)
    //             $mba_subject_name_data = $this->Sm->retrieve_data('ref_subject_name', array('subject_name_id' => $subject->subject_name_id))[0];
    //             $s_subject_code = modules::run('academic/subject', $mba_subject_name_data->subject_name_code, $subject->study_program_id, $subject->subject_credit);
    //             $a_subject_update = array(
    //                 'subject_code' => $s_subject_code
    //             );

    //             $this->Sbm->save_subject_data($a_subject_update, $subject->subject_id);
    //             print(' .');
    //         }
    //     }
    // }

    public function bear_brand_custom($s_academic_year_id)
    {
        $mba_class_subject = $this->Cgm->get_class_by_offered_subject(array(
            'ofs.academic_year_id' => $s_academic_year_id,
            'ofs.semester_type_id' => '2'
        ));
        if ($mba_class_subject) {
            $a_key_fixed = array();
            $s_file = 'list_dosen_kelas.csv';
            $s_path = APPPATH.'/uploads/templates/class_groups/'.$s_file;
            $fp = fopen($s_path, 'w+');

            fputcsv($fp, array(
                'Dosen',
                'NIDN',
                'Kode Matkul',
                'Mata Kuliah',
                'SKS',
                'Studi Program',
                'Semester'
            ), ';');

            foreach ($mba_class_subject as $subject) {
                $mba_class_lecturer = $this->Cgm->get_class_group_lecturer(array('class_group_id' => $subject->class_group_id));
                $mbo_study_program_data = $this->Sm->retrieve_data('ref_study_program', array('study_program_id' => $subject->study_program_id))[0];
                if ($mba_class_lecturer) {
                    foreach ($mba_class_lecturer as $lecturer) {
                        $s_key = $lecturer->employee_id.$subject->subject_id.$mbo_study_program_data->study_program_id.$subject->academic_year_id.$subject->semester_type_id;
                        if (!in_array($s_key,  $a_key_fixed)) {
                            fputcsv($fp, array(
                                $lecturer->personal_data_title_prefix.' '.$lecturer->personal_data_name.' '.$lecturer->personal_data_title_suffix,
                                $lecturer->employee_lecturer_number,
                                $subject->subject_code,
                                $subject->subject_name,
                                $subject->curriculum_subject_credit,
                                $mbo_study_program_data->study_program_name,
                                $subject->academic_year_id.$subject->semester_type_id
                            ), ';');
    
                            array_push($a_key_fixed, $s_key);
                        }
                    }
                }
            }

            $a_path_info = pathinfo($s_path);
            header('Content-Disposition: attachment; filename='.urlencode($s_file));
            readfile( $s_path );
        }else{
            print('kosong');
        }
    }

    public function generate_list_list_lecturer($s_academic_year_id, $s_semester_type_id)
    {
        $this->load->model('employee/Employee_model', 'Emm');
        $a_key = array();
        $mba_class_data = $this->Cgm->get_class_groups(array('academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id));
        if ($mba_class_data) {
            // print('<pre>');
            $s_file = 'list_dosen_kelas.csv';
            $s_path = APPPATH.'/uploads/templates/class_groups/'.$s_file;
            $fp = fopen($s_path, 'w+');

            fputcsv($fp, array(
                'Dosen',
                'NIDN',
                'Kode Matkul',
                'Mata Kuliah',
                'SKS',
                'Studi Program',
                'Semester'
            ), ';');

            foreach ($mba_class_data as $class) {
                $mba_score_data = $this->Scm->get_score_data(array('class_group_id' => $class->class_group_id));
                if ($mba_score_data) {
                    $mbo_class_subject = $this->Cgm->get_class_group_subject($class->class_group_id)[0];
                    if ($mbo_class_subject) {
                        $mba_lecturer_list = $this->Cgm->get_class_group_lecturer(array('cgl.class_group_id' => $class->class_group_id));
                        if ($mba_lecturer_list) {
                            foreach ($mba_lecturer_list as $lect) {
                                $mbo_lecturer_data = $this->Emm->get_employee_data(array('employee_id' => $lect->employee_id))[0];
                                $s_key = $lect->employee_id.$mbo_class_subject->subject_name.$mbo_class_subject->curriculum_subject_credit;
                                if (!in_array($s_key, $a_key)) {
                                    // $a_list = array(
                                    //     'Dosen' => $mbo_lecturer_data->personal_data_name,
                                    //     'NIDN' => $mbo_lecturer_data->employee_lecturer_number,
                                    //     'Kode Matkul' => $mbo_class_subject->subject_code,
                                    //     'Mata Kuliah' => $mbo_class_subject->subject_name,
                                    //     'SKS' => $mbo_class_subject->curriculum_subject_credit,
                                    //     'Studi Program' => $mbo_class_subject->study_program_name,
                                    //     'Semester' => $s_academic_year_id.'/'.$s_semester_type_id
                                    // );
                                    // var_dump($a_list);
                                    fputcsv($fp, array(
                                        $mbo_lecturer_data->personal_data_title_prefix.' '.$mbo_lecturer_data->personal_data_name.' '.$mbo_lecturer_data->personal_data_title_suffix,
                                        $mbo_lecturer_data->employee_lecturer_number,
                                        $mbo_class_subject->subject_code,
                                        $mbo_class_subject->subject_name,
                                        $mbo_class_subject->curriculum_subject_credit,
                                        $mbo_class_subject->study_program_name,
                                        $s_academic_year_id.'/'.$s_semester_type_id
                                    ), ';');
                                }
                            }
                        }
                    }
                }
            }

            $a_path_info = pathinfo($s_path);
            header('Content-Disposition: attachment; filename='.urlencode($s_file));
            readfile( $s_path );
        }
    }
}