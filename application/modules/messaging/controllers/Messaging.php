<?php
class Messaging extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('student/Student_model', 'Stm');
		$this->load->model('employee/Employee_model', 'Emm');
	}

	public function send_smtp()
	{
		$config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		$this->email->initialize($config);

		$this->email->from('employee@company.ac.id', 'IULI IT Service Centre');
		$this->email->to('budi.siswanto1450@gmail.com');
		$this->email->cc('employee@company.ac.id');
		$this->email->subject("TEST SMTP");
		$this->email->message('<h1>TEST NON HTML</h1>\n<hr>');
		$this->email->send();
		$this->email->clear(TRUE);
		return true;
	}

	public function academic_email_form()
	{
		$this->load->view('form/academic_email_form', $this->a_page_data);
	}

	public function trancript_draft_input()
	{
		$this->load->view('form/trancript_draft_input', $this->a_page_data);
	}
	
	public function compose_email_form()
	{
		$this->load->view('compose_email_form', $this->a_page_data);
	}

	public function send_custom_email()
	{
		$s_email_to = $this->input->post('mail_student');
		$s_email_subject = $this->input->post('mail_subject');
		$s_message = $this->input->post('body_email');
		$a_email_to = explode('; ', $s_email_to);

		if (count($a_email_to) > 0) {
			if ($this->send_email($a_email_to, $s_email_subject, $s_message)) {
				$a_rtn = array('code' => 0, 'message' => 'Success');
			}else {
				$a_rtn = array('code' => 1, 'message' => 'Email to '.$s_email_to.' failed to send');
			}
		}else{
			$a_rtn = array('code' => 1, 'message' => 'Destination email not found!');
		}

		print json_encode($a_rtn);
	}

	public function send_email_personal_student()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_subject_mail = $this->input->post('mail_subject');
            $s_message = $this->input->post('body_email');
			
            $mbo_student_data = $this->Stm->get_student_by_id($s_student_id);
            if ($this->send_email($mbo_student_data->student_email, $s_subject_mail, $s_message)) {
                $a_rtn = array('code' => 0, 'message' => 'Success');
            }else {
                $a_rtn = array('code' => 1, 'message' => 'Email to '.$mbo_student_data->student_email.' failed to send');
            }
            print json_encode($a_rtn);
        }
	}
	
	public function send_email_blast_student()
    {
        if ($this->input->is_ajax_request()) {
            $a_filter_data = array(
                'ds.academic_year_id' => $this->input->post('academic_year_id'),
                'ds.study_program_id' => $this->input->post('study_program_id'),
                'ds.student_status' => $this->input->post('student_status')
            );
            $s_subject_mail = $this->input->post('mail_subject');
            $s_message = $this->input->post('body_email');

			$mba_student_filtered = modules::run('academic/student_academic/get_data_student_filter', $a_filter_data);
            $a_mail_send = array();
            $a_mail_fail = array();
            if ($mba_student_filtered) {
                foreach ($mba_student_filtered as $student) {
                    if ($this->send_email($student->student_email, $s_subject_mail, $s_message)) {
                        array_push($a_mail_send, $student->student_email);
                    }else {
                        array_push($a_mail_fail, $student->student_email);
                    }
                }
            }

            if (count($a_mail_fail) > 0) {
                $a_rtn = array('code' => 1, 'message' => 'Failed to send email to the following student: <li>'.implode('</li><li>', $a_mail_fail).'</li>');
            }else {
                $a_rtn = array('code' => 0, 'message' => 'Success');
            }

            print json_encode($a_rtn);
        }
	}

	public function send_mail_testing()
	{
		$s_email_body = '<a href="'.base_url().'apps/gsr/set_approve">Approve</a>';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);

		$this->email->from('employee@company.ac.id', 'Show Me');
		$this->email->to('employee@company.ac.id');
		$this->email->subject('Test Approve');
		$this->email->message($s_email_body);
		$this->email->send();
	}
	
	public function send_email($msa_email_to, $s_email_topic, $s_email_body, $s_email_from = '', $a_bcc = false, $s_path_file = false, $s_category = '[Academic Services] ')
    {
        if ($s_email_from == '') {
            $employee_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $this->session->userdata('user')))[0];
            if ($employee_data) {
                $s_email_from = $employee_data->employee_email;
            }else{
                var_dump($this->session->userdata('employee_id'));
                return false;
            }
		}
		
        // $config = $this->config->item('mail_config');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from($s_email_from);
		// $this->email->to('employee@company.ac.id');
        $this->email->to($msa_email_to);
        if ($a_bcc) {
			$this->email->bcc($a_bcc);
		}
        $this->email->subject($s_category.$s_email_topic);
		$this->email->message($s_email_body);
		
		if ($s_path_file) {
			$this->email->attach($s_path_file);
		}
		
		if(!$this->email->send()){
			$this->log_activity('Email did not sent');
            $this->log_activity('Error Message: '.$this->email->print_debugger());
			return false;
		}
		else{
			return true;
		}
	}
	
	// public function send_all_score_template()
	// {
	// 	if ($this->input->is_ajax_request()) {
	// 		$a_lect_email_list = $this->input->post('lect_email_list');
	// 		if (count($a_lect_email_list)) {
	// 			foreach ($a_lect_email_list as $email) {
	// 				# code...
	// 			}
	// 		}else{
	// 			$a_return = array('code' => 1, 'message' => 'No data send!');
	// 		}
	// 		$a_return = array('code' => 1, 'message' => 'On progress development!');
	// 		print json_encode($a_return);
	// 	}
	// }

	public function send_email_score_template($a_lecturer, $a_lecturer_mail, $s_filename, $s_filepath)
	{
		$s_lecturer = implode(' / ', $a_lecturer);
		$s_mail = implode(' / ', $a_lecturer_mail);

		$body = <<<TEXT
Lecturers: {$s_lecturer}
Emails: {$s_mail}
File: {$s_filename}
	
Dear Lecturers,

1. Mid Term Qualification
a.	The Mid Term scores should be emailed to employee@company.ac.id not later than Friday, 22 April 2022
b.	Please use score template as attached. For how to use/fill score template, please read the “Guidelines” sheet on the same file.
c.	Mid term Report (mid-transcript) issuance: Monday, 25 April 2022

2. End of the course: Friday, 18 June 2022

3. Final Examination
a.	Final examination material should be emailed to exam.employee@company.ac.id at the latest on Monday, 20 June 2022.
	Please use examination’s template provided by IULI.
b.	Examination Schedule: Monday to Friday, 27 June – 09 July 2022, IULI will provide invigilators, so lecturers do not have to come to the examination class.

4. Final Examination Result
a.	The Final Examination scores should be emailed to employee@company.ac.id not later than Saturday, 16 July 2022. Please use the standard score template previously sent to you. Complete score should include all quiz scores. Assume the submit file is the most updated one.
b.	AAcademic Semester Transcripts for will be issued on Monday, 18 July 2022.

5. Repetition Examination
a.	Repetition Examination material should be emailed to exam.employee@company.ac.id at the latest on Tuesday, 25 July 2022. Please use the repetition examination template provided by IULI.
b.	Repetition Examination Schedule: Monday to Friday, 25 July – 29 July 2022, IULI will provide invigilators, so lecturers do not have to come to the examination class.

6. Repetition Examination Result
a.	The Repetition Examination scores should be emailed to employee@company.ac.id not later than Friday 05 August 2022. Please use the standard score template previously sent to you. Complete score should include all quiz scores and final result. Assume the submit file is the most updated one.
b.	Academic Semester Transcripts after Repetition will be issued on Monday, 08 August 2022.

Thank you for your attention and cooperation. If you need further
information, please do not hesitate to contact us.

Best Regards,

Chandra Hendrianto
Academic Service Centre
Phone: +62 856 9734 8846

International University Liaison Indonesia
Associate Tower 7th Floor.
Intermark Indonesia
TEXT;

		// $config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		// $this->email->initialize($config);
		$this->email->from('employee@company.ac.id', 'IULI Academic Service Centre');
		$this->email->to($a_lecturer_mail);
		$bccEmail = array($this->config->item('email')['academic']['head'], 'budhi@lect.iuli.ac.id');
		$this->email->bcc($bccEmail);

		// $this->email->to('employee@company.ac.id');
		$this->email->attach($s_filepath);
		$this->email->subject("Examination and Score Submission Schedule Even Semester, Academic Year 2019-2020");
		$this->email->message($body);
		$this->email->send();
		$this->email->clear(TRUE);
		return true;
	}
}