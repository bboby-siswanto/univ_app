<?php
class Offered_subject extends App_core
{
    function __construct()
    {
        parent::__construct('academic');
        $this->load->model('Offered_subject_model','Osm');
        $this->load->model('academic/Ofse_model', 'Ofm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('academic/Class_group_model', 'Cgm');
        $this->load->model('academic/Semester_model', 'Sm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('academic/Academic_year_model', 'Aym');
        $this->load->model('academic/Curriculum_model','Cm');
    }

    public function offered_subject_lists()
    {
        $mbo_active_admission_academic_year = $this->Aym->get_academic_year_lists(array('academic_year_intake_status' => 'active'))[0];
        $this->a_page_data['semester_type_lists'] = $this->Sm->get_semester_type_lists(false, false, array(1,2,7,8));
        $this->a_page_data['program_lists'] = $this->Spm->get_program_lists_select(array('program_main_id' => NULL));
        $this->a_page_data['academic_year_lists'] = $this->Aym->get_academic_year_lists();
        $this->a_page_data['semester_data'] = $this->Cm->get_semester_list(false, true);
        $this->a_page_data['curriculum_list'] = $this->Cm->get_curriculum_filtered(array('valid_academic_year' => $mbo_active_admission_academic_year->academic_year_id));
        $this->a_page_data['subject_type_enums'] = $this->General->get_enum_values('ref_curriculum_subject', 'curriculum_subject_type');

        // $this->a_page_data['body'] = $this->load->view('offered_subject/Offered_subject_lists', $this->a_page_data, true);
        // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
            $this->a_page_data['body'] = $this->load->view('offered_subject/offered_subject_regular', $this->a_page_data, true);
        // }
        $this->load->view('layout', $this->a_page_data);
    }

    public function get_subject_code()
    {
        if ($this->input->is_ajax_request()) {
            $s_subject_name = $this->input->post('subject_name');
            $s_study_program_id = $this->input->post('study_program_id');
            $s_subject_credit = $this->input->post('subject_credit');

            $s_subject_name_code = modules::run('academic/subject/generate_subject_name_code', $s_subject_name);
            $s_subject_code = modules::run('academic/subject/generate_subject_code', $s_subject_name_code, $s_study_program_id, $s_subject_credit);

            print json_encode(['code' => $s_subject_code]);
        }
    }

    public function form_filter_offered_subject()
    {
        $this->a_page_data['o_semester_type_lists'] = $this->Sm->get_semester_type_lists(false, false, array(1,2,7,8));
        // $this->a_page_data['o_semester_type_lists'] = $this->Sm->get_semester_type_lists();
        $this->a_page_data['o_program_lists'] = $this->Spm->get_program_lists_select(array('program_main_id' => NULL));
        $this->a_page_data['o_academic_year_lists'] = $this->Aym->get_academic_year_lists();

        $this->load->view('offered_subject/form/form_filter_offered_subject', $this->a_page_data);
    }

    public function view_lecturer_lists()
    {
        $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'offer_subject_lecturer');
        $this->a_page_data['btn_lecturer'] = $s_btn_html;
        $this->load->view('table/lists_lecturer', $this->a_page_data);
    }

    public function validate_key()
    {
        if ($this->input->is_ajax_request()) {
            $key = $this->input->post('key');
            $key = base64_decode($key);
            $this->load->model('employee/Employee_model', 'Emm');
            $mbo_employee_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $this->session->userdata('user')))[0];
            if ($mbo_employee_data) {
                $this->load->library('IULI_Ldap');
                $mba_ldap_login = $this->iuli_ldap->ldap_login($mbo_employee_data->employee_email, $key);
                if ($mba_ldap_login['code'] == 0) {
                    $a_return = array('code' => 0, 'message' => 'Success');
                }else{
                    $a_return = array('code' => 1, 'message' => 'Wrong Password');
                }
            }else{
                $a_return = array('code' => 1, 'message' => 'Account not found!');
            }

            print json_encode($a_return);
        }
    }

    public function get_lecturer_lists()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('employee/Employee_model', 'Emm');
            $s_offered_subject_id = $this->input->post('offered_subject_id');
            $mbo_lecturer_lists = $this->Osm->get_offer_subject_lecturer($s_offered_subject_id);
            $mbo_subject_data = $this->Osm->get_offered_subject_subject(array('os.offered_subject_id' => $s_offered_subject_id));
            if ($mbo_lecturer_lists AND $mbo_subject_data) {
                $mbo_subject_data = $mbo_subject_data[0];
                $credit_filled = 0;
                foreach ($mbo_lecturer_lists as $value) {
                    $credit_filled += $value->credit_allocation;
                }
                $mbo_subject_data->credit_filled = $credit_filled;
                foreach ($mbo_lecturer_lists as $key => $class_lecturer) {
                    $mbo_lecturer_lists[$key]->subject_data = $mbo_subject_data;
                    if (!is_null($class_lecturer->employee_id_reported)) {
                        $mbo_employee_reported = $this->Emm->get_employee_data(array('em.employee_id' => $class_lecturer->employee_id_reported))[0];
                        $mbo_lecturer_lists[$key]->employee_data_reported = $mbo_employee_reported;
                    }else{
                        $mbo_lecturer_lists[$key]->employee_data_reported = false;
                    }
                }
            }

            print json_encode(array('code' => 0, 'data' => $mbo_lecturer_lists));
            exit;
        }
    }

    public function view_table_offered_subject()
    {
        $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'offered_subject');
        $this->a_page_data['btn_html'] = $s_btn_html;
        $this->load->view('offered_subject/table/offered_subject_lists_table', $this->a_page_data);
    }

    public function form_input_lecturer()
    {
        $this->a_page_data['preferable_day'] = $this->General->get_enum_values('dt_class_group_lecturer', 'class_group_lecturer_preferable_day');
        $this->a_page_data['preferable_time'] = $this->General->get_enum_values('dt_class_group_lecturer', 'class_group_lecturer_preferable_time');
        $this->a_page_data['lecturer_priority'] = $this->General->get_enum_values('dt_class_group_lecturer', 'class_group_lecturer_priority');
        $this->load->view('form/form_input_lecturer', $this->a_page_data);
    }

    public function form_input_examiner()
    {
        $this->load->view('form/form_input_examiner', $this->a_page_data);
    }

    public function submit_new_subject()
    {
        if ($this->input->is_ajax_request()) {
            // $a_return = ['code' => 1, 'message' => 'sedang dalam pengembangan!'];
            // print json_encode($a_return);exit;

            $this->form_validation->set_rules('osns_subject_name', 'Subject Name', 'required|trim');
            $this->form_validation->set_rules('osns_semester_id', 'Semester', 'required|trim');
            $this->form_validation->set_rules('osns_cur_subject_type', 'Subject Type', 'required');
            $this->form_validation->set_rules('osns_subject_credit', 'Subject Credit', 'required|trim');
            $this->form_validation->set_rules('osns_subject_code', 'Subject Code', 'required|trim');

            if ($this->form_validation->run()) {
                $a_return = $this->Osm->submit_subject_data($this->input->post());
            }
            else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);exit;
        }
    }

    public function separate_class($s_class_group_id)
    {
        $mba_class_master = $this->Cgm->get_class_master_group(array('class_group_id' => $s_class_group_id))[0];
        if ($mba_class_master) {
            $mba_class_master_subject = $this->Cgm->get_class_master_study_program($mba_class_master->class_master_id);
            $a_class_study_program = array();
            
            if ($mba_class_master_subject) {
                foreach ($mba_class_master_subject as $o_class_study_program) {
                    if (!in_array($o_class_study_program->study_program_abbreviation, $a_class_study_program)) {
                        array_push($a_class_study_program, $o_class_study_program->study_program_abbreviation);
                    }
                }
            }

            $a_class_group_have_master = array();
            $a_class_master_created = array();
            $s_subject_name = '';

            $this->db->trans_start();

            $mba_class_master_group = $this->Cgm->get_class_master_group(array('cmc.class_master_id' => $mba_class_master->class_master_id));
            $mbo_class_master_data = $this->Cgm->get_class_master_data($mba_class_master->class_master_id)[0];

            if (count($mba_class_master_group) > 1) {
                $mba_class_subject_delivered = $this->Cgm->get_class_subject_delivered(array('class_master_id' => $mba_class_master->class_master_id));

                foreach ($mba_class_master_group as $o_class_groups) {
                    $s_class_master_id = $this->uuid->v4();
                    $mba_class_group_details = $this->Cgm->get_class_group_lists(array('class_group_id' => $o_class_groups->class_group_id))[0];
                    $mba_class_group_subject = $this->Cgm->get_class_group_subject($o_class_groups->class_group_id)[0];
                    if (!$mba_class_group_subject) {
                        $push_class_subject = modules::run('academic/class_group/push_class_group_subject', $s_class_group_id);
                        if ($push_class_subject) {
                            $mba_class_group_details = $this->Cgm->get_class_group_subject($o_class_groups->class_group_id)[0];
                        }else{
                            $a_return = array('code' => 1, 'message' => 'Error filling class subject- class_group_id:'.$s_class_group_id);
                            return $a_return;
                            exit;
                        }
                    }

                    if ($s_subject_name == '') {
                        $s_subject_name = $mba_class_group_details->subject_name;
                    }

                    if (!in_array($o_class_groups->class_group_id, $a_class_group_have_master)) {
                        if (($s_subject_name == '') AND (is_null($s_subject_name))) {
                            $a_return = array('code' => 1, 'message' => 'Subject name not found');
                            print json_encode($a_return);
                            exit;
                        }
                        $a_class_master_data = array(
                            'class_master_id' => $s_class_master_id,
                            'academic_year_id' => $mbo_class_master_data->academic_year_id,
                            'semester_type_id' => $mbo_class_master_data->semester_type_id,
                            'class_master_name' => $s_subject_name,
                            'date_added' => date('Y-m-d H:i:s')
                        );
                        if (!in_array($s_class_master_id, $a_class_master_created)) {
                            array_push($a_class_master_created, $s_class_master_id);
                        }
                        # save_class_master
                        $save_class_master = $this->Cgm->save_class_mastering($a_class_master_data);

                        if ($save_class_master) {
                            $mba_class_group_lecturer = $this->Cgm->get_class_group_lecturer(array('class_group_id' => $o_class_groups->class_group_id));
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

                                    # save class master lecurer
                                    $this->Cgm->save_class_master_lect_data($a_class_master_lecturer);
                                }
                            }

                            if ($mba_class_subject_delivered) {
                                foreach ($mba_class_subject_delivered as $o_subject_delivered) {
                                    $s_subject_delivered_id = $this->uuid->v4();
                                    $a_subject_delivered_data = array(
                                        'subject_delivered_id' => $s_subject_delivered_id,
                                        'class_master_id' => $s_class_master_id,
                                        'class_group_id' => $o_class_groups->class_group_id,
                                        'employee_id' => $o_subject_delivered->employee_id,
                                        'subject_delivered_time_start' => $o_subject_delivered->subject_delivered_time_start,
                                        'subject_delivered_time_end' => $o_subject_delivered->subject_delivered_time_end,
                                        'subject_delivered_description' => $o_subject_delivered->subject_delivered_description,
                                        'number_of_meeting' => $o_subject_delivered->number_of_meeting,
                                        'date_added' => $o_subject_delivered->date_added,
                                        'portal_id' => $o_subject_delivered->portal_id
                                    );

                                    $mba_class_group_subject_delivered = $this->Cgm->get_class_subject_delivered(array(
                                        'class_group_id' => $o_class_groups->class_group_id,
                                        'employee_id' => $o_subject_delivered->employee_id,
                                        'subject_delivered_time_start' => $o_subject_delivered->subject_delivered_time_start
                                    ));

                                    if ($mba_class_group_subject_delivered) {
                                        # update class_master_id
                                        $save_subject_delivered = $this->Cgm->save_subject_delivered($a_subject_delivered_data, $mba_class_group_subject_delivered[0]->subject_delivered_id);
                                    }else{
                                        # insert class_master_id
                                        $save_subject_delivered = $this->Cgm->save_subject_delivered($a_subject_delivered_data);
                                    }
                                }
                            }

                            $a_class_master_update = array(
                                'class_master_id' => $s_class_master_id
                            );

                            # update class_master_id dari dt_class_master_class
                            $this->Cgm->save_class_master_class($a_class_master_update, array(
                                'class_group_id' => $o_class_groups->class_group_id
                            ));

                            # update class_master_id dari dt_score berdasarkan class_group_id
                            $this->Scm->save_data($a_class_master_update, array(
                                'class_group_id' => $o_class_groups->class_group_id
                            ));
                            
                        }
                        array_push($a_class_group_have_master, $o_class_groups->class_group_id);
                    }
                }
            }
            
            $s_study_program_list = implode('/', $a_class_study_program);
            $i_count_class_created = count($a_class_group_have_master);
            $s_text = "Notification, <br>
                Mr/Mrs {$this->session->userdata('name')} has changed the offered subject in the subject {$s_subject_name}  in academic year {$mbo_class_master_data->academic_year_id} semester {$mbo_class_master_data->semester_type_id}<br>
                and the {$s_subject_name} class with study program {$s_study_program_list} has been separated into {$i_count_class_created} classes ";
            
            $this->config->load('portal_config_'.$this->s_environment);
            $a_academic_mail = $this->config->item('email');
            $s_mail_to = ($this->s_environment == 'production') ? $a_academic_mail['academic']['head'] : 'employee@company.ac.id';

            modules::run('messaging/send_email', 
                $s_mail_to,
                '[ACADEMIC SERVICE] Changed the Offered Subject',
                $s_text,
                $a_academic_mail['academic']['main'],
                array('employee@company.ac.id')
            );

            if ($this->db->trans_status() == FALSE) {
                $this->db->trans_rollback();
                $a_return = array('code' => 1, 'message' => 'Error separate class!');
            }else{
                $this->db->trans_commit();
                $a_return = array('code' => 0, 'message' => 'Success!');
            }
        }else{
            $a_return = array('code' => 1, 'message' => 'Class master not found!');
        }

        return $a_return;
    }
    
    public function initiate_ofse($s_academic_year_id, $s_semester_type_id)
    {
	    print "<pre>";
	    $s_previous_academic_year_id = $s_academic_year_id;
	    if($s_semester_type_id == 4){
		    $s_previous_academic_year_id -= 1;
		    $s_previous_semester_id = 6;
	    }
	    else{
		    $s_previous_semester_id = 4;
	    }
	    $a_clause = [
		    'academic_year_id' => $s_previous_academic_year_id,
		    'semester_type_id' => $s_previous_semester_id
	    ];
	    $mbo_offer_subject_lists = $this->Osm->get_offered_subject_filtered($a_clause);
	    
	    $a_clause_new = [
		    'academic_year_id' => $s_academic_year_id,
		    'semester_type_id' => $s_semester_type_id
	    ];
	    $mbo_offer_subject_lists_new = $this->Osm->get_offered_subject_filtered($a_clause_new);
	    
	    if($mbo_offer_subject_lists AND !$mbo_offer_subject_lists_new){
		    foreach($mbo_offer_subject_lists as $offer_subject){
			    $s_old_offered_subject_id = $offer_subject->offered_subject_id;
			    $s_offered_subject_id = $this->uuid->v4();
			    $a_offered_subject_data = [
				    'offered_subject_id' => $s_offered_subject_id,
					'curriculum_subject_id' => $offer_subject->curriculum_subject_id,
					'academic_year_id' => $s_academic_year_id,
					'semester_type_id' => $s_semester_type_id,
					'program_id' => 1,
					'study_program_id' => $offer_subject->study_program_id
			    ];
			    print "Offered subject data:\n";
			    print_r($a_offered_subject_data);
			    print "\n\n";
			    
			    $o_offered_subject_data = $this->Osm->get_offered_subject_filtered($a_offered_subject_data);
			    
			    if(!$o_offered_subject_data){
				    $this->Osm->save_offer_subject($a_offered_subject_data);
				    
				    $s_class_group_id = $this->uuid->v4();
				    $mbo_prodi_data = $this->Spm->get_study_program($offer_subject->study_program_id, false)[0];
                    $s_class_name = $o_offered_subject_data[0]->subject_name.' '.$mbo_prodi_data->study_program_abbreviation.' '.$s_academic_year_id.$s_semester_type_id.' OFSE';
					$a_class_group_data = [
						'class_group_id' => $s_class_group_id,
						'academic_year_id' => $s_academic_year_id,
						'semester_type_id' => $s_semester_type_id,
						'class_group_name' => $s_class_name
                    ];
                    print "Class group data:\n";
                    print_r($a_class_group_data);
                    print "\n\n";
                    
					if($this->Cgm->save_data($a_class_group_data)){
						$a_class_group_subject_data = [
	                        'class_group_subject_id' => $this->uuid->v4(),
	                        'class_group_id' => $s_class_group_id,
	                        'offered_subject_id' => $s_offered_subject_id
	                    ];
	                    print "Class group subject data:\n";
	                    print_r($a_class_group_subject_data);
	                    print "\n\n";
						$this->Cgm->save_class_group_subject($a_class_group_subject_data);
						$mba_old_lect_subject = $this->Osm->get_offer_subject_lecturer($s_old_offered_subject_id);
						if($mba_old_lect_subject){
							foreach($mba_old_lect_subject as $lect_subject){
								$a_class_group_lecturer_data = array(
		                            'class_group_lecturer_id' => $this->uuid->v4(),
		                            'class_group_id' => $s_class_group_id,
		                            'employee_id' => $lect_subject->employee_id
		                        );
		                        print "Class group lecturer data:\n";
								print_r($a_class_group_lecturer_data);
								print "\n\n";
								$this->Cgm->save_class_group_lecturer($a_class_group_lecturer_data);
							}
						}
					}	
			    }
			    print "Finish loop\n\n\n";
            }
	    }
	    else{
		    print "Nothing to do\n\n";
	    }
    }

    public function filter_curriculum_offered_subject_lists()
    {
        if ($this->input->is_ajax_request()) {
            $a_term = $this->input->post('term');

            $program_id = $a_term['program_id'];
            $study_program_id = $a_term['study_program_id'];
            if ((array_key_exists('same_prodi', $a_term)) AND ($a_term['same_prodi'] == 'true')) {
                $program_id = $a_term['os_program_id'];
                $study_program_id = $a_term['os_study_program_id'];
            }

            // print('<pre>');var_dump($a_term);exit;
            $a_clause = [
                'cs.curriculum_subject_category' => 'regular semester',
                'cr.program_id' => $program_id,
                'cs.curriculum_subject_credit > ' => '0',
            ];

            $a_study_program_id = [$study_program_id];
            if (!empty($study_program_id)) {
                $mba_main_study_program = $this->Spm->get_study_program($study_program_id, false);
                $s_study_program_main_id = (($mba_main_study_program) AND (!is_null($mba_main_study_program[0]->study_program_main_id))) ? $mba_main_study_program[0]->study_program_main_id : null;
                if (!is_null($s_study_program_main_id)) {
                    if (!in_array($s_study_program_main_id, $a_study_program_id)) {
                        array_push($a_study_program_id, $s_study_program_main_id);
                    }
                }
            }

            $mba_curriculum_subject_list = $this->Osm->get_offered_subject_curriculum($a_clause, $a_study_program_id);
            print json_encode(['data' => $mba_curriculum_subject_list]);
        }
    }

    public function filter_offered_subject_lists()
    {
        if ($this->input->is_ajax_request()) {
            $a_term = $this->input->post('term');
            
            if(in_array($a_term['semester_type_id'], [4,6])){
	            // $this->initiate_ofse($a_term['academic_year_id'], $a_term['semester_type_id']);
            }
            // $a_term['academic_year_id']
			// $a_term['semester_type_id']
            
            $a_selected_score = false;
            if((isset($a_term['student_id'])) AND ($a_term['student_id'] != 'null')){
	            $mba_score_student = $this->Scm->get_score_student($a_term['student_id'], array(
					'sc.academic_year_id' => $a_term['academic_year_id'],
					'sc.semester_type_id' => $a_term['semester_type_id']
				));
				
				$a_selected_score = array();
				if($mba_score_student){
					foreach($mba_score_student as $score){
/*
						array_push($a_selected_score, array(
							'subject_id' => $score->curriculum_subject_id,
							'approval' => $score->score_approval
						));
*/
						array_push($a_selected_score, $score->curriculum_subject_id);
					}
				}
				unset($a_term['student_id']);
            }
            
            $i_counter = count($a_term);
            $a_clause = array();
            
            $s_study_program_main_id = null;
            
            foreach ($a_term as $key => $value) {
	            if($key == 'study_program_id'){
		            $mba_main_study_program = $this->Spm->get_study_program($value, false);
		            $s_study_program_main_id = ($mba_main_study_program) ? $mba_main_study_program[0]->study_program_main_id : null;
		            $s_study_program_id = $value;
	            }
	            
                $s_key = 'dos.'.$key;
                $a_clause[$s_key] = $value;
                if (($value == '') OR (is_null($value))) {
                    // var_dump($s_key.'-'.$value);
                    $i_counter--;
                }
            }
            // var_dump($a_clause);exit;

            if ($i_counter != count($a_term)) {
                $mbo_offer_subject_lists = false;
            }else{
                if (in_array($a_term['semester_type_id'], [1, 2, 7, 8])) {
                    $a_clause['curriculum_subject_category'] = 'regular semester';
                }else if (in_array($a_term['semester_type_id'], [4,6])) {
                    $a_clause['curriculum_subject_category'] = 'ofse';
                }
                
                $mbo_offer_subject_lists = $this->Osm->get_offered_subject_lists_filtered($a_clause);
                if ($mbo_offer_subject_lists) {
                    foreach ($mbo_offer_subject_lists as $offer_subject) {
                        $mbo_lect_subject = $this->Osm->get_offer_subject_lecturer($offer_subject->offered_subject_id);
                        $a_lect_data = array();
                        $a_lect_subject = array();
                        $i_sks_count = 0;
                        if ($mbo_lect_subject) {
                            foreach ($mbo_lect_subject as $lect_subject) {
                                $s_lecturer_name = $this->Pdm->retrieve_title($lect_subject->personal_data_id);
                                // $s_lecturer_name = (!is_null($lect_subject->personal_data_title_prefix) ? $lect_subject->personal_data_title_prefix : '').' '.
                                //             $lect_subject->personal_data_name.' '.(!is_null($lect_subject->personal_data_title_suffix) ? $lect_subject->personal_data_title_suffix : '');
                                $s_lect_class = $s_lecturer_name.' ('.$lect_subject->credit_allocation.')';
                                $i_sks_count += $lect_subject->credit_allocation;
                                array_push($a_lect_subject, $s_lect_class);
                                array_push($a_lect_data, $s_lecturer_name);
                            }
                        }
                        $s_lect_data = (count($a_lect_subject) > 0) ? implode(' | ', $a_lect_subject) : '';
                        $offer_subject->sks_count_total = $i_sks_count;
                        $offer_subject->lecturer_subject = $s_lect_data;
                        $offer_subject->lecturer_data = $a_lect_data;
                        $offer_subject->deleteable = true;
                        $offer_subject->selected = false;
                        if(($a_selected_score) AND (in_array($offer_subject->curriculum_subject_id, $a_selected_score))){
	                        $offer_subject->selected = true;
                        }
                    }
                }
                // var_dump($mbo_offer_subject_lists);exit;
                
                if(!is_null($s_study_program_main_id)){
	                $a_clause['dos.study_program_id'] = $s_study_program_main_id;
                    $mbo_offer_subject_lists_two = $this->Osm->get_offered_subject_lists_filtered($a_clause);  
                    // var_dump($a_clause);exit;
	                
	                if ($mbo_offer_subject_lists_two) {
	                    foreach ($mbo_offer_subject_lists_two as $key => $offer_subject) {
	                        $mbo_lect_subject = $this->Osm->get_offer_subject_lecturer($offer_subject->offered_subject_id);
	                        $a_lect_data = array();
	                        $i_sks_count = 0;
	                        if ($mbo_lect_subject) {
	                            foreach ($mbo_lect_subject as $lect_subject) {
                                    $s_lecturer_name = $this->Pdm->retrieve_title($lect_subject->personal_data_id);
	                                // $s_lecturer_name = (!is_null($lect_subject->personal_data_title_prefix) ? $lect_subject->personal_data_title_prefix : '').' '.
	                                //             $lect_subject->personal_data_name.' '.(!is_null($lect_subject->personal_data_title_suffix) ? $lect_subject->personal_data_title_suffix : '');
	                                $s_lect_class = $lect_subject->personal_data_name.' ('.$lect_subject->credit_allocation.')';
	                                $i_sks_count += $lect_subject->credit_allocation;
	                                array_push($a_lect_data, $s_lecturer_name);
	                            }
	                        }
	                        $s_lect_data = (count($a_lect_data) > 0) ? implode(', ', $a_lect_data) : '';
	                        $offer_subject->sks_count_total = $i_sks_count;
	                        $offer_subject->lecturer_subject = $s_lect_data;
	                        $offer_subject->lecturer_data = $a_lect_data;
                            $offer_subject->deleteable = false;
                            $offer_subject->selected = false;
                            if(($a_selected_score) AND (in_array($offer_subject->curriculum_subject_id, $a_selected_score))){
                                $offer_subject->selected = true;
                            }
                            
                            $b_unset = false;

                            if ($mbo_offer_subject_lists) {
                                foreach ($mbo_offer_subject_lists as $o_offered_subject) {
                                    if ($offer_subject->subject_name == $o_offered_subject->subject_name) {
                                        $b_unset = true;
                                    }
                                }
                            }

                            if ($b_unset) {
                                unset($mbo_offer_subject_lists_two[$key]);
                            }
	                    }
                    }
                    
                    // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                    //     print(count($mbo_offer_subject_lists));
                    //     print('<br>two:');
                    //     print(count($mbo_offer_subject_lists_two));
                    //     exit;
                    // }
	                
	                if($mbo_offer_subject_lists AND $mbo_offer_subject_lists_two){
		                $mbo_offer_subject_lists = array_merge($mbo_offer_subject_lists, $mbo_offer_subject_lists_two);
	                }
	                else if($mbo_offer_subject_lists_two) {
                        $mbo_offer_subject_lists = $mbo_offer_subject_lists_two;
                    }
                    // else{
		            //     $mbo_offer_subject_lists = $mbo_offer_subject_lists_two;
	                // }
                }
            }

            // $mbo_offer_subject_lists = array_values($mbo_offer_subject_lists);
            
			$a_return = array('code' => 0, 'data' => $mbo_offer_subject_lists, 'approved_subjects' => $a_selected_score);
			print json_encode($a_return);
			exit;
        }
    }

    public function validate_student_offered_subject()
    {
        if ($this->input->is_ajax_request()) {
            $s_offered_subject_id = $this->input->post('offered_subject_id');
            $mbo_offered_subject_data = $this->Osm->get_offer_subject_class_group(array('ofs.offered_subject_id' => $s_offered_subject_id))[0];
            if ($mbo_offered_subject_data) {
                $mba_score_data = $this->Scm->get_score_data(array('sc.class_group_id' => $mbo_offered_subject_data->class_group_id));
                if ($mba_score_data) {
                    $a_return = array('code' => 1, 'message' => 'already taken by student');
                }else{
                    $a_return = array('code' => 0, 'message' => 'OK');
                }
            }else{
                $a_return = array('code' => 0, 'message' => 'OK');
            }

            print json_encode($a_return);
        }
    }
    
    public function save_examiner()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('personal_data_name', 'Examiner', 'required');
            $this->form_validation->set_rules('academic_year_id', 'Running Year', 'required');
            $this->form_validation->set_rules('semester_type_id', 'Semester type', 'required');
            $this->form_validation->set_rules('program_id', 'Program', 'required');
            $this->form_validation->set_rules('study_program_id', 'Study Program', 'required');

            $s_offered_subject_id = $this->input->post('offered_subject_id');
            $s_employee_id = $this->input->post('employee_id');

            $b_checker_semester_academic = modules::run('academic/semester/checker_semester_academic', null, 'offer_subject_ofse_end_date', $this->input->post('academic_year_id'), (($this->input->post('semester_type_id') == 4) ? 1 : 2));
            if (!$b_checker_semester_academic) {
                $a_rtn = array('code' => 1, 'message' => 'Offer subject period is not valid');
                print json_encode($a_rtn);
                exit;
            }

            if ($this->form_validation->run()) {
                if ($this->input->post('class_group_lecturer_id') != '') {
                    $a_rtn = array('code' => 0, 'message' => 'Ini Buat update');
                }else{
                    if ($s_offered_subject_id == '') {
                        $a_rtn = array('code' => 1, 'message' => 'Unable to get offered subject data');
                    }else if ($s_employee_id == '') {
                        $a_rtn = array('code' => 1, 'message' => 'Plaese select lecturer with autocomplete');
                    }else{
                        $this->db->trans_start();
    
                        $o_offered_subjet_data = $this->Osm->get_offered_subject_lists_filtered(array('offered_subject_id' => $s_offered_subject_id))[0];
                        $a_class_checker_data = array(
                            'cg.academic_year_id' => set_value('academic_year_id'),
                            'cg.semester_type_id' => set_value('semester_type_id'),
                            'ofs.program_id' => set_value('program_id'),
                            'ofs.study_program_id' => set_value('study_program_id'),
                            'cgs.offered_subject_id' => $s_offered_subject_id
                        );
    
                        $a_class_group_lecturer_data = array(
                            'class_group_lecturer_id' => $this->uuid->v4(),
                            'employee_id' => $s_employee_id
                        );
    
                        $a_class_lect_data = $this->Cgm->get_class_by_offered_subject($a_class_checker_data);
                        if ($a_class_lect_data) {
                            $s_class_group_id = $a_class_lect_data[0]->class_group_id;
                            $mbo_lect_offered_subject = $this->Cgm->get_class_group_lecturer(array('class_group_id' => $s_class_group_id, 'cgl.employee_id' => $s_employee_id));
                            if ($mbo_lect_offered_subject) {
                                $a_rtn = array('code' => 1, 'message' => 'Lecturer has been ready in this subject');
                                print json_encode($a_rtn);exit;
                            }else {
                                $a_class_group_lecturer_data['class_group_id'] = $s_class_group_id;
                                $this->Cgm->save_class_group_lecturer($a_class_group_lecturer_data);
                            }
                        }else{
                            $s_class_group_id = $this->uuid->v4();
                            $mbo_prodi_data = $this->Spm->get_study_program(set_value('study_program_id'), false)[0];
                            $s_class_name = $o_offered_subjet_data->subject_name.' '.$mbo_prodi_data->study_program_abbreviation.' '.set_value('academic_year_id').set_value('semester_type_id').' OFSE';
                            $a_class_group_data = array(
                                'class_group_id' => $s_class_group_id,
                                'academic_year_id' => set_value('academic_year_id'),
                                'semester_type_id' => set_value('semester_type_id'),
                                'class_group_name' => $s_class_name
                            );
    
                            $a_class_group_lecturer_data['class_group_id'] = $s_class_group_id;
                            $a_class_group_subject_data = array(
                                'class_group_subject_id' => $this->uuid->v4(),
                                'class_group_id' => $s_class_group_id,
                                'offered_subject_id' => $s_offered_subject_id
                            );
    
                            if ($this->Cgm->save_data($a_class_group_data)) {
                                $this->Cgm->save_class_group_lecturer($a_class_group_lecturer_data);
                                $this->Cgm->save_class_group_subject($a_class_group_subject_data);
                            }
                        }
    
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $a_rtn = array('code' => 1, 'message' => 'Error processing data');
                        }else{
                            $this->db->trans_commit();
                            $a_rtn = array('code' => 0, 'message' => 'Success proccessing data');
                        }
                    }
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_rtn);
        }
    }

    public function update_lect_credit()
    {
        if ($this->input->is_ajax_request()) {
            $s_offered_subject_id = $this->input->post('offered_subject_id');
            $s_class_group_lecturer_id = $this->input->post('class_group_lecturer_id');

            $mba_offered_subject_data = $this->Osm->get_offered_subject_subject([
                'os.offered_subject_id' => $s_offered_subject_id
            ]);

            if ($mba_offered_subject_data) {
                if (in_array($mba_offered_subject_data[0]->semester_type_id, array(7, 8))) {
                    $s_semester_type_parent_id = ($mba_offered_subject_data[0]->semester_type_id == 7) ? 1 : 2;
                    $s_field_offered_subject_end_date = 'offer_subject_short_semester_end_date';
                }else{
                    $s_semester_type_parent_id = $mba_offered_subject_data[0]->semester_type_id;
                    $s_field_offered_subject_end_date = 'offer_subject_end_date';
                }

                $b_checker_semester_academic = modules::run('academic/semester/checker_semester_academic', null, $s_field_offered_subject_end_date, $mba_offered_subject_data[0]->academic_year_id, $s_semester_type_parent_id);
                if (!$b_checker_semester_academic) {
                    if ($this->input->post('offer_subject_access') == 0) {
                        $this->load->model('employee/Employee_model', 'Emm');
                        $mbo_user_login = $this->Emm->get_employee_data(array('em.personal_data_id' => $this->session->userdata('user')))[0];
                        $message = 'Offer subject period is not valid!';

                        $a_rtn = array('code' => 2, 'message' => $message);
                        print json_encode($a_rtn);
                        exit;
                    }
                }

                $this->form_validation->set_rules('credit_allocation', 'Credit Allocation', 'required|trim|numeric');
                if ($this->form_validation->run()) {
                    $mba_class_group_lecturer_data = $this->General->get_where('dt_class_group_lecturer', ['class_group_lecturer_id' => $s_class_group_lecturer_id]);
                    $mba_class_group_lecturer_list = $this->General->get_where('dt_class_group_lecturer', ['class_group_id' => $mba_class_group_lecturer_data[0]->class_group_id]);
                    $mba_class_master_data = $this->General->get_where('dt_class_master_class', ['class_group_id' => $mba_class_group_lecturer_data[0]->class_group_id]);

                    $a_total_credit_allocation = [];
                    if ($mba_class_group_lecturer_list) {
                        foreach ($mba_class_group_lecturer_list as $o_class_lecturer) {
                            if ($o_class_lecturer->class_group_lecturer_id == $s_class_group_lecturer_id) {
                                array_push($a_total_credit_allocation, set_value('credit_allocation'));
                            }
                            else {
                                array_push($a_total_credit_allocation, $o_class_lecturer->credit_allocation);
                            }
                        }
                    }

                    if (array_sum($a_total_credit_allocation) <= $mba_offered_subject_data[0]->curriculum_subject_credit) {
                        if ($mba_class_master_data) {
                            $mba_class_master_class = $this->General->get_where('dt_class_master_class', ['class_master_id' => $mba_class_master_data[0]->class_master_id]);
                            foreach ($mba_class_master_class as $o_class_master) {
                                $mba_class_group_lecturer_master = $this->General->get_where('dt_class_group_lecturer', [
                                    'class_group_id' => $o_class_master->class_group_id,
                                    'employee_id' => $mba_class_group_lecturer_data[0]->employee_id
                                ]);

                                if ($mba_class_group_lecturer_master) {
                                    $a_lecturer_update_data = [
                                        'credit_allocation' => set_value('credit_allocation')
                                    ];
                                    $this->Cgm->save_class_group_lecturer($a_lecturer_update_data, $mba_class_group_lecturer_master[0]->class_group_lecturer_id);
                                }
                            }

                            $this->Cgm->save_class_master_lect_data(['credit_allocation' => set_value('credit_allocation')], [
                                'class_master_id' => $mba_class_master_data[0]->class_master_id,
                                'employee_id' => $mba_class_group_lecturer_data[0]->employee_id
                            ]);

                            $s_text = $this->session->userdata('name').' has changes credit allocation for subject '.$mba_offered_subject_data[0]->subject_name.' in semester '.$mba_offered_subject_data[0]->academic_year_id.'-'.$mba_offered_subject_data[0]->semester_type_id;
                            modules::run('messaging/send_email', 
                                'employee@company.ac.id',
                                '[ACADEMIC-INFO] Changed Allocation Credit the Offered Subject',
                                $s_text,
                                'employee@company.ac.id',
                                false,
                                false,
                                ''
                            );
                            $a_return = ['code' => 0, 'message' => 'Success'];
                        }
                        else {
                            $a_lecturer_update_data = [
                                'credit_allocation' => set_value('credit_allocation')
                            ];
                            $this->Cgm->save_class_group_lecturer($a_lecturer_update_data, $s_class_group_lecturer_id);

                            $a_return = ['code' => 0, 'message' => 'Success'];
                        }
                    }
                    else {
                        $a_return = ['code' => 1, 'message' => 'total credit exceeds '.$mba_offered_subject_data[0]->curriculum_subject_credit];
                    }
                    
                }
                else {
                    $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
                }

            }
            else {
                $a_return = ['code' => 1, 'message' => 'Error getting offered subject data!'];
            }

            print json_encode($a_return);
        }
    }

    public function save_team_teaching()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('credit_allocation', 'Credit Allocation', 'required|trim|numeric');
            $this->form_validation->set_rules('personal_data_name', 'Lecturer', 'required');
            $this->form_validation->set_rules('academic_year_id', 'Running Year', 'required');
            $this->form_validation->set_rules('semester_type_id', 'Semester type', 'required');
            $this->form_validation->set_rules('program_id', 'Program', 'required');
            $this->form_validation->set_rules('study_program_id', 'Study Program', 'required');
            $s_offered_subject_id = $this->input->post('offered_subject_id');
            $s_employee_id = $this->input->post('employee_id');
            $i_remaining_credit = $this->input->post('remaining_credit');
            $i_lecturer_reported = $this->input->post('lecturer_reported');

            if (in_array($this->input->post('semester_type_id'), array(7, 8))) {
                $s_semester_type_parent_id = ($this->input->post('semester_type_id') == 7) ? 1 : 2;
                $s_field_offered_subject_end_date = 'offer_subject_short_semester_end_date';
            }else{
                $s_semester_type_parent_id = $this->input->post('semester_type_id');
                $s_field_offered_subject_end_date = 'offer_subject_end_date';
            }

            $b_checker_semester_academic = modules::run('academic/semester/checker_semester_academic', null, $s_field_offered_subject_end_date, $this->input->post('academic_year_id'), $s_semester_type_parent_id);
            if (!$b_checker_semester_academic) {
                if ($this->input->post('offer_subject_access') == 0) {
                    $this->load->model('employee/Employee_model', 'Emm');
                    $mbo_user_login = $this->Emm->get_employee_data(array('em.personal_data_id' => $this->session->userdata('user')))[0];
                    $message = 'Offer subject period is not valid!';
                    // if ($mbo_user_login->employee_id_number == '221911') {
                    //     $message .= '<br><a class="btn btn-link" onclick="show_modal_ext()">Click here</a> to Customize';
                    // }

                    $a_rtn = array('code' => 2, 'message' => $message, 'data' => $this->input->post('semester_type_id'));
                    print json_encode($a_rtn);
                    exit;
                }
            }

            if ($i_lecturer_reported == 1) {
                $this->form_validation->set_rules('employee_id_reported', 'Lecturer Reported', 'required|trim');
            }

            if ($this->form_validation->run()) {
                if ($s_offered_subject_id == '') {
                    $a_rtn = array('code' => 1, 'message' => 'Unable to get offered subject data');
                }else if (($s_employee_id == '')) {
                    $a_rtn = array('code' => 1, 'message' => 'Plaese select lecturer with autocomplete');
                }
                else if (($i_remaining_credit == 0) OR (set_value('credit_allocation') > $i_remaining_credit)) {
                    $a_rtn = array('code' => 1, 'message' => 'invalid credit allocation');
                }
                else{
                    $this->db->trans_start();

                    $o_offered_subjet_data = $this->Osm->get_offered_subject_lists_filtered(array('offered_subject_id' => $s_offered_subject_id))[0];
                    $a_class_checker_data = array(
                        'cg.academic_year_id' => set_value('academic_year_id'),
                        'cg.semester_type_id' => set_value('semester_type_id'),
                        'ofs.program_id' => set_value('program_id'),
                        'ofs.study_program_id' => set_value('study_program_id'),
                        'cgs.offered_subject_id' => $s_offered_subject_id
                    );

                    $a_class_group_lecturer_data = array(
                        'class_group_lecturer_id' => $this->uuid->v4(),
                        'employee_id' => $s_employee_id,
                        'employee_id_reported' => ($i_lecturer_reported == 1) ? set_value('employee_id_reported') : $s_employee_id,
                        'credit_allocation' => set_value('credit_allocation'),
                        'credit_charged' => set_value('credit_allocation'),
                        'class_group_lecturer_preferable_day' => ($this->input->post('class_group_lecturer_preferable_day') == '') ? NULL : $this->input->post('class_group_lecturer_preferable_day'),
                        'class_group_lecturer_preferable_time' => ($this->input->post('class_group_lecturer_preferable_time') == '') ? NULL : $this->input->post('class_group_lecturer_preferable_time'),
                        'class_group_lecturer_priority' => ($this->input->post('class_group_lecturer_priority') == '') ? NULL : $this->input->post('class_group_lecturer_priority')
                    );

                    $save_data = false;
                    $a_class_lect_data = $this->Cgm->get_class_by_offered_subject($a_class_checker_data);
                    if ($a_class_lect_data) {
                        $s_class_group_id = $a_class_lect_data[0]->class_group_id;
                        $a_class_group_lecturer_data['class_group_id'] = $s_class_group_id;
                        // filter here

                        $save_data = $this->Cgm->save_class_group_lecturer($a_class_group_lecturer_data);
                    }else{
                        $s_class_group_id = $this->uuid->v4();
                        $mbo_prodi_data = $this->Spm->get_study_program(set_value('study_program_id'), false)[0];
                        $s_class_name = $o_offered_subjet_data->subject_name.' '.$mbo_prodi_data->study_program_abbreviation.' '.set_value('academic_year_id').set_value('semester_type_id');
                        $a_class_group_data = array(
                            'class_group_id' => $s_class_group_id,
                            'academic_year_id' => set_value('academic_year_id'),
                            'semester_type_id' => set_value('semester_type_id'),
                            'class_group_name' => $s_class_name
                        );

                        $a_class_group_lecturer_data['class_group_id'] = $s_class_group_id;
                        $a_class_group_subject_data = array(
                            'class_group_subject_id' => $this->uuid->v4(),
                            'class_group_id' => $s_class_group_id,
                            'offered_subject_id' => $s_offered_subject_id
                        );

                        if ($save_data = $this->Cgm->save_data($a_class_group_data)) {
                            $this->Cgm->save_class_group_lecturer($a_class_group_lecturer_data);
                            $this->Cgm->save_class_group_subject($a_class_group_subject_data);
                        }
                    }

                    if ($save_data) {
                        $mba_class_master_data = $this->Cgm->get_class_master_group(array('class_group_id' => $s_class_group_id))[0];
                        $s_class_master_id = ($mba_class_master_data) ? $mba_class_master_data->class_master_id : $this->uuid->v4();
                        if (!$mba_class_master_data) {
                            $a_class_master_data = array(
                                'class_master_id' => $s_class_master_id,
                                'academic_year_id' => set_value('academic_year_id'),
                                'semester_type_id' => set_value('semester_type_id'),
                                'class_master_name' => $s_class_name,
                                'date_added' => date('Y-m-d H:i:s')
                            );

                            if ($this->Cgm->save_class_mastering($a_class_master_data)) {
                                $a_class_master_class_data = array(
                                    'class_master_id' => $s_class_master_id,
                                    'class_group_id' => $s_class_group_id,
                                    'date_added' => date('Y-m-d H:i:s')
                                );

                                $this->Cgm->save_class_master_class($a_class_master_class_data);
                            }
                        }
                        else {
                            $mba_class_master_class = $this->Cgm->get_class_master_group(array('class_master_id' => $mba_class_master_data->class_master_id));
                            if (($mba_class_master_class) AND (count($mba_class_master_class) > 1)) {
                                // print('Class must separate!');
                                $a_return = $this->separate_class($s_class_group_id);
                                // print json_encode($a_return);exit;
                            }
                        }

                        $a_class_master_lect = array(
                            'class_master_lecturer_id' => $this->uuid->v4(),
                            'class_master_id' => $s_class_master_id,
                            'employee_id' => $s_employee_id,
                            'employee_id_reported' => ($i_lecturer_reported == 1) ? set_value('employee_id_reported') : $s_employee_id,
                            'credit_allocation' => set_value('credit_allocation'),
                            'credit_charged' => set_value('credit_allocation'),
                            'class_master_lecturer_preferable_day' => ($this->input->post('class_group_lecturer_preferable_day') == '') ? NULL : $this->input->post('class_group_lecturer_preferable_day'),
                            'class_master_lecturer_preferable_time' => ($this->input->post('class_group_lecturer_preferable_time') == '') ? NULL : $this->input->post('class_group_lecturer_preferable_time'),
                            'class_master_lecturer_priority' => ($this->input->post('class_group_lecturer_priority') == '') ? NULL : $this->input->post('class_group_lecturer_priority')
                        );
                        
                        $this->Cgm->save_class_master_lect_data($a_class_master_lect);
                    }

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $a_rtn = array('code' => 1, 'message' => 'Error processing data');
                    }else{
                        $this->db->trans_commit();
                        $a_rtn = array('code' => 0, 'message' => 'Success proccessing data');
                    }
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }
        }else{
            $a_rtn = array('code' => 0, 'message' => 'Nothing result');
        }

        print json_encode($a_rtn);
    }

    public function remove_team_teaching()
    {
        if ($this->input->is_ajax_request()) {
            $s_class_group_lecturer_id = $this->input->post('class_group_lecturer_id');

            $mbo_class_group_lecturer_data = $this->Cgm->get_class_group_lecturer(array('class_group_lecturer_id' => $s_class_group_lecturer_id));
            $mbo_class_group_data = $this->Cgm->get_class_group_filtered(array('dcg.class_group_id' => $mbo_class_group_lecturer_data[0]->class_group_id))[0];

            if (in_array($mbo_class_group_data->semester_type_id, array(4, 6))) {
                $s_field_offered_subject_end_date = 'offer_subject_ofse_end_date';
                $s_semester_type_parent_id = ($mbo_class_group_data->semester_type_id == 4) ? 1 : 2;
            }else if (in_array($mbo_class_group_data->semester_type_id, array(7, 8))) {
                $s_field_offered_subject_end_date = 'offer_subject_short_semester_end_date';
                $s_semester_type_parent_id = ($mbo_class_group_data->semester_type_id == 7) ? 1 : 2;
            }else{
                $s_semester_type_parent_id = $mbo_class_group_data->semester_type_id;
                $s_field_offered_subject_end_date = 'offer_subject_end_date';
            }

            $b_checker_semester_academic = modules::run('academic/semester/checker_semester_academic', null, $s_field_offered_subject_end_date, $mbo_class_group_data->academic_year_id, $s_semester_type_parent_id);
            // print('<pre>');
            // var_dump($b_checker_semester_academic);exit;
            if (!$b_checker_semester_academic) {
                $a_rtn = array('code' => 1, 'message' => 'Offer subject period is not valid');
                print json_encode($a_rtn);
                exit;
            }

            $mbo_class_master_data = $this->Cgm->get_class_master_group(array('class_group_id' => $mbo_class_group_data->class_group_id));
            $this->db->trans_start();

            if ($this->Cgm->remove_team_teaching($s_class_group_lecturer_id)) {
                if ($mbo_class_master_data) {
                    $mba_class_master_class = $this->Cgm->get_class_master_group(array('class_master_id' => $mbo_class_master_data[0]->class_master_id));
    
                    if (($mba_class_master_class) AND (count($mba_class_master_class) > 1)) {
                        // print('Class must separate!');
                        $a_return = $this->separate_class($mbo_class_group_data->class_group_id);
                        // print json_encode($a_return);exit;
                    }
                    else{
                        $this->Cgm->delete_class_master_lecturer(array('class_master_id' => $mbo_class_master_data[0]->class_master_id, 'employee_id' => $mbo_class_group_lecturer_data[0]->employee_id));
                    }
                    // $this->Cgm->delete_class_master_lecturer(array('class_master_id' => $mbo_class_master_data[0]->class_master_id, 'employee_id' => $mbo_class_group_lecturer_data[0]->employee_id));
                }

                $a_return = array('code' => 0, 'message' => 'Success');
                $this->db->trans_commit();
            }else{
                $a_return = array('code' => 1, 'message' => 'Error proccessing data');
                $this->db->trans_rollback();
            }

            print json_encode($a_return);exit;
        }
    }

    public function test()
    {
        print('<pre>');
        var_dump($this->session->userdata());
        if ($this->session->userdata('academic_year_id_active') !== null) {
            print('ada');
        }else{
            print('tidak ada');
        }
    }

    public function save_offer_subject()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('Curriculum_model', 'Crm');
            $s_curriculum_subject_id = $this->input->post('curriculum_subject_id');

            $this->form_validation->set_rules('academic_year_id', 'Academic Year', 'required');
            $this->form_validation->set_rules('program_id', 'Program', 'required');
            $this->form_validation->set_rules('semester_type_id', 'Semester Type', 'required');
            $this->form_validation->set_rules('study_program_id', 'Study Program', 'required');

            if (in_array($this->input->post('semester_type_id'), array(4, 6))) {
                $s_semester_type_parent_id = ($this->input->post('semester_type_id') == 4) ? 1 : 2;
                $s_field_offered_subject_end_date = 'offer_subject_ofse_end_date';

                $this->form_validation->set_rules('subject_status', 'Subject Status', 'required');
            }else if (in_array($this->input->post('semester_type_id'), array(7, 8))) {
                $s_semester_type_parent_id = ($this->input->post('semester_type_id') == 7) ? 1 : 2;
                $s_field_offered_subject_end_date = 'offer_subject_short_semester_end_date';
            }else{
                $s_semester_type_parent_id = $this->input->post('semester_type_id');
                $s_field_offered_subject_end_date = 'offer_subject_end_date';
            }

            $b_checker_semester_academic = modules::run('academic/semester/checker_semester_academic', null, $s_field_offered_subject_end_date, $this->input->post('academic_year_id'), $s_semester_type_parent_id);
            if (!$b_checker_semester_academic) {
                if ($this->input->post('offer_subject_access') == 0) {
                    $message = 'Offer subject period is not valid!';
                    $academic_login = modules::run('employee/academic_login');
                    if ($academic_login) {
                        $message .= '<br><a class="btn btn-link" onclick="show_modal_ext()">Click here</a> to Customize';
                    }

                    $a_rtn = array('code' => 2, 'message' => $message);
                    print json_encode($a_rtn);
                    exit;
                }
            }

            $mba_curriculum_subject_data = $this->Crm->get_curriculum_subject_filtered([
                'rcs.curriculum_subject_id' => $s_curriculum_subject_id
            ]);

            if (!$mba_curriculum_subject_data) {
                $a_rtn = array('code' => 7, 'message' => 'failure get curriculum subject data!');
                print json_encode($a_rtn);
                exit;
            }

            if ((!in_array($this->input->post('semester_type_id'), array(4, 6))) AND ($mba_curriculum_subject_data)) {
                if ($mba_curriculum_subject_data[0]->study_program_id != $this->input->post('study_program_id')) {
                    // if ($mba_curriculum_subject_data[0]->curriculum_subject_type == 'mandatory') {
                        $s_subject_curriculum_id = $mba_curriculum_subject_data[0]->subject_id;
                        $mba_curriculum_offered_subject_data = $this->Crm->get_curriculum_subject_filtered([
                            'rc.program_id' => $mba_curriculum_subject_data[0]->program_id,
                            'rc.study_program_id' => $mba_curriculum_subject_data[0]->study_program_id,
                            'rc.academic_year_id' => $mba_curriculum_subject_data[0]->academic_year_id,
                            'rcs.subject_id' => $mba_curriculum_subject_data[0]->subject_id,
                            'rcs.curriculum_subject_type' => $mba_curriculum_subject_data[0]->curriculum_subject_type
                        ]);

                        if ($mba_curriculum_offered_subject_data) {
                            $s_curriculum_subject_id = $mba_curriculum_offered_subject_data[0]->curriculum_subject_id;
                        }
                        else {
                            $mba_curriculum_requirement = $this->General->get_where('ref_curriculum', [
                                'program_id' => $mba_curriculum_subject_data[0]->program_id,
                                'study_program_id' => $mba_curriculum_subject_data[0]->study_program_id,
                                'academic_year_id' => $mba_curriculum_subject_data[0]->academic_year_id
                            ]);

                            if (!$mba_curriculum_requirement) {
                                $mba_curriculum_requirement = $this->General->get_where('ref_curriculum', [
                                    'program_id' => $mba_curriculum_subject_data[0]->program_id,
                                    'study_program_id' => $mba_curriculum_subject_data[0]->study_program_id
                                ]);
                            }

                            if (!$mba_curriculum_requirement) {
                                $a_rtn = array('code' => 4, 'message' => 'no curriculum founded for study program selected!');
                                print json_encode($a_rtn);
                                exit;
                            }
                            
                            $o_curriculum_requirement = $mba_curriculum_requirement[0];
                            $mba_curriculum_semester = $this->General->get_where('ref_curriculum_semester', [
                                'curriculum_id' => $o_curriculum_requirement->curriculum_id,
                                'semester_id' => $mba_curriculum_subject_data[0]->semester_id
                            ]);

                            if (!$mba_curriculum_semester) {
                                $this->Crm->create_new_curriculum_semester([
                                    'curriculum_id' => $o_curriculum_requirement->curriculum_id,
                                    'semester_id' => $mba_curriculum_subject_data[0]->semester_id
                                ]);
                            }

                            $s_curriculum_subject_id = $this->uuid->v4();
                            $a_curriculum_data = [
                                'curriculum_subject_id' => $s_curriculum_subject_id,
                                'curriculum_id' => $o_curriculum_requirement->curriculum_id,
                                'semester_id' => $mba_curriculum_subject_data[0]->semester_id,
                                'subject_id' => $mba_curriculum_subject_data[0]->subject_id,
                                'curriculum_subject_credit' => $mba_curriculum_subject_data[0]->curriculum_subject_credit,
                                'curriculum_subject_ects' => $this->grades->conversion_ects_credit($mba_curriculum_subject_data[0]->curriculum_subject_credit),
                                'curriculum_subject_category' => $mba_curriculum_subject_data[0]->curriculum_subject_category,
                                'curriculum_subject_type' => $mba_curriculum_subject_data[0]->curriculum_subject_type
                            ];

                            $b_save_new_curr_subject = $this->Crm->create_new_curriculum_subject($a_curriculum_data);
                        }
                    // }
                }
            }
            else {
                $a_rtn = array('code' => 3, 'message' => 'failure get data!');
                print json_encode($a_rtn);
                exit;
            }
            
            if ($this->form_validation->run()) {
                $this->db->trans_start();
                $s_offered_subject_id = $this->uuid->v4();
                $a_offered_subject_data = array(
                    'offered_subject_id' => $s_offered_subject_id,
                    'curriculum_subject_id' => $s_curriculum_subject_id,
                    'academic_year_id' => set_value('academic_year_id'),
                    'semester_type_id' => set_value('semester_type_id'),
                    'program_id' => set_value('program_id'),
                    'study_program_id' => set_value('study_program_id'),
                    'date_added' => date('Y-m-d H:i:s')
                );

                if ($this->input->post('is_ofse') == 'true') {
                    $a_offered_subject_data['ofse_status'] = set_value('subject_status');
                    if (set_value('subject_status') == 'mandatory') {
                        $mbo_ofse_status = $this->Osm->get_offered_subject_lists_filtered(array('dos.academic_year_id' => set_value('academic_year_id'), 'dos.semester_type_id' => set_value('semester_type_id'), 'dos.study_program_id' => set_value('study_program_id'), 'dos.program_id' => set_value('program_id'), 'dos.ofse_status' => set_value('subject_status')));
                        if (count($mbo_ofse_status) == 2) {
                            $a_rtn = array('code' => 1, 'message' => 'Subject mandatory is full');
                            print json_encode($a_rtn);exit;
                        }
                    }
                }

                $a_filter_offered_subject = array(
                    'os.academic_year_id' => set_value('academic_year_id'),
                    'os.semester_type_id' => set_value('semester_type_id'),
                    'os.study_program_id' => set_value('study_program_id'),
                    'os.program_id' => set_value('program_id'),
                    'sn.subject_name' => $mba_curriculum_subject_data[0]->subject_name,
                    'cs.curriculum_subject_credit' => $mba_curriculum_subject_data[0]->curriculum_subject_credit,
                    'cs.semester_id != ' => 17,
                    'cs.curriculum_subject_category' => 'regular semester'
                );

                if ($mba_offered_subject_data = $this->Osm->get_offered_subject_subject($a_filter_offered_subject)) {
                    $a_rtn = array('code' => 1, 'message' => 'Subject is exists in this study program and semester type selected!');
                    // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                    //     print('<pre>');var_dump($mba_offered_subject_data);exit;
                    // }
                }else{
                    if ($this->Osm->save_offer_subject($a_offered_subject_data)) {
                        if ($this->input->post('is_ofse') == 'true') {
                            $a_subject_data = $this->Crm->get_curriculum_subject_filtered(array('curriculum_subject_id' => $s_curriculum_subject_id))[0];
                            $s_class_group_id = $this->uuid->v4();
                            $a_class_data = array(
                                'class_group_id' => $s_class_group_id,
                                'academic_year_id' => set_value('academic_year_id'),
                                'semester_type_id' => set_value('semester_type_id'),
                                'class_group_name' => $a_subject_data->subject_name,
                                'date_added' => date('Y-m-d H:i:s')
                            );

                            if ($this->Cgm->save_data($a_class_data)) {
                                if ($this->Cgm->save_class_group_subject(array(
                                    'class_group_subject_id' => $this->uuid->v4(),
                                    'class_group_id' => $s_class_group_id,
                                    'offered_subject_id' => $s_offered_subject_id,
                                    'date_added' => date('Y-m-d H:i:s')
                                ))) {
                                    $a_rtn = array('code' => 0, 'message' => 'Success transfer data');
                                }else {
                                    $a_rtn = array('code' => 1, 'message' => 'Error transfer subject data');
                                }
                            }else{
                                $a_rtn = array('code' => 1, 'message' => 'Error saving transfer data');
                            }
                        }else{
                            $a_rtn = array('code' => 0, 'message' => 'Success transfer data');
                        }
                    }else{
                        $a_rtn = array('code' => 1, 'message' => 'Error transfer data');
                    }
                }

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                }else{
                    $this->db->trans_commit();
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }
        }else{
            $a_rtn = array('code' => 1, 'message' => 'Nothing action');
        }

        print json_encode($a_rtn);exit;
    }

    public function remove_offered_subject()
    {
        if ($this->input->is_ajax_request()) {
            $s_offered_subject_id = $this->input->post('offered_subject_id');
            $mbo_offered_subject_data = $this->Osm->get_offered_subject_filtered(array('offered_subject_id' => $s_offered_subject_id))[0];

            if ($mbo_offered_subject_data) {
                if (in_array($mbo_offered_subject_data->semester_type_id, array(4, 6))) {
                    $s_field_offered_subject_end_date = 'offer_subject_ofse_end_date';
                    $s_semester_type_parent_id = ($mbo_offered_subject_data->semester_type_id == 4) ? 1 : 2;
                }else if (in_array($mbo_offered_subject_data->semester_type_id, array(7, 8))) {
                    $s_field_offered_subject_end_date = 'offer_subject_short_semester_end_date';
                    $s_semester_type_parent_id = ($mbo_offered_subject_data->semester_type_id == 7) ? 1 : 2;
                }else{
                    $s_field_offered_subject_end_date = 'offer_subject_end_date';
                    $s_semester_type_parent_id = $mbo_offered_subject_data->semester_type_id;
                }

                $b_checker_semester_academic = modules::run('academic/semester/checker_semester_academic', null, $s_field_offered_subject_end_date, $mbo_offered_subject_data->academic_year_id, $s_semester_type_parent_id);
                if (!$b_checker_semester_academic) {
                    $a_rtn = array('code' => 1, 'message' => 'Offer subject period is not valid');
                    print json_encode($a_rtn);
                    exit;
                }

                $this->db->trans_start();
                $mba_class_group_data = $this->Cgm->get_class_group_subject(false, array('cgs.offered_subject_id' => $s_offered_subject_id));
                $b_separated = false;
                if ($mba_class_group_data) {
                    $check_class_group_data = true;
                    foreach ($mba_class_group_data as $o_class_group) {
                        $check_subject_filled = $this->General->get_where('dt_score', [
                            'class_group_id' => $o_class_group->class_group_id,
                            'score_approval' => 'approved'
                        ]);

                        if (!$check_subject_filled) {
                            $check_class_group_data = false;
                        }
                    }

                    if (!$check_class_group_data) {
                        $mbo_class_master_data = $this->Cgm->get_class_master_group(array('class_group_id' => $mba_class_group_data[0]->class_group_id));

                        if ($mbo_class_master_data) {
                            $mba_class_master_class = $this->Cgm->get_class_master_group(array('class_master_id' => $mbo_class_master_data[0]->class_master_id));
            
                            if (($mba_class_master_class) AND (count($mba_class_master_class) > 1)) {
                                
                                $a_return = $this->separate_class($mba_class_group_data[0]->class_group_id);

                                if ($a_return['code'] == 0) {
                                    $b_separated = true;
                                }
                            }
                            else{
                                $mba_class_master_lecturer = $this->Cgm->get_class_master_lecturer(['class_master_id' => $mbo_class_master_data[0]->class_master_id]);
                                if ($mba_class_master_lecturer) {
                                    $this->Cgm->delete_class_master_lecturer(array('class_master_id' => $mbo_class_master_data[0]->class_master_id, 'employee_id' => $mba_class_master_lecturer[0]->employee_id));
                                }
                            }
                        }
                    }else{
                        $a_rtn = array('code' => 1, 'message' => 'Subject already taken by student!');
                        print json_encode($a_rtn);
                        exit;
                    }
                }
                
                if ($b_separated) {
                    $mba_class_group_data = $this->Cgm->get_class_group_subject(false, array('cgs.offered_subject_id' => $s_offered_subject_id));
                    if ($mba_class_master_data = $this->Cgm->get_class_id_master_class(array('class_group_id' => $mba_class_group_data[0]->class_group_id))) {
                        $this->Cgm->remove_class_master_sync($mba_class_master_data[0]->class_master_id);
                    }
                    $this->Cgm->remove_class_group_data($mba_class_group_data[0]->class_group_id);
                }
                
                $delete = $this->Osm->remove_offered_subject($s_offered_subject_id);
                // var_dump($delete);exit;
                if (!$delete) {
                    $a_rtn = array('code' => 1, 'message' => 'Error remove offered subject data');
                    print json_encode($a_rtn);
                    exit;
                }

                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $a_rtn = array('code' => 0, 'message' => 'Success remove offered subject');
                }else{
                    $this->db->trans_rollback();
                    $a_rtn = array('code' => 1, 'message' => 'Error remove offered subject');
                }
            }else {
                $a_rtn = array('code' => 1, 'message' => 'Offered subject not found');
            }
        }else{
            $a_rtn = array('code' => 1, 'message' => 'Nothing action');
        }

        print json_encode($a_rtn);
    }

    function get_student_taken() {
        if ($this->input->post()) {
            $s_offered_subject_id = $this->input->post('offered_subject_id');
            // $s_offered_subject_id = '240367dc-5fde-4c5a-8671-439286b45bdb';

            $s_offered_subject_id = (empty($s_offered_subject_id)) ? 'x' : $s_offered_subject_id;
            $mba_class_group_data = $this->Cgm->get_class_group_subject(false, array('cgs.offered_subject_id' => $s_offered_subject_id));
            $a_data = false;
            if ($mba_class_group_data) {
                $a_data = [];
                foreach ($mba_class_group_data as $o_class_group) {
                    $mba_subject_taken = $this->Scm->get_student_by_score([
                        'sc.class_group_id' => $o_class_group->class_group_id,
                        'sc.score_approval != ' => 'reject'
                    ]);

                    if ($mba_subject_taken) {
                        foreach ($mba_subject_taken as $o_score) {
                            array_push($a_data, $o_score);
                        }
                    }
                }

                if (empty($a_data)) {
                    $a_data = false;
                }
            }

            // $mba_offered_subject_data = $this->General->get_where('dt_offered_subject', ['offered_subject_id' => $s_offered_subject_id]);
            // if ($mba_offered_subject_data) {
            //     $o_offered_subject = $mba_offered_subject_data[0];
            //     $mba_student_taken = $this->Scm->get_student_krs_class([
            //         'st.program_id' => $o_offered_subject->program_id,
            //         'sc.academic_year_id' => $o_offered_subject->academic_year_id,
            //         'sc.semester_type_id' => $o_offered_subject->semester_type_id,
            //         'sc.curriculum_subject_id' => $o_offered_subject->curriculum_subject_id,
            //         'sc.score_approval' => 'approved'
            //     ]);
            //     $a_return = ['code' => 0, 'data' => $mba_student_taken];
            // }
            // else {
            //     $a_return = ['code' => 1, 'data' => false];
            // }

            print json_encode(['data' => $a_data]);
        }
    }
}
