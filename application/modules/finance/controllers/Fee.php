<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Fee extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Finance_model', 'Fm');
		$this->load->model('study_program/Study_program_model', 'Spm');
		$this->load->model('admission/Admission_model', 'Adm');
	}
	
	public function lists()
	{
		$this->a_page_data['active_year'] = $this->Adm->get_active_intake_year();
		$this->a_page_data['academic_year'] = $this->General->get_batch();
		$this->a_page_data['semester'] = $this->General->get_semester();
		$this->a_page_data['scholarship'] = $this->General->get_scholarship();
		$this->a_page_data['programs'] = $this->General->get_where('ref_program', ['master_program_id != ' => NULL]);
		$this->a_page_data['payment_type'] = $this->Fm->get_payment_type_code();
		$this->a_page_data['study_programs'] = $this->Spm->get_study_program(false, false);
		$this->a_page_data['body'] = $this->load->view('fee/lists', $this->a_page_data, true);
		$this->load->view('layout', $this->a_page_data);
	}
	
	public function save_fee_data()
	{
		if($this->input->is_ajax_request()){
			$this->form_validation->set_rules('fee_description', 'Fee description', 'trim|required');
			$this->form_validation->set_rules('fee_amount_number_type', 'Number type', 'trim|required');
			$this->form_validation->set_rules('fee_amount', 'Amount', 'trim|required|numeric');
			$this->form_validation->set_rules('fee_amount_type', 'Amount', 'trim|required');
			$this->form_validation->set_rules('fee_amount_sign_type', 'Sign/Operator', 'trim|required');
			$this->form_validation->set_rules('academic_year_id', 'Year', 'trim|required');
			
			if($this->form_validation->run()){
				$a_fee_data = array(
					'fee_description' => set_value('fee_description'),
					'fee_amount_number_type' => set_value('fee_amount_number_type'),
					'fee_amount' => set_value('fee_amount'),
					'fee_amount_type' => set_value('fee_amount_type'),
					'fee_amount_sign_type' => set_value('fee_amount_sign_type'),
					'academic_year_id' => set_value('academic_year_id'),
					'fee_alt_description' => (!empty(set_value('fee_alt_description'))) ? set_value('fee_alt_description') : NULL
				);
				
				if($s_fee_id = $this->input->post('fee_id')){
					foreach($this->input->post() as $key => $val){
						if(!in_array($key, $a_fee_data)){
							$a_fee_data[$key] = ($val == '') ? null : $val;
						}
					}
					// var_dump($a_fee_data);exit;
					$this->Fm->update_fee($a_fee_data, $s_fee_id);
					$rtn = array('code' => 0, 'message' => 'Success!');
				}
				else{
					$a_fee_check_clause = array();
					
					($this->input->post('academic_year_id')) ? $a_fee_check_clause['academic_year_id'] = $this->input->post('academic_year_id') : '';
					($this->input->post('program_id')) ? $a_fee_check_clause['program_id'] = $this->input->post('program_id') : '';
					($this->input->post('scholarship_id')) ? $a_fee_check_clause['scholarship_id'] = $this->input->post('scholarship_id') : $a_fee_check_clause['scholarship_id'] = NULL;
					($this->input->post('study_program_id')) ? $a_fee_check_clause['study_program_id'] = $this->input->post('study_program_id') : NULL;
					($this->input->post('semester_id')) ? $a_fee_check_clause['semester_id'] = $this->input->post('semester_id') : $a_fee_check_clause['semester_id'] = NULL;
					($this->input->post('payment_type_code')) ? $a_fee_check_clause['payment_type_code'] = $this->input->post('payment_type_code') : '';
					
					// $mbo_old_fee_data = (set_value('fee_amount_type') == 'main') ? $this->Fm->is_fee_exists($a_fee_check_clause) : false;
					// if($mbo_old_fee_data){
					// 	$rtn = array('code' => 1, 'message' => 'Settings available, please consider editing it', 'data' => $a_fee_check_clause);
					// }
					// else{
						if(set_value('fee_amount_type') == 'main'){
							$a_fee_data = array_merge($a_fee_data, $a_fee_check_clause);
							$this->Fm->insert_fee($a_fee_data);
							$rtn = array('code' => 0, 'message' => 'Success!', 'academic_year_id' => set_value('academic_year_id'));
						}
						else{
							$a_fee_data = array_merge($a_fee_data, $a_fee_check_clause);
							$this->Fm->insert_fee($a_fee_data);
							$rtn = array('code' => 0, 'message' => 'Success!', 'academic_year_id' => set_value('academic_year_id'));
						}
					// }
				}
			}
			else{
				$rtn = array('code' => 1, 'message' => validation_errors('<span>', '</span><br>'));
			}
			
			print json_encode($rtn);
			exit;
		}
	}
	
	public function get_all_fee()
	{
		if($this->input->is_ajax_request()){
			$a_clause = array();
			
			foreach($this->input->post() as $key => $value){
				($value != '') ? $a_clause['df.'.$key] = $value : '';
			}
			
			$mba_fee_list = $this->Fm->get_all_fee($a_clause);
			
			print json_encode(array('code' => 0, 'data' => $mba_fee_list));
			exit;
		}
	}

	public function download_excel_template($s_academic_year_id, $s_program_id = 1, $s_payment_type_id)
	{
		$mba_payment_type_data = $this->General->get_where('ref_payment_type', ['payment_type_code' => $s_payment_type_id]);
		$mba_program_data = $this->General->get_where('ref_program', ['program_id' => $s_program_id]);
		$mba_study_program_list = $this->Spm->get_study_program(false, false);
		if (($mba_payment_type_data) AND ($mba_program_data) AND ($mba_study_program_list)) {
			$s_payment_name = ($s_payment_type_id == '02') ? $mba_payment_type_data[0]->payment_type_name : $mba_payment_type_data[0]->payment_type_name.' Fee';
			$s_template_path = APPPATH.'uploads/templates/blank_template.xlsx';
			$s_file_name = str_replace(' ', '', $s_payment_name).'_batch_'.$s_academic_year_id.'_Master';
			$s_filename = $s_file_name.'.xlsx';

			$s_file_path = APPPATH."uploads/finance/".strtolower(str_replace(' ', '_', $s_payment_name))."/".$s_academic_year_id."/";
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

			$i_row = 1;

            $o_spreadsheet = IOFactory::load($s_template_path);
            $o_spreadsheet->getProperties()
                ->setTitle($s_file_name)
                ->setCreator("Finance")
                ->setCategory("Student Fee");

            $o_sheet = $o_spreadsheet->getActiveSheet();
            $o_sheet->setCellValue('A'.$i_row++, strtoupper($s_payment_name));
            $o_sheet->setCellValue('A'.$i_row, 'Payment Code');
            $o_sheet->setCellValue('B'.$i_row, '="'.$s_payment_type_id.'"');
			$i_row++;
            $o_sheet->setCellValue('A'.$i_row, 'Batch');
            $o_sheet->setCellValue('B'.$i_row, $s_academic_year_id.'/'.(intval($s_academic_year_id) + 1));
			$i_row++;
            $o_sheet->setCellValue('A'.$i_row, 'Class Program');
            $o_sheet->setCellValue('B'.$i_row, $mba_program_data[0]->program_code);
			$i_row++;

			$o_sheet->setCellValue('A'.$i_row, 'Study Program');
			$o_sheet->setCellValue('B'.$i_row, 'Study Program Abbr');
			$o_sheet->setCellValue('C'.$i_row, 'Payment/Semester (1-8 Semester)');
			$o_sheet->setCellValue('K'.$i_row, 'Total');
			$o_sheet->setCellValue('L'.$i_row, 'Semester > 8');

			$o_sheet->mergeCells('A'.$i_row.':A'.($i_row + 1));
			$o_sheet->mergeCells('B'.$i_row.':B'.($i_row + 1));
			$o_sheet->mergeCells('K'.$i_row.':K'.($i_row + 1));
			$o_sheet->mergeCells('C'.$i_row.':J'.$i_row);
			$o_sheet->mergeCells('L'.$i_row.':Q'.$i_row);
			$i_row++;
			
			$char_col = 'C';
			for ($i=1; $i <=8 ; $i++) { 
				$o_sheet->setCellValue($char_col.$i_row, $i);
				$char_col++;
			}
			$char_col = 'L';
			for ($i=9; $i <=14 ; $i++) { 
				$o_sheet->setCellValue($char_col.$i_row, $i);
				$char_col++;
			}
			$i_row++;

			foreach ($mba_study_program_list as $o_prodi) {
				$char_colfee = 'C';
				for ($semester = 1; $semester <= 14; $semester++) { 
					if ($char_colfee == 'K') {
						$o_sheet->setCellValue('K'.$i_row, '=SUM(C'.$i_row.':J'.$i_row.')');
						$char_colfee++;
						continue;
					}
					$mba_semester_data = $this->General->get_where('ref_semester', ['semester_number' => $semester]);
					if ($mba_semester_data) {
						$a_fee_filter = [
							'payment_type_code' => $s_payment_type_id,
							'program_id' => $s_program_id,
							'study_program_id' => $o_prodi->study_program_id,
							'academic_year_id' => $s_academic_year_id,
							'semester_id' => $mba_semester_data[0]->semester_id,
							'scholarship_id' => NULL,
							'fee_special' => 'false'
						];

						$mba_fee_data = $this->General->get_where('dt_fee', $a_fee_filter);
						// $mbo_fee_data = $this->Fm->is_fee_exists($a_fee_filter);
						// if (($o_prodi->study_program_id == '208c8d88-2560-4640-a1b2-bfd42b0e7c16') AND ($mba_semester_data[0]->semester_id == '3')) {
						// 	print('<pre>');var_dump($mba_fee_data);exit;
						// }
						if ($mba_fee_data) {
							// print('<pre>');var_dump($mbo_fee_data);exit;
							$o_sheet->setCellValue($char_colfee.$i_row, $mba_fee_data[0]->fee_amount);
						}
					}

					$char_colfee++;
				}
				
				$o_sheet->setCellValue('A'.$i_row, $o_prodi->study_program_name);
				$o_sheet->setCellValue('B'.$i_row, $o_prodi->study_program_abbreviation);
				$i_row++;
			}

			$o_writer = IOFactory::createWriter($o_spreadsheet, 'Xlsx');
            $o_writer->save($s_file_path.$s_filename);
            $o_spreadsheet->disconnectWorksheets();
            unset($o_spreadsheet);

            // if ($b_force_download) {
            $a_path_info = pathinfo($s_file_path.$s_filename);
            $s_file_ext = $a_path_info['extension'];
            header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            readfile( $s_file_path.$s_filename );
            exit;
			// print('<pre>');var_dump($mba_payment_type_data);exit;
		}
		else {
			$a_return = ['code' => 1, 'message' => 'Payment type not found'];
			print('<pre>');var_dump($a_return);exit;
		}
	}

	public function upload_excel_files()
	{
		# code...
	}

	public function upload_excel_fee()
	{
		$s_file_path = "";
		// $s_file_path = APPPATH."uploads/finance/tuition_fee/".$s_academic_year_id."/TuitionFee_batch_".$s_academic_year_id."_Master.xlsx";
		// $s_file_path = APPPATH."uploads/finance/tuition_fee/".$s_academic_year_id."/ShortSemesterFee_batch_".$s_academic_year_id."_Master.xlsx";

		if ($this->input->is_ajax_request()) {
			$s_file_path = APPPATH."uploads/finance/tuition_fee/";
			$o_spreadsheet = IOFactory::load($_FILES["filefee"]["tmp_name"]);
			$o_sheet = $o_spreadsheet->setActiveSheetIndex(0);
			$s_htmllog = "Input Logs ================================================== <br>";
			
			$s_program_file =  trim($o_sheet->getCell('B4')->getValue());
			$s_academic_year_file =  trim($o_sheet->getCell('B3')->getValue());
			$s_payment_type_file =  trim($o_sheet->getCell('B2')->getValue());

			$s_program_id = str_replace('=', '', str_replace('"', '', $s_program_file));
			$s_academic_year_id = substr($s_academic_year_file, 0, 4);
			$s_payment_type_id = str_replace('=', '', str_replace('"', '', $s_payment_type_file));

			$mbo_program_data = $this->General->get_where('ref_program', ['program_code' => $s_program_id])[0];
			$mbo_payment_type_data = $this->General->get_where('ref_payment_type', ['payment_type_code' => $s_payment_type_id])[0];

			if (($mbo_payment_type_data) AND ($mbo_program_data)) {
				$i_max_semester_col = 0;
				switch ($s_payment_type_id) {
					case '02':
						$i_max_semester_col = 8;
						if ($o_sheet->getCell("J6")->getValue() != 8) {
							$a_return = ['code' => 1, 'message' => 'Wrong File Tuition Fee!'];
							print json_encode($a_return);exit;
						}
						break;

					case '04':
						$i_max_semester_col = 9;
						if ($o_sheet->getCell("J6")->getValue() != 8.5) {
							$a_return = ['code' => 1, 'message' => 'Wrong File Short Semester'];
							print json_encode($a_return);exit;
						}
						break;
					
					default:
						$a_return = ['code' => 1, 'message' => 'Payment type not set'];
						print json_encode($a_return);exit;
						break;
				}

				$i_row = 7;
				$a_fee_data = [];
				$a_error = [];
				while ($o_sheet->getCell("B$i_row")->getValue() !== NULL) {
					$s_prodi = trim($o_sheet->getCell('A'.$i_row)->getValue());
					$s_prodi_abbr = trim($o_sheet->getCell('B'.$i_row)->getValue());
					$mbo_study_program_data = $this->General->get_where('ref_study_program', ['study_program_abbreviation' => $s_prodi_abbr])[0];

					if ($mbo_study_program_data) {
						$char_col = 'C';
						for ($x=1; $x < 15 ; $x++) { 
							if ($char_col == 'K') {
								$char_col++;
								$x = 8;
								continue;
							}

							$semester_number = $x;
							$s_desc = '';
							switch ($s_payment_type_id) {
								case '02':
									$semester_number = $x;
									break;

								case '04':
									$semester_number = $x + 0.5;
									break;
								
								default:
									break;
							}

							$mbo_semester_data = $this->General->get_where('ref_semester', ['semester_number' => $semester_number])[0];
							if (!$mbo_semester_data) {
								// array_push($a_error, 'Semester Number '.$semester_number.' not found!');
								$a_return = ['code' => 1, 'message' => 'Semester Number '.$semester_number.' not found!'];
								print json_encode($a_return);exit;
							}

							switch ($s_payment_type_id) {
								case '02':
									$s_desc = 'Tuition Fee '.$mbo_semester_data->semester_name.' '.$s_prodi_abbr.' '.$s_academic_year_file;
									break;

								case '04':
									$s_desc = $mbo_semester_data->semester_name.' Fee '.$s_prodi_abbr.' '.$s_academic_year_file;
									break;
								
								default:
									break;
							}
							
							$d_fee = trim($o_sheet->getCell($char_col.$i_row)->getValue());
							// print($d_fee);exit;

							if (($d_fee != "") OR ($d_fee != "0")) {
								$a_fee_check_clause = [
									'payment_type_code' => $s_payment_type_id,
									'program_id' => $mbo_program_data->program_id,
									'study_program_id' => $mbo_study_program_data->study_program_id,
									'academic_year_id' => $s_academic_year_id,
									'semester_id' => $mbo_semester_data->semester_id,
									'scholarship_id' => NULL,
									'fee_special' => 'false'
								];
	
								$mba_old_fee_data = $this->General->get_where('dt_fee', $a_fee_check_clause);
								// $mbo_old_fee_data = $this->Fm->is_fee_exists($a_fee_check_clause);
								if (!$mba_old_fee_data) {
									$a_data = [
										'fee_id' => $this->uuid->v4(),
										'payment_type_code' => $s_payment_type_id,
										'program_id' => $mbo_program_data->program_id,
										'study_program_id' => $mbo_study_program_data->study_program_id,
										'academic_year_id' => $s_academic_year_id,
										'semester_id' => $mbo_semester_data->semester_id,
										'fee_amount' => $d_fee,
										'fee_description' => $s_desc
									];
									$s_numberamount = number_format($d_fee , 0, ",", ",");
									$s_htmllog .= $mbo_study_program_data->study_program_abbreviation." Semester $semester_number : IDR $s_numberamount successfully added<br>";
									array_push($a_fee_data, $a_data);
								}
							}

							$char_col++;
						}
					}else{
						array_push($a_error, 'Study program '.$s_prodi.' not found!');
					}
					
					$i_row++;
				}

				if (count($a_error) > 0) {
					$a_return = ['code' => 1, 'message' => '<li>'.implode('</li><li>', $a_error).'</li>'];
				}else if (count($a_fee_data) > 0) {
					$a_fee_data = array_values($a_fee_data);

					for ($i=0; $i < count($a_fee_data); $i++) { 
						$this->Fm->insert_fee($a_fee_data[$i]);
					}
					
					$a_return = ['code' => 0, 'message' => 'Success!', 'log_result' => $s_htmllog];
				}else{
					$a_return = ['code' => 1, 'message' => 'No data saved!'];
				}
			}
			else{
				$a_return = ['code' => 0, 'message' => 'Invalid payment type!'];
			}

			print json_encode($a_return);
		}

		// if (file_exists ( $s_file_path )) {
		// 	$o_spreadsheet = IOFactory::load("$s_file_path");
		
		// 	$o_sheet = $o_spreadsheet->setActiveSheetIndex(0);
			
		// }else{
		// 	$a_return = ['code' => 1, 'message' => 'File not found!'];
		// }

		// 
	}
}