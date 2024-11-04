<?php
// error_reporting(0);
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;	

class Doc_download extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('student/Student_model', 'Stm');
        $this->load->model('apps/Letter_numbering_model', 'Lnm');
        $this->load->model('academic/Score_model', 'Scm');
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
        $this->load->model('employee/Employee_model', 'Emm');
        $this->load->model('academic/Semester_model', 'Smm');
        $this->load->model('academic/Class_group_model', 'Cgm');
        $this->load->model('study_program/Study_program_model', 'Spm');
        $this->load->model('institution/Institution_model', 'Insm');
        $this->load->model('File_manager_model', 'File_manager');
        $this->load->model('student/Internship_model', 'Inm');

        $this->config->load('score_config');
    }

    public function download_academic_file($s_file_name, $s_file_type)
    {
        $mbo_semester_active = $this->Smm->get_active_semester();

        $s_dir = '';
        switch ($s_file_type) {
            case 'reff_letter':
                $s_dir = APPPATH.'uploads/academic/'.$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id.'/ref_letter/'.$s_file_name;
                break;

            case 'internship_letter':
                $s_dir = APPPATH.'uploads/academic/'.$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id.'/internship_letter/'.$s_file_name;
                break;

            case 'german_letter':
                $s_dir = APPPATH.'uploads/academic/'.$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id.'/german_letter/'.$s_file_name;
                break;
                
            case 'temporary_graduation':
                $s_dir = APPPATH.'uploads/academic/'.$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id.'/temporary_graduation_letter/'.$s_file_name;
                break;

            case 'english_medium_letter':
                $s_dir = APPPATH.'uploads/academic/'.$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id.'/english_medium_instruction/'.$s_file_name;
                break;
            
            default:
                break;
        }
        
		if(!file_exists($s_dir)){
			return show_404();
		}
		else{
			$a_path_info = pathinfo($s_dir);
			header('Content-Disposition: attachment; filename='.urlencode($s_file_name));
			readfile( $s_dir );
			exit;
		}
    }

    function generate_evaluation_result($a_data) {
        $s_template = APPPATH."uploads/templates/admission/Surat Penerimaan ns.docx";
        $s_file_path = APPPATH.'uploads/'.$a_data['personal_data_id'].'/evaluation_result/';
        $s_filename = 'ET_Result_'. str_replace(' ', '_', $a_data['personal_data_name']).'.docx';

        if(!file_exists($s_file_path)) {
            mkdir($s_file_path, 0777, TRUE);
        }

        $s_academic_year = $a_data['academic_year_id'].'-'.(intval($a_data['academic_year_id']) + 1);
        $s_student_name = $a_data['personal_data_name'];
        $s_student_name_regular = ucwords(strtolower($a_data['personal_data_name']));
        $s_student_name_upper = strtoupper($a_data['personal_data_name']);
        $s_highschool = $a_data['institution_name'];
        $s_place_of_birth = $a_data['personal_data_place_of_birth'];
        $s_date_of_birth = date('Y-m-d', strtotime($a_data['personal_data_date_of_birth']));
        $s_study_program = $a_data['study_program_name'];
        $s_current_date = date('d F Y');
        $year = $a_data['academic_year_id'];

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->getSettings()->setHideGrammaticalErrors(true);
        $phpWord->getSettings()->setHideSpellingErrors(true);
        $o_word = new TemplateProcessor($s_template);

        $o_word->setValue('academic_year', $s_academic_year);
        $o_word->setValue('candidate_name', $s_student_name_upper);
        $o_word->setValue('highschool', $s_highschool);
        $o_word->setValue('place_birth', $s_place_of_birth);
        $o_word->setValue('date_birth', $s_date_of_birth);
        $o_word->setValue('study_program', $s_study_program);
        $o_word->setValue('current_date', $s_current_date);
        $o_word->setValue('year', $year);

        $o_word->saveAs($s_file_path.$s_filename);
        return [
            'path' => $s_file_path,
            'file' => $s_filename
        ];
    }

    public function generate_ijazah($a_data)
    {
        $s_student_name = ucwords(strtolower($a_data['student_name']));
        $s_student_name_caps = strtoupper($a_data['student_name']);
        $mba_faculty_data = $this->General->get_where('ref_faculty', ['faculty_id' => $a_data['faculty_id']]);
        $o_faculty = $mba_faculty_data[0];
        
        $a_template_file = [
            [
                'key' => 'nd_vice_rector_version',
                'template' => APPPATH."uploads/templates/academic/ijazah/template_ijazah_ND.docx",
                'filename' => $a_data['prodi_abbreviation'].' - Ijazah ND '.$s_student_name.' (Vice Rector).docx'
            ],
            [
                'key' => 'nd_deans_version',
                'template' => APPPATH."uploads/templates/academic/ijazah/template_ijazah_ND.docx",
                'filename' => $a_data['prodi_abbreviation'].' - Ijazah ND '.$s_student_name.' (Deans).docx'
            ],
        ];
        
        $s_file_path = APPPATH.'uploads/'.$a_data['personal_data_id'].'/ijazah/';

        if ((isset($a_data['program'])) AND ($a_data['program'] == 'ijd')) {
            $a_template_file = [
                [
                    'key' => 'ijd_general',
                    'template' => APPPATH."uploads/templates/academic/ijazah/template_ijazah_IJD.docx",
                    'filename' => $a_data['prodi_abbreviation'].' - Ijazah IJD '.$s_student_name.'.docx'
                ],
                [
                    'key' => 'ijd_ver2',
                    'template' => APPPATH."uploads/templates/academic/ijazah/xx - Entwurf Urkunde Ver 1.docx",
                    'filename' => $a_data['prodi_abbreviation'].' - Ijazah IJD '.$s_student_name.' Entwurf Urkunde Ver 1.docx'
                ],
                [
                    'key' => 'ijd_ver3',
                    'template' => APPPATH."uploads/templates/academic/ijazah/xx - Entwurf Urkunde Ver 2.docx",
                    'filename' => $a_data['prodi_abbreviation'].' - Ijazah IJD '.$s_student_name.' Entwurf Urkunde Ver 2.docx'
                ],
            ];
        }
        else if ((isset($a_data['program'])) AND ($a_data['program'] == 'nd')) {
            if ($a_data['faculty_id'] == '51e2f6ff-c394-44c1-8658-7bd9dd46c654') {
                array_push($a_template_file, [
                    'key' => 'nd_deans_ls_eng_version',
                    'template' => APPPATH."uploads/templates/academic/ijazah/template_ijazah_ND.docx",
                    'filename' => $a_data['prodi_abbreviation'].' - Ijazah ND '.$s_student_name.' (Deans-LS-ENG).docx'
                ]);

                array_push($a_template_file, [
                    'key' => 'nd_vice_rector_ls_eng_version',
                    'template' => APPPATH."uploads/templates/academic/ijazah/template_ijazah_ND.docx",
                    'filename' => $a_data['prodi_abbreviation'].' - Ijazah ND '.$s_student_name.' (Vice Rector-LS-ENG).docx'
                ]);
            }
        }

        if(!file_exists($s_file_path)) {
            mkdir($s_file_path, 0777, TRUE);
        }

        $mba_rector = $this->General->get_where('ref_department', ['department_id' => 5]);
        $s_rector_name = '';
        $s_rector_email = '';
        if (($mba_rector) AND (!is_null($mba_rector[0]->employee_id))) {
            $mbo_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_rector[0]->employee_id))[0];
            $s_rector_name = $this->Pdm->retrieve_title($mbo_rector_data->personal_data_id);
            $s_rector_email = $mbo_rector_data->employee_email;
        }

        $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
        $s_vice_rector_name = '';
        $s_vice_rector_email = '';
        if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
            $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
            $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
            $s_vice_rector_email = $mbo_vice_rector_data->employee_email;
        }

        $s_deans_name = '';
        $s_hod_name = '';
        
        if (!is_null($a_data['deans_id']) AND ($a_data['deans_id'] != '')) {
            $s_deans_name = $this->Pdm->retrieve_title($a_data['deans_id']);
        }
        
        if (!is_null($a_data['head_of_department_id']) AND ($a_data['head_of_department_id'] != '')) {
            $s_hod_name = $this->Pdm->retrieve_title($a_data['head_of_department_id']);
        }

        $mbo_personal_data_document = $this->File_manager->get_files($a_data['personal_data_id'], '0bde3152-5442-467a-b080-3bb0088f6bac');
        if ($mbo_personal_data_document) {
            $s_image_path = APPPATH.'uploads/'.$a_data['personal_data_id'].'/'.$mbo_personal_data_document[0]->document_requirement_link;
            if(!file_exists($s_image_path)){
                $s_image_path = APPPATH.'uploads/templates/academic/ijazah/silhouette.png';
            }
        }
        else {
            $s_image_path = APPPATH.'uploads/templates/academic/ijazah/silhouette.png';
        }

        $faculty_name_feeder_footer = '';
        $faculty_name_footer = '';
        // print json_encode($a_template_file);exit;

        $s_prodi = $a_data['prodi_name'];
        $a_file_generated = [];
        foreach ($a_template_file as $a_template) {
            $s_key = $a_template['key'];
            $s_template = $a_template['template'];
            $s_filename = $a_template['filename'];

            if (!empty($a_data['konsentrasi'])) {
                if (in_array($s_key, ['nd_vice_rector_version', 'nd_deans_version', 'nd_deans_ls_eng_version', 'nd_vice_rector_ls_eng_version'])) {
                    $s_template = APPPATH."uploads/templates/academic/ijazah/template_ijazah_ND_concentration.docx";
                }
            }
            
            if (!empty($a_data['concentration'])) {
                if (in_array($s_key, ['nd_vice_rector_version', 'nd_deans_version', 'nd_deans_ls_eng_version', 'nd_vice_rector_ls_eng_version'])) {
                    $s_template = APPPATH."uploads/templates/academic/ijazah/template_ijazah_ND_concentration.docx";
                }
            }

            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            $o_word = new TemplateProcessor($s_template);

            $a_image_path = array(
                'path' => $s_image_path,
                'width' => 151,
                'height' => 227
            );

            if ($s_key == 'nd_deans_version') {
                if (!is_null($a_data['deans_id']) AND ($a_data['deans_id'] != '')) {
                    $s_deans_name = $this->Pdm->retrieve_title($a_data['deans_id']);
                }
                $faculty_name_feeder_footer = 'Dekan '.$a_data['faculty_name_feeder'];
                $faculty_name_footer = 'Dean of '.$a_data['faculty_name'];
            }
            else if ($s_key == 'nd_vice_rector_version') {
                $s_deans_name = $s_vice_rector_name;
                $faculty_name_feeder_footer = 'Wakil Rektor Bidang Akademik';
                $faculty_name_footer = 'Vice Rector Academic';
            }
            else if ($s_key == 'nd_deans_ls_eng_version') {
                if (!is_null($a_data['deans_id']) AND ($a_data['deans_id'] != '')) {
                    $s_deans_name = $this->Pdm->retrieve_title($a_data['deans_id']);
                }
                $mba_faculty_data = $this->General->get_where('ref_faculty', ['faculty_id' => '301a3e19-348d-4398-b640-c9d2acc491fa']);
                $o_faculty = $mba_faculty_data[0];
                $faculty_name_feeder_footer = 'Dekan '.$o_faculty->faculty_name_feeder;
                $faculty_name_footer = 'Dean of '.$o_faculty->faculty_name;
                $a_data['faculty_name_feeder'] = $o_faculty->faculty_name_feeder;
                $a_data['faculty_name'] = $o_faculty->faculty_name;
            }
            else if ($s_key == 'nd_vice_rector_ls_eng_version') {
                $mba_faculty_data = $this->General->get_where('ref_faculty', ['faculty_id' => '301a3e19-348d-4398-b640-c9d2acc491fa']);
                $o_faculty = $mba_faculty_data[0];
                $s_deans_name = $s_vice_rector_name;
                $faculty_name_feeder_footer = 'Wakil Rektor Bidang Akademik';
                $faculty_name_footer = 'Vice Rector Academic';
                $a_data['faculty_name_feeder'] = $o_faculty->faculty_name_feeder;
                $a_data['faculty_name'] = $o_faculty->faculty_name;
            }

            $degree_ijd_name = (empty($a_data['degree_ijd_name'])) ? '' : $a_data['degree_ijd_name'];
            $degree_ijd_abbreviation = (empty($a_data['degree_ijd_abbreviation'])) ? '' : $a_data['degree_ijd_abbreviation'];

            $o_word->setValue('pin', $a_data['pin_number']);
            $o_word->setValue('certificatenumber', '1'.$a_data['pin_number']);
            $o_word->setValue('student_name', $s_student_name);
            $o_word->setValue('call_gender', $a_data['call_gender']);
            $o_word->setValue('date_of_birth_indo', $a_data['date_birth_indo']);
            $o_word->setValue('date_of_birth', date('d F Y', strtotime($a_data['date_birth'])));
            $o_word->setValue('place_of_birth', $a_data['place_birth']);
            $o_word->setValue('birth_country', $a_data['country_birth']);
            $o_word->setValue('student_number', $a_data['student_number']);
            $o_word->setValue('student_batch', $a_data['batch'].'/'.(intval($a_data['batch']) + 1));
            $o_word->setValue('prodi_name_feeder', $a_data['prodi_name_feeder']);
            $o_word->setValue('prodi_name', $a_data['prodi_name']);
            $o_word->setValue('prodi_name_body', $a_data['prodi_name']);
            $o_word->setValue('konsentrasi', $a_data['konsentrasi']);
            $o_word->setValue('concentration', $a_data['concentration']);
            $o_word->setValue('graduation_date_indo', $a_data['gradute_date_indo']);
            $o_word->setValue('graduation_date', date('d F Y', strtotime($a_data['gradute_date'])));
            $o_word->setValue('degree_title', $a_data['degree_name']);
            $o_word->setValue('ijd_degree_title', $degree_ijd_name);
            $o_word->setValue('degree_abbr', $a_data['degree_abbreviation']);
            $o_word->setValue('ijd_deggree_abbr', $degree_ijd_abbreviation);
            $o_word->setValue('current_date_indo', $a_data['current_date_indo']);
            $o_word->setValue('current_date', $a_data['current_date']);
            $o_word->setValue('dean_name', $s_deans_name);
            $o_word->setValue('faculty_name_feeder', $a_data['faculty_name_feeder']);
            $o_word->setValue('faculty_name_feeder_footer', $faculty_name_feeder_footer);
            $o_word->setValue('faculty_name', $a_data['faculty_name']);
            $o_word->setValue('faculty_name_footer', $faculty_name_footer);
            $o_word->setValue('sk_prodi', $a_data['sk_prodi']);
            $o_word->setValue('rector_name', $s_rector_name);
            $o_word->setImageValue('imageformat', $a_image_path);
            
            $o_word->saveAs($s_file_path.$s_filename);
            array_push($a_file_generated, $s_filename);
        }
        
        // if ((isset($a_data['program'])) AND ($a_data['program'] == 'nd1')) {
        //     $s_file_name = $a_data['prodi_abbreviation'].' - Ijazah ND '.$s_student_name.'-Deans.docx';
        // }
        // else if ((isset($a_data['program'])) AND ($a_data['program'] == 'nd2')) {
        //     $s_file_name = $a_data['prodi_abbreviation'].' - Ijazah ND '.$s_student_name.'-Vice Rector.docx';
        // }
        // else if ((isset($a_data['program'])) AND ($a_data['program'] == 'ijd')) {
        //     $s_template = APPPATH."uploads/templates/academic/ijazah/template_ijazah_IJD.docx";
        //     $s_file_name = ;
        // }
        // else if ((isset($a_data['program'])) AND ($a_data['program'] == 'ijd1')) {
        //     $s_template = APPPATH."uploads/templates/academic/ijazah/xx - Entwurf Urkunde Ver 1.docx";
        //     $s_file_name = ;
        // }
        // else if ((isset($a_data['program'])) AND ($a_data['program'] == 'ijd2')) {
        //     $s_template = APPPATH."uploads/templates/academic/ijazah/xx - Entwurf Urkunde Ver 2.docx";
        //     $s_file_name = ;
        // }

        // shell_exec('/usr/bin/soffice --headless --convert-to pdf "' . $s_file_path.$s_file_name . '" --outdir ' . $s_file_path);
        // $s_pdf_name = str_replace('.docx', '.pdf', $s_file_name);

        if (count($a_file_generated) > 0) {
            $a_return = ['code' => 0, 'files' => $a_file_generated, 'count' => count($a_file_generated)];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'no file generated!'];
        }
        return $a_return;
    }

    public function tes_page()
    {
        $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
        $zip->open("/home/portal/applications/portal2/uploads/staff/B/47013ff8-89df-11ef-8f45-0068eb6957a0/2021/request_letter/Assignment Letter_Study Program_Lecturing Assignment (Ghani Harahap)_temp.docx");
        $zipfiles = $zip->getFromName("docProps/app.xml");
        $xml = new \DOMDocument();
        $xml->loadXML($zipfiles);
        $zip->close();
        $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;
        // preg_match("/\<Pages>(.*)\<\/Pages\>/", $zip->getFromName("docProps/app.xml"), $var);
        // $i_count_pages = $var[0];
        print('<pre>');
        var_dump($xml->documentElement);exit;
    }

    public function generate_key_letter_template($a_data)
    {
        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);

        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);

        if (($mba_user_employee) AND ($mba_template_data)) {
            $o_user_employee = $mba_user_employee[0];
            $o_template_data = $mba_template_data[0];
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);

            $mba_letter_data = $this->General->get_where('dt_letter_number', ['letter_number_result' => $a_data['letter_number']]);
            $s_letter_year = ($mba_letter_data) ? $mba_letter_data[0]->letter_year : date('Y');
            $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
            $s_filename = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
            // $s_filename = $s_file_name.'.docx';

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $mba_rector = $this->General->get_where('ref_department', ['department_id' => 5]);
            $s_rector_name = '';
            $s_rector_email = '';
            if (($mba_rector) AND (!is_null($mba_rector[0]->employee_id))) {
                $mbo_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_rector[0]->employee_id))[0];
                $s_rector_name = $this->Pdm->retrieve_title($mbo_rector_data->personal_data_id);
                $s_rector_email = $mbo_rector_data->employee_email;
            }

            $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
            $s_vice_rector_name = '';
            $s_vice_rector_email = '';
            if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
                $s_vice_rector_email = $mbo_vice_rector_data->employee_email;
            }

            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            $o_word = new TemplateProcessor($s_template);

            $o_word->setValue('key_number', $a_data['letter_number']);
            $o_word->setValue('key_date', date('d F Y'));
            $o_word->setValue('rector_name', $s_rector_name);
            $o_word->setValue('rector_email', $s_rector_email);
            $o_word->setValue('vice_academic_rector_name', $s_vice_rector_name);
            $o_word->setValue('vice_academic_rector_email', $s_vice_rector_email);
            
            $o_word->saveAs($s_file_path.$s_filename);
            $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'];
        }

        return $a_return;
    }

    public function generate_application_letter_internship_student($a_data)
    {
        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);
        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);

        $mba_student_data = $this->Stm->get_student_filtered([
            'ds.student_id' => $a_data['student_id']
        ]);

        if (($mba_student_data) AND ($mba_user_employee) AND ($mba_template_data)) {
            $o_student = $mba_student_data[0];
            $o_user_employee = $mba_user_employee[0];
            $o_template_data = $mba_template_data[0];
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);

            $mbo_hod_data = false;
            $s_hod_name = '';
            $s_hod_email = '';
            $s_prodi_name = 'Ad interim Rector';
            if(!is_null($o_student->head_of_study_program_id)) {
                $mbo_hod_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $o_student->head_of_study_program_id))[0];
                $s_hod_name = $this->Pdm->retrieve_title($o_student->head_of_study_program_id);
                $s_hod_email = $mbo_hod_data->employee_email;
                $s_prodi_name = 'Head of Study Program of '.$o_student->study_program_name;
            }

            $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
            $s_vice_rector_name = '';
            $s_vice_rector_email = '';
            if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
                $s_vice_rector_email = $mbo_vice_rector_data->employee_email;
            }
            
            if (!$mbo_hod_data) {
                $s_hod_name = $s_vice_rector_name;
                $s_hod_email = $s_vice_rector_email;
            }
            
            $mba_rector = $this->General->get_where('ref_department', ['department_id' => 5]);
            $s_rector_name = '';
            $s_rector_email = '';
            if (($mba_rector) AND (!is_null($mba_rector[0]->employee_id))) {
                $mbo_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_rector[0]->employee_id))[0];
                $s_rector_name = $this->Pdm->retrieve_title($mbo_rector_data->personal_data_id);
                $s_rector_email = $mbo_rector_data->employee_email;
            }

            $mba_letter_data = $this->General->get_where('dt_letter_number', ['letter_number_result' => $a_data['letter_number']]);
            $s_letter_year = ($mba_letter_data) ? $mba_letter_data[0]->letter_year : date('Y');

            $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
            $s_file_name = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
            $a_file_name = explode('.', $s_file_name);
            unset($a_file_name[count($a_file_name) - 1]);
            $s_file_name = implode(' ', $a_file_name).'('.ucwords(strtolower($o_student->personal_data_name)).')';
            $s_filename = $s_file_name.'.docx';
            $s_filename_temp = $s_file_name.'_temp.docx';

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_company_address_internhip = (!empty($a_data['company_address'])) ? str_replace("\r\n", "<w:br/>", $a_data['company_address']) : '...................';

            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            $o_word = new TemplateProcessor($s_template);
            $o_word->setValue('key_number', $a_data['letter_number']);
            $o_word->setValue('key_date', date('d F Y'));
            $o_word->setValue('name', ((!empty($a_data['spv_name'])) ? $a_data['spv_name'] : '...................'));
            $o_word->setValue('name_occupation', ((!empty($a_data['spv_occupation'])) ? $a_data['spv_occupation'] : '...................'));
            $o_word->setValue('company_name', ((!empty($a_data['company_name'])) ? $a_data['company_name'] : '...................'));
            $o_word->setValue('company_address', $s_company_address_internhip);
            $o_word->setValue('student_name', strtoupper($o_student->personal_data_name));
            $o_word->setValue('student_number', strtoupper($o_student->student_number));
            $o_word->setValue('prodi_name', $o_student->study_program_name);
            $o_word->setValue('student_email', strtolower($o_student->student_email));
            $o_word->setValue('student_cellular', strtolower($o_student->personal_data_cellular));
            $o_word->setValue('call_gender', (($o_student->personal_data_gender == 'M') ? 'He' : 'She'));
            $o_word->setValue('start_month', ((!empty($a_data['start_date'])) ? $a_data['start_date'] : '...................'));
            $o_word->setValue('end_month', ((!empty($a_data['end_date'])) ? $a_data['end_date'] : '...................'));
            $o_word->setValue('rector_name', $s_rector_name);
            $o_word->setValue('rector_email', $s_rector_email);
            $o_word->setValue('hod_name', $s_hod_name);
            $o_word->setValue('prodi_name', $s_prodi_name);
            $o_word->setValue('hod_email', $s_hod_email);
            $o_word->setValue('page', '1');
            $o_word->saveAs($s_file_path.$s_filename_temp);
            // print('<pre>');
            // var_dump($s_file_path.$s_filename_temp);exit;

            $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
            $zip->open($s_file_path.$s_filename_temp);
            $zipfiles = $zip->getFromName("docProps/app.xml");
            $xml = new \DOMDocument();
            $xml->loadXML($zipfiles);
            $zip->close();
            $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;
            // var_dump($i_count);exit;

            $phpWords = new \PhpOffice\PhpWord\PhpWord();
            $o_words = new TemplateProcessor($s_file_path.$s_filename_temp);
            $o_words->setValue('num_pages', $i_count);
            $o_words->saveAs($s_file_path.$s_filename);
            unlink($s_file_path.$s_filename_temp);

            $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'];
        }

        return $a_return;
    }

    public function generate_assignment_letter_examiner_thesis($a_data)
    {
        $mba_is_examiner_semester = $this->Tsm->is_advisor_examiner_defense([
            'ta.personal_data_id' => $a_data['personal_data_id'],
            'td.academic_year_id' => $a_data['academic_year_id'],
            'td.semester_type_id' => $a_data['semester_type_id']
        ], 'examiner');

        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);

        $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $a_data['personal_data_id']]);
        $mba_study_program_data = $this->Spm->get_study_program($a_data['study_program_id']);
        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);

        if (($mba_user_employee) AND ($mba_personal_data) AND ($mba_template_data)) {
            $o_personal_target = $mba_personal_data[0];
            $o_prodi = $mba_study_program_data[0];
            $o_user_employee = $mba_user_employee[0];
            $o_template_data = $mba_template_data[0];
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);

            $mbo_hod_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mba_study_program_data[0]->head_of_study_program_id))[0];
            $s_hod_name = $this->Pdm->retrieve_title($mba_study_program_data[0]->head_of_study_program_id);
            $mbo_dean_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mba_study_program_data[0]->deans_id))[0];
            $s_dean_name = $this->Pdm->retrieve_title($mba_study_program_data[0]->deans_id);
            $mba_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $a_data['semester_type_id']]);
            $s_letter_year = (isset($a_data['letter_year'])) ? $a_data['letter_year'] : date('Y');
            $mba_employee_target = $this->Emm->get_employee_data(array('em.personal_data_id' => $a_data['personal_data_id']));

            $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
            $s_vice_rector_name = '';
            if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
            }

            $mba_is_examiner_semester = $this->Tsm->is_advisor_examiner_defense([
                'ta.personal_data_id' => $this->session->userdata('user'),
                'td.academic_year_id' => $a_data['academic_year_id'],
                'td.semester_type_id' => $a_data['semester_type_id']
            ], 'examiner');
            $a_student_thesis_data = [];
            $i_count_sks = 0;
            if ($mba_is_examiner_semester) {
                $no = 1;
                foreach ($mba_is_examiner_semester as $o_student) {
                    $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $o_student->student_id]);
                    $mba_student_get_thesis_subject = $this->Scm->get_score_like_subject_name([
                        'sc.student_id' => $o_student->student_id
                    ], 'thesis');
                    $s_defense_time = '';
                    if ((!is_null($o_student->thesis_defense_time_start)) AND (!is_null($o_student->thesis_defense_time_end))) {
                        $s_defense_time = date('H:i', strtotime($o_student->thesis_defense_time_start)).' - '.date('H:i', strtotime($o_student->thesis_defense_time_start));
                    }

                    $i_count_sks += ($mba_student_get_thesis_subject) ? $mba_student_get_thesis_subject[0]->curriculum_subject_credit : 6;
                    array_push($a_student_thesis_data, [
                        'no' => $no++,
                        'student_name' => ucfirst(strtolower($mba_student_data[0]->personal_data_name)),
                        'student_number' => $mba_student_data[0]->student_number,
                        'study_program' => $mba_student_data[0]->study_program_name,
                        'defense_room' => (!is_null($o_student->thesis_defense_room)) ? $o_student->thesis_defense_room : '',
                        'defense_date' => (!is_null($o_student->thesis_defense_date)) ? date('d F Y', strtotime($o_student->thesis_defense_date)) : '',
                        'defense_time' => $s_defense_time
                    ]);
                }
            }

            $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
            $s_file_name = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
            $a_file_name = explode('.', $s_file_name);
            unset($a_file_name[count($a_file_name) - 1]);
            $s_file_name = implode(' ', $a_file_name).'('.ucwords(strtolower($o_personal_target->personal_data_name)).'-'.$a_data['academic_year_id'].$a_data['semester_type_id'].')';
            $s_filename = $s_file_name.'.docx';
            // $s_filename = str_replace(' ', '_', $s_filename);
            $s_filename_temp = $s_file_name.'_temp.docx';

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_employee_name = $this->Pdm->retrieve_title($o_personal_target->personal_data_id);
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            $o_word = new TemplateProcessor($s_template);
            $o_word->setValue('key_number', $a_data['letter_number']);
            $o_word->setValue('key_date', $a_data['letter_date']);
            $o_word->setValue('dean_call_gender', (($mbo_dean_data->personal_data_gender == 'M') ? 'His' : 'Her'));
            $o_word->setValue('lecturer_name', $s_employee_name);
            $o_word->setValue('faculty_name', $o_prodi->faculty_name);
            $o_word->setValue('faculty_name_indo', $o_prodi->faculty_name_feeder);
            $o_word->setValue('dean_sk', $o_prodi->faculty_deans_sk_number);
            $o_word->setValue('semester_type', ucwords(strtolower($mba_semester_type_data[0]->semester_type_name)));
            $o_word->setValue('semester_tipe', ucwords(strtolower($mba_semester_type_data[0]->semester_type_indonesian_name)));
            $o_word->setValue('academic_year', $a_data['academic_year_id'].'/'.(intval($a_data['academic_year_id']) + 1));
            if ($mba_employee_target) {
                $o_word->setValue('lecturer_nidn', $mba_employee_target[0]->employee_lecturer_number);
                $o_word->setValue('lecturer_nip', $mba_employee_target[0]->employee_id_number);
            }
            else {
                $o_word->setValue('lecturer_nidn', '');
                $o_word->setValue('lecturer_nip', '');
            }
            $o_word->setValue('dean_name', $s_dean_name);
            $o_word->setValue('credit_total', $i_count_sks);
            $o_word->cloneRowAndSetValues('no', $a_student_thesis_data);
            $o_word->saveAs($s_file_path.$s_filename_temp);

            $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
            $zip->open($s_file_path.$s_filename_temp);
            $zipfiles = $zip->getFromName("docProps/app.xml");
            $xml = new \DOMDocument();
            $xml->loadXML($zipfiles);
            $zip->close();
            $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;

            $phpWords = new \PhpOffice\PhpWord\PhpWord();
            $o_words = new TemplateProcessor($s_file_path.$s_filename_temp);
            $o_words->setValue('num_pages', $i_count);
            $o_words->saveAs($s_file_path.$s_filename);
            unlink($s_file_path.$s_filename_temp);

            $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'Data not found!'];
        }
        
        return $a_return;
    }

    public function generate_assignment_letter_advisor_internship($a_data)
    {
        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);

        $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $a_data['personal_data_id']]);
        $mba_study_program_data = $this->Spm->get_study_program($a_data['study_program_id'], false);
        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);

        if (($mba_user_employee) AND ($mba_personal_data) AND ($mba_template_data)) {
            $o_personal_target = $mba_personal_data[0];
            $o_prodi = $mba_study_program_data[0];
            $o_user_employee = $mba_user_employee[0];
            $o_template_data = $mba_template_data[0];
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);

            // print('<pre>');var_dump($o_prodi);exit;
            $mbo_hod_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $o_prodi->head_of_study_program_id))[0];
            $s_hod_name = $this->Pdm->retrieve_title($o_prodi->head_of_study_program_id);
            $mbo_dean_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $o_prodi->deans_id))[0];
            $s_dean_name = $this->Pdm->retrieve_title($o_prodi->deans_id);
            $mba_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $a_data['semester_type_id']]);
            $s_letter_year = (isset($a_data['letter_year'])) ? $a_data['letter_year'] : date('Y');
            $mba_employee_target = $this->Emm->get_employee_data(array('em.personal_data_id' => $a_data['personal_data_id']));

            $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
            $s_vice_rector_name = '';
            if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
            }

            $a_student_id_push = [];
            $mba_student_internship = false;
            $mba_is_lect_internship = $this->Cgm->get_class_master_filtered([
                'cml.employee_id' => $this->session->userdata('employee_id'),
                'cm.academic_year_id' => $a_data['academic_year_id'],
                'cm.semester_type_id' => $a_data['semester_type_id']
            ]);
            if ($mba_is_lect_internship) {
                foreach ($mba_is_lect_internship as $o_class_master) {
                    // print($o_class_master->subject_name.'<br>');
                    if (strpos(strtolower($o_class_master->subject_name), 'internship') !== false) {
                        // $mba_score_data = $this->General->get_where('dt_score', [
                        //     'class_master_id' => $o_class_master->class_master_id,
                        //     'score_approval' => 'approved'
                        // ]);
                        // $mba_score_data = $this->Scm->get_studentscore_like_subject_name([
                        //     'sc.class_master_id' => $o_class_master->class_master_id,
                        //     'sc.score_approval' => 'approved'
                        // ], 'internship');
                        $mba_score_data = $this->Scm->get_student_by_score(['sc.class_master_id' => $o_class_master->class_master_id, 'sc.score_approval' => 'approved']);
                        if ($mba_score_data) {
                            $mba_student_internship = [];
                            foreach ($mba_score_data as $o_score) {
                                if (!in_array($o_score->student_id, $a_student_id_push)) {
                                    array_push($a_student_id_push, $o_score->student_id);
                                    array_push($mba_student_internship, $o_score);
                                }
                            }
                            $mba_student_internship = array_values($mba_student_internship);
                        }
                    }
                }
            }
            // exit;
            $a_student_table_data = [];
            if ($mba_student_internship) {
                $no = 1;
                foreach ($mba_student_internship as $o_student) {
                    $mba_student_internship_data = $this->Inm->get_internship_student([
                        'si.student_id' => $o_student->student_id
                    ]);
                    $s_institution_name = ($mba_student_internship_data) ? $mba_student_internship_data[0]->institution_name : '';
                    array_push($a_student_table_data, [
                        'no' => $no++,
                        'student_number' => $o_student->student_number,
                        'student_name' => ucfirst(strtolower($o_student->personal_data_name)),
                        'study_program' => $o_student->study_program_name,
                        'department_name' => $s_institution_name
                    ]);
                }
            }
            // print('<pre>');var_dump($a_student_table_data);exit;

            $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
            $s_file_name = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
            $a_file_name = explode('.', $s_file_name);
            unset($a_file_name[count($a_file_name) - 1]);
            $s_file_name = implode(' ', $a_file_name).'('.ucwords(strtolower($o_personal_target->personal_data_name)).'-'.$a_data['academic_year_id'].$a_data['semester_type_id'].')';
            $s_filename = $s_file_name.'.docx';
            // $s_filename = str_replace(' ', '_', $s_filename);
            $s_filename_temp = $s_file_name.'_temp.docx';

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_employee_name = $this->Pdm->retrieve_title($o_personal_target->personal_data_id);
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            $o_word = new TemplateProcessor($s_template);
            $o_word->setValue('key_number', $a_data['letter_number']);
            $o_word->setValue('key_date', $a_data['letter_date']);
            $o_word->setValue('dean_call_gender', (($mbo_dean_data->personal_data_gender == 'M') ? 'His' : 'Her'));
            $o_word->setValue('lecturer_name', $s_employee_name);
            $o_word->setValue('faculty_name', $o_prodi->faculty_name);
            $o_word->setValue('faculty_name_indo', $o_prodi->faculty_name_feeder);
            $o_word->setValue('dean_sk', $o_prodi->faculty_deans_sk_number);
            $o_word->setValue('semester_type', ucwords(strtolower($mba_semester_type_data[0]->semester_type_name)));
            $o_word->setValue('semester_tipe', ucwords(strtolower($mba_semester_type_data[0]->semester_type_indonesian_name)));
            $o_word->setValue('academic_year', $a_data['academic_year_id'].'/'.(intval($a_data['academic_year_id']) + 1));
            if ($mba_employee_target) {
                $o_word->setValue('lecturer_nidn', $mba_employee_target[0]->employee_lecturer_number);
                $o_word->setValue('lecturer_nip', $mba_employee_target[0]->employee_id_number);
            }
            else {
                $o_word->setValue('lecturer_nidn', '');
                $o_word->setValue('lecturer_nip', '');
            }
            $o_word->setValue('dean_name', $s_dean_name);
            $o_word->setValue('credit_total', $i_count_sks);
            $o_word->cloneRowAndSetValues('no', $a_student_table_data);
            $o_word->saveAs($s_file_path.$s_filename_temp);

            $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
            $zip->open($s_file_path.$s_filename_temp);
            $zipfiles = $zip->getFromName("docProps/app.xml");
            $xml = new \DOMDocument();
            $xml->loadXML($zipfiles);
            $zip->close();
            $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;

            $phpWords = new \PhpOffice\PhpWord\PhpWord();
            $o_words = new TemplateProcessor($s_file_path.$s_filename_temp);
            $o_words->setValue('num_pages', $i_count);
            $o_words->saveAs($s_file_path.$s_filename);
            unlink($s_file_path.$s_filename_temp);

            $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'Data not found!'];
        }
        
        return $a_return;
    }

    public function generate_assignment_letter_advisor_thesis($a_data)
    {
        $mba_is_advisor_semester = $this->Tsm->is_advisor_examiner_defense([
            'ta.personal_data_id' => $a_data['personal_data_id'],
            'td.academic_year_id' => $a_data['academic_year_id'],
            'td.semester_type_id' => $a_data['semester_type_id']
        ], 'advisor');

        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);

        $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $a_data['personal_data_id']]);
        $mba_study_program_data = $this->Spm->get_study_program($a_data['study_program_id']);
        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);

        if (($mba_user_employee) AND ($mba_personal_data) AND ($mba_template_data)) {
            $o_personal_target = $mba_personal_data[0];
            $o_prodi = $mba_study_program_data[0];
            $o_user_employee = $mba_user_employee[0];
            $o_template_data = $mba_template_data[0];
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);

            $mbo_hod_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mba_study_program_data[0]->head_of_study_program_id))[0];
            $s_hod_name = $this->Pdm->retrieve_title($mba_study_program_data[0]->head_of_study_program_id);
            $mbo_dean_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mba_study_program_data[0]->deans_id))[0];
            $s_dean_name = $this->Pdm->retrieve_title($mba_study_program_data[0]->deans_id);
            $mba_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $a_data['semester_type_id']]);
            $s_letter_year = (isset($a_data['letter_year'])) ? $a_data['letter_year'] : date('Y');
            $mba_employee_target = $this->Emm->get_employee_data(array('em.personal_data_id' => $a_data['personal_data_id']));

            $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
            $s_vice_rector_name = '';
            if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
            }

            $mba_is_advisor_semester = $this->Tsm->is_advisor_examiner_defense([
                'ta.personal_data_id' => $this->session->userdata('user'),
                'td.academic_year_id' => $a_data['academic_year_id'],
                'td.semester_type_id' => $a_data['semester_type_id']
            ], 'advisor');
            $a_student_thesis_data = [];
            $i_count_sks = 0;
            if ($mba_is_advisor_semester) {
                $no = 1;
                foreach ($mba_is_advisor_semester as $o_student) {
                    $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $o_student->student_id]);
                    $mba_student_get_thesis_subject = $this->Scm->get_score_like_subject_name([
                        'sc.student_id' => $o_student->student_id
                    ], 'thesis');

                    $i_count_sks += ($mba_student_get_thesis_subject) ? $mba_student_get_thesis_subject[0]->curriculum_subject_credit : 6;
                    array_push($a_student_thesis_data, [
                        'no' => $no++,
                        'student_number' => $mba_student_data[0]->student_number,
                        'student_name' => ucfirst(strtolower($mba_student_data[0]->personal_data_name)),
                        'thesis_title' => $o_student->thesis_title,
                        'thesis_sks' => ($mba_student_get_thesis_subject) ? $mba_student_get_thesis_subject[0]->curriculum_subject_credit : 6,
                        'study_program' => $mba_student_data[0]->study_program_name
                    ]);
                }
            }

            $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
            $s_file_name = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
            $a_file_name = explode('.', $s_file_name);
            unset($a_file_name[count($a_file_name) - 1]);
            $s_file_name = implode(' ', $a_file_name).'('.ucwords(strtolower($o_personal_target->personal_data_name)).'-'.$a_data['academic_year_id'].$a_data['semester_type_id'].')';
            $s_filename = $s_file_name.'.docx';
            // $s_filename = str_replace(' ', '_', $s_filename);
            $s_filename_temp = $s_file_name.'_temp.docx';

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_employee_name = $this->Pdm->retrieve_title($o_personal_target->personal_data_id);
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            $o_word = new TemplateProcessor($s_template);
            $o_word->setValue('key_number', $a_data['letter_number']);
            $o_word->setValue('key_date', $a_data['letter_date']);
            $o_word->setValue('dean_call_gender', (($mbo_dean_data->personal_data_gender == 'M') ? 'His' : 'Her'));
            $o_word->setValue('lecturer_name', $s_employee_name);
            $o_word->setValue('faculty_name', $o_prodi->faculty_name);
            $o_word->setValue('faculty_name_indo', $o_prodi->faculty_name_feeder);
            $o_word->setValue('dean_sk', $o_prodi->faculty_deans_sk_number);
            $o_word->setValue('semester_type', ucwords(strtolower($mba_semester_type_data[0]->semester_type_name)));
            $o_word->setValue('semester_tipe', ucwords(strtolower($mba_semester_type_data[0]->semester_type_indonesian_name)));
            $o_word->setValue('academic_year', $a_data['academic_year_id'].'/'.(intval($a_data['academic_year_id']) + 1));
            if ($mba_employee_target) {
                $o_word->setValue('lecturer_nidn', $mba_employee_target[0]->employee_lecturer_number);
                $o_word->setValue('lecturer_nip', $mba_employee_target[0]->employee_id_number);
            }
            else {
                $o_word->setValue('lecturer_nidn', '');
                $o_word->setValue('lecturer_nip', '');
            }
            $o_word->setValue('dean_name', $s_dean_name);
            $o_word->setValue('credit_total', $i_count_sks);
            $o_word->cloneRowAndSetValues('no', $a_student_thesis_data);
            $o_word->saveAs($s_file_path.$s_filename_temp);

            $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
            $zip->open($s_file_path.$s_filename_temp);
            $zipfiles = $zip->getFromName("docProps/app.xml");
            $xml = new \DOMDocument();
            $xml->loadXML($zipfiles);
            $zip->close();
            $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;

            $phpWords = new \PhpOffice\PhpWord\PhpWord();
            $o_words = new TemplateProcessor($s_file_path.$s_filename_temp);
            $o_words->setValue('num_pages', $i_count);
            $o_words->saveAs($s_file_path.$s_filename);
            unlink($s_file_path.$s_filename_temp);

            $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'Data not found!'];
        }
        
        return $a_return;
    }

    public function generate_permohonan_nidn_rektor($a_data)
    {
        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);

        $mba_employee_data = $this->Emm->get_employee_data([
            'em.employee_id' => $a_data['employee_id']
        ]);

        if (($mba_employee_data) AND ($mba_user_employee)) {
            $o_employee = $mba_employee_data[0];
            $o_user_employee = $mba_user_employee[0];
            $mba_employee_department = $this->Emm->get_employee_department(['em.employee_id' => $o_employee->employee_id]);
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);
            $mba_rector_data = $this->General->get_rectorate('rector');
            $mba_vice_rector_data = $this->General->get_rectorate('vice_rector');
            $s_dept_name = '';
            
            if ($mba_employee_department) {
                $mba_prodi = $this->General->get_where('ref_study_program', ['study_program_abbreviation' => $mba_employee_department[0]->department_abbreviation]);
                $s_dept_name = ($mba_prodi) ? $mba_prodi[0]->study_program_name_feeder.' S1' : '';
            }

            $s_rector_name = ($mba_rector_data) ? $mba_rector_data->rector_full_name : '';
            $s_vice_rector_name = ($mba_vice_rector_data) ? $mba_vice_rector_data->rector_full_name : '';
            
            $s_template = APPPATH."uploads/templates/spmi/MISC/Surat Pernyataan Rektor- Permohonan NIDN.docx";
            $s_link_save = date('Y').'/other_letter/';
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_link_save;
            $s_file_name = 'Surat_Pernyataan_Rektor-Permohonan_NIDN_'.str_replace(' ', '_', $o_employee->personal_data_name);
            $s_filename = $s_file_name.'.docx';

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_employee_name = $this->Pdm->retrieve_title($o_employee->personal_data_id);
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            $o_word = new TemplateProcessor($s_template);
            // $o_word->setValue('key_date', $a_data['letter_date']);
            // $o_word->setValue('call_gender', (($o_employee->personal_data_gender == 'M') ? 'His' : 'Her'));
            $o_word->setValue('employee_name', $s_employee_name);
            // $o_word->setValue('employee_nip', $o_employee->employee_id_number);
            // $o_word->setValue('employee_job_title', $o_employee->employee_job_title);
            $o_word->setValue('employee_dept', $s_dept_name);
            // $o_word->setValue('working_year', $interval->y);
            // $o_word->setValue('employee_working_date', $o_employee->employee_join_date);
            $o_word->setValue('rector_name', $s_rector_name);
            // $o_word->setValue('head_hr_email', $mba_hr_data->employee_email);
            $o_word->saveAs($s_file_path.$s_filename);

            $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename), 'savepath' => $s_link_save.$s_filename];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'];
        }

        return $a_return;
    }

    public function test_download()
    {
        $a_data = [
            'template_id' => '23',
            'employee_id' => '1e6547f8-d6ba-4356-b389-8663d78cf11a',
            'letter_number' => 'L/HRD/1309/IULI/XI/2022',
            'academic_year_id' => '2022',
            'semester_type_id' => '1',
            'letter_date' => '2022-11-01',
            'letter_year' => '2022'
        ];

        $a_return = $this->generate_lolos_butuh_letter($a_data);
        print('<pre>');var_dump($a_return);exit;
    }

    public function generate_lolos_butuh_letter($a_data)
    {
        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);

        $mba_employee_data = $this->Emm->get_employee_data([
            'em.employee_id' => $a_data['employee_id']
        ]);

        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);
        if (($mba_employee_data) AND ($mba_user_employee) AND ($mba_template_data)) {
            $o_employee = $mba_employee_data[0];
            $o_user_employee = $mba_user_employee[0];
            $o_template_data = $mba_template_data[0];
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);
            $mba_rector_data = $this->General->get_rectorate('rector');
            $mba_hr_data = $this->General->get_rectorate('human_resource');
            $s_hr_name = ($mba_hr_data) ? $mba_hr_data->rector_full_name : '';

            $s_rector_name = ($mba_rector_data) ? $mba_rector_data->rector_full_name : '';
            $s_rector_nip = ($mba_rector_data) ? $mba_rector_data->employee_id_number : '';
            $s_rector_nidn = ($mba_rector_data) ? $mba_rector_data->employee_lecturer_number : '';
            
            $mba_employee_department = $this->Emm->get_employee_department(['em.employee_id' => $o_employee->employee_id]);
            $s_employee_prodi = '';
            
            if ($mba_employee_department) {
                $mba_prodi = $this->General->get_where('ref_study_program', ['study_program_abbreviation' => $mba_employee_department[0]->department_abbreviation]);
                $s_employee_prodi = ($mba_prodi) ? $mba_prodi[0]->study_program_name : '';
            }

            $s_letter_year = (isset($a_data['letter_year'])) ? $a_data['letter_year'] : date('Y');
            
            $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
            $s_file_name = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
            $a_file_name = explode('.', $s_file_name);
            unset($a_file_name[count($a_file_name) - 1]);
            $s_file_name = implode(' ', $a_file_name).'('.ucwords(strtolower($o_employee->personal_data_name)).'-'.$a_data['academic_year_id'].$a_data['semester_type_id'].')';
            $s_filename = $s_file_name.'.docx';
            // $s_filename = str_replace(' ', '_', $s_filename);
            $s_filename_temp = $s_file_name.'_temp.docx';

            $date = new DateTime($o_employee->employee_join_date);
            $now = new DateTime();
            $interval = $now->diff($date);

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_addess = $this->Pdm->retrieve_address($o_employee->personal_data_id);
            $s_employee_name = $this->Pdm->retrieve_title($o_employee->personal_data_id);
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            $o_word = new TemplateProcessor($s_template);
            // print($s_employee_name);exit;

            $o_word->setValue('key_number', $a_data['letter_number']);
            $o_word->setValue('key_date', $a_data['letter_date']);
            // $o_word->setValue('call_gender', (($o_employee->personal_data_gender == 'M') ? 'His' : 'Her'));
            $o_word->setValue('rector_name', $s_rector_name);
            $o_word->setValue('employee_name', $s_employee_name);
            $o_word->setValue('rector_nip', $s_rector_nip);
            $o_word->setValue('rector_nidn', $s_rector_nidn);
            $o_word->setValue('employee_nip', $o_employee->employee_id_number);
            $o_word->setValue('employee_nidn', (!is_null($o_employee->employee_lecturer_number)) ? $o_employee->employee_lecturer_number.' / ' : '');
            // $o_word->setValue('employee_job_title', (!is_null($o_employee->employee_job_title)) ? $o_employee->employee_job_title : '');
            $o_word->setValue('employee_address', $s_addess);
            $o_word->setValue('employee_prodi', $s_employee_prodi);
            // $o_word->setValue('working_year', $interval->y);
            $o_word->setValue('date_resign', $o_employee->last_date_of_work);
            $o_word->setValue('head_hr', $s_hr_name);
            $o_word->setValue('head_hr_email', $mba_hr_data->employee_email);
            $o_word->saveAs($s_file_path.$s_filename_temp);

            $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
            $zip->open($s_file_path.$s_filename_temp);
            $zipfiles = $zip->getFromName("docProps/app.xml");
            $xml = new \DOMDocument();
            $xml->loadXML($zipfiles);
            $zip->close();
            $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;

            $phpWords = new \PhpOffice\PhpWord\PhpWord();
            $o_words = new TemplateProcessor($s_file_path.$s_filename_temp);
            $o_words->setValue('num_pages', $i_count);
            $o_words->saveAs($s_file_path.$s_filename);
            unlink($s_file_path.$s_filename_temp);

            $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'];
        }

        return $a_return;
    }

    public function generate_reference_letter_resign($a_data)
    {
        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);

        $mba_employee_data = $this->Emm->get_employee_data([
            'em.employee_id' => $a_data['employee_id']
        ]);

        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);
        if (($mba_employee_data) AND ($mba_user_employee) AND ($mba_template_data)) {
            $o_employee = $mba_employee_data[0];
            $o_user_employee = $mba_user_employee[0];
            $o_template_data = $mba_template_data[0];
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);

            $mba_hr_data = $this->General->get_rectorate('human_resource');
            $s_hr_name = ($mba_hr_data) ? $mba_hr_data->rector_full_name : '';
            
            $mba_employee_department = $this->Emm->get_employee_department(['em.employee_id' => $o_employee->employee_id]);
            $s_employee_department = '';
            $s_employee_department_indo = '';
            
            if ($mba_employee_department) {
                $mba_prodi = $this->General->get_where('ref_study_program', ['study_program_abbreviation' => $mba_employee_department[0]->department_abbreviation]);
                $s_employee_department_indo = ($mba_prodi) ? 'Program Studi '.$mba_prodi[0]->study_program_name : 'Department '.$mba_employee_department[0]->department_name;
                $s_employee_department = ($mba_prodi) ? $mba_prodi[0]->study_program_name.' Study Program' : $mba_employee_department[0]->department_name.' Department';
            }

            $s_letter_year = (isset($a_data['letter_year'])) ? $a_data['letter_year'] : date('Y');
            
            $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
            $s_file_name = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
            $a_file_name = explode('.', $s_file_name);
            unset($a_file_name[count($a_file_name) - 1]);
            $s_file_name = implode(' ', $a_file_name).'('.ucwords(strtolower($o_employee->personal_data_name)).'-'.$a_data['academic_year_id'].$a_data['semester_type_id'].')';
            $s_filename = $s_file_name.'.docx';
            // $s_filename = str_replace(' ', '_', $s_filename);
            $s_filename_temp = $s_file_name.'_temp.docx';

            $date = new DateTime($o_employee->employee_join_date);
            $now = new DateTime();
            $interval = $now->diff($date);

            $job_title = (!is_null($o_employee->employee_job_title)) ? str_replace('& ', '&amp; ', $o_employee->employee_job_title) : '';

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_addess = $this->Pdm->retrieve_address($o_employee->personal_data_id);
            $s_employee_name = $this->Pdm->retrieve_title($o_employee->personal_data_id);
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            $o_word = new TemplateProcessor($s_template);

            $o_word->setValue('key_number', $a_data['letter_number']);
            $o_word->setValue('key_date', $a_data['letter_date']);
            $o_word->setValue('employee_call_gender_1', (($o_employee->personal_data_gender == 'M') ? 'He' : 'She'));
            $o_word->setValue('employee_call_gender_2', (($o_employee->personal_data_gender == 'M') ? 'His' : 'Her'));
            $o_word->setValue('employee_name', $s_employee_name);
            $o_word->setValue('employee_nip', $o_employee->employee_id_number);
            $o_word->setValue('employee_nik', $o_employee->personal_data_id_card_number);
            $o_word->setValue('employee_pob', $o_employee->personal_data_place_of_birth);
            $o_word->setValue('employee_dob', $o_employee->personal_data_date_of_birth);
            $o_word->setValue('employee_address', $s_addess);

            $o_word->setValue('start_date', $o_employee->employee_join_date);
            $o_word->setValue('end_date', $o_employee->last_date_of_work);
            $o_word->setValue('employee_job_title', $job_title);
            $o_word->setValue('department_name', $s_employee_department);

            $o_word->setValue('start_date_indo', $o_employee->employee_join_date);
            $o_word->setValue('end_date_indo', ((!empty($o_employee->last_date_of_work)) ? $o_employee->last_date_of_work : '-'));
            $o_word->setValue('department_name_indo', $s_employee_department_indo);
            
            $o_word->setValue('hrd_name', $s_hr_name);
            $o_word->saveAs($s_file_path.$s_filename_temp);

            $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
            $zip->open($s_file_path.$s_filename_temp);
            $zipfiles = $zip->getFromName("docProps/app.xml");
            $xml = new \DOMDocument();
            $xml->loadXML($zipfiles);
            $zip->close();
            $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;

            $phpWords = new \PhpOffice\PhpWord\PhpWord();
            $o_words = new TemplateProcessor($s_file_path.$s_filename_temp);
            $o_words->setValue('num_pages', $i_count);
            $o_words->saveAs($s_file_path.$s_filename);
            unlink($s_file_path.$s_filename_temp);

            $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'];
        }

        return $a_return;
    }

    public function generate_pengangkatan_karyawan($a_data)
    {
        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);

        $mba_employee_data = $this->Emm->get_employee_data([
            'em.employee_id' => $a_data['employee_id']
        ]);

        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);
        if (($mba_employee_data) AND ($mba_user_employee) AND ($mba_template_data)) {
            $o_employee = $mba_employee_data[0];
            $o_user_employee = $mba_user_employee[0];
            $o_template_data = $mba_template_data[0];
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);
            $mba_rector_data = $this->General->get_rectorate('rector');
            $mba_vice_rector_data = $this->General->get_rectorate('vice_rector');
            // $mba_hr_data = $this->General->get_rectorate('human_resource');

            $s_rector_name = ($mba_rector_data) ? $mba_rector_data->rector_full_name : '';
            $s_vice_rector_name = ($mba_vice_rector_data) ? $mba_vice_rector_data->rector_full_name : '';
            // $s_hr_name = ($mba_hr_data) ? $mba_hr_data->rector_full_name : '';

            $mba_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $a_data['semester_type_id']]);
            $s_letter_year = (isset($a_data['letter_year'])) ? $a_data['letter_year'] : date('Y');
            
            $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
            $s_file_name = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
            $a_file_name = explode('.', $s_file_name);
            unset($a_file_name[count($a_file_name) - 1]);
            $s_file_name = implode(' ', $a_file_name).'('.ucwords(strtolower($o_employee->personal_data_name)).'-'.$a_data['academic_year_id'].$a_data['semester_type_id'].')';
            $s_filename = $s_file_name.'.docx';
            // $s_filename = str_replace(' ', '_', $s_filename);
            $s_filename_temp = $s_file_name.'_temp.docx';

            $date = new DateTime($o_employee->employee_join_date);
            $now = new DateTime();
            $interval = $now->diff($date);

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_addess = $this->Pdm->retrieve_address($o_employee->personal_data_id);
            $job_title = (!is_null($o_employee->employee_job_title)) ? str_replace('& ', '&amp; ', $o_employee->employee_job_title) : '';

            $s_employee_name = $this->Pdm->retrieve_title($o_employee->personal_data_id);
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            $o_word = new TemplateProcessor($s_template);
            $o_word->setValue('key_number', $a_data['letter_number']);
            $o_word->setValue('key_date', $a_data['letter_date']);
            $o_word->setValue('call_gender', (($o_employee->personal_data_gender == 'M') ? 'His' : 'Her'));
            $o_word->setValue('employee_name', $s_employee_name);
            $o_word->setValue('employee_nip', $o_employee->employee_id_number);
            $o_word->setValue('employee_job_title', $job_title);
            $o_word->setValue('employee_address', $s_addess);
            $o_word->setValue('working_year', $interval->y);
            $o_word->setValue('employee_working_date', $o_employee->employee_join_date);
            // $o_word->setValue('semester_type', ucwords(strtolower($mba_semester_type_data[0]->semester_type_name)));
            // $o_word->setValue('semester_tipe', ucwords(strtolower($mba_semester_type_data[0]->semester_type_indonesian_name)));
            // $o_word->setValue('academic_year', $a_data['academic_year_id'].'/'.(intval($a_data['academic_year_id']) + 1));
            $o_word->setValue('head_hr', $s_hr_name);
            $o_word->setValue('head_hr_email', $mba_hr_data->employee_email);
            $o_word->saveAs($s_file_path.$s_filename_temp);

            $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
            $zip->open($s_file_path.$s_filename_temp);
            $zipfiles = $zip->getFromName("docProps/app.xml");
            $xml = new \DOMDocument();
            $xml->loadXML($zipfiles);
            $zip->close();
            $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;

            $phpWords = new \PhpOffice\PhpWord\PhpWord();
            $o_words = new TemplateProcessor($s_file_path.$s_filename_temp);
            $o_words->setValue('num_pages', $i_count);
            $o_words->saveAs($s_file_path.$s_filename);
            unlink($s_file_path.$s_filename_temp);

            $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'];
        }

        return $a_return;
    }

    public function generate_refference_letter_employee($a_data)
    {
        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);

        $mba_employee_data = $this->Emm->get_employee_data([
            'em.employee_id' => $a_data['employee_id']
        ]);

        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);
        if (($mba_employee_data) AND ($mba_user_employee) AND ($mba_template_data)) {
            $o_employee = $mba_employee_data[0];
            $o_user_employee = $mba_user_employee[0];
            $o_template_data = $mba_template_data[0];
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);
            $mba_rector_data = $this->General->get_rectorate('rector');
            $mba_vice_rector_data = $this->General->get_rectorate('vice_rector');
            $mba_hr_data = $this->General->get_rectorate('human_resource');

            $s_rector_name = ($mba_rector_data) ? $mba_rector_data->rector_full_name : '';
            $s_vice_rector_name = ($mba_vice_rector_data) ? $mba_vice_rector_data->rector_full_name : '';
            $s_hr_name = ($mba_hr_data) ? $mba_hr_data->rector_full_name : '';

            $mba_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $a_data['semester_type_id']]);
            $s_letter_year = (isset($a_data['letter_year'])) ? $a_data['letter_year'] : date('Y');
            
            $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
            $s_file_name = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
            $a_file_name = explode('.', $s_file_name);
            unset($a_file_name[count($a_file_name) - 1]);
            $s_file_name = implode(' ', $a_file_name).'('.ucwords(strtolower($o_employee->personal_data_name)).'-'.$a_data['academic_year_id'].$a_data['semester_type_id'].')';
            $s_filename = $s_file_name.'.docx';
            // $s_filename = str_replace(' ', '_', $s_filename);
            $s_filename_temp = $s_file_name.'_temp.docx';

            $date = new DateTime($o_employee->employee_join_date);
            $now = new DateTime();
            $interval = $now->diff($date);

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_addess = $this->Pdm->retrieve_address($o_employee->personal_data_id);
            $job_title = (!is_null($o_employee->employee_job_title)) ? str_replace('& ', '&amp; ', $o_employee->employee_job_title) : '';

            $s_employee_name = $this->Pdm->retrieve_title($o_employee->personal_data_id);
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            $o_word = new TemplateProcessor($s_template);
            $o_word->setValue('key_number', $a_data['letter_number']);
            $o_word->setValue('key_date', $a_data['letter_date']);
            $o_word->setValue('call_gender', (($o_employee->personal_data_gender == 'M') ? 'His' : 'Her'));
            $o_word->setValue('employee_name', $s_employee_name);
            $o_word->setValue('employee_nip', $o_employee->employee_id_number);
            $o_word->setValue('employee_job_title', $job_title);
            $o_word->setValue('employee_address', $s_addess);
            $o_word->setValue('working_year', $interval->y);
            $o_word->setValue('employee_working_date', $o_employee->employee_join_date);
            // $o_word->setValue('semester_type', ucwords(strtolower($mba_semester_type_data[0]->semester_type_name)));
            // $o_word->setValue('semester_tipe', ucwords(strtolower($mba_semester_type_data[0]->semester_type_indonesian_name)));
            // $o_word->setValue('academic_year', $a_data['academic_year_id'].'/'.(intval($a_data['academic_year_id']) + 1));
            $o_word->setValue('head_hr', $s_hr_name);
            $o_word->setValue('head_hr_email', $mba_hr_data->employee_email);
            $o_word->saveAs($s_file_path.$s_filename_temp);

            $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
            $zip->open($s_file_path.$s_filename_temp);
            $zipfiles = $zip->getFromName("docProps/app.xml");
            $xml = new \DOMDocument();
            $xml->loadXML($zipfiles);
            $zip->close();
            $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;

            $phpWords = new \PhpOffice\PhpWord\PhpWord();
            $o_words = new TemplateProcessor($s_file_path.$s_filename_temp);
            $o_words->setValue('num_pages', $i_count);
            $o_words->saveAs($s_file_path.$s_filename);
            unlink($s_file_path.$s_filename_temp);

            $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'];
        }

        return $a_return;
    }

    public function calculate_date()
    {
        // $bday = '11-03-2019';
        // $date = new DateTime($bday);
        // $now = new DateTime();
        // $interval = $now->diff($date);
        // print($interval->y);exit;
    }

    public function generate_assignment_letter_for_community($a_data)
    {
        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);

        $mba_study_program_data = $this->Spm->get_study_program($a_data['study_program_id']);
        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);

        $mba_employee_data = $this->Emm->get_employee_data([
            'em.employee_id' => $a_data['employee_id']
        ]);

        if (($mba_employee_data) AND ($mba_user_employee) AND ($mba_template_data)) {
            $mba_count_of_letter = $this->Lnm->get_count_personal_document(['letter_number_id' => $mbs_letter_number_id]);
            $a_number_of_letter = explode('/', $a_data['letter_number']);
            $s_number_of_letter = $a_number_of_letter[2];
            $o_employee = $mba_employee_data[0];
            $o_user_employee = $mba_user_employee[0];
            $o_template_data = $mba_template_data[0];
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);

            // $mbo_hod_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mba_study_program_data[0]->head_of_study_program_id))[0];
            // $s_hod_name = $this->Pdm->retrieve_title($mba_study_program_data[0]->head_of_study_program_id);
            // $mbo_dean_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mba_study_program_data[0]->deans_id))[0];
            $mbo_lpppm_dept = $this->General->get_where('ref_department', ['department_id' => '49']);
            $mbo_head_lpppm_data = $this->Emm->get_employee_data(array('em.employee_id' => $mbo_lpppm_dept[0]->employee_id));
            // $s_dean_name = $this->Pdm->retrieve_title($mba_study_program_data[0]->deans_id);
            $s_head_lpppm_name = $this->Pdm->retrieve_title($mbo_head_lpppm_data[0]->personal_data_id);
            $mba_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $a_data['semester_type_id']]);
            $s_letter_year = (isset($a_data['letter_year'])) ? $a_data['letter_year'] : date('Y');
            
            $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
            $s_vice_rector_name = '';
            if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
            }

            $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
            $s_file_name = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
            $a_file_name = explode('.', $s_file_name);
            unset($a_file_name[count($a_file_name) - 1]);
            $s_file_name = implode(' ', $a_file_name).'('.ucwords(strtolower($o_employee->personal_data_name)).'-'.$a_data['academic_year_id'].$a_data['semester_type_id'].')_'.$s_number_of_letter.'_'.$mba_count_of_letter;
            $s_filename = $s_file_name.'.docx';
            // $s_filename = str_replace(' ', '_', $s_filename);
            $s_filename_temp = $s_file_name.'_temp.docx';

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_employee_name = $this->Pdm->retrieve_title($o_employee->personal_data_id);
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            // $phpWord->getSettings()->setHideGrammaticalErrors(true);
            
            // $o_word->setValue('rector_name', $s_rector_name);
            // $o_word->setValue('rector_email', $s_rector_email);
            // $o_word->setValue('vice_academic_rector_name', $s_vice_rector_name);
            // $o_word->setValue('vice_academic_rector_email', $s_vice_rector_email);

            $o_word = new TemplateProcessor($s_template);
            $o_word->setValue('key_number', $a_data['letter_number']);
            $o_word->setValue('key_date', $a_data['letter_date']);
            $o_word->setValue('lp3m_head_call', (($mbo_head_lpppm_data[0]->personal_data_gender == 'M') ? 'His' : 'Her'));
            // $o_word->setValue('lecturer_name', $s_employee_name);
            $o_word->setValue('semester_type', ucwords(strtolower($mba_semester_type_data[0]->semester_type_name)));
            $o_word->setValue('semester_tipe', ucwords(strtolower($mba_semester_type_data[0]->semester_type_indonesian_name)));
            $o_word->setValue('academic_year', $a_data['academic_year_id'].'/'.(intval($a_data['academic_year_id']) + 1));
            $o_word->setValue('lp3m_head_name', $s_head_lpppm_name);
            $o_word->setValue('page', '1');

            $i_count_block = 0;
            $a_userassigns = [];
            if ($a_data['a_employee_id']) {
                $i_count_block++;
                $a_lecturer_assign = [];
                foreach ($a_data['a_employee_id'] as $s_employee_id) {
                    $mba_employee_assign = $this->General->get_where('dt_employee', ['employee_id' => $s_employee_id]);
                    $s_assignname = $this->General->retrieve_title($mba_employee_assign[0]->personal_data_id);
                    if (!in_array($s_assignname, $a_lecturer_assign)) {
                        array_push($a_lecturer_assign, $s_assignname);
                    }
                }
                
                array_push($a_userassigns, [
                    'lecturer_name' => implode(', ', $a_lecturer_assign),
                    'position' => 'Lecturer'
                ]);
            }
            if ($a_data['a_student_id']) {
                $i_count_block++;
                $a_student_assign = [];
                foreach ($a_data['a_student_id'] as $s_student_id) {
                    $mba_student_assign = $this->General->get_where('dt_student', ['student_id' => $s_student_id]);
                    $s_assignname = $this->General->retrieve_title($mba_student_assign[0]->personal_data_id);
                    if (!in_array($s_assignname, $a_student_assign)) {
                        array_push($a_student_assign, $s_assignname);
                    }
                }

                array_push($a_userassigns, [
                    'lecturer_name' => implode(', ', $a_student_assign),
                    'position' => 'Student'
                ]);
            }

            $o_word->cloneBlock('block_assigns', 0, true, false, $a_userassigns);

            $o_word->saveAs($s_file_path.$s_filename_temp);

            $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
            $zip->open($s_file_path.$s_filename_temp);
            $zipfiles = $zip->getFromName("docProps/app.xml");
            $xml = new \DOMDocument();
            $xml->loadXML($zipfiles);
            $zip->close();
            $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;

            $phpWords = new \PhpOffice\PhpWord\PhpWord();
            $o_words = new TemplateProcessor($s_file_path.$s_filename_temp);
            $o_words->setValue('num_pages', $i_count);
            $o_words->saveAs($s_file_path.$s_filename);
            unlink($s_file_path.$s_filename_temp);

            // header('Content-Disposition: attachment; filename='.urlencode($s_filename));
            // readfile( $s_file_path.$s_filename );
            // exit;

            $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'];
        }

        
        return $a_return;
    }

    public function generate_assignment_letter_for_research($a_data)
    {
        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);

        $mba_study_program_data = $this->Spm->get_study_program($a_data['study_program_id']);
        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);

        $mba_employee_data = $this->Emm->get_employee_data([
            'em.employee_id' => $a_data['employee_id']
        ]);

        if (($mba_employee_data) AND ($mba_user_employee) AND ($mba_template_data)) {
            $o_prodi = $mba_study_program_data[0];
            $o_employee = $mba_employee_data[0];
            $o_user_employee = $mba_user_employee[0];
            $o_template_data = $mba_template_data[0];
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);

            $mbo_hod_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mba_study_program_data[0]->head_of_study_program_id))[0];
            $s_hod_name = $this->Pdm->retrieve_title($mba_study_program_data[0]->head_of_study_program_id);
            $mbo_dean_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mba_study_program_data[0]->deans_id))[0];
            $mbo_lpppm_dept = $this->General->get_where('ref_department', ['department_id' => '49']);
            $mbo_head_lpppm_data = $this->Emm->get_employee_data(array('em.employee_id' => $mbo_lpppm_dept[0]->employee_id));
            $s_dean_name = $this->Pdm->retrieve_title($mba_study_program_data[0]->deans_id);
            $s_head_lpppm_name = $this->Pdm->retrieve_title($mbo_head_lpppm_data[0]->personal_data_id);
            $mba_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $a_data['semester_type_id']]);
            $s_letter_year = (isset($a_data['letter_year'])) ? $a_data['letter_year'] : date('Y');
            
            $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
            $s_vice_rector_name = '';
            if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
            }

            $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
            $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
            $s_file_name = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
            $a_file_name = explode('.', $s_file_name);
            unset($a_file_name[count($a_file_name) - 1]);
            $s_file_name = implode(' ', $a_file_name).'('.ucwords(strtolower($o_employee->personal_data_name)).'-'.$a_data['academic_year_id'].$a_data['semester_type_id'].')';
            $s_filename = $s_file_name.'.docx';
            // $s_filename = str_replace(' ', '_', $s_filename);
            $s_filename_temp = $s_file_name.'_temp.docx';

            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $s_employee_name = $this->Pdm->retrieve_title($o_employee->personal_data_id);
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            $o_word = new TemplateProcessor($s_template);
            $o_word->setValue('key_number', $a_data['letter_number']);
            $o_word->setValue('key_date', $a_data['letter_date']);
            $o_word->setValue('lp3m_head_call', (($mbo_head_lpppm_data[0]->personal_data_gender == 'M') ? 'His' : 'Her'));
            $o_word->setValue('lecturer_name', $s_employee_name);
            $o_word->setValue('semester_type', ucwords(strtolower($mba_semester_type_data[0]->semester_type_name)));
            $o_word->setValue('semester_tipe', ucwords(strtolower($mba_semester_type_data[0]->semester_type_indonesian_name)));
            $o_word->setValue('academic_year', $a_data['academic_year_id'].'/'.(intval($a_data['academic_year_id']) + 1));
            $o_word->setValue('lp3m_head_name', $s_head_lpppm_name);
            $o_word->setValue('position', '');
            $o_word->saveAs($s_file_path.$s_filename_temp);

            $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
            $zip->open($s_file_path.$s_filename_temp);
            $zipfiles = $zip->getFromName("docProps/app.xml");
            $xml = new \DOMDocument();
            $xml->loadXML($zipfiles);
            $zip->close();
            $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;

            $phpWords = new \PhpOffice\PhpWord\PhpWord();
            $o_words = new TemplateProcessor($s_file_path.$s_filename_temp);
            $o_words->setValue('num_pages', $i_count);
            $o_words->saveAs($s_file_path.$s_filename);
            unlink($s_file_path.$s_filename_temp);

            $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'];
        }

        return $a_return;
    }

    public function generate_assignment_letter_lecturing($a_data)
    {
        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);

        $mba_study_program_data = $this->Spm->get_study_program($a_data['study_program_id']);
        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);

        $mba_employee_data = $this->Emm->get_employee_data([
            'em.employee_id' => $a_data['employee_id']
        ]);

        if (($mba_employee_data) AND ($mba_user_employee) AND ($mba_template_data)) {
            // if ($a_data['target'] == 'deans') {
            //     // return false;
            // }
            // else if ($a_data['target'] == 'lecturer') {
                $o_prodi = ($mba_study_program_data) ? $mba_study_program_data[0] : false;
                $o_employee = $mba_employee_data[0];
                $o_user_employee = $mba_user_employee[0];
                $o_template_data = $mba_template_data[0];
                $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);
            // }

            if (!$o_prodi) {
                $o_prodi = [
                    'study_program_id' => '',
                    'study_program_name' => '',
                    'study_program_name_feeder' => '',
                    'faculty_name' => '',
                    'faculty_name_feeder' => '',
                    'head_of_study_program_sk_number' => '',
                    'faculty_deans_sk_number' => '',
                ];
                $o_prodi = (object) $o_prodi;
            }
            $mbo_hod_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mba_study_program_data[0]->head_of_study_program_id))[0];
            $s_hod_name = $this->Pdm->retrieve_title($mba_study_program_data[0]->head_of_study_program_id);
            $mbo_dean_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mba_study_program_data[0]->deans_id))[0];
            $s_dean_name = $this->Pdm->retrieve_title($mba_study_program_data[0]->deans_id);
            $mba_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $a_data['semester_type_id']]);
            
            $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
            $s_vice_rector_name = '';
            if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
            }

            $mba_employee_class = $this->Cgm->get_class_master_filtered([
                'cm.academic_year_id' => $a_data['academic_year_id'],
                'cm.semester_type_id' => $a_data['semester_type_id'],
                'cml.employee_id' => $o_employee->employee_id
            ]);

            $s_letter_year = (isset($a_data['letter_year'])) ? $a_data['letter_year'] : date('Y');

            if ($mba_employee_class) {
                $a_class_id = [];
                $a_class_data = [];
                $i_number = 1;
                $i_sks = 0;

                $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
                $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
                $s_file_name = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
                $a_file_name = explode('.', $s_file_name);
                unset($a_file_name[count($a_file_name) - 1]);
                $s_file_name = implode(' ', $a_file_name).'('.ucwords(strtolower($o_employee->personal_data_name)).'-'.$a_data['academic_year_id'].$a_data['semester_type_id'].')';
                $s_filename = $s_file_name.'.docx';
                // $s_filename = str_replace(' ', '_', $s_filename);
                $s_filename_temp = $s_file_name.'_temp.docx';

                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                foreach ($mba_employee_class as $o_class) {
                    $b_subject_teaching = true;
                    if(strpos($o_class->class_master_name, 'thesis') !== false) {
                        $b_subject_teaching = false;
                    }
                    else if(strpos($o_class->class_master_name, 'internship') !== false) {
                        $b_subject_teaching = false;
                    }
                    else if(strpos($o_class->class_master_name, 'research semester') !== false) {
                        $b_subject_teaching = false;
                    }
                    else if(strpos($o_class->class_master_name, 'research project') !== false) {
                        $b_subject_teaching = false;
                    }
                    else if(strpos($o_class->class_master_name, 'NFU') !== false) {
                        $b_subject_teaching = false;
                    }

                    if ((!in_array($o_class->class_master_id, $a_class_id)) AND ($b_subject_teaching)) {
                        $mba_score_data = $this->General->get_where('dt_score', [
                            'class_master_id' => $o_class->class_master_id,
                            'score_approval' => 'approved'
                        ]);

                        if ($mba_score_data) {
                            array_push($a_class_id, $o_class->class_master_id);
                            $a_prodi = [];
                            $mba_class_study_program = $this->Cgm->get_class_master_study_program($o_class->class_master_id);

                            if ($mba_class_study_program) {
                                foreach ($mba_class_study_program as $o_class_prodi) {
                                    array_push($a_prodi, $o_class_prodi->study_program_abbreviation);
                                }
                            }

                            $a_list_class = [
                                'no' => $i_number,
                                'subject_name' => str_replace('\'', '&apos', str_replace('"', '&quot;', str_replace('&', '&amp;', $o_class->subject_name))),
                                'subject_code' => $o_class->subject_code,
                                'subject_credit' => $o_class->credit_allocation,
                                'subject_prodi' => implode(' | ', $a_prodi)
                            ];

                            $i_sks += $o_class->credit_allocation;
                            array_push($a_class_data, $a_list_class);
                            $i_number++;
                        }
                    }
                }

                $s_employee_name = $this->Pdm->retrieve_title($o_employee->personal_data_id);
                $phpWord = new \PhpOffice\PhpWord\PhpWord();
                $phpWord->getSettings()->setHideGrammaticalErrors(true);
                $phpWord->getSettings()->setHideSpellingErrors(true);
                $o_word = new TemplateProcessor($s_template);
                $o_word->setValue('number_letter', $a_data['letter_number']);
                $o_word->setValue('prodi_name', $o_prodi->study_program_name);
                $o_word->setValue('faculty_name', $o_prodi->faculty_name);
                $o_word->setValue('faculty_name_indo', $o_prodi->faculty_name_feeder);
                $o_word->setValue('call_gender', (($mbo_dean_data->personal_data_gender == 'M') ? 'His' : 'She'));
                $o_word->setValue('sk_hod', $o_prodi->head_of_study_program_sk_number);
                $o_word->setValue('sk_dean', $o_prodi->faculty_deans_sk_number);
                $o_word->setValue('date_now', date('d F Y'));
                $o_word->setValue('lecturer_name', $s_employee_name);
                $o_word->setValue('dean_name', $s_dean_name);
                $o_word->setValue('semester_type', ucwords(strtolower($mba_semester_type_data[0]->semester_type_name)));
                $o_word->setValue('semester_tipe', ucwords(strtolower($mba_semester_type_data[0]->semester_type_indonesian_name)));
                $o_word->setValue('academic_year', $a_data['academic_year_id'].'/'.(intval($a_data['academic_year_id']) + 1));
                $o_word->setValue('lecturer_nidn', ((is_null($o_employee->employee_lecturer_number)) ? '' : $o_employee->employee_lecturer_number));
                $o_word->setValue('lecturer_nip', ((is_null($o_employee->employee_id_number)) ? '' : $o_employee->employee_id_number));
                $o_word->setValue('vice_academic_rector_name', $s_vice_rector_name);
                $o_word->setValue('page', '1');
                $o_word->setValue('credit_total', $i_sks);
                $o_word->cloneRowAndSetValues('no', $a_class_data);
                $o_word->saveAs($s_file_path.$s_filename_temp);

                $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
                $zip->open($s_file_path.$s_filename_temp);
                $zipfiles = $zip->getFromName("docProps/app.xml");
                $xml = new \DOMDocument();
                $xml->loadXML($zipfiles);
                $zip->close();
                $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;

                $phpWords = new \PhpOffice\PhpWord\PhpWord();
                $o_words = new TemplateProcessor($s_file_path.$s_filename_temp);
                $o_words->setValue('num_pages', $i_count);
                $o_words->saveAs($s_file_path.$s_filename);
                unlink($s_file_path.$s_filename_temp);

                $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
            }
            else {
                $a_return = ['code' => 1, 'message' => 'employee is not registered in any class!'];
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'];
        }

        return $a_return;
    }

    public function generate_assigment_letter_study_program($a_data)
    {
        $mba_user_employee = $this->Emm->get_employee_data([
            'em.employee_id' => $this->session->userdata('employee_id')
        ]);
        $mba_letter_data = $this->General->get_where('dt_letter_number', ['letter_number_result' => $a_data['letter_number']]);
        
        $mba_study_program_data = $this->Spm->get_study_program($a_data['study_program_id']);
        $mba_template_data = $this->Lnm->get_template(false, ['template_id' => $a_data['template_id']]);

        $mba_employee_data = $this->Emm->get_employee_data([
            'em.employee_id' => $a_data['employee_id']
        ]);

        if (($mba_employee_data) AND ($mba_study_program_data) AND ($mba_user_employee) AND ($mba_template_data)) {
            $o_prodi = $mba_study_program_data[0];
            $o_employee = $mba_employee_data[0];
            $o_user_employee = $mba_user_employee[0];
            $o_template_data = $mba_template_data[0];
            $s_firts_char_user = substr($o_user_employee->personal_data_name, 0, 1);

            $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
            $s_vice_rector_name = '';
            if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
            }
            $mbo_hod_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mba_study_program_data[0]->head_of_study_program_id))[0];
            $s_hod_name = $this->Pdm->retrieve_title($mba_study_program_data[0]->head_of_study_program_id);
            $mba_semester_type_data = $this->General->get_where('ref_semester_type', ['semester_type_id' => $a_data['semester_type_id']]);

            $mba_employee_class = $this->Cgm->get_class_group_filtered([
                'dcg.academic_year_id' => $a_data['academic_year_id'],
                'dcg.semester_type_id' => $a_data['semester_type_id'],
                'cgl.employee_id' => $o_employee->employee_id
            ]);

            if ($mba_employee_class) {
                $s_letter_year = ($mba_letter_data) ? $mba_letter_data[0]->letter_year : date('Y');
                $s_template = APPPATH."uploads/templates/spmi/".$o_template_data->letter_abbreviation."/".$o_template_data->template_filelink;
                $s_file_path = APPPATH.'uploads/staff/'.$s_firts_char_user.'/'.$o_user_employee->personal_data_id.'/'.$s_letter_year.'/request_letter/';
                $s_file_name = str_replace('_', ' ', str_replace('_Template', '', $o_template_data->filename));
                $a_file_name = explode('.', $s_file_name);
                unset($a_file_name[count($a_file_name) - 1]);
                $s_file_name = implode(' ', $a_file_name).'('.ucwords(strtolower($o_employee->personal_data_name)).')';
                $s_filename = $s_file_name.'.docx';
                $s_filename_temp = $s_file_name.'_temp.docx';

                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $a_class_id = [];
                $a_class_data = [];
                $i_number = 1;
                $i_sks = 0;
                foreach ($mba_employee_class as $o_lect) {
                    if (!in_array($o_lect->class_group_id, $a_class_id)) {
                        $mba_score_data = $this->General->get_where('dt_score', [
                            'class_master_id' => $o_lect->class_master_id,
                            'score_approval' => 'approved'
                        ]);

                        if ($mba_score_data) {
                            array_push($a_class_id, $o_lect->class_group_id);
                            $mba_class_group_details = $this->Cgm->get_class_group_filtered([
                                'dcg.class_group_id' => $o_lect->class_group_id
                            ]);
    
                            if ($mba_class_group_details) {
                                $s_class_study_program_id = (!is_null($o_lect->class_study_program_main_id)) ? $o_lect->class_study_program_main_id : $o_lect->class_group_study_program_id;
                                $mba_class_study_program_data = $this->General->get_where('ref_study_program', ['study_program_id' => $s_class_study_program_id]);
                                $a_list_class = [
                                    'no' => $i_number,
                                    'subject_name' => str_replace('\'', '&apos', str_replace('"', '&quot;', str_replace('&', '&amp;', $o_lect->subject_name))),
                                    'subject_code' => $o_lect->subject_code,
                                    'subject_credit' => $o_lect->credit_allocation,
                                    'subject_prodi' => $mba_class_study_program_data[0]->study_program_name
                                ];
    
                                $i_sks += $o_lect->credit_allocation;
                                array_push($a_class_data, $a_list_class);
                                $i_number++;
                            }
                        }
                    }
                }

                $phpWord = new \PhpOffice\PhpWord\PhpWord();
                $phpWord->getSettings()->setHideGrammaticalErrors(true);
                $phpWord->getSettings()->setHideSpellingErrors(true);
                $o_word = new TemplateProcessor($s_template);
                $o_word->setValue('number_letter', $a_data['letter_number']);
                $o_word->setValue('prodi_name', $o_prodi->study_program_name);
                $o_word->setValue('call_gender', (($o_employee->personal_data_gender == 'M') ? 'He' : 'She'));
                $o_word->setValue('sk_hod', $o_prodi->head_of_study_program_sk_number);
                $o_word->setValue('sk_dean', $o_prodi->faculty_deans_sk_number);
                $o_word->setValue('date_now', date('d F Y'));
                $o_word->setValue('lecturer_name', ucwords(strtolower($o_employee->personal_data_name)));
                $o_word->setValue('semester_type', ucwords(strtolower($mba_semester_type_data[0]->semester_type_name)));
                $o_word->setValue('semester_tipe', ucwords(strtolower($mba_semester_type_data[0]->semester_type_indonesian_name)));
                $o_word->setValue('academic_year', $a_data['academic_year_id'].'/'.(intval($a_data['academic_year_id']) + 1));
                $o_word->setValue('lecturer_nidn', ((is_null($o_employee->employee_lecturer_number)) ? '' : $o_employee->employee_lecturer_number));
                $o_word->setValue('lecturer_nip', ((is_null($o_employee->employee_id_number)) ? '' : $o_employee->employee_id_number));
                $o_word->setValue('vice_academic_rector_name', $s_vice_rector_name);
                $o_word->setValue('page', '1');
                $o_word->setValue('credit_total', $i_sks);
                $o_word->cloneRowAndSetValues('no', $a_class_data);
                $o_word->saveAs($s_file_path.$s_filename_temp);

                $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
                $zip->open($s_file_path.$s_filename_temp);
                $zipfiles = $zip->getFromName("docProps/app.xml");
                $xml = new \DOMDocument();
                $xml->loadXML($zipfiles);
                $zip->close();
                $i_count = $xml->getElementsByTagName('Pages')->item(0)->nodeValue;

                $phpWords = new \PhpOffice\PhpWord\PhpWord();
                $o_words = new TemplateProcessor($s_file_path.$s_filename_temp);
                $o_words->setValue('num_pages', $i_count);
                $o_words->saveAs($s_file_path.$s_filename);
                unlink($s_file_path.$s_filename_temp);

                $a_return = ['code' => 0, 'message' => 'Success', 'file' => urlencode($s_filename)];
            }
            else {
                $a_return = ['code' => 1, 'message' => 'employee is not registered in any class!'];
            }
        }
        else {
            $a_return = ['code' => 1, 'message' => 'data not found in system!'];
        }

        return $a_return;
    }

    public function generate_flying_faculty_transcript($s_student_id)
    {
        $mbo_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id])[0];
        if ($mbo_student_data) {
            $mba_score_flying_faculty = $this->Scm->get_score_data_transcript([
                'sc.student_id' => $s_student_id,
                'sc.score_mark_flying_faculty' => 1,
                'sc.score_approval' => 'approved',
                'sc.score_display' => 'TRUE'
            ]);

            if ($mba_score_flying_faculty) {
                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
                $s_vice_rector_name = '';
                $s_vice_rector_email = '';
                if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                    $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                    $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
                    $s_vice_rector_email = $mbo_vice_rector_data->employee_email;
                }

                $s_study_program_id = (is_null($mbo_student_data->study_program_main_id)) ? $mbo_student_data->study_program_id : $mbo_student_data->study_program_main_id;
                $mba_study_program_data = $this->Spm->get_study_program($s_study_program_id, false)[0];
                $mbo_deans_data = $this->General->get_where('dt_employee', ['personal_data_id' => $mba_study_program_data->deans_id])[0];
                $s_deans_name = $this->Pdm->retrieve_title($mba_study_program_data->deans_id);
                $s_hod_name = $this->Pdm->retrieve_title($mba_study_program_data->head_of_study_program_id);
                
                $s_header_sign_name = (is_null($mba_study_program_data->head_of_study_program_id)) ? $s_vice_rector_name : $s_hod_name;
                $s_header_sign_title = (is_null($mba_study_program_data->head_of_study_program_id)) ? 'Vice Rector Academic' : 'Head of Study Program of '.$mba_study_program_data->study_program_name;

                $s_student_name = str_replace(' ', '-', ucwords(strtolower($mbo_student_data->personal_data_name)));

                $s_template = APPPATH."uploads/templates/academic/flying_fac/template_transcript_of_Flying_Faculty.docx";
                $s_file_path = APPPATH.'/uploads/academic/transcript-flying-faculty/'.$mbo_student_data->academic_year_id.'/';
                $s_file_name = 'Flying_faculty_certification_letter_'.$mbo_student_data->student_number.'_'.$s_student_name;
                $s_filename = $s_file_name.'.docx';

                if(!file_exists($s_file_path)){
                    mkdir($s_file_path, 0777, TRUE);
                }

                $o_word = new TemplateProcessor($s_template);
                $o_word->setValue('letterdate', '');
                $o_word->setValue('letternumber', '');
                $o_word->setValue('letterto', '');

                $o_word->setValue('student_name', $mbo_student_data->personal_data_name);
                $o_word->setValue('student_number', $mbo_student_data->student_number);
                $o_word->setValue('student_prodi', $mba_study_program_data->study_program_name);

                $a_score = [];
                $i_number = 1;
                $a_student_id_flying_fac_ects_score = $this->config->item('student_id_flying_fac_ects_score');

                foreach ($mba_score_flying_faculty as $o_score) {
                    $d_score_sum = intval(round($o_score->score_sum, 0, PHP_ROUND_HALF_UP));
                    $d_grade_point = $this->grades->get_grade_point($d_score_sum);
                    $d_ects = $this->grades->get_ects_score($o_score->curriculum_subject_credit, $d_grade_point);

                    if (in_array($s_student_id, $a_student_id_flying_fac_ects_score)) {
                        $d_ects = $this->grades->get_ects_score($o_score->curriculum_subject_credit, $o_score->subject_name, $o_score->score_ects);
                    }

                    $s_grade = $this->grades->get_grade($d_score_sum);
                    $s_tui_score = $this->grades->conversion_score_to_german($d_score_sum);
                    
                    $a_list_score = [
                        'no' => $i_number,
                        'subject_name' => $o_score->subject_name,
                        'lecturer' => '',
                        'ects' => $d_ects,
                        'score' => $d_score_sum.'% / '.$s_grade,
                        'tui_score' => $s_tui_score
                    ];

                    array_push($a_score, $a_list_score);
                    $i_number++;
                }

                $o_word->cloneRowAndSetValues('no', $a_score);

                $o_word->setValue('dean_name', $s_header_sign_name);
                // $o_word->setValue('dean_faculty', 'Dean of the '.$mba_study_program_data->faculty_name);
                $o_word->setValue('dean_faculty', $s_header_sign_title);
                $o_word->setValue('dean_email', $mbo_deans_data->employee_email);

                $s_file_footer = str_replace('_', ' ', $s_file_name);
                $s_file_footer = str_replace('-', ' ', $s_file_footer);
                $o_word->setValue('filename', $s_file_footer);

                $o_word->saveAs($s_file_path.$s_filename);
                // $a_return = ['code' => 0, 'filename' => $s_filename, 'batch' => $mbo_student_data->academic_year_id, 'filepath' => $s_file_path];
                $a_return = ['code' => 0, 'filename' => $s_filename, 'batch' => $mbo_student_data->academic_year_id];
            }else{
                $a_return = ['code' => 1, 'message' => 'Score flying faculty not found!'];
            }
        }else{
            $a_return = ['code' => 1, 'message' => 'Student not found!'];
        }

        return $a_return;

        // if ($a_return['code'] == 0) {
        //     $a_path_info = pathinfo($a_return['filepath'].$a_return['filename']);
		// 	$s_file_ext = $a_path_info['extension'];
		// 	header('Content-Disposition: attachment; filename='.urlencode($a_return['filename']));
		// 	readfile( $a_return['filepath'].$a_return['filename'] );
		// 	exit;
        // }else{
        //     print('<pre>');
        //     var_dump($a_return);exit;
        // }
    }

    public function generate_english_medium_instruction()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');

            $s_letter_number_fac = $this->input->post('letter_fac');
            $s_letter_number_numb = $this->input->post('letter_number_numb');
            $s_letter_number_month = $this->input->post('letter_number_month');
            $s_letter_number_year = $this->input->post('letter_number_year');

            $s_english_request_student = $this->input->post('english_request_student');

            $s_date_letter = $this->input->post('letter_date');

            $s_request_by = strtoupper($this->input->post('request_by'));
            $s_request_gender = $this->input->post('request_gender');
            
            $s_request_purpose = $this->input->post('request_purpose');

            $s_letter_number = 'L/'.$s_letter_number_fac.'/'.$s_letter_number_numb.'/'.$s_letter_number_month.'/'.$s_letter_number_year;
            if ($s_date_letter != '') {
                $s_date_letter = date('d F Y', strtotime($s_date_letter));
            }

            $mbo_student_data = $this->Stm->get_student_filtered(array('student_id' => $s_student_id))[0];

            if ($mbo_student_data) {
                if ($s_english_request_student == 'on') {
                    $s_request_gender = 'Mrs';
                    $s_request_by = strtoupper($mbo_student_data->personal_data_name);

                    if ($mbo_student_data->personal_data_gender == 'M') {
                        $s_request_gender = 'Mr';
                    }
                }

                $s_requested = $s_request_gender.'. '.$s_request_by;

                $s_template_path = APPPATH.'uploads/templates/template_english_as_medium_instruction.docx';

                $mbo_semester_active = $this->Smm->get_active_semester();
                $mbo_deans_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mbo_student_data->deans_id))[0];
                $s_deans_name = $this->Pdm->retrieve_title($mbo_student_data->deans_id);

                $s_path_target = APPPATH."uploads/academic/".$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id."/english_medium_instruction/";
                if(!file_exists($s_path_target)) {
                    mkdir($s_path_target, 0777, TRUE);
                }
                $s_name_file = 'English_as_Medium_Instuction_'.(str_replace(' ', '_', $mbo_student_data->personal_data_name));
                $s_target_filename = $s_name_file.'.docx';
                $s_dir = $s_path_target.$s_target_filename;

                $phpWord = new \PhpOffice\PhpWord\PhpWord();
                $o_word = new TemplateProcessor($s_template_path);

                $o_word->setValue('letter_number_header', $s_letter_number);
                $o_word->setValue('date_header', $s_date_letter);
                $o_word->setValue('faculty', $mbo_student_data->faculty_name);
                $o_word->setValue('student_name', strtoupper($mbo_student_data->personal_data_name));
                $o_word->setValue('fac', str_replace('FACULTY OF', '', strtoupper($mbo_student_data->faculty_name)));
                $o_word->setValue('request_by', $s_requested);
                $o_word->setValue('purpose', $s_request_purpose);
                $o_word->setValue('deans', $s_deans_name);
                $o_word->setValue('email', $mbo_deans_data->employee_email);

                $o_word->saveAs($s_dir);
                $a_return = array('code' => 0, 'file_name' => $s_target_filename);
            }else{
                $a_return = array('code' => 1, 'message' => 'Student not found!', 'description' => '');
            }

            print json_encode($a_return);
        }
    }

    public function generate_temporary_graduation_letter()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_letter_number_fac = $this->input->post('letter_fac');
            $s_letter_number_numb = $this->input->post('letter_number_numb');
            $s_letter_number_month = $this->input->post('letter_number_month');
            $s_letter_number_year = $this->input->post('letter_number_year');

            $s_date_letter = $this->input->post('letter_date');
            
            $s_date_comprehensive_examination = $this->input->post('date_comprehensive_examination');
            $s_date_receipt = $this->input->post('date_receipt');

            $s_letter_number = 'L/'.$s_letter_number_fac.'/'.$s_letter_number_numb.'/'.$s_letter_number_month.'/'.$s_letter_number_year;
            if ($s_date_letter != '') {
                $s_date_letter = date('d F Y', strtotime($s_date_letter));
            }
            
            if ($s_date_comprehensive_examination != '') {
                $s_date_comprehensive_examination = date('d F Y', strtotime($s_date_comprehensive_examination));
            }
            
            $mbo_student_data = $this->Stm->get_student_filtered(array('student_id' => $s_student_id))[0];

            if ($mbo_student_data) {
                
                $s_template_path = APPPATH.'uploads/templates/template_temporary_graduation.docx';

                $mbo_semester_active = $this->Smm->get_active_semester();
                $mbo_deans_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mbo_student_data->deans_id))[0];
                $s_deans_name = $this->Pdm->retrieve_title($mbo_student_data->deans_id);

                $s_path_target = APPPATH."uploads/academic/".$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id."/temporary_graduation_letter/";
                $s_name_file = 'Temporary_Graduation_'.(str_replace(' ', '_', $mbo_student_data->personal_data_name));
                $s_name_file = str_replace("'", "", $s_name_file);
                $s_target_filename = $s_name_file.'.docx';
                $s_dir = $s_path_target.$s_target_filename;

                if(!file_exists($s_path_target)){
                    mkdir($s_path_target, 0777, TRUE);
                }

                $s_dob = '';
                if ($mbo_student_data->personal_data_date_of_birth != null) {
                    $s_dob = date('d F Y', strtotime($mbo_student_data->personal_data_date_of_birth));
                }

                $o_word = new TemplateProcessor($s_template_path);
                $o_word->setValue('letternumber', $s_letter_number);
                $o_word->setValue('letterdate', $s_date_letter);
                $o_word->setValue('personaldatanameheader', strtoupper($mbo_student_data->personal_data_name));
                $o_word->setValue('personaldataname', (ucwords(strtolower($mbo_student_data->personal_data_name))));
                $o_word->setValue('studentnumber', $mbo_student_data->student_number);
                $o_word->setValue('called', (($mbo_student_data->personal_data_gender == 'M') ? 'his' : 'her'));
                $o_word->setValue('pob', (ucwords(strtolower($mbo_student_data->personal_data_place_of_birth))));
                $o_word->setValue('cob', $mbo_student_data->country_of_birth_country_name);
                $o_word->setValue('dob', $s_dob);
                $o_word->setValue('citizenship', $mbo_student_data->citizenship_country_name);
                $o_word->setValue('datepassed', $s_date_comprehensive_examination);
                $o_word->setValue('datecetificate', $s_date_receipt);
                $o_word->setValue('deanname', $s_deans_name);
                $o_word->setValue('faculty', $mbo_student_data->faculty_name);
                $o_word->setValue('study_program_name', $mbo_student_data->study_program_name);
                $o_word->setValue('student_email', $mbo_student_data->student_email);
                $o_word->setValue('deanemail', $mbo_deans_data->employee_email.' ');

                $o_word->saveAs($s_dir);

                $a_return = array('code' => 0, 'file_name' => $s_target_filename);
            }else{
                $a_return = array('code' => 1, 'message' => 'Student not found!', 'description' => '');
            }

            print json_encode($a_return);
        }
    }

    public function generate_german_letter()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id');
            $s_letter_number_fac = $this->input->post('letter_number_fac');
            $s_letter_number_numb = $this->input->post('letter_number_numb');
            $s_letter_number_month = $this->input->post('letter_number_month');
            $s_letter_number_year = $this->input->post('letter_number_year');

            $s_date_letter = $this->input->post('letter_date');
            $s_academic_year = $this->input->post('academic_year_id');
            $s_program = $this->input->post('program');
            $s_date_arrival = $this->input->post('date_arrival');
            $s_date_return = $this->input->post('date_return');

            switch ($s_program) {
                case 'ijd':
                    $s_program_name = 'International Join Degree';
                    $s_program_simply = 'Joint Degree';
                    break;

                case 'dd':
                    $s_program_name = 'Double Degree';
                    $s_program_simply = 'Double Degree';
                    break;
                
                default:
                    $s_program_name = '';
                    $s_program_simply = '';
                    break;
            }

            $s_university_id = $this->input->post('university');

            $s_letter_number = 'L/'.$s_letter_number_fac.'/'.$s_letter_number_numb.'/'.$s_letter_number_month.'/'.$s_letter_number_year;
            if ($s_date_letter != '') {
                $s_date_letter = date('d F Y', strtotime($s_date_letter));
            }
            if ($s_date_arrival != '') {
                $s_date_arrival = date('d F Y', strtotime($s_date_arrival));
            }
            if ($s_date_return != '') {
                $s_date_return = date('d F Y', strtotime($s_date_return));
            }

            $s_university_name = '';
            $s_university_country = '';
            if ($s_university_id != '') {
                $mbo_institution_data = $this->Insm->get_institution_by_id($s_university_id);
                
                if ($mbo_institution_data) {
                    $s_university_name = ucwords(strtolower($mbo_institution_data->institution_name));
                    $s_university_country = $mbo_institution_data->country_name;
                }

            }

            $mbo_student_data = $this->Stm->get_student_filtered(array('student_id' => $s_student_id))[0];

            if ($mbo_student_data) {
                
                $s_template_path = APPPATH.'uploads/templates/Template_of_Letter_to_Germany.docx';

                $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
                $s_vice_rector_name = '';
                $s_vice_rector_email = '';
                if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                    $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                    $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
                    $s_vice_rector_email = $mbo_vice_rector_data->employee_email;
                }

                $mbo_semester_active = $this->Smm->get_active_semester();
                $mbo_deans_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mbo_student_data->deans_id))[0];
                $mbo_hod_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mbo_student_data->head_of_study_program_id))[0];
                $s_deans_name = $this->Pdm->retrieve_title($mbo_student_data->deans_id);
                $s_hod_name = $this->Pdm->retrieve_title($mbo_student_data->head_of_study_program_id);

                $s_header_sign_name = (is_null($mbo_student_data->head_of_study_program_id)) ? $s_vice_rector_name : $s_hod_name;
                $s_header_sign_email = (is_null($mbo_student_data->head_of_study_program_id)) ? $s_vice_rector_email : $mbo_hod_data->employee_email;
                $s_header_sign_title = (is_null($mbo_student_data->head_of_study_program_id)) ? 'Vice Rector Academic' : 'Head of Study Program of '.$mbo_student_data->study_program_name;

                $s_path_target = APPPATH."uploads/academic/".$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id."/german_letter/";
                $s_name_file = 'Ref_Letter_to_German_'.(str_replace(' ', '_', $mbo_student_data->personal_data_name));
                $s_target_filename = $s_name_file.'.docx';
                $s_dir = $s_path_target.$s_target_filename;

                $s_dob = '';
                if ($mbo_student_data->personal_data_date_of_birth != null) {
                    $s_dob = date('d F Y', strtotime($mbo_student_data->personal_data_date_of_birth));
                }

                try {
                    if(!file_exists($s_path_target)){
                        mkdir($s_path_target, 0777, TRUE);
                    }
                    
                    copy($s_template_path, $s_dir);
                    $zip_val = new ZipArchive;

                    if($zip_val->open($s_dir) == true) {
                        $key_file_name = 'word/document.xml';
                        $message = $zip_val->getFromName($key_file_name);
                        
                        $timestamp = date('d-M-Y H:i:s');
                        
                        // this data Replace the placeholders with actual values
                        $message = str_replace("{number}", $s_letter_number, $message);
                        $message = str_replace("{date}", $s_date_letter, $message);
                        $message = str_replace("{program}", $s_program_name, $message);
                        $message = str_replace("{simplyprogram}", $s_program_simply, $message);
                        $message = str_replace("{university}", $s_university_name, $message);
                        $message = str_replace("{universitycountry}", $s_university_country, $message);
                        $message = str_replace("{studentname}", $mbo_student_data->personal_data_name, $message);
                        $message = str_replace("{studentnumber}", $mbo_student_data->student_number, $message);
                        $message = str_replace("{studyprogram}", $mbo_student_data->study_program_name, $message);
                        $message = str_replace("{pob}", (ucwords(strtolower($mbo_student_data->personal_data_place_of_birth))), $message);
                        $message = str_replace("{cob}", $mbo_student_data->country_of_birth_country_name, $message);
                        $message = str_replace("{dob}", $s_dob, $message);
                        $message = str_replace("{citizenship}", $mbo_student_data->citizenship_country_name, $message);
                        // $message = str_replace("{student_faculty_name_shortly}", str_replace('Faculty of ', '', $mbo_student_data->faculty_name), $message);
                        $message = str_replace("{academicyear}", $s_academic_year, $message);
                        $message = str_replace("{startdate}", $s_date_arrival, $message);
                        $message = str_replace("{enddate}", $s_date_return, $message);
                        // $message = str_replace("{student_batch}", ($mbo_student_data->academic_year_id.'/'.(intval($mbo_student_data->academic_year_id) + 1)), $message);
                        $message = str_replace("{deanname}", $s_header_sign_name, $message);
                        $message = str_replace("{faculty}", $s_header_sign_title, $message);
                        // $message = str_replace("deans_email", '<a href="mailto:'.$mbo_deans_data->employee_email.'">'.$mbo_deans_data->employee_email.'</a>', $message);
                        $message = str_replace("{deanemail}", $s_header_sign_email, $message);
                        // print('<pre>');var_dump($message);exit;
                        // print json_encode($message);exit;
                        
                        //Replace the content with the new content created above.
                        $zip_val->addFromString($key_file_name, $message);
                        $zip_val->close();

                        // var_dump($s_dir);exit;
                        // shell_exec('/usr/bin/soffice --headless --convert-to pdf ' . $s_dir . ' --outdir ' . $s_path_target);
                        $a_return = array('code' => 0, 'file_name' => $s_target_filename);
                        // $a_return = array('code' => 0, 'file_name' => $s_name_file.'.pdf');
                    }
                } catch (Exception $exc) {
                    $a_return = array('code' => 1, 'message' => 'Error creating document!', 'description' => $exc);
                    // $error_message =  "Error creating the Word Document";
                    // var_dump($exc);
                }
            }else{
                $a_return = array('code' => 1, 'message' => 'Student not found!', 'description' => '');
            }

            print json_encode($a_return);
        }
    }

    public function generate_application_internship()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id_internship');
            $s_date_letter_internship = $this->input->post('date_letter_internship');
            $s_company_name_internship = htmlspecialchars($this->input->post('company_name_internship'));
            $s_dept_internship = htmlspecialchars($this->input->post('dept_internship'));
            $s_company_address_internhip = htmlspecialchars($this->input->post('company_address_internhip'));
            // $s_company_address_internhip = str_replace("\r\n", '</w:t></w:r></w:p></w:rPr></w:pPr></w:rPr>', $s_company_address_internhip);
            $s_company_address_internhip = str_replace("\r\n", "<w:br/>", $s_company_address_internhip);
            $s_month_start_internship = $this->input->post('month_start_internship');
            $s_month_end_internship = $this->input->post('month_end_internship');
            
            if ((!empty($s_date_letter_internship)) AND ($s_date_letter_internship != '')) {
                $s_date_letter_internship = date('d F Y', strtotime($s_date_letter_internship));
            }

            $s_template_path = APPPATH.'uploads/templates/';
            $s_template_filename = 'Template_of_Internship_Letter.docx';
            $s_template_dir = $s_template_path.$s_template_filename;

            $mbo_student_data = $this->Stm->get_student_filtered(array('student_id' => $s_student_id))[0];
            if ($mbo_student_data) {
                $mbo_semester_active = $this->Smm->get_active_semester();

                $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
                $s_vice_rector_name = '';
                $s_vice_rector_email = '';
                if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                    $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                    $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
                    $s_vice_rector_email = $mbo_vice_rector_data->employee_email;
                }

                // $mbo_deans_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mbo_student_data->deans_id))[0];
                $mbo_hod_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mbo_student_data->head_of_study_program_id))[0];
                $s_deans_name = $this->Pdm->retrieve_title($mbo_student_data->deans_id);
                $s_hod_name = $this->Pdm->retrieve_title($mbo_student_data->head_of_study_program_id);

                $s_header_sign_name = (is_null($mbo_student_data->head_of_study_program_id)) ? $s_vice_rector_name : $s_hod_name;
                $s_header_sign_email = (is_null($mbo_student_data->head_of_study_program_id)) ? $s_vice_rector_email : $mbo_hod_data->employee_email;
                $s_header_sign_title = (is_null($mbo_student_data->head_of_study_program_id)) ? 'Vice Rector Academic' : 'Head of Study Program of '.$mbo_student_data->study_program_name;

                $s_path_target = APPPATH."uploads/academic/".$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id."/internship_letter/";
                $s_name_file = 'Internship_Letter_'.(str_replace(' ', '_', $mbo_student_data->personal_data_name));
                $s_target_filename = $s_name_file.'.docx';
                $s_dir = $s_path_target.$s_target_filename;

                try {
                    if(!file_exists($s_path_target)){
                        mkdir($s_path_target, 0777, TRUE);
                    }
                    
                    copy($s_template_dir, $s_dir);
                    $zip_val = new ZipArchive;

                    if($zip_val->open($s_dir) == true) {
                        $key_file_name = 'word/document.xml';
                        $message = $zip_val->getFromName($key_file_name);
                        
                        $timestamp = date('d-M-Y H:i:s');
                        
                        // this data Replace the placeholders with actual values
                        $message = str_replace("{input_date_of_letter}", $s_date_letter_internship, $message);
                        $message = str_replace("inputcompany", $s_company_name_internship, $message);
                        $message = str_replace("input_address", $s_company_address_internhip, $message);
                        $message = str_replace("{student_name}", $mbo_student_data->personal_data_name, $message);
                        $message = str_replace("{student_number}", $mbo_student_data->student_number, $message);
                        $message = str_replace("{student_prodi}", $mbo_student_data->study_program_name, $message);
                        $message = str_replace("{student_faculty_name_shortly}", str_replace('Faculty of ', '', $mbo_student_data->faculty_name), $message);
                        $message = str_replace("{student_call_gender}", (($mbo_student_data->personal_data_gender == 'M') ? 'He' : 'She'), $message);
                        $message = str_replace("{input_department_internship}", $s_dept_internship, $message);
                        $message = str_replace("{input_start_month}", $s_month_start_internship, $message);
                        $message = str_replace("{input_end_month}", $s_month_end_internship, $message);
                        // $message = str_replace("{student_batch}", ($mbo_student_data->academic_year_id.'/'.(intval($mbo_student_data->academic_year_id) + 1)), $message);
                        $message = str_replace("{deans_name}", $s_header_sign_name, $message);
                        // $message = str_replace("{faculty_name}", 'Dean of the '.$mbo_student_data->faculty_name, $message);
                        $message = str_replace("{faculty_name}", $s_header_sign_title, $message);
                        // $message = str_replace("deans_email", '<a href="mailto:'.$mbo_deans_data->employee_email.'">'.$mbo_deans_data->employee_email.'</a>', $message);
                        $message = str_replace("{deans_email}", $s_header_sign_email, $message);
                        // print('<pre>');var_dump($s_dept_internship);exit;
                        // print json_encode($message);exit;
                        
                        //Replace the content with the new content created above.
                        // print('<pre>');var_dump($message);exit;
                        $zip_val->addFromString($key_file_name, $message);
                        $zip_val->close();

                        // var_dump($s_dir);exit;
                        // shell_exec('/usr/bin/soffice --headless --convert-to pdf ' . $s_dir . ' --outdir ' . $s_path_target);
                        $a_return = array('code' => 0, 'file_name' => $s_target_filename);
                        // $a_return = array('code' => 0, 'file_name' => $s_name_file.'.pdf');
                    }
                } catch (Exception $exc) {
                    $a_return = array('code' => 1, 'message' => 'Error creating document!', 'description' => $exc);
                    // $error_message =  "Error creating the Word Document";
                    // var_dump($exc);
                }
            }else{
                $a_return = array('code' => 1, 'message' => 'Student not found!', 'description' => '');
            }

            print json_encode($a_return);exit;
        }
    }

    public function generate_ref_letter()
    {
        if ($this->input->is_ajax_request()) {
            $s_student_id = $this->input->post('student_id_letter');
            $s_date = $this->input->post('date_letter');
            $s_number = $this->input->post('number_letter');

            // $s_date = '2020-03-04';
            // $s_student_id = 'a3b301fd-b72e-4ce7-9f10-131b98544358';
            // $s_number = '123/XXX/III/2020';

            if ($s_date != '') {
                $s_date = date('d F Y', strtotime($s_date));
            }

            $s_template_path = APPPATH.'uploads/templates/';
            $s_template_filename = 'ref_letter.docx';
            $s_template_dir = $s_template_path.$s_template_filename;

            $mbo_student_data = $this->Stm->get_student_filtered(array('student_id' => $s_student_id))[0];
            if ($mbo_student_data) {
                // print('<pre>');var_dump(str_replace('Faculty of ', '', $mbo_student_data->faculty_name));exit;
                $mbo_semester_active = $this->Smm->get_active_semester();

                $mba_vice_rector = $this->General->get_where('ref_department', ['department_id' => 6]);
                $s_vice_rector_name = '';
                $s_vice_rector_email = '';
                if (($mba_vice_rector) AND (!is_null($mba_vice_rector[0]->employee_id))) {
                    $mbo_vice_rector_data = $this->Emm->get_employee_data(array('em.employee_id' => $mba_vice_rector[0]->employee_id))[0];
                    $s_vice_rector_name = $this->Pdm->retrieve_title($mbo_vice_rector_data->personal_data_id);
                    $s_vice_rector_email = $mbo_vice_rector_data->employee_email;
                }

                // $mbo_deans_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mbo_student_data->deans_id))[0];
                $mbo_hod_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mbo_student_data->head_of_study_program_id))[0];
                $s_deans_name = $this->Pdm->retrieve_title($mbo_student_data->deans_id);
                $s_hod_name = $this->Pdm->retrieve_title($mbo_student_data->head_of_study_program_id);

                $mbo_deans_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mbo_student_data->deans_id))[0];
                $mbo_hod_data = $this->Emm->get_employee_data(array('em.personal_data_id' => $mbo_student_data->head_of_study_program_id))[0];
                $s_deans_name = $this->Pdm->retrieve_title($mbo_student_data->deans_id);
                $s_hod_name = $this->Pdm->retrieve_title($mbo_student_data->head_of_study_program_id);

                $s_header_sign_name = (is_null($mbo_student_data->head_of_study_program_id)) ? $s_vice_rector_name : $s_hod_name;
                $s_header_sign_email = (is_null($mbo_student_data->head_of_study_program_id)) ? $s_vice_rector_email : $mbo_hod_data->employee_email;
                $s_header_sign_title = (is_null($mbo_student_data->head_of_study_program_id)) ? 'Vice Rector Academic' : 'Head of Study Program of '.$mbo_student_data->study_program_name;
                
                $s_path_target = APPPATH."uploads/academic/".$mbo_semester_active->academic_year_id.$mbo_semester_active->semester_type_id."/ref_letter/";
                if(!file_exists($s_path_target)) {
                    mkdir($s_path_target, 0777, TRUE);
                }

                $s_name_file = 'Ref_Letter_'.(str_replace(' ', '_', $mbo_student_data->personal_data_name));
                $s_target_filename = $s_name_file.'.docx';
                $s_dir = $s_path_target.$s_target_filename;

                $phpWord = new \PhpOffice\PhpWord\PhpWord();
                $o_word = new TemplateProcessor($s_template_dir);

                $o_word->setValue('letter_number', $s_number);
                $o_word->setValue('date', $s_date);
                $o_word->setValue('personal_data_name', $mbo_student_data->personal_data_name);
                $o_word->setValue('student_number', $mbo_student_data->student_number);
                $o_word->setValue('student_email', $mbo_student_data->student_email);
                $o_word->setValue('study_program_name', $mbo_student_data->study_program_name);
                $o_word->setValue('faculty_name', str_replace('Faculty of ', '', $mbo_student_data->faculty_name));
                $o_word->setValue('gender', (($mbo_student_data->personal_data_gender == 'M') ? 'Male' : 'Female'));
                $o_word->setValue('place_of_birth', $mbo_student_data->personal_data_place_of_birth);
                $o_word->setValue('date_of_birth', date('d F Y', strtotime($mbo_student_data->personal_data_date_of_birth)));
                $o_word->setValue('citizenship', $mbo_student_data->citizenship_country_name);
                $o_word->setValue('student_batch', ($mbo_student_data->academic_year_id.'/'.(intval($mbo_student_data->academic_year_id) + 1)));
                $o_word->setValue('deans_name', $s_header_sign_name);
                $o_word->setValue('faculty', $s_header_sign_title);
                $o_word->setValue('deans_email', $s_header_sign_email);

                $o_word->saveAs($s_dir);
                $a_return = array('code' => 0, 'file_name' => $s_target_filename);
            }else{
                $a_return = array('code' => 1, 'message' => 'Student not found!', 'description' => '');
            }

            print json_encode($a_return);exit;
        }
    }
}
