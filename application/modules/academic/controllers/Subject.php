<?php
class Subject extends App_core
{
    function __construct()
    {
        parent::__construct('academic');
        $this->load->model('Subject_model', 'Sbm');
        $this->load->model('study_program/Study_program_model', 'Spm');
    }

    public function subject_lists()
    {
        $this->a_page_data['body'] = $this->load->view('subject/subject', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function form_subject_filter()
    {
        $this->a_page_data['o_program_lists'] = $this->Spm->get_program_lists_select();
        $this->a_page_data['o_study_program_list'] = $this->Spm->get_study_program(false, false);
        $this->load->view('subject/form/form_filter_subject', $this->a_page_data);
    }

    public function view_table_subject()
    {
        $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'subject');
        $this->a_page_data['btn_html'] = $s_btn_html;
        $this->load->view('subject/table/subject_lists_table', $this->a_page_data);
    }

    public function form_create_subject()
    {
        $this->a_page_data['o_study_program_list'] = $this->Spm->get_study_program(false, false);
        $this->a_page_data['o_program_lists'] = $this->Spm->get_program_lists_select();
        $this->a_page_data['o_subject_type'] = $this->Sbm->get_subject_type();
        $this->a_page_data['o_subject_data'] = false;
        if ($this->input->is_ajax_request()) {
            $s_subject_id = $this->input->post('subject_id');
            $this->a_page_data['o_subject_data'] = $this->Sbm->get_subject_filtered(array('subject_id' => $s_subject_id))[0];
            $s_page = $this->load->view('subject/form/form_create_subject', $this->a_page_data, true);
            print json_encode(array('data' => $s_page));
        }else{
            $s_page = $this->load->view('subject/form/form_create_subject', $this->a_page_data, true);
            print($s_page);
        }
    }

    public function filter_subject_lists()
    {
        if ($this->input->is_ajax_request()) {
            $s_program_id = $this->input->post('program_id');
            $s_study_program_id = $this->input->post('study_program_id');
            $mbo_subject_data = $this->Sbm->get_subject_prodi(array('program_id' => $s_program_id, 'study_program_id' => $s_study_program_id));

            print json_encode(array('code' => 0, 'data' => $mbo_subject_data));
        }
    }

    public function get_subject_name()
    {
        if($this->input->is_ajax_request()){
            $s_term = $this->input->post('term');
            $join_subject = ($this->input->post('from_curriculum') !== null) ? true : false;
			
			$mba_subject_name_list = $this->Sbm->get_subject_name($s_term, $join_subject);
			$a_return = array('code' => 0, 'data' => $mba_subject_name_list);
			print json_encode($a_return);
			exit;
		}
    }

    public function delete_subject()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->Sbm->delete_subject($this->input->post('subject_id'))) {
                $rtn = array('code' => 0, 'message' => 'Success delete subject');
            }else{
                $rtn = array('code' => 0, 'message' => 'Error delete subject');
            }
        }else{
            $rtn = array('code' => 1, 'message' => 'Nothing action!');
        }

        print json_encode($rtn);
    }

    public function test_()
    {
        $a_1 = array(
            'siji' => 'satu',
            'loro' => 'dua',
            'telu' => 'tiga'
        );

        $a_2 = array(
            'loro' => 'dua',
            'papat' => 'empat',
            'limo' => 'lima',
            'genep' => 'enam'
        );

        $a_result = array_merge($a_1, $a_2);
        print('<pre>');
        var_dump($a_result);
    }

    public function save_subject_data($a_data)
    {
        if (is_object($a_data)) {
            $a_data = (array)$a_data;
        }

        $this->db->trans_start();

        $s_subject_name_id = $a_data['subject_name_id'];
        $s_subject_name_code = $this->generate_subject_name_code($a_data['subject_name']);

        $a_subject_name_data = array(
            'subject_name' => $a_data['subject_name'],
            'subject_name_code' => $this->generate_subject_name_code($a_data['subject_name']),
            'date_added' => date('Y-m-d H:i:s')
        );

        if ($s_subject_name_id == '') {
            $mbo_subject_name_data = $this->Sbm->get_subject_name_filtered(array('subject_name' => $a_data['subject_name']));
            if ($mbo_subject_name_data) {
                $s_subject_name_id = $mbo_subject_name_data[0]->subject_name_id;
                $a_subject_name_update = array('subject_name' => $a_data['subject_name']);
                $s_subject_name_code = $mbo_subject_name_data[0]->subject_name_code;
                $this->Sbm->save_subject_name($a_subject_name_update, $s_subject_name_id);
            }else {
                $s_subject_name_id = $this->uuid->v4();
                $a_subject_name_data['subject_name_id'] = $s_subject_name_id;

                $this->Sbm->save_subject_name($a_subject_name_data);
            }
        }

        $s_subject_code = $this->generate_subject_code($s_subject_name_code, $a_data['study_program_id'], $a_data['subject_credit']);
        $a_subject_data = array(
            'subject_code' => ($a_data['subject_code'] == '') ? strtoupper($s_subject_code) : strtoupper($a_data['subject_code']),
            'subject_name_id' => $s_subject_name_id,
            'program_id' => $a_data['program_id'],
            'study_program_id' => $a_data['study_program_id'],
            'id_jenis_mata_kuliah' => $a_data['id_jenis_mata_kuliah'],
            'subject_credit' => $a_data['subject_credit'],
            'subject_credit_tm' => $a_data['subject_credit']
        );

        $a_clause_validate = array(
            // 'subject_code' => $a_data['subject_code'],
            'subject_name_id' => $s_subject_name_id,
            'program_id' => $a_data['program_id'],
            'study_program_id' => $a_data['study_program_id'],
            'subject_credit' => $a_data['subject_credit']
        );

        if ($a_data['subject_id'] != '') {
            $s_subject_id = $a_data['subject_id'];
            $a_subject_data['subject_id'] = $s_subject_id;
            $a_clause_validate['subject_id'] = $s_subject_id;

            if ($mbs_validate = $this->Sbm->is_validate_subject_all($a_clause_validate)) {
                $rtn = array('code' => 1, 'message' => $mbs_validate);
                return $rtn;
            }else{
                $this->Sbm->save_subject_data($a_subject_data, $s_subject_id);
            }
        }else {
            if ($this->Sbm->get_subject_filtered(array('subject_code' => $a_data['subject_code']))) {
                $rtn = array('code' => 1, 'message' => 'Subject code has registered');
            }else {
                $a_subject_data['subject_id'] = $this->uuid->v4();
                $a_subject_data['date_added'] = date('Y-m-d H:i:s');
                
                if ($mbs_validate = $this->Sbm->is_validate_subject_all($a_clause_validate)) {
                    $rtn = array('code' => 1, 'message' => $mbs_validate);
                    return $rtn;
                }else{
                    $this->Sbm->save_subject_data($a_subject_data);
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $rtn = array('code' => 1, 'message' => "Error saving data");
        }else{
            $this->db->trans_commit();
            $rtn = array('code' => 0, 'message' => 'Success', 'data' => array_merge($a_subject_data, $a_subject_name_data));
        }

        return $rtn;
    }

    public function save_subject()
    {
        // if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        //     $a_rtn = array('code' => 1, 'message' => "System maintenance on insert new subject!, Please try again in 5 minutes");
        //     print json_encode($a_rtn);exit;
        // }

        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('subject_code','Subject Code', 'trim');
            $this->form_validation->set_rules('subject_name', 'Subject Name', 'trim|required');
            $this->form_validation->set_rules('study_program_id', 'Study Program', 'trim|required');
            $this->form_validation->set_rules('id_jenis_mata_kuliah', 'Subject Type', 'trim|required');
            $this->form_validation->set_rules('subject_credit', 'Subject Credit', 'trim|required|numeric');
            $this->form_validation->set_rules('program_id', 'Program ID', 'required');
            if ($this->form_validation->run()) {
                $a_request_data = array(
                    'subject_name_id' => $this->input->post('subject_name_id'),
                    'subject_name' => set_value('subject_name'),
                    'subject_id' => $this->input->post('subject_id'),
                    'subject_code' => set_value('subject_code'),
                    // 'subject_code' => $this->input->post('subject_code'),
                    'program_id' => set_value('program_id'),
                    'study_program_id' => set_value('study_program_id'),
                    'id_jenis_mata_kuliah' => set_value('id_jenis_mata_kuliah'),
                    'subject_credit' => set_value('subject_credit'),
                    'subject_credit_tm' => set_value('subject_credit')
                );

                $a_rtn = $this->save_subject_data($a_request_data);
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }
        }else{
            $a_rtn = array('code' => 1, 'message' => "Nothing action");
        }

        print json_encode($a_rtn);exit;
    }

    public function generate_subject_name_code($s_subject_name)
    {
        $string = str_replace(' ','-',$s_subject_name);
        $replace = preg_replace('/[^A-Za-z\-]/', '', $string);
        $replace_number = (preg_replace('/[^0-9]/', '', $string) == '') ? '0' : preg_replace('/[^0-9]/', '', $string);

        $array = explode('-', $replace);
        $a_data = array();
        foreach ($array as $a => $va) {
            if ($va!='') {
                array_push($a_data, $va);
            }
        }
        $i_z = abs(count($a_data) - 4);
        $i_loop = ((count($a_data) - 4) >= 0) ? 4 : 4 - $i_z;
        $s_code = '';

        switch (count($a_data)) {
            case 1:
                $s_code = substr($a_data[0], 0, 4);
                break;
            
            case 2:
                for ($i=0; $i < $i_loop; $i++) {
                    $s_data = $a_data[$i];
                    if ($i>0) {
                        $s_code .= substr($s_data,0,3);
                    }else{
                        $s_code .= $s_data[0];
                    }
                }
                break;

            case 3:
                for ($i=0; $i < $i_loop; $i++) {
                    $s_data = $a_data[$i];
                    if ($i==1) {
                        $s_code .= substr($s_data,0,2);
                    }else{
                        $s_code .= $s_data[0];
                    }
                }
                break;
            
            case 4:
                for ($i=0; $i < $i_loop; $i++) {
                    $s_data = $a_data[$i];
                    $s_code .= $s_data[0];
                }
                break;

            default:
                for ($i=0; $i < $i_loop; $i++) {
                    if ($i <= 1) {
                        $s_data = $a_data[$i];
                    }else if($i == 2) {
                        $s_data = $a_data[count($a_data)-2];
                    }else{
                        $s_data = $a_data[count($a_data)-1];
                    }
                    $s_code .= $s_data[0];
                }
                break;
        }
        $s_code = strtoupper($s_code);
        return $s_code.$replace_number;
    }

    public function generate_subject_code($s_subject_name_code, $s_prodi_id, $sks)
    {
        $mbo_prodi_data = $this->Spm->get_study_program($s_prodi_id, false)[0];
        $s_study_program_code = $mbo_prodi_data->study_program_code;
        $i_sks = (strlen($sks) > 1) ? $sks : '0'.$sks;

        $s_alt_code = $s_subject_name_code.'-'.$s_study_program_code.$i_sks;
        $mbo_subject_name_data = $this->Sbm->get_subject_filtered(array('subject_code' => $s_alt_code), true, 'rs.portal_id', 'DESC');
        
        if ($mbo_subject_name_data) {
            $i_subject_name_count = substr($mbo_subject_name_data[0]->subject_code, -1);
            $i_subject_name_count = intval($i_subject_name_count)+1;
        }else{
            $i_subject_name_count = '1';
        }

        return $s_subject_name_code.'-'.$s_study_program_code.$i_sks.$i_subject_name_count;
    }
}
