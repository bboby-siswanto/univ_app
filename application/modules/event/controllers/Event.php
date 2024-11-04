<?php
class Event extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('event/Event_model', 'Evt');
		$this->load->model('student/Student_model', 'Stm');
		$this->load->model('admission/Admission_model', 'Adm');
	}

	public function public($s_slug = false)
	{
		// if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        //     $this->a_page_data['body'] = maintenance_page(true);
        //     $this->load->view('layout_public', $this->a_page_data);
        //     // exit;
        // }
		// else if ($s_slug) {
			$mba_event_data = $this->Evt->get_event(['event_slug' => $s_slug]);
			if ($mba_event_data) {
				$this->a_page_data['event_data'] = $mba_event_data[0];
				$this->a_page_data['event_field'] = $this->Evt->get_event_field([
					'ef.event_id' => $mba_event_data[0]->event_id
				]);
				// print('<pre>');var_dump($this->a_page_data['event_field']);exit;
				$this->a_page_data['body'] = $this->load->view('event/public/registration', $this->a_page_data, true);
				$this->load->view('layout_public', $this->a_page_data);
			}
			else {
				show_404();
			}
		// }
		// else {
		// 	show_404();
		// }
	}

	public function push_candidate()
	{
		if ($this->input->is_ajax_request()) {
			$s_booking_id = $this->input->post('booking_id');
			$mba_booking_data = $this->General->get_where('dt_event_bookings', ['booking_id' => $s_booking_id]);
			if (!$mba_booking_data) {
				$a_return  = ['code' => 1, 'message' => 'Participant event data not found!'];
			}
			else {
				$o_booking_data = $mba_booking_data[0];
				if (is_null($o_booking_data->booking_email)) {
					$a_return = ['code' => 1, 'message' => 'participant email is null, cannt push to student data!'];
				}
				else {
					$mba_candidate_has_student = $this->Adm->get_candidate_student(null, [
						'pd.personal_data_email' => $o_booking_data->booking_email
					]);
	
					if ($mba_candidate_has_student) {
						$a_return = ['code' => 1, 'message' => 'Participant status is '.$mba_candidate_has_student[0]->student_status];
					}
					else {
						// set candidate student
					}
				}
			}
		}
	}

	public function booking_list($s_slug = false)
	{
		if (!$s_slug) {
			show_404();
		}
		else {
			$mba_event_data = $this->Evt->get_event([
				'event_slug' => $s_slug
			]);

			if ($mba_event_data) {
				$this->a_page_data['event_field'] = $this->Evt->get_event_field([
					'ef.event_id' => $mba_event_data[0]->event_id
				]);
				$this->a_page_data['event_data'] = $mba_event_data[0];
				$this->a_page_data['body'] = $this->load->view('event/booking_list', $this->a_page_data, true);
				$this->load->view('layout', $this->a_page_data);
			}
			else {
				show_404();
			}
		}
	}

	public function new_event()
	{
		if ($this->input->is_ajax_request()) {
			// print('<pre>');var_dump($this->input->post());exit;
			$this->form_validation->set_rules('fields_id[]', 'Field for Registration', 'required');
			$this->form_validation->set_rules('event_name', 'Even Name', 'required|trim');
			$this->form_validation->set_rules('event_slug', 'Slug', 'required|trim');
			$this->form_validation->set_rules('event_venue', 'Venue', 'trim');
			$this->form_validation->set_rules('event_rundown', 'Run Down', 'trim');
			$this->form_validation->set_rules('event_date_start', 'Start Date', 'required|trim');
			$this->form_validation->set_rules('event_date_end', 'End Date', 'required|trim');
			$this->form_validation->set_rules('event_allocation', 'Allocation', 'required|trim');
			$this->form_validation->set_rules('submit_test', 'automatically add English online test members', 'trim');
			$this->form_validation->set_rules('public_event', 'Publish Event', 'trim');

			if ($this->form_validation->run()) {
				$s_slug_value = strtolower(str_replace(' ', '_', set_value('event_slug')));
				$mba_slug_value = $this->General->get_where('dt_event', ['event_slug' => $s_slug_value]);
				if ((empty($this->input->post('event_id'))) AND ($mba_slug_value)) {
					$a_return = ['code' => 1, 'message' => 'Slug is already used!'];
				}
				else {
					$this->db->trans_begin();
					$s_event_id = $this->uuid->v4();
					$a_data = [
						'event_id' => $s_event_id,
						'event_slug' => $s_slug_value,
						'event_name' => set_value('event_name'),
						'event_venue' => (empty(set_value('event_venue'))) ? NULL : set_value('event_venue'),
						'event_run_down' => (empty(set_value('event_rundown'))) ? NULL : set_value('event_rundown'),
						'event_start_date' => set_value('event_date_start'),
						'event_end_date' => set_value('event_date_end'),
						'event_type' => set_value('event_allocation'),
						'event_is_public' => (empty(set_value('public_event'))) ? 0 : 1,
						'event_submit_test_automaticly' => (empty(set_value('submit_test'))) ? 'false' : 'true',
						'date_added' => date('Y-m-d H:i:s')
					];
	
					if (!empty($this->input->post('event_id'))) {
						$s_event_id = $this->input->post('event_id');
						unset($a_data['event_id']);
						unset($a_data['date_added']);
						$event = $this->Evt->update_event($a_data, $this->input->post('event_id'));
					}
					else {
						$event = $this->Evt->create_event($a_data);
					}

					// prep event field
					if (is_array(set_value('fields_id'))) {
						$a_eventfield_data = [];
						foreach (set_value('fields_id') as $key => $s_field_id) {
							$field_data = $this->General->get_where('ref_event_field', ['field_id' => $s_field_id]);
							$option = NULL;
							if (!empty($this->input->post('fields_option')[$key])) {
								$s_option_string = $this->input->post('fields_option')[$key];
								$a_option_string = explode(',', $s_option_string);
								$a_option_field = [];
								foreach ($a_option_string as $s_field) {
									array_push($a_option_field, trim(str_replace("'", "", $s_field)));
								}
								$option = implode(';', $a_option_field);
							}
							$a_event_field_data = [
								'event_field_id' => $this->uuid->v4(),
								'event_id' => $s_event_id,
								'field_id' => $s_field_id,
								'field_title' => $this->input->post('fields_title')[$key],
								'field_option' => $option,
								'field_input_type' => (($field_data) AND (!is_null($field_data[0]->field_input_type_default))) ? $field_data[0]->field_input_type_default : 'text',
							];
							array_push($a_eventfield_data, $a_event_field_data);
						}

						$this->Evt->submit_event_field($s_event_id, $a_eventfield_data);
					}
					
					if ($this->db->trans_status() === TRUE) {
						$this->db->trans_commit();
						$a_post_data = [
							'access_token' => 'PUBLICAPI',
							'data' => $a_data
						];
						$this->libapi->post_data('https://pmb.iuli.ac.id/api/event/create_event', json_encode($a_post_data));
						$a_return = array('code' => 0, 'message' => 'Success');
					}
					else {
						$this->db->trans_rollback();
						$a_return = array('code' => 1, 'message' => 'Failed submit event data!');
					}
				}
			}
			else {
				$a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
			}

			print json_encode($a_return);
		}
		else {
			show_404();
		}
	}
	
	public function lists()
	{
		$this->a_page_data['field_list'] = $this->General->get_where('ref_event_field');
		$this->a_page_data['body'] = $this->load->view('lists', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function download_attachments()
	{
		if($this->input->is_ajax_request()){
			$mba_bookings_data = $this->libapi->post_data('https://pmb.iuli.ac.id/api/event/get_bookings', json_encode([
				'access_token' => 'PUBLICAPI',
				'data' => [
					'event_id' => $this->input->post('event_id')
				]
			]));
			
			$s_event_path = APPPATH.'uploads/events/'.$this->input->post('event_id').'/';
			if(!file_exists($s_event_path)){
				mkdir($s_event_path, 0755, true);
			}
			
			if (count($mba_bookings_data->data) >= 1) {
				$this->load->library('zip');
				$this->zip->compression_level = 0;
				
				foreach ($mba_bookings_data->data as $item) {
					if(!is_null($item->booking_photo) && ($item->booking_participation == 'present')){
						list($type, $data) = explode(';', $item->booking_photo);
						list(, $data) = explode(',', $data);
						$data = base64_decode($data);
						$s_file_name = implode('_', [$item->booking_name, $item->booking_id]).".png";
						file_put_contents($s_event_path.$s_file_name, $data);
						$this->zip->add_data($s_event_path.$s_file_name, $s_file_name);
					}
				}

				$s_archive_path = $s_event_path.'archive.zip';
				$this->zip->archive($s_archive_path);
				$this->session->set_flashdata('file_token', $s_archive_path);
				$rtn = ['code' => 0];
			}
			
			print json_encode($rtn);
			exit;
		}
	}

	public function event_check_in()
	{
		if ($this->input->is_ajax_request()) {
			$s_event_id = $this->input->post('event_id');
			$mba_event_field = $this->Evt->get_event_field(['ev.event_id' => $s_event_id]);
			if (!$mba_event_field) {
				$a_return = ['code' => 9, 'message' => 'error retrieve event data!'];
			}
			else {
				$o_event_data = $mba_event_field[0];
				foreach ($mba_event_field as $o_field) {
					if (($o_field->field_name == 'graduate_year') AND ($o_field->event_type == 'pmb')) {
						if ($this->input->post('booking_grade') == 'graduated') {
							$this->form_validation->set_rules('booking_'.$o_field->field_name, $o_field->field_title, 'trim|required');
						}
					}
					else {
						$s_rules = 'trim|required';
						if (in_array($o_field->field_name, ['email'])) {
							$s_rules .= '|valid_email';
						}
						else if (in_array($o_field->field_name, ['seat'])) {
							$s_rules .= '|numeric';
						}
						$this->form_validation->set_rules('booking_'.$o_field->field_name, $o_field->field_title, $s_rules);
					}
				}

				if ($this->form_validation->run()) {
					$s_booking_id = $this->uuid->v4();
					$a_booking_data = [
						'booking_id' => $s_booking_id
					];

					foreach ($mba_event_field as $o_field) {
						$a_booking_data['booking_'.$o_field->field_name] = set_value('booking_'.$o_field->field_name);
					}

					if (!empty($this->input->post('booking_id'))) {
						$a_post_data = [
							'access_token' => 'PUBLICAPI',
							'data' => $a_booking_data,
							'clause' => ['booking_id' => $this->input->post('booking_id')]
						];

						if ($_SERVER['REMOTE_ADDR'] != '202.93.225.254') {
							$this->libapi->post_data('https://pmb.iuli.ac.id/api/event/do_check_in', json_encode($a_post_data));
							$submit_data = $this->Evt->update_booking_data($a_booking_data, $a_post_data['clause']);
						}
						else {
							$submit_data = true;
						}
	
						if ($submit_data) {
							$a_return = ['code' => 0, 'message' => 'Success'];
						}
						else {
							$a_return = ['code' => 1, 'message' => 'Failed processing your data!'];
						}
					}
					else {
						$a_booking_data['event_id'] = $s_event_id;
						$a_booking_data['date_added'] = date('Y-m-d H:i:s');
						$a_post_data = [
							'access_token' => 'PUBLICAPI',
							'data' => $a_booking_data
						];

						// if ($_SERVER['REMOTE_ADDR'] != '202.93.225.254') {
							$this->libapi->post_data('https://pmb.iuli.ac.id/api/event/register_check_in', json_encode($a_post_data));
						// }

						if(!$this->Evt->check_email_bookings(set_value('booking_email'), $s_event_id)){
							if(!$this->Evt->check_phone_bookings(set_value('booking_phone'), $s_event_id)){
								$submit = true;
								// if ($_SERVER['REMOTE_ADDR'] != '202.93.225.254') {
									$submit = $this->Evt->register_event($a_booking_data);
								// }

								$a_email_list = $this->config->item('email');
								if ($submit) {
									$s_user_name = strtoupper(set_value('booking_email'));
									$this->load->library('email');
									$a_bcc = array_merge($a_email_list['it']['members']);
									$config['mailtype'] = 'html';
									$s_uri = 'https://www.iuli.ac.id/';
	
									if ($o_event_data->event_submit_test_automaticly == 'true') {
										$s_new_token = md5($this->uuid->v4().time());
										$s_new_token = substr($s_new_token, 0, 16);
										$a_new_token = str_split($s_new_token);
										$s_registration_link = 'https://portal.iuli.ac.id/exam/join_english_test';
										$s_uri = $s_registration_link;
										$s_links = '<a href="'.$s_registration_link.'">click this</a>';
										$s_token = '';
										for ($i=0; $i < count($a_new_token); $i++) { 
											if (($i > 0) AND ($i % 4 == 0)) {
												$s_token .= '-';
											}
											$s_token .= $a_new_token[$i];
										}

										$this->load->library('WaAPI');
										$s_tonumber = $this->waapi->initialize_contact(set_value('booking_phone'));
										$s_toname = set_value('booking_name');
										$result = $this->waapi->execute_post('broadcasts/whatsapp/direct', [
											'to_number' => $s_tonumber,
											'to_name' => $s_toname,
											'message_template_id' => '1c4d46e4-5ea3-4e7a-960b-bbabfede7fdf',
											'channel_integration_id' => 'cc073782-4440-4718-a874-f78769a4a67d',
											'language' => [
												'code' => 'en'
											],
											'parameters' => [
												'body' => [
													[
														'key' => '1',
														'value' => 'customer_name',
														'value_text' => $s_toname,
													],
													[
														'key' => '2',
														'value' => 'user_token',
														'value_text' => $s_token,
													],
												]
											]
										]);

										$send_whatsapp = modules::run('whatsapp/Api', $result);
										// $a_return = $this->_send_broadcast_direct($result, $mba_candidate_data);
										

										$this->email->initialize($config);
										$this->email->from($a_email_list['admission']['main'], '[IULI] International University Liaison Indonesia');
	
										$a_uri = $this->config->item('public_link');
										$s_uri = $a_uri['online_exam_login'];
										$this->add_online_test($s_booking_id, $s_token);

										$s_message = <<<TEXT
<p>Hello {$s_user_name},<p>
<p>Thank you for your participation.</p>
<p>Your token for English Comprehension Test: <strong>{$s_token}</strong></p>
<p>Please {$s_links} for login test material or visit this link:</p>
<p>{$s_registration_link}</p>
<p><br></p>
<p>Good Luck!</p>
TEXT;
	
										$this->email->to([set_value('email')]);
										$this->email->bcc($a_bcc);
										$this->email->subject('[IULI] Registration Event');
										$this->email->message($s_message);
	
										if (!$this->email->send()) {
											log_message('error', $this->email->print_debugger().__FILE__.' '.__LINE__);
		
											$this->email->from($a_email_list['it']['main'], '[IULI] ERROR Log');
											$this->email->to($a_email_list['it']['members']);
											$this->email->subject('Failed send email registration event');
											$this->email->message('message: '.$this->email->print_debugger());
											$this->email->send();
										}
									}
	
									$a_return = ['code' => 0, 'message' => 'Successfully register!', 'uri' => $s_uri];
									// $this->send_notification_telegram($s_user_name.' has registration event!');
								}
								else {
									$a_return = ['code' => 1, 'message' => 'Failed processing your data!'];
								}
							}
							else{
								$a_return = ['code' => 1, 'message' => 'Phone number is in use'];
							}
						}
						else{
							$a_return = ['code' => 1, 'message' => 'Email is in use'];
						}
					}
				}
				else{
					$a_return = ['code' => 1, 'message' => validation_errors('<span>', '</span><br>')];
				}
			}

			print json_encode($a_return);
		}
	}
	
	public function do_check_in()
	{
		if($this->input->is_ajax_request()){
			$this->form_validation->set_rules('name', 'Booking Name', 'trim|required|alpha_numeric_spaces');
			$this->form_validation->set_rules('email', 'Booking Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('phone', 'Booking Phone', 'trim|required|numeric');
			// $this->form_validation->set_rules('booking_seat', 'Booking Seat', 'trim|required|numeric');
			// $this->form_validation->set_rules('image_file', 'Photo', 'trim', [
			// 	'required' => 'Must take photo'
			// ]);
			$this->form_validation->set_rules('reference', 'Reference', 'trim|required');
			
			$s_event_id = $this->input->post('event_id');
			$mba_event_data =$this->General->get_where('dt_event', ['event_id' => $s_event_id]);

			if (($mba_event_data) AND ($mba_event_data[0]->event_type == 'pmb')) {
				$this->form_validation->set_rules('event_personal_grade', 'Grade / Class', 'trim|required');
				if ((!empty($this->input->post('event_personal_grade'))) AND ($this->input->post('event_personal_grade') == 'graduated')) {
					$this->form_validation->set_rules('event_personal_graduated_year', 'Graduate Year', 'trim|required');
				}
			}
			if (empty($s_event_id)) {
				$a_return = ['code' => 1, 'message' => 'failed retrieve your data!'];
			}
			else if($this->form_validation->run()){
				$s_booking_id = $this->uuid->v4();
				$a_booking_data = [
					'booking_id' => $s_booking_id,
					'booking_name' => strtoupper(set_value('name')),
					'booking_email' => set_value('email'),
					'booking_phone' => set_value('phone'),
					'booking_seat' => 1,
					// 'booking_photo' => set_value('image_file'),
					'booking_participation' => 'present',
					'booking_origin' => set_value('reference'),
					'booking_grade' => (!empty(set_value('event_personal_grade'))) ? set_value('event_personal_grade') : NULL,
					'booking_graduate_year' => (set_value('event_personal_grade') != 'graduated') ? NULL : set_value('event_personal_graduated_year'),
					'date_added' => date('Y-m-d H:i:s')
				];
				
				if($this->input->post('booking_id') != ''){
					$a_post_data = [
						'access_token' => 'PUBLICAPI',
						'data' => $a_booking_data,
						'clause' => ['booking_id' => $this->input->post('booking_id')]
					];

					if ($_SERVER['REMOTE_ADDR'] != '202.93.225.254') {
						$this->libapi->post_data('https://pmb.iuli.ac.id/api/event/do_check_in', json_encode($a_post_data));
						$submit_data = $this->Evt->update_booking_data($this->a_api_data['data'], $this->a_api_data['clause']);
					}
					else {
						$submit_data = true;
					}

					if ($submit_data) {
						$a_return = ['code' => 0, 'message' => 'Success'];
					}
					else {
						$a_return = ['code' => 1, 'message' => 'Failed processing your data!'];
					}
				}
				else{
					$a_booking_data['event_id'] = $this->input->post('event_id');
					$a_booking_data['date_added'] = date('Y-m-d H:i:s', time());
					$a_post_data = [
						'access_token' => 'PUBLICAPI',
						'data' => $a_booking_data
					];

					if ($_SERVER['REMOTE_ADDR'] != '202.93.225.254') {
						$this->libapi->post_data('https://pmb.iuli.ac.id/api/event/register_check_in', json_encode($a_post_data));
					}

					if(!$this->Evt->check_email_bookings(set_value('email'), $this->input->post('event_id'))){
						if(!$this->Evt->check_phone_bookings(set_value('phone'), $this->input->post('event_id'))){
							$submit = true;
							if ($_SERVER['REMOTE_ADDR'] != '202.93.225.254') {
								$submit = $this->Evt->register_event($a_booking_data);
							}
							$a_email_list = $this->config->item('email');
							if ($submit) {
								$s_user_name = strtoupper(set_value('name'));

								$s_new_token = md5($this->uuid->v4().time());
								$s_new_token = substr($s_new_token, 0, 16);
								$a_new_token = str_split($s_new_token);
								$s_registration_link = 'https://portal.iuli.ac.id/exam/auth_entrance_test';
								$s_links = '<a href="'.$s_registration_link.'">click this</a>';
								$s_token = '';
								for ($i=0; $i < count($a_new_token); $i++) { 
									if (($i > 0) AND ($i % 4 == 0)) {
										$s_token .= '-';
									}
									$s_token .= $a_new_token[$i];
								}

								$this->load->library('email');
								$a_bcc = array_merge($a_email_list['it']['members']);
								$config['mailtype'] = 'html';
								
								if (($mba_event_data) AND ($mba_event_data[0]->event_type == 'pmb')) {
									$this->email->initialize($config);
									$this->email->from($a_email_list['admission']['main'], '[IULI] International University Liaison Indonesia');

									$a_uri = $this->config->item('public_link');
									$s_uri = $a_uri['online_exam_login'];
									$this->add_online_test($s_booking_id, $s_token);
									
									// $config = $this->config->item('mail_config');
									
									$s_message = <<<TEXT
<p>Hello {$s_user_name},<p>
<p>Thank you for your participation.</p>
<p>Your token for English Comprehension Test: <strong>{$s_token}</strong></p>
<p>Please {$s_links} for login test material or visit this link:</p>
<p>{$s_registration_link}</p>
<p><br></p>
<p>Good Luck!</p>
TEXT;

									$this->email->to([set_value('email')]);
									$this->email->bcc($a_bcc);
									$this->email->subject('[IULI] Registration Event');
									$this->email->message($s_message);

									if (!$this->email->send()) {
										log_message('error', $this->email->print_debugger().__FILE__.' '.__LINE__);
	
										$this->email->from($a_email_list['it']['main'], '[IULI] ERROR Log');
										$this->email->to($a_email_list['it']['members']);
										$this->email->subject('Failed send email registration event');
										$this->email->message('message: '.$this->email->print_debugger());
										$this->email->send();
									}
								}
								else {
									$s_message = <<<TEXT
<p>Hello {$s_user_name},<p>
<p>Thank you for your participation.</p>
<p><br></p>
TEXT;
									$s_uri = 'https://www.iuli.ac.id/';
								}

								$a_return = ['code' => 0, 'message' => 'Successfully register!', 'uri' => $s_uri];
								$this->send_notification_telegram($s_user_name.' has registration event!');
							}
							else {
								$a_return = ['code' => 1, 'message' => 'Failed processing your data!'];
							}
						}
						else{
							$a_return = ['code' => 1, 'message' => 'Phone number is in use'];
						}
					}
					else{
						$a_return = ['code' => 1, 'message' => 'Email is in use'];
					}
				}
			}
			else{
				$a_return = ['code' => 1, 'message' => validation_errors('<span>', '</span><br>')];
			}
			
			print json_encode($a_return);
			exit;
		}
	}

	private function add_online_test($s_booking_id, $s_token)
	{
		$this->load->model('exam/Entrance_test_model', 'Etm');
		$s_exam_id = '8c9b0034-68ed-11ea-98d8-52540001273f';
		$mba_exam_question_list = $this->Etm->get_exam_question([
			'ep.exam_id' => $s_exam_id
		]);
		$s_exam_candidate_id = $this->uuid->v4();

		$a_exam_data = [
			'exam_candidate_id' => $s_exam_candidate_id,
			'student_id' => NULL,
			'booking_id' => $s_booking_id,
			'exam_id' => $s_exam_id,
			'token' => $s_token,
			'candidate_exam_status' => 'PENDING',
			'total_question' => ($mba_exam_question_list) ? count($mba_exam_question_list) : 0,
			'date_added' => date('Y-m-d H:i:s')
		];
		$this->Etm->save_candidate_exam($a_exam_data);
	}
	
	public function get_events()
	{
		if($this->input->is_ajax_request()){
			$a_clause = [
				'event_type != ' => 'form'
			];
			if (!empty($this->input->post('event_type'))) {
				$a_clause['event_type'] = $this->input->post('event_type');
			}
			$result = $this->Evt->get_event($a_clause);
			// $result = $this->libapi->post_data('https://pmb.iuli.ac.id/api/event/get_event_list', json_encode([
			// 	'access_token' => 'PUBLICAPI'
			// ]));
			print json_encode(['code' => 0, 'data' => $result]);
			exit;
		}
	}
	
	public function get_bookings()
	{
		if($this->input->is_ajax_request()){
			$result = $this->Evt->get_event_bookings($this->input->post('event_id'));
			if ($result) {
				foreach ($result as $o_result) {
					$mba_student_data = $this->Stm->get_student_filtered([
						'dpd.personal_data_email' => strtolower($o_result->booking_email)
					]);
					
					$o_result->is_student = ($mba_student_data) ? 'true' : 'false';
				}
			}
			
			// $result = $this->libapi->post_data('https://pmb.iuli.ac.id/api/event/get_bookings', json_encode([
			// 	'access_token' => 'PUBLICAPI',
			// 	'data' => [
			// 		'event_id' => $this->input->post('event_id')
			// 	]
			// ]));
			print json_encode(['code' => 0, 'data' => $result]);
			exit;
		}
	}
}