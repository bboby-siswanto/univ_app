<?php
class Transfer_credit extends App_core
{
    function __construct()
    {
        parent::__construct('academic');
        $this->load->model('Score_model', 'Scm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Curriculum_model', 'Crm');
    }

    public function transfer_student($s_student_id = false)
    {
        if ($s_student_id) {
            $mbo_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $s_student_id));
            if ($mbo_student_data) {
                $this->a_page_data['o_personal_data'] = $mbo_student_data;
                $this->a_page_data['student_id'] = $s_student_id;
                $this->a_page_data['body'] = $this->load->view('score/transfer_credit', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }else{
                print('<script>alert("student status is not active")</script>');
                redirect('academic/student_academic/student_lists');
            }
        }
    }

    public function shorcut_transfer($s_student_id)
    {
        if ($s_student_id) {
            $mbo_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $s_student_id));
            if ($mbo_student_data) {
                $this->a_page_data['o_personal_data'] = $mbo_student_data;
                $this->a_page_data['student_id'] = $s_student_id;
                $this->a_page_data['body'] = $this->load->view('score/transfer_credit', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
        }
    }

    public function transfer_subject_table($s_student_id)
    {
        $this->a_page_data['btn_html'] = Modules::run('layout/generate_buttons', 'academic', 'transfer_subject');
        $this->a_page_data['student_id'] = $s_student_id;
        $this->load->view('score/table/transfer_credit_subject_table', $this->a_page_data);
    }

    public function transfer_curriculum_table($s_student_id)
    {
        $this->a_page_data['btn_html'] = Modules::run('layout/generate_buttons', 'academic', 'transfer_credit');
        $this->a_page_data['student_id'] = $s_student_id;
        $this->load->view('score/table/transfer_credit_curriculum_table', $this->a_page_data);
    }

    public function get_subject_student()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');

            $mba_score_transfer_data = $this->Scm->get_score_student($s_student_id, array('sc.semester_type_id' => 5));
            print json_encode(array('code' => 0, 'data' => $mba_score_transfer_data));
        }
    }

    public function get_subject_curriculum()
    {
        if($this->input->is_ajax_request()){
            $this->load->model('study_program/Study_program_model', 'Spm');
            $this->load->model('Curriculum_model', 'Cm');
            $s_student_id = $this->input->post('student_id');

            $mbo_student_data = $this->Stm->get_student_filtered(array('student_id' => $s_student_id))[0];
            $mbo_study_program_data = $this->Spm->get_study_program($mbo_student_data->study_program_id)[0];
            $s_study_program_id = $mbo_student_data->study_program_id;
            if (($mbo_study_program_data) AND (!is_null($mbo_study_program_data->study_program_main_id))) {
                $s_study_program_id =  $mbo_study_program_data->study_program_main_id;
                $mba_curriculum_subject_data_main_prodi = $this->Cm->get_curriculum_subject_filtered(array('rc.study_program_id' => $s_study_program_id));
                $mba_curriculum_subject_data_prodi = $this->Cm->get_curriculum_subject_filtered(array('sp.study_program_main_id' => $s_study_program_id));
                $mba_curriculum_subject_data_lists = array_merge($mba_curriculum_subject_data_main_prodi, $mba_curriculum_subject_data_prodi);
            }else{
                $mba_curriculum_subject_data_lists = $this->Cm->get_curriculum_subject_filtered(array('rc.study_program_id' => $s_study_program_id));
            }

            $a_rtn = array('code' => 0, 'data' => $mba_curriculum_subject_data_lists);
            print json_encode($a_rtn);
        }
    }

    public function remove_subject()
    {
        if($this->input->is_ajax_request()) {
            $s_score_id = $this->input->post('score_id');
            $this->Scm->delete_data($s_score_id);
            print json_encode(array('code' => 0, 'message' => 'Success'));
        }
    }

    public function save_score()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_curriculum_subject_id = $this->input->post('curriculum_subject_id');
            $this->form_validation->set_rules('transfer_subject_code', 'Origin Subject Code', 'trim');
            $this->form_validation->set_rules('transfer_subject_name', 'Origin Subject Name', 'trim|required');
            $this->form_validation->set_rules('transfer_credit', 'Origin Subject Credit', 'trim|required');
            $this->form_validation->set_rules('transfer_score', 'Score transfer', 'numeric|required|trim');
            if ($this->form_validation->run()) {
                $mba_curriculum_subject_data = $this->Crm->get_curriculum_subject_filtered(array(
                    'curriculum_subject_id' => $s_curriculum_subject_id
                ))[0];

                $s_subject_origin = (!empty(set_value('transfer_subject_code'))) ? set_value('transfer_subject_code').'|'.set_value('transfer_subject_name') : set_value('transfer_subject_name');

                $s_score_grade_point = $this->grades->get_grade_point(set_value('transfer_score'));
                // $s_ects_score = $this->grades->get_score_ects($mba_curriculum_subject_data->curriculum_subject_credit, $s_score_grade_point);
                $s_ects_score = $this->grades->get_ects_score($mba_curriculum_subject_data->curriculum_subject_credit, $mba_curriculum_subject_data->subject_name);
                $s_merit_score = $this->grades->get_merit($mba_curriculum_subject_data->curriculum_subject_credit, $s_score_grade_point);

                $a_score_data = array(
                    'student_id' => $s_student_id,
                    'curriculum_subject_id' => $s_curriculum_subject_id,
                    'semester_id' => 18,
                    'semester_type_id' => 5,
                    'original_subject' => $s_subject_origin,
                    'original_credit' => set_value('transfer_credit'),
                    'score_quiz' => set_value('transfer_score'),
                    'score_final_exam' => set_value('transfer_score'),
                    'score_sum' => set_value('transfer_score'),
                    'score_grade' => $this->grades->get_grade(set_value('transfer_score')),
                    'score_grade_point' => $s_score_grade_point,
                    'score_approval' => 'approved',
                    'score_display' => 'TRUE',
                    'score_merit' => $s_merit_score,
                    'score_ects' => $s_ects_score
                );

                if ($this->Scm->Scm->save_data($a_score_data)) {
                    $a_rtn = array('code' => 0, 'message' => 'Success');
                }else{
                    $a_rtn = array('code' => 1, 'message' => 'Failed save data!');
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_rtn);
        }
    }
}
