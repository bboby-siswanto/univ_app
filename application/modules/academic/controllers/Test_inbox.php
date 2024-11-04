<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Test_inbox extends MX_Controller
{	
	public function __construct()
	{
		parent::__construct();
		$this->load->library('Imap_mailbox', [
			's_server' => 'mail.iuli.ac.id',
			's_user' => 'employee@company.ac.id',
			's_pass' => '',
			'i_port' => 0
		]);
		
		$this->load->library('Imap', [
			'mailbox' => 'mail.iuli.ac.id',
			'username' => 'employee@company.ac.id',
			'password' => ''
		]);
		
		$this->load->model('academic/Score_model', 'ScM');
		$this->load->model('academic/Semester_model', 'SeM');
		$this->load->model('academic/Class_group_model', 'CgM');
		$this->load->model('portal/Portal_model', 'Pm');
	}
	
	public function index()
	{
		print "<pre>";
		$a_inbox_items = $this->imap->getMessages();
		$a_folders = $this->imap->getFolders();
		
		if(count($a_inbox_items) >= 1){
			foreach($a_inbox_items as $key => $a_item){
				$s_sender_email = $a_item['email'];
				$s_sender_name = $a_item['from'];
				$a_original_senders = [
					'email' => $s_sender_email,
					'name' => $s_sender_name
				];
				
				$a_attachments = $this->imap_mailbox->get_attachments($key+1);
				
				if(count($a_attachments) >= 1){
					// loop all attachments
					for($i = 0; $i < count($a_attachments); $i++){
						$a_attachment_info = $a_attachments[$i];
						$a_file_info = pathinfo($a_attachment_info['name']);
						
						$a_attachment_content = $this->imap->getAttachment($a_item['uid'], $i);
						
						if(
							!array_key_exists('extension', $a_file_info) OR
				        	!in_array($a_file_info['extension'], ['xls', 'xlsx'])
						){
							$mba_move_result = $this->imap->moveMessage($a_item['uid'], 'unrelated');
						}
						else{
							$a_attachment_content = $this->imap->getAttachment($a_item['uid'], $i);
							$s_upload_directory = APPPATH.'uploads/score_uploads/tmp/';
					        if(!file_exists($s_upload_directory)){
						        mkdir($s_upload_directory, 0755, true);
					        }
					        $s_token = md5(time());
					        $s_file = $s_upload_directory.implode('.', [$s_token, $a_file_info['extension']]);
					        
					        if(@file_put_contents($s_file, $a_attachment_content['content'])){
						        $mba_score_read = $this->_read_score_file($s_file, $a_attachment_content['content'], $a_file_info['extension']);
								
								switch($mba_score_read['code'])
								{
									case 0:
										$this->_send_score_confirmation($mba_score_read['score_token'], $a_original_senders, $a_attachment_info['name']);
										$mba_move_result = $this->imap->moveMessage($a_item['uid'], 'archive');
										break;
										
									case 1:
										$mba_move_result = $this->imap->moveMessage($a_item['uid'], 'wrong_token');
										break;
										
									case 2:
										$this->_send_score_confirmation($mba_score_read['score_token'], $a_original_senders, $a_attachment_info['name']);
										$mba_move_result = $this->imap->moveMessage($a_item['uid'], 'old_token');
										break;
										
									default:
										$mba_move_result = $this->imap->moveMessage($a_item['uid'], 'uncategorized');
										break;
								}
					        }
					        else{
						        var_dump('fail put contents');
					        }
						}
					}
				}
				else{
					var_dump($a_item);
					// $this->imap_mailbox->move_message($a_item['index'], 'unrelated');
				}
			}
		}
	}
	
	public function index_two()
	{
		$a_inbox_items = $this->imap_mailbox->get_inbox_items();
		
		if(count($a_inbox_items) >= 1){
			foreach($a_inbox_items as $a_item){
				$s_sender_email = implode('@', [
					$a_item['header']->from[0]->mailbox, 
					$a_item['header']->from[0]->host]
				);
				
				$s_sender_name = (!isset($a_item['header']->from[0]->personal)) ? $s_sender_email : $a_item['header']->from[0]->personal;
				$a_original_senders = [
					'email' => $s_sender_email,
					'name' => $s_sender_name
				];
				// get message item
				$a_message_items = $this->imap_mailbox->get_message($a_item['index']);
				
				// if attachments >= 1
				if(count($a_message_items['attachments']) >= 1){
					$a_attachments = $a_message_items['attachments'];
					
					// loop all attachments
					for($i = 0; $i < count($a_attachments); $i++){
						
						$a_attachment_info = $a_attachments[$i];
						$a_file_info = pathinfo($a_attachment_info['name']);
						// get info of the attachment
						if(
							!array_key_exists('attachment', $a_attachment_info) OR 
				        	!isset($a_attachment_info['attachment']) OR
				        	!in_array($a_file_info['extension'], ['xls', 'xlsx'])
						){
							// handle rejection
							$this->imap_mailbox->move_message($a_item['index'], 'unrelated');
						}
						else{
							$s_upload_directory = APPPATH.'uploads/score_uploads/tmp/';
					        if(!file_exists($s_upload_directory)){
						        mkdir($s_upload_directory, 0755, true);
					        }
					        
					        $s_token = md5(time());
					        $s_file = $s_upload_directory.implode('.', [$s_token, $a_file_info['extension']]);
					        if(@file_put_contents($s_file, $a_attachment_info['attachment'])){
						        $mba_score_read = $this->_read_score_file($s_file, $a_attachment_info['attachment'], $a_file_info['extension']);
						        if($mba_score_read){
									$this->_send_score_confirmation($mba_score_read['score_token'], $a_original_senders, $a_attachment_info['name']);
									$this->imap_mailbox->move_message($a_item['index'], 'archive');
						        }
					        }
					        else{
						        // handle failure file_put_contents
					        }
						}
					}
				}
			}
		}
	}
	
	private function _read_score_file($s_file_path, $s_attachment, $s_extension)
    {
		$o_spreadsheet = IOFactory::load("$s_file_path");
		$o_sheet = $o_spreadsheet->setActiveSheetIndexByName("Score");
		$s_score_token =  str_replace(': ', '', $o_sheet->getCell('C6')->getValue());
		
		$mba_class_master = $this->CgM->get_class_master_student($s_score_token);
		$o_class_master = false;
		var_dump('token: '.$s_score_token);
		
		$code = 1;
		
		// check if token is found
		if($mba_class_master){
			var_dump('token found');
			$code = 0;
			$o_class_master = $mba_class_master[0];
		}
		// token not found
		else{
			// check if token is not uuid
			if(!$this->uuid->is_uuid($s_score_token)){
				var_dump('old version of token');
				$a_submission_data = $this->Pm->retrieve_data('score_submission', ['submission_token' => $s_score_token]);
				// check for old data
				if($a_submission_data){
					var_dump('submission data found');
					$o_submission_data = $a_submission_data[0];
					$i_portal_class_group = $o_submission_data->class_group_id;
					$mba_class_group_data = $this->CgM->get_class_group_lists(['portal_id' => $i_portal_class_group]);
					// check class master data
					if($mba_class_group_data){
						var_dump('class master group found');
						$mba_class_master_group = $this->CgM->get_class_master_group(['class_group_id' => $mba_class_group_data[0]->class_group_id]);
						if($mba_class_master_group){
							var_dump('class master found');
							$mba_class_master = $this->CgM->get_class_master_student($mba_class_master_group[0]->class_master_id);
							$o_class_master = ($mba_class_master) ? $mba_class_master[0] : false;
							$code = 2;
						}
					}
				}
			}
		}
	    
		if($o_class_master){
			$i_score_start = 12;
		
			$a_all_scores = [];
			$a_number_of_quizzes = [];
			$a_quizzes = [];
			
			while($o_sheet->getCell("B$i_score_start")->getValue() !== NULL){
				$s_student_number = $o_sheet->getCell("C$i_score_start")->getValue();
				$i_quiz_iterator = 1;
				$s_index_start_quiz = 'D';
	
				$a_student_quizzes = [];
				for($i = 0; $i < 7; $i++){
					$i_score = $o_sheet->getCell(implode('', [$s_index_start_quiz, $i_score_start]))->getCalculatedValue();
					if($i != 3){
						$a_student_quizzes['score_quiz'.$i_quiz_iterator] = $i_score;
						$i_quiz_iterator++;
					}
					$s_index_start_quiz++;
				}
				array_push($a_number_of_quizzes, count($a_student_quizzes));
				array_push($a_quizzes, $a_student_quizzes);
				
				$i_score_quiz = $o_sheet->getCell("K$i_score_start")->getOldCalculatedValue();
				$i_score_final_exam = $o_sheet->getCell("L$i_score_start")->getValue();
				$i_score_repetition_exam = $o_sheet->getCell("M$i_score_start")->getValue();
				
				$i_score_sum = $this->grades->get_score_sum($i_score_quiz, $i_score_final_exam);
				$s_score_grade = $this->grades->get_grade($i_score_sum);
				$i_score_grade_point = $this->grades->get_grade_point($i_score_sum);
				
				
				$a_all_scores[$s_student_number] = [
					'quizzes' => $a_student_quizzes,
					'score_quiz' => $i_score_quiz,
					'score_final_exam' => $i_score_final_exam,
					'score_repetition_exam' => $i_score_repetition_exam,
					'score_sum' => $i_score_sum,
					'score_grade' => $s_score_grade,
					'score_grade_point' => $i_score_grade_point
				];
				$i_score_start++;
			}
			
			rsort($a_number_of_quizzes, SORT_NUMERIC);
			$i_highest_num_quizzes = $a_number_of_quizzes[0];
			
			foreach($a_all_scores as $key => $value){
				$d_true_sum_quiz = 0;
				if($i_highest_num_quizzes >= 1){
					$d_true_sum_quiz = array_sum(array_values($value['quizzes'])) / $i_highest_num_quizzes;
				}
				
				$i_final_exam = ($value['score_final_exam'] !== NULL) ? $value['score_final_exam'] : 0;
				$i_repetition_exam = ($value['score_repetition_exam'] !== NULL) ? $value['score_repetition_exam'] : 0;
				
				$a_final_exams = [$i_final_exam, $i_repetition_exam];
				rsort($a_final_exams, SORT_NUMERIC);
				
				$d_true_final_exam = $a_final_exams[0];
				$d_true_score_sum = $this->grades->get_score_sum($d_true_sum_quiz, $d_true_final_exam);
				
				$mba_check_score_data = $this->ScM->get_score_data([
					'sc.class_master_id' => $o_class_master->class_master_id,
					'st.student_number' => $key
				]);
				
				if(($mba_check_score_data) AND (count($mba_check_score_data) == 1)){
					$i_score_ects = $this->grades->get_score_ects($mba_check_score_data[0]->curriculum_subject_credit, $value['score_grade_point']);
					$i_score_merit = $this->grades->get_merit($mba_check_score_data[0]->curriculum_subject_credit, $value['score_grade_point']);
					
					$a_score_data = array_merge($value, $value['quizzes']);
					unset($a_score_data['quizzes']);
					$a_score_data['score_true_average_quiz'] = $d_true_sum_quiz;
					$a_score_data['score_true_sum'] = $d_true_score_sum;
					$a_score_data['score_ects'] = $i_score_ects;
					$a_score_data['score_merit'] = $i_score_merit;
					
					$this->ScM->save_data($a_score_data, [
						'student_id' => $mba_check_score_data[0]->student_id,
						'academic_year_id' => $mba_check_score_data[0]->academic_year_id,
						'semester_type_id' => $mba_check_score_data[0]->semester_type_id
					]);
					$this->_handle_student_semester($mba_check_score_data[0]);
				}
			}
			
			$s_save_file_path = APPPATH.'uploads/score_uploads/'.implode('/', [
				$o_class_master->class_academic_year,
				$o_class_master->semester_type_id,
				implode('_', [
					$o_class_master->subject_name,
					$o_class_master->class_master_id
				])
			]);
			
			if(!file_exists($s_save_file_path)){
				mkdir($s_save_file_path, 0755, true);
			}
			
			if(rename($s_file_path, $s_save_file_path."/".date('Y-m-d_H:i:s', time()).".".$s_extension)){
				return [
					'code' => $code,
					'file_path' => $s_file_path,
					'score_token' => $o_class_master->class_master_id
				];
			}
			return ['code' => 1];
		}
		else{
			return ['code' => $code];
		}
    }
    
    private function _send_score_confirmation($s_score_token, $a_original_senders, $s_attachment_name)
    {
	    $mba_class_master_lecturers = $this->CgM->get_class_master_lecturer([
			'class_master_id' => $s_score_token
		]);
		
		$a_send_to = [
			$a_original_senders['email']
		];
		
		$a_bcc_to = [
			'employee@company.ac.id'
		];
		
		foreach($mba_class_master_lecturers as $lecturers){
			if(!is_null($lecturers->employee_email)){
				array_push($a_bcc_to, $lecturers->employee_email);
			}
		}
		
	    $s_body = <<<TEXT
Dear, Mr/Ms. {$a_original_senders['name']}
	
You have just uploaded {$s_attachment_name} if you believe that this attachment file is for score input to our database, please refer to our guidelines as follows:
	
1. Fill in the score of Quiz 1-6 as needed. If a student does not attend a quiz, fill his/her score with 0
- at least 2 scores must be submitted before the mid-term qualifications
- at least 4 scores must be submitted in a semester
- scores can be taken from quizzes, assignments, projects or homework or other tasks in a semester.
	
2. The lecturer name at the bottom right of this template is the one who MUST submit this template to employee@company.ac.id
- you may submit the score file at anytime, it will directly appear on the student's portal
- you may submit the score file using ANY email address
- you may submit the score several times to revised the score as necessary
- the next time you submit the score after the first one, you will receive a confirmation email to your VALID IULI email address. Please ask IT for assistance on your VALID IULI email address
- to confirm the revision, please click the link on the confirmation email and you will be redirected to www.iuli.ac.id upon success
- if you don't want to confirm the score revision, just ignore the email and the score will not be revised on the database

3. DO NOT CHANGE the information related to this template including the filename except for the score cells (Quiz 1 - 6, Final Exam and Repetition)

4. The filename is hashed and registered on the Database, any changes on the filename even only 1 character will not be recognized

5. For midterm submission, final exam and repetition should be left blank

6. You may copy the information on this template to other worksheet or excel file for your own needs

7. DO NOT SAVE AS TO ANOTHER FORMAT. The ONLY accepted format is .xls

8. Keep in mind that THE EXCEL FILE WILL BE FURTHER PROCESSED BY A COMPUTER SOFTWARE

{$s_score_token}
TEXT;


		$config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->to($a_send_to);
		$this->email->from('employee@company.ac.id', 'Score Academic Bot Reader');
		$this->email->subject('Score Submission Confirmation');
		$this->email->reply_to('employee@company.ac.id');
		$this->email->message($s_body);
		$this->email->bcc($a_bcc_to);
		$this->email->send();
		
		if(!$this->email->send()){
			$this->email->to('employee@company.ac.id');
			$s_body .= "\n\nEmail ini gagal dikirim. Error: ".$this->email->print_debugger();
			$this->email->from('employee@company.ac.id');
			$this->email->cc('employee@company.ac.id');
			$this->email->send();
		}
    }
    
    private function _handle_student_semester($o_check_score_data)
    {
	    $i_academic_year_id = $o_check_score_data->academic_year_id;
	    $i_semester_type_id = $o_check_score_data->semester_type_id;
	    $s_student_id = $o_check_score_data->student_id;
	    
	    $mbo_sum_credit_merit_gpa = $this->ScM->get_sum_merit_credit([
			'sc.student_id' => $s_student_id,
			'sc.academic_year_id <= ' => $i_academic_year_id,
			'sc.score_approval' => 'approved'
		]);
		
		$mbo_sum_credit_merit_gp = $this->ScM->get_sum_merit_credit([
			'sc.student_id' => $s_student_id,
			'sc.academic_year_id' => $i_academic_year_id,
			'sc.score_approval' => 'approved'
		]);
		
		$d_cumulative_gpa = $this->grades->get_ipk($mbo_sum_credit_merit_gpa->sum_merit, $mbo_sum_credit_merit_gpa->sum_credit);
		$d_cumulative_gp = $this->grades->get_ipk($mbo_sum_credit_merit_gp->sum_merit, $mbo_sum_credit_merit_gp->sum_credit);
		
		$this->SeM->save_student_semester([
			'student_semester_gpa' => $d_cumulative_gpa,
			'student_semester_gp' => $d_cumulative_gp
		],
		[
			'student_id' => $s_student_id,
			'academic_year_id' => $i_academic_year_id,
			'semester_type_id' => $i_semester_type_id
		]);
    }
}