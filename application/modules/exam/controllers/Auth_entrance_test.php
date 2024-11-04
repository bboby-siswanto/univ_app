<?php
class Auth_entrance_test extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Entrance_test_model', 'Etm');
    }

    public function sesi_exam()
    {
        print('<pre>');var_dump($this->session->userdata());
    }

    public function index()
    {
        $this->load->view('auth_entrance_test');
        if ($this->session->userdata('token')) {
            redirect($this->session->userdata('redirect_uri'));
        }
    }

    function authentication_token() {
        
    }

    public function check_token()
    {
        if ($this->input->is_ajax_request()) {
            // var_dump('a');
            $s_token = $this->input->post('token');
            $s_token = trim($s_token);
            $s_email = $this->input->post('email');
            $s_email = trim($s_email);
            $s_target = 'pmb';
            $mba_candidate_token = $this->Etm->get_candidate_exam(array('ec.token' => $s_token, 'pd.personal_data_email' => $s_email), 'pmb');

            if (!$mba_candidate_token) {
                $s_target = 'event';
                $mba_candidate_token = $this->Etm->get_candidate_exam(array('ec.token' => $s_token, 'eb.booking_email' => $s_email), 'event');
            }
            // var_dump($mba_candidate_token);exit;
            if ($mba_candidate_token) {
                $mba_candidate_token = $mba_candidate_token[0];
                // if ($mba_candidate_token->candidate_exam_status == 'FINISH') {
                //     $a_return = array('code' => 1, 'message' => 'You have already answered this online entrance test !');
                // }else if ($mba_candidate_token->candidate_exam_status == 'CANCEL') {
                //     $a_return = array('code' => 1, 'message' => 'your token is canceled!');
                // }else{
                    // if ($mba_candidate_token->personal_data_name == 'SISWANTO BUDI') {
                    //     $a_auth_session['environment'] = 'sandbox';
                    // }
                    $s_username = '';
                    if ($s_target == 'pmb') {
                        $a_auth_session['target'] = 'pmb';
                        $a_auth_session['personal_data_id'] = $mba_candidate_token->personal_data_id;
                        $a_auth_session['personal_data_name'] = $mba_candidate_token->personal_data_name;
                        $s_username = $mba_candidate_token->personal_data_name;
                        $a_auth_session['user'] = $mba_candidate_token->personal_data_id;
                    }
                    else {
                        $a_auth_session['target'] = 'event';
                        $a_auth_session['booking_id'] = $mba_candidate_token->booking_id;
                        $a_auth_session['user_name'] = $mba_candidate_token->booking_name;
                        $s_username = $mba_candidate_token->booking_name;
                        $a_auth_session['user'] = $mba_candidate_token->booking_id;
                    }

                    $a_auth_session['token'] = $s_token;
                    $a_auth_session['redirect_uri'] = 'exam/entrance_test/dasboard_exam';
                    $this->session->set_userdata($a_auth_session);
                    // if ($s_email != 'budi.siswanto1450@gmail.com') {
                    //     $this->notification_email($s_username, $s_email);
                    // }
                    $a_return = array('code' => 0, 'message' => 'success', 'redirect_uri' => base_url().'exam/entrance_test/online_test/'.$s_token);
                // }
            }else{
                $a_return = array('code' => 1, 'message' => 'Wrong token or Email!');
            }

            print json_encode($a_return);
        }
    }

    public function notification_email($s_participant_name, $s_participant_email)
    {
        $s_text = <<<TEXT
Dear team,
{$s_participant_name} ({$s_participant_email}) has join online english test
TEXT;
        $this->email->from('employee@company.ac.id', 'IULI-Portal');
		$this->email->to('employee@company.ac.id');
		$this->email->subject('[IULI-Online Test] New people join online English test');
		$this->email->message($s_text);
		$this->email->send();
    }
}
