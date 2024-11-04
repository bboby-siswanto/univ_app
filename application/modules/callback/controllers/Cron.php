<?php
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;	
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Cron extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Api_core_model', 'Api');
	}
	
	public function backup_access_log()
	{
		$x = $this->Api->backup_access_log();
		// var_dump($x);
	}

    function manage_queue() {
        // $this->log_activity('Teste');
        $a_data = ['nol', 'satu', 'dua', 'tiga'];
        for ($i=0; $i < 5; $i++) { 
            // print($a_data);
            try {
                print($a_data[$i]);
            } catch (\Throwable $th) {
                // print('<pre>');var_dump($th);
                print('ada eror');exit;
            }
        }
        
    }

    public function get_link()
    {
        print('from:'.$_SERVER['HTTP_HOST']);
    }
	
	public function get_reference_tables()
	{
		return array(
			array(
				'table' => 'dt_personal_data'
			),
			array(
				'table' => 'dt_address'
			),
			array(
				'table' => 'ref_institution'
			),
			array(
				'table' => 'ref_ocupation'
			),
/*
			array(
				'table' => 'ref_program'
			),
			array(
				'table' => 'ref_study_program'
			),
			array(
				'table' => 'ref_program_study_program',
				'composite_keys' => array('program_id', 'study_program_id')
			),
*/
			array(
				'table' => 'dt_family'
			),
			array(
				'table' => 'dt_family_member',
				'composite_keys' => array('family_id', 'personal_data_id')
			),
			array(
				'table' => 'dt_personal_address',
				'composite_keys' => array('personal_data_id', 'address_id')
			),
			array(
				'table' => 'dt_personal_data_document',
				'composite_keys' => array('personal_data_id', 'document_id')
			),
			array(
				'table' => 'dt_student'
			),
			array(
				'table' => 'dt_academic_history'
			),
			array(
				'table' => 'dt_academic_year'
			),
			array(
				'table' => 'dt_reference',
				'composite_keys' => array('referrer_id', 'referenced_id')
			)
		);
	}
	
	public function sync_reference()
	{	
		$a_reference_tables = $this->get_reference_tables();
		for($i = 0; $i < count($a_reference_tables); $i++){
			$s_table_name = $a_reference_tables[$i]['table'];
			
			$a_fields = $this->db->field_data($s_table_name);
			$a_list_fields = $this->db->list_fields($s_table_name);
			
			$a_primary_key = array();
			$b_has_sync = false;
			
			if(in_array('pmb_sync', $a_list_fields)){
				$b_has_sync = true;
			}
			
			if(array_key_exists('composite_keys', $a_reference_tables[$i])){
				$a_primary_key = $a_reference_tables[$i]['composite_keys'];
			}
			else{
				foreach($a_fields as $field){
					if($field->primary_key == 1){
						array_push($a_primary_key, $field->name);
					}
				}
			}
			
			if($b_has_sync){
				$query = $this->db->get_where($s_table_name, array('pmb_sync' => '1'));
			}
			else{
				$query = $this->db->get($s_table_name);
			}
			
			// print "Preparing data for ".$s_table_name."...\n";
			
			$a_construct_data = array(
				'table' => $s_table_name,
				'primary_key' => $a_primary_key,
				'table_fields' => $a_list_fields
			);
			
			$a_token_config = $this->config->item('token')['pmb'];
			$a_sites = $this->config->item('sites');
			$s_token = $a_token_config['access_token'];
			$s_secret_token = $a_token_config['secret_token'];
			
			// print "Sending data...\n";
			
			if($query->num_rows() >= 1){
				$iteration = 1;
				foreach($query->result() as $data){
					// print "Pushing $iteration of ".$query->num_rows()."\n";
					$a_clause = array();
					
					for($l = 0; $l < count($a_primary_key); $l++){
						$a_clause[$a_primary_key[$l]] = $data->{$a_primary_key[$l]};
					}
					$a_construct_data['batch_data'] = $data;
					$hashed_string = $this->libapi->hash_data($a_construct_data, $s_token, $s_secret_token);
					$post_data = json_encode(array(
						'access_token' => 'PORTALIULIACID',
						'data' => $hashed_string
					));
							
					$a_result = $this->libapi->post_data($a_sites['pmb'].'api/synchronizer/retrieve_sync', $post_data);
					if(!is_null($a_result)){
						if($a_result->code == 0){
							// print "Updating $iteration of ".$query->num_rows()." from ".$s_table_name."\n\n";
							if(in_array('pmb_sync', $a_list_fields)){
								$this->db->update($a_construct_data['table'], array('pmb_sync' => '0'), $a_clause);
							}
							else{
								$this->db->update($a_construct_data['table'], $data, $a_clause);
							}
						}
						else{
							// print "Updating failed\n\n";
						}
					}
					$iteration++;
				}
			}
		}
	}

    public function broadcast_english_test()
    {
        // $this->_create_contact_list();
        // $this->load->model('whatsapp/Pmb_model', 'pmb');
        // $this->load->model('exam/Entrance_test_model', 'Etm');
        // $this->load->library('WaAPI');
        // $s_page = 'https://portal.iuli.ac.id/exam/auth_entrance_test';

        // return [
        //     'send_data' => $a_send_data,
        //     'list_number' => $a_wa_list_number
        // ];

        // $get_data = $this->_prep_broadcast_english_test_data('whatsapp');
        // print('<pre>');var_dump($get_data);exit;
        // if (count($get_data['list_number']) > 0) {
        //     $result = $this->waapi->execute_post('broadcasts/whatsapp', [
        //         'name' => '',
        //         'message_template_id' => '6827c5a1-1fc2-4d3f-9b62-2553a9de8e8a',
        //         'contact_list_id' => '2050ce64-f7a2-482e-9525-3d2736a0b380',
        //         'channel_integration_id' => 'cc073782-4440-4718-a874-f78769a4a67d',
        //         'parameters' => [
        //             'body' => [
        //                 [
        //                     'key' => '1',
        //                     'value' => 'full_name'
        //                 ]
        //             ]
        //         ]
        //     ]);
        // }
            // pake fungsinya jangan yang ini
            // foreach ($mba_data as $o_exam_candidate) {
            //     if (in_array($o_exam_candidate->student_status, $a_candidate_status_allowed)) {
            //         // $i_count++;
            //         $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_exam_candidate->personal_data_id]);
            //         if ($mba_personal_data) {
            //             // modules::run('admission/api/send_invitation_online_test', $mba_personal_data[0], $o_exam_candidate->token);
            //             $mba_candidate_data = $this->pmb->get_student_data(['st.personal_data_id' => $s_personal_data_id]);
            //             $s_error_message = '';
            //             if ($mba_candidate_data) {
            //                 $s_tonumber = $this->waapi->initialize_contact($mba_candidate_data[0]->personal_data_cellular);
            //                 $s_toname = $mba_candidate_data[0]->personal_data_name;

            //                 if (!in_array($s_tonumber, $a_number_send)) {
            //                     array_push($a_number_send, $s_tonumber);

            //                     $result = $this->waapi->execute_post('broadcasts/whatsapp/direct', [
            //                         'to_number' => $s_tonumber,
            //                         'to_name' => $s_toname,
            //                         'message_template_id' => 'e5a95692-5342-4278-b86d-57bd718541ce',
            //                         'channel_integration_id' => 'cc073782-4440-4718-a874-f78769a4a67d',
            //                         'language' => [
            //                             'code' => 'en'
            //                         ],
            //                         'parameters' => [
            //                             'body' => [
            //                                 [
            //                                     'key' => '1',
            //                                     'value' => 'full_name',
            //                                     'value_text' => $s_toname
            //                                 ],
            //                                 [
            //                                     'key' => '2',
            //                                     'value' => 'link_page',
            //                                     'value_text' => $s_page
            //                                 ],
            //                                 [
            //                                     'key' => '3',
            //                                     'value' => 'link_token',
            //                                     'value_text' => $o_exam_candidate->token
            //                                 ]
            //                             ]
            //                         ]
            //                     ]);
    
            //                     if ((!empty($result)) AND ($result->status !== null)) {
            //                         if ($result->status == 'success') {
            //                             // print('<pre>');var_dump($result);exit;
            //                             $o_data = $result->data;
            //                             $o_sent_data = $result->sentdata;
            //                             $o_message_template = $o_data->message_template;
                        
            //                             $mba_broadcast_log = $this->waapi->execute_get('broadcasts/'.$o_data->id.'/whatsapp/log');
            //                             $mba_contact_data = $this->pmb->get_where('wa_contact', ['contact_id' => $o_data->contact_id]);
            //                             if (!$mba_contact_data) {
            //                                 $a_contact_data = [
            //                                     'contact_id' => $o_data->contact_id,
            //                                     'personal_data_id' => ($mba_candidate_data) ? $mba_candidate_data[0]->personal_data_id : NULL,
            //                                     'wa_phonenumber' => $o_sent_data['to_number'],
            //                                     'wa_fullname' => $o_sent_data['to_name'],
            //                                     'wa_status' => 'success',
            //                                     'wa_uniq_id' => $o_sent_data['to_number'],
            //                                     'wa_is_valid' => 'true',
            //                                     'wa_is_blocked' => 'false',
            //                                     'date_added' => date('Y-m-d H:i:s')
            //                                 ];
                        
            //                                 $this->pmb->submit_wa_contact($a_contact_data);
            //                             }
            //                             else if (is_null($mba_contact_data[0]->personal_data_id)) {
            //                                 $a_contact_data['personal_data_id'] = ($mba_candidate_data) ? $mba_candidate_data[0]->personal_data_id : NULL;
            //                                 $this->pmb->submit_wa_contact($a_contact_data, ['contact_id' => $o_data->contact_id]);
            //                             }
                        
            //                             $a_savebroadcast = [
            //                                 'broadcast_id' => $o_data->id,
            //                                 'contact_id' => $o_data->contact_id,
            //                                 'message_template_id' => $o_message_template->id,
            //                                 'message_body' => $o_message_template->body,
            //                                 'to_name' => $o_sent_data['to_name'],
            //                                 'to_number' => $o_sent_data['to_number'],
            //                                 'send_at' => $o_data->send_at
            //                             ];
                        
            //                             if ((!empty($mba_broadcast_log)) AND ($mba_broadcast_log->status !== null)) {
            //                                 if ($mba_broadcast_log->status == 'success') {
            //                                     if ((is_array($mba_broadcast_log->data)) AND (count($mba_broadcast_log->data) > 0)) {
            //                                         $broadcast_logdata = $mba_broadcast_log->data;
            //                                         $a_savebroadcast['whatsapp_message_id'] = $broadcast_logdata->whatsapp_message_id;
            //                                         $a_savebroadcast['last_retrieve_status'] = $broadcast_logdata->status;
            //                                     }
            //                                 }
            //                             }
                        
            //                             $this->pmb->submit_wa_broadcast($a_savebroadcast);
            //                         }
            //                         else {
            //                             $s_error_message = $result->error;
            //                         }
            //                     }
            //                     else {
            //                         $s_error_message = 'Error processing whatsapp ->'.json_encode($result);
            //                     }
            //                 }
            //                 else {
            //                     $s_error_message = 'already sent';
            //                 }
            //             }
            //             else {
            //                 $s_error_message = 'Candidate not found!';
            //             }
        
                        
            //             $a_sending_data = [
            //                 'name' => $o_exam_candidate->personal_data_name,
            //                 'email' => $o_exam_candidate->personal_data_email,
            //                 'token' => $o_exam_candidate->token,
            //                 'error_message' => $s_error_message
            //             ];
            //             array_push($a_send_data, $a_sending_data);
            //         }
            //     }
            // }
            // print('<pre>');var_dump($mba_data);exit;
        // }

        // if (count($a_send_data) > 0) {
        //     $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        //     $s_file_name = 'Weekly_wa_blast_invitation_onlinetest_'.date('d-M-Y');
        //     $s_filename = $s_file_name.'.xlsx';

        //     $s_file_path = APPPATH."uploads/admission/emai_blast_invitation_online_test/".date('Y')."/".date('m')."/";
        //     if(!file_exists($s_file_path)){
        //         mkdir($s_file_path, 0777, TRUE);
        //     }

        //     $o_spreadsheet = IOFactory::load($s_template_path);
        //     $o_sheet = $o_spreadsheet->getActiveSheet();
        //     $o_spreadsheet->getProperties()
        //         ->setTitle($s_file_name)
        //         ->setCreator("IULI ISTS");

        //     $i_row = 1;
        //     $o_sheet->setCellValue('A'.$i_row, 'Name');
        //     $o_sheet->setCellValue('B'.$i_row, 'Email');
        //     $o_sheet->setCellValue('C'.$i_row, 'Token');
        //     $o_sheet->setCellValue('D'.$i_row, 'Message');
        //     $i_row++;

        //     foreach ($a_send_data as $key => $value) {
        //         $o_sheet->setCellValue('A'.$i_row, $value['name']);
        //         $o_sheet->setCellValue('B'.$i_row, $value['email']);
        //         $o_sheet->setCellValue('C'.$i_row, $value['token']);
        //         $o_sheet->setCellValue('D'.$i_row, $value['error_message']);
        //         $i_row++;
        //     }

        //     $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        //     $o_writer->save($s_file_path.$s_filename);
        //     $o_spreadsheet->disconnectWorksheets();
        //     unset($o_spreadsheet);

        //     // $config = $this->config->item('mail_config');
        //     $config['mailtype'] = 'html';
        //     $this->email->initialize($config);
        //     $this->email->from('employee@company.ac.id', 'IULI Email Report');
        //     $this->email->to('employee@company.ac.id');
        //     // $this->email->to('budi.siswanto1450@gmail.com');
        //     // $this->email->to(['employee@company.ac.id', 'employee@company.ac.id']);
        //     $this->email->subject('Report Whatsapp Blast [Invitation Online Test]');
        //     $this->email->message('');
        //     $this->email->attach($s_file_path.$s_filename);
        //     $this->email->send();
        //     exit;
        // }
    }

    public function test()
    {
        $prepare = $this->_prep_broadcast_english_test_data('whatsapp');
        if ((is_array($prepare['list_number'])) AND ($prepare['list_number'] > 0)) {
            if (($prepare['path_target'] !== null) AND (!empty($prepare['path_target']))) {
                if (realpath($prepare['path_target'])) {
                    $s_mime = mime_content_type($prepare['path_target']);
                    $s_fileexport = new CURLFILE($prepare['path_target']);
                    $s_fileexport->mime = $s_mime;

                    $result = $this->waapi->execute_form('contacts/contact_lists/async', [
                        'name' => 'invitation_lists_'.date('dmY'),
                        'source_type' => 'spreadsheet',
                        'file' => $s_fileexport
                    ], true);

                    print('<pre>');var_dump($result);exit;
                }
            }
        }
        print('<pre>');var_dump($prepare);exit;
    }

    private function _prep_contact_list_wa()
    {
        // $this->_prep_broadcast_english_test_data('whatsapp');
    }

    private function _prep_broadcast_english_test_data($s_format = 'whatsapp')
    {
        $this->load->model('exam/Entrance_test_model', 'Etm');
        $a_candidate_status_allowed = ['register', 'candidate'];
        $mba_data = $this->Etm->get_candidate_exam([
            'st.finance_year_id' => '2023',
            'ec.candidate_exam_status' => 'PENDING'
        ], 'pmb');

        $a_send_data = [];
        $s_path_wa = '';
        $a_wa_list_number = [];
        $a_wa_param_data = [];
        $i_count = 0;
        if ($mba_data) {
            if ($s_format == 'whatsapp') {
                $this->load->library('WaAPI');
                $s_template_path = APPPATH.'uploads/templates/admission/wa_recipient_list_xls_file.xls';
                $s_file_name = 'recipientlists';
                $s_filename = $s_file_name.'.xls';
                $s_pathreal = realpath('.');
                $s_file_path = $s_pathreal.'/templates/';
                // print('<pre>');var_dump($s_file_path);exit;
                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $o_spreadsheet = IOFactory::load($s_template_path);
                $o_sheet = $o_spreadsheet->getActiveSheet();

                $o_sheet->setCellValue('E1', 'token');
            }
            
            $i_row = 2;
            
            foreach ($mba_data as $o_exam_candidate) {
                if (in_array($o_exam_candidate->student_status, $a_candidate_status_allowed)) {
                    // $i_count++;
                    $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_exam_candidate->personal_data_id]);
                    if ($mba_personal_data) {
                        if ($s_format == 'email') {
                            // modules::run('admission/api/send_invitation_online_test', $mba_personal_data[0], $o_exam_candidate->token);
                            // modules::run('admission/api/test_send_invitation_online_test', $mba_personal_data[0], $o_exam_candidate->token);
                            $a_sending_data = [
                                'name' => $o_exam_candidate->personal_data_name,
                                'email' => $o_exam_candidate->personal_data_email,
                                'token' => $o_exam_candidate->token
                            ];
                            array_push($a_send_data, $a_sending_data);
                        }
                        // elseif ($s_format == 'whatsapp') {
                        //     $s_phone_number = $this->waapi->initialize_contact($mba_personal_data[0]->personal_data_cellular);
                        //     if (($s_phone_number) AND (!in_array($s_phone_number, $a_wa_list_number))) {
                        //         $s_name = str_ireplace( array( '\'', '"',',' , ';', '<', '>' ), ' ', $o_exam_candidate->personal_data_name);
                        //         $a_sending_data = [
                        //             'name' => $o_exam_candidate->personal_data_name,
                        //             'email' => $o_exam_candidate->personal_data_email,
                        //             'token' => $o_exam_candidate->token
                        //         ];
                        //         $o_sheet->setCellValue('A'.$i_row, $s_phone_number);
                        //         $o_sheet->setCellValue('B'.$i_row, $s_name);
                        //         $o_sheet->setCellValue('C'.$i_row, $s_name);
                        //         $o_sheet->setCellValue('D'.$i_row, 'Candidate IULI');
                        //         $o_sheet->setCellValue('E'.$i_row, $o_exam_candidate->token);
                                
                        //         array_push($a_wa_list_number, $s_phone_number);
                        //         array_push($a_send_data, $a_sending_data);
                        //         $i_row++;
                        //     }
                        // }
                    }
                }
            }

            if ($s_format == 'whatsapp') {
                $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
                $o_writer->save($s_file_path.$s_filename);
                $o_spreadsheet->disconnectWorksheets();
                unset($o_spreadsheet);
                $s_path_wa = $s_file_path.$s_filename;
            }
        }

        return [
            'send_data' => $a_send_data,
            'list_number' => $a_wa_list_number,
            'path_target' => $s_path_wa
        ];
    }

    public function reminder_english_test()
    {
        // $a_send_data = $this->_prep_broadcast_english_test_data('email');
        // // print('<pre>');var_dump($a_send_data);exit;

        // if (count($a_send_data['send_data']) > 0) {
        //     $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        //     $s_file_name = 'Weekly_email_blast_invitation_onlinetest_'.date('d-M-Y');
        //     $s_filename = $s_file_name.'.xlsx';

        //     $s_file_path = APPPATH."uploads/admission/emai_blast_invitation_online_test/".date('Y')."/".date('m')."/";
        //     if(!file_exists($s_file_path)){
        //         mkdir($s_file_path, 0777, TRUE);
        //     }

        //     $o_spreadsheet = IOFactory::load($s_template_path);
        //     $o_sheet = $o_spreadsheet->getActiveSheet();
        //     $o_spreadsheet->getProperties()
        //         ->setTitle($s_file_name)
        //         ->setCreator("IULI ISTS");

        //     $i_row = 1;
        //     $o_sheet->setCellValue('A'.$i_row, 'Name');
        //     $o_sheet->setCellValue('B'.$i_row, 'Email');
        //     $o_sheet->setCellValue('C'.$i_row, 'Token');
        //     $i_row++;

        //     foreach ($a_send_data['send_data'] as $key => $value) {
        //         $o_sheet->setCellValue('A'.$i_row, $value['name']);
        //         $o_sheet->setCellValue('B'.$i_row, $value['email']);
        //         $o_sheet->setCellValue('C'.$i_row, $value['token']);
        //         $i_row++;
        //     }

        //     $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        //     $o_writer->save($s_file_path.$s_filename);
        //     $o_spreadsheet->disconnectWorksheets();
        //     unset($o_spreadsheet);

        //     // $config = $this->config->item('mail_config');
        //     $config['mailtype'] = 'html';
        //     $this->email->initialize($config);
        //     $this->email->from('employee@company.ac.id', 'IULI Email Report');
        //     // $this->email->to('employee@company.ac.id');
        //     // $this->email->to('budi.siswanto1450@gmail.com');
        //     $this->email->to(['employee@company.ac.id', 'employee@company.ac.id']);
        //     $this->email->subject('Report Email Blast [Invitation Online Test]');
        //     $this->email->message('');
        //     $this->email->attach($s_file_path.$s_filename);
        //     $this->email->send();
        //     exit;
        // }
    }

    public function reminder_register_students()
    {
        // $this->load->model('admission/Admission_model', 'Adm');
        // $mba_year = $this->General->get_where('dt_academic_year', ['academic_year_intake_status' => 'active']);
        // $s_year = ($mba_year) ? $mba_year[0]->academic_year_id : date('Y');
        
        // $mba_student_list = $this->Adm->get_candidate_student($s_year, [
        //     'stu.student_status' => 'register',
        //     'pd.personal_data_email_confirmation' => 'no'
        // ]);

        // $a_send_data = [];
        // if ($mba_student_list) {
        //     foreach ($mba_student_list as $o_student) {
        //         $s_template_email = $this->load->view('messaging/admission/layout_cron_register', $this->a_page_data, true);
        //         $config['mailtype'] = 'html';
        //         $this->email->initialize($config);

        //         $this->email->from('employee@company.ac.id', 'IULI Admission');
        //         $this->email->to($o_student->personal_data_email);
        //         $this->email->subject("[IULI ADMISSION] IULI Account Registration");
        //         $this->email->message($s_template_email);
        //         if($this->email->send()) {
        //             $a_sending_data = [
        //                 'name' => $o_student->personal_data_name,
        //                 'email' => $o_student->personal_data_email,
        //                 'prodi' => $o_student->study_program_name
        //             ];
        //             array_push($a_send_data, $a_sending_data);
        //         }
        //     }
        // }

        // if (count($a_send_data)) {
        //     $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        //     $s_file_name = 'Weekly_email_blast_register_student_'.date('d-M-Y');
        //     $s_filename = $s_file_name.'.xlsx';

        //     $s_file_path = APPPATH."uploads/admission/emai_blast_register_student/".date('Y')."/".date('m')."/";
        //     if(!file_exists($s_file_path)){
        //         mkdir($s_file_path, 0777, TRUE);
        //     }

        //     $o_spreadsheet = IOFactory::load($s_template_path);
        //     $o_sheet = $o_spreadsheet->getActiveSheet();
        //     $o_spreadsheet->getProperties()
        //         ->setTitle($s_file_name)
        //         ->setCreator("IULI ISTS");

        //     $i_row = 1;
        //     $o_sheet->setCellValue('A'.$i_row, 'Name');
        //     $o_sheet->setCellValue('B'.$i_row, 'Email');
        //     $o_sheet->setCellValue('C'.$i_row, 'Study Program');
        //     $i_row++;

        //     foreach ($a_send_data as $key => $value) {
        //         $o_sheet->setCellValue('A'.$i_row, $value['name']);
        //         $o_sheet->setCellValue('B'.$i_row, $value['email']);
        //         $o_sheet->setCellValue('C'.$i_row, $value['prodi']);
        //         $i_row++;
        //     }

        //     $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        //     $o_writer->save($s_file_path.$s_filename);
        //     $o_spreadsheet->disconnectWorksheets();
        //     unset($o_spreadsheet);

        //     // $config = $this->config->item('mail_config');
        //     $config['mailtype'] = 'html';
        //     $this->email->initialize($config);
        //     $this->email->from('employee@company.ac.id', 'IULI Email Report');
        //     // $this->email->to('employee@company.ac.id');
        //     // $this->email->to('budi.siswanto1450@gmail.com');
        //     $this->email->to(['employee@company.ac.id', 'employee@company.ac.id']);
        //     $this->email->subject('Report Email Blast [Register Status]');
        //     $this->email->message('');
        //     $this->email->attach($s_file_path.$s_filename);
        //     $this->email->send();
        //     exit;
        // }
    }

    public function report_ni_registration()
    {
        // $a_data = modules::run('download/excel_download/download_ni_student');
        // if ($a_data['code'] == '0') {
        //     $send = modules::run('messaging/send_email', 
        //         // ['employee@company.ac.id'],
        //         'hermawan@haryanto.id',
        //         'NI Registration From IULI',
        //         'per '.date('d F Y H:i:s'),
        //         'employee@company.ac.id',
        //         // false,
        //         ['employee@company.ac.id'],
        //         APPPATH."uploads/admission/ni_registration/".date('Y')."/".date('m')."/".$a_data['filename'],
        //         ''
        //     );

        //     if (!$send) {
        //         $this->email->from('employee@company.ac.id');
        //         $this->email->to('employee@company.ac.id');
        //         $this->email->subject('[ERROR MESSAGE]');
        //         $this->email->message('Error found in file cron.php, function report_ni_registration');
        //         $this->email->send();
        //     }
        //     // send_email($msa_email_to, $s_email_topic, $s_email_body, $s_email_from = '', $a_bcc = false, $s_path_file = false)
        // }else{
        //     $this->email->from('employee@company.ac.id');
        //     $this->email->to('employee@company.ac.id');
        //     $this->email->subject('[ERROR MESSAGE]');
        //     $this->email->message('Error found in file cron.php, function report_ni_registration: (no data generated)');
        //     $this->email->send();
        // }
    }

    public function checker_paid()
    {
        $this->load->model('finance/Bni_model', 'Bnim');
        $mba_trx_paid = $this->General->get_where('dt_sub_invoice_details', [
            'sub_invoice_details_status' => 'paid',
            'date_added > ' => '2020-06-01 00:00:00',
            'date_added < ' => '2021-01-01 00:00:00'
        ]);

        $i = 0;
        if ($mba_trx_paid) {
            // print('<pre>');
            foreach ($mba_trx_paid as $key_paid => $value_paid) {
                $a_data = [
                    'sub_invoice_details_id' => $value_paid->sub_invoice_details_id,
                    'trx_id' => $value_paid->trx_id,
                    'virtual_account' => $value_paid->sub_invoice_details_va_number,
                    'sub_invoice_details_amount_total' => $value_paid->sub_invoice_details_amount_total,
                    'sub_invoice_details_status' => $value_paid->sub_invoice_details_status,
                    'sub_invoice_details_datetime_paid_off' => $value_paid->sub_invoice_details_datetime_paid_off
                ];

                if (is_null($value_paid->trx_id)) {
                    print('trx_id NULL : '.$value_paid->sub_invoice_details_id);
                    print('<br>');
                    $i++;
                }
                else {
                    $check_inquiry_billing = $this->Bnim->inquiry_billing($value_paid->trx_id, true);
                    if (is_object($check_inquiry_billing)){
                        $check_inquiry_billing = (array) $check_inquiry_billing;
                    }

                    // if (isset($check_inquiry_billing['status'])) {
                    //     var_dump($check_inquiry_billing);
                    // }
                    // else 
                    if (isset($check_inquiry_billing['payment_amount'])) {
                        if ($check_inquiry_billing['payment_amount'] != $value_paid->sub_invoice_details_amount_paid) {
                            print('payment amount not same : '.$value_paid->sub_invoice_details_id.'('.$value_paid->sub_invoice_details_amount_paid.' != '.$check_inquiry_billing['payment_amount'].')');
                            print('<br>');
                            $i++;
                        }
                    }
                }

                // print('<br>');

            }
        }

        print('<h1>'.$i.'</h1>');
    }

    public function test_get()
    {
        $this->load->model('finance/Invoice_model', 'Im');
        
        $mba_trx_data = $this->Im->get_details_invoice_student([
            'sid.trx_id != ' => null,
            'sid.sub_invoice_details_amount_paid' => 0,
            'sid.sub_invoice_details_status != ' => 'paid'
        ]);

        if ($mba_trx_data) {
            print(count($mba_trx_data));exit;
        }
        else {
            var_dump($mba_trx_data);
        }
        
        // print('<pre>');
        // var_dump($mbo_invoice_data);
    }

    public function check_billing_srh()
    {
        $mba_srh = $this->General->get_where('dt_student_partner', [
            'student_partner_status' => 'accepted',
            'partner_program_id' => '5'
        ]);

        if ($mba_srh) {
            $this->load->model('finance/Invoice_model', 'Im');
            $this->load->model('finance/Bni_model', 'Bm');
            $this->load->model('partner/Partner_student_model', 'Psm');

            $a_invoice_id = [];
            $i_now = time();

            foreach ($mba_srh as $o_srh) {
                $mba_invoice_data = $this->Im->student_has_invoice_list($o_srh->personal_data_id, false, ['created', 'pending']);
                $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_srh->personal_data_id]);
                $mbo_student_data = $this->Psm->get_partner_student_data([
                    'sn.personal_data_id' => $o_srh->personal_data_id
                ]);
                $mbo_student_data = $mbo_student_data[0];

                if ($mba_invoice_data) {
                    foreach ($mba_invoice_data as $o_invoice) {
                        $d_total_fined = 0;
                        $b_send_reminder = false;

                        $mba_invoice_installment = $this->Im->get_invoice_installment($o_invoice->invoice_id);
                        if ($mba_invoice_installment) {
                            foreach ($mba_invoice_installment as $o_installment) {
                                if (($o_installment->sub_invoice_details_status != 'paid') AND ($o_installment->sub_invoice_details_amount > 0)) {
                                    // print('ada');exit;
                                    $i_deadline = strtotime($o_installment->sub_invoice_details_deadline);
                                    $i_datediff = $i_now - $i_deadline;
                                    $i_float = round($i_datediff / (60 * 60 * 24));
                                    
                                    if ($i_float >= 0) {
                                        // $a_update_sub_invoice_details = array(
                                        //     'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($o_installment->sub_invoice_details_deadline." +1 month"))
                                        // );

                                        // $d_amount_fined = 0;
                                        // $d_amount_total = $o_installment->sub_invoice_details_amount_total;
                                        // // if ($o_invoice->invoice_allow_fine == 'yes') {
                                        // //     if ($o_installment->sub_invoice_details_amount_fined <= 0) {
                                        // //         $d_amount_fined = 500000;
                                        // //         $d_amount_total = $o_installment->sub_invoice_details_amount + 500000;
                                        // //         $d_total_fined += $d_amount_fined;
                                        // //     }
                                        // //     else {
                                        // //         $d_total_fined += $o_installment->sub_invoice_details_amount_fined;
                                        // //     }
                                        // // }
                                        // $a_update_sub_invoice_details['sub_invoice_details_amount_fined'] = $d_amount_fined;
                                        // $a_update_sub_invoice_details['sub_invoice_details_amount_total'] = $d_amount_total;
                                        
                                        // $o_bni_data = $this->Bm->get_data_by_trx_id($o_installment->trx_id);
                                        // if ((isset($o_bni_data->va_status)) AND ($o_bni_data->va_status == 2)) {
                                        //     $a_billing_data = array(
                                        //         'trx_amount' => $d_amount_total,
                                        //         'billing_type' => 'c',
                                        //         'customer_name' => str_replace("'", "", $mba_personal_data[0]->personal_data_name),
                                        //         'virtual_account' => $o_installment->sub_invoice_details_va_number,
                                        //         'description' => $o_installment->sub_invoice_details_description,
                                        //         'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_installment->sub_invoice_details_deadline." +1 month")),
                                        //         'customer_email' => 'bni.employee@company.ac.id'
                                        //     );
                                        //     $a_bni_result = $this->Bm->create_billing($a_billing_data);
                                        //     if($a_bni_result['status'] == '000'){
                                        //         $a_update_sub_invoice_details['trx_id'] = $a_bni_result['trx_id'];
                                        //     }
                                        // }
                                        // else {
                                        //     $a_update_billing = array(
                                        //         'trx_id' => $o_installment->trx_id,
                                        //         'trx_amount' => $d_amount_total,
                                        //         'customer_name' => str_replace("'", "", $mba_personal_data[0]->personal_data_name),
                                        //         'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_installment->sub_invoice_details_deadline." +1 month")),
                                        //         'description' => $o_installment->sub_invoice_details_description,
                                        //         'customer_email' => 'bni.employee@company.ac.id'
                                        //     );
                                            
                                        //     $this->Bm->update_billing($a_update_billing);
                                        // }
                                        // $this->Im->update_sub_invoice_details($a_update_sub_invoice_details, array('sub_invoice_details_id' => $o_installment->sub_invoice_details_id));

                                        if ($i_float == 0) {
                                            $b_send_reminder = true;
                                        }
                                    }
                                    else if(($i_float >= -14) AND ($i_float < 0)) {
                                        $b_send_reminder = true;
                                    }


                                    // $a_update_sub_invoice_details = array(
                                    //     'sub_invoice_details_deadline' => date('Y-m-d 23:59:59', strtotime($o_installment->sub_invoice_details_real_datetime_deadline))
                                    // );

                                    // $d_amount_fined = '0';
                                    // $d_amount_total = $o_installment->sub_invoice_details_amount_total;
                                    // if ($o_invoice->invoice_allow_fine == 'yes') {
                                    //     if ($o_installment->sub_invoice_details_amount_fined <= 0) {
                                    //         $d_amount_fined = '0';
                                    //         $d_amount_total = $o_installment->sub_invoice_details_amount;
                                    //     }
                                    // }
                                    // $a_update_sub_invoice_details['sub_invoice_details_amount_fined'] = $d_amount_fined;
                                    // $a_update_sub_invoice_details['sub_invoice_details_amount_total'] = $d_amount_total;
                                    // $this->Im->update_sub_invoice_details($a_update_sub_invoice_details, array('sub_invoice_details_id' => $o_installment->sub_invoice_details_id));

                                    // $a_update_billing = array(
                                    //     'trx_id' => $o_installment->trx_id,
                                    //     'trx_amount' => $d_amount_total,
                                    //     'customer_name' => str_replace("'", "", $mba_personal_data[0]->personal_data_name),
                                    //     'datetime_expired' => date('Y-m-d 23:59:59', strtotime($o_installment->sub_invoice_details_real_datetime_deadline)),
                                    //     'description' => $o_installment->sub_invoice_details_description,
                                    //     'customer_email' => 'bni.employee@company.ac.id'
                                    // );
                                    
                                    // $this->Bm->update_billing($a_update_billing);
                                }
                            }

                            // $this->Im->update_sub_invoice(['sub_invoice_amount_total' => $mba_invoice_installment[0]->sub_invoice_amount + $d_total_fined], ['sub_invoice_id' => $mba_invoice_installment[0]->sub_invoice_id]);
                            // $this->Im->update_invoice(['invoice_amount_fined' => $d_total_fined], ['invoice_id' => $mba_invoice_installment[0]->invoice_id]);
                        }

                        if (($o_invoice->invoice_allow_reminder == 'yes') AND ($b_send_reminder)) {
                            $s_student_email = $mbo_student_data->personal_data_email;
						    $mba_cc_email = ['employee@company.ac.id'];
                            $a_bcc_email = array('employee@company.ac.id', 'employee@company.ac.id', 'employee@company.ac.id');
                            $a_email = $this->config->item('email');
					        $s_email_from = $a_email['finance']['payment'];

                            $o_invoice = $this->Im->get_unpaid_invoice(array('di.invoice_id' => $o_invoice->invoice_id))[0];
                            $mba_sub_invoice = $this->Im->get_sub_invoice_data(['dsi.invoice_id' => $o_invoice->invoice_id]);
                            $mba_invoice_details = $this->Im->get_invoice_details([
                                'did.invoice_id' => $o_invoice->invoice_id
                            ]);

                            foreach($mba_sub_invoice as $sub_invoice){
                                $mba_sub_invoice_details = $this->Im->get_invoice_data(['dsid.sub_invoice_id' => $sub_invoice->sub_invoice_id]);
                                $sub_invoice->sub_invoice_details_data = false;
                                
                                if($mba_sub_invoice_details){
                                    $sub_invoice->sub_invoice_details_data = $mba_sub_invoice_details;
                                }
                            }

                            $this->a_page_data['sub_invoice_data'] = $mba_sub_invoice;
                            $this->a_page_data['invoice_data'] = $o_invoice;
                            $this->a_page_data['invoice_details'] = $mba_invoice_details;
                            $s_html = $this->load->view('callback/email_template_srh', $this->a_page_data, TRUE);

                            $s_subject_email = "[REMINDER] Tuition Fee Invoice";
				            $config = $this->config->item('mail_config');
                            $config['mailtype'] = 'html';
                            $this->email->initialize($config);

                            $this->email->subject($s_subject_email);
					        $this->email->from($s_email_from, 'IULI Reminder System');

                            // $this->email->to('employee@company.ac.id');
                            $this->email->to($s_student_email);
                            if($mba_cc_email){
                                $this->email->cc($mba_cc_email);
                            }
                            $this->email->bcc($a_bcc_email);
                            $this->email->reply_to($a_email['finance']['head']);

                            $this->email->message($s_html);
                            if (!$this->email->send()) {
                                $a_return = ['code' => 1, 'message' => 'Email not send!'];
                                $this->log_activity('Email did not sent');
                                $this->log_activity('Error Message: '.$this->email->print_debugger());
                            }else{
                                $a_return = ['code' => 0, 'message' => 'Success'];
                            }

                            print json_encode($a_return);
                            print('<br>');
                        }
                    }
                }
            }
        }
    }

	public function checker_trx_id() {
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('finance/Invoice_model', 'Im');
        $mba_student_data = $this->Stm->get_student_filtered(false, ['register','candidate', 'pending', 'active', 'inactive', 'graduated', 'onleave']);
        $a_billed_amount_is_higher = 0;
        $a_billed_amount_is_lower = 0;
        $a_va_inactive = 0;
        $a_va_active = 0;
        $count_check = 0;
        if ($mba_student_data) {
            $s_file_name = 'Billing_Checker'.date('Y-m-d');
            $s_filename = $s_file_name.'.xlsx';
            $s_file_path = APPPATH.'uploads/devs/billing_checker/'.date('Y').'/'.date('m').'/';
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            // $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
            // $o_spreadsheet = IOFactory::load($s_template_path);
            $o_spreadsheet = new Spreadsheet();
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("IULI Finance Services");
            $o_sheet = $o_spreadsheet->getActiveSheet();

            $i_row = 1;
            $o_sheet->setCellValue("A$i_row", 'Student Name');
            $o_sheet->setCellValue("B$i_row", 'Study Program');
            $o_sheet->setCellValue("C$i_row", 'Student Number');
            $o_sheet->setCellValue("D$i_row", 'Virtual Account');
            $o_sheet->setCellValue("E$i_row", 'Minimum Payment');
            $o_sheet->setCellValue("F$i_row", 'BNI Billing');
            $o_sheet->setCellValue("G$i_row", 'VA Status');
            $o_sheet->setCellValue("H$i_row", 'BNI Created');
            $o_sheet->setCellValue("I$i_row", 'BNI Expired');
            $o_sheet->setCellValue("J$i_row", 'BNI message');
            $o_sheet->setCellValue("K$i_row", 'Invoice Description');

            $i_row++;
            foreach ($mba_student_data as $o_student) {
                $mba_billing_fee = $this->Im->get_student_billing([
                    'di.personal_data_id' => $o_student->personal_data_id
                ], 'fee.payment_type_code');
                if (($mba_billing_fee) AND (!is_null($o_student->student_number))) {
                    foreach ($mba_billing_fee as $o_billing) {
                        $billing_detail = modules::run('callback/api/get_list_billing', $o_student->student_id, $o_billing->payment_type_code);
                        // print('<pre>');var_dump($billing_detail);exit;
                        $count_check++;
                        if (count($billing_detail) == 0) {
                            $o_sheet->setCellValue("A$i_row", $o_student->personal_data_name);
                            $o_sheet->setCellValue("B$i_row", $o_student->study_program_abbreviation);
                            $o_sheet->setCellValue("C$i_row", $o_student->student_number);
                            $o_sheet->setCellValue("D$i_row", 'Billing detail not found!');
                        }
                        else {
                            $min_payment = $billing_detail['min_payment'];
                            $va_number = $billing_detail['va_number'];
                            $va_checked = $billing_detail['check_va'];
                            
                            $bni_billing = '-';
                            $bni_customer = '-';
                            $bni_created = '-';
                            $bni_expired = '-';
                            $bni_message = '';
                            if (($va_checked) AND (!array_key_exists('status', $va_checked))) {
                                $bni_billing = $va_checked['trx_amount'];
                                $bni_customer = $va_checked['customer_name'];
                                $bni_created = $va_checked['datetime_created'];
                                $bni_expired = $va_checked['datetime_expired'];
                                $bni_message = '';
                            }
                            $s_va_status = 'Not found';
                            switch ($billing_detail['va_status']) {
                                case 1:
                                    $a_va_active++;
                                    $s_va_status = 'VA active';
                                    break;
                                case 2:
                                    $a_va_inactive++;
                                    $s_va_status = 'VA inactive';
                                    break;
                                case 3:
                                    $a_billed_amount_is_lower++;
                                    $s_va_status = 'Minimum payment portal is lower than bni billing';
                                    break;
                                case 4:
                                    $a_billed_amount_is_higher++;
                                    $s_va_status = 'Minimum payment portal is higher than bni billing';
                                    break;
                                
                                default:
                                    break;
                            }
        
                            $o_sheet->setCellValue("A$i_row", $o_student->personal_data_name);
                            $o_sheet->setCellValue("B$i_row", $o_student->study_program_abbreviation);
                            $o_sheet->setCellValue("C$i_row", $o_student->student_number);
                            $o_sheet->setCellValue("D$i_row", $va_number);
                            $o_sheet->setCellValue("E$i_row", $min_payment);
                            $o_sheet->setCellValue("F$i_row", $bni_billing);
                            $o_sheet->setCellValue("G$i_row", $s_va_status);
                            $o_sheet->setCellValue("H$i_row", $bni_created);
                            $o_sheet->setCellValue("I$i_row", $bni_expired);
                            $o_sheet->setCellValue("J$i_row", $bni_message);
                            $o_sheet->setCellValue("K$i_row", $o_billing->invoice_description);
                        }
                        $i_row++;
                    }
                }
            }

            $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            $execution_time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $date_now = date('d F Y H:i:s');

            $s_text_body = <<<TEXT
{$date_now}
Checking billing from bni with result:
Total Checking: {$count_check} data trx_id (conditional: != paid and paid = 0 in staging_admissio)
with execution time: {$execution_time} s

Result: 
Total VA Active: {$a_va_active}
Total VA Inactive: {$a_va_inactive}
Total Tagihan tidak sesuai dengan BNI (Lebih Tinggi): {$a_billed_amount_is_higher}
Total Tagihan tidak sesuai dengan BNI (Lebih Rendah): {$a_billed_amount_is_lower}
TEXT;

            $config = $this->config->item('mail_config');
            $this->email->initialize($config);
            $this->email->from('employee@company.ac.id', '[Checker] IT IULI Service Centre');
            // $this->email->to('employee@company.ac.id');
            $this->email->to('employee@company.ac.id');
            $this->email->attach($s_file_path.$s_filename);
            $this->email->subject("Checker invoice result ");
            $this->email->message($s_text_body);
            $this->email->send();
            $this->email->clear(TRUE);
            exit;
        }
    }
	public function old_checker_trx_id()
    {
        print('menunggu perbaikan karena perubahan va');exit;
		$this->load->model('finance/Invoice_model', 'Im');
        $this->load->model('finance/Finance_model', 'Fm');
		$this->load->model('finance/Bni_model', 'Bnim');

		$a_student_by_pass = ['pending', 'participant', 'resign'];

        // $mba_trx_data = $this->General->get_where('dt_sub_invoice_details', [
        //     'trx_id != ' => null,
        //     'sub_invoice_details_amount > ' => 0,
        //     'sub_invoice_details_amount_paid' => 0,
        //     'sub_invoice_details_status != ' => 'paid'
        // ]);
        $mba_trx_data = $this->Im->get_trx_invoice_for_checking([
            'did.trx_id != ' => null,
            'did.sub_invoice_details_amount > ' => 0,
            'did.sub_invoice_details_amount_paid' => 0,
            'did.sub_invoice_details_status != ' => 'paid',
            'di.invoice_status != ' => 'paid'
        ], ['candidate', 'active', 'inactive', 'dropout', 'resign', 'graduated', 'onleave']);
        // print('<pre>');
        // var_dump($mba_trx_data);exit;
        
        
        // $mba_trx_paid = $this->General->get_where('dt_sub_invoice_details', [
        //     'sub_invoice_details_status' => 'paid',
        //     'date_added > ' => '2021-01-01 00:00:00'
        // ]);
        
        // $a_trx_not_found_in_invoice = [];
        $a_personal_data_not_found = [];
        $a_trx_error = [];
        $a_customer_not_found = [];
        $a_va_not_found = [];
        $a_billed_amount_not_same = [];
        $a_paid_amount_not_same = [];
        $a_paid_fake = [];

        if ($mba_trx_data) {
            // print(count($mba_trx_data));
            // // print('<pre>');var_dump($mba_trx_data[0]);
            // exit;
            foreach ($mba_trx_data as $o_sub_invoice_details) {
                // $mbo_invoice_data = $this->Im->get_details_invoice_student([
                //     'sid.trx_id' => $o_sub_invoice_details->trx_id
                // ]);

                // if (!$mbo_invoice_data) {
                //     array_push($a_trx_not_found_in_invoice, $o_sub_invoice_details->trx_id);
                // }else{
                    $o_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_sub_invoice_details->personal_data_id])[0];
                    if (!$o_personal_data) {
                        array_push($a_personal_data_not_found, $o_sub_invoice_details->personal_data_id);
                    }else{
						$mba_student_data = $this->General->get_where('dt_student', ['personal_data_id' => $o_personal_data->personal_data_id]);
						$b_is_participant_pending = false;

						if (count($mba_student_data) == 1) {
							if (in_array($mba_student_data[0]->student_status, $a_student_by_pass)) {
								$b_is_participant_pending = true;
							}
						}

						if (!$b_is_participant_pending) {
							$s_customer_personal_data_name = strtolower($o_personal_data->personal_data_name);

							$check_inquiry_billing = $this->Bnim->inquiry_billing($o_sub_invoice_details->trx_id, true);
							if (is_object($check_inquiry_billing)){
								$check_inquiry_billing = (array) $check_inquiry_billing;
							}

							if (isset($check_inquiry_billing['status'])) {
								$check_inquiry_billing['trx_id'] = $o_sub_invoice_details->trx_id;
								array_push($a_trx_error, $check_inquiry_billing);
							}
                            
                            if ($check_inquiry_billing['customer_name'] != 'CANCEL PAYMENT') {
                                if (strtolower($check_inquiry_billing['customer_name']) != $s_customer_personal_data_name) {
                                    array_push($a_customer_not_found, $check_inquiry_billing);
                                }
                                
                                if ($check_inquiry_billing['virtual_account'] != $o_sub_invoice_details->sub_invoice_details_va_number) {
                                    array_push($a_va_not_found, $check_inquiry_billing);
                                }
                                
                                if ($check_inquiry_billing['trx_amount'] != $o_sub_invoice_details->sub_invoice_details_amount_total) {
                                    array_push($a_billed_amount_not_same, $check_inquiry_billing);
                                }
                                
                                if ($check_inquiry_billing['payment_amount'] != $o_sub_invoice_details->sub_invoice_details_amount_paid) {
                                    array_push($a_paid_amount_not_same, $check_inquiry_billing);
                                }
                            }
						}
                        
                    }
                // }
                // break;
            }
        }

        // print('<pre>');
        // var_dump(count($a_va_not_found));exit;

        // if ($mba_trx_paid) {
        //     foreach ($mba_trx_paid as $key_paid => $value_paid) {
        //         $a_data = [
        //             'sub_invoice_details_id' => $value_paid->sub_invoice_details_id,
        //             'trx_id' => $value_paid->trx_id,
        //             'virtual_account' => $value_paid->sub_invoice_details_va_number,
        //             'sub_invoice_details_amount_total' => $value_paid->sub_invoice_details_amount_total,
        //             'sub_invoice_details_status' => $value_paid->sub_invoice_details_status,
        //             'sub_invoice_details_datetime_paid_off' => $value_paid->sub_invoice_details_datetime_paid_off
        //         ];

        //         if (is_null($value_paid->trx_id)) {
        //             $a_data['system_notes'] = 'Transaksi belum dibuat di bni!';
        //         }
        //         else {
        //             $check_inquiry_billing = $this->Bnim->inquiry_billing($value_paid->trx_id, true);
        //             if (is_object($check_inquiry_billing)){
        //                 $check_inquiry_billing = (array) $check_inquiry_billing;
        //             }

        //             // if (isset($check_inquiry_billing['status'])) {
        //             //     $a_data['system_notes'] = json_encode($check_inquiry_billing);
        //             // }
        //             // else 
        //             if ($check_inquiry_billing['payment_amount'] != $value_paid->sub_invoice_details_amount_paid) {
        //                 $a_data['system_notes'] = 'Transaksi belum dibayar di bni!';
        //             }
        //         }

        //         array_push($a_paid_fake, $a_data);
        //     }
        // }

        $execution_time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];

        $s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
        $s_file_name = 'Billing_Checker'.date('Y-m-d');
        $s_filename = $s_file_name.'.xlsx';

        $s_file_path = APPPATH.'uploads/devs/billing_checker/'.date('Y').'/'.date('m').'/';

        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0777, TRUE);
        }
        
        $o_spreadsheet = IOFactory::load($s_template_path);
        $o_spreadsheet->getProperties()
            ->setTitle($s_file_name)
            ->setCreator("IULI ITE Services")
            ->setCategory("Billing Checker");
            
        // $o_sheet_trx_not_found = $o_spreadsheet->getActiveSheet()->setTitle('trx_id tidak ada');
        
        // $i_row = 1;
        // $o_sheet_trx_not_found->setCellValue('A'.$i_row, 'trx_id tidak ada di data invoice');
        // $i_row++;
        // if (count($a_trx_not_found_in_invoice) > 0) {
        //     foreach ($a_trx_not_found_in_invoice as $s_trx_id) {
        //         $o_sheet_trx_not_found->setCellValue('A'.$i_row, '="'.$s_trx_id.'"');
        //         $i_row++;
        //     }
        // }

        $o_spreadsheet->createSheet();
        $o_spreadsheet->setActiveSheetIndex(1);
        $o_sheet_personal_not_found = $o_spreadsheet->getActiveSheet()->setTitle('personal_data tidak ada');

        $i_row = 1;
        $o_sheet_personal_not_found->setCellValue('A'.$i_row, 'personal_data_id invoice tidak ada di data personal_data');
        $i_row++;
        if (count($a_personal_data_not_found) > 0) {
            foreach ($a_personal_data_not_found as $s_personal_data_id) {
                $o_sheet_personal_not_found->setCellValue('A'.$i_row, '="'.$s_personal_data_id.'"');
                $i_row++;
            }
        }
        
        $o_spreadsheet->createSheet();
        $o_spreadsheet->setActiveSheetIndex(2);
        $o_sheet_trx_id_error = $o_spreadsheet->getActiveSheet()->setTitle('trx_id error');

        $i_row = 1;
        $o_sheet_trx_id_error->setCellValue('A'.$i_row, 'trx_id');
        $o_sheet_trx_id_error->setCellValue('B'.$i_row, 'error status');
        $o_sheet_trx_id_error->setCellValue('C'.$i_row, 'message');
        $i_row++;
        if (count($a_trx_error) > 0) {
            foreach ($a_trx_error as $a_check_inquiry_billing) {
                $o_sheet_trx_id_error->setCellValue('A'.$i_row, '="'.$a_check_inquiry_billing['trx_id'].'"');
                $o_sheet_trx_id_error->setCellValue('B'.$i_row, $a_check_inquiry_billing['status']);
                $o_sheet_trx_id_error->setCellValue('C'.$i_row, $a_check_inquiry_billing['message']);
                $i_row++;
            }
        }
        
        $o_spreadsheet->createSheet();
        $o_spreadsheet->setActiveSheetIndex(3);
        $o_sheet_customer_tidak_sama = $o_spreadsheet->getActiveSheet()->setTitle('customer tidak sama');

        $i_row = 1;
        $o_sheet_customer_tidak_sama->setCellValue('A'.$i_row, 'trx_id');
        $o_sheet_customer_tidak_sama->setCellValue('B'.$i_row, 'virtual_account');
        $o_sheet_customer_tidak_sama->setCellValue('C'.$i_row, 'trx_amount');
        $o_sheet_customer_tidak_sama->setCellValue('D'.$i_row, 'customer_name');
        $o_sheet_customer_tidak_sama->setCellValue('E'.$i_row, 'payment_amount');
        $o_sheet_customer_tidak_sama->setCellValue('F'.$i_row, 'va_status');
        $o_sheet_customer_tidak_sama->setCellValue('G'.$i_row, 'datetime_created');
        $o_sheet_customer_tidak_sama->setCellValue('H'.$i_row, 'datetime_expired');
        $o_sheet_customer_tidak_sama->setCellValue('I'.$i_row, 'description');
        $i_row++;
        if (count($a_customer_not_found) > 0) {
            foreach ($a_customer_not_found as $a_check_inquiry_billing) {
                $o_sheet_customer_tidak_sama->setCellValue('A'.$i_row, '="'.$a_check_inquiry_billing['trx_id'].'"');
                $o_sheet_customer_tidak_sama->setCellValue('B'.$i_row, '="'.$a_check_inquiry_billing['virtual_account'].'"');
                $o_sheet_customer_tidak_sama->setCellValue('C'.$i_row, $a_check_inquiry_billing['trx_amount']);
                $o_sheet_customer_tidak_sama->setCellValue('D'.$i_row, $a_check_inquiry_billing['customer_name']);
                $o_sheet_customer_tidak_sama->setCellValue('E'.$i_row, $a_check_inquiry_billing['payment_amount']);
                $o_sheet_customer_tidak_sama->setCellValue('F'.$i_row, $a_check_inquiry_billing['va_status']);
                $o_sheet_customer_tidak_sama->setCellValue('G'.$i_row, $a_check_inquiry_billing['datetime_created']);
                $o_sheet_customer_tidak_sama->setCellValue('H'.$i_row, $a_check_inquiry_billing['datetime_expired']);
                $o_sheet_customer_tidak_sama->setCellValue('I'.$i_row, $a_check_inquiry_billing['description']);
                $i_row++;
            }
        }
        
        $o_spreadsheet->createSheet();
        $o_spreadsheet->setActiveSheetIndex(4);
        $o_sheet_virtual_account_tidak_sama = $o_spreadsheet->getActiveSheet()->setTitle('virtual account tidak sama');

        $i_row = 1;
        $o_sheet_virtual_account_tidak_sama->setCellValue('A'.$i_row, 'trx_id');
        $o_sheet_virtual_account_tidak_sama->setCellValue('B'.$i_row, 'virtual_account');
        $o_sheet_virtual_account_tidak_sama->setCellValue('C'.$i_row, 'trx_amount');
        $o_sheet_virtual_account_tidak_sama->setCellValue('D'.$i_row, 'customer_name');
        $o_sheet_virtual_account_tidak_sama->setCellValue('E'.$i_row, 'payment_amount');
        $o_sheet_virtual_account_tidak_sama->setCellValue('F'.$i_row, 'va_status');
        $o_sheet_virtual_account_tidak_sama->setCellValue('G'.$i_row, 'datetime_created');
        $o_sheet_virtual_account_tidak_sama->setCellValue('H'.$i_row, 'datetime_expired');
        $o_sheet_virtual_account_tidak_sama->setCellValue('I'.$i_row, 'description');
        $i_row++;
        if (count($a_va_not_found) > 0) {
            foreach ($a_va_not_found as $a_check_inquiry_billing) {
                $o_sheet_virtual_account_tidak_sama->setCellValue('A'.$i_row, '="'.$a_check_inquiry_billing['trx_id'].'"');
                $o_sheet_virtual_account_tidak_sama->setCellValue('B'.$i_row, '="'.$a_check_inquiry_billing['virtual_account'].'"');
                $o_sheet_virtual_account_tidak_sama->setCellValue('C'.$i_row, $a_check_inquiry_billing['trx_amount']);
                $o_sheet_virtual_account_tidak_sama->setCellValue('D'.$i_row, $a_check_inquiry_billing['customer_name']);
                $o_sheet_virtual_account_tidak_sama->setCellValue('E'.$i_row, $a_check_inquiry_billing['payment_amount']);
                $o_sheet_virtual_account_tidak_sama->setCellValue('F'.$i_row, $a_check_inquiry_billing['va_status']);
                $o_sheet_virtual_account_tidak_sama->setCellValue('G'.$i_row, $a_check_inquiry_billing['datetime_created']);
                $o_sheet_virtual_account_tidak_sama->setCellValue('H'.$i_row, $a_check_inquiry_billing['datetime_expired']);
                $o_sheet_virtual_account_tidak_sama->setCellValue('I'.$i_row, $a_check_inquiry_billing['description']);
                $i_row++;
            }
        }
        
        $o_spreadsheet->createSheet();
        $o_spreadsheet->setActiveSheetIndex(5);
        $o_sheet_tagihan_tidak_sama = $o_spreadsheet->getActiveSheet()->setTitle('tagihan tidak sama');

        $i_row = 1;
        $o_sheet_tagihan_tidak_sama->setCellValue('A'.$i_row, 'trx_id');
        $o_sheet_tagihan_tidak_sama->setCellValue('B'.$i_row, 'virtual_account');
        $o_sheet_tagihan_tidak_sama->setCellValue('C'.$i_row, 'trx_amount');
        $o_sheet_tagihan_tidak_sama->setCellValue('D'.$i_row, 'customer_name');
        $o_sheet_tagihan_tidak_sama->setCellValue('E'.$i_row, 'payment_amount');
        $o_sheet_tagihan_tidak_sama->setCellValue('F'.$i_row, 'va_status');
        $o_sheet_tagihan_tidak_sama->setCellValue('G'.$i_row, 'datetime_created');
        $o_sheet_tagihan_tidak_sama->setCellValue('H'.$i_row, 'datetime_expired');
        $o_sheet_tagihan_tidak_sama->setCellValue('I'.$i_row, 'description');
        $i_row++;
        if (count($a_billed_amount_not_same) > 0) {
            foreach ($a_billed_amount_not_same as $a_check_inquiry_billing) {
                $o_sheet_tagihan_tidak_sama->setCellValue('A'.$i_row, '="'.$a_check_inquiry_billing['trx_id'].'"');
                $o_sheet_tagihan_tidak_sama->setCellValue('B'.$i_row, '="'.$a_check_inquiry_billing['virtual_account'].'"');
                $o_sheet_tagihan_tidak_sama->setCellValue('C'.$i_row, $a_check_inquiry_billing['trx_amount']);
                $o_sheet_tagihan_tidak_sama->setCellValue('D'.$i_row, $a_check_inquiry_billing['customer_name']);
                $o_sheet_tagihan_tidak_sama->setCellValue('E'.$i_row, $a_check_inquiry_billing['payment_amount']);
                $o_sheet_tagihan_tidak_sama->setCellValue('F'.$i_row, $a_check_inquiry_billing['va_status']);
                $o_sheet_tagihan_tidak_sama->setCellValue('G'.$i_row, $a_check_inquiry_billing['datetime_created']);
                $o_sheet_tagihan_tidak_sama->setCellValue('H'.$i_row, $a_check_inquiry_billing['datetime_expired']);
                // $o_sheet_tagihan_tidak_sama->setCellValue('I'.$i_row, $a_check_inquiry_billing['description']);
                $i_row++;
            }
        }
        
        $o_spreadsheet->createSheet();
        $o_spreadsheet->setActiveSheetIndex(6);
        $o_sheet_dibayar_tidak_sama = $o_spreadsheet->getActiveSheet()->setTitle('dibayar tidak sama');

        $i_row = 1;
        $o_sheet_dibayar_tidak_sama->setCellValue('A'.$i_row, 'trx_id');
        $o_sheet_dibayar_tidak_sama->setCellValue('B'.$i_row, 'virtual_account');
        $o_sheet_dibayar_tidak_sama->setCellValue('C'.$i_row, 'trx_amount');
        $o_sheet_dibayar_tidak_sama->setCellValue('D'.$i_row, 'customer_name');
        $o_sheet_dibayar_tidak_sama->setCellValue('E'.$i_row, 'payment_amount');
        $o_sheet_dibayar_tidak_sama->setCellValue('F'.$i_row, 'va_status');
        $o_sheet_dibayar_tidak_sama->setCellValue('G'.$i_row, 'datetime_created');
        $o_sheet_dibayar_tidak_sama->setCellValue('H'.$i_row, 'datetime_expired');
        $o_sheet_dibayar_tidak_sama->setCellValue('I'.$i_row, 'description');
        $i_row++;
        if (count($a_paid_amount_not_same) > 0) {
            foreach ($a_paid_amount_not_same as $a_check_inquiry_billing) {
                $o_sheet_dibayar_tidak_sama->setCellValue('A'.$i_row, '="'.$a_check_inquiry_billing['trx_id'].'"');
                $o_sheet_dibayar_tidak_sama->setCellValue('B'.$i_row, '="'.$a_check_inquiry_billing['virtual_account'].'"');
                $o_sheet_dibayar_tidak_sama->setCellValue('C'.$i_row, $a_check_inquiry_billing['trx_amount']);
                $o_sheet_dibayar_tidak_sama->setCellValue('D'.$i_row, $a_check_inquiry_billing['customer_name']);
                $o_sheet_dibayar_tidak_sama->setCellValue('E'.$i_row, $a_check_inquiry_billing['payment_amount']);
                $o_sheet_dibayar_tidak_sama->setCellValue('F'.$i_row, $a_check_inquiry_billing['va_status']);
                $o_sheet_dibayar_tidak_sama->setCellValue('G'.$i_row, $a_check_inquiry_billing['datetime_created']);
                $o_sheet_dibayar_tidak_sama->setCellValue('H'.$i_row, $a_check_inquiry_billing['datetime_expired']);
                $o_sheet_dibayar_tidak_sama->setCellValue('I'.$i_row, $a_check_inquiry_billing['description']);
                $i_row++;
            }
        }

        // $o_spreadsheet->createSheet();
        // $o_spreadsheet->setActiveSheetIndex(7);
        // $o_sheet_paid_fake = $o_spreadsheet->getActiveSheet()->setTitle('Paid Fake created 2021');

        // $i_row = 1;
        // $o_sheet_paid_fake->setCellValue('A'.$i_row, 'sub_invoice_details_id');
        // $o_sheet_paid_fake->setCellValue('B'.$i_row, 'system_notes');
        // $o_sheet_paid_fake->setCellValue('C'.$i_row, 'trx_id');
        // $o_sheet_paid_fake->setCellValue('D'.$i_row, 'virtual_account');
        // $o_sheet_paid_fake->setCellValue('E'.$i_row, 'sub_invoice_details_amount_total');
        // $o_sheet_paid_fake->setCellValue('F'.$i_row, 'sub_invoice_details_status');
        // $o_sheet_paid_fake->setCellValue('G'.$i_row, 'sub_invoice_details_datetime_paid_off');
        
        // $i_row++;
        // if (count($a_paid_fake) > 0) {
        //     foreach ($a_paid_fake as $a_data) {
        //         $o_sheet_paid_fake->setCellValue('A'.$i_row, '="'.$a_data['sub_invoice_details_id'].'"');
        //         $o_sheet_paid_fake->setCellValue('B'.$i_row, $a_data['system_notes']);
        //         $o_sheet_paid_fake->setCellValue('C'.$i_row, '="'.$a_data['trx_id'].'"');
        //         $o_sheet_paid_fake->setCellValue('D'.$i_row, '="'.$a_data['virtual_account'].'"');
        //         $o_sheet_paid_fake->setCellValue('E'.$i_row, '="'.$a_data['sub_invoice_details_amount_total'].'"');
        //         $o_sheet_paid_fake->setCellValue('F'.$i_row, $a_data['sub_invoice_details_status']);
        //         $o_sheet_paid_fake->setCellValue('G'.$i_row, '="'.$a_data['sub_invoice_details_datetime_paid_off'].'"');
        //         $i_row++;
        //     }
        // }
        
        $o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
        $o_writer->save($s_file_path.$s_filename);
        $o_spreadsheet->disconnectWorksheets();
        unset($o_spreadsheet);

        $count_check = count($mba_trx_data);
        // $count_trx_not_found = count($a_trx_not_found_in_invoice);
        $count_customer_not_found = count($a_personal_data_not_found);
        $count_trx_id_error = count($a_trx_error);
        $count_customer_name_is_not_same = count($a_customer_not_found);
        $count_va_not_same = count($a_va_not_found);
        $count_billed_amount_not_same = count($a_billed_amount_not_same);
        $count_paid_amount_not_same = count($a_paid_amount_not_same);
        // $count_paid_fake = count($a_paid_fake);
        
        $s_text_body = <<<TEXT
Checking billing from bni with result:
Total Checking: {$count_check} data trx_id (conditional: != paid and paid = 0 in staging_admissio)
with execution time: {$execution_time} s

Result: 
Total personal_data_id invoice tidak ada di data personal_data: {$count_customer_not_found}
Total trx_id error di bni: {$count_trx_id_error}
Total customer tidak sama: {$count_customer_name_is_not_same}
Total virtual account tidak sama: {$count_va_not_same}
Total tagihan tidak sama: {$count_billed_amount_not_same}
Total dibayar tidak sama: {$count_paid_amount_not_same}
TEXT;

		// $s_text_body = "<p>Checking billing from bni with result:</p>
		// 	<p>Total Checking: {$count_check} data trx_id (conditional: != paid and paid = 0 in staging_admissio)<br>
		// 	with execution time: {$execution_time} s</p>
		// 	<br>
		// 	<p>Result: </p>
		// 	<li>Total trx_id tidak ditemukan di invoice: {$count_trx_not_found}</li>
		// 	<li>Total personal_data_id invoice tidak ada di data personal_data: {$count_customer_not_found}</li>
		// 	<li>Total trx_id error di bni: {$count_trx_id_error}</li>
		// 	<li>Total customer tidak sama: {$count_customer_name_is_not_same}</li>
		// 	<li>Total virtual account tidak sama: {$count_va_not_same}</li>
		// 	<li>Total tagihan tidak sama: {$count_billed_amount_not_same}</li>
		// 	<li>Total dibayar tidak sama: {$count_paid_amount_not_same}</li>";
     
		$config = $this->config->item('mail_config');
		// $config['mailtype'] = 'html';
		$this->email->initialize($config);
        $this->email->from('employee@company.ac.id', '[Checker] IT IULI Service Centre');
        // $this->email->to('employee@company.ac.id');
        $this->email->to('employee@company.ac.id');
        $this->email->attach($s_file_path.$s_filename);
        $this->email->subject("Checker invoice result ".date('d F Y H:i:s'));
        $this->email->message($s_text_body);
		$this->email->send();
		$this->email->clear(TRUE);
		exit;

        // $a_path_info = pathinfo($s_file_path.$s_filename);
        // $s_file_ext = $a_path_info['extension'];
        // header('Content-Disposition: attachment; filename='.urlencode($s_filename));
        // readfile( $s_file_path.$s_filename );
        // exit;
    }
}