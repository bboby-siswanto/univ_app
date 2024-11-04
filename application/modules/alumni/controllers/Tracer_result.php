<?php
class Tracer_result extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('alumni/Alumni_model', 'Alm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('study_program/Study_program_model', 'Spm');
    }

    public function get_result_graph($s_question_id)
    {
        $mba_alumni_answer_list = $this->Alm->get_alumni_answer_lists();
        // $mba_question_list = $this->Alm->get_dikti_question(['parent_question_id' => NULL]);
        $mba_question_list = $this->Alm->get_dikti_question([
            'parent_question_id' => NULL,
            'dq.question_id' => $s_question_id
        ]);
        if (($mba_alumni_answer_list) AND ($mba_question_list)) {
            $a_result_input_question_number = [1,10,11,12];
            $a_result_choice_question_number = [2,3,4,5,8,9,13,14,15,16,17];
            $a_result_sub_question_number = [7,18,19];
            $i1 = 0;
            $i2 = 0;
            $i3 = 0;
            $i4 = 0;
            $i99 = 0;

            foreach ($mba_alumni_answer_list as $o_personal_data) {
                foreach ($mba_question_list as $o_question) {
                    $mba_have_child_question = $this->Alm->get_dikti_question(['parent_question_id' => $o_question->question_id]);
                    if (in_array($o_question->question_number, $a_result_input_question_number)) {
                        $mba_question_answer = $this->Alm->get_question_answer([
                            'dqa.question_id' => $o_question->question_id,
                            'dqa.personal_data_id' => $o_personal_data->personal_data_id,
                            'dq.question_number' => $o_question->question_number
                        ]);
                        if ($mba_question_answer) {
                            foreach ($mba_question_answer as $o_answer) {
                                if (($o_question->question_id == 'f3') OR ($o_question->question_id == 'f5')) {
                                    if ($o_answer->question_choice_value == 1) {
                                        $i1++;
                                    }
                                    else if ($o_answer->question_choice_value == 2) {
                                        if ($o_answer->answer_content == 0) {
                                            $i1++;
                                        }
                                        else if (intval($o_answer->answer_content) <= 6) {
                                            $i2++;
                                        }
                                        else if (intval($o_answer->answer_content) <= 12) {
                                            $i3++;
                                        }
                                        else {
                                            $i4++;
                                        }
                                    }
                                    else {
                                        $i1++;
                                    }
                                }
                            }
                        }
                        else {
                            $i99++;
                        }
                    }
                }
            }

            print('0 bulan'.$i1);print('<br>');
            print($i2);print('<br>');
            print($i3);print('<br>');
            print($i4);print('<br>');
            print($i99);print('<br>');
            exit;
        }
    }

    public function get_result_for_db($s_question_id = false, $return_type = 'table', $s_filterencode = false)
    {
        $a_result_answer = [];
        $result_key_x_axis = [];
        $a_result_graph = [];
        $a_filter_question = false;
        $b_is_child = false;
        if ($s_question_id) {
            $a_filter_question = ['dq.question_id' => $s_question_id];
        }

        $a_filter_alumni = [
            'ds.student_status != ' => 'resign'
        ];
        $a_prodi_id = false;
        $a_graduate_year_id = false;
        if ($s_filterencode) {
            $o_filter_alumni = json_decode(base64_decode(urldecode($s_filterencode)));
            // print('<pre>');var_dump($o_filter_alumni);exit;
            if ($o_filter_alumni->main_study_program != 'all') {
                $a_prodi_id = [$o_filter_alumni->main_study_program];
                $mba_prodi_child = $this->General->get_where('ref_study_program', ['study_program_main_id' => $o_filter_alumni->main_study_program]);
                if ($mba_prodi_child) {
                    foreach ($mba_prodi_child as $o_prodi) {
                        if (!in_array($o_prodi->study_program_id, $a_prodi_id)) {
                            array_push($a_prodi_id, $o_prodi->study_program_id);
                        }
                    }
                }
            }

            $a_filter_alumni['ds.academic_year_id'] = $o_filter_alumni->batch;
            $a_filter_alumni['ds.graduated_year_id'] = $o_filter_alumni->graduation_year;

            if (is_array($o_filter_alumni->graduation_year)) {
                unset($a_filter_alumni['ds.graduated_year_id']);
                $a_graduate_year_id = $o_filter_alumni->graduation_year;
            }
            
            foreach ($a_filter_alumni as $key => $value) {
                if ($value == 'all') {
                    unset($a_filter_alumni[$key]);
                }
            }
        }
        // print('<pre>');var_dump($a_prodi_id);exit;
        $mba_alumni_answer_list = $this->Alm->get_alumni_answer_lists($a_filter_alumni, $a_prodi_id, $a_graduate_year_id);
        // $a_normal_number_question = [1,2,3,4,5,8,9,10,11,12,13,14,15,16,17];
        $a_result_input_question_number = [1,10,11,12];
        $a_result_choice_question_number = [2,3,4,5,8,9,13,14,15,16,17];
        $a_result_sub_question_number = [7,18,19];
        if ($mba_alumni_answer_list) {
            if ($a_filter_question) {
                $a_filter_question['parent_question_id'] = NULL;
            }
            else {
                $a_filter_question = ['parent_question_id' => NULL];
            }

            if ($s_question_id) {
                $mba_question_detail = $this->General->get_where('dikti_questions', ['question_id' => $s_question_id]);
                if (($mba_question_detail) AND (!is_null($mba_question_detail[0]->parent_question_id))) {
                    $b_is_child = true;
                    unset($a_filter_question['parent_question_id']);
                }
            }
            $mba_question_list = $this->Alm->get_dikti_question($a_filter_question);
            // print("a<pre>");var_dump($mba_question_list);exit;
            foreach ($mba_alumni_answer_list as $o_alumni_answer) {
                if ($mba_question_list) {
                    foreach ($mba_question_list as $o_question) {
                        $mba_have_child_question = $this->Alm->get_dikti_question(['parent_question_id' => $o_question->question_id]);
                        $mba_question_answer = $this->Alm->get_question_answer([
                            'dqa.question_id' => $o_question->question_id,
                            'dqa.personal_data_id' => $o_alumni_answer->personal_data_id,
                            'dq.question_number' => $o_question->question_number
                        ]);
                        
                        $a_result = [
                            'student_id' => $o_alumni_answer->student_id,
                            'student_name' => $o_alumni_answer->personal_data_name,
                            'question_id' => $o_question->question_id,
                            'question_name' => $o_question->question_name,
                            'question_name_english' => $o_question->question_english_name,
                            'question_number' => $o_question->question_number,
                            'question_with_number' => $o_question->question_number.'. '.$o_question->question_name,
                            'question_english_with_number' => $o_question->question_number.'. '.$o_question->question_english_name
                        ];

                        if ((!$b_is_child) AND (in_array($o_question->question_number, $a_result_input_question_number))) {
                            if ($mba_question_answer) {
                                foreach ($mba_question_answer as $o_answer) {
                                    if (($o_question->question_id == 'f3') OR ($o_question->question_id == 'f5')) {
                                        if ($o_answer->question_choice_value == 1) {
                                            $a_result['question_answer'] = '0 Bulan';
                                        }
                                        else if ($o_answer->question_choice_value == 2) {
                                            if ($o_answer->answer_content == 0) {
                                                $a_result['question_answer'] = '0 Bulan';
                                            }
                                            else if (intval($o_answer->answer_content) <= 6) {
                                                $a_result['question_answer'] = '1-6 Bulan';
                                            }
                                            else if (intval($o_answer->answer_content) <= 12) {
                                                $a_result['question_answer'] = '7-12 Bulan';
                                            }
                                            else {
                                                $a_result['question_answer'] = '> 12 Bulan';
                                            }
                                        }
                                        else {
                                            $a_result['question_answer'] = '0 Bulan';
                                        }
                                    }
                                    else if (($o_question->question_id == 'f6') OR ($o_question->question_id == 'f7') OR ($o_question->question_id == 'f7a')) {
                                        if (intval($o_answer->answer_content) == 0) {
                                            $a_result['question_answer'] = '0 Perusahaan';
                                        }
                                        else if (intval($o_answer->answer_content) == 1) {
                                            $a_result['question_answer'] = '1 Perusahaan';
                                        }
                                        else if (intval($o_answer->answer_content) <= 5) {
                                            $a_result['question_answer'] = '2-5 Perusahaan';
                                        }
                                        else if (intval($o_answer->answer_content) <= 10) {
                                            $a_result['question_answer'] = '6-10 Perusahaan';
                                        }
                                        else if (intval($o_answer->answer_content) <= 20) {
                                            $a_result['question_answer'] = '11-20 Perusahaan';
                                        }
                                        else if (intval($o_answer->answer_content) > 20) {
                                            $a_result['question_answer'] = '>20 Perusahaan';
                                        }
                                        else {
                                            $a_result['question_answer'] = 'Tidak Menjawab';
                                        }
                                    }
                                    else {
                                        $a_result['question_answer'] = 'Tidak Menjawab';
                                    }
                                }
                            }
                            else {
                                $a_result['question_answer'] = 'Tidak Menjawab';
                            }

                            array_push($a_result_answer, $a_result);
                            if (!in_array($a_result['question_answer'], $result_key_x_axis)) {
                                array_push($result_key_x_axis, $a_result['question_answer']);
                                $s_key = $a_result['question_answer'];
                                $a_result_graph[$s_key] = [
                                    'result' => 1,
                                    'key_option' => $s_key
                                ];
                            }
                            else {
                                $s_key = $a_result['question_answer'];
                                $d_counter = $a_result_graph[$s_key]['result'] + 1;
                                $a_result_graph[$s_key]['result'] = $d_counter;
                            }
                        }
                        else if ((!$b_is_child) AND (in_array($o_question->question_number, $a_result_choice_question_number))) {
                            if ($o_question->is_multiple == '0') {
                                $mba_question_choice_ = $this->General->get_where('dikti_question_choices', ['question_id' => $o_question->question_id]);
                                if ($mba_question_choice_) {
                                    foreach ($mba_question_choice_ as $s_choice) {
                                        if (!in_array($s_choice->question_choice_name, $result_key_x_axis)) {
                                            array_push($result_key_x_axis, $s_choice->question_choice_name);
                                            $s_key = $s_choice->question_choice_name;
                                            $a_result_graph[$s_key] = [
                                                'result' => 0,
                                                'key_option' => $s_key
                                            ];
                                        }
                                    }
                                }
                            }

                            if ($mba_question_answer) {
                                $result_array = [];
                                foreach ($mba_question_answer as $o_answer) {
                                    // $s_answer_content = $o_answer->question_choice_name;
                                    if (is_null($o_answer->answer_content)) {
                                        array_push($result_array, $o_answer->question_choice_name);
                                    }
                                    else {
                                        array_push($result_array, $o_answer->question_choice_name.' ('.$o_answer->answer_content.')');
                                    }
                                }
                                $a_result['question_answer'] = implode('; ', $result_array);
                            }
                            else {
                                $a_result['question_answer'] = 'Tidak Menjawab';
                            }
                            
                            array_push($a_result_answer, $a_result);
                            if (!in_array($a_result['question_answer'], $result_key_x_axis)) {
                                array_push($result_key_x_axis, $a_result['question_answer']);
                                $s_key = $a_result['question_answer'];
                                $a_result_graph[$s_key] = [
                                    'result' => 1,
                                    'key_option' => $s_key
                                ];
                            }
                            else {
                                $s_key = $a_result['question_answer'];
                                $d_counter = $a_result_graph[$s_key]['result'] + 1;
                                $a_result_graph[$s_key]['result'] = $d_counter;
                            }
                        }
                        else if ($o_question->question_id == 'f13') {
                            $d_total_sallary = 0;
                            $b_have_answer = false;
                            foreach ($mba_have_child_question as $o_question_child) {
                                $mba_question_child_answer = $this->Alm->get_question_answer([
                                    'dqa.question_id' => $o_question_child->question_id,
                                    'dqa.personal_data_id' => $o_alumni_answer->personal_data_id,
                                    'dq.question_number' => $o_question_child->question_number
                                ]);

                                if ($mba_question_child_answer) {
                                    $b_have_answer = true;
                                    foreach ($mba_question_child_answer as $o_answer_child) {
                                        $s_answer_content = $o_answer_child->answer_content;
                                        $d_total_sallary += $s_answer_content;
                                    }
                                }
                            }

                            // if ((!$b_have_answer) OR ($d_total_sallary == 0)) {
                            //     $a_result['question_answer'] = '.Tidak Menjawab';
                            // }
                            // else 
                            if ((doubleval($d_total_sallary) > 0) AND (doubleval($d_total_sallary) < 5000000)) {
                                $a_result['question_answer'] = '.dibawah Rp. 5.000.000';
                            }
                            else if (doubleval($d_total_sallary) < 7000000) {
                                $a_result['question_answer'] = 'Rp. 5.000.000 s/d  Rp. 6.999.999';
                            }
                            else if (doubleval($d_total_sallary) < 10000000) {
                                $a_result['question_answer'] = 'Rp. 7.000.000 s/d Rp. 9.999.999';
                            }
                            else if (doubleval($d_total_sallary) <= 20000000) {
                                $a_result['question_answer'] = 'Rp. 10.000.000 s/d Rp. 20.000.000';
                            }
                            else if (doubleval($d_total_sallary) > 20000000) {
                                $a_result['question_answer'] = 'diatas > Rp. 20.000.000';
                            }
                            else {
                                $a_result['question_answer'] = 'Tidak Menjawab';
                            }
                            array_push($a_result_answer, $a_result);
                            if (!in_array($a_result['question_answer'], $result_key_x_axis)) {
                                array_push($result_key_x_axis, $a_result['question_answer']);
                                $s_key = $a_result['question_answer'];
                                $a_result_graph[$s_key] = [
                                    'result' => 1,
                                    'key_option' => $s_key
                                ];
                            }
                            else {
                                $s_key = $a_result['question_answer'];
                                $d_counter = $a_result_graph[$s_key]['result'] + 1;
                                $a_result_graph[$s_key]['result'] = $d_counter;
                            }
                        }
                        else if ($mba_have_child_question) {
                            foreach ($mba_have_child_question as $o_question_child) {
                                $a_result = [
                                    'student_id' => $o_alumni_answer->student_id,
                                    'question_id' => $o_question_child->question_id,
                                    'question_name' => $o_question->question_name.' / '.$o_question_child->question_name,
                                    'question_name_english' => $o_question->question_english_name.' / '.$o_question_child->question_english_name,
                                    'question_number' => $o_question->question_number.'.'.$o_question_child->question_number,
                                    'question_with_number' => $o_question->question_number.'. '.$o_question->question_name.' / '.$o_question_child->question_number.'. '.$o_question_child->question_name,
                                    'question_english_with_number' => $o_question->question_number.'. '.$o_question->question_english_name.' / '.$o_question_child->question_number.'. '.$o_question_child->question_english_name,
                                    'is_sub_question' => true
                                ];
                                
                                $mba_question_child_answer = $this->Alm->get_question_answer([
                                    'dqa.question_id' => $o_question_child->question_id,
                                    'dqa.personal_data_id' => $o_alumni_answer->personal_data_id,
                                    'dq.question_number' => $o_question_child->question_number
                                ]);
                                if ($mba_question_child_answer) {
                                    // $a_result['question_answer'] = 'Tidak Menjawab';
                                    foreach ($mba_question_child_answer as $o_answer_child) {
                                        if (!is_null($o_answer_child->question_choice_value)) {
                                            $a_result['question_answer'] = $o_answer_child->question_choice_name;
                                        }
                                        else {
                                            $a_result['question_answer'] = $o_answer_child->question_choice_name.' ('.$o_answer_child->answer_content.')';
                                        }
                                    }
                                }
                                else {
                                    $a_result['question_answer'] = 'Tidak Menjawab';
                                }

                                array_push($a_result_answer, $a_result);
                                if (!in_array($a_result['question_answer'], $result_key_x_axis)) {
                                    array_push($result_key_x_axis, $a_result['question_answer']);
                                    $s_key = $a_result['question_answer'];
                                    $a_result_graph[$s_key] = [
                                        'result' => 1,
                                        'key_option' => $s_key
                                    ];
                                }
                                else {
                                    $s_key = $a_result['question_answer'];
                                    $d_counter = $a_result_graph[$s_key]['result'] + 1;
                                    $a_result_graph[$s_key]['result'] = $d_counter;
                                }
                            }
                        }
                        else if ($b_is_child) {
                            // print('betul');exit;
                            if ($mba_question_answer) {
                                $result_array = [];
                                foreach ($mba_question_answer as $o_answer) {
                                    if (!is_null($o_answer->question_choice_value)) {
                                        array_push($result_array, $o_answer->question_choice_name);
                                    }
                                    else {
                                        array_push($result_array, $o_answer->question_choice_name.' ('.$o_answer->answer_content.')');
                                    }
                                }
                                $a_result['question_answer'] = implode('; ', $result_array);
                            }
                            else {
                                $a_result['question_answer'] = 'Tidak Menjawab';
                            }
                            array_push($a_result_answer, $a_result);
                            if (!in_array($a_result['question_answer'], $result_key_x_axis)) {
                                array_push($result_key_x_axis, $a_result['question_answer']);
                                $s_key = $a_result['question_answer'];
                                $a_result_graph[$s_key] = [
                                    'result' => 1,
                                    'key_option' => $s_key
                                ];
                            }
                            else {
                                $s_key = $a_result['question_answer'];
                                $d_counter = $a_result_graph[$s_key]['result'] + 1;
                                $a_result_graph[$s_key]['result'] = $d_counter;
                            }
                        }
                        else {
                            $a_result['question_answer'] = 'Tidak Menjawab';
                            array_push($a_result_answer, $a_result);
                            if (!in_array($a_result['question_answer'], $result_key_x_axis)) {
                                array_push($result_key_x_axis, $a_result['question_answer']);
                                $s_key = $a_result['question_answer'];
                                $a_result_graph[$s_key] = [
                                    'result' => 1,
                                    'key_option' => $s_key
                                ];
                            }
                            else {
                                $s_key = $a_result['question_answer'];
                                $d_counter = $a_result_graph[$s_key]['result'] + 1;
                                $a_result_graph[$s_key]['result'] = $d_counter;
                            }
                        }
                    }
                }
            }
        }

        if ($return_type == 'table') {
            print('<table border="1">');
            print('<tr>');
            print('<td>student_id</td>');
            print('<td>student_name</td>');
            print('<td>question_id</td>');
            print('<td>question_name</td>');
            print('<td>question_name_english</td>');
            print('<td>question_number</td>');
            print('<td>question_with_number</td>');
            print('<td>question_english_with_number</td>');
            print('<td>question_answer</td>');
            print('</tr>');
            if (count($a_result_answer) > 0) {
                foreach ($a_result_answer as $a_answer) {
                    print('<tr>');
                    print('<td>'.$a_answer['student_id'].'</td>');
                    print('<td>'.$a_answer['student_name'].'</td>');
                    print('<td>'.$a_answer['question_id'].'</td>');
                    print('<td>'.$a_answer['question_name'].'</td>');
                    print('<td>'.$a_answer['question_name_english'].'</td>');
                    print('<td>'.$a_answer['question_number'].'</td>');
                    print('<td>'.$a_answer['question_with_number'].'</td>');
                    print('<td>'.$a_answer['question_english_with_number'].'</td>');
                    print('<td>'.$a_answer['question_answer'].'</td>');
                    print('</tr>');
                }
            }
            print('</table>');
        }
        else if ($return_type == 'raw_data') {
            print('<pre>');var_dump($a_result_answer);exit;
        }
        else if (($return_type == 'graph_data') AND ($s_question_id)) {
            // harus per pertanyaan, biar looping ga kebanyakan.
            // $a_data = [];
            // foreach ($result_key_x_axis as $key => $s_answer) {
            //     if (count($a_result_answer) > 0) {
            //         foreach ($a_result_answer as $a_answer) {
            //             if ($a_answer['question_answer'] == $s_answer) {
            //                 $a_data[$key] = 
            //             }
            //         }
            //     }
            // }
            // masih gagal
            // $a_return = [
            //     'option' => $result_key_x_axis
            // ];
            // print('<pre>');
            // var_dump($result_key_x_axis);
            ksort($a_result_graph);
            $a_result_graph = array_values($a_result_graph);
            // var_dump($a_result_graph);exit;
            return $a_result_graph;
        }
        
    }

    public function get_result_question($s_question_id)
    {
        $result_db = $this->Alm->get_alumni_answer_lists(['question_id' => $s_question_id]);
        if (($s_question_id == 'f3') OR ($s_question_id == 'f5')) {
            $key = ['a', 'b', 'c', 'd'];
            $key_desc = [
                'a' => '0 Bulan',
                'b' => '1-6 Bulan',
                'c' => '7- 12 Bulan',
                'd' => '> 12'
            ];
            if ($result_db) {
                foreach ($result_db as $key => $o_result) {
                    // 
                }
            }
        }
    }
}
