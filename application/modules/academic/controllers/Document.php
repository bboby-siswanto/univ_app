<?php
class Document extends App_core
{
    function __construct()
    {
        parent::__construct();
    }

    public function list()
    {
        $this->a_page_data['list_doc'] = $this->list_dir();
		$this->a_page_data['body'] = $this->load->view('academic/document/academic_document', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
    }

    public function list_dir($s_category = 'thesis_template')
    {
        $this->load->helper('directory');

        $s_dir = APPPATH.'uploads/public/public_student/';
        switch ($s_category) {
            case 'thesis_template':
                $s_dir.='thesis_template/';
                break;

            case 'finance':
                $s_dir.='finance/';
                break;

            case 'labolatory':
                $s_dir.='labolatory/';
                break;

            case 'general':
                $s_dir.='general/';
                break;

            case 'internship':
                $s_dir.='internship_template/';
                break;

            case 'international_office':
                $s_dir.='international_office/';
                break;

            case 'zoomid_timetable':
                $s_dir.='zoomid_timetable/';
                break;
            
            default:
                $s_dir.='empty/';
                break;
        }
        
        $a_list_dir = directory_map($s_dir);
        return $a_list_dir;
        // print('<pre>');
        // var_dump($a_list_dir);
    }

    public function upload_document()
    {
        if ($this->input->is_ajax_request()) {
            $directory_file = APPPATH.'uploads/academic/public_document/';
            if(!file_exists($directory_file)){
                mkdir($directory_file, 0755);
            }

            $config['upload_path'] = $directory_file;
            $config['allowed_types'] = 'jpg|jpeg|png|pdf|zip|docx|doc|xlsx|dotx';
            $config['max_size'] = 10240;
            $config['file_ext_tolower'] = TRUE;

            $this->load->library('upload', $config);
            if($this->upload->do_upload('file_data')) {
                $a_return = array('code' => 0, 'message' => 'Upload success');
            }else{
                $a_return = array('code' => 1, 'message' => $this->upload->display_errors('<li>', '</li>'), 'data' => $this->upload->data());
            }

            print json_encode($a_return);
        }
    }

    public function remove_doc()
    {
        if ($this->input->is_ajax_request()) {
            $s_filename = $this->input->post('filename');

            $s_path = APPPATH.'uploads/academic/public_document/'.$s_filename;
            if (unlink($s_path)) {
                $a_return = array('code' => 0, 'message' => 'Remove success');
            }else{
                $a_return = array('code' => 1, 'message' => 'Remove failed');
            }

            print json_encode($a_return);
        }
    }
}
