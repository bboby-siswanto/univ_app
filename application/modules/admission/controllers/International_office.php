<?php
class International_office extends App_core
{
    function __construct()
    {
        parent::__construct('staff_international_office');

        $this->load->model('academic/Academic_year_model', 'Aym');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('thesis/Thesis_model', 'Tm');
        $this->load->model('student/Internship_model', 'Itm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('institution/Institution_model', 'Inm');
        $this->load->model('admission/International_office_model', 'Iom');
    }

    public function submit_abroad_form()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('bf_student_id', 'Student', 'trim|required');
            $this->form_validation->set_rules('bf_program_id', 'Program', 'trim|required');
            $this->form_validation->set_rules('bf_institution_id', 'Study Location', 'trim|required');
            $this->form_validation->set_rules('bf_academic_year', 'Academic Year', 'trim|required');
            $s_passpor_no = $this->input->post('bf_passpor_no');
            $s_passpor_valid_from = $this->input->post('bf_passpor_valid_from');
            $s_passpor_valid_to = $this->input->post('bf_passpor_valid_to');

            if ($this->form_validation->run()) {
                $a_exchange_data = [
                    'student_id' => set_value('bf_student_id'),
                    'program_id' => set_value('bf_program_id'),
                    'institution_id' => set_value('bf_institution_id'),
                    'academic_year_id' => set_value('bf_academic_year'),
                    'semester_type_id' => $this->session->userdata('semester_type_id_active'),
                    'exchange_type' => 'out',
                    'passport_number' => (!empty($s_passpor_no)) ? $s_passpor_no : NULL
                ];

                if (!empty($this->input->post('bf_exchange_id'))) {
                    $s_exchange_id = $this->input->post('bf_exchange_id');
                    $save = $this->Iom->submit_student_abroad($a_exchange_data, ['exchange_id' => $s_exchange_id]);
                }
                else {
                    $s_exchange_id = $this->uuid->v4();
                    $a_exchange_data['exchange_id'] = $s_exchange_id;
                    $a_exchange_data['date_added'] = date('Y-m-d H:i:s');
                    $save = $this->Iom->submit_student_abroad($a_exchange_data);
                }

                if (!$save) {
                    $a_return = ['code' => 1, 'message' => 'Unknow error submitting data'];
                }
                else {
                    if ((!empty($s_passpor_valid_from)) AND (!empty($s_passpor_valid_to))) {
                        $a_personal_data = [
                            'personal_data_id_card_valid_from' => date('Y-m-d', strtotime($s_passpor_valid_from)),
                            'personal_data_id_card_valid' => date('Y-m-d', strtotime($s_passpor_valid_to))
                        ];

                        $mba_student_data = $this->General->get_where('dt_student', ['student_id' => set_value('bf_student_id')]);
                        $s_personal_data_id = $mba_student_data[0]->personal_data_id;
                        $this->General->update_data('dt_personal_data', $a_personal_data, ['personal_data_id' => $s_personal_data_id]);
                    }
                    $a_return = ['code' => 0, 'message' => 'Success!'];
                }
            }
            else {
                $a_return = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
            }

            print json_encode($a_return);exit;
        }
    }

    public function io_student_list()
    {
        $this->a_page_data['batch'] = $this->General->get_batch();
		$this->a_page_data['ref_program'] = $this->Spm->get_program($a_clause = false, [7,8,6,4]);
		$this->a_page_data['study_program'] = $this->Spm->get_study_program_instititute();
        $this->a_page_data['status_lists'] = $this->General->get_enum_values('dt_student', 'student_status');
		
		$this->a_page_data['body'] = $this->load->view('student/table/io_student_list_table', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
    }

    public function student_abroad()
    {
        $mba_student_list = $this->Stm->get_student_filtered(false, ['active', 'graduated']);
        $a_student_list = [];
        if ($mba_student_list) {
            foreach ($mba_student_list as $o_student) {
                $s_student_name = str_replace("'", "", $o_student->personal_data_name);
                $a_student_data = [
                    'id' => $o_student->student_id,
                    'text' => $s_student_name,
                    'html' => '<span>'.$s_student_name.' <b>('.$o_student->study_program_abbreviation.'/'.$o_student->academic_year_id.')</b>.</span>',
                    'student_id' => $o_student->student_id,
                    'personal_data_name' => $s_student_name,
                    'student_number' => $o_student->student_number,
                    'study_program_name' => $o_student->study_program_name,
                    'faculty_name' => $o_student->faculty_name
                ];
                array_push($a_student_list, $a_student_data);
            }
        }
        
        $this->a_page_data['batch'] = $this->General->get_batch(false, false, 'DESC');
        $this->a_page_data['student_list'] = $a_student_list;
		$this->a_page_data['ref_program'] = $this->Spm->get_program($a_clause = false, [7,8,6,4]);
		$this->a_page_data['study_program'] = $this->Spm->get_study_program_instititute();
        $this->a_page_data['status_lists'] = $this->General->get_enum_values('dt_student', 'student_status');
		
		$this->a_page_data['body'] = $this->load->view('admission/international_office/student_abroad', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
    }

    public function get_student_io()
    {
        if ($this->input->is_ajax_request()) {
            $a_filter_form = [
                'ex.program_id' => $this->input->post('program_id'),
                'ds.study_program_id' => $this->input->post('study_program_id'),
                'ds.academic_year_id' => $this->input->post('academic_year_id'),
                'ex.academic_year_id' => $this->input->post('academic_study_year'),
                'ds.student_status' => $this->input->post('student_status'),
                'ex.exchange_type' => $this->input->post('exchange_type')
            ];

            foreach ($a_filter_form as $key => $value) {
                if (empty($value)) {
                    unset($a_filter_form[$key]);
                }
                else if($value == 'all'){
					unset($a_filter_form[$key]);
				}
                else if (is_array($value)) {
                    unset($a_filter_form[$key]);
                }
            }

            $status_filter = false;
			if ((isset($a_filter_form['student_status'])) AND (is_array($a_filter_form['student_status']))) {
				if (count($a_filter_form['student_status']) > 0) {
					$status_filter = $this->input->post('student_status');
				}
			}

            $prodi_filter = false;
			if ((isset($a_filter_form['study_program_id'])) AND (is_array($a_filter_form['study_program_id']))) {
				if (count($a_filter_form['study_program_id']) > 0) {
					$prodi_filter = $this->input->post('study_program_id');
				}
			}

            if (!isset($a_filter_form['ex.exchange_type'])) {
                $a_filter_form['ex.exchange_type'] = 'in';
            }

            // print('<pre>');var_dump($a_filter_form);exit;
            $mba_filtered_data = $this->Iom->get_student_filtered($a_filter_form, $status_filter, false, $prodi_filter, [4,6,7,8]);
            if ($mba_filtered_data) {
                foreach ($mba_filtered_data as $o_student) {
                    $mba_student_exchange_data = $this->Iom->get_international_data([
                        'ex.exchange_id' => $o_student->exchange_id
                    ]);

                    $a_filedata = [];

                    if ($this->input->post('exchange_type') == 'out') {
                        $mba_thesis_file = $this->Tm->get_list_thesis_file([
                            'ts.student_id' => $o_student->student_id
                        ]);
                        if ($mba_thesis_file) {
                            foreach ($mba_thesis_file as $o_thesis_file) {
                                $a_filetype = explode('_', $o_thesis_file->thesis_filetype);
                                $s_linkfile = 'student/'.$o_student->student_batch.'/'.$o_student->study_program_abbreviation.'/'.$o_student->student_id.'/'.$a_filetype[0].'_'.$a_filetype[1].'/'.$o_thesis_file->thesis_filename;
                                $s_linkfile = urlencode(base64_encode($s_linkfile));
                                $a_datafile = [
                                    'document_id' => $o_thesis_file->thesis_file_id,
                                    'document_name' => ucwords(strtolower(str_replace('_', ' ', $o_thesis_file->thesis_filetype))),
                                    // 'document_weight' => $o_files->document_weight,
                                    'document_requirement_link' => $o_thesis_file->thesis_filename,
                                    'personal_data_id' => $o_student->personal_data_id,
                                    'document_valid' => $s_linkfile
                                ];
                                array_push($a_filedata, $a_datafile);
                            }
                        }

                        $mba_internshipfile = $this->Itm->get_internship_document([
                            'st.student_id' => $o_student->student_id
                        ]);
                        if ($mba_internshipfile) {
                            foreach ($mba_internshipfile as $o_internship_file) {
                                $s_linkfile = 'student/'.$o_internship_file->academic_year_id.'/'.$o_internship_file->study_program_abbreviation.'/'.$o_student->student_id.'/internship/'.$o_internship_file->document_link;
                                $s_linkfile = urlencode(base64_encode($s_linkfile));
                                $a_datafile = [
                                    'document_id' => $o_internship_file->internship_file_id,
                                    'document_name' => 'Internship '.ucwords(strtolower(str_replace('_', ' ', $o_internship_file->document_type))),
                                    // 'document_weight' => $o_files->document_weight,
                                    'document_requirement_link' => $o_internship_file->document_name,
                                    'personal_data_id' => $o_student->personal_data_id,
                                    'document_valid' => $s_linkfile
                                ];
                                array_push($a_filedata, $a_datafile);
                            }
                        }

                        $mba_abroadfile = $this->Iom->get_student_abroad_document([
                            'st.student_id' => $o_student->student_id
                        ]);
                        if ($mba_abroadfile) {
                            foreach ($mba_abroadfile as $o_abroadfile) {
                                $s_linkfile = 'student/'.$o_student->student_batch.'/'.$o_student->study_program_abbreviation.'/'.$o_student->student_id.'/abroad_doc/'.$o_abroadfile->document_link;
                                $s_linkfile = urlencode(base64_encode($s_linkfile));
                                $a_datafile = [
                                    'document_id' => $o_abroadfile->exchange_file_id,
                                    'document_name' => 'Abroad '.ucwords(strtolower(str_replace('_', ' ', $o_abroadfile->document_type))),
                                    // 'document_weight' => $o_files->document_weight,
                                    'document_requirement_link' => $o_abroadfile->document_name,
                                    'personal_data_id' => $o_student->personal_data_id,
                                    'document_valid' => $s_linkfile
                                ];
                                array_push($a_filedata, $a_datafile);
                            }
                        }
                    }
                    else {
                        $mba_student_file = $this->Iom->get_candidate_document([
                            'st.student_id' => $o_student->student_id
                        ]);
                        if ($mba_student_file) {
                            foreach ($mba_student_file as $o_files) {
                                $s_linkfile = 'student/'.$o_student->student_batch.'/'.date('M', strtotime($o_student->register_date)).'/'.$o_student->personal_data_id.'/'.$o_files->document_requirement_link;
                                $s_linkfile = urlencode(base64_encode($s_linkfile));
                                $a_datafile = [
                                    'document_id' => $o_files->document_id,
                                    'document_name' => $o_files->document_name,
                                    'document_weight' => $o_files->document_weight,
                                    'document_requirement_link' => $o_files->document_requirement_link,
                                    'personal_data_id' => $o_files->personal_data_id,
                                    'document_valid' => $s_linkfile
                                ];
                                array_push($a_filedata, $a_datafile);
                            }
                        }
                    }
                    
                    $s_passpor_valid_from = (!is_null($o_student->personal_data_id_card_valid_from)) ? date('d F Y', strtotime($o_student->personal_data_id_card_valid_from)) : 'N/A';
                    $s_passpor_valid_to = (!is_null($o_student->personal_data_id_card_valid)) ? date('d F Y', strtotime($o_student->personal_data_id_card_valid)) : 'N/A';
                    $o_student->personal_data_dob = date('d F Y', strtotime($o_student->personal_data_date_of_birth));
                    $o_student->passpor_valid = $s_passpor_valid_from.' to '.$s_passpor_valid_to;

                    $mba_parent_data = $this->Iom->get_parent_student($o_student->personal_data_id);
                    $o_student->parent_name = ($mba_parent_data) ? $mba_parent_data[0]->personal_data_name : '';
                    $o_student->parent_relation = ($mba_parent_data) ? $mba_parent_data[0]->family_member_status : '';
                    $o_student->parent_email = ($mba_parent_data) ? $mba_parent_data[0]->personal_data_email : '';
                    $o_student->parent_phone = ($mba_parent_data) ? $mba_parent_data[0]->personal_data_cellular : '';
                    $o_student->document_list = $a_filedata;  
                    $o_student->program_exchange = ($mba_student_exchange_data) ? $mba_student_exchange_data[0]->program_name : '';

                    $mba_institution_contact = $this->Iom->get_institution_contact($o_student->institution_id);
                    $o_student->coordinator_name = ($mba_institution_contact) ? $mba_institution_contact->personal_data_name : '';
                    $o_student->coordinator_email = ($mba_institution_contact) ? $mba_institution_contact->personal_data_email : '';
                    $o_student->coordinator_phone = ($mba_institution_contact) ? $mba_institution_contact->personal_data_cellular : '';
                }
            }

            print json_encode(array('code' => 0, 'data' => $mba_filtered_data));
        }
    }
}
