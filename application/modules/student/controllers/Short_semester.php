<?php
class Short_semester extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('academic/Class_group_model', 'Cgm');
    }

    public function registration($s_student_id = false)
    {
        if (!$s_student_id) {
            $s_student_id = $this->session->userdata('student_id');
        }

        $mbo_student_data = $this->Stm->get_student_filtered(array('ds.student_id' => $s_student_id, 'ds.student_status' => 'active'))[0];
        $o_semester_active = $this->Smm->get_active_semester();
        if ($mbo_student_data) {
            $i_max_credit = 9;
            $mbo_registration_data = $this->Scm->get_score_student($mbo_student_data->student_id, array(
                'sc.academic_year_id' => $o_semester_active->academic_year_id,
                'sc.semester_type_id' => ($o_semester_active->semester_type_id == 1) ? 7 : 8
            ));

            if ($mbo_registration_data) {
                
                foreach ($mbo_registration_data as $o_score) {
                    $a_lecturer = array();
                    $mba_lecturer = false;

                    if (!is_null($o_score->class_master_id)) {
                        $mba_lecturer = $this->Cgm->get_class_master_lecturer(array('class_master_id' => $o_score->class_master_id));
                    }else if(!is_null($o_score->class_group_id)){
                        $mba_lecturer = $this->Cgm->get_class_group_lecturer(array('class_group_id' => $o_score->class_group_id));
                    }

                    if ($mba_lecturer) {
                        foreach ($mba_lecturer as $o_lecturer) {
                            if (!in_array($o_lecturer->personal_data_name, $a_lecturer)) {
                                array_push($a_lecturer, $o_lecturer->personal_data_name);
                            }
                        }
                    }

                    if (count($a_lecturer) > 0) {
                        # code...
                    }
                    $o_score->lecturer_name = (count($a_lecturer) > 0) ? '<li>'.(implode('</li><li>', $a_lecturer)).'</li>': 'N/A';
                }
            }

            $this->a_page_data['valid_registration'] = modules::run('academic/semester/checker_semester_academic', 'study_plan_short_semester_start_date', 'study_plan_short_semester_end_date', $o_semester_active->academic_year_id, $o_semester_active->semester_type_id);

            if ($o_semester_active->semester_type_id == 2) {
                $mbo_sum_credit = $this->Scm->get_sum_merit_credit(array(
                    'sc.student_id' => $mbo_student_data->student_id,
                    'sc.academic_year_id' => $o_semester_active->academic_year_id,
                    'sc.semester_type_id' => 7,
                    'sc.score_approval' => 'approved',
                    'sc.score_display' => 'TRUE'
                ));

                $i_max_credit = 9 - intval($mbo_sum_credit->sum_credit);
            }
            
            $this->a_page_data['mbo_registration_data'] = $mbo_registration_data;
            $this->a_page_data['o_student_data'] = $mbo_student_data;
            $this->a_page_data['o_semester_active'] = $o_semester_active;
            $this->a_page_data['a_semester_selected'] = array(
                'academic_year_id' => $o_semester_active->academic_year_id,
                'semester_type_id' => ($o_semester_active->semester_type_id == 1) ? 7 : 8
            );
            $this->a_page_data['personal_data_id'] = $mbo_student_data->personal_data_id;
            $this->a_page_data['max_credit'] = $i_max_credit;

            if (!$this->a_page_data['valid_registration']) {
                $this->a_page_data['body'] = $this->load->view('periode_over', $this->a_page_data, true);
            }else {
                $b_check_invoice = false;
                $this->load->model('finance/Invoice_model', 'Inm');

                // $mba_invoice = $this->Inm->student_has_invoice_fee_id($mbo_student_data->personal_data_id, '04');
                // if ($mba_invoice) {
                //     foreach ($mba_invoice as $o_invoice) {
                //         $mba_sub_invoice_details = $this->Inm->get_invoice_data(array('dsi.invoice_id' => $o_invoice->invoice_id));

                //         $deadline_date = date(strtotime($mba_sub_invoice_details[count($mba_sub_invoice_details)-1]->sub_invoice_details_deadline));
                //         $now = date('Y-m-d H:i:s');
                //         if ($deadline_date >= $now) {
                //             $b_check_invoice = true;
                //             break;
                //         }
                //     }
                // }

                // if ($b_check_invoice) {
                    // $this->a_page_data['body'] = $this->load->view('short_semester/after_registration', $this->a_page_data, true);
                // }else{
                    $this->a_page_data['body'] = $this->load->view('studyplan/registration', $this->a_page_data, true);
                // }

            }

            $this->load->view('layout', $this->a_page_data);
        }
    }
}
