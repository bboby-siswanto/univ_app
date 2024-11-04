<?php
class Study_program extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Study_program_model', 'Spm');
    }

    public function get_study_program_instititute($b_display_partial = false, $mbs_program_id = 'all')
    {
        if ($this->input->is_ajax_request()) {
            $mbs_program_id = $this->input->post('program_id');
        }
            
        $a_filter_data = array(
            'psp.program_id' => $mbs_program_id
        );

        if($b_display_partial){
            $a_filter_data['sp.study_program_main_id'] = NULL;	
        }

        if ($mbs_program_id == 'all') {
            unset($a_filter_data['psp.program_id']);
        }

        $mbo_program_study_lists = $this->Spm->get_study_program_instititute($a_filter_data);
        if ($mbo_program_study_lists) {
            $a_rtn = array('code' => 0, 'data' => $mbo_program_study_lists);
        }else{
            $a_rtn = array('code' => 1, 'data' => false);
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($a_rtn);
        }else{
            return $mbo_program_study_lists;
        }
    }

    public function get_study_program_by_program($b_display_partial = false)
    {
        if ($this->input->is_ajax_request()) {
            $s_program_id = $this->input->post('program_id');
            
            $a_filter_data = array(
                'program_id' => $s_program_id
            );

            if($b_display_partial){
                $a_filter_data['study_program_main_id'] = NULL;	
            }

            $mbo_program_study_lists = $this->Spm->get_study_program_lists($a_filter_data);
            if ($mbo_program_study_lists) {
                $a_rtn = array('code' => 0, 'data' => $mbo_program_study_lists);
            }else{
                $a_rtn = array('code' => 1, 'data' => false);
            }
        }else{
            $a_rtn = array('code' => 1, 'message' => 'Nothing action');
        }

        print json_encode($a_rtn);
    }
}
