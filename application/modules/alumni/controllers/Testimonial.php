<?php
class Testimonial extends App_core
{
    function __construct()
    {
        $s_user = ($this->session->userdata('type') == 'staff') ? 'staff_alumni' : 'student_alumni';
        parent::__construct($s_user);
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
    }

    public function testimonial_lists()
    {
        $this->a_page_data['body'] = $this->load->view('testimonial/list_staff', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function view_list_testimonial()
    {
        $this->load->view('testimonial/table/testimonial_list_table');
    }

    public function my_testimonial()
    {
        if ($this->session->userdata('type') == 'staff') {
            redirect('alumni/testimonial/testimonial_lists');
        }
        $this->a_page_data['body'] = $this->load->view('testimonial/default', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function form_input_testimonial()
    {
        $s_personal_data_id = $this->session->userdata('user');
        $this->a_page_data['o_student_testimoni'] = $this->Pdm->get_testimonial_personal_data($s_personal_data_id);
        $s_html = $this->load->view('testimonial/form/form_input_testimonial', $this->a_page_data, true);
        return $s_html;
    }

    public function get_testimonial_list()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('alumni/Info_model', 'Inm');
            $mba_testimonial_data = $this->Inm->get_testimonial_data();
            $a_return  = array('code' => 0, 'data' => $mba_testimonial_data);
            print json_encode($a_return);
        }
    }

    public function save_testimoni()
    {
        if ($this->input->is_ajax_request()) {
            $s_personal_data_id = $this->session->userdata('user');

            $this->form_validation->set_rules('testimoni', 'Testimoni', 'trim|required|max_length[500]');
            if ($this->form_validation->run()) {
                $a_testimoni_data = array(
                    'personal_data_id' => $s_personal_data_id,
                    'testimoni' => set_value('testimoni')
                );

                if ($this->input->post('testimonial_id') == '') {
                    $a_testimoni_data['testimonial_id'] = $this->uuid->v4();
                    $save_data = $this->Pdm->save_testimonial($a_testimoni_data);
                }else{
                    $save_data = $this->Pdm->save_testimonial($a_testimoni_data, $this->input->post('testimonial_id'));
                }

                if ($save_data) {
                    $rtn = array('code' => 0, 'message' => 'Success');
                }else{
                    $rtn = array('code' => 1, 'message' => 'Error processing testimoni');
                }
            }else{
                $rtn = array('code' => 1, 'message' => validation_errors('<li>', '</li>'));
            }
        }else{
            $rtn = array('code' => 1, 'message' => 'Nothing action');
        }

        echo json_encode($rtn);
        exit;
    }
}
