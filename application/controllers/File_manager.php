<?php
class File_manager extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('File_manager_model', 'File_manager');
	}

	public function staff_download($s_personal_data_id, $s_personal_document_id, $b_view_only = false)
	{
		$mba_document_data = $this->General->get_where('dt_personal_document', ['personal_document_id' => $s_personal_document_id]);
		$mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $s_personal_data_id]);
		if ($mba_document_data AND $mba_personal_data) {
			$o_document = $mba_document_data[0];
			$o_personal_data = $mba_personal_data[0];
			$s_firts_char_user = substr($o_personal_data->personal_data_name, 0, 1);
			$s_file = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$s_personal_data_id.'/'.$o_document->document_link;

			if (!file_exists($s_file)) {
				show_404();
			}
			else {
				$a_file = explode('/', $s_file);
				$s_filename = $a_file[count($a_file) -1];
				
				$s_mime = mime_content_type($s_file);
				header("Content-Type: ".$s_mime);
				if(!$b_view_only){
					header('Content-Disposition: attachment; filename='.urlencode($s_filename));
				}
				readfile( $s_file );
				exit;
			}
		}
	}

	public function view_doc($s_case = 'academic_calendar_2024', $s_filename = false)
	{
		if ($s_filename) {
			$s_filename = urldecode($s_filename);
		}
		switch ($s_case) {
			case 'academic_calendar':
				$s_file_path = APPPATH.'uploads/public/public_student/academic_calendar/Acad_Cal_2023_2024v0 signed.pdf';
				break;

			case 'academic_calendar_2022':
				$s_file_path = APPPATH.'uploads/public/public_student/academic_calendar/Acad_Cal_2022_2023v1 signed.pdf';
				break;

			case 'academic_calendar_2023':
				$s_file_path = APPPATH.'uploads/public/public_student/academic_calendar/Acad_Cal_2023_2024v0 signed.pdf';
				break;

			case 'academic_calendar_2024':
				$s_file_path = APPPATH.'uploads/public/public_student/academic_calendar/Acad_Cal_2024_2025v0x signed.pdf';
				break;
				
			case 'academic_regulation':
				$s_file_path = APPPATH.'uploads/public/public_student/academic_regulation/Academic Regulation June 2023 Rev11.pdf';
				break;

			case 'zoomid_timetable':
				$s_file_path = APPPATH.'uploads/public/public_student/zoomid_timetable/'.$s_filename;
				break;
			
			default:
				$s_file_path = '';
				show_404();
				break;
		}

		if(!file_exists($s_file_path)){
			show_404();
			// print($s_file_path);exit;
		}
		else {
			$s_mime = mime_content_type($s_file_path);
			$a_path_info = pathinfo($s_file_path);
			$s_file_ext = $a_path_info['extension'];
			header("Content-Type: ".$s_mime);
			readfile( $s_file_path );
			exit;
		}
	}

	public function academic_calendar()
	{
		$s_file_path = APPPATH.'uploads/public/public_student/academic_calendar/Acad_Cal_2021_2022v1 signed TN.pdf';
		if(!file_exists($s_file_path)){
			show_404();
		}
		else {
			$s_mime = mime_content_type($s_file_path);
			$a_path_info = pathinfo($s_file_path);
			$s_file_ext = $a_path_info['extension'];
			header("Content-Type: ".$s_mime);
			readfile( $s_file_path );
			exit;
		}
	}

	public function student_files($s_type, $s_student_id, $s_filename, $b_view_only = false)
	{
		$this->load->model('student/Student_model', 'Stm');
		$this->load->model('finance/Finance_model', 'Fim');
		$s_file_path = false;
		$mbo_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
		if ($mbo_student_data) {
			switch ($s_type) {
				case 'refund':
					$mba_aid_registration = $this->Fim->get_aid_request_details(['saf.request_receipt_bill_file' => $s_filename]);
					if ($mba_aid_registration) {
						$o_registration = $mba_aid_registration[0];
						$s_file_path = APPPATH.'uploads/'.$mbo_student_data[0]->personal_data_id.'/receipt_bill/'.$o_registration->aid_period_year.'_'.$o_registration->aid_period_month.'/'.$s_filename;
					}
					break;
					
				case 'record':
					$s_file_path = APPPATH.'uploads/'.$mbo_student_data[0]->personal_data_id.'/record/'.$s_filename;
					break;
				
				default:
					break;
			}
		}

		if ($s_file_path) {
			$this->_download_file($s_file_path, $b_view_only);
		}
		else {
			show_404();
			// print('apaan?');
		}
	}

	private function _download_file($s_path, $b_view_only = false)
	{
		if(!file_exists($s_path)){
			show_404();
		}
		else {
			$a_path_info = pathinfo($s_path);
			$s_file_ext = $a_path_info['extension'];
			$a_path = explode('/', $s_path);
			$download_filename = urlencode($a_path[count($a_path) - 1]);

			$s_mime = mime_content_type($s_path);
			header("Content-Type: ".$s_mime);
			if(!$b_view_only){
				header('Content-Disposition: attachment; filename='.urlencode($s_download_filename));
			}
			readfile( $s_path );
			exit;
		}
	}
	
	public function view($s_document_id, $s_personal_data_id)
	{
		$this->download($s_document_id, $s_personal_data_id, true);
	}

	public function view_public($s_document_id, $s_personal_data_id)
	{
		$this->download($s_document_id, $s_personal_data_id, true);
	}
	
	public function flash_download()
	{
		$s_file_path = $this->session->flashdata('file_token');
		$a_path_info = pathinfo($s_file_path);
		$s_file_ext = $a_path_info['extension'];
		header('Content-Disposition: attachment; filename='.urlencode(implode('.', ['download', $s_file_ext])));
		readfile($s_file_path);
	}

	public function download_files($s_filesencrypt = false, $s_customfilename = false)
	{
		if (!$s_filesencrypt) {
			return show_404();
		}

		$s_file = base64_decode(urldecode($s_filesencrypt));
		$s_file_path = APPPATH.'uploads/'.$s_file;
		// print($s_file);exit;
		if(!file_exists($s_file_path)){
			print('empty data: '.$s_file_path);exit;
			return show_404();
		}
		else {
			$a_path_info = pathinfo($s_file_path);
			$s_file_ext = $a_path_info['extension'];
			// print('<pre>');var_dump($a_path_info);exit;
			$s_mime = mime_content_type($s_file_path);
			$a_file = explode('/', $s_file_path);
			$s_download_filename = $a_file[count($a_file) - 1];
			$s_download_filename = str_replace('_', ' ', $s_download_filename).'.'.$s_file_ext;

			$s_download_filename = ($s_customfilename) ? urldecode($s_customfilename) : $s_download_filename;
			header("Content-Type: ".$s_mime);
			header("filename: ".$s_download_filename);
			
			readfile( $s_file_path );
			exit;
		}

		// $s_path = 'student/2023/Oct/21e66a5e-492a-4fcd-915d-2a02a8aa711e/budi_siswanto_KTP_or_Passport.pdf';
		// $s_file = base64_encode($s_path);
		// print($s_file);exit;
	}
	
	public function download($s_document_id, $s_personal_data_id, $b_view_only = false)
	{	
		$mbo_personal_data_document = $this->File_manager->get_files($s_personal_data_id, $s_document_id);
		if($mbo_personal_data_document){
			if(count($mbo_personal_data_document) == 1){
				$s_file_path = APPPATH.'uploads/'.$s_personal_data_id.'/'.$mbo_personal_data_document[0]->document_requirement_link;
				// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
				// 	print('<pre>');var_dump($s_file_path);exit;
				// }
				if(!file_exists($s_file_path)){
			    	return show_404();
			    }
				else{
					$a_path_info = pathinfo($s_file_path);
					$s_file_ext = $a_path_info['extension'];
					$s_mime = mime_content_type($s_file_path);
					$s_download_filename = str_replace(' ', '_', implode('-', array($mbo_personal_data_document[0]->personal_data_name, $mbo_personal_data_document[0]->document_name))).'.'.$s_file_ext;
					header("Content-Type: ".$s_mime);
					
					if(!$b_view_only){
						header('Content-Disposition: attachment; filename='.urlencode($s_download_filename));
					}
					
			    	readfile( $s_file_path );
			    	exit;
				}
			}
			else if ($s_document_id == '0bde3152-5442-467a-b080-3bb0088f6bac') {
				redirect(base_url().'assets/img/silhouette.png');
			}
			else{
				show_404();
			}
		}
		else if ($s_document_id == '0bde3152-5442-467a-b080-3bb0088f6bac') {
			redirect(base_url().'assets/img/silhouette.png');
		}
		else{
			show_404();
		}
	}

	public function download_template($s_file, $s_academic_semester = false)
	{
		if ($s_academic_semester) {
			$s_file_path = APPPATH.'/uploads/academic/'.$s_academic_semester.'/'.$s_file;
		}else{
			$s_file_path = APPPATH.'/uploads/templates/'.$s_file;
		}
		
		if(!file_exists($s_file_path)){
			return show_404();
		}
		else{
			$a_path_info = pathinfo($s_file_path);
			$s_file_ext = $a_path_info['extension'];
			// header("Content-Type: ".$mbo_personal_data_document[0]->document_mime);
			header('Content-Disposition: attachment; filename='.urlencode($s_file));
			readfile( $s_file_path );
			exit;
		}
	}

	public function download_temp($s_filename)
	{
		$s_filepath = APPPATH.'/uploads/temp/'.$s_filename;
		if(!file_exists($s_filepath)){
			return show_404();
		}
		else {
			$a_path_info = pathinfo($s_filepath);
			$s_file_ext = $a_path_info['extension'];
			// header("Content-Type: ".$mbo_personal_data_document[0]->document_mime);
			header('Content-Disposition: attachment; filename='.urlencode($s_filename));
			readfile( $s_filepath );

			unlink($s_filepath);
			exit;
		}
	}

	public function academic_download($s_file, $s_target, $s_year_semester = false)
	{
		if (!$s_year_semester) {
			$this->load->model('academic/Semester_model', 'Smm');

			$mbo_semester_active = $this->Smm->get_active_semester();
        	$s_year_semester = $mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id;
		}

        switch ($s_target) {
            case 'score_template':
                $s_file_path = APPPATH.'/uploads/templates/score_class/'.$s_file;
                break;

            case 'transcript_semester':
                $s_file_path = APPPATH.'/uploads/academic/'.$s_year_semester.'/transcript/'.$s_study_abbr.'/'.$s_file;
                break;

            case 'cummulative_gpa':
                $s_file_path = APPPATH.'/uploads/academic/'.$s_year_semester.'/cummulative_gpa/'.$s_file;
                break;

            case 'student_all_class':
                $s_file_path = APPPATH.'/uploads/academic/'.$s_year_semester.'/'.$s_file;
                break;
			
			case 'krs_registration':
                $s_file_path = APPPATH.'/uploads/academic/'.$s_year_semester.'/'.$s_file;
                break;
            
            default:
                $s_file_path = '';
                break;
        }

        // if ($this->input->is_ajax_request()) {
        //     $s_file_path = $this->input->post('file_path');
        // }

        if(!file_exists($s_file_path)){
            // return show_404();
            var_dump($s_file_path);
            var_dump($s_target);
            print('<pre>');
            var_dump($this->uri->segment_array());
		}
		else{
			$a_path_info = pathinfo($s_file_path);
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($s_file));
			readfile( $s_file_path );
			exit;
		}
	}

	public function download_template_letter($s_letter_type_id, $s_filename)
	{
		// $s_file_decode = urldecode($s_filename);
		$s_file_decode = $s_filename;
		$s_filepath = APPPATH.'uploads/templates/spmi/';
		$s_fullpath = $s_filepath.$s_file_decode;
		// print($s_fullpath);

		$a_list_file = [
			'1' => [
				'Application_Letter_to_Thesis_Research-Internship_Student_Template.docx'
			],
			'2' => [
				'Decree_Letter_Template_From_Rector_IULI.docx'
			],
			'7' => [
				'Assignment_Letter_Study_Program_Lecturing_Assignment_Template.docx'
			],
		];

		if (is_file($s_fullpath)) {
            header('Content-Disposition: attachment; filename='.$s_file_decode);
            readfile( $s_filepath . $s_file_decode );
            exit;
        }
        else {
            // log_message('error', 'ERROR from '.__FILE__.' '.__LINE__);
            $this->a_page_data['page_error'] = current_url();
            $this->a_page_data['body'] = $this->load->view('dashboard/student_error', $this->a_page_data, true);
            $this->load->view('layout', $this->a_page_data);
        }
		// $s_letter_type_names = 'Application Letter to Thesis Research-Internship Student.docx';
		// // print(urlencode($s_letter_type_name));
		// redirect(base_url().'file_manager/'.urlencode($s_letter_type_names));
		// print('<br>');
		// print(urlencode($s_letter_type_names));
		// var_dump($a_list_file[$s_letter_type_id]);

		// $s_file_path = APPPATH."uploads/public/spmi/$s_letter_type_name";
		// print($s_file_path);
	}

	public function public_download($s_category, $s_filename)
	{
		$s_filename = urldecode($s_filename);
		$s_filepath = APPPATH.'uploads/public/'.$s_category.'/';
		$s_fullpath = $s_filepath.$s_filename;

		if(!file_exists($s_fullpath)){
            $a_return = [
				'code' => 1
			];
            // var_dump($s_file_path);
            // var_dump($s_target);
            // print('<pre>');
            // var_dump($this->uri->segment_array());
		}
		else{
			// $a_path_info = pathinfo($s_fullpath);
			// $s_file_ext = $a_path_info['extension'];
			// header('Content-Disposition: attachment; filename='.urlencode($s_filename));
			// readfile( $s_fullpath );
			// exit;
			$a_return = [
				'code' => 0,
				'fp' => $s_fullpath,
				'fn' => $s_filename
			];
		}

		return $a_return;
	}
}