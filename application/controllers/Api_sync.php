<?php

class Api_sync extends Api_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('student/Student_model', 'Stm');
    }

    public function sync_data()
    {
        $this->load->model('Api_core_model','Api');
        $a_post_data = $this->a_data;
        $a_data_sync = $a_post_data['data_sync'];

        $a_return = array();

        for ($j=0; $j < count($a_data_sync); $j++) { 
            $s_table_name = $a_data_sync[$j]['table_name'];
            $a_data = $a_data_sync[$j]['data'];
            
            foreach ($a_data as $data) {
                # code...
            }
        }
    }

    public function _retrieve_data()
    {
        $a_post_data = $this->a_api_data;
        if (!empty($a_post_data['email'])) {
            $this->load->model('student/Student_model', 'Stm');
            $s_email = $a_post_data['email'];
            
            $mba_student_data = $this->Stm->get_student_filtered([
                'dpd.personal_data_email' => $s_email
            ], ['candidate', 'participant', 'cancel']);

            if ($mba_student_data) {
                // dt_personal_data, dt_academic_history, dt_address, dt_family, dt_student, dt_personal_address, ref_institution
                // personal_data
            }
            else {
                # code...
            }
        }
    }

    public function get_reference_data()
    {
        $a_post_data = $this->a_api_data;
        $s_mod = $a_post_data['module'];
        switch ($s_mod) {
            case 'feeder':
                $s_function = $a_post_data['function'];
                $s_filter_data = (isset($a_post_data['filter'])) ? $a_post_data['filter'] : false;
                // if (empty($s_filter_data)) {
                //     print('<pre>');var_dump('No filter');exit;
                //     // $result_data = modules::run('feeder/get_execute', $s_function);
                // }
                // else {
                    // print('<pre>');var_dump('ada nih');exit;
                    $result_data = modules::run('feeder/get_execute', $s_function, $s_filter_data);
                // }
                break;
            
            default:
                $result_data = 'kosong';
                break;
        }
        print('<pre>');var_dump($result_data);exit;
        $a_return = ['code' => 1, 'message' => ''];
        $this->return_json($a_return);
    }

    public function update_from_pmb()
    {
        $this->load->model('Api_core_model','api_model');
        $a_post_data = $this->a_api_data;
        $a_data_post = $a_post_data['data_api'];
        // $this->return_json($a_post_data);exit;
        $a_return_data = array();

        $this->db->trans_start();

        foreach ($a_data_post as $key => $list) {
            $s_table_name = $list['table'];
            $condition = (isset($list['condition'])) ? $list['condition'] : null;

            if ($list['table'] == 'dt_academic_history') {
                $this->load->model('personal_data/Personal_data_model', 'Pdm');
                $a_update_main_academic_history = array(
                    'academic_history_main' => 'no'
                );

                $b_avail_data = $this->api_model->check_data($list['table'], $list['condition'], $list['data']);
                $this->api_model->save_table_api($list['table'], $a_update_main_academic_history, $list['condition']);
                if ($b_avail_data) {
                    $b_save_api_data = $this->api_model->save_table_api($list['table'], $list['data'], $list['condition']);
                }else{
                    $b_save_api_data = $this->api_model->save_table_api($list['table'], $list['data']);
                }
            }else if (isset($list['condition_email'])) {
                $mbo_personal_data = $this->api_model->get_row_data($list['table'], $list['condition_email']);
                if ($mbo_personal_data) {
                    if ($mbo_personal_data->personal_data_id == $list['condition']['personal_data_id']) {
                        $b_save_api_data = $this->api_model->save_table_api($list['table'], $list['data'], $list['condition']);
                    }else{
                        $b_save_api_data = $this->api_model->save_table_api($list['table'], $list['data'], $list['condition_email']);
                    }
                }else{
                    if ($this->api_model->check_data($list['table'], $list['condition'], $list['data'])) {
                        $b_save_api_data = $this->api_model->save_table_api($list['table'], $list['data'], $list['condition']);
                    }else{
                        $b_save_api_data = $this->api_model->save_table_api($list['table'], $list['data']);
                    }
                }
            }else if (isset($list['condition'])) {
                $b_avail_data = $this->api_model->check_data($list['table'], $list['condition'], $list['data']);
                if ($b_avail_data) {
                    $b_save_api_data = $this->api_model->save_table_api($list['table'], $list['data'], $list['condition']);
                }else{
                    $b_save_api_data = $this->api_model->save_table_api($list['table'], $list['data']);
                }
            }

            if ($list['table'] == 'dt_personal_data_document') {
                $mba_document = $this->General->get_where('dt_personal_data_document', [
                    'personal_data_id' => $list['data']['personal_data_id'],
                    'document_id' => $list['data']['document_id'],
                ]);

                if ($mba_document) {
                    $s_pmb_path = '/uploads/'.$list['data']['personal_data_id'].'/';
                    $s_path2 = APPPATH.'uploads/'.$list['data']['personal_data_id'].'/';
                    // $s_path2 = $this->Stm->get_student_path(['st.personal_data_id' => $list['data']['personal_data_id']]);

                    if (file_exists($s_pmb_path.$mba_document[0]->document_requirement_link)) {
                        if(!file_exists($s_path2)){
                            mkdir($s_path2, 0777, TRUE);
                        }

                        $copy = copy($s_pmb_path.$mba_document[0]->document_requirement_link, $s_path2.$mba_document[0]->document_requirement_link);
                        // $rtn = array('code' => 3, 'message' => 'Failed transfer files!', 'error' => $copy);
                        // $this->return_json($rtn);exit;
                        if (!$copy) {
                            $rtn = array('code' => $statusCode, 'message' => 'Failed transfer files!', 'error' => $copy);
                            $this->return_json($rtn);exit;
                        }
                    }
                    else {
                        $rtn = array('code' => 3, 'message' => 'No file found!', 'error' => $s_pmb_path.$mba_document[0]->document_requirement_link);
                        $this->return_json($rtn);exit;
                    }
                }
                else {
                    $rtn = array('code' => 3, 'message' => 'Failed submiting your file document!', 'error' => $mba_document);
                    $this->return_json($rtn);exit;
                }
            }

            $a_return_data['return_of_'.$key] = array(
                'table' => $s_table_name,
                'condition' => $condition
            );
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() == FALSE) {
            $this->db->trans_rollback();
            $rtn = array('code' => 1, 'message' => 'Error', 'data' => $a_return_data);
        }else{
            $this->db->trans_commit();
            $rtn = array('code' => 0, 'message' => 'Success', 'data' => $a_return_data);
        }

        $this->return_json($rtn);
    }

    public function submit_data_result()
    {
        $a_post_data = $this->a_api_data;
        $a_result = [];
        $a_return = ['code' => 1, 'message' => NULL];
        // $this->return_json($a_post_data);
        if (!empty($a_post_data['list_data'])) {
            $mbo_student_data = false;
            foreach ($a_post_data['list_data'] as $key => $a_list_table) {
                if (count($a_list_table) > 0) {
                    foreach ($a_list_table as $s_table_name => $table_data) {
                        $a_table_data = $table_data['result'][0];
                        if (isset($table_data['key'])) {
                            $mba_data_existing = $this->General->get_where($s_table_name, $table_data['key']);
                            $mba_list_field = $this->General->list_field($s_table_name);
                            $a_data = [];
                            foreach ($mba_list_field as $s_field) {
                                if (($s_table_name == 'dt_personal_data') AND ($s_field == 'personal_data_name')) {
                                    if (isset($a_table_data['personal_data_fullname'])) {
                                        $a_data[$s_field] = $a_table_data['personal_data_fullname'];
                                    }
                                }
                                else if (isset($a_table_data[$s_field])) {
                                    $a_data[$s_field] = $a_table_data[$s_field];
                                }
                            }

                            if (count($a_data) > 0) {
                                if ($s_table_name == 'dt_personal_data_document') {
                                    $a_table_result = $table_data['result'];
                                    if ($a_table_result) {
                                        if ($mba_data_existing) {
                                            $this->General->force_delete($s_table_name, 'personal_data_id', $a_table_result[0]['personal_data_id']);
                                        }

                                        foreach ($a_table_result as $o_rowdata) {
                                            $a_datafile = [];
                                            foreach ($mba_list_field as $s_field) {
                                                if (isset($o_rowdata[$s_field])) {
                                                    $a_datafile[$s_field] = $o_rowdata[$s_field];
                                                }
                                            }
                                            $query = $this->General->insert_data($s_table_name, $a_datafile);
                                        }
                                    }
                                }
                                else if ($mba_data_existing) {
                                    # update
                                    $query = $this->General->update_data($s_table_name, $a_data, $table_data['key']);
                                    $a_result[$key][$s_table_name] = ['update' => $query, 'key' => $table_data['key']];
                                }
                                else {
                                    # insert
                                    $query = $this->General->insert_data($s_table_name, $a_data);
                                    $a_result[$key][$s_table_name] = ['insert' => $query, 'key' => $table_data['key']];
                                }
                            }

                            if (($s_table_name == 'dt_student') AND (isset($a_data['student_id']))) {
                                $mbo_student_data = $mba_data_existing[0];
                            }
                        }
                        // else if ($s_table_name == 'dt_personal_data_document') {
                        //     $a_table_data = $table_data['result'];
                        //     $o_table_data = $table_data['result'][0];

                        //     foreach ($a_table_data as $o_tabledetail) {
                        //         $mba_data_existing = $this->General->get_where($s_table_name, $table_data['key']);
                        //         $mba_list_field = $this->General->list_field($s_table_name);
                        //         $a_data = [];
                        //         foreach ($mba_list_field as $s_field) {
                        //             if (isset($o_tabledetail[$s_field])) {
                        //                 $a_data[$s_field] = $o_tabledetail[$s_field];
                        //             }
                        //         }
                        //     }
                        // }
                    }
                }
            }

            if ($mbo_student_data) {
                // $s_file_path = APPPATH.'uploads/candidate/'.$mba_userdata->student_batch.'/'.date('M', strtotime($mba_userdata->student_added)).'/'.$this->session->userdata('sess_pd').'/';
                $s_pmb_path = '/uploads/candidate/'.$mbo_student_data->academic_year_id.'/'.date('M', strtotime($mbo_student_data->date_added)).'/'.$mbo_student_data->personal_data_id.'/';
                $s_path = APPPATH.'uploads/student/'.$mbo_student_data->academic_year_id.'/'.date('M', strtotime($mbo_student_data->date_added)).'/'.$mbo_student_data->personal_data_id.'/';

                if(!file_exists($s_path)){
                    mkdir($s_path, 0777, TRUE);
                }

                if (file_exists($s_pmb_path)) {
                    $listdir = scandir($s_pmb_path);
                    foreach ($listdir as $s_filelist) {
                        if (!in_array($s_filelist, ['.', '..', '...'])) {
                            $copy = copy($s_pmb_path.$s_filelist, $s_path.$s_filelist);
                            $a_result[$s_filelist] = $copy;
                        }
                    }
                }
            }

            if (count($a_result) > 0) {
                $a_return = ['code' => 0, 'message' => 'data received!', 'result' => $a_result];
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'No data received!'];
        }
        // $a_return = $a_post_data;
        $this->return_json($a_return);
    }

    public function retrieve_candidate_data()
    {
        $a_post_data = $this->a_api_data;
        if (!empty($a_post_data['email'])) {
            $this->load->model('student/Student_model', 'Stm');
            $s_email = $a_post_data['email'];
            $query = (isset($a_post_data['query'])) ? $a_post_data['query'] : false;
            
            $mba_student_data = $this->Stm->get_student_filtered([
                'dpd.personal_data_email' => $s_email
            ], ['candidate', 'participant', 'cancel']);

            if ($mba_student_data) {
                // $a_retrieve_personal_data = ['dt_personal_data', 'dt_family', 'dt_family_member', 'dt_academic_history', 'ref_institution', 'dt_personal_address', 'dt_address'];
                // $a_retrieve_student_data = ['dt_student', 'dt_student_exchange'];
                // $a_retrieve_institution_data = ['dt_institution_contact', 'dt_personal_data'];
                // $a_retrieve_parent_data = ['dt_family_member', 'dt_personal_data'];
                // dt_personal_data, dt_academic_history, dt_address, dt_family, dt_student, dt_personal_address,
                // ref_institution, dt_family_member, dt_institution_contact, dt_student_exchange
                $a_query_result = [];
                if (($query) AND (is_array($query))) {
                    foreach ($query as $key => $s_query) {
                        $a_query_result[$key] = [
                            'query' => $s_query,
                            'result' => $this->General->execute_query($s_query)
                        ];
                    }
                }
                
                $a_return = [
                    'code' => 0,
                    'message' => 'ada niih!',
                    'query_result' => $a_query_result,
                    // 'data' => ($query) ? $this->General->execute_query($query) : ''
                ];
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Data not found!'];
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'Invalid parameter!'];
        }

        $this->return_json($a_return);
    }
}
