<?php
class Documents extends App_core
{
    function __construct()
    {
        parent::__construct('student_document');
        $this->load->model('student/Student_model', 'Stm');
    }

    public function download($s_category, $s_file)
    {
        if ($s_category == '') {
            show_404();
        }
        $s_file_decode = urldecode($s_file);
        $s_path = APPPATH.'uploads/public/public_student/'.$s_category.'/';
        $s_file_path = $s_path.$s_file_decode;
        if ((!empty($s_category)) AND (is_file($s_file_path))) {
            header('Content-Disposition: attachment; filename='.$s_file_decode);
            readfile( $s_path . $s_file_decode );
            exit;
        }
        else {
            log_message('error', 'ERROR from '.__FILE__.' '.__LINE__);
            $this->a_page_data['page_error'] = current_url();
            $this->a_page_data['body'] = $this->load->view('dashboard/student_error', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
    }

    public function thesis()
    {
        $this->a_page_data['s_category'] = 'thesis_template';
        $this->a_page_data['doc_list'] = modules::run('academic/document/list_dir', 'thesis_template');
        $this->a_page_data['body'] = $this->load->view('document/document_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }
    
    public function finance()
    {
        $this->a_page_data['s_category'] = 'finance';
        $this->a_page_data['doc_list'] = modules::run('academic/document/list_dir', 'finance');
        $this->a_page_data['body'] = $this->load->view('document/document_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }
    
    public function laboratory()
    {
        $this->a_page_data['s_category'] = 'labolatory';
        $this->a_page_data['doc_list'] = modules::run('academic/document/list_dir', 'labolatory');
        $this->a_page_data['body'] = $this->load->view('document/document_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function general()
    {
        $this->a_page_data['s_category'] = 'general';
        $this->a_page_data['doc_list'] = modules::run('academic/document/list_dir', 'general');
        $this->a_page_data['body'] = $this->load->view('document/document_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function internship()
    {
        $this->a_page_data['s_category'] = 'internship_template';
        $this->a_page_data['doc_list'] = modules::run('academic/document/list_dir', 'internship');
        $this->a_page_data['body'] = $this->load->view('document/document_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }

    public function international_office()
    {
        $this->a_page_data['s_category'] = 'international_office';
        $this->a_page_data['doc_list'] = modules::run('academic/document/list_dir', 'international_office');
        $this->a_page_data['body'] = $this->load->view('document/document_list', $this->a_page_data, true);
        $this->load->view('layout', $this->a_page_data);
    }
}
