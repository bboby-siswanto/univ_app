<?php
class Validation_requirement extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Validation_requirement_model', 'Vrm');
        $this->load->model('validation_requirement/Assessment_model', 'Asem');
    }

    function staff_assessment_univ() {
        if (in_array($this->session->userdata('type'), ['staff', 'lect', 'lecturer'])) {
            $s_assessment_id = 'f7b4fd90-ee9a-48d5-a85b-eddab3fc9ac5';
            $mba_staff_is_lecturer = $this->General->get_where('dt_class_master_lecturer', [
                'employee_id' => $this->session->userdata('employee_id')
            ]);

            if ($mba_staff_is_lecturer) {
                $s_assessment_id = '159b7990-99d2-4e41-aaa8-a7918ba46bcb';
            }
            $b_has_submit_assessment = $this->Asem->get_assessment_result([
                'qr.personal_data_id' => $this->session->userdata('user'),
                'qr.assessment_id' => $s_assessment_id
            ]);

            if (in_array($this->session->userdata('user'), ['8837c201-6d4d-4de9-926d-8acaf9ff9b08', '41261c5c-94c7-4c5e-b4f9-4117f4567b8a', '766860ea-71e9-4997-97f0-f0b48c6a4ab1'])) {
            //     # code...
            // }
            // if ($this->session->userdata('user') == '41261c5c-94c7-4c5e-b4f9-4117f4567b8a') {
                $b_has_submit_assessment = true;
            }
            if (!$b_has_submit_assessment) {
                $a_option_name = [];
                $mba_option_list = $this->Asem->get_option_list($s_assessment_id);
                if ($mba_option_list) {
                    foreach ($mba_option_list as $o_option) {
                        $s_option_name = $o_option->option_name;
                        $s_option_name .= (!empty($o_option->option_name_eng)) ? ' (<i>'.$o_option->option_name_eng.'</i>)' : '';
                        array_push($a_option_name, $s_option_name);
                    }
                }
                $this->a_page_data['assessment_id'] = $s_assessment_id;
                $this->a_page_data['list_question'] = $this->Asem->get_question_list($s_assessment_id);
                $this->a_page_data['list_option'] = $this->Asem->get_option_list($s_assessment_id);
                $this->a_page_data['option_list_name'] = $a_option_name;
                $this->load->view('validation_requirement/campuss_assessment/assessment_modal_form', $this->a_page_data);
            }
        }
    }

    public function set_not_confirm()
    {
        $this->session->set_userdata('vaccine_covid', false);
    }

    public function validation_data($s_field)
    {
        // $this->General->get_where('ref_validation_user')
    }

    public function submit_confirmation()
    {
        $submit_validation = $this->Vrm->submit_validation($this->session->userdata('user'), 'vaccine_confirmation', 'true');
        if ($submit_validation) {
            $a_return = ['code' => 0];
            $this->session->set_userdata('vaccine_covid', true);
        }
        else {
            $a_return = ['code' => 1, 'message' => 'error proceessing data!'];
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);
        }
        else {
            return $a_return;
        }
    }
}
