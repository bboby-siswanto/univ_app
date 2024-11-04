<?php
class Jobreport extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Calendar_model', 'Crm');
    }

    public function submit_eventcalendar()
    {
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->session->userdata('user');
            $this->form_validation->set_rules('calendar_event_title', 'Event Title', 'trim|required');
            $this->form_validation->set_rules('calendar_event_category', 'Event Category', 'trim|required');
            $this->form_validation->set_rules('calendar_event_start_date', 'Event Start Date', 'trim|required');
            $this->form_validation->set_rules('calendar_event_end_date', 'Event End Date', 'trim|required');
            $this->form_validation->set_rules('calendar_event_type', 'Event Type', 'trim|required');
            $this->form_validation->set_rules('body_calendar_event_desc', 'Event Description', 'trim|required');

            if ($this->form_validation->run()) {
                // print('<pre>');var_dump(set_value('body_calendar_event_desc'));exit;
                $s_report_id = (($this->input->post('calendar_event_report_id') !== null) AND (!empty($this->input->post('calendar_event_report_id')))) ? $this->input->post('calendar_event_report_id') : $this->uuid->v4();
                $a_report_data = [
                    'report_id' => $s_report_id,
                    'personal_data_id' => $this->session->userdata('user'),
                    'report_title' => set_value('calendar_event_title'),
                    'report_datestart' => set_value('calendar_event_start_date'),
                    'report_dateend' => set_value('calendar_event_end_date'),
                    'report_allday' => (($this->input->post('calendar_event_allday') !== null) AND (!empty($this->input->post('calendar_event_allday')))) ? 'true' : 'false',
                    'report_desc' => set_value('body_calendar_event_desc'),
                    'report_category' => set_value('calendar_event_category'),
                    'report_type' => set_value('calendar_event_type'),
                    'date_added' => date('Y-m-d H:i:s')
                ];

                if (($this->input->post('calendar_event_report_id') !== null) AND (!empty($this->input->post('calendar_event_report_id')))) {
                    $submit_event = $this->Crm->submit_event($a_report_data, [
                        'report_id' => $this->input->post('calendar_event_report_id')
                    ]);
                }
                else {
                    $submit_event = $this->Crm->submit_event($a_report_data);
                }

                if ($submit_event) {
                    // if (($this->input->post('calendar_event_assignor') !== null) AND (!empty($this->input->post('calendar_event_assignor')))) {
                    //     $this->Crm->remove_assignor([
                    //         'report_id' => $s_report_id,
                    //         'assign_as' => 'assignor'
                    //     ]);
    
                    //     $a_job_assign_data= [
                    //         'assign_id' => $this->uuid->v4(),
                    //         'report_id' => $s_report_id,
                    //         'personal_data_id' => (!empty($this->input->post('calendar_event_assignor_id'))) ? $this->input->post('calendar_event_assignor_id') : NULL,
                    //         'personal_data_name' => $this->input->post('calendar_event_assignor'),
                    //         'assign_as' => 'assignor',
                    //         'date_added' => date('Y-m-d H:i:s')
                    //     ];
                    //     $this->Crm->submit_job_assign($a_job_assign_data);
                    // }

                    $a_return = ['code' => 0, 'message' => 'Success'];
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'Error submiting event data!'];
                }
            }
            else {
                $a_return = array('code' => 1, 'message' => validation_errors('<span>','</span><br>'), 'fields' => $this->validation_errors());
            }
            // print('<pre>');
            // var_dump($this->input->post());
            print json_encode($a_return);
        }
    }

    public function get_event()
    {
        $startparam = (isset($_GET['start'])) ? $_GET['start'] : false;
        $endparam = (isset($_GET['end'])) ? $_GET['end'] : false;

        $a_clause = false;
        if (($startparam) AND ($endparam)) {
            $a_clause = [
                'date(jr.report_datestart) >= ' => $startparam,
                'date(jr.report_dateend) <= ' => $endparam
            ];
        }
        $mba_eventlist = $this->Crm->get_event($a_clause);
        $a_eventdata = [];
        if ($mba_eventlist) {
            foreach ($mba_eventlist as $o_event) {
                $b_allow_show = false;
                if ($o_event->report_type == 'personal') {
                    if ($o_event->personal_data_id == $this->session->userdata('user')) {
                        $b_allow_show = true;
                    }
                }else if ($o_event->report_type == 'public') {
                    $b_allow_show = true;
                }

                if ($b_allow_show) {
                    $a_eventlist = [
                        'id' => $o_event->report_id,
                        'title' => $o_event->report_title,
                        'start' => $o_event->report_datestart,
                        'end' => $o_event->report_dateend,
                        'description' => htmlspecialchars_decode($o_event->report_desc),
                        'category' => $o_event->report_category,
                        'type' => $o_event->report_type,
                        'allDay' => (date('Y-m-d', strtotime($o_event->report_datestart)) == date('Y-m-d', strtotime($o_event->report_dateend))) ? true : false,
                        'color' => $this->_get_event_color($o_event->report_category),
                        'className' => 'text-white',
                        // 'url' => base_url()
                    ];
    
                    array_push($a_eventdata, $a_eventlist);
                }
            }
        }

        // print json_encode(['code' => 0, 'data' => $a_data]);
        print json_encode($a_eventdata);
    }

    public function default()
    {
        $this->a_page_data['body'] = $this->load->view('hris/working_report/default', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    private function _get_event_color($s_category_event)
    {
        if ($s_category_event == 'event') {
            return 'purple';
        }
        else if ($s_category_event == 'birthday') {
            return 'blue';
        }
        else if ($s_category_event == 'holiday') {
            return 'red';
        }
        else if ($s_category_event == 'academic calendar') {
            return 'orange';
        }
        else {
            return 'green';
        }
    }
}
