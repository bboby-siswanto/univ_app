<?php
class Kpu extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Kpu_model', 'Km');
    }

    public function index()
    {
        if (!$this->get_access()) {
            show_404();exit;
        }

        $mba_kpu_active = $this->General->get_where('vote_period', ['period_status' => 'active']);
        
        $a_paslon = false;
        $mba_paslon = false;
        $paslon_list = 'Kotak Kosong';
        if ($mba_kpu_active) {
            $mba_paslon = $this->Km->get_paslon([
                'vp.period_id' => $mba_kpu_active[0]->period_id
            ]);

            if ($mba_paslon) {
                $a_paslon = [];
                foreach ($mba_paslon as $o_paslon) {
                    $s_chairman_paslon = (!is_null($o_paslon->chairman_personal_name)) ? ucwords(strtolower($o_paslon->chairman_personal_name)) : 'Kotak Kosong';
                    $s_vice_chairman_paslon = (!is_null($o_paslon->vice_chairman_personal_name)) ? ucwords(strtolower($o_paslon->vice_chairman_personal_name)) : 'Kotak Kosong';
                    $s_paslon = $s_chairman_paslon.' & '.$s_vice_chairman_paslon;
                    $paslon = $s_paslon;
                    array_push($a_paslon, $paslon);
                }
            }
        }
        
        $this->a_page_data['paslon'] = $a_paslon;
        $this->a_page_data['paslon_list'] = $mba_paslon;
        $this->a_page_data['vote_period'] = $mba_kpu_active;
        $this->a_page_data['body'] = $this->load->view('apps/kpu/views', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function vote_view()
    {
        $mba_kpu_active = $this->General->get_where('vote_period', ['period_status' => 'active']);
        if ($mba_kpu_active) {
            $o_vote_period = $mba_kpu_active[0];
            $mba_paslon = $this->Km->get_paslon([
                'vp.period_id' => $o_vote_period->period_id
            ]);
            $this->a_page_data['vote_period'] = $o_vote_period;
            $this->a_page_data['paslon_list'] = $mba_paslon;
            return $this->load->view('apps/kpu/voting', $this->a_page_data, true);
        }
        return '';
    }

    public function show_modal_voting()
    {
        if (($this->session->has_userdata('show_vote_modal')) AND ($this->session->userdata('show_vote_modal'))) {
            $mba_kpu_active = $this->General->get_where('vote_period', [
                'period_status' => 'active',
                'period_voting_start <= ' => date('Y-m-d H:i:s'),
                'period_voting_end >= ' => date('Y-m-d H:i:s')
            ]);
            
            if ($mba_kpu_active) {
                return $this->load->view('kpu/voting_modal', $this->a_page_data, true);
            }
        }

        return '';
    }

    public function view_result()
    {
        if (!$this->get_access()) {
            show_404();exit;
        }
        
        $mba_kpu_active = $this->General->get_where('vote_period', ['period_status' => 'active']);
        if ($mba_kpu_active) {
            $o_vote_period = $mba_kpu_active[0];
            $mba_paslon = $this->Km->get_paslon([
                'vp.period_id' => $o_vote_period->period_id
            ]);
            $this->a_page_data['vote_period'] = $o_vote_period;
            $this->a_page_data['paslon_list'] = $mba_paslon;
            $this->a_page_data['body'] = $this->load->view('apps/kpu/result_vote', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
        else {
            show_404();
        }
    }

    public function view_pict($s_period_id = false, $s_pic_name = false)
    {
        $s_file_path = APPPATH.'uploads/bem/kpu/'.$s_period_id.'/'.$s_pic_name;
        if ((!$s_period_id) OR (!$s_pic_name)) {
            header("Content-Type: image");
            $s_file_path = APPPATH.'uploads/img/silhouette.png';
        }
        readfile( $s_file_path );
        exit;
    }

    public function paslon_form()
    {
        $a_student_clause = false;
        if ($this->session->has_userdata('academic_year_id_active')) {
            $a_student_clause = ['st.academic_year_id <= ' => $this->session->userdata('academic_year_id_active')];
        }
        
        $mba_kpu_active = $this->General->get_where('vote_period', ['period_status' => 'active']);
        $mba_student_active = $this->Km->get_student($a_student_clause);
        $this->a_page_data['vote_period'] = $mba_kpu_active;
        $this->a_page_data['student_list'] = $mba_student_active;
        $form = $this->load->view('kpu/paslon_edit', $this->a_page_data, true);
        return $form;
    }

    public function paslon_view($s_paslon_id = false)
    {
        if ($s_paslon_id) {
            $mba_paslon_data = $this->get_paslon($s_paslon_id);
            if ($mba_paslon_data) {
                $this->a_page_data['paste'] = true;
                $this->a_page_data['nomor_urut_paslon'] = $mba_paslon_data->nomor_urut;
                $this->a_page_data['nama_ketua_paslon'] = (!is_null($mba_paslon_data->chairman_personal_name)) ? $mba_paslon_data->chairman_personal_name : 'Kotak Kosong';
                $this->a_page_data['nama_wakil_paslon'] = (!is_null($mba_paslon_data->vice_chairman_personal_name)) ? $mba_paslon_data->vice_chairman_personal_name : 'Kotak Kosong';
                $this->a_page_data['vision_paslon'] = (!is_null($mba_paslon_data->vision)) ? $mba_paslon_data->vision : 'N/A';
                $this->a_page_data['mission_paslon'] = (!is_null($mba_paslon_data->mision)) ? $mba_paslon_data->mision : 'N/A';
                $this->a_page_data['img_ketua_paslon'] = base_url().'apps/kpu/view_pict/'.$mba_paslon_data->period_id.'/'.$mba_paslon_data->paslon_chairman_pict;
                $this->a_page_data['img_wakil_paslon'] = base_url().'apps/kpu/view_pict/'.$mba_paslon_data->period_id.'/'.$mba_paslon_data->paslon_vice_chairman_pict;
            }
        }
        return $this->load->view('kpu/paslon_view', $this->a_page_data, true);
    }

    // public function paslon_view($s_paslon_id = '')
    // {
    //     if ($this->input->is_ajax_request()) {
    //         $s_paslon_id = $this->input->post('paslon_id');
    //     }

    //     if ($s_paslon_id == '') {
    //         $s_html = '';
    //     }
    //     else {
    //         $mba_paslon_data = $this->Km->get_paslon(['vp.paslon_id' => ]);
    //     }
    // }

    public function get_result($s_period_id = false)
    {
        if ($this->input->is_ajax_request()) {
            $s_period_id = $this->input->post('period_id');
        }

        $mba_paslon_data = $this->General->get_where('vote_paslon', [
            'period_id' => $s_period_id
        ]);

        $a_return = ['data' => $mba_paslon_data];

        if ($this->input->is_ajax_request()) {
            print json_encode($a_return);
        }
        else {
            return $a_return;
        }
    }

    public function get_paslon($s_paslon_id = '')
    {
        if ($this->input->is_ajax_request()) {
            $s_paslon_id = $this->input->post('data_uniq');
        }

        $mba_paslon_data = false;
        if (!empty($s_paslon_id)) {
            $mba_paslon_data = $this->Km->get_paslon([
                'vp.paslon_id' => $s_paslon_id
            ]);

            if ($mba_paslon_data) {
                $mba_paslon_data = $mba_paslon_data[0];
            }
        }

        if ($this->input->is_ajax_request()) {
            print json_encode($mba_paslon_data);
        }
        else {
            return $mba_paslon_data;
        }
    }

    public function update_voting()
    {
        if ($this->input->is_ajax_request()) {
            $s_period_id = $this->input->post('dataid');
            $this->form_validation->set_rules('voting_period_name', 'Title', 'required|trim');
            $this->form_validation->set_rules('period_vote_start', 'Start Voting Date', 'required|trim');
            $this->form_validation->set_rules('period_vote_end', 'End Voting Date', 'required|trim');
            $this->form_validation->set_rules('period_vote_hour_start', 'Start Voting Time', 'required|trim');
            $this->form_validation->set_rules('period_vote_minute_start', 'Start Voting Time', 'required|trim');
            $this->form_validation->set_rules('period_vote_hour_end', 'End Voting Time', 'required|trim');
            $this->form_validation->set_rules('period_vote_minute_end', 'End Voting Time', 'required|trim');

            if ($this->form_validation->run()) {
                if (empty($s_period_id)) {
                    $a_return = ['code' => 2, 'message' => 'Failed retrieve period data!'];
                }
                else {
                    $a_period_data = [
                        'period_name' => set_value('voting_period_name'),
                        'period_voting_start' => set_value('period_vote_start').' '.set_value('period_vote_hour_start').':'.set_value('period_vote_minute_start').':00',
                        'period_voting_end' => set_value('period_vote_end').' '.set_value('period_vote_hour_end').':'.set_value('period_vote_minute_end').':00',
                        'date_added' => date('Y-m-d H:i:s')
                    ];

                    $submit_data = $this->Km->update_period($a_period_data, $s_period_id);
                    if ($submit_data) {
                        $a_return = ['code' => 0, 'message' => 'Success'];
                    }
                    else {
                        $a_return = ['code' => 1, 'message' => 'Nothing update'];
                    }
                }
            }
            else {
                $a_return = array('code' => 2, 'message' => $this->upload->display_errors('<span>', '</span><br>'));
            }

            print json_encode($a_return);
        }
        else {
            show_404();
        }
    }

    public function submit_paslon()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('ketua_student_id', 'Chairman', 'trim');
            $this->form_validation->set_rules('wakil_student_id', 'Vice Chairman', 'trim');
            $this->form_validation->set_rules('nomor_urut', 'Nomor Urut', 'required|trim');
            $this->form_validation->set_rules('vision', 'Vision', 'trim');
            $this->form_validation->set_rules('mission', 'Mission', 'trim');
            $s_paslon_id = $this->input->post('paslon_id');

            if ($this->form_validation->run()) {
                $s_ketua_filename = NULL; //paslon_chairman_pict
                $a_paslon_data = [
                    'paslon_chairman' => (set_value('ketua_student_id') == '') ? NULL : set_value('ketua_student_id'),
                    'paslon_vice_chairman' => (set_value('wakil_student_id') == '') ? NULL : set_value('wakil_student_id'),
                    'paslon_chairman_pict' => NULL,
                    'paslon_vice_chairman_pict' => NULL,
                    'nomor_urut' => set_value('nomor_urut'),
                    'vision' => (set_value('vision') == '') ? NULL : set_value('vision'),
                    'mision' => (set_value('mission') == '') ? NULL : set_value('mission')
                ];

                if (empty($s_paslon_id)) {
                    $mba_kpu_data = $this->General->get_where('vote_period', ['period_status' => 'active']);
                    $s_period_id = $mba_kpu_data[0]->period_id;
                    $a_paslon_data['paslon_id'] = $this->uuid->v4();
                    $a_paslon_data['period_id'] = $s_period_id;
                    $a_paslon_data['date_added'] = date('Y-m-d H:i:s');
                }
                else {
                    $mba_kpu_data = $this->General->get_where('vote_paslon', ['paslon_id' => $s_paslon_id]);
                    $s_period_id = $mba_kpu_data[0]->period_id;
                }
                
                $s_file_path = APPPATH.'uploads/bem/kpu/'.$s_period_id.'/';
                if (!empty($_FILES['ketua_picture']['name'])) {
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = 102400;
                    $config['encrypt_name'] = true;
                    $config['file_ext_tolower'] = true;
                    $config['upload_path'] = $s_file_path;

                    if(!file_exists($s_file_path)){
                        mkdir($s_file_path, 0755, true);
                    }
                    $this->load->library('upload', $config);

                    if($this->upload->do_upload('ketua_picture')){
                        $a_paslon_data['paslon_chairman_pict'] = $this->upload->data('file_name');
                    }
                    else{
                        $a_return = array('code' => 2, 'message' => $this->upload->display_errors('<span>', '</span><br>'));
                        print json_encode($a_return);
                        exit;
                    }
                }

                if (!empty($_FILES['wakil_picture']['name'])) {
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = 102400;
                    $config['encrypt_name'] = true;
                    $config['file_ext_tolower'] = true;
                    $config['upload_path'] = $s_file_path;

                    if(!file_exists($s_file_path)){
                        mkdir($s_file_path, 0755, true);
                    }
                    $this->load->library('upload', $config);

                    if($this->upload->do_upload('wakil_picture')){
                        $a_paslon_data['paslon_vice_chairman_pict'] = $this->upload->data('file_name');
                    }
                    else{
                        $a_return = array('code' => 2, 'message' => $this->upload->display_errors('<span>', '</span><br>'));
                        print json_encode($a_return);
                        exit;
                    }
                }

                if (empty($s_paslon_id)) {
                    $submit_data = $this->Km->save_paslon($a_paslon_data);
                }
                else {
                    $submit_data = $this->Km->save_paslon($a_paslon_data, $s_paslon_id);
                }
                
                if ($submit_data) {
                    $a_return = ['code' => 0, 'message' => 'Success!', 'data' => $a_paslon_data];
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'Error Processing your data!'];
                }
            }
            else {
                $a_return = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }

            print json_encode($a_return);
        }
    }

    public function submit_vote()
    {
        if ($this->input->is_ajax_request()) {
            $s_paslon_id = $this->input->post('target');
            
            $mba_paslon_data = $this->General->get_where('vote_paslon', ['paslon_id' => $s_paslon_id]);
            if ($mba_paslon_data) {
                $mba_kpu_data = $this->General->get_where('vote_period', [
                    'period_id' => $mba_paslon_data[0]->period_id,
                    'period_voting_start <= ' => date('Y-m-d H:i:s'),
                    'period_voting_end >= ' => date('Y-m-d H:i:s')
                ]);

                if ($mba_kpu_data) {
                    $a_vote_data = [
                        'voting_id' => $this->uuid->v4(),
                        'student_id' => $this->session->userdata('student_id'),
                        'period_id' => $mba_paslon_data[0]->period_id,
                        'has_pick' => 'yes',
                        'date_added' => date('Y-m-d H:i:s')
                    ];

                    $a_paslon_data = [
                        'paslon_result' => intval($mba_paslon_data[0]->paslon_result) + 1
                    ];

                    $submit_vote = $this->Km->submit_voting($a_vote_data, $a_paslon_data, $s_paslon_id);
                    if ($submit_vote) {
                        $a_return = ['code' => 0, 'message' => 'Success!'];
                        $this->session->unset_userdata('show_vote_modal');
                        // send email
                        $this->send_notification_telegram($this->session->userdata('name').' has vote '.$s_paslon_id);
                    }
                    else {
                        $a_return = ['code' => 1, 'message' => 'Cannt submit your vote!'];
                    }
                }
                else {
                    $a_return = ['code' => 2, 'message' => 'Error retrieve vote, please contact IT Dept.'];
                }
            }
            else {
                $a_return = ['code' => 2, 'message' => 'Error retrieve voting data!, please contact IT Dept.!'];
            }

            print json_encode($a_return);
        }
    }

    public function get_access()
    {
        $a_access_kpu_vote = $this->config->item('bem_member')['kpu'];

        return (in_array($this->session->userdata('user'), $a_access_kpu_vote)) ? true : false;
    }
}
