<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;	

class Ofse extends App_core
{
    function __construct()
    {
        parent::__construct('academic');
        $this->load->model('academic/Ofse_model', 'Ofm');
        $this->load->model('academic/Class_group_model','Cgm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('academic/Curriculum_model', 'Cm');
        $this->load->model('academic/Offered_subject_model', 'Osm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('institution/Institution_model', 'Inm');
        $this->load->model('thesis/Thesis_model', 'Tm');
    }

    public function submit_shcedule()
    {
        if ($this->input->is_ajax_request()) {
            $s_score_id = $this->input->post('sc_score_id');
            $s_room = $this->input->post('sc_room');
            $s_date = $this->input->post('sc_date');
            $s_time_start = $this->input->post('sc_time');
            $s_time_end = intval($s_time_start) + 1;
            $s_time_start = (strlen($s_time_start) == 1) ? str_pad($s_time_start, 2, "0", STR_PAD_LEFT).':00' : $s_time_start.':00';
            $s_time_end = (strlen($s_time_end) == 1) ? str_pad($s_time_end, 2, "0", STR_PAD_LEFT).':00' : $s_time_end.':00';

            $this->db->trans_begin();
            $mba_exam_avail = $this->General->get_where('dt_ofse_exam', [
                'score_id' => $s_score_id
            ]);
            
            $a_exam_data = [
                'score_id' => $s_score_id,
                'exam_room' => $s_room,
                'exam_date' => $s_date,
                'exam_time_start' => $s_time_start,
                'exam_time_end' => $s_time_end,
                'date_added' => date('Y-m-d H:i:s')
            ];

            if ($mba_exam_avail) {
                $this->Ofm->submit_schedule($a_exam_data, ['ofse_exam_id' => $mba_exam_avail[0]->ofse_exam_id]);
            }
            else {
                $a_exam_data['ofse_exam_id'] = $this->uuid->v4();
                $this->Ofm->submit_schedule($a_exam_data);
            }

            if ($this->db->trans_status() === TRUE) {
                $a_return = ['code' => 0, 'message' => 'Success!'];
                $this->db->trans_commit();
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Error Processing data!'];
                $this->db->trans_rollback();
            }

            print json_encode($a_return);
        }
    }

    public function examiner()
    {
        $mba_ofse_data = $this->General->get_where('dt_ofse', [
            'ofse_status' => 'active'
        ]);
        if ($this->session->userdata('user') == '41261c5c-94c7-4c5e-b4f9-4117f4567b8a') {
            $mba_ofse_data = $this->General->get_where('dt_ofse'); 
        }
        if ($mba_ofse_data) {
            $mba_ofse_data = $mba_ofse_data[0];
            $this->a_page_data['ofse_data'] = $mba_ofse_data;
            $this->a_page_data['body'] = $this->load->view('ofse/ofse_structure', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function lock_form_evaluation()
    {
        if ($this->input->is_ajax_request()) {
            $s_subject_question_id = $this->input->post('fq_subject_question_id');
            $s_score_id = $this->input->post('fq_score_id');
            $s_personal_data_id = $this->session->userdata('user');

            
            $mbo_is_examiner = $this->check_examiner($s_score_id);
            if ($mbo_is_examiner) {
                $mba_update_lock_data = [
                    'examiner_attendance' => 'PRESENT',
                    'examiner_lock_evaluation' => 'true'
                ];

                $this->Ofm->submit_ofse_examiner($mba_update_lock_data, [
                    'student_examiner_id' => $mbo_is_examiner->student_examiner_id
                ]);
                $a_return = ['code' => 0, 'message' => 'Success'];
            }
            else {
                $a_return = ['code' => 2, 'message' => 'You dont have access to insert score!'];
            }

            print json_encode($a_return);
        }
    }

    public function submit_evaluation()
    {
        if ($this->input->is_ajax_request()) {
            $s_subject_question_id = $this->input->post('fq_subject_question_id');
            $s_score_id = $this->input->post('fq_score_id');
            $s_personal_data_id = $this->session->userdata('user');

            $mba_is_examiner = $this->Ofm->get_student_ofse_examiner($s_score_id, [
                'ta.personal_data_id' => $s_personal_data_id,
                'sc.score_id' => $s_score_id
            ]);

            if ($mba_is_examiner) {
                $o_examiner = $mba_is_examiner[0];
                $a_score_value = $this->input->post('ofse_input_score');
                $a_score_comment = $this->input->post('ofse_input_comment');
                $a_subject_number_question = $this->input->post('subject_number_question');

                $this->db->trans_begin();
                $this->Ofm->destroy_score_evaluation($o_examiner->student_examiner_id, $s_subject_question_id);
                if ((is_array($a_score_value)) AND (count($a_score_value) > 0)) {
                    $mba_subject_question_data = $this->General->get_where('dt_ofse_subject_question', [
                        'subject_question_id' => $s_subject_question_id
                    ]);

                    $i_sequence = 1;
                    $a_score_data = [];
                    foreach ($a_score_value as $key => $score) {
                        if (!empty($a_score_value[$key])) {
                            array_push($a_score_data, $score);
                            $a_score_evaluation_data = [
                                'score_evaluation_id' => $this->uuid->v4(),
                                'student_examiner_id' => $o_examiner->student_examiner_id,
                                'subject_question_id' => $s_subject_question_id,
                                'score_sequence' => $a_subject_number_question[$key],
                                'score' => $score,
                                'comment' => $a_score_comment[$key],
                                'date_added' => date('Y-m-d H:i:s')
                            ];
                            $i_sequence++;

                            $this->Ofm->submit_score_evaluation($a_score_evaluation_data);
                            $this->Ofm->submit_file([
                                'has_pick' => 'true'
                            ], [
                                'subject_question_id' => $s_subject_question_id
                            ]);
                        }
                    }

                    $d_total_score = array_sum($a_score_data);
                    $d_average_score = (intval($mba_subject_question_data[0]->subject_number_question) == 0) ? 0 : $d_total_score / intval($mba_subject_question_data[0]->subject_number_question);

                    $mba_update_lock_data = [
                        'examiner_attendance' => 'PRESENT',
                        'examiner_score' => $d_average_score
                    ];
    
                    $this->Ofm->submit_ofse_examiner($mba_update_lock_data, [
                        'student_examiner_id' => $o_examiner->student_examiner_id
                    ]);
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $a_return = ['code' => 1, 'message' => 'Error processing data!'];
                }
                else {
                    $this->db->trans_commit();
                    $a_return = ['code' => 0, 'message' => 'Success!'];
                }
            }
            else {
                $a_return = ['code' => 2, 'message' => 'You dont have access to insert score!'];
            }

            print json_encode($a_return);
        }
    }

    public function publish_all_ofse_score($s_ofse_period_id)
    {
        $mba_score_data = $this->Scm->get_score_data([
            'sc.ofse_period_id' => $s_ofse_period_id,
            'sc.semester_id' => '17',
            'sc.score_approval' => 'approved'
        ]);

        $mba_ofse_period_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id]);

        if (($mba_score_data) AND ($mba_ofse_period_data)) {
            $o_ofse_data = $mba_ofse_period_data[0];
            $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

            $s_file_name = 'Result_Publish_Score_Ofse_'.date('Y-m-d');
            $s_filename = $s_file_name.'.xlsx';
            $s_file_path = APPPATH."uploads/academic/ofse/".str_replace(' ', '-', $o_ofse_data->ofse_period_name)."/publish_score/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_sheet = $o_spreadsheet->getActiveSheet()->setTitle("Score List");
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Academic Services")
                ->setCategory("OFSE");

            $o_sheet->setCellValue('A1', "Student Name");
            $o_sheet->setCellValue('B1', "Subject");
            $o_sheet->setCellValue('C1', "Final Score");
            $o_sheet->setCellValue('D1', "Score Examiner");
            $o_sheet->setCellValue('E1', "Status");
            $o_sheet->setCellValue('F1', "Message");
            
            $i_row = 2;
            foreach ($mba_score_data as $o_score) {
                $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $o_score->student_id]);
                $o_sheet->setCellValue('A'.$i_row, $mba_student_data[0]->personal_data_name);
                $o_sheet->setCellValue('B'.$i_row, $o_score->subject_name);

                $result_publish = $this->_publish_ofse_score($o_score->score_id);
                if ($result_publish['code'] == 0) {
                    $result_data = $result_publish['data'];
                    $o_sheet->setCellValue('C'.$i_row, $result_data['score_sum']);
                    $o_sheet->setCellValue('D'.$i_row, $result_data['score_examiner']);
                    $o_sheet->setCellValue('E'.$i_row, "Success");
                    $o_sheet->setCellValue('F'.$i_row, "Success");
                }
                else {
                    $o_sheet->setCellValue('E'.$i_row, "Fail");
                    $o_sheet->setCellValue('F'.$i_row, $result_publish['message']);
                }
                $i_row++;
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $a_path_info = pathinfo($s_file_path.$s_filename);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.$s_filename);
			readfile( $s_file_path.$s_filename);
            exit;
        }
        else {
            $a_return = ['code' => 1, 'message' => 'No score data found!'];
            print json_encode($a_return);
        }
    }
    
    public function publish_score()
    {
        if ($this->input->is_ajax_request()) {
            $s_score_id = $this->input->post('score_id');
            $a_return = $this->_publish_ofse_score($s_score_id);

            print json_encode($a_return);
        }
    }

    private function _publish_ofse_score($s_score_id)
    {
        $mba_score_examiner = $this->Ofm->get_student_ofse_examiner($s_score_id);
        if ($mba_score_examiner) {
            $a_score_examiner = [];
            $a_score = [];
            $b_all_sign = true;
            foreach ($mba_score_examiner as $o_examiner) {
                $a_score_examiner[$o_examiner->examiner_type] = $o_examiner->examiner_score;
                if (!is_null($o_examiner->examiner_score)) {
                    array_push($a_score, $o_examiner->examiner_score);
                }
                $b_all_sign = ($o_examiner->examiner_lock_evaluation == 'true') ? true : false;
            }

            $i_score_sum = array_sum($a_score);
            if (count($a_score) > 0) {
                $f_ofse_score = $i_score_sum / count($a_score);
                $a_score_data = [
                    'score_quiz' => $f_ofse_score,
                    'score_final_exam' => $f_ofse_score,
                    'score_sum' => $f_ofse_score,
                    'score_grade' => $this->grades->get_grade($f_ofse_score),
                    'score_approval' => 'approved',
                    'score_examiner' => json_encode($a_score_examiner)
                ];

                $this->Scm->save_data($a_score_data, array('score_id' => $s_score_id));
                $a_return = ['code' => 0, 'message' => 'Success', 'data' => $a_score_data];
            }
            else if (!$b_all_sign) {
                $a_return = ['code' => 2, 'message' => 'one of the examiners not sign!'];
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Empty score!'];
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'Examiner not found!'];
        }

        return $a_return;
    }

    public function result_score($s_score_id)
    {
        $mba_ofse_exam_data = $this->Ofm->get_ofse_exam_data(['oe.score_id' => $s_score_id]);
        if ($mba_ofse_exam_data) {
            $mba_ofse_subject_data = $this->Ofm->get_ofse_list_subject([
                'sc.score_id' => $s_score_id
            ]);
            $mba_student_examiner = $this->Ofm->get_student_ofse_examiner($s_score_id);
            $a_subject_question_id = [];
            if ($mba_student_examiner) {
                foreach ($mba_student_examiner as $key => $o_examiner) {
                    $mba_employee = $this->General->get_where('dt_employee', ['personal_data_id' => $o_examiner->personal_data_id]);
                    $o_examiner->sign_email = ($mba_employee) ? $mba_employee[0]->employee_email : $o_examiner->personal_data_email;
                    $o_examiner->examiner_total_score = $o_examiner->examiner_score;
                    $s_examiner_name = $this->Pdm->retrieve_title($o_examiner->personal_data_id);
                    $mba_examiner_score = $this->Ofm->get_score_evaluation([
                        'oe.student_examiner_id' => $o_examiner->student_examiner_id
                    ]);
                    // print('<pre>');var_dump($mba_examiner_score);

                    if ($mba_examiner_score) {
                        if (!in_array($mba_examiner_score[0]->subject_question_id, $a_subject_question_id)) {
                            array_push($a_subject_question_id, $mba_examiner_score[0]->subject_question_id);
                        }
                    }
                    $o_examiner->examiner_name = $s_examiner_name;
                    $o_examiner->examiner_score = $mba_examiner_score;
                }
            }

            // $mba_subject_question_data = false;
            // if (count($a_subject_question_id) >= 1) {
            //     $mba_subject_question_data = $this->Ofm->get_subject_question_multi($a_subject_question_id);
            //     if ($mba_subject_question_data) {
            //         foreach ($mba_subject_question_data as $o_subject_question) {
            //             for ($i=1; $i <= $o_subject_question->subject_number_question ; $i++) {
            //                 foreach ($mba_student_examiner as $key => $o_examiner) {
            //                     $mba_examiner_score = $this->Ofm->get_score_evaluation([
            //                         'oe.student_examiner_id' => $o_examiner->student_examiner_id,
            //                     ]);
            //                     $o_examiner->examiner_score = 
            //                 }
            //             }
            //         }
            //     }
            // }
            // print('<pre>');var_dump($a_subject_question_id);exit;

            $this->a_page_data['ofse_examiner'] = $mba_student_examiner;
            $this->a_page_data['ofse_subject'] = $mba_ofse_subject_data[0];
            $this->a_page_data['score_id'] = $s_score_id;
            // $this->a_page_data['ofse_subject_question'] = $mba_subject_question_data;
            $this->a_page_data['ofse_data'] = $mba_ofse_exam_data;
            $this->a_page_data['body'] = $this->load->view('ofse/ofse_result_score', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    public function form_evaluation($s_score_id, $s_subject_question_id, $s_ofse_subject_code)
    {
        $mba_ofse_exam_data = $this->Ofm->get_ofse_exam_data(['oe.score_id' => $s_score_id]);
        $mba_ofse_subject_data = $this->Ofm->get_ofse_subject_question(['oq.subject_question_id' => $s_subject_question_id]);

        if (($mba_ofse_exam_data) AND ($mba_ofse_subject_data)) {
            $o_is_examiner = $this->check_examiner($s_score_id);
            $mba_evaluation_data = false;
            if ($o_is_examiner) {
                $mba_evaluation_data = $this->Ofm->get_score_evaluation([
                    'oe.student_examiner_id' => $o_is_examiner->student_examiner_id,
                    'oe.subject_question_id' => $s_subject_question_id
                ]);
            }

            $this->a_page_data['examiner_data'] = $o_is_examiner;
            $this->a_page_data['ofse_data'] = $mba_ofse_exam_data;
            $this->a_page_data['ofse_score_data'] = $mba_evaluation_data;
            $this->a_page_data['subject_data'] = $mba_ofse_subject_data;
            $this->a_page_data['body'] = $this->load->view('ofse/form/form_ofse_evaluation', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            print('<script>alert("Exam data not found!");window.location.href = "'.base_url().'academic/ofse/examiner";</script>');
        }
    }

    public function view_question($s_ofse_period_id, $s_ofse_subject_code)
    {
        $mba_ofse_question = $this->Ofm->get_ofse_subject_question([
            'oq.ofse_subject_code' => $s_ofse_subject_code,
            'oq.ofse_period_id' => $s_ofse_period_id
        ]);
        $s_path = false;
        if ($mba_ofse_question) {
            $s_path = base_url().'student/ofse/view/'.$mba_ofse_question[0]->subject_question_id.'/'.$s_ofse_subject_code;
        }
        $this->a_page_data['link_question'] = $s_path;
        $this->a_page_data['body'] = $this->load->view('ofse/exam_question', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function get_ofse_subject_question()
    {
        if ($this->input->is_ajax_request()) {
            $s_ofse_period_id = $this->input->post('ofse_period_id');
            $s_ofse_subject_code = $this->input->post('ofse_subject_code');
            $s_ofse_score_id = $this->input->post('ofse_score_id');
            $mba_question_data = $this->Ofm->get_ofse_subject_question([
                'oq.ofse_period_id' => $s_ofse_period_id,
                'oq.ofse_subject_code' => $s_ofse_subject_code
            ]);
            $b_allow = true;
            $s_login_type = $this->session->userdata('type');
            if ($s_login_type == 'student') {
                $mba_ofse_exam = $this->General->get_where('dt_ofse_exam', ['score_id' => $s_ofse_score_id]);
                if ($mba_ofse_exam) {
                    $o_exam = $mba_ofse_exam[0];
                    $dtexam_start = $o_exam->exam_date.' '.$o_exam->exam_time_start;
                    $dtexam_end = $o_exam->exam_date.' '.$o_exam->exam_time_end;

                    $now = date('Y-m-d H:i:s');
                    $exam_date = date('Y-m-d H:i:s', strtotime($dtexam_start." -10 minutes "));
                    $exam_date_end = date('Y-m-d H:i:s', strtotime($dtexam_end));
                    // print($exam_date);exit;
                    // if (($now < $exam_date) AND ($now > $exam_date_end)) {
                    if ($now < $exam_date) {
                        $b_allow = false;
                    }
                    else if ($now > $exam_date_end) {
                        $b_allow = false;
                    }
                }
            }

            if (!$b_allow) {
                $mba_question_data = false;
            }

            if ($mba_question_data) {
                foreach ($mba_question_data as $o_varian_subject) {
                    $b_is_fill_eval = false;
                    if (!empty($s_ofse_score_id)) {
                        $mba_is_fill_eval = $this->Ofm->get_evaluation_examiner([
                            'om.score_id' => $s_ofse_score_id,
                            'oe.subject_question_id' => $o_varian_subject->subject_question_id,
                            // 'oq.has_pick' => 'true'
                        ]);
                        $b_is_fill_eval = ($mba_is_fill_eval) ? true : false;
                    }
                    $o_varian_subject->is_pick_student = $b_is_fill_eval;
                }
            }

            print json_encode(['data' => $mba_question_data]);
        }
    }

    public function submit_ofse_question()
    {
        if ($this->input->is_ajax_request()) {
            $post = $this->input->post();
            $files = $_FILES;
            $i_saved_file = 0;
            $a_error_list = [];
            
            $s_ofse_period_id = $post['ofse_period_id'];
            $s_ofse_subject_code = $post['ofse_subject_code'];
            $s_subject_id = $post['ofse_subject_id'];
            $i_total_question = $post['question_number'];
            $mba_ofse_data = $this->General->get_where('dt_ofse', [
                'ofse_period_id' => $s_ofse_period_id
            ]);

            if ($mba_ofse_data) {
                $o_ofse_data = $mba_ofse_data[0];
                if (is_array($post['number'])) {
                    $s_ofse_name = str_replace(' ', '-', $o_ofse_data->ofse_period_name);
                    $s_file_path = APPPATH.'uploads/academic/ofse/'.$s_ofse_name.'/question_list/'.$s_ofse_subject_code.'/';

                    if(!file_exists($s_file_path)){
						mkdir($s_file_path, 0755, true);
					}

                    $config['allowed_types'] = 'pdf';
                    $config['max_size'] = 52400;
                    $config['file_ext_tolower'] = true;
                    $config['overwrite'] = true;
                    $config['upload_path'] = $s_file_path;
                    $this->load->library('upload', $config);
                    
                    foreach ($post['number'] as $key => $value) {
                        if (!empty($_FILES['question_file']['name'][$key])) {
                            $s_fname = $s_ofse_name.'_'.$s_ofse_subject_code.'_'.($key + 1);
                            $config['file_name'] = $s_fname;

                            $_FILES['filedata']['name'] = $_FILES['question_file']['name'][$key];
                            $_FILES['filedata']['type'] = $_FILES['question_file']['type'][$key];
                            $_FILES['filedata']['tmp_name'] = $_FILES['question_file']['tmp_name'][$key];
                            $_FILES['filedata']['error'] = $_FILES['question_file']['error'][$key];
                            $_FILES['filedata']['size'] = $_FILES['question_file']['size'][$key];

                            $this->upload->initialize($config);
                            if($this->upload->do_upload('filedata')){
                                $i_saved_file++;
                                
                                $a_file_data = [
                                    'subject_question_id' => $this->uuid->v4(),
                                    'ofse_period_id' => $s_ofse_period_id,
                                    'subject_id' => $s_subject_id,
                                    'ofse_subject_code' => $s_ofse_subject_code,
                                    'ofse_question_sequence' => $value,
                                    'subject_fname' => $this->upload->data('file_name'),
                                    'subject_number_question' => $i_total_question[$key],
                                    'date_added' => date('Y-m-d H:i:s')
                                ];

                                $mba_question_exist = $this->Ofm->get_ofse_subject_question([
                                    'ofse_period_id' => $s_ofse_period_id,
                                    'ofse_subject_code' => $s_ofse_subject_code,
                                    'ofse_question_sequence' => $value
                                ]);

                                if ($mba_question_exist) {
                                    unset($a_file_data['subject_question_id']);
                                    unset($a_file_data['date_added']);
                                    $this->Ofm->submit_file($a_file_data, [
                                        'subject_question_id' => $mba_question_exist[0]->subject_question_id
                                    ]);
                                }
                                else {
                                    $this->Ofm->submit_file($a_file_data);
                                }
                            }
                            else{
                                array_push($a_error_list, $this->upload->display_errors('<span>', '</span>').$_FILES['question_file']['name'][$key]);
                            }
                        }
                    }
                }

                if (count($a_error_list) > 0) {
                    $a_return = ['code' => 1, 'message' => implode('; ', $a_error_list)];
                }
                else if ($i_saved_file == 0) {
                    $a_return = ['code' => 1, 'message' => 'no file saved!'];
                }
                else {
                    $a_return = ['code' => 0, 'message' => 'Success!'];
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Error retrieve ofse data!'];
            }

            print json_encode($a_return);exit;
        }
    }

    public function ofse_subject_question($s_ofse_period_id = '', $s_ofse_subject_code = '', $s_subject_id = '', $s_count_student = 0)
    {
        if ($this->input->is_ajax_request()) {
            $s_ofse_subject_code = $this->input->post('ofse_subject_code');
            $s_ofse_period_id = $this->input->post('ofse_period_id');
            $s_count_student = $this->input->post('count_student');
            $s_subject_id = $this->input->post('subject_id');
        }

        if ((!empty($s_ofse_period_id)) AND (!empty($s_ofse_subject_code)) AND (!empty($subject_id))) {
            // 
        }

        $this->a_page_data['subject_question_exist'] = $this->Ofm->get_ofse_subject_question(['oq.ofse_period_id' => $s_ofse_period_id, 'oq.subject_id' => $s_subject_id]);
        $this->a_page_data['ofse_subject_code'] = $s_ofse_subject_code;
        $this->a_page_data['ofse_period_id'] = $s_ofse_period_id;
        $this->a_page_data['subject_id'] = $s_subject_id;
        $this->a_page_data['count_student'] = $s_count_student;
        $s_html = $this->load->view('ofse/form/ofse_subject_question', $this->a_page_data, true);

        if ($this->input->is_ajax_request()) {
            print json_encode(['html' => $s_html]);
        }
        else {
            if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                // print('<pre>');var_dump($this->a_page_data['subject_question_exist']);exit;
                // print($s_)
            }
            return $s_html;
        }
    }

    public function ofse_subject($s_ofse_period_id)
    {
        $this->a_page_data['ofse_period_id'] = $s_ofse_period_id;
        $this->a_page_data['body'] = $this->load->view('ofse/ofse_subject_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function get_list_ofse_subject()
    {
        if ($this->input->is_ajax_request()) {
            $s_ofse_period_id = $this->input->post('ofse_period_id');
            if (in_array($this->session->userdata('type'), ['student', 'stud'])) {
                $mba_ofse_subject = $this->Ofm->get_ofse_list_subject([
                    'sc.ofse_period_id' => $s_ofse_period_id,
                    'sc.student_id' => $this->session->userdata('student_id')
                ]);
            }
            else if (in_array($this->session->userdata('type'), ['staff', 'lecturer', 'lect'])) {
                $mba_ofse_subject = $this->Ofm->get_ofse_list_subject(['sc.ofse_period_id' => $s_ofse_period_id]);
            }
            if ($mba_ofse_subject) {
                foreach ($mba_ofse_subject as $o_subject_ofse) {
                    $mba_offered_subject = $this->General->get_where('dt_offered_subject', ['ofse_period_id' => $s_ofse_period_id, 'curriculum_subject_id' => $o_subject_ofse->curriculum_subject_id]);
                    $o_subject_ofse->subject_type = ($mba_offered_subject) ? ucwords(strtolower(str_replace('_', ' ', $mba_offered_subject[0]->ofse_status))) : '';
                    $s_subject_code = $this->generate_subject_code($o_subject_ofse->subject_name, $s_ofse_period_id);
                    $o_subject_ofse->subject_code = $s_subject_code;
                    $mba_subject_question = $this->Ofm->get_ofse_subject_question([
                        'oq.ofse_period_id' => $s_ofse_period_id,
                        'oq.ofse_subject_code' => $s_subject_code
                    ]);
                    $mba_ofse_student_member = $this->Ofm->get_ofse_list_student([
                        'sc.ofse_period_id' => $s_ofse_period_id,
                        'sn.subject_name' => $o_subject_ofse->subject_name
                    ]);
                    $o_subject_ofse->count_question = ($mba_subject_question) ? count($mba_subject_question) : 0;
                    $o_subject_ofse->count_student = ($mba_ofse_student_member) ? count($mba_ofse_student_member) : 0;
                }
            }

            print json_encode(['data' => $mba_ofse_subject]);
        }
    }

    public function unlock_evaluation()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_examiner_id = $this->input->post('student_examiner_id');
            $delete = $this->Ofm->unlock_evaluation($s_student_examiner_id);
            print json_encode(['code' => 0, 'message' => 'Success!']);
        }
    }

    public function remove_examiner()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_examiner_id = $this->input->post('student_examiner_id');
            $mba_examiner_has_filled_score = $this->General->get_where('dt_ofse_evaluation', ['student_examiner_id' => $s_student_examiner_id]);
            if ($mba_examiner_has_filled_score) {
                $a_return = ['code' => 1, 'message' => 'Examiner has filled out score!'];
            }
            else {
                $delete = $this->Ofm->remove_examiner($s_student_examiner_id);
                $a_return = ['code' => 0, 'message' => 'Success!'];
            }
            
            print json_encode($a_return);
        }
    }

    public function get_examiner_list()
    {
        if ($this->input->is_ajax_request()) {
            $s_score_id = $this->input->post('score_id');
            $mba_ofse_examiner = $this->Ofm->get_subject_examiner([
                'oe.score_id' => $s_score_id
            ]);

            if ($mba_ofse_examiner) {
                foreach ($mba_ofse_examiner as $o_examiner) {
                    $s_examiner_name = $this->Pdm->retrieve_title($o_examiner->personal_data_id);
                    $o_examiner->examiner_name = $s_examiner_name;
                    $o_examiner->examiner_type = ucfirst(strtolower(str_replace('_', ' ', $o_examiner->examiner_type)));
                }
            }

            print json_encode(['data' => $mba_ofse_examiner]);
        }
    }

    public function list_examiner()
    {
        $this->load->view('ofse/table/ofse_list_examiner', $this->a_page_data);
    }

    public function submit_ofse_examiner()
    {
        if ($this->input->is_ajax_request()) {
            $s_score_id = $this->input->post("score_id");
            $s_examiner_1 = $this->input->post("examiner_1");
            $s_examiner_1_id = $this->input->post("examiner_1_id");
            $s_examiner_1_institute = $this->input->post("examiner_1_institute");
            $s_examiner_1_id_institute = $this->input->post("examiner_1_id_institute");
            $s_examiner_2 = $this->input->post("examiner_2");
            $s_examiner_2_id = $this->input->post("examiner_2_id");
            $s_examiner_2_institute = $this->input->post("examiner_2_institute");
            $s_examiner_2_id_institute = $this->input->post("examiner_2_id_institute");
            $s_examiner_3 = $this->input->post("examiner_3");
            $s_examiner_3_id = $this->input->post("examiner_3_id");
            $s_examiner_3_institute = $this->input->post("examiner_3_institute");
            $s_examiner_3_id_institute = $this->input->post("examiner_3_id_institute");
            $s_examiner_4 = $this->input->post("examiner_4");
            $s_examiner_4_id = $this->input->post("examiner_4_id");
            $s_examiner_4_institute = $this->input->post("examiner_4_institute");
            $s_examiner_4_id_institute = $this->input->post("examiner_4_id_institute");
            
            $this->db->trans_begin();

            $mbs_examiner_1 = $this->processing_examiner($s_examiner_1_id, $s_examiner_1, $s_examiner_1_id_institute, $s_examiner_1_institute);
            $mbs_examiner_2 = $this->processing_examiner($s_examiner_2_id, $s_examiner_2, $s_examiner_2_id_institute, $s_examiner_2_institute);
            $mbs_examiner_3 = $this->processing_examiner($s_examiner_3_id, $s_examiner_3, $s_examiner_3_id_institute, $s_examiner_3_institute);
            $mbs_examiner_4 = $this->processing_examiner($s_examiner_4_id, $s_examiner_4, $s_examiner_4_id_institute, $s_examiner_4_institute);

            if ($mbs_examiner_1) {
                $student_examiner_1 = $this->Ofm->get_subject_examiner([
                    'oe.score_id' => $s_score_id,
                    'oe.examiner_type' => 'examiner_1'
                ]);

                $a_student_examiner_data_1 = [
                    'score_id' => $s_score_id,
                    'advisor_id' => $mbs_examiner_1,
                    'examiner_type' => 'examiner_1'
                ];
                if ($student_examiner_1) {
                    $this->Ofm->submit_ofse_examiner($a_student_examiner_data_1, ['student_examiner_id' => $student_examiner_1[0]->student_examiner_id]);
                }
                else {
                    $a_student_examiner_data_1['student_examiner_id'] = $this->uuid->v4();
                    $this->Ofm->submit_ofse_examiner($a_student_examiner_data_1);
                }
            }

            if ($mbs_examiner_2) {
                $student_examiner_2 = $this->Ofm->get_subject_examiner([
                    'oe.score_id' => $s_score_id,
                    'oe.examiner_type' => 'examiner_2'
                ]);

                $a_student_examiner_data_2 = [
                    'score_id' => $s_score_id,
                    'advisor_id' => $mbs_examiner_2,
                    'examiner_type' => 'examiner_2'
                ];
                if ($student_examiner_2) {
                    $this->Ofm->submit_ofse_examiner($a_student_examiner_data_2, ['student_examiner_id' => $student_examiner_2[0]->student_examiner_id]);
                }
                else {
                    $a_student_examiner_data_2['student_examiner_id'] = $this->uuid->v4();
                    $this->Ofm->submit_ofse_examiner($a_student_examiner_data_2);
                }
            }
            
            if ($mbs_examiner_3) {
                $student_examiner_3 = $this->Ofm->get_subject_examiner([
                    'oe.score_id' => $s_score_id,
                    'oe.examiner_type' => 'examiner_3'
                ]);

                $a_student_examiner_data_3 = [
                    'score_id' => $s_score_id,
                    'advisor_id' => $mbs_examiner_3,
                    'examiner_type' => 'examiner_3'
                ];
                if ($student_examiner_3) {
                    $this->Ofm->submit_ofse_examiner($a_student_examiner_data_3, ['student_examiner_id' => $student_examiner_3[0]->student_examiner_id]);
                }
                else {
                    $a_student_examiner_data_3['student_examiner_id'] = $this->uuid->v4();
                    $this->Ofm->submit_ofse_examiner($a_student_examiner_data_3);
                }
            }

            if ($mbs_examiner_4) {
                $student_examiner_4 = $this->Ofm->get_subject_examiner([
                    'oe.score_id' => $s_score_id,
                    'oe.examiner_type' => 'examiner_4'
                ]);

                $a_student_examiner_data_4 = [
                    'score_id' => $s_score_id,
                    'advisor_id' => $mbs_examiner_4,
                    'examiner_type' => 'examiner_4'
                ];
                if ($student_examiner_4) {
                    $this->Ofm->submit_ofse_examiner($a_student_examiner_data_4, ['student_examiner_id' => $student_examiner_4[0]->student_examiner_id]);
                }
                else {
                    $a_student_examiner_data_4['student_examiner_id'] = $this->uuid->v4();
                    $this->Ofm->submit_ofse_examiner($a_student_examiner_data_4);
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $a_return = ['code' => 1, 'message' => 'Failed processing data!'];
            }
            else {
                $this->db->trans_commit();
                $a_return = ['code' => 0, 'message' => 'success'];
            }

            print json_encode($a_return);
        }
    }

    public function processing_examiner($s_advisor_id, $s_personal_data_name, $s_institution_id, $s_institution_name)
    {
        $mbs_advisor_id = false;
        
        if (empty($s_advisor_id)) {
            $s_personal_data_id = '';
            if (!empty($s_personal_data_name)) {
                $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_name' => $s_personal_data_name]);
                if ($mba_personal_data) {
                    $s_personal_data_id = $mba_personal_data[0]->personal_data_id;
                }
                else {
                    $s_personal_data_id = $this->uuid->v4();
                    $a_personal_data = [
                        'personal_data_id' => $s_personal_data_id,
                        'personal_data_name' => $s_personal_data_name,
                        'personal_data_cellular' => 0
                    ];
                    
                    $this->Pdm->create_personal_data_parents($a_personal_data);
                }
            }

            if (!empty($s_personal_data_id)) {
                if (empty($s_institution_id)) {
                    if (!empty($s_institution_name)) {
                        $s_institution_id = $this->uuid->v4();
                        $a_institution_data = [
                            'institution_id' => $s_institution_id,
                            'institution_name' => $s_institution_name
                        ];
                        
                        $this->Inm->insert_institution($a_institution_data);
                    }
                }
    
                $s_institution_id = (empty($s_institution_id)) ? null : $s_institution_id;
                $mba_advisor_data = $this->General->get_where('thesis_advisor', ['personal_data_id' => $s_personal_data_id]);
                if ($mba_advisor_data) {
                    $o_advisor = $mba_advisor_data[0];
                    $mbs_advisor_id = $o_advisor->advisor_id;
                    $a_advisor_data = [
                        'personal_data_id' => $s_personal_data_id,
                        'institution_id' => $s_institution_id
                    ];
                    $this->Tm->submit_advisor($a_advisor_data, [
                        'advisor_id' => $mbs_advisor_id
                    ]);
                }
                else {
                    $mbs_advisor_id = $this->uuid->v4();
                    $a_advisor_data = [
                        'advisor_id' => $mbs_advisor_id,
                        'personal_data_id' => $s_personal_data_id,
                        'institution_id' => $s_institution_id
                    ];
                    $this->Tm->submit_advisor($a_advisor_data);
                }
            }
        }
        else {
            $mba_advisor_data = $this->General->get_where('thesis_advisor', ['advisor_id' => $s_advisor_id]);
            if ($mba_advisor_data) {
                if (empty($s_institution_id)) {
                    if (!empty($s_institution_name)) {
                        $s_institution_id = $this->uuid->v4();
                        $a_institution_data = [
                            'institution_id' => $s_institution_id,
                            'institution_name' => $s_institution_name
                        ];
                        
                        $this->Inm->insert_institution($a_institution_data);
                    }
                }
    
                $s_institution_id = (empty($s_institution_id)) ? null : $s_institution_id;
                $mbs_advisor_id = $s_advisor_id;
                $a_advisor_data = [
                    'institution_id' => $s_institution_id
                ];
                $this->Tm->submit_advisor($a_advisor_data, [
                    'advisor_id' => $mbs_advisor_id
                ]);
            }
        }

        return $mbs_advisor_id;
    }

    public function form_examiner()
    {
        $this->load->view('ofse/form/form_input_examiner', $this->a_page_data);
    }

    public function download_file($s_file, $s_ofse_period_id)
    {
        $mbo_ofse_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id])[0];
        if ($mbo_ofse_data) {
            $s_period_name = str_replace(' ', '-', $mbo_ofse_data->ofse_period_name);

            $s_file_path = APPPATH.'/uploads/academic/ofse/'.$s_period_name.'/'.$s_file;
            if(!file_exists($s_file_path)){
                show_404();
            }
            else{
                $a_path_info = pathinfo($s_file_path);
                $s_file_ext = $a_path_info['extension'];
                // header("Content-Type: ".$mbo_personal_data_document[0]->document_mime);
                header('Content-Disposition: attachment; filename='.urlencode($s_file));
                readfile( $s_file_path );
                exit;
            }
        }else{
            show_404();
        }
    }

    public function approve_subjects()
    {
	    if($this->input->is_ajax_request()){
		    $a_subjects = $this->input->post('subjects');
			$s_student_id = $this->input->post('student_id');
			$s_academic_year_id = $this->input->post('academic_year_id');
			$s_semester_type_id = $this->input->post('semester_type_id');
			
			$this->db->trans_start();
			
			for($i = 0; $i < count($a_subjects); $i++){
				$mba_score_data = $this->Scm->get_score_student($s_student_id, array(
					'sc.academic_year_id' => $s_academic_year_id,
					'sc.semester_type_id' => $s_semester_type_id,
					'sc.curriculum_subject_id' => $a_subjects[$i]
				));
				
				if(!$mba_score_data){
					$this->Scm->save_data(array(
						'academic_year_id' => $s_academic_year_id,
						'semester_type_id' => $s_semester_type_id,
						'curriculum_subject_id' => $a_subjects[$i],
						'student_id' => $s_student_id,
						'score_approval' => 'approved'
					));
				}
				else{
					$this->Scm->save_data(array(
						'score_approval' => 'approved'
					), array(
						'score_id' => $mba_score_data[0]->score_id
					));
				}
			}
			
			$mba_score_data = $this->Scm->get_score_student($s_student_id, array(
				'sc.academic_year_id' => $s_academic_year_id,
				'sc.semester_type_id' => $s_semester_type_id
			));
			
			foreach($mba_score_data as $score){
				if(!in_array($score->curriculum_subject_id, $a_subjects)){
					$this->Scm->delete_data($score->score_id);
				}
			}
			
			if($this->db->trans_status() === false){
				$this->db->trans_rollback();
				$rtn = array('code' => 1, 'message' => 'Fail');
			}
			else{
                $this->db->trans_commit();
				$rtn = array('code' => 0, 'message' => 'Success');
			}
			
			print json_encode($rtn);
			exit;
	    }
    }
    
    public function approval($s_student_id, $s_academic_year_id, $s_semester_type_id)
    {
	    $this->a_page_data['params'] = array(
		    'student_id' => $s_student_id,
		    'academic_year_id' => $s_academic_year_id,
		    'semester_type_id' => $s_semester_type_id,
		    'module' => 'academic'
	    );
	    $this->a_page_data['body'] = $this->load->view('ofse/approval', $this->a_page_data, true);
	    $this->load->view('layout', $this->a_page_data);
    }
    
    public function get_ofse_participants_list()
    {
	    if($this->input->is_ajax_request()){
		    $s_academic_year_id = $this->input->post('academic_year_id');
		    $s_semester_type_id = $this->input->post('semester_type_id');
		    $s_study_program_id = $this->input->post('study_program_id');
		    
		    $mba_ofse_participants_list = $this->Ofm->get_ofse_participants_list($s_academic_year_id, $s_semester_type_id, $s_study_program_id);
		    if($mba_ofse_participants_list){
			    foreach($mba_ofse_participants_list as $mopl){
				    $mba_selected_subjects = $this->Ofm->get_ofse_participants_subjects($mopl->student_id, $s_academic_year_id, $s_semester_type_id);
				    $mopl->selected_subjects = $mba_selected_subjects;
			    }
		    }
		    
		    print json_encode(array(
			    'code' => 0,
			    'data' => $mba_ofse_participants_list
		    ));
		    exit;
	    }
    }
    
    public function ofse_participant_table()
    {
	    $this->load->view('ofse/table/registered_ofse_participants', $this->a_page_data);
    }
    
    public function ofse_participants()
    {
	    $this->a_page_data['body'] = $this->load->view('ofse/ofse_approval', $this->a_page_data, true);
	    $this->load->view('layout', $this->a_page_data);
    }

    public function ofse_lists($s_class_group_id = false)
    {
        // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
            if ($this->input->is_ajax_request()) {
                $mba_ofse_list = $this->General->get_where('dt_ofse');
                print json_encode(['code' => 0, 'data' => $mba_ofse_list]);
            }else{
                $this->a_page_data['body'] = $this->load->view('ofse/ofse_list', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }
        // }else{
        //     $this->a_page_data['body'] = $this->load->view('dashboard/maintenance_page', $this->a_page_data, true);
        //     $this->load->view('layout', $this->a_page_data);
        // }
        // if ($s_class_group_id) {
        //     $mbo_class_data = $this->Cgm->get_class_by_offered_subject(array('cg.class_group_id' => $s_class_group_id));
        //     if ($mbo_class_data) {
        //         $this->a_page_data['class_details'] = $this->get_class_details($s_class_group_id);

        //         $this->a_page_data['class_group_id'] = $s_class_group_id;
        //         $this->a_page_data['body'] = $this->load->view('ofse/ofse_member', $this->a_page_data, true);
        //         $this->load->view('layout', $this->a_page_data);
        //     }else{
        //         $this->a_page_data['body'] = $this->load->view('ofse/ofse', $this->a_page_data, true);
        //         $this->load->view('layout', $this->a_page_data);
        //     }
        // }
        // else{
        //     $this->a_page_data['body'] = $this->load->view('ofse/ofse', $this->a_page_data, true);
        //     $this->load->view('layout', $this->a_page_data);
        // }
    }

    public function force_registration()
    {
        if ($this->session->userdata('type') == 'staff') {
            $a_params = [
                'academic_year_id' => 2020,
                'semester_type_id' => 6,
                'student_id' => '5f0cad6f-932b-4880-9e7c-7cac1af3a64d',
                'module' => 'staff'
            ];
            $this->a_page_data['body'] = modules::run('student/ofse_registration', $a_params);
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    public function check_examiner($s_score_id)
    {
        $s_personal_data_id = $this->session->userdata('user');
        $mba_is_examiner = $this->Ofm->get_student_ofse_examiner($s_score_id, [
            'ta.personal_data_id' => $s_personal_data_id
        ]);

        return ($mba_is_examiner) ? $mba_is_examiner[0] : false;
    }

    public function upload_ofse_score()
    {
        print('function closed!');exit;
        $s_file_path = APPPATH.'uploads/academic/ofse/OFSE-June-2021/OFSE_Scores.xlsx';
        $o_spreadsheet = IOFactory::load("$s_file_path");
        $o_sheet = $o_spreadsheet->getActiveSheet();
        $a_row_score_not_found = [];
        $a_score_success = [];
        $a_score_failed = [];

        $i_row = 2;
        $i_count_examiner = 0;
        $a_col_examiner = [];
        $c_start_examiner = $c_cols_examiner = $c_last_examiner = 'G';
        
        while ($o_sheet->getCell($c_cols_examiner.'1')->getValue() !== NULL) {
            $s_examiner = $o_sheet->getCell($c_cols_examiner.'1')->getValue();
            $s_examiner = strtolower($s_examiner);
            $find_string = strpos($s_examiner, 'examiner');
            
            if ($find_string === false) {
                break;
            }
            else {
                array_push($a_col_examiner, $c_cols_examiner);
                $c_last_examiner = $c_cols_examiner;
                $i_count_examiner++;
            }
            $c_cols_examiner++;
        }

        while ($o_sheet->getCell("C$i_row")->getValue() !== NULL) {
            $s_student_number = $o_sheet->getCell("D$i_row")->getValue();
            $s_subject_name = $o_sheet->getCell("E$i_row")->getValue();
            $s_student_name = $o_sheet->getCell("C$i_row")->getValue();

            $mba_student = $this->General->get_where('dt_student', [
                'student_number' => $s_student_number,
                'student_status' => 'active'
            ]);

            if (!$mba_student) {
                print('student row '.$i_row.' not found!');exit;
            }
            
            $o_student = $mba_student[0];
            $mba_score_data = $this->Scm->get_score_data([
                'sc.student_id' => $o_student->student_id,
                'sc.score_approval' => 'approved',
                'sn.subject_name' => $s_subject_name,
                'curs.curriculum_subject_credit' => 0,
                'sc.semester_id' => 17
            ], [4,6]);

            if (!$mba_score_data) {
                array_push($a_row_score_not_found, [
                    'student_name' => $s_student_name,
                    'subject_name' => $s_subject_name
                ]);
            }
            else {
                $o_score_data = $mba_score_data[0];
                $s_score_id = $o_score_data->score_id;

                $i_numb = 1;
                $a_score_examiner = [];
                $a_score = [];
                foreach ($a_col_examiner as $s_cols) {
                    $f_score_examiner = (float) $o_sheet->getCell($s_cols.$i_row)->getValue();;
                    array_push($a_score_examiner, ['score_examiner_'.$i_numb++ => $f_score_examiner]);

                    if (($f_score_examiner != '') AND ($f_score_examiner != 0)) {
                        array_push($a_score, $f_score_examiner);
                    }
                }

                $f_ofse_score = array_sum($a_score) / count($a_score);
                $a_score_data = array(
                    'score_quiz' => $f_ofse_score,
                    'score_final_exam' => $f_ofse_score,
                    'score_sum' => $f_ofse_score,
                    'score_grade' => $this->grades->get_grade($f_ofse_score),
                    'score_approval' => 'approved',
                    'score_examiner' => json_encode($a_score_examiner)
                );

                if ($this->Scm->save_data($a_score_data, array('score_id' => $s_score_id))) {
                    array_push($a_score_success, $a_score_data);
                }else{
                    array_push($a_score_failed, $a_score_data);
                }
            }
            
            $i_row++;
        }

        print('<pre>');
        print('<p>===================================================</p>');
        print('KRS Not found: <br>');var_dump($a_row_score_not_found);
        print('<p>===================================================</p>');
        print('Failed save data: <br>');var_dump($a_score_failed);
        print('<p>===================================================</p>');
        print('Success save data: <br>');var_dump($a_score_success);
        exit;
    }

    public function ofse_test()
    {
        $this->a_page_data['body'] = $this->load->view('ofse/ofse', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function ofse_list()
    {
        if ($this->input->is_ajax_request()) {
            $mba_ofse_list = $this->General->get_where('dt_ofse');
            print json_encode(['code' => 0, 'data' => $mba_ofse_list]);
        }else{
            $this->a_page_data['body'] = $this->load->view('ofse/ofse_list', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function form_new_ofse()
    {
        $this->load->view('ofse/form/new_ofse', $this->a_page_data);
    }

    public function offered_subject($s_ofse_period_id)
    {
        $mbo_ofse_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id])[0];
        if ($mbo_ofse_data) {
            $this->a_page_data['ofse_data'] = $mbo_ofse_data;
            $this->a_page_data['ofse_status_list'] = $this->General->get_enum_values('dt_offered_subject', 'ofse_status');
            $this->a_page_data['study_program_list'] = $this->Spm->get_study_program(false, false);
            $this->a_page_data['body'] = $this->load->view('ofse/offered_subject_ofse', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }else{
            print('Ofse not found!');
        }
    }

    public function submit_offered_subject_ofse()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('subject_status', 'Subject Status', 'required');
            $s_ofse_period_id = $this->input->post('ofse_period_id');
            $s_curriculum_subject_id = $this->input->post('curriculum_subject_id');
            $s_study_program_id = $this->input->post('study_program_id');

            $mbo_ofse_period_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id])[0];
            $mbo_semester_active = $this->Smm->get_active_semester();
            $mbo_curriculum_subject_data = $this->Cm->get_curriculum_subject_data($s_curriculum_subject_id);

            if ($this->form_validation->run()) {
                if ($mbo_ofse_period_data AND $mbo_semester_active) {
                    $mba_check_offer_subject_exists = $this->General->get_where('dt_offered_subject', [
                        'curriculum_subject_id' => $s_curriculum_subject_id,
                        'ofse_period_id' => $s_ofse_period_id,
                        'study_program_id' => $s_study_program_id,
                        'ofse_status' => set_value('subject_status')
                    ]);

                    if (!$mba_check_offer_subject_exists) {
                        $s_offered_subject_id = $this->uuid->v4();
                        $a_data = [
                            'offered_subject_id' => $s_offered_subject_id,
                            'curriculum_subject_id' => $s_curriculum_subject_id,
                            'academic_year_id' => $mbo_semester_active->academic_year_id,
                            'semester_type_id' => ($mbo_semester_active->semester_type_id == 1) ? 4 : 6,
                            'program_id' => 1,
                            'study_program_id' => $s_study_program_id,
                            'ofse_period_id' => $s_ofse_period_id,
                            'ofse_status' => set_value('subject_status'),
                            'date_added' => date('Y-m-d H:i:s')
                        ];

                        if ($this->Osm->save_offer_subject($a_data)) {
                            $s_class_group_id = $this->uuid->v4();

                            $a_class_group_data = [
                                'class_group_id' => $s_class_group_id,
                                'academic_year_id' => $mbo_semester_active->academic_year_id,
                                'semester_type_id' => ($mbo_semester_active->semester_type_id == 1) ? 4 : 6,
                                'class_group_name' => 'OFSE '.$mbo_curriculum_subject_data->subject_name,
                                'date_added' => date('Y-m-d H:i:s')
                            ];

                            $a_class_group_subject_data = [
                                'class_group_subject_id' => $this->uuid->v4(),
                                'class_group_id' => $s_class_group_id,
                                'offered_subject_id' => $s_offered_subject_id,
                                'date_added' => date('Y-m-d H:i:s')
                            ];

                            if ($this->Cgm->save_data($a_class_group_data)) {
                                if ($this->Cgm->save_class_group_subject($a_class_group_subject_data)) {
                                    $a_return = ['code' => 0, 'message' => 'Success'];
                                }else{
                                    $a_return = ['code' => 1, 'message' => 'Fail processing subject!'];
                                }
                            }else{
                                $a_return = ['code' => 1, 'message' => 'Fail processing class!'];
                            }
                        }else{
                            $a_return = ['code' => 1, 'message' => 'Fail submitting data!'];
                        }
                    }else{
                        $a_return = ['code' => 1, 'message' => 'Subject has been offered before!'];
                    }
                }else{
                    $a_return = ['code' => 1, 'message' => 'OFSE data not found!'];
                }
            }else{
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }

    public function get_ofse_offered_subject()
    {
        if ($this->input->is_ajax_request()) {
            $s_study_program_id = $this->input->post('study_program_id');
            $s_ofse_period_id = $this->input->post('ofse_period_id');

            $mbo_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $s_study_program_id])[0];
            $mba_offered_subject_list = false;
            if ($mbo_study_program_data) {
                $mba_offered_subject_data_main_prodi = false;
                if (!is_null($mbo_study_program_data->study_program_main_id)) {
                    $mba_offered_subject_data_main_prodi = $this->Osm->get_offered_subject_lists_filtered([
                        'dos.study_program_id' => $mbo_study_program_data->study_program_main_id,
                        'dos.ofse_period_id' => $s_ofse_period_id,
                        'rcs.curriculum_subject_category' => 'ofse',
                        'rcs.semester_id' => 17
                    ]);
                }
                
                $mba_offered_subject_data_prodi = $this->Osm->get_offered_subject_lists_filtered([
                    'dos.study_program_id' => $s_study_program_id,
                    'dos.ofse_period_id' => $s_ofse_period_id,
                    'rcs.curriculum_subject_category' => 'ofse',
                    'rcs.semester_id' => 17
                ]);

                if ($mba_offered_subject_data_main_prodi AND $mba_offered_subject_data_prodi) {
                    $mba_offered_subject_list = array_merge($mba_offered_subject_data_main_prodi, $mba_offered_subject_data_prodi);
                }else if ($mba_offered_subject_data_main_prodi) {
                    $mba_offered_subject_list = $mba_offered_subject_data_main_prodi;
                }else{
                    $mba_offered_subject_list = $mba_offered_subject_data_prodi;
                }

                if ($mba_offered_subject_list) {
                    foreach ($mba_offered_subject_list as $o_offered_subject) {
                        $mba_lect_subject = $this->Osm->get_offer_subject_lecturer($o_offered_subject->offered_subject_id);
                        $i_count_lect = 0;
                        $o_offered_subject->lecturer_1 = '-';
                        $o_offered_subject->lecturer_2 = '-';
                        $o_offered_subject->subject_name = $o_offered_subject->subject_name.' ('.$o_offered_subject->study_program_abbreviation.')';
                        if ($mba_lect_subject) {
                            foreach ($mba_lect_subject as $o_lecturer) {
                                $string = 'lecturer_';
                                $o_offered_subject->$string.$i = $o_lecturer->personal_data_name;
                            }
                        }
                    }
                }
            }

            print json_encode([
                'code' => 0,
                'prodi' => ($mbo_study_program_data) ? $mbo_study_program_data->study_program_abbreviation : '',
                'data' => $mba_offered_subject_list]
            );
        }
    }

    public function get_ofse_curriculum_subject()
    {
        if ($this->input->is_ajax_request()) {
            $s_study_program_id = $this->input->post('study_program_id');

            $mbo_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $s_study_program_id])[0];

            if (($mbo_study_program_data) AND (!is_null($mbo_study_program_data->study_program_main_id))) {
                $s_study_program_id =  $mbo_study_program_data->study_program_main_id;

                $mba_curriculum_subject_data_main_prodi = $this->Cm->get_curriculum_subject_filtered(array(
                    'rc.study_program_id' => $s_study_program_id,
                    'rcs.curriculum_subject_category' => 'ofse',
                    'rcs.semester_id' => 17
                ));

                $mba_curriculum_subject_data_prodi = $this->Cm->get_curriculum_subject_filtered(array(
                    'sp.study_program_main_id' => $s_study_program_id,
                    'rcs.curriculum_subject_category' => 'ofse',
                    'rcs.semester_id' => 17
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
                    'rcs.curriculum_subject_category' => 'ofse',
                    'rcs.semester_id' => 17
                ));
            }

            print json_encode([
                'code' => 0,
                'prodi' => ($mbo_study_program_data) ? $mbo_study_program_data->study_program_abbreviation : '',
                'data' => $mba_curriculum_subject_data_lists]
            );
        }
    }

    public function submit_new_ofse()
    {
        if ($this->input->is_ajax_request()) {
            $s_ofse_period_id = $this->input->post('ofse_period_id');

            $this->form_validation->set_rules('ofse_period_name', 'Period', 'required');
            $this->form_validation->set_rules('study_plan_ofse_period', 'Student Registration Date', 'required');

            if ($this->form_validation->run()) {
                $s_date_range = set_value('study_plan_ofse_period');
                $a_dates = explode('-', $s_date_range);
                $s_start_date = date('Y-m-d 00:00:00', strtotime(trim($a_dates[0])));
                $s_end_date = date('Y-m-d 23:59:59', strtotime(trim($a_dates[1])));

                $a_data = [
                    'study_plan_ofse_start_date' => $s_start_date,
                    'study_plan_ofse_end_date' => $s_end_date,
                    'ofse_period_name' => set_value('ofse_period_name')
                ];

                if ($s_ofse_period_id == '') {
                    $a_data['ofse_period_id'] = $this->uuid->v4();
                    $s_ofse_period_id = $a_data['ofse_period_id'];

                    $save_data = $this->Ofm->submit_new_ofse($a_data);
                }else{
                    $save_data = $this->Ofm->submit_new_ofse($a_data, $s_ofse_period_id);
                }

                if ($save_data) {
                    $a_rtn = array('code' => 0, 'message' => 'Success');
                    $a_rtn['uri'] = ($s_ofse_period_id == '') ? base_url().'academic/ofse/offered_subject/'.$s_ofse_period_id : '';
                }else{
                    $a_rtn = array('code' => 1, 'message' => 'Fail submit data!');
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_rtn);
        }
    }

    public function form_filter_ofse()
    {
        $this->load->model('academic/Academic_year_model','Aym');
        
        $this->a_page_data['mba_study_program'] = $this->Spm->get_study_program_lists(array(
	        'program_id' => 1
        ));
        $this->a_page_data['mbo_semester_type'] = $this->Smm->get_semester_type_lists(false, false, array(4,6));
        $this->a_page_data['mbo_academic_year'] = $this->Aym->get_academic_year_lists();
        $this->load->view('ofse/form/ofse_filter', $this->a_page_data);
    }

    public function view_ofse_lists_table()
    {
        $s_btn_html = Modules::run('layout/generate_buttons', 'academic', 'ofse_table_action');
        $this->a_page_data['btn_html'] = $s_btn_html;
        $this->load->view('ofse/table/ofse_lists_table', $this->a_page_data);
    }

    // public function view_member()
    // {
    //     $this->load->view('table/ofse_member', $this->a_page_data);
    // }

    // public function form_filter_ofse_member()
    // {
    //     $this->load->view('ofse/form/ofse_member_filter', $this->a_page_data);
    // }

    public function form_input_score()
    {
        $this->load->view('ofse/form/ofse_input_score', $this->a_page_data);
    }

    // public function view_ofse_lists_member($s_class_group_id)
    // {
    //     $s_btn_html = modules::run('layout/generate_buttons', 'academic', 'ofse_member_action');
    //     $this->a_page_data['btn_html'] = $s_btn_html;

    //     $this->a_page_data['class_group_id'] = $s_class_group_id;
    //     $this->load->view('ofse/table/lists_ofse_member', $this->a_page_data);
    // }

    public function get_member_details()
    {
        if ($this->input->is_ajax_request()) {
            $s_ofse_period_id = $this->input->post('ofse_period_id');
            
            $mba_ofse_member = $this->Ofm->get_ofse_member([
                'sc.ofse_period_id' => $s_ofse_period_id,
                'sc.score_approval' => 'approved',
                'sc.semester_id' => 17
            ], true);

            if ($mba_ofse_member) {
                foreach ($mba_ofse_member as $o_member) {
                    $mba_student_subject = $this->Ofm->get_ofse_member([
                        'sc.student_id' => $o_member->student_id,
                        'sc.ofse_period_id' => $s_ofse_period_id,
                        'sc.score_approval' => 'approved',
                        'sc.semester_id' => 17
                    ]);

                    $o_member->total_subject = ($mba_student_subject) ? count($mba_student_subject) : 0;
                }
            }
            
            $a_rtn = array('code' => 0, 'data' => $mba_ofse_member);
            print(json_encode($a_rtn));
        }
    }

    public function get_ofse_structure()
    {
        if ($this->input->is_ajax_request()) {
            $s_ofse_period_id = $this->input->post('ofse_period_id');
            
            $mba_ofse_structure = $this->Ofm->get_ofse_structure($s_ofse_period_id);
            if ($this->session->userdata('user') == '41261c5c-94c7-4c5e-b4f9-4117f4567b8a') {
                $mba_ofse_structure = $this->Ofm->get_ofse_structure();
            }
            $mba_examiner = $this->Ofm->is_examiner([
                'ta.personal_data_id' => $this->session->userdata('user')
            ]);

            // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
            //     $mba_ofse_member = $this->Ofm->get_ofse_member([
            //         'sc.ofse_period_id' => $s_ofse_period_id,
            //         'sc.score_approval' => 'approved',
            //         'sc.semester_id' => 17
            //     ], true);
            // }

            if ($mba_ofse_structure) {
                foreach ($mba_ofse_structure as $key => $o_ofse) {
                    $o_ofse->ofse_subject_code = $this->generate_subject_code($o_ofse->subject_name, $s_ofse_period_id);
                    // $o_ofse->ofse_exam_date = ($o_ofse->exam_date) ? date('d F Y', strtotime($o_ofse->exam_date)) : '';
                    // $o_ofse->ofse_exam_time = ($o_ofse->exam_date) ? date('H:i', strtotime($o_ofse->exam_time_start)).' - '.date('H:i', strtotime($o_ofse->exam_time_end)) : '';
                    if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                        $mba_is_child = $this->General->get_where('dt_ofse_examiner', [
                            'score_id' => $o_ofse->score_id,
                            'advisor_id' => $mba_examiner[0]->advisor_id
                        ]);
    
                        if (!$mba_is_child) {
                            unset($mba_ofse_structure[$key]);
                        }
                    }
                }
                $mba_ofse_structure = array_values($mba_ofse_structure);
            }
            
            $a_rtn = array('code' => 0, 'data' => $mba_ofse_structure);
            print(json_encode($a_rtn));
        }
    }

    // public function get_ofse_student()
    // {
    //     if ($this->input->is_ajax_request()) {
    //         $a_filter = [
    //             'sc.academic_year_id' => $this->input->post('academic_year_id'),
    //             'sc.semester_type_id' => $this->input->post('semester_type_id'),
    //             'st.study_program_id' => $this->input->post('study_program_id')
    //         ];

    //         if ($this->input->post('study_program_id') == 'All') {
    //             unset($a_filter['st.study_program_id']);
    //         }

    //         $mba_score_student_list = $this->Scm->get_student_by_score($a_filter);
    //         print json_encode(['code' => 0, 'data' => $mba_score_student_list]);
    //     }
    // }

    public function get_ofse_subject_student()
    {
        if ($this->input->is_ajax_request()) {
            $a_term = $this->input->post('term');

            $s_academic_year_id = $a_term['academic_year_id'];
            $s_semester_type_id = $a_term['semester_type_id'];
            $s_study_program_id = $a_term['study_program_id'];
            $s_program_id = $a_term['program_id'];
            $s_student_id = $a_term['student_id'];

            $a_param = [
                'ofs.academic_year_id' => $s_academic_year_id,
                'ofs.study_program_id' => $s_study_program_id,
                'ofs.semester_type_id' => $s_semester_type_id
            ];

            $mba_ofse_subject_lists = $this->filter_ofse_subject_data($a_param);
            // $mba_ofse_subject_lists = array_values($mba_ofse_subject_lists);
            // print('<pre>');
            // var_dump($mba_ofse_subject_lists);exit;
            
            $a_rtn = array('code' => 0, 'data' => $mba_ofse_subject_lists);
            print json_encode($a_rtn);exit;
        }
    }

    // public function get_ofse_subject_student()
    // {
    //     if ($this->input->is_ajax_request()) {
    //         $a_param = array(
    //             'ofs.academic_year_id' => $this->input->post('academic_year_id'),
    //             'ofs.study_program_id' => $this->input->post('study_program_id'),
    //             'ofs.semester_type_id' => $this->input->post('semester_type_id')
    //         );

    //         $mba_ofse_subject_lists = $this->filter_ofse_subject_data($a_param);
            
    //         $a_rtn = array('code' => 0, 'data' => $mba_ofse_subject_lists);
    //         print json_encode($a_rtn);exit;
    //     }
    // }

    // public function get_ofse_subject_class()
    // {
    //     if ($this->input->is_ajax_request()) {
    //         $a_filter_data = array(
    //             'ofs.academic_year_id' => $this->input->post('academic_year_id'),
    //             'ofs.semester_type_id' => $this->input->post('semester_type_id')
    //         );

    //         if ($this->input->post('study_program_id') != 'All') {
    //             $a_filter_data['ofs.study_program_id'] = $this->input->post('study_program_id');
    //         }
    //         $mbo_ofse_lists = $this->Ofm->get_ofse_class($a_filter_data);
    //         $this->load->model('personal_data/Personal_data_model', 'Pdm');
    //         if ($mbo_ofse_lists) {
    //             $i_count_examiner = 0;
                
    //             foreach ($mbo_ofse_lists as $ofse) {
    //                 $s_examiner_ofse = 'N/A';
    //                 $a_examiner_ofse = array();
                    
    //                 $mbo_ofse_student_count = $this->Ofm->get_ofse_student($ofse->class_group_id);
    //                 if ($mbo_ofse_student_count) {
    //                     $ofse->student_count = count($mbo_ofse_student_count);
    //                 }else{
    //                     $ofse->student_count = 0;
    //                 }
    //                 $mbo_ofse_examiner = $this->Ofm->get_ofse_examiner($ofse->class_group_id);
    //                 if ($mbo_ofse_examiner) {
    //                     foreach ($mbo_ofse_examiner as $examiner) {
    //                         $s_examiner_name = $this->Pdm->retrieve_title($examiner->personal_data_id);
    //                         if (!in_array($s_examiner_name, $a_examiner_ofse)) {
    //                             array_push($a_examiner_ofse, $s_examiner_name);
    //                         }
    //                     }
    //                     $s_examiner_ofse = implode(" / ", $a_examiner_ofse);
    //                 }
    //                 $i_count_examiner = ($i_count_examiner < count($a_examiner_ofse)) ? count($a_examiner_ofse) : $i_count_examiner;
                    
    //                 $ofse->examiner_ofse = $s_examiner_ofse;
    //                 $ofse->a_examiner_ofse = $a_examiner_ofse;
    //             }
    //         }
            
    //         $a_rtn = array('code' => 0, 'data' => $mbo_ofse_lists);
    //         print json_encode($a_rtn);exit;
    //     }
    // }

    public function get_ofse_subject_lists()
    {
        if ($this->input->is_ajax_request()) {
            $a_filter_data = array(
                'ofs.academic_year_id' => $this->input->post('academic_year_id'),
                'ofs.semester_type_id' => $this->input->post('semester_type_id')
            );

            if ($this->input->post('study_program_id') != 'All') {
                $a_filter_data['ofs.study_program_id'] = $this->input->post('study_program_id');
            }
            $mba_ofse_subject_lists = $this->filter_ofse_subject_data($a_filter_data);
            
            $a_rtn = array('code' => 0, 'data' => $mba_ofse_subject_lists);
            print json_encode($a_rtn);exit;
        }
    }

    public function filter_ofse_subject_data($a_param)
    {
        $mbo_ofse_lists = $this->Ofm->get_ofse_lists_subject($a_param);
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        if ($mbo_ofse_lists) {
	        $i_count_examiner = 0;
	       	 
            foreach ($mbo_ofse_lists as $ofse) {
                $s_examiner_ofse = 'N/A';
                $a_examiner_ofse = array();
                
                $mbo_ofse_student_count = $this->Ofm->get_ofse_student($ofse->class_group_id);
                if ($mbo_ofse_student_count) {
                    $ofse->student_count = count($mbo_ofse_student_count);
                }else{
                    $ofse->student_count = 0;
                }
                $mbo_ofse_examiner = $this->Ofm->get_ofse_examiner($ofse->class_group_id);
                if ($mbo_ofse_examiner) {
                    foreach ($mbo_ofse_examiner as $examiner) {
                        $s_examiner_name = $this->Pdm->retrieve_title($examiner->personal_data_id);
                        if (!in_array($s_examiner_name, $a_examiner_ofse)) {
                            array_push($a_examiner_ofse, $s_examiner_name);
                        }
                    }
                    $s_examiner_ofse = implode(" / ", $a_examiner_ofse);
                }
                $i_count_examiner = ($i_count_examiner < count($a_examiner_ofse)) ? count($a_examiner_ofse) : $i_count_examiner;
                
                $ofse->examiner_ofse = $s_examiner_ofse;
                $ofse->a_examiner_ofse = $a_examiner_ofse;
            }
        }
        return $mbo_ofse_lists;
    }

    public function get_class_details($s_class_group_id)
    {
        $a_examiner_lists = false;
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $mbo_examiner_data = $this->Cgm->get_class_group_lecturer(array('class_group_id' => $s_class_group_id));
        if ($mbo_examiner_data) {
            $a_examiner_lists = array();
            foreach ($mbo_examiner_data as $examiner) {
                // var_dump($examiner->personal_data_id);exit;
                $s_examiner_name = $this->Pdm->retrieve_title($examiner->personal_data_id);
                array_push($a_examiner_lists, $s_examiner_name);
            }
        }

        // $mbo_student_lists = $this->Cgm->get_class_group_student($s_class_group_id, array('st.student_status' => 'active', 'score_approval' => 'approved'));
        $mbo_student_lists = $this->Ofm->get_class_group_ofse_student($s_class_group_id, array('score_approval' => 'approved'));
        
        $mbo_class_detail = array(
            'subject' => $this->Cgm->get_class_group_subject($s_class_group_id)[0]->subject_name,
            'examiner_lists' => $a_examiner_lists,
            'examiner_details' => $mbo_examiner_data,
            'student_count' => ($mbo_student_lists) ? count($mbo_student_lists) : 0
        );

        return $mbo_class_detail;
    }

    public function get_ofse_schedule()
    {
        if ($this->input->is_ajax_request()) {
            $s_ofse_period_id = $this->input->post('ofse_period_id');

            $mba_schedule = $this->Ofm->get_ofse_shecdule_date($s_ofse_period_id);
            if ($mba_schedule) {
                foreach ($mba_schedule as $o_schedule) {
                    $mba_exam_participant = $this->Ofm->get_ofse_participant_exam_date([
                        'ox.exam_date' => $o_schedule->exam_date,
                        'ox.exam_room' => $o_schedule->exam_room
                    ]);

                    $o_schedule->exam_day = date('D', strtotime($o_schedule->exam_date));
                    $o_schedule->exam_date_view = date('d F Y', strtotime($o_schedule->exam_date));
                    $o_schedule->exam_participant = $mba_exam_participant;
                    $o_schedule->exam_participant_count = ($mba_exam_participant) ? count($mba_exam_participant) : 0;
                }
            }
            print json_encode(['data' => $mba_schedule]);
        }
    }

    public function get_ofse_subject_participant()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_ofse_period_id = $this->input->post('ofse_period_id');
            $mba_ofse_subject_participant_data = $this->Ofm->get_ofse_structure($s_ofse_period_id, [
                'sc.student_id' => $s_student_id
            ]);
            print json_encode(['data' => $mba_ofse_subject_participant_data]);
        }
    }

    public function submit_schedule()
    {
        if ($this->input->is_ajax_request()) {
            $post = $this->input->post();
            
            if ((empty($post['input_exam_date'])) OR (empty($post['input_exam_room']))) {
                $a_return = ['code' => 1, 'message' => 'Please input date or room!'];
            }
            else {
                $s_ofse_period_id = $post['ofse_period_id'];
                $s_ofse_exam_date = $post['input_exam_date'];
                $s_ofse_exam_room = $post['input_exam_room'];
                $s_exam_zoomid = $post['input_exam_zoomid'];
                $s_exam_zoompasscode = $post['input_exam_zoompasscode'];
                $a_ofse_exam_score_id = $post['input_exam_score_id'];

                $a_return = $this->Ofm->submit_bulk_ofse_schedule(
                    $s_ofse_period_id,
                    $s_ofse_exam_date,
                    $s_ofse_exam_room,
                    $s_exam_zoomid,
                    $s_exam_zoompasscode,
                    $a_ofse_exam_score_id,
                    $post['input_exam_time_start'],
                    $post['input_exam_time_end']
                );
            }
            // print('<pre>');var_dump($post['input_exam_score_id']);

            print json_encode($a_return);
        }
    }

    public function get_exam_room_participant()
    {
        if ($this->input->is_ajax_request()) {
            $s_ofse_period_id = $this->input->post('ofse_period_id');
            $s_exam_room = $this->input->post('exam_room');
            $s_exam_date = $this->input->post('exam_date');

            $mba_ofse_exam_data = $this->Ofm->get_ofse_exam_data([
                'oe.exam_date' => $s_exam_date,
                'oe.exam_room' => $s_exam_room,
                'sc.ofse_period_id' => $s_ofse_period_id
            ]);

            if ($mba_ofse_exam_data) {
                foreach ($mba_ofse_exam_data as $o_exam) {
                    $a_examiner_name = [];
                    $mba_student_examiner = $this->Ofm->get_student_ofse_examiner($o_exam->score_id);
                    if ($mba_student_examiner) {
                        foreach ($mba_student_examiner as $o_examiner) {
                            $s_examiner_name = $this->Pdm->retrieve_title($o_examiner->personal_data_id);
                            array_push($a_examiner_name, $s_examiner_name);
                        }
                    }

                    $o_exam->list_examiner = implode('|', $a_examiner_name);
                    $o_exam->examiner_data = $mba_student_examiner;
                }
            }

            print json_encode(['data' => $mba_ofse_exam_data]);
        }
    }

    public function manage_ofse_schedule($s_ofse_period_id, $s_ofse_date = false, $s_ofse_room = false)
    {
        $mbo_ofse_period_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id])[0];
        if ($mbo_ofse_period_data) {
            $mba_ofse_participant = $this->Ofm->get_ofse_participant_data([
                'sc.ofse_period_id' => $s_ofse_period_id
            ]);

            $mba_ofse_exam_data = false;
            if ($s_ofse_date AND $s_ofse_room) {
                $mba_ofse_exam_data = $this->Ofm->get_ofse_exam_data([
                    'oe.exam_date' => $s_ofse_date,
                    'oe.exam_room' => $s_ofse_room,
                    'sc.ofse_period_id' => $s_ofse_period_id
                ]);
            }
            // print('<pre>');var_dump($mba_ofse_exam_data);exit;

            $this->a_page_data['ofse_period_id'] = $s_ofse_period_id;
            $this->a_page_data['ofse_data'] = $mbo_ofse_period_data;
            $this->a_page_data['ofse_date'] = $s_ofse_date;
            $this->a_page_data['ofse_room'] = $s_ofse_room;
            $this->a_page_data['ofse_participant'] = $mba_ofse_participant;
            $this->a_page_data['ofse_exam_data'] = $mba_ofse_exam_data;

            // print('<pre>');var_dump($this->a_page_data['ofse_participant']);exit;
            $this->a_page_data['body'] = $this->load->view('academic/ofse/form/form_ofse_schedule', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }else{
            show_404();
        }
    }

    public function ofse_schedule($s_ofse_period_id)
    {
        $mbo_ofse_period_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id])[0];
        if ($mbo_ofse_period_data) {
            $this->a_page_data['ofse_period_id'] = $s_ofse_period_id;
            $this->a_page_data['ofse_data'] = $mbo_ofse_period_data;
            $this->a_page_data['body'] = $this->load->view('academic/ofse/table/ofse_schedule', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }else{
            show_404();
        }
    }

    public function ofse_student_member($s_ofse_period_id)
    {
        $mbo_ofse_period_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id])[0];
        if ($mbo_ofse_period_data) {
            $this->a_page_data['ofse_period_id'] = $s_ofse_period_id;
            $this->a_page_data['ofse_data'] = $mbo_ofse_period_data;
            $this->a_page_data['body'] = $this->load->view('academic/ofse/table/ofse_member', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }else{
            show_404();
        }
    }

    public function student_krs($s_ofse_period_id, $s_student_id)
    {
        $mbo_ofse_period_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id])[0];
        $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);

        if ($this->input->is_ajax_request()) {
            $a_clause_data = [
                'sc.score_approval' => 'approved',
                'sc.ofse_period_id' => $s_ofse_period_id,
                'sc.student_id' => $s_student_id
            ];

            $mba_score_data = $this->Scm->get_score_data_transcript($a_clause_data, $a_semester_id = [17]);
            if ($mba_score_data) {
                foreach ($mba_score_data as $o_score) {
                    if (!is_null($o_score->class_group_id)) {
                        $mbo_offered_subject_data = $this->Ofm->get_ofse_lists_subject(['cgs.class_group_id' => $o_score->class_group_id])[0];
                    }else{
                        $mbo_offered_subject_data =  $this->Osm->get_offered_subject_lists_filtered([
                            'dos.study_program_id' => $o_score->student_study_program_id,
                            'dos.ofse_period_id' => $s_ofse_period_id,
                            'dos.curriculum_subject_id' => $o_score->curriculum_subject_id,
                            'rcs.curriculum_subject_category' => 'ofse',
                            'rcs.semester_id' => 17
                        ])[0];
                    }

                    $mba_score_examiner = $this->Ofm->get_subject_examiner([
                        'oe.score_id' => $o_score->score_id
                    ]);
                    $s_examiner_name = '';
                    if ($mba_score_examiner) {
                        $a_examiner_name = [];
                        foreach ($mba_score_examiner as $key => $o_examiner) {
                            $s_examiner_name = $this->Pdm->retrieve_title($o_examiner->personal_data_id);
                            $o_examiner->examiner_name = $s_examiner_name;
                            if (!in_array($s_examiner_name, $a_examiner_name)) {
                                array_push($a_examiner_name, $s_examiner_name);
                            }
                        }
                        $s_examiner_name = implode(' / ', $a_examiner_name);
                    }

                    if ($mbo_offered_subject_data) {
                        $o_score->study_program_abbreviation = ($mbo_offered_subject_data->study_program_abbreviation == 'COS') ? 'CSE' : $mbo_offered_subject_data->study_program_abbreviation;
                        $o_score->ofse_status = ucwords(str_replace('_', ' ', $mbo_offered_subject_data->ofse_status));
                    }else{
                        $mbo_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $o_score->student_study_program_id])[0];
                        $o_score->ofse_status = '-';
                    }

                    $mba_ofse_exam = $this->General->get_where('dt_ofse_exam', ['score_id' => $o_score->score_id]);
                    $s_schedule = 'N/A';
                    if ($mba_ofse_exam) {
                        $o_exam = $mba_ofse_exam[0];
                        $s_schedule = $o_exam->exam_room.' / '.date('d F Y', strtotime($o_exam->exam_date)).' '.date('H:i', strtotime($o_exam->exam_time_start)).'-'.date('H:i', strtotime($o_exam->exam_time_end));
                    }

                    $mba_is_fill_eval = $this->Ofm->get_evaluation_examiner([
                        'om.score_id' => $o_score->score_id
                        // 'oq.has_pick' => 'true'
                    ]);

                    $o_score->ofse_has_exam = ($mba_is_fill_eval) ? true : false;
                    $o_score->ofse_subject_code = $this->generate_subject_code($o_score->subject_name, $s_ofse_period_id);
                    $o_score->ofse_examiner = $mba_score_examiner;
                    $o_score->ofse_schedule = $s_schedule;
                    $o_score->ofse_examiner_name = $s_examiner_name;
                    $o_score->ofse_exam_date = ($mba_ofse_exam) ? $mba_ofse_exam[0]->exam_date : false;
                    $o_score->ofse_exam_time_start = ($mba_ofse_exam) ? $mba_ofse_exam[0]->exam_time_start : false;
                    $o_score->ofse_exam_time_end = ($mba_ofse_exam) ? $mba_ofse_exam[0]->exam_time_end : false;
                }
            }
            print json_encode(['code' => 0, 'data' => $mba_score_data]);
        }else{
            if (($mbo_ofse_period_data) AND ($mbo_student_data)) {
                $this->a_page_data['ofse_data'] = $mbo_ofse_period_data;
                $this->a_page_data['student_data'] = $mbo_student_data;
                $this->a_page_data['body'] = $this->load->view('academic/ofse/table/ofse_student_krs', $this->a_page_data, true);
                $this->load->view('layout', $this->a_page_data);
            }else{
                show_404();
            }
        }
    }

    public function save_ofse_score()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('academic/Score_model', 'Scm');
            $s_score_id = $this->input->post('score_id');

            $this->form_validation->set_rules('ofse_score_1', 'Score Examiner 1', 'numeric');
            $this->form_validation->set_rules('ofse_score_2', 'Score Examiner 2', 'numeric');
            $this->form_validation->set_rules('ofse_score_3', 'Score Examiner 3', 'numeric');

            if ($s_score_id == '') {
                $a_rtn = array('code' => 1, 'message' => 'Error retrieve score data id');
            }else if ($this->form_validation->run()) {
                $f_score_examiner_1 = (float) set_value('ofse_score_1');
                $f_score_examiner_2 = (float) set_value('ofse_score_2');
                $f_score_examiner_3 = (float) set_value('ofse_score_3');
                $a_score_examiner_value = [$f_score_examiner_1, $f_score_examiner_2, $f_score_examiner_3];

                $f_ofse_score = ($f_score_examiner_1 + $f_score_examiner_2 + $f_score_examiner_3)/3;
                if ($f_ofse_score == 0) {
                    $a_rtn = array('code' => 2, 'message' => 'All score is empty or 0');
                }
                else {
                    $a_score_examiner = array(
                        'score_examiner_1' => set_value('ofse_score_1'),
                        'score_examiner_2' => set_value('ofse_score_2'),
                        'score_examiner_3' => set_value('ofse_score_3')
                    );

                    $i_count_fill_score = 0;
                    for ($i=0; $i < count($a_score_examiner_value); $i++) { 
                        if (($a_score_examiner_value[$i] != '') AND ($a_score_examiner_value[$i] != '0')) {
                            $i_count_fill_score++;
                        }
                    }

                    $f_ofse_score = array_sum($a_score_examiner_value) / $i_count_fill_score;
                    $a_score_data = array(
                        'score_quiz' => $f_ofse_score,
                        'score_final_exam' => $f_ofse_score,
                        'score_sum' => $f_ofse_score,
                        'score_grade' => $this->grades->get_grade($f_ofse_score),
                        'score_approval' => 'approved',
                        'score_examiner' => json_encode($a_score_examiner)
                    );
    
                    if ($this->Scm->save_data($a_score_data, array('score_id' => $s_score_id))) {
                        $a_rtn = array('code' => 0, 'message' => 'Success');
                    }else{
                        $a_rtn = array('code' => 1, 'message' => 'Error saving score data');
                    }
                }
            }else{
                $a_rtn = array('code' => 1, 'message' => $this->validation_errors('<li>', '</li>'));
            }

            print(json_encode($a_rtn));exit;
        }
    }

    public function generate_ofse_student_old()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');

            $a_class_group_id = array();
            $mbo_class_lists = $this->Cgm->get_class_group_lists(array('academic_year_id' => $s_academic_year_id, 'semester_type_id' => $s_semester_type_id));
            if ($mbo_class_lists) {
                foreach ($mbo_class_lists as $class) {
                    array_push($a_class_group_id, $class->class_group_id);
                }
            }

            if (count($a_class_group_id) > 0) {
                // $mbo_student_in_class = $this->Cgm->get_class_group_student(false, $a_class_group_id, 'class_group_id');
                $mbo_student_in_class = $this->Ofm->get_class_group_ofse_student(false, $a_class_group_id, 'class_group_id');

                if ($mbo_student_in_class) {
                    $s_file = 'ofse_student_template.csv';
                    $s_path = APPPATH.'/uploads/templates/'.$s_file;
                    $fp = fopen($s_path, 'w+');

                    fputcsv($fp, array(
                        "Student ID",
                        "Student Name",
                        "Study Program",
                        "IULI's email"
                    ), ';');

                    foreach ($mbo_student_in_class as $student) {
                        fputcsv($fp, array(
                            $student->student_number,
                            $student->personal_data_name,
                            $student->study_program_abbreviation,
                            $student->student_email
                        ), ';');
                    }

                    $a_rtn = array('code' => 0, 'file' => $s_file);
                }else{
                    $a_rtn = array('code' => 1, 'message' => 'Student not found');
                }
            }else {
                $a_rtn = array('code' => 1, 'message' => 'Class not found');
            }

            print(json_encode($a_rtn));
        }
    }

    public function generate_ofse_result()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_study_program_id = $this->input->post('study_program_id');
            $s_ofse_period_id = $this->input->post('ofse_period_id');

            $a_filter_data = [
                'sc.academic_year_id' => $s_academic_year_id,
                'sc.semester_type_id' => $s_semester_type_id,
                'st.study_program_id' => $s_study_program_id,
                'sc.ofse_period_id' => $s_ofse_period_id,
                'sc.score_approval' => 'approved',
                'sc.semester_id' => '17'
            ];

            foreach ($a_filter_data as $key => $value) {
                if ($value == '') {
                    unset($a_filter_data[$key]);
                }
            }

            if (strtolower($s_study_program_id) == 'all') {
                unset($a_filter_data['ofs.study_program_id']);
            }

            $mbo_ofse_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id])[0];
            $mba_ofse_member = $this->Ofm->get_ofse_member($a_filter_data, true);
            if (($mbo_ofse_data) AND ($mba_ofse_member)) {
                $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
                
                $s_period_name = str_replace(' ', '-', $mbo_ofse_data->ofse_period_name);
                $s_file_name = 'OFSE_Result_'.$s_period_name;
                $s_filename = $s_file_name.'.xlsx';
                $s_file_path = APPPATH."uploads/academic/ofse/".$s_period_name."/";
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $o_spreadsheet = IOFactory::load($s_template_path);
                $o_sheet = $o_spreadsheet->getActiveSheet();
                $o_spreadsheet->getProperties()
                    ->setTitle($s_file_name)
                    ->setCreator("IULI OFSE Result")
                    ->setCategory("OFSE Result ".date('Y-m-d'));

                $o_sheet->setCellValue('A1', "List of OFSE Result");
                $o_sheet->setCellValue('A2', "Periode ".$mbo_ofse_data->ofse_period_name);

                $i_row = 4;
                $o_sheet->setCellValue('A'.$i_row, "Student ID");
                $o_sheet->setCellValue('B'.$i_row, "Student Name");
                $o_sheet->setCellValue('C'.$i_row, "Faculty");
                $o_sheet->setCellValue('D'.$i_row, "Study Program");
                $o_sheet->setCellValue('E'.$i_row, "OFSE Subject Status");
                $o_sheet->setCellValue('F'.$i_row, "Subject Code");
                $o_sheet->setCellValue('G'.$i_row, "Subject Name");
                $o_sheet->setCellValue('H'.$i_row, "Examiner 1");
                $o_sheet->setCellValue('I'.$i_row, "Examiner 2");
                $o_sheet->setCellValue('J'.$i_row, "Final Score");
                $o_sheet->setCellValue('K'.$i_row, "Grade");
                $i_row++;

                foreach ($mba_ofse_member as $o_member) {
                    $a_clause_data = [
                        'sc.student_id' => $o_member->student_id,
                        'sc.semester_id' => '17',
                        'sc.ofse_period_id' => $s_ofse_period_id,
                        'sc.score_approval' => 'approved'
                    ];

                    $mba_student_subject = $this->Scm->get_score_data_transcript($a_clause_data, $a_semester_id = [17]);
                    if ($mba_student_subject) {
                        foreach ($mba_student_subject as $o_subject) {
                            $mbo_offered_subject_data = $this->General->get_where('dt_offered_subject', [
                                'ofse_period_id' => $s_ofse_period_id,
                                'study_program_id' => $o_member->study_program_id,
                                'academic_year_id' => $o_subject->academic_year_id,
                                'semester_type_id' => $o_subject->semester_type_id,
                                'curriculum_subject_id' => $o_subject->curriculum_subject_id
                            ])[0];

                            if (!$mbo_offered_subject_data) {
                                $a_return = ['code' => 1, 'message' => 'Error retrieving subject data!'];
                                print json_encode($a_return);exit;
                            }

                            $s_examiner_1 = '-';
                            $s_examiner_2 = '-';
                            if (!is_null($o_subject->score_examiner)) {
                                // print('<pre>');var_dump($o_subject->score_examiner);exit;
                                $is_json = json_decode($o_subject->score_examiner);
                                
                                if (!is_null($is_json)) {
                                    if (isset($is_json->score_examiner_1)) {
                                        $s_examiner_1 = $is_json->score_examiner_1;
                                    }
                                    
                                    if (isset($is_json->score_examiner_2)) {
                                        $s_examiner_2 = $is_json->score_examiner_2;
                                    }
                                }
                            }

                            $o_sheet->setCellValue('A'.$i_row, $o_member->student_number);
                            $o_sheet->setCellValue('B'.$i_row, $o_member->personal_data_name);
                            $o_sheet->setCellValue('C'.$i_row, $o_member->faculty_abbreviation);
                            $o_sheet->setCellValue('D'.$i_row, (($o_member->study_program_abbreviation == 'COS') ? 'CSE' : $o_member->study_program_abbreviation));
                            $o_sheet->setCellValue('E'.$i_row, ucwords(str_replace('_', ' ', $mbo_offered_subject_data->ofse_status)));
                            $o_sheet->setCellValue('F'.$i_row, $o_subject->subject_code);
                            $o_sheet->setCellValue('G'.$i_row, $o_subject->subject_name);
                            $o_sheet->setCellValue('H'.$i_row, $s_examiner_1);
                            $o_sheet->setCellValue('I'.$i_row, $s_examiner_2);
                            $o_sheet->setCellValue('J'.$i_row, $o_subject->score_sum);
                            $o_sheet->setCellValue('K'.$i_row, $o_subject->score_grade);
                            $i_row++;
                        }
                    }
                }

                $styleArray = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => array('argb' => '00000000'),
                        )
                    )
                );

                $o_sheet->getStyle('A1:K'.$i_row)->getFont()->setSize(11);
                $o_sheet->mergeCells('A1:K1');
                $o_sheet->mergeCells('A2:K2');
                $o_sheet->getStyle('A1:A2')->getFont()->setBold( true );
                $o_sheet->getStyle('A4:K'.($i_row-1))->applyFromArray($styleArray);

                $c_max_cols_absence_alt = 'A';
                for ($i = 0; $i <= 11; $i++) {
                    $o_sheet->getColumnDimension($c_max_cols_absence_alt)->setAutoSize(true);
                    ++$c_max_cols_absence_alt;
                }

                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
                $o_writer->save($s_file_path.$s_filename);
                $o_spreadsheet->disconnectWorksheets();
                unset($o_spreadsheet);

                $a_return = ['code' => 0, 'message' => 'Success', 'file' => $s_filename];
            }else{
                $a_return = ['code' => 1, 'message' => 'Ofse Structure not found!'];
            }

            print json_encode($a_return);exit;
        }
    }

    public function generate_ofse_structure()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_study_program_id = $this->input->post('study_program_id');
            $s_ofse_period_id = $this->input->post('ofse_period_id');

            $a_filter_data = [
                'sc.academic_year_id' => $s_academic_year_id,
                'sc.semester_type_id' => $s_semester_type_id,
                'st.study_program_id' => $s_study_program_id,
                'sc.ofse_period_id' => $s_ofse_period_id,
                'sc.score_approval' => 'approved',
                'sc.semester_id' => '17'
            ];

            foreach ($a_filter_data as $key => $value) {
                if ($value == '') {
                    unset($a_filter_data[$key]);
                }
            }

            if (strtolower($s_study_program_id) == 'all') {
                unset($a_filter_data['ofs.study_program_id']);
            }

            $mbo_ofse_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id])[0];
            $mba_ofse_member = $this->Ofm->get_ofse_member($a_filter_data, true);

            if (($mbo_ofse_data) AND ($mba_ofse_member)) {
                $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
                
                $s_period_name = str_replace(' ', '-', $mbo_ofse_data->ofse_period_name);
                $s_file_name = 'OFSE_Structure_'.$s_period_name;
                $s_filename = $s_file_name.'.xlsx';
                $s_file_path = APPPATH."uploads/academic/ofse/".$s_period_name."/";
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $o_spreadsheet = IOFactory::load($s_template_path);
                $o_sheet = $o_spreadsheet->getActiveSheet();
                $o_spreadsheet->getProperties()
                    ->setTitle($s_file_name)
                    ->setCreator("IULI OFSE Structure")
                    ->setCategory("OFSE Structure ".date('Y-m-d'));

                $o_sheet->setCellValue('A1', "List of OFSE Structure");
                $o_sheet->setCellValue('A2', "Periode ".$mbo_ofse_data->ofse_period_name);

                $i_row = 4;
                $o_sheet->setCellValue('A'.$i_row, "Student ID");
                $o_sheet->setCellValue('B'.$i_row, "Student Name");
                $o_sheet->setCellValue('C'.$i_row, "Faculty");
                $o_sheet->setCellValue('D'.$i_row, "Study Program");
                $o_sheet->setCellValue('E'.$i_row, "OFSE Subject Status");
                $o_sheet->setCellValue('F'.$i_row, "Subject Code");
                $o_sheet->setCellValue('G'.$i_row, "Subject Name");
                $i_row++;

                foreach ($mba_ofse_member as $o_member) {
                    $a_clause_data = [
                        'sc.student_id' => $o_member->student_id,
                        'sc.semester_id' => '17',
                        'sc.ofse_period_id' => $s_ofse_period_id,
                        'sc.score_approval' => 'approved'
                    ];

                    $mba_student_subject = $this->Scm->get_score_data_transcript($a_clause_data, $a_semester_id = [17]);
                    if ($mba_student_subject) {
                        foreach ($mba_student_subject as $o_subject) {
                            $mbo_offered_subject_data = $this->General->get_where('dt_offered_subject', [
                                'ofse_period_id' => $s_ofse_period_id,
                                'study_program_id' => $o_member->study_program_id,
                                'academic_year_id' => $o_subject->academic_year_id,
                                'semester_type_id' => $o_subject->semester_type_id,
                                'curriculum_subject_id' => $o_subject->curriculum_subject_id
                            ])[0];

                            if (!$mbo_offered_subject_data) {
                                $a_return = ['code' => 1, 'message' => 'Error retrieving subject data!'.$o_subject->curriculum_subject_id];
                                print json_encode($a_return);exit;
                            }

                            $o_sheet->setCellValue('A'.$i_row, $o_member->student_number);
                            $o_sheet->setCellValue('B'.$i_row, $o_member->personal_data_name);
                            $o_sheet->setCellValue('C'.$i_row, $o_member->faculty_abbreviation);
                            $o_sheet->setCellValue('D'.$i_row, (($o_member->study_program_abbreviation == 'COS') ? 'CSE' : $o_member->study_program_abbreviation));
                            $o_sheet->setCellValue('E'.$i_row, ucwords(str_replace('_', ' ', $mbo_offered_subject_data->ofse_status)));
                            $o_sheet->setCellValue('F'.$i_row, $o_subject->subject_code);
                            $o_sheet->setCellValue('G'.$i_row, $o_subject->subject_name);
                            $i_row++;
                            // 16c31e03-93fe-4a38-ae37-a8028968ff03
                        }
                    }
                }

                $styleArray = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => array('argb' => '00000000'),
                        )
                    )
                );

                $o_sheet->getStyle('A1:G'.$i_row)->getFont()->setSize(11);
                $o_sheet->mergeCells('A1:G1');
                $o_sheet->mergeCells('A2:G2');
                $o_sheet->getStyle('A1:A2')->getFont()->setBold( true );
                $o_sheet->getStyle('A4:G'.($i_row-1))->applyFromArray($styleArray);

                $c_max_cols_absence_alt = 'A';
                for ($i = 0; $i <= 7; $i++) {
                    $o_sheet->getColumnDimension($c_max_cols_absence_alt)->setAutoSize(true);
                    ++$c_max_cols_absence_alt;
                }

                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
                $o_writer->save($s_file_path.$s_filename);
                $o_spreadsheet->disconnectWorksheets();
                unset($o_spreadsheet);

                $a_return = ['code' => 0, 'message' => 'Success', 'file' => $s_filename];
            }else{
                $a_return = ['code' => 1, 'message' => 'Ofse Structure not found!'];
            }

            print json_encode($a_return);exit;
        }
    }

    public function generate_ofse_student()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_study_program_id = $this->input->post('study_program_id');
            $s_ofse_period_id = $this->input->post('ofse_period_id');

            $a_filter_data = [
                'sc.academic_year_id' => $s_academic_year_id,
                'sc.semester_type_id' => $s_semester_type_id,
                'st.study_program_id' => $s_study_program_id,
                'sc.ofse_period_id' => $s_ofse_period_id
            ];

            foreach ($a_filter_data as $key => $value) {
                if ($value == '') {
                    unset($a_filter_data[$key]);
                }
            }

            if (strtolower($s_study_program_id) == 'all') {
                unset($a_filter_data['ofs.study_program_id']);
            }

            $mbo_ofse_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id])[0];
            $mba_ofse_student_list = $this->Ofm->get_ofse_member($a_filter_data, true);

            if (($mbo_ofse_data) AND ($mba_ofse_student_list)) {
                $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';

                $s_period_name = str_replace(' ', '-', $mbo_ofse_data->ofse_period_name);
                $s_file_name = 'OFSE_Student_'.$s_period_name;
                $s_filename = $s_file_name.'.xlsx';
                $s_file_path = APPPATH."uploads/academic/ofse/".$s_period_name."/";
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $o_spreadsheet = IOFactory::load($s_template_path);
                $o_sheet = $o_spreadsheet->getActiveSheet();
                $o_spreadsheet->getProperties()
                    ->setTitle($s_file_name)
                    ->setCreator("IULI OFSE Student")
                    ->setCategory("OFSE Student ".date('Y-m-d'));

                $o_sheet->setCellValue('A1', "List of OFSE Student");
                $o_sheet->setCellValue('A2', "Periode ".$mbo_ofse_data->ofse_period_name);

                $i_row = 4;
                $o_sheet->setCellValue('A'.$i_row, "Student ID");
                $o_sheet->setCellValue('B'.$i_row, "Student Name");
                $o_sheet->setCellValue('C'.$i_row, "Faculty");
                $o_sheet->setCellValue('D'.$i_row, "Study Program");
                $o_sheet->setCellValue('E'.$i_row, "Student Email");
                $i_row++;

                foreach ($mba_ofse_student_list as $o_student) {
                    $o_sheet->setCellValue('A'.$i_row, '="'.$o_student->student_number.'"');
                    $o_sheet->setCellValue('B'.$i_row, $o_student->personal_data_name);
                    $o_sheet->setCellValue('C'.$i_row, $o_student->faculty_abbreviation);
                    $o_sheet->setCellValue('D'.$i_row, (($o_student->study_program_abbreviation == 'COS') ? 'CSE' : $o_student->study_program_abbreviation));
                    $o_sheet->setCellValue('E'.$i_row, $o_student->student_email);
                    $i_row++;
                }

                $styleArray = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => array('argb' => '00000000'),
                        )
                    )
                );

                $o_sheet->getStyle('A1:E'.$i_row)->getFont()->setSize(11);
                $o_sheet->mergeCells('A1:E1');
                $o_sheet->mergeCells('A2:E2');
                $o_sheet->getStyle('A1:A2')->getFont()->setBold( true );
                $o_sheet->getStyle('A4:E'.($i_row-1))->applyFromArray($styleArray);

                $c_max_cols_absence_alt = 'A';
                for ($i = 0; $i <= 5; $i++) {
                    $o_sheet->getColumnDimension($c_max_cols_absence_alt)->setAutoSize(true);
                    ++$c_max_cols_absence_alt;
                }

                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
                $o_writer->save($s_file_path.$s_filename);
                $o_spreadsheet->disconnectWorksheets();
                unset($o_spreadsheet);

                $a_return = ['code' => 0, 'message' => 'Success', 'file' => $s_filename];
            }else{
                $a_return = ['code' => 1, 'message' => 'Ofse Student not found!'];
            }

            print json_encode($a_return);
        }
    }
    
    public function generate_ofse_subject()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $s_study_program_id = $this->input->post('study_program_id');
            $s_ofse_period_id = $this->input->post('ofse_period_id');

            $a_filter_data = [
                'ofs.academic_year_id' => $s_academic_year_id,
                'ofs.semester_type_id' => $s_semester_type_id,
                'ofs.study_program_id' => $s_study_program_id,
                'ofs.ofse_period_id' => $s_ofse_period_id
            ];
            // print('<pre>');var_dump($a_filter_data);exit;

            foreach ($a_filter_data as $key => $value) {
                if ($value == '') {
                    unset($a_filter_data[$key]);
                }
            }

            if (strtolower($s_study_program_id) == 'all') {
                unset($a_filter_data['ofs.study_program_id']);
            }

            $mbo_ofse_data = $this->General->get_where('dt_ofse', ['ofse_period_id' => $s_ofse_period_id])[0];
            $mba_ofse_subject_list = $this->Ofm->get_ofse_lists_subject($a_filter_data);
            if (($mbo_ofse_data) AND ($mba_ofse_subject_list)) {
                // $mbo_semester_type_academic = $this->General->get_where('ref_semester_type', ['semester_type_id' => $s_semester_type_id])[0];
                // if (!$mbo_semester_type_academic) {
                //     $a_return = ['code' => 1, 'message' => 'Error retrieve data!'];
                //     print json_encode($a_return);exit;
                // }

                $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
                // $s_semester_academic = $s_academic_year_id.'-'.$mbo_semester_type_academic->semester_type_master;
                // $s_semester_ofse = $s_academic_year_id.'-'.$s_semester_type_id;
                $s_period_name = str_replace(' ', '-', $mbo_ofse_data->ofse_period_name);
                $s_file_name = 'OFSE_Subject_'.$s_period_name;
                $s_filename = $s_file_name.'.xlsx';
                $s_file_path = APPPATH."uploads/academic/ofse/".$s_period_name."/";
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $o_spreadsheet = IOFactory::load($s_template_path);
                $o_sheet = $o_spreadsheet->getActiveSheet();
                $o_spreadsheet->getProperties()
                    ->setTitle($s_file_name)
                    ->setCreator("IULI OFSE Subject")
                    ->setCategory("OFSE Subject ".date('Y-m-d'));

                $o_sheet->setCellValue('A1', "List of OFSE Subject");
                $o_sheet->setCellValue('A2', "Periode ".$mbo_ofse_data->ofse_period_name); // harus ambil dari dt_ofse

                $i_row = 4;
                $o_sheet->setCellValue('A'.$i_row, "Study Program");
                $o_sheet->setCellValue('B'.$i_row, "Subject Code");
                $o_sheet->setCellValue('C'.$i_row, "Subject Name");
                $o_sheet->setCellValue('D'.$i_row, "OFSE Subject Status");
                $o_sheet->setCellValue('E'.$i_row, "Examiner 1");
                $o_sheet->setCellValue('F'.$i_row, "Examiner 2");

                $i_row++;
                foreach ($mba_ofse_subject_list as $o_subject) {
                    $s_prodi_abbr = ($o_subject->study_program_abbreviation == 'COS') ? 'CSE' : $o_subject->study_program_abbreviation;

                    $o_sheet->setCellValue('A'.$i_row, $s_prodi_abbr);
                    $o_sheet->setCellValue('B'.$i_row, $o_subject->subject_code);
                    $o_sheet->setCellValue('C'.$i_row, $o_subject->subject_name);
                    $o_sheet->setCellValue('D'.$i_row, ucwords(str_replace('_', ' ', $o_subject->ofse_status)));
                    $o_sheet->setCellValue('E'.$i_row, 'N/A');
                    $o_sheet->setCellValue('F'.$i_row, 'N/A');

                    if (!is_null($o_subject->class_group_id)) {
                        $mba_ofse_examiner = $this->Ofm->get_ofse_examiner($o_subject->class_group_id);

                        if ($mba_ofse_examiner) {
                            $c_col = 'D';
                            // $i_examiner = 1;
                            foreach ($mba_ofse_examiner as $o_examiner) {
                                $s_examiner = $this->Pdm->retrieve_title($o_examiner->personal_data_id);
                                $o_sheet->setCellValue(++$c_col.$i_row, $s_examiner);
                            }
                        }
                    }

                    $i_row++;
                }

                $styleArray = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => array('argb' => '00000000'),
                        )
                    )
                );

                $o_sheet->getStyle('A1:F'.$i_row)->getFont()->setSize(11);
                $o_sheet->mergeCells('A1:F1');
                $o_sheet->mergeCells('A2:F2');
                $o_sheet->getStyle('A1:F2')->getFont()->setBold( true );
                $o_sheet->getStyle('A4:F'.($i_row-1))->applyFromArray($styleArray);

                $c_max_cols_absence_alt = 'A';
                for ($i = 0; $i <= 6; $i++) {
                    $o_sheet->getColumnDimension($c_max_cols_absence_alt)->setAutoSize(true);
                    ++$c_max_cols_absence_alt;
                }

                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
                $o_writer->save($s_file_path.$s_filename);
                $o_spreadsheet->disconnectWorksheets();
                unset($o_spreadsheet);

                $a_return = ['code' => 0, 'message' => 'Success', 'file' => $s_filename];
            }else{
                $a_return = ['code' => 1, 'message' => 'Ofse subject not found!'];
            }

            print json_encode($a_return);
        }
    }

    public function generate_ofse_csv()
    {
        if ($this->input->is_ajax_request()) {
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');
            $this->load->model('Subject_model', 'Sbm');

            $mbo_ofse_lists = $this->Ofm->get_ofse_lists_subject(array('ofs.academic_year_id' => $s_academic_year_id, 'ofs.semester_type_id' => $s_semester_type_id));
            if ($mbo_ofse_lists) {
                foreach ($mbo_ofse_lists as $ofse_subject) {
                    $mbo_ofse_examiner = $this->Ofm->get_ofse_examiner($ofse_subject->class_group_id);
                    $i_examiner = 1;
                    if ($mbo_ofse_examiner) {
                        foreach ($mbo_ofse_examiner as $examiner) {
                            $key_examiner = 'examiner_'.$i_examiner;
                            $ofse_subject->$key_examiner = $this->Pdm->retrieve_title($examiner->personal_data_id);
                            $i_examiner++;
                        }
                    }
                }

                $s_file = 'ofse_subject_template.csv';
                $s_path = APPPATH.'/uploads/templates/'.$s_file;
                $fp = fopen($s_path, 'w+');

                fputcsv($fp, array(
                    'Study Program',
                    'Subject Code',
                    'Subject Name',
                    'Ofse Status',
                    'Examiner 1',
                    'Examiner 2'
                ), ';');

                foreach ($mbo_ofse_lists as $ofse_lists) {
                    fputcsv($fp, array(
                        $ofse_lists->study_program_name,
                        $ofse_lists->subject_code,
                        $ofse_lists->subject_name,
                        strtoupper(str_replace('_', ' ', $ofse_lists->ofse_status)),
                        (isset($ofse_lists->examiner_1)) ? $ofse_lists->examiner_1 : 'N/A',
                        (isset($ofse_lists->examiner_2)) ? $ofse_lists->examiner_2 : 'N/A'
                    ), ';');
                }

                $a_rtn = array('code' => 0, 'file' => $s_file);
            }else{
                $a_rtn = array('code' => 1, 'message' => 'OFSE Subject not found');
            }

            print json_encode($a_rtn);exit;
        }
    }

    public function generate_ofse_registration_structure()
    {
        if ($this->input->is_ajax_request()) {
            // $s_academic_year_id = 2019;
            // $s_semester_type_id = 4;
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');

            $mbo_student_in_subject = $this->Cgm->get_class_subject_student(array('cg.academic_year_id' => $s_academic_year_id, 'cg.semester_type_id' => $s_semester_type_id));

            if ($mbo_student_in_subject) {
                $s_file = 'ofse_registration_structure.csv';
                $s_path = APPPATH.'/uploads/templates/'.$s_file;
                $fp = fopen($s_path, 'w+');

                fputcsv($fp, array(
                    "Student ID",
                    "Student Name",
                    "Study Program",
                    "Status",
                    "Subject Code",
                    "Subject Name"
                ), ';');

                foreach ($mbo_student_in_subject as $ofse) {
                    fputcsv($fp, array(
                        $ofse->student_number,
                        $ofse->personal_data_name,
                        $ofse->study_program_abbreviation,
                        $ofse->ofse_status,
                        $ofse->subject_code,
                        $ofse->subject_name
                    ), ';');
                }

                $a_rtn = array('code' => 0, 'file' => $s_file);
            }else{
                $a_rtn = array('code' => 1, 'message' => 'Data not found');
            }

            print(json_encode($a_rtn));
        }
    }

    public function generate_ofse_result_old()
    {
        if ($this->input->is_ajax_request()) {
            // $s_academic_year_id = 2019;
            // $s_semester_type_id = 4;
            $s_academic_year_id = $this->input->post('academic_year_id');
            $s_semester_type_id = $this->input->post('semester_type_id');

            $mbo_student_in_subject = $this->Cgm->get_class_subject_student(array('cg.academic_year_id' => $s_academic_year_id, 'cg.semester_type_id' => $s_semester_type_id));
            
            if ($mbo_student_in_subject) {
                $s_file = 'ofse_result.csv';
                $s_path = APPPATH.'/uploads/templates/'.$s_file;
                $fp = fopen($s_path, 'w+');

                fputcsv($fp, array(
                    "Student ID",
                    "Student Name",
                    "Study Program",
                    "Status",
                    "Subject Code",
                    "Subject Name",
                    "Examiner 1",
                    "Examiner 2",
                    "Final Score",
                    "Grade"
                ), ';');

                foreach ($mbo_student_in_subject as $ofse) {
                    $s_score_examiner = $ofse->score_examiner;
                    $o_score_examiner = json_decode($s_score_examiner);
                    $s_result_examiner_1 = $o_score_examiner->score_examiner_1;
                    $s_result_examiner_2 = $o_score_examiner->score_examiner_2;

                    fputcsv($fp, array(
                        $ofse->student_number,
                        $ofse->personal_data_name,
                        $ofse->study_program_abbreviation,
                        $ofse->ofse_status,
                        $ofse->subject_code,
                        $ofse->subject_name,
                        $s_result_examiner_1,
                        $s_result_examiner_2,
                        $ofse->score_sum,
                        $ofse->score_grade
                    ), ';');
                }

                $a_rtn = array('code' => 0, 'file' => $s_file);
            }else{
                $a_rtn = array('code' => 1, 'message' => 'Data not found');
            }

            print(json_encode($a_rtn));
        }
    }

    public function fill_class_group()
    {
        $a_filter_data = array(
            'academic_year_id' => 2019,
            'semester_type_id' => 4
        );
        $mba_ofse_subject_lists = $this->Osm->get_offered_subject_filtered($a_filter_data);
        print('<pre>');
        if ($mba_ofse_subject_lists) {
            foreach ($mba_ofse_subject_lists as $offer_subject) {
                $class_group_subject = $this->Cgm->get_class_group_subject(false, array('dos.offered_subject_id' => $offer_subject->offered_subject_id));
                if (!$class_group_subject) {
                    $o_offered_subjet_data = $this->Osm->get_offered_subject_lists_filtered(array('offered_subject_id' => $offer_subject->offered_subject_id))[0];
                    $mbo_prodi_data = $this->Spm->get_study_program($offer_subject->study_program_id, false)[0];
                    $s_class_name = $o_offered_subjet_data->subject_name.' '.$mbo_prodi_data->study_program_abbreviation.' 20194';
                    $s_class_group_id = $this->uuid->v4();
                    $a_class_group_data = array(
                        'class_group_id' => $s_class_group_id,
                        'academic_year_id' => 2019,
                        'semester_type_id' => 4,
                        'class_group_name' => $s_class_name
                    );

                    if ($this->Cgm->save_data($a_class_group_data)) {
                        $a_class_group_subject = array(
                            'class_group_subject_id' => $this->uuid->v4(),
                            'class_group_id' => $s_class_group_id,
                            'offered_subject_id' => $offer_subject->offered_subject_id,
                            'date_added' => date('Y-m-d H:i:s')
                        );
                        if ($this->Cgm->save_class_group_subject($a_class_group_subject)) {
                            print('. ');
                        }else {
                            print('-');
                            print($offer_subject->offered_subject_id);
                        }
                    }else{
                        print($offer_subject->offered_subject_id);
                    }
                    // var_dump($offer_subject->offered_subject_id);
                    print('<br>');
                }
            }
        }
        
        // var_dump($mba_ofse_subject_lists);
    }

    public function generate_subject_code($s_subject_name, $s_ofse_period_id)
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
        $s_ofse_subject_code = $s_code.$replace_number.'-OFSE';

        $subject_check = $this->Ofm->get_ofse_subject_question([
            'oq.ofse_subject_code' => $s_ofse_subject_code,
            'oq.ofse_period_id' => $s_ofse_period_id
        ]);

        if (($subject_check) AND ($subject_check[0]->subject_name != $s_subject_name)) {
            $s_code = $subject_check[0]->subject_code;
            $s_ofse_subject_code = $s_code.'-OFSE';
        }
        
        return $s_ofse_subject_code;
    }
}
