<?php
class Study_plan extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('academic/Score_model', 'Scm');
    }

    public function registration($s_student_id = false)
    {
        if (!$s_student_id) {
            $s_student_id = $this->session->userdata('student_id');
        }
        // var_dump($s_student_id);

        $mbo_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $s_student_id, 'ds.student_status' => 'active'))[0];
        $o_semester_active = $this->Smm->get_active_semester();
        if ($mbo_student_data) {
            $mbo_registration_data = $this->Scm->get_score_student($mbo_student_data->student_id, array(
                'sc.academic_year_id' => $o_semester_active->academic_year_id,
                'sc.semester_type_id' => $o_semester_active->semester_type_id
                // 'sc.score_approval' => 'pending'
            ));
            $this->a_page_data['valid_registration'] = modules::run('academic/semester/checker_semester_academic', 'study_plan_start_date', 'study_plan_end_date', $o_semester_active->academic_year_id, $o_semester_active->semester_type_id);
            
            // $this->a_page_data['valid_registration'] = true;
            $this->a_page_data['mbo_registration_data'] = $mbo_registration_data;
            $this->a_page_data['o_student_data'] = $mbo_student_data;
            $this->a_page_data['o_semester_active'] = $o_semester_active;
            $this->a_page_data['a_semester_selected'] = (array)$o_semester_active;
            $this->a_page_data['personal_data_id'] = $mbo_student_data->personal_data_id;
            $this->a_page_data['max_credit'] = 24;
            if (!$this->a_page_data['valid_registration']) {
                $this->a_page_data['body'] = $this->load->view('periode_over', $this->a_page_data, true);
            }else {
                $this->a_page_data['body'] = $this->load->view('studyplan/registration', $this->a_page_data, true);
            }
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_403();
        }
    }

    public function view_recomendation($s_personal_data_id)
    {
        $mbo_student_data = $this->Stm->get_student_by_personal_data_id($s_personal_data_id);
        if ($mbo_student_data) {
            $this->a_page_data['o_student_data'] = $mbo_student_data;
            $this->load->view('studyplan/table/lists_subject_recommendation');
        }
    }
}
