<?php
class Ijd extends App_core
{
    public function __construct()
    {
        parent::__construct('academic');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('Academic_year_model', 'Aym');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('academic/Curriculum_model', 'Cm');
    }

    public function research_semester_subject($s_student_id)
    {
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
        if ($mbo_student_data) {
            $mbo_score_research_semester = $this->Scm->get_score_like_subject_name([
                'sc.student_id' => $s_student_id,
                'sc.score_approval' => 'approved'
            ], 'research semester')[0];

            if ($mbo_score_research_semester) {
                $this->a_page_data['student_id'] = $s_student_id;
                $this->a_page_data['body'] = $this->load->view('academic/score/research_semester_subject', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }else{
                print('Student never take Research Semester subject!');
            }
        }
    }

    public function transfer_curriculum_table($s_student_id)
    {
        $this->a_page_data['student_id'] = $s_student_id;
        $this->load->view('score/table/transfer_credit_curriculum_table', $this->a_page_data);
    }

    public function submit_subject_research_semester()
    {
        if ($this->input->is_ajax_request()) {
            $s_curriculum_subject_id = $this->input->post('curriculum_subject_id');
            $s_student_id = $this->input->post('student_id');

            $mba_curriculum_subject_data = $this->Cm->get_curriculum_subject_filtered(array(
                'curriculum_subject_id' => $s_curriculum_subject_id
            ))[0];

            $mba_score_exist = $this->Scm->get_score_like_subject_name([
                'sc.student_id' => $s_student_id,
                'curs.curriculum_subject_category' => 'research semester',
                'sc.score_approval' => 'approved'
            ], $mba_curriculum_subject_data->subject_name);

            $mbo_score_research_semester = $this->Scm->get_score_like_subject_name([
                'sc.student_id' => $s_student_id,
                'sc.score_approval' => 'approved'
            ], 'research semester')[0];

            if ($mba_score_exist) {
                $a_rtn = ['code' => 1, 'message' => 'Subject is already taken!'];
            }else{
                $a_score_data = array(
                    'student_id' => $s_student_id,
                    'curriculum_subject_id' => $s_curriculum_subject_id,
                    'semester_id' => $mbo_score_research_semester->semester_id,
                    'semester_type_id' => $mbo_score_research_semester->semester_type_id,
                    'academic_year_id' => $mbo_score_research_semester->academic_year_id,
                    'score_approval' => 'approved',
                    'score_display' => 'TRUE'
                );
    
                if ($this->Scm->save_data($a_score_data)) {
                    $a_rtn = array('code' => 0, 'message' => 'Success');
                }else{
                    $a_rtn = array('code' => 1, 'message' => 'Failed save data!');
                }
            }

            print json_encode($a_rtn);
        }
    }

    public function remove_subject_data()
    {
        if ($this->input->is_ajax_request()) {
            $s_score_id = $this->input->post('data_id');
            if ($this->Scm->delete_data($s_score_id)) {
                $a_return = ['code' => 0, 'message' => 'Success!'];
            }else{
                $a_return = ['code' => 1, 'message' => 'Fail remove data!'];
            }

            print json_encode($a_return);
        }
    }

    public function get_subject_curriculum()
    {
        if($this->input->is_ajax_request()){
            $s_student_id = $this->input->post('student_id');

            $mbo_student_data = $this->Stm->get_student_filtered(array('student_id' => $s_student_id))[0];
            $mbo_study_program_data = $this->Spm->get_study_program($mbo_student_data->study_program_id)[0];
            $s_study_program_id = $mbo_student_data->study_program_id;

            if (($mbo_study_program_data) AND (!is_null($mbo_study_program_data->study_program_main_id))) {
                $s_study_program_id =  $mbo_study_program_data->study_program_main_id;

                $mba_curriculum_subject_data_main_prodi = $this->Cm->get_curriculum_subject_filtered(array(
                    'rc.study_program_id' => $s_study_program_id,
                    'rcs.curriculum_subject_category' => 'research semester'
                ));

                $mba_curriculum_subject_data_prodi = $this->Cm->get_curriculum_subject_filtered(array(
                    'sp.study_program_main_id' => $s_study_program_id,
                    'rcs.curriculum_subject_category' => 'research semester'
                ));
                
                if ($mba_curriculum_subject_data_main_prodi AND $mba_curriculum_subject_data_prodi) {
                    $mba_curriculum_subject_data_lists = array_merge($mba_curriculum_subject_data_main_prodi, $mba_curriculum_subject_data_prodi);
                }else if ($mba_curriculum_subject_data_main_prodi) {
                    $mba_curriculum_subject_data_lists = $mba_curriculum_subject_data_main_prodi;
                }else {
                    $mba_curriculum_subject_data_lists = $mba_curriculum_subject_data_prodi;
                }
            }else{
                $mba_curriculum_subject_data_lists = $this->Cm->get_curriculum_subject_filtered(array(
                    'rc.study_program_id' => $s_study_program_id,
                    'rcs.curriculum_subject_category' => 'research semester'
                ));
            }

            $a_rtn = array('code' => 0, 'data' => $mba_curriculum_subject_data_lists);
            print json_encode($a_rtn);
        }
    }

    public function get_subject_student()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');

            $mba_score_student = $this->Scm->get_score_data_transcript([
                'sc.student_id' => $s_student_id,
                'curs.curriculum_subject_category' => 'research semester',
                'sc.score_approval' => 'approved'
            ]);

            print json_encode(['code' => 0, 'data' => $mba_score_student]);
        }
    }
}
