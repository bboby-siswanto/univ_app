<?php
class Iuli_marketing extends App_core
{
    function __construct() {
        parent::__construct();
        $this->load->model('student/Student_model', 'Stm');
    }

    function index() {
        $this->a_page_data['body'] = $this->load->view('apps/public_marketing/candidate_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    function send_mail() {
        if ($this->input->is_ajax_request()) {
            $message = $this->input->post('message_body_input');
            $subject = $this->input->post('message_subject');
            $a_student = $this->input->post('student_id');
            // print('<pre>');var_dump($_FILES);exit;
            $s_error = '';
            $a_file_attach = [];
            if (isset($_FILES['fileattach'])) {
                $directory_file = APPPATH.'uploads/temp/';
                $config['upload_path'] = $directory_file;
                $config['allowed_types'] = 'jpeg|jpg|png|doc|docx|pdf|xls|xlsx|bmp';
                $config['max_size'] = 2024;
                $config['file_ext_tolower'] = TRUE;
                $config['replace'] = TRUE;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                $a_error = [];
                $d_total_size = 0;
                foreach ($_FILES['fileattach']['name'] as $key => $value) {
                    $_FILES['file']['name']= $_FILES['fileattach']['name'][$key];
                    $_FILES['file']['type']= $_FILES['fileattach']['type'][$key];
                    $_FILES['file']['tmp_name']= $_FILES['fileattach']['tmp_name'][$key];
                    $_FILES['file']['error']= $_FILES['fileattach']['error'][$key];
                    $_FILES['file']['size']= $_FILES['fileattach']['size'][$key];

                    if($this->upload->do_upload('file')) {
                        array_push($a_file_attach, $directory_file.$this->upload->data('file_name'));
                        $d_total_size += $this->upload->data('file_size');
                    }
                    else {
                        array_push($a_error, $this->upload->display_errors('<span>', '</span><br>'));
                    }
                }
                
                if (count($a_error) > 0) {
                    $s_error = implode(';', $a_error);
                }
                else if ($d_total_size > 3036) {
                    $s_error = 'Total size of uploaded file exceeds the limit';
                }
            }

            if (empty($s_error)) {
                if ((is_array($a_student)) AND (count($a_student) > 0)) {
                    if (!empty($subject)) {
                        $a_error_list = [];
                        foreach ($a_student as $s_student_id) {
                            $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
                            if ($mba_student_data) {
                                $o_student = $mba_student_data[0];

                                $message_body = $message;
                                $message_body = str_replace('$candidate_name', $o_student->personal_data_name, $message_body);
                                $s_email_to = $o_student->personal_data_email;
                                // $s_email_to = 'employee@company.ac.id';
                                $this->email->clear(TRUE);
                                $config['mailtype'] = 'html';
                                $this->email->initialize($config);
                                if (count($a_file_attach) > 0) {
                                    foreach ($a_file_attach as $s_path_file) {
                                        $this->email->attach($s_path_file);
                                    }
                                }
        
                                $this->email->from('employee@company.ac.id', 'IULI Admission');
                                $this->email->to($s_email_to);
                                $this->email->bcc(['employee@company.ac.id']);
                                $this->email->subject($subject);
                                $this->email->message($message_body);
                                if(!$this->email->send()){
                                    $this->log_activity('Email did not sent');
                                    $this->log_activity('Error Message: '.$this->email->print_debugger());
                                    
                                    array_push($a_error_list, 'Email not send to '.$s_email_to.' !');
                                }
                            }
                            else {
                                array_push($a_error_list, "candidate with id ".$s_student_id." not found!");
                            }
                        }

                        if (count($a_error_list) > 0) {
                            $a_return = ['code' => 1, 'message' => implode('<br>', $a_error_list)];
                        }
                        else {
                            $a_return = ['code' => 0, 'message' => 'Success'];
                        }
                    }
                    else {
                        $a_return = ['cde' => 1, 'message' => 'Please fill subject field!'];
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Empty selected candidate'];
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => $s_error];
            }
            
            // print('<pre>');var_dump($message);exit;
            // $a_return = ['code' => 9, 'message' => 'Not yet ready'];
            print json_encode($a_return);
        }
    }
}
