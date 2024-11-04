<?php
class Api extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('hris/Hris_model', 'Hrm');
    }

    public function test_join()
    {
        $mba_data = $this->Hrm->get_employee_hid();
        print('<pre>');var_dump($mba_data);exit;
    }

    public function get_attendance_employee()
    {
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->input->post('personal_data_id');
            $s_date_selected = $this->input->post('date_selected');
            $s_date_selected = (empty($s_date_selected)) ? date('Y-m-d') : $s_date_selected;

            $s_month = date('m', strtotime($s_date_selected));
            $s_year = date('Y', strtotime($s_date_selected));
            $s_days = cal_days_in_month(CAL_GREGORIAN, $s_month, $s_year);

            $s_hid_data = $this->Hrm->get_where_hr('dt_hid', ['personal_data_id' => $s_personal_data_id]);
            $a_data = false;
            if ($s_hid_data) {
                $a_data = [];
                for ($i=1; $i <= $s_days; $i++) {
                    $s_date = date('Y-m-d', strtotime($i.'-'.$s_month.'-'.$s_year));
                    $mba_att = $this->Hrm->get_log_absence(['hid.personal_data_id' => $s_personal_data_id, 'log.log_date' => $s_date]);
                    if ($mba_att) {
                        array_push($a_data, [
                            'date' => $i,
                            'day' => date('D', strtotime($i.'-'.$s_month.'-'.$s_year)),
                            'hid_key' => $mba_att[0]->hid_key,
                            'hid_id' => $mba_att[0]->hid_id,
                            'log_date' => $mba_att[0]->log_date,
                            'log_checkin' => $mba_att[0]->log_checkin,
                            'log_checkout' => $mba_att[0]->log_checkout,
                            'log_late_in' => $mba_att[0]->log_late_in,
                            'log_early_out' => $mba_att[0]->log_early_out,
                            'log_working_hour' => $mba_att[0]->log_working_hour,
                            'hid_status' => $s_hid_data[0]->hid_status,
                            'hid_should_checkin' => $s_hid_data[0]->hid_should_checkin,
                            'hid_should_checkout' => $s_hid_data[0]->hid_should_checkout,
                            'hid_standar_working_hour' => $s_hid_data[0]->hid_standar_working_hour,
                        ]);
                    }
                    else {
                        array_push($a_data, [
                            'date' => $i,
                            'day' => date('D', strtotime($i.'-'.$s_month.'-'.$s_year)),
                            'hid_key' => '',
                            'hid_id' => '',
                            'log_date' => '',
                            'log_checkin' => '',
                            'log_checkout' => '',
                            'log_late_in' => '',
                            'log_early_out' => '',
                            'log_working_hour' => '',
                            'hid_status' => '',
                            'hid_should_checkin' => '',
                            'hid_should_checkout' => '',
                            'hid_standar_working_hour' => '',
                        ]);
                    }
                }
            }
            
            print json_encode(['data' => $a_data, 'date_selected' => $s_month]);exit;
            // print($s_date_selected);exit;
        }
    }

    public function post_attendance()
    {
        if ($this->input->is_ajax_request()) {
            $current = date('Y-m-d H:i:s');
            $s_hidkey = $this->input->post('hidkey');
            $mbo_user_data = $this->Hrm->get_hid_data($s_hidkey);
            $mbs_hid_id = false;
            
            if ($mbo_user_data) {
                $mbs_hid_id = $mbo_user_data->hid_id;
                $s_type = false;
                $s_nip = false;
                $mba_user_details = $this->General->get_where('dt_employee', ['personal_data_id' => $mbo_user_data->personal_data_id]);
                
                if ($mba_user_details) {
                    $s_type = 'employee';
                    $s_nip = $mba_user_details[0]->employee_id_number;
                }
                else {
                    $mba_user_details = $this->General->get_where('dt_student', ['personal_data_id' => $mbo_user_data->personal_data_id]);
                    if ($mba_user_details) {
                        $s_type = 'student';
                        $s_nip = $mba_user_details[0]->student_number;
                    }
                }

                if ($mba_user_details) {
                    $mba_user_photo = $this->General->get_where('dt_personal_data_document', [
                        'personal_data_id' => $mbo_user_data->personal_data_id,
                        'document_id' => '0bde3152-5442-467a-b080-3bb0088f6bac'
                    ]);

                    $a_return = [
                        'code' => 0,
                        'hid' => $mbo_user_data->hid_key,
                        'nip' => $s_nip,
                        'type' => $s_type,
                        'time' => $current,
                        'name' => $mbo_user_data->personal_data_name,
                        'photo' => ($mba_user_photo) ? base_url().'file_manager/view_public/0bde3152-5442-467a-b080-3bb0088f6bac/'.$mbo_user_data->personal_data_id : ''
                    ];
                }
                else {
                    $this->send_notification_telegram("User HID $s_hidkey not found!!!");
                    $a_return = ['code' => 1, 'message' => 'Your Badge is not registered. Please contact IULI HR Dept.'];
                }
            }
            else {
                $this->send_notification_telegram("HID $s_hidkey not registered!!!");
                $a_return = ['code' => 1, 'message' => 'Your Badge is not registered. Please contact IULI HR Dept.'];
            }

            $a_data = [
                'log_id' => $this->uuid->v4(),
                'hid_id' => ($mbo_user_data) ? $mbs_hid_id : NULL,
                'hid_key' => $s_hidkey,
                'date_added' => date('Y-m-d H:i:s')
            ];
            
            $submit_attendance = $this->_submit_attendance($a_data, $mbo_user_data);
            $a_return = array_merge($a_return, $submit_attendance);
            print json_encode($a_return);
        }
    }

    private function _submit_attendance($a_data, $mbo_hid_data = false)
    {
        $current = date('Y-m-d H:i:s');
        $time_current = new DateTime($current);
		$today = date('Y-m-d');
        $should_checkin = ($mbo_hid_data) ? $mbo_hid_data->hid_should_checkin : '08:00:00';
        $should_checkout = ($mbo_hid_data) ? $mbo_hid_data->hid_should_checkout : '17:00:00';

        $mba_absence_data = $this->Hrm->get_log_absence([
            'hid_key' => $a_data['hid_key'],
            'log_date' => $today
        ]);
        $att_type = 'IN';
        
        if ($mba_absence_data) {
            $o_absence_data = $mba_absence_data[0];
            $s_log_id = $o_absence_data->log_id;
            $checkin = date('Y-m-d H:i:s', strtotime($today.' '.$o_absence_data->log_checkin));
            $time_checkin = new DateTime($checkin);
            // $time_should_checkout = date('Y-m-d H:i:s', strtotime($today.' '.$should_checkout));
            $time_should_checkout = new DateTime($today.' '.$should_checkout);

            $working_time = $time_current->diff($time_checkin);
            $working_time = $working_time->format("%H:%I:%S");
            $early_checkout = '00:00:00';
			if($time_current < $time_should_checkout){
				$early_checkout = $time_should_checkout->diff($time_current);
				$early_checkout = $early_checkout->format("%H:%I:%S");
			}

            $a_data['log_checkout'] = date('H:i:s', strtotime($current));
            $a_data['log_early_out'] = date('H:i:s', strtotime($early_checkout));
            $a_data['log_working_hour'] = date('H:i:s', strtotime($working_time));

            $submit_data = $this->Hrm->submit_attendace($a_data, [
                'log_id' => $s_log_id
            ]);
            $att_type = 'OUT';
        }
        else {
            // $time_should_checkin = date('Y-m-d H:i:s', strtotime($today.' '.$should_checkin));
            $time_should_checkin = new DateTime($today.' '.$should_checkin);
            $latein = '00:00:00';
			if($time_current > $time_should_checkin){
				$latein = $time_should_checkin->diff($time_current);
                // $latein = date_diff( $time_should_checkin, $time_current );
				$latein = $latein->format("%H:%I:%S");
			}

            $a_data['log_date'] = $today;
            $a_data['log_checkin'] = date('H:i:s', strtotime($current));
            $a_data['log_late_in'] = date('H:i:s', strtotime($latein));

            $submit_data = $this->Hrm->submit_attendace($a_data);
        }

        if (!$submit_data) {
            $this->send_notification_telegram("ERROR submit attendace!!");
        }

        return ['att_type' => $att_type, 'time_current' => date('H:i', strtotime($current))];
    }

    public function get_hid()
    {
        if ($this->input->is_ajax_request()) {
            $s_hidkey = $this->input->post('hidkey');
            $mbo_user_data = $this->Hrm->get_hid_data($s_hidkey);
            if ($mbo_user_data) {
                $s_type = false;
                $s_nip = false;
                $mba_user_details = $this->General->get_where('dt_employee', ['personal_data_id' => $mbo_user_data->personal_data_id]);
                
                if ($mba_user_details) {
                    $s_type = 'employee';
                    $s_nip = $mba_user_details[0]->employee_id_number;
                }
                else {
                    $mba_user_details = $this->General->get_where('dt_student', ['personal_data_id' => $mbo_user_data->personal_data_id]);
                    if ($mba_user_details) {
                        $s_type = 'student';
                        $s_nip = $mba_user_details[0]->student_number;
                    }
                }

                if ($mba_user_details) {
                    $mba_user_photo = $this->General->get_where('dt_personal_data_document', [
                        'personal_data_id' => $mbo_user_data->personal_data_id,
                        'document_id' => '0bde3152-5442-467a-b080-3bb0088f6bac'
                    ]);
                    $a_return = [
                        'code' => 0,
                        'hid' => $mbo_user_data->hid_key,
                        'nip' => $s_nip,
                        'type' => $s_type,
                        'name' => $mbo_user_data->personal_data_name,
                        'photo' => ($mba_user_photo) ? base_url().'file_manager/view_public/0bde3152-5442-467a-b080-3bb0088f6bac/'.$mbo_user_data->personal_data_id : ''
                    ];
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'Your Badge is not registered. Please contact IULI HR Dept.'];
                }
            }
            else {
                $a_return = ['code' => 1, 'message' => 'Your Badge is not registered. Please contact IULI HR Dept.'];
            }

            print json_encode($a_return);
        }
    }
}
